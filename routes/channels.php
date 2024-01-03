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
    return $order->status && ($user->id == 1 || $user->id == $order->user_id || in_array($user->id, $order->performers->pluck('id')->toArray()));
});

Broadcast::channel('notice_{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
