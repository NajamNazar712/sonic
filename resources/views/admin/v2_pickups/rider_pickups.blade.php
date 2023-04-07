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
							<!-- <div class="row justify-content-center">
                                <div class="col-3">
                                    <label class="font-medium-2 font-weight-bold block">Old Rider Pickup</label>
                                    <div class="form-group">
                                        <label for="old_rider_pickup" class="font-medium-2 text-bold-600 mr-1">No</label>
                                        <input type="checkbox" name="old_rider_pickup" id="old_rider_pickup" class=" old_rider_pickup" data-size="sm" data-switchery="true">
                                        <label for="old_rider_pickup" class="font-medium-2 text-bold-600 ml-1">Yes</label>
                                    </div>
                                </div>
                            </div> -->
							<div id="search_form" class="row mb-2 justify-content-center">

		                        <div class="col-3 ">

		                            <div class="form-group input-group ml-1">
		                                <div class="input-group-prepend">
		                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
		                                <span class="la la-calendar-o"></span>
		                            </span>
		                                </div>

		                                <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Date (From)">
		                            </div>
		                        </div>
		                        <div class="col-3 ">
		                            <div class="form-group input-group ml-1">
		                                <div class="input-group-prepend">
		                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
		                                <span class="la la-calendar-o"></span>
		                            </span>
		                                </div>

		                                <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Date (To)">
		                            </div>

		                        </div>

								<div class="col-3">
									<div class="form-group">
										<select name="search_hub" id="search_hub" class="form-control select2" data-rule-required="true" data-msg-required="Hub is required" >
											@foreach($hubs as $hub)
												<option value="{{$hub->id}}">{{$hub->name}}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="col-3">
									<div class="form-group">
										<select name="search_area" id="search_area" class="form-control select2">
											@foreach($areas as $area)
												<option value="{{$area->id}}">{{$area->name}} - {{ $area->hubs->name }}</option>
											@endforeach
										</select>
									</div>
								</div>

			                    <div class="col-2">
			                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
			                    </div>
			                </div>
			                <div class="row justify-content-center d-none">
			                	<div class="col-4">
			                		<table class="table table-bordered text-center">
				                		<thead>
				                			<tr>
				                				<th>Picked</th>
				                				<th>Not Picked</th>
				                			</tr>
				                		</thead>
				                		<tbody>
				                			<tr>
				                				<td id="picked">0</td>
				                				<td id="notpicked">0</td>
				                			</tr>
				                		</tbody>
			                		</table>
			                	</div>
			                	
			                </div>
							<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1">Added At</th>
										<th class="border-primary border-darken-1">Rider</th>
										<th class="border-primary border-darken-1">Shipper</th>
										<th class="border-primary border-darken-1">Address</th>
										<th class="border-primary border-darken-1">City</th>
										<th class="border-primary border-darken-1">Area</th>
										<th class="border-primary border-darken-1">Type</th>
										<th class="border-primary border-darken-1">Updated At</th>
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
										<th class="border-primary border-darken-1">Remark(s)</th>
										<th class="border-primary border-darken-1">Picture</th>
										<th class="border-primary border-darken-1">Audio</th>
										<th class="border-primary border-darken-1">Shipper Signature Via App</th>
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

	<div class="modal fade" id="picture_modal" data-backdrop="static" role="dialog" aria-labelledby="picture_modal" aria-hidden="true">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="picture_modal_title">Picture</h4>

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

	<div class="modal fade" id="signature_modal" data-backdrop="static" role="dialog" aria-labelledby="signature_modal" aria-hidden="true">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="signature_modal_title">Signature</h4>

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

	<div class="modal fade" id="audio_modal" data-backdrop="static" role="dialog" aria-labelledby="audio_modal" aria-hidden="true">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="audio_modal_title">Audio</h4>

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

@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
@endsection

@section('js')
	<script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
	<script>
		$(document).ready(function() {

			$('#search_form #search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_to').pickadate('picker').set('min', $('#search_form #search_date_from').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_form #search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_from').pickadate('picker').set('max', $('#search_form #search_date_to').pickadate('picker').get('select'));
                    }
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
						url: '{{ route('admin.v2_pickups.rider.list') }}',
						data: params,
						success: function (result) {
							head = [];

							head.push('Added At');
							head.push('Rider');
							head.push('Shipper');
							head.push('Address');
							head.push('City');
							head.push('Area');
							head.push('Type');
							head.push('Updated At');
							head.push('Start Location Latitude');
							head.push('Start Location Longitude');
							head.push('Actual Location Latitude');
							head.push('Actual Location Longitude');
							head.push('Distance (Start to Actual)');
							head.push('Current Location Latitude');
							head.push('Current Location Longitude');
							head.push('Distance (Current to Actual)');
							head.push('Shipment(s)');
							head.push('Reason');
							head.push('Remark(s)');
							head.push('Picture');
							head.push('Audio');
							head.push('Shipper Signature Via App');
							head.push('Pickup Note ID');
							head.push('Pickup Request ID');


							$.each(result.data, function(index, values) {
								row = [];

								row.push(values.added_at);
								row.push(values.rider);
								row.push(values.shipper);
								row.push(values.pickup_address);
								row.push(values.city);
								row.push(values.city_area_name);
								row.push(values.pickup_type);
								row.push(values.created_at);
								row.push(values.start_location_latitude);
								row.push(values.start_location_longitude);
								row.push(values.actual_location_latitude);
								row.push(values.actual_location_longitude);
								row.push(values.distance_from_start_to_actual);
								row.push(values.current_location_latitude);
								row.push(values.current_location_longitude);
								row.push(values.distance_from_current_to_actual);
								row.push(values.shipments);
								row.push(values.reason);
								row.push(values.rider_remarks);
								row.push(values.picture_path);
								row.push(values.audio_path);
								row.push(values.signature_via_app);
								row.push(values.pickup_note_id);
								row.push(values.pickup_request_id);

								body.push(row);
							});
						},
						async: false
					});

					return {body: body, header: head};
				}
			} );

			table = $('#datatable').DataTable({
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
			ajax: {
				url: '{{ route('admin.v2_pickups.rider.list') }}',
				//url: tableUrl,
				data: function (d) {
					d.search_date_from = $('input[name="search_date_from_formatted"]').val();
					d.search_date_to = $('input[name="search_date_to_formatted"]').val();
					d.search_hub = $('input[name="search_hub"]').val();
					d.search_area = $('#search_area').val();
				}
			},
			rowId: 'id',
			order: [[7, 'desc']],
			columns: [
				{data: 'added_at', name: 'v2_rider_pickups.added_at', class: 'align-middle added_at'},
				{data: 'rider', name: 'r.name', class: 'align-middle rider'},
				{data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
				{data: 'pickup_address', name: 'usi.pickup_address', class: 'align-middle pickup_address'},
				{data: 'city', name: 'c.name', class: 'align-middle city'},
				{data: 'city_area_name', name: 'cas.name', class: 'align-middle city_area_name'},
				{data: 'pickup_type', name: 'v2_rider_pickups.pickup_type', class: 'align-middle pickup_type'},
				{data: 'created_at', name: 'v2_rider_pickups.created_at', class: 'align-middle created_at'},
				{data: 'start_location_latitude', name: 'v2_rider_pickups.start_location_latitude', class: 'align-middle start_location_latitude'},
				{data: 'start_location_longitude', name: 'v2_rider_pickups.start_location_longitude', class: 'align-middle start_location_longitude'},
				{data: 'actual_location_latitude', name: 'v2_rider_pickups.actual_location_latitude', class: 'align-middle actual_location_latitude'},
				{data: 'actual_location_longitude', name: 'v2_rider_pickups.actual_location_longitude', class: 'align-middle actual_location_longitude'},
				{data: 'distance_from_start_to_actual', name: 'v2_rider_pickups.distance_from_start_to_actual', class: 'align-middle distance_from_start_to_actual'},
				{data: 'current_location_latitude', name: 'v2_rider_pickups.current_location_latitude', class: 'align-middle current_location_latitude'},
				{data: 'current_location_longitude', name: 'v2_rider_pickups.current_location_longitude', class: 'align-middle current_location_longitude'},
				{data: 'distance_from_current_to_actual', name: 'v2_rider_pickups.distance_from_current_to_actual', class: 'align-middle distance_from_current_to_actual'},
				{data: 'shipments', name: 'v2_rider_pickups.shipments', class: 'align-middle shipments'},
				{data: 'reason', name: 'v2_rider_pickups.pickup_not_pick_reason_id', class: 'align-middle reason'},
				{data: 'rider_remarks', name: 'v2_rider_pickups.rider_remarks', class: 'align-middle rider_remarks', orderable: false, searchable: false},
				{data: 'picture_path', name: 'v2_rider_pickups.picture_path', class: 'align-middle picture_path text-center', orderable: false, searchable: false},
				{data: 'audio_path', name: 'v2_rider_pickups.audio_path', class: 'align-middle audio_path text-center', orderable: false, searchable: false},
				{data: 'signature_via_app', name: 'v2_rider_pickups.signature_via_app', class: 'align-middle signature_via_app text-center', orderable: false, searchable: false},
				{data: 'pickup_note_id', name: 'v2_rider_pickups.pickup_note_id', class: 'align-middle pickup_note_id'},
				{data: 'pickup_request_id', name: 'v2_rider_pickups.pickup_request_id', class: 'align-middle pickup_request_id'}
			],
			drawCallback: function (settings) {
				var api = new $.fn.dataTable.Api( settings );
				var data = api.rows( {page:'current'} ).data();
				if(data.length > 0){
					$('#picked').text(data[0].pickup_picked);
					$('#notpicked').text(data[0].pickup_not_picked);
				}
				
			},
			initComplete: function(settings,json) {
				
				var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

				var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
				var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
				var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
				var pickup_type_select = '<select name="pickup_type_select" id="pickup_type_select" class="select2 form-control"></select>';
				var reason_select = '<select name="reason_select" id="reason_select" class="select2 form-control"></select>';

				this.api().columns().every(function(column_id) {
					var column = this;
					var header = column.header();

					if ($(header).is('.serial_number') || $(header).is('.picture_path') || $(header).is('.rider_remarks') || $(header).is('.audio_path')) {
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

			$('#search_filter_btn').on('click',function () {
                table.draw();
            });

			var route = '{!! route('admin.tracking.index') !!}';

			$('#datatable tbody').on('click','tr td.picture_path button',function () {
				var link = $(this).attr('data-link');

				var image = '<img src="' + link + '" style="width: 100%; max-width: 200px;" />';

				$('#picture_modal .modal-body').html(image);

				$('#picture_modal').modal('show');
			});

			$('#datatable tbody').on('click','tr td.signature_via_app button',function () {
				var link = $(this).attr('data-link');

				var image = '<img src="' + link + '" style="width: 100%; max-width: 200px;" />';

				$('#signature_modal .modal-body').html(image);

				$('#signature_modal').modal('show');
			});

			$('#datatable tbody').on('click','tr td.audio_path button',function () {
				var link = $(this).attr('data-link');

				var audio = '<audio controls id="sound"> <source src="' + link + '" type="audio/mp4"  > </audio>';

				$('#audio_modal .modal-body').html(audio);

				$('#audio_modal').modal('show');
			});
			
			$('#audio_modal').on('hide.bs.modal', function (e) {
				$('audio#sound')[0].pause();
				$('audio#sound')[0].currentTime = 0;
			});

			$("#search_hub").prepend('<option value="" selected></option>').select2({
				placeholder: "Select Hub",
				allowClear: true,
				width: '100%',
			});
			 $("#search_area").prepend('<option value="" selected></option>').select2({
				placeholder: "Select Area",
				allowClear: true,
				width: '100%',
			});


	
		});
	</script>
@endsection