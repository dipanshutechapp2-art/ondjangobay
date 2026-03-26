<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\Order;
use App\Models\Currency; 
use App\Models\CartItem;
use App\Models\PartnerOrder;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Mail;


class StripeController extends Controller
{
    public function __construct()
    {
        #DYNAMIC STRIPE CONFIGURATION
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
    }

    public function createPaymentIntent(Request $request)
    {
        
        $request->validate([
            'amount'      => 'required',
            'currency'    => 'required',
        ]);
        
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => intval($request->amount * 100),
                'currency' => $request->currency ?? 'usd',
                'payment_method_types' => ['card'],
            ]);

            return response()->json([
                'clientSecret'    => $paymentIntent->client_secret,
                'paymentIntentId' => $paymentIntent->id,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function capturePayment(Request $request)
    {
        $request->validate([
            'paymentIntentId' => 'required',
        ]);
        
        try {
            $paymentIntent = PaymentIntent::retrieve($request->paymentIntentId);
            $paymentIntent->capture();

            return response()->json($paymentIntent);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
	
	#COMPAIGN ORDER PAYMENT
	public function stripeCreate(Request $request, $order)
	{
		try {
			$orderDetails = PartnerOrder::find($order);

			if (!$orderDetails) {
				return response()->json(['error' => 'Order not found'], 404);
			}

			// Amount + Currency
			$grandTotal    = $orderDetails->amount;
			$orderCurrency = $orderDetails->currency;
			$currencyInfo  = Currency::where('symbol', $orderCurrency)->first()->code ?? 'usd';

			// Dynamic Stripe Keys
			$gateway = PaymentGateway::where('name', 'Stripe')->first();
			$credentials = $gateway->mode === 'test'
				? $gateway->test_credentials
				: $gateway->live_credentials;

			Stripe::setApiKey($credentials['secret_key']);

			// Create checkout session
			$session = \Stripe\Checkout\Session::create([
				'payment_method_types' => ['card'],
				'line_items' => [[
					'price_data' => [
						'currency' => $currencyInfo,
						'product_data' => [
							'name' => 'Order Payment',
						],
						'unit_amount' => $grandTotal * 100, // Stripe in cents
					],
					'quantity' => 1,
				]],
				'mode' => 'payment',

				// API URLs
				'success_url' => url("/api/partner/stripe/success/{$order}") . '?session_id={CHECKOUT_SESSION_ID}',
				'cancel_url'  => url("/api/partner/stripe/cancel/{$order}"),
			]);

			return response()->json([
				'status' => 'success',
				'checkout_url' => $session->url,
				'session_id' => $session->id
			]);

		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}
	}
	
	public function stripeSuccess(Request $request, $order)
	{
		try {
			$sessionId = $request->session_id;

			if (!$sessionId) {
				return response()->json(['error' => 'Missing session_id'], 400);
			}

			// Stripe Keys
			$gateway = PaymentGateway::where('name', 'Stripe')->first();
			$credentials = $gateway->mode === 'test'
				? $gateway->test_credentials
				: $gateway->live_credentials;

			Stripe::setApiKey($credentials['secret_key']);

			// Retrieve session
			$session = \Stripe\Checkout\Session::retrieve($sessionId);
			$paymentIntentId = $session->payment_intent;

			if (!$paymentIntentId) {
				return response()->json(['error' => 'Invalid payment session'], 400);
			}

			// Retrieve payment
			$paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);

			// Update order
			$orderDetails = PartnerOrder::find($order);

			if ($orderDetails) {
				$orderDetails->payment_method  = 'Stripe';
				$orderDetails->payment_status  = 'paid';
				$orderDetails->status          = 'confirmed';
				$orderDetails->payment_transaction_id = $paymentIntentId;
				$orderDetails->save();
				
				#SEND MAIL
				$orderInfo = PartnerOrder::with('product')->find($orderDetails->id);
		
				Mail::send('emails.partner-order-receipt', ['order' => $orderInfo], function ($message) use ($orderInfo) {
					$message->to([$orderInfo->billing_email, get_admin_email(),get_vendor_email($orderInfo->vendor_id)])
						->subject('Partner Order Receipt #' . $orderInfo->order_number);
				});
				
			}
			
			return response()->json([
				'status' => 'success',
				'message' => 'Payment completed via Stripe',
				'order_id' => $order,
				'transaction_id' => $paymentIntentId
			]);

		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}
	}

	public function stripeCancel($order)
	{
		try {
			$orderDetails = PartnerOrder::find($order);

			if ($orderDetails) {
				$orderDetails->payment_method = 'Stripe';
				$orderDetails->payment_status = 'failed';
				$orderDetails->status         = 'cancelled';
				$orderDetails->payment_transaction_id = null;
				$orderDetails->save();
			}

			return response()->json([
				'status' => 'cancelled',
				'order_id' => $order,
				'message' => 'Stripe payment cancelled'
			]);

		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}
	}

}
