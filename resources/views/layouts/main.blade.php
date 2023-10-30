<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-language" content="{{ app()->getLocale() }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    <title>{{ isset($title) && $title ? $title : trans('content.can_i_help_you') }}</title>
    @if (isset($keywords) && $keywords)
        <meta name="keywords" content="{{ $keywords }}">
    @endif

    @if (isset($description) && $description)
        <meta name="description" content="{{ $description }}">
    @endif

    @include('blocks.favicon_block')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/icons/fontawesome/styles.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/icons/icomoon/styles.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/icons/fontawesome/styles.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/datatables.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.fancybox.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/owl.carousel.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.mCustomScrollbar.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/loader.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}" />

    <script src="https://code.jquery.com/jquery-3.6.3.js" integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://api-maps.yandex.ru/2.1/?apikey=fa455148-7970-4574-b087-4f913652328d&lang=ru_RU" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('js/datatables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery.maskedinput.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery.fancybox.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/fancybox_init.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/owl.carousel.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/max.height.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/main.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/loader.js') }}"></script>
    @if (!auth()->check())
        <script type="text/javascript" src="{{ asset('js/auth.js') }}"></script>
    @endif
</head>

<body style="overflow-y: hidden;">
<div id="loader"><div></div></div>

@if (!auth()->check())
    <x-modal id="login-modal" head="{{ trans('menu.login_or_register') }}">
        <form method="post" action="{{ route('auth.login') }}">
            @csrf
            @include('blocks.input_phone_block')
            @include('blocks.input_block',[
                'name' => 'password',
                'addClass' => 'password',
                'type' => 'password',
                'label' => trans('auth.password'),
                'icon' => 'icon-eye',
                'ajax' => true
            ])
            @include('blocks.button_block',[
                'id' => 'enter-button',
                'primary' => true,
                'buttonType' => 'submit',
                'icon' => 'icon-enter3',
                'buttonText' => trans('auth.enter'),
                'disabled' => true
            ])
            @include('blocks.button_block',[
                'id' => null,
                'primary' => false,
                'addClass' => 'mb-3',
                'dataTarget' => 'register-modal',
                'dataDismiss' => true,
                'icon' => 'icon-user-plus',
                'buttonText' => trans('auth.register')
            ])
            @include('blocks.checkbox_block',[
                'checked' => true,
                'addClass' => 'text-center',
                'name' => 'remember',
                'label' => trans('auth.remember_me'),
                'ajax' => true
            ])
        </form>
        @include('blocks.forgot_password_block')
    </x-modal>

    <x-modal id="register-modal" head="{{ trans('auth.register') }}">
        <form method="post" action="{{ route('auth.register') }}">
            @csrf
            @include('blocks.input_phone_block')
            @include('blocks.input_passwords_block')
            @include('blocks.input_code_block')
            @include('blocks.button_block',[
                'id' => 'get-register-code',
                'disabled' => true,
                'addClass' => 'mb-3',
                'primary' => true,
                'buttonType' => 'submit',
                'icon' => 'icon-key',
                'buttonText' => trans('auth.get_code'),
                'disabled' => true
            ])
            @include('blocks.button_block',[
                'id' => 'register-button',
                'disabled' => true,
                'addClass' => 'mb-3 d-none',
                'primary' => true,
                'buttonType' => 'submit',
                'icon' => 'icon-reset',
                'buttonText' => trans('auth.register'),
                'disabled' => true
            ])
            @include('blocks.checkbox_block',[
                'checked' => false,
                'addClass' => 'text-center',
                'name' => 'i_agree',
                'label' => trans('content.i_agree'),
                'ajax' => true
            ])
            @include('blocks.forgot_password_block')
        </form>
        @include('blocks.get_code_again_block')
    </x-modal>

    <x-modal id="restore-password-modal" head="{{ trans('auth.reset_password') }}">
        <form method="post" action="{{ route('auth.reset_password') }}">
            @csrf
            @include('blocks.input_phone_block')
            @include('blocks.button_block',[
                'id' => 'reset-button',
                'primary' => true,
                'buttonType' => 'submit',
                'icon' => 'icon-user-plus',
                'buttonText' => trans('auth.restore_password'),
                'disabled' => true
            ])
            @include('blocks.button_block',[
                'id' => null,
                'primary' => false,
                'addClass' => 'mb-3',
                'dataTarget' => 'register-modal',
                'dataDismiss' => true,
                'icon' => 'icon-enter3',
                'buttonText' => trans('auth.enter')
            ])
        </form>
    </x-modal>
@endif

<div class="container">
    <div id="main-container">
        <div id="top-line" class="w-100 d-flex align-items-center justify-content-between">
            <a class="d-none d-lg-block" href="{{ route('home') }}">
                <div class="logo-block d-none d-lg-flex align-items-center justify-content-between">
                    @include('blocks.logo_block')
                    <img class="logo-text" src="{{ asset('images/logo_text.svg') }}" />
                </div>
            </a>
            @include('blocks.main_nav_block', [
                'id' => 'main-nav',
                'useHome' => false,
                'nlAddClass' => 'brands'
            ])

            <div class="d-block d-lg-none">
                <a href="{{ route('home') }}">
                    @include('blocks.logo_block')
                </a>
            </div>
            <div class="d-block d-lg-none">
                <a id="account-href" {{ !auth()->check() ? 'class=d-none' : '' }} href="{{ route('account.change') }}">@include('blocks.account_icon_block')</a>
                @if (!auth()->check())
                    <a id="login-href" href="#" {{ auth()->check() ? 'class=d-none' : '' }} data-bs-toggle="modal" data-bs-target="#login-modal">@include('blocks.account_icon_block')</a>
                @endif
            </div>

            <div id="right-button-block" class="buttons-block d-none d-lg-flex align-items-center justify-content-{{ !auth()->check() ? 'end' : 'between' }}" {{ request()->path() == '/' ? 'style=width:250px;' : '' }}>
                <a class="nav-link dropdown-toggle {{ !auth()->check() ? 'd-none' : '' }}" id="navbar-dropdown-messages" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-bell-o"></i>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbar-dropdown-messages">
                    <ul id="dropdown"></ul>
                </div>
                @if (auth()->check() && request()->path() != '/')
                    <a href="{{ route('order.new_order') }}">
                        @include('blocks.button_block',[
                            'primary' => false,
                            'icon' => 'icon-magazine',
                            'buttonText' => trans('content.home_head3')
                        ])
                    </a>
                @endif
                <a href="{{ route('account.change') }}">
                    @include('blocks.button_block',[
                        'id' => 'account-button',
                        'addClass' => !auth()->check() ? 'd-none' : '',
                        'primary' => false,
                        'icon' => 'icon-user-lock',
                        'buttonText' => trans('menu.account')
                    ])
                </a>
                @if (!auth()->check())
                    @include('blocks.button_block',[
                    'id' => 'login-button',
                    'addAttr' => ['style' => 'width:200px'],
                    'addClass' => auth()->check() ? 'd-none' : '',
                    'primary' => false,
                    'dataTarget' => 'login-modal',
                    'icon' => 'icon-enter3',
                    'buttonText' => trans('menu.login_or_register')
                ])
                @endif
            </div>
        </div>
        @yield('content')
    </div>
</div>

<x-modal id="message-modal" head="{{ trans('content.message') }}">
    <h4 class="text-center p-4">{{ session()->has('message') ? session()->get('message') : '' }}</h4>
</x-modal>

@if (!auth()->check())
    <script>
        {{--window.getPointsURL = "{{ route('get_points') }}";--}}
        const generateCodeUrl = "{{ route('auth.generate_code') }}",
            accountUrl = "{{ route('account.change') }}",
            passwordsMismatch = "{{ trans('auth.password_mismatch') }}",
            passwordCannotBeLess = "{{ trans('auth.password_cannot_be_less', ['length' => 6]) }}",
            youMustConsent = "{{ trans('auth.you_must_consent_to_the_processing_of_personal_data') }}";
    </script>
@else
    <script>getSubscriptionsNews("{{ route('order.get_subscriptions_news') }}","{{ route('order.orders') }}","{{ trans('content.new_order_from') }}");</script>
@endif
<script>
    const openMessageModalFlag = parseInt("{{ session()->has('message') }}"),
        errorFieldMustBeFilledIn = "{{ trans('validation.field_must_be_filled_in') }}",
        errorSelectOneOfItems = "{{ trans('validation.you_must_select_one_of_the_items') }}",
        errorWrongValue = "{{ trans('validation.wrong_value') }}";
</script>
</body>
</html>
