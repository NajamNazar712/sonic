@extends('client.layout.master')
@section('title','Cancelled Shipments Arrival')

@section('content')
    <h1 class="mb-1">
        Cancelled Shipment Arrival
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('client.inc.messages')
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 offset-4">
                            <label class="font-medium-2 font-weight-bold block">Cancelled Shipment Arrival
                            </label>
                            <div class="form-group">
                                <div class="ml-5">
                                    <label class="font-medium-2 text-bold-600 mr-1">No</label>
                                    <input type="checkbox" name="cancel_arrival_box" value="1" id="cancel_arrival_box"
                                           class="switchery cancel_arrival_box" data-size="sm"
                                           data-switchery="true" {{(!empty($shipper)) ? '' : 'checked' }}>
                                    <label class="font-medium-2 text-bold-600 ml-1">Yes</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

    <link rel="stylesheet" type="text/css"
          href="{{asset('app-assets/vendors/css/forms/toggle/bootstrap-switch.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/toggle/switchery.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/switch.css')}}">

    <style>
        .btn-group .dropdown-menu .dropdown-item {
            white-space: normal;
        }

        #toast-bottom-center.toast-container {
            text-align: center;
        }

        #toast-bottom-center.toast-container .toast {
            display: table;
            width: auto !important;
            text-align: left;
        }

        /*    for toggle*/

        /*    toggle end*/
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>


    <script src="{{asset('app-assets/vendors/js/forms/toggle/bootstrap-switch.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/toggle/bootstrap-checkbox.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/toggle/switchery.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/forms/switch.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            $("#cancel_arrival_box").change(function () {
                // var id = $(this).val();
                if ($("#cancel_arrival_box").is(':checked'))
                    var id = 1;
                else
                    var id = 0;

                if (id == 1 || id == 0) {
                    $.ajax({
                        url: '{!! route('cod.cancelled_shipments_arrival.cancelled_shipments_arrival') !!}',
                        method: 'POST',
                        data: {
                            'id': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if (data.status == 1) {
                            toastr.success(data.success, 'Success!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        } else {
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });
                }
            });


        });
    </script>
@endsection