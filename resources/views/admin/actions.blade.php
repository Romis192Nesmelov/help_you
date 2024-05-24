@extends('layouts.admin')

@section('content')
    <div class="panel panel-flat">
        @include('admin.blocks.title_block')
        <div class="panel-body">
            <actions-component
                get_actions_url="{{ route('admin.get_actions') }}"
                edit_action_url="{{ route('admin.actions') }}"
                delete_action_url="{{ route('admin.delete_action') }}"
                arrange="{{ json_encode(['field' => 'id', 'direction' => 'desc']) }}"
            >
            </actions-component>
        </div>
        @include('admin.blocks.add_button_block')
    </div>
@endsection
