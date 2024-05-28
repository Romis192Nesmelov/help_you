@extends('layouts.admin')

@section('content')
    <div class="panel panel-flat">
        @include('admin.blocks.title_block')
        <div class="panel-body">
            <partner-component
                incoming_obj="{{ isset($partner) ? json_encode($partner) : '' }}"
                edit_url="{{ route('admin.edit_partner') }}"
                back_url="{{ route('admin.partners') }}"
                placeholder_image="{{ asset('images/placeholder.jpg') }}"
                broadcast_on="admin_partner_event"
                broadcast_as="admin_partner"
            ></partner-component>
        </div>
    </div>
    @if (isset($partner))
        <div class="panel panel-flat">
            <x-atitle>Акции партнера</x-atitle>
            <div class="panel-body">
                <div class="panel-body">
                    @if ($partner->actions->count())
                        <actions-component
                            get_actions_url="{{ route('admin.get_actions',['parent_id' => $partner->id]) }}"
                            edit_action_url="{{ route('admin.actions',['parent_id' => $partner->id]) }}"
                            delete_action_url="{{ route('admin.delete_action') }}"
                            arrange="{{ json_encode(['field' => 'id', 'direction' => 'desc']) }}"
                        >
                        </actions-component>
                    @endif
                </div>
                <div class="panel-body">
                    <a href="{{ route('admin.actions',['slug' => 'add','parent_id' => $partner->id]) }}">
                        @include('admin.blocks.button_block', [
                            'primary' => true,
                            'buttonType' => 'button',
                            'icon' => 'icon-database-add',
                            'buttonText' => 'Добавить акцию',
                            'addClass' => 'pull-right'
                        ])
                    </a>
                </div>
            </div>
        </div>
    @endif
@endsection
