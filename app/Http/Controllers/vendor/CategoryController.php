<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();
		
		if ($request->ajax()) {
			
			$categories = Category::orderBy('name')->get();
			
			return DataTables::of($categories)
				->addColumn('parent', function($categories) {
					
					if(!empty($categories->parent_id)) {
						$parent = Category::find($categories->parent_id);
						return $parent->name ?? 'N/A';
					}else{
						return '--';
					}
				})
				->addColumn('status', function($categories) {
                    
					$checked = $categories->status == 1 ? 'checked' : '';
					return '
						<input type="checkbox" name="status" class="category-status-switch"
							data-bootstrap-switch data-off-color="danger"
							data-on-color="success" '.$checked.'
							data-id="'.$categories->id.'">';
				})
				->addColumn('created_at', function($categories) {
					return date('Y-m-d H:i',strtotime($categories->created_at));
				})
				->addColumn('action', function($categories) {
					
					$editUrl   = route('category.edit', $categories->id);
					$deleteUrl = route('category.destroy', $categories->id);
					$token = csrf_token();

					/*return '
						<div class="btn-group">
							<button type="button" class="btn btn-success">Action</button>
							<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
								<span class="sr-only">Toggle Dropdown</span>
							</button>
							<div class="dropdown-menu" role="menu">
								<a class="dropdown-item" href="'.$editUrl.'" data-id="'.$categories->id.'" data-name="'.$categories->name.'">Edit</a>
								<a class="dropdown-item delete-category" href="#" data-id="'.$categories->id.'">Delete</a>
								<form id="delete-form-'.$categories->id.'" action="'.$deleteUrl.'" method="POST" style="display: none;">
									<input type="hidden" name="_token" value="'.$token.'">
									<input type="hidden" name="_method" value="DELETE">
								</form>
							</div>
						</div>
					'; */
					
					return '
						<div class="btn-group">
							<button type="button" class="btn btn-success">Action</button>
							<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
								<span class="sr-only">Toggle Dropdown</span>
							</button>
							<div class="dropdown-menu" role="menu">
								<a class="dropdown-item" href="'.$editUrl.'" data-id="'.$categories->id.'" data-name="'.$categories->name.'">Edit</a>
							</div>
						</div>
					';
				})
				->rawColumns(['action','status'])
				->make(true);
		}
		
        return view('vendor.category.list', compact('categories'));
    }

    public function create()
    {
        return view('vendor.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|unique:categories,name',
			'desktop_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
			'mobile_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);
		
		if (isset($request->desktop_image)) {
			$imageName = uniqid() . '.' . $request->desktop_image->extension();
			$request->desktop_image->move(public_path('uploads/categories'), $imageName);
		}

		if (isset($request->mobile_image)) {
			$mobileimageName = uniqid() . '.' . $request->mobile_image->extension();
			$request->mobile_image->move(public_path('uploads/categories'), $mobileimageName);
		}
		
        Category::create([
            'name'            => $request->name,
            'parent_id'       => $request->parent_id,
            'slug'            => Str::slug($request->name),
			'desktop_image'   => isset($imageName) ? $imageName : null,
             'mobile_image'   => isset($mobileimageName) ? $mobileimageName : null,
        ]);

        return redirect()->route('category.index')->with('success', 'Category created successfully.');
    }

    public function edit($id)
    {
        $categories = Category::orderBy('name')->get();
        $category = Category::findOrFail($id);
        return view('vendor.category.edit', compact('category','categories'));
    }

     public function update(Request $request, string $id)
    {
			$request->validate([
				'name' => 'required|string|unique:categories,name,' . $id,
			]);
			
			$category = Category::findOrFail($id);

			// Store current image names to reuse if no new file uploaded
			$imageName = $category->desktop_image;
			$mobileimageName = $category->mobile_image;

			// Handle desktop image
			if ($request->hasFile('desktop_image')) {
				if ($imageName && file_exists(public_path('uploads/categories/' . $imageName))) {
					unlink(public_path('uploads/categories/' . $imageName));
				}

				$imageName = uniqid() . '.' . $request->desktop_image->extension();
				$request->desktop_image->move(public_path('uploads/categories'), $imageName);
			}

			// Handle mobile image
			if ($request->hasFile('mobile_image')) {
				if ($mobileimageName && file_exists(public_path('uploads/categories/' . $mobileimageName))) {
					unlink(public_path('uploads/categories/' . $mobileimageName));
				}

				$mobileimageName = uniqid() . '.' . $request->mobile_image->extension();
				$request->mobile_image->move(public_path('uploads/categories'), $mobileimageName);
			}
			
        try {

            $category->name          = $request->name;
			$category->slug          = Str::slug($request->name);
			$category->parent_id     = $request->parent_id;
			$category->desktop_image = $imageName;
			$category->mobile_image  = $mobileimageName;
			$category->save();

            return redirect()->route('category.index')->with('success', 'User updated successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to update User: ' . $th->getMessage())->withInput();
        }
    }

    public function destroy(Category $category)
    {
        $category->delete();
        
        return redirect()->route('category.index')->with('success', 'Category deleted successfully.');
    }

    public function updateStatus(Request $request)
    {
        $category = Category::find($request->id);

        if ($category) {
            $category->status = $request->status;
            $category->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }
}
