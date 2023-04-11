@extends('admin.layout.master')

@section('title', 'Active Accounts List')

@section('content')
    <h1>Active Accounts List</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('admin.inc.messages')
    
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            @if (session('role_id') == 1 || count(array_intersect([276, 321], session('permissions'))) !== 0)
                            <div id="search_form" class="row p-1 mb-2">
                                <div class="col-4">
                                    <fieldset class="form-group">
                                        <select name="search_admins" id="search_admins" class="form-control select2" required data-rule-required="true" data-msg-required="This field is required">
                                            @foreach($sale_name as $admin)
                                                <option value="{{$admin->id}}">{{$admin->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>

                                <div class="col-4">
                                    <fieldset class="form-group">
                                        <input type="text" name="search_iban" id="search_iban" class="form-control iban" placeholder="IBAN">
                                    </fieldset>
                                </div>
                                <div class="col-4">
                                    <fieldset class="form-group">
                                        <input type="text" name="search_cnic" id="search_cnic" class="form-control cnic" placeholder="CNIC">
                                    </fieldset>
                                </div>
                                <div class="col-4">
                                    <fieldset class="form-group">
                                        <select name="search_shipper" id="search_shipper" class="form-control select2" required data-rule-required="true" data-msg-required="This field is required">
                                            @foreach($shippers as $shipper)
                                                <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-4">
                                    <fieldset class="form-group">
                                        <input type="email" name="search_email" id="search_email" class="form-control cnic" placeholder="Email">
                                    </fieldset>
                                </div>
                                <div class="col-2">
                                    <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                                </div>
                            </div>
                            @endif
                            <table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3">
                                <thead>
                                    <tr class="bg-primary white">
                                        <th class="border-primary border-darken-1"></th>
                                        <th class="border-primary border-darken-1">S. No</th>
                                        <th class="border-primary border-darken-1">Account ID</th>
                                        <th class="border-primary border-darken-1">Account Type</th>
                                        <th class="border-primary border-darken-1">Company Name</th>
                                        <th class="border-primary border-darken-1">Contact Person</th>
                                        <th class="border-primary border-darken-1">Address</th>
                                        <th class="border-primary border-darken-1">Region</th>
                                        <th class="border-primary border-darken-1">City</th>
                                        <th class="border-primary border-darken-1">Territory</th>
                                        <th class="border-primary border-darken-1">Product Type</th>
                                        <th class="border-primary border-darken-1">Status</th>
                                        <th class="border-primary border-darken-1">Sales Person Tagged</th>
                                        <th class="border-primary border-darken-1">POC Tagged</th>
                                        <th class="border-primary border-darken-1">KAM Tagged</th>
                                        <th class="border-primary border-darken-1">REF Tagged</th>
                                        <th class="border-primary border-darken-1">Request Date</th>
                                        <th class="border-primary border-darken-1">Rate Added By</th>
                                        <th class="border-primary border-darken-1">Rate Added At</th>
                                        <th class="border-primary border-darken-1">Rate Updated By</th>
                                        <th class="border-primary border-darken-1">Rate Status</th>
                                        <th class="border-primary border-darken-1">Rate Status Remarks</th>
                                        <th class="border-primary border-darken-1">Rate Approved By</th>
                                        <th class="border-primary border-darken-1">Rate Approved At</th>
                                        <th class="border-primary border-darken-1">Rates Rejected By</th>
                                        <th class="border-primary border-darken-1">Rates Rejected At</th>
                                        <th class="border-primary border-darken-1">Account Activated By</th>
                                        <th class="border-primary border-darken-1">Account Activation Date</th>
                                        <th class="border-primary border-darken-1">Account Disable Date</th>
                                        <th class="border-primary border-darken-1">Account Disable Remarks</th>
                                        <th class="border-primary border-darken-1">Account Disable Count</th>
                                        <th class="border-primary border-darken-1">Account Disable Days</th>
                                        <th class="border-primary border-darken-1">Document Uploaded At</th>
                                        <th class="border-primary border-darken-1">Documents Approved By</th>
                                        <th class="border-primary border-darken-1">Document Approved At</th>
                                        <th class="border-primary border-darken-1">Documents Rejected By</th>
                                        <th class="border-primary border-darken-1">Documents Rejected At</th>
                                        <th class="border-primary border-darken-1">Documents Status</th>
                                        <th class="border-primary border-darken-1">Documents Rejection Reason</th>
                                        <th class="border-primary border-darken-1">Duplicate</th>
                                        <th class="border-primary border-darken-1">Intl Rate Status</th>
                                        <th class="border-primary border-darken-1">Intl Rate Status Remarks</th>
                                        <th class="border-primary border-darken-1">Segment</th>
                                        <th class="border-primary border-darken-1">Sub Category Segment</th>
                                        <th class="border-primary border-darken-1">Referral Code</th>
                                        <th class="border-primary border-darken-1">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade text-left" id="SalesTierTypeTagModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="SalesTierTypeTagModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Tag Sales Tiers</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="shipper_id1">
                    <div class="mb-2">
                        <select name="poc" id="poc" class="form-control select2">
                            @foreach($sale_tier_types as $poc)
                                <option value="{{ $poc->id }}" > {{ $poc->name }} </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <select name="kam" id="kam" class="form-control select2">
                            @foreach($sale_tier_types as $kam)
                                <option value="{{ $kam->id }}" > {{ $kam->name }} </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <select name="ref" id="ref" class="form-control select2">
                            @foreach($sale_tier_types as $ref)
                                <option value="{{ $ref->id }}" > {{ $ref->name }} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="salesTierTypeTagSubmit">Submit</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="SalesTagModal1" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="SalesTagModal1"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Tag Sales Person</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="shipper_id1">
                    <select name="Sale_person" id="saletag1" class="form-control select2">
                        @foreach($sale_name as $sn)
                            <option value="{{ $sn->id }}" > {{ $sn->name }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="salesTagSubmit1">Submit</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="SetSegment" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="SetSegment"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Set Segment</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="shipper_id1">
                    <select name="Set_segment" id="set_segment" class="form-control select2">
                        @foreach($segments as $segment)
                            <option value="{{ $segment->id }}" > {{ $segment->name }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="setsegmentSubmit">Submit</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="SalesTagModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="SalesTagModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Tag Sales Person</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="shipper_id">
                    <select name="Sale_person" id="saletag" class="form-control select2">
                        @foreach($sale_name as $sn)
                            <option value="{{ $sn->id }}" > {{ $sn->name }} </option>
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
    <div class="modal fade text-left" id="ShipmentCancellationDaysModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ShipmentCancellationDaysModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Auto Shipment Cancel Days</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="shipper_id">
                    <input type="text" class="form-control cancellation_days" name="cancellation_days" id="cancellation_days" placeholder="Days*" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="accountDisableDaysSubmit">Submit</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="duplicate_modal" data-backdrop="static" role="dialog" aria-labelledby="duplicate_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="bookings_modal_title">Duplicate Data</h4>

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

    <div class="modal fade text-left" id="PaymentCycleModal" data-backdrop="static" role="dialog" aria-labelledby="PaymentCycleModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Payment Cycle</h4>
                </div>
                <form id="payment_cycle_form" class="form" novalidate="novalidate" method="post" action="{{ route('admin.accounts.payment_cycle.submit') }}">
                    @csrf
                <div class="modal-body">
                    <input type="hidden" name="shipper_id" id="shipper_id">
                    <div class="form-group">
                        <select name="payment_cycle_select" id="payment_cycle_select" class="form-control select2" data-rule-required="true" data-msg-required="Payment Cycle is required">
                            @foreach($payment_cycles as $pc)
                                <option value="{{ $pc->id }}" > {{ $pc->name }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" id="payment_day_div">
                        <label for="payment_day">1 for Monday, 5 for Friday or For Monthly select date between (1 - 29)</label>
                        <input type="text" name="payment_day" id="payment_day" class="form-control" data-rule-required="true" data-msg-required="Payment Day is required">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id="payment_cycle_submit">Submit</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="RestrictOrderIDModal" data-backdrop="static" role="dialog" aria-labelledby="RestrictOrderIDModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Restrict Order ID</h4>
                </div>
                <form id="restrict_order_id_form" class="form" novalidate="novalidate" method="post" action="{{ route('admin.accounts.restrict_order_id.submit') }}">
                    @csrf
                <div class="modal-body">
                    <div class="col text-center">
                        <label class="font-medium-2 font-weight-bold block">Restrict Order ID for Booking</label>
                        <div class="form-group">
                            <input type="hidden" name="user_id" id="restrict_user_id">
                            <label for="restrict_order_id_checkbox" class="font-medium-2 text-bold-600 mr-1">No</label>
                            <input type="checkbox" name="restrict_order_id_checkbox" id="restrict_order_id_checkbox" class="switchery restrict_order_id_checkbox" data-size="sm" data-switchery="true">
                            <label for="restrict_order_id_checkbox" class="font-medium-2 text-bold-600 ml-1">Yes</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id="restrict_order_id_submit">Submit</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="RateHistoryModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="RateHistoryModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Select Date</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                   {{-- <form id="rate_history" action="#"  novalidate="novalidate">--}}
                       {{-- @method('post')--}}
                      {{--  {{ csrf_field() }}--}}
                        <input type="text" hidden id="user_id" name="user_id">
                        <div class="col-12">

                                <select name="old_rate_date"  id="old_rate_date" class="form-control select2">

                                </select>

                        </div>

                        {{--<div class="row justify-content-center mt-4">
                            <div class="col-4">
                                <button id="edit" type="submit" class="btn btn-primary btn-block">Submit</button>
                            </div>
                        </div>
                    </form>--}}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="TerritoryTag" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="TerritoryTagModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Tag Territory</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                        <form id="set_territory" action="{{route('admin.accounts.add_territory')}}" method="post">
                            @csrf
                            @method('post')
                            <div class="form-group text-center">
                                <input type="text" hidden name="user_ids" class="user_ids">
                                <select name="territory" id="territory" class="form-control select2" data-rule-required="true" data-msg-required="Territory is required">
                                    @foreach($territories as $territory)
                                        <option value="{{ $territory->id }}" > {{ $territory->name }} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mt-2" style="text-align: center">
                                <button type="submit" class="btn btn-success" id="territoryTagSubmit">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="TerritoryReTag" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="TerritoryReTagModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Re-Tag Territory</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                        <form id="set_retag_territory" action="{{route('admin.accounts.add_retag_territory')}}" method="post">
                            @csrf
                            @method('post')
                            <div class="form-group text-center">
                                <input type="text" hidden name="user_ids" class="user_ids">
                                <select name="territory" id="retagterritory" class="form-control select2" data-rule-required="true" data-msg-required="Territory is required">
                                    @foreach($territories as $territory)
                                        <option value="{{ $territory->id }}" > {{ $territory->name }} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mt-2" style="text-align: center">
                                <button type="submit" class="btn btn-success" id="territoryReTagSubmit">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="ChangeRateType" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ChangeRateTypeModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Change Rate Type</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                        <div class="form-group text-center">
                            <input type="text" hidden name="user_id" class="user_id">
                            <select name="corporate_rate_type" id="corporate_rate_type" class="form-control select2" data-rule-required="true" data-msg-required="Rate Type is required">
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="RemoveSalesTierTaggingModal" data-backdrop="static" role="dialog" aria-labelledby="RemoveSalesTierTaggingModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Remove Sales Tier Tagging</h4>
                </div>
                <form id="remove_sales_tier_form" class="form" novalidate="novalidate" method="post" action="{{ route('admin.accounts.kam_poc_ref_tag.remove') }}">
                    @csrf
                    <input type="hidden" name="shipper_id" id="shipper_id">
                    <div class="modal-body">

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="payment_cycle_submit">Submit</button>
                        <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="CreditLimitModal" data-backdrop="static" role="dialog" aria-labelledby="CreditLimitModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Credit Limit</h4>
                </div>
                <form id="set_credit_limit_form" class="form" novalidate="novalidate" method="post">
                <input type="hidden" name="shipper_id" id="credit_shipper_id">
                <div class="modal-body">
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="bg-primary white">
                                        <td class="border-primary border-darken-1">User</td>
                                        <td class="border-primary border-darken-1">Limit Used</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td id="credit_user"></td>
                                        <td id="credit_user_limit_used"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="credit_limit">Maximum Credit Limit</label>
                                <input type="text" name="credit_limit" id="credit_limit" class="form-control" data-rule-required="true" data-msg-required="Credit is required" placeholder="Maximum credit limit">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id="credit_limit_btn">Submit</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
                </form>

            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="SegmentTagModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="SegmentTagModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Tag Segments</h4>
                </div>
                <div class="modal-body">
                    <div>
                        <form id="set_segments" action="{{route('admin.accounts.add_segments')}}" method="post">
                            @csrf
                            @method('post')
                            <input type="text" hidden name="user_ids" class="user_ids">

                            <div class="form-group text-center">
                                <select name="bulk_segment" id="bulk_segment1" class="form-control select2" data-rule-required="true" data-msg-required="Segment is required">
                                    @foreach($segments as $seg)
                                        <option value="{{ $seg->id }}" > {{ $seg->name }} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group text-center">
                                <select name="bulk_sub_segment" id="bulk_sub_segment1" class="form-control select2" data-rule-required="true" data-msg-required="Sub Segment is required">
                                </select>
                            </div>
                            <div class="mt-2" style="text-align: center">
                                <button type="submit" class="btn btn-success" id="segmentTagSubmit1">Submit</button>
                            </div>
                        </form>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-success" id="segmentTagSubmit1">Submit</button> --}}
                    {{-- <button type="button" class="btn btn-info" data-dismiss="modal">Close</button> --}}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="AutoCancelationDaysModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AutoCancelationDaysModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Auto Cancelation Days</h4>
                </div>
                <div class="modal-body">
                    <div>
                        <form id="auto_cancelation_days_form" action="{{route('admin.accounts.auto_cancelation_days')}}" method="post">
                            @csrf
                            @method('post')
                            <input type="text" hidden name="user_id" id="user_id">

                            <div class="form-group text-center">
                                <input type="text" class="form-control" placeholder="Auto Cancelation Days" name="cancelation_days" id="cancelation_days" data-rule-required="true" data-msg-required="Cancelation Day is Required">
                            </div>
                            <div class="mt-2" style="text-align: center">
                                <button type="submit" class="btn btn-success" id="AutoCancelationDaysSubmit">Submit</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

<div class="modal fade text-left" id="CorporateInvoiceLogModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="CorporateInvoiceLogModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Packaging Invoice Toggle Log</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                   
                </div>
            </div>
        </div>
    </div>





{{-- Add Fintech Charges Modal --}}


<div class="modal fade text-left" id="AddFintechChargesModal" data-backdrop="static" role="dialog" aria-labelledby=""
    aria-hidden="true">
   <div class="modal-dialog modal-md" role="document">
       <div class="modal-content">
           <div class="modal-header">
               <h4 class="modal-title">Add Fintech Charges</h4>
           </div>
               <div class="modal-body">
                   <div class="col text-center">
                       <label class="font-medium-2 font-weight-bold block">Is Shipper Paying Fintech Charges ?</label>
                       <div class="form-group">
                           <input type="hidden" id="userID">
                           <label for="" class="font-medium-2 text-bold-600 mr-1">No</label>
                           <input type="checkbox" onchange="checkboxStatus()" id="user_fintech_charges_checkbox" class="switchery restrict_order_id_checkbox" data-size="sm" data-switchery="true">
                           <label for="" class="font-medium-2 text-bold-600 ml-1">Yes</label>
                       </div>

                       <div class="row justify-content-center UserFintechCharges"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button " class="btn btn-success"  onclick="save_fintech_charges()" >Submit</button>
                        <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                    </div>
                   </div>
               </div>
               
          
       </div>
   </div>
</div>

{{-- End Add Fintech Charges Modal --}}



@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">


@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">

function checkboxStatus(){
    if (document.getElementById('user_fintech_charges_checkbox').checked) {
        $(".UserFintechCharges").html(''); 
              $(".UserFintechCharges").append(`
            <div class="form-group text-left">
              
                <input type="number" id="user_fintech_charges_txtbox" class="form-control valid" placeholder="Fintech Charges *" aria-invalid="false">
            </div>`);
        } 
        else{
                $(".UserFintechCharges").html('');     
        } 
}

function save_fintech_charges(){
        if (document.getElementById('user_fintech_charges_checkbox').checked) {
               var fintechCharges = $("#user_fintech_charges_txtbox").val();
               var userID = $("#userID").val();
               $.ajax({
                type : 'POST',
                url  :  "{!! route('admin.accounts.add_fintech_charges') !!}",
                data : {fintechCharges:fintechCharges,userID:userID,'_token': '{{ csrf_token() }}'},
                success:function(res){
                    if(res.status == '201'){
                        $('#AddFintechChargesModal').modal('hide');
                    }
                }
            });

        } 
        else{
            $("#user_fintech_charges_txtbox").val('');
        }
    }
    $(document).ready(function() {

        $("input[name='search_phone']").inputmask({'mask': "9999-9999999", 'clearIncomplete': true});
        $("input[name='search_cnic']").inputmask({'mask': "99999-9999999-9", 'clearIncomplete': true});
        $('body').on('change','#search_iban',function() {
            $(this).val($(this).val().trim());
        });
        $('.cancellation_days').inputmask({
            'alias': 'integer',
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            'min': 0,
            'max': 200
        });

        $('#credit_limit').inputmask({
            'alias': 'integer',
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            'min': 100,
        });
        $("#bulk_sub_segment1").prepend('<option value="" selected></option>').select2({
            placeholder: "Select Sub Segment",
            width:'100%',
            dropdownParent:$('#set_segments')
        });
        
        $("#bulk_segment1").prepend('<option value="" selected></option>').select2({
            placeholder: "Select Segment",
            width:'100%',
            dropdownParent:$('#set_segments')
        }).bind('change', function() {
                var id = parseInt($(this).val());
                $.ajax({
						url: '{!! route('cod.get_sub_segment') !!}',
						method: 'POST',
						data: {
							'segment_id': id,
							'_token': '{{ csrf_token() }}'
						}
					})
					.done(function(data) {
                    $("#bulk_sub_segment1").html('');

							if (data.status == 0) {
								var sub_segment = data.sub_segments;

                                $.each(data.sub_segments, function (index, sub_segment) {
									console.log(index);	
									console.log(sub_segment);	
                                    $('#bulk_sub_segment1').append('<option value="' + sub_segment['id'] + '" class="select2">' + sub_segment['name'] + '</option>');
									});

                            }


                });
            });
           
            
        $("#territory").prepend('<option value="" selected></option>').select2({
            placeholder: "Select Territory",
            width:'100%',
            dropdownParent:$('#TerritoryTag')
        });
        $("#retagterritory").prepend('<option value="" selected></option>').select2({
            placeholder: "Select Territory For Retagging",
            width:'100%',
            dropdownParent:$('#TerritoryReTag')
        });
        $('#search_admins').prepend('<option value="" selected></option>').select2({
            width:'100%',
            placeholder:"Select Sale Persons",
            allowClear:true,
        });
        $('#search_shipper').prepend('<option value="" selected></option>').select2({
            width:'100%',
            placeholder:"Select Shipper",
            allowClear:true,
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
                    url: '{{ route('admin.accounts.active.ajax') }}',
                    method: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: params,
                    success: function (result) {
                        head = [];

                        head.push('S.No');
                        head.push('Account ID');
                        head.push('Account Type');
                        head.push('Company Name');
                        head.push('Contact Person');
                        head.push('Address');
                        head.push('Region');
                        head.push('City');
                        head.push('Territory');
                        head.push('Product Type');
                        head.push('Status');
                        head.push('Sales Person Tagged');
                        head.push('POC Tagged');
                        head.push('KAM Tagged');
                        head.push('REF Tagged');
                        head.push('Request Date');
                        head.push('Rates Added By');
                        head.push('Rates Added At');
                        head.push('Rates Updated By');
                        head.push('Rates Status');
                        head.push('Rates Status Remarks');
                        head.push('Rates Approved By');
                        head.push('Rates Approved At');
                        head.push('Rates Rejected By');
                        head.push('Rates Rejected At');
                        head.push('Account Activated By');
                        head.push('Account Activation Date');
                        head.push('Account Disable Date');
                        head.push('Account Disable Remarks');
                        // head.push('Account Disable Remarks');
                        head.push('Account Disable Count');
                        head.push('Account Disable Day(s)');
                        head.push('Documents Uploaded At');
                        head.push('Documents Approved By');
                        head.push('Documents Approved At');
                        head.push('Documents Rejected By');
                        head.push('Documents Rejected At');
                        head.push('Documents Status');
                        head.push('Documents Rejection Reason');
                        head.push('Intl Rates Status');
                        head.push('Intl Rates Status Remarks');
                        head.push('Segment');
                        head.push('Sub Category Segment');
                        head.push('Referral Code');
                        $.each(result.data, function(index, values) {
                            row = [];


                            row.push(index + 1);
                            row.push(values.id_padded);
                            row.push(values.account_type);
                            row.push(values.name);
                            row.push(values.poc);
                            row.push(values.address);
                            row.push(values.zone);
                            row.push(values.city);
                            row.push(values.territory);
                            row.push(values.product_type);
                            row.push(values.status);
                            row.push(values.admin_tag_id);
                            row.push(values.tagged_poc);
                            row.push(values.kam);
                            row.push(values.ref);
                            row.push(values.created_at);
                            row.push(values.added_by);
                            row.push(values.rates_added_at);
                            row.push(values.updated_by);
                            row.push(values.rate_status);
                            row.push(values.rejected_reason);
                            row.push(values.approved_by);
                            row.push(values.rates_approved_at);
                            row.push(values.rates_rejected_by);
                            row.push(values.rates_rejected_at);
                            row.push(values.account_activated_by);
                            row.push(values.activated_date);
                            row.push(values.disable_at);
                            row.push(values.disable_remarks);
                            row.push(values.status_count);
                            row.push(values.days_to_disable);
                            row.push(values.documents_uploaded_at);
                            row.push(values.documents_approved_by);
                            row.push(values.documents_approved_at);
                            row.push(values.documents_rejected_by);
                            row.push(values.documents_rejected_at);
                            row.push(values.documents_status);
                            row.push(values.documents_rejection_reason);
                            row.push(values.international_rate_status);
                            row.push(values.international_rejected_reason);
                            row.push(values.segment);
                            row.push(values.sub_segment);
                            row.push(values.referral_name);

                            body.push(row);
                        });
                    },
                    async: false
                });

                return {body: body, header: head};
            }
        } );
       var selected_rows = [];
       var table = $('#datatable').DataTable({
           dom: '<"d-inline-block"l><"pull-right"B>tipr',
           scrollX: true, scrollY: '800px',
           buttons: [
            @if (session('role_id') == 1 || in_array(609, session('permissions')))
                    {
                            text: 'Segment Tagging',
                            className: 'btn btn-primary bulk_segment_tagging',
                            enabled:false,
                            action: function (e, dt, node, config) {
                                if(selected_rows != ''){
                           $('.user_ids').val(selected_rows);
                           $('#SegmentTagModal').modal('show');

                       }else{
                           var error = "Atleast Select One Shipper";
                           toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                       }
                        //    if(selected_rows != ''){
                              
                        //         $('#SegmentTagModal').modal('show');
                        //         // console.log(selected_rows);
                        //         $('#segmentTagSubmit1').on('click',function () {
                        //             var assign = parseInt($('#saletag1').val());
                        //             swal({
                        //                 text: 'Are you sure, you want to Tag?',
                        //                 icon: 'info',
                        //                 buttons: {
                        //                     cancel: {
                        //                         text: 'No',
                        //                         value: null,
                        //                         visible: true,
                        //                         closeModal: true,
                        //                     },
                        //                     confirm: {
                        //                         text: 'Yes',
                        //                         value: true,
                        //                         visible: true,
                        //                         closeModal: true
                        //                     }
                        //                 },
                        //                 closeOnClickOutside: false,
                        //                 closeOnEsc: false,
                        //                 dangerMode: true
                        //             }).then(function(confirm) {
                        //                 if (confirm) {
                        //                     if (assign) {
                        //                         $.ajax({
                        //                             url: '{!! route('admin.accounts.tag.submit.bulk') !!}',
                        //                             method: 'POST',
                        //                             data: {
                        //                                 'admin_id': assign,
                        //                                 'shipper_ids[]': selected_rows,
                        //                                 '_token': '{{ csrf_token() }}'
                        //                             }
                        //                         })
                        //                             .done(function (data) {
                        //                                 if (data.status == 1) {
                        //                                     $('#SegmentTagModal').modal('hide');
                        //                                     toastr.success(data.success, 'Success!', {
                        //                                         positionClass: 'toast-bottom-center',
                        //                                         containerId: 'toast-bottom-center'
                        //                                     });
                        //                                 } else {
                        //                                     toastr.error(data.error, 'Error!', {
                        //                                         positionClass: 'toast-top-center',
                        //                                         containerId: 'toast-top-center'
                        //                                     });
                        //                                 }
                        //                                 selected_rows = [];

                        //                                 table.rows().deselect();
                        //                                 $('#saletag1').val('').trigger('change');
                        //                                 $('#SegmentTagModal').modal('hide');
                        //                                 table.draw(true);
                        //                                 table.button('.bulk_tagging').disable();
                        //                                 table.button('.bulk_segment_tagging').disable();
                        //                                 table.button('.set_commission').disable();
                        //                                 table.button('.tag').disable();
                        //                                 table.button('.approve_commission').disable();
                        //                                 table.button('.territory_tag').disable();

                        //                             });
                        //                     } else {
                        //                         var error = "Account Not Selected!";
                        //                         toastr.error(error, 'Error!', {
                        //                             positionClass: 'toast-top-center',
                        //                             containerId: 'toast-top-center'
                        //                         });
                        //                     }
                        //                 }
                        //             });
                        //         });

                        //     }else{
                        //         var error = "Account Not selected!";
                        //         toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        //     }
                        }
                    },
                    @endif
                   @if (session('role_id') == 1 || in_array(427, session('permissions')))
                    {
                        text: 'Set Commission',
                        className: 'btn btn-primary set_commission',
                        enabled:false,
                        action: function (e, dt, node, config) {
                            if(selected_rows != ''){
                                swal({
                                    title: 'Are You Sure?',
                                    text: 'Select Yes to Set Commission!',
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
                                }).then(function (confirm) {
                                    if (confirm) {
                                        var link = '{{ route('admin.settings.commission.set_commission', ["ids" => 0]) }}';
                                        window.location = link.substr(0, link.lastIndexOf('/')) + '/' + selected_rows;
                                    }
                                });
                            }
                            else{
                                var error = "Something went wrong please refresh page and try again!";
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                            }
                        }
                    },
                   /* {
                        text: 'Set Segment',
                        className: 'btn btn-primary set_segment',
                        enabled:false,
                        action: function (e, dt, node, config) {
                            if(selected_rows != ''){

                                $('#SetSegment').modal('show');
                                $('#setsegmentSubmit').on('click',function () {
                                    var segment = parseInt($('#set_segment').val());
                                    console.log(segment);
                                    swal({
                                        text: 'Are you sure, you want to set Segment?',
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
                                            if (segment) {
                                                $.ajax({
                                                    url: '{!! route('admin.accounts.set.segment_bulk') !!}',
                                                    method: 'POST',
                                                    data: {
                                                        'segment_id': segment,
                                                        'shipper_ids[]': selected_rows,
                                                        '_token': '{{ csrf_token() }}'
                                                    }
                                                })
                                                    .done(function (data) {
                                                        if (data) {
                                                            $('#SetSegment').modal('hide');
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
                                                        $('#set_segment').val('').trigger('change');
                                                        $('#SetSegment').modal('hide');
                                                        table.draw(true);
                                                        table.button('.bulk_tagging').disable();
                                                        table.button('.set_segment').disable();
                                                        table.button('.set_commission').disable();
                                                        table.button('.approve_commission').disable();

                                                    });
                                            } else {
                                                var error = "Account Not Selected!";
                                                toastr.error(error, 'Error!', {
                                                    positionClass: 'toast-top-center',
                                                    containerId: 'toast-top-center'
                                                });
                                            }
                                        }
                                    });
                                });

                            }else{
                                var error = "Account Not selected!";
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }

                        }
                    },*/
                    {
                        text: 'Approve Commission',
                        className: 'btn btn-primary approve_commission',
                        enabled:false,
                        action: function (e, dt, node, config) {
                            if(selected_rows != ''){
                                swal({
                                    title: 'Are You Sure?',
                                    text: 'Select Yes to Approve Commission!',
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
                                }).then(function (confirm) {
                                    if (confirm) {
                                        var link = '{{ route('admin.settings.commission.approve_commission', ["ids" => 0]) }}';
                                        window.location = link.substr(0, link.lastIndexOf('/')) + '/' + selected_rows;
                                    }
                                });


                            }
                            else{
                                var error = "Something went wrong please refresh page and try again!";
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                            }
                        }
                    },
                    @endif
                    @if (session('role_id') == 1 || in_array(361, session('permissions')))
                    {
                            text: 'Tagging',
                            className: 'btn btn-primary bulk_tagging',
                            enabled:false,
                            action: function (e, dt, node, config) {
                           if(selected_rows != ''){
                              
                                $('#SalesTagModal1').modal('show');
                                // console.log(selected_rows);
                                $('#salesTagSubmit1').on('click',function () {
                                    var assign = parseInt($('#saletag1').val());
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
                                    }).then(function(confirm) {
                                        if (confirm) {
                                            if (assign) {
                                                $.ajax({
                                                    url: '{!! route('admin.accounts.tag.submit.bulk') !!}',
                                                    method: 'POST',
                                                    data: {
                                                        'admin_id': assign,
                                                        'shipper_ids[]': selected_rows,
                                                        '_token': '{{ csrf_token() }}'
                                                    }
                                                })
                                                    .done(function (data) {
                                                        if (data.status == 1) {
                                                            $('#SalesTagModal1').modal('hide');
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
                                                        $('#saletag1').val('').trigger('change');
                                                        $('#SalesTagModal1').modal('hide');
                                                        table.draw(true);
                                                        table.button('.bulk_tagging').disable();
                                                        table.button('.bulk_segment_tagging').disable();
                                                        table.button('.set_commission').disable();
                                                        table.button('.tag').disable();
                                                        table.button('.approve_commission').disable();
                                                        table.button('.territory_tag').disable();
                                                        table.button('.territory_retag').disable();

                                                    });
                                            } else {
                                                var error = "Account Not Selected!";
                                                toastr.error(error, 'Error!', {
                                                    positionClass: 'toast-top-center',
                                                    containerId: 'toast-top-center'
                                                });
                                            }
                                        }
                                    });
                                });

                            }else{
                                var error = "Account Not selected!";
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        }
                    },

               {
                   text: 'Sales Tier Tagging',
                   className: 'btn btn-primary tag',
                   enabled:false,
                   action: function (e, dt, node, config) {
                       if(selected_rows != ''){

                           $('#SalesTierTypeTagModal').modal('show');
                           $('#salesTierTypeTagSubmit').on('click',function () {
                               var poc = $('#poc').val();
                               var kam = $('#kam').val();
                               var ref = $('#ref').val();
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
                               }).then(function(confirm) {
                                   if (confirm) {

                                       $.ajax({
                                           url: '{!! route('admin.accounts.kam_poc_ref_tag.submit') !!}',
                                           method: 'POST',
                                           data: {
                                               'poc': poc,
                                               'kam': kam,
                                               'ref': ref,
                                               'shipper_ids[]': selected_rows,
                                               '_token': '{{ csrf_token() }}'
                                           }
                                       })
                                           .done(function (data) {
                                               if (data.status === 0) {
                                                   toastr.error(data.error, 'Error!', {
                                                       positionClass: 'toast-top-center',
                                                       containerId: 'toast-top-center'
                                                   });
                                               } else {
                                                   $('#SalesTierTypeTagModal').modal('hide');
                                                   toastr.success(data.success, 'Success!', {
                                                       positionClass: 'toast-bottom-center',
                                                       containerId: 'toast-bottom-center'
                                                   });
                                               }
                                               selected_rows = [];

                                               table.rows().deselect();
                                               $('#SalesTierTypeTagModal').modal('hide');
                                               table.draw(true);
                                               table.button('.tag').disable();
                                               table.button('.bulk_tagging').disable();
                                               table.button('.bulk_segment_tagging').disable();
                                               table.button('.set_commission').disable();
                                               table.button('.approve_commission').disable();
                                               table.button('.territory_tag').disable();
                                               table.button('.territory_retag').disable();


                                           });
                                   }
                               });
                           });

                       }else{
                           var error = "Atleast Select One Shipper";
                           toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                       }
                   }
               },
                    @endif

                   @if (session('role_id') == 1 || in_array(445, session('permissions')))
               {
                   text: 'Tag Territory',
                   className: 'btn btn-primary territory_tag',
                   enabled:false,
                   action: function (e, dt, node, config) {
                       if(selected_rows != ''){
                           $('.user_ids').val(selected_rows);
                           $('#TerritoryTag').modal('show');

                       }else{
                           var error = "Atleast Select One Shipper";
                           toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                       }
                   }
               },
               @endif
               @if (session('role_id') == 1 || in_array(658, session('permissions')))
                {
                    text: 'Re-Tag Territory',
                    className: 'btn btn-primary territory_retag',
                    enabled:false,
                    action: function (e, dt, node, config) {
                        if(selected_rows != ''){
                            $('.user_ids').val(selected_rows);
                            $('#TerritoryReTag').modal('show');

                        }else{
                            var error = "Atleast Select One Shipper";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    }
                },
                @endif
                    {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox') && !$(row.node()).hasClass('selected')) {
                                    id = parseInt(row.id());

                                    hub_id = $(row.node()).data('id');

                                    var allow = false;

                                    if(hub_ids.length == 0) {
                                        hub_ids.push(hub_id);

                                        allow = true;
                                    }
                                    else if(hub_ids[0] == hub_id) {
                                        allow = true;
                                    }

                                    if (allow) {
                                        row.select();

                                        var index = $.inArray(id, selected_rows);

                                        if (index === -1) {
                                            selected_rows.push(id);
                                        }

                                        table.button('.bulk_tagging').enable();
                                        table.button('.bulk_segment_tagging').enable();
                                        table.button('.set_commission').enable();
                                        table.button('.approve_commission').enable();
                                        table.button('.set_segment').enable();
                                        table.button('.tag').enable();
                                        table.button('.territory_tag').enable();
                                        table.button('.territory_retag').enable();
                                    }
                                }
                            });
                        }
                    }, {
                        extend: 'selectNone',
                        text: 'Select None',
                        className: 'select_none',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
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
                                        table.button('.bulk_segment_tagging').disable();
                                        table.button('.set_commission').disable();
                                        table.button('.approve_commission').disable();
                                        table.button('.set_segment').disable();
                                        table.button('.tag').disable();
                                        table.button('.territory_tag').disable();
                                        table.button('.territory_retag').disable();
                                        hub_ids.splice(index, 1);
                                    }
                                }
                            });
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Active Accounts',
                        className:'btn-primary',
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
           deferLoading: 0,
            rowId: 'id',
            order: [[27, 'desc']],
            ajax: {
               url: '{{ route('admin.accounts.active.ajax') }}',
                method: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
               data: function (d) {
                   d.sale_persons = $('#search_admins').val();
                   d.search_cnic = $('#search_cnic').val();
                   d.search_shipper = $('#search_shipper').val();
                   d.search_iban = $('#search_iban').val();
                   d.search_email = $('#search_email').val();
               }
           },
            columns: [
                {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                {data: 'id_padded', name: 'users.id', class: 'align-middle account_id'},
                {data: 'account_type', name: 'at.name', class: 'align-middle account_type'},
                {data: 'name', name: 'name', class: 'align-middle company_name'},
                {data: 'poc', name: 'poc', class: 'align-middle contact_person'},
                {data: 'address', name: 'users.address', class: 'align-middle address'},
                {data: 'zone', name: 'z.name', class: 'align-middle zone'},
                {data: 'city', name: 'cities.name', class: 'align-middle city'},
                {data: 'territory', name: 't.name', class: 'align-middle territory'},
                {data: 'product_type', name: 'product_type', class: 'align-middle product_type'},
                {data: 'status', name: 'status', class: 'align-middle status'},
                {data: 'admin_tag_id', name: 'ad.name', class: 'align-middle admin_tag_id'},
                {data: 'tagged_poc', name: 'poc.name', class: 'align-middle tagged_poc'},
                {data: 'kam', name: 'k.name', class: 'align-middle kam'},
                {data: 'ref', name: 'r.name', class: 'align-middle ref'},
                {data: 'created_at', name: 'users.created_at', class: 'align-middle created_at'},
                {data: 'added_by', name: 'rab.name', class: 'align-middle added_by'},
                {data: 'rates_added_at', name: 'users.rates_added_at', class: 'align-middle rates_added_at'},
                {data: 'updated_by', name: 'rabna.name', class: 'align-middle updated_by'},
                {data: 'rate_status', name: 'rate_status', class: 'align-middle rate_status'},
                {data: 'rejected_reason', name: 'users.rejected_reason', class: 'align-middle rejected_reason'},
                {data: 'approved_by', name: 'rabb.name', class: 'align-middle approved_by'},
                {data: 'rates_approved_at', name: 'users.rates_approved_at', class: 'align-middle rates_approved_at'},
                {data: 'rates_rejected_by', name: 'rrb.name', class: 'align-middle rates_rejected_by'},
                {data: 'rates_rejected_at', name: 'users.rates_rejected_at', class: 'align-middle rates_rejected_at'},
                {data: 'account_activated_by', name: 'rabba.name', class: 'align-middle account_activated_by'},
                {data: 'activated_date', name: 'users.activated_at', class: 'align-middle activated_date'},
                {data: 'disable_at', name: 'users.disable_at', class: 'align-middle disable_at'},
                {data: 'disable_remarks', name: 'users.disable_remarks', class: 'align-middle disable_remarks', orderable: false, searchable: false},
                {data: 'status_count', name: 'ucs.status_count', class: 'align-middle status_count'},
                {data: 'days_to_disable', name: 'days_to_disable', class: 'align-middle days_to_disable'},
                {data: 'documents_uploaded_at', name: 'uda.uploaded_at', class: 'align-middle documents_uploaded_at', searchable: false},
                {data: 'documents_approved_by', name: 'dab.name', class: 'align-middle documents_approved_by', searchable: false},
                {data: 'documents_approved_at', name: 'uda.approved_at', class: 'align-middle documents_approved_at', searchable: false},
                {data: 'documents_rejected_by', name: 'drb.name', class: 'align-middle documents_rejected_by', searchable: false},
                {data: 'documents_rejected_at', name: 'uda.rejected_at', class: 'align-middle documents_rejected_at', searchable: false},
                {data: 'documents_status', name: 'users.documents_status', class: 'align-middle documents_status'},
                {data: 'documents_rejection_reason', name: 'users.documents_status_reason', class: 'align-middle documents_rejection_reason'},
                {data: 'duplication', name: 'duplication', class: 'align-middle duplicate', orderable: false, searchable: false},
                {data: 'international_rate_status', name: 'iui.status', class: 'align-middle international_rate_status'},
                {data: 'international_rejected_reason', name: 'international_rejected_reason', class: 'align-middle international_rejected_reason', orderable: false, searchable: false},
                {data: 'segment', name: 'seg.name', class: 'align-middle segment'},
                {data: 'sub_segment', name: 'seg_sub.name', class: 'align-middle sub_segment'},
                {data: 'referral_name', name: 'ref.name', class: 'align-middle referral_name'},
                
                {data: 'action', name: 'action', class: 'align-middle action', orderable: false, searchable: false}
            ],
           rowCallback: function(row, data, index) {
            //    var info = table.page.info();
            //    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                var info = table.page.info();

                $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                if ($.inArray(data.id, selected_rows) !== -1) {
                    table.row(row).select();
                }
           },
            initComplete: function() {
                var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                var drop_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                    '<option value="3">Enable</option>' +
                    '<option value="4">Disable</option>' +
                    '</select>';
                var documents_drop_select = '<select name="documents_status_select" id="documents_status_select" class="select2 form-control">' +
                    '<option value="0">Incomplete</option>' +
                    '<option value="1">Pending for Approval</option>' +
                    '<option value="2">Approved</option>' +
                    '<option value="3">Rejected</option>' +
                    '</select>';
                var intl_drop_select = '<select name="intl_rate_status_select" id="intl_rate_status_select" class="select2 form-control">' +
                    '<option value="1">Approved</option>' +
                    '<option value="2">Requested</option>' +
                    '<option value="3">Rejected</option>' +
                    '</select>';
                var product_select = '<select name="product_select" id="product_select" class="select2 form-control"></select>';

                this.api().columns().every(function(column_id) {
                    var column = this;
                    var header = column.header();

                    if ($(header).is('.select') || $(header).is('.action')  || $(header).is('.serial_number') || $(header).is('.disable_remarks') || $(header).is('.rate_status') || $(header).is('.duplicate') || $(header).is('.international_rejected_reason')) {
                        $(td).appendTo($(search));
                    }else if($(header).is('.status')){
                        $(drop_select).appendTo($(search))
                            .on( 'change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            } ).wrap(td);
                    }else if($(header).is('.product_type')){
                        $(product_select).appendTo($(search))
                            .on( 'change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            } ).wrap(td);
                    }else if($(header).is('.documents_status')){
                        $(documents_drop_select).appendTo($(search))
                            .on( 'change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            } ).wrap(td);
                    }else if($(header).is('.international_rate_status')){
                        $(intl_drop_select).appendTo($(search))
                            .on( 'change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            } ).wrap(td);
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
                $("#documents_status_select").prepend('<option value="" selected></option>').select2({
                    placeholder: "Select a Status",
                    width:'100%',
                    containerCssClass: 'select-xs',
                    dropdownCssClass: 'form-control-sm p-0'
                });
                $("#intl_rate_status_select").prepend('<option value="" selected></option>').select2({
                    placeholder: "Select International Rate Status",
                    width:'100%',
                    containerCssClass: 'select-xs',
                    dropdownCssClass: 'form-control-sm p-0'
                });
                $("#status_select").prepend('<option value="" selected></option>').select2({
                    placeholder: "Select a Status",
                    width:'100%',
                    containerCssClass: 'select-xs',
                    dropdownCssClass: 'form-control-sm p-0'
                });
                var data1 = $.map({!! $products !!}, function (obj) {
                    obj.id = obj.id // replace pk with your identifier

                    return obj;
                });
                var data1 = $.map({!! $products !!}, function (obj) {
                    obj.text = obj.product_name; // replace name with the property used for the text

                    return obj;
                });

                $("#product_select").prepend('<option value="" selected></option>').select2({
                    data:data1,
                    placeholder: "Select Product",
                    width:'100%',
                    containerCssClass: 'select-xs',
                    dropdownCssClass: 'form-control-sm p-0'
                });
                this.api().table().columns.adjust();
            }
        });

        var hub_ids = [];

        $('#search_filter_btn').on('click',function () {
            table.rows().nodes().each(function(index) {
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
                        table.button('.bulk_segment_tagging').disable();
                        table.button('.set_commission').disable();
                        table.button('.approve_commission').disable();
                        table.button('.set_segment').disable();
                        table.button('.tag').disable();
                        hub_ids.splice(index, 1);
                    }
                }
            });
            table.draw();
        });
        $('body').on('change','.blacklist_reason',function() {
            $(this).val($(this).val().trim());
        });
        $('body').on('click','button.blacklist',function () {
            var id = $(this).parents('tr').attr('id');
            var status = $(this).attr('rel');
            swal({
                // title: 'Are You Sure?',
                text: 'Write a reason to blacklist this account!',
                content: {
                    element: "input",
                    attributes: {
                        placeholder: "Write a reason",
                        class: "form-control blacklist_reason",
                    },
                },
                buttons: {
                    cancel: {
                        text: 'No',
                        value: false,
                        visible: true,
                        closeModal: true,
                    },
                    confirm: {
                        text: 'Yes',
                        value: true,
                        visible: true,
                        closeModal: false
                    }
                },
                closeOnClickOutside: false,
                closeOnEsc: false,
                dangerMode: true
            }).then((value) => {
                    if (value) {
                        if (value === '') {
                            swal("You have not selected any reason!", {
                                icon: "warning",
                            });
                        } else {
                        if (id) {
                            $.ajax({
                                url: '{!! route('admin.accounts.status.block') !!}',
                                method: 'POST',
                                data: {
                                    'id': id,
                                    'reason': value,
                                    'status': status,
                                    '_token': '{{ csrf_token() }}'
                                }
                            }).done(function (data) {
                                swal.close();
                                if (data.status === 1) {
                                    table.draw('false');
                                    swal.close();
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

                            });
                        }
                    }
                    }else{
                        swal.close();
                    }

            });


        });
        $("#saletag").prepend('<option value="" selected></option>').select2({
            placeholder: "Select Sales Person",
            width:'100%',
            dropdownParent:$('#SalesTagModal')
        });
        $("#saletag1").prepend('<option value="" selected></option>').select2({
            placeholder: "Select Sales Person",
            width:'100%',
            dropdownParent:$('#SalesTagModal1')
        });
        $("#set_segment").prepend('<option value="" selected></option>').select2({
            placeholder: "Select Segment",
            width:'100%',
            dropdownParent:$('#SetSegment')
        });
        $("#poc").prepend('<option value="" selected></option>').select2({
            placeholder: "Select POC",
            width:'100%',
            dropdownParent:$('#SalesTierTypeTagModal')
        });
        $("#kam").prepend('<option value="" selected></option>').select2({
            placeholder: "Select KAM",
            width:'100%',
            dropdownParent:$('#SalesTierTypeTagModal')
        });
        $("#ref").prepend('<option value="" selected></option>').select2({
            placeholder: "Select REFERRAL",
            width:'100%',
            dropdownParent:$('#SalesTierTypeTagModal')
        });

        $('#SalesTagModal').on('shown.bs.modal',function (e) {
            var $invoker = $(e.relatedTarget);
            var shipper_id = $invoker.data('target-id');
            $('#shipper_id').val(shipper_id);
        });

        $('#salesTagSubmit').on('click',function () {
            var shipper = $('#shipper_id').val();
            var tag = parseInt($('#saletag').val());
            if(tag){
                $.ajax({
                    url: '{!! route('admin.accounts.tag.submit') !!}',
                    method: 'POST',
                    data: {
                        'admin_id': tag,
                        'shipper_id':shipper,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function(data) {
                        if(data.status){
                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        }
                        else {
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        $('#saletag').val('').trigger('change');
                        $('#SalesTagModal').modal('hide');
                        table.draw(true);
                    });
            }else{
                var error = "Sales Person Not Selected!";
                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
            }

        });
        $('body').on('click','button.userenable',function () {
            var status  = "enable";
            var id = $(this).parents('tr').attr('id');
            swal({
                title: 'Are You Sure?',
                text: 'Select Yes to enable this account!',
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
            }).then(function (confirm) {
                if(confirm){
                    if(id){
                        $.ajax({
                            url: '{!! route('admin.accounts.status.change') !!}',
                            method: 'POST',
                            data: {
                                'id':id,
                                'status':status,
                                '_token': '{{ csrf_token() }}'
                            }
                        }).done(function (data) {
                            if(data.status === 1){
                                table.draw('false');
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            }else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }

                        });
                    }
                }
            });

        });
        $('body').on('click','button.userdisable',function () {
            var status  = "disable";
            var id = $(this).parents('tr').attr('id');
            swal({
                title: 'Are You Sure?',
                text: 'Select Yes to Disable this account!',
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
            }).then(function (confirm) {
               if(confirm){
                   if(id){
                       $.ajax({
                           url: '{!! route('admin.accounts.status.change') !!}',
                           method: 'POST',
                           data: {
                               'id':id,
                               'status':status,
                               '_token': '{{ csrf_token() }}'
                           }
                       }).done(function (data) {
                           if(data.status == 1){
                               table.draw('false');
                               toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                           }else{
                               toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                           }

                       });
                   }
               }
            });

        });

        $('#datatable').on('click', 'button.warehousing_enable', function(){
            var id = $(this).parents('tr').attr('id');
            if(id){
               $.ajax({
                   url: '{!! route('admin.accounts.warehousing.active') !!}',
                   method: 'POST',
                   data: {
                       'id':id,
                       '_token': '{{ csrf_token() }}'
                   }
               }).done(function (data) {
                   if(data.status == 1){
                       table.draw('false');
                       toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                   }else{
                       toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                   }

               });
            }
        });

        $('#datatable').on('click', 'button.warehousing_disable', function(){
            var id = $(this).parents('tr').attr('id');
            if(id){
               $.ajax({
                   url: '{!! route('admin.accounts.warehousing.inactive') !!}',
                   method: 'POST',
                   data: {
                       'id':id,
                       '_token': '{{ csrf_token() }}'
                   }
               }).done(function (data) {
                   if(data.status == 1){
                       table.draw('false');
                       toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                   }else{
                       toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                   }

               });
            }
        });

        $('body').on('click','button.shipment_days_button',function () {
            var id = $(this).parents('tr').attr('id');
            var days = table.row($(this).parents('tr')).data().auto_shipment_cancellation_days;
            $('#shipper_id').val(id);
            $('#cancellation_days').val(days);
            $('#ShipmentCancellationDaysModal').modal('show');
        });

        $('#accountDisableDaysSubmit').on('click',function () {
            var shipper = $('#shipper_id').val();
            var days = parseInt($('#cancellation_days').val());
            if(days){
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to update auto disable days for this account!',
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
                }).then(function (confirm) {
                    if(confirm){
                        $.ajax({
                            url: '{!! route('admin.accounts.auto_shipment_cancel_days.submit') !!}',
                            method: 'POST',
                            data: {
                                'days': days,
                                'shipper_id':shipper,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function(data) {
                                if(data.status == 1){
                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                }
                                else {
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                                $('#ShipmentCancellationDaysModal').modal('hide');
                                table.draw(true);
                            });
                    }
                });
            }else{
                var error = "Days field is required!";
                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
            }

        });

        $('body').on('click', 'button.duplicate_modal',  function(){
            var id = $(this).parents('tr').attr('id');
            if(id){
                $.ajax({
                    url: '{!! route('admin.accounts.duplicate.info') !!}',
                    data: {
                        'shipper_id': id,
                    }
                })
                    .done(function(data) {
                        if(data.status){
                            $('#duplicate_modal').modal('show');
                            var html = '<table class="table table-bordered"><tr><td><strong>Phone</strong></td><td>'+ data.info.phone +'</td></tr><tr><td><strong>CNIC</strong></td><td>'+ data.info.cnic +'</td></tr><tr><td><strong>IBAN</strong></td><td>'+ data.info.iban +'</td></tr><tr><td><strong>Name</strong></td><td>'+ data.info.name +'</td></tr>';
                            $('#duplicate_modal .modal-body').html(html);
                        }

                    });
            }
        });
        var old_dates = [];

        $('#old_rate_date').prepend('<option value="" selected="selected"></option>').select2({
            placeholder:'Select Date',
            width:'100%',
            //allowClear:true
        });
        $('#corporate_rate_type').prepend('<option value="" selected="selected"></option>').select2({
            placeholder:'Change Rate Type',
            width:'100%',
            //allowClear:true
        });
        var redirect = '{!! url('/admin') !!}';

        $('#RateHistoryModal').on('hide.bs.modal', function (e) {
            $('#old_rate_date').find('option').remove();
            $('#old_rate_date').prepend('<option value="" selected="selected"></option>').trigger('change');
        });

        $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {


            var user_id = table.row( $(this).parents('tr') ).data().id;

            $('#user_id').val(user_id);

            if ($(this).hasClass('rates_history')) {

                $.ajax({
                    url: '{!! route('admin.view.user') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'user_id': user_id
                    }
                }).done(function(data){
                    if (data.status == 1) {
                        $.each(data.details,function(key,value){
                            var newOption = new Option(value, value, false, false);
                            $('#old_rate_date').append(newOption).trigger('change');
                        });
                        $('#RateHistoryModal').modal('show');

                        $('#RateHistoryModal #old_rate_date').bind('change', function () {
                            var date = $(this).val();
                            if (date != '') {

                                if(data.account_type == 1){

                                    var id = user_id;
                                    var url = redirect + '/accounts/'+ id + '/view/rates/' + date;
                                    window.location = url;
                                }
                                else if(data.account_type == 2){
                                    var id = user_id;
                                    var url = redirect + '/corporate/'+ id + '/view/rates/' + date;
                                    window.location = url;
                                }
                                else{
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                            }
                            return false;
                        });
                    }
                    else{
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }

                });
            }
            if($(this).hasClass('credit_limit')){
                $.ajax({
                    url: '{!! route('admin.international.rates.update.get_credit') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'user_id': user_id
                    }
                }).done(function(data){
                    $('#credit_user').text('');
                    $('#credit_user_limit_used').text('');
                    $('#credit_limit').val('');
                    if(data.status == 0){
                        $('#credit_shipper_id').val(user_id);
                        $('#CreditLimitModal').modal('show');
                        if(data.limit_set){
                            $('#credit_limit').val(data.credit_limit);
                            $('#credit_user_limit_used').text(data.limit_usage);
                        }
                        $('#credit_user').text(data.user);
                    }
                    else{
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                });
            }
        });


        $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
            //modalll
            var id = $(this).parents('tr').attr('id');
            
            if($(this).hasClass('add_fintech_charges')){
                if(id){
                    $("#userID").val(id);
                    var userID = id;
                    $.ajax({
                    type : 'POST',
                    url  :  "{!! route('admin.accounts.add_fintech_charges') !!}",
                    data : {userID:userID,'_token': '{{ csrf_token() }}'},
                    success:function(res){
                        if(res.status == '200'){
                          
                            $('#user_fintech_charges_checkbox').click();
                            $('#user_fintech_charges_checkbox').prop('checked',true);
                            $(".UserFintechCharges").html('');
                            $(".UserFintechCharges").append(`
                        <div class="form-group text-left">
                          
                            <input type="number" value="${res.data.fintech_charges}" id="user_fintech_charges_txtbox" class="form-control valid" placeholder="Fintech Charges *" aria-invalid="false">
                        </div>`);
                        }
                        else{
                            $('#user_fintech_charges_checkbox').click();
                            $('#user_fintech_charges_checkbox').prop('checked',false );
                            $(".UserFintechCharges").html('');
                        }
                    }
                });
                   
                    $('#AddFintechChargesModal').modal('show');
                }
            }
        });


        $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {

            var user_id = table.row( $(this).parents('tr') ).data().id;
            var rate_type_id = table.row( $(this).parents('tr') ).data().corporate_rate_type_id;


            if ($(this).hasClass('change_rate_type')) {

                $.ajax({
                    url: '{!! route('admin.corporate.default.rate_type') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'user_id': user_id,
                        'rate_type_id': rate_type_id
                    }
                }).done(function(data){
                   
                    if (data.status == 1) {
                       
                        $.each(data.rate_types,function(key,value){
                            var newOption = new Option(value.name, value.id, false, false);
                            $('#corporate_rate_type').append(newOption).trigger('change');
                        });
                       
                        $('#ChangeRateType').modal('show');

                        $('#ChangeRateType #corporate_rate_type').bind('change', function () {
                            var rate_type_id = $(this).val();
                            if (rate_type_id) {
                                var url = redirect + '/corporate/'+ user_id + '/add/rates/' + rate_type_id;
                                window.location = url;
                            }
                        });
                    }
                    else{
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }

                });
            }
        });

        $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));
              
                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.bulk_tagging').enable();
                    table.button('.bulk_segment_tagging').enable();
                    
                    table.button('.set_commission').enable();
                    table.button('.tag').enable();
                    table.button('.approve_commission').enable();
                    table.button('.territory_tag').enable();
                    table.button('.territory_retag').enable();

                }
                else {
                    table.button('.bulk_tagging').disable();
                    table.button('.bulk_segment_tagging').disable();
                    
                    table.button('.tag').disable();
                    table.button('.set_commission').disable();
                    table.button('.approve_commission').disable();
                    table.button('.territory_tag').disable();
                    table.button('.territory_retag').disable();
                }
        });
        $('#payment_cycle_select').prepend('<option value="" selected="selected"></option>').select2({
            width: '100%',
            placeholder: 'Select Payment Cycle'
        }).bind('change', function() {
            var id = parseInt($(this).val());
            if(id == 1){
                $('#payment_day_div').addClass('d-none');
            }else if(id == 2){
                $('#payment_day_div').removeClass('d-none');
                $('#payment_day').inputmask({
                    'alias': 'integer',
                    'allowMinus': false,
                    'allowPlus': false,
                    'rightAlign': false,
                    'min': 1,
                    'max': 5
                });
            }else if(id == 3){
                $('#payment_day_div').removeClass('d-none');
                $('#payment_day').inputmask({
                    'alias': 'integer',
                    'allowMinus': false,
                    'allowPlus': false,
                    'rightAlign': false,
                    'min': 1,
                    'max': 29
                });
            }
        });

        $( "#set_territory" ).validate({
            errorClass:"danger",
            normalizer: function(value) {
                return $.trim(value);
            },
            errorPlacement: function(error, element) {
                error.addClass('w-100','text-center').appendTo(element.parent('.form-group'));
            },
            submitHandler: function(form) {

                form.submit();

            }
        });
        $( "#set_segments" ).validate({
            errorClass:"danger",
            normalizer: function(value) {
                return $.trim(value);
            },
            errorPlacement: function(error, element) {
                error.addClass('w-100','text-center').appendTo(element.parent('.form-group'));
            },
            submitHandler: function(form) {

                form.submit();

            }
        });

        
        $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
            var id = $(this).parents('tr').attr('id');
            if($(this).hasClass('payment_cycle')){
                if(id){
                    $.ajax({
                        url: '{!! route('admin.accounts.payment_cycle.info') !!}',
                        data: {
                            'shipper_id': id,
                        }
                    }).done(function(data){
                        if(data.status == 0){
                            $('#payment_cycle_form #shipper_id').val(id);
                            $('#payment_cycle_select').val(data.details.payment_cycle_id).trigger('change');
                            if(data.details.payment_cycle_id != 1){
                                $('#payment_day').val(data.details.payment_day);
                            }else{
                                $('#payment_day_div').addClass('d-none');
                            }
                            $('#PaymentCycleModal').modal('show');
                        }else{
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    });
                }
            }
        });

        $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
            var id = $(this).parents('tr').attr('id');
            if($(this).hasClass('restrict_order_id')){
                if(id){
                    $.ajax({
                        url: '{!! route('admin.accounts.restrict_order_id.info') !!}',
                        method: 'POST',
                        data: {
                            'user_id': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                        .done(function(data) {
                            $('#restrict_order_id_checkbox').prop('checked',false);
                            if (data.status == 1) {
                                $('#restrict_order_id_checkbox').click();
                            }
                            $('#restrict_user_id').val(id);
                            $('#RestrictOrderIDModal').modal('show');


                        });
                }
            }

            if($(this).hasClass('auto_cancel_days_setting')){
                if(id){
                    let auto_shipment_cancellation_days = table.row( $(this).parents('tr') ).data().auto_shipment_cancellation_days;
                    $("#auto_cancelation_days_form #cancelation_days").val(auto_shipment_cancellation_days);
                    $("#auto_cancelation_days_form #user_id").val(id);
                    $("#AutoCancelationDaysModal").modal('show');
                }
            }
        });

        $('#restrict_order_id_form').validate({
            errorClass: 'danger',
            successClass: 'success',
            normalizer: function(value) {
                return $.trim(value);
            },
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
            submitHandler: function(form) {
                swal({
                    title: 'Please Wait!',
                    text: 'Order ID is being restricted!',
                    icon: 'info',
                    buttons: false,
                    closeOnClickOutside: false,
                    closeOnEsc: false
                });
                form.submit();
            }
        });

        $("#auto_cancelation_days_form #cancelation_days").inputmask({
            'alias': 'integer',
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            'min': 5,
            'max': 60
        });
        $('#auto_cancelation_days_form').validate({
            errorClass: 'danger',
            successClass: 'success',
            normalizer: function(value) {
                return $.trim(value);
            },
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
            submitHandler: function(form) {
                swal({
                    title: 'Please Wait!',
                    text: 'Auto Cancelation Days being updated!',
                    icon: 'info',
                    buttons: false,
                    closeOnClickOutside: false,
                    closeOnEsc: false
                });
                form.submit();
            }
        });
        $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
            var id = $(this).parents('tr').attr('id');
            if($(this).hasClass('remove_sales_tier')){
                if(id){
                    $.ajax({
                        url: '{!! route('admin.accounts.kam_poc_ref_tag.info') !!}',
                        data: {
                            'shipper_id': id,
                        }
                    }).done(function(data){
                        if(data.status == 1){
                            $('#remove_sales_tier_form #shipper_id').val(id);
                            var html = '<table class="table table-bordered">' +
                                '<tr>' +
                                '<td style="vertical-align: middle;"><strong>POC</strong></td><td style="vertical-align: middle;">'+ data.info.poc +'</td>';
                                if(data.info.poc !== '-') {
                                    html += '<td><input type="checkbox" class="sales_tier_checkbox" id="poc" name="poc" value="poc"><label for="poc"> Remove</label></td>';
                                }
                                html += '</tr>' +
                                '<tr>' +
                                '<td style="vertical-align: middle;"><strong>KAM</strong></td><td style="vertical-align: middle;">'+ data.info.kam +'</td>';
                                if(data.info.kam !== '-'){
                                    html += '<td><input type="checkbox" class="sales_tier_checkbox" id="kam" name="kam" value="kam"><label for="kam"> Remove</label></td>';
                                }
                                html += '</tr>' +
                                '<tr>' +
                                '<td style="vertical-align: middle;"><strong>REFERRAL</strong></td><td style="vertical-align: middle;">'+ data.info.ref +'</td>';
                                if(data.info.ref !== '-') {
                                    html += '<td><input type="checkbox" class="sales_tier_checkbox" id="ref" name="ref" value="ref"><label for="ref"> Remove</label></td>';
                                }
                                html += '</tr>' +
                                '</table>';
                            $('#RemoveSalesTierTaggingModal .modal-body').html(html);

                            $('#remove_sales_tier_form .sales_tier_checkbox').each(function() {
                                var checkbox = $(this);
                                var label = checkbox.next();
                                var text = label.text();

                                label.remove();

                                checkbox.iCheck({
                                    checkboxClass: 'icheckbox_line pt-1 pb-1',
                                    checkedClass: 'checked bg-danger',
                                    uncheckedClass: 'bg-secondary',
                                    insert: '<div class="icheck_line-icon"></div>' + text
                                });
                            });
                            $('#RemoveSalesTierTaggingModal').modal('show');
                        }else{
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    });
                }
            }
        });

        $('#payment_cycle_form').validate({
            errorClass: 'danger',
            successClass: 'success',
            normalizer: function(value) {
                return $.trim(value);
            },
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
            submitHandler: function(form) {
                    swal({
                        title: 'Please Wait!',
                        text: 'Payment Cycle is being Updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });
                    form.submit();
            }
        });
        $('#SalesTierTypeTagModal').on('hide.bs.modal', function (e) {
            $('#SalesTierTypeTagModal #poc').val('').trigger('change');
            $('#SalesTierTypeTagModal #kam').val('').trigger('change');
            $('#SalesTierTypeTagModal #ref').val('').trigger('change');
        });

        $('.close').on('click',function(){
               $('#territory').val('').trigger('change');
        });

        $('#ChangeRateType').on('hide.bs.modal', function (e) {
            $('#corporate_rate_type').find('option').remove();
            $('#corporate_rate_type').prepend('<option value="" selected="selected"></option>').trigger('change');
        });

        $('#set_credit_limit_form').validate({
            errorClass: 'danger',
            successClass: 'success',
            normalizer: function(value) {
                return $.trim(value);
            },
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
            submitHandler: function(form) {
                // var form = this;
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to Update User Credit Limit!',
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
                }).then(function (confirm) {
                    if (confirm) {
                        var shipper_id = $('#credit_shipper_id').val();
                        var limit = $('#credit_limit').val();
                        $.ajax({
                            url: '{!! route('admin.international.rates.update.credit') !!}',
                            method: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'user_id': shipper_id,
                                'limit':limit
                            }
                        }).done(function(data){
                            $('#credit_user').text('');
                            $('#credit_user_limit_used').text('');
                            $('#credit_limit').val('');
                            $('#credit_shipper_id').val('');
                            $('#CreditLimitModal').modal('hide');
                            if(data.status == 0){
                                toastr.success(data.success, 'Success!', {
                                    positionClass: 'toast-bottom-center',
                                    containerId: 'toast-bottom-center'
                                });
                            }
                            else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        });
                    }
                });
            }
        });

        $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {

            var user_id = table.row( $(this).parents('tr') ).data().id;

            if ($(this).hasClass('view_invoice_log')) {

                $.ajax({
                    url: '{!! route('admin.accounts.packaging.invoice.log') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'user_id': user_id,
                    }
                }).done(function(data){

                    if (data.status == 0) {


                        var html = '<table class="table table-bordered">' +
                                    '<thead><tr><td><strong>S.No</strong></td><td><strong>Admin</strong></td><td><strong>Status</strong></td><td><strong>Time</strong></td></tr></thead><tbody>';

                        $.each(data.details, function (index,value) {
                            console.log(value,value.admin);
                                var serial = index + 1;
                                var status = '';
                                if(value['status'] == 1){
                                    status = 'On';
                                }
                                else{
                                    status = 'Off';
                                }

                                html += '<tr><td>'+serial +'</td><td>' + value['admin'] + '</td>' +
                                         '<td>'+ status + '</td>' +
                                    '<td>' + value['time']+ '</td></tr>';
                            serial++;
                        });

                         html +=  '</tbody></table>';

                        $('#CorporateInvoiceLogModal .modal-body').html(html);

                        $('#CorporateInvoiceLogModal').modal('show');

                    }
                    else{
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }

                });
            }
        });
    });

</script>

@endsection

