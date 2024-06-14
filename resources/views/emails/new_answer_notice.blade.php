@extends('layouts.mail')

@section('content')
    @include('blocks.email_head_block')
    <h3>{{ trans('mail.new_answer',['subject' => $answer->ticket->subject]) }}</h3>
    <p><a href="{{ route('tickets.my_tickets',['id' => $answer->ticket->id]) }}" target="_blank">{{ trans('mail.read_answer') }}</a></p>
@endsection
