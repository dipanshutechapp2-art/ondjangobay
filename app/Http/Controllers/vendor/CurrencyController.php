<?php

namespace App\Http\Controllers\vendor;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;
use App\Models\VendorStore;
use App\Models\Currency;
use Auth;

class CurrencyController extends Controller
{
    
	public function index(Request $request){
		
	    
		if ($request->ajax()) {
			
			$currency = Currency::orderBy('id','DESC')->get();

			return DataTables::of($currency)
				->addColumn('created_at', function ($currency) {
					return $currency->created_at->format('M d, Y');
				})
				->addColumn('action', function($currency) {
						
						$editUrl   = url('/vendor/currency/edit', $currency->id);
						$deleteUrl = url('/vendor/currency/delete', $currency->id);
						$token     = csrf_token();
				   
						return '
							<div class="btn-group">
								<button type="button" class="btn btn-success">Action</button>
								<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<div class="dropdown-menu" role="menu">
									<a class="dropdown-item" href="'.$editUrl.'" data-id="'.$currency->id.'" data-name="'.$currency->name.'">Edit</a>
									<a class="dropdown-item delete-category" onclick="return validateDelete(this);" href="'.$deleteUrl.'" data-id="'.$currency->id.'">Delete</a>
									<form id="delete-form-'.$currency->id.'" action="'.$deleteUrl.'" method="POST" style="display: none;">
										<input type="hidden" name="_token" value="'.$token.'">
										<input type="hidden" name="_method" value="DELETE">
									</form>
								</div>
							</div>
						';
					})
				->rawColumns(['action'])
				->make(true);
		}
		
		return view('vendor.currency.list');
    }
	
	public function add_currency(){
       
		return view('vendor.currency.add');
		
    }
	
	public function add_currency_action(Request $request){ 
     
		$request->validate([
            'code'       => 'required|unique:currencies,code',
            'symbol'     => 'required',
            'rate'       => 'required|numeric',
            'is_default' => 'nullable|boolean'
        ]);
		
		if ($request->is_default) {
            Currency::where('is_default', true)->update(['is_default' => false]);
        }

		$currency = new Currency;
		$currency->code          = $request->code;
		$currency->symbol        = $request->symbol;
		$currency->rate          = $request->rate;
		$currency->is_default    = $request->is_default;
		$currency->save();
		
		if (empty($request->is_default)) {
			$totalCounts = Currency::where('is_default', true)->count();
            if($totalCounts==0){
				$currencyInfo = Currency::first();
				$currencyInfo->is_default = true;
				$currencyInfo->save();
			}
        }
		
		
		return redirect('/vendor/currency')->with('success', 'You have successfully added!');
    }
	
	public function edit_currency($currency_id){
		
		$currencyInfo = Currency::where('id',$currency_id)->first();
		return view('vendor.currency.edit',compact('currencyInfo'));
		
    }
	
	public function edit_currency_action(Request $request){
		 
		$request->validate([
            'code'       => 'required|unique:currencies,code,'.$request->currency_id,
            'symbol'     => 'required',
            'rate'       => 'required|numeric',
            'is_default' => 'nullable|boolean'
        ]);
		
		if ($request->is_default) {
            Currency::where('is_default', true)->update(['is_default' => false]);
        }
		
		$currency = Currency::where('id',$request->currency_id)->first();
		
		$currency->code          = $request->code;
		$currency->symbol        = $request->symbol;
		$currency->rate          = $request->rate;
		$currency->is_default    = $request->is_default;
		$currency->save();
		
		if (empty($request->is_default)) {
			$totalCounts = Currency::where('is_default', true)->count();
            if($totalCounts==0){
				$currencyInfo = Currency::first();
				$currencyInfo->is_default = true;
				$currencyInfo->save();
			}
        }
		
		
        return redirect('/vendor/currency')->with('success', 'You have successfully updated!');
    }
	
	public function delete_currency($currency_id){
	
		Currency::where('id',$currency_id)->delete();
		return redirect('/vendor/currency')->with('success', 'You have successfully deleted!');
    }
	 
	public function setCurrency($id)
	{
		session(['currency_id' => $id]);
		return back();
	}

}
