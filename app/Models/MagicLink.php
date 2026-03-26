<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MagicLink extends Model
{
    protected $fillable = ['user_id','token','expires_at','used','request_ip','user_agent'];

    protected $casts = [
        'expires_at' => 'datetime',
        'used' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isValid(): bool
    {
        return ! $this->used && $this->expires_at && $this->expires_at->isFuture();
    }
}