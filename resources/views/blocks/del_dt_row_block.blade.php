<td class="icon"><i {{ isset($title) && $title ? 'title='.$title : '' }} del-data="{{ $id }}" modal-data="{{ $modal ?? 'delete-modal' }}" class="{{ $icon ?? 'icon-close2' }}"></i></td>
