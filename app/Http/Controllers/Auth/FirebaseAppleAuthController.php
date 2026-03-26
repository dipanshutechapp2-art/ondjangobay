<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Kreait\Firebase\Auth as FirebaseAuth;

class FirebaseController extends Controller
{
    protected $firebaseAuth;

    public function __construct(FirebaseAuth $firebaseAuth)
    {
        $this->firebaseAuth = $firebaseAuth;
    }

    public function appleSignIn(Request $request)
    {  
        $request->validate([
            'idToken' => 'required|string',
        ]);

        try {
            $verifiedIdToken = $this->firebaseAuth->verifyIdToken($request->idToken);

            $uid = $verifiedIdToken->claims()->get('sub');
            $email = $verifiedIdToken->claims()->get('email');
            $name  = $verifiedIdToken->claims()->get('name') ?? explode('@', $email)[0];

            if (!$email) {
                return response()->json([
                    'status' => false,
                    'message' => 'No email returned from Apple.'
                ], 422);
            }

            $user = User::firstOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => bcrypt(str()->random(12))]
            );

            Auth::login($user);
			
			#UPDATE CART
			update_cart_item_after_login();
			
            return response()->json([
                'status' => true,
                'redirect' => url('/my-account')
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
