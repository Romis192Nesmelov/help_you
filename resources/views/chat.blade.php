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
    <chat-component
        user_id="{{ auth()->id() }}"
        new_message_url="{{ route('messages.new_message') }}"
        read_message_url="{{ route('messages.read_message') }}"
        chats_url="{{ route('messages.chats') }}"
        order="{{ json_encode($order) }}"
        companion="{{ $order->user_id == auth()->id() ? json_encode($order->performers[0]) : json_encode($order->user) }}"
    ></chat-component>
@endsection
