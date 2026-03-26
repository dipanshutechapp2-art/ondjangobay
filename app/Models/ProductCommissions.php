<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCommissions extends Model {
    use HasFactory;

    protected $fillable = [
        'vendor_id', 'name', 'commission_type', 'commission_value', 'origin', 'shipping_condition', 'product_id'
    ];

    public function vendor() {
        return $this->belongsTo(User::class,'vendor_id');
    }

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }
}

