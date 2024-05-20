@extends('layouts.admin')

@section('content')
    <div class="panel panel-flat">
        @include('admin.blocks.title_block')
        <div class="panel-body">
            <orders-component
                get_orders_url="{{ route('admin.get_orders') }}"
                edit_order_url="{{ route('admin.orders') }}"
                delete_order_url="{{ route('admin.delete_order') }}"
                arrange="{{ json_encode(['field' => 'id', 'direction' => 'desc']) }}"
            ></orders-component>
        </div>
        @include('admin.blocks.add_button_block')
    </div>
@endsection
