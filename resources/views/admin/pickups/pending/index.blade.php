@extends('admin.layout.master')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Pending Pickups
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
										<th class="border-primary border-darken-1">Shipper</th>
										<th class="border-primary border-darken-1">Contact Person</th>
										<th class="border-primary border-darken-1">Contact No(s).</th>
										<th class="border-primary border-darken-1">Address</th>
										<th class="border-primary border-darken-1">City</th>
										<th class="border-primary border-darken-1">No. of Bookings</th>
										<th class="border-primary border-darken-1">Pickup Type</th>
										<th class="border-primary border-darken-1">Booking Date</th>
										<th class="border-primary border-darken-1">Action</th>
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
	<style>
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

		table.dataTable tbody tr td.select-checkbox:before {
			top: 50%;
			border-color: #666EE8;
		}

		table.dataTable tbody tr.selected td.select-checkbox:after {
			top: 50%;
			text-shadow: none;
		}
	</style>
@endsection

@section('js')
	<script>
		$(document).ready(function() {
			var selected_rows_assign = [];
			var selected_rows_cancel = [];

			var table = $('#datatable').DataTable({
				dom: '<"d-inline-block"l><"pull-right"B>tipr',
				buttons: [{
					text: 'Assign',
					className: 'btn btn-primary assign',
					enabled: false,
					action: function (e, dt, node, config) {
						// $.ajax({
						// 	url: '{!! route('admin.pickups.pending.store') !!}',
						// 	method: 'POST',
						// 	data: {
						// 		'receiving_sheet_ids[]': selected_rows,
						// 		'_token': '{{ csrf_token() }}'
						// 	}
						// })
						// .done(function(data) {
						// 	if (data.status == 0) {
						// 		toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

						// 		print(data.receiving_sheet_id);
						// 	}
						// 	else {
						// 		toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
						// 	}

						// 	$.each(selected_rows, function(index, selected_row) {
						// 		table.row($('#datatable tbody tr#' + selected_row)).deselect();
						// 	});

						// 	selected_rows = [];

						// 	table.button(0).disable();

						// 	table.ajax.reload();
						// });
					}
				}, {
					text: 'Cancel',
					className: 'btn btn-danger ml-1 cancel',
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
				ajax: '{{ route('admin.pickups.pending.list') }}',
				rowId: 'id',
				order: [[1, 'asc']],
				columns: [
					{data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'id', name: 'receiving_sheets.id', class: "align-middle id"},
					{data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
					{data: 'contact_person', name: 'usi.poc', class: 'align-middle contact_person'},
					{data: 'contact_number', name: 'usi.phone', class: 'align-middle contact_number'},
					{data: 'address', name: 'usi.pickup_address', class: 'align-middle address'},
					{data: 'city', name: 'ci.city_name', class: 'align-middle city'},
					{data: 'no_of_bookings', name: 'no_of_bookings', class: 'align-middle no_of_bookings'},
					{data: 'pickup_type', name: 'pickup_type', class: 'align-middle pickup_type'},
					{data: 'booking_date', name: 'booking_date', class: 'align-middle booking_date'},
					{data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
				],
				rowCallback: function(row, data, index) {
					if (!data.receiving_sheet) {
						$('td:eq(0)', row).addClass('select-checkbox');

						if ($.inArray(data.id, selected_rows_assign) !== -1) {
							table.row(row).select();
						}

						if ($.inArray(data.id, selected_rows_cancel) !== -1) {
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
				var action = $(this).parent('tr').children('td.action').children('button.cancel');

				if (action.length) {
					var index = $.inArray(id, selected_rows_cancel);

					if (index === -1) {
						selected_rows_cancel.push(id);
					}
					else {
						selected_rows_cancel.splice(index, 1);
					}

					if (selected_rows_cancel.length > 0) {
						table.button(1).enable();
					}
					else {
						table.button(1).disable();
					}

					$.each(selected_rows_assign, function(index, selected_row) {
						table.row($('#datatable tbody tr#' + selected_row)).deselect();
					});

					selected_rows_assign = [];
				}
				else {
					var index = $.inArray(id, selected_rows_assign);

					if (index === -1) {
						selected_rows_assign.push(id);
					}
					else {
						selected_rows_assign.splice(index, 1);
					}

					if (selected_rows_assign.length > 0) {
						table.button(0).enable();
					}
					else {
						table.button(0).disable();
					}

					$.each(selected_rows_cancel, function(index, selected_row) {
						table.row($('#datatable tbody tr#' + selected_row)).deselect();
					});

					selected_rows_cancel = [];
				}
			});
		});
	</script>
@endsection