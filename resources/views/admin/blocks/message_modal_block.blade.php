<x-modal id="message-modal" head="{{ trans('content.message') }}">
    <h4 class="text-center p-4">{{ session()->has('message') ? session()->get('message') : '' }}</h4>
</x-modal>
@if (session()->has('message'))
    <script>$('#message-modal').modal('show');</script>
@endif
