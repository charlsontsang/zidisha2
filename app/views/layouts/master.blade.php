<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="shortcut icon" href="favicon.ico">

        <title>Zidisha | @yield('page-title')</title>


        <!-- Bootstrap core CSS -->
        <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet">

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

        <!-- Mixpanel -->
        {{ \Zidisha\Vendor\Mixpanel::headScript() }}
    </head>
    <body>

        @include('partials.header')
        
        @yield('content-top')

        <div class="container">
            @yield('content')
        </div>

        @yield('content-bottom')

        @include('partials.footer')

        @if(!\Auth::check())
            @include('partials.login-modal')
            @include('partials.join-modal')
        @endif
        
        <script src="{{ asset('assets/vendor/jquery/dist/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/bootstrap-3-datepicker/js/bootstrap-datepicker.js') }}"></script>
        <script src="{{ asset('assets/js/zidisha.js') }}"></script>
        <script src="{{ asset('assets/vendor/remarkable-bootstrap-growl/bootstrap-growl.min.js') }}"></script>
        <script>
            $(function() {
                $('.flash-modal').modal();
            });
        </script>
        @yield('script-footer')
        @include('partials/_flash')
        <!-- Mixpanel -->
        {{ \Zidisha\Vendor\Mixpanel::bodyScript() }}
    </body>
</html>
