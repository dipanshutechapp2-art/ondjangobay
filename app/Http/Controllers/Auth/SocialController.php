<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SocialController extends Controller
{
    // Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user();
     
        $user = $this->findOrCreateUser($googleUser, 'google');

        Auth::login($user);
		
		#UPDATE CART
		update_cart_item_after_login();
		
        return redirect()->intended('/my-account');
    }

    // Facebook
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        $facebookUser = Socialite::driver('facebook')->user();

        $user = $this->findOrCreateUser($facebookUser, 'facebook');

        Auth::login($user);
		
		#UPDATE CART
		update_cart_item_after_login();
		
        return redirect()->intended('/my-account');
    }

    protected function findOrCreateUser($providerUser, $provider)
    {
        $user = User::where('provider_id', $providerUser->getId())
            ->where('provider', $provider)
            ->first();

        if (!$user) {
            $user = User::create([
                'name'         => $providerUser->getName(),
                'email'        => $providerUser->getEmail(),
                'provider'     => $provider,
                'provider_id'  => $providerUser->getId(),
                'password'     => bcrypt(Str::random(16)),
            ]);
        }

        return $user;
    }
}