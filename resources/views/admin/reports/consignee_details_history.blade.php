@extends('admin.layout.master')

@section('title', 'Consignee Details History Report')

@section('content')
    <h1 class="mb-1">
        Consignee Details History Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center">
                    <form id="search_form" class="col mb-1 form-inline justify-content-center" novalidate="novalidate">
                        <div class="form-group">
                            <input type="text" class="form-control" name="search_tracking_no" id="search_tracking_no" placeholder="Tracking Number">
                        </div>
                        <div class="col-4">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                </div>
                                <input type="text" name="search_date_from" class="form-control bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Search Date (From)" data-value="">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                </div>
                                <input type="text" name="search_date_to" class="form-control bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Search Date (To)" data-value="">
                            </div>
                        </div>

                        <div class="col-2">
                            <button type="button" id="search_filter_btn" class="btn btn-primary"><i class="la la-search"></i> Search</button>
                        </div>
                    </form>

                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Old Consignee Name</th>
                        <th class="border-primary border-darken-1">Old Consignee Phone No.</th>
                        <th class="border-primary border-darken-1">Old Consignee Address</th>
                        <th class="border-primary border-darken-1">Old Special Instruction</th>
                        <th class="border-primary border-darken-1">New Consignee Name</th>
                        <th class="border-primary border-darken-1">New Consignee Phone No.</th>
                        <th class="border-primary border-darken-1">New Consignee Address</th>
                        <th class="border-primary border-darken-1">New Special Instruction</th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>


@endsection

@section('css')
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
        a.btn.btn-secondary {
            border-radius: 20px;
            background: #64a0d2;
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
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    {{--<script src="{{asset('js/main-1.0.js')}}" type="text/javascript"></script>--}}

    <script type="text/javascript">
        $(document).ready(function () {
            $('#search_form #search_tracking_no').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#search_form #search_date_from').pickadate({
                firstDay: 1,
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function (context) {
                    if (context.select) {
                        $('#search_form #search_date_to').pickadate('picker').set('min', $('#search_form #search_date_from').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_form #search_date_to').pickadate({
                firstDay: 1,
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function (context) {
                    if (context.select) {
                        $('#search_form #search_date_from').pickadate('picker').set('max', $('#search_form #search_date_to').pickadate('picker').get('select'));
                    }
                }
            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    blockPagePermanently();
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.consignee_details.list') }}',
                        data: {
                            'page': 'all',
                            'search_tracking_no': $('#search_tracking_no').val(),
                            'search_date_from': $('input[name="search_date_from_formatted"]').val(),
                            'search_date_to': $('input[name="search_date_to_formatted"]').val(),
                        },
                        success: function (result) {
                            head = [];
                            head.push('S. No.');
                            head.push('Origin');
                            head.push('Old Consignee City');
                            head.push('Old Consignee Name');
                            head.push('Old Consignee Phone No.');
                            head.push('Old Consignee Address');
                            head.push('New Consignee City');
                            head.push('New Consignee Name');
                            head.push('New Consignee Phone No.');
                            head.push('New Consignee Address');
                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.origin);
                                row.push(values.o_name);
                                row.push(values.o_phone_no);
                                row.push(values.o_address);
                                row.push(values.o_s_instruction);
                                row.push(values.n_name);
                                row.push(values.n_phone_no);
                                row.push(values.n_address);
                                row.push(values.n_s_instruction);
                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header:head};
                }
            });
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Consignee Details History Report',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                "autoWidth": false,
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,ajax: {
                    url: '{{ route('admin.reports.consignee_details.list') }}',
                    data: function (d) {
                        d.search_tracking_no = $('#search_tracking_no').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                order: [[1, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'tracking_number_link' ,name: 's.tracking_number', class: 'align-middle text-center tracking_number_link'},
                    { data:'origin' ,name: 'oc.id', class: 'align-middle origin'},
                    { data:'o_name' ,name: 'shipment_information_logs.old_consignee_name', class: 'align-middle o_name'},
                    { data:'o_phone_no' ,name: 'shipment_information_logs.old_consignee_phone', class: 'align-middle o_phone_no'},
                    { data:'o_address' ,name: 'shipment_information_logs.old_consignee_address', class: 'align-middle o_address'},
                    { data:'o_s_instruction' ,name: 'shipment_information_logs.old_special_instruction', class: 'align-middle o_s_instruction'},
                    { data:'n_name' ,name: 'shipment_information_logs.new_consignee_name', class: 'align-middle n_name'},
                    { data:'n_phone_no' ,name: 'shipment_information_logs.new_consignee_phone', class: 'align-middle n_phone_no'},
                    { data:'n_address' ,name: 'shipment_information_logs.old_consignee_address', class: 'align-middle n_address'},
                    { data:'n_s_instruction' ,name: 'shipment_information_logs.new_special_instruction', class: 'align-middle n_s_instruction'},

                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });
            $('#search_filter_btn').on('click',function () {
                table.draw();
            });

        });
    </script>
@endsection