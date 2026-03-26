<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\Currency;
use App\Models\PaymentGateway;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Exception;

class WalletController extends Controller
{

    public function index()
	{
		$user = auth()->user();

		$wallet = $user->wallet()->firstOrCreate([
			'user_id' => $user->id,
		]);

		$wallet->load('histories');

		$balance      = $wallet->balance;
		$transactions = $wallet->histories()->latest()->paginate(5);
		$transactions->transform(function ($tx) {
			$tx->currency_obj = Currency::where('code', $tx->currency)->first();
			return $tx;
		});

		return view('account.wallet.index', compact('wallet', 'balance', 'transactions'));
	}
	
	public function showAddForm()
	{	
		$paymentGatewayList = PaymentGateway::whereNotIn('name',['COD','Wallet'])->where('status',true)->get();

		return view('account.wallet.add',compact('paymentGatewayList')); 
	}
	
    public function addBalance(Request $request)
    {  
        $request->validate([
            'amount'  => 'required|numeric|min:1',
            'gateway' => 'required|in:paypal,stripe',
        ]);
		
        if ($request->gateway === 'stripe') {
            if ($request->amount < 50) { 
				return back()->with('error', 'Minimum amount for Stripe is 50.');
			}
			session(['amount' => $request->amount]);
			return $this->processStripe($request->amount);
        }

        if ($request->gateway === 'paypal') {
			
			if ($request->amount < 50) { 
				return back()->with('error', 'Minimum amount for Paypal is 50.');
			}
			
			session(['amount' => $request->amount]);
            return $this->processPaypal($request->amount);
        }

        return back()->with('error', 'Invalid payment gateway selected.');
    }

    public function processStripe($amount)
    {
        
		$gateway = PaymentGateway::where('name', 'Stripe')->first();
		
		if ($gateway->mode === 'test') { 
			$publishable_key = $gateway->test_credentials['publishable_key'] ?? null;
			$secret_key      = $gateway->test_credentials['secret_key'] ?? null;
		} else {
			$publishable_key  = $gateway->live_credentials['publishable_key'] ?? null;
			$secret_key       = $gateway->live_credentials['secret_key'] ?? null;
		}
		
		if(!empty($secret_key)) {
		   Stripe::setApiKey($secret_key);
		}else{
		   Stripe::setApiKey(getenv('STRIPE_SECRET'));
		}
		
		try {
			$session = StripeSession::create([
				'payment_method_types' => ['card'],
				'line_items' => [[
					'price_data' => [
						'currency' => getUserAuthOrDefaultSelectedCurrency(),
						'product_data' => [
							'name' => 'Wallet Top-up',
						],
						'unit_amount' => $amount * 100,
					],
					'quantity' => 1,
				]],
				'mode' => 'payment',
				'success_url' => route('wallet.stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
				'cancel_url' => route('wallet.stripe.cancel'),
			]);

			return redirect($session->url);

		} catch (ApiErrorException $e) {
			return back()->with('error', $e->getMessage());
		} catch (\Exception $e) {
			return back()->with('error', 'Something went wrong: ' . $e->getMessage());
		}
		
    }

    public function stripeSuccess(Request $request)
    {
        $sessionId = $request->get('session_id');
        if (!$sessionId) {
            return redirect()->route('wallet.index')->with('error', 'Payment session not found.');
        }
		
		$gateway = PaymentGateway::where('name', 'Stripe')->first();
		if ($gateway->mode === 'test') { 
			$publishable_key = $gateway->test_credentials['publishable_key'] ?? null;
			$secret_key      = $gateway->test_credentials['secret_key'] ?? null;
		} else {
			$publishable_key  = $gateway->live_credentials['publishable_key'] ?? null;
			$secret_key       = $gateway->live_credentials['secret_key'] ?? null;
		}
		
		if(!empty($secret_key)) {
		   Stripe::setApiKey($secret_key);
		}else{
		   Stripe::setApiKey(getenv('STRIPE_SECRET'));
		}
        
		
        $session = \Stripe\Checkout\Session::retrieve($sessionId);

        if ($session->payment_status === 'paid') {
            $wallet = auth()->user()->wallet;
            $amount = session('amount', 0);

            $wallet->increment('balance', $amount);

            $wallet->histories()->create([
				'user_id'        => auth()->id(),
				'currency'       => getUserAuthOrDefaultSelectedCurrency(),
				'type'           => 'credit',
				'amount'         => $amount,
				'method'         => 'stripe',
				'transaction_id' => $session->payment_intent,
				'status'         => 'completed',
				'old_balance'    => $wallet->balance - $amount,
				'new_balance'    => $wallet->balance,
				'remarks'        => 'Wallet top-up via Stripe',
			]);

            session()->forget('amount');

            return redirect()->route('wallet.index')->with('success', 'Balance added successfully via Stripe!');
        }

        return redirect()->route('wallet.index')->with('error', 'Stripe payment failed.');
    }

    public function stripeCancel()
    {
        return redirect()->route('wallet.index')->with('error', 'Stripe payment was cancelled.');
    }

    /* public function processPaypal($amount)
    {
        
		$provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->setApiCredentials($configData);
		$token = $provider->getAccessToken();

		
        $order = $provider->createOrder([
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'amount' => [
                    'currency_code' => getUserAuthOrDefaultSelectedCurrency(),
                    'value' => $amount
                ]
            ]],
            'application_context' => [
                'return_url' => route('wallet.paypal.success'),
                'cancel_url' => route('wallet.paypal.cancel'),
            ]
        ]);

        foreach ($order['links'] as $link) {
            if ($link['rel'] === 'approve') {
                return redirect($link['href']);
            }
        }

        return redirect()->route('wallet.index')->with('error', 'Something went wrong with PayPal.');
    } */
	
	
	public function processPaypal($amount)
	{
		try {
			
			$gateway = PaymentGateway::where('name', 'Paypal')->firstOrFail();
			
			if ($gateway->mode === 'test') {
			    $creds     = $gateway->test_credentials ?? [];
				$mode      = 'sandbox';
			}else{
				$creds     = $gateway->live_credentials ?? [];
				$mode      = 'live';
			}
			
			$configData = [
				'mode' => $mode,
				'sandbox' => [
					'client_id'     => $creds['client_id'] ?? '',
					'client_secret' => $creds['secret'] ?? '',
					'app_id'        => '',
				],
				'live' => [
					'client_id'     => $creds['client_id'] ?? '',
					'client_secret' => $creds['secret'] ?? '',
					'app_id'        => '',
				],
				'payment_action' => 'Sale',
				'currency'       => getUserAuthOrDefaultSelectedCurrency() ?? 'USD',
				'notify_url'     => '',
				'locale'         => 'en_US',
				'validate_ssl'   => true,
			];

			$provider = new PayPalClient;
			$provider->setApiCredentials($configData);

			$token = $provider->getAccessToken();
			if (isset($token['error'])) {
				$errorMessage = $token['error']['error_description'] ?? 'PayPal authentication failed. Please check your credentials.';
				return back()->with('error', $errorMessage);
			}

			$order = $provider->createOrder([
				'intent' => 'CAPTURE',
				'purchase_units' => [[
					'amount' => [
						'currency_code' => $configData['currency'],
						'value' => $amount,
					],
				]],
				'application_context' => [
					'return_url' => route('wallet.paypal.success'),
					'cancel_url' => route('wallet.paypal.cancel'),
				],
			]);

			if (!empty($order['links'])) {
				foreach ($order['links'] as $link) {
					if ($link['rel'] === 'approve') {
						return redirect($link['href']);
					}
				}
			}

			throw new Exception('Something went wrong while creating PayPal order.');

		} catch (Exception $e) {
			\Log::error('PayPal Error: '.$e->getMessage(), [
				'trace' => $e->getTraceAsString(),
			]);
			return back()->with('error', 'PayPal error:'.$e->getMessage());
		}
	}


    public function paypalSuccess(Request $request)
    {
        #DUNAMIC PAYPAL CONFIGURATION
		$gateway = PaymentGateway::where('name', 'Paypal')->firstOrFail();
		
		if ($gateway->mode === 'test') {
			$creds     = $gateway->test_credentials ?? [];
			$mode      = 'sandbox';
		}else{
			$creds     = $gateway->live_credentials ?? [];
			$mode      = 'live';
		}
		
		$configData = [
			'mode' => $mode,
			'sandbox' => [
				'client_id'     => $creds['client_id'] ?? '',
				'client_secret' => $creds['secret'] ?? '',
				'app_id'        => '',
			],
			'live' => [
				'client_id'     => $creds['client_id'] ?? '',
				'client_secret' => $creds['secret'] ?? '',
				'app_id'        => '',
			],
			'payment_action' => 'Sale',
			'currency'       => getUserAuthOrDefaultSelectedCurrency() ?? 'USD',
			'notify_url'     => '',
			'locale'         => 'en_US',
			'validate_ssl'   => true,
		];
		
		$provider = new PayPalClient;
        //$provider->setApiCredentials(config('paypal'));
        $provider->setApiCredentials($configData);
        $token    = $provider->getAccessToken();
		
		if (isset($token['error'])) {
			$errorMessage = $token['error']['error_description'] ?? 'PayPal authentication failed. Please check your credentials.';
			return back()->with('error', $errorMessage);
		}

        $response = $provider->capturePaymentOrder($request->token);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            $wallet = auth()->user()->wallet;
            $amount = session('amount', 0);

            $wallet->increment('balance', $amount);

			$wallet->histories()->create([
				'user_id'        => auth()->id(),
				'currency'       => getUserAuthOrDefaultSelectedCurrency(),
				'type'           => 'credit',
				'amount'         => $amount,
				'method'         => 'paypal',
				'transaction_id' => $response['id'],
				'status'         => 'completed',
				'old_balance'    => $wallet->balance - $amount,
				'new_balance'    => $wallet->balance,
				'remarks'        => 'Wallet top-up via Paypal',
			]);
			

            session()->forget('amount');

            return redirect()->route('wallet.index')->with('success', 'Balance added successfully via PayPal!');
        }

        return redirect()->route('wallet.index')->with('error', 'PayPal payment failed.');
    }

    public function paypalCancel()
    {
        return redirect()->route('wallet.index')->with('error', 'PayPal payment was cancelled.');
    }
}