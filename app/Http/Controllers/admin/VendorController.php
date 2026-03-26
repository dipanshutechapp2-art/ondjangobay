<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::where('role', 'vendor')->get();
        return  view('admin.vendor.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.vendor.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:15',
        ]);
        try {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' =>  Hash::make($request->password),
                'phone' => $request->phone,
                'role' => 'vendor',
            ]);

            return redirect()->route('vendor.index')->with('success', 'Vendor created successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to create Vendor: ' . $th->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $agent = User::findOrFail($id);
        return view('admin.vendor.edit', compact('agent'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:15',
        ]);

        try {
            $agent = User::findOrFail($id);
            $agent->name = $request->name;
            $agent->email = $request->email;
            $agent->phone = $request->phone;

            if ($request->filled('password')) {
                $agent->password = Hash::make($request->password);
            }

            $agent->save();

            return redirect()->route('vendor.index')->with('success', 'Vendor updated successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to update Vendor: ' . $th->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $agent = User::findOrFail($id);
            $agent->delete();

            return redirect()->route('vendor.index')->with('success', 'Vendor deleted successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to delete Vendor: ' . $th->getMessage());
        }
    }
    
   public function updateStatus(Request $request)
    {
        $user = User::find($request->id);
        if ($user) {
            $user->status = $request->status;
            $user->save();

            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

}
