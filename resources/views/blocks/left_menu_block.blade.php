<left-menu-component
    user="{{ json_encode(auth()->user()) }}"
    left_menu="{{ json_encode($leftMenu) }}"
    logout_url="{{ route('auth.logout') }}"
    change_avatar_url="{{ $changeAvatarUrl ?? '' }}"
    active_left_menu="{{ $active_left_menu }}"
></left-menu-component>
