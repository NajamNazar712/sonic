@extends('admin.layout.master')

@section('title', 'Pending Pickups')

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
										<th class="border-primary border-darken-1">Pickup Request ID</th>
										<th class="border-primary border-darken-1">Pickup Date</th>
										<th class="border-primary border-darken-1">Requested Datetime</th>
										<th class="border-primary border-darken-1">Shipper</th>
										<th class="border-primary border-darken-1">Contact Person</th>
										<th class="border-primary border-darken-1">Contact No(s).</th>
										<th class="border-primary border-darken-1">Address</th>
										<th class="border-primary border-darken-1">City</th>
										<th class="border-primary border-darken-1">Booking(s)</th>
										<th class="border-primary border-darken-1">Pending Booking(s)</th>
										<th class="border-primary border-darken-1">Total Estimated Weight (kg)</th>
										<th class="border-primary border-darken-1">Pickup Type</th>
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
				<!--Shipments popup pending booking-->
				<div class="modal fade" id="pending_bookings_modal" data-backdrop="static" role="dialog" aria-labelledby="pending_bookings_modal" aria-hidden="true">
					<div class="modal-dialog modal-sm" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title" id="pending_bookings_modal_title">Pending Booking Shipment(s)</h4>

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
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

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
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.pickups.pending.list') }}',
                        data: {
                            'page': 'all',
                        },
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Pickup Request ID');
                            head.push('Pickup Date');
                            head.push('Requested Datetime');
                            head.push('Shipper');
                            head.push('Contact Person');
                            head.push('Contact No(s).');
                            head.push('Address');
                            head.push('City');
                            head.push('Booking(s)');
                            head.push('Pending Booking(s)');
                            head.push('Total Estimated Weight (kg)');
                            head.push('Pickup Type');
                            head.push('Pickup Date');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.id);
                                row.push(values.pickup_date);
                                row.push(values.requested_at);
                                row.push(values.shipper);
                                row.push(values.contact_person);
                                row.push(values.contact_number);
                                row.push(values.address);
                                row.push(values.city);
                                row.push(values.bookings);
                                row.push(values.pending_bookings);
                                row.push(values.total_estimated_weight);
                                row.push(values.pickup_type);
                                row.push(values.pickup_date);


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
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
				@if (session('role_id') == 1 || count(array_intersect([18, 19], session('permissions'))) !== 0)

					buttons: [
					@if (session('role_id') == 1 || in_array(19, session('permissions')))
						{
							text: 'Assign',
							className: 'btn btn-primary assign',
							enabled: false,
							action: function (e, dt, node, config) {
								$('#assign_to_rider .rider').val(null).trigger('change');

								$('#assign_to_rider').modal('show');
							}
						},
					@endif

					@if (session('role_id') == 1 || in_array(18, session('permissions')))
						{
							text: 'Cancel',
							className: 'btn btn-danger cancel',
							enabled: false,
							action: function (e, dt, node, config) {
								swal({
									text: 'Are you sure, you want to Cancel these Pickup(s)?',
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
												toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
											}

											table.rows().deselect();

											selected_rows = [];

											table.button('.assign').disable();
											table.button('.cancel').disable();

											table.draw('false');
										});
									}
								});
							}
						},
					@endif
                        {
                            extend: 'excel',
                            title: 'Pending Pickups',
                        	className: 'btn btn-primary',
                            text: '<i class="la la-file-excel-o"></i> Excel',
                        }, {
		                  extend: 'selectAll',
		                  text: 'Select All',
		                  className: 'select_all',
		                  action : function(e) {
		                    e.preventDefault();

		                    table.rows().nodes().each(function(index) {
		                      var row = table.row(index);

		                      if ($(row.node().firstChild).hasClass('select-checkbox')) {
		                        row.select();

		                        id = parseInt(row.id());

		                        var index = $.inArray(id, selected_rows);

		                        if (index === -1) {
		                          selected_rows.push(id);
		                        }

		                        table.button('.assign').enable();
		                        table.button('.cancel').enable();
		                      }
		                    });
		                  }
		                }, {
		                  extend: 'selectNone',
		                  text: 'Select None',
		                  className: 'select_none',
		                  action : function(e) {
		                    e.preventDefault();

		                    table.rows().nodes().each(function(index) {
		                      var row = table.row(index);

		                      if ($(row.node().firstChild).hasClass('select-checkbox')) {
		                        row.deselect();

		                        id = parseInt(row.id());

		                        var index = $.inArray(id, selected_rows);

		                        if (index !== -1) {
		                            selected_rows.splice(index, 1);
		                        }

		                        if (selected_rows.length == 0) {
		                            table.button('.assign').disable();
		                            table.button('.cancel').disable();
		                        }
		                      }
		                    });
		                  }
		                }
                        ],
				@else
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Pending Pickups',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }
                    ],
				@endif
				scrollX: true, scrollY: '350px',
				select: {
					info: false,
					style: 'multi',
					selector: 'td.select-checkbox',
					className: 'selected bg-primary bg-lighten-5 primary'
				},
				lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
				pageLength: 50,
				pagingType: 'full_numbers',
				processing: true,
                language: {
                    processing: data_table_loader
                },
				serverSide: true,
				ajax: '{{ route('admin.pickups.pending.list') }}',
				rowId: 'id',
				order: [[2, 'asc'], [3, 'asc']],
				columns: [
					{data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'serial_number', orderable: false, searchable: false, name: 'pickup_requests.id', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data: 'pickup_request_id', name: 'pickup_requests.id', class: 'align-middle pickup_request_id'},
					{data: 'pickup_date', name: 'pickup_requests.pickup_date', class: 'align-middle pickup_date'},
					{data: 'requested_at', name: 'pickup_requests.created_at', class: 'align-middle requested_at'},
					{data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
					{data: 'contact_person', name: 'usi.poc', class: 'align-middle contact_person'},
					{data: 'contact_number', name: 'usi.phone', class: 'align-middle contact_number'},
					{data: 'address', name: 'usi.pickup_address', class: 'align-middle address'},
					{data: 'city', name: 'ci.name', class: 'align-middle city'},
					{data: 'bookings_link', name: 'pickup_requests.bookings', class: 'align-middle bookings_link text-center'},
					{data: 'pending_bookings_link', name: 'pickup_requests.pending_bookings', class: 'align-middle pending_bookings_link'},
					{data: 'total_estimated_weight', name: 'pickup_requests.total_estimated_weight', class: 'align-middle total_estimated_weight'},
					{data: 'pickup_type', name: 'pickup_type', class: 'align-middle pickup_type'},
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
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="0">Light</option>' +
                        '<option value="1">Heavy</option>' +
                        '</select>';
					this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.action')) {
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
					table.button('.assign').enable();
					table.button('.cancel').enable();
				}
				else {
					table.button('.assign').disable();
					table.button('.cancel').disable();
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
							toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
						}

						table.rows().deselect();

						selected_rows = [];

						table.button('.assign').disable();
						table.button('.cancel').disable();

						table.draw('false');

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
								toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
							}

							var index = $.inArray(pickup_request_id, selected_rows);

							if (index !== -1) {
								selected_rows.splice(index, 1);
							}

							if (selected_rows.length > 0) {
								table.button('.assign').enable();
								table.button('.cancel').enable();
							}
							else {
								table.button('.assign').disable();
								table.button('.cancel').disable();
							}

							table.draw('false');
						});
					}
				});
			});
            var route = '{!! route('admin.tracking.index') !!}';
			$('body').on('click','#datatable tbody tr td.bookings_link button',function () {
			    var id = parseInt($(this).parents('tr').attr('id'));
                $('#bookings_modal .modal-body').html('');
                $('#bookings_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.pickups.pending.bookings.all') !!}',
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

            $('#datatable tbody').on('click','tr td.pending_bookings_link button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#pending_bookings_modal .modal-body').html('');
                $('#pending_bookings_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.pickups.pending.bookings') !!}',
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
                            $('#pending_bookings_modal .modal-body').html(shipments);


                        }
                    });

            });
		});
	</script>
@endsection