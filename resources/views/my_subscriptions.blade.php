@extends('layouts.main')

@section('content')

@include('blocks.modal_delete_block',[
    'action' => 'account.delete_subscription',
    'head' => trans('content.do_you_really_want_to_unsubscribe')
])

<div class="row">
    @include('blocks.left_menu_block',['hasChangeAvatar' => false])
    <div id="my-subscriptions" class="col-12 col-lg-8 right-block">
        <div class="rounded-block tall">
            <h2>{{ trans('account.my_subscriptions') }}</h2>
            <div id="content-my-subscriptions" class="content-block simple">
                @if ($unread_orders->count())
                    <table class="table datatable-basic default">
                        @foreach ($unread_orders as $unread_order)
                            <tr class="row-{{ $unread_order->order->id }}">
                                <td class="id">{{ $unread_order->order->id }}</td>
                                <td class="cell-avatar">
                                    @include('blocks.avatar_block', ['user' => $unread_order->order->user, 'coof' => 0.35])
                                </td>
                                <td>
                                    <div class="head">@include('blocks.user_name_block',['user' => $unread_order->order->user])</div>
                                    <div class="content user-age">{{ getUserAge($unread_order->order->user) }}</div>
                                </td>
                                <td>
                                    <div class="head"><a href="{{ route('order.orders',['id' => $unread_order->order->id]) }}">{{ $unread_order->order->orderType->name }}</a></div>
                                    <div class="content">{{ trans('content.address').': '.$unread_order->order->address }}</div>
                                </td>
                                @include('blocks.del_dt_row_block', [
                                    'id' => $unread_order->subscription->id,
                                    'icon' => 'icon-bell-cross',
                                    'title' => trans('account.unsubscribe')
                                ])
                            </tr>
                        @endforeach
                    </table>
                @endif
                <h4 class="no-data-block text-uppercase text-secondary {{ $unread_orders->count() ? 'd-none' : '' }}">{{ trans('content.no_data') }}</h4>
            </div>
        </div>
    </div>
</div>
<script> const unsubscribeText = "{{ trans('account.unsubscribe') }}";</script>
@endsection
