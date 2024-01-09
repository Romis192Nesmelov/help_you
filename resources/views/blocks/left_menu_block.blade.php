<div class="col-12 col-lg-4">
    <div class="rounded-block tall">
        <div id="avatar-block" {{ isset($addClass) && $addClass ? 'class='.$addClass : '' }}>
            <div class="w-100">
                <div class="d-flex align-items-center justify-content-start">
                    <div class="avatar cir @error('avatar') error @enderror" style="{!! avatarProps(auth()->user()->avatar, auth()->user()->avatar_props, 0.35) !!}">
                        @if ($hasChangeAvatar)
                            <img src="{{ asset('images/input_image_hover.svg') }}" />
                            <input type="file" name="avatar">
                        @endif
                    </div>
                    <div class="fs-lg-6 fs-sm-7 ms-3">{!! trans('content.welcome', ['user' => auth()->user()->name ? auth()->user()->name.' '.auth()->user()->family : auth()->user()->phone ]) !!}</div>
                </div>
                @include('blocks.error_block', ['name' => 'avatar'])
            </div>
        </div>
        <ul class="menu">
            @foreach ($leftMenu as $itemMenu)
                <li id="left-menu-{{ $itemMenu['id'] }}" {{ $active_left_menu == $itemMenu['key'] ? 'class=active' : '' }}>
                    <a href="{{ route($itemMenu['key']) }}"><i class="{{ $itemMenu['icon'] }}"></i>{{ trans($itemMenu['key']) }}</a>
                </li>
            @endforeach
        </ul>
        <div class="bottom-block">
            <div class="d-flex align-items-center justify-content-left">
                <a href="{{ route('auth.logout') }}"><i class="icon-exit3 me-1"></i>{{ trans('auth.logout') }}</a>
            </div>
        </div>
    </div>
</div>
