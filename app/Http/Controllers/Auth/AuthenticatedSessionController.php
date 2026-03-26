<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;
use App\Models\CartItem;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use DB;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        
		#CHECK ROLE ONLY FOR ADMIN AND VENDOR LOGIN
		$user = User::where('email', $request->email)->whereIn('role',['vendor','admin'])->first();
		if (!$user) {
			auth()->logout();
			return redirect()->route('login')->with('error', 'This login can access only for admin and vendor.');
		}
		
		$request->session()->regenerate();
		
		#UPDATE CART
		//update_cart_item_after_login();

        // return redirect()->intended(route('dashboard', absolute: false));
            // Use intended method to redirect back to the intended URL
    return redirect()->intended($this->getRedirectPath());
    }
	
	
	/* public function store(LoginRequest $request): RedirectResponse
	{
		$user = User::where('email', $request->email)->first();

		if (!$user || !Hash::check($request->password, $user->password)) {
			throw ValidationException::withMessages([
				'email' => __('auth.failed'),
			]);
		}

		$otp = rand(100000, 999999);
		$user->login_otp = $otp;
		$user->otp_expires_at = now()->addMinutes(5);
		$user->save();

		#SEND LOGIN OTP MAIL
		Mail::send('emails.user-login-otp', ['user' => $user], function ($message) use ($user) {
			$message->to([$user->email,get_admin_email()])
					->subject('Your ' . config('app.name') . ' Login OTP Code');
		});
		

		session(['otp_user_id' => $user->id]);

		return redirect()->route('verify.otp.form')->with('success', 'OTP sent successfully. Please check your email.');
	} */
	

    protected function getRedirectPath(): string
	{
    switch (Auth::user()->role) {
        case 'admin':
            return 'admin/dashboard';
        case 'vendor':
            return 'vendor/dashboard';
        case 'user':
            return '/my-account';
        default:
            return '/';
    }
}

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {  
        $userId = auth()->id();
		if(!empty($userId)){
		   DB::table('sessions')->where('user_id', $userId)->delete();
		}
		
		if ($request->user()) {
            $request->user()->tokens()->delete();
        }
        
		Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        //return redirect('/login');
        return redirect('/');
    }
	
	public function checkoutLogin(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
		$user = User::where('email', $request->email)->whereIn('role',['user'])->first();
		if (!$user) {
			auth()->logout();
			return redirect()->route('login')->with('error', 'This login can access only for admin and vendor.');
		}
		
		$request->session()->regenerate();
		
		#UPDATE CART
		update_cart_item_after_login();
		
        return redirect()->intended($this->getRedirectPath());
    }
	
}
