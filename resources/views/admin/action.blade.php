@extends('layouts.admin')

@section('content')
    <div class="panel panel-flat">
        @include('admin.blocks.title_block')
        <div class="panel-body">
            <form class="form-horizontal" action="{{ route('admin.edit_action') }}" method="post">
                @csrf
                @if (isset($action))
                    @include('admin.blocks.hidden_id_block',['id' => $action->id])
                @endif
                <div class="col-lg-3 col-md-4 col-sm-12">
                    <div class="panel panel-flat">
                        <x-atitle val="5">Партнер акции</x-atitle>
                        <div class="panel-body">
                            @include('admin.blocks.select_block',[
                                'name' => 'partner_id',
                                'values' => $partners,
                                'option' => 'name',
                                'selected' => isset($action) ? $action->partner_id : 1
                            ])
                        </div>
                    </div>
                    <div class="panel panel-flat">
                        <x-atitle val="5">Пользователи в акции</x-atitle>
                        <div class="panel-body">
                            <action-users-component
                                action_users="{{ json_encode(isset($action) ? $action->actionUsers->pluck('user_id')->toArray() : []) }}"
                                incoming_users="{{ json_encode($users) }}"
                            ></action-users-component>
                        </div>
                    </div>
                    <div class="panel panel-flat">
                        <x-atitle val="5">Время акции</x-atitle>
                        <div class="panel-body">
                            @include('admin.blocks.date_block',[
                                'label' => 'Начало',
                                'name' => 'start',
                                'value' => isset($action) ? Carbon\Carbon::parse($action->start)->timestamp : time()
                            ])
                            @include('admin.blocks.date_block',[
                                'label' => 'Окончание',
                                'name' => 'end',
                                'value' => isset($action) ? Carbon\Carbon::parse($action->end)->timestamp : time() + (60 * 60 * 24 * 30)
                            ])
                        </div>
                    </div>
                    <div class="panel panel-flat">
                        <x-atitle val="5">Рейтинг акции</x-atitle>
                        <div class="panel-body">
                            @include('admin.blocks.radio_button_block',[
                                'name' => 'rating',
                                'values' => [
                                    ['val' => 1, 'descript' => 'Один'],
                                    ['val' => 2, 'descript' => 'Два'],
                                ],
                                'activeValue' => isset($action) ? $action->rating : 1
                            ])
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 col-md-8 col-sm-12">
                    <div class="panel panel-flat">
                        <div class="panel-body">
                            @include('admin.blocks.input_block', [
                                'label' => 'Название акции',
                                'name' => 'name',
                                'type' => 'text',
                                'max' => 50,
                                'placeholder' => 'Название акции',
                                'value' => isset($action) ? $action->name : ''
                            ])

                            @include('admin.blocks.textarea_block',[
                                'label' => 'Содержание акции',
                                'name' => 'html',
                                'max' => 50000,
                                'value' => isset($action) ? $action->html : '',
                            ])
                            @include('admin.blocks.save_button_block')
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
