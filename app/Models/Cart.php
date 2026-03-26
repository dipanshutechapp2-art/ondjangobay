<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model

{
    protected $fillable = ['user_id', 'product_id', 'quantity', 'attribute_value_id', 'attribute_id','variants','price'];
    
    protected $casts = [
        'variants' => 'array',
    ];
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }
    
    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class, 'attribute_value_id');
    }
}