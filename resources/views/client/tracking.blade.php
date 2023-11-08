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
                                        <input type="text" name="tracking_numbers" class="tracking_numbers" placeholder="Tracking Number(s)*" data-tags-input-name="tracking_number" data-rule-required="true" data-msg-required="Tracking Number is required">
                                    </div>
                                </div>


								<div class="form-group ml-1">
									<button type="submit" name="track" class="btn btn-primary" value="Track">Track</button>
								</div>
							</form>

							<div class="tracking" id="tracking">
							</div>
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
                            <div class="row justify-content-center">
                                <div class="col-10">
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
                                    <div class="col-10">
                                        <fieldset class="form-group">
                                            <select name="case_nature_complaint" id="case_nature_complaints" class="form-control select2" data-rule-required="true" data-msg-required="Complaint Type is required">
                                                @foreach($case_nature_complaints as $complaints)
                                                    <option value="{{$complaints->id}}">{{$complaints->type}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-10">
                                        <fieldset class="form-group">
                                            <textarea class="form-control" name="complaint_description" id="complaint_description" rows="5" placeholder="Enter Description Here..." data-rule-required="true" data-msg-required="Description is required"></textarea>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <div class="service d-none" id="request_service">
                                <div class="row justify-content-center">
                                    <div class="col-10">
                                        <fieldset class="form-group">
                                            <select name="case_nature_request" id="case_nature_requests" class="form-control select2" data-rule-required="true" data-msg-required="Complaint Type is required">
                                                @foreach($case_nature_service_requests as $service)
                                                    <option value="{{$service->id}}">{{$service->type}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-10 d-none" id="alternate_phone_input">
                                        <fieldset class="form-group">
                                            <input type="text" name="alternate_phone" class="form-control" id="alternate_phone" placeholder="Enter Alternate Number" data-rule-required="true" data-msg-required="Alternate Number is required">
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

                                    <div class="col-10">
                                        <fieldset class="form-group">
                                            <textarea class="form-control" name="service_description" id="service_description" rows="5" placeholder="Enter Description*" data-rule-required="true" data-msg-required="Description is required"></textarea>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <div class="feedback d-none" id="request_feedback">
                                <div class="row justify-content-center">
                                    <div class="col-10">
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
                                    <div class="col-10">
                                        <fieldset class="form-group">
                                            <select name="case_nature_tclaim" id="case_nature_claim" class="form-control select2">
                                                @foreach($case_nature_type_claims as $claim)
                                                    <option value="{{$claim->id}}">{{$claim->type}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-10" id="claim_product_cost_div">
                                        <fieldset class="form-group">
                                            <input class="form-control" name="claim_product_cost" id="claim_product_cost" value="" placeholder="Enter Product Cost">
                                        </fieldset>
                                    </div>
                                    <div class="col-10 d-none" id="receiving_sheet_div">
                                        <fieldset class="form-group">
                                            <select name="receiving_sheet_id"  id="request_id" class="form-control select2" data-rule-required="true" data-msg-required="Please Select Receiving Sheet" >

                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-10 text-left" id="claim_product_picture_div">
                                        <fieldset class="form-group">
                                            <label for="product_picture"><b>Product Picture:</b></label>
                                            <input class="form-control form-control-sm" type="file" name="product_picture" id="product_picture" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">
                                        </fieldset>
                                    </div>

                                    <div class="col-10 text-left" id="claim_invoice_picture_div">
                                        <fieldset class="form-group">
                                            <label for="invoice_picture"><b>Invoice Picture:</b></label>
                                            <input class="form-control form-control-sm" type="file" name="invoice_picture" id="invoice_picture" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">
                                        </fieldset>
                                    </div>

                                    <div class="col-10 text-left d-none" id="claim_shipment_damage_div">
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
                                        <fieldset class="form-group">
                                            <input class="form-control damage_claim_product_cost" name="damage_claim_product_cost" id="damage_claim_product_cost" value="" placeholder="Enter Actual Damaged Product Cost">
                                        </fieldset>

                                    </div>

                                    <div class="col-10 text-left d-none" id="claim_content_short_div">
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
                                            <input class="form-control" name="claim_content_product_cost" id="claim_content_product_cost" value="" placeholder="Enter Actual Missing Product Cost">
                                        </fieldset>
                                    </div>

                                    <div class="col-10">
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

    {{-- Call History Modal --}}
    <div class="modal fade" id="update_call_status_modal" data-backdrop="static" role="dialog" aria-labelledby="update_call_status_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Call History</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div class="modal-header text-center">
                        <h4 class="modal-title font-weight-bold" id="shipments_title">Remarks Log</h4>
                    </div>
                    <div class="modal-body text-center" id="remarks_log_modal_body">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

    <style>
        .selectize-control {
            width: 305px !important;
        }

        .tracking_numbers{
            width: 100% !important;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
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
					}
					else {
						return false;
					}
				}
			});

			@if (app('request')->has('tracking_number'))
            track({{ app('request')->input('tracking_number') }});
			@endif

			function track(tracking_numbers){
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

                            toastr.error(message, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }

                        if (data.disallowed !== undefined) {
                            var message = 'Following Tracking Number(s) don\'t belong to you: ' + data.disallowed.join(', ');

                            toastr.error(message, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }

                        if (data.shipments != undefined) {
                            $.each(data.shipments, function(id, details) {
                                var $international_tracking_number = '';
                                var shipment = '';

                                if(details.international_shipment){
                                    $international_tracking_number = ' <span>(' + details.international_tracking_number + ')</span> ';
                                }
                                shipment += '<div class="mt-4 border-primary">';
                                shipment += '<div class="d-flex align-items-center bg-primary">';
                                shipment += '<div class="mb-0 ml-1 font-medium-3 white">' + details.tracking_number + $international_tracking_number + '</div>';
                                shipment += '<div class="mb-0 ml-1">  '+ details.received_img + '  </div>';
                                shipment += '<button class="btn btn-secondary ml-auto mr-0 mr-sm-1  add_request" id=' + id + ' data-tracking=' + details.tracking_number + '>Add Request</button>';
                                shipment += '<button class="btn btn-secondary ml-0 mr-1 mr-sm-1 call_status" id=' + id + ' data-tracking=' + details.tracking_number + '>Call History</button>';
                                if(details.pod_file){
                              
                                    shipment += '<button class="btn btn-secondary mr-sm-1 d-sm-inline-block print" id=' + id + '>Print</button>';
                                    shipment += '<a class="btn btn-secondary d-sm-inline-block file" href="' + details.pod_file + '" target="_blank" id=' + id + '><i class="la la-lg la-image align-middle"></i> POD File</a>';

                                }else{
                                    shipment += '<button class="btn btn-secondary d-sm-inline-block print" id=' + id + '>Print</button>';

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
                                shipment += '<td><strong>City</strong></td>';
                                shipment += '<td>' + details.shipper.city + '</td>';
                                shipment += '</tr>';

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
                                else{
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
                                shipment += '<td><strong>Vendor</strong></td>';
                                if (details.pickup.vendor) {
                                    shipment += '<td>' + details.pickup.vendor + '</td>';
                                }
                                else{
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
                                else{
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
                                else{
                                    shipment += '<td colspan="3"></td>'
                                }
                                shipment += '</tr>';
                                shipment += '<tr>';
                                shipment += '<td><strong>Address</strong></td>';
                                shipment += '<td colspan="3">' + details.consignee.address + '</td>';
                                shipment += '</tr>';
                                if(details.consignee.delivery_area){

                                    shipment += '<tr>';
                                    shipment += '<td><strong>Delivery Area</strong></td>';
                                    shipment += '<td colspan="3">' + details.consignee.delivery_area + '</td>';
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
                                    shipment += '<td>' + ((item.description) ? item.description : '-') + '</td>';
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
                                shipment += '<td>' + ((details.order_information.order_id) ? details.order_information.order_id : '-') + '</td>';
                                shipment += '<td><strong>Order Date</strong></td>';
                                shipment += '<td>' + ((details.order_information.order_date) ? details.order_information.order_date : '-') + '</td>';
                                shipment += '<td><strong>Instructions</strong></td>';

                                if (details.order_information.charges_mode_id) {
                                    shipment += '<td>' + ((details.order_information.instructions) ? details.order_information.instructions : '-') + '</td>';
                                }
                                else {
                                    shipment += '<td colspan="3">' + ((details.order_information.instructions) ? details.order_information.instructions : '-') + '</td>';
                                }

                                shipment += '</tr>';
                                shipment += '<tr>';

                                if (details.order_information.charges_mode_id) {
                                    shipment += '<td><strong>Charges Mode</strong></td>';
                                    shipment += '<td>' + details.order_information.charges_mode + '</td>';
                                }
                                shipment += '<td><strong>Piece(s)</strong></td>';
                                shipment += '<td>'+ details.order_information.pieces +'</td>';
                                shipment += '<td><strong>Business Category</strong></td>';
                                shipment += '<td>'+ details.order_information.business_category +'</td>';
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
                                shipment += '<th><strong>Received/Refused By</strong></th>';
                                shipment += '</tr>';
                                shipment += '</thead>';
                                shipment += '<tbody>';

                                $.each(details.tracking_history, function(index, history) {
                                    shipment += '<tr>';
                                    shipment += '<td>' + history.date_time + '</td>';
                                    shipment += '<td>' + history.status + '</td>';
                                    shipment += '<td>' + ((history.status_reason) ? history.status_reason : '') + '</td>';
                                    shipment += '<td>' + history.status_remarks + '</td>';
                                    shipment += '<td>' + history.received_or_refused_by + '</td>';
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

                                if ('pickup_history' in details) {
                                    shipment += '<div class="col-12 mt-2">';
                                    shipment += '<h4><u>Pickup History</u></h4>';
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

                                shipment += '</div>';
                                shipment += '</div>';


                                shipment += '</div>';

                                $('#tracking').append(shipment);
                                $('#old_amount').val(details.order_information.amount);
                            });

                            $('#tracking table.datatable.tracking_history').DataTable({
                                dom: 't',
                                paging: false,
                                order: [[0, 'desc']],
                                columns: [
                                    {name: 'date_time', class: 'align-middle date_time'},
                                    {name: 'status', class: 'align-middle status'},
                                    {name: 'reason', class: 'align-middle reason'},
                                    {name: 'status_remarks', class: 'align-middle status_remarks'},
                                    {name: 'received_or_refused_by', class: 'align-middle received_or_refused_by'}
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
                                    {name: 'payable_remarks', class: 'align-middle payable_remarks'}
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
			$('#tracking').on('click', '.add_request', function() {
				id = $(this).attr('id');
                var tracking = $(this).attr('data-tracking');
                var tracking_rows = '<div class="col-4"><span class="mr-1"><i class="la la-angle-right align-bottom"></i><b> '+ tracking +'</b></span></div>';
                $('#requested_shipment_id').val(id);
                $('#requested_shipments').html(tracking_rows);
                $('#AddRequestModal').modal('show');
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
                            $('#AddNewRequest').attr('disabled',true);
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

            $('#case_nature_requests').on('change',function (e) {
            
                if($(this).val() == 13){
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
            $( "#add_request_form" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    var case_nature_id = parseInt($('#case_nature_select').val());
                    if(case_nature_id === 1){
                        var complaint_id = $('#case_nature_complaints').val();
                        var description = $('#complaint_description').val();
                    }
                    else if(case_nature_id === 3){
                        var feedback_flag = true;
                        var feedback_description = $('#feedback_description_request').val();
                    }else{
                        var complaint_id = $('#case_nature_requests').val();
                        var description = $('#service_description').val();
                    }

                    if(case_nature_id === 3)
                    {
                        if(!feedback_description){
                            feedback_flag = false;
                            var error = "Please Enter Description!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        if(feedback_flag){
                            $('#AddNewRequest').attr('disabled',true);
                            $.ajax({
                                url: '{!! route('cod.crm.feedback.add') !!}',
                                method: 'POST',
                                data: {
                                    '_token': '{{ csrf_token() }}',
                                    'shipment_id': $('#requested_shipment_id').val(),
                                    'description' : feedback_description
                                }
                            })
                                .done(function(data) {
                                    if(data.status){
                                        if(data.flag){
                                            var html = '';

                                            $.each(data.already_existed_shipments, function(index, tracking_number) {
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
                                        }else{
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
                                    $('#AddNewRequest').attr('disabled',false);
                                });
                        }
                    }

                    else if(case_nature_id === 4)
                    {
                        var nature_flag = true;
                        var case_nature_claim_id = $('#case_nature_claim').val();
                        var product_cost = $('#claim_product_cost').val();
                        var check_product_picture = $('#product_picture').val();
                        var check_invoice_picture = $('#invoice_picture').val();
                        var claim_description = $('#claim_description').val();
                        $('#shipment_ids').val($('#requested_shipment_id').val());
                        $('#case_nature_id').val(case_nature_id);
                        $('#complaint_id').val(case_nature_claim_id);
                        $('#claim_description').val(claim_description);

                        var formData = new FormData($('#add_request_form')[0]);
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
                        if(!case_nature_claim_id){
                            nature_flag = false;
                            var error = "Please select Claim type!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        if(case_nature_claim_id !== "26"){
                            if(!check_product_picture){
                                nature_flag = false;
                                var error = "Please attach Product Picture!";
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                            if(!product_cost){
                                nature_flag = false;
                                var error = "Please enter Product Cost!";
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                            if(!check_invoice_picture){
                                nature_flag = false;
                                var error = "Please attach Invoice Picture!";
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        }
                        if(nature_flag){
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
                                enctype: 'multipart/form-data',
                                data: formData,
                                dataType: 'json',
                                processData: false,
                                contentType: false,
                            })
                                .done(function(data) {
                                    swal.close();

                                    if(data.status){
                                        if(data.flag){
                                            var html = '';

                                            $.each(data.already_existed_shipments, function(index, tracking_number) {
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
                                        }else{
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
                                    $('#AddNewRequest').attr('disabled',false);
                                });
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

                            var complaint_id = $('#case_nature_requests').val();
                        
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
                            else{
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
                $('#feedback_description_request').val('');
                $('#request_complaints').addClass('d-none');
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
            });


            $('#tracking').on('click', '.replacement_booked_image', function () {
                window.open($(this).data('link'), '_blank');

            });

            $('#tracking').on('click', '.replacement_collected_image', function () {
                window.open($(this).data('link'), '_blank');

            });

            $('#tracking').on('click', '.call_status', function () {
            var id = $(this).attr('id');
            var tracking = $(this).attr('data-tracking');
            var tracking_rows = '<div class="col-4"><span class="mr-1"><i class="la la-angle-right align-bottom"></i><b> '+ tracking +'</b></span></div>';
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
            .done(function(data) {
                if (data) {
                    var modalBody = $('#remarks_log_modal_body');

                    modalBody.html('');

                    var tableHtml = '<table id="call_history_table" class="table-striped table-bordered" style="width:100%">';
                    tableHtml += '<thead class="text-center"><tr><th class="p-1">Calling Date</th><th>Calling Time</th><th>Call Findings</th><th>Un Responsive Finding</th><th>Other Remarks</th><th>Call To</th><th>Status</th><th>User</th></tr></thead>';
                    tableHtml += '<tbody class="text-center">';

                    $.each(data, function(index, value) {
                        var updated_at = value.updated_at;
                        var trimmedDateTime = updated_at.substring(0, 10);
                        var trimmedTime = updated_at.substring(11, 16);
                        var remark = value.remark;
                        var custom_remarks = value.sub_status_call_finding_remarks;
                        if(custom_remarks == null){
                            custom_remarks = '-';
                        }
                        var status = value.status;
                        var updated_by = value.updated_by;
                        var call_to_id = value.call_to_id;
                        if(call_to_id == 1){
                            call_to_id = 'Shipper';
                        } else{
                            call_to_id = 'Consignee';
                        }
                        var call_finding_id = value.call_finding_id;
                        if(call_finding_id == 1){
                            call_finding_id = 'Un-responsive';
                        } else{
                            call_finding_id = '';
                        }

                        tableHtml += '<tr><td class="p-1">' + trimmedDateTime + '</td><td>' + trimmedTime + '</td><td>' + call_finding_id + '</td><td>' + remark + '</td><td>' + custom_remarks + '</td><td>' + call_to_id + '</td><td>' + status + '</td><td>' + updated_by + '</td></tr>';
                    });

                    tableHtml += '</tbody></table>';

                    modalBody.append(tableHtml);
                }
                
                    });

                $('#update_call_status_modal').modal('show');
            });

            $('#update_call_status_modal').on('shown.bs.modal', function () {
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