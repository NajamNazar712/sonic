@extends('admin.layout.master')

@section('title', 'Invoices')

@section('content')
	<h1 class="mb-1">
		Invoices
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
						<th class="border-primary border-darken-1">Account Type</th>
						<th class="border-primary border-darken-1">Invoice Number</th>
						<th class="border-primary border-darken-1">Shipper</th>
						<th class="border-primary border-darken-1">City</th>
						<th class="border-primary border-darken-1">Total Charges</th>
						<th class="border-primary border-darken-1">Total GST</th>
						<th class="border-primary border-darken-1">Total Invoice Amount</th>
						<th class="border-primary border-darken-1">Generation Date</th>
						<th class="border-primary border-darken-1">Invoicing Date</th>
						<th class="border-primary border-darken-1">Invoicing Cycle</th>
						<th class="border-primary border-darken-1">Aging</th>
						<th class="border-primary border-darken-1">Due Date</th>
						<th class="border-primary border-darken-1">Overdue By</th>
						<th class="border-primary border-darken-1">Corporate Invoicing Type</th>
						<th class="border-primary border-darken-1">Received Date</th>
						<th class="border-primary border-darken-1">Company Bank</th>
						<th class="border-primary border-darken-1">Status</th>
						<th class="border-primary border-darken-1">Deposit Date</th>
						<th class="border-primary border-darken-1">Deposit Slip</th>
						<th class="border-primary border-darken-1">Received Amount</th>
						<th class="border-primary border-darken-1">Tax Amount</th>
						<th class="border-primary border-darken-1">Payment Type</th>
						<th class="border-primary border-darken-1"></th>
						{{--<th class="border-primary border-darken-1">Payment Type</th>--}}
						{{--  <th class="border-primary border-darken-1">Status</th>--}}
					</tr>
					</thead>
				</table>

				<div class="modal fade" id="mark_as_received" role="dialog" aria-labelledby="mark_as_received_title" aria-hidden="true">
					<div class="modal-dialog modal-sm" role="document">
						<div class="modal-content">
							<form class="form-horizontal" method="POST" action="{{ route('admin.finance.invoices.mark_as_received') }}" novalidate="novalidate">
								{{ csrf_field() }}

								<input type="hidden" name="id" class="id">

								<div class="modal-header">
									<h4 class="modal-title" id="mark_as_received_title">Mark as Received<span></span></h4>

									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">×</span>
									</button>
								</div>
								<div class="modal-body">
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
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
									<button type="submit" class="btn btn-primary ml-auto">Mark as Received</button>
								</div>
							</form>
						</div>
					</div>
				</div>

				<!--Deposit Slip Modal -->
				<div class="modal fade text-left" id="uploadDepositSlip" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="uploadDepositSlip"
					 aria-hidden="true">
					<div class="modal-dialog modal-xl" role="document">
						<div class="modal-content">
							<div class="modal-header bg-primary white">
								<h4 class="modal-title white">Deposit Slip Upload</h4>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body text-center">
								<form id="invoice_upload_form" class="form" action="{{route('admin.finance.invoices.slip')}}" method="post" enctype="multipart/form-data">
									@csrf
									<input type="hidden" name="invoice_id" id="invoice_id"/>
									<table class="table table-bordered datatable" id="invoice_upload_table" style="z-index: 3;">
										<thead>
										<tr role="row" class="bg-primary white">
											<th class="border-primary border-darken-1">S. No.</th>
											<th class="border-primary border-darken-1">Date</th>
											<th class="border-primary border-darken-1">Bank Name</th>
											<th class="border-primary border-darken-1">Deposit Slip</th>
											<th class="border-primary border-darken-1"></th>
										</tr>
										</thead>
									</table>
									<hr>
									<div class="row justify-content-center">
										<div class="col-3">
											<button id="DepositSlipButton" type="submit" class="btn btn-primary btn-block" disabled>Upload</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>


				<div class="modal fade text-left" id="ViewDepositSlip" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ViewDepositSlip"
					 aria-hidden="true">
					<div class="modal-dialog modal-lg" role="document">
						<div class="modal-content">
							<div class="modal-header bg-primary white">
								<h4 class="modal-title white">Deposit Slips View</h4>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body text-center">
								<table class="table table-bordered datatable" id="deposit_slip_table" style="z-index: 3;">
									<thead>
									<tr role="row" class="bg-primary white">

										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">Date</th>
										<th class="border-primary border-darken-1">Bank Name</th>
										<th class="border-primary border-darken-1">Deposit Slip</th>

									</tr>
									</thead>
								</table>

								<hr>
								<div class="row justify-content-center">
									<div class="col-3">
										<button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Close</button>
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
                        url: '{{ route('admin.finance.invoices.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Account Type');
                            head.push('Invoice No.');
                            head.push('Shipper');
                            head.push('City');
                            head.push('Total Charges');
                            head.push('Total GST');
                            head.push('Total Invoice Amount');
                            head.push('Generation Date');
							head.push('Invoicing Date');
							head.push('Invoicing Cycle');
                            head.push('Aging');
                            head.push('Due Date');
                            head.push('Overdue By');
							head.push('Corporate Invoicing Type');
                            head.push('Received Date');
                            head.push('Company Bank');
                            head.push('Status');
                            head.push('Deposit Date');
							head.push('Received Amount');
							head.push('Tax Amount');
                            head.push('Payment Type');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.account);
                                row.push(values.invoice_number);
                                row.push(values.shipper);
                                row.push(values.city);
                                row.push(values.total_charges);
                                row.push(values.total_gst);
                                row.push(values.total_invoice_amount);
                                row.push(values.created_at);
								row.push(values.invoicing_date);
								row.push(values.invoicing_cycle);
                                row.push(values.aging);
                                row.push(values.due_date);
                                row.push(values.overdue_by);
                                row.push(values.invoice_type);
                                row.push(values.received_date);
                                row.push(values.company_bank);
                                row.push(values.status);
                                row.push(values.deposit_date);
                                row.push(values.received_amount);
                                row.push(values.tax_amount);
                                row.push(values.payment_type);

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
					@if ((session('role_id') == 1 || in_array(122, session('permissions'))))
					{
						text: '<i class="ft-plus-circle"></i> Mark as Received',
						className: 'btn btn-primary mark_as_received_all_btn',
						enabled: false,
						action: function (e, dt, node, config) {
							if(selected_rows.length > 0){
								swal({
									title: 'Are You Sure?',
									text: 'Select Yes to mark invoices recieved!',
									icon: 'warning',
									buttons: {
										cancel: {
											text: 'No',
											value: null,
											visible: true,
											closeModal: true,
										},
										confirm: {
											text: 'Yes',
											value: true,
											visible: true,
											closeModal: true
										}
									},
									closeOnClickOutside: false,
									closeOnEsc: false,
									dangerMode: true
								}).then(function (confirm) {
									if (confirm) {
										blockPagePermanently();
										$.ajax({
											url: '{!! route('admin.finance.invoices.mark_as_received_all') !!}',
											method: 'POST',
											data: {
												'_token': '{{ csrf_token() }}',
												'id': selected_rows,
											}
										}).done(function(data) {
											UnblockPagePermanently();
											if(data == 1)
											{
												swal({
													title: 'Invoice Marked as Received',
													icon: 'success',
													closeOnClickOutside: false,
													closeOnEsc: false
												});

												table.draw();
											}
											else{
												swal({
													title: 'Error Occurred In Marking Invoice Received',
													icon: 'error',
													closeOnClickOutside: false,
													closeOnEsc: false
												});
											}
										});
									}
								});

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
										console.log(selected_rows)
									}

									table.button('.mark_as_received_all_btn').enable();
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
										table.button('.mark_as_received_all_btn').disable();
									}
								}
							});
						}
					},
					{
						extend: 'excel',
						title: 'Invoices',
						className: 'btn btn-primary',
						text: '<i class="la la-file-excel-o"></i> Excel',
					},
				'reset'],
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
				ajax: '{{ route('admin.finance.invoices.list') }}',
				rowId: 'id',
				order: [[9, 'desc']],
				columns: [
					{data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data:'account', name: 'account_type', class: 'align-middle text-center account'},
					{data:'invoice_number_btn', name: 'invoice_number_btn', class: 'align-middle text-center invoice_number_btn'},
					{data:'shipper', name: 'shipper', class: 'align-middle text-center shipper'},
					{data:'city', name: 'city', class: 'align-middle text-center city'},
					{data:'total_charges', name: 'total_charges', class: 'align-middle text-center total_charges'},
					{data:'total_gst', name: 'total_gst', class: 'align-middle text-center total_gst'},
					{data:'total_invoice_amount', name: 'total_invoice_amount', class: 'align-middle text-center total_invoice_amount'},
					{data:'created_at', name: 'created_at', class: 'align-middle text-center created_at'},
					{data:'invoicing_date', name: 'invoicing_date', class: 'align-middle text-center invoicing_date'},
					{data:'invoicing_cycle', name: 'invoicing_cycle', class: 'align-middle text-center invoicing_cycle'},
					{data:'aging', name: 'aging', class: 'align-middle text-center aging', orderable: false, searchable: false},
					{data:'due_date', name: 'due_date', class: 'align-middle text-center due_date'},
					{data:'overdue_by', name: 'overdue_by', class: 'align-middle text-center overdue_by', orderable: false, searchable: false},
					{data:'invoice_type', name: 'invoice_type', class: 'align-middle text-center invoice_type'},
					{data:'received_date', name: 'received_date', class: 'align-middle text-center received_date'},
					{data:'company_bank', name: 'invoices.company_bank', class: 'align-middle text-center company_bank'},
					{data:'status', name: 'status', class: 'align-middle text-center status'},
					{data:'deposit_date', name: 'deposit_date', class: 'align-middle text-center deposit_date'},
					{data:'deposit_slip', name: 'deposit_slip', class: 'align-middle text-center deposit_slip', orderable: false, searchable: false},
					{data:'received_amount', name: 'received_amount', class: 'align-middle text-center received_amount'},
					{data:'tax_amount', name: 'tax_amount', class: 'align-middle text-center tax_amount'},
					{data:'payment_type', name: 'payment_type', class: 'align-middle text-center payment_type'},
					{data:'action', name: 'action', class: 'align-middle text-center action', orderable: false, searchable: false},
				],
				rowCallback: function(row, data, index) {

					if (data.account_type == 2 && (data.is_id == 1 || data.is_id == 2)) {
						$('td:eq(0)', row).addClass('select-checkbox');
					}

					var info = table.page.info();

					$('td:eq(1)', row).html(index + 1 + info.page * info.length);

					if (selected_rows.length != 0) {
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
                    var company_bank_select = '<select name="company_bank_select" id="company_bank_select" class="select2 form-control"></select>';
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';
					var invoice_type_select = '<select name="invoice_type_select" id="invoice_type_select" class="select2 form-control">' +
							'<option value="1">Courier Invoice</option>' +
							'<option value="2">Packaging Invoice</option>' +
							'</select>';
				/*	var account = '<select name="account" id="account" class="select2 form-control">' +
							'<option value="1">Corporate Account</option>' +
							'<option value="2">Reimbursement Account</option>' +
							'</select>';*/


					this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.aging') || $(header).is('.overdue_by') || $(header).is('.action') || $(header).is('.upload_slip') || $(header).is('.deposit_slip')) {
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
						/*else if ($(header).is('.account')) {
							$(account).appendTo($(search))
									.on('change', function() {
										column.search($(this).val(), false, false, true).draw();
									}).wrap(td);
						}*/

						else if($(header).is('.invoice_type')){
							$(invoice_type_select).appendTo($(search))
									.on( 'change', function () {
										column.search($(this).val(), false, false, true).draw();
									} ).wrap(td);
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
                        obj.id = obj.name;
                        obj.text = obj.name;

                        return obj;
                    });

                    $('#company_bank_select').prepend('<option value="" selected></option>').select2({
                        data: company_banks,
                        placeholder: 'Select Company Bank',
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

					/*$('#account').prepend('<option value="" selected></option>').select2({
						data: account,
						placeholder: 'Select Account Type',
						width:'100%',
						containerCssClass: 'select-xs',
						dropdownCssClass: 'form-control-sm p-0'
					});*/

					$("#invoice_type_select").prepend('<option value="" selected></option>').select2({
						placeholder: "Select Invoice Type",
						width:'100%',
						containerCssClass: 'select-xs',
						dropdownCssClass: 'form-control-sm p-0'
					});

                    var statuses = $.map({!! $invoice_statuses !!}, function (obj) {
                        obj.id = obj.name;
                        obj.text = obj.name;

                        return obj;
                    });

                    $('#status_select').prepend('<option value="" selected></option>').select2({
                        data: statuses,
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
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parent('.form-group'));
				}
			});


			$('#datatable tbody').on('click', 'tr td.invoice_number_btn button', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                var account = table.row($(this).parents('tr')).data().account_type;

				var url ='';
				if(account == 2){
					url = '{!! route('admin.finance.invoices.invoices_print') !!}';
				}
				else{
					url = '{!! route('admin.finance.invoices.reimbursement.invoices_print') !!}';
				}

                if (id) {
                    $.ajax({
						url: url,
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

           /* $('#datatable tbody').on('contextmenu', 'tr td.invoice_number_btn button', function(e) {
                e.preventDefault();

                var id = parseInt($(this).parents('tr').attr('id'));

                if (id) {
                    $.ajax({
                        url: '{!! route('admin.finance.invoices.reimbursement.detail_print') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'id': id,
                            'header': true
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
            });*/

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
					table.button('.mark_as_received_all_btn').enable();
				}
				else {
					table.button('.mark_as_received_all_btn').disable();
				}
			});

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
				var id = parseInt($(this).parents('tr').attr('id'));
				var account_type = parseInt(table.row($(this).parents('tr')).data().account_type);

				if ($(this).hasClass('export_to_excel')) {
					if(account_type == 2) {
						window.open('{!! route('admin.finance.invoices.export_to_excel') !!}?id=' + id, '_blank');
					}
					else if(account_type == 1)
					{
						window.open('{!! route('admin.finance.invoices.reimbursement.export_to_excel') !!}?id=' + id, '_blank');
					}
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
							'id': id,
							'account_type':account_type,
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
				else if ($(this).hasClass('print_gst_wise')) {
					$.ajax({
						url: '{!! route('admin.finance.invoices.print_gst_wise') !!}',
						method: 'POST',
						data: {
							'_token': '{{ csrf_token() }}',
							'id': id,
							'account_type':account_type,
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

			var banks_list = $.map({!! $company_banks !!}, function (obj) {
				obj.id = obj.id;
				obj.text = obj.name;
				return obj;
			});
			var deposit_table;

			var rows_count = 0;
			$('#uploadDepositSlip').on('shown.bs.modal', function (event) {
				var id = event.relatedTarget;
				var invoice_id = $(id).data('target-id');
				$('#invoice_id').val($(id).data('target-id'));



				deposit_table = $('#invoice_upload_table').DataTable({
					dom: '<"d-inline-block"l><"pull-right"B>tipr',
					buttons:[{
						title: 'Add Row',
						className: 'btn btn-primary mb-1',
						text: '<i class="la la-plus"></i> Add Row',
						action:function (e) {
							add_row();
						}
					}],
					ordering:false,
					paging:false,
					columns: [
						{orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
						{name: 'date', class: 'align-middle date date-col-width form-group', width: '20%'},
						{name: 'bank_name', class: 'align-middle bank_name form-group'},
						{name: 'deposit_slip', class: 'align-middle deposit_slip form-group'},
						{name: 'action', class: 'align-middle action'},
					],

					rowCallback: function(row, data, index) {
						var info = deposit_table.page.info();

						$('td:eq(0)', row).html(index + 1 + info.page * info.length);

					},
					initComplete: function() {

						// this.api().table().columns.adjust();
					}
				});


				function add_row() {
					rows_count++;
					var date_input = '<div class="form-group input-group input-group-sm mb-0"><div class="input-group-prepend"><span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left"><span class="la la-calendar-o"></span></span></div><input type="text" name="date['+rows_count+']" id="deposit_date_' + rows_count + '" class="form-control pickadate-short-string bg-primary border-primary white rounded-right" placeholder="Date*" data-rule-required="true" data-msg-required="Date is required"></div>';
					var bank_select = '<select class="form-control hub_select select2" name="bank['+rows_count+']" data-rule-required="true" data-msg-required="Bank is required"></select>';
					var deposit_slip = '<input class="form-control form-control-sm" accept="image/png,image/jpeg" type="file" id="deposit_slip_'+rows_count+'" name="deposit_slip['+rows_count+']" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Deposit Slip is required">';
					if(rows_count == 1){
						var remove = '';
					}else{
						var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-sm btn-danger remove_row"><i class="la la-close"></i></a>';

					}
					deposit_table.row.add([0, date_input,bank_select,deposit_slip,remove]).node().id = rows_count;
					deposit_table.draw(true);
					$('#DepositSlipButton').attr('disabled', false);
					$('select[name="bank['+rows_count+']"]').prepend('<option value="" selected="selected"></option>').select2({
						data:banks_list,
						placeholder:'Select Bank',
						allowClear:true,
						width:'100%',
						dropdownCssClass: 'form-control-sm p-0'
					});
					$('#deposit_date_' + rows_count).pickadate({
						firstDay: 1,
						today: '',
						clear: '',
						close: '',
						max: new Date(),
						weekdaysShort: ['S', 'M', 'Tu', 'W', 'Th', 'F', 'S'],
						showMonthsShort: true,
						formatSubmit: 'yyyy-mm-dd 00:00:00',
						hiddenSuffix: '_formatted',
						onOpen: function() {
							// $('#deposit_date_' + rows_count+'_root').css('top', '-262px');
						},
					});
				}
			});

			$('body').on('click', '#invoice_upload_table a.remove_row',function () {
				var rid = parseInt($(this).parents('tr').attr('id'));
				deposit_table.row( $(this).parents('tr') ).remove().draw();
			});

			$('#uploadDepositSlip').on('hidden.bs.modal', function () {
				deposit_table.clear();
				deposit_table.destroy();
			});

			$('#invoice_upload_form').validate({
				errorClass: 'danger',
				successClass: 'success',
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parent('.form-group'));
				},
				submitHandler: function(form) {
					var pressed_button = $(this.submitButton);
					swal({
						title: 'Are You Sure?',
						text: 'Select Yes to upload Deposit Slips!',
						icon: 'warning',
						buttons: {
							cancel: {
								text: 'No',
								value: null,
								visible: true,
								closeModal: true,
							},
							confirm: {
								text: 'Yes',
								value: true,
								visible: true,
								closeModal: true
							}
						},
						closeOnClickOutside: false,
						closeOnEsc: false,
						dangerMode: true
					}).then(function (confirm) {
						if (confirm) {
							form.submit();
						}
					});

				}
			});

			var deposit_slip_table;
			$('body').on('click','.deposit_slip_view', function () {
				var id = $(this).parents('tr').attr('id');
				if(id){
					$.ajax({
						url:'{!! route('admin.finance.invoices.slip_view') !!}',
						type:'POST',
						data: {
							'invoice_id':id,
							'_token': '{{ csrf_token() }}'
						}
					}).done(function (data) {
						if(data.status){
							toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
						}else{
							$('#ViewDepositSlip').modal('show');
							deposit_slip_table = $('#deposit_slip_table').DataTable({
								dom: 'ltipr',
								ordering:false,
								paging:false,
								columns: [
									{orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
									{name: 'date', class: 'align-middle date date-col-width form-group'},
									{name: 'bank_name', class: 'align-middle bank_name form-group'},
									{name: 'deposit_slip', class: 'align-middle deposit_slip form-group'}
								],

								rowCallback: function(row, data, index) {
									var info = deposit_slip_table.page.info();

									$('td:eq(0)', row).html(index + 1 + info.page * info.length);

								},
								initComplete: function() {

								}
							});

							$.each(data.slips, function (index, value) {
								deposit_slip_table.row.add([0, value.date, value.bank, value.image]);
								deposit_slip_table.draw(true);
							});
						}
					});
				}
			});

			$('#ViewDepositSlip').on('hidden.bs.modal', function () {
				deposit_slip_table.clear();
				deposit_slip_table.destroy();
			});

			$('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
				var id = parseInt($(this).parents('tr').attr('id'));
				var account_type = parseInt(table.row($(this).parents('tr')).data().account_type);

				if ($(this).hasClass('detail_print')) {
					var url ='';
					if(account_type == 2){
						url = '{!! route('admin.finance.invoices.invoices_detail_print') !!}';
					}
					else{
						url = '{!! route('admin.finance.invoices.reimbursement.detail_print') !!}';
					}

					if (id) {
						$.ajax({
							url:url,
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
				}
			});



			});
	</script>
@endsection