@extends('layouts.admin')

@section('content')
    <div class="panel panel-flat">
        @include('admin.blocks.title_block')
        <div class="panel-body">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="panel panel-flat">
                    <x-atitle val="5">Время акции</x-atitle>
                    <div class="panel-body">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            @include('admin.blocks.date_block',[
                                'label' => 'Начало',
                                'name' => 'start',
                                'value' => isset($action) ? Carbon\Carbon::parse($action->start)->timestamp : time()
                            ])
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            @include('admin.blocks.date_block',[
                                'label' => 'Окончание',
                                'name' => 'end',
                                'value' => isset($action) ? Carbon\Carbon::parse($action->end)->timestamp : time() + (60 * 60 * 24 * 30)
                            ])
                        </div>
                    </div>
                </div>
            </div>
            <action-component
                incoming_users="{{ json_encode($users) }}"
                incoming_partners="{{ json_encode($partners) }}"
                incoming_obj="{{ isset($action) ? json_encode($action) : '' }}"
                edit_url="{{ route('admin.edit_action') }}"
                back_url="{{ request('parent_id') ? route('admin.actions',['id' => request('parent_id')]) : request('parent_id') }}"
                broadcast_on="admin_action_event"
                broadcast_as="admin_action"
                parent_id="{{ request('parent_id') }}"
            ></action-component>
        </div>
    </div>
@endsection
