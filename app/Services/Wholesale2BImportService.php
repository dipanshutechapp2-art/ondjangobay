<?php

namespace App\Services;

use XMLReader;
use App\Models\Product;
use App\Models\ProductGallery;
use App\Models\VendorStore;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class Wholesale2BImportService
{
    public function import(int $categoryId, int $store_id): int
    {  
        $feedUrl = config('services.wholesale2b.feed_url');
        $tmpFile = storage_path('app/feed.xml');

        file_put_contents($tmpFile, file_get_contents($feedUrl));

        $reader = new XMLReader();
        $reader->open($tmpFile);

        $count = 0;
		
		$onlyData       = array();
		$specifications = array();
		
		$storeInfo = VendorStore::where('id',$store_id)->first();
		
        while ($reader->read()) {
            
			if ($reader->nodeType === XMLReader::ELEMENT && $reader->localName === 'item') {
                
				$node = simplexml_load_string($reader->readOuterXML());
				
				$nodeData = (object)$this->xml_to_array($node);
				
				
				#SPECIFICATIONS
				if(!empty($nodeData->brand)) {
					$specifications[] = [
						'key'   => 'Brand',
						'value' => $nodeData->brand ?? "",
					];
				}
				if(!empty($nodeData->upc)) {
					$specifications[] = [
						'key'   => 'UPC',
						'value' => $nodeData->upc ?? "",
					];
				}
				if(!empty($nodeData->weight)) {
					$specifications[] = [
						'key'   => 'Weight',
						'value' => $nodeData->weight ?? "",
					];
				}
				if(!empty($nodeData->condition)) {
					$specifications[] = [
						'key'   => 'Condition',
						'value' => $nodeData->condition ?? "",
					];
				}
				
				
				$specs = [];
				
				if(!empty($specifications)) {
					foreach ($specifications as $spec) {
						if (!empty($spec['key']) && !empty($spec['value'])) {
							$specs[$spec['key']] = $spec['value'];
						}
					}
				}
			
				#IMG DELETE BEFORE UPDATE RECORD
				$productImg = Product::where('sku',$nodeData->sku)->first();
				if(!empty($productImg->image)) {
					if ($productImg->image && File::exists(public_path('/uploads/products/'.$productImg->image))) {
						File::delete(public_path('/uploads/products/'.$productImg->image));
					}
				}
		   
				$data = [
					'specifications'      => $specs,
					'sku'                 => $nodeData->sku,
					'name'                => $nodeData->title,
					'meta_title'          => $nodeData->title,
					'meta_keyword'        => $nodeData->title,
					'meta_description'    => $nodeData->title,
					'short_description'   => $this->truncateWords($nodeData->description,200),
					'description'         => $nodeData->description,
					'price'               => (float)$nodeData->retail_price,
					'quantity'            => (int)$nodeData->stock,
					'image'         	  => $this->downloadImage((string) $nodeData->original_image_url),
					'gallery_images' => array_filter([
						$nodeData->extra_img_1,
						$nodeData->extra_img_2,
						$nodeData->extra_img_3,
						$nodeData->extra_img_4,
						$nodeData->extra_img_5,
						$nodeData->extra_img_6,
						$nodeData->extra_img_7,
						$nodeData->extra_img_8,
						$nodeData->extra_img_9,
						$nodeData->extra_img_10,
					]),
					'category_ids'    => [$categoryId],
					'store_ids'       => [$storeInfo->id],
					'seller_id'       => $storeInfo->user_id,
					'attributes'      => [],
				];
				
				$this->saveImportedProduct($data);
				
				//$onlyData[] = $data;
                $count++;
            }
        }
	
        $reader->close();
        unlink($tmpFile);

        return $count;
    }
	
	public function truncateWords(string $text, int $limit = 200): string
	{
		$words = preg_split('/\s+/', trim($text));

		if (count($words) <= $limit) {
			return $text;
		}

		return implode(' ', array_slice($words, 0, $limit)) . '...';
	}
	
	public static function xml_to_array(\SimpleXMLElement $xml): array
	{
		$array = [];

		foreach ($xml as $key => $value) {
			$array[$key] = $value->count() > 0
				? self::xml_to_array($value)
				: (string) $value;
		}

		return $array;
	}
	
	public function saveImportedProduct(array $data): Product
	{
		
		$product = Product::updateOrCreate(
			['sku' => $data['sku']],
			[
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
				file_put_contents($imagePath, file_get_contents($imageUrl));
                
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
			file_put_contents($imagePath, file_get_contents($url));
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
                'product_id' => $product->id,
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

}
