@extends('admin.layout.master')

@section('title', 'Telenor Calls')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Telenor Calls
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
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Call Date</th>
                                    <th class="border-primary border-darken-1">Response</th>
                                    <th class="border-primary border-darken-1">Response Status</th>
                                    <th class="border-primary border-darken-1">Error</th>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
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

        .selectize-control {
            width: 100%;
        }
    </style>
@endsection
@section('js')

    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pagination/moment.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    blockPagePermanently();
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.shipment.poc_kam_tagged_accounts.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Tracking Number');
                            head.push('Status');
                            head.push('Call Date');
                            head.push('Response');
                            head.push('Response Status');
                            head.push('Error');



                            $.each(result.data, function (index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.status);
                                row.push(values.created_at);
                                row.push(values.response);
                                row.push(values.response_status);
                                row.push(values.error);
                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header: head};
                }
            });


            var table = $('#datatable').DataTable({
                scrollX: false, scrollY: '500px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Telenor Calls',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o "></i> Excel',
                    },
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.telenor.call.list') }}',
                },
                rowId: 'id',
                order: [[4, 'desc']],
                columns: [
                    {orderable: false,searchable: false,name: 'serial_number',class: 'align-middle serial_number',targets: 0, render: function (data, type, row) { return '';}},
                    {data: 'tracking_number_link',name: 'telenor_call_responses.tracking_number',class: 'align-middle text_center tracking_number'},
                    {data: 'status',name: 'telenor_call_responses.status',class: 'align-middle text_center status'},
                    {data: 'created_at',name: 'telenor_call_responses.created_at',class: 'align-middle text_center created_at'},
                    {data: 'response',name: 'telenor_call_responses.response',class: 'align-middle text_center response'},
                    {data: 'response_status',name: 'telenor_call_responses.response_status',class: 'align-middle text_center response_status'},
                    {data: 'error',name: 'tae.text',class: 'align-middle text_center error', orderable: false,searchable: false}

                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function () {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());
                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var drop_select = '<select name="status" id="status" class="select2 form-control">' +
                        '<option value="0">Pending for Call</option>' +
                        '<option value="1">Call Sent</option>' +
                        '<option value="2">Response Received</option>' +
                        '<option value="3">Error In API</option>' +
                        '</select>';
                    var reason_drop_select = '<select name="response" id="response" class="select2 form-control">' +
                        '<option value="1">Delivered</option>' +
                        '<option value="2">Not Delivered</option>' +
                        '<option value="3">Not Responded</option>' +
                        '</select>';
                    var status_drop_select = '<select name="response_status" id="response_status" class="select2 form-control">' +
                        '<option value="0">Call Scheduled</option>' +
                        '<option value="1">Call Sent</option>' +
                        '<option value="2">Recipient Busy</option>' +
                        '<option value="3">Not Responding</option>' +
                        '<option value="4">Not Answering</option>' +
                        '</select>';
                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.action') || $(header).is('.serial_number')) {
                            $(td).appendTo($(search));
                        }
                        else if($(header).is('.status')) {
                            $(drop_select).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        }
                        else if($(header).is('.response')) {
                            $(reason_drop_select).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        }
                        else if($(header).is('.response_status')) {
                            $(status_drop_select).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        }else {
                            var current = $(input).appendTo($(search)).on('change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });
                    $("#status").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select a Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $("#response").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select a Response",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $("#response_status").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select a Response Status",
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
