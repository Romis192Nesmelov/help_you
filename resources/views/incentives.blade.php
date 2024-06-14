@extends('layouts.main')

@section('content')
<div class="row">
    @include('blocks.left_menu_block')
    <incentives-component
        user_id="{{ auth()->id() }}"
        get_incentives_url="{{ route('account.get_my_incentive') }}"
        incentive_url="{{ route('account.incentives') }}"
        delete_incentives_url="{{ route('account.delete_incentive') }}"
    ></incentives-component>
</div>
@endsection
