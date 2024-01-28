@extends('layouts.main')

@section('content')

<div class="row">
    <left-menu-component
        user="{{ json_encode(auth()->user()) }}"
        allow_change_avatar="0"
        left_menu="{{ json_encode($leftMenu) }}"
        logout_url="{{ route('auth.logout') }}"
        active_left_menu="{{ $active_left_menu }}"
    ></left-menu-component>
    <my-chats-component
        user_id="{{ auth()->id() }}"
        orders_urls="{{ json_encode(['my_orders' => route('messages.chats_my_orders'), 'performer' => route('messages.chats_performer')]) }}"
        chat_url="{{ route('messages.chat') }}"
    ></my-chats-component>

{{--            @include('blocks.top_sub_menu_block',[--}}
{{--                'menus' => ['my_orders','im_performer'],--}}
{{--                'prefix' => 'messages',--}}
{{--                'items' => $chats--}}
{{--            ])--}}
{{--            @foreach (['my_orders','im_performer'] as $menuItem)--}}
{{--                <div id="content-{{ $menuItem }}" class="content-block" {{ !$loop->first ? 'style=display:none' : '' }}>--}}
{{--                    @if (count($chats[$menuItem]))--}}
{{--                        @include('blocks.data_table_block',[--}}
{{--                            'orders' => $chats[$menuItem],--}}
{{--                            'chatMode' => true,--}}
{{--                            'editRoute' => null--}}
{{--                        ])--}}
{{--                    @endif--}}
{{--                    <h4 class="no-data-block text-uppercase text-secondary {{ count($chats[$menuItem]) ? 'd-none' : '' }}">{{ trans('content.no_data') }}</h4>--}}
{{--                </div>--}}
{{--            @endforeach--}}
{{--        </div>--}}
{{--    </div>--}}
</div>
{{--<script>const participantsText = "{{ trans('messages.participants') }}";</script>--}}
@endsection
