<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Modern admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities with bitcoin dashboard.">
    <meta name="keywords" content="admin template, modern admin template, dashboard template, flat admin template, responsive admin template, web app, crypto dashboard, bitcoin dashboard">
    <meta name="author" content="Trax IT">
    <title>Reset Password - Sonic | Trax</title>
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('img/apple-touch-icon_new.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon_new-32x32.png') }} ">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon_new-16x16.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{  asset('img/favicon_new.ico') }}">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Quicksand:300,400,500,700"
          rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/line-awesome/css/line-awesome.min.css')}}">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/vendors.css')}}">
    <!-- END VENDOR CSS-->
    <!-- BEGIN MODERN CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/app.css')}}">
    <!-- END MODERN CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/menu/menu-types/vertical-overlay-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/colors/palette-gradient.css')}}">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/style.css')}}">
    <!-- END Custom CSS-->

    <link rel="stylesheet" type="text/css" href="{{asset('css/login.css')}}?v=2.2">
</head>
<body class="vertical-layout vertical-overlay-menu 1-column bg-full-screen-image menu-expanded blank-page blank-page"
      data-open="click" data-menu="vertical-overlay-menu" data-col="1-column">
<!-- ////////////////////////////////////////////////////////////////////////////-->
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <section class="flexbox-container" style="overflow: auto;">
                <div class="col-12 d-flex align-items-center justify-content-center">
                    <div class="col-md-4 col-10 box-shadow-2 p-0">
                        <div class="card border-grey border-lighten-3 px-2 py-2 m-0">
                            <div class="card-header border-0 pb-0">
                                <div class="card-title text-center">
                                    <div class="row align-items-center">
                                        <div class="col sonic_logo align-middle text-left">
                                            <img src="{{asset('img/sonic_logo_new.png')}}" alt="Sonic" class="d-inline-block mx-auto w-75">
                                        </div>

                                        <div class="col trax_logo align-middle text-right">
                                            <img src="{{asset('img/trax_logo_new.png')}}" alt="Trax" class="d-inline-block mx-auto w-75">
                                        </div>
                                    </div>
                                </div>
                                <h6 class="card-subtitle line-on-side text-muted text-center font-small-3 pt-2">
                                    <span>We will send you a link to reset password</span>
                                </h6>
                            </div>
                            <div class="card-content">
                                <div class="card-body">

                                    <form class="form-horizontal" method="post" action="{{ route('password.request') }}">
                                        @csrf
                                        <input type="hidden" name="token" value="{{ $token }}">
                                        <fieldset class="form-group position-relative has-icon-left">
                                            <input type="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" name="email" id="user-email"
                                                   placeholder="Email Address" required autofocus>
                                            <div class="form-control-position">
                                                <i class="ft-mail"></i>
                                            </div>
                                            @if ($errors->has('email'))
                                                <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                            @endif
                                        </fieldset>
                                        <fieldset class="form-group position-relative has-icon-left">
                                            <input type="password" name="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" id="password" placeholder="Enter New Password"
                                                   required>
                                            <div class="form-control-position">
                                                <i class="la la-key"></i>
                                            </div>
                                            @if ($errors->has('password'))
                                                <span class="invalid-feedback">
                                            <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                            @endif
                                        </fieldset>
                                        <fieldset class="form-group position-relative has-icon-left">
                                            <input type="password" id="password-confirm" name="password_confirmation" class="form-control {{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" placeholder="Confirm Password"
                                                   required>
                                            <div class="form-control-position">
                                                <i class="la la-key"></i>
                                            </div>
                                            @if ($errors->has('password_confirmation'))
                                                <span class="invalid-feedback">
                                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                                    </span>
                                            @endif
                                        </fieldset>
                                        <button type="submit" class="btn btn-outline-info btn-block"><i class="ft-unlock"></i> Reset Password</button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<!-- ////////////////////////////////////////////////////////////////////////////-->
<!-- BEGIN VENDOR JS-->
<script src="{{asset('app-assets/vendors/js/vendors.min.js')}}" type="text/javascript"></script>
<!-- BEGIN VENDOR JS-->
<!-- BEGIN PAGE VENDOR JS-->
<script src="{{asset('app-assets/vendors/js/forms/validation/jqBootstrapValidation.js')}}"
        type="text/javascript"></script>
<!-- END PAGE VENDOR JS-->
<!-- BEGIN MODERN JS-->
<script src="{{asset('app-assets/js/core/app-menu.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/js/core/app.js')}}" type="text/javascript"></script>
<!-- END MODERN JS-->
<!-- BEGIN PAGE LEVEL JS-->
<script src="{{asset('app-assets/js/scripts/forms/form-login-register.js')}}" type="text/javascript"></script>
<!-- END PAGE LEVEL JS-->
</body>
</html>