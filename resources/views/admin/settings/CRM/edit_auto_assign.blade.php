@extends('admin.layout.master')

@section('title', 'Auto Assigning Agents')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                   Edit Auto Assigning Agents
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
                                            
                                            <select name="admin_id" id="agent_id" class="form-control select2" required data-rule-required="true" data-msg-required="Agent is required" disabled>
                                                @foreach($agents as $agent)
                                                <option value="{{ $agent->id }}" >
                                                    {{ $agent->name }}
                                                </option>                                                   
                                                @endforeach
                                                </select>
                                                <input type="hidden" name="id" value="{{ $selected_agent->agent_id }}" />
                                            </div>
                                        </div>
                                        @php  $ss  = $selected_agent->shipment_statuses->pluck('shipment_status_id')->toArray();  @endphp
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>Shipment Status</label>
                                                <select name="shipment_status_id[]" id="shipment_status_id" class="form-control select2" multiple="multiple" >
                                                    @foreach($shipment_status as $status)
                                                    <option value="{{ $status->id }}"  {{ (in_array($status->id,$ss)) ? 'selected' : ''  }} >
                                                        {{ $status->name }}
                                                    </option>                                                    
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    @php  $zid  = $selected_agent->zones->pluck('zone_id')->toArray(); @endphp

                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>Zone*</label>
                                                <select name="zone_id[]" id="zone_id" class="form-control select2" multiple="multiple" required data-rule-required="true" data-msg-required="This field is required">
                                                    <option value="" disabled>Select</option>
                                                    @foreach($zones as $zone)
                                                        <option value="{{ $zone->id }}" {{ in_array($zone->id, $zid)  ? 'selected' : '' }}> {{ $zone->name }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>


                                        

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>Hub</label>
                                                <select name="hub_id[]" id="hub_id"  class="form-control select2" multiple="multiple"  >
                                                    @foreach($hubs as $hub)
                                                    <option value="{{ $hub->id }}" > {{ $hub->name }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>



                                    @php    $cnh = $selected_agent->case_natures->pluck('case_nature_id')->toArray(); @endphp

                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>Case Nature*</label>
                                                <select name="case_nature_id[]" id="case_nature_id" class="form-control select2" multiple="multiple" required data-rule-required="true" data-msg-required="This field is required">
                                                    @foreach($case_natures as $cn)
                                                        <option value="{{ $cn->id }}"  {{ in_array($cn->id, $cnh)  ? 'selected' : '' }}> {{ $cn->name }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>


                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>Case Nature Type</label>
                                                <select name="case_nature_type_id[]" id="case_nature_type_id" class="form-control select2" multiple="multiple"  >
                                                    @foreach($case_nature_types as $case_nature_type)
                                                    <option value="{{ $case_nature_type->id }}"> {{ $case_nature_type->type }} </option>
                                                    @endforeach
                                                </select>
                                               
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @php  $sk  = $selected_agent->shipper_keys->pluck('shipper_key_id')->toArray(); @endphp

                                    <div class="row">
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label>Shipper Key</label>
                                                <select name="shipper_key_id[]" id="shipper_key_id" class="form-control select2" multiple="multiple"  >
                                                    @foreach($shipper_key as $cn)
                                                        <option value="{{ $cn->id }}" {{ in_array($cn->id, $sk)  ? 'selected' : '' }}>{{ $cn->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        @php  $snk  = $selected_agent->shipper_non_keys->pluck('shipper_non_key_id')->toArray(); @endphp

                                        <div class="col-4">
                                            <div class="form-group">
                                                <label>Shipper Non Key</label>
                                                <select name="shipper_non_key_id[]" id="shipper_non_key_id" class="form-control select2" multiple="multiple"  >
                                                    @foreach($shipper_non_key as $cn)
                                                    <option value="{{ $cn->id }}" {{ in_array($cn->id, $snk)  ? 'selected' : '' }}>
                                                        {{ $cn->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        @php  $bsk  = $selected_agent->business_types->pluck('business_segment_id')->toArray(); @endphp

                                        <div class="col-4">
                                            <div class="form-group">
                                                <label>Segments</label>
                                                <select name="business_segment_id[]" id="business_segment_id" class="form-control select2" multiple="multiple"  >
                                                    @foreach($segments as $sg)
                                                        <option value="{{ $sg->id }}" {{ in_array($sg->id, $bsk)  ? 'selected' : '' }}> {{ $sg->name }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-success" id="assign_agentSubmit">Edit Assign Agent</button>
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

            let zone = $('#zone_id').val();
            getHubs(zone);

            let case_nature = $('#case_nature_id').val();
            getCaseNatureType(case_nature);

            var hb = @jSON($selected_agent->hubs->pluck('hub_id')->toArray());
            var cnt = @jSON($selected_agent->case_nature_types->pluck('case_nature_type_id')->toArray());

            function getHubs(zone) {
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
                            let selected = (hb.includes(field.id)) ? 'selected' : '';
                            options+=`<option value='${field.id}' ${selected} >${field.name}<option>`;
                        });
                        $('#hub_id').append(options).trigger('change');
                        hb = [];
                    }
                })
            }

            function getCaseNatureType(case_nature){
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
                            let selected = (cnt.includes(field.id)) ? 'selected' : '';
                            options+=`<option value='${field.id}' ${selected}>${field.type}<option>`;
                        });
                        $('#case_nature_type_id').append(options);
                        cnt = [];
                    }
                })
            }

            $('#AssignAgentModal').on('hidden.bs.modal', function () {
                // $("agent_id").select2('val', '')
                $('#agent_id').val('').trigger('change.select2');
                $('#zone_id').val('').trigger('change.select2');
                // $('#case_nature_id').val('').trigger('change.select2');
            });
            $('#agent_id').prepend('<option selected></option>').select2({
                width:'100%',
                placeholder:"Select Agent",
                allowClear:true,
                dropdownParent:$('#crm_agent_assign')
            }).val({{ $selected_agent->agent_id }}).trigger('change');

            $('#case_nature_id').select2({
                width:'100%',
                placeholder:"Case Nature",
                allowClear:false,
                dropdownParent:$('#crm_agent_assign')
            }).bind('change', function() {

                var case_nature = $(this).val();
                getCaseNatureType(case_nature);

            });
        

            $('#case_nature_type_id').select2({
                width:'100%',
                placeholder:"Nature Type",
                allowClear:false,
                dropdownParent:$('#crm_agent_assign')
            }).bind('change', function() {

            });
            $('#shipper_non_key_id').select2({
                width:'100%',
                placeholder:"Shipper N-Key",
                allowClear:false,
                dropdownParent:$('#crm_agent_assign')
            });
            $('#shipper_key_id').select2({
                width:'100%',
                placeholder:"Shipper Key",
                allowClear:false,
                dropdownParent:$('#crm_agent_assign')
            }).bind('change', function() {

            });
            $('#zone_id').select2({
                width:'100%',
                placeholder:"Select Zone",
                allowClear:false,
                dropdownParent:$('#crm_agent_assign')
            }).bind('change', function() {

                var zone = $(this).val();
                getHubs(zone);

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



            $( "#crm_agent_assign" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $('#agent_id').prop('disabled', false);
                    form.submit();


                }

            });
        });
    </script>
@endsection