@extends('layouts.admin')

@section('content')
    <div class="panel panel-flat">
        @include('admin.blocks.title_block')
        <div class="panel-body">
            <users-component
                get_users_url="{{ route('admin.get_users') }}"
                edit_user_url="{{ route('admin.users') }}"
                delete_user_url="{{ route('admin.delete_user') }}"
                arrange="{{ json_encode(['field' => 'id', 'direction' => 'desc']) }}"
            ></users-component>
        </div>
        @include('admin.blocks.add_button_block')
    </div>
@endsection
