<div class="col-12 col-lg-4">
    <div class="rounded-block tall">
        @include('blocks.avatar_block')
        <ul class="menu">
            @foreach ($leftMenu as $itemMenu)
                <li {{ $active_left_menu == $itemMenu['key'] ? 'class=active' : '' }}><a href="{{ route('account.'.$itemMenu['key']) }}"><i class="{{ $itemMenu['icon'] }}"></i>{{ trans('auth.'.$itemMenu['key']) }}</a></li>
            @endforeach
        </ul>
        <div class="bottom-block">
            <div class="d-flex align-items-center justify-content-left">
                <a href="{{ route('auth.logout') }}"><i class="icon-exit3 me-1"></i>{{ trans('auth.logout') }}</a>
            </div>
        </div>
    </div>
</div>
