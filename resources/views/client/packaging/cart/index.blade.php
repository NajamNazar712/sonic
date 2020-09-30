@extends('client.layout.master')

@section('title', 'Packaging Material Cart')

@section('content')
    <section>
        <div class="app-content content">
            <div class="content-wrapper">
                <div class="content-header row">
                </div>
                <div class="content-body">
                    <div class="card">
                        <div class="card-content" aria-expanded="true">
                            <div class="card-body">
                                @include('client.inc.messages')
                                @foreach($packaging_types as $index => $packaging_type)
                                    <div class="col">
                                        <h1 class="mb-1">
                                            {{$packaging_type->type}}
                                        </h1>
                                        <div class="row">
                                            @foreach($packaging_type->sizes as $index => $size)
                                                @php
                                                    $row = $index + 1;
                                                @endphp
                                            <div class="col-2 m-1">
                                                <div class="col mb-1 text-center">
                                                    <img class="" alt="flyer" src="{{ asset('img/logo.png') }}">
                                                </div>
                                                <div class="col mb-1 text-center">
                                                    <p><b>{{$size->size}}</b></p>
                                                </div>
                                                <div class="col mb-1">
                                                    <p><b>Flyer</b> {{$row}}<p>
                                                </div>
                                                <div class="col mb-1">
                                                    <button class="btn btn-primary">Add to cart</button>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <hr>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">


@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>



    <script type="text/javascript">
        $('document').ready(function(){

        });
    </script>

@endsection