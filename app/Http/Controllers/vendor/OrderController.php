<?php
namespace App\Http\Controllers\vendor;
use App\Models\Product;
use App\Models\Order;
use App\Models\Order_product;
use App\Models\Order_total;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index() {
		
        //$orders = Order::with('user')->where('vendor_id',auth()->id())->orderBy('created_at', 'desc')->paginate(20);
        $orders = Order::with('user')->where('vendor_id',auth()->id())->orderBy('created_at', 'desc')->get();

        return view('vendor.orders.list', compact('orders'));
    }
	
	public function orderDetails(Request $request,$id) { 
	
	    $orderInfo = Order::with('orderProduct.product.userInfo','orderTotal')->where('id',$id)->where('vendor_id',auth()->id())->first();
	
		return view('vendor.orders.order_details',compact('orderInfo'));
    }
	
	public function downloadInvoice($id)
    {
        $orderInfo = Order::with('orderProduct.product.userInfo','orderTotal')->where('id',$id)->where('vendor_id',auth()->id())->first();
			
        $pdf = Pdf::loadView('vendor.invoices.invoice', compact('orderInfo'));

        return $pdf->download('invoice_order_' . $orderInfo->id . '.pdf');
    }
	
    public function updateorderStatus(Request $request) {
		
        $id                  = $request->order_id;
        $order               = Order::findOrFail($id);
        $order->order_status = $request->order_status;
        $order->save();
		
		#SEND ORDER MAIL
    	$orderInfo = Order::with('orderProduct.product','orderTotal')->where('id',$order->id)->first();
		$res = Mail::send('emails.order-status', ['order' => $orderInfo], function ($message) use ($orderInfo) {
			$message->to([$orderInfo->email,'sandeepsvi1990@gmail.com'])
					->subject('Your Order Receipt - #' . $orderInfo->order_number);
		});
		
        return redirect()->back()->with('success', 'Order status updated successfully.');
    }
    
	public function updatePaymentStatus(Request $request) {
		
        $id                    = $request->order_id;
        $order                 = Order::findOrFail($id);
        $order->payment_status = $request->payment_status;
        $order->save();
		
		#SEND ORDER MAIL
    	/* $orderInfo = Order::with('orderProduct.product','orderTotal')->where('id',$order->id)->first();
		$res = Mail::send('emails.order-status', ['order' => $orderInfo], function ($message) use ($orderInfo) {
			$message->to([$orderInfo->email,'sandeepsvi1990@gmail.com'])
					->subject('Your Order Receipt - #' . $orderInfo->order_number);
		}); */
		
        return redirect()->back()->with('success', 'Payment status updated successfully.');
    }

}
