
{{--        {{$payable}}--}}
{{--        {{$adjustment}}--}}
{{--        {{$total_amount}}--}}
{{--        {{$tracking_no}}--}}
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
<style>
    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }
    th{
        color: white;
        background-color: #649bc8;
    }
    td, th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }
</style>
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-body">

            <div class="app-content content">
                <div class="content-wrapper">
                    <div class="content-header row">
                    </div>
                    <div class="content-body">
                        <h1 class="mb-1">
                            Payment Details
                        </h1>

                        <div class="card">
                            <div class="card-content" aria-expanded="true">
                                <div class="card-body">
                                    @include('client.inc.messages')

                                    <table>
                                        <tr>
                                            <th>Tracking Number</th>
                                            <th>Total Amount</th>
                                            <th>Payable</th>
                                            <th>Adjustment Charges</th>

                                        </tr>
                                        <tr>
                                            <td>{{$tracking_no}}</td>
                                            <td>{{$total_amount}}</td>
                                            <td>{{$payable}}</td>
                                            <td>{{$adjustment}}</td>
                                        </tr>
                                    </table>

                                    <div class="tracking" id="tracking">
                                    </div>
                                </div>
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