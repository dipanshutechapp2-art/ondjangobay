<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Category;
use App\Services\DobaDropshippingService;

class DobaController extends Controller
{
    protected $doba;

    public function __construct(DobaDropshippingService $doba)
    {
        $this->doba = $doba;
    }

    public function import()
    {
		$data  = $this->doba->getProducts('shoes');
		//$data  = $this->doba->getProducts();
		dd($data);
        $list  = $data['products'] ?? [];
        $count = $this->doba->importProductsFromDoba($list, 5, 1);
        return response()->json(['imported' => $count]);
    }

    public function createOrder($orderId)
    {
        return $this->doba->createOrderFromLocal($orderId);
    }

    public function track($orderId)
    {
        return $this->doba->getOrderStatus($orderId);
    }
}
