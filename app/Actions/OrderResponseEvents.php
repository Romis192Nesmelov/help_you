<?php

namespace App\Actions;

use App\Events\Admin\AdminOrderEvent;
use App\Events\NotificationEvent;
use App\Models\Order;

class OrderResponseEvents
{
    public function handle(Order $order): void
    {
        broadcast(new NotificationEvent('new_performer', $order, $order->user_id));
    }
}
