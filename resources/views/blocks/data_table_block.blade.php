<table class="table datatable-basic">
    @foreach($items as $item)
        <tr id="row-{{ $item->id }}">
            <td class="id">{{ $item->id }}</td>
            <td>
                <div class="head">{{ isset($relationHead) ? $item->$relationHead->$headName : $item->$headName }}</div>
                <div class="content">{{ isset($relationContent) ? $item->$relationContent->$contentName : $item->$contentName }}</div>
            </td>
            @if (isset($$editRoute))
                @include('blocks.edit_dt_row_block')
            @else
                <td></td>
            @endif
            @include('blocks.del_dt_row_block', ['id' => $item->id])
        </tr>
    @endforeach
</table>
