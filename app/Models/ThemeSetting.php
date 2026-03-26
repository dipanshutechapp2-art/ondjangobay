<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThemeSetting  extends Model
{
    protected $table = 'theme_setting';

    protected $fillable = [
        'site_name',
        'favicon',
        'header_logo',
        'footer_logo',
        'address',
        'email',
        'primary_phone',
        'alt_phone',
        'copyright',
        'header_menu',
        'footer_menu1',
        'footer_menu2',
        'google_analytics',
        'currency',
        'currency_position',
        'meta_tags',
        'meta_description',
        'country'
    ];
 
}
