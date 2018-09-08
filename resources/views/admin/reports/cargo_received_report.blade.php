@extends('admin.layout.master')

@section('content')
    <h1 class="mb-1">
        Cargo Received Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center">

                    <div class="col-3">
                        <fieldset class="form-group">
                            <input type="text" class="form-control" name="search_cargo_no" id="search_cargo_no" placeholder="Search Cargo Number">
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
                        <fieldset class="form-group">
                            <select name="search_shippimg_modes" id="search_shippimg_modes" class="form-control select2">
                                @foreach($shippimg_modes as $shippimg_mode)
                                    <option value="{{$shippimg_mode->id}}">{{$shippimg_mode->mode}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class="form-group">
                            <input type="text" name="transit_date" class="form-control bg-primary border-primary white rounded-right" id="transit_date" placeholder="Transit Date" data-value="">
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class="form-group">
                            <input type="text" name="received_date" class="form-control bg-primary border-primary white rounded-right" id="received_date" placeholder="Received Date" data-value="">
                        </fieldset>
                    </div>


                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Cargo No.</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Shipment(s)</th>
                        <th class="border-primary border-darken-1">Shipping Mode</th>
                        <th class="border-primary border-darken-1">Transitted By</th>
                        <th class="border-primary border-darken-1">Transit Date</th>
                        <th class="border-primary border-darken-1">Received By</th>
                        <th class="border-primary border-darken-1">Received Date</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
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
            $('#search_cargo_no').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#search_origin').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Origin',
                width:'100%',
                allowClear:true
            });
            $('#search_destination').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Destination',
                width:'100%',
                allowClear:true
            });
            $('#search_shippimg_modes').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Shipping Mode',
                width:'100%',
                allowClear:true
            });
            var transit_date = $('#transit_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#transit_date_root').css('top','40px');
                },
                onSet: function(context) {
                }
            });
            var received_date = $('#received_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#received_date_root').css('top','40px');
                },
                onSet: function(context) {
                }
            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.cargo_received.list') }}',
                        data: {
                            'page': 'all',
                            'search_cargo_no': $('#search_cargo_no').val(),
                            'search_origin': $('#search_origin').val(),
                            'search_destination': $('#search_destination').val(),
                            'search_shippimg_modes': $('#search_shippimg_modes').val(),
                            'search_transit_date': $('input[name="transit_date_formatted"]').val(),
                            'search_received_date': $('input[name="received_date_formatted"]').val()
                        },
                        success: function (result) {
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.cargo_id);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.shipments);
                                row.push(values.shipping_mode);
                                row.push(values.transit_by);
                                row.push(values.transit_at);
                                row.push(values.received_by);
                                row.push(values.received_at);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: $("#datatable thead tr th").map(function() { return this.innerHTML; }).get()};
                }
            } );
            var index_column = 0;
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                    extend: 'excelHtml5',
                    title: 'Received Cargo Report',
                    text:'<i class="la la-file-excel-o"></i> Excel',
                    },

                ],
                lengthMenu: [[25, 50, 100], [25, 50, 100]],
                pageLength: 25,
                stateSave: true,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.cargo_received.list') }}',
                    data: function (d) {
                        d.search_cargo_no = $('#search_cargo_no').val();
                        d.search_origin = $('#search_origin').val();
                        d.search_destination = $('#search_destination').val();
                        d.search_shippimg_modes = $('#search_shippimg_modes').val();
                        d.search_transit_date = $('input[name="transit_date_formatted"]').val();
                        d.search_received_date = $('input[name="received_date_formatted"]').val();
                    }
                },
                rowId: 'cargo_id',
                order: [[1, 'asc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'cargo_id', name: 'cargo_consignments.id', class: 'align-middle cargo_id'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'h.name', class: 'align-middle destination'},
                    {data: 'shipments', name: 'cargo_consignments.shipments', class: 'align-middle shipments'},
                    {data: 'shipping_mode', name: 'sm.mode', class: 'align-middle shipping_mode'},////
                    {data: 'transit_by', name: 'si.name', class: 'align-middle transit_by'},
                    {data: 'transit_at', name: 'cargo_consignments.created_at', class: 'align-middle transit_at'},
                    {data: 'received_by', name: 'ri.name', class: 'align-middle received_by'},
                    {data: 'received_at', name: 'cargo_consignments.updated_at', class: 'align-middle received_at'}
                    // {data: 'received_shipments', name: 'cargo_consignments.received_shipments', class: 'align-middle received_shipments'},
                    // {data: 'short_received', name: 'short_received', class: 'align-middle short_received'},
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

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.serial_number') || $(header).is('.received_shipments') || $(header).is('.short_received')) {
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
                }
            });

            $('#search_filter_btn').on('click',function () {
                table.draw();
            });

        });

    </script>
@endsection