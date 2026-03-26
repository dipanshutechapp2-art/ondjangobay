<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {
	
    protected $table = 'orders';

    public function user()
	{
		return $this->belongsTo(User::class);
	}
	
	public function vendor()
	{
		return $this->belongsTo(User::class,'vendor_id');
	}
	
	public function orderProduct() {
		
        return $this->hasMany(Order_product::class,'order_id');
    }
	
	public function orderTotal() {
		
        return $this->hasMany(Order_total::class,'order_id');
    }
	
	public function shipments()
	{
		return $this->hasMany(Shipment::class);
	}
}
