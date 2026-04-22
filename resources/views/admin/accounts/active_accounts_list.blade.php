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
                                        <select name="search_shipper[]" id="search_shipper" class="form-control select2" multiple>
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
                                        <th class="border-primary border-darken-1">Lead ID</th>
                                        <th class="border-primary border-darken-1">Account ID</th>
                                        <th class="border-primary border-darken-1">Account Type</th>
                                        <th class="border-primary border-darken-1">Company Name</th>
                                        <th class="border-primary border-darken-1">Expected Shipments</th>
                                        <th class="border-primary border-darken-1">FAF Percentage</th>
                                        <th class="border-primary border-darken-1">Contact Person</th>
                                        {{-- <th class="border-primary border-darken-1">Address</th> --}}
                                        <th class="border-primary border-darken-1">Zone</th>
                                        <th class="border-primary border-darken-1">City</th>
                                        <th class="border-primary border-darken-1">Territory</th>
                                        <th class="border-primary border-darken-1">Product Type</th>
                                        <th class="border-primary border-darken-1">User Type</th>
                                        <th class="border-primary border-darken-1">Status</th>
                                        <th class="border-primary border-darken-1">Sales Person Tagged</th>
                                        <th class="border-primary border-darken-1">POC Tagged</th>
                                        <th class="border-primary border-darken-1">KAM Tagged</th>
                                        <th class="border-primary border-darken-1">REF Tagged</th>
                                        {{-- <th class="border-primary border-darken-1">ESO Tagged</th> --}}
                                        <th class="border-primary border-darken-1">SMS charges per shipment</th>
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
                                        <th class="border-primary border-darken-1">Account Disable Reason</th>
                                        {{-- <th class="border-primary border-darken-1">Account Disable Count</th> --}}
                                        <th class="border-primary border-darken-1">Account Disable Days</th>
                                        <th class="border-primary border-darken-1">Document Uploaded At</th>
                                        <th class="border-primary border-darken-1">Documents Approved By</th>
                                        <th class="border-primary border-darken-1">Document Approved At</th>
                                        <th class="border-primary border-darken-1">Documents Rejected By</th>
                                        <th class="border-primary border-darken-1">Documents Rejected At</th>
                                        <th class="border-primary border-darken-1">Documents Status</th>
                                        <th class="border-primary border-darken-1">Documents Rejection Reason</th>
                                        <th class="border-primary border-darken-1">FAF Charges Applied</th>
                                        <th class="border-primary border-darken-1">Duplicate</th>
                                        <th class="border-primary border-darken-1">Intl Rate Status</th>
                                        <th class="border-primary border-darken-1">Intl Rate Status Remarks</th>
                                        <th class="border-primary border-darken-1">Segment</th>
                                        <th class="border-primary border-darken-1">Sub Category Segment</th>
                                        {{-- <th class="border-primary border-darken-1">Referral Code</th> --}}
                                        <th class="border-primary border-darken-1">Payment Cycle</th>
                                        <th class="border-primary border-darken-1">Payment Cycle Days</th>
                                        
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
        <div class="modal-dialog modal-lg" role="document">
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
                        <select name="payment_cycles" id="payment_cycles" class="form-control select2" data-rule-required="true" data-msg-required="Payment Cycle is required">
                            @foreach($payment_cycles as $pc)
                                <option value="{{ $pc->id }}" > {{ $pc->name }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div id="checkboxContainer" class="d-none">
                    </div>
                
                    <span class="text-danger d-none" id="payment_cycle_msg"></span>
                    <span class="text-danger d-none" id="msg_payment">Maximum Selected</span>

                    <div>
                        <input type="hidden" id="fortnite_val" name="fortnite" value="">
                        <label class="d-none" id="label">Day 1</label>
                        <select name="fornite" id="fornite"
                            class="select2 form-control d-none"
                            style="width: 100%">

                            <option value="none">Please Select Day</option>
                            @for ($i = 1; $i < 14; $i++)
                                <option value="{{ $i }}">
                                    {{ $i . ' day' }}</option>
                            @endfor
                        </select>
                    </div>
                   

                    <div class="mt-2">
                        <label class="d-none" id="label_2">Day 2</label>
                        <select name="fornite_2" id="fornite_2"
                            class="select2 form-control d-none"
                            style="width: 100%">
                        </select>
                    </div>

                    <div>
                        <select name="monthly" id="monthly"
                            class="select2 form-control d-none"
                            style="width: 100%">
                            <option value="nonem">Please Select Day</option>

                            @for ($i = 1; $i < 29; $i++)
                                <option value="{{ $i }}">
                                    {{ $i . ' day' }}</option>
                            @endfor
                        </select>
                    </div>
                    <div id="msg_limit_days" class="d-none text-danger">
                    </div>
                    <input type="hidden" name="selected_days" value="" id="selected_days">

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
                        <div class="col-12 d-flex">

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

    <div class="modal fade text-left" id="CorporateDeliveredInvoiceLogModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="CorporateDeliveredInvoiceLogModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Invoicing On Delivered Toggle Log</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                   
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade text-left" id="faf_charges_modal" data-backdrop="static" role="dialog" aria-labelledby=""
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">FaF Charges</h4>
                </div>
                <form id="faf_charges_form" class="form" novalidate="novalidate" method="post" action="{{ route('admin.accounts.faf_charges.submit') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="col text-center">
                            <label class="font-medium-2 font-weight-bold block">Apply FAF Charges</label>
                            <div class="form-group">
                                <input type="hidden" name="user_id" id="faf_charges_user_id">
                                <label for="" class="font-medium-2 text-bold-600 mr-1">No</label>
                                <input type="checkbox" name="faf_charges_checkbox" id="faf_charges_checkbox" class="switchery faf_charges_checkbox" data-size="sm" data-switchery="true">
                                <label for="" class="font-medium-2 text-bold-600 ml-1">Yes</label>

                                <input type="text" class="form-control" placeholder="FAF Percentage" name="faf_percentage" id="faf_percentage" data-rule-required="true" data-msg-required="Required">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="faf_charges_submit">Submit</button>
                        <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="exp_shipment_modal" data-backdrop="static" role="dialog" aria-labelledby=""
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Expected Shipment Chargeable Percentage</h4>
                </div>
                            
                <form id="exp_shipment_form" class="form" novalidate="novalidate" method="post" action="{{ route('admin.accounts.exp_shipment_percentage.submit') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="col text-center">
                            <label class="font-medium-2 font-weight-bold block">Add Percentage</label>
                            <div class="form-group">
                                <input type="hidden" name="user_id" id="exp_shipment_user_id">
                                
                                <input type="text" class="form-control" placeholder="Expected Shipment Chargeable Percentage" name="exp_shipment_percentage" id="exp_shipment_percentage" data-rule-required="true" data-msg-required="Required">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="faf_charges_submit">Submit</button>
                        <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="SwitchCorporate" data-backdrop="static" role="dialog" aria-labelledby="SwitchCorporate_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="corporate_rate_type_title">Switch To Corporate Invoicing Account</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div class="form-group">
                        <input type="hidden" id="corporate_rate_type_shipper_id">
                        <select name="corporate_rate_type_id" id="corporate_rate_type_select" class="form-control select2">
                            @foreach($corporate_rate_types as $rate_type)
                                <option value="{{ $rate_type->id }}" > {{ $rate_type->name }} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="switch_corporate_submit_btn" class="btn btn-success">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="SwitchReimbursement" data-backdrop="static" role="dialog" aria-labelledby="SwitchReimbursement_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="reimbursement_rate_type_title">Switch To Reimbursement Account</h4>
                    <input type="hidden" id="reimbursement_rate_type_shipper_id">

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body">
                    <p class="mb-0 text-danger">
                        Are you sure? This will permanently change this account to a reimbursement account.
                    </p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" id="switch_reimbursement_submit_btn" class="btn btn-success">Submit</button>
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
                    <form id="Save_fintech_charges" method="POST" action="{{route('admin.accounts.add_fintech_charges')}}" enctype="multipart/form-data">
                       @csrf
                        <div class="col text-center">
                            <label class="font-medium-2 font-weight-bold block">Is Shipper Paying Fintech Charges ?</label>
                            <div class="form-group">
                                <input type="hidden" name="userID" id="userID">
                                <label for="" class="font-medium-2 text-bold-600 mr-1">No</label>
                                <input type="checkbox" onchange="checkboxStatus()" id="user_fintech_charges_checkbox" class="switchery restrict_order_id_checkbox" data-size="sm" data-switchery="true">
                                <label for="" class="font-medium-2 text-bold-600 ml-1">Yes</label>
                            </div>

                            <div class="row justify-content-center UserFintechCharges"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit " class="btn btn-success"  onclick="" >Submit</button>
                                <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
               </div>
               
          
       </div>
   </div>
</div>

{{-- End Add Fintech Charges Modal --}}

{{-- Commission Moal --}}
<div class="modal fade text-left" id="SalesTierTypeTagModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="SalesTierTypeTagModal" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 100%;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Rates</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="ratesAdditionForm" class="card-body card-dashboard" action="#" method="post" novalidate>
                @csrf
                <input type="hidden" name="datatable_check" id="datatable_check">

                <div class="modal-body">
                    <div class="col text-center">
                        <h1 id="shipper_ids_msg"></h1>
                        <div class="row justify-content-center mt-2" id="commission_div">
                            <div class="form-group row d-none">
                                <label class="col-md-4 label-control" for="commission">Total Commission</label>
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Total Commission"
                                            id="commission_max" name="commission_max" value="{{$commission_percentage}}" readonly>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div id="add_user_commission_form" class="form mb-1 justify-content-center">
                                    <div class="row justify-content-center">
                                        <div class="col-2 form-group">
                                            <select name="sales_tier" class="select2" id="sales_tier_select">
                                                @foreach($sales_tiers as $tier)
                                                    <option value="{{ $tier->id }}" type="{{$tier->tier_type}}"
                                                        sales="{{$tier->sales_status}}">{{ $tier->tier_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-3 form-group">
                                            <input type="text" id="external_person_name" name="external_person_name"
                                                class="form-control" placeholder="External Tier Person Name" disabled>
                                        </div>
                                        <div class="col-2 form-group">
                                            <select name="user" class="select2" id="user_select" disabled>
                                            </select>
                                        </div>
                                        <div class="col-3 form-group">
                                            <div class="input-group form-group">
                                                <input type="text" id="user_commission" class="form-control commission"
                                                    placeholder="User Commission" name="user_commission">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-1 form-group">
                                            <button type="button" class="btn btn-primary" id="commission_add_button">Add</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <table class="table table-bordered datatable_rate" id="datatable_rate" style="z-index: 3;">
                                    <thead>
                                        <tr role="row" class="bg-primary white">
                                            <th class="border-primary border-darken-1">S. No.</th>
                                            <th class="border-primary border-darken-1">User Name</th>
                                            <th class="border-primary border-darken-1">Tier</th>
                                            <th class="border-primary border-darken-1">Commission Percentage</th>
                                            <th class="border-primary border-darken-1"></th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <input type="hidden" value="0" name="total_commission" id="total_commission">
                                        <tr>
                                            <th colspan="3" style="text-align:right" rowspan="1">Total Commission:</th>
                                            <th rowspan="1" colspan="2"><span id="total_commission_value">0</span>%</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" style="margin-right:680px;">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- End --}}

{{-- Add intercept shipper modal --}}
<div class="modal fade text-left" id="AddShipperExcludeInterceptType" data-backdrop="static" role="dialog" aria-labelledby="modalTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalTitle">Intercept Request Exclude Shippers</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="interceptModalCloseBtn_1">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="add_shipper_exclude_intercept_type" method="POST" action="{{ route('admin.accounts.store_shipper_exclude') }}">
                    @csrf
                    <input type="hidden" name="user_id" id="user_id">
                    <div class="text-center">
                        <h4 id="shipper_name"></h4>
                    </div>
                    <div class="container">
                        <div class="row">
                            <div class="col-4 mt-4">
                                <input type="checkbox" name="exclude_shipper" id="exclude_shipper">
                                <label for="exclude_shipper">Exclude Shipper</label>
                            </div>

                            <div class="col-4 mt-4">
                                <input type="checkbox" name="different_consignee" id="different_consignee">
                                <label for="different_consignee">Disable Different Consignee</label>
                            </div>

                            <div class="col-4 mt-4">
                                <input type="checkbox" name="same_consignee" id="same_consignee">
                                <label for="same_consignee">Disable Same Consignee</label>
                            </div>
                        </div>
                        <div class="text-center mt-4">
                            <input type="submit" value="Submit" class="btn btn-success">
                            <button type="button" class="btn btn-primary" id="interceptModalCloseBtn_2" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
{{-- End intercept shipper modal --}}
<div class="modal fade text-left" id="BlockDisableReasonModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="BlockDisableReasonModal"
aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="BlockDisableReasonModalHeading"></h4>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control mb-1" placeholder="Enter Remarks" name="block_disable_remarks" id="block_disable_remarks">
                <div class="text-danger d-none blocked_remarks" style="margin-top: -12px; margin-bottom: 15px;" id="blocked_remarks">Remarks Are Required</div>
                <select name="block_disable_reason" id="block_disable_reason" class="form-control select2">
                    @foreach($block_disable_reasons as $block_disable_reason)
                        <option value="{{ $block_disable_reason->id }}" > {{ $block_disable_reason->name }} </option>
                    @endforeach
                </select>
                <span class="text-danger d-none blocked_reasons">Reasons Are Required</span>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="BlockDisableReasonSubmit">Submit</button>
                <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="AccountTaggingHistoryModal" tabindex="-1" role="dialog" aria-labelledby="AccountTaggingHistoryLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title">Account Tagging History</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
          <table class="table table-bordered table-striped" id="taggingHistoryTable">
              <thead>
                  <tr>
                      <th>#</th>
                      <th>Changed By</th>
                      <th>Previous Sales/KAM</th>
                      <th>New Sales/KAM</th>
                      <th>Type</th>
                      <th>Changed At</th>
                  </tr>
              </thead>
              <tbody>
              </tbody>
          </table>
      </div>
    </div>
  </div>
</div>


@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">

<style>
     #checkboxContainer label {
            display: inline-block;
            padding: 10px;
            margin: 5px;
            background-color: #3498db;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Style the checkbox to be hidden */
        #checkboxContainer input[type="checkbox"] {
            display: none;
        }

        /* Style the label when the checkbox is checked */
        #checkboxContainer input[type="checkbox"]:checked+label {
            background-color: #56e73c; }

        /* Style the actions dropdown due to increase in action buttons */
        #datatable > tbody > tr > td:last-child > div.btn-group > div.dropdown-menu.dropdown-menu-sm.accounts {
            overflow-y: scroll;
            height: 300px;
        }
</style>
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

function checkboxStatus() {
        if (document.getElementById('user_fintech_charges_checkbox').checked) {
            $(".UserFintechCharges").append(`
                <div class="form-group text-left">
                    <div class="input-group">
                        <input type="text"  onkeydown="inputValidate()" id="user_fintech_charges_txtbox" class="form-control input-filtered fuel_factor" placeholder ="Fintech Charges*" aria-invalid="true"  data-rule-required="true" data-msg-required="Fintech Charges is Required">
                        <div class="input-group-append">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>`);
        } else {
            $(".UserFintechCharges").html('');
        }
    }

    $("#Save_fintech_charges").validate({
        ignore: ":not(:visible),:disabled",
        errorClass: 'danger',
        successClass: 'success',
        errorPlacement: function(error, element) {
            error.addClass('w-100').appendTo(element.parents('.form-group'));
        },
        submitHandler: function(form) {
            swal({
                title: 'Are You Sure?',
                text: 'Select Yes to update Shipper Fintech Charges!',
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
                    var fintechCharges = $("#user_fintech_charges_txtbox").val();
                    var userID = $("#userID").val();
                    if (document.getElementById('user_fintech_charges_checkbox').checked) {
                        var checkboxval = 'true';
                    } else {
                        var checkboxval = 'false';
                    }
                    $.ajax({
                        type: 'POST',
                        url: "{!! route('admin.accounts.add_fintech_charges') !!}",
                        data: {
                            fintechCharges: fintechCharges,
                            userID: userID,
                            checkboxval: checkboxval,
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            if (res.status == '200') {
                                toastr.success(res.message, 'Success!', {
                                    positionClass: 'toast-bottom-center',
                                    containerId: 'toast-bottom-center',
                                });
                                $("#AddFintechChargesModal").modal('hide');
                            } 
                            
                            if (res.status == '401') {
                                toastr.error(res.message, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            } 
                        }
                    });
                }
            });
        }
    });

    $(document).ready(function() {
        $("#user_select").prepend('<option value="" selected></option>').select2({
            placeholder: "Select Users",
            width:'100%',
            dropdownParent:$('#SalesTierTypeTagModal')
        });
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
									// console.log(index);	
									// console.log(sub_segment);	
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
        $('#search_shipper').select2({
            width:'100%',
            placeholder:"Select Shipper",
            allowClear:true,
            multiple: true,
            minimumInputLength: 2,
            ajax: {
                dataType: 'json',
                url:  '{!! route('admin.accounts.shipper_names.dropdown',['type'=>'active']) !!}',
                    data: function (params) {
                        return {
                            search: params.term,
                        }
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    },
                delay: 700,
            }
         });

         $('#block_disable_reason').prepend('<option value="" selected></option>').select2({
            width: '100%',
            placeholder: "Select Reasons",
            allowClear: true,
            dropdownParent: $('#BlockDisableReasonModal')
        }).on('change', function() {
            $('.blocked_reasons').addClass('d-none');
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
                        head.push('Lead ID');
                        head.push('Account ID');
                        head.push('Account Type');
                        head.push('Company Name');
                        head.push('Expected Shipments');
                        head.push('FAF Percentage');
                        head.push('Contact Person');
                        //head.push('Address');
                        head.push('Zone');
                        head.push('City');
                        head.push('Territory');
                        head.push('Product Type');
                        head.push('User Type');
                        head.push('Status');
                        head.push('Sales Person Tagged');
                        head.push('POC Tagged');
                        head.push('KAM Tagged');
                        head.push('REF Tagged');
                        // head.push('ESO Tagged');
                        head.push('SMS charges per shipment');
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
                        head.push('Account Disable Reason');
                        //head.push('Account Disable Count');
                        head.push('Account Disable Day(s)');
                        head.push('Documents Uploaded At');
                        head.push('Documents Approved By');
                        head.push('Documents Approved At');
                        head.push('Documents Rejected By');
                        head.push('Documents Rejected At');
                        head.push('Documents Status');
                        head.push('Documents Rejection Reason');
                        head.push('FAF Charges Applied');
                        head.push('Duplicate');
                        head.push('Intl Rates Status');
                        head.push('Intl Rates Status Remarks');
                        head.push('Segment');
                        head.push('Sub Category Segment');
                        //head.push('Referral Code');
                        head.push('Payment Cycle');
                        head.push('Payment Cycle Days');
                        
                        $.each(result.data, function(index, values) {
                            row = [];


                            row.push(index + 1);
                            row.push(values.lead_id);
                            row.push(values.id);
                            row.push(values.account_type);
                            // row.push(values.name);

                            // Decode HTML entities for comapany name
                            row.push((() => {
                                const tempElement = document.createElement('textarea');
                                tempElement.innerHTML = values.name;
                                return tempElement.value;
                            })());
                            row.push(values.average_shipments);
                            row.push(values.faf_percentage);
                            row.push(values.poc);
                            //row.push(values.address);
                            row.push(values.zone);
                            row.push(values.city);
                            row.push(values.territory);
                            row.push(values.product_type);
                            row.push(values.wallet_shippers)
                            row.push(values.status);
                            row.push(values.admin_tag_id);
                            row.push(values.tagged_poc);
                            row.push(values.kam);
                            row.push(values.ref);
                            // row.push(values.eso);
                            row.push(values.sms_charges);
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
                            row.push(values.disable_reason);
                            row.push(values.reason);
                            //row.push(values.status_count);
                            row.push(values.days_to_disable);
                            row.push(values.documents_uploaded_at);
                            row.push(values.documents_approved_by);
                            row.push(values.documents_approved_at);
                            row.push(values.documents_rejected_by);
                            row.push(values.documents_rejected_at);
                            row.push(values.documents_status);
                            row.push(values.documents_rejection_reason);
                            row.push(values.fc_status);
                            row.push(values.duplication);
                            row.push(values.international_rate_status);
                            row.push(values.international_rejected_reason);    
                            row.push(values.segment);
                            row.push(values.sub_segment);
                            //row.push(values.referral_name);
                            row.push(values.payment_cycle);
                            row.push(values.payment_cycle_days);
                            
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


                    @if (session('role_id') == 1 || in_array(365, session('permissions')))
                    {
                            text: 'Payment Cycle',
                            className: 'btn btn-primary payment_cycle',
                            enabled:false,
                            action: function (e, dt, node, config) {
                                if(selected_rows != ''){
                           $('#payment_cycle_form [name="shipper_id"]').val(selected_rows);
                           $('#PaymentCycleModal').modal('show');

                       }else{
                           var error = "Atleast Select One Shipper";
                           toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                       }
        
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
                            if(selected_rows_2 != ''){
                                $('#SalesTierTypeTagModal').modal('show');
                                var route = '{!! route('admin.accounts.add_rate_commission_corporate_reimb', ':shippers') !!}';
                                route = route.replace(':shippers', encodeURIComponent(selected_rows_2));
                                $("#SalesTierTypeTagModal #ratesAdditionForm").attr('action', route);

                                $('#shipper_ids_msg').text("Selected Shipper:"+ selected_rows_2)

                            }else{
                                var error = "Rates Are Rejected";
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
                                        table.button('.payment_cycle').enable();

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
            order: [[17, 'desc']],
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
                {data: 'lead_id_link', name: 'users.lead_id', class:'align-middle lead_id_link'},
                {data: 'id_padded', name: 'users.id', class: 'align-middle account_id'},
                {data: 'account_type', name: 'at.name', class: 'align-middle account_type'},
                {data: 'name', name: 'name', class: 'align-middle company_name'},
                {data: 'average_shipments', name: 'users.average_shipments', class: 'align-middle average_shipments'},
                {data: 'percentage', name: 'faf_charges.percentage', class: 'align-middle percentage'},

                {data: 'poc', name: 'poc', class: 'align-middle contact_person'},
                //{data: 'address', name: 'users.address', class: 'align-middle address'},
                {data: 'zone', name: 'z.name', class: 'align-middle zone'},
                {data: 'city', name: 'cities.name', class: 'align-middle city'},
                {data: 'territory', name: 't.name', class: 'align-middle territory'},
                {data: 'product_type', name: 'product_type', class: 'align-middle product_type'},
                {data: 'wallet_shippers', name: 'wallet_shippers', class: 'align-middle wallet_shippers'},
                {data: 'status', name: 'status', class: 'align-middle status'},
                {data: 'admin_tag_id', name: 'ad.name', class: 'align-middle admin_tag_id'},
                {data: 'tagged_poc', name: 'poc.name', class: 'align-middle tagged_poc'},
                {data: 'kam', name: 'k.name', class: 'align-middle kam'},
                {data: 'ref', name: 'r.name', class: 'align-middle ref'},
                {data: 'sms_charges', name: 'users.sms_charges', class: 'align-middle sms_charges'},
                // {data: 'eso', name: 'e.name', class: 'align-middle eso'},
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
                {data: 'disable_reason', name: 'users.disable_reason', class: 'align-middle disable_reason', orderable: false, searchable: false},
                {data: 'reason', name: 'bdru.name', class: 'align-middle reason'},
                //{data: 'status_count', name: 'ucs.status_count', class: 'align-middle status_count'},
                {data: 'days_to_disable', name: 'days_to_disable', class: 'align-middle days_to_disable'},
                {data: 'documents_uploaded_at', name: 'uda.uploaded_at', class: 'align-middle documents_uploaded_at', searchable: false},
                {data: 'documents_approved_by', name: 'dab.name', class: 'align-middle documents_approved_by', searchable: false},
                {data: 'documents_approved_at', name: 'uda.approved_at', class: 'align-middle documents_approved_at', searchable: false},
                {data: 'documents_rejected_by', name: 'drb.name', class: 'align-middle documents_rejected_by', searchable: false},
                {data: 'documents_rejected_at', name: 'uda.rejected_at', class: 'align-middle documents_rejected_at', searchable: false},
                {data: 'documents_status', name: 'users.documents_status', class: 'align-middle documents_status'},
                {data: 'documents_rejection_reason', name: 'users.documents_status_reason', class: 'align-middle documents_rejection_reason'},
                {data: 'fc_status', name: 'faf_charges.status', class: 'align-middle fc_status'},
                {data: 'duplication', name: 'duplication', class: 'align-middle duplicate', orderable: false, searchable: false},
                {data: 'international_rate_status', name: 'iui.status', class: 'align-middle international_rate_status'},
                {data: 'international_rejected_reason', name: 'international_rejected_reason', class: 'align-middle international_rejected_reason', orderable: false, searchable: false},
                {data: 'segment', name: 'seg.name', class: 'align-middle segment'},
                {data: 'sub_segment', name: 'seg_sub.name', class: 'align-middle sub_segment'},
                //{data: 'referral_name', name: 'ref.name', class: 'align-middle referral_name'},
                {data: 'payment_cycle', name: 'pc.id', class: 'align-middle payment_cycle'},
                {data: 'payment_cycle_days', name: 'users.payment_cycle_days', class: 'align-middle payment_cycle_days'},
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
                    '<option value="6">Booking Paused</option>' +
                    '</select>';

                var wallet_shippers = '<select name="wallet_shippers" id="wallet_shippers" class="select2 form-control">' +
                    '<option value="1">Fintech</option>' +
                    '<option value="2">Normal</option>' +
                    '<option value="3">All Type</option>' +
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
                var payment_cycle_select =
                        '<select name="payment_cycle_select" id="payment_cycle_select" class="select2 form-control">' +
                        '<option value="1">Daily</option>' +
                        '<option value="2">Weekly</option>' +
                        '<option value="3">Monthly</option>' +
                        '<option value="4">Twice A Week</option>' +
                        '<option value="5">Thrice A Week</option>' +
                        '<option value="6">Fortnite</option>' +

                        '</select>';

                var fc_status_drop_select = '<select name="fc_status_select" id="fc_status_select" class="select2 form-control">' +
                    '<option value="1">Yes</option>' +
                    '<option value="0">No</option>' +
                    '</select>';


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
                    }
                    else if($(header).is('.wallet_shippers')){
                        $(wallet_shippers).appendTo($(search))
                            .on( 'change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            } ).wrap(td);
                    }
                    else if($(header).is('.product_type')){
                        $(product_select).appendTo($(search))
                            .on( 'change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            } ).wrap(td);
                    }else if($(header).is('.documents_status')){
                        $(documents_drop_select).appendTo($(search))
                            .on( 'change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            } ).wrap(td);
                    }else if($(header).is('.fc_status')){
                        $(fc_status_drop_select).appendTo($(search))
                            .on( 'change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            } ).wrap(td);
                    }else if($(header).is('.international_rate_status')){
                        $(intl_drop_select).appendTo($(search))
                            .on( 'change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            } ).wrap(td);
                    } else if ($(header).is('.payment_cycle')) {
                            $(payment_cycle_select).appendTo($(search))
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
                $("#payment_cycle_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Cycle",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0',
                        allowClear:true,

                    
                });
                $("#documents_status_select").prepend('<option value="" selected></option>').select2({
                    placeholder: "Select a Status",
                    width:'100%',
                    containerCssClass: 'select-xs',
                    dropdownCssClass: 'form-control-sm p-0'
                });

                
                $("#fc_status_select").prepend('<option value="" selected></option>').select2({
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
                $("#wallet_shippers").prepend('<option value="" selected></option>').select2({
                    placeholder: "Select User Type",
                    width:'100%',
                    containerCssClass: 'select-xs',
                    dropdownCssClass: 'form-control-sm p-0'
                });
                $("#sms_charges_select").prepend('<option value="" selected></option>').select2({
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

        var block_user_id;
        var block_user_status;

        var disable_user_id;
        var disable_user_status;

        
        $('body').on('click', 'button.blacklist', function () {
            $('#BlockDisableReasonModal').modal('show');
            $('#BlockDisableReasonModal #BlockDisableReasonModalHeading').text('Block Reason Remarks');
            block_user_id = $(this).data('id');
            block_user_status = $(this).attr('rel');
            disable_user_id = null; 
        });

        $('body').on('click', 'button.userdisable', function () {
            $('#BlockDisableReasonModal').modal('show');
            $('#BlockDisableReasonModal #BlockDisableReasonModalHeading').text('Disable Reason Remarks');
            disable_user_id = $(this).data('id');
            disable_user_status = 'disable';
            block_user_id = null; 
        });

        
        $('#BlockDisableReasonSubmit').click(function () {
            var remarks = $('#BlockDisableReasonModal').find('.modal-body input[name="block_disable_remarks"]').val();
            var reasons = $('#BlockDisableReasonModal').find('.modal-body select[name="block_disable_reason"]').val();
            if(remarks == ''){
                $('.blocked_remarks').removeClass('d-none');
            }else if (reasons == ''){
                $('.blocked_reasons').removeClass('d-none');
            }else{
                $.ajax({
                url:  block_user_id ? '{!! route('admin.accounts.status.block') !!}' : '{!! route('admin.accounts.status.change') !!}',
                method: 'POST',
                data: {
                    'id': block_user_id ? block_user_id : disable_user_id,
                    'reason': reasons,
                    'remarks': remarks,
                    'status' : block_user_id ? block_user_status : 'disable',
                    '_token': '{{ csrf_token() }}'
                }
            }).done(function (data) {
                if (data.status === 1) {
                    toastr.success(data.success, 'Success!', {
                        positionClass: 'toast-bottom-center',
                        containerId: 'toast-bottom-center'
                    });
                    // $('#blocked_remarks').addClass('d-none');
                    // $('#blocked_reasons').addClass('d-none');
                    $('#BlockDisableReasonModal').modal('hide');
                    $('#BlockDisableReasonModal').find('.modal-body input[name="block_disable_remarks"]').val('');            
                    $('#BlockDisableReasonModal').find('.modal-body select[name="block_disable_reason"]').val(null).trigger('change');   
                    table.draw()
                } else {
                    toastr.error(data.error, 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                }
                });
            }
        });  
              
            

        $('input[name="block_disable_remarks"]').keyup(function() {
            $('#blocked_remarks').addClass('d-none');
        });

        $('#BlockDisableReasonModal').on('hide.bs.modal',function (e) {
            $('#BlockDisableReasonModal').find('.modal-body input[name="block_disable_remarks"]').val('');            
            $('#BlockDisableReasonModal').find('.modal-body select[name="block_disable_reason"]').val(null).trigger('change'); 
            $('#blocked_remarks').addClass('d-none');
            $('#blocked_reasons').addClass('d-none');
            disable_user_id = null; 
            block_user_id = null; 
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
        $("#eso").prepend('<option value="" selected></option>').select2({
            placeholder: "Select ESO",
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

         //Pause User
         $('body').on('click','button.pause_shipper_booking',function () {
            var status  = "pause";
            var id = $(this).parents('tr').attr('id');
            swal({
                title: 'Are You Sure?',
                text: 'Select Yes to pause this account!',
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
                        if(data.status == 1){
                            $('#duplicate_modal').modal('show');
                            // var html = '<table class="table table-bordered"><tr><td><strong>Phone</strong></td><td>'+ data.info.phone +'</td></tr><tr><td><strong>CNIC</strong></td><td>'+ data.info.cnic +'</td></tr><tr><td><strong>IBAN</strong></td><td>'+ data.info.iban +'</td></tr><tr><td><strong>Name</strong></td><td>'+ data.info.name +'</td></tr>';

                                // var html = '<table class="table table-bordered">' +
                                //             '<tr>' +
                                //                 '<td><strong>Phone</strong></td>' +
                                //                 '<td>' + data.info.phone + '</td>' +
                                //                 '<td>' + (data.info.shared_phone ? data.info.shared_phone : '') + '</td>' +
                                //             '</tr>' +
                                //             '<tr>' +
                                //                 '<td><strong>CNIC</strong></td>' +
                                //                 '<td>' + data.info.cnic + '</td>' +
                                //                 '<td>' + (data.info.shared_cnic ? data.info.shared_cnic : '') + '</td>' +
                                //             '</tr>' +
                                //             '<tr>' +
                                //                 '<td><strong>IBAN</strong></td>' +
                                //                 '<td>' + data.info.iban + '</td>' +
                                //                 '<td>' + (data.info.shared_iban ? data.info.shared_iban : '') + '</td>' +
                                //             '</tr>' +
                                //             '<tr>' +
                                //                 '<td><strong>Name</strong></td>' +
                                //                 '<td>' + data.info.name + '</td>' +
                                //                 '<td>' + (data.info.shared_name ? data.info.shared_name : '') + '</td>' +
                                //             '</tr>' +
                                //         '</table>';


                                var baseURL = "{{ url('admin/accounts') }}";
                                var html = '<table class="table table-bordered">';
                                html += '<thead>';
                                html += '<tr>' +
                                    '<th><strong>User Information</strong></th>' +
                                    '<th><strong>User Values</strong></th>' +
                                    '<th><strong>Duplicate Ids</strong></th>' +
                                    '</tr>';
                                html += '</thead>';
                                html += '<tbody>';
                                html += '<tr>' +
                                    '<td><strong>Phone</strong></td>' +
                                    '<td>' + data.info.phone + '</td>' +
                                    '<td>' + (data.info.shared_phone ?
                                        generateLinks(data.info.shared_phone.split(','), baseURL, 'phone') : '') + '</td>' +
                                    '</tr>';
                                html += '<tr>' +
                                    '<td><strong>CNIC</strong></td>' +
                                    '<td>' + data.info.cnic + '</td>' +
                                    '<td>' + (data.info.shared_cnic ?
                                        generateLinks(data.info.shared_cnic.split(','), baseURL, 'cnic') : '') + '</td>' +
                                    '</tr>';
                                html += '<tr>' +
                                    '<td><strong>IBAN</strong></td>' +
                                    '<td>' + data.info.iban + '</td>' +
                                    '<td>' + (data.info.shared_iban ?
                                        generateLinks(data.info.shared_iban.split(','), baseURL, 'iban') : '') + '</td>' +
                                    '</tr>';
                                html += '<tr>' +
                                    '<td><strong>Name</strong></td>' +
                                    '<td>' + data.info.name + '</td>' +
                                    '<td>' + (data.info.shared_name ?
                                        generateLinks(data.info.shared_name.split(','), baseURL, 'name') : '') + '</td>' +
                                    '</tr>';
                                html += '<tr>' +
                                    '<td><strong>NTN</strong></td>' +
                                    '<td>' + (data.info.ntn && data.info.shared_ntn_no.length ? data.info.ntn : '') + '</td>' +
                                    '<td>' + (data.info.shared_ntn_no ?
                                        generateLinks(data.info.shared_ntn_no.split(','), baseURL, 'ntn') : '') + '</td>' +
                                    '</tr>';
                                html += '<tr>' +
                                    '<td><strong>Email</strong></td>' +
                                    '<td>' + (data.info.shared_email && data.info.shared_email.includes(data.info.email) ? data.info.email : '') + '</td>' +
                                    '<td>' + (data.info.shared_email && data.info.shared_email !== '' && !data.info.shared_email.includes(data.info.email) ?
                                        generateLinks(data.info.shared_email.split(','), baseURL, 'email') : '') + '</td>' +
                                    '</tr>';
                                html += '</tbody>';
                                html += '</table>';

                                function generateLinks(ids, baseURL, type) {
                                    var links = [];
                                    for (var i = 0; i < ids.length; i++) {
                                        var url = baseURL + '/' + ids[i].trim() + '/view';
                                        links.push('<a href="' + url + '" target="_blank">' + ids[i].trim() + '</a>');
                                    }
                                    return links.join(', ');
                                }

                            $('#duplicate_modal .modal-body').html(html);
                        }

                    });
            }
        });
        var old_dates = [];

        $("#corporate_rate_type_select").prepend('<option value="" selected></option>').select2({
            placeholder: "Select Corporate Rate Type",
            width:'100%',
            dropdownParent:$('#SwitchCorporate')
        });

        $('body').on('click', 'button.switch_corporate_button',  function(){
            var id = $(this).parents('tr').attr('id');
            if(id){
                $('#SwitchCorporate').modal('show');
                $('#corporate_rate_type_shipper_id').val(id);
            }
        });

        $('#switch_corporate_submit_btn').on('click',function () {
            var shipper   = Number($('#corporate_rate_type_shipper_id').val());
            var rate_type = Number($('#corporate_rate_type_select').val());
            var url = "{{ url('') }}/admin/corporate/" + shipper + "/add/rates";
            if(rate_type){
                $.ajax({
                    url: '{!! route('admin.accounts.switch_corporate_submit') !!}',
                    method: 'POST',
                    data: {
                        'shipper_id':shipper,
                        'rate_type':rate_type,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function(data) {
                        if(data.status){
                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            window.location.href = url;
                        }
                        else {
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                        }
                        $('#corporate_rate_type_select').val('').trigger('change');
                        $('#SwitchCorporate').modal('hide');
                        table.draw(true);
                    });
            }else{
                var error = "Rate Type Not Selected!";
                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
            }

        });

        //switch re

        $('body').on('click', 'button.switch_reimbursement_button', function () {
            var id = $(this).data('target-id');

            if (id) {
                $('#reimbursement_rate_type_shipper_id').val(id);
                $('#SwitchReimbursement').modal('show');
            }
        });

        $('#switch_reimbursement_submit_btn').on('click', function () {
            var shipper = Number($('#reimbursement_rate_type_shipper_id').val());
            var url = "{{ url('') }}/admin/accounts/" + shipper + "/add/rates";

            if (shipper) {
                $.ajax({
                    url: '{!! route('admin.accounts.switch_reimbursement_submit') !!}',
                    method: 'POST',
                    data: {
                        shipper_id: shipper,
                        _token: '{{ csrf_token() }}'
                    }
                })
                    .done(function (data) {
                        if (data.status) {
                            toastr.success(data.success, 'Success!', {
                                positionClass: 'toast-bottom-center',
                                containerId: 'toast-bottom-center'
                            });

                            window.location.href = url;
                        } else {
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }

                        $('#reimbursement_rate_type_shipper_id').val('');
                        $('#SwitchReimbursement').modal('hide');
                        table.draw(true);
                    })
                    .fail(function () {
                        toastr.error('Something went wrong!', 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    });
            } else {
                toastr.error('Shipper not selected!', 'Error!', {
                    positionClass: 'toast-top-center',
                    containerId: 'toast-top-center'
                });
            }
        });

        //end

        $('#old_rate_date').prepend('<option value="" selected="selected"></option>').select2({
            placeholder:'Select Date',
            width:'100%',
            templateResult: formatState
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
                        $.each(data.details, function(key, value) {
                            let weightSign = value.weight === 'green' ? '↑' : (value.weight === 'red' ? '↓' : (value.weight === 'yellow' ? '←→' : 'Rates Updated Only'));
                            let weightColor = value.weight === 'green' ? 'green' : (value.weight === 'red' ? 'red' : (value.weight === 'yellow' ? 'yellow' : 'Rates Updated Only'));

                            let fuelSign = value.fuel === 'green' ? '↑' : (value.fuel === 'red' ? '↓' : (value.fuel === 'yellow' ? '←→' : 'Fuel Added Only'));
                            let fuelColor = value.fuel === 'green' ? 'green' : (value.fuel === 'red' ? 'red' : (value.fuel === 'yellow' ? 'yellow' : 'Fuel Added Only'));


                            $('#old_rate_date').append(
                                $('<option></option>')
                                    .attr('value', key)
                                    .attr('data-wsign', weightSign)
                                    .attr('data-wcolor', weightColor)
                                    .attr('data-fsign', fuelSign)
                                    .attr('data-fcolor', fuelColor)
                                    .text(key)
                            );
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
    var id = $(this).parents('tr').attr('id');
        if ($(this).hasClass('add_fintech_charges')) {
            if (id) {
                $("#userID").val(id);
                var userID = id;
                $.ajax({
                    type: 'Get',
                    url: "{!! route('admin.accounts.user_fintech_charges') !!}",
                    data: {
                        userID: userID,
                    },
                    success: function(res) {
                        if (res.status == '200') {
                            if (document.getElementById('user_fintech_charges_checkbox').checked) {

                            } 
                            else {
                                $('#user_fintech_charges_checkbox').click();
                            }
                            var toggleButton = document.getElementById("user_fintech_charges_checkbox");
                            $(".UserFintechCharges").html('');
                            $(".UserFintechCharges").append(`
                            <div class="form-group ">
                                <div class="input-group ">
                                    <input type="text" value="${res.data.fintech_charges}" onkeydown="inputValidate()" id="user_fintech_charges_txtbox" class="form-control fuel_factor" placeholder ="Fintech Charges*" aria-invalid="true"  data-rule-required="true" data-msg-required="Fintech Charges is Required">
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>`);
                        } 
                        else {
                            if (document.getElementById('user_fintech_charges_checkbox').checked) {
                                $('#user_fintech_charges_checkbox').click();
                            } else {

                            }
                            $(".UserFintechCharges").html('');
                        }
                    }
                });

                $('#AddFintechChargesModal').modal('show');
            }
        }
    });

        // Shipper exclude feature
        $(document).ready(function() {
            $('#shipper').prepend('<option value="" selected></option>').select2({
                width:'100%',
                placeholder:"Select Shipper",
                allowClear:true,
            });

            // Setup event handlers for checkboxes
            var exclude_shipper = $('#exclude_shipper');
            var different_consignee = $('#different_consignee');
            var same_consignee = $('#same_consignee');

            $('#interceptModalCloseBtn_1, #interceptModalCloseBtn_2').click(function() {
                exclude_shipper.prop('checked', false);
                different_consignee.prop('checked', false);
                same_consignee.prop('checked', false);
                exclude_shipper.prop('disabled', false);
                different_consignee.prop('disabled', false);
                same_consignee.prop('disabled', false)
                $('#add_shipper_exclude_intercept_type')[0].reset();
            });

            // if exclude shipper is checked
            exclude_shipper.on('change', function() {
                if ($(this).prop('checked')) {
                    different_consignee.prop('disabled', true);
                    same_consignee.prop('disabled', true);
                } else {
                    different_consignee.prop('disabled', false);
                    same_consignee.prop('disabled', false);
                }
            });

            // if different consignee is checked
            different_consignee.on('change', function() {
                if ($(this).prop('checked')) {
                    exclude_shipper.prop('disabled', true);
                    if (same_consignee.prop('checked')) {
                        var error = "Cannot select Different Consignee when Same Consignee is selected. Please un-check.";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        $(this).prop('checked', false);
                    }
                } else {
                    exclude_shipper.prop('disabled', false);
                }
            });

            // if same consignee is checked
            same_consignee.on('change', function() {
                if ($(this).prop('checked')) {
                    exclude_shipper.prop('disabled', true);
                if (different_consignee.prop('checked')) {
                        var error = "Cannot select Same Consignee when Different Consignee is selected. Please un-check.";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        $(this).prop('checked', false);
                    }
                } else {
                    exclude_shipper.prop('disabled', false);
                }
            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                var id = $(this).parents('tr').attr('id');
                var name = $(this).parents('tr').find('td:eq(4)').text();
                if ($(this).hasClass('add_shipper_exclude_intercept_type')) {
                    $('#AddShipperExcludeInterceptType #add_shipper_exclude_intercept_type #user_id').val(id);
                    $('#shipper_name').text(name);
                    $('#AddShipperExcludeInterceptType').modal('show');
                }
            });

            $("#AddShipperExcludeInterceptType #add_shipper_exclude_intercept_type").validate({
                errorClass: "danger",
                successClass: 'success',
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    if (!exclude_shipper.prop('checked') && !different_consignee.prop('checked') && !same_consignee.prop('checked')) {
                            var error = "Please select at least one option.";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    } else {
                        swal({
                            title: 'Are you sure?',
                            text: 'Select Yes to update Intercept Request Exclude Shippers!',
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
                            })
                        .then(function(confirm) {
                            if (confirm) {
                                form.submit();
                            }
                        });
                    }
                }
            });
        });

        $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {

            var user_id = table.row( $(this).parents('tr') ).data().id;
            var rate_type_id = table.row( $(this).parents('tr') ).data().corporate_rate_type_id;

            if ($(this).hasClass('add_shipper_exclude_intercept_type')){
                $.ajax({
                    url: '{!! route('admin.accounts.excluded_shippers') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'user_id': user_id,
                    }
                    }).done(function(response){
                        if (response.intercept_shipper !== null) {
                            var interceptShipperData = response.intercept_shipper;
                            var different_consignee = interceptShipperData.different_consignee;
                            var exclude_shipper = interceptShipperData.exclude_shipper;
                            var same_consignee = interceptShipperData.same_consignee;
                            if (different_consignee == 1) {
                                $('#different_consignee').prop('checked', true);
                            }
                            if (exclude_shipper == 1) {
                                $('#exclude_shipper').prop('checked', true);
                            }
                            if (same_consignee == 1) {
                                $('#same_consignee').prop('checked', true);
                            }
                        } else {
                            return false;
                        }
                    });
            }

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
        var selected_rows_2 = [];
        $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));
              
                var index = $.inArray(id, selected_rows);
                var index_2 = $.inArray(id, selected_rows_2);

                var dataTable = $('#datatable').DataTable();
                var tr = $(this).closest('tr');
                var row = dataTable.row(tr);
                var rowData = row.data();
                var cond = (rowData.status_id != 2);
               
               if (index_2 === -1 && cond) {
                    selected_rows_2.push(id);
                }else {
                    if(selected_rows_2.includes(id)){
                        selected_rows_2.splice(index_2, 1);
                    }
                }
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
                    table.button('.payment_cycle').enable();

                }
                else {
                    table.button('.bulk_tagging').disable();
                    table.button('.bulk_segment_tagging').disable();
                    table.button('.territory_retag').disable();

                    table.button('.tag').disable();
                    table.button('.set_commission').disable();
                    table.button('.approve_commission').disable();
                    table.button('.territory_tag').disable();
                    table.button('.payment_cycle').disable();
                }
        });
        $('#payment_cycles').prepend('<option value="" selected="selected"></option>').select2({
            width: '100%',
            placeholder: 'Select Payment Cycle'
        }).on('change', function () {
            $('#msg_limit_days').addClass('d-none')
           
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

        var len = null;

        function updateCheckboxValues(limit, data) {
            var paymentDayString = data;
            var paymentDayArray = paymentDayString.split(',');
            $('#selected_days').val(paymentDayArray);            
            var selectedCount = 0;
            var selectedValues = [];

            $('#checkboxContainer input[type="checkbox"]').each(function() {
                var checkboxValue = $(this).val();
                if (paymentDayArray.includes(checkboxValue)) {
                    if (selectedCount < limit) {
                        $(this).prop('checked', true);
                        selectedValues.push(checkboxValue); 
                        selectedCount++;
                    } else {
                        $(this).prop('checked', false);
                    }
                } else {
                    $(this).prop('checked', false);
                }
            });

            $('#checkboxContainer input[type="checkbox"]').on('click', function() {
                var checkboxValue = $(this).val();

                if ($(this).prop('checked')) {
                    if (selectedCount >= limit) {
                        $(this).prop('checked', false);
                    } else {
                        selectedCount++;
                        selectedValues.push(checkboxValue);
                    }
                } else {
                    selectedCount--;
                    selectedValues = selectedValues.filter(function(value) {
                        return value !== checkboxValue;
                    });
                }

                selectedValues = selectedValues.sort(function(a, b) {
                    return a - b;
                });
                if(selectedValues.length != 0){
                    $('#selected_days').val(selectedValues);
                }else if (paymentDayArray.length == selectedValues.length){
                    $('#selected_days').val(paymentDayArray);
                }else{
                    $('#selected_days').val('');
                }
            });
        }

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
                            $('#payment_cycles').val(data.details.payment_cycle_id).trigger('change');
                            if(data.details.payment_cycle_id == 6){//Fortnight
                                var paymentDayString = data.details.payment_day;
                                var paymentDayArray = paymentDayString.split(',');
                                var option = $('<option></option>').attr('value', paymentDayArray[1]).text(paymentDayArray[1] + " Days");
                                var value = paymentDayArray[0] + ',' + paymentDayArray[1];
                                $('#fornite').val(paymentDayArray[0]);
                                $("#fornite_2").empty().append(option);
                                $('#fortnite_val').val(value);
                                $('#fornite_2').removeClass('d-none');
                                $('#label_2').removeClass('d-none');
                            }else if(data.details.payment_cycle_id == 3){// Monthly
                                $('#monthly').removeClass('d-none');
                                $('#monthly').val(data.details.payment_day);
                            }else if (data.details.payment_cycle_id == 4) { // Twice a week
                                updateCheckboxValues(2, data.details.payment_day);
                            }else if(data.details.payment_cycle_id == 5){//Thrice a week
                                updateCheckboxValues(3, data.details.payment_day);
                            }else if(data.details.payment_cycle_id == 2){//weekly
                                updateCheckboxValues(1, data.details.payment_day);
                            }else if(data.details.payment_cycle_id != 1){//Daily
                                $('#payment_day').val(data.details.payment_day);
                            } else{
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

        $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
            var id = $(this).parents('tr').attr('id');
            if($(this).hasClass('faf_charges_status')){
                if(id){
                    $.ajax({
                        url: '{!! route('admin.accounts.faf_charges.info') !!}',
                        method: 'POST',
                        data: {
                            'user_id': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                        .done(function(data) {
                            $('#faf_charges_checkbox').prop('checked',false);
                            if (data.status == 1) {
                                $('#faf_charges_checkbox').click();
                                $('#faf_percentage').val(data.percentage);
                            }
                            $('#faf_charges_user_id').val(id);
                            $('#faf_charges_modal').modal('show');


                        });
                }
            }
        });

        $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
            var id = $(this).parents('tr').attr('id');
            if($(this).hasClass('exp_shipment_percentage')){
                if(id){
                    $.ajax({
                        url: '{!! route('admin.accounts.exp_shipment_percentage.info') !!}',
                        method: 'POST',
                        data: {
                            'user_id': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                    .done(function(data) {
                        if (data.status == 1) {
                            $('#exp_shipment_percentage').val(data.percentage);
                        }
                        $('#exp_shipment_user_id').val(id);
                        $('#exp_shipment_modal').modal('show');


                    });
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
        $('#exp_shipment_form').validate({
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
                    text: 'Its being updated!',
                    icon: 'info',
                    buttons: false,
                    closeOnClickOutside: false,
                    closeOnEsc: false
                });
                form.submit();
            }
        });
        $('#faf_charges_form').validate({
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
                    text: 'Its being updated!',
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
            normalizer: function (value) {
                return $.trim(value);
            },
            errorPlacement: function (error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
            submitHandler: function (form) {
                var formData = $(form).serializeArray();
                var fortnite = formData[3]['value'].split(',');
                var monthly = formData[6]['value'];
                var selected_days = [];

                if (formData[7] && formData[7]['value']) {
                    var splitValues = formData[7]['value'].split(',');
                    if (splitValues.length > 0) {
                        selected_days = splitValues;
                    }
                }                

                var swalConfig = {
                    title: 'Please Wait!',
                    text: 'Payment Cycle is being Updated!',
                    icon: 'info',
                    buttons: false,
                    closeOnClickOutside: false,
                    closeOnEsc: false
                };
                fortnite = fortnite.length;
                selected_days = selected_days.length;
                if ((formData[2]['value'] == '4' && selected_days === 2) ||
                    (formData[2]['value'] == '5' && selected_days === 3) ||
                    (formData[2]['value'] == '2' && selected_days === 1) || 
                    (formData[2]['value'] == '6' && fortnite > 1) || 
                    (formData[2]['value'] == '1') ||
                    (formData[2]['value'] == '3' && (monthly !== "nonem"))) {
                        swal(swalConfig);
                        form.submit();   
                } 
                else {
                    if(formData[2]['value'] == '4'){
                        days = 2;
                        $('#msg_limit_days').text('Select ' + days + ' days only');
                        $('#msg_limit_days').removeClass('d-none');
                    }else if (formData[2]['value'] == '5'){
                        days = 3
                        $('#msg_limit_days').text('Select ' + days + ' days only');
                        $('#msg_limit_days').removeClass('d-none');
                    }else if (formData[2]['value'] == '2'){
                        days = 1;
                        $('#msg_limit_days').text('Select ' + days + ' day only');
                        $('#msg_limit_days').removeClass('d-none');
                    }else if (formData[2]['value'] == '6'){
                        $('#msg_limit_days').text('Select day');
                        $('#msg_limit_days').removeClass('d-none');
                    }else if (formData[2]['value'] == '3'){
                        $('#msg_limit_days').text('Select day');
                        $('#msg_limit_days').removeClass('d-none');

                    }
                }
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
                            // console.log(value,value.admin);
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

            if ($(this).hasClass('view_delivered_setting_log')) {

                $.ajax({
                    url: '{!! route('admin.accounts.on_delivered.invoice.log') !!}',
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
                            // console.log(value,value.admin);
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

                        $('#CorporateDeliveredInvoiceLogModal .modal-body').html(html);

                        $('#CorporateDeliveredInvoiceLogModal').modal('show');

                    }
                    else{
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }

                });
            }
        });
    });



    function inputValidate() {
    $('#Save_fintech_charges input.fuel_factor').inputmask({
       
        'allowMinus': true,
        'allowPlus': false,
        'max':100
    });
    var oldValue = "";
    $("input.input-filtered").on("input", function() {
        if (this.value === "" || this.value !== oldValue) {
            oldValue = this.value;
            this.value = this.value.replace(/^\D+/g, '').replace(/[^0-9.%.]/g, '').replace(/(\..*)\./g, '$1').replace(/(\d+)(%.*)$/g, '$1%');
        }
    });
}

var days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];
        var selectedValues = []; // Create an array to store selected values
        for (var i = 0; i < days.length; i++) {
            var day = days[i];
            var checkboxId = day;
            var labelId = "label_" + day;
            var value = parseInt([i], 10) + 1;

            var checkbox = $("<input>", {
                type: "checkbox",
                id: checkboxId,
                value: value,
            });

            var label = $("<label>", {
                for: checkboxId,
                text: day
            });

            $("#checkboxContainer").append(checkbox);
            $("#checkboxContainer").append(label);
        }


        var numSelected = 1;
        var maxSelections_1 = 1; //Weekly
        var maxSelections_2 = 2; //Twice A Week
        var maxSelections_3 = 3; //Thrice A Week

        function handleCheckboxSelection(numSelectedVar, maxSelectionsVar) {
            return function() {
                var checkbox = $(this);
                var value = checkbox.val()
                if (checkbox.is(':checked')) {
                    if (numSelectedVar <= maxSelectionsVar) {
                        numSelectedVar++;
                        selectedValues.push(value);
                        $('#selected_days').val(selectedValues);

                    } else {
                        checkbox.prop('checked', false);
                        $('#msg_payment').removeClass("d-none");
                    }
                } else {
                    numSelectedVar--;
                    $('#msg_payment').addClass("d-none");
                    var index = selectedValues.indexOf(value);
                    if (index !== -1) {
                        selectedValues.splice(index, 1);
                        $('#selected_days').val(selectedValues);

                    }
                }
            }
        }

        $('#payment_cycles').on('change', function() {
            $("#checkboxContainer input[type='checkbox']").prop('checked', false);
            $('#msg_payment').addClass("d-none");
            selectedValues = [];
            $('#payment_cycle_msg').text('')
            var id = $(this).val();


            var option = $('<option></option>').attr('value', 1).text(1 + " Days");
            $("#fornite_2").empty().append(option);


            if (id == 4 || id == 5 || id == 2) {
                $("#checkboxContainer").removeClass("d-none");
                $('#fornite').addClass('d-none')
                $('#fornite_2').addClass('d-none');
                $('#monthly').addClass('d-none');
                $('#label').addClass('d-none');
                $('#label_2').addClass('d-none');
              

                if (id == 4) {//Twice A Week
                    $("#checkboxContainer input[type='checkbox']").off('click').on('click', handleCheckboxSelection(
                        numSelected, maxSelections_2));
                } else if (id == 5) {//Thrice A Week
                    $("#checkboxContainer input[type='checkbox']").off('click').on('click', handleCheckboxSelection(
                        numSelected, maxSelections_3));
                } else if (id == 2) {//Weekly
                    $("#checkboxContainer input[type='checkbox']").off('click').on('click', handleCheckboxSelection(
                        numSelected, maxSelections_1));
                }
            } else if (id == 6) {
                $("#checkboxContainer").addClass("d-none");
                $('#fornite').removeClass('d-none')
                $('#label').removeClass('d-none')
                $('#monthly').addClass('d-none');

            } else if (id == 3) {
                $('#monthly').removeClass('d-none')
                $("#checkboxContainer").addClass("d-none");
                $('#fornite').addClass('d-none');
                $('#label').addClass('d-none');
                $('#fornite_2').addClass('d-none')
                $('#label_2').addClass('d-none');

            } else {
                $("#checkboxContainer").addClass("d-none");
                $('#fornite').addClass('d-none');
                $('#fornite_2').addClass('d-none');
                $('#monthly').addClass('d-none');
                $('#label_2').addClass('d-none');
                $('#label').addClass('d-none')

            }
        });
        $('#fornite').on('change', function() {
            var fornite = parseInt($(this).val(), 10);
            var fornite_2 = fornite + 15;
            var value = $(this).val() + ',' + fornite_2;
            var option = $('<option></option>').attr('value', fornite_2).text(fornite_2 + " Days");
            $("#fornite_2").empty().append(option);
            $('#fornite_2').removeClass('d-none');
            $('#label_2').removeClass('d-none');
            $('#msg_payment').addClass("d-none");
            $('#fortnite_val').val(value);
        });

        $('#SalesTierTypeTagModal').on('hide.bs.modal', function (e) {
            selected_commission = 0;
            commission = 0;
            selected_users.length = 0;   
            commission_max = $('#commission_max').val()         
            $('#total_commission').val(0).trigger('change');
            $('#total_commission_value').text('0');
            $('#datatable_rate').DataTable().clear().draw();
        });
        
    var selected_users = [];
    var tier_sales;
    var users = @json($all_users);

    var users_data = $.map(users, function (obj) {
        obj.id = obj.id || obj.text;
        return obj;
    });
    $('#user_select').prepend('<option value="" selected></option>').select2({
        placeholder: "Select User",
        width: '100%',
        data: users_data,
    }).bind('change', function () {
        var th = $(this);
        var id = $(this).val();
        var group = $(this).find(':selected').closest('optgroup').attr('label');
        if (group == 'Admins') {
            if (tier_sales == 1) {
                th.val(null).trigger('change');
                var error = 'Select sales related user!';
                toastr.error(error, 'Error!', {
                    positionClass: 'toast-top-center',
                    containerId: 'toast-top-center'
                });
            }
        }
        if (group != 'Admins' && group != 'Sales'){
            $('#user_commission').val(1.8)
            $('#user_commission').attr('disabled', true)
        } else {
            $('#user_commission').val('');
            if($('#sales_tier_select').val() == 3){
                $('#user_commission').attr('disabled', true)
                $('#user_commission').val('0');
            }
            else
            {
                $('#user_commission').attr('disabled', false)
                $('#user_commission').val('');
            }
        }

        
        if($('#user_select').val() != '')
        {
            $('#commission_add_button').attr('disabled', false);
        }

        var index = $.inArray(id, selected_users);
        if (index !== -1) {
            var error = 'User previously selected!';
            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
            $('#user_select').val(null).trigger('change');
        }
    });

    var riders_permanents_data = {!! json_encode($riders_permanent) !!};
    var selectHtml = '';
    for (var i = 0; i < riders_permanents_data.length; i++) {
        selectHtml += '<option value="' + riders_permanents_data[i].id + 'riders' +'">' + riders_permanents_data[i].name + '-' + riders_permanents_data[i].trax_id + '</option>';
    }
    $('#user_select').append(selectHtml);

    $('#sales_tier_select').prepend('<option value="" selected></option>').select2({
        placeholder: "Select Sales Tier",
        width: '100%'
    }).bind('change', function () {
        $('#user_select').attr('disabled', true);
        $('#external_person_name').attr('disabled', true);
        var type = $(this).find(":selected").attr('type');
        var sales = $(this).find(":selected").attr('sales');
        tier_sales = sales;
        if (type == 1) {
            $('#user_select').attr('disabled', false);
        } else {
            $('#external_person_name').attr('disabled', false);
        }

        if($(this).val() == 3)
        {
            $('#user_commission').attr('disabled', true);
            $('#user_commission').val('0');
        }
        else
        {
            $('#user_commission').attr('disabled', false);
            $('#user_commission').val('');
        }

    });

    var commission_max = $('#commission_max').val();
    $('.commission').inputmask({
        'alias': 'decimal',
        'allowMinus': false,
        'allowPlus': false,
        'rightAlign': false,
        'digits': 2,
        'min': 0.00,
        'max': commission_max,
    });

    $('#exp_shipment_percentage').inputmask({
        'alias': 'decimal',
        'allowMinus': false,
        'allowPlus': false,
        'rightAlign': false,
        'digits': 2,
        'min': 0.00,
    });

    var table_2 = $('#datatable_rate').DataTable({
        dom: 'ltipr',
        paging: false,
        ordering: false,
        sorting: false,
        bInfo: false,
        columns: [
            {
                orderable: false,
                searchable: false,
                name: 'serial_number',
                class: 'align-middle serial_number',
                targets: 1,
                render: function (data, type, row) {
                    return '';
                }
            },
            {name: 'user_name', class: 'align-middle user_name'},
            {name: 'tier', class: 'align-middle tier'},
            {name: 'commission_percentage', class: 'align-middle commission_percentage'},
            {name: 'action', class: 'align-middle action'}

        ],
        rowCallback: function (row, data, index) {
            var info = table_2.page.info();

            $('td:eq(0)', row).html(index + 1 + info.page * info.length);

        },
    });

    var row = 1;
    var selected_commission = 0;

    function add_commission_row(tier_id, tier_name, tier_type, user_id, user_name, commission) {
        var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-danger remove"><i class="la la-close"></i></a>';
        var tier = '<div><input type="hidden" name="tier_id[' + row + ']"  value="' + tier_id + '">' + tier_name + '</div>';
        if (tier_type == 1) {
            var name = '<div><input type="hidden" name="user_id[' + row + ']"  value="' + user_id + '">' + user_name + '</div>';
        } else {
            var name = '<div><input type="hidden" name="user_id[' + row + ']"  value="' + user_name + '">' + user_name + '</div>';
        }
        var commission_percentage = '<div><input type="hidden" name="commission_percentage[' + row + ']"  value="' + commission + '">' + commission + '%</div>';
        table_2.row.add([row, name, tier, commission_percentage, remove]).node().id = row;
        table_2.draw(false);
        if (tier_type == 1) {
            selected_users.push(user_id);
        }
        $('#commission_add_button').attr('disabled', false);
        $('#total_commission_value').html(selected_commission);
        $('#total_commission').val(selected_commission);
        $('#total_commission_value').val(selected_commission);

        row++;
    }

    function roundToTwo(num) {
        return +(Math.round(num + "e+2") + "e-2");
    }

    $('#commission_add_button').on('click', function () {
        var commission = parseFloat($('#user_commission').val());
        var this_btn = $(this);

        var flag = true;
        var type = $('#sales_tier_select').find(":selected").attr('type');
        if (!$('#sales_tier_select').valid()) {
            flag = false;
        }
        if (type == 1) {
            if (!$('#user_select').valid()) {
                flag = false;
            }
        }
        if (type == 2) {
            if (!$('#external_person_name').valid()) {
                flag = false;
            }
        }
        if (!$('#user_commission').valid()) {
            flag = false;
        }

        if (flag) {
       

            if (commission <= commission_max) {
                selected_commission = roundToTwo(selected_commission + commission);
                commission_max = commission_max - commission;
                this_btn.attr('disabled', true);
                var user_id = '';
                var user_name = '';
                var tier_id = '';
                var tier_name = '';
                var tier_type = '';
                tier_id = $('#sales_tier_select').val();
                tier_name = $('#sales_tier_select').find(":selected").text();
                tier_type = $('#sales_tier_select').find(":selected").attr('type');
                if (tier_type == 1) {
                    user_id = $('#user_select').val();
                    user_name = $('#user_select').find(":selected").text();
                } else {
                    user_name = $('#external_person_name').val();
                }

                if(tier_id == 3)
                {
                    if($('#user_select').val() == '')
                    {
                        var error = 'Please select user!';
                        toastr.error(error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                            });
                            return 0;
                    }
                }

                add_commission_row(tier_id, tier_name, tier_type, user_id, user_name, commission);
                $('#sales_tier_select').val(null).trigger('change');
                $('#user_select').val(null).trigger('change');
                $('#user_select').attr('disabled', true);
                $('#external_person_name').val('');
                $('#external_person_name').attr('disabled', true);
                $('#user_commission').val('');

            } else {
                var error = 'Selected Commission value exceeds!';
                toastr.error(error, 'Error!', {
                    positionClass: 'toast-top-center',
                    containerId: 'toast-top-center'
                });
            }
        }
    });
        $('#datatable_rate tbody').on('click', 'tr td.action a.remove', function () {
            var id = $(this).parents('tr').attr('id');

            var user_id = $('input[name="user_id[' + id + ']"]').val();
            if (user_id) {
                var index = $.inArray(user_id, selected_users);
                if (index !== -1) {
                    selected_users.splice(index, 1);
                }
            }
            var commission = parseFloat($('input[name="commission_percentage[' + id + ']"]').val());
            commission_max = roundToTwo(commission_max + commission);
            selected_commission = roundToTwo(selected_commission - commission);
            $('#total_commission_value').html(selected_commission);
            $('#total_commission').val(selected_commission);
            table_2.row($(this).parents('tr')).remove().draw();
        });
        $('.decimal').inputmask({
            'alias': 'decimal',
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            'digits': 2,
            'min': 0.00,
            'max': 100000
        });

        $('.amount').inputmask({
            'alias': 'decimal',
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            'digits': 2,
            'min': 0.00,
            'max': 1000000.00
        });

        $('.percent').inputmask({
            'alias': 'numeric',
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            'min': 0,
            'max': 500
        });
        $('.numeric').inputmask({
            'alias': 'integer',
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            'min': 0,
            'max': 1000000
        });
        $('.dec-percent').inputmask("Regex", {
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            regex: '^\\d{1,9}(\\.\\d{1,2})?%?$'
        });

         $('#ratesAdditionForm').submit(function (cz) {
            if (table_2.data().count() === 0) {
                toastr.error('Enter Atleast One Row', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                cz.preventDefault();
            } else {
                $('#ratesAdditionForm').submit()
            }
        });

        function formatState(state) {
            if (!state.id) {
                return state.text;
            }

            let weightColor = $(state.element).data('wcolor');
            let weightSign = $(state.element).data('wsign');

            let fuelColor = $(state.element).data('fcolor');
            let fuelSign = $(state.element).data('fsign');

            let $weightColorSquare = $('<span></span>').css({
                'width': '15px',
                'height': '15px',
                'background-color': weightColor,
                'border': '1px solid #2c3e50',
                'display': 'inline-block',
                'margin-right': '5px'
            });

            let $weightSign = $('<span></span>').text(weightSign).css({
                'margin-right': '5px'
            });

            let $fuelColorSquare = $('<span></span>').css({
                'width': '15px',
                'height': '15px',
                'background-color': fuelColor,
                'border': '1px solid #2c3e50',
                'display': 'inline-block',
                'margin-right': '5px'
            });

            let $fuelSign = $('<span></span>').text(fuelSign).css({
                'margin-right': '5px'
            });

            let $result = $('<span></span>').css({
                'display': 'flex',
                'justify-content': 'space-between',
                'align-items': 'center',
                'width': '100%'
            });

            let $left = $('<span></span>').text(state.text);
            let $right = $('<span></span>').css({
                'display': 'flex',
                'align-items': 'center'
            });

            $right.append('W: ').append($weightSign).append($weightColorSquare);
            $right.append(' F: ').append($fuelSign).append($fuelColorSquare);

            $result.append($left).append($right);

            return $result;
        }

        $(document).on("click", ".account_tagging_history", function () {
            let accountId = $(this).data("id");
            let tableBody = $("#taggingHistoryTable tbody");
            tableBody.html("<tr><td colspan='6' class='text-center'>Loading...</td></tr>");

            $.get("/admin/management/" + accountId + "/tagging-history", function (groups) {
                tableBody.empty();

                if ($.isEmptyObject(groups)) {
                    tableBody.html("<tr><td colspan='6' class='text-center'>No history found</td></tr>");
                } else {
                    let groupIndex = 1;
                    $.each(groups, function (timestamp, logs) {
                        let first = true;
                        logs.forEach((log, index) => {
                            let type = log.type == 1 ? "Sales" : (log.type == 3 ? "KAM" : "Other");

                            tableBody.append(`
                                <tr>
                                    <td>${first ? groupIndex : ''}</td>
                                    <td>${log.changed_by ? log.changed_by.name : '-'}</td>
                                    <td>${log.prev_sales_admin ? log.prev_sales_admin.name : '-'}</td>
                                    <td>${log.new_sales_admin ? log.new_sales_admin.name : '-'}</td>
                                    <td>${type}</td>
                                    <td>${first ? timestamp : ''}</td>
                                </tr>
                            `);

                            first = false;
                        });
                        groupIndex++;
                    });
                }
            });
        });



    </script>

@endsection

