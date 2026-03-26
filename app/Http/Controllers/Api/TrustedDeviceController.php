<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrustedDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TrustedDeviceController extends Controller
{
    public function registerDevice(Request $request)
    {
        $request->validate([
            'device_name' => 'required|string',
        ]);

        $deviceToken = Str::uuid()->toString();

        TrustedDevice::updateOrCreate(
            ['user_id' => auth()->id(), 'device_name' => $request->device_name],
            [
                'device_token' => $deviceToken,
                'is_trusted'   => true,
                'last_used_at' => Carbon::now(),
                'expires_at'   => Carbon::now()->addMonths(6),
            ]
        );
        
        return response()->json([
            'status'           => true,
            'message'          => 'Device registered as trusted.',
            'device_token'     => $deviceToken,
         ], 200);
    }

    public function checkDevice(Request $request)
    {
        $request->validate([
            'device_token' => 'required|string',
        ]);

        $device = TrustedDevice::where('device_token', $request->device_token)
                    ->where('user_id', auth()->id())
                    ->first();

        if ($device && $device->is_trusted && $device->expires_at > Carbon::now()) {
            $device->update(['last_used_at' => Carbon::now()]);
            
            return response()->json([
                //'status'           => true,
                'trusted'          => true,
             ], 200);
            
        }
        return response()->json([
            //'status'           => false,
            'trusted'          => false,
        ], 401);
            
    }
}
