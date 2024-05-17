@extends('layouts.main')

@section('content')

<div class="row">
    <left-menu-component
        user="{{ json_encode(auth()->user()) }}"
        left_menu="{{ json_encode($leftMenu) }}"
        logout_url="{{ route('auth.logout') }}"
        active_left_menu="{{ $active_left_menu }}"
    ></left-menu-component>
    <my-chats-component
        user_id="{{ auth()->id() }}"
        orders_urls="{{ json_encode(['my_orders' => route('messages.chats_my_orders'), 'performer' => route('messages.chats_performer')]) }}"
        chat_url="{{ route('messages.chat') }}"
    ></my-chats-component>
</div>
@endsection
