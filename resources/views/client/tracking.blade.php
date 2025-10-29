@extends('client.layout.master')

@section('title', 'Tracking')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Tracking
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('client.inc.messages')

                            <form id="track_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                                <div class="col-lg-4 col-md-4 col-sm-6">
                                    <div class="form-group ">
                                        <input type="text" name="tracking_numbers" class="tracking_numbers"
                                            placeholder="Tracking Number(s)*" data-tags-input-name="tracking_number"
                                            data-rule-required="true" data-msg-required="Tracking Number is required">
                                    </div>
                                </div>


                                <div class="form-group ml-1">
                                    <button type="submit" name="track" class="btn btn-primary"
                                        value="Track">Track</button>
                                </div>
                            </form>

                            <div class="tracking" id="tracking">
                            </div>
                                <div class="modal fade" id="rider_information" role="dialog"
                                    aria-labelledby="rider_information_title" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="rider_information_title">Rider Information</h4>

                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Close</button>
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

    {{-- Geo Codes --}}
    <div class="modal fade text-left" id="AddGeoCodeModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="AddGeoCodeModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Consignee Address Geo Code</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_geo_code_form" method="post">
                        @method('POST')
                        @csrf
                        <div class="container">
                            <div class="row">
                                <h2 class="heading">Tracking Number(s)</h2>
                            </div>

                            <input type="hidden" name="shipment_id" id="geo_code_shipment_id">
                            <div class="row old_scroll" id="geo_code_shipments">
                            </div>
                            <hr>
                            <div class="row justify-content-center">
                                <div class="col-10">
                                    <fieldset class="form-group">
                                        <input type="text" class="form-control" placeholder="Enter Latitude" name="lat" id="lat"  data-rule-required="true"
                                               data-msg-required="Consignee Address Latitude is required">
                                    </fieldset>
                                    <fieldset class="form-group">
                                        <input type="text" class="form-control" placeholder="Enter Longitude" name="long" id="long"  data-rule-required="true"
                                               data-msg-required="Consignee Address Longitude is required">
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <button id="AdGeoCodeRequest" type="submit"
                                            class="btn btn-primary btn-block">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Get Support --}}
    <div class="modal fade text-left" id="AddRequestModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="AddRequestModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Get Support</h4>
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

                            <input type="hidden" id="requested_shipment_id">
                            <div class="row old_scroll" id="requested_shipments">

                            </div>
                            <hr>
                            {{-- <div class="row justify-content-center">
                                <div class="col-10">
                                    <fieldset class="form-group">
                                        <select name="case_nature_select" id="case_nature_select"
                                            class="form-control select2" data-rule-required="true"
                                            data-msg-required="Case Nature is required">
                                            @foreach ($case_nature as $nature)
                                                <option value="{{ $nature->id }}">{{ $nature->name }}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                            </div> --}}

                            <div class="row justify-content-center">
                                <div class="col-10">
                                    <fieldset class="form-group">
                                        <select name="case_nature_select" id="case_nature_select"
                                            class="form-control select2" data-rule-required="true"
                                            data-msg-required="Case Nature is required">
                                            @foreach ($case_nature as $nature)
                                                <option value="{{ $nature->id }}">{{ $nature->name }}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                            </div>


                            <div class="complaints d-none" id="request_complaints">
                                <div class="row justify-content-center">
                                    <div class="col-10">
                                        <fieldset class="form-group">
                                            <select name="case_nature_complaint" id="case_nature_complaints"
                                                class="form-control select2" data-rule-required="true"
                                                data-msg-required="Complaint Type is required">
                                                @foreach ($case_nature_complaints as $complaints)
                                                    <option value="{{ $complaints->id }}">{{ $complaints->type }}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-10">
                                        <fieldset class="form-group">
                                            <select name="case_nature_complainant" id="case_nature_complainant"
                                                    class="form-control select2" data-rule-required="true"
                                                    data-msg-required="Complainant is required">
                                                <option value="1">Consignee</option>
                                                <option value="2">Shipper</option>
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-10">
                                        <fieldset class="form-group">
                                            <input type="text" class="form-control" placeholder="Enter Phone Number" name="complainant_phone" id="complainant_phone"  data-rule-required="true"
                                                   data-msg-required="Complainant Phone is required">
                                        </fieldset>
                                    </div>
                                    @foreach($case_nature_complaints as $complaint)
                                        @if($complaint->remarks_visibility == 1)
                                            <div class="col-10 d-none" id="case_nature_remarks_div">
                                                <fieldset class="form-group">
                                                    <select name="complaint_description[]" id="case_nature_remarks" class="form-control select2" multiple="multiple">

                                                    </select>
                                                </fieldset>
                                            </div>
                                        @else
                                            <div class="col-10 d-none" id="complaint_description_textarea_new">
                                                <fieldset class="form-group">
                                                    <textarea class="form-control" name="complaint_description[]" id="complaint_description_new" rows="5"
                                                        placeholder="Enter Description Here..." data-rule-required="true" data-msg-required="Description is required"></textarea>
                                                </fieldset>
                                            </div>
                                        @endif
                                    @endforeach
                                    <div class="col-10 d-none" id="complaint_description_textarea">
                                        <fieldset class="form-group">
                                            <textarea class="form-control" name="complaint_description[]" id="complaint_description" rows="5"
                                                placeholder="Enter Description Here..." data-rule-required="true" data-msg-required="Description is required"></textarea>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>

                            <div class="service d-none" id="request_service">
                                <div class="row justify-content-center">
                                    <div class="col-10">
                                        <fieldset class="form-group">
                                            <select name="case_nature_request" id="case_nature_requests"
                                                class="form-control select2" data-rule-required="true"
                                                data-msg-required="Complaint Type is required">
                                                @foreach ($case_nature_service_requests as $service)
                                                    <option value="{{ $service->id }}">{{ $service->type }}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-10 d-none" id="alternate_phone_input">
                                        <fieldset class="form-group">
                                            <input type="text" name="alternate_phone" class="form-control"
                                                id="alternate_phone" placeholder="Enter Alternate Number"
                                                data-rule-required="true"
                                                data-msg-required="Alternate Number is required">
                                        </fieldset>
                                    </div>

                                    <div  class="col-10 d-none" id="cod_change">
                                        <div class="row justify-content-center">
                                            <div class="col-6">
                                                <fieldset class="form-group input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Old COD Amount</span>
                                                    </div>

                                                    <input type="text" name="old_amount" id="old_amount" readonly class="form-control rounded-right">
                                                </fieldset>
                                            </div>
                                            <div class="col-6">
                                                <fieldset class="form-group input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">New COD Amount</span>
                                                    </div>

                                                    <input type="text" name="new_amount" id="new_amount" class="form-control rounded-right new_amount" placeholder="Enter Amount" data-rule-required="true" data-msg-required="New COD Amount is required">
                                                </fieldset>
                                            </div>
                                            <div class="col-6 d-none" id="cod_parcel_value_change">
                                                <fieldset class="form-group input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Enter Parcel Value</span>
                                                    </div>

                                                    <input type="text" name="cod_parcel_value" id="cod_parcel_value" class="form-control rounded-right cod_parcel_value" placeholder="Parcel Value" data-rule-required="true" data-msg-required="Parcel Value is required"  oninput="if(this.value=='0') this.value=''">
                                                </fieldset>
                                            </div>
                                            <div class="col-12">
                                                <fieldset class="form-group">
                                                    <textarea class="form-control" name="cod_remarks" id="cod_remarks" rows="3" placeholder="Enter Remarks*" data-rule-required="true" data-msg-required="Remarks is required"></textarea>
                                                </fieldset>
                                            </div>
                                        </div>
                                    </div>

                                    @foreach ($case_nature_service_requests as $service)
                                        @if ($service->remarks_visibility == 1)
                                            <div class="col-10 d-none" id="case_nature_service_remarks_div">
                                                <fieldset class="form-group">
                                                    <select name="service_description[]" id="case_nature_service_remarks" class="form-control select2" multiple="multiple">

                                                    </select>
                                                </fieldset>
                                            </div>
                                        {{-- @else
                                            <div class="col-10 d-none" id="service_description_textarea_new">
                                                <fieldset class="form-group">
                                                    <textarea class="form-control" name="service_description[]" id="service_description_new" rows="5" placeholder="Enter Description*" data-rule-required="true" data-msg-required="Description is required"></textarea>
                                                </fieldset>
                                            </div> --}}
                                        @endif
                                    @endforeach

                                    <div class="col-10 d-none" id="service_description_textarea">
                                        <fieldset class="form-group">
                                            <textarea class="form-control" name="service_description[]" id="service_description" rows="5" placeholder="Enter Description*" data-rule-required="true" data-msg-required="Description is required"></textarea>
                                        </fieldset>
                                    </div>

                                </div>
                            </div>
                            <div class="feedback d-none" id="request_feedback">
                                <div class="row justify-content-center">
                                    <div class="col-10">
                                        <fieldset class="form-group">
                                            <textarea class="form-control" name="feedback_description_request" id="feedback_description_request" rows="5"
                                                placeholder="Enter Description Here..." data-rule-required="true" data-msg-required="Description is required"></textarea>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <div class="claims d-none" id="request_claims">
                                <input type="hidden" name="shipment_ids" id="shipment_ids">
                                <input type="hidden" name="case_nature_id" id="case_nature_id">
                                <input type="hidden" name="complaint_id" id="complaint_id">
                                <div class="row justify-content-center">
                                    <div class="col-10">
                                        <fieldset class="form-group">
                                            <select name="case_nature_tclaim" id="case_nature_claim"
                                                class="form-control select2" data-rule-required="true" data-msg-required="Select claim type">
                                                @foreach ($case_nature_type_claims as $claim)
                                                    <option value="{{ $claim->id }}">{{ $claim->type }}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-10" id="claim_product_cost_div">
                                        <fieldset class="form-group">
                                            <input class="form-control" name="claim_product_cost" id="claim_product_cost"
                                                value="" placeholder="Enter Claim Amount">
                                        </fieldset>
                                    </div>
                                    <div class="col-10 d-none" id="receiving_sheet_div">
                                        <fieldset class="form-group">
                                            <select name="receiving_sheet_id" id="request_id"
                                                class="form-control select2" data-rule-required="true"
                                                data-msg-required="Please Select Receiving Sheet">

                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-10 text-left" id="claim_product_picture_div">
                                        <fieldset class="form-group">
                                            <label for="product_picture"><b>Product Picture:</b></label>
                                            <input class="form-control form-control-sm" type="file"
                                                name="product_picture" id="product_picture"
                                                data-rule-extension="jpeg|jpg|png"
                                                data-msg-extension="Only file with extension jpeg, jpg or png allowed"
                                                data-rule-accept="image/*" data-msg-accept="Only Image file allowed"
                                                data-rule-maxsize="2097152"
                                                data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)."
                                                data-rule-required="true" data-msg-required="Image is required">
                                        </fieldset>
                                    </div>

                                    <div class="col-10 text-left" id="claim_invoice_picture_div">
                                        <fieldset class="form-group">
                                            <label for="invoice_picture"><b>Invoice Picture:</b></label>
                                            <input class="form-control form-control-sm" type="file"
                                                name="invoice_picture" id="invoice_picture"
                                                data-rule-extension="jpeg|jpg|png"
                                                data-msg-extension="Only file with extension jpeg, jpg or png allowed"
                                                data-rule-accept="image/*" data-msg-accept="Only Image file allowed"
                                                data-rule-maxsize="2097152"
                                                data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)."
                                                data-rule-required="true" data-msg-required="Image is required">
                                        </fieldset>
                                    </div>

                                    <div class="col-10 text-left d-none" id="claim_shipment_damage_div">
                                        <fieldset class="form-group">
                                            <label for="damage_product_picture"><b>Damage Picture:</b></label>
                                            <input class="form-control form-control-sm" type="file"
                                                name="damage_product_picture" id="damage_product_picture"
                                                data-rule-extension="jpeg|jpg|png"
                                                data-msg-extension="Only file with extension jpeg, jpg or png allowed"
                                                data-rule-accept="image/*" data-msg-accept="Only Image file allowed"
                                                data-rule-maxsize="2097152"
                                                data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)."
                                                data-rule-required="true" data-msg-required="Image is required">
                                        </fieldset>
                                        <fieldset class="form-group">
                                            <label for="product_packaging_picture"><b>Product Packaging
                                                    Picture:</b></label>
                                            <input class="form-control form-control-sm" type="file"
                                                name="product_packaging_picture" id="product_packaging_picture"
                                                data-rule-extension="jpeg|jpg|png"
                                                data-msg-extension="Only file with extension jpeg, jpg or png allowed"
                                                data-rule-accept="image/*" data-msg-accept="Only Image file allowed"
                                                data-rule-maxsize="2097152"
                                                data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)."
                                                data-rule-required="true" data-msg-required="Image is required">
                                        </fieldset>
                                        <fieldset class="form-group">
                                            <label for="actual_product_picture"><b>Actual Product Picture:</b></label>
                                            <input class="form-control form-control-sm" type="file"
                                                name="actual_product_picture" id="actual_product_picture"
                                                data-rule-extension="jpeg|jpg|png"
                                                data-msg-extension="Only file with extension jpeg, jpg or png allowed"
                                                data-rule-accept="image/*" data-msg-accept="Only Image file allowed"
                                                data-rule-maxsize="2097152"
                                                data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)."
                                                data-rule-required="true" data-msg-required="Image is required">
                                        </fieldset>
                                        <fieldset class="form-group">
                                            <input class="form-control damage_claim_product_cost"
                                                name="damage_claim_product_cost" id="damage_claim_product_cost"
                                                value="" placeholder="Enter Actual Damaged Product Cost">
                                        </fieldset>

                                    </div>

                                    <div class="col-10 text-left d-none" id="claim_content_short_div">
                                        <fieldset class="form-group">
                                            <label for="missing_product_picture"><b>Missing Product Picture:</b></label>
                                            <input class="form-control form-control-sm" type="file"
                                                name="missing_product_picture" id="missing_product_picture"
                                                data-rule-extension="jpeg|jpg|png"
                                                data-msg-extension="Only file with extension jpeg, jpg or png allowed"
                                                data-rule-accept="image/*" data-msg-accept="Only Image file allowed"
                                                data-rule-maxsize="2097152"
                                                data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)."
                                                data-rule-required="true" data-msg-required="Image is required">
                                        </fieldset>
                                        <fieldset class="form-group">
                                            <label for="product_packaging_picture_content_short"><b>Product Packaging
                                                    Picture:</b></label>
                                            <input class="form-control form-control-sm" type="file"
                                                name="product_packaging_picture_content_short"
                                                id="product_packaging_picture_content_short"
                                                data-rule-extension="jpeg|jpg|png"
                                                data-msg-extension="Only file with extension jpeg, jpg or png allowed"
                                                data-rule-accept="image/*" data-msg-accept="Only Image file allowed"
                                                data-rule-maxsize="2097152"
                                                data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)."
                                                data-rule-required="true" data-msg-required="Image is required">
                                        </fieldset>
                                        <fieldset class="form-group">
                                            <label for="actual_product_picture_content_short"><b>Actual Product
                                                    Picture:</b></label>
                                            <input class="form-control form-control-sm" type="file"
                                                name="actual_product_picture_content_short"
                                                id="actual_product_picture_content_short"
                                                data-rule-extension="jpeg|jpg|png"
                                                data-msg-extension="Only file with extension jpeg, jpg or png allowed"
                                                data-rule-accept="image/*" data-msg-accept="Only Image file allowed"
                                                data-rule-maxsize="2097152"
                                                data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)."
                                                data-rule-required="true" data-msg-required="Image is required">
                                        </fieldset>
                                        <fieldset class="form-group">
                                            <input class="form-control" name="claim_content_product_cost"
                                                id="claim_content_product_cost" value=""
                                                placeholder="Enter Actual Missing Product Cost">
                                        </fieldset>
                                    </div>

                                    @foreach ($case_nature_type_claims as $claim)
                                        @if ($claim->remarks_visibility == 1)
                                            <div class="col-10 d-none" id="case_nature_claim_remarks_div">
                                                <fieldset class="form-group">
                                                    <select name="description[]" id="case_nature_claim_remarks" class="form-control select2" multiple="multiple" data-rule-required="true" data-msg-required="Claim remarks is required.">

                                                    </select>
                                                </fieldset>
                                            </div>
                                        @else
                                            <div class="col-10 d-none" id="claim_description_div_new">
                                                <fieldset class="form-group">
                                                    <textarea class="form-control" name="description[]" id="claim_description_new" rows="5"
                                                        placeholder="Enter Description Here..."></textarea>
                                                </fieldset>
                                            </div>
                                        @endif
                                    @endforeach

                                    <div class="col-10 d-none" id="claim_description_div">
                                        <fieldset class="form-group">
                                            <textarea class="form-control" name="description[]" id="claim_description" rows="5"
                                                placeholder="Enter Description Here..."></textarea>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <button id="AddNewRequest" type="submit"
                                        class="btn btn-primary btn-block d-none">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Call History Modal --}}
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
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">

    <style>
        .selectize-control {
            width: 305px !important;
        }

        .tracking_numbers {
            width: 100% !important;
        }
    </style>
@endsection

@section('js')
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/tags/tagging.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/extensions/sweetalert.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js') }}"
        type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            function print(id) {
                $.ajax({
                        url: '{!! route('cod.shipment.book.print_air_waybill') !!}',
                        method: 'POST',
                        data: {
                            'ids[]': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                    .done(function(data) {
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
            $('#claim_product_cost').inputmask({
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

            $('.new_amount').inputmask({
				'alias': 'integer',
				'allowMinus': false,
				'allowPlus': false
			});

            $('.cod_parcel_value').inputmask({
				'alias': 'integer',
				'allowMinus': false,
				'allowPlus': false
			});

            $('#complainant_phone').inputmask({
                mask: '9999-9999999',
                'clearIncomplete': true
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

            $('#case_nature_complainant').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: "Select Complainant",
                allowClear: true,
                dropdownParent: $('#add_request_form')
            });
            @if (app('request')->has('tracking_number'))
                track({{ app('request')->input('tracking_number') }});
            @endif

            function track(tracking_numbers) {
                $.ajax({
                        url: '{!! route('cod.tracking.track') !!}',
                        method: 'POST',
                        data: {
                            'tracking_numbers': tracking_numbers,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                    .done(function(data) {
                        select[0].selectize.clear();

                        $('#tracking').html('');

                        if (data.invalid !== undefined) {
                            var message = 'Invalid Tracking Number(s): ' + data.invalid.join(', ');

                            toastr.error(message, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }

                        if (data.disallowed !== undefined) {
                            var message = 'Following Tracking Number(s) don\'t belong to you: ' + data
                                .disallowed.join(', ');

                            toastr.error(message, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }

                        if (data.shipments != undefined) {
                            $.each(data.shipments, function(id, details) {
                                var $international_tracking_number = '';
                                var shipment = '';

                                if (details.international_shipment) {
                                    $international_tracking_number = ' <span>(' + details
                                        .international_tracking_number + ')</span> ';
                                }
                                shipment += '<div class="mt-4 border-primary">';
                                shipment += '<div class="d-flex align-items-center bg-primary">';
                                shipment += '<div class="mb-0 ml-1 font-medium-3 white">' + details
                                    .tracking_number + $international_tracking_number + '</div>';
                                shipment += '<div class="mb-0 ml-1">  ' + details.received_img +
                                    '  </div>';
                                shipment +=
                                    '<button class="btn btn-secondary ml-auto mr-0 mr-sm-1  add_request" id=' +
                                    id + ' data-tracking=' + details.tracking_number +
                                    '>Get Support</button>';
                                shipment +=
                                    '<button class="btn btn-secondary ml-0 mr-1 mr-sm-1 geo_codes" id=' +
                                    id + ' data-tracking=' + details.tracking_number +
                                    '>Provide Geocodes</button>';
                                shipment +=
                                    '<button class="btn btn-secondary ml-0 mr-1 mr-sm-1 call_status" id=' +
                                    id + ' data-tracking=' + details.tracking_number +
                                    '>Call History</button>';
                                if (details.pod_file) {

                                    shipment +=
                                        '<button class="btn btn-secondary mr-sm-1 d-sm-inline-block print" id=' +
                                        id + '>Print</button>';
                                    shipment +=
                                        '<a class="btn btn-secondary d-sm-inline-block file" href="' +
                                        details.pod_file + '" target="_blank" id=' + id +
                                        '><i class="la la-lg la-image align-middle"></i> POD File</a>';

                                } else {
                                    shipment +=
                                        '<button class="btn btn-secondary d-sm-inline-block print" id=' +
                                        id + '>Print</button>';

                                }

                                shipment += '</div>';

                                shipment += '<div class="p-1">';
                                shipment += '<div class="row justify-content-between">';

                                shipment += '<div class="col-12">';
                                shipment += '<h4><u>Shipper Information</u></h4>';
                                shipment += '<div class="border table-responsive">';
                                shipment += '<table class="table table-sm table-borderless mb-0">';
                                shipment += '<tbody>';

                                shipment += '<tr>';
                                shipment += '<td><strong>Name</strong></td>';
                                // shipment += '<td>' + details.shipper.name + '</td>';

                                var user_id = details.shipper.id;
                                var global_settings_userId = details.shipper.assigned;
                                if (user_id && global_settings_userId && global_settings_userId.length > 0 && global_settings_userId[0] != null) {
                                    var userIdArray = global_settings_userId[0].split(',');

                                    var isMatch = userIdArray.some(function(userIdString) {
                                        return userIdString === user_id.toString();
                                    });

                                    if (isMatch) {
                                        shipment += '<td>' + details.shipper.name + '</td>';
                                    } else {
                                        if (details.pickup.vendor == null || details.pickup.vendor == ''){
                                            shipment += '<td>' + details.shipper.name + '</td>';
                                        } else {
                                            shipment += '<td>' + details.shipper.name + ' (' + details.pickup.vendor + ')' + '</td>';
                                        }
                                    }
                                } else {
                                    // shipment += '<td>' + details.shipper.name + '</td>';
                                    shipment += '<td>' + details.shipper.name + ' (' + details.pickup.vendor + ')' + '</td>';
                                }

                                shipment += '<td><strong>Account No.</strong></td>';
                                shipment += '<td>' + details.shipper.account_number + '</td>';
                                shipment += '<td><strong>City</strong></td>';
                                shipment += '<td>' + details.shipper.city + '</td>';
                                shipment += '</tr>';

                                shipment += '<tr>';
                                shipment += '<td><strong>Phone No(s).</strong></td>';

                                if (!details.shipper.phone_number_2) {
                                    shipment += '<td>' + details.shipper.phone_number_1 + '</td>';
                                } else {
                                    shipment += '<td>' + details.shipper.phone_number_1 + '<br/>' +
                                        details.shipper.phone_number_2 + '</td>';
                                }

                                shipment += '<td><strong>Email</strong></td>';
                                if (details.shipper.email) {
                                    shipment += '<td colspan="3">' + details.shipper.email + '</td>';
                                } else {
                                    shipment += '<td colspan="3"></td>'
                                }

                                shipment += '</tr>';

                                shipment += '</tbody>';
                                shipment += '</table>';
                                shipment += '</div>';
                                shipment += '</div>';

                                shipment += '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-5 mt-2">';
                                shipment += '<h4><u>Pickup Information</u></h4>';
                                shipment += '<div class="border table-responsive">';
                                shipment += '<table class="table table-sm table-borderless mb-0">';
                                shipment += '<tbody>';
                                shipment += '<tr>';
                                shipment += '<td><strong>Person of Contact</strong></td>';
                                shipment += '<td>' + details.pickup.person_of_contact + '</td>';

                                if (isMatch){
                                    // shipment += '<td><strong>Vendor</strong></td>';
                                    if (details.pickup.vendor) {
                                        // shipment += '<td>' + details.pickup.vendor + '</td>';
                                        shipment += '<td></td>'
                                    }
                                    // else {
                                    //     shipment += '<td></td>'
                                    // }
                                }
                                else {
                                    shipment += '<td><strong>Brand Name</strong></td>';
                                    if (details.pickup.pickup_brand_name) {
                                        shipment += '<td>' + details.pickup.pickup_brand_name + '</td>';
                                    } else {
                                        shipment += '<td></td>'
                                    }
                                }

                                // shipment += '<td><strong>Vendor</strong></td>';
                                // if (details.pickup.vendor) {
                                //     shipment += '<td>' + details.pickup.vendor + '</td>';
                                // } else {
                                //     shipment += '<td></td>'
                                // }

                                shipment += '</tr>';
                                shipment += '<tr>';
                                shipment += '<td><strong>Phone No.</strong></td>';
                                shipment += '<td>' + details.pickup.phone_number + '</td>';
                                shipment += '<td><strong>Origin</strong></td>';
                                shipment += '<td>' + details.pickup.origin + '</td>';
                                shipment += '</tr>';
                                shipment += '<tr>';
                                shipment += '<td><strong>Email</strong></td>';
                                if (details.pickup.email) {
                                    shipment += '<td colspan="3">' + details.pickup.email + '</td>';
                                } else {
                                    shipment += '<td colspan="3"></td>'
                                }
                                shipment += '</tr>';
                                shipment += '<tr>';
                                shipment += '<td><strong>Address</strong></td>';
                                shipment += '<td colspan="3">' + details.pickup.address + '</td>';
                                shipment += '</tr>';
                                shipment += '</tbody>';
                                shipment += '</table>';
                                shipment += '</div>';
                                shipment += '</div>';

                                shipment += '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-5 mt-2">';
                                shipment += '<h4><u>Consignee Information</u></h4>';
                                shipment += '<div class="border table-responsive">';
                                shipment += '<table class="table table-sm table-borderless mb-0">';
                                shipment += '<tbody>';
                                shipment += '<tr>';
                                shipment += '<td><strong>Consignee</strong></td>';
                                shipment += '<td>' + details.consignee.name + '</td>';
                                shipment += '<td><strong>Destination</strong></td>';
                                shipment += '<td>' + details.consignee.destination + '</td>';
                                shipment += '</tr>';
                                shipment += '<tr>';
                                shipment += '<td><strong>Phone No(s).</strong></td>';

                                if (!details.consignee.phone_number_2) {
                                    shipment += '<td>' + details.consignee.phone_number_1 + '</td>';
                                } else {
                                    shipment += '<td>' + details.consignee.phone_number_1 + '<br/>' +
                                        details.consignee.phone_number_2 + '</td>';
                                }


                                shipment += '<td colspan="2"></td>';
                                shipment += '</tr>';
                                shipment += '<td><strong>Email</strong></td>';
                                if (details.consignee.email) {
                                    shipment += '<td colspan="3">' + details.consignee.email + '</td>';
                                } else {
                                    shipment += '<td colspan="3"></td>'
                                }
                                shipment += '</tr>';
                                shipment += '<tr>';
                                shipment += '<td><strong>Address</strong></td>';
                                shipment += '<td colspan="3">' + details.consignee.address + '</td>';
                                shipment += '</tr>';
                                if (details.consignee.delivery_area) {

                                    shipment += '<tr>';
                                    shipment += '<td><strong>Delivery Area</strong></td>';
                                    shipment += '<td colspan="3">' + details.consignee.delivery_area +
                                        '</td>';
                                    shipment += '</tr>';
                                }

                                shipment += '</tbody>';
                                shipment += '</table>';
                                shipment += '</div>';
                                shipment += '</div>';

                                shipment += '<div class="col-12 mt-2">';
                                shipment += '<h4><u>Order Information</u></h4>';
                                shipment += '<div class="border table-responsive">';
                                shipment += '<table class="table table-sm table-borderless mb-0">';
                                shipment += '<tbody>';

                                $.each(details.order_information.items, function(index, item) {
                                    shipment += '<tr>';
                                    shipment += '<td><strong>Product Type</strong></td>';
                                    shipment += '<td>' + item.product_type + '</td>';
                                    shipment += '<td><strong>Description</strong></td>';
                                    shipment += '<td>' + ((item.description) ? item
                                        .description : '-') + '</td>';
                                    shipment += '<td><strong>Quantity</strong></td>';
                                    shipment += '<td>' + item.quantity + '</td>';
                                    shipment += '</tr>';
                                });

                                shipment += '<tr>';
                                shipment += '<td><strong>Weight</strong></td>';
                                shipment += '<td>' + details.order_information.weight + ' kg</td>';
                                shipment += '<td><strong>Shipping Mode</strong></td>';
                                shipment += '<td>' + details.order_information.shipping_mode + '</td>';
                                shipment += '<td><strong>Collection Amount</strong></td>';
                                shipment += '<td>Rs. ' + details.order_information.amount + '</td>';
                                shipment += '</tr>';

                                shipment += '<tr>';
                                shipment += '<td><strong>Order ID</strong></td>';
                                shipment += '<td>' + ((details.order_information.order_id) ? details
                                    .order_information.order_id : '-') + '</td>';
                                shipment += '<td><strong>Order Date</strong></td>';
                                shipment += '<td>' + ((details.order_information.order_date) ? details
                                    .order_information.order_date : '-') + '</td>';
                                shipment += '<td><strong>Instructions</strong></td>';

                                if (details.order_information.charges_mode_id) {
                                    shipment += '<td>' + ((details.order_information.instructions) ?
                                        details.order_information.instructions : '-') + '</td>';
                                } else {
                                    shipment += '<td colspan="3">' + ((details.order_information
                                            .instructions) ? details.order_information
                                        .instructions : '-') + '</td>';
                                }

                                shipment += '</tr>';
                                shipment += '<tr>';

                                if (details.order_information.charges_mode_id) {
                                    shipment += '<td><strong>Charges Mode</strong></td>';
                                    shipment += '<td>' + details.order_information.charges_mode +
                                        '</td>';
                                }
                                shipment += '<td><strong>Piece(s)</strong></td>';
                                shipment += '<td>' + details.order_information.pieces + '</td>';
                                shipment += '<td><strong>Business Category</strong></td>';
                                shipment += '<td>' + details.order_information.business_category + '</td>';
                                shipment += '</tr>';

                                // Sub segment of shipper
                                shipment += '<tr>';
                                shipment += '<td><strong>Sub Segment</strong></td>';
                                shipment += '<td>' + details.order_information.sub_segment + '</td>';
                                shipment += '</tr>';

                                shipment += '<tr>';
                                shipment += '<td><strong>Booking Channel</strong></td>';
                                shipment += '<td>' + details.order_information.channel + '</td>';
                                shipment += '</tr>';

                                shipment += '</tbody>';
                                shipment += '</table>';
                                shipment += '</div>';
                                shipment += '</div>';

                                shipment += '<div class="col-12 mt-2">';
                                shipment += '<h4><u>Tracking History</u></h4>';
                                shipment += '<div class="border table-responsive">';

                                shipment +=
                                    '<table class="table table-sm table-borderless datatable tracking_history">';
                                shipment += '<thead>';
                                shipment += '<tr role="row">';
                                shipment += '<th><strong>Date / Time</strong></th>';
                                shipment += '<th><strong>Status</strong></th>';
                                shipment += '<th><strong>Details</strong></th>';
                                shipment += '<th><strong>Reason</strong></th>';
                                shipment += '<th><strong>Remarks</strong></th>';
                                shipment += '<th><strong>Received/Refused By</strong></th>';
                                shipment += '</tr>';
                                shipment += '</thead>';
                                shipment += '<tbody>';

                                $.each(details.tracking_history, function(index, history) {
                                    shipment += '<tr>';
                                    shipment += '<td>' + history.date_time + '</td>';
                                    shipment += '<td>' + history.status + '</td>';
                                    shipment += '<td>' + 
                                    (history.image_audio_location !== undefined ? history.image_audio_location : '-') + '|' + 
                                    (history.responsible && history.responsible.length > 0 ? 
                                        '<button class="btn btn-sm btn-outline-info align-middle responsible_person_shipment" data-shipment-id="' + id + '" data-journey_updated_at="' + history.responsible[0].journey_updated_at + '">' + 'Responsibles (' + history.responsible.length + ') </button>' :
                                        '-'
                                    ) +
                                    '</td>';
                                    shipment += '<td>' + ((history.status_reason) ? history
                                        .status_reason : '') + '</td>';
                                    shipment += '<td>' + history.status_remarks + '</td>';
                                    shipment += '<td>' + history.received_or_refused_by +
                                        '</td>';
                                    shipment += '</tr>';
                                });

                                shipment += '</tbody>';
                                shipment += '</table>';

                                shipment += '</div>';
                                shipment += '</div>';

                                if ('payment_history' in details) {
                                    shipment += '<div class="col-12 mt-2">';
                                    shipment += '<h4><u>Payment History</u></h4>';
                                    shipment += '<div class="border table-responsive">';

                                    shipment +=
                                        '<table class="table table-sm table-borderless datatable payment_history">';
                                    shipment += '<thead>';
                                    shipment += '<tr role="row">';
                                    shipment += '<th><strong>Date / Time</strong></th>';
                                    shipment += '<th><strong>Status</strong></th>';
                                    shipment += '<th><strong>User</strong></th>';
                                    shipment += '<th><strong>Remarks</strong></th>';
                                    shipment += '</tr>';
                                    shipment += '</thead>';
                                    shipment += '<tbody>';

                                    $.each(details.payment_history, function(index, history) {
                                        shipment += '<tr>';
                                        shipment += '<td>' + history.date_time + '</td>';
                                        shipment += '<td>' + history.status + '</td>';
                                        shipment += '<td>' + history.user + '</td>';
                                        shipment += '<td>' + history.payable_remarks + '</td>';
                                        shipment += '</tr>';
                                    });

                                    shipment += '</tbody>';
                                    shipment += '</table>';

                                    shipment += '</div>';
                                    shipment += '</div>';
                                }

                                if ('pickup_history_v2' in details) {
                                    shipment += '<div class="col-12 mt-2">';
                                    shipment += '<h4><u>Pickup History</u></h4>';
                                    shipment += '<div class="border table-responsive">';

                                    shipment +=
                                        '<table class="table table-sm table-borderless datatable pickup_history">';
                                    shipment += '<thead>';
                                    shipment += '<tr role="row">';
                                    shipment += '<th><strong>Date / Time</strong></th>';
                                    shipment += '<th><strong>Status</strong></th>';
                                    shipment += '<th><strong>Reason</strong></th>';
                                    shipment += '<th><strong>User</strong></th>';
                                    shipment += '</tr>';
                                    shipment += '</thead>';
                                    shipment += '<tbody>';

                                    $.each(details.pickup_history_v2, function(index, history) {
                                        shipment += '<tr>';
                                        shipment += '<td>' + history.date_time + '</td>';
                                        shipment += '<td>' + history.status + '</td>';
                                        shipment += '<td>' + history.reason + '</td>';
                                        shipment += '<td>' + history.user + '</td>';
                                        shipment += '</tr>';
                                    });

                                    shipment += '</tbody>';
                                    shipment += '</table>';

                                    shipment += '</div>';
                                    shipment += '</div>';
                                }
                                else if ('pickup_history_v3' in details) {
                                    shipment += '<div class="col-12 mt-2">';
                                    shipment += '<h4><u>Pickup History</u></h4>';
                                    shipment += '<div class="border table-responsive">';

                                    shipment +=
                                        '<table class="table table-sm table-borderless datatable pickup_history">';
                                    shipment += '<thead>';
                                    shipment += '<tr role="row">';
                                    shipment += '<th><strong>Date / Time</strong></th>';
                                    shipment += '<th><strong>Status</strong></th>';
                                    shipment += '<th><strong>Reason</strong></th>';
                                    shipment += '<th><strong>User</strong></th>';
                                    shipment += '</tr>';
                                    shipment += '</thead>';
                                    shipment += '<tbody>';

                                    $.each(details.pickup_history_v3, function(index, history) {
                                        shipment += '<tr>';
                                        shipment += '<td>' + history.date_time + '</td>';
                                        shipment += '<td>' + history.status + '</td>';
                                        shipment += '<td>' + history.reason + '</td>';
                                        shipment += '<td>' + history.user + '</td>';
                                        shipment += '</tr>';
                                    });

                                    shipment += '</tbody>';
                                    shipment += '</table>';

                                    shipment += '</div>';
                                    shipment += '</div>';
                                }

                                shipment += '</div>';
                                shipment += '</div>';


                                shipment += '</div>';

                                $('#tracking').append(shipment);
                                $('#old_amount').val(details.order_information.amount);
                            });

                            $('#tracking table.datatable.tracking_history').DataTable({
                                dom: 't',
                                paging: false,
                                order: [
                                    [0, 'desc']
                                ],
                                columns: [{
                                        name: 'date_time',
                                        class: 'align-middle date_time'
                                    },
                                    {
                                        name: 'status',
                                        class: 'align-middle status'
                                    },
                                    {
                                        name: 'image_audio_location',
                                        class: 'align-middle image_audio_location'
                                    },
                                    {
                                        name: 'reason',
                                        class: 'align-middle reason'
                                    },
                                    {
                                        name: 'status_remarks',
                                        class: 'align-middle status_remarks'
                                    },
                                    {
                                        name: 'received_or_refused_by',
                                        class: 'align-middle received_or_refused_by'
                                    }
                                ]
                            });

                            $('#tracking table.datatable.payment_history').DataTable({
                                dom: 't',
                                paging: false,
                                order: [
                                    [0, 'desc']
                                ],
                                columns: [{
                                        name: 'date_time',
                                        class: 'align-middle date_time'
                                    },
                                    {
                                        name: 'status',
                                        class: 'align-middle status'
                                    },
                                    {
                                        name: 'user',
                                        class: 'align-middle user'
                                    },
                                    {
                                        name: 'payable_remarks',
                                        class: 'align-middle payable_remarks'
                                    }
                                ]
                            });
                        }
                    });
            }

            $('#track_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('form'));
                },
                submitHandler: function(form) {

                    track($(form).find('.tracking_numbers').val());


                    return false;
                }
            });

            $('#tracking').on('click', '.print', function() {

                // alert('1');
                id = $(this).attr('id');

                print(id);
            });
            $("#tracking").on('click','.geo_codes',function (){
                // Form reset
                $("#add_geo_code_form")[0].reset();
                // Validation reset
                $("#add_geo_code_form").validate().resetForm();
                // Danger class remove
                $("#add_geo_code_form").find(".danger").removeClass("danger");
                $('#lat').val('');
                $('#long').val('');

                id = $(this).attr('id');
                var tracking = $(this).attr('data-tracking');
                var tracking_rows =
                    '<div class="col-4"><span class="mr-1"><i class="la la-angle-right align-bottom"></i><b> ' +
                    tracking + '</b></span></div>';

                $('#geo_code_shipment_id').val(id);
                $('#geo_code_shipments').html(tracking_rows);
                $.ajax({
                    url: "{{ route('cod.tracking.get_shipment_geo_codes') }}",
                    type: "POST",
                    data: {
                        shipment_id: id,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (res) {
                        if(res.status === 0){
                            if(res.lat) $("#lat").val(res.lat);
                            if(res.long) $("#long").val(res.long);
                        }
                        $('#AddGeoCodeModal').modal('show'); // modal ab show hoga
                    },
                    error: function () {
                        $('#AddGeoCodeModal').modal('show'); // error pe bhi modal dikhana ho
                    }
                });
                // $('#AddGeoCodeModal').modal('show');
            });

            // $( "#add_geo_code_form" ).validate({
            //     errorClass:"danger",
            //     normalizer: function(value) {
            //         return $.trim(value);
            //     },
            //     errorPlacement: function(error, element) {
            //         error.addClass('w-100').appendTo(element.parent('.form-group'));
            //     },
            //     submitHandler: function(form) {
            //
            //         form.submit();
            //
            //     }
            // });
            $("#add_geo_code_form").validate({
                errorClass:"danger",
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $.ajax({
                        url: "{{ route('cod.tracking.update_geo_codes') }}", // tumhara update route
                        type: "POST",
                        data: $(form).serialize(), // form ke sare fields bhej do
                        success: function(res) {
                            if (res.status === 0) {
                                toastr.success(res.message); // success msg
                                $('#AddGeoCodeModal').modal('hide');

                                // Reset form
                                $("#add_geo_code_form")[0].reset();
                                $("#add_geo_code_form").validate().resetForm();
                                $("#add_geo_code_form").find(".danger").removeClass("danger");
                            } else {
                                toastr.error(res.error); // validation ya custom error
                            }
                        },
                        error: function() {
                            toastr.error("Something went wrong!");
                        }
                    });
                    return false; // normal submit rokne ke liye
                }
            });


            $('#tracking').on('click', '.add_request', function() {
                id = $(this).attr('id');
                var tracking = $(this).attr('data-tracking');
                var tracking_rows =
                    '<div class="col-4"><span class="mr-1"><i class="la la-angle-right align-bottom"></i><b> ' +
                    tracking + '</b></span></div>';
                $('#requested_shipment_id').val(id);
                $('#requested_shipments').html(tracking_rows);
                $('#AddRequestModal').modal('show');
            });
            $('#tracking').on('click', '.rider_information', function() {
                id = $(this).attr('data-id');
                var showRiderResponseBtn = $(this).attr('data-showRiderRespone');
                var note = $(this).attr('data-note');

                $.ajax({
                        url: '{!! route('cod.tracking.rider_information') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'id': id
                        }
                    })
                    .done(function(data) {
                        var details = '<table class="table table-sm table-bordered"><tbody>';

                        details +=
                            '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Name</strong></td><td class="align-middle text-center">' +
                            data.name + '</td></tr>';
                        details +=
                            '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Phone Number</strong></td><td class="align-middle text-center">' +
                            data.phone_number + '</td></tr>';
                        details +=
                            '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>City</strong></td><td class="align-middle text-center">' +
                            data.city + '</td></tr>';
                        details +=
                            '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Category</strong></td><td class="align-middle text-center">' +
                            data.category + '</td></tr>';
                        details +=
                            '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Route</strong></td><td class="align-middle text-center">' +
                            data.route + '</td></tr>';
                        if (showRiderResponseBtn != undefined) {
                            details += '<tr data-id="' + data.id + '" data-note="' + note +
                                '"><td class="align-middle text-center"><button type="button" class="btn btn-warning btnRiderResponsiveStatus" data-type="1">Unresponsive</button></td><td class="align-middle text-center"><button type="button" class="btn btn-danger btnRiderResponsiveStatus" data-type="2">Powered Off</button></td></tr>';
                        }

                        details += '</tbody></table>';

                        $('#rider_information .modal-body').html(details);

                        $('#rider_information').modal('show');
                    });
            });
            // $('#case_nature_select').prepend('<option value="" selected="selected"></option>').select2({
            //     width: '100%',
            //     placeholder: "Select Case Nature",
            //     allowClear: true,
            //     dropdownParent: $('#add_request_form')
            // }).bind('change', function() {
            //     var id = parseInt($(this).val());
            //     if (id === 1) {
            //         $('#request_service').addClass('d-none');
            //         $('#request_complaints').removeClass('d-none');
            //         $('#request_feedback').addClass('d-none');
            //         $('#AddNewRequest').removeClass('d-none');
            //         $('#request_claims').addClass('d-none');
            //     } else if (id === 2) {
            //         $('#request_complaints').addClass('d-none');
            //         $('#request_service').removeClass('d-none');
            //         $('#request_feedback').addClass('d-none');
            //         $('#AddNewRequest').removeClass('d-none');
            //         $('#request_claims').addClass('d-none');
            //     } else if (id === 3) {
            //         $('#request_complaints').addClass('d-none');
            //         $('#request_service').addClass('d-none');
            //         $('#request_feedback').removeClass('d-none');
            //         $('#AddNewRequest').removeClass('d-none');
            //         $('#request_claims').addClass('d-none');
            //     } else if (id === 4) {
            //         $('#request_complaints').addClass('d-none');
            //         $('#request_service').addClass('d-none');
            //         $('#request_feedback').addClass('d-none');
            //         $('#request_claims').removeClass('d-none');
            //         $('#AddNewRequest').removeClass('d-none');
            //     } else {
            //         $('#request_complaints').addClass('d-none');
            //         $('#request_service').addClass('d-none');
            //         $('#AddNewRequest').addClass('d-none');
            //         $('#request_claims').addClass('d-none');
            //         $('#case_nature_remarks_div').addClass('d-none');
            //         $('#case_nature_service_remarks_div').addClass('d-none');
            //         $('#case_nature_claim_remarks').addClass('d-none');
            //     }
            // });

            $('#case_nature_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: "Select Case Nature",
                allowClear: true,
                dropdownParent: $('#add_request_form')
            }).on('change', function() {
                var id = parseInt($(this).val());
                $('#AddNewRequest').attr('disabled',false);
                $('#case_nature_claim').val('').trigger('change');
                $('#case_nature_complaints').val('').trigger('change');
                $('#case_nature_requests').val('').trigger('change');
                $('#claim_product_cost').val('');
                $('#claim_description_new').val('');
                var shipment_id = $('#requested_shipment_id').val();
                $.ajax({
                    url: '{{ route('cod.tracking.shipper_visibility') }}',
                    type: 'POST',
                    data: {
                        'id': id,
                        'shipment_id': shipment_id,
                    },
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (id === 1) {
                            $('#request_service').addClass('d-none');
                            $('#request_complaints').removeClass('d-none');
                            $('#request_feedback').addClass('d-none');
                            $('#AddNewRequest').removeClass('d-none');
                            $('#request_claims').addClass('d-none');

                            // Update options based on response.case_nature_type_complaints
                            updateOptions('#case_nature_complaints', response.case_nature_types);
                        } else if (id === 2) {
                            $('#request_complaints').addClass('d-none');
                            $('#request_service').removeClass('d-none');
                            $('#request_feedback').addClass('d-none');
                            $('#AddNewRequest').removeClass('d-none');
                            $('#request_claims').addClass('d-none');

                            // Update options based on response.case_nature_type_service_requests
                            updateOptions('#case_nature_requests', response.case_nature_types);
                        } else if (id === 3) {
                            $('#request_complaints').addClass('d-none');
                            $('#request_service').addClass('d-none');
                            $('#request_feedback').removeClass('d-none');
                            $('#AddNewRequest').removeClass('d-none');
                            $('#request_claims').addClass('d-none');

                            // You can add a similar update here if needed
                        } else if (id === 4) {
                            $('#request_complaints').addClass('d-none');
                            $('#request_service').addClass('d-none');
                            $('#request_feedback').addClass('d-none');
                            $('#request_claims').removeClass('d-none');
                            $('#AddNewRequest').removeClass('d-none');

                            // Update options based on response.case_nature_type_claims
                            updateOptions('#case_nature_claim', response.case_nature_types);
                        } else {
                            $('#request_complaints').addClass('d-none');
                            $('#request_service').addClass('d-none');
                            $('#AddNewRequest').addClass('d-none');
                            $('#request_claims').addClass('d-none');
                            $('#case_nature_remarks_div').addClass('d-none');
                            $('#case_nature_service_remarks_div').addClass('d-none');
                            $('#case_nature_claim_remarks').addClass('d-none');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                    }
                });
            });

            function updateOptions(selectElementId, optionsData) {
                var $select = $(selectElementId);
                // Clear existing options
                //$select.empty();
                $select.find('option:not(:first)').remove();
                // Append new options
                $.each(optionsData, function(index, option) {
                    $select.append($('<option>', {
                        value: option.id,
                        text: option.type
                    }));
                });
                // Trigger change event
                $select.trigger('change');
            }

            // complaints
            var isComplainChange = false;
            $('#case_nature_remarks').select2({
                width: '100%',
                placeholder: "Select Remarks",
                allowClear: true,
                dropdownParent: $('#add_request_form')
            });

            $('#case_nature_complaints').on('change', function() {
                var complaintId = $(this).val();
                $('#case_nature_remarks').empty();
                $('#complaint_description_textarea_new').addClass('d-none');
                $('#case_nature_remarks_div').addClass('d-none');
                if (complaintId) {
                    $.ajax({
                        url: '{{ route('cod.tracking.case_nature_remarks') }}',
                        type: 'POST',
                        data: { complaint_id: complaintId },
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            var $remarksDropdown = $('#case_nature_remarks');
                            $remarksDropdown.empty();
                            $.each(response.data, function(index, item) {
                                $remarksDropdown.append('<option value="' + item.id + '">' + item.remarks + '</option>');
                            });
                            $remarksDropdown.append('<option value="0">Others</option>');

                            if (response.remarks_visibility == 1) {
                                $('#case_nature_remarks_div').removeClass('d-none');
                                $('#complaint_description_textarea').addClass('d-none');
                            } else {
                                $('#case_nature_remarks_div').addClass('d-none');
                                $('#complaint_description_textarea').removeClass('d-none');
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error('An error occurred while fetching remarks:', textStatus, errorThrown);
                        }
                    });
                } else {
                    $('#case_nature_remarks').empty().append('<option value="" selected="selected">Select Remarks</option>');
                    $('#case_nature_remarks_div').addClass('d-none');
                }
            });

            $('#case_nature_remarks').on('change', function() {
                // Important to stop recursive action
                if (isComplainChange) {
                    isComplainChange = false;
                    return;
                }
                var selectedValues = $(this).val();
                var $textareaDiv = $('#complaint_description_textarea');
                if (selectedValues && selectedValues.includes('0')) {
                    if (selectedValues.length > 1) {
                        selectedValues = selectedValues.filter(value => value !== '0');
                        $(this).val(selectedValues).trigger('change');
                        $textareaDiv.addClass('d-none');
                    } else {
                        $textareaDiv.removeClass('d-none');
                    }
                } else {
                    $textareaDiv.addClass('d-none');
                }
            });

            // service request
            var isServiceChange = false;
            $('#case_nature_service_remarks').select2({
                width: '100%',
                placeholder: "Select Remarks",
                allowClear: true,
                dropdownParent: $('#add_request_form')
            });

            $('#case_nature_requests').on('change', function() {
                var serviceId = $(this).val();
                $('#case_nature_service_remarks').empty();
                $('#case_nature_service_remarks_div').addClass('d-none');
                $('#service_description_textarea_new').addClass('d-none');
                $('#service_description_textarea').addClass('d-none');
                if (serviceId) {
                    $.ajax({
                        url: '{{ route('cod.tracking.case_nature_service_remarks') }}',
                        type: 'POST',
                        data: { service_id: serviceId },
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            var $remarksDropdown = $('#case_nature_service_remarks');
                            $remarksDropdown.empty();

                            $.each(response.data, function(index, item) {
                                $remarksDropdown.append('<option value="' + item.id + '">' + item.remarks + '</option>');
                            });
                            $remarksDropdown.append('<option value="0">Others</option>');

                            if (response.remarks_visibility == 1) {
                                $('#case_nature_service_remarks_div').removeClass('d-none');
                                $('#service_description_textarea_new').addClass('d-none');
                                $('#service_description_textarea').addClass('d-none');
                            } else {
                                $('#service_description_textarea_new').removeClass('d-none');
                                $('#service_description_textarea').removeClass('d-none');
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error('An error occurred while fetching remarks:', textStatus, errorThrown);
                        }
                    });
                } else {
                    $('#case_nature_service_remarks').empty().append('<option value="" selected="selected">Select Remarks</option>');
                    $('#case_nature_service_remarks_div').addClass('d-none');
                    $('#service_description_textarea_new').addClass('d-none');
                    $('#service_description_textarea').addClass('d-none');
                }
            });

            $('#case_nature_service_remarks').on('change', function() {
                // Important to stop recursive action
                if (isServiceChange) {
                    isServiceChange = false;
                    return;
                }
                var selectedValues = $(this).val();
                var $textareaDiv = $('#service_description_textarea');
                if (selectedValues && selectedValues.includes('0')) {
                    if (selectedValues.length > 1) {
                        selectedValues = selectedValues.filter(value => value !== '0');
                        $(this).val(selectedValues).trigger('change');
                        $textareaDiv.addClass('d-none');
                    } else {
                        $textareaDiv.removeClass('d-none');
                    }
                } else {
                    $textareaDiv.addClass('d-none');
                }
            });

            // claim
            var isClaimChange = false;
            $('#case_nature_claim_remarks').select2({
                width: '100%',
                placeholder: "Select Remarks",
                allowClear: true,
                dropdownParent: $('#add_request_form')
            });

            $('#case_nature_claim').on('change', function() {
                var claimId = $(this).val();
                $('#case_nature_claim_remarks').empty();
                $('#case_nature_claim_remarks_div').addClass('d-none');
                $('#claim_description_div_new').addClass('d-none');
                $('#claim_description_div').addClass('d-none');

                if (claimId) {
                    $.ajax({
                        url: '{{ route('cod.tracking.case_nature_claim_remarks') }}',
                        type: 'POST',
                        data: { claim_id: claimId },
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            var $remarksDropdown = $('#case_nature_claim_remarks');
                            $remarksDropdown.empty();

                            $.each(response.data, function(index, item) {
                                $remarksDropdown.append('<option value="' + item.id + '">' + item.remarks + '</option>');
                            });
                            $remarksDropdown.append('<option value="0">Others</option>');

                            if (response.remarks_visibility == 1) {
                                $('#case_nature_claim_remarks_div').removeClass('d-none');
                                $('#claim_description_div_new').addClass('d-none');
                                $('#claim_description_div').addClass('d-none');
                            } else {
                                $('#claim_description_div_new').addClass('d-none');
                                $('#claim_description_div').removeClass('d-none');
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error('An error occurred while fetching remarks:', textStatus, errorThrown);
                        }
                    });
                } else {
                    $('#case_nature_claim_remarks').empty().append('<option value="" selected="selected">Select Remarks</option>');
                    $('#case_nature_claim_remarks_div').addClass('d-none');
                    $('#claim_description_div_new').addClass('d-none');
                    $('#claim_description_div').addClass('d-none');
                }
            });

            $('#case_nature_claim_remarks').on('change', function() {
                // Important to stop recursive action
                if (isClaimChange) {
                    isClaimChange = false;
                    return;
                }
                var selectedValues = $(this).val();
                var $textareaDiv = $('#claim_description_div');
                if (selectedValues && selectedValues.includes('0')) {
                    if (selectedValues.length > 1) {
                        selectedValues = selectedValues.filter(value => value !== '0');
                        $(this).val(selectedValues).trigger('change');
                        $textareaDiv.addClass('d-none');
                    } else {
                        $textareaDiv.removeClass('d-none');
                    }
                } else {
                    $textareaDiv.addClass('d-none');
                }
            });

            var lost_flag = true;
            $('#case_nature_claim').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: "Select Claim Type",
                allowClear: true,
                dropdownParent: $('#add_request_form')
            }).bind('select2:select', function() {
                var id = parseInt($(this).val());
                if (this.value && this.value == 23 && lost_flag === true) {
                    $('#receiving_sheet_div').removeClass('d-none');
                    var shipment_id = $('#requested_shipment_id').val();
                    $.ajax({
                        url: '{!! route('cod.crm.request.lost.claim') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'shipment_id': shipment_id,
                        }
                    }).done(function(data) {
                        $('#request_id').empty().trigger('change');
                        $('#request_id').prepend('<option value="" selected="selected"></option>')
                            .select2({
                                width: '100%',
                                placeholder: "Select Receiving Sheet ID",
                                allowClear: true,
                                dropdownParent: $('#add_request_form')
                            });
                        if (data.status == 1) {
                            var newOption = new Option(data.receiving_sheet_id, data
                                .receiving_sheet_id, false, false);
                            $('#request_id').append(newOption).trigger('change');

                        } else {
                            lost_flag = true;
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                            $('#AddNewRequest').attr('disabled', true);
                        }
                    });
                } else {
                    lost_flag = true;
                    $('#receiving_sheet_div').addClass('d-none');
                    $('#AddNewRequest').attr('disabled', false);

                }
                if (this.value && this.value == 21) {
                    $('#claim_shipment_damage_div').removeClass('d-none');
                } else {
                    $('#claim_shipment_damage_div').addClass('d-none');
                }

                if (this.value && this.value == 22) {
                    $('#claim_content_short_div').removeClass('d-none');
                } else {
                    $('#claim_content_short_div').addClass('d-none');
                }

                if (id === 26) {
                    $('#claim_product_cost_div').addClass('d-none');
                    $('#claim_product_picture_div').addClass('d-none');
                    $('#claim_invoice_picture_div').addClass('d-none');
                } else {
                    $('#claim_product_cost_div').removeClass('d-none');
                    $('#claim_product_picture_div').removeClass('d-none');
                    $('#claim_invoice_picture_div').removeClass('d-none');
                }

            });

            var max_char_request = 245;
            $('#feedback_description').on('keypress copy paste', function(e) {
                if ($(this).val().length == max_char_request) {
                    e.preventDefault();
                } else if ($(this).val().length > max_char_request) {
                    // Maximum exceeded
                    this.value = this.value.substring(0, max_char_request);
                }
            });
            $('#service_description').on('keypress copy paste', function(e) {
                if ($(this).val().length == max_char_request) {
                    e.preventDefault();
                } else if ($(this).val().length > max_char_request) {
                    // Maximum exceeded
                    this.value = this.value.substring(0, max_char_request);
                }
            });
            $('#complaint_description').on('keypress copy paste', function(e) {
                if ($(this).val().length == max_char_request) {
                    e.preventDefault();
                } else if ($(this).val().length > max_char_request) {
                    // Maximum exceeded
                    this.value = this.value.substring(0, max_char_request);
                }
            });
            $('#case_nature_complaints').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: "Select Complaint Type",
                allowClear: true,
                dropdownParent: $('#add_request_form')
            });
            $('#case_nature_requests').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: "Select Request Type",
                allowClear: true,
                dropdownParent: $('#add_request_form')
            });

            $('body').on('change', '#add_request_form textarea', function() {
                $(this).val($(this).val().trim());
            });
            $('#add_request_form').on('submit', function(e) {
                e.preventDefault();
            });

            $('#case_nature_requests').on('change', function(e) {

                if ($(this).val() == 13) {
                    $('#alternate_phone_input').removeClass('d-none');
                    // $('#cod_amount_input').addClass('d-none');

                }
                else{
                    // $('#cod_amount_input').addClass('d-none');
                    $('#alternate_phone_input').addClass('d-none');

                }

                if($(this).val() == 12){
                    $('#cod_change').removeClass('d-none');
                }
                else{
                    $('#cod_change').addClass('d-none');

                }
            });

            // Form submit
            $("#add_request_form").validate({
                errorClass: "danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {



                    var case_nature_id = parseInt($('#case_nature_select').val());
                    // if (case_nature_id === 1) {
                    //     var complaint_id = $('#case_nature_complaints').val();
                    //     var description = $('#complaint_description').val();
                    // }

                    if (case_nature_id === 1) {
                        var complaint_id = $('#case_nature_complaints').val();
                        var description = "";
                        if (!$('#case_nature_remarks_div').hasClass('d-none')) {
                            var selectedOptions = $('#case_nature_remarks option:selected');
                            var useTextarea = false;
                            var selectedTexts = [];

                            selectedOptions.each(function() {
                                var optionValue = $(this).val();
                                var optionText = $(this).text().trim();
                                if (optionValue === '0') {
                                    useTextarea = true;
                                    return false;
                                } else {
                                    selectedTexts.push(optionText);
                                }
                            });

                            if (useTextarea) {
                                description = $('#complaint_description').val().trim();
                            } else {
                                description = selectedTexts.join(', ');
                                if(!description)
                                {
                                    description = $('#complaint_description').val().trim();
                                }
                            }
                        } else if (!$('#complaint_description_textarea').hasClass('d-none')) {
                            description = $('#complaint_description').val().trim();
                        }
                    }

                    else if (case_nature_id === 3) {
                        var feedback_flag = true;
                        var feedback_description = $('#feedback_description_request').val();
                    }

                    // else {
                    //     var complaint_id = $('#case_nature_requests').val();
                    //     var description = $('#service_description').val();
                    // }

                    else if (case_nature_id == 2) {
                        var alternate_phone = $('#alternate_phone').val();
                        var complaint_id = $('#case_nature_requests').val();
                        var description = "";
                        if (!$('#case_nature_service_remarks_div').hasClass('d-none')) {
                            var selectedOptions = $('#case_nature_service_remarks option:selected');
                            var useTextarea = false;
                            var selectedTexts = [];
                            selectedOptions.each(function() {
                                var optionValue = $(this).val();
                                var optionText = $(this).text().trim();
                                if (optionValue === '0') {
                                    useTextarea = true;
                                    return false;
                                } else {
                                    selectedTexts.push(optionText);
                                }
                            });

                            if (useTextarea) {
                                description = $('#service_description').val().trim();
                            } else {
                                description = selectedTexts.join(', ');
                                if(!description)
                                {
                                    description = $('#service_description').val().trim();
                                }

                            }
                        } else if (!$('#service_description_textarea_new').hasClass('d-none')) {
                            description = $('#service_description').val().trim();
                        }
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
                                        'shipment_id': $('#requested_shipment_id').val(),
                                        'description': feedback_description
                                    }
                                })
                                .done(function(data) {
                                    if (data.status) {
                                        if (data.flag) {
                                            var html = '';

                                            $.each(data.already_existed_shipments, function(index,
                                                tracking_number) {
                                                html += tracking_number + '<br/>';
                                            });

                                            if (!data.cannot_change) {
                                                html +=
                                                    '<br/>Request/Complaint already lodged for the above Shipment(s)!';
                                            } else {
                                                html +=
                                                    '<br/>Request for Change cannot be opened for the above Shipment(s) at the Current Status!';
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

                                    $('#AddRequestModal').modal('hide');
                                    $('#AddNewRequest').attr('disabled', false);
                                });
                        }
                    }

                    else if (case_nature_id === 4) {
                        var nature_flag = true;
                        var case_nature_claim_id = $('#case_nature_claim').val();
                        var product_cost = parseFloat($('#claim_product_cost').inputmask('unmaskedvalue'));
                        var check_product_picture = $('#product_picture').val();
                        var check_invoice_picture = $('#invoice_picture').val();
                        // var claim_description = $('#claim_description').val();
                        $('#shipment_ids').val($('#requested_shipment_id').val());
                        $('#case_nature_id').val(case_nature_id);
                        $('#complaint_id').val(case_nature_claim_id);
                        // $('#claim_description').val(claim_description);

                        var formData = new FormData($('#add_request_form')[0]);
                        var claim_description = '';
                        if ($('#case_nature_claim_remarks_div').length && !$('#case_nature_claim_remarks_div').hasClass('d-none')) {

                            var selectedOptions = $('#case_nature_claim_remarks option:selected');
                            var selectedTexts = [];
                            var useTextarea = false;
                            selectedOptions.each(function() {
                                if ($(this).val() == '0') {
                                    useTextarea = true;
                                    return false;
                                } else {
                                    selectedTexts.push($(this).text().trim());
                                }
                            });

                            if (useTextarea) {
                                var textarea = document.getElementById('claim_description');
                                claim_description = textarea.value.trim();
                            } else {
                                claim_description = selectedTexts.join(', ');
                            }
                        } else if (!$('#claim_description_div').hasClass('d-none')) {
                            var textarea = document.getElementById('claim_description');
                            claim_description = textarea.value.trim();
                        }
                        formData.append('description', claim_description);
                        $('#claim_description').val(claim_description);

                        // if(case_nature_claim_id === 17){
                        //     if($('#request_id').val() == "" || $('#request_id').val() == null){
                        //         nature_flag = false;
                        //         var error = "Please select receiving sheet!";
                        //         toastr.error(error, 'Error!', {
                        //             positionClass: 'toast-top-center',
                        //             containerId: 'toast-top-center'
                        //         });
                        //     }
                        // }
                        if (!case_nature_claim_id) {
                            nature_flag = false;
                            var error = "Please select Claim type!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                        if (!claim_description) {
                            nature_flag = false;
                            var error = "Either a claim description or remarks are required!";
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
                                var error = isNaN(product_cost) ? "Please enter Claim Amount!" : "Claim Amount cannot be zero !!";
                                toastr.error(error, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }
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
                                .done(function(data) {
                                    swal.close();

                                    if (data.status) {
                                        if (data.flag) {
                                            var html = '';

                                            $.each(data.already_existed_shipments, function(index,
                                                tracking_number) {
                                                html += tracking_number + '<br/>';
                                            });

                                            if (!data.cannot_change) {
                                                html +=
                                                    '<br/>Request/Complaint already lodged for the above Shipment(s)!';
                                            } else {
                                                html +=
                                                    '<br/>Request for Change cannot be opened for the above Shipment(s) at the Current Status!';
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

                                    $('#AddRequestModal').modal('hide');
                                    $('#request_id').val('').trigger('change');
                                    $('#receiving_sheet_div').addClass('d-none');
                                    $('#AddNewRequest').attr('disabled', false);
                                });
                        }
                    }

                    else {
                        $('#AddNewRequest').attr('disabled',true);


                            /*********
                            // Commented this because in Complain type = 1, the value set in different variable
                            // i.e: $('#case_nature_complaints').val();
                            // so this should be manage according to case nature except here, which seems like it already handled in first two,
                            // if required for three four, then adjust this on top like case nature
                            *********/
                            // var complaint_id = $('#case_nature_requests').val();

                            if(complaint_id == 12)
                            {
                                swal({
                                    title: 'Are You Sure?',
                                    text: 'Select Yes to change COD!',
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

                                        var is_zero_cod = 0;
                                        if($('#new_amount').val() == 0 && $('#new_amount').val() != '')
                                        {
                                            is_zero_cod = 1;
                                        }

                                        $.ajax({
                                            url: '{!! route('cod.crm.request.add') !!}',
                                            method: 'POST',
                                            data: {
                                                '_token': '{{ csrf_token() }}',
                                                'shipment_id': $('#requested_shipment_id').val(),
                                                'case_nature_id': case_nature_id,
                                                'complaint_id': complaint_id,
                                                'description': description,
                                                'complainant_phone' : $('#complainant_phone').val(),
                                                'case_nature_complainant' : $('#case_nature_complainant').val(),
                                                'cod_new_amount': $('#new_amount').val(),
                                                'cod_remarks': $('#cod_remarks').val(),
                                                'is_zero_cod': is_zero_cod,
                                                'cod_parcel_value': $('#cod_parcel_value').val(),
                                                'is_automated_cod_change': 1,
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
                                            $('#AddRequestModal').modal('hide');
                                            $('#request_id').val('').trigger('change');
                                            $('#receiving_sheet_div').addClass('d-none');
                                        });
                                    }
                                    else{
                                        $('#AddNewRequest').attr('disabled',false);
                                    }
                                });
                            }
                            else if (complaint_id == 39) {
                                swal({
                                    title: 'Are you sure to change service type?',
                                    text: 'Select Yes to change service type!',
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
                                }).then(function (confirm){
                                    if(confirm){
                                        $.ajax({
                                            url: '{!! route('cod.crm.request.add') !!}',
                                            method: 'POST',
                                            data: {
                                                '_token': '{{ csrf_token() }}',
                                                'shipment_id': $('#requested_shipment_id').val(),
                                                'case_nature_id': case_nature_id,
                                                'complaint_id': complaint_id,
                                                'description': description,
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
                                                $('#AddRequestModal').modal('hide');
                                                $('#request_id').val('').trigger('change');
                                                $('#receiving_sheet_div').addClass('d-none');
                                                $('#AddNewRequest').attr('disabled',false);
                                            });
                                    }
                                    else {
                                        $('#AddNewRequest').attr('disabled',false);
                                    }
                                });
                            }
                            else{
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
                                        'shipment_id': $('#requested_shipment_id').val(),
                                        'case_nature_id': case_nature_id,
                                        'complaint_id': complaint_id,
                                        'description': description,
                                        'complainant_phone' : $('#complainant_phone').val(),
                                        'case_nature_complainant' : $('#case_nature_complainant').val(),
                                        'alternate_phone': alternate_phone,
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
                                    $('#AddRequestModal').modal('hide');
                                    $('#request_id').val('').trigger('change');
                                    $('#receiving_sheet_div').addClass('d-none');
                                    $('#AddNewRequest').attr('disabled',false);
                                });
                            }
                    }
                }
            });

            $('#AddRequestModal').on('hide.bs.modal', function (e) {
                var old_amount = $('#old_amount').val();
                $('#add_request_form')[0].reset();
                $('#old_amount').val(old_amount);
                $('#cod_parcel_value_change').addClass('d-none');
                $('#case_nature_complaints').val('').trigger('change');
                $('#case_nature_select').val('').trigger('change');
                $('#case_nature_requests').val('').trigger('change');

                $('#complaint_description').val('');
                $('#service_description').val('');
                $('#request_complaints').addClass('d-none');

                $('#complaint_description_new').val('');
                $('#service_description_new').val('');
                $('#request_complaints_new').val('');

                $('#feedback_description_request').val('');
                $('#request_service').addClass('d-none');
                $('#request_feedback').addClass('d-none');
                $('#request_claims').addClass('d-none');
                $('#case_nature_claim').val('').trigger('change');
                $('#claim_channel').val('').trigger('change');
                $('#claim_product_cost').val('');
                $('#request_id').val('').trigger('change');
                $('#receiving_sheet_div').addClass('d-none');
                $('#alternate_phone_input').addClass('d-none');
                $('#alternate_phone').val('');
                // $('#cod_amount_input').addClass('d-none');
                // $('#cod_amount').val('');
                $('#AddNewRequest').attr('disabled',false);
                $('#case_nature_complainant').val('').trigger('change');
                $('#complainant_phone').val('');
            });

            $('#tracking').on('click', '.picture', function() {
                var pod_image = $(this).data('link');
                window.open(pod_image, "_blank")
            });
            $('#tracking').on('click', '.replacement_booked_image', function() {
                window.open($(this).data('link'), '_blank');

            });

            $('#tracking').on('click', '.replacement_collected_image', function() {
                window.open($(this).data('link'), '_blank');

            });


            $('#tracking').on('click', '.call_status', function() {
                var id = $(this).attr('id');
                var tracking = $(this).attr('data-tracking');
                var tracking_rows =
                    '<div class="col-4"><span class="mr-1"><i class="la la-angle-right align-bottom"></i><b> ' +
                    tracking + '</b></span></div>';
                $('#shipment_id').val(id);

                $('#call_history_modal .modal-body').html('');
                $('#call_history_modal').modal('show');

                $.ajax({
                        url: '{!! route('cod.tracking.call_status_history') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'shipment_id': id
                        }
                    })
                    .done(function(response) {
                        if (response) {
                            var modalBody = $('#call_history_modal .modal-body');
                            modalBody.html('');

                            var tableHtml =
                                '<table id="call_history_table" class="table-striped table-bordered" style="width:100%">';
                            tableHtml +=
                                '<thead class="text-center"><tr><th class="p-1">Calling Date</th><th>Calling Time</th><th>Call Finding</th><th>Call Finding Reason</th><th>Remarks</th><th>Call To</th><th>Status</th><th>User</th></tr></thead>';
                            tableHtml += '<tbody class="text-center">';
                            $.each(response, function(index, value) {
                                var updated_at = value.data.updated_at;
                                var trimmedDateTime = updated_at.substring(0, 10);
                                var trimmedTime = updated_at.substring(11, 19);
                                var call_finding_id = value.data.call_status ?? 'Not Connected';
                                var call_finding_reason_id = value.data.rv_call_finding.name;

                                var remarks = value.data.remarks;
                                if (remarks == null) {
                                    remarks = '-';
                                }
                                var current_shipment_status = value.data.shipment.status_shipper
                                    .name;
                                var updated_by = value.user_name;

                                var call_to_id = value.data.call_to_id;
                                if (call_to_id == 1) {
                                    call_to_id = 'Consignee'
                                } else {
                                    call_to_id = 'Shipper'
                                }

                                tableHtml += '<tr><td class="p-1">' + trimmedDateTime +
                                    '</td><td>' + trimmedTime + '</td><td>' + call_finding_id +
                                    '</td><td>' + call_finding_reason_id +
                                    '</td><td>' + remarks + '</td><td>' + call_to_id +
                                    '</td><td>' + current_shipment_status +
                                    '</td><td>' + updated_by + '</td></tr>';
                            });

                            tableHtml += '</tbody></table>';

                            modalBody.append(tableHtml);

                            $('#call_history_modal').modal('show');
                        }
                    });
            });

            $('#update_call_status_modal').on('shown.bs.modal', function() {
                $('#call_to').val('').change();
                $('#custom_remark').val('');
                $('#sub_status_call_finding').val('').change();
                $('#call_finding_dropdown').val('').change();
            });

            var textarea = $('#cod_remarks');

            textarea.on('input', function() {
                var wordLimit = 10;
                var textarea = $('#cod_remarks');
                var text = textarea.val();
                var words = text.trim().split(/\s+/); // Split the text into words

                if (words.length > wordLimit) {

                    words = words.slice(0, wordLimit); // Keep only the first 10 words
                    textarea.val(words.join(' ')); // Update the textarea value
                }
            });

            $('#new_amount').on('keyup', function () {

                var new_amount = $(this).val();

                if(new_amount == 0 && new_amount != '')
                {
                    $('#cod_parcel_value_change').removeClass('d-none');
                }
                else{
                    $('#cod_parcel_value_change').addClass('d-none');
                }

            });

		});
	</script>
@endsection
