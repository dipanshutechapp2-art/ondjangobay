<?php

namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AliExpressService;

class AlibabaProductController extends Controller
{
    public function __construct(
        protected AliExpressService $importService
    ) {}

    public function import(Request $request) {   
	
        $keyword = $request->keyword;

        $success = $this->importService->importProducts($keyword);

        if ($success) {
            return response()->json(['message' => 'Products imported successfully']);
        }

        return response()->json(['message' => 'Failed to import products'], 500);
    }
	
	public function importProduct(Request $request)
	{
		return view('vendor.alibaba.import_product');
	}
}
