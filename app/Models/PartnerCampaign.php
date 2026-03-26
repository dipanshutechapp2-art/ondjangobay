<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerCampaign extends Model
{
    use HasFactory;

    protected $table = 'partner_campaigns';

    // Fillable fields
    protected $fillable = [
        'name',
        'frequency',
        'start_date',
        'end_date',
        'upload_deadline',
        'min_value',
        'min_quantity',
        'goal_quantity',
        'category_id',
        'cart_timer_minutes',
        'cart_max_volume',
        'status',
    ];

    // Casts
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'upload_deadline' => 'date',
        'min_value' => 'decimal:2',
        'min_quantity' => 'integer',
        'goal_quantity' => 'integer',
        'cart_timer_minutes' => 'integer',
        'cart_max_volume' => 'integer',
    ];

    // Relationship: Campaign has many products
    public function products()
    {
        return $this->hasMany(PartnerProduct::class);
    }
	
	public function campaignProducts()
    {
        return $this->hasMany(PartnerProduct::class, 'partner_campaign_id');
    }

    // Relationship: Category
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
