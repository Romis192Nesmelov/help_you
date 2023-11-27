@extends('layouts.main')

@section('content')

<div class="row">
    @include('blocks.left_menu_block')
    <div class="col-12 col-lg-8">
        <div class="rounded-block tall">
            <h2 class="d-flex justify-content-start">
                <a href="{{ route('messages.chats') }}"><i title="{{ trans('messages.chats') }}" class="icon-arrow-left52"></i></a>
                {{ trans('messages.chat_head',[
                    'order_id' => $order->id,
                    'order_date' => $order->created_at->format('d.m.y'),
                    'order_name' => $order->orderType->name]) }}
            </h2>
            <div class="content-block simple">
                <div id="messages">
                    @foreach ($order->messages as $message)
                        <div class="message-block">
                            <div class="message{{ $message->user->id == auth()->id() ? ' you' : '' }}">
                                <div class="author">{{ $message->user->name.' '.$message->user->family }}:</div>
                                <div>{{ $message->body }}</div>
                                <div class="time">{{ $message->created_at->format('d.m.y H.i.m') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <form method="post" id="new-message" class="w-100" action="{{ route('messages.new_message') }}">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <div class="chat-input">
                        @include('blocks.input_block',[
                            'name' => 'body',
                            'type' => 'text',
                            'icon' => 'icon-arrow-right6',
                            'min' => 1,
                            'max' => 3000,
                            'ajax' => true
                        ])
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    const orderId = parseInt("{{ $order->id }}"),
        readMessageUrl = "{{ route('messages.read_message') }}";
</script>
@endsection
