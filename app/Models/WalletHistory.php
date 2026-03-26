<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletHistory extends Model
{
    protected $fillable = [
        'wallet_id',
        'user_id',
        'amount',
        'type',
        'method',
        'transaction_id',
        'status',
        'old_balance',
        'new_balance',
        'remarks',
        'currency',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

