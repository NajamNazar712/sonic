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
                                        <th class="border-primary border-darken-1">S No.</th>
                                        <th class="border-primary border-darken-1">Tracking No.</th>
                                        <th class="border-primary border-darken-1">Order ID</th>
                                        <th class="border-primary border-darken-1">Account No.</th>
                                        <th class="border-primary border-darken-1">Shipper</th>
                                        <th class="border-primary border-darken-1">Service Type</th>
                                        <th class="border-primary border-darken-1">Status</th>
                                        <th class="border-primary border-darken-1">Reason</th>
                                        <th class="border-primary border-darken-1">Payment Status</th>
                                        <th class="border-primary border-darken-1">Origin</th>
                                        <th class="border-primary border-darken-1">Destination</th>
                                        <th class="border-primary border-darken-1">Consignee Name</th>
                                        <th class="border-primary border-darken-1">Consignee Contact</th>
                                        <th class="border-primary border-darken-1">Consignee Address</th>
                                        <th class="border-primary border-darken-1">Collection Amount</th>
                                        <th class="border-primary border-darken-1">Product Type</th>
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
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

	<script type="text/javascript">
		$(document).ready(function() {
            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.cargo.pending.list') }}',
                        data: {
                            'page': 'all',
                            'shipment_type': $('#shipment_type_search_form #shipment_type').val()
                        },
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Tracking Number');
                            head.push('Order ID');
                            head.push('Service Type');
                            head.push('Status');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Shipper');
                            head.push('Amount');
                            head.push('Shipping Mode');
                            head.push('Booked Datetime');
                            head.push('Arrival Datetime');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.tracking);
                                row.push(values.order_id);
                                row.push(values.service_type);
                                row.push(values.status);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.shipper);
                                row.push(values.amount);
                                row.push(values.shipping_mode);
                                row.push(values.booked_at);
                                row.push(values.arrival_at);


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
                order: [[16, 'desc']],
                columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number_hyperlink', name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'order_id', name: 'shipments.order_id', class: 'align-middle order_id'},
                    {data: 'account_number', name: 'u.id', class: 'align-middle account_number'},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'service_type', name: 'service_type', class: 'align-middle service_type'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'reason', name: 'ssr.name', class: 'align-middle reason'},
                    {data: 'payment_status', name: 'payment_status', class: 'align-middle payment_status'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'consignee_name', name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                    {data: 'consignee_contact', name: 'consignee_contact', class: 'align-middle consignee_contact'},
                    {data: 'consignee_address', name: 'shipments.consignee_address', class: 'align-middle consignee_address'},
                    {data: 'amount', name: 'shipments.amount', class: 'align-middle amount'},
                    {data: 'product_type', name: 'product_type', class: 'align-middle product_type'},
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
                        else if ($(header).is('.payment_status')) {
                            $(payment_select).appendTo($(search))
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
                    var data4 = $.map({!! $payment_status !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                    var data4 = $.map({!! $payment_status !!}, function (obj) {
                        obj.text = obj.name;

                        return obj;
                    });

                    $('#payment_select').prepend('<option value="" selected></option>').select2({
                        data:data4,
                        placeholder: "Select Payment",
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
		});
	</script>
@endsection