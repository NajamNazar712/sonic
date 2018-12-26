@extends('admin.layout.master')
@section('title','Month Closing Shipments')

@section('content')
    <h1 class="mb-1">
        Month Closing Shipments
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
                        <th class="border-primary border-darken-1">Shipper Name</th>
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
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>
    <div class="modal fade" id="add_shipments_modal" role="dialog" aria-labelledby="add_shipments_title" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="add_shipments_title">Shipment(s)</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_shipment_form" class="form-horizontal mb-1 justify-content-center" novalidate="novalidate">

                        <div class="form-group">
                            <input type="text" name="tracking_number" class="form-control tracking_number" placeholder="Tracking Number*" data-rule-required="true" data-msg-required="Tracking Number is required">
                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" name="add" class="btn btn-primary add" value="Add">Add Shipment</button>
                            <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Close</button>

                        </div>
                    </form>

                </div>

                {{--<div class="modal-footer">--}}
                    {{--<button type="submit" class="btn btn-primary" id="add_shipment_submit_btn">Submit</button>--}}
                    {{--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>--}}
                {{--</div>--}}
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
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.month_closing.list') }}',
                        data: {
                            'page': 'all',
                        },
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Tracking No.');
                            head.push('Shipper Name');
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
                            head.push('Arrival Date');
                            head.push('Status Date');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.tracking);
                                row.push(values.shipper);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.hub);
                                row.push(values.consignee_name);
                                row.push(values.consignee_phone);
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
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                @if (session('role_id') == 1 || count(array_intersect([142, 143, 144], session('permissions'))) !== 0)

                buttons: [
                        @if (session('role_id') == 1 || in_array(142, session('permissions')))
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
                                        $.ajax({
                                            url:"{{route('admin.month_closing.confirm')}}",
                                            method:'POST',
                                            data:{
                                                'shipment_ids':selected_rows,
                                                '_token':'{{ csrf_token() }}'
                                            }
                                        }).done(function (data) {
                                            selected_rows = [];
                                            table.button('.confirm').disable();
                                            table.button('.re-attempt').disable();
                                            table.draw(true);
                                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                            // if(data.untouched){
                                            //     var comma = '';
                                            //     var shipments = '';
                                            //     $.each(data.untouched_shipments, function (index, value) {
                                            //         if(data.untouched_shipments != index+1){
                                            //             comma = ", ";
                                            //         }else{
                                            //             comma = '';
                                            //         }
                                            //         shipments += value+comma;
                                            //
                                            //     });
                                            //
                                            //     swal({
                                            //         title: 'Shipments Not updated!',
                                            //         text: shipments,
                                            //         icon: 'error',
                                            //         closeOnClickOutside: false,
                                            //         closeOnEsc: false
                                            //     });
                                            // }

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

                        @if (session('role_id') == 1 || in_array(143, session('permissions')))
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
                                        $.ajax({
                                            url:"{{route('admin.month_closing.reattempt')}}",
                                            method:'POST',
                                            data:{
                                                'shipment_ids':selected_rows,
                                                '_token':'{{ csrf_token() }}'
                                            }
                                        }).done(function (data) {
                                            selected_rows = [];
                                            table.button('.confirm').disable();
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

                        @if( session('role_id') == 1 || in_array(144, session('permissions')))
                        {
                            text: '<i class="la la-plus"></i> Add Shipment',
                            className: 'btn btn-primary add_shipment',
                            enabled: true,
                            action: function (e, dt, node, config) {
                                $('#add_shipments_modal').modal('show');
                            }
                        },
                        @endif
                    {
                        extend: 'excel',
                        title: 'Month Closing',
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
                                        row.select();

                                        var index = $.inArray(id, selected_rows);

                                        if (index === -1) {
                                            selected_rows.push(id);
                                        }

                                        table.button('.confirm').enable();
                                        table.button('.re-attempt').enable();

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
                                    }
                                }
                            });
                        }
                    },
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
                ajax: '{{ route('admin.month_closing.list') }}',
                rowId: 'shId',
                order: [[16, 'asc'], [17, 'asc']],
                columns: [
                    {data: 'shId', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'id',defaultContent:'', orderable: false, searchable: false, class: 'align-middle serial_number'},
                    {data: 'tracking_number', name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
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
                    {data: 'remarks', name: 'shipments_journey.remarks', class: 'align-middle remarks'},
                    {data: 'arrival', name: 'sj.created_at', class: 'align-middle arrival'},
                    {data: 'status_date', name: 'shipments_journey.created_at', class: 'align-middle status_date'}

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

                        if ( $(header).is('.select') || $(header).is('.serial_number') ||  $(header).is('.shipment_remarks') ) {
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
                        table.button('.confirm').enable();
                        table.button('.re-attempt').enable();
                    }
                    else {
                        table.button('.confirm').disable();
                        table.button('.re-attempt').disable();
                    }

            });


            $('#add_shipment_form input.tracking_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            $('#add_shipment_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                normalizer: function(value) {
                    return $.trim(value);
                },
                submitHandler: function(form) {
                    // $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to add as Month Closing Shipment!',
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
                            var tracking_number = $('#add_shipment_form input.tracking_number').val();
                            $('#add_shipment_form button[type="submit"]').attr('disabled', 'disabled');
                            $.ajax({
                                url: '{!! route('admin.month_closing.add') !!}',
                                method: 'POST',
                                data: {
                                    'tracking_number': tracking_number,
                                    '_token': '{{ csrf_token() }}'
                                }
                            }).done(function(data){
                               if(data.status){
                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    table.draw(true);
                               }else{
                                   toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                               }
                                $('#add_shipment_form input.tracking_number').val('');
                               $('#add_shipments_modal').modal('hide');
                                $('#add_shipment_form button[type="submit"]').attr('disabled', false);
                            });

                        }
                    });


                }
            });

        });
    </script>
@endsection