@extends('admin.layout.master')

@section('title', 'Outstanding SDN')

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
										<th class="border-primary border-darken-1">Deposited by</th>
										<th class="border-primary border-darken-1">Company Bank</th>
										<th class="border-primary border-darken-1">Deposited Datetime</th>
										<th class="border-primary border-darken-1">Deposit Slip</th>
										<th class="border-primary border-darken-1"></th>
									</tr>
								</thead>
							</table>

							<div class="modal fade" id="reconcile_delivery_notes" role="dialog" aria-labelledby="reconcile_delivery_notes_title" aria-hidden="true">
								<div class="modal-dialog modal-lg modal-full-length" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h4 class="modal-title" id="reconcile_delivery_notes_title">Reconcile Delivery Notes of SDN</h4>

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
														<th class="border-primary border-darken-1">Assigned Datetime</th>
														<th class="border-primary border-darken-1">Updated by</th>
														<th class="border-primary border-darken-1">Updated Datetime</th>
														<th class="border-primary border-darken-1">DNCC Amount</th>
													</tr>
												</thead>
											</table>

											<form id="reconcile_delivery_notes_form" class="form-inline mt-1 mb-1 justify-content-center" novalidate="novalidate" method="POST" action="{{ route('admin.finance.outstanding_sdn.reconcile_delivery_notes') }}">
												{{ csrf_field() }}

												<input type="hidden" name="station_deposit_note_id" class="station_deposit_note_id">
												<input type="hidden" name="delivery_note_ids" class="delivery_note_ids">

												<button type="button" class="mr-auto btn btn-secondary" data-dismiss="modal">Close</button>
												<button type="submit" name="reconcile" class="btn btn-primary reconcile">Reconcile</button>
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
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
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
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.finance.outstanding_sdn.list') }}',
                        data: {
                            'page': 'all',
                        },
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('SDN No.');
                            head.push('Hub');
                            head.push('DNCCs');
                            head.push('Delivered Shipments');
                            head.push('DNCC Amount');
                            head.push('Deposited By');
                            head.push('Company Bank');
                            head.push('Deposited Datetime');




                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.delivery_note_number);
                                row.push(values.hub);
                                row.push(values.dncc_count);
                                row.push(values.sdn_delivered_shipments);
                                row.push(values.sdn_amount);
                                row.push(values.deposited_by);
                                row.push(values.bank);
                                row.push(values.deposited_at);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
			var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Outstanding SDN',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }],
				scrollX: true, scrollY: '350px',
				lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
				pageLength: 50,
				pagingType: 'full_numbers',
				processing: true,
				serverSide: true,
				ajax: '{{ route('admin.finance.outstanding_sdn.list') }}',
				rowId: 'id',
				order: [[8, 'desc']],
				columns: [
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data:'sdn_number', name: 'station_deposit_notes.id', class: 'align-middle text-center sdn_number'},
					{data:'hub', name: 'h.name', class: 'align-middle hub'},
					{data:'dncc_count', name: 'station_deposit_notes.dncc_count', class: 'align-middle dnccs'},
					{data:'sdn_delivered_shipments', name: 'sdn_delivered_shipments', class: 'align-middle delivered_shipments'},
					{data:'sdn_amount', name: 'station_deposit_notes.sdn_amount', class: 'align-middle amount'},
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

					this.api().table().columns.adjust();
				}
			});

			var station_deposit_note_id = null;

			var selected_rows = [];

			var reconcile_delivery_notes_table = $('#reconcile_delivery_notes #reconcile_delivery_notes_datatable').DataTable({
				dom: 'tr',
				scrollX: true, scrollY: '350px',
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
					{data:'dncc_amount', name: 'dn.received_cod_amount', class: 'align-middle dncc_amount'}
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

					this.api().table().columns.adjust();
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

				if (index === -1) {
					selected_rows.push(id);
				}
				else {
					selected_rows.splice(index, 1);
				}

				$('#reconcile_delivery_notes #reconcile_delivery_notes_form .delivery_note_ids').val(selected_rows);
			});
		});
	</script>
@endsection