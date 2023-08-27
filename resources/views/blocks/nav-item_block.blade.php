@php
if (isset($styles) && is_array($styles) && count($styles)) {
    $stylesStr = '';
    foreach ($styles as $key => $val) {
        $stylesStr .= $key.':'.$val.(is_integer($val) ? 'px' : '').';';
    }
}
@endphp
<li id="{{ $id.'-'.$menuItemKey }}" class="nav-item{{ ($activeMenu == $menuItemKey ? ' active' : '').(isset($addClass) && $addClass ? ' '.$addClass : '').(isset($menuItem['sub']) ? '  dropdown' : '') }}" {{ isset($stylesStr) ? 'style='.$stylesStr : '' }}>
    @if (isset($menuItem['sub']))
        <a class="nav-link dropdown-toggle" href="#" id="navbar-dropdown-{{ $menuItemKey }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">{{ trans('menu.'.$menuItemKey) }}</a>
        <ul class="dropdown-menu" aria-labelledby="navbar-dropdown-{{ $menuItemKey }}">
            @foreach($menuItem['sub'] as $subMenuKey)
                <li><a class="dropdown-item" href="{{ route($subMenuKey) }}">{{ trans('menu.'.$subMenuKey) }}</a></li>
            @endforeach
        </ul>
    @else
        <a class="nav-link{{ isset($nlAddClass) && !$menuItem['href'] ? ' '.$nlAddClass : '' }}" href="{{ $menuItem['href'] ? route($menuItemKey) : '#' }}">{{ trans('menu.'.$menuItemKey) }}</a>
    @endif
</li>
