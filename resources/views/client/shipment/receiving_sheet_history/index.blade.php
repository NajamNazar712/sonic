@extends('client.layout.master')

@section('title', 'Receiving Sheet History')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Receiving Sheet History
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('client.inc.messages')

							<h3 class="mb-1">Over Received Shipments</h3>

							<table class="table table-stripped table-bordered datatable" id="short_received_datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">Received</th>
										<th class="border-primary border-darken-1">Address</th>
										<th class="border-primary border-darken-1">Origin</th>
										<th class="border-primary border-darken-1"></th>
									</tr>
								</thead>
							</table>

							<hr class="mt-2 mb-2">

							<h3 class="mb-1">Receiving Sheets</h3>

							<table class="table table-stripped table-bordered datatable" id="receiving_sheet_datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">Receiving Sheet</th>
										<th class="border-primary border-darken-1">Booked</th>
										<th class="border-primary border-darken-1">Received</th>
										<th class="border-primary border-darken-1">Short Received</th>
										<th class="border-primary border-darken-1">Origin</th>
										<th class="border-primary border-darken-1">Booking Date</th>
										<th class="border-primary border-darken-1"></th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>

				<div class="modal fade" id="booked_shipments" role="dialog" aria-labelledby="booked_shipments_title" aria-hidden="true">
					<div class="modal-dialog modal-sm" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title" id="booked_shipments_title">Booked Shipments</h4>

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

				<div class="modal fade" id="received_shipments" role="dialog" aria-labelledby="received_shipments_title" aria-hidden="true">
					<div class="modal-dialog modal-sm" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title" id="received_shipments_title">Received Shipments</h4>

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

				<div class="modal fade" id="short_received_shipments" role="dialog" aria-labelledby="short_received_shipments_title" aria-hidden="true">
					<div class="modal-dialog modal-sm" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title" id="short_received_shipments_title">Short Received Shipments</h4>

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
			</div>
		</div>
	</div>
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}" type="text/javascript"></script>
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

			var short_received_table = $('#short_received_datatable').DataTable({
				dom: 'ltipr',
				scrollX: true, scrollY: '200px',
				lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
				pageLength: 50,
				pagingType: 'full_numbers',
				processing: true,
				serverSide: true,
				ajax: '{{ route('cod.shipment.receiving_sheet_history.short_received_list') }}',
				rowId: 'pickup_address_id',
				order: [[3, 'asc']],
				columns: [
					{data: 'serial_number', orderable: false, searchable: false, name: 'pickup_address_id', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'received', name: 'receiving_sheets.received', class: 'text-center align-middle received', orderable: false, searchable: false},
					{data: 'pickup_address', name: 'usi.pickup_address', class: 'align-middle pickup_address'},
					{data: 'origin', name: 'c.name', class: 'align-middle origin'},
					{data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
				],
				rowCallback: function(row, data, index) {
					var info = short_received_table.page.info();

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

						if ($(header).is('.serial_number') || $(header).is('.received') || $(header).is('.action')) {
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

			$('#short_received_datatable tbody').on('click', 'tr td.received button', function() {
				var pickup_address_id = parseInt($(this).parents('tr').attr('id'));

				$('#received_shipments .modal-body').html();

				$.ajax({
					url: '{!! route('cod.shipment.receiving_sheet_history.received_shipments') !!}',
					method: 'POST',
					data: {
						'_token': '{{ csrf_token() }}',
						'type': 0,
						'pickup_address_id': pickup_address_id
					}
				})
				.done(function(data) {
					if (data) {
						var tracking_numbers = '';

						$.each(data, function(index, tracking_number) {
							tracking_numbers += tracking_number + '<br/>';
						});

						$('#received_shipments .modal-body').html(tracking_numbers);

						$('#received_shipments').modal('show');
					}
				});
			});

			$('#short_received_datatable tbody').on('click', 'tr td.action button.create_receiving_sheet', function() {
				var pickup_address_id = parseInt($(this).parents('tr').attr('id'));

				$.ajax({
					url: '{!! route('cod.shipment.receiving_sheet_history.create') !!}',
					method: 'POST',
					data: {
						'pickup_address_id': pickup_address_id,
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

					short_received_table.draw('false');

					receiving_sheet_table.draw('false');
				});
			});

			var receiving_sheet_table = $('#receiving_sheet_datatable').DataTable({
				dom: 'ltipr',
				scrollX: true, scrollY: '350px',
				lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
				pageLength: 50,
				pagingType: 'full_numbers',
				processing: true,
				serverSide: true,
				ajax: '{{ route('cod.shipment.receiving_sheet_history.receiving_sheet_list') }}',
				rowId: 'id',
				order: [[6, 'desc']],
				columns: [
					{data: 'serial_number', orderable: false, searchable: false, name: 'id', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'receiving_sheet_id', name: 'receiving_sheets.id', class: 'text-center align-middle receiving_sheet p-1'},
					{data: 'booked', name: 'receiving_sheets.booked', class: 'text-center align-middle booked'},
					{data: 'received', name: 'receiving_sheets.received', class: 'text-center align-middle received'},
					{data: 'short_received', name: 'short_received', class: 'text-center align-middle short_received', orderable: false, searchable: false},
					{data: 'origin', name: 'c.name', class: 'align-middle origin'},
					{data: 'booking_date', name: 'receiving_sheets.created_at', class: 'align-middle booking_date'},
					{data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
				],
				rowCallback: function(row, data, index) {
					var info = receiving_sheet_table.page.info();

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

						if ($(header).is('.serial_number') || $(header).is('.short_received') || $(header).is('.action')) {
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

			$('#receiving_sheet_datatable tbody').on('click', 'tr td.receiving_sheet button', function() {
				print(parseInt($(this).children('.id').html()));
			});

			$('#receiving_sheet_datatable tbody').on('click', 'tr td.booked button', function() {
				var receiving_sheet_id = parseInt($(this).parents('tr').attr('id'));

				$('#booked_shipments .modal-body').html();

				$.ajax({
					url: '{!! route('cod.shipment.receiving_sheet_history.booked_shipments') !!}',
					method: 'POST',
					data: {
						'receiving_sheet_id': receiving_sheet_id,
						'_token': '{{ csrf_token() }}'
					}
				})
				.done(function(data) {
					if (data) {
						var tracking_numbers = '';

						$.each(data, function(index, tracking_number) {
							tracking_numbers += tracking_number + '<br/>';
						});

						$('#booked_shipments .modal-body').html(tracking_numbers);

						$('#booked_shipments').modal('show');
					}
				});
			});

			$('#receiving_sheet_datatable tbody').on('click', 'tr td.received button', function() {
				var receiving_sheet_id = parseInt($(this).parents('tr').attr('id'));

				$('#received_shipments .modal-body').html();

				$.ajax({
					url: '{!! route('cod.shipment.receiving_sheet_history.received_shipments') !!}',
					method: 'POST',
					data: {
						'_token': '{{ csrf_token() }}',
						'type': 1,
						'receiving_sheet_id': receiving_sheet_id
					}
				})
				.done(function(data) {
					if (data) {
						var tracking_numbers = '';

						$.each(data, function(index, tracking_number) {
							tracking_numbers += tracking_number + '<br/>';
						});

						$('#received_shipments .modal-body').html(tracking_numbers);

						$('#received_shipments').modal('show');
					}
				});
			});

			$('#receiving_sheet_datatable tbody').on('click', 'tr td.short_received button', function() {
				var receiving_sheet_id = parseInt($(this).parents('tr').attr('id'));

				$('#short_received_shipments .modal-body').html();

				$.ajax({
					url: '{!! route('cod.shipment.receiving_sheet_history.short_received_shipments') !!}',
					method: 'POST',
					data: {
						'_token': '{{ csrf_token() }}',
						'receiving_sheet_id': receiving_sheet_id
					}
				})
				.done(function(data) {
					if (data) {
						var tracking_numbers = '';

						$.each(data, function(index, tracking_number) {
							tracking_numbers += tracking_number + '<br/>';
						});

						$('#short_received_shipments .modal-body').html(tracking_numbers);

						$('#short_received_shipments').modal('show');
					}
				});
			});

			$('#receiving_sheet_datatable tbody').on('click', 'tr td.action button.view_short_received', function() {
				var receiving_sheet_id = parseInt($(this).parents('tr').attr('id'));

				$.ajax({
					url: '{!! route('cod.shipment.receiving_sheet_history.short_received_shipments') !!}',
					method: 'POST',
					data: {
						'receiving_sheet_id': receiving_sheet_id,
						'_token': '{{ csrf_token() }}'
					}
				})
				.done(function(data) {
					if (data) {
						var html = '';

						$.each(data, function(index, tracking_number) {
							html += tracking_number + '<br/>';
						});

						html += '<br/>Are you sure, you want to void these shipment(s) from Receiving Sheet?';

						content = document.createElement('div');
						content.innerHTML = html;

						swal({
							title: 'Short Received!',
							content: content,
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
									url: '{!! route('cod.shipment.receiving_sheet_history.void') !!}',
									method: 'PUT',
									data: {
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

									receiving_sheet_table.draw('false');
								});
							}
						});
					}
				});
			});
		});
	</script>
@endsection