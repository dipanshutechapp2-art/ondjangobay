<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'user_id',
        'rating',
        'review',
        'author',
        'email',
    ];

    public function store()
    {
        return $this->belongsTo(VendorStore::class,'store_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    
}
