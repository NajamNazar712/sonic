
<form action="{{route('admin.management.international.city.edit',['id'=>$city->id])}}" method="post" class="mt-2" id="editCityHubForm" novalidate="novalidate">
    {{csrf_field()}}
    <input type="hidden" name="_method" value="PUT">
    <div class="row mb-2">
        <div class="col @if($city->hub == 1)d-none @endif" id="city_name_div">
            <fieldset class="form-group">
                <input type="text" class="form-control" name="cityName" value="{{$city->name}}" placeholder="Add City Name" required data-rule-required="true" data-msg-required="This field is required">
            </fieldset>
        </div>
       

        <div class="col @if($city->hub == 0)d-none @endif" id="country_name_div">
            <fieldset class="form-group">
                <input type="text" class="form-control" name="countryName" value="{{$city->name}}" placeholder="Add Country Name" required data-rule-required="true" data-msg-required="This field is required">
            </fieldset>
        </div>
        
        <div class="col">
            <fieldset class="form-group">
                <input type="text" class="form-control" name="city_code" value="{{$city->city_code}}" placeholder="Add City Code">
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

    <div class="row mb-2" id="international_hub_list_div" style="display:{{($isHub == 1)? 'none':''}}">
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

    <div class="mb-2 {{(($isHub == 0) ? 'd-none' : '')}}" id="international_zone_selection">
        <div class="row">
            <div class="col-3">
                <fieldset class="form-group">
                    <input type="text" class="form-control" name="iata_code" value="{{$city->iata_code}}" placeholder="Add Iata Code" data-rule-required="true" data-msg-required="Iata Code is required">
                </fieldset>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <fieldset class="form-group">
                    <select name="zone_id" id="international_zone" class="form-control select2" data-rule-required="true" data-msg-required="Zone is required">
                        @foreach($zones as $zone)
                            <option value="{{ $zone->id }}" @if ($zone->id == $city->zone_id) selected="selected" @endif>{{ $zone->name }}</option>
                        @endforeach
                    </select>
                </fieldset>
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-6">
            <fieldset class="form-group">
                <input type="text" name="attempt_tat" id="attempt_tat" class="form-control attempt_tat" value="{{$city->attempt_tat}}" placeholder="Add Attempt Tat*" required data-rule-required="true" data-msg-required="This field is required" data-rule-min="1" data-msg-min="Attempt tat can not be less than 1">
            </fieldset>
        </div>
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-warning btn-min-width mr-1 mb-1" id="confirmAction">Update</button>
        <button type="button" class="btn btn-primary btn-min-width mr-1 mb-1" data-dismiss="modal">Cancel</button>

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
        $('#international_hub_list').prepend('<option value="" selected="selected"></option>').select2({
            placeholder: 'Select a Hub',
            dropdownParent: $("#editCity")
        });

        $('#international_zone').select2({
            placeholder: 'Zone',
            width:'100%'
        });

        var city_selected = '{!! isset($cityhub[0])? $cityhub[0]->id:''; !!}';
        $('#international_hub_list').val(city_selected).trigger('change');
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
                            if($('#international_hub_list_div').is(':hidden')){
                                $('#international_hub_list_div').fadeIn("slow");

                                $('#international_zone_selection').addClass('d-none');
                                $('#country_name_div').addClass('d-none');
                                $('#city_name_div').removeClass('d-none');
                            }
                        }
                    });
                }else{

                    $('#city_type').val('city');
                    if($('#international_hub_list_div').is(':hidden')){
                        // $('#international_hub_list_div').css('display','block');
                        $('#international_hub_list_div').fadeIn("slow");

                        $('#international_zone_selection').addClass('d-none');
                        $('#country_name_div').addClass('d-none');
                        $('#city_name_div').removeClass('d-none');
                    }
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


        $( "#editCityHubForm" ).validate({
            errorClass:"danger",
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parents('.form-group'));
            },
            submitHandler: function(form) {
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
        });


    });
</script>