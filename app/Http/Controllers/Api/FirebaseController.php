<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notification;
use Kreait\Firebase\Factory;

class FirebaseController extends Controller
{
 
    public function saveDeviceToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = User::findOrFail(auth()->id());
        $user->device_token = $request->fcm_token;
        $user->fcm_token    = $request->fcm_token;
        $user->save();

        return response()->json(['success' => true, 'message' => 'Device token saved.']);
    }

    public function sendNotification(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        $user = User::findOrFail(auth()->id());

        if (!$user->device_token) {
            return response()->json(['success' => false, 'message' => 'User has no device token']);
        }

        $notification = Notification::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'body' => $request->body,
        ]);

        $messaging = (new Factory)
            ->withServiceAccount(config('firebase.credentials'))
            ->createMessaging();

        $message = [
            'token' => $user->fcm_token,
            'notification' => [
                'title' => $request->title,
                'body' => $request->body,
            ],
            'data' => [
                'notification_id' => (string) $notification->id,
            ]
        ];

        $messaging->send($message);

        return response()->json(['success' => true, 'message' => 'Notification sent', 'notification' => $notification]);
    }

    public function getNotifications(Request $request)
    {
        $perPage = $request->input('limit', 10); 
    
        $notifications = Notification::where('user_id', auth()->id())->orderBy('id', 'desc')->paginate($perPage);
        
        $unreadCount = Notification::where('user_id', auth()->id())
                               ->where('is_read', false)
                               ->count();
    
        return response()->json([
            'status'        => true,
            'total_unread'  => $unreadCount,
            'data'          => $notifications
        ], 200);
    }


    public function markAsRead(Request $request)
    {
        $userId = auth()->id();
       Notification::where('user_id', $userId)->update(['is_read' => true]);
        
        return response()->json([
            'status'  => true,
            'message'    => 'You have read all notifications successfully.'
        ],200);
    }
    
    public function deleteNotification(Request $request)
    {
        $request->validate([
            'notification_id' => 'required|integer|exists:notifications,id',
        ]);
        
       $userId = auth()->id();
       Notification::where('user_id', $userId)->where('id',$request->notification_id)->delete();
        
        return response()->json([
            'status'  => true,
            'message' => 'You have deleted successfully.'
        ],200);
    }

    /*public function verifyIdToken(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        $auth = (new Factory)
            ->withServiceAccount(config('firebase.credentials'))
            ->createAuth();

        try {
            $verifiedToken = $auth->verifyIdToken($request->id_token);
            $uid = $verifiedToken->claims()->get('sub');

            return response()->json(['success' => true, 'uid' => $uid]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Invalid ID token'], 401);
        }
    }*/
}
