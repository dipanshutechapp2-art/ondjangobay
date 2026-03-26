<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Facades\File;

class VendorStore extends Model
{
    use HasFactory;
	
	protected $fillable = [
        'user_id', 'store_name', 'slug', 'description', 'logo','status',
    ];
	
	public function user()  {
		
        return $this->belongsTo(User::class);
    }

    /* public function products() {
		
        return $this->hasMany(Product::class);
    } */
	
	public function products() {
		
		return $this->belongsToMany(Product::class, 'product_store', 'store_id', 'product_id')
                ->withTimestamps();
	}
	
	public function deleteWithImage() {

        if ($this->logo) {
            $imagePath = public_path('/uploads/store/'.$this->logo);
			
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }
        return $this->delete();
    }
	
	public function storeReviews()
	{
		return $this->hasMany(\App\Models\StoreReview::class, 'store_id');
	}
	
	public function getAverageRatingAttribute()
	{
		return $this->storeReviews()->avg('rating') ?? 0;
	}

	public function getReviewCountAttribute()
	{
		return $this->storeReviews()->count();
	}
	/* ADD NEW */
	public function categories()
	{

		return $this->belongsToMany(Category::class, 'category_store', 'store_id', 'category_id')
					->withTimestamps();
	}
	public function vendor()
    {
        return $this->belongsTo(User::class); 
    }
}
