@extends('admin.layout.master')

@section('title', 'Cargo Received Report')

@section('content')
    <h1 class="mb-1">
        Cargo Received Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div id="search_form" class="row mb-2 justify-content-center">

                    <div class="col-4">
                        <fieldset class="form-group">
                            <input type="text" class="form-control" name="search_cargo_no" id="search_cargo_no" placeholder="Search Cargo Number">
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_origin" id="search_origin" class="form-control select2">
                                @foreach($cities as $city)
                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_destination" id="search_destination" class="form-control select2">
                                @foreach($cities as $city)
                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_shippimg_modes" id="search_shippimg_modes" class="form-control select2">
                                @foreach($shippimg_modes as $shippimg_mode)
                                    <option value="{{$shippimg_mode->id}}">{{$shippimg_mode->mode}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">

                            <div class="form-group input-group ">
                                <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                </div>
                            <input type="text" name="transit_date" class="form-control bg-primary border-primary white rounded-right" id="transit_date" placeholder="Transit Date" data-value="">
                            </div>

                    </div>
                    <div class="col-4">

                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                </div>
                            <input type="text" name="received_date" class="form-control bg-primary border-primary white rounded-right" id="received_date" placeholder="Received Date" data-value="">
                            </div>

                    </div>
                    <div class="col-3 ">
                        <div class="form-group">
                            <select name="cargo_type" class="select2" id="cargo_type">
                                <option value="" selected="selected"></option>
                                <option value="0">All</option>
                                <option value="1">Normal</option>
                                <option value="2">Return</option>
                            </select>
                        </div>

                    </div>
                    <div class="col-3 ">

                        <div class="form-group input-group ml-1">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Date (From)">
                        </div>
                    </div>
                    <div class="col-3 ">
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
                        <th class="border-primary border-darken-1">Cargo No.</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Shipment(s)</th>
                        <th class="border-primary border-darken-1">Shipping Mode</th>
                        <th class="border-primary border-darken-1">Cargo Type</th>
                        <th class="border-primary border-darken-1">Shipments Weight</th>
                        <th class="border-primary border-darken-1">Chargeable Weight</th>
                        <th class="border-primary border-darken-1">Actual Weight</th>
                        <th class="border-primary border-darken-1">Vendor Weight</th>
                        <th class="border-primary border-darken-1">Transitted By</th>
                        <th class="border-primary border-darken-1">Transit Date</th>
                        <th class="border-primary border-darken-1">Received By</th>
                        <th class="border-primary border-darken-1">Received Date</th>
                        <th class="border-primary border-darken-1">Aging</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="shipments" role="dialog" aria-labelledby="shipments_title" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="shipments_title">Shipment(s)</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
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
            function print(id) {
                $.ajax({
                    url: '{!! route('admin.reports.cargo_received.print') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function(data) {
                        var tab = window.open('', '_blank');

                        if(!tab) {
                            swal({
                                title: 'Popup Blocker Enabled!',
                                text: 'Please add this site to your exception list.',
                                icon: 'error',
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                        }
                        else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                    });
            }
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
            $('#cargo_type').select2({
                width: '100%',
                placeholder: 'Cargo Type'
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
            $('#search_form #search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_to').pickadate('picker').set('min', $('#search_form #search_date_from').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_form #search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_from').pickadate('picker').set('max', $('#search_form #search_date_to').pickadate('picker').get('select'));
                    }
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
                            'search_received_date': $('input[name="received_date_formatted"]').val(),
                            'search_date_from': $('input[name="search_date_from_formatted"]').val(),
                            'search_date_to': $('input[name="search_date_to_formatted"]').val(),
                            'cargo_type': $('#cargo_type').val()
                        },
                        success: function (result) {
                            head = [];

                            head.push('S. No');
                            head.push('Cargo No.');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Shipment(s)');
                            head.push('Shipping Mode');
                            head.push('Cargo Type');
                            head.push('Shipments Weight');
                            head.push('Chargeable Weight');
                            head.push('Actual Weight');
                            head.push('Vendor Weight');
                            head.push('Transitted By');
                            head.push('Transit Date');
                            head.push('Received By');
                            head.push('Received Date');
                            head.push('Aging');
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.cargo_id);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.shipments);
                                row.push(values.shipping_mode);
                                row.push(values.cargo_type);
                                row.push(values.shipments_weight);
                                row.push(values.chargeable_weight);
                                row.push(values.actual_weight);
                                row.push(values.vendor_weight);
                                row.push(values.transit_by);
                                row.push(values.transit_at);
                                row.push(values.received_by);
                                row.push(values.received_at);
                                row.push(values.aging);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            var index_column = 0;
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '350px',
                buttons: [
                    {
                    extend: 'excelHtml5',
                    title: 'Received Cargo Report',
                    text:'<i class="la la-file-excel-o"></i> Excel',
                    },

                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
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
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                        d.cargo_type = $('#cargo_type').val();
                    }
                },
                rowId: 'cargo_id',
                order: [[12, 'asc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'cargo_id_link', name: 'cargo_consignments.id', class: 'align-middle cargo_id_link'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'h.name', class: 'align-middle destination'},
                    {data: 'shipments_link', name: 'cargo_consignments.shipments', class: 'align-middle text-center shipments_link'},
                    {data: 'shipping_mode', name: 'sm.mode', class: 'align-middle shipping_mode'},
                    {data: 'cargo_type', name: 'cargo_consignments.type', class: 'align-middle cargo_type'},
                    {data: 'shipments_weight', name: 'cargo_consignments.shipments_weight', class: 'align-middle shipments_weight'},
                    {data: 'chargeable_weight', name: 'cargo_consignments.chargeable_weight', class: 'align-middle chargeable_weight', orderable: false, searchable: false},
                    {data: 'actual_weight', name: 'cargo_consignments.actual_weight', class: 'align-middle actual_weight'},
                    {data: 'vendor_weight', name: 'cargo_consignments.vendor_weight', class: 'align-middle vendor_weight'},
                    {data: 'transit_by', name: 'si.name', class: 'align-middle transit_by'},
                    {data: 'transit_at', name: 'cargo_consignments.created_at', class: 'align-middle transit_at'},
                    {data: 'received_by', name: 'ri.name', class: 'align-middle received_by'},
                    {data: 'received_at', name: 'cargo_consignments.updated_at', class: 'align-middle received_at'},
                    {data: 'aging', name: 'aging', class: 'align-middle aging',orderable: false, searchable: false},
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

            $('#datatable tbody').on('click', 'tr td a.cargo_print', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                if(id){
                    print(id);
                }
            });

            var route = '{!! route('admin.tracking.index') !!}';

            $('#datatable tbody').on('click', 'tr td.shipments_link button', function() {
                var id = parseInt($(this).parents('tr').attr('id'));

                $('#shipments .modal-body').html('');

                $.ajax({
                    url: '{!! route('admin.reports.cargo_received.shipments') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var details = '<table class="table table-sm table-bordered"><tbody>';

                            details += '<tr>';

                            details += '<td class="border-primary border-darken-1 align-middle text-center"><strong>Shipment</strong></td>';
                            details += '<td class="border-primary border-darken-1 align-middle text-center"><strong>Estimated Weight</strong></td>';
                            details += '<td class="border-primary border-darken-1 align-middle text-center"><strong>Actual Weight</strong></td>';
                            details += '<td class="border-primary border-darken-1 align-middle text-center"><strong>Chargeable Weight</strong></td>';

                            details += '</tr>';

                            $.each(data, function(index, shipment) {
                                details += '<tr>';

                                details += '<td class="align-middle text-center"><u><a href=' + route + '?tracking_number=' + shipment.tracking_number + ' target="_blank">' + shipment.tracking_number + '</a></u></td>';

                                details += '<td class="align-middle text-center">' + shipment.estimated_weight + '</td>';
                                details += '<td class="align-middle text-center">' + shipment.actual_weight + '</td>';
                                details += '<td class="align-middle text-center">' + shipment.chargeable_weight + '</td>';

                                details += '</tr>';
                            });

                            details += '</tbody></table>';

                            $('#shipments .modal-body').html(details);

                            $('#shipments').modal('show');
                        }
                    });
            });
        });

    </script>
@endsection