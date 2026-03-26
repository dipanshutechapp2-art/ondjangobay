<?php

namespace App\Http\Controllers\admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request; 
use App\Models\ShippingPrice;
use App\Models\ShippingOption;
use App\Models\Country;
use DB;

class ShippingPriceController  extends Controller
{

    public function index()
    {
        $prices = ShippingPrice::with('country','option')->orderBy('sort_order','asc')->get();
        return view('admin.shipping_prices.index', compact('prices'));
    }

    public function create()
    { 
        return view('admin.shipping_prices.create', [
            'countries' => Country::all(),
            'options' => ShippingOption::all()
        ]);
    }

    public function store(Request $request)
    { 
        
		$request->validate([
			'shipping_option_id' => 'required|exists:shipping_options,id',
			'price' => 'required|numeric',
			'eta_min' => 'required|integer',
			'eta_max' => 'required|integer',
		]);
		
		ShippingPrice::updateOrCreate(
            [
                'country_id' => $request->country_id,
                'shipping_option_id' => $request->shipping_option_id,
            ],
            [
                'price' => $request->price,
                'eta_min' => $request->eta_min,
                'eta_max' => $request->eta_max,
                'is_active' => true
            ]
        );

        return redirect()->route('shipping-prices.index')
            ->with('success', 'Shipping price saved');
    }
	
	public function toggleStatus(Request $request, $id)
	{
		$price = ShippingPrice::findOrFail($id);

		$price->is_active = !$price->is_active;
		$price->save();

		return response()->json([
			'success' => true,
			'status' => $price->is_active
		]);
	}
	
	public function edit($id)
	{
		$price = ShippingPrice::findOrFail($id);

		return view('admin.shipping_prices.edit', [
			'price'     => $price,
			'countries' => Country::all(),
			'options'   => ShippingOption::all()
		]);
	}
	
	public function update(Request $request, $id)
	{
		$request->validate([
			'shipping_option_id' => 'required|exists:shipping_options,id',
			'price' => 'required|numeric',
			'eta_min' => 'required|integer',
			'eta_max' => 'required|integer',
			'sort_order' => 'nullable|integer',
		]);

		$price = ShippingPrice::findOrFail($id);
	
		DB::transaction(function () use ($request, $price) {

			if ($request->has('is_default')) {
				ShippingPrice::where('is_default', 1)
					->where('id', '!=', $price->id)
					->update(['is_default' => 0]);
			}

			$price->update([
				'country_id' => $request->country_id,
				'shipping_option_id' => $request->shipping_option_id,
				'price' => $request->price,
				'eta_min' => $request->eta_min,
				'eta_max' => $request->eta_max,
				'sort_order' => $request->sort_order ?? 0,
				'is_default' => $request->has('is_default'),
			]);
		});

		return redirect()
			->route('shipping-prices.index')
			->with('success', 'Shipping price updated successfully');
	}

	public function setDefault($id)
	{
		DB::transaction(function () use ($id) {
			// sabko un-default
			ShippingPrice::where('is_default', 1)->update(['is_default' => 0]);

			// selected ko default
			ShippingPrice::where('id', $id)->update(['is_default' => 1]);
		});

		return response()->json(['success' => true]);
	}
	
	public function updateOrder(Request $request, $id)
	{
		$request->validate([
			'sort_order' => 'required|integer'
		]);

		ShippingPrice::where('id', $id)
			->update(['sort_order' => $request->sort_order]);

		return response()->json(['success' => true]);
	}


}