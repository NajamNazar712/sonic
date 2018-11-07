@extends('admin.layout.master')

@section('title', 'Shipment Cancellation Cut-Off Days')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Shipment Cancellation Cut-Off Days
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<form id="booking_form" class="form-horizontal" method="POST" action="{{ route('cod.shipment.book.store') }}" novalidate="novalidate">
								{{ csrf_field() }}

								<div class="form-group">
									<input type="text" name="shipment_cancellation_cut_off_days" class="form-control" placeholder="Shipment Cancellation Cut-Off Days*" data-rule-required="true" data-msg-required="Shipment Cancellation Cut-Off Days is required" @if ($settings) value="{{ $settings->value }}" @endif>
								</div>

							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			$('#add_shipment_form input.tracking_number').inputmask({
				'alias': 'integer',
				'allowMinus': false,
				'allowPlus': false
			});

			$('#add_shipment_form').validate({
				errorClass: 'danger',
				successClass: 'success',
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parents('form'));
				},
				submitHandler: function(form) {
					$('#add_shipment_form button.add').prop('disabled', true);

					var tracking_number = $(form).find('input.tracking_number').val();

					form.reset();

					if (table.columns('.tracking_number').data().eq(0).indexOf(parseInt(tracking_number)) === -1) {
						$.ajax({
							url: '{!! route('admin.cargo.receive.shipment_details') !!}',
							method: 'POST',
							data: {
								'tracking_number': tracking_number,
								'cargo_consignment_id': cargo_consignment_id,
								'_token': '{{ csrf_token() }}'
							}
						})
						.done(function(data) {
							if (data.status == 0) {
								id = data.details.id;

								var index = $.inArray(id, shipment_ids);

								if (index === -1) {
									table.row.add([0, data.details.tracking_number, data.details.origin, data.details.destination, data.details.hub, data.details.consignee, data.details.amount, data.details.shipping_mode, data.details.service_type]);
									table.draw(false);

									shipment_ids.push(data.details.id);

									$('#information .scanned').html(shipment_ids.length);

									$('#add_shipment_form button.add').prop('disabled', false);

									$('#receive_form .receive').prop('disabled', false);

									toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
								}
							}
							else {
								$('#add_shipment_form button.add').prop('disabled', false);

								toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
							}
						});
					}
					else {
						$('#add_shipment_form button.add').prop('disabled', false);

						toastr.error('Shipment has been added already', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
					}

					return false;
				}
			});
		});
	</script>
@endsection