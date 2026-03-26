<?php

namespace App\Http\Controllers\admin;

use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
class BlogController extends Controller
{
    // Display all categories
    public function index()
    {
        $blogs = Blog::get();
        return view('admin.blog.list', compact('blogs'));
    }

    // Show create form
    public function create()
    {
        $categories = BlogCategory::where('status', 1)->get();
        return view('admin.blog.add', compact('categories'));
    }

    // Store new blog-category
    public function store(Request $request)
    {
       
        $request->validate([
            'title' => 'required|string|unique:blogs,title',
            'category_id' => 'required|exists:blog_category,id',
            'photo' => 'required',
            'description' => 'required',
        ]);

        if (isset($request->photo)) {
            $imageName = uniqid() . '.' . $request->photo->extension();
            $request->photo->move(public_path('uploads/blogs'), $imageName);
        }

        Blog::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'category_id' => $request->category_id,
            'photo' => isset($imageName) ? $imageName : null,
            'description' => $request->description,
            'tags' => $request->tags ?? null,
            'source' => $request->source ?? null,
            'meta_tags' => $request->meta_tags ?? null,
            'meta_description' => $request->meta_description ?? null,
        ]);

        return redirect()->route('blog.index')->with('success', 'Blog created successfully.');
    }

    // Show edit form
    public function edit(Blog $blog)
    {
        $categories = BlogCategory::where('status', 1)->get();
        return view('admin.blog.edit', compact('blog', 'categories'));
    }
    // Update existing blog
    public function update(Request $request, Blog $blog)
    {
        $request->validate([
            'title' => 'required|string|unique:blogs,title,' . $blog->id,
            'category_id' => 'required|exists:blog_category,id',
            'photo' => 'nullable|image',
            'description' => 'required|string',
        ]);

        $data = [
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'category_id' => $request->category_id,
            'description' => $request->description,
            'tags' => $request->tags ?? null,
            'source' => $request->source ?? null,
            'meta_tags' => $request->meta_tags ?? null,
            'meta_description' => $request->meta_description ?? null,
        ];

        if ($request->hasFile('photo')) {
            // Remove old image if exists
            if ($blog->photo && file_exists(public_path('uploads/blog/' . $blog->photo))) {
                unlink(public_path('uploads/blog/' . $blog->photo));
            }
            // Save new image
            $imageName = uniqid() . '.' . $request->photo->extension();
            $request->photo->move(public_path('uploads/blog'), $imageName);
            $data['photo'] = $imageName;
        }

        $blog->update($data);

        return redirect()->route('blog.index')->with('success', 'Blog updated successfully.');
    }


    public function destroy(Blog $blog)
    {
        // Remove photo if exists
        if ($blog->photo && file_exists(public_path('uploads/blog/' . $blog->photo))) {
            unlink(public_path('uploads/blog/' . $blog->photo));
        }
        $blog->delete();
        return redirect()->route('blog.index')->with('success', 'Blog deleted successfully.');
    }


    public function updateStatus(Request $request)
    {
        $blog = Blog::find($request->id);

        if ($blog) {
            $blog->status = $request->status;
            $blog->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }
}
