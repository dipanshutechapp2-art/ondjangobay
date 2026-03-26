<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\VendorOrder;
use App\Models\Order_product;
use App\Models\VendorOrderProduct;
use App\Models\Order_total;
use App\Models\VendorOrderTotal;
use App\Models\Country;
use App\Models\State;
use App\Models\Address;
use App\Models\ThemeSetting;
use App\Models\CartItem;
use App\Models\PaymentGateway;
use App\Models\Wallet;
use App\Models\WalletHistory;
use App\Models\Currency;
use Illuminate\Support\Facades\Mail;
use App\Services\CjDropshippingService;
use App\Services\AutoDSService;

use App\Models\Transaction;
use App\Services\CommissionCalculationService;
use App\Models\ProductCommissions;
use App\Models\VendorCommissions;


use App\Services\MyUSService;
use App\Services\DHL\DHLShipmentService;
use App\Services\ShippingService;
use App\Models\ShippingPrice;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
   
    public function selectShipping(Request $request)
	{
		$request->validate([
			'shipping_price_id' => 'required|exists:shipping_prices,id'
		]);

		$shipping = ShippingPrice::with('option')->findOrFail(
			$request->shipping_price_id
		);

		session([
			'shipping_price_id' => $shipping->id,
			'shipping_price'    => $shipping->price,
			'shipping_title'    => $shipping->option->title,
			'shipping_carrier' => $shipping->option->default_carrier,
		]);

		return redirect()->back()->with('success', 'Shipping option has been applied.');

	}
   
    public function checkout() {
		
		#AUTO-DS ORDEE
		
		/* $autoDsServicesinfo = new AutoDSService();	
		$order = $autoDsServicesinfo->createAutoDSOrdersFromLocalOrder(315);
		dd($order);  */
		
		//$orderIds = ['297','296'];
		/* $orderIds = ['297'];
		return redirect()->route('emis.pay', [
				'order' => base64_encode(json_encode($orderIds)),
			]); */
			
		/* $autoDsServicesinfo = new AutoDSService();	
		$order = $autoDsServicesinfo->getAutoDSOrderByIdOnly(2164747791);
		dd($order); */
		
		//$MyUSServicesinfo = new MyUSService();
		//$MyUSServicesinfo->pushMyUSOrder(315);
		
		
		//$shipment = app(DHLShipmentService::class)->create(315);
		//dd($shipment);
		
		
		//$countryCode = $request->country ?? session('country_code', 'IN');
		
		#SHIPPING OPTIONS
		$countryCode = 'IN';
		$shippingServicesInfo = new ShippingService();	
		$shippingOptions      = $shippingServicesInfo->getOptions($countryCode);
		
		if (!session()->has('shipping_price_id') && $shippingOptions->count()) {
			$defaultShipping = $shippingOptions
				->sortBy('price')
				->first();
			session([
				'shipping_price_id' => $defaultShipping->id,
				'shipping_price'    => $defaultShipping->price,
				'shipping_title'    => $defaultShipping->option->title,
				'shipping_carrier'  => $defaultShipping->option->default_carrier,
			]);
		}
		$shippingPrice = session('shipping_price', 0);
		
		$country        = Country::get();
		$paymentGatewayList = PaymentGateway::where('status',true)->get();
		$defaultSetting = ThemeSetting::first();
		$addresses      = Auth::check() ? Address::where('user_id', Auth::id())->get() : collect();
		
		$cartItems = [];

		if (Auth::check()) {

			$items = CartItem::with('product.userInfo')
				->where('user_id', Auth::id())
				->get();

			foreach ($items as $item) {
				$product = $item->product;
				if (!$product) continue;

				$cartItems[] = [
					'cart_key'     => $item->id,
					'product_id'   => $product->id,
					'slug'         => $product->slug,
					'name'         => $product->name,
					'price'        => $item->price,
					'quantity'     => $item->quantity,
					'image'        => $product->image ? asset('uploads/products/' . $product->image) : '',
					'vendor'       => $product->userInfo->name ?? 'N/A',
					'origin'       => $product->type ?? 'unknown',
					'subtotal'     => $item->price * $item->quantity,
					'variant_text' => $this->getVariantText($item->variants ?? []),
				];
			}
			
		} else {
		
			$cart = session()->get('cart', []);
			foreach ($cart as $key => $item) {
				$product = Product::with('userInfo')->find($item['product_id']);
				if (!$product) continue;

				$cartItems[] = [
					'cart_key'     => $key,
					'product_id'   => $item['product_id'],
					'slug'         => $product->slug,
					'name'         => $item['name'],
					'price'        => $item['price'],
					'quantity'     => $item['quantity'],
					'image'        => $item['image'],
					'vendor'       => $product->userInfo->name ?? 'N/A',
					'origin'       => $product->type ?? 'unknown',
					'subtotal'     => $item['price'] * $item['quantity'],
					'variant_text' => $this->getVariantText($item['variants'] ?? []),
				];
			}
		}
		
		return view('checkout.index', compact('country', 'cartItems', 'addresses', 'defaultSetting', 'paymentGatewayList', 'shippingOptions','shippingPrice'));
	}
	
	public function getStates(Request $request,$country_name){
		
		$country = Country::where('name',$country_name)->first();
		$states  = State::where('country_id', $country->id)->get();
		
        return response()->json($states);
	}
	
	private function getVariantText($variants = [])
    {
        $text = '';

        if (!empty($variants)) {
            foreach ($variants as $type => $valueId) {
                $variant = ProductVariant::with('attributeValue')->where('value', $valueId)->first();
                if ($variant && $variant->attributeValue) {
                    $text .= ucfirst($type) . ': ' . $variant->attributeValue->value . ', ';
                }
            }
        }

        return rtrim($text, ', ');
    }
	    
	public function placeOrder(Request $request)
	{	
		//dd($request->all());
		$user    = auth()->user();
		$isGuest = !$user;
		$userId  = $user?->id;

		$cart = [];

		if (auth()->check()) {
			$cartItems = CartItem::with('product')->where('user_id', auth()->id())->get();
			foreach ($cartItems as $item) {
				$cart[] = [
					'product_id' => $item->product_id,
					'name'       => $item->product->name ?? '',
					'price'      => $item->price,
					'quantity'   => $item->quantity,
					'variants'   => $item->variants,
				];
			}
		} else {
			$cart = session()->get('cart', []);
		}

		if (empty($cart)) {
			return redirect()->back()->with('error', 'Your cart is empty.');
		}

		$subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
		$currency = getDefaultSelectedCurrency();

		$coupon = session('coupon');

		$rules = [
			'payment_method' => 'required|in:cod,paypal,stripe,wallet,emis',
		];

		if ($isGuest || $request->use_new === 'on') {
			$rules = array_merge($rules, [
				'billing_first_name'   => 'required|string|max:100',
				'billing_last_name'    => 'required|string|max:100',
				'billing_company_name' => 'nullable|string|max:150',
				'billing_country'      => 'required|string|max:100',
				'billing_address_1'    => 'required|string|max:255',
				'billing_address_2'    => 'nullable|string|max:255',
				'billing_city'         => 'required|string|max:100',
				'billing_state'        => 'required|string|max:100',
				'billing_zip'          => 'nullable|string|max:20',
				//'billing_phone'        => 'required|regex:/^[0-9]{10,15}$/',
				'billing_phone'        => 'required',
				'billing_email'        => 'required|email|max:255',
				'order_notes'          => 'nullable|string|max:1000',
			]);

			if ($request->shipping_toggle_value == '1') {
				$rules = array_merge($rules, [
					'shipping_first_name'       => 'required|string|max:100',
					'shipping_last_name'        => 'required|string|max:100',
					'shipping_company_name'     => 'nullable|string|max:150',
					'shipping_country'          => 'required|string|max:100',
					'shipping_state'            => 'required|string|max:100',
					'shipping_street_address_1' => 'required|string|max:255',
					'shipping_street_address_2' => 'nullable|string|max:255',
					'shipping_city'             => 'required|string|max:100',
					'shipping_zipcode'          => 'nullable|string|max:20',
				]);
			}
		} else {
			$rules = array_merge($rules, [
				'address_id' => 'required|exists:address,id,user_id,' . $userId,
			]);
		}

		$validated = $request->validate($rules);

		if (!$isGuest && $request->use_new === 'on') {
			Address::where('user_id', $userId)->update([
				'is_default'  => '0',
				'is_billing'  => '0',
				'is_shipping' => '0',
			]);

			$address               = new Address;
			$address->user_id      = $userId;
			$address->first_name   = $validated['billing_first_name'];
			$address->last_name    = $validated['billing_last_name'];
			$address->company      = $validated['billing_company_name'] ?? '';
			$address->country      = $validated['billing_country'];
			$address->address_1    = $validated['billing_address_1'];
			$address->address_2    = $validated['billing_address_2'] ?? '';
			$address->city         = $validated['billing_city'];
			$address->state        = $validated['billing_state'];
			$address->zipcode      = $validated['billing_zip'] ?? '';
			$address->phone        = $validated['billing_phone'];
			$address->is_default   = '1';
			$address->is_billing   = '1';
			$address->is_shipping  = $request->shipping_toggle_value == '1' ? '0' : '1';
			$address->save();
		}

		if ($isGuest || $request->use_new === 'on') {
			$billing = [
				'first_name' => $validated['billing_first_name'],
				'last_name'  => $validated['billing_last_name'],
				'company'    => $validated['billing_company_name'] ?? '',
				'country'    => $validated['billing_country'],
				'address_1'  => $validated['billing_address_1'],
				'address_2'  => $validated['billing_address_2'] ?? '',
				'city'       => $validated['billing_city'],
				'state'      => $validated['billing_state'],
				'zipcode'    => $validated['billing_zip'] ?? '',
				'phone'      => $validated['billing_phone'],
				'email'      => $validated['billing_email'],
			];
		} else {
			$defaultAddress = Address::where('user_id', $userId)->where('id', $validated['address_id'])->first();
			$billing = $defaultAddress ? $defaultAddress->toArray() : [];
		}

		if ($request->shipping_toggle_value == '1') {
			$shipping = [
				'first_name' => $validated['shipping_first_name'],
				'last_name'  => $validated['shipping_last_name'],
				'company'    => $validated['shipping_company_name'] ?? '',
				'country'    => $validated['shipping_country'],
				'state'      => $validated['shipping_state'],
				'address_1'  => $validated['shipping_street_address_1'],
				'address_2'  => $validated['shipping_street_address_2'] ?? '',
				'city'       => $validated['shipping_city'],
				'zipcode'    => $validated['shipping_zipcode'] ?? '',
			];
		} else {
			$shipping = $billing;
		}

		$grouped = collect($cart)->groupBy(function ($item) {
			$product = Product::find($item['product_id']);
			return $product->stores->first()->user_id ?? null;
		});

		$vendorData = $grouped->map(function ($items) {
			return [
				'products' => $items,
				'total'    => $items->sum(fn($item) => $item['price'] * $item['quantity']),
			];
		});
		
		#PAYMENT STATUS
		if($validated['payment_method']=='stripe' || $validated['payment_method']=='paypal' || $validated['payment_method']=='wallet'){
			$payment_status = 'failed';
		}else{
			$payment_status = 'pending';
		}
		
		#ADD SHIPPING PRICE GLOBAL
		$shippingPriceId = $request->input('shipping');
		$shippingTotal   = 0;
		$shippingInfo 	 = ShippingPrice::with('option')->findOrFail($shippingPriceId);
		
		if(!empty($shippingInfo->price) && $shippingInfo->price>0 ){
			$shippingTotal = $shippingInfo->price;
		}
		$shippingApplied = false;
		
		$orderIds = [];
		$orderTotalAmt = 0;

		foreach ($grouped as $vendorId => $vendorItems) {
			
			$vendorSubtotal = $vendorData[$vendorId]['total'];
			$discountAmount = 0;
			$couponId       = null;
			$couponCode     = null;

			if ($coupon) {
				if ($coupon['vendor_id'] && $coupon['vendor_id'] == $vendorId) {
					$discountAmount = min($coupon['discount'], $vendorSubtotal);
					$couponId       = $coupon['id'];
					$couponCode     = $coupon['code'];
				} elseif (!$coupon['vendor_id']) {
					$discountAmount = round(($vendorSubtotal / $subtotal) * $coupon['discount'], 2);
					$couponId       = $coupon['id'];
					$couponCode     = $coupon['code'];
				}
			}

			$finalTotal = $vendorSubtotal - $discountAmount;
			
			
			$applyShipping = !$shippingApplied;
			
			
			#CREATE NEW ORDER
			$order                       = new Order;
			$order->user_id              = $userId ?? null;
			$order->vendor_id            = $vendorId;
			$order->is_guest             = $isGuest ? '1' : '0';
			$order->coupon_id            = $couponId;
			$order->coupon_code          = $couponCode;
			$order->coupon_amount        = $discountAmount;
			$order->payment_method       = $validated['payment_method'];
			$order->order_status         = 'pending';
			$order->payment_status       = $payment_status;
			$order->total_amount         = priceCalculatedOnlyAccordingToCurrency($finalTotal+($applyShipping ? $shippingTotal : 0));
			
			#SHIPPING DETAILS
			$order->shipping_option_id   = $shippingInfo->shipping_option_id;
			$order->shipping_title       = $shippingInfo->option->title ?? null;
			$order->shipping_carrier     = $shippingInfo->option->default_carrier;
			$order->shipping_eta         = $shippingInfo->eta_min . '-' . $shippingInfo->eta_max;
			
			$order->currency             = $currency;
			$order->order_notes          = $validated['order_notes'] ?? '';
			$order->order_number         = 'ORDER' . rand(100000, 999999);
			//$order->tracking_number      = 'TRACK' . rand(100000, 999999);
			$order->tracking_number      = $this->generateTrackingNo();
			$order->shipping_provider    = $shippingInfo->option->title ?? '';

			#BILLING
			$order->billing_first_name   = $billing['first_name'] ?? $request->billing_first_name;
			$order->billing_last_name    = $billing['last_name'] ?? $request->billing_last_name;
			$order->billing_company      = $billing['company'] ?? $request->billing_company_name;
			$order->billing_country      = $billing['country'] ?? $request->billing_country;
			$order->billing_address_1    = $billing['address_1'] ?? $request->billing_address_1;
			$order->billing_address_2    = $billing['address_2'] ?? $request->billing_address_2;
			$order->billing_city         = $billing['city'] ?? $request->billing_city;
			$order->billing_state        = $billing['state'] ?? $request->billing_state;
			$order->billing_zipcode      = $billing['zipcode'] ?? ($request->billing_zip ?? '');
			$order->phone                = $billing['phone'] ?? $request->billing_phone;
			$order->email                = $billing['email'] ?? $request->billing_email ?? $user?->email;

			#SHIPPING
			$order->shipping_first_name  = $shipping['first_name'] ?? $request->shipping_first_name;
			$order->shipping_last_name   = $shipping['last_name'] ?? $request->shipping_last_name;
			$order->shipping_company     = $shipping['company'] ?? $request->shipping_company_name;
			$order->shipping_country     = $shipping['country'] ?? $request->shipping_country;
			$order->shipping_state       = $shipping['state'] ?? $request->shipping_state;
			$order->shipping_address_1   = $shipping['address_1'] ?? $request->shipping_street_address_1;
			$order->shipping_address_2   = $shipping['address_2'] ?? $request->shipping_street_address_2;
			$order->shipping_city        = $shipping['city'] ?? $request->shipping_city;
			$order->shipping_zipcode     = $shipping['zipcode'] ?? ($request->shipping_zipcode ?? '');

			$order->save();

			$vendorSubtotal = 0;
			
			#CALL COMMISSIONS SERVICES
			$commissionService = new CommissionCalculationService();
			
			
			#ORDER PRODUCTS
			foreach ($vendorItems as $item) {
				
				
				#GET THE PRODUCT COMMISSIONS START
				$productCommission = ProductCommissions::where('product_id', $item['product_id'])->first();

				$vendorCommission = null;
				if (!empty($productCommission) && !empty($productCommission->vendor_id)) {
					$vendorCommission = VendorCommissions::where('vendor_id', $productCommission->vendor_id)->first();
				}
				if (empty($vendorCommission)) {
					$vendorCommission = VendorCommissions::where('vendor_id', $vendorId)->first();
				}
				
				$commission = $commissionService->calculate($productCommission, $vendorCommission, $item['price']);
				
				$transaction = new Transaction();
				$transaction->order_id            = $order->id;
				$transaction->vendor_id           = $vendorId;
				$transaction->product_id          = $item['product_id'];
				$transaction->vendor_amount       = $commission['vendor_amount'];
				$transaction->ondjango_commission = $commission['ondjango_commission'];
				$transaction->commission_rate     = $commission['commission_rate'] ?? 0.00;
				$transaction->payment_method      = $order->payment_method ?? 'unknown';
				$transaction->transaction_id      = null;
				$transaction->payment_flow        = $commission['payment_flow'];
				$transaction->payment_note        = $commission['payment_note'];
				$transaction->vendor_type         = $commission['vendor_type'];
				$transaction->status = 'paid';
				$transaction->save();

				if ($commission['vendor_type'] === 'internal') {
					// Internal Vendor
					// → 100% payment goes to Ondjango
					// → Finance/Admin later settles vendor amount manually or via cron
				} else {
					// External Vendor
					// → Split payment logic (example)
					// PaymentGateway::splitPayment([
					//     'vendor_account' => $vendorCommission->bank_account,
					//     'vendor_amount'  => $commission['vendor_amount'],
					//     'ondjango_amount' => $commission['ondjango_commission'],
					// ]);
				}
				
				#GET THE PRODUCT COMMISSIONS END
			
				$product  = Product::with('stores.user')->where('id', $item['product_id'])->first();
				$store    = $product->stores->first();

				$orderProduct                       = new Order_product;
				$orderProduct->order_id             = $order->id;
				$orderProduct->store_id             = $store?->id;
				$orderProduct->vendor_id            = $vendorId;
				$orderProduct->vendor_amount        = $commission['vendor_amount'] ?? 0.00;
				$orderProduct->ondjango_commission  = $commission['ondjango_commission'] ?? 0.00;
				$orderProduct->commission_rate      = $commission['commission_rate'] ?? 0.00;
				$orderProduct->currency             = $currency;
				$orderProduct->product_id           = $item['product_id'];
				$orderProduct->name                 = $item['name'];
				$orderProduct->quantity             = $item['quantity'];
				$orderProduct->price                = priceCalculatedOnlyAccordingToCurrency($item['price']);
				$orderProduct->total                = priceCalculatedOnlyAccordingToCurrency($item['price'] * $item['quantity']);
				$orderProduct->variant_ids          = isset($item['variants']) ? implode('-', $item['variants']) : null;
				$orderProduct->variant_text         = $this->buildVariantText($item['variants'] ?? []);
				$orderProduct->save();

				if (!empty($item['variants'])) {
					foreach ($item['variants'] as $valueId) {
						$variant = ProductVariant::where('value', $valueId)->first();
						if ($variant && $variant->stock !== null) {
							$variant->stock = max(0, $variant->stock - $item['quantity']);
							$variant->save();
						}
					}
				} else {
					$product = Product::find($item['product_id']);
					if ($product && $product->quantity !== null) {
						$product->quantity = max(0, $product->quantity - $item['quantity']);
						$product->save();
					}
				}

				$getTotal        = $item['price'] * $item['quantity'];
				$vendorSubtotal += $getTotal;
			}

			# TOTALS
			$orderTotal = [];

			$orderTotal[] = [
				'order_id'   => $order->id,
				'meta_key'   => 'Sub Total',
				'meta_value' => priceCalculatedOnlyAccordingToCurrency($vendorSubtotal),
				'currency'   => $currency,
			];

			if ($discountAmount > 0) {
				$orderTotal[] = [
					'order_id'   => $order->id,
					'meta_key'   => 'Discount',
					'meta_value' => -priceCalculatedOnlyAccordingToCurrency($discountAmount),
					'currency'   => $currency,
				];
			}

			$orderTotal[] = [
				'order_id'   => $order->id,
				'meta_key'   => 'Shipping',
				'meta_value' => priceCalculatedOnlyAccordingToCurrency(($applyShipping ? $shippingTotal : 0)),
				'currency'   => $currency,
			];
			$orderTotal[] = [
				'order_id'   => $order->id,
				'meta_key'   => 'Total',
				'meta_value' => priceCalculatedOnlyAccordingToCurrency($finalTotal+($applyShipping ? $shippingTotal : 0)),
				'currency'   => $currency,
			];
			
			foreach ($orderTotal as $orderTotalList) {
				$orderTotals                  = new Order_total;
				$orderTotals->order_id        = $orderTotalList['order_id'];
				$orderTotals->meta_key        = $orderTotalList['meta_key'];
				$orderTotals->meta_value      = $orderTotalList['meta_value'];
				$orderTotals->currency        = $orderTotalList['currency'];
				$orderTotals->save();
			}
			
			if ($request->payment_method == 'cod') {
				
				
				$orderInfo = Order::with('orderProduct.product.userInfo', 'orderTotal')->where('id', $order->id)->where('vendor_id', $order->vendor_id)->first();
								
				#CREATE CJ DROPSHIPPING ORDER
				/* $cjService   = new CjDropshippingService();
				$result		 = $cjService->createCjOrdersFromLocalOrder($orderInfo->id); */
				
				#AUTO-DS DROPSHIPPING
				$autoDsService = new AutoDSService();
				$autoDsService->createAutoDSOrdersFromLocalOrder($orderInfo->id);
				
				#MYUS SHIPPING OPTIONS
				$MyUSServicesinfo = new MyUSService();
				$MyUSServicesinfo->pushMyUSOrder($orderInfo->id);
				
				#EMAIL
				Mail::send('emails.order-receipt', ['order' => $orderInfo], function ($message) use ($orderInfo) {
					$message->to([$orderInfo->email ?? '', get_admin_email(),get_vendor_email($orderInfo->vendor_id)])
							->subject('Your Order Receipt - #' . $orderInfo->order_number);
				});
			}
			
			$orderTotalAmt = $orderTotalAmt+$finalTotal;
			$orderIds[] = $order->id;
			
			$shippingApplied = true;
		}

		if($request->payment_method == 'cod') {
			
			$user ? CartItem::where('user_id', $userId)->delete() : session()->forget('cart');
			session()->forget('coupon');
			return redirect('/order-complete')->with('success', 'Order placed successfully!');
			
		}elseif ($request->payment_method == 'paypal') {
			
			return redirect()->route('paypal.payment', [
				'order' => base64_encode(json_encode($orderIds)),
			]);
			
		}elseif ($request->payment_method == 'emis') {
			
			return redirect()->route('emis.pay', [
				'order' => base64_encode(json_encode($orderIds)),
			]);
				
		}elseif ($request->payment_method == 'stripe') {
			
			return redirect()->route('stripe.payment', [
				'order' => base64_encode(json_encode($orderIds)),
			]);
		}elseif ($request->payment_method == 'wallet') {
			
			#WALLET PAYMENT
			$user   = auth()->user();
			$amount = $orderTotalAmt;

			$wallet = $user->wallet()->firstOrCreate([
				'user_id' => $user->id,
			], [
				'balance' => 0,
			]);

			if ($wallet->balance < $amount) {
				return back()->with('error', 'Insufficient wallet balance.');
			}
			$oldBalance       = $wallet->balance;
			$wallet->balance -= $amount;
			$wallet->save();

			$transactionId = 'TXN-' . strtoupper(uniqid()) . '-' . mt_rand(1000, 9999);

			WalletHistory::create([
				'currency'       => Currency::where('symbol',$currency)->first()->code ?? "USD",
				'wallet_id'      => $wallet->id ,
				'user_id'        => $user->id,
				'transaction_id' => $transactionId,
				'amount'         => $amount,
				'type'           => 'debit',
				'status'         => 'completed',
				'method'         => 'wallet',
				'old_balance'    => $oldBalance,
				'new_balance'    => $wallet->balance,
				'remarks'        => 'Payment for order via wallet',
			]);
			
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
					$orderDetails->payment_method          = 'Wallet';
					$orderDetails->payment_status          = 'paid';
					$orderDetails->order_status            = 'confirmed';
					$orderDetails->payment_transaction_id  = $transactionId;
					$orderDetails->save();
					
					$orderInfo = Order::with('orderProduct.product.userInfo', 'orderTotal')->where('id',$order_id)->first();
					Mail::send('emails.order-receipt', ['order' => $orderInfo], function ($message) use ($orderInfo) {
						$message->to([$orderInfo->email ?? '', get_admin_email(),get_vendor_email($orderInfo->vendor_id)])
								->subject('Your Order Receipt - #' . $orderInfo->order_number);
					});
				}
			}
			
			#COUPON
			session()->forget('coupon');	
			
			$user ? CartItem::where('user_id', $userId)->delete() : session()->forget('cart');
			return redirect('/order-complete')->with('success', 'Order placed successfully!');
		}
	}

	private function buildVariantText($variantIds)
	{
		if (empty($variantIds)) return null;

		$variantTextParts = [];

		foreach ($variantIds as $type =>$valueId) {
			$variant = ProductVariant::with('attributeValue')->where('value', $valueId)->first();

			 if ($variant && $variant->attributeValue) {
				$variantTextParts[] = ucfirst($type) . ': ' . $variant->attributeValue->value . ' ';
			}
		}

		return implode(', ', $variantTextParts);
	}

	public function generateTrackingNo()
	{
		$prefix = '1Z';
		$chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

		$tracking = $prefix;

		for ($i = 0; $i < 16; $i++) {
			$tracking .= $chars[rand(0, strlen($chars) - 1)];
		}

		return $tracking;
	}	
}
