@extends('admin.layout.master')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Make Payments
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1"></th>
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">Shipper</th>
										<th class="border-primary border-darken-1">City</th>
										<th class="border-primary border-darken-1">Phone No(s).</th>
										<th class="border-primary border-darken-1">Address</th>
										<th class="border-primary border-darken-1">Total Shipments</th>
										<th class="border-primary border-darken-1">Delivered Shipments</th>
										<th class="border-primary border-darken-1">Returned Shipments</th>
										<th class="border-primary border-darken-1">Total Amount</th>
										<th class="border-primary border-darken-1">Total Charges</th>
										<th class="border-primary border-darken-1">Total GST</th>
										<th class="border-primary border-darken-1">Total Payable</th>
										<th class="border-primary border-darken-1">Bank</th>
										<th class="border-primary border-darken-1">Bank Branch</th>
										<th class="border-primary border-darken-1">Account No.</th>
										<th class="border-primary border-darken-1">Account Title</th>
										<th class="border-primary border-darken-1">IBAN</th>
										<th class="border-primary border-darken-1">Account City</th>
										<th class="border-primary border-darken-1">Payment Mode</th>
										<th class="border-primary border-darken-1">Payment Cycle</th>
										<th class="border-primary border-darken-1">Return Shipments Avg. Aging</th>
										<th class="border-primary border-darken-1"></th>
									</tr>
								</thead>
							</table>

							<div class="modal fade" id="delivered_shipments" role="dialog" aria-labelledby="delivered_shipments_title" aria-hidden="true">
								<div class="modal-dialog modal-sm" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h4 class="modal-title" id="delivered_shipments_title">Delivered Shipment(s)</h4>

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

							<div class="modal fade" id="returned_shipments" role="dialog" aria-labelledby="returned_shipments_title" aria-hidden="true">
								<div class="modal-dialog modal-sm" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h4 class="modal-title" id="returned_shipments_title">Returned Shipment(s)</h4>

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

							<div class="modal fade" id="view_details" role="dialog" aria-labelledby="view_details_title" aria-hidden="true">
								<div class="modal-dialog modal-lg" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h4 class="modal-title" id="view_details_title">Details</h4>

											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">×</span>
											</button>
										</div>
										<div class="modal-body">
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
										</div>
									</div>
								</div>
							</div>

							<div class="modal fade" id="make_payments" role="dialog" aria-labelledby="make_payments_title" aria-hidden="true">
								<div class="modal-dialog modal-lg modal-full-length" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h4 class="modal-title" id="make_payments_title">Make Payments<span></span></h4>

											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">×</span>
											</button>
										</div>
										<div class="modal-body">
											<table class="table table-bordered datatable" id="make_payments_datatable" style="z-index: 3;">
												<thead>
													<tr role="row" class="bg-primary white">
														<th class="border-primary border-darken-1"></th>
														<th class="border-primary border-darken-1">S. No.</th>
														<th class="border-primary border-darken-1">Shipper</th>
														<th class="border-primary border-darken-1">Shipment</th>
														<th class="border-primary border-darken-1">Type</th>
														<th class="border-primary border-darken-1">Amount</th>
														<th class="border-primary border-darken-1">Charges</th>
														<th class="border-primary border-darken-1">GST</th>
														<th class="border-primary border-darken-1">Payable</th>
													</tr>
												</thead>
											</table>

											<form id="make_payments_form" class="form-inline mt-1 mb-1 justify-content-center" novalidate="novalidate" method="POST" action="{{ route('admin.finance.make_payments.store') }}">
												{{ csrf_field() }}

												<input type="hidden" name="pending_payment_shipment_ids" class="pending_payment_shipment_ids">

												<div class="form-group">
													<input type="text" name="total_amount" class="form-control total_amount" placeholder="Total Amount" readonly="readonly">
												</div>

												<div class="form-group ml-1">
													<input type="text" name="total_charges" class="form-control total_charges" placeholder="Total Charges" readonly="readonly">
												</div>

												<div class="form-group ml-1">
													<input type="text" name="total_gst" class="form-control total_gst" placeholder="Total GST" readonly="readonly">
												</div>

												<div class="form-group ml-1">
													<input type="text" name="total_payable" class="form-control total_payable" placeholder="Total Payable" readonly="readonly">
												</div>

												<div class="form-group ml-1">
													<input type="text" name="total_hold" class="form-control total_hold" placeholder="Total Hold" readonly="readonly">
												</div>

												<div class="w-100"></div>

												<button type="button" class="mr-auto btn btn-secondary" data-dismiss="modal">Close</button>
												<button type="submit" name="reconcile" class="btn btn-primary make">Make</button>
											</form>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('css')
	<style>
		table,
		table.dataTable {
			font-size: 12px;
		}

		table thead tr th,
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

		table tbody tr td,
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
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			var selected_rows = [];

			var selected_pending_payment_ids = [];

			var selected_rows_shipments = [];

			var table = $('#datatable').DataTable({
				scrollX: true,
				dom: '<"d-inline-block"l><"pull-right"B>tipr',
				buttons: [{
					text: 'Make Payment(s)',
					className: 'btn btn-primary make_payment',
					enabled: false,
					action: function (e, dt, node, config) {
						//TO DO
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
				lengthMenu: [[1, 25, 50, 100], [1, 25, 50, 100]],
				pageLength: 25,
				stateSave: true,
				pagingType: 'full_numbers',
				processing: true,
				serverSide: true,
				ajax: '{{ route('admin.finance.make_payments.list') }}',
				rowId: 'id',
				order: [[2, 'asc']],
				columns: [
					{data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data:'shipper', name: 'u.name', class: 'align-middle text-center shipper'},
					{data:'city', name: 'c.name', class: 'align-middle text-center city'},
					{data:'phone_numbers', name: 'phone_numbers', class: 'align-middle text-center phone_numbers'},
					{data:'address', name: 'u.address', class: 'align-middle text-center address'},
					{data:'total_shipments', name: 'pending_payments.total_shipments', class: 'align-middle text-center total_shipments'},
					{data:'delivered_shipments', name: 'pending_payments.delivered_shipments', class: 'align-middle text-center delivered_shipments'},
					{data:'returned_shipments', name: 'pending_payments.returned_shipments', class: 'align-middle text-center returned_shipments'},
					{data:'total_amount', name: 'total_amount', class: 'align-middle text-center total_amount'},
					{data:'total_charges', name: 'total_charges', class: 'align-middle text-center total_charges'},
					{data:'total_gst', name: 'total_gst', class: 'align-middle text-center total_gst'},
					{data:'total_payable', name: 'total_payable', class: 'align-middle text-center total_payable'},
					{data:'bank', name: 'ubi.bank_name', class: 'align-middle text-center bank'},
					{data:'bank_branch', name: 'ubi.bank_branch', class: 'align-middle text-center bank_branch'},
					{data:'account_no', name: 'ubi.account_no', class: 'align-middle text-center account_no'},
					{data:'account_title', name: 'ub.account_title', class: 'align-middle text-center account_title'},
					{data:'iban', name: 'ubi.iban', class: 'align-middle text-center iban'},
					{data:'account_city', name: 'bc.name', class: 'align-middle text-center account_city'},
					{data:'payment_mode', name: 'ubi.payment_mode', class: 'align-middle text-center payment_mode'},
					{data:'payment_cycle', name: 'ubi.payment_cycle', class: 'align-middle text-center payment_cycle'},
					{data:'return_shipments_average_aging', name: 'return_shipments_average_aging', class: 'align-middle text-center return_shipments_average_aging'},
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

						if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.total_amount') || $(header).is('.total_charges') || $(header).is('.total_gst') || $(header).is('.total_payable') || $(header).is('.return_shipments_average_aging') || $(header).is('.action')) {
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

			var make_payments_table = $('#make_payments #make_payments_datatable').DataTable({
				dom: 'tr',
				select: {
					info: false,
					style: 'multi',
					selector: 'td.select-checkbox',
					className: 'selected bg-primary bg-lighten-5 primary'
				},
				processing: true,
				serverSide: true,
				ajax: {
					url: '{{ route('admin.finance.make_payments.shipments_list') }}',
					data: function (d) {
						d.ids = selected_pending_payment_ids;
					}
				},
				rowId: 'id',
				order: [[2, 'asc']],
				columns: [
					{data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data:'delivery_note_number', name: 'dn.id', class: 'align-middle text-center delivery_note_number'},
					{data:'hub', name: 'h.name', class: 'align-middle hub'},
					{data:'rider', name: 'ri.name', class: 'align-middle rider'},
					{data:'route', name: 'route', class: 'align-middle route'},
					{data:'shipments', name: 'dn.shipments_count', class: 'align-middle shipments'},
					{data:'delivered_shipments', name: 'dn.delivered_shipments', class: 'align-middle delivered_shipments'},
					{data:'assigned_by', name: 'a.name', class: 'align-middle assigned_by'},
					{data:'assigned_at', name: 'dn.created_at', class: 'align-middle assigned_at'},
					{data:'updated_by', name: 'a.name', class: 'align-middle updated_by'},
					{data:'updated_at', name: 'dn.updated_at', class: 'align-middle updated_at'},
					{data:'dncc_amount', name: 'dn.received_cod_amount', class: 'align-middle dncc_amount'},
					{data:'expense', name: 'dn.expense', class: 'align-middle expense'}
				],
				rowCallback: function(row, data, index) {
					$('td:eq(1)', row).html(index + 1);
				},
				initComplete: function() {
					var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

					var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
					var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
					var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

					this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.select') || $(header).is('.serial_number')) {
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

			$('#datatable tbody').on('click', 'tr td.delivered_shipments button', function() {
				var id = parseInt($(this).parents('tr').attr('id'));

				$('#delivered_shipments .modal-body').html('');

				$.ajax({
					url: '{!! route('admin.finance.make_payments.delivered_shipments') !!}',
					method: 'POST',
					data: {
						'_token': '{{ csrf_token() }}',
						'id': id
					}
				})
				.done(function(data) {
					if (data) {
						var tracking_numbers = '';

						$.each(data, function(index, tracking_number) {
							tracking_numbers += tracking_number + '<br/>';
						});

						$('#delivered_shipments .modal-body').html(tracking_numbers);

						$('#delivered_shipments').modal('show');
					}
				});
			});

			$('#datatable tbody').on('click', 'tr td.returned_shipments button', function() {
				var id = parseInt($(this).parents('tr').attr('id'));

				$('#returned_shipments .modal-body').html('');

				$.ajax({
					url: '{!! route('admin.finance.make_payments.returned_shipments') !!}',
					method: 'POST',
					data: {
						'_token': '{{ csrf_token() }}',
						'id': id
					}
				})
				.done(function(data) {
					if (data) {
						var tracking_numbers = '';

						$.each(data, function(index, tracking_number) {
							tracking_numbers += tracking_number + '<br/>';
						});

						$('#returned_shipments .modal-body').html(tracking_numbers);

						$('#returned_shipments').modal('show');
					}
				});
			});

			$('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
				var id = parseInt($(this).parents('tr').attr('id'));

				if ($(this).hasClass('view_details')) {
					$('#view_details .modal-body').html('');

					$.ajax({
						url: '{!! route('admin.finance.make_payments.shipment_details') !!}',
						method: 'POST',
						data: {
							'_token': '{{ csrf_token() }}',
							'id': id
						}
					})
					.done(function(data) {
						var details = '<table class="table table-sm table-bordered"><thead><tr role="row" class="bg-primary white"><th class="border-primary border-darken-1 align-middle text-center"Shipment</th><th class="border-primary border-darken-1 align-middle text-center">Type</th><th class="border-primary border-darken-1 align-middle text-center">Amount</th><th class="border-primary border-darken-1 align-middle text-center">Charges</th><th class="border-primary border-darken-1 align-middle text-center">GST</th><th class="border-primary border-darken-1 align-middle text-center">Payable</th></tr></thead><tbody>';

						$.each(data, function(index, detail) {
							details += '<tr>';
							details += '<td class="align-middle text-center">' + detail.tracking_number + '</td>';
							details += '<td class="align-middle text-center">' + detail.type + '</td>';
							details += '<td class="align-middle text-center">' + detail.amount + '</td>';
							details += '<td class="align-middle text-center">' + detail.charges + '</td>';
							details += '<td class="align-middle text-center">' + detail.gst + '</td>';
							details += '<td class="align-middle text-center">' + detail.payable + '</td>';
							details += '</tr>';
						});

						details += '</tbody></table>';

						$('#view_details .modal-body').html(details);

						$('#view_details').modal('show');
					});
				}
				else if ($(this).hasClass('make_payment')) {
					selected_pending_payment_ids = [];

					selected_pending_payment_ids.push(id);

					make_payments_table.clear().draw();

					$('#make_payments').modal('show');
				}
			});

			$('#make_payments #make_payments_datatable tbody').on('click', 'tr td.select-checkbox', function() {
				var id = parseInt($(this).parent('tr').attr('id'));

				var index = $.inArray(id, selected_rows_shipments);

				if (index === -1) {
					selected_rows_shipments.push(id);
				}
				else {
					selected_rows_shipments.splice(index, 1);
				}

				//Calculation of Totals
			});
		});
	</script>
@endsection