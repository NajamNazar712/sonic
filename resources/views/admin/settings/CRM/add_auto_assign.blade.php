@extends('admin.layout.master')

@section('title', 'Auto Assigning Agents')

@section('content')
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
                            <form method="post" id="crm_agent_assign" action="{{route('admin.settings.auto_assigning.submit')}}">
                                @csrf

                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>Agent*</label>
                                                <select name="admin_id" id="agent_id" class="form-control select2" required data-rule-required="true" data-msg-required="Agent is required">
                                                    @foreach($agents as $agent)
                                                        <option value="{{ $agent->id }}" data-role_id="{{$agent->role_id}}"> {{ $agent->name }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>Shipment Status &nbsp;(<input type="checkbox" class="checkAll"  >Select All)</label>
                                                <select name="shipment_status_id[]" id="shipment_status_id" class="form-control select2" multiple="multiple" >
                                                    @foreach($shipment_status as $status)
                                                        <option value="{{ $status->id }}" > {{ $status->name }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>Destination Zone* &nbsp;(<input type="checkbox" class="checkAll"  >Select All)</label>
                                                <select name="zone_id[]" id="zone_id" class="form-control select2" multiple="multiple" required data-rule-required="true" data-msg-required="This field is required">
                                                    @foreach($zones as $zone)
                                                        <option value="{{ $zone->id }}" > {{ $zone->name }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>Destination Hub* &nbsp;(<input type="checkbox" class="checkAll"  >Select All)</label>
                                                <select name="hub_id[]" disabled id="hub_id" class="form-control select2" multiple="multiple"  >

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label>Origin Zone*&nbsp;(<input type="checkbox" class="checkAll" id="origin_zone_id_checkbox">Select All)</label>
                                                <select name="origin_zone_id[]" id="origin_zone_id" class="form-control select2" multiple="multiple" required data-rule-required="true" data-msg-required="This field is required">
                                                    @foreach($zones as $zone)
                                                        <option value="{{ $zone->id }}" > {{ $zone->name }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label>Origin Hub (<input type="checkbox" class="checkAll" id="origin_id_checkbox" >Select All)</label>
                                                <select name="origin_id[]" disabled id="origin_id" class="form-control select2" multiple="multiple">
{{--                                                    @foreach($origins as $origin)--}}
{{--                                                        <option value="{{ $origin->id }}" > {{ $origin->name }} </option>--}}
{{--                                                    @endforeach--}}
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <div class="form-group">
                                                    <label>Origin Area&nbsp;(<input type="checkbox" class="checkAll"  id="origin_area_id_checkbox" >Select All)</label>
                                                    <select name="origin_area_id[]" disabled id="origin_area_id" class="form-control select2" multiple="multiple"  >

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>Case Nature* &nbsp;(<input type="checkbox" class="checkAll"  >Select All)</label>
                                                <select name="case_nature_id[]" id="case_nature_id" class="form-control select2" multiple="multiple" required data-rule-required="true" data-msg-required="This field is required">
                                                    @foreach($case_natures as $cn)
                                                        <option value="{{ $cn->id }}" > {{ $cn->name }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>Case Nature Type &nbsp;(<input type="checkbox" class="checkAll"  >Select All)</label>
                                                <select name="case_nature_type_id[]" disabled id="case_nature_type_id" class="form-control select2" multiple="multiple"  >

                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>Segments &nbsp;(<input type="checkbox" class="checkAll"  >Select All)</label>
                                                <select name="business_segment_id[]" id="business_segment_id" class="form-control select2" multiple="multiple"  >
                                                    @foreach($segments as $sg)
                                                        <option value="{{ $sg->id }}" > {{ $sg->name }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>Sub Segments &nbsp;(<input type="checkbox" class="checkAll"  >Select All)</label>
                                                <select name="sub_business_segment_id[]" disabled id="sub_business_segment_id" class="form-control select2" multiple="multiple"  >
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>Shipper with KAM &nbsp;(<input type="checkbox" class="checkAll"  >Select All)</label>
                                                <select name="shipper_key_id[]" disabled id="shipper_key_id" class="form-control select2" multiple="multiple">
{{--                                                    @foreach($shipper_key as $cn)--}}
{{--                                                        <option value="{{ $cn->id }}" > {{ $cn->name }} </option>--}}
{{--                                                    @endforeach--}}
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>Shipper without KAM &nbsp;(<input type="checkbox" class="checkAll" id="shipper_non_checkbox"  >Select All)</label>
                                                <select name="shipper_non_key_id[]" disabled id="shipper_non_key_id" class="form-control select2" multiple="multiple"  >
{{--                                                    @foreach($shipper_non_key as $cn)--}}
{{--                                                        <option value="{{ $cn->id }}" > {{ $cn->name }} </option>--}}
{{--                                                    @endforeach--}}
                                                </select>
                                            </div>
                                        </div>

                                    </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-success" id="assign_agentSubmit">Assign</button>
                                </div>
                            </form>

                        </div>
                    </div>
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

            function getShipperKey(agent_id) {
                let url = "{{ route('admin.settings.auto_assigning.get_shipper_key', ['agent_id' => '__AGENT_ID__']) }}";
                url = url.replace('__AGENT_ID__', agent_id);
                $.ajax({
                    url: url,
                    method: 'GET'
                }).done(function (data) {
                    if(data.status == 1){
                        $('#shipper_key_id').removeAttr('disabled');
                        let options = "";
                        $.each(data.shipper_keys, function(index, field) {
                            options+=`<option value='${field.id}' >${field.name}</option>`;
                        });
                        $('#shipper_key_id').append(options);

                        $('#shipper_key_id').find('option').filter(function() {
                            return $.trim($(this).text()) === '';
                        }).remove();
                    }else {
                        toastr.error('No shipper against this KAM', 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                })
            }
            function getShipperNonKey() {

                $.ajax({
                    url:'{!! route("admin.settings.auto_assigning.get_shipper_non_key") !!}',
                    method: 'GET'
                }).done(function (data) {
                    if(data.status == 1){
                        $('#shipper_non_key_id').removeAttr('disabled');
                        let options = "";
                        $.each(data.shipper_non_keys, function(index, field) {
                            options+=`<option value='${field.id}' >${field.name}</option>`;
                        });
                        $('#shipper_non_key_id').append(options);

                        $('#shipper_non_key_id').find('option').filter(function() {
                            return $.trim($(this).text()) === '';
                        }).remove();
                    }
                })
            }



            $('#AssignAgentModal').on('hidden.bs.modal', function () {
                // $("agent_id").select2('val', '')
                $('#agent_id').val('').trigger('change.select2');
                $('#zone_id').val('').trigger('change.select2');
                // $('#case_nature_id').val('').trigger('change.select2');
            });

            $('#origin_zone_id').select2({
                width:'100%',
                placeholder:"Select Zone",
                allowClear:false,
                dropdownParent:$('#crm_agent_assign')
            }).bind('change', function() {

                var origin_zone_id = $(this).val();
                $('#origin_id').attr('disabled','disabled');
                $('#origin_id').empty();
                $("#origin_area_id").attr('disabled','disabled');
                $('#origin_area_id').empty();
                $("#origin_id_checkbox,#origin_area_id_checkbox").prop('checked', false)
                if(origin_zone_id!='') {
                    $.ajax({
                        url:'{!! route("admin.settings.auto_assigning.get_origin_hub") !!}',
                        method: 'POST',
                        data: {
                            'origin_zone_id': origin_zone_id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if(data.status == 1){
                            $('#origin_id').removeAttr('disabled');
                            let options = "";
                            $.each(data.origin_hubs, function(index, field) {
                                options+=`<option value='${field.id}' >${field.name}</option>`;
                            });
                            $('#origin_id').append(options);

                            $('#origin_id').find('option').filter(function() {
                                return $.trim($(this).text()) === '';
                            }).remove();
                        }
                    })
                }

            });



            $('#origin_id').select2({
                width:'100%',
                placeholder:"Select Hub Origin",
                allowClear:false,
                dropdownParent:$('#crm_agent_assign')
            }).bind('change',function (){
                var origin_ids = $(this).val();
                $('#origin_area_id').attr('disabled','disabled');
                $('#origin_area_id').empty();
                if(origin_ids!=''){

                    $.ajax({
                        url:'{!! route("admin.settings.auto_assigning.get_origin_areas") !!}',
                        method: 'POST',
                        data: {
                            'origin_ids': origin_ids,
                            '_token': '{{ csrf_token() }}'
                        }

                    }).done(function (data){
                        if(data.status == 1){
                            $('#origin_area_id').removeAttr('disabled');
                            let options = "";
                            $.each(data.origin_areas, function(index, field) {
                                options+=`<option value='${field.id}' >${field.name}</option>`;
                            });
                            $('#origin_area_id').append(options);

                            $('#origin_area_id').find('option').filter(function() {
                                return $.trim($(this).text()) === '';
                            }).remove();
                        }
                    });
                }

            });

            $('#origin_area_id').select2({
                width:'100%',
                placeholder:"Select Area",
                allowClear:false,
                dropdownParent:$('#crm_agent_assign')
            });

            $('#agent_id').prepend('<option selected></option>').select2({
                width:'100%',
                placeholder:"Select Agent",
                allowClear:true,
                dropdownParent:$('#crm_agent_assign')
            }).bind('change',function (){
                $('#shipper_key_id').attr('disabled','disabled');
                $('#shipper_key_id').empty();
                $('#shipper_non_key_id').attr('disabled','disabled');
                $('#shipper_non_key_id').empty();

                var role_id = $(this).find(':selected').data('role_id');
                var agent_id = $(this).val();
                if(role_id==28 || role_id==37){
                    getShipperNonKey()

                } else if (role_id==43 || role_id==67 || role_id==75 || role_id==115) {
                    getShipperKey(agent_id);
                }

            });
            $('#case_nature_id').select2({
                width:'100%',
                placeholder:"Case Nature",
                allowClear:false,
                dropdownParent:$('#crm_agent_assign')
            }).bind('change', function() {

                var case_nature = $(this).val();
                $('#case_nature_type_id').attr('disabled','disabled');
                $('#case_nature_type_id').empty();
                $.ajax({
                    url:'{!! route("admin.settings.auto_assigning.case_nature_type") !!}',
                    method: 'POST',
                    data: {
                        'case_nature': case_nature,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status == 1){
                        $('#case_nature_type_id').removeAttr('disabled');
                        let options = "";
                        $.each(data.case_nature_type, function(index, field) {
                            options+=`<option value='${field.id}' >${field.type}</option>`;
                        });
                        $('#case_nature_type_id').append(options);

                        $('#case_nature_type_id').find('option').filter(function() {
                            return $.trim($(this).text()) === '';
                        }).remove();
                    }
                })

            });

            $('#case_nature_type_id').select2({
                width:'100%',
                placeholder:"Nature Type",
                allowClear:false,
                dropdownParent:$('#crm_agent_assign')
            });
            $('#shipper_non_key_id').select2({
                width:'100%',
                placeholder:"Shipper without KAM",
                allowClear:false,
                dropdownParent:$('#crm_agent_assign')
            });
            $('#shipper_key_id').select2({
                width:'100%',
                placeholder:"Shipper with KAM",
                allowClear:false,
                dropdownParent:$('#crm_agent_assign')
            });
            $('#zone_id').select2({
                width:'100%',
                placeholder:"Select Zone",
                allowClear:false,
                dropdownParent:$('#crm_agent_assign')
            }).bind('change', function() {

                var zone = $(this).val();
                $('#hub_id').attr('disabled','disabled');
                $('#hub_id').empty();
                $.ajax({
                    url:'{!! route("admin.settings.auto_assigning.get_hub") !!}',
                    method: 'POST',
                    data: {
                        'zone_id': zone,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status == 1){
                        $('#hub_id').removeAttr('disabled');
                        let options = "";
                        $.each(data.hubs, function(index, field) {
                            options+=`<option value='${field.id}' >${field.name}</option>`;
                        });
                        $('#hub_id').append(options);

                        $('#hub_id').find('option').filter(function() {
                            return $.trim($(this).text()) === '';
                        }).remove();
                    }
                })

            });

            $('#hub_id').select2({
                width:'100%',
                placeholder:"Select Hub",
                allowClear:false,
                dropdownParent:$('#crm_agent_assign')
            }).bind('change', function() {

            });

            $('#shipment_status_id').select2({
                width:'100%',
                placeholder:"Select Status",
                allowClear:false,
                dropdownParent:$('#crm_agent_assign')
            }).bind('change', function() {

            });
            $('#business_segment_id').select2({
                width:'100%',
                placeholder:"Select Segments",
                allowClear:false,
                dropdownParent:$('#crm_agent_assign')
            }).bind('change', function() {
                var business_segment_id = $(this).val();
                $('#sub_business_segment_id').attr('disabled','disabled');
                $('#sub_business_segment_id').empty();
                $.ajax({
                    url:'{!! route("admin.settings.auto_assigning.get_sub_segments") !!}',
                    method: 'POST',
                    data: {
                        'business_segment_id': business_segment_id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status == 1){
                        $('#sub_business_segment_id').removeAttr('disabled');
                        let options = "";
                        $.each(data.sub_segment, function(index, field) {
                            options+=`<option value='${field.id}' >${field.name}</option>`;
                        });
                        $('#sub_business_segment_id').append(options);
                        $('#sub_business_segment_id').find('option').filter(function() {
                            return $.trim($(this).text()) === '';
                        }).remove();
                    }
                })
            });
            $('#sub_business_segment_id').select2({
                width:'100%',
                placeholder:"Select Sub Segments",
                allowClear:false,
                dropdownParent:$('#crm_agent_assign')
            }).bind('change', function() {

            });

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
            $('#edit_origin_id').select2({
                width:'100%',
                allowClear:true,
                dropdownParent:$('#crm_agent_edit')
            });



            $( "#crm_agent_assign" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    let role_id = $('#agent_id').find(':selected').data('role_id') || 0; // Ensure role_id is a number
                    if ([43, 67, 75, 115].includes(role_id)){
                        let shipper_ids = $('#shipper_key_id').val();

                        if(shipper_ids!=''){
                            form.submit();
                        }else {
                            toastr.error('Select At least one shipper', 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                            return false;
                        }
                    } else {
                        form.submit();
                    }
                }

            });

            $("#crm_agent_assign .checkAll").on('click',function (){
                var nextSelect = $(this).closest('.form-group').find('select');
                var options = $(nextSelect).find('option');
                if($(this).is(':checked')) {
                    options.prop("selected", "selected");
                    $(nextSelect).trigger("change");
                }else{
                    options.prop("selected", false);
                    $(nextSelect).trigger("change");
                }
            });
        });

        // function checkAll(check){
        //     if($(check).is(':checked')){
        //         // var nextSelect = $(check).closest('.form-group').find('select');
        //         $('#shipment_status_id > option').prop("selected","");
        //         // $(options).prop("selected","");
        //         $('#shipment_status_id').trigger("change");
        //
        //     }
        // }
    </script>
@endsection