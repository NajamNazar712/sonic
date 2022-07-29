@extends('admin.layout.master')
@section('title','Return Confirmation Pending SMS')

@section('content')
    <h1 class="mb-1">
        Return Confirmation Pending SMS
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking Number</th>
                        <th class="border-primary border-darken-1">SMS Recipient</th>
                        <th class="border-primary border-darken-1">Consignee/Shipper Name</th>
                        <th class="border-primary border-darken-1">Consignee/Shipper Phone</th>
                        <th class="border-primary border-darken-1">Message</th>
                        <th class="border-primary border-darken-1">Date/Time</th>
                        <th class="border-primary border-darken-1">Agent name</th>
                        <th class="border-primary border-darken-1">Status</th>
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
        .selectize-control {
            width: 300px !important;
        }
        .goldClass{
            background-color: gold;
        }

        .show_message
        {
            display: block;
            width: 200px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            cursor: default;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        var selected_rows = [];
        var restricted_rows = [];
        $(document).ready(function () {

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.return.confirmation_pending_manual_sms_list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S. No.');
                            head.push('Tracking Number');
                            head.push('SMS Recipient');
                            head.push('Consignee/Shipper Name');
                            head.push('Consignee/Shipper Phone');
                            head.push('Message');
                            head.push('Date/Time');
                            head.push('Agent name');
                            head.push('Status');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.recepient);
                                row.push(values.recepient_name);
                                row.push(values.phone);
                                row.push(values.message);
                                row.push(values.datetime);
                                row.push(values.agent_name);
                                row.push(values.status);
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
                lengthMenu: [[5,50, 100, 500, 1000, -1], [5,50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax:{
                    url:'{{ route('admin.return.confirmation_pending_manual_sms_list') }}',
                    data: function (d) {
                    }
                },
                rowId: 'id',
                order: [[6, 'desc']],
                scrollX: false, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        className: 'btn btn-primary',
                        title: 'RCP SMS Response List',
                        text: '<i class="la la-file-excel-o"></i> Excel',

                    },
                ],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, class: 'align-middle text-center serial_number',targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number', name: 'rcp_manual_sms.tracking_number', class: 'align-middle text-center tracking_number'},
                    {data: 'recepient', name: 'rcp_manual_sms.recepient', class: 'align-middle text-center recepient',orderable: false},
                    {data: 'recepient_name', name: 'rcp_manual_sms.recepient_name', class: 'align-middle text-center recepient_name'},
                    {data: 'phone', name: 'rcp_manual_sms.phone', class: 'align-middle text-center phone'},
                    {data: 'message', name: 'rcp_manual_sms.message', class: 'align-middle message',orderable: false},
                    {data: 'datetime', name: 'rcp_manual_sms.created_at', class: 'align-middle text-center datetime'},
                    {data: 'agent_name', name: 'agent.name', class: 'align-middle text-center agent_name'},
                    {data: 'status', name: 'sms.status', class: 'align-middle text-center status',orderable: false},
                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var recepient_drop = '<select name="status_select" id="status_select" class="select2 form-control">'+
                    '<option value="shipper">Shipper</option>' +
                    '<option value="consignee">Consignee</option>' +
                    '</select>';
                    var sms_status = '<select name="sms_status" id="sms_status" class="select2 form-control">'+
                    '<option value="delivered">Delivered</option>' +
                    '<option value="not_delivered">Not Delivered</option>' +
                    '</select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number')|| $(header).is('.message')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.recepient')){
                            $(recepient_drop).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        else if($(header).is('.status')){
                            $(sms_status).appendTo($(search))
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
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Shipper",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    $("#sms_status").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    this.api().table().columns.adjust();
                }
            });
        });
    </script>
@endsection