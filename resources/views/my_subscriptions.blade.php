@extends('layouts.main')

@section('content')

@include('blocks.modal_delete_block',[
    'action' => 'account.delete_subscription',
    'head' => trans('content.do_you_really_want_to_unsubscribe')
])

<div class="row">
    @include('blocks.left_menu_block',['hasChangeAvatar' => false])
    <div class="col-12 col-lg-8">
        <div class="rounded-block tall">
            <h2>{{ trans('account.my_subscriptions') }}</h2>
            <div class="content-block simple">
                @if (count($subscriptions))
                    <table class="table datatable-basic subscriptions">
                        @foreach ($subscriptions as $subscription)
                            @foreach ($subscription->orders as $order)
                                <tr class="row-{{ $subscription->id }}">
                                    <td class="id">{{ $subscription->id }}</td>
                                    <td class="cell-avatar">
                                        <div class="avatar cir" style="background-image: url({{ asset($subscription->user->avatar ?? 'images/def_avatar.svg') }} );"></div>
                                    </td>
                                    <td>
                                        <div class="head">{{ $subscription->user->name.' '.$subscription->user->family }}</div>
                                        <div class="content">{!! trans('content.born_date',['born' => $subscription->user->born]) !!}</div>
                                    </td>
                                    <td>
                                        <div class="head"><a href="{{ route('order.orders',['id' => $order->id]) }}">{{ $order->orderType->name }}</a></div>
                                        <div class="content">{{ trans('content.address').': '.$order->address }}</div>
                                    </td>
                                    @include('blocks.del_dt_row_block', [
                                        'id' => $subscription->id,
                                        'icon' => 'icon-bell-cross',
                                        'title' => trans('account.unsubscribe')
                                    ])
                                </tr>
                            @endforeach
                        @endforeach
                    </table>
                @else
                    <h4 class="no-data-block text-uppercase text-secondary">{{ trans('content.no_data') }}</h4>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
