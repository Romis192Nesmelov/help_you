@extends('layouts.admin')

@section('content')
    <div class="panel panel-flat">
        @include('admin.blocks.title_block')
        <div class="panel-body">
            <form class="form-horizontal" action="{{ route('admin.edit_user') }}" method="post">
                @csrf
                @if (isset($user))
                    @include('admin.blocks.hidden_id_block',['id' => $user->id])
                @endif
                <div class="panel panel-flat">
                    <div class="panel-body">
                        @include('blocks.input_block', [
                            'label' => 'E-mail',
                            'name' => 'email',
                            'type' => 'email',
                            'max' => 100,
                            'placeholder' => 'E-mail',
                            'value' => isset($user) ? $user->email : ''
                        ])
                        @include('blocks.input_block', [
                            'label' => trans('admin.phone'),
                            'name' => 'phone',
                            'type' => 'text',
                            'max' => 16,
                            'placeholder' => trans('admin.phone'),
                            'value' => isset($user) ? $user->phone : ''
                        ])
                        @include('blocks.input_block', [
                            'label' => trans('admin.address'),
                            'name' => 'address',
                            'type' => 'phone',
                            'max' => 255,
                            'placeholder' => trans('admin.address'),
                            'value' => isset($user) ? $user->address : ''
                        ])
                        <div class="panel panel-flat">
                            @if (isset($user))
                                <div class="panel-heading">
                                    <h4 class="text-grey-300">{{ trans('admin.if_you_doesnt_want_to_change_password') }}</h4>
                                </div>
                            @endif
                            <div class="panel-body">
                                @include('blocks.input_block', [
                                    'label' => trans('admin.user_password'),
                                    'name' => 'password',
                                    'type' => 'password',
                                    'max' => 50,
                                    'placeholder' => trans('admin.user_password'),
                                    'value' => ''
                                ])

                                @include('blocks.input_block', [
                                    'label' => trans('admin.confirm_password'),
                                    'name' => 'password_confirmation',
                                    'type' => 'password',
                                    'max' => 50,
                                    'placeholder' => trans('admin.confirm_password'),
                                    'value' => ''
                                ])

                            </div>
                        </div>
                        @include('admin.blocks.checkbox_block', [
                            'name' => 'is_admin',
                            'label' => trans('admin.is_admin'),
                            'checked' => isset($user) ? $user->is_admin : false
                        ])
                        @include('admin.blocks.checkbox_block', [
                            'name' => 'active',
                            'label' => trans('admin.user_active'),
                            'checked' => isset($user) ? $user->active : true
                        ])
                    </div>
                </div>
                @include('admin.blocks.save_button_block')
            </form>
        </div>
    </div>
@endsection
