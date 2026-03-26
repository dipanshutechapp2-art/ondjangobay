<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CjOrder extends Model
{
    use HasFactory;

    protected $table = 'cj_orders';

    protected $fillable = [
        'local_order_id',
        'vendor_id',
        'cj_order_number',
        'cj_orderid',
        'status',
        'request_payload',
        'response_data',
        'logistic_name', 
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_data' => 'array',
    ];

    public function localOrder()
    {
        return $this->belongsTo(\App\Models\Order::class, 'local_order_id');
    }
}
