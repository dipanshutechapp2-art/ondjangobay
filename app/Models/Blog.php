<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $table = 'blogs';
    protected $fillable = ['title', 'slug', 'category_id', 'photo', 'description', 'tags','source', 'meta_tags', 'meta_description', 'status','views'];

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

}
