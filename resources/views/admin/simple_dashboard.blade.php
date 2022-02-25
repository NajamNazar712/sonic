@extends('admin.layout.master')

@section('title', 'Dashboard')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">

            @include('admin.inc.messages')
            <div class="content-body">
                <div class="card">
                    <div class="card-content text-center">
                        <div class="card-body">
                            <h1 class="">Welcome to Sonic..</h1>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


@endsection
@section('css')

    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/line-awesome/css/line-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/cryptocoins/cryptocoins.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">



@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

        });
    </script>
@endsection