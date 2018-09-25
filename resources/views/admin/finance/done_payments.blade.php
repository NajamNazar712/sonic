@extends('admin.layout.master')

@section('title', 'Done Payments')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Done Payments
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
										<th class="border-primary border-darken-1">Payment ID</th>
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
										<th class="border-primary border-darken-1">Total Deductable</th>
										<th class="border-primary border-darken-1">Total Payable</th>
										<th class="border-primary border-darken-1">Bank</th>
										<th class="border-primary border-darken-1">Return Shipments Avg. Aging</th>
										<th class="border-primary border-darken-1">Reference No.</th>
										<th class="border-primary border-darken-1">Done Datetime</th>
										<th class="border-primary border-darken-1">Company Bank</th>
										<th class="border-primary border-darken-1">Status</th>
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

							<div class="modal fade" id="update_details" role="dialog" aria-labelledby="update_details_title" aria-hidden="true">
								<div class="modal-dialog modal-sm" role="document">
									<div class="modal-content">
										<form class="form-horizontal" novalidate="novalidate">
											<input type="hidden" name="id" class="id">

											<div class="modal-header">
												<h4 class="modal-title" id="update_details_title">Update Details<span></span></h4>

												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">×</span>
												</button>
											</div>
											<div class="modal-body">
												<div class="form-group">
													<input type="text" name="reference_number" class="form-control reference_number" placeholder="Reference Number*" data-rule-required="true" data-msg-required="Reference Number is required">
												</div>

												<div class="form-group">
													<select name="company_bank" class="select2 company_bank" data-rule-required="true" data-msg-required="Company Bank is required">
														@foreach($banks as $bank)
															<option value="{{ $bank->id }}">{{ $bank->name }}</option>
														@endforeach
													</select>
												</div>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
												<button type="submit" class="btn btn-primary ml-auto">Update</button>
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
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			$('#update_details .company_bank').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Company Bank*'
			}).bind('change', function() {
				if ($(this).hasClass('danger')) {
					$(this).valid();
				}
			});

			var selected_rows = [];
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.finance.done_payments.list') }}',
                        data: {
                            'page': 'all',
                        },
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Payment ID');
                            head.push('Shipper');
                            head.push('City');
                            head.push('Phone No(s).');
                            head.push('Address');
                            head.push('Total Shipments');
                            head.push('Delivered Shipments');
                            head.push('Returned Shipments');
                            head.push('Adjusted Shipments');
                            head.push('Total Amount');
                            head.push('Total Charges');
                            head.push('Total GST');
                            head.push('Total Deductable');
                            head.push('Total Payable');
                            head.push('Bank');
                            head.push('Return Shipments Avg. Aging');
                            head.push('Reference No.');
                            head.push('Done Datetime');
                            head.push('Company Bank');
                            head.push('Status');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.id);
                                row.push(values.shipper);
                                row.push(values.city);
                                row.push(values.phone_numbers);
                                row.push(values.address);
                                row.push(values.total_shipments);
                                row.push(values.delivered_shipments_count);
                                row.push(values.returned_shipments_count);
                                row.push(values.adjusted_shipments_count);
                                row.push(values.total_amount);
                                row.push(values.total_charges);
                                row.push(values.total_gst);
                                row.push(values.total_deductable);
                                row.push(values.total_payable);
                                row.push(values.bank);
                                row.push(values.return_shipments_average_aging);
                                row.push(values.reference_number);
                                row.push(values.done_at);
                                row.push(values.company_bank);
                                row.push(values.status);


                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
			var table = $('#datatable').DataTable({
				scrollX: true, scrollY: '350px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
				@if (session('role_id') == 1 || count(array_intersect([62, 63], session('permissions'))) !== 0)

					buttons: [
						@if (session('role_id') == 1 || in_array(62, session('permissions')))
							{
							text: 'Paid',
							className: 'btn btn-primary paid',
							enabled: false,
							action: function (e, dt, node, config) {
								$.ajax({
									url: '{!! route('admin.finance.done_payments.paid') !!}',
									method: 'PUT',
									data: {
										'_token': '{{ csrf_token() }}',
										'ids': selected_rows
									}
								})
								.done(function(data) {
									if (data.status == 0) {
										toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
									}
									else {
										toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
									}

									$.each(selected_rows, function(index, id) {
										table.row($('#datatable tbody tr#' + id)).deselect();
									});

									selected_rows = [];

									table.button('.paid').disable();
									table.button('.reverted').disable();

									table.draw('false');
								});
							}
						},
					@endif

					@if (session('role_id') == 1 || in_array(63, session('permissions')))
						{
							text: 'Reverted',
							className: 'btn btn-primary reverted',
							enabled: false,
							action: function (e, dt, node, config) {
								$.ajax({
									url: '{!! route('admin.finance.done_payments.reverted') !!}',
									method: 'PUT',
									data: {
										'_token': '{{ csrf_token() }}',
										'ids': selected_rows
									}
								})
								.done(function(data) {
									if (data.status == 0) {
										toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
									}
									else {
										toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
									}

									$.each(selected_rows, function(index, id) {
										table.row($('#datatable tbody tr#' + id)).deselect();
									});

									selected_rows = [];

									table.button('.paid').disable();
									table.button('.reverted').disable();

									table.draw('false');
								});
							}
						},
					@endif
                    {
                        extend: 'excel',
                        title: 'Done Payments',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }],
				@else
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Done Payments',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }],
				@endif
				scrollX: true, scrollY: '350px',
				select: {
					info: false,
					style: 'multi',
					selector: 'td.select-checkbox',
					className: 'selected bg-primary bg-lighten-5 primary'
				},
				lengthMenu: [[25, 50, 100], [25, 50, 100]],
				pageLength: 25,
				pagingType: 'full_numbers',
				processing: true,
				serverSide: true,
				ajax: '{{ route('admin.finance.done_payments.list') }}',
				rowId: 'id',
				order: [[2, 'desc']],
				columns: [
					{data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data:'id', name: 'done_payments.id', class: 'align-middle text-center id'},
					{data:'shipper', name: 'u.name', class: 'align-middle text-center shipper'},
					{data:'city', name: 'c.name', class: 'align-middle text-center city'},
					{data:'phone_numbers', name: 'phone_numbers', class: 'align-middle text-center phone_numbers'},
					{data:'address', name: 'u.address', class: 'align-middle text-center address'},
					{data:'total_shipments', name: 'done_payments.total_shipments', class: 'align-middle text-center total_shipments'},
					{data:'delivered_shipments', name: 'done_payments.delivered_shipments', class: 'align-middle text-center delivered_shipments'},
					{data:'returned_shipments', name: 'done_payments.returned_shipments', class: 'align-middle text-center returned_shipments'},
					{data:'adjusted_shipments', name: 'done_payments.adjusted_shipments', class: 'align-middle text-center adjusted_shipments'},
					{data:'total_amount', name: 'total_amount', class: 'align-middle text-center total_amount', orderable: false},
					{data:'total_charges', name: 'total_charges', class: 'align-middle text-center total_charges', orderable: false},
					{data:'total_gst', name: 'total_gst', class: 'align-middle text-center total_gst', orderable: false},
					{data:'total_deductable', name: 'total_deductable', class: 'align-middle text-center total_deductable', orderable: false},
					{data:'total_payable', name: 'total_payable', class: 'align-middle text-center total_payable', orderable: false},
					{data:'bank', name: 'ub.name', class: 'align-middle text-center bank'},
					{data:'return_shipments_average_aging', name: 'return_shipments_average_aging', class: 'align-middle text-center return_shipments_average_aging', orderable: false},
					{data:'reference_number', name: 'done_payments.reference_number', class: 'align-middle text-center reference_number'},
					{data:'done_at', name: 'done_payments.created_at', class: 'align-middle text-center done_at'},
					{data:'company_bank', name: 'b.name', class: 'align-middle text-center company_bank'},
					{data:'status', name: 'status', class: 'align-middle text-center status'},
					{data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
				],
				rowCallback: function(row, data, index) {
					var info = table.page.info();

					$('td:eq(1)', row).html(index + 1 + info.page * info.length);

					if (data.status != 'Paid') {
						$('td:eq(0)', row).addClass('select-checkbox');
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

						if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.total_amount') || $(header).is('.total_charges') || $(header).is('.total_gst') || $(header).is('.total_deductable') || $(header).is('.total_payable') || $(header).is('.return_shipments_average_aging') || $(header).is('.action')) {
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
					table.button('.paid').enable();
					table.button('.reverted').enable();
				}
				else {
					table.button('.paid').disable();
					table.button('.reverted').disable();
				}
			});

			$('#datatable tbody').on('click', 'tr td.delivered_shipments button', function() {
				var id = parseInt($(this).parents('tr').attr('id'));

				$('#delivered_shipments .modal-body').html('');

				$.ajax({
					url: '{!! route('admin.finance.done_payments.delivered_shipments') !!}',
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
					url: '{!! route('admin.finance.done_payments.returned_shipments') !!}',
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
					url: '{!! route('admin.finance.done_payments.adjusted_shipments') !!}',
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
					$.ajax({
						url: '{!! route('admin.finance.done_payments.details_print') !!}',
						method: 'POST',
						data: {
							'_token': '{{ csrf_token() }}',
							'id': id
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
				else if ($(this).hasClass('update_details')) {
					$.ajax({
						url: '{!! route('admin.finance.done_payments.details') !!}',
						method: 'POST',
						data: {
							'_token': '{{ csrf_token() }}',
							'id': id
						}
					})
					.done(function(data) {
						if (data) {
							$('#update_details form .id').val(id);

							$('#update_details form .reference_number').val(data.reference_number);

							$('#update_details form .company_bank').val(data.company_bank_id).trigger('change');

							$('#update_details').modal('show');
						}
					});
				}
				else if ($(this).hasClass('export_to_excel')) {
					window.open('{!! route('admin.finance.done_payments.export_to_excel') !!}?id=' + id, '_blank');
				}
			});

			$('#update_details').on('show.bs.modal', function (e) {
				$(this).find('input').removeClass('danger');
				$(this).find('select').removeClass('danger');

				$(this).find('label').remove();
			});

			$('#update_details form').validate({
				errorClass: 'danger',
				successClass: 'success',
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parent('.form-group'));
				},
				submitHandler: function(form) {
					var id = parseInt($(form).find('input.id').val());
					var reference_number = $(form).find('input.reference_number').val();
					var company_bank_id = parseInt($(form).find('select.company_bank').val());

					$.ajax({
						url: '{!! route('admin.finance.done_payments.update_details') !!}',
						method: 'PUT',
						data: {
							'_token': '{{ csrf_token() }}',
							'id': id,
							'reference_number': reference_number,
							'company_bank_id': company_bank_id
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

						$('#update_details').modal('hide');
					});
				}
			});
		});
	</script>
@endsection