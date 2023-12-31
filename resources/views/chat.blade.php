@extends('layouts.main')

@section('content')
<x-modal id="user-data-modal" head="{{ trans('content.user_data') }}">
    @include('blocks.user_creds_block',[
        'user' => $order->user_id == auth()->id() ? $order->performers[0] : $order->user,
        'rating' => $order->user_id == auth()->id() ? getUserRating($order->performers[0]) : null
    ])
    <h5 class="text-center">{{ trans('content.info_about_user') }}</h5>
    @include('blocks.info_about_user_block',['info' => $order->user_id == auth()->id() ? $order->performers[0]->info_about : $order->user->info_about])
</x-modal>

<x-modal id="order-data-modal">
    <div id="selected-points m-auto">
        <div id="points-container">
                <h6 class="text-center">
                    {{ trans('messages.chat_head',[
                        'order_id' => $order->id,
                        'order_name' => $order->name,
                        'order_type_name' => $order->orderType->name,
                        'order_date' => $order->created_at->format('d.m.y')
                    ]) }}
                </h6>
                @include('blocks.user_creds_block',['user' => $order->user])
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
                    <a data-bs-toggle="modal" data-bs-target="#user-data-modal">
                        <b>
                            @if ($order->user_id == auth()->id())
                                @include('blocks.user_name_block',['user' => $order->performers[0]])
                            @else
                                @include('blocks.user_name_block',['user' => $order->user])
                            @endif
                        </b>
                    </a>
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
    window.orderId = parseInt("{{ $order->id }}");
    const newMessageUrl  = "{{ route('messages.new_message') }}",
        readMessageUrl = "{{ route('messages.read_message') }}";
</script>
@endsection
