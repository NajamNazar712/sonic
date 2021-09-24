@extends('admin.layout.master')

@section('title', 'Outstanding Walk-In Shipments')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Outstanding Walk-In Shipments
                </h1>
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <form id="track_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                                <div class="col-3">
                                    <div class="form-group">
                                        <input type="text" name="tracking_numbers" class="tracking_numbers ml-4" placeholder="Tracking Number(s)" id="tracking_numbers" style="width: 100%" >
                                    </div>
                                </div>

                                <div class="col-3">
                                    <div class="form-group">
                                        <button id="datatable_filter_btn" type="submit" class=" btn btn-outline-primary btn-min-width"><i
                                                    class="la la-search"></i> Search
                                        </button>
                                    </div>
                                </div>

                            </form>
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1"></th>
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Tracking Number</th>
                                    <th class="border-primary border-darken-1">Booked By</th>
                                    <th class="border-primary border-darken-1">Consignee</th>
                                    <th class="border-primary border-darken-1">Address</th>
                                    <th class="border-primary border-darken-1">Origin</th>
                                    <th class="border-primary border-darken-1">Destination</th>
                                    <th class="border-primary border-darken-1">Hub</th>
                                    <th class="border-primary border-darken-1">Item Quantity</th>
                                    <th class="border-primary border-darken-1">Product Name</th>
                                    <th class="border-primary border-darken-1">Mode of Shipment</th>
                                    <th class="border-primary border-darken-1">Actual Weight</th>
                                    <th class="border-primary border-darken-1">Chargeable Weight</th>
                                    <th class="border-primary border-darken-1">Weight Charges</th>
                                    <th class="border-primary border-darken-1">Fuel Surcharge</th>
                                    <th class="border-primary border-darken-1">Return Charges</th>
                                    <th class="border-primary border-darken-1">GST</th>
                                    <th class="border-primary border-darken-1">Total Charges</th>
                                    <th class="border-primary border-darken-1">Charges Mode</th>
                                    <th class="border-primary border-darken-1">Shipment Status</th>
                                    <th class="border-primary border-darken-1">Updated Datetime</th>
                                    <th class="border-primary border-darken-1">Updated By</th>
                                    <th class="border-primary border-darken-1">Arrival DateTime</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Aging</th>
                                    <th class="border-primary border-darken-1">Resolved At</th>
                                    <th class="border-primary border-darken-1">Resolved By</th>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            var selected_rows = [];

            var select = $('#track_form .tracking_numbers').selectize({
                placeholder: 'Tracking Number(s)',
                delimiter: ',',
                createOnBlur: true,
                persist: false,
                plugins: ['remove_button'],
                onDropdownOpen: function (dropdown) {
                    dropdown.remove();
                },
                onType: function (str) {
                    var regex = /^[0-9,]+$/;

                    if (!regex.test(str)) {
                        select[0].selectize.setTextboxValue('');
                    }
                },
                create: function (input) {
                    if (input.length >= 12 && Math.floor(input) == input && $.isNumeric(input)) {
                        return {
                            value: input,
                            text: input
                        }
                    }
                    else {
                        return false;
                    }
                }
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.finance.outstanding_shipments.walk_in_list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Tracking Number');
                            head.push('Booked By');
                            head.push('Consignee');
                            head.push('Address');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Hub');
                            head.push('Item Quantity');
                            head.push('Product Name');
                            head.push('Mode of Shipment');
                            head.push('Actual Weight');
                            head.push('Chargeable Weight');
                            head.push('Weight Charges');
                            head.push('Fuel Surcharge');
                            head.push('Return Charges');
                            head.push('GST');
                            head.push('Total Charges');
                            head.push('Charges Mode');
                            head.push('Shipment Status');
                            head.push('Updated Datetime');
                            head.push('Updated By');
                            head.push('Arrival Date/Time');
                            head.push('Status');
                            head.push('Aging');
                            head.push('Resolved At');
                            head.push('Resolved By');




                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.tracking_no);
                                row.push(values.booked_by);
                                row.push(values.consignee);
                                row.push(values.address);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.hub);
                                row.push(values.item_quantity);
                                row.push(values.product_name);
                                row.push(values.shipping_mode);
                                row.push(values.actual_weight);
                                row.push(values.chargeable_weight);
                                row.push(values.weight_charges);
                                row.push(values.fuel_surcharge);
                                row.push(values.return_charges);
                                row.push(values.gst);
                                row.push(values.charges);
                                row.push(values.charges_modes);
                                row.push(values.status);
                                row.push(values.status_updated_at);
                                row.push(values.updated_by);
                                row.push(values.arrival_date);
                                row.push(values.walk_in_status);
                                row.push(values.aging);
                                row.push(values.resolved_at);
                                row.push(values.resolved_by);
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
                buttons: [
                    @if(session('role_id') == 1 || in_array(168, session('permissions')))
                    {
                        text: 'Resolve',
                        className: 'btn btn-primary bulk_resolved',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            if(selected_rows.length > 0){
                                swal({
                                    title: 'Are you sure?',
                                    text: 'You want to mark selected Tracking Numbers Resolved?',
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
                                            url: '{!! route('admin.finance.outstanding_shipments.walk_in_bulk_resolved') !!}',
                                            method: 'post',
                                            data: {
                                                'shipments': selected_rows,
                                                '_token': '{{ csrf_token() }}'
                                            }
                                        })
                                            .done(function(data) {
                                                if (data.status == 0) {
                                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                                }
                                                else {
                                                    toastr.error('Something went wrong!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                                }
                                                table.rows().deselect();

                                                selected_rows = [];

                                                table.button('.bulk_resolved').disable();

                                                table.draw('false');
                                            });
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

                                    table.button('.bulk_resolved').enable();
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
                                        table.button('.bulk_resolved').disable();
                                    }
                                }
                            });
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Outstanding Walk-in Shipments',
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
                    url: '{{ route('admin.finance.outstanding_shipments.walk_in_list') }}',
                    data:function (d) {
                        d.tracking_numbers = $('#tracking_numbers').val();
                    }
                },
                rowId: 'id',
                order: [[21, 'desc']],
                columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data:'tracking_number', name: 'shipments.tracking_number', class: 'align-middle text-center tracking_number'},
                    {data:'booked_by', name: 'adn.name', class: 'align-middle text-center booked_by'},
                    {data:'consignee', name: 'shipments.consignee_name', class: 'align-middle text-center consignee'},
                    {data:'address', name: 'shipments.consignee_address', class: 'align-middle text-center address'},
                    {data:'origin', name: 'oc.name', class: 'align-middle text-center origin'},
                    {data:'destination', name: 'dc.name', class: 'align-middle text-center destination'},
                    {data:'hub', name: 'hc.name', class: 'align-middle text-center hub'},
                    {data:'item_quantity', name: 'sis.quantity', class: 'align-middle text-center item_quantity'},
                    {data:'product_name', name: 'prod.product_name', class: 'align-middle text-center product_name'},
                    {data:'shipping_mode', name: 'sm.id', class: 'align-middle text-center shipping_mode'},
                    {data:'actual_weight', name: 'shipments.actual_weight', class: 'align-middle text-center actual_weight'},
                    {data:'chargeable_weight', name: 'shipments.chargeable_weight', class: 'align-middle text-center chargeable_weight'},
                    {data:'weight_charges', name: 'shipments.weight_charges', class: 'align-middle text-center weight_charges'},
                    {data:'fuel_surcharge', name: 'shipments.fuel_surcharge', class: 'align-middle text-center fuel_surcharge'},
                    {data:'return_charges', name: 'shipments.return_charges', class: 'align-middle text-center return_charges'},
                    {data:'gst', name: 'shipments.gst', class: 'align-middle text-center gst'},
                    {data:'charges', name: 'charges', class: 'align-middle text-center charges', orderable: false, searchable: false},
                    {data:'charges_modes', name: 'cm.id', class: 'align-middle text-center charges_modes'},
                    {data:'status', name: 'ss.id', class: 'align-middle text-center status'},
                    {data:'status_updated_at', name: 'sj.updated_at', class: 'align-middle text-center status_updated_at'},
                    {data:'updated_by', name: 'a.name', class: 'align-middle text-center updated_by'},
                    {data:'created_at', name: 'shipments.created_at', class: 'align-middle text-center created_at'},
                    {data:'walk_in_status', name: 'shipments.walk_in_status', class: 'align-middle text-center walk_in_status'},
                    {data:'aging', name: 'aging', class: 'align-middle text-center aging', orderable: false, searchable: false},
                    {data:'resolved_at', name: 'ros.created_at', class: 'align-middle text-center resolved_at'},
                    {data:'resolved_by', name: 'rosa.name', class: 'align-middle text-center resolved_by'},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    if(((data.charges_mode_id === 1) || (data.charges_mode_id === 2 && (data.shipper_status_id === 14 || data.shipper_status_id === 25))) && (data.walk_in_status !== 1)){
                        $('td:eq(0)', row).addClass('select-checkbox');
                    }

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var status_walk_in_select = '<select name="status_walk_in_select" id="status_walk_in_select" class="select2 form-control"></select>';
                    var charges_mode = '<select name="charges_mode" id="charges_mode" class="select2 form-control"></select>';
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';
                    var shipping_mode = '<select name="shipping_mode" id="shipping_mode" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.charges') || $(header).is('.aging') || $(header).is('.action')) {
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
                        }else if($(header).is('.walk_in_status')){
                            $(status_walk_in_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.charges_modes')){
                            $(charges_mode).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.shipping_mode')){
                            $(shipping_mode).appendTo($(search))
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

                    var data2 = $.map({!! $charges_mode_name !!}, function (obj) {
                        obj.id = obj.id;
                        return obj;
                    });
                    var data2 = $.map({!! $charges_mode_name !!}, function (obj) {
                        obj.text = obj.charges_mode;

                        return obj;
                    });

                    $("#charges_mode").prepend('<option value="" selected></option>').select2({
                        data:data2,
                        placeholder: "Charges Modes",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    var data3 = $.map({!! $shipping_modes !!}, function (obj) {
                        obj.id = obj.id;
                        return obj;
                    });
                    var data3 = $.map({!! $shipping_modes !!}, function (obj) {
                        obj.text = obj.mode;

                        return obj;
                    });

                    $("#shipping_mode").prepend('<option value="" selected></option>').select2({
                        data:data3,
                        placeholder: "Shipping Modes",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });


                    $("#status_walk_in_select").prepend('<option value="" selected></option>').select2({
                        data:{!! $status !!},
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                var tracking_number = $(this).parents('tr').children('td.tracking_number').text();

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
                                url: '{!! route('admin.finance.outstanding_shipments.walk_in_resolved') !!}',
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
                    table.button('.bulk_resolved').enable();
                }
                else {
                    table.button('.bulk_resolved').disable();
                }
            });
            $('#track_form').bind('submit',function (e) {
                e.preventDefault();
                var tracking_numbers = $('#track_form .tracking_numbers').val();
                if (tracking_numbers != '') {
                    table.draw();
                }
            });
        });
    </script>
@endsection