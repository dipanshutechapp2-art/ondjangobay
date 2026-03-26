<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerOrder extends Model
{
    protected $fillable = [
        'user_id',
        'vendor_id',
        'partner_campaign_id',
        'order_number',
        'partner_product_id',
        'quantity',
        'amount',
        'currency',
        'status',
        'payment_method',
        'payment_status',
        'payment_transaction_id',
        'billing_first_name',
        'billing_last_name',
        'billing_company',
        'billing_country',
        'billing_address_1',
        'billing_address_2',
        'billing_city',
        'billing_state',
        'billing_zipcode',
        'billing_phone',
        'billing_email',
        'shipping_first_name',
        'shipping_last_name',
        'shipping_company',
        'shipping_country',
        'shipping_address_1',
        'shipping_address_2',
        'shipping_city',
        'shipping_state',
        'shipping_zipcode',
        'tracking_no',
        'shipping_provider',
        'shipped_at',
        'reason',
        'order_notes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(PartnerProduct::class, 'partner_product_id');
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function campaign()
    {
        return $this->belongsTo(PartnerCampaign::class, 'partner_campaign_id');
    }
}