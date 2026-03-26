<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserLogin;
use App\Models\User;
use App\Models\LoginMethod;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Factory;

class SocialLinkController extends Controller
{
    public function linkAccount(Request $request)
    {
        $request->validate([
            'provider_code' => 'required|string|in:google,facebook,apple',
            'identifier'    => 'required|string',
            'secret'        => 'required|string',
            'email'         => 'required|string',
        ]);
		
        $user = Auth::user(); 
        if (!$user) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

		$userExist= User::where('id',$user->id)->where('email',$request->email)->first();
		if(!$userExist){
			return response()->json(['message' => 'This is email does not exits.'], 401);
		}
		
        try {
            $firebase = (new Factory)
                ->withServiceAccount(storage_path('app/firebase/firebase_credentials.json'));
            $firebaseAuth = $firebase->createAuth();

            $verifiedIdToken = $firebaseAuth->verifyIdToken($request->secret);

        } catch (\Kreait\Firebase\Exception\Auth\FailedToVerifyToken $e) {
            return response()->json(['error' => 'Invalid Firebase token.'], 403);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        if ($verifiedIdToken->claims()->get('sub') !== $request->identifier) {
            return response()->json(['error' => 'Firebase UID does not match token.'], 403);
        }

        $loginMethod = LoginMethod::where('code', $request->provider_code)->firstOrFail();
		
		
		UserLogin::where('user_id', $user->id)->update(['is_primary' => false]);

        $userLogin = UserLogin::updateOrCreate(
            [
                'user_id' => $user->id,
                'login_method_id' => $loginMethod->id,
            ],
            [
                'identifier' => $request->identifier,
                'secret'     => $request->secret,
                'is_primary' => true,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => ucfirst($loginMethod->name) . ' account linked successfully.',
            'linked' => $userLogin
        ]);
    }
}
