<?php

namespace App\Services;
use App\Models\AutoDSToken;
use App\Models\Order;
use App\Models\AutoDSOrder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Str;

class OrderService
{
    public function expirePendingOrders()
    {
        return 1;
    }
	
}
