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

<div class="row">
    @include('blocks.left_menu_block',['hasChangeAvatar' => false])
    <div class="col-12 col-lg-8">
        <div class="rounded-block tall">
            <h2>{{ trans('account.my_orders') }}</h2>
            @include('blocks.top_sub_menu_block',[
                'menus' => ['active','approving','archive'],
                'prefix' => 'auth',
                'postfix' => '_orders',
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
        resumeOrderUrl = "{{ route('order.resume_order') }}",
        editOrderUrl = "{{ route('order.edit_order') }}",
        inApproveLabelText = "{{ trans('content.in_approve') }}",
        archiveLabelText = "{{ trans('content.status_0') }}",
        closeOrderText = "{{ trans('content.close') }}",
        resumeOrderText = "{{ trans('content.resume') }}",
        editOrderText = "{{ trans('content.edit') }}",
        deleteOrderText = "{{ trans('content.delete') }}";
</script>
@endsection
