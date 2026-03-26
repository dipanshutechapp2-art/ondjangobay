<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsletterSubscription;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    { 
        $validator = Validator::make($request->all(), [
			'email' => 'required|email|unique:newsletter_subscriptions,email',
		]);
		
		if ($validator->fails()) {
			return response()->json([
				'message' => 'Validation failed.',
				'errors' => $validator->errors()
			], 422);
		}
		
        NewsletterSubscription::create([
            'email' => $request->email,
        ]);
		
		$res = Mail::send('emails.NewsletterSubscribed', ['data' => $request], function ($message) use ($request) {
			$message->to([$request->email,get_admin_email()])
					->subject('Thanks for Subscribing!');
		});
		
		return response()->json(['message' => 'You have successfully subscribed to our newsletter!']);
    }
}

