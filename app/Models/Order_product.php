<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order_product extends Model {
	
    protected $table = 'order_products';
	
	public function product()
	{
		return $this->belongsTo(Product::class);
	}
	
	public function vendor()
	{
		return $this->belongsTo(Vendor::class);
	}

}
