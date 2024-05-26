@extends('layouts.admin')

@section('content')
    <div class="panel panel-flat">
        @include('admin.blocks.title_block')
        <div class="panel-body">
            <partner-component
                incoming_obj="{{ isset($partner) ? json_encode($partner) : '' }}"
                edit_url="{{ route('admin.edit_partner') }}"
                back_url="{{ route('admin.partners') }}"
                placeholder_image="{{ asset('images/placeholder.gif') }}"
                broadcast_on="admin_partner_event"
                broadcast_as="admin_partner"
            ></partner-component>
        </div>
    </div>
@endsection
