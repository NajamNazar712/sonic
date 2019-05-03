@extends('admin.layout.master')

@section('title', 'Negative Balance Customers Report')

@section('content')
    <h1 class="mb-1">
        Negative Balance Customers Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
    @include('admin.inc.messages')
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Account No.</th>
                        <th class="border-primary border-darken-1">Client Name</th>
                        <th class="border-primary border-darken-1">Phone No.</th>
                        <th class="border-primary border-darken-1">COD Amount</th>
                        <th class="border-primary border-darken-1">Charges</th>
                        <th class="border-primary border-darken-1">Net Payable</th>
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
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    blockPagePermanently();
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.negative_balance_customers.list') }}',
                        data: {
                            'page': 'all'
                        },
                        success: function (result) {
                            head = [];
                            head.push('S. No.');
                            head.push('Account No.');
                            head.push('Client Name');
                            head.push('Phone No.');
                            head.push('COD Amount');
                            head.push('Charges');
                            head.push('Net Payable');
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.account_no);
                                row.push(values.name);
                                row.push(values.phone);
                                row.push(values.amount);
                                row.push(values.charges);
                                row.push(values.payable);
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
                scrollX: true, scrollY: '350px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Negative Balance Customer',
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
                serverSide: true,
                ajax: '{{ route('admin.reports.negative_balance_customers.list') }}',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'account_no' ,name: 'u.id', class: 'align-middle text-center account_no'},
                    { data:'name' ,name: 'u.name', class: 'align-middle shipper'},
                    { data:'phone' ,name: 'u.phone', class: 'align-middle name'},
                    { data:'amount' ,name: 'pending_payment_shipments.amount', class: 'align-middle amount'},
                    { data:'charges' ,name: 'pending_payment_shipments.charges', class: 'align-middle charges'},
                    { data:'payable' ,name: 'pending_payment_shipments.payable', class: 'align-middle payable'}

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