
<form action="{{route('admin.management.international.city')}}" method="post" class="mt-2" id="addCityHubForm" novalidate="novalidate">
    {{csrf_field()}}

    <div class="row mb-2">
        <div class="col" id="city_name_div">
            <fieldset class="form-group">
                <input type="text" class="form-control" name="cityName" placeholder="Add City Name*" required data-rule-required="true" data-msg-required="This field is required">
            </fieldset>
            
        </div>
       
        
        <div class="col d-none" id="country_name_div">
            <fieldset class="form-group">
                <input type="text" class="form-control" name="countryName" placeholder="Add Country Name*" required data-rule-required="true" data-msg-required="This field is required">
            </fieldset>
        </div>
        <div class="col">
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


    <div class="row mb-2" id="international_hub_list_div" >
        <div class="col-6">
            <fieldset class="form-group">
                <select name="hubs" id="international_hub_list" class="form-control select2" style="width: 100%;" required data-rule-required="true" data-msg-required="This field is required">

                    @foreach($hubs as $hub)
                        <option value="{{$hub->hub_id}}">{{$hub->name}}</option>
                    @endforeach
                </select>
            </fieldset>
        </div>
    </div>

    <div class="mb-2 d-none" id="international_zone_selection">
        <div class="row">
            <div class="col-6">
                <fieldset class="form-group">
                    <input type="text" class="form-control" name="iata_code" placeholder="Add Iata Code" data-rule-required="true" data-msg-required="Iata Code is required">
                </fieldset>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <fieldset class="form-group">
                    <select name="zone_id" id="international_zone" class="form-control select2" data-rule-required="true" data-msg-required="Zone is required">
                        @foreach($zones as $zone)
                            <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                        @endforeach
                    </select>
                </fieldset>
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-6">
            <fieldset class="form-group">
                <input type="text" name="attempt_tat" id="attempt_tat" class="form-control attempt_tat" placeholder="Add Attempt Tat*" required data-rule-required="true" data-msg-required="This field is required" data-rule-min="1" data-msg-min="Attempt tat can not be less than 1">
            </fieldset>
        </div>
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-warning btn-min-width btn-glow mr-1 mb-1" id="confirmAction">Add</button>
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
        $('input.icheck').iCheck({
            checkboxClass: 'icheckbox_square-red',
            radioClass: 'iradio_square-red'
        });
        $('input.icheckbox').iCheck({
            checkboxClass: 'icheckbox_flat-red',
            radioClass: 'iradio_flat-red',
            increaseArea: '20%' // optional
        });

        $('#international_hub_list').prepend('<option value="" selected></option>').select2({
            placeholder: 'Select Hub',
            dropdownParent: $("#addInternationalCity")
        });


        $('#international_zone').prepend('<option value="" selected></option>').select2({
            placeholder: 'Zone',
            dropdownParent: $("#addInternationalCity"),
            width:'100%'
        });

        $("input[type='radio'][name='city-radio']").on('ifChecked', function(event){
            var rtype = $(this).attr('rel');
            if(rtype == 'city'){
                $('#city_type').val('city');
                if($('#international_hub_list_div').is(':hidden')){
                    // $('#international_hub_list_div').css('display','block');
                    $('#international_hub_list_div').fadeIn("slow");

                    $('#international_zone_selection').addClass('d-none');
                    $('#country_name_div').addClass('d-none');
                    $('#city_name_div').removeClass('d-none');
                }
            }else if(rtype == 'hub'){
                $('#city_type').val('hub');

                if(!$('#international_hub_list_div').is(':hidden')){
                    $('#international_hub_list_div').fadeOut("slow");

                    $('#international_zone_selection').removeClass('d-none');
                    $('#country_name_div').removeClass('d-none');
                    $('#city_name_div').addClass('d-none');
                }

            }
        });


        $( "#addCityHubForm" ).validate({
            errorClass:"danger",
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parents('.form-group'));
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