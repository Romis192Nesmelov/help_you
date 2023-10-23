@extends('layouts.main')

@section('content')
    <x-modal id="complete-modal" head="{{ trans('new_order.complete_message') }}">
        <img class="w-100" src="{{ asset('images/new_order/final.png') }}" />
    </x-modal>

    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="rounded-block tall">
                @for ($i=1;$i<=4;$i++)
                    <h1 id="head1-step{{ $i }}" class="mb-4 {{ getStepClass($i) }}">{{ trans('new_order.h1_step'.$i) }}</h1>
                @endfor
                <div id="progress-bar" class="{{ !session()->has('steps') ? 'd-none' : '' }}">
                    <label>{{ trans('new_order.order_was_created_for') }}</label>
                    <div class="progress" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar" style="width: {{ getStepProgress() }}">{{ getStepProgress() }}</div>
                    </div>
                </div>
                @for ($i=1;$i<=4;$i++)
                    <h2 id="head2-step{{ $i }}" class="{{ getStepClass($i) }}">{{ trans('new_order.h2_step'.$i) }}</h2>
                @endfor

                <form method="post" action="{{ route('order.next_step') }}">
                    @csrf
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
                                            value="{{ $order_type->id }}" {{ $loop->first || (session()->has('steps') && session()->get('steps')[0]['order_type_id'] == $order_type->id) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="order-type-{{ $order_type->id }}">{{ $order_type->name }}</label>
                                    </div>
                                    @if ($order_type->subtypes)
                                        <div class="sub-types-block checkbox-block">
                                            @foreach($order_type->subtypes as $subType)
                                                @include('blocks.checkbox_block',[
                                                    'id' => 'sub-type-'.$subType['id'],
                                                    'checked' => (session()->has('steps') && isset(session()->get('steps')[0]['subtype']) && in_array($subType['id'], session()->get('steps')[0]['subtype'])),
                                                    'noGap' => true,
                                                    'checkType' => 'form-check',
                                                    'name' => 'subtypes[]',
                                                    'value' => $subType['id'],
                                                    'label' => $subType['name'],
                                                    'ajax' => true
                                                ])
                                            @endforeach
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
                            'value' =>  session()->has('steps') && count(session()->get('steps')) >= 2 ? session()->get('steps')[1]['performers'] : 1,
                            'ajax' => true
                        ])
                    </div>
                    <div id="inputs-step3" class="{{ getStepClass(3) }}">
                        @include('blocks.input_block',[
                            'name' => 'address',
                            'type' => 'text',
                            'value' => session()->has('steps') && count(session()->get('steps')) >= 3 ? session()->get('steps')[2]['address'] : '',
                            'ajax' => true
                        ])
                    </div>
                    <div id="inputs-step4" class="{{ getStepClass(4) }}">
                        @include('blocks.textarea_block',[
                            'name' => 'description',
                            'ajax' => true,
                            'value' => auth()->user()->info_about
                        ])
                    </div>

                    <div class="bottom-block">
                        <div class="d-flex align-items-center justify-content-center justify-content-md-end">
                            @include('blocks.button_block',[
                                'id' => 'back',
                                'primary' => false,
                                'addClass' => 'link-cover '.(!session()->has('steps') ? 'd-none' : ''),
                                'buttonText' => trans('new_order.back')
                            ])
                            @include('blocks.button_block',[
                                'id' => 'next',
                                'primary' => false,
                                'buttonType' => 'submit',
                                'addClass' => 'link-cover',
                                'buttonText' => trans('new_order.next')
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
                    <p id="description-step{{ $i }}" class="{{ getStepClass($i) }}">{{ trans('new_order.description_step'.$i) }}</p>
                @endfor
            </div>
        </div>
    </div>
    <script>
        const nextStepUrl = "{{ route('order.next_step') }}",
            backStepUrl = "{{ route('order.prev_step') }}",
            orderPreviewUrl  = "{{ route('order.orders',['preview' => 1]) }}",
            yandexApiKey = "{{ env('YANDEX_API_KEY') }}",
            errorCheckAddress = "{{ trans('validation.check_the_address') }}";
        let step = parseInt("{{ session()->has('steps') ? count(session()->get('steps')) : 0 }}");
    </script>
    @if (session()->has('steps') && count(session()->get('steps')) >= 3)
        <script>let point = [parseFloat("{{ session()->get('steps')[2]['latitude'] }}"), parseFloat("{{ session()->get('steps')[2]['longitude'] }}")];</script>
    @else
        <script>let point = [];</script>
    @endif
    <script type="text/javascript" src="{{ asset('js/new_order.js') }}"></script>
@endsection
