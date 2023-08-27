@if ($attributes->has('label') && $attributes->get('label'))
    <label for="{{ $attributes->get('name') }}">{{ $attributes->get('label') }} <sup>{{ (int)$attributes->get('required') ? '*' : '' }}</sup></label>
@endif
<div class="form-group has-feedback has-feedback-left {{ $attributes->has('error') && $attributes->get('error') ? "error" : '' }}">
    {!! $slot !!}
    @if (($attributes->has('icon') && $attributes->get('icon')) && (!$attributes->has('label') || !$attributes->get('label')))
        <div class="form-control-feedback">
            <i class="{{ $attributes->has('error') && $attributes->get('error') ? 'text-danger-800 '.$attributes->get('icon') : $attributes->get('icon') }} text-muted"></i>
        </div>
    @endif
    @include('blocks.error_block')
</div>
