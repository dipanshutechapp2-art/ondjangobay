<?php
namespace App\Services\DHL;

class DHLTrackingService extends DHLBaseService
{
	
	public function track($trackingNumber)
	{
		return $this->client()->get(
			$this->baseUrl() . '/track/shipments',
			['trackingNumber' => $trackingNumber]
		)->json();
	}
}

