@extends('admin.layout.master')

@section('title', 'Summary Report')

@section('content')
    <h1 class="mb-1">
        Summary Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center">
                    <div class="col-12 ">
                        <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate" method="post" action="{{route('admin.reports.customer_sales.export_to_excel')}}">

                            <div class="col-4">
                                <div class="form-group pb-1">
                                    <select name="shipper" class="select2" id="shipper" data-rule-required="true" data-msg-required="Shipper is required">
                                        @foreach($shippers as $shipper)
                                            <option value="{{ $shipper->id }}">{{ $shipper->name }}</option>
                                        @endforeach
                                    </select>
                                </div></div>
                            <div class="col-4">
                                <div class="form-group pb-1">
                                    <select name="origin" class="select2" id="origin">
                                        @foreach($hubs as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group pb-1">
                                    <select name="destination" class="select2" id="destination">
                                        @foreach($hubs as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                    </div>
                                    <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From" data-rule-required="true" data-msg-required="Date(From) is required" data-value="{{$thirtyday}}">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                    </div>
                                    <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To" data-rule-required="true" data-msg-required="Date(To) is required" data-value="{{$today}}">
                                </div>
                            </div>

                            <div class="col-2">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-outline-info btn-min-width"><i class="la la-search"></i> Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="" id="report_data">
                    <div class="row">
                        <input type="hidden" id="cards_filter_input">
                        <div class="col-3">
                            <div class="card pull-up">
                                <div class="card-content border rounded" id="total_shipments">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-grid font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-right">
                                                <h3 id="total">0</h3>
                                                <span>Total Booked Shipment(s)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card bg-gradient-directional-primary pull-up">
                                <div class="card-content" id="total_pending">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-hourglass text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="booked">0</h3>
                                                <span>Pending Shipment(s)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card bg-gradient-directional-info pull-up">
                                <div class="card-content" id="total_received">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-layers text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="received">0</h3>
                                                <span>Received Shipment(s)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><div class="col-3">
                            <div class="card bg-gradient-directional-success pull-up">
                                <div class="card-content" id="total_delivered">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-check text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="delivered">0</h3>
                                                <span>Delivered Shipment(s)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-3">
                            <div class="card bg-gradient-directional-warning pull-up">
                                <div class="card-content" id="total_return">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-loop text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="return">0</h3>
                                                <span>Returned Shipment(s)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card bg-gradient-directional-inprocess pull-up">
                                <div class="card-content" id="total_inprocess">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-shuffle text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="in_process">0</h3>
                                                <span>In Process Shipment(s)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card bg-gradient-directional-red pull-up">
                                <div class="card-content" id="total_cancelled">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-close text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="canceled">0</h3>
                                                <span>Cancelled Shipment(s)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">

                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Tracking No.</th>
                            <th class="border-primary border-darken-1">Order ID</th>
                            <th class="border-primary border-darken-1">Status</th>
                            <th class="border-primary border-darken-1">Payment Status</th>
                            <th class="border-primary border-darken-1">Service Type</th>
                            <th class="border-primary border-darken-1">Product Category</th>
                            <th class="border-primary border-darken-1">Description</th>
                            <th class="border-primary border-darken-1">Arrival Date</th>
                            <th class="border-primary border-darken-1">Origin</th>
                            <th class="border-primary border-darken-1">Destination</th>
                            <th class="border-primary border-darken-1">Collection Amount</th>
                            <th class="border-primary border-darken-1">Actual Weight</th>
                            <th class="border-primary border-darken-1">Weight Charges</th>
                            <th class="border-primary border-darken-1">Cash Handling Charges</th>

                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <style>
        .bg-gradient-directional-inprocess {
            background-image: linear-gradient(45deg, #d6a42a, #ffec07fa);
            background-repeat: repeat-x;
        }
    </style>

@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {
            $('#search_form #origin').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Origin',
                allowClear:true
            });
            $('#search_form #destination').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Destination',
                allowClear:true
            });

            $('#search_form #shipper').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Shipper',
                allowClear:true
            });
            var thirtydays = '{{ $thirtyday }}';
            var today = '{{ $today }}';
            var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                min: new Date(thirtydays),
                max : new Date(today),
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#from_date_root').css('top','40px');
                },
                onSet: function(context) {
                    var old_date_formatted = $('input[name="from_date_formatted"]').val();
                    to_date.pickadate('picker').set('min', new Date(old_date_formatted),{muted:true});
                }
            });
            var to_date = $('#to_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max : new Date(today),
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#to_date_root').css('top', '40px');
                },
                onSet: function(context) {
                    // var current_date_formatted = $('input[name="to_date_formatted"]').val();
                    // from_date.pickadate('picker').set('max',new Date(current_date_formatted),{muted:true});
                }
            });
            $('#search_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    blockPagePermanently();


                    var from_date = $('#search_form input[name="from_date_formatted"]').val();
                    var to_date = $('#search_form input[name="to_date_formatted"]').val();
                    var origin = $('#origin').val();
                    var destination = $('#destination').val();
                    var shipper = $('#shipper').val();
                    $.ajax({
                        url: '{!! route('admin.reports.summary.data') !!}',
                        method: 'post',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'from_date': from_date,
                            'to_date': to_date,
                            'origin': origin,
                            'destination': destination,
                            'shipper': shipper,
                        }
                    }).done(function (data) {
                       if(data.status){
                            $('#total').text(data.stats.total);
                            $('#booked').text(data.stats.booked);
                            $('#received').text(data.stats.received);
                            $('#delivered').text(data.stats.delivered);
                            $('#in_process').text(data.stats.in_process);
                            $('#return').text(data.stats.return);
                            $('#canceled').text(data.stats.canceled);
                            table.draw();

                       }else{
                           $('#total').text(0);
                           $('#booked').text(0);
                           $('#received').text(0);
                           $('#delivered').text(0);
                           $('#in_process').text(0);
                           $('#return').text(0);
                           $('#canceled').text(0);
                           table.draw();
                       }
                        UnblockPagePermanently();
                    });
                    return false;
                }
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('cod.reports.summary.list') }}',
                        data: {
                            'page': 'all',
                            'search_origin': $('#origin').val(),
                            'search_destination': $('#destination').val(),
                            'search_shipper': $('#shipper').val(),
                            'cards_filter': $('#cards_filter_input').val(),
                            'search_date_from': $('input[name="from_date_formatted"]').val(),
                            'search_date_to': $('input[name="to_date_formatted"]').val(),

                        },
                        success: function (result) {
                            head = [];

                            head.push('S. No.');
                            head.push('Tracking No.');
                            head.push('Order ID');
                            head.push('Status');
                            head.push('Payment Status');
                            head.push('Service Type');
                            head.push('Product Category');
                            head.push('Description');
                            head.push('Arrival Date');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Collection Amount');
                            head.push('Actual Weight');
                            head.push('Weight Charges');
                            head.push('Cash Handling Charges');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.order_id);
                                row.push(values.current_status);
                                row.push(values.payment_status);
                                row.push(values.service_type);
                                row.push(values.product_name);
                                row.push(values.description);
                                row.push(values.arrival_date);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.collection_amount);
                                row.push(values.actual_weight);
                                row.push(values.weight_charges);
                                row.push(values.cash_handling_charges);

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
                        title: 'Summary Reportt',
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
                    url: '{{ route('admin.reports.summary.list') }}',
                    data: function (d) {
                        d.search_origin = $('#origin').val();
                        d.search_destination = $('#destination').val();
                        d.search_shipper = $('#shipper').val();
                        d.cards_filter = $('#cards_filter_input').val();
                        d.search_date_from = $('input[name="from_date_formatted"]').val();
                        d.search_date_to = $('input[name="to_date_formatted"]').val();
                    }
                },
                order: [[8, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'tracking_number_link' ,name: 'shipments.tracking_number', class: 'align-middle text-center tracking_number'},
                    { data:'order_id' ,name: 'shipments.order_id', class: 'align-middle order_id'},
                    { data:'current_status' ,name: 'ss.name', class: 'align-middle current_status'},
                    { data:'payment_status' ,name: 'sps.name', class: 'align-middle payment_status'},
                    { data:'service_type' ,name: 'bt.booking_type', class: 'align-middle service_type'},
                    { data:'product_name', name: 'p.product_name', class: 'align-middle product_name'},
                    { data:'description', name: 'si.description', class: 'align-middle description'},
                    { data:'arrival_date' ,name: 'sj.created_at', class: 'align-middle arrival_date'},
                    { data:'origin' ,name: 'oc.name', class: 'align-middle origin'},
                    { data:'destination' ,name: 'dc.name', class: 'align-middle destination'},
                    { data:'collection_amount' ,name: 'shipments.amount', class: 'align-middle collection_amount'},
                    { data:'actual_weight' ,name: 'shipments.actual_weight', class: 'align-middle actual_weight'},
                    { data:'weight_charges' ,name: 'shipments.weight_charges', class: 'align-middle weight_charges'},
                    { data:'cash_handling_charges' ,name: 'shipments.cash_handling_charges', class: 'align-middle cash_handling_charges'}
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
            $('#total_shipments').on('click', function () {
                $('#cards_filter_input').val('total');
                table.draw();
            });
            $('#total_pending').on('click', function () {
                $('#cards_filter_input').val('booked');
                table.draw();
            });
            $('#total_received').on('click', function () {
                $('#cards_filter_input').val('received');
                table.draw();
            });
            $('#total_delivered').on('click', function () {
                $('#cards_filter_input').val('delivered');
                table.draw();
            });
            $('#total_return').on('click', function () {
                $('#cards_filter_input').val('returned');
                table.draw();
            });
            $('#total_inprocess').on('click', function () {
                $('#cards_filter_input').val('in_process');
                table.draw();
            });
            $('#total_cancelled').on('click', function () {
                $('#cards_filter_input').val('cancelled');
                table.draw();
            });

        });

    </script>
@endsection