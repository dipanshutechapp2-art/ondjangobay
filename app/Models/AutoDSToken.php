<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoDSToken extends Model
{
    protected $table = 'autods_tokens';

    protected $fillable = [
        'user_id',
        'id_token',
        'access_token',
        'refresh_token',
        'expires_in',
        'expires_at',
        'store_id',
        'store_name',
    ];

    public $timestamps = true;
}
