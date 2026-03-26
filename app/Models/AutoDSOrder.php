<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoDSOrder extends Model
{
    use HasFactory;

    protected $table = 'autods_orders';

    protected $fillable = [
        'local_order_id',
        'vendor_id',
        'autods_order_number',
        'autods_orderid',
        'logistic_name',
        'status',
        'request_payload',
        'response_data',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_data'   => 'array',
    ];

    public function localOrder()
    {
        return $this->belongsTo(Order::class, 'local_order_id');
    }
}
