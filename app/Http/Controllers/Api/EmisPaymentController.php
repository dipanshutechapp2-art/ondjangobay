<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class EmisPaymentController extends Controller
{
   
    public function pay(Request $request)
    {
        $request->validate([
            'order_id'       => 'required',
            'pay_type'       => 'required|in:emis',
        ]);
		
		$orderIds  = explode(',',$request->order_id);
        
        if (!is_array($orderIds) || empty($orderIds)) {
            return $this->errorResponse('Invalid orders', 400, $request);
        }

        $orders = Order::whereIn('id', $orderIds)->get();

        if ($orders->count() !== count($orderIds)) {
            return $this->errorResponse('Some orders not found', 404, $request);
        }

        $totalAmount    = $orders->sum('total_amount');
        $groupReference = 'ORD' . time();

        Order::whereIn('id', $orderIds)->update([
            'payment_group_reference' => $groupReference,
            'order_status' => 'pending',
        ]);

        $response = Http::acceptJson()->post(
            config('emis.base_url') . '/online-payment-gateway/webframe/v1/frameToken',
            [
                'reference'   => $groupReference,
                'amount'      => number_format($totalAmount, 2, '.', ''),
                'token'       => config('emis.frame_token'),
                'mobile'      => 'PAYMENT',
                'qrCode'      => 'PAYMENT',
                'card'        => 'DISABLED',
                'terminal'    => config('emis.terminal'),
                'callbackUrl' => route('emis.callback'),
            ]
        );

        if (!$response->successful()) {
            Log::error('EMIS Token Error', ['body' => $response->body()]);
            return $this->errorResponse('EMIS Token Error', 500, $request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success'     => true,
                'frame_token' => $response['id'],
                'reference'   => $groupReference,
                'amount'      => $totalAmount,
            ]);
        }
    }

    /**
     * WEB + API : EMIS Callback
     */
    public function callback(Request $request)
    {
        Log::info('EMIS Callback', $request->all());

        $order = Order::where(
            'payment_group_reference',
            $request->merchantReferenceNumber
        )->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        if ($request->status === 'PAYMENT_COMPLETED') {
            $order->update([
                'order_status'      => 'paid',
                'payment_reference' => $request->id ?? null,
            ]);
        } else {
            $order->update([
                'order_status'  => 'failed',
                'payment_error' => $request->errorMessage ?? 'Payment failed',
            ]);
        }

        return response()->json(['success' => true]);
    }

    private function errorResponse(string $message, int $code, Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], $code);
        }

        abort($code, $message);
    }
}
