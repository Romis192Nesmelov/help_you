@extends('layouts.admin')

@section('content')
    <div class="panel panel-flat">
        @include('admin.blocks.title_block')
        <div class="panel-body">
            <tickets-component
                get_tickets_url="{{ route('admin.get_tickets') }}"
                edit_ticket_url="{{ route('admin.tickets') }}"
                delete_ticket_url="{{ route('admin.delete_ticket') }}"
                arrange="{{ json_encode(['field' => 'id', 'direction' => 'desc']) }}"
            ></tickets-component>
        </div>
        @include('admin.blocks.add_button_block')
    </div>
@endsection
