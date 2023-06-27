@extends('admin.layout.master')

@section('title', 'Revenue Report By Invoice')

@section('content')
    <h1 class="mb-1">
        Revenue Report By Invoice
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div id="search_form" class="row mb-2 justify-content-center">

                    <div class="col-4">
                        <fieldset class="form-group">
                            <input type="text" class="form-control" name="search_invoice_number" id="search_invoice_number" placeholder="Search Invoice Number">
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_shipper" id="search_shipper" class="form-control select2">
                                @foreach($shippers as $shipper)
                                    <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_origin" id="search_origin" class="form-control select2">
                                @foreach($cities as $origin)
                                    <option value="{{$origin->id}}">{{$origin->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_segment" id="search_segment" class="form-control select2">
                                @foreach($segments as $segment)
                                    <option value="{{$segment->id}}">{{$segment->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>

                    <div class="col-5">
                        <fieldset class="form-group">
                            <select name="search_sub_segment" id="search_sub_segment" class="form-control select2">
                                @foreach($sub_segments as $sub_segment)
                                    <option value="{{$sub_segment->id}}">{{$sub_segment->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-5">
                        <fieldset class="form-group">
                            <select name="search_account_type" id="search_account_type" class="form-control select2">
                                @foreach($account_types as $at)
                                    <option value="{{$at->id}}">{{$at->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-5">
                        <fieldset class="form-group">
                            <select name="search_business_category" id="search_business_category" class="form-control select2">
                                @foreach($business_categories as $bc)
                                    <option value="{{$bc->id}}">{{$bc->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-5">

                        <div class="form-group input-group ml-1">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Invoicing Date (From)" data-value="{{ \Carbon\Carbon::today()->subDays(30)->toDateString() }}">
                        </div>
                    </div>
                    <div class="col-5 ">
                        <div class="form-group input-group ml-1">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Invoicing Date (To)" data-value="{{ \Carbon\Carbon::today()->toDateString() }}">
                        </div>

                    </div>

                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Account Type</th>
                        <th class="border-primary border-darken-1">Business Category</th>
                        <th class="border-primary border-darken-1">Segment</th>
                        <th class="border-primary border-darken-1">Sub Category Segment</th>
                        <th class="border-primary border-darken-1">Invoice No.</th>
                        <th class="border-primary border-darken-1">Invoicing Date</th>
                        <th class="border-primary border-darken-1">Account ID</th>
                        <th class="border-primary border-darken-1">Shipper Name</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Payment Type</th>
                        <th class="border-primary border-darken-1">Weight Charges</th>
                        <th class="border-primary border-darken-1">Cash Handling Charges</th>
                        <th class="border-primary border-darken-1">Insurance Charges</th>
                        <th class="border-primary border-darken-1">Packaging Charges</th>
                        <th class="border-primary border-darken-1">Fuel Surcharge</th>
                        <th class="border-primary border-darken-1">Return Charges</th>
                        <th class="border-primary border-darken-1">Replacement Charges</th>
                        <th class="border-primary border-darken-1">Try & Buy Charges</th>
                        <th class="border-primary border-darken-1">NSA/OSA Charges</th>
                        <th class="border-primary border-darken-1">Intercept Charges</th>
                        <th class="border-primary border-darken-1">GST</th>
                        <th class="border-primary border-darken-1">Total Charges</th>
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
    <script src="{{asset('app-assets/vendors/js/pagination/moment.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#search_invoice_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#search_segment').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Segment',
                width:'100%',
                allowClear:true
            });
            $('#search_sub_segment').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Sub Segment',
                width:'100%',
                allowClear:true
            });
            $('#search_shipper').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Shipper',
                width:'100%',
                allowClear:true
            });
            $('#search_origin').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Origin City',
                width:'100%',
                allowClear:true
            });
            $('#search_business_category').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Business Category',
                allowClear:true
            });
            $('#search_account_type').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Account Type',
                width:'100%',
                allowClear:true
            });


            var from_date = $('#search_form #search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        var old_date_formatted = $('input[name="search_date_from_formatted"]').val();
                        var currentDate = moment(old_date_formatted);

                        var to_date_formatted = $('input[name="search_date_to_formatted"]').val();
                        var toDate = moment(to_date_formatted);


                        to_date.pickadate('picker').clear();


                        var afterDate = currentDate.add(30, 'days');
                        to_date.pickadate('picker').set({'max': afterDate.toDate()},{muted: true});
                        to_date.pickadate('picker').set('select', afterDate.toDate(),{muted:true});


                    }
                }
            });
            var to_date = $('#search_form #search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        var current_date_formatted = $('input[name="search_date_to_formatted"]').val();
                        var currentDate = moment(current_date_formatted);

                        var from_date_formatted = $('input[name="search_date_from_formatted"]').val();
                        var fromDate = moment(from_date_formatted);

                        if (currentDate.format('x') < fromDate.format('x')) {
                            from_date.pickadate('picker').clear();
                        }

                        var beforeDate = currentDate.subtract(30, 'days');
                        from_date.pickadate('picker').set({'min': beforeDate.toDate()},{muted: true});
                    }
                }
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
                        url: '{{ route('admin.reports.revenue_report_by_invoice.list') }}',
                        method:'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S. No.');
                            head.push('Account Type');
                            head.push('Business Category');
                            head.push('Segment');
                            head.push('Sub Category Segment');
                            head.push('Invoice No.');
                            head.push('Invoicing Date');
                            head.push('Account ID');
                            head.push('Shipper Name');
                            head.push('Origin');
                            head.push('Payment Type');
                            head.push('Weight Charges');
                            head.push('Cash Handling Charges');
                            head.push('Insurance Charges');
                            head.push('Packaging Charges');
                            head.push('Fuel Surcharge');
                            head.push('Return Charges');
                            head.push('Replacement Charges');
                            head.push('Try & Buy Charges');
                            head.push('NSA/OSA Charges');
                            head.push('Intercept Charges');
                            head.push('GST');
                            head.push('Total Charges');
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.account_type);
                                row.push(values.business_category);
                                row.push(values.segment);
                                row.push(values.sub_segment);
                                row.push(values.invoice_number);
                                row.push(values.invoicing_date);
                                row.push(values.account_no);
                                row.push(values.shipper);
                                row.push(values.origin);
                                row.push(values.payment_type);
                                row.push(values.weight_charges);
                                row.push(values.cash_handling_charges);
                                row.push(values.insurance_charges);
                                row.push(values.packaging_material_charges);
                                row.push(values.fuel_surcharge);
                                row.push(values.return_charges);
                                row.push(values.replacement_charges);
                                row.push(values.try_and_buy_charges);
                                row.push(values.nsa_osa_charges);
                                row.push(values.intercept_charges);
                                row.push(values.gst);
                                row.push(values.total_charges);

                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header:head};
                }
            } );
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                deferLoading: [50, 0],
                buttons: [
                    {
                        extend: 'excelHtml5',
                        filename: 'Revenue Report By Invoice',
                        title: '',
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
                ajax:{
                    url: '{{ route('admin.reports.revenue_report_by_invoice.list') }}',
                    method:'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function (d) {
                        d.search_invoice_number = $('#search_invoice_number').val();
                        d.search_shipper = $('#search_shipper').val();
                        d.search_origin = $('#search_origin').val();
                        d.search_segment = $('#search_segment').val();
                        d.search_sub_segment = $('#search_sub_segment').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                        d.search_account_type = $('#search_account_type').val();
                        d.search_business_category = $('#search_business_category').val();
                    }
                },
                order: [[6, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'account_type' ,name: 'at.name', class: 'align-middle text-center account_type'},
                    { data:'business_category' ,name: 'bc.name', class: 'align-middle business_category'},
                    { data:'segment' ,name: 'seg.name', class: 'align-middle segment'},
                    { data:'sub_segment' ,name: 'seg_sub.name', class: 'align-middle sub_segment'},
                    { data:'invoice_number' ,name: 'rbi.invoice_number', class: 'align-middle text-center invoice_number'},
                    { data:'invoicing_date' ,name: 'rbi.invoicing_date', class: 'align-middle text-center invoicing_date'},
                    { data:'account_no' ,name: 'users.id', class: 'align-middle account_no'},
                    { data:'shipper' ,name: 'users.name', class: 'align-middle shipper'},
                    { data:'origin' ,name: 'oc.name', class: 'align-middle origin'},
                    { data:'payment_type' ,name: 'rbi.payment_type', class: 'align-middle payment_type'},
                    { data:'weight_charges' ,name: 'rbi.weight_charges', class: 'align-middle weight_charges'},
                    { data:'cash_handling_charges' ,name: 'rbi.cash_handling_charges', class: 'align-middle cash_handling_charges'},
                    { data:'insurance_charges' ,name: 'rbi.insurance_charges', class: 'align-middle insurance_charges'},
                    { data:'packaging_charges' ,name: 'rbi.packaging_charges', class: 'align-middle packaging_charges'},
                    { data:'fuel_surcharge' ,name: 'rbi.fuel_surcharge', class: 'align-middle fuel_surcharge'},
                    { data:'return_charges' ,name: 'rbi.return_charges', class: 'align-middle return_charges'},
                    { data:'replacement_charges' ,name: 'rbi.replacement_charges', class: 'align-middle replacement_charges'},
                    { data:'try_buy_charges' ,name: 'rbi.try_buy_charges', class: 'align-middle try_buy_charges'},
                    { data:'nsa_osa_charges' ,name: 'rbi.nsa_osa_charges', class: 'align-middle nsa_osa_charges'},
                    { data:'intercept_charges' ,name: 'rbi.intercept_charges', class: 'align-middle intercept_charges'},
                    { data:'gst' ,name: 'rbi.gst', class: 'align-middle gst'},
                    { data:'total_charges' ,name: 'rbi.total_charges', class: 'align-middle total_charges'}
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