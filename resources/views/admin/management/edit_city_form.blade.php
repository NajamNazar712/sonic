
<form action="{{route('admin.management.city.edit',['id'=>$city->id])}}" method="post" class="mt-2" id="editCityHubForm" novalidate="novalidate">
    {{csrf_field()}}
    <input type="hidden" name="_method" value="PUT">
    <div class="row mb-2">
        <div class="col">
            <fieldset class="form-group">
                <input type="text" class="form-control" name="cityName" value="{{$city->name}}" placeholder="Add City Name" required data-rule-required="true" data-msg-required="This field is required">
            </fieldset>
        </div>
        <div class="col-3">
            <input type="hidden" id="city_type" name="postType" value="{{($isHub == 1)? 'hub':'city'}}">
            <fieldset class="radio-inline ml-1">
                <input type="radio" name="city-radio" class="icheck cradio" id="city-radio" rel="city" {{($isHub == 0)? 'checked':''}}>
                <label for="city-radio">City</label>
            </fieldset>
        </div>
        <div class="col-3">
            <fieldset class="radio-inline ml-1">
                <input type="radio" name="city-radio" class="icheck cradio" id="hub-radio" rel="hub" {{($isHub == 1)? 'checked':''}}>
                <label for="hub-radio">Hub</label>
            </fieldset>

        </div>
    </div>

    <div class="row mb-2" id="hub_list_div" style="display:{{($isHub == 1)? 'none':''}}">
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

    <div class="row mb-2 {{(($isHub == 0) ? 'd-none' : '')}}" id="zone_selection">
        <div class="col-6">
            <fieldset class="form-group">
                <select name="zone_id" id="zone" class="form-control select2" data-rule-required="true" data-msg-required="Zone is required">
                    @foreach($zones as $zone)
                        <option value="{{ $zone->id }}" @if ($zone->id == $city->zone_id) selected="selected" @endif>{{ $zone->name }}</option>
                    @endforeach
                </select>
            </fieldset>
        </div>
    </div>

    <div class="row" id="zone_selection">
        <div class="col">
            <h2 class="card-title"><U>Services</U></h2>
        </div>

    </div>
    <div class="row mb-2">
        <div class="col-3">
            <h4 class="card-title font-weight-bold">Pickup</h4>
        </div>
        <div class="col">
            <fieldset class="">
                <input type="checkbox" name="pickup" class="icheckbox" {{($city->pickup == 1)? 'checked':''}}>
                <label for="pickup" class="">Pickup</label>
            </fieldset>
        </div>
        <div class="col-12">
            <h4 class="card-title font-weight-bold">Delivery</h4>

            @foreach($bookings as $index => $booking)

                <div class="bs-callout-primary callout-border-left callout-square p-1">
                    <strong>{{$booking->booking_type}}&nbsp;<input type="checkbox"  name="booking[{{$booking->id}}]" {{isset($delivery[$booking->id])? 'checked':''}} class="icheckbox bookingtype{{$booking->id}}" {{($booking->id == 1)? 'disabled':''}}></strong>
                    <div class="mt-1 form-group">

                        @foreach($shippingMode as $sindex => $shipping)
                            @php
                            $checked = '';
                            if(isset($delivery[$booking->id])){
                                if(in_array($shipping->id, $delivery[$booking->id])){
                                    $checked = 'checked';
                                }else{
                                    $checked = '';

                                }
                            }
                            @endphp
                            <fieldset class="checkbox-inline mr-1">
                                <input type="checkbox" id="updatedelivery[{{$booking->id}}][{{$shipping->id}}]" name="updatedelivery[{{$booking->id}}][{{$shipping->id}}]" {{$checked}}  class="icheckbox shippingmode booking_{{$booking->id}}_shipping_mode" {{isset($delivery[$booking->id])? '':'disabled'}}>
                                <label for="updatedelivery[{{$booking->id}}][{{$shipping->id}}]" class="">{{ucfirst($shipping->mode)}}</label>
                            </fieldset>

                        @endforeach
                            <p class="text-danger" id="shipping_error{{$booking->id}}" style="display:none;">Please select atleast one option</p>

                    </div>
                </div>

            @endforeach

        </div>
    </div>
    <div>


    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-warning btn-min-width btn-glow mr-1 mb-1" id="confirmAction">Update City</button>
        <button type="button" class="btn btn-primary btn-min-width btn-glow mr-1 mb-1" data-dismiss="modal">Cancel</button>

    </div>


</form>

<script type="text/javascript">
    $(document).ready(function () {
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
        $('#hub_list').prepend('<option value="" selected="selected"></option>').select2({
            placeholder: 'Select a Hub',
            dropdownParent: $("#editCity")
        });

        $('#zone').select2({
            placeholder: 'Zone',
            width:'100%'
        });

        var city_selected = '{!! isset($cityhub[0])? $cityhub[0]->id:''; !!}';
                $('#hub_list').val(city_selected).trigger('change');
        $("input[type='radio'][name='city-radio']").on('ifChecked', function(event){
            var rtype = $(this).attr('rel');
            var id = '{!! $city->id !!}';
            if(rtype == 'city'){
                var isHub = {{ $isHub }};
                if(isHub == 1){
                    $.ajax({
                        url:'/admin/management/city/'+id+'/status/ajax',
                        type:'GET',
                        dataType:'json',
                    }).done(function (data) {
                        var name = [];
                        if(data.length > 0){
                            $('#editCity').modal('hide');
                            var comma = '';
                            $.each(data, function (index, value) {
                                if(data.length != index+1){ comma = ", ";}else{
                                    comma = '';
                                }
                                name += value.name+comma;

                            });
                            swal({
                                title: 'Please remove following cities from hub!',
                                text: name,
                                icon: 'info',
                                buttons: {
                                    cancel: {
                                        text: 'Close',
                                        value: null,
                                        visible: true,
                                        closeModal: true,
                                    }
                                },
                                closeOnClickOutside: true,
                                closeOnEsc: true
                            });

                        }else{
                            $('#city_type').val('city');
                            if($('#hub_list_div').is(':hidden')){
                                // $('#hub_list_div').css('display','block');
                                $('#hub_list_div').fadeIn("slow");

                                $('#zone_selection').addClass('d-none');
                            }
                        }
                    });
                }else{

                    $('#city_type').val('city');
                    if($('#hub_list_div').is(':hidden')){
                        // $('#hub_list_div').css('display','block');
                        $('#hub_list_div').fadeIn("slow");

                        $('#zone_selection').addClass('d-none');
                    }
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

        @foreach($bookings as $booking)
        $('input.bookingtype{{$booking->id}}').on('ifChecked',function () {
            var shippingmode = $(this).parent().parent().next().find('input.shippingmode');
            checkAtleastOne($(this),{{$booking->id}});
            $(shippingmode).iCheck('enable');
        });
        $('input.bookingtype{{$booking->id}}').on('ifUnchecked',function () {
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


        $( "#editCityHubForm" ).validate({


            errorClass:"danger",
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parents('.form-group'));
            },
            submitHandler: function(form) {
                // $(form).find('button[type=submit]').attr('disabled', 'disabled');

                if(errors === 1){
                    return false;
                }else{
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    swal({
                        title: 'Please Wait!',
                        text: 'City/Hub is being updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }

            }
        });


    });
</script>