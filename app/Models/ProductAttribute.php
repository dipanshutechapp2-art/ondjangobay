<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ProductAttribute extends Model
{
    protected $fillable = ['product_id', 'attribute_id'];

    public function product() {
		
        return $this->belongsTo(Product::class);
    }

    public function attribute() {
		
        return $this->belongsTo(Attribute::class);
    }

    public function variants() {
		
        return $this->hasMany(ProductVariant::class);
    }
	
	public function values()  {
		
        return $this->variants()->select('value')->groupBy('value');
    }
}
