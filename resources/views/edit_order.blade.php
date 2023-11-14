@extends('layouts.main')

@section('content')
    <x-modal id="complete-modal" head="{{ trans('edit_order.complete_message') }}">
        <img class="w-100" src="{{ asset('images/new_order/final.png') }}" />
    </x-modal>

    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="rounded-block tall">
                @for ($i=1;$i<=4;$i++)
                    <h1 id="head1-step{{ $i }}" class="mb-4 {{ getStepClass($i) }}">
                        @if ($i == 1 && isset($order))
                            {{ trans('edit_order.edit_order',['id' => $order->id]) }}
                        @else
                            {{ trans('edit_order.h1_step'.$i) }}
                        @endif
                    </h1>
                @endfor
                <div id="progress-bar" class="{{ !session()->has('steps') ? 'd-none' : '' }}">
                    <label>{{ trans('edit_order.order_was_created_for') }}</label>
                    <div class="progress" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar" style="width: {{ getStepProgress() }}">{{ getStepProgress() }}</div>
                    </div>
                </div>
                @for ($i=1;$i<=4;$i++)
                    <h2 id="head2-step{{ $i }}" class="{{ getStepClass($i) }}">{{ trans('edit_order.h2_step'.$i) }}</h2>
                @endfor

                <form method="post" enctype="multipart/form-data" action="{{ route('order.next_step') }}">
                    @csrf
                    @if (isset($order))
                        <input type="hidden" name="id" value="{{ $order->id }}">
                    @endif

                    <div id="inputs-step1" class="{{ getStepClass(1) }}">
                        <div class="ps-3">
                            @foreach ($order_types as $order_type)
                                <div id="radio-group-{{ $order_type->id }}" class="radio-group">
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            id="order-type-{{ $order_type->id }}"
                                            type="radio"
                                            name="order_type_id"
                                            value="{{ $order_type->id }}"
                                            @checked(
                                                ($loop->first && !session()->has('steps')) ||
                                                (isset($order) && $order->order_type_id == $order_type->id) ||
                                                (session()->has('steps') && session()->get('steps')[0]['order_type_id'] == $order_type->id)
                                            )
                                        >
                                        <label class="form-check-label" for="order-type-{{ $order_type->id }}">{{ $order_type->name }}</label>
                                    </div>
                                    @if ($order_type->subtypes)
                                        <div class="sub-types-block">
                                            @include('blocks.radiobox_block',[
                                                'addClass' => 'small',
                                                'items' => $order_type->subtypesActive,
                                                'idPrefix' => 'sub_type',
                                                'name' => 'subtype_id',
                                                'checked' => (isset($order) && $order->subtype_id ? $order->subtype_id : (session()->has('steps') ? session()->get('steps')[0]['subtype_id'] : null)),
                                                'option' => 'name'
                                            ])
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div id="inputs-step2" class="{{ getStepClass(2) }}">
                        @include('blocks.input_block',[
                            'name' => 'need_performers',
                            'min' => 1,
                            'max' => 20,
                            'type' => 'number',
                            'addClass' => 'col-12 col-md-6',
                            'value' => isset($order) ? $order->need_performers : (session()->has('steps') && count(session()->get('steps')) >= 2 ? session()->get('steps')[1]['need_performers'] : 1),
                            'ajax' => true
                        ])

                        <h2 class="mt-4">{{ trans('content.add_order_photo') }}</h2>
                        <div class="row">
{{--                            <label>{{ trans('content.add_order_photo') }}</label>--}}
                            @for ($i=1;$i<=3;$i++)
                                @php $orderImage = null; @endphp
                                @if (isset($order) && $order->images->count())
                                    @foreach ($order->images as $image)
                                        @php
                                            $fileInfo = pathinfo($image->image);
                                            $imagePosition = (int)str_replace('order'.$order->id.'_','',$fileInfo['filename']);
                                        @endphp
                                        @if ($imagePosition == $i)
                                            @php $orderImage = $image->image; @endphp
                                            @break
                                        @endif
                                    @endforeach
                                @endif
                                <div class="col-lg-4 col-md-4 col-sm-12">
                                    @include('blocks.order_photo_block',[
                                        'name' => 'photo'.$i,
                                        'image' => $orderImage
                                    ])
                                </div>
                            @endfor
                        </div>
                    </div>
                    <div id="inputs-step3" class="{{ getStepClass(3) }}">
                        @include('blocks.input_block',[
                            'name' => 'address',
                            'type' => 'text',
                            'value' => isset($order) ? $order->address : (session()->has('steps') && count(session()->get('steps')) >= 3 ? session()->get('steps')[2]['address'] : ''),
                            'ajax' => true
                        ])
                    </div>
                    <div id="inputs-step4" class="{{ getStepClass(4) }}">
                        @include('blocks.textarea_block',[
                            'name' => 'description_short',
                            'label' => trans('edit_order.description_short'),
                            'max' => 200,
                            'ajax' => true,
                            'value' => isset($order) ? $order->description_short : (session()->has('steps') && count(session()->get('steps')) == 4 ? session()->get('steps')[3]['description_short'] : ''),
                        ])
                        @include('blocks.textarea_block',[
                            'name' => 'description_full',
                            'label' => trans('edit_order.description_full'),
                            'max' => 1000,
                            'ajax' => true,
                            'value' => isset($order) ? $order->description_full : (session()->has('steps') && count(session()->get('steps')) == 4 ? session()->get('steps')[3]['description_full'] : ''),
                        ])
                    </div>
                    <div class="bottom-block">
                        <div class="d-flex align-items-center justify-content-center justify-content-md-end">
                            @include('blocks.button_block',[
                                'id' => 'back',
                                'primary' => false,
                                'addClass' => 'link-cover '.(!session()->has('steps') ? 'd-none' : ''),
                                'buttonText' => trans('edit_order.back')
                            ])
                            @include('blocks.button_block',[
                                'id' => 'next',
                                'primary' => false,
                                'buttonType' => 'submit',
                                'addClass' => 'link-cover',
                                'buttonText' => trans('edit_order.next')
                            ])
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="rounded-block three-quarter d-none d-md-flex align-items-center justify-content-center">
                @foreach ([1,2,4] as $step)
                    <img id="image-step{{ $step }}" class="{{ getStepClass($step) }}" src="{{ asset('images/new_order/step'.$step.'.png') }}">
                @endforeach
                <div id="image-step3" class="{{ getStepClass(3) }}"></div>
            </div>
            <div class="rounded-block one-quarter">
                @for ($i=1;$i<=5;$i++)
                    <p id="description-step{{ $i }}" class="{{ getStepClass($i) }}">{{ trans('edit_order.description_step'.$i) }}</p>
                @endfor
            </div>
        </div>
    </div>
    <script>
        const nextStepUrl = "{{ route('order.next_step') }}",
            backStepUrl = "{{ route('order.prev_step') }}",
            orderPreviewUrl  = "{{ route('order.orders',['preview' => 1]) }}",
            deleteOrderImageUrl = "{{ route('order.delete_order_image') }}",
            yandexApiKey = "{{ env('YANDEX_API_KEY') }}",
            errorCheckAddress = "{{ trans('validation.check_the_address') }}";
        let step = parseInt("{{ session()->has('steps') ? count(session()->get('steps')) : 0 }}");
    </script>

    @if (session()->has('steps') && count(session()->get('steps')) >= 3)
        <script>let point = [parseFloat("{{ session()->get('steps')[2]['latitude'] }}"), parseFloat("{{ session()->get('steps')[2]['longitude'] }}")];</script>
    @elseif (isset($order))
        <script>
            let point = [parseFloat("{{ $order->latitude }}"), parseFloat("{{ $order->longitude }}")]
            const orderId = parseInt({{ $order->id }});
                editOrderFlag = true;
        </script>
    @else
        <script>
            let point = [];
            const orderId = null;
        </script>
    @endif

    <script type="text/javascript" src="{{ asset('js/edit_order.js') }}"></script>
@endsection
