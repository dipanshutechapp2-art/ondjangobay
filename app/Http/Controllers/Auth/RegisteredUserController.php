<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserOtp;
use App\Models\CartItem;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
			'phone' => 'required|unique:users,phone',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
			'g-recaptcha-response' => 'required',
        ]);
		
		 // Verify reCAPTCHA
        $recaptchaResponse = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip()
        ])->json();

        if (!$recaptchaResponse['success']) {
            return back()->withErrors(['g-recaptcha-response' => 'reCAPTCHA verification failed.'])->withInput();
        }
		
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));
		
		#OTP SEND FOR VERIFY EMAIL OR PHONE
		$emailOtp = rand(100000, 999999);
		UserOtp::create([
			'user_id'     => $user->id,
			'otp'         => $emailOtp,
			'expires_at'  => now()->addMinutes(10),
		]);
		session(['pending_user_id' => $user->id]);
		#SEND MAIL
		Mail::send('emails.user-otp', ['user' => $user, 'otp' => $emailOtp], function ($message) use ($user) {
			$message->to($user->email)->subject('Your Email OTP');
		});
		
		#SEND PHONE
		#TWILIO SMS GATEWAY ETC
		
		
		#SEND SIGNUP MAIL
		/* Mail::send('emails.user-signup', ['user' => $user], function ($message) use ($user) {
			$message->to([$user->email,get_admin_email()])
					->subject('Welcome to ' . config('app.name') . ' – Let’s Get Started!');
		});
		
        Auth::login($user);
		
		if (session()->has('cart')) {
			$sessionCart = session('cart');
			foreach ($sessionCart as $item) {
				CartItem::updateOrCreate([
					'session_id'    => session()->getId() ?? null,
					'user_id'       => Auth::id(),
					'product_id'    => $item['product_id'],
					'variants'      => $item['variants'],
				], [
					'price'    => $item['price'],
					'quantity' => \DB::raw("quantity + {$item['quantity']}"),
				]);
			}

			session()->forget('cart');
		} */

        //return redirect(route('dashboard', absolute: false));
        //return redirect(route('account.my_account', absolute: false));
		return redirect()->route('user.userShowOtpForm')->with('success', 'Your otp has been send on your entered mail!');
    }
	
	public function userShowOtpForm(Request $request){
		
		return view('auth.userVerifyOtp');
	}
	
	public function userVerifyOtp(Request $request){
	  
		$request->validate(['otp' => 'required|numeric']);
	  
		$userId = session('pending_user_id');
	
		if (!$userId) {
		  return back()->withErrors(['otp' => 'Session expired, please register again.']);
		}
		
		$otpRecord = UserOtp::where('user_id', $userId)
        ->where('otp', $request->otp)
        ->where('expires_at', '>', now())
        ->first();

		if (!$otpRecord) {
			return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
		}

		$user = User::find($userId);
		$user->email_verified_at = now();
		$user->is_phone_verified = '1';
		$user->save();

		$otpRecord->delete();
		session()->forget('pending_user_id');
		
		#SEND SIGNUP MAIL
		Mail::send('emails.user-signup', ['user' => $user], function ($message) use ($user) {
			$message->to([$user->email,get_admin_email()])
					->subject('Welcome to ' . config('app.name') . ' – Let’s Get Started!');
		});
		

		return redirect()->route('user.login')->with('success', 'Your account is verified!');
	}
}
