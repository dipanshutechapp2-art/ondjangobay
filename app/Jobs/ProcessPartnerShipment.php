<?php
namespace App\Jobs;

use App\Models\PartnerCampaign;

class ClosePartnerCampaign extends Job
{
    public function handle(PartnerCampaign $campaign)
	{
		foreach ($campaign->products as $product) {
			foreach ($product->orders as $order) {
				$order->update(['status' => 'confirmed']);
				// DHLService::createShipment($order);
				NotificationService::notifyShipment($order->user);
			}
		}
	}

}
