<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;
use App\Models\Language;

class LanguageController extends Controller
{
    public function index(Request $request) {
		
		if ($request->ajax()) {
		
			$languages = Language::orderBy('id','DESC')->get();

			return DataTables::of($languages)
				->addColumn('created_at', function ($languages) {
					return $languages->created_at->format('M d, Y');
				})
				->addColumn('image', function ($language) {
					$url = asset('uploads/languages/' . $language->image);
					return '<img src="' . $url . '" alt="Language Image" width="25">';
				})
				->addColumn('action', function($languages) {
						
						$editUrl     = route('languages.edit', $languages->id);
						$deleteUrl   = route('languages.destroy', $languages->id);
						$token       = csrf_token();
				   
						return '
							<div class="btn-group">
								<button type="button" class="btn btn-success">Action</button>
								<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<div class="dropdown-menu" role="menu">
									<a class="dropdown-item" href="'.$editUrl.'">Edit</a>
									<a class="dropdown-item delete-users" href="#" data-id="'.$languages->id.'">Delete</a>
									<form id="delete-form-'.$languages->id.'" action="'.$deleteUrl.'" method="POST" style="display: none;">
										<input type="hidden" name="_token" value="'.$token.'">
										<input type="hidden" name="_method" value="DELETE">
									</form>
								</div>
							</div>';
							})
				->rawColumns(['action','image'])
				->make(true);
		}
		
        return view('admin.languages.index');
    }

    public function create() {
		
        return view('admin.languages.create');
    }

    public function store(Request $request) {
		
        $request->validate([
            'name'  => 'required|string|max:50',
            'code'  => 'required|string|max:5|unique:languages,code',
			'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
		
		if(!empty($request->has('is_default'))) {
		  Language::where('is_default', true)->update(['is_default' => false]);
		}
		
        $language             = new Language();
        $language->name       = $request->name;
        $language->code       = $request->code;
        $language->is_default = $request->has('is_default');
		
		if ($request->hasFile('image')) {
			$filename = time() . '.' . $request->image->extension();
			$request->image->move(public_path('uploads/languages'), $filename);
			$language->image = $filename;
		}
		
        $language->save();

        if (!Language::where('is_default', true)->exists()) {
			$language->is_default = true;
			$language->save();
		}

        return redirect()->route('languages.index')->with('success', 'Language added successfully.');
    }

    public function edit($id) {
		
        $language = Language::findOrFail($id);
		
        return view('admin.languages.edit', compact('language'));
    }

    public function update(Request $request, $id) {
		
        $language = Language::findOrFail($id);

        $request->validate([
            'name'   => 'required|string|max:50',
            'code'   => 'required|string|max:5|unique:languages,code,' . $language->id,
			'image'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
		
		if(!empty($request->has('is_default'))) {
		  Language::where('is_default', true)->update(['is_default' => false]);
		}
		
        $language->name       = $request->name;
        $language->code       = $request->code;
        $language->is_default = $request->has('is_default');
		
		if ($request->hasFile('image')) {
			if($language->image && file_exists(public_path('uploads/languages/' . $language->image))) {
				unlink(public_path('uploads/languages/' . $language->image));
			}
			$filename = time() . '.' . $request->image->extension();
			$request->image->move(public_path('uploads/languages'), $filename);
			$language->image = $filename;
		}
		
        $language->save();
		
        if (!Language::where('is_default', true)->exists()) {
			$language->is_default = true;
			$language->save();
		}

        return redirect()->route('languages.index')->with('success', 'Language updated successfully.');
    }

    public function destroy($id) {
	   
        $language = Language::findOrFail($id);

        if ($language->is_default) {
            return redirect()->back()->with('error', 'Cannot delete the default language.');
        }
		
        $language->delete();
		
		if (!Language::where('is_default', true)->exists()) {
			$language->is_default = true;
			$language->save();
		}
		
        return redirect()->route('languages.index')->with('success', 'Language deleted successfully.');
    }

    public function setDefault($id) {
		
        Language::where('is_default', true)->update(['is_default' => false]);

        $language = Language::findOrFail($id);
        $language->is_default = true;
        $language->save();

        cache()->forget('default_language');

        return redirect()->back()->with('success', 'Default language updated.');
    }
}
