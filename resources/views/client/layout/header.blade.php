<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta name="description" content="Trax, Sonic">
<meta name="keywords" content="Trax">
<meta name="author" content="Trax IT">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title') - Sonic | Trax</title>
<link rel="apple-touch-icon" sizes="152x152" href="{{ asset('img/apple-touch-icon_new.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon_new-32x32.png') }} ">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon_new-16x16.png') }}">
<link rel="shortcut icon" type="image/x-icon" href="{{  asset('img/favicon_new.ico') }}">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Quicksand:300,400,500,700"
rel="stylesheet">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/line-awesome/css/line-awesome.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/vendors.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/app.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/menu/menu-types/vertical-overlay-menu.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/colors/palette-gradient.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/style.css')}}">
<style>
    .feedback {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
    }
    .feedback .item {
        width: 90px;
        height: 90px;
        display: flex;
        justify-content: center;
        align-items: center;
        user-select: none;
    }
    .feedback .radio {
        display: none;
    }
    .feedback .radio ~ span {
        font-size: 3rem;
        filter: grayscale(100);
        cursor: pointer;
        transition: 0.3s;
    }

    .feedback .radio:checked ~ span {
        filter: grayscale(0);
        font-size: 4rem;
    }
    .feedback .radio:hover ~ span {
        filter: grayscale(0);
        font-size: 4rem;
    }
</style>
@yield('css')

<link rel="stylesheet" type="text/css" href="{{asset('css/custom.css')}}">
