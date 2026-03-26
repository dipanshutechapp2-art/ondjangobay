<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;
use App\Models\Category;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\VendorStore;
use App\Models\ProductAttribute;
use App\Models\ProductVariant;
use App\Models\Brands;
use App\Models\SearchHistory;
use Auth;

class ProductController extends Controller
{
    
	public function shopList(Request $request)
	{
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
		
		
		$q = $request->query('search', '');
		if (!empty($q)) {
			$query->where(function ($subQuery) use ($q) {
				$subQuery->where('name', 'like', "%{$q}%")
					->orWhere('meta_keyword', 'like', "%{$q}%")
					->orWhere('sku', 'like', "%{$q}%")
					->orWhereHas('brand', function ($b) use ($q) {
						$b->where('title', 'like', "%{$q}%");
					});
			});
			
		    #STORE SEARCH HISTORY IN DATABASE
			if (Auth::check()) {
				SearchHistory::updateOrCreate(
					[
						'user_id' => Auth::id(),
						'query' => $q, 
					],
					[
						'updated_at' => now(),
					]
				);
			}
		}
		
		if ($request->filled('category')) {
			$categoryIds = is_array($request->category)
				? $request->category
				: explode(',', $request->category);

			$query->whereHas('categories', function ($q) use ($categoryIds) {
				$q->whereIn('categories.id', $categoryIds);
			});
		}
		
		if ($request->filled('brands')) {
			$brandIds = is_array($request->brands)
				? $request->brands
				: explode(',', $request->brands);
				
			$query->whereIn('brand_id', $brandIds);
		}

		if ($request->filled('min_price')) {
			$query->where('price', '>=', $request->min_price);
		}
		if ($request->filled('max_price')) {
			$query->where('price', '<=', $request->max_price);
		}
		
		#STOCK AVAILABILITY
		if ($request->has('availability') && is_array($request->availability)) {
			$availabilityConditions = [];
			
			if (in_array('in_stock', $request->availability)) {
				$availabilityConditions[] = ['quantity', '>', 0];
			}
			
			if (in_array('out_of_stock', $request->availability)) {
				$availabilityConditions[] = ['quantity', '<=', 0];
			}
			
			if (!empty($availabilityConditions)) {
				$query->where(function($q) use ($availabilityConditions) {
					foreach ($availabilityConditions as $condition) {
						$q->orWhere([$condition]);
					}
				});
			}
		}
		
		#CONDITION
		if ($request->has('types') && is_array($request->types)) {
			$query->whereIn('type', $request->types);
		}
		
		#MIN-RATING
		if ($request->filled('min_rating')) {
			$query->whereHas('reviews', function ($q) use ($request) {
				$q->select('product_id')
				  ->groupBy('product_id')
				  ->havingRaw('AVG(rating) >= ?', [$request->min_rating]);
			});
		}
		
		switch ($request->sort) {
			case 'most-popular': 
				$query->withCount('orders')->orderBy('orders_count', 'desc');
				break;

			case 'new-arrivals':
				$query->orderBy('created_at', 'desc');
				break;

			case 'top-rated':
				$query->withAvg('reviews', 'rating')->orderBy('reviews_avg_rating', 'desc');
				break;

			case 'price-low':
				$query->orderBy('price', 'asc');
				break;

			case 'price-high':
				$query->orderBy('price', 'desc');
				break;

			default:
				$query->orderBy('created_at', 'desc');
		}
		
		$products = $query->paginate(20)->withQueryString();
		
		#DEFULT SELECTED CATEGORY START
		$selectedCategoryId = $request->input('category');
		$expandedCategoryIds = [];

		if ($selectedCategoryId) {
			$current = \App\Models\Category::find($selectedCategoryId);

			while ($current) {
				$expandedCategoryIds[] = $current->id;
				$current = $current->parent;
			}
		}
		#DEFULT SELECTED CATEGORY END
		
		$categories = Category::with('children.children.children')
			->where('status', 1)
			->whereNull('parent_id')
			->get();


		$brands     = Brands::where('status', 1)->get();

		return view('products.shop', compact('products', 'categories', 'brands','expandedCategoryIds','selectedCategoryId'));
	}
	
	public function suggestions(Request $request)
	{
		$q = trim($request->query('q', ''));
		if (strlen($q) < 1) return response()->json([]);

		$limit = 6;

		$products = Product::with('categories', 'attributes', 'stores', 'reviews')
			->where(function ($query) use ($q) {
				$query->where('name', 'like', "%{$q}%")
					  ->orWhere('meta_keyword', 'like', "%{$q}%")
					  ->orWhere('sku', 'like', "%{$q}%");
			})
			->orderByRaw("name LIKE '{$q}%' DESC")
			->limit($limit)
			->get(['id', 'name', 'slug'])
			->map(fn($p) => [
				'type' => 'Product',
				'label' => $p->name,
				'url'   => url('/product/' . $p->slug),
			]);

		$brands = Brands::where('title', 'like', "%{$q}%")
			->orderByRaw("title LIKE '{$q}%' DESC")
			->limit($limit)
			->get(['id', 'title'])
			->map(fn($b) => [
				'type' => 'Brand',
				'label' => $b->title,
				//'url' => route('search.page', ['search' => '', 'brand' => $b->id]),
				'url' => route('product.shopList', ['search' => '', 'brands' => $b->id]),
			]);

		$data = $products->concat($brands)->splice(0, $limit * 2);

		return response()->json($data);
	}

	public function product_details($slug) { 
	
	    $product  = Product::with('categories','productAttributes.attribute','productAttributes.variants.attributeValue','stores.user','galleryImages','brand')->where('slug', $slug)->with('reviews','stores.storeReviews')->first();

	    $products = Product::with('categories','attributes','stores.user')->get();

		if (!$product) {
			return view('errors.error_404');
		}
		
	   return view('products.product_details', compact('product','products'));
    }
	
	public function quickView($id) {
		
		$product  = Product::with('quickViewcategory','productAttributes.attribute','productAttributes.variants.attributeValue','galleryImages','brand','reviews')->where('id', $id)->first();

		return view('partials.quickview', compact('product'));
	}
}
