@extends('admin.layout.master')

@section('title', 'Overall Sales Report')

@section('content')
    <h1 class="mb-1">
        Overall Sales Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div id="search_form" class="row mb-2 justify-content-center">

                    {{--<div class="col-4">--}}
                        {{--<fieldset class="form-group">--}}
                            {{--<input type="text" class="form-control" name="search_dn_no" id="search_dn_no" placeholder="Search delivery Note Number">--}}
                        {{--</fieldset>--}}
                    {{--</div>--}}
                    <div class="col-4">
                        <fieldset class="form-group">
                            <input type="text" class="form-control" name="search_tracking_no" id="search_tracking_no" placeholder="Search Tracking Number">
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
                            <select name="search_destination" id="search_destination" class="form-control select2">
                                @foreach($cities as $destination)
                                    <option value="{{$destination->id}}">{{$destination->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_hub" id="search_hub" class="form-control select2">
                                @foreach($hubs as $hub)
                                    <option value="{{$hub->id}}">{{$hub->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_status" id="search_status" class="form-control select2">
                                @foreach($statuses as $status)
                                    <option value="{{$status->id}}">{{$status->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>

                    <div class="col-4">

                        <div class="form-group input-group ml-1">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Date (From)">
                        </div>
                    </div>
                    <div class="col-4 ">
                        <div class="form-group input-group ml-1">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Date (To)">
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
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Account No.</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Payment Status</th>
                        <th class="border-primary border-darken-1">Service Type</th>
                        <th class="border-primary border-darken-1">Arrival Date</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Collection Amount</th>
                        <th class="border-primary border-darken-1">Actual Weight</th>
                        <th class="border-primary border-darken-1">Weight Charges</th>
                        <th class="border-primary border-darken-1">Cash Handling Charges</th>
                        <th class="border-primary border-darken-1">Insurance Charges</th>
                        <th class="border-primary border-darken-1">Packaging Charges</th>
                        <th class="border-primary border-darken-1">Return Charges</th>
                        <th class="border-primary border-darken-1">Replacement Charges</th>
                        <th class="border-primary border-darken-1">Try & Buy Charges</th>
                        <th class="border-primary border-darken-1">Fuel Surcharge</th>
                        <th class="border-primary border-darken-1">GST</th>
                        <th class="border-primary border-darken-1">Total Charges</th>
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
            $('#search_tracking_no').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
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
            $('#search_destination').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Destination City',
                width:'100%',
                allowClear:true
            });
            $('#search_hub').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Hub',
                width:'100%',
                allowClear:true
            });
            $('#search_status').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Status',
                width:'100%',
                allowClear:true
            });

            var submission_date = $('#submission_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#submission_date_root').css('top','40px');
                },
                onSet: function(context) {
                }
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
                        $('#search_date_to').pickadate('picker').set('min', $('#search_date_from').pickadate('picker').get('select'));
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
                        $('#search_date_from').pickadate('picker').set('max', $('#search_date_to').pickadate('picker').get('select'));
                    }
                }
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.overall_sales.list') }}',
                        data: {
                            'page': 'all',
                            'search_tracking': $('#search_tracking_no').val(),
                            'search_shipper': $('#search_shipper').val(),
                            'search_origin': $('#search_origin').val(),
                            'search_destination': $('#search_destination').val(),
                            'search_hub': $('#search_hub').val(),
                            'search_status': $('#search_status').val(),
                            'search_date_from': $('input[name="search_date_from_formatted"]').val(),
                            'search_date_to': $('input[name="search_date_to_formatted"]').val()
                        },
                        success: function (result) {
                            head = [];

                            head.push('S. No.');
                            head.push('Tracking No.');
                            head.push('Account No.');
                            head.push('Shipper');
                            head.push('Status');
                            head.push('Payment Status');
                            head.push('Service Type');
                            head.push('Arrival Date');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Hub');
                            head.push('Collection Amount');
                            head.push('Actual Weight');
                            head.push('Weight Charges');
                            head.push('Cash Handling Charges');
                            head.push('Insurance Charges');
                            head.push('Packaging Charges');
                            head.push('Return Charges');
                            head.push('Replacement Charges');
                            head.push('Try & Buy Charges');
                            head.push('Fuel Surcharge');
                            head.push('GST');
                            head.push('Total Charges');
                            head.push('Net Payable');
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.account_no);
                                row.push(values.shipper);
                                row.push(values.current_status);
                                row.push(values.payment_status);
                                row.push(values.service_type);
                                row.push(values.arrival_date);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.hub);
                                row.push(values.collection_amount);
                                row.push(values.actual_weight);
                                row.push(values.weight_charges);
                                row.push(values.cash_handling_charges);
                                row.push(values.insurance_charges);
                                row.push(values.packaging_material_charges);
                                row.push(values.return_charges);
                                row.push(values.replacement_charges);
                                row.push(values.try_and_buy_charges);
                                row.push(values.fuel_surcharge);
                                row.push(values.gst);
                                row.push(values.total_charges);
                                row.push(values.net_payable);

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
                scrollX: true, scrollY: '350px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Overall Sales Report',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax:{
                    url: '{{ route('admin.reports.overall_sales.list') }}',
                    data: function (d) {
                        d.search_tracking = $('#search_tracking_no').val();
                        d.search_shipper = $('#search_shipper').val();
                        d.search_origin = $('#search_origin').val();
                        d.search_destination = $('#search_destination').val();
                        d.search_hub = $('#search_hub').val();
                        d.search_status = $('#search_status').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                order: [[7, 'asc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'tracking_number' ,name: 'shipments.tracking_number', class: 'align-middle text-center tracking_number'},
                    { data:'account_no' ,name: 'u.id', class: 'align-middle account_no'},
                    { data:'shipper' ,name: 'u.name', class: 'align-middle shipper'},
                    { data:'current_status' ,name: 'ss.name', class: 'align-middle current_status'},
                    { data:'payment_status' ,name: 'ss.name', class: 'align-middle payment_status'},
                    { data:'service_type' ,name: 'bt.booking_type', class: 'align-middle service_type'},
                    { data:'arrival_date' ,name: 'sj.created_at', class: 'align-middle arrival_date'},
                    { data:'origin' ,name: 'oc.name', class: 'align-middle origin'},
                    { data:'destination' ,name: 'dc.name', class: 'align-middle destination'},
                    { data:'hub' ,name: 'h.name', class: 'align-middle hub'},
                    { data:'collection_amount' ,name: 'shipments.amount', class: 'align-middle collection_amount'},
                    { data:'actual_weight' ,name: 'shipments.actual_weight', class: 'align-middle actual_weight'},
                    { data:'weight_charges' ,name: 'shipments.weight_charges', class: 'align-middle weight_charges'},
                    { data:'cash_handling_charges' ,name: 'shipments.cash_handling_charges', class: 'align-middle cash_handling_charges'},
                    { data:'insurance_charges' ,name: 'shipments.insurance_charges', class: 'align-middle insurance_charges'},
                    { data:'packaging_material_charges' ,name: 'shipments.packaging_material_charges', class: 'align-middle packaging_material_charges'},
                    { data:'return_charges' ,name: 'shipments.return_charges', class: 'align-middle return_charges'},
                    { data:'replacement_charges' ,name: 'shipments.replacement_charges', class: 'align-middle replacement_charges'},
                    { data:'try_and_buy_charges' ,name: 'shipments.try_and_buy_charges', class: 'align-middle try_and_buy_charges'},
                    { data:'fuel_surcharge' ,name: 'shipments.fuel_surcharge', class: 'align-middle fuel_surcharge'},
                    { data:'gst' ,name: 'pps.gst', class: 'align-middle gst'},
                    { data:'total_charges' ,name: 'pps.charges', class: 'align-middle total_charges'},
                    { data:'net_payable' ,name: 'pps.payable', class: 'align-middle net_payable'},
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