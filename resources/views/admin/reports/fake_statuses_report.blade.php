@extends('admin.layout.master')

@section('title', 'Fake Statuses Report')

@section('content')
    <h1 class="mb-1">
        Fake Statuses Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center">
                    <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                        <div class="form-group">
                                <input type="text" class="form-control" name="search_tracking_no" id="search_tracking_no" placeholder="Search Tracking Number">
                        </div>
                        <div class="form-group ml-1">
                            <select name="riders" class="select2" id="riders">
                                @foreach($riders as $rider)
                                    <option value="{{ $rider->id }}">{{ $rider->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group ml-1">
                            <select name="hubs" class="select2" id="hubs">
                                @foreach($hubs as $hub)
                                    <option value="{{ $hub->id }}">{{ $hub->name }}</option>
                                @endforeach
                            </select>
                        </div>

                            <div class="form-group input-group ml-1">
                                <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                </div>

                                <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Search Date (From)">
                            </div>


                            <div class="form-group input-group ml-1">
                                <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                </div>

                                <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Search Date (To)" disabled>
                            </div>


                        <div class="col-2 mt-2">
                            <button type="button" id="search_filter_btn" class="btn btn-primary"><i class="la la-search"></i> Search</button>
                        </div>
                    </form>

                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Rider Name</th>
                        <th class="border-primary border-darken-1">Rider City</th>
                        <th class="border-primary border-darken-1">Delivery Note No.</th>
                        <th class="border-primary border-darken-1">Delivery Note Created At</th>
                        <th class="border-primary border-darken-1">Delivery Note Verified At</th>
                        <th class="border-primary border-darken-1">Total Shipments</th>
                        <th class="border-primary border-darken-1">Undelivered Shipments</th>
                        <th class="border-primary border-darken-1">Shipments Marked With Fake Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!--Shipments popup -->
    <div class="modal fade" id="shipments_modal" data-backdrop="static" role="dialog" aria-labelledby="shipments_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="shipments_modal_title">Shipment(s)</h4>

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
    <!--Shipments popup -->
    <!--Delivered Shipments popup -->
    <div class="modal fade" id="undelivered_shipments_modal" data-backdrop="static" role="dialog" aria-labelledby="undelivered_shipments_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="undelivered_shipments_modal_title">Undelivered Shipment(s)</h4>

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
    <!--Shipments popup -->
    <div class="modal fade" id="fake_status_shipments_modal" data-backdrop="static" role="dialog" aria-labelledby="fake_status_shipments_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="fake_status_shipments_modal_title">Fake Status Shipment(s)</h4>

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
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#search_form #search_tracking_no').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#search_form #riders').prepend('<option value="" selected="selected"></option>').select2({
                width: '200px',
                placeholder: 'Select Rider',
                allowClear:true
            });
            $('#search_form #hubs').prepend('<option value="" selected="selected"></option>').select2({
                width: '200px',
                placeholder: 'Select Hub',
                allowClear:true
            });

            var future_date = new Date();
            future_date.setDate(future_date.getDate()-7);
            $('#search_form #search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                min:future_date,
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    $('#search_date_to').pickadate('picker').clear({muted: true});
                    if (context.select) {
                        var selected_date = new Date(context.select);
                        $('#search_date_to').attr('disabled', false);
                        $('#search_form #search_date_to').pickadate('picker').set({'min':selected_date},{muted: true});
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
                        // $('#search_form #search_date_from').pickadate('picker').set('max', $('#search_form #search_date_to').pickadate('picker').get('select'));
                    }
                }
            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.fake_status.list') }}',
                        data: {
                            'page': 'all',
                            'rider': $('#riders').val(),
                            'hub': $('#hubs').val(),
                            'search_tracking_no': $('#search_tracking_no').val(),
                            'search_date_from': $('input[name="search_date_from_formatted"]').val(),
                            'search_date_to': $('input[name="search_date_to_formatted"]').val(),
                        },
                        success: function (result) {
                            head = [];

                            head.push('S. No');
                            head.push('Rider Name');
                            head.push('Rider City');
                            head.push('Delivery Note No.');
                            head.push('Delivery Note Created At');
                            head.push('Delivery Note Verified At');
                            head.push('Total Shipments');
                            head.push('Undelivered Shipments');
                            head.push('Shipments Marked With Fake Status');
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.rider_name);
                                row.push(values.rider_city);
                                row.push(values.delivery_note_id);
                                row.push(values.created_at);
                                row.push(values.verified_at);
                                row.push(values.total_shipments);
                                row.push(values.total_shipments - values.delivered_shipments);
                                row.push(values.shipment_fake_status);

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
                        title: 'Fake Status Report',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                    },

                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.fake_status.list') }}',
                    data: function (d) {
                        d.rider = $('#riders').val();
                        d.hub = $('#hubs').val();
                        d.search_tracking_no = $('#search_tracking_no').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                order: [[5, 'desc']],
                rowId: 'delivery_note_id',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'rider_name', name: 'r.name', class: 'align-middle rider_name'},
                    {data: 'rider_city', name: 'c.name', class: 'align-middle rider_city'},
                    {data: 'delivery_note_id', name: 'delivery_notes.id', class: 'align-middle delivery_note_id'},
                    {data: 'created_at', name: 'delivery_notes.created_at', class: 'align-middle text-center created_at'},
                    {data: 'verified_at', name: 'delivery_notes.status_verified_at', class: 'align-middle verified_at'},
                    {data: 'shipments_count_link', name: 'delivery_notes.shipments_count', class: 'align-middle shipments_count_link', orderable: false, searchable: false},
                    {data: 'undelivered_shipments_link', name: 'delivery_notes.delivered_shipments', class: 'align-middle undelivered_shipments_link', orderable: false, searchable: false},
                    {data: 'shipment_fake_status_link', name: 'cargo_consignments.chargeable_weight', class: 'align-middle shipment_fake_status_link', orderable: false, searchable: false}
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

        var route = '{!! route('admin.tracking.index') !!}';

        $('#datatable tbody').on('click','tr td.shipments_count_link button',function () {
            var id = parseInt($(this).parents('tr').attr('id'));
            $('#shipments_modal .modal-body').html('');
            $('#shipments_modal').modal('show');
            $.ajax({
                url: '{!! route('admin.reports.fake_status.shipments.total') !!}',
                method: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'delivery_note_id': id
                }
            })
                .done(function(data) {
                    if (data) {
                        var html = '';

                        if (data.shipments) {
                            $.each(data.shipments, function(index, tracking_number) {
                                html += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
                            });
                        }
                        $('#shipments_modal .modal-body').html(html);
                    }
                });

        });

        $('#datatable tbody').on('click','tr td.undelivered_shipments_link button',function () {
            var id = parseInt($(this).parents('tr').attr('id'));
            $('#undelivered_shipments_modal .modal-body').html('');
            $('#undelivered_shipments_modal').modal('show');
            $.ajax({
                url: '{!! route('admin.reports.fake_status.shipments.undelivered') !!}',
                method: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'delivery_note_id': id
                }
            })
                .done(function(data) {
                    if (data) {
                        var html = '';

                        if (data.shipments) {
                            $.each(data.shipments, function(index, tracking_number) {
                                html += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
                            });
                        }
                        $('#undelivered_shipments_modal .modal-body').html(html);
                    }
                });

        });

        $('#datatable tbody').on('click','tr td.shipment_fake_status_link button',function () {
            var id = parseInt($(this).parents('tr').attr('id'));
            $('#fake_status_shipments_modal .modal-body').html('');
            $('#fake_status_shipments_modal').modal('show');

            $.ajax({
                url: '{!! route('admin.reports.fake_status.fake_status_shipment') !!}',
                method: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'delivery_note_id': id
                }
            })
                .done(function(data) {
                    if (data) {
                        var html = '';

                        if (data.shipments) {
                            $.each(data.shipments, function(index, tracking_number) {
                                html += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
                            });
                        }
                        $('#fake_status_shipments_modal .modal-body').html(html);
                    }
                });

        });
        </script>
@endsection