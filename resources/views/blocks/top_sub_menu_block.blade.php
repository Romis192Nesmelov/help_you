<div class="top-submenu">
    @foreach ($menus as $menu)
        <div class="tab {{ $loop->first ? 'active' : '' }}">
            <a id="top-submenu-{{ $menu }}" href="#">{{ trans($prefix.'.'.$menu.($postfix ?? '')) }}</a>
            @if (isset($items))
                <sup>{{ count($items[$menu]) }}</sup>
            @endif
        </div>
    @endforeach
</div>
