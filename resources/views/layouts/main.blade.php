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
    <link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.fancybox.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/loader.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}" />

    <script src="https://code.jquery.com/jquery-3.6.3.js" integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://api-maps.yandex.ru/2.1/?apikey=fa455148-7970-4574-b087-4f913652328d&lang=ru_RU" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('js/plugins/forms/styling/uniform.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery.maskedinput.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery.fancybox.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/fancybox_init.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/max.height.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/main.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/loader.js') }}"></script>
</head>

<body style="overflow-y: hidden">
<div id="loader"><div></div></div>

{{--<x-modal id="message-modal" head="{{ trans('content.message') }}">--}}
{{--    <h3 class="text-center"></h3>--}}
{{--</x-modal>--}}

<div class="container">
    <div id="main-container">
        <div id="top-line" class="w-100 d-flex align-items-center justify-content-between">
            <a class="d-none d-lg-block" href="{{ route('home') }}">
                <div class="logo-block d-none d-lg-flex align-items-center justify-content-between">
                    @include('blocks.logo_block')
                    <img class="logo-text" src="{{ asset('storage/images/logo_text.svg') }}" />
                </div>
            </a>
            @include('blocks.main_nav_block', [
                'id' => 'main-nav',
                'useHome' => false,
                'nlAddClass' => 'brands'
            ])

            <div class="d-block d-lg-none">
                @include('blocks.logo_block')
            </div>
            <div class="d-block d-lg-none">
                <a href="#"><img class="account-icon" src="{{ asset('storage/images/account.svg') }}" /></a>
            </div>

            <div class="buttons-block d-none d-lg-flex align-items-center justify-content-between">
                <i class="fa fa-bell-o"><span class="dot"></span></i>
                @include('blocks.button_block',[
                    'id' => null,
                    'primary' => false,
                    'icon' => 'icon-enter3',
                    'buttonText' => trans('menu.login_or_register')
                ])
            </div>
        </div>
        @yield('content')
    </div>
</div>

<script>
    window.currentURL = "{{ request()->path() }}";
    window.getPointsURL = "{{ route('get_points') }}";
</script>
</body>
</html>
