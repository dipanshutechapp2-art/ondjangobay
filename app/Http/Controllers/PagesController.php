<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\VendorStore;
use App\Models\Category;
use App\Models\CategoryStore;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\ProductAttribute;
use App\Models\ProductVariant;
use App\Models\Brands;
use App\Models\StoreReview;

class PagesController extends Controller
{
   
    public function about_us()
    {   
	   return view('pages.about_us');
    }
	
	public function become_a_vendor()
    {   
	   return view('pages.become_a_vendor');
    }
	
	public function contact_us()
    {   
	   return view('pages.contact_us');
    }
	
	public function faq()
    {   
	   return view('pages.faq');
    }
	
	public function error_404()
    {   
	   return view('pages.error_404');
    }
	
	public function order_complete()
    {   
	   return view('pages.order_complete');
    }
	
	public function wishlist()
    {   
	   return view('pages.wishlist');
    }

	public function compare()
    {   
	   return view('pages.compare');
    }
	
	public function order_view()
    {   
	   return view('pages.order_view');
    }
	
	public function blogList()
    {   
	   return view('pages.blog');
    }
	
	public function single_blog()
    {   
	   return view('pages.single_blog');
    }
	
	public function vendors()
    {   
	   return view('pages.vendors');
    }
	
	/* public function stores()
    {   
	   $stores = VendorStore::with('user','storeReviews')->paginate(20);

	   return view('pages.stores',compact('stores'));
    } */
	
	
	public function stores(Request $request)
	{
		//$stores = VendorStore::with('user','storeReviews')->paginate(20);
		
		$categoryId = $request->category;

		/* $stores = VendorStore::when($categoryId, function ($query, $categoryId) {
			$query->whereHas('products.categories', function ($q) use ($categoryId) {
				$q->where('categories.id', $categoryId);
			});
		})->with(['vendor', 'products' => function ($q) use ($categoryId) {		
			if ($categoryId) {
				$q->whereHas('categories', fn($c) => $c->where('categories.id', $categoryId));
			}
		}, 'products.categories'])
		->paginate(10); */
		
		
		
		$stores = VendorStore::when($categoryId, function ($query, $categoryId) {
			$query->whereHas('categories', function ($q) use ($categoryId) {
				$q->whereRaw("FIND_IN_SET(?, category_store.category_id)", [$categoryId]);
			});
		})
		->with(['vendor', 'categories'])
		->paginate(10);
	
		//$storeCategories = CategoryStore::with('category')->select('category_id')->distinct()->get();

		$allCategoryIds = CategoryStore::pluck('category_id')->toArray();
		$categoryIds = collect($allCategoryIds)
			->flatMap(function ($ids) {
				return explode(',', $ids); 
			})
			->map(fn($id) => (int) trim($id)) 
			->filter()   
			->unique()  
			->values()
			->toArray();

		$storeCategories = Category::whereIn('id', $categoryIds)->get();
		
		return view('pages.stores',compact('stores','storeCategories'));
	}

	
	
	public function storesProduct(Request $request, $slug)
	{   
		$storeInfo = VendorStore::where('slug', $slug)->firstOrFail();
		
		
		/* FETCH STORE CATEGORY START */
		$allCategoryIds = CategoryStore::where('store_id', $storeInfo->id)->pluck('category_id')->first();
		$categoryIds = collect(explode(',', $allCategoryIds))
			->map(fn($id) => (int) trim($id))
			->filter()
			->unique()
			->values()
			->toArray();
		
		// Load children and grandchildren
		$storeCategories = Category::whereIn('id', $categoryIds)
			->where(function ($q) use ($categoryIds) {
				$q->whereNull('parent_id')
				  ->orWhereNotIn('parent_id', $categoryIds);
			})
			->with('children.children','parent') 
			->get();

		$selectedCategorySlug = $request->query('category');
		if (!$selectedCategorySlug && $storeCategories->count()) {
			$firstParent = $storeCategories->first(); 
			$selectedCategorySlug = $firstParent->slug;
		}

		/* FETCH STORE CATEGORY END */
		
		$store = VendorStore::with(['products' => function ($query) {
			$query->with([
				'categories',
				'attributes',
				'productAttributes.attribute',
				'productAttributes.variants.attributeValue',
				'galleryImages',
				'brand',
				'reviews'
			]);
		}, 'user'])->findOrFail($storeInfo->id);
		
		$storeReview = StoreReview::where('store_id', $storeInfo->id)
			->orderBy('id','DESC')
			->get();

		// Check query string for category
		$categorySlug  = $request->query('category');
		$productsQuery = $store->products();

		if ($categorySlug) {
			$category = Category::where('slug', $categorySlug)->first();
			if ($category) {
				$productsQuery = $productsQuery->whereHas('categories', function($q) use ($category) {
					$q->where('categories.id', $category->id);
				});
			}
		}

		$products = $productsQuery->paginate(10);
      
		return view('pages.storeProduct', [
			'storeCategories'          => $storeCategories,
			'storeReview'              => $storeReview,
			'storeInfo'                => $store,
			'products'                 => $products,
			'defSelectedCategorySlug'  => $selectedCategorySlug,
			'selectedCategory'         => $categorySlug ?? $selectedCategorySlug,
		]);
	}

	
	public function submitStoreReview(Request $request){
		
		$request->validate([
            'store_id'     => 'required|numeric',
            'rating'       => 'required|numeric',
            'review'       => 'required|string',
            'author'       => 'required|string',
            'email'        => 'required|string',
        ]);
		
		if(auth()->check()){
			
			$storeReview = new StoreReview;
			$storeReview->store_id  = $request->store_id;
			$storeReview->rating    = $request->rating;
			$storeReview->review    = $request->review;
			$storeReview->author    = $request->author;
			$storeReview->email     = $request->email;
			$storeReview->user_id   = auth()->id();
			$storeReview->save();
			
			return redirect()->back()->with('success', 'You have submitted successfully!');
			
		}else{
			
			return redirect()->back()->with('error', 'You should be logged in!');
		}
	}
	
	
	public function career()
    {   
	   return view('pages.career');
    }
	
	public function team_member()
    {   
	   return view('pages.team_member');
    }
	
	public function affilate()
    {   
	   return view('pages.affilate');
    }
	
	public function help()
    {   
	   return view('pages.help');
    }
	
	public function track_order()
    {   
	   return view('pages.track_order');
    }
	
	public function privacy_policy()
    {   
	   return view('pages.privacy_policy');
    }
	
	public function term_conditions()
    {   
	   return view('pages.term_conditions');
    }
	
	public function support_center()
    {   
	   return view('pages.support_center');
    }
	
	public function myCommunityDeal()
    {   
	   return view('pages.my_community_deal');
    }
	
	public function commodities()
    {   
	   return view('pages.commodities');
    }
	
	public function agriculturalCommodities()
    {   
	   return view('pages.agriculturalCommodities');
    }
	
	public function mineralsMaterials()
    {   
	   return view('pages.mineralsMaterials');
    }
}
