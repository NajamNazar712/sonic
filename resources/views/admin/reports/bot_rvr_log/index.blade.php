@extends('admin.layout.master')

@section('title', 'BOT CALL LOG REPORT')

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <div class="row w-100">
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            <h1 class="text-center align-middle">BOT CALL LOG REPORT</h1>
                            @include('admin.inc.messages')
                            <div class="col mt-3">
                                <form id="search_form" class="row mb-2 justify-content-center" novalidate="novalidate">
                                    <div class="col-4">
                                        <fieldset class="form-group">
                                            <input type="text" class="form-control" name="search_tracking_no" id="search_tracking_no" placeholder="Search Tracking Number" style="text-align: right;" fdprocessedid="l9xr5h">
                                        </fieldset>
                                    </div>
                                    {{-- Search date from filter --}}
                                    <div class="col-4">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o small-calender-icon"></span>
                                                </span>
                                            </div>
                                            <input type="text" name="search_date_from" class="form-control bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Date (From)"  data-value="{{ \Carbon\Carbon::now() }}">
                                        </div>
                                    </div>

                                    {{-- Search date to filter --}}
                                    <div class="col-4">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o small-calender-icon"></span>
                                                </span>
                                            </div>
                                            <input type="text" name="search_date_to" class="form-control bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Date (To)" data-value="{{ \Carbon\Carbon::now() }}">
                                        </div>
                                    </div>

                                    {{-- Search btn --}}
                                    <div class="col-2">
                                       
                                            <button type="submit" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                                        
                                    </div>
                                </form>
                            </div>


                            <table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                               <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1 text-center align-middle " rowspan="2">Serial No</th>
                                    <th class="border-primary border-darken-1 text-center align-middle " rowspan="2">Tracking No</th>
                                    <th class="border-primary border-darken-1 text-center align-middle "  colspan="4">Call Initiate Record</th>
                                    <th class="border-primary border-darken-1 text-center align-middle "  colspan="6">Zong Call Response Record</th>
                                </tr>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1 text-center align-middle ">Shipment Id</th>
                                    <th class="border-primary border-darken-1 text-center align-middle ">Call No</th>
                                    {{-- <th class="border-primary border-darken-1 text-center align-middle ">Response</th> --}}
                                    <th class="border-primary border-darken-1 text-center align-middle ">Message</th>
                                    <th class="border-primary border-darken-1 text-center align-middle ">Date</th>
                                    <th class="border-primary border-darken-1 text-center align-middle">Shipment Id</th>
                                    {{-- <th class="border-primary border-darken-1 text-center align-middle ">Response</th> --}}
                                    <th class="border-primary border-darken-1 text-center align-middle">Message</th>
                                    <th class="border-primary border-darken-1 text-center align-middle">Call Start Date</th>
                                    <th class="border-primary border-darken-1 text-center align-middle">Call End Date</th>
                                    <th class="border-primary border-darken-1 text-center align-middle">Input</th>
                                    <th class="border-primary border-darken-1 text-center align-middle">Date</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/fonts/line-awesome/css/line-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/fonts/simple-line-icons/style.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/cryptocoins/cryptocoins.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">
    <style>
        .error-message {
            color: red
        }

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

        .small-calender-icon {
            font-size: 17px !important;
        }

        .bg-gradient-directional-booked_shipments {
            background-image: linear-gradient(45deg, #5e187b, #ed86ff);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-arrived_shipments {
            background-image: linear-gradient(45deg, #074077, #2fbef5);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-in_transit {
            background-image: linear-gradient(45deg, #535BE2, #9ea5ff);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-destination {
            background-image: linear-gradient(45deg, #027d8a, #01e4e4);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-out_for_delivery {
            background-image: linear-gradient(45deg, #ff9819, #fff824);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-pending_shipments {
            background-image: linear-gradient(45deg, #39546d, #90929a);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-pending_confirmation {
            background-image: linear-gradient(45deg, #6a1fa2, #ff4961);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-delivered {
            background-image: linear-gradient(45deg, #076500, #11f118);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-return_confirm {
            background-image: linear-gradient(45deg, #ff0c0c, #ff9191);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-pending_return {
            background-image: linear-gradient(45deg, #7d491c, #e0b668de);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-return_delivered {
            background-image: linear-gradient(45deg, #02c123, #99ff12d1);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-cancelled_shipments {
            background-image: linear-gradient(45deg, #ff6a00, #ffb74c);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-complaints_launched {
            background-image: linear-gradient(45deg, #074077, #2FBEF5);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-complaints_in_process {
            background-image: linear-gradient(45deg, #6A1FA2, #FF4961);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-complaints_closed {
            background-image: linear-gradient(45deg, #076500, #11F118);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-complaints_rejected {
            background-image: linear-gradient(45deg, #FF0C0C, #FF9191);
            background-repeat: repeat-x;
        }

        .selectize-control {
            width: 300px !important;
        }

        .div_border {
            border-style: double;
        }

        .statusBooked {
            background-color: #5DADE2;
        }

        .statusOrigin {
            background-color: #E67E22;
        }

        .statusIntransit {
            background-color: #7F8C8D;
        }

        .statusDestination {
            background-color: #F1C40F;
        }

        .statusNotattempted {
            background-color: #1F618D;
        }

        .statusDeliveryunsuccessful {
            background-color: #28B463;
        }

        .statusOnhold {
            background-color: #154360;
        }
        .tooltip-inner {
            color: red; /* Change to any color you want */
            white-space: pre-wrap; /* Ensure the JSON content wraps properly */
            max-width: 500px;      /* Set maximum width to prevent overflow */
            word-break: break-all; /* Break long words */
            background-color: #fff; /* Optional: Change background color of the tooltip */
            border: 1px solid #ccc; /* Optional: Add a border to the tooltip */
        }

        /* Optional: Style the arrow of the tooltip */
        .tooltip.bs-tooltip-top .arrow::before {
            border-top-color: #fff; /* Match the background color */
        }
        .json-tooltip + .tooltip > .tooltip-inner {
            color: blue; /* Change the color to blue for this tooltip */
            background-color: yellow; /* Optional: Custom background color */
        }
    </style>
@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    {{-- <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script> --}}
    <script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>


    <script>
        $(document).ready(function() {

            
        $('#search_shipper_name').prepend('<option value="" selected="selected"></option>')
            .select2({
                width: '100%',
                placeholder: 'Select Shippers',
                allowClear: true,
        });
        $('#search_agent_name').prepend('<option value="" selected="selected"></option>')
            .select2({
                width: '100%',
                placeholder: 'Select Agent',
                allowClear: true,
        });
            $('#search_tracking_no').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            var search_date_from = $('#search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 10:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {

                    var old_date_formatted = $('input[name="search_date_from_formatted"]').val();
                    var contractMoment = moment(old_date_formatted);
                    var current = moment(contractMoment).add(31, 'days');
                    search_date_to.pickadate('picker').set('min', new Date(old_date_formatted),{muted:true});
                    search_date_to.pickadate('picker').set('max', new Date(current.toDate()),{muted:true});
                    search_date_to.pickadate('picker').set('select', new Date(old_date_formatted),{muted:true});
                }
            });

            var search_date_to = $('#search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 22:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                }
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    if(params !== undefined){
                        params.start = 0;
                        params.length = -1;
                        params.excel = true;
                    }
                    else{
                        params = {
                            'excel':true,
                        }
                    }
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.bot_rvr_log.list') }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');  
                            head.push('Tracking No');
                            head.push('ShipmentId');
                            head.push('Call Count');
                            // head.push('Response');
                            head.push('Message');
                            head.push('Date Time');
                            head.push('ShipmentId');
                            // head.push('Response');
                            head.push('Message');
                            head.push('Call Start Date');
                            head.push('Call End Date');
                            head.push('Input');
                            head.push('Date Time');
                           
                            $.each(result.data, function(index, values) {
                                console.log(result.data);
                                row = [];
                                row.push(index + 1);
                                row.push(values.tracking_number.split(">")[2].slice(0,-3));
                                row.push(values.shipmentNo);
                                row.push(values.call_count);
                                row.push(values.message1);
                                // row.push(values.call_message);
                                row.push(values.created_at);
                                row.push(values.shipmentNo1);
                                // row.push(values.api_request);
                                row.push(values.message);
                                row.push(values.call_start_date);
                                row.push(values.call_end_date);
                                row.push(values.input);
                                row.push(values.date_time);
                                body.push(row);
                            });
                        
                        },
                        async: false
                    });
                    
                    return {body: body, header: head};
                    
                }
            });

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [{
                    extend: 'excelHtml5',
                    title: 'BOT CALL Log REPORT',
                    text: '<i class="la la-file-excel-o underline"></i> Excel',
                    className: 'btn btn-primary datatable_excel_btn',
                    
                },'reset'],
                scrollX: true, scrollY: '500px',
                autoWidth: false,
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                deferLoading: 0,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.bot_rvr_log.list')}}',
                    method: 'POST',
                    headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    data: function (d) {
                        d.search_tracking_no = $('#search_tracking_no').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                  order: [[3, 'asc']], // Order by 'created_at' DESC and 'description' ASC
                columns: [
                    {name: 'serial_number', class: 'align-middle serial_number', orderable: false, searchable: false, targets: 0, render: function(data, type, row) {return '';}},
                    {data: 'tracking_number', name: 's.tracking_number', class: 'text-center align-middle tracking_number', searchable: true},
                    {data: 'shipmentNo', name: 'shipmentNo', class: 'align-middle shipmentNo', searchable: false},
                    {data: 'call_count', name: 'call_count_initiate', orderable: false, class: 'text-center align-middle call_count', searchable: true},
                    {data: 'message1', name: 'response', orderable: false, class: 'text-center align-middle response', searchable: false},
                    // {data: 'call_message', name: 'call_message', orderable: false, class: 'text-center align-middle call_message', searchable: false},
                    {data: 'created_at', name: 'created_at', orderable: false, class: 'text-center align-middle created_at', searchable: false},
                    {data: 'shipmentNo1', name: 'shipmentNo1', class: 'align-middle shipmentNo1', searchable: false},
                    // {data: 'api_request', name: 'api_request', class: 'align-middle api_request', searchable: false},
                    {data: 'message', name: 'message', class: 'align-middle message', searchable: false},
                    {data: 'call_start_date', name: 'call_start_date', class: 'align-middle call_start_date', searchable: false},
                    {data: 'call_end_date', name: 'call_end_date', class: 'align-middle call_end_date', searchable: false},
                    {data: 'input', name: 'input', class: 'align-middle input', searchable: false},
                    {data: 'date_time', name: 'date_time', class: 'align-middle date_time', searchable: false},
                    
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>')
                        .appendTo(this.api().table().header());

                    var td =
                        '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input =
                        '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon =
                        '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                   
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.select') || $(header).is('.serial_number') ||  $(header).is('.shipmentNo') 
                        || $(header).is('.response') || $(header).is('.created_at') || $(header).is('.shipmentNo1')
                        || $(header).is('.message') || $(header).is('.call_start_date') || $(header).is('.call_end_date')
                        || $(header).is('.input') || $(header).is('.date_time')) {
                            $(td).appendTo($(search));
                        } else {
                            var current = $(input).appendTo($(search)).on('change', function() {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });
                   

                    
                }
        });

        $('#search_form').bind('submit', function (e) {
            e.preventDefault();
            var search_date_from = $('#search_form #search_date_from').val();
            var search_date_to = $('#search_form #search_date_to').val();

            if ((search_date_from != '' && search_date_to != '' )) {
                table.draw();
            }

        });

        });
    </script>
    @endsection
