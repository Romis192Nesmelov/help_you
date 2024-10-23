@extends('layouts.admin')

@section('content')
    <div class="panel panel-flat">
        @include('admin.blocks.title_block')
        <div class="panel-body">
            <ticket-component
                incoming_obj="{{ isset($ticket) ? json_encode($ticket) : '' }}"
                edit_url="{{ route('admin.edit_ticket') }}"
                back_url="{{ request('parent_id') ? route('admin.users',['id' => request('parent_id')]) : route('admin.tickets') }}"
                placeholder_image="{{ asset('images/placeholder.jpg') }}"
                broadcast_on="admin_ticket_event"
                broadcast_as="admin_ticket"
                incoming_users="{{ json_encode($users) }}"
                parent_id="{{ request('parent_id') }}"
            ></ticket-component>
        </div>
    </div>
    @if (isset($ticket))
        <div class="panel panel-flat">
            <x-atitle>Переписка</x-atitle>
            <div class="panel-body">
                <div class="panel-body">
                    @if ($ticket->answers->count())
                        @php
                            if (request('parent_id')) {
                                $answerTableParams = ['parent_parent_id' => request('parent_id'),'parent_id' => $ticket->id];
                                $addButtonParams = ['slug' => 'add','parent_parent_id' => request('parent_id'),'parent_id' => $ticket->id];
                            } else {
                                $answerTableParams = ['parent_id' => $ticket->id];
                                $addButtonParams = ['slug' => 'add','parent_id' => $ticket->id];
                            }
                        @endphp

                        <answers-component
                            get_answers_url="{{ route('admin.get_answers',$answerTableParams) }}"
                            edit_answer_url="{{ route('admin.answers',$answerTableParams) }}"
                            delete_answer_url="{{ route('admin.delete_answer') }}"
                            arrange="{{ json_encode(['field' => 'id', 'direction' => 'desc']) }}"
                        ></answers-component>
                    @endif
                </div>
                <div class="panel-body">
                    <a href="{{ route('admin.answers',['slug' => 'add', 'parent_id' => $ticket->id]) }}">
                        @include('admin.blocks.button_block', [
                            'primary' => true,
                            'buttonType' => 'button',
                            'icon' => 'icon-database-add',
                            'buttonText' => 'Ответить',
                            'addClass' => 'pull-right'
                        ])
                    </a>
                </div>
            </div>
        </div>
    @endif
@endsection
