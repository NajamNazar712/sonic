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

                           {{-- <div class="col-4">
                                <div class="form-group pb-1">
                                    <select name="shipper" class="select2" id="shipper" data-rule-required="true" data-msg-required="Shipper is required">
                                        @foreach($shippers as $shipper)
                                            <option value="{{ $shipper->id }}">{{ $shipper->name }}</option>
                                        @endforeach
                                    </select>
                                </div></div>--}}
                            <div class="col-4">
                                <fieldset class="form-group pb-1">
                                    <select name="search_shipper" id="search_shipper" class="form-control select2" required data-rule-required="true" data-msg-required="This field is required">
                                        @foreach($shippers as $shipper)
                                            <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
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
                                <fieldset class="form-group">
                                    <select name="search_shipping_mode" id="search_shipping_mode" class="form-control select2">
                                        @foreach($shipping_modes as $shipping_mode)
                                            <option value="{{$shipping_mode->id}}">{{$shipping_mode->mode}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
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

                            <div class="col-2 mt-2">
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
                                                <span>Total Shipment(s)</span>
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
                                                <span>Booked Shipment(s)</span>
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
                                                <span class="font-13">Received / In-Transit Shipment(s)</span>
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
                            <div class="card bg-gradient-directional-return_intransit pull-up">
                                <div class="card-content" id="total_return_intransit">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-loop text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="returned_intransit">0</h3>
                                                <span>Return - In Transit Shipment(s)</span>
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
                            <th class="border-primary border-darken-1">Shipper</th>
                            <th class="border-primary border-darken-1">Vendor</th>
                            <th class="border-primary border-darken-1">Status</th>
                            <th class="border-primary border-darken-1">Reason</th>
                            <th class="border-primary border-darken-1">Remark</th>
                            <th class="border-primary border-darken-1">Total Attempt</th>
                            <th class="border-primary border-darken-1">Payment Status</th>
                            <th class="border-primary border-darken-1">Service Type</th>
                            <th class="border-primary border-darken-1">Arrival Date</th>
                            <th class="border-primary border-darken-1">Last Status Date</th>
                            <th class="border-primary border-darken-1">Origin</th>
                            <th class="border-primary border-darken-1">Destination</th>
                            <th class="border-primary border-darken-1">Hub</th>
                            <th class="border-primary border-darken-1">Consignee Name</th>
                            <th class="border-primary border-darken-1">Consignee Contact</th>
                            <th class="border-primary border-darken-1">Consignee Address</th>
                            <th class="border-primary border-darken-1">Collection Amount</th>
                            <th class="border-primary border-darken-1">Booking Date</th>


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
        .bg-gradient-directional-return_intransit {
            background-image: linear-gradient(45deg, #ff39aed6, #bb82e7);
            background-repeat: repeat-x;
        }
        
        .show_active{
            -webkit-box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            -moz-box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
        }
        span.font-13{
            font-size: 13px;
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
            $('#search_shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Shipping Mode',
                width:'100%',
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
            $('#search_shipper').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Shipper",
                allowClear:true,
            });
            var thirtydays = '{{ $thirtyday }}';
            var today = '{{ $today }}';
            var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                // min: new Date(thirtydays),
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
                    var contractMoment = moment(old_date_formatted);
                    var current = moment(contractMoment).add(29, 'days');
                    to_date.pickadate('picker').set('min', new Date(old_date_formatted),{muted:true});
                    to_date.pickadate('picker').set('max', new Date(current.toDate()),{muted:true});
                    to_date.pickadate('picker').set('select', new Date(current.toDate()),{muted:true});
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
                    var shipper = $('#search_shipper').val();
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
                            $('#returned_intransit').text(data.stats.return_intransit);
                            $('#canceled').text(data.stats.canceled);
                            table.draw();

                       }else{
                           $('#total').text(0);
                           $('#booked').text(0);
                           $('#received').text(0);
                           $('#delivered').text(0);
                           $('#in_process').text(0);
                           $('#return').text(0);
                           $('#returned_intransit').text(0);
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
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.summary.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S. No.');
                            head.push('Tracking No.');
                            head.push('Order ID');
                            head.push('Shipper');
                            head.push('Vendor');
                            head.push('Status');
                            head.push('Reason');
                            head.push('Remark');
                            head.push('Total Attempt');
                            head.push('Payment Status');
                            head.push('Service Type');
                            head.push('Arrival Date');
                            head.push('Last Status Date');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Hub');
                            head.push('Consignee Name');
                            head.push('Consignee Contact');
                            head.push('Consignee Address');
                            head.push('Collection Amount');
                            head.push('Booking Date');



                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.order_id);
                                row.push(values.shipper);
                                row.push(values.vendor);
                                row.push(values.current_status);
                                row.push(values.reason);
                                row.push(values.remark);
                                row.push(values.total_attempt);
                                row.push(values.payment_status);
                                row.push(values.service_type);
                                row.push(values.arrival_date);
                                row.push(values.last_status_date);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.hub);
                                row.push(values.consignee_name);
                                row.push(values.consignee_phone);
                                row.push(values.consignee_address);
                                row.push(values.collection_amount);
                                row.push(values.booking_date);

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
                        className: 'btn btn-primary',
                        title: 'Summary Report',
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
                       /* d.search_shipper = $('#shipper').val();*/
                        d.search_shipper = $('#search_shipper').val();
                        d.cards_filter = $('#cards_filter_input').val();
                        d.search_shipping_mode = $('#search_shipping_mode').val();
                        d.search_date_from = $('input[name="from_date_formatted"]').val();
                        d.search_date_to = $('input[name="to_date_formatted"]').val();
                    }
                },
                order: [[8, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'tracking_number_link' ,name: 'shipments.tracking_number', class: 'align-middle text-center tracking_number'},
                    { data:'order_id' ,name: 'shipments.order_id', class: 'align-middle order_id'},
                    { data:'shipper' ,name: 'u.name', class: 'align-middle shipper'},
                    { data:'vendor' ,name: 'u.name', class: 'align-middle shipper'},
                    { data:'current_status' ,name: 'ss.name', class: 'align-middle current_status'},
                    { data:'reason' ,name: 'reason', class: 'align-middle reason'},
                    { data:'remark' ,name: 'remark', class: 'align-middle remark'},
                    { data:'total_attempt' ,name: 'total_attempt', class: 'align-middle total_attempt'},
                    { data:'payment_status' ,name: 'sps.name', class: 'align-middle payment_status'},
                    { data:'service_type' ,name: 'bt.booking_type', class: 'align-middle service_type'},
                    { data:'arrival_date' ,name: 'sj.created_at', class: 'align-middle arrival_date'},
                    { data:'last_status_date' ,name: 'sju.created_at', class: 'align-middle last_status_date'},
                    { data:'origin' ,name: 'oc.name', class: 'align-middle origin'},
                    { data:'destination' ,name: 'dc.name', class: 'align-middle destination'},
                    { data:'hub' ,name: 'h.name', class: 'align-middle hub'},
                    { data: 'consignee_name', name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                    { data: 'phone', name: 'phone', class: 'align-middle phone'},
                    { data: 'consignee_address', name: 'shipments.consignee_address', class: 'align-middle consignee_address'},
                    { data: 'collection_amount' ,name: 'shipments.amount', class: 'align-middle collection_amount'},
                    { data: 'booking_date', name: 'shipments.created_at', class: 'align-middle booking_date'},

                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });
            function add_animation(box) {
                $("#report_data div").removeClass("show_active");
                box.addClass('show_active');
            }
            $('#search_filter_btn').on('click',function () {
                table.draw();
            });
            $('#total_shipments').on('click', function () {
                add_animation($(this));
                $('#cards_filter_input').val('total');
                table.draw();
            });
            $('#total_pending').on('click', function () {
                add_animation($(this));
                $('#cards_filter_input').val('booked');
                table.draw();
            });
            $('#total_received').on('click', function () {
                add_animation($(this));
                $('#cards_filter_input').val('received');
                table.draw();
            });
            $('#total_delivered').on('click', function () {
                add_animation($(this));
                $('#cards_filter_input').val('delivered');
                table.draw();
            });
            $('#total_return').on('click', function () {
                add_animation($(this));
                $('#cards_filter_input').val('returned');
                table.draw();
            });
            $('#total_return_intransit').on('click', function () {
                add_animation($(this));
                $('#cards_filter_input').val('returned_intransit');
                table.draw();
            });
            
            
            $('#total_inprocess').on('click', function () {
                add_animation($(this));
                $('#cards_filter_input').val('in_process');
                table.draw();
            });
            $('#total_cancelled').on('click', function () {
                add_animation($(this));
                $('#cards_filter_input').val('cancelled');
                table.draw();
            });

        });

    </script>
@endsection