@extends('retail.layout.master')
@section('title','Return Confirmation Pending Shipments')

@section('content')
    <h1 class="mb-1">
        Return Confirmation Pending Shipments
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('retail.inc.messages')

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Order ID</th>
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
                        <th class="border-primary border-darken-1">OSA Estimated Charges</th>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">



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
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('retail.return.confirmation_pending.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Tracking No.');
                            head.push('Order ID');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Hub');
                            head.push('Consignee Name');
                            head.push('Consignee Phone');
                            head.push('Address');
                            head.push('Collection Amount');
                            head.push('Shipping Mode');
                            head.push('Service Type');
                            head.push('Status');
                            head.push('Reason');
                            head.push('Remarks');
                            head.push('OSA Estimated Charges');
                            head.push('Arrival Date');
                            head.push('Status Date');
                            // head.push('Consolidation');
                            // head.push('Consolidation ID');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.tracking);
                                row.push(values.order_id);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.hub);
                                row.push(values.consignee_name);
                                row.push(values.consignee_phone_number_1 + '|' + values.consignee_phone_number_2);
                                row.push(values.consignee_address);
                                row.push(values.amount);
                                row.push(values.mode);
                                row.push(values.service_type);
                                row.push(values.status);
                                row.push(values.reason);
                                row.push(values.remarks);
                                row.push(values.nsa_osa_estimated_charges);
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
            var shipment_remarks = {};
            var selected_rows = [];
            var selected_rows_nsa = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Return Confirmation Pending',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                   
                    'reset'
                ],

                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: '{{ route('retail.return.confirmation_pending.list') }}',
                rowId: 'shId',
                order: [[18, 'desc']],
                columns: [
                    {data: 'id',defaultContent:'', orderable: false, searchable: false, class: 'align-middle serial_number'},
                    {data: 'tracking_number', name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'order_id', name: 'shipments.order_id', class: 'align-middle order_id'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'hub', name: 'h.name', class: 'align-middle hub'},
                    {data: 'consignee_name', name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                    {data: 'consignee_phone', name: 'consignee_phone', class: 'align-middle consignee_phone'},
                    {data: 'consignee_address', name: 'shipments.consignee_address', class: 'align-middle consignee_address'},
                    {data: 'amount', name: 'shipments.amount', class: 'align-middle amount'},
                    {data: 'mode', name: 'sm.id', class: 'align-middle mode'},
                    {data: 'service_type', name: 'bt.id', class: 'align-middle service_type'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'reason', name: 'ssr.name', class: 'align-middle reason'},
                    {data: 'shipment_remarks', name: 'shipments_journey.remarks', class: 'align-middle shipment_remarks', orderable: false, searchable: false},
                    {data: 'nsa_osa_estimated_charges', name: 'nsa_osa_estimated_charges', class: 'align-middle nsa_osa_estimated_charges'},
                    {data: 'arrival', name: 'sj.created_at', class: 'align-middle arrival'},
                    {data: 'status_date', name: 'shipments_journey.created_at', class: 'align-middle status_date'},
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
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';
                    var mode_drop_select = '<select name="mode_select" id="mode_select" class="select2 form-control"></select>';
                    var service_drop_select = '<select name="service_select" id="service_select" class="select2 form-control"></select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.action')) {
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
                        obj.id = obj.id;
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
                    var data1 = $.map({!! $shipping_mode !!}, function (obj) {
                        obj.id = obj.id;
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

            $('#submit_nsa').on('click', function () {
                var new_selected_rows = selected_rows;
                var nsa = null;
                selected_rows_nsa.forEach(function (index) {
                    if($('input[name="confirm['+index+']"]').prop('checked') === false){
                        var key = $.inArray(index, new_selected_rows);
                        new_selected_rows.splice(key, 1);
                    }
                });
                table.rows().nodes().each(function (index) {
                    var row = table.row(index);
                    if ($(row.node()).hasClass('selected')) {
                        var id = parseInt(row.id());
                        var remarks = $(row.node()).find('td.shipment_remarks textarea').val();
                        shipment_remarks[id] = remarks;
                    }

                });
                $.ajax({
                    url: "{{route('cod.return.pending.reattempt.status')}}",
                    method: 'POST',
                    data: {
                        'shipment_ids': new_selected_rows,
                        '_token': '{{ csrf_token() }}',
                        'remark': shipment_remarks
                    }
                }).done(function (data) {
                    table.rows().deselect();
                    selected_rows = [];
                    new_selected_rows = [];
                    shipment_remarks = {};
                    table.button('.reattempt').disable();
                    table.draw('false');
                    if (data.not_updated_shipments.length > 0) {
                        var alert_icon = 'warning';
                        var html = '';
                        if (data.updated_shipments.length > 0) {
                            alert_icon = 'success';
                            $.each(data.updated_shipments, function (index, tracking_number) {
                                html += tracking_number + '<br/>';
                            });
                            html += '<br/>Shipment(s) has been requested for Re-Attempt, Please note that this is subjected to final confirmation by Customer Experience!</br><hr>';

                        }
                        $.each(data.not_updated_shipments, function (index, tracking_number) {
                            html += tracking_number + '<br/>';
                        });
                        html += '<br/>Shipment(s) are already updated';
                        content = document.createElement('div');
                        content.innerHTML = html;
                        swal({
                            title: 'Shipments Re-Attempt Requested',
                            content: content,
                            icon: alert_icon,
                            buttons: {
                                cancel: {
                                    text: 'Close',
                                    value: null,
                                    visible: true,
                                    closeModal: true,
                                }
                            },
                            closeOnClickOutside: true,
                            closeOnEsc: false,
                            dangerMode: true
                        });
                    } else {
                        toastr.success(data.success, 'Success!', {
                            positionClass: 'toast-bottom-center',
                            containerId: 'toast-bottom-center'
                        });

                    }


                });
            });

            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {

                var id = parseInt($(this).parent('tr').attr('id'));
                var hub_id = $(this).parents('tr').data('hub');
                var con_id = parseInt($(this).parent('tr').attr('consolidation_id'));
                if(con_id){
                    if(hub_ids.length == 0){
                        hub_ids.push(hub_id);
                    }else if(hub_ids[0] != hub_id){
                        var error = "Selected hubs should be the same!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        return false;
                    }
                    table.rows().nodes().each(function(index) {
                        var row = table.row(index);
                        if ($(row.node()).attr('consolidation_id') == con_id) {
                            var rid = parseInt($(row.node()).attr('id'));
                            var rindex = $.inArray(rid, selected_rows);

                            if (rindex === -1) {
                                selected_rows.push(rid);
                                if(id != rid){

                                    table.row(row).select();
                                }
                            }
                            else {
                                if(id != rid){

                                    row.deselect();
                                }
                                selected_rows.splice(rindex, 1);
                            }
                            if (selected_rows.length > 0) {
                                table.button('.confirm').enable();
                                table.button('.reattempt').enable();
                            }
                            else {
                                table.button('.confirm').disable();
                                table.button('.reattempt').disable();
                            }
                        }
                    });
                }else{
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
                            table.button('.reattempt').enable();
                        }
                        else {
                            table.button('.confirm').disable();
                            table.button('.reattempt').disable();
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
                                table.button('.reattempt').enable();
                            }
                            else {
                                table.button('.confirm').disable();
                                table.button('.reattempt').disable();
                            }
                        }else{
                            var error = "Selected hubs should be the same!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            return false;
                        }

                    }
                }

            });

            $('body').on('click','.returnMarkStatus',function () {
                var row_id = $(this).parents('tr').attr('id');
                var remark = $(this).parents('tr').find('td.shipment_remarks textarea').val();


                if(row_id != ''){
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
                            $.ajax({
                                url:"{{route('cod.return.pending.marked.status.single')}}",
                                method:'POST',
                                data:{
                                    'shipment_id':row_id,
                                    '_token':'{{ csrf_token() }}',
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
            $('body').on('click','.returnReattemptStatus',function () {
                var row_id = $(this).parents('tr').attr('id');
                var remark = $(this).parents('tr').find('td.shipment_remarks textarea').val();

                if(row_id != ''){
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to request shipment for Re-Attempt!',
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
                                url: "{{route('retail.return.pending_reattempt_nsa')}}",
                                method: 'POST',
                                data: {
                                    'shipment_ids': row_id,
                                    '_token': '{{ csrf_token() }}',
                                    'single' : 1
                                }
                            }).done(function (data) {
                                if(data.status == 1){
                                    var html = '';
                                    html += 'Out of Service Area / Non Service Area Shipment against Tracking Number: '+ data.nsa_shipment +'<br/><br/>';
                                    html += '<b>Estimated Charges: '+ data.estimated_charge +'</b><br/><br/>';
                                    html += 'Are you sure you want to deliver shipment with additional charges?';
                                    content = document.createElement('div');
                                    content.innerHTML = html;
                                    swal({
                                        content: content,
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
                                        if(confirm){
                                            $.ajax({
                                                url:"{{route('retail.return.mark_reattempt')}}",
                                                method:'POST',
                                                data:{
                                                    'shipment_id':row_id,
                                                    '_token':'{{ csrf_token() }}',
                                                    'remark':remark
                                                }
                                            }).done(function (data) {
                                                if(data.status == 1){
                                                    table.draw('false');
                                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                                                }else{
                                                    table.draw('false');
                                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                                                }

                                            });
                                        }
                                    })
                                }
                                else{
                                    $.ajax({
                                        url:"{{route('retail.return.mark_reattempt')}}",
                                        method:'POST',
                                        data:{
                                            'shipment_id':row_id,
                                            '_token':'{{ csrf_token() }}',
                                            'remark':remark
                                        }
                                    }).done(function (data) {
                                        if(data.status == 1){
                                            table.draw('false');
                                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                                        }else{
                                            table.draw('false');
                                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                                        }

                                    });
                                }
                            });
                        }
                        {{--if (confirm) {--}}

                        {{--}--}}
                    });


                }
            });


            $('body').on('click','.intercept',function () {
                var row_id = $(this).parents('tr').attr('id');

                if(row_id != ''){
                    var redirect = '{!! route('cod.intercept.index', ':id') !!}';
                    var url = redirect.replace(':id', row_id);
                    window.open(url);
                }
            });

            $('#datatable').on('click', '.selfCollection', function () {
                var row_id = $(this).parents('tr').attr('id');
                var remark = $.trim($('tr#' + row_id).find('td.shipment_remarks textarea').val());
                if(row_id){
                    swal({
                        text: 'Are you sure you want to mark shipment for Self-Collection?',
                        icon: 'info',
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
                            blockPagePermanently();
                            $.ajax({
                                url:"{{route('cod.return.pending.marked.self_collection')}}",
                                method:'POST',
                                data:{
                                    'shipment_id':row_id,
                                    'remark': remark,
                                    '_token':'{{ csrf_token() }}',
                                }
                            }).done(function (data) {
                                UnblockPagePermanently();
                                if(data.status == 1){
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }else{
                                    table.draw(false);
                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                }
                            });
                        }
                    });
                }
            });
            $('body').on('click', 'button.consignee_info_label', function () {
                var phone = $(this).attr('rel');
                if(phone){
                    $.ajax({
                        url: '{!! route('cod.return.pending.consignee') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'phone': phone
                        }
                    })
                        .done(function(data) {
                            if (data.status == 0) {
                                details = data.details;
                                $('#consignee_information_id').val(details.consignee.id);
                                var html = '<div class="row mb-1">';

                                html += '<div class="col-4">Consignee Name :</div><div class="col-8">'+ details.consignee.name +'</div>';
                                html += '<div class="col-4">Consignee Phone Number 1 :</div><div class="col-8">'+ details.consignee.phone +'</div>';
                                var consignee_phone = '';
                                if(details.consignee.phone2 != null){
                                    consignee_phone = details.consignee.phone2;
                                }
                                html += '<div class="col-4">Consignee Phone Number 2 :</div><div class="col-8">'+ consignee_phone +'</div>';
                                html += '<div class="col-4">Consignee Address :</div><div class="col-8">'+ details.consignee.address +'</div>';
                                html += '<div class="col-4">Consignee City :</div><div class="col-8">'+ details.consignee.city +'</div>';

                                html += '</div>';
                                if ('blacklist' in details) {
                                    html += '<div class="row p-1" style="background-color: '+ details.blacklist.color +'; color:white;">';
                                    html += '<div class="col-12">';
                                    html += '<table class="table table-sm table-bordered mb-0">';
                                    html += '<tbody>';
                                    html += '<tr>';
                                    html += '<td><strong>Total Shipments</strong></td>';
                                    html += '<td><strong>Delivered</strong></td>';
                                    html += '<td><strong>Ratio</strong></td>';
                                    html += '<td><strong>Undelivered</strong></td>';
                                    html += '<td><strong>Ratio</strong></td>';
                                    html += '<td><strong>Return Confirmed</strong></td>';
                                    html += '<td><strong>Ratio</strong></td>';
                                    html += '</tr>';
                                    html += '<tr>';
                                    html += '<td>' + details.blacklist.total_shipments + '</td>';
                                    html += '<td>' + details.blacklist.delivered + '</td>';
                                    html += '<td>' + details.blacklist.delivered_ratio + ' %</td>';
                                    html += '<td>' + details.blacklist.undelivered + '</td>';
                                    html += '<td>' + details.blacklist.undelivered_ratio + ' %</td>';
                                    html += '<td>' + details.blacklist.return + '</td>';
                                    html += '<td>Rs. ' + details.blacklist.return_ratio + ' %</td>';
                                    html += '</tr>';
                                    html += '</tbody>';
                                    html += '</table>';
                                    html += '</div></div>';
                                }

                                $('#consignee_info_div').html(html);

                                $('#ConsigneeInformationModal').modal('show');

                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            }
                            else {

                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        });
                }
            });


        });
    </script>
@endsection