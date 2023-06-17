
<form action="{{route('admin.management.city')}}" method="post" class="mt-2" id="addCityHubForm" novalidate="novalidate">
    {{csrf_field()}}

    <div class="row mb-2">
        <div class="col">
            <fieldset class="form-group">
                <input type="text" class="form-control" name="cityName" placeholder="Add City Name*" required data-rule-required="true" data-msg-required="This field is required">
            </fieldset>
            <fieldset class="form-group">
                <input type="text" class="form-control" name="city_code" placeholder="Add City Code">
            </fieldset>
        </div>
        <div class="col-3">
            <input type="hidden" id="city_type" name="postType" value="city">
            <fieldset class="radio-inline ml-1">
                <input type="radio" name="city-radio" class="icheck cradio" id="city-radio" rel="city" checked>
                <label for="city-radio">City</label>
            </fieldset>
        </div>
        <div class="col-3">
            <fieldset class="radio-inline ml-2">
                <input type="radio" name="city-radio" class="icheck cradio" id="hub-radio" rel="hub">
                <label for="hub-radio">Hub</label>
            </fieldset>

        </div>
    </div>
    <div class="row mb-2" id="hub_list_div" >
        <div class="col-6">
            <fieldset class="form-group">
                <select name="hubs" id="hub_list" class="form-control select2" style="width: 100%;" required data-rule-required="true" data-msg-required="This field is required">

                    @foreach($hubs as $hub)
                        <option value="{{$hub->hub_id}}">{{$hub->name}}</option>
                    @endforeach
                </select>
            </fieldset>
        </div>
    </div>

    <div class="row mb-2 d-none" id="zone_selection">
        <div class="col-6">
            <fieldset class="form-group">
                <select name="zone_id" id="zone" class="form-control select2" data-rule-required="true" data-msg-required="Zone is required">
                    @foreach($zones as $zone)
                        <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                    @endforeach
                </select>
            </fieldset>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-6">
            <fieldset class="form-group">
                <input type="text" name="attempt_tat" id="attempt_tat" class="form-control attempt_tat" placeholder="Add Attempt Tat*" required data-rule-required="true" data-msg-required="This field is required" data-rule-min="1" data-msg-min="Attempt tat can not be less than 1">
            </fieldset>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col">
            <fieldset class="form-group">
                <textarea type="text" name="address" id="address" class="form-control address" placeholder="Office Address"></textarea>
            </fieldset>
        </div>
    </div>

    <div class="row">
        <div class="col-2">
            <h4 class="card-title font-weight-bold">Location</h4>
        </div>
        <div class="col">
            <fieldset class="form-group">
                <input type="text" name="latitude" id="latitude" class="form-control latitude" placeholder="Latitude" data-rule-required="true" data-msg-required="Latitude is required">
            </fieldset>
        </div>
        <div class="col">
            <fieldset class="form-group">
                <input type="text" name="longitude" id="longitude" class="form-control longitude" placeholder="Longitude" data-rule-required="true" data-msg-required="Longitude is required">
            </fieldset>
        </div>
    </div>
    <div class="row">
        <div class="col-2">
            <h4 class="card-title font-weight-bold">Hub Location</h4>
        </div>
        <div class="col">
            <fieldset class="form-group">
                <input type="text" name="hub_latitude" id="hub_latitude" class="form-control hub_latitude" placeholder="Latitude" data-rule-required="true" data-msg-required="Latitude is required">
            </fieldset>
        </div>
        <div class="col">
            <fieldset class="form-group">
                <input type="text" name="hub_longitude" id="hub_longitude" class="form-control hub_longitude" placeholder="Longitude" data-rule-required="true" data-msg-required="Longitude is required">
            </fieldset>
        </div>
    </div>

    <div class="row">
        <div class="col-3">
            <h4 class="card-title font-weight-bold">GC Area</h4>
        </div>
        <div class="col">
            <fieldset class="">
                <input type="checkbox" name="gc_area" id="gc_area" class="icheckbox gc_area">
                <label for="gc_area" class="">GC Area</label>
            </fieldset>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <h2 class="card-title"><U>Services</U></h2>
        </div>
    </div>
    <div class="row">
        <div class="col-3">
            <h4 class="card-title font-weight-bold">Pickup</h4>
        </div>
        <div class="col-2">
            <fieldset class="">
                <input type="checkbox" name="pickup" id="pickup_checkbox" class="icheckbox">
                <label for="pickup" class="">Pickup</label>
            </fieldset>
        </div>
        <div class="col-7">
            <fieldset class="form-group">
                <input type="text" name="pickup_cut_off_time" id="pickup_cut_off_time" class="form-control pickup_cut_off_time" placeholder="Pickup Cut Off time" data-rule-min="0" data-msg-min="Pickup Cut-Off Time can not be less than 0" data-rule-max="23" data-msg-min="Pickup Cut-Off Time can not be more than 23" disabled data-rule-required="true" data-msg-required="Please enter hour between 0 and 23">
            </fieldset>
        </div>
    </div>
    <div class="row mb-2">

        <div class="col-12">
            <h4 class="card-title font-weight-bold">Delivery</h4>
            {{--<p class="text-danger" id="service_error_1" style="display:none;">Regular delivery is required</p>--}}

            @foreach($bookings as $booking)

                <div class="bs-callout-primary callout-border-left callout-square p-1">
                    <strong>{{$booking->booking_type}}&nbsp;<input type="checkbox" name="booking[{{$booking->id}}]" class="icheckbox bookingtype{{$booking->id}}" {{($booking->id == 1)? 'checked disabled':''}}></strong>

                    <div class="mt-1 form-group">
                        @foreach($shippingMode as $shipping)
                            <fieldset class="checkbox-inline mr-1 ">
                                <input type="checkbox" id="delivery[{{$booking->id}}][{{$shipping->id}}]" name="delivery[{{$booking->id}}][{{$shipping->id}}]" class="icheckbox shippingmode booking_{{$booking->id}}_shipping_mode" {{($booking->id == 1)? '':'disabled'}} {{($booking->id == 1) && ($shipping->id == 1)? 'checked':''}}>
                                <label for="delivery[{{$booking->id}}][{{$shipping->id}}]" class="">{{ucfirst($shipping->mode)}}</label>
                            </fieldset>
                        @endforeach
                        <p class="text-danger" id="shipping_error{{$booking->id}}" style="display:none;">Please select atleast one option</p>
                    </div>

                </div>
            @endforeach
            <div class="input-group">
                <div class="bs-callout-primary callout-border-left callout-square p-1">
                    <strong>Walk-In</strong>
                    <div class="mt-1 form-group">
                        @foreach($shippingMode as $sindex => $shipping)
                            @if($shipping->id != 4)
                                <fieldset class="checkbox-inline mr-1">
                                    <input type="checkbox" id="walk_in_delivery[{{$shipping->id}}]" name="walk_in_delivery[{{$shipping->id}}]" class="icheckbox">
                                    <label for="walk_in_delivery[{{$shipping->id}}]" class="">{{ucfirst($shipping->mode)}}</label>
                                </fieldset>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <h2 class="card-title"><U>OSA List</U></h2>
        </div>
        <div class="col-12" id="osa_list">
            
        </div>
        <div class="col-12">
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <a class="btn btn-success text-white" id="add_osa"><i class="la la-plus"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-warning btn-min-width btn-glow mr-1 mb-1" id="confirmAction">Add City</button>
        <button type="button" class="btn btn-primary btn-min-width btn-glow mr-1 mb-1" data-dismiss="modal">Cancel</button>

    </div>


</form>

<script type="text/javascript">
    $(document).ready(function () {

        $('#attempt_tat').inputmask({
            'alias': 'integer',
            'allowMinus': false,
            'allowPlus': false
        });
        $('#latitude').inputmask({
            'alias': 'decimal',
            'allowMinus': true,
            'allowPlus': false,
            'rightAlign': false,
            'digits': 6,
        });
        $('#longitude').inputmask({
            'alias': 'decimal',
            'allowMinus': true,
            'allowPlus': false,
            'rightAlign': false,
            'digits': 6,
        });
        $('#hub_latitude').inputmask({
            'alias': 'decimal',
            'allowMinus': true,
            'allowPlus': false,
            'rightAlign': false,
            'digits': 6,
        });
        $('#hub_longitude').inputmask({
            'alias': 'decimal',
            'allowMinus': true,
            'allowPlus': false,
            'rightAlign': false,
            'digits': 6,
        });
        $('#pickup_cut_off_time').inputmask({
            'alias': 'integer',
            'allowMinus': false,
            'allowPlus': false,
        });
        var errors = 0;
        function checkAtleastOne(targ,id) {
            if($(targ).is(':checked') === true ){

                if ($('.booking_'+id+'_shipping_mode:checked').length === 0) {
                    $('#shipping_error'+id+'').css('display','block');
                    errors = 1;
                }else{
                    errors = 0;

                    $('#shipping_error'+id+'').css('display','none');

                }

            }else{
                errors = 0;

                $('#shipping_error'+id+'').css('display','none');

            }

        }
        $('input.icheck').iCheck({
            checkboxClass: 'icheckbox_square-red',
            radioClass: 'iradio_square-red'
        });
        $('input.icheckbox').iCheck({
            checkboxClass: 'icheckbox_flat-red',
            radioClass: 'iradio_flat-red',
            increaseArea: '20%' // optional
        });
       
        $('#hub_list').prepend('<option value="" selected></option>').select2({
            placeholder: 'Select Hub',
            dropdownParent: $("#addCity")
        });


        $('#zone').prepend('<option value="" selected></option>').select2({
            placeholder: 'Zone',
            dropdownParent: $("#addCity"),
            width:'100%'
        });

        $("input[type='radio'][name='city-radio']").on('ifChecked', function(event){
            var rtype = $(this).attr('rel');
            if(rtype == 'city'){
                $('#city_type').val('city');
                if($('#hub_list_div').is(':hidden')){
                    // $('#hub_list_div').css('display','block');
                    $('#hub_list_div').fadeIn("slow");

                    $('#zone_selection').addClass('d-none');
                }
            }else if(rtype == 'hub'){
                $('#city_type').val('hub');

                if(!$('#hub_list_div').is(':hidden')){
                    $('#hub_list_div').fadeOut("slow");

                    // $('#hub_list_div').fadeIn('slow');

                    $('#zone_selection').removeClass('d-none');
                }

            }
        });
        // $('input.bookingtype1').on('ifUnchecked',function (e) {
        //     var checkbox = $(this);
        //         $('#service_error_1').css('display','block');
        //         errors = 1;
        // });
        // $('input.bookingtype1').on('ifChecked',function (e) {
        //
        //     var checkbox = $(this);
        //
        //         $('#service_error_1').css('display','none');
        //         errors = 0;
        // });

        @foreach($bookings as $booking)
        $('input.bookingtype{{$booking->id}}').on('ifChecked',function () {
            var shippingmode = $(this).parent().parent().next().find('input.shippingmode');
            checkAtleastOne($(this),{{$booking->id}});
            $(shippingmode).iCheck('enable');
        });
        $('input.bookingtype{{$booking->id}}').on('ifUnchecked',function (e) {

            var shippingmode = $(this).parent().parent().next().find('input.shippingmode');
            checkAtleastOne($(this),{{$booking->id}});
            $(shippingmode).iCheck('disable');
        });
        @endforeach

        @foreach($bookings as $booking)
        $('input.booking_{{$booking->id}}_shipping_mode').on('ifChecked',function () {

            if ($('.booking_{{$booking->id}}_shipping_mode:checked').length === 0) {
                $('#shipping_error{{$booking->id}}').css('display','block');
                errors = 1;
            }else{
                errors = 0;

                $('#shipping_error{{$booking->id}}').css('display','none');

            }

        });
        $('input.booking_{{$booking->id}}_shipping_mode').on('ifUnchecked',function () {

            if ($('.booking_{{$booking->id}}_shipping_mode:checked').length === 0) {
                $('#shipping_error{{$booking->id}}').css('display','block');
                errors = 1;
            }else{
                errors = 0;

                $('#shipping_error{{$booking->id}}').css('display','none');

            }


        });
        @endforeach

        $('#pickup_checkbox').on('ifChecked', function(event){
            $('#pickup_cut_off_time').prop('disabled', false);
        });
        $('#pickup_checkbox').on('ifUnchecked', function(event){
            $('#pickup_cut_off_time').prop('disabled', true);
        });

        $( "#addCityHubForm" ).validate({



            errorClass:"danger",
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parents('.form-group'));
            },
            submitHandler: function(form) {

                $('input.bookingtype{{$booking->id}}').on('ifUnchecked',function () {
                    var shippingmode = $(this).parent().parent().next().find('input.shippingmode');
                    checkAtleastOne($(this),{{$booking->id}});
                    $(shippingmode).iCheck('disable');
                });
                if(errors === 1){
                    return false;
                }else{
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    swal({
                        title: 'Please Wait!',
                        text: 'City/Hub is being added!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }


            }
        });
     

        $('#add_osa').click(function() {
            var counter = document.getElementById('osa_list').childElementCount;
            var html='<div class="row">';
                html += '<div class="col-5">';
                    html += '<div class="form-group">';
                        html += '<input type="text" class="form-control" name="osa_name['+counter+']" class="osa" required data-rule-required="true" data-msg-required="This field is required" placeholder="Enter OSA Area">';
                        html += '</div>';
                        html += '</div>';
                        html += '<div class="col-5">';
                            html += '<div class="form-group">';
                                html += '<input type="text" class="form-control text-left osa_charges" name="osa_rate['+counter+']" id="osa_rate['+counter+']" required data-rule-required="true" data-msg-required="This field is required" placeholder="OSA Charges">';
                                html += '</div>';
                                html += '</div>';
                                html += '<div class="col-2">';
                                html += '<a href="javascript:void(0);" class="btn btn-icon btn-danger remove_list" ><i class="la la-close"></i></a>';
                                html += '</div>';
                                $('#osa_list').append(html);

                                $('#osa_list input.osa_charges').inputmask({
                                    'alias': 'integer',
                                    'allowMinus': false,
                                    'allowPlus': false
                                });
        });

        $(document.body).on('click', '.remove_list' ,function(){
            $(this).parent().parent().remove();
        });
        
    });

</script>