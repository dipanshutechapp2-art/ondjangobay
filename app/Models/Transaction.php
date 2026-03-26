<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model {
    use HasFactory;

    protected $fillable = [
        'order_id', 'vendor_id', 'product_id', 'vendor_amount', 'ondjango_commission', 'payment_method', 'transaction_id', 'status','settled_at'
    ];

    public function vendor() {
        return $this->belongsTo(User::class,'vendor_id');
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
