<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;
use App\Models\VendorStore;
use App\Models\User;
use Auth;

class VendorStoreController extends Controller
{
      public function add() {
        $vendors=User::where('role', 'vendor')->get();
        return view('admin.store.create', compact('vendors'));
      }
		

public function show(Request $request)
{
    if ($request->ajax()) {
        $stores = VendorStore::with('user')->get();

        return DataTables::of($stores)
            ->addColumn('logo', function ($store) {
                $logoPath = $store->logo ? asset('/uploads/store/' . $store->logo) : asset('/admin/images/no_store.png');
                return '<img src="' . $logoPath . '" alt="Logo" class="img-fluid rounded" style="max-height: 80px;">';
            })
             ->addColumn('vendor_name', function ($store) {
                return $store->user->name;
            })

            ->addColumn('status', function ($store) {
                return $store->status
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>';
            })
            ->addColumn('created_at', function ($store) {
                return $store->created_at->format('M d, Y');
            })
            ->addColumn('updated_at', function ($store) {
                return $store->updated_at->format('M d, Y');
            })
       
            ->addColumn('action', function($store) {
					
					$editUrl   = route('admin.vendorstore.edit', $store->slug);
					$deleteUrl = route('admin.vendorstore.destroy', $store->id);
					$token = csrf_token();
			   
					return '
						<div class="btn-group">
							<button type="button" class="btn btn-success">Action</button>
							<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
								<span class="sr-only">Toggle Dropdown</span>
							</button>
							<div class="dropdown-menu" role="menu">
								<a class="dropdown-item" href="'.$editUrl.'" data-id="'.$store->slug.'" data-name="'.$store->name.'">Edit</a>
								<a class="dropdown-item delete-category" href="'.$deleteUrl.'" data-id="'.$store->id.'">Delete</a>
								<form id="delete-form-'.$store->id.'" action="'.$deleteUrl.'" method="POST" style="display: none;">
									<input type="hidden" name="_token" value="'.$token.'">
									<input type="hidden" name="_method" value="DELETE">
								</form>
							</div>
						</div>
					';
				})
            ->rawColumns(['logo', 'status', 'action'])
            ->make(true);
        }

    return view('admin.store.show');
}

	public function create(Request $request) {

	    $request->validate([
            'store_name'  => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo'        => 'nullable|image|max:2048',
        ]);
		
        $store = new VendorStore;
		$store->user_id     = $request->user_id;
		$store->store_name  = $request->store_name;
        $store->slug        = Str::slug($request->store_name);
        $store->description = $request->description;
        $store->status      = $request->status ?? 1;

        if(isset($request->logo)) {
			
			#IMG DELETE BEFORE UPDATE RECORD
			if ($store->logo && File::exists(public_path('/uploads/store/'.$store->logo))) {
				File::delete(public_path('/uploads/store/'.$store->logo));
			}
			$imageName = time() . '.' . $request->logo->extension();
			$request->logo->move(public_path('uploads/store/'), $imageName);
			$store->logo    = $imageName;
		}
		
        $store->save();

        return redirect()->route('admin.vendorstore.show')->with('success', 'Store created successfully.');
    }
	

    public function edit($slug) {
	
        $store = VendorStore::where('slug',$slug)->firstOrFail();
		 $vendors=User::where('role', 'vendor')->get();
        return view('admin.store.edit', compact('store', 'vendors'));
    }

    public function update(Request $request,$slug) {
		
        $request->validate([
            'store_name'  => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo'        => 'nullable|image|max:2048',
        ]);

        $store = VendorStore::where('slug',$slug)->firstOrFail();
        
		$store->store_name  = $request->store_name;
		$store->user_id  = $request->user_id;
        $store->slug        = Str::slug($request->store_name);
        $store->description = $request->description;
        $store->status      = $request->status;

        if(isset($request->logo)) {
			
			#IMG DELETE BEFORE UPDATE RECORD
			if ($store->logo && File::exists(public_path('/uploads/store/'.$store->logo))) {
				File::delete(public_path('/uploads/store/'.$store->logo));
			}
			$imageName = time() . '.' . $request->logo->extension();
			$request->logo->move(public_path('uploads/store/'), $imageName);
			$store->logo    = $imageName;
		}
		
        $store->save();

        return redirect()->route('admin.vendorstore.show')->with('success', 'Store updated successfully.');
    }

    public function destroy($id) {
       $store = VendorStore::where('id', $id)->firstOrFail();

       // Delete logo file if exists
       if ($store->logo && File::exists(public_path('/uploads/store/' . $store->logo))) {
          File::delete(public_path('/uploads/store/' . $store->logo));
       }

       $store->delete();
       return redirect()->back()->with('success', 'Store deleted successfully.');
    }
     

}
