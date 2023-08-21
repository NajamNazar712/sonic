@extends('admin.layout.master')

@section('title', 'Fintech Report')

@section('content')
    <h1 class="mb-1">
        Fintech Report
    </h1>



    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                <div class="row mb-2 justify-content-center">
                    <div class="col-4">

                        <input type="text" class="form-control" name="search_tracking_no" id="search_tracking_no"
                            placeholder="Tracking Number">
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_riders" class="select2" id="riders">
                                @foreach ($riders as $rider)
                                    <option value="{{ $rider->id }}">{{ $rider->name }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>

                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_shippers" class="select2" id="shippers">
                                @foreach ($shippers as $shipper)
                                    <option value="{{ $shipper->id }}">{{ $shipper->name }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>

                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_fintech_companies" class="select2" id="fintech_companies">
                                @foreach ($fintech_companies as $fintech_company)
                                    <option value="{{ $fintech_company->id }}">{{ $fintech_company->company_name }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>

                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_hubs" class="select2" id="hubs">
                                @foreach ($hubs as $hubs)
                                    <option value="{{ $hubs->id }}">{{ $hubs->name }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>



                    <div class="col-4">
                        <input type="text" class="form-control" name="search_fintech_transactions"
                            id="search_fintech_transactions" placeholder="Trax Pay ID">
                    </div>


                    <div class="col-4">

                        <input type="text" class="form-control" name="search_delivery_notes" id="search_delivery_notes"
                            placeholder="Delivery Note">
                    </div>

                    <div class="col-4">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                    <span class="la la-calendar-o"></span>
                                </span>
                            </div>
                            <input type="text" name="search_date_from"
                                class="form-control bg-primary border-primary white rounded-right" id="search_date_from"
                                placeholder="Search Date (From)" title="Search Date (From)">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                    <span class="la la-calendar-o"></span>
                                </span>
                            </div>
                            <input type="text" name="search_date_to"
                                class="form-control bg-primary border-primary white rounded-right" id="search_date_to"
                                placeholder="Search Date (To)" title="Search Date (To)">
                        </div>
                    </div>


                    <div class="col-2">
                        <button type="button" id="search_filter_btn"
                            class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i>
                            Search</button>
                    </div>
                </div>
                @include('admin.inc.messages')
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Tracking Number</th>
                            <th class="border-primary border-darken-1">Status</th>
                            <th class="border-primary border-darken-1">Payment ID</th>
                            <th class="border-primary border-darken-1">Transaction ID</th>
                            <th class="border-primary border-darken-1">COD Amount</th>
                            <th class="border-primary border-darken-1">Delivery Note ID</th>
                            <th class="border-primary border-darken-1">Rider Name</th>
                            <th class="border-primary border-darken-1">TRAX ID</th>
                            <th class="border-primary border-darken-1">City</th>
                            <th class="border-primary border-darken-1">Shipper</th>
                            <th class="border-primary border-darken-1">Delivered Date</th>
                            <th class="border-primary border-darken-1">Fintech Charges</th>
                            <th class="border-primary border-darken-1">Applied to (Shipper/Consignee)</th>
                            <th class="border-primary border-darken-1">Transaction Date</th>
                            <th class="border-primary border-darken-1">Fintech Company Name </th>
                            <th class="border-primary border-darken-1">Fintech Company Charges</th>
                            <th class="border-primary border-darken-1">Fintech Company FED Tax</th>
                            <th class="border-primary border-darken-1">Gross Revenue</th>
                            <th class="border-primary border-darken-1">FED TAX</th>
                            <th class="border-primary border-darken-1">Net Revenue</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.min.css') }}">
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
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js') }}"
        type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/legacy.js') }}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function() {

            $('#search_tracking_no').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#search_fintech_transactions').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#search_delivery_notes').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#riders').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Rider',
                allowClear: true
            });

            $('#shippers').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Shipper',
                allowClear: true
            });

            $('#fintech_companies').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Fintech Companies',
                allowClear: true
            });

            $('#hubs').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Hubs',
                allowClear: true
            });


            $('#search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        // $('#search_form #search_date_from').pickadate('picker').set('max', $('#search_form #search_date_to').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        // $('#search_form #search_date_from').pickadate('picker').set('max', $('#search_form #search_date_to').pickadate('picker').get('select'));
                    }
                }
            });


            jQuery.fn.DataTable.Api.register('buttons.exportData()', function(options) {
                if (this.context.length) {
                    blockPagePermanently();
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.fintech_report.list') }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: params,
                        success: function(result) {
                            head = [];

                            head.push('S. No');
                            head.push('Tracking Number');
                            head.push('Status');
                            head.push('Payment ID');
                            head.push('Transaction ID');
                            head.push('COD Amount');
                            head.push('Delivery Note ID');
                            head.push('Rider Name');
                            head.push('TRAX ID');
                            head.push('City');
                            head.push('Shipper');
                            head.push('Delivered Date');
                            head.push('Fintech Charges');
                            head.push('Applied to (Shipper/Consignee)');
                            head.push('Transaction Date');
                            head.push('Fintech Company Name');
                            head.push('Fintech Company Charges');
                            head.push('Fintech Company FED Tax');
                            head.push('Gross Revenue');
                            head.push('FED TAX');
                            head.push('Net Revenue');


                            $.each(result.data, function(index, values) {
                                row = [];

                                console.log(values)
                                row.push(index + 1);
                                row.push(values.tracking_number.tracking_number);
                                row.push(values.shipper_status);
                                row.push(values.trax_pay_id);
                                row.push(values.transaction_id);
                                row.push(values.cod_amount);
                                row.push(values.delivery_note_id);
                                row.push(values.rider_name);
                                row.push(values.rider_trax_id);
                                row.push(values.city);
                                row.push(values.name);
                                row.push(values.delivered_date);
                                row.push(values.fintech_charges);
                                row.push(values.applied_to);
                                row.push(values.transaction_date);
                                row.push(values.fc_name);
                                row.push(values.fintech_company_charges);
                                row.push(values.fintech_company_fed_tax);
                                row.push(values.gross_revenue);
                                row.push(values.fed_tax);
                                row.push(values.net_revenue);


                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {
                        body: body,
                        header: head
                    };
                }
            });
            var index_column = 0;
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true,
                scrollY: '500px',
                buttons: [{
                        extend: 'excelHtml5',
                        title: 'Fintech Report',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },

                ],
                lengthMenu: [
                    [50, 100, 500, 1000, -1],
                    [50, 100, 500, 1000, 'All']
                ],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('admin.reports.fintech_report.list') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(d) {
                        d.search_riders = $('#riders').val();
                        d.search_shippers = $('#shippers').val();
                        d.search_fintech_companies = $('#fintech_companies').val();
                        d.search_hubs = $('#hubs').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                        d.search_tracking_no = $('#search_tracking_no').val();
                        d.search_delivery_notes = $('#search_delivery_notes').val();
                        d.search_fintech_transactions = $('#search_fintech_transactions').val();
                        d.delivery_notes = $('#delivery_notes').val();

                    }
                },
                rowId: 'tracking_number',
                order: [
                    [2, 'desc']
                ],
                columns: [{
                        orderable: false,
                        searchable: false,
                        name: 'serial_number',
                        class: 'align-middle serial_number',
                        targets: 0,
                        render: function(data, type, row) {
                            return '';
                        }
                    },
                    {
                        "data": "tracking_number",
                        "render": function(data, type, row) {
                            console.log(data)
                            return '<u><a href="' + data.tracking_link +
                                '" class="tracking" target="_blank">' + data.tracking_number +
                                '</a></u>';
                        }
                    }, {
                        data: 'shipper_status',
                        name: 'sj.shipper_status_id',
                        class: 'align-middle shipper_status'
                    },
                    {
                        data: 'trax_pay_id',
                        name: 'fpd.trax_pay_id',
                        class: 'align-middle trax_pay_id'
                    },
                    {
                        data: 'transaction_id',
                        name: 'fpd.transaction_id',
                        class: 'align-middle transaction_id'
                    },
                    {
                        data: 'cod_amount',
                        name: 'shipments.cod_amount',
                        class: 'align-middle text-center cod_amount'
                    },
                    {
                        data: 'delivery_note_id',
                        name: 'trax_pay_transactions.delivery_note_id',
                        class: 'align-middle type'
                    },
                    {
                        data: 'rider_name',
                        name: 'riders.rider_name',
                        class: 'align-middle rider_name text-center'
                    },
                    {
                        data: 'rider_trax_id',
                        name: 'riders.rider_trax_id',
                        class: 'align-middle rider_name text-center'
                    },
                    {
                        data: 'city',
                        name: 'shipments.consignee_city_id',
                        class: 'align-middle city'
                    },
                    {
                        data: 'name',
                        name: 'users.name',
                        class: 'align-middle name'
                    },
                    {
                        data: 'delivered_date',
                        name: 'sj.created_at',
                        class: 'align-middle delivered_date'
                    },
                    {
                        data: 'fintech_charges',
                        name: 'shipments.fintech_charges',
                        class: 'align-middle fintech_charges'
                    },
                    {
                        data: 'applied_to',
                        name: 'sfc.applied_to',
                        class: 'align-middle applied_to'
                    },
                    {
                        data: 'transaction_date',
                        name: 'fpd.created_at',
                        class: 'align-middle transaction_date'
                    },
                    {
                        data: 'fc_name',
                        name: 'fc.company_name',
                        class: 'align-middle fc_name'
                    },
                    {
                        data: 'fintech_company_charges',
                        name: 'fcc.fintech_company_charges',
                        class: 'align-middle fintech_company_charges'
                    },
                    {
                        data: 'fintech_company_fed_tax',
                        name: 'fcc.fintech_company_fed_tax',
                        class: 'align-middle fintech_company_fed_tax'
                    },
                    {
                        data: 'gross_revenue',
                        name: 'fpd.revenue',
                        class: 'align-middle gross_revenue'
                    },

                    {
                        data: 'fed_tax',
                        name: 'fed_tax',
                        class: 'align-middle fed_tax'
                    },
                    {
                        data: 'net_revenue',
                        name: 'net_revenue',
                        class: 'align-middle net_revenue'
                    },


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




        });
    </script>
@endsection
