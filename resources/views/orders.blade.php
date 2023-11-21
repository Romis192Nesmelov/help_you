@extends('layouts.main')

@section('content')
<x-modal id="order-respond-modal">
    <h5 class="bg-gray">{!! trans('content.thanks_to_respond') !!}</h5>
    <hr>
    <h2 class="order-type text-center mt-3"></h2>
    <p class="small text-center"><b>{{ trans('content.waiting_for_you_at_the_address') }}</b></p>
    <h6 class="order-address text-center mt-0"></h6>
    <hr>
    <p>{{ trans('content.you_can_discuss_details_in_chat') }}</p>
    <hr>
    <h1 class="text-orange text-center">{{ trans('content.thank_you') }}</h1>
</x-modal>

<x-modal id="order-full-description-modal" head=" "><p class="p-3"></p></x-modal>

<div class="rounded-block mb-2 p-3 h-auto">
    <form method="post" class="row d-flex justify-content-center ps-3 pe-3" action="{{ route('order.get_orders') }}">
        @csrf
        <div class="col-lg-2 col-sm-12 col-12 row d-flex align-items-end m-0 p-0">
            <label class="ms-3">{{ trans('content.order_types') }}</label>
            @include('blocks.select_block',[
                'firstEmpty' => trans('content.all'),
                'name' => 'order_type',
                'values' => $order_types,
                'option' => 'name'
            ])
        </div>
        <div class="col-lg-4 col-12 row d-flex align-items-end m-0 p-0">
            <label class="ms-3">{{ trans('content.number_of_performers') }}</label>
            <div class="col-sm-6 col-12">
                @include('blocks.select_block',[
                    'addClass' => 'mb-2 mb-sm-0',
                    'prefix' => trans('content.from'),
                    'name' => 'performers_from',
                    'values' => range(1, 19)
                ])
            </div>
            <div class="col-sm-6 col-12">
                @include('blocks.select_block',[
                    'prefix' => trans('content.to'),
                    'name' => 'performers_to',
                    'values' => range(1, 20),
                    'selected' => 20
                ])
            </div>
        </div>
        <div class="col-lg-2 col-sm-12 col-12 d-flex align-items-end m-0">
            @include('blocks.button_block',[
                'id' => 'apply-button',
                'addClass' => 'w-100 mt-lg-0 mt-3',
                'buttonType'=> 'submit',
                'primary' => true,
                'buttonText' => trans('content.apply')
            ])
        </div>
        <div class="col-lg-4 col-12 mt-lg-0 mt-2 row d-flex align-items-end m-0 p-0">
            <label class="ms-3">{{ trans('content.search') }}</label>
            @include('blocks.input_block',[
                'name' => 'search',
                'addClass' => 'w-100',
                'type' => 'text',
                'icon' => 'icon-search4'
            ])
        </div>
    </form>
</div>
<div id="map-container" class="rounded-block">
    <div id="map"></div>
    <div id="selected-points">
        <i class="icon-close2"></i>
        <div id="points-container">

{{--            <div class="mb-3">--}}
{{--                <h6 class="text-center">Заявка от</h6>--}}
{{--                --}}
{{--                <div class="w-100 d-flex align-items-center justify-content-between">--}}
{{--                    <div class="d-flex align-items-center justify-content-center">--}}
{{--                        <div class="avatar cir" style="background-image: url({{ asset(auth()->user()->avatar ? auth()->user()->avatar : 'images/def_avatar.svg') }} );"></div>--}}
{{--                        <div>--}}
{{--                            <div class="user-name">Несмелов Роман</div>--}}
{{--                            <div class="born">14-07-1976</div>--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    <button id="subscribe-button" type="button" class="btn btn-primary small mt-0">--}}
{{--                        <i class="icon-bell-check"></i>--}}
{{--                        <span>Подписаться</span>--}}
{{--                    </button>--}}
{{--                    --}}
{{--                </div>--}}

{{--                <div class="images owl-carousel d-none">--}}
{{--                    <div class="image"></div>--}}
{{--                </div>--}}

{{--                <h2 class="order-type text-center mt-3 mb-1">Какой-то типа</h2>--}}
{{--                <ul class="subtypes"></ul>--}}
{{--                <p class="mb-1 text-center">{{ trans('content.address') }}: <span class="order-address"></span></p>--}}
{{--                <p class="small text-center fst-italic mt-2 mb-0">{{ trans('content.description') }}:</p>--}}
{{--                <p class="text-center order-description fst-italic mb-1"></p>--}}
{{--                <p class="small text-center fst-italic mb-2">{{ trans('content.need_performers') }} 1; {{ trans('content.ready_to_help') }} 2</p>--}}
{{--                --}}
{{--                <button type="button" class="btn btn-primary w-100">--}}
{{--                    <span>Откликнуться на заявку</span>--}}
{{--                </button>--}}
{{--            </div>--}}

{{--            <hr>--}}
        </div>
    </div>
<script>
    let getPreviewFlag = parseInt("{{ request()->has('preview') && request()->input('preview') }}");
    const orderResponseUrl = "{{ route('order.order_response') }}",
        orderReadOrderUrl = "{{ route('order.read_order') }}",
        getPreviewUrl = "{{ route('order.get_preview') }}",
        subscribeUrl = "{{ route('account.subscription') }}",
        getOrdersUrl = "{{ route('order.get_orders') }}",
        getUserAgeUrl = "{{ route('order.get_user_age') }}",
        orderNumber = "{{ trans('content.order_number') }}",
        fromText = "{{ trans('content.from') }}",
        outOfText = "{{ trans('content.out_of') }}",
        address = "{{ trans('content.address') }}",
        descriptionShortText = "{{ trans('content.description_short') }}",
        descriptionFullText = "{{ trans('content.description_full') }}",
        descriptionFullOfOrderText = "{{ trans('content.description_full_of_order') }}",
        numberOfPerformersText = "{{ trans('content.number_of_performers') }}",
        respondToAnOrder = "{{ trans('content.respond_to_an_order') }}",
        copyOrderHrefToClipboard = "{{ trans('content.copy_order_href_to_clipboard') }}",
        hrefIsCopied = "{{ trans('content.href_is_copied') }}",
        userId = parseInt("{{ auth()->id() }}");
        window.openOrderId = parseInt("{{ request()->id }}");
</script>
@endsection
