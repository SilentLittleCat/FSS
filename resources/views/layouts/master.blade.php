<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/handsontable/dist/handsontable.full.css') }}">

    <!-- Styles -->

    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="{{ asset('vendor/admin-lte/bootstrap/css/bootstrap.min.css') }}">
    
    <link rel="stylesheet" href="{{ asset('vendor/jquery-ui-dist/jquery-ui.min.css') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/font-awesome/css/font-awesome.min.css') }}">

    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('vendor/admin-lte/plugins/iCheck/all.css') }}">

    <!-- Spectrum -->
    <link rel="stylesheet" href="{{ asset('vendor/spectrum-colorpicker/spectrum.css') }}">

    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('vendor/admin-lte/plugins/select2/select2.min.css') }}">

    <!-- Colorpicker -->
    <link rel="stylesheet" href="{{ asset('vendor/admin-lte/plugins/colorpicker/bootstrap-colorpicker.min.css') }}">
    
    <!-- Datatables -->
    <link rel="stylesheet" href="{{ asset('vendor/admin-lte/plugins/datatables/dataTables.bootstrap.css') }}">

    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('vendor/admin-lte/dist/css/AdminLTE.min.css') }}">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('vendor/admin-lte/dist/css/skins/_all-skins.min.css') }}">

    <link rel="stylesheet" href="{{ asset('vendor/admin-lte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}">

    <link rel="stylesheet" href="{{ asset('vendor/fine-uploader/fine-uploader-new.min.css') }}">

    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    @yield('style')
</head>
<body class="hold-transition skin-blue sidebar-mini">

<div class="wrapper">

    <header class="main-header">
        <a href="{{ route('index') }}" class="logo">
            <span class="logo-mini"><b>F</b>SS</span>
            <span class="logo-lg"><b>FileShare</b>Sys</span>
        </a>

        <nav class="navbar navbar-static-top" role="navigation">
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    @if(Auth::check())
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="{{ Auth::user()->image ?: asset(env('DEFAULT_USER_IMAGE')) }}" class="user-image" alt="User Image">
                                <span class="hidden-xs">{{ Auth::user()->username }}</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="user-header">
                                    <img src="{{ Auth::user()->image ?: asset(env('DEFAULT_USER_IMAGE')) }}">
                                    <p>{{ Auth::user()->username }}</p>
                                </li>
                                <li class="user-footer">
                                    <div class="pull-right">
                                        <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="btn btn-default btn-flat">
                                            {{ __('messages.logout') }}
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </nav>
    </header>

    @if(Auth::check())
    <aside class="main-sidebar control-sidebar-open">
        <section class="sidebar">
            <div class="user-panel">
                @if(Auth::check())
                    <div class="pull-left image">
                        <img src="{{ Auth::user()->image ?: asset(env('DEFAULT_USER_IMAGE')) }}" class="img-circle" alt="User Image">
                    </div>
                    <div class="pull-left info">
                        <p>{{ Auth::user()->username }}</p>
                        <a href="#"><i class="fa fa-circle text-success"></i>{{ __('messages.online') }}</a>
                    </div>
                @else
                    <div class="pull-left image">
                        <img src="{{ asset(env('DEFAULT_USER_NOLOGIN_IMAGE')) }}" class="img-circle" alt="User Image">
                    </div>
                    <div class="pull-left info">
                        <p></p>
                        <a href="#"><i class="fa fa-circle text-danger"></i>{{ __('messages.no-login') }}</a>
                    </div>
                @endif
            </div>

            <ul class="sidebar-menu">
                <li class="header">{{ __('messages.navigation') }}</li>
                <li>
                    <a href="{{ route('home') }}">
                        <i class="fa fa-home"></i>
                        <span>{{ __('messages.home') }}</span>
                    </a>
                </li>
                <li class="treeview">
                    <a href="">
                        <i class="fa fa-table"></i>
                        <span>{{ __('messages.checklist-self') }}</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a href="{{ route('checklists.index') }}">
                                <i class="fa fa-circle-o"></i>
                                {{ __('messages.checklist.brower') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('checklists.template.create') }}">
                                <i class="fa fa-circle-o"></i>
                                {{ __('messages.checklist.create-template') }}
                            </a>
                        </li>
                    </ul>
                </li>
                @if(Auth::check() && Auth::user()->canManageProject())
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-dashboard"></i>
                            <span>{{ __('messages.project.manage') }}</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li>
                                <a href="{{ route('projects.index') }}">
                                    <i class="fa fa-circle-o"></i>
                                    {{ __('messages.project.brower') }}
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
            </ul>
        </section>
    </aside>
    @endif
    <div class="content-wrapper">
        @yield('content')
    </div>

    <footer class="main-footer" style="text-align: center;">
        <strong>Copyright &copy; 2017 <a href="http://9026.com/">{{ __('messages.company') }}</a>.</strong> All rights reserved.
    </footer>
</div>

    <!-- Scripts -->
    <!-- jQuery 2.2.3 -->
    <script src="{{ asset('vendor/admin-lte/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>

    <!-- Select2 -->
    <script src="{{ asset('vendor/admin-lte/plugins/select2/select2.full.min.js') }}"></script>

    <!-- iCheck -->
    <script src="{{ asset('vendor/admin-lte/plugins/iCheck/icheck.min.js') }}"></script>

    <!-- Spectrum -->
    <script src="{{ asset('vendor/spectrum-colorpicker/spectrum.js') }}"></script>

    <!-- Colorpicker -->
    <script src="{{ asset('vendor/admin-lte/plugins/colorpicker/bootstrap-colorpicker.min.js') }}"></script>

    <!-- Datatables -->
    <script src="{{ asset('vendor/admin-lte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/admin-lte/plugins/datatables/dataTables.bootstrap.js') }}"></script>

    <!-- jQuery UI 1.11.4 -->
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>

    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button);
    </script>

    <!-- Bootstrap 3.3.6 -->
    <script src="{{ asset('vendor/admin-lte/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendor/admin-lte/dist/js/app.min.js') }}"></script>
    <script src="{{ asset('vendor/fine-uploader/fine-uploader.min.js') }}"></script>
    <script src="{{ asset('vendor/handsontable/dist/handsontable.full.zh.js') }}"></script>

    @yield('script')
</body>
</html>
