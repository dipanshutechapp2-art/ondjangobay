<?php

namespace App\Http\Controllers\vendor;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;
use App\Models\VendorStore;
use App\Models\User;
use App\Models\CategoryStore;
use App\Models\Category;
use Auth;

class StoreCategoryController extends Controller
{
    public function add() {
		
        $categories = Category::whereNull('parent_id')->get();
        $stores   = VendorStore::where('user_id', auth()->user()->id)->get();
		
        return view('vendor.category_store.create', compact('categories','stores'));
    }
		
	public function show(Request $request) {
		
		if ($request->ajax()) {
            $stores = CategoryStore::with(['store', 'category'])->where('vendor_id',auth()->user()->id)->get();
			return DataTables::of($stores)
				 ->addColumn('store', function ($store) {
					return $store->store->store_name;
				})
				->addColumn('category', function ($store) {
					$ids   = explode(',', $store->category_id);
					$names = \App\Models\Category::whereIn('id', $ids)->pluck('name')->toArray();
					$badges = collect($names)->map(function ($name) {
						return '<span class="badge badge-primary mr-1">' . e($name) . '</span>';
					})->implode(' ');

					return $badges;
				})
				->addColumn('created_at', function ($store) {
					return $store->created_at->format('M d, Y');
				})
				->addColumn('updated_at', function ($store) {
					return $store->updated_at->format('M d, Y');
				})
		   
				->addColumn('action', function($store) {
						
						$editUrl   = route('vendor.categorystore.edit', $store->id);
						$deleteUrl = route('vendor.categorystore.destroy', $store->id);
						$token = csrf_token();
				   
						return '
							<div class="btn-group">
								<button type="button" class="btn btn-success">Action</button>
								<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<div class="dropdown-menu" role="menu">
									<a class="dropdown-item" href="'.$editUrl.'" data-id="'.$store->slug.'" data-name="'.$store->name.'">Edit</a>
									<a class="dropdown-item delete-category" onclick="return validateDelete(this)" href="'.$deleteUrl.'" data-id="'.$store->id.'">Delete</a>
									<form id="delete-form-'.$store->id.'" action="'.$deleteUrl.'" method="POST" style="display: none;">
										<input type="hidden" name="_token" value="'.$token.'">
										<input type="hidden" name="_method" value="DELETE">
									</form>
								</div>
							</div>
						';
					})
				->rawColumns(['action','category'])
				->make(true);
		}

		return view('vendor.category_store.show');
	}

	public function create(Request $request) {

	    $request->validate([
			'store_id'       => 'required|integer|exists:vendor_stores,id',
			'category_id'    => 'required|array',
			'category_id.*'  => 'integer|exists:categories,id',
		]);
		
		if(CategoryStore::where('store_id',$request->store_id)->where('vendor_id',auth()->user()->id)->count()>0){
			return redirect()->back()->with('error', 'Category store is already exists!.');
		}
		
		$categoryIds = implode(',', $request->category_id);

        $categoryStore                   = new CategoryStore;
		$categoryStore->vendor_id        = auth()->user()->id;
		$categoryStore->store_id         = $request->store_id;
		$categoryStore->category_id      = $categoryIds;
        $categoryStore->save();

        return redirect()->route('vendor.categorystore.show')->with('success', 'Category Store created successfully.');
    }
	
    public function edit($id) {
	
        $categories         = Category::whereNull('parent_id')->get();
        $stores             = VendorStore::where('user_id', auth()->user()->id)->get();
        $categoryStore      = CategoryStore::where('id', $id)->first();
		
        return view('vendor.category_store.edit', compact('categoryStore','categories','stores'));
    }

    public function update(Request $request,$id) {
		
        $request->validate([
			'store_id'       => 'required|integer|exists:vendor_stores,id',
			'category_id'    => 'required|array',
			'category_id.*'  => 'integer|exists:categories,id',
		]);

        $store       = CategoryStore::where('id',$id)->firstOrFail();
        
		$categoryIds = implode(',', $request->category_id);
		
		$store->vendor_id          = auth()->user()->id;
		$store->store_id           = $request->store_id;
		$store->category_id        = $categoryIds;
        $store->save();

        return redirect()->route('vendor.categorystore.show')->with('success', 'Store updated successfully.');
    }

    public function destroy($id) {
		
		CategoryStore::where('id', $id)->where('vendor_id',auth()->user()->id)->delete();
		
		return redirect()->back()->with('success', 'Category Store deleted successfully.');
	}

}
