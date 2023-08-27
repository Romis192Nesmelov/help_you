<div class="form-group mt-3">
    <label class="checkbox-inline">
        <input class="styled" type="checkbox" name="{{ $name }}" {{ !count($errors) ? (isset($checked) && $checked ? 'checked=checked' : '') : (old($name) == 'on' ? 'checked=checked' : '') }} {{ isset($disabled) && $disabled ? 'disabled=disabled' : '' }}>
        @if (isset($label) && $label)
            {!! $label !!}
        @endif
    </label>
    @include('blocks.error_block')
</div>
