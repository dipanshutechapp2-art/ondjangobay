<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = ['user_id', 'balance'];

    public function histories()
    {
        return $this->hasMany(WalletHistory::class);
    }
	
	public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}

