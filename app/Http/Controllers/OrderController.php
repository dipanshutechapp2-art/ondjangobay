<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Product;
use App\Models\Order;
use App\Models\PartnerOrder;
use App\Models\Order_product;
use App\Models\Order_total;
use App\Models\Country;
use App\Models\State;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
   
    public function order_complete(Request $request) { 
	   
	   if(auth()->check()) {
	    $orderInfo = Order::with('orderProduct.product.userInfo','orderTotal')->where('user_id', auth()->id())->orderBy('id','DESC')->first();
	   }else{
	    $orderInfo = Order::with('orderProduct.product.userInfo','orderTotal')->where('is_guest','1')->orderBy('id','DESC')->first();
	   }

		return view('order.order_complete',compact('orderInfo'));
    }
	
	public function orders(Request $request) { 
	
	    $orderList = Order::with('orderProduct.product','orderTotal')->where('user_id', auth()->id())->first();

		return view('account.orders',compact('orderList'));
    }
	
	public function order_view(Request $request) {   
	
		return view('order.order_view');
    }
	
	public function downloadInvoice(Request $request,$id)
    {
        $orderInfo = Order::with('orderProduct.product.userInfo','orderTotal')->where('order_number',$id)->first();

        $pdf = Pdf::loadView('order.invoices.invoice', compact('orderInfo'));

        return $pdf->download('invoice_order_' . $orderInfo->id . '.pdf');
    }
	
	public function downloadPartnerInvoice(Request $request,$id)
    {  
        $orderInfo = PartnerOrder::with('product')->where('order_number',$id)->first();

        $pdf = Pdf::loadView('order.invoices.partner_invoice', compact('orderInfo'));

        return $pdf->download('invoice_partner_order_' . $orderInfo->id . '.pdf');
    }
}
