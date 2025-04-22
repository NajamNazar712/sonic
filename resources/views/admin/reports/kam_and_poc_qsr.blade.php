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
                    <div class="col-3">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="">Current Status From</span>
                            </span>
                            </div>
                            <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From" data-value="{{ Carbon\Carbon::now() }}">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="">Current Status To</span>
                            </span>
                            </div>
                            <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To" data-value="{{ Carbon\Carbon::now() }}">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="">Arrival Date From</span>
                            </span>
                            </div>
                            <input type="text" name="from_date1" class="form-control bg-primary border-primary white rounded-right" id="from_date1" placeholder="Arrival Date From">
                        </div>
                    </div>
                    <div class="col-3">
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
                                    <th class="border-primary border-darken-1">{{ $header }}</th>
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
                }
            },

            rowId: 'shipment_id',
            order: [[1, 'desc']],  // Update to a valid index based on your columns
            columns: [
                { orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', title: 'S.no', targets: 0, render: function (data, type, row) { return ''; } },
                { data: 'tracking_number_link', text: 'Tracking Number', as: 'tracking_number_link', value: 'tracking_number_link', download: true },
                { data: 'shipment_order_id', text: 'Order ID', as: 'shipment_order_id', value: 'shipment_order_id', download: true },
                { data: 'shipper', text: 'Shipper', as: 'shipper', value: 'shipper', download: true },
                { data: 'sale_person_name', text: 'Sales Person', as: 'sale_person_name', value: 'sale_person_name', download: true },
                { data: 'first_attempt_date', class: 'align-middle first_attempt_date', text: 'First Attempt Date', as: 'first_attempt_date', value: 'first_attempt_date', download: true },
                { data: 'rider_picked_status_date', text: 'Rider Picked Status Date', as: 'rider_picked_status_date', value: 'rider_picked_status_date', download: true },
                { data: 'status_name', text: 'Status', as: 'status_name', value: 'status_name', download: true },
                { data: 'sub_hub', text: 'Sub Hub', as: 'sub_hub', value: 'sub_hub', download: true },
                { data: 'reason', text: 'Reason', as: 'reason', value: 'reason', download: true },
                { data: 'shipment_journey_remarks', text: 'Remarks', as: 'shipment_journey_remarks', value: 'shipment_journey_remarks', download: true },
                { data: 'last_scanned_by', text: 'Last Scanned By', as: 'last_scanned_by', value: 'last_scanned_by', download: true },
                { data: 'last_scanned_by_date', text: 'Last Scanned At Date', as: 'last_scanned_by_date', value: 'last_scanned_by_date', download: true },
                { data: 'last_location_screen_location_name', text: 'Last Scanned Location', as: 'last_location_screen_location_name', value: 'last_location_screen_location_name', download: true },
                { data: 'total_attempt', text: 'Total Attempt', as: 'total_attempt', value: 'total_attempt', download: true },
                { data: 'bag_seal_number', text: 'Bag Seal Number', as: 'bag_seal_number', value: 'bag_seal_number', download: true },
                { data: 'bag_status', text: 'Bag Status', as: 'bag_status', value: 'bag_status', download: true },
                { data: 'origin_arrival_date', text: 'Origin Arrival Date', as: 'origin_arrival_date', value: 'origin_arrival_date', download: true },
                { data: 'destination_arrival_date', text: 'Destination Arrival Date', as: 'destination_arrival_date', value: 'destination_arrival_date', download: true },
                { data: 'last_status_date', text: 'Last Status Date', as: 'last_status_date', value: 'last_status_date', download: true },
                { data: 'booked_date', text: 'Booked Status Date', as: 'booked_date', value: 'booked_date', download: true },
                { data: 'shipping_mode', text: 'Shipping Mode', as: 'shipping_mode', value: 'shipping_mode', download: true },
                { data: 'origin', text: 'Origin', as: 'origin', value: 'origin', download: true },
                { data: 'destination', text: 'Destination', as: 'destination', value: 'destination', download: true },
                { data: 'hub', text: 'Hub', as: 'hub', value: 'hub', download: true },
                { data: 'area', text: 'Area', as: 'area', value: 'area', download: true },
                { data: 'concerned_hub_name', text: 'Concerned Hub', as: 'concerned_hub_name', value: 'concerned_hub_name', download: true },
                { data: 'return_city', text: 'Return City', as: 'return_city', value: 'return_city', download: true },
                { data: 'zone', text: 'Zone', as: 'zone', value: 'zone', download: true },
                { data: 'consignee_name', text: 'Consignee Name', as: 'consignee_name', value: 'consignee_name', download: true },
                { data: 'consignee_number', text: 'Consignee No', as: 'consignee_number', value: 'consignee_number', download: true },
                { data: 'consignee_address', text: 'Consignee Address', as: 'consignee_address', value: 'consignee_address', download: true },
                { data: 'product_type', text: 'Product Description', as: 'product_type', value: 'product_type', download: true },
                { data: 'amount', text: 'Amount', as: 'amount', value: 'amount', download: true },
                { data: 'aging', text: 'Aging (Arrival)', as: 'aging', value: 'aging', download: true },
                { data: 'aging_last_status', text: 'Aging (Last Status)', as: 'aging_last_status', value: 'aging_last_status', download: true },
                { data: 'request_number_id', text: 'Request No.', as: 'request_number_id', value: 'request_number_id', download: true },
                { data: 'valid_invalid_crm_status', text: 'Valid/Invalid Status', as: 'valid_invalid_crm_status', value: 'valid_invalid_crm_status', download: true },
                { data: 'request_status', text: 'Request Status', as: 'request_status', value: 'request_status', download: true },
                { data: 'crm_case_nature', text: 'Case Nature', as: 'crm_case_nature', value: 'crm_case_nature', download: true },
                { data: 'crm_case_nature_type', text: 'Case Nature Type', as: 'crm_case_nature_type', value: 'crm_case_nature_type', download: true },
                { data: 'request_launched_date_created_at', text: 'Complaint Launched Date', as: 'request_launched_date_created_at', value: 'request_launched_date_created_at', download: true },
                { data: 'current_tat', text: 'TAT (launched + in process)', as: 'current_tat', value: 'current_tat', download: true },
                { data: 'request_closed_created_at', text: 'Closed Date', as: 'request_closed_created_at', value: 'request_closed_created_at', download: true },
                { data: 'case_closed_remarks', text: 'Case Closed Remark', as: 'case_closed_remarks', value: 'case_closed_remarks', download: true },
                { data: 'complaint_description', text: 'Complainant', as: 'complaint_description', value: 'complaint_description', download: true },
                { data: 'complainant_phone_number', text: 'Complainant Contact Number', as: 'complainant_phone_number', value: 'complainant_phone_number', download: true },
                { data: 'shipment_quantity', text: 'Item Quantity', as: 'shipment_quantity', value: 'shipment_quantity', download: true },
                { data: 'shipment_pieces', text: 'Pieces', as: 'shipment_pieces', value: 'shipment_pieces', download: true },
                { data: 'rider_name', text: 'Rider', as: 'rider_name', value: 'rider_name', download: true }
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
                let col_name = column.value;
                let col_text = column.text;
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