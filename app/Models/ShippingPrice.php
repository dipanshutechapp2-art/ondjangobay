<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingPrice extends Model {
    protected $fillable = [
        'country_id',
        'shipping_option_id',
        'price',
        'eta_min',
        'eta_max',
        'is_active',
		'sort_order',
		'is_default'
    ];

    public function country() {
        return $this->belongsTo(Country::class,'country_id');
    }

    public function option() {
        return $this->belongsTo(ShippingOption::class, 'shipping_option_id');
    }
}

