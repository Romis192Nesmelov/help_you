@extends('layouts.main')

@section('content')

@include('blocks.modal_delete_block',[
    'action' => 'order.delete_order',
    'head' => trans('content.do_you_really_want_to_delete_this_order')
])

<x-modal
    id="order-closing-confirm-modal"
    head="{{ trans('content.warning') }}"
    footer="1"
    yes_button="1"
    yes_button_class="close-yes"
>
    @csrf
    <h3 class="text-center">{{ trans('content.do_you_really_want_to_close_this_order') }}</h3>
</x-modal>

<x-modal id="order-closed-modal" head="{{ trans('content.order_is_closed') }}">
    <img class="w-100" src="{{ asset('images/closed.png') }}" />
</x-modal>

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
                'useButton' => true,
                'editRoute' => 'order.edit_order'
            ])
        </div>
    </div>
</div>
<script>
    const closeOrderUrl = "{{ route('order.close_order') }}",
        archiveLabelText = "{{ trans('content.status_0') }}";
</script>
<script type="text/javascript" src="{{ asset('js/orders_list.js') }}"></script>
@endsection
