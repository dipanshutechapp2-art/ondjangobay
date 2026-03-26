<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use XMLReader;
use App\Models\Product;
use App\Models\ProductGallery;
use App\Models\VendorStore;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Exception;
use Illuminate\Support\Facades\Cache;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductAttribute;
use App\Models\ProductVariant;
use App\Models\CjOrder;

class CjDropshippingService 
{
    protected $baseUrl = 'https://developers.cjdropshipping.com/api2.0/v1/';
    protected $email;
    protected $apiKey;
    protected $accessToken;

    public function __construct()
    {
        $this->email  = config('services.cj.email');
        $this->apiKey = config('services.cj.api_key');
    }
	
	public function getAccessToken()
    {  
        return Cache::remember('cj_access_token', now()->addMinutes(10), function () {
            try {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->post($this->baseUrl . 'authentication/getAccessToken', [
                    'email'  => $this->email,
                    'apiKey' => $this->apiKey,
                ]);
			
                if ($response->successful()) {
                    $data = $response->json();
                    $token = $data['data']['accessToken'] ?? null;

                    if (!$token) {
                        throw new Exception('CJ returned empty access token');
                    }

                    $this->accessToken = $token;
                    return $token;
                }

                throw new Exception('CJ API Auth failed: ' . $response->body());

            } catch (Exception $e) {
                \Log::error('CJ AccessToken Error: ' . $e->getMessage());
                return null;
            }
        });
    }
	
	public function getProducts($keyword = '', $page = 1, $size = 10)
    {
        $cacheKey = 'cj_products_' . md5($keyword . $page . $size);

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            if (!$this->accessToken) {
                $this->accessToken = $this->getAccessToken();
            }

            if (!$this->accessToken) {
                return [
                    'success' => false,
                    'error' => 'Failed to get access token from CJ',
                ];
            }

			$response = Http::withHeaders([
				'CJ-Access-Token' => $this->accessToken,
			])->get($this->baseUrl . 'product/list', [
				'keyword' => $keyword,
				'pageNum' => $page,
				'pageSize'=> $size,
			]);
		
            if ($response->successful()) {
                $data = $response->json();
                Cache::put($cacheKey, $data, now()->addMinutes(5));
                return $data;
            }

            throw new Exception('CJ Product API failed: ' . $response->body());

        } catch (Exception $e) {
            \Log::error('CJ Product Fetch Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
	
	public function importProductsFromCJ(array $productList, int $categoryId, int $storeId)
	{
		$storeInfo = VendorStore::find($storeId);
		$count = 0;

		if (!$this->accessToken) {
			$this->accessToken = $this->getAccessToken();
		}
		
		foreach ($productList as $item) {

			$response = Http::withHeaders([
				'CJ-Access-Token' => $this->accessToken,
			])->get($this->baseUrl . 'product/query', [
				'pid' => $item['pid'],
			]);

			if ($response->successful()) {
				$data = $response->json()['data'] ?? [];
				
				$galleryImages = [];
				if (!empty($data['productImage'])) {
					$images = json_decode($data['productImage'], true);
					if (is_array($images)) {
						$galleryImages = $images;
					} else {
						$galleryImages = [$data['productImage']];
					}
				}

				$specifications = [];
				if (!empty($data['productType'])) {
					$specifications['Type'] = $data['productType'];
				}
				if (!empty($data['productWeight'])) {
					$specifications['Weight'] = $data['productWeight'];
				}
				if (!empty($data['saleStatus'])) {
					$specifications['Sale Status'] = $data['saleStatus'];
				}
				
				$productData = [
					'specifications'   => $specifications,
					'cj_pid'           => $data['pid'],
					'sku'              => $data['productSku'] ?? $data['pid'],
					'name'             => $data['productNameEn'] ?? $data['productName'],
					'meta_title'       => $data['productNameEn'] ?? $data['productName'],
					'meta_keyword'     => $data['productNameEn'] ?? $data['productName'],
					'meta_description' => $data['productNameEn'] ?? $data['productName'],
					'short_description'=> $data['description'] ?? '',
					'description'      => $data['description'] ?? '',
					'price'            => (float)($data['sellPrice'] ?? 0),
					'quantity'         => 1000,
					'image'            => $this->downloadImage($galleryImages[0] ?? null),
					'gallery_images'   => $galleryImages,
					'category_ids'     => [$categoryId],
					'store_ids'        => [$storeInfo->id],
					'seller_id'        => $storeInfo->user_id,
				];

				$product = $this->saveImportedProduct($productData);
				
				#ADD VARIENT
				if (!empty($data['variants'])) {

					$attribute = Attribute::firstOrCreate([
						'name' => 'Options',
						'vendor_id' => $storeInfo->user_id,
					]);

					$productAttribute = ProductAttribute::firstOrCreate([
						'product_id'   => $product->id,
						'attribute_id' => $attribute->id,
					]);

					foreach ($data['variants'] as $variant) {
						$variantKey = trim($variant['variantKey'] ?? '');

						if (!$variantKey) continue;

						$attributeValue = AttributeValue::firstOrCreate([
							'attribute_id' => $attribute->id,
							'value'        => $variantKey,
							'vendor_id'    => $storeInfo->user_id,
						]);

						$variantImage = null;
						if (!empty($variant['variantImage'])) {
							try {
								
								$imageUrl = $variant['variantImage'];
								$extension = pathinfo($imageUrl, PATHINFO_EXTENSION) ?: 'jpg';
								$imageName = time() . '_' . uniqid() . '.' . $extension;
								$savePath = public_path('uploads/variant_images/' . $imageName);

								if (!File::exists(public_path('uploads/variant_images'))) {
									File::makeDirectory(public_path('uploads/variant_images'), 0755, true);
								}

								/* file_put_contents($savePath, file_get_contents($imageUrl));
								$variantImage = $imageName; */
								
								$response = Http::get($imageUrl);
								File::put($savePath, $response->body());
								$variantImage = $imageName;
								
								
							} catch (\Exception $e) {
								
								\Log::error('Variant image download failed for SKU: ' . ($variant['variantSku'] ?? 'N/A') . ' | ' . $e->getMessage());
							}
						}

						ProductVariant::updateOrCreate(
							[
								'product_id' => $product->id,
								'sku'        => $variant['variantSku'],
							],
							[
								'product_attribute_id' => $productAttribute->id,
								'value'                => $attributeValue->id,
								'price'                => $variant['variantSellPrice'],
								'stock'                => $variant['inventoryNum'] ?? 100,
								'image'                => $variantImage,
								'cj_vid'               => $variant['vid'],
							]
						);
					}
				}

				
				$count++;
			}
		}

		return $count;
	}

	
	public function saveImportedProduct(array $data): Product
	{
		
		$product = Product::updateOrCreate(
			['sku' => $data['sku']],
			[
				'cj_pid'             => $data['cj_pid'] ?? null,
				'specifications'     => $data['specifications'] ?? null,
				'seller_id'          => $data['seller_id'] ?? null, 
				'name'               => $data['name'] ?? '',
				'slug'               => Str::slug($data['name'] ?? ''),
				'meta_title'         => $data['meta_title'] ?? '',
				'meta_keyword'       => $data['meta_keyword'] ?? '',
				'meta_description'   => $data['meta_description'] ?? '',
				'price'              => $data['price'] ?? 0,
				'quantity'           => $data['quantity'] ?? 0,
				'short_description'  => $data['short_description'] ?? '',
				'description'        => $data['description'] ?? '',
				'type'               => $data['type'] ?? 'simple',
				'brand_id'           => $data['brand_id'] ?? null,
				'image'              => $data['image'] ?? null,
			]
		);


		if (!empty($data['category_ids'])) {
			$product->categories()->sync($data['category_ids']);
		}

		if (!empty($data['store_ids'])) {
			$product->stores()->sync($data['store_ids']);
		}


		/* if (!empty($data['attributes']) && is_array($data['attributes'])) {
			$this->saveAttributesAndVariants($product, $data['attributes']);
		} */


		if (!empty($data['gallery_images']) && is_array($data['gallery_images'])) {
			
			#IMG DELETE BEFORE UPDATE RECORD
			$galleryImge = ProductGallery::where('product_id',$product->id)->get();
			foreach ($galleryImge as $oldImageName) {
				if ($oldImageName->image && File::exists(public_path('/uploads/product/gallery/'.$oldImageName->image))) {
					File::delete(public_path('/uploads/product/gallery/'.$oldImageName->image));
				}
			}
			#PREVIOUS DELETE GALLERY IMAGES
			ProductGallery::where('product_id',$product->id)->delete();
			
			foreach ($data['gallery_images'] as $imageUrl) {

				$imageName = time() . '_' . uniqid() . '.jpg';
				$imagePath = public_path('uploads/product/gallery/' . $imageName);
				//file_put_contents($imagePath, file_get_contents($imageUrl));
				$response = Http::get($imageUrl);
				if ($response->successful()) {
				   File::put($imagePath, $response->body());
				}
                
				ProductGallery::updateOrCreate([
					'product_id' => $product->id,
					'image' => $imageName
				]);
			}
		}

		return $product;
	}
	
	private function downloadImage(string $url): ?string
	{
		if (!$url) return null;

		try {
			$imageName = time() . '_' . uniqid() . '.' . pathinfo($url, PATHINFO_EXTENSION);
			$imagePath = public_path('uploads/products/' . $imageName);
			//file_put_contents($imagePath, file_get_contents($url));
			$response = Http::get($url);
			if ($response->successful()) {
			   File::put($imagePath, $response->body());
			}
			$variantImage = $imageName;
			
			return $imageName;
		} catch (\Exception $e) {
			return null;
		}
	}
	
	private function saveAttributesAndVariants(Product $product, array $attributes) {
		
        foreach ($product->attributes as $oldAttribute) {
            $oldAttribute->variants()->delete();
            $oldAttribute->delete();
        }

        foreach ($attributes as $attributeData) {
            $productAttribute = ProductAttribute::create([
                'product_id'   => $product->id,
                'attribute_id' => $attributeData['attribute_id'],
            ]);

            foreach ($attributeData['variants'] as $variant) {
				
                $imageName = $variant['existing_image'] ?? null;
                if (isset($variant['image']) && $variant['image'] instanceof \Illuminate\Http\UploadedFile) {
                    if (!empty($variant['existing_image'])) {
                        $oldPath = public_path('uploads/variant_images/' . $variant['existing_image']);
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }
                    $imageName = time() . '_' . uniqid() . '.' . $variant['image']->extension();
                    $variant['image']->move(public_path('uploads/variant_images/'), $imageName);
                }

                ProductVariant::create([
                    'product_id'             => $product->id,
                    'product_attribute_id'   => $productAttribute->id,
                    'value'                  => $variant['value'],
                    'price'                  => $variant['price'],
                    'sku'                    => $variant['sku'],
                    'stock'                  => $variant['stock'],
                    'image'                  => $imageName,
                ]);
            }
        }
    }
	
	#CREATE CJ ORDERS	
	public function createCjOrdersFromLocalOrder(int $orderId)
	{
		try {
			
			$order = \App\Models\Order::with('orderProduct.product.userInfo', 'orderTotal')
				->where('id', $orderId)
				->first();

			if (!$order) {
				throw new \Exception("Order not found.");
			}

			$productsByVendor = $order->orderProduct->groupBy('vendor_id');
			$createdOrders = [];

			if (!$this->accessToken) {
				$this->accessToken = $this->getAccessToken();
			}

			foreach ($productsByVendor as $vendorId => $products) {
				$cjProducts = [];

				foreach ($products as $productItem) {
					$product = \App\Models\Product::find($productItem->product_id);

					if (!$product || !$product->cj_pid) {
						\Log::warning("Product {$productItem->id} missing CJ PID, skipping.");
						continue;
					}

					$variantInfo = \App\Models\ProductVariant::where('product_id', $productItem->product_id)
						->where('value', $productItem->variant_ids)
						->first();

					$pid = is_numeric($product->cj_pid) ? (string)$product->cj_pid : $product->cj_pid;
					$vid = $variantInfo && $variantInfo->cj_vid
						? (is_numeric($variantInfo->cj_vid) ? (string)$variantInfo->cj_vid : $variantInfo->cj_vid)
						: null;

					$cjProducts[] = [
						'vid'             => $vid,
						'quantity'        => (int)$productItem->quantity,
						'storeLineItemId' => 'item-' . $productItem->id,
					];
				}

				if (empty($cjProducts)) {
					continue;
				}

				$payload = [
					"orderNumber"          => $order->order_number . '-' . $vendorId,
					"shippingZip"          => $order->shipping_zipcode ?? '',
					"shippingCountry"      => $order->shipping_country ?? 'India',
					"shippingCountryCode"  => strtoupper($order->shipping_country_code ?? 'IN'),
					"shippingProvince"     => $order->shipping_state ?? '',
					"shippingCity"         => $order->shipping_city ?? '',
					"shippingCounty"       => "",
					"shippingPhone"        => $order->phone ?? '',
					"shippingCustomerName" => trim(($order->shipping_first_name ?? '') . ' ' . ($order->shipping_last_name ?? '')),
					"shippingAddress"      => $order->shipping_address_1 ?? '',
					"shippingAddress2"     => $order->shipping_address_2 ?? '',
					"taxId" 			   => "",
					"remark"               => "Order placed by ondjangobay.",
					"email"                => $order->email ?? "",
					"consigneeID"          => "",
					"shopAmount"           => "",
					"logisticName"         => "PostNL",
					"fromCountryCode"      => "CN",
					"houseNumber"          => "",
					"iossType"             => "",
					//"platform"           => "woocommerce",
					"iossNumber"           => "",
					"products"             => $cjProducts,
				];

				$response = Http::withHeaders([
					'CJ-Access-Token' => $this->accessToken,
					'Content-Type'    => 'application/json',
				])->post($this->baseUrl . 'shopping/order/createOrderV3', $payload);

				$responseData = $response->json();
				
				\Log::info('CJ Order Create Payload', ['payload' => $payload]);
				\Log::info('CJ Order Create Response', ['response' => $responseData]);
					
				if ($responseData['success'] ?? false) {
					$cjOrder = CjOrder::create([
						'local_order_id'   => $order->id,
						'vendor_id'        => $vendorId,
						//'logistic_name'  => null,
						'cj_order_number'  => $responseData['data']['orderNumber'] ?? null,
						'cj_orderid'  	   => $responseData['data']['orderId'] ?? null,
						'status'           => $responseData['data']['orderStatus'] ?? 'pending',
						'request_payload'  => json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
						'response_data'    => json_encode($responseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
					]);

					$createdOrders[] = $cjOrder;
				} else {
					\Log::error('CJ Order creation failed', [
						'vendor_id' => $vendorId,
						'response'  => $responseData
					]);
				}
			}

			return [
				'success'         => true,
				'orders_created'  => count($createdOrders),
				'details'         => $createdOrders,
			];
			
		} catch (\Exception $e) {
			\Log::error('CJ Order Creation Failed: ' . $e->getMessage());
			return [
				'success'  => false,
				'error'    => $e->getMessage(),
			];
		}
	}




	
}
