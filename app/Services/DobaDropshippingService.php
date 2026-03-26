<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\ProductGallery;
use App\Models\VendorStore;
use Exception;

class DobaDropshippingService
{
    protected string $baseUrl;
    protected string $appKey;
    protected string $privateKey;
    
    public function __construct()
    {
        $this->appKey = config('services.doba.app_key');
        $this->privateKey = File::get(storage_path('app/keys/doba_private.pem'));
        $this->baseUrl = config('services.doba.base_url', 'https://openapi.doba.com/api/v2/');
		
    }
    
    protected function generateSignature(array $params): string
    {
        ksort($params);
        $queryString = urldecode(http_build_query($params));
        openssl_sign($queryString, $signature, $this->privateKey, OPENSSL_ALGO_SHA256);
        return base64_encode($signature);
    }
    
    protected function getHeaders(): array
    {
        $timestamp = round(microtime(true) * 1000);
        $signParams = [
            'appKey'    => $this->appKey,
            'signType'  => 'rsa2',
            'timestamp' => $timestamp,
        ];
        $sign = $this->generateSignature($signParams);
        
        return [
            'appKey'       => $this->appKey,
            'signType'     => 'rsa2',
            'timestamp'    => (string)$timestamp,
            'sign'         => $sign,
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }
    
    protected function get(string $endpoint, array $params = [])
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                             ->get($this->baseUrl . $endpoint, $params);
            
            if ($response->failed()) {
                Log::error("Doba GET failed", [
                    'endpoint' => $endpoint,
                    'status'   => $response->status(),
                    'body'     => $response->body()
                ]);
            }
            
            return $response->json();
        } catch (Exception $e) {
            Log::error("Doba GET Error: {$e->getMessage()}");
            return ['error' => $e->getMessage()];
        }
    }
    
    protected function post(string $endpoint, array $body = [])
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                             ->post($this->baseUrl . $endpoint, $body);
            
            if ($response->failed()) {
                Log::error("Doba POST failed", [
                    'endpoint' => $endpoint,
                    'status'   => $response->status(),
                    'body'     => $response->body()
                ]);
            }
            
            return $response->json();
        } catch (Exception $e) {
            Log::error("Doba POST Error: {$e->getMessage()}");
            return ['error' => $e->getMessage()];
        }
    }
    
    /**
     * Retrieve products from Doba.
     */
    public function getProducts(string $keyword = '', int $page = 1, int $limit = 50)
    {
        $cacheKey = 'doba_products_' . md5($keyword . '_' . $page . '_' . $limit);
        
        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($keyword, $page, $limit) {
            $params = [
                'keyword' => $keyword ?: null,
                'page'    => $page,
                'limit'   => $limit,
            ];
            // possibly the endpoint path is "catalog/products" or "products/search"
            return $this->get('catalog/products', $params);
        });
    }
    
    /**
     * Import products to local system.
     */
    public function importProductsFromDoba(array $productList, int $categoryId, int $storeId)
    {
        $storeInfo = VendorStore::findOrFail($storeId);
        $count = 0;
        
        foreach ($productList as $item) {
            $productData = [
                'doba_id'          => $item['id'] ?? null,
                'sku'              => $item['sku'] ?? null,
                'name'             => $item['title'] ?? ($item['name'] ?? ''),
                'meta_title'       => $item['title'] ?? ($item['name'] ?? ''),
                'meta_keyword'     => $item['title'] ?? ($item['name'] ?? ''),
                'meta_description' => $item['description'] ?? '',
                'price'            => $item['price'] ?? 0,
                'quantity'         => 1000,
                'image'            => $this->downloadImage($item['image'] ?? null),
                'category_ids'     => [$categoryId],
                'store_ids'        => [$storeInfo->id],
                'seller_id'        => $storeInfo->user_id,
            ];
            
            $this->saveImportedProduct($productData);
            $count++;
        }
        
        return $count;
    }
    
    protected function saveImportedProduct(array $data)
    {
        $product = Product::updateOrCreate(
            ['sku' => $data['sku']],
            [
                'doba_id'        => $data['doba_id'],
                'seller_id'      => $data['seller_id'],
                'name'           => $data['name'],
                'slug'           => Str::slug($data['name']),
                'meta_title'     => $data['meta_title'],
                'meta_keyword'   => $data['meta_keyword'],
                'meta_description'=> $data['meta_description'],
                'price'          => $data['price'],
                'quantity'       => $data['quantity'],
                'image'          => $data['image'],
            ]
        );
        
        if (! empty($data['category_ids'])) {
            $product->categories()->sync($data['category_ids']);
        }
        if (! empty($data['store_ids'])) {
            $product->stores()->sync($data['store_ids']);
        }
        
        return $product;
    }
    
    protected function downloadImage(?string $url): ?string
    {
        if (! $url) return null;
        try {
            $imageName = time() . '_' . uniqid() . '.' . pathinfo($url, PATHINFO_EXTENSION);
            $savePath  = public_path('uploads/products/' . $imageName);
            $response  = Http::get($url);
            if ($response->successful()) {
                File::put($savePath, $response->body());
                return $imageName;
            }
        } catch (Exception $e) {
            Log::error("Doba image download failed: {$e->getMessage()}");
        }
        return null;
    }
    
    /**
     * Place order via Doba
     */
    public function createOrderFromLocal(int $orderId)
    {
        try {
            $order = \App\Models\Order::with('orderProduct.product')->findOrFail($orderId);
            
            $items = [];
            foreach ($order->orderProduct as $item) {
                $product = $item->product;
                if (! $product || ! $product->doba_id) {
                    continue;
                }
                $items[] = [
                    'sku'      => $product->sku,
                    'quantity' => $item->quantity,
                ];
            }
            
            if (empty($items)) {
                throw new Exception('No valid Doba items found in order #'.$orderId);
            }
            
            $payload = [
                'customer_id' => $order->user_id,
                'order'       => [
                    'ship_to'         => [
                        'name'    => trim(($order->shipping_first_name ?? '') . ' ' . ($order->shipping_last_name ?? '')),
                        'address1'=> $order->shipping_address_1 ?? '',
                        'city'    => $order->shipping_city ?? '',
                        'state'   => $order->shipping_state ?? '',
                        'zip'     => $order->shipping_zipcode ?? '',
                        'country' => $order->shipping_country ?? '',
                        'phone'   => $order->phone ?? '',
                    ],
                    'items'          => $items,
                    'shipping_method' => 'Standard'
                ]
            ];
            
            return $this->post('order/place_order', $payload);
        } catch (Exception $e) {
            Log::error("Doba Order Creation Error: {$e->getMessage()}");
            return ['error' => $e->getMessage()];
        }
    }
    
    /**
     * Get order status via Doba.
     */
    public function getOrderStatus(string $orderId)
    {
        return $this->get('order/status', ['order_id' => $orderId]);
    }
}
