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
use Yajra\DataTables\Facades\DataTables;

class ShippingOptionsController  extends Controller
{

    public function index(Request $request)
    {
        $shippingOptions = ShippingOption::get();
        return view('admin.shipping_options.index', compact('shippingOptions'));
    }

}