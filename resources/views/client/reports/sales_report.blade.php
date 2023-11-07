@extends('client.layout.master')

@section('title', 'Report - Overall Sales')

@section('content')
    <h1 class="mb-1">
        Report - Overall Sales
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('client.inc.messages')

                <form id="search_form" class="row mb-2 justify-content-center" novalidate="novalidate">
                    <div class="col-3">
                        <fieldset class="form-group">
                            <input type="text" class="form-control require_one" name="search_tracking_no" id="search_tracking_no" placeholder="Search Tracking Number">
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class="form-group">
                            <select name="search_origin" id="search_origin" class="form-control select2">
                                @foreach($cities as $origin)
                                    <option value="{{$origin->id}}">{{$origin->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class="form-group">
                            <select name="search_destination" id="search_destination" class="form-control select2">
                                @foreach($cities as $destination)
                                    <option value="{{$destination->id}}">{{$destination->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class="form-group">
                            <select name="search_status" id="search_status" class="form-control select2">
                                @foreach($statuses as $status)
                                    <option value="{{$status->id}}">{{$status->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
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
                            <select name="search_service_type" id="search_service_type" class="form-control select2">
                                @foreach($service_types as $service_type)
                                    <option value="{{$service_type->id}}">{{$service_type->booking_type}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class="form-group">
                            <select name="search_account_type[]" id="search_account_type" class="form-control select2" multiple>
                                <option value="" disabled ></option> <!-- added this line -->
                                @foreach($merged_accounts as $merged_account)
                                    <option value="{{$merged_account->id}}">{{$merged_account->name}} </option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>


                    <div class="col-3">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                    <span class="la la-calendar-o"></span>
                                </span>
                            </div>

                            <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right require_one search_date_from" id="search_date_from" placeholder="Arrival Date (From)">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                    <span class="la la-calendar-o"></span>
                                </span>
                            </div>

                            <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right search_date_to" id="search_date_to" placeholder="Arrival Date (To)">
                        </div>
                    </div>

                    <div class="w-100"></div>

                    <div class="col-4">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                    <span class="la la-calendar-o"></span>
                                </span>
                            </div>

                            <input type="text" name="dr_search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right require_one dr_search_date_from" id="dr_search_date_from" placeholder="Delivered/Returned Date (From)">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                    <span class="la la-calendar-o"></span>
                                </span>
                            </div>

                            <input type="text" name="dr_search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right dr_search_date_to" id="dr_search_date_to" placeholder="Delivered/Returned Date (To)">
                        </div>
                    </div>
                    <div class="col-2">
                        <button type="submit" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </form>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Account No.</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Pickup Address</th>
                        <th class="border-primary border-darken-1">Vendor</th>
                        <th class="border-primary border-darken-1">Order ID</th>
                        <th class="border-primary border-darken-1">Order Date</th>
                        <th class="border-primary border-darken-1">Consignee Name</th>
                        <th class="border-primary border-darken-1">Consignee Phone No. 1</th>
                        <th class="border-primary border-darken-1">Consignee Phone No. 2</th>
{{--                        <th class="border-primary border-darken-1">Consignee Address</th>--}}
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Reason</th>
                        <th class="border-primary border-darken-1">Payment Status</th>
                        <th class="border-primary border-darken-1">Payment ID</th>
                        <th class="border-primary border-darken-1">Processed Date</th>
                        <th class="border-primary border-darken-1">Paid Date</th>
                        <th class="border-primary border-darken-1">Service Type</th>
                        <th class="border-primary border-darken-1">Product Category</th>
                        <th class="border-primary border-darken-1">Description</th>
                        <th class="border-primary border-darken-1">Arrival Date</th>
                        <th class="border-primary border-darken-1">Shipping Mode</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Collection Amount</th>
                        <th class="border-primary border-darken-1">Estimated Weight</th>
                        <th class="border-primary border-darken-1">Actual Weight</th>
                        <th class="border-primary border-darken-1">Chargeable Weight</th>
                        <th class="border-primary border-darken-1">Weight Charges</th>
                        <th class="border-primary border-darken-1">Cash Handling Charges</th>
                        <th class="border-primary border-darken-1">Insurance Charges</th>
                        <th class="border-primary border-darken-1">Packaging Charges</th>
                        <th class="border-primary border-darken-1">Fuel Surcharge</th>
                        <th class="border-primary border-darken-1">Return Charges</th>
                        <th class="border-primary border-darken-1">Replacement Charges</th>
                        <th class="border-primary border-darken-1">Try & Buy Charges</th>
                        <th class="border-primary border-darken-1">NSA/OSA Charges</th>
                        <th class="border-primary border-darken-1">GST</th>
                        <th class="border-primary border-darken-1">Intercept Charges</th>
                        <th class="border-primary border-darken-1">Total Charges</th>
                        <th class="border-primary border-darken-1">Estimated Charges</th>
                        <th class="border-primary border-darken-1">Packing Charges</th>
                        <th class="border-primary border-darken-1">Net Payable</th>
                        <th class="border-primary border-darken-1">Delivered/Returned Date</th>
                        <th class="border-primary border-darken-1">Received/Refused By</th>
                        <th class="border-primary border-darken-1">Shipper Reference 1</th>
                        <th class="border-primary border-darken-1">Shipper Reference 2</th>
                        <th class="border-primary border-darken-1">Shipper Reference 3</th>
                        <th class="border-primary border-darken-1">Shipper Reference 4</th>
                        <th class="border-primary border-darken-1">Shipper Reference 5</th>
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

    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
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
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pagination/moment.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#search_tracking_no').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#search_origin').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Origin City',
                width:'100%',
                allowClear:true
            });
            $('#search_destination').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Destination City',
                width:'100%',
                allowClear:true
            });
            $('#search_status').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Status',
                width:'100%',
                allowClear:true
            });
            $('#search_shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Shipping Mode',
                width:'100%',
                allowClear:true
            });
            $('#search_service_type').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Service Type',
                width:'100%',
                allowClear:true
            });
            $('#search_account_type').select2({
                placeholder:'Select Merged Account ',
                width:'100%',
                // allowClear:true
            });


            var from_date = $('#search_date_from').pickadate({
                firstDay: 1,
                // clear: '',
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

                        if (currentDate.format('x') > toDate.format('x')) {
                            to_date.pickadate('picker').clear();
                        }

                        var afterDate = currentDate.add(31, 'days');
                        to_date.pickadate('picker').set({'max': afterDate.toDate()},{muted: true});

                        from_date.valid();
                    }
                }
            });
            var to_date = $('#search_date_to').pickadate({
                firstDay: 1,
                // clear: '',
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

                        var beforeDate = currentDate.subtract(31, 'days');
                        from_date.pickadate('picker').set({'min': beforeDate.toDate()},{muted: true});

                        to_date.valid();
                    }
                }
            });

            var dr_from_date = $('#dr_search_date_from').pickadate({
                firstDay: 1,
                // clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        var old_date_formatted = $('input[name="dr_search_date_from_formatted"]').val();
                        var currentDate = moment(old_date_formatted);

                        var to_date_formatted = $('input[name="dr_search_date_to_formatted"]').val();
                        var toDate = moment(to_date_formatted);

                        if (currentDate.format('x') > toDate.format('x')) {
                            dr_to_date.pickadate('picker').clear();
                        }

                        var afterDate = currentDate.add(31, 'days');
                        dr_to_date.pickadate('picker').set({'max': afterDate.toDate()},{muted: true});

                        dr_from_date.valid();
                    }
                }
            });

            var dr_to_date = $('#dr_search_date_to').pickadate({
                firstDay: 1,
                // clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        var current_date_formatted = $('input[name="dr_search_date_to_formatted"]').val();
                        var currentDate = moment(current_date_formatted);

                        var from_date_formatted = $('input[name="dr_search_date_from_formatted"]').val();
                        var fromDate = moment(from_date_formatted);

                        if (currentDate.format('x') < fromDate.format('x')) {
                            dr_from_date.pickadate('picker').clear();
                        }

                        var beforeDate = currentDate.subtract(31, 'days');
                        dr_from_date.pickadate('picker').set({'min': beforeDate.toDate()},{muted: true});

                        dr_to_date.valid();
                    }
                }
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('cod.reports.sales.list') }}',
                        method:'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S. No.');
                            head.push('Tracking No.');
                            head.push('Account No.');
                            head.push('Shipper');
                            head.push('Pickup Address');
                            head.push('Vendor');
                            head.push('Order ID');
                            head.push('Order Date');
                            head.push('Consignee Name');
                            head.push('Consignee Phone No. 1');
                            head.push('Consignee Phone No. 2');
                            // head.push('Consignee Address');
                            head.push('Status');
                            head.push('Reason');
                            head.push('Payment Status');
                            head.push('Payment ID');
                            head.push('Processed Date');
                            head.push('Paid Date');
                            head.push('Service Type');
                            head.push('Product Category');
                            head.push('Description');
                            head.push('Arrival Date');
                            head.push('Shipping Mode');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Collection Amount');
                            head.push('Estimated Weight');
                            head.push('Actual Weight');
                            head.push('Chargeable Weight');
                            head.push('Weight Charges');
                            head.push('Cash Handling Charges');
                            head.push('Insurance Charges');
                            head.push('Packaging Charges');
                            head.push('Fuel Surcharge');
                            head.push('Return Charges');
                            head.push('Replacement Charges');
                            head.push('Try & Buy Charges');
                            head.push('NSA/OSA Charges');
                            head.push('GST Charges');
                            head.push('Intercept Charges');
                            head.push('Total Charges');
                            head.push('Estimated Charges');
                            head.push('Packing Charges');
                            head.push('Net Payable');
                            head.push('Delivered/Returned Date');
                            head.push('Received/Refused By');
                            head.push('Shipper Reference 1');
                            head.push('Shipper Reference 2');
                            head.push('Shipper Reference 3');
                            head.push('Shipper Reference 4');
                            head.push('Shipper Reference 5');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.account_no);
                                row.push(values.shipper);
                                row.push(values.pickup_address);
                                row.push(values.vendor);
                                row.push(values.order_id);
                                row.push(values.order_date);
                                row.push(values.consignee_name);
                                row.push(values.consignee_phone_number_1);
                                row.push(values.consignee_phone_number_2);
                                // row.push(values.consignee_address);
                                row.push(values.current_status);
                                row.push(values.reason_name);
                                row.push(values.payment_status);
                                row.push(values.payment_id);
                                row.push(values.processed_date);
                                row.push(values.paid_date);
                                row.push(values.service_type);
                                row.push(values.product_name);
                                row.push(values.description);
                                row.push(values.arrival_date);
                                row.push(values.shipping_mode);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.s_collection_amount);
                                row.push(values.estimated_weight);
                                row.push(values.actual_weight);
                                row.push(values.chargeable_weight);
                                row.push(values.weight_charges);
                                row.push(values.cash_handling_charges);
                                row.push(values.insurance_charges);
                                row.push(values.packaging_material_charges);
                                row.push(values.fuel_surcharge);
                                row.push(values.return_charges);
                                row.push(values.replacement_charges);
                                row.push(values.try_and_buy_charges);
                                row.push(values.nsa_osa_charges);
                                row.push(values.p_gst);
                                row.push(values.intercept_charges);
                                row.push(values.p_total_charges);
                                row.push(values.estimated_charges);
                                row.push(values.packaging_charges);
                                row.push(values.p_net_payable);
                                row.push(values.delivered_or_returned);
                                row.push(values.received_or_refused_by);
                                row.push(values.reference_1);
                                row.push(values.reference_2);
                                row.push(values.reference_3);
                                row.push(values.reference_4);
                                row.push(values.reference_5);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header:head};
                }
            } );
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Sales Report',
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
                ajax:{
                    url: '{{ route('cod.reports.sales.list') }}',
                    method:'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function (d) {
                        d.search_tracking = $('#search_tracking_no').val();
                        d.search_origin = $('#search_origin').val();
                        d.search_destination = $('#search_destination').val();
                        d.search_status = $('#search_status').val();
                        d.search_shipping_mode = $('#search_shipping_mode').val();
                        d.search_service_type = $('#search_service_type').val();
                        d.search_account_type = $('#search_account_type').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                        d.dr_search_date_from = $('input[name="dr_search_date_from_formatted"]').val();
                        d.dr_search_date_to = $('input[name="dr_search_date_to_formatted"]').val();
                    }
                },
                order: [[18, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'tracking_number' ,name: 'shipments.tracking_number', class: 'align-middle text-center tracking_number'},
                    { data:'account_no' ,name: 'u.id', class: 'align-middle account_no'},
                    { data:'shipper' ,name: 'u.name', class: 'align-middle shipper'},
                    { data:'pickup_address' ,name: 'usi.pickup_address', class: 'align-middle pickup_address'},
                    { data:'vendor' ,name: 'usi.vendor', class: 'align-middle vendor'},
                    { data:'order_id' ,name: 'shipments.order_id', class: 'align-middle order_id'},
                    { data:'order_date' ,name: 'sod.order_date', class: 'align-middle order_date'},
                    { data:'consignee_name' ,name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                    { data:'consignee_phone_number_1' ,name: 'shipments.consignee_phone_number_1', class: 'align-middle consignee_phone_number_1'},
                    { data:'consignee_phone_number_2' ,name: 'shipments.consignee_phone_number_2', class: 'align-middle consignee_phone_number_2'},
                    // { data:'consignee_address' ,name: 'shipments.consignee_address', class: 'align-middle consignee_address'},
                    { data:'current_status' ,name: 'ss.name', class: 'align-middle current_status'},
                    { data: 'reason_name' ,name: 'ssreason.name', class: 'align-middle reason_name'},
                    { data:'payment_status' ,name: 'sps.name', class: 'align-middle payment_status'},
                    { data:'payment_id' ,name: 'dps.id', class: 'align-middle payment_id'},
                    { data:'processed_date' ,name: 'spjproceed_date.created_at', class: 'align-middle processed_date'},
                    { data:'paid_date' ,name: 'spjpaid_date.created_at', class: 'align-middle paid_date'},
                    { data:'service_type' ,name: 'bt.booking_type', class: 'align-middle service_type'},
                    { data:'product_name', name: 'p.product_name', class: 'align-middle product_name'},
                    { data:'description', name: 'si.description', class: 'align-middle description'},
                    { data:'arrival_date' ,name: 'sj.created_at', class: 'align-middle arrival_date'},
                    { data:'shipping_mode' ,name: 'sm.mode', class: 'align-middle shipping_mode'},
                    { data:'origin' ,name: 'oc.name', class: 'align-middle origin'},
                    { data:'destination' ,name: 'dc.name', class: 'align-middle destination'},
                    { data:'s_collection_amount' ,name: 'shipments.amount', class: 'align-middle s_collection_amount'},
                    { data:'estimated_weight' ,name: 'shipments.estimated_weight', class: 'align-middle estimated_weight'},
                    { data:'actual_weight' ,name: 'shipments.actual_weight', class: 'align-middle actual_weight'},
                    { data:'chargeable_weight' ,name: 'shipments.chargeable_weight', class: 'align-middle chargeable_weight'},
                    { data:'weight_charges' ,name: 'shipments.weight_charges', class: 'align-middle weight_charges'},
                    { data:'cash_handling_charges' ,name: 'shipments.cash_handling_charges', class: 'align-middle cash_handling_charges'},
                    { data:'insurance_charges' ,name: 'shipments.insurance_charges', class: 'align-middle insurance_charges'},
                    { data:'packaging_material_charges' ,name: 'shipments.packaging_material_charges', class: 'align-middle packaging_material_charges'},
                    { data:'fuel_surcharge' ,name: 'shipments.fuel_surcharge', class: 'align-middle fuel_surcharge'},
                    { data:'return_charges' ,name: 'shipments.return_charges', class: 'align-middle return_charges'},
                    { data:'replacement_charges' ,name: 'shipments.replacement_charges', class: 'align-middle replacement_charges'},
                    { data:'try_and_buy_charges' ,name: 'shipments.try_and_buy_charges', class: 'align-middle try_and_buy_charges'},
                    { data:'nsa_osa_charges' ,name: 'shipments.nsa_osa_charges', class: 'align-middle nsa_osa_charges'},
                    { data:'p_gst' ,name: 'pps.p_gst', class: 'align-middle p_gst',sortable:false},
                    { data:'intercept_charges' ,name: 'shipments.intercept_charges', class: 'align-middle intercept_charges'},
                    { data:'p_total_charges' ,name: 'pps.charges', class: 'align-middle total_charges'},
                    { data:'estimated_charges' ,name: 'estimated_charges', class: 'align-middle estimated_charges',sortable:false},
                    { data:'packaging_charges' ,name: 'shipments.packaging_charges', class: 'align-middle packaging_charges',sortable:false},
                    { data: 'p_net_payable' ,name: 'pps.payable', class: 'align-middle net_payable'},
                    { data: 'delivered_or_returned' ,name: 'dr.created_at', class: 'align-middle delivered_or_returned'},
                    { data: 'received_or_refused_by' ,name: 'dr.received_or_refused_by', class: 'align-middle received_or_refused_by'},
                    { data: 'reference_1' ,name: 'ssr.reference_1', class: 'align-middle reference_1'},
                    { data: 'reference_2' ,name: 'ssr.reference_2', class: 'align-middle reference_2'},
                    { data: 'reference_3' ,name: 'ssr.reference_3', class: 'align-middle reference_3'},
                    { data: 'reference_4' ,name: 'ssr.reference_4', class: 'align-middle reference_4'},
                    { data: 'reference_5' ,name: 'ssr.reference_5', class: 'align-middle reference_5'}
                    
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });
            // $('#search_filter_btn').on('click',function () {
            //     table.draw();
            // });

            $.validator.addClassRules("require_one", {
                require_from_group: [1, ".require_one"]
            });

            $.validator.addClassRules("search_date_to", {
                required: function(element) {
                    return $("#search_date_from").val().length > 0;
                }
            });

           $.validator.addClassRules("dr_search_date_to", {
                required: function(element) {
                    return $("#dr_search_date_from").val().length > 0;
                }
            });

            $('#search_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    table.draw();
                }
            });

        });
    </script>
@endsection