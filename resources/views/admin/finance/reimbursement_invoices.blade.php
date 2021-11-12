@extends('admin.layout.master')

@section('title', 'Reimbursement Invoices')

@section('content')
	<h1 class="mb-1">
		Reimbursement Invoices
	</h1>

	<div class="card">
		<div class="card-content" aria-expanded="true">
			<div class="card-body">
				@include('admin.inc.messages')

				<table class="table table-bordered datatable" id="datatable" style="z-index: 3;width:100%;">
					<thead>
						<tr role="row" class="bg-primary white">
							<th class="border-primary border-darken-1">S. No.</th>
							<th class="border-primary border-darken-1">Invoice Number</th>
							<th class="border-primary border-darken-1">Shipper</th>
							<th class="border-primary border-darken-1">City</th>
							<th class="border-primary border-darken-1">Total Charges</th>
							<th class="border-primary border-darken-1">Total GST</th>
							<th class="border-primary border-darken-1">Total Invoice Amount</th>
							<th class="border-primary border-darken-1">Generation Date</th>
							<th class="border-primary border-darken-1">Invoicing Cycle</th>
							<th class="border-primary border-darken-1">Invoicing Date</th>
							<th class="border-primary border-darken-1">Invoicing Type</th>
							<th class="border-primary border-darken-1"></th>
						</tr>
					</thead>
				</table>
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
                            head.push('Invoice No.');
                            head.push('Shipper');
                            head.push('City');
                            head.push('Total Charges');
                            head.push('Total GST');
                            head.push('Total Invoice Amount');
                            head.push('Generation Date');
                            head.push('Invoicing Cycle');
                            head.push('Invoicing Date');
                            head.push('Invoicing Type');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.invoice_number);
                                row.push(values.shipper);
                                row.push(values.city);
                                row.push(values.total_charges);
                                row.push(values.total_gst);
                                row.push(values.total_invoice_amount);
                                row.push(values.created_at);
                                row.push(values.invoicing_cycle);
                                row.push(values.invoicing_date);
                                row.push(values.payment_type);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });

			var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
					{
						extend: 'excel',
						title: 'Pending Invoices',
						className: 'btn btn-primary',
						text: '<i class="la la-file-excel-o"></i> Excel',
					},
				'reset'],
				scrollX: true, scrollY: '500px',
				lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
				pageLength: 50,
				pagingType: 'full_numbers',
				processing: true,
                language: {
                    processing: data_table_loader
                },
				serverSide: true,
				ajax: '{{ route('admin.finance.invoices.reimbursement.list') }}',
				rowId: 'id',
				order: [[9, 'desc']],
				columns: [
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data:'invoice_number_button', name: 'invoice_for_reimbursements.invoice_number', class: 'align-middle text-center invoice_number'},
					{data:'shipper', name: 'u.name', class: 'align-middle text-center shipper'},
					{data:'city', name: 'c.name', class: 'align-middle text-center shipper'},
					{data:'total_charges', name: 'invoice_for_reimbursements.total_charges', class: 'align-middle text-center total_charges'},
					{data:'total_gst', name: 'invoice_for_reimbursements.total_gst', class: 'align-middle text-center total_gst'},
					{data:'total_invoice_amount', name: 'invoice_for_reimbursements.total_invoice_amount', class: 'align-middle text-center total_invoice_amount'},
					{data:'created_at', name: 'invoice_for_reimbursements.created_at', class: 'align-middle text-center generation_date'},
					{data:'invoicing_cycle', name: 'ic.name', class: 'align-middle text-center invoicing_cycle', orderable: false, searchable: false},
					{data:'invoicing_date', name: 'invoice_for_reimbursements.invoicing_date', class: 'align-middle text-center invoicing_date'},
					{data:'payment_type', name: 'invoice_for_reimbursements.payment_type', class: 'align-middle text-center payment_type'},
					{data:'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
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
					var payment_type_select = '<select name="payment_type_select" id="payment_type_select" class="select2 form-control"></select>';
					this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.invoicing_cycle')) {
							$(td).appendTo($(search));
						} else if ($(header).is('.payment_type'))
						{
							$(payment_type_select).appendTo($(search))
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

					var payment_types = $.map([{'id':0,'name':'Make'},{'id':1,'name':'Done'}], function (obj) {
						obj.id = obj.id;
						obj.text = obj.name;

						return obj;
					});


					$('#payment_type_select').prepend('<option value="" selected></option>').select2({
						data: payment_types,
						placeholder: 'Select Payment Type',
						width:'100%',
						containerCssClass: 'select-xs',
						dropdownCssClass: 'form-control-sm p-0'
					});

					this.api().table().columns.adjust();
				}
			});

			$('#datatable tbody').on('click', 'tr td.invoice_number button', function() {
                var id = parseInt($(this).parents('tr').attr('id'));

                if (id) {
                    $.ajax({
						url: '{!! route('admin.finance.invoices.reimbursement.print') !!}',
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

            $('#datatable tbody').on('contextmenu', 'tr td.invoice_number button', function(e) {
                e.preventDefault();

                var id = parseInt($(this).parents('tr').attr('id'));

                if (id) {
                    $.ajax({
                        url: '{!! route('admin.finance.invoices.reimbursement.print') !!}',
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
            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
				var id = parseInt($(this).parents('tr').attr('id'));

				if ($(this).hasClass('export_to_excel')) {
					window.open('{!! route('admin.finance.invoices.reimbursement.export_to_excel') !!}?id=' + id, '_blank');
				}
				else if ($(this).hasClass('print_origin_wise')) {
					$.ajax({
						url: '{!! route('admin.finance.invoices.reimbursement.print_origin_wise') !!}',
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
				else if ($(this).hasClass('print_gst_wise')) {
					$.ajax({
						url: '{!! route('admin.finance.invoices.reimbursement.print_gst_wise') !!}',
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