@extends('admin.layout.master')

@section('title', 'In Transit Report Bag Wise')

@section('content')
    <h1 class="mb-1">
        In Transit Report Bag Wise
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

    <script type="text/javascript">
        $(document).ready(function() {
            function print(id) {
                $.ajax({
                        url: '{!! route('admin.master_cargo.in_transit.print') !!}',
                        method: 'POST',
                        data: {
                            'id': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                    .done(function(data) {
                        var tab = window.open('', '_blank');

                        if (!tab) {
                            swal({
                                title: 'Popup Blocker Enabled!',
                                text: 'Please add this site to your exception list.',
                                icon: 'error',
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                        } else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                    });
            }

            jQuery.fn.DataTable.Api.register('buttons.exportData()', function(options) {
                if (this.context.length) {
                    blockPagePermanently();
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.master_cargo.bag.in_transit.list') }}',
                        data: params,
                        success: function(result) {
                            head = [];

                            head.push('S. No');
                            head.push('Tracking Number');
                            head.push('Master Cargo.');
                            head.push('Bag Type');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('No. of Shipments');
                            head.push('Short Received Shipments');
                            head.push('Shipping Mode');
                            head.push('Shipments Weight');
                            head.push('Transit Datetime');
                            head.push('Transitted By');
                            head.push('Master Cargo Received Datetime');
                            head.push('Master Cargo Received By');
                            head.push('Status');
                            head.push('Aging');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.bag_no);
                                row.push(values.master_cargo_id);
                                row.push(values.type);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.total_shipments);
                                row.push(values.short_received_shipments);
                                row.push(values.shipping_mode);
                                row.push(values.shipments_weight);
                                row.push(values.transitted_date);
                                row.push(values.transitted_by);
                                row.push(values.received_at);
                                row.push(values.received_by);
                                row.push(values.status);
                                row.push(values.aging);


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
                        title: 'In-Transit Report Bag Wise',
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
                    url: '{{ route('admin.reports.master_cargo.bag.in_transit.list') }}',
                },
                rowId: 'bag_no',
                order: [
                    [10, 'desc']
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
                        data: 'bag_no',
                        name: 'bags.seal_number',
                        class: 'align-middle bag_no'
                    },
                    {
                        data: 'id_padded_link',
                        name: 'mc.id',
                        class: 'align-middle master_cargo_number'
                    },
                    {
                        data: 'type',
                        name: 'type',
                        class: 'align-middle type'
                    },
                    {
                        data: 'origin',
                        name: 'oc.name',
                        class: 'align-middle origin'
                    },
                    {
                        data: 'destination',
                        name: 'dc.name',
                        class: 'align-middle destination'
                    },
                    {
                        data: 'shipments',
                        name: 'shipments',
                        class: 'align-middle text-center shipments'
                    },
                    {
                        data: 'short_received',
                        name: 'short_received',
                        class: 'align-middle short_received text-center'
                    },
                    {
                        data: 'shipping_mode',
                        name: 'sm.mode',
                        class: 'align-middle shipping_mode'
                    },
                    {
                        data: 'shipments_weight',
                        name: 'shipments_weight',
                        class: 'align-middle shipments_weight'
                    },
                    {
                        data: 'transitted_date',
                        name: 'mc.created_at',
                        class: 'align-middle transitted_date'
                    },
                    {
                        data: 'transitted_by',
                        name: 'ad.name',
                        class: 'align-middle transitted_date'
                    },
                    {
                        data: 'received_at',
                        name: 'received_at',
                        class: 'align-middle received_at'
                    },
                    {
                        data: 'received_by',
                        name: 'a.name',
                        class: 'align-middle received_by'
                    },
                    {
                        data: 'status_id',
                        name: 'bs.id',
                        class: 'align-middle status_id'
                    },
                    {
                        orderable: false,
                        searchable: false,
                        data: 'aging',
                        name: 'aging',
                        class: 'align-middle aging'
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




        });
    </script>
@endsection
