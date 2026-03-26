<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SidePost extends Model
{
    protected $table = 'sideposts';
   
    protected $fillable = [
    'title1', 'description1', 'url1',
    'title2', 'description2', 'url2',
    'desktop_image1', 'mobile_image1',
    'desktop_image2', 'mobile_image2',
];



}
