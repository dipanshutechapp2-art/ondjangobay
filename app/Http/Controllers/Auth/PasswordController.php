<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\File;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
    
    function view_login_auth($name)
    {
        $userName = str_replace(' ', '_', $name); 
        $userFolder = public_path("uploads/$userName");
    
        // Check if user folder exists
        $folders = [];
        if (is_dir($userFolder)) {
            // Get all subdirectories inside user folder and extract only folder names
            $folders = array_map('basename', array_filter(glob($userFolder . '/*'), 'is_dir'));
        }
    // dd($folders);
        return view('auth.auth-check', compact('folders','name'));
    }


    function login_auth($name, $folder)
    {
        $userName = str_replace(' ', '_', $name); 
        $userFolder = public_path("uploads/$userName/$folder"); // Specific folder inside user folder
    
        $screenshots = [];
    
        // Check if folder exists
        if (is_dir($userFolder)) {
            // Get all image files
            $images = glob($userFolder . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    
            // Sort images by latest modified time (descending order)
            usort($images, function ($a, $b) {
                return filemtime($b) - filemtime($a);
            });
    
            // Convert absolute paths to URLs
            foreach ($images as $image) {
                $screenshots[] = asset(str_replace(public_path(), 'public', $image));
            }
        }
    
        // Implement pagination (15 per page)
        $perPage = 16;
        $currentPage = request()->input('page', 1);
        $currentItems = array_slice($screenshots, ($currentPage - 1) * $perPage, $perPage);
        $paginatedScreenshots = new LengthAwarePaginator($currentItems, count($screenshots), $perPage, $currentPage, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);
    
        return view('auth.auth', compact('paginatedScreenshots', 'name', 'folder'));
    }

    
    public function login_auth_trash(Request $request)
    {
        // Remove domain URL and leading slash
        $relativePath = str_replace(url('/'), '', $request->image);
        $relativePath = ltrim($relativePath, '/'); 
    
        // Ensure 'public/' appears only once
        $relativePath = str_replace('public/', '/', $relativePath);
        
        // Get the correct file path
        $imagePath = public_path($relativePath);
    
        // Debugging (Check if file exists)
        if (!file_exists($imagePath)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found.',
                'path' => $imagePath
            ], 404);
        }
    
        // Delete the file
        unlink($imagePath);
    
        return response()->json([
            'success' => true,
            'message' => 'Screenshot deleted successfully.'
        ]);
    }




}
