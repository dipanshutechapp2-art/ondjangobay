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
use Auth;

class SearchController  extends Controller
{
    
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
				'url' => route('search.page', ['search' => '', 'brand' => $b->id]),
			]);

		$data = $products->concat($brands)->splice(0, $limit * 2);

		return response()->json($data);
	}

	public function searchPage(Request $request)
	{
		$q        = $request->query('search', '');
		$brand    = $request->query('brand');
		$catInput = $request->query('category');

		$products = Product::query()
			->when($q, function ($query) use ($q) {
				$query->where(function ($subQuery) use ($q) {
					$subQuery->where('name', 'like', "%{$q}%")
							 ->orWhere('meta_keyword', 'like', "%{$q}%")
							 ->orWhere('sku', 'like', "%{$q}%")
							 ->orWhereHas('brand', function ($b) use ($q) {
								 $b->where('title', 'like', "%{$q}%");
							 });
				});
			})
			->when($brand, fn($q3) => $q3->where('brand_id', $brand))
			->when($catInput, function ($query) use ($catInput) {
				$query->whereHas('categories', function ($q) use ($catInput) {
					$q->where('category_id', $catInput);
				});
			})
			->with([
				'categories',
				'productAttributes.attribute',
				'productAttributes.variants.attributeValue',
				'stores.user',
				'galleryImages',
				'brand'
			])
			->paginate(20)
			->withQueryString();

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
		
		//$categories = Category::with('children')->where('status', 1)->whereNull('parent_id')->get();
		$categories = Category::with('children.children.children')
			->where('status', 1)
			->whereNull('parent_id')
			->get();
		
		$brands     = Brands::where('status', 1)->get();

		return view('products.search_results', compact('products', 'categories', 'brands', 'q', 'brand','expandedCategoryIds','selectedCategoryId'));
	}

}
