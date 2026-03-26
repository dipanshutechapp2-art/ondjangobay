<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DropshipProduct extends Model
{
    use HasFactory;
		protected $fillable = [
		'external_id',
		'title',
		'aliexpress_product_id',
		'description',
		'price',
		'stock',
		'image',
		'supplier_name',
	];
}
