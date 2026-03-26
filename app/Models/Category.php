<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table    = 'categories';
	
    protected $fillable = ['name', 'slug', 'status','parent_id','desktop_image','mobile_image'];
	
	public function products() {
		
		return $this->belongsToMany(Product::class,'category_product')->withTimestamps();
	}
	
	public function children() {
		
		return $this->hasMany(Category::class, 'parent_id')->with('children');
	}

	public function parent() {
		
		return $this->belongsTo(Category::class, 'parent_id');
	}
	
	/* ADD NEW */
	
	public function stores()
	{
		return $this->belongsToMany(VendorStore::class, 'category_store', 'category_id', 'store_id')
					->withTimestamps();
	}
	
}
