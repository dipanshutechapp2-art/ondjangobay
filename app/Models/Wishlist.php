<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Wishlist extends Model
{
    use HasFactory;
	
	protected $fillable = ['id','product_id','user_id'];
	
	
	public function user() {
		
        return $this->belongsTo(User::class,'user_id');
    }
	
	public function product() {
		
        return $this->belongsTo(Product::class,'product_id');
    }
}
