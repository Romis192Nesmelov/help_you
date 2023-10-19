@extends('layouts.main')

@section('content')

@include('blocks.modal_delete_block',[
    'action' => 'delete_order',
    'head' => trans('content.do_you_really_want_to_delete_this_order')
])

<div class="row">
    @include('blocks.left_menu_block')
    <div class="col-12 col-lg-8">
        <div class="rounded-block tall">
            <h2>{{ trans('auth.my_orders') }}</h2>
            <div class="top-submenu">
                @foreach (['active','approving','archive'] as $menu)
                    <div class="tab {{ $loop->first ? 'active' : '' }}">
                        <a id="top-submenu-{{ $menu }}" href="#">{{ trans('auth.'.$menu.'_orders') }}</a>
                        <sup>{{ count($orders[$menu]) }}</sup>
                    </div>
                @endforeach
            </div>
            @foreach (['active','approving','archive'] as $item)
                <div id="content-{{ $item }}" class="content-block" {{ !$loop->first ? 'style=display:none' : '' }}>
                    @if (count($orders[$item]))
                        <table class="table datatable-basic">
                            @foreach($orders[$item] as $orderItem)
                                <tr id="row-{{ $orderItem->id }}">
                                    <td class="id">{{ $orderItem->id }}</td>
                                    <td>
                                        <div class="head">{{ $orderItem->orderType->name }}</div>
                                        <div class="address">{{ $orderItem->address }}</div>
                                    </td>
                                    @include('blocks.edit_dt_row_block')
                                    @include('blocks.del_dt_row_block', ['id' => $orderItem->id])
                                </tr>
                            @endforeach
                        </table>
                    @endif
                    <h4 class="no-data-block text-uppercase text-secondary {{ count($orders[$item]) ? 'd-none' : '' }}">{{ trans('content.no_data') }}</h4>
                </div>
            @endforeach
            <a href="{{ route('new_order') }}">
                @include('blocks.button_block',[
                    'addClass' => 'absolute-bottom',
                    'primary' => true,
                    'buttonText' => trans('content.home_head3')
                ])
            </a>
        </div>
    </div>
</div>
{{--    <script type="text/javascript" src="{{ asset('js/account.js') }}"></script>--}}
@endsection
