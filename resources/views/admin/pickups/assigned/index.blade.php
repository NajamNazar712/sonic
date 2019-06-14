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
										<th class="border-primary border-darken-1">Rider Route</th>
										<th class="border-primary border-darken-1">Rider City</th>
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
				<!--Pickups popup -->
				<div class="modal fade" id="pickups_modal" data-backdrop="static" role="dialog" aria-labelledby="pickups_modal" aria-hidden="true">
					<div class="modal-dialog modal-sm" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title" id="pickups_modal_title">Booking Shipment(s)</h4>

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
				<!--Pickups popup -->
			</div>
		</div>
	</div>
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">

@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

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
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
				@if (session('role_id') == 1 || in_array(22, session('permissions')))

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

									table.rows().deselect();

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
                            className:'btn btn-primary',
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

		                        table.button('.print').enable();
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
		                            table.button('.print').disable();
		                        }
		                      }
		                    });
		                  }
		                },'reset'],
				@else
	               buttons:[{
                    extend: 'excel',
                    title: 'Assigned Pickups',
                    className:'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },'reset'],
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
				ajax: '{{ route('admin.pickups.assigned.list') }}',
				rowId: 'id',
				order: [[10, 'desc']],
				columns: [
					{data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'serial_number', orderable: false, searchable: false, name: 'pickup_notes.id', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data: 'rider', name: 'rider', class: 'align-middle rider'},
					{data: 'rider_type', name: 'rider_type', class: 'align-middle rider_type'},
					{data: 'route', name: 'route', class: 'align-middle route'},
					{data: 'city', name: 'c.name', class: 'align-middle city'},
					{data: 'pickups_link', name: 'pickup_notes.pickups', class: 'align-middle pickups_link text-center'},
					{data: 'bookings_link', name: 'pickup_notes.bookings', class: 'align-middle bookings_link text-center'},
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
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="0">Light</option>' +
                        '<option value="1">Heavy</option>' +
                        '</select>';
                    var rider_select = '<select name="rider_select" id="rider_select" class="select2 form-control"></select>';

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
                        }else if($(header).is('.rider_type')){
                            $(rider_select).appendTo($(search))
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
                    var data1 = $.map({!! $rider_category !!}, function (obj) {
                        obj.id = obj.id

                        return obj;
                    });
                    var data1 = $.map({!! $rider_category !!}, function (obj) {
                        obj.text = obj.name

                        return obj;
                    });

                    $("#rider_select").prepend('<option value="" selected></option>').select2({
                        data:data1,
                        placeholder: "Select Rider",
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
									toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
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
								var shipper = '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Shipper</strong></td><td class="align-middle text-center">' + details.shipper + '</td></tr>';
								var contact_person = '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Contact Person</strong></td><td class="align-middle text-center">' + details.contact_person + '</td></tr>';
								var vendor = '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Vendor</strong></td><td class="align-middle text-center">' + ((details.vendor) ? details.vendor : '') + '</td></tr>';
								var contact_number = '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Contact Number</strong></td><td class="align-middle text-center">' + details.contact_number + '</td></tr>';
								var address = '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Address</strong></td><td class="align-middle text-center">' + details.address + '</td></tr>';
								var bookings = '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Bookings</strong></td><td class="align-middle text-center">' + details.bookings + '</td></tr>';
								var total_estimated_weight = '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Total Estimated Weight</strong></td><td class="align-middle text-center">' + details.total_estimated_weight + 'kg</td></tr>';
								var pickup_type = '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Pickup Type</strong></td><td class="align-middle text-center">' + details.pickup_type + '</td></tr>';

								pickup_requests += '<table class="table table-sm table-bordered mb-1"><tbody>' + shipper + contact_person + vendor + contact_number + address + bookings + total_estimated_weight + pickup_type + '</tbody></table>';
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
									toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
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

            var route = '{!! route('admin.tracking.index') !!}';
            $('#datatable tbody').on('click','tr td.bookings_link button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#bookings_modal .modal-body').html('');
                $('#bookings_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.pickups.assigned.bookings.all') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'pickup_note_id': id
                    }
                })
                    .done(function(data) {
                        if (data) {

                            var shipments = '';
                            if (data.booked) {
                                $.each(data.booked, function(index, shipment_ids) {
                                    
                                    shipments += "<div><b>Shipper : "+index+"</b></div>";
                                    $.each(shipment_ids, function (index,tracking_numbers) {
                                        shipments += '<u><a href='+route+'?tracking_number='+tracking_numbers+' target="_blank">'+tracking_numbers+'</a></u><br>';

                                    });
                                });
                            }
                            $('#bookings_modal .modal-body').html(shipments);
                        }
                    });

            });

            $('#datatable tbody').on('click','tr td.pickups_link button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#pickups_modal .modal-body').html('');
                $('#pickups_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.pickups.assigned.pickups') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'pickup_note_id': id
                    }
                })
                    .done(function(data) {

                        if (data) {
                            var shipments = '<table class="table table-bordered"><thead><tr><td class="border-primary border-darken-2 align-middle"><b>Shipper</b></td><td class="border-primary border-darken-2 align-middle"><b>Bookings</b></td><td class="border-primary border-darken-2 align-middle"><b>Pending Bookings</b></td></tr></thead><tbody>';

                            if (data.pickups) {
                                $.each(data.pickups, function(index, details) {
                                        shipments += '<tr><td class="align-middle">'+details.name+'</td><td class="align-middle">'+details.bookings+'</td><td class="align-middle">'+details.pending_bookings+'</td></tr>';

                                });
                            }
                            shipments += '</tbody></table>';
                            $('#pickups_modal .modal-body').html(shipments);


                        }
                    });

            });
		});
	</script>
@endsection