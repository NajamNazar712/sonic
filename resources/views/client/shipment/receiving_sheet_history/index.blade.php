@extends('client.layout.master')

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

							<table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1"></th>
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">Receiving Sheet</th>
										<th class="border-primary border-darken-1">Booked</th>
										<th class="border-primary border-darken-1">Received</th>
										<th class="border-primary border-darken-1">Origin</th>
										<th class="border-primary border-darken-1">Booking Date</th>
										<th class="border-primary border-darken-1">Action</th>
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
			</div>
		</div>
	</div>
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

	<style>
		table.dataTable {
			font-size: 12px;
		}

		table.dataTable thead tr th {
			padding-left: 0.5em;
			white-space: normal;
			word-wrap: break-word;
		}

		table.dataTable thead tr th:before,
		table.dataTable thead tr th:after {
			height: 20px;
			margin-bottom: -10px;
			bottom: 50% !important;
		}

		table.dataTable tbody tr td {
			padding-left: 0.5em;
			padding-right: 0.5em;
		}

		table.dataTable tbody tr td.select-checkbox:before {
			top: 50%;
			border-color: #64a0d2;
		}

		table.dataTable tbody tr.selected td.select-checkbox:after {
			top: 50%;
			text-shadow: none;
		}

		.btn-group .dropdown-menu .dropdown-item {
			white-space: normal;
		}

		#toast-bottom-center.toast-container {
			text-align: center;
		}

		#toast-bottom-center.toast-container .toast {
			display: table;
			width: auto !important;
			text-align: left;
		}
	</style>
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

			var table = $('#datatable').DataTable({
				dom: 'ltipr',
				lengthMenu: [[25, 50, 100], [25, 50, 100]],
				pageLength: 25,
				stateSave: true,
				pagingType: 'full_numbers',
				processing: true,
				serverSide: true,
				ajax: '{{ route('cod.shipment.receiving_sheet_history.list') }}',
				rowId: 'id',
				order: [[6, 'asc']],
				columns: [
					{data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'serial_number', orderable: false, searchable: false, name: 'id', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data: 'receiving_sheet', name: 'receiving_sheet', class: 'text-center align-middle receiving_sheet p-1'},
					{data: 'booked', name: 'booked', class: 'align-middle booked', orderable: false, searchable: false},
					{data: 'received', name: 'received', class: 'align-middle received', orderable: false, searchable: false},
					{data: 'origin', name: 'ci.city_name', class: 'align-middle origin'},
					{data: 'booking_date', name: 'rs.created_at', class: 'align-middle booking_date'},
					{data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
				],
				rowCallback: function(row, data, index) {
					var info = table.page.info();

					$('td:eq(1)', row).html(index + 1 + info.page * info.length);
				},
				initComplete: function() {
					var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

					var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
					var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
					var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

					this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.booked') || $(header).is('.received') || $(header).is('.action')) {
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
				}
			});

			$('#datatable tbody').on('click', 'tr td.receiving_sheet button', function() {
				print(parseInt($(this).children('.id').html()));
			});

			$('#datatable tbody').on('click', 'tr td.booked button', function() {
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

			$('#datatable tbody').on('click', 'tr td.received button', function() {
				var receiving_sheet_id = parseInt($(this).parents('tr').attr('id'));
				var pickup_address_id = parseInt($(this).attr('data-id'));

				$('#received_shipments .modal-body').html();

				$.ajax({
					url: '{!! route('cod.shipment.receiving_sheet_history.received_shipments') !!}',
					method: 'POST',
					data: {
						'receiving_sheet_id': receiving_sheet_id,
						'pickup_address_id': pickup_address_id,
						'_token': '{{ csrf_token() }}'
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

			$('#datatable tbody').on('click', 'tr td.action button', function() {
				if ($(this).hasClass('view_short_received')) {
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

										table.draw('false');
									});
								}
							});
						}
					});
				}
				else if ($(this).hasClass('create_receiving_sheet')) {
					var pickup_address_id = parseInt($(this).attr('data-id'));

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

						table.draw('false');
					});
				}
			});
		});
	</script>
@endsection