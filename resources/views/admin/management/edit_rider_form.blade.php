{{--/**--}}
 {{--* Created by PhpStorm.--}}
 {{--* User: WaqasTrax--}}
 {{--* Date: 6/1/2018--}}
 {{--* Time: 1:19 PM--}}
 {{--*/--}}

<style>
    textarea#address {
        resize: none;
    }
</style>
{{--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBMo9kqvMhqVAe_GCXZXOfzfAZ_oeBapkQ&callback=initMap" type="text/javascript"></script>--}}
<form action="{{route('admin.management.riders.edit',['id'=>$rider_id])}}" method="post" class="mt-2" id="editRiderForm" novalidate="novalidate">
    @csrf
    @method('PUT')
    <div class="row justify-content-center">
        @if($type == 1)
                <div class="col text-center">
                    <label class="font-medium-2 font-weight-bold block">Credit Card on Delivery-CCD</label>
                    <div class="form-group">
                        <label for="edit_ccd_rider_checkbox" class="font-medium-2 text-bold-600 mr-1">No</label>
                        <input type="checkbox" name="edit_ccd_rider_checkbox" id="edit_ccd_rider_checkbox" class="switchery edit_ccd_rider_checkbox" data-size="sm" data-switchery="true" {{ ($rider->ccd)? 'checked':'' }}>
                        <label for="edit_ccd_rider_checkbox" class="font-medium-2 text-bold-600 ml-1">Yes</label>
                    </div>
                </div>
        @endif
    </div>

    <div class="row mb-2">
        <div class="col">
            <fieldset class="form-group">
                <select name="city_id" id="city_list" class="form-control select2" style="width: 100%;" required data-rule-required="true" data-msg-required="This field is required">
                    {{--<option value="{{$rider->city->id}}" selected>{{$rider->city->name}}</option>--}}
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
                    <input type="text" class="form-control" name="rider_name" value="{{$rider->name}}" placeholder="Rider Name" required data-rule-required="true" data-msg-required="This field is required">
                </fieldset>

            </div>
            <div class="col">
                <fieldset class="form-group">
                    <input type="text" class="form-control" name="phone"  value="{{$rider->phone}}" placeholder="Phone No." required data-rule-required="true" data-msg-required="This field is required" data-rule-remote="{{ route('admin.management.rider.phone_unique', ['id' => $rider_id]) }}" data-msg-remote="Phone must be unique">
                </fieldset>
            </div>
            <div class="col">
                <fieldset class="form-group">
                    <input type="text" class="form-control" name="cnic"  value="{{$rider->cnic}}" placeholder="CNIC" required data-rule-required="true" data-msg-required="This field is required">
                </fieldset>
            </div>
            <div class="col">
                <fieldset class="form-group">
                    <input type="text" class="form-control" name="pin"  value="{{$rider->dummy_pin}}" placeholder="PIN" data-rule-minlength="4" data-rule-maxlength="4">
                </fieldset>
            </div>
            <div class="col">
                <fieldset class="form-group">
                    <input type="text" class="form-control" name="trax_id" value="{{$rider->trax_id}}"  placeholder="Trax ID">
                </fieldset>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col">
                <fieldset class="form-group">
                    <textarea name="address" class="form-control" placeholder="Address" id="address" cols="30" rows="5" required data-rule-required="true" data-msg-required="This field is required">{{$rider->address}}</textarea>
                </fieldset>
            </div>
        </div>
        <div class="row mb-2">
        <div class="col">
              <fieldset class="form-group">
                <select name="area" id="area_list" class="form-control select2" style="width: 100%;" required data-rule-required="true" data-msg-required="This field is required">
                   <option value="" selected>Select an Area</option>
                </select>
            </fieldset>
        </div>
    </div>
        <div class="row">
            <div class="col">
                <fieldset class="form-group">
                    <select name="rider_main_category" id="main_category_list" class="form-control select2" style="width: 100%;" required data-rule-required="true" data-msg-required="This field is required">

                        @foreach($main_category as $category)
                            <option value="{{$category->id}}">{{$category->name}}</option>
                        @endforeach
                    </select>
                </fieldset>
            </div>
            
            <div class="col" id="incentive_amount_div">
                <fieldset class="form-group">
                    <input type="text" name="incentive_amount" id="incentive_amount_input" class="form-control decimal" value="{{$rider->incentive_amount}}" maxlength="6" placeholder="Enter Incentive Amount" data-rule-required="true" data-msg-required="Incentive Amount is required">
                </fieldset>
            </div>
             <div class="col">
                <fieldset class="form-group">
                    <select name="rider_category" id="category_list" class="form-control select2" style="width: 100%;" required data-rule-required="true" data-msg-required="This field is required">

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
                        @foreach($routes as $route)
                            <option value="{{$route->id}}">{{$route->code}} ({{$route->start}} to {{$route->end}})</option>
                        @endforeach
                        <option value="other">Other</option>
                    </select>
                </fieldset>
            </div>
            <div class="col">
                <fieldset class="form-group">
                    <select name="operation_rider_id" id="operation_rider_id" class="form-control select2" style="width: 100%;" required data-rule-required="true" data-msg-required="This field is required">
                        @foreach($operation_rider_ids as $operation)
                            <option value="{{$operation->id}}">{{$operation->name}}</option>
                        @endforeach
                    </select>
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
        <button type="submit" class="btn btn-warning btn-min-width mr-1 mb-1" id="confirmAction">Update Rider</button>
        <button type="button" class="btn btn-primary btn-min-width mr-1 mb-1" data-dismiss="modal">Cancel</button>

    </div>
</form>
<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

<script type="text/javascript">
    $(document).ready(function () {

        // var elem = document.querySelector('.special_rider_checkbox');
        // var switchery = new Switchery(elem);
        @if($type == 1)
        var edit_ccd_elem = document.querySelector('.edit_ccd_rider_checkbox');
        var edit_ccd_switchery = new Switchery(edit_ccd_elem);
        @endif

        @if($rider->incentive_amount == null)
            $('#incentive_amount_div').addClass('d-none');
        @endif
        $('#editRiderForm .select2').select2({
            dropdownParent: $("#editRiderForm")
        });
        var category_id = {{$rider->rider_category_id}};
        $('#category_list').val(category_id).trigger('change');

        $('#main_category_list').prepend('<option value="" selected="selected"></option>').select2({
            placeholder:'Select Rider Main Category',
            dropdownParent: $("#editRiderForm")
        }).bind('change', function () {
                var id = parseInt($(this).val());
                console.log(id);
                if(id == 1){
                    $('#incentive_amount_div').removeClass('d-none');
                }else{
                    $('#incentive_amount_div').addClass('d-none');
                }
            });
        @if($rider->rider_main_category_id != null)
        var main_category_id = {{$rider->rider_main_category_id}};
        $('#main_category_list').val(main_category_id).trigger('change');
        
        @endif

        $('#area_list').select2({
            width:'100%',
            placeholder:"Select An Area",
            dropdownParent: $("#editRiderForm")
        });

        var area_list = $('#area_list');
        area_list.empty();
        @if(count(areas_list) > 0)
            var areas = @json($areas_list);
            area_list.attr("disabled", false);
            area_list.append(`<option value="">Select Area</option>`)
            $.each(areas, function (key, value) {
                var newOption = "<option value="+ value.id +">" + value.name + "</option>";
                area_list.append(newOption);
            });
            @if(!empty($rider->area_id))
                area_list.val({{ $rider->area_id }}).trigger('change');
            @endif
        
        @else{
            area_list.attr("disabled", true);
            area_list.attr("data-rule-required", false);
            ('#area_list-error').hide();
        @endif



        $("input[name='pin']").inputmask({
            'alias': 'integer',
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            'mask':"9999"
        });
        $('#incentive_amount_input').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 2,
                'min': 0,
                'max': 1000000
            });
        $("input[name='cnic']").inputmask({'mask': "99999-9999999-9", 'clearIncomplete': true});
        $("input[name='phone']").inputmask({'mask': "9999-9999999", 'clearIncomplete': true});
        //$('#riderInfoDiv input,#riderInfoDiv textarea,#riderInfoDiv select').attr('disabled','disabled');
        $('#city_list').val({!! $rider->city_id !!}).trigger('change');
        @if($rider->route_id != Null)
            $('#route_list').val({!! $rider->route_id !!}).trigger('change');
        @else
        $('#route_list').prepend('<option value="" selected="selected"></option>').select2({
            placeholder:'Select a route',
            dropdownParent: $("#editRiderForm")
        });
        @endif

        @if($rider->shift_id != Null)
        $('#shift_list').val({!! $rider->shift_id !!}).trigger('change');
        @else
        $('#shift_list').prepend('<option value="" selected="selected"></option>').select2({
            placeholder:'Select Shift',
            dropdownParent: $("#editRiderForm")
        });
        @endif

        @if($rider->reporting_location_id != Null)
        $('#location_list').val({!! $rider->reporting_location_id !!}).trigger('change');
        @else
        $('#location_list').prepend('<option value="" selected="selected"></option>').select2({
            placeholder:'Select Reporting Location',
            dropdownParent: $("#editRiderForm")
        });
        @endif

        @if($rider->operation_rider_id != Null)
        $('#operation_rider_id').val({!! $rider->operation_rider_id !!}).trigger('change');
        @else
        $('#operation_rider_id').prepend('<option value="" selected="selected"></option>').select2({
            placeholder:'Select Functional Category',
            dropdownParent: $("#editRiderForm")
        });
        @endif

        $('#city_list').on('change',function () {
            var routelist = $('#route_list');
            var id = $('#city_list').val();
            var area_list = $('#area_list');
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
                    //     var option = new Option(data[i].code+' ('+data[i].start+' - '+data[i].end+')', data[i].id, true, true);
                    //     routelist.append(option).trigger('change');
                    // }
                    $.each(data.route, function (key, value) {
                        var newOption = "<option value="+ value.id +">" + value.code + ' ('  + value.start + ' to ' + value.end +')' +"</option>";
                        routelist.append(newOption);
                    });
                    routelist.val('').trigger('change');

                    area_list.empty();
                    if(data.areas.length > 0){ 
                        area_list.attr("disabled", false);
                        $.each(data.areas, function (key, value) {
                            var newOption = "<option value="+ value.id +">" + value.name + "</option>";
                            area_list.append(newOption);
                        });
                        area_list.val('').trigger('change');
                    }
                    else{
                        area_list.attr("disabled", true);
                        area_list.attr("data-rule-required", false);
                        $('#area_list-error').remove();
                    }


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
        $("#editRiderForm").validate({

            errorClass: "danger",
            errorPlacement: function (error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
            submitHandler: function (form) {

                $(form).find('button[type=submit]').attr('disabled', 'disabled');
                swal({
                    title: 'Please Wait!',
                    text: 'Rider is being updated!',
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