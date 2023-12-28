<div {{ isset($id) ? 'id='.$id : '' }} class="form-group text-area {{ isset($icon) && $icon ? 'has-icon' : '' }}">
    @if (isset($label) && $label)
        <label for="{{ $name }}">{{ $label }}</label>
    @endif
    @if (isset($icon) && $icon)
        <i class="{{ $icon }}"></i>
    @endif
    <textarea
        name="{{ $name }}"
        class="form-control @error($name) error @enderror {{ $addClass ?? '' }}"
        placeholder="{{ isset($placeholder) && $placeholder ? $placeholder : '' }}"
        {{ isset($max) && $max ? 'maxlength='.$max : '' }}
    >{{ old($name, ($value ?? '')) }}</textarea>
    @if (isset($ajax) && $ajax)
        @include('blocks.error_block')
    @else
        @error($name)
            @include('blocks.error_block')
        @enderror
    @endif
</div>
