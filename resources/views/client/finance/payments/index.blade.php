@extends('client.layout.master')

@section('title', 'Finance Payments')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Finance Payments
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('client.inc.messages')

							<form id="tracking_number_search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
								<div class="form-group">
									<input type="text" name="tracking_number" class="form-control tracking_number" id="tracking_number" placeholder="Tracking Number">
								</div>
							</form>

							<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">Payment ID</th>
										<th class="border-primary border-darken-1">Shipper</th>
										<th class="border-primary border-darken-1">City</th>
										<th class="border-primary border-darken-1">Phone No(s).</th>
										<th class="border-primary border-darken-1">Address</th>
										<th class="border-primary border-darken-1">Total Shipment(s)</th>
										<th class="border-primary border-darken-1">Delivered Shipment(s)</th>
										<th class="border-primary border-darken-1">Returned Shipment(s)</th>
										<th class="border-primary border-darken-1">Adjusted Shipment(s)</th>
										<th class="border-primary border-darken-1">Fintech Shipment(s)</th>
										<th class="border-primary border-darken-1">Total Amount</th>
										<th class="border-primary border-darken-1">Total Charges</th>
										<th class="border-primary border-darken-1">Total GST</th>
										<th class="border-primary border-darken-1">Total WHT</th>
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

							<div class="modal fade" id="fintech_shipments" role="dialog" aria-labelledby="fintech_shipments_title" aria-hidden="true">
								<div class="modal-dialog modal-lg" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h4 class="modal-title" id="fintech_shipments_title">Fintech Shipment(s)</h4>

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

							<div class="modal fade text-left" id="AddRequestModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddRequestModal"
								 aria-hidden="true">
								<div class="modal-dialog modal-lg" role="document">
									<div class="modal-content">
										<div class="modal-header bg-primary white">
											<h4 class="modal-title white">Add Request</h4>
											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">&times;</span>
											</button>
										</div>
										<div class="modal-body text-center">
											<form id="add_request_form" method="post">
												@csrf
												<div class="container">
													<div class="row ml-1">
														<h2 class="heading">Payment ID(s)</h2>
													</div>

													<input type="hidden" id="payment_id">
													<div class="row old_scroll" id="requested_payment_id">

													</div>
													<hr>
													<div class="complaints" id="request_complaints">
														<div class="row justify-content-center">
															<div class="col">
																<fieldset class="form-group">
																	<textarea class="form-control" name="complaint_description" id="complaint_description" rows="5" placeholder="Enter Description Here..."></textarea>
																</fieldset>
															</div>
														</div>
													</div>
													<div class="row justify-content-center">
														<div class="col-3">
															<button id="AddNewRequest" type="submit" class="btn btn-primary btn-block">Submit</button>
														</div>
													</div>
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
	</div>
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>


	<script>
		$(document).ready(function() {
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('cod.finance.payments.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Payment ID');
                            head.push('Shipper');
                            head.push('City');
                            head.push('Phone No(s).');
                            head.push('Address');
                            head.push('Total Shipment(s)');
                            head.push('Delivered Shipment(s)');
                            head.push('Returned Shipment(s)');
                            head.push('Adjusted Shipment(s)');
                            head.push('Fintech Shipment(s)');
                            head.push('Total Amount');
                            head.push('Total Charges');
                            head.push('Total GST');
                            head.push('Total WHT');
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
                                row.push(values.id_padded);
                                row.push(values.shipper);
                                row.push(values.city);
                                row.push(values.phone_numbers);
                                row.push(values.address);
                                row.push(values.total_shipments);
                                row.push(values.delivered_shipments_count);
                                row.push(values.returned_shipments_count);
                                row.push(values.adjusted_shipments_count);
                                row.push(values.count_fintech_shipments);
                                row.push(values.total_amount);
                                row.push(values.total_charges);
                                row.push(values.total_gst);
                                row.push(values.total_wht);
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
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Finance Payments',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
				lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
				pageLength: 50,
				pagingType: 'full_numbers',
				processing: true,
                language: {
                    processing: data_table_loader
                },
				serverSide: true,
				ajax: {
					url: '{{ route('cod.finance.payments.list') }}',
					data: function (d) {
						d.tracking_number = $('#tracking_number_search_form #tracking_number').val();
					}
				},
				rowId: 'id',
				order: [[1, 'desc']],
				columns: [
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data:'id_padded', name: 'done_payments.id', class: 'align-middle text-center id'},
					{data:'shipper', name: 'u.name', class: 'align-middle text-center shipper'},
					{data:'city', name: 'c.name', class: 'align-middle text-center city'},
					{data:'phone_numbers', name: 'phone_numbers', class: 'align-middle text-center phone_numbers'},
					{data:'address', name: 'u.address', class: 'align-middle text-center address'},
					{data:'total_shipments', name: 'done_payments.total_shipments', class: 'align-middle text-center total_shipments'},
					{data:'delivered_shipments', name: 'done_payments.delivered_shipments', class: 'align-middle text-center delivered_shipments'},
					{data:'returned_shipments', name: 'done_payments.returned_shipments', class: 'align-middle text-center returned_shipments'},
					{data:'adjusted_shipments', name: 'done_payments.adjusted_shipments', class: 'align-middle text-center adjusted_shipments'},
					{data:'count_fintech_shipments', name: 'count_fintech_shipments', class: 'align-middle text-center count_fintech_shipments', orderable: false, searchable: false},
					{data:'total_amount', name: 'total_amount', class: 'align-middle text-center total_amount', sortable: false},
					{data:'total_charges', name: 'total_charges', class: 'align-middle text-center total_charges', sortable: false},
					{data:'total_gst', name: 'total_gst', class: 'align-middle text-center total_gst', sortable: false},
					{data:'total_wht', name: 'total_wht', class: 'align-middle text-center total_wht', sortable: false},
					{data:'total_payable', name: 'total_payable', class: 'align-middle text-center total_payable', sortable: false},
					{data:'bank', name: 'bank', class: 'align-middle text-center bank'},
					{data:'return_shipments_average_aging', name: 'return_shipments_average_aging', class: 'align-middle text-center return_shipments_average_aging', sortable: false},
					{data:'reference_number', name: 'done_payments.reference_number', class: 'align-middle text-center reference_number'},
					{data:'done_at', name: 'done_payments.created_at', class: 'align-middle text-center done_at'},
					{data:'company_bank', name: 'company_bank', class: 'align-middle text-center company_bank'},
					{data:'status', name: 'status', class: 'align-middle text-center status'},
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
                    var bank_select = '<select name="bank_select" id="bank_select" class="select2 form-control"></select>';
                    var company_bank_select = '<select name="company_bank_select" id="company_bank_select" class="select2 form-control"></select>';
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="0">Processed</option>' +
                        '<option value="1">Paid</option>' +
                        '<option value="2">Reverted</option>' +
                        '</select>';
					this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.total_amount') || $(header).is('.total_charges') || $(header).is('.total_gst') || $(header).is('.total_payable') || $(header).is('.return_shipments_average_aging') || $(header).is('.action') || $(header).is('.total_wht')) {
							$(td).appendTo($(search));
						}else if($(header).is('.bank')){
                            $(bank_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.company_bank')){
                            $(company_bank_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.status')){
                            $(status_select).appendTo($(search))
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
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
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
                    var data1 = $.map({!! $company_banks !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                    var data1 = $.map({!! $company_banks !!}, function (obj) {
                        obj.text = obj.name;

                        return obj;
                    });

                    $("#company_bank_select").prepend('<option value="" selected></option>').select2({
                        data:data1,
                        placeholder: "Select Bank",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
					this.api().table().columns.adjust();
				}
			});

			$('#tracking_number_search_form').bind('submit', function(e) {
				e.preventDefault();

				length = $('#tracking_number_search_form #tracking_number').val().length;

				if (length == 0 || length >= 12) {
					table.draw();
				}
			});

			$('#tracking_number_search_form #tracking_number').inputmask({
				'alias': 'integer',
				'allowMinus': false,
				'allowPlus': false
			}).bind('input', function() {
				if (this.value.length == 0 || this.value.length >= 6) {
					table.draw();
				}
			});

			$('#datatable tbody').on('click', 'tr td.delivered_shipments button', function() {
				var id = parseInt($(this).parents('tr').attr('id'));

				$('#delivered_shipments .modal-body').html('');

				$.ajax({
					url: '{!! route('cod.finance.payments.delivered_shipments') !!}',
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
					url: '{!! route('cod.finance.payments.returned_shipments') !!}',
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
					url: '{!! route('cod.finance.payments.adjusted_shipments') !!}',
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

			$('#datatable tbody').on('click', 'tr td.count_fintech_shipments button', function() {
				var id = parseInt($(this).parents('tr').attr('id'));

				$('#fintech_shipments .modal-body').html('');

				$.ajax({
					url: '{!! route('cod.finance.payments.fintech_shipments') !!}',
					method: 'POST',
					data: {
						'_token': '{{ csrf_token() }}',
						'id': id
					}
				})
				.done(function(details) {
					if (details) {
						var html = '';
						html += '<table class="table table-sm datatable text-center">';
						html += '<thead><tr><th>S No.</th><th>Tracking Number</th><th>Cod Amount</th><th>Fintech Charges</th><th>Received Cod Amount</th><th>Created Date</th></tr></thead>';
						html += '<tbody>';
						$.each(details.data, function(index, shipment) {
							console.log(shipment);
							html += '<tr>' +
										'<td>' + index + 1 + '</td>' +
										'<td>' + shipment.tracking_number + '</td>' +
										'<td>' + shipment.cod_amount + '</td>' +
										'<td>' + shipment.fintech_charges + '</td>' +
										'<td>' + shipment.received_amount + '</td>' +
										'<td>' + shipment.created_at + '</td>' +
									'</tr>';
						});
						html += '</tbody></table>';

						$('#fintech_shipments .modal-body').html(html);

						$('#fintech_shipments').modal('show');
					}
				});
			});

			$('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
				var id = parseInt($(this).parents('tr').attr('id'));

				if ($(this).hasClass('view_details')) {
					$.ajax({
						url: '{!! route('cod.finance.payments.details_print') !!}',
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
				else if ($(this).hasClass('export_to_excel')) {
					window.open('{!! route('cod.finance.payments.export_to_excel') !!}?id=' + id, '_blank');
				}
                else if ($(this).hasClass('request_add')) {
                    var selected_id = id.toString().padStart(6, 0);
                    var description = 'Payment not received-' + selected_id + '.';
                    $('#complaint_description').val(description);
                    $('#AddRequestModal').modal('show');
                    $('#payment_id').val(id);
                    var html_rows = '<div class="col-4"><span class="mr-1"><i class="la la-angle-right align-bottom"></i><b> '+ selected_id +'</b></span></div>';
                    $('#requested_payment_id').html(html_rows);
                }
			});
            var max_char = 245;
            $('#complaint_description').on('keypress copy paste',function (e) {
                if ($(this).val().length == max_char) {
                    e.preventDefault();
                } else if ($(this).val().length > max_char) {
                    // Maximum exceeded
                    this.value = this.value.substring(0, max_char);
                }
            });
            $('body').on('change','#add_request_form textarea',function() {
                $(this).val($(this).val().trim());
            });
            $( "#add_request_form" ).bind('submit', function (e) {
                e.preventDefault();

                var nature_flag = true;
                var case_nature_id = 1;
                var case_nature_complaint_id = 1;
                var complaint_description = $('#complaint_description').val();
                var requested_payment_id = $('#payment_id').val();
                if(!complaint_description){
                    nature_flag = false;
                    var error = "Please select Description!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
                if(nature_flag){
                    $('#AddNewRequest').attr('disabled',true);
                    $.ajax({
                        url: '{!! route('cod.crm.request.add') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'payment_id': requested_payment_id,
                            'case_nature_id' : case_nature_id,
                            'complaint_id' : case_nature_complaint_id,
                            'description' : complaint_description,
                            'payment_request' : 1
                        }
                    })
                        .done(function(data) {
                            if (data.status) {
                                toastr.success(data.success, 'Success!', {
                                    positionClass: 'toast-bottom-center',
                                    containerId: 'toast-bottom-center'
                                });
                            }
                            else {
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }

                            table.button('.paid').disable();
                            table.button('.reverted').disable();

                            selected_rows = [];

                            table.rows().deselect();

                            table.draw('false');

                            $('#AddRequestModal').modal('hide');
                            $('#AddNewRequest').attr('disabled',false);
                        });

                }
            });
            $('#AddRequestModal').on('hide.bs.modal', function (e) {
                $('#add_request_form')[0].reset();
                $('#complaint_description').val('');
            });
		});
	</script>
@endsection