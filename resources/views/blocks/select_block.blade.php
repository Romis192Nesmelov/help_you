<div {{ isset($id) ? 'id='.$id : '' }} class="form-group {{ isset($label) && $label ? 'has-label' : '' }} {{ isset($addClass) && $addClass ? $addClass : '' }}">
    @if (isset($label) && $label)
        <label for="{{ $name }}">{{ $label }}</label>
    @endif
    <select name="{{ $name }}" class="form-select" {{ isset($disabled) && $disabled ? 'disabled=disabled' : '' }}>
        @if (isset($firstEmpty) && $firstEmpty)
            <option value="" {{ (!count($errors) && isset($selected) ? $selected == null : '') }}>{{ (isset($prefix) && $prefix ? $prefix.' ' : '').$firstEmpty }}</option>
        @endif
        @if (is_array($values))
            @foreach ($values as $value)
                <option value="{{ $value }}" {{ (!count($errors) && isset($selected) ? $value == $selected : $value == old($name)) ? 'selected' : '' }}>{{ (isset($prefix) && $prefix ? $prefix.' ' : '').$value }}</option>
            @endforeach
        @else
            @foreach ($values as $value)
                <option value="{{ $value->id }}" {{ (!count($errors) && isset($selected) ? $value->id == $selected : $value->id == old($name)) ? 'selected' : '' }}>{{ (isset($prefix) && $prefix ? $prefix.' ' : '').$value[$option] }}</option>
            @endforeach
        @endif
    </select>
    @include('blocks.wrap_error_block')
</div>
