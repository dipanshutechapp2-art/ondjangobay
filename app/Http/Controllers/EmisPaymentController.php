<?php

namespace App\Http\Controllers;

use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Currency;
use App\Models\CartItem;
use App\Models\PaymentGateway;
use App\Models\PartnerOrder;
use Illuminate\Support\Facades\Mail;
use App\Services\CjDropshippingService;
use App\Services\AutoDSService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmisPaymentController extends Controller
{
    public function pay($encodedOrderIds)
    {	
		$orderIds = json_decode(base64_decode($encodedOrderIds), true);

		if (!is_array($orderIds) || empty($orderIds)) {
			abort(400, 'Invalid orders');
		}

		$orders = Order::whereIn('id', $orderIds)->get();

		if ($orders->count() !== count($orderIds)) {
			abort(404, 'Some orders not found');
		}

		$totalAmount = $orders->sum('total_amount');
		
		$groupReference = 'ORD' . time();

		Order::whereIn('id', $orderIds)->update([
			'payment_group_reference' => $groupReference,
			'order_status' => 'pending',
		]);
		
		#DYNAMIC PAYPAL CONFIGURATION
		$gateway = PaymentGateway::where('name', 'EMIS')->firstOrFail();
		if ($gateway->mode === 'test') {
			$creds     = $gateway->test_credentials ?? [];
			$mode      = 'sandbox';
			$emisIframeOrigin = $creds['emis_url'];
		}else{
			$creds     = $gateway->live_credentials ?? [];
			$mode      = 'live';
			$emisIframeOrigin = $creds['emis_url'];
		}
		
		$response = Http::acceptJson()
			->withHeaders([
				'Content-Type' => 'application/json',
			])
			->post(
				$creds['emis_url'].'/online-payment-gateway/webframe/v1/frameToken',
				[
					'reference'   => $groupReference,
					//'amount'      => number_format($totalAmount, 2, '.', ''),
					'amount'      => number_format(0.1, 2, '.', ''),
					'token'       => $creds['emis_frame_token'],
					'mobile'      => 'PAYMENT',
					'qrCode'      => 'PAYMENT',
					'card'        => 'DISABLED',
					'terminal'    => $creds['emis_termnal_id'],
					'callbackUrl' => route('emis.callback'),
				]
			);

		/* dd([
			'status' => $response->status(),
			'ok'     => $response->successful(),
			'json'   => $response->json(),
			'body'   => $response->body(),
			'headers'=> $response->headers(),
		]); */
		
		if (!$response->successful()) {
			abort(500, 'EMIS Token Error');
		}
		
		return view('payment.emis', [
			'frameToken' => $response['id'],
			'emisIframeOrigin' => $emisIframeOrigin,
		]);
    }
	
	public function payment_failed(Request $request){
		
		return view('payment.failed');
	}
	
	public function callback(Request $request)
	{
		
		Log::info('EMIS CALLBACK HIT', [
			'time' => now()->toDateTimeString(),
			'payload' => $request->all(),
		]);

		return response()->json(['ok' => true]);
		
		/* $order = Order::where('reference', $request->merchantReferenceNumber)->first();
		
		if (!$order) {
			return response()->json(['error' => 'Order not found'], 404);
		}

		if ($request->status === 'PAYMENT_COMPLETED') {
			$order->update([
				'status' => 'paid',
				'payment_reference' => $request->id,
			]);
		} else {
			$order->update([
				'status' => 'failed',
				'payment_error' => $request->errorMessage,
			]);
		}

		return response()->json(['success' => true]); */
	}

}
