<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\DropshipProduct;

class AliExpressService
{
    protected string $apiUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('services.alibaba.api_url');
        $this->apiKey = config('services.alibaba.api_key');
    }

    public function fetchProducts(string $keyword, int $page = 1): array|null
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->get("{$this->apiUrl}/products", [
            'q' => $keyword,
            'page' => $page,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    public function importProducts(string $keyword): bool
    {
        $data = $this->fetchProducts($keyword);

        if (!$data || empty($data['items'])) {
            return false;
        }

        foreach ($data['items'] as $item) {
			DropshipProduct::updateOrCreate(
				['external_id' => $item['id']], 
				[
					'title' => $item['title'] ?? $item['name'], 
					'aliexpress_product_id' => $item['id'],
					'description' => $item['description'] ?? $data['description'] ?? null,
					'price' => $item['price'] ?? $data['price']['original_price'] ?? 0,
					'stock' => 999,
					'image' => $item['image_url'] ?? $data['images'][0] ?? null,
					'supplier_name' => $data['store']['store_name'] ?? 'Unknown',
				]
			);
		}

        return true;
    }
}
