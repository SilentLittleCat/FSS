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

    <link rel="stylesheet" href="{{ asset('vendor/admin-lte/plugins/select2/select2.min.css') }}">

    <link rel="stylesheet" href="{{ asset('vendor/admin-lte/plugins/colorpicker/bootstrap-colorpicker.min.css') }}">
    
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
</head>
<body class="hold-transition skin-blue sidebar-collapse">

<div class="wrapper">

    <div class="content-wrapper" style="background-color: #e4e7ea">
        <div class="container auth-content" id="loginContent" style="padding-top: 30px;">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div class="row" style="color: #636e7b">
                        <div class="col-md-7">
                            <h3 style="font-size: 2.1em; color: black">
                                <span style="color: #1caf9a"><b>[</b></span>
                                {{ __('messages.fss') }}
                                <span style="color: #1caf9a"><b>]</b></span>
                            </h3>
                            <h5><b>明日歌</b></h5>
                            <p style="margin-top: 50px;">
                                明日复明日， 明日何其多？ 我生待明日， 万事成蹉跎。 世人若被明日累， 春去秋来老将至。 朝看水东流， 暮看日西坠。 百年明日能几何？ 请君听我明日歌！
                            </p>
                        </div>
                        <div class="col-md-5">
                            <div class="panel panel-default" style="background-color: #eaecee; padding: 20px 10px; color: #636e7b">
                                <div class="panel-body">
                                    <h4>
                                        {{ __('messages.login') }}
                                    </h4>
                                    <form class="form-horizontal" role="form" method="POST" action="{{ route('login') }}">
                                        {{ csrf_field() }}
                                        <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                                            <div class="col-md-12">
                                                <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" required autofocus placeholder="用户名">
                                                @if ($errors->has('username'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('username') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                            <div class="col-md-12">
                                                <input id="password" type="password" class="form-control" name="password" required placeholder="密码">     

                                                @if ($errors->has('password'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('password') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>{{ __('messages.remember-me') }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-default btn-block" style="background-color: #1caf9a; color: white; border-color: #1caf9a;">
                                                    {{ __('messages.login') }}
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 col-md-offset-2" style="color: #636e7b; margin-top: -80px;">
                    <footer style="border-top: 1px solid #dddddd; padding-top: 8px; font-size: 0.9em;">
                        <div class="pull-right hidden-xs">
                            Created By: <a href="http://9026.com/"> Think Different</a>
                        </div>
                        &copy; 2017. All Rights Reserved.
                    </footer>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Scripts -->
    <!-- jQuery 2.2.3 -->
    <script src="{{ asset('vendor/admin-lte/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
    <script src="{{ asset('vendor/admin-lte/plugins/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('vendor/admin-lte/plugins/colorpicker/bootstrap-colorpicker.min.js') }}"></script>
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

</body>
</html>
