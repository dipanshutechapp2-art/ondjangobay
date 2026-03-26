<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = ['code', 'symbol', 'rate', 'is_default'];

    public static function getDefaultCurrency()
    {
        return self::where('is_default', true)->first();
    }

    public static function getSelectedCurrency()
    {
        $currencyId = session('currency_id');
        return $currencyId ? self::find($currencyId) : self::getDefaultCurrency();
    }
}
