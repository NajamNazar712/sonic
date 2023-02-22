@extends('admin.layout.master')

@section('title', 'Scanning History')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Scanning History
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <form id="track_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                                <div class="form-group">
                                    <input type="text" name="tracking_number" class="form-control tracking_number" id="tracking_number" placeholder="Tracking Number*" data-tags-input-name="tracking_number">
                                </div>
                                <div class="ml-1 form-group">
                                    <input type="text" name="seal_number" class="form-control seal_number" id="seal_number" placeholder="Seal Number*" data-tags-input-name="seal_number">
                                </div>

                                <div class="form-group ml-1">
                                    <button type="submit" name="search" class="btn btn-primary" value="search">Search</button>
                                </div>
                            </form>

                            <div class="tracking" id="tracking">
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

    <style>
        .selectize-control {
            width: 300px !important;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="https://kit.fontawesome.com/e7bc565afe.js" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            $('#track_form #tracking_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#track_form #seal_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            function track(tracking_number, type) {
                var search_type_name = '';
                if(type == 1){
                    search_type_name = 'Tracking Number';
                }
                else{
                    search_type_name = 'Seal Number';
                }
                $.ajax({
                    url: '{!! route('admin.scanning_history.details') !!}',
                    method: 'POST',
                    data: {
                        'tracking_number': tracking_number,
                        'search_type': type,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function (data){
                    $('#tracking_number').val('');
                        $('#tracking').html('');
                        console.log(data);
                        if (data.invalid !== undefined) {
                            var message = 'Invalid '+ search_type_name +': ' + data.invalid;
                            scan_sound(2);
                            toastr.error(message, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                        if (data.empty !== undefined) {
                            var message = 'No scanning history found of '+ search_type_name +': ' + data.empty;
                            scan_sound(2);
                            toastr.error(message, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                        if (data.history != undefined) {
                            scan_sound(1);
                            var shipment = '';
                            shipment += '<div class="mt-4 border-primary">';
                            shipment += '<div class="d-flex flex-wrap align-items-center bg-primary">';
                            shipment += '<div class="mb-0 ml-1 mr-1 font-medium-3 white">' + data.tracking_number + '</div>';

                            shipment += '</div>';

                            shipment += '<div class="col-12 mt-2">';
                            shipment += '<h4><u>Scanned Screen Locations</u></h4>';
                            shipment += '<div class="border table-responsive mb-2">';

                            shipment += '<table class="table table-sm table-borderless datatable tracking_history">';
                            shipment += '<thead>';
                            shipment += '<tr role="row">';
                            shipment += '<th><strong>Location Name</strong></th>';
                            shipment += '<th><strong>Account Type</strong></th>';
                            shipment += '<th><strong>Scanned By</strong></th>';
                            shipment += '<th><strong>City</strong></th>';
                            shipment += '<th><strong>Scanned At</strong></th>';
                            shipment += '<th><strong>IP Address</strong></th>';
                            shipment += '<th><strong>Latitude</strong></th>';
                            shipment += '<th><strong>Longitude</strong></th>';
                            shipment += '</tr>';
                            shipment += '</thead>';
                            shipment += '<tbody>';

                            $.each(data.history, function (index, history) {
                                shipment += '<tr>';
                                shipment += '<td>' + history.screen_location + '</td>';
                                shipment += '<td>' + history.account_type + '</td>';
                                shipment += '<td>' + history.scanned_by + '</td>';
                                shipment += '<td>' + history.city + '</td>';
                                shipment += '<td>' + history.scanned_at + '</td>';
                                shipment += '<td>' + history.ip_address + '</td>';
                                shipment += '<td>' + history.latitude + '</td>';
                                shipment += '<td>' + history.longitude + '</td>';
                                shipment += '</tr>';
                            });

                            shipment += '</tbody>';
                            shipment += '</table>';

                            shipment += '</div>';
                            shipment += '</div>';
                            $('#tracking').append(shipment);
                        }
                    });
            }

            $('#track_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parents('form'));
                },
                submitHandler: function (form) {
                    var search_type = 0;
                    var scan_input = '';
                    if($(form).find('.tracking_number').val() != ''){
                        search_type = 1;
                        scan_input = $(form).find('.tracking_number').val();
                        console.log(search_type);
                        track(scan_input, search_type);
                    }
                    else if($(form).find('.seal_number').val() != ''){
                        search_type = 2;
                        scan_input = $(form).find('.seal_number').val();
                        console.log(search_type);
                        track(scan_input, search_type);
                    }
                    else{
                        toastr.error('Tracking Number/Seal Number is required!', 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }

                    return false;
                }
            });
        });
    </script>
@endsection