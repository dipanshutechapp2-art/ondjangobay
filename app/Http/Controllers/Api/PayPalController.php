<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Models\Order;
use App\Models\Currency;
use App\Models\CartItem;
use App\Models\PaymentGateway;
use App\Models\PartnerOrder;
use Illuminate\Support\Facades\Mail;

class PayPalController extends Controller
{
    protected $provider;

    public function __construct()
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
        
        $this->provider = new PayPalClient;
        //$this->provider->setApiCredentials(config('paypal'));
        $this->provider->setApiCredentials($configData);
        $this->provider->getAccessToken();
    }

    public function createOrder(Request $request)
    {   
        $request->validate([
            'order_id'    => 'required',
        ]);
        
		$orderIds  = explode(',',$request->order_id);
	   
		$orders          = Order::whereIn('id',$orderIds)->get();
		$grandTotal      = $orders->sum('total_amount');
		$amount          = number_format($grandTotal, 2, '.', '');
	
		$orderedData     = $orders->first();
		$orderCurrency   = $orders->first()->currency;
		$currencyInfo    = Currency::where('symbol',$orderCurrency)->first()->code;
		
		if($currencyInfo=='INR') {
		  $amount       = convertINRtoUSD($grandTotal);
		  $currencyInfo = 'USD';
		}
		
        $response = $this->provider->createOrder([
			"intent" => "CAPTURE",
			"purchase_units" => [[
				"amount" => [
					"currency_code" => $currencyInfo,
					"value"         => $amount,
				],
				"description" => "Order - Payment for your products",
				"shipping" => [
					"name" => [
						"full_name" => $orderedData->billing_first_name . ' ' . $orderedData->billing_last_name,
					],
					"address" => [
						"address_line_1" => $orderedData->billing_address_1,
						"address_line_2" => $orderedData->billing_address_2 ?? '',
						"admin_area_2"   => $orderedData->billing_city, 
						"admin_area_1"   => $orderedData->billing_state, 
						//"postal_code"    => $orderedData->billing_zipcode,
						"postal_code"    => 'NW16XE',
						//"country_code"   => strtoupper($orderedData->billing_country), // e.g. "US", "IN"
						"country_code"   => 'US', // e.g. "US", "IN"
					]
				],
			]],
			"application_context" => [
				"cancel_url" => route('paypal.cancel',$orderIds),
				"return_url" => route('paypal.success', $orderIds),
			]
		]);

        return response()->json($response);
    }

    public function captureOrder(Request $request)
    {
        $orderId = $request->orderId;

        $result = $this->provider->capturePaymentOrder($orderId);

        return response()->json($result);
    }
	
	#COMPAIGN ORDER PAYPAL
	public function paypalCreateCompaignOrder(Request $request, $order)
	{  
		try {
			$orderData = PartnerOrder::find($order);
			
			if (!$orderData) {
				return response()->json(['error' => 'Invalid order id'], 404);
			}

			$grandTotal   = $orderData->amount;
			$currencyInfo = Currency::where('symbol', $orderData->currency)->first()->code;
			
			if($currencyInfo == 'INR') {
				$grandTotal   = convertINRtoUSD($grandTotal);
				$currencyInfo = 'USD';
			}

			$gateway = PaymentGateway::where('name', 'Paypal')->firstOrFail();
			$creds   = $gateway->mode=='test' ? $gateway->test_credentials : $gateway->live_credentials;
			$mode    = $gateway->mode == 'test' ? 'sandbox' : 'live';

			$configData = [
				'mode' => $mode,
				$mode => [
					'client_id'     => $creds['client_id'],
					'client_secret' => $creds['secret'],
				],
				'payment_action' => 'Sale',
				'currency'       => $currencyInfo,
				"locale"         => "en_US",
				"notify_url"     => "",
				'validate_ssl'   => true,
			];
		
			$provider = new PayPalClient;
			
			$provider->setApiCredentials($configData);
			$token = $provider->getAccessToken();
			if (isset($token['error'])) {
				return response()->json(['error' => $token['error']['error_description']], 500);
			}

			$provider->setAccessToken($token);

			$response = $provider->createOrder([
				"intent" => "CAPTURE",
				"purchase_units" => [[
					"amount" => [
						"currency_code" => $currencyInfo,
						"value" => $grandTotal
					],
					"description" => "Order Payment",
				]],
				"application_context" => [
					"cancel_url" => url('/api/partner/paypal/cancel/'.$order),
					"return_url" => url('/api/partner/paypal/success/'.$order),
				]
			]);

			if ($response['status'] == 'CREATED') {
				foreach ($response['links'] as $link) {
					if ($link['rel'] === 'approve') {

						return response()->json([
							'status' => 'success',
							'approval_url' => $link['href'],
							'order_id' => $order
						]);
					}
				}
			}

			return response()->json(['error' => 'Failed to create PayPal order'], 500);

		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}
	}
	
	public function paypalSuccessCompaignOrder(Request $request, $order)
	{		
		try {
			$gateway = PaymentGateway::where('name','Paypal')->firstOrFail();
			$creds   = $gateway->mode=='test' ? $gateway->test_credentials : $gateway->live_credentials;
			$mode    = $gateway->mode=='test' ? 'sandbox' : 'live';
			
			$provider = new PayPalClient;
			$provider->setApiCredentials([
				'mode' => $mode,
				$mode => [
					'client_id' => $creds['client_id'],
					'client_secret' => $creds['secret'],
				],
				"payment_action" => "Sale",                 
				"currency"       => getUserAuthOrDefaultSelectedCurrency() ?? "USD",
				"locale"         => "en_US",
				"notify_url"     => "",     
				"validate_ssl"   => true    
			]);

			$token = $provider->getAccessToken();
			$provider->setAccessToken($token);

			$response = $provider->capturePaymentOrder($request->token);
			
			if ($response['status'] == 'COMPLETED') {

				$orderDetails = PartnerOrder::find($order);
				$orderDetails->payment_method = 'Paypal';
				$orderDetails->payment_status = 'paid';
				$orderDetails->status = 'confirmed';
				$orderDetails->payment_transaction_id = $response['id'];
				$orderDetails->save();
				
				#SEND MAIL
				$orderInfo = PartnerOrder::with('product')->find($orderDetails->id);
		
				Mail::send('emails.partner-order-receipt', ['order' => $orderInfo], function ($message) use ($orderInfo) {
					$message->to([$orderInfo->billing_email, get_admin_email(),get_vendor_email($orderInfo->vendor_id)])
						->subject('Partner Order Receipt #' . $orderInfo->order_number);
				});
				
				return response()->json([
					'status' => 'success',
					'message' => 'Payment completed.',
					'order_id' => $order,
					'transaction_id' => $response['id']
				]);
			}

			return response()->json(['status' => 'failed', 'message'=>'Payment failed'], 400);

		} catch (\Exception $e){
			return response()->json(['error'=>$e->getMessage()],500);
		}
	}
	
	public function paypalCancelCompaignOrder($order)
	{
		$orderDetails = PartnerOrder::find($order);

		if (!$orderDetails) {
			return response()->json(['error'=>'Invalid order'],404);
		}

		$orderDetails->payment_status = 'failed';
		$orderDetails->status = 'cancelled';
		$orderDetails->save();
		
		return response()->json([
			'status'=>'cancelled',
			'message'=>'Payment cancelled by user'
		]);
	}

}
