@extends('layouts.main')

@section('content')
    <div class="row">
        <left-menu-component
            user="{{ json_encode(auth()->user()) }}"
            allow_change_avatar="1"
            input_image_hover="{{ asset('images/input_image_hover.svg') }}"
            left_menu="{{ json_encode($leftMenu) }}"
            logout_url="{{ route('auth.logout') }}"
            change_avatar_url="{{ route('account.change_avatar') }}"
            active_left_menu="{{ $active_left_menu }}"
        ></left-menu-component>
        <account-component
            user="{{ json_encode(auth()->user()) }}"
            edit_account_url="{{ route('account.edit_account') }}"
            get_code_url="{{ route('account.get_code') }}"
            change_phone_url="{{ route('account.change_phone') }}"
            change_password_url="{{ route('account.change_password') }}"
        ></account-component>
    </div>
@endsection
