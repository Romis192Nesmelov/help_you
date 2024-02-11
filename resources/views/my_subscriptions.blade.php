@extends('layouts.main')

@section('content')
<div class="row">
    <left-menu-component
        user="{{ json_encode(auth()->user()) }}"
        allow_change_avatar="0"
        left_menu="{{ json_encode($leftMenu) }}"
        logout_url="{{ route('auth.logout') }}"
        active_left_menu="{{ $active_left_menu }}"
    ></left-menu-component>
    <my-subscriptions-component
        user_id="{{ auth()->id() }}"
        unsubscribe_url="{{ route('account.delete_subscription') }}"
        order_response_url="{{ route('order.order_response') }}"
        orders_urls="{{ json_encode(['open' => route('account.my_unread_subscriptions')]) }}"
        orders_map_url="{{ route('order.orders') }}"
        read_order_url="{{ route('order.read_order') }}"
        chat_url="{{ route('messages.chat') }}"
    ></my-subscriptions-component>
</div>
@endsection
