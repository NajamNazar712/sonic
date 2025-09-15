@extends('admin.layout.master')

@section('title', 'Lost/Case Closed Summary Report')

@section('content')
    <h1 class="mb-1">
        Lost/Case Closed Summary Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row justify-content-center">
                    <div class="col-3">
                        <div class="form-group">
                            <input type="text" name="tracking_numbers" id="tracking_number"
                                class="dt_search tracking_numbers" placeholder="Tracking Number(s)"
                                data-tags-input-name="tracking_number">
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                    <span class="la la-calendar-o"></span>
                                </span>
                            </div>
                            <input type="text" name="date_from"
                                class="form-control pickadate bg-primary border-primary white rounded-right"
                                id="date_from" placeholder="Date (From)">
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                    <span class="la la-calendar-o"></span>
                                </span>
                            </div>
                            <input type="text" name="date_to"
                                class="form-control pickadate bg-primary border-primary white rounded-right"
                                id="date_to" placeholder="Date (To)">
                        </div>
                    </div>

                    <div class="form-group ml-1">
                        <button type="button" id="search_filter_btn" class="btn btn-outline-primary btn-min-width">
                            <i class="la la-search"></i>
                            Search
                        </button>
                    </div>
                </div>

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Tracking No.</th>
                            <th class="border-primary border-darken-1">Shipper Name</th>
                            <th class="border-primary border-darken-1">Defaulter Name</th>
                            <th class="border-primary border-darken-1">Trax Id</th>
                            <th class="border-primary border-darken-1">Active or Inactive Status</th>
                            <th class="border-primary border-darken-1">Responsible City</th>
                            <th class="border-primary border-darken-1">Shipment Last Status</th>
                            <th class="border-primary border-darken-1">Lost Category</th>
                            <th class="border-primary border-darken-1">Lost Requested By</th>
                            <th class="border-primary border-darken-1">Lost requested date/time</th>
                            <th class="border-primary border-darken-1">Approved By</th>
                            <th class="border-primary border-darken-1">Approved by date/time</th>
                            <th class="border-primary border-darken-1">COD Amount</th>
                            <th class="border-primary border-darken-1">Parcel Value</th>
                            <th class="border-primary border-darken-1">Case Closed Remarks</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.min.css') }}">

    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">

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

        .table>thead>tr>th {
            width: calc(1500px / 5);
            text-align: left;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
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

        .selectize-control {
            width: 100%;
        }

        .selectize-control .selectize-input {
            vertical-align: middle;
        }

        .selectize-control .selectize-input .item {
            word-break: break-all;
        }

        .green-row {
            background-color: #90ee90;
        }
    </style>
@endsection

@section('js')
<script src="{{ asset('app-assets/vendors/js/forms/tags/tagging.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/datatable_buttons.js') }}" type="text/javascript"></script>

<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>

<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
<script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
<script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">

        // Start date
        $('#date_from').pickadate({
            firstDay: 1,
            clear: '',
            selectYears: true,
            selectMonths: true,
            formatSubmit: 'yyyy-mm-dd',
            hiddenSuffix: '_formatted',
            onSet: function (context) {
                var selectedDate = this.get('select'); 
                if (selectedDate) {
                    $('#date_to').pickadate('picker').set('min', selectedDate);
                }
            }
        });

        // End date
        $('#date_to').pickadate({
            firstDay: 1,
            clear: '',
            selectYears: true,
            selectMonths: true,
            formatSubmit: 'yyyy-mm-dd',
            hiddenSuffix: '_formatted',
        });

        jQuery.fn.DataTable.Api.register('buttons.exportData()', function(options) {
            if (this.context.length) {
                body = [];
                var params = table.ajax.params();
                params.start = 0;
                params.length = -1;
                params.excel = true;
                var jsonResult = $.ajax({
                    url: '{{ route('admin.reports.lost_and_case_closed_summary_report.list') }}',
                    data: params,
                    method: 'POST',
                    success: function(result) {
                        head = [];
                        head.push('S.No');
                        head.push('Tracking .No');
                        head.push('Shipper Name');
                        head.push('Defaulter Name');
                        head.push('Trax Id');
                        head.push('Active or Inactive Status');
                        head.push('Responsible City');
                        head.push('Shipment Last Status');
                        head.push('Lost Category');
                        head.push('Lost Requested By');
                        head.push('Approved requested date/time');
                        head.push('Approved By');
                        head.push('Lost requested date/time');
                        head.push('COD Amount');
                        head.push('Parcel Value');
                        head.push('Case Closed Remarks');

                        $.each(result.data, function(index, values) {
                            row = [];
                            row.push(index + 1);
                            // row.push(values.shipment_tracking_number);
                            var trackingNumber = $(values.tracking_number).text();
                            row.push(trackingNumber);
                            row.push(values.shipper_name);
                            row.push(values.defaulter_name);
                            row.push(values.trax_id);
                            row.push(values.employee_status);
                            row.push(values.responsible_city);
                            row.push(values.latest_shipment_status);
                            row.push($('<textarea/>').html(values.lost_category).text());
                            row.push(values.requested_admin_name);
                            row.push(values.requested_date_time);
                            row.push(values.request_approved_admin);
                            row.push(values.approval_date_time);
                            row.push(values.cod_amount);
                            row.push(values.parcel_value);
                            row.push(values.case_closed_remarks);

                            body.push(row);
                        });
                    },
                    async: false
                });

                return {
                    body: body,
                    header: head
                };
            }
        });

        var table = $('#datatable').DataTable({
            dom: '<"d-inline-block"l><"pull-right"B>tipr',
            buttons: [{
                    extend: 'excel',
                    title: 'Lost and Case Closed Summary Report',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },
                'reset'
            ],
            scrollX: true,
            scrollY: '500px',
            lengthMenu: [
                [50, 100, 500, 1000, -1],
                [50, 100, 500, 1000, 'All']
            ],
            pageLength: 50,
            pagingType: 'full_numbers',
            processing: true,
            deferLoading: 0,
            language: {
                processing: data_table_loader
            },
            serverSide: true,
            ajax: {
                url: '{{ route('admin.reports.lost_and_case_closed_summary_report.list') }}',
                method: 'POST',
                data: function(d) {
                    d._token = '{{ csrf_token() }}'
                    d.search_date_from = $('input[name="date_from"]').val();
                    d.search_date_to = $('input[name="date_to"]').val();
                    d.tracking_numbers = $('#tracking_number').val();
                }
            },
            rowId: 'latest_shipment',
            order: [
                [9, 'desc']
            ],
            columns: [{
                    orderable: false,
                    searchable: false,
                    name: 'serial_number',
                    class: 'align-middle serial_number',
                    targets: 0,
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    orderable: true,
                    searchable: true,
                    class: 'align-middle tracking_number',
                    data: 'tracking_number',
                    name: 'tracking_number'
                },
                {
                    orderable: true,
                    searchable: true,
                    class: 'align-middle shipper_name',
                    data: 'shipper_name',
                    name: 'shipper_name'
                },
                {
                    orderable: true,
                    searchable: true,
                    class: 'align-middle defaulter_name',
                    data: 'defaulter_name',
                    name: 'defaulter_name'
                },
                {
                    orderable: true,
                    searchable: true,
                    class: 'align-middle trax_id',
                    data: 'trax_id',
                    name: 'trax_id'
                },
                {
                    orderable: true,
                    searchable: true,
                    class: 'align-middle employee_status',
                    data: 'employee_status',
                    name: 'employee_status'
                },
                {
                    orderable: true,
                    searchable: true,
                    class: 'align-middle responsible_city',
                    data: 'responsible_city',
                    name: 'responsible_city'
                },
                {
                    orderable: true,
                    searchable: true,
                    class: 'align-middle latest_shipment_status',
                    data: 'latest_shipment_status',
                    name: 'latest_shipment_status'
                },
                {data: 'lost_category', name: 'lcs.type', class: 'align-middle lost_category'},
                {
                    orderable: true,
                    searchable: true,
                    class: 'align-middle requested_admin_name',
                    data: 'requested_admin_name',
                    name: 'requested_admin_name'
                },
                {
                    orderable: true,
                    searchable: true,
                    class: 'align-middle requested_date_time',
                    data: 'requested_date_time',
                    name: 'requested_date_time'
                },
                {
                    orderable: true,
                    searchable: true,
                    class: 'align-middle request_approved_admin',
                    data: 'request_approved_admin',
                    name: 'request_approved_admin'
                },
                {
                    orderable: true,
                    searchable: true,
                    class: 'align-middle approval_date_time',
                    data: 'approval_date_time',
                    name: 'approval_date_time'
                },
                {
                    orderable: true,
                    searchable: true,
                    class: 'align-middle cod_amount',
                    data: 'cod_amount',
                    name: 'cod_amount'
                },
                {
                    orderable: true,
                    searchable: true,
                    class: 'align-middle parcel_value',
                    data: 'parcel_value',
                    name: 'parcel_value'
                },
                {
                    orderable: false,
                    searchable: false,
                    class: 'align-middle case_closed_remarks',
                    data: 'case_closed_remarks',
                    name: 'case_closed_remarks'
                }
            ],
            rowCallback: function(row, data, index) {
                var info = table.page.info();
                $('td:eq(0)', row).html(index + 1 + info.page * info.length);
            },

            initComplete: function() {
                var searchRow = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this
                    .api().table().header());

                var status_dropdown = '<select class="select2 form-control" id="status_dropdown">' +
                                        '<option value="0">All</option>' +
                                        '<option value="1">Active</option>' +
                                        '<option value="2">Inactive</option>' +
                                        '<option value="3">Active - No Info</option>' +
                                        '</select>'

                this.api().columns().every(function(index) {
                    var column = this;
                    var header = $(column.header());
                    var td = $(
                        '<td style="padding:5px;" class="border-primary border-lighten-2"></td>');

                    // Skip search bar for serial_number (index 0) and case_closed_remarks
                    if (index === 0 || header.hasClass('case_closed_remarks')) {
                        td.appendTo(searchRow);
                        return;
                    }

                    // Add select dropdown for employee_status column
                    if (header.hasClass('employee_status')) {
                        var select = $(status_dropdown)
                            .appendTo(td)
                            .on('change', function() {
                                column.search($(this).val()).draw();
                            });

                        if (column.search()) {
                            select.val(column.search());
                        }
                    } else {
                        // Create input field for other searchable columns
                        var input = $(
                                '<input type="text" class="form-control form-control-sm input-sm primary">'
                            )
                            .appendTo(td)
                            .on('change', function() {
                                column.search($(this).val()).draw();
                            });

                        if (column.search()) {
                            input.val(column.search());
                        }
                    }

                    td.appendTo(searchRow);
                });

                this.api().table().columns.adjust();
            }


        });

        $("#status_dropdown").prepend('<option value="" selected></option>').select2({
            placeholder: "Select Status",
            width:'100%',
            containerCssClass: 'select-xs',
            dropdownCssClass: 'form-control-sm p-0'
        });

        //Selectize
        var select = $('#tracking_number').selectize({
            placeholder: 'Tracking Number(s)',
            delimiter: ',',
            createOnBlur: true,
            persist: false,
            plugins: ['remove_button'],
            onDropdownOpen: function(dropdown) {
                dropdown.remove();
            },
            onType: function(str) {
                var regex = /^[0-9,]+$/;
                if (!regex.test(str)) {
                    select[0].selectize.setTextboxValue('');
                }
            },
            create: function(input) {
                if (input.length >= 1 && Math.floor(input) == input && $.isNumeric(input)) {
                    return {
                        value: input,
                        text: input
                    }
                } else {
                    return false;
                }
            },
        });

        // $('#track_form').bind('submit', function(e) {
        //     var tracking_numbers = $('#track_form .tracking_numbers').val();
        //     if (tracking_numbers != '') {
        //         table.draw();
        //     }
        // });

        table.draw();
        $('#search_filter_btn').on('click', function() {
            table.draw();
        });
    </script>
@endsection
