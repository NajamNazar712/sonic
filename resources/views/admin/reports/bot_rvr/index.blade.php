@extends('admin.layout.master')

@section('title', 'BOT CALL REPORT')

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
                            <h1 class="text-center align-middle">BOT CALL REPORT</h1>
                            @include('admin.inc.messages')
                            <div class="col mt-3">
                                <form id="search_form" class="row mb-2 justify-content-center" novalidate="novalidate">
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
                                    <th class="border-primary border-darken-1 text-center align-middle " rowspan="2">Date</th>
                                    <th class="border-primary border-darken-1 text-center align-middle "  rowspan="2">Description</th>
                                    <th class="border-primary border-darken-1 text-center align-middle "  colspan="10">Connected Calls</th>
                                    <th class="border-primary border-darken-1 text-center align-middle "  colspan="6">Not Connected Calls</th>
                                    <th class="border-primary border-darken-1 text-center align-middle "  rowspan="2">Grand Total</th>
                                    <th class="border-primary border-darken-1 text-center align-middle "  rowspan="2">Grand Total %</th>
                                    <th class="border-primary border-darken-1 text-center align-middle "  rowspan="2">No. of Shipments</th>
                                </tr>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1 text-center align-middle ">Option 1</th>
                                    <th class="border-primary border-darken-1 text-center align-middle ">Option1 %</th>
                                    <th class="border-primary border-darken-1 text-center align-middle ">Option 2</th>
                                    <th class="border-primary border-darken-1 text-center align-middle ">Option2 %</th>
                                    <th class="border-primary border-darken-1 text-center align-middle ">Option 3</th>
                                    <th class="border-primary border-darken-1 text-center align-middle ">Option3 %</th>
                                    <th class="border-primary border-darken-1 text-center align-middle ">No option</th>
                                    <th class="border-primary border-darken-1 text-center align-middle ">No Option %</th>
                                    <th class="border-primary border-darken-1 text-center align-middle ">Total</th>
                                    <th class="border-primary border-darken-1 text-center align-middle ">Total %</th>
                                    {{-- <th class="border-primary border-darken-1">Not Answered</th> --}}
                                    <th class="border-primary border-darken-1 text-center align-middle">Busy</th>
                                    <th class="border-primary border-darken-1 text-center align-middle">Busy %</th>
                                    <th class="border-primary border-darken-1 text-center align-middle">Disconnected</th>
                                    <th class="border-primary border-darken-1 text-center align-middle">Disconnected %</th>
                                    {{-- <th class="border-primary border-darken-1">Invalid Number</th> --}}
                                    <th class="border-primary border-darken-1 text-center align-middle ">Total</th>
                                    <th class="border-primary border-darken-1 text-center align-middle ">Total %</th>
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
                formatSubmit: 'yyyy-mm-dd 00:00:00',
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
                formatSubmit: 'yyyy-mm-dd 23:59:59',
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
                        url: '{{ route('admin.reports.bot_rvr.list') }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');  
                            head.push('Date');
                            head.push('Description');
                            head.push('Option 1');
                            head.push('Option %');
                            head.push('Option 2');
                            head.push('Option %');
                            head.push('Option 3');
                            head.push('Option %');
                            head.push('No Option');
                            head.push('No Option %');
                            head.push('Total');
                            head.push('Total %');
                            head.push('Busy');
                            head.push('Busy %');
                            head.push('Disconnected');
                            head.push('Disconnected %');
                            head.push('Total');
                            head.push('Total %');
                            head.push('Grand Total');
                            head.push('Grand Total %');
                            head.push('No of Shipment');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.date);
                                row.push(values.description);
                                row.push(values.option1);
                                row.push(values.option1_per);
                                row.push(values.option2);
                                row.push(values.option2_per);
                                row.push(values.option3);
                                row.push(values.option3_per);
                                row.push(values.option4);
                                row.push(values.option4_per);
                                row.push(values.total1);
                                row.push(values.total1_per);
                                row.push(values.busy);
                                row.push(values.busy_per);
                                row.push(values.disconnected);
                                row.push(values.disconnected_per);
                                row.push(values.total2);
                                row.push(values.total2_per);
                                row.push(values.grandtotal);
                                row.push(values.grandtotal_per);
                                row.push(values.no_of_shipment);
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
                    title: 'BOT CALL REPORT',
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
                    url: '{{ route('admin.reports.bot_rvr.list')}}',
                    method: 'POST',
                    headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    data: function (d) {
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                  order: [[1, 'desc'], [2, 'asc']], // Order by 'created_at' DESC and 'description' ASC
                columns: [
                    {name: 'serial_number', class: 'align-middle serial_number', orderable: false, searchable: false, targets: 0, render: function(data, type, row) {return '';}},
                    {data: 'date', name: 'date', class: 'text-center align-middle date', searchable: false},
                    {data: 'description', name: 'description', class: 'align-middle description', searchable: false},
                    {data: 'option1', name: 'option1', orderable: false, class: 'text-center align-middle option1', searchable: false},
                    {data: 'option1_per', name: 'option1_per', orderable: false, class: 'text-center align-middle option1_per', searchable: false},
                    {data: 'option2', name: 'option2', orderable: false, class: 'text-center align-middle option2', searchable: false},
                    {data: 'option2_per', name: 'option2_per', orderable: false, class: 'text-center align-middle option2_per', searchable: false},
                    {data: 'option3', name: 'option3', orderable: false, class: 'text-center align-middle option3', searchable: false},
                    {data: 'option3_per', name: 'option3_per', orderable: false, class: 'text-center align-middle option3_per', searchable: false},
                    {data: 'option4', name: 'option4', orderable: false, class: 'text-center align-middle option4', searchable: false},
                    {data: 'option4_per', name: 'option4_per', orderable: false, class: 'text-center align-middle option4_per', searchable: false},
                    {data: 'total1', name: 'total1', orderable: false, class: 'text-center align-middle total1', searchable: false},
                    {data: 'total1_per', name: 'total1_per', orderable: false, class: 'text-center align-middle total1_per', searchable: false},
                    {data: 'busy', name: 'busy', orderable: false, class: 'text-center align-middle busy', searchable: false},
                    {data: 'busy_per', name: 'busy_per', orderable: false, class: 'text-center align-middle busy_per', searchable: false},
                    {data: 'disconnected', name: 'disconnected', orderable: false, class: 'text-center align-middle disconnected', searchable: false}, // Center text
                    {data: 'disconnected_per', name: 'disconnected_per', orderable: false, class: 'text-center align-middle disconnected_per', searchable: false}, // Center text
                    {data: 'total2', name: 'total2', orderable: false, class: 'text-center align-middle total2', searchable: false},
                    {data: 'total2_per', name: 'total2_per', orderable: false, class: 'text-center align-middle total2_per', searchable: false},
                    {data: 'grandtotal', name: 'grandtotal', orderable: false, class: 'text-center align-middle grandtotal', searchable: false},
                    {data: 'grandtotal_per', name: 'grandtotal_per', orderable: false, class: 'text-center align-middle grandtotal_per', searchable: false},
                    {data: 'no_of_shipment', name: 'no_of_shipment', orderable: false, class: 'text-center align-middle no_of_shipment', searchable: false},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
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
