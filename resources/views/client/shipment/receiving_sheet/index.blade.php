@extends('client.layout.master')

@section('title', 'Receiving Sheet')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Receiving Sheet
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('client.inc.messages')

							<table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1"></th>
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">Tracking Number</th>
										<th class="border-primary border-darken-1">Order ID</th>
										<th class="border-primary border-darken-1">Service Type</th>
										<th class="border-primary border-darken-1">Pickup Address</th>
										<th class="border-primary border-darken-1">Origin</th>
										<th class="border-primary border-darken-1">Destination</th>
										<th class="border-primary border-darken-1">Booking Date</th>
										<th class="border-primary border-darken-1">Receiving Sheet</th>
										<th class="border-primary border-darken-1"></th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>

				<div class="modal fade" id="add_in_receiving_sheet" role="dialog" aria-labelledby="add_in_receiving_sheet_title" aria-hidden="true">
					<div class="modal-dialog modal-sm" role="document">
						<div class="modal-content">
							<form class="form-horizontal">
								{{ csrf_field() }}

								<div class="modal-header">
									<h4 class="modal-title" id="add_in_receiving_sheet_title">Add in Receiving Sheet</h4>
								</div>
								<div class="modal-body">
									<input type="hidden" name="shipment_id" class="shipment_id">

									<div class="form-group m-0">
										<select name="receiving_sheet" class="select2 receiving_sheet" data-rule-required="true" data-msg-required="Receiving Sheet is required"></select>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
									<button type="submit" class="btn btn-primary ml-auto">Add</button>
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
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			function print(id) {
				$.ajax({
					url: '{!! route('cod.shipment.receiving_sheet.print') !!}',
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
			}

			$('#add_in_receiving_sheet').modal({
				backdrop: 'static',
				keyboard: false,
				show: false
			});

			var selected_rows = [];

			var table = $('.datatable').DataTable({
				dom: '<"d-inline-block"l><"pull-right"B>tipr',
				scrollX: true, scrollY: '350px',
				buttons: [{
					text: 'Create',
					className: 'btn btn-primary create',
					enabled: false,
					action: function (e, dt, node, config) {
						$.ajax({
							url: '{!! route('cod.shipment.receiving_sheet.store') !!}',
							method: 'POST',
							data: {
								'shipment_ids[]': selected_rows,
								'_token': '{{ csrf_token() }}'
							}
						})
						.done(function(data) {
							if (data.status == 0) {
								toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

								print(data.receiving_sheet_id);
							}
							else {
								toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
							}

							$.each(selected_rows, function(index, selected_row) {
								table.row($('.datatable tbody tr#' + selected_row)).deselect();
							});

							selected_rows = [];

							table.button(0).disable();

							table.draw('false');
						});
					}
				}],
				select: {
					info: false,
					style: 'multi',
					selector: 'td.select-checkbox',
					className: 'selected bg-primary bg-lighten-5 primary'
				},
				lengthMenu: [[25, 50, 100], [25, 50, 100]],
				pageLength: 25,
				pagingType: 'full_numbers',
				processing: true,
				serverSide: true,
				ajax: '{{ route('cod.shipment.receiving_sheet.list') }}',
				rowId: 'id',
				order: [[7, 'asc']],
				columns: [
					{data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'serial_number', orderable: false, searchable: false, name: 'id', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data: 'tracking_number', name: 'tracking_number', class: 'align-middle tracking_number'},
					{data: 'order_id', name: 'order_id', class: 'align-middle order_id'},
					{data: 'service_type', name: 'bt.booking_type', class: 'align-middle service_type'},
					{data: 'pickup_address', name: 'usi.pickup_address', class: 'align-middle pickup_address'},
					{data: 'origin_city', name: 'oc.name', class: 'align-middle origin_city'},
					{data: 'destination_city', name: 'dc.name', class: 'align-middle destination_city'},
					{data: 'booking_date', name: 'shipments.created_at', class: 'align-middle booking_date'},
					{data: 'receiving_sheet', name: 'receiving_sheet', class: 'text-center align-middle receiving_sheet p-1'},
					{data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
				],
				rowCallback: function(row, data, index) {
					var info = table.page.info();

					$('td:eq(1)', row).html(index + 1 + info.page * info.length);

					if (!data.receiving_sheet) {
						$('td:eq(0)', row).addClass('select-checkbox');

						if ($.inArray(data.id, selected_rows) !== -1) {
							table.row(row).select();
						}
					}
				},
				initComplete: function() {
					var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

					var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
					var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
					var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

					this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.action')) {
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

			$('.datatable tbody').on('click', 'tr td.select-checkbox', function() {
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
				}
				else {
					table.button(0).disable();
				}
			});

			$('#add_in_receiving_sheet form').validate({
				errorClass: 'danger',
				successClass: 'success',
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parent('.form-group'));
				},
				submitHandler: function(form) {
					var shipment_id = parseInt($(form).find('input.shipment_id').val());
					var receiving_sheet_id = parseInt($(form).find('select.receiving_sheet').val());

					$.ajax({
						url: '{!! route('cod.shipment.receiving_sheet.add') !!}',
						method: 'PUT',
						data: {
							'shipment_id': shipment_id,
							'receiving_sheet_id': receiving_sheet_id,
							'_token': '{{ csrf_token() }}'
						}
					})
					.done(function(data) {
						if (data.status == 0) {
							toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
						}
						else {
							toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
						}

						var index = $.inArray(shipment_id, selected_rows);

						if (index !== -1) {
							selected_rows.splice(index, 1);
						}

						if (selected_rows.length > 0) {
							table.button(0).enable();
						}
						else {
							table.button(0).disable();
						}

						table.row($('.datatable tbody tr#' + shipment_id)).deselect();

						table.draw('false');

						$('#add_in_receiving_sheet').modal('hide');
					});
				}
			});

			$('.datatable tbody').on('click', 'tr td.receiving_sheet button.print', function() {
				print(parseInt($(this).children('.id').html()));
			});

			$('.datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
				if ($(this).hasClass('add')) {
					var shipment_id = $(this).parents('tr').attr('id');

					$('#add_in_receiving_sheet input.shipment_id').val(shipment_id);

					$.ajax({
						url: '{!! route('cod.shipment.receiving_sheet.all') !!}',
						method: 'GET'
					})
					.done(function(data) {
						if (data) {
							var options = [];

							$.each(data, function(index, receiving_sheet) {
								var id = receiving_sheet['id'];
								var text = receiving_sheet['id'].toString();

								while (text.length < 6) {
									text = '0' + text;
								}

								options.push({id: id, text: text});
							});

							if ($('#add_in_receiving_sheet .receiving_sheet').hasClass('select2-hidden-accessible')) {
								$('#add_in_receiving_sheet .receiving_sheet').empty();
								$('#add_in_receiving_sheet .receiving_sheet').select2('destroy');
							}

							$('#add_in_receiving_sheet .receiving_sheet').select2({
								width: '100%',
								placeholder: 'Receiving Sheet*',
								data: options
							}).bind('change', function() {
								if ($(this).hasClass('danger')) {
									$(this).valid();
								}
							}).val(null).trigger('change');

							$('#add_in_receiving_sheet').modal('show');
						}
					});
				}
				else {
					var shipment_id = $(this).parents('tr').attr('id');
					var receiving_sheet = $(this).parents('tr').find('td.receiving_sheet .id').html();
					var tracking_number = $(this).parents('tr').children('td.tracking_number').html();

					swal({
						title: tracking_number,
						text: 'Are you sure, you want to Void this Shipment?',
						icon: 'warning',
						buttons: {
							cancel: {
								text: 'Close',
								value: null,
								visible: true,
								closeModal: true,
							},
							confirm: {
								text: 'Void',
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
								url: '{!! route('cod.shipment.receiving_sheet.void') !!}',
								method: 'PUT',
								data: {
									'shipment_id': shipment_id,
									'receiving_sheet_id': parseInt(receiving_sheet),
									'_token': '{{ csrf_token() }}'
								}
							})
							.done(function(data) {
								if (data.status == 0) {
									toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
								}
								else {
									toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
								}

								table.draw('false');
							});
						}
					});
				}
			});
		});
	</script>
@endsection