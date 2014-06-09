<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="favicon.ico">

    <title>Zidisha |  @yield('page-title')</title>


    <!-- Bootstrap core CSS -->
    <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

    @include('partials.header')

    @yield('content-top')

    <div class="container">
        @include('partials/_flash')
        @yield('content')
    </div>

    @yield('content-bottom')

    @include('partials.footer')


    <div class="modal fade" id="LoginModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">
                        Log in
                    </h4>
                </div>
                <div class="modal-body">

                    <button class="btn btn-primary btn-lg btn-block btn-icon">
                        <span class="icon-container">
                            <span class="fa fa-facebook fa-lg"></span>
                        </span>
                        <span class="text-container">
                            Log in with Facebook
                        </span>
                    </button>

                    <div class="hr-text">
                        <hr>
                        <span>or</span>
                    </div>

                    <form action="" method="POST">

                        <div class="form-group has-feedback">
                            <div class="sr-only">Email Address</div>
                            <input type="text" class="form-control" name="username" placeholder="Email Address">
                            <span class="fa-lg form-control-feedback text-muted" style="top: 0;">@</span>
                        </div>

                        <div class="form-group has-feedback">
                            <div class="sr-only">Password</div>
                            <input type="text" class="form-control" name="password" placeholder="Password">
                            <span class="fa fa-lock fa-lg form-control-feedback text-muted" style="top: 0;"></span>
                        </div>

                        <div class="row">
                            <div class="col-xs-6">
                                <label class="checkbox" style="margin-top: 0">
                                    <input type="checkbox" value="remember-me">Remember Me
                                </label>
                            </div>
                            <div class="col-xs-6">
                                <p class="pull-right">
                                    <a href="#">Forgot password?</a>
                                </p>
                            </div>
                        </div>
                    </form>

                    <button class="btn btn-lg btn-primary btn-block" type="submit">Log in</button>
                </div>
                <div class="modal-footer" style="text-align: left; margin-top: 0">
                    Not a member? <a href="#">Join</a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="{{ asset('assets/vendor/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script>$('.flash-modal').modal();</script>
    </body>
</html>
