<div class="panel-heading">
    <h{{ $attributes->has('val') ? $attributes->get('val') : '3' }} class="panel-title {{ $attributes->has('align') ? $attributes->get('align') : '' }}">{{ $slot }}</h{{ $attributes->has('val') ? $attributes->get('val') : '3' }}>
</div>
