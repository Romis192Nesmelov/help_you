@extends('layouts.mail')

@section('content')
    @include('blocks.email_head_block')
    <h3>{{ trans('mail.new_award',['name' => $action->name]) }}</h3>
    <p><a href="{{ route('incentives',['id' => $action->id]) }}" target="_blank">{{ route('incentives',['id' => $action->id]) }}</a></p>
@endsection
