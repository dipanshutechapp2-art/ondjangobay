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
use App\Services\MyUSService;

class PaypalPaymentController extends Controller
{
   
	/* public function createPayment(Request $request, $order)
	{   
	    $orderID   = $order;
		$orderIds  = json_decode(base64_decode($order), true);
		
		if (!is_array($orderIds) || empty($orderIds)) {
			return redirect()->back()->with('error', 'Invalid order reference.');
		}
		
		$orders          = Order::whereIn('id',$orderIds)->get();
		$grandTotal      = $orders->sum('total_amount');
		$orderedData     = $orders->first();
		$orderCurrency   = $orders->first()->currency;
		$currencyInfo    = Currency::where('symbol',$orderCurrency)->first()->code;
		
		$provider = new PayPalClient;
		$paypal   = $provider->setApiCredentials(config('paypal'));
		$token    = $provider->getAccessToken();
		$provider->setAccessToken($token);

		$response = $provider->createOrder([
			"intent" => "CAPTURE",
			"purchase_units" => [[
				"amount" => [
					"currency_code" => $currencyInfo,
					"value"         => $grandTotal,
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
						"postal_code"    => $orderedData->billing_zipcode,
						//"country_code"   => strtoupper($orderedData->billing_country), // e.g. "US", "IN"
						"country_code"   => 'US', // e.g. "US", "IN"
					]
				],
			]],
			"application_context" => [
				"cancel_url" => route('paypal.cancel',$orderID),
				"return_url" => route('paypal.success', $orderID),
			]
		]);
	
		if (isset($response['id']) && $response['status'] == 'CREATED') {
			foreach ($response['links'] as $link) {
				if ($link['rel'] === 'approve') {
					return redirect()->away($link['href']);
				}
			}
		}
		
		$errorMessage = $response['message'] ?? 'Something went wrong.';
		if (isset($response['error']['details'][0]['description'])) {
			$errorMessage .= ' - ' . $response['error']['details'][0]['description'];
		}
		
		return redirect()->back()->with('error', $errorMessage);
	} */
	
	public function createPayment(Request $request, $order)
	{   
		try {
			$orderID   = $order;
			$orderIds  = json_decode(base64_decode($order), true);

			if (!is_array($orderIds) || empty($orderIds)) {
				return redirect()->back()->with('error', 'Invalid order reference.');
			}

			$orders        = Order::whereIn('id',$orderIds)->get();
			$grandTotal    = $orders->sum('total_amount');
			$orderedData   = $orders->first();
			$orderCurrency = $orders->first()->currency;
			$currencyInfo  = Currency::where('symbol',$orderCurrency)->first()->code;
			
			if($currencyInfo=='INR') {
			  $grandTotal   = convertINRtoUSD($grandTotal);
			  $currencyInfo = 'USD';
			}
			
			#DYNAMIC PAYPAL CONFIGURATION
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
				'currency'       => $currencyInfo,
				'notify_url'     => '',
				'locale'         => 'en_US',
				'validate_ssl'   => true,
			];
			
			$provider = new PayPalClient;
			//$paypal   = $provider->setApiCredentials(config('paypal'));
			$paypal   = $provider->setApiCredentials($configData);
			$token    = $provider->getAccessToken();

			if (isset($token['error'])) {
				$errorMessage = $token['error']['error_description'] ?? 'PayPal authentication failed. Please check your credentials.';
				return back()->with('error', $errorMessage);
			}
			
			$provider->setAccessToken($token);

			$response = $provider->createOrder([
				"intent" => "CAPTURE",
				"purchase_units" => [[
					"amount" => [
						"currency_code" => $currencyInfo,
						"value"         => $grandTotal,
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
							"postal_code"    => $orderedData->billing_zipcode,
							//"country_code"   => strtoupper($orderedData->billing_country), 
							"country_code"   => 'US',
						]
					],
				]],
				"application_context" => [
					"cancel_url" => route('paypal.cancel',$orderID),
					"return_url" => route('paypal.success', $orderID),
				]
			]);

			if (isset($response['id']) && $response['status'] == 'CREATED') {
				foreach ($response['links'] as $link) {
					if ($link['rel'] === 'approve') {
						return redirect()->away($link['href']);
					}
				}
			}

			$errorMessage = $response['message'] ?? 'Something went wrong.';
			if (isset($response['error']['details'][0]['description'])) {
				$errorMessage .= ' - ' . $response['error']['details'][0]['description'];
			}

			return redirect()->back()->with('error', $errorMessage);

		} catch (\Exception $e) {
			\Log::error('PayPal Payment Error: '.$e->getMessage(), [
				'orderID' => $order ?? null,
				//'user_id' => auth()->id(),
			]);
			return back()->with('error', 'PayPal error:'.$e->getMessage());
		}
	}



    public function success(Request $request,$order)
    {  
		$user    = auth()->user();
		$isGuest = !$user;
		$userId  = $user?->id;
		
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
        //$paypal   = $provider->setApiCredentials(config('paypal'));
        $paypal   = $provider->setApiCredentials($configData);
        $token    = $provider->getAccessToken();
		
		if (isset($token['error'])) {
			$errorMessage = $token['error']['error_description'] ?? 'PayPal authentication failed. Please check your credentials.';
			return back()->with('error', $errorMessage);
		}
		
        $provider->setAccessToken($token);

        $response = $provider->capturePaymentOrder($request->token);
		
        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
			
			$orderIds  = json_decode(base64_decode($order), true);
			foreach($orderIds as $order_id)	{
				
				#CJ-DROPSHIPPING
				/* $cjService   = new CjDropshippingService();
				$result		 = $cjService->createCjOrdersFromLocalOrder($order_id); */
				
				#AUTO-DS DROPSHIPPING
				$autoDsService = new AutoDSService();
				$autoDsService->createAutoDSOrdersFromLocalOrder($order_id);
				
				#MYUS SHIPPING OPTIONS
				$MyUSServicesinfo = new MyUSService();
				$MyUSServicesinfo->pushMyUSOrder($order_id);
				
				$orderDetails = Order::where('id', $order_id)->first();
				$orderDetails->payment_method  = 'Paypal';
				$orderDetails->payment_status  = 'paid';
				$orderDetails->order_status    = 'confirmed';
				$orderDetails->payment_transaction_id = $response['id'];
				$orderDetails->save();
				
				#COUPON
				session()->forget('coupon');
				
				#EMAIL
				$orderInfo = Order::with('orderProduct.product.userInfo', 'orderTotal')->where('id',$order_id)->first();
			
				Mail::send('emails.order-receipt', ['order' => $orderInfo], function ($message) use ($orderInfo) {
					$message->to([$orderInfo->email ?? '', get_admin_email(),get_vendor_email($orderInfo->vendor_id)])
							->subject('Your Order Receipt - #' . $orderInfo->order_number);
				}); 
			}
			
			#CLEAR CART ITEMS
			$user ? CartItem::where('user_id', $userId)->delete() : session()->forget('cart');
			
            return redirect('/order-complete')->with('success', 'Payment successful!');
        } else {
            return redirect('/checkout')->with('error', 'Payment failed.');
        }
    }

    public function cancel($order)
    {
        $orderIds  = json_decode(base64_decode($order), true);
        foreach ($orderIds as $order_id) {
            
			$orderDetails = Order::find($order_id);
            
			if ($orderDetails) {
                $orderDetails->payment_method  = 'Paypal';
                $orderDetails->payment_status  = 'failed';
                $orderDetails->order_status    = 'cancelled';
                $orderDetails->payment_transaction_id = NULL;
                $orderDetails->save();
				
				#COUPON
				session()->forget('coupon');
				
                $orderInfo = Order::with('orderProduct.product.userInfo', 'orderTotal')
                                  ->where('id',$order_id)->first();

                Mail::send('emails.order-receipt', ['order' => $orderInfo], function ($message) use ($orderInfo) {
                    $message->to([$orderInfo->email ?? '', get_admin_email(),get_vendor_email($orderInfo->vendor_id)])
                            ->subject('Your Order Cancelled - #' . $orderInfo->order_number);
                });
            }
        }
		return redirect('/checkout')->with('error', 'You canceled the payment.');
    }
	
	
	#PARTNER ORDER PAYMENT SECTION
	public function createPartnerOrderPayment(Request $request, $order)
	{   
		try {
		
			$orderID   = $order;
			
			if (empty($orderID)) {
				return redirect()->back()->with('error', 'Invalid order reference.');
			}

			$orders        = PartnerOrder::where('id',$orderID)->first();
			$grandTotal    = $orders->amount;
			$orderedData   = $orders;
			$orderCurrency = $orders->currency;
			$currencyInfo  = Currency::where('symbol',$orderCurrency)->first()->code;
			
			if($currencyInfo=='INR') {
			  $grandTotal   = convertINRtoUSD($grandTotal);
			  $currencyInfo = 'USD';
			}
			
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
				'currency'       => $currencyInfo,
				'notify_url'     => '',
				'locale'         => 'en_US',
				'validate_ssl'   => true,
			];
			
			$provider = new PayPalClient;
			$paypal   = $provider->setApiCredentials($configData);
			$token    = $provider->getAccessToken();

			if (isset($token['error'])) {
				$errorMessage = $token['error']['error_description'] ?? 'PayPal authentication failed. Please check your credentials.';
				return back()->with('error', $errorMessage);
			}
			
			$provider->setAccessToken($token);

			$response = $provider->createOrder([
				"intent" => "CAPTURE",
				"purchase_units" => [[
					"amount" => [
						"currency_code" => $currencyInfo,
						"value"         => $grandTotal,
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
							"postal_code"    => $orderedData->billing_zipcode,
							//"country_code"   => strtoupper($orderedData->billing_country), 
							"country_code"   => 'US',
						]
					],
				]],
				"application_context" => [
					"cancel_url" => route('partner.paypal.cancelPartnerOrder',$orderID),
					"return_url" => route('partner.paypal.successPartnerOrder', $orderID),
				]
			]);

			if (isset($response['id']) && $response['status'] == 'CREATED') {
				foreach ($response['links'] as $link) {
					if ($link['rel'] === 'approve') {
						return redirect()->away($link['href']);
					}
				}
			}

			$errorMessage = $response['message'] ?? 'Something went wrong.';
			if (isset($response['error']['details'][0]['description'])) {
				$errorMessage .= ' - ' . $response['error']['details'][0]['description'];
			}

			return redirect()->back()->with('error', $errorMessage);

		} catch (\Exception $e) {
			\Log::error('PayPal Payment Error: '.$e->getMessage(), [
				'orderID' => $order ?? null,
				//'user_id' => auth()->id(),
			]);
			return back()->with('error', 'PayPal error:'.$e->getMessage());
		}
	}
	
	public function successPartnerOrder(Request $request,$order)
    {  
		$user    = auth()->user();
		$isGuest = !$user;
		$userId  = $user?->id;
		
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
        $paypal   = $provider->setApiCredentials($configData);
        $token    = $provider->getAccessToken();
		
		if (isset($token['error'])) {
			$errorMessage = $token['error']['error_description'] ?? 'PayPal authentication failed. Please check your credentials.';
			return back()->with('error', $errorMessage);
		}
		
        $provider->setAccessToken($token);

        $response = $provider->capturePaymentOrder($request->token);
		
        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
				
				$orderDetails = PartnerOrder::where('id', $order)->first();
				$orderDetails->payment_method  = 'Paypal';
				$orderDetails->payment_status  = 'paid';
				$orderDetails->status    = 'confirmed';
				$orderDetails->payment_transaction_id = $response['id'];
				$orderDetails->save();
				
				#EMAIL
				$orderInfo = PartnerOrder::with('product')->where('id',$order)->first();
			
				Mail::send('emails.partner-order-receipt', ['order' => $orderInfo], function ($message) use ($orderInfo) {
					$message->to([$orderInfo->billing_email ?? '', get_admin_email(),get_vendor_email($orderInfo->vendor_id)])
							->subject('Your Partner Order Receipt - #' . $orderInfo->order_number);
				}); 
			
			
            return redirect('/partner/checkout/success/'.$order)->with('success', 'Payment successful!');
        } else {
			return redirect('/partner/checkout/cancel/'.$order)->with('success', 'Payment failed.');
        }
    }

    public function cancelPartnerOrder($order)
    {
        
		$orderDetails = PartnerOrder::find($order);
		
		if ($orderDetails) {
			$orderDetails->payment_method  = 'Paypal';
			$orderDetails->payment_status  = 'failed';
			$orderDetails->status          = 'cancelled';
			$orderDetails->payment_transaction_id = NULL;
			$orderDetails->save();
			
			$orderInfo = PartnerOrder::with('product')
							  ->where('id',$order)->first();

			Mail::send('emails.partner-order-receipt', ['order' => $orderInfo], function ($message) use ($orderInfo) {
				$message->to([$orderInfo->billing_email ?? '', get_admin_email()])
						->subject('Your Partner Order Cancelled - #' . $orderInfo->order_number);
			});
		}
		return redirect('/partner/checkout/cancel/'.$order)->with('error', 'You canceled the payment');
    }
	
}
