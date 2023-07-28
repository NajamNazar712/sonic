@extends('admin.layout.master')

@section('title', 'Assigned Agent Shipments')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                   Assigned Agent Shipments
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row mb-2 justify-content-center">
                                <div class="col-12">
                                    <input type="hidden" id="number_of_tickets_input" value="">
                                    <input type="hidden" id="number_of_available_agents_input" value="">
                                    <input type="hidden" id="number_of_pending_tickets_input" value="">
                                    <input type="hidden" id="number_of_closed_tickets_input" value="">
                                    <input type="hidden" id="number_of_connected_calls_input" value="">
                                    <input type="hidden" id="number_of_unresponsive_calls_input" value="">
                                    <input type="hidden" id="number_of_reattempt_calls_input" value="">
                                    <input type="hidden" id="number_of_return_confirm_calls_input" value="">
                                    <input type="hidden" id="number_of_intercepted_calls_input" value="">
                                    <input type="hidden" id="number_of_self_collection_calls_input" value="">
                                
                                    <div class="row justify-content-center">
                                        <div class="col-3" id="number_of_tickets_div">
                                            <div class="card bg-gradient-directional-booked_shipments pull-up cursor-pointer">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <div class="media d-flex">
                                                            <div class="align-self-center">
                                                                <i class="icon-grid text-white font-large-2 float-left"></i>
                                                            </div>
                                                            <div class="media-body text-white text-right">
                                                                <h3 class="text-white">
                                                                    <p id="total_leads" class="d-inline">{{ ($number_of_rv_tickets) }}</p>
                                                                </h3>
                                                                <span>Overall Number of Tickets (RCP count) </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                       
                                        <div class="col-3" id="number_of_pending_tickets_div">
                                            <div class="card bg-gradient-directional-in_transit pull-up cursor-pointer">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <div class="media d-flex">
                                                            <div class="align-self-center">
                                                                <i class="icon-clock text-white font-large-2 float-left"></i>
                                                            </div>
                                                            <div class="media-body text-white text-right">
                                                                <h3 class="text-white">
                                                                    <p id="in_process" class="d-inline">{{ count($number_of_pending_tickets) }}</p> (<p
                                                                        id="in_process_percentage" class="d-inline">
                                                                        {{ $number_of_pending_ticket_percentage }}</p>%)
                                                                </h3>
                                                                <span>Pending Tickets</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                
                                        <div class="col-3" id="number_of_closed_tickets_div">
                                            <div class="card bg-gradient-directional-out_for_delivery pull-up cursor-pointer">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <div class="media d-flex">
                                                            <div class="align-self-center">
                                                                <i class="icon-clock text-white font-large-2 float-left"></i>
                                                            </div>
                                                            <div class="media-body text-white text-right">
                                                                <h3 class="text-white">
                                                                    <p id="in_process_activation" class="d-inline">{{ count($number_of_closed_tickets) }}
                                                                    </p>
                                                                    (<p id="in_process_for_activation_percentage" class="d-inline">
                                                                        {{ $number_of_closed_ticket_percentage }}</p>
                                                                    %)
                                                                </h3>
                                                                <span>Closed Tickets</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                
                                    </div>
                                    <div class="row justify-content-center">
                                        <div class="col-3" id="number_of_connected_calls_div">
                                            <div class="card bg-gradient-directional-pending_shipments pull-up cursor-pointer">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <div class="media d-flex">
                                                            <div class="align-self-center">
                                                                <i class="icon-close text-white font-large-2 float-left"></i>
                                                            </div>
                                                            <div class="media-body text-white text-right">
                                                                <h3 class="text-white">
                                                                    <p id="dead_leads" class="d-inline">{{ count($number_of_connected_calls) }}</p> (<p
                                                                        id="in_process_for_activation_percentage" class="d-inline">
                                                                        {{ $number_of_connected_calls_percentage }}</p>
                                                                    %)
                                                                </h3>
                                                                <span>No. of Connected Calls.</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-3" id="number_of_unresponsive_calls_div">
                                            <div class="card bg-gradient-directional-return_delivered pull-up cursor-pointer">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <div class="media d-flex">
                                                            <div class="align-self-center">
                                                                <i class="icon-check text-white font-large-2 float-left"></i>
                                                            </div>
                                                            <div class="media-body text-white text-right">
                                                                <h3 class="text-white">
                                                                    <p id="dead_leads" class="d-inline">{{ count($number_of_unresponsive_call) }}</p> (
                                                                    <p id="in_process_for_activation_percentage" class="d-inline">
                                                                        {{ $number_of_unresponsive_percentage }}</p>
                                                                    %)
                                                                </h3>
                                                                <span>No. of Unresponsive Calls</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                
                                        <div class="col-3" id="number_of_reattempt_calls_div">
                                            <div class="card bg-gradient-directional-return_confirm pull-up cursor-pointer">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <div class="media d-flex">
                                                            <div class="align-self-center">
                                                                <i class="la la-calculator text-white font-large-2 float-left"></i>
                                                            </div>
                                                            <div class="media-body text-white text-right">
                                                                <h3 class="text-white">
                                                                    <p id="dead_leads" class="d-inline">{{ count($number_of_reattempt_call) }}</p>
                                                                    (<p id="in_process_for_activation_percentage" class="d-inline">
                                                                        {{ $number_of_reattempt_percentage }}</p>
                                                                    %)
                                                                </h3>
                                                                 <span>No. of Re-attempt updated</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-3" id="number_of_return_confirm_calls_div">
                                            <div class="card bg-gradient-directional-destination pull-up cursor-pointer">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <div class="media d-flex">
                                                            <div class="align-self-center">
                                                                <i class="la la-calculator text-white font-large-2 float-left"></i>
                                                            </div>
                                                            <div class="media-body text-white text-right">
                                                                <h3 class="text-white">
                                                                    <p id="dead_leads" class="d-inline">{{ count($number_of_return_confirm_call) }}</p>
                                                                    (<p id="in_process_for_activation_percentage" class="d-inline">
                                                                        {{ $number_of_return_confirm_percentage }}</p>
                                                                    %)
                                                                </h3>
                                                                <span>No. of Return Confirm updated</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center">
                                        <div class="col-3" id="number_of_intercepted_calls_div">
                                            <div class="card bg-gradient-directional-pending_confirmation pull-up cursor-pointer">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <div class="media d-flex">
                                                            <div class="align-self-center">
                                                                <i class="la la-hourglass text-white font-large-2 float-left"></i>
                                                            </div>
                                                            <div class="media-body text-white text-right">
                                                                <h3 class="text-white">
                                                                    <p id="dead_leads" class="d-inline">{{ count($number_of_intercept_call) }}</p>
                                                                    (<p id="in_process_for_activation_percentage" class="d-inline">
                                                                        {{ $number_of_intercept_percentage }}</p>
                                                                    %)
                                                                </h3>
                                                                <span>No. of Intercept updated.</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-3" id="number_of_self_collection_calls_div">
                                            <div class="card bg-gradient-directional-pending_confirmation pull-up cursor-pointer">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <div class="media d-flex">
                                                            <div class="align-self-center">
                                                                <i class="la la-hourglass text-white font-large-2 float-left"></i>
                                                            </div>
                                                            <div class="media-body text-white text-right">
                                                                <h3 class="text-white">
                                                                    <p id="dead_leads" class="d-inline">{{ count($number_of_self_collection_call) }}</p>
                                                                    (<p id="in_process_for_activation_percentage" class="d-inline">
                                                                        {{ $number_of_self_collection_percentage }}</p>
                                                                    %)
                                                                </h3>
                                                                <span>No. of Self-collection updated.</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Shipment ID</th>
                                    <th class="border-primary border-darken-1">Tracking Number</th>
                                    <th class="border-primary border-darken-1">Assign Agent</th>
                                    <th class="border-primary border-darken-1">Assign Agent Shipment Status</th>
                                    <th class="border-primary border-darken-1">Assign Agent Shipment Sub Status</th>
                                    <th class="border-primary border-darken-1">Assign Shipment State</th>
                                    <th class="border-primary border-darken-1">Updated By</th>
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
            $('#number_of_tickets_div').on('click', function() {
                $('#number_of_tickets_input').val(1);
                $('#number_of_available_agents_input').val('');
                $('#number_of_pending_tickets_input').val('');
                $('#number_of_closed_tickets_input').val('');
                $('#number_of_connected_calls_input').val('');
                $('#number_of_unresponsive_calls_input').val('');
                $('#number_of_reattempt_calls_input').val('');
                $('#number_of_return_confirm_calls_input').val('');
                $('#number_of_intercepted_calls_input').val('');
                $('#number_of_self_collection_calls_input').val('');

                table.draw();
            })

            $('#number_of_available_agents_div').on('click', function() {
                $('#number_of_available_agents_input').val(2);
                $('#number_of_tickets_input').val('');
                $('#number_of_pending_tickets_input').val('');
                $('#number_of_closed_tickets_input').val('');
                $('#number_of_connected_calls_input').val('');
                $('#number_of_unresponsive_calls_input').val('');
                $('#number_of_reattempt_calls_input').val('');
                $('#number_of_return_confirm_calls_input').val('');
                $('#number_of_intercepted_calls_input').val('');
                $('#number_of_self_collection_calls_input').val('');


                table.draw();
            })


            $('#number_of_pending_tickets_div').on('click', function() {

                $('#number_of_pending_tickets_input').val(3);
                $('#number_of_available_agents_input').val('');
                $('#number_of_tickets_input').val('');
                $('#number_of_closed_tickets_input').val('');
                $('#number_of_connected_calls_input').val('');
                $('#number_of_unresponsive_calls_input').val('');
                $('#number_of_reattempt_calls_input').val('');
                $('#number_of_return_confirm_calls_input').val('');
                $('#number_of_intercepted_calls_input').val('');
                $('#number_of_self_collection_calls_input').val('');


                table.draw();
            })


            $('#number_of_closed_tickets_div').on('click', function() {

                $('#number_of_closed_tickets_input').val(4);
                $('#number_of_pending_tickets_input').val('');
                $('#number_of_available_agents_input').val('');
                $('#number_of_tickets_input').val('');
                $('#number_of_connected_calls_input').val('');
                $('#number_of_unresponsive_calls_input').val('');
                $('#number_of_reattempt_calls_input').val('');
                $('#number_of_return_confirm_calls_input').val('');
                $('#number_of_intercepted_calls_input').val('');
                $('#number_of_self_collection_calls_input').val('');

                table.draw();
            })


            $('#number_of_connected_calls_div').on('click', function() {

                $('#number_of_connected_calls_input').val(5);
                $('#number_of_pending_tickets_input').val('');
                $('#number_of_available_agents_input').val('');
                $('#number_of_tickets_input').val('');
                $('#number_of_closed_tickets_input').val('');
                $('#number_of_unresponsive_calls_input').val('');
                $('#number_of_reattempt_calls_input').val('');
                $('#number_of_return_confirm_calls_input').val('');
                $('#number_of_intercepted_calls_input').val('');
                $('#number_of_self_collection_calls_input').val('');

                table.draw();
            })


            $('#number_of_unresponsive_calls_div').on('click', function() {
                $('#number_of_unresponsive_calls_input').val(6);
                $('#number_of_connected_calls_input').val('');
                $('#number_of_pending_tickets_input').val('');
                $('#number_of_available_agents_input').val('');
                $('#number_of_tickets_input').val('');
                $('#number_of_closed_tickets_input').val('');
                $('#number_of_reattempt_calls_input').val('');
                $('#number_of_return_confirm_calls_input').val('');
                $('#number_of_intercepted_calls_input').val('');
                $('#number_of_self_collection_calls_input').val('');

                table.draw();
            })


            $('#number_of_reattempt_calls_div').on('click', function() {
                $('#number_of_reattempt_calls_input').val(7);
                $('#number_of_unresponsive_calls_input').val('');
                $('#number_of_connected_calls_input').val('');
                $('#number_of_pending_tickets_input').val('');
                $('#number_of_available_agents_input').val('');
                $('#number_of_tickets_input').val('');
                $('#number_of_closed_tickets_input').val('');
                $('#number_of_return_confirm_calls_input').val('');
                $('#number_of_intercepted_calls_input').val('');
                $('#number_of_self_collection_calls_input').val('');

                table.draw();
            })

            $('#number_of_return_confirm_calls_div').on('click', function() {
                $('#number_of_return_confirm_calls_input').val(8);
                $('#number_of_reattempt_calls_input').val('');
                $('#number_of_unresponsive_calls_input').val('');
                $('#number_of_connected_calls_input').val('');
                $('#number_of_pending_tickets_input').val('');
                $('#number_of_available_agents_input').val('');
                $('#number_of_tickets_input').val('');
                $('#number_of_closed_tickets_input').val('');
                $('#number_of_intercepted_calls_input').val('');
                $('#number_of_self_collection_calls_input').val('');

                table.draw();
            })

            $('#number_of_intercepted_calls_div').on('click', function() {
                $('#number_of_intercepted_calls_input').val(9);
                $('#number_of_return_confirm_calls_input').val('');
                $('#number_of_reattempt_calls_input').val('');
                $('#number_of_unresponsive_calls_input').val('');
                $('#number_of_connected_calls_input').val('');
                $('#number_of_pending_tickets_input').val('');
                $('#number_of_available_agents_input').val('');
                $('#number_of_tickets_input').val('');
                $('#number_of_closed_tickets_input').val('');
                $('#number_of_self_collection_calls_input').val('');

                table.draw();
            })


            $('#number_of_self_collection_calls_div').on('click', function() {
                $('#number_of_self_collection_calls_input').val(10);
                $('#number_of_intercepted_calls_input').val('');
                $('#number_of_return_confirm_calls_input').val('');
                $('#number_of_reattempt_calls_input').val('');
                $('#number_of_unresponsive_calls_input').val('');
                $('#number_of_connected_calls_input').val('');
                $('#number_of_pending_tickets_input').val('');
                $('#number_of_available_agents_input').val('');
                $('#number_of_tickets_input').val('');
                $('#number_of_closed_tickets_input').val('');

                table.draw();
            })



            // jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
            //     if ( this.context.length ) {
            //         body = [];
            //         var params = table.ajax.params();
            //         params.start = 0;
            //         params.length = -1;
            //         params.excel = true;
            //         var jsonResult = $.ajax({
            //             url: '{{ route('admin.assigned_shipment.list')}}',
            //             data: params,
            //             success: function (result) {
            //                 head = [];
            //                 head.push('S.No');
            //                 head.push('Employee ID');
            //                 head.push('Name');
            //                 head.push('Phone Number');
            //                 head.push('Email');
            //                 head.push('City');
            //                 head.push('Designation');
            //                 head.push('Department');
            //                 $.each(result.data, function(index, values) {
            //                     row = [];
            //                     row.push(index + 1);
            //                     row.push(values.trax_id);
            //                     row.push(values.name);
            //                     row.push(values.phone_number);
            //                     row.push(values.email);
            //                     row.push(values.city);
            //                     row.push(values.designation);
            //                     row.push(values.department_name);
            //                     body.push(row);
            //                 });
            //             },
            //             async: false
            //         });
            //         return {body: body, header: head};
            //     }
            // });
           /* var selected_rows = [];*/
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
               /* select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },*/
                serverSide: true,
        
                ajax: {
                    url: '{{ route('admin.assigned_shipment.list')}}',
                    data: function (d) {

                        d.number_of_tickets_input = $('#number_of_tickets_input').val();
                        d.number_of_available_agents_input = $('#number_of_available_agents_input')
                            .val();
                        d.number_of_pending_tickets_input = $('#number_of_pending_tickets_input').val();
                        d.number_of_closed_tickets_input = $('#number_of_closed_tickets_input').val();
                        d.number_of_connected_calls_input = $('#number_of_connected_calls_input').val();
                        d.number_of_unresponsive_calls_input = $('#number_of_unresponsive_calls_input')
                            .val();
                        d.number_of_reattempt_calls_input = $('#number_of_reattempt_calls_input')
                            .val();
                        d.number_of_return_confirm_calls_input = $(
                                '#number_of_return_confirm_calls_input')
                            .val();
                        d.number_of_intercepted_calls_input = $('#number_of_intercepted_calls_input')
                            .val();
                        d.number_of_self_collection_calls_input = $(
                                '#number_of_self_collection_calls_input')
                            .val();


                    }
                },
                rowId: 'id',
                order: [[0, 'asc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'shipment_id', name: 'rv_shipment_assign_agents.shipment_id', class: 'text-center align-middle trax_id',searchable: false,orderable:false},
                    {data: 'tracking_number', name: 'shipment.tracking_number', class: 'text-center align-middle trax_id',searchable: false,orderable:false},
                    {data: 'agent_name', name: 'staff.name', class: 'align-middle name',searchable: false,orderable:false},
                    {data: 'status', name: 'rv_shipment_assign_agents.rv_assign_agent_status_id', class: 'align-middle phone_number',searchable: false,orderable:false},
                    {data: 'sub_status', name: 'rv_shipment_assign_agents.rv_assign_agent_sub_status_id', class: 'align-middle email',searchable: false,orderable:false},
                    {data: 'state', name: 'rv_shipment_assign_agents.rv_state_id', class: 'align-middle city',searchable: false,orderable:false},
                    {data: 'updated_by', name: 'rv_shipment_assign_agents.updated_by_id', class: 'align-middle designation',searchable: false,orderable:false},
                ],
                rowCallback: function(row, data, index) {

                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                  /*  $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    if ($.inArray(data.id, selected_rows) !== -1) {
                        table.row(row).select();
                    }*/
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());
                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.select-checkbox') || $(header).is('.trax_id') || $(header).is('.name') || $(header).is('.phone_number') || $(header).is('.email') || $(header).is('.city') || $(header).is('.designation') || $(header).is('.department_name') ||  $(header).is('.select-checkbox')) {
                            $(td).appendTo($(search));
                        }
                        else {
                            var current = $(input).appendTo($(search)).on('change', function() {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });
                    this.api().table().columns.adjust();
                }
            });

      
        });
    </script>
    @endsection
