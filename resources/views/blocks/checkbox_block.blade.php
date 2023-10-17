<div class="{{ $checkType ?? 'form-check form-switch' }} {{ isset($noGap) && $noGap ? '' : 'mt-3' }} {{ isset($addClass) && $addClass ? $addClass : '' }}">
    <label class="checkbox-inline">
        <input class="form-check-input" id="{{ $id ?? $name.'-checkbox' }}" @error($name) error @enderror" type="checkbox" name="{{ $name }}" {{ isset($value) ? 'value='.$value : '' }} {{ isset($checked) && $checked ? 'checked=checked' : '' }} {{ isset($disabled) && $disabled ? 'disabled=disabled' : '' }}>
        <label class="form-check-label" for="{{ $id ?? $name.'-checkbox' }}">{!! $label !!}</label>
    </label>
    @include('blocks.wrap_error_block')
</div>
