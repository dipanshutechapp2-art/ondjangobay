<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerProduct extends Model
{
    protected $fillable = [
        'partner_campaign_id', 'vendor_id', 'store_id', 'name', 'description',
        'image', 'old_price', 'new_price', 'min_quantity', 'max_quantity', 'status'
    ];

    public function campaign()
    {
        return $this->belongsTo(PartnerCampaign::class,'partner_campaign_id');
    }

    public function vendor()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(VendorStore::class);
    }

    public function orders()
    {
        return $this->hasMany(PartnerOrder::class);
    }

    public function getDiscountPercentAttribute()
    {
        return round((($this->old_price - $this->new_price) / $this->old_price) * 100);
    }
}
