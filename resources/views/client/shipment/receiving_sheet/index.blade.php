@extends('client.layout.master')

@section('title', 'Receiving Sheet')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Receiving Sheet
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('client.inc.messages')

							<table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1"></th>
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">ID</th>
										<th class="border-primary border-darken-1">Tracking Number</th>
										<th class="border-primary border-darken-1">Shipper</th>
										<th class="border-primary border-darken-1">Order ID</th>
										<th class="border-primary border-darken-1">Service Type</th>
										<th class="border-primary border-darken-1">Status</th>
										<th class="border-primary border-darken-1">Pickup Address</th>
										<th class="border-primary border-darken-1">Origin</th>
										<th class="border-primary border-darken-1">Destination</th>
										<th class="border-primary border-darken-1">Booking Date</th>
										<th class="border-primary border-darken-1">Receiving Sheet</th>
										<th class="border-primary border-darken-1">Amount</th>
										<th class="border-primary border-darken-1"></th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>

				<div class="modal fade" id="add_in_receiving_sheet" role="dialog" aria-labelledby="add_in_receiving_sheet_title" aria-hidden="true">
					<div class="modal-dialog modal-sm" role="document">
						<div class="modal-content">
							<form class="form-horizontal">
								{{ csrf_field() }}

								<div class="modal-header">
									<h4 class="modal-title" id="add_in_receiving_sheet_title">Add in Receiving Sheet</h4>
								</div>
								<div class="modal-body">
									<input type="hidden" name="shipment_id" class="shipment_id">

									<div class="form-group m-0">
										<select name="receiving_sheet" class="select2 receiving_sheet" data-rule-required="true" data-msg-required="Receiving Sheet is required"></select>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
									<button type="submit" class="btn btn-primary ml-auto">Add</button>
								</div>
							</form>
						</div>
					</div>
				</div>

				<div class="modal fade" id="print_receiving_sheet_and_air_waybill" role="dialog" aria-labelledby="print_receiving_sheet_and_air_waybill_title" aria-hidden="true">
					<div class="modal-dialog modal-sm" role="document">
						<div class="modal-content">
							<form class="form-horizontal">
								{{ csrf_field() }}

								<div class="modal-header">
									<h4 class="modal-title" id="print_receiving_sheet_and_air_waybill_title">Print Receiving Sheet and Air Waybill(s)</h4>
								</div>
								<div class="modal-body">
									<div class="form-group m-0">
										<select name="receiving_sheet" class="select2 receiving_sheet" data-rule-required="true" data-msg-required="Receiving Sheet is required"></select>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
									<button type="submit" class="btn btn-primary ml-auto">Print</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade text-left" id="UpdateConsigneeInfoModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="UpdateConsigneeInfoModal"
		 aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header bg-primary white">
					<h4 class="modal-title white">Update Consignee Info</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body text-center">
					<form id="update_consignee_info_form" method="post" action="{{route('cod.shipment.receiving_sheet.cn.update')}}">
						@csrf
						<div class="container">
							<div class="row">
								<h2 class="heading">Tracking Number</h2>
							</div>

							<input type="hidden" name="update_consignee_info_shipment_id" id="update_consignee_info_shipment_id">
							<div class="row old_scroll" id="update_consignee_info_shipment">

							</div>
							<hr>
							<div class="row justify-content-center">
								<div class="col-8">
									<fieldset class="form-group">
										<label for="update_consignee_name"><b>Consignee Name:</b></label>
										<input type="text" name="update_consignee_name" class="form-control" placeholder="Consignee Name*" id="update_consignee_name"  data-rule-required="true" data-msg-required="Consignee Name is required">
									</fieldset>
								</div>
							</div>
							<div class="row justify-content-center">
								<div class="col-8">
									<fieldset class="form-group">
										<label for="update_consignee_address"><b>Consignee Address:</b></label>
										<input type="text" name="update_consignee_address" class="form-control" placeholder="Consignee Address*" id="update_consignee_address" data-rule-required="true" data-msg-required="Consignee Address is required" data-rule-maxlength="255" data-msg-maxlength="Address can be maximum 255 characters">
									</fieldset>
								</div>
							</div>
							<div class="row justify-content-center">
								<div class="col-8">
									<fieldset class="form-group">
										<label for="update_consignee_phone"><b>Consignee Phone:</b></label>
										<input type="text" name="update_consignee_phone" class="form-control phone_number" id="update_consignee_phone" placeholder="Consignee Phone Number*" data-rule-required="true" data-msg-required="Consignee Phone Number is required">
									</fieldset>
								</div>
							</div>
							<div class="row justify-content-center">
								<div class="col-8">
									<fieldset class="form-group">
										<label for="update_order_id"><b>Order ID:</b></label>
										<input type="text" name="update_order_id" class="form-control order_id" id="update_order_id" placeholder="Update Order ID" data-rule-maxlength="100" data-msg-maxlength="Order ID can be maximum 100 characters">
									</fieldset>
								</div>
							</div>
							<div class="row justify-content-center">
								<div class="col-8">
									<fieldset class="form-group">
										<label for="update_order_id"><b>Amount:</b></label>
										<input type="text" name="update_amount" class="form-control amount" id="update_amount" placeholder="Update Amount" data-rule-required="true" data-msg-required="Amount is required">
									</fieldset>
								</div>
							</div>
							<div class="row justify-content-center">
								<div class="col-8">
									<fieldset class="form-group">
										<label for="shipping_mode"><b>Shipping Mode:</b></label>
										<select name="shipping_mode" class="shipping_mode select2" id="shipping_mode" data-rule-required="true" data-msg-required="Mode of Shipping is required">
										</select>
									</fieldset>
								</div>
							</div>
							<div class="row justify-content-center">
								<div class="col-8">
									<fieldset class="form-group">
										<label for="update_pieces"><b>Pieces:</b></label>
										<input type="text" name="update_pieces" class="form-control pieces" id="update_pieces" placeholder="Update Pieces*" data-rule-required="true" data-msg-required="Pieces is required" max="10">
									</fieldset>
								</div>
							</div>
							<div class="row justify-content-center">
								<div class="col-8">
									<fieldset class="form-group">
										<label for="update_special_instructions"><b>Special Instruction:</b></label>
										<textarea class="form-control" name="update_special_instructions" id="update_special_instructions" rows="5" placeholder="Enter Special Instructions Here..."></textarea>
									</fieldset>
								</div>
							</div>
							<div class="row justify-content-center">
								<div class="col-3">
									<button id="Update_consignee_info_button" type="submit" class="btn btn-primary btn-block">Update</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			$('.shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Mode of Shipping*',
			});
			$('.amount').inputmask({
				'alias': 'integer',
				'allowMinus': false,
				'allowPlus': false
			});
			$('.pieces').inputmask({
				'alias': 'integer',
				'min': 1,
				'max': 10,
				'allowMinus': false,
				'allowPlus': false
			});
			$('.phone_number').inputmask({
				'mask': '9999-9999999',
				'clearIncomplete': true
			});
			function print(id) {
				$.ajax({
					url: '{!! route('cod.shipment.receiving_sheet.print') !!}',
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

			$('#add_in_receiving_sheet').modal({
				backdrop: 'static',
				keyboard: false,
				show: false
			});

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('cod.shipment.receiving_sheet.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Tracking Number');
                            head.push('Shipper');
                            head.push('Order ID');
                            head.push('Service Type');
                            head.push('Status');
                            head.push('Pickup Address');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Booking Date');
                            head.push('Receiving Sheet');
                            head.push('Amount');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.user);
                                row.push(values.order_id);
                                row.push(values.service_type);
                                row.push(values.shipment_status);
                                row.push(values.pickup_address);
                                row.push(values.origin_city);
                                row.push(values.destination_city);
                                row.push(values.booking_date);
                                row.push(values.receiving_sheet_no);
                                row.push(values.amount);


                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );

			var selected_rows = [];

			var table = $('.datatable').DataTable({
				dom: '<"d-inline-block"l><"pull-right"B>tipr',
				scrollX: true, scrollY: '500px',
				buttons: [{
					text: 'Create',
					className: 'btn btn-primary create',
					enabled: false,
					action: function (e, dt, node, config) {
						table.button('.create').disable();

						swal({
							title: 'Please Wait!',
							text: 'Your Receiving Sheet is being created!',
							icon: 'info',
							buttons: false,
							closeOnClickOutside: false,
							closeOnEsc: false
						});

						$.ajax({
							url: '{!! route('cod.shipment.receiving_sheet.store') !!}',
							method: 'POST',
							data: {
								'shipment_ids[]': selected_rows,
								'_token': '{{ csrf_token() }}'
							}
						})
						.done(function(data) {
							if (data.status == 0) {
								toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

								print(data.receiving_sheet_id);
							}
							else {
								toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
							}

							table.rows().deselect();

							selected_rows = [];

							table.draw('false');

							swal.close();
						});
					}
				}, {
					text: 'Print Receiving Sheet & Air Waybill(s)',
					className: 'btn btn-primary print_receiving_sheet_and_air_waybill_button',
					action: function (e, dt, node, config) {
						$.ajax({
							url: '{!! route('cod.shipment.receiving_sheet.all') !!}',
							method: 'GET'
						})
						.done(function(data) {
							if (data) {
								var options = [];

								$.each(data, function(index, receiving_sheet) {
									var id = receiving_sheet['id'];
									var text = receiving_sheet['id'].toString();

									while (text.length < 6) {
										text = '0' + text;
									}

									options.push({id: id, text: text});
								});

								if ($('#print_receiving_sheet_and_air_waybill .receiving_sheet').hasClass('select2-hidden-accessible')) {
									$('#print_receiving_sheet_and_air_waybill .receiving_sheet').empty();
									$('#print_receiving_sheet_and_air_waybill .receiving_sheet').select2('destroy');
								}

								$('#print_receiving_sheet_and_air_waybill .receiving_sheet').select2({
									width: '100%',
									placeholder: 'Receiving Sheet*',
									data: options
								}).bind('change', function() {
									if ($(this).hasClass('danger')) {
										$(this).valid();
									}
								}).val(null).trigger('change');

								$('#print_receiving_sheet_and_air_waybill').modal('show');
							}
						});
					}
				},
				{
					extend: 'excel',
					title: 'Receiving Sheet',
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

								id = parseInt(row.id());

								var index = $.inArray(id, selected_rows);

								if (index === -1) {
									selected_rows.push(id);
								}

								table.button('.create').enable();
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
	                            table.button('.create').disable();
	                        }
	                      }
	                    });
					}
				}],
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
				ajax: '{{ route('cod.shipment.receiving_sheet.list') }}',
				rowId: 'id',
				order: [[2, 'desc']],
				columns: [
					{data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'serial_number', orderable: false, searchable: false, name: 'id', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data: 'id', name: 'shipments.id', orderable: true, searchable: false, visible: false},
					{data: 'tracking_number', name: 'tracking_number', class: 'align-middle tracking_number'},
					{data: 'user', name: 'u.name', class: 'align-middle user'},
					{data: 'order_id', name: 'order_id', class: 'align-middle order_id'},
					{data: 'service_type', name: 'bt.id', class: 'align-middle service_type'},
					{data: 'shipment_status', name: 'ss.name', class: 'align-middle shipment_status'},
					{data: 'pickup_address', name: 'usi.pickup_address', class: 'align-middle pickup_address'},
					{data: 'origin_city', name: 'oc.name', class: 'align-middle origin_city'},
					{data: 'destination_city', name: 'dc.name', class: 'align-middle destination_city'},
					{data: 'booking_date', name: 'shipments.created_at', class: 'align-middle booking_date'},
					{data: 'receiving_sheet', name: 'receiving_sheet', class: 'text-center align-middle receiving_sheet p-1'},
					{data: 'amount', name: 'shipments.amount', class: 'text-center align-middle amount'},
					{data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
				],
				rowCallback: function(row, data, index) {
					var info = table.page.info();

					$('td:eq(1)', row).html(index + 1 + info.page * info.length);

					if (!data.receiving_sheet) {
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
                    var service_drop_select = '<select name="service_select" id="service_select" class="select2 form-control"></select>';

					this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.action')) {
							$(td).appendTo($(search));
						}else if($(header).is('.service_type')){
                            $(service_drop_select).appendTo($(search))
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
                    var data2 = $.map({!! $service_type !!}, function (obj) {
                        obj.id = obj.id

                        return obj;
                    });
                    var data2 = $.map({!! $service_type !!}, function (obj) {
                        obj.text = obj.booking_type;

                        return obj;
                    });

                    $("#service_select").prepend('<option value="" selected></option>').select2({
                        data:data2,
                        placeholder: "Select Service",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
					this.api().table().columns.adjust();
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
					table.button('.create').enable();
				}
				else {
					table.button('.create').disable();
				}
			});

			$('#add_in_receiving_sheet form').validate({
				errorClass: 'danger',
				successClass: 'success',
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parent('.form-group'));
				},
				submitHandler: function(form) {
					var shipment_id = parseInt($(form).find('input.shipment_id').val());
					var receiving_sheet_id = parseInt($(form).find('select.receiving_sheet').val());

					$.ajax({
						url: '{!! route('cod.shipment.receiving_sheet.add') !!}',
						method: 'PUT',
						data: {
							'shipment_id': shipment_id,
							'receiving_sheet_id': receiving_sheet_id,
							'_token': '{{ csrf_token() }}'
						}
					})
					.done(function(data) {
						if (data.status == 0) {
							toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
						}
						else {
							toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
						}

						var index = $.inArray(shipment_id, selected_rows);

						if (index !== -1) {
							selected_rows.splice(index, 1);
						}

						if (selected_rows.length > 0) {
							table.button('.create').enable();
						}
						else {
							table.button('.create').disable();
						}

						table.row($('.datatable tbody tr#' + shipment_id)).deselect();

						table.draw('false');

						$('#add_in_receiving_sheet').modal('hide');
					});
				}
			});

			$('.datatable tbody').on('click', 'tr td.receiving_sheet button.print', function() {
				print(parseInt($(this).children('.id').html()));
			});

			$('.datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
				if ($(this).hasClass('add')) {
					var shipment_id = $(this).parents('tr').attr('id');

					$('#add_in_receiving_sheet input.shipment_id').val(shipment_id);

					$.ajax({
						url: '{!! route('cod.shipment.receiving_sheet.all') !!}',
						method: 'GET'
					})
					.done(function(data) {
						if (data) {
							var options = [];

							$.each(data, function(index, receiving_sheet) {
								var id = receiving_sheet['id'];
								var text = receiving_sheet['id'].toString();

								while (text.length < 6) {
									text = '0' + text;
								}

								options.push({id: id, text: text});
							});

							if ($('#add_in_receiving_sheet .receiving_sheet').hasClass('select2-hidden-accessible')) {
								$('#add_in_receiving_sheet .receiving_sheet').empty();
								$('#add_in_receiving_sheet .receiving_sheet').select2('destroy');
							}

							$('#add_in_receiving_sheet .receiving_sheet').select2({
								width: '100%',
								placeholder: 'Receiving Sheet*',
								data: options
							}).bind('change', function() {
								if ($(this).hasClass('danger')) {
									$(this).valid();
								}
							}).val(null).trigger('change');

							$('#add_in_receiving_sheet').modal('show');
						}
					});
				}
				else if($(this).hasClass('edit_cn')){
					var shipment_id = parseInt($(this).parents('tr').attr('id'));
					$.ajax({
						url: '{!! route('cod.shipment.receiving_sheet.cn.info') !!}',
						method: 'POST',
						data: {
							'shipment_id': shipment_id,
							'_token': '{{ csrf_token() }}'
						}
					})
						.done(function(data) {
							if (data.status == 0) {
								var consignee_name = data.shipment_info.consignee_name;
								var consignee_address = data.shipment_info.consignee_address;
								var consignee_phone = data.shipment_info.consignee_phone_number_1;
								var order_id = data.shipment_info.order_id;
								var amount = data.shipment_info.amount;
								var pieces = data.shipment_info.pieces;
								var special_instructions = data.shipment_info.special_instructions;
								var shipping_mode_id = data.shipment_info.shipping_mode_id;
								var tracking_number = data.shipment_info.tracking_number;
								$('#shipping_mode').removeClass('d-none');
								if(data.shipping_modes != null){
									$('.shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
										width: '100%',
										placeholder: 'Mode of Shipping*',
									});
									$.each(data.shipping_modes, function (index, shipping_mode) {
										if(shipping_mode_id === shipping_mode['id']) {
											$('#shipping_mode').append('<option value="' + shipping_mode['id'] + '" selected class="select2">' + shipping_mode['mode'] + '</option>');
										}
										else{
											$('#shipping_mode').append('<option value="' + shipping_mode['id'] + '" class="select2">' + shipping_mode['mode'] + '</option>');
										}
									});
								}
								else{
									$('#shipping_mode').addClass('d-none','select2');
								}

								html_row = '<div class="col-4"><span class="mr-1"><i class="la la-angle-right align-bottom"></i><b> '+ tracking_number +'</b></span></div>';
								$('#update_consignee_info_shipment_id').val(shipment_id);
								$('#update_consignee_name').val(consignee_name);
								$('#update_consignee_address').val(consignee_address);
								$('#update_consignee_phone').val(consignee_phone);
								$('#update_order_id').val(order_id);
								$('#update_amount').val(amount);
								$('#update_pieces').val(pieces);
								$('#update_special_instructions').val(special_instructions);
								$('#update_consignee_info_shipment').html(html_row);
								$('#UpdateConsigneeInfoModal').modal('show');
							}
							else {
								toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
							}
						});
				}
				else {
					var shipment_id = $(this).parents('tr').attr('id');
					var receiving_sheet = $(this).parents('tr').find('td.receiving_sheet .id').html();
					var tracking_number = $(this).parents('tr').children('td.tracking_number').html();

					swal({
						title: tracking_number,
						text: 'Are you sure, you want to Void this Shipment?',
						icon: 'warning',
						buttons: {
							cancel: {
								text: 'Close',
								value: null,
								visible: true,
								closeModal: true,
							},
							confirm: {
								text: 'Void',
								value: true,
								visible: true,
								closeModal: true
							}
						},
						closeOnClickOutside: false,
						closeOnEsc: false,
						dangerMode: true
					}).then(function(confirm) {
						if (confirm) {
							$.ajax({
								url: '{!! route('cod.shipment.receiving_sheet.void') !!}',
								method: 'PUT',
								data: {
									'shipment_id': shipment_id,
									'receiving_sheet_id': parseInt(receiving_sheet),
									'_token': '{{ csrf_token() }}'
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
					});
				}
			});

			var validator_consignee_info_form = $( "#update_consignee_info_form" ).validate({
				errorClass: 'danger',
				successClass: 'success',
				normalizer: function(value) {
					return $.trim(value);
				},
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parent('.form-group'));
				},
				submitHandler: function(form) {
					$(form).find('button[type=submit]').attr('disabled', 'disabled');

					swal({
						title: 'Please Wait!',
						text: 'Your shipment is being updated!',
						icon: 'info',
						buttons: false,
						closeOnClickOutside: false,
						closeOnEsc: false
					});

					form.submit();
				}
			});

			$('#UpdateConsigneeInfoModal').on('hide.bs.modal', function (e) {
				// console.log($('#update_consignee_info_form')[0]);
				validator_consignee_info_form.resetForm();
				if ($('#shipping_mode').hasClass("select2-hidden-accessible")) {
					$('#shipping_mode').html('').select2('destroy');
				}
				$('#update_consignee_info_form')[0].reset();
				$('#update_consignee_info_shipment_id').val('');
				$('#update_consignee_name').val('');
				$('#update_consignee_address').val('');
				$('#update_consignee_phone').val('');
				$('#update_special_instructions').val('');
				$('#update_order_id').val('');
				$('#update_amount').val('');
				$('#update_pieces').val('');
			});

			$('#print_receiving_sheet_and_air_waybill form').validate({
				errorClass: 'danger',
				successClass: 'success',
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parent('.form-group'));
				},
				submitHandler: function(form) {
					var receiving_sheet_id = parseInt($(form).find('select.receiving_sheet').val());

					$.ajax({
						url: '{!! route('cod.shipment.receiving_sheet.print_receiving_sheet_and_air_waybill') !!}',
						method: 'POST',
						data: {
							'receiving_sheet_id': receiving_sheet_id,
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

						$('#print_receiving_sheet_and_air_waybill').modal('hide');
					});
				}
			});
		});
	</script>
@endsection