<?php

namespace App\Http\Controllers\admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Product;
use App\Models\ThemeSetting;
use App\Models\MailSetting;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;
use App\Models\WalletHistory;
use App\Models\Wallet;
use App\Models\Country;
use App\Models\Currency; 
use App\Models\ActivityLog;
use App\Models\Order;
use DB;

class AdminController extends Controller
{

    function index()
    {
        $vendors = User::where('role', 'vendor')->count();
        $users = User::where('role', 'user')->count();
        $products = Product::count();
		$revenues = Order::where('payment_status', 'paid')->select('currency', DB::raw('SUM(total_amount) as total'))->groupBy('currency')->get();

        return view('admin.dashboard',compact('vendors','users','products','revenues'));
    }

    function setting()
     {
        $user_id = auth()->user()->id;
        $data = User::where('id', $user_id)->first();
        return view('admin.setting', compact('data'));
     }

   public function setting_action(Request $request)
    {
        $dataArray = array();

        $dataArray['name'] = $request->name;
        $dataArray['email'] = $request->email;

        if (isset($request->image)) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('admin/images'), $imageName);
            $dataArray['image'] = $imageName;
        }

        $check = User::where('id', $request->user_id)->update($dataArray);

        if ($check) {
            return redirect()->back()->with('success', 'You have successfully updated!');
        } else {
            return redirect()->back()->with('failed', 'You have not successfully updated!');
        }
    }

    function change_password()
        {
            return view('admin.change-password');
        }
    
   public function change_password_action(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect('/admin/change-password')
                ->withErrors($validator)
                ->withInput();
        }

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect('/admin/change-password')
                ->withErrors(['current_password' => 'The provided password does not match your current password.'])
                ->withInput()
                ->with('status', 'Failed');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect('/admin/change-password')->with('success', 'Password Change Successfully!');
    }
    
        public function theme_setting()
    {
        $getCountry = Country::get();

        $setting = ThemeSetting::firstOrNew();
        return view('admin.theme-setting.edit', compact('setting','getCountry'));
    }
    public function theme_setting_action(Request $request)
    { 
        try {
            // Validate the incoming request data
            $validated = $request->validate([
                'site_name'            => 'required|string|max:255',
                'header_logo'          => 'nullable|image',
                'footer_logo'          => 'nullable|image',
                'address'              => 'nullable|string|max:255',
                'email'                => 'nullable|email',
                'primary_phone'        => 'nullable|string',
                'alt_phone'            => 'nullable|string|max:20',
                'copyright'            => 'nullable|string|max:255',
                'header_menu'          => 'nullable',
                'footer_menu1'         => 'nullable',
                'footer_menu2'         => 'nullable',
                'meta_tags'            => 'required',
                'meta_description'     => 'required',
                'country'     		   => 'required',
            ]);

            // Find the first setting or create a new one if it doesn't exist
            $setting = ThemeSetting::firstOrNew();

            // Update the setting fields with validated data
            $setting->fill($validated);


            // Handle logo upload
		if ($request->hasFile('favicon')) {
			$oldImage = $setting->favicon;
			$file_path = public_path('uploads/setting') . '/' . $oldImage;
			if ($oldImage && file_exists($file_path) && is_file($file_path)) {
				unlink($file_path);
			}
			$uploadedFavicon = $request->file('favicon');
			$extension = $uploadedFavicon->getClientOriginalExtension();
			//$faviconName = 'favicon.' . $extension;
		    $faviconName = 'favicon_'.time().'.'.$extension;
			$uploadedFavicon->move(public_path('uploads/setting'), $faviconName);
			$setting->favicon = $faviconName;
		}

		if ($request->hasFile('header_logo')) {
			$oldImage = $setting->header_logo;
			$file_path = public_path('uploads/setting') . '/' . $oldImage;
			if (file_exists($file_path) && is_file($file_path)) {
				unlink($file_path);
			}
			$uploadedHeaderLogo = $request->file('header_logo');
			$extension = $uploadedHeaderLogo->getClientOriginalExtension();
			//$headerLogoName = 'header-logo.' . $extension;
			$headerLogoName = 'header-logo'.time().'.'.$extension;
			$uploadedHeaderLogo->move(public_path('uploads/setting'), $headerLogoName);
			$setting->header_logo = $headerLogoName;
		}

		if ($request->hasFile('footer_logo')) {
			$oldImage = $setting->footer_logo;
			$file_path = public_path('uploads/setting') . '/' . $oldImage;
			if (file_exists($file_path) && is_file($file_path)) {
				unlink($file_path);
			}
			$uploadedFooterLogo = $request->file('footer_logo');
			$extension = $uploadedFooterLogo->getClientOriginalExtension();
			//$footerLogoName = 'footer-logo.' . $extension;
			$footerLogoName = 'footer-logo'.time().'.'.$extension;
			$uploadedFooterLogo->move(public_path('uploads/setting'), $footerLogoName);
			$setting->footer_logo = $footerLogoName;
		}

            // Save the setting
            $setting->save();

            // Redirect back with success message
            return redirect()->back()->with('success', 'Theme settings updated successfully.');
        } catch (\Throwable $th) {
            // Handle errors and redirect back with the error message
            return redirect()->back()->with('error', $th->getMessage())->withInput();
        }
    }

       
     public function mail_setting()
        {
            $setting = MailSetting::firstOrNew();
            return view('admin.mail-setting.edit', compact('setting'));
        }

    public function mail_setting_action(Request $request)
        {
          try {
            // Validate the incoming request data
            $validated = $request->validate([
                'smtp_host' => 'required',
                'smtp_port' => 'required',
                'encryption' => 'required',
                'smtp_username' => 'required',
                'smtp_password' => 'nullable',
                'from_email' => 'required',
                'from_name' => 'required',
            ]);

            // Find the first setting or create a new one if it doesn't exist
            $setting = MailSetting::firstOrNew();

            // Update the setting fields with validated data
            $setting->fill($validated);

            // Save the setting
            $setting->save();

            // Redirect back with success message
            return redirect()->back()->with('success', 'Mail settings updated successfully.');
        } catch (\Throwable $th) {
            // Handle errors and redirect back with the error message
            return redirect()->back()->with('error', $th->getMessage())->withInput();
        }
    }

  public function wallet_balance(Request $request)
        {
        if ($request->ajax()) {
            
			$users = Wallet::with('user')->get();
		
            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('name', function ($user) {
                    return $user->user->name;
                })
                ->addColumn('phone', function ($user) {
                    return $user->user->phone;
                })
                ->addColumn('wallet_balance', function ($user) {
                    return number_format($user->balance, 2);
                })
                ->addColumn('wallet_history', function ($user) {
                    $historyUrl = route('admin.wallet_history', $user->user->id);
                    return '<a href="'.$historyUrl.'" class="btn btn-success">Wallet History</a>';
                })
                ->rawColumns(['wallet_history'])
                ->make(true);
        }
            return view('admin.wallet.list');
        }

    public function wallet_history(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($request->ajax()) {
            $walletHistory = WalletHistory::where('user_id', $id)->get();
			$walletHistory->transform(function ($tx) {
				$tx->currency_obj = Currency::where('code', $tx->currency)->first();
				return $tx;
			});

            return DataTables::of($walletHistory)
                ->addIndexColumn()
                ->addColumn('amount', function ($history) {
                    return $history->currency_obj->symbol.''.number_format($history->amount, 2);
                })
                ->addColumn('type', function ($history) {
                    return ucfirst($history->type);
                })
                ->addColumn('method', function ($history) {
                    return $history->method;
                })
                ->addColumn('transaction_id', function ($history) {
                    return $history->transaction_id;
                })
                ->addColumn('old_balance', function ($history) {
					return $history->currency_obj->symbol.''.number_format($history->old_balance, 2);
                })
				->addColumn('new_balance', function ($history) {
					return $history->currency_obj->symbol.''.number_format($history->new_balance, 2);
                })
                 ->addColumn('status', function ($history) {
                    return $history->status;
                })
                ->addColumn('created_at', function ($history) {
                    return $history->created_at->format('d-m-Y H:i');
                })
                ->make(true);
        }

        return view('admin.wallet.history', compact('user'));
    }
	
	public function activityLlogHistory(Request $request)
    {
        if ($request->ajax()) {
            $logHistory = ActivityLog::with('user')->orderBy('id','DESC')->get();
		
            return DataTables::of($logHistory)
                ->addIndexColumn()
                ->addColumn('user', function ($logHistory) {
                    return $logHistory->user->name;
                })
                ->addColumn('logged_at', function ($logHistory) {
                    return date('Y-m-d H:i',strtotime($logHistory->logged_at));
                })
                ->make(true);
        }

        return view('admin.log.activity_history');
    }

}