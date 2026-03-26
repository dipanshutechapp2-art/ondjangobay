<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductComparison;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class ProductComparisonController extends Controller
{
    protected $maxComparisonCount = 4;

    // Get current user/session identifier
    private function getComparisonIdentifier()
    {
        return Auth::id() ?? Session::getId();
    }

    // Check if user is logged in
    private function isUserComparison()
    {
        return Auth::check();
    }

    // Get comparison count
    private function getComparisonCount(): int
    {
        $identifier = $this->getComparisonIdentifier();

        return ProductComparison::when($this->isUserComparison(), 
            function ($query) use ($identifier) {
                $query->where('user_id', $identifier);
            }, 
            function ($query) use ($identifier) {
                $query->where('session_id', $identifier);
            }
        )->count();
    }

    // Get comparison products
    private function getComparisonProducts()
    {
        $identifier = $this->getComparisonIdentifier();

        return ProductComparison::with(['product'])
            ->when($this->isUserComparison(), 
                function ($query) use ($identifier) {
                    $query->where('user_id', $identifier);
                }, 
                function ($query) use ($identifier) {
                    $query->where('session_id', $identifier);
                }
            )
            ->get()
            ->pluck('product');
    }

    // Main controller methods
    public function index(): View
    {
        $products = $this->getComparisonProducts();
        $count = $this->getComparisonCount();

        // Get all unique specifications from compared products
        $allSpecifications = [];
        foreach ($products as $product) {
            if ($product->specifications && is_array($product->specifications)) {
                $allSpecifications = array_merge($allSpecifications, array_keys($product->specifications));
            }
        }
        $allSpecifications = array_unique($allSpecifications);

        return view('pages.compare', compact('products', 'count', 'allSpecifications'));
    }

    public function store(Product $product): JsonResponse
    {
        $comparisonCount = $this->getComparisonCount();

        // Check maximum limit
        if ($comparisonCount >= $this->maxComparisonCount) {
            return response()->json([
                'success' => false,
                'message' => 'Maximum comparison limit reached (4 products)',
                'count' => $comparisonCount
            ], 422);
        }

        $identifier = $this->getComparisonIdentifier();
		
        // Check if product already in comparison
        $existingComparison = ProductComparison::when($this->isUserComparison(), 
            function ($query) use ($identifier) {
                $query->where('user_id', $identifier);
            }, 
            function ($query) use ($identifier) {
                $query->where('session_id', $identifier);
            }
        )->where('product_id', $product->id)->exists();

        if ($existingComparison) {
            return response()->json([
                'success' => false,
                'message' => 'Product already in comparison',
                'count' => $comparisonCount
            ], 422);
        }

        // Add to comparison
        ProductComparison::create([
            $this->isUserComparison() ? 'user_id' : 'session_id' => $identifier,
            'product_id' => $product->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product added to comparison',
            'count' => $this->getComparisonCount()
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $identifier = $this->getComparisonIdentifier();

        ProductComparison::where('product_id', $product->id)
            ->where(function ($query) use ($identifier) {
                if ($this->isUserComparison()) {
                    $query->where('user_id', $identifier);
                } else {
                    $query->where('session_id', $identifier);
                }
            })
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from comparison',
            'count' => $this->getComparisonCount()
        ]);
    }

    public function clear(): JsonResponse
    {
        $identifier = $this->getComparisonIdentifier();

        ProductComparison::when($this->isUserComparison(), 
            function ($query) use ($identifier) {
                $query->where('user_id', $identifier);
            }, 
            function ($query) use ($identifier) {
                $query->where('session_id', $identifier);
            }
        )->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comparison cleared successfully'
        ]);
    }

    public function count(): JsonResponse
    {
        $count = $this->getComparisonCount();
        return response()->json(['count' => $count]);
    }

    public function check(Product $product): JsonResponse
    {
        $identifier = $this->getComparisonIdentifier();

        $inComparison = ProductComparison::when($this->isUserComparison(), 
            function ($query) use ($identifier) {
                $query->where('user_id', $identifier);
            }, 
            function ($query) use ($identifier) {
                $query->where('session_id', $identifier);
            }
        )->where('product_id', $product->id)->exists();

        return response()->json([
            'in_comparison' => $inComparison,
            'count' => $this->getComparisonCount()
        ]);
    }
}