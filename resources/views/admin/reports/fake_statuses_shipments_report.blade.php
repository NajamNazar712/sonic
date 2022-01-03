@extends('admin.layout.master')

@section('title', 'Fake Statuses Shipments Report')

@section('content')
    <h1 class="mb-1">
        Fake Statuses Shipments Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center">
                    <div class="col-4">
                        <input type="text" class="form-control" name="search_tracking_no" id="search_tracking_no" placeholder="Tracking Number">
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="riders" class="select2" id="riders">
                                @foreach($riders as $rider)
                                    <option value="{{ $rider->id }}">{{ $rider->name }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="hubs" class="select2" id="hubs">
                                @foreach($hubs as $hub)
                                    <option value="{{ $hub->id }}">{{ $hub->name }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="destinations" class="select2" id="destinations">
                                @foreach($destinations as $destination)
                                    <option value="{{ $destination->id }}">{{ $destination->name }}</option>
                                @endforeach
                            </select>
                        </fieldset>
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
                        <fieldset class="form-group">
                            <select name="search_zone" id="search_zone" class="form-control select2">
                                @foreach($zones as $zone)
                                    <option value="{{$zone->id}}">{{$zone->name}}</option>
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
                            <input type="text" name="search_date_from" class="form-control bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Search Date (From)" data-value="">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>
                            <input type="text" name="search_date_to" class="form-control bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Search Date (To)" data-value="">
                        </div>
                    </div>
                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>

                {{--</div>--}}
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking Number</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Fake Status Date/Time</th>
                        <th class="border-primary border-darken-1">Rider Name</th>
                        <th class="border-primary border-darken-1">Remarks</th>
                        <th class="border-primary border-darken-1">Raised By</th>
                        <th class="border-primary border-darken-1">Remarks By Debriefer</th>
                        <th class="border-primary border-darken-1">Raised By Debriefer</th>
                        <th class="border-primary border-darken-1">Department</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
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
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#search_tracking_no').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#search_shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Shipping Mode',
                width:'100%',
                allowClear:true
            });
            $('#riders').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Rider',
                allowClear:true
            });
            $('#hubs').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Hub',
                allowClear:true
            });
            $('#destinations').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Destination',
                allowClear:true
            });
            $('#search_zone').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Zone',
                width:'100%',
                allowClear:true
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
                        // $('#search_form #search_date_from').pickadate('picker').set('max', $('#search_form #search_date_to').pickadate('picker').get('select'));
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
                        // $('#search_form #search_date_from').pickadate('picker').set('max', $('#search_form #search_date_to').pickadate('picker').get('select'));
                    }
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
                        url: '{{ route('admin.reports.fake_status_shipments.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S. No');
                            head.push('Tracking Number');
                            head.push('Shipper Name');
                            head.push('Destination');
                            head.push('Hub');
                            head.push('Fake Reason Date/Time');
                            head.push('Rider Name');
                            head.push('Remarks');
                            head.push('Raised By');
                            head.push('Remarks By Debriefer');
                            head.push('Raised By Debriefer');
                            head.push('Department');
                            
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.shipper);
                                row.push(values.destination);
                                row.push(values.hub);
                                row.push(values.updated_at);
                                row.push(values.rider_name);
                                row.push(values.remarks);
                                row.push(values.raised_by);
                                row.push(values.debrifer_remark);
                                row.push(values.debrifer_name);
                                row.push(values.department);

                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header: head};
                }
            } );
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Fake Status Shipments Report',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                    },

                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                autoWidth: false,
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.fake_status_shipments.list') }}',
                    data: function (d) {
                        d.rider = $('#riders').val();
                        d.hub = $('#hubs').val();
                        d.destination = $('#destinations').val();
                        d.search_tracking_no = $('#search_tracking_no').val();
                        d.search_shipping_mode = $('#search_shipping_mode').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                        d.zone = $('#search_zone').val();
                    }
                },
                order: [[5, 'desc']],
                // rowId: 'delivery_note_id',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number_link', name: 's.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'hub', name: 'h.name', class: 'align-middle hub'},
                    {data: 'updated_at', name: 'delivery_note_shipments.fake_status_updated_at', class: 'align-middle text-center updated_at'},
                    {data: 'rider_name', name: 'r.name', class: 'align-middle rider_name'},
                    {data: 'remarks', name: 'delivery_note_shipments.remarks', class: 'align-middle remarks'},
                    {data: 'raised_by', name: 'admin.name', class: 'align-middle raised_by'},
                    {data: 'debrifer_remark', name: 'sj.remarks', class: 'align-middle debrifer_remark'},
                    {data: 'debrifer_name', name: 'sj.remarks', class: 'align-middle debrifer_name'},
                    {data: 'department', name: 'ad.name', class: 'align-middle department'},
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