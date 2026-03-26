<?php
namespace App\Services;

use App\Models\Transaction;
use App\Models\VendorCommissions;

class SplitPaymentService {
    public function process(Transaction $transaction, VendorCommissions $vendor) {
        if ($vendor->category_code === 'internal') {
            // Internal: full payment to Ondjango
            return [
                'status' => 'pending_settlement',
                'message' => 'Payment received, will settle manually later.'
            ];
        } else {
            // External: integrate with Stripe Connect / Paystack Split / custom
            // Pseudo-code example:
            return [
                'status' => 'success',
                'message' => 'Split payment sent to vendor and Ondjango.'
            ];
        }
    }
}

