@extends('admin.layout.master')

@section('title', 'Pending Shipments for Bag ')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Pending Shipments for Bag
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <div class="row mb-2 justify-content-center">
                                <div class="col-12 ">
                                    <form id="shipment_type_search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                                        <div class="col">
                                            <div class="form-group mr-1">
                                                <select name="search_shipping_mode" id="search_shipping_mode" class="form-control select2">
                                                    @foreach($shipping_mode as $mode)
                                                        <option value="{{$mode->id}}">{{$mode->mode}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <select name="shipment_type" class="select2" id="shipment_type">
                                                    <option value="" selected="selected"></option>
                                                    <option value="0">All</option>
                                                    <option value="1">Normal</option>
                                                    <option value="2">Return</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="form-group input-group ">
                                                <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                                                </div>
                                                <input type="text" name="search_date_from"
                                                    class="form-control pickadate bg-primary border-primary white rounded-right"
                                                    id="search_date_from" placeholder="Done Payment From Date" title="Arrival From Date" data-value="{{ Carbon\Carbon::today() }}">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group input-group">
                                                <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                                                </div>
                                                <input type="text" name="search_date_to"
                                                    class="form-control pickadate bg-primary border-primary white rounded-right"
                                                    id="search_date_to" placeholder="Done Payment To Date" title="Arrival To Date" data-value="{{ Carbon\Carbon::today() }}">
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="form-group ml-1">
                                                <button type="button" id="search_filter_btn" class="btn btn-primary">Search</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="col justify-content-end mb-3">
                                <div class="card-header">
                                    <div class="heading-elements">
                                        <ul class="list-inline">
                                            <li class="primary border-primary round" value="0" id="star_shippers_filter"><a>
                                                    Star Shippers</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Tracking Number</th>
                                    <th class="border-primary border-darken-1">Order ID</th>
                                    <th class="border-primary border-darken-1">Service Type</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Origin</th>
                                    <th class="border-primary border-darken-1">Origin Hub</th>
                                    <th class="border-primary border-darken-1">Destination</th>
                                    <th class="border-primary border-darken-1">Sub Station</th>
                                    <th class="border-primary border-darken-1">Shipper</th>
                                    <th class="border-primary border-darken-1">Amount</th>
                                    <th class="border-primary border-darken-1">Shipping Mode</th>
                                    <th class="border-primary border-darken-1">Booked Date/Time</th>
                                    <th class="border-primary border-darken-1">Arrival Date/Time</th>
                                    <th class="border-primary border-darken-1">Current Status Date/Time</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function() {

            var search_date_to = $('#shipment_type_search_form #search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#shipment_type_search_form #search_date_from').pickadate('picker').set('max', $('#shipment_type_search_form #search_date_to').pickadate('picker').get('select'));
                    }
                }
            });

            var search_date_from = $('#shipment_type_search_form #search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#shipment_type_search_form #search_date_to').pickadate('picker').set('min', $('#shipment_type_search_form #search_date_from').pickadate('picker').get('select'));
                    }
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
                        url: '{{ route('admin.cargo_manifest.bags.pending.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Tracking Number');
                            head.push('Order ID');
                            head.push('Service Type');
                            head.push('Status');
                            head.push('Origin');
                            head.push('Origin Hub');
                            head.push('Destination');
                            head.push('Sub Station');
                            head.push('Shipper');
                            head.push('Amount');
                            head.push('Shipping Mode');
                            head.push('Booked Datetime');
                            head.push('Arrival Datetime');
                            head.push('Current Status Datetime');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.tracking);
                                row.push(values.order_id);
                                row.push(values.service_type);
                                row.push(values.status);
                                row.push(values.origin);
                                row.push(values.origin_hub);
                                row.push(values.destination);
                                row.push(values.sub_station);
                                row.push(values.shipper);
                                row.push(values.amount);
                                row.push(values.shipping_mode);
                                row.push(values.booked_at);
                                row.push(values.arrival_at);
                                row.push(values.current_status);


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
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Pending For Bags',
                        className:'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                deferLoading: 0,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.cargo_manifest.bags.pending.list') }}',
                    data: function (d) {
                        d.shipment_type = $('#shipment_type_search_form #shipment_type').val();
                        d.search_shipping_mode = $('#shipment_type_search_form #search_shipping_mode').val();
                        d.star_shipper_filter = $('#star_shippers_filter').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                rowId: 'id',
                order: [[12, 'desc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'pickup_notes.id', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number', name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'order_id', name: 'shipments.order_id', class: 'align-middle order_id'},
                    {data: 'service_type', name: 'service_type', class: 'align-middle service_type'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'origin_hub', name: 'ohc.name', class: 'align-middle origin_hub'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'sub_station', name: 'dlm.area_name', class: 'align-middle sub_station',orderable: false,searchable: false},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'amount', name: 'shipments.amount', class: 'align-middle amount'},
                    {data: 'shipping_mode', name: 'shipping_mode', class: 'align-middle shipping_mode'},
                    {data: 'booked_at', name: 'shipments.created_at', class: 'align-middle booked_at'},
                    {data: 'arrival_at', name: 'shipments_journey.created_at', class: 'align-middle arrival_at'},
                    {data: 'current_status', name: 'csj.created_at', class: 'align-middle current_status'}
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
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '</select>';
                    var service_drop_select = '<select name="service_select" id="service_select" class="select2 form-control">' +
                        '</select>';
                    var mode_drop_select = '<select name="mode_select" id="mode_select" class="select2 form-control">' +
                        '</select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.sub_station')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.status')){
                            $(drop_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.service_type')){
                            $(service_drop_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.shipping_mode')){
                            $(mode_drop_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
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
                    var data = $.map({!! $shipment_status !!}, function (obj) {
                        obj.id = obj.id // replace pk with your identifier

                        return obj;
                    });
                    var data = $.map({!! $shipment_status !!}, function (obj) {
                        obj.text = obj.text || obj.name; // replace name with the property used for the text

                        return obj;
                    });

                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        data:data,
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data1 = $.map({!! $service_type !!}, function (obj) {
                        obj.id = obj.id // replace pk with your identifier

                        return obj;
                    });
                    var data1 = $.map({!! $service_type !!}, function (obj) {
                        obj.text = obj.booking_type; // replace name with the property used for the text

                        return obj;
                    });

                    $("#service_select").prepend('<option value="" selected></option>').select2({
                        data:data1,
                        placeholder: "Select Service",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data2 = $.map({!! $shipping_mode !!}, function (obj) {
                        obj.id = obj.id // replace pk with your identifier

                        return obj;
                    });
                    var data2 = $.map({!! $shipping_mode !!}, function (obj) {
                        obj.text = obj.mode; // replace name with the property used for the text

                        return obj;
                    });

                    $("#mode_select").prepend('<option value="" selected></option>').select2({
                        data:data2,
                        placeholder: "Select Mode",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });

            $('#shipment_type_search_form #shipment_type').select2({
                width: '100%',
                placeholder: 'Shipment Type',
                allowClear:true
            });

            $('#shipment_type_search_form #search_shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Shipping Mode',
                allowClear:true
            });

            $('#star_shippers_filter').on('click',function () {
                $('#star_shippers_filter').val(1);
                table.draw(true);
                $('#star_shippers_filter').val(0);
            });

            $('#search_filter_btn').on('click',function () {
                table.draw(true);
            });

        });
    </script>
@endsection