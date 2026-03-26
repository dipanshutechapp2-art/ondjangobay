<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\PayoneerService;

class PayoneerController extends Controller
{
    public function pay($order_id)
    {
        $order = Order::findOrFail($order_id);

        $payoneer = new PayoneerService();
        $response = $payoneer->createPayment($order);

        // Save transaction
        $order->update([
            "payment_id" => $response["id"],
            "payment_status" => "pending"
        ]);

        return redirect()->route('payoneer.success', $order->id);
    }

    public function success($order_id)
    {
        return view("payment.success", compact("order_id"));
    }
	
	public function handle(Request $req)
    {
        $payment_id = $req->paymentId;
        $status     = $req->status;

        $order = Order::where("payment_id", $payment_id)->first();

        if ($status == "paid") {
            $order->update(["payment_status" => "completed"]);
        }

        if ($status == "failed") {
            $order->update(["payment_status" => "failed"]);
        }

        return response()->json(["ok" => true]);
    }
}
