@php($environment = env('APP_ENV'))
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta name="description" content="Trax, Sonic">
<meta name="keywords" content="Trax">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="author" content="Trax IT">
<title>@yield('title') - Sonic | Trax</title>
<link rel="apple-touch-icon" sizes="152x152" href="{{ asset('img/apple-touch-icon_new.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon_new-32x32.png') }} ">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon_new-16x16.png') }}">
<link rel="shortcut icon" type="image/x-icon" href="{{  asset('img/favicon_new.ico') }}">
@if($environment != 'local')
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Quicksand:300,400,500,700"
      rel="stylesheet">
@else
    <link rel="stylesheet" href="{{asset('app-assets/css/local_font.css')}}">
@endif
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/line-awesome/css/line-awesome.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/vendors.css')}}">
{{--This needs to be moved--}}
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/daterange/daterangepicker.css')}}">

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/toggle/bootstrap-switch.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/toggle/switchery.min.css')}}">
{{--This needs to be moved--}}
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/app.css')}}">
@if($environment != 'local')
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
@else
<script type="text/javascript" src="{{asset('app-assets/js/local.js')}}"></script>
@endif
{{--Alerts--}}
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/menu/menu-types/vertical-overlay-menu.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/colors/palette-gradient.css')}}">
{{--This needs to be moved--}}
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/switch.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/style.css')}}">

@yield('css')

<link rel="stylesheet" type="text/css" href="{{asset('css/custom.css')}}">