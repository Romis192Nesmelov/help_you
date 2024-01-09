<table class="table datatable-basic default">
    @foreach($orders as $order)
        <tr id="row-{{ $order->id }}">
            <td class="id">{{ $order->id }}</td>

            {{-- Head cell --}}
            <td>
                @if (isset($chatMode) && $chatMode)
                    <a href="{{ route('messages.chat',['order_id' => $order->id]) }}">
                        @include('blocks.datatable_head_column_block')
                    </a>
                @else
                    @include('blocks.datatable_head_column_block')
                @endif
            </td>

            {{-- Edit or remove performers cell --}}
            @if ($editRoute && $order['status'] == 2)
                <td class="order-cell-edit icon">
                    <a title="{{ trans('content.edit') }}" href="{{ route($editRoute,['id' => $order->id]) }}"><i class="icon-pencil5"></i></a>
                </td>
            @elseif ((isset($chatMode) && $chatMode) || $order['status'] == 1)
                <td class="order-cell-edit icon">
                    <nobr>
                        <i id="order-performers-{{ $order->id }}" title="{{ trans('messages.participants') }}" class="{{ $order->user_id == auth()->id() ? 'performers-list' : '' }} icon-users4 me-1"></i>
                        <span>{{ $order->performers->count() }}</span>
                    </nobr>
                </td>
            @else
                <td class="order-cell-edit icon empty"></td>
            @endif

            {{-- Status label cell --}}
            <td class="text-center">
                @if ($order->approved)
                    <span class="label {{ ['closed','in-progress','open'][$order->status] }}">{{ trans('content.status_'.((string)$order->status)) }}</span>
                @else
                    <span class="label in-approve">{{ trans('content.in_approve') }}</span>
                @endif
            </td>

            {{-- Close, resume or delete cell --}}
            @if (!$order->status && (!isset($chatMode) || !$chatMode))
                <td class="order-cell-button">
                    @include('blocks.button_block',[
                        'addClass' => 'resume-order micro',
                        'primary' => false,
                        'buttonText' => trans('content.resume')
                    ])
                </td>
            @elseif ($order->status == 1 && $order->approved && $order->user_id == auth()->id() && (!isset($chatMode) || !$chatMode))
                <td class="order-cell-button">
                    @include('blocks.button_block',[
                        'addClass' => 'close-order micro',
                        'primary' => false,
                        'buttonText' => trans('content.close')
                    ])
                </td>
            @elseif ($order->status == 2 && (!isset($chatMode) || !$chatMode))
                @include('blocks.del_dt_row_block', [
                    'id' => $order->id,
                    'title' => trans('content.delete')
                ])
            @else
                <td class="empty"></td>
            @endif
        </tr>
    @endforeach
</table>
