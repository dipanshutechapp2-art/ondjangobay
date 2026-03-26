<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Product extends Model
{
    use HasFactory;
	
	protected $fillable = ['name','slug','sku','meta_title','meta_keyword','meta_description','short_description','price','quantity','category_id','image','status','description','brand_id','seller_id','specifications','cj_pid','auto_ds_product_id','auto_ds_main_picture_url', 'autods_store_id'];
	
	protected $casts = [
		'specifications' => 'array',
	];
	
	public function user() {
		
        return $this->hasMany(User::class,'seller_id');
    }
	
	public function userInfo() {
		
        return $this->belongsTo(User::class,'seller_id');
    }
	
	public function category() {
		
        return $this->belongsTo(Category::class)->withTimestamps();
    }
	
	public function quickViewcategory() {
		
        return $this->belongsTo(Category::class);
    }
	
	public function categories() {
		
		return $this->belongsToMany(Category::class,'category_product')->withTimestamps();
	}
	
	public function stores() {
		
		return $this->belongsToMany(VendorStore::class, 'product_store', 'product_id', 'store_id')->withTimestamps();
	}
	
	public function attributes() {
		
        return $this->hasMany(ProductAttribute::class);
    }
	
	public function productAttributes()
	{
		return $this->hasMany(ProductAttribute::class)->with('attribute');
	}
	
	public function deleteWithImage() {
		
		$this->load('productAttributes.variants');
		
		foreach ($this->productAttributes as $attribute) {
			$attribute->variants()->delete();
		}
		$this->productAttributes()->delete();
		
		
        if ($this->image) {
            $imagePath = public_path('/uploads/products/'.$this->image);
			
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }
        return $this->delete();
    }

	public function reviews()
		{
			return $this->hasMany(\App\Models\Review::class);
		}
		
	public function galleryImages()
	{
		return $this->hasMany(ProductGallery::class, 'product_id');
	}

	public function brand() {
		
        return $this->belongsTo(Brands::class);
    }
	
	public function orders()
	{
		return $this->hasMany(Order_product::class, 'product_id'); 
	}
	
	public function comparisons(): HasMany
    {
        return $this->hasMany(ProductComparison::class);
    }

}
