@extends('layouts.mail')

@section('content')
    @include('blocks.email_head_block')
    <h3>{{ trans('mail.new_message',['name' => $order->name]) }}</h3>
    @include('blocks.to_go_to_the_chat_block',['order_id' => $order->id])
@endsection
