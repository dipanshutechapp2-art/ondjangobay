<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wallet; 
use App\Models\WalletHistory;
use App\Models\Currency;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class WalletController extends Controller
{
    
    public function getUserWalletBalance(Request $request){
      
        $user = auth()->user();

		$wallet = $user->wallet()->firstOrCreate([
			'user_id' => $user->id,
		]);

		//$wallet->load('histories');

		return response()->json([
            'status'  => true,
            'data'    => $wallet,
        ], 201);
      
    }
    
    public function getUserWalletHistory(Request $request){
      
        $perPage = $request->input('limit', 10);
        
        $user = auth()->user();
        
		$wallet = $user->wallet()->firstOrCreate([
			'user_id' => $user->id,
		]);

		$wallet->load('histories');
		$transactions = $wallet->histories()->latest()->paginate($perPage);
		$transactions->transform(function ($tx) {
			$tx->currency_obj = Currency::where('code', $tx->currency)->first();
			return $tx;
		});

		return response()->json([
            'status'  => true,
            'data'    => $transactions,
        ], 201);
      
    }
    
    public function addBalance(Request $request)
    {
        $request->validate([
            'amount'  => 'required|numeric|min:1',
            'gateway' => 'required|in:paypal,stripe',
        ]);

        if ($request->gateway === 'stripe') {
            if ($request->amount < 50) {
                return response()->json(['status' => false, 'message' => 'Minimum amount for Stripe is 50.'], 422);
            }

            return $this->processStripe($request->amount);
        }

        if ($request->gateway === 'paypal') {
            if ($request->amount < 50) {
                return response()->json(['status' => false, 'message' => 'Minimum amount for PayPal is 50.'], 422);
            }

            return $this->processPaypal($request->amount);
        }

        return response()->json(['status' => false, 'message' => 'Invalid gateway selected.'], 422);
    }

    public function processStripe($amount)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => getUserAuthOrDefaultSelectedCurrency(),
                    'product_data' => ['name' => 'Wallet Top-up'],
                    'unit_amount' => $amount * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => config('app.frontend_url') . '/wallet/stripe-success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => config('app.frontend_url') . '/wallet/stripe-cancel',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Stripe session created.',
            'checkout_url' => $session->url,
            'session_id'   => $session->id,
        ]);
    }

    public function stripeWebhook(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = \Stripe\Checkout\Session::retrieve($request->session_id);

        if ($session->payment_status === 'paid') {
            $user = auth()->user(); // or find by metadata
            $wallet = $user->wallet;
            $amount = $session->amount_total / 100;

            $wallet->increment('balance', $amount);

            $wallet->histories()->create([
                'user_id'        => $user->id,
                'currency'       => $session->currency,
                'type'           => 'credit',
                'amount'         => $amount,
                'method'         => 'stripe',
                'transaction_id' => $session->payment_intent,
                'status'         => 'completed',
                'old_balance'    => $wallet->balance - $amount,
                'new_balance'    => $wallet->balance,
                'remarks'        => 'Wallet top-up via Stripe (API)',
            ]);

            return response()->json(['status' => true, 'message' => 'Wallet credited successfully']);
        }

        return response()->json(['status' => false, 'message' => 'Stripe payment failed'], 400);
    }

    public function processPaypal($amount)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $order = $provider->createOrder([
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'amount' => [
                    'currency_code' => getUserAuthOrDefaultSelectedCurrency(),
                    'value' => $amount
                ]
            ]],
            'application_context' => [
                'return_url' => config('app.frontend_url') . '/wallet/paypal-success',
                'cancel_url' => config('app.frontend_url') . '/wallet/paypal-cancel',
            ]
        ]);

        foreach ($order['links'] as $link) {
            if ($link['rel'] === 'approve') {
                return response()->json([
                    'status' => true,
                    'message' => 'PayPal order created.',
                    'approve_url' => $link['href'],
                    'order_id'    => $order['id'],
                ]);
            }
        }

        return response()->json(['status' => false, 'message' => 'Something went wrong with PayPal.'], 400);
    }

    public function paypalWebhook(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
    
        $response = $provider->capturePaymentOrder($request->token);
    
        if (isset($response['status']) && $response['status'] === 'COMPLETED') {
            $user   = auth()->user();
            $wallet = $user->wallet;
    
            // Correct structure
            $capture   = $response['purchase_units'][0]['payments']['captures'][0];
            $amount    = $capture['amount']['value'];
            $currency  = $capture['amount']['currency_code'];
    
            $wallet->increment('balance', $amount);
    
            $wallet->histories()->create([
                'user_id'        => $user->id,
                'currency'       => $currency,
                'type'           => 'credit',
                'amount'         => $amount,
                'method'         => 'paypal',
                'transaction_id' => $capture['id'],
                'status'         => 'completed',
                'old_balance'    => $wallet->balance - $amount,
                'new_balance'    => $wallet->balance,
                'remarks'        => 'Wallet top-up via PayPal (API)',
            ]);
    
            return response()->json([
                'status'  => true,
                'message' => 'Wallet credited successfully',
                'balance' => $wallet->balance
            ]);
        }
    
        return response()->json([
            'status'  => false,
            'message' => 'PayPal payment failed',
            'raw'     => $response // debug if needed
        ], 400);
    }

}
