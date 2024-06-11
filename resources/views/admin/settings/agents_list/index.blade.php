@extends('admin.layout.master')

@section('title', 'Agents List')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    RVR Caller Agents List
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
        <div class="modal fade text-left" id="UpdateAgentTypeModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="UpdateAgentTypeModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Update Agent Type</h4>
                </div>
                <form method="post" id="add_agent_type" novalidate="novalidate">
                    @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <select name="agent_type" id="update_agent_type_id" class="form-control" data-rule-required="true"  data-msg-required="Agent Type is required">
                            <option value="" disabled selected>Select Agent Type</option>
                            @foreach($agent_types as $agent_type)
                                <option value="{{ $agent_type->id }}"> {{ $agent_type->name }} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id="update_agent_type_bulk_submit">Update</button>
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
                            <option disabled selected>Select Agent Type</option>
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

        .danger-text
        {
            color: red;
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
        $(document).ready(function() {

            $('#UpdateAgentTypeModal').on('hidden.bs.modal', function () {
                
                $('#update_agent_type_id').val('').change();
                $('#zone_id').val('').trigger('change.select2');
                
            });
            
            var selected_rows = [];

            var admin_ids = [];

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons:[
                    @if (session('role_id') == 1 || in_array(994, session('permissions')))
                    {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action: function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox') && !$(row.node()).hasClass('selected')) {
                                    id = parseInt(row.id());

                                    hub_id = $(row.node()).data('id');

                                    var allow = false;

                                    if(admin_ids.length == 0) {
                                        admin_ids.push(hub_id);

                                        allow = true;
                                    }
                                    else if(admin_ids[0] == hub_id) {
                                        allow = true;
                                    }

                                    if (allow) {
                                        row.select();

                                        var index = $.inArray(id, selected_rows);

                                        if (index === -1) {
                                            selected_rows.push(id);
                                        }

                                        table.button('.update_agent_type').enable();

                                    }
                                }
                            });
                        }
                    },
                    {
                        extend: 'selectNone',
                        text: 'Select None',
                        className: 'select_none',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox') && $(row.node()).hasClass('selected')) {
                                    row.deselect();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index !== -1) {
                                        selected_rows.splice(index, 1);
                                    }

                                    if (selected_rows.length == 0) {
                                        table.button('.update_agent_type').disable();
                                        admin_ids.splice(index, 1);
                                    }
                                }
                            });
                        }
                    },
                    {
                        text: '<i class="la la-plus"></i> Update Agent Type',
                        className: 'btn btn-primary update_agent_type',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            
                            if(selected_rows != ''){
                              $('#UpdateAgentTypeModal').modal('show');

                          }else{
                              var error = "Account Not selected!";
                              toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                          }
                            
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
                ajax: '{{ route('admin.settings.agents_list.list') }}',
                rowId: 'id',
                columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'name', name: 'name', class: 'align-middle name'},
                    {data: 'agent_type.name', name: 'agent_type.name', class: 'align-middle agent_type', orderable: false},
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
                   
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.action') ||  $(header).is('.agent_type')) {
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

            var selected_rows_2 = [];
            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));
              
                var index = $.inArray(id, selected_rows);
                var index_2 = $.inArray(id, selected_rows_2);

                var dataTable = $('#datatable').DataTable();
                var tr = $(this).closest('tr');
                var row = dataTable.row(tr);
                var rowData = row.data();
                var cond = (rowData.status_id != 2);
               
               if (index_2 === -1 && cond) {
                    selected_rows_2.push(id);
                }else {
                    if(selected_rows_2.includes(id)){
                        selected_rows_2.splice(index_2, 1);
                    }
                }
                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.update_agent_type').enable();

                }
                else {
                    table.button('.update_agent_type').disable();
                }
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
                    if(data.agent_caller_type !== null)
                    {
                        $('#edit_agent_type_id').val(data.agent_caller_type);
                    }
                    else
                    {
                        $('#edit_agent_type_id').val($('#edit_agent_type_id option:first').val()).trigger('change');
                    }
                    $('#editAgentTypeModal').modal('show');
                })
                
            });

            
            $( "#add_agent_type" ).validate({
                errorClass:"danger-text",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    var assign = parseInt($('#update_agent_type_id').val());
                                  swal({
                                      text: 'Are you sure, you want to Update?',
                                      icon: 'info',
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
                                      if (confirm) {
                                          if (assign) {
                                              $.ajax({
                                                  url: '{!! route('admin.settings.agents_list.update.bulk') !!}',
                                                  method: 'POST',
                                                  data: {
                                                      'agent_type_id': assign,
                                                      'admin_ids[]': selected_rows,
                                                      '_token': '{{ csrf_token() }}'
                                                  }
                                              })
                                                  .done(function (data) {
                                                      if (data.status == 1) {
                                                          $('#UpdateAgentTypeModal').modal('hide');
                                                          toastr.success(data.success, 'Success!', {
                                                              positionClass: 'toast-bottom-center',
                                                              containerId: 'toast-bottom-center'
                                                          });
                                                      } else {
                                                          toastr.error(data.error, 'Error!', {
                                                              positionClass: 'toast-top-center',
                                                              containerId: 'toast-top-center'
                                                          });
                                                      }
                                                      selected_rows = [];

                                                      table.rows().deselect();
                                                      $('#update_agent_type_id').val('').trigger('change');
                                                      $('#UpdateAgentTypeModal').modal('hide');
                                                      table.button('.update_agent_type').disable();
                                                      table.draw(true);

                                                  });
                                          } else {
                                              var error = "Agents Not Selected!";
                                              toastr.error(error, 'Error!', {
                                                  positionClass: 'toast-top-center',
                                                  containerId: 'toast-top-center'
                                              });
                                          }
                                      }
                                  });   
                }
                
                });

                $( "#agent_type_edit" ).validate({
                errorClass:"danger-text",
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