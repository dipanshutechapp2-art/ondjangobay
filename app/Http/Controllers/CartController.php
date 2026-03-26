<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use Auth;
use App\Services\ShippingService;
use App\Models\ShippingPrice;

class CartController extends Controller
{

    public function applyCoupon(Request $request)
	{  
		$code = $request->input('code');
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
			return redirect()->back()->with('error', 'Invalid or expired coupon.');
		}

		$cart = Auth::check()
			? CartItem::with('product')->where('user_id', Auth::id())->get()
			: collect(session()->get('cart', []));

		if ($cart->isEmpty()) {
			return redirect()->back()->with('error', 'Your cart is empty.');
		}

		$total    = 0;
		$discount = 0;
		
		#GLOBAL USAGE LIMIT (max_uses)
		if (!is_null($coupon->max_uses)) {
			$usedCount = Order::where('coupon_id', $coupon->id)->count();

			if ($usedCount >= $coupon->max_uses) {
				return redirect()->back()->with('error', 'This coupon has reached its maximum usage limit.');
			}
		}
		#PER USER LIMIT (max_uses_per_user)
		if (Auth::check() && !is_null($coupon->max_uses_per_user)) {
			$userUsedCount = Order::where('coupon_id', $coupon->id)
				->where('user_id', Auth::id())
				->count();

			if ($userUsedCount >= $coupon->max_uses_per_user) {
				return redirect()->back()->with('error', 'You have already used this coupon maximum allowed times.');
			}
		}
		
		// Vendor-specific or global coupon
		if ($coupon->vendor_id) {
			// Vendor-specific coupon
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
			
			// Global coupon
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
			return redirect()->back()->with('error', 'Minimum order amount for this coupon is ' . $coupon->min_order_amount);
		}
		
		// Save coupon details in session
		session()->put('coupon', [
			'id'        => $coupon->id,
			'code'      => $coupon->code,
			'discount'  => $discount,
			'vendor_id' => $coupon->vendor_id,
		]);

		return redirect()->back()->with('success', 'Coupon applied successfully.');
	}

	
	public function removeCoupon(Request $request)
    { 
        session()->forget('coupon');
        return redirect()->back()->with('success', 'Coupon removed.');
    }
	
	public function cart() {
		
		#SHIPPING OPTIONS
		$countryCode = 'IN';
		$shippingServicesInfo = new ShippingService();	
		$shippingOptions      = $shippingServicesInfo->getOptions($countryCode);
		
		if (!session()->has('shipping_price_id') && $shippingOptions->count()) {
			$defaultShipping = $shippingOptions
				->sortBy('price')
				->first();
			session([
				'shipping_price_id' => $defaultShipping->id,
				'shipping_price'    => $defaultShipping->price,
				'shipping_title'    => $defaultShipping->option->title,
				'shipping_carrier'  => $defaultShipping->option->default_carrier,
			]);
		}
		$shippingPrice = session('shipping_price', 0);
		
		$cartItems = [];

		if (Auth::check()) {

			$items = CartItem::with('product.userInfo')
				->where('user_id', Auth::id())
				->get();

			foreach ($items as $item) {
				$product = $item->product;

				if (!$product) continue;

				$cartItems[] = [
					'cart_key'     => $item->id,
					'product_id'   => $product->id,
					'slug'         => $product->slug,
					'name'         => $product->name,
					'price'        => $item->price,
					'quantity'     => $item->quantity,
					'image'        => $product->image ? asset('uploads/products/' . $product->image) : '',
					'vendor'       => $product->userInfo->name ?? 'N/A',
					'origin'       => $product->type ?? 'unknown',
					'subtotal'     => $item->price * $item->quantity,
					'variant_text' => $this->getVariantText($item->variants ?? []),
				];
			}
		} else {

			$cart = session()->get('cart', []);

			foreach ($cart as $key => $item) {
				$product = Product::with('userInfo')->find($item['product_id']);
				if (!$product) continue;

				$cartItems[] = [
					'cart_key'     => $key,
					'product_id'   => $item['product_id'],
					'slug'         => $product->slug,
					'name'         => $item['name'],
					'price'        => $item['price'],
					'quantity'     => $item['quantity'],
					'image'        => $item['image'],
					'vendor'       => $product->userInfo->name ?? 'N/A',
					'origin'       => $product->type ?? 'unknown',
					'subtotal'     => $item['price'] * $item['quantity'],
					'variant_text' => $this->getVariantText($item['variants'] ?? []),
				];
			}
		}
		
		#COUPONS
		$vendorCoupons = Coupon::with('vendor')
            ->where('is_active', 1)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>=', now());#COUPONS
            })
            ->where(function ($q) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', now());
            })
            ->orderBy('id', 'desc')
            ->get();

		return view('cart.index', compact('cartItems','vendorCoupons','shippingOptions','shippingPrice'));
	}

	public function addToCart(Request $request) {
		
		$product  = Product::findOrFail($request->product_id);
		$variants = $request->input('variants', []);
		$quantity = (int) $request->input('quantity', 1);
		$extraPrice = 0;
		$variantStock = null;

		if (!empty($variants)) {
			foreach ($variants as $type => $valueId) {
				$variant = ProductVariant::with('attributeValue')->where('product_id',$product->id)->where('value', $valueId)->first();
				if ($variant) {
					$variantPrice = $variant->price ?? ($variant->attributeValue->price ?? 0);
					$extraPrice += $variantPrice;

					if ($variant->stock !== null) {
						$variantStock = $variantStock === null ? $variant->stock : min($variantStock, $variant->stock);
					}
				}
			}
		}

		$cartKey = $this->generateCartKey($product->id, $variants);
		$finalPrice = $product->price + $extraPrice;

		if (Auth::check()) {
			$user = Auth::user();
			$cartItem = CartItem::where('user_id', $user->id)
				->where('product_id', $product->id)
				->where('variants', json_encode($variants))
				->first();

			$currentQty = $cartItem ? $cartItem->quantity : 0;
			$maxStock = $variantStock ?? $product->quantity;

			if (($currentQty + $quantity) > $maxStock) {
				return response()->json([
					'success' => false,
					'message' => 'Not enough stock for this product/variant.'
				]);
			}

			if ($cartItem) {
				$cartItem->increment('quantity', $quantity);
			} else {
				CartItem::create([
					'user_id'    => $user->id,
					'session_id' => null,
					'product_id' => $product->id,
					'variants'   => $variants,
					'price'      => $finalPrice,
					'quantity'   => $quantity,
				]);
			}

		} else {

			$cart = session()->get('cart', []);
			$currentQtyInCart = isset($cart[$cartKey]) ? $cart[$cartKey]['quantity'] : 0;

			$maxStock = $variantStock ?? $product->quantity;
			if (($currentQtyInCart + $quantity) > $maxStock) {
				return response()->json([
					'success' => false,
					'message' => 'Not enough stock for this product/variant.'
				]);
			}

			if (isset($cart[$cartKey])) {
				$cart[$cartKey]['quantity'] += $quantity;
			} else {
				$cart[$cartKey] = [
					'product_id' => $product->id,
					'name'       => $product->name,
					'price'      => $finalPrice,
					'quantity'   => $quantity,
					'image'      => $product->image ? asset('uploads/products/' . $product->image) : '',
					'variants'   => $variants
				];
			}

			session()->put('cart', $cart);
		}

		return $this->cartJsonResponse();
	}
	
	public function remove(Request $request) {
		
		$cartKey = $request->input('cart_key');

		if (Auth::check()) {
			CartItem::where('id', $cartKey)
				->where('user_id', Auth::id())
				->delete();
		} else {
			$cart = session()->get('cart', []);
			if (isset($cart[$cartKey])) {
				unset($cart[$cartKey]);
				session()->put('cart', $cart);
			}
		}
		return $this->cartJsonResponse();
	}
	
    public function updateQuantity(Request $request) {
		
		$cartKey     = $request->input('cart_key');
		$newQuantity = max((int) $request->input('quantity', 1), 1);

		if (Auth::check()) {

			$cartItem = CartItem::with('product')->where('id', $cartKey)->where('user_id', Auth::id())->first();

			if (!$cartItem || !$cartItem->product) {
				return response()->json(['success' => false, 'message' => 'Cart item or product not found.']);
			}

			$product = $cartItem->product;
			$variantStock = null;

			if (!empty($cartItem->variants)) {
				foreach ($cartItem->variants as $type => $valueId) {
					$variant = ProductVariant::where('value', $valueId)->first();
					if ($variant && $variant->stock !== null) {
						$variantStock = $variantStock === null ? $variant->stock : min($variantStock, $variant->stock);
					}
				}

				if ($variantStock === null || $newQuantity > $variantStock) {
					return response()->json(['success' => false, 'message' => 'Quantity exceeds variant stock.']);
				}
			} else {
				if ($newQuantity > $product->quantity) {
					return response()->json(['success' => false, 'message' => 'Quantity exceeds product stock.']);
				}
			}

			$cartItem->update(['quantity' => $newQuantity]);
		} else {
			$cart = session()->get('cart', []);

			if (!isset($cart[$cartKey])) {
				return response()->json(['success' => false, 'message' => 'Item not found in cart.']);
			}

			$item = $cart[$cartKey];
			$product = Product::find($item['product_id']);
			$variantStock = null;

			if (!$product) {
				return response()->json(['success' => false, 'message' => 'Product not found.']);
			}

			if (!empty($item['variants'])) {
				foreach ($item['variants'] as $type => $valueId) {
					$variant = ProductVariant::where('value', $valueId)->first();
					if ($variant && $variant->stock !== null) {
						$variantStock = $variantStock === null ? $variant->stock : min($variantStock, $variant->stock);
					}
				}

				if ($variantStock === null || $newQuantity > $variantStock) {
					return response()->json(['success' => false, 'message' => 'Quantity exceeds variant stock.']);
				}
			} else {
				if ($newQuantity > $product->quantity) {
					return response()->json(['success' => false, 'message' => 'Quantity exceeds product stock.']);
				}
			}

			$cart[$cartKey]['quantity'] = $newQuantity;
			session()->put('cart', $cart);
		}

		return $this->cartJsonResponse();
	}
	
	public function updateQuantityByCartPage(Request $request) {
		
		$cartKey     = $request->input('cart_key');
		$newQuantity = max((int) $request->input('quantity', 1), 1);

		if (Auth::check()) {

			$cartItem = CartItem::with('product')->where('id', $cartKey)->where('user_id', Auth::id())->first();

			if (!$cartItem || !$cartItem->product) {
				return redirect()->back()->with('error', 'Cart item or product not found.');
			}

			$product = $cartItem->product;
			$variantStock = null;

			if (!empty($cartItem->variants)) {
				foreach ($cartItem->variants as $type => $valueId) {
					$variant = \App\Models\ProductVariant::where('value', $valueId)->first();
					if ($variant && $variant->stock !== null) {
						$variantStock = $variantStock === null ? $variant->stock : min($variantStock, $variant->stock);
					}
				}

				if ($variantStock === null || $newQuantity > $variantStock) {
					return redirect()->back()->with('error', 'Quantity exceeds available variant stock.');
				}
			} else {
				if ($newQuantity > $product->quantity) {
					return redirect()->back()->with('error', 'Quantity exceeds available product stock.');
				}
			}

			$cartItem->update(['quantity' => $newQuantity]);
		} else {

			$cart = session()->get('cart', []);

			if (!isset($cart[$cartKey])) {
				return redirect()->back()->with('error', 'Product not found in cart.');
			}

			$item    = $cart[$cartKey];
			$product = Product::find($item['product_id']);

			if (!$product) {
				return redirect()->back()->with('error', 'Product not found.');
			}

			$variantStock = null;

			if (!empty($item['variants'])) {
				foreach ($item['variants'] as $type => $valueId) {
					$variant = \App\Models\ProductVariant::where('value', $valueId)->first();
					if ($variant && $variant->stock !== null) {
						$variantStock = $variantStock === null ? $variant->stock : min($variantStock, $variant->stock);
					}
				}

				if ($variantStock === null || $newQuantity > $variantStock) {
					return redirect()->back()->with('error', 'Quantity exceeds available variant stock.');
				}
			} else {
				if ($newQuantity > $product->quantity) {
					return redirect()->back()->with('error', 'Quantity exceeds available product stock.');
				}
			}

			$cart[$cartKey]['quantity'] = $newQuantity;
			session()->put('cart', $cart);
		}

		return redirect()->back()->with('success', 'Cart updated successfully.');
	}

	public function getCart() {
		
		if (Auth::check()) {
			$cartItems = CartItem::with(['product.userInfo'])
				->where('user_id', Auth::id())
				->get();
			return $this->cartJsonResponse($cartItems, true);
		} else {
			$cart = session()->get('cart', []);
			return $this->cartJsonResponseOther($cart, false);
		}
	}
	

    public function clear(){
		
		if (auth()->check()) {
			CartItem::where('user_id', auth()->id())->delete();
		} else {
			session()->forget('cart');
		}

		return redirect('/cart')->with('success', 'Cart cleared successfully.');
	}
	
	public function removeCartProduct(Request $request) {
		
		$cartKey = $request->input('cart_key');

		if (Auth::check()) {
			$cartItem = CartItem::where('user_id', Auth::id())->where('id', $cartKey)->first();

			if ($cartItem) {
				$cartItem->delete();
			}
			
		} else {
			
			$cart = session()->get('cart', []);
			
			if (isset($cart[$cartKey])) {
				unset($cart[$cartKey]);
				session()->put('cart', $cart);
			}
		}

		return redirect()->back()->with('success', 'Cart updated successfully.');
	}

    private function generateCartKey($productId, $variants = []) {
		
        if (empty($variants)) {
            return (string) $productId;
        }

        ksort($variants); 
        return $productId . '-' . collect($variants)->implode('-');
    }
	
	private function cartJsonResponseOther($cartData = [], $isFromDatabase = false) {
		
		$cartItems = [];
		$total = 0;

		if ($isFromDatabase) {

			foreach ($cartData as $item) {
				$product = $item->product;

				if (!$product) continue;

				$subtotal = $item->price * $item->quantity;
				$total += $subtotal;

				$variantText = $this->getVariantText($item->variants ?? []);

				$cartItems[] = [
					'cart_key'     => $item->id,
					'product_id'   => $product->id,
					'slug'         => $product->slug,
					'name'         => $product->name,
					'price'        => $item->price,
					'quantity'     => $item->quantity,
					'image'        => $product->image ? asset('uploads/products/' . $product->image) : '',
					'vendor'       => $product->userInfo->name ?? 'N/A',
					'origin'       => $product->type ?? 'unknown',
					'subtotal'     => $subtotal,
					'variant_text' => $variantText,
				];
				$totals = calculateCartTotals($cartData, true);
			}
		} else {

			foreach ($cartData as $key => $item) {
				$product = Product::with('userInfo')->find($item['product_id']);

				if (!$product) continue;

				$subtotal = $item['price'] * $item['quantity'];
				$total += $subtotal;

				$variantText = $this->getVariantText($item['variants'] ?? []);

				$cartItems[] = [
					'cart_key'     => $key,
					'product_id'   => $item['product_id'],
					'slug'         => $product->slug,
					'name'         => $item['name'],
					'price'        => $item['price'],
					'quantity'     => $item['quantity'],
					'image'        => $item['image'],
					'vendor'       => $product->userInfo->name ?? 'N/A',
					'origin'       => $product->type ?? 'unknown',
					'subtotal'     => $subtotal,
					'variant_text' => $variantText,
					
				];
			}
			$totals = calculateCartTotals($cartData, true);
		}

		session()->put('cart_total', $total);

		return response()->json([
			'success'    => true,
			'cart_items' => $cartItems,
			//'cart_total' => $total,
			'discount'   => $totals['discount'],
			'cart_total' => $totals['grand_total'],
		]);
	}

	
	private function cartJsonResponse()
	{
		$cartItems = [];
		$total = 0;

		if (Auth::check()) {
			$items = CartItem::with('product.userInfo')
				->where('user_id', Auth::id())
				->get();

			foreach ($items as $item) {
				$product = $item->product;
				$subtotal = $item->price * $item->quantity;
				$total += $subtotal;

				$cartItems[] = [
					'cart_key'     => $item->id,
					'product_id'   => $product->id,
					'slug'         => $product->slug,
					'name'         => $product->name,
					'price'        => $item->price,
					'quantity'     => $item->quantity,
					'image'        => asset('uploads/products/' . $product->image),
					'vendor'       => $product->userInfo->name ?? "N/A",
					'origin'       => $product->type ?? "unknown",
					'subtotal'     => $subtotal,
					'variant_text' => $this->getVariantText($item->variants),
				];
			}
			
			$totals = calculateCartTotals($items, true);
		

		} else {
			$cart = session()->get('cart', []);
			foreach ($cart as $key => $item) {
				$product = Product::with('userInfo')->find($item['product_id']);
				if (!$product) continue;

				$subtotal = $item['price'] * $item['quantity'];
				$total += $subtotal;

				$cartItems[] = [
					'cart_key'     => $key,
					'product_id'   => $item['product_id'],
					'slug'         => $product->slug,
					'name'         => $item['name'],
					'price'        => $item['price'],
					'quantity'     => $item['quantity'],
					'image'        => $item['image'],
					'vendor'       => $product->userInfo->name ?? "N/A",
					'origin'       => $product->type ?? "unknown",
					'subtotal'     => $subtotal,
					'variant_text' => $this->getVariantText($item['variants'] ?? []),
				];
			}
			$totals = calculateCartTotals($cart, true);
		}

		session()->put('cart_total', $total);

		return response()->json([
			'success'    => true,
			'cart_items' => $cartItems,
			//'cart_total' => $total,
			'discount'   => $totals['discount'],
			'cart_total' => $totals['grand_total'],
		]);
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
}
