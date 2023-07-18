@extends('admin.layout.master')

@section('title', 'Auto Tagging Users')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Auto Tagging Users
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">User</th>
                                    <th class="border-primary border-darken-1">City</th>
                                    <th class="border-primary border-darken-1">Hub Area</th>
                                    <th class="border-primary border-darken-1">Case Nature</th>
                                    <th class="border-primary border-darken-1">Case Nature Type</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <div class="modal fade text-left" id="AssignAgentModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AssignAgentModal" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Tag User</h4>
                </div>
                <form method="post" id="crm_agent_assign" action="{{route('admin.settings.auto_tagging.submit')}}">
                    @csrf

                <div class="modal-body">
                    <div class="form-group">
                        <select name="admin_id" id="agent_id" class="form-control select2" data-rule-required="true" data-msg-required="Agent is required">
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}" > {{ $agent->name }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="city_id" id="city_id" class="form-control select2" data-rule-required="true" data-msg-required="City is required">
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" > {{ $city->name }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="city_area_id" id="city_area_id" class="form-control select2">
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="crm_case_nature_id" id="crm_case_nature_id" class="form-control select2">
                            @foreach($case_natures as $case_nature)
                                <option value="{{ $case_nature->id }}" > {{ $case_nature->name }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="crm_case_nature_type_id" id="crm_case_nature_type_id" class="form-control select2">
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" > {{ $city->name }} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id="assign_agentSubmit">Tag</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </form>

            </div>
        </div>
    </div>


    <div class="modal fade text-left" id="EditAgentModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="EditAgentModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Edit User</h4>
                </div>
                <form method="post" id="crm_agent_edit" action="{{route('admin.settings.auto_tagging.update')}}">
                    @csrf

                <div class="modal-body">
                    <input type="hidden" name="crm_agent_id" id="crm_agent_id">
                    <div class="form-group">
                        <select name="admin_id" id="edit_agent_id" class="form-control select2" data-rule-required="true" data-msg-required="Agent is required">
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}" > {{ $agent->name }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="city_id" id="edit_city_id" class="form-control select2" data-rule-required="true" data-msg-required="City is required">
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" > {{ $city->name }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="city_area_id" id="edit_city_area_id" class="form-control select2">
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="crm_case_nature_id" id="edit_crm_case_nature_id" class="form-control select2">
                            @foreach($case_natures as $case_nature)
                                <option value="{{ $case_nature->id }}" > {{ $case_nature->name }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="crm_case_nature_type_id" id="edit_crm_case_nature_type_id" class="form-control select2">
                        </select>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id="assign_agentSubmit">Update</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </form>

            </div>
        </div>
    </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">


@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>


    <script>
        $(document).ready(function() {

            $('#AssignAgentModal').on('hidden.bs.modal', function () {
                $('#agent_id').val('').trigger('change.select2');
                $('#city_id').val('').trigger('change.select2');
                $('#city_area_id').val('').trigger('change.select2');
                $('#crm_case_nature_id').val('').trigger('change.select2');
                $('#crm_case_nature_type_id').val('').trigger('change.select2');
            });
            $('#agent_id').prepend('<option selected></option>').select2({
                width:'100%',
                placeholder:"Select User",
                allowClear:true,
                dropdownParent:$('#crm_agent_assign')
            });
            $('#city_id').prepend('<option selected></option>').select2({
                width:'100%',
                placeholder:"Select City",
                allowClear:true,
                dropdownParent:$('#crm_agent_assign')
            });
            $('#city_area_id').prepend('<option selected></option>').select2({
                width:'100%',
                placeholder:"Select Hub Area",
                allowClear:true,
                dropdownParent:$('#crm_agent_assign')
            });
            $('#crm_case_nature_id').prepend('<option selected></option>').select2({
                width:'100%',
                placeholder:"Select Case Nature",
                allowClear:true,
                dropdownParent:$('#crm_agent_assign')
            });
            $('#crm_case_nature_type_id').prepend('<option selected></option>').select2({
                width:'100%',
                placeholder:"Select Case Nature Type",
                allowClear:true,
                dropdownParent:$('#crm_agent_assign')
            });

            $('#edit_agent_id').select2({
                width:'100%',
                placeholder:"Select User",
                allowClear:true,
                dropdownParent:$('#crm_agent_edit')
            });
            $('#edit_city_id').select2({
                width:'100%',
                placeholder:"Select City",
                allowClear:true,
                dropdownParent:$('#crm_agent_edit')
            });
            $('#edit_city_area_id').select2({
                width:'100%',
                placeholder:"Select Hub Area",
                allowClear:true,
                dropdownParent:$('#crm_agent_edit')
            });
            $('#edit_crm_case_nature_id').select2({
                width:'100%',
                placeholder:"Select Case Nature",
                allowClear:true,
                dropdownParent:$('#crm_agent_edit')
            });
            $('#edit_crm_case_nature_type_id').select2({
                width:'100%',
                placeholder:"Select Case Nature Type",
                allowClear:true,
                dropdownParent:$('#crm_agent_edit')
            });
            
            

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons:[
                    @if (session('role_id') == 1 || in_array(640, session('permissions')))
                    
                    {
                        text: '<i class="la la-plus"></i> Add',
                        className: 'btn btn-primary tag_agents',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            $('#AssignAgentModal').modal('show');
                            
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
                ajax: '{{ route('admin.settings.auto_tagging.list') }}',
                rowId: 'id',
                order: [[2, 'desc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'agent_name', name: 'ad.name', class: 'align-middle agent_name'},
                    {data: 'city_name', name: 'c.name', class: 'align-middle city_name'},
                    {data: 'city_area_name', name: 'ca.name', class: 'align-middle city_area_name'},
                    {data: 'case_natue', name: 'cn.name', class: 'align-middle case_natue'},
                    {data: 'case_nature_type', name: 'cnt.type', class: 'align-middle case_nature_type'},
                    {data: 'status', name: 'crm_auto_tag_users.status', class: 'align-middle status'},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
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
                    var departments_select = '<select name="departments_select" id="departments_select" class="select2 form-control"></select>';
                    
                    var status = '<select name="status" id="status" class="select2 form-control">';
                        status +='<option value="1">Enable</option>';
                        status +='<option value="0">Disable</option>';
                        status +='</select>';
                   
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }
                        else if($(header).is('.department')){
                            $(departments_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
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
    
            $('#city_id').on('change', function() {

                var city_id = $('#city_id').val();
                $('#city_area_id').empty().trigger('change');
                
                $.ajax({
                    url:'{!! route("admin.settings.auto_tagging.hub_areas") !!}',
                    method: 'POST',
                    data: {
                        'city_id': city_id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (result) {

                    if(result && result.length > 0)
                    {
                        $.each(result, function(index, option) {
                            var newOption = new Option(option.name, option.id); 
                            $('#city_area_id').append(newOption).trigger('change');
                        });
                    }
                })
                
            });

            $('#crm_case_nature_id').on('change', function() {

                var crm_case_nature_id = $('#crm_case_nature_id').val();
                $('#crm_case_nature_type_id').empty().trigger('change');

                $.ajax({
                    url:'{!! route("admin.settings.auto_tagging.case_nature_types") !!}',
                    method: 'POST',
                    data: {
                        'crm_case_nature_id': crm_case_nature_id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (result) {

                    if(result && result.length > 0)
                    {
                        $.each(result, function(index, option) {
                            var newOption = new Option(option.name, option.id); 
                            $('#crm_case_nature_type_id').append(newOption).trigger('change');
                        });
                    }
                })

            });

            $('#edit_city_id').on('change', function() {

                var edit_city_id = $('#edit_city_id').val();
                $('#edit_city_area_id').empty().trigger('change');

                $.ajax({
                    url:'{!! route("admin.settings.auto_tagging.hub_areas") !!}',
                    method: 'POST',
                    data: {
                        'city_id': edit_city_id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (result) {

                    if(result && result.length > 0)
                    {
                        $.each(result, function(index, option) {
                            var newOption = new Option(option.name, option.id); 
                            $('#edit_city_area_id').append(newOption).trigger('change');
                        });
                    }
                })

            });

            $('#edit_crm_case_nature_id').on('change', function() {

                var edit_crm_case_nature_id = $('#edit_crm_case_nature_id').val();
                $('#edit_crm_case_nature_type_id').empty().trigger('change');

                $.ajax({
                    url:'{!! route("admin.settings.auto_tagging.case_nature_types") !!}',
                    method: 'POST',
                    data: {
                        'crm_case_nature_id': edit_crm_case_nature_id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (result) {

                    if(result && result.length > 0)
                    {
                        $.each(result, function(index, option) {
                            var newOption = new Option(option.name, option.id); 
                            $('#edit_crm_case_nature_type_id').append(newOption).trigger('change');
                        });
                    }
                })

            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.edit', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                $.ajax({
                    url:'{!! route("admin.settings.auto_tagging.data") !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {

                    $('#city_area_id').empty().trigger('change');
                    $('#crm_case_nature_type_id').empty().trigger('change');

                    $.each(data.city_areas, function(index, option) {
                        var newOption = new Option(option.name, option.id); 
                        $('#edit_city_area_id').append(newOption).trigger('change');
                    });

                    $.each(data.crm_case_nature_types, function(index, option) {
                        var newOption = new Option(option.name, option.id); 
                        $('#edit_crm_case_nature_type_id').append(newOption).trigger('change');
                    });

                    $('#edit_agent_id').val(data.agent_id).change();
                    $('#edit_city_id').val(data.city_id).change();
                    $('#edit_city_area_id').val(data.city_area_id).change();
                    $('#edit_crm_case_nature_id').val(data.crm_case_nature_id).change();
                    $('#edit_crm_case_nature_type_id').val(data.crm_case_nature_type_id).change();
                    $('#crm_agent_id').val(data.crm_agent_id);
                    
                    $('#EditAgentModal').modal('show');

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
            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.enable_disable', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                                         $.ajax({
                                            url:'{!! route("admin.settings.auto_tagging.enable_disable") !!}',
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

            
            $( "#crm_agent_assign" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    form.submit();    
                }
                
            });
            
            $( "#crm_agent_edit" ).validate({
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