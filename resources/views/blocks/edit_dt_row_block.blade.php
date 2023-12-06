<td class="order-cell-edit icon"><a {{ isset($title) && $title ? 'title='.$title : '' }} href="{{ route($editRoute,['id' => $id]) }}"><i class="icon-pencil5"></i></a></td>
