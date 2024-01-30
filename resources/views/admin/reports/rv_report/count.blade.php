@extends('admin.layout.master')

@section('title', 'RV Action Count Report')

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <div class="row">
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            <h1>RV Action Count Report</h1>
                            @include('admin.inc.messages')
                            <div class="row justify-content-center">
                                <div class="col mt-2">
                                    <form id="search_form" class="row mb-2 justify-content-center" novalidate="novalidate">
                                        {{-- Search date from filter --}}
                                        <div class="col-4">
                                            <div class="form-group input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                        <span class="la la-calendar-o small-calender-icon"></span>
                                                    </span>
                                                </div>
                                                <input type="text" name="search_date_from" class="form-control bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Date (From)"  data-value="{{ \Carbon\Carbon::today()->subDays(31)->startOfDay() }}">
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
                            </div>

                            {{-- Cards Section --}}
                            <div class="row justify-content-center">
                                {{-- <div class="col-3">
                                    <div class="card bg-gradient-directional-in_transit pull-up cursor-pointer" id="search_total_div">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <div class="media d-flex">
                                                    <div class="align-self-center">
                                                        <i class="icon-clock text-white font-large-2 float-left"></i>
                                                    </div>
                                                    <div class="media-body text-white text-right">
                                                        <h3 class="text-white">
                                                            <p id="total_sar" class="d-inline">
                                                                {{ ($total_of_shipments) }}</p>
                                                        </h3>
                                                        <span>Total Of Shipments</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                                
                                <div class="col-3" id="search_rvr_div">
                                    <div class="card bg-gradient-directional-booked_shipments pull-up cursor-pointer">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <div class="media d-flex">
                                                    <div class="align-self-center">
                                                        <i class="icon-grid text-white font-large-2 float-left"></i>
                                                    </div>
                                                    <div class="media-body text-white text-right">
                                                        <h3 class="text-white">
                                                            <p id="total_rvr" class="d-inline">
                                                                {{ count($reason_validation_required) }}</p>
                                                            ({{ round($percantage_reason_validation_required) }}%)
                                                        </h3>
                                                        <span>Reason Validation Required </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
            
            
                                <div class="col-3">
                                    <div class="card bg-gradient-directional-complaints_launched pull-up cursor-pointer" id="search_sar_div">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <div class="media d-flex">
                                                    <div class="align-self-center">
                                                        <i class="icon-flag text-white font-large-2 float-left"></i>
                                                    </div>
                                                    <div class="media-body text-white text-right">
                                                        <h3 class="text-white">
                                                            <p id="total_sar" class="d-inline">
                                                                {{ count($shipper_advised_requested) }}</p>
                                                            ({{ round($percentage_shipper_advised_requested) }}%)
                                                        </h3>
                                                        <span>Shipper Advised Requested </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-3">
                                    <div class="card bg-gradient-directional-destination pull-up cursor-pointer" id="search_unresponsive_div">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <div class="media d-flex">
                                                    <div class="align-self-center">
                                                        <i class="la la-calculator text-white font-large-2 float-left"></i>
                                                    </div>
                                                    <div class="media-body text-white text-right">
                                                        <h3 class="text-white">
                                                            <p id="total_sar" class="d-inline">
                                                                {{ count($unresponsive_count) }}</p>
                                                        </h3>
                                                        <span>Unresponsive Count</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
            
                                <div class="col-3" >
                                    <div class="card bg-gradient-directional-in_transit pull-up cursor-pointer" id="number_of_pending_tickets_div">
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
                                                                {{ round($number_of_pending_ticket_percentage, 2) }}</p>%)
                                                        </h3>
                                                        <span>Pending Tickets</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
            
                                <div class="col-3" >
                                    <div class="card bg-gradient-directional-return_delivered pull-up cursor-pointer" id="number_of_inprocess_tickets_div">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <div class="media d-flex">
                                                    <div class="align-self-center">
                                                        <i class="icon-check text-white font-large-2 float-left"></i>
                                                    </div>
                                                    <div class="media-body text-white text-right">
                                                        <h3 class="text-white">
                                                            <p id="dead_leads" class="d-inline">{{ count($number_of_inprocess_tickets) }}</p> (
                                                            <p id="in_process_for_activation_percentage" class="d-inline">
                                                                {{ round($number_of_inprocess_tickets_percentage, 2) }}</p>
                                                            %)
                                                        </h3>
                                                        <span>No. of Inprocess Ticket</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
            
                                    <div class="col-3" >
                                        <div class="card bg-gradient-directional-complaints_launched pull-up cursor-pointer" id="number_of_available_agents_div">
                                            <div class="card-content">
                                                <div class="card-body">
                                                    <div class="media d-flex">
                                                        <div class="align-self-center">
                                                            <i class="icon-flag text-white font-large-2 float-left"></i>
                                                        </div>
                                                        <div class="media-body text-white text-right">
                                                            <h3 class="text-white">
                                                                <p id="received_leads" class="d-inline">{{ count($number_of_available_agents) }}</p>
                                                            </h3>
                                                            <span>Online/ Available Agents </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
            
                                    <div class="col-3" >
                                        <div class="card bg-gradient-directional-pending_confirmation pull-up cursor-pointer">
                                            <div class="card-content">
                                                <div class="card-body">
                                                    <div class="media d-flex">
                                                        <div class="align-self-center">
                                                            <i class="la la-hourglass text-white font-large-2 float-left"></i>
                                                        </div>
                                                        <div class="media-body text-white text-right">
                                                            <h3 class="text-white">
                                                                <p id="dead_leads" class="d-inline">
                                                                    {{ $average_aging > 24 ? (round($average_aging / 60, 2)) . ' days' : round($average_aging, 2) . ' hrs' }}
                                                                </p>                                                    
                                                            </h3>
                                                            <span>Average Aging.</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
            
                                    <div class="col-3" >
                                        <div class="card bg-gradient-directional-delivered pull-up cursor-pointer">
                                            <div class="card-content">
                                                <div class="card-body">
                                                    <div class="media d-flex">
                                                        <div class="align-self-center">
                                                            <i class="la la-hourglass text-white font-large-2 float-left"></i>
                                                        </div>
                                                        <div class="media-body text-white text-right">
                                                            <h3 class="text-white">
                                                                <p id="dead_leads" class="d-inline">
                                                                    {{ $average_response_time > 24 ? (round($average_response_time / 60, 2)) . ' days' : round($average_response_time, 2) . ' hrs' }}
                                                                </p>                                       
                                                            </h3>
                                                            <span>Average Response Time.</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
            
                                    <div class="col-3" >
                                        <div class="card bg-gradient-directional-oldest_shipment pull-up cursor-pointer" id="number_of_oldest_shipments_div">
                                            <div class="card-content">
                                                <div class="card-body">
                                                    <div class="media d-flex">
                                                        <div class="align-self-center">
                                                            <i class="la la-hourglass text-white font-large-2 float-left"></i>
                                                        </div>
                                                        <div class="media-body text-white text-right">
                                                            <h3 class="text-white">
                                                                <p id="dead_leads" class="d-inline">{{ $oldest_shipments }} </p>
                                                            </h3>
                                                            <span>Oldest Shipment Count.</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
    table.dataTable {
        font-size: 12px;
    }

    .cursor_color {
        cursor: pointer;
        color: #010a10;
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
        .bg-gradient-directional-delivered {
        background-image: linear-gradient(45deg, #653800, #11f1ea);
        background-repeat: repeat-x;
    }
    .bg-gradient-directional-booked_shipments {
        background-image: linear-gradient(45deg, #5e187b, #ed86ff);
        background-repeat: repeat-x;
    }

    .bg-gradient-directional-complaints_launched {
        background-image: linear-gradient(45deg, #074077, #2FBEF5);
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
    .bg-gradient-directional-return_delivered {
        background-image: linear-gradient(45deg, #02c123, #99ff12d1);
        background-repeat: repeat-x;
    }
    .bg-gradient-directional-pending_confirmation {
        background-image: linear-gradient(45deg, #6a1fa2, #ff4961);
        background-repeat: repeat-x;
    }

    .bg-gradient-directional-complaints_launched {
        background-image: linear-gradient(45deg, #074077, #2FBEF5);
        background-repeat: repeat-x;
    }

    .bg-gradient-directional-oldest_shipment {
        background-image: linear-gradient(45deg, #7f8b96, #f52f2f);
        background-repeat: repeat-x;
    }
    #toast-bottom-center.toast-container {
        text-align: center;
    }

    #toast-bottom-center.toast-container .toast {
        display: table;
        width: auto !important;
        text-align: left;
    }

    .selectize-control {
        width: 300px !important;
    }

    .goldClass {
        background-color: gold;
    }

    .GreenColor {
        background-color: #0aff00;
    }

    #search_rvr_div {
        z-index: 1; /* Set a higher z-index for the "Reason Validation Required" card */
    }

    #search_date_from,
    #search_date_to {
        position: relative;
        z-index: 0; /* Set a lower z-index for the date inputs */
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
                    search_date_to.pickadate('picker').set('select', new Date(current.toDate()),{muted:true});
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
        });
    </script>
    @endsection
