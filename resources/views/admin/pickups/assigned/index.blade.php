@extends('admin.layout.master')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Assigned Pickups
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1"></th>
										<th class="border-primary border-darken-1">ID</th>
										<th class="border-primary border-darken-1">Pickup(s)</th>
										<th class="border-primary border-darken-1">Booking(s)</th>
										<th class="border-primary border-darken-1">Total Estimated Weight (kg)</th>
										<th class="border-primary border-darken-1">Pickup Type</th>
										<th class="border-primary border-darken-1">Assigned Date</th>
										<th class="border-primary border-darken-1">Assigned By</th>
										<th class="border-primary border-darken-1">Pickup Note No.</th>
										<th class="border-primary border-darken-1">Status</th>
										<th class="border-primary border-darken-1">Action</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>

				<div class="modal fade" id="view_details" role="dialog" aria-labelledby="view_details_title" aria-hidden="true">
					<div class="modal-dialog modal-sm" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title" id="view_details_title">Details</h4>
							</div>
							<div class="modal-body">
								<!--  -->
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
			$('#view_details').modal({
				backdrop: 'static',
				keyboard: false,
				show: false
			});

			var selected_rows = [];

			var table = $('#datatable').DataTable({
				dom: '<"d-inline-block"l><"pull-right"B>tipr',
				buttons: [{
					text: 'Print',
					className: 'btn btn-primary print',
					enabled: false,
					action: function (e, dt, node, config) {
					}
				}],
				fixedHeader: {
					header: true,
					headerOffset: $('.header-navbar').height()
				},
				select: {
					info: false,
					style: 'multi',
					selector: 'td.select-checkbox',
					className: 'selected bg-primary bg-lighten-5 primary'
				},
				lengthMenu: [[25, 50, 100], [25, 50, 100]],
				pageLength: 25,
				stateSave: true,
				pagingType: 'full_numbers',
				processing: true,
				serverSide: true,
				ajax: '{{ route('admin.pickups.assigned.list') }}',
				rowId: 'id',
				order: [[1, 'asc']],
				columns: [
					{data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'id', name: 'pickup_notes.id', class: 'align-middle id'},
					{data: 'pickups', name: 'pickup_notes.pickups', class: 'align-middle pickups'},
					{data: 'bookings', name: 'pickup_notes.bookings', class: 'align-middle bookings'},
					{data: 'total_estimated_weight', name: 'pickup_notes.total_estimated_weight', class: 'align-middle total_estimated_weight'},
					{data: 'pickup_type', name: 'pickup_type', class: 'align-middle pickup_type'},
					{data: 'assigned_date', name: 'pickup_notes.created_at', class: 'align-middle assigned_date'},
					{data: 'assigned_by', name: 'a.name', class: 'align-middle assigned_by'},
					{data: 'pickup_note_no', name: 'pickup_note_no', class: 'align-middle pickup_note_no'},
					{data: 'status', name: 'status', class: 'align-middle status'},
					{data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
				],
				rowCallback: function(row, data, index) {
					if (data.status_id == 2) {
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

						if ($(header).is('.select') || $(header).is('.action')) {
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

			$('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
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

			$('.datatable tbody').on('click', 'tr td.action button', function() {
				var pickup_note_id = parseInt($(this).parents('tr').attr('id'));

				if ($(this).hasClass('cancel')) {
					swal({
						title: pickup_note_id,
						text: 'Are you sure, you want to Cancel this Pickup?',
						icon: 'warning',
						buttons: {
							cancel: {
								text: 'Close',
								value: null,
								visible: true,
								closeModal: true,
							},
							confirm: {
								text: 'Cancel',
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
								url: '{!! route('admin.pickups.assigned.cancel') !!}',
								method: 'PUT',
								data: {
									'pickup_note_id': pickup_note_id,
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

								var index = $.inArray(pickup_note_id, selected_rows);

								if (index !== -1) {
									selected_rows.splice(index, 1);
								}

								if (selected_rows.length > 0) {
									table.button(0).enable();
								}
								else {
									table.button(0).disable();
								}

								table.ajax.reload();
							});
						}
					});
				}
				else if ($(this).hasClass('view_details')) {
					$('#view_details .modal-body').html();

					$.ajax({
						url: '{!! route('admin.pickups.assigned.view_details') !!}',
						method: 'POST',
						data: {
							'pickup_note_id': pickup_note_id,
							'_token': '{{ csrf_token() }}'
						}
					})
					.done(function(data) {
						if (data) {
							var pickup_requests = '';

							$.each(data, function(index, details) {
								var shipper = '<div><strong>Shipper</strong><span class="ml-1 border-bottom-primary">' + details.shipper + '</span></div>';
								var contact_person = '<div><strong>Contact Person</strong><span class="ml-1 border-bottom-primary">' + details.contact_person + '</span></div>';
								var contact_number = '<div><strong>Contact Number</strong><span class="ml-1 border-bottom-primary">' + details.contact_number + '</span></div>';
								var address = '<div><strong>Address</strong><span class="ml-1 border-bottom-primary">' + details.address + '</span></div>';
								var bookings = '<div><strong>Bookings</strong><span class="ml-1 border-bottom-primary">' + details.bookings + '</span></div>';
								var total_estimated_weight = '<div><strong>Total Estimated Weight</strong><span class="ml-1 border-bottom-primary">' + details.total_estimated_weight + 'kg</span></div>';
								var pickup_type = '<div><strong>Pickup Type</strong><span class="ml-1 border-bottom-primary">' + details.pickup_type + '</span></div>';

								pickup_requests += '<div class="pl-1 mb-1 border-left-primary border-left-3">' + shipper + contact_person + contact_number + address + bookings + total_estimated_weight + pickup_type + '</div>';
							});

							$('#view_details .modal-body').html(pickup_requests);

							$('#view_details').modal('show');
						}
					});
				}
			});
		});
	</script>
@endsection