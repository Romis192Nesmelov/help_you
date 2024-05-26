@props([
    'labelClass' => '',
    'name' => '',
    'error' => null,
    'label' => null
])

<div class="mb-3">
    @if ($label)
        <label {{ $labelClass ? 'class='.$labelClass : '' }} for="{{ $name }}">{{ $label }}</label>
    @endif
    <div class="form-group {{ $error ? "error" : '' }}">
        {!! $slot !!}
        @error($name)
            @include('admin.blocks.error_block')
        @enderror
    </div>
</div>
