@extends('admin.layout.master')

@section('title', 'Opertaion Service Level Report')

@section('content')
    <h1 class="mb-1">
        Opertaion Service Level Report
    </h1>
    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center">
                    <div class="col-4 mb-1">
                        <fieldset class="form-group">
                            <select name="search_shipper[]" id="search_shipper" class="form-control select2" multiple="multiple" required data-rule-required="true" data-msg-required="This field is required">
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
                    <div class="col-4 mb-1">
                        <fieldset class="form-group">
                        <select name="search_hub" id="search_hub" class="form-control select2">
                            @foreach($hubs as $hub)
                                <option value="{{$hub->id}}">{{$hub->name}}</option>
                            @endforeach
                        </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_status" id="search_status" class="form-control select2">
                                @foreach($statuses as $status)
                                    <option value="{{$status->id}}">{{$status->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_zone" id="search_zone" class="form-control select2">
                                @foreach($zones as $zone)
                                    <option value="{{$zone->id}}">{{$zone->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4 mb-1">
                        <fieldset class="form-group">
                            <select name="search_shippimg_modes" id="search_shippimg_modes" class="form-control select2">
                                @foreach($shippimg_modes as $shippimg_mode)
                                    <option value="{{$shippimg_mode->id}}">{{$shippimg_mode->mode}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <div class="form-group input-group ">
                            <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o"></span>
                                    </span>
                            </div>
                            <input type="text" name="arrival_date_from"
                                   class="form-control pickadate bg-primary border-primary white rounded-right"
                                   id="arrival_date_from" placeholder="Arrival Date (From)">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o"></span>
                                    </span>
                            </div>
                            <input type="text" name="arrival_date_to"
                                   class="form-control pickadate bg-primary border-primary white rounded-right"
                                   id="arrival_date_to" placeholder="Arrival Date Date (To)">
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="form-group input-group ">
                            <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o"></span>
                                    </span>
                            </div>
                            <input type="text" name="booking_date_from"
                                   class="form-control pickadate bg-primary border-primary white rounded-right"
                                   id="booking_date_from" placeholder="Booking Date (From)" title="Booking Date (From)" data-value="{{ Carbon\Carbon::today() }}">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o"></span>
                                    </span>
                            </div>
                            <input type="text" name="booking_date_to"
                                   class="form-control pickadate bg-primary border-primary white rounded-right"
                                   id="booking_date_to" placeholder="Booking Date Date (To)" title="Booking Date Date (To)" data-value="{{ Carbon\Carbon::today() }}">
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
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Consignee Name</th>
                        <th class="border-primary border-darken-1">History Status</th>
                        <th class="border-primary border-darken-1">Service</th>
                        <th class="border-primary border-darken-1">Booking Date</th>
                        <th class="border-primary border-darken-1">Arrival Date</th>
                        <th class="border-primary border-darken-1">Last Status Date</th>
                        <th class="border-primary border-darken-1">Shipping Mode</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Product Type</th>
                        <th class="border-primary border-darken-1">Product Description</th>
                        <th class="border-primary border-darken-1">Collection Amount</th>
                        <th class="border-primary border-darken-1">Aging (Booking)</th>
                        <th class="border-primary border-darken-1">Aging (Arrival)</th>
                        <th class="border-primary border-darken-1">Aging (Last Status)</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <style type="text/css">
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
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            // $('#search_shipper').prepend('<option value="" selected="selected"></option>').select2({
            //     placeholder:'Select Shipper',
            //     width:'100%',
            //     allowClear:true
            // });
            $('#search_shipper').select2({
                width:'100%',
                placeholder:"Select Shipper",
                allowClear:true,
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
            $('#search_shippimg_modes').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Shipping Mode',
                width:'100%',
                allowClear:true
            });
            $('#search_status').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Status',
                width:'100%',
                allowClear:true
            });

            
            $('#search_zone').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Zone',
                width:'100%',
                allowClear:true
            });
          
            var from_max = '{{ Carbon\Carbon::now() }}';
            var to_max = '{{ Carbon\Carbon::now() }}';

            var arrival_date_from = $('#arrival_date_from').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: from_max,
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#arrival_date_from_root').css('top','40px');
                },
                onSet: function(context) {
                    var old_date_formatted = $('input[name="arrival_date_from_formatted"]').val();
                    to_date.pickadate('picker').set('min', new Date(old_date_formatted),{muted:true});
                }
            });
            var arrival_date_to = $('#arrival_date_to').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: to_max,
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#arrival_date_to_root').css('top', '40px');
                },
                onSet: function(context) {
                    var current_date_formatted = $('input[name="arrival_date_to_formatted"]').val();
                    from_date.pickadate('picker').set('max',new Date(current_date_formatted),{muted:true});
                }
            });


            var booking_date_from = $('#booking_date_from').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: from_max,
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#booking_date_from_root').css('top','40px');
                },
                onSet: function(context) {
                    var old_date_formatted = $('input[name="booking_date_from_formatted"]').val();
                    to_date.pickadate('picker').set('min', new Date(old_date_formatted),{muted:true});
                }
            });
            var booking_date_to = $('#booking_date_to').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: to_max,
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#booking_date_to_root').css('top', '40px');
                },
                onSet: function(context) {
                    var current_date_formatted = $('input[name="booking_date_to_formatted"]').val();
                    from_date.pickadate('picker').set('max',new Date(current_date_formatted),{muted:true});
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
                        url: '{{ route('admin.reports.operation_service_level.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Tracking No.');
                            head.push('Account No.');
                            head.push('Shipper');
                            head.push('Consignee Name');
                            head.push('History Status');
                            head.push('Service Type');
                            head.push('Booking Date');
                            head.push('Arrival');
                            head.push('Last Status Date');
                            head.push('Shipping Mode');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Hub');
                            head.push('Product Type');
                            head.push('Product Description');
                            head.push('Collection Amount');
                            head.push('Aging (Booking)');
                            head.push('Aging (Arrival)');
                            head.push('Aging (Last Status)');
                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.shipper);
                                row.push(values.account_no);
                                row.push(values.name);
                                row.push(values.history_status);
                                row.push(values.service_type);
                                row.push(values.created_at);
                                row.push(values.arrival);
                                row.push(values.last_status_date);
                                row.push(values.shipping_mode);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.hub);
                                row.push(values.product_type);
                                row.push(values.description);
                                row.push(values.amount);
                                row.push(values.aging_booking);
                                row.push(values.aging);
                                row.push(values.aging_last_status);

                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header: head};
                }
            } );

            var index_column = [];
            var flag = false;
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Operation Service Level Report',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                deferLoading: 0,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.operation_service_level.list') }}',
                    data: function (d) {
                        d.search_shipper = $('#search_shipper').val();
                        d.search_origin = $('#search_origin').val();
                        d.search_destination = $('#search_destination').val();
                        d.search_hub = $('#search_hub').val();
                        d.search_shipping_mode = $('#search_shippimg_modes').val();
                        d.arrival_date_to = $('input[name="arrival_date_to_formatted"]').val();
                        d.arrival_date_from = $('input[name="arrival_date_from_formatted"]').val();
                        d.booking_date_to = $('input[name="booking_date_to_formatted"]').val();
                        d.booking_date_from = $('input[name="booking_date_from_formatted"]').val();
                        d.search_status = $('#search_status').val();
                        d.search_zone = $('#search_zone').val();
                        
                        
                    }
                },
                rowId: 'shId',
                order: [[7, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number_link', name: 'shipments.tracking_number', class: 'align-middle tracking_number_link'},
                    {data: 'account_no', name: 'u.id', class: 'align-middle account_no'},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'name', name: 'shipments.consignee_name', class: 'align-middle name'},
                    {data: 'history_status', name: 'ss.name', class: 'align-middle history_status'},
                    {data: 'service_type', name: 'bt.booking_type', class: 'align-middle service_type'},
                    {data: 'created_at', name: 'shipments.created_at', class: 'align-middle created_at'},
                    {data: 'arrival', name: 'sj.created_at', class: 'align-middle arrival'},
                    {data: 'last_status_date', name: 'journey.created_at', class: 'align-middle last_status_date'},
                    {data: 'shipping_mode', name: 'sm.mode', class: 'align-middle shipping_mode'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'hub', name: 'h.name', class: 'align-middle hub'},
                    {data: 'product_type', name: 'p.product_name', class: 'align-middle product_type'},
                    {data: 'description', name: 'si.description', class: 'align-middle description'},
                    {data: 'amount', name: 'shipments.amount', class: 'align-middle amount'},
                    {data: 'aging_booking', name: 'aging_booking', class: 'align-middle aging_booking',orderable: false, searchable: false},
                    {data: 'aging', name: 'aging', class: 'align-middle aging',orderable: false, searchable: false},
                    {data: 'aging_last_status', name: 'aging_last_status', class: 'align-middle aging',orderable: false, searchable: false}

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