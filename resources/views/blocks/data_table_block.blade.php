<table class="table datatable-basic default">
    @foreach($items as $item)
        <tr id="row-{{ $item->id }}">
            <td class="id">{{ $item->id }}</td>
            <td>
                <div class="head">{{ isset($relationHead) ? $item->$relationHead->$headName : $item->$headName }}</div>
                <div class="content">{{ isset($relationContent) ? $item->$relationContent->$contentName : $item->$contentName }}</div>
            </td>

            @if (isset($editRoute) && isset($statusField) && $statusField && $item[$statusField] > 1)
                @include('blocks.edit_dt_row_block',[
                    'id' => $item->id,
                    'title' => trans('content.edit')
                ])
            @elseif (isset($chatMode) && $chatMode)
                <td class="icon"><nobr><i title="{{ trans('messages.participants') }}" class="icon-users4 me-1"></i>{{ $item->performers->count() }}</nobr></td>
            @else
                <td class="order-cell-edit empty"></td>
            @endif

            @if (isset($statusField) && $statusField)
                <td class="text-center">
                    @if ($item->approved)
                        <span class="label {{ ['closed','in-progress','open'][$item[$statusField]] }}">{{ trans('content.status_'.((string)$item[$statusField])) }}</span>
                    @else
                        <span class="label in_approve">{{ trans('content.in_approve') }}</span>
                    @endif
                </td>
            @else
                <td class="empty"></td>
            @endif

            @if (isset($statusField) && $statusField && $item->status == 1 && $item->approved && (!isset($chatMode) || !$chatMode))
                <td class="order-cell-button">
                    @include('blocks.button_block',[
                        'addClass' => 'close-order micro',
                        'primary' => false,
                        'buttonText' => trans('content.close')
                    ])
                </td>
            @elseif (isset($statusField) && $statusField && !$item->status && (!isset($chatMode) || !$chatMode))
                <td class="order-cell-button">
                    @include('blocks.button_block',[
                        'addClass' => 'resume-order micro',
                        'primary' => false,
                        'buttonText' => trans('content.resume')
                    ])
                </td>
            @elseif ((!isset($menuItem) || $menuItem != 'archive') && (!isset($chatMode) || !$chatMode))
                @include('blocks.del_dt_row_block', [
                    'id' => $item->id,
                    'title' => trans('content.delete')
                ])
            @elseif (isset($chatMode) && $chatMode)
                @include('blocks.chat_dt_row_block',[
                    'id' => $item->id,
                    'title' => trans('content.open')
                ])
            @else
                <td class="order-cell-delete empty"></td>
            @endif
        </tr>
    @endforeach
</table>
