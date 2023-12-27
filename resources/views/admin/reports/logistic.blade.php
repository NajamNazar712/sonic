@extends('admin.layout.master')

@section('title', 'Logistic Report')

@section('content')
    <h1 class="mb-1">
        Logistic Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div id="search_form" class="row mb-2 justify-content-center">
                    <div class="col-4">
                        <fieldset class="form-group">
                            <input type="text" class="form-control" name="search_tracking_no" id="search_tracking_no" placeholder="Search Tracking Number">
                        </fieldset>
                    </div>
                    @if(!empty($shippers))
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_shipper[]" id="search_shipper" class="form-control select2" multiple>
                                @foreach($shippers as $shipper)
                                    <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    @endif

                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_destination" id="search_destination" class="form-control select2">
                                @foreach($cities as $destination)
                                    <option value="{{$destination->id}}">{{$destination->name}}</option>
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
                            <select name="search_status" id="search_status" class="form-control select2">
                                @foreach($statuses as $status)
                                    <option value="{{$status->id}}">{{$status->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>

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
                 {{--   <div class="col-3">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                  <span class="">Arrival Time From</span>
                              </span>
                            </div>
                            <input type="text" name="arrival_time_from" class="form-control bg-primary border-primary white rounded-right pickatime arrival_time_from" value="12:00 AM" id="arrival_time_from" placeholder="From">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                  <span class="">Arrival Time To</span>
                              </span>
                            </div>
                            <input type="text" name="arrival_time_to" class="form-control bg-primary border-primary white rounded-right pickatime arrival_time_to" value="11:30 PM" id="arrival_time_to" placeholder="To">
                        </div>
                    </div>--}}
                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Booking Date</th>
                        <th class="border-primary border-darken-1">Pickup Address</th>
                        <th class="border-primary border-darken-1">Description</th>
                        <th class="border-primary border-darken-1">Order ID</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Consignee Name</th>
                        <th class="border-primary border-darken-1">Consignee Address</th>
                        <th class="border-primary border-darken-1">Consignee Phone</th>
                        <th class="border-primary border-darken-1">Quantity</th>
                        <th class="border-primary border-darken-1">Pieces</th>
                        <th class="border-primary border-darken-1">Weight</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Aging (Days)</th>
                        <th class="border-primary border-darken-1">Delivered/Returned Date</th>
                        <th class="border-primary border-darken-1">Shipment Status</th>
                       
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
            $('#search_tracking_no').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            $('#search_shipper').select2({
                placeholder:'Select Shipper',
                width:'100%'
            });

            $('#search_destination').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Destination City',
                width:'100%',
                allowClear:true
            });
            $('#search_hub').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Hub',
                width:'100%',
                allowClear:true
            });
            $('#search_status').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Status',
                width:'100%',
                allowClear:true
            });

          /*  $('.arrival_time_from').pickatime({
                clear: '',
                format: 'h:i A',
                interval: 30,
                onSet: function(context) {
                    if($('input[name="search_date_from_formatted"]').val()==$('input[name="search_date_to_formatted"]').val())
                    {
                        if (context.select) {
                            $('#arrival_time_to').pickatime('picker').set('min', $('#arrival_time_from').pickatime('picker').get('select'));
                        }
                    }
                    else{
                        if (context.select) {
                            $('#arrival_time_to').pickatime('picker').set('min', '');
                        }
                    }
                }

            });
            $('.arrival_time_to').pickatime({
                clear: '',
                format: 'h:i A',
                interval: 30,
                onSet: function(context) {
                    if($('input[name="search_date_from_formatted"]').val()==$('input[name="search_date_to_formatted"]').val())
                    {
                        if (context.select) {
                            $('#arrival_time_from').pickatime('picker').set('max', $('#arrival_time_to').pickatime('picker').get('select'));
                        }
                    }
                    else{
                        if (context.select) {
                            $('#arrival_time_from').pickatime('picker').set('max', '');
                        }
                    }
                }
            });*/
            var submission_date = $('#submission_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#submission_date_root').css('top','40px');
                },
                onSet: function(context) {
                }
            });
            var from_date = $('#search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_date_to').pickadate('picker').set('min', $('#search_date_from').pickadate('picker').get('select'));
                        // $('#arrival_time_from').pickatime('picker').clear();
                        // $('#arrival_time_to').pickatime('picker').clear();
                        $('input[name="arrival_time_from"]').val('12:00 AM');
                        $('input[name="arrival_time_to"]').val('11:30 PM');
                    }
                }
            });

            var to_date = $('#search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_date_from').pickadate('picker').set('max', $('#search_date_to').pickadate('picker').get('select'));
                        // $('#arrival_time_from').pickatime('picker').clear();
                        // $('#arrival_time_to').pickatime('picker').clear();
                        $('input[name="arrival_time_from"]').val('12:00 AM');
                        $('input[name="arrival_time_to"]').val('11:30 PM');
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
                        url: '{{ route('admin.reports.logistic.list') }}',
                        method:'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S. No.');
                            head.push('Booking Date');
                            head.push('Pickup Address');
                            head.push('Description');
                            head.push('Order ID');
                            head.push('Tracking No.');
                            head.push('Shipper');
                            head.push('Consignee Name');
                            head.push('Consignee Address');
                            head.push('Consignee Phone');
                            head.push('Quantity');
                            head.push('Pieces');
                            head.push('Weight');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Aging (Days)');
                            head.push('Delivered/Returned Date');
                            head.push('Shipment Status');
                            
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.booking_date);
                                row.push(values.shipper_address);
                                row.push(values.description);
                                row.push(values.order_id);
                                row.push(values.tracking_number);
                                row.push(values.shipper);
                                row.push(values.consignee_name);
                                row.push(values.consignee_address);
                                row.push(values.consignee_phone_excel);
                                row.push(values.quantity);
                                row.push(values.pieces);
                                row.push(values.estimated_weight);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.aging);
                                row.push(values.delivered_or_returned);
                                row.push(values.current_status);
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
                        title: 'Logistic Report',
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
                    url: '{{ route('admin.reports.logistic.list') }}',
                    method:'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function (d) {
                        d.search_tracking = $('#search_tracking_no').val();
                        d.search_shipper = $('#search_shipper').val();
                        d.search_destination = $('#search_destination').val();
                        d.search_hub = $('#search_hub').val();
                        d.search_status = $('#search_status').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                        d.arrival_time_from= $('input[name="arrival_time_from"]').val();
                        d.arrival_time_to= $('input[name="arrival_time_to"]').val();
                    }
                },
                order: [[1, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'booking_date' ,name: 'shipments.created_at', class: 'align-middle booking_date'},
                    { data:'shipper_address' ,name:'shipments.shipper_address', class: 'align-middle shipper_address'},
                    { data:'description' ,name:'sj.description', class: 'align-middle description'},
                    { data:'order_id' ,name: 'shipments.order_id', class: 'align-middle order_id'},
                    { data:'tracking_number_link' ,name: 'shipments.tracking_number', class: 'align-middle text-center tracking_number_link'},
                    { data:'shipper' ,name: 'u.name', class: 'align-middle shipper'},
                    { data:'consignee_name' ,name:'shipments.consignee_name', class: 'align-middle consignee_name'},
                    { data:'consignee_address' ,name:'shipments.consignee_address', class: 'align-middle consignee_address'},
                    { data:'consignee_phone' ,name:'consignee_phone', class: 'align-middle consignee_phone'},
                    { data:'quantity' ,name: 'si.quantity', class: 'align-middle quantity'},
                    { data:'pieces' ,name: 'shipments.pieces', class: 'align-middle pieces'},
                    { data:'estimated_weight' ,name: 'shipments.estimated_weight', class: 'align-middle estimated_weight'},
                    { data:'origin' ,name: 'oc.name', class: 'align-middle origin'}, //
                    { data:'destination' ,name: 'dc.name', class: 'align-middle destination'},
                    { data: 'aging' ,name: 'aging', class: 'align-middle aging', orderable: false, searchable: false},
                    { data: 'delivered_or_returned' ,name: 'dr.created_at', class: 'align-middle delivered_or_returned'},
                    { data:'current_status' ,name: 'ss.name', class: 'align-middle current_status'},
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