@extends('admin.layout.master')

@section('title', 'Invoices History')

@section('content')
	<h1 class="mb-1">
		Invoices History
	</h1>

	<div class="card">
		<div class="card-content" aria-expanded="true">
			<div class="card-body">
				@include('admin.inc.messages')

				<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
					<thead>
						<tr role="row" class="bg-primary white">
							<th class="border-primary border-darken-1">S. No.</th>
							<th class="border-primary border-darken-1">Invoice ID</th>
							<th class="border-primary border-darken-1">Invoice No.</th>
							<th class="border-primary border-darken-1">Shipper</th>
							<th class="border-primary border-darken-1">Total Shipment(s)</th>
							<th class="border-primary border-darken-1">Delivered Shipment(s)</th>
							<th class="border-primary border-darken-1">Returned Shipment(s)</th>
							<th class="border-primary border-darken-1">Adjusted Shipment(s)</th>
							<th class="border-primary border-darken-1">Total Charges</th>
							<th class="border-primary border-darken-1">Total GST</th>
							<th class="border-primary border-darken-1">Total Invoice Amount</th>
							<th class="border-primary border-darken-1">Billing Period (From)</th>
							<th class="border-primary border-darken-1">Billing Period (To)</th>
							<th class="border-primary border-darken-1">Due Date</th>
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
			</div>
		</div>
	</div>
@endsection

@section('css')
@endsection

@section('js')
	<script>
		$(document).ready(function() {
			var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [{
                    extend: 'excel',
                    title: 'Invoices History',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                }],
				scrollX: true, scrollY: '350px',
				lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
				pageLength: 50,
				pagingType: 'full_numbers',
				processing: true,
				serverSide: true,
				ajax: '{{ route('admin.finance.invoices_history.list') }}',
				rowId: 'id',
				order: [[1, 'asc']],
				columns: [
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data:'id_padded', name: 'invoices.id', class: 'align-middle text-center id'},
					{data:'invoice_number', name: 'invoices.invoice_number', class: 'align-middle text-center invoice_number'},
					{data:'shipper', name: 'u.name', class: 'align-middle text-center shipper'},
					{data:'total_shipments', name: 'invoices.total_shipments', class: 'align-middle text-center total_shipments'},
					{data:'total_delivered_shipments', name: 'invoices.total_delivered_shipments', class: 'align-middle text-center total_delivered_shipments'},
					{data:'total_returned_shipments', name: 'invoices.total_returned_shipments', class: 'align-middle text-center total_returned_shipments'},
					{data:'total_adjusted_shipments', name: 'invoices.total_adjusted_shipments', class: 'align-middle text-center total_adjusted_shipments'},
					{data:'total_charges', name: 'invoices.total_charges', class: 'align-middle text-center total_charges'},
					{data:'total_gst', name: 'invoices.total_gst', class: 'align-middle text-center total_gst'},
					{data:'total_invoice_amount', name: 'invoices.total_invoice_amount', class: 'align-middle text-center total_invoice_amount'},
					{data:'billing_period_from_date', name: 'invoices.billing_period_from_date', class: 'align-middle text-center billing_period_from_date'},
					{data:'billing_period_to_date', name: 'invoices.billing_period_to_date', class: 'align-middle text-center billing_period_to_date'},
					{data:'due_date', name: 'invoices.due_date', class: 'align-middle text-center due_date'}
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
                    var bank_select = '<select name="bank_select" id="bank_select" class="select2 form-control"></select>';
					this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.serial_number')) {
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

			$('#datatable tbody').on('click', 'tr td.id button', function() {
				var id = parseInt($(this).parents('tr').attr('id'));

				window.open('{!! route('admin.finance.generate_invoices.print') !!}?id=' + id, '_blank');
			});

			$('#datatable tbody').on('click', 'tr td.total_delivered_shipments button', function() {
				var id = parseInt($(this).parents('tr').attr('id'));

				$('#delivered_shipments .modal-body').html('');

				$.ajax({
					url: '{!! route('admin.finance.invoices_history.delivered_shipments') !!}',
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

			$('#datatable tbody').on('click', 'tr td.total_returned_shipments button', function() {
				var id = parseInt($(this).parents('tr').attr('id'));

				$('#returned_shipments .modal-body').html('');

				$.ajax({
					url: '{!! route('admin.finance.invoices_history.returned_shipments') !!}',
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

			$('#datatable tbody').on('click', 'tr td.total_adjusted_shipments button', function() {
				var id = parseInt($(this).parents('tr').attr('id'));

				$('#adjusted_shipments .modal-body').html('');

				$.ajax({
					url: '{!! route('admin.finance.invoices_history.adjusted_shipments') !!}',
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
		});
	</script>
@endsection