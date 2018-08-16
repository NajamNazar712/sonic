@extends('admin.layout.master')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Outstanding SDN
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">SDN No.</th>
										<th class="border-primary border-darken-1">Hub</th>
										<th class="border-primary border-darken-1">DNCCs</th>
										<th class="border-primary border-darken-1">Delivered Shipments</th>
										<th class="border-primary border-darken-1">DNCC Amount</th>
										<th class="border-primary border-darken-1">Expense</th>
										<th class="border-primary border-darken-1">Net Amount</th>
										<th class="border-primary border-darken-1">Deposited by</th>
										<th class="border-primary border-darken-1">Company Bank</th>
										<th class="border-primary border-darken-1">Deposited at</th>
										<th class="border-primary border-darken-1">Deposit Slip</th>
										<th class="border-primary border-darken-1"></th>
									</tr>
								</thead>
							</table>

							<div class="modal fade" id="reconcile_delivery_notes" role="dialog" aria-labelledby="reconcile_delivery_notes_title" aria-hidden="true">
								<div class="modal-dialog modal-lg modal-full-length" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h4 class="modal-title" id="reconcile_delivery_notes_title">Reconcile Delivery Notes of SDN No. <span></span></h4>

											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">×</span>
											</button>
										</div>
										<div class="modal-body">
											<table class="table table-bordered datatable" id="reconcile_delivery_notes_datatable" style="z-index: 3;">
												<thead>
													<tr role="row" class="bg-primary white">
														<th class="border-primary border-darken-1"></th>
														<th class="border-primary border-darken-1">S. No.</th>
														<th class="border-primary border-darken-1">Delivery Note No.</th>
														<th class="border-primary border-darken-1">Hub</th>
														<th class="border-primary border-darken-1">Rider</th>
														<th class="border-primary border-darken-1">Shipments</th>
														<th class="border-primary border-darken-1">Shipments Delivered</th>
														<th class="border-primary border-darken-1">Assigned by</th>
														<th class="border-primary border-darken-1">Assigned at</th>
														<th class="border-primary border-darken-1">Updated by</th>
														<th class="border-primary border-darken-1">Updated at</th>
														<th class="border-primary border-darken-1">DNCC Amount</th>
														<th class="border-primary border-darken-1">Expense</th>
														<th class="border-primary border-darken-1"></th>
													</tr>
												</thead>
											</table>

											<form id="reconcile_delivery_notes_form" class="form-inline mt-1 mb-1 justify-content-center" novalidate="novalidate" method="POST" action="{{ route('admin.finance.outstanding_sdn.reconcile_delivery_notes') }}">
												{{ csrf_field() }}

												<input type="hidden" name="station_deposit_note_id" class="station_deposit_note_id">
												<input type="hidden" name="delivery_note_ids" class="delivery_note_ids">

												<div class="form-group">
													<input type="text" name="total_dncc_amount" class="form-control total_dncc_amount" placeholder="Total DNCC Amount" readonly="readonly">
												</div>

												<div class="form-group ml-1">
													<input type="text" name="total_expense" class="form-control total_expense" placeholder="Total Expense" readonly="readonly">
												</div>

												<div class="form-group ml-1">
													<input type="text" name="total_net_amount" class="form-control total_net_amount" placeholder="Total Net Amount" readonly="readonly">
												</div>

												<div class="w-100"></div>

												<button type="button" class="mr-auto btn btn-secondary" data-dismiss="modal">Close</button>
												<button type="submit" name="reconcile" class="btn btn-primary reconcile">Reconcile</button>
											</form>
										</div>
									</div>
								</div>
							</div>

							<div class="modal fade" id="edit_expense" role="dialog" aria-labelledby="edit_expense_title" aria-hidden="true">
								<div class="modal-dialog modal-sm" role="document">
									<div class="modal-content">
										<form class="form-horizontal" novalidate="novalidate">
											<input type="hidden" name="id" class="id">

											<div class="modal-header">
												<h4 class="modal-title" id="edit_expense_title">Edit Expense of DNCC #<span></span></h4>

												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">×</span>
												</button>
											</div>
											<div class="modal-body">
												<div class="form-group m-0">
													<input type="text" name="expense" class="form-control expense" placeholder="Expense">
												</div>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
												<button type="submit" class="btn btn-primary ml-auto">Edit</button>
											</div>
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
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

	<style>
		.modal .modal-dialog.modal-lg.modal-full-length {
			max-width: 95%;
		}

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
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			function print(id) {
				$.ajax({
					url: '{!! route('admin.delivery.sdn.print') !!}',
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
				fixedHeader: {
					header: true,
					headerOffset: $('.header-navbar').height()
				},
				lengthMenu: [[1, 25, 50, 100], [1, 25, 50, 100]],
				pageLength: 25,
				stateSave: true,
				pagingType: 'full_numbers',
				processing: true,
				serverSide: true,
				ajax: '{{ route('admin.finance.outstanding_sdn.list') }}',
				rowId: 'id',
				order: [[1, 'asc']],
				columns: [
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data:'sdn_number', name: 'station_deposit_notes.id', class: 'align-middle text-center sdn_number'},
					{data:'hub', name: 'h.name', class: 'align-middle hub'},
					{data:'dncc_count', name: 'station_deposit_notes.dncc_count', class: 'align-middle dnccs'},
					{data:'sdn_delivered_shipments', name: 'sdn_delivered_shipments', class: 'align-middle delivered_shipments'},
					{data:'sdn_amount', name: 'station_deposit_notes.sdn_amount', class: 'align-middle amount'},
					{data:'sdn_expense', name: 'station_deposit_notes.sdn_expense', class: 'align-middle expense'},
					{data:'sdn_net_amount', name: 'station_deposit_notes.sdn_net_amount', class: 'align-middle net_amount'},
					{data:'deposited_by', name: 'a.name', class: 'align-middle deposited_by'},
					{data:'bank', name: 'b.name', class: 'align-middle bank'},
					{data:'deposited_at', name: 'station_deposit_notes.created_at', class: 'align-middle deposited_at'},
					{data:'deposit_slip', name: 'deposit_slip', class: 'align-middle deposit_slip'},
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

						if ($(header).is('.serial_number') || $(header).is('.deposit_slip') || $(header).is('.action')) {
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

			var station_deposit_note_id = null;

			var selected_rows = [];

			var reconcile_delivery_notes_table = $('#reconcile_delivery_notes #reconcile_delivery_notes_datatable').DataTable({
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
					url: '{{ route('admin.finance.outstanding_sdn.delivery_notes_list') }}',
					data: function (d) {
						d.id = station_deposit_note_id;
					}
				},
				rowId: 'id',
				order: [[2, 'asc']],
				columns: [
					{data: 'dn.id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data:'delivery_note_number', name: 'dn.id', class: 'align-middle text-center delivery_note_number'},
					{data:'hub', name: 'h.name', class: 'align-middle hub'},
					{data:'rider', name: 'ri.name', class: 'align-middle rider'},
					{data:'shipments', name: 'dn.shipments_count', class: 'align-middle shipments'},
					{data:'delivered_shipments', name: 'dn.delivered_shipments', class: 'align-middle delivered_shipments'},
					{data:'assigned_by', name: 'a.name', class: 'align-middle assigned_by'},
					{data:'assigned_at', name: 'dn.created_at', class: 'align-middle assigned_at'},
					{data:'updated_by', name: 'a.name', class: 'align-middle updated_by'},
					{data:'updated_at', name: 'dn.updated_at', class: 'align-middle updated_at'},
					{data:'dncc_amount', name: 'dn.received_cod_amount', class: 'align-middle dncc_amount'},
					{data:'expense', name: 'dn.expense', class: 'align-middle expense'},
					{data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
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
				}
			});

			$('#datatable tbody').on('click', 'tr td.sdn_number button', function() {
				print(parseInt($(this).parents('tr').attr('id')));
			});

			$('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
				var id = parseInt($(this).parents('tr').attr('id'));

				if ($(this).hasClass('reconcile_delivery_notes')) {
					$('#reconcile_delivery_notes #reconcile_delivery_notes_form .total_dncc_amount').val('');
					$('#reconcile_delivery_notes #reconcile_delivery_notes_form .total_expense').val('');
					$('#reconcile_delivery_notes #reconcile_delivery_notes_form .total_net_amount').val('');

					$('#reconcile_delivery_notes #reconcile_delivery_notes_form .station_deposit_note_id').val(id);

					station_deposit_note_id = id;

					selected_rows = [];

					reconcile_delivery_notes_table.clear().draw();

					$('#reconcile_delivery_notes #reconcile_delivery_notes_title span').html(id);

					$('#reconcile_delivery_notes').modal('show');
				}
				else if ($(this).hasClass('export_to_excel')) {
					window.open('{!! route('admin.finance.outstanding_sdn.export_to_excel') !!}?id=' + id, '_blank');
				}
			});

			$('#reconcile_delivery_notes #reconcile_delivery_notes_datatable tbody').on('click', 'tr td.select-checkbox', function() {
				var parent = $(this).parent('tr');

				var id = parseInt(parent.attr('id'));

				var index = $.inArray(id, selected_rows);

				total_dncc_amount_selector = $('#reconcile_delivery_notes #reconcile_delivery_notes_form .total_dncc_amount');
				total_expense_selector = $('#reconcile_delivery_notes #reconcile_delivery_notes_form .total_expense');
				total_net_amount_selector = $('#reconcile_delivery_notes #reconcile_delivery_notes_form .total_net_amount');

				if (index === -1) {
					selected_rows.push(id);

					var total_dncc_amount = ((total_dncc_amount_selector.val() != '') ? parseInt(total_dncc_amount_selector.val()) : 0) + ((parent.children('td.dncc_amount').html() != '') ? parseInt(parent.children('td.dncc_amount').html()) : 0);
					var total_expense = ((total_expense_selector.val() != '') ? parseInt(total_expense_selector.val()) : 0) + ((parent.children('td.expense').html() != '') ? parseInt(parent.children('td.expense').html()) : 0);
				}
				else {
					selected_rows.splice(index, 1);

					var total_dncc_amount = ((total_dncc_amount_selector.val() != '') ? parseInt(total_dncc_amount_selector.val()) : 0) - ((parent.children('td.dncc_amount').html() != '') ? parseInt(parent.children('td.dncc_amount').html()) : 0);
					var total_expense = ((total_expense_selector.val() != '') ? parseInt(total_expense_selector.val()) : 0) - ((parent.children('td.expense').html() != '') ? parseInt(parent.children('td.expense').html()) : 0);
				}

				var total_net_amount = total_dncc_amount - total_expense;

				if (selected_rows.length > 0) {
					total_dncc_amount_selector.val(total_dncc_amount);
					total_expense_selector.val(total_expense);
					total_net_amount_selector.val(total_net_amount);
				}
				else {
					total_dncc_amount_selector.val('');
					total_expense_selector.val('');
					total_net_amount_selector.val('');
				}

				$('#reconcile_delivery_notes #reconcile_delivery_notes_form .delivery_note_ids').val(selected_rows);
			});

			$('#edit_expense form input.expense').inputmask({
				'alias': 'integer',
				'allowMinus': false,
				'allowPlus': false
			});

			$('#edit_expense form').validate({
				errorClass: 'danger',
				successClass: 'success',
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parent('.form-group'));
				},
				submitHandler: function(form) {
					var id = $(form).find('input.id').val();
					var expense = $(form).find('input.expense').val();

					$.ajax({
						url: '{!! route('admin.finance.outstanding_sdn.delivery_note_expense_edit') !!}',
						method: 'PUT',
						data: {
							'id': id,
							'expense': expense,
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

						$('#reconcile_delivery_notes #reconcile_delivery_notes_form .total_dncc_amount').val('');
						$('#reconcile_delivery_notes #reconcile_delivery_notes_form .total_expense').val('');
						$('#reconcile_delivery_notes #reconcile_delivery_notes_form .total_net_amount').val('');

						selected_rows = [];

						reconcile_delivery_notes_table.clear().draw();

						$('#edit_expense').modal('hide');
					});

					return false;
				}
			});

			$('#reconcile_delivery_notes #reconcile_delivery_notes_datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.edit_expense', function() {
				var parent = $(this).parents('tr');
				var id = parseInt(parent.attr('id'));
				var value = parent.children('td.expense').html();

				$('#edit_expense form #edit_expense_title span').html(id);

				$('#edit_expense form input.id').val(id);
				$('#edit_expense form input.expense').val(value);

				$('#edit_expense').modal('show');
			});
		});
	</script>
@endsection