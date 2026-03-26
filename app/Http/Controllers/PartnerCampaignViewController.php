<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PartnerCampaign;
use App\Models\PartnerProduct;
use App\Models\PartnerOrder;
use App\Models\Country;
use App\Models\PaymentGateway;
use App\Models\ThemeSetting;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use Illuminate\Support\Facades\Mail;

class PartnerCampaignViewController extends Controller
{
    public function index(Request $request)
	{
		$campaigns = PartnerCampaign::where('status', 'active')
			->whereDate('start_date', '<=', now())
			->whereDate('end_date', '>=', now())
			->with(['products' => function($q){
				$q->where('status', 'approved');
			}])
			->latest()
			->get();

		// Group products → category wise → vendor wise
		foreach ($campaigns as $campaign) {
			$campaign->groupedProducts =
				$campaign->products
					->groupBy('category_id')     // Category cart
					->map(function ($categoryItems) {
						return $categoryItems->groupBy('vendor_id'); // Vendor wise
					});
		}

		return view('partner_campaigns.index', compact('campaigns'));
	}

	
	public function show(Request $request, PartnerProduct $product)
	{
		if ($product->status !== 'approved' || $product->max_quantity <= 0) {
			return redirect()->back()->with('error', 'This product is not available.');
		}

		$quantity = $request->input('quantity', 1);

		$total = $product->new_price * $quantity;
		
		
		$country            = Country::get();
		$defaultSetting     = ThemeSetting::first();
		$addresses          = Auth::check() ? Address::where('user_id', Auth::id())->get() : collect();
		$paymentGatewayList = PaymentGateway::where('status',true)->get();

		return view('partner_campaigns.checkout', compact('product', 'quantity', 'total','paymentGatewayList','country','defaultSetting','addresses'));
	}

	public function process(Request $request, PartnerProduct $product)
	{
		$request->validate([
			'quantity'        => 'required|integer|min:1',
			'payment_method'  => 'required|in:cod,paypal,stripe,wallet',
		]);

		// Determine if using existing address or new address
		$usingExistingAddress = $request->has('address_id') && !$request->has('use_new');
		$usingNewAddress      = $request->has('use_new') || !$request->has('address_id');

		// Validate based on address type
		if ($usingNewAddress) {
			$request->validate([
				'billing_first_name'   => 'required|string|max:100',
				'billing_last_name'    => 'required|string|max:100',
				'billing_country'      => 'required|string|max:100',
				'billing_address_1'    => 'required|string|max:255',
				'billing_city'         => 'required|string|max:100',
				'billing_state'        => 'required|string|max:100',
				'billing_phone'        => 'required|regex:/^[0-9]{10,15}$/',
				'billing_email'        => 'required|email|max:255',
			]);
		}

		$quantity = $request->quantity;
		$amount = $product->new_price * $quantity;
	
		if ($product->max_quantity < $quantity) {
			return back()->with('error', 'Only ' . $product->max_quantity . ' items available.');
		}

		// Prepare address data
		if ($usingExistingAddress && $request->address_id) {
			// Use existing address from addresses table
			$address = \App\Models\Address::find($request->address_id);
			
			if (!$address) {
				return back()->with('error', 'Selected address not found.');
			}

			$billingData = [
				'billing_first_name'    => $address->first_name,
				'billing_last_name'     => $address->last_name,
				'billing_company'       => $address->company ?? '',
				'billing_country'       => $address->country,
				'billing_address_1'     => $address->address_1,
				'billing_address_2'     => $address->address_2 ?? '',
				'billing_city'          => $address->city,
				'billing_state'         => $address->state,
				'billing_zipcode'       => $address->zipcode ?? '',
				'billing_phone'         => $address->phone,
				'billing_email'         => auth()->user()->email ?? $request->billing_email,
			];

			// If shipping is different, use shipping form data, otherwise use billing data
			if ($request->shipping_toggle_value == '1') {
				
				$shippingData = [
					'shipping_first_name'     => $request->shipping_first_name ?? $address->first_name,
					'shipping_last_name'      => $request->shipping_last_name ?? $address->last_name,
					'shipping_company'        => $request->shipping_company_name ?? '',
					'shipping_country'        => $request->shipping_country ?? $address->country,
					'shipping_address_1'      => $request->shipping_street_address_1 ?? $address->address_1,
					'shipping_address_2'      => $request->shipping_street_address_2 ?? $address->address_2,
					'shipping_city'           => $request->shipping_city ?? $address->city,
					'shipping_state'          => $request->shipping_state ?? $address->state,
					'shipping_zipcode'        => $request->shipping_zipcode ?? $address->zipcode,
				];
				
			} else {
				
				$shippingData = [
					'shipping_first_name'    => $billingData['billing_first_name'],
					'shipping_last_name'     => $billingData['billing_last_name'],
					'shipping_company'       => $billingData['billing_company'],
					'shipping_country'       => $billingData['billing_country'],
					'shipping_address_1'     => $billingData['billing_address_1'],
					'shipping_address_2'     => $billingData['billing_address_2'],
					'shipping_city'          => $billingData['billing_city'],
					'shipping_state'         => $billingData['billing_state'],
					'shipping_zipcode'       => $billingData['billing_zipcode'],
				];
			}

		} else {
			// Use new address from form
			$billingData = [
				'billing_first_name'   => $request->billing_first_name,
				'billing_last_name'    => $request->billing_last_name,
				'billing_company'      => $request->billing_company_name ?? '',
				'billing_country'      => $request->billing_country,
				'billing_address_1'    => $request->billing_address_1,
				'billing_address_2'    => $request->billing_address_2 ?? '',
				'billing_city'         => $request->billing_city,
				'billing_state'        => $request->billing_state,
				'billing_zipcode'      => $request->billing_zip ?? '',
				'billing_phone'        => $request->billing_phone,
				'billing_email'        => $request->billing_email,
			];

			// If shipping is different, use shipping form data, otherwise use billing data
			if ($request->shipping_toggle_value == '1') {
				$shippingData = [
					'shipping_first_name'  => $request->shipping_first_name ?? $request->billing_first_name,
					'shipping_last_name'   => $request->shipping_last_name ?? $request->billing_last_name,
					'shipping_company'     => $request->shipping_company_name ?? '',
					'shipping_country'     => $request->shipping_country ?? $request->billing_country,
					'shipping_address_1'   => $request->shipping_street_address_1 ?? $request->billing_address_1,
					'shipping_address_2'   => $request->shipping_street_address_2 ?? $request->billing_address_2,
					'shipping_city'        => $request->shipping_city ?? $request->billing_city,
					'shipping_state'       => $request->shipping_state ?? $request->billing_state,
					'shipping_zipcode'     => $request->shipping_zipcode ?? $request->billing_zip,
				];
			} else {
				$shippingData = [
					'shipping_first_name'   => $billingData['billing_first_name'],
					'shipping_last_name'    => $billingData['billing_last_name'],
					'shipping_company'      => $billingData['billing_company'],
					'shipping_country'      => $billingData['billing_country'],
					'shipping_address_1'    => $billingData['billing_address_1'],
					'shipping_address_2'    => $billingData['billing_address_2'],
					'shipping_city'         => $billingData['billing_city'],
					'shipping_state'        => $billingData['billing_state'],
					'shipping_zipcode'      => $billingData['billing_zipcode'],
				];
			}
		}

		// Generate order number and prepare data outside transaction
		$orderNumber = 'PO-' . date('Ymd') . '-' . strtoupper(uniqid());
		
		#PAYMENT STATUS
		if($request->payment_method=='stripe' || $request->payment_method=='paypal' || $request->payment_method=='wallet'){
			$paymentStatus = 'failed';
		}else{
			$paymentStatus = 'pending';
		}
		
		$orderData = [
			'user_id'                  => auth()->id(),
			'vendor_id'                => $product->vendor_id ?? null,
			'partner_campaign_id'      => $product->partner_campaign_id ?? null,
			'order_number'             => $orderNumber,
			'partner_product_id'       => $product->id,
			'quantity'                 => $quantity,
			'amount'                   => priceCalculatedOnlyAccordingToCurrency($amount),
			'currency'                 => getDefaultSelectedCurrency() ?? 'USD', 
			'payment_method'           => $request->payment_method,
			'payment_status'           => $paymentStatus,
			'payment_transaction_id'   => null,
			'tracking_no'              => null,
			'shipping_provider'        => 'Standard Shipping',
			'shipped_at'               => null,
			'reason'                   => null,
			'order_notes'              => $request->order_notes ?? '',
		];

		// Merge billing and shipping data
		$orderData = array_merge($orderData, $billingData, $shippingData);

		$order = null;

		DB::transaction(function () use (&$order, $product, $quantity, $orderData) {
			$order = \App\Models\PartnerOrder::create($orderData);
			$product->decrement('max_quantity', $quantity);
		});

		// Handle different payment methods AFTER transaction
		switch ($request->payment_method) {
			case 'paypal':
				return redirect()->route('partner.paypal.createPartnerOrderPayment', ['order' => $order->id]);
				
			case 'stripe':
				return redirect()->route('partner.stripe.createPartnerOrderPayment', ['order' => $order->id]);
				
			case 'wallet':
				return $this->processWalletPayment($order, $amount);
				
			case 'cod':
			default:
				$this->sendOrderConfirmation($order);
				return redirect()->route('partner.checkout.success', $order->id)
								 ->with('success', 'Order placed successfully!');
		}
	}

	private function processWalletPayment($order, $amount)
	{
		$user = auth()->user();
		$wallet = $user->wallet()->firstOrCreate([
			'user_id' => $user->id,
		], [
			'balance' => 0,
		]);

		if ($wallet->balance < $amount) {
			return back()->with('error', 'Insufficient wallet balance.');
		}

		$oldBalance = $wallet->balance;
		$wallet->balance -= $amount;
		$wallet->save();

		$transactionId = 'TXN-' . strtoupper(uniqid()) . '-' . mt_rand(1000, 9999);
		$currencyInfo   = getDefaultSelectedCurrency() ?? 'USD';
		
		\App\Models\WalletHistory::create([
			'currency'        => \App\Models\Currency::where('symbol', $currencyInfo)->first()->code ?? "USD",
			'wallet_id'       => $wallet->id,
			'user_id'         => $user->id,
			'transaction_id'  => $transactionId,
			'amount'          => $amount,
			'type'            => 'debit',
			'status'          => 'completed',
			'method'          => 'wallet',
			'old_balance'     => $oldBalance,
			'new_balance'     => $wallet->balance,
			'remarks'         => 'Payment for partner campaign order',
		]);

		$order->update([
			'payment_status'          => 'paid',
			'status'                  => 'confirmed',
			'payment_transaction_id'  => $transactionId,
		]);

		$this->sendOrderConfirmation($order);

		return redirect()->route('partner.checkout.success', $order->id)
						 ->with('success', 'Order placed successfully using wallet!');
	}

	private function sendOrderConfirmation($order)
	{
		$orderInfo = PartnerOrder::with('product')->find($order->id);
		Mail::send('emails.partner-order-receipt', ['order' => $orderInfo], function ($message) use ($orderInfo) {
			$message->to([$orderInfo->billing_email ?? '', get_admin_email()])
					->subject('Your Partner Order Receipt - #' . $orderInfo->order_number);
		});
		
	}

	public function success($order)
	{   
	    $orderInfo = PartnerOrder::with('product')->find($order);
		return view('partner_campaigns.success', compact('orderInfo'));
	}
	
	public function cancelOrder($order)
	{   
	    $orderInfo = PartnerOrder::with('product')->find($order);
		return view('partner_campaigns.canceled', compact('orderInfo'));
	}
	
}
