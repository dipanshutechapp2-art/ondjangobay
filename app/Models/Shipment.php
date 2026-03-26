<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = [
        'order_id',
        'vendor_id',
        'carrier',
        'external_order_id',
        'external_shipment_id',
        'tracking_number',
        'tracking_url',
        'shipment_status',
        'shipped_at',
        'delivered_at',
        'raw_response'
    ];

    protected $casts = [
        'raw_response' => 'array',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

