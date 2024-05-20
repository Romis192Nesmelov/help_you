<x-incover
    name="{{ $name }}"
    error="{{ count($errors) && $errors->has($name) ? $errors->first($name) : '' }}"
    label="{{ isset($label) && $label ? $label : ''  }}"
>
    <select name="{{ $name }}" class="select form-control @error($name) error @enderror">
        @if (is_array($values))
            @foreach ($values as $value => $options)
                <option value="{{ $value }}" {{ (!count($errors) ? $value == $selected : $value == old($name)) ? 'selected' : '' }}>{{ $options }}</option>
            @endforeach
        @else
            @foreach ($values as $value)
                <option value="{{ $value->id }}" {{ (!count($errors) ? $value->id == $selected : $value->id == old($name)) ? 'selected' : '' }}>
                    {{ $value[$option].(isset($addOption) ? ' '.$value[$addOption] : '') }}
                </option>
            @endforeach
        @endif
    </select>
</x-incover>
