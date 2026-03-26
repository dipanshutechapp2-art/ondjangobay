<?php

namespace App\Http\Controllers\admin;

use App\Models\Sliders;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\File;

class SlidersController extends Controller
{
    public function index(Request $request)
    {
        $sliders = Sliders::get();
		
		if ($request->ajax()) {
			
			$sliders = Sliders::get();
			
			return DataTables::of($sliders)
				->addColumn('status', function($sliders) {
                    
					$checked = $sliders->status == 1 ? 'checked' : '';
					return '
						<input type="checkbox" name="status" class="category-status-switch"
							data-bootstrap-switch data-off-color="danger"
							data-on-color="success" '.$checked.'
							data-id="'.$sliders->id.'">';
				})
				->addColumn('created_at', function($sliders) {
					return date('Y-m-d H:i',strtotime($sliders->created_at));
				})

				->addColumn('action', function($sliders) {
					
					$editUrl   = route('sliders.edit', $sliders->id);
					$deleteUrl = route('sliders.destroy', $sliders->id);
					$token = csrf_token();

					return '
						<div class="btn-group">
							<button type="button" class="btn btn-success">Action</button>
							<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
								<span class="sr-only">Toggle Dropdown</span>
							</button>
							<div class="dropdown-menu" role="menu">
								<a class="dropdown-item" href="'.$editUrl.'" data-id="'.$sliders->id.'" data-name="'.$sliders->name.'">Edit</a>
								<a class="dropdown-item delete-category" href="#" data-id="'.$sliders->id.'">Delete</a>
								<form id="delete-form-'.$sliders->id.'" action="'.$deleteUrl.'" method="POST" style="display: none;">
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
		
        return view('admin.sliders.list', compact('sliders'));
    }

     public function create()
        {
            return view('admin.sliders.create');
        }

public function store(Request $request)
{
    $request->validate([
        'title'          => 'required|string|max:255',
        'url'            => 'nullable',
        'description'    => 'nullable',
        'desktop_image'  => 'required|image',
        'mobile_image'   => 'required|image',
    ]);

    $imageName = null;
    $mobileimageName = null;

    if ($request->hasFile('desktop_image')) {
        $imageName = uniqid() . '.' . $request->file('desktop_image')->getClientOriginalExtension();
        $request->file('desktop_image')->move(public_path('uploads/sliders'), $imageName);
    }

    if ($request->hasFile('mobile_image')) {
        $mobileimageName = uniqid() . '.' . $request->file('mobile_image')->getClientOriginalExtension();
        $request->file('mobile_image')->move(public_path('uploads/sliders'), $mobileimageName);
    }

    Sliders::create([
        'title'          => $request->title,
        'url'            => $request->url,
        'description'    => $request->description,
        'desktop_image'  => $imageName,
        'mobile_image'   => $mobileimageName,
    ]);

    return redirect()->route('sliders.index')->with('success', 'Sliders created successfully.');
}

        
    public function edit($id)
        {
            $sliders = Sliders::get();
            $slider = Sliders::findOrFail($id);
            return view('admin.sliders.edit', compact('slider','sliders'));
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

    public function destroy(Sliders $slider)
        {
            $slider->delete();
            return redirect()->route('sliders.index')->with('success', 'sliders deleted successfully.');
        }

    public function updateStatus(Request $request)
        {
            $slider = Sliders::find($request->id);

            if ($slider) {
                $slider->status = $request->status;
                $slider->save();

                return response()->json(['success' => true]);
            }
            return response()->json(['success' => false], 404);
        }

}
