<?php
namespace App\Http\Controllers\admin;
use App\Models\Order;
use App\Models\PartnerOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;

class PartnerOrderController extends Controller
{
    public function index()
    {
        $orders = PartnerOrder::with('user')->orderBy('created_at', 'desc')->get();

        return view('admin.partner_orders.list', compact('orders'));
    }
	
	public function orderDetails(Request $request,$id) { 
	
	    $orderInfo = PartnerOrder::with('product')->where('id',$id)->first();
		
		return view('admin.partner_orders.order_details',compact('orderInfo'));
    }
	
	public function downloadInvoice($id)
    {
        $orderInfo = PartnerOrder::with('product')->where('id',$id)->first();

        $pdf = Pdf::loadView('admin.invoices.partner_invoice', compact('orderInfo'));

        return $pdf->download('invoice_partner_order_' . $orderInfo->id . '.pdf');
    }
	
    public function updateorderStatus(Request $request)
    {
        $id    = $request->order_id;
        $order = PartnerOrder::findOrFail($id);
        $order->status = $request->order_status;
        $order->save();
		
		#SEND ORDER MAIL
    	$orderInfo = PartnerOrder::with('product')->where('id',$order->id)->first();
		$res = Mail::send('emails.partner-order-status', ['order' => $orderInfo], function ($message) use ($orderInfo) {
			$message->to([$orderInfo->billing_email,'sandeepsvi1990@gmail.com'])
					->subject('Your Partner Order Receipt - #' . $orderInfo->order_number);
		});

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }
	
	public function updatePaymentStatus(Request $request) {
		
        $id                    = $request->order_id;
        $order                 = PartnerOrder::findOrFail($id);
        $order->payment_status = $request->payment_status;
        $order->save();
		
		#SEND ORDER MAIL
    	/* $orderInfo = Order::with('orderProduct.product','orderTotal')->where('id',$order->id)->first();
		$res = Mail::send('emails.order-status', ['order' => $orderInfo], function ($message) use ($orderInfo) {
			$message->to([$orderInfo->billing_email,'sandeepsvi1990@gmail.com'])
					->subject('Your Partner Order Receipt - #' . $orderInfo->order_number);
		}); */
		
        return redirect()->back()->with('success', 'Payment status updated successfully.');
    }

}
