<?php

namespace App\Services;

use App\Models\ProductCommissions;
use App\Models\VendorCommissions;
use App\Models\Commission;

class CommissionCalculationService
{
    /**
     * Calculate commission + payment flow based on vendor type
     */
    public function calculate(?ProductCommissions $product, ?VendorCommissions $vendor, float $basePrice = 0): array
    {
        $global = Commission::where('level', 'global')->value('commission_value') ?? 10;

        $commission = $global;

        if ($vendor && $vendor->commission_type === 'custom' && $vendor->commission_value) {
            $commission = $vendor->commission_value;
        }

        if ($product && $product->commission_type === 'custom' && $product->commission_value) {
            $commission = $product->commission_value;
        }

        $price = $product->price ?? $basePrice ?? 0;

        $vendorAmount       = $price - ($price * $commission / 100);
        $ondjangoCommission = $price - $vendorAmount;

        $vendorType  = $vendor->category_code ?? 'internal';
        $paymentFlow = 'ondjango_only';
        $paymentNote = '100% of payment goes to Ondjango. Vendor settlement handled manually later.';

        if ($vendorType === 'external') {
            $paymentFlow = 'split_payment';
            $paymentNote = 'Payment split automatically: Vendor receives base price, Ondjango receives commission.';
        }

        return [
            'vendor_amount'       => round($vendorAmount, 2),
            'ondjango_commission' => round($ondjangoCommission, 2),
            'commission_rate'     => round($commission, 2),
            'price_base'          => round($price, 2),
            'payment_flow'        => $paymentFlow,
            'payment_note'        => $paymentNote,
            'vendor_type'         => $vendorType,
        ];
    }
}
