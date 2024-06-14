@extends('layouts.main')

@section('content')
<div class="row">
    @include('blocks.left_menu_block')
    <chat-component
        user_id="{{ auth()->id() }}"
        new_message_url="{{ route('messages.new_message') }}"
        read_message_url="{{ route('messages.read_message') }}"
        chats_url="{{ route('messages.chats') }}"
        order="{{ json_encode($order) }}"
        companion="{{ $order->user_id == auth()->id() ? json_encode($order->performers[0]) : json_encode($order->user) }}"
    ></chat-component>
@endsection
