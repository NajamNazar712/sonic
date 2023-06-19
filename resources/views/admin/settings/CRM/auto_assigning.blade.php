@extends('admin.layout.master')

@section('title', 'Auto Assigning Agents')

@section('content')
    <style>
        table.datatable tbody{
            height: 500px;
        }
    </style>
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Auto Assigning Agents
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>

                                    <th class="border-primary border-darken-1">Agent</th>
                                    <th class="border-primary border-darken-1" style="width: 150px">Zone</th>
                                    <th class="border-primary border-darken-1" style="width: 150px" >Hub</th>
                                    <th class="border-primary border-darken-1" style="width: 150px" >Case Nature</th>
                                    <th class="border-primary border-darken-1" style="width: 200px" >Case Nature Type</th>
                                    <th class="border-primary border-darken-1" style="width: 150px" >Bus Segments</th>
                                    <th class="border-primary border-darken-1" style="width: 150px" >Sub Segments</th>
                                    <th class="border-primary border-darken-1" style="width: 160px" >Shipper With KAM</th>
                                    <th class="border-primary border-darken-1" style="width: 180px" >Shipper Without KAM</th>
                                    <th class="border-primary border-darken-1" style="width: 150px" >Shipment Status</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Created At</th>
                                    <th class="border-primary border-darken-1">Action</th>
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
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Assign Agent</h4>
                </div>

            </div>
        </div>
    </div>


    <div class="modal fade text-left" id="EditAgentModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AssignAgentModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Edit Agent</h4>
                </div>
{{--                <form method="post" id="crm_agent_edit" action="{{route('admin.settings.auto_assigning.update')}}">--}}
{{--                    @csrf--}}

{{--                <div class="modal-body">--}}
{{--                    <input type="hidden" name="crm_agent_id" id="crm_agent_id">--}}
{{--                    <div class="form-group">--}}
{{--                        <select name="admin_id" id="edit_agent_id" class="form-control select2" data-rule-required="true" data-msg-required="Agent is required">--}}
{{--                            @foreach($agents as $agent)--}}
{{--                                <option value="{{ $agent->id }}" > {{ $agent->name }} </option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                    <div class="form-group">--}}
{{--                        <select name="zone_id" id="edit_zone_id" class="form-control select2" data-rule-required="true" data-msg-required="Zone is required">--}}
{{--                            @foreach($zones as $zone)--}}
{{--                                <option value="{{ $zone->id }}" > {{ $zone->name }} </option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                    <input type="hidden" id="edit_case_nature_id" name="case_nature_id">--}}
{{--                </div>--}}
{{--                <div class="modal-footer">--}}
{{--                    <button type="submit" class="btn btn-success" id="assign_agentSubmit">Assign</button>--}}
{{--                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>--}}
{{--                </div>--}}
{{--            </form>--}}

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
                // $("agent_id").select2('val', '')
                $('#agent_id').val('').trigger('change.select2');
                $('#zone_id').val('').trigger('change.select2');
                // $('#case_nature_id').val('').trigger('change.select2');
            });


            // $('#case_nature_id').prepend('<option selected></option>').select2({
            //     width:'100%',
            //     placeholder:"Select Case Nature",
            //     allowClear:true,
            //     dropdownParent:$('#crm_agent_assign')
            // });

            $('#edit_agent_id').select2({
                width:'100%',
                allowClear:true,
                dropdownParent:$('#crm_agent_edit')
            });
            $('#edit_zone_id').select2({
                width:'100%',
                allowClear:true,
                dropdownParent:$('#crm_agent_edit')
            });
            // $('#edit_case_nature_id').select2({
            //     width:'100%',
            //     allowClear:true,
            //     dropdownParent:$('#crm_agent_edit')
            // });
            

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons:[
                    @if (session('role_id') == 1 || in_array(617, session('permissions')))
                    
                    {
                        text: 'Add',
                        className: 'btn btn-primary bulk_internal_comment',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            window.location.href = "{{route('admin.settings.auto_assigning.add')}}"
                        }
                    },
                    @endif
                    @if ((session('role_id') == 1 || in_array(872, session('permissions'))) && !empty($settings))
                        {
                            @if($settings->setting_value == 0)
                            text: '<i class="la la-check-circle"></i>Enable Auto Assigning',
                            className: 'btn btn-success ',
                            enabled: true,
                            action: function (e, dt, node, config) {

                                swal({
                                    text: 'Are you sure, you want to Enable Auto Assigning ?',
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
                                    if(confirm) {
                                        window.location.href = "{{route('admin.settings.auto_assigning.global_status')}}"
                                    }
                                });



                            }
                            @else
                            text: '<i class="la la-times-circle"></i>Disable Auto Assigning',
                            className: 'btn btn-danger ',
                            enabled: true,
                            action: function (e, dt, node, config) {

                                swal({
                                    text: 'Are you sure, you want to Disable Auto Assigning?',
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
                                    if(confirm) {
                                        window.location.href = "{{route('admin.settings.auto_assigning.global_status')}}";
                                    }
                                });

                            }
                            @endif
                        },
                    @endif

                    'reset'
                    ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                scrollX: true,
                language: {
                    processing: data_table_loader
                },
                ajax: '{{ route('admin.settings.auto_assigning.list') }}',
                rowId: 'id',
                order: [[12, 'desc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: ' serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'agent_name', name: 'ad.name', class: ' agent_name'},
                    {data: 'zone', name: 'z.name', class: ' zone', orderable: false, searchable: false},
                    {data: 'hub', name: 'z.name', class: ' hub', orderable: false, searchable: false,},
                    {data: 'case_nature', name: 'case_nature', class: ' case_nature', orderable: false, searchable: false,},
                    {data: 'case_nature_type', name: 'case_nature_type', class: ' case_nature_type', orderable: false, searchable: false,},
                    {data: 'business_segment', name: 'business_segment', class: ' business_segment', orderable: false, searchable: false,},
                    {data: 'sub_business_segment', name: 'sub_business_segment', class: ' sub_business_segment', orderable: false, searchable: false,},
                    {data: 'shipper_key', name: 'shipper_key', class: ' shipper_key', orderable: false, searchable: false,},
                    {data: 'shipper_non_key', name: 'shipper_non_key', class: ' shipper_non_key', orderable: false, searchable: false,},
                    {data: 'shipment_status', name: 'shipment_status', class: ' shipment_status', orderable: false, searchable: false,},
                    {data: 'status', name: 'crm_agents.status', class: ' status'},
                    {data: 'created_at', name: 'crm_agent_auto_assigns.created_at', class: ' created_at'},
                    {data: 'action', name: 'action', class: 'text-center  action p-1', orderable: false, searchable: false}
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

                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.case_nature')|| $(header).is('.zone')|| $(header).is('.hub')|| $(header).is('.case_nature_type')|| $(header).is('.business_segment')|| $(header).is('.shipper_key')|| $(header).is('.shipper_non_key')|| $(header).is('.shipment_status') || $(header).is('.sub_business_segment')) {
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
                {{--var id = parseInt($(this).parents('tr').attr('id'));--}}
                {{--$.ajax({--}}
                {{--    url:'{!! route("admin.settings.auto_assigning.data") !!}',--}}
                {{--    method: 'POST',--}}
                {{--    data: {--}}
                {{--        'id': id,--}}
                {{--        '_token': '{{ csrf_token() }}'--}}
                {{--    }--}}
                {{--}).done(function (data) {--}}
                {{--    $('#edit_agent_id').val(data.agent_id).change();--}}
                {{--    $('#edit_zone_id').val(data.zone_id).change();--}}
                {{--    if(data.case_nature_id != 4){--}}
                {{--        $('#edit_case_nature_id').val(null).change();--}}
                {{--    }else{--}}
                {{--        $('#edit_case_nature_id').val(data.case_nature_id).change();--}}
                {{--    }--}}
                {{--    $('#crm_agent_id').val(data.crm_agent_id);--}}

                {{--    $('#EditAgentModal').modal('show');--}}

                {{--})--}}
                
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
                                    if (confirm) {
                                        $.ajax({
                                            url: '{!! route("admin.settings.auto_assigning.delete") !!}',
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
                                    }
                            });

                
            });
            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.enable_disable', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                                         $.ajax({
                                            url:'{!! route("admin.settings.auto_assigning.enable_disable") !!}',
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
        });
    </script>
    <style>
        #datatable tbody ul{
            margin-left: -20px;
        }
    </style>
@endsection