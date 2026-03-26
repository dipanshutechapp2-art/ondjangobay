<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sliders extends Model
{
    protected $table ='sliders';
	
    protected $fillable = ['title', 'url', 'status','description','desktop_image','mobile_image'];
	

}
