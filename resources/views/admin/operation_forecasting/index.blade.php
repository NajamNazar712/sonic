
@extends('admin.layout.master')
@section('title','Operation Incoming Forecasting Shipments')

@section('content')
    <h1 class="mb-1">
        Operation Incoming Forecasting Shipments List
    </h1>
    <div class="row justify-content-center">
        <div class="col-4">
            <div class="card">
                <div class="card-content" aria-expanded="true">
                    <div class="card-body">
                        {{--<div class="text-center">--}}
                            {{--<h4>{{$status}}</h4>--}}
                        {{--</div>--}}
                        <div id="shipments_link" class="text-center">
                            @foreach($shipments as $shipment)
                                <div>
                                    <u><a href='{{route('admin.tracking.index')}}?tracking_number={{$shipment->tracking_number}}' class='tracking' target='_blank'>{{$shipment->tracking_number}}</a></u>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

        });
    </script>
@endsection