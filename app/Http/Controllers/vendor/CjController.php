<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CjDropshippingService;
use Illuminate\Support\Facades\Log;
use App\Models\Category;

class CjController extends Controller
{
    protected $cj;

    public function __construct(CjDropshippingService $cj)
    {
        $this->cj = $cj;
    }

    public function getToken()
    {
        try {
            $token = $this->cj->getAccessToken();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate access token',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'access_token' => $token,
            ]);

        } catch (\Exception $e) {
            Log::error('CJ Token Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while generating the token',
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'store_id'    => 'required|exists:vendor_stores,id',
        ]);

        try {
            //$categoryName = Category::find($request->category_id)->name;

            //$products = $this->cj->getProducts($categoryName, 1, 10);
            $products = $this->cj->getProducts(null, 1, 10);
		
            if (isset($products['success']) && $products['success'] === false) {
                return back()->with('error', 'Failed to fetch products: ' . $products['error']);
            }
			
			$productList  = $products['data']['list'] ?? [];

			$importedCount = $this->cj->importProductsFromCJ($productList, $request->category_id, $request->store_id);
			
			return back()->with('success', "$importedCount products imported successfully.");
			
        } catch (\Exception $e) {
            Log::error('CJ Import Error: ' . $e->getMessage());
            return back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }
}
