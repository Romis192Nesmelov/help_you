@extends('layouts.main')

@section('content')
    <x-modal id="change-phone-modal" head="{{ trans('auth.change_phone') }}">
        <form method="post" action="{{ route('account.change_phone') }}">
            @csrf
            @include('blocks.input_phone_block')
            @include('blocks.input_code_block')
            @include('blocks.button_block',[
                 'id' => 'get-code',
                 'disabled' => true,
                 'addClass' => 'mb-3',
                 'primary' => true,
                 'icon' => 'icon-key',
                 'buttonText' => trans('auth.get_code'),
             ])
            @include('blocks.button_block',[
                'id' => 'change-phone-button',
                'disabled' => true,
                'addClass' => 'mb-3 d-none',
                'primary' => true,
                'buttonType' => 'submit',
                'icon' => 'icon-iphone',
                'buttonText' => trans('auth.change_phone'),
            ])
        </form>
        @include('blocks.get_code_again_block')
    </x-modal>

    <x-modal id="change-password-modal" head="{{ trans('auth.change_password') }}">
        <form method="post" action="{{ route('account.change_password') }}">
            @csrf
            @include('blocks.input_block',[
                'name' => 'old_password',
                'addClass' => 'password',
                'type' => 'password',
                'label' => trans('auth.old_password'),
                'icon' => 'icon-eye',
                'ajax' => true
            ])
            @include('blocks.input_passwords_block', [
                'labelPassword' => trans('auth.new_password'),
                'labelConfirmPassword' => trans('auth.new_password_confirm')
            ])
            @include('blocks.button_block',[
                'id' => 'change-password-button',
                'addClass' => 'mb-3',
                'primary' => true,
                'buttonType' => 'submit',
                'icon' => 'icon-file-locked2',
                'buttonText' => trans('auth.change_password'),
                'disabled' => true
            ])
        </form>
    </x-modal>

    <form class="row" method="post" action="{{ route('account.edit_account') }}">
        @csrf
        @include('blocks.left_menu_block')
        <div class="col-12 col-lg-8">
            <div class="rounded-block tall">
                <div class="row">
                    <div class="col-12 col-lg-6">
                        @foreach (['name','family','born','email'] as $accountItem)
                            @include('blocks.input_block',[
                                'name' => $accountItem,
                                'placeholder' => trans('auth.enter_your_'.$accountItem),
                                'label' => trans('auth.'.$accountItem),
                                'ajax' => true,
                                'value' => auth()->user()->$accountItem
                            ])
                        @endforeach
                        <p id="phone-number" class="mt-3 mb-0 text-center">{{ trans('auth.phone').': '.auth()->user()->phone }}</p>
                        @include('blocks.button_block',[
                            'primary' => false,
                            'addClass' => 'w-100 mt-3',
                            'dataTarget' => 'change-phone-modal',
                            'buttonText' => trans('auth.change_phone')
                        ])
                        @include('blocks.button_block',[
                            'primary' => false,
                            'addClass' => 'w-100 mt-2 mb-3',
                            'dataTarget' => 'change-password-modal',
                            'buttonText' => trans('auth.change_password')
                        ])
                    </div>
                    <div class="col-12 col-lg-6">
                        @include('blocks.textarea_block',[
                            'id' => 'info-about',
                            'name' => 'info_about',
                            'label' => trans('auth.info_about'),
                            'ajax' => true,
                            'value' => auth()->user()->info_about
                        ])
                        <div class="w-100 text-end mt-4">
                            @include('blocks.button_block',[
                                'id' => 'account-save',
                                'primary' => true,
                                'buttonType' => 'submit',
                                'type' => 'submit',
                                'buttonText' => trans('content.save'),
                                'disabled' => !auth()->user()->name || !auth()->user()->family || !auth()->user()->born || !auth()->user()->email
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <script>
        const getCodeUrl = "{{ route('account.get_code') }}",
            currentPhone = "{{ substr(auth()->user()->phone,2) }}",
            errorBornMessage = "{{ trans('validation.wrong_date') }}",
            passwordCannotBeLess = "{{ trans('auth.password_cannot_be_less', ['length' => 6]) }}",
            passwordsMismatch = "{{ trans('auth.password_mismatch') }}";
    </script>
    <script type="text/javascript" src="{{ asset('js/account.js') }}"></script>
@endsection
