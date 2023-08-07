
@extends('admin.layout.master')
@section('title','Track Actual Shipment Position')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <h1 class="mb-1">
                    Track Actual Shipment Position
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <form id="shipment_form" class="form-horizontal" method="POST" action="{{ route('admin.tracking.shipment_position.upload') }}" novalidate="novalidate" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="row align-items-center justify-content-center">
                                    <div class="col">
                                        <div class="form-group">
                                            <input type="file" name="shipments" class="w-100 p-1 border-primary" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="form-group text-left">
                                            <button type="submit" name="upload" class="btn btn-primary">Track</button>
                                        </div>
                                    </div>

                                    <div class="col ml-auto">
                                        <div class="form-group text-right">
                                            <a href="{{ asset('file/Track Actual Shipment Position.xlsx') }}?v=01_12_2022" class="btn btn-primary"><i class="la la-download"></i> Download Template</a>
                                        </div>
                                    </div>
                                </div>
                            </form>


                            <table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1"></th>
                                    <th class="border-primary border-darken-1">Tracking Number</th>
                                    <th class="border-primary border-darken-1">Origin</th>
                                    <th class="border-primary border-darken-1">Destination</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Status Date Time</th>
                                    <th class="border-primary border-darken-1">Status By</th>
                                    <th class="border-primary border-darken-1">Shipper Name</th>
                                    <th class="border-primary border-darken-1">COD Amount</th>
                                    <th class="border-primary border-darken-1">Last Scanned Location</th>
                                    <th class="border-primary border-darken-1">Last Scanned City</th>
                                    <th class="border-primary border-darken-1">Trax Id</th>
                                    <th class="border-primary border-darken-1">Last Scanned By</th>
                                    <th class="border-primary border-darken-1">Last Scanned At</th>
                                    <th class="border-primary border-darken-1">Handover Note</th>
                                    <th class="border-primary border-darken-1">Handover Created By</th>
                                    <th class="border-primary border-darken-1">Handover Created At</th>
                                    <th class="border-primary border-darken-1">Handover From</th>
                                    <th class="border-primary border-darken-1">Handover To</th>
                                    <th class="border-primary border-darken-1">Handover Received By</th>
                                    <th class="border-primary border-darken-1">Handover Received At</th>
                                    <th class="border-primary border-darken-1">Last Action Performed</th>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
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
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {
            $('#shipment_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Your shipment(s) are being tracked!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
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
                        url: '{{ route('admin.tracking.shipment_position.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Tracking Number');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Status');
                            head.push('Status Date Time');
                            head.push('Status By');
                            head.push('Shipper Name');
                            head.push('COD Amount');
                            head.push('Last Scanned Location');
                            head.push('Last Scanned City');
                            head.push('Trax Id');
                            head.push('Last Scanned By');
                            head.push('Last Scanned At');
                            head.push('Handover Note');
                            head.push('Handover Created By');
                            head.push('Handover Created At');
                            head.push('Handover From');
                            head.push('Handover To');
                            head.push('Handover Received By');
                            head.push('Handover Received At');
                            head.push('Last Action Performed');
                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.status);
                                row.push(values.status_at);
                                row.push(values.status_by);
                                row.push(values.shipper_name);
                                row.push(values.cod_value);
                                row.push(values.screen_location);
                                row.push(values.city);
                                row.push(values.trax_id);
                                row.push(values.scanned_by);
                                row.push(values.scanned_at);
                                row.push(values.handover_note);
                                row.push(values.handover_created_by);
                                row.push(values.handover_created_at);
                                row.push(values.handover_from);
                                row.push(values.handover_to);
                                row.push(values.handover_received_by);
                                row.push(values.handover_received_at);
                                row.push(values.last_action);

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
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Track Actual Shipment Position',
                        className:'btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },

                    'reset'
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                rowId: 'id',
                order: [[1, 'desc']],
                ajax: {
                    url: '{{ route('admin.tracking.shipment_position.list') }}',
                },
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number_link', name: 'shipment_positions.tracking_number', class: 'align-middle tracking_number_link'},
                    {data: 'origin', name: 'shipment_positions.origin', class: 'align-middle origin'},
                    {data: 'destination', name: 'shipment_positions.destination', class: 'align-middle destination'},
                    {data: 'status', name: 'shipment_positions.status', class: 'align-middle status'},
                    {data: 'status_at', name: 'shipment_positions.status_at', class: 'align-middle status_at'},
                    {data: 'status_by', name: 'shipment_positions.status_by', class: 'align-middle status_by'},
                    {data: 'shipper_name', name: 'u.name', class: 'align-middle shipper_name'},
                    {data: 'cod_value', name: 's.amount', class: 'align-middle shipper_name'},
                    {data: 'screen_location', name: 'shipment_positions.screen_location', class: 'align-middle screen_location'},
                    {data: 'city', name: 'shipment_positions.city', class: 'align-middle city'},
                    {data: 'trax_id', name: 'trax_id', class: 'align-middle trax_id'},
                    {data: 'scanned_by', name: 'shipment_positions.scanned_by', class: 'align-middle scanned_by'},
                    {data: 'scanned_at', name: 'shipment_positions.scanned_at', class: 'align-middle scanned_at'},
                    {data: 'handover_note', name: 'shipment_positions.handover_note', class: 'align-middle handover_note'},
                    {data: 'handover_created_by', name: 'shipment_positions.handover_created_by', class: 'align-middle handover_created_by'},
                    {data: 'handover_created_at', name: 'shipment_positions.handover_created_at', class: 'align-middle handover_created_at'},
                    {data: 'handover_from', name: 'shipment_positions.handover_from', class: 'align-middle handover_from'},
                    {data: 'handover_to', name: 'shipment_positions.handover_to', class: 'align-middle handover_to'},
                    {data: 'handover_received_by', name: 'shipment_positions.handover_received_by', class: 'align-middle handover_received_by'},
                    {data: 'handover_received_at', name: 'shipment_positions.handover_received_at', class: 'align-middle handover_received_at'},
                    {data: 'last_action', name: 'shipment_positions.last_action', class: 'align-middle action'},
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


                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number')) {
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
            })
        });
    </script>
@endsection