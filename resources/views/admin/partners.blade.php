@extends('layouts.admin')

@section('content')
    <div class="panel panel-flat">
        @include('admin.blocks.title_block')
        <div class="panel-body">
            <partner-component
                get_partners_url="{{ route('admin.get_partners') }}"
                edit_partner_url="{{ route('admin.partners') }}"
                delete_partner_url="{{ route('admin.delete_partner') }}"
                arrange="{{ json_encode(['field' => 'id', 'direction' => 'desc']) }}"
            ></partner-component>
        </div>
        @include('admin.blocks.add_button_block')
    </div>
@endsection
