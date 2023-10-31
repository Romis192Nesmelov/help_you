<table class="table datatable-basic default">
    @foreach($items as $item)
        <tr id="row-{{ $item->id }}">
            <td class="id">{{ $item->id }}</td>
            <td>
                <div class="head">{{ isset($relationHead) ? $item->$relationHead->$headName : $item->$headName }}</div>
                <div class="content">{{ isset($relationContent) ? $item->$relationContent->$contentName : $item->$contentName }}</div>
            </td>
            @if (isset($editRoute))
                @include('blocks.edit_dt_row_block',['id' => $item->id])
            @else
                <td class="empty"></td>
            @endif
            @if (isset($statusField) && $statusField)
                <td class="text-center">
                    <span class="label {{ ['closed','in-progress','open'][$item[$statusField]] }}">{{ trans('content.status_'.((string)$item[$statusField])) }}</span>
                </td>
            @else
                <td class="empty"></td>
            @endif
            @if (isset($statusField) && $statusField && $item->status == 1)
                <td class="close-order-cell">
                    @include('blocks.button_block',[
                        'addClass' => 'close-order micro',
                        'primary' => false,
                        'buttonText' => trans('content.close')
                    ])
                </td>
            @elseif (isset($statusField) && $statusField && !$item->status)
                <td></td>
            @elseif (!isset($menuItem) || $menuItem != 'archive')
                @include('blocks.del_dt_row_block', ['id' => $item->id])
            @else
                <td class="empty"></td>
            @endif
        </tr>
    @endforeach
</table>
