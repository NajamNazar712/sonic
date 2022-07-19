@extends('admin.layout.master')

@section('title', 'Leads Management')

@section('content')
    <h1 class="mb-1">
        Leads Management
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mt-2">
                    <div class="card col-12">
                        <div class="card-content collapse show">
                            <div class="card-body">
                                <form id="search_form" class="card-body card-dashboard" novalidate="novalidate">
                                    <div class="row justify-content-center">
                                        <input type="hidden" name="search_statistics_div" id="search_statistics_div"
                                               value="">
                                        <div class="form-group col">
                                            <input type="text" name="from_date"
                                                   class="form-control graph_date bg-primary border-primary white rounded-right"
                                                   id="from_date" placeholder="Date From"
                                                   data-value="{{$dates['old_date']}}" data-rule-required="true"
                                                   data-msg-required="This field is required">
                                        </div>
                                        <div class="form-group col">
                                            <input type="text" name="to_date"
                                                   class="form-control graph_date bg-primary border-primary white rounded-right"
                                                   id="to_date" placeholder="Date To" data-value="{{$dates['current']}}"
                                                   data-rule-required="true" data-msg-required="This field is required">
                                        </div>
                                        <div class="form-group col">
                                            <select name="search_origin" id="search_origin"
                                                    class="select2 form-control">
                                                @foreach($cities as $city)
                                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col">
                                            <select name="search_sale_person" id="search_sale_person"
                                                    class="select2 form-control">
                                                @foreach($sale_name as $sn)
                                                    <option value="{{ $sn->id }}"> {{ $sn->name }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-1">
                                            <button type="submit" class="btn round btn-primary search_button">Search <i
                                                        class="ft-bar-chart"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-3" id="total_leads_div">
                        <div class="card bg-gradient-directional-booked_shipments pull-up cursor-pointer">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media d-flex">
                                        <div class="align-self-center">
                                            <i class="icon-grid text-white font-large-2 float-left"></i>
                                        </div>
                                        <div class="media-body text-white text-right">
                                            <h3 class="text-white"><p id="total_leads"
                                                                      class="d-inline">{{$leads['total']}}</p> (100%)
                                            </h3>
                                            <span>Total Leads</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3" id="received_leads_div">
                        <div class="card bg-gradient-directional-complaints_launched pull-up cursor-pointer">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media d-flex">
                                        <div class="align-self-center">
                                            <i class="icon-flag text-white font-large-2 float-left"></i>
                                        </div>
                                        <div class="media-body text-white text-right">
                                            <h3 class="text-white"><p id="received_leads"
                                                                      class="d-inline">{{$leads['received']}}</p> (<p
                                                        id="received_percentage"
                                                        class="d-inline">{{$leads['received_percentage']}}</p>%)
                                            </h3>
                                            <span>Leads Received</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3" id="in_process_div">
                        <div class="card bg-gradient-directional-in_transit pull-up cursor-pointer">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media d-flex">
                                        <div class="align-self-center">
                                            <i class="icon-clock text-white font-large-2 float-left"></i>
                                        </div>
                                        <div class="media-body text-white text-right">
                                            <h3 class="text-white"><p id="in_process"
                                                                      class="d-inline">{{$leads['in_process']}}</p> (<p
                                                        id="in_process_percentage"
                                                        class="d-inline">{{$leads['in_process_percentage']}}</p>%)
                                            </h3>
                                            <span>In Process</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-3" id="in_process_activation_div">
                        <div class="card bg-gradient-directional-out_for_delivery pull-up cursor-pointer">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media d-flex">
                                        <div class="align-self-center">
                                            <i class="icon-clock text-white font-large-2 float-left"></i>
                                        </div>
                                        <div class="media-body text-white text-right">
                                            <h3 class="text-white"><p id="in_process_activation"
                                                                      class="d-inline">{{$leads['in_process_for_activation']}}</p>
                                                (<p id="in_process_for_activation_percentage"
                                                    class="d-inline">{{$leads['in_process_for_activation_percentage']}}</p>
                                                %)
                                            </h3>
                                            <span>In Process For Activation</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row justify-content-center">
                    <div class="col-3" id="dead_leads_div">
                        <div class="card bg-gradient-directional-pending_shipments pull-up cursor-pointer">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media d-flex">
                                        <div class="align-self-center">
                                            <i class="icon-close text-white font-large-2 float-left"></i>
                                        </div>
                                        <div class="media-body text-white text-right">
                                            <h3 class="text-white"><p id="dead_leads"
                                                                      class="d-inline">{{$leads['dead_leads']}}</p> (<p
                                                        id="dead_percentage"
                                                        class="d-inline">{{$leads['dead_leads_percentage']}}</p>%)
                                            </h3>
                                            <span>Dead Leads</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3" id="activated_leads_div">
                        <div class="card bg-gradient-directional-return_delivered pull-up cursor-pointer">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media d-flex">
                                        <div class="align-self-center">
                                            <i class="icon-check text-white font-large-2 float-left"></i>
                                        </div>
                                        <div class="media-body text-white text-right">
                                            <h3 class="text-white"><p id="active_leads"
                                                                      class="d-inline">{{$leads['accounts_activated']}}</p>
                                                (<p id="active_percentage"
                                                    class="d-inline">{{$leads['accounts_activated_percentage']}}</p>%)
                                            </h3>
                                            <span>Accounts Activated</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-3" id="lead_time_ratio_div">
                        <div class="card bg-gradient-directional-return_confirm pull-up cursor-pointer">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media d-flex">
                                        <div class="align-self-center">
                                            <i class="la la-calculator text-white font-large-2 float-left"></i>
                                        </div>
                                        <div class="media-body text-white text-right">
                                            <h3 class="text-white" id="dead_ratio">{{ $leads['dead_leads_ratio']}}</h3>
                                            <span>Dead Lead Time Ratio</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3" id="lead_time_ratio_div">
                        <div class="card bg-gradient-directional-destination pull-up cursor-pointer">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media d-flex">
                                        <div class="align-self-center">
                                            <i class="la la-calculator text-white font-large-2 float-left"></i>
                                        </div>
                                        <div class="media-body text-white text-right">
                                            <h3 class="text-white"
                                                id="active_ratio">{{ $leads['active_leads_ratio']}}</h3>
                                            <span>Active Lead Time Ratio</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-3" id="dormant_div">
                        <div class="card bg-gradient-directional-pending_confirmation pull-up cursor-pointer">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media d-flex">
                                        <div class="align-self-center">
                                            <i class="la la-hourglass text-white font-large-2 float-left"></i>
                                        </div>
                                        <div class="media-body text-white text-right">
                                            <h3 class="text-white"><p id="dormant" class="d-inline">{{$leads['dormant']}} </p></h3>
                                            <span>Dormant</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1"></th>
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Lead ID</th>
                        <th class="border-primary border-darken-1">Contact Person</th>
                        <th class="border-primary border-darken-1">City</th>
                        <th class="border-primary border-darken-1">Territory</th>
                        <th class="border-primary border-darken-1">Area</th>
                        <th class="border-primary border-darken-1">Phone No</th>
                        <th class="border-primary border-darken-1">Email Address</th>
                        <th class="border-primary border-darken-1">Message</th>
                        <th class="border-primary border-darken-1">Service</th>
                        <th class="border-primary border-darken-1">Brand</th>
                        <th class="border-primary border-darken-1">Company</th>
                        <th class="border-primary border-darken-1">Lead Reference</th>
                        <th class="border-primary border-darken-1">Requested Date/Time</th>
                        <th class="border-primary border-darken-1">Aging</th>
                        <th class="border-primary border-darken-1">Sale Person Tagged</th>
                        <th class="border-primary border-darken-1">Sale Person Tagged At</th>
                        <th class="border-primary border-darken-1">Sale Person Tagged Aging</th>
                        <th class="border-primary border-darken-1">Reference Person</th>
                        <th class="border-primary border-darken-1">Lead Status</th>
                        <th class="border-primary border-darken-1">Lead Reason</th>
                        <th class="border-primary border-darken-1">Updated By</th>
                        <th class="border-primary border-darken-1">Updated AT</th>
                        <th class="border-primary border-darken-1">Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="SalesTagModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="SalesTagModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Tag Sales Person</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="shipper_id">
                    <select name="Sale_person" id="saletag" class="form-control select2">
                        @foreach($sale_name as $sn)
                            <option value="{{ $sn->id }}"> {{ $sn->name }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="salesTagSubmit">Submit</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="ForwardLeadModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="ForwardLeadModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Forward Lead</h4>
                </div>
                <div class="modal-body">
                    <div class="col mb-1">
                        <select name="sale_person" id="saletag1" class="form-control select2">
                            @foreach($sale_name as $sn)
                                <option value="{{ $sn->id }}"> {{ $sn->name }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <select name="reference_person" id="reference_person" class="form-control select2">
                            @foreach($sale_name as $sn)
                                <option value="{{ $sn->id }}"> {{ $sn->name }} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="ForwardLeadSubmit">Submit</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="DetailsModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="DetailsModal"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">

                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add_remarks_modal" role="dialog" aria-labelledby="add_remarks_title" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="add_remarks_title">Remarks</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_remarks_form" class="form-horizontal mb-1 justify-content-center"
                          novalidate="novalidate">

                        <div class="form-group">
                            <input type="text" name="add_remarks" id="add_remarks" class="form-control add_remarks"
                                   placeholder="Remarks" data-rule-required="true"
                                   data-msg-required="Remarks is required">

                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" name="add" class="btn btn-primary add" value="Add">Add Remarks
                            </button>
                            <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Close</button>

                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="add_status_modal" role="dialog" aria-labelledby="add_status_modal_title"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="add_remarks_title">Update Status</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_status_form" class="form-horizontal mb-1 justify-content-center"
                          novalidate="novalidate">

                        <div class="form-group">
                            <select name="update_lead_status" id="update_lead_status" class="form-control select2">
                                @foreach($lead_statuses as $lead_status)
                                    <option value="{{ $lead_status->id }}"> {{ $lead_status->name }} </option>
                                @endforeach
                            </select>
                        </div>
                        <div id="div_lead_status_rejected" class="form-group d-none">
                            <select name="lead_status_rejected" id="lead_status_rejected" class="form-control select2">
                                <option value="1"> Prohibited Items</option>
                                <option value="2"> Wrong Contact Details</option>
                                <option value="3"> Duplicate</option>
                                <option value="10"> Others</option>
                            </select>
                        </div>
                        <div id="div_lead_status_notinterested" class="form-group d-none">
                            <select name="lead_status_notinterested" id="lead_status_notinterested"
                                    class="form-control select2">
                                <option value="4"> A/C Query Call</option>
                                <option value="10"> Others</option>
                            </select>
                        </div>
                        <div id="div_lead_status_irrelevant" class="form-group d-none">
                            <select name="lead_status_irrelevant" id="lead_status_irrelevant"
                                    class="form-control select2">
                                <option value="5"> Operational Query</option>
                                <option value="6"> HR Query</option>
                                <option value="7"> Sales Person Already Assigned</option>
                                <option value="10"> Others</option>
                            </select>
                        </div>
                        <div id="div_lead_status_blocked" class="form-group d-none">
                            <select name="lead_status_blocked" id="lead_status_blocked" class="form-control select2">
                                <option value="8"> Unresponsive</option>
                                <option value="9"> Customer Wants To Be Contacted Later</option>
                                <option value="11"> Customer Needs More Time</option>
                                <option value="12"> General Query</option>
                                <option value="13"> Rates Negotiations</option>
                            </select>
                        </div>
                        <div id="div_lead_status_dormant" class="form-group d-none">
                            <select name="lead_status_dormant" id="lead_status_dormant" class="form-control select2">
                                <option value="8"> Unresponsive</option>
                                <option value="9"> Customer Wants To Be Contacted Later</option>
                                <option value="11"> Customer Needs More Time</option>
                                <option value="12"> General Query</option>
                                <option value="13"> Rates Negotiations</option>
                            </select>
                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" name="add" class="btn btn-primary add" value="Add">Update</button>
                            <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Close</button>

                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>


    {{--    Add Lead--}}
    <div class="modal fade" id="add_lead_modal" role="dialog" aria-labelledby="add_lead_modal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="edit_lead_modal_title">Add Lead</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">

                    <form id="add_lead_form" class="form-horizontal mb-1 justify-content-center" novalidate="novalidate" method="POST" action="{{ route('admin.leads.add') }}">
                        @method('POST')
                        @csrf

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <input name="contact_person" id="add_name" class="form-control select2" placeholder="Contact Person Name*" data-rule-required="true"  data-msg-required="Contact Person Name is required">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="phone_number" id="add_phone_number" placeholder="Phone Number*" data-rule-required="true"  data-msg-required="Phone Number is required">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <input type="email" class="form-control" name="email_address" id="add_email" placeholder="Email*" data-rule-required="true"  data-msg-required="Email is required">
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    <select name="service_id" id="add_service" class="form-control select2" data-rule-required="true"  data-msg-required="Service is required">
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}"> {{ $service->name }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <select name="city_id" id="add_city" class="form-control select2" data-rule-required="true"  data-msg-required="City is required">
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}"> {{ $city->name }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <select name="territory_id" id="add_territory" class="form-control select2" data-rule-required="true"  data-msg-required="Territory is required">
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <select name="territory_area_id" id="add_area" class="form-control select2">
                                    </select>
                                </div>
                            </div>


                            <div class="col-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="brand" id="add_brand" placeholder="Brand" data-rule-required="true"  data-msg-required="Brand is required">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="company" id="add_company" placeholder="Company" data-rule-required="true"  data-msg-required="Company Name is required">
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <div class="form-group ml-1">
                                <button type="submit" class="btn btn-primary width-200" value="Add">Add</button>
                                <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Close</button>

                            </div>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>

    {{--    End Add Lead--}}
    {{--    todo bulk status model--}}
    <div class="modal fade" id="edit_lead_modal" role="dialog" aria-labelledby="edit_lead_modal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="edit_lead_modal_title">Edit Lead (<span></span>)</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <table class="table table-bordered datatable text-center" id="lead_info_table">
                        <thead>
                            <tr>
                                <th>City</th>
                                <th>Territory</th>
                                <th>Area</th>
                                <th>Phone Number</th>
                                <th>Email</th>
                                <th>Brand</th>
                                <th>Company</th>
                            </tr>
                        </thead>
                    </table>

                    <form id="edit_lead_form" class="form-horizontal mb-1 justify-content-center" novalidate="novalidate" method="POST" action="{{ route('admin.leads.edit') }}">
                        @method('POST')
                        @csrf
                        <input type="hidden" name="edit_lead_id" id="edit_lead_id">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <select name="city_id" id="edit_city" class="form-control select2" data-rule-required="true"  data-msg-required="City is required">
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}"> {{ $city->name }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <select name="territory_id" id="edit_territory" class="form-control select2" data-rule-required="true"  data-msg-required="Territory is required">
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <select name="territory_area_id" id="edit_area" class="form-control select2">
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="phone_number" id="edit_phone_number" placeholder="Phone Number" data-rule-required="true"  data-msg-required="Phone Number is required">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <input type="email" class="form-control" name="email_address" id="edit_email" placeholder="Email" data-rule-required="true"  data-msg-required="Email is required">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="brand" id="edit_brand" placeholder="Brand" data-rule-required="true"  data-msg-required="Brand is required">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="company" id="edit_company" placeholder="Company" data-rule-required="true"  data-msg-required="Company Name is required">
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <div class="form-group ml-1">
                                <button type="submit" class="btn btn-primary width-200" value="Add">Edit</button>
                                <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Close</button>

                            </div>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>
    {{--    todo bulk status model end--}}

    <div class="modal fade text-left" id="DetailsModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="DetailsModal"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">

                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{--    todo bulk status model--}}
    <div class="modal fade" id="add_bulk_status_modal" role="dialog" aria-labelledby="add_bulk_status_modal_title"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="add_remarks_title">Update Bulk Statuss</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_bulk_status_form" class="form-horizontal mb-1 justify-content-center"
                          novalidate="novalidate">
                        <div class="form-group">
                            <select name="update_lead_bulk_status" id="update_bulk_lead_status"
                                    class="form-control select2">
                                @foreach($lead_statuses as $lead_status)
                                    <option value="{{ $lead_status->id }}"> {{ $lead_status->name }} </option>
                                @endforeach
                            </select>
                        </div>
                        <div id="div_lead_status_rejected1" class="form-group d-none">
                            <select name="lead_status_rejected1" id="lead_status_rejected1"
                                    class="form-control select2">
                                <option value="1"> Prohibited Items</option>
                                <option value="2"> Wrong Contact Details</option>
                                <option value="3"> Duplicate</option>
                                <option value="10"> Others</option>
                            </select>
                        </div>
                        <div id="div_lead_status_notinterested1" class="form-group d-none">
                            <select name="lead_status_notinterested1" id="lead_status_notinterested1"
                                    class="form-control select2">
                                <option value="4"> A/C Query Call</option>
                                <option value="10"> Others</option>
                            </select>
                        </div>
                        <div id="div_lead_status_irrelevant1" class="form-group d-none">
                            <select name="lead_status_irrelevant1" id="lead_status_irrelevant1"
                                    class="form-control select2">
                                <option value="5"> Operational Query</option>
                                <option value="6"> HR Query</option>
                                <option value="7"> Sales Person Already Assigned</option>
                                <option value="10"> Others</option>
                            </select>
                        </div>
                        <div id="div_lead_status_blocked1" class="form-group d-none">
                            <select name="lead_status_blocked1" id="lead_status_blocked1"
                                    class="form-control select2">
                                <option value="8"> Unresponsive</option>
                                <option value="9"> Customer Wants To Be Contacted Later</option>
                                <option value="11"> Customer Needs More Time</option>
                                <option value="12"> General Query</option>
                                <option value="13"> Rates Negotiations</option>
                            </select>
                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" name="add" class="btn btn-primary add" value="Add">Update</button>
                            <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Close</button>

                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>
    {{--    todo bulk status model end--}}


@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/line-awesome/css/line-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/cryptocoins/cryptocoins.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <style>
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
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            var area = '';
            var territory = '';

            $("#search_origin").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Origin",
                width: '100%'
            });

            $("#search_sale_person").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Sale Person",
                width: '100%'
            });

            var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                format: 'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function (context) {
                    if (context.select) {
                        $('#search_form #to_date').pickadate('picker').set('min', $('#search_form #from_date').pickadate('picker').get('select'));
                    }
                }
            });

            var to_date = $('#to_date').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                format: 'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function (context) {
                    if (context.select) {
                        $('#search_form #from_date').pickadate('picker').set('max', $('#search_form #to_date').pickadate('picker').get('select'));
                    }
                }
            });
            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.leads.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Lead ID');
                            head.push('Contact Person');
                            head.push('City');
                            head.push('Territory');
                            head.push('Area');
                            head.push('Phone No');
                            head.push('Email Address');
                            head.push('Message');
                            head.push('Service');
                            head.push('Brand');
                            head.push('Company');
                            head.push('Lead Reference');
                            head.push('Requested Date/Time');
                            head.push('Aging');
                            head.push('Sale Person Tagged');
                            head.push('Sale Person Tagged At');
                            head.push('Sale Person Tagged Aging');
                            head.push('Reference Person');
                            head.push('Lead Status');
                            head.push('Reason');
                            head.push('Updated By');
                            head.push('Updated At');

                            $.each(result.data, function (index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.lead_id);
                                row.push(values.contact_person);
                                row.push(values.city);
                                row.push(values.territory);
                                row.push(values.area);
                                row.push(values.phone_number);
                                row.push(values.email_address);
                                row.push(values.message);
                                row.push(values.service);
                                row.push(values.brand);
                                row.push(values.company);
                                row.push(values.lead_reference);
                                row.push(values.requested_date);
                                row.push(values.aging);
                                row.push(values.sale_person);
                                row.push(values.sale_person_updated_at);
                                row.push(values.sale_person_tagged_aging);
                                row.push(values.reference_person);
                                row.push(values.status);
                                row.push(values.reason_id);
                                row.push(values.updated_by);
                                row.push(values.updated_at);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });
            var selected_rows = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [

                        @if (session('role_id') == 1 || in_array(769, session('permissions')))
                    {
                        text: '<i class="la la-plus"></i> Add Lead',
                        className: 'btn btn-primary add_lead',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            $('#add_lead_modal').modal('show');
                        }
                    },
                        @endif
                        @if (session('role_id') == 1 || in_array(678, session('permissions')))
                    {
                        text: 'Bulk Update Status',
                        className: 'btn btn-primary update_status',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            // table.button('.update_status').disable();
                            //todo show modal
                            if (selected_rows != '') {
                                $('#add_bulk_status_modal').modal('show');
                            }
                        }
                    },
                        @endif
                        @if (session('role_id') == 1 || in_array(361, session('permissions')))
                    {
                        text: 'Tag',
                        className: 'btn btn-primary bulk_tagging',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            if (selected_rows != '') {
                                $('#SalesTagModal').modal('show');
                            } else {
                                var error = "Account Not selected!";
                                toastr.error(error, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }
                        }
                    },
                        @endif
                    {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action: function (e) {
                            e.preventDefault();

                            table.rows().nodes().each(function (index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox') && !$(row.node()).hasClass('selected')) {
                                    id = parseInt(row.id());
                                    row.select();

                                    var index = $.inArray(id, selected_rows);

                                    if (index === -1) {
                                        selected_rows.push(id);
                                    }

                                    table.button('.bulk_tagging').enable();
                                    table.button('.update_status').enable();
                                }
                            });
                        }
                    }, {
                        extend: 'selectNone',
                        text: 'Select None',
                        className: 'select_none',
                        action: function (e) {
                            e.preventDefault();

                            table.rows().nodes().each(function (index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox') && $(row.node()).hasClass('selected')) {
                                    row.deselect();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index !== -1) {
                                        selected_rows.splice(index, 1);
                                    }

                                    if (selected_rows.length == 0) {
                                        table.button('.bulk_tagging').disable();
                                        table.button('.update_status  ').disable();
                                    }
                                }
                            });
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Lead Management',
                        className: 'btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'
                ],
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.leads.list') }}',
                    data: function (d) {
                        d.search_origin = $('#search_origin').val();
                        d.search_sale_person = $('#search_sale_person').val();
                        d.search_statistics = $('#search_statistics_div').val();
                        d.search_date_from = $('input[name="from_date_formatted"]').val();
                        d.search_date_to = $('input[name="to_date_formatted"]').val();
                    }
                },
                rowId: 'leadid',
                order: [[14, 'desc']],
                columns: [
                    {data: 'lead_id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'id',defaultContent:'', orderable: false, searchable: false, class: 'align-middle serial_number'},
                    {data: 'lead_id_link', name: 'leads.id', class: 'align-middle lead_id_link'},
                    {data: 'contact_person', name: 'leads.contact_person', class: 'align-middle contact_person'},
                    {data: 'city', name: 'c.name', class: 'align-middle city'},
                    {data: 'territory', name: 't.name', class: 'align-middle territory'},
                    {data: 'area', name: 'at.name', class: 'align-middle area'},
                    {data: 'phone_number', name: 'leads.phone_number', class: 'align-middle phone_number'},
                    {data: 'email_address', name: 'leads.email_address', class: 'align-middle email_address'},
                    {data: 'message', name: 'leads.message', class: 'align-middle message'},
                    {data: 'service', name: 'leads.service_id', class: 'align-middle service'},
                    {data: 'brand', name: 'leads.brand', class: 'align-middle brand'},
                    {data: 'company', name: 'leads.company', class: 'align-middle company'},
                    {data: 'lead_reference', name: 'lr.name', class: 'align-middle lead_reference'},
                    {data: 'requested_date', name: 'leads.requested_date', class: 'align-middle requested_date'},
                    {data: 'aging', class: 'align-middle aging', orderable: false, searchable: false},
                    {data: 'sale_person', name: 'sp.name', class: 'align-middle sale_person'},
                    {
                        data: 'sale_person_updated_at',
                        name: 'leads.sale_person_updated_at',
                        class: 'align-middle sale_person_updated_at'
                    },
                    {data: 'sale_person_tagged_aging', name: 'sale_person_tagged_aging', class: 'align-middle sale_person_tagged_aging', orderable: false, searchable: false},
                    {data: 'reference_person', name: 'rp.name', class: 'align-middle sale_person'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'reason_id', name: 'leads.reason', class: 'align-middle reason_id'},
                    {data: 'updated_by', name: 'ub.name', class: 'align-middle updated_by'},
                    {data: 'updated_at', name: 'leads.updated_at', class: 'align-middle updated_at'},
                    {data: 'action', name: 'action', class: 'align-middle action', orderable: false, searchable: false}
                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    if ($.inArray(data.id, selected_rows) !== -1) {
                        table.row(row).select();
                    }
                },
                initComplete: function () {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';
                    var service_select = '<select name="service_select" id="service_select" class="select2 form-control"></select>';
                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.action') || $(header).is('.aging') || $(header).is('.reason_id') || $(header).is('.sale_person_tagged_aging')) {
                            $(td).appendTo($(search) || $(header).is('.serial_number'));
                        } else if ($(header).is('.status')) {
                            $(status_select).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        } else if ($(header).is('.service')) {
                            $(service_select).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        } else {
                            var current = $(input).appendTo($(search)).on('change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });
                    var data1 = $.map({!! $statuses !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;

                        return obj;
                    });

                    var data2 = $.map({!! $services !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;

                        return obj;
                    });
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        data: data1,
                        placeholder: "Select Status",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $("#service_select").prepend('<option value="" selected></option>').select2({
                        data: data2,
                        placeholder: "Select Status",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });

            $('#datatable tbody').on('click', 'tr td.select-checkbox', function () {
                var id = parseInt($(this).parent('tr').attr('id'));

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                } else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.bulk_tagging').enable();
                    table.button('.update_status').enable();

                } else {
                    table.button('.bulk_tagging').disable();
                    table.button('.update_status').disable();
                }

                if (selected_rows.length > 0) {
                    table.button('.create').enable();
                } else {
                    table.button('.create').disable();
                }
            });

            $("#saletag").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Sales Person",
                width: '100%',
                dropdownParent: $('#SalesTagModal')
            });
            $("#saletag1").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Sales Person",
                width: '100%',
                dropdownParent: $('#ForwardLeadModal')
            });
            $("#reference_person").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Reference Person",
                width: '100%',
                dropdownParent: $('#ForwardLeadModal')
            });
            $('#salesTagSubmit').on('click', function () {
                var assign = parseInt($('#saletag').val());
                swal({
                    text: 'Are you sure, you want to Tag?',
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
                }).then(function (confirm) {
                    if (confirm) {
                        if (assign) {
                            $('#SalesTagModal').modal('hide');
                            swal({
                                title: 'Please Wait!',
                                text: 'Lead is being Tagged!',
                                icon: 'info',
                                buttons: false,
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });

                            $.ajax({
                                url: '{!! route('admin.leads.tag_sale_person') !!}',
                                method: 'POST',
                                data: {
                                    'sale_person': assign,
                                    'lead_ids[]': selected_rows,
                                    '_token': '{{ csrf_token() }}'
                                }
                            })
                                .done(function (data) {
                                    if (data.status == 1) {
                                        $('#SalesTagModal').modal('hide');
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
                                    selected_rows = [];

                                    table.rows().deselect();
                                    $('#saletag').val('').trigger('change');
                                    table.draw(true);
                                    table.button('.bulk_tagging').disable();

                                    swal.close();
                                });
                        } else {
                            var error = "Lead Not Selected!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    }
                });
            });
            var status_lead_id = null;
            var remark_lead_id = null;
            var forward_lead_id = null;

            $('body').on('click', '#datatable .forward_lead', function () {
                forward_lead_id = parseInt($(this).parents('tr').attr('id'));
                $('#ForwardLeadModal').modal('show');
            });
            $('#ForwardLeadSubmit').on('click', function () {
                var tag = parseInt($('#saletag1').val());
                var refer_person = parseInt($('#reference_person').val());
                if (tag && refer_person) {
                    swal({
                        title: 'Please Wait!',
                        text: 'Lead is being forwarded!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });
                    $.ajax({
                        url: '{!! route('admin.leads.tag_sale_person') !!}',
                        method: 'POST',
                        data: {
                            'sale_person': tag,
                            'reference_person': refer_person,
                            'lead_ids[]': forward_lead_id,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                        .done(function (data) {
                            if (data.status) {
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
                            $('#saletag1').val('').trigger('change');
                            $('#reference_person').val('').trigger('change');
                            $('#ForwardLeadModal').modal('hide');
                            forward_lead_id = null;
                            swal.close();
                            table.draw(true);
                        });
                } else {
                    if (!tag) {
                        var error = "Sales Person Not Selected!";
                        toastr.error(error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                    if (!refer_person) {
                        var error = "Reference Person Not Selected!";
                        toastr.error(error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                }
            });


            $("#update_lead_status").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Status",
                width: '100%',
                dropdownParent: $('#add_status_modal')
            }).
            bind('change', function () {
                $(this).valid();
                $('#lead_status_rejected').val('').trigger('change');
                $('#lead_status_notinterested').val('').trigger('change');
                $('#lead_status_irrelevant').val('').trigger('change');
                $('#lead_status_blocked').val('').trigger('change');
                $('#lead_status_dormant').val('').trigger('change');
                if (this.value == 10) {
                    $('#div_lead_status_rejected').removeClass('d-none');
                    $('#div_lead_status_notinterested').addClass('d-none');
                    $('#div_lead_status_irrelevant').addClass('d-none');
                    $('#div_lead_status_blocked').addClass('d-none');
                    $('#div_lead_status_dormant').addClass('d-none');
                } else if (this.value == 4) {
                    $('#div_lead_status_rejected').addClass('d-none');
                    $('#div_lead_status_notinterested').removeClass('d-none');
                    $('#div_lead_status_irrelevant').addClass('d-none');
                    $('#div_lead_status_blocked').addClass('d-none');
                    $('#div_lead_status_dormant').addClass('d-none');
                } else if (this.value == 3) {
                    $('#div_lead_status_rejected').addClass('d-none');
                    $('#div_lead_status_notinterested').addClass('d-none');
                    $('#div_lead_status_irrelevant').removeClass('d-none');
                    $('#div_lead_status_blocked').addClass('d-none');
                    $('#div_lead_status_dormant').addClass('d-none');
                } else if(this.value == 11){
                    $('#div_lead_status_rejected').addClass('d-none');
                    $('#div_lead_status_notinterested').addClass('d-none');
                    $('#div_lead_status_irrelevant').addClass('d-none');
                    $('#div_lead_status_blocked').removeClass('d-none');
                    $('#div_lead_status_dormant').addClass('d-none');
                } else if(this.value == 14){
                    $('#div_lead_status_rejected').addClass('d-none');
                    $('#div_lead_status_notinterested').addClass('d-none');
                    $('#div_lead_status_irrelevant').addClass('d-none');
                    $('#div_lead_status_blocked').addClass('d-none');
                    $('#div_lead_status_dormant').removeClass('d-none');
                }
                else {
                    $('#div_lead_status_rejected').addClass('d-none');
                    $('#div_lead_status_notinterested').addClass('d-none');
                    $('#div_lead_status_irrelevant').addClass('d-none');
                    $('#div_lead_status_blocked').addClass('d-none');
                    $('#div_lead_status_dormant').addClass('d-none');
                }
            });

            {{--$('body').on('click','#datatable .view_remarks',function(){--}}
            {{--    var lead_id = parseInt($(this).parents('tr').attr('id'));--}}
            {{--    var link = '{{ route('admin.leads.view_remarks', ["id" => 0]) }}';--}}

            {{--    window.location = link.substr(0, link.lastIndexOf('/')) + '/' + lead_id;--}}
            {{--});--}}
            //todo sub modal under status
            $("#update_bulk_lead_status").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Status",
                width: '100%',
                dropdownParent: $('#add_bulk_status_modal')
            }).bind('change', function ()
            {
                $(this).valid();
                $('#lead_status_rejected1').val('').trigger('change');
                $('#lead_status_notinterested1').val('').trigger('change');
                $('#lead_status_irrelevant1').val('').trigger('change');
                $('#lead_status_blocked1').val('').trigger('change');
                $('#lead_status_dormant1').val('').trigger('change');
                if (this.value == 10) {
                    $('#div_lead_status_rejected1').removeClass('d-none');
                    $('#div_lead_status_notinterested1').addClass('d-none');
                    $('#div_lead_status_irrelevant1').addClass('d-none');
                    $('#div_lead_status_blocked1').addClass('d-none');
                    $('#div_lead_status_dormant1').addClass('d-none');
                } else if (this.value == 4) {
                    $('#div_lead_status_rejected1').addClass('d-none');
                    $('#div_lead_status_notinterested1').removeClass('d-none');
                    $('#div_lead_status_irrelevant1').addClass('d-none');
                    $('#div_lead_status_blocked1').addClass('d-none');
                    $('#div_lead_status_dormant1').addClass('d-none');
                } else if (this.value == 3) {
                    $('#div_lead_status_rejected1').addClass('d-none');
                    $('#div_lead_status_notinterested1').addClass('d-none');
                    $('#div_lead_status_irrelevant1').removeClass('d-none');
                    $('#div_lead_status_blocked1').addClass('d-none');
                    $('#div_lead_status_dormant1').addClass('d-none');
                } else if (this.value == 11) {
                    $('#div_lead_status_rejected1').addClass('d-none');
                    $('#div_lead_status_notinterested1').addClass('d-none');
                    $('#div_lead_status_irrelevant1').addClass('d-none');
                    $('#div_lead_status_blocked1').removeClass('d-none');
                    $('#div_lead_status_dormant1').addClass('d-none');
                } else if (this.value == 14) {
                    $('#div_lead_status_rejected1').addClass('d-none');
                    $('#div_lead_status_notinterested1').addClass('d-none');
                    $('#div_lead_status_irrelevant1').addClass('d-none');
                    $('#div_lead_status_blocked1').addClass('d-none');
                    $('#div_lead_status_dormant1').removeClass('d-none');
                }
                else {
                    $('#div_lead_status_rejected1').addClass('d-none');
                    $('#div_lead_status_notinterested1').addClass('d-none');
                    $('#div_lead_status_irrelevant1').addClass('d-none');
                    $('#div_lead_status_blocked1').addClass('d-none');
                    $('#div_lead_status_dormant1').addClass('d-none');
                }
            });
            //todo sub modal under status end
            $("#lead_status_rejected").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Rejected Reason",
                width: '100%',
                dropdownParent: $('#add_status_modal')
            });
            $("#lead_status_rejected1").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Rejected Reason",
                width: '100%',
                dropdownParent: $('#add_bulk_status_modal')
            });

            $("#lead_status_notinterested").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Not Interested Reason",
                width: '100%',
                dropdownParent: $('#add_status_modal')
            });
            $("#lead_status_notinterested1").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Not Interested Reason",
                width: '100%',
                dropdownParent: $('#add_bulk_status_modal')
            });
            $("#lead_status_irrelevant").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Irrelevant Reason",
                width: '100%',
                dropdownParent: $('#add_status_modal')
            });
            $("#lead_status_irrelevant1").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Irrelevant Reason",
                width: '100%',
                dropdownParent: $('#add_bulk_status_modal')
            });
            $("#lead_status_blocked").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Block Reason",
                width: '100%',
                dropdownParent: $('#add_status_modal')
            });
            $("#lead_status_blocked1").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Block Reason",
                width: '100%',
                dropdownParent: $('#add_bulk_status_modal')
            });
            $("#lead_status_dormant").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Dormant Reason",
                width: '100%',
                dropdownParent: $('#add_status_modal')
            });
            $("#lead_status_dormant1").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Dormant Reason",
                width: '100%',
                dropdownParent: $('#add_bulk_status_modal')
            });
            $('body').on('click', '#datatable .update', function () {
                status_lead_id = parseInt($(this).parents('tr').attr('id'));
                $('#add_status_modal').modal('show');
            });

            $('#add_status_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                normalizer: function (value) {
                    return $.trim(value);
                },
                submitHandler: function (form) {
                    var new_status = $('#update_lead_status').val();
                    var lead_status_rejected = $('#lead_status_rejected').val();
                    var lead_status_notinterested = $('#lead_status_notinterested').val();
                    var lead_status_irrelevant = $('#lead_status_irrelevant').val();
                    var lead_status_blocked = $('#lead_status_blocked').val();
                    var lead_status_dormant = $('#lead_status_dormant').val();
                    var check = 1;
                    if ((lead_status_rejected == "" && new_status == 10) || (lead_status_notinterested == "" && new_status == 4) || (lead_status_irrelevant == "" && new_status == 3) || (lead_status_blocked == "" && new_status == 11) || (lead_status_dormant == "" && new_status == 14)) {
                        check = 0;
                        var error = 'Reason  not Selected!';
                        toastr.error(error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                    if (new_status && check == 1) {
                        var reason;
                        if (lead_status_rejected)
                            reason = lead_status_rejected;

                        else if (lead_status_notinterested)
                            reason = lead_status_notinterested;

                        else if (lead_status_irrelevant)
                            reason = lead_status_irrelevant;

                        else if (lead_status_blocked)
                            reason = lead_status_blocked;

                        else if (lead_status_dormant)
                            reason = lead_status_dormant;

                        blockPagePermanently();
                        $.ajax({
                            url: "{{route('admin.leads.add_status')}}",
                            method: 'POST',
                            data: {
                                'lead_id': status_lead_id,
                                'status': new_status,
                                'reason': reason,
                                '_token': '{{ csrf_token() }}'
                            }
                        }).done(function (data) {
                            $('#add_status_modal').modal('hide');
                            UnblockPagePermanently();
                            new_status = null;
                            if (data.status == 1) {
                                toastr.success(data.success, 'Success!', {
                                    positionClass: 'toast-bottom-center',
                                    containerId: 'toast-bottom-center'
                                });
                                table.draw();
                            } else {
                                toastr.error(data.error, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }
                        });
                    } else {
                        if (check == 0) {
                        } else {
                            var error = 'Status not Selected!';
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    }
                }
            });

            //todo bulk_status ka form submit hora h
            $('#add_bulk_status_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                normalizer: function (value) {
                    return $.trim(value);
                },
                submitHandler: function (form) {

                    var new_status = $('#update_bulk_lead_status').val();
                    var lead_status_rejected = $('#lead_status_rejected1').val();
                    var lead_status_notinterested = $('#lead_status_notinterested1').val();
                    var lead_status_irrelevant = $('#lead_status_irrelevant1').val();
                    var lead_status_blocked = $('#lead_status_blocked1').val();
                    var lead_status_dormant = $('#lead_status_dormant1').val();
                    var check = 1;
                    if ((lead_status_rejected == "" && new_status == 10) || (lead_status_notinterested == "" && new_status == 4) || (lead_status_irrelevant == "" && new_status == 3) || (lead_status_blocked == "" && new_status == 11) || (lead_status_dormant == "" && new_status == 14)) {
                        check = 0;
                        var error = 'Reason  not Selected!';
                        toastr.error(error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                    if (new_status && check == 1) {
                        var reason;
                        if (lead_status_rejected)
                            reason = lead_status_rejected;

                        else if (lead_status_notinterested)
                            reason = lead_status_notinterested;

                        else if (lead_status_irrelevant)
                            reason = lead_status_irrelevant;

                        else if (lead_status_blocked)
                            reason = lead_status_blocked;

                        else if (lead_status_dormant)
                            reason = lead_status_dormant;

                        blockPagePermanently();
                        $.ajax({
                            url: "{{route('admin.leads.add_bulk_status')}}",
                            method: 'POST',
                            data: {
                                'lead_id[]': selected_rows,
                                'status': new_status,
                                'reason': reason,
                                '_token': '{{ csrf_token() }}'
                            }
                        }).done(function (data) {
                            $('#add_bulk_status_modal').modal('hide');
                            UnblockPagePermanently();
                            new_status = null;
                            selected_rows = [];

                            table.rows().deselect();
                            table.button('.update_status').disable();
                            if (data.status == 1) {
                                toastr.success(data.success, 'Success!', {
                                    positionClass: 'toast-bottom-center',
                                    containerId: 'toast-bottom-center'
                                });
                                table.draw();
                            } else {
                                toastr.error(data.error, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }
                        });
                    } else {
                        if (check == 0) {
                        } else {
                            var error = 'Status not Selected!';
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    }
                }
            });
            //todo bulk_status ka form submit end

            $('#add_status_modal').on('hide.bs.modal', function () {
                $('#update_lead_status').val('').trigger('change');
                $('#lead_status_rejected').val('').trigger('change');
                $('#lead_status_notinterested').val('').trigger('change');
                $('#lead_status_irrelevant').val('').trigger('change');
                $('#lead_status_blocked').val('').trigger('change');
                $('#lead_status_dormant').val('').trigger('change');
            });
            $('#add_bulk_status_modal').on('hide.bs.modal', function () {
                $('#update_bulk_lead_status').val('').trigger('change');
                $('#lead_status_rejected1').val('').trigger('change');
                $('#lead_status_notinterested1').val('').trigger('change');
                $('#lead_status_irrelevant1').val('').trigger('change');
                $('#lead_status_blocked1').val('').trigger('change');
                $('#lead_status_dormant1').val('').trigger('change');
            });

            $('body').on('click', '#datatable .add_remarks', function () {
                remark_lead_id = parseInt($(this).parents('tr').attr('id'));
                $('#add_remarks_modal').modal('show');
            });

            $('#add_remarks_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                normalizer: function (value) {
                    return $.trim(value);
                },
                submitHandler: function (form) {
                    var remarks = $('#add_remarks').val();
                    if (remark_lead_id != null) {
                        blockPagePermanently();
                        $.ajax({
                            url: "{{route('admin.leads.add_remarks')}}",
                            method: 'POST',
                            data: {
                                'lead_id': remark_lead_id,
                                'remarks': remarks,
                                '_token': '{{ csrf_token() }}'
                            }
                        }).done(function (data) {
                            UnblockPagePermanently();
                            $('#add_remarks_modal').modal('hide');
                            remark_lead_id = null;
                            toastr.success(data.success, 'Success!', {
                                positionClass: 'toast-bottom-center',
                                containerId: 'toast-bottom-center'
                            });
                        });
                    } else {
                        var error = 'Invalid Lead ID!';
                        toastr.error(error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                }
            });

            $('#add_remarks_modal').on('hide.bs.modal', function () {
                $('#add_remarks_form input.add_remarks').val('');
            });
            $("#search_form").validate({
                errorClass: "danger",
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    $.ajax({
                        url: '{!! route('admin.leads.lead_statistics') !!}',
                        method: 'POST',
                        data: {
                            'search_origin': $('#search_origin').val(),
                            'search_sale_person': $('#search_sale_person').val(),
                            'search_date_from': $('input[name="from_date_formatted"]').val(),
                            'search_date_to': $('input[name="to_date_formatted"]').val(),
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if (data.status === 1) {
                            $('#total_leads').text(data.leads.total);
                            $('#received_leads').text(data.leads.received);
                            $('#in_process').text(data.leads.in_process);
                            $('#dead_leads').text(data.leads.dead_leads);
                            $('#active_leads').text(data.leads.accounts_activated);
                            $('#dead_ratio').text(data.leads.dead_leads_ratio);
                            $('#active_ratio').text(data.leads.active_leads_ratio);
                            $('p#received_percentage').text(data.leads.received_percentage);
                            $('p#in_process_percentage').text(data.leads.in_process_percentage);
                            $('p#in_process_for_activation_percentage').text(data.leads.in_process_for_activation_percentage);
                            $('p#dead_percentage').text(data.leads.dead_leads_percentage);
                            $('p#active_percentage').text(data.leads.accounts_activated_percentage);
                        }
                    });
                    $('#search_statistics_div').val(null);
                    table.draw();
                }
            });
            $('#total_leads_div').on('click', function () {
                $('#search_statistics_div').val(1);
                table.draw();
            });
            $('#received_leads_div').on('click', function () {
                $('#search_statistics_div').val(2);
                table.draw();
            });
            $('#in_process_div').on('click', function () {
                $('#search_statistics_div').val(3);
                table.draw();
            });
            $('#dead_leads_div').on('click', function () {
                $('#search_statistics_div').val(4);
                table.draw();
            });
            $('#activated_leads_div').on('click', function () {
                $('#search_statistics_div').val(5);
                table.draw();
            });
            $('#in_process_activation_div').on('click', function () {
                $('#search_statistics_div').val(6);
                table.draw();
            });
            $('#dormant_div').on('click', function () {
                $('#search_statistics_div').val(7);
                table.draw();
            });


            $("#edit_territory").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Territory",
                width: '100%'
            });
            $("#edit_area").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Area",
                width: '100%'
            });

            $("#add_territory").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Territory*",
                width: '100%'
            });
            $("#add_area").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Area",
                width: '100%'
            });

            $("#add_service").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Service*",
                width: '100%'
            });

            $('#edit_phone_number').inputmask("Regex", { regex: "[+|0][0-9]*"});
            $("#edit_phone_number").inputmask({'mask': "9999-9999999", 'clearIncomplete': true});

            $('#add_phone_number').inputmask("Regex", { regex: "[+|0][0-9]*"});
            $("#add_phone_number").inputmask({'mask': "9999-9999999", 'clearIncomplete': true});



            $('#edit_city').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder:'Select City',
            }).bind('change', function () {
                var id = $(this).val();
                if(id) {
                    $.ajax({
                        url: '{!! route('cod.territory') !!}',
                        method: 'POST',
                        data: {
                            'id': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {

                        if (data.status == 0) {
                            $('#edit_territory').empty();
                            $.each(data.territory, function (key, value) {
                                var newOption = "<option value="+ value.id +">" + value.name + "</option>";
                                $('#edit_territory').append(newOption);
                            });
                            $('#edit_territory').val(territory).trigger('change');

                        } else {
                            $('#edit_territory').empty();
                            var error = 'No Territory found for the selected city';
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }

                    });
                }
            });
            $('#edit_territory').on('change',function () {
                var territory_id = $(this).val();
                if(territory_id){
                    $.ajax({
                        url: '{!! route('cod.area') !!}',
                        method: 'POST',
                        data: {
                            'id': territory_id,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                    .done(function (data) {

                        if (data.status == 0) {

                            $('#edit_area').empty();
                            $.each(data.area, function (key, value) {
                                var newOption = "<option value="+ value.id +">" + value.name + "</option>";
                                $('#edit_area').append(newOption);
                            });
                            $('#edit_area').val(area).trigger('change');
                        }
                        else{
                            $('#edit_area').empty();
                            var error = 'No Area found for the selected Territory';
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });
                }

            });
            $('#edit_lead_modal').on('hidden.bs.modal', function (e) {
                // $('#edit_lead_form')[0].reset();
                var form_errors = $('#edit_lead_form');
                form_errors.validate().resetForm();
                $('#edit_city').val('').trigger('change');
                $('#edit_territory').val('').trigger('change');
                $('#edit_area').val('').trigger('change');
                form_errors.find('.error').removeClass('error');
            });


            var lead_table = $('#lead_info_table').DataTable({
                dom: 'ltipr',
                scrollX: false,
                autoWidth : true,
                paging:false,
                bInfo:false,
                "order": [],
                columns: [
                    {name: 'city',  class: 'align-middle city', orderable: false, searchable: false},
                    {name: 'territory', class: 'align-middle tracking_number', orderable: false, searchable: false},
                    {name: 'area', class: 'align-middle order_id', orderable: false, searchable: false},
                    {name: 'phone_number', class: 'align-middle service_type', orderable: false, searchable: false},
                    {name: 'email_address', class: 'align-middle destination', orderable: false, searchable: false},
                    {name: 'brand', class: 'align-middle amount', orderable: false, searchable: false},
                    {name: 'company', class: 'align-middle open_box', orderable: false, searchable: false},
                ],
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });
            $('#datatable tbody').on('click', 'tr button.edit', function (){
               var lead_id = parseInt($(this).parents('tr').attr('id'));

               if(lead_id){
                   $.ajax({
                       url: "{{route('admin.leads.info')}}",
                       method: 'POST',
                       data: {
                           'lead_id': lead_id,
                           '_token': '{{ csrf_token() }}'
                       }
                   }).done(function (data) {
                        if(data.status == 0){

                            lead_table.clear();
                            var details = data.details;
                            lead_table.row.add([details.city, details.territory, details.area, details.phone_number, details.email_address, details.brand, details.company]).node().id = lead_id;
                            lead_table.draw(true);
                            if(details.city_id){
                                $('#edit_city').val(details.city_id).trigger('change');
                            }

                            if(details.area_id){
                                area = details.area_id;
                            }

                            if(details.territory_id){
                                territory = details.territory_id;
                            }

                            $('#edit_lead_modal_title span').text(lead_id);
                            $('#edit_lead_form #edit_lead_id').val(lead_id);
                            $('#edit_lead_form #edit_phone_number').val(details.phone_number);
                            $('#edit_lead_form #edit_email').val(details.email_address);
                            $('#edit_lead_form #edit_brand').val(details.brand);
                            $('#edit_lead_form #edit_company').val(details.company);
                            $('#edit_lead_form #edit_territory').trigger('change');
                            $('#edit_lead_form #edit_area').trigger('change');
                            $('#edit_lead_modal').modal('show');


                        }
                   });
               }
               else{
                   var error = 'Invalid Lead ID!';
                   toastr.error(error, 'Error!', {
                       positionClass: 'toast-top-center',
                       containerId: 'toast-top-center'
                   });
               }
            });


            $('#edit_lead_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                normalizer: function (value) {
                    return $.trim(value);
                },
                submitHandler: function (form) {
                    swal({
                        text: 'Are you sure, you want to edit this lead?',
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
                            var lead_id = $('#edit_lead_id').val();

                            if (lead_id != null) {
                                blockPagePermanently();
                                form.submit();
                            } else {
                                var error = 'Invalid Lead ID!';
                                toastr.error(error, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }
                        }
                    });
                }
            });

            $('#add_city').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder:'Select City*',
            }).bind('change', function () {
                var id = $(this).val();
                if(id) {
                    $.ajax({
                        url: '{!! route('cod.territory') !!}',
                        method: 'POST',
                        data: {
                            'id': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {

                        if (data.status == 0) {
                            $('#add_territory').empty();
                            $.each(data.territory, function (key, value) {
                                var newOption = "<option value="+ value.id +">" + value.name + "</option>";
                                $('#add_territory').append(newOption);
                            });
                            $('#add_territory').val(territory).trigger('change');

                        } else {
                            $('#add_territory').empty();
                            var error = 'No Territory found for the selected city';
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }

                    });
                }
            });
            $('#add_territory').on('change',function () {
                var territory_id = $(this).val();
                if(territory_id){
                    $.ajax({
                        url: '{!! route('cod.area') !!}',
                        method: 'POST',
                        data: {
                            'id': territory_id,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                        .done(function (data) {

                            if (data.status == 0) {

                                $('#add_area').empty();
                                $.each(data.area, function (key, value) {
                                    var newOption = "<option value="+ value.id +">" + value.name + "</option>";
                                    $('#add_area').append(newOption);
                                });
                                $('#add_area').val(area).trigger('change');
                            }
                            else{
                                $('#add_area').empty();
                                var error = 'No Area found for the selected Territory';
                                toastr.error(error, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }
                        });
                }

            });

            $('#add_lead_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                normalizer: function (value) {
                    return $.trim(value);
                },
                submitHandler: function (form) {
                    swal({
                        text: 'Are you sure, you want to add this lead?',
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
                            form.submit();
                        }
                    });
                }
            });

        });

    </script>
@endsection