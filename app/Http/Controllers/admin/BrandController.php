<?php

namespace App\Http\Controllers\admin;

use App\Models\Brands;
use App\Models\Sliders;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\File;

class BrandController extends Controller
 {
   public function index(Request $request)
     {
        $brands = Brands::get();
		
		if ($request->ajax()) {
			
			$brands = Brands::get();
			
			return DataTables::of($brands)
				->addColumn('status', function($brands) {
                    
					$checked = $brands->status == 1 ? 'checked' : '';
					return '
						<input type="checkbox" name="status" class="category-status-switch"
							data-bootstrap-switch data-off-color="danger"
							data-on-color="success" '.$checked.'
							data-id="'.$brands->id.'">';
				})
				->addColumn('created_at', function($brands) {
					return date('Y-m-d H:i',strtotime($brands->created_at));
				})

				->addColumn('action', function($brands) {					
					$deleteUrl = route('brands.destroy', $brands->id);
					$token = csrf_token();
					return '
						<div class="btn-group">
							<button type="button" class="btn btn-success">Action</button>
							<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
								<span class="sr-only">Toggle Dropdown</span>
							</button>
							<div class="dropdown-menu" role="menu">
								<a class="dropdown-item delete-category" href="#" data-id="'.$brands->id.'">Delete</a>
								<form id="delete-form-'.$brands->id.'" action="'.$deleteUrl.'" method="POST" style="display: none;">
									<input type="hidden" name="_token" value="'.$token.'">
									<input type="hidden" name="_method" value="DELETE">
								</form>
							</div>
						</div>
					';
				})
				->rawColumns(['action','status'])
				->make(true);
		}
		
        return view('admin.brands.list', compact('brands'));
    }

     public function create()
        {
            return view('admin.brands.create');
        }

public function store(Request $request)
{
    $request->validate([
        'title'          => 'required|string|max:255',
    ]);

    $imageName = null;
    $mobileimageName = null;

    Brands::create([
        'title'          => $request->title,
    ]);

    return redirect()->route('brands.index')->with('success', 'brands created successfully.');
}
        
    public function edit($id)
        {
            $sliders = Sliders::get();
            $slider = Sliders::findOrFail($id);
            return view('admin.brands.edit', compact('slider','brands'));
        }

   public function update(Request $request, string $id)
    {
    $request->validate([
        'title' => 'required|string|unique:sliders,title,' . $id,
    ]);

    $slider = Sliders::findOrFail($id);

    // Store current image names to reuse if no new file uploaded
    $imageName = $slider->desktop_image;
    $mobileimageName = $slider->mobile_image;

    // Handle desktop image
    if ($request->hasFile('desktop_image')) {
        if ($imageName && file_exists(public_path('uploads/sliders/' . $imageName))) {
            unlink(public_path('uploads/sliders/' . $imageName));
        }

        $imageName = uniqid() . '.' . $request->desktop_image->extension();
        $request->desktop_image->move(public_path('uploads/sliders'), $imageName);
    }

    // Handle mobile image
    if ($request->hasFile('mobile_image')) {
        if ($mobileimageName && file_exists(public_path('uploads/sliders/' . $mobileimageName))) {
            unlink(public_path('uploads/sliders/' . $mobileimageName));
        }

        $mobileimageName = uniqid() . '.' . $request->mobile_image->extension();
        $request->mobile_image->move(public_path('uploads/sliders'), $mobileimageName);
    }

    try {
        $slider->title = $request->title;
        $slider->url = $request->url;
        $slider->description = $request->description;
        $slider->desktop_image = $imageName;
        $slider->mobile_image = $mobileimageName;
        $slider->save();

        return redirect()->route('sliders.index')->with('success', 'sliders updated successfully.');

    } catch (\Throwable $th) {
        return redirect()->back()->with('error', 'Failed to update slider: ' . $th->getMessage())->withInput();
    }
}

   public function destroy(Brands $brand)
        {
            $brand->delete();
            return redirect()->route('brands.index')->with('success', 'brands deleted successfully.');
        }

    public function updateStatus(Request $request)
        {
            $brand = Brands::find($request->id);

            if ($brand) {
                $brand->status = $request->status;
                $brand->save();
                return response()->json(['success' => true]);
            }
            return response()->json(['success' => false], 404);
        }

}
