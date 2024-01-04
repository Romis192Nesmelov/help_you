@extends('layouts.main')

@section('content')

<x-modal id="order-data-modal">
    <div id="selected-points m-auto">
        <div id="points-container">
                <h6 class="text-center">
                    {{ trans('messages.chat_head',['order_id' => $order->id,'order_date' => $order->created_at->format('d.m.y'),'order_name' => $order->orderType->name]) }}
                </h6>
                <div class="w-100 d-flex mb-3 align-items-center justify-content-between">
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="avatar cir" style="{!! avatarProps($order->user->avatar, $order->user->avatar_props, 0.35) !!}"></div>
                        <div>
                            <div class="user-name">@include('blocks.user_name_block', ['user' => $order->user])</div>
                            <div class="born">{{ $order->user->born }}</div>
                        </div>
                    </div>
{{--                    <button id="subscribe-button" type="button" class="btn btn-primary small mt-0">--}}
{{--                        <i class="icon-bell-check"></i>--}}
{{--                        <span>{{ trans('content.subscribe') }}</span>--}}
{{--                    </button>--}}
                </div>
                @if ($order->images->count())
                    <div class="images owl-carousel">
                        @foreach ($order->images as $image)
                            <div class="image">
                                <img src="{{ asset($image->image) }}" />
                            </div>
                        @endforeach
                    </div>
                @endif
                <h2 class="order-type text-center mt-3 mb-1">{{ $order->orderType->name }}</h2>
                @if ($order->subtype_id)
                    <ul class="subtypes">
                        <li>{{ $order->subType->name }}</li>
                    </ul>
                @endif
                <p class="mb-1 text-center">{{ trans('content.address') }}: <span class="order-address">{{ $order->address }}</span></p>
                @if ($order->description_full || $order->description_short)
                    <p class="small text-center fst-italic mt-2 mb-0">{{ trans('content.description') }}:</p>
                    <p class="text-center order-description fst-italic mb-1">{{ $order->description_full ?? $order->description_short }}</p>
                @endif
            </div>
        </div>
    </div>
</x-modal>

<div class="row">
    @include('blocks.left_menu_block',['hasChangeAvatar' => false])
    <div class="col-12 col-lg-8">
        <div class="rounded-block tall white pt-4">
            <div class="d-flex justify-content-start align-items-center mb-3">
                <a href="{{ route('messages.chats') }}"><i title="{{ trans('messages.chats') }}" class="icon-arrow-left8 fs-4 me-3"></i></a>
                <div>
                    <b>
                        @if ($order->user_id == auth()->id())
                            @include('blocks.user_name_block',['user' => $order->performers[0]])
                        @else
                            @include('blocks.user_name_block',['user' => $order->user])
                        @endif
                    </b>
                    <div class="fs-lg-6 fs-sm-7">
                        <a data-bs-toggle="modal" data-bs-target="#order-data-modal">
                            {{ trans('messages.chat_head',[
                                'order_name' => $order->name,
                                'order_type_name' => $order->orderType->name,
                                'order_date' => $order->created_at->format('d.m.y')
                            ]) }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="content-block simple">
                <div id="messages">
                    @if ($order->messages->count())
                        @include('blocks.chat_date_block',['timestamp' => $order->messages[0]->created_at->timestamp])
                        @foreach ($order->messages as $k => $message)
                            @if ($k !== 0 && $message->created_at->format('d') != $order->messages[$k-1]->created_at->format('d'))
                                @include('blocks.chat_date_block',['timestamp' => $message->created_at->timestamp])
                            @endif
                            <div class="message-row {{ $message->user_id == auth()->id() ? 'my-self' : '' }}">
                                @if ($message->image)
                                    <div class="attached-image">
                                        <a href="{{ asset($message->image) }}" class="fancybox"><img src="{{ asset($message->image) }}" /></a>
                                        @include('blocks.message_block')
                                    </div>
                                @else
                                    @include('blocks.message_block')
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <div class="chat-input">
                    <div class="chat-attach-file">
                        <i class="icon-camera"></i>
                        <input type="file" name="image">
                    </div>
                    @include('blocks.textarea_block',[
                        'name' => 'body',
                        'icon' => 'icon-circle-right2',
                        'placeholder' => trans('messages.message'),
                        'max' => 255,
                        'ajax' => true
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    const orderId = parseInt("{{ $order->id }}"),
        newMessageUrl  = "{{ route('messages.new_message') }}",
        readMessageUrl = "{{ route('messages.read_message') }}";
</script>
@endsection
