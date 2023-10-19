<x-modal
    class="delete-modal"
    id="{{ $id ?? 'delete-modal' }}"
    head="{{ trans('content.warning') }}"
    footer="1"
    yes_button="1"
    del-function="{{ route($action) }}"
>
    @csrf
    <h3 class="text-center">{{ $head }}</h3>
</x-modal>
