<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="refresh" content="15">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Trax Shippers Dashboard">
    <meta name="keywords" content="Trax logistics,trax,trax logistics,delivery">
    <meta name="author" content="Trax IT">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Maintenance Underway - Sonic | Trax</title>
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('img/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon-32x32.png') }} ">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon-16x16.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{  asset('img/favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Quicksand:300,400,500,700"
    rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/vendors.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/app.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/custom.css')}}">
</head>
<body class="vertical-layout vertical-overlay-menu 2-columns menu-expanded fixed-navbar"
data-open="click" data-menu="vertical-overlay-menu" data-col="2-columns">
  <nav class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-without-dd-arrow fixed-top navbar-dark bg-primary navbar-shadow navbar-brand-center">
    <div class="navbar-wrapper">
      <div class="navbar-header" style="top: 0;">
        <ul class="nav navbar-nav flex-row">
          <li class="nav-item">
            <a class="navbar-brand" href="#">
                <img class="brand-logo sonic" alt="Sonic" src="{{ asset('img/sonic_logo_white.png') }}">
                <img class="brand-logo trax" alt="Trax" src="{{ asset('img/trax_logo_white.png') }}">
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="app-content content">
    <div class="content-wrapper">
      <div class="content-body">

        <div class="app-content content">
            <div class="content-wrapper">
                <div class="content-body">
                    <div class="card">
                        <div class="card-content" aria-expanded="true">
                            <div class="card-body text-center">
                                <h1>Maintenance Underway</h1>

                                <h2>Please Wait...</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

      </div>
    </div>
  </div>

  <footer class="footer footer-static footer-light navbar-border navbar-shadow">
    <p class="clearfix blue-grey lighten-2 text-sm-center mb-0 px-2">
    <span class="float-md-left d-block d-md-inline-block">Copyright &copy; 2018 By <a class="text-bold-800 grey darken-2" href="#">Trax Logistics </a>, All Rights Reserved. </span>
    <span class="float-md-right d-block d-md-inline-blockd-none d-lg-block">Made with <i class="ft-heart pink"></i></span>
    </p>
  </footer>

</body>
</html>