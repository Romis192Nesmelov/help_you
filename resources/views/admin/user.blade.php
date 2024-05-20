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
                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
                    <div class="panel panel-flat">
                        <div class="panel-body display-flex">
                            @if (isset($user))
                                <avatar-component
                                    user_id="{{ $user->id }}"
                                    avatar_image="{{ $user->avatar }}"
                                    avatar_props="{{ json_encode($user->avatar_props) }}"
                                    change_avatar_url="{{ route('admin.change_avatar') }}"
                                >
                                </avatar-component>
                            @else
                                <div class="avatar cir" style="background-image: url({{ asset('images/def_avatar.svg') }})">
                                    <img src="{{ asset('images/input_image_hover.svg') }}" />
                                    <input type="file" name="avatar">
                                </div>
                            @endif
                            <user-rating-component ratings="{{ json_encode($user->ratings) }}"></user-rating-component>
                        </div>
                    </div>
                </div>
                <div class="col-lg-10 col-md-9 col-sm-8 col-xs-12">
                    <div class="panel panel-flat">
                        <div class="panel-body">
                            @include('admin.blocks.input_block', [
                                'label' => 'Имя пользователя',
                                'name' => 'name',
                                'type' => 'text',
                                'max' => 255,
                                'placeholder' => 'Имя пользователя',
                                'value' => isset($user) ? $user->name : ''
                            ])
                            @include('admin.blocks.input_block', [
                                'label' => 'Фамилия пользователя',
                                'name' => 'family',
                                'type' => 'text',
                                'max' => 255,
                                'placeholder' => 'Фамилия пользователя',
                                'value' => isset($user) ? $user->family : ''
                            ])
                            @include('admin.blocks.input_block', [
                                'label' => 'Дата рождения',
                                'name' => 'born',
                                'type' => 'text',
                                'max' => 10,
                                'placeholder' => '__-__-____',
                                'value' => isset($user) ? $user->born : ''
                            ])
                            @include('admin.blocks.input_block', [
                                'label' => 'Телефон',
                                'name' => 'phone',
                                'type' => 'text',
                                'max' => 16,
                                'placeholder' => '+7(___)___-__-__',
                                'value' => isset($user) ? $user->phone : ''
                            ])
                            @include('admin.blocks.input_block', [
                                'label' => 'E-mail',
                                'name' => 'email',
                                'type' => 'email',
                                'max' => 100,
                                'placeholder' => 'E-mail',
                                'value' => isset($user) ? $user->email : ''
                            ])
                            <div class="panel panel-flat">
                                @if (isset($user))
                                    <div class="panel-heading">
                                        <h4 class="text-grey-300">Если вы не хотите менять пароль, оставьте эти поля пустыми</h4>
                                    </div>
                                @endif
                                <div class="panel-body">
                                    @include('admin.blocks.input_block', [
                                        'label' => 'Пароль',
                                        'name' => 'password',
                                        'type' => 'password',
                                        'max' => 50,
                                        'placeholder' => 'Пароль',
                                        'value' => ''
                                    ])
                                    @include('admin.blocks.input_block', [
                                        'label' => 'Повтор пароля',
                                        'name' => 'password_confirmation',
                                        'type' => 'password',
                                        'max' => 50,
                                        'placeholder' => 'Повтор пароля',
                                        'value' => ''
                                    ])
                                </div>
                            </div>
                            @include('admin.blocks.textarea_block',[
                                'label' => 'Информация о пользователе',
                                'name' => 'text',
                                'max' => 5000,
                                'value' => isset($user) ? $user->info_about : '',
                            ])
                            @include('admin.blocks.checkbox_block', [
                                'name' => 'admin',
                                'label' => 'Пользователь является админом',
                                'checked' => isset($user) ? $user->admin : false
                            ])
                            @include('admin.blocks.checkbox_block', [
                                'name' => 'active',
                                'label' => 'Пользователь активен',
                                'checked' => isset($user) ? $user->active : true
                            ])
                            @include('admin.blocks.save_button_block')
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
