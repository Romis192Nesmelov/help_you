<?php

namespace App\Actions;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SetReadUnread
{
    public function handle(Model $model): void
    {
        $model->query()
            ->whereIn('order_id',Order::query()->where('user_id',Auth::id())->pluck('id')->toArray())
            ->where('read',null)
            ->update(['read' => true]);
    }
}
