<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Wholesale2BImportService;
use App\Models\User;
use App\Models\Category;
use App\Models\VendorStore;
use App\Models\AutoDSStore;
use App\Models\AutoDSToken;
use Illuminate\Support\Str;

class ProductImportController extends Controller
{
    
	public function wholesale2bImport()
    {	
        $categories       = Category::all();
		$stores           = VendorStore::get();
		$autoDsStores     = AutoDSStore::where('user_id',auth()->id())->active()->get();
		$autoDsTokenInfo  = AutoDSToken::where('user_id',auth()->id())->first();
		
	    return view('admin.wholesale2.import_product',compact('categories','stores','autoDsStores','autoDsTokenInfo'));
    }
	
	public function import(Request $request, Wholesale2BImportService $importService)
    {	
		$request->validate([
			'category_id'  => 'required|exists:categories,id',
			'store_id'     => 'required|exists:vendor_stores,id',
		]);
       
		try {
			$count = $importService->import($request->category_id, $request->store_id);
			return redirect()->back()->with('success', "Imported $count products successfully.");
		} catch (\Exception $e) {
			return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
		}
    }
}
