<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginMethod extends Model
{
    protected $fillable = ['name', 'code', 'is_active'];

    public function userLogins()
    {
        return $this->hasMany(UserLogin::class);
    }
}

