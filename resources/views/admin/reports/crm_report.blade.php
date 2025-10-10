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
                            <input type="text" class="form-control" name="search_request_number" id="search_request_number" placeholder="Search Ticket Number(s)">
                        </fieldset>
                    </div>

                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_shipper" id="search_shipper" class="form-control select2" multiple="multiple">
                            </select>
                        </fieldset>
                    </div>


                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_hub" id="search_hub" class="form-control select2">
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

                <div class="row justify-content-end">
                    <div class="col-4">
                        <h4 for="export" class="font">Excel Column(s):</h4>

                        <fieldset class="form-group">
                            <select name="export[]" id="export" class="form-control select2" multiple="multiple">
                                <option value="selectAll">Select All</option>
                            </select>
                        </fieldset>
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
                        <th class="border-primary border-darken-1">Aging (From Launch Date To Today)</th>

                        <th class="border-primary border-darken-1">Responsible Hub</th>

                        <!-- Sub Hub column added -->
                        <th class="border-primary border-darken-1">Sub Hub</th>

                        <th class="border-primary border-darken-1">Responsible Zone</th>
                        <th class="border-primary border-darken-1">Agent</th>

                        <!-- Parcel Value column added -->
                        <th class="border-primary border-darken-1">Parcel Value</th>
                        <th class="border-primary border-darken-1">Claim Amount</th>

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
                        <th class="border-primary border-darken-1">Actual Weight</th>

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
                        <th class="border-primary border-darken-1">Complainant</th>
                        <th class="border-primary border-darken-1">Complainant Contact Number</th>

                        <th class="border-primary border-darken-1">Claim Resolved/Invalid Reason</th>
                        <th class="border-primary border-darken-1">Claim Resolved Sub Reason</th>

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
                placeholder: 'Search Ticket Number(s)',
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
            $('#search_hub').select2({
                width:'100%',
                placeholder:"Select Hub",
                allowClear:true,
                multiple: true,
                minimumInputLength: 2,
                ajax: {
                    dataType: 'json',
                    url:  '{!! route('admin.reports.data_for_dropdown', ['type'=>'hub']) !!}',
                        data: function (params) {
                            return {
                                search: params.term,
                            }
                        },
                        processResults: function (data) {
                            return {
                                results: data
                            };
                        },
                    delay: 700,
                }
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
            $('#search_case_nature_type').select2({
                width:'100%',
                placeholder:"Select Case Nature Type",
                allowClear:true,
                multiple: true,
                minimumInputLength: 2,
                ajax: {
                    dataType: 'json',
                    url:  '{!! route('admin.reports.data_for_dropdown', ['type'=>'nature']) !!}',
                        data: function (params) {
                            return {
                                search: params.term,
                            }
                        },
                        processResults: function (data) {
                            return {
                                results: data
                            };
                        },
                    delay: 700,
                }
            });
            $('#search_shipper').select2({
                width:'100%',
                placeholder:"Select Shipper",
                allowClear:true,
                multiple: true,
                minimumInputLength: 2,
                ajax: {
                    dataType: 'json',
                    url:  '{!! route('admin.accounts.shipper_names.dropdown',['type'=>'active']) !!}',
                        data: function (params) {
                            return {
                                search: params.term,
                                exclude_shipper : 0
                            }
                        },
                        processResults: function (data) {
                            return {
                                results: data
                            };
                        },
                    delay: 700,
                }
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

            function addMonths(date, months) {
                let d = new Date(date);
                d.setMonth(d.getMonth() + months);
                return d;
            }

            function subtractMonths(date, months) {
                let d = new Date(date);
                d.setMonth(d.getMonth() - months);
                return d;
            }

            $('#from_date').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        let fromDate = new Date(context.select);
                        let toMinDate = new Date(fromDate); // same as from date
                        let toMaxDate = addMonths(fromDate, 3); // max 3 months later

                        let toPicker = $('#to_date').pickadate('picker');
                        toPicker.set('min', toMinDate);
                        toPicker.set('max', toMaxDate);
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
                        let toDate = new Date(context.select);
                        let fromMaxDate = new Date(toDate); // same as to date
                        let fromMinDate = subtractMonths(toDate, 3); // min 3 months earlier

                        let fromPicker = $('#from_date').pickadate('picker');
                        fromPicker.set('max', fromMaxDate);
                        fromPicker.set('min', fromMinDate);
                    }
                }
            });


            var index_column = 0;
            var table = $('#datatable').DataTable({
                scrollX: true, scrollY: '500px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'CRM Report',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                        action: function(e, dt, node, config) {
                            if ($('#export').val().length === 0) {
                                swal({
                                    text: 'At least 1 column should be selected for export!',
                                    title: 'Please select excel column(s)',
                                    icon: 'warning',
                                    buttons: {
                                        cancel: {
                                            text: 'OK',
                                            value: null,
                                            visible: true,
                                            closeModal: true,
                                        }
                                    },
                                    dangerMode: true
                                });
                                return;
                            }

                            $.ajax({
                                url: "{{ route('admin.reports.crm.list') }}",
                                method: "POST",
                                data: {
                                    excel: true,
                                    _token: $('meta[name="csrf-token"]').attr('content'),
                                    search_tracking_no : $('#search_tracking_no').val(),
                                    search_hub : $('#search_hub').val(),
                                    search_zone : $('#search_zone').val(),
                                    search_agent : $('#search_agent').val(),
                                    search_case_nature : $('#search_case_nature').val(),
                                    search_case_nature_type : $('#search_case_nature_type').val(),
                                    search_shipper : $('#search_shipper').val(),
                                    search_request_number : $('#search_request_number').val(),
                                    search_status : $('#search_status').val(),
                                    search_from : $('input[name="from_date_formatted"]').val(),
                                    search_to : $('input[name="to_date_formatted"]').val(),
                                    selectedValue: $('#export').val(),
                                    selectedTexts: $('#export option:selected').map(function() {
                                        return $(this).text()
                                    }).get(),
                                },
                                beforeSend: function() {
                                    swal({
                                        title: 'Please Wait!',
                                        text: 'Downloading is in progress',
                                        icon: 'info',
                                        buttons: false,
                                        closeOnClickOutside: false,
                                        closeOnEsc: false
                                    });
                                },
                                complete: function() {
                                    swal.close();
                                },
                                success: function(response, status, xhr) {
                                    var disposition = xhr.getResponseHeader('Content-Disposition');
                                    if (disposition && disposition.indexOf('attachment') !== -1) {
                                        var filename = 'CRM_Report.csv';
                                        var blob = new Blob([response], { type: 'text/csv' });
                                        var link = document.createElement('a');
                                        var url = window.URL.createObjectURL(blob);
                                        link.href = url;
                                        link.download = filename;
                                        document.body.appendChild(link);
                                        link.click();
                                        window.URL.revokeObjectURL(url);
                                        document.body.removeChild(link);
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error('Failed to fetch CSV data:', status, error);
                                }
                            });
                        }
                    }
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
                    {
                        orderable: false,
                        searchable: false,
                        name: 'serial_number',
                        class: 'align-middle serial_number',
                        targets: 0,
                        render: function (data, type, row) { return ''; }
                    },
                    {data: 'id_padded_link', name: 'crm_requests.id', class: 'align-middle request_number', text: 'Request Number', value: 'id_padded_link', download: true},
                    {data: 'request_status', name: 'crs.name', class: 'align-middle request_status', text: 'Request Status', value: 'request_status', download: true},

                    {data: 'valid_invalid_status', name: 'crm_requests.status', class: 'align-middle valid_invalid_status', orderable: false, searchable: false, text: 'Valid/Invalid Status', value: 'valid_invalid_status', download: true},
                    {data: 'tracking_number_link', name: 's.tracking_number', class: 'align-middle tracking_number_link', text: 'Tracking Number', value: 'tracking_number_link', download: true},
                    {data: 'shipper_name', name: 'u.name', class: 'align-middle shipper_name', text: 'Shipper Name', value: 'shipper_name', download: true},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin', text: 'Origin', value: 'origin', download: true},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination', text: 'Destination', value: 'destination', download: true},
                    {data: 'hub', name: 'h.name', class: 'align-middle hub', text: 'Hub', value: 'hub', download: true},
                    {data: 'zone', name: 'z.name', class: 'align-middle zone', text: 'Zone', value: 'zone', download: true},
                    {data: 'arrival_date', name: 'sj.created_at', class: 'align-middle arrival_date', text: 'Arrival Date', value: 'arrival_date', download: true},

                    // Placeholder for Arrival to Today (TAT)
                    {data: 'arrival_today', name: 'sj.updated_at', class: 'align-middle arrival_today', text: 'Arrival to Today (TAT)', value: 'arrival_today', download: true},

                    {data: 'status', name: 'ss.name', class: 'align-middle status', text: 'Status', value: 'status', download: true},
                    {data: 'last_status_date', name: 'ss.created_at', class: 'align-middle last_status_date', text: 'Last Status Date', value: 'last_status_date', download: true},
                    {data: 'case_nature', name: 'crcn.name', class: 'align-middle case_nature', text: 'Case Nature', value: 'case_nature', download: true},
                    {data: 'case_nature_type', name: 'crcnt.type', class: 'align-middle case_nature_type', text: 'Case Nature Type', value: 'case_nature_type', download: true},
                    {data: 'description', name: 'crm_requests.description', class: 'align-middle description', text: 'Description', value: 'description', download: true},
                    {data: 'launched_date', name: 'crm_requests.created_at', class: 'align-middle launched_date', text: 'Launched Date', value: 'launched_date', download: true},
                    {data: 'launched_to_today', name: 'launched_to_today', class: 'align-middle launched_date', text: 'Aging (From Launch Date To Today) ', value: 'launched_to_today', download: true , orderable: false},

                    {data: 'responsible_hub', name: 'h.name', class: 'align-middle responsible_hub', text: 'Responsible Hub', value: 'responsible_hub', download: true},

                    // Placeholder for Sub Hub
                    {data: 'sub_hub', name: 'ca.name', class: 'align-middle sub_hub', text: 'Sub Hub', value: 'sub_hub', download: true},

                    {data: 'responsible_zone', name: 'z.name', class: 'align-middle responsible_zone', text: 'Responsible Zone', value: 'responsible_zone', download: true},
                    {data: 'agent', name: 'a.name', class: 'align-middle agent', text: 'Agent', value: 'agent', download: true},

                    // Parcel Value column added here
                    {data: 'parcel_value', name: 's.parcel_value', class: 'align-middle parcel_value', text: 'Parcel Value', value: 'parcel_value', download: true},

                    {data: 'product_cost', name: 'crm_requests.product_cost', class: 'align-middle product_cost', text: 'Claim Amount', value: 'product_cost', download: true},

                    {data: 'cod_amount', name: 's.amount', class: 'align-middle cod_amount', text: 'COD Amount', value: 'cod_amount', download: true},
                    {data: 'adjusted_amount', name: 'adjustment.adjustment_amount', class: 'align-middle adjusted_amount', text: 'Adjusted Amount', value: 'adjusted_amount', download: true},
                    {data: 'weight_charges', name: 'change_shipment_weight_logs.new_charges', class: 'align-middle weight_charges', text: 'Weight Charges', value: 'weight_charges', download: true},

                    // Segment column added here
                    {data: 'segment', name: 'seg.name', class: 'align-middle segment', text: 'Segment', value: 'segment', download: true},

                    // Placeholder for Product Description
                    {data: 'shipment_description', name: 'si.description', class: 'align-middle shipment_description', text: 'Product Description', value: 'shipment_description', download: true},

                    // Placeholder for Product Type
                    {data: 'product_name', name: 'prod.product_name', class: 'align-middle product_name', text: 'Product Name', value: 'product_name', download: true},

                    // Placeholder for pieces
                    {data: 'pieces', name: 's.pieces', class: 'align-middle pieces', text: 'Pieces', value: 'pieces', download: true},

                    // Placeholder for Quantity
                    {data: 'shipment_quantity', name: 'si.quantity', class: 'align-middle shipment_quantity', text: 'Quantity', value: 'shipment_quantity', download: true},

                    // Placeholder for Weight
                    {data: 'actual_weight', name: 's.actual_weight', class: 'align-middle actual_weight', text: 'Weight', value: 'actual_weight', download: true},

                    // Placeholder for Key Account Category
                    {data: 'shipper_category', name: 'shipper_category', class: 'align-middle shipper_category', text: 'Shipper Category', value: 'shipper_category', download: true, orderable: false},

                    {data: 'launched_by_name', name: 'launched_by_name', class: 'align-middle launched_by_name', text: 'Launched By', value: 'launched_by_name', download: true},
                    {data: 'channel', name: 'crc.channel', class: 'align-middle channel', text: 'Channel', value: 'channel', download: true},
                    {data: 'launched_by_type', name: 'launched_by_type', class: 'align-middle launched_by_type', text: 'Launched By Type', value: 'launched_by_type', download: true},
                    {data: 'tagged_to', name: 'crm_requests.tagged_to', class: 'align-middle tagged_to', text: 'Tagged To', value: 'tagged_to', download: true, orderable: false},

                    // Placeholder for Tagging (Manual or Auto)
                    {data: 'tagged_manual_auto', name: 'tagged_manual_auto', class: 'align-middle tagged_manual_auto', text: 'Tagging (Manual/Auto)', value: 'tagged_manual_auto', download: true, orderable: false},

                    {data: 'closed_date', name: 'crshc.created_at', class: 'align-middle closed_date', text: 'Closed Date', value: 'closed_date', download: true},
                    {data: 'resolved_date', name: 'crshr.created_at', class: 'align-middle resolved_date', text: 'Resolved Date', value: 'resolved_date', download: true},
                    {data: 'case_closed_remark', name: 'sjcc.remarks', class: 'align-middle case_closed_remark', text: 'Case Closed Remark', value: 'case_closed_remark', download: true},
                    {data: 'case_nature_complainant', name: 'crm_requests.case_nature_complainant', class: 'align-middle case_nature_complainant', text: 'Complainant', value: 'case_nature_complainant', download: true},
                    {data: 'complainant_phone', name: 'crm_requests.complainant_phone', class: 'align-middle complainant_phone', text: 'Complainant Contact Number', value: 'complainant_phone', download: true},
                    {data: 'claim_resolved_invalid_reason', name: 'claim_resolved_invalid_reason', class: 'align-middle claim_resolved_invalid_reason', text: 'Claim Resolved/Invalid Reason', value: 'claim_resolved_invalid_reason', download: true},
                    {data: 'claim_resolved_sub_reason', name: 'claim_resolved_sub_reason', class: 'align-middle claim_resolved_sub_reason', text: 'Claim Resolved Sub Reason', value: 'claim_resolved_sub_reason', download: true}

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

            let option = '';
            var columnNames2 = table.settings().init().columns.map(function (column) {
                if(column.download){
                    let col_name = column.value;
                    let col_text = column.text;

                    // if(column.as){
                    //     col_name+= ' as ' +column.as;
                    // }
                    if (col_name && col_text) {
                        option += `<option value="${col_name}">${col_text}</option>`;
                    }
                }
            });
            $('#export').append(option).select2({
                columns: 1,
                placeholder: 'Excel Column(s)',
                search: true,
                selectAll: true
            });

            $('#export').select2({
                width:'100%',
                placeholder:"Excel Column(s)",
                allowClear:true,
            });
            $('#export').on('select2:select', function(e) {
                var selectAll = $('#export').find('option[value="selectAll"]');
                var firstOption = $('#export option').first();
                if (e.params.data.id === 'selectAll') {
                    firstOption.data().data.text = 'Un Select All';
                    $('#export').find('option').not(selectAll).prop('selected', true).trigger('change');

                }
            });
            $('#export').on('select2:unselect', function(e) {
                var selectAll = $('#export').find('option[value="selectAll"]');
                var firstOption = $('#export option').first();
                if (e.params.data.id === 'selectAll') {
                    firstOption.data().data.text = 'Select All';
                    $('#export').find('option').not(selectAll).prop('selected', false).trigger('change');
                }
            });

        });

    </script>
@endsection