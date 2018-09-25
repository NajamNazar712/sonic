@extends('admin.layout.master')

@section('title', 'Cargo in Transit')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Cargo in Transit
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							@if (session('role_id') == 1 || in_array(31, session('permissions')))
								<form id="receive_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate" method="POST" action="{{ route('admin.cargo.in_transit.receive') }}">
									{{ csrf_field() }}

									<div class="form-group">
										<input type="text" name="cargo_number" class="form-control cargo_number" placeholder="Cargo No.*" data-rule-required="true" data-msg-required="Cargo No. is required">
									</div>

									<div class="form-group ml-1">
										<button type="submit" name="receive" class="btn btn-primary" value="Receive">Receive</button>
									</div>
								</form>
							@endif

							<div class="text-center">
								<form id="cargo_type_search_form" class="d-inline-block form-inline mb-1 justify-content-center text-left" novalidate="novalidate">
									<div class="form-group">
										<select name="cargo_type" class="select2" id="cargo_type">
											<option value="" selected="selected"></option>
											<option value="0">All</option>
											<option value="1">Normal</option>
											<option value="2">Return</option>
										</select>
									</div>
								</form>

								<form id="tracking_number_search_form" class="d-inline-block form-inline ml-1 mb-1 justify-content-center" novalidate="novalidate">
									<div class="form-group">
										<input type="text" name="tracking_number" class="form-control tracking_number" id="tracking_number" placeholder="Tracking Number">
									</div>
								</form>

								<form id="seal_number_search_form" class="d-inline-block form-inline ml-1 mb-1 justify-content-center" novalidate="novalidate">
									<div class="form-group">
										<input type="text" name="seal_number" class="form-control seal_number" id="seal_number" placeholder="Seal Number">
									</div>
								</form>
							</div>

							<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">Cargo No.</th>
										<th class="border-primary border-darken-1">Origin</th>
										<th class="border-primary border-darken-1">Destination</th>
										<th class="border-primary border-darken-1">Shipment(s)</th>
										<th class="border-primary border-darken-1">Shipping Mode</th>
										<th class="border-primary border-darken-1">Junction 1</th>
										<th class="border-primary border-darken-1">Junction 2</th>
										<th class="border-primary border-darken-1">Transport Mode</th>
										<th class="border-primary border-darken-1">Vendor</th>
										<th class="border-primary border-darken-1">Builty No.</th>
										<th class="border-primary border-darken-1">Transit Datetime</th>
										<th class="border-primary border-darken-1">Transitted By</th>
										<th class="border-primary border-darken-1">Status</th>
										<th class="border-primary border-darken-1"></th>
									</tr>
								</thead>
							</table>

							@if (session('role_id') == 1 || in_array(30, session('permissions')))
								<div class="modal fade" id="receive_at_link" role="dialog" aria-labelledby="receive_at_link_title" aria-hidden="true">
									<div class="modal-dialog modal-lg" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<h4 class="modal-title" id="receive_at_link_title">Receive at Link</h4>

												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">×</span>
												</button>
											</div>
											<div class="modal-body">
												<form id="scan_seal_number_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
													<div class="form-group">
														<input type="text" name="seal_number" class="form-control seal_number" placeholder="Seal Number*" data-rule-required="true" data-msg-required="Seal Number is required">
													</div>

													<div class="form-group ml-1">
														<button type="submit" name="add" class="btn btn-primary" value="Add">Scan</button>
													</div>
												</form>

												<table class="table table-bordered datatable" id="receive_at_link_datatable" style="z-index: 3;">
													<thead>
														<tr role="row" class="bg-primary white">
															<th class="border-primary border-darken-1">S. No.</th>
															<th class="border-primary border-darken-1">Cargo No.</th>
															<th class="border-primary border-darken-1">Origin</th>
															<th class="border-primary border-darken-1">Destination</th>
															<th class="border-primary border-darken-1">Seal No.</th>
														</tr>
													</thead>
												</table>

												<form id="receive_at_link_form" class="form-inline mt-1 mb-1 justify-content-center" novalidate="novalidate">
													<div class="form-group">
														<select name="junction" class="select2 junction" data-rule-required="true" data-msg-required="Junction is required">
														</select>
													</div>

													<div class="w-100"></div>

													<button type="button" class="mr-auto btn btn-secondary" data-dismiss="modal">Close</button>
													<button type="submit" name="receive" class="btn btn-primary receive">Receive</button>
												</form>
											</div>
										</div>
									</div>
								</div>
							@endif

							@if (session('role_id') == 1 || in_array(28, session('permissions')))
								<div class="modal fade" id="add_forwarding_details" role="dialog" aria-labelledby="add_forwarding_details_title" aria-hidden="true">
									<div class="modal-dialog modal-lg" role="document">
										<div class="modal-content">
											<form class="form-horizontal" method="POST" action="{{ route('admin.cargo.in_transit.update') }}" novalidate="novalidate">
												{{ csrf_field() }}

												<input type="hidden" name="cargo_consignment_id" class="cargo_consignment_id">

												<div class="modal-header">
													<h4 class="modal-title" id="add_forwarding_details_title">Forwarding Details</h4>

													<button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">×</span>
													</button>
												</div>
												<div class="modal-body">
													<div class="row">
														<div class="col-12">
															<h4 class="form-section mb-2 text-center">Shipper Information</h4>
														</div>

														<div class="col">
															<div class="form-group">
																<select name="junction_1" class="select2 junction_1" data-rule-required="true" data-msg-required="Junction 1 is required">
																</select>
															</div>
														</div>

														<div class="col">
															<div class="form-group">
																<select name="junction_2" class="select2 junction_2">
																</select>
															</div>
														</div>

														<div class="col-5">
															<div class="form-group input-group">
																<div class="input-group-prepend">
																	<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
																		<span class="la la-calendar-o"></span>
																	</span>
																</div>

																<input type="text" name="expected_arrival_date" class="form-control pickadate bg-primary border-primary white rounded-right expected_arrival_date" placeholder="Expected Arrival Date*" data-rule-required="true" data-msg-required="Expected Arrival Date is required">
															</div>
														</div>

														<div class="w-100"></div>

														<div class="col">
															<div class="form-group">
																<select name="shipping_mode" class="select2 shipping_mode" data-rule-required="true" data-msg-required="Shipping Mode is required">
																</select>
															</div>
														</div>

														<div class="col">
															<div class="form-group">
																<select name="transport_mode" class="select2 transport_mode" data-rule-required="true" data-msg-required="Transport Mode is required">
																</select>
															</div>
														</div>

														<div class="col">
															<div class="form-group">
																<select name="transport_mode_vendor" class="select2 transport_mode_vendor" data-rule-required="true" data-msg-required="Vendor is required">
																</select>
															</div>
														</div>

														<div id="new_vendor" class="col d-none">
															<div class="form-group">
																<input type="text" name="vendor_name" class="form-control vendor_name" placeholder="Vendor Name*" data-rule-required="true" data-msg-required="Vendor Name is required">
															</div>
														</div>

														<div class="w-100"></div>

														<div class="col">
															<div class="form-group">
																<input type="text" name="seal_number" class="form-control rounded-right seal_number" placeholder="Seal Number*" data-rule-required="true" data-msg-required="Seal Number is required">
															</div>
														</div>

														<div class="col">
															<div class="form-group">
																<input type="text" name="builty_number" class="form-control rounded-right builty_number" placeholder="Builty Number*" data-rule-required="true" data-msg-required="Builty Number is required">
															</div>
														</div>

														<div class="w-100"></div>

														<div class="col">
															<div class="form-group">
																<input type="text" name="weight_charges_per_kg" class="form-control rounded-right weight_charges_per_kg" placeholder="Weight Charges / kg*" data-rule-required="true" data-msg-required="Builty Number is required">
															</div>
														</div>

														<div class="col">
															<div class="form-group">
																<input type="text" name="extra_charges" class="form-control rounded-right extra_charges" placeholder="Extra Charges*" data-rule-required="true" data-msg-required="Extra Charges is required">
															</div>
														</div>

														<div class="col">
															<div class="form-group">
																<input type="text" name="total_weight_charges" class="form-control rounded-right total_weight_charges" placeholder="Total Weight Charges*" data-rule-required="true" data-msg-required="Total Weight Charges is required">
															</div>
														</div>

														<div class="col-12">
															<h4 class="form-section mb-2 text-center">Sender Information</h4>
														</div>

														<div class="col">
															<div class="form-group">
																<p class="mt-1 border-bottom border-light text-center font-medium-1 text-bold-600 sender_name"></p>
															</div>
														</div>

														<div class="col">
															<div class="form-group">
																<select name="receiver_id" class="select2 receiver_id">
																</select>
															</div>
														</div>
													</div>
												</div>
												<div class="modal-footer">
													<button type="button" class="mr-auto btn btn-secondary" data-dismiss="modal">Close</button>
													<button type="submit" name="update" class="btn btn-primary">Update</button>
												</div>
											</form>
										</div>
									</div>
								</div>
							@endif

							<div class="modal fade" id="view_forwarding_details" role="dialog" aria-labelledby="view_forwarding_details_title" aria-hidden="true">
								<div class="modal-dialog modal-lg" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h4 class="modal-title" id="view_forwarding_details_title">Forwarding Details</h4>

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
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">

@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			function print(id) {
				$.ajax({
					url: '{!! route('admin.cargo.in_transit.print') !!}',
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

			@if (session('print'))
				print('{{ session('print') }}');
			@endif
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.cargo.in_transit.list') }}',
                        data: {
                            'page': 'all',
                            'cargo_type': $('#cargo_type_search_form #cargo_type').val(),
                    		'tracking_number': $('#tracking_number_search_form #tracking_number').val(),
                    		'seal_number': $('#seal_number_search_form #seal_number').val(),
                        },
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Cargo No.');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Shipment(s)');
                            head.push('Shipping Mode');
                            head.push('Junction 1');
                            head.push('Junction 2');
                            head.push('Transport Mode');
                            head.push('Vendor');
                            head.push('Builty No.');
                            head.push('Transit Datetime');
                            head.push('Transitted By');
                            head.push('Status');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.id);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.shipments);
                                row.push(values.shipping_mode);
                                row.push(values.junction_1);
                                row.push(values.junction_2);
                                row.push(values.transport_mode);
                                row.push(values.vendor);
                                row.push(values.builty_number);
                                row.push(values.transit_at);
                                row.push(values.transitted_by);
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
				@if (session('role_id') == 1 || in_array(30, session('permissions')))

					buttons: [{
						text: 'Update at Link',
						className: 'btn btn-primary receive_at_link',
						action: function (e, dt, node, config) {
							$.ajax({
								url: '{!! route('admin.cargo.in_transit.junctions') !!}',
								method: 'POST',
								data: {
									'_token': '{{ csrf_token() }}'
								}
							})
							.done(function(data) {
								$('#receive_at_link #scan_seal_number_form .seal_number').val('');

								receive_at_link_table.clear().draw();

								cargo_consignment_ids = [];

								$('#receive_at_link #receive_at_link_form button.confirm').prop('disabled', true);

								if ($('#receive_at_link #receive_at_link_form .junction').hasClass('select2-hidden-accessible')) {
									$('#receive_at_link #receive_at_link_form .junction').html('').select2('destroy');
								}

								$.each(data, function(index, junction) {
									$('#receive_at_link #receive_at_link_form .junction').append('<option value="' + junction.id + '">' + junction.name + '</option>');
								});

								$('#receive_at_link #receive_at_link_form .junction').prepend('<option value="" selected="selected"></option>').select2({
									width: '150px',
									placeholder: 'Junction*'
								}).bind('change', function() {
									$(this).valid();
								});
							});

							$('#receive_at_link').modal('show');
						}
					},
                        {
                            extend: 'excel',
                            title: 'Cargo In-transit',
                            className: 'btn btn-primary',
                            text: '<i class="la la-file-excel-o"></i> Excel',
                        }],
				@else
                	buttons:[{
                    extend: 'excel',
                    title: 'Cargo In-transit',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                	}],
				@endif
				scrollX: true, scrollY: '350px',
				lengthMenu: [[25, 50, 100], [25, 50, 100]],
				pageLength: 25,
				pagingType: 'full_numbers',
				processing: true,
				serverSide: true,
				ajax: {
					url: '{{ route('admin.cargo.in_transit.list') }}',
					data: function (d) {
						d.cargo_type = $('#cargo_type_search_form #cargo_type').val();
						d.tracking_number = $('#tracking_number_search_form #tracking_number').val();
						d.seal_number = $('#seal_number_search_form #seal_number').val();
					}
				},
				rowId: 'id',
				order: [[1, 'asc']],
				columns: [
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data: 'id', name: 'cargo_consignments.id', class: 'align-middle cargo_number'},
					{data: 'origin', name: 'oh.name', class: 'align-middle origin'},
					{data: 'destination', name: 'dh.name', class: 'align-middle destination'},
					{data: 'shipments', name: 'cargo_consignments.shipments', class: 'align-middle shipments'},
					{data: 'shipping_mode', name: 'sm.mode', class: 'align-middle shipping_mode'},
					{data: 'junction_1', name: 'jh1.name', class: 'align-middle junction_1'},
					{data: 'junction_2', name: 'jh2.name', class: 'align-middle junction_2'},
					{data: 'transport_mode', name: 'tm.name', class: 'align-middle transport_mode'},
					{data: 'vendor', name: 'tmv.name', class: 'align-middle vendor'},
					{data: 'builty_number', name: 'cargo_consignments.builty_number', class: 'align-middle builty_number'},
					{data: 'transit_at', name: 'cargo_consignments.created_at', class: 'align-middle transit_at'},
					{data: 'transitted_by', name: 'a.name', class: 'align-middle transitted_by'},
					{data: 'status', name: 'ccs.name', class: 'align-middle status'},
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

						if ($(header).is('.serial_number') || $(header).is('.action')) {
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

			var cargo_consignment_ids = [];

			@if (session('role_id') == 1 || in_array(30, session('permissions')))
				var receive_at_link_table = $('#receive_at_link_datatable').DataTable({
					dom: 'tr',
					scrollX: true, scrollY: '350px',
					paging: false,
					columns: [
						{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
						{name: 'cargo_number', class: 'align-middle cargo_number'},
						{name: 'origin', class: 'align-middle origin'},
						{name: 'destination', class: 'align-middle destination'},
						{name: 'seal_number', class: 'align-middle seal_number'}
					],
					rowCallback: function(row, data, index) {
						var info = receive_at_link_table.page.info();

						$('td:eq(0)', row).html(index + 1 + info.page * info.length);
					},
					initComplete: function() {
						this.api().table().columns.adjust();
					}
				});
			@endif

			@if (session('role_id') == 1 || in_array(31, session('permissions')))
				$('#receive_form .cargo_number').inputmask({
					'alias': 'integer',
					'allowMinus': false,
					'allowPlus': false
				});

				$('#receive_form').validate({
					errorClass: 'danger',
					successClass: 'success',
					errorPlacement: function(error, element) {
						error.addClass('w-100').appendTo(element.parents('form'));
					}
				});
			@endif

			$('#cargo_type_search_form #cargo_type').select2({
				width: '125px',
				placeholder: 'Cargo Type'
			}).bind('change', function() {
				table.draw();
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
				if (this.value.length == 0 || this.value.length >= 12) {
					table.draw();
				}
			});

			$('#seal_number_search_form').bind('submit', function(e) {
				e.preventDefault();

				table.draw();
			});

			$('#seal_number_search_form #seal_number').inputmask({
				'alias': 'integer',
				'allowMinus': false,
				'allowPlus': false
			}).bind('input', function() {
				table.draw();
			});

			@if (session('role_id') == 1 || in_array(30, session('permissions')))
				$('#receive_at_link #scan_seal_number_form .seal_number').inputmask({
					'alias': 'integer',
					'allowMinus': false,
					'allowPlus': false
				});
			@endif

			@if (session('role_id') == 1 || in_array(28, session('permissions')))
				$('#add_forwarding_details form .seal_number').inputmask({
					'alias': 'integer',
					'allowMinus': false,
					'allowPlus': false
				});

				$('#add_forwarding_details form .weight_charges_per_kg').inputmask({
					'alias': 'decimal',
					'allowMinus': false,
					'allowPlus': false,
					'digits': 2,
					'min': 0.1,
					'max': 1000
				});

				$('#add_forwarding_details form .extra_charges').inputmask({
					'alias': 'decimal',
					'allowMinus': false,
					'allowPlus': false,
					'digits': 2,
					'min': 0.1,
					'max': 10000
				});

				$('#add_forwarding_details form .total_weight_charges').inputmask({
					'alias': 'decimal',
					'allowMinus': false,
					'allowPlus': false,
					'digits': 2,
					'min': 0.1,
					'max': 1000000
				});
			@endif

			@if (session('role_id') == 1 || in_array(30, session('permissions')))
				$('#receive_at_link #scan_seal_number_form').validate({
					errorClass: 'danger',
					successClass: 'success',
					errorPlacement: function(error, element) {
						error.addClass('w-100').appendTo(element.parents('form'));
					},
					submitHandler: function(form) {
						var seal_number = $(form).find('.seal_number').val();

						if (receive_at_link_table.columns('.seal_number').data().eq(0).indexOf(parseInt(seal_number)) === -1) {
							$.ajax({
								url: '{!! route('admin.cargo.in_transit.details') !!}',
								method: 'POST',
								data: {
									'seal_number': seal_number,
									'_token': '{{ csrf_token() }}'
								}
							})
							.done(function(data) {
								$('#receive_at_link #scan_seal_number_form .seal_number').val('');

								if (data.status == 0) {
									receive_at_link_table.row.add([0, data.details.cargo_number, data.details.origin, data.details.destination, data.details.seal_number]).node().id = data.details.cargo_number;
									receive_at_link_table.draw(false);

									cargo_consignment_ids.push(data.details.cargo_number);

									$('#receive_at_link #receive_at_link_form button.confirm').prop('disabled', false);

									toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
								}
								else {
									toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
								}
							});
						}
						else {
							toastr.error('Cargo has been scanned already', 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
						}

						return false;
					}
				});

				$('#receive_at_link #receive_at_link_form').validate({
					errorClass: 'danger',
					successClass: 'success',
					errorPlacement: function(error, element) {
						error.addClass('w-100').insertAfter(element.parent('.form-group'));
					},
					submitHandler: function(form) {
						var junction = $(form).find('.junction').val();

						$.ajax({
							url: '{!! route('admin.cargo.in_transit.receive_at_link') !!}',
							method: 'POST',
							data: {
								'cargo_consignment_ids': cargo_consignment_ids,
								'junction': junction,
								'_token': '{{ csrf_token() }}'
							}
						})
						.done(function(data) {
							if (data.status == 0) {
								$('#receive_at_link').modal('hide');

								toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
							}
							else {
								toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
							}

							table.draw();
						});

						return false;
					}
				});
			@endif

			@if (session('role_id') == 1 || in_array(29, session('permissions')))
	            $('body').on('click','.launch_dispute',function(){
	                var cargo_id = parseInt($(this).parents('tr').attr('id'));

	                $('#UniversalDisputeModal').modal('show');
	                $('#universal_dispute_id').val(cargo_id);
	            });
	            var select;
	            $('#UniversalDisputeModal').on('shown.bs.modal',function () {
	                var id = $('#universal_dispute_id').val();

	                if(id){
	                    $.ajax({
	                        url: '{!! route('admin.dispute.data') !!}',
	                        method: 'POST',
	                        data:{
	                            '_token': '{{ csrf_token() }}',
								'intransit':'intransit',
	                            'cargo_id':id
	                        }
	                    }).done(function (data) {
	                        if(data.success === 1){
	                            $('#universal_city_select').prepend('<option value="" selected="selected"></option>').select2({
	                                placeholder:'Select a city',
	                                dropdownParent:$('#universal_dispute_form')
	                            });
	                            $.each(data.cities,function(key,value){
	                                var newOption = new Option(value.name, value.id, false, false);
	                                $('#universal_city_select').append(newOption).trigger('select');
	                            });
	                            $('#universal_dispute_type_select').prepend('<option value="" selected="selected"></option>').select2({
	                                placeholder:'Select a Dispute type',
	                                dropdownParent:$('#universal_dispute_form')
	                            });
	                            $.each(data.dispute_types,function(key,value) {
	                                var dispute = new Option(value.type, value.id, false, false);
	                                $('#universal_dispute_type_select').append(dispute).trigger('select');
	                            });

								$('#universal_tracking_number').val(data.cargo_shipments);
	                            select = $('#universal_tracking_number').selectize({
	                                placeholder: 'Tracking Number(s)*',
	                                delimiter: ',',
	                                createOnBlur: true,
	                                persist: false,
	                                plugins: ['remove_button'],
	                                onDropdownOpen: function(dropdown) {
	                                    dropdown.remove();
	                                },
	                                onType: function(str) {
	                                    var regex = /^[0-9,]+$/;

	                                    if (!regex.test(str)) {
	                                        select[0].selectize.setTextboxValue('');
	                                    }
	                                },
	                                create: function(input) {
	                                    if (input.length >= 12 && Math.floor(input) == input && $.isNumeric(input)) {
	                                        return {
	                                            value: input,
	                                            text: input
	                                        }
	                                    }
	                                    else {
	                                        return false;
	                                    }
	                                }
	                            });

	                        }
	                    });
	                }
	            });

				var max_char = 190;
				$('#universal_description').on('keypress copy paste',function (e) {
	                // var comment = $(this).val();
	                // console.log(comment)
	                if ($(this).val().length == max_char) {
	                    e.preventDefault();
	                } else if ($(this).val().length > max_char) {
	                    // Maximum exceeded
	                    this.value = this.value.substring(0, max_char);
	                }
	            });

	            $('#universal_dispute_form').validate({
	                ignore: [],
	                errorClass:"danger",
	                errorPlacement: function(error, element) {
	                    error.addClass('w-100').appendTo(element.parents('.form-group'));
	                },
	                submitHandler: function(form) {

	                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

	                    var city_select = $('#universal_city_select').val();
	                    var dispute_type_select = $('#universal_dispute_type_select').val();
	                    var tracking_number = $('#universal_tracking_number').val();
	                    var description = $('#universal_description').val();
	                    var cargo_id = $('#universal_dispute_id').val();
	                    $.ajax({
	                        url: '{!! route('admin.dispute.create.universal') !!}',
	                        method: 'POST',
	                        data: {
	                            '_token': '{{ csrf_token() }}',
	                            'city_select': city_select,
								'cargo_id': cargo_id,
	                            'dispute_type_select':dispute_type_select,
	                            'tracking_number':tracking_number,
	                            'description':description
	                        }
	                    }).done(function(data){
	                        $('#UniversalDisputeModal').modal('hide');
	                        if (data.invalid !== undefined) {

	                            var message = 'Invalid Tracking Number(s): ' + data.invalid.join(', ');

	                            toastr.error(message, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
	                        }

	                        if(data.success != undefined){
	                            // table.ajax.reload();
	                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
	                            var redirect = '{!! route('admin.dispute.index') !!}';
							window.location = redirect;
	                        }
	                    });

	                }


	            });
	            $('#UniversalDisputeModal').on('hidden.bs.modal',function () {
	                $('#universal_dispute_form')[0].reset();
	                $('#UniversalDisputeCreate').removeAttr('disabled');
	                select[0].selectize.destroy();
	                $('#universal_city_select').empty().trigger('change');
	                $('#universal_dispute_type_select').empty().trigger('change');
	            });
            @endif

			@if (session('role_id') == 1 || in_array(28, session('permissions')))
				$('#add_forwarding_details form').validate({
					errorClass: 'danger',
					successClass: 'success',
					normalizer: function(value) {
						return $.trim(value);
					},
					errorPlacement: function(error, element) {
						error.addClass('w-100').appendTo(element.parent('.form-group'));
					}
				});
			@endif

			var picker = null;

			$('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
				var cargo_consignment_id = parseInt($(this).parents('tr').attr('id'));

				if ($(this).hasClass('print')) {
					print(cargo_consignment_id);
				}
				@if (session('role_id') == 1 || in_array(28, session('permissions')))
					else if ($(this).hasClass('add_forwarding_details')) {
						$.ajax({
							url: '{!! route('admin.cargo.in_transit.forwarding_details') !!}',
							method: 'POST',
							data: {
								'add': 1,
								'cargo_consignment_id': cargo_consignment_id,
								'_token': '{{ csrf_token() }}'
							}
						})
						.done(function(data) {
							$('#add_forwarding_details form .cargo_consignment_id').val(cargo_consignment_id);

							if ($('#add_forwarding_details form .junction_1').hasClass('select2-hidden-accessible') || $('#add_forwarding_details form .junction_2').hasClass('select2-hidden-accessible')) {
								$('#add_forwarding_details form .junction_1').html('').select2('destroy');
								$('#add_forwarding_details form .junction_2').html('').select2('destroy');
							}

							$.each(data.junctions, function(index, junction) {
								$('#add_forwarding_details form .junction_1').append('<option value="' + junction.id + '">' + junction.name + '</option>');
								$('#add_forwarding_details form .junction_2').append('<option value="' + junction.id + '">' + junction.name + '</option>');
							});

							$('#add_forwarding_details form .junction_1').prepend('<option value="" selected="selected"></option>').select2({
								width: '100%',
								placeholder: 'Junction 1*'
							}).bind('change', function() {
								$(this).valid();
							});

							$('#add_forwarding_details form .junction_2').prepend('<option value="" selected="selected"></option>').select2({
								width: '100%',
								placeholder: 'Junction 2',
								allowClear: true
							});

							if (picker) {
								picker.pickadate('picker').clear();
							}
							else {
								picker = $('#add_forwarding_details form .expected_arrival_date').pickadate({
									firstDay: 1,
									clear: '',
									min: '{{ Carbon\Carbon::now() }}',
									selectYears: true,
									selectMonths: true,
									formatSubmit: 'yyyy-mm-dd 00:00:00',
									hiddenSuffix: '_formatted',
									onSet: function(context) {
										$('#add_forwarding_details form .expected_arrival_date').valid();
									}
								});
							}

							if ($('#add_forwarding_details form .shipping_mode').hasClass('select2-hidden-accessible')) {
								$('#add_forwarding_details form .shipping_mode').html('').select2('destroy');
							}

							$.each(data.shipping_modes, function(index, shipping_mode) {
								$('#add_forwarding_details form .shipping_mode').append('<option value="' + shipping_mode.id + '">' + shipping_mode.mode + '</option>');
							});

							$('#add_forwarding_details form .shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
								width: '100%',
								placeholder: 'Shipment Mode*'
							}).bind('change', function() {
								$(this).valid();
							});

							if ($('#add_forwarding_details form .transport_mode').hasClass('select2-hidden-accessible')) {
								$('#add_forwarding_details form .transport_mode').html('').select2('destroy');
								$('#add_forwarding_details form .transport_mode_vendor').html('').select2('destroy');
							}

							$.each(data.transport_modes, function(index, transport_mode) {
								$('#add_forwarding_details form .transport_mode').append('<option value="' + transport_mode.id + '">' + transport_mode.name + '</option>');
							});

							$('#add_forwarding_details form .transport_mode').prepend('<option value="" selected="selected"></option>').select2({
								width: '100%',
								placeholder: 'Transport Mode*'
							}).bind('change', function() {
								$(this).valid();

								$('#add_forwarding_details form .transport_mode_vendor').html('');

								$.each(transport_mode_vendors[this.value], function(index, vendor) {
									var option = new Option(vendor.name, vendor.id, false, false);
									$('#add_forwarding_details form .transport_mode_vendor').append(option);
								});

								var option = new Option('Others', 0, false, false);
								$('#add_forwarding_details form .transport_mode_vendor').append(option);

								$('#add_forwarding_details form .transport_mode_vendor').val(null).trigger('change');
							});

							transport_mode_vendors = data.transport_mode_vendors;

							$('#add_forwarding_details form .transport_mode_vendor').prepend('<option value="" selected="selected"></option>').select2({
								width: '100%',
								placeholder: 'Vendor*'
							}).bind('change', function() {
								if (this.value) {
									$(this).valid();
								}

								if (this.value && this.value == 0) {
									$('#add_forwarding_details #new_vendor').removeClass('d-none');
								}
								else {
									$('#add_forwarding_details #new_vendor').addClass('d-none');

									$('#add_forwarding_details #vendor_name-error').remove();
								}
							});

							if ($('#add_forwarding_details form .receiver_id').hasClass('select2-hidden-accessible')) {
								$('#add_forwarding_details form .receiver_id').html('').select2('destroy');
							}

							$.each(data.receivers, function(index, receiver) {
								$('#add_forwarding_details form .receiver_id').append('<option value="' + receiver.id + '">' + receiver.name + '</option>');
							});

							$('#add_forwarding_details form .receiver_id').prepend('<option value="" selected="selected"></option>').select2({
								width: '100%',
								placeholder: 'Receiver Name',
								allowClear: true
							}).bind('change', function() {
								$(this).valid();
							});

							$('#add_forwarding_details form .junction_1').val(data.cargo_consignment.junction_hub_1_id).trigger('change');
							$('#add_forwarding_details form .junction_2').val(data.cargo_consignment.junction_hub_2_id).trigger('change');
							picker.pickadate('picker').set('select', data.cargo_consignment.expected_arrival_date);
							$('#add_forwarding_details form .shipping_mode').val(data.cargo_consignment.shipping_mode_id).trigger('change');
							$('#add_forwarding_details form .transport_mode').val(data.cargo_consignment.transport_mode_id).trigger('change');
							$('#add_forwarding_details form .transport_mode_vendor').val(data.cargo_consignment.transport_mode_vendor_id).trigger('change');
							$('#add_forwarding_details form .seal_number').val(data.cargo_consignment.seal_number);
							$('#add_forwarding_details form .builty_number').val(data.cargo_consignment.builty_number);
							$('#add_forwarding_details form .sender_name').html(data.cargo_consignment.sender_name);
							$('#add_forwarding_details form .receiver_id').val(data.cargo_consignment.receiver_id).trigger('change');
							$('#add_forwarding_details form .weight_charges_per_kg').val(data.cargo_consignment.weight_charges_per_kg);
							$('#add_forwarding_details form .extra_charges').val(data.cargo_consignment.extra_charges);
							$('#add_forwarding_details form .total_weight_charges').val(data.cargo_consignment.total_weight_charges);

							$('#add_forwarding_details').modal('show');
						});
					}
				@endif
				else if ($(this).hasClass('view_forwarding_details')) {
					$('#view_forwarding_details .modal-body').html('');

					$.ajax({
						url: '{!! route('admin.cargo.in_transit.forwarding_details') !!}',
						method: 'POST',
						data: {
							'add': 0,
							'cargo_consignment_id': cargo_consignment_id,
							'_token': '{{ csrf_token() }}'
						}
					})
					.done(function(data) {
						var cargo_consignment = data.cargo_consignment;

						var details = '<table class="table table-sm table-bordered"><tbody>';

						details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Junction 1</strong></td><td class="align-middle text-center">' + cargo_consignment.junction_hub_1 + '</td></tr>';
						details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Junction 2</strong></td><td class="align-middle text-center">' + ((cargo_consignment.junction_hub_2) ? cargo_consignment.junction_hub_2 : '') + '</td></tr>';
						details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Expected Arrival Date</strong></td><td class="align-middle text-center">' + cargo_consignment.expected_arrival_date + '</td></tr>';
						details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Shipping Mode</strong></td><td class="align-middle text-center">' + cargo_consignment.shipping_mode + '</td></tr>';
						details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Transport Mode</strong></td><td class="align-middle text-center">' + cargo_consignment.transport_mode + '</td></tr>';
						details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Transport Mode Vendor</strong></td><td class="align-middle text-center">' + cargo_consignment.transport_mode_vendor + '</td></tr>';
						details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Seal Number</strong></td><td class="align-middle text-center">' + cargo_consignment.seal_number + '</td></tr>';
						details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Builty Number</strong></td><td class="align-middle text-center">' + ((cargo_consignment.builty_number) ? cargo_consignment.builty_number : '') + '</td></tr>';
						details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Weight Charges / kg</strong></td><td class="align-middle text-center">' + ((cargo_consignment.weight_charges_per_kg) ? cargo_consignment.weight_charges_per_kg : '') + '</td></tr>';
						details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Shipments Weight</strong></td><td class="align-middle text-center">' + cargo_consignment.shipments_weight + '</td></tr>';
						details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Actual Weight</strong></td><td class="align-middle text-center">' + cargo_consignment.actual_weight + '</td></tr>';
						details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Extra Charges</strong></td><td class="align-middle text-center">' + ((cargo_consignment.extra_charges) ? cargo_consignment.extra_charges : '') + '</td></tr>';
						details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Total Weight Charges</strong></td><td class="align-middle text-center">' + ((cargo_consignment.total_weight_charges) ? cargo_consignment.total_weight_charges : '') + '</td></tr>';
						details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Sender Name</strong></td><td class="align-middle text-center">' + cargo_consignment.sender_name + '</td></tr>';
						details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Receiver Name</strong></td><td class="align-middle text-center">' + cargo_consignment.receiver_name + '</td></tr>';

						details += '</tbody></table>';

						$('#view_forwarding_details .modal-body').html(details);

						$('#view_forwarding_details').modal('show');
					});
				}
				@if (session('role_id') == 1 || in_array(31, session('permissions')))
					else if ($(this).hasClass('receive')) {
						$('#receive_form .cargo_number').val(cargo_consignment_id);

						$('#receive_form').submit();
					}
				@endif
			});
		});
	</script>
@endsection