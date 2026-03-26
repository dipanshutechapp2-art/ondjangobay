<?php

namespace App\Services;

use App\Models\AutoDSToken;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductAttribute;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\VendorStore;
use App\Models\ProductGallery;
use App\Models\AutoDSStore;
use App\Models\Order;
use App\Models\AutoDSOrder;
use App\Models\Country;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Str;

class AutoDSService
{
    protected string $authUrl;
    protected string $apiBase;
    protected string $clientId;
    protected string $redirectUri;
    protected string $orderUri;

    public function __construct()
    {
        $this->authUrl     = config('services.autods.auth_url');
        $this->apiBase     = config('services.autods.api_url');
        $this->clientId    = config('services.autods.client_id');
        $this->redirectUri = config('services.autods.redirect_uri');
        $this->orderUri    = config('services.autods.order_url');
    }
	
	public function exchangeCodeForToken(string $code, int $userId): void
    {
        $response = Http::asForm()->post(
            "{$this->authUrl}/oauth2/token",
            [
                'client_id'    => $this->clientId,
                'grant_type'   => 'authorization_code',
                'code'         => $code,
                'redirect_uri' => $this->redirectUri,
            ]
        );
		
        if (!$response->successful()) {
            Log::error('AutoDS Token Exchange Failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \Exception('AutoDS token exchange failed');
        }
		
        $this->storeTokens($response->json(), $userId);
		$stores = $this->getStores($userId);
	
		$this->syncUserStores($stores, $userId);
		
    }
	
	public function getStores(int $userId): array
	{  
		$response = Http::withHeaders($this->headers($userId))
			->get("{$this->apiBase}/store/list");
		
		if (!$response->successful()) {
			Log::error('AutoDS Store Fetch Failed', [
				'body' => $response->body(),
			]);
			throw new \Exception('Failed to fetch AutoDS stores');
		}
		return $response->json();
	}
	
	protected function syncUserStores(array $stores, int $userId): void
	{
		foreach ($stores as $item) {

			if (!isset($item['store'])) {
				continue;
			}

			$store = $item['store'];
			
			AutoDSStore::updateOrCreate(
				[
					'user_id'          => $userId,
					'autods_store_id'  => $store['id'],
				],
				[
					'name'      => $store['name'] ?? null,
					'store_url'=> $store['store_url'] ?? null,
					'active'   => !empty($store['active']),
				]
			);
		}
	}
	
	protected function storeTokens(array $tokens, int $userId): void
    {  
        AutoDSToken::updateOrCreate(
            ['user_id' => $userId],
            [
                'id_token'      => $tokens['id_token'],
                'access_token'  => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
                'expires_in'    => $tokens['expires_in'],
                'expires_at'    => Carbon::now()->addSeconds($tokens['expires_in'] - 60),
            ]
        );
    }
	
    protected function getAccessToken(int $userId): string
    {
        $token = AutoDSToken::where('user_id', $userId)->first();

        if (!$token) {
            throw new \Exception('AutoDS account not connected');
        }

        if (now()->lt($token->expires_at)) {
            return $token->id_token;
        }
	
        return $this->refreshToken($token);
    }

    protected function refreshToken(AutoDSToken $token): string
	{
		$response = Http::asForm()->post(
			"{$this->authUrl}/oauth2/token",
			[
				'client_id'     => $this->clientId,
				'grant_type'    => 'refresh_token',
				'refresh_token' => $token->refresh_token,
			]
		);

		if (!$response->successful()) {
			Log::error('AutoDS Refresh Failed', [
				'status' => $response->status(),
				'body'   => $response->body(),
			]);
			throw new \Exception('AutoDS refresh failed');
		}

		$data = $response->json();
		
		Log::info('AutoDS Refresh Success', $data);

		$token->update([
			'id_token'      => $data['id_token'],
			'access_token'  => $data['access_token'] ?? null,
			'expires_in'    => $data['expires_in'],
			'expires_at'    => Carbon::now()->addSeconds($data['expires_in'] - 60),
		]);

		return $data['id_token'];
	}

    protected function headers(int $userId): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->getAccessToken($userId),
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
        ];
    }

    public function getProducts(
        int $userId,
        int $autoDsStoreId,
        int $limit = 20,
        int $offset = 0
    ): array {
        $response = Http::withHeaders($this->headers($userId))
            ->post("{$this->apiBase}/products/{$autoDsStoreId}/list/", [
                'limit'          => $limit,
                'offset'         => $offset,
                'condition'      => 'and',
                'filters'        => [],
                'product_status' => 2, // Published,1 Draft
            ]);
		
        if (!$response->successful()) {
            Log::error('AutoDS Product Fetch Failed', [
                'body' => $response->body(),
            ]);
            throw new \Exception('Failed to fetch AutoDS products');
        }

        return $response->json();
    }
	
	public function importProductsFromAutoDS(
		int $userId,
		int $autoDsStoreId,
		int $categoryId,
		int $storeId
	): int {
		
		#UPDATE AUTO-DS ID
		$autoDsTokenInfo = AutoDSToken::where('user_id',$userId)->first();
		$autoDsTokenInfo->autods_store_id = $autoDsStoreId;
		$autoDsTokenInfo->save();
		
		$response = $this->getProducts(
			$userId,
			$autoDsStoreId,
			1000,
			0
		);
		
		$products = $response['results'] ?? [];

		if (empty($products)) {
			return 0;
		}

		return $this->saveAutoDSProducts(
			$products,
			$categoryId,
			$storeId,
			$userId
		);
	}

	
	private function saveAutoDSProducts(
		array $products,
		int $categoryId,
		int $storeId,
		int $userId
	): int {

		$store = VendorStore::findOrFail($storeId);
		$count = 0;

		foreach ($products as $item) {
            
			//already imported
			/* if (Product::where('auto_ds_product_id', $item['id'])->exists()) {
				continue;
			} */
			
			$product = $this->createAutoDSProduct(
				$item,
				$categoryId,
				$store
			);

			$this->importAutoDSVariants(
				$product,
				$item,
				$store
			);

			$count++;
			// break;
		}

		return $count;
	}
	
	private function createAutoDSProduct(
		array $item,
		int $categoryId,
		VendorStore $store
	): Product {
		
		$mainVariation = $item['variations'][0] ?? [];
		
		$sku  = $item['sku'] ?? 'AUTODS-' . $item['id'];
		$slug = Str::slug($item['title']) . '-' . substr(md5($item['id']), 0, 6);

		$product = Product::updateOrCreate(
			[
				'sku'       => $sku,
				'seller_id' => $store->user_id,
			],
			[
				'specifications'     => $item['item_specifics'] ?? null,
				'auto_ds_main_picture_url' => $item['main_picture_url']['url'] ?? null,
				'autods_store_id'    => $item['autods_store_id'] ?? null,
				'name'               => $item['title'],
				'slug'               => $slug,
				'type'               => 'imported',
				'price'              => isset($mainVariation['price'])
										  ? (float) $mainVariation['price']
										  : 0,
				'quantity'           => isset($mainVariation['quantity'])
										  ? (int) $mainVariation['quantity']
										  : 0,
				'description'        => $item['description'] ?? null,
				'short_description'  => Str::limit(strip_tags($item['description'] ?? ''), 190),
				'meta_title'         => $item['title'],
				'meta_description'   => Str::limit(strip_tags($item['description'] ?? ''), 160),
				'status'             => 1,
			]
		);
		$product->forceFill([
			'auto_ds_product_id' => $item['id'],
		])->save();
		
		// image sirf pehli baar
		if ($product->wasRecentlyCreated && empty($product->image)) {
			$product->update([
				'image' => $this->downloadImage(
					$item['main_picture_url']['url'] ?? null
				),
			]);
		}
		
		$product->categories()->syncWithoutDetaching([$categoryId]);
		$product->stores()->syncWithoutDetaching([$store->id]);
		
		#GALLERY IMAGES
		if (!empty($item['images']) && is_array($item['images'])) {
			#IMG DELETE BEFORE UPDATE RECORD
			$galleryImge = ProductGallery::where('product_id',$product->id)->get();
			foreach ($galleryImge as $oldImageName) {
				if ($oldImageName->image && File::exists(public_path('/uploads/product/gallery/'.$oldImageName->image))) {
					File::delete(public_path('/uploads/product/gallery/'.$oldImageName->image));
				}
			}
			#PREVIOUS DELETE GALLERY IMAGES
			ProductGallery::where('product_id',$product->id)->delete();
			
			foreach ($item['images'] as $imageUrl) {
				ProductGallery::updateOrCreate([
					'product_id' => $product->id,
					'image' => $imageUrl['url']
				]);
			}
		}
		
		return $product;
	}


	
	private function importAutoDSVariants(
		Product $product,
		array $item,
		VendorStore $store
	): void {
       
		if (empty($item['variations'])) return;

		$attribute = Attribute::firstOrCreate([
			'name'      => 'Options',
			'vendor_id' => $store->user_id,
		]);

		$productAttribute = ProductAttribute::firstOrCreate([
			'product_id'   => $product->id,
			'attribute_id' => $attribute->id,
		]);

		foreach ($item['variations'] as $variant) {
			// build value text like: Color:Red | Size:M
			$valueText = collect($variant['attributes'] ?? [])
				->map(fn ($v, $k) => $k . ':' . $v)
				->implode(' | ');

			if (!$valueText) {
				$valueText = 'Default';
			}

			// 2️⃣ Attribute Value
			$attributeValue = AttributeValue::firstOrCreate([
				'attribute_id' => $attribute->id,
				'value'        => $valueText,
				'vendor_id'    => $store->user_id,
			]);
			
			$existingVariant = ProductVariant::where('product_id', $product->id)
				->where('sku', $variant['sku'])->first();
			
			$data = [
				'product_id'            => $product->id,
				'sku'                   => $variant['sku'],
				'product_attribute_id'  => $productAttribute->id,
				'value'                 => (string) $attributeValue->id, // IMPORTANT
				'price'                 => $variant['price'] ?? 0,
				//'price'                 => 0,
				'stock'                 => $variant['quantity'] ?? 0,
				'auto_ds_variant_id'    => (string)$variant['active_buy_item']['item_id_on_site'] ?? null,
				'auto_ds_buy_item_url'  => (string)$variant['active_buy_item']['url'] ?? null,
				'autods_site_id'        => $variant['active_buy_item']['site_id'] ?? null,
				'autods_region'         => $variant['active_buy_item']['region'] ?? null,
				/* 'image'                 => $this->saveVariantImage(
					$variant['main_picture_url']['url'] ?? null,
					$existingVariant->image ?? null
				), */
				'image'                 => $variant['main_picture_url']['url'] ?? null,
			];
			
			if ($existingVariant) {
				$existingVariant->forceFill($data);
				$existingVariant->save();
				
			} else {
				//ProductVariant::create($data);
				$variant = new ProductVariant();
				$variant->forceFill($data);
				$variant->save();
				
			}
			
		}
	}

	private function saveVariantImage(?string $url, ?string $existingImage = null): ?string
	{
		if (!$url) {
			return $existingImage;
		}

		try {
			// delete old image
			if ($existingImage) {
				$oldPath = public_path('uploads/variant_images/' . $existingImage);
				if (file_exists($oldPath)) {
					unlink($oldPath);
				}
			}

			$imageName = time() . '_' . uniqid() . '.jpg';
			$path = public_path('uploads/variant_images/' . $imageName);

			if (!File::exists(dirname($path))) {
				File::makeDirectory(dirname($path), 0755, true);
			}

			$response = Http::get($url);

			if ($response->successful()) {
				File::put($path, $response->body());
				return $imageName;
			}

			return $existingImage;

		} catch (\Exception $e) {
			Log::error('Variant Image Save Failed', [
				'error' => $e->getMessage(),
				'url'   => $url,
			]);
			return $existingImage;
		}
	}

    private function downloadImage(?string $url, string $type = 'main'): ?string
    {
        if (!$url) return null;

        try {
            $folder = $type === 'gallery'
                ? 'uploads/product/gallery/'
                : 'uploads/products/';

            $name = time() . '_' . uniqid() . '.jpg';
            $path = public_path($folder . $name);

            if (!File::exists(dirname($path))) {
                File::makeDirectory(dirname($path), 0755, true);
            }

            $response = Http::get($url);

            if ($response->successful()) {
                File::put($path, $response->body());
                return $name;
            }

            return null;

        } catch (\Exception $e) {
            return null;
        }
    }
	
	#CREATE AUTO-DS ORDER 
	public function createAutoDSOrdersFromLocalOrder(int $orderId)
    {
        try {
            $order = Order::with('orderProduct')->findOrFail($orderId);
			
            $grouped = $order->orderProduct->groupBy('vendor_id');
           
            foreach ($grouped as $vendorId => $items) {

				foreach ($items as $item) {
				
					$product = Product::find($item->product_id);
					if (!$product || !$product->auto_ds_product_id) {
						continue;
					}

					$variant = ProductVariant::where('product_id', $product->id)
						->where('value', $item->variant_ids)
						->first();

					if (!$variant || !$variant->auto_ds_variant_id) {
						continue;
					}
					
					$countryCode = Country::where('name', $order->shipping_country)->value('shortname');
					$countryCode = $countryCode ?? 'US';
					
					$sellSiteOrderId    = 'AUTODS-ONDJ' . now()->format('Ymd') . $order->id . uniqid();
					$supplier 			= (int) $variant->autods_site_id; // 1 or 15
					
					/* $payload = [
						'product_title'       => $product->name,
						'sell_item_id'        => $product->auto_ds_product_id,
						'sell_site_order_id'  => $sellSiteOrderId,
						'sold_date'           => now()->toISOString(),
						'supplier'            => (string) $supplier,
						'site_id'             => (string) $variant->autods_site_id,
						'region'              => (int) ($variant->autods_region ?? 1),
						'buyer_email'    	  => $order->email,
						'phone_number'    	  => $order->phone,
						'buy_item_real_id'    => $variant->auto_ds_variant_id,
						'buy_item_url' 		  => $variant->auto_ds_buy_item_url ?? NULL,
						'quantity_to_buy'     => (int) $item->quantity,
						'suggested_buy_price' => (float) $product->price,
						'first_name'          => $order->shipping_first_name,
						'last_name'           => $order->shipping_last_name,
						'address_1'           => $order->shipping_address_1,
						'address_2'           => $order->shipping_address_2,
						'city'                => Str::before($order->shipping_city, '-'),
						'zip_code'            => $order->shipping_zipcode,
						'country'             => $countryCode,
						'phone'               => $order->phone,
						"carrier"             => NULL
					]; */
					
				    #CREATE VARIENT OPTIONS
					$options = $item->variant_text;
					$options = str_replace('Options:', '', $options);
					$attributes = [];
					foreach (explode('|', $options) as $option) {
						[$key, $value] = array_map('trim', explode(':', $option, 2));
						$attributes[strtolower($key)] = $value;
					}
					
					/* $quantity = (int) $item->quantity;
					$buyPrice = (float) $variant->price;
					$sellUnitPrice = (float) $item->price; */
					
					
					$quantity  = (int) $item->quantity;
					$buyPrice  = (float) $variant->price;   // Amazon buy price
					// Pricing rules
					$profitPercentage = 15;
					$feesPercentage   = 12;
					$fixedProfit      = 5;
					$maxBuyPrice      = 65.00;
					// Calculate sell price PER UNIT
					$profitAmount = ($buyPrice * $profitPercentage) / 100;
					$feesAmount   = ($buyPrice * $feesPercentage) / 100;
					$sellUnitPrice = round(
						$buyPrice + $profitAmount + $feesAmount + $fixedProfit,
						2
					);
					
					$payload = [
						'sell_site_order_id'   => $sellSiteOrderId,
						'sell_site_order_name' => 'External Order ' . $sellSiteOrderId,

						'store_id'   => (int) $product->autods_store_id,
						'store_name' => 'ONDJANGO-BAY',

						'first_name' => $order->shipping_first_name,
						'last_name'  => $order->shipping_last_name,

						'address_1' => $order->shipping_address_1,
						'address_2' => $order->shipping_address_2,
						'city'      => Str::before($order->shipping_city, '-'),
						'state'     => $order->shipping_state ?? null,
						'zip_code'  => $order->shipping_zipcode,
						'country'   => $countryCode,
						'phone_number' => $order->phone,

						'buy_site_id'      => $variant->autods_buy_site_name ?? 'Amazon',
						'quantity_to_buy'  => $quantity,

						'buy_item_real_id' => $variant->auto_ds_variant_id,
						'buy_item_variant' => $variant->auto_ds_variant_id ?? null,
						'buy_item_url'     => $variant->auto_ds_buy_item_url,

						'product_title'     => $product->name,
						'supplier'          => (int) $variant->autods_site_id,
						'region'            => (int) ($variant->autods_region ?? 1),
						'autods_product_id' => $product->auto_ds_product_id,

						// ✅ CORRECT PRICING FIELDS
						'suggested_buy_price'    => round($buyPrice, 2),
						'maximum_buy_item_price' => $maxBuyPrice,

						'profit_percentage' => $profitPercentage,
						'fees_percentage'   => $feesPercentage,

						// ✅ PER UNIT SELL PRICE (IMPORTANT)
						'sell_price' => $sellUnitPrice,

						'buyer_email' => $order->email,
						'buy_item_attributes' => $attributes,

						'prime_only' => true,
						'tags' => ['ondjango', 'external'],

						'listing_fee' => 0,
						'include_shipping_fee' => true,
						'maximum_ship_days' => 7,
						'bce_conversion' => 1,

						'product_picture_url' => $product->auto_ds_main_picture_url,
					];

					//dd($payload);
					
					$autodsProductID = $product->autods_store_id;
					$vendorID        = $product->seller_id;
					
					/* $response = Http::withHeaders(
						$this->headers($vendorID)
					)->post("{$this->apiBase}/orders/$autodsProductID/order/create", $payload); */
					
					$response = Http::withHeaders($this->headers($vendorID))->post("{$this->orderUri}", $payload);
					
					/* dd([
						'status'  => $response->status(),
						'headers' => $response->headers(),
						'json'    => $response->json(),
						'body'    => $response->body(),
					]); */
					
					Log::info('AutoDS Order Item Payload', $payload);
					Log::info('AutoDS Order Item Response', $response->json());
					
					if ($response->successful()) {
						
						$responseBodyRaw  = $response->body();
						$responseBodyJson = null;

						try {
							$responseBodyJson = json_decode($responseBodyRaw, true);
						} catch (\Throwable $e) {
							// ignore JSON parse error
						}

						AutoDSOrder::create([
							'local_order_id'      => $order->id,
							'vendor_id'           => $vendorId,
							'autods_order_number' => $responseBodyJson['sell_site_order_id'] ?? null,
							'autods_orderid'      => $responseBodyJson['id'] ?? null,
							//'logistic_name'       => $responseBodyJson['logistic_name'] ?? null,
							'status'              => $responseBodyJson['status'] ?? NULL,
							'request_payload'     => $payload,
							'response_data'       => $responseBodyJson,
						]);
					}
				
				}
			}

            return [
                'success' => true,
            ];

        } catch (\Exception $e) {
            Log::error('AutoDS Order Failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }
	
	/* public function getAutoDSOrderByIdOnly(int|string $autoDsOrderId): ?array
	{
	
		$localAutoDsOrder = AutoDSOrder::where('autods_orderid', $autoDsOrderId)->first();
		
		if (!$localAutoDsOrder) {
			throw new \Exception('AutoDS order not found in local database');
		}

		$vendorId = $localAutoDsOrder->vendor_id;

		$token = AutoDSToken::where('user_id', $vendorId)->first();
		
		if (!$token) {
			throw new \Exception('AutoDS token not found for vendor');
		}

		$userId = $token->user_id;
		$storeId = $token->autods_store_id;
		
		if (!$storeId) {
			throw new \Exception('AutoDS store id not found');
		}

		$payload = [
			'limit'     => 1,
			'offset'    => 0,
			'condition' => 'and',
			'filters'   => [
				[
					//'name'       => 'order_id',
					//'name'       => 'sell_site_order_name',
					'name'       => 'id',
					'value'      => (string) $autoDsOrderId,
					'op'         => '=',
					'value_type' => 'string',
				],
			],
			'order_by' => [
				'name'      => 'sold_date',
				'direction' => 'desc',
			],
		];
		
		$response = Http::withHeaders(
			$this->headers($userId)
		)->post(
			"{$this->apiBase}/orders/{$storeId}/list/",
			$payload
		);
		
		#DD
		dd([
			'payload'  => $payload,
			'status'   => $response->status(),
			'response' => $response->json(),
		]);
		
		Log::info('AutoDS Order Fetch By ID Only', [
			'payload' => $payload,
			'response' => $response->json(),
		]);

		if (!$response->successful()) {
			throw new \Exception('Failed to fetch AutoDS order');
		}

		return $response->json()['results'][0] ?? null;
	} */
	
	public function getAutoDSOrderByIdOnly(int|string $autoDsOrderId): ?array
	{
		
		$localAutoDsOrder = AutoDSOrder::where('autods_orderid', $autoDsOrderId)->first();

		if (!$localAutoDsOrder) {
			throw new \Exception('AutoDS order not found in local database');
		}

		$vendorId = $localAutoDsOrder->vendor_id;

		$token = AutoDSToken::where('user_id', $vendorId)->first();

		if (!$token) {
			throw new \Exception('AutoDS token not found for vendor');
		}

		$userId  = $token->user_id;
		$storeId = $token->autods_store_id;

		if (!$storeId) {
			throw new \Exception('AutoDS store id not found');
		}

		$url = "{$this->apiBase}/orders/{$storeId}/order/{$autoDsOrderId}/";

		$response = Http::withHeaders(
			$this->headers($userId)
		)->get($url);
		
		#DD
		dd([
			'status'   => $response->status(),
			'response' => $response->json(),
		]);
		
		Log::info('AutoDS Single Order Fetch', [
			'url'      => $url,
			'status'   => $response->status(),
			'response' => $response->json(),
		]);

		if (!$response->successful()) {
			throw new \Exception('Failed to fetch AutoDS order by ID');
		}

		// 5️⃣ Single order object directly return hota hai
		return $response->json();
	}

	
	
}
