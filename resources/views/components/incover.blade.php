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
        @include('admin.blocks.wrap_error_block')
    </div>
</div>
