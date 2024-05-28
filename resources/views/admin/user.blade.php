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
    @if (isset($user))
        <div class="panel panel-flat">
            <x-atitle>Заявки пользователя</x-atitle>
            <div class="panel-body">
                @if ($user->orders->count())
                    <orders-component
                        get_orders_url="{{ route('admin.get_orders',['parent_id' => $user->id]) }}"
                        edit_order_url="{{ route('admin.orders',['parent_id' => $user->id]) }}"
                        delete_order_url="{{ route('admin.delete_order') }}"
                        arrange="{{ json_encode(['field' => 'id', 'direction' => 'desc']) }}"
                    ></orders-component>
                @endif
            </div>
            <div class="panel-body">
                <a href="{{ route('admin.orders',['slug' => 'add','parent_id' => $user->id]) }}">
                    @include('admin.blocks.button_block', [
                        'primary' => true,
                        'buttonType' => 'button',
                        'icon' => 'icon-database-add',
                        'buttonText' => 'Добавить заявку',
                        'addClass' => 'pull-right'
                    ])
                </a>
            </div>
        </div>
        <div class="panel panel-flat">
            <x-atitle>Обращения в тех.поддержку</x-atitle>
            <div class="panel-body">
                <tickets-component
                    get_tickets_url="{{ route('admin.get_tickets',['parent_id' => $user->id]) }}"
                    edit_ticket_url="{{ route('admin.tickets',['parent_id' => $user->id]) }}"
                    delete_ticket_url="{{ route('admin.delete_ticket') }}"
                    arrange="{{ json_encode(['field' => 'id', 'direction' => 'desc']) }}"
                ></tickets-component>
            </div>
            <div class="panel-body">
                <a href="{{ route('admin.tickets',['slug' => 'add','parent_id' => $user->id]) }}">
                    @include('admin.blocks.button_block', [
                        'primary' => true,
                        'buttonType' => 'button',
                        'icon' => 'icon-database-add',
                        'buttonText' => 'Добавить обращение',
                        'addClass' => 'pull-right'
                    ])
                </a>
            </div>
        </div>
    @endif
@endsection
