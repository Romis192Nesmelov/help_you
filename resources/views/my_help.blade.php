@extends('layouts.main')

@section('content')

@include('blocks.modal_delete_block',[
    'action' => 'order.delete_response',
    'head' => trans('content.do_you_really_want_to_withdraw_your_response')
])

<div class="row">
    @include('blocks.left_menu_block',['hasChangeAvatar' => false])
    <div class="col-12 col-lg-8">
        <div class="rounded-block tall">
            <h2>{{ trans('account.my_help') }}</h2>
            @include('blocks.top_sub_menu_block',[
                'menus' => ['active','archive'],
                'prefix' => 'auth',
                'postfix' => '_orders',
                'items' => $orders
            ])
            @include('blocks.tab_orders_block', [
                'menus' => ['active','archive'],
                'useButton' => false,
                'editRoute' => null
            ])
        </div>
    </div>
</div>
@endsection
