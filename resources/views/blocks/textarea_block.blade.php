<div class="form-group mb-3">
    @if (isset($label) && $label)
        <label for="{{ $name }}">{{ $label }}</label>
    @endif
    <textarea name="{{ $name }}" class="form-control @error($name) error @enderror" placeholder="{{ isset($placeholder) && $placeholder ? $placeholder : '' }}">{{ old($name, '') }}</textarea>
    @if (isset($ajax) && $ajax)
        @include('blocks.error_block')
    @else
        @error($name)
            @include('blocks.error_block')
        @enderror
    @endif
</div>
