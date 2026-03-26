<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    protected $fillable = [
        'user_id',
        'device_name',
        'device_uuid',
        'biometric_enabled',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
