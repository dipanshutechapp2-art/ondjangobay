<?php
namespace App\Services\DHL;

use Illuminate\Support\Facades\Http;

class DHLBaseService
{
    protected function baseUrl()
    {
        return config('dhl.env') === 'sandbox'
            ? config('dhl.sandbox_base_url')
            : config('dhl.prod_base_url');
    }

    protected function client()
	{
		return Http::withBasicAuth(
			config('dhl.username'),
			config('dhl.password')
		)->withHeaders([
			'Content-Type' => 'application/json',
		]);
	}

}

