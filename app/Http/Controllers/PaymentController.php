<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;

class PaymentController extends Controller
{
   public function payments(Request $request)
    {
		 return view('payments');
	}
   
   // Stripe Payment (Apple Pay, Google Pay)
    public function stripePay(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => 2000, // Amount in cents, adjust as needed
                'currency' => 'usd',
                'payment_method' => $request->payment_method,
                'confirmation_method' => 'manual',
                'confirm' => true,
            ]);

            return response()->json([
                'success' => $paymentIntent->status === 'succeeded'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    // PayPal capture payment
    public function paypalCapture(Request $request)
    {
        $clientId = config('services.paypal.client_id');
        $secret = config('services.paypal.secret');

        $environment = new SandboxEnvironment($clientId, $secret);
        $client = new PayPalHttpClient($environment);

        $orderId = $request->orderID;

        $captureRequest = new OrdersCaptureRequest($orderId);
        $captureRequest->prefer('return=representation');

        try {
            $response = $client->execute($captureRequest);

            if ($response->statusCode == 201) {
                return response()->json(['success' => true]);
            }

            return response()->json(['success' => false], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
