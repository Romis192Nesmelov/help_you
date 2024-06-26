@extends('layouts.main')

@section('content')
<orders-component
    user_id="{{ auth()->id() }}"
    orders_url="{{ route('order.orders') }}"
    get_orders_url="{{ route('order.get_orders') }}"
    order_response_url="{{ route('order.order_response') }}"
    read_order_url="{{ route('order.read_order') }}"
    get_preview_url="{{ route('order.get_preview') }}"
    subscription_url="{{ route('account.subscription') }}"
    chat_url="{{ route('messages.chat') }}"
    get_preview_flag="{{ request()->has('preview') && request()->preview ? 1 : 0 }}"
    order_id="{{ request()->has('id') ? request()->id : '' }}"
    order_type="{{ request()->has('order_type') ? request()->order_type : 0 }}"
    performers_from="{{ request()->has('performers_from') ? request()->performers_from : 1 }}"
    performers_to="{{ request()->has('performers_to') ? request()->performers_to : 20 }}"
    search="{{ request()->has('search') ? request()->search : '' }}"
    order_types="{{ json_encode($order_types) }}"
></orders-component>
@endsection
