@extends('layouts.admin')

@section('content')
    <div class="panel panel-flat">
        @include('admin.blocks.title_block')
        <div class="panel-body">
            <form class="form-horizontal" action="{{ route('admin.edit_order') }}" enctype="multipart/form-data" method="post">
                @csrf
                @if (isset($order))
                    @include('admin.blocks.hidden_id_block',['id' => $order->id])
                @endif
                <div class="col-lg-3 col-md-4 col-sm-12">
                    <div class="panel panel-flat">
                        <x-atitle val="5">Заявитель</x-atitle>
                        <div class="panel-body">
                            @include('admin.blocks.select_block',[
                                'name' => 'user_id',
                                'values' => $users,
                                'selected' => isset($order) ? $order->user_id : 0,
                                'option' => 'family',
                                'addOption' => 'name'
                            ])
                        </div>
                    </div>
                    <div class="panel panel-flat">
                        <x-atitle val="5">Тип заявки</x-atitle>
                        <div class="panel-body">
                            @foreach($types as $type)
                                <div class="form-group no-margin-bottom">
                                    <input id="order_type_{{ $type->id }}" name="order_type_id" value="{{ $type->id }}" type="radio" {{ !isset($order) || $order->orderType->id == $type->id ? 'checked' : '' }}>
                                    <label class="ml-10 form-check-label" for="order_type_{{ $type->id }}">{{ $type->name }}</label>
                                    @if ($type->subtypesActive->count())
                                        <div class="subtypes {{ (!isset($order) && $loop->first) || $order->orderType->id == $type->id ? '' : 'hidden' }}">
                                            @foreach($type->subtypesActive as $k => $subtype)
                                                <div class="pl-15">
                                                    <div class="form-group no-margin-bottom">
                                                        <input id="order_sub_type_{{ $subtype->id }}" name="subtype_id" value="{{ $subtype->id }}" type="radio" {{ (isset($order) && $order->subType && $order->subType->id == $subtype->id) || $loop->first ? 'checked' : '' }}>
                                                        <label class="ml-10 form-check-label" for="order_sub_type_{{ $subtype->id }}"><small>{{ $subtype->name }}</small></label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="panel panel-flat">
                        <x-atitle val="5">Статус заявки</x-atitle>
                        <div class="panel-body">
                            @include('admin.blocks.radio_button_block',[
                                'name' => 'status',
                                'values' => [
                                    ['val' => 0, 'descript' => 'Закрыта'],
                                    ['val' => 1, 'descript' => 'В работе'],
                                    ['val' => 2, 'descript' => 'Открыта'],
                                    ['val' => 3, 'descript' => 'Новая'],
                                ],
                                'activeValue' => isset($order) ? $order->status : 2
                            ])
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 col-md-8 col-sm-12">
                    <div class="panel panel-flat">
                        <div class="panel-body">
                            <div class="col-lg-8 col-md-6 col-sm-12">
                                @include('admin.blocks.input_block', [
                                    'label' => 'Название заявки',
                                    'name' => 'name',
                                    'type' => 'text',
                                    'max' => 50,
                                    'placeholder' => 'Название заявки',
                                    'value' => isset($order) ? $order->name : ''
                                ])
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                @include('admin.blocks.input_block', [
                                    'label' => 'Количество исполнителей',
                                    'name' => 'need_performers',
                                    'type' => 'number',
                                    'min' => 1,
                                    'max' => 20,
                                    'placeholder' => 'Количество исполнителей',
                                    'value' => isset($order) ? $order->need_performers : 1
                                ])
                            </div>
                            <div class="col-md-12 col-sm-12">
                                <div class="panel panel-flat">
                                    <x-atitle val="5">Изображения</x-atitle>
                                    <div class="panel-body">
                                        @for($i=1;$i<=3;$i++)
                                            <div class="col-md-4 col-sm-12">
                                                @php
                                                    $currentImage = null;
                                                    if (isset($order) && $order->images->count()) {
                                                        foreach ($order->images as $image) {
                                                            if ($image->position == $i) $currentImage = $image->image;
                                                            break;
                                                        }
                                                    }
                                                @endphp
                                                <div id="photo{{ $i }}" {{ $currentImage ? 'photo_exist=1' : '' }} class="order-photo{{ count($errors) && $errors->has('photo'.$i) ? ' error' : '' }}" {!! $currentImage ? 'style="background-image:url('.asset($currentImage).')"' : '' !!}>
                                                    <i class="icon-file-plus2 {{ $currentImage ? 'hidden' : '' }}"></i>
                                                    <img class="hover-image" src="{{ asset('images/input_image_hover.svg') }}" />
                                                    <i id="remove-{{ $i }}" class="icon-close2 {{ !$currentImage ? 'hidden' : '' }}"></i>
                                                    <input type="file" name="photo{{ $i }}">
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                                <div class="panel panel-flat">
                                    <x-atitle val="5">Адрес</x-atitle>
                                    <div class="panel-body">
                                        <edit-order-map-component
                                            incoming_latitude="{{ isset($order) ? $order->latitude : 0 }}"
                                            incoming_longitude="{{ isset($order) ? $order->longitude : 0 }}"
                                            incoming_address="{{ isset($order) ? $order->address : '' }}"
                                            yandex_api_key="{{ env('YANDEX_API_KEY') }}"
                                        ></edit-order-map-component>
                                    </div>
                                </div>
                                <div class="panel panel-flat">
                                    <x-atitle val="5">Описание</x-atitle>
                                    <div class="panel-body">
                                        @include('admin.blocks.textarea_block',[
                                            'label' => 'Краткое описание (до 200 символов)',
                                            'name' => 'description_short',
                                            'max' => 200,
                                            'value' => isset($order) ? $order->description_short : '',
                                        ])
                                        @include('admin.blocks.textarea_block',[
                                            'label' => 'Полное описание (до 1000 символов)',
                                            'name' => 'description_full',
                                            'max' => 1000,
                                            'value' => isset($order) ? $order->description_full : '',
                                        ])
                                    </div>
                                </div>
                            </div>
                            @include('admin.blocks.save_button_block')
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
