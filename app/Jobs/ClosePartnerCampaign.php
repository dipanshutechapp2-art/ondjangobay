<?php

namespace App\Jobs;

use App\Models\PartnerCampaign;
use App\Jobs\ProcessPartnerRefund;
use App\Jobs\ProcessPartnerShipment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ClosePartnerCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Find all active campaigns whose end_date is passed
        $campaigns = PartnerCampaign::with('products.orders')
            ->where('status', 'active')
            ->whereDate('end_date', '<=', now())
            ->get();

        foreach ($campaigns as $campaign) {
            try {
                // Flatten all orders for products in this campaign
                $totalOrders = $campaign->products->flatMap->orders;
                $totalQty = $totalOrders->sum('quantity');

                // Log campaign check
                Log::info("Checking campaign #{$campaign->id} — totalQty: {$totalQty}, minQty: {$campaign->min_quantity}");

                // Refund or ship based on goal reached
                if ($totalQty < $campaign->min_quantity) {
                    ProcessPartnerRefund::dispatch($campaign);
                    Log::info("Refund triggered for campaign #{$campaign->id}");
                } else {
                    ProcessPartnerShipment::dispatch($campaign);
                    Log::info("Shipment triggered for campaign #{$campaign->id}");
                }

                // Close the campaign
                $campaign->update(['status' => 'closed']);
            } catch (\Throwable $e) {
                Log::error("Error processing campaign #{$campaign->id}: " . $e->getMessage());
            }
        }
    }
}
