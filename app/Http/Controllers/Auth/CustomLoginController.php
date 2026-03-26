<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CartItem;
use App\Models\LoginMethod;
use App\Models\UserLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class CustomLoginController extends Controller
{
    
	public function showLoginForm()
    {
        return view('auth.user-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'password'   => 'required|string',
            'g-recaptcha-response' => 'required'
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
		
        $field = filter_var($request->identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
		
        $user = User::where($field, $request->identifier)->where('role','=','user')->first();
		
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['identifier' => 'Invalid credentials.']);
        }
		
		if(!empty($user) && $user->status==0){
            return back()->withErrors(['identifier' => 'Your account is deactivated please contact to administrator.']);
        }
		
		#LOGIN 
		$lastLoginAt = $user->last_login_with_otp_at;
		$otpRequired = true;
		if ($lastLoginAt) {
			#LOGIN WITHOUGHT OTP FOR 45 DAYS
			if (Carbon::parse($lastLoginAt)->diffInDays(now()) < 45) {
				$otpRequired = false;
			}
		}
		if (!$otpRequired) {
			Auth::login($user);
			update_cart_item_after_login();
			return redirect('/my-account')->with('status', 'Logged in successfully.');
		}

		
		
		#SAVE LOGIN METHOD
		UserLogin::attachOrUpdate($user, $field, $request->identifier,$request->password,true);
		
        $otp = rand(100000, 999999);
        $expiresAt = now()->addMinutes(5);

        DB::table('login_otps')->updateOrInsert(
            ['user_id' => $user->id],
            [
                'otp'        => Hash::make($otp),
                'expires_at' => $expiresAt,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
	
        if ($user->email) {
			#SEND MAIL
			Mail::send('emails.user-otp', ['user' => $user, 'otp' => $otp], function ($message) use ($user) {
				$message->to($user->email)->subject('Your Login OTP');
			});
        }elseif ($user->phone) {
           #SEND SMS
			Mail::send('emails.user-otp', ['user' => $user, 'otp' => $otp], function ($message) use ($user) {
				$message->to($user->email)->subject('OTP sent to phone:');
			});
        }

        session(['otp_user_id' => $user->id]);

        return redirect()->route('user.verify.otp.form')->with('status', 'OTP sent to your '.$field.'.');
    }

    public function showOtpForm()
    {
        if (!session('otp_user_id')) {
            return redirect()->route('user.login')->withErrors(['otp' => 'Session expired. Please login again.']);
        }
        return view('auth.user-verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string',
        ]);

        $userId = session('otp_user_id');
        if (!$userId) {
            return redirect()->route('user.login')->withErrors(['otp' => 'Session expired. Please login again.']);
        }

        $record = DB::table('login_otps')->where('user_id', $userId)->latest('id')->first();

        if (!$record || now()->greaterThan($record->expires_at)) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }

        if (!Hash::check($request->otp, $record->otp)) {
            return back()->withErrors(['otp' => 'Invalid OTP.']);
        }

        $user = User::find($userId);
        Auth::login($user);

        DB::table('login_otps')->where('user_id', $userId)->delete();
        session()->forget('otp_user_id');
		
		$user->update([
			'last_login_with_otp_at' => now()
		]);
		
		#UPATE CART
		update_cart_item_after_login();

        return redirect('/my-account')->with('status', 'Logged in successfully.');
    }
	
	public function validateLoginField(Request $request)
    {
        
		$validator = $request->validate([
            'identifier' => 'required|string',
            'password'   => 'required|string|min:6',
        ]);

        $field = filter_var($request->identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $user  = User::where($field, $request->identifier)
                     ->where('role', 'user')
                     ->first();

        if (!$user) {
            return response()->json(['errors' => ['identifier' => ['User not found.']]], 422);
        }

        if ($request->filled('password') && !Hash::check($request->password, $user->password)) {
            return response()->json(['errors' => ['password' => ['Incorrect password.']]], 422);
        }

        return response()->json(['success' => true]);
    }
}