<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'review',
        'author',
        'email',
        'image',
        'likes', 
        'dislikes',
    ];

    protected $appends = [
        'review_image',
        'review_image_url',
        'user_image',
        'user_image_url',
    ];


    protected $hidden = ['image', 'user'];

    public function getReviewImageAttribute()
    {
        return $this->image ?? null;
    }

    public function getReviewImageUrlAttribute()
    {
        return $this->image ? asset('uploads/reviews/') : null;
    }

    public function getUserImageAttribute()
    {
        return $this->user->image ?? null;
    }
    
    public function getUserImageUrlAttribute()
    {
        return $this->user && $this->user->image
            ? asset('uploads/users/')
            : null;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
