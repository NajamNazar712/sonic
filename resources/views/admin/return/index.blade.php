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

                {{-- <form id="search_rvr" class="card-body card-dashboard" novalidate="novalidate"> --}}
                <div class="row justify-content-center" >
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
                    {{-- </form> --}}


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
                                                ({{ round($percentage_unresponsive_count) }}%)
                                            </h3>
                                            <span>Unresponsive Count</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col justify-content-end mb-5">
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
                                                            Requested
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
                        
                    <div class="alert alert-danger d-none shipment_msg_error">
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
                        <form id="assign_agent_form" class="form-horizontal" method="POST"
                            action="{{ route('admin.return.excel.assign_agent_excel') }}" novalidate="novalidate"
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
                                        <th colspan="2" class="border-primary border-darken-1">Agents</th>
                                    </tr>
                                    <tr role="row" class="bg-primary bg-lighten-1 white">
                                        <th class="text-center border-primary border-lighten-2">ID</th>
                                        <th class="border-primary border-lighten-2">Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($agents as $agent)
                                        <tr role="row">
                                            <td class="text-center">{{ $agent->id }}</td>
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
        <div class="modal fade" id="EditEstimateChargesModalNSAreattempt" role="dialog"
            aria-labelledby="EditEstimateChargesModalNSAreattempt" aria-hidden="true">
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

        <div class="modal fade" id="ReturnConfirmReasonModal" data-backdrop="static" role="dialog"
            aria-labelledby="ReturnConfirmReasonModal" aria-hidden="true">
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

        <div class="modal fade text-left" id="AssignAgentModal" data-backdrop="static" tabindex="-1" role="dialog"
            aria-labelledby="AssignAgentModal" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="">Assign Agent</h4>
                    </div>
                    <div class="modal-body">
                        <select name="Sale_person" id="assign_agent" class="form-control select2">
                            @foreach ($agents as $agent)
                                <option value="{{ $agent->id }}"> {{ $agent->name }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="assign_agentSubmit">Assign</button>
                        <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
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
                    <div class="modal-body">
                        <div id="consignee_info_div"></div>
                        <form id="label_update_form" class="mb-1 mt-2" method="POST"
                            action="{{ route('admin.settings.blacklist.search.update') }}" novalidate="novalidate">
                            {{ csrf_field() }}

                            <input type="hidden" id="consignee_information_id" name="consignee_information_id">
                            <div class="row justify-content-center">
                                <div class="col-4">
                                    <div class="form-group">
                                        <select name="label_select" id="label_select" class="form-control"
                                            data-rule-required="true" data-msg-required="This field is required">
                                            @foreach ($blacklists as $blacklist)
                                                <option value="{{ $blacklist->id }}">{{ $blacklist->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <div class="form-group">
                                        <button type="submit" name="action" class="btn btn-primary btn-block"
                                            id="label_btn" value="label">Label</button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade text-left" id="send_sms_modal" data-backdrop="static" tabindex="-1" role="dialog"
            aria-labelledby="send_sms_modal" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h4 class="modal-title white" id="">Send SMS</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="send_sms_form" method="POST" novalidate="novalidate">
                            @csrf
                            <input type="hidden" name="id" class="id" id="id">
                            <input type="hidden" name="send_via" class="send_via" id="send_via" value="auto">
                            <div class="row justify-content-center">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
                                    <div class="form-group">
                                        <label for="send_sms_checkbox"
                                            class="font-medium-2 font-weight-bold mr-1">Auto</label>
                                        <input type="checkbox" name="send_sms_checkbox" id="send_sms_checkbox"
                                            class="switchery send_sms_checkbox" data-size="sm" data-switchery="true">
                                        <label for="send_sms_checkbox"
                                            class="font-medium-2 font-weight-bold ml-1">Manual</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center mannual_sms d-none">
                                <div class="col-6">
                                    <div class="form-group input-group text-center">
                                        <label class="font-medium-2 font-weight-bold block ">Send Message TO</label>
                                        <select name="shipper_consignee" id="shipper_consignee"
                                            class="form-control select2" data-msg-required="Select Shipper/Consignee"
                                            data-rule-required="true">
                                            <option value="shipper">Shipper</option>
                                            <option value="consignee">Consignee</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 text-center">
                                    <div class="form-group input-group text-center">
                                        <label class="font-medium-2 font-weight-bold block">Message</label>
                                        <textarea id="send_mannual_message" name="send_mannual_message" disabled class="form-control"
                                            placeholder="Type Your Message*" rows="5" data-rule-required="true"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">

                                <div class="col-12 form-group text-center">
                                    <button type="submit" class="btn btn-primary btn-block">Send</button>
                                </div>
                            </div>
                        </form>
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
                        <form id="update_call_status_form" class="form-horizontal mb-1 justify-content-center"
                            novalidate="novalidate">
                            @csrf
                            <div class="form-group text-left">
                                <input type="hidden" id="shipment_id" value="">
                                <select name="call_finding_dropdown" class="form-control select2"
                                    id="call_finding_dropdown" data-rule-required="true"
                                    data-msg-required="Call Finding is required">

                                    {{-- id 6 is for unresposive is in rv_assign_agent_statuses table --}}
                                    <option value="6">Unresponsive</option>  
                                </select>
                            </div>

                            <div class="form-group text-left sub_status_call_finding_container d-none">
                                <select name="sub_status_call_finding" class="form-control select2"
                                    id="sub_status_call_finding">
                                    @foreach ($sub_status_call_finding as $sscf)
                                        <option value="{{ $sscf->id }}">{{ $sscf->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group text-left custom_remark_container d-none">
                                <input type="text" id="custom_remark" name="custom_remark" class="form-control"
                                    placeholder="Enter Other Text">
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


    @endsection

    @section('css')
        <link rel="stylesheet" type="text/css"
            href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
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
        <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript">
        </script>
        <script src="{{ asset('app-assets/vendors/js/forms/validation/additional-methods.min.js') }}" type="text/javascript">
        </script>
        <script src="{{ asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js') }}"
            type="text/javascript"></script>
        <script src="{{ asset('app-assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('js/datatable_buttons.js') }}" type="text/javascript"></script>

        <script type="text/javascript">
            var selected_rows = [];
            var restricted_rows = [];
            var tableagent = $('#agenttable').DataTable({
                scrollY: '200px',
            });
            $('#agent_assign_modal').on('shown.bs.modal', function() {
                tableagent.columns.adjust();
            });
            @php
                $permission = in_array(490, session('permissions'));
                if ($permission) {
                    $permission = 1;
                } else {
                    $permission = 0;
                }
            @endphp
            $(document).ready(function() {

                // update_call_status_modal function
                $('#update_call_status_modal').on('shown.bs.modal', function() {
                    $('#call_to').val('').change();
                    $('#custom_remark').val('');
                    $('#sub_status_call_finding').val('').change();
                    $('#call_finding_dropdown').val('').change();
                });

                $('#update_call_status_form').validate({
                    errorClass: 'danger',
                    successClass: 'success',
                    errorPlacement: function(error, element) {
                        error.addClass('w-100').appendTo(element.parent('.form-group'));
                    },
                    submitHandler: function(form) {
                        var data = {
                            shipment_id: $('#shipment_id').val(),
                            call_finding_id: $('#call_finding_dropdown').val(),
                            sub_status_call_finding_id: $('#sub_status_call_finding').val(),
                            remarks: $('#custom_remark').val(),
                            call_to_id: $('#call_to').val(),
                            '_token': '{{ csrf_token() }}'
                        };
                        // AJAX request
                        $.ajax({
                            url: "{{ route('admin.return.update_call_status') }}",
                            type: 'POST',
                            data: data,
                            success: function(response) {
                                if (response.status == 1) {
                                    swal({
                                        text: 'Call History Updated Successfully',
                                        icon: 'success',
                                        closeOnClickOutside: false,
                                        closeOnEsc: false
                                    });
                                    $('#update_call_status_modal').modal('hide');
                                } else {
                                    swal({
                                        title: 'Something Went Wrong!',
                                        text: 'Please Update Status Again',
                                        icon: 'error',
                                        closeOnClickOutside: false,
                                        closeOnEsc: false
                                    });
                                }
                                table.draw();
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.error('Form submission failed:', textStatus,
                                    errorThrown);
                            }
                        });
                    }
                });

                // End update_call_status_modal function

                // call_history_modal datatable function
                $('#datatable').on('click', 'tr td.remarks button', function() {
                    var shipment_id = table.row($(this).parents('tr')).data().shId;

                    $('#call_history_modal .modal-body').html('');
                    $('#call_history_modal').modal('show');

                    $.ajax({
                            url: '{!! route('admin.return.call_status_history') !!}',
                            method: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'shipment_id': shipment_id
                            }
                        })
                        .done(function(data) {
                            if (data) {
                                var modalBody = $('#call_history_modal .modal-body');
                                modalBody.html('');

                                var tableHtml =
                                    '<table id="call_history_table" class="table-striped table-bordered" style="width:100%">';
                                tableHtml +=
                                    '<thead class="text-center"><tr><th class="p-1">Calling Date</th><th>Calling Time</th><th>Call Finding</th><th>Call Finding Reason</th><th>Remarks</th><th>Call To</th><th>Status</th><th>User</th></tr></thead>';
                                tableHtml += '<tbody class="text-center">';
                                $.each(data, function(index, value) {
                                    var updated_at = value.updated_at;
                                    var trimmedDateTime = updated_at.substring(0, 10);
                                    var trimmedTime = updated_at.substring(11, 16);
                                    var call_finding_id = value.call_finding_id;
                                    var call_finding_reason_id = value.call_finding_reason_id;
                                    
                                    var remarks = value.remarks;
                                    if (remarks == null) {
                                        remarks = '-';
                                    }
                                    var current_shipment_status = value.current_shipment_status;
                                    var updated_by = value.updated_by;

                                    var call_to_id = value.call_to_id;
                                    if (call_to_id == 1) {
                                        call_to_id = 'Consignee'
                                    } else {
                                        call_to_id = 'Shipper'
                                    }
                                    // var call_finding_id = value.call_finding_id;
                                    // if (call_finding_id == 1) {
                                    //     call_finding_id = 'Un-responsive'
                                    // } else {
                                    //     call_finding_id = ''
                                    // }

                                    tableHtml += '<tr><td class="p-1">' + trimmedDateTime +
                                        '</td><td>' + trimmedTime + '</td><td>' + call_finding_id + '</td><td>' + call_finding_reason_id +
                                        '</td><td>' + remarks + '</td><td>' + call_to_id + '</td><td>' + current_shipment_status +
                                        '</td><td>' + updated_by + '</td></tr>';
                                });

                                tableHtml += '</tbody></table>';

                                modalBody.append(tableHtml);

                                $('#call_history_modal').modal('show');
                            }
                        });
                });
                $('#search_rvr_div').on('click', function() {
                    $('#search_rvr_value_div').val(1);
                    $('#search_sar_value_div').val('');
                    $('#search_total_value_div').val('');
                    $('#search_unresponsive_value_div').val('');


                    // Assuming 'table' is defined elsewhere in your code
                    table.draw();
                });

                $('#search_sar_div').on('click', function() {
                    $('#search_sar_value_div').val(2);
                    $('#search_rvr_value_div').val('');
                    $('#search_total_value_div').val('');
                    $('#search_unresponsive_value_div').val('');

                    // Assuming 'table' is defined elsewhere in your code
                    table.draw();
                });

                $('#search_total_div').on('click', function() {
                    $('#search_total_value_div').val(3);
                    $('#search_sar_value_div').val('');
                    $('#search_rvr_value_div').val('');
                    $('#search_unresponsive_value_div').val('');

                    // Assuming 'table' is defined elsewhere in your code
                    table.draw();
                });


                $('#search_unresponsive_div').on('click', function() {
                    $('#search_unresponsive_value_div').val(4);
                    $('#search_total_value_div').val('');
                    $('#search_sar_value_div').val('');
                    $('#search_rvr_value_div').val('');

                    // Assuming 'table' is defined elsewhere in your code
                    table.draw();
                });


                // End call_history_modal datatable function


                // update_call_status_modal Validations
                $("#call_finding_dropdown").change(function() {
                    var selectedValue = $(this).val();

                    if (selectedValue === '6') {
                        $('.sub_status_call_finding_container').removeClass('d-none');
                        $('#sub_status_call_finding').attr('data-rule-required', true);
                        $('#sub_status_call_finding').attr('data-msg-required', 'Call Finding is required');
                    } else {
                        $('.sub_status_call_finding_container').addClass('d-none');
                        $('#sub_status_call_finding').removeAttr('data-rule-required', true);
                        $('#sub_status_call_finding').removeAttr('data-msg-required',
                            'Call Finding is required');
                    }


                });

                $("#sub_status_call_finding").change(function() {
                    var selectedValue = $(this).val();
                    if (selectedValue === '19') {
                        $('.custom_remark_container').removeClass('d-none');
                        $('#custom_remark').attr('data-rule-required', true);
                        $('#custom_remark').attr('data-msg-required', 'Other text is required');
                    } else {
                        $('.custom_remark_container').addClass('d-none');
                        $('#custom_remark').removeAttr('data-rule-required', true);
                        $('#custom_remark').removeAttr('data-msg-required', 'Other text is required');
                    }

                });


                $('#sub_status_call_finding').prepend('<option value="" selected="selected"></option>')
                    .select2({
                        width: '100%',
                        placeholder: 'Select Status',
                        allowClear: true,
                        dropdownParent: $('#update_call_status_form')
                    });

                $('#call_finding_dropdown').prepend('<option value="" selected="selected"></option>')
                    .select2({
                        width: '100%',
                        placeholder: 'Select Call Finding',
                        allowClear: true,
                        dropdownParent: $('#update_call_status_form')
                    });

                $('#call_to').prepend('<option value="" selected="selected"></option>')
                    .select2({
                        width: '100%',
                        placeholder: 'Select Call To',
                        allowClear: true,
                        dropdownParent: $('#update_call_status_form')
                    });

                // update_call_status_modal Validations

                // $('#send_sms_modal').modal('show');

                $('#send_sms_form #shipper_consignee').prepend('<option value="" selected="selected"></option>')
                    .select2({
                        width: '100%',
                        placeholder: 'Select Shipper/Consignee',
                        allowClear: true,
                        dropdownParent: $('#send_sms_form')
                    });

                $('#send_sms_checkbox').on('change', function() {

                    var send_sms_checkbox = document.querySelector('.switchery.send_sms_checkbox');

                    if (send_sms_checkbox.checked === true) {
                        $('#send_sms_form #send_via').val('manual');
                        $('.mannual_sms').removeClass('d-none');

                    } else if (send_sms_checkbox.checked === false) {
                        $('#send_sms_form #send_via').val('auto');
                        $('.mannual_sms').addClass('d-none');
                    }
                });

                $('#shipper_consignee').on('select2:unselect', function() {

                    $('#send_mannual_message').val('');
                    $('#send_mannual_message').attr('disabled', true);
                });

                $('#shipper_consignee').select2({
                    placeholder: 'Select Shipper/Consignee',
                    width: '100%',
                    allowClear: true
                }).bind('select2:select', function() {
                    $('#send_mannual_message').attr('disabled', false);
                });

                $('#send_sms_form').validate({
                    errorClass: 'danger',
                    successClass: 'success',
                    normalizer: function(value) {
                        return $.trim(value);
                    },
                    errorPlacement: function(error, element) {
                        error.addClass('w-100').appendTo(element.parent('.form-group'));
                    },
                    submitHandler: function(form) {

                        var form = $("#send_sms_form");
                        var id = $(form).find('#id').val();
                        var send_via = $(form).find('#send_via').val();
                        var send_to = $(form).find('#shipper_consignee').val();
                        var message = $(form).find('#send_mannual_message').val();

                        if (id) {

                            swal({
                                title: 'Are You Sure?',
                                text: 'Select Yes to update Estimated Charges!',
                                icon: 'warning',
                                buttons: {
                                    cancel: {
                                        text: 'No',
                                        value: null,
                                        visible: true,
                                        closeModal: true,
                                    },
                                    confirm: {
                                        text: 'Yes',
                                        value: true,
                                        visible: true,
                                        closeModal: true
                                    }
                                },
                                closeOnClickOutside: false,
                                closeOnEsc: false,
                                dangerMode: true
                            }).then(function(confirm) {
                                if (confirm) {
                                    swal({
                                        text: 'Please Wait!',
                                        icon: 'info',
                                        buttons: false,
                                        closeOnClickOutside: false,
                                        closeOnEsc: false
                                    });

                                    $.ajax({
                                            url: '{!! route('admin.return.rcp_sms') !!}',
                                            method: 'POST',
                                            data: {
                                                '_token': '{{ csrf_token() }}',
                                                'id': id,
                                                'send_via': send_via,
                                                'send_to': send_to,
                                                'message': message
                                            }
                                        })
                                        .done(function(data) {
                                            if (data.status == 0) {
                                                toastr.success(data.success, 'Success!', {
                                                    positionClass: 'toast-bottom-center',
                                                    containerId: 'toast-bottom-center'
                                                });
                                            } else {
                                                toastr.error(data.error, 'Error!', {
                                                    positionClass: 'toast-top-center',
                                                    containerId: 'toast-top-center'
                                                });
                                            }
                                            $('#send_sms_modal').modal('hide');

                                            swal.close();

                                        });
                                }
                            });
                        }
                    }
                });

                $('#send_sms_modal').on('hide.bs.modal', function(e) {

                    if ($("#send_sms_checkbox").is(":checked")) {
                        $("#send_sms_checkbox").trigger('click');
                    }

                    $('#shipper_consignee').val(null).trigger('change');

                    $('#send_mannual_message').val('');
                    $('#send_mannual_message').attr('disabled', true);


                });

                $('#label_select').prepend('<option value="" selected="selected"></option>').select2({
                    width: '100%',
                    placeholder: 'Labeling*'
                });
                $("#assign_agent").prepend('<option value="" selected></option>').select2({
                    placeholder: "Select Agent",
                    width: '100%',
                    dropdownParent: $('#AssignAgentModal')
                });

                $("#consignee_refused_reasons").prepend('<option value="" selected></option>').select2({
                    placeholder: "Select Sub Reason",
                    width: '100%',
                    dropdownParent: $('#update_return_reason_form')
                });
                $("#single_consignee_refused_reasons").prepend('<option value="" selected></option>').select2({
                    placeholder: "Select Sub Reason",
                    width: '100%',
                    dropdownParent: $('#ReturnConfirmReasonSingleModal')
                });

                $('#search_shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
                    width: '100%',
                    placeholder: 'Shipping Mode',
                    allowClear: true
                }).bind('change', function() {
                    table.draw();
                });
                $('#return_reason_select').prepend('<option value="" selected="selected"></option>').select2({
                    width: '100%',
                    placeholder: 'Select Reason'
                });
                $('#single_return_reason_select').prepend('<option value="" selected="selected"></option>').select2({
                    width: '100%',
                    placeholder: 'Select Reason'
                });
                jQuery.fn.DataTable.Api.register('buttons.exportData()', function(options) {
                    if (this.context.length) {
                        body = [];
                        var params = table.ajax.params();
                        params.start = 0;
                        params.length = -1;
                        params.excel = true;
                        var jsonResult = $.ajax({
                            url: '{{ route('admin.return.list') }}',
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: params,
                            success: function(result) {
                                head = [];
                                head.push('S.No');
                                head.push('Tracking No.');
                                head.push('Order ID');
                                head.push('Shipper Name');
                                head.push('Shipper Phone');
                                head.push('Vendor');
                                head.push('Origin');
                                head.push('Destination');
                                head.push('Hub');
                                head.push('Consignee Name');
                                head.push('Consignee Phone');
                                head.push('Address');
                                head.push('Sub Station');
                                head.push('Collection Amount');
                                head.push('Shipping Mode');
                                head.push('Service Type');
                                head.push('Status');
                                head.push('Reason');
                                head.push('Call Findings');
                                head.push('Remarks');
                                head.push('Shipper Remarks');
                                head.push('OSA Estimated Charges');
                                head.push('Arrival Date');
                                head.push('Status Date');
                                head.push('Status Updated');

                                head.push('Confirmation Required');
                                head.push('Confirmation On');
                                head.push('Delivery Attempt Count');
                                head.push('Re-Attempt Count');
                                head.push('Assigned Agent');
                                head.push('Assigned At');
                                head.push('Assigned By');
                                head.push('Consolidation');
                                head.push('Consolidation IDs');

                                    $.each(result.data, function(index, values) {
                                    row = [];
                                    row.push(index + 1);
                                    row.push(values.tracking);
                                    row.push(values.order_id);
                                    row.push(values.shipper);
                                    row.push(values.shipper_phone);
                                    row.push(values.vendor_name);
                                    row.push(values.origin);
                                    row.push(values.destination);
                                    row.push(values.hub);
                                    row.push(values.consignee_name);
                                    row.push(values.consignee_phone_number_1 + '|' + values.consignee_phone_number_2);
                                    row.push(values.consignee_address);
                                    row.push(values.sub_station);
                                    row.push(values.amount);
                                    row.push(values.mode);
                                    row.push(values.service_type);
                                    row.push(values.status);
                                    row.push(values.reason);
                                    row.push(values.remarks_excel); //Remarks Count
                                    row.push(values.shipment_remarks_excel);
                                    row.push(values.shipper_remarks);
                                    row.push(values.nsa_osa_estimated_charges);
                                    row.push(values.arrival);
                                    row.push(values.last_status_date);
                                    row.push(values.reattemp_status_remarks);
                                    row.push(values.confirmation_req);
                                    row.push(values.confirmation_on);
                                    row.push(values.delivery_attempt);
                                    row.push(values.reattempts);
                                    row.push(values.assigned_agent);
                                    row.push(values.assigned_at);
                                    row.push(values.assigned_by);
                                    row.push(values.consolidation);
                                    row.push(values.consolidated_id);
                                    body.push(row);
                                    }
                                );
                            },
                            async: false
                        });

                        return {
                            body: body,
                            header: head
                        };
                    }
                });

                var return_confirm_reasons = @json($return_confirm_reasons);
                var shipment_remarks = {};
                var table = $('#datatable').DataTable({
                    dom: '<"d-inline-block"l><"pull-right"B>tipr',
                    @if (session('role_id') == 1 || count(array_intersect([45, 46, 316], session('permissions'))) !== 0)
                        buttons: [
                            @if (session('role_id') == 1 || in_array(316, session('permissions')))
                                {
                                    text: 'Assign Agent',
                                    className: 'btn btn-primary assign',
                                    enabled: false,
                                    action: function(e, dt, node, config) {
                                        if (selected_rows !== '') {
                                            $('#AssignAgentModal').modal('show');

                                            $('#assign_agentSubmit').on('click', function() {
                                                var assign = parseInt($('#assign_agent').val());
                                                swal({
                                                    text: 'Are you sure, you want to Assign these shipments(s)?',
                                                    icon: 'info',
                                                    buttons: {
                                                        cancel: {
                                                            text: 'No',
                                                            value: null,
                                                            visible: true,
                                                            closeModal: true,
                                                        },
                                                        confirm: {
                                                            text: 'Yes',
                                                            value: true,
                                                            visible: true,
                                                            closeModal: true
                                                        }
                                                    },
                                                    closeOnClickOutside: false,
                                                    closeOnEsc: false,
                                                    dangerMode: true
                                                }).then(function(confirm) {
                                                    if (confirm) {
                                                        if (assign) {
                                                            $.ajax({
                                                                    url: '{!! route('admin.return.assign.agent') !!}',
                                                                    method: 'POST',
                                                                    data: {
                                                                        'admin_id': assign,
                                                                        'shipment_ids[]': selected_rows,
                                                                        '_token': '{{ csrf_token() }}'
                                                                    }
                                                                })
                                                                .done(function(data) {
                                                                    if (data.status == 0) 
                                                                    {
                                                                        const $divElement = $('.shipment_msg_success');

                                                                                // Check if the $divElement exists and has the d-none class
                                                                                if ($divElement.length && $divElement.hasClass('d-none')) {
                                                                                    // Remove the d-none class
                                                                                    $divElement.removeClass('d-none');

                                                                                    // Append text to the div
                                                                                    $divElement.text(data.success);
                                                                                }
                                                                                $('#AssignAgentModal').modal('hide');

                                                                                // setTimeout(function() {
                                                                                //     $divElement.addClass('d-none');
                                                                                // }, 10000); //

                                                                                table.draw()
                                                                            } 
                                                                            else {
                                                                                const $divElement = $('.shipment_msg_error');

                                                                                // Check if the $divElement exists and has the d-none class
                                                                                if ($divElement.length && $divElement.hasClass('d-none')) {
                                                                                    // Remove the d-none class
                                                                                    $divElement.removeClass('d-none');

                                                                                    // Append text to the div
                                                                                    $divElement.text(data.error);
                                                                                }
                                                                                $('#AssignAgentModal').modal('hide');

                                                                                // setTimeout(function() {
                                                                                //     $divElement.addClass('d-none');
                                                                                // }, 10000); //

                                                                                table.draw();
                                                                            }
                                                                            
                                                                    selected_rows  = [];
                                                                    restricted_rows = [];

                                                                    table.rows().deselect();
                                                                    table.draw(true);
                                                                    table.button('.assign').disable();
                                                                    table.button('.confirm').disable();
                                                                    table.button('.re-attempt').disable();
                                                                    table.button('.un-assign').disable();
                                                                });
                                                        } else {
                                                            var error =
                                                                "Agent Not Selected!";
                                                            toastr.error(error,
                                                                'Error!', {
                                                                    positionClass: 'toast-top-center',
                                                                    containerId: 'toast-top-center'
                                                                });
                                                        }
                                                    }
                                                });
                                            });

                                        } else {
                                            var error = "Not selected any shipments!";
                                            toastr.error(error, 'Error!', {
                                                positionClass: 'toast-top-center',
                                                containerId: 'toast-top-center'
                                            });
                                        }
                                    }
                                },
                            @endif
                            @if (session('role_id') == 1 || in_array(316, session('permissions')))
                                {
                                    text: 'Un Assign Agent',
                                    className: 'btn btn-primary un-assign',
                                    enabled: false,
                                    action: function(e, dt, node, config) {
                                        if (selected_rows != '' && restricted_rows.length == 0) {
                                            swal({
                                                title: 'Are You Sure?',
                                                text: 'Select Yes to Un Assign Agent!',
                                                icon: 'warning',
                                                buttons: {
                                                    cancel: {
                                                        text: 'No',
                                                        value: null,
                                                        visible: true,
                                                        closeModal: true,
                                                    },
                                                    confirm: {
                                                        text: 'Yes',
                                                        value: true,
                                                        visible: true,
                                                        closeModal: true
                                                    }
                                                },
                                                closeOnClickOutside: false,
                                                closeOnEsc: false,
                                                dangerMode: true
                                            }).then(function(confirm) {
                                                if (confirm) {
                                                    blockPagePermanently();
                                                    table.rows().nodes().each(function(index) {
                                                        var row = table.row(index);

                                                        if ($(row.node()).hasClass(
                                                                'selected')) {
                                                            var id = parseInt(row.id());
                                                            // var remark = $(row.node()).find('td.shipment_remarks textarea').val();
                                                            // shipment_remarks[id] = remark;
                                                        }
                                                    });

                                                    $.ajax({
                                                        url: '{!! route('admin.return.unassign.agent') !!}',
                                                        method: 'POST',
                                                        data: {
                                                            'shipment_ids': selected_rows,
                                                            '_token': '{{ csrf_token() }}',
                                                            'action': 'un-assign',
                                                        }
                                                    }).done(function(data) {
                                                        UnblockPagePermanently();
                                                        selected_rows = [];
                                                        restricted_rows = [];
                                                        table.rows().deselect();
                                                        table.draw('false');
                                                        table.button('.confirm')
                                                            .disable();
                                                        table.button('.re-attempt')
                                                            .disable();
                                                        table.button('.assign')
                                                            .disable();
                                                        table.button('.un-assign')
                                                            .disable();

                                                        toastr.success(data.success,
                                                            'Success!', {
                                                                positionClass: 'toast-bottom-center',
                                                                containerId: 'toast-bottom-center'
                                                            });

                                                    });
                                                }
                                            });

                                        }
                                    }
                                },
                            @endif
                            @if (session('role_id') == 1 || in_array(45, session('permissions')))
                                {
                                    text: 'Confirm',
                                    className: 'btn btn-primary confirm',
                                    enabled: false,
                                    action: function(e, dt, node, config) {
                                        if (selected_rows !== '' && restricted_rows.length == 0) {
                                            $('#ReturnConfirmReasonModal').modal('show');

                                        } else {
                                            var error = "Not selected any shipments!";
                                            toastr.error(error, 'Error!', {
                                                positionClass: 'toast-top-center',
                                                containerId: 'toast-top-center'
                                            });
                                        }
                                    }
                                },
                            @endif,

                            @if (session('role_id') == 1 || in_array(907, session('permissions')))
                                {
                                    text: 'Call History',
                                    className: 'btn btn-primary call_history',
                                    enabled: false,
                                    action: function(e, dt, node, config) {
                                        if (selected_rows !== '' && restricted_rows.length == 0) {
                                            $('#update_call_status_modal').modal('show');
                                            $('#shipment_id').val(call_history);


                                        } else {
                                            var error = "Not selected any shipments!";
                                            toastr.error(error, 'Error!', {
                                                positionClass: 'toast-top-center',
                                                containerId: 'toast-top-center'
                                            });
                                        }
                                    }
                                },
                            @endif,

                            @if (session('role_id') == 1 || in_array(46, session('permissions')))
                                {
                                    text: 'Re-Attempt',
                                    className: 'btn btn-primary re-attempt',
                                    enabled: false,
                                    action: function(e, dt, node, config) {
                                        if (selected_rows != '' && restricted_rows.length == 0) {
                                            swal({
                                                title: 'Are You Sure?',
                                                text: 'Select Yes to change shipment status to Re-Attempt!',
                                                icon: 'warning',
                                                buttons: {
                                                    cancel: {
                                                        text: 'No',
                                                        value: null,
                                                        visible: true,
                                                        closeModal: true,
                                                    },
                                                    confirm: {
                                                        text: 'Yes',
                                                        value: true,
                                                        visible: true,
                                                        closeModal: true
                                                    }
                                                },
                                                closeOnClickOutside: false,
                                                closeOnEsc: false,
                                                dangerMode: true
                                            }).then(function(confirm) {
                                                if (confirm) {
                                                    blockPagePermanently();
                                                    table.rows().nodes().each(function(index) {
                                                        var row = table.row(index);

                                                        if ($(row.node()).hasClass(
                                                                'selected')) {
                                                            var id = parseInt(row.id());
                                                            var remark = $(row.node())
                                                                .find(
                                                                    'td.shipment_remarks textarea'
                                                                ).val();
                                                            shipment_remarks[id] =
                                                                remark;
                                                        }
                                                    });

                                                    $.ajax({
                                                        url: "{{ route('admin.return.reattempt.status') }}",
                                                        method: 'POST',
                                                        data: {
                                                            'shipment_ids': selected_rows,
                                                            '_token': '{{ csrf_token() }}',
                                                            'action': 'reattempt',
                                                            'remark': shipment_remarks
                                                        }
                                                    }).done(function(data) {
                                                        UnblockPagePermanently();
                                                        selected_rows = [];
                                                        restricted_rows = [];
                                                        shipment_remarks = {};
                                                        table.button('.confirm')
                                                            .disable();
                                                        table.button('.re-attempt')
                                                            .disable();
                                                        table.button('.assign')
                                                            .disable();
                                                        table.button('.un-assign')
                                                            .disable();
                                                        table.rows().deselect();
                                                        table.draw('false');

                                                        if (data.status == 1) {
                                                            UnblockPagePermanently();
                                                            table.draw('false');
                                                            toastr.success(data.success,
                                                                'Success!', {
                                                                    positionClass: 'toast-bottom-center',
                                                                    containerId: 'toast-bottom-center'
                                                                });
                                                        } else {
                                                            UnblockPagePermanently();
                                                            toastr.error(data.error,
                                                                'Error!', {
                                                                    positionClass: 'toast-top-center',
                                                                    containerId: 'toast-top-center'
                                                                });
                                                        }

                                                    });
                                                }
                                            });

                                        }
                                    }
                                },
                                
                            @endif {
                                extend: 'excel',
                                title: 'Return Marked',
                                className: 'btn btn-primary',
                                text: '<i class="la la-file-excel-o"></i> Excel',
                            }, {
                                extend: 'selectAll',
                                text: 'Select All',
                                className: 'select_all',
                                action: function(e) {
                                    e.preventDefault();

                                    table.rows().nodes().each(function(index) {
                                        var row = table.row(index);

                                        if ($(row.node().firstChild).hasClass(
                                                'select-checkbox') && !$(row.node()).hasClass(
                                                'selected')) {
                                            id = parseInt(row.id());

                                            var assigned_agent_id = row.data()
                                                .assigned_agent_id;
                                            var tat = row.data().confirmation_on;

                                            hub_id = $(row.node()).data('hub');

                                            var allow = false;

                                            if (hub_ids.length == 0) {
                                                hub_ids.push(hub_id);

                                                allow = true;
                                            } else if (hub_ids[0] == hub_id) {
                                                allow = true;
                                            }

                                            if (allow) {
                                                row.select();

                                                var index = $.inArray(id, selected_rows);

                                                if (index === -1) {
                                                    selected_rows.push(id);
                                                }


                                                if ({{ session('role_id') }} != 1 &&
                                                    {{ $permission }} == 0 &&
                                                    assigned_agent_id != {{ auth()->id() }} &&
                                                    tat > 0) {
                                                    restricted_index = $.inArray(id,
                                                        restricted_rows);

                                                    if (restricted_index === -1) {
                                                        restricted_rows.push(id);
                                                    }
                                                }

                                                if (restricted_rows.length == 0) {
                                                    table.button('.confirm').enable();
                                                    table.button('.re-attempt').enable();
                                                }
                                                table.button('.assign').enable();
                                                table.button('.un-assign').enable();
                                            }
                                        }
                                    });
                                }
                            }, {
                                extend: 'selectNone',
                                text: 'Select None',
                                className: 'select_none',
                                action: function(e) {
                                    e.preventDefault();

                                    table.rows().nodes().each(function(index) {
                                        var row = table.row(index);

                                        if ($(row.node().firstChild).hasClass(
                                                'select-checkbox') && $(row.node()).hasClass(
                                                'selected')) {
                                            row.deselect();

                                            id = parseInt(row.id());

                                            var index = $.inArray(id, selected_rows);

                                            if (index !== -1) {
                                                selected_rows.splice(index, 1);
                                            }

                                            var restricted_index = $.inArray(id,
                                                restricted_rows);

                                            if (restricted_index !== -1) {
                                                restricted_rows.splice(restricted_index, 1);
                                            }

                                            if (selected_rows.length == 0) {
                                                table.button('.confirm').disable();
                                                table.button('.assign').disable();
                                                table.button('.re-attempt').disable();
                                                table.button('.un-assign').disable();
                                                hub_ids.splice(index, 1);
                                            }
                                        }
                                    });
                                }
                            },
                            {
                                title: 'Upload',
                                className: 'btn btn-primary excel-upload',
                                text: '<i class="la la-file-excel-o"></i> Upload',
                                action: function(e) {
                                    $('#excel_upload_modal').modal('show');
                                }
                            },
                            {
                                title: 'Upload Agent',
                                className: 'btn btn-primary excel-upload',
                                text: '<i class="la la-file-excel-o"></i> Upload Agent',
                                action: function(e) {
                                    $('#agent_assign_modal').modal('show');
                                }
                            },
                            'reset'
                        ],
                    @else
                        buttons: [{
                            extend: 'excel',
                            title: 'Return Marked',
                            className: 'btn btn-primary',
                            text: '<i class="la la-file-excel-o"></i> Excel',
                        }, 'reset'],
                    @endif
                    select: {
                        info: false,
                        style: 'multi',
                        selector: 'td.select-checkbox',
                        className: 'selected bg-primary bg-lighten-5 primary'
                    },
                    scrollX: true,
                    scrollY: '500px',
                    lengthMenu: [
                        [50, 100, 500, 1000, -1],
                        [50, 100, 500, 1000, 'All']
                    ],
                    pageLength: 50,
                    pagingType: 'full_numbers',
                    processing: true,
                    language: {
                        processing: data_table_loader
                    },
                    serverSide: true,
                    ajax: {
                        url: '{{ route('admin.return.list') }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: function(d) {
                            d.tracking_numbers = $('#track_form .tracking_numbers').val();
                            d.search_shipping_mode = $('#search_shipping_mode').val();
                            d.star_shipper_filter = $('#star_shippers_filter').val();
                            d.complaint_filter = $('#complaint_filter').val();
                            d.out_of_service_area_filter = $('#out_of_service_area_filter').val();
                            d.shipment_re_attempt_request_filter = $('#shipment_re_attempt_request_filter')
                                .val();
                            d.try_buy_filter = $('#try_buy_filter').val();
                            d.return_confirmation_pending_filter = $('#return_confirmation_pending_filter')
                                .val();
                            d.search_rvr_value_div = $('#search_rvr_value_div').val();
                            d.search_sar_value_div = $('#search_sar_value_div').val();
                            d.search_total_value_div = $('#search_total_value_div').val();
                            d.search_unresponsive_value_div = $('#search_unresponsive_value_div').val();

                        },

                    },
                    rowId: 'shId',
                    order: [
                        [24, 'desc']
                    ],
                    columns: 
                        [{data: 'shId',orderable: false,searchable: false,class: 'text-center align-middle select p-1',targets: 0,
                            render: function(data, type, row) {
                                return '';
                            }
                        },
                        {data: 'id', defaultContent: '',orderable: false,searchable: false, class: 'align-middle serial_number'},
                        {data: 'tracking_number',name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
                        {data: 'order_id',name: 'shipments.order_id',class: 'align-middle order_id'},
                        {data: 'shipper', name: 'u.name',class: 'align-middle shipper'},
                        {data: 'shipper_phone',name: 'shipper_phone',class: 'align-middle shipper_phone'},
                        {data: 'vendor_name', name: 'usi.vendor', class: 'align-middle vendor_name'},
                        {data: 'origin',name: 'oc.name',class: 'align-middle origin'},
                        {data: 'destination',name: 'dc.name', class: 'align-middle destination'},
                        {data: 'hub',name: 'h.name',class: 'align-middle hub'},
                        {data: 'consignee_name', name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                        {data: 'consignee_phone',name: 'consignee_phone',class: 'align-middle consignee_phone'},
                        {data: 'consignee_address', name: 'shipments.consignee_address', class: 'align-middle consignee_address'},
                        {data: 'sub_station',name: 'dlm.area_name',class: 'align-middle sub_station',orderable: false,searchable: false},
                        {data: 'amount',name: 'shipments.amount',class: 'align-middle amount'},
                        {data: 'mode',name: 'sm.id',class: 'align-middle mode'},
                        {data: 'service_type',name: 'bt.id',class: 'align-middle service_type'},
                        {data: 'status',name: 'status',class: 'align-middle status' },
                        {data: 'reason',name: 'ssr.name',class: 'align-middle reason'},
                        {data: 'remarks',name: 'sscf.remark',class: 'align-middle text-center remarks'},
                        {data: 'shipment_remarks',name: 'admin_journey.remarks',class: 'align-middle shipment_remarks'},
                        {data: 'shipper_remarks',name: 'shipments_journey.remarks',class: 'align-middle shipper_remarks' },
                        {data: 'nsa_osa_estimated_charges',name: 'nsa_osa_estimated_charges',class: 'align-middle nsa_osa_estimated_charges'},
                        {data: 'arrival',name: 'sj.created_at',class: 'align-middle arrival' },
                        {data: 'status_date',name: 'shipments_journey.created_at',class: 'align-middle status_date'},
                        {data: 'reattemp_status_remarks',name: 'reattempt_shipment_status_remarks.remarks',class: 'align-middle reattemp_status_remarks',orderable: false,searchable: false},
                        {data: 'confirmation_req',name: '',class: 'align-middle confirmation_req', orderable: false,searchable: false},
                        {data: 'confirmation_on',name: '',class: 'align-middle confirmation_on', orderable: false,searchable: false},
                        {data: 'delivery_attempt',name: '',class: 'align-middle reattempts', orderable: false,searchable: false},
                        {data: 'reattempts',name: 'sret.created_at',class: 'align-middle reattempts', orderable: false,searchable: false},
                        {data: 'assigned_agent',name: 'assigned_agent.name',class: 'align-middle assigned_agent' },
                        {data: 'assigned_at',name: 'ras.created_at',class: 'align-middle assigned_at'},
                        {data: 'assigned_by',name: 'asadby.name',class: 'align-middle assigned_by'},
                        {data: 'consolidation',name: 'consolidation',class: 'align-middle consolidation', orderable: false,searchable: false},
                        {data: 'consolidated_id',name: 'consolidations.consolidation_id',class: 'align-middle consolidated_id', orderable: false,searchable: false},
                        {data: 'rvsaa_count',name: 'rvsaa.unresponsive_count',class: 'align-middle consolidated_id', orderable: false,searchable: false},
                        {data: 'unresponsive_attempt_time',name: 'rvsaa.unresponsive_attempt_time',class: 'align-middle consolidated_id', orderable: false,searchable: false},
                        {data: 'action',name: 'action',class: 'text-center align-middle action p-1', orderable: false,searchable: false}

                    ],
                    rowCallback: function(row, data, index) {
                        var info = table.page.info();
                        $('td:eq(1)', row).html(index + 1 + info.page * info.length);

                        if (data.OsaStatus == 0) {
                            $('td:eq(0)', row).addClass('select-checkbox');

                            if ($.inArray(data.shId, selected_rows) !== -1) {
                                table.row(row).select();
                            }
                        }
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
                        var drop_select =
                            '<select name="status_select" id="status_select" class="select2 form-control"></select>';
                        var mode_drop_select =
                            '<select name="mode_select" id="mode_select" class="select2 form-control"></select>';
                        var service_drop_select =
                            '<select name="service_select" id="service_select" class="select2 form-control"></select>';
                        this.api().columns().every(function(column_id) {
                            var column = this;
                            var header = column.header();

                            if ($(header).is('.select') || $(header).is('.serial_number') || $(
                                    header).is('.action') || $(header).is('.shipment_remarks') || $(
                                    header).is('.reattempts') || $(header).is('.consolidation') ||
                                $(header).is('.reattemp_status_remarks')) {
                                $(td).appendTo($(search) || $(header).is('sub_station'));
                            } else if ($(header).is('.status')) {
                                $(drop_select).appendTo($(search))
                                    .on('change', function() {
                                        column.search($(this).val(), false, false, true).draw();
                                    }).wrap(td);
                            } else if ($(header).is('.mode')) {
                                $(mode_drop_select).appendTo($(search))
                                    .on('change', function() {
                                        column.search($(this).val(), false, false, true).draw();
                                    }).wrap(td);
                            } else if ($(header).is('.service_type')) {
                                $(service_drop_select).appendTo($(search))
                                    .on('change', function() {
                                        column.search($(this).val(), false, false, true).draw();
                                    }).wrap(td);
                            } else {
                                var current = $(input).appendTo($(search)).on('change', function() {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td).after(icon);

                                if (column.search()) {
                                    current.val(column.search());
                                }
                            }
                        });
                        var data = $.map({!! $shipment_status !!}, function(obj) {
                            obj.id = obj.id;
                            obj.text = obj.name;
                            return obj;
                        });

                        $("#status_select").prepend('<option value="" selected></option>').select2({
                            data: data,
                            placeholder: "Select Status",
                            width: '100%',
                            containerCssClass: 'select-xs',
                            dropdownCssClass: 'form-control-sm p-0'
                        });
                        var data1 = $.map({!! $shipping_mode !!}, function(obj) {
                            obj.id = obj.id;
                            obj.text = obj.mode;
                            return obj;
                        });

                        $("#mode_select").prepend('<option value="" selected></option>').select2({
                            data: data1,
                            placeholder: "Select Mode",
                            width: '100%',
                            containerCssClass: 'select-xs',
                            dropdownCssClass: 'form-control-sm p-0'
                        });
                        var data2 = $.map({!! $service_type !!}, function(obj) {
                            obj.id = obj.id;
                            obj.text = obj.booking_type;
                            return obj;
                        });
                        $("#service_select").prepend('<option value="" selected></option>').select2({
                            data: data2,
                            placeholder: "Select Service",
                            width: '100%',
                            containerCssClass: 'select-xs',
                            dropdownCssClass: 'form-control-sm p-0'
                        });
                        this.api().table().columns.adjust();
                    }
                });

                $('#AssignAgentModal').on('shown.bs.modal', function(e) {});
                $('#AssignAgentModal').on('hide.bs.modal', function(e) {
                    $('#assign_agent').val('').trigger('change');
                });
                $('#ReturnConfirmReasonModal').on('hide.bs.modal', function(e) {
                    $('#return_reason_select').val('').trigger('change');
                    $('#return_reason_shipment_remarks').val('');
                });
                $('#ReturnConfirmReasonSingleModal').on('hide.bs.modal', function(e) {
                    $('#single_return_reason_select').val('').trigger('change');
                    $('#return_reason_shipment_remarks_single').val('');
                });
                $('#EditEstimateChargesModalNSAreattempt').on('hide.bs.modal', function(e) {
                    $('#estimated_charges_NSAreattempt_input').val('');
                });

                var hub_ids = [];

                var call_history = [];

                $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {

                    var id = parseInt($(this).parent('tr').attr('id'));
                    var hub_id = $(this).parents('tr').data('hub');
                    var con_id = parseInt($(this).parent('tr').attr('consolidation_id'));
                    var assigned_agent_id = table.row($(this).parents('tr')).data().assigned_agent_id;
                    var tat = table.row($(this).parents('tr')).data().confirmation_on;
                    var id = parseInt($(this).parent('tr').attr('id'));
                    
                    var index = call_history.indexOf(id);                    
                    if (index === -1) {
                        call_history.push(id);
                    } else {
                        call_history.splice(index, 1);
                    }


                    if(call_history.length > 0){
                        table.button('.call_history').enable();
                    }else{
                        table.button('.call_history').disable();
                    }

                    
                    if (con_id) {
                        if (hub_ids.length == 0) {
                            hub_ids.push(hub_id);
                        } else if (hub_ids[0] != hub_id) {
                            var error = "Selected hubs should be the same!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                            return false;
                        }
                        table.rows().nodes().each(function(index) {
                            var row = table.row(index);
                            if ($(row.node()).attr('consolidation_id') == con_id) {
                                var rid = parseInt($(row.node()).attr('id'));
                                var rindex = $.inArray(rid, selected_rows);

                                if (rindex === -1) {
                                    selected_rows.push(rid);
                                    if (id != rid) {

                                        table.row(row).select();
                                    }
                                } else {
                                    if (id != rid) {

                                        row.deselect();
                                    }
                                    selected_rows.splice(rindex, 1);
                                }
                                if (selected_rows.length > 0 || call_history.length > 0) {
                                    table.button('.confirm').enable();
                                    table.button('.assign').enable();
                                    table.button('.re-attempt').enable();
                                    table.button('.un-assign').enable();

                                } else {
                                    table.button('.confirm').disable();
                                    table.button('.assign').disable();
                                    table.button('.re-attempt').disable();
                                    table.button('.un-assign').disable();

                                }
                            }
                        });
                    } else {
                        if (hub_ids.length == 0) {
                            hub_ids.push(hub_id);
                            var index = $.inArray(id, selected_rows);

                            if (index === -1) {
                                selected_rows.push(id);
                            } else {
                                selected_rows.splice(index, 1);
                            }

                            if ({{ session('role_id') }} != 1 && {{ $permission }} == 0 &&
                                assigned_agent_id != {{ auth()->id() }} && tat > 0) {
                                var restricted_index = $.inArray(id, restricted_rows);

                                if (restricted_index === -1) {
                                    restricted_rows.push(id);
                                } else {
                                    restricted_rows.splice(restricted_index, 1);
                                }
                            }

                            if (selected_rows.length > 0) {
                                if (restricted_rows.length == 0) {
                                    table.button('.re-attempt').enable();
                                    table.button('.confirm').enable();
                                } else {
                                    table.button('.re-attempt').disable();
                                    table.button('.confirm').disable();
                                }
                                table.button('.assign').enable();
                                table.button('.un-assign').enable();
                            } else {
                                table.button('.confirm').disable();
                                table.button('.assign').disable();
                                table.button('.re-attempt').disable();
                                table.button('.un-assign').disable();
                            }
                        } else {
                            if (hub_ids[0] == hub_id) {
                                var index = $.inArray(id, selected_rows);

                                if (index === -1) {
                                    selected_rows.push(id);
                                } else {
                                    selected_rows.splice(index, 1);
                                }

                                if ({{ session('role_id') }} != 1 && {{ $permission }} == 0 &&
                                    assigned_agent_id != {{ auth()->id() }} && tat > 0) {
                                    var restricted_index = $.inArray(id, restricted_rows);

                                    if (restricted_index === -1) {
                                        restricted_rows.push(id);
                                    } else {
                                        restricted_rows.splice(restricted_index, 1);
                                    }
                                }


                                if (selected_rows.length > 0) {
                                    if (restricted_rows.length == 0) {
                                        table.button('.re-attempt').enable();
                                        table.button('.confirm').enable();
                                    } else {
                                        table.button('.re-attempt').disable();
                                        table.button('.confirm').disable();
                                    }
                                    table.button('.assign').enable();
                                    table.button('.un-assign').enable();
                                } else {
                                    table.button('.confirm').disable();
                                    table.button('.assign').disable();
                                    table.button('.re-attempt').disable();
                                    table.button('.un-assign').disable();
                                }
                            } else {
                                var error = "Selected hubs should be the same!";
                                toastr.error(error, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                                return false;
                            }

                        }
                    }

                });
                $('body').on('click', '.returnMarkStatus', function() {
                    var action = $(this).data('action');
                    var id = $(this).data('id');
                    var row_id = $(this).parents('tr').attr('id');
                    var remark = $(this).parents('tr').find('td.shipment_remarks textarea').val();
                    if (action === 'confirm') {
                        atext = 'Select Yes to change shipment status to Return-Confirm!';
                        $('#ReturnConfirmReasonSingleModal').modal('show');
                        $('#return_reason_shipment_id').val(row_id);
                        //$('#return_reason_shipment_remarks').val(remark);
                    } else if (action === 'reattempt') {
                        atext = 'Select Yes to change shipment status to Re-Attempt!';
                    }
                    if (row_id != '' && action === 'reattempt' && id >= 1) {
                        var Shid = $(this).parents('tr').attr('id');
                        if (Shid) {
                            $('#EditEstimateChargesModalNSAreattempt').modal('show');
                            $('#eec_shipment_id_NSAreattempt').val(Shid);
                            $('#eec_shipment_remark_NSAreattempt').val(remark);
                        }
                    }
                    if (row_id != '' && action === 'reattempt' && id == '') {

                        swal({
                            title: 'Are You Sure?',
                            text: atext,
                            icon: 'warning',
                            buttons: {
                                cancel: {
                                    text: 'No',
                                    value: null,
                                    visible: true,
                                    closeModal: true,
                                },
                                confirm: {
                                    text: 'Yes',
                                    value: true,
                                    visible: true,
                                    closeModal: true
                                }
                            },
                            closeOnClickOutside: false,
                            closeOnEsc: false,
                            dangerMode: true
                        }).then(function(confirm) {
                            if (confirm) {
                                blockPagePermanently();
                                $.ajax({
                                    url: "{{ route('admin.return.marked.status.single') }}",
                                    method: 'POST',
                                    data: {
                                        'shipment_id': row_id,
                                        '_token': '{{ csrf_token() }}',
                                        'action': action,
                                        'remark': remark
                                    }
                                }).done(function(data) {
                                    if (data.status == 1) {
                                        UnblockPagePermanently();
                                        table.draw('false');
                                        toastr.success(data.success, 'Success!', {
                                            positionClass: 'toast-bottom-center',
                                            containerId: 'toast-bottom-center'
                                        });

                                    } else {
                                        UnblockPagePermanently();
                                        toastr.error(data.error, 'Error!', {
                                            positionClass: 'toast-top-center',
                                            containerId: 'toast-top-center'
                                        });

                                    }

                                });
                            }
                        });


                    }
                    if (action === 'call_history') {
                        var call_history = $(this).parents('tr').attr('id');
                        if (call_history) {
                            $('#shipment_id').val(call_history);
                            $('#update_call_status_modal').modal('show');
                        }
                    }
                });

                $('body').on('click', '.intercept', function() {
                    var row_id = $(this).parents('tr').attr('id');

                    if (row_id != '') {
                        var redirect = '{!! route('admin.intercept.index', ':id') !!}';
                        var url = redirect.replace(':id', row_id);
                        window.open(url);
                    }
                });

                $.validator.addMethod('maxsize', function(value, element, params) {
                    if ($(element).attr('type') === 'file') {
                        if (element.files && element.files.length) {
                            for (var c = 0; c < element.files.length; c++) {
                                if (element.files[c].size > params) {
                                    return false;
                                }
                            }
                        }
                    }

                    return true;
                }, $.validator.format("File Size must not exceed {0} bytes."));

                $('#return_status_form').validate({
                    errorClass: 'danger',
                    successClass: 'success',
                    normalizer: function(value) {
                        return $.trim(value);
                    },
                    errorPlacement: function(error, element) {
                        error.addClass('w-100').appendTo(element.parent('.form-group'));
                    },
                    submitHandler: function(form) {
                        $(form).find('button[type=submit]').attr('disabled', 'disabled');

                        swal({
                            title: 'Please Wait!',
                            text: 'Your shipment(s) are being updated!',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        form.submit();
                    }
                });
                $('#assign_agent_form').validate({
                    errorClass: 'danger',
                    successClass: 'success',
                    normalizer: function(value) {
                        return $.trim(value);
                    },
                    errorPlacement: function(error, element) {
                        error.addClass('w-100').appendTo(element.parent('.form-group'));
                    },
                    submitHandler: function(form) {
                        $(form).find('button[type=submit]').attr('disabled', 'disabled');

                        swal({
                            title: 'Please Wait!',
                            text: 'Your shipment(s) are being updated!',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        form.submit();
                    }
                });
                $('#update_return_reason_form').validate({
                    errorClass: 'danger',
                    successClass: 'success',
                    errorPlacement: function(error, element) {
                        error.addClass('w-100').appendTo(element.parent('.form-group'));
                    },
                    submitHandler: function(form) {

                        var return_reason_select = $('#return_reason_select').val();
                        var consignee_refused_reasons = $('#consignee_refused_reasons').val();
                        var remarks = $('#return_reason_shipment_remarks').val();
                        swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes to change shipment status to Return-Confirm!',
                            icon: 'warning',
                            buttons: {
                                cancel: {
                                    text: 'No',
                                    value: null,
                                    visible: true,
                                    closeModal: true,
                                },
                                confirm: {
                                    text: 'Yes',
                                    value: true,
                                    visible: true,
                                    closeModal: true
                                }
                            },
                            closeOnClickOutside: false,
                            closeOnEsc: false,
                            dangerMode: true
                        }).then(function(confirm) {
                            if (confirm) {
                                blockPagePermanently();
                                $(form).find('button[type=submit]').attr('disabled', 'disabled');
                                table.rows().nodes().each(function(index) {
                                    var row = table.row(index);
                                    if ($(row.node()).hasClass('selected')) {
                                        var id = parseInt(row.id());
                                        //var remarks = $(row.node()).find('td.shipment_remarks textarea').val();
                                        //shipment_remarks[id] = remarks;
                                    }
                                });

                                $.ajax({
                                    url: "{{ route('admin.return.confirm.status') }}",
                                    method: 'POST',
                                    data: {
                                        'shipment_ids': selected_rows,
                                        '_token': '{{ csrf_token() }}',
                                        'action': 'confirm',
                                        'remark': remarks,
                                        'return_reason_select': return_reason_select,
                                        'consignee_refused_reasons': consignee_refused_reasons,
                                    }
                                }).done(function(data) {
                                    if (data.status == 1) {
                                        UnblockPagePermanently();
                                        table.draw('false');
                                        toastr.success(data.success, 'Success!', {
                                            positionClass: 'toast-bottom-center',
                                            containerId: 'toast-bottom-center'
                                        });

                                    } else {
                                        UnblockPagePermanently();
                                        toastr.error(data.error, 'Error!', {
                                            positionClass: 'toast-top-center',
                                            containerId: 'toast-top-center'
                                        });

                                    }
                                    table.rows().deselect();
                                    selected_rows = [];
                                    shipment_remarks = {};
                                    table.button('.confirm').disable();
                                    table.button('.assign').disable();
                                    table.button('.re-attempt').disable();
                                    table.button('.un-assign').disable();
                                    $('#ReturnConfirmReasonModal').modal('hide');
                                    $('#return_reason_select').val(null).trigger('change');
                                    $('button.update_return_confirm').attr('disabled',
                                        false);
                                });
                            }
                        });

                    }
                });

                // select all return confirm work

                $('#return_reason_select').on('change', function() {
                    var selected_value = $(this).val();
                    if (selected_value == 38) {
                        $('#return_reason_shipment_remarks').attr('readonly', true);
                        $('#consignee_refused_reasons').parent('fieldset').removeClass('d-none');
                        $('#btnReturn').attr('disabled', true);

                    } else {
                        $('#return_reason_shipment_remarks').attr('readonly', false);
                        $('#consignee_refused_reasons').parent('fieldset').addClass('d-none');
                        $('#btnReturn').attr('disabled', false);
                    }
                });

                $('#consignee_refused_reasons').on('change', function() {
                    var selected_value = $(this).val();
                    var selected_data = $(this).select2('data');
                    var selected_text = selected_data[0].text;
                    if (selected_value == 12) {
                        $('#return_reason_shipment_remarks').attr('readonly', false);
                        $('#return_reason_shipment_remarks').val('');
                        // $('#return_reason_shipment_remarks').attr('data-rule-required','true');
                        $('#btnReturn').attr('disabled', true);

                    } else {
                        $('#return_reason_shipment_remarks').attr('readonly', true);
                        $('#return_reason_shipment_remarks').val(selected_text);
                        $('#btnReturn').attr('disabled', false);
                    }

                });

                $('#return_reason_shipment_remarks_single').keyup(function() {
                    value = $(this).val();
                    if (value.length > 0) {
                        $('#single_reason_update_btn').attr('disabled', false);
                    } else {
                        $('#single_reason_update_btn').attr('disabled', true);
                    }
                });

                // single return confirm work

                $('#single_return_reason_select').on('change', function() {
                    var selected_value = $(this).val();
                    if (selected_value == 38) {
                        $('#return_reason_shipment_remarks_single').attr('readonly', true);
                        $('#single_consignee_refused_reasons').parent('fieldset').removeClass('d-none');
                        $('#single_reason_update_btn').attr('disabled', true);

                    } else {
                        $('#return_reason_shipment_remarks_single').attr('readonly', false);
                        $('#single_consignee_refused_reasons').parent('fieldset').addClass('d-none');
                        $('#single_reason_update_btn').attr('disabled', false);
                    }
                });

                $('#single_consignee_refused_reasons').on('change', function() {
                    var selected_value = $(this).val();
                    var selected_data = $(this).select2('data');
                    var selected_text = selected_data[0].text;
                    if (selected_value == 12) {
                        $('#return_reason_shipment_remarks_single').attr('readonly', false);
                        $('#return_reason_shipment_remarks_single').val('');
                        // $('#return_reason_shipment_remarks').attr('data-rule-required','true');
                        $('#single_reason_update_btn').attr('disabled', true);

                    } else {
                        $('#return_reason_shipment_remarks_single').attr('readonly', true);
                        $('#return_reason_shipment_remarks_single').val(selected_text);
                        $('#single_reason_update_btn').attr('disabled', false);
                    }

                });

                $('#return_reason_shipment_remarks').keyup(function() {
                    value = $(this).val();
                    if (value.length > 0) {
                        $('#btnReturn').attr('disabled', false);
                    } else {
                        $('#btnReturn').attr('disabled', true);
                    }
                });

                $('#single_reason_update_btn').on('click', function() {
                    var shipment_id = $('#return_reason_shipment_id').val();
                    var remarks = $('#return_reason_shipment_remarks_single').val();
                    var single_return_reason_select = $('#single_return_reason_select').val();
                    var single_consignee_refused_reasons = $('#single_consignee_refused_reasons').val();
                    if (single_return_reason_select === '') {
                        var error = 'Select a reason!';
                        toastr.error(error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    } else {
                        swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes to change shipment status to Return-Confirm!',
                            icon: 'warning',
                            buttons: {
                                cancel: {
                                    text: 'No',
                                    value: null,
                                    visible: true,
                                    closeModal: true,
                                },
                                confirm: {
                                    text: 'Yes',
                                    value: true,
                                    visible: true,
                                    closeModal: true
                                }
                            },
                            closeOnClickOutside: false,
                            closeOnEsc: false,
                            dangerMode: true
                        }).then(function(confirm) {
                            if (confirm) {
                                var action = 'confirm';
                                blockPagePermanently();
                                $.ajax({
                                    url: "{{ route('admin.return.marked.status.single') }}",
                                    method: 'POST',
                                    data: {
                                        'shipment_id': shipment_id,
                                        '_token': '{{ csrf_token() }}',
                                        'action': action,
                                        'remark': remarks,
                                        'single_return_reason_select': single_return_reason_select,
                                        'single_consignee_refused_reasons': single_consignee_refused_reasons,
                                    }
                                }).done(function(data) {
                                    if (data.status == 1) {
                                        UnblockPagePermanently();
                                        table.draw('false');
                                        toastr.success(data.success, 'Success!', {
                                            positionClass: 'toast-bottom-center',
                                            containerId: 'toast-bottom-center'
                                        });

                                    } else {
                                        UnblockPagePermanently();
                                        toastr.error(data.error, 'Error!', {
                                            positionClass: 'toast-top-center',
                                            containerId: 'toast-top-center'
                                        });

                                    }
                                    $('#single_return_reason_select').val(null).trigger(
                                        'change');
                                    $('#return_reason_shipment_id').val('');
                                    $('#return_reason_shipment_remarks').val('');
                                    $('#ReturnConfirmReasonSingleModal').modal('hide');

                                });
                            }
                        });


                    }

                });



                var select = $('#track_form .tracking_numbers').selectize({
                    placeholder: 'Tracking Number(s)*',
                    delimiter: ',',
                    createOnBlur: true,
                    persist: false,
                    plugins: ['remove_button'],
                    onDropdownOpen: function(dropdown) {
                        dropdown.remove();
                    },
                    onType: function(str) {
                        var regex = /^[0-9,]+$/;

                        if (!regex.test(str)) {
                            select[0].selectize.setTextboxValue('');
                        }
                    },
                    create: function(input) {
                        if (input.length >= 6 && Math.floor(input) == input && $.isNumeric(input)) {
                            return {
                                value: input,
                                text: input
                            }
                        } else {
                            return false;
                        }
                    }
                });

                $('#track_form').bind('submit', function(e) {
                    e.preventDefault();
                    var tracking_numbers = $('#track_form .tracking_numbers').val();

                    if (tracking_numbers != '') {
                        table.draw();
                    }

                });

                $('#datatable').on('click', '.selfCollection', function() {
                    var row_id = $(this).parents('tr').attr('id');
                    var remark = $.trim($('tr#' + row_id).find('td.shipment_remarks textarea').val());
                    if (row_id) {
                        swal({
                            text: 'Are you sure you want to mark shipment for Self-Collection?',
                            icon: 'info',
                            buttons: {
                                cancel: {
                                    text: 'No',
                                    value: null,
                                    visible: true,
                                    closeModal: true,
                                },
                                confirm: {
                                    text: 'Yes',
                                    value: true,
                                    visible: true,
                                    closeModal: true
                                }
                            },
                            closeOnClickOutside: false,
                            closeOnEsc: false,
                            dangerMode: true
                        }).then(function(confirm) {
                            if (confirm) {
                                blockPagePermanently();
                                $.ajax({
                                    url: "{{ route('admin.return.marked.self_collection') }}",
                                    method: 'POST',
                                    data: {
                                        'shipment_id': row_id,
                                        'remark': remark,
                                        '_token': '{{ csrf_token() }}',
                                    }
                                }).done(function(data) {
                                    UnblockPagePermanently();
                                    if (data.status == 1) {
                                        toastr.error(data.error, 'Error!', {
                                            positionClass: 'toast-top-center',
                                            containerId: 'toast-top-center'
                                        });
                                    } else {
                                        table.draw(false);
                                        toastr.success(data.success, 'Success!', {
                                            positionClass: 'toast-bottom-center',
                                            containerId: 'toast-bottom-center'
                                        });
                                    }
                                });
                            }
                        });
                    }
                });


                $('.decimal').inputmask({
                    'alias': 'integer',
                    'allowMinus': false,
                    'allowPlus': false,
                    'rightAlign': false,
                    'min': 0,
                    'max': 100000
                });


                $('#datatable').on('click', '.editEstimateCharges', function() {
                    var id = $(this).parents('tr').attr('id');
                    if (id) {
                        $('#EditEstimateChargesModal').modal('show');
                        $('#eec_shipment_id').val(id);
                    }

                });
                $('#update_charges_NSAreattempt_form').validate({
                    errorClass: 'danger',
                    successClass: 'success',
                    errorPlacement: function(error, element) {
                        error.addClass('w-100').appendTo(element.parent('.form-group'));
                    },
                    normalizer: function(value) {
                        return $.trim(value);
                    },
                    submitHandler: function(form) {
                        swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes to update Estimated Charges with change of shipment status to Re-Attempt!',
                            icon: 'warning',
                            buttons: {
                                cancel: {
                                    text: 'No',
                                    value: null,
                                    visible: true,
                                    closeModal: true,
                                },
                                confirm: {
                                    text: 'Yes',
                                    value: true,
                                    visible: true,
                                    closeModal: true
                                }
                            },
                            closeOnClickOutside: false,
                            closeOnEsc: false,
                            dangerMode: true
                        }).then(function(confirm) {
                            if (confirm) {
                                blockPagePermanently();
                                var charges = $('#estimated_charges_NSAreattempt_input').val();
                                var shipment_id = $('#eec_shipment_id_NSAreattempt').val();
                                var shipment_remark = $('#eec_shipment_remark_NSAreattempt').val();
                                $.ajax({
                                    url: "{{ route('admin.return.marked.status.single') }}",
                                    method: 'POST',
                                    data: {
                                        'charges': charges,
                                        'shipment_id': shipment_id,
                                        '_token': '{{ csrf_token() }}',
                                        'action': 'reattempt',
                                        'remark': shipment_remark

                                    }
                                }).done(function(data) {
                                    UnblockPagePermanently();
                                    $('#EditEstimateChargesModal').modal('hide');

                                    if (data.status == 1) {
                                        UnblockPagePermanently();
                                        table.draw(false);
                                        toastr.success(data.success, 'Success!', {
                                            positionClass: 'toast-bottom-center',
                                            containerId: 'toast-bottom-center'
                                        });
                                    } else {
                                        UnblockPagePermanently();
                                        toastr.error(data.error, 'Error!', {
                                            positionClass: 'toast-top-center',
                                            containerId: 'toast-top-center'
                                        });
                                    }
                                    $('#estimated_charges_NSAreattempt_input').val('');
                                    $('#eec_shipment_id_NSAreattempt').val('');
                                    $('#EditEstimateChargesModalNSAreattempt').modal(
                                        'hide');
                                });

                            }
                        });
                    }
                });
                $('#update_charges_form').validate({
                    errorClass: 'danger',
                    successClass: 'success',
                    errorPlacement: function(error, element) {
                        error.addClass('w-100').appendTo(element.parent('.form-group'));
                    },
                    normalizer: function(value) {
                        return $.trim(value);
                    },
                    submitHandler: function(form) {
                        // $(form).find('button[type=submit]').attr('disabled', 'disabled');
                        swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes to update Estimated Charges!',
                            icon: 'warning',
                            buttons: {
                                cancel: {
                                    text: 'No',
                                    value: null,
                                    visible: true,
                                    closeModal: true,
                                },
                                confirm: {
                                    text: 'Yes',
                                    value: true,
                                    visible: true,
                                    closeModal: true
                                }
                            },
                            closeOnClickOutside: false,
                            closeOnEsc: false,
                            dangerMode: true
                        }).then(function(confirm) {
                            if (confirm) {
                                blockPagePermanently();
                                var charges = $('#estimated_charges_input').val();
                                var shipment_id = $('#eec_shipment_id').val();
                                $.ajax({
                                    url: '{!! route('admin.return.edit.estimated_charges') !!}',
                                    method: 'POST',
                                    data: {
                                        'charges': charges,
                                        'shipment_id': shipment_id,
                                        '_token': '{{ csrf_token() }}'
                                    }
                                }).done(function(data) {
                                    UnblockPagePermanently();
                                    $('#EditEstimateChargesModal').modal('hide');

                                    if (data.status) {
                                        toastr.error(data.error, 'Error!', {
                                            positionClass: 'toast-top-center',
                                            containerId: 'toast-top-center'
                                        });
                                    } else {
                                        table.draw(false);
                                        toastr.success(data.success, 'Success!', {
                                            positionClass: 'toast-bottom-center',
                                            containerId: 'toast-bottom-center'
                                        });
                                    }
                                    $('#estimated_charges_input').val('');
                                    $('#eec_shipment_id').val('');
                                });

                            }
                        });
                    }
                });


                $('body').on('click', 'button.consignee_info_label', function() {
                    var phone = $(this).attr('rel');
                    if (phone) {
                        $.ajax({
                                url: '{!! route('admin.settings.blacklist.search.consignee') !!}',
                                method: 'POST',
                                data: {
                                    '_token': '{{ csrf_token() }}',
                                    'phone': phone
                                }
                            })
                            .done(function(data) {
                                if (data.status == 0) {
                                    details = data.details;
                                    $('#consignee_information_id').val(details.consignee.id);
                                    var html = '<div class="row mb-1">';

                                    html += '<div class="col-4">Consignee Name :</div><div class="col-8">' +
                                        details.consignee.name + '</div>';
                                    html +=
                                        '<div class="col-4">Consignee Phone Number 1 :</div><div class="col-8">' +
                                        details.consignee.phone + '</div>';
                                    var consignee_phone = '';
                                    if (details.consignee.phone2 != null) {
                                        consignee_phone = details.consignee.phone2;
                                    }
                                    html +=
                                        '<div class="col-4">Consignee Phone Number 2 :</div><div class="col-8">' +
                                        consignee_phone + '</div>';
                                    html +=
                                        '<div class="col-4">Consignee Address :</div><div class="col-8">' +
                                        details.consignee.address + '</div>';
                                    html += '<div class="col-4">Consignee City :</div><div class="col-8">' +
                                        details.consignee.city + '</div>';

                                    html += '</div>';
                                    if ('blacklist' in details) {
                                        html += '<div class="row p-1" style="background-color: ' + details
                                            .blacklist.color + '; color:white;">';
                                        html += '<div class="col-12">';
                                        html += '<table class="table table-sm table-bordered mb-0">';
                                        html += '<tbody>';
                                        html += '<tr>';
                                        html += '<td><strong>Total Shipments</strong></td>';
                                        html += '<td><strong>Delivered</strong></td>';
                                        html += '<td><strong>Ratio</strong></td>';
                                        html += '<td><strong>Undelivered</strong></td>';
                                        html += '<td><strong>Ratio</strong></td>';
                                        html += '<td><strong>Return Confirmed</strong></td>';
                                        html += '<td><strong>Ratio</strong></td>';
                                        html += '</tr>';
                                        html += '<tr>';
                                        html += '<td>' + details.blacklist.total_shipments + '</td>';
                                        html += '<td>' + details.blacklist.delivered + '</td>';
                                        html += '<td>' + details.blacklist.delivered_ratio + ' %</td>';
                                        html += '<td>' + details.blacklist.undelivered + '</td>';
                                        html += '<td>' + details.blacklist.undelivered_ratio + ' %</td>';
                                        html += '<td>' + details.blacklist.return+'</td>';
                                        html += '<td>Rs. ' + details.blacklist.return_ratio + ' %</td>';
                                        html += '</tr>';
                                        html += '</tbody>';
                                        html += '</table>';
                                        html += '</div></div>';
                                    }

                                    $('#consignee_info_div').html(html);

                                    $('#label_select').val('').trigger('change');
                                    $('#ConsigneeInformationModal').modal('show');

                                    toastr.success(data.success, 'Success!', {
                                        positionClass: 'toast-bottom-center',
                                        containerId: 'toast-bottom-center'
                                    });
                                } else {
                                    $(form).find('button.search').prop('disabled', false);

                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                            });
                    }
                });
                $('#label_update_form').validate({
                    errorClass: 'danger',
                    successClass: 'success',
                    errorPlacement: function(error, element) {
                        error.addClass('w-100').appendTo(element.parents('.form-group'));
                    },
                    submitHandler: function(form) {
                        $(form).find('button[type=submit]').attr('disabled', 'disabled');

                        swal({
                            title: 'Please Wait!',
                            text: 'Category is being added!',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        form.submit();
                    }
                });

                $('#datatable tbody').on('click', '.dropdown-menu a.rcp_sms', function() {
                    var id = parseInt($(this).parents('tr').attr('id'));


                    $('#send_sms_form #id').val(id);
                    $('#send_sms_modal').modal('show');
                });

                $('#star_shippers_filter').on('click', function() {
                    $('#star_shippers_filter').val(1);
                    table.draw(true);
                    $('#star_shippers_filter').val(0);
                });
                $('#complaint_filter').on('click', function() {
                    $('#complaint_filter').val(1);
                    table.draw(true);
                    $('#complaint_filter').val(0);
                });
                $('#out_of_service_area_filter').on('click', function() {
                    $('#out_of_service_area_filter').val(1);
                    table.draw(true);
                    $('#out_of_service_area_filter').val(0);
                });
                $('#shipment_re_attempt_request_filter').on('click', function() {
                    $('#shipment_re_attempt_request_filter').val(1);
                    table.draw(true);
                    $('#shipment_re_attempt_request_filter').val(0);
                });
                $('#try_buy_filter').on('click', function() {
                    $('#try_buy_filter').val(1);
                    table.draw(true);
                    $('#try_buy_filter').val(0);
                });
                $('#return_confirmation_pending_filter').on('click', function() {
                    $('#return_confirmation_pending_filter').val(1);
                    table.draw(true);
                    $('#return_confirmation_pending_filter').val(0);
                });

            });
        </script>
    @endsection
