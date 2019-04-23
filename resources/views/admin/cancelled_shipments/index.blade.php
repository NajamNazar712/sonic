@extends('admin.layout.master')

@section('title', 'Cancelled Shipments')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Cancelled Shipments
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
                                        <th class="border-primary border-darken-1">Tracking No.</th>
                                        <th class="border-primary border-darken-1">Order ID</th>
                                        <th class="border-primary border-darken-1">Account No.</th>
                                        <th class="border-primary border-darken-1">Shipper</th>
                                        <th class="border-primary border-darken-1">Service Type</th>
                                        <th class="border-primary border-darken-1">Remarks</th>
                                        <th class="border-primary border-darken-1">Origin</th>
                                        <th class="border-primary border-darken-1">Destination</th>
                                        <th class="border-primary border-darken-1">Consignee Name</th>
                                        <th class="border-primary border-darken-1">Consignee Contact</th>
                                        <th class="border-primary border-darken-1">Consignee Address</th>
                                        <th class="border-primary border-darken-1">Collection Amount</th>
                                        <th class="border-primary border-darken-1">Booking Date</th>
                                        <th class="border-primary border-darken-1">Instructions</th>
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
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

	<script type="text/javascript">
		$(document).ready(function() {
            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.cancelled_shipments.list') }}',
                        data: {
                            'page': 'all',
                            'shipment_type': $('#shipment_type_search_form #shipment_type').val()
                        },
                        success: function (result) {
                            head = [];

                            head.push('S No.');
                            head.push('Tracking No.');
                            head.push('Order ID');
                            head.push('Account No.');
                            head.push('Shipper');
                            head.push('Service Type');
                            head.push('Remarks');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Consignee Name');
                            head.push('Consignee Contact');
                            head.push('Consignee Address');
                            head.push('Collection Amount');
                            head.push('Booking Date');
                            head.push('Instructions');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.order_id);
                                row.push(values.account_number);
                                row.push(values.shipper);
                                row.push(values.service_type);
                                row.push(values.remarks);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.consignee_name);
                                row.push(values.consignee_contact);
                                row.push(values.consignee_address);
                                row.push(values.collection_amount);
                                row.push(values.booking_date);
                                row.push(values.instructions);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });

            var selected_rows = [];

            var table = $('#datatable').DataTable({
                scrollX: true, scrollY: '350px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                @if (session('role_id') == 1 || in_array(118, session('permissions')))
                    {
                        text: 'Revert',
                        className: 'btn btn-primary revert',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            swal({
                                text: 'Are you sure, you want to Revert these Shipment(s)?',
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
                                        url: '{!! route('admin.cancelled_shipments.revert') !!}',
                                        method: 'PUT',
                                        data: {
                                            '_token': '{{ csrf_token() }}',
                                            'shipment_ids': selected_rows
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

                                        table.button('.revert').disable();

                                        table.draw('false');
                                    });
                                }
                            });
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

                        table.button('.revert').enable();
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
                            table.button('.revert').disable();
                        }
                      }
                    });
                  }
                }, {
                    extend: 'excel',
                    title: 'Cancelled Shipments',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                }],
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
                serverSide: true,
                ajax: '{{ route('admin.cancelled_shipments.list') }}',
                rowId: 'id',
                order: [[14, 'desc']],
                columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number_hyperlink', name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'order_id', name: 'shipments.order_id', class: 'align-middle order_id'},
                    {data: 'account_number', name: 'u.id', class: 'align-middle account_number'},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'service_type', name: 'service_type', class: 'align-middle service_type'},
                    {data: 'remarks', name: 'shipments_journey.remarks', class: 'align-middle remarks'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'consignee_name', name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                    {data: 'consignee_contact', name: 'consignee_contact', class: 'align-middle consignee_contact'},
                    {data: 'consignee_address', name: 'shipments.consignee_address', class: 'align-middle consignee_address'},
                    {data: 'collection_amount', name: 'shipments.amount', class: 'align-middle amount'},
                    {data: 'booking_date', name: 'shipments.created_at', class: 'align-middle booking_date'},
                    {data: 'instructions', name: 'shipments.special_instructions', class: 'align-middle instructions'},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}

                ],
                rowCallback: function(row, data, index) {
                    $('td:eq(0)', row).addClass('select-checkbox');

                    if ($.inArray(data.id, selected_rows) !== -1) {
                        table.row(row).select();
                    }

                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '</select>';
                    var service_drop_select = '<select name="service_select" id="service_select" class="select2 form-control"></select>';
                    var product_select = '<select name="product_select" id="product_select" class="select2 form-control"></select>';
                    var payment_select = '<select name="payment_select" id="payment_select" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.action') || $(header).is('.serial_number') || $(header).is('.select')) {
                            $(td).appendTo($(search));
                        }
                        else if ($(header).is('.status')) {
                            $(drop_select).appendTo($(search))
                            .on('change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td);
                        }
                        else if ($(header).is('.service_type')) {
                            $(service_drop_select).appendTo($(search))
                            .on('change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td);
                        }
                        else if ($(header).is('.product_type')) {
                            $(product_select).appendTo($(search))
                            .on('change', function () {
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
                    var data = $.map({!! $shipment_status !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });

                    var data = $.map({!! $shipment_status !!}, function (obj) {
                        obj.text = obj.name;

                        return obj;
                    });

                    $('#status_select').prepend('<option value="" selected></option>').select2({
                        data:data,
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    var data2 = $.map({!! $service_type !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });

                    var data2 = $.map({!! $service_type !!}, function (obj) {
                        obj.text = obj.booking_type;

                        return obj;
                    });

                    $('#service_select').prepend('<option value="" selected></option>').select2({
                        data:data2,
                        placeholder: "Select Service",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    var data3 = $.map({!! $products !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });

                    var data3 = $.map({!! $products !!}, function (obj) {
                        obj.text = obj.product_name;

                        return obj;
                    });

                    $('#product_select').prepend('<option value="" selected></option>').select2({
                        data:data3,
                        placeholder: "Select Product",
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
                    table.button('.revert').enable();
                }
                else {
                    table.button('.revert').disable();
                }
            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.revert', function() {
                var shipment_id = parseInt($(this).parents('tr').attr('id'));

                swal({
                    text: 'Are you sure, you want to Revert this Shipment?',
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
                    selected_rows = [];

                    table.rows().deselect();

                    table.button('.revert').disable();

                    selected_rows.push(shipment_id);

                    if (confirm) {
                        $.ajax({
                            url: '{!! route('admin.cancelled_shipments.revert') !!}',
                            method: 'PUT',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'shipment_ids': selected_rows
                            }
                        })
                        .done(function(data) {
                            if (data.status == 0) {
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            }
                            else {
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }

                            table.draw('false');
                        });
                    }
                });
            });
		});
	</script>
@endsection