@if (isset($ajax) && $ajax)
    @include('blocks.error_block')
@else
    @error($name)
        @include('blocks.error_block')
    @enderror
@endif
