<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentGateway;

class PaymentGatewayController extends Controller
{
    public function index()
    {
        $gateways = PaymentGateway::all()->keyBy('name');
        return view('admin.payment_gateways.index', compact('gateways'));
    }
	
	public function update(Request $request, $id)
	{
		$gateway = PaymentGateway::findOrFail($id);

		$request->validate([
			'mode'             => 'in:test,live',
			'test_credentials' => 'array',
			'live_credentials' => 'array',
			'status'           => 'boolean',
			'is_default'       => 'boolean',
		]);
		
		if ($request->has('is_default') && $request->boolean('is_default')) {
			PaymentGateway::where('is_default', true)->update(['is_default' => false]);
			$gateway->is_default = true;
		} else {
			$gateway->is_default = false;
		}

		if ($request->has('test_credentials')) {
			$gateway->test_credentials = $request->test_credentials;
		}
		if ($request->has('live_credentials')) {
			$gateway->live_credentials = $request->live_credentials;
		}
		
		$gateway->mode = $request->mode;
		$gateway->status = $request->status ?? $gateway->status;
		
		#LOGO UPLOADS
		if ($request->hasFile('logo')) {
			$file = $request->file('logo');
			$filename = time() . '_' . $file->getClientOriginalName();
			$file->move(public_path('uploads/payment_gateways'), $filename);

			if ($gateway->logo && file_exists(public_path('uploads/payment_gateways/' . $gateway->logo))) {
				unlink(public_path('uploads/payment_gateways/' . $gateway->logo));
			}

			$gateway->logo = $filename; 
		}

		
		$gateway->save();

		return redirect()->route('admin.payment_gateways.index')->with('success', $gateway->name . ' updated successfully.');
	}

}
