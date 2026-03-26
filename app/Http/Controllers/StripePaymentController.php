<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use App\Models\Order;
use App\Models\Currency;
use App\Models\CartItem;
use App\Models\PaymentGateway;
use App\Models\PartnerOrder;
use Illuminate\Support\Facades\Mail;
use App\Services\CjDropshippingService;
use App\Services\AutoDSService;
use App\Services\MyUSService;

class StripePaymentController extends Controller
{
    public function createPayment(Request $request, $order)
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
        $currencyInfo    = Currency::where('symbol',$orderCurrency)->first()->code ?? 'usd';
		
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
				
		try {
			$session = StripeSession::create([
				'payment_method_types' => ['card'],
				'line_items' => [[
					'price_data' => [
						'currency' => $currencyInfo,
						'product_data' => [
							'name' => 'Order Payment',
						],
						'unit_amount' => $grandTotal * 100, // in cents
					],
					'quantity' => 1,
				]],
				'mode'        => 'payment',

				'success_url' => route('stripe.success', $orderID) . '?session_id={CHECKOUT_SESSION_ID}',
				'cancel_url'  => route('stripe.cancel', $orderID),
			]);

			return redirect()->away($session->url);

		} catch (ApiErrorException $e) {
			
			\Log::error('Stripe Error: ' . $e->getMessage());
			return back()->with('error', $e->getMessage());
			
		} catch (\Exception $e) {

			\Log::error('General Error: ' . $e->getMessage());
			return back()->with('error', 'Something went wrong: ' . $e->getMessage());
		}
		
    }

    public function success(Request $request, $order)
    {
       	$sessionId = $request->get('session_id');
        
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

        $session = StripeSession::retrieve($sessionId);
        $paymentIntentId = $session->payment_intent;

        $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
        $transactionId = $paymentIntent->charges->data[0]->id ?? null;

        $orderIds  = json_decode(base64_decode($order), true);
        foreach ($orderIds as $order_id) {
            
			#CREATE CJ DROPSHIPPING ORDER
			/* $cjService   = new CjDropshippingService();
			$result		 = $cjService->createCjOrdersFromLocalOrder($order_id); */
			
			#AUTO-DS DROPSHIPPING
			$autoDsService = new AutoDSService();
			$autoDsService->createAutoDSOrdersFromLocalOrder($order_id);
			
			#MYUS SHIPPING OPTIONS
			$MyUSServicesinfo = new MyUSService();
			$MyUSServicesinfo->pushMyUSOrder($order_id);
			
			$orderDetails = Order::find($order_id);
            
			if ($orderDetails) {
                $orderDetails->payment_method  = 'Stripe';
                $orderDetails->payment_status  = 'paid';
                $orderDetails->order_status    = 'confirmed';
                $orderDetails->payment_transaction_id = $paymentIntentId;
                $orderDetails->save();
				
				#COUPON
				session()->forget('coupon');
				
                $orderInfo = Order::with('orderProduct.product.userInfo', 'orderTotal')
                                  ->where('id',$order_id)->first();

                Mail::send('emails.order-receipt', ['order' => $orderInfo], function ($message) use ($orderInfo) {
                    $message->to([$orderInfo->email ?? '', get_admin_email(),get_vendor_email($orderInfo->vendor_id)])
                            ->subject('Your Order Receipt - #' . $orderInfo->order_number);
                });
            }
        }

        $user = auth()->user();
        $user ? CartItem::where('user_id', $user->id)->delete() : session()->forget('cart');

        return redirect('/order-complete')->with('success', 'Payment successful via Stripe!');
    }

    public function cancel($order)
    {	
		$orderIds  = json_decode(base64_decode($order), true);
        foreach ($orderIds as $order_id) {
            
			$orderDetails = Order::find($order_id);
            
			if ($orderDetails) {
                $orderDetails->payment_method  = 'Stripe';
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
        return redirect('/checkout')->with('error', 'Stripe payment canceled.');
    }
	
	
	#PARTNER ORDER PAYMENT SECTION
	public function createPartnerOrderPayment(Request $request, $order)
    {  
        $orderID   = $order;
       
		if (empty($orderID)) {
			return redirect()->back()->with('error', 'Invalid order reference.');
		}

        $orders          = PartnerOrder::where('id',$orderID)->first();
		$grandTotal      = $orders->amount;
		$orderedData     = $orders;
		$orderCurrency   = $orders->currency;
        $currencyInfo    = Currency::where('symbol',$orderCurrency)->first()->code ?? 'usd';
		
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
				
		try {
			$session = StripeSession::create([
				'payment_method_types' => ['card'],
				'line_items' => [[
					'price_data' => [
						'currency' => $currencyInfo,
						'product_data' => [
							'name' => 'Order Payment',
						],
						'unit_amount' => $grandTotal * 100, // in cents
					],
					'quantity' => 1,
				]],
				'mode'        => 'payment',

				'success_url' => route('partner.stripe.successPartnerOrder', $orderID) . '?session_id={CHECKOUT_SESSION_ID}',
				'cancel_url'  => route('partner.stripe.cancelPartnerOrder', $orderID),
			]);

			return redirect()->away($session->url);

		} catch (ApiErrorException $e) {
			
			\Log::error('Stripe Error: ' . $e->getMessage());
			return back()->with('error', $e->getMessage());
			
		} catch (\Exception $e) {

			\Log::error('General Error: ' . $e->getMessage());
			return back()->with('error', 'Something went wrong: ' . $e->getMessage());
		}
		
    }

    public function successPartnerOrder(Request $request, $order)
    {
       	$sessionId = $request->get('session_id');
        
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

        $session = StripeSession::retrieve($sessionId);
        $paymentIntentId = $session->payment_intent;

        $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
        $transactionId = $paymentIntent->charges->data[0]->id ?? null;

		$orderDetails = PartnerOrder::find($order);
		
		if ($orderDetails) {
			$orderDetails->payment_method  = 'Stripe';
			$orderDetails->payment_status  = 'paid';
			$orderDetails->status          = 'confirmed';
			$orderDetails->payment_transaction_id = $paymentIntentId;
			$orderDetails->save();
			
			$orderInfo = PartnerOrder::with('product')
							  ->where('id',$order)->first();

			Mail::send('emails.partner-order-receipt', ['order' => $orderInfo], function ($message) use ($orderInfo) {
				$message->to([$orderInfo->billing_email ?? '', get_admin_email(),get_vendor_email($orderInfo->vendor_id)])
						->subject('Your Order Receipt - #' . $orderInfo->order_number);
			});
		}
        return redirect('/partner/checkout/success/'.$order)->with('success', 'Payment successful via Stripe!');
    }

    public function cancelPartnerOrder($order)
    {	
		$orderIds  = json_decode(base64_decode($order), true);
        foreach ($orderIds as $order_id) {
            
			$orderDetails = PartnerOrder::find($order_id);
            
			if ($orderDetails) {
                $orderDetails->payment_method  = 'Stripe';
                $orderDetails->payment_status  = 'failed';
                $orderDetails->status    = 'cancelled';
                $orderDetails->payment_transaction_id = NULL;
                $orderDetails->save();
				
				#COUPON
				session()->forget('coupon');
				
                $orderInfo = PartnerOrder::with('product')
                                  ->where('id',$order_id)->first();

                Mail::send('emails.partner-order-receipt', ['order' => $orderInfo], function ($message) use ($orderInfo) {
                    $message->to([$orderInfo->billing_email ?? '',get_admin_email(),get_vendor_email($orderInfo->vendor_id)])
                            ->subject('Your Partner Order Cancelled - #' . $orderInfo->order_number);
                });
            }
        }
	   return redirect('/partner/checkout/cancel/'.$order)->with('error', 'Stripe payment canceled');
    }
}
