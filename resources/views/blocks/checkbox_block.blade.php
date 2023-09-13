<div class="form-check form-switch mt-3 {{ isset($addClass) && $addClass ? $addClass : '' }}">
    <label class="checkbox-inline">
        <input class="form-check-input @error($name) error @enderror" id="{{ $name }}-checkbox" type="checkbox" name="{{ $name }}" {{ isset($checked) && $checked ? 'checked=checked' : '' }} {{ isset($disabled) && $disabled ? 'disabled=disabled' : '' }}>
        <label class="form-check-label" for="{{ $name }}-checkbox">{!! $label !!}</label>
    </label>
    @if (isset($ajax))
        @include('blocks.error_block')
    @else
        @error($name)
            @include('blocks.error_block')
        @enderror
    @endif
</div>
