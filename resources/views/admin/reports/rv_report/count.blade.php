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
                                                <input type="text" name="search_date_from" class="form-control bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Date From" data-rule-required="true" data-msg-required="Date(From) is required" data-value="{{ \Carbon\Carbon::now() }}">
                                                {{-- <input type="text" name="search_date_from" class="form-control bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Date (From)"  data-value="{{ \Carbon\Carbon::today()->subDays(31)->startOfDay() }}"> --}}
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
                                                <input type="text" name="search_date_to" class="form-control bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Date To" data-rule-required="true" data-msg-required="Date(To) is required" data-value="{{ \Carbon\Carbon::now() }}">
                                                {{-- <input type="text" name="search_date_to" class="form-control bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Date (To)" data-value="{{ \Carbon\Carbon::now() }}"> --}}
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
                                                            <p id="reason_validation_required" class="d-inline">
                                                                {{-- {{ isset($reason_validation_required) ? count($reason_validation_required) : 0 }} --}}
                                                                0
                                                            </p>
                                                        </h3>
                                                        <span>Reason Validation Required </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-3">
                                    <div class="card bg-gradient-directional-in_transit pull-up cursor-pointer" id="intercepted_div">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <div class="media d-flex">
                                                    <div class="align-self-center">
                                                        <i class="icon-clock text-white font-large-2 float-left"></i>
                                                    </div>
                                                    <div class="media-body text-white text-right">
                                                        <h3 class="text-white">
                                                            <p id="intercepted" class="d-inline">
                                                                {{-- {{ isset($intercepted) ? ($intercepted) : 0 }} --}}
                                                                0
                                                            </p>
                                                        </h3>
                                                        <span>Intercepted</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-3" >
                                    <div class="card bg-gradient-directional-in_transit pull-up cursor-pointer" id="reattempted_div">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <div class="media d-flex">
                                                    <div class="align-self-center">
                                                        <i class="icon-clock text-white font-large-2 float-left"></i>
                                                    </div>
                                                    <div class="media-body text-white text-right">
                                                        <h3 class="text-white">
                                                            <p id="reattempted" class="d-inline">
                                                                {{-- {{ isset($reattempted) ? count($reattempted) : 0 }} --}}
                                                                0
                                                            </p>
                                                        </h3>
                                                        <span>Reattempted</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-3" >
                                    <div class="card bg-gradient-directional-return_delivered pull-up cursor-pointer" id="returned_div">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <div class="media d-flex">
                                                    <div class="align-self-center">
                                                        <i class="icon-check text-white font-large-2 float-left"></i>
                                                    </div>
                                                    <div class="media-body text-white text-right">
                                                        <h3 class="text-white">
                                                            <p id="returned" class="d-inline">
                                                                {{-- {{ isset($returned) ? count($returned) : 0 }} --}}
                                                                0
                                                            </p> 
                                                            {{-- (<p id="in_process_for_activation_percentage" class="d-inline">
                                                                {{ round($number_of_inprocess_tickets_percentage, 2) }}</p>
                                                            %) --}}
                                                        </h3>
                                                        <span>Returned</span>
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
                                                            <p id="on_hold" class="d-inline">
                                                                {{-- {{ count($number_of_available_agents) }} --}}
                                                                {{-- {{ isset($on_hold) ? count($on_hold) : 0 }} --}}
                                                                0
                                                            </p>
                                                        </h3>
                                                        <span>On Hold</span>
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
                                                            <p id="unresponsive" class="d-inline">
                                                                {{-- {{ isset($unresponsive) ? count($unresponsive) : 0 }} --}}
                                                                0
                                                            </p>
                                                        </h3>
                                                        <span>Unresponsive Count</span>
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
                                                            <p id="shipper_advised_requested" class="d-inline">
                                                                0
                                                                {{-- {{ count($shipper_advised_requested) }} --}}
                                                                {{-- {{ isset($shipper_advised_requested) ? count($shipper_advised_requested) : 0 }} --}}
                                                            </p>
                                                            {{-- ({{ round($percentage_shipper_advised_requested) }}%) --}}
                                                        </h3>
                                                        <span>Shipper Advised Requested</span>
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
    <div class="loader"></div>
@endsection

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/fonts/line-awesome/css/line-awesome.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/fonts/simple-line-icons/style.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/cryptocoins/cryptocoins.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css') }}">
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
<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>


    <script>
        $(document).ready(function() {
            var from_date = $('#search_date_from').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#search_date_from_root').css('top','40px');
                },
                onSet: function(context) {

                var old_date_formatted = $('input[name="search_date_from_formatted"]').val();
                to_date.pickadate('picker').set('select', new Date(old_date_formatted),{muted:true});
                }
            });

            var to_date = $('#search_date_to').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format:'dd mmmm, yyyy',
                // max: '{{ Carbon\Carbon::now() }}',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#search_date_to_root').css('top', '40px');
                }
            });

            $('#search_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    $('#table').removeClass('d-none');

                    var search_date_from = $('input[name="search_date_from_formatted"]').val();
                    var search_date_to = $('input[name="search_date_to_formatted"]').val();

                    if (search_date_from != '' && search_date_to != '') {
                        $.ajax({
                            url: '{!! route('admin.reports.rv_action_count_report.fetch') !!}',
                            method: 'POST',
                            headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                search_date_from: search_date_from,
                                search_date_to: search_date_to
                            },
                            dataType: 'json',
                            beforeSend:function(){
                                $(".loader").append(data_table_loader);
                            },
                            success: function(response) {
                                var reason_validation_required = response.reason_validation_required;
                                if (reason_validation_required > 0) {
                                    $('#reason_validation_required').text(reason_validation_required);
                                } else {
                                    $('#reason_validation_required').text('0');
                                }
                                var shipper_advised_requested = response.shipper_advised_requested;
                                if (shipper_advised_requested > 0) {
                                    $('#shipper_advised_requested').text(shipper_advised_requested);
                                } else {
                                    $('#shipper_advised_requested').text('0');
                                }
                                var intercepted = response.intercepted;
                                if (intercepted > 0) {
                                    $('#intercepted').text(intercepted);
                                } else {
                                    $('#intercepted').text('0');
                                }
                                var reattempted = response.reattempted;
                                if (reattempted > 0) {
                                    $('#reattempted').text(reattempted);
                                } else {
                                    $('#reattempted').text('0');
                                }
                                var returned = response.returned;
                                if (returned > 0) {
                                    $('#returned').text(returned);
                                } else {
                                    $('#returned').text('0');
                                }
                                var on_hold = response.on_hold;
                                if (on_hold > 0) {
                                    $('#on_hold').text(on_hold);
                                } else {
                                    $('#on_hold').text('0');
                                }
                                var unresponsive = response.unresponsive;
                                if (unresponsive > 0) {
                                    $('#unresponsive').text(unresponsive);
                                } else {
                                    $('#unresponsive').text('0');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr, status, error);
                            },
                            complete:function(){
                                $(".loader").empty();
                            }
                        });
                    }
                }
            });
        });
    </script>
    @endsection
