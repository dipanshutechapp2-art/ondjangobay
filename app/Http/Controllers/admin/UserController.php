<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
  public function index(Request $request)
{
    if ($request->ajax()) {
        $users = User::where('role', 'user')->where('is_deleted','!=','1')->get();

        return DataTables::of($users)
            ->addColumn('status', function ($user) {
                $checked = $user->status == 1 ? 'checked' : '';
                return '
                    <input type="checkbox" name="status" class="users-status-switch"
                        data-bootstrap-switch data-off-color="danger"
                        data-on-color="success" '.$checked.'
                        data-id="'.$user->id.'">';
                })
            
            ->addColumn('created_at', function ($user) {
                return date('d-M-Y', strtotime($user->created_at));
            })
            ->addColumn('action', function ($user) {
                $editUrl   = route('users.edit', $user->id);
                $deleteUrl = route('users.destroy', $user->id);
                $token     = csrf_token();

                return '
                    <div class="btn-group">
                        <button type="button" class="btn btn-success">Action</button>
                        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <div class="dropdown-menu" role="menu">
                            <a class="dropdown-item" href="'.$editUrl.'">Edit</a>
                            <a class="dropdown-item delete-users" href="#" data-id="'.$user->id.'">Delete</a>
                            <form id="delete-form-'.$user->id.'" action="'.$deleteUrl.'" method="POST" style="display: none;">
                                <input type="hidden" name="_token" value="'.$token.'">
                                <input type="hidden" name="_method" value="DELETE">
                            </form>
                        </div>
                    </div>';
            })
            ->rawColumns(['status', 'action'])
            ->addIndexColumn()
            ->make(true);
    }

    return view('admin.users.index');
}

    public function create()
    {
        return view('admin.users.create');
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
                'role' => 'user',
            ]);

            return redirect()->route('users.index')->with('success', 'User created successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to create User: ' . $th->getMessage())->withInput();
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
        return view('admin.users.edit', compact('agent'));
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

            return redirect()->route('users.index')->with('success', 'User updated successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to update User: ' . $th->getMessage())->withInput();
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

            return redirect()->route('users.index')->with('success', 'User deleted successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to delete User: ' . $th->getMessage());
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
