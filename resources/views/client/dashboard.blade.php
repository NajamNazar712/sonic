@extends('client.layout.master')

@section('title', 'Dashboard')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <div class="row">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <h1>Order Details</h1>
                                @include('client.inc.messages')
                                <div class="col mt-2">
                                    <form id="track_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">

                                        <div class="col-3">
                                           <div class="form-group">
                                               <input type="text" name="tracking_numbers" class="tracking_numbers" placeholder="Tracking Number(s)*" data-tags-input-name="tracking_number" data-rule-required="true" data-msg-required="Tracking Number is required">
                                           </div>
                                       </div>

                                        <div class="col-auto">
                                            <div class="form-group">
                                                <input type="text" name="phone_number" class="form-control phone_number" placeholder="Phone Number (Full)">
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group input-group">
                                                <div class="input-group-prepend">
                                      <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o small-calender-icon"></span>
                                      </span>
                                                </div>
                                                <input type="text" name="booking_from_date" class="form-control bg-primary border-primary white rounded-right" id="booking_from_date" placeholder="Booking Date From"  data-value="{{ \Carbon\Carbon::today()->subDays(31)->startOfDay() }}">
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group input-group">
                                                <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                            <span class="la la-calendar-o small-calender-icon"></span>
                                        </span>
                                                </div>
                                                <input type="text" name="booking_to_date" class="form-control bg-primary border-primary white rounded-right" id="booking_to_date" placeholder="Booking Date To" data-value="{{ \Carbon\Carbon::now() }}">
                                            </div>
                                        </div>

                                        <div class="form-group col-md-3 mt-2 justify-content-center">
                                            <button type="submit" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                                        </div>
                                    </form>
                                </div>
                                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                    <thead>
                                    <tr role="row" class="bg-primary white">
                                        <th class="border-primary border-darken-1"></th>
                                        <th class="border-primary border-darken-1">S No.</th>
                                        <th class="border-primary border-darken-1">Shipment ID</th>
                                        <th class="border-primary border-darken-1">Tracking No.</th>
                                        <th class="border-primary border-darken-1">Business Category</th>
                                        <th class="border-primary border-darken-1">Order ID</th>
                                        <th class="border-primary border-darken-1">Shipper</th>
                                        <th class="border-primary border-darken-1">Booked By</th>
                                        <th class="border-primary border-darken-1">Service Type</th>
                                        <th class="border-primary border-darken-1">Status</th>
                                        <th class="border-primary border-darken-1">Reason</th>
                                        <th class="border-primary border-darken-1">Payment Status</th>
                                        <th class="border-primary border-darken-1">Origin</th>
                                        <th class="border-primary border-darken-1">Destination</th>
                                        <th class="border-primary border-darken-1">Consignee Name</th>
                                        <th class="border-primary border-darken-1">Consignee Contact</th>
                                        <th class="border-primary border-darken-1">Consignee Address</th>
                                        <th class="border-primary border-darken-1">Collection Amount</th>
                                        <th class="border-primary border-darken-1">Booking Date</th>
                                        <th class="border-primary border-darken-1">Instructions</th>
                                        <th class="border-primary border-darken-1">Cancellation Remarks</th>
                                        <th class="border-primary border-darken-1">Payment Mode</th>
{{--                                        <th class="border-primary border-darken-1">Payment Mode</th>--}}
                                        <th class="border-primary border-darken-1"></th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!--Shipment Charges Modal -->
    <div class="modal fade text-left" id="ShipmentChargesModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ShipmentChargesModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="shipment_charges_modal_heading">Shipment Charges of #<span></span></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <input type="hidden" id="shipment_charges_modal_id">
                <div class="modal-body shipment_charges_body text-center" id="shipment_charges_body">
                </div>
            </div>
        </div>
    </div>

    <!--Shipment Charges Modal -->
    <!--Dispute Modal -->
    <div class="modal fade text-left" id="DisputeModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="DisputeModal"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Launch Dispute</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body  text-center">
                    <form id="dispute_form" action="" method="post">
                        <input type="hidden" id="dispute_shipment_id" name="dispute_shipment_id">
                        <div class="row mb-2">
                            <div class="col-12 form-group">
                                <select name="city_select" id="city_select" class="select2 form-control" style="width:100%;" data-rule-required="true" data-msg-required="This field is required">
                                    <option></option>
                                    @foreach($cities as $city)
                                        <option value="{{$city->id}}">{{$city->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 form-group">
                                <select name="dispute_type_select" id="dispute_type_select" class="select2 form-control" style="width:100%;" data-rule-required="true" data-msg-required="This field is required">
                                    <option></option>
                                    @foreach($dispute_types as $dispute)
                                        <option value="{{$dispute->id}}">{{$dispute->type}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2 justify-content-center">
                            <div class="col-12 form-group">
                                <input name="tracking_number" id="tracking_number" class="tracking_number" data-tags-input-name="tracking_number" data-rule-required="true" data-msg-required="Tracking Number is required">
                            </div>
                        </div>
                        <div class="row mb-2 justify-content-center">
                            <div class="col-12 form-group">
                                <textarea name="description" id="description" class="form-control" cols="30" rows="3" placeholder="Enter Description" data-rule-required="true" data-msg-required="This field is required"></textarea>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <button id="DisputeCreate" type="submit" class="btn btn-primary btn-block">Launch Dispute</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Dispute Modal -->
    <div class="modal fade text-left" id="AddRequestModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddRequestModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Request</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_request_form" method="post">
                        @method('POST')
                        @csrf
                        <div class="container">
                            <div class="row">
                                <h2 class="heading">Tracking Number(s)</h2>
                            </div>

                            <input type="hidden" id="requested_shipment_ids">
                            <div class="row old_scroll" id="requested_shipments">

                            </div>
                            <hr>
                            <div class="row justify-content-center">
                                <div class="col-8">
                                    <fieldset class="form-group">
                                        <select name="case_nature_select" id="case_nature_select" class="form-control select2" data-rule-required="true" data-msg-required="Case Nature is required">
                                            @foreach($case_nature as $nature)
                                                <option value="{{$nature->id}}">{{$nature->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="complaints d-none" id="request_complaints">
                                <div class="row justify-content-center">
                                    <div class="col-8">
                                        <fieldset class="form-group">
                                            <select name="case_nature_complaint" id="case_nature_complaints" class="form-control select2" data-rule-required="true" data-msg-required="Complaint Type is required">
                                                @foreach($case_nature_complaints as $complaints)
                                                    <option value="{{$complaints->id}}">{{$complaints->type}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-8">
                                        <fieldset class="form-group">
                                            <textarea class="form-control" name="complaint_description" id="complaint_description" rows="5" placeholder="Enter Description Here..." data-rule-required="true" data-msg-required="Description is required"></textarea>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <div class="service d-none" id="request_service">
                                <div class="row justify-content-center">
                                    <div class="col-8">
                                        <fieldset class="form-group">
                                            <select name="case_nature_request" id="case_nature_requests" class="form-control select2" data-rule-required="true" data-msg-required="Complaint Type is required">
                                                @foreach($case_nature_service_requests as $service)
                                                    <option value="{{$service->id}}">{{$service->type}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-8 d-none" id="alternate_phone_input">
                                        <fieldset class="form-group">
                                            <input type="text" name="alternate_phone" class="form-control" id="alternate_phone" placeholder="Enter Alternate Number" data-rule-required="true" data-msg-required="Alternate Number is required">
                                        </fieldset>
                                    </div>
                                    {{-- <div class="col-8 d-none" id="cod_amount_input">
                                        <fieldset class="form-group">
                                            <input type="text" name="cod_amount" class="form-control" id="cod_amount" placeholder="Enter COD Amount" data-rule-required="true" data-msg-required="COD Amount is required">
                                        </fieldset>
                                    </div> --}}

                                    <div class="col-8">
                                        <fieldset class="form-group">
                                            <textarea class="form-control" name="service_description" id="service_description" rows="5" placeholder="Enter Description Here..." data-rule-required="true" data-msg-required="Description is required"></textarea>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <div class="feedback d-none" id="request_feedback">
                                <div class="row justify-content-center">
                                    <div class="col-8">
                                        <fieldset class="form-group">
                                            <textarea class="form-control" name="feedback_description_request" id="feedback_description_request" rows="5" placeholder="Enter Description Here..." data-rule-required="true" data-msg-required="Description is required"></textarea>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <div class="claims d-none" id="request_claims">
                                <input type="hidden" name="shipment_ids" id="shipment_ids">
                                <input type="hidden" name="case_nature_id" id="case_nature_id">
                                <input type="hidden" name="complaint_id" id="complaint_id">
                                <div class="row justify-content-center">
                                    <div class="col-8">
                                        <fieldset class="form-group">
                                            <select name="case_nature_tclaim" id="case_nature_claim" class="form-control select2">
                                                @foreach($case_nature_type_claims as $claim)
                                                    <option value="{{$claim->id}}">{{$claim->type}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-8" id="claim_product_cost_div">
                                        <fieldset class="form-group">
                                            <input class="form-control" name="claim_product_cost" id="claim_product_cost" value="" placeholder="Enter Product Cost">
                                        </fieldset>
                                    </div>
                                    <div class="col-8 d-none" id="receiving_sheet_div">
                                        <fieldset class="form-group">
                                            {{--                                            <select name="receiving_sheet_id"  id="request_id" class="form-control select2" data-rule-required="true" data-msg-required="Please Select Receiving Sheet">--}}
                                            <select name="receiving_sheet_id"  id="request_id" class="form-control select2">

                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-8 text-left" id="claim_product_picture_div">
                                        <fieldset class="form-group">
                                            <label for="product_picture"><b>Product Picture:</b></label>
                                            <input class="form-control form-control-sm" type="file" name="product_picture" id="product_picture" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">
                                        </fieldset>
                                    </div>

                                    <div class="col-8 text-left" id="claim_invoice_picture_div">
                                        <fieldset class="form-group">
                                            <label for="invoice_picture"><b>Invoice Picture:</b></label>
                                            <input class="form-control form-control-sm" type="file" name="invoice_picture" id="invoice_picture" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">
                                        </fieldset>
                                    </div>

                                    <div class="col-8 text-left d-none" id="claim_shipment_damage_div">
                                        <fieldset class="form-group">
                                            <label for="damage_product_picture"><b>Damage Picture:</b></label>
                                            <input class="form-control form-control-sm" type="file" name="damage_product_picture" id="damage_product_picture" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">
                                        </fieldset>
                                        <fieldset class="form-group">
                                            <label for="product_packaging_picture"><b>Product Packaging Picture:</b></label>
                                            <input class="form-control form-control-sm" type="file" name="product_packaging_picture" id="product_packaging_picture" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">
                                        </fieldset>
                                        <fieldset class="form-group">
                                            <label for="actual_product_picture"><b>Actual Product Picture:</b></label>
                                            <input class="form-control form-control-sm" type="file" name="actual_product_picture" id="actual_product_picture" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">
                                        </fieldset>
                                        {{--                                        <div class="col-8" id="damage_claim_product_cost_div">--}}
                                        <fieldset class="form-group">
                                            <input class="form-control" name="damage_claim_product_cost" id="damage_claim_product_cost" value="" placeholder="Enter Actual Damaged Product Price">
                                        </fieldset>
                                        {{--                                        </div>--}}
                                    </div>

                                    <div class="col-8 text-left d-none" id="claim_content_short_div">
                                        <fieldset class="form-group">
                                            <label for="missing_product_picture"><b>Missing Product Picture:</b></label>
                                            <input class="form-control form-control-sm" type="file" name="missing_product_picture" id="missing_product_picture" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">
                                        </fieldset>
                                        <fieldset class="form-group">
                                            <label for="product_packaging_picture_content_short"><b>Product Packaging Picture:</b></label>
                                            <input class="form-control form-control-sm" type="file" name="product_packaging_picture_content_short" id="product_packaging_picture_content_short" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">
                                        </fieldset>
                                        <fieldset class="form-group">
                                            <label for="actual_product_picture_content_short"><b>Actual Product Picture:</b></label>
                                            <input class="form-control form-control-sm" type="file" name="actual_product_picture_content_short" id="actual_product_picture_content_short" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">
                                        </fieldset>
                                        <fieldset class="form-group">
                                            <input class="form-control" name="claim_content_product_cost" id="claim_content_product_cost" value="" placeholder="Enter Actual Missing Product Price">
                                        </fieldset>
                                    </div>

                                    <div class="col-8">
                                        <fieldset class="form-group">
                                            <textarea class="form-control" name="description" id="claim_description" rows="5" placeholder="Enter Description Here..."></textarea>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <button id="AddNewRequest" type="submit" class="btn btn-primary btn-block d-none">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{--Request modal end   --}}
    <div class="modal fade text-left" id="AddFeedbackModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddFeedbackModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Feedback</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_feedback_form" action="#" method="post">
                        @method('POST')
                        @csrf
                        <div class="container">

                            <div class="row justify-content-center">

                                <div class="col-8">
                                    <fieldset class="form-group">
                                        <textarea class="form-control" name="feedback_description" id="feedback_description" rows="5" placeholder="Enter Description Here..."></textarea>
                                    </fieldset>
                                </div>
                            </div>


                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <button id="AddNewFeedback" type="submit" class="btn btn-primary btn-block">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{--Request modal end   --}}
    <div class="modal fade text-left" id="ConsolidateModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ConsolidateModal"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Consolidate Shipments</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="consolidate_shipments_form" class="form-horizontal" method="POST" action="{{ route('cod.orders.consolidate.submit') }}" novalidate="novalidate" enctype="multipart/form-data">
                        @method('POST')
                        @csrf
                        <input type="hidden" id="consolidate_shipment_ids" name="shipment_ids">


                        <div id="consolidate_shipment_table" class="row justify-content-center">
                            <div class="col-lg-12 consolidate_shipment_table">

                            </div>

                        </div>


                        <div class="row justify-content-center">
                            <div class="col-3 mt-1">
                                <button id="AddConsolidateShipments" type="submit" class="btn btn-primary btn-block">Consolidate</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="CancelReasonModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="CancelReasonModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Cancel Reason</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">

                    <textarea type="text" rows="5" class="form-control" cols="50" id="cancel_reason" name="cancel_reason" placeholder="Enter Reason"></textarea>


                    <div class="row justify-content-center">
                        <div class="col-3 mt-1">
                            <button id="CancelReasonSubmit" type="button" class="btn btn-primary btn-block">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade text-left" id="BulkCancelModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="BulkCancelModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Cancel Reason</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <input type="hidden" name="cancel_shipment_id" id="cancel_shipment_id" value="">
                    <textarea type="text" rows="5" class="form-control" cols="50" id="bulk_cancel_reason" name="cancel_reason" placeholder="Enter Reason"></textarea>


                    <div class="row justify-content-center">
                        <div class="col-3 mt-1">
                            <button id="BulkCancelSubmit" type="button" class="btn btn-primary btn-block">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/line-awesome/css/line-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/cryptocoins/cryptocoins.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">

    <style type="text/css">
        .small-calender-icon{
            font-size: 17px !important;
        }

        .bg-gradient-directional-inprocess {
            background-image: linear-gradient(45deg, #d6a42a, #ffec07fa);
            background-repeat: repeat-x;
        }
        .selectize-control {
            width: 305px !important;
        }
        .align-bottom{
            vertical-align: bottom;
        }
        .old_scroll{
            overflow-y: auto;
            max-height: 100px;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}?v=24052022" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pagination/moment.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>



    <script type="text/javascript">
        $(document).ready(function () {

            $('#track_form .phone_number').inputmask({
                'mask': '9999-9999999',
                'clearIncomplete': true
            });
            
            $('#claim_product_cost').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 2,
                'min': 0.00,
                'max': 1000000.00
            });
            $('#damage_claim_product_cost').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 2,
                'min': 0.00,
                'max': 1000000.00
            });
            $('#claim_content_product_cost').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 2,
                'min': 0.00,
                'max': 1000000.00
            });

            $('#alternate_phone').inputmask({
                'mask': '9999-9999999',
                'clearIncomplete': true
            });
            // $('#cod_amount').inputmask({
            //     'alias': 'integer',
            //     'allowMinus': false,
            //     'allowPlus': false,
            //     'rightAlign': false,
            //     'digits': 2,
            //     'min': 0,
            //     'max': 1000000
            // });

            var booking_from_date = $('#booking_from_date').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {

                    var old_date_formatted = $('input[name="booking_from_date_formatted"]').val();
                    var contractMoment = moment(old_date_formatted);
                    var current = moment(contractMoment).add(31, 'days');
                    booking_to_date.pickadate('picker').set('min', new Date(old_date_formatted),{muted:true});
                    booking_to_date.pickadate('picker').set('max', new Date(current.toDate()),{muted:true});
                    booking_to_date.pickadate('picker').set('select', new Date(current.toDate()),{muted:true});
                }
            });

            var booking_to_date = $('#booking_to_date').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    // if (context.select) {
                    //     $('#track_form #booking_from_date').pickadate('picker').set('max', $('#track_form #booking_to_date').pickadate('picker').get('select'));
                    // }
                }
            });

            function print(selected_rows) {

                $.ajax({
                    url: '{!! route('cod.shipment.book.shipment_check') !!}',
                    method: 'POST',
                    data: {
                        'ids[]': selected_rows,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function (data) {
                        if(data.status === 1) {
                            if (data.sticker) {
                                $.ajax({
                                    url: '{!! route('cod.shipment.book.print_air_waybill') !!}',
                                    xhrFields: {
                                        responseType: 'blob'
                                    },
                                    method: 'POST',
                                    data: {
                                        'ids[]': data.ids,
                                        'sticker': 1,
                                        '_token': '{{ csrf_token() }}'
                                    }
                                })
                                    .done(function(data) {
                                        var blob = new Blob([data]);
                                        var link = document.createElement('a');
                                        link.href = window.URL.createObjectURL(blob);
                                        link.download = 'air_waybills.pdf';
                                        link.click();
                                    });
                            }
                            else {
                                $.ajax({
                                    url: '{!! route('cod.shipment.book.print_air_waybill') !!}',
                                    method: 'POST',
                                    data: {
                                        'ids[]': data.ids,
                                        'sticker': 0,
                                        '_token': '{{ csrf_token() }}'
                                    }
                                })
                                    .done(function (data) {

                                        var tab = window.open('', '_blank');

                                        if (!tab) {
                                            swal({
                                                title: 'Popup Blocker Enabled!',
                                                text: 'Please add this site to your exception list.',
                                                icon: 'error',
                                                closeOnClickOutside: false,
                                                closeOnEsc: false
                                            });
                                        } else {
                                            tab.document.write(data);
                                            tab.document.close();
                                            tab.focus();
                                        }
                                    });
                            }
                        }
                    });
            }

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    blockPagePermanently();
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('cod.orders.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Tracking No.');
                            head.push('Business Category');
                            head.push('Order ID');
                            head.push('Shipper');
                            head.push('Booked By');
                            head.push('Service Type');
                            head.push('Status');
                            head.push('Reason');
                            head.push('Payment Status');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Consignee Name');
                            head.push('Consignee Contact');
                            head.push('Consignee Address');
                            head.push('Collection Amount');
                            head.push('Booking Date');
                            head.push('Instructions');
                            head.push('Cancellation Remarks');
                            head.push('Payment Mode');
                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.tracking);
                                row.push(values.business_category);
                                row.push(values.order_id);
                                row.push(values.user_name);
                                row.push(values.booked_by);
                                row.push(values.service_type);
                                row.push(values.status);
                                row.push(values.reason);
                                row.push(values.payment_status);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.consignee_name);
                                row.push(values.phone);
                                row.push(values.consignee_address);
                                row.push(values.amount);
                                row.push(values.booking_date);
                                row.push(values.instructions);
                                row.push(values.cancellation_remarks);
                                row.push(values.payment_module);
                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header: head};
                }
            } );

            var selected_rows = [];
            var table = $('#datatable').DataTable({
                scrollX: true, scrollY: '500px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Order Details',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },

                        {{--{--}}
                        {{--    text: '<i class="la la-plus"></i> Consolidate',--}}
                        {{--    className: 'btn btn-primary consolidate',--}}
                        {{--    enabled: false,--}}
                        {{--    action: function (e, dt, node, config) {--}}
                        {{--        if (selected_rows.length > 1) {--}}
                        {{--            $.ajax({--}}
                        {{--                url: '{!! route('cod.orders.consolidate.shipment_info') !!}',--}}
                        {{--                method: 'POST',--}}
                        {{--                data: {--}}
                        {{--                    'shipment_ids': selected_rows,--}}
                        {{--                    '_token': '{{ csrf_token() }}'--}}
                        {{--                }--}}
                        {{--            }).done(function (data) {--}}
                        {{--                if (data.status === 1) {--}}
                        {{--                    $('#consolidate_shipment_ids').val(selected_rows);--}}
                        {{--                    var route = '{!! route('cod.tracking.index') !!}';--}}
                        {{--                    var html = '';--}}
                        {{--                    html += '<table class="table datatable text-center">';--}}
                        {{--                    html += '<thead><tr><th>S No.</th><th><strong>Tracking No</strong></th><th><strong>Origin</strong></th><th><strong>Destination</strong><th><strong>Consignee Name & Phone</strong></th><th><strong>Consignee Address</strong></th><th><strong>COD Amount</strong></th><th><strong>Product Type</strong></th><th><strong>Booking Date</strong></th><th><strong>Action</strong></th></tr></thead>';--}}
                        {{--                    html += '<tbody>';--}}
                        {{--                    $.each(data.shipment_info, function(index, value) {--}}
                        {{--                        var ind = index+1;--}}
                        {{--                        html += '<tr class=""><td>' + ind + '</td>';--}}
                        {{--                        html += '<td><u><a href='+route+'?tracking_number='+value.tracking_number+' target="_blank">'+value.tracking_number+'</a></u></td>';--}}
                        {{--                        // if(value.order_id != null){--}}
                        {{--                        //     html += '<td>' + value.order_id + '</td>';--}}
                        {{--                        // } else{--}}
                        {{--                        //     html += '<td>-</td>';--}}
                        {{--                        // }--}}
                        {{--                        html += '<td>' + value.origin + '</td>';--}}
                        {{--                        html += '<td>' + value.destination + '</td>';--}}
                        {{--                        html += '<td>' + value.consignee_name + ' | ' + value.consignee_phone_number_1 + '</td>';--}}
                        {{--                        // html += '<td>' + value.consignee_phone_number_1 + '</td>';--}}
                        {{--                        html += '<td>' + value.consignee_address + '</td>';--}}
                        {{--                        html += '<td>' + value.amount + '</td>';--}}
                        {{--                        html += '<td>' + value.product_type + '</td>';--}}
                        {{--                        html += '<td>' + value.created_at + '</td>';--}}
                        {{--                        if(index == 0){--}}
                        {{--                            html += '<td><input type="radio" name="default-radio" class="icheck dradio" id="default-radio" value="' + value.id + '" checked="checked"><label for="default-radio">Make Default</label></td></tr>';--}}
                        {{--                        }--}}
                        {{--                        else{--}}
                        {{--                            html += '<td><input type="radio" name="default-radio" class="icheck dradio" id="default-radio" value="' + value.id + '"><label for="default-radio">Make Default</label></td></tr>';--}}
                        {{--                        }--}}
                        {{--                    });--}}
                        {{--                    html += '</tbody></table>';--}}

                        {{--                    $('#ConsolidateModal .modal-body .row .consolidate_shipment_table').html(html);--}}
                        {{--                    $('#ConsolidateModal').modal('show');--}}
                        {{--                } else if(data.status === 2){--}}
                        {{--                        var consolidated_html = '';--}}

                        {{--                        $.each(data.consolidated_Shipments, function(index, tracking_number) {--}}
                        {{--                            consolidated_html += tracking_number + '<br/>';--}}
                        {{--                        });--}}

                        {{--                        consolidated_html += '<br/>Above Shipment(s) are already Consolidated!';--}}

                        {{--                        content = document.createElement('div');--}}
                        {{--                        content.innerHTML = consolidated_html;--}}

                        {{--                        swal({--}}
                        {{--                            title: 'Already Consolidated',--}}
                        {{--                            content: content,--}}
                        {{--                            icon: 'warning',--}}
                        {{--                            buttons: {--}}
                        {{--                                cancel: {--}}
                        {{--                                    text: 'Close',--}}
                        {{--                                    value: null,--}}
                        {{--                                    visible: true,--}}
                        {{--                                    closeModal: true,--}}
                        {{--                                },--}}
                        {{--                            },--}}
                        {{--                            closeOnClickOutside: false,--}}
                        {{--                            closeOnEsc: false,--}}
                        {{--                            dangerMode: true--}}
                        {{--                        });--}}
                        {{--                } else{--}}
                        {{--                    toastr.error(data.error, 'Error!', {--}}
                        {{--                        positionClass: 'toast-top-center',--}}
                        {{--                        containerId: 'toast-top-center'--}}
                        {{--                    });--}}
                        {{--                }--}}
                        {{--            });--}}
                        {{--        }--}}
                        {{--        else{--}}
                        {{--            var error = 'Select at least two shipments to Consolidate';--}}
                        {{--            toastr.error(error, 'Error!', {--}}
                        {{--                positionClass: 'toast-top-center',--}}
                        {{--                containerId: 'toast-top-center'--}}
                        {{--            });--}}
                        {{--        }--}}
                        {{--    }--}}
                        {{--},--}}
                    {
                        text: '<i class="la la-print"></i> Print',
                        className: 'btn btn-primary print',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            table.button(0).disable();
                            table.button(1).disable();
                            print(selected_rows);
                            table.rows().deselect();
                            selected_rows = [];
                        }
                    }, {
                        text: '<i class="la la-cancel"></i> Cancel',
                        className: 'btn btn-danger cancel',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            $('#BulkCancelModal').modal('show');
                        }
                    },
                    {
                        text: '<i class="la la-plus"></i> Add Request',
                        className: 'btn btn-primary request_add',
                        action: function (e, dt, node, config) {
                            if (selected_rows.length > 0) {
                                $('#AddRequestModal').modal('show');
                                $('#requested_shipment_ids').val(selected_rows);
                                var html_rows = '';
                                var count = 1;
                                table.rows().nodes().each(function (index) {
                                    var row = table.row(index);
                                    if ($(row.node()).hasClass('selected')) {
                                        var tracking = $(row.node()).find('td.tracking_number').text();
                                        html_rows += '<div class="col-4"><span class="mr-1"><i class="la la-angle-right align-bottom"></i><b> ' + tracking + '</b></span></div>';
                                        count++;
                                    }
                                });
                                $('#requested_shipments').html(html_rows);
                            }
                            else{
                                $('#AddFeedbackModal').modal('show');
                            }
                        }
                    },
                    {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action: function (e) {
                            e.preventDefault();

                            table.rows().nodes().each(function (index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.select();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index === -1) {
                                        selected_rows.push(id);
                                    }

                                    table.button('.print').enable();
                                    table.button('.cancel').enable();
                                    table.button('.consolidate').enable();

                                }
                            });


                        }
                    },
                    {
                        extend: 'selectNone',
                        text: 'Select None',
                        className: 'select_none',
                        action: function (e) {
                            e.preventDefault();

                            table.rows().nodes().each(function (index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.deselect();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index !== -1) {
                                        selected_rows.splice(index, 1);
                                    }

                                    if (selected_rows.length == 0) {
                                        table.button('.print').disable();
                                        table.button('.cancel').disable();
                                        table.button('.consolidate').disable();

                                    }
                                }
                            });
                        }
                    }
                ],

                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                lengthMenu: [[10, 50, 100, 500, 1000], [10, 50, 100, 500, 1000]],
                pageLength: 10,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                deferLoading: 0,
                ajax: {
                    url: '{{ route('cod.orders.list') }}',
                    data: function (d) {
                        d.tracking_numbers = $('#track_form .tracking_numbers').val();
                        d.booking_from_date = $('input[name="booking_from_date_formatted"]').val();
                        d.booking_to_date = $('input[name="booking_to_date_formatted"]').val();
                        d.phone_number = $('input[name="phone_number"]').val();
                    }
                },
                rowId: 'shipment_id',
                order: [[2, 'desc']],

                columns: [
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        class: 'text-center align-middle select p-1',
                        targets: 0,
                        render: function (data, type, row) {
                            return '';
                        }
                    },
                    {
                        orderable: false,
                        searchable: false,
                        name: 'serial_number',
                        class: 'align-middle serial_number',
                        targets: 0,
                        render: function (data, type, row) {
                            return '';
                        }
                    },
                    {data: 'shipment_id', name: 'shipments.id', visible: false},
                    {data: 'tracking_number', name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'business_category', name: 'bc.id', class: 'align-middle business_category'},
                    {data: 'order_id', name: 'shipments.order_id', class: 'align-middle order_id'},
                    {data: 'user_name', name: 'u.name', class: 'align-middle user_name'},
                    {data: 'booked_by', name: 'shipments.booked_by', class: 'align-middle booked_by'},
                    {data: 'service_type', name: 'bt.id', class: 'align-middle service_type'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'reason', name: 'ssr.name', class: 'align-middle reason'},
                    {data: 'payment_status', name: 'payment_status', class: 'align-middle payment_status'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'consignee_name', name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                    {data: 'phone', name: 'phone', class: 'align-middle phone'},
                    {
                        data: 'consignee_address',
                        name: 'shipments.consignee_address',
                        class: 'align-middle consignee_address'
                    },
                    {data: 'amount', name: 'shipments.amount', class: 'align-middle amount'},
                    {data: 'booking_date', name: 'shipments.created_at', class: 'align-middle booking_date'},
                    {data: 'instructions', name: 'shipments.special_instructions', class: 'align-middle instructions'},
                    {
                        data: 'cancellation_remarks',
                        name: 'shipments_journey.remarks',
                        class: 'align-middle cancellation_remarks'
                    },
                    {
                        data: 'payment_module',
                        name: 'shipments.payment_mode_id',
                        class: 'align-middle payment_module'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        class: 'text-center align-middle action p-1',
                        orderable: false,
                        searchable: false
                    }

                ],

            rowCallback: function (row, data, index) {
                    var info = table.page.info();
                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);

                    $('td:eq(0)', row).addClass('select-checkbox');

                    if ($.inArray(data.shipment_id, selected_rows) !== -1) {
                        table.row(row).select();
                    }

                },
                initComplete: function () {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '</select>';
                    var payment_select = '<select name="payment_select" id="payment_select" class="select2 form-control"></select>';
                    var payment_mode ='<select name="payment_mode" id="payment_mode" class="select2 form-control"></select>';
                    var service_drop_select = '<select name="service_select" id="service_select" class="select2 form-control"></select>';
                    var product_select = '<select name="product_select" id="product_select" class="select2 form-control"></select>';
                    var business_category = '<select name="business_category" id="business_category" class="select2 form-control"></select>';
                    var user_select = '<select name="user_select" id="user_select" class="select2 form-control">' +
                        '<option value="1">Main User</option>' +
                        '<option value="2">Substitute User</option>' +
                        '</select>';

                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();

                        if (column.visible()) {
                            if ($(header).is('.action') || $(header).is('.serial_number') || $(header).is('.select')) {
                                $(td).appendTo($(search));
                            } else if ($(header).is('.status')) {
                                $(status_select).appendTo($(search))
                                    .on('change', function () {
                                        column.search($(this).val(), false, false, true).draw();
                                    }).wrap(td);
                            } else if ($(header).is('.payment_status')) {
                                $(payment_select).appendTo($(search))
                                    .on('change', function () {
                                        column.search($(this).val(), false, false, true).draw();
                                    }).wrap(td);
                            } else if ($(header).is('.payment_module')) {
                                    $(payment_mode).appendTo($(search))
                                        .on('change', function () {
                                            column.search($(this).val(), false, false, true).draw();
                                        }).wrap(td);
                            }  else if ($(header).is('.business_category')) {
                                $(business_category).appendTo($(search))
                                    .on('change', function () {
                                        column.search($(this).val(), false, false, true).draw();
                                    }).wrap(td);
                            } else if ($(header).is('.service_type')) {
                                $(service_drop_select).appendTo($(search))
                                    .on('change', function () {
                                        console.log($(this).val())
                                        column.search($(this).val(), false, false, true).draw();
                                    }).wrap(td);
                            } else if ($(header).is('.product_type')) {
                                $(product_select).appendTo($(search))
                                    .on('change', function () {
                                        column.search($(this).val(), false, false, true).draw();
                                    }).wrap(td);
                            } else if ($(header).is('.booked_by')) {
                                $(user_select).appendTo($(search))
                                    .on('change', function () {
                                        column.search($(this).val(), false, false, true).draw();
                                    }).wrap(td);
                            }
                            else {
                                var current = $(input).appendTo($(search)).on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td).after(icon);

                                if (column.search()) {
                                    current.val(column.search());
                                }
                            }
                        }
                    });
                    $("#user_select").prepend('<option value="" selected></option>').select2({
                        data: data,
                        placeholder: "Select User",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data = $.map({!! $shipment_status !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                    var data = $.map({!! $shipment_status !!}, function (obj) {
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
                    var data2 = $.map({!! $service_type !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                    var data2 = $.map({!! $service_type !!}, function (obj) {
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
                    var data3 = $.map({!! $products !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                    var data3 = $.map({!! $products !!}, function (obj) {
                        obj.text = obj.product_name;

                        return obj;
                    });

                    $("#product_select").prepend('<option value="" selected></option>').select2({
                        data: data3,
                        placeholder: "Select Product",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data4 = $.map({!! $payment_status !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                    var data4 = $.map({!! $payment_status !!}, function (obj) {
                        obj.text = obj.name;

                        return obj;
                    });
                    $("#payment_select").prepend('<option value="" selected></option>').select2({
                        data: data4,
                        placeholder: "Select Payment",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    var data6 = $.map({!! $payment_module !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.mode;

                        return obj;
                    });
                    $("#payment_mode").prepend('<option value="" selected></option>').select2({
                        data: data6,
                        placeholder: "Select Payment Mode",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data5 = $.map({!! $business_categories !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;

                        return obj;
                    });
                    $("#business_category").prepend('<option value="" selected></option>').select2({
                        data: data5,
                        placeholder: "Select Business Category",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });

            $('#BulkCancelSubmit').click(function () {
                var reason = $('#bulk_cancel_reason').val();
                swal({
                    text: 'Are you sure, you want to cancel these Shipment(s)?',
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
                        $.ajax({
                            url: '{!! route('cod.orders.cancel_all') !!}',
                            method: 'POST',
                            data: {
                                'ids[]': selected_rows,
                                'reason' : reason,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function (data) {
                                if(data.status === 1){


                                    table.draw('false');
                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                }else{
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                                table.button(0).disable();
                                table.button(1).disable();

                                table.rows().deselect();
                                selected_rows = [];
                                $('#bulk_cancel_reason').val('');
                                $('#BulkCancelModal').modal('hide');
                            });
                    }
                });
            });
            $('body').on('click', '.cancel_order', function () {
                var cancel_shipment_id = parseInt($(this).parents('tr').attr('id'));
                if (cancel_shipment_id) {
                    $('#cancel_shipment_id').val(cancel_shipment_id);
                    $('#CancelReasonModal').modal('show');
                }
            });

            $('#case_nature_requests').on('change',function (e) {
            
                if($(this).val() == 13){
                    $('#alternate_phone_input').removeClass('d-none');
                    // $('#cod_amount_input').addClass('d-none');

                }
                // else if($(this).val() == 12){
                //     $('#cod_amount_input').removeClass('d-none');
                //     $('#alternate_phone_input').addClass('d-none');

                // }
                else{
                    // $('#cod_amount_input').addClass('d-none');
                    $('#alternate_phone_input').addClass('d-none');

                }
            });

            $('#CancelReasonSubmit').on('click',function () {
                var reason = $('#cancel_reason').val();
                var id = parseInt($('#cancel_shipment_id').val());
                $.ajax({
                    url: '{!! route('cod.orders.cancel') !!}',
                    method: 'POST',
                    data: {
                        'shipment_id': id,
                        'reason': reason,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if (data.status === 1) {
                        $('#CancelReasonModal').modal('hide');
                        table.draw('false');
                        toastr.success(data.success, 'Success!', {
                            positionClass: 'toast-bottom-center',
                            containerId: 'toast-bottom-center'
                        });

                    } else {
                        $('#CancelReasonModal').modal('hide');
                        toastr.error(data.error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                });
            });
            $('.datatable tbody').on('click', 'tr td.select-checkbox', function () {
                var id = parseInt($(this).parent('tr').attr('id'));

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button(0).enable();
                    table.button(1).enable();
                    table.button(2).enable();

                }
                else {
                    table.button(0).disable();
                    table.button(1).disable();
                    table.button(2).disable();
                }
            });

            //Dispute
            $('#city_select').select2({
                placeholder: 'Select a city',
                dropdownParent: $('#dispute_form')
            });
            $('#dispute_type_select').select2({
                placeholder: 'Select a Dispute type',
                dropdownParent: $('#dispute_form')
            });

            $('body').on('click', '.dispute_modal', function () {
                var shipment_id = parseInt($(this).parents('tr').attr('id'));
                $('#DisputeModal').modal('show');
                $('#dispute_shipment_id').val(shipment_id);
            });
            $('#DisputeModal').on('shown.bs.modal', function () {
                var id = $('#dispute_shipment_id').val();
                if (id) {
                    $.ajax({
                        url: '{!! route('cod.dispute.data') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'shipment_id': id
                        }
                    }).done(function (data) {
                        if (data.success == 1) {
                            $('#tracking_number').val(data.tracking);
                            select = $('#tracking_number').selectize({
                                placeholder: 'Tracking Number(s)*',
                                delimiter: ',',
                                createOnBlur: true,
                                persist: false,
                                plugins: ['remove_button'],
                                onDropdownOpen: function (dropdown) {
                                    dropdown.remove();
                                },
                                onType: function (str) {
                                    var regex = /^[0-9,]+$/;

                                    if (!regex.test(str)) {
                                        select[0].selectize.setTextboxValue('');
                                    }
                                },
                                create: function (input) {
                                    if (input.length >= 12 && Math.floor(input) == input && $.isNumeric(input)) {
                                        return {
                                            value: input,
                                            text: input
                                        }
                                    }
                                    else {
                                        return false;
                                    }
                                }
                            });
                        } else {
                            toastr.error(message, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });

                        }
                    });
                }
            });

            var max_char = 190;
            $('#description').on('keypress copy paste', function (e) {
                // var comment = $(this).val();
                // console.log(comment)
                if ($(this).val().length == max_char) {
                    e.preventDefault();
                } else if ($(this).val().length > max_char) {
                    // Maximum exceeded
                    this.value = this.value.substring(0, max_char);
                }
            });
            $('body').on('change', '#DisputeModal input,#DisputeModal textarea', function () {
                $(this).val($(this).val().trim());
            });
            $("#dispute_form").validate({
                ignore: [],
                errorClass: "danger",
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function (form) {

                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    var city_select = $('#city_select').val();
                    var dispute_type_select = $('#dispute_type_select').val();
                    var tracking_number = $('#tracking_number').val();
                    var description = $('#description').val();
                    $.ajax({
                        url: '{!! route('cod.dispute.create') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'city_select': city_select,
                            'dispute_type_select': dispute_type_select,
                            'tracking_number': tracking_number,
                            'description': description
                        }
                    }).done(function (data) {
                        $('#DisputeModal').modal('hide');

                        if (data.invalid !== undefined) {
                            var message = 'Invalid Tracking Number(s): ' + data.invalid.join(', ');

                            toastr.error(message, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }

                        if (data.disallowed !== undefined) {
                            var message = 'Following Tracking Number(s) doesn\'t belong to you: ' + data.disallowed.join(', ');

                            toastr.error(message, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                        if (data.success != undefined) {
                            table.draw('false');
                            toastr.success(data.success, 'Success!', {
                                positionClass: 'toast-bottom-center',
                                containerId: 'toast-bottom-center'
                            });

                        }
                    });

                }


            });
            $('#DisputeModal').on('hidden.bs.modal', function (e) {
                $('#dispute_form')[0].reset();
                $('#DisputeCreate').removeAttr('disabled');
                select[0].selectize.destroy();
                $('#dispute_form').validate().resetForm();
                $('#city_select').val('').trigger('change');
                $('#dispute_type_select').val('').trigger('change');
            });

            $('body').on('click', '.view_charges', function () {
                var shipment_id = $(this).parents('tr').attr('id');
                $('#ShipmentChargesModal').modal('show');
                $('#shipment_charges_modal_id').val(shipment_id);
                $.ajax({
                    url: '{!! route("cod.orders.charges") !!}',
                    method: 'POST',
                    data: {
                        'shipment_id': shipment_id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    $('#shipment_charges_body').html(data);
                    $('#shipment_charges_modal_heading span').text(shipment_id);
                })
            });


            //Selectize
            var select = $('#track_form .tracking_numbers').selectize({
                placeholder: 'Tracking Number(s)*',
                delimiter: ',',
                createOnBlur: true,
                persist: false,
                plugins: ['remove_button'],
                onDropdownOpen: function (dropdown) {
                    dropdown.remove();
                },
                onType: function (str) {
                    var regex = /^[0-9,]+$/;

                    if (!regex.test(str)) {
                        select[0].selectize.setTextboxValue('');
                    }
                },
                create: function (input) {
                    if (input.length >= 6 && Math.floor(input) == input && $.isNumeric(input)) {
                        return {
                            value: input,
                            text: input
                        }
                    }
                    else {
                        return false;
                    }
                }
            });

            $('#track_form').bind('submit', function (e) {
                e.preventDefault();
                var tracking_numbers = $('#track_form .tracking_numbers').val();
                var booking_from_date = $('#track_form #booking_from_date').val();
                var booking_to_date = $('#track_form #booking_to_date').val();
                var phone_number = $('#track_form .phone_number').val();

                if (tracking_numbers != ''  || (booking_from_date != '' && booking_to_date != '' || phone_number != '' )) {
                    table.draw();
                }

            });
            $('#consolidate_shipments_form').bind('submit', function (e) {
                e.preventDefault();
                var form = this;
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to consolidate shipments!',
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
                        swal({
                            title: 'Please Wait!',
                            text: 'Shipments are being consolidated!',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });
                        form.submit();
                    }
                });
            });

            $('#case_nature_select').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Case Nature",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            }).bind('change', function () {
                var id = parseInt($(this).val());
                if(id === 1){
                    $('#request_service').addClass('d-none');
                    $('#request_complaints').removeClass('d-none');
                    $('#request_feedback').addClass('d-none');
                    $('#AddNewRequest').removeClass('d-none');
                    $('#request_claims').addClass('d-none');
                }else if(id === 2){
                    $('#request_complaints').addClass('d-none');
                    $('#request_service').removeClass('d-none');
                    $('#request_feedback').addClass('d-none');
                    $('#AddNewRequest').removeClass('d-none');
                    $('#request_claims').addClass('d-none');

                }
                else if(id === 3){
                    $('#request_complaints').addClass('d-none');
                    $('#request_service').addClass('d-none');
                    $('#request_feedback').removeClass('d-none');
                    $('#AddNewRequest').removeClass('d-none');
                    $('#request_claims').addClass('d-none');

                }
                else if(id === 4){
                    $('#request_complaints').addClass('d-none');
                    $('#request_service').addClass('d-none');
                    $('#request_feedback').addClass('d-none');
                    $('#AddNewRequest').removeClass('d-none');
                    $('#request_claims').removeClass('d-none');
                }else{
                    $('#request_complaints').addClass('d-none');
                    $('#request_service').addClass('d-none');
                    $('#request_feedback').addClass('d-none');
                    $('#AddNewRequest').addClass('d-none');
                    $('#request_claims').addClass('d-none');

                }
            });

            var max_char_request = 245;
            $('#feedback_description').on('keypress copy paste',function (e) {
                if ($(this).val().length == max_char_request) {
                    e.preventDefault();
                } else if ($(this).val().length > max_char_request) {
                    // Maximum exceeded
                    this.value = this.value.substring(0, max_char_request);
                }
            });
            $('#service_description').on('keypress copy paste',function (e) {
                if ($(this).val().length == max_char_request) {
                    e.preventDefault();
                } else if ($(this).val().length > max_char_request) {
                    // Maximum exceeded
                    this.value = this.value.substring(0, max_char_request);
                }
            });
            $('#complaint_description').on('keypress copy paste',function (e) {
                if ($(this).val().length == max_char_request) {
                    e.preventDefault();
                } else if ($(this).val().length > max_char_request) {
                    // Maximum exceeded
                    this.value = this.value.substring(0, max_char_request);
                }
            });
            // $('#case_nature_claim').prepend('<option value="" selected="selected"></option>').select2({
            //     width:'100%',
            //     placeholder:"Select Claim Type",
            //     allowClear:true,
            //     dropdownParent:$('#add_request_form')
            // }).bind('change', function () {
            //     var id = parseInt($(this).val());
            //     if(id === 26){
            //         $('#claim_product_cost_div').addClass('d-none');
            //         $('#claim_product_picture_div').addClass('d-none');
            //         $('#claim_invoice_picture_div').addClass('d-none');
            //     }
            //     else{
            //         $('#claim_product_cost_div').removeClass('d-none');
            //         $('#claim_product_picture_div').removeClass('d-none');
            //         $('#claim_invoice_picture_div').removeClass('d-none');
            //     }
            // });
            var lost_flag = true;
            $('#case_nature_claim').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Claim Type",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            }).bind('select2:select', function () {
                var id = parseInt($(this).val());
                var value = $('#case_nature_claim').val();
                // console.log(value);
                if (this.value && this.value == 23 && lost_flag === true) {
                    $('#receiving_sheet_div').removeClass('d-none');
                    var shipment_id = $('#requested_shipment_ids').val();
                    $.ajax({
                        url: '{!! route('cod.crm.request.lost.claim') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'shipment_id': shipment_id,
                        }
                    }).done(function (data) {
                        $('#request_id').empty().trigger('change');
                        $('#request_id').prepend('<option value="" selected="selected"></option>').select2({
                            width:'100%',
                            placeholder:"Select Receiving Sheet ID",
                            allowClear:true,
                            dropdownParent:$('#add_request_form')
                        });
                        if (data.status == 1) {
                            var newOption = new Option(data.receiving_sheet_id, data.receiving_sheet_id, false, false);
                            $('#request_id').append(newOption).trigger('change');

                        } else {
                            lost_flag = true;
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                            // $('#AddNewRequest').attr('disabled',true);
                        }

                    });
                }
                else{
                    lost_flag = true;
                    $('#receiving_sheet_div').addClass('d-none');
                    $('#AddNewRequest').attr('disabled',false);

                }
                if (this.value && this.value == 21)
                {
                    $('#claim_shipment_damage_div').removeClass('d-none');
                }
                else {
                    $('#claim_shipment_damage_div').addClass('d-none');
                }

                if (this.value && this.value == 22)
                {
                    $('#claim_content_short_div').removeClass('d-none');
                }
                else {
                    $('#claim_content_short_div').addClass('d-none');
                }

                if(id === 26){
                    $('#claim_product_cost_div').addClass('d-none');
                    $('#claim_product_picture_div').addClass('d-none');
                    $('#claim_invoice_picture_div').addClass('d-none');
                }
                else{
                    $('#claim_product_cost_div').removeClass('d-none');
                    $('#claim_product_picture_div').removeClass('d-none');
                    $('#claim_invoice_picture_div').removeClass('d-none');
                }
            });
            $('#case_nature_complaints').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Complaint Type",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            });
            $('#case_nature_requests').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Request Type",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            });
            $('body').on('change', '#add_request_form textarea', function () {
                $(this).val($(this).val().trim());
            });
            $('#add_request_form').on('submit',function (e) {
                e.preventDefault();
            });
            $( "#add_request_form" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    var case_nature_id = parseInt($('#case_nature_select').val());
                    if (case_nature_id === 1) {
                        var complaint_id = $('#case_nature_complaints').val();
                        var description = $('#complaint_description').val();
                    } else if (case_nature_id === 3) {
                        var feedback_flag = true;
                        var feedback_description = $('#feedback_description_request').val();
                    } else {
                        var complaint_id = $('#case_nature_requests').val();
                        var description = $('#service_description').val();
                    }

                    if (case_nature_id === 3) {
                        if (!feedback_description) {
                            feedback_flag = false;
                            var error = "Please Enter Description!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                        if (feedback_flag) {
                            $('#AddNewRequest').attr('disabled', true);
                            $.ajax({
                                url: '{!! route('cod.crm.feedback.add') !!}',
                                method: 'POST',
                                data: {
                                    '_token': '{{ csrf_token() }}',
                                    'shipment_ids': selected_rows,
                                    'description': feedback_description
                                }
                            })
                                .done(function (data) {
                                    if (data.status) {
                                        if (data.flag) {
                                            var html = '';

                                            $.each(data.already_existed_shipments, function (index, tracking_number) {
                                                html += tracking_number + '<br/>';
                                            });

                                            if (!data.cannot_change) {
                                                html += '<br/>Request/Complaint already lodged for the above Shipment(s)!';
                                            }
                                            else {
                                                html += '<br/>Request for Change cannot be opened for the above Shipment(s) at the Current Status!';
                                            }

                                            content = document.createElement('div');
                                            content.innerHTML = html;

                                            swal({
                                                title: 'Request / Complaint Cannot Be Lodged!',
                                                content: content,
                                                icon: 'warning',
                                                buttons: {
                                                    cancel: {
                                                        text: 'Close',
                                                        value: null,
                                                        visible: true,
                                                        closeModal: true,
                                                    },
                                                },
                                                closeOnClickOutside: false,
                                                closeOnEsc: false,
                                                dangerMode: true
                                            });
                                        } else {
                                            toastr.success(data.success, 'Success!', {
                                                positionClass: 'toast-bottom-center',
                                                containerId: 'toast-bottom-center'
                                            });
                                        }
                                    } else {
                                        toastr.error(data.error, 'Error!', {
                                            positionClass: 'toast-top-center',
                                            containerId: 'toast-top-center'
                                        });
                                    }

                                    table.button('.print').disable();
                                    table.button('.cancel').disable();
                                    table.button('.consolidate').disable();

                                    selected_rows = [];

                                    table.rows().deselect();

                                    table.draw('false');

                                    $('#AddRequestModal').modal('hide');
                                    $('#AddNewRequest').attr('disabled', false);
                                });
                        }
                    } else if (case_nature_id === 4) {
                        if(selected_rows.length > 1){
                            var error = "Cannot select more than one shipment";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                            $('#AddRequestModal').modal('hide');
                            $('#AddNewRequest').attr('disabled', false);
                        }
                        else{
                            var nature_flag = true;
                            var case_nature_claim_id = $('#case_nature_claim').val();
                            var product_cost = $('#claim_product_cost').val();
                            // var damage_product_cost = $('#claim_product_cost').val();
                            var check_product_picture = $('#product_picture').val();
                            var check_invoice_picture = $('#invoice_picture').val();
                            $('#shipment_ids').val(selected_rows);
                            $('#case_nature_id').val(case_nature_id);
                            $('#complaint_id').val(case_nature_claim_id);
                            var formData = new FormData($('#add_request_form')[0]);
                            if(case_nature_claim_id === 23){
                                if($('#request_id').val() == "" || $('#request_id').val() == null){
                                    nature_flag = false;
                                    var error = "Please select receiving sheet!";
                                    toastr.error(error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                            }
                            if (!case_nature_claim_id) {
                                nature_flag = false;
                                var error = "Please select Claim type!";
                                toastr.error(error, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }
                            if (case_nature_claim_id !== "26") {
                                if (!check_product_picture) {
                                    nature_flag = false;
                                    var error = "Please attach Product Picture!";
                                    toastr.error(error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                if (!product_cost) {
                                    nature_flag = false;
                                    var error = "Please enter Product Cost!";
                                    toastr.error(error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                // if (!damage_product_cost) {
                                //     nature_flag = false;
                                //     var error = "Please enter Damage Product Cost!";
                                //     toastr.error(error, 'Error!', {
                                //         positionClass: 'toast-top-center',
                                //         containerId: 'toast-top-center'
                                //     });
                                // }
                                if (!check_invoice_picture) {
                                    nature_flag = false;
                                    var error = "Please attach Invoice Picture!";
                                    toastr.error(error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                            }
                            if (nature_flag) {
                                $('#AddNewRequest').attr('disabled', true);
                                swal({
                                        title: 'Please Wait!',
                                        text: 'Launching Request.',
                                        icon: 'info',
                                        buttons: false,
                                        closeOnClickOutside: false,
                                        closeOnEsc: false
                                    });
                                $.ajax({
                                    url: '{!! route('cod.crm.request.add') !!}',
                                    method: 'POST',
                                    enctype: 'multipart/form-data',
                                    data: formData,
                                    dataType: 'json',
                                    processData: false,
                                    contentType: false,
                                })
                                    .done(function (data) {
                                    swal.close();

                                        if (data.status) {
                                            if (data.flag) {
                                                var html = '';

                                                $.each(data.already_existed_shipments, function (index, tracking_number) {
                                                    html += tracking_number + '<br/>';
                                                });

                                                if (!data.cannot_change) {
                                                    html += '<br/>Request/Complaint already lodged for the above Shipment(s)!';
                                                }
                                                else {
                                                    html += '<br/>Request for Change cannot be opened for the above Shipment(s) at the Current Status!';
                                                }

                                                content = document.createElement('div');
                                                content.innerHTML = html;

                                                swal({
                                                    title: 'Request / Complaint Cannot Be Lodged!',
                                                    content: content,
                                                    icon: 'warning',
                                                    buttons: {
                                                        cancel: {
                                                            text: 'Close',
                                                            value: null,
                                                            visible: true,
                                                            closeModal: true,
                                                        },
                                                    },
                                                    closeOnClickOutside: false,
                                                    closeOnEsc: false,
                                                    dangerMode: true
                                                });
                                            } else {
                                                toastr.success(data.success, 'Success!', {
                                                    positionClass: 'toast-bottom-center',
                                                    containerId: 'toast-bottom-center'
                                                });
                                            }
                                        } else {
                                            toastr.error(data.error, 'Error!', {
                                                positionClass: 'toast-top-center',
                                                containerId: 'toast-top-center'
                                            });
                                        }

                                        table.button('.print').disable();
                                        table.button('.cancel').disable();

                                        selected_rows = [];

                                        table.rows().deselect();

                                        table.draw('false');

                                        $('#AddRequestModal').modal('hide');
                                        $('#AddNewRequest').attr('disabled', false);
                                    });
                            }
                        }
                    }
                    else {
                        $('#AddNewRequest').attr('disabled',true);
                        swal({
                                        title: 'Please Wait!',
                                        text: 'Launching Request.',
                                        icon: 'info',
                                        buttons: false,
                                        closeOnClickOutside: false,
                                        closeOnEsc: false
                                    });
                        $.ajax({
                            url: '{!! route('cod.crm.request.add') !!}',
                            method: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'shipment_ids': selected_rows,
                                'case_nature_id': case_nature_id,
                                'complaint_id': complaint_id,
                                'description': description,
                                'alternate_phone': $('#alternate_phone').val(),
                                // 'cod_amount': $('#cod_amount').val(),
                            }
                        })
                            .done(function (data) {
                                swal.close();

                                if (data.status) {
                                    if (data.flag) {
                                        var html = '';

                                        $.each(data.already_existed_shipments, function (index, tracking_number) {
                                            html += tracking_number + '<br/>';
                                        });

                                        if (!data.cannot_change) {
                                            html += '<br/>Request/Complaint already lodged for the above Shipment(s)!';
                                        }
                                        else {
                                            html += '<br/>Request for Change cannot be opened for the above Shipment(s) at the Current Status!';
                                        }

                                        content = document.createElement('div');
                                        content.innerHTML = html;

                                        swal({
                                            title: 'Request / Complaint Cannot Be Lodged!',
                                            content: content,
                                            icon: 'warning',
                                            buttons: {
                                                cancel: {
                                                    text: 'Close',
                                                    value: null,
                                                    visible: true,
                                                    closeModal: true,
                                                },
                                            },
                                            closeOnClickOutside: false,
                                            closeOnEsc: false,
                                            dangerMode: true
                                        });
                                    } else {
                                        toastr.success(data.success, 'Success!', {
                                            positionClass: 'toast-bottom-center',
                                            containerId: 'toast-bottom-center'
                                        });
                                    }
                                    // toastr.success(data.success, 'Success!', {
                                    //     positionClass: 'toast-bottom-center',
                                    //     containerId: 'toast-bottom-center'
                                    // });
                                } else {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }

                                table.button('.print').disable();
                                table.button('.cancel').disable();
                                table.button('.consolidate').disable();

                                selected_rows = [];

                                table.rows().deselect();

                                table.draw('false');

                                $('#AddRequestModal').modal('hide');
                                $('#AddNewRequest').attr('disabled',false);
                            });
                    }
                }
            });

            $('#AddRequestModal').on('hide.bs.modal', function (e) {
                $('#add_request_form')[0].reset();
                $('#case_nature_complaints').val('').trigger('change');
                $('#case_nature_select').val('').trigger('change');
                $('#case_nature_requests').val('').trigger('change');

                $('#complaint_description').val('');
                $('#service_description').val('');
                $('#feedback_description_request').val('');
                $('#request_complaints').addClass('d-none');
                $('#request_service').addClass('d-none');
                $('#request_feedback').addClass('d-none');
                $('#request_claims').addClass('d-none');
                $('#case_nature_claim').val('').trigger('change');
                $('#claim_channel').val('').trigger('change');
                $('#claim_product_cost').val('');
                // $('#damage_claim_product_cost').val('');
                $('#request_id').val('').trigger('change');
                $('#receiving_sheet_div').addClass('d-none');
                $('#alternate_phone_input').addClass('d-none');
                $('#alternate_phone').val('');
                // $('#cod_amount_input').addClass('d-none');
                // $('#cod_amount').val('');
            });

            $('#add_feedback_form').bind('submit', function (e) {
                e.preventDefault();
                var feedback_flag = true;
                var feedback_description = $('#feedback_description').val();
                if(!feedback_description){
                    feedback_flag = false;
                    var error = "Please Enter Description!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
                if(feedback_flag){
                    $('#AddNewFeedback').attr('disabled',true);
                    $.ajax({
                        url: '{!! route('cod.crm.feedback.add') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'description' : feedback_description
                        }
                    })
                        .done(function(data) {
                            if (data.status) {
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            }
                            else {
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }

                            $('#AddFeedbackModal').modal('hide');
                            $('#AddNewFeedback').attr('disabled',false);
                        });
                }

            });
            $('#AddFeedbackModal').on('hide.bs.modal', function (e) {
                $('#feedback_channel').val('').trigger('change');
                $('#feedback_description').val('');
            });
            $('#CancelReasonModal').on('hide.bs.modal', function (e) {
                $('#cancel_reason').val('').trigger('change');
            });
        });
    </script>

@endsection