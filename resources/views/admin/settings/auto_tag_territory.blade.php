@extends('admin.layout.master')

@section('title', 'Auto Tag Territory')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Auto Tag Territory 
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Sales Person</th>
                                    <th class="border-primary border-darken-1">City</th>
                                    <th class="border-primary border-darken-1">Territory</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Other territories</th>
                                    <th class="border-primary border-darken-1"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade text-left" id="AssignAgentModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AssignAgentModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Tag Territory</h4>
                </div>
                <form method="post" id="agent_assign" action="{{route('admin.settings.auto_tag_territories.submit')}}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group" id="city_select">
                            <select name="city_id" id="city_id" class="form-control select2" data-rule-required="true" data-msg-required="City is required">
                            </select>
                        </div>
                        <div class="form-group" id="territory_select">
                            <select name="territory_id[]" id="territory_id" class="form-control select2" data-rule-required="true" data-msg-required="Territory is required">
                            </select>
                        </div>
                        
                        <div class="form-group" id="agent_select">
                            <select name="agent_id" id="agent_id" class="form-control select2" data-rule-required="true" data-msg-required="Agent is required">
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}" > {{ $agent->name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-check d-none" id="lead_agent_div">
                            <label class="form-check-label mr-1" for="is_lead_user" style="margin: 0px 5px 0px 0px;">Lead User</label>
                            <input type="checkbox" name="is_lead_user" id="is_lead_user">
                        </div>

                        <div class="form-group mt-2 d-none" id="remaining_territory_select_div">
                            <select name="territory_id[]" id="remaining_territory_id" class="form-control select2" multiple="multiple">
                            </select>
                            <span class="text-danger d-none" id="remaining_territory_error">Assign at least 1 territory</span>
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


    <div class="modal fade text-left" id="EditAgentModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AssignAgentModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Edit Tagged User</h4>
                </div>
                <form method="post" id="agent_edit" action="{{route('admin.settings.auto_tag_territories.update')}}" novalidate="novalidate">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="auto_tagging_id" id="auto_tagging_id">
                        
                        <div class="form-group" id="edit_city_select">
                            <select name="city_id" id="edit_city_id" class="form-control select2" data-rule-required="true" data-msg-required="City is required">
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" > {{ $city->name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" id="edit_territory_select">
                            <select name="territory_id[]" id="edit_territory_id" class="form-control select2" data-rule-required="true" data-msg-required="Territory is required">
                                @foreach($territories as $territory)
                                    <option value="{{ $territory->id }}" > {{ $territory->name }} </option>
                                @endforeach
                            </select>
                        </div>

                        
                        <div class="form-group">
                            <select name="agent_id" id="edit_agent_id" class="form-control select2" data-rule-required="true" data-msg-required="Agent is required">
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}" > {{ $agent->name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group d-flex" id="edit_lead_agent_div">
                            <label class="form-check-label mr-1" for="edit_is_lead_user" style="margin: 0px 5px 0px 0px;">Lead User</label>
                            <input type="checkbox" name="is_lead_user" id="edit_is_lead_user">
                        </div>

                        <div class="form-group mt-2" id="edit_remaining_territory_select_div">
                            <select name="territory_id[]" id="edit_remaining_territory_id" class="form-control select2" multiple="multiple">
                            </select>
                            <span class="text-danger d-none" id="edit_remaining_territory_error">Assign at least 1 territory</span>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="edit_agentSubmit">Tag</button>
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
                $('#territory_id').val('').trigger('change.select2');
                $('#is_lead_user').prop('checked', false);
                $('remaining_territory_id').val('').trigger('change.select2');
                $('#remaining_territory_select_div').addClass('d-none');
            });

            function toggleLeadUserCheckbox() {
                var disableLeadUser = !$('#agent_id').val() || !$('#city_id').val() || !$('#territory_id').val();
                $('#is_lead_user').prop('disabled', disableLeadUser);
            }

            $('#AssignAgentModal').on('show.bs.modal', function() {
                toggleLeadUserCheckbox();
            });

            $('#agent_id, #city_id, #territory_id').change(function() {
                toggleLeadUserCheckbox();
            });

            function toggleLeadUserCheckboxEdit() {
                var disableLeadUser = !$('#edit_agent_id').val() || !$('#edit_city_id').val() || !$('#edit_territory_id').val();
                $('#edit_is_lead_user').prop('disabled', disableLeadUser);
                if (disableLeadUser == true){
                    $('#edit_remaining_territory_select_div').addClass('d-none');
                } else if(disableLeadUser == false) {
                    $('#edit_remaining_territory_select_div').removeClass('d-none');
                }
            }

            $('#AssignAgentModal').on('show.bs.modal', function() {
                toggleLeadUserCheckboxEdit();
            });

            $('#edit_agent_id, #edit_city_id, #edit_territory_id').change(function() {
                toggleLeadUserCheckboxEdit();
            });

            $('#territory_select').css('display','none');
            $('#agent_select').css('display','none');

            $('#agent_id').prepend('<option selected></option>').select2({
                width:'100%',
                placeholder:"Select Sales Person",
                allowClear:true,
                dropdownParent:$('#agent_assign')
            });
            
            var city_obj = [];
            city_obj.length = 0

            $.map({!! $cities !!}, function (obj,index) {
                city_obj.push({id: obj.id, text: obj.name});
            });

            $('#city_id').prepend('<option selected></option>').select2({
                    width:'100%',
                    placeholder:"Select City",
                    allowClear:true,
                    dropdownParent:$('#agent_assign'),
                    data:city_obj
                }).bind('change', function() {

                    var id = parseInt($(this).val());

                    $('#territory_select').css('display','block');
                    $('#agent_select').css('display','block');
                    $('#lead_agent_div').removeClass('d-none');
                    $('#lead_agent_div').addClass('d-flex');

                    $('#territory_id').children().remove()

                    var territory_obj  = [];
                    var remaining_territory_obj  = [];
                    territory_obj.length = 0
                    remaining_territory_obj.length = 0

                    $.map({!! $territories !!}, function (obj) {
                            if(id == obj.city_id){
                                territory_obj.push({id: obj.id, text: obj.name});
                                remaining_territory_obj.push({id: obj.id, text: obj.name});
                            }
                    });

                    $('#territory_id').prepend('<option selected></option>').select2({
                            width:'100%',
                            placeholder:"Select Territory",
                            allowClear:true,
                            dropdownParent:$('#agent_assign'),
                            data:territory_obj
                    });

                    $('#is_lead_user').unbind('change').change(function() {
                        if ($(this).is(':checked')) {
                            var selectedTerritoryId = $('#territory_id').val();
                            var filteredTerritories = remaining_territory_obj.filter(function(item) {
                                return item.id != selectedTerritoryId;
                            });

                            $('#remaining_territory_select_div').removeClass('d-none');
                            $('#remaining_territory_id').empty().prepend('<option></option>').select2({
                                width: '100%',
                                placeholder: "Select Territory",
                                allowClear: true,
                                dropdownParent: $('#agent_assign'),
                                data: filteredTerritories
                            });
                        } else {
                            $('#remaining_territory_select_div').addClass('d-none');
                            $('#remaining_territory_id').val(null).trigger('change');
                            $('#remaining_territory_id').empty();
                        }
                    });

                    $('#territory_id').change(function() {
                        if ($('#is_lead_user').is(':checked')) {
                            var selectedTerritoryId = $(this).val();
                            var filteredTerritories = remaining_territory_obj.filter(function(item) {
                                return item.id != selectedTerritoryId;
                            });
                            $('#remaining_territory_id').empty().prepend('<option selected></option>').select2({
                                width: '100%',
                                placeholder: "Select Territory",
                                allowClear: true,
                                dropdownParent: $('#agent_assign'),
                                data: filteredTerritories
                            });
                        }
                    }); 

                });

            $('#edit_agent_id').prepend('<option selected></option>').select2({
                width:'100%',
                placeholder:"Select Agent",
                allowClear:true,
                dropdownParent:$('#agent_edit')
            });

            $('#edit_city_id').select2({
                width:'100%',
                allowClear:true,
                dropdownParent:$('#agent_edit')
            });

            $('#edit_territory_id').prepend('<option selected></option>').select2({
                width:'100%',
                placeholder:"Select Territory",
                allowClear:true,
                dropdownParent:$('#agent_edit')
            });

            $('#edit_remaining_territory_id').prepend('<option selected></option>').select2({
                width:'100%',
                placeholder:"Other Territory",
                allowClear:true,
                dropdownParent:$('#agent_edit')
            });
            
            var city_obj = [];
            city_obj.length = 0
            $.map({!! $cities !!}, function (obj, index) {
                city_obj.push({id: obj.id, text: obj.name});
            });    

            $('#edit_city_id').select2({
                    width:'100%',
                    allowClear:true,
                    dropdownParent:$('#agent_edit'),
                    data:city_obj
                }).bind('change', function() {
                var id = parseInt($(this).val());
                    $('#edit_territory_select').css('display','block');
                    $('#edit_territory_id').children().remove();
                    $('#edit_remaining_territory_id').children().remove();
                    var territory_obj = [];
                    var edit_remaining_territory_obj  = [];
                    territory_obj.length = 0
                    edit_remaining_territory_obj.length = 0

                $.map({!! $territories !!}, function (obj) {
                    if(id == obj.city_id){
                        territory_obj.push({id: obj.id, text: obj.name});
                    } else {
                        edit_remaining_territory_obj.push({id: obj.id, text: obj.name});
                    }
                });

                $('#edit_territory_id').prepend('<option selected></option>').select2({
                    width:'100%',
                    placeholder:"Select Territory",
                    allowClear:true,
                    dropdownParent:$('#agent_edit'),
                    data:territory_obj
                }); 

                $('#edit_remaining_territory_id').select2({
                    width:'100%',
                    placeholder:"Other Territory",
                    allowClear:true,
                    dropdownParent:$('#agent_edit'),
                    data:edit_remaining_territory_obj
                }); 

                $('#EditAgentModal').on('show.bs.modal', function(){
                    var selectedTerritoryIdModal = $('#edit_territory_id').val();
                    var filteredTerritoriesModal = edit_remaining_territory_obj  .filter(function(item) {
                        return item.id != selectedTerritoryIdModal;
                    });
                    $('#edit_remaining_territory_select_div').removeClass('d-none');
                    $('#edit_remaining_territory_id').select2({
                        width: '100%',
                        placeholder: "Other Territory",
                        allowClear: true,
                        dropdownParent: $('#agent_edit'),
                        data: filteredTerritoriesModal
                    });
                });

                $('#edit_is_lead_user').unbind('change').change(function() {
                    if ($(this).is(':checked')) {
                        var selectedTerritoryId = $('#edit_territory_id').val();
                        var filteredTerritories = edit_remaining_territory_obj  .filter(function(item) {
                            return item.id != selectedTerritoryId;
                        });

                        $('#edit_remaining_territory_select_div').removeClass('d-none');
                        $('#edit_remaining_territory_id').select2({
                            width: '100%',
                            placeholder: "Other Territory",
                            allowClear: true,
                            dropdownParent: $('#agent_edit'),
                            data: filteredTerritories
                        });
                    } else {
                        $('#edit_remaining_territory_select_div').addClass('d-none');
                    }
                });
            });

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons:[
                    @if (session('role_id') == 1 || in_array(698, session('permissions')))
                    
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
                ajax: '{{ route('admin.settings.auto_tag_territories.list') }}',
                rowId: 'id',
                order: [[3, 'desc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'agent_name', name: 'ad.name', class: 'align-middle agent_name'},
                    {data: 'city_name', name: 'c.name', class: 'align-middle city_name'},
                    {data: 'territory_name', name: 't.name', class: 'align-middle territory_name'},
                    {data: 'status', name: 'auto_tag_territories.status', class: 'align-middle status'},
                    {data: 'territory_names', name: 'territory_names', class: 'align-middle territory_names'},
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

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.edit', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                $.ajax({
                    url:'{!! route("admin.settings.auto_tag_territories.data") !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    $('#auto_tagging_id').val(data.auto_tagging_id);
                    $('#edit_territory_select').css('display','block');
                    $('#edit_territory_id').css('display','block');
                    $('#edit_agent_id').val(data.agent_id).change();
                    $('#edit_city_id').val(data.city_id).change();
                    $('#edit_territory_id').val(data.territory_id).change();
                    if (data.is_lead_user == 0) {
                        $('#edit_is_lead_user').prop('checked', false);
                        $('#edit_remaining_territory_select_div').addClass('d-none');
                    } else if (data.is_lead_user == 1) {
                        $('#edit_is_lead_user').prop('checked', true);
                        $('#edit_remaining_territory_select_div').removeClass('d-none');
                    }
                    $('#edit_remaining_territory_id').empty();  
                    if (data.other_territory_names && data.other_territory_names.length > 0) {
                        data.other_territory_names.forEach(function(territory) {
                            $('#edit_remaining_territory_id').append('<option value="' + territory.id + '" selected>' + territory.name + '</option>');
                        });
                    }
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
                    url:'{!! route("admin.settings.auto_tag_territories.enable_disable") !!}',
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

            // $( "#agent_assign" ).validate({
            //         errorClass:"danger",
            //         errorPlacement: function(error, element) {
            //             error.addClass('w-100').appendTo(element.parent('.form-group'));
            //         },
            //         submitHandler: function(form) {
            //             form.submit();    
            //         }
                
            // });


            $( "#agent_assign" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $('#assign_agentSubmit').on('click', function() {
                        if ($('#is_lead_user').is(':checked') && $('#remaining_territory_id').val() == ''){
                            $('#remaining_territory_error').removeClass('d-none');
                        } else {
                            $('#remaining_territory_error').addClass('d-none');
                            form.submit();
                        }
                    });    
                }
            });

            // $( "#agent_edit" ).validate({
            //     errorClass:"danger",
            //     errorPlacement: function(error, element) {
            //         error.addClass('w-100').appendTo(element.parent('.form-group'));
            //     },
            //     submitHandler: function(form) {
            //         form.submit();    
            //     }
            
            // });

            $( "#agent_edit" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $('#edit_agentSubmit').on('click', function() {
                        if ($('#edit_is_lead_user').is(':checked') && $('#edit_remaining_territory_id').val() == ''){
                            $('#edit_remaining_territory_error').removeClass('d-none');
                        } else {
                            $('#edit_remaining_territory_error').addClass('d-none');
                            form.submit();
                        }
                    });  
                }
            
            });

            // assign_agentSubmit
        });
    </script>
@endsection