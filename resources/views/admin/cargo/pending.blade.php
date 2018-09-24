@extends('admin.layout.master')

@section('title', 'Pending Shipments for Cargo')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Pending Shipments for Cargo
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<form id="shipment_type_search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
								<div class="form-group">
									<select name="shipment_type" class="select2" id="shipment_type">
										<option value="" selected="selected"></option>
										<option value="0">All</option>
										<option value="1">Normal</option>
										<option value="2">Return</option>
									</select>
								</div>
							</form>

							<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">Tracking Number</th>
										<th class="border-primary border-darken-1">Order ID</th>
										<th class="border-primary border-darken-1">Service Type</th>
										<th class="border-primary border-darken-1">Status</th>
										<th class="border-primary border-darken-1">Origin</th>
										<th class="border-primary border-darken-1">Destination</th>
										<th class="border-primary border-darken-1">Shipper</th>
										<th class="border-primary border-darken-1">Amount</th>
										<th class="border-primary border-darken-1">Shipping Mode</th>
										<th class="border-primary border-darken-1">Booked Datetime</th>
										<th class="border-primary border-darken-1">Arrival Datetime</th>
									</tr>
								</thead>
							</table>
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
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			var table = $('#datatable').DataTable({
				dom: 'ltipr',
				scrollX: true, scrollY: '300px',
				lengthMenu: [[25, 50, 100], [25, 50, 100]],
				pageLength: 25,
				pagingType: 'full_numbers',
				processing: true,
				serverSide: true,
				ajax: {
					url: '{{ route('admin.cargo.pending.list') }}',
					data: function (d) {
						d.shipment_type = $('#shipment_type_search_form #shipment_type').val();
					}
				},
				rowId: 'id',
				order: [[10, 'asc']],
				columns: [
					{data: 'serial_number', orderable: false, searchable: false, name: 'pickup_notes.id', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data: 'tracking_number', name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
					{data: 'order_id', name: 'shipments.order_id', class: 'align-middle order_id'},
					{data: 'service_type', name: 'bt.booking_type', class: 'align-middle service_type'},
					{data: 'status', name: 'ss.name', class: 'align-middle status'},
					{data: 'origin', name: 'oc.name', class: 'align-middle origin'},
					{data: 'destination', name: 'dc.name', class: 'align-middle destination'},
					{data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
					{data: 'amount', name: 'shipments.amount', class: 'align-middle amount'},
					{data: 'shipping_mode', name: 'sm.mode', class: 'align-middle shipping_mode'},
					{data: 'booked_at', name: 'shipments.created_at', class: 'align-middle booked_at'},
					{data: 'arrival_at', name: 'shipments_journey.created_at', class: 'align-middle arrival_at'}
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

						if ($(header).is('.serial_number')) {
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

			$('#shipment_type_search_form #shipment_type').select2({
				width: '150px',
				placeholder: 'Shipment Type'
			}).bind('change', function() {
				table.draw();
			});
		});
	</script>
@endsection