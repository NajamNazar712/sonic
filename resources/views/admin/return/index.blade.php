@extends('admin.layout.master')
@section('title','Return Marked Shipments')

@section('content')
    <h1 class="mb-1">
        Return Marked Shipments
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
                        <th class="border-primary border-darken-1">Shipper Name</th>
                        <th class="border-primary border-darken-1">Shipper Phone(s)</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Consignee Name</th>
                        <th class="border-primary border-darken-1">Consignee Phone</th>
                        <th class="border-primary border-darken-1">Address</th>
                        <th class="border-primary border-darken-1">Collection Amount</th>
                        <th class="border-primary border-darken-1">Shipping Mode</th>
                        <th class="border-primary border-darken-1">Service Type</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Reason</th>
                        <th class="border-primary border-darken-1">Remarks</th>
                        <th class="border-primary border-darken-1">Arrival Date</th>
                        <th class="border-primary border-darken-1">Status Date</th>
                        <th class="border-primary border-darken-1">Re-Attempt Count</th>
                        <th class="border-primary border-darken-1">Action</th>
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
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    {{--    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.return.list') }}',
                        data: {
                            'page': 'all',
                        },
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Tracking No.');
                            head.push('Order ID');
                            head.push('Shipper Name / Phone');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Hub');
                            head.push('Consignee Name / Phone');
                            head.push('Address');
                            head.push('Collection Amount');
                            head.push('Shipping Mode');
                            head.push('Service Type');
                            head.push('Status');
                            head.push('Reason');
                            head.push('Remarks');
                            head.push('Arrival Date');
                            head.push('Status Date');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.tracking);
                                row.push(values.order_id);
                                row.push(values.shipper);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.hub);
                                row.push(values.consignee_name);
                                row.push(values.consignee_address);
                                row.push(values.amount);
                                row.push(values.mode);
                                row.push(values.service_type);
                                row.push(values.status);
                                row.push(values.reason);
                                row.push(values.remarks);
                                row.push(values.arrival);
                                row.push(values.last_status_date);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            var selected_rows = [];
            var shipment_remarks = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                @if (session('role_id') == 1 || count(array_intersect([45, 46], session('permissions'))) !== 0)

                    buttons: [
                    @if (session('role_id') == 1 || in_array(45, session('permissions')))
                        {
                            text: 'Confirm',
                            className: 'btn btn-primary confirm',
                            enabled: false,
                            action: function (e, dt, node, config) {
                                if(selected_rows !== ''){
                                    swal({
                                        title: 'Are You Sure?',
                                        text: 'Select Yes to change shipment status to Return-Confirm!',
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
                                    }).then(function (confirm) {
                                        if (confirm) {

                                            table.rows().nodes().each(function(index) {
                                                var row = table.row(index);

                                                if ($(row.node()).hasClass('selected')) {
                                                    id = parseInt(row.id());
                                                    var remark = $(row.node()).find('td.shipment_remarks input').val();
                                                    shipment_remarks[id] = remark;
                                                }
                                            });

                                            $.ajax({
                                                url:"{{route('admin.return.marked.status')}}",
                                                method:'POST',
                                                data:{
                                                    'shipment_ids':selected_rows,
                                                    '_token':'{{ csrf_token() }}',
                                                    'action': 'confirm',
                                                    'remark': shipment_remarks
                                                }
                                            }).done(function (data) {
                                                table.rows().deselect();
                                                selected_rows = [];
                                                shipment_remarks = [];
                                                table.button('.confirm').disable();
                                                table.button('.re-attempt').disable();
                                                table.draw('false');
                                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                                            });
                                        }
                                    });


                                }else{
                                    var error = "Not selected any shipments!";
                                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                            }
                        },
                    @endif

                    @if (session('role_id') == 1 || in_array(46, session('permissions')))
                        {
                            text: 'Re-Attempt',
                            className: 'btn btn-primary re-attempt',
                            enabled: false,
                            action: function (e, dt, node, config) {
                                if(selected_rows != ''){
                                    swal({
                                        title: 'Are You Sure?',
                                        text: 'Select Yes to change shipment status to Re-Attempt!',
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
                                    }).then(function (confirm) {
                                        if (confirm) {
                                            table.rows().nodes().each(function(index) {
                                                var row = table.row(index);

                                                if ($(row.node()).hasClass('selected')) {
                                                    id = parseInt(row.id());
                                                    var remark = $(row.node()).find('td.shipment_remarks input').val();
                                                    shipment_remarks[id] = remark;
                                                }
                                            });

                                            $.ajax({
                                                url:"{{route('admin.return.marked.status')}}",
                                                method:'POST',
                                                data:{
                                                    'shipment_ids':selected_rows,
                                                    '_token':'{{ csrf_token() }}',
                                                    'action': 'reattempt',
                                                    'remark': shipment_remarks
                                                }
                                            }).done(function (data) {
                                                selected_rows = [];
                                                shipment_remarks = [];
                                                table.button('.confirm').disable();
                                                table.button('.re-attempt').disable();
                                                table.draw('false');
                                                table.rows().deselect();
                                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                                            });
                                        }
                                    });

                                }
                            }
                        },
                    @endif
                        {
                            extend: 'excel',
                            title: 'Return Marked',
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

                                    if ($(row.node().firstChild).hasClass('select-checkbox') && !$(row.node()).hasClass('selected')) {
                                        id = parseInt(row.id());

                                        hub_id = $(row.node()).data('hub');

                                        var allow = false;

                                        if(hub_ids.length == 0) {
                                            hub_ids.push(hub_id);

                                            allow = true;
                                        }
                                        else if(hub_ids[0] == hub_id) {
                                            allow = true;
                                        }

                                        if (allow) {
                                            row.select();

                                            var index = $.inArray(id, selected_rows);

                                            if (index === -1) {
                                                selected_rows.push(id);
                                            }

                                            table.button('.confirm').enable();
                                            table.button('.re-attempt').enable();
                                        }
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
                                        table.button('.confirm').disable();
                                        table.button('.re-attempt').disable();

                                        hub_ids.splice(index, 1);
                                    }
                                  }
                                });
                            }
                        }
                        ],
                @else
                   buttons:[{
                    extend: 'excel',
                    title: 'Return Marked',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                }],
                @endif
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
                serverSide: true,
                ajax: '{{ route('admin.return.list') }}',
                rowId: 'shId',
                order: [[17, 'asc'], [16, 'asc']],
                columns: [
                    {data: 'shId', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'id',defaultContent:'', orderable: false, searchable: false, class: 'align-middle serial_number'},
                    {data: 'tracking_number', name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'order_id', name: 'shipments.order_id', class: 'align-middle order_id'},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'shipper_phone', name: 'shipper_phone', class: 'align-middle shipper_phone'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'hub', name: 'h.name', class: 'align-middle hub'},
                    {data: 'consignee_name', name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                    {data: 'consignee_phone_number_1', name: 'shipments.consignee_phone_number_1', class: 'align-middle consignee_phone_number_1'},
                    {data: 'consignee_address', name: 'shipments.consignee_address', class: 'align-middle consignee_address'},
                    {data: 'amount', name: 'shipments.amount', class: 'align-middle amount'},
                    {data: 'mode', name: 'sm.id', class: 'align-middle mode'},
                    {data: 'service_type', name: 'bt.id', class: 'align-middle service_type'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'reason', name: 'ssr.name', class: 'align-middle reason'},
                    {data: 'shipment_remarks', name: 'shipments_journey.remarks', class: 'align-middle shipment_remarks'},
                    {data: 'arrival', name: 'sj.created_at', class: 'align-middle arrival'},
                    {data: 'status_date', name: 'shipments_journey.created_at', class: 'align-middle status_date'},
                    {data: 'reattempts', name: 'sret.created_at', class: 'align-middle reattempts'},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}

                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    if ($.inArray(data.shId, selected_rows) !== -1) {
                        table.row(row).select();
                    }
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';
                    var mode_drop_select = '<select name="mode_select" id="mode_select" class="select2 form-control"></select>';
                    var service_drop_select = '<select name="service_select" id="service_select" class="select2 form-control"></select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.action') || $(header).is('.shipment_remarks')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.status')){
                            $(drop_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.mode')){
                            $(mode_drop_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.service_type')){
                            $(service_drop_select).appendTo($(search))
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
                        obj.id = obj.id // replace pk with your identifier
                        obj.text = obj.text || obj.name;
                        return obj;
                    });

                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        data:data,
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data1 = $.map({!! $shipping_mode !!}, function (obj) {
                        obj.id = obj.id
                        obj.text = obj.mode;
                        return obj;
                    });

                    $("#mode_select").prepend('<option value="" selected></option>').select2({
                        data:data1,
                        placeholder: "Select Mode",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data2 = $.map({!! $service_type !!}, function (obj) {
                        obj.id = obj.id;
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
            var hub_ids = [];

            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {

                var id = parseInt($(this).parent('tr').attr('id'));
                var hub_id = $(this).parents('tr').data('hub');
                if(hub_ids.length == 0){
                    hub_ids.push(hub_id);
                    var index = $.inArray(id, selected_rows);

                    if (index === -1) {
                        selected_rows.push(id);
                    }
                    else {
                        selected_rows.splice(index, 1);
                    }

                    if (selected_rows.length > 0) {
                        table.button('.confirm').enable();
                        table.button('.re-attempt').enable();
                    }
                    else {
                        table.button('.confirm').disable();
                        table.button('.re-attempt').disable();
                    }
                }else{
                    if(hub_ids[0] == hub_id){
                        var index = $.inArray(id, selected_rows);

                        if (index === -1) {
                            selected_rows.push(id);
                        }
                        else {
                            selected_rows.splice(index, 1);
                        }

                        if (selected_rows.length > 0) {
                            table.button('.confirm').enable();
                            table.button('.re-attempt').enable();
                        }
                        else {
                            table.button('.confirm').disable();
                            table.button('.re-attempt').disable();
                        }
                    }else{
                        var error = "Selected hubs should be the same!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        return false;
                    }

                }

            });
            $('body').on('click','.returnMarkStatus',function () {
                var action = $(this).data('action');
                var row_id = $(this).parents('tr').attr('id');
                var remark = $(this).parents('tr').find('td.shipment_remarks input').val();
                if(action === 'confirm'){
                    atext = 'Select Yes to change shipment status to Return-Confirm!';
                }else if(action === 'reattempt'){
                    atext = 'Select Yes to change shipment status to Re-Attempt!';
                }

                if(row_id != '' && action != ''){
                    swal({
                        title: 'Are You Sure?',
                        text: atext,
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
                    }).then(function (confirm) {
                        if (confirm) {
                            $.ajax({
                                url:"{{route('admin.return.marked.status.single')}}",
                                method:'POST',
                                data:{
                                    'shipment_id':row_id,
                                    '_token':'{{ csrf_token() }}',
                                    'action': action,
                                    'remark':remark
                                }
                            }).done(function (data) {
                                if(data.status == 1){
                                    table.draw('false');
                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                                }else{
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                                }

                            });
                        }
                    });


                }
            });
        });
    </script>
@endsection