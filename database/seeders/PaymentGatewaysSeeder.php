<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentGateway;

class PaymentGatewaysSeeder extends Seeder
{
    public function run()
    {
        $names = [
            'Stripe','PayPal','Wallet','COD'
        ];

        foreach ($names as $name) {
            PaymentGateway::updateOrCreate(
                ['name' => $name],
                ['mode' => 'test', 'credentials' => [], 'status' => false]
            );
        }
    }
}
