@extends('layouts.mail')

@section('content')
    @include('blocks.email_head_block')
    <h3>{{ trans('mail.ticket_closed',['subject' => $ticket->subject]) }}</h3>
    <p><a href="{{ route('tickets.my_tickets') }}" target="_blank">{{ trans('mail.to_tickets') }}</a></p>
@endsection
