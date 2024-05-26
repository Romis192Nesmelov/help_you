@extends('layouts.admin')

@section('content')
    <div class="panel panel-flat">
        @include('admin.blocks.title_block')
        <div class="panel-body">
            <user-component
                incoming_obj="{{ isset($user) ? json_encode($user) : '' }}"
                edit_url="{{ route('admin.edit_user') }}"
                back_url="{{ route('admin.users') }}"
                change_avatar_url="{{ route('admin.change_avatar') }}"
                def_avatar="{{ asset('images/def_avatar.svg') }}"
                input_image_hover="{{ asset('images/input_image_hover.svg') }}"
                broadcast_on="admin_user_event"
                broadcast_as="admin_user"
            >
            </user-component>
        </div>
    </div>
@endsection
