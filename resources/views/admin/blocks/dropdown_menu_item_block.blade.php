<li class="dropdown dropdown-user">
    <a class="dropdown-toggle" data-toggle="dropdown">
        <span>
            {{ $menuName }}
            @if (isset($icon))
                <i class="{{ $icon }}"></i>
            @endif
        </span>
        <i class="caret"></i>
    </a>
    <ul class="dropdown-menu dropdown-menu-right">
        @foreach($menu as $item)
            <li>
                <a href="{{ $item['href'] }}">
                    @if (isset($item['icon']))
                        <i class="{{ $item['icon'] }}"></i>
                    @endif
                    {{ $item['text'] }}
                </a>
            </li>
        @endforeach
    </ul>
</li>
