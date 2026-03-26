<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductComparison;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductComparisonController extends Controller
{
    protected $maxComparisonCount = 4;

    // Get comparison count
    private function getComparisonCount(): int
    {
        return ProductComparison::where('user_id', Auth::id())->count();
    }

    private function getComparisonProducts()
    {
        return ProductComparison::with(['product'])
            ->where('user_id', Auth::id())
            ->get()
            ->map(function ($comparison) {
                return $this->formatProductResponse($comparison->product);
            });
    }

    private function formatProductResponse(Product $product)
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'price' => $product->price,
            'compare_price' => $product->compare_price,
            'in_stock' => $product->quantity,
            'sku' => $product->sku,
            'brand' => $product->brand,
            'specifications' => $product->specifications ?? [],
            'image' => $product->image ?? [],
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
        ];
    }

    /**
     * Get comparison products
     */
    public function index(): JsonResponse
    {
        try {
            $products = $this->getComparisonProducts();
            $count = $this->getComparisonCount();

            // Get all unique specifications from compared products
            $allSpecifications = [];
            foreach ($products as $product) {
                if (!empty($product['specifications']) && is_array($product['specifications'])) {
                    $allSpecifications = array_merge($allSpecifications, array_keys($product['specifications']));
                }
            }
            $allSpecifications = array_unique($allSpecifications);

            return response()->json([
                'success' => true,
                'data' => [
                    'products' => $products,
                    'count' => $count,
                    'max_count' => $this->maxComparisonCount,
                    'all_specifications' => $allSpecifications
                ],
                'message' => 'Comparison products retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve comparison products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add product to comparison
     */
    public function store(Product $product): JsonResponse
    {
        try {
            $comparisonCount = $this->getComparisonCount();

            // Check maximum limit
            if ($comparisonCount >= $this->maxComparisonCount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maximum comparison limit reached (4 products)',
                    'data' => ['count' => $comparisonCount]
                ], 422);
            }

            // Check if product already in comparison
            $existingComparison = ProductComparison::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->exists();

            if ($existingComparison) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product already in comparison',
                    'data' => ['count' => $comparisonCount]
                ], 422);
            }

            // Add to comparison
            ProductComparison::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product added to comparison',
                'data' => [
                    'product' => $this->formatProductResponse($product),
                    'count' => $this->getComparisonCount()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add product to comparison',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove product from comparison
     */
    public function destroy(Product $product): JsonResponse
    {
        try {
            ProductComparison::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product removed from comparison',
                'data' => [
                    'removed_product_id' => $product->id,
                    'count' => $this->getComparisonCount()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove product from comparison',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear all comparisons
     */
    public function clear(): JsonResponse
    {
        try {
            $deletedCount = ProductComparison::where('user_id', Auth::id())->delete();

            return response()->json([
                'success' => true,
                'message' => 'Comparison cleared successfully',
                'data' => [
                    'deleted_count' => $deletedCount
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear comparison',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comparison count
     */
    public function count(): JsonResponse
    {
        try {
            $count = $this->getComparisonCount();

            return response()->json([
                'success' => true,
                'data' => [
                    'count' => $count,
                    'max_count' => $this->maxComparisonCount
                ],
                'message' => 'Comparison count retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get comparison count',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if product is in comparison
     */
    public function check(Product $product): JsonResponse
    {
        try {
            $inComparison = ProductComparison::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->exists();

            return response()->json([
                'success' => true,
                'data' => [
                    'in_comparison' => $inComparison,
                    'product_id' => $product->id,
                    'count' => $this->getComparisonCount()
                ],
                'message' => 'Product comparison status retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check product comparison status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}