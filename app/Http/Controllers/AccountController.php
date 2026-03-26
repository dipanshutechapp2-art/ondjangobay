<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Product;
use App\Models\Order;
use App\Models\Order_product;
use App\Models\Order_total;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\Address;
use App\Models\MagicLink;
use App\Models\LoginMethod;
use App\Models\UserLogin;
use App\Models\CartItem;
use App\Models\SearchHistory;
use App\Models\PartnerOrder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use DB;
use Str;

class AccountController extends Controller
{ 
   
    
	public function deactivateAccount(Request $request) {   
	
	  $user = User::where('id', auth()->id())->first();
	  $user->status = '0';
	  $user->save();
	  
	  $userId = auth()->id();
		if(!empty($userId)){
		   DB::table('sessions')->where('user_id', $userId)->delete();
		}
		
		if ($request->user()) {
            $request->user()->tokens()->delete();
        }
        
		Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

	  return redirect('/user/login')->with('success', 'Your account has been successfully deactivated.');
    }
	
	public function deleteAccountPermanently(Request $request){
		
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
		
		return redirect('/user/login')->with('success', 'Your account has been permanently deleted.');
	}
	
	
	public function my_account() {   
	
	   return view('account.my_account');
    }
	
	public function magicLink(Request $request) {   
	   
	   //$magicLinks = MagicLink::where('user_id',auth()->id())->get();

	   return view('account.magic_link');
    }
	
	public function partnerOrders(Request $request) { 
	
	    $orders = PartnerOrder::with('product')->where('user_id', auth()->id())->orderBy('id','DESC')->paginate(10);
	
		return view('account.partner_orders',compact('orders'));
    }
	
	public function orders(Request $request) { 
	
	    $orders = Order::with('orderProduct.product.userInfo','orderTotal')->where('user_id', auth()->id())->orderBy('id','DESC')->paginate(10);
		
		return view('account.orders',compact('orders'));
    }
	
	public function CancelOrders(Request $request,$order_number) { 
		
	    $orders = Order::where('order_number',$order_number)->where('user_id', auth()->id())->first();
		
		return view('account.order_cancel',compact('orders'));
    }
	
	public function order_action(Request $request) { 
		
	    $order = Order::where('order_number',$request->order_number)->where('user_id', auth()->id())->first();
		$order->order_status = 'cancelled';
		$order->reason       = $request->reason;
		$order->save();
		
		#SEND ORDER MAIL
    	$orderInfo = Order::with('orderProduct.product','orderTotal')->where('id',$order->id)->first();
		$res = Mail::send('emails.order-cancel', ['order' => $orderInfo], function ($message) use ($orderInfo) {
			$message->to([$orderInfo->email,get_admin_email()])
					->subject('Your Order Status - #' . $orderInfo->order_number);
		});
		
		return redirect('/account/orders')->with('success', 'Your order has been canceled.');
    }
	
	public function CancelPartnerOrders(Request $request,$order_number) { 
		
	    $orders = PartnerOrder::where('order_number',$order_number)->where('user_id', auth()->id())->first();
		
		return view('account.partner_order_cancel',compact('orders'));
    }
	
	public function partner_order_action(Request $request) { 
		
	    $order = PartnerOrder::where('order_number',$request->order_number)->where('user_id', auth()->id())->first();
		$order->status 		 = 'cancelled';
		$order->reason       = $request->reason;
		$order->save();
		
		#SEND ORDER MAIL
    	$orderInfo = PartnerOrder::with('product')->where('id',$order->id)->first();
		$res = Mail::send('emails.partner-order-cancel', ['order' => $orderInfo], function ($message) use ($orderInfo) {
			$message->to([$orderInfo->billing_email,get_admin_email()])
					->subject('Your Partner Order Status - #' . $orderInfo->order_number);
		});
		
		return redirect('/account/partner-orders')->with('success', 'Your order has been canceled.');
    }
	
	public function orderDetails(Request $request,$id) { 
	
	    $orderInfo = Order::with('orderProduct.product.userInfo','orderTotal')->where('user_id', auth()->id())->where('order_number',$id)->first();
	
		return view('account.order_details',compact('orderInfo'));

    }
	
	public function partnerOrderDetails(Request $request,$id) { 
	
	    $orderInfo = PartnerOrder::with('product')->where('user_id', auth()->id())->where('order_number',$id)->first();
	
		return view('account.partner_order_details',compact('orderInfo'));

    }
	
	public function searchHistory(Request $request){
		
		$recentSearches = SearchHistory::where('user_id', Auth::id())->orderBy('updated_at', 'desc')->paginate(10);
		
		return view('account.search_history',compact('recentSearches'));
	}
			
	public function reOrders(Request $request, $orderNumber)
	{
			$originalOrder = \App\Models\Order::with('orderProduct')->where('order_number', $orderNumber)->first();

			if (!$originalOrder) {
				return redirect('/account/orders')->with('error', 'Invalid order number.');
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

			return redirect('/cart')->with('success', 'Items added to cart from previous order.');
	}
	
	public function downloads() {   
	
	   return view('account.downloads');
    }
	
	public function accountDetails(Request $request){
		
		$userinfo = User::where('id',auth()->id())->first();

		return view('account.account_details',compact('userinfo'));
	}  
	
	public function update_account_action(Request $request){
		
		$request->validate([
            'firstname'          => 'required|string|max:255',
            'lastname'           => 'required|string|max:255',
            'display_name'       => 'required|string|max:255',
            'email'              => 'required|email|unique:users,email,'.auth()->id(),
			'current_password'   => 'nullable',
            'password'           => 'nullable|confirmed',
            'dob'                => 'nullable|date',
            'gender'             => 'nullable|string',
            'phone'              => 'nullable|numeric|unique:users,phone,'.auth()->id(),
			'profile_pic' 		 => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
        ]);
		
		$userInfo = User::where('id',auth()->id())->first();
		$userInfo->name           = $request->firstname;
		$userInfo->last_name      = $request->lastname;
		$userInfo->display_name   = $request->display_name;
		$userInfo->email          = $request->email;
		$userInfo->dob            = $request->dob;
		$userInfo->gender         = $request->gender;
		$userInfo->phone          = $request->phone;
		
		if (isset($request->profile_pic)) {
			
			#IMG DELETE BEFORE UPDATE RECORD
			if (auth()->user()->image && File::exists(public_path('/uploads/users/'.auth()->user()->image))) {
				File::delete(public_path('/uploads/users/'.auth()->user()->image));
			}
			
            $imageName = time() . '.' . $request->profile_pic->extension();
            $request->profile_pic->move(public_path('uploads/users/'), $imageName);
            $userInfo->image = $imageName;
        }
		
		$userInfo->save();
		
		return redirect('/account/details')->with('success', 'You have updated successfully.');
	}
	
	public function changePassword(Request $request){
		
		return view('account.change_password');
	}
	
	public function changePasswordAction(Request $request){
		
		$request->validate([
			'current_password'   => 'nullable',
            'password'           => 'nullable|confirmed',
        ]);
		
		$user = auth()->user();
	   
		if (!Hash::check($request->current_password, $user->password)) {
			return redirect('/account/change-password')
				->withErrors(['current_password' => 'The provided password does not match your current password.'])
				->withInput()
				->with('status', 'Failed');
		}
		$user->update([
			'password' => Hash::make($request->password),
		]);
		
		return redirect('/account/change-password')->with('success', 'You have updated successfully.');
		
	}
	
	public function userAddress(Request $request){
		
		$address = Address::where('user_id',auth()->id())->get();

		return view('account.address',compact('address'));
		
	}
	
	public function link_accounts(Request $request){
		
		$user = Auth::user();
		$user->load('logins.method');
		
		$availableMethods = LoginMethod::where('is_active', 1)->get();

		return view('account.link_account',compact('user','availableMethods'));
		
	}
	
    public function showAddLoginForm($method)
    {
        $user = Auth::user();
        $loginMethod = LoginMethod::where('code', $method)->where('is_active', 1)->firstOrFail();

        return view('account.add_login', compact('user', 'loginMethod'));
    }
	
	public function sendOtp(Request $request)
	{
		$request->validate([
			'identifier'   => 'required|string',
			'password'     => 'required|string|min:6',
			'login_method' => 'required|in:email,phone',
		]);

		$user = Auth::user();
		$identifier = $request->identifier;
		$password = $request->password;
		$loginMethod = $request->login_method;
		
		if ($loginMethod === 'email') {
			
			$existingUser = User::where('id',$user->id)->where('email', $identifier)->where('role','user')->first();
			
			if (!$existingUser) {
				return response()->json([
					'success' => false,
					'message' => 'Invalid user details.'
				], 422);
			}
			
			if ($existingUser) {

				if (!Hash::check($password, $existingUser->password)) {
					return response()->json([
						'success' => false,
						'message' => 'Email and password combination is incorrect.'
					], 422);
				}
				
				if ($existingUser->id !== $user->id) {
					return response()->json([
						'success' => false,
						'message' => 'This email is already registered with another account.'
					], 422);
				}
			}
			
		} else {
			
			$existingUser = User::where('id',$user->id)->where('phone', $identifier)->where('role','user')->first();
			
			if (!$existingUser) {
				return response()->json([
					'success' => false,
					'message' => 'Invalid user details.'
				], 422);
			}
			
			if ($existingUser) {

				if (!Hash::check($password, $existingUser->password)) {
					return response()->json([
						'success' => false,
						'message' => 'Phone number and password combination is incorrect.'
					], 422);
				}
				
				if ($existingUser->id !== $user->id) {
					return response()->json([
						'success' => false,
						'message' => 'This phone number is already registered with another account.'
					], 422);
				}
			}
		}

		$method = LoginMethod::where('code', $loginMethod)->where('is_active', 1)->first();
		if (!$method) {
			return response()->json([
				'success' => false,
				'message' => 'Invalid login method.'
			], 422);
		}

		$existingLogin = UserLogin::where('user_id', $user->id)
			->where('login_method_id', $method->id)
			->first();

		if ($existingLogin) {
			return response()->json([
				'success' => false,
				'message' => 'This login method is already linked to your account.'
			], 422);
		}

		$otp = rand(100000, 999999);

		Session::put('otp_' . $identifier, $otp);
		Session::put('login_method_' . $identifier, $loginMethod);
		Session::put('password_' . $identifier, $password);

		if ($loginMethod === 'email') {

			Mail::send('emails.user-otp', ['user' => $user, 'otp' => $otp], function ($message) use ($identifier) {
				$message->to($identifier)->subject('OTP Verification');
			});
		} else {
			// Send OTP via SMS (placeholder - implement your SMS service)
			// SmsService::send($identifier, "Your OTP is: $otp");
			
			// For now, send email as fallback
			Mail::send('emails.user-otp', ['user' => $user, 'otp' => $otp], function ($message) use ($user) {
				$message->to($user->email)->subject('OTP Verification');
			});
		}

		return response()->json([
			'success' => true,
			'message' => 'OTP sent successfully!'
		]);
	}

	public function storeLogin(Request $request)
	{
		$request->validate([
			'identifier'   => 'required|string',
			'login_method' => 'required|in:email,phone',
			'password'     => 'required|string',
			'otp'          => 'required|digits:6',
		]);

		$user       = Auth::user();
		$identifier = $request->identifier;
		$password   = $request->password;
		$otp = $request->otp;


		$storedOtp      = Session::get('otp_' . $identifier);
		$storedPassword = Session::get('password_' . $identifier);
		$storedMethod   = Session::get('login_method_' . $identifier);

		if ($storedOtp != $otp) {
			return redirect()->back()->with('error', 'Invalid OTP. Please try again.');
		}

		if ($storedPassword !== $password) {
			return redirect()->back()->with('error', 'Password verification failed.');
		}

		if ($storedMethod !== $request->login_method) {
			return redirect()->back()->with('error', 'Login method mismatch.');
		}

		if ($request->login_method === 'email') {
			$existingUser = User::where('email', $identifier)->first();
			if ($existingUser) {
				if (!Hash::check($password, $existingUser->password)) {
					return redirect()->back()->with('error', 'Email and password combination is incorrect.');
				}
				if ($existingUser->id !== $user->id) {
					return redirect()->back()->with('error', 'This email belongs to another account.');
				}
			}
		} else {
			$existingUser = User::where('phone', $identifier)->first();
			if ($existingUser) {
				if (!Hash::check($password, $existingUser->password)) {
					return redirect()->back()->with('error', 'Phone number and password combination is incorrect.');
				}
				if ($existingUser->id !== $user->id) {
					return redirect()->back()->with('error', 'This phone number belongs to another account.');
				}
			}
		}

		$method = LoginMethod::where('code', $request->login_method)->where('is_active', 1)->firstOrFail();

		if (UserLogin::where('user_id', $user->id)
			->where('login_method_id', $method->id)
			->exists()) {
			return back()->with('error', 'This login method is already linked.');
		}

		UserLogin::create([
			'user_id'          => $user->id,
			'login_method_id'  => $method->id,
			'identifier'       => $identifier,
			'secret'           => bcrypt($password),
			'is_primary'       => false,
		]);

		if ($request->login_method === 'email' && $user->email !== $identifier) {
			$user->email = $identifier;
			$user->save();
		} elseif ($request->login_method === 'phone' && $user->phone !== $identifier) {
			$user->phone = $identifier;
			$user->is_phone_verified = '1';
			$user->save();
		}

		Session::forget('otp_' . $identifier);
		Session::forget('login_method_' . $identifier);
		Session::forget('password_' . $identifier);

		return redirect()->route('account.link_accounts')->with('success', 'Login method linked successfully.');
	}
	
	public function switchPrimary($id)
    {
        $user   = Auth::user();
        $login = UserLogin::where('user_id', $user->id)->findOrFail($id);

        $user->logins()->update(['is_primary' => false]);

        $login->is_primary = true;
        $login->save();

        return back()->with('success', 'Primary login updated successfully.');
    }

    public function unlinkLogin($id)
    {
        $user = Auth::user();
        $login = UserLogin::where('user_id', $user->id)->findOrFail($id);

        if ($login->is_primary) {
            return back()->with('error', 'Cannot unlink primary login.');
        }

        $login->delete();

        return back()->with('success', 'Login method unlinked successfully.');
    }
	
	public function addAddress(Request $request) {
		
		$country  = Country::get();
		$state    = State::where('country_id','101')->get();
		
		return view('account.add_address',compact('country','state'));
	}
	
	public function add_address_action(Request $request) {
		
		$request->validate([
            'firstname'          => 'required|string',
            'lastname'           => 'required|string',
            'company'            => 'required|string',
            'country'            => 'required|string',
            'state'              => 'required|string',
            'address_1'          => 'required|string',
            'address_2'          => 'nullable|string',
            'city'               => 'required|string',
            'zipcode'            => 'required|string',
            'phone'              => 'required|string',
        ]);
		
		$dataArray = array("is_default" => '0',"is_billing" => '0',"is_shipping" => '0');
		
		$defaultUpdate = Address::where('user_id',auth()->id())->update($dataArray);
		
		
		$addressInfo = new Address;
		
		$addressInfo->user_id         = auth()->id();
		$addressInfo->first_name      = $request->firstname;
		$addressInfo->last_name       = $request->lastname;
		$addressInfo->company         = $request->company;
		$addressInfo->country         = $request->country;
		$addressInfo->address_1       = $request->address_1;
		$addressInfo->address_2       = $request->address_2;
		$addressInfo->city            = $request->city;
		$addressInfo->state           = $request->state;
		$addressInfo->zipcode         = $request->zipcode;
		$addressInfo->phone           = $request->phone;
		$addressInfo->is_default      = $request->is_default ?? '1';
		$addressInfo->is_billing      = $request->is_billing ?? '1';
		$addressInfo->is_shipping     = $request->is_shipping ?? '1';
		$addressInfo->save();
		
		return redirect('/account/address')->with('success', 'You have added successfully.');
	}
	
	public function deleteAddress(Request $request,$id){
		
		#UPDATE
		$dataArray     = array("is_default" => '0',"is_billing" => '0',"is_shipping" => '0');
		$defaultUpdate = Address::where('user_id',auth()->id())->update($dataArray);
		
		
		$addressInfo = Address::where('user_id',auth()->id())->where('id',$id)->delete();
		
		#SET IS DEFAULT
		$addressDataInfo   = Address::where('user_id', auth()->id())->latest()->first();
		
		if(!empty($addressDataInfo)) {
			$addressDataInfo->is_default      = '1';
			$addressDataInfo->is_billing      = '1';
			$addressDataInfo->is_shipping     = '1';
			$addressDataInfo->save();
		}
		
		return redirect('/account/address')->with('success', 'You have deleted successfully.');
	}
	
	public function editAddress(Request $request,$id) {
		
		$country  = Country::get();
		$state    = State::where('country_id','101')->get();
		
		$addressInfo = Address::where('user_id',auth()->id())->where('id',$id)->first();
		
		return view('account.edit_address',compact('addressInfo','country','state'));
	}
	
	public function update_address_action(Request $request) {
		
		$request->validate([
		    'address_id'         => 'required|integer|exists:address,id',
            'firstname'          => 'required|string',
            'lastname'           => 'required|string',
            'company'            => 'required|string',
            'country'            => 'required|string',
            'state'              => 'required|string',
            'address_1'          => 'required|string',
            'address_2'          => 'nullable|string',
            'city'               => 'required|string',
            'zipcode'            => 'required|string',
            'phone'              => 'required|string',
        ]);
		
		$defaultUpdate = Address::where('user_id',auth()->id())->where('id',$request->address_id)->first();
		$defaultUpdate->is_default      = $request->is_default ?? '0';
		$defaultUpdate->is_billing      = $request->is_billing ?? '0';
		$defaultUpdate->is_shipping     = $request->is_shipping ?? '0';
		$defaultUpdate->save();
		
		$addressInfo = Address::where('user_id',auth()->id())->where('id',$request->address_id)->first();
		
		$addressInfo->first_name      = $request->firstname;
		$addressInfo->last_name       = $request->lastname;
		$addressInfo->company         = $request->company;
		$addressInfo->country         = $request->country;
		$addressInfo->address_1       = $request->address_1;
		$addressInfo->address_2       = $request->address_2;
		$addressInfo->city            = $request->city;
		$addressInfo->state           = $request->state;
		$addressInfo->zipcode         = $request->zipcode;
		$addressInfo->phone           = $request->phone;
		$addressInfo->is_default      = $request->is_default ?? '1';
		$addressInfo->is_billing      = $request->is_billing ?? '1';
		$addressInfo->is_shipping     = $request->is_shipping ?? '1';
		$addressInfo->save();
		
		return redirect('/account/address')->with('success', 'You have updated successfully.');
	}
	
	public function update_action(Request $request) {
		
		#UPDATE
		$dataArray     = array("is_default" => '0',"is_billing" => '0',"is_shipping" => '0');
		$defaultUpdate = Address::where('user_id',auth()->id())->update($dataArray);
		
		$defaultUpdate = Address::where('user_id',auth()->id())->where('id',$request->address_id)->first();
		$defaultUpdate->is_default      = $request->is_default ?? '0';
		$defaultUpdate->is_billing      = $request->is_billing ?? '1';
		$defaultUpdate->is_shipping     = $request->is_shipping ?? '1';
		$defaultUpdate->save();

		return redirect('/account/address')->with('success', 'You have updated successfully.');
	}
	
	public function wishlist(Request $request){
		
		$wishlist = Wishlist::with('product.categories','user')->where('user_id',auth()->id())->paginate(10);

		return view('account.wishlist',compact('wishlist'));
	}
	
	public function addWishlist(Request $request){
		
		$productId    = $request->input('product_id');
		
		$wishlistInfo = Wishlist::where('product_id',$productId)->where('user_id',auth()->id())->first();
		
		if(empty($wishlistInfo)) {
			
			Wishlist::firstOrCreate([
				'user_id'    => auth()->id(),
				'product_id' => $productId
			]);

			return response()->json(['message' => 'Added to wishlist']);
			
		}else{
			return response()->json(['message' => 'You have all ready added in wishlist']);
		}
	}
	
	public function removeWishlist($id) {
		
		Wishlist::where('user_id', auth()->id())->where('product_id', $id)->delete();

		return response()->json(['message' => 'Removed from wishlist']);
	}
	
}
