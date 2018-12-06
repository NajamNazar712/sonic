@extends('admin.layout.master')

@section('title', 'Change Shipment Amount')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Change Shipment Amount
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

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

							@if (session('role_id') == 1 || in_array(58, session('permissions')))
								<form id="change_amount_form" class="form-inline mb-1 justify-content-center mt-2 d-none" method="POST" action="{{ route('admin.finance.change_shipment_amount.store') }}" novalidate="novalidate">
									{{ csrf_field() }}

									<input type="hidden" name="shipment_id" class="shipment_id">

									<div class="form-group">
										<input type="text" name="amount" class="form-control amount" placeholder="Amount*" data-rule-required="true" data-msg-required="Amount is required">
									</div>

									<div class="form-group ml-1">
										<button type="submit" name="change" class="btn btn-primary change" value="Change">Change</button>
									</div>
								</form>
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

					@if (session('role_id') == 1 || in_array(58, session('permissions')))
						$('#change_amount_form').addClass('d-none');

						$('#change_amount_form input.tracking_number').val('');
					@endif

					var tracking_number = $(form).find('input.tracking_number').val();

					form.reset();

					$.ajax({
						url: '{!! route('admin.finance.change_shipment_amount.shipment_details') !!}',
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
							shipment += '<td><strong>Origin</strong></td>';
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

							@if (session('role_id') == 1 || in_array(58, session('permissions')))
								$('#change_amount_form').removeClass('d-none');

								$('#change_amount_form input.shipment_id').val(details.id);
							@endif

							$(form).find('button.search').prop('disabled', false);

							toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
						}
						else {
							$(form).find('button.search').prop('disabled', false);

							toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
						}
					});

					return false;
				}
			});

			@if (session('role_id') == 1 || in_array(58, session('permissions')))
				$('#change_weight_form input.amount').inputmask({
					'alias': 'integer',
					'allowMinus': false,
					'allowPlus': false,
					'groupSeparator': ',',
					'autoGroup': true,
					'max': 1000000
				});

				$('#change_amount_form').validate({
					errorClass: 'danger',
					successClass: 'success',
					errorPlacement: function(error, element) {
						error.addClass('w-100').appendTo(element.parents('form'));
					}
				});
			@endif
		});
	</script>
@endsection