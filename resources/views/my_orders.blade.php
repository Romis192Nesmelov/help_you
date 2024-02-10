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
    <my-orders-list-component
        user_id="{{ auth()->id() }}"
        orders_urls="{{ json_encode(['active' => route('account.my_orders_active'), 'open' => route('account.my_orders_open'),'approving' => route('account.my_orders_approving'),'archive' => route('account.my_orders_archive')]) }}"
        read_unread_by_my_orders="{{ route('account.set_read_unread_by_my_orders') }}"
        close_order_url="{{ route('order.close_order') }}"
        resume_order_url="{{ route('order.resume_order') }}"
        delete_order_url="{{ route('order.delete_order') }}"
        remove_performer_url="{{ route('order.remove_order_performer') }}"
        set_rating_url="{{ route('order.set_rating') }}"
        edit_order_url="{{ route('order.edit_order') }}"
        resume_image="{{ asset('images/resumed.png') }}"
        closed_image="{{ asset('images/closed.png') }}"
    ></my-orders-list-component>
</div>
@endsection
