@extends('admin.layout.master')
@section('title', 'Shipment - Reason Validation Required')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
    <h1 class="mb-1">
        Shipment - Reason Validation Required
    </h1>



    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row justify-content-center">

                    <div class="col-3">
                        <select name="search_shipping_mode" id="search_shipping_mode" class="form-control select2">
                            @foreach ($shipping_mode as $mode)
                                <option value="{{ $mode->id }}">{{ $mode->mode }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col-5">
                        <form id="track_form" class="form-inline mb-1 " novalidate="novalidate">
                            <div class="form-group">
                                <input type="text" name="tracking_numbers" class="tracking_numbers"
                                    placeholder="Tracking Number(s)*" data-tags-input-name="tracking_number"
                                    data-rule-required="true" data-msg-required="Tracking Number is required">
                            </div>

                            <div class="form-group ml-1">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>
                        </form>
                    </div>
                </div>

                <input type="hidden" name="search_rvr_value_div" id="search_rvr_value_div">
                <input type="hidden" name="search_sar_value_div" id="search_sar_value_div">
                <input type="hidden" name="search_total_value_div" id="search_total_value_div">
                <input type="hidden" name="search_unresponsive_value_div" id="search_unresponsive_value_div">
                <input type="hidden" name="number_of_pending_tickets_value_div" id="number_of_pending_tickets_value_div">
                <input type="hidden" name="number_of_inprocess_tickets_value_div" id="number_of_inprocess_tickets_value_div">
                <input type="hidden" name="number_of_available_agents_value_div" id="number_of_available_agents_value_div">
                <input type="hidden" name="number_of_oldest_shipments_value_div" id="number_of_oldest_shipments_value_div">

                <div class="row justify-content-center" >
                    <div class="col-3">
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
                                                {{-- ({{ round($percentage_total_of_shipment) }}%) --}}
                                            </h3>
                                            <span>Total Of Shipments</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
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

                    
                    <div class="col justify-content-end mb-3">
                        <div class="card">
                            <div class="card-header">
                                <div class="heading-elements">
                                    <ul class="list-inline">
                                        <li class="primary border-primary round" value="0" id="star_shippers_filter">
                                            <a>
                                                Star Shippers</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li class="primary border-primary round"><a data-action="collapse">Filters <i
                                                    class="ft-plus"></i> </a> </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content collapse">
                                <div class="card-body p-1">
                                    <div class="row justify-content-end">
                                        <div class="col-md-6">
                                            <h4 class=" info">Filters</h4>
                                            <table class="table mb-0">
                                                <tbody>
                                                    <tr value="0" id="complaint_filter" class="complaint_row">
                                                        <td class="align-middle cursor_color">Complaint</td>
                                                    </tr>
                                                    <tr value="0" id="out_of_service_area_filter"
                                                        class=" nsa_osa_reason">
                                                        <td class="align-middle cursor_color">Out of Service Area</td>
                                                    </tr>
                                                    <tr value="0" id="shipment_re_attempt_request_filter"
                                                        class="goldClass">
                                                        <td class="align-middle cursor_color">Shipment - Re-Attempt
                                                            Requested / Re - Attempt Call Requested
                                                        </td>
                                                    </tr>
                                                    <tr value="0" id="try_buy_filter" class="tnb_row">
                                                        <td class="align-middle cursor_color">Try & Buy</td>
                                                    </tr>
                                                    <tr value="0" id="return_confirmation_pending_filter"
                                                        class="GreenColor">
                                                        <td class="align-middle cursor_color">Return Confirmation Pending
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    <div class="alert alert-warning d-none shipment_msg_error">
                    </div>

                    <div class="alert alert-success d-none shipment_msg_success">
                    </div>
                    </div>



                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                            <tr role="row" class="bg-primary white">

                                <th class="border-primary border-darken-1"></th>
                                <th class="border-primary border-darken-1">S. No.</th>
                                <th class="border-primary border-darken-1">Tracking No.</th>
                                <th class="border-primary border-darken-1">Order ID</th>
                                <th class="border-primary border-darken-1">Shipper Name</th>
                                <th class="border-primary border-darken-1">Shipper Phone(s)</th>
                                <th class="border-primary border-darken-1">Vendor</th>
                                <th class="border-primary border-darken-1">Origin</th>
                                <th class="border-primary border-darken-1">Destination</th>
                                <th class="border-primary border-darken-1">Hub</th>
                                <th class="border-primary border-darken-1">Zone</th>
                                <th class="border-primary border-darken-1">Consignee Name</th>
                                <th class="border-primary border-darken-1">Consignee Phone</th>
                                <th class="border-primary border-darken-1">Address</th>
                                <th class="border-primary border-darken-1">Sub Station</th>
                                <th class="border-primary border-darken-1">Collection Amount</th>
                                {{--   <th class="border-primary border-darken-1">RCP SMS Count</th> --}}
                                <th class="border-primary border-darken-1">Shipping Mode</th>
                                <th class="border-primary border-darken-1">Service Type</th>
                                <th class="border-primary border-darken-1">Status</th>
                                <th class="border-primary border-darken-1">Reason</th>
                                <th class="border-primary border-darken-1">Call Findings</th>
                                <th class="border-primary border-darken-1">Remarks</th>
                                <th class="border-primary border-darken-1">Shipper Remarks</th>
                                <th class="border-primary border-darken-1">OSA Estimated Charges</th>
                                <th class="border-primary border-darken-1">Arrival Date</th>
                                <th class="border-primary border-darken-1">Status Date</th>
                                <th class="border-primary border-darken-1">Status Updated</th>
                                <th class="border-primary border-darken-1">Confirmation Required</th>
                                <th class="border-primary border-darken-1">Confirmation On</th>
                                <th class="border-primary border-darken-1">Delivery Attempt Count</th>
                                <th class="border-primary border-darken-1">Re-Attempt Count</th>
                                <th class="border-primary border-darken-1">Assigned Agent</th>
                                <th class="border-primary border-darken-1">Assigned At</th>
                                <th class="border-primary border-darken-1">Assigned By</th>
                                <th class="border-primary border-darken-1">Consolidation</th>
                                <th class="border-primary border-darken-1">Consolidated IDs</th>
                                <th class="border-primary border-darken-1">Unresponsive Count</th>
                                <th class="border-primary border-darken-1">Unresponsive Call Time</th>
                                <th class="border-primary border-darken-1">Last Agent Name</th>
                                <th class="border-primary border-darken-1">Actions</th>
                            </tr>
                        </thead>
                    </table>

                </div>
            </div>
        </div>
        <div class="modal fade" id="excel_upload_modal" data-backdrop="static" role="dialog"
            aria-labelledby="excel_upload_modal" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="excel_upload_modal_title">Upload Excel</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <form id="return_status_form" class="form-horizontal" method="POST"
                            action="{{ route('admin.return.excel.store') }}" novalidate="novalidate"
                            enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="row align-items-center justify-content-center">
                                <div class="col">
                                    <div class="form-group">
                                        <input type="file" name="shipments" class="w-100 p-1 border-primary"
                                            title="Select File" data-rule-required="true"
                                            data-msg-required="File is required" data-rule-extension="xls|xlsx"
                                            data-msg-extension="Only file with extension xls or xlsx allowed"
                                            data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                            data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880"
                                            data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="form-group text-left">
                                        <button type="submit" name="upload" class="btn btn-primary">Upload</button>
                                    </div>
                                </div>

                                <div class="col ml-auto">
                                    <div class="form-group text-right">
                                        <a href="{{ asset('file/Trax Return Status Template.xlsx') }}"
                                            class="btn btn-primary"><i class="la la-download"></i> Download Template</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Upload Excel for Agent Assigning Modal --}}
        <div class="modal fade" id="agent_assign_modal" data-backdrop="static" role="dialog"
            aria-labelledby="agent_assign_modal" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="agent_assign_modal_title">Upload Excel for Agent Assigning</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <form id="assign_agent_form" class="form-horizontal" method="POST" action="{{ route('admin.return.excel.assign_agent_excel') }}" novalidate="novalidate" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="row align-items-center justify-content-center">
                                <div class="col">
                                    <div class="form-group">
                                        <input type="file" name="shipments" class="w-100 p-1 border-primary"
                                            title="Select File" data-rule-required="true"
                                            data-msg-required="File is required" data-rule-extension="xls|xlsx"
                                            data-msg-extension="Only file with extension xls or xlsx allowed"
                                            data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                            data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880"
                                            data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="form-group text-left">
                                        <button type="submit" name="upload" class="btn btn-primary">Upload</button>
                                    </div>
                                </div>

                                <div class="col ml-auto">
                                    <div class="form-group text-right">
                                        <a href="{{ asset('file/Trax Agent Assign Template.xlsx') }}"
                                            class="btn btn-primary"><i class="la la-download"></i> Download Template</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="row align-items-center justify-content-center">
                        <div class="col">
                            <table class="table table-bordered" id="agenttable">
                                <thead>
                                    <tr role="row" class="bg-primary white text-center">
                                        <th colspan="3" class="border-primary border-darken-1 ">Agents</th>
                                    </tr>
                                    <tr role="row" class="bg-primary bg-lighten-1 white">
                                        <th class="text-center border-primary border-lighten-2">Agent ID</th>
                                        <th class="border-primary border-lighten-2">Trax ID</th>
                                        <th class="border-primary border-lighten-2">Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($agents as $agent)
                                        <tr role="row">
                                            <td class="text-center">{{ $agent->id }}</td>
                                            <td>{{ $agent->trax_id }}</td>
                                            <td>{{ $agent->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="EditEstimateChargesModal" role="dialog" aria-labelledby="EditEstimateChargesModal"
            aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Estimate Charges</h4>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <form id="update_charges_form" class="form-horizontal mb-1 justify-content-center"
                            novalidate="novalidate">

                            <div class="form-group">
                                <input type="text" name="estimate_charges" id="estimated_charges_input"
                                    class="form-control decimal" placeholder="Enter Estimate Charges"
                                    data-rule-required="true" data-msg-required="Estimate Charge is required">

                            </div>
                            <input type="hidden" id="eec_shipment_id">
                            <div class="form-group ml-1">
                                <button type="submit" name="add" class="btn btn-primary update_charges"
                                    value="Add">Update Charges</button>
                                <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Close</button>

                            </div>
                        </form>

                    </div>

                </div>
            </div>
        </div>
        <div class="modal fade" id="EditEstimateChargesModalNSAreattempt" role="dialog" aria-labelledby="EditEstimateChargesModalNSAreattempt" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Estimated Charges for OSA Reattempt</h4>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <form id="update_charges_NSAreattempt_form" class="form-horizontal mb-1 justify-content-center"
                            novalidate="novalidate">

                            <div class="form-group">
                                <input type="text" name="estimate_charges" id="estimated_charges_NSAreattempt_input"
                                    class="form-control decimal" placeholder="Enter Estimate Charges"
                                    data-rule-required="true" data-msg-required="Estimate Charge is required">
                            </div>
                            <input type="hidden" id="eec_shipment_id_NSAreattempt">
                            <input type="hidden" id="eec_shipment_remark_NSAreattempt">
                            <div class="form-group ml-1">
                                <button type="submit" name="add" class="btn btn-primary update_charges"
                                    value="Add">Re-attempt</button>
                                <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Close</button>

                            </div>
                        </form>

                    </div>

                </div>
            </div>
        </div>

        {{-- Return COnfirm Modal on header confirm button click --}}
        <div class="modal fade" id="ReturnConfirmReasonModal" data-backdrop="static" role="dialog" aria-labelledby="ReturnConfirmReasonModal" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Return Confirm Reason</h4>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <form id="update_return_reason_form" class="form-horizontal mb-1 justify-content-center"
                            novalidate="novalidate">
                            <div class="form-group">
                                @if ($return_confirm_reasons)
                                    <select id="return_reason_select" data-rule-required="true"
                                        data-msg-required="Reason is required">
                                        @foreach ($return_confirm_reasons as $reason)
                                            <option value="{{ $reason->id }}">{{ $reason->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="form-group">
                                @if ($consignee_refused_reasons)
                                    <fieldset class="form-group d-none">
                                        <select name="consignee_refused_reasons" id="consignee_refused_reasons"
                                            name="consignee_refused_reasons" class="form-control select2">
                                            @foreach ($consignee_refused_reasons as $reasons)
                                                <option value="{{ $reasons->id }}">{{ $reasons->reasons }}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                @endif
                            </div>
                            <div class="form-group">
                                <input type="text" id="return_reason_shipment_remarks" maxlength="100"
                                    class="form-control" placeholder="Remarks">
                            </div>

                            <div class="form-group ml-1">
                                <button type="submit" name="add" id="btnReturn"
                                    class="btn btn-primary update_return_confirm" value="Add">Update To Return
                                    Confirm</button>
                                <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Close</button>

                            </div>
                        </form>

                    </div>

                </div>
            </div>
        </div>
        <div class="modal fade" id="ReturnConfirmReasonSingleModal" data-backdrop="static" role="dialog"
            aria-labelledby="ReturnConfirmReasonSingleModal" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Return Confirm Reason</h4>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <form id="single_update_return_reason_form" class="form-horizontal mb-1 justify-content-center"
                            novalidate="novalidate">
                            <input type="hidden" id="return_reason_shipment_id">
                            <div class="form-group">
                                @if ($return_confirm_reasons)
                                    <select id="single_return_reason_select" data-rule-required="true"
                                        data-msg-required="Reason is required">
                                        @foreach ($return_confirm_reasons as $reason)
                                            <option value="{{ $reason->id }}">{{ $reason->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="form-group">
                                @if ($consignee_refused_reasons)
                                    <fieldset class="form-group d-none">
                                        <select name="single_consignee_refused_reasons"
                                            id="single_consignee_refused_reasons" name="single_consignee_refused_reasons"
                                            class="form-control select2">
                                            @foreach ($consignee_refused_reasons as $reasons)
                                                <option value="{{ $reasons->id }}">{{ $reasons->reasons }}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                @endif
                            </div>
                            <div class="form-group">
                                <input type="text" id="return_reason_shipment_remarks_single" maxlength="100"
                                    class="form-control" placeholder="Remarks">
                            </div>

                            <div class="form-group ml-1">
                                <button type="button" name="add"
                                    class="btn btn-primary single_update_return_confirm"
                                    id="single_reason_update_btn">Update To Return Confirm</button>
                                <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Close</button>

                            </div>
                        </form>

                    </div>

                </div>
            </div>
        </div>

        {{-- <div class="modal fade text-left" id="AssignAgentModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AssignAgentModal" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="">Assign Agent</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group text-left">
                                <select name="select_emp_type" class="form-control select2" id="select_emp_type" required>
                                    @foreach ($staff_types as $staff_type)
                                        <option value="{{ $staff_type->id }}">{{ $staff_type->name }}</option>
                                    @endforeach
                                </select>
                        </div>
                        <select name="assign_agent" id="assign_agent" class="form-control select2">
                            @foreach ($agents as $agent)
                                <option value="{{ $agent->id }}"> {{ $agent->name }} - {{ $agent->trax_id }} - {{ $agent->city_name }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <span id="error_emp_type" style="color: red;"></span>
                        <button type="button" class="btn btn-success" id="assign_agentSubmit">Assign</button>
                        <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div> --}}

        {{-- assign agent modal --}}
        <div class="modal fade" id="AssignAgentModal" data-backdrop="static" role="dialog"
            aria-labelledby="AssignAgentModal" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Assign Agent</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                            <div class="form-group text-left">
                                <select name="select_emp_type" class="form-control select2" id="select_emp_type" required>
                                    @foreach ($staff_types as $staff_type)
                                        <option value="{{ $staff_type->id }}">{{ $staff_type->name }}</option>
                                    @endforeach
                                </select>
                                <div id="error_staff_type" class="text-danger"></div>
                            </div>
                            <div class="form-group text-left assign_agent_container d-none">
                                <select name="assign_agent" id="assign_agent" class="form-control assign_agent select2" required>
                                </select>
                                <div id="error_assign_agent" class="text-danger"></div>
                            </div>

                            

                            <div class="form-group ml-1 ">
                                {{-- <button type="submit" name="add" id="btnReturn" class="btn btn-primary update_return_confirm" value="Add">Update Call History</button> --}}
                                <button type="submit" name="add" id="assign_agentSubmit" class="btn btn-primary update_assign_agent" value="Add">Assign</button>
                                <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Close</button>
                            </div>

                        {{-- </form> --}}
                    </div>
                </div>
            </div>
        </div>

         {{-- call history modal --}}
         <div class="modal fade" id="update_call_status_modal" data-backdrop="static" role="dialog"
         aria-labelledby="update_call_status_modal" aria-hidden="true">
         <div class="modal-dialog modal-md" role="document">
             <div class="modal-content">
                 <div class="modal-header">
                     <h4 class="modal-title">Call History</h4>

                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">×</span>
                     </button>
                 </div>
                 <div class="modal-body text-center">
                     <form id="update_call_status_form" class="form-horizontal mb-1 justify-content-center" novalidate="novalidate">
                         @csrf
                         <div class="form-group text-left">
                             <input type="hidden" id="shipment_id" value="">
                             <select name="call_finding_dropdown" class="form-control select2"
                                 id="call_finding_dropdown" data-rule-required="true"
                                 data-msg-required="Call Finding is required">
                                 <option value="6">Unresponsive</option>  
                             </select>
                         </div>

                         <div class="form-group text-left sub_status_call_finding_container d-none">
                             <select name="sub_status_call_finding" class="form-control select2" id="sub_status_call_finding">
                                 @foreach ($sub_status_call_finding as $sscf)
                                     <option value="{{ $sscf->id }}">{{ $sscf->name }}</option>
                                 @endforeach
                             </select>
                         </div>

                         <div class="form-group text-left custom_remark_container d-none">
                             <input type="text" id="custom_remark" name="custom_remark" class="form-control"
                                 placeholder="Enter Other Text*">
                         </div>

                         <div class="form-group text-left">
                             <select name="call_to" class="form-control select2" id="call_to"
                                 data-rule-required="true" data-msg-required="Call To is required">
                                 {{-- <option value="0">Shippper</option> --}}
                                 <option value="1">Consigneee</option>
                             </select>
                         </div>

                         <div class="form-group ml-1 ">
                             <button type="submit" name="add" id="btnReturn"
                                 class="btn btn-primary update_return_confirm" value="Add">Update Call
                                 History</button>
                             <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Close</button>
                         </div>

                     </form>
                 </div>
             </div>
         </div>
     </div>

     {{-- call history log modal --}}
     <div class="modal fade" id="call_history_modal" role="dialog" aria-labelledby="call_history_modal_title"
         aria-hidden="true">
         <div class="modal-dialog modal-xl" role="document">
             <div class="modal-content">
                 <div class="modal-header text-center">
                     <h4 class="modal-title font-weight-bold" id="shipments_title">Remarks Log</h4>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">×</span>
                     </button>

                 </div>
                 <div class="modal-body text-center">
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                 </div>
             </div>
         </div>
     </div>


        <div class="modal fade text-left" id="ConsigneeInformationModal" data-backdrop="static" tabindex="-1"
            role="dialog" aria-labelledby="ConsigneeInformationModal" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="">Consignee</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                   