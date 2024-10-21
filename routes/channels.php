<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\Answer;
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
    $order = Order::query()->where('id',$id)->select(['id','status','user_id'])->with('performers')->first();
    return $order->status && ($user->id == 1 || $user->id == $order->user_id || in_array($user->id, $order->performers->pluck('id')->toArray()));
});

Broadcast::channel('ticket_{id}', function ($user, $id) {
    return (int)$user->id === (int) $id;
});

Broadcast::channel('answer_{id}', function ($user, $id) {
    return (int)$user->id === (int) $id;
});

Broadcast::channel('notice_{id}', function ($user, $id) {
    return (int)$user->id === (int) $id;
});

Broadcast::channel('incentive_{id}', function ($user, $id) {
    return (int)$user->id === (int) $id;
});

Broadcast::channel('admin_incentive{id}', function ($user) {
    return (int)$user->admin;
});

Broadcast::channel('admin_action{id}', function ($user) {
    return (int)$user->admin;
});

Broadcast::channel('admin_order{id}', function ($user) {
    return (int)$user->admin;
});

Broadcast::channel('admin_partner{id}', function ($user) {
    return (int)$user->admin;
});

Broadcast::channel('admin_user{id}', function ($user) {
    return (int)$user->admin;
});

Broadcast::channel('admin_ticket{id}', function ($user) {
    return (int)$user->admin;
});

Broadcast::channel('admin_answer{id}', function ($user) {
    return (int)$user->admin;
});
