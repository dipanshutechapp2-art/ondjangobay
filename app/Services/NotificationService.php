<?php

namespace App\Services;

use App\Models\PartnerProduct;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    public static function notifyVendorApproval(PartnerProduct $product)
    {
        $vendor = $product->vendor;
		
        Notification::route('mail', $vendor->email)
            ->notify(new \App\Notifications\GenericNotification('Your product has been approved!'));
    }

    public static function notifyRefund(User $user)
    {
        Notification::route('mail', $user->email)
            ->notify(new \App\Notifications\GenericNotification('Your campaign purchase was refunded.'));
    }

    public static function notifyShipment(User $user)
    {
        Notification::route('mail', $user->email)
            ->notify(new \App\Notifications\GenericNotification('Your campaign product is on the way!'));
    }
}
