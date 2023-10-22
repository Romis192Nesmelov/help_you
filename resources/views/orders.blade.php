@extends('layouts.main')

@section('content')
<x-modal id="order-modal">
    <h5 class="bg-gray">{!! trans('content.order_number') !!}</h5>
    <hr/>
    @include('blocks.avatar_block',['accountMode' => false])
    <h2 class="order-type mt-3 bg-orange"></h2>
    <ul class="subtypes"></ul>
    <hr>
    <p class="text-center mb-0"><b>{{ trans('content.address') }}:</b></p>
    <h5 class="order-address mb-3 text-center"></h5>
    <p class="bg-gray">
        <b>{{ trans('content.need_performers') }}</b> <span id="need-performers"></span><br>
        <b>{{ trans('content.ready_to_help') }}</b> <span id="ready-to-help"></span>
    </p>
    <h6 class="text-center">{{ trans('content.description') }}</h6>
    <p id="order-description" class="fst-italic"></p>
    <hr>
    @include('blocks.button_block',[
        'id' => 'respond-button',
        'primary' => true,
        'buttonText' => trans('content.respond_to_an_order')
    ])
</x-modal>

<x-modal id="order-respond-modal">
    <h5 class="bg-gray">{!! trans('content.thanks_to_respond') !!}</h5>
    <hr>
    <h2 class="order-type bg-orange mt-3"></h2>
    @include('blocks.avatar_block',['accountMode' => false])
    <p class="text-center"><b>{{ trans('content.waiting_for_you_at_the_address') }}</b></p>
    <h5 class="order-address text-center mt-0"></h5>
    <hr>
    <p>{{ trans('content.you_can_discuss_details_in_chat') }}</p>
    <hr>
    <h1 class="text-orange text-center">{{ trans('content.thank_you') }}</h1>
</x-modal>

<div class="row">
    <div class="col-12 col-lg-3">
        <div class="rounded-block tall">
            <form method="post" action="{{ $order_preview ? route('get_preview') : route('get_orders') }}">
                @csrf
                <h2>{{ trans('content.filters') }}</h2>
                @include('blocks.select_block',[
                    'addClass' => 'mb-4',
                    'firstEmpty' => trans('content.all'),
                    'label' => trans('content.order_types'),
                    'name' => 'order_type',
                    'values' => $order_types,
                    'option' => 'name'
                ])
                @include('blocks.select_block',[
                    'addClass' => 'mb-4',
                    'firstEmpty' => trans('content.any'),
                    'label' => trans('content.number_of_performers'),
                    'name' => 'performers',
                    'values' => range(1, 20)
                ])
                <div class="bottom-block">
                    @include('blocks.button_block',[
                        'id' => 'apply-button',
                        'buttonType'=> 'submit',
                        'primary' => true,
                        'buttonText' => trans('content.apply')
                    ])
                    @if ($order_preview)
                        @include('blocks.button_block',[
                            'id' => 'show-default',
                            'primary' => true,
                            'addClass' => 'mt-2',
                            'buttonText' => trans('content.show_default_map')
                        ])
                    @endif
                </div>
            </form>
        </div>
    </div>
    <div class="col-12 col-lg-9">
        <div id="map" class="rounded-block tall"></div>
    </div>
</div>
<script type="text/javascript" src="{{ asset('js/orders.js') }}"></script>
<script>
    let orderResponseUrl = "{{ route('order_response') }}",
        getOrdersUrl =  "{{ route('get_orders') }}",
        absentDescr = "{{ trans('content.absent') }}",
        userId = parseInt("{{ auth()->id() }}");
</script>
@endsection
