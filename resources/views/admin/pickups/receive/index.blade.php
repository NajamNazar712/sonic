@extends('admin.layout.master')

@section('title', 'Receive Pickups')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Receive Pickups
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							@if (session('role_id') == 1 || in_array(24, session('permissions')))
								<form id="receive_pickup_note_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate" method="POST" action="{{ route('admin.pickups.receive.pickup_note') }}">
									{{ csrf_field() }}

									<div class="form-group">
										<input type="text" name="pickup_note_no" class="form-control pickup_note_no" placeholder="Pickup Note No.*" data-rule-required="true" data-msg-required="Pickup Note No. is required">
									</div>

									<div class="form-group ml-1">
										<button type="submit" name="receive" class="btn btn-primary" value="Receive">Receive</button>
									</div>
								</form>
							@endif

							<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">Rider</th>
										<th class="border-primary border-darken-1">Rider Type</th>
										<th class="border-primary border-darken-1">Route</th>
										<th class="border-primary border-darken-1">City</th>
										<th class="border-primary border-darken-1">Pickup(s)</th>
										<th class="border-primary border-darken-1">Booking(s)</th>
										<th class="border-primary border-darken-1">Pickup Type</th>
										<th class="border-primary border-darken-1">Assigned Date</th>
										<th class="border-primary border-darken-1">Assigned By</th>
										<th class="border-primary border-darken-1">Pickup Note No.</th>
										<th class="border-primary border-darken-1">Status</th>
										<th class="border-primary border-darken-1"></th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>

				<div class="modal fade" id="view_details" role="dialog" aria-labelledby="view_details_title" aria-hidden="true">
					<div class="modal-dialog modal-lg" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title" id="view_details_title">Details</h4>

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
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			function print(id) {
				$.ajax({
					url: '{!! route('admin.pickups.assigned.print') !!}',
					method: 'POST',
					data: {
						'ids': [id],
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

			var table = $('#datatable').DataTable({
				dom: 'ltipr',
				scrollX: true,
				lengthMenu: [[25, 50, 100], [25, 50, 100]],
				pageLength: 25,
				pagingType: 'full_numbers',
				processing: true,
				serverSide: true,
				ajax: '{{ route('admin.pickups.receive.list') }}',
				rowId: 'id',
				order: [[4, 'asc']],
				columns: [
					{data: 'serial_number', orderable: false, searchable: false, name: 'pickup_notes.id', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data: 'rider', name: 'rider', class: 'align-middle rider'},
					{data: 'rider_type', name: 'rc.name', class: 'align-middle rider_type'},
					{data: 'route', name: 'route', class: 'align-middle route'},
					{data: 'city', name: 'c.name', class: 'align-middle city'},
					{data: 'pickups', name: 'pickup_notes.pickups', class: 'align-middle pickups'},
					{data: 'bookings', name: 'pickup_notes.bookings', class: 'align-middle bookings'},
					{data: 'pickup_type', name: 'pickup_notes.pickup_type', class: 'align-middle pickup_type'},
					{data: 'assigned_date', name: 'pickup_notes.created_at', class: 'align-middle assigned_date'},
					{data: 'assigned_by', name: 'a.name', class: 'align-middle assigned_by'},
					{data: 'pickup_note_no', name: 'pickup_notes.id', class: 'align-middle pickup_note_no'},
					{data: 'status', name: 'status', class: 'align-middle status'},
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

			@if (session('role_id') == 1 || in_array(24, session('permissions')))
				$('#receive_pickup_note_form input.pickup_note_no').inputmask({
					'alias': 'integer',
					'allowMinus': false,
					'allowPlus': false
				});

				$('#receive_pickup_note_form').validate({
					errorClass: 'danger',
					successClass: 'success',
					errorPlacement: function(error, element) {
						error.addClass('w-100').appendTo(element.parents('form'));
					}
				});

				$('#datatable tbody').on('click', 'tr td.pickup_note_no button.print', function() {
					var pickup_note_id = parseInt($(this).parents('tr').attr('id'));

					print(pickup_note_id);
				});

				$('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
					var pickup_note_id = parseInt($(this).parents('tr').attr('id'));

					if ($(this).hasClass('receive')) {
						$('#receive_pickup_note_form input.pickup_note_no').val(pickup_note_id);

						$('#receive_pickup_note_form').submit();
					}
					else if ($(this).hasClass('summary')) {
						$('#receive_pickup_note_form input.pickup_note_no').val(pickup_note_id);

						$('#receive_pickup_note_form').submit();
					}
				});
			@endif
		});
	</script>
@endsection