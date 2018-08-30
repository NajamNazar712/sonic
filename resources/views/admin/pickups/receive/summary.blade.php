@extends('admin.layout.master')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Received Pickup Summary
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">Shipper</th>
										<th class="border-primary border-darken-1">Contact Person</th>
										<th class="border-primary border-darken-1">Contact Number</th>
										<th class="border-primary border-darken-1">Address</th>
										<th class="border-primary border-darken-1">City</th>
										<th class="border-primary border-darken-1">Bookings</th>
										<th class="border-primary border-darken-1">Received</th>
										<th class="border-primary border-darken-1">Short Received</th>
										<th class="border-primary border-darken-1">Pickup Type</th>
										<th class="border-primary border-darken-1">Booking Date</th>
										<th class="border-primary border-darken-1">Assigned Date</th>
										<th class="border-primary border-darken-1">Assigned By</th>
										<th class="border-primary border-darken-1">Pickup Note No</th>
										<th class="border-primary border-darken-1"></th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>

				<form id="receive_pickup_note_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate" method="POST" action="{{ route('admin.pickups.receive.pickup_note') }}">
					{{ csrf_field() }}

					<input type="hidden" name="summary" value="1">

					<input type="hidden" name="pickup_note_no" class="pickup_note_no" value="{{ session('pickup_receive_pickup_note_id') }}">
				</form>
			</div>
		</div>
	</div>
@endsection

@section('css')
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
			border-color: #666EE8;
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
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			var table = $('#datatable').DataTable({
				dom: 'ltipr',
				fixedHeader: {
					header: true,
					headerOffset: $('.header-navbar').height()
				},
				lengthMenu: [[1, 25, 50, 100], [1, 25, 50, 100]],
				pageLength: 25,
				pagingType: 'full_numbers',
				processing: true,
				serverSide: true,
				ajax: {
					url: '{{ route('admin.pickups.receive.summary.list') }}',
					data: function(data) {
						data.pickup_receive_pickup_note_id = {{ session('pickup_receive_pickup_note_id') }};
					}
				},
				rowId: 'id',
				order: [[9, 'asc']],
				columns: [
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
					{data: 'contact_person', name: 'usi.poc', class: 'align-middle contact_person'},
					{data: 'contact_number', name: 'usi.phone', class: 'align-middle contact_number'},
					{data: 'address', name: 'usi.pickup_address', class: 'align-middle address'},
					{data: 'city', name: 'ci.name', class: 'align-middle city'},
					{data: 'bookings', name: 'pickup_requests.bookings', class: 'align-middle bookings'},
					{data: 'received', name: 'pickup_requests.received', class: 'align-middle received'},
					{data: 'short_received', name: 'pickup_requests.short_received', class: 'align-middle short_received'},
					{data: 'pickup_type', name: 'pickup_type', class: 'align-middle pickup_type'},
					{data: 'booking_date', name: 'pickup_requests.created_at', class: 'align-middle booking_date'},
					{data: 'assigned_date', name: 'pn.created_at', class: 'align-middle assigned_date'},
					{data: 'assigned_by', name: 'a.name', class: 'align-middle assigned_by'},
					{data: 'pickup_note_no', name: 'pn.id', class: 'align-middle pickup_note_no'},
					{data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
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

						if ($(header).is('.serial_number') || $(header).is('.action')) {
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

			$('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
				var pickup_request_id = parseInt($(this).parents('tr').attr('id'));

				if ($(this).hasClass('receive')) {
					$('#receive_pickup_note_form').submit();
				}
				else if ($(this).hasClass('done')) {
					$.ajax({
						url: '{!! route('admin.pickups.receive.summary.request.short_received') !!}',
						method: 'POST',
						data: {
							'pickup_request_id': pickup_request_id,
							'_token': '{{ csrf_token() }}'
						}
					})
					.done(function(data) {
						if (data.status == 0) {
							if (data.short_received) {
								var html = 'There are shipments that are short received from:<br/>';

								$.each(data.short_received, function(receiving_sheet_id, shipments) {
									var receiving_sheet_number = receiving_sheet_id.toString();

									while (receiving_sheet_number.length < 12) {
										receiving_sheet_number = '0' + receiving_sheet_number;
									}

									html += receiving_sheet_number + ': ' + shipments.join(' - ') + '<br/>';
								});

								html += 'Are you sure, you want to mark this Pickup Done?';
							}
							else {
								var html = 'Are you sure, you want to mark this Pickup Done?';
							}

							content = document.createElement('div');
							content.innerHTML = html;

							swal({
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
										text: 'Done',
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
										url: '{!! route('admin.pickups.receive.summary.request.done') !!}',
										method: 'PUT',
										data: {
											'pickup_request_id': pickup_request_id,
											'_token': '{{ csrf_token() }}'
										}
									})
									.done(function(data) {
										if (data.status == 0) {
											toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

											table.draw('false');

											if (data.complete) {
												setTimeout(function() {
													window.location.href = '{{ route('admin.pickups.receive.index') }}';
												}, 2500);
											}
										}
										else {
											toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

											table.draw('false');
										}
									});
								}
							});
						}
						else {
							toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
						}
					});
				}
				else if ($(this).hasClass('not_done')) {
					swal({
						text: 'Are you sure, you want to mark this Pickup Not Done?',
						icon: 'warning',
						buttons: {
							cancel: {
								text: 'Close',
								value: null,
								visible: true,
								closeModal: true,
							},
							confirm: {
								text: 'Not Done',
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
								url: '{!! route('admin.pickups.receive.summary.request.not_done') !!}',
								method: 'PUT',
								data: {
									'pickup_request_id': pickup_request_id,
									'_token': '{{ csrf_token() }}'
								}
							})
							.done(function(data) {
								if (data.status == 0) {
									toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

									if (data.complete) {
										setTimeout(function() {
											window.location.href = '{{ route('admin.pickups.receive.index') }}';
										}, 5000);
									}
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