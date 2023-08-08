@extends('admin.layout.master')
@section('title','Month Closing Resolved')


@section('content')
    <h1 class="mb-1">
        Month Closing-Resolved
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center">
                    <div class="col-12 ">
                        <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">

                            <div class="col-4">
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                    </div>
                                    <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From" data-rule-required="true" data-msg-required="Date(From) is required">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                    </div>
                                    <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To" data-rule-required="true" data-msg-required="Date(To) is required">
                                </div>
                            </div>

                            <div class="col-2">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-outline-info btn-min-width"><i class="la la-search"></i> Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1"></th>
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking ID.</th>
                        <th class="border-primary border-darken-1">Current Status</th>
                        <th class="border-primary border-darken-1">COD Amount</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Consignee Name</th>
                        <th class="border-primary border-darken-1">Number</th>
                        <th class="border-primary border-darken-1">Claim ID </th>
                        <th class="border-primary border-darken-1">Claim Type</th>
                        <th class="border-primary border-darken-1">Closing Type</th>
                        <th class="border-primary border-darken-1">Consignee Address</th>
                        <th class="border-primary border-darken-1">Comments</th>
                        <th class="border-primary border-darken-1">Closing Status</th>
                        <th class="border-primary border-darken-1">Arrival Date</th>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">

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
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#from_date_root').css('top','40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #to_date').pickadate('picker').set('min', $('#search_form #from_date').pickadate('picker').get('select'));
                    }
                }
            });
            var to_date = $('#to_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#to_date_root').css('top', '40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #from_date').pickadate('picker').set('max', $('#search_form #to_date').pickadate('picker').get('select'));
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
                        url: '{{ route('admin.month_closing.resolved.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Tracking No.');
                            head.push('Current Status');
                            head.push('COD Amount');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Hub');
                            head.push('Shipper');
                            head.push('Consignee Name');
                            head.push('Number');
                            head.push('Claim ID');
                            head.push('Claim Type');
                            head.push('Closing Type');
                            head.push('Consignee Address');
                            head.push('Comments');
                            head.push('Closing Status');
                            head.push('Arrival Date');


                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.current_status);
                                row.push(values.cod_amount);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.hub);
                                row.push(values.shipper);
                                row.push(values.consignee_name);
                                row.push(values.consignee_phone);
                                row.push(values.claim_id);
                                row.push(values.claim_type);
                                row.push(values.closing_type);
                                row.push(values.consignee_address);
                                row.push(values.remarks);
                                row.push(values.closing_status);
                                row.push(values.arrival_Date);


                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            var selected_rows = [];
            var shipment_remarks = {};
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                @if (session('role_id') == 1 || count(array_intersect([413, 442, 587], session('permissions'))) !== 0)

                buttons: [
                    @if (session('role_id') == 1 || in_array(587, session('permissions')))
                    {
                        text: 'Return Confirm',
                        className: 'btn btn-primary return_confirm',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            if(selected_rows != ''){
                                swal({
                                    title: 'Are You Sure?',
                                    text: 'Select Yes to change shipment status to Return Confirm!',
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
                                        blockPagePermanently();
                                        table.rows().nodes().each(function (index) {
                                            var row = table.row(index);
                                            if ($(row.node()).hasClass('selected')) {
                                                var id = parseInt(row.id());
                                                var remarks = $(row.node()).find('td.remarks input').val();
                                                shipment_remarks[id] = remarks;
                                            }
                                        });
                                        $.ajax({
                                            url:"{{route('admin.month_closing.confirm')}}",
                                            method:'POST',
                                            data:{
                                                'shipment_ids':selected_rows,
                                                '_token':'{{ csrf_token() }}',
                                                'remark': shipment_remarks
                                            }
                                        }).done(function (data) {
                                            UnblockPagePermanently();
                                            selected_rows = [];
                                            shipment_remarks = {};
                                            table.rows().deselect();
                                            table.button('.close_action').disable();
                                            table.button('.return_confirm').disable();
                                            table.button('.re-attempt').disable();

                                            table.draw(true);
                                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                                        });
                                    }
                                });

                            }
                        }
                    },
                    @endif
                    @if (session('role_id') == 1 || in_array(413, session('permissions')))
                    {
                        text: 'Switch To Close',
                        className: 'btn btn-primary close_action',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            if(selected_rows != ''){
                                swal({
                                    title: 'Are You Sure?',
                                    text: 'Select Yes to update!',
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
                                        blockPagePermanently();
                                        table.rows().nodes().each(function (index) {
                                            var row = table.row(index);
                                            if ($(row.node()).hasClass('selected')) {
                                                var id = parseInt(row.id());
                                                var remarks = $(row.node()).find('td.remarks input').val();
                                                shipment_remarks[id] = remarks;
                                            }
                                        });
                                        $.ajax({
                                            url:"{{route('admin.month_closing.resolved.closed')}}",
                                            method:'POST',
                                            data:{
                                                'shipment_ids':selected_rows,
                                                '_token':'{{ csrf_token() }}',
                                                'remark': shipment_remarks
                                            }
                                        }).done(function (data) {
                                            UnblockPagePermanently();
                                            table.rows().deselect();
                                            selected_rows = [];
                                            shipment_remarks = {};
                                            table.button('.close_action').disable();
                                            table.button('.re-attempt').disable();
                                            table.button('.return_confirm').disable();
                                            
                                            table.draw(true);
                                            if(data.status == 1) {

                                                var html = '';

                                                html += 'The following Shipment(s) could not be added:<br/>';

                                                $.each(data.errors, function (index, message) {
                                                    html += index + ', ';
                                                });

                                                html = html.slice(0, -2);

                                                content = document.createElement('div');
                                                content.innerHTML = html;
                                                swal({
                                                    // title: 'Month Closing!',
                                                    content: content,
                                                    icon: 'warning',
                                                    buttons: {
                                                        cancel: {
                                                            text: 'Close',
                                                            value: null,
                                                            visible: true,
                                                            closeModal: true,
                                                        },
                                                    },
                                                    closeOnClickOutside: false,
                                                    closeOnEsc: false,
                                                    dangerMode: true
                                                });
                                                scan_sound(2);

                                            }
                                            else if(data.status == 2){
                                                var success = "Shipment(s) has been successfully added";
                                                toastr.success(success, 'Success!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                                                scan_sound(1);
                                            }
                                            else if(data.status == 3){
                                                var html = '';

                                                html += 'Some Shipment(s) has been successfully added!<br/><br/>';

                                                html += 'The following Shipment(s) could not be added:<br/>';

                                                $.each(data.errors, function (index, message) {
                                                    html += index + ', ';
                                                });

                                                html = html.slice(0, -2);

                                                content = document.createElement('div');
                                                content.innerHTML = html;
                                                swal({
                                                    // title: 'Month Closing!',
                                                    content: content,
                                                    icon: 'warning',
                                                    buttons: {
                                                        cancel: {
                                                            text: 'Close',
                                                            value: null,
                                                            visible: true,
                                                            closeModal: true,
                                                        },
                                                    },
                                                    closeOnClickOutside: false,
                                                    closeOnEsc: false,
                                                    dangerMode: true
                                                });
                                                scan_sound(2);
                                            }

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

                    @if (session('role_id') == 1 || in_array(442, session('permissions')))
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
                                        blockPagePermanently();
                                        table.rows().nodes().each(function (index) {
                                            var row = table.row(index);
                                            if ($(row.node()).hasClass('selected')) {
                                                var id = parseInt(row.id());
                                                var remarks = $(row.node()).find('td.remarks input').val();
                                                shipment_remarks[id] = remarks;
                                            }
                                        });
                                        $.ajax({
                                            url:"{{route('admin.month_closing.reattempt')}}",
                                            method:'POST',
                                            data:{
                                                'shipment_ids':selected_rows,
                                                '_token':'{{ csrf_token() }}',
                                                'remark': shipment_remarks
                                            }
                                        }).done(function (data) {
                                            UnblockPagePermanently();
                                            selected_rows = [];
                                            shipment_remarks = {};
                                            table.rows().deselect();
                                            table.button('.close_action').disable();
                                            table.button('.re-attempt').disable();
                                            table.button('.return_confirm').disable();
                                            
                                            table.draw(true);
                                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

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

                                if ($(row.node().firstChild).hasClass('select-checkbox') && !$(row.node()).hasClass('selected')) {
                                    id = parseInt(row.id());
                                    row.select();

                                    var index = $.inArray(id, selected_rows);

                                    if (index === -1) {
                                        selected_rows.push(id);
                                    }

                                    table.button('.close_action').enable();
                                    table.button('.re-attempt').enable();
                                    table.button('.return_confirm').enable();
                                    

                                }
                            });
                        }
                    },
                    {
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
                                        table.button('.close_action').disable();
                                        table.button('.re-attempt').disable();
                                        table.button('.return_confirm').disable();
                                        
                                    }
                                }
                            });
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Month Closing Resolved',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },'reset'
                ],
                @else
                buttons:[{
                    extend: 'excel',
                    title: 'Month Closing Resolved',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },'reset'],
                @endif
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
                ajax:{
                    url: '{{ route('admin.month_closing.resolved.list') }}',
                    data: function (d) {
                        d.search_date_from = $('input[name="from_date_formatted"]').val();
                        d.search_date_to = $('input[name="to_date_formatted"]').val();
                    }
                },
                rowId: 'shipment_id',
                order: [[1, 'asc']],
                columns: [
                    {data: 'shipment_id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number_link', name: 'shipments.tracking_number', class: 'align-middle tracking_number_link'},
                    {data: 'current_status', name: 'ss.name', class: 'align-middle current_status'},
                    {data: 'cod_amount', name: 'shipments.amount', class: 'align-middle cod_amount'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'hub', name: 'h.name', class: 'align-middle hub'},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'consignee_name', name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                    {data: 'consignee_phone', name: 'consignee_phone', class: 'align-middle consignee_phone'},
                    {data: 'claim_id_link', name: 'cr.id', class: 'align-middle claim_id_link'},
                    {data: 'claim_type', name: 'crn.type', class: 'align-middle claim_type'},
                    {data: 'closing_type', name: 'mct.name', class: 'align-middle closing_type'},
                    {data: 'consignee_address', name: 'shipments.consignee_address', class: 'align-middle consignee_address'},
                    {data: 'shipment_remarks', name: 'month_closings.remarks', class: 'align-middle remarks', orderable: false},
                    {data: 'closing_status', name: 'mcs.name', class: 'align-middle closing_status'},
                    {data: 'arrival_date', name: 'sj.created_at', class: 'align-middle arrival_date'},

                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    $('td:eq(0)', row).addClass('select-checkbox');
                    if ($.inArray(data.shipment_id, selected_rows) !== -1) {
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

                        if ( $(header).is('.select') || $(header).is('.serial_number') ||  $(header).is('.shipment_remarks') ) {
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


                    this.api().table().columns.adjust();
                }
            });

            $('#search_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {

                    table.draw(true);
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
                    table.button('.close_action').enable();
                    table.button('.re-attempt').enable();
                    table.button('.return_confirm').enable();
                    

                }
                else {
                    table.button('.close_action').disable();
                    table.button('.re-attempt').disable();
                    table.button('.return_confirm').disable();
                    
                }

            });

            
            $('#deduct_switch').on('change',function(){
                deduct_amount_switch_change = document.querySelector('#deduct_switch');
                var users_count = $('#responsible_persons').val().length;
                if(users_count > 0){
                    if(deduct_amount_switch_change.checked === true){
                        var html = '';
                        var responsible_ids = $('#responsible_persons').val();
                        $.each(responsible_ids, function (index, value) {
                            var name = $('#responsible_persons').find('option[value="'+value+'"]').attr('rel');
                            html += '<div class="form-group row justify-content-center">\n' +
                                '                                 <div class="col-3">\n' +
                                '                                     <label for="deduct_amount" class="mb-0 align-middle">'+ name +'</label>\n' +
                                '                                 </div>\n' +
                                '                                 <div class="form-group mb-0 col-6">\n' +
                                '                                     <input type="text" id="deduct_amount_'+ value +'" name="deduct_amount_individual['+ value +']" class="form-control deduct_amount validated" placeholder="Deduct Amount" data-rule-required="true" data-msg-required="Deduct Amount is required">\n' +
                                '                                 </div>\n' +
                                '                             </div>';

                        });
                        $('#deduct_individual_div').html(html);
                        $('.deduct_amount').inputmask({
                            'alias': 'integer',
                            'allowMinus': false,
                            'allowPlus': false,
                            'rightAlign': false,
                            'min': 0,
                            'max': 10000000
                        });
                        $(".deduct_amount .validated").each(function(){
                            $( this ).rules( "add", {
                                required: true,
                            });
                        });
                        $('#deduct_individual_div').slideDown();
                        $('#deduct_all_div').slideUp();

                    }
                    else{
                        $('#deduct_all_div').slideDown();
                        $('#deduct_individual_div').slideUp();
                    }
                }
                else{
                    if(deduct_amount_switch_change.checked === true){
                        var error = 'Select atleast one responsible person!';
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        $('#deduct_switch').trigger('click');
                    }

                }

            });
            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                var shipment_id = parseInt($(this).parents('tr').attr('id'));
                if(shipment_id){
                    if ($(this).hasClass('assign_responsible')) {
                        $('#add_responsible_modal').modal('show');
                        $('#responsible_person_shipment_id').val(shipment_id);
                    }
                }
            });



        });
    </script>
@endsection
