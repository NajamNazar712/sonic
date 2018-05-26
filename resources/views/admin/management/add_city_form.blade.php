
<form action="{{route('admin.management.city')}}" method="post" class="mt-2" id="addCityHubForm" novalidate="novalidate">
    {{csrf_field()}}

    <div class="row mb-2">
        <div class="col">
            <fieldset class="form-group">
                <input type="text" class="form-control" name="cityName" placeholder="Add City Name" required data-rule-required="true" data-msg-required="This field is required">
            </fieldset>
        </div>
        <div class="col">
            <input type="hidden" id="city_type" name="postType" value="city">
            <fieldset class="radio-inline ml-1">
                <input type="radio" name="city-radio" class="icheck cradio" id="city-radio" rel="city" checked>
                <label for="city-radio">City</label>
            </fieldset>
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
                        <option value="" selected>Select a hub</option>
                        @foreach($hubs as $hub)
                            <option value="{{$hub->hub_id}}">{{$hub->name}}</option>
                        @endforeach
                    </select>
            </fieldset>
        </div>
    </div>

    <div class="row">
        <div class="col-4">
            <h2 class="card-title"><U>Services</U></h2>
        </div>
        <div class="col-8">
            <fieldset class="mt-1">
                <input type="checkbox" name="pickup" class="icheckbox">
                <label for="pickup" class="">Pickup</label>
            </fieldset>
        </div>
    </div>
    <div class="row mb-2">

        <div class="col-12">
            <h4 class="card-title">Delivery</h4>
            @foreach($booking as $booking)
            <div class="bs-callout-primary callout-border-left callout-square p-1">
                <strong>{{$booking->booking_type}}&nbsp;<input type="checkbox" name="booking[{{$booking->id}}]" class="icheckbox bookingtype"></strong>
                <div class="mt-1">
                @foreach($shippingMode as $shipping)
                    <fieldset class="checkbox-inline mr-1">
                        <input type="checkbox" name="delivery[{{$booking->id}}][{{$shipping->id}}]" class="icheckbox shippingmode" disabled>
                        <label for="delivery[{{$booking->id}}][{{$shipping->id}}]" class="">{{ucfirst($shipping->mode)}}</label>
                    </fieldset>
                    @endforeach
                </div>
            </div>
            @endforeach

        </div>
    </div>


    <div class="modal-footer">
        <button type="submit" class="btn btn-warning btn-min-width btn-glow mr-1 mb-1" id="confirmAction">Add City</button>
        <button type="button" class="btn btn-primary btn-min-width btn-glow mr-1 mb-1" data-dismiss="modal">Cancel</button>

    </div>


</form>

<script type="text/javascript">
    $(document).ready(function () {


    $('input.icheck').iCheck({
        checkboxClass: 'icheckbox_square-red',
        radioClass: 'iradio_square-red'
    });
    $('input.icheckbox').iCheck({
        checkboxClass: 'icheckbox_flat-red',
        radioClass: 'iradio_flat-red',
        increaseArea: '20%' // optional
    });
    $('.select2').select2();

    $("input[type='radio'][name='city-radio']").on('ifChecked', function(event){
               var rtype = $(this).attr('rel');
               if(rtype == 'city'){
                   $('#city_type').val('city');
                   if($('#hub_list_div').is(':hidden')){
                       // $('#hub_list_div').css('display','block');
                       $('#hub_list_div').fadeIn("slow");
                   }
               }else if(rtype == 'hub'){
                   $('#city_type').val('hub');

                   if(!$('#hub_list_div').is(':hidden')){
                       $('#hub_list_div').fadeOut("slow");

                       // $('#hub_list_div').fadeIn('slow');
                   }

               }
    });

    $('input.bookingtype').on('ifChecked',function () {
        var shippingmode = $(this).parent().parent().next().find('input.shippingmode');

        $(shippingmode).iCheck('enable');
    });
        $('input.bookingtype').on('ifUnchecked',function () {
            var shippingmode = $(this).parent().parent().next().find('input.shippingmode');

            $(shippingmode).iCheck('disable');
        });

        $( "#addCityHubForm" ).validate({
            errorClass:"danger",
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
            submitHandler: function(form) {
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
        });


    });
</script>