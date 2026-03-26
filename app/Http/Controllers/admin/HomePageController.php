<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\HomePage;
use App\Models\SidePost;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class HomePageController extends Controller
  {

   public function hero_section()
    {
        $hero_section = SidePost::first();
        return view('admin.homepage.hero', compact('hero_section'));
    }

public function hero_section_update(Request $request)
 {
    $request->validate([
        'title1' => 'required|string|max:255',
        'description1' => 'required|string|max:255',
        'url1' => 'required',
        'title2' => 'required|string|max:255',
        'description2' => 'required|string|max:255',
        'url2' => 'required',
        'desktop_image1' => 'nullable|image',
        'mobile_image1' => 'nullable|image',
        'desktop_image2' => 'nullable|image',
        'mobile_image2' => 'nullable|image',
    ]);

    $data = SidePost::first() ?? new SidePost();
    $heroDir = public_path('uploads/homepage/hero');

    if (!file_exists($heroDir)) {
        mkdir($heroDir, 0777, true);
    }

    // Upload Desktop Image 1
    if ($request->hasFile('desktop_image1')) {
        if (!empty($data->desktop_image1) && file_exists($heroDir . '/' . $data->desktop_image1)) {
            @unlink($heroDir . '/' . $data->desktop_image1);
        }
        $image = $request->file('desktop_image1');
        $image_name = 'desktop1-' . time() . '.' . $image->getClientOriginalExtension();
        $image->move($heroDir, $image_name);
        $data->desktop_image1 = $image_name;
    }

    // Upload Mobile Image 1
    if ($request->hasFile('mobile_image1')) {
        if (!empty($data->mobile_image1) && file_exists($heroDir . '/' . $data->mobile_image1)) {
            @unlink($heroDir . '/' . $data->mobile_image1);
        }
        $image = $request->file('mobile_image1');
        $image_name = 'mobile1-' . time() . '.' . $image->getClientOriginalExtension();
        $image->move($heroDir, $image_name);
        $data->mobile_image1 = $image_name;
    }

    // Upload Desktop Image 2
    if ($request->hasFile('desktop_image2')) {
        if (!empty($data->desktop_image2) && file_exists($heroDir . '/' . $data->desktop_image2)) {
            @unlink($heroDir . '/' . $data->desktop_image2);
        }
        $image = $request->file('desktop_image2');
        $image_name = 'desktop2-' . time() . '.' . $image->getClientOriginalExtension();
        $image->move($heroDir, $image_name);
        $data->desktop_image2 = $image_name;
    }

    // Upload Mobile Image 2
    if ($request->hasFile('mobile_image2')) {
        if (!empty($data->mobile_image2) && file_exists($heroDir . '/' . $data->mobile_image2)) {
            @unlink($heroDir . '/' . $data->mobile_image2);
        }
        $image = $request->file('mobile_image2');
        $image_name = 'mobile2-' . time() . '.' . $image->getClientOriginalExtension();
        $image->move($heroDir, $image_name);
        $data->mobile_image2 = $image_name;
    }

    // Set Text Content
    $data->title1 = $request->title1;
    $data->description1 = $request->description1;
    $data->url1 = $request->url1;
    $data->title2 = $request->title2;
    $data->description2 = $request->description2;
    $data->url2 = $request->url2;

    $data->save();

    return redirect()->back()->with('success', 'Hero section updated successfully.');
}


    # Sections Heading
    public function sections_heading()
    {
        $data = HomePage::first();
        $section = null;
        if ($data && $data->section) {
            $section = json_decode($data->section);
        }

        return view('admin.homepage.headings', compact('section'));
    }

    public function sections_heading_update(Request $request)
    {

        $data = HomePage::first();
        if (!$data) {
            $data = new HomePage();
        }
        $data->section = json_encode($request->section);
        $data->save();
        return redirect()->back()->with('success', 'Sections headings updated successfully.');
    }
}
