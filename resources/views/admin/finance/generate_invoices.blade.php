@extends('admin.layout.master')

@section('title', 'Generate Invoices')

@section('content')
	<h1 class="mb-1">
		Generate Invoices
	</h1>

	<div class="card">
		<div class="card-content" aria-expanded="true">
			<div class="card-body">
				@include('admin.inc.messages')

				<form id="search_form" class="form-inline mb-4 justify-content-center" novalidate="novalidate">
					<div class="form-group">
						<select name="shipper" class="select2" id="shipper" data-rule-required="true" data-msg-required="Shipper is required">
							@foreach($users as $user)
								<option value="{{ $user->id }}">{{ $user->name }}</option>
							@endforeach
						</select>
					</div>

					<div class="form-group input-group ml-1">
						<div class="input-group-prepend">
							<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
								<span class="la la-calendar-o"></span>
							</span>
						</div>

						<input type="text" name="from_date" class="form-control pickadate bg-primary border-primary white rounded-right" id="from_date" placeholder="Date (From)*" data-rule-required="true" data-msg-required="Date (From) is required">
					</div>

					<div class="form-group input-group ml-1">
						<div class="input-group-prepend">
							<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
								<span class="la la-calendar-o"></span>
							</span>
						</div>

						<input type="text" name="to_date" class="form-control pickadate bg-primary border-primary white rounded-right" id="to_date" placeholder="Date (To)*" data-rule-required="true" data-msg-required="Date (To) is required">
					</div>

					<div class="form-group ml-1">
						<button type="submit" name="search" class="btn btn-primary search" value="Search">Search</button>
					</div>
				</form>

				<div class="row mb-4 justify-content-center text-center" id="total">
					<div class="col-4">
						<div class="form-group">
							<label class="mx-auto">Total Charges</label>
							<input type="text" name="total_charges" class="form-control text-center total_charges w-auto" value="0" readonly="readonly">
						</div>
					</div>

					<div class="col-4">
						<div class="form-group">
							<label class="mx-auto">Total GST</label>
							<input type="text" name="total_gst" class="form-control text-center total_gst" value="0" readonly="readonly">
						</div>
					</div>

					<div class="col-4">
						<div class="form-group">
							<label class="mx-auto">Total Invoice Amount</label>
							<input type="text" name="total_invoice_amount" class="form-control text-center total_invoice_amount" value="0" readonly="readonly">
						</div>
					</div>
				</div>

				<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
					<thead>
						<tr role="row" class="bg-primary white">
							<th class="border-primary border-darken-1"></th>
							<th class="border-primary border-darken-1">S. No.</th>
							<th class="border-primary border-darken-1">Shipper</th>
							<th class="border-primary border-darken-1">Tracking Number</th>
							<th class="border-primary border-darken-1">Type</th>
							<th class="border-primary border-darken-1">Created Datetime</th>
							<th class="border-primary border-darken-1">City</th>
							<th class="border-primary border-darken-1">Phone No(s).</th>
							<th class="border-primary border-darken-1">Address</th>
							<th class="border-primary border-darken-1">Charges</th>
							<th class="border-primary border-darken-1">GST</th>
							<th class="border-primary border-darken-1">Invoice Amount</th>
							<th class="border-primary border-darken-1">Bank</th>
							<th class="border-primary border-darken-1">Bank Branch</th>
							<th class="border-primary border-darken-1">Account No.</th>
							<th class="border-primary border-darken-1">Account Title</th>
							<th class="border-primary border-darken-1">IBAN</th>
							<th class="border-primary border-darken-1">Account City</th>
						</tr>
					</thead>
				</table>

				@if (session('role_id') == 1 || in_array(121, session('permissions')))
					<div class="modal fade" id="generate_invoice" role="dialog" aria-labelledby="generate_invoice_title" aria-hidden="true">
						<div class="modal-dialog modal-sm" role="document">
							<div class="modal-content">
								<form id="generate_invoice_form" class="form-horizontal" novalidate="novalidate" method="POST" action="{{ route('admin.finance.generate_invoices.store') }}">
									{{ csrf_field() }}

									<input type="hidden" name="shipper_id" class="shipper_id">

									<input type="hidden" name="from_date" class="from_date">

									<input type="hidden" name="to_date" class="to_date">

									<input type="hidden" name="pending_invoice_shipment_ids" class="pending_invoice_shipment_ids">

									<div class="modal-header">
										<h4 class="modal-title" id="cargo_consignment_title">Generate Invoice</h4>
									</div>
									<div class="modal-body">
										<div class="form-group input-group">
											<div class="input-group-prepend">
												<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
													<span class="la la-calendar-o"></span>
												</span>
											</div>

											<input type="text" name="due_date" class="form-control pickadate bg-primary border-primary white rounded-right" id="due_date" placeholder="Due Date*" data-rule-required="true" data-msg-required="Due Date is required">
										</div>
									</div>
									<div class="modal-footer text-right">
										<button type="submit" name="submit" class="btn btn-primary">Generate</button>
									</div>
								</form>
						</div>
						</div>
					</div>
				@endif
			</div>
		</div>
	</div>
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			@if (session('print'))
				window.open('{!! route('admin.finance.generate_invoices.print') !!}?id=' + '{{ session('print') }}', '_blank');
			@endif

			$('#search_form #shipper').prepend('<option value="" selected="selected"></option>').select2({
				width: '150px',
				placeholder: 'Shipper*'
			});

			$('#search_form #from_date').pickadate({
				firstDay: 1,
				clear: '',
				selectYears: true,
				selectMonths: true,
				formatSubmit: 'yyyy-mm-dd 00:00:00',
				hiddenSuffix: '_formatted',
				onSet: function(context) {
                    if (context.select) {
                        $('#search_form #to_date').pickadate('picker').set('min', $('#search_form #from_date').pickadate('picker').get('select'));
                    }
                }
			});

			$('#search_form #to_date').pickadate({
				firstDay: 1,
				clear: '',
				selectYears: true,
				selectMonths: true,
				formatSubmit: 'yyyy-mm-dd 23:59:59',
				hiddenSuffix: '_formatted',
				onSet: function(context) {
                    if (context.select) {
                        $('#search_form #from_date').pickadate('picker').set('max', $('#search_form #to_date').pickadate('picker').get('select'));
                    }
                }
			});

			$('#search_form').validate({
				errorClass: 'danger',
				successClass: 'success',
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parents('form'));
				},
				submitHandler: function(form) {
					$('#generate_invoice #generate_invoice_form .shipper_id').val($(form).find('#shipper').val());
					$('#generate_invoice #generate_invoice_form .from_date').val($(form).find('input[name="from_date_formatted"]').val());
					$('#generate_invoice #generate_invoice_form .to_date').val($(form).find('input[name="to_date_formatted"]').val());

					table.draw('false');
				}
			});

			$('#generate_invoice #generate_invoice_form #due_date').pickadate({
				firstDay: 1,
				clear: '',
				min: '{{ Carbon\Carbon::now() }}',
				selectYears: true,
				selectMonths: true,
				formatSubmit: 'yyyy-mm-dd 00:00:00',
				hiddenSuffix: '_formatted',
				onSet: function(context) {
                    $('#generate_invoice #generate_invoice_form #due_date').valid();
                }
			});

			$('#generate_invoice #generate_invoice_form').validate({
				errorClass: 'danger',
				successClass: 'success',
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parent('.form-group'));
				},
				submitHandler: function(form) {
					$(form).find('button[type=submit]').attr('disabled', 'disabled');

					swal({
						title: 'Please Wait!',
						text: 'Your invoice is being generated!',
						icon: 'info',
						buttons: false,
						closeOnClickOutside: false,
						closeOnEsc: false
					});

					form.submit();
				}
			});

			function calculation(parent) {
				var id = parseInt(parent.attr('id'));

				var index = $.inArray(id, selected_rows);

				var total_charges_selector = $('#total .total_charges');
				var total_gst_selector = $('#total .total_gst');
				var total_invoice_amount_selector = $('#total .total_invoice_amount');

				if (index === -1) {
					selected_rows.push(id);

					var total_charges = ((total_charges_selector.val() != '') ? parseInt(total_charges_selector.val()) : 0) + ((parent.children('td.charges').html() != '') ? parseInt(parent.children('td.charges').html().replace(/,/g, '')) : 0);
					var total_gst = ((total_gst_selector.val() != '') ? parseInt(total_gst_selector.val()) : 0) + ((parent.children('td.gst').html() != '') ? parseInt(parent.children('td.gst').html().replace(/,/g, '')) : 0);
					var total_invoice_amount = ((total_invoice_amount_selector.val() != '') ? parseInt(total_invoice_amount_selector.val()) : 0) + ((parent.children('td.invoice_amount').html() != '') ? parseInt(parent.children('td.invoice_amount').html().replace(/,/g, '')) : 0);
				}
				else {
					selected_rows.splice(index, 1);

					var total_charges = ((total_charges_selector.val() != '') ? parseInt(total_charges_selector.val()) : 0) - ((parent.children('td.charges').html() != '') ? parseInt(parent.children('td.charges').html().replace(/,/g, '')) : 0);
					var total_gst = ((total_gst_selector.val() != '') ? parseInt(total_gst_selector.val()) : 0) - ((parent.children('td.gst').html() != '') ? parseInt(parent.children('td.gst').html().replace(/,/g, '')) : 0);
					var total_invoice_amount = ((total_invoice_amount_selector.val() != '') ? parseInt(total_invoice_amount_selector.val()) : 0) - ((parent.children('td.invoice_amount').html() != '') ? parseInt(parent.children('td.invoice_amount').html().replace(/,/g, '')) : 0);
				}

				if (selected_rows.length > 0) {
					total_charges_selector.val(parseInt(total_charges));
					total_gst_selector.val(parseInt(total_gst));
					total_invoice_amount_selector.val(parseInt(total_invoice_amount));

					table.button('.generate_invoice').enable();
				}
				else {
					total_charges_selector.val(0);
					total_gst_selector.val(0);
					total_invoice_amount_selector.val(0);

					table.button('.generate_invoice').disable();
				}
			}
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.finance.generate_invoices.list') }}',
                        data: {
                            'page': 'all',
                            'shipper' : $('#search_form #shipper').val(),
                    		'from_date' : $('#search_form input[name="from_date_formatted"]').val(),
                    		'to_date': $('#search_form input[name="to_date_formatted"]').val()
                        },
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Shipper');
                            head.push('Tracking Number');
                            head.push('Type');
                            head.push('Created Datetime');
                            head.push('City');
                            head.push('Phone No(s)');
                            head.push('Address');
                            head.push('Charges');
                            head.push('GST');
                            head.push('Invoice Amount');
                            head.push('Bank');
                            head.push('Bank Branch');
                            head.push('Account No.');
                            head.push('Account Title');
                            head.push('IBAN');
                            head.push('Account City');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.shipper);
                                row.push(values.tracking_number);
                                row.push(values.type);
                                row.push(values.created_at);
                                row.push(values.city);
                                row.push(values.phone_numbers);
                                row.push(values.address);
                                row.push(values.charges);
                                row.push(values.gst);
                                row.push(values.invoice_amount);
                                row.push(values.bank);
                                row.push(values.bank_branch);
                                row.push(values.account_no);
                                row.push(values.account_title);
                                row.push(values.iban);
                                row.push(values.account_city);


                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
			selected_rows = [];

			var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
				@if (session('role_id') == 1 || in_array(121, session('permissions')))
					buttons: [{
						text: 'Generate Invoice',
						className: 'btn btn-primary generate_invoice',
						enabled: false,
						action: function (e, dt, node, config) {
							$('#generate_invoice #generate_invoice_form .pending_invoice_shipment_ids').val(selected_rows);

							$('#generate_invoice').modal('show');
						}
					},
                    {
                        extend: 'excel',
                        title: 'Generate Invoice',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }, {
	                    extend: 'selectAll',
	                    text: 'Select All',
	                    className: 'select_all',
	                    action : function(e) {
	                        e.preventDefault();

	                        table.rows().nodes().each(function(index) {
	                            var row = table.row(index);

	                            if ($(row.node().firstChild).hasClass('select-checkbox')) {
	                                row.select();

	                                var parent = $(row.node());

									calculation(parent);
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

	                          if ($(row.node().firstChild).hasClass('select-checkbox') && $(row.node()).hasClass('selected')) {
	                            row.deselect();

	                            var parent = $(row.node());

								calculation(parent);
	                          }
	                        });
	                    }
	                }],
				@else
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Generate Invoice',
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
				lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
				pageLength: 50,
				pagingType: 'full_numbers',
				processing: true,
				serverSide: true,
				ajax: {
					url: '{{ route('admin.finance.generate_invoices.list') }}',
					data: function (d) {
						d.shipper = $('#search_form #shipper').val();
						d.from_date = $('#search_form input[name="from_date_formatted"]').val();
						d.to_date = $('#search_form input[name="to_date_formatted"]').val();
					}
				},
				rowId: 'id',
				order: [[5, 'asc']],
				columns: [
					{data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data:'shipper', name: 'u.name', class: 'align-middle text-center shipper'},
					{data:'tracking_number_link', name: 's.tracking_number', class: 'align-middle text-center tracking_number_link'},
					{data:'type', name: 'type', class: 'align-middle text-center type'},
					{data:'created_at', name: 'pending_invoice_shipments.created_at', class: 'align-middle text-center created_at'},
					{data:'city', name: 'c.name', class: 'align-middle text-center city'},
					{data:'phone_numbers', name: 'phone_numbers', class: 'align-middle text-center phone_numbers'},
					{data:'address', name: 'u.address', class: 'align-middle text-center address'},
					{data:'charges', name: 'pending_invoice_shipments.charges', class: 'align-middle text-center charges'},
					{data:'gst', name: 'pending_invoice_shipments.gst', class: 'align-middle text-center gst'},
					{data:'invoice_amount', name: 'pending_invoice_shipments.invoice_amount', class: 'align-middle text-center invoice_amount'},
					{data:'bank', name: 'bank', class: 'align-middle text-center bank'},
					{data:'bank_branch', name: 'ubi.bank_branch', class: 'align-middle text-center bank_branch'},
					{data:'account_no', name: 'ubi.account_no', class: 'align-middle text-center account_no'},
					{data:'account_title', name: 'ubi.account_title', class: 'align-middle text-center account_title'},
					{data:'iban', name: 'ubi.iban', class: 'align-middle text-center iban'},
					{data:'account_city', name: 'bc.name', class: 'align-middle text-center account_city'}
				],
				rowCallback: function(row, data, index) {
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
                    var bank_select = '<select name="bank_select" id="bank_select" class="select2 form-control"></select>';
					this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.select') || $(header).is('.serial_number')) {
							$(td).appendTo($(search));
						}else if($(header).is('.bank')){
                            $(bank_select).appendTo($(search))
                                .on('change', function () {
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
                    var data = $.map({!! $banks !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                    var data = $.map({!! $banks !!}, function (obj) {
                        obj.text = obj.name;

                        return obj;
                    });

                    $("#bank_select").prepend('<option value="" selected></option>').select2({
                        data:data,
                        placeholder: "Select Bank",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

					this.api().table().columns.adjust();
				}
			});

			$('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
				var parent = $(this).parent('tr');

				calculation(parent);
			});
		});
	</script>
@endsection