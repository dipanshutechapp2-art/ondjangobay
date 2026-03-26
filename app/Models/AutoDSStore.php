<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AutoDSStore extends Model
{
    use HasFactory;

    protected $table = 'auto_ds_stores';

    protected $fillable = [
        'user_id',
        'autods_store_id',
        'name',
        'store_url',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function isActive(): bool
    {
        return (bool) $this->active;
    }
}
