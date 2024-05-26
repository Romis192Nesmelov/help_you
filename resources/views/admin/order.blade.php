@extends('layouts.admin')

@section('content')
    <div class="panel panel-flat">
        @include('admin.blocks.title_block')
        <div class="panel-body">
            <order-component
                incoming_obj="{{ isset($order) ? json_encode($order) : '' }}"
                edit_url="{{ route('admin.edit_order') }}"
                delete_image="{{ route('admin.delete_order_image') }}"
                back_url="{{ route('admin.orders') }}"
                input_image_hover="{{ asset('images/input_image_hover.svg') }}"
                broadcast_on="admin_order_event"
                broadcast_as="admin_order"
                incoming_users="{{ json_encode($users) }}"
                incoming_types="{{ json_encode($types) }}"
                yandex_api_key="{{ env('YANDEX_API_KEY') }}"
            ></order-component>
        </div>
    </div>
@endsection
