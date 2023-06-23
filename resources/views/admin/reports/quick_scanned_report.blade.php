@extends('admin.layout.master')

@section('title', 'Quick Scanned Report')

@section('content')
    <h1 class="mb-1">
        Quick Scanned Report
    </h1>
    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('client.inc.messages')

                    <form id="track_form" class="justify-content-center m-2"  novalidate="novalidate">
                       <div class="row">
                        <div class="col-3">
                            <div class="form-group">
                                <input type="text" name="tracking_numbers" class="tracking_numbers" placeholder="Tracking Number(s)*"  id="tracking_numbers" data-tags-input-name="tracking_number" data-rule-required="true" data-msg-required="Tracking Number is required">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <select name="rider" id="rider" class="form-control select2" >
                                    @foreach($riders as $rider)
                                        <option value="{{$rider->id}}">{{$rider->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                       <div class="col-3">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o small-calender-icon"></span>
                                            </span>
                                </div>
                                <input type="text" name="search_date_from"  class="form-control bg-primary border-primary white rounded-right pickadate" id="search_date_from" placeholder="From" data-value="{{ Carbon\Carbon::now()->subDays(30) }}">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                            <span class="la la-calendar-o small-calender-icon"></span>
                                        </span>
                                </div>
                                <input type="text" name="search_date_to" class="form-control bg-primary border-primary white rounded-right pickadate" id="search_date_to" placeholder="To" data-value="{{ Carbon\Carbon::today() }}">
                            </div>
                        </div> 
                        <div class="col-12 d-flex justify-content-center">
                            <div class="form-group">
                                <button type="submit" id="search_filter_btn" class="btn btn-outline-primary btn-min-width search">
                                    <i class="la la-search"></i> Search
                                </button>
                            </div>
                        </div>
                       </div>
                    </form>

                <table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Tracking No.</th> 
                            <th class="border-primary border-darken-1">Origin</th>
                            <th class="border-primary border-darken-1">Destination</th>
                            <th class="border-primary border-darken-1">Status</th>
                            <th class="border-primary border-darken-1">Shipper</th>
                            <th class="border-primary border-darken-1">Arrival Date</th>
                            <th class="border-primary border-darken-1">Status Date Time</th>
                            <th class="border-primary border-darken-1">Status By</th>
                            <th class="border-primary border-darken-1">Last Scanned Location</th>
                            <th class="border-primary border-darken-1">Last Scanned City</th>
                            <th class="border-primary border-darken-1">Last Scanned By</th>
                            <th class="border-primary border-darken-1">Last Scanned At</th>
                        </tr>
                    </thead>
                </table>

                <hr class="mt-2 mb-2">
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
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

        .selectize-control {
            width: 100%;
        }
     
        .tracking_numbers{
            width: 100% !important;
        }


        /* .selectize-control .selectize-input {
             vertical-align: middle;
         }

         .selectize-control .selectize-input .item {
             word-break: break-all;
         }*/
    </style>

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    {{-- <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script> --}}
    <script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pagination/moment.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('#track_form #rider').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Search Rider',
                allowClear:true
            });

            var search_date_from = $('#track_form #search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#track_form #search_date_to').pickadate('picker').set('min', $('#track_form #search_date_from').pickadate('picker').get('select'));
                    }
                }
            });

            var search_date_to = $('#track_form #search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#track_form #search_date_from').pickadate('picker').set('max', $('#track_form #search_date_from').pickadate('picker').get('select'));
                    }
                }
            });

            var select = $('#track_form .tracking_numbers').selectize({
                placeholder: 'Tracking Number(s)*',
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
                    if (input.length >= 12 && Math.floor(input) == input && $.isNumeric(input)) {
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

             jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                    if ( this.context.length ) {
                        blockPagePermanently();
                        body = [];
                        var params = table.ajax.params();
                        params.start = 0;
                        params.length = -1;
                        params.excel = true;
                        var jsonResult = $.ajax({
                            url: '{{ route('admin.reports.quick_scanned_report.list')}}',
                            data: params,
                            success: function (result)
                            {
                                head = [];
                                head.push('S.No');
                                head.push('Tracking No.');
                                head.push('Origin');
                                head.push('Destination');
                                head.push('Status');
                                head.push('Shipper Name');
                                head.push('Arrival Date');
                                head.push('Status Date Time');
                                head.push('Status By');
                                head.push('Last Scanned Location');
                                head.push('Last Scanned City');
                                head.push('Last Scanned By');
                                head.push('Last Scanned At');
                                $.each(result.data, function(index, values) {
                                    row = [];
                                    row.push(index + 1);
                                    row.push(values.tracking_number);
                                    row.push(values.origin);
                                    row.push(values.destination);
                                    row.push(values.status);
                                    row.push(values.shipper_name);
                                    row.push(values.arrival_date);
                                    row.push(values.status_date_time);
                                    row.push(values.account_type);
                                    row.push(values.last_scanned_location);
                                    row.push(values.last_scanned_city);
                                    row.push(values.last_scanned_by);
                                    row.push(values.last_scanned_at);
                                    body.push(row);
                                });
                            },
                            async: false
                        });
                        UnblockPagePermanently();

                        return {body: body, header: head};
                    }
                });
            
                var table = $('#datatable').DataTable({
                    scrollX: true, scrollY: '500px',
                    dom: '<"d-inline-block"l><"pull-right"B>tipr',
                    buttons: [
                        {
                            extend: 'excel',
                            title: 'Quick Scanned Report',
                            className: 'btn btn-primary',
                            text: '<i class="la la-file-excel-o "></i> Excel',
                        },
                    ],
                    lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                    pageLength: 50,
                    pagingType: 'full_numbers',
                    processing: false,
                    deferLoading: 0,
                    language: {
                        processing: data_table_loader
                    },
                    serverSide: true,
                    ajax: {
                        url: '{{ route('admin.reports.quick_scanned_report.list') }}',
                        data: function (d) {
                            d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                            d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                            d.rider = $('select[name="rider"]').val();
                            d.tracking_numbers = $('#tracking_numbers').val();
                        }
                    },
                    rowId: 'id',
                    order: [[12, 'asc']],
                    columns: [
                        {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                        {data: 'tracking_number_hyperlink', name: 'shipments.tracking_number', class: 'align-middle text_center tracking_number'},
                        {data: 'origin', name: 'oc.name', class: 'align-middle text_center origin '},
                        {data: 'destination', name: 'dc.name', class: 'align-middle text_center destination '},
                        {data: 'status', name: 'ss.name', class: 'align-middle text_center shipment_status'},
                        {data: 'shipper_name', name: 'u.name', class: 'text_center align-middle shipper_name'},
                        {data: 'arrival_date', name: 'sj.created_at', class: 'text_center align-middle arrival_date'},
                        {data: 'status_date_time', name: 'sjl.created_at', class: 'text_center align-middle status_date_time'},
                        {data: 'last_status_by', name: 'last_status_by', class: 'text_center align-middle last_status_by', orderable: false, searchable: false},
                        {data: 'last_scanned_location', name: 'sssl.name', class: 'text_center align-middle last_scanned_location'},
                        {data: 'last_scanned_city', name: 'last_scanned_city', class: 'text_center align-middle last_scanned_city', orderable: false, searchable: false},
                        {data: 'last_scanned_by', name: 'last_scanned_by', class: 'text_center align-middle last_scanned_by', orderable: false, searchable: false},
                        {data: 'last_scanned_at', name: 'ssj.created_at', class: 'text_center align-middle last_scanned_at'},
                    ],
                    rowCallback: function(row, data, index) {
                        var info = table.page.info();
                        $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                    },
                    initComplete: function() {
                        this.api().table().columns.adjust();
                    }
                });

                $('#track_form').bind('submit', function (e) {
                    e.preventDefault();
                    var tracking_numbers = $('#track_form .tracking_numbers').val();
                    var search_date_from = $('#track_form #search_date_from').val();
                    var search_date_to = $('#track_form #search_date_to').val();
                    var rider = $('#track_form #rider').val();
                    if (tracking_numbers != '' || (search_date_from != '' && search_date_to != '') || rider != '') {
                        table.draw();
                    }
                });

        });
    </script>
@endsection
