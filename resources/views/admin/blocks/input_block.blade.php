<x-incover
    name="{{ $name }}"
    error="{{ count($errors) && $errors->has($name) ? $errors->first($name) : '' }}"
    label="{{ isset($label) && $label ? $label : ''  }}"
    ajax="{{ $ajax ?? false }}"
>
    <input
        type="{{ isset($type) && $type ? $type : 'text' }}"
        name="{{ $name }}" {{ isset($step) ? 'step='.$step : '' }}
        value="{{ old($name, ($value ?? '')) }}"
        class="@error($name) error @enderror form-control"
        placeholder="{{ isset($placeholder) && $placeholder ? $placeholder : '' }}"
        {{ isset($min) ? (!isset($type) || $type == 'text' ? 'minlength=' : 'min=').$min : '' }}
        {{ isset($max) ? (!isset($type) || $type == 'text' ? 'maxlength=' : 'max=').$max : '' }}
        {{ isset($step) ? 'step='.$step : '' }}
        {{ isset($disabled) && $disabled ? 'disabled=disabled' : '' }}
    >
</x-incover>
