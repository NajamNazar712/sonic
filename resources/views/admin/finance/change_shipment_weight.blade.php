@extends('admin.layout.master')

@section('title', 'Change Shipment Weight')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Change Shipment Weight
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<form id="shipment_weight_excel_form" class="form-horizontal" method="POST" action="{{ route('admin.finance.change_shipment_weight.excel_store') }}" novalidate="novalidate" enctype="multipart/form-data">
								{{ csrf_field() }}

								<div class="row align-items-center justify-content-center">
									<div class="col">
										<div class="form-group">
											<input type="file" name="shipments" class="w-100 p-1 border-primary" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
										</div>
									</div>

									<div class="col">
										<div class="form-group text-left">
											<button type="submit" name="upload" class="btn btn-primary">Upload</button>
										</div>
									</div>

									<div class="col ml-auto">
										<div class="form-group text-right">
											<a href="{{ asset('file/Change Shipment Weight Template.xlsx') }}" class="btn btn-primary"><i class="la la-download"></i> Download Template</a>
										</div>
									</div>
								</div>
							</form>

							<form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
								<div class="form-group">
									<input type="text" name="tracking_numbers" class="form-control tracking_number" placeholder="Tracking Number*" data-rule-required="true" data-msg-required="Tracking Number is required">
								</div>

								<div class="form-group ml-1">
									<button type="submit" name="search" class="btn btn-primary search" value="Search">Search</button>
								</div>
							</form>

							<div class="shipment mt-2" id="shipment">
							</div>

							@if (session('role_id') == 1 || in_array(135, session('permissions')))
								<div class="row justify-content-center">
									<div class="col-6">
										<form id="change_weight_form" class="form mb-1 justify-content-center mt-2 d-none" method="POST" action="{{ route('admin.finance.change_shipment_weight.store') }}" novalidate="novalidate">
											{{ csrf_field() }}

											<input type="hidden" name="shipment_id" class="shipment_id">

											<div class="form-group">
												<input type="text" name="weight" class="form-control weight" placeholder="Weight (kg)*" data-rule-required="true" data-msg-required="Weight is required" data-rule-range="[0.01,100000]" data-msg-range="Weight needs to be from 0.01 to 100000">
											</div>
											<div id="replacement_div" class="d-none">
												<div class="form-group">
													<div id="replacement_switch_div" class="form-group text-center p-1 border border-light rounded">
														<label class="d-block">Replacement Items Weight Breakup</label>
														<input type="checkbox" name="replacement_checkbox" class="switch" id="replacement_checkbox">
													</div>
												</div>
												<div id="replacement_weight_div" class="d-none">

														<div class="form-group">
														<input type="text" name="shipment_weight" class="form-control weight" placeholder="Shipment Weight (kg)*" data-rule-range="[0.01,100000]" data-msg-range="Shipment Weight needs to be from 0.01 to 100000">
													</div>
														<div class="form-group">
														<input type="text" name="replacement weight" class="form-control weight" placeholder="Replacement Shipment Weight (kg)*" data-rule-required="true" data-msg-required="Replacement Weight is required" data-rule-range="[0.01,100000]" data-msg-range="Shipment Weight needs to be from 0.01 to 100000">
													</div>
												</div>

											</div>

											<div class="form-group text-center">
												<button type="submit" name="change" class="btn btn-primary change" value="Change">Change</button>
											</div>
										</form>
									</div>
								</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

	<style>
		table.table.table-sm td {
			border: 1px solid #626E82 !important;
		}
	</style>
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			$('#search_form input.tracking_number').inputmask({
				'alias': 'integer',
				'allowMinus': false,
				'allowPlus': false
			});

			$('#search_form').validate({
				errorClass: 'danger',
				successClass: 'success',
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parents('form'));
				},
				submitHandler: function(form) {
					$(form).find('button.search').prop('disabled', true);

					$('#shipment').html('');

					@if (session('role_id') == 1 || in_array(135, session('permissions')))
						$('#change_weight_form').addClass('d-none');

						$('#change_weight_form input.tracking_number').val('');
					@endif

					var tracking_number = $(form).find('input.tracking_number').val();

					form.reset();

					$.ajax({
						url: '{!! route('admin.finance.change_shipment_weight.shipment_details') !!}',
						method: 'POST',
						data: {
							'_token': '{{ csrf_token() }}',
							'tracking_number': tracking_number
						}
					})
					.done(function(data) {
						if (data.status == 0) {
							details = data.details;

							shipment = '<div class="row justify-content-between">';

							shipment += '<div class="col-12">';
							shipment += '<table class="table table-sm table-bordered mb-0">';
							shipment += '<tbody>';
							shipment += '<tr>';
							shipment += '<td><strong>Tracking Number</strong></td>';
							shipment += '<td><strong>Status</strong></td>';
							shipment += '<td><strong>Service Type</strong></td>';
							shipment += '<td><strong>Shipping Mode</strong></td>';
							shipment += '<td><strong>Weight</strong></td>';
							shipment += '<td><strong>Payment Mode</strong></td>';
							shipment += '<td><strong>Amount</strong></td>';
							shipment += '</tr>';
							shipment += '<tr>';
							shipment += '<td>' + details.tracking_number + '</td>';
							shipment += '<td>' + details.status + '</td>';
							shipment += '<td>' + details.service_type + '</td>';
							shipment += '<td>' + details.shipping_mode + '</td>';
							shipment += '<td>' + details.weight + ' kg</td>';
							shipment += '<td>' + details.payment_mode + '</td>';
							shipment += '<td>Rs. ' + details.amount + '</td>';
							shipment += '</tr>';
							shipment += '</tbody>';
							shipment += '</table>';
							shipment += '</div>';

							shipment += '<div class="col-6 mt-1">';
							shipment += '<table class="table table-sm table-bordered mb-0">';
							shipment += '<tbody>';
							shipment += '<tr>';
							shipment += '<td><strong>Shipper</strong></td>';
							shipment += '<td>' + details.shipper.name + '</td>';
							shipment += '<td><strong>Account No.</strong></td>';
							shipment += '<td>' + details.shipper.account_number + '</td>';
							shipment += '</tr>';
							shipment += '<tr>';
							shipment += '<td><strong>Phone No(s).</strong></td>';

							if (!details.shipper.phone_number_2) {
								shipment += '<td>' + details.shipper.phone_number_1 + '</td>';
							}
							else {
								shipment += '<td>' + details.shipper.phone_number_1 + '<br/>' + details.shipper.phone_number_2 + '</td>';
							}

							shipment += '<td><strong>Origin</strong></td>';
							shipment += '<td>' + details.shipper.origin + '</td>';
							shipment += '</tr>';
							shipment += '<tr>';
							shipment += '<td><strong>Address</strong></td>';
							shipment += '<td colspan="3">' + details.shipper.address + '</td>';
							shipment += '</tr>';
							shipment += '</tbody>';
							shipment += '</table>';
							shipment += '</div>';

							shipment += '<div class="col-6 mt-1">';
							shipment += '<table class="table table-sm table-bordered mb-0">';
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
							shipment += '<tr>';
							shipment += '<td><strong>Address</strong></td>';
							shipment += '<td colspan="3">' + details.consignee.address + '</td>';
							shipment += '</tr>';
							shipment += '</tbody>';
							shipment += '</table>';
							shipment += '</div>';

							$('#shipment').html(shipment);

							if(details.booking_type_id == 2){
								$('#replacement_div').removeClass('d-none');
							}
							@if (session('role_id') == 1 || in_array(135, session('permissions')))
								$('#change_weight_form').removeClass('d-none');

								$('#change_weight_form input.shipment_id').val(details.id);
							@endif

							$(form).find('button.search').prop('disabled', false);

							toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
							if(data.warning != ''){
								toastr.error(data.warning, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
							}
						}
						else {
							$(form).find('button.search').prop('disabled', false);

							toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
						}
					});

					return false;
				}
			});

			@if (session('role_id') == 1 || in_array(135, session('permissions')))
				$('#change_weight_form input.weight').inputmask({
					'alias': 'decimal',
					'allowMinus': false,
					'allowPlus': false,
					'digits': 2
				});

				$('#change_weight_form').validate({
					errorClass: 'danger',
					successClass: 'success',
					errorPlacement: function(error, element) {
						error.addClass('w-100').appendTo(element.parent('.form-group'));
					}
				});
			@endif

			$('#replacement_checkbox').checkboxpicker();
			$('#replacement_checkbox').on('change', function() {
				var check = $(this);
				if(check.is(':checked')){
					$('#replacement_weight_div').removeClass('d-none');
					$('input[name="weight"]').addClass('d-none');
				}else{
					$('#replacement_weight_div').addClass('d-none');
					$('input[name="weight"]').removeClass('d-none');
				}
			});

			$('#shipment_weight_excel_form').validate({
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
						text: 'Your Payment(s) are being updated!',
						icon: 'info',
						buttons: false,
						closeOnClickOutside: false,
						closeOnEsc: false
					});

					form.submit();
				}
			});


		});
	</script>
@endsection