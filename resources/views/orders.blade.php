@extends('layouts.main')

@section('content')
<x-modal id="order-modal">
    <h5 class="text-center">{!! trans('content.order_number') !!}</h5>
    <hr/>
    @include('blocks.avatar_block',['accountMode' => false])
    <h2 id="order-type" class="mt-3 text-center"></h2>
    <ul></ul>
    <p class="text-center mb-0"><b>{{ trans('content.address') }}:</b></p>
    <h5 id="order-address" class="mb-3 text-center"></h5>
    <p class="text-center">
        <b>{{ trans('content.need_performers') }}</b> <span id="need-performers"></span><br>
        <b>{{ trans('content.ready_to_help') }}</b> <span id="ready-to-help"></span>
    </p>
    <h6 class="text-center">{{ trans('content.description') }}</h6>
    <p id="order-description"></p>
    <hr>
    @include('blocks.button_block',[
        'id' => 'respond-button',
        'primary' => true,
        'buttonText' => trans('content.respond_to_an_order')
    ])
</x-modal>

<div class="row">
    <div class="col-12 col-lg-3">
        <div class="rounded-block tall">

        </div>
    </div>
    <div class="col-12 col-lg-9">
        <div id="map" class="rounded-block tall">

        </div>
    </div>
</div>
<script type="text/javascript" src="{{ asset('js/orders.js') }}"></script>
<script>
    let absentDescr = "{{ trans('content.absent') }}",
        subtypes = [],
        points = [];
</script>

@foreach ($subtypes as $subtype)
    <script>
        subtypes.push({
            id: parseInt("{{ $subtype['id'] }}"),
            name: "{{ $subtype['name'] }}"
        });
    </script>
@endforeach

@foreach ($orders as $order)
    @if (count($order->performers) < $order->need_performers)
        <script>
            points.push({
                    id: parseInt("{{ $order->id }}"),
                    user: {
                        avatar: "{{ $order->user->avatar }}",
                        name: "{{ $order->user->name.' '.$order->user->family }}",
                        born: "{{ $order->user->born }}"
                    },
                    coordinates: [parseFloat("{{ $order->latitude }}"),parseFloat("{{ $order->longitude }}")],
                    type: "{{ $order->orderType->name }}",
                    subtypes: parseInt("{{ $order->subtypes !== null }}") ? "{{ json_encode($order->subtypes) }}" : null,
                    address: "{{ $order->address }}",
                    need_performers: parseInt("{{ $order->need_performers }}"),
                    performers: parseInt("{{ count($order->performers) }}"),
                    date: "{{ $order->created_at->format('d.m.Y') }}",
                    description: "{{ $order->description }}"
                }
            );
        </script>
    @endif
@endforeach
@endsection
