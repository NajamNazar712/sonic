@extends('admin.layout.master')

@section('title', 'Lead Time Report')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
    <h1 class="mb-1">
        Lead Time Report
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
                            <select name="search_hub" id="search_hub" class="form-control select2">
                                @foreach($hubs as $hub)
                                    <option value="{{$hub->id}}">{{$hub->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_status" id="search_status" class="form-control select2">
                                @foreach($statuses as $status)
                                    <option value="{{$status->id}}">{{$status->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_shipper" id="search_shipper" class="form-control select2">
                                @foreach($shipper as $shippers)
                                    <option value="{{$shippers->id}}">{{$shippers->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_shipping_mode" id="search_shipping_mode" class="form-control select2">
                                @foreach($shipping_modes as $mode)
                                    <option value="{{$mode->id}}">{{$mode->mode}}</option>
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
                            <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From" data-value="{{Carbon\Carbon::now()->subDays(10)}}">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>
                            <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To" data-value="{{ Carbon\Carbon::today() }}">
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
                        <th class="border-primary border-darken-1">Pickup Note</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Address</th>
                        <th class="border-primary border-darken-1">Arrive at Origin</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Booking Date</th>
                        <th class="border-primary border-darken-1">Pickup Date</th>
                        <th class="border-primary border-darken-1">Arrival Date</th>
                        <th class="border-primary border-darken-1">Rider</th>
                        <th class="border-primary border-darken-1">Status</th>
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
            $('#search_shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Shipping Mode',
                width:'100%',
                allowClear:true
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
            $('#search_hub').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Hub',
                width:'100%',
                allowClear:true
            });
            // $('#search_shipper').prepend('<option value="" selected="selected"></option>').select2({
            //     placeholder:'Search Shipper',
            //     width:'100%',
            //     allowClear:true
            // });
            $('#search_status').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Status',
                width:'100%',
                allowClear:true
            });
            $('#search_shipper').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Shipper',
                width:'100%',
                allowClear:true
            });
            var from_max = '{{ Carbon\Carbon::now() }}';
            var to_max = '{{ Carbon\Carbon::now() }}';
            var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: from_max,
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
                    to_date.pickadate('picker').set('min', new Date(old_date_formatted),{muted:true});
                }
            });
            var to_date = $('#to_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: to_max,
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#to_date_root').css('top', '40px');
                },
                onSet: function(context) {
                    var current_date_formatted = $('input[name="to_date_formatted"]').val();
                    from_date.pickadate('picker').set('max',new Date(current_date_formatted),{muted:true});
                }
            });
            /*jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    blockPagePermanently();
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.lead_time.list') }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Tracking .No');
                            head.push('Account No.');
                            head.push('Cargo Number');
                            head.push('In Transit Date');
                            head.push('Vendor Name');
                            head.push('Shipper');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Hub');
                            head.push('Shipping Mode');
                            head.push('Current Status');
                            head.push('Payment Status');
                            head.push('Arrival Date(A)');
                            head.push('Reached At Destination Date(B)');
                            head.push('Transit TAT(A-B)');
                            head.push('junction Date');
                            head.push('First Delivery Note No');
                            head.push('First Attempt Date');
                            head.push('First Status');
                            head.push('First Status Date(C)');
                            head.push('First Verification Status');
                            head.push('First Verification Date');
                            head.push('Last Delivery Note No');
                            head.push('Last Status');
                            head.push('Last Status Date(C)');
                            head.push('Last Verification Status');
                            head.push('Last Verification Date');
                            head.push('Attempt TAT(A-C)');
                            head.push('Dispatch TAT(B-C)');
                            head.push('Delivered Date(D)');
                            head.push('Delivered TAT(A-D)');
                            head.push('Return Confirm(E)');
                            head.push('Return Cargo Number');
                            head.push('Return In Transit Date');
                            head.push('Reached At Origin(F)');
                            head.push('Return Transit TAT(E-F)');
                            head.push('Return Status');
                            head.push('Return Status Date(G)');
                            head.push('Return Dispatch TAT(F-G)');
                            head.push('Return TAT(E-G)');
                            head.push('Payment Done Date(H)');
                            head.push('Payment TAT(D-H,G-H)');
                            head.push('Shipment TAT');
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.account_no);
                                row.push(values.cargo_number);
                                row.push(values.cargo_date_time);
                                row.push(values.vendor);
                                row.push(values.shipper);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.hub);
                                row.push(values.shipping_mode);
                                row.push(values.current_status);
                                row.push(values.payment_status);
                                row.push(values.arrival_date);
                                row.push(values.reached_at_destination);
                                row.push(values.transit_tat);
                                row.push(values.junction);
                                row.push(values.first_delivery_note_id);
                                row.push(values.first_attempt);
                                row.push(values.first_status);
                                row.push(values.first_status_date);
                                row.push(values.first_verification);
                                row.push(values.verification_status_date);
                                row.push(values.last_delivery_note_id);
                                row.push(values.last_status);
                                row.push(values.last_status_date);
                                row.push(values.last_verification);
                                row.push(values.last_verification_status_date);
                                row.push(values.attempt_tat);
                                row.push(values.dispatch_tat);
                                row.push(values.delivered_date);
                                row.push(values.delivered_tat);
                                row.push(values.return_confirm);
                                row.push(values.return_cargo_number);
                                row.push(values.return_cargo_date_time);
                                row.push(values.return_reached_at_destination);
                                row.push(values.return_transit_tat);
                                row.push(values.return_delivered_status);
                                row.push(values.return_delivered_date);
                                row.push(values.return_dispatch_tat);
                                row.push(values.return_tat);
                                row.push(values.payment_done_date);
                                row.push(values.payment_tat);
                                row.push(values.total_tat);


                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header: head};
                }
            } ); */
              var index_column = 0;
            var table = $('#datatable').DataTable({
                scrollX: true, scrollY: '500px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Lead Time Report',
                        text: '<i class="la la-file-excel-o"></i> Excel',
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
                ajax: {
                    url: '{{ route('admin.reports.pickup_history_cn_wise.list') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function (d) {
                        d.search_tracking_no = $('#search_tracking_no').val();
                        d.search_origin = $('#search_origin').val();
                        d.search_destination = $('#search_destination').val();
                        d.search_hub = $('#search_hub').val();
                        d.search_status = $('#search_status').val();
                        d.search_shipper = $('#search_shipper').val();
                        d.search_shipping_mode = $('#search_shipping_mode').val();
                        d.search_from = $('input[name="from_date_formatted"]').val();
                        d.search_to = $('input[name="to_date_formatted"]').val();
                    }
                },
                rowId: 'shipment_id',
                order: [[0, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number_link', name: 'shipments.tracking_number', class: 'align-middle tracking_number_link',searchable: false},
                    {data: 'pickup_note_no', name: 'vpn.pickup_note_no', class: 'align-middle pickup_note_no',searchable: false},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper',searchable: false},
                    {data: 'pickup_address', name: 'usi.pickup_address', class: 'align-middle pickup_address',searchable: false},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin',searchable: false},
                    {data: 'hub', name: 'h.name', class: 'align-middle hub',searchable: false},
                    {data: 'booking_date', name: 'shipments.created_at', class: 'align-middle booking_date',searchable: false},
                    {data: 'pickup_date', name: 'vrp.created_at', class: 'align-middle pickup_date',searchable: false},
                    {data: 'arrival_date', name: 'sj.created_at', class: 'align-middle arrival_date',searchable: false},
                    {data: 'rider', name: 'cr.name', class: 'align-middle rider',searchable: false},
                    {data: 'arrival_status_badge', name: '', class: 'align-middle arrival_status_badge',searchable: false,orderable: false},

                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });
            table.draw();
            /*$('#search_filter_btn').on('click',function () {
            });*/

        });

    </script>
@endsection