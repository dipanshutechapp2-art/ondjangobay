<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PartnerCampaign;
use App\Models\PartnerProduct;
use App\Models\PartnerOrder;
use App\Models\Country;
use App\Models\PaymentGateway;
use App\Models\ThemeSetting;
use App\Models\Address;
use App\Models\User;
use App\Models\Category;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PartnerProductImport;

class PartnerCampaignApiController extends Controller
{ 
   public function index(Request $request)
	{
		$limit = $request->get('limit', 10);
		
		$campaigns = PartnerCampaign::where('status', 'active')
			->where('start_date', '<=', now())
			->where('end_date', '>=', now())
			->with(['products' => function ($q) {
				$q->where('status', 'approved');
			}])
			->latest()
			->paginate($limit);
		
		$response = [];

		foreach ($campaigns as $campaign) {

			$categoryGroups = [];

			// Group products by category → vendor
			$grouped = $campaign->products
				->groupBy('category_id')
				->map(function ($catItems) {
					return $catItems->groupBy('vendor_id');
				});

			foreach ($grouped as $categoryId => $vendorGroups) {

				$category = Category::find($categoryId);
				if (!$category) continue;

				// category sold qty (from partner_orders)
				$categorySold = PartnerOrder::join('partner_products', 'partner_orders.partner_product_id', '=', 'partner_products.id')
					->where('partner_orders.partner_campaign_id', $campaign->id)
					->where('partner_products.category_id', $categoryId)
					->sum('partner_orders.quantity');

				$goal = (int) ($campaign->goal_quantity ?? 0);
				$maxVolume = (int) ($campaign->cart_max_volume ?? 0);

				$target = $goal > 0 ? $goal : ($maxVolume > 0 ? $maxVolume : 1);
				$percent = round(($categorySold / $target) * 100, 2);

				$remaining = $maxVolume > 0 ? max(0, $maxVolume - $categorySold) : null;

				// build vendors + products
				$vendors = [];

				foreach ($vendorGroups as $vendorId => $products) {
					$vendor = User::find($vendorId);

					$productList = [];

					foreach ($products as $product) {

						$productSold = PartnerOrder::where('partner_product_id', $product->id)->sum('quantity');

						$discount = 0;
						if ($product->old_price > 0) {
							$discount = round((($product->old_price - $product->new_price) / $product->old_price) * 100);
						}

						$productList[] = [
							"id" => $product->id,
							"name" => $product->name,
							"image" => $product->image ? asset($product->image) : asset('/default.png'),
							"new_price" => $product->new_price,
							"old_price" => $product->old_price,
							"discount_percent" => $discount,
							"max_quantity" => (int) $product->max_quantity,
							"product_sold" => $productSold
						];
					}

					$vendors[] = [
						"vendor_id" => $vendorId,
						"vendor_name" => $vendor ? $vendor->name : "Vendor #{$vendorId}",
						"products" => $productList
					];
				}

				$categoryGroups[] = [
					"category_id" => $categoryId,
					"category_name" => $category->name,
					"category_sold" => $categorySold,
					"progress_percent" => $percent,
					"remaining_volume" => $remaining,
					"vendors" => $vendors
				];
			}

			$response[] = [
				"id" => $campaign->id,
				"name" => $campaign->name,
				"description" => $campaign->description,
				"start_date" => $campaign->start_date,
				"end_date" => $campaign->end_date,
				"cart_max_volume" => $campaign->cart_max_volume,
				"goal_quantity" => $campaign->goal_quantity,
				"category_groups" => $categoryGroups
			];
		}

		return response()->json([
			"status" => true,
			"campaigns" => $response
		]);
	}



    public function show(Request $request, PartnerProduct $product)
    {
        if ($product->status !== 'approved' || $product->max_quantity <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'This product is not available.'
            ], 400);
        }

        $quantity = $request->input('quantity', 1);
        $total = $product->new_price * $quantity;

        return response()->json([
            'status' => true,
            'product' => $product,
            'quantity' => $quantity,
            'total' => $total,
            'countries' => Country::get(),
            'theme_setting' => ThemeSetting::first(),
            'addresses' => Auth::check() ? Address::where('user_id', Auth::id())->get() : [],
            'payment_gateways' => PaymentGateway::where('status', true)->get(),
        ]);
    }


    public function process(Request $request, PartnerProduct $product)
    {  
        if ($product->status !== 'approved' || $product->max_quantity <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'This product is not available.'
            ], 400);
        }
		
		$request->validate([
            'quantity'       => 'required|integer|min:1',
            'payment_method' => 'required|in:cod,paypal,stripe,wallet',
        ]);

        $usingExistingAddress = $request->has('address_id') && !$request->has('use_new');
        $usingNewAddress = $request->has('use_new') || !$request->has('address_id');
		
        if ($usingNewAddress) {
            $request->validate([
                'billing_first_name' => 'required|string|max:100',
                'billing_last_name'  => 'required|string|max:100',
                'billing_country'    => 'required|string|max:100',
                'billing_address_1'  => 'required|string|max:255',
                'billing_city'       => 'required|string|max:100',
                'billing_state'      => 'required|string|max:100',
                'billing_phone'      => 'required|regex:/^[0-9]{10,15}$/',
                'billing_email'      => 'required|email|max:255',
            ]);
        }

        $quantity = $request->quantity;
        $amount = $product->new_price * $quantity;

        if ($product->max_quantity < $quantity) {
            return response()->json([
                'status' => false,
                'message' => 'Only ' . $product->max_quantity . ' items available.'
            ], 400);
        }

        // BUILD ADDRESS
        if ($usingExistingAddress && $request->address_id) {
            $address = Address::where('user_id',auth()->id())->find($request->address_id);

            if (!$address) {
                return response()->json(['status' => false, 'message' => 'Address not found.'], 404);
            }

            $billingData = [
                'billing_first_name' => $address->first_name,
                'billing_last_name' => $address->last_name,
                'billing_company' => $address->company,
                'billing_country' => $address->country,
                'billing_address_1' => $address->address_1,
                'billing_address_2' => $address->address_2,
                'billing_city' => $address->city,
                'billing_state' => $address->state,
                'billing_zipcode' => $address->zipcode,
                'billing_phone' => $address->phone,
                'billing_email' => auth()->user()->email,
            ];

            if ($request->shipping_toggle_value == '1') {
                $shippingData = [
                    'shipping_first_name' => $request->shipping_first_name ?? $address->first_name,
                    'shipping_last_name' => $request->shipping_last_name ?? $address->last_name,
                    'shipping_company' => $request->shipping_company_name,
                    'shipping_country' => $request->shipping_country ?? $address->country,
                    'shipping_address_1' => $request->shipping_street_address_1 ?? $address->address_1,
                    'shipping_address_2' => $request->shipping_street_address_2 ?? $address->address_2,
                    'shipping_city' => $request->shipping_city ?? $address->city,
                    'shipping_state' => $request->shipping_state ?? $address->state,
                    'shipping_zipcode' => $request->shipping_zipcode ?? $address->zipcode,
                ];
            } else {
                $shippingData = [
                    'shipping_first_name' => $billingData['billing_first_name'],
                    'shipping_last_name' => $billingData['billing_last_name'],
                    'shipping_company' => $billingData['billing_company'],
                    'shipping_country' => $billingData['billing_country'],
                    'shipping_address_1' => $billingData['billing_address_1'],
                    'shipping_address_2' => $billingData['billing_address_2'],
                    'shipping_city' => $billingData['billing_city'],
                    'shipping_state' => $billingData['billing_state'],
                    'shipping_zipcode' => $billingData['billing_zipcode'],
                ];
            }
        } else {
            // NEW ADDRESS
            $billingData = [
                'billing_first_name' => $request->billing_first_name,
                'billing_last_name' => $request->billing_last_name,
                'billing_company' => $request->billing_company_name,
                'billing_country' => $request->billing_country,
                'billing_address_1' => $request->billing_address_1,
                'billing_address_2' => $request->billing_address_2,
                'billing_city' => $request->billing_city,
                'billing_state' => $request->billing_state,
                'billing_zipcode' => $request->billing_zip,
                'billing_phone' => $request->billing_phone,
                'billing_email' => $request->billing_email,
            ];

            if ($request->shipping_toggle_value == '1') {
                $shippingData = [
                    'shipping_first_name' => $request->shipping_first_name,
                    'shipping_last_name' => $request->shipping_last_name,
                    'shipping_company' => $request->shipping_company_name,
                    'shipping_country' => $request->shipping_country,
                    'shipping_address_1' => $request->shipping_street_address_1,
                    'shipping_address_2' => $request->shipping_street_address_2,
                    'shipping_city' => $request->shipping_city,
                    'shipping_state' => $request->shipping_state,
                    'shipping_zipcode' => $request->shipping_zipcode,
                ];
            } else {
                $shippingData = [
                    'shipping_first_name' => $billingData['billing_first_name'],
                    'shipping_last_name' => $billingData['billing_last_name'],
                    'shipping_company' => $billingData['billing_company'],
                    'shipping_country' => $billingData['billing_country'],
                    'shipping_address_1' => $billingData['billing_address_1'],
                    'shipping_address_2' => $billingData['billing_address_2'],
                    'shipping_city' => $billingData['billing_city'],
                    'shipping_state' => $billingData['billing_state'],
                    'shipping_zipcode' => $billingData['billing_zipcode'],
                ];
            }
        }

        // Order
        $orderNumber = 'PO-' . date('Ymd') . '-' . strtoupper(uniqid());
        
		#PAYMENT STATUS
		if($request->payment_method=='stripe' || $request->payment_method=='paypal' || $request->payment_method=='wallet'){
			$paymentStatus = 'failed';
		}else{
			$paymentStatus = 'pending';
		}

        $orderData = array_merge([
            'user_id'              => auth()->id(),
            'vendor_id'            => $product->vendor_id,
            'partner_campaign_id'  => $product->partner_campaign_id,
            'order_number'         => $orderNumber,
            'partner_product_id'   => $product->id,
            'quantity'             => $quantity,
            'amount'               => priceCalculatedOnlyAccordingToCurrency($amount),
            'currency'             => getDefaultSelectedCurrency() ?? 'USD',
            'payment_method'       => $request->payment_method,
            'payment_status'       => $paymentStatus,
        ], $billingData, $shippingData);
		
        DB::transaction(function () use (&$order, $product, $quantity, $orderData) {
            $order = PartnerOrder::create($orderData);
            $product->decrement('max_quantity', $quantity);
        });

        $paytype = '';
        
        if($request->payment_method=='cod'){
		  
    	   $paytype = 'cod';
		   $this->sendOrderConfirmation($order);
    	   
		}elseif($request->payment_method=='paypal'){
			
			$paytype = 'paypal';
			
		}elseif($request->payment_method=='stripe'){
			
			$paytype = 'stripe';
			
		}elseif ($request->payment_method === "wallet") {
			
			$paytype = 'wallet';
			
            $this->processWalletPayment($order, $amount);
        }

        return response()->json([
            'success'      => true,
            'message'      => 'Order placed successfully',
            'order_id'     => $order->id,
            'order_number' => $order->order_number,
            'pay_type'     => $paytype,
            'order'        => $order->id,
        ]);
    }


    private function processWalletPayment($order, $amount)
    {
        $user = auth()->user();
        $wallet = $user->wallet()->firstOrCreate(['user_id' => $user->id], ['balance' => 0]);

        if ($wallet->balance < $amount) {
            return response()->json([
                'status' => false,
                'message' => 'Insufficient wallet balance.'
            ], 400);
        }

        $oldBalance = $wallet->balance;
        $wallet->balance -= $amount;
        $wallet->save();

        $transactionId = 'TXN-' . strtoupper(uniqid()) . '-' . mt_rand(1000, 9999);
		
		$currencyInfo = getDefaultSelectedCurrency() ?? 'USD'; 
		
        \App\Models\WalletHistory::create([
		    'currency'  => Currency::where('symbol',$currencyInfo)->first()->code ?? "USD",
            'wallet_id' => $wallet->id,
            'user_id' => $user->id,
            'transaction_id' => $transactionId,
            'amount' => $amount,
            'type' => 'debit',
            'status' => 'completed',
            'method' => 'wallet',
            'old_balance' => $oldBalance,
            'new_balance' => $wallet->balance,
            'remarks' => 'Payment for partner campaign order',
        ]);

        $order->update([
            'payment_status' => 'paid',
            'status' => 'confirmed',
            'payment_transaction_id' => $transactionId,
        ]);

        $this->sendOrderConfirmation($order);
    }


    private function sendOrderConfirmation($order)
    {
		$orderInfo = PartnerOrder::with('product')->find($order->id);
		
        Mail::send('emails.partner-order-receipt', ['order' => $orderInfo], function ($message) use ($orderInfo) {
            $message->to([$orderInfo->billing_email, get_admin_email()])
                ->subject('Partner Order Receipt #' . $orderInfo->order_number);
        });
    }


    public function success($id)
    {
        return response()->json([
            'status' => true,
            'order' => PartnerOrder::with('product')->find($id)
        ]);
    }

    public function cancelOrder($id)
    {
        return response()->json([
            'status' => true,
            'order' => PartnerOrder::with('product')->find($id)
        ]);
    }
	
	#VENDOR UPLOAD COMPAIGN PRODUCTS
	public function products(Request $request)
    {   
		$limit = $request->get('limit', 10);
		
        $products = PartnerProduct::where('vendor_id', auth()->id())
            ->with('campaign')
            ->orderBy('id', 'desc')
            ->paginate($limit);

        return response()->json([
            'status' => true,
            'products' => $products
        ]);
	}

    public function downloadTemplate(Request $request)
	{
		$filePath = "templates/campaign/partner_upload_template.xlsx";

		if (!file_exists(public_path($filePath))) {
			return response()->json([
				'status' => false,
				'message' => 'Template not found.'
			], 404);
		}

		$url = url($filePath); // generates full URL

		return response()->json([
			'status' => true,
			'message' => 'Template download link',
			'download_url' => $url
		]);
	}


    public function upload(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:partner_campaigns,id',
            'file'        => 'required|file|mimes:xlsx,csv'
        ]);

        Excel::import(new PartnerProductImport(auth()->id(), $request->campaign_id), $request->file('file'));

        $failed = DB::table('partner_product_import_logs')
            ->where('vendor_id', auth()->id())
            ->where('partner_campaign_id', $request->campaign_id)
            ->count();

        return response()->json([
            'status' => true,
            'message' => $failed > 0 
                ? "$failed rows failed validation."
                : "Products uploaded successfully!"
        ]);
    }

    public function importErrors(Request $request)
    {
        $limit = $request->get('limit', 10);
		$logs = DB::table('partner_product_import_logs')
            ->where('vendor_id', auth()->id())
            ->orderBy('id','desc')
            ->paginate($limit);

        return response()->json([
            'status' => true,
            'errors' => $logs
        ]);
    }

    public function clearImportErrors(Request $request)
    {
        $campaignId = $request->get('campaign_id');

        DB::table('partner_product_import_logs')
            ->where('vendor_id', auth()->id())
            ->when($campaignId, fn($q) => $q->where('partner_campaign_id', $campaignId))
            ->delete();

        return response()->json([
            'status' => true,
            'message' => 'Import errors cleared successfully'
        ]);
    }
	
	#PARTNER VENDOR ORDERS
	public function partnerVendorOrders(Request $request)
    {
        $limit  = $request->get('limit', 10);
		$orders = PartnerOrder::with('user')->where('vendor_id',auth()->id())->orderBy('created_at', 'desc')->paginate($limit);

        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }
	
	#PARTNER USER ORDERS
	public function partnerUserOrders(Request $request)
    {
        $limit  = $request->get('limit', 10);
		$orders = PartnerOrder::with('user')->where('user_id',auth()->id())->orderBy('created_at', 'desc')->paginate($limit);

        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }
	
	public function partnerOrderDetails($order_id)
    {
       $orderInfo = PartnerOrder::with('product')->where('id',$order_id)->first();

        return response()->json([
            'status' => true,
            'data' => $orderInfo
        ]);
    }
	
	public function updateorderStatus(Request $request) {
		
        $validated = $request->validate([
			'order_id'      => 'required|exists:partner_orders,id',
			'order_status'  => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled'
		], [
			'order_id.required' => 'Order ID is required.',
			'order_id.exists'   => 'Order not found.',
			'order_status.in'   => 'Invalid order status.'
		]);
		
		$id                  = $request->order_id;
        $order               = PartnerOrder::findOrFail($id);
        $order->status 		 = $request->order_status;
        $order->save();
		
		#SEND ORDER MAIL
    	$orderInfo = PartnerOrder::with('product')->where('id',$order->id)->first();
		
		$res = Mail::send('emails.partner-order-status', ['order' => $orderInfo], function ($message) use ($orderInfo) {
			$message->to([$orderInfo->billing_email,get_admin_email()])
					->subject('Your Partner Order Receipt - #' . $orderInfo->order_number);
		});
		
        return response()->json([
            'status' => true,
            'data' => $orderInfo
        ]);
    }
	
	public function updatePaymentStatus(Request $request) {
		
        $validated = $request->validate([
			'order_id'        => 'required|exists:partner_orders,id',
			'payment_status'  => 'required|in:pending,paid,failed,refunded',
		], [
			'order_id.required'       => 'Order ID is required.',
			'order_id.exists'         => 'Order not found.',
			'payment_status.required' => 'Payment status is required.',
			'payment_status.in'       => 'Invalid payment status.',
		]);

		$order = PartnerOrder::with('product')->findOrFail($request->order_id);
		$order->payment_status = $request->payment_status;
		$order->save();

		return response()->json([
			'status'  => true,
			'message' => 'Payment status updated successfully.',
			'data'    => $order
		], 200);
    }
	
	public function downloadInvoice($id)
	{
		try {

			$orderInfo = PartnerOrder::with('product')
				->where('id', $id)
				->where('vendor_id', auth()->id())
				->first();

			if (!$orderInfo) {
				return response()->json([
					'status'  => false,
					'message' => 'Order not found or unauthorized.'
				], 404);
			}


			$pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('vendor.invoices.partner_invoice', compact('orderInfo'));

			$base64PDF = base64_encode($pdf->output());

			return response()->json([
				'status'  => true,
				'message' => 'Invoice generated successfully.',
				'file_name' => 'invoice_partner_order_' . $orderInfo->id . '.pdf',
				'pdf_base64' => $base64PDF
			], 200);

		} catch (\Exception $e) {

			return response()->json([
				'status'  => false,
				'message' => 'Error generating invoice.',
				'error'   => $e->getMessage()
			], 500);
		}
	}

	
}
