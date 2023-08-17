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
                            <input type="text" class="form-control" name="search_tracking_no" id="search_tracking_no" placeholder="Search Tracking Number(s)">
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <input type="text" class="form-control" name="search_request_number" id="search_request_number" placeholder="Search Request Number">
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
                        <th class="border-primary border-darken-1">Request No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Shipment Status</th>
                        <th class="border-primary border-darken-1">Last Status Date</th>
                        <th class="border-primary border-darken-1">Case Nature</th>
                        <th class="border-primary border-darken-1">Case Nature Type</th>
                        <th class="border-primary border-darken-1">Description</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Zone</th>
                        <th class="border-primary border-darken-1">Responsible Hub</th>
                        <th class="border-primary border-darken-1">Responsible Zone</th>
                        <th class="border-primary border-darken-1">Arrival Date</th>
                        <th class="border-primary border-darken-1">Channel</th>
                        <th class="border-primary border-darken-1">Agent</th>
                        <th class="border-primary border-darken-1">COD Amount</th>
                        <th class="border-primary border-darken-1">Adjusted Amount</th>
                        <th class="border-primary border-darken-1">Weight Adjusted Amount</th>
                        <th class="border-primary border-darken-1">Adjusted Percentage</th>
                        <th class="border-primary border-darken-1">Remaining Percentage</th>
                        <th class="border-primary border-darken-1">Request Status</th>
                        <th class="border-primary border-darken-1">Re-Open Status Date</th>
                        <th class="border-primary border-darken-1">Launched By</th>
                        <th class="border-primary border-darken-1">Launched By User Type</th>
                        <th class="border-primary border-darken-1">Admin User's Department</th>
                        <th class="border-primary border-darken-1">Launched Date</th>
                        <th class="border-primary border-darken-1">Launched To Date (TAT)</th>
                        <th class="border-primary border-darken-1">Assigned Date</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Valid/Invalid Date</th>
                        <th class="border-primary border-darken-1">Resolved Date</th>
                        <th class="border-primary border-darken-1">Closed Date</th>
                        <th class="border-primary border-darken-1">Case Closed Remark</th>
                        <th class="border-primary border-darken-1">Last Internal Comment By</th>
                        <th class="border-primary border-darken-1">Last Internal Comment</th>
                        <th class="border-primary border-darken-1">Last Internal Comment Date</th>
                        <th class="border-primary border-darken-1">Last External Comment</th>
                        <th class="border-primary border-darken-1">Last External Comment Date</th>
                        <th class="border-primary border-darken-1">Tagged To</th>
                        <th class="border-primary border-darken-1">Tagged Hub</th>
                        <th class="border-primary border-darken-1">Tagged At</th>
                        <th class="border-primary border-darken-1">Tagged TAT</th>
                        <th class="border-primary border-darken-1">Rating</th>
                        <th class="border-primary border-darken-1">Responsilbe Person</th>
                        <th class="border-primary border-darken-1">Responsilbe Person's Hub</th>
                        <th class="border-primary border-darken-1">Claim Adjustment Status</th>
                        
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
                placeholder: 'Search Request Number(s)*',
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

                            head.push('S.No');  
                            head.push('Request No.');
                            head.push('Tracking No.');
                            head.push('Shipment Status');
                            head.push('Last Status Date');
                            head.push('Case Nature');
                            head.push('Case Nature Type');
                            head.push('Description');
                            head.push('Shipper');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Hub');
                            head.push('Zone');
                            head.push('Reponsible Hub');
                            head.push('Responsible Zone');
                            head.push('Arrival Date');
                            head.push('Channel');
                            head.push('Agent');
                            head.push('COD Amount');
                            head.push('Adjusted Amount');
                            head.push('Weight Adjusted Amount');
                            head.push('Adjusted Percentage');
                            head.push('Remaining Percentage');
                            head.push('Request Status');
                            head.push('Re Open Status Date');
                            head.push('Launched By');
                            head.push('Launched By User Type');
                            head.push('Admin User Department');
                            head.push('Launched Date');
                            head.push('Launched To Date (TAT)');
                            head.push('Assigned Date');
                            head.push('Status');                          
                            head.push('Valid/Invalid Date');
                            head.push('Resolved Date');
                            head.push('Closed Date');
                            head.push('Case closed Remarks');
                            head.push('Last Internal Comment By');
                            head.push('Last Internal Comment');
                            head.push('Last Internal Comment Date');
                            head.push('Last External Comment');
                            head.push('Last External Comment Date');
                            head.push('Tagged To');
                            head.push('Tagged Hub');
                            head.push('Tagged At');
                            head.push('Tagged TAT');
                            head.push('Rating');
                            head.push('Responsilbe Person');
                            head.push('Responsilbe Person\'s Hub');
                            head.push('Claim Adjustment Status');
                            
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.request_number);
                                row.push(values.tracking_number);
                                row.push(values.status);
                                row.push(values.last_status_date);
                                row.push(values.case_nature);
                                row.push(values.case_nature_type);
                                row.push(values.description);
                                row.push(values.shipper_name);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.hub);
                                row.push(values.zone);
                                row.push(values.responsible_hub);
                                row.push(values.responsible_zone);
                                row.push(values.arrival_date);
                                row.push(values.channel);
                                row.push(values.agent);
                                row.push(values.cod_amount);
                                row.push(values.adjusted_amount);
                                row.push(values.weight_charges);
                                row.push(values.adjusted_percentage);
                                row.push(values.remaining_percentage);
                                row.push(values.request_status);
                                row.push(values.reopen_date);
                                row.push(values.launched_by_name);
                                row.push(values.launched_by_type);
                                row.push(values.admin_department);
                                row.push(values.launched_date);
                                row.push(values.launched_to_today);
                                row.push(values.assigned_date);
                                row.push(values.valid_invalid_status);
                                row.push(values.valid_invalid_date);
                                row.push(values.resolved_date);
                                row.push(values.closed_date);
                                row.push(values.case_closed_remark);
                                row.push(values.last_comment_name);
                                row.push(values.last_comment.replace(/<br>/gi, '\n'));
                                row.push(values.last_comment_date);
                                row.push(values.last_comment_external);
                                row.push(values.last_comment_date_external);
                                row.push(values.tagged_to);
                                row.push(values.tagged_hub);
                                row.push(values.tagged_at);
                                row.push(values.tagged_aging);
                                row.push(values.rating);
                                row.push(values.responsibe_person_name);
                                row.push(values.responsibe_person_hub);
                                row.push(values.claim_adjustment_status);
                                


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

                    }
                },
                // rowId: 'shipment_id',
                order: [[27, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'id_padded_link', name: 'crm_requests.id', class: 'align-middle request_number'},
                    {data: 'tracking_number_link', name: 's.tracking_number', class: 'align-middle tracking_number_link'},
                    {data: 'status', name: 'ss.name', class: 'align-middle status'},
                    {data: 'last_status_date', name: 'ss.created_at', class: 'align-middle last_status_date'},
                    {data: 'case_nature', name: 'crcn.name', class: 'align-middle case_nature'},
                    {data: 'case_nature_type', name: 'crcnt.type', class: 'align-middle case_nature_type'},
                    {data: 'description', name: 'crm_requests.description', class: 'align-middle description'},
                    {data: 'shipper_name', name: 'u.name', class: 'align-middle shipper_name'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'hub', name: 'h.name', class: 'align-middle hub'},
                    {data: 'zone', name: 'z.name', class: 'align-middle zone'},
                    {data: 'responsible_hub', name: 'responsible_hub', class: 'align-middle responsible_hub' ,orderable: false, searchable: false,},
                    {data: 'responsible_zone', name: 'responsible_zone', class: 'align-middle responsible_zone',orderable: false, searchable: false,},
                    {data: 'arrival_date', name: 'sj.created_at', class: 'align-middle arrival_date'},
                    {data: 'channel', name: 'crc.id', class: 'align-middle channel'},
                    {data: 'agent', name: 'a.name', class: 'align-middle agent'},
                    {data: 'cod_amount', name: 's.amount', class: 'align-middle cod_amount'},
                    {data: 'adjusted_amount', name: 'adjustment.adjustment_amount', class: 'align-middle adjusted_amount'},
                    {data: 'weight_charges', name: 'change_shipment_weight_logs.new_charges', class: 'align-middle weight_charges'},
                    {data: 'adjusted_percentage', name: 'adjusted_percentage', class: 'align-middle adjusted_percentage', orderable: false, searchable: false},
                    {data: 'remaining_percentage', name: 'remaining_percentage', class: 'align-middle remaining_percentage', orderable: false, searchable: false},
                    {data: 'request_status', name: 'crs.name', class: 'align-middle request_status'},
                    {data: 'reopen_date', name: 'crsh.created_at', class: 'align-middle reopen_date'},
                    {data: 'launched_by_name', name: 'launched_by_name', class: 'align-middle launched_by_name'},
                    {data: 'launched_by_type', name: 'crm_requests.launched_by', class: 'align-middle launched_by_type'},
                    {data: 'admin_department', name: 'admin_department', class: 'align-middle admin_department'},
                    {data: 'launched_date', name: 'crm_requests.created_at', class: 'align-middle launched_date'},
                    {data: 'launched_to_today', name: 'launched_to_today', class: 'align-middle launched_to_today', orderable: false, searchable: false},
                    {data: 'assigned_date', name: 'crah.created_at', class: 'align-middle assigned_date'},
                    {data: 'valid_invalid_status', name: 'crm_requests.status', class: 'align-middle valid_invalid_status', orderable: false, searchable: false},
                    {data: 'valid_invalid_date', name: 'crsh.created_at', class: 'align-middle valid_invalid_date', orderable: false, searchable: false},
                    {data: 'resolved_date', name: 'crshr.created_at', class: 'align-middle resolved_date'},
                    {data: 'closed_date', name: 'crshc.created_at', class: 'align-middle closed_date'},
                    {data: 'case_closed_remark', name: 'sjcc.remarks', class: 'align-middle case_closed_remark'},
                    {data: 'last_comment_name', name: 'last_comment_name', class: 'align-middle last_comment_name'},
                    {data: 'last_comment', name: 'ccs.comment', class: 'align-middle last_comment'},
                    {data: 'last_comment_date', name: 'ccs.created_at', class: 'align-middle last_comment_date'},
                    {data: 'last_comment_external', name: 'ccse.comment', class: 'align-middle last_comment'},
                    {data: 'last_comment_date_external', name: 'ccse.created_at', class: 'align-middle last_comment_date'},
                    {data: 'tagged_to', name: 'crt.tagged_id', class: 'align-middle tagged_to', orderable: false, searchable: false},
                    {data: 'tagged_hub', name: 'crtadh.name', class: 'align-middle tagged_hub', orderable: false, searchable: false},
                    {data: 'tagged_at', name: 'crt.created_at', class: 'align-middle tagged_at', orderable: false, searchable: false},
                    {data: 'tagged_aging', name: 'crt.created_at', class: 'align-middle tagged_aging', orderable: false, searchable: false},
                    {data: 'rating_code', name: 'crr.name', class: 'align-middle rating_code'},
                    {data: 'responsibe_person_name', name: 'responsibe_person_name', class: 'align-middle responsibe_person_name'},
                    {data: 'responsibe_person_hub', name: 'responsibe_person_hub', class: 'align-middle responsibe_person_hub'},
                    {data: 'claim_adjustment_status', name: 'claim_adjustment_status', class: 'align-middle claim_adjustment_status'},

                    
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