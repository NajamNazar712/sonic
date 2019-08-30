@extends('admin.layout.master')

@section('title', 'Outstanding Shipments')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Outstanding Shipments
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<form id="search_form" class=" mb-1 justify-content-center" novalidate="novalidate">
                                <div class="row justify-content-center">
                                    <div class="col-4">
                                        <div class="form-group">
                                            <select name="recovery_status" class="select2" id="recovery_status_select" data-rule-required="true" data-msg-required="Status is required">
                                                <option value="0">All</option>
                                                <option value="1">Outstanding</option>
                                                <option value="7">Resolved</option>
                                                <option value="11">Revert Requested</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <select name="hub" class="select2" id="hub" data-rule-required="true" data-msg-required="Hub is required">
                                                <option value="0">All</option>

                                                @foreach($hubs as $hub)
                                                    <option value="{{ $hub->id }}">{{ $hub->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <select name="service" class="select2" id="service">
                                                <option value="0">All</option>

                                                @foreach($booking_types as $booking_type)
                                                    <option value="{{ $booking_type->id }}">{{ $booking_type->booking_type }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
										<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
											<span class="la la-calendar-o"></span>
										</span>
                                            </div>

                                            <input type="text" name="delivery_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="delivery_date_from" placeholder="Delivery Date (From)">
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
										<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
											<span class="la la-calendar-o"></span>
										</span>
                                            </div>

                                            <input type="text" name="delivery_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="delivery_date_to" placeholder="Delivery Date (To)">
                                        </div>

                                    </div>

                                    <div class="col-3">
                                        <div class="form-group">
                                            <button type="submit" name="search" class="btn btn-primary" value="Search">Search</button>
                                        </div>
                                    </div>





                                </div>

							</form>

							<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1"></th>
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">Tracking Number</th>
										<th class="border-primary border-darken-1">Consignee</th>
										<th class="border-primary border-darken-1">Address</th>
										<th class="border-primary border-darken-1">Destination</th>
										<th class="border-primary border-darken-1">Hub</th>
										<th class="border-primary border-darken-1">Shipper</th>
										<th class="border-primary border-darken-1">Service Type</th>
										<th class="border-primary border-darken-1">Amount</th>
										<th class="border-primary border-darken-1">Recovery Status</th>
										<th class="border-primary border-darken-1">Recovery Status Date</th>
										<th class="border-primary border-darken-1">Revert Requested Image</th>
										<th class="border-primary border-darken-1">Status</th>
										<th class="border-primary border-darken-1">Status Updated Datetime</th>
										<th class="border-primary border-darken-1">Remarks</th>
										<th class="border-primary border-darken-1">DNCC</th>
										<th class="border-primary border-darken-1">SDN</th>
										<th class="border-primary border-darken-1">Aging</th>
										<th class="border-primary border-darken-1"></th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade text-left" id="RevertModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="RevertModal"
		 aria-hidden="true">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header bg-primary white">
					<h4 class="modal-title white">Revert Status Request</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body text-center">
					<form id="revert_status_add_form" class="form" action="{{route('admin.finance.outstanding_shipments.revert_request_submit')}}" method="post" enctype="multipart/form-data">
						@csrf
						<input type="hidden" name="revert_shipment_ids" id="revert_shipment_ids"/>
						<input type="hidden" name="revert_delivery_note_ids" id="revert_delivery_note_ids"/>
						<table class="table table-bordered datatable" id="revert_status_table" style="z-index: 3;">
							<thead>
							<tr role="row" class="bg-primary white">

								<th class="border-primary border-darken-1">S. No.</th>
								<th class="border-primary border-darken-1">Tracking</th>
								<th class="border-primary border-darken-1">Remarks</th>
								<th class="border-primary border-darken-1">Image</th>

							</tr>
							</thead>
						</table>
						<hr>
						<div class="row justify-content-center">
							<div class="col-3">
								<button type="submit" class="btn btn-primary btn-block">Update</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
	<style>
		table.dataTable tbody tr td.select-checkbox:before {
			top: 50%;
			border-color: #64a0d2;
		}

		table.dataTable tbody tr.selected td.select-checkbox:after {
			top: 50%;
			text-shadow: none;
		}

		.outstanding_revert{
			color: #FDFEFE;
			background-color: #F39C12;
		}
		.resolved_revert{
			/*color: #FDFEFE;*/
			background-color: #F7DC6F;
		}
	</style>
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			$('#search_form #hub').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Hub*'
			}).bind('change', function() {
				$(this).valid();
			});

			$('#search_form #recovery_status_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Recovery Status*'
			}).bind('change', function() {
				$(this).valid();
			});

			$('#search_form #service').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Service'
			});

			$('#search_form #delivery_date_from').pickadate({
				firstDay: 1,
				clear: '',
				selectYears: true,
				selectMonths: true,
				formatSubmit: 'yyyy-mm-dd 00:00:00',
				hiddenSuffix: '_formatted',
				onSet: function(context) {
					if (context.select) {
						$('#search_form #delivery_date_to').pickadate('picker').set('min', $('#search_form #delivery_date_from').pickadate('picker').get('select'));
					}
				}
			});

			$('#search_form #delivery_date_to').pickadate({
				firstDay: 1,
				clear: '',
				selectYears: true,
				selectMonths: true,
				formatSubmit: 'yyyy-mm-dd 00:00:00',
				hiddenSuffix: '_formatted',
				onSet: function(context) {
					if (context.select) {
						$('#search_form #delivery_date_from').pickadate('picker').set('max', $('#search_form #delivery_date_to').pickadate('picker').get('select'));
					}
				}
			});

			$('#search_form').validate({
				errorClass: 'danger',
				successClass: 'success',
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parents('form'));
				},
				submitHandler: function(form) {
					table.draw();

					return false;
				}
			});

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.finance.outstanding_shipments.list') }}',
                        data: {
                            'page': 'all',
                            'hub': $('#search_form #hub').val(),
                            'recovery_status': $('#search_form #recovery_status_select').val(),
                    		'service': $('#search_form #service').val(),
                    		'delivery_date_from': $('#search_form input[name="delivery_date_from_formatted"]').val(),
                    		'delivery_date_to': $('#search_form input[name="delivery_date_to_formatted"]').val(),
                        },
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Tracking Number');
                            head.push('Consignee');
                            head.push('Address');
                            head.push('Destination');
                            head.push('Hub');
                            head.push('Shipper');
                            head.push('Service Type');
                            head.push('Amount');
                            head.push('Recovery Status');
                            head.push('Recovery Status Date');
                            head.push('Status');
                            head.push('Status Updated Datetime');
                            head.push('Remarks');
                            head.push('DNCC');
                            head.push('SDN');
                            head.push('Aging');




                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.consignee);
                                row.push(values.address);
                                row.push(values.destination);
                                row.push(values.hub);
                                row.push(values.shipper);
                                row.push(values.service_type);
                                row.push(values.amount);
                                row.push(values.shipment_recovery_status);
                                row.push(values.recovery_date);
                                row.push(values.status);
                                row.push(values.status_updated_at);
                                row.push(values.remarks);
                                row.push(values.dncc);
                                row.push(values.sdn);
                                row.push(values.aging);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            var selected_rows = [];
			var selected_delivery_note_ids = [];
            var revert_status_table;
			var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    @if (session('role_id') == 1 || in_array(249, session('permissions')))
                    {
                        text: 'Request Revert',
                        className: 'btn btn-primary revert_request',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            if(selected_rows.length > 0){
                                // table.rows().nodes().each(function(index) {
                                //     var row = table.row(index);
                                //
                                //     // if ($(row.node()).hasClass('selected')) {
                                //     //     var id = parseInt(row.id());
                                //     //     var delivery_note_id = $(row.node()).attr('data-dncc');
                                //     //     selected_delivery_note_ids[id] = delivery_note_id;
                                //     // }
                                // });

                                $.ajax({
                                    url: '{!! route('admin.finance.outstanding_shipments.revert_request_shipments_check') !!}',
                                    method: 'PUT',
                                    data: {
                                        'shipment_ids': selected_rows,
										'delivery_note_ids': selected_delivery_note_ids,
                                        '_token': '{{ csrf_token() }}'
                                    }
                                })
                                    .done(function(data) {
                                        if (data.status == 0) {
                                            $('#RevertModal').modal('show');
                                            revert_status_table = $('#revert_status_table').DataTable({
                                                dom: 'ltipr',
                                                ordering:false,
                                                paging:false,
                                                columns: [
                                                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                                                    {name: 'tracking_number', class: 'align-middle tracking_number form-group'},
                                                    {name: 'remarks', class: 'align-middle remarks form-group'},
                                                    {name: 'image', class: 'align-middle image form-group'},
                                                ],
                                                rowCallback: function(row, data, index) {
                                                    var info = revert_status_table.page.info();

                                                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                                                }
                                            });
                                            selected_rows = [];
                                            selected_delivery_note_ids = [];
                                            $.each(data.shipments, function (index, value) {
                                                selected_rows.push(index);
                                                selected_delivery_note_ids.push(data.delivery_note_ids[index]);
                                                var remarks_input = '<textarea class="form-control form-control-sm remarks" rows="5" name="remarks['+index+']" placeholder="Remarks" data-rule-required="true" data-msg-required="Remarks are required"></textarea>';
                                                var upload_image = '<input class="form-control form-control-sm" type="file" name="upload_image'+index+'" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">';
                                                revert_status_table.row.add([0, value, remarks_input, upload_image]).node().id = index;
                                                revert_status_table.draw(true);
                                            });
                                            $('body').on('change','#revert_status_table tr td.remarks textarea',function() {
                                                $(this).val($(this).val().trim());
                                            });


                                        }
                                        else {
                                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                            selected_delivery_note_ids = [];
                                            selected_rows = [];
                                            table.draw(false);
                                        }

                                    });
							}

                        }
                    },
                    @endif
                    {
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

                                    table.button('.revert_request').enable();
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

                                }
                            });
                            if (selected_rows.length == 0) {
                                table.button('.revert_request').disable();
                            }
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Outstanding Shipments',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },'reset'],
				scrollX: true, scrollY: '500px',
				lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
				pageLength: 50,
				pagingType: 'full_numbers',
				processing: true,
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                language: {
                    processing: data_table_loader
                },
				serverSide: true,
				ajax: {
					url: '{{ route('admin.finance.outstanding_shipments.list') }}',
					data: function (d) {
						d.hub = $('#search_form #hub').val();
						d.recovery_status = $('#search_form #recovery_status_select').val();
						d.service = $('#search_form #service').val();
						d.delivery_date_from = $('#search_form input[name="delivery_date_from_formatted"]').val();
						d.delivery_date_to = $('#search_form input[name="delivery_date_to_formatted"]').val();
					}
				},
				rowId: 'id',
				order: [[12, 'desc']],
				columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data:'tracking_number', name: 's.tracking_number', class: 'align-middle text-center tracking_number'},
					{data:'consignee', name: 's.consignee_name', class: 'align-middle text-center consignee'},
					{data:'address', name: 's.consignee_address', class: 'align-middle text-center address'},
					{data:'destination', name: 'dc.name', class: 'align-middle text-center destination'},
					{data:'hub', name: 'hc.name', class: 'align-middle text-center hub'},
					{data:'shipper', name: 'u.name', class: 'align-middle text-center shipper'},
					{data:'service_type', name: 'bt.id', class: 'align-middle text-center service_type'},
					{data:'amount', name: 's.amount', class: 'align-middle text-center amount'},
					{data:'shipment_recovery_status', name: 'delivery_note_shipments.status', class: 'align-middle text-center shipment_recovery_status'},
                    {data:'recovery_date', name: 'sj.remarks', class: 'align-middle text-center recovery_date'},
                    {data:'revert_requested_image_button', name: 'revert_requested_image_button', class: 'align-middle text-center revert_requested_image_button', orderable: false, searchable: false},
                    {data:'status', name: 'ss.id', class: 'align-middle text-center status'},
                    {data:'status_updated_at', name: 'sj.updated_at', class: 'align-middle text-center status_updated_at'},
                    {data:'remarks', name: 'sj.remarks', class: 'align-middle text-center remarks'},
					{data:'dncc_link', name: 'delivery_note_shipments.delivery_note_id', class: 'align-middle text-center dncc_link'},
					{data:'sdn_link', name: 'dnsdn.station_deposit_note_id', class: 'align-middle text-center sdn_link'},
					{data:'aging', name: 'aging', class: 'align-middle text-center aging', orderable: false, searchable: false},
					{data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
				],
				rowCallback: function(row, data, index) {
					var info = table.page.info();
					$('td:eq(1)', row).html(index + 1 + info.page * info.length);
					if(data.recovery_status == 11){
                        $('td:eq(0)', row).removeClass('select-checkbox');
					}
				},
				initComplete: function() {
					var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

					var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
					var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
					var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var service_drop_select = '<select name="service_select" id="service_select" class="select2 form-control"></select>';
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';

					this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.serial_number') || $(header).is('.aging') || $(header).is('.action') || $(header).is('.select') || $(header).is('.shipment_recovery_status') || $(header).is('.revert_requested_image_button')) {
							$(td).appendTo($(search));
						}else if($(header).is('.service_type')){
                            $(service_drop_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.status')){
                            $(status_select).appendTo($(search))
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
                    var data = $.map({!! $shipment_status !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                    var data = $.map({!! $shipment_status !!}, function (obj) {
                        obj.text = obj.name;

                        return obj;
                    });

                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        data:data,
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data2 = $.map({!! $service_type !!}, function (obj) {
                        obj.id = obj.id

                        return obj;
                    });
                    var data2 = $.map({!! $service_type !!}, function (obj) {
                        obj.text = obj.booking_type;

                        return obj;
                    });

                    $("#service_select").prepend('<option value="" selected></option>').select2({
                        data:data2,
                        placeholder: "Select Service",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
					this.api().table().columns.adjust();
				}
			});

            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));
                var delivery_note_id = $(this).parents('tr').attr('data-dncc');

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                    selected_delivery_note_ids.push(delivery_note_id);
                }
                else {
                    selected_rows.splice(index, 1);
                    selected_delivery_note_ids.push(index, 1);

                }

                if (selected_rows.length > 0) {
                    table.button('.revert_request').enable();
                }
                else {
                    table.button('.revert_request').disable();
                }
            });

			$('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
				var id = parseInt($(this).parents('tr').attr('id'));
				var tracking_number = $(this).parents('tr').children('td.tracking_number').text();
				var dncc = parseInt($(this).parents('tr').attr('data-dncc'));

				if ($(this).hasClass('resolve')) {
					swal({
						title: 'Are you sure?',
						text: 'You want to mark ' + tracking_number + ' Resolved?',
						icon: 'success',
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
								url: '{!! route('admin.finance.outstanding_shipments.resolved') !!}',
								method: 'PUT',
								data: {
									'id': id,
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

								table.draw(false);
							});
						}
					});
				}
				else if ($(this).hasClass('reject')) {
					swal({
						title: 'Are you sure?',
						text: 'You want to reject status reversion of ' + tracking_number + ' ?',
						icon: 'success',
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
								url: '{!! route('admin.finance.outstanding_shipments.resolved') !!}',
								method: 'PUT',
								data: {
									'id': id,
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

								table.draw(false);
							});
						}
					});
				}
				else if ($(this).hasClass('adjust_in_payment')) {
					swal({
						title: 'Are you sure?',
						text: 'You want to Adjust ' + tracking_number + ' in Payment?',
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
								url: '{!! route('admin.finance.outstanding_shipments.adjust_in_payment') !!}',
								method: 'PUT',
								data: {
									'id': id,
									'dncc': dncc,
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

								table.draw(false);
							});
						}
					});
				}
			});
            $('#revert_status_add_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $('#revert_shipment_ids').val(selected_rows);
                    $('#revert_delivery_note_ids').val(selected_delivery_note_ids);

                    form.submit();

                }
            });
            function printDNCC(id) {
                $.ajax({
                    url: '{!! route('admin.delivery.sdn.dncc.print') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
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
            $('#datatable tbody').on('click', 'tr td.dncc_link button.print', function() {
                var delivery_note_id = parseInt($(this).parents('tr').data('dncc'));

                printDNCC(delivery_note_id);
            });

            function printSDN(id) {
                $.ajax({
                    url: '{!! route('admin.delivery.sdn.print') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
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
            $('#datatable tbody').on('click', 'tr td.sdn_link button.print', function() {
                var sdn = parseInt($(this).parents('tr').data('sdn'));
				if(sdn){
                    printSDN(sdn);
                }else{
				    var error = "Station Deposit Note not found!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                }
            });
		});
	</script>
@endsection