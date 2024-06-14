@extends('layouts.main')

@section('content')
<div class="row">
    @include('blocks.left_menu_block')
    <my-help-list-component
        user_id="{{ auth()->id() }}"
        orders_urls="{{ json_encode(['active' => route('account.my_help_active'), 'archive' => route('account.my_help_archive')]) }}"
        read_unread_by_performer="{{ route('account.set_read_unread_by_performer') }}"
    ></my-help-list-component>
</div>
@endsection
