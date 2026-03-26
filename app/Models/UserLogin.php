<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserLogin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'login_method_id',
        'identifier',
        'secret',
        'is_primary',
    ];

    protected $hidden = ['secret'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function method()
    {
        return $this->belongsTo(LoginMethod::class, 'login_method_id');
    }

    public static function attachOrUpdate($user, string $methodCode, string $identifier, ?string $secret = null, bool $isPrimary = false)
    {
        $method = LoginMethod::where('code', $methodCode)->firstOrFail();
		
		if ($isPrimary) {
			static::where('user_id', $user->id)->update(['is_primary' => false]);
		}
		
        return static::updateOrCreate(
            [
                'user_id'         => $user->id,
                'login_method_id' => $method->id,
                'identifier'      => $identifier,
            ],
            [
                'secret'     => $secret ? bcrypt($secret) : null,
                'is_primary' => $isPrimary,
            ]
        );
    }

    public static function hasLoginMethod($user, string $methodCode): bool
    {
        return $user->logins()
            ->whereHas('method', fn ($q) => $q->where('code', $methodCode))
            ->exists();
    }
}
