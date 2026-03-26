<?php
namespace App\Imports;

use App\Models\Product;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductAttribute;
use App\Models\ProductVariant;
use App\Models\VendorStore;
use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class ProductsImport implements ToCollection
{
    protected $productCache = [];

    public function collection(Collection $rows)
    {
        $rows->shift();

        foreach ($rows as $row) {
            $productId     = $row[0];
            $productName   = $row[1];
            $productSlug   = $row[2] ?? Str::slug($productName);
            $productSku    = $row[3];
            $variantSku    = $row[4];
            $productPrice  = $row[5];
            $variantPrice  = $row[6];
            $productStock  = $row[7] ?? 100;
            $variantStock  = $row[8];
            $type          = strtolower(trim($row[9]));
            $attributeStr  = $row[10]; 
            $storeNames    = explode(',', $row[11] ?? '');
            $categoryNames = explode(',', $row[12] ?? '');
            $variantImage  = basename($row[13] ?? '');
            $productImage  = basename($row[14] ?? '');
            $description   = $row[15] ?? '';
			
			$catSlug = (array)Str::slug($categoryNames[0]);
			$categoryIds = Category::whereIn('slug', array_map('trim', $catSlug))->pluck('id')->toArray();
			
			#PRODUCT IMAGE
			/* $productImageUrl = $row[14] ?? '';
			if ($productImageUrl && filter_var($productImageUrl, FILTER_VALIDATE_URL)) {
				$imageContents = @file_get_contents($productImageUrl);
				if ($imageContents) {
					$filename = basename(parse_url($productImageUrl, PHP_URL_PATH));
					file_put_contents(public_path('uploads/products/' . $filename), $imageContents);
					$productImage = $filename;
				}
			} */
			
			#VARIENT IMAGE
			/* $varientImageUrl = $row[13] ?? '';
			if ($varientImageUrl && filter_var($varientImageUrl, FILTER_VALIDATE_URL)) {
				$imageContents = @file_get_contents($varientImageUrl);
				if ($imageContents) {
					$filename = basename(parse_url($varientImageUrl, PHP_URL_PATH));
					file_put_contents(public_path('uploads/variant_images/' . $filename), $imageContents);
					$variantImage = $filename;
				}
			} */
			
			
			# PRODUCT IMAGE
			$productImageUrl = $row[14] ?? '';

			if ($productImageUrl && filter_var($productImageUrl, FILTER_VALIDATE_URL)) {
				try {
					$response = Http::get($productImageUrl);

					if ($response->successful()) {
						$filename = basename(parse_url($productImageUrl, PHP_URL_PATH));
						$savePath = public_path('uploads/products/' . $filename);

						File::put($savePath, $response->body());
						$productImage = $filename;
					}
				} catch (\Exception $e) {
					Log::error('Product image download failed: ' . $e->getMessage());
				}
			}

			# VARIANT IMAGE
			$variantImageUrl = $row[13] ?? '';

			if ($variantImageUrl && filter_var($variantImageUrl, FILTER_VALIDATE_URL)) {
				try {
					$response = Http::get($variantImageUrl);

					if ($response->successful()) {
						$filename = basename(parse_url($variantImageUrl, PHP_URL_PATH));
						$savePath = public_path('uploads/variant_images/' . $filename);

						File::put($savePath, $response->body());
						$variantImage = $filename;
					}
				} catch (\Exception $e) {
					Log::error('Variant image download failed: ' . $e->getMessage());
				}
			}
			
        
            if (empty($productSku)) {
                continue;
            }

            if (isset($this->productCache[$productSku])) {
                $product = $this->productCache[$productSku];
            } else {
                $product = Product::firstOrNew(['sku' => $productSku]);

                $product->name        = $productName;
                $product->slug        = $productSlug;
                $product->price       = $productPrice;
                $product->quantity    = $productStock;
                $product->description = $description;
                $product->image       = $productImage;
                $product->seller_id   = Auth::id();
                $product->save();

                $storeIds = VendorStore::whereIn('store_name', array_map('trim', $storeNames))->pluck('id')->toArray();
                $product->stores()->syncWithoutDetaching($storeIds);

                //$categoryIds = Category::whereIn('name', array_map('trim', $categoryNames))->pluck('id')->toArray();
                $product->categories()->syncWithoutDetaching($categoryIds);

                $this->productCache[$productSku] = $product;
            }

            if ($type === 'variant' && !empty($variantSku)) {

                if (strpos($attributeStr, ':') !== false) {
                    [$attrName, $attrValueText] = array_map('trim', explode(':', $attributeStr));

                    $attribute = Attribute::firstOrCreate(['name' => $attrName]);
                    $attrValue = AttributeValue::firstOrCreate([
                        'attribute_id' => $attribute->id,
                        'value'        => $attrValueText,
                    ]);

                    $productAttr = ProductAttribute::firstOrCreate([
                        'product_id'   => $product->id,
                        'attribute_id' => $attribute->id,
                    ]);

                    ProductVariant::updateOrCreate(
                        [
                            'product_attribute_id' => $productAttr->id,
                            'sku'                  => $variantSku,
                        ],
                        [
                            'price' => $variantPrice,
                            'stock' => $variantStock,
                            'value' => $attrValue->id,
                            'image' => $variantImage,
                        ]
                    );
                }
            }
        }
    }
}

