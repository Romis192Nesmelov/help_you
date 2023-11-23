<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Order;
/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('chat_{id}', function ($user, $id) {
    $order = Order::find($id);
    return $user->id == $order->user_id || in_array($user->id, $order->performers->pluck('id')->toArray());
});
