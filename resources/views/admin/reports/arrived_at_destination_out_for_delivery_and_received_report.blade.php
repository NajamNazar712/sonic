@extends('admin.layout.master')

@section('title', 'Arrived At Destination VS Out For Delivery VS Receive Report')

@section('content')
    <h1 class="mb-1">
        Arrived At Destination VS Out For Delivery VS Receive Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <form id="search_form" novalidate="novalidate">
                    <div class="row mb-2 justify-content-center">
                        <div class="col-3">
                            <fieldset class="form-group">
                                <select name="search_zone" id="search_zone" class="form-control select2">
                                    @foreach($zones as $zone)
                                        <option value="{{$zone->id}}">{{$zone->name}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-3">
                            <fieldset class="form-group">
                                <select name="search_hub" id="search_hub" class="form-control select2">
                                    @foreach($hubs as $hub)
                                        <option value="{{$hub->id}}">{{$hub->name}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-3">
                            <fieldset class="form-group">
                                <select name="search_destination" id="search_destination" class="form-control select2">
                                    @foreach($destination_cities as $destination_city)
                                        <option value="{{$destination_city->id}}">{{$destination_city->name}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-3">
                            <fieldset class="form-group">
                                <select name="shipment_type" id="shipment_type" class="form-control select2" data-rule-required="true" data-msg-required="Shipments Type is required">
                                    <option value="1">Normal</option>
                                    <option value="2">Return</option>
                                </select>
                            </fieldset>
                        </div>

                        <div class="col-3 ">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                    <span class="la la-calendar-o"></span>
                                </span>
                                </div>
                                <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From" data-rule-required="true" data-msg-required="From date is required" data-value="{{ Carbon\Carbon::now()->subDays(7) }}">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                    <span class="la la-calendar-o"></span>
                                </span>
                                </div>
                                <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To" data-rule-required="true" data-msg-required="To date is required" data-value="{{ Carbon\Carbon::now() }}">
                            </div>
                        </div>
                        <div class="col-2">
                            <button type="submit" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                        </div>
                    </div>
                </form>
                <div id="datatable_wrapper">
                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Hub</th>
                            <th class="border-primary border-darken-1">Total Booked</th>
                            <th class="border-primary border-darken-1">Total Arrived At Destination/Origin</th>
                            <th class="border-primary border-darken-1">Total In Transit</th>
                            <th class="border-primary border-darken-1">Pending Arrived At Destination/Origin</th>
                            <th class="border-primary border-darken-1">Out For Delivery</th>
                            <th class="border-primary border-darken-1">Delivered</th>
                            <th class="border-primary border-darken-1">Delivered %</th>
                            <th class="border-primary border-darken-1">Undelivered</th>
                            <th class="border-primary border-darken-1">Undelivered %</th>
                            <th class="border-primary border-darken-1">Confirmation Pending</th>
                            <th class="border-primary border-darken-1">Confirmation Pending %</th>
                        {{--</tr>--}}
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
    <style type="text/css">
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

    <script type="text/javascript">
        $(document).ready(function () {
            $('#datatable_wrapper').hide();
            var booked = 0;
            var arrived_at_destination = 0;
            var in_transit = 0;
            var pending_arrived_at_destination = 0;
            var out_for_delivery = 0;
            var delivered_shipments = 0;
            var delivered_shipments_per = 0;
            var undelivered_shipments = 0;
            var undelivered_shipments_per = 0;
            var confirmation_pending_shipments = 0;
            var confirmation_pending_shipments_per = 0;

            $('#search_zone').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Zone',
                width:'100%',
                allowClear:true
            });
            $('#search_hub').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Hub',
                width:'100%',
                allowClear:true
            });
            $('#search_destination').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Destination',
                width:'100%',
                allowClear:true
            });
            $('#shipment_type').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Type',
                width:'100%',
                allowClear:true
            });




            var today = '{{ Carbon\Carbon::now() }}';
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
                    var current = moment(contractMoment).add(7, 'days');
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

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    blockPagePermanently();
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.destination_delivery_received.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            footer = [];

                            head.push('S.No');
                            head.push('Hub');
                            head.push('Total Booked');
                            head.push('Total Arrived At Destination/Origin');
                            head.push('Total In Transit');
                            head.push('Pending Arrived At Destination/Origin');
                            head.push('Out For Delivery');
                            head.push('Delivered');
                            head.push('Delivered %');
                            head.push('Undelivered');
                            head.push('Undelivered %');
                            head.push('Confirmation Pending');
                            head.push('Confirmation Pending %');
                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.hub_name);
                                row.push(values.booked);
                                row.push(values.arrived_at_destination);
                                row.push(values.in_transit);
                                row.push(values.pending_arrived_at_destination);
                                row.push(values.out_for_delivery);
                                row.push(values.delivered_shipments);
                                row.push(values.delivered_shipments_per);
                                row.push(values.undelivered_shipments);
                                row.push(values.undelivered_shipments_per);
                                row.push(values.confirmation_pending_shipments);
                                row.push(values.confirmation_pending_shipments_per);

                                body.push(row);
                            });

                            footer.push('-');
                            footer.push('Total');
                            footer.push(booked.toFixed(2));
                            footer.push(arrived_at_destination.toFixed(2));
                            footer.push(in_transit.toFixed(2));
                            footer.push(pending_arrived_at_destination.toFixed(2));
                            footer.push(out_for_delivery.toFixed(2));
                            footer.push(delivered_shipments.toFixed(2));
                            footer.push(delivered_shipments_per.toFixed(2));
                            footer.push(undelivered_shipments.toFixed(2));
                            footer.push(undelivered_shipments_per.toFixed(2));
                            footer.push(confirmation_pending_shipments.toFixed(2));
                            footer.push(confirmation_pending_shipments_per.toFixed(2));
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header: head, footer: footer};
                }
            } );

            $('#datatable').append("<tfoot><tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr></tfoot>");
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Route Distribution Summary Report',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                        footer: true
                    },
                ],
                lengthMenu: [[1000, -1], [1000, 'All']],
                autoWidth:false,
                pageLength: 1000,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax:{
                    url: '{{ route('admin.reports.destination_delivery_received.list') }}',
                    data: function (d) {
                        d.search_destination = $('#search_destination').val();
                        d.search_zone = $('#search_zone').val();
                        d.search_hub = $('#search_hub').val();
                        d.shipment_type = $('#shipment_type').val();
                        d.search_from = $('input[name="from_date_formatted"]').val();
                        d.search_to = $('input[name="to_date_formatted"]').val();
                    }
                },
                // order: [[1, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'hub_name' ,name: 'c.name', class: 'align-middle text-center hub_name'},
                    { data:'booked', class: 'align-middle booked', orderable: false, searchable: false},
                    { data:'arrived_at_destination', class: 'align-middle arrived_at_destination', orderable: false, searchable: false},
                    { data:'in_transit', class: 'align-middle in_transit', orderable: false, searchable: false},
                    { data:'pending_arrived_at_destination', class: 'align-middle pending_arrived_at_destination', orderable: false, searchable: false},
                    { data:'out_for_delivery', class: 'align-middle out_for_delivery', orderable: false, searchable: false},
                    { data:'delivered_shipments', class: 'align-middle delivered_shipments', orderable: false, searchable: false},
                    { data:'delivered_shipments_per', class: 'align-middle delivered_shipments_per', orderable: false, searchable: false},
                    { data:'undelivered_shipments', class: 'align-middle undelivered_shipments', orderable: false, searchable: false},
                    { data:'undelivered_shipments_per', class: 'align-middle undelivered_shipments_per', orderable: false, searchable: false},
                    { data:'confirmation_pending_shipments', class: 'align-middle confirmation_pending_shipments', orderable: false, searchable: false},
                    { data:'confirmation_pending_shipments_per', class: 'align-middle confirmation_pending_shipments_per', orderable: false, searchable: false},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                },
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    api.columns('.hub_name', {
                        page: 'current'
                    }).every(function() {
                        $(this.footer()).html('Total');
                    });
                    api.columns('.booked', {
                        page: 'current'
                    }).every(function() {
                        booked = this
                            .data()
                            .reduce(function(a, b) {
                                var x = parseFloat(a) || 0;
                                var y = parseFloat(b) || 0;
                                return x + y;
                            }, 0);
                        $(this.footer()).html(booked.toFixed(2));
                    });
                    api.columns('.arrived_at_destination', {
                        page: 'current'
                    }).every(function() {
                        arrived_at_destination = this
                            .data()
                            .reduce(function(a, b) {
                                var x = parseFloat(a) || 0;
                                var y = parseFloat(b) || 0;
                                return x + y;
                            }, 0);
                        $(this.footer()).html(arrived_at_destination.toFixed(2));
                    });
                    api.columns('.in_transit', {
                        page: 'current'
                    }).every(function() {
                        in_transit = this
                            .data()
                            .reduce(function(a, b) {
                                var x = parseFloat(a) || 0;
                                var y = parseFloat(b) || 0;
                                return x + y;
                            }, 0);
                        $(this.footer()).html(in_transit.toFixed(2));
                    });
                    api.columns('.pending_arrived_at_destination', {
                        page: 'current'
                    }).every(function() {
                        pending_arrived_at_destination = this
                            .data()
                            .reduce(function(a, b) {
                                var x = parseFloat(a) || 0;
                                var y = parseFloat(b) || 0;
                                return x + y;
                            }, 0);
                        $(this.footer()).html(pending_arrived_at_destination.toFixed(2));
                    });
                    api.columns('.out_for_delivery', {
                        page: 'current'
                    }).every(function() {
                        out_for_delivery = this
                            .data()
                            .reduce(function(a, b) {
                                var x = parseFloat(a) || 0;
                                var y = parseFloat(b) || 0;
                                return x + y;
                            }, 0);
                        $(this.footer()).html(out_for_delivery.toFixed(2));
                    });
                    api.columns('.delivered_shipments', {
                        page: 'current'
                    }).every(function() {
                        delivered_shipments = this
                            .data()
                            .reduce(function(a, b) {
                                var x = parseFloat(a) || 0;
                                var y = parseFloat(b) || 0;
                                return x + y;
                            }, 0);
                        $(this.footer()).html(delivered_shipments.toFixed(2));
                    });
                    api.columns('.delivered_shipments_per', {
                        page: 'current'
                    }).every(function() {
                        delivered_shipments_per = (delivered_shipments/booked)*100;
                        $(this.footer()).html(delivered_shipments_per.toFixed(2));
                    });
                    api.columns('.undelivered_shipments', {
                        page: 'current'
                    }).every(function() {
                        undelivered_shipments = this
                            .data()
                            .reduce(function(a, b) {
                                var x = parseFloat(a) || 0;
                                var y = parseFloat(b) || 0;
                                return x + y;
                            }, 0);
                        $(this.footer()).html(undelivered_shipments.toFixed(2));
                    });
                    api.columns('.undelivered_shipments_per', {
                        page: 'current'
                    }).every(function() {
                        undelivered_shipments_per = (undelivered_shipments/booked)*100;
                        $(this.footer()).html(undelivered_shipments_per.toFixed(2));
                    });
                    api.columns('.confirmation_pending_shipments', {
                        page: 'current'
                    }).every(function() {
                        confirmation_pending_shipments = this
                            .data()
                            .reduce(function(a, b) {
                                var x = parseFloat(a) || 0;
                                var y = parseFloat(b) || 0;
                                return x + y;
                            }, 0);
                        $(this.footer()).html(confirmation_pending_shipments.toFixed(2));
                    });
                    api.columns('.confirmation_pending_shipments_per', {
                        page: 'current'
                    }).every(function() {
                        confirmation_pending_shipments_per = (confirmation_pending_shipments/booked)*100;
                        $(this.footer()).html(confirmation_pending_shipments_per.toFixed(2));
                    });
                }
            });

            $('#search_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $('#datatable_wrapper').show();
                    table.draw();
                }
            });

        });

    </script>
@endsection