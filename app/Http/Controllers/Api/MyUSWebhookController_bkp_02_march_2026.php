<?php
namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MyUSWebhookController
{
    private function validateSecret(Request $request)
    {
        $secret = $request->header('X-MyUS-Secret');

        if ($secret !== config('services.myus.webhook_secret')) {
            Log::warning('Invalid MyUS webhook secret', [
                'received' => $secret
            ]);
            abort(401, 'Unauthorized');
        }
    }

    public function orderStatus(Request $request)
    {
        $this->validateSecret($request);

        Log::info('MyUS Order Status Push', $request->all());

        $order = Order::where('order_number', $request->MasterOrderNumber)->first();

        if (!$order) {
            return response()->json(['success' => false]);
        }

        $order->update([
            'order_status' => strtolower($request->OrderStatus ?? 'processing')
        ]);

        return response()->json(['success' => true]);
    }

    public function shipmentUpdate(Request $request)
    {  
        $this->validateSecret($request);
		 
        Log::info('MyUS Shipment Push', $request->all());

        $order = Order::where('order_number', $request->MasterOrderNumber)->first();

        if (!$order) {
            return response()->json(['success' => false]);
        }

        $shipment = Shipment::create([
            'order_id' => $order->id,
            'vendor_id' => $order->vendor_id,
            'carrier' => 'MYUS',
            'external_order_id' => $request->OrderId ?? null,
            'external_shipment_id' => $request->ShipmentId ?? null,
            'tracking_number' => $request->TrackingNumber ?? null,
            'tracking_url' => $request->TrackingURL ?? null,
            'shipment_status' => strtolower($request->ShipmentStatus ?? 'shipped'),
            'shipped_at' => now(),
            'raw_response' => $request->all()
        ]);

        $order->update([
            'order_status' => 'shipped',
            'tracking_number' => $shipment->tracking_number,
            'shipping_provider' => 'MYUS',
			'shipping_status'   => 'shipped',
            'shipped_at' => now()
        ]);

        return response()->json(['success' => true]);
    }
}