<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VendorCommissions;
use Illuminate\Http\Request;

class VendorCommissionController extends Controller
{
    
    public function indexBlade()
    {
        $vendors = VendorCommissions::orderBy('id', 'desc')->paginate(10);

        return view('admin.vendor_commissions.index', compact('vendors'));
    }

    public function create()
    {
        $vendorsList = User::where('role','vendor')->get();
	
		return view('admin.vendor_commissions.form',compact('vendorsList'));
    }

    public function storeBlade(Request $request)
    {
        $validated = $this->validateVendor($request);
        VendorCommissions::create($validated);
        return redirect()->route('admin.vendor_commissions.index')->with('success', 'Vendor created successfully.');
    }

    public function edit($id)
    {
        $vendor  = VendorCommissions::findOrFail($id);
		$vendorsList = User::where('role','vendor')->get();
        return view('admin.vendor_commissions.form', compact('vendor','vendorsList'));
    }

    public function updateBlade(Request $request, $id)
    {
        $vendor = VendorCommissions::findOrFail($id);
        $validated = $this->validateVendor($request, true,$id);
        $vendor->update($validated);
        return redirect()->route('admin.vendor_commissions.index')->with('success', 'Vendor updated successfully.');
    }

    public function destroyBlade($id)
    {
        VendorCommissions::findOrFail($id)->delete();
        return redirect()->route('admin.vendor_commissions.index')->with('success', 'Vendor deleted successfully.');
    }

    private function validateVendor(Request $request, $isUpdate = false, $id = null)
	{
		$rules = [
			'name'             => $isUpdate ? 'sometimes|string|max:255' : 'required|string|max:255',
			'category_code'    => 'required|in:internal,external',
			'commission_type'  => 'required|in:global,custom',
			'commission_value' => 'required|numeric|min:0|max:100',
			'bank_account'     => 'nullable|string|max:255',
		];

		if (!$isUpdate) {
			$rules['vendor_id'] = 'required|exists:users,id|unique:vendor_commissions,vendor_id';
		} else {
			$rules['vendor_id'] = 'required|exists:users,id|unique:vendor_commissions,vendor_id,' . $id;
		}

		return $request->validate($rules);
	}

}
