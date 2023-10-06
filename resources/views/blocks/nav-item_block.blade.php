<li id="{{ $id.'-'.$menuItem }}" class="nav-item{{ ($activeMainMenu == $menuItem ? ' active' : '').(isset($addClass) && $addClass ? ' '.$addClass : '') }}" {{ isset($stylesStr) ? 'style='.$stylesStr : '' }}>
    <a class="nav-link{{ isset($nlAddClass) ? ' '.$nlAddClass : '' }}" href="{{ route($menuItem) }}">{{ $menuName }}</a>
</li>
