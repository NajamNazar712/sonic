@extends('admin.layout.master')

@section('title', 'Overall RV Action Report')

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <div class="row">
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            <h1>Overall RV Action Report</h1>
                            @include('admin.inc.messages')
                            <div class="col mt-2">
                                <form id="search_form" class="row mb-2 justify-content-center" novalidate="novalidate">
                                    {{-- Search by tracking number --}}
                                    <div class="col-4">
                                        <fieldset class="form-group">
                                            <input type="text" class="form-control" name="search_tracking_no" id="search_tracking_no" placeholder="Search Tracking Number" style="text-align: right;" fdprocessedid="l9xr5h">
                                        </fieldset>
                                    </div>
                                    {{-- Search by shipper name --}}
                                    <div class="col-4">
                                        <div class="form-group">
                                            <select name="search_shipper_name" id="search_shipper_name" class="form-control select2">
                                                @foreach($shippers as $shipper)
                                                    <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    {{-- Search by agent name --}}
                                    <div class="col-4">
                                        <div class="form-group">
                                            <select name="search_agent_name" id="search_agent_name" class="form-control select2">
                                                @foreach($agents as $agent)
                                                    <option value="{{$agent->id}}">{{$agent->name}} - {{$agent->trax_id}} - {{$agent->city_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Search date from filter --}}
                                    <div class="col-4">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o small-calender-icon"></span>
                                                </span>
                                            </div>
                                            <input type="text" name="search_date_from" class="form-control bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Date (From)"  data-value="{{ \Carbon\Carbon::now() }}">
                                        </div>
                                    </div>

                                    {{-- Search date to filter --}}
                                    <div class="col-4">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o small-calender-icon"></span>
                                                </span>
                                            </div>
                                            <input type="text" name="search_date_to" class="form-control bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Date (To)" data-value="{{ \Carbon\Carbon::now() }}">
                                        </div>
                                    </div>

                                    {{-- Search btn --}}
                                    <div class="col-2">
                                       
                                            <button type="submit" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                                        
                                    </div>
                                </form>
                                    <div class="row justify-content-end">

                                        <div class="col-6">
                                            <h4 for="export" class="font">Excel Column(s):</h4>
                                            <fieldset class="form-group">
                                                <select name="export[]" id="export" class="form-control select2" multiple="multiple">
                                                    <option value="selectAll">Select All</option>
                                                </select>
                                            </fieldset>
                                        </div>
                                    </div>
                            </div>


                            <table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Tracking Number</th>
                                    <th class="border-primary border-darken-1">Shipper Name</th>
                                    <th class="border-primary border-darken-1">Origin</th>
                                    <th class="border-primary border-darken-1">Destination</th>
                                    <th class="border-primary border-darken-1">Hub</th>
                                    {{-- <th class="border-primary border-darken-1">Area</th> --}}
                                    {{-- <th class="border-primary border-darken-1">Consignee Name</th> --}}
                                    {{-- <th class="border-primary border-darken-1">Number</th> --}}
                                    {{-- <th class="border-primary border-darken-1">Address</th> --}}
                                    <th class="border-primary border-darken-1">COD Amount</th>
                                    {{-- <th class="border-primary border-darken-1">Weight</th> --}}
                                    <th class="border-primary border-darken-1">Shipping Mode</th>
                                    <th class="border-primary border-darken-1">Service Type</th>
                                    <th class="border-primary border-darken-1">Arrival Date</th>
                                    <th class="border-primary border-darken-1">Action</th>
                                    <th class="border-primary border-darken-1">Call Status</th>
                                    <th class="border-primary border-darken-1">Call Findings</th>
                                    <th class="border-primary border-darken-1">Remarks</th>
                                    <th class="border-primary border-darken-1">Action Date</th>
                                    <th class="border-primary border-darken-1">Action Updated By</th>
                                    <th class="border-primary border-darken-1">RCP Agent Updated By</th>
                                    <th class="border-primary border-darken-1">RV Status</th>
                                    <th class="border-primary border-darken-1">RV Reason</th>
                                    <th class="border-primary border-darken-1">RV Status Date</th>
                                    <th class="border-primary border-darken-1">Current Status</th>
                                    <th class="border-primary border-darken-1">Current Status Date</th>
                                    <th class="border-primary border-darken-1">Fake Status</th>
                                    <th class="border-primary border-darken-1">Return Confirm Reason</th>
                                    <th class="border-primary border-darken-1">Delivery Attempt Count</th>
                                    <th class="border-primary border-darken-1">Re-Attempt Count</th>
                                    <th class="border-primary border-darken-1">Call History</th>
                                    <th class="border-primary border-darken-1">Call Count</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="unresponsive_count" data-backdrop="static" role="dialog" aria-labelledby="unresponsive_count" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Call History</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Calling Date</th>
                                <th>Calling Time</th>
                                <th>Call Findings</th>
                                <th>Call Finding Reasons</th>
                                <th>Remarks</th>
                                <th>Call To</th>
                                <th>Status</th>
                                <th>User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                       
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/fonts/line-awesome/css/line-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/fonts/simple-line-icons/style.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/cryptocoins/cryptocoins.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">
    <style>
        .error-message {
            color: red
        }

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

        .small-calender-icon {
            font-size: 17px !important;
        }

        .bg-gradient-directional-booked_shipments {
            background-image: linear-gradient(45deg, #5e187b, #ed86ff);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-arrived_shipments {
            background-image: linear-gradient(45deg, #074077, #2fbef5);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-in_transit {
            background-image: linear-gradient(45deg, #535BE2, #9ea5ff);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-destination {
            background-image: linear-gradient(45deg, #027d8a, #01e4e4);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-out_for_delivery {
            background-image: linear-gradient(45deg, #ff9819, #fff824);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-pending_shipments {
            background-image: linear-gradient(45deg, #39546d, #90929a);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-pending_confirmation {
            background-image: linear-gradient(45deg, #6a1fa2, #ff4961);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-delivered {
            background-image: linear-gradient(45deg, #076500, #11f118);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-return_confirm {
            background-image: linear-gradient(45deg, #ff0c0c, #ff9191);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-pending_return {
            background-image: linear-gradient(45deg, #7d491c, #e0b668de);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-return_delivered {
            background-image: linear-gradient(45deg, #02c123, #99ff12d1);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-cancelled_shipments {
            background-image: linear-gradient(45deg, #ff6a00, #ffb74c);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-complaints_launched {
            background-image: linear-gradient(45deg, #074077, #2FBEF5);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-complaints_in_process {
            background-image: linear-gradient(45deg, #6A1FA2, #FF4961);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-complaints_closed {
            background-image: linear-gradient(45deg, #076500, #11F118);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-complaints_rejected {
            background-image: linear-gradient(45deg, #FF0C0C, #FF9191);
            background-repeat: repeat-x;
        }

        .selectize-control {
            width: 300px !important;
        }

        .div_border {
            border-style: double;
        }

        .statusBooked {
            background-color: #5DADE2;
        }

        .statusOrigin {
            background-color: #E67E22;
        }

        .statusIntransit {
            background-color: #7F8C8D;
        }

        .statusDestination {
            background-color: #F1C40F;
        }

        .statusNotattempted {
            background-color: #1F618D;
        }

        .statusDeliveryunsuccessful {
            background-color: #28B463;
        }

        .statusOnhold {
            background-color: #154360;
        }
    </style>
@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    {{-- <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script> --}}
    <script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>


    <script>
        $(document).ready(function() {

            
        $('#search_shipper_name').prepend('<option value="" selected="selected"></option>')
            .select2({
                width: '100%',
                placeholder: 'Select Shippers',
                allowClear: true,
        });
        $('#search_agent_name').prepend('<option value="" selected="selected"></option>')
            .select2({
                width: '100%',
                placeholder: 'Select Agent',
                allowClear: true,
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
            $('#search_tracking_no').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            var search_date_from = $('#search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {

                    var old_date_formatted = $('input[name="search_date_from_formatted"]').val();
                    var contractMoment = moment(old_date_formatted);
                    var current = moment(contractMoment).add(31, 'days');
                    search_date_to.pickadate('picker').set('min', new Date(old_date_formatted),{muted:true});
                    search_date_to.pickadate('picker').set('max', new Date(current.toDate()),{muted:true});
                    search_date_to.pickadate('picker').set('select', new Date(old_date_formatted),{muted:true});
                }
            });

            var search_date_to = $('#search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                }
            });

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'RV Report',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                        action: function(e){
                                    if ($('#export').val().length === 0) {
                                        swal({
                                            text: 'Atleast 1 column should be selected for export.!',
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
                                        })
                                        return;
                                    }
                                    $.ajax({
                                        url: "{{ route('admin.reports.rv_report.list') }}",
                                        method: "POST",
                                        data: {
                                            excel: true,
                                            _token: $('meta[name="csrf-token"]').attr('content'),
                                            search_tracking_no : $('#search_tracking_no').val(),
                                            search_shipper_name : $('#search_shipper_name').val(),
                                            search_agent_name : $('#search_agent_name').val(),
                                            search_date_from : $('input[name="search_date_from_formatted"]').val(),
                                            search_date_to : $('input[name="search_date_to_formatted"]').val(),
                                            selectedValue: $('#export').val(),
                                            selectedTexts: $('#export option:selected').map(function() {
                                                return $(this).text()
                                            }).get()
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
                                            // Hide loader
                                            swal.close();
                                        },
                                        success: function(response) {
                                            var blob = new Blob([response], {
                                                type: 'text/csv'
                                            });
                                            var url = window.URL.createObjectURL(blob);
                                            var a = document.createElement('a');
                                            a.href = url;
                                            a.download = 'RV Report.csv';
                                            document.body.appendChild(a);
                                            a.click();
                                            window.URL.revokeObjectURL(url);
                                            document.body.removeChild(a);
                                        },
                                        error: function(xhr, status, error) {
                                            console.error('Failed to fetch CSV data:', status, error);
                                        }
                                    });
                                }
                    },'reset'],
                scrollX: true, scrollY: '500px',
                autoWidth: false,
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                deferLoading: 0,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.rv_report.list')}}',
                    method: 'POST',
                    headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    data: function (d) {
                        d.search_tracking_no = $('#search_tracking_no').val();
                        d.search_shipper_name = $('#search_shipper_name').val();
                        d.search_agent_name = $('#search_agent_name').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                order: [[13, 'desc']],
                columns: [
                    {name: 'serial_number', class: 'align-middle serial_number', orderable: false, searchable: false, targets: 0, render: function(data, type, row) {return '';}},
                    {data: 'tracking_number', name: 'tracking_number', class: 'text-center align-middle tracking_number',searchable: false, text:'Tracking Number', value:'tracking_number',download:true},
                    {data: 'shipper_name', name: 'shipper_name', class: 'align-middle shipper_name',searchable: false, text:'Shipper Name', value:'shipper_name',download:true},
                    {data: 'origin', name: 'origin', class: 'text-center align-middle origin',searchable: false,text:'Origin', value:'origin',download:true },
                    {data: 'destination', name: 'destination', class: 'align-middle destination',searchable: false , text:'Destination', value:'destination',download:true},
                    {data: 'hub', name: 'hub', class: 'align-middle hub',searchable: false, text:'Hub', value:'hub',download:true},
                    {data: 'cod_amount', name: 'cod_amount', class: 'align-middle cod_amount',searchable: false, text:'COD Amount', value:'cod_amount',download:true},
                    {data: 'shipping_mode', name: 'shipping_mode', class: 'align-middle shipping_mode',searchable: false, text:'Shipping Mode', value:'shipping_mode',download:true},
                    {data: 'service_type', name: 'service_type', class: 'align-middle service_type',searchable: false, text:'Service Type', value:'service_type',download:true},
                    {data: 'arrival_date', name: 'arrival_date', class: 'align-middle arrival_date',searchable: false,text:'Arrival Date', value:'arrival_date',download:true },
                    {data: 'action', name: 'action', class: 'align-middle action',searchable: false,text:'Action', value:'action',download:true},
                    {data: 'call_status', name: 'call_status', class: 'align-middle reason',searchable: false, text:'Call Status', value:'call_status',download:true},
                    {data: 'reason', name: 'reason', class: 'align-middle reason',searchable: false, text:'Call Findings', value:'reason',download:true},
                    {data: 'remarks', name: 'remarks', class: 'align-middle remarks',searchable: false, text:'Remarks', value:'remarks',download:true},
                    {data: 'action_date', name: 'action_date', class: 'align-middle action_date',searchable: false, text:'Action Date', value:'action_date',download:true},
                    {data: 'action_updated_by', name: 'action_updated_by', class: 'align-middle action_updated_by',searchable: false,text:'Action Updated By', value:'action_updated_by',download:true},
                    {data: 'rcp_agent_updated_by', name: 'rcp_agent_updated_by', class: 'align-middle rcp_agent_updated_by',searchable: false, text:'RCP Agent Updated By', value:'rcp_agent_updated_by',download:true},
                    {data: 'rv_status_name', name: 'rv_status.name', class: 'align-middle rv_status_name',searchable: false, text:'RV Status', value:'rv_status_name',download:true},
                    {data: 'rv_reason', name: 'rv_reason.name', class: 'align-middle rv_reason',searchable: false,text:'RV Reason', value:'rv_reason',download:true },
                    {data: 'rv_status_date', name: 'sj.updated_at', class: 'align-middle rv_status_date',searchable: false,text:'RV Status Date', value:'rv_status_date',download:true },
                    {data: 'current_status', name: 's_status.name', class: 'align-middle current_status',searchable: false,text:'Current Status', value:'current_status',download:true},
                    {data: 'current_status_date', name: 'shipments.updated_at', class: 'align-middle current_status_date',searchable: false, text:'Current Status Date', value:'current_status_date',download:true},
                    {data: 'fake_status', name: 'fake_status', class: 'align-middle fake_status',searchable: false,text:'Fake Status', value:'fake_status',download:true },
                    {data: 'rc_reason_name', name: 'rc_reason_name', class: 'align-middle fake_status',searchable: false,text:'Return Confirm Reason', value:'rc_reason_name',download:true },
                    {data: 'delivery_attempt_count', name: 'delivery_attempt_count', class: 'align-middle delivery_attempt_count',searchable: false, text:'Delivery Attempt Count', value:'delivery_attempt_count',download:true},
                    {data: 're_attempt_count', name: 're_attempt_count', class: 'align-middle re_attempt_count',searchable: false,text:'Re-Attempt Count', value:'re_attempt_count',download:true },
                    {data: 'unresponsive_count', name: 'unresponsive_count', class: 'align-middle unresponsive_count',searchable: false, text:'Call History', value:'unresponsive_count',download:false},
                    {data: 'call_count', name: 'call_count', class: 'align-middle call_count',searchable: false, text:'Call Count', value:'call_count',download:true},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
        });

            $('#search_form').bind('submit', function (e) {
                e.preventDefault();
                var tracking_number = $('#search_form #search_tracking_no').val();
                var shipper_name = $('#search_form #search_shipper_name').val();
                var agent_name = $('#search_form #search_agent_name').val();
                var search_date_from = $('#search_form #search_date_from').val();
                var search_date_to = $('#search_form #search_date_to').val();

                if (shipper_name !== '' || agent_name !== '' || tracking_number != ''  || (search_date_from != '' && search_date_to != '' )) {
                    table.draw();
                }

            });

            $('body').on('click', '.unresponsive_count_label', function() {
                var dataId = $(this).attr('data-shipments');
                $.ajax({
                    url: '{!! route('admin.reports.rv_report.rv_call_history') !!}',
                    method: 'GET',
                    data: { shipment_id: dataId },
                    dataType: 'json',
                    success: function(response) {
                        var tableBody = $('#unresponsive_count').find('tbody');
                        tableBody.empty();
                        $.each(response.data, function(index, rowData) {

                            var dateTimeParts = rowData.data.updated_at.split(' ');
                            var row = $('<tr>');
                            row.append($('<td>').text(dateTimeParts[0])); // Display date
                            row.append($('<td>').text(dateTimeParts[1])); // Display time
                            row.append($('<td>').text(rowData.data.call_status)); //Call Findings
                            row.append($('<td>').text(rowData.data.rv_call_finding.name)); //Call Finding reasons
                            row.append($('<td>').text(rowData.data.remarks != null ? rowData.data.remarks : '-')); //Reamrks
                            row.append($('<td>').text('Consignee')); //Call To
                            row.append($('<td>').text(rowData.data.shipment.status_shipper.name != null ? rowData.data.shipment.status_shipper.name : '-')); //Status
                            row.append($('<td>').text(rowData.user_name)); //User
                            tableBody.append(row);
                        });

                        $("#unresponsive_count").modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr, status, error);
                    }
                });
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
        });
    </script>
    @endsection
