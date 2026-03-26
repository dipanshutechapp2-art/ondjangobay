<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MagicLink; 
use App\Notifications\SendMagicLink;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Auth;

class MagicLinkController extends Controller
{
    public function magicLink()
    {
         return view('magic-link');
    }
	public function request(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            throw ValidationException::withMessages(['email' => 'No user found for this email.']);
        }

        $token = hash('sha256', Str::random(40) . now());
        $magic = MagicLink::create([
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => Carbon::now()->addMinutes(15),
            'request_ip' => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
        ]);

        $signedUrl = URL::temporarySignedRoute(
            'magic.login.web', 
            $magic->expires_at,
            ['user' => $user->id, 'token' => $magic->token]
        );

        $deepLink = "myapp://magic-login?token={$magic->token}";


        Notification::route('mail', $user->email)
            ->notify(new SendMagicLink($user, $signedUrl, $deepLink));

        //return response()->json(['message' => 'Magic link sent if the email exists.']);
		 return redirect()->back()->with(['success' => 'Magic link sent if the email exists.']);
    }
	
	public function handleWeb(Request $request)
    {  
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired link.');
        }
	
        $userId = $request->query('user');
        $token = $request->query('token');
		
        $magic = MagicLink::where('user_id', $userId)->where('token', $token)->first();
		
        if (! $magic || ! $magic->isValid()) {
            return redirect()->route('login')->withErrors(['magic' => 'This magic link is invalid or expired.']);
        }

        $magic->used = true;
        $magic->save();

        $user = User::findOrFail($userId);
        Auth::login($user, true);
		
		#UPDATE CART
		update_cart_item_after_login();
		
        return redirect('/my-account')->with(['success' => 'You have successfully logged in..']);
       // return redirect()->intended('/my-account');
    }
    
    public function requestApi(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            throw ValidationException::withMessages(['email' => 'No user found for this email.']);
        }

        $token = hash('sha256', Str::random(40) . now());

        $magic = MagicLink::create([
            'user_id'    => $user->id,
            'token'      => $token,
            'expires_at' => Carbon::now()->addMinutes(15),
            'request_ip' => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
        ]);

        $signedUrl = URL::temporarySignedRoute(
            'magic.login.web',
            $magic->expires_at,
            ['user' => $user->id, 'token' => $magic->token]
        );

        // API link (for mobile apps)
        $apiUrl = url("/api/magic-login?user={$user->id}&token={$magic->token}");

        // Deep link for Android/iOS app
        $deepLink = "myapp://magic-login?user={$user->id}&token={$magic->token}";

        Notification::route('mail', $user->email)
            ->notify(new SendMagicLink($user, $signedUrl, $deepLink, $apiUrl));

        return response()->json(['message' => 'Magic link sent if the email exists.']);
    }
    
    public function handleApi(Request $request)
    {
        $userId = $request->query('user');
        $token  = $request->query('token');

        $magic = MagicLink::where('user_id', $userId)
            ->where('token', $token)
            ->first();

        if (! $magic || ! $magic->isValid()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'This magic link is invalid or expired.'
            ], 401);
        }

        $magic->used = true;
        $magic->save();

        $user = User::findOrFail($userId);

        $apiToken = $user->createToken('MagicLinkLogin')->plainTextToken;
		
        return response()->json([
            'status'  => 'success',
            'message' => 'Login successful.',
            'token'   => $apiToken,
            'user'    => $user
        ]);
    }
}
