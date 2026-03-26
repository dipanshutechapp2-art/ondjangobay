<?php
use App\Models\Currency;
use App\Models\User;
use App\Models\Order;
use App\Models\ActivityLog;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

if (!function_exists('get_vendor_email')) {
	function get_vendor_email($vendor_id)
	{
		$vendorEmail = User::where('id',$vendor_id)->where('role','vendor')->first()->email;
		return  $vendorEmail;
	}
}

if (!function_exists('sendFirebaseNotification')) {
	function logActivity(string $action, string $details = null): void
	{
		/* ActivityLog::create([
			'user_id'    => auth()->id() ?? NULL,
			'action'     => $action,
			'details'    => $details,
			'ip_address' => request()->ip(),
			'user_agent' => request()->userAgent(),
			'logged_at'  => now(),
		]); */
		
		ActivityLog::updateOrCreate(
			[
				'user_id' => auth()->id() ?? NULL,
				'action'  => $action ?? NULL,
				'details' => $details ?? NULL,
			],
			[
				'ip_address' => request()->ip()  ?? NULL,
				'user_agent' => request()->userAgent()  ?? NULL,
				'logged_at'  => now(),
			]
		); 

	}
}

if (!function_exists('sendFirebaseNotification')) {
    function sendFirebaseNotification($user, string $title, string $body, array $data = [])
    {
        if (is_numeric($user)) {
            $user = \App\Models\User::find($user);
        }

        if (!$user || !$user->fcm_token) {
            return [
                'success' => false,
                'message' => 'User not found or has no device token'
            ];
        }

        $notification = \App\Models\Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'body' => $body,
        ]);

        try {
            $messaging = (new \Kreait\Firebase\Factory)
                ->withServiceAccount(config('firebase.credentials'))
                ->createMessaging();

            $message = [
                'token' => $user->fcm_token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => array_merge($data, [
                    'notification_id' => (string) $notification->id,
                ])
            ];

            $messaging->send($message);

        } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {

            return [
                'success' => false,
                'message' => 'FCM token not found or invalid',
                'notification' => $notification
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Notification failed',
                'error' => $e->getMessage(),
                'notification' => $notification
            ];
        }

        return [
            'success' => true,
            'message' => 'Notification sent',
            'notification' => $notification
        ];
    }
}


if (!function_exists('renderCategoryOptions')) {
   /*  function renderCategoryOptions($categories, $parentId = null, $level = 0, $selected = []) {
        $children = $categories->where('parent_id', $parentId);

        foreach ($children as $category) {
            $hasChildren = $categories->where('parent_id', $category->id)->count() > 0;

            if ($hasChildren) {
                echo '<optgroup label="' . str_repeat('--', $level) . ' ' . e($category->name) . '">';
                renderCategoryOptions($categories, $category->id, $level + 1, $selected);
                echo '</optgroup>';
            } else {
                $isSelected = in_array($category->id, $selected) ? 'selected' : '';
                echo '<option value="' . $category->id . '" ' . $isSelected . '>';
                echo str_repeat('--', $level) . ' ' . e($category->name);
                echo '</option>';
            }
        }
    } */
	
	function renderCategoryOptions($categories, $parentId = null, $level = 0, $selected = []) {
		$children = $categories->where('parent_id', $parentId);

		foreach ($children as $category) {
			$isSelected = in_array($category->id, $selected) ? 'selected' : '';
			$hasChildren = $categories->where('parent_id', $category->id)->isNotEmpty();

			$style = $hasChildren ? 'style="font-weight:bold;"' : '';

			echo '<option value="' . $category->id . '" ' . $isSelected . ' ' . $style . '>';
			echo str_repeat('--', $level) . ' ' . e($category->name);
			echo '</option>';

			renderCategoryOptions($categories, $category->id, $level + 1, $selected);
		}
	}


	
	/* function renderCategoryOptions($categories, $parentId = null, $level = 0, $selected = []) {
		$children = $categories->where('parent_id', $parentId);

		foreach ($children as $category) {
			$isSelected = in_array($category->id, $selected) ? 'selected' : '';
			echo '<option value="' . $category->id . '" ' . $isSelected . '>';
			echo str_repeat('--', $level) . ' ' . e($category->name);
			echo '</option>';

			renderCategoryOptions($categories, $category->id, $level + 1, $selected);
		}
	} */

}

function calculateCartTotals($cartItems)
{
    $total = 0;

    foreach ($cartItems as $item) {
        if (is_array($item)) {
            $subtotal = $item['price'] * $item['quantity'];
        } else {
            $subtotal = $item->price * $item->quantity;
        }

        $total += $subtotal;
    }

    $discount = session('coupon.discount', 0);

    $grandTotal = max($total - $discount, 0);

    return [
        'total'       => $total,
        'discount'    => $discount,
        'grand_total' => $grandTotal,
    ];
}

/* if (!function_exists('renderCategorySingleOptions')) {
    function renderCategorySingleOptions($categories, $parentId = null, $level = 0, $selected = null) {
        $children = $categories->where('parent_id', $parentId);

        foreach ($children as $category) {
            $hasChildren = $categories->where('parent_id', $category->id)->isNotEmpty();
			
            $isSelected = (!$hasChildren && (string) $selected === (string) $category->id) ? 'selected' : '';

            $disabled = $hasChildren ? 'disabled' : '';
            $indent = str_repeat('--', $level);

            echo '<option value="' . $category->id . '" ' . $isSelected . ' ' . $disabled . '>';
            echo $indent . ' ' . e($category->name);
            echo '</option>';

            if ($hasChildren) {
                renderCategorySingleOptions($categories, $category->id, $level + 1, $selected);
            }
        }
    }
} */

if (!function_exists('renderCategorySingleOptions')) {
    function renderCategorySingleOptions($categories, $parentId = null, $level = 0, $selected = null) {
        $children = $categories->where('parent_id', $parentId);

        foreach ($children as $category) {
            $hasChildren = $categories->where('parent_id', $category->id)->isNotEmpty();

            $isSelected = ((string) $selected === (string) $category->id) ? 'selected' : '';
            $indent     = str_repeat('--', $level);

            $style = $hasChildren ? 'style="font-weight:bold;"' : '';

            echo '<option value="' . $category->id . '" ' . $isSelected . ' ' . $style . '>';
            echo $indent . ' ' . e($category->name);
            echo '</option>';

            renderCategorySingleOptions($categories, $category->id, $level + 1, $selected);
        }
    }
}

if (!function_exists('renderParentCategories')) {
    function renderParentCategories($categories, $selected = null) {
        // Sirf parent categories filter karo (jinka parent_id NULL ya 0 ho)
        $parents = $categories->where('parent_id', null)
                              ->merge($categories->where('parent_id', 0));

        foreach ($parents as $category) {
            $isSelected = ((string)$selected === (string)$category->id) ? 'selected' : '';

            echo '<option value="' . $category->id . '" ' . $isSelected . '>';
            echo e($category->name);
            echo '</option>';
        }
    }
}




if (!function_exists('formatCurrency')) {
    function formatCurrency($amount)
    {
        if(!empty(Auth::user()->currency_code)){
			
			$currency  = Currency::where('code',Auth::user()->currency_code)->first();
			//return $currency->symbol . number_format($amount, 2);
			$converted = $amount * $currency->rate;
			return $currency->symbol . number_format($converted, 2);
			
		}else{
			
			$currencyCode = Session::get('currency_code');
			
			if(!empty($currencyCode)) {
				
				$currency  = Currency::where('code',$currencyCode)->first();
				//return $currency->symbol . number_format($amount, 2);
				$converted = $amount * $currency->rate;
				return $currency->symbol . number_format($converted, 2);

			}else{
				
				$currency  = Currency::getSelectedCurrency();
				//return $currency->symbol . number_format($amount, 2);
				$converted = $amount * $currency->rate;
				return $currency->symbol . number_format($converted, 2);
			}
		}
    }
}

if (!function_exists('formatCurrencyPriceCalculate')) {
    function formatCurrencyPriceCalculate($amount)
    {
        if(!empty(Auth::user()->currency_code)){
			
			$currency  = Currency::where('code',Auth::user()->currency_code)->first();
			$converted = $amount * $currency->rate;
			return  number_format($converted, 2);
			
		}else{
			
			$currencyCode = Session::get('currency_code');
			
			if(!empty($currencyCode)) {
				
				$currency  = Currency::where('code',$currencyCode)->first();
				$converted = $amount * $currency->rate;
				return number_format($converted, 2);

			}else{
				
				$currency  = Currency::getSelectedCurrency();
				$converted = $amount * $currency->rate;
				return number_format($converted, 2);
			}
		}
    }
}

if (!function_exists('priceCalculatedOnlyAccordingToCurrency')) {
    function priceCalculatedOnlyAccordingToCurrency($amount)
    {
        if(!empty(Auth::user()->currency_code)){
			
			$currency  = Currency::where('code',Auth::user()->currency_code)->first();
			$converted = $amount * $currency->rate;
			return  $converted;
			
		}else{
			
			$currencyCode = Session::get('currency_code');
			
			if(!empty($currencyCode)) {
				
				$currency  = Currency::where('code',$currencyCode)->first();
				$converted = $amount * $currency->rate;
				return  $converted;

			}else{
				
				$currency  = Currency::getSelectedCurrency();
				$converted = $amount * $currency->rate;
				return  $converted;
			}
		}
    }
}

if (!function_exists('formatCurrencyPriceCalculateViaJs')) {
    function formatCurrencyPriceCalculateViaJs()
    {
        if(!empty(Auth::user()->currency_code)){
			
			$currencyInfo  = Currency::where('code',Auth::user()->currency_code)->first();
			return  $currencyInfo;
			
		}else{
			
			$currencyCode = Session::get('currency_code');
			
			if(!empty($currencyCode)) {
				
				$currencyInfo  = Currency::where('code',$currencyCode)->first();
				return  $currencyInfo;

			}else{
				
				$currencyInfo  = Currency::getSelectedCurrency();
				return  $currencyInfo;
			}
		}
    }
}

if (!function_exists('getDefaultSelectedCurrency')) {
    function getDefaultSelectedCurrency()
    {	
		if(!empty(Auth::user()->currency_code)){
		   
		   $currency  = Currency::where('code',Auth::user()->currency_code)->first();
           return $currency->symbol;
		   
		}else{
			
			$currencyCode = Session::get('currency_code');
			
			if(!empty($currencyCode)) {
				
				$currency  = Currency::where('code',$currencyCode)->first();
				return $currency->symbol;
				
			}else{
				
				$currency  = Currency::getSelectedCurrency();
				return $currency->symbol;
			}
		}
    }
}

if (!function_exists('getDefaultSelectedCurrencyCode')) {
    function getDefaultSelectedCurrencyCode() {	

		if(!empty(Auth::user()->currency_code)){
			
			$currencyInfo  = Currency::where('code',Auth::user()->currency_code)->first();
			return $currencyInfo->code;
			
		}else{
			
			$currencyCode = Session::get('currency_code');
			if(!empty($currencyCode)) {
				
				$currencyInfo  = Currency::where('code',$currencyCode)->first();
				return $currencyInfo->code;
				
			}else{
				
				$currencyInfo  = Currency::where('is_default','1')->first();
				return $currencyInfo->code;
			}
		}
    }
}

if (!function_exists('is_track_order')) {
    function is_track_order() {	
		
		$isOrder = 0;
		
		if(!empty(Auth::user()->id)){
			
			$isOrder = Order::where('user_id', Auth::user()->id)->where('order_status', 'confirmed')->count();
		}
		return $isOrder;
    }
}

if (!function_exists('getUserAuthOrDefaultSelectedCurrency')) {
    function getUserAuthOrDefaultSelectedCurrency() {	
		
		$currency =   Currency::where('is_default','1')->first()->code;

		if(!empty(Auth::user()->currency_code)){
			$currency = Auth::user()->currency_code;
		}
		
		return $currency;
    }
}

if (!function_exists('convertINRtoUSD')) {
	function convertINRtoUSD($inrAmount, $conversionRate = 0.012) {
		// Example: 1 INR = 0.012 USD (rate can be updated)
		$usdAmount = $inrAmount * $conversionRate;
		return round($usdAmount, 2);
	}
}

if (!function_exists('get_admin_email')) {
	function get_admin_email() {
		if(!empty(getenv('ADMIN_EMAIL'))) {
		   $adminEmail = getenv('ADMIN_EMAIL');
		}else{
			$adminEmail = 'sandeepsvi1990@gmail.com';
		}
		return $adminEmail;
	}
}

if (!function_exists('update_cart_item_after_login')) {
	function update_cart_item_after_login() {
		# UPDATE CART
		if (session()->has('cart')) {
			$sessionCart = session('cart');
			
			foreach ($sessionCart as $item) {
				
				$existing = CartItem::where('user_id', Auth::id())
					->where('product_id', $item['product_id'])
					->where('variants', json_encode($item['variants']))
					->first();

				if ($existing) {
					$existing->update([
						'quantity' => $existing->quantity + $item['quantity'],
					]);
				  
				} else {

					CartItem::updateOrCreate([
						'session_id'    => session()->getId() ?? null,
						'user_id'       => Auth::id(),
						'product_id'    => $item['product_id'],
						'variants'      => $item['variants'],
					], [
						'price'    => $item['price'],
						'quantity' => \DB::raw("quantity + {$item['quantity']}"),
					]);
					
				}
			}

			session()->forget('cart');
		}
		return true;
	}
}

