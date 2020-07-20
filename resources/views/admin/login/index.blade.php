<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>夜场ERP管理系统 | 登录</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.0.2/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Ionicons -->
{{--    <link rel="stylesheet" href="/ionicons/ionicons.min.css">--}}
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.0.2/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.0.2/dist/css/adminlte.min.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link href="{{ asset('googleapis/source-sans-pro.css') }}" rel="stylesheet">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="javascript: void (0);"><b>夜场ERP</b>管理系统</a>
    </div>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">欢迎使用夜场ERP管理系统</p>

            @if ($errors->has('name'))
                <div class="alert alert-danger text-center" role="alert">
                    用户不存在或密码错误
                </div>
            @endif

            <form action="{{ route('admin.login.login') }}" method="post">

                {{ csrf_field() }}

                <div class="input-group mb-3">
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="账号">

                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>


                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="密码">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" name="remember" id="remember">
                            <label for="remember">
                                记住我
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">登录</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

            {{--<div class="social-auth-links text-center mb-3">--}}
            {{--<p>- OR -</p>--}}
            {{--<a href="#" class="btn btn-block btn-primary">--}}
            {{--<i class="fab fa-facebook mr-2"></i> Sign in using Facebook--}}
            {{--</a>--}}
            {{--<a href="#" class="btn btn-block btn-danger">--}}
            {{--<i class="fab fa-google-plus mr-2"></i> Sign in using Google+--}}
            {{--</a>--}}
            {{--</div>--}}
        <!-- /.social-auth-links -->

            {{--<p class="mb-1">--}}
            {{--<a href="forgot-password.html">I forgot my password</a>--}}
            {{--</p>--}}
            {{--<p class="mb-0">--}}
            {{--<a href="register.html" class="text-center">Register a new membership</a>--}}
            {{--</p>--}}
        </div>
        <!-- /.login-card-body -->
    </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="{{ asset('AdminLTE-3.0.2/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('AdminLTE-3.0.2/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('AdminLTE-3.0.2/dist/js/adminlte.min.js') }}"></script>

</body>
</html>
