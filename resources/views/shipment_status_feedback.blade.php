@section('title', 'Shipment Status Feedback')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

    <style>
        .selectize-control {
            width: 300px !important;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script>

    </script>
@endsection

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    @include('client.layout.header')
</head>
<body class="vertical-layout vertical-overlay-menu 2-columns menu-expanded fixed-navbar"
      data-open="click" data-menu="vertical-overlay-menu" data-col="2-columns">
<nav class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-without-dd-arrow fixed-top navbar-dark bg-primary navbar-shadow navbar-brand-center">
    <div class="navbar-wrapper">
        <div class="navbar-header" style="top: 0;">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item">
                    <a class="navbar-brand" href="{{route('cod.dashboard')}}">
                        <img class="brand-logo sonic" alt="Sonic" src="{{ asset('img/sonic_logo_white_new.png') }}">
                        <img class="brand-logo trax" alt="Trax" src="{{ asset('img/trax_logo_white_new.png') }}">
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
                            <div class="card-body text-center">

                                <h2>Thank you for your response.</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@include('client.layout.footer')
</body>
</html>