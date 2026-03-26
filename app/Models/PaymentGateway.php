<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    protected $fillable = [
		'name',
		'mode',
		'test_credentials',
		'live_credentials',
		'status',
		'is_default',
	];

	protected $casts = [
		'test_credentials' => 'array',
		'live_credentials' => 'array',
		'status' => 'boolean',
		'is_default' => 'boolean',
	];
	
	protected $appends = ['logo_path'];
	
	public function getLogoPathAttribute()
    {
        if ($this->logo) {
            return asset('uploads/payment_gateways/' . $this->logo);
        }
        return null; 
    }
}

