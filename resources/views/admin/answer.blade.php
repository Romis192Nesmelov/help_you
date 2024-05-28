@extends('layouts.admin')

@section('content')
    <div class="panel panel-flat">
        @include('admin.blocks.title_block')
        <div class="panel-body">
            <answer-component
                admin_id="{{ auth()->id() }}"
                incoming_obj="{{ isset($answer) ? json_encode($answer) : '' }}"
                edit_url="{{ route('admin.edit_answer') }}"
                back_url="{{ route('admin.tickets',['id' => request('parent_id')]) }}"
                placeholder_image="{{ asset('images/placeholder.jpg') }}"
                broadcast_on="admin_answer_event"
                broadcast_as="admin_answer"
                parent_id="{{ request('parent_id') }}"
            >
            </answer-component>
        </div>
    </div>
@endsection
