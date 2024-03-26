@extends('admin.layout.master')
@section('title', 'Dashboard - Shipment - Reason Validation Required')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
    <h1 class="mb-1">
        Shipment - Reason Validation Required - Dashboard
    </h1>



    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
            @if (session('role_id') == 1 || in_array(943, session('permissions')))

                <div class="row justify-content-center" >

                    {{-- Cards --}}

                        <div class="col-3">
                            <div class="card bg-gradient-directional-in_transit pull-up " id="search_total_div">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-clock text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="total_of_shipments">
                                                    0
                                                </h3>
                                                <span>Total Of Shipments</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-3" id="search_rvr_div">
                            <div class="card bg-gradient-directional-booked_shipments pull-up ">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-grid text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white">
                                                    <span id="total_rvr"></span> / <span id="percentage_reason_validation_required"></span>%
                                                </h3>
                                                <span>Reason Validation Required</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="card bg-gradient-directional-complaints_launched pull-up " id="search_sar_div">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-flag text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white">
                                                    <span id="shipper_advised_requested"></span> / <span id="percentage_shipper_advised_requested"></span>%
                                                </h3>
                                                <span>Shipper Advised Requested </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> 

                        <div class="col-3">
                            <div class="card bg-gradient-directional-destination pull-up " id="search_unresponsive_div">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white">
                                                    <span id="reattempt_call_requested">-</span> / <span id="percentage_reattempt_call_requested">-</span>%
                                                </h3>
                                                <span>Re-attempt Call Requested</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-3" >
                            <div class="card bg-gradient-directional-in_transit pull-up ">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-clock text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white">
                                                    <span id="number_of_pending_first_call"></span> / <span id="number_of_pending_first_call_percentage"></span>%
                                                </h3>
                                                <span>Pending First Call</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-3" >
                            <div class="card bg-gradient-directional-return_delivered pull-up " id="number_of_inprocess_tickets_div">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-check text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white">
                                                    <span id="number_of_pending_second_call"></span> / <span id="number_of_pending_second_call_percentage"></span>%
                                                </h3>
                                                <span>Pending Second Call</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-3" >
                            <div class="card bg-gradient-directional-complaints_launched pull-up " id="number_of_available_agents_div">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-flag text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white">
                                                    <span id="online_agents"></span> / <span id="number_of_available_agents"></span>
                                                </h3>
                                                <span>Total Agents </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-3" >
                            <div class="card bg-gradient-directional-pending_confirmation pull-up ">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="la la-hourglass text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white">
                                                    <span id="average_aging"></span>
                                                </h3>
                                                <span>Online/ Available Agents.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- <div class="col-3" >
                            <div class="card bg-gradient-directional-delivered pull-up ">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="la la-hourglass text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white">
                                                    <span id="average_response_time"></span>
                                                </h3>
                                                <span>Total Get Ticket.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}

                        {{-- <div class="col-3" >
                            <div class="card bg-gradient-directional-oldest_shipment pull-up " id="number_of_oldest_shipments_div">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="la la-hourglass text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="oldest_shipments">
                                                    0
                                                </h3>
                                                <span>Oldest Shipment Count.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    
                    

                </div>
                <div class="row justify-content-center" >
                <div class="col-3">
                            <div class="card bg-gradient-directional-in_transit pull-up " id="search_total_div">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-clock text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="total_of_shipments">
                                                    0
                                                </h3>
                                                <span>Total Get Ticket.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-3" id="search_rvr_div">
                            <div class="card bg-gradient-directional-booked_shipments pull-up ">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-grid text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white">
                                                    <span id="total_rvr"></span> / <span id="percentage_reason_validation_required"></span>%
                                                </h3>
                                                <span>Average Ticket Per Agent.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="card bg-gradient-directional-complaints_launched pull-up " id="search_sar_div">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-flag text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white">
                                                    <span id="shipper_advised_requested"></span> / <span id="percentage_shipper_advised_requested"></span>%
                                                </h3>
                                                <span>Average First Call Time.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> 

                        <div class="col-3">
                            <div class="card bg-gradient-directional-destination pull-up " id="search_unresponsive_div">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white">
                                                    <span id="reattempt_call_requested">-</span> / <span id="percentage_reattempt_call_requested">-</span>%
                                                </h3>
                                                <span>Average Aging</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>            
            @endif

            </div>
        </div>

    @endsection

    @section('css')
        <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">
        <style>
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
        </style>
    @endsection

    @section('js')
        <script src="{{ asset('app-assets/vendors/js/forms/validation/additional-methods.min.js') }}" type="text/javascript">
        </script>
        <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}" type="text/javascript"></script>

        <script type="text/javascript">

            $(document).ready(function() {

                function fetchData() {
                $.ajax({
                        url: '{!! route('admin.return.data') !!}',
                        method: 'GET'
                    }).done(function (data) 
                    {
                       if(data.status){
                            $('#total_of_shipments').text(data.stats.total_of_shipments);
                            $('#total_rvr').text(data.stats.reason_validation_required);
                            $('#percentage_reason_validation_required').text(data.stats.percentage_reason_validation_required);
                            $('#shipper_advised_requested').text(data.stats.shipper_advised_requested);
                            $('#percentage_shipper_advised_requested').text(data.stats.percentage_shipper_advised_requested);
                            $('#reattempt_call_requested').text(data.stats.reattempt_call_requested);
                            $('#percentage_reattempt_call_requested').text(data.stats.percentage_reattempt_call_requested);
                            $('#number_of_pending_first_call').text(data.stats.number_of_pending_first_call);
                            $('#number_of_pending_first_call_percentage').text(data.stats.number_of_pending_first_call_percentage);
                            $('#number_of_pending_second_call').text(data.stats.number_of_pending_second_call);
                            $('#number_of_pending_second_call_percentage').text(data.stats.number_of_pending_second_call_percentage);
                            $('#online_agents').text(data.stats.online_agents);
                            $('#number_of_available_agents').text(data.stats.number_of_available_agents);

                            var averageAging = parseFloat(data.stats.average_aging);
                            var content = averageAging > 24 ? (Math.round(averageAging / 60 * 100) / 100) + ' days' : Math.round(averageAging * 100) / 100 + ' hrs';
                            $('#average_aging').text(content);

                            //var average_response_time = parseFloat(data.stats.average_response_time);
                            //var average_response_time_content = average_response_time > 24 ? (Math.round(average_response_time / 60 * 100) / 100) + ' days' : Math.round(average_response_time * 100) / 100 + ' hrs';
                            //$('#average_response_time').text(average_response_time_content);

                            // $('#oldest_shipments').text(data.stats.oldest_shipments);
                       }
                    });
                }

                fetchData();
                
            });
        </script>
    @endsection
