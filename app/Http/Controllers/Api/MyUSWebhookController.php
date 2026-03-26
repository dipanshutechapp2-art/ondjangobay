<?php
namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use DB;
use Carbon\Carbon;

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
		
    public function receivePackage(Request $request)
    {
        $this->validateSecret($request);

        Log::info('MyUS ReceivePackage', $request->all());

        $data = $request->all();
        
        foreach ($data['Packages'] ?? [] as $packageData) {

            $photoUrl = null;

			foreach ($packageData['PackageDocuments'] ?? [] as $doc) {
				if ($doc['DocumentType'] === 'Photo') {
					$photoUrl = $doc['Url'];
				}
			}
			
			$order = null;
            foreach ($packageData['PackageItemDetails'] ?? [] as $item) {
                if (!empty($item['MasterOrderNumber'])) {
                    $order = Order::where('master_order_number', $item['MasterOrderNumber'])->first();
                    if ($order) break;
                }
            }

            if ($order) {
                $order->update([
                    'myus_package_status' => $packageData['PackageStatus'],
                    'myus_package_received_at' => now()
                ]);
            }
			
			DB::table('myus_packages')->updateOrInsert(
				['package_id' => $packageData['PackageId']],
				[
					'order_id'          => $order->id ?? null,
					'tracking_number'   => $packageData['ShipperTrackingNumber'] ?? null,
					'weight'            => $packageData['Weight'] ?? null,
					'weight_unit'       => $packageData['WeightUnit'] ?? null,
					'package_status'    => $packageData['PackageStatus'],
					'package_substatus' => $packageData['PackageSubStatus'] ?? null,
					//'arrival_date'    => $packageData['PackageArrivalDateAtWarehouse'],
					'arrival_date' => isset($packageData['PackageArrivalDateAtWarehouse'])
    ? Carbon::parse($packageData['PackageArrivalDateAtWarehouse']) : null,
					'raw_response'      => json_encode($packageData),
					'photo_url'         => $photoUrl,
					'updated_at'        => now(),
					'created_at'        => DB::raw('COALESCE(created_at, NOW())')
				]
			);
			
        }

        return response()->json([
            'notification_type' => 'RECEIVEPACKAGE',
            'issuccess' => true,
            'exception' => null
        ]);
    }
		
	public function maintainPackageStatus(Request $request)
	{
		$this->validateSecret($request);
		
		Log::info('MyUS Maintain Package Status', $request->all());

		$data = $request->all();

		foreach ($data['Packages'] ?? [] as $packageData) {

			DB::table('myus_packages')->updateOrInsert(
				['package_id' => $packageData['PackageId']],
				[
					'package_status' => $packageData['PackageStatus'] ?? null,
					'package_substatus' => $packageData['PackageSubStatus'] ?? null,
					'updated_at' => now()
				]
			);
		}

		return response()->json([
			'notification_type' => 'MAINTAINPACKAGESTATUS',
			'issuccess' => true,
			'exception' => null
		]);
	}
	
    public function maintainShipmentStatus(Request $request)
	{  
		$this->validateSecret($request);
		 
		Log::info('MyUS Shipment Push', $request->all());

		$data = $request->all();
		
		foreach ($data['Shipments'] ?? [] as $shipmentData) {
		  
			$masterOrderNumbers = [];
			foreach ($shipmentData['MasterOrderInfo'] ?? [] as $orderInfo) {
				$masterOrderNumbers[] = $orderInfo['MasterOrderNumber'];
			}

			$orders = Order::whereIn('master_order_number', $masterOrderNumbers)->get();
			$primaryOrder = $orders->first();

			if ($primaryOrder) {
				
				$existingShipment = Shipment::where('external_shipment_id', $shipmentData['ShipmentId'])
					->where('carrier', 'MYUS')
					->first();
				
				if ($existingShipment) {
				
					Log::info('Updating existing shipment', [
						'shipment_id' => $existingShipment->id,
						'external_shipment_id' => $shipmentData['ShipmentId']
					]);
					
					$existingShipment->update([
						'tracking_number' => $shipmentData['CAWBNumber'] ?? $shipmentData['MAWBNumber'] ?? $existingShipment->tracking_number,
						'shipment_status' => strtolower($shipmentData['ShipmentStatus'] ?? $existingShipment->shipment_status),
						'raw_response' => json_encode($shipmentData),
						'updated_at' => now()
					]);
					
					$shipment = $existingShipment;
				} else {
					
					$tracking = $shipmentData['CAWBNumber'] ?? $shipmentData['MAWBNumber'] ?? null;
					
					$shipment = Shipment::create([
						'order_id' => $primaryOrder->id,
						'vendor_id' => $primaryOrder->vendor_id,
						'carrier' => 'MYUS',
						'external_order_id' => $primaryOrder->external_order_id,
						'external_shipment_id' => $shipmentData['ShipmentId'] ?? null,
						'tracking_number' => $tracking,
						'tracking_url' => $this->generateTrackingUrl('myus', $tracking),
						'shipment_status' => strtolower($shipmentData['ShipmentStatus'] ?? 'shipped'),
						'shipped_at' => now(),
						'raw_response' => json_encode($shipmentData)
					]);
					
					Log::info('Created new shipment', [
						'shipment_id' => $shipment->id,
						'external_shipment_id' => $shipmentData['ShipmentId']
					]);
				}

				foreach ($orders as $order) {
					if ($order->tracking_number !== $shipment->tracking_number) {
						$order->update([
							'order_status' => 'shipped',
							'tracking_number' => $shipment->tracking_number,
							'shipping_carrier' => 'MYUS',
							'shipping_status' => 'shipped',
							'shipped_at' => now()
						]);
					}
				}
			} else {
				Log::warning('No orders found for shipment', [
					'master_order_numbers' => $masterOrderNumbers,
					'shipment_id' => $shipmentData['ShipmentId'] ?? null
				]);
			}
		}

		return response()->json([
			'notification_type' => 'MAINTAINSHIPMENTSTATUS',
			'issuccess' => true,
			'exception' => null
		]);
	}

	public function generateTrackingUrl($carrier, $trackingNumber)
	{
		$carrier = strtolower($carrier);

		return match ($carrier) {
			'ups' => "https://www.ups.com/track?tracknum={$trackingNumber}",
			'fedex' => "https://www.fedex.com/fedextrack/?trknbr={$trackingNumber}",
			'dhl' => "https://www.dhl.com/in-en/home/tracking.html?tracking-id={$trackingNumber}",
			'myus' => "https://www.myus.com/track/?trackingNumber={$trackingNumber}",
			default => null
		};
	}
}