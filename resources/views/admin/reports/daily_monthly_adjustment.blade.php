@extends('admin.layout.master')

@section('title', 'Daily and Monthly Adjustments Report')

@section('content')
    <h1 class="mb-1">
        Daily and Monthly Adjustments Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div id="search_form" class="row mb-2 justify-content-center">
                    <div class="col-3">
                        <div class="form-group input-group ml-1">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Date (From)" title="Date (From)" data-value="{{ Carbon\Carbon::today() }}">
                        </div>
                    </div>
                    <div class="col-3 ">
                        <div class="form-group input-group ml-1">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Date (To)" title="Date (To)" data-value="{{ Carbon\Carbon::today() }}">
                        </div>

                    </div>
                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col mt-3">
                        <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                            <thead>
                            <tr role="row" class="bg-primary white">
                                <th class="border-primary border-darken-1">S. No.</th>
                                <th class="border-primary border-darken-1">Tracking No.</th>
                                <th class="border-primary border-darken-1">Hub</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="col">
                        <table class="table table-bordered datatable" id="sum_datatable" style="z-index: 3;">
                            <thead>
                            <tr role="row" class="bg-primary white">
                                <th class="border-primary border-darken-1">S. No.</th>
                                <th class="border-primary border-darken-1">Hub</th>
                                <th class="border-primary border-darken-1">Adjustment No.</th>
                                <th class="border-primary border-darken-1">Ratio %</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>


@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
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
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    {{--<script src="{{asset('js/main-1.0.js')}}" type="text/javascript"></script>--}}

    <script type="text/javascript">
        $(document).ready(function () {
            $('#search_form #search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_to').pickadate('picker').set('min', $('#search_form #search_date_from').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_form #search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_from').pickadate('picker').set('max', $('#search_form #search_date_to').pickadate('picker').get('select'));
                    }
                }
            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    blockPagePermanently();
                    body_sum = [];
                    body = [];
                    var params_sum = sum_table.ajax.params();
                    var params = table.ajax.params();
                    params_sum.start = 0;
                    params_sum.length = -1;
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult_sum = $.ajax({
                        url: '{{ route('admin.reports.daily_monthly_adjustment.summary_list') }}',
                        data: params_sum,
                        success: function (result) {
                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.hub);
                                row.push(values.shipment_count);
                                row.push(values.ratio);
                                body_sum.push(row);
                            });
                        },
                        async: false
                    });
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.daily_monthly_adjustment.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            footer = [];
                            head.push('S. No.');
                            head.push('Tracking No.');
                            head.push('Hub');
                            head.push('');
                            head.push('');
                            head.push('');
                            head.push('S. No.');
                            head.push('Hub');
                            head.push('Adjustment No.');
                            head.push('Ratio %');
                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.hub);
                                row.push('');
                                row.push('');
                                row.push('');
                                if(body_sum[index]){
                                    row.push(body_sum[index][0]);
                                    row.push(body_sum[index][1]);
                                    row.push(body_sum[index][2]);
                                    row.push(body_sum[index][3]);
                                }
                                else{
                                    row.push('');
                                    row.push('');
                                    row.push('');
                                    row.push('');
                                }
                                body.push(row);
                            });

                            footer.push('-');
                            footer.push('Total Adjustments Made');
                            footer.push(shipments_count);
                            footer.push('');
                            footer.push('');
                            footer.push('');
                            footer.push('');
                            footer.push('');
                            footer.push('');
                            footer.push('');
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header:head, footer:footer};
                }
            });
            $('#datatable').append("<tfoot><tr><td></td><td></td><td></td></tr></tfoot>");
            var table = $('#datatable').DataTable({
                dom: 'lrtip',
                scrollX: true, scrollY: '500px',
                paging: false,

                autoWidth: false,
                processing: true,
                deferLoading: 0,
                language: {
                    processing: data_table_loader
                },
                rowId:'shipment_id',
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.daily_monthly_adjustment.list') }}',
                    data: function (d) {
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                order: [[1, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'tracking_number_link' ,name: 's.tracking_number', class: 'align-middle text-center tracking_number'},
                    { data:'hub' ,name: 'h.name', class: 'align-middle hub'}
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                },
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    api.columns('.tracking_number', {
                        page: 'current'
                    }).every(function() {
                        $(this.footer()).html('Total Adjustments Made');
                    });
                    api.columns('.hub', {
                        page: 'current'
                    }).every(function() {
                        shipments_count = this
                            .data()
                            .reduce(function(a, b) {
                                var x = parseFloat(a) || 0;
                                return x + 1;
                            }, 0);
                        $(this.footer()).html(shipments_count);
                    });
                }
            });

            var sum_table = $('#sum_datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Daily and Monthly Adjustments and Summary Report',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                        footer: true
                    },
                ],
                paging: false,
                autoWidth: false,
                processing: true,
                deferLoading: 0,
                language: {
                    processing: data_table_loader
                },
                rowId:'shipment_id',
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.daily_monthly_adjustment.summary_list') }}',
                    data: function (d) {
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                order: [[1, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'hub' ,name: 'h.name', class: 'align-middle hub'},
                    { data:'shipment_count' ,name: 'shipment_count', class: 'align-middle text-center shipment_count', orderable: false, searchable: false},
                    { data:'ratio' ,name: 'ratio', class: 'align-middle text-center ratio', orderable: false, searchable: false},
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
                sum_table.draw();
            });

        });
    </script>
@endsection