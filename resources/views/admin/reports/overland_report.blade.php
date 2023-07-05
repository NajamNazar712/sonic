@extends('admin.layout.master')

@section('title', 'Overland Report')

@section('content')
    <h1 class="mb-1">
        Overland Report
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
{{--                    <div class="col-3">--}}
{{--                        <fieldset class="form-group">--}}
{{--                            <select name="search_business_category" id="search_business_category" class="form-control select2">--}}
{{--                                @foreach($business_categories as $bc)--}}
{{--                                    <option value="{{$bc->id}}">{{$bc->name}}</option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                        </fieldset>--}}
{{--                    </div>--}}

{{--                    <div class="col-3">--}}
{{--                        <div class="form-group">--}}
{{--                            <select name="search_shipping_mode" id="search_shipping_mode" class="form-control select2" data-rule-required="true" data-msg-required="Shipping Mode is required">--}}
{{--                                @foreach($shipping_modes as $shipping_mode)--}}
{{--                                    <option value="{{$shipping_mode->id}}">{{$shipping_mode->mode}}</option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    <div class="col-3">--}}
{{--                        <div class="form-group">--}}
{{--                            <select name="sub_segment_select" id="sub_segment_select" class="select2">--}}
{{--                                @foreach($sub_segments as $sub_segment)--}}
{{--                                    <option value="{{$sub_segment->id}}">{{$sub_segment->name}}</option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                    </div>--}}

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
                            <input type="text" name="arrival_time_from" class="form-control bg-primary border-primary white rounded-right pickatime arrival_time_from" value="12:00 AM" id="arrival_time_from" placeholder="From">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                  <span class="">Arrival Time To</span>
                              </span>
                            </div>
                            <input type="text" name="arrival_time_to" class="form-control bg-primary border-primary white rounded-right pickatime arrival_time_to" value="11:30 PM" id="arrival_time_to" placeholder="To">
                        </div>
                    </div>
                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Account No.</th>
                        <th class="border-primary border-darken-1">Shipper Name</th>
                        <th class="border-primary border-darken-1">Order ID</th>
                        <th class="border-primary border-darken-1">Sub Segment</th>
                        <th class="border-primary border-darken-1">Current Status</th>
                        <th class="border-primary border-darken-1">Service Type</th>
                        <th class="border-primary border-darken-1">Booked Date</th>
                        <th class="border-primary border-darken-1">Arrival Date</th>
                        <th class="border-primary border-darken-1">In Transit Date</th>
                        <th class="border-primary border-darken-1">Arrived At Destination Date</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Origin Zone</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Destination Zone</th>
                        <th class="border-primary border-darken-1">COD Amount</th>
                        <th class="border-primary border-darken-1">Actual Weight</th>
                        <th class="border-primary border-darken-1">Delivered/Return Date</th>
                        <th class="border-primary border-darken-1">Sales Person</th>
                        <th class="border-primary border-darken-1">Booking to Arrival Tat</th>
                        <th class="border-primary border-darken-1">Arrival to Transit Tat</th>
                        <th class="border-primary border-darken-1">Transit to Arrive at Destination Tat</th>
                        <th class="border-primary border-darken-1">Arrived at Destination to First Out for Delivery Tat</th>
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
                placeholder:'Select Hub',
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
                placeholder: 'Sub Segment*'
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
                        $('input[name="arrival_time_from"]').val('12:00 AM');
                        $('input[name="arrival_time_to"]').val('11:30 PM');
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
                        $('input[name="arrival_time_from"]').val('12:00 AM');
                        $('input[name="arrival_time_to"]').val('11:30 PM');
                    }
                }
            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    blockPagePermanently();
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.overland.list') }}',
                        method:'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S. No.');
                            head.push('Tracking No.');
                            head.push('Account No.');
                            head.push('Shipper Name');
                            head.push('Order ID');
                            head.push('Sub Segment');
                            head.push('Current Status');
                            head.push('Service Type');
                            head.push('Booked Date');
                            head.push('Arrival Date');
                            head.push('In Transit Date');
                            head.push('Arrived At Destination Date');
                            head.push('Origin');
                            head.push('Origin Zone');
                            head.push('Destination');
                            head.push('Hub');
                            head.push('Destination Zone');
                            head.push('COD Amount');
                            head.push('Actual Weight');
                            head.push('Delivered/Return Date');
                            head.push('Sale Person');
                            head.push('Booking To Arrival Tat');
                            head.push('Arrival To Transit Tat');
                            head.push('Transit To Arrival At Destination Tat');
                            head.push('Arrived At Destination to First Out Four Delivery Tat');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.account_no);
                                row.push(values.shipper);
                                row.push(values.order_id);
                                row.push(values.sub_segment);
                                row.push(values.current_status);
                                row.push(values.service_type);
                                row.push(values.booked_date);
                                row.push(values.arrival_date);
                                row.push(values.intransit_date);
                                row.push(values.arrived_at_destination_date);
                                row.push(values.origin);
                                row.push(values.zone);
                                row.push(values.destination);
                                row.push(values.hub);
                                row.push(values.destination_zone);
                                row.push(values.s_collection_amount);
                                row.push(values.actual_weight);
                                row.push(values.delivered_or_returned);
                                row.push(values.sales_person);
                                row.push(values.booking_to_arrival_date_count);
                                row.push(values.arrival_to_transit_date_count);
                                row.push(values.transit_to_arrived_dest_date_count);
                                row.push(values.arrive_to_delivery_date_count);

                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header:head};
                }
            } );
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Overland Report',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                    },
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
                    url: '{{ route('admin.reports.overland.list') }}',
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
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                        d.search_business_category = $('#search_business_category').val();
                        d.arrival_time_from= $('input[name="arrival_time_from"]').val();
                        d.arrival_time_to= $('input[name="arrival_time_to"]').val();
                        d.search_shipping_mode = $('#search_shipping_mode').val();
                    }
                },
                order: [[1, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'tracking_number_link' ,name: 'shipments.tracking_number', class: 'align-middle text-center tracking_number_link'},
                    { data:'account_no' ,name: 'u.id', class: 'align-middle account_no'},
                    { data:'shipper' ,name: 'u.name', class: 'align-middle shipper'},
                    { data:'order_id' ,name: 'shipments.order_id', class: 'align-middle',sortable:false},
                    { data:'sub_segment' ,name: 'u.name', class: 'align-middle',sortable:false},
                    { data:'current_status' ,name: 'ss.name', class: 'align-middle current_status'},
                    { data:'service_type' ,name:'bt.booking_type', class: 'align-middle'},
                    { data:'booked_date' ,name:'sj.created_at', class: 'align-middle remark'},
                    { data:'arrival_date' ,name: 'sja.created_at', class: 'align-middle total_attempt'},
                    { data:'intransit_date' ,name: 'sjb.created_at', class: 'align-middle payment_status'},
                    { data:'arrived_at_destination_date' ,name: 'sjc.created_at', class: 'align-middle text-center invoice_number'},
                    { data:'origin' ,name: 'oc.name', class: 'align-middle payment_status'},
                    { data:'zone' ,name: 'z.name', class: 'align-middle service_type'},
                    { data:'destination' ,name: 'dc.name', class: 'align-middle arrival_date'},
                    { data:'hub' ,name: 'h.name', class: 'align-middle origin'},
                    { data:'destination_zone' ,name: 'dz.name', class: 'align-middle destination'},
                    { data:'s_collection_amount' ,name: 'shipments.amount', class: 'align-middle consignee_address'},
                    { data:'actual_weight' ,name: 'shipments.actual_weight', class: 'align-middle hub'},
                    { data:'delivered_or_returned', name: 'dr.created_at', class: 'align-middle return_city'},
                    { data:'sales_person' ,name: 'adsp.name', class: 'align-middle zone'},
                    { data:'booking_to_arrival_date_count' ,name: 'zu.name', class: 'align-middle'},
                    { data:'arrival_to_transit_date_count' ,name: 'u.name', class: 'align-middle'},
                    { data:'transit_to_arrived_dest_date_count' ,name: 'u.name', class: 'align-middle'},
                    { data:'arrive_to_delivery_date_count' ,name: 'u.name', class: 'align-middle'},
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

        });
    </script>
@endsection