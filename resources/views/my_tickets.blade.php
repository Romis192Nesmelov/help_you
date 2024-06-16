@extends('layouts.main')

@section('content')
<div class="row">
    @include('blocks.left_menu_block')
    <my-tickets-component
        user_id="{{ auth()->id() }}"
        my_tickets_url = "{{ route('tickets.my_tickets') }}"
        get_tickets_url = "{{ route('tickets.get_tickets') }}"
        new_ticket_url = "{{ route('tickets.new_ticket') }}"
        close_ticket_url = "{{ route('tickets.close_ticket') }}"
        resume_ticket_url = "{{ route('tickets.resume_ticket') }}"
    ></my-tickets-component>
</div>
@endsection
