<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerRefund extends Model
{
    protected $fillable = ['partner_order_id', 'reason', 'amount', 'processed_at'];

    public function order()
    {
        return $this->belongsTo(PartnerOrder::class);
    }
}

