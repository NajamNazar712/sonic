
@extends('admin.layout.master')
@section('title','Debriefing Agent Report')

@section('content')
    <h1 class="mb-1">
        Debriefing Agent Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div id="search_form" class="row mb-2 justify-content-center">
                    <div class="col-3 ">

                        <div class="form-group input-group ml-1">
                            <div class="input-group-prepend">
                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                            <span class="la la-calendar-o"></span>
                        </span>
                            </div>

                            <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Date (From)" title="Date (From)" data-value="{{ Carbon\Carbon::today() }}">
                        </div>
                    </div>
                    <div class="col-3 ">
                        <div class="form-group input-group ml-1">
                            <div class="input-group-prepend">
                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                            <span class="la la-calendar-o"></span>
                        </span>
                            </div>

                            <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Date (To)" title="Date (To)" data-value="{{ Carbon\Carbon::today() }}">
                        </div>

                    </div>
                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;width: 100%;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">Date</th>
                        <th class="border-primary border-darken-1">Agent Name</th>
                        <th class="border-primary border-darken-1">Live Hours</th>
                        <th class="border-primary border-darken-1">Break Hours</th>
                        <th class="border-primary border-darken-1">Assigned Calls</th>
                        <th class="border-primary border-darken-1">Completed Calls</th>

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
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            $('#search_form #search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_to').pickadate('picker').set('min', $('#search_form #search_date_from').pickadate('picker').get('select'));
                    }
                }
            });

            $('#search_form #search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_from').pickadate('picker').set('max', $('#search_form #search_date_to').pickadate('picker').get('select'));
                    }
                }
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{route('admin.reports.debriefing.agent_list')}}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('Date');
                            head.push('Agent Name');
                            head.push('Live Hours');
                            head.push('Break Hours');
                            head.push('Assigned Calls');
                            head.push('Completed Calls');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(values.date);
                                row.push(values.agent_name);
                                row.push(values.live_hours);
                                row.push(values.break_hours);
                                row.push(values.assigned_calls_excel);
                                row.push(values.completed_calls_excel);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Debriefing Agent Report',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                deferLoading: 0,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.debriefing.agent_list') }}',
                    data: function (d) {
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                order: [[0, 'desc']],
                columns: [
                    { data:'date' ,name: 'agent_days.date', class: 'align-middle date text-center'},
                    { data:'agent_name' ,name: 'agent.name', class: 'align-middle agent_name text-center'},
                    { data:'live_hours' ,name: 'live_hours', class: 'align-middle live_hours text-center',orderable: false, searchable: false},
                    { data:'break_hours' ,name: 'break_hours', class: 'align-middle break_hours text-center',orderable: false, searchable: false},
                    { data:'assigned_calls' ,name: 'assigned_calls', class: 'align-middle assigned_calls text-center',orderable: false, searchable: false},
                    { data:'completed_calls' ,name: 'completed_calls', class: 'align-middle completed_calls text-center',orderable: false, searchable: false},
                ],
                drawCallback: function (settings) {
                    var api = new $.fn.dataTable.Api( settings );
                    var data = api.rows( {page:'current'} ).data();

                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });

            $('#search_filter_btn').on('click',function () {
                table.draw();
            });
        });
    </script>
@endsection