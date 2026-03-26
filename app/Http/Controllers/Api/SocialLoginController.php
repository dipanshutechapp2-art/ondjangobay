<?php
namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Http;

class SocialLoginController extends Controller
{

    public function loginWithGoogle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_token' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $googleResponse = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $request->id_token,
        ]);
       
        if (!$googleResponse->ok() || !$googleResponse->json('email_verified')) {
            return response()->json(['error' => 'Invalid Google token'], 401);
        }
      
        $email   = $googleResponse->json('email');
        $name    = $googleResponse->json('name');
        $picture  = $googleResponse->json('picture');

        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => bcrypt(Str::random(16))]
        );
        
        $profileImg = $this->uploadProfilePicFromUrl($picture ?? null, $user);
        $user->image = $profileImg ?? '';
        $user->save();
        
        if(!empty($user) && $user->status==0){
             return response()->json(['message' => ' Your account is deactivated please contact to administrator.'], 401);
        }
        
        #SAVE LOGIN METHOD
	    UserLogin::attachOrUpdate($user, 'google', $email,'google',true);
        
        $token = $user->createToken('mobile_app')->plainTextToken;
    
        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
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

    public function loginWithFacebook(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'access_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $fbResponse = Http::get('https://graph.facebook.com/me', [
            'fields'       => 'id,name,email',
            'access_token' => $request->access_token,
        ]);

        if (!$fbResponse->ok() || !$fbResponse->json('email')) {
            return response()->json(['error' => 'Invalid Facebook token'], 401);
        }

        $email = $fbResponse->json('email');
        $name = $fbResponse->json('name');

        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => bcrypt(Str::random(16))]
        );
        
        if(!empty($user) && $user->status==0){
             return response()->json(['message' => ' Your account is deactivated please contact to administrator.'], 401);
        }
        
        #SAVE LOGIN METHOD
	    UserLogin::attachOrUpdate($user, 'facebook', $email,'facebook',true);
        
        $token = $user->createToken('mobile_app')->plainTextToken;

        return response()->json([
            'token'  => $token,
            'user'   => $user,
        ]);
    }
}
