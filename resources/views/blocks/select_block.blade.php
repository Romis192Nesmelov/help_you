<div {{ isset($id) ? 'id='.$id : '' }} class="form-group {{ isset($label) && $label ? 'has-label' : '' }} {{ isset($addClass) && $addClass ? $addClass : '' }}">
    @if (isset($label) && $label)
        <label for="{{ $name }}">{{ $label }}</label>
    @endif
    <select name="{{ $name }}" class="form-select" {{ isset($disabled) && $disabled ? 'disabled=disabled' : '' }}>
        @if (is_array($values))
            @foreach ($values as $value => $options)
                <option value="{{ $value }}" {{ (!count($errors) ? $value == $selected : $value == old($name)) ? 'selected' : '' }}>{{ $options }}</option>
            @endforeach
        @else
            @foreach ($values as $value)
                <option value="{{ $value->id }}" {{ (!count($errors) ? $value->id == $selected : $value->id == old($name)) ? 'selected' : '' }}>{{ $value[$option] }}</option>
            @endforeach
        @endif
    </select>
    @include('blocks.wrap_error_block')
</div>
