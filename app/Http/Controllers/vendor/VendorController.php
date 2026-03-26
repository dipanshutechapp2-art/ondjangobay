<?php
namespace App\Http\Controllers\vendor;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\User;
use App\Models\VendorStore;
use App\Models\Product;
use App\Models\Order;
use DB;

class VendorController extends Controller
{

    public function index(Request $request) {
        
		$totalStores    = VendorStore::where('user_id',auth()->user()->id)->count();
		$totalUsers     = User::where('id',auth()->user()->id)->count();
		$totalOrder     = Order::where('vendor_id',auth()->user()->id)->count();
		
		$totalProducts  = Product::where('seller_id',auth()->user()->id)->count();
		
		$revenues = Order::where('vendor_id', auth()->id())->where('payment_status', 'paid')->select('currency', DB::raw('SUM(total_amount) as total'))->groupBy('currency')->get();
		
        return view('vendor.dashboard',compact('totalStores','totalUsers','totalProducts','revenues', 'totalOrder'));
    }
    
	public function profile(Request $request) {
	    
		$vendorInfo = auth()->user();
		return view('vendor.profile',compact('vendorInfo'));
	}
	
	public function update_profile_action(Request $request) {
		
		$validator  = \Validator::make($request->all(), [
            'name'  => ['required', 'string', 'max:255'],
			'image' => ['image', 'mimes:jpg,jpeg,png,gif', 'max:2048'], // 2MB
        ]);
		
        if ($validator->fails()) {
			return redirect()->back()->withErrors($validator)->withInput();
        }
		
        $dataArray             = array();
        $dataArray['name']     = $request->name;
       
        if (isset($request->image)) {
			
			#IMG DELETE BEFORE UPDATE RECORD
			if (auth()->user()->image && File::exists(public_path('/uploads/users/'.auth()->user()->image))) {
				File::delete(public_path('/uploads/users/'.auth()->user()->image));
			}
			
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/users/'), $imageName);
            $dataArray['image'] = $imageName;
        }

        $check = User::where('id', auth()->user()->id)->update($dataArray);

        if ($check) {
            return redirect()->back()->with('success', 'You have successfully updated!');
        } else {
            return redirect()->back()->with('failed', 'You have not successfully updated!');
        }
    }
	
    public function change_password() {
	
		return view('vendor.change-password');
	}
    
    public function change_password_action(Request $request) {

        $validator = \Validator::make($request->all(), [
            'current_password' => 'required',
            'password'         => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect('/vendor/change-password')->withErrors($validator)->withInput();
        }

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect('/vendor/change-password')
                ->withErrors(['current_password' => 'The provided password does not match your current password.'])
                ->withInput()
                ->with('status', 'Failed');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect('/vendor/change-password')->with('success', 'Password Change Successfully!');
    }   
}