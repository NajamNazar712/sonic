@extends('client.layout.master')

@section('title', 'Payments Reconcile through Receiving Sheet')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-body">
				<h1 class="mb-1">
					Payments Reconcile through Receiving Sheet
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('client.inc.messages')

							<form action="#" id="receiving_sheet_form">
								<div class="row justify-content-center mb-2">
									<div class="col-3">
										<fieldset>
											<input type="text" class="form-control" placeholder="Receiving Sheet" name="receiving_sheet_id">
										</fieldset>
									</div>
								</div>
							</form>

							<div id="details" class="d-none">
								<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
									<thead>
										<tr role="row" class="bg-primary white">
											<th class="border-primary border-darken-1">S. No.</th>
											<th class="border-primary border-darken-1">Shipment ID</th>
											<th class="border-primary border-darken-1">Tracking No.</th>
											<th class="border-primary border-darken-1">Order ID</th>
											<th class="border-primary border-darken-1">Service Type</th>
											<th class="border-primary border-darken-1">Consignee Name</th>
											<th class="border-primary border-darken-1">Consignee Phone No(s).</th>
											<th class="border-primary border-darken-1">Booking Date</th>
											<th class="border-primary border-darken-1">Destination</th>
											<th class="border-primary border-darken-1">Weight</th>
											<th class="border-primary border-darken-1">Shipment Status</th>
											<th class="border-primary border-darken-1">Payment Status</th>
											<th class="border-primary border-darken-1">Payment ID(s)</th>
										</tr>
									</thead>
								</table>
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
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			$('#receiving_sheet_form input').inputmask({
				'alias': 'integer',
				'allowMinus': false,
				'allowPlus': false
			});

			var table = $('#datatable').DataTable({
				dom: '<"d-inline-block"l><"pull-right"B>tipr',
				scrollX: true, scrollY: '500px',
				buttons: [{
					extend: 'excel',
					title: 'Payments Reconcile through Receiving Sheet',
					className:'btn btn-primary',
					text: '<i class="la la-file-excel-o"></i> Excel'
				}, 'reset'],

				lengthMenu: [[10, 50, 100, 500, 1000, -1], [10, 50, 100, 500, 1000, 'All']],
				pageLength: 10,
				pagingType: 'full_numbers',
				processing: true,
				language: {
					processing: data_table_loader
				},
				serverSide: true,
				ajax: {
					url: '{{ route('cod.finance.payments.reconcile_through_receiving_sheet.list') }}',
					data: function (d) {
						d.receiving_sheet_id = $('#receiving_sheet_form input').val();
					}
				},
				rowId: 'id',
				order: [[1, 'desc']],
				columns: [
					{orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'id', name: 's.id', class: 'align-middle id', visible: false},
					{data: 'tracking_number', name: 's.tracking_number', class: 'align-middle tracking_number'},
					{data: 'order_id', name: 's.order_id', class: 'align-middle order_id'},
					{data: 'service_type', name: 's.booking_type_id', class: 'align-middle service_type'},
					{data: 'consignee_name', name: 's.consignee_name', class: 'align-middle consignee_name'},
					{data: 'consignee_phone', name: 'consignee_phone', class: 'align-middle consignee_phone'},
					{data: 'created_at', name: 's.created_at', class: 'align-middle created_at'},
					{data: 'destination', name: 'c.name', class: 'align-middle destination'},
					{data: 'actual_weight', name: 's.actual_weight', class: 'align-middle actual_weight'},
					{data: 'shipment_status', name: 'sj.consignee_status_id', class: 'align-middle shipment_status'},
					{data: 'shipment_payment_status', name: 's.payment_status_id', class: 'align-middle shipment_payment_status'},
					{data: 'payment_ids', name: 'payment_ids', class: 'align-middle payment_ids'}
				],
				rowCallback: function (row, data, index) {
					var info = table.page.info();
					$('td:eq(0)', row).html(index + 1 + info.page * info.length);
				},
				initComplete: function() {
					var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

					var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
					var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
					var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
					var service_type_select = '<select name="service_type_select" id="service_type_select" class="select2 form-control"></select>';
					var shipment_status_select = '<select name="shipment_status_select" id="shipment_status_select" class="select2 form-control"></select>';
					var shipment_payment_status_select = '<select name="shipment_payment_status_select" id="shipment_payment_status_select" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.serial_number')) {
							$(td).appendTo($(search));
						}
						else if($(header).is('.service_type')) {
							$(service_type_select).appendTo($(search)).on('change', function () {
								column.search($(this).val(), false, false, true).draw();
							}).wrap(td);
						}
						else if($(header).is('.shipment_status')) {
							$(shipment_status_select).appendTo($(search)).on('change', function () {
								column.search($(this).val(), false, false, true).draw();
							}).wrap(td);
						}
						else if($(header).is('.shipment_payment_status')) {
							$(shipment_payment_status_select).appendTo($(search)).on('change', function () {
								column.search($(this).val(), false, false, true).draw();
							}).wrap(td);
						}
						else if(!$(header).is('.id')) {
							var current = $(input).appendTo($(search)).on('change', function() {
								column.search($(this).val(), false, false, true).draw();
							}).wrap(td).after(icon);

							if (column.search()) {
								current.val(column.search());
							}
						}
					});

					var service_types = $.map({!! $service_types !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.booking_type;

                        return obj;
                    });

                    var shipment_statuses = $.map({!! $shipment_statuses !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;

                        return obj;
                    });

                    var shipment_payment_statuses = $.map({!! $shipment_payment_statuses !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;

                        return obj;
                    });

                    $('#service_type_select').prepend('<option value="" selected></option>').select2({
                        data: service_types,
                        placeholder: 'Service Type',
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    $('#shipment_status_select').prepend('<option value="" selected></option>').select2({
                        data: shipment_statuses,
                        placeholder: 'Shipment Status',
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    $('#shipment_payment_status_select').prepend('<option value="" selected></option>').select2({
                        data: shipment_payment_statuses,
                        placeholder: 'Shipment Payment Status',
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    this.api().table().columns.adjust();
				}
			});

			$('#receiving_sheet_form').bind('submit', function(e) {
				$('#details').addClass('d-none');

				e.preventDefault();

				table.draw();

				$('#details').removeClass('d-none');
			});
		});
	</script>
@endsection