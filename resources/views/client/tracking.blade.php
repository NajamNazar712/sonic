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
								<div class="form-group">
									<input type="text" name="tracking_numbers" class="tracking_numbers" placeholder="Tracking Number(s)*" data-tags-input-name="tracking_number" data-rule-required="true" data-msg-required="Tracking Number is required">
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
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
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
	<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>

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
                                var shipment = '';

                                shipment += '<div class="mt-4 border-primary">';
                                shipment += '<div class="d-flex align-items-center bg-primary">';
                                shipment += '<div class="mb-0 ml-1 font-medium-3 white">' + details.tracking_number + '</div>';
                                shipment += '<button class="btn btn-secondary ml-auto mr-0 mr-sm-1  add_request" id=' + id + ' data-tracking=' + details.tracking_number + '>Add Request</button>';
                                shipment += '<button class="btn btn-secondary d-sm-inline-block print" id=' + id + '>Print</button>';
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
                                shipment += '<th><strong>Received/Refused By</strong></th>';
                                shipment += '</tr>';
                                shipment += '</thead>';
                                shipment += '<tbody>';

                                $.each(details.tracking_history, function(index, history) {
                                    shipment += '<tr>';
                                    shipment += '<td>' + history.date_time + '</td>';
                                    shipment += '<td>' + history.status + '</td>';
                                    shipment += '<td>' + ((history.status_reason) ? history.status_reason : '') + '</td>';
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

                                shipment += '</div>';
                                shipment += '</div>';


                                shipment += '</div>';

                                $('#tracking').append(shipment);
                            });

                            $('#tracking table.datatable.tracking_history').DataTable({
                                dom: 't',
                                paging: false,
                                order: [[0, 'desc']],
                                columns: [
                                    {name: 'date_time', class: 'align-middle date_time'},
                                    {name: 'status', class: 'align-middle status'},
                                    {name: 'reason', class: 'align-middle reason'},
                                    {name: 'city', class: 'align-middle city'}
                                ]
                            });

                            $('#tracking table.datatable.payment_history').DataTable({
                                dom: 't',
                                paging: false,
                                order: [[0, 'desc']],
                                columns: [
                                    {name: 'date_time', class: 'align-middle date_time'},
                                    {name: 'status', class: 'align-middle status'},
                                    {name: 'user', class: 'align-middle user'}
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
                }else if(id === 2){
                    $('#request_complaints').addClass('d-none');
                    $('#request_service').removeClass('d-none');
                    $('#request_feedback').addClass('d-none');
                    $('#AddNewRequest').removeClass('d-none');

                }
                else if(id === 3){
                    $('#request_complaints').addClass('d-none');
                    $('#request_service').addClass('d-none');
                    $('#request_feedback').removeClass('d-none');
                    $('#AddNewRequest').removeClass('d-none');

                }else{
                    $('#request_complaints').addClass('d-none');
                    $('#request_service').addClass('d-none');
                    $('#request_feedback').addClass('d-none');
                    $('#AddNewRequest').addClass('d-none');

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

                                            html += '<br/>Request/Complaint already lodged for the above Shipment(s) !';

                                            content = document.createElement('div');
                                            content.innerHTML = html;

                                            swal({
                                                title: 'Request / Complaint Already Lodged!',
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
                    else {
                        $('#AddNewRequest').attr('disabled',true);
                        $.ajax({
                            url: '{!! route('cod.crm.request.add') !!}',
                            method: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'shipment_id': $('#requested_shipment_id').val(),
                                'case_nature_id': case_nature_id,
                                'complaint_id': complaint_id,
                                'description': description
                            }
                        })
                            .done(function (data) {
                                if (data.status) {
                                    if (data.flag) {
                                        var html = '';

                                        $.each(data.already_existed_shipments, function (index, tracking_number) {
                                            html += tracking_number + '<br/>';
                                        });

                                        html += '<br/>Request/Complaint already lodged for the above Shipment(s) !';

                                        content = document.createElement('div');
                                        content.innerHTML = html;

                                        swal({
                                            title: 'Request / Complaint Already Lodged!',
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
            });
		});
	</script>
@endsection