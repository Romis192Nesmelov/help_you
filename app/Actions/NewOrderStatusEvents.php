<?php

namespace App\Actions;

use App\Events\Admin\AdminOrderEvent;
use App\Events\NotificationEvent;
use App\Events\OrderEvent;
use App\Models\Order;

class NewOrderStatusEvents
{
    public function handle(Order $order): void
    {
        broadcast(new NotificationEvent('new_order_status', $order, $order->user_id));
        broadcast(new OrderEvent('new_order_status', $order));
        broadcast(new AdminOrderEvent('change_item', $order));
    }
}
