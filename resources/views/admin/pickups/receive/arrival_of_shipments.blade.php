@extends('admin.layout.master')

@section('title', 'Arrival of Shipments')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Arrival of Shipments
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<form id="add_shipment_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
								<input type="hidden" name="pickup_receive_pickup_note_id" class="pickup_receive_pickup_note_id" value="{{ session('pickup_receive_pickup_note_id') }}">

								<div class="form-group">
									<input type="text" name="tracking_number" class="form-control tracking_number" placeholder="Tracking Number*" data-rule-required="true" data-msg-required="Tracking Number is required">
								</div>

								<div class="form-group ml-1">
									<input type="text" name="weight" class="form-control weight" placeholder="Weight (kg)*" data-rule-required="true" data-msg-required="Weight is required">
								</div>

								<div class="form-group text-center mt-1 mb-1 ml-1 p-1 border border-light rounded">
									<label class="mr-1">Volumetric Weight</label>
									<input type="checkbox" name="volumetric_weight" class="switch hidden volumetric_weight" data-group-cls="btn-group-sm">
								</div>

								<div class="form-group ml-1 volumetric_weights">
									<input type="text" name="length" class="form-control form-control-sm length" placeholder="Length (cm)*" data-rule-required="true" data-msg-required="Length is required" disabled="disabled">
								</div>

								<div class="form-group ml-1 volumetric_weights">
									<input type="text" name="breadth" class="form-control form-control-sm breadth" placeholder="Breadth (cm)*" data-rule-required="true" data-msg-required="Breadth is required" disabled="disabled">
								</div>

								<div class="form-group ml-1 volumetric_weights">
									<input type="text" name="height" class="form-control form-control-sm height" placeholder="Height (cm)*" data-rule-required="true" data-msg-required="Height is required" disabled="disabled">
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
										<th class="border-primary border-darken-1">Receiving Sheet No.</th>
										<th class="border-primary border-darken-1">Order ID</th>
										<th class="border-primary border-darken-1">Destination</th>
										<th class="border-primary border-darken-1">COD Amount</th>
										<th class="border-primary border-darken-1">Estimated Weight (kg)</th>
										<th class="border-primary border-darken-1">Actual Weight (kg)</th>
										<th class="border-primary border-darken-1"></th>
									</tr>
								</thead>
							</table>

							<form id="arrival_of_shipments_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.pickups.receive.arrival_of_shipments.store') }}" novalidate="novalidate">
								{{ csrf_field() }}

								<input type="hidden" name="pickup_receive_pickup_note_id" class="pickup_receive_pickup_note_id" value="{{ session('pickup_receive_pickup_note_id') }}">

								<input type="hidden" name="shipment_ids" class="shipment_ids">

								<div class="form-group ml-1">
									<button type="submit" name="confirm" class="btn btn-primary confirm" value="Confirm" disabled="disabled">Confirm</button>
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
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/toggle/bootstrap-switch.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/toggle/bootstrap-checkbox.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			var shipment_ids = [];

			$('#add_shipment_form input.tracking_number').focus();

			var table = $('#datatable').DataTable({
				dom: 'ltipr',
				scrollX: true, scrollY: '300px',
				lengthMenu: [[25, 50, 100], [25, 50, 100]],
				pageLength: 25,
				pagingType: 'full_numbers',
				columns: [
					{orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
					{name: 'tracking_number', class: 'align-middle tracking_number'},
					{name: 'receiving_sheet_no', class: 'align-middle receiving_sheet_no'},
					{name: 'order_id', class: 'align-middle order_id'},
					{name: 'destination', class: 'align-middle destination'},
					{name: 'cod_amount', class: 'align-middle cod_amount'},
					{name: 'estimated_weight', class: 'align-middle estimated_weight'},
					{name: 'actual_weight', class: 'align-middle actual_weight'},
					{name: 'remove', class: 'align-middle remove', sortable: false}
				],
				rowCallback: function(row, data, index) {
					var info = table.page.info();

					$('td:eq(0)', row).html(index + 1 + info.page * info.length);
				},
				initComplete: function() {
					var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

					var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
					var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
					var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

					this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.serial_number') || $(header).is('.remove')) {
							$(td).appendTo($(search));
						}
						else {
							var current = $(input).appendTo($(search)).on('change', function() {
								column.search($(this).val(), false, false, true).draw();
							}).wrap(td).after(icon);

							if (column.search()) {
								current.val(column.search());
							}
						}
					});

					this.api().table().columns.adjust();
				}
			});

			$('#add_shipment_form input.volumetric_weight').checkboxpicker().bind('change', function() {
				var parent = $(this).parent('.form-group').prev('.form-group');

				if (this.checked) {
					$('#add_shipment_form input.weight').val('').prop('disabled', true);

					$('#add_shipment_form .volumetric_weights input').val('').prop('disabled', false);
				}
				else {
					$('#add_shipment_form input.weight').val('').prop('disabled', false);

					$('#add_shipment_form .volumetric_weights input').val('').prop('disabled', true);
				}
			});

			$('#add_shipment_form input.tracking_number').inputmask({
				'alias': 'integer',
				'allowMinus': false,
				'allowPlus': false
			});

			$('#add_shipment_form input.weight').inputmask({
				'alias': 'decimal',
				'allowMinus': false,
				'allowPlus': false,
				'digits': 2,
				'min': 0.1,
				'max': 1000
			});

			$('#add_shipment_form .volumetric_weights input.length').inputmask({
				'alias': 'decimal',
				'allowMinus': false,
				'allowPlus': false,
				'digits': 2,
				'min': 0.1,
				'max': 175
			});

			$('#add_shipment_form .volumetric_weights input.breadth').inputmask({
				'alias': 'decimal',
				'allowMinus': false,
				'allowPlus': false,
				'digits': 2,
				'min': 0.1,
				'max': 175
			});

			$('#add_shipment_form .volumetric_weights input.height').inputmask({
				'alias': 'decimal',
				'allowMinus': false,
				'allowPlus': false,
				'digits': 2,
				'min': 0.1,
				'max': 175
			});

			$('#add_shipment_form').validate({
				errorClass: 'danger',
				successClass: 'success',
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parents('form'));
				},
				submitHandler: function(form) {
					$('#add_shipment_form button.add').prop('disabled', true);

					var pickup_receive_pickup_note_id = $(form).find('input.pickup_receive_pickup_note_id').val();
					var tracking_number = $(form).find('input.tracking_number').val();
					var weight = $(form).find('input.weight').val();
					var length = $(form).find('input.length').val();
					var breadth = $(form).find('input.breadth').val();
					var height = $(form).find('input.height').val();

					if (table.columns('.tracking_number').data().eq(0).indexOf(parseInt(tracking_number)) === -1) {
						$.ajax({
							url: '{!! route('admin.pickups.receive.shipment_details') !!}',
							method: 'POST',
							data: {
								'pickup_receive_pickup_note_id': pickup_receive_pickup_note_id,
								'tracking_number': tracking_number,
								'weight': weight,
								'length': length,
								'breadth': breadth,
								'height': height,
								'_token': '{{ csrf_token() }}'
							}
						})
						.done(function(data) {
							form.reset();

							$('#add_shipment_form input.tracking_number').val('').focus();

							$('#add_shipment_form button.add').prop('disabled', false);

							remove_button = '<button type="button" class="btn btn-icon btn-danger"><i class="la la-close"></i></button>';

							if (data.status == 0) {
								table.row.add([0, data.details.tracking_number, data.details.receiving_sheet_no, data.details.order_id, data.details.destination, data.details.cod_amount, data.details.estimated_weight, data.details.actual_weight, remove_button]).node().id = data.details.id;
								table.draw(false);

								shipment_ids.push(data.details.id);

								$('#arrival_of_shipments_form button.confirm').prop('disabled', false);

								toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
							}
							else {
								toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
							}
						});
					}
					else {
						$('#add_shipment_form button.add').prop('disabled', false);

						toastr.error('Shipment has been added already', 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
					}

					return false;
				}
			});

			$('#arrival_of_shipments_form').bind('submit', function(e) {
				e.preventDefault();

				$('#arrival_of_shipments_form input.shipment_ids').val(shipment_ids);

				var form = this;

				swal({
					text: 'Are you sure, you want to Receive these Shipments?',
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
						form.submit();
					}
				});
			});

			$('#datatable tbody').on('click', 'tr td.remove button', function() {
				var parent = $(this).parents('tr');
				var id = parseInt(parent.attr('id'));

				$.ajax({
					url: '{!! route('admin.pickups.receive.shipment_remove') !!}',
					method: 'POST',
					data: {
						'id': id,
						'_token': '{{ csrf_token() }}'
					}
				})
				.done(function(data) {
					if (data.status == 0) {
						table.row(parent).remove();
						table.draw(false);

						var index = $.inArray(id, shipment_ids);

						if (index !== -1) {
							shipment_ids.splice(index, 1);

							if (shipment_ids.length == 0) {
								$('#arrival_of_shipments_form button.confirm').prop('disabled', true);
							}
						}

						toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
					}
					else {
						toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
					}
				});
			});
		});
	</script>
@endsection