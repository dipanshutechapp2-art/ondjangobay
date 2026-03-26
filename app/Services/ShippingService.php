<?php

namespace App\Services;

use XMLReader;
use App\Models\ShippingPrice;
use App\Models\Country;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ShippingService {

    public function getOptions(string $countryCode)
    {
        $country = Country::where('phonecode',$countryCode)->first();

        // 1️⃣ Country prices
        $prices = ShippingPrice::with('option')
            ->where('country_id', $country?->id)
            ->where('is_active',1)
			->OrderBy('sort_order','asc')
            ->get();

        if ($prices->count() === 3) {
            return $prices;
        }

        // 2️⃣ Global fallback
        return ShippingPrice::with('option')
            ->whereNull('country_id')
            ->where('is_active',1)
            ->OrderBy('sort_order','asc')
            ->get();
    }
}

