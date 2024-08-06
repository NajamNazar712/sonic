@extends('admin.layout.master')

@section('title', 'Shipment Reversal Report')

@section('content')
    <h1 class="mb-1">
        Shipment Reversal Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div id="reversal_search_form" class="row mb-2 justify-content-center">
                    <div class="col-4">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                    <span class="">Date (From)</span>
                                </span>
                            </div>
                            <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Date (From)" title="Date (From)" />
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                    <span class="">Date (To)</span>
                                </span>
                            </div>
                            <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Date (To)" title="Date (To)" />
                        </div>
                    </div>
                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width">
                            <i class="la la-search"></i>
                            Search
                        </button>
                    </div>
                </div>


                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Tracking Number</th>
                            <th class="border-primary border-darken-1">Consignee Name</th>
                            <th class="border-primary border-darken-1">Consignee Phone Number</th>
                            <th class="border-primary border-darken-1">Consignee Address</th>
                            <th class="border-primary border-darken-1">Consignee City</th>
                            <th class="border-primary border-darken-1">Amount</th>
                            <th class="border-primary border-darken-1">DNCC Number</th>
                            <th class="border-primary border-darken-1">Status</th>
                            <th class="border-primary border-darken-1">Delivered At</th>
                            <th class="border-primary border-darken-1">Reverted At</th>
                            <th class="border-primary border-darken-1">Reverted By</th>
                        </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
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
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>

    <script>
        function getFirstAndLastDatesOfMonth(date) {
            var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
            var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
            return { firstDay: firstDay, lastDay: lastDay };
        }
    
        $('#reversal_search_form #search_date_from').pickadate({
            firstDay: 1,
            clear: '',
            selectYears: true,
            selectMonths: true,
            formatSubmit: 'yyyy-mm-dd 00:00:00',
            hiddenSuffix: '_formatted',
            onSet: function(context) {
                if (context.select) {
                    var selectedDate = new Date(context.select);
                    var dates = getFirstAndLastDatesOfMonth(selectedDate);
                    $('#reversal_search_form #search_date_from').pickadate('picker').set('min', dates.firstDay);
                    $('#reversal_search_form #search_date_from').pickadate('picker').set('max', dates.lastDay);
                    $('#reversal_search_form #search_date_to').pickadate('picker').set('min', dates.firstDay);
                    $('#reversal_search_form #search_date_to').pickadate('picker').set('max', dates.lastDay);
                }
            }
        });
    
        $('#reversal_search_form #search_date_to').pickadate({
            firstDay: 1,
            clear: '',
            selectYears: true,
            selectMonths: true,
            formatSubmit: 'yyyy-mm-dd 23:59:59',
            hiddenSuffix: '_formatted',
            onSet: function(context) {
                if (context.select) {
                    var selectedDate = new Date(context.select);
                    var dates = getFirstAndLastDatesOfMonth(selectedDate);
                    $('#reversal_search_form #search_date_from').pickadate('picker').set('min', dates.firstDay);
                    $('#reversal_search_form #search_date_from').pickadate('picker').set('max', dates.lastDay);
                    $('#reversal_search_form #search_date_to').pickadate('picker').set('min', dates.firstDay);
                    $('#reversal_search_form #search_date_to').pickadate('picker').set('max', dates.lastDay);
                }
            }
        });

        var table = $('#datatable').DataTable({
            dom: '<"d-inline-block"l><"pull-right"B>tipr',
            scrollX: true, scrollY: '500px',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Shipment Reversal Report',
                    text:'<i class="la la-file-excel-o"></i> Excel',
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
            deferLoading: 0,
            autoWidth: false,
            ajax: {
                url: '{{ route('admin.reports.shipment_reversal_report.list') }}',
                data: function (d) {
                    d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                    d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                }
            },
            columns: [
                { 
                    orderable: false, 
                    searchable: false, 
                    name: 'serial_number', 
                    class: 'align-middle serial_number', 
                    targets: 0, 
                    render: function (data, type, row) {
                        return '1'; 
                    }
                },
                { 
                    data: 'tracking_number', 
                    name: 'tracking_number', 
                    class: 'align-middle tracking_number', 
                    orderable: false, 
                    searchable: false 
                },
                { 
                    data: 'consignee_name', 
                    name: 'consignee_name', 
                    class: 'align-middle consignee_name', 
                    orderable: false, 
                    searchable: false 
                },
                { 
                    data: 'consignee_address', 
                    name: 'consignee_address', 
                    class: 'align-middle consignee_address', 
                    orderable: false, 
                    searchable: false 
                },
                { 
                    data: 'consignee_phone_number_1', 
                    name: 'consignee_phone_number_1', 
                    class: 'align-middle consignee_phone_number_1', 
                    orderable: false, 
                    searchable: false 
                },
                { 
                    data: 'consignee_city_id', 
                    name: 'consignee_city_id', 
                    class: 'align-middle consignee_city_id', 
                    orderable: false, 
                    searchable: false 
                },
                { 
                    data: 'amount', 
                    name: 'amount', 
                    class: 'align-middle amount', 
                    orderable: false, 
                    searchable: false 
                },
                { 
                    data: 'delivery_note_id', 
                    name: 'delivery_note_id', 
                    class: 'align-middle delivery_note_id', 
                    orderable: false, 
                    searchable: false 
                },
                { 
                    data: 'delivery_created_at', 
                    name: 'delivery_created_at', 
                    class: 'align-middle delivery_created_at', 
                    orderable: false, 
                    searchable: false 
                },
                { 
                    data: 'reversal_created_at', 
                    name: 'reversal_created_at', 
                    class: 'align-middle reversal_created_at', 
                    orderable: false, 
                    searchable: false 
                },
                { 
                    data: 'admin_id', 
                    name: 'admin_id', 
                    class: 'align-middle admin_id', 
                    orderable: false, 
                    searchable: false 
                }
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

    </script>
@endsection

