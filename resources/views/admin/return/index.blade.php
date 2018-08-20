@extends('admin.layout.master')

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
                        <th class="border-primary border-darken-1">Shipper Name / Phone</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Consignee Name / Phone</th>
                        <th class="border-primary border-darken-1">Address</th>
                        <th class="border-primary border-darken-1">COD Amount</th>
                        <th class="border-primary border-darken-1">Shipping Mode</th>
                        <th class="border-primary border-darken-1">Service Type</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Reason</th>
                        <th class="border-primary border-darken-1">Remarks</th>
                        <th class="border-primary border-darken-1">Arrival Date</th>
                        <th class="border-primary border-darken-1">Status Date</th>
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
            border-color: #666EE8;
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
            var selected_rows = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX:true,
                buttons: [{
                    text: 'Confirm',
                    className: 'btn btn-primary confirm',
                    enabled: false,
                    action: function (e, dt, node, config) {
                        if(selected_rows != ''){
                            $.ajax({
                                url:"{{route('admin.return.marked.status')}}",
                                method:'POST',
                                data:{
                                    'shipment_ids':selected_rows,
                                    '_token':'{{ csrf_token() }}',
                                    'action': 'confirm'
                                }
                            }).done(function (data) {
                                $.each(selected_rows, function(index, id) {
                                    table.row($('#datatable tbody tr#' + id)).deselect();
                                });
                                selected_rows = [];
                                table.button(0).disable();
                                table.button(1).disable();
                                table.ajax.reload();
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                            });

                        }else{
                            var error = "Not selected any shipments!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        }
                    }
                    }, {
                        text: 'Re-Attempt',
                        className: 'btn btn-primary re-attempt',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            if(selected_rows != ''){
                                $.ajax({
                                    url:"{{route('admin.return.marked.status')}}",
                                    method:'POST',
                                    data:{
                                        'shipment_ids':selected_rows,
                                        '_token':'{{ csrf_token() }}',
                                        'action': 'reattempt'
                                    }
                                }).done(function (data) {
                                    selected_rows = [];
                                    table.button(0).disable();
                                    table.button(1).disable();
                                    table.ajax.reload();
                                    $.each(selected_rows, function(index, id) {
                                        table.row($('#datatable tbody tr#' + id)).deselect();
                                    });
                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                                });
                            }
                        }
                }],
                fixedHeader: {
                    header: true,
                    headerOffset: $('.header-navbar').height()
                },
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                lengthMenu: [[25, 50, 100], [25, 50, 100]],
                pageLength: 25,
                stateSave: true,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.return.list') }}',
                rowId: 'shId',
                order: [[2, 'asc']],
                columns: [
                    {data: 'shId', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'id',defaultContent:'', orderable: false, searchable: false, class: 'align-middle serial_number'},
                    {data: 'tracking_number', name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'order_id', name: 'shipments.order_id', class: 'align-middle order_id'},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'origin', name: 'on.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'hub', name: 'h.name', class: 'align-middle hub'},
                    {data: 'consignee_name', name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                    {data: 'consignee_address', name: 'shipments.consignee_address', class: 'align-middle consignee_address'},
                    {data: 'amount', name: 'shipments.amount', class: 'align-middle amount'},
                    {data: 'mode', name: 'sm.mode', class: 'align-middle mode'},
                    {data: 'service_type', name: 'bt.booking_type', class: 'align-middle service_type'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'reason', name: 'reason', class: 'align-middle reason'},
                    {data: 'remarks', name: 'shipments_journey.remarks', class: 'align-middle remarks'},
                    {data: 'arrival', name: 'arrival', class: 'align-middle arrival'},
                    {data: 'status_date', name: 'shipments_journey.created_at', class: 'align-middle status_date'},
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

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.action')) {
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
                        table.button(0).enable();
                        table.button(1).enable();
                    }
                    else {
                        table.button(0).disable();
                        table.button(1).disable();
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
                            table.button(0).enable();
                            table.button(1).enable();
                        }
                        else {
                            table.button(0).disable();
                            table.button(1).disable();
                        }
                    }else{
                        var error = "Selected hubs should be the same!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        return false;
                    }

                }

            });
            $('body').on('click','.returnMarkStatus',function () {
                var action = $(this).data('action');
                var row_id = $(this).parents('tr').attr('id');
                if(row_id != '' && action != ''){
                        $.ajax({
                            url:"{{route('admin.return.marked.status.single')}}",
                            method:'POST',
                            data:{
                                'shipment_id':row_id,
                                '_token':'{{ csrf_token() }}',
                                'action': action
                            }
                        }).done(function (data) {
                           if(data.status == 1){
                               table.ajax.reload();
                               toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                           }else{
                               toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                           }

                        });

                }
            });
        });
    </script>
@endsection