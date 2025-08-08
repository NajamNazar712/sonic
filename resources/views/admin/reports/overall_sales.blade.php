@extends('admin.layout.master')

@section('title', 'Overall Sales Report')

@section('content')
    <h1 class="mb-1">
        Overall Sales Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div id="search_form" class="row mb-2 justify-content-center">
                    <div class="col-4">
                        <fieldset class="form-group">
                            <input type="text" class="form-control" name="search_tracking_no" id="search_tracking_no" placeholder="Search Tracking Number">
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_shipper" id="search_shipper" class="form-control select2">
                                @foreach($shippers as $shipper)
                                    <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_shippers[]" id="search_shippers" class="form-control select2" multiple="multiple" required data-rule-required="true" data-msg-required="This field is required">
                                @foreach($shippers as $shipper)
                                    <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    @if (session('role_id') == 1 || in_array(261, session('permissions')))
                    <div class="col-4">
                        <div class="form-group">
                            <select name="search_sales_person" class="select2" id="sales_person_select">
                                @foreach($sales_persons as $sales)
                                    <option value="{{ $sales->id }}">{{ $sales->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                        @else
                        <input type="hidden" name="search_sales_person" value="null">
                    @endif
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_origin" id="search_origin" class="form-control select2">
                                @foreach($cities as $origin)
                                    <option value="{{$origin->id}}">{{$origin->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_destination" id="search_destination" class="form-control select2">
                                @foreach($cities as $destination)
                                    <option value="{{$destination->id}}">{{$destination->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>

                    
                    <div class="col-3 mb-1">
                        <fieldset class="form-group">
                            <select name="search_origin_hub" id="search_origin_hub" class="form-control select2">
                                @foreach($hubs as $hub)
                                    <option value="{{$hub->id}}">{{$hub->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>

                    <div class="col-3 mb-1">
                        <fieldset class="form-group">
                            <select name="search_origin_zone" id="search_origin_zone" class="form-control select2">
                                @foreach($zones as $zone)
                                    <option value="{{$zone->id}}">{{$zone->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
         
                    <div class="col-3">
                        <fieldset class="form-group">
                            <select name="search_hub" id="search_hub" class="form-control select2">
                                @foreach($hubs as $hub)
                                    <option value="{{$hub->id}}">{{$hub->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class="form-group">
                            <select name="search_status" id="search_status" class="form-control select2">
                                @foreach($statuses as $status)
                                    <option value="{{$status->id}}">{{$status->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class="form-group">
                            <select name="search_business_category" id="search_business_category" class="form-control select2">
                                @foreach($business_categories as $bc)
                                    <option value="{{$bc->id}}">{{$bc->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>

                    <div class="col-3">
                        <div class="form-group">
                            <select name="search_shipping_mode" id="search_shipping_mode" class="form-control select2" data-rule-required="true" data-msg-required="Shipping Mode is required">
                                @foreach($shipping_modes as $shipping_mode)
                                    <option value="{{$shipping_mode->id}}">{{$shipping_mode->mode}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="form-group">
                            <select name="sub_segment_select" id="sub_segment_select" class="select2">
                                @foreach($sub_segments as $sub_segment)
                                    <option value="{{$sub_segment->id}}">{{$sub_segment->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-3">
                        <select name="service_type_select" id="service_type_select" class="select2">
                            @foreach($service_types as $service_type)
                                <option value="{{$service_type->id}}">{{$service_type->booking_type}}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col-3">
                        <div class="form-group">
                            <select name="ref_name_select" id="ref_name_select" class="select2">
                                @foreach($referral_names as $referral_name)
                                    <option value="{{$referral_name->id}}">{{$referral_name->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    
                    <div class="col-3">
                        <div class="form-group">
                            <select name="rider_types_referral" id="rider_types_referral" class="select2">
                                @foreach($rider_types_referral as $rider_type_referral)
                                    <option value="{{$rider_type_referral->id}}">{{$rider_type_referral->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-3">

                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Date (From)" data-value="{{ Carbon\Carbon::now() }}">
                        </div>
                    </div>
                    <div class="col-3 ">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Date (To)" data-value="{{ Carbon\Carbon::now() }}">
                        </div>

                    </div>
                    <div class="col-3">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                  <span class="">Arrival Time From</span>
                              </span>
                            </div>
                            <input type="text" name="arrival_time_from" class="form-control bg-primary border-primary white rounded-right pickatime arrival_time_from"  id="arrival_time_from" placeholder="From">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                  <span class="">Arrival Time To</span>
                              </span>
                            </div>
                            <input type="text" name="arrival_time_to" class="form-control bg-primary border-primary white rounded-right pickatime arrival_time_to"  id="arrival_time_to" placeholder="To">
                        </div>
                    </div>
                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>

                 <div class="row justify-content-end">
                    <div class="col-4">
                        <h4 for="export" class="font">Excel Column(s):</h4>

                        <fieldset class="form-group">
                            <select name="export[]" id="export" class="form-control select2" multiple="multiple">
                                <option value="selectAll">Select All</option>
                            </select>
                        </fieldset>
                    </div>
                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">ID</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Account No.</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Sub Segment</th>
                        <th class="border-primary border-darken-1">Vendor</th>
                        <th class="border-primary border-darken-1">Order ID</th>
                        <th class="border-primary border-darken-1">First Attempt Date</th>
                        <th class="border-primary border-darken-1">Item Quantity</th>
                        <th class="border-primary border-darken-1">Pieces</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Reason</th>
                        <th class="border-primary border-darken-1">Remark</th>
                        <th class="border-primary border-darken-1">Total Attempt</th>
                        <th class="border-primary border-darken-1">Payment Status</th>
                        <th class="border-primary border-darken-1">Invoice No.</th>
                        <th class="border-primary border-darken-1">Payment ID</th>
                        <th class="border-primary border-darken-1">Processed Date</th>
                        <th class="border-primary border-darken-1">Paid Date</th>
                        <th class="border-primary border-darken-1">Service Type</th>
                        <th class="border-primary border-darken-1">Arrival Date</th>
                        <th class="border-primary border-darken-1">Rider</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Origin Hub</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Destination Hub</th>
                        <th class="border-primary border-darken-1">Return City</th>
                        <th class="border-primary border-darken-1">Origin Zone</th>
                        <th class="border-primary border-darken-1">Class</th>
                        <th class="border-primary border-darken-1">Attempts</th>
                        <th class="border-primary border-darken-1">Shipping Mode</th>
                        <th class="border-primary border-darken-1">Category</th>
                        <th class="border-primary border-darken-1">Description</th>
                        <th class="border-primary border-darken-1">International Tracking No.</th>
                        <th class="border-primary border-darken-1">Collection Amount</th>
                        <th class="border-primary border-darken-1">Actual Weight</th>
                        <th class="border-primary border-darken-1">Chargeable Weight</th>
                        <th class="border-primary border-darken-1">Weight Charges</th>
                        <th class="border-primary border-darken-1">Cash Handling Charges</th>
                        <th class="border-primary border-darken-1">Insurance Charges</th>
                        <th class="border-primary border-darken-1">Packaging Charges</th>
                        <th class="border-primary border-darken-1">FAF Charges</th>
                        <th class="border-primary border-darken-1">Wallet Charges</th>
                        <th class="border-primary border-darken-1">Fuel Surcharge</th>
                        <th class="border-primary border-darken-1">Return Charges</th>
                        <th class="border-primary border-darken-1">Replacement Charges</th>
                        <th class="border-primary border-darken-1">Try & Buy Charges</th>
                        <th class="border-primary border-darken-1">Reverse Pickup Charges</th>
                        <th class="border-primary border-darken-1">NSA/OSA Charges</th>
                        <th class="border-primary border-darken-1">GST</th>
                        <th class="border-primary border-darken-1">SMS Charges</th>
                        <th class="border-primary border-darken-1">WHT</th>
                        <th class="border-primary border-darken-1">COD SST</th>
                        <th class="border-primary border-darken-1">Intercept Charges</th>
                        <th class="border-primary border-darken-1">Fintech Charges</th>
                        <th class="border-primary border-darken-1">Total Charges</th>
                        <th class="border-primary border-darken-1">Estimated Charges</th>
                        <th class="border-primary border-darken-1">Packing Charges</th>
                        <th class="border-primary border-darken-1">Net Payable</th>
                        <th class="border-primary border-darken-1">Delivered/Returned Date</th>
                        <th class="border-primary border-darken-1">Received/Refused By</th>
                        <th class="border-primary border-darken-1">Sales Person</th>
                        <th class="border-primary border-darken-1">Referral Name</th>
                        <th class="border-primary border-darken-1">Special Instructions</th>
                        <th class="border-primary border-darken-1">Cost</th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>


@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">

    <style>
        table.dataTable {
            font-size: 12px;
        }

        table.dataTable thead tr th {
            padding-left: 0.5em;
            white-space: normal;
            word-wrap: break-word;
        }

        table.dataTable thead tr th:before,
        table.dataTable thead tr th:after {
            height: 20px;
            margin-bottom: -10px;
            bottom: 50% !important;
        }

        table.dataTable tbody tr td {
            padding-left: 0.5em;
            padding-right: 0.5em;
        }

        table.dataTable tbody tr td.select-checkbox:before {
            top: 50%;
            border-color: #64a0d2;
        }

        table.dataTable tbody tr.selected td.select-checkbox:after {
            top: 50%;
            text-shadow: none;
        }

        .btn-group .dropdown-menu .dropdown-item {
            white-space: normal;
        }
        a.btn.btn-secondary {
            border-radius: 20px;
            background: #64a0d2;
        }
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
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.time.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {
            $('#search_tracking_no').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#sales_person_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Sales Person',
                allowClear:true
            });
            $('#search_shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Shipping mode',
                width:'100%',
                allowClear:true
            });
            $('#search_business_category').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Business Category',
                allowClear:true
            });
           $('#search_shipper').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Shipper',
                width:'100%',
                allowClear:true
            });
            $('#search_origin').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Origin City',
                width:'100%',
                allowClear:true
            });
            $('#search_destination').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Destination City',
                width:'100%',
                allowClear:true
            });
            $('#search_hub').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Destination Hub',
                width:'100%',
                allowClear:true
            });
            $('#search_status').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Status',
                width:'100%',
                allowClear:true
            });
            $('#search_shippers').select2({
                width:'100%',
                placeholder:"Select Multiple Shippers",
                allowClear:true,
            });
            $('#sub_segment_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Sub Segment',
                allowClear:true
            });
             $('#ref_name_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Referral Name',
                allowClear:true
            });
            $('#search_origin_hub').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Origin Hub',
                allowClear:true,
            });
            $('#search_origin_zone').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Origin Zone',
                allowClear:true,
            });
        

            $('#rider_types_referral').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Rider Type Referral*'
            }).on('change', function() {
                var rider_type = $(this).val();
                if (rider_type == 3) {
                    $('#ref_name_select').prop('disabled', true); 
                } else {
                    $('#ref_name_select').prop('disabled', false); 
                }
            });

            
            
            $('.arrival_time_from').pickatime({
                clear: '',
                format: 'h:i A',
                interval: 30,
                onSet: function(context) {
                    if($('input[name="search_date_from_formatted"]').val()==$('input[name="search_date_to_formatted"]').val())
                    {
                        if (context.select) {
                        $('#arrival_time_to').pickatime('picker').set('min', $('#arrival_time_from').pickatime('picker').get('select'));
                        }
                    }
                    else{
                        if (context.select) {
                        $('#arrival_time_to').pickatime('picker').set('min', '');
                        }
                    }
                }

            });
            $('.arrival_time_to').pickatime({
                clear: '',
                format: 'h:i A',
                interval: 30,
                onSet: function(context) {
                    if($('input[name="search_date_from_formatted"]').val()==$('input[name="search_date_to_formatted"]').val())
                    {
                        if (context.select) {
                            $('#arrival_time_from').pickatime('picker').set('max', $('#arrival_time_to').pickatime('picker').get('select'));
                        }
                    }
                    else{
                        if (context.select) {
                            $('#arrival_time_from').pickatime('picker').set('max', '');
                        }
                    }
                }
            });

            $('#service_type_select').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Service Type",
                allowClear:true,
            });
            var submission_date = $('#submission_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#submission_date_root').css('top','40px');
                },
                onSet: function(context) {
                }
            });
            var from_date = $('#search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_date_to').pickadate('picker').set('min', $('#search_date_from').pickadate('picker').get('select'));
                        // $('#arrival_time_from').pickatime('picker').clear();
                        // $('#arrival_time_to').pickatime('picker').clear();
                        // $('input[name="arrival_time_from"]').val('12:00 AM');
                        // $('input[name="arrival_time_to"]').val('11:30 PM');
                    }
                }
            });

            var to_date = $('#search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_date_from').pickadate('picker').set('max', $('#search_date_to').pickadate('picker').get('select'));
                        // $('#arrival_time_from').pickatime('picker').clear();
                        // $('#arrival_time_to').pickatime('picker').clear();
                        // $('input[name="arrival_time_from"]').val('12:00 AM');
                        // $('input[name="arrival_time_to"]').val('11:30 PM');
                    }
                }
            });

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
               buttons: [
                    {
                        extend: 'excel',
                        title: 'Overall Sales Report',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                        action: function(e, dt, node, config) {
                            if ($('#export').val().length === 0) {
                                swal({
                                    text: 'At least 1 column should be selected for export!',
                                    title: 'Please select excel column(s)',
                                    icon: 'warning',
                                    buttons: {
                                        cancel: {
                                            text: 'OK',
                                            value: null,
                                            visible: true,
                                            closeModal: true,
                                        }
                                    },
                                    dangerMode: true
                                });
                                return;
                            }

                            $.ajax({
                                url: '{{ route('admin.reports.overall_sales.list') }}',
                                method: "POST",
                                data: {
                                    excel: true,
                                    search_tracking: $('#search_tracking_no').val(),
                                    search_sales_person: $('#sales_person_select').val(),
                                    search_shipper: $('#search_shipper').val(),
                                    search_shippers: $('#search_shippers').val(),
                                    search_origin: $('#search_origin').val(),
                                    search_destination: $('#search_destination').val(),
                                    search_hub: $('#search_hub').val(),
                                    search_status: $('#search_status').val(),
                                    sub_segment: $('#sub_segment_select').val(),
                                    ref: $('#ref_name_select').val(),
                                    search_date_from: $('input[name="search_date_from_formatted"]').val(),
                                    search_date_to: $('input[name="search_date_to_formatted"]').val(),
                                    search_business_category: $('#search_business_category').val(),
                                    arrival_time_from: $('input[name="arrival_time_from"]').val(),
                                    arrival_time_to: $('input[name="arrival_time_to"]').val(),
                                    search_shipping_mode: $('#search_shipping_mode').val(),
                                    search_origin_hub: $('#search_origin_hub').val(),
                                    search_origin_zone: $('#search_origin_zone').val(),
                                    service_type_select: $('#service_type_select').val(),
                                    rider_type_referral: $('#rider_types_referral').val(),
                                    selectedValue: $('#export').val(),
                                    selectedTexts: $('#export option:selected').map(function () {
                                        return $(this).text();
                                    }).get(),
                                    _token: $('meta[name="csrf-token"]').attr('content'),
                                },
                                beforeSend: function() {
                                    swal({
                                        title: 'Please Wait!',
                                        text: 'Downloading is in progress',
                                        icon: 'info',
                                        buttons: false,
                                        closeOnClickOutside: false,
                                        closeOnEsc: false
                                    });
                                },
                                complete: function() {
                                    swal.close();
                                },
                                success: function(response, status, xhr) {
                                    var disposition = xhr.getResponseHeader('Content-Disposition');
                                    if (disposition && disposition.indexOf('attachment') !== -1) {
                                        var filename = 'OverallSales_Report.csv';
                                        var blob = new Blob([response], { type: 'text/csv' });
                                        var link = document.createElement('a');
                                        var url = window.URL.createObjectURL(blob);
                                        link.href = url;
                                        link.download = filename;
                                        document.body.appendChild(link);
                                        link.click();
                                        window.URL.revokeObjectURL(url);
                                        document.body.removeChild(link);
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error('Failed to fetch CSV data:', status, error);
                                }
                            });
                        }
                    }
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                deferLoading: 0,
                ajax:{
                    url: '{{ route('admin.reports.overall_sales.list') }}',
                    method:'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function (d) {
                        d.search_tracking = $('#search_tracking_no').val();
                        d.search_sales_person =  $('#sales_person_select').val();
                        d.search_shipper = $('#search_shipper').val();
                        d.search_shippers = $('#search_shippers').val();
                        d.search_origin = $('#search_origin').val();
                        d.search_destination = $('#search_destination').val();
                        d.search_hub = $('#search_hub').val();
                        d.search_status = $('#search_status').val();
                        d.sub_segment = $('#sub_segment_select').val();
                        d.ref = $('#ref_name_select').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                        d.search_business_category = $('#search_business_category').val();
                        d.arrival_time_from= $('input[name="arrival_time_from"]').val();
                        d.arrival_time_to= $('input[name="arrival_time_to"]').val();
                        d.search_shipping_mode = $('#search_shipping_mode').val();
                        d.search_origin_hub = $('#search_origin_hub').val();
                        d.search_origin_zone = $('#search_origin_zone').val();
                        d.service_type_select = $('#service_type_select').val()
                        d.rider_type_referral = $('#rider_types_referral').val();
                    }
                },
                order: [[1, 'desc']],
                columns: [
                    {
                        orderable: false,
                        searchable: false,
                        name: 'serial_number',
                        class: 'align-middle serial_number',
                        targets: 0,
                        render: function (data, type, row) { return ''; }
                    },
                    { data: 'shipment_id', name: 'shipments.id', orderable: true, searchable: false, visible: false, value: 'shipment_id', text: 'Shipment ID', download: true },
                    { data: 'tracking_number_link', name: 'shipments.tracking_number', class: 'align-middle text-center tracking_number_link', value: 'tracking_number_link', text: 'Tracking Number', download: true },
                    { data: 'account_no', name: 'u.id', class: 'align-middle account_no', value: 'account_no', text: 'Account Number', download: true },
                    { data: 'shipper', name: 'u.name', class: 'align-middle shipper', value: 'shipper', text: 'Shipper Name', download: true },
                    { data: 'sub_segment', name: 'scs.name', class: 'align-middle sub_segment', value: 'sub_segment', text: 'Sub Segment', download: true },
                    { data: 'vendor', name: 'usi.vendor', class: 'align-middle vendor', value: 'vendor', text: 'Vendor', download: true },
                    { data: 'order_id', name: 'shipments.order_id', class: 'align-middle order_id', value: 'order_id', text: 'Order ID', download: true },
                    { data: 'first_attempt_date', name: 'first_attempt_date', class: 'align-middle first_attempt_date', value: 'first_attempt_date', text: 'First Attempt Date', download: true },
                    { data: 'item_quantity', name: 'item_quantity', class: 'align-middle item_quantity', value: 'item_quantity', text: 'Item Quantity', download: true },
                    { data: 'pieces', name: 'pieces', class: 'align-middle pieces', value: 'pieces', text: 'Pieces', download: true },
                    { data: 'current_status', name: 'ss.name', class: 'align-middle current_status', value: 'current_status', text: 'Current Status', download: true },
                    { data: 'reason', name: 'reason', class: 'align-middle reason', value: 'reason', text: 'Reason', download: true },
                    { data: 'remark', name: 'remark', class: 'align-middle remark', value: 'remark', text: 'Remark', download: true },
                    { data: 'total_attempt', name: 'total_attempt', class: 'align-middle total_attempt', value: 'total_attempt', text: 'Total Attempts', download: true },
                    { data: 'payment_status', name: 'sps.name', class: 'align-middle payment_status', value: 'payment_status', text: 'Payment Status', download: true },
                    { data: 'invoice_number', name: 'invoices.invoice_number', class: 'align-middle text-center invoice_number', value: 'invoice_number', text: 'Invoice Number', download: true },
                    { data: 'payment_id', name: 'dps.id', class: 'align-middle payment_id', value: 'payment_id', text: 'Payment ID', download: true },
                    { data: 'processed_date', name: 'spjproceed_date.created_at', class: 'align-middle processed_date', value: 'processed_date', text: 'Processed Date', download: true },
                    { data: 'paid_date', name: 'spjpaid_date.created_at', class: 'align-middle paid_date', value: 'paid_date', text: 'Paid Date', download: true },
                    { data: 'service_type', name: 'bt.booking_type', class: 'align-middle service_type', value: 'service_type', text: 'Service Type', download: true },
                    { data: 'arrival_date', name: 'sj.created_at', class: 'align-middle arrival_date', value: 'arrival_date', text: 'Arrival Date', download: true },
                    { data: 'ridername', name: 'r.name', class: 'align-middle ridername', value: 'ridername', text: 'Rider Name', download: true },
                    { data: 'origin', name: 'oc.name', class: 'align-middle origin', value: 'origin', text: 'Origin', download: true },
                    { data: 'origin_hub', name: 'och.name', class: 'align-middle origin_hub', value: 'origin_hub', text: 'Origin Hub', download: true },
                    { data: 'destination', name: 'dc.name', class: 'align-middle destination', value: 'destination', text: 'Destination', download: true },
                    { data: 'hub', name: 'h.name', class: 'align-middle hub', value: 'hub', text: 'Hub', download: true },
                    { data: 'return_city', name: 'return_city', class: 'align-middle return_city', value: 'return_city', text: 'Return City', download: true },
                    { data: 'zone', name: 'z.name', class: 'align-middle zone', value: 'zone', text: 'Zone', download: true },
                    { data: 'class', name: 'zcc.class', class: 'align-middle class', value: 'class', text: 'Class', download: true },
                    { data: 'attempts', name: 'attempts', class: 'align-middle attempts', sortable: false, value: 'attempts', text: 'Attempts', download: true },
                    { data: 'shipping_mode', name: 'sm.mode', class: 'align-middle shipping_mode', value: 'shipping_mode', text: 'Shipping Mode', download: true },
                    { data: 'category', name: 'p.product_name', class: 'align-middle category', value: 'category', text: 'Category', download: true },
                    { data: 'description', name: 'si.description', class: 'align-middle description', value: 'description', text: 'Description', download: true },
                    { data: 'international_tracking_number', name: 'ibs.international_tracking_number', class: 'align-middle international_tracking_number', value: 'international_tracking_number', text: 'International Tracking Number', download: true },
                    { data: 'p_collection_amount', name: 'pps.amount', class: 'align-middle collection_amount', value: 'p_collection_amount', text: 'Collection Amount', download: true },
                    { data: 'actual_weight', name: 'shipments.actual_weight', class: 'align-middle actual_weight', value: 'actual_weight', text: 'Actual Weight', download: true },
                    { data: 'chargeable_weight', name: 'shipments.chargeable_weight', class: 'align-middle chargeable_weight', value: 'chargeable_weight', text: 'Chargeable Weight', download: true },
                    { data: 'weight_charges', name: 'shipments.weight_charges', class: 'align-middle weight_charges', value: 'weight_charges', text: 'Weight Charges', download: true },
                    { data: 'cash_handling_charges', name: 'shipments.cash_handling_charges', class: 'align-middle cash_handling_charges', value: 'cash_handling_charges', text: 'Cash Handling Charges', download: true },
                    { data: 'insurance_charges', name: 'shipments.insurance_charges', class: 'align-middle insurance_charges', value: 'insurance_charges', text: 'Insurance Charges', download: true },
                    { data: 'packaging_material_charges', name: 'shipments.packaging_material_charges', class: 'align-middle packaging_material_charges', value: 'packaging_material_charges', text: 'Packaging Material Charges', download: true },
                    { data: 'faf_charges', name: 'sac.faf_charges', class: 'align-middle faf_charges', value: 'faf_charges', text: 'FAF Charges', download: true },
                    { data: 'wallet_charges', name: 'sac.wallet_charges', class: 'align-middle wallet_charges', value: 'wallet_charges', text: 'Wallet Charges', download: true },
                    { data: 'fuel_surcharge', name: 'shipments.fuel_surcharge', class: 'align-middle fuel_surcharge', value: 'fuel_surcharge', text: 'Fuel Surcharge', download: true },
                    { data: 'return_charges', name: 'shipments.return_charges', class: 'align-middle return_charges', value: 'return_charges', text: 'Return Charges', download: true },
                    { data: 'replacement_charges', name: 'shipments.replacement_charges', class: 'align-middle replacement_charges', value: 'replacement_charges', text: 'Replacement Charges', download: true },
                    { data: 'try_and_buy_charges', name: 'shipments.try_and_buy_charges', class: 'align-middle try_and_buy_charges', value: 'try_and_buy_charges', text: 'Try & Buy Charges', download: true },
                    { data: 'reverse_pickup_charges', name: 'ss_charge.reverse_pickup_charges', class: 'align-middle reverse_pickup_charges', value: 'reverse_pickup_charges', text: 'Reverse Pickup Charges', download: true },
                    { data: 'nsa_osa_charges', name: 'shipments.nsa_osa_charges', class: 'align-middle nsa_osa_charges', value: 'nsa_osa_charges', text: 'NSA/OSA Charges', download: true },
                    { data: 'p_gst', name: 'pps.p_gst', class: 'align-middle p_gst', sortable: false, value: 'p_gst', text: 'GST', download: true },
                    { data: 'pps_sms_charges', name: 'pps.pps_sms_charges', class: 'align-middle pps_sms_charges', sortable: false, value: 'pps_sms_charges', text: 'SMS Charges', download: true },
                    { data: 'pps_wht', name: 'pps_wht', class: 'align-middle pps_wht', sortable: false, value: 'pps_wht', text: 'WHT', download: true },
                    { data: 'pps_cod_sst', name: 'pps_cod_sst', class: 'align-middle pps_cod_sst', sortable: false, value: 'pps_cod_sst', text: 'COD SST', download: true },
                    { data: 'intercept_charges', name: 'shipments.intercept_charges', class: 'align-middle intercept_charges', value: 'intercept_charges', text: 'Intercept Charges', download: true },
                    { data: 'fintech_charges', name: 'shipments.fintech_charges', class: 'align-middle fintech_charges', value: 'fintech_charges', text: 'Fintech Charges', download: true },
                    { data: 'p_total_charges', name: 'pps.charges', class: 'align-middle total_charges', value: 'p_total_charges', text: 'Total Charges', download: true },
                    { data: 'estimated_charges', name: 'estimated_charges', class: 'align-middle estimated_charges', sortable: false, value: 'estimated_charges', text: 'Estimated Charges', download: true },
                    { data: 'packaging_charges', name: 'shipments.packaging_charges', class: 'align-middle packaging_charges', sortable: false, value: 'packaging_charges', text: 'Packaging Charges', download: true },
                    { data: 'p_net_payable', name: 'pps.payable', class: 'align-middle net_payable', value: 'p_net_payable', text: 'Net Payable', download: true },
                    { data: 'delivered_or_returned', name: 'dr.created_at', class: 'align-middle delivered_or_returned', value: 'delivered_or_returned', text: 'Delivered/Returned Date', download: true },
                    { data: 'received_or_refused_by', name: 'dr.received_or_refused_by', class: 'align-middle received_or_refused_by', value: 'received_or_refused_by', text: 'Received/Refused By', download: true },
                    { data: 'sales_person', name: 'adsp.name', class: 'align-middle sales_person', value: 'sales_person', text: 'Sales Person', download: true },
                    { data: 'ref', name: 'r.name', class: 'align-middle ref', value: 'ref', text: 'Reference', download: true },
                    { data: 'special_instructions', name: 'shipments.special_instructions', class: 'align-middle special_instructions', value: 'special_instructions', text: 'Special Instructions', download: true },
                    { data: 'cost', name: 'ibs.cost', class: 'align-middle cost', value: 'cost', text: 'Cost', download: true }
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });
            $('#search_filter_btn').on('click',function () {
                table.draw();
            });

            let option = '';
            var columnNames2 = table.settings().init().columns.map(function (column) {
                if(column.download){
                    let col_name = column.value;
                    let col_text = column.text;

                    // if(column.as){
                    //     col_name+= ' as ' +column.as;
                    // }
                    if (col_name && col_text) {
                        option += `<option value="${col_name}">${col_text}</option>`;
                    }
                }
            });
            $('#export').append(option).select2({
                columns: 1,
                placeholder: 'Excel Column(s)',
                search: true,
                selectAll: true
            });

            $('#export').select2({
                width:'100%',
                placeholder:"Excel Column(s)",
                allowClear:true,
            });
            $('#export').on('select2:select', function(e) {
                var selectAll = $('#export').find('option[value="selectAll"]');
                var firstOption = $('#export option').first();
                if (e.params.data.id === 'selectAll') {
                    firstOption.data().data.text = 'Un Select All';
                    $('#export').find('option').not(selectAll).prop('selected', true).trigger('change');

                }
            });
            $('#export').on('select2:unselect', function(e) {
                var selectAll = $('#export').find('option[value="selectAll"]');
                var firstOption = $('#export option').first();
                if (e.params.data.id === 'selectAll') {
                    firstOption.data().data.text = 'Select All';
                    $('#export').find('option').not(selectAll).prop('selected', false).trigger('change');
                }
            });
        });
    </script>
@endsection