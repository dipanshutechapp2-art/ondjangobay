<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
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
		
		/* dd([
			'status'  => $response->status(),
			'headers' => $response->headers(),
			'json'    => $response->json(),
			'body'    => $response->body(),
		]); */
		
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

        if (! $response->successful()) {
            throw new \Exception(
                'MyUS UpdateOrder failed: ' . $response->body()
            );
        }

        return $response->json();
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

			$orderItems = [];
			
			foreach ($order->orderProduct as $item) {

				$product = $item->product;

				$attributes = [];
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

								$attributes[strtolower($key)] = $value;

								if (strtolower($key) === 'color') {
									$color = $value;
								}

								if (strtolower($key) === 'size') {
									$size = $value;
								}
							}
						}
					}
				}

				$description = $product->name ?? '';

				if (!empty($attributes)) {
					$description .= ' (' . collect($attributes)
						->map(fn($v, $k) => ucfirst($k) . ': ' . $v)
						->implode(', ') . ')';
				}

				$orderItems[] = [
					"OrderNumberItemId" => $order->order_number . '-' . $item->id,
					"Description"       => $description,
					"SKU"               => $product->sku ?? null,
					"UPC"               => null,
					"Color"             => $color,
					"Size"              => $size,
					"Quantity"          => (int) $item->quantity,
					"Value"             => round($item->price),
					"ProductImage"      => $product->image 
											? asset('uploads/products/' . $product->image) 
											: null,
					"ProductURL"        => route('product.product_details', $product->slug),
				];
			}

			$payload = [
				"ToName" => 'ESCX Retailer',
				"CustomerEmail" => $order->email,
				"AffiliateID" => (int)config('services.myus.affiliate_id'),
				"MasterOrderNumber" => $order->order_number,
				"ShipServiceID" => 0,
				"ShippingFrequency" => 0,
				"OrderStatusID" => 1,
				"CustOrderDate" => now()->format('Y-m-d H:i:s'),
				"Merchant" => "ESCX",
				"OrderAddress" => [
					"FullName" => $order->billing_first_name.' '.$order->billing_last_name,
					"Address1" => $order->billing_address_1,
					"Address2" => $order->billing_address_2,
					"Company" => null,
					"City" => $order->billing_city,
					"State" => $order->billing_state,
					"Postal" => $order->billing_zipcode,
					"CountryISO" => 'IN',
					"PhoneISO" => '91',
					//"PhoneISO" => 'IN',
					"PhoneLocal" => preg_replace('/[^0-9]/', '', $order->phone),
				],

				"ListOptions" => [
					"Insurance" => true
				],

				"OrderItems" => $orderItems
			];
			
			$response = $this->addOrder($payload);
			//dd($response);
			
			$order->external_order_id = $response['OrderId'] ?? null;
			$order->shipping_response = $response ?? null;
			$order->shipping_status   = 'confirmed';
			$order->save();
			
			DB::commit();

		} catch (\Throwable $e) {

			DB::rollBack();

			Log::error('MyUS Order Push Failed', [
				'order_id' => $order->id,
				'error' => $e->getMessage()
			]);
			
			$order->shipping_response = $response ?? null;
			$order->shipping_status   = 'failed';
			$order->save();
			
			throw $e;
		}
	}
	
}
