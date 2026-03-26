<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AutoDSService;
use App\Models\AutoDSStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AutoDSController extends Controller
{
    protected AutoDSService $autoDS;

    public function __construct(AutoDSService $autoDS)
    {
        $this->autoDS = $autoDS;
    }
	
	public function redirectApi()
	{
		$vendor_id = auth()->id();
	
		if (!$vendor_id) {
			return response()->json([
				'success' => false,
				'message' => 'Unauthenticated vendor',
			], 401);
		}
		
		$state = base64_encode(json_encode([
			'flow' => 'api',
			'vendor_id' => $vendor_id,
		]));
		
		$query = http_build_query([
			'client_id'     => config('services.autods.client_id'),
			'response_type' => 'code',
			'scope'         => 'email openid phone',
			'redirect_uri'  => config('services.autods.redirect_uri'),
			'state'         => $state,
		]);

		return response()->json([
			'success'  => true,
			'auth_url' => config('services.autods.auth_url') . '/login?' . $query,
		]);
	}
	
	public function getAutoDsStore(Request $request){
		
		$vendorID     = auth()->id();
		$autoDsStores = AutoDSStore::where('user_id',$vendorID)->get();
		
		return response()->json([
			'success' => true,
			'data'    => $autoDsStores,
		]);
	}
	
	public function autoDsProductImport(Request $request)
	{
		$validator = \Validator::make($request->all(), [
			'category_id'     => 'required|exists:categories,id',
			'store_id'        => 'required|exists:vendor_stores,id',
			'autods_store_id' => 'required|exists:auto_ds_stores,autods_store_id',
		]);

		if ($validator->fails()) {
			return response()->json([
				'success' => false,
				'message' => 'Validation failed',
				'errors'  => $validator->errors(),
			], 422);
		}

		try {
			$vendorID    = auth()->id();
			$autoDsStoreId = $request->autods_store_id;
			
			$count = $this->autoDS->importProductsFromAutoDS(
				$vendorID,
				$autoDsStoreId,
				$request->category_id,
				$request->store_id
			);

			if ($count === 0) {
				return response()->json([
					'success' => true,
					'message' => 'No new AutoDS products found.',
					'imported_count' => 0,
				]);
			}

			return response()->json([
				'success' => true,
				'message' => "{$count} AutoDS products imported successfully.",
				'imported_count' => $count,
			]);

		} catch (\Throwable $e) {

			Log::error('AutoDS Import API Error', [
				'user_id' => auth()->id(),
				'error'   => $e->getMessage(),
			]);

			return response()->json([
				'success' => false,
				'message' => 'AutoDS import failed',
				'error'   => config('app.debug') ? $e->getMessage() : 'Something went wrong',
			], 500);
		}
	}



}
