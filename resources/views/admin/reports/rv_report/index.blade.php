@extends('admin.layout.master')

@section('title', 'Rv Report')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                   RV Report
                </h1>
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

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
                                    <th class="border-primary border-darken-1">RCP Agent Updated By</th>
                                    <th class="border-primary border-darken-1">Current Status</th>
                                    <th class="border-primary border-darken-1">Current Status Date</th>
                                    <th class="border-primary border-darken-1">Fake Status</th>
                                    <th class="border-primary border-darken-1">Delivery Attempt Count</th>
                                    <th class="border-primary border-darken-1">Re-Attempt Count</th>
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
    <script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>


    <script>
        $(document).ready(function() {

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
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.rv_report.list')}}'
                },
                // rowId: 'id',
                order: [[0, 'asc']],
                columns: [
                    {name: 'serial_number', class: 'align-middle serial_number', orderable: false, searchable: false, targets: 0, render: function(data, type, row) {return '';}},
                    // {data: 'serial_number', name: 'serial_number', class: 'align-middle serial_number', searchable: false,orderable:false, targets: 0, render: function (data, type, row) {return '';}},
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
                    {data: 'call_count', name: 'call_count', class: 'align-middle designation',searchable: false,orderable:false},
                ],
                rowCallback: function(row, data, index) {
                    // $('td:eq(0)', row).addClass('select-checkbox');
                    var info = table.page.info();
                    console.log(data);
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
            });

      
        });
    </script>
    @endsection
