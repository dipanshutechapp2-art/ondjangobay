<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserDevice;
use App\Models\User;
use App\Models\UserLogin;

class BiometricController extends Controller
{

    public function enable(Request $request)
    {
        $request->validate([
            'device_uuid' => 'required|string',
            'device_name' => 'required|string',
        ]);

        $device = UserDevice::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'device_uuid' => $request->device_uuid,
            ],
            [
                'device_name' => $request->device_name,
                'biometric_enabled' => true,
            ]
        );
        
        #SAVE LOGIN METHOD
        $user     = auth()->user();
	    UserLogin::attachOrUpdate($user, 'biometric', $request->device_uuid,$request->device_uuid,true);
        
        return response()->json([
            'success' => true,
            'message' => 'Biometric login enabled for this device',
            'device' => $device
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'device_uuid' => 'required|string',
        ]);

        $device = UserDevice::where('device_uuid', $request->device_uuid)
            ->where('biometric_enabled', true)
            ->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Device not registered for biometric login'
            ], 401);
        }

        $user = User::find($device->user_id);
		
		if(!empty($user) && $user->status==0){
             return response()->json(['message' => ' Your account is deactivated please contact to administrator.'], 401);
        }
		
		#SAVE LOGIN METHOD
	    UserLogin::attachOrUpdate($user, 'biometric', $request->device_uuid,$request->device_uuid,true);

        $token = $user->createToken('biometric-login')->plainTextToken;

        return response()->json([
            'success' => true,
            'token'   => $token,
            'user'    => $user,
            
        ]);
    }
}
