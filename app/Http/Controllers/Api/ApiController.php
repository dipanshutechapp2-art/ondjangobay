<?php

namespace App\Http\Controllers\Api;
 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\VendorStore;
use App\Models\ProductAttribute;
use App\Models\ProductVariant;
use App\Models\Sliders;
use App\Models\SidePost;
use App\Models\Review;
use App\Models\Order;
use App\Models\Order_product;
use App\Models\Order_total;
use App\Models\Country;
use App\Models\State;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Address;
use App\Models\Wishlist;
use App\Models\Brands;
use App\Models\Currency;
use App\Models\UserOtp;
use App\Models\Coupon;
use App\Models\UserLogin;
use App\Models\LoginMethod;
use App\Models\Wallet;
use App\Models\WalletHistory;
use App\Models\PaymentGateway; 
use App\Models\NewsletterSubscription;
use App\Models\SearchHistory;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Str;
use DB;
use Illuminate\Support\Facades\Mail;
use App\Services\Wholesale2BImportService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Kreait\Firebase\Factory;
use App\Services\CjDropshippingService;
use App\Services\AutoDSService;

use App\Models\Transaction;
use App\Services\CommissionCalculationService;
use App\Models\ProductCommissions;
use App\Models\VendorCommissions;

class ApiController extends Controller
  {
    
    public function getProductByCategoryID(Request $request)
    {
        $categoryId = $request->input('category_id');
       
        if (empty($categoryId)) {
            return response()->json([
                'status' => false,
                'message' => 'Category ID is required.'
            ], 400);
        }
    
        $query = Product::with([
            'galleryImages' // we only need this for main image
        ])
        ->select('id', 'name', 'price','image') // only essential columns
        ->whereHas('categories', function ($q) use ($categoryId) {
            $q->where('categories.id', $categoryId);
        })
        ->where('status', 1);
    
        // Optional filters
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
    
        // Sorting
        switch ($request->sort) {
            case 'price-low':
                $query->orderBy('price', 'asc');
                break;
            case 'price-high':
                $query->orderBy('price', 'desc');
                break;
            case 'new-arrivals':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('id', 'desc');
        }
    
        // Pagination limit
        $limit = $request->input('limit', 10);
        $products = $query->paginate($limit);
    
        // Transform data for clean response
        $data = $products->map(function ($product) {
            return [
                'id'    => $product->id,
                'name'  => $product->name,
                'price' => $product->price,
                'image' => $product->image ?? optional($product->galleryImages->first())->image,
            ];
        }); 
    
        return response()->json([
            'status'      => true,
            'category_id' => $categoryId,
            'pagination'  => [
                'total'        => $products->total(),
                'current_page' => $products->currentPage(),
                'per_page'     => $products->perPage(),
                'last_page'    => $products->lastPage(),
            ],
            'data' => $data,
        ]);
    }
    
    public function cancel_order_action(Request $request) { 
		
	     $request->validate([
           'order_id' => 'required|integer|exists:orders,id',
           'reason'   => 'required',
        ]);
        
	    $order = Order::where('id',$request->order_id)->where('user_id', auth()->id())->first();
		$order->order_status = 'cancelled';
		$order->reason       = $request->reason;
		$order->save();
		
		#SEND ORDER MAIL
    	$orderInfo = Order::with('orderProduct.product','orderTotal')->where('id',$order->id)->first();
		$res = Mail::send('emails.order-cancel', ['order' => $orderInfo], function ($message) use ($orderInfo) {
			$message->to([$orderInfo->email,get_admin_email()])
					->subject('Your Order Status - #' . $orderInfo->order_number);
		});
		
		return response()->json([
            'success' => true,
            'message' => 'Your order has been canceled.'
        ],200);

    }
    
    public function reOrderItem(Request $request){
        
        $request->validate([
           'order_id' => 'required|integer|exists:orders,id',
        ]);
        
        $originalOrder = \App\Models\Order::with('orderProduct')->where('id', $request->order_id)->first();
        
        if (!$originalOrder) {
			return response()->json([
                'success' => false,
                'message' => 'Invalid order id..'
            ], 404);
		}

		$userId = auth()->id();

		foreach ($originalOrder->orderProduct as $orderItem) {
		    
			$product = \App\Models\Product::find($orderItem->product_id);
			
			if (!$product && $product->status != '1') {
				continue; 
			}
			$variantIds = $orderItem->variant_ids ? explode('-', $orderItem->variant_ids) : [];
			$variants = [];
		
			foreach ($variantIds as $variantId) {

				$variant = \App\Models\ProductVariant::with('attributeValue')->where('product_id',$product->id)->where('value', $variantId)->first();

				if ($variant && $variant->attributeValue && $variant->attributeValue->attribute) {
					$attributeName = strtolower($variant->attributeValue->attribute->name);
					$variants[$attributeName] = (string) $variant->value; // Store value ID
				}
			}
	
			ksort($variants);

			$existingCartItem = \App\Models\CartItem::where('user_id', $userId)
				->where('product_id', $product->id)
				->where('variants', json_encode($variants))
				->first();

			if ($existingCartItem) {
				$existingCartItem->increment('quantity', $orderItem->quantity);
			} else {

				\App\Models\CartItem::create([
					'user_id'    => $userId,
					'session_id' => null,
					'product_id' => $product->id,
					'variants'   => $variants, 
					'price'      => $orderItem->price,
					'quantity'   => $orderItem->quantity,
				]);
			}
		}
		
        return response()->json([
            'success' => true,
            'message' => 'Items added to cart from previous order.'
        ],200);
    }
    
    public function deactivateAccount(Request $request){
       
        $user = User::where('id', auth()->id())->first();
	    $user->status = '0';
	    $user->save();
	  
	    if ($request->user()) {
            $request->user()->tokens()->delete();
        }
    
        Auth::guard('web')->logout();
    
        //$request->session()->invalidate();
        //$request->session()->regenerateToken();
    
        return response()->json([
            'success' => true,
            'message' => 'Your account has been deactivated.'
        ],200);
        
    }
    
    public function deletePermanentAccount(Request $request)
    {
        $user = User::where('id', auth()->id())->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }
    
        $timestamp = now()->timestamp;
        $prefix = 'deleted_';
    
        $user->name = $prefix . \Str::slug($user->name) . "_$timestamp";
        $user->last_name = $user->last_name ? $prefix . \Str::slug($user->last_name) . "_$timestamp" : null;
        $user->display_name = $user->display_name ? $prefix . \Str::slug($user->display_name) . "_$timestamp" : null;
        $user->email = $prefix . "user_" . $user->id . "@deleted.com";
        $user->phone = null;
        $user->image = null;
        $user->gender = null;
        $user->dob = null;
        $user->email_verified_at = null;
        $user->is_phone_verified = '0';
        $user->status = '0';
        $user->login_otp = null;
        $user->otp_expires_at = null;
        $user->password = bcrypt(\Str::random(32));
        $user->provider = null;
        $user->provider_id = null;
        $user->wallet_balance = '0';
        $user->currency_code = null;
        $user->fcm_token = null;
        $user->remember_token = null;
        $user->device_token = null;
        $user->is_deleted = '1';
        $user->updated_at = now();
    
        $user->save();
    
        if ($request->user()) {
            $request->user()->tokens()->delete();
        }
    
        Auth::guard('web')->logout();
    
        return response()->json([
            'success' => true,
            'message' => 'Your account has been permanently deleted.'
        ], 200);
    }

    
    public function getUserSearchHistory(Request $request)
    {
        $perPage = $request->input('limit', 10);
        $search  = $request->input('search'); 
    
        $user_id = auth()->id();
    
        $query = SearchHistory::where('user_id', $user_id);
    
        if (!empty($search)) {
            $query->where('query', 'like', '%' . $search . '%');
        }
    
        $recentSearches = $query->orderBy('updated_at', 'desc')->paginate($perPage);
    
        return response()->json([
            'status' => true,
            'data'   => $recentSearches,
        ], 200);
    }

    
    public function addSubscribe(Request $request){
    
        $validator = Validator::make($request->all(), [
			'email' => 'required|email|unique:newsletter_subscriptions,email',
		]);
		
		if ($validator->fails()) {
			return response()->json([
				'message' => 'Validation failed.',
				'errors' => $validator->errors()
			], 422);
		}
		
        $dataInfo = NewsletterSubscription::create([
            'email' => $request->email,
        ]);
		
		$res = Mail::send('emails.NewsletterSubscribed', ['data' => $request], function ($message) use ($request) {
			$message->to([$request->email,get_admin_email()])
					->subject('Thanks for Subscribing!');
		});
		
		 return response()->json([
            'status'  =>true,
            'message' => 'You have successfully subscribed to our newsletter!',
            'data'    =>$dataInfo,
        ], 201);
    }
    
    public function getConfigCaptcha(Request $request)
    {
        $recaptchaConfig = config('services.recaptcha');
        return response()->json([
            'status'  =>true,
            'data'    =>$recaptchaConfig,
        ], 201);
    }
    
    public function validateRecaptcha(Request $request)
    {
        $request->validate([
            'g-recaptcha-response' => 'required',
        ]);
       
        $recaptchaResponse = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip()
        ])->json();
       
        if (!$recaptchaResponse['success']) {
            return response()->json([
                'status' => false,
                'message' => 'reCAPTCHA verification failed.',
                'errors' => ['g-recaptcha-response' => ['reCAPTCHA verification failed.']]
            ], 422);
        }
        
        // If reCAPTCHA verification is successful
        return response()->json([
            'status' => true,
            'message' => 'reCAPTCHA verification successful.',
            'data' => [
                'recaptcha_response' => $recaptchaResponse,
                'timestamp' => now()->toISOString()
            ]
        ], 200);
    }
    
   public function getProductsByCategory(Request $request)
    {
        $perPage = $request->input('limit', 10);
        
        $query = Product::with([
            'categories',
            'attributes',
            'productAttributes.attribute',
            'productAttributes.variants.attributeValue',
            'stores.user',
            'galleryImages',
            'brand',
            'reviews'
        ]);
    
        if ($request->filled('search')) {
            $q = $request->query('search');
            $query->where('name', 'like', "%{$q}%");
        }
    

        if ($request->filled('category_id')) {
            $categoryIds = is_array($request->category_id)
                ? $request->category_id
                : explode(',', $request->category_id);
    
            $query->whereHas('categories', function ($cat) use ($categoryIds) {
                $cat->whereIn('categories.id', $categoryIds);
            });
        }
    
        $query->orderBy('created_at', 'desc');
    
        $products = $query->paginate($perPage)->withQueryString();
    
        return response()->json([
            'success'  => true,
            'products' => $products
        ]);
    }


    
    public function getPaymentGatewayList(Request $request){
        
        $paymentGatewayList = PaymentGateway::where('status',true)->get();
        
        return response()->json([
            'status'  =>true,
            'data'    =>$paymentGatewayList,
        ], 201);
    }
    
    public function updateCurrency(Request $request){
        
        $user_id = auth()->user()->id;
        
        $request->validate([
            'currency_code' => 'required|string|exists:currencies,code',
        ]);
        
        $user =  User::where('id',$user_id)->first();
        $user->currency_code = $request->currency_code;
        $user->save();
        
        #SEND NOTIFICATIONS
        sendFirebaseNotification($user_id,'Currency Updated','Currency updated successfully.');

        return response()->json([
            'status'  =>true,
            'message' => 'Currency updated successfully',
            'user'    =>$user,
        ], 201);
       
    }
    
    public function testing(){
        return response([
        'status'=>'success'
        ]);
    }
    
   public function register(Request $request)
      {
         $validator = Validator::make($request->all(), [
             'role' => 'required',
             'phone' => 'required|integer|unique:users,phone',
             'name' => 'required',
             'email' => 'required|email|unique:users,email',
             'password' => 'required',
         ]);
 
         if ($validator->fails()) {
             return response()->json(['errors' => $validator->errors()], 422);
         }
 
        $user = User::create([
            'name'  => $request->name,
            'role'  => $request->role,
            'email' => $request->email,
            'phone'  => $request->phone,
            'password' => Hash::make($request->password),
        ]);
        
        #Generate OTP
        $otp = rand(100000, 999999);
    
        UserOtp::create([
            'user_id'    => $user->id,
            'otp'        => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);
      
        Mail::send('emails.user-otp', ['user' => $user, 'otp' => $otp], function ($message) use ($user) {
            $message->to([$user->email])
                    ->subject('Your OTP Code – ' . config('app.name'));
        });
        
        #SEND SIGNUP MAIL
		Mail::send('emails.user-signup', ['user' => $user], function ($message) use ($user) {
			$message->to([$user->email,get_admin_email()])
					->subject('Welcome to ' . config('app.name') . ' – Let’s Get Started!');
		});
        
        if(!empty($user->email_verified_at)) {
           $user->is_email_verified  = 1;
        }else{
           $user->is_email_verified  = 0;
        }
        
        $token = $user->createToken('auth_token')->plainTextToken;
         
        $users = User::where('id',$user->id)->first();
         
         return response()->json([
             'status'=>true,
             'message' => 'User registered successfully. Please verify OTP sent to your email.',
             'token' => $token,
             'user'=>$users,
         ], 201);
     }
 
    public function verifyUserOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);
       
       $user = auth()->user();
    
       $user = User::where('email', $user->email)->first();
       $otpRecord = UserOtp::where('user_id', $user->id)
            ->where('otp', $request->otp)
            ->where('expires_at', '>=', now())
            ->latest()
            ->first();
    
        if (!$otpRecord) {
            return response()->json(['status' => false, 'message' => 'Invalid or expired OTP'], 400);
        }
        
        $user->email_verified_at = now();
        $user->save();
        
        $otpRecord->delete();
        
        if(!empty($user->email_verified_at)) {
           $user->is_email_verified  = 1;
        }else{
           $user->is_email_verified  = 0;
        }
        
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'status' => true,
            'message' => 'OTP verified Successfully!.',
            'token' => $token,
            'user' => $user,
        ],201);
    }
    
    public function resendOtp(Request $request)
    {
        
        $user = auth()->user();
        UserOtp::where('user_id', $user->id)->delete();
        
        #Generate OTP
        $otp = rand(100000, 999999);
    
        UserOtp::create([
            'user_id'    => $user->id,
            'otp'        => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);
      
        Mail::send('emails.user-otp', ['user' => $user, 'otp' => $otp], function ($message) use ($user) {
            $message->to([$user->email])
                    ->subject('Your OTP Code – ' . config('app.name'));
        });
       
        if(!empty($user->email_verified_at)) {
           $user->is_email_verified  = 1;
        }else{
           $user->is_email_verified  = 0;
        }
        
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'status' => true,
            'message' => 'OTP has been sent successfully to your registered email.',
            'token' => $token,
            'user' => $user,
        ],201);
    }
    
     /* public function login(Request $request)
     { 
         $validator = Validator::make($request->all(), [
            'email'        => 'required|email',
            'password'     => 'required',
			'login_method' => 'required|string|in:email,google,facebook,web,android,ios',
         ]); 
		 
		$allowedMethods   = ['email', 'google', 'facebook', 'web', 'android', 'ios'];
		$loginMethodInput = $request->input('login_method');
		if (is_numeric($loginMethodInput) && isset($allowedMethods[(int) $loginMethodInput])) {
			$request->merge([
				'login_method' => $allowedMethods[(int) $loginMethodInput]
			]);
		}
		
		
		$validator = Validator::make($request->all(), [
			'email'        => 'required|email',
			'password'     => 'required',
			'login_method' => 'nullable|string|in:' . implode(',', $allowedMethods),
		], [
			'login_method.in' => 'The selected login_method is invalid. Allowed values are: ' . implode(', ', $allowedMethods) . '.',
		]);
 
         if ($validator->fails()) {
             return response()->json(['errors' => $validator->errors()], 422);
         }
 
         $user = User::where('email', $request->email)->first();
 
         if (!$user || !Hash::check($request->password, $user->password)) {
             return response()->json(['message' => 'Invalid credentials'], 401);
         }
 
         $token = $user->createToken($request->email)->plainTextToken;
 
         return response()->json([
              'status'=>true,
             'message' => 'Login successful',
             'token' => $token,
             'user' => $user,
         ], 200);
    } */
    
    public function getLoginMethods(Request $request){
        
        $availableMethods = LoginMethod::where('is_active', 1)->get();
        
        return response()->json([
            'status'  =>true,
            'data'    =>$availableMethods,
        ], 201);
    }
    
    public function getUserLinkedAccount(Request $request){
        
        $user = Auth::user();
		$user->load('logins.method');
        
        return response()->json([
            'status'  => true,
            'data'    => $user,
        ], 201);
    }
    
    /*public function AddLinkAccount(Request $request){
        
        $request->validate([
            'login_method' => 'required|string|exists:login_methods,code',
            'identifier'   => 'required|string|max:255',
            'secret'       => 'nullable|string|max:255',
        ]);
        
        $user        = Auth::user();
        $methodCode  = $request->login_method; 
        $identifier  = $request->identifier;
        $secret      = $request->secret ?? null;

        $method = LoginMethod::where('code', $methodCode)->where('is_active', 1)->firstOrFail();

        if (UserLogin::where('user_id', $user->id)
            ->where('login_method_id', $method->id)
            ->exists()) {
                
            return response()->json([
                'status' => true,
                'message' => 'This login method is already linked.',
            ],200);
        }

        UserLogin::create([
            'user_id'          => $user->id,
            'login_method_id'  => $method->id,
            'identifier'       => $identifier,
            'secret'           => $secret ? bcrypt($secret) : null,
            'is_primary'       => false,
        ]);
        
        #SEND NOTIFICATIONS
        sendFirebaseNotification($user->id,'Link Account','This login method is linked successfully.');
        
        return response()->json([
            'status' => true,
            'message' => 'This login method is linked successfully.',
        ],200);
    }*/
    
    public function sendOtpForLoginLinking(Request $request)
    {
        $request->validate([
            'identifier'   => 'required|string',
            'secret'     => 'required|string|min:6',
            'login_method' => 'required|in:email,phone',
        ]);
    
        $user = Auth::user();
        $identifier = $request->identifier;
        $password = $request->secret;
        $loginMethod = $request->login_method;
    
        $method = LoginMethod::where('code', $loginMethod)->where('is_active', 1)->first();
        if (!$method) {
            return response()->json(['success' => false, 'message' => 'Invalid login method.'], 422);
        }
    
        if (UserLogin::where('user_id', $user->id)->where('login_method_id', $method->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'This login method is already linked.'], 422);
        }
    
        $existingUser = User::where('id',$user->id)->where($loginMethod, $identifier)->first();
        if (!$existingUser) {
            return response()->json(['success' => false, 'message' => "This {$loginMethod} is does not exists."], 422);
        }
    
        if (!$existingUser || !Hash::check($password, $user->password)) {
            return response()->json(['success' => false, 'message' => "{$loginMethod} and password combination is incorrect."], 422);
        }
    
        $otp = rand(100000, 999999);
    
        $cacheKey = "otp_{$user->id}_{$loginMethod}_{$identifier}";
        Cache::put($cacheKey, [
            'otp' => $otp,
            'identifier' => $identifier,
            'password' => $password,
            'login_method' => $loginMethod,
        ], now()->addMinutes(10));
    
        if ($loginMethod === 'email') {
            Mail::send('emails.user-otp', ['user' => $user, 'otp' => $otp], function ($message) use ($identifier) {
                $message->to($identifier)->subject('OTP Verification');
            });
        } else {
            // Implement SMS sending or fallback to email
            Mail::send('emails.user-otp', ['user' => $user, 'otp' => $otp], function ($message) use ($user) {
                $message->to($user->email)->subject('OTP for Phone Linking');
            });
        }
    
        return response()->json(['success' => true, 'message' => 'OTP sent successfully.']);
    }
    
    public function verifyAndStoreLoginMethod(Request $request)
    {
        $request->validate([
            'identifier'   => 'required|string',
            'login_method' => 'required|in:email,phone',
            'secret'       => 'required|string',
            'otp'          => 'required|digits:6',
        ]);
    
        $user = Auth::user();
        $identifier  = $request->identifier;
        $password    = $request->secret;
        $loginMethod = $request->login_method;
        $otp = $request->otp;
    
        $cacheKey = "otp_{$user->id}_{$loginMethod}_{$identifier}";
        $cachedData = Cache::get($cacheKey);
    
        if (!$cachedData || $cachedData['otp'] != $otp) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired OTP.'], 422);
        }
    
        if ($cachedData['password'] !== $password || $cachedData['login_method'] !== $loginMethod) {
            return response()->json(['success' => false, 'message' => 'Verification failed.'], 422);
        }
    
        $method = LoginMethod::where('code', $loginMethod)->where('is_active', 1)->firstOrFail();
    
        if (UserLogin::where('user_id', $user->id)->where('login_method_id', $method->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'This login method is already linked.'], 422);
        }
    
        UserLogin::create([
            'user_id'         => $user->id,
            'login_method_id' => $method->id,
            'identifier'      => $identifier,
            'secret'          => bcrypt($password),
            'is_primary'      => false,
        ]);
    
        if ($loginMethod === 'email' && $user->email !== $identifier) {
            $user->email = $identifier;
            $user->save();
        } elseif ($loginMethod === 'phone' && $user->phone !== $identifier) {
            $user->phone = $identifier;
            $user->is_phone_verified = '1';
            $user->save();
        }
    
        Cache::forget($cacheKey);
    
        return response()->json([
            'success' => true,
            'message' => 'Login method linked successfully.'
        ]);
    }
    
    public function verifySocialAccount(Request $request)
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
    
    public function switchPrimaryLoginMethod($id)
    { 
        
        $user   = Auth::user();
        $login = UserLogin::where('user_id', $user->id)->where('id',$id)->first();
    
        if(empty($login)) {
            return response()->json([
                'status' => true,
                'message' => 'Invalid login method id.',
            ],200);
        }
        $user->logins()->update(['is_primary' => false]);

        $login->is_primary = true;
        $login->save();
        
        #SEND NOTIFICATIONS
        sendFirebaseNotification($user->id,'Primary login change','Primary login updated successfully.');
        
        return response()->json([
            'status' => true,
            'message' => 'Primary login updated successfully.',
        ],200);
    }
    
    public function deleteUserLoginMethod($id)
    {
        $user = Auth::user();
        $login = UserLogin::where('user_id', $user->id)->where('id',$id)->first();
        
        if(empty($login)) {
            return response()->json([
                'status' => true,
                'message' => 'Invalid login method id.',
            ],200);
        }
        
        if ($login->is_primary) {
            return response()->json([
                'status' => true,
                'message' => 'Cannot delete primary login.',
            ],200);
        }

        $login->delete();
        
        #SEND NOTIFICATIONS
        sendFirebaseNotification($user->id,'Login method unlinked','Login method unlinked successfully.');
        
        return response()->json([
            'status' => true,
            'message' => 'Login method unlinked successfully.',
        ],200);
    }
    
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'nullable|email',
            'phone'    => 'nullable|string',
            'password' => 'required|string',
        ], [
            'email.email' => 'Invalid email format.',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        if ($request->filled('email')) {
            $field = 'email';
            $identifier = $request->email;
        } elseif ($request->filled('phone')) {
            $field = 'phone';
            $identifier = $request->phone;
        } else {
            return response()->json(['message' => 'Email or phone is required.'], 422);
        }
    
        //$user = User::where($field, $identifier)->where('role', 'user')->first();
         $user = User::where($field, $identifier)->whereIn('role', ['user','vendor','guest'])->first();
        if (!$user) {
            return response()->json(['message' => ucfirst($field) . ' not registered.'], 404);
        }
        
        if(!empty($user) && $user->status==0){
             return response()->json(['message' => ' Your account is deactivated please contact to administrator.'], 401);
        }
        
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid password.'], 401);
        }
        
        #GUEST LOGIN
        if(!empty($user->role) && $user->role=='guest'){
            $token = $user->createToken('api-token')->plainTextToken;
            return response()->json([
                'status' => true,
                'message' => 'Guest login successfully.',
                'token'   => $token,
                'user'    => $user,
            ]);
        }
        #VENDOR LOGIN
        if(!empty($user->role) && $user->role=='vendor'){
            $token = $user->createToken('api-token')->plainTextToken;
            return response()->json([
                'status' => true,
                'message' => 'Vendor login successfully.',
                'token'   => $token,
                'user'    => $user,
            ]);
        }
        
        $otp = rand(100000, 999999);
        $expiresAt = now()->addMinutes(5);
    
        $otpToken = Str::random(60);
    
        DB::table('login_otps')->updateOrInsert(
            ['user_id' => $user->id],
            [
                'otp'        => Hash::make($otp),
                'token'      => $otpToken,
                'expires_at' => $expiresAt,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    
        if ($field === 'email') {
           
            #SEND MAIL
			Mail::send('emails.user-otp', ['user' => $user, 'otp' => $otp], function ($message) use ($user) {
				$message->to($user->email)->subject('Your Login OTP');
			});
            
        } else {
            // Replace with your SMS service
            #SEND MAIL
			Mail::send('emails.user-otp', ['user' => $user, 'otp' => $otp], function ($message) use ($user) {
				$message->to(array($user->email))->subject('Your Login Phone OTP');
			});
            
        } 
        
        #SAVE LOGIN METHOD
	    UserLogin::attachOrUpdate($user, $field, $identifier,$request->password,true);
            
        return response()->json([
            'status' => true,
            'message' => 'OTP sent successfully.',
            'otp_expires_at' => $expiresAt->toDateTimeString(),
            'otp_token' => $otpToken,   
            'login_field' => $field,
            //'otp' => $otp,
        ]);
    }

    public function verifyLoginOtp(Request $request)
    {
        $request->validate([
            'otp'       => 'required|string',
            'otp_token' => 'required|string',
        ]);
    
        $record = DB::table('login_otps')->where('token', $request->otp_token)->latest('id')->first();
        if (!$record) {
            return response()->json(['message' => 'Invalid OTP token.'], 404);
        }
    
 
        if (now()->greaterThan($record->expires_at)) {
            return response()->json(['message' => 'OTP expired. Please login again.'], 401);
        }
    
 
        if (!Hash::check($request->otp, $record->otp)) {
            return response()->json(['message' => 'Invalid OTP.'], 401);
        }
    

        $user = User::find($record->user_id);
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }
    

        $token = $user->createToken('api-token')->plainTextToken;
    

        DB::table('login_otps')->where('user_id', $user->id)->delete();
        
        #SEND NOTIFICATIONS
        sendFirebaseNotification($user->id,'OTP Verified','OTP verified successfully.');
        
        return response()->json([
            'status' => true,
            'message' => 'OTP verified successfully.',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function editProfile(Request $request)
    {
        $user = auth()->user(); // Get the logged-in user

        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'password' => 'nullable',
            'image'    => 'nullable|image',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->name = $request->name;
        $user->phone = $request->phone;

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->image && file_exists(public_path('uploads/users/' . $user->image))) {
                unlink(public_path('uploads/users/' . $user->image));
            }

            // Save new image
            $imageName = uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/users'), $imageName);
            $user->image = $imageName;
        }

        // Handle password update
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();
        
        #SEND NOTIFICATIONS
        sendFirebaseNotification($user->id,'Profile update','Profile updated successfully.');
        
        return response()->json([
            'status'  => true,
            'image_path'=>'/uploads/users/',
            'message' => 'Profile updated successfully',
            'user'    => $user,
        ], 200);
    }


   public function me(Request $request)
     {
       $user = auth()->user();
       
        return response()->json([
        'status'=>true,
        'image_path'=>'/uploads/users/',
        'message' => 'User data',
        'user'    => $user,
        ], 200);
     }

    /* public function categories()
    {
        try {
            $categories = Category::get();

            return response()->json([
                'status' => true,
                'message' => 'categories data',
                'categories' => $categories,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch categories.',
                'error' => $e->getMessage(),
            ], 500);
        }
    } */
	
	public function categories()
	{
		try {
			$categories = Category::get();
			$allCategories = $categories->keyBy('id');
			$categories->transform(function ($category) use ($allCategories) {
				$category->junior_id = 0;
				
				$children = $allCategories->where('parent_id', $category->id);
				
				if ($children->isNotEmpty()) {
				
					$firstChild = $children->first();
					
					$level = $this->getCategoryLevel($category->id, $allCategories);
					
					if ($level < 3) {
						$category->junior_id = $firstChild->id;
					}
				}
				
				return $category;
			});

			return response()->json([
				'status' => true,
				'message' => 'categories data',
				'categories' => $categories,
			], 200);
		} catch (\Exception $e) {
			return response()->json([
				'status' => false,
				'message' => 'Failed to fetch categories.',
				'error' => $e->getMessage(),
			], 500);
		}
	}

	private function getCategoryLevel($categoryId, $allCategories, $level = 1)
	{
		$category = $allCategories->get($categoryId);
		
		if (!$category || $category->parent_id === null) {
			return $level;
		}
		
		return $this->getCategoryLevel($category->parent_id, $allCategories, $level + 1);
	}
	
	
    
    public function getParentCategories(Request $request)
    {   
        $perPage = $request->input('limit', 10);
    
        $categories = Category::with(['products' => function ($q) {
            $q->select('products.id', 'products.name', 'products.price', 'products.image')
              ->orderBy('id','DESC')  
              ->limit(4);  // only 4 products
        }])->whereNull('parent_id')
          ->paginate($perPage);
        
        foreach ($categories as $category) {
            foreach ($category->products as $product) {
                unset($product->pivot);
            }
        }
        
        return response()->json([
            'status'     => true,
            'message'    => 'categories data',
            'categories' => $categories,
        ], 200);
    }

    
    public function products(Request $request)
        { 
			$perPage    = $request->input('limit', 10);
			$searchTerm = $request->input('search');
			
            /* $products = Product::with('categories','attributes','productAttributes.attribute','productAttributes.variants.attributeValue','stores','brand','galleryImages')->get(); */
                
				
				$query = Product::with([
					'categories',
					'attributes',
					'productAttributes.attribute',
					'productAttributes.variants.attributeValue',
					'stores',
					'brand',
					'galleryImages'
				]);
				if (!empty($searchTerm)) {
					$query->where(function ($q) use ($searchTerm) {
						$q->where('name', 'LIKE', "%{$searchTerm}%")
						  ->orWhere('sku', 'LIKE', "%{$searchTerm}%");
					});
					
					#STORE SEARCH HISTORY IN DATABASE
        			if (auth()->id()) {
        				SearchHistory::updateOrCreate(
        					[
        						'user_id' => auth()->id(),
        						'query' => $searchTerm, 
        					],
        					[
        						'updated_at' => now(),
        					]
        				);
        			}
					
				}
				$products = $query->orderBy('id', 'desc')->paginate($perPage);
				
				return response()->json([
					'status'=>true,
					'gallery_images_path'=>'/uploads/product/gallery/',
					'message' => 'products data',
					'products'    => $products,
                ], 200);
        }

     public function stores()
        {
             $stores = VendorStore::get();
                return response()->json([
                'status'=>true,
                'message' => 'stores data',
                'stores'    => $stores,
               ], 200);
        }
        
      public function sliders()
        {
               $sliders = Sliders::where('status', 1)->get();
                return response()->json([
                'status'=>true,
                'image_path'=>'/uploads/sliders/',
                'message' => 'sliders data',
                'sliders'    => $sliders,
               ], 200);
        }


      public function product_details($id) { 
	
	    $product  = Product::with('categories','attributes','productAttributes.attribute','productAttributes.variants.attributeValue','stores.user','galleryImages','brand')->where('id', $id)->with('reviews')->first();

	        return response()->json([
                'status'=>true,
                'gallery_images_path'=>'/uploads/product/gallery/',
                'message' => 'product data',
                'product'    => $product,
               ], 200);
         }

         
 /* public function logout(Request $request)
{
    $userId = auth()->id();
    DB::table('sessions')->where('user_id', $userId)->delete();
    
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'success' => true,
        'message' => 'Logged out successfully.'
    ]);
} */

public function logout(Request $request)
{
    if ($request->user()) {
        $request->user()->tokens()->delete();
    }

    Auth::guard('web')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->json([
        'success' => true,
        'message' => 'Logged out successfully.'
    ]);
}

public function review_submit(Request $request)
{   
    if (!auth()->check()) {
        
        return response()->json([
            'status' => false,
            'message' => 'Please login to post a review.'
        ]);
    }

    $validated = $request->validate([
        'rating'     => 'required|integer|min:1|max:5',
        'review'     => 'required|string',
        //'author'     => 'required|string',
        //'email'      => 'required|email',
        'product_id' => 'required|integer',
        'image'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    ]);
    
    $user = auth()->user();

    $userId = auth()->id();
    $product = Product::where('id', $request->product_id)->first();

    $existingReview = Review::where('user_id', $userId)
                            ->where('product_id', $validated['product_id'])
                            ->first();

    if ($existingReview) {
        return response()->json([
            'status' => false,
            'message' => 'You have already submitted a review for this product.'
        ]);
    }
    
    $imagePath = null;
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('uploads/reviews'), $filename);
        $imagePath = $filename;
    }
    $validated['email']    = $user->email;
    $validated['author']   = $user->name;
    $validated['user_id']  = $userId;
    $validated['image']    = $imagePath;
    
    Review::create($validated);
        
        #SEND NOTIFICATIONS
        sendFirebaseNotification($userId,'Review Submit','Review submitted successfully.');
       
        return response()->json([
            'status' => true,
            'message' => 'Review submitted successfully!'
        ]);
}

public function review_update(Request $request)
{   
    if (!auth()->check()) {
        return response()->json([
            'status' => false,
            'message' => 'Please login to update a review.'
        ]);
    }

    $validated = $request->validate([
        'review_id'  => 'required|exists:reviews,id',
        'rating'     => 'required|integer|min:1|max:5',
        'review'     => 'required|string',
        //'author'     => 'required|string',
        //'email'      => 'required|email',
        'product_id' => 'required|exists:products,id',
        'image'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    ]);

    $userId = auth()->id();

    $existingReview = Review::where('id', $validated['review_id'])
                            ->where('user_id', $userId)
                            ->where('product_id', $validated['product_id'])
                            ->first();

    if (!$existingReview) {
        return response()->json([
            'status' => false,
            'message' => 'Review not found or you are not authorized to update it.'
        ]);
    }

    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('uploads/reviews'), $filename);
        $validated['image'] = $filename;
    } else {
        unset($validated['image']);
    }

    unset($validated['review_id']);
    unset($validated['user_id']);

    $existingReview->update($validated);

    sendFirebaseNotification($userId, 'Review Update', 'Review updated successfully.');

    return response()->json([
        'status' => true,
        'message' => 'Review updated successfully!'
    ]);
}

public function review_delete(Request $request)
{
    if (!auth()->check()) {
        return response()->json([
            'status' => false,
            'message' => 'Please login to delete a review.'
        ]);
    }

    $validated = $request->validate([
        'review_id' => 'required|exists:reviews,id',
    ]);

    $userId = auth()->id();

    $review = Review::where('id', $validated['review_id'])
                    ->where('user_id', $userId)
                    ->first();

    if (!$review) {
        return response()->json([
            'status' => false,
            'message' => 'Review not found or you are not authorized to delete it.'
        ]);
    }

    if ($review->image) {
        $imagePath = public_path('uploads/reviews/' . $review->image);
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    $review->delete();

    sendFirebaseNotification($userId, 'Review Deleted', 'Your review has been deleted.');

    return response()->json([
        'status' => true,
        'message' => 'Review deleted successfully.'
    ]);
}


public function getReviewDetails(Request $request)
{
    if (!auth()->check()) {
        return response()->json([
            'status' => false,
            'message' => 'Please login to delete a review.'
        ]);
    }

    $validated = $request->validate([
        'review_id' => 'required|exists:reviews,id',
    ]);

    $userId = auth()->id();

    $review = Review::where('id', $validated['review_id'])
                    ->where('user_id', $userId)
                    ->first();

    if (!$review) {
        return response()->json([
            'status' => false,
            'message' => 'Review not found.'
        ]);
    }

    return response()->json([
        'status' => true,
        'data' => $review
    ]);
}


/*public function total_reviews(Request $request)
{
    if (!auth()->check()) {
        return response()->json([
            'status' => false,
            'message' => 'Please login first.'
        ]);
    }

    $validated = $request->validate([
        'product_id' => 'required|exists:products,id',
    ]);

    $productId = $validated['product_id'];

    $totalReviews = Review::where('product_id', $productId)->count();
    $averageRating = Review::where('product_id', $productId)->avg('rating');
    $totalLikes = Review::where('product_id', $productId)->sum('likes');
    $totalDislikes = Review::where('product_id', $productId)->sum('dislikes');

    return response()->json([
        'status' => true,
        'message' => 'Review stats fetched successfully.',
        'data' => [
            'total_reviews' => $totalReviews,
            'average_rating' => round($averageRating, 1),
            'total_likes' => $totalLikes,
            'total_dislikes' => $totalDislikes,
        ]
    ]);
}*/


public function total_reviews(Request $request)
{
    if (!auth()->check()) {
        return response()->json([
            'status' => false,
            'message' => 'Please login first.'
        ]);
    }

    $validated = $request->validate([
        'product_id' => 'required|exists:products,id',
    ]);

    $productId = $validated['product_id'];

    $totalReviews = Review::where('product_id', $productId)->count();
    $averageRating = Review::where('product_id', $productId)->avg('rating');
    $totalLikes = Review::where('product_id', $productId)->sum('likes');
    $totalDislikes = Review::where('product_id', $productId)->sum('dislikes');

    // ⭐ Star rating count breakdown
    $ratingsCount = Review::select('rating', DB::raw('count(*) as count'))
        ->where('product_id', $productId)
        ->groupBy('rating')
        ->pluck('count', 'rating');

    // Ensure all ratings from 1 to 5 are present
    $starRatings = [];
    for ($i = 5; $i >= 1; $i--) {
        $starRatings[$i] = $ratingsCount[$i] ?? 0;
    }
    
    $totalRatingGiven = array_sum($starRatings);
    
    return response()->json([
        'status' => true,
        'message' => 'Review stats fetched successfully.',
        'data' => [ 
            'total_reviews'    => $totalReviews,
            'total_rating'     => $totalRatingGiven,
            'average_rating'   => round($averageRating, 1),
            'total_likes'      => $totalLikes,
            'total_dislikes'   => $totalDislikes,
            'star_ratings'     => $starRatings, 
        ]
    ]);
}

/*public function review_react(Request $request)
{
    if (!auth()->check()) {
        return response()->json([
            'status' => false,
            'message' => 'Please login to react to a review.'
        ]);
    }

    $validated = $request->validate([
        'review_id' => 'required|exists:reviews,id',
        'reaction'  => 'required|in:like,dislike',
    ]);

    $review = Review::find($validated['review_id']);

    if (!$review) {
        return response()->json([
            'status' => false,
            'message' => 'Review not found.'
        ]);
    }

    if ($validated['reaction'] === 'like') {
        $review->increment('likes');
    } else {
        $review->increment('dislikes');
    }

    return response()->json([
        'status' => true,
        'message' => 'Your reaction has been recorded.',
        'data' => [
            'likes' => $review->likes,
            'dislikes' => $review->dislikes,
        ]
    ]);
}*/

public function review_react(Request $request)
{
    if (!auth()->check()) {
        return response()->json([
            'status' => false,
            'message' => 'Please login to react to a review.'
        ]);
    }

    $validated = $request->validate([
        'review_id' => 'required|exists:reviews,id',
        'reaction'  => 'required|in:like,dislike',
    ]);

    $userId = auth()->id();
    $review = Review::findOrFail($validated['review_id']);

    $reactions = json_decode($review->reactions ?? '{}', true);

    $previousReaction = $reactions[$userId] ?? null;
    $currentReaction = $validated['reaction'];

    $message = '';

    if ($previousReaction === $currentReaction) {
        $message = $currentReaction === 'like' ? 'You already liked this review.' : 'You already disliked this review.';
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => [
                'likes'    => $review->likes,
                'dislikes' => $review->dislikes,
            ]
        ]);
    }

    // If user switching reaction
    if ($previousReaction === 'like' && $currentReaction === 'dislike') {
        $review->decrement('likes');
        $review->increment('dislikes');
        $message = 'You disliked the review.';
    } elseif ($previousReaction === 'dislike' && $currentReaction === 'like') {
        $review->decrement('dislikes');
        $review->increment('likes');
        $message = 'You liked the review.';
    } elseif (!$previousReaction) {
        // First time reacting
        if ($currentReaction === 'like') {
            $review->increment('likes');
            $message = 'You liked the review.';
        } else {
            $review->increment('dislikes');
            $message = 'You disliked the review.';
        }
    }

    // Update user's reaction record
    $reactions[$userId] = $currentReaction;
    $review->reactions = json_encode($reactions);
    $review->save();

    return response()->json([
        'status' => false,
        'message' => $message,
        'data' => [
            'likes' => $review->likes,
            'dislikes' => $review->dislikes,
        ]
    ]);
}

public function getReviews(Request $request){
    
    $perPage = $request->input('limit', 10);
    
    $results = Review::with('user')->where('product_id',$request->product_id)->paginate($perPage);
    
    return response()->json([
        'status' => true,
        'data'   => $results
    ],200);
}

/* public function placeOrderBkp(Request $request)
{
    $request->validate([
        'billing_first_name'         => 'required|string|max:100',
        'billing_last_name'          => 'required|string|max:100',
        'billing_company_name'       => 'nullable|string|max:150',
        'billing_country'            => 'required|string|max:100',
        'billing_address_1'          => 'required|string|max:255',
        'billing_address_2'          => 'nullable|string|max:255',
        'billing_city'               => 'required|string|max:100',
        'billing_state'              => 'required|string|max:100',
        'billing_zip'                => 'required|string|max:20',
        'billing_phone'              => 'required|regex:/^[0-9]{10,15}$/',
        'billing_email'              => 'required|email|max:255',
        'shipping_first_name'        => 'nullable|string|max:100',
        'shipping_last_name'         => 'nullable|string|max:100',
        'shipping_company_name'      => 'nullable|string|max:150',
        'shipping_country'           => 'nullable|string|max:100',
        'shipping_state'             => 'nullable|string|max:100',
        'shipping_street_address_1'  => 'nullable|string|max:255',
        'shipping_street_address_2'  => 'nullable|string|max:255',
        'shipping_city'              => 'nullable|string|max:100',
        'shipping_zipcode'           => 'nullable|string|max:20',
        'order_notes'                => 'nullable|string|max:1000',
        'payment_method'             => 'required|in:cod',
        'cart'                       => 'required|array|min:1',
        'cart.*.product_id'          => 'required|integer|exists:products,id',
        'cart.*.name'                => 'required|string',
        'cart.*.quantity'            => 'required|integer|min:1',
        'cart.*.price'               => 'required|numeric|min:0',
    ]);

    $userId = auth()->id(); // Make sure API is authenticated

    $cart = $request->cart;

    $subtotal = collect($cart)->sum(function ($item) {
        return $item['price'] * $item['quantity'];
    });

    $currency = getDefaultSelectedCurrency(); // Implement as needed

    $order = new Order;
    $order->user_id                    = $userId;
    $order->payment_method             = $request->payment_method;
    $order->order_status              = 'pending';
    $order->total_amount               = $subtotal;
    $order->currency                   = $currency;
    $order->billing_first_name         = $request->billing_first_name;
    $order->billing_last_name          = $request->billing_last_name;
    $order->billing_company            = $request->billing_company_name;
    $order->billing_country            = $request->billing_country;
    $order->billing_address_1          = $request->billing_address_1;
    $order->billing_address_2          = $request->billing_address_2;
    $order->billing_city               = $request->billing_city;
    $order->billing_zipcode            = $request->billing_zip;
    $order->billing_state              = $request->billing_state;
    $order->phone                      = $request->billing_phone;
    $order->email                      = $request->billing_email;
    $order->shipping_first_name        = $request->shipping_first_name;
    $order->shipping_last_name         = $request->shipping_last_name;
    $order->shipping_company           = $request->shipping_company_name;
    $order->shipping_country           = $request->shipping_country;
    $order->shipping_state             = $request->shipping_state;
    $order->shipping_address_1         = $request->shipping_street_address_1;
    $order->shipping_address_2         = $request->shipping_street_address_2;
    $order->shipping_city              = $request->shipping_city;
    $order->shipping_zipcode           = $request->shipping_zipcode;
    $order->order_notes                = $request->order_notes;
    $order->order_number               = 'ORDER' . rand(100000, 999999);
    $check=$order->save();

    foreach ($cart as $item) {
        $orderProduct = new Order_product;
        $orderProduct->order_id   = $order->id;
        $orderProduct->currency   = $currency;
        $orderProduct->product_id = $item['product_id'];
        $orderProduct->name       = $item['name'];
        $orderProduct->quantity   = $item['quantity'];
        $orderProduct->price      = $item['price'];
        $orderProduct->total      = $item['price'] * $item['quantity'];
        $orderProduct->save();
    }

    $orderTotals = [
        ['meta_key' => 'Sub Total', 'meta_value' => $subtotal],
        ['meta_key' => 'Total', 'meta_value' => $subtotal],
    ];

    foreach ($orderTotals as $item) {
        $orderTotal = new Order_total;
        $orderTotal->order_id   = $order->id;
        $orderTotal->meta_key   = $item['meta_key'];
        $orderTotal->meta_value = $item['meta_value'];
        $orderTotal->currency   = $currency;
        $orderTotal->save();
    }

    if($check){
           Cart::where('user_id',$userId)->delete();
    }

    return response()->json([
        'success' => true,
        'message' => 'Order placed successfully',
        'order_id' => $order->id,
        'order_number' => $order->order_number,
    ]);
} */

     public function orderStatusUpdate(Request $request){
        
       $request->validate([
            'order_id'       => 'required',
            'pay_type'       => 'required|in:paypal,stripe',
            'transaction_id' => 'required',
        ]);
        
        $user    = auth()->user();
		$userId  = $user?->id;
        
        $orderIds  = explode(',',$request->order_id);
        
		foreach($orderIds as $order_id)	{
			
			#CREATE CJ DROPSHIPPING ORDER
			/* $cjService   = new CjDropshippingService();
			$result		 = $cjService->createCjOrdersFromLocalOrder($order_id); */
			
			#AUTO-DS DROPSHIPPING
			$autoDsService = new AutoDSService();
			$autoDsService->createAutoDSOrdersFromLocalOrder($order_id);
			
			$orderDetails = Order::where('id', $order_id)->first();
			$orderDetails->payment_method  = ucfirst($request->pay_type);
			$orderDetails->payment_status  = 'paid';
			$orderDetails->order_status    = 'confirmed';
			$orderDetails->payment_transaction_id = $request->transaction_id;
			$orderDetails->save();
			
			#EMAIL
			$orderInfo = Order::with('orderProduct.product.userInfo', 'orderTotal')->where('id',$order_id)->first();
		
			Mail::send('emails.order-receipt', ['order' => $orderInfo], function ($message) use ($orderInfo) {
				$message->to([$orderInfo->email ?? '', get_admin_email(),get_vendor_email($orderInfo->vendor_id)])
						->subject('Your Order Receipt - #' . $orderInfo->order_number);
			}); 
		}
		
		#CLEAR CART ITEMS
		$user ? CartItem::where('user_id', $userId)->delete() : session()->forget('cart');
		
		#SEND NOTIFICATIONS
        sendFirebaseNotification($userId,'Update Order','Order placed successfully.');
		
		return response()->json([
            'success'      => true,
            'message'      => 'Order placed successfully',
            'order_id'     => $orderInfo->id,
            'order_number' => $orderInfo->order_number,
            'order'        => implode(',',$orderIds),
        ]);
		
    }
    
    public function placeOrder(Request $request) {  
       
        $userId   = auth()->id();
        $user     = auth()->user();
        
        #CART DATA FROM CART ITEM
        $cart = [];
    	if (auth()->check()) {
    		$cartItems = CartItem::with('product')->where('user_id', auth()->id())->get();
    		foreach ($cartItems as $item) {
    			$cart[] = [
    				'product_id' => $item->product_id,
    				'name'       => $item->product->name ?? '',
    				'price'      => $item->price,
    				'quantity'   => $item->quantity,
    				'variants'   => $item->variants,
    			];
    		}
    	}
        
        if (empty($cart)) {
			return response()->json([
                'success' => true,
                'message' => 'Your cart is empty',
            ]);
		}
        
        $subtotal = collect($cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    
        $currency = getDefaultSelectedCurrency(); 
        
        
        if (!empty($request->address_id)) {
    
            $request->validate([
                'address_id'        => 'nullable|integer|exists:address,id',
                'coupon_id' => [
                    'nullable',
                    'integer',
                    Rule::exists('coupons', 'id')->where(function ($query) {
                        $query->where('is_active', 1)
                              ->where(function ($q) {
                                  $q->whereNull('expires_at')
                                    ->orWhere('expires_at', '>', now());
                              })
                              ->where(function ($q) {
                                  $q->whereNull('starts_at')
                                    ->orWhere('starts_at', '<=', now());
                              });
                    }),
                ],
                'payment_method'    => 'required|in:cod,paypal,stripe,wallet',
            ]);
        
            $address = Address::where('user_id', $userId)->where('id', $request->address_id)->firstOrFail();
        
            Address::where('user_id', $userId)->update([
                'is_billing'  => '0', 
                'is_shipping' => '0',
                'is_default' => '0',
            ]);
        
            $address->update([
                'is_billing' => '1',
                'is_shipping' => '1',
                'is_default' => '1',
            ]);
        
        } else {
    
            $request->validate([
                'address_id'                 => 'nullable|integer|exists:address,id',
                'coupon_id' => [
                    'nullable',
                    'integer',
                    Rule::exists('coupons', 'id')->where(function ($query) {
                        $query->where('is_active', 1)
                              ->where(function ($q) {
                                  $q->whereNull('expires_at')
                                    ->orWhere('expires_at', '>', now());
                              })
                              ->where(function ($q) {
                                  $q->whereNull('starts_at')
                                    ->orWhere('starts_at', '<=', now());
                              });
                    }),
                ],
                'billing_first_name'         => 'required|string|max:100',
                'billing_last_name'          => 'required|string|max:100',
                'billing_company_name'       => 'nullable|string|max:150',
                'billing_country'            => 'required|string|max:100',
                'billing_address_1'          => 'required|string|max:255',
                'billing_address_2'          => 'nullable|string|max:255',
                'billing_city'               => 'required|string|max:100',
                'billing_state'              => 'required|string|max:100',
                'billing_zip'                => 'required|string|max:20',
                'billing_phone'              => 'required|regex:/^[0-9]{10,15}$/',
                'billing_email'              => 'required|email|max:255',
                'shipping_first_name'        => 'nullable|string|max:100',
                'shipping_last_name'         => 'nullable|string|max:100',
                'shipping_company_name'      => 'nullable|string|max:150',
                'shipping_country'           => 'nullable|string|max:100',
                'shipping_state'             => 'nullable|string|max:100',
                'shipping_street_address_1'  => 'nullable|string|max:255',
                'shipping_street_address_2'  => 'nullable|string|max:255',
                'shipping_city'              => 'nullable|string|max:100',
                'shipping_zipcode'           => 'nullable|string|max:20',
                'order_notes'                => 'nullable|string|max:1000',
                'payment_method'             => 'required|in:cod,paypal,stripe,wallet,emis',
            ]);
        
            Address::where('user_id', $userId)->update([
                'is_billing' => '0',
                'is_shipping' => '0',
                'is_default' => '0'
            ]);
        
            $address = Address::create([
                'user_id'               => $userId,
                'first_name'            => $request->billing_first_name,
                'last_name'             => $request->billing_last_name,
                'company'               => $request->billing_company_name,
                'country'               => $request->billing_country,
                'address_1'             => $request->billing_address_1,
                'address_2'             => $request->billing_address_2,
                'city'                  => $request->billing_city,
                'state'                 => $request->billing_state,
                'zipcode'               => $request->billing_zip,
                'phone'                 => $request->billing_phone,
                //'email'               => $request->billing_email,
                'is_billing'            => '1',
                'is_shipping'           => '1',
                'is_default'            => '1',
            ]);
            
            
            
        }
        
        $grouped = collect($cart)->groupBy(function ($item) {
    		$product = Product::find($item['product_id']);
    		return $product->stores->first()->user_id ?? null; 
    	});
    	
    	$vendorData = $grouped->map(function ($items) {
    		return [
    			'products' => $items,
    			'total'    => $items->sum(fn($item) => $item['price'] * $item['quantity']),
    		];
    	});
    	
    	#PAYMENT STATUS
		if($request->payment_method=='stripe' || $request->payment_method=='paypal' || $request->payment_method=='wallet' || $request->payment_method=='emis'){
			$payment_status = 'failed';
		}else{
			$payment_status = 'pending';
		}
    	
       $orderIds =  array();
       $orderTotalAmt = 0;
       
       if(!empty($request->coupon_id)) {
          $coupon = $this->checkApplyCoupon($request->coupon_id);
       }else{
         $coupon = array();
       }
    
       foreach ($grouped as $vendorId => $vendorItems) {
            
            $vendorSubtotal = $vendorData[$vendorId]['total'];
			$discountAmount = 0;
			$couponId       = null;
			$couponCode     = null;

			if ($coupon) {
				if ($coupon['vendor_id'] && $coupon['vendor_id'] == $vendorId) {
					$discountAmount = min($coupon['discount'], $vendorSubtotal);
					$couponId       = $coupon['id'];
					$couponCode     = $coupon['code'];
				} elseif (!$coupon['vendor_id']) {
					$discountAmount = round(($vendorSubtotal / $subtotal) * $coupon['discount'], 2);
					$couponId       = $coupon['id'];
					$couponCode     = $coupon['code'];
				}
			}

			$finalTotal = $vendorSubtotal - $discountAmount;
            
            $order = new Order;
            $order->user_id                    = $userId;
            $order->vendor_id                  = $vendorId;
			$order->coupon_id                  = $couponId;
			$order->coupon_code                = $couponCode;
			$order->coupon_amount              = $discountAmount;
            $order->payment_method             = $request->payment_method;
            $order->order_status               = 'pending';
            $order->payment_status             = $payment_status;
            $order->total_amount               =  priceCalculatedOnlyAccordingToCurrency($finalTotal);
            $order->currency                   = $currency;
    
            $order->billing_first_name         = $address->first_name;
            $order->billing_last_name          = $address->last_name;
            $order->billing_company            = $address->company_name;
            $order->billing_country            = $address->country;
            $order->billing_address_1          = $address->address_1;
            $order->billing_address_2          = $address->address_2;
            $order->billing_city               = $address->city;
            $order->billing_zipcode            = $address->zip;
            $order->billing_state              = $address->state;
            $order->phone                      = $address->phone ??  $user?->email;
            $order->email                      = $address->email ?? $user?->email;
            
            $order->shipping_first_name        = $request->shipping_first_name ?? $address->first_name;
            $order->shipping_last_name         = $request->shipping_last_name ?? $address->last_name;
            $order->shipping_company           = $request->shipping_company_name ?? $address->company_name;
            $order->shipping_country           = $request->shipping_country ?? $address->country;
            $order->shipping_state             = $request->shipping_state ?? $address->state;
            $order->shipping_address_1         = $request->shipping_street_address_1 ?? $address->address_1;
            $order->shipping_address_2         = $request->shipping_street_address_2 ?? $address->address_2;
            $order->shipping_city              = $request->shipping_city ?? $address->city;
            $order->shipping_zipcode           = $request->shipping_zipcode ?? $address->zip;
            $order->order_notes                = $request->order_notes ?? "";
            
            $order->order_number               = 'ORDER' . rand(100000, 999999);
    		$order->tracking_number            = 'TRACK' . rand(100000, 999999);
    		$order->shipping_provider          = 'DHL';
            $order->save();
            
            $vendorSubtotal = 0;
			
            #CALL COMMISSIONS SERVICES
			$commissionService = new CommissionCalculationService();
			
            foreach ($vendorItems as $item) {
                
                #GET THE PRODUCT COMMISSIONS START
				$productCommission = ProductCommissions::where('product_id', $item['product_id'])->first();

				$vendorCommission = null;
				if (!empty($productCommission) && !empty($productCommission->vendor_id)) {
					$vendorCommission = VendorCommissions::where('vendor_id', $productCommission->vendor_id)->first();
				}
				if (empty($vendorCommission)) {
					$vendorCommission = VendorCommissions::where('vendor_id', $vendorId)->first();
				}
				
				$commission = $commissionService->calculate($productCommission, $vendorCommission, $item['price']);
				
				$transaction = new Transaction();
				$transaction->order_id              = $order->id;
				$transaction->vendor_id             = $vendorId;
				$transaction->vendor_amount         = $commission['vendor_amount'] ?? 0.00;
				$transaction->ondjango_commission   = $commission['ondjango_commission'] ?? 0.00;
				$transaction->commission_rate       = $commission['commission_rate'] ?? 0.00;
				$transaction->product_id            = $item['product_id'];
				$transaction->payment_method        = $order->payment_method ?? 'unknown';
				$transaction->transaction_id        = null;
				$transaction->payment_flow          = $commission['payment_flow'];
				$transaction->payment_note          = $commission['payment_note'];
				$transaction->vendor_type           = $commission['vendor_type'];
				$transaction->status = 'paid';
				$transaction->save();

				if ($commission['vendor_type'] === 'internal') {
					// Internal Vendor
					// → 100% payment goes to Ondjango
					// → Finance/Admin later settles vendor amount manually or via cron
				} else {
					// External Vendor
					// → Split payment logic (example)
					// PaymentGateway::splitPayment([
					//     'vendor_account' => $vendorCommission->bank_account,
					//     'vendor_amount'  => $commission['vendor_amount'],
					//     'ondjango_amount' => $commission['ondjango_commission'],
					// ]);
				}
				
				#GET THE PRODUCT COMMISSIONS END
				
				
				$product  = Product::with('stores.user')->where('id', $item['product_id'])->first();
    			$store    = $product->stores->first();
                
                $orderProduct = new Order_product;
                $orderProduct->order_id             = $order->id;
                $orderProduct->store_id             = $store?->id;
    			$orderProduct->vendor_id            = $vendorId;
				$orderProduct->vendor_amount        = $commission['vendor_amount'] ?? 0.00;
				$orderProduct->ondjango_commission  = $commission['ondjango_commission'] ?? 0.00;
				$orderProduct->commission_rate      = $commission['commission_rate'] ?? 0.00;
                $orderProduct->currency             = $currency;
                $orderProduct->product_id           = $item['product_id'];
                $orderProduct->name                 = $item['name'];
                $orderProduct->quantity             = $item['quantity'];
                $orderProduct->price                = priceCalculatedOnlyAccordingToCurrency($item['price']);
                $orderProduct->total                = priceCalculatedOnlyAccordingToCurrency($item['price'] * $item['quantity']);
                $orderProduct->variant_ids          = isset($item['variants']) ? implode('-', $item['variants']) : null;
    			$orderProduct->variant_text         = $this->buildVariantText($item['variants'] ?? []);
                $orderProduct->save();
                
                $getTotal        = $item['price'] * $item['quantity'];
    			$vendorSubtotal += $getTotal;
            }
            
            $orderTotals[] = [
    			'order_id'   => $order->id,
    			'meta_key'   => 'Sub Total',
    			'meta_value' => priceCalculatedOnlyAccordingToCurrency($vendorSubtotal),
    			'currency'   => $currency,
    		];
			
			if ($discountAmount > 0) {
				$orderTotals[] = [
					'order_id'   => $order->id,
					'meta_key'   => 'Discount',
					'meta_value' => -priceCalculatedOnlyAccordingToCurrency($discountAmount),
					'currency'   => $currency,
				];
			}
			
    		$orderTotals[] = [
    			'order_id'   => $order->id,
    			'meta_key'   => 'Shipping',
    			'meta_value' => priceCalculatedOnlyAccordingToCurrency(0),
    			'currency'   => $currency,
    		];
    		$orderTotals[] = [
    			'order_id'   => $order->id,
    			'meta_key'   => 'Total',
    			'meta_value' => priceCalculatedOnlyAccordingToCurrency($finalTotal),
    			'currency'   => $currency,
    		];
           
           #ORDER TOTAL    
           Order_total::insert($orderTotals);
           
           if($request->payment_method=='cod'){
        		#EMAIL
        		$orderInfo = Order::with('orderProduct.product.userInfo', 'orderTotal')->where('id',$order->id)->where('vendor_id',$order->vendor_id)->first();
        	    
        	    #CREATE CJ DROPSHIPPING ORDER
				/* $cjService   = new CjDropshippingService();
				$result		 = $cjService->createCjOrdersFromLocalOrder($orderInfo->id); */
				
				#AUTO-DS DROPSHIPPING
				$autoDsService = new AutoDSService();
				$autoDsService->createAutoDSOrdersFromLocalOrder($orderInfo->id);
        	    
        		Mail::send('emails.order-receipt', ['order' => $orderInfo], function ($message) use ($orderInfo) {
        			$message->to([$orderInfo->email ?? '', get_admin_email(),get_vendor_email($orderInfo->vendor_id)])
        					->subject('Your Order Receipt - #' . $orderInfo->order_number);
        		});
           }
    		
    		$orderTotalAmt = $orderTotalAmt+$finalTotal;
    		$orderIds[] = $order->id;
    		
        }
        
        $paytype = '';
        
        if($request->payment_method=='cod'){
		  
    	   $user ? CartItem::where('user_id', $userId)->delete() : session()->forget('cart');
    	   
    	   $paytype = 'cod';
    	   
		}elseif($request->payment_method=='paypal'){
			
			$paytype = 'paypal';
			
		}elseif($request->payment_method=='stripe'){
			
			$paytype = 'stripe';
			
		}elseif($request->payment_method=='emis'){
			
			$paytype = 'emis';
			
		}elseif ($request->payment_method == 'wallet') {
			
			$paytype = 'wallet';
			
			#WALLET PAYMENT
			$user   = auth()->user();
			$amount = $orderTotalAmt;

			$wallet = $user->wallet()->firstOrCreate([
				'user_id' => $user->id,
			], [
				'balance' => 0,
			]);

			if ($wallet->balance < $amount) {
				return back()->with('error', 'Insufficient wallet balance.');
				return response()->json([
                    'success'      => true,
                    'message'      => 'Insufficient wallet balance.',
                ]);
			}
			$oldBalance       = $wallet->balance;
			$wallet->balance -= $amount;
			$wallet->save();

			$transactionId = 'TXN-' . strtoupper(uniqid()) . '-' . mt_rand(1000, 9999);

			WalletHistory::create([
				'currency'       => Currency::where('symbol',$currency)->first()->code ?? "USD",
				'wallet_id'      => $wallet->id ,
				'user_id'        => $user->id,
				'transaction_id' => $transactionId,
				'amount'         => $amount,
				'type'           => 'debit',
				'status'         => 'completed',
				'method'         => 'wallet',
				'old_balance'    => $oldBalance,
				'new_balance'    => $wallet->balance,
				'remarks'        => 'Payment for order via wallet',
			]);
			
			foreach ($orderIds as $order_id) {
                
                #CREATE CJ DROPSHIPPING ORDER
				/* $cjService   = new CjDropshippingService();
				$result		 = $cjService->createCjOrdersFromLocalOrder($order_id); */
				
				#AUTO-DS DROPSHIPPING
				$autoDsService = new AutoDSService();
				$autoDsService->createAutoDSOrdersFromLocalOrder($order_id);
                
				$orderDetails = Order::find($order_id);
				
				if ($orderDetails) {
					$orderDetails->payment_method          = 'Wallet';
					$orderDetails->payment_status          = 'paid';
					$orderDetails->order_status            = 'confirmed';
					$orderDetails->payment_transaction_id  = $transactionId;
					$orderDetails->save();
					
					$orderInfo = Order::with('orderProduct.product.userInfo', 'orderTotal')->where('id',$order_id)->first();
					Mail::send('emails.order-receipt', ['order' => $orderInfo], function ($message) use ($orderInfo) {
						$message->to([$orderInfo->email ?? '', get_admin_email(),get_vendor_email($orderInfo->vendor_id)])
								->subject('Your Order Receipt - #' . $orderInfo->order_number);
					});
				}
			}
			
			#COUPON
			session()->forget('coupon');	
			
			$user ? CartItem::where('user_id', $userId)->delete() : session()->forget('cart');
		}
        
    	#SEND NOTIFICATIONS
        sendFirebaseNotification($userId,'New Order Placed','Order placed successfully.');
           
        return response()->json([
            'success'      => true,
            'message'      => 'Order placed successfully',
            'order_id'     => $order->id,
            'order_number' => $order->order_number,
            'pay_type'     => $paytype,
            'order'        => implode(',',$orderIds),
        ]);
    }
    
    private function checkApplyCoupon($coupon_id)
	{  
	
		$coupon = Coupon::where('id', $coupon_id)
			->where('is_active', 1)
			->where(function ($q) {
				$now = now();
				$q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
			})
			->where(function ($q) {
				$now = now();
				$q->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
			})
			->first();
      
		$cart = Auth::check()
			? CartItem::with('product')->where('user_id', Auth::id())->get()
			: collect(session()->get('cart', []));

		$total    = 0;
		$discount = 0;
       
		if ($coupon->vendor_id) {

			$vendorTotal = 0;

			foreach ($cart as $item) {
				if (Auth::check()) {
					$product  = $item->product;
					$price    = $item->price;
					$quantity = $item->quantity;
				} else {
					$product  = Product::find($item['product_id']);
					$price    = $item['price'];
					$quantity = $item['quantity'];
				}

				if ($product && $product->seller_id == $coupon->vendor_id) {
					$vendorTotal += ($price * $quantity);
				}
			}

			if ($coupon->type === 'fixed') {
				$discount = min($coupon->value, $vendorTotal);
			} else {
				$discount = ($vendorTotal * $coupon->value) / 100;
			}
			
		} else {

			foreach ($cart as $item) {
				if (Auth::check()) {
					$price    = $item->price;
					$quantity = $item->quantity;
				} else {
					$price    = $item['price'];
					$quantity = $item['quantity'];
				}

				$total += ($price * $quantity);
			}

			if ($coupon->type === 'fixed') {
				$discount = min($coupon->value, $total);
			} else {
				$discount = ($total * $coupon->value) / 100;
			}
		}

		$dataRes = array(
		    'id'        => $coupon->id,
			'code'      => $coupon->code,
			'discount'  => $discount,
			'vendor_id' => $coupon->vendor_id,
		 );
		 
		return $dataRes;
	}
    
    private function buildVariantText($variantIds)
	{
		if (empty($variantIds)) return null;

		$variantTextParts = [];

		foreach ($variantIds as $type =>$valueId) {
			$variant = ProductVariant::with('attributeValue')->where('value', $valueId)->first();

			 if ($variant && $variant->attributeValue) {
				$variantTextParts[] = ucfirst($type) . ': ' . $variant->attributeValue->value . ' ';
			}
		}

		return implode(', ', $variantTextParts);
	}

 public function addToCart(Request $request)
    {   
        
        $user_id=auth()->user()->id;
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'price'     => 'required|numeric|min:0.01',
        ]);
      
      $cart = CartItem::where('user_id', $user_id)
                ->where('product_id', $request->product_id)
                ->first();
        
    if ($cart) {
        $cart->quantity += $request->quantity;
        $cart->save();
    } else {
        $cart = CartItem::create([
            'user_id'              => $user_id,
            'product_id'           => $request->product_id, 
            'quantity'             => $request->quantity,
            'price'                => $request->price,
            //'attribute_id'         => $request->attribute_id ?? "",
            //'attribute_value_id'   => $request->attribute_value_id ?? "",
            'variants'             => $request->input('variants', []),
        ]);
    }
        
        return response()->json(['success' => true, 'message' => 'Product added to cart', 'data' => $cart]);
    }

    public function increaseQty(Request $request){
        
        $user_id=auth()->user()->id;
        $request->validate([
            'cart_id'    => 'required|exists:cart_items,id',
            'quantity'   => 'required|integer|min:1',
        ]);
          
        $cart = CartItem::where('id', $request->cart_id)->first();
        
        if ($cart) {
            $cart->quantity += $request->quantity;
            $cart->save();
        }
        return response()->json(['success' => true, 'message' => 'Your cart hase been successfully updated', 'data' => $cart]);
    }
    
    public function getCart()
    {
        $user_id = auth()->user()->id;
        $cartItems = CartItem::with('product')->where('user_id', $user_id)->get();
        
        foreach ($cartItems as $keys=>$item) {
			$product = $item->product;

			if (!$product) continue;

            $cartItems[$keys]->vendor         = $product->userInfo->name ?? 'N/A';
			$cartItems[$keys]->origin         = $product->type ?? 'unknown';
			$cartItems[$keys]->variant_text   = $this->getVariantText($item->variants ?? []);
		}
        
            
        return response()->json(['success' => true, 'cart' => $cartItems]);
    }
   
   private function getVariantText($variants = [])
    {
        $text = '';

        if (!empty($variants)) {
            foreach ($variants as $type => $valueId) {
                $variant = ProductVariant::with('attributeValue')->where('value', $valueId)->first();
                if ($variant && $variant->attributeValue) {
                    $text .= ucfirst($type) . ': ' . $variant->attributeValue->value . ', ';
                }
            }
        }

        return rtrim($text, ', ');
    }
   
  public function deleteFromCart($id)
    {
        CartItem::where('id',$id)->delete();

        return response()->json(['success' => true, 'message' => 'Product removed from cart']);
    }

 public function addToWishlist(Request $request)
    {
         $user_id=auth()->user()->id;

         $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $exists = Wishlist::where('user_id', $user_id)
                          ->where('product_id', $request->product_id)
                          ->first();

        if ($exists) {
            return response()->json(['message' => 'Product already in wishlist'], 200);
        }

        $wishlist = Wishlist::create([
            'user_id'    => $user_id,
            'product_id' => $request->product_id,
        ]);
        
        #SEND NOTIFICATIONS
        sendFirebaseNotification($user_id,'Wishlist ','Added to product in wishlist.');
        
        
        return response()->json(['message' => 'Added to wishlist', 'data' => $wishlist], 201);
    }

    //  Get Wishlist
    public function getWishlist()
    {
         $user_id=auth()->user()->id;

        $wishlist = Wishlist::with('product')->where('user_id', $user_id)->get();
        return response()->json(['wishlist' => $wishlist]);
    }

    public function removeFromWishlist($id)
    {
 
        Wishlist::where('id', $id)->delete();

        return response()->json(['message' => 'Removed from wishlist']);
    }

    #vendor Api Code start

      /* public function vendor_products(Request $request)
        {	
			$perPage = $request->input('limit', 10);
			
            $user_id=auth()->user()->id;
            $products = Product::with('categories','attributes','stores','brand','galleryImages')->where('seller_id',$user_id)->orderBy('id', 'desc')->paginate($perPage);
                return response()->json([
                'status'=>true,
                'gallery_images_path'=>'/uploads/product/gallery/',
                'message' => 'products data',
                'products'    => $products,
                ], 200);
        } */
		
		
	public function vendor_products(Request $request)
	{
		$perPage = $request->input('limit', 10);
		$searchTerm = $request->input('search');

		$user_id = auth()->user()->id;

		$productsQuery = Product::with(['categories', 'attributes', 'stores', 'brand', 'galleryImages'])
			->where('seller_id', $user_id)
			->orderBy('id', 'desc');

		if ($searchTerm) {
			$productsQuery->where('name', 'like', '%' . $searchTerm . '%');
		}

		$products = $productsQuery->paginate($perPage);

		return response()->json([
			'status' => true,
			'gallery_images_path' => '/uploads/product/gallery/',
			'message' => 'products data',
			'products' => $products,
		], 200);
	}



public function create_store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'store_name'  => 'required|string|max:255|unique:vendor_stores,store_name',
        'description' => 'nullable|string',
        'logo'        => 'nullable|image|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    $store = new VendorStore;
    $store->user_id     = auth()->id();
    $store->store_name  = $request->store_name;
    $store->slug        = Str::slug($request->store_name);
    $store->description = $request->description;
    $store->status      = $request->status ?? 1;

    if ($request->hasFile('logo')) {
        $imageName = time() . '.' . $request->logo->extension();
        $request->logo->move(public_path('uploads/store/'), $imageName);
        $store->logo = $imageName;
    }

    $store->save();
        return response()->json([
            'status' => true,
            'message' => 'Store created successfully.',
            'data' => $store
        ], 201);
     }

	public function vendor_stores(Request $request) {
		 $stores = VendorStore::with('user')->where('user_id',auth()->user()->id)->orderBy('id', 'desc')->paginate($request->limit);
         return response()->json([
        'status' => true,
        'message' => 'Vendor Stores.',
        'data' => $stores
    ], 201);
    }

    public function vendor_store_details($id) {

        $store   = VendorStore::where('id',$id)->firstOrFail();
        return response()->json([
            'status' => true,
            'message' => 'Vendor Store.',
            'data' => $store
        ], 201);
       }
       
    public function delete_store($id) {

        VendorStore::where('id',$id)->delete();
        return response()->json([
            'status' => true,
            'message' => 'Vendor Store Deleted Successfully.',
        ], 201);
       }

public function update_store(Request $request, $id)
   {
     $store = VendorStore::find($id);

    if (!$store) {
        return response()->json([
            'status' => false,
            'message' => 'Store not found.'
        ], 404);
    }

    $validator = Validator::make($request->all(), [
        'store_name'  => 'required|string|max:255|unique:vendor_stores,store_name,' . $id,
        'description' => 'nullable|string',
        'logo'        => 'nullable|image',
        'status'      => 'nullable|in:0,1',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    $store->store_name  = $request->store_name;
    $store->slug        = Str::slug($request->store_name);
    $store->description = $request->description;
    $store->status      = $request->status ?? $store->status;

    if ($request->hasFile('logo')) {
        if ($store->logo && file_exists(public_path('uploads/store/' . $store->logo))) {
            unlink(public_path('uploads/store/' . $store->logo));
        }
        $imageName = time() . '.' . $request->logo->extension();
        $request->logo->move(public_path('uploads/store/'), $imageName);
        $store->logo = $imageName;
    }

    $store->save();

    return response()->json([
        'status' => true,
        'message' => 'Store updated successfully.',
        'data' => $store
    ], 200);

}


public function product_add(Request $request) {

    $validator = Validator::make($request->all(), [
        'product_name'                            => ['required', 'string', 'max:255'],
        'meta_title'                              => ['nullable', 'string', 'max:255'],
        'meta_keyword'                            => ['nullable', 'string', 'max:255', 'unique:products,meta_keyword'],
        'meta_description'                        => ['nullable', 'string', 'max:500'],
        'sku'                                     => 'required|string|unique:products,sku|max:100',
        'price'                                   => ['required', 'numeric', 'min:1'],
        'quantity'                                => ['required', 'integer', 'min:1'],
        'category_ids'                            => 'required|array',
        'category_ids.*'                          => 'exists:categories,id',
        'store_ids'                               => ['required', 'array'],
        'store_ids.*'                             => ['exists:vendor_stores,id'],
        'short_description'                       => ['nullable', 'string', 'max:255'],
        'description'                             => ['nullable', 'string'],
        'image'                                   => ['required','image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        'galler_image.*'                          => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        'attributes'                              => 'nullable|array',
        'attributes.*.attribute_id'               => 'required_with:attributes.*|exists:attributes,id',
        'attributes.*.variants'                   => 'required_with:attributes.*.attribute_id|array|min:1',
        'attributes.*.variants.*.value'           => 'required_with:attributes.*.variants|string|max:255',
        'attributes.*.variants.*.price'           => 'required_with:attributes.*.variants|numeric|min:0',
        'attributes.*.variants.*.sku'             => 'required_with:attributes.*.variants|string|max:255|unique:product_variants,sku',
        'attributes.*.variants.*.stock'           => 'required_with:attributes.*.variants|integer|min:0',
        'attributes.*.variants.*.image'           => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'attributes.*.variants.*.existing_image'  => ['nullable', 'string', 'max:255'],
    ]);

    if ($validator->fails()) {
        return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
    }

    $user = auth()->user();

    $product = new Product;
    $product->seller_id          = $user->id;
    $product->name               = $request->product_name;
    $product->slug               = Str::slug($request->product_name);
    $product->meta_title         = $request->meta_title;
    $product->meta_keyword       = $request->meta_keyword;
    $product->meta_description   = $request->meta_description;
    $product->sku                = $request->sku;
    $product->price              = $request->price;
    $product->quantity           = $request->quantity;
    $product->short_description  = $request->short_description;
    $product->description        = $request->description;
    $product->type               = $request->type;
    $product->brand_id           = $request->brand_id;

    // Product image
    if ($request->hasFile('image')) {
        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('uploads/products/'), $imageName);
        $product->image = $imageName;
    }

    $product->save();

    // Categories & Stores
    $product->categories()->attach($request->category_ids);
    $product->stores()->sync($request->store_ids);

    // Attributes & Variants
    if ($request->has('attributes') && is_array($request->attributes)) {
        $this->saveAttributesAndVariants($product, $request->attributes);
    }

    // Gallery Images
    if ($request->hasFile('galler_image')) {
        foreach ($request->file('galler_image') as $image) {
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/product/gallery'), $imageName);

            ProductGallery::create([
                'product_id' => $product->id,
                'image'      => $imageName,
            ]);
        }
    }

    return response()->json([
        'status' => true,
        'message' => 'Product created successfully.',
        'product_id' => $product->id
    ]);
  }

	private function saveAttributesAndVariants(Product $product, array $attributes) {

        foreach ($product->attributes as $oldAttribute) {
            $oldAttribute->variants()->delete();
            $oldAttribute->delete();
        }

        foreach ($attributes as $attributeData) {
            $productAttribute = ProductAttribute::create([
                'product_id' => $product->id,
                'attribute_id' => $attributeData['attribute_id'],
            ]);

            foreach ($attributeData['variants'] as $variant) {
				
                $imageName = $variant['existing_image'] ?? null;
                if (isset($variant['image']) && $variant['image'] instanceof \Illuminate\Http\UploadedFile) {
                    if (!empty($variant['existing_image'])) {
                        $oldPath = public_path('uploads/variant_images/' . $variant['existing_image']);
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }
                    $imageName = time() . '_' . uniqid() . '.' . $variant['image']->extension();
                    $variant['image']->move(public_path('uploads/variant_images/'), $imageName);
                }

                ProductVariant::create([
                    'product_id'             => $product->id,
                    'product_attribute_id'   => $productAttribute->id,
                    'value'                  => $variant['value'],
                    'price'                  => $variant['price'],
                    'sku'                    => $variant['sku'],
                    'stock'                  => $variant['stock'],
                    'image'                  => $imageName,
                ]);
            }
        }
    }

  public function product_Update(Request $request, string $id)
   {
     try {
        $validated = $request->validate([
            'product_name'                           => ['required', 'string', 'max:255'],
            'meta_title'                             => ['nullable', 'string', 'max:255'],
            'meta_keyword'                           => ['nullable', 'string', 'max:255','unique:products,meta_keyword,' . $id],
            'meta_description'                       => ['nullable', 'string', 'max:500'],
            'sku'                                     => 'required|string|unique:products,sku,' . $id,
            'price'                                   => ['required', 'numeric', 'min:1'],
            'quantity'                                => ['required', 'integer', 'min:1'],
            'category_ids'                            => 'required|array',
            'category_ids.*'                          => 'exists:categories,id',
            'store_ids'                               => ['required', 'array'],
            'store_ids.*'                             => ['exists:vendor_stores,id'],
            'short_description'                       => ['nullable', 'string', 'max:255'],
            'description'                             => ['nullable', 'string'],
            'image'                                   => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'attributes'                              => 'nullable|array',
            'attributes.*.attribute_id'               => 'required_with:attributes.*|exists:attributes,id',
            'attributes.*.variants'                   => 'required_with:attributes.*.attribute_id|array|min:1',
            'attributes.*.variants.*.value'           => 'required_with:attributes.*.variants|string|max:255',
            'attributes.*.variants.*.price'           => 'required_with:attributes.*.variants|numeric|min:0',
            'attributes.*.variants.*.sku'             => 'required_with:attributes.*.variants|string|max:255',
            'attributes.*.variants.*.stock'           => 'required_with:attributes.*.variants|integer|min:0',
            'attributes.*.variants.*.image'           => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'attributes.*.variants.*.existing_image'  => ['string', 'max:255'],
        ]);

        $product = Product::findOrFail($id);
        $product->seller_id          = Auth::id();
        $product->name               = $request->product_name;
        $product->slug               = Str::slug($request->product_name);
        $product->meta_title         = $request->meta_title;
        $product->meta_keyword       = $request->meta_keyword;
        $product->meta_description   = $request->meta_description;
        $product->sku                = $request->sku;
        $product->price              = $request->price;
        $product->quantity           = $request->quantity;
        $product->short_description  = $request->short_description;
        $product->description        = $request->description;
        $product->type               = $request->type;
        $product->brand_id           = $request->brand_id;

        if ($request->hasFile('image')) {
            if ($product->image && File::exists(public_path('/uploads/products/' . $product->image))) {
                File::delete(public_path('/uploads/products/' . $product->image));
            }

            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/products/'), $imageName);
            $product->image = $imageName;
        }

        $product->save();

        $product->categories()->sync($request->category_ids);
        $product->stores()->sync($request->store_ids);

        foreach ($product->attributes as $oldAttribute) {
            $oldAttribute->variants()->delete();
            $oldAttribute->delete();
        }

        if (isset($validated['attributes'])) {
            $this->saveAttributesAndVariants($product, $validated['attributes']);
        }

        if ($request->hasFile('galler_image')) {
            foreach ($request->file('galler_image') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/product/gallery'), $imageName);

                ProductGallery::create([
                    'product_id' => $product->id,
                    'image' => $imageName,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'product' => $product,
        ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $th->getMessage(),
            ], 500);
        }
    }

      public function product_delete($id) { 
	    Product::where('id', $id)->delete();
	        return response()->json([
                'status'=>true,
                'message' => 'product deleted successfully',
               ], 200);
         }

   public function productupdateStatus(Request $request) {
		
        $product = Product::find($request->id);

        if ($product) {
            $product->status = $request->status;
            $product->save();

                return response()->json([
                'status'=>true,
                'message' => 'status update successfully',
               ], 200);
        }

          return response()->json([
                'status'=>false,
                'message' => 'failed',
               ], 404);
    }

    
     public function brands() { 
	     $data = Brands::where('status', '1')->orderBy('id', 'desc')->get();
	        return response()->json([
                'status'=>true,
                'message' => ' brands data',
                'data' => $data,
               ], 200);
         }

public function decreaseQuantity(Request $request)
{
    $user_id = auth()->user()->id;

    $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity'   => 'required|integer|min:1',
    ]);

    $cart = CartItem::where('user_id', $user_id)
                ->where('product_id', $request->product_id)
                ->first();

    if (!$cart) {
        return response()->json(['success' => false, 'message' => 'Product not found in cart']);
    }

    if ($cart->quantity <= $request->quantity) {
        // Agar quantity zero ya negative ho jaaye to item delete kar do
        $cart->delete();
        return response()->json(['success' => true, 'message' => 'Product removed from cart']);
    }

    // Quantity decrease karo
    $cart->quantity -= $request->quantity;
    $cart->save();

    return response()->json(['success' => true, 'message' => 'Product quantity decreased', 'data' => $cart]);
}


 public function attribute_create(Request $request) {
		
		$data = $request->validate([
			'name'       => 'required|string|unique:attributes,name',	
			'values'     => 'required|array|min:1',
            'values.*'   => 'required|string'
		]);
		
		$attribute = new Attribute;
		$attribute->name     = $request->name;
		$attribute->save();
		
		foreach ($request->values as $value) {
			$attribute->values()->create([
				'value' => $value,
			]);
		}
            return response()->json([
            'status'=>true,
            'message' => ' Add Successfully',
            ], 200);
     }

    public function attribute_details($id) {
        $attributes  = Attribute::findOrFail($id);
         return response()->json([
            'status'=>true,
            'message' => 'attributes',
            'data' => $attributes,
            ], 200);
        }  

      public function attributes() {
			$attributes = Attribute::with('values')->orderBy('id', 'desc')->get();
            return response()->json([
            'status'=>true,
            'message' => 'attributes',
            'data' => $attributes,
            ], 200);
        }

     public function update_attribute(Request $request, string $id) {

			$data = $request->validate([
				'name'      => 'required|string|unique:attributes,name,'.$id,
                'values'    => 'required|array|min:1',
				'values.*'  => 'required|string',
				'value_ids' => 'required|array',				
			]);
			
			$attribute = Attribute::findOrFail($id);
			$attribute->name        = $request->name;
			$attribute->save();
			
			$existingIds = $attribute->values()->pluck('id')->toArray();
			$submittedIds = array_filter($request->value_ids); 

			$idsToDelete = array_diff($existingIds, $submittedIds);
			
			if (!empty($idsToDelete)) {
				AttributeValue::destroy($idsToDelete);
			}

			foreach ($request->values as $index => $val) {
				$valueId = $request->value_ids[$index];

				if ($valueId && $valueId != 0) {
					AttributeValue::where('id', $valueId)->update([
						'value' => $val,
					]);
				} else {
					$attribute->values()->create([
						'value' => $val,
					]);
				}
			}
			
		    return response()->json([
            'status'=>true,
            'message' => 'attributes update successfully',
            ], 200);
    }

    public function currency(){
			$currency = Currency::orderBy('id','DESC')->get();
            return response()->json([
            'status'=>true,
            'message' => 'currency',
            'data' => $currency,
            ], 200);
        }

       public function add_currency(Request $request){ 
     
		$request->validate([
            'code'       => 'required|unique:currencies,code',
            'symbol'     => 'required',
            'rate'       => 'required|numeric',
            'is_default' => 'nullable|boolean'
        ]);
		
		if ($request->is_default) {
            Currency::where('is_default', true)->update(['is_default' => false]);
        }

		$currency = new Currency;
		$currency->code          = $request->code;
		$currency->symbol        = $request->symbol;
		$currency->rate          = $request->rate;
		$currency->is_default    = $request->is_default;
		$currency->save();
		
		if (empty($request->is_default)) {
			$totalCounts = Currency::where('is_default', true)->count();
            if($totalCounts==0){
				$currencyInfo = Currency::first();
				$currencyInfo->is_default = true;
				$currencyInfo->save();
			}
        }
           return response()->json([
            'status'=>true,
            'message' => 'currency add successfully',
            ], 200);

    }
	
     public function userAddress(Request $request){
		$address = Address::where('user_id',auth()->id())->get();
		  return response()->json([
            'status'=>true,
            'message' => 'users address',
            'data' => $address,
            ], 200);
	    }

    public function get_countryandstate(Request $request) {
        $country = Country::all();
        $state   = State::where('country_id', '101')->get();

        return response()->json([
            'status' => true,
            'message' => 'Data fetched successfully.',
            'data' => [
                'countries' => $country,
                'states' => $state
            ]
        ]);
       }

public function add_address_action(Request $request) {
    $request->validate([
        'firstname' => 'required|string',
        'lastname' => 'required|string',
        'company' => 'nullable|string',
        'country' => 'required|string',
        'state' => 'required|string',
        'address_1' => 'required|string',
        'address_2' => 'nullable|string',
        'city' => 'required|string',
        'zipcode' => 'required|string',
        'phone' => 'required|string',
    ]);

    Address::where('user_id', auth()->id())->update([
        "is_default" => '0',
        "is_billing" => '0',
        "is_shipping" => '0'
    ]);

    $address = new Address;
    $address->user_id = auth()->id();
    $address->first_name = $request->firstname;
    $address->last_name = $request->lastname;
    $address->company = $request->company;
    $address->country = $request->country;
    $address->state = $request->state;
    $address->address_1 = $request->address_1;
    $address->address_2 = $request->address_2;
    $address->city = $request->city;
    $address->zipcode = $request->zipcode;
    $address->phone = $request->phone;
    $address->is_default = $request->is_default ?? '1';
    $address->is_billing = $request->is_billing ?? '1';
    $address->is_shipping = $request->is_shipping ?? '1';
    $address->save();
    
    #SEND NOTIFICATIONS
    sendFirebaseNotification(auth()->id(),'Add New Address ','Address added successfully.');
    
    return response()->json([
        'status' => true,
        'message' => 'Address added successfully.',
        'data' => $address
    ]);
}

public function deleteAddress(Request $request, $id) {
    Address::where('user_id', auth()->id())->update([
        "is_default" => '0',
        "is_billing" => '0',
        "is_shipping" => '0'
    ]);

    Address::where('user_id', auth()->id())->where('id', $id)->delete();

    $lastAddress = Address::where('user_id', auth()->id())->latest()->first();
    if ($lastAddress) {
        $lastAddress->is_default = '1';
        $lastAddress->is_billing = '1';
        $lastAddress->is_shipping = '1';
        $lastAddress->save();
    }
    
    #SEND NOTIFICATIONS
    sendFirebaseNotification(auth()->id(),'Delete Address ','Address deleted successfully.');
    
    return response()->json([
        'status' => true,
        'message' => 'Address deleted successfully.',
        'data' => $lastAddress
    ]);
}

public function editAddress(Request $request, $id) {
    $country = Country::all();
    $state = State::where('country_id', '101')->get();
    $address = Address::where('user_id', auth()->id())->where('id', $id)->first();

    if (!$address) {
        return response()->json(['status' => false, 'message' => 'Address not found'], 404);
    }

    return response()->json([
        'status' => true,
        'message' => 'Address fetched successfully.',
        'data' => [
            'address' => $address,
            'countries' => $country,
            'states' => $state
        ]
    ]);
}

public function update_address_action(Request $request) {
    $request->validate([
        'address_id' => 'required|integer|exists:address,id',
        'firstname' => 'required|string',
        'lastname' => 'required|string',
        'company' => 'required|string',
        'country' => 'required|string',
        'state' => 'required|string',
        'address_1' => 'required|string',
        'address_2' => 'required|string',
        'city' => 'required|string',
        'zipcode' => 'required|string',
        'phone' => 'required|string',
    ]);

    $address = Address::where('user_id', auth()->id())->where('id', $request->address_id)->first();
    if (!$address) {
        return response()->json(['status' => false, 'message' => 'Address not found'], 404);
    }

    $address->first_name = $request->firstname;
    $address->last_name = $request->lastname;
    $address->company = $request->company;
    $address->country = $request->country;
    $address->state = $request->state;
    $address->address_1 = $request->address_1;
    $address->address_2 = $request->address_2;
    $address->city = $request->city;
    $address->zipcode = $request->zipcode;
    $address->phone = $request->phone;
    $address->is_default = $request->is_default ?? '1';
    $address->is_billing = $request->is_billing ?? '1';
    $address->is_shipping = $request->is_shipping ?? '1';
    $address->save();

    return response()->json([
        'status' => true,
        'message' => 'Address updated successfully.',
        'data' => $address
    ]);
}

public function update_action(Request $request) {
    $request->validate([
        'address_id' => 'required|integer|exists:address,id',
    ]);

    Address::where('user_id', auth()->id())->update([
        "is_default" => '0',
        "is_billing" => '0',
        "is_shipping" => '0'
    ]);

    $address = Address::where('user_id', auth()->id())->where('id', $request->address_id)->first();
    if (!$address) {
        return response()->json(['status' => false, 'message' => 'Address not found'], 404);
    }

    $address->is_default = $request->is_default ?? '0';
    $address->is_billing = $request->is_billing ?? '1';
    $address->is_shipping = $request->is_shipping ?? '1';
    $address->save();

    return response()->json([
        'status' => true,
        'message' => 'Address status updated successfully.',
        'data' => $address
    ]);
}

	public function orders(Request $request) { 
	
		$perPage     = $request->input('limit', 10);
		$searchTerm  = $request->input('search');
	
	    /* $orders = $order = Order::with('orderProduct.product','orderTotal')->where('user_id', auth()->id())->orderBy('id','DESC')->paginate($request->limit); */
		
		$ordersQuery = Order::with('orderProduct.product.userInfo', 'orderTotal')
			->where('user_id', auth()->id())
			->orderBy('id', 'DESC');
			
		if (!empty($searchTerm)) {
			$ordersQuery->where(function ($query) use ($searchTerm) {
				$query->where('order_number', 'like', "%{$searchTerm}%")
					  ->orWhere('order_status', 'like', "%{$searchTerm}%")
					  ->orWhereHas('orderProduct.product', function ($q) use ($searchTerm) {
						  $q->where('name', 'like', "%{$searchTerm}%");
					  });
			});
		}

		$orders = $ordersQuery->paginate($perPage);
		
		return response()->json([
			'status'  => true,
			'message' => 'user orders.',
			'data'    => $orders
		]);
    }

    public function orders_details($id) { 
	    $orders = $order = Order::with('orderProduct.product','orderTotal')->where('id',$id)->first();
		    return response()->json([
        'status' => true,
        'message' => 'user order details.',
        'data' => $orders
    ]);
    }

   public function vendor_dashboard(){
        $store_count = VendorStore::where('user_id',auth()->user()->id)->count();
        $product_count = Product::where('seller_id',auth()->user()->id)->count();
        $attribute_count = Attribute::count();
        $orders = Order::count();
        $category =  category::count();

		 return response()->json([
        'status' => true,
        'message' => 'Dashboard Data',
        'store_count' => $store_count,
        'product_count' => $product_count,
        'attribute_count' => $attribute_count,
        'orders' => $orders,
        'category' => $category,
        'revenue' => 0,
    ]);
    }
	
	
	public function applyCoupon(Request $request)
	{  
		$request->validate([
            'code' => 'required|exists:coupons,code',
        ]);
        
		$code   = $request->input('code');
		
		$coupon = Coupon::where('code', $code)
			->where('is_active', 1)
			->where(function ($q) {
				$now = now();
				$q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
			})
			->where(function ($q) {
				$now = now();
				$q->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
			})
			->first();

		if (!$coupon) {
			return response()->json([
                'status'  => false,
                'message' => "Invalid or expired coupon.",
            ], 200);
		}

		$cart = Auth::check()
			? CartItem::with('product')->where('user_id', Auth::id())->get()
			: collect(session()->get('cart', []));

		if ($cart->isEmpty()) {
			return response()->json([
                'status'  => true,
                'message' => "Your cart is empty.",
            ], 200);
		}

		$total    = 0;
		$discount = 0;
		
		#GLOBAL USAGE LIMIT (max_uses)
		if (!is_null($coupon->max_uses)) {
			$usedCount = Order::where('coupon_id', $coupon->id)->count();
			if ($usedCount >= $coupon->max_uses) {
				return response()->json([
					'status'  => true,
					'message' => "This coupon has reached its maximum usage limit.",
				], 200);
			}
		}
		#PER USER LIMIT (max_uses_per_user)
		if (Auth::check() && !is_null($coupon->max_uses_per_user)) {
			$userUsedCount = Order::where('coupon_id', $coupon->id)
				->where('user_id', Auth::id())
				->count();

			if ($userUsedCount >= $coupon->max_uses_per_user) {
				return response()->json([
					'status'  => true,
					'message' => "You have already used this coupon maximum allowed times.",
				], 200);
			}
		}
		
		if ($coupon->vendor_id) {

			$vendorTotal = 0;

			foreach ($cart as $item) {
				if (Auth::check()) {
					$product  = $item->product;
					$price    = $item->price;
					$quantity = $item->quantity;
				} else {
					$product  = Product::find($item['product_id']);
					$price    = $item['price'];
					$quantity = $item['quantity'];
				}

				if ($product && $product->seller_id == $coupon->vendor_id) {
					$vendorTotal += ($price * $quantity);
				}
			}

			if ($coupon->type === 'fixed') {
				$discount = min($coupon->value, $vendorTotal);
			} else {
				$discount = ($vendorTotal * $coupon->value) / 100;
			}
			
			$total = $vendorTotal;
			
			
		} else {

			foreach ($cart as $item) {
				if (Auth::check()) {
					$price    = $item->price;
					$quantity = $item->quantity;
				} else {
					$price    = $item['price'];
					$quantity = $item['quantity'];
				}

				$total += ($price * $quantity);
			}

			if ($coupon->type === 'fixed') {
				$discount = min($coupon->value, $total);
			} else {
				$discount = ($total * $coupon->value) / 100;
			}
		}
		
		#MINIMUM ORDER AMOUNT CHECK
		if (!is_null($coupon->min_order_amount) && formatCurrencyPriceCalculate($total) < $coupon->min_order_amount) {
			return response()->json([
					'status'  => true,
					'message' => "Minimum order amount for this coupon is " . $coupon->min_order_amount,
				], 200);
		}
		
		$dataRes = array(
		    'coupon_id' => $coupon->id,
			'code'      => $coupon->code,
			'discount'  => $discount,
			'vendor_id' => $coupon->vendor_id,
		 );
		 
		  #SEND NOTIFICATIONS
		  if(!empty(auth()->id())) {
            sendFirebaseNotification(auth()->id(),'Coupon applied ','Coupon applied successfully.');
		  }
		 
		return response()->json([
            'status'  => true,
            'message' => "Coupon applied successfully.",
            'data'    => $dataRes,
        ], 200);

	}
	
	public function createVendorCoupons(Request $request)
    {   
       
		$data = $request->validate([
            'code'               => [
                'required',
                'string',
                Rule::unique('coupons')->where(function ($query) use ($request) {
                    return $query->where('vendor_id', auth()->id());
                }),
            ],
            'type'               => 'required|in:fixed,percentage',
            'value'              => 'required|numeric|min:0',
            'min_order_amount'   => 'nullable|numeric|min:0',
            'max_uses'           => 'nullable|integer|min:1',
            'max_uses_per_user'  => 'nullable|integer|min:1',
            'starts_at'          => 'nullable|date',
            'expires_at'         => 'nullable|date|after_or_equal:starts_at',
            'is_active'          => 'required|boolean',
        ]);
        
        $coupon = new Coupon();
        $coupon->vendor_id         = auth()->id();
        $coupon->code              = $data['code'];
        $coupon->type              = $data['type'];
        $coupon->value             = $data['value'];
        $coupon->min_order_amount  = $data['min_order_amount'] ?? null;
        $coupon->max_uses          = $data['max_uses'] ?? null;
        $coupon->max_uses_per_user = $data['max_uses_per_user'] ?? null;
        $coupon->starts_at         = $data['starts_at'] ?? null;
        $coupon->expires_at        = $data['expires_at'] ?? null;
        $coupon->is_active         = $data['is_active'];
        $coupon->save();
        
        #SEND NOTIFICATIONS
		sendFirebaseNotification(auth()->id(),'Create Coupon','You have created coupon successfully.');
        
        return response()->json([
            'status'  => true,
            'message' => "You have created coupon successfully.",
            'data'   => $coupon,
        ], 200);
		
    } 

	public function editVendorCoupons($id)
    { 
        $coupon = Coupon::where('vendor_id',auth()->id())->where('id',$id)->first();
        
        return response()->json([
            'status'  => true,
            'message' => "You have fetch coupon details successfully.",
            'data'   => $coupon,
        ], 200);
    }
    
    public function deleteVendorCoupons($id)
    { 
        $coupon = Coupon::where('vendor_id',auth()->id())->where('id',$id)->first();
        
        if(!empty($coupon)) {
            $coupon->delete();
            
            #SEND NOTIFICATIONS
		    sendFirebaseNotification(auth()->id(),'Delete Coupon','You have deleted successfully.');
            
            return response()->json([
                'status'  => true,
                'message' => "You have deleted successfully.",
            ], 200);
        }else{
            
            #SEND NOTIFICATIONS
		    sendFirebaseNotification(auth()->id(),'Coupon Not Exists ','"Coupon id does not exixts.');
            
            return response()->json([
                'status'  => true,
                'message' => "Coupon id does not exixts!.",
            ], 200);
        }
        
    }
    
	public function updateVendorCoupons(Request $request)
    {   
       
		$data = $request->validate([
            'coupon_id'         => 'required|integer|exists:coupons,id',
            'code'               => [
				'required',
				'string',
				Rule::unique('coupons')->where(function ($query) use ($request) {
					return $query->where('vendor_id', $request->vendor_id);
				})->ignore($request->coupon_id),
			],
            'type'               => 'required|in:fixed,percentage',
            'value'              => 'required|numeric|min:0',
            'min_order_amount'   => 'nullable|numeric|min:0',
            'max_uses'           => 'nullable|integer|min:1',
            'max_uses_per_user'  => 'nullable|integer|min:1',
            'starts_at'          => 'nullable|date',
            'expires_at'         => 'nullable|date|after_or_equal:starts_at',
            'is_active'          => 'required|boolean',
        ]);
        
        $coupon = Coupon::where('vendor_id',auth()->id())->where('id',$request->coupon_id)->first();
        $coupon->vendor_id         = auth()->id();
        $coupon->code              = $data['code'];
        $coupon->type              = $data['type'];
        $coupon->value             = $data['value'];
        $coupon->min_order_amount  = $data['min_order_amount'] ?? null;
        $coupon->max_uses          = $data['max_uses'] ?? null;
        $coupon->max_uses_per_user = $data['max_uses_per_user'] ?? null;
        $coupon->starts_at         = $data['starts_at'] ?? null;
        $coupon->expires_at        = $data['expires_at'] ?? null;
        $coupon->is_active         = $data['is_active'];
        $coupon->save();
        
         return response()->json([
            'status'  => true,
            'message' => "You have updated coupon successfully.",
            'data'   => $coupon,
        ], 200);
		
    }
	
	public function getUserCoupons(Request $request){
	    
	    $perPage    = $request->input('limit', 10);
		$searchTerm = $request->input('search');
		
		$query      = Coupon::query();
		
		$query->where('is_active', 1)
          ->where(function ($q) {
              $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });
		
		if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('code', 'like', "%{$searchTerm}%")
                  ->orWhere('type', 'like', "%{$searchTerm}%")
                  ->orWhere('value', 'like', "%{$searchTerm}%");
            });
        }
        
	    $coupons = $query->latest()->paginate($perPage);
	
        return response()->json([
            'status'  => true,
            'message' => "Fetch vendor coupon successfully.",
            'data'   => $coupons,
        ], 200);
	}
	
	public function getVendorCoupons(Request $request)
    {
		
		$perPage    = $request->input('limit', 10);
		$searchTerm = $request->input('search');
		
		$query      = Coupon::query();
		
		if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('code', 'like', "%{$searchTerm}%")
                  ->orWhere('type', 'like', "%{$searchTerm}%")
                  ->orWhere('value', 'like', "%{$searchTerm}%");
            });
        }
        
	    $coupons = $query->where('vendor_id',auth()->id())->latest()->paginate($perPage);
	
        return response()->json([
            'status'  => true,
            'message' => "Fetch vendor coupon successfully.",
            'data'   => $coupons,
        ], 200);
    } 
	
	public function productImport(Request $request, Wholesale2BImportService $importService)
    { 
        $validator = Validator::make($request->all(), [
            'category_id'  => 'required|exists:categories,id',
            'store_id'     => 'required|exists:vendor_stores,id',
        ]);
     
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $count = $importService->import(
                (int) $request->category_id,
                $request->store_id
            );
			
            return response()->json([
                'status'  => true,
                'message' => "Imported $count products successfully.",
                'count'   => $count,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Import failed.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
	
	public function getVendorStores(Request $request)
    {
		try {
				
			$stores = VendorStore::where('user_id', auth()->id())->get();
					
            return response()->json([
                'status'  => true,
                'message' => "Fetch vendor store successfully.",
                'data'   => $stores,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Import failed.',
                'error'   => $e->getMessage(),
            ], 500);
        }
		
        return response()->json([
            'status' => true,
            'data'   => $categories
        ],200);
    } 
	
	public function getCategories(Request $request)
    {
		try {
				
			$categoriesData = Category::select('id', 'name', 'parent_id')->get();
			$categories       = $this->buildTree($categoriesData);
					
            return response()->json([
                'status'  => true,
                'message' => "Fetch categories successfully.",
                'data'   => $categories,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Import failed.',
                'error'   => $e->getMessage(),
            ], 500);
        }
		
        return response()->json([
            'status' => true,
            'data'   => $categories
        ],200);
    } 
	
	private function buildTree($categories, $parentId = null)
    {
        $branch = [];

        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {
                $children = $this->buildTree($categories, $category->id);

                $branch[] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'children' => $children
                ];
            }
        }

        return $branch;
    }
	
    public function vendor_store_status(Request $request) {
		
		$request->validate([
			'store_id' => 'required|integer|exists:vendor_stores,id',
			'status'   => 'required|integer|in:0,1',
		]);

		$storeInfo = VendorStore::where('user_id', auth()->id())->where('id', $request->store_id)->first();
		if (!$storeInfo) {
			return response()->json(['status' => false, 'message' => 'Store not found'], 404);
		}

		$storeInfo->status = $request->status;
		$storeInfo->save();

		return response()->json([
            'status'  => true,
			'message' => "Status updated successfully.",
            'data'    => $storeInfo
        ],200);
	}
	
	
	public function vendor_order_payment_status(Request $request) {
		
		$request->validate([
			'order_id'          => 'required|integer|exists:orders,id',
			'payment_status'    => 'required|string|in:pending,paid,failed,refunded',
		]);

		$orderInfo = Order::where('id', $request->order_id)->first();
		if (!$orderInfo) {
			return response()->json(['status' => false, 'message' => 'Order not found'], 404);
		}

		$orderInfo->payment_status = $request->payment_status;
		$orderInfo->save();

		return response()->json([
            'status'  => true,
			'message' => "Order payment status change successfully.",
            'data'    => $orderInfo
        ],200);
	}
	
	public function vendor_orders_details(Request $request,$id) {
	
		$orderInfo = Order::with('orderProduct.product.userInfo','orderTotal')->where('id',$id)->first();
		if (!$orderInfo) {
			return response()->json(['status' => false, 'message' => 'Order not found'], 404);
		}

		return response()->json([
            'status'  => true,
			'message' => "Order details fetch successfully.",
            'data'    => $orderInfo
        ],200);
	}
	
	public function vendor_order_status(Request $request) {
		
		$request->validate([
			'order_id'        => 'required|integer|exists:orders,id',
			'order_status'    => 'required|string|in:pending,confirmed,shipped,delivered,cancelled,returned',
		]);

		$orderInfo = Order::where('id', $request->order_id)->first();
		if (!$orderInfo) {
			return response()->json(['status' => false, 'message' => 'Order not found'], 404);
		}

		$orderInfo->order_status = $request->order_status;
		$orderInfo->save();
        
		return response()->json([
            'status'  => true,
			'message' => "Order status change successfully.",
            'data'    => $orderInfo
        ],200);
	}
	
	public function vendor_orders(Request $request) {
		
		$perPage    = $request->input('limit', 10);
		$searchTerm = $request->input('search');
		
		$query      = Order::query();
		
		if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('order_number', 'like', "%{$searchTerm}%")
                  ->orWhere('payment_status', 'like', "%{$searchTerm}%")
                  ->orWhere('order_status', 'like', "%{$searchTerm}%");
            });
        }
        
	    $orderList = $query->where('vendor_id',auth()->id())->latest()->paginate($perPage);

		return response()->json([
            'status'  => true,
			'message' =>  "Fetch vendor orders successfully.",
            'data'    => $orderList
        ],200);
	}
	
	public function updateFcmToken(Request $request) {
	
		$request->validate([
			'fcm_token' => 'required|string',
		]);

		$user = $request->user();
		if (!$user) {
			return response()->json(['status' => false, 'message' => 'User not found'], 404);
		}

	    $user->fcm_token = $request->fcm_token;
	    $user->device_token = $request->fcm_token;
        $user->save();
        
		return response()->json([
            'status'  => true,
			'message' => 'FCM token updated successfully.',
            'data'    => $user
        ],200);
	}
	
	public function getCurrency()
    {
        $currencyList = Currency::get();
        return response()->json([
              'status'  => true,
             'message'  => 'Currency fetched succesffully.',
             'data'     => $currencyList,
         ], 200);
    }
    
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password'    => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['status'   => false,'message' => 'Current password is incorrect.'], 403);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();
        
        #SEND NOTIFICATIONS
		sendFirebaseNotification($user->id,'Password changed','Password changed successfully.');
		
        return response()->json([
            'status'   => true,
            'message'  => 'Password changed successfully.',
         ], 200);
    }
    
    public function sendOtp(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email|exists:users,email',
            'phone' => 'nullable|integer|exists:users,phone',
        ], [
            'email.exists' => 'This email is not registered.',
            'phone.exists' => 'This phone number is not registered.',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        if ($request->filled('email')) {
            $type = 'email';
            $identifier = $request->email;
        } elseif ($request->filled('phone')) {
            $type = 'phone';
            $identifier = $request->phone;
        } else {
            return response()->json(['message' => 'Email or phone is required.'], 422);
        }
        
        $otp = rand(100000, 999999);
        $expiresAt = now()->addMinutes(5);
    
        DB::table('password_reset_otps')
            ->where('type', $type)
            ->where($type, $identifier)
            ->delete();
    
        DB::table('password_reset_otps')->insert([
            'email'      => $type === 'email' ? $identifier : null,
            'phone'      => $type === 'phone' ? $identifier : null,
            'type'       => $type,
            'otp'        => Hash::make($otp),
            'expires_at' => $expiresAt,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    

        if ($type === 'email') {
            Mail::raw("Your OTP to reset your password is: $otp", function ($message) use ($identifier) {
                $message->to([$identifier])
                        ->subject('Your Password Reset OTP');
            });
        } else {
            #PHONE SMS
            Mail::raw("Your OTP to reset your password is: $otp", function ($message) use ($identifier) {
                $message->to(get_admin_email()) 
                        ->subject("OTP for phone: $identifier");
            });
        }
    
        return response()->json([
            'status' => true,
            'message' => "OTP sent successfully to your $type.",
            'otp_expires_at' => $expiresAt->toDateTimeString(),
            'otp_field' => $type,
        ], 200);
    }

   /* public function resetPasswordWithOtp(Request $request)
    {
        $request->validate([
            'email'     => 'required|email|exists:users,email',
            'otp'       => 'required|string',
            'password'  => 'required|string|min:6|confirmed',
        ]);

        $record = DB::table('password_reset_otps')
                    ->where('email', $request->email)
                    ->latest()
                    ->first();

        if (!$record) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 400);
        }

        if (now()->greaterThan(Carbon::parse($record->expires_at))) {
            return response()->json(['message' => 'OTP has expired.'], 400);
        }

        if (!Hash::check($request->otp, $record->otp)) {
            return response()->json(['message' => 'Invalid OTP.'], 400);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_otps')->where('email', $request->email)->delete();

        return response()->json([
            'status'   => true,
            'message'  => 'Password has been reset successfully.',
         ], 200);
        
        
    }*/
    
    public function resetPasswordWithOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'nullable|email|exists:users,email',
            'phone'    => 'nullable|integer|exists:users,phone',
            'otp'      => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'email.exists' => 'This email is not registered.',
            'phone.exists' => 'This phone number is not registered.',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        if ($request->filled('email')) {
            $type = 'email';
            $identifier = $request->email;
        } elseif ($request->filled('phone')) {
            $type = 'phone';
            $identifier = $request->phone;
        } else {
            return response()->json(['message' => 'Email or phone is required.'], 422);
        }
    
        $record = DB::table('password_reset_otps')
                    ->where('type', $type)
                    ->where($type, $identifier)
                    ->latest()
                    ->first();
    
        if (!$record) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 400);
        }
    
        if (now()->greaterThan(Carbon::parse($record->expires_at))) {
            return response()->json(['message' => 'OTP has expired.'], 400);
        }
    
        if (!Hash::check($request->otp, $record->otp)) {
            return response()->json(['message' => 'Invalid OTP.'], 400);
        }
    
        $user = User::where($type, $identifier)->first();
        if (!$user) {
            return response()->json(['message' => ucfirst($type) . ' not found.'], 404);
        }
    
        $user->password = Hash::make($request->password);
        $user->save();
    
        DB::table('password_reset_otps')->where('type', $type)->where($type, $identifier)->delete();
    
        return response()->json([
            'status'  => true,
            'message' => 'Password has been reset successfully.',
        ], 200);
    }
    
	
	public function getConfigContent(Request $request)
    {
        $content = config('app_content');
		
		 return response()->json([
            'status'   => true,
            'message'  => 'Fetch config details succesffully.',
            'data'     => $content,
         ], 200);
    }
    
}