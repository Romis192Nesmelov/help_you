<div class="form-group {{ isset($type) && $type == 'number' ? 'number' : '' }} {{ isset($label) && $label ? 'has-label' : '' }}">
    @if (isset($label) && $label)
        <label for="{{ $name }}">{{ $label }} {!! isset($required) && $required ? '<sup>*</sup>' : '' !!}</label>
    @endif
    <input type="{{ isset($type) && $type ? $type : 'text' }}" name="{{ $name }}" {{ isset($step) ? 'step='.$step : '' }} value="{{ old($name, (isset($value) ? $value : '')) }}" class="form-control {{ isset($addClass) && $addClass ? $addClass : '' }} {{ isset($icon) && $icon ? 'has-icon' : '' }}@error($name) error @enderror" placeholder="{{ isset($placeholder) && $placeholder ? $placeholder : '' }}">

    @if (isset($icon) && $icon)
        <i class="{{ $icon }}"></i>
    @endif

    @if (isset($ajax))
        @include('blocks.error_block')
    @else
        @error($name)
            @include('blocks.error_block')
        @enderror
    @endif
</div>
