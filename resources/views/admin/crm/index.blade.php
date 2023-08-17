@extends('admin.layout.master')

@section('title', 'CRM Permissions')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    CRM Permissions
                </h1>

                <div class="card">

                    <div class="card-content" aria-expanded="true">
                        <div class="card-body card-dashboard">
                            @include('admin.inc.messages')
                            <div id="search_form" class="row p-1 mb-2">

                                <div class="col-4">
                                    <fieldset class="form-group">
                                        <input type="text" name="search_Permission" id="search_Permission"
                                            class="form-control cnic" placeholder="Permission">
                                    </fieldset>
                                </div>
                                <div class="col-2">
                                    <button type="button" id="search_filter_btn"
                                        class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i>
                                        Search</button>
                                </div>
                            </div>

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                    <tr role="row" class="bg-primary white">
                                        <th class="border-primary border-darken-1">S. No.</th>
                                        <th class="border-primary border-darken-1">Designation</th>
                                        <th class="border-primary border-darken-1">Department</th>
                                        <th class="border-primary border-darken-1">Created Datetime</th>
                                        <th class="border-primary border-darken-1">Updated Datetime</th>
                                        <th class="border-primary border-darken-1">Updated by</th>
                                        <th class="border-primary border-darken-1"></th>
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
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">

    <style>
        #search_filter_btn {
            margin-left: 300px;
        }
    </style>
@endsection


<style>
        table.dataTable tbody tr td.select-checkbox:before {
            top: 50%;
            border-color: #64a0d2;
        }

        table.dataTable tbody tr.selected td.select-checkbox:after {
            top: 50%;
            text-shadow: none;
        }
</style>

@section('js')
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/datatable_buttons.js') }}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            var selected_rows = [];

            var table = $('#datatable').DataTable({
                scrollX: true,
                scrollY: '500px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons:[
                        @if (session('role_id') == 1 || in_array(789, session('permissions')))	
                        {
                            text: 'Search Role Permissions',
                            className: 'btn btn-primary add',
                            action: function (e, dt, node, config) {
                                window.location = '{{ route('admin.crm_permission.index') }}';
                            }
                        },
                        @endif
                    
                        @if (session('role_id') == 1 || in_array(775, session('permissions')))
                        {
                            text: 'Bulk Add',
                            className: 'btn btn-primary bulk_add',
                            enabled: false,
                            action: function (e, dt, node, config) {
                                var redirect = '{!! route('admin.crm_permission.bulk_add', ':id') !!}';
                                var url = redirect.replace(':id', selected_rows);
                                window.location = url;
                                selected_rows = [];
                            }
                        },
                        @endif
                        @if (session('role_id') == 1 || in_array(776, session('permissions')))
                        
                        {
                            text: 'Bulk Remove',
                            className: 'btn btn-primary remove',
                            enabled: false,
                            action: function (e, dt, node, config) {
                                var redirect = '{!! route('admin.crm_permission.bulk_remove', ':id') !!}';
                                var url = redirect.replace(':id', selected_rows);
                                window.location = url;
                                selected_rows = [];

                            }
                        },
                        @endif
                        @if (session('role_id') == 1 || count(array_intersect([775, 776], session('permissions'))) !== 0)
                        {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.select();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index === -1) {
                                        selected_rows.push(id);
                                    }

                                    table.button('.bulk_add').enable();
                                    table.button('.remove').enable();
                                    
                                }
                            });
                        }
                    }, {
                        extend: 'selectNone',
                        text: 'Select None',
                        className: 'select_none',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.deselect();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index !== -1) {
                                        selected_rows.splice(index, 1);
                                    }

                                    if (selected_rows.length == 0) {
                                        table.button('.bulk_add').disable();
                                        table.button('.remove').disable();

                                    }
                                }
                            });
                        }
                    },
                    @endif  
                    'reset'
                    ],
                select: {
					info: false,
					style: 'multi',
					selector: 'td.select-checkbox',
					className: 'selected bg-primary bg-lighten-5 primary'
            	},
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                language: {
                    processing: data_table_loader
                },
                ajax: '{{ route('admin.crm.list') }}',
                rowId: 'id',
                order: [
                    [3, 'desc']
                ],
                columns: [{
                        data: 'serial_number',
                        orderable: false,
                        searchable: false,
                        name: 'serial_number',
                        class: 'align-middle serial_number',
                        targets: 1,
                        render: function(data, type, row) {
                            return '';
                        }
                    },
                    {
                        data: 'name',
                        name: 'admin_roles.name',
                        class: 'align-middle name'
                    },
                    {
                        data: 'department',
                        name: 'ad.id',
                        class: 'align-middle department'
                    },
                    {
                        data: 'created_at',
                        name: 'admin_roles.created_at',
                        class: 'align-middle created_at'
                    },
                    {
                        data: 'updated_at',
                        name: 'admin_roles.updated_at',
                        class: 'align-middle updated_at'
                    },
                    {
                        data: 'updated_by',
                        name: 'a.name',
                        class: 'align-middle updated_by'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        class: 'text-center align-middle action p-1',
                        orderable: false,
                        searchable: false
                    }
                ],
                rowCallback: function(row, data, index) {
					var info = table.page.info();
					$('td:eq(0)', row).addClass('select-checkbox');
					if ($.inArray(data.id, selected_rows) !== -1) {
						table.row(row).select();
					}
					// $('td:eq(0)', row).html(index + 1 + info.page * info.length);
				},
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>')
                        .appendTo(this.api().table().header());

                    var td =
                        '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input =
                        '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon =
                        '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var departments_select =
                        '<select name="departments_select" id="departments_select" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        } else if ($(header).is('.department')) {
                            $(departments_select).appendTo($(search))
                                .on('change', function() {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        } else {
                            var current = $(input).appendTo($(search)).on('change', function() {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });

                    var data1 = $.map({!! $departments !!}, function(obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                    var data1 = $.map({!! $departments !!}, function(obj) {
                        obj.text = obj.name;

                        return obj;
                    });

                    $("#departments_select").prepend('<option value="" selected></option>').select2({
                        data: data1,
                        placeholder: "Select Department",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    this.api().table().columns.adjust();
                }
            });


            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }
                if (selected_rows.length > 0) {
                    table.button('.bulk_add').enable();
                    table.button('.remove').enable();
                }
                else {
                    table.button('.bulk_add').disable();
                    table.button('.remove').disable();
                }
				
            });

            @if (session('role_id') == 1 || session('role_id') == 6 || in_array(188, session('permissions')))
                $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.edit',
                    function() {
                        var id = parseInt($(this).parents('tr').attr('id'));
                        var link = '{{ route('admin.crm.update.index', ['id' => 0]) }}';

                        window.location = link.substr(0, link.lastIndexOf('/')) + '/' + id;
                    });
            @endif
        });
    </script>
@endsection
