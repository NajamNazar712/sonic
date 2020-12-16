@extends('admin.layout.master')
@section('title','Month Closing Pending')


@section('content')
    <h1 class="mb-1">
        Month Closing-Pending
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
                        <th class="border-primary border-darken-1">Actions</th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>
    <div class="modal fade" id="add_responsible_modal" role="dialog" aria-labelledby="add_responsible_modal_title" aria-hidden="true">
         <div class="modal-dialog modal-md" role="document">
             <div class="modal-content">
                 <div class="modal-header">
                     <h4 class="modal-title" id="add_responsible_modal_title">Assign Responsible(s)</h4>

                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">×</span>
                     </button>
                 </div>
                 <div class="modal-body text-center">
                     <form id="add_responsible_form" class="form-horizontal mb-1 justify-content-center" novalidate="novalidate">

                         <div class="form-group">
                             <select name="responsible_persons[]" id="responsible_persons" class="form-control select2" multiple="multiple" required data-rule-required="true" data-msg-required="This field is required">
                                 @foreach($admins as $admin)
                                     <option value="{{$admin->id}}">{{$admin->name}}</option>
                                 @endforeach
                             </select>
                         </div>
                         <div class="form-group">
                             <input type="text" name="add_remarks" class="form-control add_remarks" placeholder="Remarks">

                         </div>
                         <div class="form-group ml-1">
                             <button type="submit" name="add" class="btn btn-primary add" value="Add">Add Shipment</button>
                             <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Close</button>

                         </div>
                     </form>

                 </div>

             </div>
         </div>
     </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">

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
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#responsible_persons').select2({
                width:'100%',
                placeholder:"Search Name",
                allowClear:true,
            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.month_closing.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Tracking No.');
                            /*  head.push('Current Status');*/
                            head.push('COD Amount');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Hub');
                            head.push('Shipper');
                            head.push('Consignee Name');
                            head.push('Number');
                            head.push('Claim ID');
                            head.push('Claim Type');
                            /*  head.push('Closing Type');*/
                            head.push('Consignee Address');
                            head.push('Comments');


                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.tracking_number);
                                /*   row.push(values.current_status);*/
                                row.push(values.cod_amount);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.hub);
                                row.push(values.shipper);
                                row.push(values.consignee_name);
                                row.push(values.consignee_phone);
                                row.push(values.claim_id);
                                row.push(values.claim_type);
                                /*  row.push(values.closing_type);*/
                                row.push(values.consignee_address);
                                row.push(values.shipment_remarks);


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

                    {
                        text: 'Switch To Resolve',
                        className: 'btn btn-primary resolve',
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
                                        blockPagePermanently();
                                        table.rows().nodes().each(function(index) {
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
                                            table.button('.confirm').disable();
                                            table.button('.re-attempt').disable();
                                            table.draw(true);
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

                                    table.button('.resolve').enable();
                                    table.button('.re-attempt').enable();

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
                                        table.button('.resolve').disable();
                                        table.button('.re-attempt').disable();
                                    }
                                }
                            });
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Month Closing',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }
                ],
                @else
                buttons:[{
                    extend: 'excel',
                    title: 'Month Closing Pending',
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
                ajax: '{{ route('admin.month_closing.pending.list') }}',
                rowId: 'shipment_id',
                order: [[1, 'asc']],
                columns: [
                    {data: 'shipment_id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
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
                    {data: 'remarks', name: 'mc.remarks', class: 'align-middle remarks'},
                    {data: 'closing_status', name: 'mcs.name', class: 'align-middle closing_status'},
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
                    // var drop_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';
                    // var mode_drop_select = '<select name="mode_select" id="mode_select" class="select2 form-control"></select>';
                    // var service_drop_select = '<select name="service_select" id="service_select" class="select2 form-control"></select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ( $(header).is('.select') || $(header).is('.serial_number') ||  $(header).is('.shipment_remarks') ) {
                            $(td).appendTo($(search));
                        }
                        // else if($(header).is('.status')){
                        //     $(drop_select).appendTo($(search))
                        //         .on( 'change', function () {
                        //             column.search($(this).val(), false, false, true).draw();
                        //         } ).wrap(td);
                        // }else if($(header).is('.mode')){
                        //     $(mode_drop_select).appendTo($(search))
                        //         .on( 'change', function () {
                        //             column.search($(this).val(), false, false, true).draw();
                        //         } ).wrap(td);
                        // }else if($(header).is('.service_type')){
                        //     $(service_drop_select).appendTo($(search))
                        //         .on( 'change', function () {
                        //             column.search($(this).val(), false, false, true).draw();
                        //         } ).wrap(td);
                        // }
                        else {
                            var current = $(input).appendTo($(search)).on('change', function() {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });


                    // $("#status_select").prepend('<option value="" selected></option>').select2({
                    //       data:data,
                    //       placeholder: "Select Status",
                    //       width:'100%',
                    //       containerCssClass: 'select-xs',
                    //       dropdownCssClass: 'form-control-sm p-0'
                    //   });
                    //
                    //
                    //   $("#mode_select").prepend('<option value="" selected></option>').select2({
                    //       data:data1,
                    //       placeholder: "Select Mode",
                    //       width:'100%',
                    //       containerCssClass: 'select-xs',
                    //       dropdownCssClass: 'form-control-sm p-0'
                    //   });
                    //
                    //   $("#service_select").prepend('<option value="" selected></option>').select2({
                    //       data:data2,
                    //       placeholder: "Select Service",
                    //       width:'100%',
                    //       containerCssClass: 'select-xs',
                    //       dropdownCssClass: 'form-control-sm p-0'
                    //   });
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
                    table.button('.resolve').enable();

                }
                else {
                    table.button('.resolve').disable();

                }

            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                var shipment_id = parseInt($(this).parents('tr').attr('id'));
                if(shipment_id){
                    if ($(this).hasClass('assign_responsible')) {

                    }
                }
            });
        });
    </script>
@endsection
