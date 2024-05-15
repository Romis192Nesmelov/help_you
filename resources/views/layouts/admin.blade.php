<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Вам помочь. {{ trans('admin.admin_page').'. '.trans('admin_menu.'.end($breadcrumbs)['key']) }}</title>
    @include('blocks.favicon_block')
    <!-- Global stylesheets -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <script src="https://code.jquery.com/jquery-3.6.3.js" integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery.maskedinput@1.4.1/src/jquery.maskedinput.min.js" type="text/javascript"></script>
    @vite([
        'resources/css/icons/fontawesome/styles.min.css',
        'resources/css/icons/icomoon/styles.css',
        'resources/css/admin/bootstrap.css',
        'resources/css/admin/bootstrap-switch.css',
        'resources/css/admin/bootstrap-toggle.min.css',
        'resources/css/admin/core.css',
        'resources/css/admin/components.css',
        'resources/css/admin/colors.css',
        'resources/css/jquery.fancybox.min.css',
        'resources/css/admin/admin.css',
        'resources/js/admin/jquery.min.js',
        'resources/js/admin/bootstrap.min.js',
        'resources/js/admin/styling/uniform.min.js',
        'resources/js/admin/styling/switchery.min.js',
        'resources/js/admin/styling/bootstrap-switch.js',
        'resources/js/admin/styling/bootstrap-toggle.min.js',
        'resources/js/jquery.fancybox.min.js',
        'resources/js/admin/app.js',
        'resources/js/admin/admin.js',
    ])
</head>

<body>
@csrf
<!-- Main navbar -->
<div class="navbar navbar-inverse">
    <div class="navbar-header">
        <ul class="nav navbar-nav visible-xs-block">
            <li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
            <li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
        </ul>
    </div>

    <div class="navbar-collapse collapse" id="navbar-mobile">
        <ul class="nav navbar-nav">
            <li><a class="sidebar-control sidebar-main-toggle hidden-xs"><i class="icon-paragraph-justify3"></i></a></li>
        </ul>

        <ul class="nav navbar-nav navbar-right">
            @include('admin.blocks.dropdown_menu_item_block',[
                'menuName' => auth()->user()->email,
                'menu' => [['href' => route('auth.logout'), 'icon' => 'icon-switch2', 'text' => trans('admin.exit')]]
            ])
        </ul>

    </div>
</div>
<!-- /main navbar -->

<!-- Page container -->
<div class="page-container">

<!-- Page content -->
<div class="page-content">

<!-- Main sidebar -->
<div class="sidebar sidebar-main">
    <div class="sidebar-content">
        <!-- User menu -->
        <div class="sidebar-user">
            <div class="category-content">
                <div class="media">
                    <div class="media-body">
                        <div class="text-size-mini text-muted">
                            <i class="glyphicon glyphicon-user text-size-small"></i>
                            {{ trans('admin.welcome') }}<br>{{ auth()->user()->email }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /user menu -->

        <!-- Main navigation -->
        <div class="sidebar-category sidebar-category-visible">
            <div class="category-content no-padding">
                <ul class="navigation navigation-main navigation-accordion">
                    @foreach ($menu as $k => $item)
                        @if (!$item['hidden'])
                            <li {{ ($item['key'] == $menu_key) || (isset($parent_key) && $item['key'] == $parent_key) ? 'class=active' : '' }}>
                                <a href="{{ route('admin.'.$item['key']) }}"><i class="{{ $item['icon'] }}"></i> <span>{{ trans('admin_menu.'.$item['key']) }}</span></a>
                                @if (isset($submenu) && ($item['key'] == $menu_key || $item['key'] == $parent_key))
                                    <ul>
                                        @foreach ($submenu as $subItem)
                                            <li {{ (request('id') && $subItem['id'] == request('id')) || (request('parent_id') && $subItem['id'] == request('parent_id')) || (isset($current_sub_item) && $subItem['id'] == $current_sub_item) ? 'class=active' : '' }}>
                                                <a href="{{ route('admin.'.$parent_key,['id' => $subItem['id']]) }}">{{ ($subItem['name'] ?? $subItem['head']) }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
        <!-- /main navigation -->
    </div>
</div>
<!-- /main sidebar -->

<!-- Main content -->
<div class="content-wrapper">
    <!-- Page header -->
    <div class="page-header page-header-default">
        <div class="page-header-content">
            <div class="page-title">
                <h4>
                    @foreach ($breadcrumbs as $breadcrumb)
                        @if ($loop->first)
                            <i class="icon-home2"></i>
                            <span class="text-semibold">
                                  @include('admin.blocks.breadcrumb_name_block')
                            </span>
                        @else
                            @include('admin.blocks.breadcrumb_name_block')
                        @endif
                        @if (!$loop->last)
                            <i class="icon-arrow-right7"></i>
                        @endif
                    @endforeach
                 </h4>
            </div>
        </div>

        <div class="breadcrumb-line">
            <ul class="breadcrumb">
                @foreach ($breadcrumbs as $breadcrumb)
                    <li>
                        <a href="{{ isset($breadcrumb['params']) ? route('admin.'.$breadcrumb['key'],$breadcrumb['params']) : route('admin.'.$breadcrumb['key']) }}">
                            @include('admin.blocks.breadcrumb_name_block')
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <!-- /page header -->

    <!-- Content area -->
    <div id="app" class="content">@yield('content')</div>
    <!-- /content area -->

</div>
<!-- /main content -->

</div>
<!-- /page content -->

</div>
<!-- /page container -->
@include('admin.blocks.message_modal_block')
</body>
</html>
