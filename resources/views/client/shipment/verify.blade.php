@extends('client.layout.master')

@section('title', 'Shipments Verify')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Shipments Verify
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('client.inc.messages')

							<form id="consignee_tracking_number_and_phone_number_search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
								<div class="form-group">
									<input type="text" name="tracking_number" class="form-control tracking_number" placeholder="Tracking Number" data-rule-required="true" data-msg-required="Tracking Number is required">
								</div>

								<div class="form-group ml-1">
									<input type="text" name="consignee_phone_number" class="form-control consignee_phone_number" placeholder="Consignee Phone Number" data-rule-required="true" data-msg-required="Consignee Phone Number is required">
								</div>

								<div class="form-group ml-1">
									<button type="submit" name="add" class="btn btn-primary add" value="Add">Add</button>
								</div>
							</form>

							<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">Tracking Number</th>
										<th class="border-primary border-darken-1">Consignee Name</th>
										<th class="border-primary border-darken-1">Consignee Phone Number</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">

	<style>
		#consignee_tracking_number_and_phone_number_search_form input.tracking_number {
			min-width: 225px;
		}

		#consignee_tracking_number_and_phone_number_search_form input.consignee_phone_number {
			min-width: 225px;
		}
	</style>
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			$('#consignee_tracking_number_and_phone_number_search_form input.tracking_number').focus();

			var table = $('#datatable').DataTable({
				dom: '<"d-inline-block"l><"pull-right"B>tipr',
				buttons: [{
					extend: 'excel',
					title: 'Shipments Verify',
					className:'btn btn-primary',
					text: '<i class="la la-file-excel-o"></i> Excel'
				}],
				paging: false,
				columns: [
					{name: 'serial_number', orderable: false, searchable: false, class: 'align-middle serial_number'},
					{name: 'tracking_number', class: 'align-middle tracking_number', orderable: false},
					{name: 'consignee_name', class: 'align-middle consignee_name', orderable: false},
					{name: 'consignee_phone_number', class: 'align-middle consignee_phone_number', orderable: false}
				]
			});

			$('#consignee_tracking_number_and_phone_number_search_form input.tracking_number').inputmask({
				'alias': 'integer',
				'allowMinus': false,
				'allowPlus': false
			});

			$('#consignee_tracking_number_and_phone_number_search_form input.consignee_phone_number').inputmask({
				'alias': 'integer',
				'allowMinus': false,
				'allowPlus': false
			});

			var shipment_ids = [];
			var serial_number = 1;

			$('#consignee_tracking_number_and_phone_number_search_form').validate({
				errorClass: 'danger',
				successClass: 'success',
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parents('form'));
				},
				submitHandler: function(form) {
					$('#consignee_tracking_number_and_phone_number_search_form button.add').prop('disabled', true);

					var tracking_number = $(form).find('input.tracking_number').val();
					var consignee_phone_number = $(form).find('input.consignee_phone_number').val();

					$(form).find('input.tracking_number').val('');
					$(form).find('input.consignee_phone_number').val('');

					if (table.columns('.tracking_number').data().eq(0).indexOf(parseInt(tracking_number)) === -1) {
						blockPagePermanently();

						$.ajax({
							url: '{!! route('cod.shipment.verify.store') !!}',
							method: 'POST',
							data: {
								'_token': '{{ csrf_token() }}',
								'tracking_number': tracking_number,
								'consignee_phone_number': consignee_phone_number
							}
						})
						.done(function(data) {
							if (data.status == 0) {
								id = data.shipment.id;

								var index = $.inArray(id, shipment_ids);

								if (index === -1) {
									table.row.add([serial_number, data.shipment.tracking_number, data.shipment.consignee_name, data.shipment.consignee_phone_number]).node().id = data.shipment.id;

									shipment_ids.push(data.shipment.id);

									serial_number++;

									table.draw(false);

									table.order([0, 'desc']).draw();

									table.button('.print').enable();

									scan_sound(1);

									UnblockPagePermanently();

									$('#consignee_tracking_number_and_phone_number_search_form #tracking_number-error').remove();

									$('#consignee_tracking_number_and_phone_number_search_form #consignee_phone_number-error').remove();

									$('#consignee_tracking_number_and_phone_number_search_form button.add').prop('disabled', false);

									$('#consignee_tracking_number_and_phone_number_search_form input.tracking_number').focus();

									toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
								}
								else {
									$('#consignee_tracking_number_and_phone_number_search_form #tracking_number-error').remove();

									$('#consignee_tracking_number_and_phone_number_search_form #consignee_phone_number-error').remove();

									$('#consignee_tracking_number_and_phone_number_search_form button.add').prop('disabled', false);

									UnblockPagePermanently();

									scan_sound(2);

									$('#consignee_tracking_number_and_phone_number_search_form input.tracking_number').focus();

									toastr.error('Shipment has been added already', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
								}
							}
							else {
								$('#consignee_tracking_number_and_phone_number_search_form #tracking_number-error').remove();

								$('#consignee_tracking_number_and_phone_number_search_form #consignee_phone_number-error').remove();

								$('#consignee_tracking_number_and_phone_number_search_form button.add').prop('disabled', false);

								UnblockPagePermanently();

								scan_sound(2);

								$('#consignee_tracking_number_and_phone_number_search_form input.tracking_number').focus();

								toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
							}
						});
					}
					else {
						$('#consignee_tracking_number_and_phone_number_search_form #tracking_number-error').remove();

						$('#consignee_tracking_number_and_phone_number_search_form #consignee_phone_number-error').remove();

						$('#consignee_tracking_number_and_phone_number_search_form button.add').prop('disabled', false);

						UnblockPagePermanently();

						scan_sound(2);

						$('#consignee_tracking_number_and_phone_number_search_form input.tracking_number').focus();

						toastr.error('Shipment has been added already', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
					}

					return false;
				}
			});
		});
	</script>
@endsection