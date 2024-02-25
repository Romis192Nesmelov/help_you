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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.3.js" integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://api-maps.yandex.ru/2.1/?apikey=fa455148-7970-4574-b087-4f913652328d&lang=ru_RU" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery.maskedinput@1.4.1/src/jquery.maskedinput.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.js"></script>

    @vite([
        'resources/js/interactions.min.js',
        'resources/js/touch.min.js',
        'resources/js/widgets.min.js',
        'resources/js/jquery.fancybox.min.js',
        'resources/js/owl.carousel.min.js',
        'resources/js/app.js',
        'resources/css/icons/fontawesome/styles.min.css',
        'resources/css/icons/icomoon/styles.css',
        'resources/css/jquery.fancybox.min.css',
        'resources/css/owl.carousel.min.css',
        'resources/css/loader.css',
        'resources/css/app.css',
    ])
</head>

<body style="overflow-y: hidden;">

<script>window.orderStatuses = [];</script>
@for ($i=0;$i<3;$i++)
    <script>window.orderStatuses.push("{{ trans('content.status_'.$i) }}");</script>
@endfor
<script>window.orderStatuses.push("{{ trans('content.in_approve') }}");</script>

<div id="app" class="container">
    @csrf
    <div id="main-container">
        <top-line-component
            auth="{{ auth()->check() }}"
            user_id="{{ auth()->check() ? auth()->id() : 0 }}"
            on_root="{{ request()->path() == '/' ? 1 : 0 }}"
            home_url="{{ route('home') }}"
            login_url="{{ route('auth.login') }}"
            register_url="{{ route('auth.register') }}"
            get_code_url="{{ route('auth.generate_code') }}"
            reset_pass_url="{{ route('auth.reset_password') }}"
            logo_image="{{ asset('images/logo.svg') }}"
            logo_text_image="{{ asset('images/logo_text.svg') }}"
            main_menu="{{ json_encode($mainMenu) }}"
            account_icon="{{ asset('images/account.svg') }}"
            new_order_url="{{ route('order.edit_order') }}"
            account_change_url="{{ route('account.change') }}"
            messages_url="{{ route('account.messages') }}"
            orders_url="{{ route('order.orders') }}"
            active_main_menu="{{ $activeMainMenu }}"
            get_unread_messages_url="{{ route('messages.get_unread_messages') }}"
            chat_url = "{{ route('messages.chat') }}"
            my_orders_url = "{{ route('account.my_orders') }}"
            my_help_url = "{{ route('account.my_help') }}"
            get_orders_new_url="{{ route('order.get_orders_news') }}"
            order_statuses="{{ json_encode([trans('content.status_0'),trans('content.status_1'),trans('content.status_2'),trans('content.status_3')]) }}"
        ></top-line-component>
        @yield('content')
    </div>
    <message-component></message-component>
</div>

<div id="loader"><div></div></div>

</body>
</html>
