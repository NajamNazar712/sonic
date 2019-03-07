@extends('admin.layout.master')

@section('title', 'Multiple Payments Report')

@section('content')
    <h1 class="mb-1">
        Multiple Payments Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center">
                    <form id="tracking_number_search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                        <div class="form-group ml-1">
                            <input type="text" class="input-group form-control" name="search_tracking_number" id="search_tracking_number" placeholder="Tracking Number">
                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" id="search_filter_btn" class="btn btn-primary"><i class="la la-search"></i> Search</button>
                        </div>
                    </form>

                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking Number</th>
                        <th class="border-primary border-darken-1">Payment ID</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Actual Weight</th>
                        <th class="border-primary border-darken-1">Amount</th>
                        <th class="border-primary border-darken-1">Cash Handling Charges</th>
                        <th class="border-primary border-darken-1">Insurance Charges</th>
                        <th class="border-primary border-darken-1">Return Charges</th>
                        <th class="border-primary border-darken-1">Fuel Surcharge</th>
                        <th class="border-primary border-darken-1">Replacement Charges</th>
                        <th class="border-primary border-darken-1">Packaging Material Charges</th>
                        <th class="border-primary border-darken-1">GST</th>
                        <th class="border-primary border-darken-1">Total Payable</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
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
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.multiple_payment_report.list') }}',
                        data: {
                            'page': 'all',
                            'tracking_number': $('#tracking_number_search_form #search_tracking_number').val()
                        },
                        success: function (result) {
                            head = [];
                            head.push('S. No.');
                            head.push('Tracking Number');
                            head.push('Payment ID');
                            head.push('Status');
                            head.push('Actual Weight');
                            head.push('Amount');
                            head.push('Cash Handling Charges');
                            head.push('Insurance Charges');
                            head.push('Return Charges');
                            head.push('Fuel Surcharge');
                            head.push('Replacement Charges');
                            head.push('Packaging Material Charges');
                            head.push('GST');
                            head.push('Total Payable');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.id_padded);
                                row.push(values.status);
                                row.push(values.actual_weight);
                                row.push(values.amount);
                                row.push(values.cash_handling_charges);
                                row.push(values.insurance_charges);
                                row.push(values.return_charges);
                                row.push(values.fuel_surcharge);
                                row.push(values.replacement_charges);
                                row.push(values.packaging_material_charges);
                                row.push(values.gst);
                                row.push(values.total_payable);

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
                scrollX: true, scrollY: '350px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Multiple Payment Report',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                    },

                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.multiple_payment_report.list') }}',
                    data: function (d) {
                        d.tracking_number = $('#tracking_number_search_form #search_tracking_number').val();
                    }
                },
                order: [[3, 'asc']],
                rowId: 'done_payment_id',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number_link', name: 's.tracking_number', class: 'align-middle tracking_number_link'},
                    {data: 'payment_id_link', name: 'done_payment_shipments.done_payment_id', class: 'align-middle payment_id_link'},
                    {data: 'status', name: 'done_payment_shipments.type', class: 'align-middle status'},
                    {data: 'actual_weight', name: 's.actual_weight', class: 'align-middle actual_weight'},
                    {data: 'amount', name: 'done_payment_shipments.amount', class: 'align-middle amount'},
                    {data: 'cash_handling_charges', name: 's.cash_handling_charges', class: 'align-middle cash_handling_charges'},
                    {data: 'insurance_charges', name: 's.insurance_charges', class: 'align-middle rider_city'},
                    {data: 'return_charges', name: 's.return_charges', class: 'align-middle insurance_charges'},
                    {data: 'fuel_surcharge', name: 's.fuel_surcharge', class: 'align-middle fuel_surcharge'},
                    {data: 'replacement_charges', name: 's.replacement_charges', class: 'align-middle replacement_charges'},
                    {data: 'packaging_material_charges', name: 's.packaging_material_charges', class: 'align-middle packaging_material_charges'},
                    {data: 'gst', name: 'done_payment_shipments.gst', class: 'align-middle gst'},
                    {data: 'total_payable', name: 'done_payment_shipments.payable', class: 'align-middle total_payable'}
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });

            $('#tracking_number_search_form').on('submit',function (e) {
                e.preventDefault();
                table.draw();
            });

            function print(id){
                $.ajax({
                    url: '{!! route('admin.finance.done_payments.details_print') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': id
                    }
                })
                    .done(function(data) {
                        var tab = window.open('', '_blank');

                        if(!tab) {
                            swal({
                                title: 'Popup Blocker Enabled!',
                                text: 'Please add this site to your exception list.',
                                icon: 'error',
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                        }
                        else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                    });
            }
            $('#datatable tbody').on('click', 'tr td.payment_id_link button', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                console.log(id);
                if (id) {
                    print(id);
                } else {
                    var error = "Payment Details not found!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                }
            });

        });



    </script>
@endsection