@extends('admin.layout.master')

@section('title', 'Assigned Pickups')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Assigned Pickups
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
										<th class="border-primary border-darken-1">Rider</th>
										<th class="border-primary border-darken-1">Rider Type</th>
										<th class="border-primary border-darken-1">Route</th>
										<th class="border-primary border-darken-1">City</th>
										<th class="border-primary border-darken-1">Pickup(s)</th>
										<th class="border-primary border-darken-1">Booking(s)</th>
										<th class="border-primary border-darken-1">Total Estimated Weight (kg)</th>
										<th class="border-primary border-darken-1">Pickup Type</th>
										<th class="border-primary border-darken-1">Assigned Date</th>
										<th class="border-primary border-darken-1">Assigned By</th>
										<th class="border-primary border-darken-1">Pickup Note No.</th>
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
	<style type="text/css">
		a.btn.btn-secondary{
			border-radius: 20px;
			background: #64a0d2;
		}
	</style>
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			function print(ids) {
				$.ajax({
					url: '{!! route('admin.pickups.assigned.print') !!}',
					method: 'POST',
					data: {
						'ids': ids,
						'dispatch': 1,
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

					table.draw('false');
				});
			}

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.pickups.assigned.list') }}',
                        data: {
                            'page': 'all',
                        },
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Rider');
                            head.push('Rider Type');
                            head.push('Route');
                            head.push('City');
                            head.push('Pickup(s)');
                            head.push('Booking(s)');
                            head.push('Total Estimated Weight (kg)');
                            head.push('Pickup Type');
                            head.push('Assigned Date');
                            head.push('Assigned By');
                            head.push('Pickup Note No.');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.rider_name+" | "+values.rider_phone);
                                row.push(values.rider_type);
                                row.push(values.route);
                                row.push(values.city);
                                row.push(values.pickups);
                                row.push(values.bookings);
                                row.push(values.total_estimated_weight);
                                row.push(values.pickup_type);
                                row.push(values.assigned_date);
                                row.push(values.assigned_by);
                                row.push(values.pickup_note_no);


                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );

			var selected_rows = [];

			var table = $('#datatable').DataTable({
				@if (session('role_id') == 1 || in_array(22, session('permissions')))
					dom: '<"d-inline-block"l><"pull-right"B>tipr',
					buttons: [{
						text: '<i class="la la-print"></i> Print',
						className: 'btn btn-primary print',
						enabled: false,
						action: function (e, dt, node, config) {
							swal({
								text: 'Are you sure, you want to Dispatch these Pickup Notes?',
								icon: 'warning',
								buttons: {
									cancel: {
										text: 'No',
										value: null,
										visible: true,
										closeModal: true,
									},
									confirm: {
										text: 'Yes',
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
									print(selected_rows);

									$.each(selected_rows, function(index, id) {
										table.row($('#datatable tbody tr#' + id)).deselect();
									});

									selected_rows = [];

									table.button('.print').disable();

									table.draw('false');
								}
							});
						}
					},
                        {
                            extend: 'excel',
                            title: 'Assigned Pickups',
                            text: '<i class="la la-file-excel-o"></i> Excel',
                        }],
				@else
	                dom: 'ltipr',
	            @endif
	            scrollX: true, scrollY: '300px',
				select: {
					info: false,
					style: 'multi',
					selector: 'td.select-checkbox',
					className: 'selected bg-primary bg-lighten-5 primary'
				},
				lengthMenu: [[25, 50, 100], [25, 50, 100]],
				pageLength: 25,
				pagingType: 'full_numbers',
				processing: true,
				serverSide: true,
				ajax: '{{ route('admin.pickups.assigned.list') }}',
				rowId: 'id',
				order: [[6, 'asc']],
				columns: [
					{data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'serial_number', orderable: false, searchable: false, name: 'pickup_notes.id', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data: 'rider', name: 'rider', class: 'align-middle rider'},
					{data: 'rider_type', name: 'rc.name', class: 'align-middle rider_type'},
					{data: 'route', name: 'route', class: 'align-middle route'},
					{data: 'city', name: 'c.name', class: 'align-middle city'},
					{data: 'pickups', name: 'pickup_notes.pickups', class: 'align-middle pickups'},
					{data: 'bookings', name: 'pickup_notes.bookings', class: 'align-middle bookings'},
					{data: 'total_estimated_weight', name: 'pickup_notes.total_estimated_weight', class: 'align-middle total_estimated_weight'},
					{data: 'pickup_type', name: 'pickup_notes.pickup_type', class: 'align-middle pickup_type'},
					{data: 'assigned_date', name: 'pickup_notes.created_at', class: 'align-middle assigned_date'},
					{data: 'assigned_by', name: 'a.name', class: 'align-middle assigned_by'},
					{data: 'pickup_note_no', name: 'pickup_notes.id', class: 'align-middle pickup_note_no'},
					{data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
				],
				rowCallback: function(row, data, index) {
					var info = table.page.info();

					$('td:eq(1)', row).html(index + 1 + info.page * info.length);

					$('td:eq(0)', row).addClass('select-checkbox');

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

					this.api().table().columns.adjust();
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
					table.button('.print').enable();
				}
				else {
					table.button('.print').disable();
				}
			});

			$('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
				var pickup_note_id = parseInt($(this).parents('tr').attr('id'));

				if ($(this).hasClass('cancel')) {
					swal({
						text: 'Are you sure, you want to Cancel this Pickup?',
						icon: 'warning',
						buttons: {
							cancel: {
								text: 'No',
								value: null,
								visible: true,
								closeModal: true,
							},
							confirm: {
								text: 'Yes',
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
								url: '{!! route('admin.pickups.assigned.cancel') !!}',
								method: 'PUT',
								data: {
									'pickup_note_id': pickup_note_id,
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

								var index = $.inArray(pickup_note_id, selected_rows);

								if (index !== -1) {
									selected_rows.splice(index, 1);
								}

								if (selected_rows.length > 0) {
									table.button('.print').enable();
								}
								else {
									table.button('.print').disable();
								}

								table.draw('false');
							});
						}
					});
				}
				else if ($(this).hasClass('view_details')) {
					$('#view_details .modal-body').html();

					$.ajax({
						url: '{!! route('admin.pickups.assigned.view_details') !!}',
						method: 'POST',
						data: {
							'pickup_note_id': pickup_note_id,
							'_token': '{{ csrf_token() }}'
						}
					})
					.done(function(data) {
						if (data) {
							var pickup_requests = '';

							$.each(data, function(index, details) {
								var shipper = '<tr><td class="bg-primary white border-primary border-darken-1"><strong>Shipper</strong></td><td>' + details.shipper + '</td></tr>';
								var contact_person = '<tr><td class="bg-primary white border-primary border-darken-1"><strong>Contact Person</strong></td><td>' + details.contact_person + '</td></tr>';
								var contact_number = '<tr><td class="bg-primary white border-primary border-darken-1"><strong>Contact Number</strong></td><td>' + details.contact_number + '</td></tr>';
								var address = '<tr><td class="bg-primary white border-primary border-darken-1"><strong>Address</strong></td><td>' + details.address + '</td></tr>';
								var bookings = '<tr><td class="bg-primary white border-primary border-darken-1"><strong>Bookings</strong></td><td>' + details.bookings + '</td></tr>';
								var total_estimated_weight = '<tr><td class="bg-primary white border-primary border-darken-1"><strong>Total Estimated Weight</strong></td><td>' + details.total_estimated_weight + 'kg</td></tr>';
								var pickup_type = '<tr><td class="bg-primary white border-primary border-darken-1"><strong>Pickup Type</strong></td><td>' + details.pickup_type + '</td></tr>';

								pickup_requests += '<table class="table table-sm table-bordered mb-1"><tbody>' + shipper + contact_person + contact_number + address + bookings + total_estimated_weight + pickup_type + '</tbody></table>';
							});

							$('#view_details .modal-body').html(pickup_requests);

							$('#view_details').modal('show');
						}
					});
				}
				else if ($(this).hasClass('print_pickup_note')) {
					swal({
						text: 'Are you sure, you want to Dispatch this Pickup Note?',
						icon: 'warning',
						buttons: {
							cancel: {
								text: 'No',
								value: null,
								visible: true,
								closeModal: true,
							},
							confirm: {
								text: 'Yes',
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
							print([pickup_note_id]);

							var index = $.inArray(pickup_note_id, selected_rows);

							if (index !== -1) {
								selected_rows.splice(index, 1);
							}

							if (selected_rows.length > 0) {
								table.button('.print').enable();
							}
							else {
								table.button('.print').disable();
							}

							table.draw('false');
						}
					});
				}
				else if ($(this).hasClass('sms_rider')) {
					swal({
						text: 'Are you sure, you want to Dispatch this Pickup Note?',
						icon: 'warning',
						buttons: {
							cancel: {
								text: 'No',
								value: null,
								visible: true,
								closeModal: true,
							},
							confirm: {
								text: 'Yes',
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
								url: '{!! route('admin.pickups.assigned.sms') !!}',
								method: 'POST',
								data: {
									'pickup_note_id': pickup_note_id,
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

								var index = $.inArray(pickup_note_id, selected_rows);

								if (index !== -1) {
									selected_rows.splice(index, 1);
								}

								if (selected_rows.length > 0) {
									table.button('.print').enable();
								}
								else {
									table.button('.print').disable();
								}

								table.draw('false');
							});
						}
					});
				}
			});
		});
	</script>
@endsection