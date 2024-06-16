@extends('layouts.main')

@section('content')
<div class="row">
    @include('blocks.left_menu_block')
    <ticket-component
        user_id="{{ auth()->id() }}"
        incoming_ticket="{{ json_encode($ticket) }}"
        new_answer_url="{{ route('tickets.new_answer') }}"
        back_url="{{ route('tickets.my_tickets') }}"
    ></ticket-component>
</div>
@endsection
