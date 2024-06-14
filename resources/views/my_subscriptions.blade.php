@extends('layouts.main')

@section('content')
<div class="row">
    @include('blocks.left_menu_block')
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
