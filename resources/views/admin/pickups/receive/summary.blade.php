@extends('admin.layout.master')

@section('title', 'Received Pickup Summary')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Received Pickup Summary
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">Shipper</th>
										<th class="border-primary border-darken-1">Contact Person</th>
										<th class="border-primary border-darken-1">Contact Number</th>
										<th class="border-primary border-darken-1">Address</th>
										<th class="border-primary border-darken-1">City</th>
										<th class="border-primary border-darken-1">Bookings</th>
										<th class="border-primary border-darken-1">Received</th>
										<th class="border-primary border-darken-1">Short Received</th>
										<th class="border-primary border-darken-1">Over Received</th>
										<th class="border-primary border-darken-1">Pickup Type</th>
										<th class="border-primary border-darken-1">Booking Date</th>
										<th class="border-primary border-darken-1">Assigned Date</th>
										<th class="border-primary border-darken-1">Assigned By</th>
										<th class="border-primary border-darken-1">Pickup Note No</th>
										<th class="border-primary border-darken-1"></th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>

				<div class="modal fade" id="short_received_shipments" role="dialog" aria-labelledby="delivered_shipments_title" aria-hidden="true">
					<div class="modal-dialog modal-sm" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title" id="short_received_title">Short Received Shipment(s)</h4>

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

				<div class="modal fade" id="over_received_shipments" role="dialog" aria-labelledby="delivered_shipments_title" aria-hidden="true">
					<div class="modal-dialog modal-sm" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title" id="over_received_title">Over Received Shipment(s)</h4>

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

				<form id="receive_pickup_note_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate" method="POST" action="{{ route('admin.pickups.receive.pickup_note') }}">
					{{ csrf_field() }}

					<input type="hidden" name="type" value="0">

					<input type="hidden" name="pickup_note_no" class="pickup_note_no" value="{{ session('pickup_receive_pickup_note_id') }}">
				</form>

				<!--Shipments popup -->
				<div class="modal fade" id="bookings_modal" data-backdrop="static" role="dialog" aria-labelledby="bookings_modal" aria-hidden="true">
					<div class="modal-dialog modal-sm" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title" id="bookings_modal_title">Booking Shipment(s)</h4>

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
				<!--Shipments popup -->
				<!--Shipments Received popup -->
				<div class="modal fade" id="received_bookings_modal" data-backdrop="static" role="dialog" aria-labelledby="received_bookings_modal" aria-hidden="true">
					<div class="modal-dialog modal-sm" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title" id="received_bookings_modal_title">Received Booking Shipment(s)</h4>

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
				<!--Shipments popup -->

			</div>
		</div>
	</div>
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">

@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			@if (session('print_shipment_ids'))
				$.ajax({
					url: '{!! route('cod.shipment.book.corporate_invoice') !!}',
					method: 'POST',
					data: {
						'ids': {!! json_encode(session('print_shipment_ids')) !!},
						'admin': true,
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
			@endif

			var table = $('#datatable').DataTable({
				dom: 'ltipr',
				scrollX: true, scrollY: '350px',
                paging:false,
                processing: true,
				serverSide: true,
				ajax: {
					url: '{{ route('admin.pickups.receive.summary.list') }}',
					data: function(data) {
						data.pickup_receive_pickup_note_id = {{ session('pickup_receive_pickup_note_id') }};
					}
				},
				rowId: 'id',
				order: [[12, 'desc']],
				columns: [
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
					{data: 'contact_person', name: 'usi.poc', class: 'align-middle contact_person'},
					{data: 'contact_number', name: 'usi.phone', class: 'align-middle contact_number'},
					{data: 'address', name: 'usi.pickup_address', class: 'align-middle address'},
					{data: 'city', name: 'ci.name', class: 'align-middle city'},
					{data: 'bookings_link', name: 'pickup_requests.bookings', class: 'align-middle bookings_link text-center'},
					{data: 'received_link', name: 'pickup_requests.received', class: 'align-middle received_link text-center'},
					{data: 'short_received', name: 'pickup_requests.short_received', class: 'align-middle short_received text-center'},
					{data: 'over_received', name: 'pickup_requests.over_received', class: 'align-middle over_received text-center'},
					{data: 'pickup_type', name: 'pickup_type', class: 'align-middle pickup_type'},
					{data: 'booking_date', name: 'pickup_requests.created_at', class: 'align-middle booking_date'},
					{data: 'assigned_date', name: 'pn.created_at', class: 'align-middle assigned_date'},
					{data: 'assigned_by', name: 'a.name', class: 'align-middle assigned_by'},
					{data: 'pickup_note_no', name: 'pn.id', class: 'align-middle pickup_note_no'},
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
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="0">Light</option>' +
                        '<option value="1">Heavy</option>' +
                        '</select>';
					this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.serial_number') || $(header).is('.action')) {
							$(td).appendTo($(search));
						}else if($(header).is('.pickup_type')){
                        $(drop_select).appendTo($(search))
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
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Pickup Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
					this.api().table().columns.adjust();
				}
			});

			$('#datatable tbody').on('click', 'tr td.short_received button', function() {
				var pickup_request_id = parseInt($(this).parents('tr').attr('id'));

				$('#short_received_shipments .modal-body').html('');

				$.ajax({
					url: '{!! route('admin.pickups.receive.summary.request.short_received') !!}',
					method: 'POST',
					data: {
						'_token': '{{ csrf_token() }}',
						'pickup_request_id': pickup_request_id
					}
				})
				.done(function(data) {
					if (data) {
						var shipments = '';

						if (data.short_received) {
							$.each(data.short_received, function(receiving_sheet_id, tracking_numbers) {
								var receiving_sheet_number = receiving_sheet_id.toString();

								while (receiving_sheet_number.length < 12) {
									receiving_sheet_number = '0' + receiving_sheet_number;
								}

								shipments += receiving_sheet_number + ': ' + tracking_numbers.join(' - ') + '<br/>';
							});
						}

						if (data.voided_short_received) {
							shipments += 'Shipments Voided from Receiving Sheet: ' + data.voided_short_received.join(' - ') + '<br/>';
						}

						$('#short_received_shipments .modal-body').html(shipments);

						$('#short_received_shipments').modal('show');
					}
				});
			});

			$('#datatable tbody').on('click', 'tr td.over_received button', function() {
				var pickup_request_id = parseInt($(this).parents('tr').attr('id'));

				$('#over_received_shipments .modal-body').html('');

				$.ajax({
					url: '{!! route('admin.pickups.receive.summary.request.over_received') !!}',
					method: 'POST',
					data: {
						'_token': '{{ csrf_token() }}',
						'pickup_request_id': pickup_request_id
					}
				})
				.done(function(data) {
					if (data) {
						var shipments = '';

						$.each(data.over_received, function(index, tracking_number) {
							shipments += tracking_number + '<br/>';
						});

						$('#over_received_shipments .modal-body').html(shipments);

						$('#over_received_shipments').modal('show');
					}
				});
			});

			$('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
				var pickup_request_id = parseInt($(this).parents('tr').attr('id'));

				if ($(this).hasClass('receive')) {
					$('#receive_pickup_note_form').submit();
				}
				else if ($(this).hasClass('done')) {
					$.ajax({
						url: '{!! route('admin.pickups.receive.summary.request.over_short_received') !!}',
						method: 'POST',
						data: {
							'pickup_request_id': pickup_request_id,
							'_token': '{{ csrf_token() }}'
						}
					})
					.done(function(data) {
						if (data.status == 0) {
                            var html = '';

							if (data.short_received || data.voided_short_received || data.over_received) {
								if (data.short_received) {
                                    html += 'There are shipments that are short received from:<br/>';

                                    $.each(data.short_received, function (receiving_sheet_id, shipments) {
                                        var receiving_sheet_number = receiving_sheet_id.toString();

                                        while (receiving_sheet_number.length < 12) {
                                            receiving_sheet_number = '0' + receiving_sheet_number;
                                        }

                                        html += receiving_sheet_number + ': ' + shipments.join(' - ') + '<br/>';
                                    });

                                    html += '<br/>';
                                }

                                if (data.voided_short_received) {
									html += 'Shipments Voided from Receiving Sheet: ' + data.voided_short_received.join(' - ') + '<br/>';

									html += '<br/>';
								}

								if (data.over_received) {
									html += 'There are shipments that are over received:<br/>';

									$.each(data.over_received, function (index, shipment) {
										html += shipment + '<br/>';
									});

                                    html += '<br/>';
								}
							}

                        	html += 'Are you sure, you want to mark this Pickup Done?';

							content = document.createElement('div');
							content.innerHTML = html;

							swal({
								content: content,
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
										url: '{!! route('admin.pickups.receive.summary.request.done') !!}',
										method: 'PUT',
										data: {
											'pickup_request_id': pickup_request_id,
											'_token': '{{ csrf_token() }}'
										}
									})
									.done(function(data) {
										if (data.status == 0) {
											toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

											table.draw(false);

											if (data.complete) {
												setTimeout(function() {
													window.location.href = '{{ route('admin.pickups.receive.index') }}';
												}, 2500);
											}
										}
										else {
											toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

											table.draw(false);
										}
									});
								}
							});
						}
						else {
							toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

							table.draw(false);
						}
					});
				}
				else if ($(this).hasClass('not_done')) {
					swal({
						text: 'Are you sure, you want to mark this Pickup Not Done?',
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
								url: '{!! route('admin.pickups.receive.summary.request.not_done') !!}',
								method: 'PUT',
								data: {
									'pickup_request_id': pickup_request_id,
									'_token': '{{ csrf_token() }}'
								}
							})
							.done(function(data) {
								if (data.status == 0) {
									toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

									table.draw(false);

									if (data.complete) {
										setTimeout(function() {
											window.location.href = '{{ route('admin.pickups.receive.index') }}';
										}, 2500);
									}
								}
								else {
									toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

									table.draw(false);
								}
							});
						}
					});
				}
			});
            var route = '{!! route('admin.tracking.index') !!}';
            $('#datatable tbody').on('click','tr td.bookings_link button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#bookings_modal .modal-body').html('');
                $('#bookings_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.pickups.receive.summary.bookings.all') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'pickup_request_id': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var shipments = '';

                            if (data.booked) {
                                $.each(data.booked, function(index, tracking_numbers) {
                                    shipments += '<u><a href='+route+'?tracking_number='+tracking_numbers+' target="_blank">'+tracking_numbers+'</a></u><br>';
                                });
                            }
                            $('#bookings_modal .modal-body').html(shipments);


                        }
                    });

            });
            $('#datatable tbody').on('click','tr td.received_link button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#received_bookings_modal .modal-body').html('');
                $('#received_bookings_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.pickups.receive.summary.received') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'pickup_request_id': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var shipments = '';

                            if (data.booked) {
                                $.each(data.booked, function(index, tracking_numbers) {
                                    shipments += '<u><a href='+route+'?tracking_number='+tracking_numbers+' target="_blank">'+tracking_numbers+'</a></u><br>';
                                });
                            }
                            $('#received_bookings_modal .modal-body').html(shipments);


                        }
                    });

            });
		});
	</script>
@endsection