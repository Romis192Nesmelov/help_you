<div {{ isset($id) ? 'id='.$id : '' }} class="form-group mb-3">
    @if (isset($label) && $label)
        <label for="{{ $name }}">{{ $label }}</label>
    @endif
    <textarea name="{{ $name }}" class="form-control @error($name) error @enderror {{ $addClass ?? '' }}" placeholder="{{ isset($placeholder) && $placeholder ? $placeholder : '' }}">{{ old($name, ($value ?? '')) }}</textarea>
    @if (isset($ajax) && $ajax)
        @include('blocks.error_block')
    @else
        @error($name)
            @include('blocks.error_block')
        @enderror
    @endif
</div>
