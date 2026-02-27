@extends('admin.layout.master')

@section('title', '2nd Attempt Performance Report')

@section('content')
    <h1 class="mb-1">
        2nd Attempt Performance Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div id="search_form" class="row mb-2 justify-content-center">

{{--                    <div class="col-3">--}}
{{--                        <fieldset class="form-group">--}}
{{--                            <select name="search_status" id="search_status" class="form-control select2">--}}
{{--                                @foreach($statuses as $status)--}}
{{--                                    <option value="{{$status->id}}">{{$status->name}}</option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                        </fieldset>--}}
{{--                    </div>--}}

                    <div class="col-3">

                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Date (From)" data-value="{{ Carbon\Carbon::now() }}">
                        </div>
                    </div>
                    <div class="col-3 ">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Date (To)" data-value="{{ Carbon\Carbon::now() }}">
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
                            <th class="border-primary border-darken-1">No of Attempts</th>
                            <th class="border-primary border-darken-1">Shipper</th>
                            <th class="border-primary border-darken-1">Service Type</th>
                            <th class="border-primary border-darken-1">Sub Segment</th>
                            <th class="border-primary border-darken-1">Origin</th>
                            <th class="border-primary border-darken-1">Destination</th>
                            <th class="border-primary border-darken-1">Destination Hub</th>
                            <th class="border-primary border-darken-1">Shipping Mode</th>
                            <th class="border-primary border-darken-1">Rider Category</th>
                            <th class="border-primary border-darken-1">Arrival Date</th>
                            <th class="border-primary border-darken-1">Reached at Destination Date</th>
                            {{--first out for delivery  kae baat ju status hai wu yaa aye ga--}}
                            <th class="border-primary border-darken-1">First Status</th>
                            <th class="border-primary border-darken-1">First Status Date</th>
                            {{--lastest status of shipment--}}
                            <th class="border-primary border-darken-1">Last Status</th>
                            <th class="border-primary border-darken-1">Last Status Date</th>

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

        table {
            width: 100% !important;
        }

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
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.time.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            $('#search_status').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Status',
                width:'100%',
                allowClear:true
            });

            var from_date = $('#search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {

                    var old_date_formatted = $('input[name="search_date_from_formatted"]').val();
                    var contractMoment = moment(old_date_formatted);
                    var current = moment(contractMoment).add(31, 'days');
                    var current_max = moment(contractMoment).add(1, 'days');
                    to_date.pickadate('picker').set('min', new Date(old_date_formatted),{muted:true});
                    to_date.pickadate('picker').set('max', new Date(current.toDate()),{muted:true});
                    to_date.pickadate('picker').set('select', new Date(current.toDate()),{muted:true});
                }
            });

            var to_date = $('#search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {

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
                        url: '{{ route('admin.reports.shipment_attempt_performance.list') }}',
                        method:'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S. No.');
                            head.push('Tracking No');
                            head.push('No of Attempts');
                            head.push('Shipper');
                            head.push('Service Type');
                            head.push('Sub Segment');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Destination HUB');
                            head.push('Shipping Mode');
                            head.push('Rider Category');
                            head.push('Arrival Date');
                            head.push('Reached at Destination Date');
                            head.push('First Status');
                            head.push('First Status Date');
                            head.push('Last Status');
                            head.push('Last Status Date');



                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.ofd_attempts);
                                row.push(values.shipper);
                                row.push(values.service_type);
                                row.push(values.sub_segment);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.hub_city);
                                row.push(values.shipping_mode);
                                row.push(values.rider_category);
                                row.push(values.arrival_date);
                                row.push(values.arrived_at_destination_date);
                                row.push(values.before_ofd_status);
                                row.push(values.before_ofd_date);
                                row.push(values.shipment_status);
                                row.push(values.shipment_status_date);
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
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: '2nd Attempt Performance Report',
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
                    url: '{{ route('admin.reports.shipment_attempt_performance.list') }}',
                    method:'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function (d) {

                        // d.search_status = $('#search_status').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                order: [[1, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'tracking_number_link' ,name: 'shipments.tracking_number', class: 'align-middle  tracking_number_link'},
                    { data:'ofd_attempts' ,name: 'ofd_attempts', class: 'align-middle text-center ofd_attempts'},
                    { data:'shipper' ,name: 'u.name', class: 'align-middle shipper'},
                    { data:'service_type' ,name: 'bt.id', class: 'align-middle  service_type'},
                    { data:'sub_segment' ,name: 'sub_sg.id', class: 'align-middle  sub_segment'},
                    { data:'origin' ,name: 'oc.name', class: 'align-middle origin'},
                    { data:'destination' ,name: 'dc.name', class: 'align-middle destination'},
                    { data:'hub_city' ,name: 'h.name', class: 'align-middle hub_city'},
                    { data:'shipping_mode' ,name: 'sh.id', class: 'align-middle shipping_mode'},
                    { data:'rider_category' ,name: 'rider_category', class: 'align-middle rider_category'},
                    { data:'arrival_date' ,name: 'shipment_arival_journey.created_at', class: 'align-middle arrival_date'},
                    { data:'arrived_at_destination_date' ,name: 'sjc.created_at', class: 'align-middle arrived_at_destination_date'},
                    { data:'before_ofd_status' ,name: 'before_ofd_status', class: 'align-middle before_ofd_status'},
                    { data:'before_ofd_date' ,name: 'before_ofd_date', class: 'align-middle before_ofd_date'},
                    { data:'shipment_status' ,name: 'ss.id', class: 'align-middle shipment_status'},
                    { data:'shipment_status_date' ,name: 'lj.created_at', class: 'align-middle shipment_status_date'},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var booking_type_select = '<select name="booking_type" id="booking_type" class="select2 form-control"></select>';
                    var sub_segment_select = '<select name="sub_segment" id="sub_segment" class="select2 form-control"></select>';
                    var shipping_mode_select = '<select name="shipping_mode" id="shipping_mode" class="select2 form-control"></select>';
                    var rider_cat_select = '<select name="rider_category" id="rider_category" class="select2 form-control"></select>';
                    var last_status_select = '<select name="last_status_select" id="last_status_select" class="select2 form-control"></select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.shipments_unverified_link')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.service_type')){
                            $(booking_type_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        else if($(header).is('.sub_segment')){
                            $(sub_segment_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        else if($(header).is('.shipping_mode')){
                            $(shipping_mode_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        else if($(header).is('.rider_category')){
                            $(rider_cat_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        // else if($(header).is('.shipment_status')){
                        //     $(last_status_select).appendTo($(search))
                        //         .on( 'change', function () {
                        //             column.search($(this).val(), false, false, true).draw();
                        //         } ).wrap(td);
                        // }
                        else if($(header).is('.action')  || $(header).is('.before_ofd_status') || $(header).is('.before_ofd_date')  || $(header).is('.ofd_attempts') || $(header).is('.shipment_status')){
                            $(td).appendTo($(search));
                        }
                        else {
                            var current = $(input).appendTo($(search)).on('change', function() {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });


                    var bk_types = $.map({!! $booking_types !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.booking_type;

                        return obj;
                    });
                    $("#booking_type").prepend('<option value="" selected></option>').select2({
                        data:bk_types,
                        placeholder: "Select Service Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    var sub_segs = $.map({!! $sub_segments !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;

                        return obj;
                    });

                    $("#sub_segment").prepend('<option value="" selected></option>').select2({
                        data:sub_segs,
                        placeholder: "Sub Segment",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    var shipping_md = $.map({!! $shipping_modes !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.mode;
                        return obj;
                    });

                    $("#shipping_mode").prepend('<option value="" selected></option>').select2({
                        data:shipping_md,
                        placeholder: "Shipping Modes",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    var rider_cat = $.map({!! $rider_operation_categories !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;
                        return obj;
                    });

                    $("#rider_category").prepend('<option value="" selected></option>').select2({
                        data:rider_cat,
                        placeholder: "Operation Rider Category",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    var ship_status = $.map({!! $shipment_statuses !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;
                        return obj;
                    });

                    $("#last_status_select").prepend('<option value="" selected></option>').select2({
                        data:ship_status,
                        placeholder: "Shipment Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    this.api().table().columns.adjust();
                }
            });
            $('#search_filter_btn').on('click',function () {
                table.draw();
            });

        });
    </script>
@endsection