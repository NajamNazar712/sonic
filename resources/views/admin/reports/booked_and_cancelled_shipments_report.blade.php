@extends('admin.layout.master')

@section('title', 'Booked And Cancelled Shipments Report')

@section('content')
    <h1 class="mb-1">
        Booked And Cancelled Shipments Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div class="row mb-2 justify-content-center">
                    <form id="track_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                        <div class="col-4 mb-1">
                                <input type="text" name="tracking_numbers" class="tracking_numbers" placeholder="Tracking Number(s)*" data-tags-input-name="tracking_number" data-rule-required="true" data-msg-required="Tracking Number is required">
                            </div>
                    <div class="col-4 mb-1">
                        <fieldset class="form-group">
                            <select name="search_shipper" id="search_shipper" class="form-control select2">
                                @foreach($shippers as $shipper)
                                    <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4 mb-1">
                        <fieldset class="form-group">
                            <select name="search_shippimg_modes" id="search_shipping_modes" class="form-control select2">
                                @foreach($shipping_modes as $shipping_mode)
                                    <option value="{{$shipping_mode->id}}">{{$shipping_mode->mode}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_service_type" id="search_service_type" class="select2 form-control">
                                @foreach($service_types as $service_type)
                                    <option value="{{$service_type->id}}">{{$service_type->booking_type}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4 mt-1 mb-1">
                        <fieldset class="form-group">
                            <select name="search_status" id="search_status" class="form-control select2">
                                @foreach($statuses as $status)
                                    <option value="{{$status->id}}">{{$status->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4 mb-1">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>
                            <input type="text" name="search_date_from" class="form-control bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Search Date (From)" title="Search Date (From)" data-value="{{ Carbon\Carbon::today() }}">
                        </div>
                    </div>
                        <div class="col-4 mb-1">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>
                            <input type="text" name="search_date_to" class="form-control bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Search Date (To)" title="Search Date (To)" data-value="{{ Carbon\Carbon::today() }}">
                        </div>
                    </div>
                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Service Type</th>
                        <th class="border-primary border-darken-1">Shipping Mode</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Remarks</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Item Quantity</th>
                    </tr>
                    </thead>
                </table>
                </form>
            </div>
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
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    {{--<script src="{{asset('js/main-1.0.js')}}" type="text/javascript"></script>--}}

    <script type="text/javascript">
        $(document).ready(function () {
            // $('#search_form #search_tracking_no').inputmask({
            //     'alias': 'integer',
            //     'allowMinus': false,
            //     'allowPlus': false
            // });
            $('#search_service_type').prepend('<option value="" selected="selected""></option>').select2({
                width:'100%',
                placeholder:"Select Service Type",
                allowClear:true
            });
            $('#search_status').prepend('<option value="" selected="selected""></option>').select2({
                width:'100%',
                placeholder:"Select Status",
                allowClear:true
            });
            $('#search_shipping_modes').prepend('<option value="" selected="selected""></option>').select2({
                width:'100%',
                placeholder:"Select Shipping Mode",
                allowClear:true
            });
            $('#search_shipper').prepend('<option value="" selected="selected""></option>').select2({
                width:'100%',
                placeholder:"Select Shipper",
                allowClear:true
            });
            $('#search_date_from').pickadate({
                firstDay: 1,
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function (context) {
                    if (context.select) {
                        $('#search_form #search_date_to').pickadate('picker').set('min', $('#search_form #search_date_from').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_date_to').pickadate({
                firstDay: 1,
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function (context) {
                    if (context.select) {
                        $('#search_form #search_date_from').pickadate('picker').set('max', $('#search_form #search_date_to').pickadate('picker').get('select'));
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
                        url: '{{ route('admin.reports.booked_and_cancelled.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S. No.');
                            head.push('Tracking Number');
                            head.push('Shipper');
                            head.push('Service Type');
                            head.push('Shipping Mode');
                            head.push('Status');
                            head.push('Remarks');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Item Quantity');
                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.shipper);
                                row.push(values.service_type);
                                row.push(values.shipping_mode);
                                row.push(values.status);
                                row.push(values.remarks);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.item_quantity);
                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header:head};
                }
            });
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Booked And Cancelled Shipments Report',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                "autoWidth": false,
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                deferLoading: 0,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,ajax: {
                    url: '{{ route('admin.reports.booked_and_cancelled.list') }}',
                    data: function (d) {
                        d.tracking_numbers = $('#track_form .tracking_numbers').val();
                        d.search_shipping_mode = $('#search_shipping_modes').val();
                        d.search_service_type = $('#search_service_type').val();
                        d.search_status = $('#search_status').val();
                        d.search_shipper = $('#search_shipper').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                order: [[1, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number', name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
                    { data:'shipper' ,name: 'u.name', class: 'align-middle origin'},
                    { data:'service_type' ,name: 'bt.booking_type', class: 'align-middle origin'},
                    { data:'shipping_mode' ,name: 'sm.mode', class: 'align-middle origin'},
                    { data:'status' ,name: 'ss.name', class: 'align-middle origin'},
                    { data:'remarks' ,name: 'sj.remarks', class: 'align-middle origin'},
                    { data:'origin' ,name: 'oc.id', class: 'align-middle origin'},
                    { data:'destination' ,name: 'oc.id', class: 'align-middle origin'},
                    { data:'item_quantity' ,name: 'si.quantity', class: 'align-middle origin'},

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
            //Selectize
            var select = $('#track_form .tracking_numbers').selectize({
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
                    if (input.length >= 6 && Math.floor(input) == input && $.isNumeric(input)) {
                        return {
                            value: input,
                            text: input
                        }
                    }
                    else {
                        return false;
                    }
                },
            });
            $('#track_form').bind('submit',function (e) {
                e.preventDefault();

                table.draw();
            });

        });
    </script>
@endsection