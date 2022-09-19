@extends('admin.layout.master')

@section('title', 'Auto Tagging')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Auto Tagging
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Zone</th>
                                    <th class="border-primary border-darken-1">City</th>
                                    <th class="border-primary border-darken-1">Territory</th>
                                    <th class="border-primary border-darken-1">Service</th>
                                    <th class="border-primary border-darken-1">Tagged Salesperson</th>
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
        <div class="modal fade text-left" id="AssignAgentModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AssignAgentModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Tag User</h4>
                </div>
                <form method="post" id="agent_assign" action="{{route('admin.settings.lead_tagging.submit')}}">
                    @csrf

                <div class="modal-body">
                    <div class="form-group">
                        <select name="zone_id" id="zone_id" class="form-control select2" data-rule-required="true" data-msg-required="Zone is required">
                                <option value="0" > All Zones </option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}" > {{ $zone->name }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" id="city_select">
                        <select name="city_id" id="city_id" class="form-control select2" data-rule-required="true" data-msg-required="City is required">
                            <option value="0" > All Cities </option>

                        </select>
                    </div>
                    <div class="form-group" id="territory_select">
                        <select name="territory_id" id="territory_id" class="form-control select2" data-rule-required="true" data-msg-required="Territory is required">
                            <option value="0" > All Territories </option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="service_select">
                        <select name="service_id[]" id="service_id" class="form-control select2" data-rule-required="true" data-msg-required="Service is required"  multiple="multiple">
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" > {{ $service->name }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" id="agent_select">
                        <select name="agent_id" id="agent_id" class="form-control select2" data-rule-required="true" data-msg-required="Agent is required">
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}" > {{ $agent->name }} </option>
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


    <div class="modal fade text-left" id="EditAgentModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AssignAgentModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Edit Tagged User</h4>
                </div>
                <form method="post" id="agent_edit" action="{{route('admin.settings.lead_tagging.update')}}" novalidate="novalidate">
                    @csrf

                <div class="modal-body">
                    <input type="hidden" name="lead_tagging_id" id="lead_tagging_id">
                    
                    <div class="form-group">
                        <select name="zone_id" id="edit_zone_id" class="form-control select2" data-rule-required="true" data-msg-required="Zone is required">
                            <option value="0" > All Zones </option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}" > {{ $zone->name }} </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" id="edit_city_select">
                        <select name="city_id" id="edit_city_id" class="form-control select2" data-rule-required="true" data-msg-required="City is required">
                            <option value="0" > All Cities </option>
                            
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" > {{ $city->name }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" id="edit_territory_select">
                        <select name="territory_id" id="edit_territory_id" class="form-control select2" data-rule-required="true" data-msg-required="Territory is required">
                            <option value="0" > All Territories </option>
                            @foreach($territories as $territory)
                                <option value="{{ $territory->id }}" > {{ $territory->name }} </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <select name="service_id[]" id="edit_service_id" class="form-control select2" data-rule-required="true" data-msg-required="Service is required" multiple="multiple">
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" > {{ $service->name }} </option>
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

    <div class="modal fade" id="all_services" role="dialog" aria-labelledby="services_title" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="services_title">Service(s)</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                $('#zone_id').val('').trigger('change.select2');
                $('#city_id').val('').trigger('change.select2');
                $('#territory_id').val('').trigger('change.select2');
                $('#service_id').val('').trigger('change.select2');
                
            });

            // $('#EditAgentModal').on('hidden.bs.modal', function () {
                
            //     $("agent_id").select2('val', '')
            //     $('#edit_zone_id').val('').trigger('change.select2');
            //     $('#city_id').val('').trigger('change.select2');
            //     $('#territory_id').val('').trigger('change.select2');
            //     $('#service_id').val('').trigger('change.select2');
                
            // });
           
            $('#agent_id').prepend('<option selected></option>').select2({
                width:'100%',
                placeholder:"Select Sales Person",
                allowClear:true,
                dropdownParent:$('#agent_assign')
            });
            $('#service_id').select2({
                width:'100%',
                placeholder:"Select Service",
                allowClear:true,
                dropdownParent:$('#agent_assign')
            });
            $('#service_select').css('display','none');
            $('#city_select').css('display','none');
            $('#city_id').val(0).trigger('change.select2');
            $('#territory_select').css('display','none');
            $('#territory_id').val(0).trigger('change.select2');
            
            $('#agent_select').css('display','none');

            $('#zone_id').prepend('<option selected></option>').select2({
                width:'100%',
                placeholder:"Select Zone",
                allowClear:true,
                dropdownParent:$('#agent_assign')
            }).bind('change', function() {
                // $('#service_select').css('display','block');
                // $('#agent_select').css('display','block');

               
                var id = parseInt($(this).val());
                if(id == 0){
                        $('#service_select').css('display','block');
                        $('#agent_select').css('display','block');
                    $('#city_select').css('display','none');
                    $('#city_id').val(0).trigger('change.select2');
                    $('#territory_select').css('display','none');
                    $('#territory_id').val(0).trigger('change.select2');
                }else{
                    $('#city_select').css('display','block');
                    $('#city_id').children().remove()

                    var city_obj = [];
                    city_obj.length = 0

                $.map({!! $cities !!}, function (obj,index) {

                    // city_obj.push({id: obj.id, text: obj.name});

                    if(index == 0){
                        city_obj.push({id: 0, text: 'All Cities'});
                    }
                    if(id == obj.zone_id){
                            city_obj.push({id: obj.id, text: obj.name});

                        }
                });

                $('#city_id').prepend('<option selected></option>').select2({
                        width:'100%',
                        placeholder:"Select City",
                        allowClear:true,
                        dropdownParent:$('#agent_assign'),
                        data:city_obj
                    }).bind('change', function() {

                        var id = parseInt($(this).val());

                        if(id == 0){
                            $('#service_select').css('display','block');
                            $('#agent_select').css('display','block');
                            $('#territory_select').css('display','none');
                            $('#territory_id').val(0).trigger('change.select2');
                        }else{


                            $('#service_select').css('display','block');
                            $('#territory_select').css('display','block');
                            $('#agent_select').css('display','block');

                            $('#territory_id').children().remove()

                                var territory_obj  = [];
                                territory_obj.length = 0

                            $.map({!! $territories !!}, function (obj,index) {
                                if(index == 0) {
                                    territory_obj.push({id: 0, text: 'All Territories'});
                                }
                               if(id == obj.city_id){
                                        territory_obj.push({id: obj.id, text: obj.name});
                                }
                            });

                            $('#territory_id').prepend('<option selected></option>').select2({
                                    width:'100%',
                                    placeholder:"Select Territory",
                                    allowClear:true,
                                    dropdownParent:$('#agent_assign'),
                                    data:territory_obj
                                });
                        }
                    });
                }
            });    


            $('#edit_agent_id').prepend('<option selected></option>').select2({
                width:'100%',
                placeholder:"Select Agent",
                allowClear:true,
                dropdownParent:$('#agent_edit')
            });
            $('#edit_service_id').prepend('<option selected></option>').select2({
                width:'100%',
                placeholder:"Select Service",
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

            $('#edit_zone_id').select2({
                width:'100%',
                allowClear:true,
                dropdownParent:$('#agent_edit')
            }).bind('change', function() {
                
                // $("#edit_agent_id").select2('val', '');
                // $("#edit_service_id").select2('val', '');


               
                var id = parseInt($(this).val());
                if(id == 0){
                    $('#edit_service_id').val('').trigger('change.select2');
                    $('#edit_agent_id').val('').trigger('change.select2');
                    $('#edit_city_select').css('display','none');
                    $('#edit_city_id').val('').trigger('change.select2');
                    $('#edit_territory_select').css('display','none');
                    $('#edit_territory_id').val('').trigger('change.select2');

                }else{  
                    $('#edit_service_id').val('').trigger('change.select2');
                    $('#edit_agent_id').val('').trigger('change.select2');
                    $('#edit_city_id').val(0).trigger('change.select2');
                    $('#edit_city_select').css('display','block');
                    $('#edit_territory_select').css('display','none');
                    $('#edit_territory_id').val('').trigger('change.select2');
                    $('#edit_city_id').children().remove();
                // $('#edit_city_id').select2('destroy');
                    var city_obj = [];
                    city_obj.length = 0

                $.map({!! $cities !!}, function (obj, index) {
                    if(index == 0){
                        city_obj.push({id: 0, text: 'All Cities'});
                    }
                        if(id == obj.zone_id){
                            city_obj.push({id: obj.id, text: obj.name});
                        }
                });    
                $('#edit_city_id').select2({
                        width:'100%',
                        allowClear:true,
                        dropdownParent:$('#agent_edit'),
                        data:city_obj
                    }).bind('change', function() {
                
                        var id = parseInt($(this).val());
                            console.log('change_city');
                        if(id == 0){
                            $("#edit_service_id").select2('val', '');
                            $("#edit_agent_id").select2('val', '');

                            $('#edit_territory_select').css('display','none');
                            $('#edit_territory_id').children().remove();
                        // $('#edit_territory_id').select2('destroy');
                        
                        }else{
                            $('#edit_territory_select').css('display','block');

                            $("#edit_service_id").select2('val', '');
                            $("#edit_agent_id").select2('val', '');

                            $('#edit_territory_id').children().remove();
                            // $('#edit_territory_id').select2('destroy');
                            var territory_obj = [];
                            territory_obj.length = 0

                        $.map({!! $territories !!}, function (obj,index) {
                            if(index == 0){
                                territory_obj.push({id: 0, text: 'All Territories'});
                            }
                            if(id == obj.city_id){
                                    territory_obj.push({id: obj.id, text: obj.name});
                                }
                        });

                        $('#edit_territory_id').prepend('<option selected></option>').select2({
                                width:'100%',
                                placeholder:"Select Territory",
                                allowClear:true,
                                dropdownParent:$('#agent_edit'),
                                data:territory_obj
                            });
                        }
                        
                        
                    });
                }

            }); 

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons:[
                    @if (session('role_id') == 1 || in_array(662, session('permissions')))
                    
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
                ajax: '{{ route('admin.settings.lead_tagging.list') }}',
                rowId: 'id',
                // order: [[4, 'desc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'zone', name: 'z.name', class: 'align-middle zone'},
                    {data: 'city_name', name: 'c.name', class: 'align-middle city_name'},
                    {data: 'territory_name', name: 't.name', class: 'align-middle territory_name'},
                    {data: 'service2_link', name: 'service2', class: 'align-middle service', orderable: false, searchable: false},
                    {data: 'agent_name', name: 'ad.name', class: 'align-middle agent_name'},
                    {data: 'status', name: 'lead_taggings.status', class: 'align-middle status'},
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

                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.service')) {
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
                    url:'{!! route("admin.settings.lead_tagging.data") !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    $('#lead_tagging_id').val(data.lead_tagging_id);

                    if(data.zone_id == 0){
                        $('#edit_zone_id').val(data.zone_id).change();

                        $('#edit_city_id').css('display','none');
                        $('#edit_territory_id').css('display','none');
                        $('#edit_city_select').css('display','none');
                        $('#edit_territory_select').css('display','none');
                        $('#edit_service_id').val(data.service_id).change();

                        $('#edit_agent_id').val(data.agent_id).change();

                    }else{
                        if(data.city_id == 0){
                            $('#edit_zone_id').val(data.zone_id).change();
                        console.log('agent _cty  zero',data.agent_id);

                        console.log('city_0');
                        $('#edit_city_id').css('display','block');
                        $('#edit_city_select').css('display','block');
                        $('#edit_agent_id').val(data.agent_id).change();
                            // $('#edit_agent_id').val(2);
                            $('#edit_city_id').val(data.city_id);
                            $('#edit_service_id').val(data.service_id).change();
                            // $('#lead_tagging_id').val(data.lead_tagging_id).change();
                            $('#edit_territory_id').css('display','none');

                            $('#edit_territory_select').css('display','none');

                            // $('#edit_territory_id').val(data.territory_id).change();
                        }else{
                        console.log('agent _cty not zero',data.agent_id);
                            $('#edit_zone_id').val(data.zone_id).change();

                            $('#edit_territory_select').css('display','block');
                            $('#edit_territory_id').css('display','block');

                            $('#edit_agent_id').val(data.agent_id).change();
                            $('#edit_city_id').val(data.city_id).change();
                            $('#edit_service_id').val(data.service_id).change();
                            // $('#lead_tagging_id').val(data.lead_tagging_id).change();
                            $('#edit_territory_id').val(data.territory_id).change();
                        }
                        
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
                                            url:'{!! route("admin.settings.lead_tagging.enable_disable") !!}',
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

            
            $( "#agent_assign" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    form.submit();    
                }
                
            });

            $( "#agent_edit" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    form.submit();    
                }
                
            });
            $('#datatable tbody').on('click','tr td.service button',function () {
                var id = $(this).attr('id');
                console.log(id);
                $('#all_services .modal-body').html('');

                if(id){
                    $.ajax({
                        url:"{{route('admin.settings.lead_tagging.services')}}",
                        method:'POST',
                        data:{
                            'lead_tagging_id':id,
                            '_token':'{{ csrf_token() }}',
                        }
                    }).done(function (data) {
                        if (data) {
                            var services_names = '';

                            $.each(data.services, function(index, service) {
                                services_names += '<span class="font-weight-bold">'+service+'</span><br>';
                            });

                            $('#all_services .modal-body').html(services_names);

                            $('#all_services').modal('show');
                        }
                        // UnblockPagePermanently();

                    });
                }


            });
        });
    </script>
@endsection