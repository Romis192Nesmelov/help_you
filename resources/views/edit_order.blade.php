@extends('layouts.main')

@section('content')
    <div class="row">
        <edit-order-component
            next_step_url="{{ route('order.next_step') }}"
            prev_step_url="{{ route('order.prev_step') }}"
            order_preview_url="{{ route('order.orders',['preview' => 1]) }}"
            delete_order_image_url="{{ route('order.delete_order_image') }}"
            images="{{ json_encode([asset('images/edit_order/step1.png'),asset('images/edit_order/step2.png'),null,asset('images/edit_order/step4.png'),asset('images/edit_order/final.png')]) }}"
            order_types="{{ json_encode($order_types) }}"
            session="{{ session()->has($session_key) ? json_encode(session()->get($session_key)) : json_encode([]) }}"
            order="{{ isset($order) ? json_encode($order) : '' }}"
            yandex_api_key="{{ env('YANDEX_API_KEY') }}"
            input_image_hover="{{ asset('images/input_image_hover.svg') }}"
        >
        </edit-order-component>
    </div>
@endsection
