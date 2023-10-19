<div class="col-12 col-lg-4">
    <div class="rounded-block tall">
        <div id="avatar-block">
            <table>
                <tr>
                    <td style="width: 70px;">
                        <div class="avatar cir @error('avatar') error @enderror" style="background-image: url({{ asset(auth()->user()->avatar ? auth()->user()->avatar : 'images/def_avatar.svg') }} );">
                            <img src="{{ asset('images/avatar_hover.svg') }}" />
                            <input type="file" name="avatar">
                        </div>
                    </td>
                    <td><div class="welcome">{!! trans('content.welcome', ['user' => auth()->user()->name ? auth()->user()->name.' '.auth()->user()->family : auth()->user()->phone ]) !!}</div></td>
                </tr>
                <tr>
                    <td colspan="2">
                        @include('blocks.error_block', ['name' => 'avatar'])
                    </td>
                </tr>
            </table>
        </div>
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
