<td class="icon"><a {{ isset($title) && $title ? 'title='.$title : '' }} href="{{ route('messages.chat',['order_id' => $id]) }}"><i class="icon-bubbles6"></i></a></td>
