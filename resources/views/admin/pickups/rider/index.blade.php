@extends('admin.layout.master')

@section('title', 'Rider Pickups')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Rider Pickups
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1">Added At</th>
										<th class="border-primary border-darken-1">Rider</th>
										<th class="border-primary border-darken-1">Shipper</th>
										<th class="border-primary border-darken-1">Address</th>
										<th class="border-primary border-darken-1">City</th>
										<th class="border-primary border-darken-1">Type</th>
										<th class="border-primary border-darken-1">Start Location Latitude</th>
										<th class="border-primary border-darken-1">Start Location Longitude</th>
										<th class="border-primary border-darken-1">Actual Location Latitude</th>
										<th class="border-primary border-darken-1">Actual Location Longitude</th>
										<th class="border-primary border-darken-1">Distance (Start to Actual)</th>
										<th class="border-primary border-darken-1">Current Location Latitude</th>
										<th class="border-primary border-darken-1">Current Location Longitude</th>
										<th class="border-primary border-darken-1">Distance (Current to Actual)</th>
										<th class="border-primary border-darken-1">Shipment(s)</th>
										<th class="border-primary border-darken-1">Reason</th>
										<th class="border-primary border-darken-1">Picture</th>
										<th class="border-primary border-darken-1">Pickup Note ID</th>
										<th class="border-primary border-darken-1">Pickup Request ID</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>

				<div class="modal fade" id="shipments_modal" data-backdrop="static" role="dialog" aria-labelledby="shipments_modal" aria-hidden="true">
					<div class="modal-dialog modal-sm" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title" id="shipments_modal_title">Shipment(s)</h4>

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
			</div>
		</div>
	</div>
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
@endsection

@section('js')
	<script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			var table = $('#datatable').DataTable({
				dom: '<"d-inline-block"l><"pull-right"B>tipr',
				buttons: [{
					extend: 'excel',
					title: 'Rider Pickups',
					className:'btn btn-primary',
					text: '<i class="la la-file-excel-o"></i> Excel'
				}, 'reset'],
				scrollX: true,
				lengthMenu: [[10, 50, 100, 500, 1000, -1], [10, 50, 100, 500, 1000, 'All']],
				pageLength: 10,
				pagingType: 'full_numbers',
				processing: true,
				language: {
					processing: data_table_loader
				},
				serverSide: true,
				ajax: '{{ route('admin.pickups.rider.list') }}',
				order: [[0, 'desc']],
				columns: [
					{data: 'added_at', name: 'rider_pickups.added_at', class: 'align-middle added_at'},
					{data: 'rider', name: 'r.name', class: 'align-middle rider'},
					{data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
					{data: 'pickup_address', name: 'usi.pickup_address', class: 'align-middle pickup_address'},
					{data: 'city', name: 'c.name', class: 'align-middle city'},
					{data: 'pickup_type', name: 'rider_pickups.pickup_type', class: 'align-middle pickup_type'},
					{data: 'start_location_latitude', name: 'rider_pickups.start_location_latitude', class: 'align-middle start_location_latitude'},
					{data: 'start_location_longitude', name: 'rider_pickups.start_location_longitude', class: 'align-middle start_location_longitude'},
					{data: 'actual_location_latitude', name: 'rider_pickups.actual_location_latitude', class: 'align-middle actual_location_latitude'},
					{data: 'actual_location_longitude', name: 'rider_pickups.actual_location_longitude', class: 'align-middle actual_location_longitude'},
					{data: 'distance_from_start_to_actual', name: 'rider_pickups.distance_from_start_to_actual', class: 'align-middle distance_from_start_to_actual'},
					{data: 'current_location_latitude', name: 'rider_pickups.current_location_latitude', class: 'align-middle current_location_latitude'},
					{data: 'current_location_longitude', name: 'rider_pickups.current_location_longitude', class: 'align-middle current_location_longitude'},
					{data: 'distance_from_current_to_actual', name: 'rider_pickups.distance_from_current_to_actual', class: 'align-middle distance_from_current_to_actual'},
					{data: 'shipments', name: 'shipments', class: 'align-middle shipments', orderable: false, searchable: false},
					{data: 'reason', name: 'rider_pickups.pickup_not_pick_reason_id', class: 'align-middle reason'},
					{data: 'picture_path', name: 'rider_pickups.picture_path', class: 'align-middle picture_path', orderable: false, searchable: false},
					{data: 'pickup_note_id', name: 'rider_pickups.pickup_note_id', class: 'align-middle pickup_note_id'},
					{data: 'pickup_request_id', name: 'rider_pickups.pickup_request_id', class: 'align-middle pickup_request_id'}
				],
				initComplete: function() {
					var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

					var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
					var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
					var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
					var pickup_type_select = '<select name="pickup_type_select" id="pickup_type_select" class="select2 form-control"></select>';
					var reason_select = '<select name="reason_select" id="reason_select" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.serial_number') || $(header).is('.picture_path')) {
							$(td).appendTo($(search));
						}
						else if($(header).is('.pickup_type')) {
							$(pickup_type_select).appendTo($(search)).on('change', function () {
								column.search($(this).val(), false, false, true).draw();
							}).wrap(td);
						}
						else if($(header).is('.reason')) {
							$(reason_select).appendTo($(search)).on('change', function () {
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

					var pickup_not_pick_reasons = $.map({!! $pickup_not_pick_reasons !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;

                        return obj;
                    });

                    $('#pickup_type_select').prepend('<option value="" selected></option>').select2({
                        data: {!! json_encode($pickup_types) !!},
                        placeholder: "Select Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    $('#reason_select').prepend('<option value="" selected></option>').select2({
                        data: pickup_not_pick_reasons,
                        placeholder: "Select Reason",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    this.api().table().columns.adjust();
				}
			});

			var route = '{!! route('admin.tracking.index') !!}';

			$('#datatable tbody').on('click','tr td.shipments button', function () {
				var id = parseInt($(this).parents('tr').attr('id'));

				$('#shipments_modal .modal-body').html('');

				$.ajax({
					url: '{!! route('admin.pickups.rider.shipments') !!}',
					method: 'GET',
					data: {
						'_token': '{{ csrf_token() }}',
						'rider_pickup_id': id
					}
				})
				.done(function(data) {
					if (data.status == 0) {
						var tracking_numbers = '';

						$.each(data.tracking_numbers, function (index, tracking_number) {
							tracking_numbers += '<a href="' + route + '?tracking_number=' + tracking_number + '" target="_blank">'
						});

						$('#shipments_modal .modal-body').html(tracking_numbers);

						$('#shipments_modal').modal('show');
					}
					else {
						toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
					}
				});
			});
		});
	</script>
@endsection