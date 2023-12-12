@extends('layouts.main')

@section('content')

<div class="row">
    @include('blocks.left_menu_block',['hasChangeAvatar' => false])
    <div class="col-12 col-lg-8">
        <div class="rounded-block tall">
            <h2>{{ trans('messages.chats') }}</h2>
            @include('blocks.top_sub_menu_block',[
                'menus' => ['my_orders','im_performer'],
                'prefix' => 'messages',
                'items' => $chats
            ])
            @foreach (['my_orders','im_performer'] as $menuItem)
                <div id="content-{{ $menuItem }}" class="content-block" {{ !$loop->first ? 'style=display:none' : '' }}>
                    @if (count($chats[$menuItem]))
                        @include('blocks.data_table_block',[
                            'items' => $chats[$menuItem],
                            'relationHead' => 'orderType',
                            'headName' => 'name',
                            'contentName' => 'address',
                            'statusField' => 'status',
                            'chatMode' => true
                        ])
                    @endif
                    <h4 class="no-data-block text-uppercase text-secondary {{ count($chats[$menuItem]) ? 'd-none' : '' }}">{{ trans('content.no_data') }}</h4>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
