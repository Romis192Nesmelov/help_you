<div {{ isset($id) ? 'id='.$id : '' }} class="form-group {{ isset($label) && $label ? 'has-label' : '' }} {{ isset($addClass) && $addClass ? $addClass : '' }}">
    @if (isset($icon) && $icon)
        <i class="{{ $icon }}"></i>
    @endif

    @if (isset($label) && $label)
        <label for="{{ $name }}">{{ $label }}</label>
    @endif
    <input
        type="{{ isset($type) && $type ? $type : 'text' }}"
        name="{{ $name }}" {{ isset($step) ? 'step='.$step : '' }}
        value="{{ old($name, ($value ?? '')) }}"
        class="form-control {{ isset($icon) && $icon ? 'has-icon' : '' }}@error($name) error @enderror"
        placeholder="{{ isset($placeholder) && $placeholder ? $placeholder : '' }}"
        {{ isset($min) ? 'min='.$min : '' }}
        {{ isset($max) ? 'max='.$max : '' }}
        {{ isset($disabled) && $disabled ? 'disabled=disabled' : '' }}
    >
    @include('blocks.wrap_error_block')
</div>
