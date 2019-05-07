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
								<form id="receive_pickup_note_form" class="form-inline mb-1 justify-content-center" target="_blank" novalidate="novalidate" method="POST" action="{{ route('admin.pickups.receive.pickup_note') }}">
									{{ csrf_field() }}

									<input type="hidden" name="type" class="form-control type" value="0">

									<div class="form-group">
										<input type="text" name="pickup_note_no" class="form-control pickup_note_no" placeholder="Pickup Note No.*" data-rule-required="true" data-msg-required="Pickup Note No. is required">
									</div>

									<div class="form-group ml-1">
										<button type="submit" name="receive" class="btn btn-primary" value="Receive">Receive</button>
									</div>
								</form>
							@endif

							<form id="tracking_number_search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
								<div class="form-group">
									<input type="text" name="tracking_number" class="form-control tracking_number" id="tracking_number" placeholder="Tracking Number">
								</div>
							</form>

							<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">Rider</th>
										<th class="border-primary border-darken-1">Rider Type</th>
										<th class="border-primary border-darken-1">Rider Route</th>
										<th class="border-primary border-darken-1">Rider City</th>
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
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			@if(session('errors'))
            	scan_sound(2);
			@endif
			@if(session('success'))
            	scan_sound(1);
			@endif
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
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.pickups.receive.list') }}',
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
                            head.push('Pickup Type');
                            head.push('Assigned Date');
                            head.push('Assigned By');
                            head.push('Pickup Note No.');
                            head.push('Status');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.rider_name+" | "+values.rider_phone);
                                row.push(values.rider_type);
                                row.push(values.route);
                                row.push(values.city);
                                row.push(values.pickups);
                                row.push(values.bookings);
                                row.push(values.pickup_type);
                                row.push(values.assigned_date);
                                row.push(values.assigned_by);
                                row.push(values.pickup_note_id);
                                row.push(values.status);


                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
			var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '350px',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Receive Pickups',
						className:'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }
                ],
				lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
				pageLength: 50,
				pagingType: 'full_numbers',
				processing: true,
                language: {
                    processing: data_table_loader
                },
				serverSide: true,
				ajax: {
					url: '{{ route('admin.pickups.receive.list') }}',
					data: function (d) {
						d.tracking_number = $('#tracking_number_search_form #tracking_number').val();
					}
				},
				rowId: 'id',
				order: [[10, 'desc']],
				columns: [
					{data: 'serial_number', orderable: false, searchable: false, name: 'pickup_notes.id', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data: 'rider', name: 'rider', class: 'align-middle rider'},
					{data: 'rider_type', name: 'rider_type', class: 'align-middle rider_type'},
					{data: 'route', name: 'route', class: 'align-middle route'},
					{data: 'city', name: 'c.name', class: 'align-middle city'},
					{data: 'pickups', name: 'pickup_notes.pickups', class: 'align-middle pickups'},
					{data: 'bookings_link', name: 'pickup_notes.bookings', class: 'align-middle bookings_link text-center'},
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
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="0">Light</option>' +
                        '<option value="1">Heavy</option>' +
                        '</select>';
                    var rider_select = '<select name="rider_select" id="rider_select" class="select2 form-control"></select>';
                    var pickup_select = '<select name="pickup_select" id="pickup_select" class="select2 form-control"></select>';

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
                        }else if($(header).is('.rider_type')){
                            $(rider_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.status')){
                            $(pickup_select).appendTo($(search))
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
                        obj.id = obj.id;
                        obj.text = obj.name;
                        return obj;
                    });


                    $("#rider_select").prepend('<option value="" selected></option>').select2({
                        data:data1,
                        placeholder: "Select Rider",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data2 = $.map({!! $pickup_status !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;
                        return obj;
                    });

                    $("#pickup_select").prepend('<option value="" selected></option>').select2({
                        data:data2,
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
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
					},
                    submitHandler: function(form) {
                        scan_sound(1);
                        form.submit();
                        $('#receive_pickup_note_form input.pickup_note_no').val('');
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

						$('#receive_pickup_note_form input.type').val(0);

						$('#receive_pickup_note_form').submit();
					}
					else if ($(this).hasClass('summary')) {
						$('#receive_pickup_note_form input.pickup_note_no').val(pickup_note_id);

						$('#receive_pickup_note_form input.type').val(1);

						$('#receive_pickup_note_form').submit();
					}
				});
			@endif

			$('#tracking_number_search_form').bind('submit', function(e) {
				e.preventDefault();

				length = $('#tracking_number_search_form #tracking_number').val().length;

				if (length == 0 || length >= 12) {
					table.draw();
				}
			});

			$('#tracking_number_search_form #tracking_number').inputmask({
				'alias': 'integer',
				'allowMinus': false,
				'allowPlus': false
			}).bind('input', function() {
				if (this.value.length == 0 || this.value.length >= 12) {
					table.draw();
				}
			});
            var route = '{!! route('admin.tracking.index') !!}';
            $('#datatable tbody').on('click','tr td.bookings_link button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#bookings_modal .modal-body').html('');
                $('#bookings_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.pickups.receive.bookings.all') !!}',
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
		});
	</script>
@endsection