<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User; 

class OtpController extends Controller
{
    public function showOtpForm()
    {
        return view('auth.verify-otp');
    }
	
	 public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $userId = session('otp_user_id');
        if (!$userId) {
            return redirect()->route('login')->withErrors(['otp' => 'Session expired, please login again']);
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('login')->withErrors(['otp' => 'User not found']);
        }

        if ($user->login_otp !== $request->otp) {
            throw ValidationException::withMessages([
                'otp' => 'Invalid OTP entered.',
            ]);
        }

        if ($user->otp_expires_at < now()) {
            return redirect()->route('login')->withErrors(['otp' => 'OTP expired, please login again']);
        }


        Auth::login($user);
		
		#UPDATE CART
		update_cart_item_after_login();
		
        $user->login_otp = null;
        $user->otp_expires_at = null;
        $user->save();


        $request->session()->forget('otp_user_id');

		return redirect()->intended($this->getRedirectPath());
    }
	
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
}
