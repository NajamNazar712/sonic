
@extends('admin.layout.master')
@section('title','Replacement Not Collected')

@section('content')
    <h1 class="mb-1">
        Replacement Not Collected
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
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Consignee Name</th>
                        <th class="border-primary border-darken-1">Phone</th>
                        <th class="border-primary border-darken-1">Address</th>
                        <th class="border-primary border-darken-1">Collection Amount</th>
                        <th class="border-primary border-darken-1">Reason</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
{{--    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">--}}


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
            border-color: #64a0d2;
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
        .selectize-control {
            width: 100%;
        }

        .selectize-control .selectize-input {
            vertical-align: middle;
        }

        .selectize-control .selectize-input .item {
            word-break: break-all;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
{{--    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>--}}
{{--    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>--}}
{{--    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {


            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.delivery.replacement.not_collected.list') }}',
                        data: {
                            'page': 'all',
                        },
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Tracking No.');
                            head.push('Destination');
                            head.push('Consignee Name');
                            head.push('Phone');
                            head.push('Address');
                            head.push('Collection Amount');
                            head.push('Reason');
                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.destination);
                                row.push(values.consignee_name);
                                row.push(values.phone);
                                row.push(values.consignee_address);
                                row.push(values.cod_amount);
                                row.push(values.reason_name);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            var selected_rows = [];
            var shipment_amount= {};
            var shipment_reason= {};

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        text: 'Re-Attempt',
                        className: 'btn btn-primary re_attempt',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            swal({
                                text: 'Are you sure, you want to mark these Shipment(s) for Re-Attempt?',
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
                                    table.rows().nodes().each(function(index) {
                                        var row = table.row(index);
                                        if ($(row.node()).hasClass('selected')) {
                                            var id = parseInt(row.id());
                                            var amount = $(row.node()).find('td.amount input').val();
                                            var selected_reason = $(row.node('.reason')).find(':selected');
                                            var reason = parseInt(selected_reason.val());
                                            shipment_amount[id] = amount;
                                            shipment_reason[id] = reason;
                                        }
                                    });
                                    $.ajax({
                                        url: '{!! route('admin.delivery.replacement.not_collected.re_attempt') !!}',
                                        method: 'POST',
                                        data: {
                                            '_token': '{{ csrf_token() }}',
                                            'shipment_ids': selected_rows,
                                            'shipment_amount': shipment_amount,
                                            'shipment_reason': shipment_reason
                                        }
                                    })
                                        .done(function(data) {
                                            if (data.status == 1) {
                                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                            }
                                            else {
                                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                            }

                                            table.button('.re_attempt').disable();
                                            table.button('.regular_re_attempt').disable();

                                            selected_rows = [];
                                            amounts = [];
                                            reasons = [];

                                            table.rows().deselect();

                                            table.draw('false');
                                        });
                                }
                            });
                        }
                    },{
                        text: 'Re-Attempt (Regular)',
                        className: 'btn btn-primary regular_re_attempt',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            swal({
                                text: 'Are you sure, you want to change service type to Regular and mark these Shipment(s) for Re-Attempt?',
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
                                    table.rows().nodes().each(function(index) {
                                    var row = table.row(index);
                                    if ($(row.node()).hasClass('selected')) {
                                        var id = parseInt(row.id());
                                        var amount = $(row.node()).find('td.amount input').val();
                                        var selected_reason = $(row.node('.reason')).find(':selected');
                                        var reason = parseInt(selected_reason.val());
                                        shipment_amount[id] = amount;
                                        shipment_reason[id] = reason;
                                    }
                                });
                                    $.ajax({
                                        url: '{!! route('admin.delivery.replacement.not_collected.regular_re_attempt') !!}',
                                        method: 'POST',
                                        data: {
                                            '_token': '{{ csrf_token() }}',
                                            'shipment_ids': selected_rows,
                                            'shipment_amount': shipment_amount,
                                            'shipment_reason': shipment_reason
                                        }
                                    })
                                        .done(function(data) {
                                            if (data.status == 1) {
                                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                            }
                                            else {
                                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                            }

                                            table.button('.re_attempt').disable();
                                            table.button('.regular_re_attempt').disable();

                                            selected_rows = [];
                                            amounts = [];
                                            reasons = [];

                                            table.rows().deselect();

                                            table.draw('false');
                                        });
                                }
                            });
                        }
                    },
                    {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox') && !$(row.node()).hasClass('selected')) {
                                    id = parseInt(row.id());
                                    row.select();

                                    var index = $.inArray(id, selected_rows);

                                    if (index === -1) {
                                        selected_rows.push(id);
                                    }

                                    table.button('.re_attempt').enable();
                                    table.button('.regular_re_attempt').enable();
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

                                if ($(row.node().firstChild).hasClass('select-checkbox') && $(row.node()).hasClass('selected')) {
                                    row.deselect();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index !== -1) {
                                        selected_rows.splice(index, 1);
                                    }

                                    if (selected_rows.length == 0) {
                                        table.button('.re_attempt').disable();
                                        table.button('.regular_re_attempt').disable();
                                    }
                                }
                            });
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Replacement Not Collected',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                scrollX: true, scrollY: '350px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                autoWidth: false,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: '{{ route('admin.delivery.replacement.not_collected.list') }}',
                rowId: 'shipment_id',
                order: [[1, 'desc']],
                columns: [
                    {data: 'shipment_id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number_link', name: 'shipments.tracking_number', class: 'align-middle tracking_number_link'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'consignee_name', name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                    {data: 'phone', name: 'shipments.consignee_phone_number_1', class: 'align-middle phone'},
                    {data: 'consignee_address', name: 'shipments.consignee_address', class: 'align-middle consignee_address'},
                    {data: 'amount', name: 'shipments.amount', class: 'align-middle amount form-group'},
                    {data: 'reason', name: 'reason', class: 'align-middle reason custom-col-width form-group'},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    if ($.inArray(data.shipment_id, selected_rows) !== -1) {
                        table.row(row).select();
                    }

                },
                drawCallback: function (settings) {
                    $(".reason_select").prepend('<option value="" selected="selected"></option>').select2({
                        placeholder: "Select Reason",
                        width:'100%'
                    });
                    $('.col_amount').inputmask({
                        'alias': 'integer',
                        'allowMinus': false,
                        'allowPlus': false,
                        'rightAlign': false,
                    });
                    var api = new $.fn.dataTable.Api( settings );
                    var data = api.rows( {page:'current'} ).data();
                    $.each(data,function (key,value) {
                            $('select[name="reason['+value.shipment_id+']"]').val(value.reason_id).trigger('change');
                    });
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.select') || $(header).is('.reason')) {
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

                    $(".reason_select").on('change', function(){
                        table.columns.adjust().draw();
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
                            table.button('.re_attempt').enable();
                            table.button('.regular_re_attempt').enable();
                        }
                        else {
                            table.button('.re_attempt').disable();
                            table.button('.regular_re_attempt').disable();
                        }

                    });
                    this.api().table().columns.adjust();
                }
            });


        });
    </script>
@endsection