@extends('layouts.main')

@section('content')
<div class="row">
    <left-menu-component
        user="{{ json_encode(auth()->user()) }}"
        allow_change_avatar="0"
        left_menu="{{ json_encode($leftMenu) }}"
        logout_url="{{ route('auth.logout') }}"
        active_left_menu="{{ $active_left_menu }}"
    ></left-menu-component>
    <my-help-list-component
        user_id="{{ auth()->id() }}"
        orders_urls="{{ json_encode(['active' => route('account.my_help_active'), 'archive' => route('account.my_help_archive')]) }}"
    ></my-help-list-component>
</div>
@endsection
