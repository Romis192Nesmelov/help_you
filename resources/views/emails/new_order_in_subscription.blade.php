@extends('layouts.mail')

@section('content')
    @include('blocks.email_head_block')
    <h3>{{ trans('mail.new_order_in_subscription',['user_name' => view('blocks.user_name_block',['user' => $order->user])->render()]) }}</h3>
    @include('blocks.to_go_to_the_chat_block',['id' => $order->id])
@endsection
