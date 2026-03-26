<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class WebPasswordResetController extends Controller
{

    public function showForgotForm()
    {
        return view('auth.user-forgot-password');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
        ]);

        $type = filter_var($request->identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $user = User::where($type, $request->identifier)->first();
	
        if (!$user) {
            return back()->withErrors(['identifier' => 'User not found']);
        }

        $otp = rand(100000, 999999);
        $expiresAt = now()->addMinutes(5);

        DB::table('password_reset_otps')
            ->where('type', $type)
            ->where($type, $request->identifier)
            ->delete();

        DB::table('password_reset_otps')->insert([
            'email'      => $type === 'email' ? $request->identifier : null,
            'phone'      => $type === 'phone' ? $request->identifier : null,
            'type'       => $type,
            'otp'        => Hash::make($otp),
            'expires_at' => $expiresAt,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($type === 'email') {
            /* Mail::raw("Your OTP is: $otp", function ($message) use ($request) {
                $message->to($request->identifier)
                        ->subject('Password Reset OTP');
            }); */
			
			#SEND MAIL
			Mail::send('emails.user-otp', ['user' => $user, 'otp' => $otp], function ($message) use ($user,$request) {
				$message->to($request->identifier)->subject('Your OTP');
			});
			
        } else {
            // Send SMS via your provider
			
			#SEND MAIL
			Mail::send('emails.user-otp', ['user' => $user, 'otp' => $otp], function ($message) use ($user,$request) {
				$message->to(get_admin_email())->subject('Your Phone number OTP');
			});
        }

        //return back()->with('status', "OTP sent to your $type.");
         return redirect()->route('user.password.reset')->with('status', 'OTP sent to your '.$type.'.');
    }

    public function showResetForm()
    {
        return view('auth.user-reset-password');
    }


    public function resetPassword(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'otp'        => 'required|string',
            'password'   => 'required|string|min:6|confirmed',
        ]);

        $type = filter_var($request->identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $record = DB::table('password_reset_otps')
            ->where('type', $type)
            ->where($type, $request->identifier)
            ->latest('id')
            ->first();

        if (!$record) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }

        if (now()->greaterThan($record->expires_at)) {
            return back()->withErrors(['otp' => 'OTP has expired.']);
        }

        if (!Hash::check($request->otp, $record->otp)) {
            return back()->withErrors(['otp' => 'Invalid OTP.']);
        }

        $user = User::where($type, $request->identifier)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_otps')
            ->where('type', $type)
            ->where($type, $request->identifier)
            ->delete();

        return redirect()->route('user.login')->with('status', 'Password reset successfully.');
    }
}