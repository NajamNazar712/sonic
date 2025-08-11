@extends('admin.layout.master')

@section('title', 'KAM & POC QSR Report')


@section('content')
    <h1 class="mb-1">
        KAM & POC QSR Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div class="row mb-2 justify-content-start">


                    <div class="col-4">
                            <fieldset class="form-group">
                                <select name="search_shipper[]" id="search_shippers" class="form-control select2" multiple required data-rule-required="true" data-msg-required="This field is required">
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
                                    @if(!in_array($status->id, [14, 25]))
                                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <select name="search_area" id="search_area" class="select2">
                            @foreach($areas as $area)
                                <option value="{{$area->id}}">{{$area->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-4">
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
                    <div class="col-4">
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
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="">Arrival Date To</span>
                            </span>
                            </div>
                            <input type="text" name="to_date1" class="form-control bg-primary border-primary white rounded-right" id="to_date1" placeholder="Arrival Date To">
                        </div>
                    </div>

                    <div class="col-12 mt-1 text-center">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
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
                    
                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                            <tr role="row" class="bg-primary white">
                                @foreach ($table_headers as $header)
                                    <th class="border-primary border-darken-1">
                                        {{ $header }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                    </table>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
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
        #toast-top-full-width {
            position: fixed !important;
            width: 100% !important;
            top: 0 !important;
            left: 0 !important;
            text-align: center !important;
        }
        .toast-top-full-width .toast {
            width: 90rem !important;
        }
        .toast-top-full-width .toast-message {
            font-size: 24px !important;
        }
        .toast-title{
            display: none !important;
        }

    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
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

        $('#search_shipment_status').prepend('<option value="" selected="selected"></option>').select2({
            placeholder:'Select Status',
            width:'100%',
            allowClear:true
        });
        $('#search_shippers').select2({
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
                        sub_segment_select : $('#sub_segment_select').val(),
                        report_type : 1
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
        $('#search_origin').prepend('<option value="" selected="selected"></option>').select2({
            placeholder:'Select Origin City',
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
        $('#search_zone').prepend('<option value="" selected="selected"></option>').select2({
            placeholder:'Select Zone',
            width:'100%',
            allowClear:true
        });
        $('#search_hub').prepend('<option value="" selected="selected"></option>').select2({
            placeholder:'Select Hub',
            width:'100%',
            allowClear:true
        });
        $('#search_area').prepend('<option value="" selected="selected"></option>').select2({
            placeholder:'Select Area',
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
            }
        });
        $('#to_date1').change(function() {
            var selectedOption = $(this).val();
            if (selectedOption != null)
            {
                $('#from_date').val(null).trigger('change');
                $('#to_date').val(null).trigger('change');
            }
        });

        var table = $('#datatable').DataTable({
            dom: '<"d-inline-block"l><"pull-right"B>tipr',
            scrollX: true,
            scrollY: '500px',
            buttons: [
                {
                    extend: 'excel',
                    title: 'QSR Report',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                    action: function(e) {
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
                            });
                            return;
                        }
                        $.ajax({
                            url: "{{ route('admin.reports.kam_and_poc_qsr.list') }}",
                            method: "POST",
                            data: {
                                excel: true,
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                search_shipment_status: $('#search_shipment_status').val(),
                                search_shippers: $('#search_shippers').val(),
                                search_origin: $('#search_origin').val(),
                                search_destination: $('#search_destination').val(),
                                search_qsr: $('#search_qsr').val(),
                                search_zone: $('#search_zone').val(),
                                search_hub: $('#search_hub').val(),
                                search_area :  $("#search_area").val(),
                                search_from: $('input[name="from_date_formatted"]').val(),
                                search_to: $('input[name="to_date_formatted"]').val(),
                                arrival_search_from: $('input[name="from_date1_formatted"]').val(),
                                arrival_search_to: $('input[name="to_date1_formatted"]').val(),
                                selectedValue: $('#export').val(),
                                selectedTexts: $('#export option:selected').map(function() {
                                    return $(this).text();
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
                            success: function(response) {
                                var blob = new Blob([response], { type: 'text/csv' });
                                var url = window.URL.createObjectURL(blob);
                                var a = document.createElement('a');
                                a.href = url;
                                a.download = 'KAM & POC QSR.csv';
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
                url: '{{ route('admin.reports.kam_and_poc_qsr.list') }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (d) {
                    d.search_from = $('input[name="from_date_formatted"]').val();
                    d.search_to = $('input[name="to_date_formatted"]').val();
                    d.arrival_search_from = $('input[name="from_date1_formatted"]').val();
                    d.arrival_search_to = $('input[name="to_date1_formatted"]').val();
                    d.search_shipment_status= $('#search_shipment_status').val();
                    d.search_shippers= $('#search_shippers').val();
                    d.search_origin= $('#search_origin').val();
                    d.search_destination= $('#search_destination').val();
                    d.search_qsr= $('#search_qsr').val();
                    d.search_zone= $('#search_zone').val();
                    d.search_hub= $('#search_hub').val();
                    d.search_area = $("#search_area").val();
                }
            },

            rowId: 'shipment_id',
            order: [[0, 'desc']],  // Update to a valid index based on your columns
            columns: [
                { data: null, name: 'serial_number', title: 'S.no', class: 'align-middle serial_number', orderable: false, searchable: false, render: function (data, type, row, meta) { return meta.row + 1; } },
                { data: 'tracking_number_link', title: 'Tracking Number', class: 'tracking_number_link', download: true },
                { data: 'shipment_order_id', title: 'Order ID', name: 'shipment_order_id', class: 'align-middle', download: true },
                { data: 'shipper', title: 'Shipper', class: 'shipper', download: true },
                { data: 'sale_person_name', title: 'Sales Person', class: 'sale_person_name', download: true },
                { data: 'first_attempt_date', title: 'First Attempt Date', class: 'first_attempt_date', download: true },
                { data: 'rider_picked_status_date', title: 'Rider Picked Status Date', class: 'rider_picked_status_date', download: true },
                { data: 'status_name', title: 'Status', class: 'status_name', download: true },
                { data: 'sub_hub', title: 'Sub Hub', class: 'sub_hub', download: true },
                { data: 'reason', title: 'Reason', class: 'reason', download: true },
                { data: 'shipment_journey_remarks', title: 'Remarks', class: 'shipment_journey_remarks', download: true },
                { data: 'last_scanned_by', title: 'Last Scanned By', class: 'last_scanned_by', download: true },
                { data: 'last_scanned_by_date', title: 'Last Scanned At Date', class: 'last_scanned_by_date', download: true },
                { data: 'last_location_screen_location_name', title: 'Last Scanned Location', class: 'last_location_screen_location_name', download: true },
                { data: 'total_attempt', title: 'Total Attempt', class: 'total_attempt', download: true },
                { data: 'bag_status', title: 'Cargo Status', class: 'total_attempt', download: true },
                { data: 'bag_seal_number', title: 'Bag Seal Number', class: 'bag_seal_number', download: true },
                // { data: 'bag_status', title: 'Bag Status', class: 'bag_status', download: true },
                { data: 'origin_arrival_date', title: 'Origin Arrival Date', class: 'origin_arrival_date', download: true },
                { data: 'destination_arrival_date', title: 'Destination Arrival Date', class: 'destination_arrival_date', download: true },
                { data: 'last_status_date', title: 'Last Status Date', class: 'last_status_date', download: true },
                { data: 'booked_date', title: 'Booked Status Date', class: 'booked_date', download: true },
                { data: 'shipping_mode', title: 'Shipping Mode', class: 'shipping_mode', download: true },
                { data: 'origin', title: 'Origin', class: 'origin', download: true },
                { data: 'destination', title: 'Destination', class: 'destination', download: true },
                { data: 'hub', title: 'Hub', class: 'hub', download: true },
                { data: 'area', title: 'Consignee City Area', class: 'area', download: true },
                { data: 'concerned_hub_name', title: 'Concerned Hub', class: 'concerned_hub_name', download: true },
                { data: 'return_city', title: 'Return City', class: 'return_city', download: true },
                { data: 'zone', title: 'Zone', class: 'zone', download: true },
                { data: 'consignee_name', title: 'Consignee Name', class: 'consignee_name', download: true },
                { data: 'consignee_number', title: 'Consignee No', class: 'consignee_number', download: true },
                { data: 'consignee_address', title: 'Consignee Address', class: 'consignee_address', download: true },
                { data: 'product_type', title: 'Product Description', class: 'product_type', download: true },
                { data: 'amount', title: 'Amount', class: 'amount', download: true },
                { data: 'aging', title: 'Aging (Arrival)', class: 'aging', download: true },
                { data: 'aging_last_status', title: 'Aging (Last Status)', class: 'aging_last_status', download: true },
                { data: 'request_number_id', title: 'Request No.', class: 'request_number_id', download: true },
                { data: 'valid_invalid_crm_status', title: 'Valid/Invalid Status', class: 'valid_invalid_crm_status', download: true },
                { data: 'request_status', title: 'Request Status', class: 'request_status', download: true },
                { data: 'crm_case_nature', title: 'Case Nature', class: 'crm_case_nature', download: true },
                { data: 'crm_case_nature_type', title: 'Case Nature Type', class: 'crm_case_nature_type', download: true },
                { data: 'request_launched_date_created_at', title: 'Complaint Launched Date', class: 'request_launched_date_created_at', download: true },
                { data: 'current_tat', title: 'TAT (launched + in process)', class: 'current_tat', download: true },
                { data: 'request_closed_created_at', title: 'Closed Date', class: 'request_closed_created_at', download: true },
                { data: 'case_closed_remarks', title: 'Case Closed Remark', class: 'case_closed_remarks', download: true },
                // { data: 'complaint_description', title: 'Complainant', class: 'complaint_description', download: true },
                { data: 'complaint_description', title: 'Complainant', class: 'complaint_description', download: true, render: (data) => data == 1 ? 'Consignee' : data == 2 ? 'Shipper' : '-' },

                { data: 'complainant_phone_number', title: 'Complainant Contact Number', class: 'complainant_phone_number', download: true },
                { data: 'shipment_quantity', title: 'Item Quantity', class: 'shipment_quantity', download: true },
                { data: 'shipment_pieces', title: 'Pieces', class: 'shipment_pieces', download: true },
                { data: 'rider_name', title: 'Rider', class: 'rider_name', download: true }
            ],

            rowCallback: function(row, data, index) {
                var info = table.page.info();
                $('td:eq(0)', row).html(index + 1 + info.page * info.length);
            },
            initComplete: function() {
                this.api().table().columns.adjust();
            }
        });

        
        let option = '';
        var columnNames2 = table.settings().init().columns.map(function (column) {
            if(column.download){
                let col_name = column.data;
                let col_text = column.title;
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

        $('#search_filter_btn').on('click',function () {
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            var arrival_from_date = $('#from_date1').val();
            var arrival_to_date = $('#to_date1').val();

            // Check if either pair of dates is filled
            var isFromDateFilled = from_date && to_date;
            var isArrivalDateFilled = arrival_from_date && arrival_to_date;

            if (!isFromDateFilled && !isArrivalDateFilled) {
                toastr.error('Please fill in either the "From Date" and "To Date" pair or the "Arrival From Date" and "Arrival To Date" pair.', 'Error!', {
                    positionClass: 'toast-top-full-width',
                    containerId: 'toast-top-full-width'
                });
                return false;
            } else {
                table.draw();
            }
        });

    </script>
@endsection