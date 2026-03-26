<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    protected $table = 'blog_category';
    protected $fillable = ['name', 'slug', 'status', 'image'];

    public function posts()
    {
        return $this->hasMany(Blog::class, 'category_id');
    }
}
