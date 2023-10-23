@extends('layouts.main')

@section('content')

@include('blocks.modal_delete_block',[
    'action' => 'order.delete_order',
    'head' => trans('content.do_you_really_want_to_delete_this_order')
])

<div class="row">
    @include('blocks.left_menu_block')
    <div class="col-12 col-lg-8">
        <div class="rounded-block tall">
            <h2>{{ trans('auth.my_orders') }}</h2>
            @include('blocks.top_sub_menu_block',[
                'menus' => ['active','approving','archive'],
                'suffix' => '_orders',
                'items' => $orders
            ])
            @include('blocks.tab_orders_block', [
                'menus' => ['active','approving','archive'],
                'useButton' => true
            ])
        </div>
    </div>
</div>
@endsection
