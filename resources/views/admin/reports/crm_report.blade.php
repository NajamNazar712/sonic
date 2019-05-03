@extends('admin.layout.master')

@section('title', 'CRM Report')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
    <h1 class="mb-1">
        CRM Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center">

                    <div class="col-4">
                        <fieldset class="form-group">
                            <input type="text" class="form-control" name="search_tracking_no" id="search_tracking_no" placeholder="Search Tracking Number">
                        </fieldset>
                    </div>

                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_shipper" id="search_shipper" class="form-control select2">
                                @foreach($shippers as $shipper)
                                    <option value="{{$shipper->id}}">{{$shipper->name}}</option>
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
                            <select name="search_hub" id="search_hub" class="form-control select2">
                                @foreach($hubs as $hub)
                                    <option value="{{$hub->id}}">{{$hub->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>

                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_case_nature" id="search_case_nature" class="form-control select2">
                                @foreach($case_natures as $case_nature)
                                    <option value="{{$case_nature->id}}">{{$case_nature->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>

                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_agent" id="search_agent" class="form-control select2">
                                @foreach($agents as $agent)
                                    <option value="{{$agent->id}}">{{$agent->name}}</option>
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
                            <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From">
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>
                            <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To">
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
                        <th class="border-primary border-darken-1">Request No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Case Nature</th>
                        <th class="border-primary border-darken-1">Case Nature Type</th>
                        <th class="border-primary border-darken-1">Description</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Channel</th>
                        <th class="border-primary border-darken-1">Agent</th>
                        <th class="border-primary border-darken-1">Launched By</th>
                        <th class="border-primary border-darken-1">Launched By User Type</th>
                        <th class="border-primary border-darken-1">Launched Date</th>
                        <th class="border-primary border-darken-1">Assigned Date</th>
                        <th class="border-primary border-darken-1">Valid/Invalid Date</th>
                        <th class="border-primary border-darken-1">Resolved Date</th>
                        <th class="border-primary border-darken-1">Closed Date</th>
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
            $('#search_tracking_no').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#search_destination').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Destination',
                width:'100%',
                allowClear:true
            });
            $('#search_hub').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Hub',
                width:'100%',
                allowClear:true
            });
            $('#search_agent').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Agent',
                width:'100%',
                allowClear:true
            });
            $('#search_case_nature').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Case Nature',
                width:'100%',
                allowClear:true
            });
            $('#search_shipper').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Shipper',
                width:'100%',
                allowClear:true
            });
            $('#from_date').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#to_date').pickadate('picker').set('min', $('#from_date').pickadate('picker').get('select'));
                    }
                }
            });
            $('#to_date').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#from_date').pickadate('picker').set('max', $('#to_date').pickadate('picker').get('select'));
                    }
                }
            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.crm.list') }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            'page': 'all',
                            'search_tracking_no': $('#search_tracking_no').val(),
                            'search_destination': $('#search_destination').val(),
                            'search_hub': $('#search_hub').val(),
                            'search_agent': $('#search_agent').val(),
                            'search_case_nature': $('#search_case_nature').val(),
                            'search_shipper': $('#search_shipper').val(),
                            'search_from': $('input[name="from_date_formatted"]').val(),
                            'search_to': $('input[name="to_date_formatted"]').val()
                        },
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Request No.');
                            head.push('Tracking No.');
                            head.push('Case Nature');
                            head.push('Case Nature Type');
                            head.push('Description');
                            head.push('Shipper');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Hub');
                            head.push('Channel');
                            head.push('Agent');
                            head.push('Launched By');
                            head.push('Launched By User Type');
                            head.push('Launched Date');
                            head.push('Assigned Date');
                            head.push('Valid/Invalid Date');
                            head.push('Resolved Date');
                            head.push('Closed Date');
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.request_number);
                                row.push(values.tracking_number);
                                row.push(values.case_nature);
                                row.push(values.case_nature_type);
                                row.push(values.description);
                                row.push(values.shipper_name);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.hub);
                                row.push(values.channel);
                                row.push(values.agent);
                                row.push(values.launched_by_name);
                                row.push(values.launched_by_type);
                                row.push(values.launched_date);
                                row.push(values.assigned_date);
                                row.push(values.valid_invalid_date);
                                row.push(values.resolved_date);
                                row.push(values.closed_date);


                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );            var index_column = 0;
            var table = $('#datatable').DataTable({
                scrollX: true, scrollY: '350px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'CRM Report',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.crm.list') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function (d) {
                        d.search_tracking_no = $('#search_tracking_no').val();
                        d.search_origin = $('#search_origin').val();
                        d.search_destination = $('#search_destination').val();
                        d.search_hub = $('#search_hub').val();
                        d.search_agent = $('#search_agent').val();
                        d.search_case_nature = $('#search_case_nature').val();
                        d.search_shipper = $('#search_shipper').val();
                        d.search_from = $('input[name="from_date_formatted"]').val();
                        d.search_to = $('input[name="to_date_formatted"]').val();
                    }
                },
                // rowId: 'shipment_id',
                order: [[14, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'request_number', name: 'crm_requests.id', class: 'align-middle request_number'},
                    {data: 'tracking_number_link', name: 's.tracking_number', class: 'align-middle tracking_number_link'},
                    {data: 'case_nature', name: 'crcn.name', class: 'align-middle case_nature'},
                    {data: 'case_nature_type', name: 'crcnt.type', class: 'align-middle case_nature_type'},
                    {data: 'description', name: 'crm_requests.description', class: 'align-middle description'},
                    {data: 'shipper_name', name: 'u.name', class: 'align-middle shipper_name'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'hub', name: 'h.name', class: 'align-middle hub'},
                    {data: 'channel', name: 'crc.id', class: 'align-middle channel'},
                    {data: 'agent', name: 'a.name', class: 'align-middle agent'},
                    {data: 'launched_by_name', name: 'launched_by_name', class: 'align-middle launched_by_name'},
                    {data: 'launched_by_type', name: 'crm_requests.launched_by', class: 'align-middle launched_by_type'},
                    {data: 'launched_date', name: 'crm_requests.created_at', class: 'align-middle launched_date'},
                    {data: 'assigned_date', name: 'crah.created_at', class: 'align-middle assigned_date'},
                    {data: 'valid_invalid_date', name: 'crsh.created_at', class: 'align-middle valid_invalid_date'},
                    {data: 'resolved_date', name: 'crshr.created_at', class: 'align-middle resolved_date'},
                    {data: 'closed_date', name: 'crshc.created_at', class: 'align-middle closed_date'}

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