<?php

namespace App\Http\Controllers\admin;

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
use App\Models\ProductGallery;
use App\Models\Brands;
use App\Models\User;
use Auth;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;

class ProductController extends Controller
{
    public function index(Request $request) {
		
		if ($request->ajax()) {
			
			$products = Product::with('categories','attributes','stores')->get();
			
			return DataTables::of($products)
				->addColumn('price', function($products) {
                    
					return formatCurrency($products->price);
							
				})
				->addColumn('is_varient', function($products) {
                  
					if ($products->attributes->count() > 0) {
						return ' <span class="badge badge-success">Yes, has variants</span>';
					} else {
						return '<span class="badge badge-secondary">No, single product</span>';
					}
							
				})
				->addColumn('category', function($products) {
                   
					 return $products->categories->map(function ($category) {
						return '<span class="badge badge-primary">' . e($category->name) . '</span>';
					})->implode(' ');
				})
				->addColumn('stores', function($products) {
                   
					 return $products->stores->map(function ($stores) {
						return '<span class="badge badge-primary">' . e($stores->store_name) . '</span>';
					})->implode(' ');
				})
				->addColumn('image', function($products) {
                   
					if(!empty($products->image)) {
						$imagePath = asset('public/uploads/products/' . $products->image);
					    return '<img src="' . $imagePath . '" alt="Product Image" width="60" height="60">';
					}else{
						return "";
					}
				})
				->addColumn('status', function($products) {
                    
					$checked = $products->status == 1 ? 'checked' : '';
					return '
						<input type="checkbox" name="status" class="category-status-switch"
							data-bootstrap-switch data-off-color="danger"
							data-on-color="success" '.$checked.'
							data-id="'.$products->id.'">';
				})
				->addColumn('created_at', function($products) {
					return date('Y-m-d H:i',strtotime($products->created_at));
				})
				->addColumn('action', function($products) {
					
					$editUrl   = route('admin.products.edit', $products->id);
					$deleteUrl = route('admin.products.destroy', $products->id);
					$token = csrf_token();
			   
					return '
						<div class="btn-group">
							<button type="button" class="btn btn-success">Action</button>
							<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
								<span class="sr-only">Toggle Dropdown</span>
							</button>
							<div class="dropdown-menu" role="menu">
								<a class="dropdown-item" href="'.$editUrl.'" data-id="'.$products->id.'" data-name="'.$products->name.'">Edit</a>
								<a class="dropdown-item delete-category" href="#" data-id="'.$products->id.'">Delete</a>
								<form id="delete-form-'.$products->id.'" action="'.$deleteUrl.'" method="POST" style="display: none;">
									<input type="hidden" name="_token" value="'.$token.'">
									<input type="hidden" name="_method" value="DELETE">
								</form>
							</div>
						</div>
					';
				})
				->rawColumns(['action', 'status','image','is_varient','category','stores'])
				->make(true);
		}
		
		return view('admin.products.list');
    }

    public function create() {
		
		$categories   = Category::get();
		$vendors      = User::where('role','vendor')->get();
		$stores       = VendorStore::all();
		$attributes   = Attribute::with('values')->get();
		$brands=Brands::where('status','1')->get();
		
        return view('admin.products.create',compact('categories','attributes','stores','brands','vendors'));
    }

    public function store(Request $request) {
		
		$validated = $request->validate([
			'product_name'                            => ['required', 'string', 'max:255'],
			'meta_title'                              => ['nullable', 'string', 'max:255', 'max:255'],
			'meta_keyword'                            => ['nullable', 'string', 'max:255','unique:products,meta_keyword'],
			'meta_description'                        => ['nullable', 'string', 'max:500', 'max:255'],
			'sku'                                     => 'required|string|unique:products,sku|max:100',
			'price'                                   => ['required', 'numeric', 'min:1'],
			'quantity'                                => ['required', 'integer', 'min:1'],
			'category_ids'                            => 'required|array',
			'category_ids.*'                          => 'exists:categories,id',
			'store_ids'                               => ['required', 'array'],
			'store_ids.*'                             => ['exists:vendor_stores,id'],
			'short_description'                       => ['nullable', 'string',],
			'description '                            => ['nullable', 'string'],
			'image'                                   => ['required','image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
			'attributes'                              => 'nullable|array',
			'attributes.*.attribute_id'               => 'required_with:attributes.*|exists:attributes,id',
			'attributes.*.variants'                   => 'required_with:attributes.*.attribute_id|array|min:1',
			'attributes.*.variants.*.value'           => 'required_with:attributes.*.variants|string|max:255',
			'attributes.*.variants.*.price'           => 'required_with:attributes.*.variants|numeric|min:0',
			'attributes.*.variants.*.sku'             => 'required_with:attributes.*.variants|string|max:255|unique:product_variants,sku',
			'attributes.*.variants.*.stock'           => 'required_with:attributes.*.variants|integer|min:0',
			'attributes.*.variants.*.image'           => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
			'attributes.*.variants.*.existing_image'  => ['string', 'max:255'],
			'specifications'                          => ['nullable', 'array'],
            'specifications.*.key'                    => ['required_with:specifications.*.value', 'string', 'max:255'],
            'specifications.*.value'                  => ['required_with:specifications.*.key', 'string', 'max:255'],
			
		]);
		
		$specs = [];
		if(!empty($request->input('specifications', []))) {
			foreach ($request->input('specifications', []) as $spec) {
				if (!empty($spec['key']) && !empty($spec['value'])) {
					$specs[$spec['key']] = $spec['value'];
				}
			}
		}
		
		$product = new Product;
		$product->specifications     = $specs ?? null;
		//$product->seller_id          = Auth::user()->id;
		$product->seller_id          = $request->vendor_id;
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

		if (isset($request->image)) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/products/'), $imageName);
            $product->image    = $imageName;
        }
		
		$product->save();
		
		#STORE MULTILPE PRODUCT WITH CATEGORIES
		$product->categories()->attach($request->category_ids);
		
		#STORE MULTILPE PRODUCT WITH STORE
		$product->stores()->sync($request->store_ids);
		
		
		#STORE ATTRBUTES & VARIENTS
		if (isset($validated['attributes']) && is_array($validated['attributes'])) {
			$this->saveAttributesAndVariants($product, $validated['attributes']);
		}
		
		if ($request->hasFile('galler_image')) {
			foreach ($request->file('galler_image') as $image) {
				$imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
				$image->move(public_path('uploads/product/gallery'), $imageName);

				// Save to ProductGallery table
				ProductGallery::create([
					'product_id' => $product->id,
					'image'      =>$imageName,
				]);
			}
		}	
			
        return redirect()->route('admin.products.index')->with('success', 'Category created successfully.');
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
	
    public function edit($id) {
		
		$attributes         = Attribute::with('values')->get();
        $categories         = Category::all();
        $product            = Product::with('attributes.variants','galleryImages')->findOrFail($id);
		$selectedCategories = $product->categories->pluck('id')->toArray();
		$stores             = VendorStore::all();
		$selectedStores     = $product->stores->pluck('id')->toArray();
		$brands            = Brands::where('status','1')->get();
		$vendors      = User::where('role','vendor')->get();
		
		return view('admin.products.edit', compact('product','categories','attributes','selectedCategories','selectedStores','stores','brands','vendors'));
    }

	public function update(Request $request, string $id) {
		
        try {
			
			$product = Product::where('id',$id)->first();
			
			if(empty($product->auto_ds_product_id)) {
				
				$validated = $request->validate([
					'product_name'                            => ['required', 'string', 'max:255'],
					'meta_title'                              => ['nullable', 'string', 'max:255', 'max:255'],
					'meta_keyword'                            => ['nullable', 'string', 'max:255','unique:products,meta_keyword,'.$id],
					'meta_description'                        => ['nullable', 'string', 'max:500', 'max:255'],
					'sku'                                     => 'required|string|unique:products,sku,'.$id,
					'price'                                   => ['required', 'numeric', 'min:1'],
					'quantity'                                => ['required', 'integer', 'min:1'],
					'category_ids'                            => 'required|array',
					'category_ids.*'                          => 'exists:categories,id',
					'store_ids'                               => ['required', 'array'],
					'store_ids.*'                             => ['exists:vendor_stores,id'],
					'short_description'                       => ['nullable', 'string'],
					'description'                             => ['nullable', 'string'],
					'specifications'                          => ['nullable', 'array'],
					'specifications.*.key'                    => ['required_with:specifications.*.value', 'string', 'max:255'],
					'specifications.*.value'                  => ['required_with:specifications.*.key', 'string', 'max:255'],
				]);
				
			}else{
			
				$validated = $request->validate([
					'product_name'                           => ['required', 'string', 'max:255'],
					'meta_title'                             => ['nullable', 'string', 'max:255', 'max:255'],
					'meta_keyword'                           => ['nullable', 'string', 'max:255','unique:products,meta_keyword,'.$id],
					'meta_description'                       => ['nullable', 'string', 'max:500', 'max:255'],
					'sku'                                    => 'required|string|unique:products,sku,'.$id,
					'price'                                  => ['required', 'numeric', 'min:1'],
					'quantity'                               => ['required', 'integer', 'min:1'],
					'category_ids'                           => 'required|array',
					'category_ids.*'                         => 'exists:categories,id',
					'store_ids'                              => ['required', 'array'],
					'store_ids.*'                            => ['exists:vendor_stores,id'],
					'short_description'                      => ['nullable', 'string'],
					'description'                           => ['nullable', 'string'],
					'image'                                  => ['image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
					'attributes'                             => 'nullable|array',
					'attributes.*.attribute_id'              => 'required_with:attributes.*|exists:attributes,id',
					'attributes.*.variants'                  => 'required_with:attributes.*.attribute_id|array|min:1',
					'attributes.*.variants.*.value'          => 'required_with:attributes.*.variants|string|max:255',
					'attributes.*.variants.*.price'          => 'required_with:attributes.*.variants|numeric|min:0',
					'attributes.*.variants.*.sku'            => 'required_with:attributes.*.variants|string|max:255',
					'attributes.*.variants.*.stock'          => 'required_with:attributes.*.variants|integer|min:0',
					'attributes.*.variants.*.image'          => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
					'attributes.*.variants.*.existing_image' => ['string', 'max:255'],
					'specifications'                          => ['nullable', 'array'],
					'specifications.*.key'                    => ['required_with:specifications.*.value', 'string', 'max:255'],
					'specifications.*.value'                  => ['required_with:specifications.*.key', 'string', 'max:255'],
				]);
			}
			
			$specs = [];
			if (!empty($request->specifications)) {
				foreach ($request->specifications as $spec) {
					if (!empty($spec['key']) && !empty($spec['value'])) {
						$specs[$spec['key']] = $spec['value'];
					}
				}
			}
			
			$product = Product::where('id',$id)->first();
			//$product->seller_id          = Auth::user()->id;
			$product->seller_id          = $request->vendor_id;
			$product->specifications     = $specs ?? null;
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

			if (isset($request->image)) {
				
				#IMG DELETE BEFORE UPDATE RECORD
				if ($product->image && File::exists(public_path('/uploads/products/'.$product->image))) {
					File::delete(public_path('/uploads/products/'.$product->image));
				}
				
				$imageName = time() . '.' . $request->image->extension();
				$request->image->move(public_path('uploads/products/'), $imageName);
				$product->image    = $imageName;
			}
			
			$product->save();
			
			#UPDATE PRODUCT WITH MULTIPLE CATEGORY
			$product->categories()->sync($request->category_ids);
			
			#STORE MULTILPE PRODUCT WITH STORE
			$product->stores()->sync($request->store_ids);
		
			if(empty($product->auto_ds_product_id)) {
			
				foreach($product->attributes as $oldAttribute) {
					$oldAttribute->variants()->delete();
					$oldAttribute->delete(); 
				}
			
				#STORE ATTRBUTES & VARIENTS
				if (isset($validated['attributes']) && is_array($validated['attributes'])) {
					foreach ($validated['attributes'] as $attributeData) {
						$productAttribute = ProductAttribute::create([
							'product_id'   => $product->id,
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
								'product_id' => $product->id,
								'product_attribute_id' => $productAttribute->id,
								'value'   => $variant['value'],
								'price'   => $variant['price'],
								'sku'     => $variant['sku'],
								'stock'   => $variant['stock'],
								'image'   => $imageName, 
							]);
						}
					}
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
			}

			return redirect()->route('admin.products.index')->with('success', 'Updated successfully.');
			
        } catch (\Throwable $th) {
			
            return redirect()->back()->with('error', 'Failed to update User: ' . $th->getMessage())->withInput();
        }
    }

    public function destroy(Product $product) {
		
		if ($product) {
			$product->deleteWithImage();
			return redirect()->route('admin.products.index')->with('success', 'Category deleted successfully.');
		}
    }

    public function updateStatus(Request $request) {
		
        $product = Product::find($request->id);

        if ($product) {
            $product->status = $request->status;
            $product->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

	public function exportProducts() {
		
		return Excel::download(new ProductsExport, 'products.xlsx');
		//return Excel::download(new ProductsExport, 'products.csv', \Maatwebsite\Excel\Excel::CSV);
	}
	
	public function importProducts(Request $request) {
		
		$request->validate([
			'file' => 'required|mimes:xlsx,xls,csv',
		]);

		Excel::import(new ProductsImport, $request->file('file'));

		return back()->with('success', 'Products imported successfully.');
	}

	public function delete($id)
{
    $gallery = ProductGallery::findOrFail($id);

    // Delete image file from server
    if (file_exists(public_path($gallery->image))) {
        unlink(public_path($gallery->image));
    }

    // Delete from DB
    $gallery->delete();

    return back()->with('success', 'Image deleted successfully');
}

	public function getVendorStores($vendorId)
	{
		$stores = VendorStore::where('user_id', $vendorId)->get(['id', 'store_name']);
		return response()->json($stores);
	}

}
