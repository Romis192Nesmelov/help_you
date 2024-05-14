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
    <incentives-component
        user_id="{{ auth()->id() }}"
        get_incentives_url="{{ route('account.get_my_incentive') }}"
        incentive_url="{{ route('account.incentives') }}"
        delete_incentives_url="{{ route('account.delete_incentive') }}"
    ></incentives-component>
</div>
@endsection
