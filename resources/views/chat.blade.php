@extends('layouts.main')

@section('content')

<div class="row">
    @include('blocks.left_menu_block')
    <div class="col-12 col-lg-8">
        <div class="rounded-block tall white">
            <h2 class="d-flex justify-content-start">
                <a href="{{ route('messages.chats') }}"><i title="{{ trans('messages.chats') }}" class="icon-arrow-left52"></i></a>
                {{ trans('messages.chat_head',[
                    'order_id' => $order->id,
                    'order_date' => $order->created_at->format('d.m.y'),
                    'order_name' => $order->orderType->name]) }}
            </h2>
            <div class="content-block simple">
                <div id="messages">
                    @include('blocks.chat_date_block',['timestamp' => $order->messages[0]->created_at->timestamp])
                    @foreach ($order->messages as $k => $message)
                        @if ($k !== 0 && $message->created_at->format('d') != $order->messages[$k-1]->created_at->format('d'))
                            @include('blocks.chat_date_block',['timestamp' => $message->created_at->timestamp])
                        @endif
                        <div class="message-block">
                            <div class="avatar cir" style="background: url({{ asset($message->user->avatar ?? 'images/def_avatar.svg' ) }})"></div>
                            <div class="message">
                                <div class="author">{{ $message->user->name.' '.$message->user->family }}<span>{{ $message->created_at->format('H:m') }}</span></div>
                                <div>{{ $message->body }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <div class="chat-input">
                    @include('blocks.input_block',[
                        'name' => 'body',
                        'type' => 'text',
                        'icon' => 'icon-arrow-right6',
                        'min' => 1,
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
