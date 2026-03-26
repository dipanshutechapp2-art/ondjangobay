<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'provider',
        'provider_id',
        'gender',
        'dob',
        'image',
        'currency_code',
        'device_token',
        'last_login_with_otp_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
	
	public function vendorStore() {
		
		return $this->hasOne(\App\Models\VendorStore::class);
	}
	
	public function notifications()
	{
		return $this->hasMany(Notification::class)->orderBy('id', 'desc');
	}
	
	public function devices()
	{
		return $this->hasMany(UserDevice::class);
	}
	
	public function trustedDevices()
	{
		return $this->hasMany(TrustedDevice::class);
	}
	
	public function logins()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function primaryLogin()
    {
        return $this->hasOne(UserLogin::class)->where('is_primary', true);
    }
	
	public function wallet()
	{
		return $this->hasOne(Wallet::class);
	}

	public function walletHistories()
	{
		return $this->hasMany(WalletHistory::class);
	}
}

