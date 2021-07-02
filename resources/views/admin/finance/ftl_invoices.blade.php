@extends('admin.layout.master')

@section('title', 'FTL Invoices')

@section('content')
	<h1 class="mb-1">
		FTL Invoices
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
							<th class="border-primary border-darken-1">Invoice Number</th>
							<th class="border-primary border-darken-1">Req ID</th>
							<th class="border-primary border-darken-1">Origin</th>
							<th class="border-primary border-darken-1">Destination</th>
							<th class="border-primary border-darken-1">Tracking Number</th>
							<th class="border-primary border-darken-1">Weight</th>
							<th class="border-primary border-darken-1">Required Vehicle</th>
							<th class="border-primary border-darken-1">Quantity</th>
							<th class="border-primary border-darken-1">Request Date</th>
							<th class="border-primary border-darken-1">Vendor</th>
							<th class="border-primary border-darken-1">Total Cost</th>
							<th class="border-primary border-darken-1">Charges</th>
							<th class="border-primary border-darken-1">GST</th>
							<th class="border-primary border-darken-1">Charges Collection</th>
							<th class="border-primary border-darken-1">Status</th>
							<th class="border-primary border-darken-1">Receiving Date</th>
							<th class="border-primary border-darken-1">Company Bank</th>
							<th class="border-primary border-darken-1">Received Amount</th>
							<th class="border-primary border-darken-1">Tax Amount</th>
							<th class="border-primary border-darken-1">Deposit Date</th>
							<th class="border-primary border-darken-1"></th>
						</tr>
					</thead>
				</table>

				<div class="modal fade" id="mark_as_received" role="dialog" aria-labelledby="mark_as_received_title" aria-hidden="true">
					<div class="modal-dialog modal-sm" role="document">
						<div class="modal-content">
							<form class="form-horizontal" method="POST" action="{{ route('admin.finance.ftl_invoice.received') }}" novalidate="novalidate">
								{{ csrf_field() }}

								<input type="hidden" name="id" class="id">
								<input type="hidden" name="ids" class="ids">

								<div class="modal-header">
									<h4 class="modal-title" id="mark_as_received_title">Mark as Received<span></span></h4>

									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">×</span>
									</button>
								</div>
								<div class="modal-body">
									<div class="form-group input-group">
										<div class="input-group-prepend">
											<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
												<span class="la la-calendar-o"></span>
											</span>
										</div>

										<input type="text" name="receiving_date" class="form-control pickadate bg-primary border-primary white rounded-right receiving_date" id="receiving_date" placeholder="Rreceiving Date*" data-rule-required="true" data-msg-required="Rreceiving Date is required">
									</div>

                                    <div class="form-group">
										<select name="company_bank" class="select2 company_bank" data-rule-required="true" data-msg-required="Company Bank is required">
											@foreach($company_banks as $bank)
												<option value="{{ $bank->id }}">{{ $bank->name }}</option>
											@endforeach
										</select>
									</div>


									<div class="form-group">
										<input type="text" name="received_amount" class="form-control received_amount" placeholder="Received Amount*" data-rule-required="true" data-msg-required="Received Amount is required" data-rule-number="true" data-msg-number="Received Amount should to be a valid number">
									</div>

									<div class="form-group">
										<input type="text" name="tax_amount" class="form-control tax_amount" placeholder="Tax Amount*" data-rule-required="true" data-msg-required="Tax Amount is required" data-rule-number="true" data-msg-number="Tax Amount should to be a valid number">
									</div>

									<div class="form-group input-group">
										<div class="input-group-prepend">
											<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
												<span class="la la-calendar-o"></span>
											</span>
										</div>

										<input type="text" name="deposit_date" class="form-control pickadate bg-primary border-primary white rounded-right deposit_date" id="deposit_date" placeholder="Deposit Date*" data-rule-required="true" data-msg-required="Deposit Date is required">
									</div>

								</div>
								<div class="modal-footer">
									<button type="submit" class="btn btn-primary ml-auto">Update</button>
								</div>
							</form>
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
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
	<script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			
			$('#mark_as_received form select.company_bank').prepend('<option value="" selected></option>').select2({
                placeholder: 'Select Company Bank',
                width:'100%'
            }).bind('change', function() {
				if ($(this).hasClass('danger')) {
					$(this).valid();
				}
			});

			$('#mark_as_received form input.received_amount').inputmask({
				'alias': 'decimal',
				'allowMinus': false,
				'allowPlus': false,
				'digits': 2
			});

			$('#mark_as_received form input.tax_amount').inputmask({
				'alias': 'decimal',
				'allowMinus': false,
				'allowPlus': false,
				'digits': 2
			});
            
            $('#mark_as_received form input.receiving_date').pickadate({
				firstDay: 1,
				max: new Date(),
				clear: '',
				selectYears: true,
				selectMonths: true,
				formatSubmit: 'yyyy-mm-dd 00:00:00',
				hiddenSuffix: '_formatted',
				onSet: function(context) {
					$('#mark_as_received input.receiving_date').valid();
				}
			});
			$('#mark_as_received form input.deposit_date').pickadate({
				firstDay: 1,
				clear: '',
				selectYears: true,
				selectMonths: true,
				formatSubmit: 'yyyy-mm-dd 00:00:00',
				hiddenSuffix: '_formatted',
				onSet: function(context) {
					$('#mark_as_received input.deposit_date').valid();
				}
			});

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.finance.ftl_invoice.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
							
                            head.push('S.No');
                            head.push('Invoice No.');
                            head.push('Req ID');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Tracking Number');
                            head.push('Weight');
                            head.push('Required Vehicle');
                            head.push('Quantity');
                            head.push('Request Date');
                            head.push('Vendor');
                            head.push('Total Cost');
                            head.push('Charges');
                            head.push('GST');
                            head.push('Charges Collection');
                            head.push('Status');
                            head.push('Received Date');
                            head.push('Company Bank');
                            head.push('Received Amount');
                            head.push('Tax Amount');
                            head.push('Deposit Date');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.invoice_number);
                                row.push(values.request_id);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.tracking_number);
                                row.push(values.weight);
                                row.push(values.vehicle);
                                row.push(values.quantity);
                                row.push(values.request_date);
                                row.push(values.vendor);
                                row.push(values.total_cost);
                                row.push(values.charges);
                                row.push(values.gst);
                                row.push(values.collection_type);
                                row.push(values.status);
                                row.push(values.receiving_date);
                                row.push(values.company_bank);
                                row.push(values.received_amount);
                                row.push(values.tax_amount);
                                row.push(values.deposit_date);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });

            var selected_rows = [];
			var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
					@if ((session('role_id') == 1 || in_array(510, session('permissions'))))
					{
						text: '<i class="ft-plus-circle"></i> Receive',
						className: 'btn btn-primary mark_as_received',
						enabled: false,
						action: function (e, dt, node, config) {
							if(selected_rows.length > 0){
								console.log(selected_rows);
								$('#mark_as_received form input.ids').val(selected_rows);
								$('#mark_as_received form select.company_bank').val('').change();
								$('#mark_as_received form input.received_amount').val('');
								$('#mark_as_received form input.tax_amount').val('');
								$('#mark_as_received form input.pickadate').val('');
								$('#mark_as_received form label.danger').remove();
								$('#mark_as_received').modal('show');

							}

						}
					},
					@endif
					{
						extend: 'selectAll',
						text: 'Select All',
						className: 'select_all',
						action : function(e) {
							e.preventDefault();

							table.rows().nodes().each(function(index) {
								var row = table.row(index);

								if ($(row.node().firstChild).hasClass('select-checkbox')) {
									row.select();

									id = parseInt(row.id());

									var index = $.inArray(id, selected_rows);

									if (index === -1) {
										selected_rows.push(id);
									}

									table.button('.mark_as_received').enable();
								}
							});
						}
					}, {
						extend: 'selectNone',
						text: 'Select None',
						className: 'select_none',
						action : function(e) {
							e.preventDefault();

							table.rows().nodes().each(function(index) {
								var row = table.row(index);

								if ($(row.node().firstChild).hasClass('select-checkbox')) {
									row.deselect();

									id = parseInt(row.id());

									var index = $.inArray(id, selected_rows);

									if (index !== -1) {
										selected_rows.splice(index, 1);
									}

									if (selected_rows.length == 0) {
										table.button('.mark_as_received').disable();
									}
								}
							});
						}
					},{
						extend: 'excel',
						title: 'Ftl Invoices',
						className: 'btn btn-primary',
						text: '<i class="la la-file-excel-o"></i> Excel',
					}],
				scrollX: true, scrollY: '500px',
				select: {
					info: false,
					style: 'multi',
					selector: 'td.select-checkbox',
					className: 'selected bg-primary bg-lighten-5 primary'
				},
				lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
				pageLength: 50,
				pagingType: 'full_numbers',
				processing: true,
                language: {
                    processing: data_table_loader
                },
				serverSide: true,
				ajax: '{{ route('admin.finance.ftl_invoice.list') }}',
				rowId: 'id',
				order: [[8, 'desc']],
				columns: [
					
					{data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data:'invoice_number_button', name: 'walkin_ftl_invoices.invoice_number', class: 'align-middle text-center invoice_number'},
					{data:'request_id', name: 'ftlr.id', class: 'align-middle text-center request_id'},
					{data:'origin', nashipperme: 'origin.name', class: 'align-middle text-center origin'},
					{data:'destination', nashipperme: 'destination.name', class: 'align-middle text-center destination'},
					{data:'tracking_number', name: 's.tracking_number', class: 'align-middle text-center tracking_number'},
					{data:'weight', name: 'ftlr.weight', class: 'align-middle text-center weight'},
					{data:'vehicle', name: 'vt.name', class: 'align-middle text-center vehicle'},
					{data:'quantity', name: 'ftlr.quantity', class: 'align-middle text-center quantity'},
					{data:'request_date', name: 'ftlr.date', class: 'align-middle text-center request_date'},
					{data:'vendor', name: 'ven.name', class: 'align-middle text-center vendor'},
					{data:'total_cost', name: 'ftlr.freight_cost', class: 'align-middle text-center total_cost'},
					{data:'charges', name: 'ftlr.freight_charges', class: 'align-middle text-center charges'},
					{data:'gst', name: 'ftlr.gst', class: 'align-middle text-center gst'},
					{data:'collection_type', name: 'ftlr.collection_type', class: 'align-middle text-center collection_type'},
					{data:'status', name: 'walkin_ftl_invoices.status_id', class: 'align-middle text-center status'},
					{data:'receiving_date', name: 'walkin_ftl_invoices.receiving_date', class: 'align-middle text-center receiving_date'},
					{data:'company_bank', name: 'bl.name', class: 'align-middle text-center company_bank'},
					{data:'received_amount', name: 'walkin_ftl_invoices.received_amount', class: 'align-middle text-center received_amount'},
					{data:'tax_amount', name: 'walkin_ftl_invoices.tax_amount', class: 'align-middle text-center tax_amount'},
					{data:'deposit_date', name: 'walkin_ftl_invoices.deposit_date', class: 'align-middle text-center deposit_date'},
					{data:'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
				],
				rowCallback: function(row, data, index) {
					var info = table.page.info();

					$('td:eq(1)', row).html(index + 1 + info.page * info.length);
					if (data.status == 'Pending') {
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
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';
                    var company_bank_select = '<select name="company_bank_select" id="company_bank_select" class="select2 form-control"></select>';

					this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.aging') || $(header).is('.overdue_by') || $(header).is('.action')) {
							$(td).appendTo($(search));
						}
						else if ($(header).is('.company_bank')) {
                            $(company_bank_select).appendTo($(search))
							.on('change', function() {
								column.search($(this).val(), false, false, true).draw();
							}).wrap(td);
                        }
						
                        else if ($(header).is('.status')) {
                            $(status_select).appendTo($(search))
                            .on('change', function() {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td);
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

					var company_banks = $.map({!! $company_banks !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;

                        return obj;
                    });
					console.log(company_banks);
                    $('#company_bank_select').prepend('<option value="" selected></option>').select2({
                        data: company_banks,
                        placeholder: 'Select Company Bank',
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    

                    $('#status_select').prepend('<option value="" selected></option>').select2({
                        data: [{id: 1, name: "Pending", text: "Pending"},{id: 2, name: "Received", text: "Received"}],
                        placeholder: 'Select Status',
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

					this.api().table().columns.adjust();
				}
			});

			$('#mark_as_received form').validate({
				errorClass: 'danger',
				successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parent('.form-group'));
				}
			});


			$('#datatable tbody').on('click', 'tr td.invoice_number button', function() {
                var id = parseInt($(this).parents('tr').attr('id'));

                if (id) {
                    $.ajax({
						url: '{!! route('admin.finance.ftl_invoice.print') !!}',
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
            });

			$('.datatable tbody').on('click', 'tr td.select-checkbox', function() {
				var id = parseInt($(this).parent('tr').attr('id'));

				var index = $.inArray(id, selected_rows);

				if (index === -1) {
					selected_rows.push(id);
				}
				else {
					selected_rows.splice(index, 1);
				}

				if (selected_rows.length > 0) {
					table.button('.mark_as_received').enable();
				}
				else {
					table.button('.mark_as_received').disable();
				}
			});

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
				var id = parseInt($(this).parents('tr').attr('id'));

				if ($(this).hasClass('export_to_excel')) {
					window.open('{!! route('admin.finance.ftl_invoice.export_to_excel') !!}?id=' + id, '_blank');
				}
				else if ($(this).hasClass('email_reminder')) {
					$.ajax({
						url: '{!! route('admin.finance.invoices.email_reminder') !!}',
						method: 'PUT',
						data: {
							'_token': '{{ csrf_token() }}',
							'id': id
						}
					})
					.done(function(data) {
						if (data.status == 0) {
							toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
						}
						else {
							toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
						}

						table.draw('false');
					});
				}
				else if ($(this).hasClass('mark_as_received')) {
					$('#mark_as_received form input.id').val(id);
					$('#mark_as_received form select.company_bank').val('').change();

					$('#mark_as_received form input.received_amount').val('');
					$('#mark_as_received form input.tax_amount').val('');
					$('#mark_as_received form input.pickadate').val('');

					$('#mark_as_received form label.danger').remove();


					$('#mark_as_received').modal('show');
				}
				else if ($(this).hasClass('print_origin_wise')) {
					$.ajax({
						url: '{!! route('admin.finance.invoices.print_origin_wise') !!}',
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
			});
		});
	</script>
@endsection