<?php

namespace App\Actions;

use App\Models\Order;
use App\Models\OrderUser;
use App\Models\ReadOrder;
use App\Models\ReadPerformer;
use App\Models\ReadStatusOrder;

class OrderResponse
{
    public function handle(Order $order): void
    {
        OrderUser::query()->create([
            'order_id' => $order->id,
            'user_id' => request('performer_id')
        ]);
        ReadPerformer::query()->create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
        ]);
        ReadStatusOrder::query()->create([
            'order_id' => $order->id,
            'status' => 1,
        ]);
        ReadOrder::query()->where('order_id',$order->id)->delete();
    }
}
