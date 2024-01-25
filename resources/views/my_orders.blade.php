@extends('layouts.main')

@section('content')

@include('blocks.modal_delete_block',[
    'action' => 'order.delete_order',
    'head' => trans('content.do_you_really_want_to_delete_this_order')
])

@include('blocks.modal_delete_block',[
    'id' => 'remove-performer-modal',
    'action' => 'order.remove_order_performer',
    'head' => trans('content.do_you_really_want_to_remove_this_performer')
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

<x-modal
    id="order-resume-confirm-modal"
    head="{{ trans('content.warning') }}"
    footer="1"
    yes_button="1"
    yes_button_class="resume-yes"
>
    @csrf
    <h3 class="text-center">{{ trans('content.do_you_really_want_to_resume_this_order') }}</h3>
</x-modal>

<x-modal id="order-closed-modal" head="{{ trans('content.order_is_closed') }}">
    <form method="post" action="{{ route('order.set_rating') }}" class="rating-form d-flex flex-column align-items-center">
        <input type="hidden" name="order_id" value="">
        <input type="hidden" name="rating" value="0">
        @csrf
        <img class="w-50" src="{{ asset('images/closed.png') }}" />
        <h2 class="text-center mt-3">{{ trans('content.set_rate') }}</h2>
        @include('blocks.rating_line_block')
        @include('blocks.button_block',[
            'buttonType' => 'submit',
            'primary' => true,
            'buttonText' => trans('content.send')
        ])
    </form>
</x-modal>

<x-modal id="order-resumed-modal" head="{{ trans('content.order_is_resumed') }}">
    <img class="w-100" src="{{ asset('images/resumed.png') }}" />
</x-modal>

<x-modal id="order-performers-modal" head="{{ trans('content.performers') }}">
    <h4 class="text-center p-4">{{ trans('content.no_performers') }}</h4>
    <table class="table table-striped">
        <tr>
            <td>@include('blocks.user_creds_block',['rating' => 1])</td>
            <td class="order-cell-delete icon align-middle">
                <i title="{{ trans('content.remove_this_performer') }}" class="icon-user-block fs-5"></i>
            </td>
        </tr>
    </table>
</x-modal>

<div class="row">
    <left-menu-component
        user="{{ json_encode(auth()->user()) }}"
        allow_change_avatar="0"
        left_menu="{{ json_encode($leftMenu) }}"
        logout_url="{{ route('auth.logout') }}"
        active_left_menu="{{ $active_left_menu }}"
    ></left-menu-component>
    <order-list-component
        order_list="{{ json_encode($orders) }}"
        edit_order_url="{{ route('order.edit_order') }}"
    ></order-list-component>

{{--    <div id="my-orders" class="col-12 col-lg-8 right-block">--}}
{{--        <div class="rounded-block tall">--}}
{{--            <h2>Мои запросы</h2>--}}
{{--            <tabs-component--}}
{{--                tabs="{{ json_encode([['key' => 'active', 'name' => 'Активные'],['key' => 'approving', 'name' => 'На модерации'],['key' => 'archive', 'name' => 'Архив']]) }}"--}}
{{--                counters="{{ json_encode(['active' => count($orders['active']),'approving' => count($orders['approving']),'archive' => count($orders['archive'])]) }}"--}}
{{--            ></tabs-component>--}}
{{--            @include('blocks.top_sub_menu_block',[--}}
{{--                'menus' => ['active','approving','archive'],--}}
{{--                'prefix' => 'auth',--}}
{{--                'postfix' => '_orders',--}}
{{--                'items' => $orders--}}
{{--            ])--}}
{{--            @include('blocks.tab_orders_block', [--}}
{{--                'menus' => ['active','approving','archive'],--}}
{{--                'useButton' => true,--}}
{{--                'editRoute' => 'order.edit_order'--}}
{{--            ])--}}
{{--        </div>--}}
{{--    </div>--}}
</div>
{{--<script>--}}
{{--    const closeOrderUrl = "{{ route('order.close_order') }}",--}}
{{--        resumeOrderUrl = "{{ route('order.resume_order') }}",--}}
{{--        editOrderUrl = "{{ route('order.edit_order') }}",--}}
{{--        getOrderPerformersUrl = "{{ route('order.get_order_performers') }}",--}}
{{--        removeOrderPerformerUrl = "{{ route('order.remove_order_performer') }}",--}}
{{--        closeOrderText = "{{ trans('content.close') }}",--}}
{{--        resumeOrderText = "{{ trans('content.resume') }}",--}}
{{--        editOrderText = "{{ trans('content.edit') }}",--}}
{{--        deleteOrderText = "{{ trans('content.delete') }}",--}}
{{--        participantsText = "{{ trans('messages.participants') }}";--}}
{{--</script>--}}
@endsection
