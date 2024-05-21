<?php

namespace App\Actions;

use App\Models\Message;
use App\Models\ReadOrder;
use App\Models\ReadPerformer;
use App\Models\ReadRemovedPerformer;

class RemoveOrderUnreadMessages
{
    public function handle(int $orderId): void
    {
        foreach ([new Message(), new ReadOrder(), new ReadPerformer(), new ReadRemovedPerformer()] as $model) {
            $model->where('order_id',$orderId)->delete();
        }
    }
}
