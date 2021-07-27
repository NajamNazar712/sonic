<form action="{{route('admin.incidence_monitoring.update',['id'=>$incidence_monitoring->id])}}" method="post" class="mt-2" id="editIncidenceForm" novalidate="novalidate">

    @csrf
@method('put')
<div class="form-group">
    <select class="form-control" name="station_id" id="station_edit" data-rule-required="true" data-msg-required="Station is required">
        @foreach($stations as $station)
            <option value="{{$station->id}}">{{$station->name}}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <select class="form-control" name="monitoring_area_id" id="monitoring_area_edit" data-rule-required="true" data-msg-required="Monitoring Area is required">
        @foreach($monitoring_areas as $monitoring_area)
            <option value="{{$monitoring_area->id}}">{{$monitoring_area->name}}</option>
        @endforeach
    </select>
</div>

<div class="form-group input-group">
    <div class="input-group-prepend">
        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
            <p class="mt-1">Time From:</p>
        </span>
    </div>

    <input type="time" name="time_from" value="{{$incidence_monitoring->time_from}}"  class="form-control pickadate bg-primary border-primary white rounded-right deposit_date" id="time_from_edit" placeholder="Time*" data-rule-required="true" data-msg-required="Time is required">
</div>


<div class="form-group input-group">
    <div class="input-group-prepend">
        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
            <p class="mt-1 mr-1">Time To:</p>
        </span>
    </div>

    <input type="time" name="time_to" value="{{$incidence_monitoring->time_to}}"  class="form-control pickadate bg-primary border-primary white rounded-right deposit_date" id="time_to_edit" placeholder="Time*" data-rule-required="true" data-msg-required="Time is required">
</div>

<div class="form-group">
    <select class="form-control" name="case_nature_id" id="case_nature_edit" data-rule-required="true" data-msg-required="Case Nature is required">
        @foreach($case_natures as $case_nature)
            <option value="{{$case_nature->id}}">{{$case_nature->name}}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <input type="text" class="form-control" name="observations" value="{{$incidence_monitoring->observation}}" id="observations_edit" placeholder="Enter Observations" data-rule-required="true" data-msg-required="Observations is required">
</div>

<div class="form-group">
    <select class="form-control" name="nc_level_id" id="nc_level_edit" data-rule-required="true" data-msg-required="NC Level is required">
        @foreach($nc_levels as $nc_level)
            <option value="{{$nc_level->id}}">{{$nc_level->name}}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <select class="form-control select2" name="tagged_to_edit[]"  multiple="multiple" id="tagged_to_edit" data-rule-required="true" data-msg-required="Select Manager is required">
        @foreach($all_persons as $all_person)
        <option value="{{$all_person->admin_id}}">{{$all_person->admin->name}}({{$all_person->admin->role->name}})</option>
     @endforeach
    </select>
</div>

<div class="form-group">
    <input type="text" class="form-control" name="clip_link" value="{{$incidence_monitoring->clip_link}}" placeholder="Enter Clip Link" id="clip_link_edit" data-rule-required="true" data-msg-required="Clip Link To is required">
</div>
<div class="form-group text-right my-1">
        <button type="submit" class="btn btn-primary">Update</button>
    <button type="button" class="btn btn-dark btn-min-width mr-1" data-dismiss="modal">Cancel</button>

</div>





{{-- 

<div class="row align-items-center justify-content-center">
    
    <div class="col-md-12">
        <div class="row justify-content-center">
            <div class="col-12 form-group">
                <input type="text" name="reg_number" value="{{$fleet->reg_number}}" id="reg_number" class="form-control reg_number" placeholder="Registration Number*" data-rule-required="true" data-msg-required="Registration Number is required" >
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12 form-group">
             <select class="form-control vehicle_select" id="vehicle_select" name="vehicle_select" data-rule-required="true" data-msg-required="Vehicle Type is required">
                 @foreach($vehicles as $vehicle)
                            <option value="{{$vehicle->id}}">{{$vehicle->name}}</option>
                 @endforeach
             </select>
            </div>

            <div class="col-12 form-group d-none" id="other_picker_name_div_edit">
             <div class="form-group col-md">
                 <input type="text" name="vehicle_type_name" id="vehicle_type_name" class="form-control" placeholder="New Vehicle Type" data-rule-required="true" data-msg-required="Vehicle Type is required">
             </div>
             </div>
        </div>
        <div class="row justify-content-center">
             <div class="col-12 form-group">
                 <input type="text" value="{{$fleet->tracking_id}}" name="tracking_id" id="tracking_id" class="form-control tracking_id" placeholder="Tracking ID*" data-rule-required="true" data-msg-required="Tracking ID is required">
             </div>
         </div>
        
    </div>
    
    <div class="col-auto">
        <div class="form-group text-right my-1">
            <button type="submit" class="btn btn-primary">Update</button>
        <button type="button" class="btn btn-dark btn-min-width mr-1" data-dismiss="modal">Cancel</button>

        </div>
    </div>
</div> --}}

    


</form>

<script type="text/javascript">
    $(document).ready(function () {

        var data_11 = $.map({!! $agents !!}, function (obj) {
                        obj.text = obj.name+'('+obj.role_name+')';

                        return obj;
                    });


        $('#tagged_to_edit').select2({
                data:data_11,
                width:'100%',
                placeholder:"Tag Person*",
                allowClear:true,
                dropdownParent:$('#editIncidenceForm')
            });
            var datra = @json($incidence_monitoring_tagged_persons);
            $('#tagged_to_edit').val(datra).trigger('change');
            
           

            $('#station_edit').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Select Station *',
                dropdownParent:$('#editIncidenceForm')
			});

            var station_edit_id = @json($incidence_monitoring->station_id);
                $('#station_edit').val(station_edit_id).trigger('change');

            $('#monitoring_area_edit').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Monitoring Area',
                width:'100%',
                allowClear:true,
                dropdownParent:$('#editIncidenceForm')
            });

            var area_edit_id = @json($incidence_monitoring->area_id);
                $('#monitoring_area_edit').val(area_edit_id).trigger('change');


            $('#case_nature_edit').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Case Nature',
                width:'100%',
                allowClear:true,
                dropdownParent:$('#editIncidenceForm')
            });

            var case_nature_edit_id = @json($incidence_monitoring->case_nature_id);
                $('#case_nature_edit').val(case_nature_edit_id).trigger('change');

            $('#nc_level_edit').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select NC Level',
                width:'100%',
                allowClear:true,
                dropdownParent:$('#editIncidenceForm')
            });

            var nc_level_edit_id = @json($incidence_monitoring->nc_level_id);
                $('#nc_level_edit').val(nc_level_edit_id).trigger('change');
   


      
        var errors = 0;
        
        $( "#editIncidenceForm" ).validate({
            errorClass:"danger",
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parents('.form-group'));
            },
            normalizer: function(value) {
                    return $.trim(value);
                },
            submitHandler: function(form) {
                // $(form).find('button[type=submit]').attr('disabled', 'disabled');
                if(errors === 1){
                    return false;
                }else{
                    form.submit();
                }
            }
        });

        $( "#station_edit" ).change(function() {
            $.ajax({
                        url: '{!! route('admin.incidence_monitoring.get_managers') !!}',
                        method: 'POST',
                        data: {
                            'hub_id': $(this).val(),
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                            console.log(data);
                            if(data.status){
                                $('#tagged_to_edit').html('');
                                $.each(data.agents, function (index, agent) {
                                    $('#tagged_to_edit').append('<option value="'+agent.id+'" >'+agent.name+'('+agent.role_name+')</option>')
                                });
                            }else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                            
                        });
        });


    });
</script>