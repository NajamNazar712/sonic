<form action="{{route('admin.settings.fleet.update',['id'=>$fleet->id])}}" method="post" class="mt-2" id="editFleetForm" novalidate="novalidate">

    @csrf
@method('put')
<div class="row align-items-center justify-content-center">
    <div class="col-md-12">
        <div class="row justify-content-center">
            <div class="col-12 form-group">
                <input type="text" name="reg_number" value={{$fleet->reg_number}} id="reg_number" class="form-control reg_number" placeholder="Registration Number*" data-rule-required="true" data-msg-required="Registration Number is required" >
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12 form-group">
             <select class="form-control select2" id="vehicle_select" name="vehicle_select" data-rule-required="true" data-msg-required="Vehicle Type is required">
                 @foreach($vehicles as $vehicle)
                            <option value="{{$vehicle->id}}">{{$vehicle->name}}</option>
                 @endforeach
             </select>
            </div>

            <div class="col-12 form-group d-none" id="other_picker_name_div">
             <div class="form-group col-md">
                 <input type="text" name="vehicle_type_name" id="vehicle_type_name" class="form-control" placeholder="New Vehicle Type" data-rule-required="true" data-msg-required="Vehicle Type is required">
             </div>
             </div>
        </div>
        <div class="row justify-content-center">
             <div class="col-12 form-group">
                 <input type="text" value="{{$vehicle->tracking_id}}" name="tracking_id" id="tracking_id" class="form-control tracking_id" placeholder="Tracking ID*" data-rule-required="true" data-msg-required="Tracking ID is required">
             </div>
         </div>
        
    </div>
    
    <div class="col-auto">
        <div class="form-group text-right my-1">
            <button type="submit" class="btn btn-primary">Update</button>
        <button type="button" class="btn btn-dark btn-min-width mr-1" data-dismiss="modal">Cancel</button>

        </div>
    </div>
</div>

    


</form>

<script type="text/javascript">
    $(document).ready(function () {


        $('.select2').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Vehicle*',
                dropdownParent:$('#editFleetForm')
            });


                var id = @json($fleet->vehicle_type_id);
                $('.select2').val(id).trigger('change');


        var errors = 0;
        
        $( "#editFleetForm" ).validate({
            errorClass:"danger",
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parents('.form-group'));
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


    });
</script>