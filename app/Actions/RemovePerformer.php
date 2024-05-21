<?php

namespace App\Actions;

use App\Models\Order;
use App\Models\OrderUser;
use App\Models\ReadRemovedPerformer;

class RemovePerformer
{
    public function handle(Order $order, int $performerId): void
    {
        OrderUser::query()->where('order_id',$order->id)->where('user_id',$order->user_id)->delete();
        ReadRemovedPerformer::create([
            'order_id' => $order->id,
            'user_id' => $performerId,
        ]);
    }
}
