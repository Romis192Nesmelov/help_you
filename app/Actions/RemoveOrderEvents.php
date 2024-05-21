<?php

namespace App\Actions;

use App\Events\Admin\AdminOrderEvent;
use App\Events\OrderEvent;
use App\Models\Order;

class RemoveOrderEvents
{
    public function handle(Order $order): void
    {
        broadcast(new OrderEvent('remove_order', $order));
        broadcast(new AdminOrderEvent('change_item', $order));
    }
}
