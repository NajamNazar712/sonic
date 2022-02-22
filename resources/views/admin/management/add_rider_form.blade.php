<style>
    textarea#address {
        resize: none;
    }
</style>

<form action="{{route('admin.management.riders.add')}}" method="post" class="mt-1" id="addRiderForm" novalidate="novalidate">
    {{csrf_field()}}
    <div class="row justify-content-center">
        @if($type == 1)
            <div class="col text-center">
                <label class="font-medium-2 font-weight-bold block">Credit Card on Delivery-CCD</label>
                <div class="form-group">
                    <label for="ccd_rider_checkbox" class="font-medium-2 text-bold-600 mr-1">No</label>
                    <input type="checkbox" name="ccd_rider_checkbox" id="ccd_rider_checkbox" class="switchery ccd_rider_checkbox" data-size="sm" data-switchery="true">
                    <label for="ccd_rider_checkbox" class="font-medium-2 text-bold-600 ml-1">Yes</label>
                </div>
            </div>
        @endif
    </div>


    <div class="row mb-2">
        <div class="col">
            <fieldset class="form-group">
                <select name="city_id" id="city_list" class="form-control select2" style="width: 100%;" required data-rule-required="true" data-msg-required="This field is required">
                    @foreach($cities as $city)
                        <option value="{{$city->id}}">{{$city->name}}</option>
                    @endforeach
                </select>
            </fieldset>
        </div>

        <div class="col">
            <fieldset class="form-group">
                <select name="rider_shift" id="shift_list" class="form-control select2" style="width: 100%;" required data-rule-required="true" data-msg-required="This field is required">
                    @foreach($shifts as $shift)
                        <option value="{{$shift->id}}"> {{$shift->name}}</option>
                    @endforeach
                </select>
            </fieldset>
        </div>
    </div>
<div id="riderInfoDiv">
    <div class="row mb-2">
        <div class="col">
            <fieldset class="form-group">
                <input type="text" class="form-control" name="rider_name" placeholder="Rider Name" required data-rule-required="true" data-msg-required="This field is required">
            </fieldset>

        </div>
        <div class="col">
            <fieldset class="form-group">
                <input type="text" class="form-control" name="phone"  placeholder="Phone No." required data-rule-required="true" data-msg-required="This field is required" data-rule-remote="{{ route('admin.management.rider.phone_unique') }}" data-msg-remote="Phone must be unique">
            </fieldset>
        </div>
        <div class="col">
            <fieldset class="form-group">
                <input type="text" class="form-control" name="cnic"  placeholder="CNIC" required data-rule-required="true" data-msg-required="This field is required">
            </fieldset>
        </div>
        <div class="col">
            <fieldset class="form-group">
                <input type="text" class="form-control" name="pin"  placeholder="PIN" required data-rule-required="true" data-msg-required="This field is required" data-rule-minlength="4" data-rule-maxlength="4">
            </fieldset>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col">
            <fieldset class="form-group">
                <textarea name="address" class="form-control" placeholder="Address" id="address" cols="30" rows="5" required data-rule-required="true" data-msg-required="This field is required"></textarea>
            </fieldset>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <fieldset class="form-group">
                <select name="rider_main_category" id="category_main_list" class="form-control select2" style="width: 100%;" required data-rule-required="true" data-msg-required="This field is required">
                    <option value="" selected>Select a Rider Main Category</option>
                    @foreach($main_category as $category)
                        <option value="{{$category->id}}">{{$category->name}}</option>
                    @endforeach
                </select>
            </fieldset>
        </div>
        <div class="col">
            <fieldset class="form-group">
                <select name="rider_category" id="category_list" class="form-control select2" style="width: 100%;" required data-rule-required="true" data-msg-required="This field is required">
                    <option value="" selected>Select a Rider Sub Category</option>
                    @foreach($categories as $category)
                        <option value="{{$category->id}}">{{$category->name}}</option>
                    @endforeach
                </select>
            </fieldset>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <fieldset class="form-group">
                <select name="route_id" id="route_list" class="form-control select2" style="width: 100%;" required data-rule-required="true" data-msg-required="This field is required">
                    <option value="" selected>Select a Route</option>

                </select>
            </fieldset>
        </div>
        <div class="col">
            <fieldset class="form-group">
                <select name="operation_rider_id" id="operation_rider_id" class="form-control select2" required>
                    <option value="" selected>Select a Category</option>
                    @foreach($operation_riders as $operation)
                        <option value="{{$operation->id}}">{{$operation->name}}</option>
                    @endforeach
                </select>
                <div class="danger" id="rider_error" style="display:none;">This field is required</div>
            </fieldset>
        </div>
    </div>
    <div id="new_route_div" class="d-none">
        <div class="row mb-2">
            <div class="col">
                <fieldset class="form-group">
                    <input type="text" class="form-control" name="route_code" placeholder="Route Code" required data-rule-required="true" data-msg-required="This field is required">
                </fieldset>

            </div>
            <div class="col">
                <fieldset class="form-group">
                    <input type="text" class="form-control" name="start"  id="startSearchTextField" placeholder="Start Point" required data-rule-required="true" data-msg-required="This field is required">
                </fieldset>
            </div>
            <div class="col">
                <fieldset class="form-group">
                    <input type="text" class="form-control" name="end"  placeholder="End Point" required data-rule-required="true" data-msg-required="This field is required">
                </fieldset>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col">
                <fieldset class="form-group">
                    <select name="route_type_id" id="route_type_id" class="form-control select2" style="width: 100%;" required data-rule-required="true" data-msg-required="This field is required">
                        <option value="" selected>Select Route Type</option>
                        @foreach($route_types as $route_type)
                            <option value="{{$route_type->id}}">{{$route_type->name}}</option>
                        @endforeach
                    </select>
                </fieldset>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col">
                <fieldset class="form-group">
                    <textarea name="junction" class="form-control" placeholder="Add Junctions (comma seperated)" id="junction" cols="30" rows="5" required data-rule-required="true" data-msg-required="This field is required"></textarea>
                </fieldset>
            </div>
        </div>
    </div>
</div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-warning btn-min-width mr-1 mb-1" id="confirmAction">Add Rider</button>
        <button type="button" class="btn btn-primary btn-min-width mr-1 mb-1" data-dismiss="modal">Cancel</button>

    </div>
</form>
<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

<script type="text/javascript">
    $(document).ready(function () {
        @if($type == 1)
        var ccd_elem = document.querySelector('.ccd_rider_checkbox');
        var ccd_switchery = new Switchery(ccd_elem);
        @endif
        $('#city_list').prepend('<option value="" selected="selected"></option>').select2({
            placeholder:'Select City',
            dropdownParent: $("#addRiderForm")
        });
        $('#route_list').prepend('<option value="" selected="selected"></option>').select2({
            placeholder:'Select a route',
            dropdownParent: $("#addRiderForm")
        });
        $('#operation_rider_id').prepend('<option value="" selected="selected"></option>').select2({
            placeholder:'Select Functional Category',
            dropdownParent: $("#addRiderForm")
        });
        $('#category_list').prepend('<option value="" selected="selected"></option>').select2({
            placeholder:'Select Rider Sub-Category',
            dropdownParent: $("#addRiderForm")
        });
        $('#category_main_list').prepend('<option value="" selected="selected"></option>').select2({
            placeholder:'Select Rider Main Category',
            dropdownParent: $("#addRiderForm")
        });
        $('#shift_list').prepend('<option value="" selected="selected"></option>').select2({
            placeholder:'Select Shift',
            dropdownParent: $("#addRiderForm")
        });
        $('#location_list').prepend('<option value="" selected="selected"></option>').select2({
            placeholder:'Select Reporting Location',
            dropdownParent: $("#addRiderForm")
        });
        $("input[name='cnic']").inputmask({'mask': "99999-9999999-9", 'clearIncomplete': true});
        $("input[name='phone']").inputmask({'mask': "9999-9999999", 'clearIncomplete': true});
        $("input[name='pin']").inputmask({
            'alias': 'integer',
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            'mask':"9999"
        });
        $('#riderInfoDiv input,#riderInfoDiv textarea,#riderInfoDiv select').attr('disabled','disabled');


        $('#city_list').on('change',function () {
            var routelist = $('#route_list');
            var id = $('#city_list').val();
            if($(this).val() != ''){
                $('#riderInfoDiv input,#riderInfoDiv textarea,#riderInfoDiv select').removeAttr('disabled');
            }
            $.ajax({
                url:'{!! route('admin.management.rider.category.ajax') !!}',
                type:'GET',
                dataType:'json',
                    data: {
                    'id':id,
                },
                success:function (data) {

                    routelist.empty();
                    // for(var i = 0; i < data.length; i++){
                    //     //var option = new Option(data[i].code+' ('+data[i].start+' to '+data[i].end+')', data[i].id, true, true);
                    //     routelist.append(option);
                    // }
                    //routelist.append('<option value="other">Other</option>').trigger('change');
                    $.each(data, function (key, value) {
                        var newOption = "<option value="+ value.id +">" + value.code + ' ('  + value.start + ' to ' + value.end +')' +"</option>";
                        routelist.append(newOption);
                    });
                   routelist.val('').trigger('change');
                }
            });
        });
        $('#route_list').on('change', function () {
            var selection = $(this).val();
            if(selection == 'other'){
                $('#new_route_div').removeClass('d-none');
            }else{
                $('#new_route_div').addClass('d-none');
            }
        });
        $("#addRiderForm").validate({

            errorClass: "danger",
            errorPlacement: function (error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
            submitHandler: function (form) {

                $(form).find('button[type=submit]').attr('disabled', 'disabled');
                swal({
                    title: 'Please Wait!',
                    text: 'Rider is being added!',
                    icon: 'info',
                    buttons: false,
                    closeOnClickOutside: false,
                    closeOnEsc: false
                });

                form.submit();


            }
        });
    });
</script>