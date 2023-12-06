@extends('admin.layout.master')

@section('title', 'Quality of Service Report')

@section('content')
    <h1 class="mb-1">
        Quality of Service Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div class="row mb-2 justify-content-center">

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
                            <select name="search_shippers[]" id="search_shippers" class="form-control select2" multiple="multiple" required data-rule-required="true" data-msg-required="This field is required">
                                @foreach($shippers as $shipper)
                                    <option value="{{$shipper->id}}">{{$shipper->name}}</option>
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
                            <select name="search_origin" id="search_origin" class="form-control select2">
                                @foreach($cities as $origin)
                                    <option value="{{$origin->id}}">{{$origin->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_destination" id="search_destination" class="form-control select2">
                                @foreach($cities as $destination)
                                    <option value="{{$destination->id}}">{{$destination->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_concerned_hub" id="search_concerned_hub" class="form-control select2">
                                @foreach($hubs as $concerned_hub)
                                    <option value="{{$concerned_hub->id}}">{{$concerned_hub->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>

                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_qsr" id="search_qsr" class="form-control select2">
                                <option value="1">Delivery</option>
                                <option value="2">Return</option>
                                <option value="3">All</option>
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_shipment_status" id="search_shipment_status" class="form-control select2">
                                @foreach($shipment_status as $status)
                                    <option value="{{$status->id}}">{{$status->name}}</option>
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
                        <fieldset class="form-group">
                            <select name="search_types" id="search_types" class="form-control select2">
                                @foreach($types as $id => $type)
                                    <option value="{{ $id }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <select name="sub_segment_select" id="sub_segment_select" class="select2">
                            @foreach($sub_segments as $sub_segment)
                                <option value="{{$sub_segment->id}}">{{$sub_segment->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-4 ">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="">Current Status From</span>
                            </span>
                            </div>
                            <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From" data-value="{{ Carbon\Carbon::now() }}">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="">Current Status To</span>
                            </span>
                            </div>
                            <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To" data-value="{{ Carbon\Carbon::now() }}">
                        </div>
                    </div>
                    {{--todo new--}}
                    <div class="col-4 ">
                        {{--                        <label>Arrival Date From</label>--}}
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="">Arrival Date From</span>
                            </span>
                            </div>
                            <input type="text" name="from_date1" class="form-control bg-primary border-primary white rounded-right" id="from_date1" placeholder="Arrival Date From">
                        </div>
                    </div>
                    <div class="col-4">
                        {{--                        <label>Arrival Date To</label>--}}
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="">Arrival Date To</span>
                            </span>
                            </div>
                            <input type="text" name="to_date1" class="form-control bg-primary border-primary white rounded-right" id="to_date1" placeholder="Arrival Date To">
                        </div>
                    </div>
                    {{--todo new end--}}

                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Order ID</th>
                        <th class="border-primary border-darken-1">Account No.</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Sub Segment</th>
                        <th class="border-primary border-darken-1">Consignee Name</th>
                        <th class="border-primary border-darken-1">First Attempt Date</th>
                        <th class="border-primary border-darken-1">Rider Picked Status Date</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Reason</th>
                        <th class="border-primary border-darken-1">Remarks</th>
                        <th class="border-primary border-darken-1">Total Attempt</th>
                        <th class="border-primary border-darken-1">History Status</th>
                        <th class="border-primary border-darken-1">Cargo Status</th>
                        <th class="border-primary border-darken-1">Bag Seal Number</th>
                        <th class="border-primary border-darken-1">Bag Status</th>
                        <th class="border-primary border-darken-1">Service</th>
                        <th class="border-primary border-darken-1">Arrival Date</th>
                        <th class="border-primary border-darken-1">Last Status Date</th>
                        <th class="border-primary border-darken-1">Booked Status Date</th>
                        <th class="border-primary border-darken-1">Shipping Mode</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Concerned Hub</th>
                        <th class="border-primary border-darken-1">Return City</th>
                        <th class="border-primary border-darken-1">Zone</th>
                        <th class="border-primary border-darken-1">Product Type</th>
                        <th class="border-primary border-darken-1">Product Description</th>
                        <th class="border-primary border-darken-1">Collection Amount</th>
                        <th class="border-primary border-darken-1">Aging (Arrival)</th>
                        <th class="border-primary border-darken-1">Aging (Last Status)</th>
                        <th class="border-primary border-darken-1">Request #</th>
                        <th class="border-primary border-darken-1">Request Status</th>
                        <th class="border-primary border-darken-1">Case Nature</th>
                        <th class="border-primary border-darken-1">Case Nature Type</th>
                        <th class="border-primary border-darken-1">Adjusted amount</th>
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

    <script type="text/javascript">
        $(document).ready(function () {
            $('#search_shipment_status').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Status',
                width:'100%',
                allowClear:true
            });
            $('#search_shipper').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Shipper',
                width:'100%',
                allowClear:true
            });
            $('#search_shippers').select2({
                width:'100%',
                placeholder:"Select Multiple Shippers",
                allowClear:true,
            });
            $('#search_origin').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Origin City',
                width:'100%',
                allowClear:true
            });
            $('#search_concerned_hub').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Concerned Hub',
                width:'100%',
                allowClear:true
            });
            $('#search_destination').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Destination City',
                width:'100%',
                allowClear:true
            });
            $('#search_qsr').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select QSR',
                width:'100%',
                allowClear:true
            });
            $('#search_hub').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Hub',
                width:'100%',
                allowClear:true
            });
            $('#search_shippimg_modes').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Shipping Mode',
                width:'100%',
                allowClear:true
            });
            $('#search_types').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Type',
                width:'100%'
            });
            $('#sub_segment_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Sub Segment*'
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

            var booking_from_date = $('#from_date1').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function (context) {
                    if (context.select) {
                        $('#to_date1').pickadate('picker').set('min', $('#from_date1').pickadate('picker').get('select'));
                    }
                }
            });
            var booking_to_date = $('#to_date1').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function (context) {
                    if (context.select) {
                        $('#from_date1').pickadate('picker').set('max', $('#to_date1').pickadate('picker').get('select'));
                    }
                }
            });

            $('#from_date1').change(function() {
                var selectedOption = $(this).val();
                if (selectedOption != null)
                {
                    $('#from_date').val(null).trigger('change');
                    $('#to_date').val(null).trigger('change');
                    // console.log(selectedOption);
                }
            });
            $('#to_date1').change(function() {
                var selectedOption = $(this).val();
                if (selectedOption != null)
                {
                    $('#from_date').val(null).trigger('change');
                    $('#to_date').val(null).trigger('change');
                    // console.log(selectedOption);
                }
            });

            // $('#from_date').change(function() {
            //     console.log('jjj');
            //     var selectedOption = $(this).val();
            //     if (selectedOption != null)
            //     {
            //         $('#from_date1').val(null).trigger('change');
            //         $('#to_date1').val(null).trigger('change');
            //         // console.log(selectedOption);
            //     }
            // });
            // $('#to_date').change(function() {
            //     console.log('jjj');
            //     var selectedOption = $(this).val();
            //     if (selectedOption != null)
            //     {
            //         $('#from_date1').val(null).trigger('change');
            //         $('#to_date1').val(null).trigger('change');
            //         // console.log(selectedOption);
            //     }
            // });


            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    blockPagePermanently();
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.qsr.list') }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Tracking No.');
                            head.push('Order ID');
                            head.push('Account No.');
                            head.push('Shipper');
                            head.push('Sub Segment');
                            head.push('Consignee Name');
                            head.push('First Attempt Date');
                            head.push('Rider Picked Status Date');
                            head.push('Status');
                            head.push('Reason');
                            head.push('Remarks');
                            head.push('Total Attempt');
                            head.push('History Status');
                            head.push('Cargo Status');
                            head.push('Bag Seal Number');
                            head.push('Bag Status');
                            head.push('Service Type');
                            head.push('Arrival');
                            head.push('Last Status Date');
                            head.push('Booked Status Date');
                            head.push('Shipping Mode');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Hub');
                            head.push('Concerned Hub');
                            head.push('Return City');
                            head.push('Zone');
                            head.push('Product Type');
                            head.push('Product Description');
                            head.push('Amount');
                            head.push('Aging (Arrival)');
                            head.push('Aging (Last Status)');
                            head.push('Request #');
                            head.push('Request Status');
                            head.push('Case Nature');
                            head.push('Case Nature Type');
                            head.push('Adjusted amount');
                            $.each(result.data, function(index, values) {
                                row = [];
                                
                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.order_id);
                                row.push(values.account_no);
                                row.push(values.shipper);
                                row.push(values.sub_segment);
                                row.push(values.name);
                                row.push(values.first_attempt_date);
                                row.push(values.rider_picked_status_date);
                                row.push(values.status);
                                row.push(values.reason);
                                row.push(values.remarks);
                                row.push(values.total_attempt);
                                row.push(values.history_status);
                                row.push(values.cargo_status);
                                row.push(values.seal_number);
                                row.push(values.bag_status);
                                row.push(values.service_type);
                                row.push(values.arrival);
                                row.push(values.last_status_date);
                                row.push(values.created_at);
                                row.push(values.shipping_mode);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.hub);
                                row.push(values.current_hub);
                                row.push(values.return_city);
                                row.push(values.zone);
                                row.push(values.product_type);
                                row.push(values.description);
                                row.push(values.amount);
                                row.push(values.aging);
                                row.push(values.aging_last_status);
                                row.push(values.crm_id_padded);
                                row.push(values.crm_request_status);
                                row.push(values.crm_request_case_nature);
                                row.push(values.crm_request_case_nature_type);
                                row.push(values.adjusted_amount);

                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header: head};
                }
            } );

            var index_column = [];
            var flag = false;
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'QSR Report',
                        className: 'btn btn-primary',
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
                deferLoading: 0,
                ajax: {
                    url: '{{ route('admin.reports.qsr.list') }}',
                    method: 'POST',
                    headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    data: function (d) {
                        d.search_shipment_status = $('#search_shipment_status').val();
                        d.search_shipper = $('#search_shipper').val();
                        d.sub_segment = $('#sub_segment_select').val();
                        d.search_shippers = $('#search_shippers').val();
                        d.search_origin = $('#search_origin').val();
                        d.search_destination = $('#search_destination').val();
                        d.search_qsr = $('#search_qsr').val();
                        d.search_hub = $('#search_hub').val();
                        d.search_shipping_mode = $('#search_shippimg_modes').val();
                        d.search_from = $('input[name="from_date_formatted"]').val();
                        d.search_to = $('input[name="to_date_formatted"]').val();
                        d.arrival_search_from = $('input[name="from_date1_formatted"]').val();
                        d.arrival_search_to = $('input[name="to_date1_formatted"]').val();
                        // d.requested_from_date = $('#from_date1').val();
                        // d.requested_to_date = $('#to_date1').val();
                        d.search_types = $('#search_types').val();
                        d.search_concerned_hub = $('#search_concerned_hub').val();
                    }
                },
                rowId: 'shId',
                order: [[19, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number_link', name: 'shipments.tracking_number', class: 'align-middle tracking_number_link'},
                    {data: 'order_id', name: 'shipments.order_id', class: 'align-middle order_id'},
                    {data: 'account_no', name: 'u.id', class: 'align-middle account_no'},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'sub_segment', name: 'scs.name', class: 'align-middle sub_segment'},
                    {data: 'name', name: 'shipments.consignee_name', class: 'align-middle name'},
                    {data: 'first_attempt_date', name: 'first_attempt_date', class: 'align-middle first_attempt_date'},
                    {data: 'rider_picked_status_date', name: 'rider_picked_status_date', class: 'align-middle rider_picked_status_date'},
                    {data: 'status', name: 'ss.name', class: 'align-middle status'},
                    {data: 'reason', name: 'ssr.name', class: 'align-middle reason'},
                    {data: 'remarks', name: 'sjr.remarks', class: 'align-middle remarks'},
                    {data: 'total_attempt' ,name: 'total_attempt', class: 'align-middle total_attempt'},
                    {data: 'history_status', name: 'ss.name', class: 'align-middle history_status'},
                    {data: 'cargo_status', name: 'cargo_status.name', class: 'align-middle history_status'},
                    {data: 'seal_number', name: 'cmb.seal_number', class: 'align-middle history_status'},
                    {data: 'bag_status', name: 'bs.name', class: 'align-middle history_status'},
                    {data: 'service_type', name: 'bt.booking_type', class: 'align-middle service_type'},
                    {data: 'arrival', name: 'sj.created_at', class: 'align-middle arrival'},
                    {data: 'last_status_date', name: 'journey.created_at', class: 'align-middle last_status_date'},
                    {data: 'created_at', name: 'shipments.created_at', class: 'align-middle created_at'},
                    {data: 'shipping_mode', name: 'sm.mode', class: 'align-middle shipping_mode'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'hub', name: 'h.name', class: 'align-middle hub'},
                    {data: 'current_hub', name: 'cmbh.name', class: 'align-middle current_hub'},
                    {data: 'return_city', name: 'return_city', class: 'align-middle return_city'},
                    {data: 'zone', name: 'z.name', class: 'align-middle zone'},
                    {data: 'product_type', name: 'p.product_name', class: 'align-middle product_type'},
                    {data: 'description', name: 'si.description', class: 'align-middle description'},
                    {data: 'amount', name: 'shipments.amount', class: 'align-middle amount'},
                    {data: 'aging', name: 'aging', class: 'align-middle aging',orderable: false, searchable: false},
                    {data: 'aging_last_status', name: 'aging_last_status', class: 'align-middle aging',orderable: false, searchable: false},
                    {data: 'crm_id_padded_link', name: 'cr.id', class: 'align-middle crm_id_padded'},
                    {data: 'crm_request_status', name: 'aging_last_status', class: 'align-middle crm_request_status'},
                    {data: 'crm_request_case_nature', name: 'crcn.name', class: 'align-middle crm_request_case_nature'},
                    {data: 'crm_request_case_nature_type', name: 'crcnt.type', class: 'align-middle crm_request_case_nature_type'},
                    {data: 'adjusted_amount', name: 'adjustment.adjustment_amount', class: 'align-middle adjusted_amount'},
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