<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use DB;

class MyUSService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected int $affiliateId;
    protected int $merchantId;
    protected string $authToken;
    protected string $retailerName = 'ESCX'; 

    public function __construct()
    {
        $this->baseUrl     = rtrim(config('services.myus.base_url'), '/');
        $this->apiKey      = config('services.myus.api_key');
        $this->affiliateId = (int) config('services.myus.affiliate_id');
        $this->merchantId  = (int) config('services.myus.merchant_id');
        $this->authToken   = config('services.myus.auth_token');
    }

    protected function headers(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->authToken,
            'api_key'       => $this->apiKey,
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
        ];
    }
    
    protected function addOrder(array $payload): array
    {
        $url = "{$this->baseUrl}/Orders/MerchantOrder/{$this->merchantId}/AddOrder";
    
        Log::info('MyUS AddOrder Request', $payload);

        $response = Http::withHeaders($this->headers())
            ->timeout(60)
            ->post($url, $payload);
        
        Log::info('MyUS AddOrder Response', [
            'status' => $response->status(),
            'body' => $response->json(),
        ]);

        if (! $response->successful()) {
            throw new \Exception(
                'MyUS AddOrder failed: ' . $response->body()
            );
        }

        return $response->json();
    }

    public function updateOrder(array $payload): array
    {
        $url = "{$this->baseUrl}/Orders/MerchantOrder/{$this->merchantId}/UpdateOrder";
	
        Log::info('MyUS UpdateOrder Request', $payload);

        $response = Http::withHeaders($this->headers())
            ->timeout(30)
            ->post($url, $payload);
		
        Log::info('MyUS UpdateOrder Response', [
            'status' => $response->status(),
            'body' => $response->json(),
        ]);

        if (!$response->successful()) {
            throw new \Exception(
                'MyUS UpdateOrder failed: ' . $response->body()
            );
        }

        return $response->json();
    }
    
    public function updateTracking(string $masterOrderNumber, string $trackingNumber, string $carrier = 'UPS'): array
    {
        $payload = [
            "MasterOrderNumber" => $masterOrderNumber,
            "AffiliateID" => $this->affiliateId,
            "OrderStatusID" => 5,
            "CarrierMethod" => $carrier,
            "TrackingNumber" => $trackingNumber
        ];
		
        return $this->updateOrder($payload);
    }

    public function cancelOrder(string $masterOrderNumber): array
    {
        $payload = [
            "MasterOrderNumber" => $masterOrderNumber,
            "AffiliateID" => $this->affiliateId,
            "OrderStatusID" => 4
        ];

        return $this->updateOrder($payload);
    }
    
    public function pushMyUSOrder(int $orderId)
    {   
	
		
        $order = Order::with([
            'orderProduct.product',
        ])->findOrFail($orderId);
       
        if ($order->shipping_carrier !== 'MYUS') {
            return;
        }
        
        DB::beginTransaction();

        try {
            $masterOrderNumber = $this->retailerName . '-' . $order->order_number . '-' . $this->getCountryISO($order);
            
            $orderItems = [];
            $totalWeight = 0;
            
            foreach ($order->orderProduct as $item) {
				
                $product = $item->product;
                $color = null;
                $size  = null;

                if (!empty($item->variant_text)) {
                    $variantText = str_replace('Options:', '', $item->variant_text);
                    $variantText = trim($variantText);

                    if (strtolower($variantText) !== 'default') {
                        $normalized = str_replace(["\n", "|"], ',', $variantText);
                        $parts = explode(',', $normalized);

                        foreach ($parts as $part) {
                            $part = trim($part);
                            if (!str_contains($part, ':')) {
                                continue;
                            }

                            [$key, $value] = array_map('trim', explode(':', $part, 2));

                            if ($key !== '' && $value !== '') {
                                if (strtolower($key) === 'color') {
                                    $color = $this->cleanField($value);
                                }
                                if (strtolower($key) === 'size') {
                                    $size = $this->cleanField($value);
                                }
                            }
                        }
                    }
                }

                $description = $this->cleanField($product->name ?? '');

                $itemWeight = $product->weight ?? 0.5;
                $totalWeight += $itemWeight * $item->quantity;

                $orderItems[] = [
                    "OrderNumberItemId" => $masterOrderNumber . '-' . ($product->sku ?? $item->id),
                    "Description"       => $description,
                    "SKU"               => $product->sku ?? null,
                    "UPC"               => $product->upc ?? null,
                    "Color"             => $color,
                    "Size"              => $size,
                    "Quantity"          => (int) $item->quantity,
                    "Value"             => round($item->price, 2), 
                    "UnitOfMeasurement" => "pcs",
                    "ProductImage"      => $product->image 
                                            ? asset('uploads/products/' . $product->image) 
                                            : null,
                    "ProductURL"        => route('product.product_details', $product->slug),
                    "Weight"            => $itemWeight,
                ];
            }
			
            $payload = [
                "ToName" => $this->cleanField($order->shipping_first_name . ' ' . $order->shipping_last_name),
                "CustomerEmail" => $order->email,
                "AffiliateID" => $this->affiliateId,
                "MasterOrderNumber" => $masterOrderNumber,
                "ShipServiceID" => 0,
                "ShippingFrequency" => 0,
                "OrderStatusID" => 1,
                "CustOrderDate" => $order->created_at->format('Y-m-d H:i:s'),
                "Merchant" => $this->retailerName,
                "OrderAddress" => [
                    "FullName" => $this->cleanField($order->shipping_first_name . ' ' . $order->shipping_last_name),
                    "Address1" => $this->cleanField($order->shipping_address_1),
                    "Address2" => $this->cleanField($order->shipping_address_2),
                    "Company" => $this->cleanField($order->shipping_company),
                    "City" => $this->cleanField($order->shipping_city),
                    "State" => $this->cleanField($order->shipping_state),
                    "Postal" => $order->shipping_zipcode,
                    "CountryISO" => $this->getCountryISO($order),
                    "PhoneISO" => $this->extractPhoneCountry($order->phone),
                    "PhoneLocal" => preg_replace('/[^0-9]/', '', $order->phone),
                ],

                "ListOptions" => [
                    "Insurance" => false, 
                    "Urgent" => false,
                    "Pallet" => false,
                    "DiscardShoeBoxes" => false,
                    "FragileStickers" => false,
                    "ExtraPadding" => false
                ],

                "OrderItems" => $orderItems
            ];
			
            if (!empty($order->tax_id)) {
                $payload['OrderAddress']['TaxID'] = $order->tax_id;
            }
            
            $response = $this->addOrder($payload);
            
            $order->master_order_number = $masterOrderNumber;
            $order->external_order_id = $response['OrderId'] ?? null;
            $order->shipping_response = json_encode($response);
            $order->shipping_status = 'confirmed';
            $order->save();
			
			#TRACKING NUMBER UPDATE
			$this->updateTracking($masterOrderNumber,$order->tracking_number,'MYUS');
			
			
            foreach ($response['OrderItems'] ?? [] as $item) {
				
                DB::table('order_myus_items')->insert([
                    'order_id' => $order->id,
                    'order_product_id' => $item->id ?? null,
                    'myus_order_item_id' => $item['Id'],
                    'myus_order_number_item_id' => $item['OrderNumberItemId'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            DB::commit();

            return $response;

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('MyUS Order Push Failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            
            $order->shipping_response = json_encode(['error' => $e->getMessage()]);
            $order->shipping_status = 'failed';
            $order->save();
            
            throw $e;
        }
    }
	
    protected function cleanField(?string $value): ?string
    {
        if ($value === null) return null;
        
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        
        return substr($value, 0, 35);
    }

    protected function getCountryISO(Order $order): string
    {
        $country = $order->shipping_country ?? $order->billing_country ?? 'US';
        return $this->countryNameToISO($country);
    }

    protected function extractPhoneCountry(string $phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        if (str_starts_with($phone, '+1')) return 'US';
        if (str_starts_with($phone, '+44')) return 'GB';
        if (str_starts_with($phone, '+91')) return 'IN';
        if (str_starts_with($phone, '+61')) return 'AU';
        if (str_starts_with($phone, '+86')) return 'CN';
        if (str_starts_with($phone, '+81')) return 'JP';
        if (str_starts_with($phone, '+49')) return 'DE';
        if (str_starts_with($phone, '+33')) return 'FR';
        
        return 'US';
    }

    protected function countryNameToISO(string $country): string
    {
        $countries = [
            'United States' => 'US',
            'USA' => 'US',
            'Canada' => 'CA',
            'United Kingdom' => 'GB',
            'UK' => 'GB',
            'Australia' => 'AU',
            'India' => 'IN',
            'Germany' => 'DE',
            'France' => 'FR',
            'Japan' => 'JP',
            'China' => 'CN',
        ];

        return $countries[$country] ?? $country;
    }
}