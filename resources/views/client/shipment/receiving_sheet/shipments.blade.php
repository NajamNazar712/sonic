@extends('client.layout.master')

@section('title', 'Receiving Sheet Shipments')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Receiving Sheet Shipments
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('client.inc.messages')

							<div id="search_form" class="row mb-2 justify-content-center">
								<div class="col-4">
									<div class="form-group input-group">
										<div class="input-group-prepend">
			                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
			                                    <span class="la la-calendar-o"></span>
			                                </span>
										</div>

										<input type="text" name="search_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_from" placeholder="Date (From)">
									</div>
								</div>

			                    <div class="col-4">
			                        <div class="form-group input-group">
			                            <div class="input-group-prepend">
			                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
			                                    <span class="la la-calendar-o"></span>
			                                </span>
			                            </div>

			                            <input type="text" name="search_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_to" placeholder="Date (To)">
			                        </div>
			                    </div>

			                    <div class="col-2">
			                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
			                    </div>
			                </div>

							<table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">Tracking Number</th>
										<th class="border-primary border-darken-1">Receiving Sheet</th>
										<th class="border-primary border-darken-1">Order ID</th>
										<th class="border-primary border-darken-1">Warehouse/Store ID</th>
										<th class="border-primary border-darken-1">Pickup Address</th>
										<th class="border-primary border-darken-1">Origin</th>
										<th class="border-primary border-darken-1">Destination</th>
										<th class="border-primary border-darken-1">Estimated Weight</th>
										<th class="border-primary border-darken-1">Amount</th>
										<th class="border-primary border-darken-1">Booking Date</th>
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
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			var search_to = $('#search_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
				onSet: function(context) {
					if (context.select) {
						$('#search_form #search_from').pickadate('picker').set('max', $('#search_form #search_to').pickadate('picker').get('select'));
					}
				}
            });

			var search_from = $('#search_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
				onSet: function(context) {
					if (context.select) {
						$('#search_form #search_to').pickadate('picker').set('min', $('#search_form #search_from').pickadate('picker').get('select'));
					}
				}
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('cod.shipment.receiving_sheet.shipments.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Tracking Number');
                            head.push('Receiving Sheet');
                            head.push('Order ID');
                            head.push('Warehouse/Store ID');
                            head.push('Pickup Address');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Estimated Weight');
                            head.push('Amount');
                            head.push('Booking Date');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.receiving_sheet);
                                row.push(values.order_id);
                                row.push(values.warehouse_id);
                                row.push(values.pickup_address);
                                row.push(values.origin_city);
                                row.push(values.destination_city);
                                row.push(values.estimated_weight);
                                row.push(values.amount);
                                row.push(values.booking_date);

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
				responsive: true,
				scrollY: '500px',
				buttons: [{
					extend: 'excel',
					title: 'Receiving Sheet',
					className: 'btn btn-primary',
					text: '<i class="la la-file-excel-o"></i> Excel'
				}],
				lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
				pageLength: 50,
				pagingType: 'full_numbers',
				processing: true,
                language: {
                    processing: data_table_loader
                },
				serverSide: true,
				deferLoading: true,
				ajax:{
                    url: '{{ route('cod.shipment.receiving_sheet.shipments.list') }}',
                    data: function (d) {
                        d.search_to = $('input[name="search_to_formatted"]').val();
                        d.search_from = $('input[name="search_from_formatted"]').val();
                    }
                },
				order: [[9, 'desc']],
				searchable: false,
				columns: [
					{data: 'serial_number', orderable: false, searchable: false, name: 'id', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'tracking_number', name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
					{data: 'receiving_sheet', name: 'rs.id', class: 'align-middle receiving_sheet p-1'},
					{data: 'order_id', name: 'shipments.order_id', class: 'align-middle order_id'},
					{data: 'warehouse_id', name: 'gapa.warehouse_id', class: 'align-middle warehouse_id'},
					{data: 'pickup_address', name: 'usi.pickup_address', class: 'align-middle pickup_address'},
					{data: 'origin_city', name: 'oc.name', class: 'align-middle origin_city'},
					{data: 'destination_city', name: 'dc.name', class: 'align-middle destination_city'},
					{data: 'estimated_weight', name: 'shipments.estimated_weight', class: 'align-middle estimated_weight'},
					{data: 'amount', name: 'shipments.amount', class: 'align-middle amount'},
					{data: 'booking_date', name: 'shipments.created_at', class: 'align-middle booking_date'}
				],
				rowCallback: function(row, data, index) {
					var info = table.page.info();

					$('td:eq(0)', row).html(index + 1 + info.page * info.length);
				}
			});

			$('#search_filter_btn').on('click', function() {
                table.draw();
            });
		});
	</script>
@endsection