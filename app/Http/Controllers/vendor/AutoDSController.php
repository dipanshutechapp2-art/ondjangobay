<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Services\AutoDSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AutoDSController extends Controller
{
    protected AutoDSService $autoDS;

    public function __construct(AutoDSService $autoDS)
    {
        $this->autoDS = $autoDS;
    }

    public function store(Request $request)
	{
		$request->validate([
			'category_id'        => 'required|exists:categories,id',
			'store_id'           => 'required|exists:vendor_stores,id',
			'autods_store_id'    => 'required|exists:auto_ds_stores,autods_store_id',
		]);

		try {
			$userId        = auth()->id();
			$autoDsStoreId = $request->autods_store_id;
			
			$count = $this->autoDS->importProductsFromAutoDS(
				$userId,
				$autoDsStoreId,
				$request->category_id,
				$request->store_id
			);

			if ($count === 0) {
				return back()->with('error', 'No new AutoDS products found.');
			}

			return back()->with(
				'success',
				"{$count} AutoDS products imported successfully."
			);

		} catch (\Throwable $e) {

			Log::error('AutoDS Import Error', [
				'error' => $e->getMessage(),
			]);

			return back()->with('error', $e->getMessage());
		}
	}

}
