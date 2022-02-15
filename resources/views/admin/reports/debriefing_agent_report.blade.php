
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


                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;width: 100%;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">Date</th>
                        <th class="border-primary border-darken-1">Agent Name</th>
                        <th class="border-primary border-darken-1">Live Hours</th>
                        <th class="border-primary border-darken-1">Break Hours</th>
                        <th class="border-primary border-darken-1">Assigned Calls</th>
                        <th class="border-primary border-darken-1">Completed Calls</th>
                        <th class="border-primary border-darken-1">Pending Calls</th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>

@endsection

@section('css')
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

    <script type="text/javascript">
        $(document).ready(function () {

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
                            head.push('Pending Calls');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(values.date);
                                row.push(values.agent_name);
                                row.push(values.live_hours);
                                row.push(values.break_hours);
                                row.push(values.assigned_calls_excel);
                                row.push(values.completed_calls_excel);
                                row.push(values.pending_calls_excel);
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
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.debriefing.agent_list') }}',
                },
                order: [[0, 'desc']],
                columns: [
                    { data:'date' ,name: 'agent_days.date', class: 'align-middle date text-center'},
                    { data:'agent_name' ,name: 'agent.name', class: 'align-middle agent_name text-center'},
                    { data:'live_hours' ,name: 'live_hours', class: 'align-middle live_hours text-center',orderable: false, searchable: false},
                    { data:'break_hours' ,name: 'break_hours', class: 'align-middle break_hours text-center',orderable: false, searchable: false},
                    { data:'assigned_calls' ,name: 'assigned_calls', class: 'align-middle assigned_calls text-center',orderable: false, searchable: false},
                    { data:'completed_calls' ,name: 'completed_calls', class: 'align-middle completed_calls text-center',orderable: false, searchable: false},
                    { data:'pending_calls' ,name: 'pending_calls', class: 'align-middle pending_calls text-center',orderable: false, searchable: false},
                ],
                drawCallback: function (settings) {
                    var api = new $.fn.dataTable.Api( settings );
                    var data = api.rows( {page:'current'} ).data();

                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });
        });
    </script>
@endsection