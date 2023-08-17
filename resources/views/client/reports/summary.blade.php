@extends('client.layout.master')

@section('title', 'Report - Summary')

@section('content')

    <h1 class="mb-1">
        Summary Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                <input type="hidden" id="cards_filter_input">
                <div class="row mb-2 justify-content-center">
                    <div class="col-3">
                        <fieldset class="form-group">
                            <select name="search_user" id="search_user" class="form-control select2">
                                <option value="{{$user->id}}">{{$user->name}}</option>
                                @foreach($sister_users as $sister_user)
                                    <option value="{{$sister_user->id}}">{{$sister_user->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class="form-group">
                            <select name="search_origin" id="search_origin" class="form-control select2">
                                @foreach($cities as $city)
                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class="form-group">
                            <select name="search_destination" id="search_destination" class="form-control select2">
                                @foreach($cities as $city)
                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <div class="form-group input-group ml">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Date (From)" data-value="{{$thirtyday}}">
                        </div>
                    </div>
                    <div class="col-3 ">
                        <div class="form-group input-group ml">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Date (To)" data-value="{{$today}}">
                        </div>
                    </div>
                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>

                <div id="report_data">
                    <div class="row">
                        <div class="col-3">
                            <div class="card pull-up">
                                <div class="card-content border rounded cursor-pointer" id="total_shipments">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-grid font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-right">
                                                <h3 id="total">{{$stats['total']}}</h3>
                                                <span>Total Shipment(s)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card bg-gradient-directional-primary pull-up cursor-pointer">
                                <div class="card-content" id="total_pending">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-hourglass text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="booked">{{$stats['booked']}}</h3>
                                                <span>Booked Shipment(s)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card bg-gradient-directional-info pull-up cursor-pointer">
                                <div class="card-content" id="total_received">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-layers text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="received">{{$stats['received']}}</h3>
                                                <span class="font-13">Received / In-Transit Shipment(s)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card bg-gradient-directional-success pull-up cursor-pointer">
                                <div class="card-content" id="total_delivered">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-check text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="delivered">{{$stats['delivered']}}</h3>
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
                            <div class="card bg-gradient-directional-inprocess pull-up cursor-pointer">
                                <div class="card-content" id="total_inprocess">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-shuffle text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="in_process">{{$stats['in_process']}}</h3>
                                                <span>In Process Shipment(s)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="card bg-gradient-directional-warning pull-up cursor-pointer">
                                <div class="card-content" id="total_return">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-loop text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="return">{{$stats['return']}}</h3>
                                                <span>Returned Shipment(s)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card bg-gradient-directional-red pull-up cursor-pointer">
                                <div class="card-content" id="total_cancelled">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-close text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="canceled">{{$stats['canceled']}}</h3>
                                                <span>Cancelled Shipment(s)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{--row end--}}
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Order ID</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Contact Person</th>
                        <th class="border-primary border-darken-1">Phone No.</th>
                        <th class="border-primary border-darken-1">Rider Picked Status Date</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Return Reason</th>
                        <th class="border-primary border-darken-1">Payment Status</th>
                        <th class="border-primary border-darken-1">Service Type</th>
                        <th class="border-primary border-darken-1">Product Category</th>
                        <th class="border-primary border-darken-1">Description</th>
                        <th class="border-primary border-darken-1">Booking Date</th>
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

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
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
        .bg-gradient-directional-inprocess {
            background-image: linear-gradient(45deg, #d6a42a, #ffec07fa);
            background-repeat: repeat-x;
        }
        span.font-13{
            font-size: 13px;
        }
        .show_active{
            -webkit-box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            -moz-box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
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

    <script type="text/javascript">
        $(document).ready(function () {
            // $('#search_tracking_no').inputmask({
            //     'alias': 'integer',
            //     'allowMinus': false,
            //     'allowPlus': false
            // });
            $('#search_user').select2({
                placeholder:'Select User',
                width:'100%',
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
            var from_date;
            var to_date;

            from_date = $('#search_date_from').pickadate({
                firstDay: 1,
                clear: false,
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
                        to_date.pickadate('picker').set({'select': afterDate.toDate()},{muted: true});


                    }
                }
            });
            to_date = $('#search_date_to').pickadate({
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

                        var beforeDate = currentDate.subtract(31, 'days');
                        from_date.pickadate('picker').set({'min': beforeDate.toDate()},{muted: true});
                    }
                }
            });
            // $('#search_status').prepend('<option value="" selected="selected"></option>').select2({
            //     placeholder:'Select Status',
            //     width:'100%',
            //     allowClear:true
            // });
            //
            //
            // var from_date = $('#search_date_from').pickadate({
            //     firstDay: 1,
            //     clear: '',
            //     selectYears: true,
            //     selectMonths: true,
            //     formatSubmit: 'yyyy-mm-dd 00:00:00',
            //     hiddenSuffix: '_formatted',
            //     onSet: function(context) {
            //         if (context.select) {
            //             var old_date_formatted = $('input[name="search_date_from_formatted"]').val();
            //             var currentDate = moment(old_date_formatted);
            //
            //             var to_date_formatted = $('input[name="search_date_to_formatted"]').val();
            //             var toDate = moment(to_date_formatted);
            //
            //             if (currentDate.format('x') > toDate.format('x')) {
            //                 to_date.pickadate('picker').clear();
            //             }
            //
            //             var afterDate = currentDate.add(31, 'days');
            //             to_date.pickadate('picker').set({'max': afterDate.toDate()},{muted: true});
            //
            //
            //         }
            //     }
            // });
            // var to_date = $('#search_date_to').pickadate({
            //     firstDay: 1,
            //     clear: '',
            //     selectYears: true,
            //     selectMonths: true,
            //     formatSubmit: 'yyyy-mm-dd 23:59:59',
            //     hiddenSuffix: '_formatted',
            //     onSet: function(context) {
            //         if (context.select) {
            //             var current_date_formatted = $('input[name="search_date_to_formatted"]').val();
            //             var currentDate = moment(current_date_formatted);
            //
            //             var from_date_formatted = $('input[name="search_date_from_formatted"]').val();
            //             var fromDate = moment(from_date_formatted);
            //
            //             if (currentDate.format('x') < fromDate.format('x')) {
            //                 from_date.pickadate('picker').clear();
            //             }
            //
            //             var beforeDate = currentDate.subtract(31, 'days');
            //             from_date.pickadate('picker').set({'min': beforeDate.toDate()},{muted: true});
            //         }
            //     }
            // });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('cod.reports.summary.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S. No.');
                            head.push('Tracking No.');
                            head.push('Order ID');
                            head.push('Shipper');
                            head.push('Contact Person');
                            head.push('Phone No.');
                            head.push('Rider Picked Status Date');
                            head.push('Status');
                            head.push('Return Reason');
                            head.push('Payment Status');
                            head.push('Service Type');
                            head.push('Product Category');
                            head.push('Description');
                            head.push('Booking Date');
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
                                row.push(values.user_name);
                                row.push(values.consignee_name);
                                row.push(values.consignee_phone);
                                row.push(values.rider_picked_status_date);
                                row.push(values.current_status);
                                row.push(values.return_reason);
                                row.push(values.payment_status);
                                row.push(values.service_type);
                                row.push(values.product_name);
                                row.push(values.description);
                                row.push(values.booking_date);
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
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excelHtml5',
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
                deferLoading: 0,
                ajax:{
                    url: '{{ route('cod.reports.summary.list') }}',
                    data: function (d) {
                        d.search_user = $('#search_user').val();
                        d.search_origin = $('#search_origin').val();
                        d.search_destination = $('#search_destination').val();
                        d.cards_filter = $('#cards_filter_input').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                order: [[13, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'tracking_number_link' ,name: 'shipments.tracking_number', class: 'align-middle text-center tracking_number'},
                    { data:'order_id' ,name: 'shipments.order_id', class: 'align-middle order_id'},
                    { data:'user_name' ,name: 'u.name', class: 'align-middle user_name'},
                    { data:'consignee_name' ,name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                    { data:'consignee_phone' ,name: 'shipments.consignee_phone_number_1', class: 'align-middle consignee_phone'},
                    { data:'rider_picked_status_date' ,name: 'sjrp.created_at', class: 'align-middle rider_picked_status_date'},
                    { data:'current_status' ,name: 'ss.name', class: 'align-middle current_status'},
                    { data: 'return_reason' ,name: 'ssr.name', class: 'align-middle return_reason'},
                    { data:'payment_status' ,name: 'sps.name', class: 'align-middle payment_status'},
                    { data:'service_type' ,name: 'bt.booking_type', class: 'align-middle service_type'},
                    { data:'product_name', name: 'p.product_name', class: 'align-middle product_name'},
                    { data:'description', name: 'si.description', class: 'align-middle description'},
                    { data:'booking_date' ,name: 'booking_date', class: 'align-middle booking_date'},
                    { data:'arrival_date' ,name: 'sj.created_at', class: 'align-middle arrival_date'},
                    { data:'origin' ,name: 'oc.name', class: 'align-middle origin'},
                    { data:'destination' ,name: 'dc.name', class: 'align-middle destination'},
                    { data:'collection_amount' ,name: 'shipments.amount', class: 'align-middle collection_amount'},
                    { data:'actual_weight' ,name: 'shipments.actual_weight', class: 'align-middle actual_weight'},
                    { data:'weight_charges' ,name: 'shipments.weight_charges', class: 'align-middle weight_charges'},
                    { data:'cash_handling_charges' ,name: 'shipments.cash_handling_charges', class: 'align-middle cash_handling_charges'},
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
                console.log($('#cards_filter_input').val());
                $("#report_data div").removeClass("show_active");
                box.addClass('show_active');
            }
            $('#search_filter_btn').on('click',function () {
                table.draw();
                get_summary_cards_data();
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
                $('#cards_filter_input').val('returned');
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

            function get_summary_cards_data() {
                var from_date = $('input[name="search_date_from_formatted"]').val();
                var to_date = $('input[name="search_date_to_formatted"]').val();
                var origin = $('#search_origin').val();
                var user = $('#search_user').val();
                var destination = $('#search_destination').val();
                $.ajax({
                    url: '{!! route('cod.reports.summary.data') !!}',
                    method: 'post',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'from_date': from_date,
                        'to_date': to_date,
                        'user': user,
                        'origin': origin,
                        'destination': destination,

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

                    }else{
                        $('#total').text(0);
                        $('#booked').text(0);
                        $('#received').text(0);
                        $('#delivered').text(0);
                        $('#in_process').text(0);
                        $('#return').text(0);
                        $('#canceled').text(0);

                    }
                });
            }
        });
    </script>

@endsection