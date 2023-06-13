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
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o small-calender-icon"></span>
                                                </span>
                                    </div>
                                    <input type="text" name="search_date_from"  class="form-control bg-primary border-primary white rounded-right pickadate" id="search_date_from" placeholder="Report From">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o small-calender-icon"></span>
                                            </span>
                                    </div>
                                    <input type="text" name="search_date_to" class="form-control bg-primary border-primary white rounded-right pickadate" id="search_date_to" placeholder="Report To">
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
                               <div class="form-group">
                                   <button type="submit" id="search_filter_btn" class="btn btn-outline-primary btn-min-width search"><i class="la la-search"></i> Search</button>
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
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pagination/moment.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('#track_form #rider').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Search Rider',
                allowClear:true
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
                            head.push('City');
                            head.push('Hub');
                            head.push('Rider');
                            head.push('Pickup Note ID');
                            head.push('Pickup Request ID');
                            head.push('Shipper Name');
                            head.push('Vendor');
                            head.push('Address');
                            head.push('Status');
                            head.push('Created At');

                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.city);
                                row.push(values.hub);
                                row.push(values.rider);
                                row.push(values.note_id);
                                row.push(values.request_id);
                                row.push(values.shipper_name);
                                row.push(values.vendor);
                                row.push(values.address);
                                row.push(values.status);
                                row.push(values.created_at);
                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header: head};
                }
            } );
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

            var table = $('#datatable').DataTable({
                scrollX: false, scrollY: '500px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'App Efficiency Report',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o "></i> Excel',
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
                ajax: {
                    url: '{{ route('admin.reports.quick_scanned_report.list') }}',
                    data: function (d) {
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                        d.rider = $('select[name="rider"]').val();
                    }
                },
                rowId: 'id',
                order: [[1, 'asc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'city', name: 'c.name', class: 'align-middle text_center city '},
                    {data: 'hub', name: 'ci.name', class: 'align-middle text_center hub '},
                    {data: 'rider', name: 'r.name', class: 'align-middle text_center rider '},
                    {data: 'note_id', name: 'v2_rider_pickups.pickup_note_id', class: 'align-middle text_center note_id'},
                    {data: 'request_id', name: 'v2_rider_pickups.pickup_request_id', class: 'align-middle text_center request_id'},
                    {data: 'shipper_name', name: 'u.name', class: 'text_center align-middle shipper_name'},
                    {data: 'vendor', name: 'usi.vendor', class: 'text_center align-middle vendor'},
                    {data: 'address', name: 'usi.pickup_address', class: 'text_center align-middle address'},
                    {data: 'status', name: 'vprs.id', class: 'text_center align-middle status'},
                    {data: 'created_at', name: 'v2_rider_pickups.created_at', class: 'text_center align-middle created_at'},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());
                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="1">Requested</option>' +
                        '<option value="2">Picked</option>' +
                        '<option value="3">Not Picked</option>' +
                        '<option value="4">Cancelled</option>' +
                        '</select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.action') || $(header).is('.serial_number')) {
                            $(td).appendTo($(search));
                        }
                        else if($(header).is('.status')){
                            $(status_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
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
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });

            $('#track_form').bind('submit', function (e) {
                e.preventDefault();
                table.draw();
            });

        });
    </script>
@endsection
