@extends('admin.layout.master')

@section('title', 'Overall RV Action Report')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                   Overall RV Action Report
                </h1>
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <div id="search_form" class="row mb-2 justify-content-center">
                                {{-- Search by tracking number --}}
                                <div class="col-4">
                                    <fieldset class="form-group">
                                        <input type="text" class="form-control" name="search_tracking_no" id="search_tracking_no" placeholder="Search Tracking Number">
                                    </fieldset>
                                </div>
                                {{-- Search by shipper name --}}
                                <div class="col-4">
                                    <fieldset class="form-group">
                                        <select name="search_shipper_name" id="search_shipper_name" class="form-control select2">
                                            @foreach($shippers as $shipper)
                                                <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                                {{-- Search date from filter --}}
                                <div class="col-4">
                                    <div class="form-group input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                                        </div>
                                        
                                        <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Date (From)">
                                    </div>
                                </div>
                                {{-- Search date to filter --}}
                                <div class="col-4">
                                    <div class="form-group input-group">
                                        <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o"></span>
                                    </span>
                                        </div>
        
                                        <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Date (To)">
                                    </div>
        
                                </div>
                                {{-- Search btn --}}
                                <div class="col-2">
                                    <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                                </div>
                            </div>
                            <table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Tracking Number</th>
                                    <th class="border-primary border-darken-1">Shipper Name</th>
                                    <th class="border-primary border-darken-1">Origin</th>
                                    <th class="border-primary border-darken-1">Destination</th>
                                    <th class="border-primary border-darken-1">Hub</th>
                                    <th class="border-primary border-darken-1">Area</th>
                                    <th class="border-primary border-darken-1">Consignee Name</th>
                                    <th class="border-primary border-darken-1">Number</th>
                                    <th class="border-primary border-darken-1">Address</th>
                                    <th class="border-primary border-darken-1">COD Amount</th>
                                    <th class="border-primary border-darken-1">Weight</th>
                                    <th class="border-primary border-darken-1">Shipping Mode</th>
                                    <th class="border-primary border-darken-1">Service Type</th>
                                    <th class="border-primary border-darken-1">Arrival Date</th>
                                    <th class="border-primary border-darken-1">Action</th>
                                    <th class="border-primary border-darken-1">Reason</th>
                                    <th class="border-primary border-darken-1">Remarks</th>
                                    <th class="border-primary border-darken-1">Action Date</th>
                                    <th class="border-primary border-darken-1">Action Updated By</th>
                                    <th class="border-primary border-darken-1">RCP tracking_nums Updated By</th>
                                    <th class="border-primary border-darken-1">Current Status</th>
                                    <th class="border-primary border-darken-1">Current Status Date</th>
                                    <th class="border-primary border-darken-1">Fake Status</th>
                                    <th class="border-primary border-darken-1">Delivery Attempt Count</th>
                                    <th class="border-primary border-darken-1">Re-Attempt Count</th>
                                    <th class="border-primary border-darken-1">Call History</th>
                                    <th class="border-primary border-darken-1">Call Count</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="unresponsive_count" data-backdrop="static" role="dialog" aria-labelledby="unresponsive_count" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Call History</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>S No</th>
                                <th>Calling Date</th>
                                <th>Calling Time</th>
                                <th>Call Findings</th>
                                <th>Unresponsive Finding</th>
                                <th>Other Remarks</th>
                                <th>User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                       
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
            $('#search_tracking_no').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            var currDate='{{ Carbon\Carbon::now() }}';
            $('#search_form #search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                max: currDate,
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        var fromDate = $('#search_form #search_date_from').pickadate('picker').get('select');
                        $('#search_form #search_date_to').pickadate('picker').set('min', fromDate);

                        // toDate returns 'invalid date', logic is to be corrected
                        var toDate = new Date();
                        console.log("new date", toDate);
                        toDate.setDate(new Date(fromDate.year, fromDate.month, fromDate.date + 30));

                        $('#search_form #search_date_to').pickadate('picker').set('max', toDate);
                    }
                }
            });

            $('#search_form #search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                max: currDate,
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {    
                        var toDate = $('#search_form #search_date_to').pickadate('picker').get('select');
                        $('#search_form #search_date_from').pickadate('picker').set('max', toDate);
                    }
                }
            });


            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [{
                    extend: 'excel',
                    title: 'Trax Directory',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                    className: 'btn btn-primary datatable_excel_btn d-none',
                }],
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
                    url: '{{ route('admin.reports.rv_report.list')}}',
                    data: function (d) {
                        d.search_tracking_no = $('#search_tracking_no').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                // rowId: 'id',
                order: [[1, 'asc']],
                columns: [
                    {name: 'serial_number', class: 'align-middle serial_number', orderable: false, searchable: false, targets: 0, render: function(data, type, row) {return '';}},
                    {data: 'tracking_number', name: 'tracking_number', class: 'text-center align-middle trax_id',searchable: false,orderable:false},
                    {data: 'shipper_name', name: 'shipper_name', class: 'align-middle serial_number',searchable: false,orderable:false},
                    {data: 'origin', name: 'origin', class: 'text-center align-middle trax_id',searchable: false,orderable:false},
                    {data: 'destination', name: 'destination', class: 'align-middle name',searchable: false,orderable:false},
                    {data: 'hub', name: 'hub', class: 'align-middle name',searchable: false,orderable:false},
                    {data: 'area', name: 'area', class: 'align-middle name',searchable: false,orderable:false},
                    {data: 'consignee_name', name: 'consignee_name', class: 'align-middle phone_number',searchable: false,orderable:false},
                    {data: 'number', name: 'number', class: 'align-middle email',searchable: false,orderable:false},
                    {data: 'address', name: 'address', class: 'align-middle email',searchable: false,orderable:false},
                    {data: 'cod_amount', name: 'cod_amount', class: 'align-middle city',searchable: false,orderable:false},
                    {data: 'weight', name: 'weight', class: 'align-middle designation',searchable: false,orderable:false},
                    {data: 'shipping_mode', name: 'shipping_mode', class: 'align-middle designation',searchable: false,orderable:false},
                    {data: 'service_type', name: 'service_type', class: 'align-middle designation',searchable: false,orderable:false},
                    {data: 'arrival_date', name: 'arrival_date', class: 'align-middle designation',searchable: false,orderable:false},
                    {data: 'rv_status', name: 'rv_status', class: 'align-middle designation',searchable: false,orderable:false},
                    {data: 'reason', name: 'reason', class: 'align-middle designation',searchable: false,orderable:false},
                    {data: 'remarks', name: 'remarks', class: 'align-middle designation',searchable: false,orderable:false},
                    {data: 'action_date', name: 'action_date', class: 'align-middle designation',searchable: false,orderable:false},
                    {data: 'action_updated_by', name: 'action_updated_by', class: 'align-middle designation',searchable: false,orderable:false},
                    {data: 'rcp_agent_updated_by', name: 'rcp_agent_updated_by', class: 'align-middle designation',searchable: false,orderable:false},
                    {data: 'current_status', name: 'current_status', class: 'align-middle designation',searchable: false,orderable:false},
                    {data: 'current_status_date', name: 'current_status_date', class: 'align-middle designation',searchable: false,orderable:false},
                    {data: 'fake_status', name: 'fake_status', class: 'align-middle designation',searchable: false,orderable:false},
                    {data: 'delivery_attempt_count', name: 'delivery_attempt_count', class: 'align-middle designation',searchable: false,orderable:false},
                    {data: 're_attempt_count', name: 're_attempt_count', class: 'align-middle designation',searchable: false,orderable:false},
                    {data: 'unresponsive_count', name: 'unresponsive_count', class: 'align-middle unresponsive_count',searchable: false,orderable:false},
                    {data: 'call_count', name: 'call_count', class: 'align-middle designation',searchable: false,orderable:false},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
            });

            $('#search_filter_btn').on('click',function () {
                var trackingNo = $('#search_tracking_no').val();
                var shipperName = $('#search_shipper_name').val();
                var dateFrom = $('input[name="search_date_from_formatted"]').val();
                var dateTo = $('input[name="search_date_to_formatted"]').val();

                if (shipperName !== '' || trackingNo !== '' || (dateFrom !== '' && dateTo !== '')) {
                    table.draw();
                }

            });

            $('body').on('click', '.unresponsive_count_label', function() {
                var dataId = $(this).attr('data-shipments');
                $.ajax({
                    url: '{!! route('admin.reports.rv_report.rv_call_history') !!}',
                    method: 'GET',
                    data: { id: dataId },
                    dataType: 'json',
                    success: function(response) {
                        var tableBody = $('#unresponsive_count').find('tbody');
                        tableBody.empty();
                        console.log(response.data);
                        $.each(response.data, function(index, rowData) {
                            var dateTimeParts = rowData.data.created_at.split(' ');
                            var row = $('<tr>');
                            row.append($('<td>').text(index + 1)); 
                            row.append($('<td>').text(dateTimeParts[0])); // Display date
                            row.append($('<td>').text(dateTimeParts[1])); // Display time
                            row.append($('<td>').text('Unresponsive'));
                            row.append($('<td>').text(rowData.data.rv_call_finding.remark));
                            row.append($('<td>').text(rowData.data.remarks != null ? rowData.data.remarks : '-'));
                            row.append($('<td>').text(rowData.user_name));
                            tableBody.append(row);
                        });

                        $("#unresponsive_count").modal('show');
                    },
                    error: function(xhr, status, error) {
                        // Handle errors here
                        console.error(xhr, status, error);
                    }
                });
            });
        });
    </script>
    @endsection
