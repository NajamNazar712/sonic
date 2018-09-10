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
										<th class="border-primary border-darken-1">Adjusted Shipments</th>
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

							<div class="modal fade" id="adjusted_shipments" role="dialog" aria-labelledby="adjusted_shipments_title" aria-hidden="true">
								<div class="modal-dialog modal-sm" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h4 class="modal-title" id="adjusted_shipments_title">Adjusted Shipment(s)</h4>

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

												<input type="hidden" name="pending_payment_ids" class="pending_payment_ids">
												<input type="hidden" name="shipment_ids" class="shipment_ids">

												<div class="col-2">
													<div class="form-group">
														<label class="mx-auto">Total Amount</label>
														<input type="text" name="total_amount" class="form-control text-center total_amount" placeholder="Total Amount" readonly="readonly">
													</div>
												</div>

												<div class="col-2">
													<div class="form-group">
														<label class="mx-auto">Total Charges</label>
														<input type="text" name="total_charges" class="form-control text-center total_charges" placeholder="Total Charges" readonly="readonly">
													</div>
												</div>

												<div class="col-2">
													<div class="form-group">
														<label class="mx-auto">Total GST</label>
														<input type="text" name="total_gst" class="form-control text-center total_gst" placeholder="Total GST" readonly="readonly">
													</div>
												</div>

												<div class="w-100 mt-2"></div>

												<div class="col-2">
													<div class="form-group">
														<label class="mx-auto">Total Payable</label>
														<input type="text" name="total_payable" class="form-control text-center total_payable" placeholder="Total Payable" readonly="readonly">
													</div>
												</div>

												<div class="col-2">
													<div class="form-group">
														<label class="mx-auto">Total Hold</label>
														<input type="text" name="total_hold" class="form-control text-center total_hold" placeholder="Total Hold" readonly="readonly">
													</div>
												</div>

												<div class="w-100"></div>

												<button type="button" class="mr-auto btn btn-secondary" data-dismiss="modal">Close</button>
												<button type="submit" name="make" class="mr-1 btn btn-primary make">Make</button>
												<button type="button" name="export_bank_order" class="btn btn-info export_bank_order">Export Bank Order</button>
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
		.modal .modal-dialog.modal-lg.modal-full-length {
			max-width: 95%;
		}

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
	<script>
		$(document).ready(function() {
			var selected_rows = [];

			var selected_pending_payment_ids = [];

			var selected_rows_shipments = [];

			var initial_total_hold = 0;

			var table = $('#datatable').DataTable({
				scrollX: true,
				@if (session('role_id') == 1 || in_array(60, session('permissions')))
					dom: '<"d-inline-block"l><"pull-right"B>tipr',
					buttons: [{
						text: 'Make Payment(s)',
						className: 'btn btn-primary make_payment',
						enabled: false,
						action: function (e, dt, node, config) {
							$('#make_payments #make_payments_form .total_amount').val(0);
							$('#make_payments #make_payments_form .total_charges').val(0);
							$('#make_payments #make_payments_form .total_gst').val(0);
							$('#make_payments #make_payments_form .total_payable').val(0);
							$('#make_payments #make_payments_form .total_hold').val(0);

							$('#make_payments #make_payments_form button.make').prop('disabled', true);
							$('#make_payments #make_payments_form button.export_bank_order').prop('disabled', true);

							$('#make_payments #make_payments_form .pending_payment_ids').val('');
							$('#make_payments #make_payments_form .shipment_ids').val('');

							selected_pending_payment_ids = selected_rows;

							selected_rows_shipments = [];

							make_payments_table.clear().draw();

							$('#make_payments').modal('show');
						}
					}],
				@else
					dom: 'ltipr',
				@endif
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
				ajax: '{{ route('admin.finance.make_payments.list') }}',
				rowId: 'id',
				order: [[2, 'asc']],
				columns: [
					{data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data:'shipper', name: 'u.name', class: 'align-middle text-center shipper'},
					{data:'city', name: 'c.name', class: 'align-middle text-center city'},
					{data:'phone_numbers', name: 'phone_numbers', class: 'align-middle text-center phone_numbers'},
					{data:'address', name: 'u.address', class: 'align-middle text-center address'},
					{data:'total_shipments', name: 'pending_payments.total_shipments', class: 'align-middle text-center total_shipments'},
					{data:'delivered_shipments', name: 'pending_payments.delivered_shipments', class: 'align-middle text-center delivered_shipments'},
					{data:'returned_shipments', name: 'pending_payments.returned_shipments', class: 'align-middle text-center returned_shipments'},
					{data:'adjusted_shipments', name: 'pending_payments.adjusted_shipments', class: 'align-middle text-center adjusted_shipments'},
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
				paging: false,
				select: {
					info: false,
					style: 'multi',
					selector: 'td.select-checkbox',
					className: 'selected bg-primary bg-lighten-5 primary'
				},
				processing: true,
				serverSide: true,
				ajax: {
					url: '{{ route('admin.finance.make_payments.shipment_list') }}',
					data: function (d) {
						d.ids = selected_pending_payment_ids;
					}
				},
				rowId: 'id',
				order: [[2, 'asc']],
				columns: [
					{data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data:'shipper', name: 'u.name', class: 'align-middle shipper'},
					{data:'shipment', name: 's.tracking_number', class: 'align-middle shipment'},
					{data:'type', name: 'pending_payment_shipments.type', class: 'align-middle type'},
					{data:'amount', name: 'pending_payment_shipments.amount', class: 'align-middle amount'},
					{data:'charges', name: 'pending_payment_shipments.charges', class: 'align-middle charges'},
					{data:'gst', name: 'pending_payment_shipments.gst', class: 'align-middle gst'},
					{data:'payable', name: 'pending_payment_shipments.payable', class: 'align-middle payable'}
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
				},
				drawCallback: function() {
					initial_total_hold = this.api().column('.payable').data().reduce(function (a, b) {
						return parseFloat(a) + parseFloat(b);
					}, 0);

					$('#make_payments #make_payments_form .total_hold').val(parseFloat(initial_total_hold.toFixed(2)));
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
					table.button('.make_payment').enable();
				}
				else {
					table.button('.make_payment').disable();
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

			$('#datatable tbody').on('click', 'tr td.adjusted_shipments button', function() {
				var id = parseInt($(this).parents('tr').attr('id'));

				$('#adjusted_shipments .modal-body').html('');

				$.ajax({
					url: '{!! route('admin.finance.make_payments.adjusted_shipments') !!}',
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

						$('#adjusted_shipments .modal-body').html(tracking_numbers);

						$('#adjusted_shipments').modal('show');
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
						var details = '<table class="table table-sm table-bordered"><thead><tr role="row" class="bg-primary white"><th class="border-primary border-darken-1 align-middle text-center">Shipment</th><th class="border-primary border-darken-1 align-middle text-center">Type</th><th class="border-primary border-darken-1 align-middle text-center">Amount</th><th class="border-primary border-darken-1 align-middle text-center">Charges</th><th class="border-primary border-darken-1 align-middle text-center">GST</th><th class="border-primary border-darken-1 align-middle text-center">Payable</th></tr></thead><tbody>';

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
					$('#make_payments #make_payments_form .total_amount').val(0);
					$('#make_payments #make_payments_form .total_charges').val(0);
					$('#make_payments #make_payments_form .total_gst').val(0);
					$('#make_payments #make_payments_form .total_payable').val(0);
					$('#make_payments #make_payments_form .total_hold').val(0);

					$('#make_payments #make_payments_form button.make').prop('disabled', true);
					$('#make_payments #make_payments_form button.export_bank_order').prop('disabled', true);

					$('#make_payments #make_payments_form .pending_payment_ids').val('');
					$('#make_payments #make_payments_form .shipment_ids').val('');


					selected_pending_payment_ids = [];

					selected_rows_shipments = [];

					selected_pending_payment_ids.push(id);

					make_payments_table.clear().draw();

					$('#make_payments').modal('show');
				}
			});

			$('#make_payments #make_payments_datatable tbody').on('click', 'tr td.select-checkbox', function() {
				var parent = $(this).parent('tr');

				var id = parseInt(parent.attr('id'));

				var index = $.inArray(id, selected_rows_shipments);

				var total_amount_selector = $('#make_payments #make_payments_form .total_amount');
				var total_charges_selector = $('#make_payments #make_payments_form .total_charges');
				var total_gst_selector = $('#make_payments #make_payments_form .total_gst');
				var total_payable_selector = $('#make_payments #make_payments_form .total_payable');
				var total_hold_selector = $('#make_payments #make_payments_form .total_hold');

				if (index === -1) {
					selected_rows_shipments.push(id);

					var total_amount = ((total_amount_selector.val() != '') ? parseFloat(total_amount_selector.val()) : 0) + ((parent.children('td.amount').html() != '') ? parseFloat(parent.children('td.amount').html()) : 0);
					var total_charges = ((total_charges_selector.val() != '') ? parseFloat(total_charges_selector.val()) : 0) + ((parent.children('td.charges').html() != '') ? parseFloat(parent.children('td.charges').html()) : 0);
					var total_gst = ((total_gst_selector.val() != '') ? parseFloat(total_gst_selector.val()) : 0) + ((parent.children('td.gst').html() != '') ? parseFloat(parent.children('td.gst').html()) : 0);
					var total_payable = ((total_payable_selector.val() != '') ? parseFloat(total_payable_selector.val()) : 0) + ((parent.children('td.payable').html() != '') ? parseFloat(parent.children('td.payable').html()) : 0);
					var total_hold = ((total_hold_selector.val() != '') ? parseFloat(total_hold_selector.val()) : 0) - ((parent.children('td.payable').html() != '') ? parseFloat(parent.children('td.payable').html()) : 0);
				}
				else {
					selected_rows_shipments.splice(index, 1);

					var total_amount = ((total_amount_selector.val() != '') ? parseFloat(total_amount_selector.val()) : 0) - ((parent.children('td.amount').html() != '') ? parseFloat(parent.children('td.amount').html()) : 0);
					var total_charges = ((total_charges_selector.val() != '') ? parseFloat(total_charges_selector.val()) : 0) - ((parent.children('td.charges').html() != '') ? parseFloat(parent.children('td.charges').html()) : 0);
					var total_gst = ((total_gst_selector.val() != '') ? parseFloat(total_gst_selector.val()) : 0) - ((parent.children('td.gst').html() != '') ? parseFloat(parent.children('td.gst').html()) : 0);
					var total_payable = ((total_payable_selector.val() != '') ? parseFloat(total_payable_selector.val()) : 0) - ((parent.children('td.payable').html() != '') ? parseFloat(parent.children('td.payable').html()) : 0);
					var total_hold = ((total_hold_selector.val() != '') ? parseFloat(total_hold_selector.val()) : 0) + ((parent.children('td.payable').html() != '') ? parseFloat(parent.children('td.payable').html()) : 0);
				}

				if (selected_rows_shipments.length > 0) {
					total_amount_selector.val(parseFloat(total_amount.toFixed(2)));
					total_charges_selector.val(parseFloat(total_charges.toFixed(2)));
					total_gst_selector.val(parseFloat(total_gst.toFixed(2)));
					total_payable_selector.val(parseFloat(total_payable.toFixed(2)));
					total_hold_selector.val(parseFloat(total_hold.toFixed(2)));

					$('#make_payments #make_payments_form button.make').prop('disabled', false);
					$('#make_payments #make_payments_form button.export_bank_order').prop('disabled', false);
				}
				else {
					total_amount_selector.val(0);
					total_charges_selector.val(0);
					total_gst_selector.val(0);
					total_payable_selector.val(0);
					total_hold_selector.val(parseFloat(initial_total_hold.toFixed(2)));

					$('#make_payments #make_payments_form button.make').prop('disabled', true);
					$('#make_payments #make_payments_form button.export_bank_order').prop('disabled', true);
				}

				$('#make_payments #make_payments_form .pending_payment_ids').val(selected_pending_payment_ids);
				$('#make_payments #make_payments_form .shipment_ids').val(selected_rows_shipments);
			});

			$('#make_payments #make_payments_form .export_bank_order').bind('click', function(e) {
				e.preventDefault();

				window.open('{!! route('admin.finance.make_payments.export_bank_order') !!}?pending_payment_ids=' + selected_pending_payment_ids + '&shipment_ids=' + selected_rows_shipments, '_blank');
			});
		});
	</script>
@endsection