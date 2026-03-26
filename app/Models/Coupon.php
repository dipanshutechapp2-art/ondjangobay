<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Facades\File;

class Coupon extends Model
{
    protected $fillable = [
        'vendor_id', 'store_id', 'code', 'type', 'value',
        'min_order_amount', 'max_uses', 'max_uses_per_user',
        'starts_at', 'expires_at', 'is_active'
    ];

    public function vendor() {
        return $this->belongsTo(User::class,'vendor_id');
    }

    public function store() {
        return $this->belongsTo(VendorStore::class,'store_id');
    }
}

