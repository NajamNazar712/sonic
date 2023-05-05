@extends('admin.layout.master')

@section('title', 'Activity Trail Log')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Activity Trail Log
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('client.inc.messages')

                            <div id="search_form" class="row mb-2 justify-content-center">
                                <div class="col-4">
                                    <div class="form-group input-group">
                                        <div class="input-group-prepend">
			                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
			                                    <span class="la la-calendar-o"></span>
			                                </span>
                                        </div>

                                        <input type="text" name="search_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_from" placeholder="Date (From)" data-value="{{Carbon\Carbon::now()->subDays(30)}}">
                                    </div>
                                </div>

                                <div class="col-4">
                                    <div class="form-group input-group">
                                        <div class="input-group-prepend">
			                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
			                                    <span class="la la-calendar-o"></span>
			                                </span>
                                        </div>

                                        <input type="text" name="search_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_to" placeholder="Date (To)" data-value="{{ Carbon\Carbon::today() }}">
                                    </div>
                                </div>

                                <div class="col-2">
                                    <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                                </div>
                            </div>

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                    <tr class="bg-primary white">
                                        <th class="border-primary border-darken-1">S. No</th>
                                        <th class="border-primary border-darken-1">Team Member Name</th>
                                        <th class="border-primary border-darken-1">Team Member Area Name</th>
                                        <th class="border-primary border-darken-1">Designation</th>
                                        <th class="border-primary border-darken-1">Screen Name</th>
                                        <th class="border-primary border-darken-1">Action Performed</th>
                                        <th class="border-primary border-darken-1">Action Performed Time</th>
                                        <th class="border-primary border-darken-1">IP Address</th>
                                        <th class="border-primary border-darken-1">Latitude</th>
                                        <th class="border-primary border-darken-1">Longitude</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(function () {

            var from_date = $('#search_from').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_to').pickadate('picker').set('min', $('#search_form #search_from').pickadate('picker').get('select'));
                    }
                }
            });
            var to_date = $('#search_to').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_from').pickadate('picker').set('max', $('#search_form #search_to').pickadate('picker').get('select'));
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
                        url: '{{ route('admin.activity_trail.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            footer = [];

                            head.push('S. No');
                            head.push('Team Member Name');
                            head.push('Area');
                            head.push('Designation');
                            head.push('Screen Name');
                            head.push('Action Performed');
                            head.push('Action Performed Time');
                            head.push('IP Address');
                            head.push('Latitude');
                            head.push('Longitude');
                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.name);
                                row.push(values.city_area);
                                row.push(values.designation);
                                row.push(values.screen_name);
                                row.push(values.action);
                                row.push(values.created_at);
                                row.push(values.ip_address);
                                row.push(values.latitude);
                                row.push(values.longitude);
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
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                order: [[ 4, "desc" ]],
                scrollX: false, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        className: 'btn btn-primary',
                        title: 'Activity Trail Log',
                        text: '<i class="la la-file-excel-o"></i> Excel',

                    },
                    'reset',
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                language: {
                    processing: data_table_loader
                },
                ajax:{
                    url: '{{ route('admin.activity_trail.list') }}',
                    data: function (d) {
                        d.search_from = $('input[name="search_from_formatted"]').val();
                        d.search_to = $('input[name="search_to_formatted"]').val();
                    }
                },
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'name', name: 'a.name', class: 'align-middle name'},
                    {data: 'city_area', name: 'ca.name', class: 'align-middle city_area'},
                    {data: 'designation', name: 'ed.name', class: 'align-middle designation'},
                    {data: 'screen_name', name: 'ata.screen_name', class: 'align-middle screen'},
                    {data: 'action', name: 'ata.action', class: 'align-middle action'},
                    {data: 'created_at', name: 'activity_trail_logs.created_at', class: 'align-middle created_at'},
                    {data: 'ip_address', name: 'activity_trail_logs.ip_address', class: 'align-middle ip_address'},
                    {data: 'latitude', name: 'activity_trail_logs.latitude', class: 'align-middle latitude'},
                    {data: 'longitude', name: 'activity_trail_logs.longitude', class: 'align-middle longitude'},
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

                    this.api().table().columns.adjust();
                }
            });
            $('#search_filter_btn').on('click', function() {
                table.draw();
            });
        });

    </script>

@endsection

