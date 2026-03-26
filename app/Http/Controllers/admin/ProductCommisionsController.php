<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCommissions;
use App\Models\Product;
use App\Models\VendorCommissions;
use Illuminate\Http\Request;

class ProductCommisionsController extends Controller
{
    public function getVendorProducts($vendor_id)
    {
        $products = Product::where('seller_id', $vendor_id)->get();
        return response()->json($products);
    }

    public function indexBlade()
    {
        $products = ProductCommissions::with('vendor')->orderBy('id', 'desc')->paginate(10);
        return view('admin.product_commissions.index', compact('products'));
    }

    public function create()
    {
        $vendors = VendorCommissions::all();
        $product = null;
        return view('admin.product_commissions.form', compact('vendors', 'product'));
    }

    public function storeBlade(Request $request)
    {
        $validated = $this->validateProduct($request);
		
        $exists = ProductCommissions::where('vendor_id', $validated['vendor_id'])
            ->where('product_id', $validated['product_id'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['duplicate' => 'This product is already assigned to the selected vendor.']);
        }

        ProductCommissions::create($validated);

        return redirect()->route('admin.product_commissions.index')
            ->with('success', 'Product added successfully.');
    }

    public function edit($id)
    {
        $product = ProductCommissions::findOrFail($id);
        $vendors = VendorCommissions::all();
        return view('admin.product_commissions.form', compact('product', 'vendors'));
    }

    public function updateBlade(Request $request, $id)
    {
        $product = ProductCommissions::findOrFail($id);
        $validated = $this->validateProduct($request, true);

        $exists = ProductCommissions::where('vendor_id', $validated['vendor_id'])
            ->where('product_id', $validated['product_id'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['duplicate' => 'This product is already assigned to the selected vendor.']);
        }

        $product->update($validated);

        return redirect()->route('admin.product_commissions.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroyBlade($id)
    {
        ProductCommissions::findOrFail($id)->delete();
        return redirect()->route('admin.product_commissions.index')
            ->with('success', 'Product deleted successfully.');
    }

    private function validateProduct(Request $request, $isUpdate = false)
    {
        return $request->validate([
            'vendor_id'          => 'required|exists:vendor_commissions,vendor_id',
            'product_id'         => 'required|exists:products,id',
            'name'               => $isUpdate ? 'sometimes|string|max:255' : 'required|string|max:255',
            'commission_type'    => 'required|in:global,custom',
            'commission_value'   => 'nullable|numeric|min:0|max:100',
            'origin'             => 'nullable|string|max:255',
            'shipping_condition' => 'nullable|string|max:255',
        ]);
    }
}
