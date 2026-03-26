<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PayoneerService
{
    private $url, $username, $password, $partner, $program;

    public function __construct()
    {
        $this->url      = env('PAYONEER_BASE_URL');
        $this->username = env('PAYONEER_USERNAME');
        $this->password = env('PAYONEER_PASSWORD');
        $this->partner  = env('PAYONEER_PARTNER_ID');
        $this->program  = env('PAYONEER_PROGRAM_ID');
    }

    // OAuth Token
    public function token()
    {
        $response = Http::asForm()->post($this->url.'oauth2/token', [
            'grant_type' => 'password',
            'username'   => $this->username,
            'password'   => $this->password,
        ]);

        return $response->json()['access_token'];
    }

    // Create payment (customer pay)
    public function createPayment($order)
    {
        $token = $this->token();

        return Http::withToken($token)
            ->post($this->url."programs/{$this->program}/payments", [
                "amount"   => $order->grand_total,
                "currency" => "USD",
                "payeeId"  => $order->vendor_id,
                "description" => "Order #{$order->id}"
            ])
            ->json();
    }

    // Payment Status
    public function status($paymentId)
    {
        $token = $this->token();

        return Http::withToken($token)
            ->get($this->url."payments/$paymentId")
            ->json();
    }

    // Payout to vendor
    public function payout($vendor, $amount)
    {
        $token = $this->token();

        return Http::withToken($token)
            ->post($this->url.'payouts', [
                "payeeId"     => $vendor->payoneer_id,
                "amount"      => $amount,
                "currency"    => "USD",
                "description" => "Vendor Payout"
            ])
            ->json();
    }
	
	//Vendor Payout Button (Admin Panel)
	/* public function payout_vendor($vendor_id)
	{
		$vendor = Vendor::find($vendor_id);

		$payoneer = new PayoneerService();
		$response = $payoneer->payout($vendor, $vendor->balance);

		return back()->with("success", "Payout Sent!");
	} */
}
