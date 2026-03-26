<?php

namespace App\Http\Controllers\admin;

use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
class BlogCategoryController extends Controller
{
    // Display all categories
    public function index()
    {
        $categories = BlogCategory::orderBy('name')->get();
        return view('admin.blog-category.list', compact('categories'));
    }

    // Show create form
    public function create()
    {
        return view('admin.blog-category.create');
    }

    // Store new blog-category
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:blog_category,name',
            'image' => 'required|image',
        ]);

        if (isset($request->image)) {
            $imageName = uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/blog-category'), $imageName);
        }

        BlogCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => isset($imageName) ? $imageName : null,

        ]);

        return redirect()->route('blog-category.index')->with('success', 'blog category created successfully.');
    }

    // Show edit form
    public function edit(BlogCategory $category)
    {
        return view('admin.blog-category.edit', compact('category'));
    }

    // Update existing category
    public function update(Request $request, BlogCategory $category)
    {
        $request->validate([
            'name' => 'required|string|unique:blog_category,name,' . $category->id,
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ];

        if ($request->hasFile('image')) {
            // Remove old image if exists
            if ($category->image && file_exists(public_path('uploads/blog-category/' . $category->image))) {
                unlink(public_path('uploads/blog-category/' . $category->image));
            }
            // Save new image
            $imageName = uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/blog-category'), $imageName);
            $data['image'] = $imageName;
        }

        $category->update($data);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('blog-category.index')->with('success', 'blog-category updated.');
    }

    // Delete category
    public function destroy($category)
    {
        BlogCategory::where('id', $category)->delete();
        return redirect()->route('blog-category.index')->with('success', 'blog category deleted successfully.');
    }

    public function updateStatus(Request $request)
    {
        $category = BlogCategory::find($request->id);

        if ($category) {
            $category->status = $request->status;
            $category->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }
}
