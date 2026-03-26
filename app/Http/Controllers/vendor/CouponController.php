<?php

namespace App\Http\Controllers\vendor;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\User;
use App\Models\VendorStore;
use App\Models\Coupon;
use Auth;

class CouponController extends Controller
{
    public function index(Request $request) {

		if ($request->ajax()) {
			
			$coupons = Coupon::with('vendor')->where('vendor_id',auth()->id())->get();
			
			return DataTables::of($coupons)
				->addColumn('created_at', function($coupons) {
					return date('Y-m-d H:i',strtotime($coupons->created_at));
				})
				->addColumn('action', function($coupons) {
					
					$editUrl   = route('vendor.coupon.edit', $coupons->id);
					$deleteUrl = route('vendor.coupon.destroy', $coupons->id);
					$token = csrf_token();
			   
					return '
						<div class="btn-group">
							<button type="button" class="btn btn-success">Action</button>
							<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
								<span class="sr-only">Toggle Dropdown</span>
							</button>
							<div class="dropdown-menu" role="menu">
								<a class="dropdown-item" href="'.$editUrl.'" data-id="'.$coupons->id.'" data-name="'.$coupons->name.'">Edit</a>
								<a class="dropdown-item delete-category" href="#" data-id="'.$coupons->id.'">Delete</a>
								<form id="delete-form-'.$coupons->id.'" action="'.$deleteUrl.'" method="POST" style="display: none;">
									<input type="hidden" name="_token" value="'.$token.'">
									<input type="hidden" name="_method" value="DELETE">
								</form>
							</div>
						</div>
					';
				})
				->rawColumns(['action','vendor_name'])
				->make(true);
		}
		return view('vendor.coupon.list');
    }

    public function create() {
		
		$vendors    = User::where('role','vendor')->get();
		
		return view('vendor.coupon.create',compact('vendors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vendor_id'          => 'required|numeric|exists:users,id',
            'code'               => [
                'required',
                'string',
                Rule::unique('coupons')->where(function ($query) use ($request) {
                    return $query->where('vendor_id', $request->vendor_id);
                }),
            ],
            'type'               => 'required|in:fixed,percentage',
            'value'              => 'required|numeric|min:0',
            'min_order_amount'   => 'nullable|numeric|min:0',
            'max_uses'           => 'nullable|integer|min:1',
            'max_uses_per_user'  => 'nullable|integer|min:1',
            'starts_at'          => 'nullable|date',
            'expires_at'         => 'nullable|date|after_or_equal:starts_at',
            'is_active'          => 'required|boolean',
        ]);

        $coupon = new Coupon();
        $coupon->vendor_id         = $data['vendor_id'];
        $coupon->code              = $data['code'];
        $coupon->type              = $data['type'];
        $coupon->value             = $data['value'];
        $coupon->min_order_amount  = $data['min_order_amount'] ?? null;
        $coupon->max_uses          = $data['max_uses'] ?? null;
        $coupon->max_uses_per_user = $data['max_uses_per_user'] ?? null;
        $coupon->starts_at         = $data['starts_at'] ?? null;
        $coupon->expires_at        = $data['expires_at'] ?? null;
        $coupon->is_active         = $data['is_active'];
        $coupon->save();

        return redirect()
            ->route('vendor.coupon.show')
            ->with('success', 'Coupon created successfully.');
    }

    public function edit($id) {
		
		$vendors    = User::where('role','vendor')->get();
        $coupon     = Coupon::findOrFail($id);

        return view('vendor.coupon.edit', compact('coupon','vendors'));
    }


	public function update(Request $request, $id)
	{
		$coupon = Coupon::findOrFail($id);

		$data = $request->validate([
			'vendor_id'          => 'required|numeric|exists:users,id',
			'code'               => [
				'required',
				'string',
				Rule::unique('coupons')->where(function ($query) use ($request) {
					return $query->where('vendor_id', $request->vendor_id);
				})->ignore($coupon->id),
			],
			'type'               => 'required|in:fixed,percentage',
			'value'              => 'required|numeric|min:0',
			'min_order_amount'   => 'nullable|numeric|min:0',
			'max_uses'           => 'nullable|integer|min:1',
			'max_uses_per_user'  => 'nullable|integer|min:1',
			'starts_at'          => 'nullable|date',
			'expires_at'         => 'nullable|date|after_or_equal:starts_at',
			'is_active'          => 'required|boolean',
		]);

		$coupon->vendor_id         = $data['vendor_id'];
		$coupon->code              = $data['code'];
		$coupon->type              = $data['type'];
		$coupon->value             = $data['value'];
		$coupon->min_order_amount  = $data['min_order_amount'] ?? null;
		$coupon->max_uses          = $data['max_uses'] ?? null;
		$coupon->max_uses_per_user = $data['max_uses_per_user'] ?? null;
		$coupon->starts_at         = $data['starts_at'] ?? null;
		$coupon->expires_at        = $data['expires_at'] ?? null;
		$coupon->is_active         = $data['is_active'];

		$coupon->save();

		return redirect()
			->route('vendor.coupon.show')
			->with('success', 'Coupon updated successfully.');
	}


    public function destroy($id)
	{
		$coupon = Coupon::findOrFail($id);
		$coupon->delete();

		return redirect()
			->route('vendor.coupon.show')
			->with('success', 'Coupon deleted successfully.');
	}
}
