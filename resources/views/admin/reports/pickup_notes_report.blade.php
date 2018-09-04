@extends('admin.layout.master')

@section('content')
    <h1 class="mb-1">
        Pickup Notes Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center">

                    <div class="col-3">
                        <fieldset class="form-group">
                            <input type="text" class="form-control" name="search_pn_no" id="search_pn_no" placeholder="Search Pickup Note Number">
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class="form-group">
                            <select name="search_assigned_by" id="search_assigned_by" class="form-control select2">
                                @foreach($admins as $admin)
                                    <option value="{{$admin->id}}">{{$admin->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class="form-group">
                            <select name="search_rider" id="search_rider" class="form-control select2">
                                @foreach($riders as $rider)
                                    <option value="{{$rider->id}}">{{$rider->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class="form-group">
                            <select name="search_city" id="search_city" class="form-control select2">
                                @foreach($cities as $city)
                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class="form-group">
                            <select name="search_completed_by" id="search_completed_by" class="form-control select2">
                                @foreach($admins as $admin)
                                    <option value="{{$admin->id}}">{{$admin->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class="form-group">
                            <input type="text" name="completed_date" class="form-control bg-primary border-primary white rounded-right" id="completed_date" placeholder="Completion Date" data-value="">
                        </fieldset>
                    </div>
                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Pickup Note No.</th>
                        <th class="border-primary border-darken-1">City</th>
                        <th class="border-primary border-darken-1">No. Of Pickups</th>
                        <th class="border-primary border-darken-1">No. Of Shipments</th>
                        <th class="border-primary border-darken-1">Rider</th>
                        <th class="border-primary border-darken-1">Assigned Date</th>
                        <th class="border-primary border-darken-1">Assigned By</th>
                        <th class="border-primary border-darken-1">Completed By</th>
                        <th class="border-primary border-darken-1">Completed Date</th>
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
            border-color: #666EE8;
        }

        table.dataTable tbody tr.selected td.select-checkbox:after {
            top: 50%;
            text-shadow: none;
        }
        a.btn.btn-secondary {
            border-radius: 20px;
            background: #666ee8;
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
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#search_pn_no').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#search_completed_by').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Completed By',
                width:'100%',
                allowClear:true
            });
            $('#search_assigned_by').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Assigned By',
                width:'100%',
                allowClear:true
            });
            $('#search_rider').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Rider',
                width:'100%',
                allowClear:true
            });
            $('#search_city').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search City',
                width:'100%',
                allowClear:true
            });

            var completed_date = $('#completed_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#completed_date_root').css('top','40px');
                },
                onSet: function(context) {
                }
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.pickup_note.list') }}',
                        data: {
                                'page': 'all',
                                'search_pn_no': $('#search_pn_no').val(),
                                'search_rider': $('#search_rider').val(),
                                'search_assigned_by': $('#search_assigned_by').val(),
                                'search_completed_by': $('#search_completed_by').val(),
                                'search_city': $('#search_city').val(),
                                'search_completed_date': $('input[name="completed_date_formatted"]').val()
                        },
                        success: function (result) {
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.pn_id);
                                row.push(values.city);
                                row.push(values.pickups);
                                row.push(values.count);
                                row.push(values.rider);
                                row.push(values.assigned_date);
                                row.push(values.assigned_by);
                                row.push(values.completed_by);
                                row.push(values.completed_date);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: $("#datatable thead tr th").map(function() { return this.innerHTML; }).get()};
                }
            } );

            var index_column = 0;
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                    extend: 'excelHtml5',
                    title: 'Completed Pickup Notes Report',
                    text: '<i class="la la-file-excel-o"></i> Excel'
                    },
                ],
                fixedHeader: {
                    header: true,
                    headerOffset: $('.header-navbar').height()
                },
                lengthMenu: [[25, 50, 100], [25, 50, 100]],
                pageLength: 25,
                stateSave: true,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.pickup_note.list') }}',
                    data: function (d) {
                        d.search_pn_no = $('#search_pn_no').val();
                        d.search_rider = $('#search_rider').val();
                        d.search_assigned_by = $('#search_assigned_by').val();
                        d.search_completed_by = $('#search_completed_by').val();
                        d.search_city = $('#search_city').val();
                        d.search_completed_date = $('input[name="completed_date_formatted"]').val();
                    }
                },
                rowId: 'pn_id',
                order: [[1, 'asc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'pn_id', name: 'pickup_notes.id', class: 'align-middle pn_id'},
                    {data: 'city', name: 'cities.name', class: 'align-middle city'},
                    {data: 'pickups', name: 'pickup_notes.pickups', class: 'align-middle pickups'},
                    {data: 'count', name: 'pickup_notes.bookings', class: 'align-middle count'},
                    {data: 'rider', name: 'riders.name', class: 'align-middle rider'},
                    {data: 'assigned_date', name: 'pickup_notes.created_at', class: 'align-middle assigned_date'},
                    {data: 'assigned_by', name: 'ab.name', class: 'align-middle assigned_by'},
                    {data: 'completed_by', name: 'up.name', class: 'align-middle completed_by'},
                    {data: 'completed_date', name: 'pickup_notes.updated_at', class: 'align-middle completed_date'}
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

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.serial_number')) {
                            $(td).appendTo($(search));
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
                }
            });

            $('#search_filter_btn').on('click',function () {
                table.draw();
            });

        });

    </script>
@endsection