@extends('admin.layout.master')
@section('title', 'Return Confirm OTP')

@section('content')
    <h1 class="mb-1">
        Return Confirm OTP
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div class="row mb-2 justify-content-center">
                    <div class="col-12 ">
                        <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                            <div class="col-4">
                                <fieldset class="form-group">
                                    <select name="search_rider" id="search_rider" class="form-control select2"
                                        multiple="multiple">
                                        @foreach ($rider_name as $admin)
                                            <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-4">
                                <fieldset class="form-group">
                                    <select name="search_hub[]" id="search_hub" class="form-control select2"
                                        multiple="multiple">
                                        @foreach ($hub_name as $hub)
                                            <option value="{{ $hub->id }}">{{ $hub->name }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-4">
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                                        <span
                                            class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                            <span class="la la-calendar-o"></span>
                                        </span>
                                    </div>
                                    <input type="text" name="from_date"
                                        class="form-control bg-primary border-primary white rounded-right" id="from_date"
                                        placeholder="Date (From)" data-value="{{$today}}">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                                        <span
                                            class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                            <span class="la la-calendar-o"></span>
                                        </span>
                                    </div>
                                    <input type="text" name="to_date"
                                        class="form-control bg-primary border-primary white rounded-right" id="to_date"
                                        placeholder="Date (To)" data-value="{{$today}}">
                                </div>
                            </div>

                            <div class="col-2 mt-2">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-outline-info btn-min-width"><i
                                            class="la la-search"></i> Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Delivery Note ID</th>
                            <th class="border-primary border-darken-1">Tracking No.</th>
                            <th class="border-primary border-darken-1">Rider Employee ID</th>
                            <th class="border-primary border-darken-1">Rider Name</th>
                            <th class="border-primary border-darken-1">Origin</th>
                            <th class="border-primary border-darken-1">Destination</th>
                            <th class="border-primary border-darken-1">Hub</th>
                            {{-- <th class="border-primary border-darken-1">Last Status</th>
                            <th class="border-primary border-darken-1">Last Status Date</th> --}}
                            <th class="border-primary border-darken-1">Current Status</th>
                            <th class="border-primary border-darken-1">Current Status Date</th>
                            <th class="border-primary border-darken-1">RCP Reason</th>
                            <th class="border-primary border-darken-1">RCP Count</th>
                            <th class="border-primary border-darken-1">OTP Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>


@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css') }}">


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
    </style>
@endsection

@section('js')
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/legacy.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/tags/tagging.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/datatable_buttons.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function() {

            var sevenDays = '{{ $sevenDays }}';
            var today = '{{ $today }}';
            var start_of_year = '{{ Carbon\Carbon::now()->subMonth(2) }}';
           
             var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                // min: new Date(thirtydays),
                max : new Date(today),
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
                    var contractMoment = moment(old_date_formatted);
                    var current = moment(contractMoment).add(7, 'days');
                    to_date.pickadate('picker').set('min', new Date(old_date_formatted),{muted:true});
                    to_date.pickadate('picker').set('max', new Date(current.toDate()),{muted:true});
                    to_date.pickadate('picker').set('select', new Date(current.toDate()),{muted:true});
                }
            });
            var to_date = $('#to_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max : new Date(today),
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#to_date_root').css('top', '40px');
                },
                onSet: function(context) {
                    // var current_date_formatted = $('input[name="to_date_formatted"]').val();
                    // from_date.pickadate('picker').set('max',new Date(current_date_formatted),{muted:true});
                }
            });

            $('#search_rider').select2({
                width: '100%',
                placeholder: "Select Rider",
                allowClear: true,
                dropdownParent: $('#search_form')
            });
            $('#search_hub').select2({
                width: '100%',
                placeholder: "Select Hub",
                allowClear: true,
                dropdownParent: $('#search_form')
            });


            $('#search_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                rules: {
                    // from_date: {
                    //     required: true
                    // },
                    // to_date: {
                    //     required: true
                    // }
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    table.draw(true);
                }
            });

            jQuery.fn.DataTable.Api.register('buttons.exportData()', function(options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.return.return_confirm_otp.list') }}',
                        data: params,
                        success: function(result) {
                            head = [];

                            head.push('S.No');
                            head.push('Delivery Note ID');
                            head.push('Tracking Number');
                            head.push('Rider Employee ID');
                            head.push('Rider Name');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Hub');
                            // head.push('Last Status');
                            // head.push('Last Status Date');
                            head.push('Current Status');
                            head.push('Current Status Date');
                            head.push('RCP Reason');
                            head.push('RCP Count');
                            head.push('OTP Status');

                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.delivery_note_id);
                                row.push(values.tracking_number_excel);
                                row.push(values.rider_employee_id);
                                row.push(values.rider_name);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.hubname);
                                // row.push(values.last_status);
                                // row.push(values.last_status_date);
                                row.push(values.current_status);
                                row.push(values.date);
                                row.push(values.reason);
                                row.push(values.rcp_count);
                                row.push(values.otp_entered);
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
                        title: 'Return Confirm OTP',
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
                autoWidth: false,
                pagingType: 'full_numbers',
                processing: true,
                deferLoading: 0,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.return.return_confirm_otp.list') }}',
                    data: function(d) {
                        d.rider = $('#search_rider').val();
                        d.hub = $('#search_hub').val();
                        d.search_date_from = $('input[name="from_date_formatted"]').val();
                        d.search_date_to = $('input[name="to_date_formatted"]').val();
                    }
                },
                order: [
                    [1, 'desc']
                ],
                columns: [{
                    orderable: false,searchable: false,name: 'serial_number',class: 'align-middle serial_number',targets: 0,
                    render: function(data, type, row) {
                        return '';
                    }},
                    {data: 'delivery_note_id', name: 'delivery_note_id', class: 'align-middle delivery_note_id'},
                    {data: 'tracking_number',name: 'shipments.tracking_number',class: 'align-middle tracking_number'},
                    {data: 'rider_employee_id', name: 'emp.trax_id',class: 'align-middle rider_employee_id'},
                    {data: 'rider_name',name: 'riders.name',class: 'align-middle delivery_note_id'},
                    {data: 'origin',name: 'cities.name',class: 'align-middle origin'},
                    {data: 'destination',name: 'destinationcity.name',class: 'align-middle destination'},
                    {data: 'hubname',name: 'hub.name',class: 'align-middle hub'},
                    // {data: 'last_status',class: 'align-middle last_status'},
                    // {data: 'last_status_date',name: 'ls.updated_at',class: 'align-middle date'},
                    {data: 'current_status',class: 'align-middle current_status'},
                    {data: 'date',name: 'sj.updated_at',class: 'align-middle date'},
                    {data: 'reason',name: 'ssr.name',class: 'align-middle date'},
                    {data: 'rcp_count',class: 'align-middle date'},
                    {data: 'otp_entered',name: 'otp_entered',class: 'align-middle otp_entered'},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>')
                        .appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var current_status = '<select name="current_status" id="current_status" class="select2 form-control"></select>';
                    var last_status = '<select name="last_status" id="last_status" class="select2 form-control"></select>';

                    var otp_entered =
                        '<select name="otp_entered" id="otp_entered" class="select2 form-control">' +
                        '<option value="0">No</option>' +
                        '<option value="1">Yes</option>' +
                        '</select>';


                    var input =
                        '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon =
                        '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.otp')) {
                            $(td).appendTo($(search));
                        } else if ($(header).is('.otp_entered')) {
                            $(otp_entered).appendTo($(search))
                                .on('change', function() {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        } else if ($(header).is('.current_status')) {
                            $(current_status).appendTo($(search))
                                .on('change', function() {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        }
                        else if ($(header).is('.last_status')) {
                            $(last_status).appendTo($(search))
                                .on('change', function() {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        } 
                         else {
                            var current = $(input).appendTo($(search)).on('change', function() {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });
                    $("#otp_entered").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    var data2 = $.map({!! $shipment_status !!}, function(obj) {
                        obj.text = obj.name;
                        return obj;
                    });

                    $('#current_status').prepend('<option value="" selected></option>').select2({
                        data: data2,
                        placeholder: "Select Shipment Status",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $('#last_status').prepend('<option value="" selected></option>').select2({
                        data: data2,
                        placeholder: "Select Shipment Status",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });


                    this.api().table().columns.adjust();
                }
            });
        });
    </script>
@endsection
