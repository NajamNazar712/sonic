@extends('admin.layout.master')

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
							@include('admin.inc.messages')

							<form id="track_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
								<div class="form-group">
									<input type="text" name="tracking_numbers" class="tracking_numbers" placeholder="Tracking Number(s)*" data-tags-input-name="tracking_number" data-rule-required="true" data-msg-required="Tracking Number is required">
								</div>

								<div class="form-group ml-1">
									<button type="submit" name="track" class="btn btn-primary" value="Track">Track</button>
								</div>
							</form>

							<div class="tracking" id="tracking">
							</div>

							<div class="modal fade" id="rider_information" role="dialog" aria-labelledby="rider_information_title" aria-hidden="true">
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
											<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
										</div>
									</div>
								</div>
							</div>

							{{--<div class="modal fade" id="cargo_consignment_details" role="dialog" aria-labelledby="cargo_consignment_details_title" aria-hidden="true">--}}
								{{--<div class="modal-dialog modal-lg" role="document">--}}
									{{--<div class="modal-content">--}}
										{{--<div class="modal-header">--}}
											{{--<h4 class="modal-title" id="cargo_consignment_details_title">Cargo Consignment Details</h4>--}}

											{{--<button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
												{{--<span aria-hidden="true">×</span>--}}
											{{--</button>--}}
										{{--</div>--}}
										{{--<div class="modal-body">--}}
										{{--</div>--}}
										{{--<div class="modal-footer">--}}
											{{--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>--}}
										{{--</div>--}}
									{{--</div>--}}
								{{--</div>--}}
							{{--</div>--}}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

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
                        @csrf
                        <div class="container">
                            <div class="row">
                                <h2 class="heading">Tracking Number(s)</h2>
                            </div>

                            <input type="hidden" id="requested_shipment_id">
                            <div class="row old_scroll" id="requested_shipments">

                            </div>
                            <hr>
                            <div class="row justify-content-center">
                                <div class="col-8">
                                    <fieldset class="form-group">
                                        <select name="case_nature_select" id="case_nature_select" class="form-control select2">
                                            @foreach($case_nature as $nature)
                                                <option value="{{$nature->id}}">{{$nature->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="complaints d-none" id="request_complaints">
                                <div class="row justify-content-center">
                                    <div class="col-6">
                                        <fieldset class="form-group">
                                            <select name="case_nature_complaint" id="case_nature_complaints" class="form-control select2">
                                                @foreach($case_nature_complaints as $complaints)
                                                    <option value="{{$complaints->id}}">{{$complaints->type}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-6">
                                        <fieldset class="form-group">
                                            <select name="complaint_channel" id="complaint_channels" class="form-control select2">
                                                @foreach($case_nature_channels as $channel1)
                                                    <option value="{{$channel1->id}}">{{$channel1->channel}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-6">
                                        <fieldset class="form-group">
                                            <textarea class="form-control" name="complaint_description" id="complaint_description" rows="5" placeholder="Enter Description Here..."></textarea>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <div class="service d-none" id="request_service">
                                <div class="row justify-content-center">
                                    <div class="col-6">
                                        <fieldset class="form-group">
                                            <select name="case_nature_request" id="case_nature_requests" class="form-control select2">
                                                @foreach($case_nature_service_requests as $service)
                                                    <option value="{{$service->id}}">{{$service->type}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-6">
                                        <fieldset class="form-group">
                                            <select name="request_channel" id="request_channels" class="form-control select2">
                                                @foreach($case_nature_channels as $channel2)
                                                    <option value="{{$channel2->id}}">{{$channel2->channel}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-6">
                                        <fieldset class="form-group">
                                            <textarea class="form-control" name="service_description" id="service_description" rows="5" placeholder="Enter Description Here..."></textarea>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <div class="feedback d-none" id="request_feedback">
                                <div class="row justify-content-center">
                                    <div class="col-8">
                                        <fieldset class="form-group">
                                            <select name="feedback_channel_request" id="feedback_channel_request" class="form-control select2">
                                                @foreach($case_nature_channels as $channel1)
                                                    <option value="{{$channel1->id}}">{{$channel1->channel}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
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
                                <input type="hidden" name="channel_id" id="channel_id">
                                <div class="row justify-content-center">
                                    <div class="col-8">
                                        <fieldset class="form-group">
                                            <select name="case_nature_claim" id="case_nature_claim" class="form-control select2">
                                                @foreach($case_nature_type_claims as $claim)
                                                    <option value="{{$claim->id}}">{{$claim->type}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-8">
                                        <fieldset class="form-group">
                                            <select name="claim_channel" id="claim_channel" class="form-control select2">
                                                @foreach($case_nature_channels as $channel1)
                                                    <option value="{{$channel1->id}}">{{$channel1->channel}}</option>
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

{{--                                    <div class="col-8 text-left d-none" id="claim_shipment_damage_div">--}}
{{--                                        <fieldset class="form-group">--}}
{{--                                            <label for="damage_product_picture"><b>Damage Picture:</b></label>--}}
{{--                                            <input class="form-control form-control-sm" type="file" name="damage_product_picture" id="damage_product_picture" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">--}}
{{--                                        </fieldset>--}}
{{--                                        <fieldset class="form-group">--}}
{{--                                            <label for="product_packaging_picture"><b>Product Packaging Picture:</b></label>--}}
{{--                                            <input class="form-control form-control-sm" type="file" name="product_packaging_picture" id="product_packaging_picture" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">--}}
{{--                                        </fieldset>--}}
{{--                                        <fieldset class="form-group">--}}
{{--                                            <label for="actual_product_picture"><b>Actual Product Picture:</b></label>--}}
{{--                                            <input class="form-control form-control-sm" type="file" name="actual_product_picture" id="actual_product_picture" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">--}}
{{--                                        </fieldset>--}}
{{--                                        <fieldset class="form-group">--}}
{{--                                            <input class="form-control" name="damage_claim_product_cost" id="damage_claim_product_cost" value="" placeholder="Enter Actual Damaged Product Cost">--}}
{{--                                        </fieldset>--}}

{{--                                    </div>--}}

{{--                                    <div class="col-8 text-left d-none" id="claim_content_short_div">--}}
{{--                                        <fieldset class="form-group">--}}
{{--                                            <label for="missing_product_picture"><b>Missing Product Picture:</b></label>--}}
{{--                                            <input class="form-control form-control-sm" type="file" name="missing_product_picture" id="missing_product_picture" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">--}}
{{--                                        </fieldset>--}}
{{--                                        <fieldset class="form-group">--}}
{{--                                            <label for="product_packaging_picture_content_short"><b>Product Packaging Picture:</b></label>--}}
{{--                                            <input class="form-control form-control-sm" type="file" name="product_packaging_picture_content_short" id="product_packaging_picture_content_short" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">--}}
{{--                                        </fieldset>--}}
{{--                                        <fieldset class="form-group">--}}
{{--                                            <label for="actual_product_picture_content_short"><b>Actual Product Picture:</b></label>--}}
{{--                                            <input class="form-control form-control-sm" type="file" name="actual_product_picture_content_short" id="actual_product_picture_content_short" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">--}}
{{--                                        </fieldset>--}}
{{--                                        <fieldset class="form-group">--}}
{{--                                            <input class="form-control" name="claim_content_product_cost" id="claim_content_product_cost" value="" placeholder="Enter Actual Missing Product Cost">--}}
{{--                                        </fieldset>--}}
{{--                                    </div>--}}

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
    <div class="modal fade text-left" id="ReattemptModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ReattemptModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Re Attempt</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="reattempt_request_form" method="post">
                        @csrf
                        <div class="container">
                            <div class="row">
                                <h2 class="heading">Tracking Number(s)</h2>
                            </div>

                            <input type="hidden" id="reattempt_shipment_id">
                            <div class="row old_scroll" id="reattempt_shipments">
                            </div>
                            <hr>
                            <div class="remarks" id="request_remarks">
                                <div class="row justify-content-center">
                                    <div class="col-12 d-none" id="reattempt_charges">
                                        <fieldset class="form-group">
                                            <input type="text" name="estimate_charges" id="estimated_charges_input" class="form-control decimal" placeholder="Enter Estimate Charges" data-rule-required="true" data-msg-required="Estimate Charge is required">
                                        </fieldset>  
                                    </div>
                                    <div class="col-12">
                                        <fieldset class="form-group">
                                            <textarea class="form-control" name="reattempt_remarks" id="reattempt_remarks" rows="3" placeholder="Enter Remarks Here..." data-rule-required="true" data-msg-required="Remarks is required"></textarea>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <button id="btnReattempt" type="submit" class="btn btn-primary btn-block">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
                    <form id="update_return_reason_form" class="form-horizontal mb-1 justify-content-center" novalidate="novalidate">
                        <div class="container">
                            <div class="row">
                                <h2 class="heading">Tracking Number(s)</h2>
                            </div>

                            <input type="hidden" id="return_shipment_id">
                            <div class="row old_scroll" id="return_shipments">
                            </div>
                            <hr>
                            <div class="form-group">
                                @if($return_confirm_reasons)
                                    <select id="return_reason_select" data-rule-required="true" data-msg-required="Reason is required">
                                        @foreach($return_confirm_reasons as $reason)
                                            <option value="{{$reason->id}}">{{$reason->name}}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="form-group">
                                <input type="text" id="return_reason_shipment_remarks" class="form-control" maxlength="100" placeholder="Remarks">
                            </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <button id="btnReturn" type="submit" class="btn btn-primary btn-block">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

	<style>
		.selectize-control {
			width: 300px !important;
		}
	</style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="https://kit.fontawesome.com/e7bc565afe.js" crossorigin="anonymous"></script>
	<script>
		$(document).ready(function() {

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
            $('#return_reason_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Reason'
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
                    $('#request_claims').removeClass('d-none');
                    $('#AddNewRequest').removeClass('d-none');
                }else{
                    $('#request_complaints').addClass('d-none');
                    $('#request_service').addClass('d-none');
                    $('#AddNewRequest').addClass('d-none');
                    $('#request_claims').addClass('d-none');
                }
            });
            var lost_flag = true;
            $('#case_nature_claim').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Claim Type",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            }).bind('select2:select', function () {
                var id = parseInt($(this).val());
                var value = $('#case_nature_claim').val();
                if (this.value && this.value == 23 && lost_flag === true ) {
                    $('#receiving_sheet_div').removeClass('d-none');
                    var shipment_id = $('#requested_shipment_id').val();
                    $.ajax({
                        url: '{!! route('admin.crm.request.lost.claim') !!}',
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
                // if (this.value && this.value == 21)
                // {
                //     $('#claim_shipment_damage_div').removeClass('d-none');
                // }
                // else {
                //     $('#claim_shipment_damage_div').addClass('d-none');
                // }
                //
                // if (this.value && this.value == 22)
                // {
                //     $('#claim_content_short_div').removeClass('d-none');
                // }
                // else {
                //     $('#claim_content_short_div').addClass('d-none');
                // }
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
            $('#claim_channel').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Channel",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            });
            $('#case_nature_complaints').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Complaint Type",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            });
            $('#complaint_channels').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Channel",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            });
            $('#case_nature_requests').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Request Type",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            });
            $('#request_channels').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Channel",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            });
            $('#feedback_channel').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Channel",
                allowClear:true,
                dropdownParent:$('#add_feedback_form')
            });
            $('#feedback_channel_request').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Channel",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            });
            function print(id, booking_type_id, shipment_type) {

                if(shipment_type == 1){
                    if (booking_type_id != 4 ) {
                        var url = '{!! route('cod.shipment.book.print_air_waybill') !!}';
                    }
                    else {
                        var url = '{!! route('admin.shipment.book.print_air_waybill') !!}';
                    }
                }
                else{
                    var url = '{!! route('admin.retail.accounts.retail_slip') !!}';
                }

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        'ids[]': id,
                        'admin': true,
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
                        }
                        else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                    });
            }

            var complain_route = '{{ route('admin.crm.request.details', 0) }}';
            complain_route = complain_route.slice(0, -1);

            function track(tracking_numbers) {
                $.ajax({
                    url: '{!! route('admin.tracking.track_v2') !!}',
                    method: 'POST',
                    data: {
                        'tracking_numbers': tracking_numbers,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function (data) {
                        select[0].selectize.clear();

                        $('#tracking').html('');

                        if (data.invalid !== undefined) {
                            var message = 'Invalid Tracking Number(s): ' + data.invalid.join(', ');
                            scan_sound(2);
                            toastr.error(message, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                        if (data.unauthorized !== undefined) {
                            var message = 'You are not allowed for Tracking Number(s): ' + data.unauthorized.join(', ');
                            scan_sound(2);
                            toastr.error(message, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }

                        if (data.shipments != undefined) {
                            
                            $.each(data.shipments, function (index, details) {
                                
                                var id = details.shipment_id;
                                var shipment_type = details.shipment_type;
                                var shipment = '';
                                var open_box_iocn = '';
                                var ccd_icon = '';
                                var roll_id = @json(session('role_id') == 1);
                                var department_id =  @json(session('department_id') == 6);
                                if(details.open_box){
                                    open_box_iocn = '<span><i class="fas fa-box-open"></i></span>';
                                }
                                if(details.ccd){
                                    ccd_icon = '<span><i class="fas fa-credit-card"></i> (Credit Card on Delivery-CCD)</span>';
                                }
                                shipment += '<div class="mt-4 border-primary">';
                                shipment += '<div class="d-flex flex-wrap align-items-center bg-primary">';
                                shipment += '<div class="mb-0 ml-1 mr-1 font-medium-3 white">' + details.tracking_number + '  '+ open_box_iocn +'  '+ ccd_icon +'</div>';
                                
                                shipment += '<button class="btn btn-secondary ml-auto mr-1 mr-sm-1 add_request" id=' + id + ' data-tracking=' + details.tracking_number + '>Add Request</button>';
                                @if (session('role_id') == 1 || in_array(45, session('permissions')))
                                shipment += '<button class="btn btn-secondary ml-0 mr-1 mr-sm-1 return" id=' + id + ' data-tracking=' + details.tracking_number + '>Return</button>';
                                @endif
                                @if (session('role_id') == 1 || in_array(46, session('permissions')))
                                shipment += '<button class="btn btn-secondary ml-0 mr-1 mr-sm-1 returnMarkStatus" id=' + id + ' data-tracking=' + details.tracking_number + '>Re-Attempt</button>';
                                @endif
                                @if (session('role_id') == 1 || in_array(245, session('permissions')))
                                shipment += '<button class="btn btn-secondary ml-0 mr-1 mr-sm-1 intercept" id=' + id + ' data-tracking=' + details.tracking_history[0].status_id + '>Intercept</button>';
                                @endif
                                console.log(details.dws_image);
                                if(details.dws_image != null){
                                    shipment += '<a class="btn btn-secondary d-sm-inline-block file" href="' + details.dws_image + '" target="_blank" id=' + id + '><i class="la la-lg la-image align-middle"></i> DWS File</a>';
                                }
                                if ('complain' in details) {
                                    shipment += '<a class="mr-1 d-sm-inline-block" href="' + complain_route + details.complain.id + '" target="_blank"><button class="btn btn-sm w-100 ';

                                    if (details.complain.tat >= 3) {
                                        shipment += 'white bg-red';
                                    }
                                    else {
                                        shipment += 'red bg-white';
                                    }

                                    shipment += '">' + details.complain.padded_id + ' (' + details.complain.tat + 'd)</button></a>';
                                    if(details.pod_file){
                              
                                        shipment += '<button class="d-none mr-sm-1 d-sm-inline-block btn btn-secondary print" id=' + id + ' data-booking-type-id=' + details.order_information.booking_type_id + ' shipment_type=' + shipment_type + ' >Print</button>';
                                        shipment += '<a class="btn btn-secondary d-sm-inline-block file" href="' + details.pod_file + '" target="_blank" id=' + id + '><i class="la la-lg la-image align-middle"></i> POD File</a>';

                                    }else{
                                        shipment += '<button class="d-none d-sm-inline-block btn btn-secondary print" id=' + id + ' data-booking-type-id=' + details.order_information.booking_type_id + ' shipment_type=' + shipment_type + ' >Print</button>';

                                    }
                                    if((parseInt(roll_id) == 1 && details.order_information.shipping_mode_id == 2 && details.order_information.pieces > 1)|| (parseInt(department_id) == 6 && details.order_information.shipping_mode_id == 2 && details.order_information.pieces > 1)){
                                        shipment += '<button class="btn btn-secondary d-sm-inline-block btn btn-secondary ml-1 print_pieces" id=' + id + ' data-booking-type-id=' + details.order_information.booking_type_id + ' shipment_type=' + shipment_type + ' >Print Pieces</button>';
                                    }
                                }
                                else {
                                    if(details.pod_file){

                                        shipment += '<button class="btn btn-secondary mr-sm-1 d-sm-inline-block btn btn-secondary print" id=' + id + ' data-booking-type-id=' + details.order_information.booking_type_id + ' shipment_type=' + shipment_type + ' >Print</button>';
                                        shipment += '<a class="btn btn-secondary d-sm-inline-block file" href="' + details.pod_file + '" target="_blank" id=' + id + '><i class="la la-lg la-image align-middle"></i> POD File</a>';

                                    }else{
                                        shipment += '<button class="btn btn-secondary d-sm-inline-block btn btn-secondary print" id=' + id + ' data-booking-type-id=' + details.order_information.booking_type_id + ' shipment_type=' + shipment_type + ' >Print</button>';
                                    }
                                    if((parseInt(roll_id) == 1 && details.order_information.shipping_mode_id == 2 && details.order_information.pieces > 1) || (parseInt(department_id) == 6 && details.order_information.shipping_mode_id == 2 && details.order_information.pieces > 1)){
                                        shipment += '<button class="btn btn-secondary d-sm-inline-block btn btn-secondary ml-1 print_pieces" id=' + id + ' data-booking-type-id=' + details.order_information.booking_type_id + ' shipment_type=' + shipment_type + ' >Print Pieces</button>';
                                    }
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
                                shipment += '<td>' + details.shipper.name + '</td>';
                                shipment += '<td><strong>Account No.</strong></td>';
                                shipment += '<td>' + details.shipper.account_number + '</td>';
                                shipment += '<td colspan="3"><strong>City</strong></td>';
                                shipment += '<td>' + details.shipper.city + '</td>';
                                shipment += '</tr>';

                                if(shipment_type == 1){
                                    shipment += '<tr>';
                                    shipment += '<td><strong>Phone No(s).</strong></td>';

                                    if (!details.shipper.phone_number_2) {
                                        shipment += '<td>' + details.shipper.phone_number_1 + '</td>';
                                    }
                                    else {
                                        shipment += '<td>' + details.shipper.phone_number_1 + '<br/>' + details.shipper.phone_number_2 + '</td>';
                                    }

                                    shipment += '<td><strong>Email</strong></td>';
                                    if (details.shipper.email) {
                                        shipment += '<td colspan="3">' + details.shipper.email + '</td>';
                                    }
                                    else {
                                        shipment += '<td colspan="3"></td>';
                                    }
                                    shipment += '<td><strong>Sales Person</strong></td>';

                                    if (details.shipper.sales_person != null) {
                                        shipment += '<td colspan="3">' + details.shipper.sales_person + '</td>';
                                    }
                                    else {
                                        shipment += '<td colspan="3"></td>'
                                    }

                                    shipment += '</tr>';
                                }
                                else{
                                    shipment += '<tr>';
                                    shipment += '<td><strong>Phone No(s).</strong></td>';

                                    if (!details.shipper.phone_number_2) {
                                        shipment += '<td>' + details.shipper.phone_number_1 + '</td>';
                                    }
                                    else {
                                        shipment += '<td>' + details.shipper.phone_number_1 + '<br/>' + details.shipper.phone_number_2 + '</td>';
                                    }
                                    
                                    shipment += '<td><strong> Retail User Name</strong></td>';

                                    shipment += '<td>' + details.retail_user.name + '</td>';

                                    shipment += '<td><strong> Branch</strong></td>';

                                    shipment += '<td colspan="3">' + details.retail_user.code + '</td>';


                                    shipment += '</tr>';
                                }


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
                                shipment += '<td><strong>Vendor</strong></td>';
                                if (details.pickup.vendor) {
                                    shipment += '<td>' + details.pickup.vendor + '</td>';
                                }
                                else {
                                    shipment += '<td></td>'
                                }
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
                                }
                                else {
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
                                shipment += '<tr>';
                                shipment += '<td><strong>Phone No(s).</strong></td>';

                                if (!details.consignee.phone_number_2) {
                                    shipment += '<td>' + details.consignee.phone_number_1 + '</td>';
                                }
                                else {
                                    shipment += '<td>' + details.consignee.phone_number_1 + '<br/>' + details.consignee.phone_number_2 + '</td>';
                                }

                                shipment += '<td colspan="2"></td>';
                                shipment += '</tr>';
                                shipment += '<td><strong>Email</strong></td>';
                                if (details.consignee.email) {
                                    shipment += '<td colspan="3">' + details.consignee.email + '</td>';
                                }
                                else {
                                    shipment += '<td colspan="3"></td>';
                                }
                                shipment += '</tr>';
                                shipment += '<tr>';
                                shipment += '<td><strong>Address</strong></td>';
                                shipment += '<td colspan="3">' + details.consignee.address + '</td>';
                                shipment += '</tr>';
                                shipment += '</tbody>';
                                shipment += '</table>';
                                shipment += '</div>';
                                shipment += '</div>';

                                
                                shipment += '<div class="col-12 mt-2">';
                                shipment += '<h4><u>Order Information</u></h4>';
                                shipment += '<div class="border table-responsive">';
                                shipment += '<table class="table table-sm table-borderless mb-0">';
                                shipment += '<tbody>';
                                
                                $.each(details.order_information.items, function (index, item) {
                                    shipment += '<tr>';
                                    shipment += '<td><strong>Product Type</strong></td>';
                                    shipment += '<td>' + item.product_type + '</td>';
                                    shipment += '<td><strong>Description</strong></td>';
                                    shipment += '<td>' + ((item.description) ? item.description : '-') + '</td>';
                                    shipment += '<td><strong>Quantity</strong></td>';
                                    shipment += '<td>' + item.quantity + '</td>';
                                    shipment += '<td><strong>Order ID</strong></td>';
                                    shipment += '<td>' + ((details.order_information.order_id) ? details.order_information.order_id : '-') + '</td>';
                                    shipment += '</tr>';
                                });
                                // mywork
                                var weight = '';
                                var height = '';
                                var length = '';
                                var breadth = '';
                                if(details.order_information.height != null){
                                    weight += '<td ><strong>Weight </strong><small>(Volumetric)</small></td><td>'+details.order_information.weight+'kg</td>';
                                    height += '<td ><strong>Height</strong></td>'+'<td>'+details.order_information.height+'cm</td>';
                                    length += '<td ><strong>Length</strong></td>'+'<td>'+details.order_information.length+'cm</td>';
                                    breadth += '<td><strong>Breadth</strong></td>'+'<td>'+details.order_information.breadth+'cm</td>';
                                    
                                }
                                else
                                {
                                    weight += '<td ><strong>Weight </strong><small>(Dense)</small></td>';
                                    weight +='<td>' + details.order_information.weight + ' kg</td>';
                                }
                                shipment += '<tr>';
                                shipment += weight;
                                shipment += '<td><strong>Service Type</strong></td>';
                                shipment += '<td>' + details.order_information.booking_type + '</td>';
                                shipment += '<td><strong>Collection Amount</strong></td>';
                                shipment += '<td>Rs. ' + details.order_information.amount + '</td>';
                                shipment += '<td><strong>Piece(s)</strong></td>';
                                shipment += '<td>'+ details.order_information.pieces +'</td>';
                                shipment += '</tr>';

                                shipment += '<tr>';
                                shipment +=length;
                                shipment += '<td><strong>Shipping Mode</strong></td>';
                                shipment += '<td>' + details.order_information.shipping_mode + '</td>';
                                shipment += '<td><strong>Instructions</strong></td>';

                                if (details.order_information.charges_mode_id) {
                                    shipment += '<td>' + ((details.order_information.instructions) ? details.order_information.instructions : '-') + '</td>';
                                    shipment += '<td><strong>Charges Mode</strong></td>';
                                    shipment += '<td>' + details.order_information.charges_mode + '</td>';
                                }
                                else {
                                    shipment += '<td colspan="3">' + ((details.order_information.instructions) ? details.order_information.instructions : '-') + '</td>';
                                }

                                shipment += '</tr>';
                                shipment += '<tr>';
                                shipment +=breadth;
                                shipment += '<td><strong>Business Category</strong></td>';
                                shipment += '<td>'+ details.order_information.business_category +'</td>';
                                shipment += '</tr>';
                                shipment += '<tr>';
                                shipment +=height;
                                shipment += '</tr>';
                                shipment += '</tbody>';
                                shipment += '</table>';
                                shipment += '</div>';
                                shipment += '</div>';


                                shipment += '<div class="col-12 mt-2">';
                                shipment += '<h4><u>Tracking History</u></h4>';
                                shipment += '<div class="border table-responsive">';

                                shipment += '<table class="table table-sm table-borderless datatable tracking_history">';
                                shipment += '<thead>';
                                shipment += '<tr role="row">';
                                shipment += '<th><strong>Date / Time</strong></th>';
                                shipment += '<th><strong>Status</strong></th>';
                                shipment += '<th><strong>Reason</strong></th>';
                                shipment += '<th><strong>Remarks</strong></th>';
                                shipment += '<th><strong>User</strong></th>';
                                shipment += '<th><strong>City</strong></th>';
                                shipment += '<th><strong>Received/Refused By</strong></th>';
                                shipment += '<th><strong>IP Address</strong></th>';
                                shipment += '<th><strong>Rider</strong></th>';
                                shipment += '</tr>';
                                shipment += '</thead>';
                                shipment += '<tbody>';

                                $.each(details.tracking_history, function (index, history) {
                                    shipment += '<tr>';
                                    shipment += '<td>' + history.date_time + '</td>';
                                    shipment += '<td>' + history.status + '</td>';
                                    shipment += '<td>' + ((history.status_reason) ? history.status_reason : '') + '</td>';
                                    shipment += '<td>' + history.remarks + '</td>';
                                    shipment += '<td>' + history.user + '</td>';
                                    shipment += '<td>' + history.city + '</td>';
                                    shipment += '<td>' + history.received_or_refused_by + '</td>';
                                    shipment += '<td>' + history.ip + '</td>';
                                    shipment += '<td>' + history.rider + '</td>';
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

                                    shipment += '<table class="table table-sm table-borderless datatable payment_history">';
                                    shipment += '<thead>';
                                    shipment += '<tr role="row">';
                                    shipment += '<th><strong>Date / Time</strong></th>';
                                    shipment += '<th><strong>Status</strong></th>';
                                    shipment += '<th><strong>User</strong></th>';
                                    shipment += '<th><strong>Remarks</strong></th>';
                                    shipment += '</tr>';
                                    shipment += '</thead>';
                                    shipment += '<tbody>';

                                    $.each(details.payment_history, function (index, history) {
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

                                if ('pickup_history' in details) {
                                    shipment += '<div class="col-12 mt-2">';
                                    shipment += '<h4><u>Pickup History (V2)</u></h4>';
                                    shipment += '<div class="border table-responsive">';

                                    shipment += '<table class="table table-sm table-borderless datatable pickup_history">';
                                    shipment += '<thead>';
                                    shipment += '<tr role="row">';
                                    shipment += '<th><strong>Date / Time</strong></th>';
                                    shipment += '<th><strong>Status</strong></th>';
                                    shipment += '<th><strong>Reason</strong></th>';
                                    shipment += '<th><strong>User</strong></th>';
                                    shipment += '</tr>';
                                    shipment += '</thead>';
                                    shipment += '<tbody>';

                                    $.each(details.pickup_history, function (index, history) {
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
                                if ('old_pickup_history' in details) {
                                    shipment += '<div class="col-12 mt-2">';
                                    shipment += '<h4><u>Pickup History (V1)</u></h4>';
                                    shipment += '<div class="border table-responsive">';

                                    shipment += '<table class="table table-sm table-borderless datatable pickup_history">';
                                    shipment += '<thead>';
                                    shipment += '<tr role="row">';
                                    shipment += '<th><strong>Date / Time</strong></th>';
                                    shipment += '<th><strong>Status</strong></th>';
                                    shipment += '<th><strong>User</strong></th>';
                                    shipment += '</tr>';
                                    shipment += '</thead>';
                                    shipment += '<tbody>';

                                    $.each(details.old_pickup_history, function (index, history) {
                                        shipment += '<tr>';
                                        shipment += '<td>' + history.date_time + '</td>';
                                        shipment += '<td>' + history.status + '</td>';
                                        shipment += '<td>' + history.user + '</td>';
                                        shipment += '</tr>';
                                    });

                                    shipment += '</tbody>';
                                    shipment += '</table>';

                                    shipment += '</div>';
                                    shipment += '</div>';
                                }
                                if ('handover_history' in details) {
                                    shipment += '<div class="col-12 mt-2">';
                                    shipment += '<h4><u>Handover Shipment History</u></h4>';
                                    shipment += '<div class="border table-responsive">';

                                    shipment += '<table class="table table-sm table-borderless datatable pickup_history">';
                                    shipment += '<thead>';
                                    shipment += '<tr role="row">';
                                    shipment += '<th><strong>Handover Id</strong></th>';
                                    shipment += '<th><strong>Status</strong></th>';
                                    shipment += '<th><strong>Date / Time</strong></th>';
                                    shipment += '</tr>';
                                    shipment += '</thead>';
                                    shipment += '<tbody>';

                                    $.each(details.handover_history, function (index, history) {
                                        shipment += '<tr>';
                                        shipment += '<td>' + history.handover_id + '</td>';
                                        shipment += '<td>' + history.status + '</td>';
                                        shipment += '<td>' + history.created_at + '</td>';
                                        shipment += '</tr>';
                                    });

                                    shipment += '</tbody>';
                                    shipment += '</table>';

                                    shipment += '</div>';
                                    shipment += '</div>';
                                }

                                if ('amount_history' in details) {
                                    shipment += '<div class="col-12 mt-2">';
                                    shipment += '<h4><u>Amount History</u></h4>';
                                    shipment += '<div class="border table-responsive">';

                                    shipment += '<table class="table table-sm table-borderless datatable amount_history">';
                                    shipment += '<thead>';
                                    shipment += '<tr role="row">';
                                    shipment += '<th><strong>Date / Time</strong></th>';
                                    shipment += '<th><strong>Old Amount</strong></th>';
                                    shipment += '<th><strong>New Amount</strong></th>';
                                    shipment += '<th><strong>Remarks</strong></th>';
                                    shipment += '<th><strong>User</strong></th>';
                                    shipment += '</tr>';
                                    shipment += '</thead>';
                                    shipment += '<tbody>';

                                    $.each(details.amount_history, function (index, history) {
                                        shipment += '<tr>';
                                        shipment += '<td>' + history.date_time + '</td>';
                                        shipment += '<td>' + history.old_amount + '</td>';
                                        shipment += '<td>' + history.new_amount + '</td>';
                                        shipment += '<td>' + history.remarks + '</td>';
                                        shipment += '<td>' + history.user + '</td>';
                                        shipment += '</tr>';
                                    });

                                    shipment += '</tbody>';
                                    shipment += '</table>';

                                    shipment += '</div>';
                                    shipment += '</div>';
                                }

                                if ('weight_history' in details) {
                                    shipment += '<div class="col-12 mt-2">';
                                    shipment += '<h4><u>Weight History</u></h4>';
                                    shipment += '<div class="border table-responsive">';

                                    shipment += '<table class="table table-sm table-borderless datatable weight_history">';
                                    shipment += '<thead>';
                                    shipment += '<tr role="row">';
                                    shipment += '<th><strong>Date / Time</strong></th>';
                                    shipment += '<th><strong>Old Weight</strong></th>';
                                    shipment += '<th><strong>New Weight</strong></th>';
                                    shipment += '<th><strong>User</strong></th>';
                                    shipment += '</tr>';
                                    shipment += '</thead>';
                                    shipment += '<tbody>';

                                    $.each(details.weight_history, function (index, history) {
                                        shipment += '<tr>';
                                        shipment += '<td>' + history.date_time + '</td>';
                                        shipment += '<td>' + history.old_weight + '</td>';
                                        shipment += '<td>' + history.new_weight + '</td>';
                                        shipment += '<td>' + history.user + '</td>';
                                        shipment += '</tr>';
                                    });

                                    shipment += '</tbody>';
                                    shipment += '</table>';

                                    shipment += '</div>';
                                    shipment += '</div>';
                                }

                                if ('crm_requests' in details) {
                                    shipment += '<div class="col-12 mt-2">';
                                    shipment += '<h4><u>CRM History</u></h4>';
                                    shipment += '<div class="border table-responsive">';

                                    shipment += '<table class="table table-sm table-borderless datatable crm_requests_history">';
                                    shipment += '<thead>';
                                    shipment += '<tr role="row">';
                                    shipment += '<th><strong>S.No</strong></th>';
                                    shipment += '<th><strong>Status</strong></th>';
                                    shipment += '<th><strong>Created At</strong></th>';
                                    shipment += '<th><strong>Created By</strong></th>';
                                    shipment += '</tr>';
                                    shipment += '</thead>';
                                    shipment += '<tbody>';
                                    $count = 0;
                                    $.each(details.crm_requests, function (index, crm_request) {
                                        $count = $count + 1;
                                        shipment += '<tr>';
                                        shipment += '<td>' + $count + '</td>';
                                        if (crm_request.status_id === 1 || crm_request.status_id === 5) {
                                            shipment += '<td>' + crm_request.status + '(<a class="btn btn-sm btn-outline-info align-middle" href="' + complain_route + crm_request.id + '" target="_blank">(' + crm_request.id + ')</a>)</td>';
                                        }
                                        else {
                                            shipment += '<td>' + crm_request.status + '</td>';
                                        }
                                        shipment += '<td>' + crm_request.created_at + '</td>';
                                        shipment += '<td>' + crm_request.created_by + '</td>';
                                        shipment += '</tr>';
                                    });

                                    shipment += '</tbody>';
                                    shipment += '</table>';

                                    shipment += '</div>';
                                    shipment += '</div>';
                                }

                                if ('open_box_journey' in details) {
                                    shipment += '<div class="col-12 mt-2">';
                                    shipment += '<h4><u>Open Box History</u></h4>';
                                    shipment += '<div class="border table-responsive">';

                                    shipment += '<table class="table table-sm table-borderless datatable open_box_history">';
                                    shipment += '<thead>';
                                    shipment += '<tr role="row">';
                                    shipment += '<th><strong>Date / Time</strong></th>';
                                    shipment += '<th><strong>Status</strong></th>';
                                    shipment += '<th><strong>Created By</strong></th>';
                                    shipment += '</tr>';
                                    shipment += '</thead>';
                                    shipment += '<tbody>';

                                    $.each(details.open_box_journey, function (index, history) {
                                        shipment += '<tr>';
                                        shipment += '<td>' + history.date_time + '</td>';
                                        shipment += '<td>' + history.status + '</td>';
                                        shipment += '<td>' + history.created_by + '</td>';
                                        shipment += '</tr>';
                                    });

                                    shipment += '</tbody>';
                                    shipment += '</table>';

                                    shipment += '</div>';
                                    shipment += '</div>';
                                }

                                if ('quick_receiving_shipments_journeys' in details) {
                                    shipment += '<div class="col-12 mt-2">';
                                    shipment += '<h4><u>Quick Receiving Shipment History</u></h4>';
                                    shipment += '<div class="border table-responsive">';

                                    shipment += '<table class="table table-sm table-borderless datatable pickup_history">';
                                    shipment += '<thead>';
                                    shipment += '<tr role="row">';
                                    shipment += '<th><strong>Date / Time</strong></th>';
                                    shipment += '<th><strong>Delivery Note ID</strong></th>';
                                    shipment += '<th><strong>Received By</strong></th>';
                                    shipment += '</tr>';
                                    shipment += '</thead>';
                                    shipment += '<tbody>';

                                    $.each(details.quick_receiving_shipments_journeys, function (index, history) {
                                        shipment += '<tr>';
                                        shipment += '<td>' + history.created_at + '</td>';
                                        shipment += '<td>' + history.delivery_note_id + '</td>';
                                        shipment += '<td>' + history.received_by + '</td>';
                                        shipment += '</tr>';
                                    });

                                    shipment += '</tbody>';
                                    shipment += '</table>';

                                    shipment += '</div>';
                                    shipment += '</div>';
                                }
                                if ('outstanding_history' in details) {
                                    shipment += '<div class="col-12 mt-2">';
                                    shipment += '<h4><u>Resolved Outstanding Shipment History</u></h4>';
                                    shipment += '<div class="border table-responsive">';

                                    shipment += '<table class="table table-sm table-borderless datatable resolved_outstanding_shipment">';
                                    shipment += '<thead>';
                                    shipment += '<tr role="row">';
                                    shipment += '<th><strong>Date / Time</strong></th>';
                                    shipment += '<th><strong>Resolved By</strong></th>';
                                    shipment += '</tr>';
                                    shipment += '</thead>';
                                    shipment += '<tbody>';

                                    $.each(details.outstanding_history, function (index, history) {
                                        shipment += '<tr>';
                                        shipment += '<td>' + history.date_time + '</td>';
                                        shipment += '<td>' + history.resolved_by + '</td>';
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
                            });
                            scan_sound(1);

                            $('#tracking table.datatable.tracking_history').DataTable({
                                dom: 't',
                                paging: false,
                                order: [[0, 'desc']],
                                columns: [
                                    {name: 'date_time', class: 'align-middle date_time'},
                                    {name: 'status', class: 'align-middle status'},
                                    {name: 'reason', class: 'align-middle reason'},
                                    {name: 'remarks', class: 'align-middle remarks'},
                                    {name: 'user', class: 'align-middle user'},
                                    {name: 'city', class: 'align-middle city'},
                                    {name: 'received_or_refused_by', class: 'align-middle received_or_refused_by'},
                                    {name: 'ip', class: 'align-middle ip'},
                                    {name: 'rider', class: 'align-middle rider'}
                                ]
                            });

                            $('#tracking table.datatable.payment_history').DataTable({
                                dom: 't',
                                paging: false,
                                order: [[0, 'desc']],
                                columns: [
                                    {name: 'date_time', class: 'align-middle date_time'},
                                    {name: 'status', class: 'align-middle status'},
                                    {name: 'user', class: 'align-middle user'},
                                    {name: 'remarks', class: 'align-middle remarks'}
                                ]
                            });
                        }
                    });
            }

			@if (app('request')->has('tracking_number'))
            track({{ app('request')->input('tracking_number') }});
					@endif

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

            $('#track_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parents('form'));
                },
                submitHandler: function (form) {
                    track($(form).find('.tracking_numbers').val());

                    return false;
                }
            });

            $('#tracking').on('click', '.print', function () {
                id = $(this).attr('id');

                shipment_type = $(this).attr('shipment_type');

                booking_type_id = $(this).attr('data-booking-type-id');

                print(id, booking_type_id,shipment_type);
            });

            $('#tracking').on('click', '.print_pieces', function () {
                id = $(this).attr('id');
                if(id){
                    pieces_print(id);
                }
            });
            function pieces_print(id) {

                $.ajax({
                    url: '{!! route('admin.tracking.pieces_print') !!}',
                    method: 'POST',
                    data: {
                        'shipment_id': id,
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
                        }
                        else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                    });
            }


            $('#tracking').on('click', '.add_request', function () {
                id = $(this).attr('id');
                var tracking = $(this).attr('data-tracking');
                var tracking_rows = '<div class="col-4"><span class="mr-1"><i class="la la-angle-right align-bottom"></i><b> '+ tracking +'</b></span></div>';
                $('#requested_shipment_id').val(id);
                $('#requested_shipments').html(tracking_rows);
                $('#AddRequestModal').modal('show');

            });
            $('#tracking').on('click','.returnMarkStatus', function () {
                id = $(this).attr('id');
                var tracking = $(this).attr('data-tracking');
                var tracking_rows = '<div class="col-4"><span class="mr-1"><i class="la"></i><b> '+ tracking +'</b></span></div>';
                $('#reattempt_shipment_id').val(id);
                $('#reattempt_shipments').html(tracking_rows);
                $('#reattempt_remarks').val('');
                $('#estimated_charges_input').val('');
                $('#reattempt_charges').addClass('d-none');
                $.ajax({
                    url: '{!! route('admin.tracking.estimation_check') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'shipment_id': id
                    }
                })
                    .done(function (data) {
                        if(data.contains){
                        $('#reattempt_charges').removeClass('d-none');
                        }
                    });
                $('#ReattemptModal').modal('show');

            });
            $('#tracking').on('click','.intercept', function () {
                id = $(this).attr('id');
                status_id = $(this).attr('data-tracking');
                debugger;
                if(id != ''){
                    var redirect = '{!! route('admin.intercept.index', ':id') !!}';
                    if(status_id==12 ||status_id==52)
                    {
                        var url = redirect.replace(':id', id);
                        window.open(url);
                    }
                    else
                    {
                        toastr.error('Shipment is already updated with Status, Cannot mark it as Intercept! ', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                }

            });
            $('#tracking').on('click','.return', function () {
                id = $(this).attr('id');
                var tracking = $(this).attr('data-tracking');
                var tracking_rows = '<div class="col-4"><span class="mr-1"><i class="la"></i><b> '+ tracking +'</b></span></div>';
                $('#return_shipment_id').val(id);
                $('#return_shipments').html(tracking_rows);
                //$('#return_reason_select').val(0);
                //$("#return_reason_select").empty();

                $('#ReturnConfirmReasonModal').modal('show');

            });
            $('#tracking').on('click', '.rider_information', function () {
                id = $(this).attr('data-id');

                $.ajax({
                    url: '{!! route('admin.tracking.rider_information') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': id
                    }
                })
                    .done(function (data) {
                        var details = '<table class="table table-sm table-bordered"><tbody>';

                        details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Name</strong></td><td class="align-middle text-center">' + data.name + '</td></tr>';
                        details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Phone Number</strong></td><td class="align-middle text-center">' + data.phone_number + '</td></tr>';
                        details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>City</strong></td><td class="align-middle text-center">' + data.city + '</td></tr>';
                        details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Category</strong></td><td class="align-middle text-center">' + data.category + '</td></tr>';
                        details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Route</strong></td><td class="align-middle text-center">' + data.route + '</td></tr>';

                        details += '</tbody></table>';

                        $('#rider_information .modal-body').html(details);

                        $('#rider_information').modal('show');
                    });
            });

            $('#tracking').on('click', '.cargo_note_print', function () {
                id = $(this).attr('data-id');
                $.ajax({
                    url: '{!! route('admin.cargo.in_transit.print') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
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
                        }
                        else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                    });
            });
            $('#tracking').on('click', '.return_note_print', function () {
                id = $(this).attr('data-id');
                $.ajax({
                    url: '{!! route('admin.return.receive.rn.print') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function(data) {
                        var tab = window.open('', '_blank');

                        if(!tab) {
                            swal({
                                title: 'Popup Blocker Enabled!',
                                text: 'Please add this site to your exception list.',
                                icon: 'error',
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                        }
                        else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                    });
            });
            $('#tracking').on('click', '.delivery_note_print', function () {
                id = $(this).attr('data-id');
                $.ajax({
                    url: '{!! route('admin.delivery.receive.print') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
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
                        }
                        else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                    });
            });
            $('#tracking').on('click', '.payment_print', function () {
                id = $(this).attr('data-id');
                shipment_type = $(this).attr('data-shipment_type');
                
                if(shipment_type == 1){
                    var url = '{!! route('admin.finance.done_payments.details_print') !!}';
                }
                else{
                    var url = '{!! route('admin.finance.retail.done_payments.details_print') !!}';
                }

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': id
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
                        }
                        else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                    });
            });
        });



        var max_char = 245;
        $('#feedback_description').on('keypress copy paste',function (e) {
            if ($(this).val().length == max_char) {
                e.preventDefault();
            } else if ($(this).val().length > max_char) {
                // Maximum exceeded
                this.value = this.value.substring(0, max_char);
            }
        });
        $('#service_description').on('keypress copy paste',function (e) {
            if ($(this).val().length == max_char) {
                e.preventDefault();
            } else if ($(this).val().length > max_char) {
                // Maximum exceeded
                this.value = this.value.substring(0, max_char);
            }
        });
        $('#complaint_description').on('keypress copy paste',function (e) {
            if ($(this).val().length == max_char) {
                e.preventDefault();
            } else if ($(this).val().length > max_char) {
                // Maximum exceeded
                this.value = this.value.substring(0, max_char);
            }
        });
        $('body').on('change','#add_request_form textarea, #add_feedback_form textarea',function() {
            $(this).val($(this).val().trim());
        });
        $( "#add_request_form" ).validate({
            errorClass:"danger",
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
            submitHandler: function(form) {
                var case_nature_id = parseInt($('#case_nature_select').val());
                if (case_nature_id === 1) {
                    var nature_flag = true;
                    var case_nature_complaint_id = $('#case_nature_complaints').val();
                    var case_nature_channel_id = $('#complaint_channels').val();
                    var complaint_description = $('#complaint_description').val();
                    if (!case_nature_complaint_id) {
                        nature_flag = false;
                        var error = "Please select Complaint type!";
                        toastr.error(error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                    if (!case_nature_channel_id) {
                        nature_flag = false;
                        var error = "Please select Channel!";
                        toastr.error(error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                    if (!complaint_description) {
                        nature_flag = false;
                        var error = "Please select Description!";
                        toastr.error(error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
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
                            url: '{!! route('admin.crm.request.add') !!}',
                            method: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'shipment_id': $('#requested_shipment_id').val(),
                                'case_nature_id': case_nature_id,
                                'complaint_id': case_nature_complaint_id,
                                'channel_id': case_nature_channel_id,
                                'description': complaint_description
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
                                    // toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
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

                } else if (case_nature_id == 2) {
                    var nature_flag = true;
                    var case_nature_complaint_id = $('#case_nature_requests').val();
                    var case_nature_channel_id = $('#request_channels').val();
                    var service_description = $('#service_description').val();
                    if (!case_nature_complaint_id) {
                        nature_flag = false;
                        var error = "Please select Complaint type!";
                        toastr.error(error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                    if (!case_nature_channel_id) {
                        nature_flag = false;
                        var error = "Please select Channel!";
                        toastr.error(error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                    if (!service_description) {
                        nature_flag = false;
                        var error = "Please select Description!";
                        toastr.error(error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                    if (nature_flag) {
                        $('#AddNewRequest').attr('disabled', true);
                        $.ajax({
                            url: '{!! route('admin.crm.request.add') !!}',
                            method: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'shipment_id': $('#requested_shipment_id').val(),
                                'case_nature_id': case_nature_id,
                                'complaint_id': case_nature_complaint_id,
                                'channel_id': case_nature_channel_id,
                                'description': service_description
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
                                    // toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
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
                } else if (case_nature_id == 3) {
                    var feedback_flag = true;
                    var feedback_channel = $('#feedback_channel_request').val();
                    var feedback_description = $('#feedback_description_request').val();
                    if (!feedback_description) {
                        feedback_flag = false;
                        var error = "Please select Description!";
                        toastr.error(error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                    if (!feedback_channel) {
                        feedback_flag = false;
                        var error = "Please select Channel!";
                        toastr.error(error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                    if (feedback_flag) {
                        $('#AddNewRequest').attr('disabled', true);
                        $.ajax({
                            url: '{!! route('admin.crm.feedback.add') !!}',
                            method: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'channel_id': $('#feedback_channel_request').val(),
                                'shipment_id': $('#requested_shipment_id').val(),
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

                                $('#AddRequestModal').modal('hide');
                                $('#AddNewRequest').attr('disabled', false);
                            });
                    }
                } else if (case_nature_id === 4) {
                    var nature_flag = true;
                    var case_nature_claim_id = $('#case_nature_claim').val();
                    var case_nature_channel_id = $('#claim_channel').val();
                    var product_cost = $('#claim_product_cost').val();
                    var check_product_picture = $('#product_picture').val();
                    var check_invoice_picture = $('#invoice_picture').val();
                    $('#shipment_ids').val($('#requested_shipment_id').val());
                    $('#case_nature_id').val(case_nature_id);
                    $('#channel_id').val(case_nature_channel_id);
                    $('#complaint_id').val(case_nature_claim_id);
                    var formData = new FormData($('#add_request_form')[0]);
                    if (case_nature_claim_id === 23) {
                        if ($('#request_id').val() == "" || $('#request_id').val() == null) {
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
                    if (!case_nature_channel_id) {
                        nature_flag = false;
                        var error = "Please select Channel!";
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
                        $.ajax({
                            url: '{!! route('admin.crm.request.add') !!}',
                            method: 'POST',
                            enctype: 'multipart/form-data',
                            data: formData,
                            dataType: 'json',
                            processData: false,
                            contentType: false,
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
                                    // toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
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
            }
        });
        $( "#update_return_reason_form" ).validate({
            errorClass:"danger",
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
            submitHandler: function(form) {
                    var return_reason_select = $('#return_reason_select').val();
                    var remarks = $('#return_reason_shipment_remarks').val();
                    swal({
                            title: 'Please Wait!',
                            text: ' ',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });
                    $.ajax({
                        url: '{!! route('admin.return.marked.status.single') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'shipment_id': $('#return_shipment_id').val(),
                            'single_return_reason_select': return_reason_select,
                            'remark': remarks,
                            'action': 'confirm'
                        }
                    })
                    .done(function (data) {
                            swal.close();
                        if(data.status == 1){
                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        }
                        else{
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }

                        $('#ReturnConfirmReasonModal').modal('hide');
                    });
                    
            }
        });
        $('#ReturnConfirmReasonModal').on('hide.bs.modal', function (e) {
                $('#return_reason_select').val('').trigger('change');
                $('#return_reason_shipment_remarks').val('');
            });
        $( "#reattempt_request_form" ).validate({
            errorClass:"danger",
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
            submitHandler: function(form) {
                    var reattempt_remarks = $('#reattempt_remarks').val();
                    var charges = $('#estimated_charges_input').val();
                    swal({
                            title: 'Please Wait!',
                            text: ' ',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });
                    $.ajax({
                        url: '{!! route('admin.return.marked.status.single') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'shipment_id': $('#reattempt_shipment_id').val(),
                            'remark': reattempt_remarks,
                            'action': 'reattempt',
                            'charges': charges
                        }
                    })
                    .done(function (data) {
                            swal.close();
                        if(data.status == 1){
                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        }
                        else{
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }

                        $('#ReattemptModal').modal('hide');
                    });
                    
            }
        });
        $('#AddRequestModal').on('hide.bs.modal', function (e) {
            $('#add_request_form')[0].reset();
            $('#case_nature_complaints').val('').trigger('change');
            $('#case_nature_select').val('').trigger('change');
            $('#case_nature_requests').val('').trigger('change');
            $('#complaint_channels').val('').trigger('change');
            $('#request_channels').val('').trigger('change');
            $('#feedback_channel_request').val('').trigger('change');
            $('#complaint_description').val('');
            $('#service_description').val('');
            $('#feedback_description_request').val('');
            $('#request_claims').addClass('d-none');
            $('#case_nature_claim').val('').trigger('change');
            $('#claim_channel').val('').trigger('change');
            $('#claim_product_cost').val('');
            $('#request_id').val('').trigger('change');
            $('#receiving_sheet_div').addClass('d-none');

        });

				{{--$.ajax({--}}
				{{--url: '{!! route('admin.tracking.cargo_consignment_details') !!}',--}}
				{{--method: 'POST',--}}
				{{--data: {--}}
					{{--'_token': '{{ csrf_token() }}',--}}
					{{--'id': id--}}
				{{--}--}}
				{{--})--}}
				{{--.done(function(data) {--}}
					{{--var details = '<table class="table table-sm table-bordered"><tbody>';--}}

					{{--details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Junction 1</strong></td><td class="align-middle text-center">' + data.junction_hub_1 + '</td></tr>';--}}
					{{--details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Junction 2</strong></td><td class="align-middle text-center">' + ((data.junction_hub_2) ? data.junction_hub_2 : '') + '</td></tr>';--}}
					{{--details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Expected Arrival Date</strong></td><td class="align-middle text-center">' + data.expected_arrival_date + '</td></tr>';--}}
					{{--details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Shipping Mode</strong></td><td class="align-middle text-center">' + data.shipping_mode + '</td></tr>';--}}
					{{--details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Transport Mode</strong></td><td class="align-middle text-center">' + data.transport_mode + '</td></tr>';--}}
					{{--details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Transport Mode Vendor</strong></td><td class="align-middle text-center">' + data.transport_mode_vendor + '</td></tr>';--}}
					{{--details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Seal Number</strong></td><td class="align-middle text-center">' + data.seal_number + '</td></tr>';--}}
					{{--details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Builty Number</strong></td><td class="align-middle text-center">' + ((data.builty_number) ? data.builty_number : '') + '</td></tr>';--}}
					{{--details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Shipments Weight</strong></td><td class="align-middle text-center">' + data.shipments_weight + '</td></tr>';--}}
					{{--details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Actual Weight</strong></td><td class="align-middle text-center">' + data.actual_weight + '</td></tr>';--}}
					{{--details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Vendor Weight</strong></td><td class="align-middle text-center">' + ((data.vendor_weight) ? data.vendor_weight : '') + '</td></tr>';--}}
					{{--details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Weight Charges / kg</strong></td><td class="align-middle text-center">' + ((data.weight_charges_per_kg) ? data.weight_charges_per_kg : '') + '</td></tr>';--}}
					{{--details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Extra Charges</strong></td><td class="align-middle text-center">' + ((data.extra_charges) ? data.extra_charges : '') + '</td></tr>';--}}
					{{--details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Total Weight Charges</strong></td><td class="align-middle text-center">' + ((data.total_weight_charges) ? data.total_weight_charges : '') + '</td></tr>';--}}
					{{--details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Sender Name</strong></td><td class="align-middle text-center">' + data.sender_name + '</td></tr>';--}}
					{{--details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Receiver Name</strong></td><td class="align-middle text-center">' + data.receiver_name + '</td></tr>';--}}

					{{--details += '</tbody></table>';--}}

					{{--$('#cargo_consignment_details .modal-body').html(details);--}}

					{{--$('#cargo_consignment_details').modal('show');--}}
				{{--});--}}
	</script>
@endsection