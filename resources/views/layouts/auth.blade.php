<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ trans('content.can_i_help_you').' '.trans('admin.enter_admin_panel') }}</title>

    @include('blocks.favicon_block')

    <!-- Global stylesheets -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <script src="https://code.jquery.com/jquery-2.2.4.js" integrity="sha256-iT6Q9iMJYuQiMWNd9lDyBUStIq/8PuOW33aOqmvFpqI=" crossorigin="anonymous"></script>
    @vite([
        'resources/css/admin/bootstrap.css',
        'resources/css/admin/core.css',
        'resources/css/components.css',
        'resources/css/admin/admin.css',
        'resources/js/admin/bootstrap.min.js',
        'resources/js/styling/uniform.min.js',
        'resources/js/admin/app.js',
        'resources/js/admin/login.js',
    ])
</head>

<body class="login-container bg-slate-800">

<!-- Page container -->
<div class="page-container">

    <!-- Page content -->
    <div class="page-content">

        <!-- Main content -->
        <div class="content-wrapper">

            <!-- Content area -->
            <div class="content">
                @yield('content')
            </div>
            <!-- /content area -->

        </div>
        <!-- /main content -->

    </div>
    <!-- /page content -->

</div>
<!-- /page container -->

</body>
</html>
