@extends('admin.layout.master')

@section('title', 'Rider Pickup Action Logs')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Rider Pickup Action Logs
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1">Logged At</th>
										<th class="border-primary border-darken-1">Rider</th>
										<th class="border-primary border-darken-1">Shipper</th>
										<th class="border-primary border-darken-1">Address</th>
										<th class="border-primary border-darken-1">City</th>
										<th class="border-primary border-darken-1">Type</th>
										<th class="border-primary border-darken-1">Pickup Note ID</th>
										<th class="border-primary border-darken-1">Pickup Request ID</th>
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
                    title: 'Rider Pickup Action Logs',
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
				ajax: '{{ route('admin.pickups.rider.action_log.list') }}',
				order: [[0, 'desc']],
				columns: [
					{data: 'logged_at', name: 'rider_pickup_action_logs.logged_at', class: 'align-middle logged_at'},
					{data: 'rider', name: 'r.name', class: 'align-middle rider'},
					{data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
					{data: 'pickup_address', name: 'usi.pickup_address', class: 'align-middle pickup_address'},
					{data: 'city', name: 'c.name', class: 'align-middle city'},
					{data: 'type', name: 'rider_pickup_action_logs.type_id', class: 'align-middle type'},
					{data: 'pickup_note_id', name: 'rider_pickup_action_logs.pickup_note_id', class: 'align-middle pickup_note_id'},
					{data: 'pickup_request_id', name: 'rider_pickup_action_logs.pickup_request_id', class: 'align-middle pickup_request_id'}
				],
				initComplete: function() {
					var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

					var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
					var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
					var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
					var type_select = '<select name="type_select" id="type_select" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.picture_path')) {
							$(td).appendTo($(search));
						}
						else if($(header).is('.type')) {
							$(type_select).appendTo($(search)).on('change', function () {
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

					var pickup_actions = $.map({!! $pickup_actions !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;

                        return obj;
                    });

                    $('#type_select').prepend('<option value="" selected></option>').select2({
                        data: pickup_actions,
                        placeholder: "Select Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    this.api().table().columns.adjust();
				}
			});
		});
	</script>
@endsection