@extends('layouts.mail')

@section('content')
    @include('blocks.email_head_block')
    <h3>{{ trans('mail.removed_performer',['user_name' => $order->user->name.' '.$order->user->family, 'name' => $order->name]) }}</h3>
@endsection
