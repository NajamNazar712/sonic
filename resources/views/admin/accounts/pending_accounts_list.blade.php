@extends('admin.layout.master')

@section('title', 'Pending Accounts List')

@section('content')
    <h1>Pending Accounts List</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('admin.inc.messages')
                    @if (session('role_id') == 1 || count(array_intersect([276, 321], session('permissions'))) !== 0)
                        <div id="search_form" class="row p-1 mb-2">
                            <div class="col-4">
                                <fieldset class="form-group">
                                    <select name="search_admins[]" id="search_admins" class="form-control select2" multiple="multiple" required data-rule-required="true" data-msg-required="This field is required">
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
                                    {{-- <input type="text" name="search_shipper[]" id="search_shipper" class="form-control shipper_name" placeholder="Shipper Name" multiple> --}}
                                    <select name="search_shipper[]" id="search_shipper" class="form-control select2"  multiple>
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-4">
                                <fieldset class="form-group">
                                    <input type="email" name="search_email" id="search_email" class="form-control shipper_name" placeholder="Email">
                                </fieldset>
                            </div>
                            <div class="col-2">
                                <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                            </div>
                        </div>
                    @endif

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                    <tr class="bg-primary white">
                                        <th class="border-primary border-darken-1"></th>
                                        <th class="border-primary border-darken-1">S. No</th>
                                        <th class="border-primary border-darken-1">Lead ID</th>
                                        <th class="border-primary border-darken-1">Account ID</th>
                                        <th class="border-primary border-darken-1">Account Type</th>
                                        <th class="border-primary border-darken-1">Company</th>
                                        <th class="border-primary border-darken-1">Contact Person</th>
                                        <th class="border-primary border-darken-1">Address</th>
                                        <th class="border-primary border-darken-1">City</th>
                                        <th class="border-primary border-darken-1">Territory</th>
                                        <th class="border-primary border-darken-1">Product Type</th>
                                        <th class="border-primary border-darken-1">Request Date</th>
                                        <th class="border-primary border-darken-1">Status</th>
                                        <th class="border-primary border-darken-1">Sales Person Tagged</th>
                                        <th class="border-primary border-darken-1">POC Tagged</th>
                                        <th class="border-primary border-darken-1">KAM Tagged</th>
                                        <th class="border-primary border-darken-1">REF Tagged</th>
                                        {{-- <th class="border-primary border-darken-1">ESO Tagged</th> --}}
                                        <th class="border-primary border-darken-1">SMS charges per shipment</th>
                                        <th class="border-primary border-darken-1">Rate Status</th>
                                        <th class="border-primary border-darken-1">Rate Status Remarks</th>
                                        <th class="border-primary border-darken-1">Rates Added By</th>
                                        <th class="border-primary border-darken-1">Rates Added At</th>
                                        <th class="border-primary border-darken-1">Rates Approved By</th>
                                        <th class="border-primary border-darken-1">Rates Approved At</th>
                                        <th class="border-primary border-darken-1">Rates Rejected By</th>
                                        <th class="border-primary border-darken-1">Rates Rejected At</th>
                                        <th class="border-primary border-darken-1">Documents Uploaded At</th>
                                        <th class="border-primary border-darken-1">Documents Approved By</th>
                                        <th class="border-primary border-darken-1">Documents Approved At</th>
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
                                        <th class="border-primary border-darken-1">Referral Code</th>
                                        <th class="border-primary border-darken-1">Payment Cycle</th>
                                        <th class="border-primary border-darken-1">Payment Cycle Days</th>
                                        <th class="border-primary border-darken-1">Lead Account Progress (%)</th>
                                        <th class="border-primary border-darken-1">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div style="display: none;">
                        <form id="account_active_form" action="{{route('admin.accounts.status')}}" method="post" class="mt-2">
                            {{csrf_field()}}
                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" name="shid" id="shid">
                            <input type="hidden" name="status" id="shstatus">
                        </form>
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

    <div class="modal fade" id="corporate_rate_type_modal" data-backdrop="static" role="dialog" aria-labelledby="corporate_rate_type_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="corporate_rate_type_title">Corporate Rate Type</h4>

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
                    <button type="button" id="corporate_rate_type_btn" class="btn btn-success">Submit</button>
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
                                <input type="checkbox" onchange="checkboxStatus()"  id="user_fintech_charges_checkbox" class="switchery restrict_order_id_checkbox" data-size="sm" data-switchery="true">
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
   </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>

    <script>

           
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

            if($('#user_select').val() != '')
            {
                $('#commission_add_button').attr('disabled', false);
            }
            
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

    var kam_count = 0;
    $('#commission_add_button').on('click', function () {
        var is_kam = $('#sales_tier_select').find(":selected").text();
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
            if(is_kam == 'KAM' && kam_count > 0 ){
                var kam_error = 'You Can Select One KAM Only!';
                toastr.error(kam_error, 'Error!', {
                    positionClass: 'toast-top-center',
                    containerId: 'toast-top-center'
                });
            }else{
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
                    if(is_kam == 'KAM'){
                        kam_count+=1;
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
       
        }
    });
    $('#datatable_rate tbody').on('click', 'tr td.action a.remove', function () {
        var id = $(this).parents('tr').attr('id');
        //check if sale tier is KAM
        var rowData = table_2.row($(this).parents('tr')).data();
        var regex = /KAM/;

        if (regex.test(rowData[2])) {
            kam_count-=1;
        } 
        //
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

        $("input[name='search_phone']").inputmask({'mask': "9999-9999999", 'clearIncomplete': true});
        $("input[name='search_cnic']").inputmask({'mask': "99999-9999999-9", 'clearIncomplete': true});
        $('body').on('change','#search_iban',function() {
            $(this).val($(this).val().trim());
        });
        $('#search_admins').select2({
            width:'100%',
            placeholder:"Select Sale Persons",
            allowClear:true,
            dropdownParent:$('#search_form')
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
        $("#bulk_sub_segment1").prepend('<option value="" selected></option>').select2({
            placeholder: "Select Sub Segment",
            width:'100%',
            dropdownParent:$('#set_segments')
        });

        $("#user_select").prepend('<option value="" selected></option>').select2({
            placeholder: "Select Users",
            width:'100%',
            dropdownParent:$('#SalesTierTypeTagModal')
        });

        $('#search_shipper').select2({
            width:'100%',
            placeholder:"Select Shipper",
            allowClear:true,
            multiple: true,
            minimumInputLength: 2,
            ajax: {
                dataType: 'json',
                url:  '{!! route('admin.accounts.shipper_names.dropdown',['type'=>'pending']) !!}',
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

        $('#payment_cycles').prepend('<option value="" selected="selected"></option>').select2({
            width: '100%',
            placeholder: 'Select Payment Cycle',
            dropdownParent:$('#PaymentCycleModal')

        }).on('change', function () {
            $('#msg_limit_days').addClass('d-none')
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
                                    $('#bulk_sub_segment1').append('<option value="' + sub_segment['id'] + '" class="select2">' + sub_segment['name'] + '</option>');
									});

                            }


                });
            });
           

        jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
            if ( this.context.length ) {
                body = [];
                var params = table.ajax.params();
                if(params !== undefined){
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    params['_token'] = "{{csrf_token()}}";
                }
                else{
                    params = {
                        'excel':true,
                        '_token': "{{csrf_token()}}",
                    };
                }

                var jsonResult = $.ajax({
                    url: '{{ route('admin.accounts.pending.ajax') }}',
                    type: "POST",
                    data: params,
                    success: function (result) {
                        head = [];

                        head.push('S.No');
                        head.push('Lead ID');
                        head.push('Account ID');
                        head.push('Account Type');
                        head.push('Company Name');
                        head.push('Contact Person');
                        head.push('Address');
                        head.push('City');
                        head.push('Territory');
                        head.push('Product Type');
                        head.push('Request Date');
                        head.push('Status');
                        head.push('Sales Person Tagged');
                        head.push('POC Tagged');
                        head.push('KAM Tagged');
                        head.push('REF Tagged');
                        // head.push('ESO Tagged');
                        head.push('SMS charges per shipment');
                        head.push('Rate Status');
                        head.push('Rates Status Remarks');
                        head.push('Rates Added By');
                        head.push('Rates Added At');
                        head.push('Rates Approved By');
                        head.push('Rates Approved At');
                        head.push('Rates Rejected By');
                        head.push('Rates Rejected At');
                        head.push('Documents Uploaded At');
                        head.push('Documents Approved By');
                        head.push('Documents Approved At');
                        head.push('Documents Rejected By');
                        head.push('Documents Rejected At');
                        head.push('Documents Status');
                        head.push('Documents Rejection Reason');
                        head.push('FAF Charges Applied');
                        head.push('Duplicate');
                        head.push('International Rate Status');
                        head.push('International Rate Status Remarks');
                        head.push('Segment');
                        head.push('Sub Category Segment');
                        head.push('Referral Code');
                        head.push('Payment Cycle');
                        head.push('Payment Cycle Days');
                        head.push('Lead Account Progress (%)');


                        $.each(result.data, function(index, values) {
                            row = [];


                            row.push(index + 1);
                            row.push(values.lead_id);
                            row.push(values.id);
                            row.push(values.account_type);
                            row.push(values.name);
                            row.push(values.poc);
                            row.push(values.address);
                            row.push(values.city);
                            row.push(values.territory);
                            row.push(values.product_type);
                            row.push(values.created_at);
                            row.push(values.status);
                            row.push(values.admin_tag_id);
                            row.push(values.tagged_poc);
                            row.push(values.kam);
                            row.push(values.ref+' - ' + values.rider_id);
                            // row.push(values.eso);
                            row.push(values.sms_charges);
                            row.push(values.rate_status);
                            row.push(values.rejected_reason);
                            row.push(values.rates_added_by);
                            row.push(values.rates_added_at);
                            row.push(values.rates_authorized_by);
                            row.push(values.rates_approved_at);
                            row.push(values.rates_rejected_by);
                            row.push(values.rates_rejected_at);
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
                            row.push(values.referral_name);
                            row.push(values.payment_cycle);
                            row.push(values.payment_cycle_days);
                            row.push(values.lead_progress);

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
                            text: 'Bulk Segment Tagging',
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
                    /*{
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
                    },*/
                    
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
                    @if (session('role_id') == 1 || in_array(361, session('permissions')))
                    {
                            text: 'Bulk Tagging',
                            className: 'btn btn-primary assign_rider',
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
                                                        table.button('.assign_rider').disable();
                                                        table.button('.bulk_segment_tagging').disable();
                                                        
                                                        table.button('.tag').disable();


                                                    });
                                            } else {
                                                var error = "Rider Not Selected!";
                                                toastr.error(error, 'Error!', {
                                                    positionClass: 'toast-top-center',
                                                    containerId: 'toast-top-center'
                                                });
                                            }
                                        }
                                    });
                                });

                            }else{
                                var error = "Not selected any Rider!";
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
                                var error = "Inappropiate Corporate Type Selected OR Rates Are Rejected !!";
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

                                        table.button('.assign_rider').enable();
                                        table.button('.bulk_segment_tagging').enable();
                                        table.button('.payment_cycle').enable();

                                        table.button('.territory_tag').enable();
                                        table.button('.territory_retag').enable();
                                        table.button('.tag').enable();

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
                                        table.button('.assign_rider').disable();
                                        table.button('.bulk_segment_tagging').disable();
                                        table.button('.payment_cycle').disable();

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
                        title: 'Pending Accounts',
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
            order: [[10, 'desc']],
            ajax: {
                url: '{{ route('admin.accounts.pending.ajax') }}',
                type: "POST",
                data: function (d) {
                    d['_token'] = "{{csrf_token()}}";
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
                {data: 'lead_id_link', name: 'users.lead_id', class: 'align-middle lead_id'},
                {data: 'id_padded', name: 'users.id', class: 'align-middle account_id'},
                {data: 'account_type', name: 'at.name', class: 'align-middle account_type'},
                {data: 'name', name: 'name', class: 'align-middle company_name'},
                {data: 'poc', name: 'poc', class: 'align-middle contact_person'},
                {data: 'address', name: 'users.address', class: 'align-middle address'},
                {data: 'city', name: 'cities.name', class: 'align-middle city'},
                {data: 'territory', name: 't.name', class: 'align-middle territory'},
                {data: 'product_type', name: 'product_type', class: 'align-middle product_type'},
                {data: 'created_at', name: 'created_at', class: 'align-middle created'},
                {data: 'status', name: 'status', class: 'align-middle status'},
                {data: 'admin_tag_id', name: 'ad.name', class: 'align-middle admin_tag_id'},
                {data: 'tagged_poc', name: 'p.name', class: 'align-middle tagged_poc'},
                {data: 'kam', name: 'k.name', class: 'align-middle kam'},
                {data: 'ref', name: 'r.name', class: 'align-middle ref'},
                // {data: 'eso', name: 'e.name', class: 'align-middle eso'},
                {data: 'sms_charges', name: 'users.sms_charges', class: 'align-middle sms_charges'},
                {data: 'rate_status', name: 'users.rate_status', class: 'align-middle rate_status'},
                {data: 'rejected_reason', name: 'users.rejected_reason', class: 'align-middle rejected_reason'},
                {data: 'rates_added_by', name: 'rab.name', class: 'align-middle rates_added_by'},
                {data: 'rates_added_at', name: 'users.rates_added_at', class: 'align-middle rates_added_at'},
                {data: 'rates_authorized_by', name: 'rabb.name', class: 'align-middle rates_authorized_by'},
                {data: 'rates_approved_at', name: 'users.rates_approved_at', class: 'align-middle rates_approved_at'},
                {data: 'rates_rejected_by', name: 'rrb.name', class: 'align-middle rates_rejected_by'},
                {data: 'rates_rejected_at', name: 'users.rates_rejected_at', class: 'align-middle rates_rejected_at'},
                {data: 'documents_uploaded_at', name: 'uda.uploaded_at', class: 'align-middle documents_uploaded_at'},
                {data: 'documents_approved_by', name: 'dab.name', class: 'align-middle documents_approved_by'},
                {data: 'documents_approved_at', name: 'uda.approved_at', class: 'align-middle documents_approved_at'},
                {data: 'documents_rejected_by', name: 'drb.name', class: 'align-middle documents_rejected_by'},
                {data: 'documents_rejected_at', name: 'uda.rejected_at', class: 'align-middle documents_rejected_at'},
                {data: 'documents_status', name: 'users.documents_status', class: 'align-middle documents_status'},
                {data: 'documents_rejection_reason', name: 'users.documents_status_reason', class: 'align-middle documents_rejection_reason'},
                {data: 'fc_status', name: 'faf_charges.status', class: 'align-middle fc_status'},
                {data: 'duplication', name: 'duplication', class: 'align-middle duplicate', orderable: false, searchable: false},
                {data: 'international_rate_status', name: 'international_rate_status', class: 'align-middle international_rate_status', orderable: false, searchable: false},
                {data: 'international_rejected_reason', name: 'international_rejected_reason', class: 'align-middle international_rejected_reason', orderable: false, searchable: false},
                {data: 'segment', name: 'seg.name', class: 'align-middle segment'},
                {data: 'sub_segment', name: 'seg_sub.name', class: 'align-middle sub_segment'},
                {data: 'referral_name', name: 'ref.name', class: 'align-middle referral_name'},
                {data: 'payment_cycle', name: 'pc.id', class: 'align-middle payment_cycle'},
                {data: 'payment_cycle_days', name: 'users.payment_cycle_days', class: 'align-middle payment_cycle_days'},
                {data: 'lead_progress', name: 'lead_progress', class: 'align-middle lead_progress', orderable: false, searchable: false},
                {data: 'action', name: 'action', class: 'align-middle action', orderable: false, searchable: false}
            ],
               rowCallback: function(row, data, index) {
                var info = table.page.info();

                $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                if ($.inArray(data.id, selected_rows) !== -1) {
                    table.row(row).select();
                }
                   
                //    var info = table.page.info();
                //    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
               },
            initComplete: function() {
                var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                var drop_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                    '<option value="0">Request Received</option>' +
                    '<option value="1">Rates Added</option>' +
                    '<option value="2">Pending For Activation</option>' +
                    '<option value="5">Rates Rejected</option>' +
                    '</select>';
                var documents_drop_select = '<select name="documents_status_select" id="documents_status_select" class="select2 form-control">' +
                    '<option value="0">Incomplete</option>' +
                    '<option value="1">Pending for Approval</option>' +
                    '<option value="2">Approved</option>' +
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

                    if ($(header).is('.select') || $(header).is('.action') || $(header).is('.serial_number') || $(header).is('.rate_status') || $(header).is('.duplicate') || $(header).is('.international_rate_status') || $(header).is('.international_rejected_reason') || $(header).is('.lead_progress')) {
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
                    }else if($(header).is('.fc_status')){
                        $(fc_status_drop_select).appendTo($(search))
                            .on( 'change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            } ).wrap(td);
                    }else if ($(header).is('.payment_cycle')) {
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
                $("#status_select").prepend('<option value="" selected></option>').select2({
                    placeholder: "Select a Status",
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
                $("#payment_cycle_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Cycle",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0',
                        allowClear:true,

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
                        table.button('.assign_rider').disable();
                        table.button('.bulk_segment_tagging').disable();
                        
                        table.button('.tag').disable();

                        hub_ids.splice(index, 1);
                    }
                }
            });
            table.draw();
        });
       $('body').on('click','button.active_account',function () {
           var id = $(this).parents('tr').attr('id');
           var status = $(this).attr('rel');
           swal({
               title: 'Are You Sure?',
               text: 'Select Yes to Activate this account!',
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
                $('#account_active_form #shid').val(id);
                $('#account_active_form #shstatus').val(status);
                $('#account_active_form').submit();
               }
           });
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
        $('body').on('click','button.reject_rates',function () {
            var id = $(this).parents('tr').attr('id');
            swal({
                // title: 'Are You Sure?',
                text: 'Write a reason to reject rates!',
                content: {
                    element: "input",
                    attributes: {
                        class: "form-control rate_rejection",
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
                        swal("You have not entered any remarks!", {
                            icon: "warning",
                        });
                    } else {
                        if (id) {
                            $.ajax({
                                url: '{!! route('admin.accounts.rejectreason.submit') !!}',
                                method: 'POST',
                                data: {
                                    'rejected_reason': value,
                                    'shipper_id':id,
                                    '_token': '{{ csrf_token() }}'
                                }
                            })
                            .done(function(data) {
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                table.draw();
                                swal.close();
                            });
                        }
                    }
                }else{
                    swal.close();
                }

            });
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

        var hub_ids = [];
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

        // $("#poc").prepend('<option value="" selected></option>').select2({
        //     placeholder: "Select POC",
        //     width:'100%',
        //     dropdownParent:$('#SalesTierTypeTagModal')
        // });
        // $("#kam").prepend('<option value="" selected></option>').select2({
        //     placeholder: "Select KAM",
        //     width:'100%',
        //     dropdownParent:$('#SalesTierTypeTagModal')
        // });
        // $("#ref").prepend('<option value="" selected></option>').select2({
        //     placeholder: "Select REFFERAL",
        //     width:'100%',
        //     dropdownParent:$('#SalesTierTypeTagModal')
        // });

        // $("#eso").prepend('<option value="" selected></option>').select2({
        //     placeholder: "Select ESO",
        //     width:'100%',
        //     dropdownParent:$('#SalesTierTypeTagModal')
        // });

        
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
                toastr.error('error', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
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
                        if (data.status) {
                            $('#duplicate_modal').modal('show');

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
                                '<td>' + (data.info.shared_email && data.info.shared_email.includes(data.info.email) ?
                                    data.info.email : '') + '</td>' +
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

        var selected_rows_2 = [];

        $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));
                var index = $.inArray(id, selected_rows);
                var index_2 = $.inArray(id, selected_rows_2);

                var dataTable = $('#datatable').DataTable();
                var tr = $(this).closest('tr');
                var row = dataTable.row(tr);
                var rowData = row.data();
                var cond = (rowData.account_type_id == 2 && rowData.corporate_rate_type_id == 3 && rowData.status_id != 2) ||
               (rowData.account_type_id == 1 && rowData.status_id != 2);
               
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
                    table.button('.assign_rider').enable();
                    table.button('.bulk_segment_tagging').enable();
                    table.button('.tag').enable();
                    table.button('.payment_cycle').enable();

                    table.button('.territory_tag').enable();
                    table.button('.territory_retag').enable();
                }
                else {
                    table.button('.assign_rider').disable();
                    table.button('.bulk_segment_tagging').disable();
                    table.button('.tag').disable();
                    table.button('.payment_cycle').disable();

                    table.button('.territory_tag').disable();
                    table.button('.territory_retag').disable();
                }
        });

        $("#corporate_rate_type_select").prepend('<option value="" selected></option>').select2({
            placeholder: "Select Corporate Rate Type",
            width:'100%',
            dropdownParent:$('#corporate_rate_type_modal')
        });

        $('body').on('click', 'button.rate_type',  function(){
            var id = $(this).parents('tr').attr('id');
            if(id){
                $('#corporate_rate_type_modal').modal('show');
                $('#corporate_rate_type_shipper_id').val(id);
            }
        });

        $('#corporate_rate_type_btn').on('click',function () {
            var shipper = parseInt($('#corporate_rate_type_shipper_id').val());
            var rate_type = parseInt($('#corporate_rate_type_select').val());
            if(rate_type){
                $.ajax({
                    url: '{!! route('admin.accounts.rate_type.submit') !!}',
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

                        }
                        else {
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                        }
                        $('#corporate_rate_type_select').val('').trigger('change');
                        $('#corporate_rate_type_modal').modal('hide');
                        table.draw(true);
                    });
            }else{
                var error = "Rate Type Not Selected!";
                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
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
        $( "#set_retag_territory" ).validate({
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


         $('#Save_fintech_charges input.fuel_factor').inputmask({
            'alias': 'integer',
            'allowMinus': true,
            'allowPlus': false,
            'max':100
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
                            }
                            $('#faf_charges_user_id').val(id);
                            $('#faf_charges_modal').modal('show');


                        });
                }
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

        $('.close').on('click',function(){
            $('#territory').val('').trigger('change');
        })

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
                var monthly = formData[5]['value'];
                var selected_days = [];

                if (formData[6] && formData[6]['value']) {
                    var splitValues = formData[6]['value'].split(',');
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

    $('#payment_cycles').on('change', function() {
        
            $("#checkboxContainer input[type='checkbox']").prop('checked', false);
            $('#msg_payment').addClass("d-none");
            selectedValues = [];
            $('#payment_cycle_msg').text('')
            var id = $(this).val();
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

