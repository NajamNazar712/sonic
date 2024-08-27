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
                                    Date (From)
                                </span>
                            </div>
                            <input type="text" name="search_date_from"
                                class="form-control pickadate bg-primary border-primary white rounded-right"
                                id="search_date_from" placeholder="Date (From)">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                    Date (To)
                                </span>
                            </div>
                            <input type="text" name="search_date_to"
                                class="form-control pickadate bg-primary border-primary white rounded-right"
                                id="search_date_to" placeholder="Date (To)">
                        </div>
                    </div>

                    <div class="col-2">
                        <button type="button" id="search_filter_btn"
                            class="mr-1 mb-1 btn btn-outline-primary btn-min-width">
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
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.min.css') }}">

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
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/legacy.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pagination/moment.min.js') }}" type="text/javascript"></script>

    <script>
        var from_date = $('#search_date_from').pickadate({
            firstDay: 1,
            clear: '',
            selectYears: true,
            selectMonths: true,
            formatSubmit: 'yyyy-mm-dd 00:00:00',
            hiddenSuffix: '_formatted',
            max: new Date(),
            onSet: function(context) {
                if (context.select) {
                    var fromDateFormatted = $('input[name="search_date_from_formatted"]').val();
                    var fromDate = moment(fromDateFormatted);

                    var toDateFormatted = $('input[name="search_date_to_formatted"]').val();
                    var toDate = moment(toDateFormatted);

                    if (fromDate.isAfter(toDate)) {
                        to_date.pickadate('picker').clear();
                    }

                    var maxDate = fromDate.add(30, 'days').toDate();
                    if (maxDate > new Date()) {
                        maxDate = new Date(); // Ensure maxDate does not exceed today
                    }
                    to_date.pickadate('picker').set({
                        'max': maxDate
                    }, {
                        muted: true
                    });
                }
            }
        });

        var to_date = $('#search_date_to').pickadate({
            firstDay: 1,
            clear: '',
            selectYears: true,
            selectMonths: true,
            formatSubmit: 'yyyy-mm-dd 23:59:59',
            hiddenSuffix: '_formatted',
            max: new Date(),
            onSet: function(context) {
                if (context.select) {
                    var toDateFormatted = $('input[name="search_date_to_formatted"]').val();
                    var toDate = moment(toDateFormatted);

                    var fromDateFormatted = $('input[name="search_date_from_formatted"]').val();
                    var fromDate = moment(fromDateFormatted);

                    if (toDate.isBefore(fromDate)) {
                        from_date.pickadate('picker').clear();
                    }

                    var minDate = toDate.subtract(30, 'days').toDate();
                    from_date.pickadate('picker').set({
                        'min': minDate,
                        'max': new Date()
                    }, {
                        muted: true
                    });
                }
            }
        });

        jQuery.fn.DataTable.Api.register('buttons.exportData()', function(options) {
            if (this.context.length) {
                body = [];
                var params = table.ajax.params();

                if (params) {
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.shipment_reversal_report.list') }}',
                        data: params,
                        success: function(result) {
                            head = [];
                            head.push('S.No');
                            head.push('Tracking Number');
                            head.push('Consignee Name');
                            head.push('Consignee Phone Number');
                            head.push('Consignee Address');
                            head.push('Consignee City');
                            head.push('Amount');
                            head.push('DNCC Number');
                            head.push('Status');
                            head.push('Delivered At');
                            head.push('Reverted At');
                            head.push('Reverted By');

                            $.each(result.data, function(index, values) {
                                row = [];
                                let trackingNumber = values.tracking_number.match(/>(\d+)<\/a>/);
                                if (trackingNumber) {
                                    trackingNumber = trackingNumber[1];
                                }
                                row.push(index + 1);
                                row.push(`="${trackingNumber}"`);
                                row.push(values.consignee_name);
                                row.push(values.consignee_phone_number_1);
                                row.push(values.consignee_address);
                                row.push(values.consignee_city_name);
                                row.push(values.amount);
                                row.push(values.dncc);
                                row.push(values.current_status);
                                row.push(values.delivered_at);
                                row.push(values.reverted_at);
                                row.push(values.admin_name);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {
                        body: body,
                        header: head
                    };
                } else {
                    console.error('params is undefined');
                    return {
                        body: [],
                        header: []
                    };
                }
            }
        });

        var table = $('#datatable').DataTable({
            dom: '<"d-inline-block"l><"pull-right"B>tipr',
            scrollX: true,
            scrollY: '500px',
            buttons: [{
                extend: 'csv',
                title: 'Shipment Reversal Report',
                text: '<i class="la la-file-excel-o"></i> Excel',
            }, ],
            lengthMenu: [
                [50, 100, 500, 1000, -1],
                [50, 100, 500, 1000, 'All']
            ],
            pageLength: 50,
            pagingType: 'full_numbers',
            processing: true,
            deferLoading: 0,
            language: {
                processing: data_table_loader
            },
            serverSide: true,
            ajax: {
                url: '{{ route('admin.reports.shipment_reversal_report.list') }}',
                data: function(d) {
                    d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                    d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                }
            },
            columns: [{
                    orderable: false,
                    searchable: false,
                    name: 'serial_number',
                    class: 'align-middle serial_number',
                    targets: 0,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
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
                    data: 'consignee_phone_number_1',
                    name: 'consignee_phone_number_1',
                    class: 'align-middle consignee_phone_number_1',
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
                    data: 'consignee_city_name',
                    name: 'cities.name',
                    class: 'align-middle consignee_city_name',
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
                    data: 'dncc',
                    name: 'reversion_delivered_shipments.dncc',
                    class: 'align-middle dncc',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'current_status',
                    name: 'shipment_status.name',
                    class: 'align-middle current_status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'delivered_at',
                    name: 'dr.created_at',
                    class: 'align-middle delivered_at',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'reverted_at',
                    name: 'reversion_delivered_shipments.created_at',
                    class: 'align-middle reverted_at',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'admin_name',
                    name: 'admins.name',
                    class: 'align-middle admin_name',
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

        $('#search_filter_btn').on('click', function() {
            table.draw();
        });
    </script>
@endsection
