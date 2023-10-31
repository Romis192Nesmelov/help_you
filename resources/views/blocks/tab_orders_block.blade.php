@foreach ($menus as $menuItem)
    <div id="content-{{ $menuItem }}" class="content-block" {{ !$loop->first ? 'style=display:none' : '' }}>
        @if (count($orders[$menuItem]))
            @include('blocks.data_table_block',[
                'items' => $orders[$menuItem],
                'relationHead' => 'orderType',
                'headName' => 'name',
                'contentName' => 'address',
                'statusField' => 'status'
            ])
        @endif
        <h4 class="no-data-block text-uppercase text-secondary {{ count($orders[$menuItem]) ? 'd-none' : '' }}">{{ trans('content.no_data') }}</h4>
    </div>
@endforeach
@if ($useButton)
    <a href="{{ route('order.new_order') }}">
        @include('blocks.button_block',[
            'addClass' => 'absolute-bottom',
            'primary' => true,
            'buttonText' => trans('content.home_head3')
        ])
    </a>
@endif
