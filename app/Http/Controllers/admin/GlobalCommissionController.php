<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use Illuminate\Http\Request;

class GlobalCommissionController extends Controller
{
    public function showForm()
	{
		$commission = Commission::where('level', 'global')->first();
		return view('admin.commissions.global', compact('commission'));
	}

	public function save(Request $request)
	{
		$request->validate([
			'commission_value' => 'required|numeric|min:0|max:100'
		]);

		\App\Models\Commission::updateOrCreate(
			['level' => 'global'],
			['commission_value' => $request->commission_value]
		);

		return redirect()->route('admin.global.commission')->with('success', 'Global commission updated.');
	}
}
