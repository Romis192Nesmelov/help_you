@extends('layouts.main')

@section('content')
<div class="row">
    @include('blocks.left_menu_block',['changeAvatarUrl' => route('account.change_avatar')])
    <account-component
        user="{{ json_encode(auth()->user()) }}"
        edit_account_url="{{ route('account.edit_account') }}"
        get_code_url="{{ route('account.get_code') }}"
        change_phone_url="{{ route('account.change_phone') }}"
        change_password_url="{{ route('account.change_password') }}"
    ></account-component>
</div>
@endsection
