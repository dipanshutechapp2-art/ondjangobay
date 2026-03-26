<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Category;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\VendorStore;
use App\Models\ProductAttribute;
use App\Models\ProductVariant;
use App\Models\Sliders;
use App\Models\SidePost;
use App\Models\Review;
use App\Models\Currency;
use App\Models\PartnerCampaign;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Services\AutoDSService;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
   
    public function index(Request $request)
    {   
	    
		/* if ($request->has('code')) {
			try {
				if (!auth()->check()) {
					return redirect('/login')
						->with('error', 'Please login before connecting AutoDS');
				}

				app(AutoDSService::class)->exchangeCodeForToken(
					$request->get('code'),
					auth()->id()
				);
				
				if (auth()->user()->role === 'vendor') {
					return redirect('/vendor/wholesale2b/products/import')
						->with('success', 'AutoDS connected successfully');
				}

				if (auth()->user()->role === 'admin') {
					return redirect('/admin/wholesale2b/products/import')
						->with('success', 'AutoDS connected successfully');
				}

				return redirect('/')->with('success', 'AutoDS connected successfully');

			} catch (\Throwable $e) {
				Log::error('AutoDS callback failed', [
					'error' => $e->getMessage()
				]);
				
				if (auth()->user()->role === 'vendor') {
					return redirect('/vendor/wholesale2b/products/import')
						->with('error', 'AutoDS connection failed');
				}

				if (auth()->user()->role === 'admin') {
					return redirect('/admin/wholesale2b/products/import')
						->with('error', 'AutoDS connection failed');
				}
				
				return redirect('/')
					->with('error', 'AutoDS connection failed');
			}
		} */
		
		if ($request->has('code')) {
			try {

				$stateData = [];
				if ($request->filled('state')) {
					$stateData = json_decode(
						base64_decode($request->get('state')),
						true
					) ?? [];
				}
				
				if (
					auth()->check() &&
					(!isset($stateData['flow']) || $stateData['flow'] === 'web')
				) {
					app(AutoDSService::class)->exchangeCodeForToken(
						$request->get('code'),
						auth()->id()
					);

					if (auth()->user()->role === 'vendor') {
						return redirect('/vendor/wholesale2b/products/import')
							->with('success', 'AutoDS connected successfully');
					}

					if (auth()->user()->role === 'admin') {
						return redirect('/admin/wholesale2b/products/import')
							->with('success', 'AutoDS connected successfully');
					}

					return redirect('/')
						->with('success', 'AutoDS connected successfully');
				}

				if (
					isset($stateData['flow']) &&
					$stateData['flow'] === 'api'
				) {
					
					app(AutoDSService::class)->exchangeCodeForToken(
						$request->get('code'),
						$stateData['vendor_id']
					);

					return response()->json([
						'success' => true,
						'message' => 'AutoDS connected successfully',
					]);
				}

				return redirect('/login')
					->with('error', 'AutoDS connection failed. Please login first as a store owner.');

			} catch (\Throwable $e) {
				Log::error('AutoDS callback failed', [
					'error' => $e->getMessage(),
				]);

				if (auth()->check()) {
					if (auth()->user()->role === 'vendor') {
						return redirect('/vendor/wholesale2b/products/import')
							->with('error', 'AutoDS connection failed');
					}

					if (auth()->user()->role === 'admin') {
						return redirect('/admin/wholesale2b/products/import')
							->with('error', 'AutoDS connection failed');
					}
				}

				return response()->json([
					'success' => false,
					'message' => 'AutoDS connection failed',
				], 500);
			}
		}

		
		
		$stores     = VendorStore::all();
        //$products   = Product::with('categories','attributes','stores','reviews')->get();
        $products = Product::with([
			'categories',
			'productAttributes.attribute',
			'productAttributes.variants.attributeValue',
			'stores.user',
			'galleryImages',
			'brand'
		])
		->latest() 
		->take(10)
		->get();
		
		#PRODUCT DISPLAY ACCORDING TO CATEGORY WISE (DISPLAY ONLY 4 CATEGORY AND EACH CATEGORY ONLY 6 PRODUCT)
		$categoriesProduct = Category::where('status', 1)->whereNull('parent_id')->take(4)->get();
		$categoriesProduct->load([
			'children.children' => function ($query) {
				$query->with(['products' => function ($query) {
					$query->with([
						'galleryImages',
						'brand',
						'stores.user',
						'productAttributes.attribute',
						'productAttributes.variants.attributeValue'
					])
					->latest()
					->take(12); 
				}]);
			}
		]);

		$parentCategoriesAndChiled = Category::where('status', 1)
			->whereNull('parent_id')
			->with('children')
			->paginate(4);		
			
		$categories = Category::where('status', 1)->whereNull('parent_id')->get();
		$sliders = Sliders::where('status', 1)->get();
		$posts = SidePost::first();
		
		#COMPAIGN LIST
		$campaigns = PartnerCampaign::where('status', 'active')
			->whereDate('start_date', '<=', now())
			->whereDate('end_date', '>=', now())
			->with(['products' => function($q){
				$q->where('status', 'approved');
			}])
			->latest()
			->get();

		// Group products → category wise → vendor wise
		foreach ($campaigns as $campaign) {
			$campaign->groupedProducts =
				$campaign->products
					->groupBy('category_id')     // Category cart
					->map(function ($categoryItems) {
						return $categoryItems->groupBy('vendor_id'); // Vendor wise
					});
		}
		
	
		return view('index',compact('stores','categories','products','sliders','posts','parentCategoriesAndChiled','categoriesProduct','campaigns'));
    }
	
	
	
	public function switchCurrency($code)
	{
		$currency = Currency::where('code', $code)->first();
		
		if ($currency) {
			Session::put('currency_code', $currency->code);
			if (auth()->check()) { 
				auth()->user()->update(['currency_code' => $currency->code]);
			}
		}
		return redirect()->back();
	}
	
	public function showProducts(VendorStore $store)
	{  
		$store->load(['products.categories']);
		//dd($store->products);
		return view('products', compact('store')); 
		
	}

	public function review_submit(Request $request)
	{
		// Check if user is logged in
		if (!auth()->check()) {
			$product = Product::find($request->product_id);
			$slug = $product ? $product->slug : 'product';
			return Redirect::to('/product/' . $slug . '#review-section')->with('error', 'Please login to post a review.');
		}

		$validated = $request->validate([
			'rating'     => 'required|integer|min:1|max:5',
			'review'     => 'required|string',
			'author'     => 'required|string',
			'email'      => 'required|email',
			'product_id' => 'required|integer',
		]);

		$userId = auth()->id();
		$product = Product::where('id', $request->product_id)->first();

		// Check if user already reviewed this product
		$existingReview = Review::where('user_id', $userId)
								->where('product_id', $validated['product_id'])
								->first();

		if ($existingReview) {
			return Redirect::to('/product/' . $product->slug . '#review-section')->with('error', 'You have already submitted a review for this product.');
		}

		// Add user_id to validated data
		$validated['user_id'] = $userId;

		Review::create($validated);

		return Redirect::to('/product/' . $product->slug . '#review-section')->with('success', 'Review submitted successfully!');
	}


	public function registerAsVendor(Request $request){
		
		return view('auth.register_as_vendor');
	}
	
	public function registerAsVendorAction(Request $request) {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
			'phone' => 'required|unique:users,phone',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
			'g-recaptcha-response' => 'required',
        ]);
		
		 // Verify reCAPTCHA
        $recaptchaResponse = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip()
        ])->json();

        if (!$recaptchaResponse['success']) {
            return back()->withErrors(['g-recaptcha-response' => 'reCAPTCHA verification failed.'])->withInput();
        }
		
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => 'Vendor',
            'password' => Hash::make($request->password),
        ]);

		return redirect('/login')->with('success', 'You have successfuly registered as vendor.');
    }
}
