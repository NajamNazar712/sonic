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

{{--                    <div class="col-4">--}}
{{--                        <fieldset class="form-group">--}}
{{--                            <input type="text" class="form-control" name="search_tracking_no" id="search_tracking_no" placeholder="Search Tracking Number(s)">--}}
{{--                        </fieldset>--}}
{{--                    </div>--}}
                    <div class="col-4">
                        <fieldset class="form-group">
                            <input type="text" class="form-control" name="search_request_number" id="search_request_number" placeholder="Search Ticket Number(s)">
                        </fieldset>
                    </div>

                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_shipper" id="search_shipper" class="form-control select2" multiple="multiple">
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
                            <select name="search_zone" id="search_zone" class="form-control select2">
                                @foreach($zones as $zone)
                                    <option value="{{$zone->id}}">{{$zone->name}}</option>
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
                            <select name="search_case_nature_type" id="search_case_nature_type" class="form-control select2">
                                @foreach($case_nature_types as $case_nature_type)
                                    <option value="{{$case_nature_type->id}}">{{$case_nature_type->type}}</option>
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
                        <fieldset class="form-group">
                            <select name="search_status" id="search_status" class="form-control select2" multiple="multiple">
                                @foreach($statuses as $status)
                                    <option value="{{$status->id}}">{{$status->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>

{{--                    <div class="col-4">--}}
{{--                        <fieldset class="form-group">--}}
{{--                            <select name="search_shipping_mode" id="search_shipping_mode" class="form-control select2">--}}
{{--                                @foreach($shipping_modes as $shipping_mode)--}}
{{--                                    <option value="{{$shipping_mode->id}}">{{$shipping_mode->mode}}</option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                        </fieldset>--}}
{{--                    </div>--}}
{{--                    <div class="col-4">--}}
{{--                        <select name="service_type_select" id="service_type_select" class="select2">--}}
{{--                            @foreach($service_types as $service_type)--}}
{{--                                <option value="{{$service_type->id}}">{{$service_type->booking_type}}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}
{{--                    </div>--}}


                    <div class="col-4">
                        {{-- <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>
                            <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From">
                        </div> --}}
                    </div>


                    <div class="col-4">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>
                            <input type="text" name="from_date"
                                   class="form-control bg-primary border-primary white rounded-right"
                                   id="from_date" placeholder="Date From" data-value="{{ Carbon\Carbon::today() }}">
                        </div>

                    </div>

                    <div class="col-4">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>
                            <input type="text" name="to_date"
                                   class="form-control bg-primary border-primary white rounded-right"
                                   id="to_date" placeholder="Date To" data-value="{{ Carbon\Carbon::today() }}">
                        </div>
                    </div>

                    <div class="col-2">
                        <button type="button" id="search_filter_btn"
                                class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i>
                            Search
                        </button>
                    </div>
                    
                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Ticket No.</th>
                        <th class="border-primary border-darken-1">Ticket Status</th>
                        <th class="border-primary border-darken-1">Valid/Invalid</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Zone</th>
                        <th class="border-primary border-darken-1">Arrival Date</th>

                        <!-- Placeholder for Arrival to Today (TAT) -->
                        <th class="border-primary border-darken-1">Arrival to Today (TAT)</th>

                        <th class="border-primary border-darken-1">Shipment Status</th>
                        <th class="border-primary border-darken-1">Last Status Date</th>
                        <th class="border-primary border-darken-1">Case Nature</th>
                        <th class="border-primary border-darken-1">Case Nature Type</th>
                        <th class="border-primary border-darken-1">Description</th>
                        <th class="border-primary border-darken-1">Launched Date</th>

                        <th class="border-primary border-darken-1">Responsible Hub</th>

                        <!-- Sub Hub column added -->
                        <th class="border-primary border-darken-1">Sub Hub</th>

                        <th class="border-primary border-darken-1">Responsible Zone</th>
                        <th class="border-primary border-darken-1">Agent</th>

                        <!-- Parcel Value column added -->
                        <th class="border-primary border-darken-1">Parcel Value</th>

                        <th class="border-primary border-darken-1">COD Value</th>
                        <th class="border-primary border-darken-1">Adjusted Amount</th>
                        <th class="border-primary border-darken-1">Weight Adjusted Amount</th>

                        <!-- Segment column added -->
                        <th class="border-primary border-darken-1">Segment</th>

                        <!-- Product Description column added -->
                        <th class="border-primary border-darken-1">Product Description</th>

                        <!-- Product Type column added -->
                        <th class="border-primary border-darken-1">Product Type</th>

                        <!-- Pieces column added -->
                        <th class="border-primary border-darken-1">Pieces</th>

                        <!-- Segment column added -->
                        <th class="border-primary border-darken-1">Quantity</th>

                        <!-- Quantity column added -->
                        <th class="border-primary border-darken-1">Quantity</th>

                        <!-- Key Account Category column added -->
                        <th class="border-primary border-darken-1">Key Account Category</th>

                        <th class="border-primary border-darken-1">Launched By</th>
                        <th class="border-primary border-darken-1">Channel</th>
                        <th class="border-primary border-darken-1">Launched By Type</th>
                        <th class="border-primary border-darken-1">Tagged To</th>

                        <!--Tagging (Manual or Auto) column added -->
                        <th class="border-primary border-darken-1">Tagging (Manual or Auto)</th>

                        <th class="border-primary border-darken-1">Closed Date</th>
                        <th class="border-primary border-darken-1">Resolved Date</th>
                        <th class="border-primary border-darken-1">Case Closed Remarks</th>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
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
        td.rating_code {
            font-size: 2em !important;
        }
    </style>
@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            var select = $('#search_tracking_no').selectize({
                    placeholder: 'Search Tracking Number(s)',
                    delimiter: ',',
                    createOnBlur: true,
                    persist: false,
                    plugins: ['remove_button'],
                    onDropdownOpen: function (dropdown) {
                        dropdown.remove();
                    },
                    onType: function (str) {
                        var regex = /^[0-9,]+$/;

                        if (!regex.test(str)) {
                            select[0].selectize.setTextboxValue('');
                        }
                    },
                    create: function (input) {
                        if (input.length >= 6 && Math.floor(input) == input && $.isNumeric(input)) {
                            return {
                                value: input,
                                text: input
                            }
                        }
                        else {
                            return false;
                        }
                    }
                });

            // $('#search_tracking_no').inputmask({
            //     'alias': 'integer',
            //     'allowMinus': false,
            //     'allowPlus': false
            // });

            var select = $('#search_request_number').selectize({
                placeholder: 'Search Ticket Number(s)*',
                delimiter: ',',
                createOnBlur: true,
                persist: false,
                plugins: ['remove_button'],
                onDropdownOpen: function (dropdown) {
                    dropdown.remove();
                },
                onType: function (str) {
                    var regex = /^[0-9,]+$/;

                    if (!regex.test(str)) {
                        select[0].selectize.setTextboxValue('');
                    }
                },
                create: function (input) {
                    if (Math.floor(input) == input && $.isNumeric(input)) {
                        return {
                            value: input,
                            text: input
                        }
                    }
                    else {
                        return false;
                    }
                }
            });

            $('#search_shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Shipping Mode',
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
            $('#search_zone').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Zone',
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
            $('#search_case_nature_type').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Case Nature Type',
                width:'100%',
                allowClear:true
            });
            
            $('#search_shipper').select2({
                placeholder:'Search Shipper',
                width:'100%',
                allowClear:true
            });
            $('#search_status').select2({
                placeholder:'Search CRM Status',
                width:'100%',
                allowClear:true
            });
            $('#service_type_select').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Service Type",
                allowClear:true,
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
                    var params = table.ajax.params();
                    if(params !== undefined){
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                }
                else{
                    params = {
                        'excel':true,
                    }
                }
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.crm.list') }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S. No');
                            head.push('Ticket No');
                            head.push('Ticket Status');
                            head.push('Valid/Invalid');
                            head.push('Tracking No.');
                            head.push('Shipper');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Hub');
                            head.push('Zone');
                            head.push('Arrival Date');

                            head.push('Arrival to Today (TAT)');  // Placeholder for Arrival to Today (TAT)

                            head.push('Shipment Status');
                            head.push('Last Status Date');
                            head.push('Case Nature');
                            head.push('Case Nature Type');
                            head.push('Description');
                            head.push('Launched Date');
                            head.push('Aging (From Launch Date To Today)');
                            head.push('Responsible Hub');

                            head.push('Sub Hub'); // Placeholder for Sub Hub

                            head.push('Responsible Zone');
                            head.push('Agent');
                            head.push('Parcel Value');
                            head.push('COD Value');
                            head.push('Adjusted Amount');
                            head.push('Weight Adjusted Amount');

                            head.push('Segment'); // Placeholder for Segment
                            head.push('Product Description'); // Placeholder for Product Description
                            head.push('Product Type'); // Placeholder for Product Type
                            head.push('Pieces');  // Placeholder for Pieces
                            head.push('Quantity'); // Placeholder for Quantity
                            head.push('Weight'); // Placeholder for Weight
                            head.push('Key Account Category'); // Placeholder for Key Account Category

                            head.push('Launched By');
                            head.push('Launched By Type');
                            head.push('Tagged To');

                            head.push('Tagging (Manual or Auto)'); // Placeholder for future addition

                            head.push('Closed Date');
                            head.push('Resolved Date');
                            head.push('Case Closed Remarks');

                            
                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.id_padded_link);
                                row.push(values.request_status);
                                row.push(values.valid_invalid_status);
                                row.push(values.tracking_number);
                                row.push(values.shipper_name);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.hub);
                                row.push(values.zone);
                                row.push(values.arrival_date);
                                row.push(values.arrival_today);  // Placeholder for Arrival to Today (TAT)
                                row.push(values.shipment_status);
                                row.push(values.last_status_date);
                                row.push(values.case_nature);
                                row.push(values.case_nature_type);
                                row.push(values.description);
                                row.push(values.launched_date);
                                row.push(values.aging_launch_today);
                                row.push(values.responsible_hub);

                                row.push(values.sub_hub);  // Placeholder for Sub Hub

                                row.push(values.responsible_zone);
                                row.push(values.agent);

                                row.push(values.parcel_value);  // Placeholder for Parcel Value

                                row.push(values.cod_amount);
                                row.push(values.adjusted_amount);
                                row.push(values.weight_charges);

                                row.push(values.segment); // Placeholder for Segment
                                row.push(values.shipment_description); // Placeholder for Product Description Of Shipment
                                row.push(values.product_name); // Placeholder for Product Type
                                row.push(values.pieces); // Placeholder for Pieces
                                row.push(values.shipment_quantity); // Placeholder for Quantity
                                row.push(values.actual_weight); // Placeholder for Weight
                                row.push(values.shipper_category); // Placeholder for Key Account Category

                                row.push(values.launched_by_name);
                                row.push(values.launched_by_type);
                                row.push(values.tagged_to);

                                row.push(values.tagged_manual_auto); // Placeholder for Tagging (Manual or Auto)

                                row.push(values.closed_date);
                                row.push(values.resolved_date);
                                row.push(values.case_closed_remark);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );            var index_column = 0;
            var table = $('#datatable').DataTable({
                scrollX: true, scrollY: '500px',
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
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                deferLoading: 0,

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
                        d.search_zone = $('#search_zone').val();
                        d.search_agent = $('#search_agent').val();
                        d.search_case_nature = $('#search_case_nature').val();
                        d.search_case_nature_type = $('#search_case_nature_type').val();
                        d.search_shipping_mode = $('#search_shipping_mode').val();
                        d.search_shipper = $('#search_shipper').val();
                        d.search_status = $('#search_status').val();
                        d.search_from = $('input[name="from_date_formatted"]').val();
                        d.search_to = $('input[name="to_date_formatted"]').val();
                        d.search_request_number = $('#search_request_number').val();
                        d.service_type_select = $('#service_type_select').val();
                    }
                },
                // rowId: 'shipment_id',
                order: [[27, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'id_padded_link', name: 'crm_requests.id', class: 'align-middle request_number'},
                    {data: 'request_status', name: 'crs.name', class: 'align-middle request_status'},

                    {data: 'valid_invalid_status', name: 'crm_requests.status', class: 'align-middle valid_invalid_status', orderable: false, searchable: false},
                    {data: 'tracking_number_link', name: 's.tracking_number', class: 'align-middle tracking_number_link'},
                    {data: 'shipper_name', name: 'u.name', class: 'align-middle shipper_name'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'hub', name: 'h.name', class: 'align-middle hub'},
                    {data: 'zone', name: 'z.name', class: 'align-middle zone'},
                    {data: 'arrival_date', name: 'sj.created_at', class: 'align-middle arrival_date'},

                    // Placeholder for Arrival to Today (TAT)
                    {data: 'arrival_today', name: 'arrival_today', class: 'align-middle arrival_today'},


                    {data: 'status', name: 'ss.name', class: 'align-middle status'},
                    {data: 'last_status_date', name: 'ss.created_at', class: 'align-middle last_status_date'},
                    {data: 'case_nature', name: 'crcn.name', class: 'align-middle case_nature'},
                    {data: 'case_nature_type', name: 'crcnt.type', class: 'align-middle case_nature_type'},
                    {data: 'description', name: 'crm_requests.description', class: 'align-middle description'},
                    {data: 'launched_date', name: 'crm_requests.created_at', class: 'align-middle launched_date'},

                    {data: 'responsible_hub', name: 'h.name', class: 'align-middle responsible_hub'},

                    // Placeholder for Sub Hub
                    {data: 'sub_hub', name: 'ca.name', class: 'align-middle sub_hub'},

                    {data: 'responsible_zone', name: 'z.name', class: 'align-middle responsible_zone'},
                    {data: 'agent', name: 'a.name', class: 'align-middle agent'},

                    // Parcel Value column added here
                    {data: 'parcel_value', name: 's.parcel_value', class: 'align-middle parcel_value'},

                    {data: 'cod_amount', name: 's.amount', class: 'align-middle cod_amount'},
                    {data: 'adjusted_amount', name: 'sj.adjusted_amount', class: 'align-middle adjusted_amount'},
                    {data: 'weight_charges', name: 'change_shipment_weight_logs.new_charges', class: 'align-middle weight_charges'},

                    // Segment column added here
                    {data: 'segment', name: 'seg.name', class: 'align-middle segment'},

                    // Placeholder for Product Description
                    {data: 'shipment_description', name: 'si.description', class: 'align-middle shipment_description'},

                    // Placeholder for Product Type
                    {data: 'product_name', name: 'prod.product_name', class: 'align-middle product_name'},

                    // Placeholder for pieces
                    {data: 'pieces', name: 's.pieces', class: 'align-middle pieces'},

                    // Placeholder for Quantity
                    {data: 'shipment_quantity', name: 'si.quantity', class: 'align-middle shipment_quantity'},

                    // Placeholder for Weight
                    {data: 'actual_weight', name: 's.actual_weight', class: 'align-middle actual_weight'},

                    // Placeholder for Key Account Category
                    {data: 'shipper_category', name: 'shipper_category', class: 'align-middle shipper_category'},

                    {data: 'launched_by_name', name: 'launched_by_name', class: 'align-middle launched_by_name'},
                    {data: 'channel', name: 'crm_requests.channel', class: 'align-middle channel'},
                    {data: 'launched_by_type', name: 'crm_requests.launched_by_type', class: 'align-middle launched_by_type'},
                    {data: 'tagged_to', name: 'crm_requests.tagged_to', class: 'align-middle tagged_to'},

                    // Placeholder for Tagging (Manual or Auto)
                    {data: 'tagged_manual_auto', name: 'tagged_manual_auto', class: 'align-middle tagged_manual_auto'},

                    {data: 'closed_date', name: 'crm_requests.closed_date', class: 'align-middle closed_date'},
                    {data: 'resolved_date', name: 'crm_requests.resolved_date', class: 'align-middle resolved_date'},
                    {data: 'case_closed_remark', name: 'sjcc.remarks', class: 'align-middle case_closed_remark'},
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