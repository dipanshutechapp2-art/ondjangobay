<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ZipArchive;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ImportImageController extends Controller
{
    public function showForm()
    {
        $uploadedPaths     = [];
        $defaultFilePath   = asset('/public/product_images/');
        return view('admin.import-images.upload', compact('uploadedPaths','defaultFilePath'));
    }

    public function upload(Request $request)
    {
	  
		$rules = [
			'images_zip' => 'nullable|file|mimes:zip|max:1048576', 
			'images.*'   => 'nullable|image|max:102400', 
		];

		$messages = [
			'images_zip.mimes' => 'Only ZIP files are allowed.',
			'images_zip.max'   => 'ZIP file cannot exceed 1 GB.',
			'images.*.image'  => 'Each file must be an image.',
			'images.*.max'    => 'Each image cannot exceed 100 MB.',
		];
		
		$validator = Validator::make($request->all(), $rules, $messages);
	
		if ($validator->fails()) {
			return redirect()->back()
				->withErrors($validator) // send errors to Blade
				->withInput();           // retain old input
		}

        $extractPath = public_path('product_images');
        if (!file_exists($extractPath)) {
            mkdir($extractPath, 0777, true);
        }

        $uploadedPaths = [];

        //  ZIP Upload
        if ($request->hasFile('images_zip')) {
            $zip = new ZipArchive;
            $file = $request->file('images_zip');
            $zipPath = $file->getRealPath();

            if ($zip->open($zipPath) === true) {
                $zip->extractTo($extractPath);
                $zip->close();

                $files = glob($extractPath . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
                foreach ($files as $f) {
                    $relativePath = 'product_images/' . basename($f);
                    $uploadedPaths[] = asset($relativePath); 
                }
            }
        }

        //  Multiple Image Upload
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move($extractPath, $filename);

                $relativePath = 'product_images/' . $filename;
                $uploadedPaths[] = asset($relativePath);
            }
        }

        if (empty($uploadedPaths)) {
            return back()->with('error', 'Please upload at least one ZIP or image.');
        }
		
		 $defaultFilePath   = asset('/public/product_images/');
		
        return view('admin.import-images.upload', compact('uploadedPaths','defaultFilePath'))
            ->with('success', 'Images uploaded successfully!');
    }
}
