@extends('layouts.mail')

@section('content')
    @include('blocks.email_head_block')
    <h3>{{ trans('mail.new_order_status',['order_id' => $order->id, 'status' => trans('content.status_'.$order->status)]) }}</h3>
@endsection
