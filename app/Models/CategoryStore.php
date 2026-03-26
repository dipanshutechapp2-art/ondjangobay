<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryStore extends Model
{
    protected $table = 'category_store';

    protected $fillable = ['store_id', 'category_id'];

    public function store()
    {
        return $this->belongsTo(VendorStore::class, 'store_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
