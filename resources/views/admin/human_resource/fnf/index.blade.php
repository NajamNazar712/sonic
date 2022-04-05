@extends('admin.layout.master')

@section('title', 'FNF Dashboard')

@section('content')
    <h1>FNF Dashboard</h1>

    <section>
        <div class="row">

            <div class="col-12">
                <div class="card">
                    @include('admin.inc.messages')
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            {{--<div class="row justify-content-center">
                                <div class="col-3">
                                    <select name="search_status" id="search_status" class="form-control select2">
                                        @foreach($erf_status as $status)
                                            <option value="{{$status->id}}">{{$status->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>--}}

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No</th>
                                    <th class="border-primary border-darken-1">FNF ID</th>
                                    <th class="border-primary border-darken-1">Trax Id</th>
                                    <th class="border-primary border-darken-1">Name</th>
                                    <th class="border-primary border-darken-1">City</th>
                                    <th class="border-primary border-darken-1">Designation</th>
                                    <th class="border-primary border-darken-1">Department</th>
                                    <th class="border-primary border-darken-1">Line Manager</th>
                                    <th class="border-primary border-darken-1">HOD</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Joining Date</th>
                                    <th class="border-primary border-darken-1">Resign Date</th>
                                    <th class="border-primary border-darken-1">Requested Date</th>
                                    <th class="border-primary border-darken-1">Created By</th>
                                    <th class="border-primary border-darken-1"></th>

                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('css')
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
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script>

        $(document).ready(function() {

            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.human_resource.fnf.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('FNF ID');
                            head.push('Trax Id');
                            head.push('Employee Name');
                            head.push('City');
                            head.push('Designation');
                            head.push('Department');
                            head.push('Line Manager');
                            head.push('HOD');
                            head.push('Status');
                            head.push('Joining Date');
                            head.push('Resign Date');
                            head.push('Requested Date');
                            head.push('Requested By');

                            $.each(result.data, function (index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.fnf_id);
                                row.push(values.trax_id);
                                row.push(values.employee_name);
                                row.push(values.city);
                                row.push(values.designation);
                                row.push(values.department);
                                row.push(values.line_manager);
                                row.push(values.hod);
                                row.push(values.status);
                                row.push(values.joining_date);
                                row.push(values.resign_date);
                                row.push(values.created_at);
                                row.push(values.created_by);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });

            var route = '<?php echo route('admin.human_resource.fnf.add'); ?>';
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                        @if(session('role_id') == 1 ||  in_array(569, session('permissions')))
                    {
                        title: 'Add FNF',
                        className: 'btn btn-primary',
                        text: '<i class="la la-plus"></i> Add FNF',
                        action: function (e) {
                            window.location = route;

                        }
                    },
                        @endif
                    {
                        extend: 'excel',
                        title: 'FNF List',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'
                ],
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.human_resource.fnf.list') }}',
                    data: function (d) {
                        d.search_status = $('#search_status').val();
                    }
                },
                rowId: 'shId',
                order: [[12, 'desc']],
                columns: [
                    {
                        orderable: false,
                        searchable: false,
                        name: 'serial_number',
                        class: 'align-middle serial_number',
                        targets: 0,
                        render: function (data, type, row) {
                            return '';
                        }
                    },

                    {data: 'fnf_id', name: 'fnf.id', class: 'align-middle fnf_id'},
                    {data: 'trax_id', name: 'trax_id', class: 'align-middle trax_id'},
                    {data: 'employee_name', name: 'employees.name', class: 'align-middle employee_name'},
                    {data: 'city', name: 'c.name', class: 'align-middle city'},
                    {data: 'designation', name: 'ed.name', class: 'align-middle designation'},
                    {data: 'department', name: 'd.name', class: 'align-middle department'},
                    {data: 'line_manager', name: 'a.name', class: 'align-middle line_manager'},
                    {data: 'hod', name: 'ah.name', class: 'align-middle hod'},
                    {data: 'status', name: 'fs.name', class: 'align-middle status'},
                    {data: 'joining_date', name: 'joining_date', class: 'align-middle joining_date'},
                    {data: 'resign_date', name: 'resign_date', class: 'align-middle resign_date'},
                    {data: 'created_at', name: 'created_at', class: 'align-middle created_at'},
                    {data: 'created_by', name: 'h.name', class: 'align-middle created_by'},
                    {data: 'actions', name: 'actions', class: 'align-middle actions'},


                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                },
                initComplete: function () {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';
                    var mode_drop_select = '<select name="mode_select" id="mode_select" class="select2 form-control">' +
                        '</select>';
                    var service_drop_select = '<select name="service_select" id="service_select" class="select2 form-control"></select>';
                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.action') || $(header).is('.serial_number')) {
                            $(td).appendTo($(search));
                        } else {
                            var current = $(input).appendTo($(search)).on('change', function () {
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



            $('#datatable tbody').on('click', '.rm_view', function () {
                var fnf_id = table.row($(this).parents('tr')).data().id;
                if ($(this).hasClass('rm_view')) {
                    var route = '{!! route('admin.human_resource.fnf.rm.index', ':id') !!}';
                    route = route.replace(':id', fnf_id);
                    window.location = route;
                }
            });

            $('#datatable tbody').on('click', '.cs_view', function () {
                var fnf_id = table.row($(this).parents('tr')).data().id;
                if ($(this).hasClass('cs_view')) {
                    var route = '{!! route('admin.human_resource.fnf.cs.index', ':id') !!}';
                    route = route.replace(':id', fnf_id);
                    window.location = route;
                }
            });

            $('#datatable tbody').on('click', '.admin_view', function () {
                var fnf_id = table.row($(this).parents('tr')).data().id;
                if ($(this).hasClass('admin_view')) {
                    var route = '{!! route('admin.human_resource.fnf.administration.index', ':id') !!}';
                    route = route.replace(':id', fnf_id);
                    window.location = route;
                }
            });

            $('#datatable tbody').on('click', '.it_view', function () {
                var fnf_id = table.row($(this).parents('tr')).data().id;
                if ($(this).hasClass('it_view')) {
                    var route = '{!! route('admin.human_resource.fnf.it_support.index', ':id') !!}';
                    route = route.replace(':id', fnf_id);
                    window.location = route;
                }
            });

            $('#datatable tbody').on('click', '.finance_view', function () {
                var fnf_id = table.row($(this).parents('tr')).data().id;
                if ($(this).hasClass('finance_view')) {
                    var route = '{!! route('admin.human_resource.fnf.finance.index', ':id') !!}';
                    route = route.replace(':id', fnf_id);
                    window.location = route;
                }
            });

            $('#datatable tbody').on('click', '.hod_view', function () {
                var fnf_id = table.row($(this).parents('tr')).data().id;
                if ($(this).hasClass('hod_view')) {
                    var route = '{!! route('admin.human_resource.fnf.hod_approval_index', ':id') !!}';
                    route = route.replace(':id', fnf_id);
                    window.location = route;
                }
            });

            $('#datatable tbody').on('click', '.hr_view', function () {
                var fnf_id = table.row($(this).parents('tr')).data().id;
                if ($(this).hasClass('hr_view')) {
                    var route = '{!! route('admin.human_resource.fnf.hr.index', ':id') !!}';
                    route = route.replace(':id', fnf_id);
                    window.location = route;
                }
            });

            $('#datatable tbody').on('click', '.hr_print', function () {
                var fnf_id = table.row($(this).parents('tr')).data().id;
                if ($(this).hasClass('hr_print')) {
                    var route = '{!! route('admin.human_resource.fnf.hr.print') !!}';
                    // route = route.replace(':id', fnf_id);
                    $.ajax({
                    url: route,
                    method: 'POST',
                    data: {
                        'id': fnf_id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function(data) {
                        var tab = window.open('', '_blank');
                        if(!tab) {
                            swal({
                                title: 'Popup Blocker Enabled!',
                                text: 'Please add this site to your exception list.',
                                icon: 'error',
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                        }
                        else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                });
                    
                }
            });

            $('#datatable tbody').on('click', '.update', function () {
                var fnf_id = table.row($(this).parents('tr')).data().id;
                if ($(this).hasClass('update')) {
                    var route = '{!! route('admin.human_resource.fnf.edit_fnf_request', ':id') !!}';
                    route = route.replace(':id', fnf_id);
                    window.location = route;
                }
            });

            $('#datatable tbody').on('click', '.history', function () {
                var fnf_id = table.row($(this).parents('tr')).data().id;
                if ($(this).hasClass('history')) {
                    var route = '{!! route('admin.human_resource.fnf.fnf_history_index', ':id') !!}';
                    route = route.replace(':id', fnf_id);
                    window.location = route;
                }
            });

        });

    </script>
@endsection