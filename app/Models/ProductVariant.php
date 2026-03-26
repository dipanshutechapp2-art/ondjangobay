<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ProductVariant extends Model
{
    protected $fillable = ['product_attribute_id', 'value', 'price', 'sku', 'stock', 'image','product_id','cj_vid','auto_ds_buy_item_url', 'auto_ds_variant_id','autods_site_id', 'autods_region'];

    public function productAttribute()  {
		
        return $this->belongsTo(ProductAttribute::class);
    }
	
	public function attributeValue() {
		
      return $this->belongsTo(AttributeValue::class, 'value');

    }
}
