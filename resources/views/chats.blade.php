@extends('layouts.main')

@section('content')
<div class="row">
    @include('blocks.left_menu_block')
    <my-chats-component
        user_id="{{ auth()->id() }}"
        orders_urls="{{ json_encode(['my_orders' => route('messages.chats_my_orders'), 'performer' => route('messages.chats_performer')]) }}"
        chat_url="{{ route('messages.chat') }}"
    ></my-chats-component>
</div>
@endsection
