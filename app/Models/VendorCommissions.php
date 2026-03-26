<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorCommissions extends Model {
    use HasFactory;

    protected $fillable = [
        'name', 'category_code', 'commission_type', 'commission_value', 'bank_account','vendor_id'
    ];

    public function products() {
        return $this->hasMany(Product::class,'product_id');
    }

    public function transactions() {
        return $this->hasMany(Transaction::class,'vendor_id');
    }
}

