<?php
namespace App\Exports;

use App\Models\Product;
use App\Models\AttributeValue;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        if(!empty(auth()->user()->role) && auth()->user()->role=='admin') {
             $products = Product::with([
                'productAttributes.attribute',
                'productAttributes.variants',
                'stores',
                'categories'
            ])->get();
        
        }else{
            $products = Product::with([
                'productAttributes.attribute',
                'productAttributes.variants',
                'stores',
                'categories'
            ])->where('seller_id', auth()->id())->get();
        }
        $data = [];

        foreach ($products as $product) {
            $storeNames    = $product->stores->pluck('store_name')->implode(', ');
            $categoryNames = $product->categories->pluck('name')->implode(', ');

            $hasVariants = false;

            foreach ($product->productAttributes as $productAttribute) {
                foreach ($productAttribute->variants as $variant) {
                    $hasVariants = true;

                    $attributeName = $productAttribute->attribute->name ?? '';
                    $attributeValue = AttributeValue::find($variant->value)->value ?? '';

                    $data[] = [
                        'Product ID'     => $product->id,
                        'Product Name'   => $product->name,
                        'Product Slug'   => $product->slug,
                        'Product Sku'    => $product->sku,
                        'Variant Sku'    => $variant->sku,
                        'Product Price'  => $product->price,
                        'Variant Price'  => $variant->price,
                        'Product Stock'  => $product->quantity,
                        'Variant Stock'  => $variant->stock,
                        'Type'           => 'Variant',
                        'Attributes'     => "$attributeName: $attributeValue",
                        'Stores'         => $storeNames,
                        'Categories'     => $categoryNames,
                        'Variant Image'  => $variant->image ? asset('uploads/variant_images/' . $variant->image) : '',
                        'Product Image'  => $product->image ? asset('uploads/products/' . $product->image) : '',
                        'Description'    => $product->description,
                    ];
                }
            }

            if (!$hasVariants) {
                $data[] = [
                    'Product ID'     => $product->id,
                    'Product Name'   => $product->name,
                    'Product Slug'   => $product->slug,
                    'Product Sku'    => $product->sku,
                    'Variant Sku'    => "",
                    'Product Price'  => $product->price,
                    'Variant Price'  => "",
                    'Product Stock'  => $product->quantity,
                    'Variant Stock'  => "",
                    'Type'           => 'Single',
                    'Attributes'     => '',
                    'Stores'         => $storeNames,
                    'Categories'     => $categoryNames,
                    'Variant Image'  => '',
                    'Product Image'  => $product->image ? asset('uploads/products/' . $product->image) : '',
                    'Description'    => $product->description,
                ];
            }
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Product ID',
            'Product Name',
            'Product Slug',
            'Product Sku',
            'Variant Sku',
            'Product Price',
            'Variant Price',
            'Product Stock',
            'Variant Stock',
            'Type',
            'Attributes',
            'Stores',
            'Categories',
            'Variant Image',
            'Product Image',
            'Description',
        ];
    }
}

