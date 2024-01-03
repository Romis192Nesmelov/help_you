@extends('layouts.mail')

@section('content')
    <h2>{{ trans('mail.message_from_the_site').' '.env('APP_NAME') }}</h2>
    <h3>{{ trans('mail.new_message',['order_id' => $order->id]) }}</h3>
    <p>{{ trans('mail.to_go_to_the_chat') }}</p>
    <p><a href="{{ route('messages.chat',['order_id' => $order->id]) }}">{{ route('messages.chat',['order_id' => $order->id]) }}</a></p>
@endsection
