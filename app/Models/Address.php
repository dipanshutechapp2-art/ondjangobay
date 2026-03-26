<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Address extends Model {
	
    protected $table = 'address';
	
	protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'company_name',
        'country',
        'address_1',
        'address_2',
        'city',
        'state',
        'zip',
        'phone',
        'is_billing',
        'is_shipping',
        'is_default',
    ];
	
    public function user()
	{
		return $this->belongsTo(User::class);
	}
}
