@extends('admin.layout.master')

@section('title', 'Agents List')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Agents List
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1"></th>
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Agent Name</th>
                                    <th class="border-primary border-darken-1">Agent Type</th>
                                    <th class="border-primary border-darken-1">Action</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade text-left" id="AddAgentTypeModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddAgentTypeModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Assign Agent Type</h4>
                </div>
                <form method="post" id="add_agent_type" action="{{route('admin.settings.agent_types.store')}}">
                    @csrf

                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" name="name" class="form-control" placeholder="Name*" maxlength="50" data-rule-required="true" data-msg-required="Agent Type Name is required">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id="assign_agentSubmit">Add</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </form>

            </div>
        </div>
    </div>


    <div class="modal fade text-left" id="editAgentTypeModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="editAgentTypeModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Edit Agent Type</h4>
                </div>
                <form method="post" id="agent_type_edit" action="{{route('admin.settings.agents_list.update')}}" novalidate="novalidate">
                    @csrf

                <div class="modal-body">
                    <input type="hidden" name="admin_id" id="edit_agent_id">
                    
                    <div class="form-group">
                        <input type="text" name="agent_name" id="edit_agent_name" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <select name="agent_type" id="edit_agent_type_id" class="form-control" data-rule-required="true"  data-msg-required="Agent Type is required">
                            @foreach($agent_types as $agent_type)
                                <option value="{{ $agent_type->id }}"> {{ $agent_type->name }} </option>
                            @endforeach
                        </select>
                    </div>
                    
                    
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id="edit_agent_typeSubmit">Update</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </form>

            </div>
        </div>
    </div>
    </div>
@endsection

@section('css')
    <style>
        input.select2-search__field {
            width: 140px !important;
        }
        
        table.dataTable tbody tr td.select-checkbox:before {
            top: 50%;
            border-color: #64a0d2;
        }

        table.dataTable tbody tr.selected td.select-checkbox:after {
            top: 50%;
            text-shadow: none;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">


@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>


    <script>
        @php
            $permission = in_array(490, session('permissions'));
            if ($permission) {
                $permission = 1;
            } else {
                $permission = 0;
            }
        @endphp
        $(document).ready(function() {

            $('#AddAgentTypeModal').on('hidden.bs.modal', function () {
                
                $("agent_id").select2('val', '')
                $('#zone_id').val('').trigger('change.select2');
                
            });
           
            $('#agent_id').select2({
                width:'100%',
                placeholder:"Select Sales Person",
                allowClear:true,
                dropdownParent:$('#add_agent_type')
            });
            
            var selected_rows = [];

            var admin_ids = [];

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons:[
                    @if (session('role_id') == 1 || in_array(665, session('permissions')))
                    {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action: function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass(
                                        'select-checkbox') && !$(row.node()).hasClass(
                                        'selected')) {
                                    id = parseInt(row.id());

                                    var assigned_agent_id = row.data()
                                        .assigned_agent_id;
                                    var tat = row.data().confirmation_on;

                                    // admin_id = $(row.node()).data('id');
                                    admin_id = id;
                                   
                                    var allow = false;

                                    if (admin_ids.length == 0) {
                                        admin_ids.push(admin_id);

                                        allow = true;
                                    } else if (admin_ids[0] == admin_id) {
                                        allow = true;
                                    }

                                    if (allow) {
                                        row.select();

                                        var index = $.inArray(id, selected_rows);

                                        if (index === -1) {
                                            selected_rows.push(id);
                                        }

                                        table.button('.assign').enable();
                                        table.button('.un-assign').enable();
                                    }
                                }
                            });
                        }
                    },
                    {
                        text: '<i class="la la-plus"></i> Assign Agent Type',
                        className: 'btn btn-primary tag_agents',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            $('#AddAgentTypeModal').modal('show');
                            
                        }
                    },
                    @endif
                    'reset'
                    ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                language: {
                    processing: data_table_loader
                },
                ajax: '{{ route('admin.settings.agents_list.list') }}',
                rowId: 'id',
                columns: [{data: 'id',orderable: false,searchable: false,class: 'text-center align-middle select p-1',targets: 0,
                            render: function(data, type, row) {
                                return '';
                            }
                        },
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'name', name: 'name', class: 'align-middle name'},
                    {data: 'agent_type.name', name: 'agent_type.name', class: 'align-middle agent_type'},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    $('td:eq(0)', row).addClass('select-checkbox');
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var departments_select = '<select name="departments_select" id="departments_select" class="select2 form-control"></select>';
                   
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }else if ($(header).is('.status')) {
                            $(status).appendTo($(search))
                                .on('change', function () {
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
                    

                    $('#status').prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    this.api().table().columns.adjust();
                }
            });

            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {

            var id = parseInt($(this).parent('tr').attr('id'));
            var hub_id = $(this).parents('tr').data('hub');
            var verify_shipment = table.row($(this).parents('tr')).data().verify_shipment
            var con_id = parseInt($(this).parent('tr').attr('consolidation_id'));
            var agent_assigned = table.row($(this).parents('tr')).data().RvShipmentAssignedAgent
            var assigned_agent_id = table.row($(this).parents('tr')).data().assigned_agent_id;
            var tat = table.row($(this).parents('tr')).data().confirmation_on;
            var id = parseInt($(this).parent('tr').attr('id'));



            // if (con_id) {
            //     table.rows().nodes().each(function(index) {
            //         var row = table.row(index);
            //         if ($(row.node()).attr('consolidation_id') == con_id) {
            //             var rid = parseInt($(row.node()).attr('id'));
            //             var rindex = $.inArray(rid, selected_rows);

            //             if (rindex === -1) {
            //                 selected_rows.push(rid);
            //                 if (id != rid) {

            //                     table.row(row).select();
            //                 }
            //             } else {
            //                 if (id != rid) {

            //                     row.deselect();
            //                 }
            //                 selected_rows.splice(rindex, 1);
            //             }
            //             if (selected_rows.length > 0 || call_history.length > 0) {
            //                 table.button('.confirm').enable();
            //                 table.button('.assign').enable();
            //                 table.button('.re-attempt').enable();
            //                 table.button('.un-assign').enable();

            //             } else {
            //                 table.button('.confirm').disable();
            //                 table.button('.assign').disable();
            //                 table.button('.re-attempt').disable();
            //                 table.button('.un-assign').disable();

            //             }
            //         }
            //     });
            // } 
            // if (verify_shipment === 1) {
            //     table.button('.confirm').enable();
            // }
            // else{
            //     table.button('.confirm').disable();
            // }

            });
            
            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.edit', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                $.ajax({
                    url:'{!! route("admin.settings.agents_list.data") !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    $('#edit_agent_id').val(data.agent_id);
                    $('#edit_agent_name').val(data.agent_name);
                    $('#edit_agent_type_name').val(data.agent_type_name);
                    $('#edit_agent_type_id').val(data.agent_type_id);
                    $('#editAgentTypeModal').modal('show');
                })
                
            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.delete', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                swal({
                                text: 'Are you sure, you want to Delete?',
                                icon: 'warning',
                                buttons: {
                                    cancel: {
                                        text: 'No',
                                        value: null,
                                        visible: true,
                                        closeModal: true,
                                    },
                                    confirm: {
                                        text: 'Yes',
                                        value: true,
                                        visible: true,
                                        closeModal: true
                                    }
                                },
                                closeOnClickOutside: false,
                                closeOnEsc: false,
                                dangerMode: true
                            }).then(function(confirm) {
                                         $.ajax({
                                            url:'{!! route("admin.settings.auto_tagging.delete") !!}',
                                            method: 'POST',
                                            data: {
                                                'id': id,
                                                '_token': '{{ csrf_token() }}'
                                            }
                                        }).done(function (data) {
                                            toastr.success(data.success, 'Success!', {
                                                positionClass: 'toast-bottom-center',
                                                containerId: 'toast-bottom-center'
                                            });
                                            table.draw();
                                        });
                            });

                
            });

            
            $( "#add_agent_type" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    form.submit();    
                }
                
                });

                $( "#agent_type_edit" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    form.submit();    
                }
                
                });
        });
    </script>
@endsection