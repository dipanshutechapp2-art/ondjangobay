<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Services\OrderService;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

#Expire Pending Orders (CRON)
Artisan::command('orders:expire', function (OrderService $orderService) {

    $count = $orderService->expirePendingOrders();

    Log::info('orders:expire cron executed', [
        'expired_orders' => $count,
        'time' => now()->toDateTimeString(),
    ]);

    $this->info("Expired pending orders count: {$count}");
})
->purpose('Expire pending orders older than 30 minutes');

