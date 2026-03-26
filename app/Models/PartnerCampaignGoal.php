<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerCampaignGoal extends Model
{
    protected $fillable = ['partner_campaign_id', 'current_quantity', 'current_value'];

    public function campaign()
    {
        return $this->belongsTo(PartnerCampaign::class);
    }
}

