@extends('layouts.mail')

@section('content')
    @include('blocks.email_head_block')
    <h3>{{ trans('mail.sent_from_feedback_form') }}</h3>
    <p><b>{{ trans('mail.from_user_with_name') }}</b> {{ $name }}</p>
    <p><b>E-mail</b> {{ $email }}</p>
    <h4>{{ trans('mail.message') }}</h4>
    <p>{{ $text }}</p>
@endsection
