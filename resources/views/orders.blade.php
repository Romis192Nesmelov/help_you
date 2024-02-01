@extends('layouts.main')

@section('content')
<orders-component
    user_id="{{ auth()->id() }}"
    get_orders_url="{{ route('order.get_orders') }}"
    order_response_url="{{ route('order.order_response') }}"
    read_order_url="{{ route('order.read_order') }}"
    get_preview_url="{{ route('order.get_preview') }}"
    subscription_url="{{ route('account.subscription') }}"
    chat_url="{{ route('messages.chat') }}"
    get_preview_flag="{{ request()->has('preview') && request()->preview ? 1 : 0 }}"
    order_id="{{ request()->has('id') ? request()->id : '' }}"
    order_types="{{ json_encode($order_types) }}"
></orders-component>
@endsection
