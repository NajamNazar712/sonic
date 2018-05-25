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
										<th class="border-primary border-darken-1">Status</th>
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
	<script>
		$(document).ready(function() {
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
					{data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'id', name: 'pickup_notes.id', class: 'align-middle id'},
					{data: 'pickups', name: 'pickup_notes.pickups', class: 'align-middle pickups'},
					{data: 'bookings', name: 'pickup_notes.bookings', class: 'align-middle bookings'},
					{data: 'total_estimated_weight', name: 'pickup_notes.total_estimated_weight', class: 'align-middle total_estimated_weight'},
					{data: 'pickup_type', name: 'pickup_type', class: 'align-middle pickup_type'},
					{data: 'assigned_date', name: 'pickup_notes.created_at', class: 'align-middle assigned_date'},
					{data: 'assigned_by', name: 'u.name', class: 'align-middle assigned_by'},
					{data: 'status', name: 'status', class: 'align-middle status'},
					{data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
				],
				rowCallback: function(row, data, index) {
					if ($.inArray(data.id, selected_rows) !== -1) {
						table.row(row).select();
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
		});
	</script>
@endsection