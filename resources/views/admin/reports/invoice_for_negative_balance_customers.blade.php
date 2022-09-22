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

                <form id="search_form" class=" mb-1 justify-content-center" novalidate="novalidate">
                    <div class="row mb-2 justify-content-center">
                        <div class="col-3">
                            <fieldset class="form-group">
                                <select name="search_admins[]" id="search_admins" class="form-control select2" multiple="multiple" required data-rule-required="true" data-msg-required="This field is required">
                                    @foreach($salesperson as $admin)
                                        <option value="{{$admin->id}}">{{$admin->name}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <select name="status_select" class="select2" id="status_select">
                                    <option value="1">Highlighted</option>
                                    <option value="2">Non Highlighted</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-3">
                            <fieldset class="form-group">
                                <select name="search_shipping_mode" id="search_shipping_mode" class="form-control select2">
                                    @foreach($shipping_modes as $shipping_mode)
                                        <option value="{{$shipping_mode->id}}">{{$shipping_mode->mode}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-3">
                            <fieldset class="form-group">
                                <select name="search_shipper" id="search_shipper" class="form-control select2">
                                    @foreach($shippers as $shipper)
                                        <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-2">
                            <div class="form-group">
                                <button type="submit" name="search" class="btn btn-primary" value="Search">Search</button>
                            </div>
                        </div>
                    </div>
                </form>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Sales Person</th>
                        <th class="border-primary border-darken-1">Account No.</th>
                        <th class="border-primary border-darken-1">Client Name</th>
                        <th class="border-primary border-darken-1">Phone No.</th>
                        <th class="border-primary border-darken-1">COD Amount</th>
                        <th class="border-primary border-darken-1">Charges</th>
                        <th class="border-primary border-darken-1">Net Payable</th>
                        <th class="border-primary border-darken-1">Status</th>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">

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
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            $("#status_select").prepend('<option value="" selected="selected"></option>').select2({
                placeholder: "Select Status",
                width:'100%',
                dropdownCssClass: 'form-control-sm p-0'
            });
            $('#search_shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Shipping Mode',
                width:'100%',
                allowClear:true
            });
            $('#search_shipper').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Shipper',
                width:'100%',
                allowClear:true
            });
            $('#search_admins').select2({
                width:'100%',
                placeholder:"Select Sale Persons",
                allowClear:true,
                dropdownParent:$('#search_form')
            });
            $('#search_form').on('submit',function (e) {
                e.preventDefault();
                table.draw();
            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    blockPagePermanently();
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.negative_balance_customers.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S. No.');
                            head.push('Sales Person');
                            head.push('Account No.');
                            head.push('Client Name');
                            head.push('Phone No.');
                            head.push('COD Amount');
                            head.push('Charges');
                            head.push('Net Payable');
                            head.push('Status');
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.sales_person);
                                row.push(values.account_no);
                                row.push(values.name);
                                row.push(values.phone);
                                row.push(values.amount);
                                row.push(values.charges);
                                row.push(values.overall_payable);
                                row.push(values.status);
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
                ajax: {
                    url: '{{ route('admin.reports.negative_balance_customers.list') }}',
                    data: function (d) {
                        d.search_shipping_mode = $('#search_shipping_mode').val();
                        d.search_shipper = $('#search_shipper').val();
                        d.sale_persons = $('#search_admins').val();
                        d.status_select = $('#status_select').val();
                    }
                },
                columns: [
                    { orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'sales_person' ,name: 'a.name', class: 'align-middle text-center sales_person'},
                    { data:'account_no' ,name: 'u.id', class: 'align-middle text-center account_no'},
                    { data:'name' ,name: 'u.name', class: 'align-middle shipper'},
                    { data:'phone' ,name: 'u.phone', class: 'align-middle name'},
                    { data:'amount' ,name: 'pending_payment_shipments.amount', class: 'align-middle amount'},
                    { data:'charges' ,name: 'pending_payment_shipments.charges', class: 'align-middle charges'},
                    { data:'overall_payable' ,name: 'overall_payable', class: 'align-middle overall_payable'},
                    { orderable: false, data:'status' ,name: 'status', class: 'align-middle status'}

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