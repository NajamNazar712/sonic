@extends('admin.layout.master')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Pending Pickups
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1"></th>
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">Requested at</th>
										<th class="border-primary border-darken-1">Shipper</th>
										<th class="border-primary border-darken-1">Contact Person</th>
										<th class="border-primary border-darken-1">Contact No(s).</th>
										<th class="border-primary border-darken-1">Address</th>
										<th class="border-primary border-darken-1">City</th>
										<th class="border-primary border-darken-1">Booking(s)</th>
										<th class="border-primary border-darken-1">Pending Booking(s)</th>
										<th class="border-primary border-darken-1">Total Estimated Weight (kg)</th>
										<th class="border-primary border-darken-1">Pickup Type</th>
										<th class="border-primary border-darken-1">Pickup Date</th>
										<th class="border-primary border-darken-1"></th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>

				<div class="modal fade" id="assign_to_rider" role="dialog" aria-labelledby="assign_to_rider_title" aria-hidden="true">
					<div class="modal-dialog modal-sm" role="document">
						<div class="modal-content">
							<form class="form-horizontal">
								{{ csrf_field() }}

								<div class="modal-header">
									<h4 class="modal-title" id="assign_to_rider_title">Assign to Rider</h4>
								</div>
								<div class="modal-body">
									<div class="form-group m-0">
										<select name="rider" class="select2 rider" data-rule-required="true" data-msg-required="Rider is required">
											@foreach($riders as $rider)
												<option value="{{ $rider->id }}">{{ $rider->name }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
									<button type="submit" class="btn btn-primary ml-auto">Assign</button>
								</div>
							</form>
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

	<style>
		table.dataTable {
			font-size: 12px;
		}

		table.dataTable thead tr th {
			padding-left: 0.5em;
			white-space: normal;
			word-wrap: break-word;
		}

		table.dataTable thead tr th:before,
		table.dataTable thead tr th:after {
			height: 20px;
			margin-bottom: -10px;
			bottom: 50% !important;
		}

		table.dataTable tbody tr td {
			padding-left: 0.5em;
			padding-right: 0.5em;
		}

		table.dataTable tbody tr td.select-checkbox:before {
			top: 50%;
			border-color: #666EE8;
		}

		table.dataTable tbody tr.selected td.select-checkbox:after {
			top: 50%;
			text-shadow: none;
		}

		.btn-group .dropdown-menu .dropdown-item {
			white-space: normal;
		}

		#toast-bottom-center.toast-container {
			text-align: center;
		}

		#toast-bottom-center.toast-container .toast {
			display: table;
			width: auto !important;
			text-align: left;
		}
	</style>
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			$('#assign_to_rider').modal({
				backdrop: 'static',
				keyboard: false,
				show: false
			});

			var selected_rows = [];

			var table = $('#datatable').DataTable({
				dom: '<"d-inline-block"l><"pull-right"B>tipr',
				buttons: [{
					text: 'Assign',
					className: 'btn btn-primary assign',
					enabled: false,
					action: function (e, dt, node, config) {
						$('#assign_to_rider .rider').val(null).trigger('change');

						$('#assign_to_rider').modal('show');
					}
				}, {
					text: 'Cancel',
					className: 'btn btn-danger ml-1 cancel',
					enabled: false,
					action: function (e, dt, node, config) {
						swal({
							text: 'Are you sure, you want to Cancel these Pickup(s)?',
							icon: 'warning',
							buttons: {
								cancel: {
									text: 'Close',
									value: null,
									visible: true,
									closeModal: true,
								},
								confirm: {
									text: 'Cancel',
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
									url: '{!! route('admin.pickups.pending.multiple_cancel') !!}',
									method: 'PUT',
									data: {
										'pickup_request_ids': selected_rows,
										'_token': '{{ csrf_token() }}'
									}
								})
								.done(function(data) {
									if (data.status == 0) {
										toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
									}
									else {
										toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
									}

									$.each(selected_rows, function(index, id) {
										table.row($('#datatable tbody tr#' + id)).deselect();
									});

									selected_rows = [];

									table.button(0).disable();
									table.button(1).disable();

									table.ajax.reload();
								});
							}
						});
					}
				}],
				fixedHeader: {
					header: true,
					headerOffset: $('.header-navbar').height()
				},
				select: {
					info: false,
					style: 'multi',
					selector: 'td.select-checkbox',
					className: 'selected bg-primary bg-lighten-5 primary'
				},
				lengthMenu: [[25, 50, 100], [25, 50, 100]],
				pageLength: 25,
				stateSave: true,
				pagingType: 'full_numbers',
				processing: true,
				serverSide: true,
				ajax: '{{ route('admin.pickups.pending.list') }}',
				rowId: 'id',
				order: [[2, 'asc']],
				columns: [
					{data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'serial_number', orderable: false, searchable: false, name: 'pickup_requests.id', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data: 'requested_at', name: 'pickup_requests.created_at', class: 'align-middle requested_at'},
					{data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
					{data: 'contact_person', name: 'usi.poc', class: 'align-middle contact_person'},
					{data: 'contact_number', name: 'usi.phone', class: 'align-middle contact_number'},
					{data: 'address', name: 'usi.pickup_address', class: 'align-middle address'},
					{data: 'city', name: 'ci.name', class: 'align-middle city'},
					{data: 'bookings', name: 'pickup_requests.bookings', class: 'align-middle bookings'},
					{data: 'pending_bookings', name: 'pickup_requests.pending_bookings', class: 'align-middle pending_bookings'},
					{data: 'total_estimated_weight', name: 'pickup_requests.total_estimated_weight', class: 'align-middle total_estimated_weight'},
					{data: 'pickup_type', name: 'pickup_type', class: 'align-middle pickup_type'},
					{data: 'pickup_date', name: 'pickup_requests.pickup_date', class: 'align-middle pickup_date'},
					{data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
				],
				rowCallback: function(row, data, index) {
					var info = table.page.info();

					$('td:eq(1)', row).html(index + 1 + info.page * info.length);

					if ($.inArray(data.id, selected_rows) !== -1) {
						table.row(row).select();
					}
				},
				initComplete: function() {
					var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

					var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
					var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
					var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

					this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.action')) {
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
				}
			});

			$('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
				var id = parseInt($(this).parent('tr').attr('id'));

				var index = $.inArray(id, selected_rows);

				if (index === -1) {
					selected_rows.push(id);
				}
				else {
					selected_rows.splice(index, 1);
				}

				if (selected_rows.length > 0) {
					table.button(0).enable();
					table.button(1).enable();
				}
				else {
					table.button(0).disable();
					table.button(1).disable();
				}
			});

			$('#assign_to_rider .rider').select2({
				width: '100%',
				placeholder: 'Rider*'
			}).bind('change', function() {
				if ($(this).hasClass('danger')) {
					$(this).valid();
				}
			});

			$('#assign_to_rider form').validate({
				errorClass: 'danger',
				successClass: 'success',
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parent('.form-group'));
				},
				submitHandler: function(form) {
					var rider_id = parseInt($(form).find('select.rider').val());

					$.ajax({
						url: '{!! route('admin.pickups.pending.assign') !!}',
						method: 'PUT',
						data: {
							'pickup_request_ids': selected_rows,
							'rider_id': rider_id,
							'_token': '{{ csrf_token() }}'
						}
					})
					.done(function(data) {
						if (data.status == 0) {
							toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
						}
						else {
							toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
						}

						$.each(selected_rows, function(index, id) {
							table.row($('#datatable tbody tr#' + id)).deselect();
						});

						selected_rows = [];

						table.button(0).disable();
						table.button(1).disable();

						table.ajax.reload();

						$('#assign_to_rider').modal('hide');
					});
				}
			});

			$('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.cancel', function() {
				var pickup_request_id = parseInt($(this).parents('tr').attr('id'));

				swal({
					text: 'Are you sure, you want to Cancel this Pickup?',
					icon: 'warning',
					buttons: {
						cancel: {
							text: 'Close',
							value: null,
							visible: true,
							closeModal: true,
						},
						confirm: {
							text: 'Cancel',
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
							url: '{!! route('admin.pickups.pending.cancel') !!}',
							method: 'PUT',
							data: {
								'pickup_request_id': pickup_request_id,
								'_token': '{{ csrf_token() }}'
							}
						})
						.done(function(data) {
							if (data.status == 0) {
								toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
							}
							else {
								toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
							}

							var index = $.inArray(pickup_request_id, selected_rows);

							if (index !== -1) {
								selected_rows.splice(index, 1);
							}

							if (selected_rows.length > 0) {
								table.button(0).enable();
								table.button(1).enable();
							}
							else {
								table.button(0).disable();
								table.button(1).disable();
							}

							table.ajax.reload();
						});
					}
				});
			});
		});
	</script>
@endsection