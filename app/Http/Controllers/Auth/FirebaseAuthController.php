<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Models\User;
use App\Models\CartItem;
use App\Models\UserLogin;

class FirebaseAuthController extends Controller
{
    public function __construct(protected FirebaseService $firebaseService) {}

    public function login(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        try {
            $verifiedIdToken = $this->firebaseService->verifyIdToken($request->token);
            $uid = $verifiedIdToken->claims()->get('sub');

            $firebaseUser = $this->firebaseService->getUser($uid);
            $email = $firebaseUser->email ?? null;

            if (!$email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email not available from Firebase user.',
                ], 422);
            }

            $signInProvider = $verifiedIdToken->claims()->get('firebase')['sign_in_provider'] ?? '';
            $providerName = in_array('facebook', explode('.', $signInProvider)) ? 'facebook' : (in_array('google', explode('.', $signInProvider)) ? 'google' : '');
			
			
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'         => $firebaseUser->displayName ?? $email,
                    'display_name' => $firebaseUser->displayName ?? $email,
                    'provider'     => $providerName,
                    'provider_id'  => $signInProvider,
                    'password'     => bcrypt(str()->random(16)),
                ]
            );

            $profileImg = $this->uploadProfilePicFromUrl($firebaseUser->photoUrl ?? null, $user);
            $user->image = $profileImg ?? '';
            $user->provider = $providerName;
            $user->provider_id = $signInProvider;
            $user->save();
			
			#SAVE LOGIN METHOD
			UserLogin::attachOrUpdate($user, $providerName, $uid,$providerName,true);
			
			if(!empty($user) && $user->status=='0'){
			   return response()->json([
					'error'   => false,
					'message' => 'Your account is deactivated please contact to administrator.',
				]);
			}else{
			   Auth::login($user);
			}
			
			#UPDATE CART
			update_cart_item_after_login();

            return response()->json([
                'success' => true,
                'message' => 'User logged in successfully.',
                'user_id' => $user->id,
                'user' => $user,
            ]);

        } catch (\Kreait\Firebase\Exception\Auth\FailedToVerifyToken $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Firebase token.',
                'error' => $e->getMessage()
            ], 401);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function uploadProfilePicFromUrl($url, $user)
    {
        if (!$url) return null;

        try {
            if ($user->image && File::exists(public_path('/uploads/users/'.$user->image))) {
                File::delete(public_path('/uploads/users/'.$user->image));
            }

            $contents = file_get_contents($url);
            $filename = time().'.png';
            $path = public_path('uploads/users/' . $filename);

            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            file_put_contents($path, $contents);

            return $filename;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
