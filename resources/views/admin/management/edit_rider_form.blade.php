{{--/**--}}
 {{--* Created by PhpStorm.--}}
 {{--* User: WaqasTrax--}}
 {{--* Date: 6/1/2018--}}
 {{--* Time: 1:19 PM--}}
 {{--*/--}}

{{--/**--}}
{{--* Created by PhpStorm.--}}
{{--* User: WaqasTrax--}}
{{--* Date: 6/1/2018--}}
{{--* Time: 8:16 AM--}}
{{--*/--}}
{{--/**--}}
{{--* Created by PhpStorm.--}}
{{--* User: WaqasTrax--}}
{{--* Date: 5/30/2018--}}
{{--* Time: 12:00 PM--}}
{{--*/--}}
<style>
    textarea#address {
        resize: none;
    }
</style>
{{--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBMo9kqvMhqVAe_GCXZXOfzfAZ_oeBapkQ&callback=initMap" type="text/javascript"></script>--}}
<form action="{{route('admin.management.rider.edit',['id'=>$rider_id])}}" method="post" class="mt-2" id="editRiderForm" novalidate="novalidate">
    @csrf
    @method('PUT')

    <div class="row mb-2">
        <div class="col">
            <fieldset class="form-group">
                <select name="city_id" id="city_list" class="form-control select2" style="width: 100%;" required data-rule-required="true" data-msg-required="This field is required">
                    <option value="{{$rider->city->id}}" selected>{{$rider->city->name}}</option>
                    @foreach($cities as $city)
                        <option value="{{$city->id}}">{{$city->name}}</option>
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
                    <input type="text" class="form-control" name="phone"  value="{{$rider->phone}}" placeholder="Phone No." required data-rule-required="true" data-msg-required="This field is required">
                </fieldset>
            </div>
            <div class="col">
                <fieldset class="form-group">
                    <input type="text" class="form-control" name="cnic"  value="{{$rider->cnic}}" placeholder="CNIC" required data-rule-required="true" data-msg-required="This field is required">
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
        <div class="row">
            <div class="col">
                <fieldset class="form-group">
                    <select name="route_id" id="route_list" class="form-control select2" style="width: 100%;" required data-rule-required="true" data-msg-required="This field is required">
                        <option value="{{$rider->route->id}}" selected>{{$rider->route->code}} ({{$rider->route->start}} to {{$rider->route->end}})</option>

                    </select>
                </fieldset>
            </div>
            <div class="col">
                <fieldset class="form-group">
                    <select name="rider_category" id="category_list" class="form-control select2" style="width: 100%;" required data-rule-required="true" data-msg-required="This field is required">
                        <option value="{{$rider->rider_category->id}}" selected>{{$rider->rider_category->name}}</option>
                        @foreach($categories as $category)
                            <option value="{{$category->id}}">{{$category->name}}</option>
                        @endforeach
                    </select>
                </fieldset>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-warning btn-min-width btn-glow mr-1 mb-1" id="confirmAction">Update Rider</button>
        <button type="button" class="btn btn-primary btn-min-width btn-glow mr-1 mb-1" data-dismiss="modal">Cancel</button>

    </div>
</form>
<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

<script type="text/javascript">
    $(document).ready(function () {

        $('.select2').select2({
            dropdownParent: $("#editRider")
        });
        $("input[name='cnic']").inputmask({'mask': "99999-9999999-9", 'clearIncomplete': true});
        $("input[name='phone']").inputmask({'mask': "9999-9999999", 'clearIncomplete': true});
        //$('#riderInfoDiv input,#riderInfoDiv textarea,#riderInfoDiv select').attr('disabled','disabled');

        $('#city_list').change(function () {

        });
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
                    for(var i = 0; i < data.length; i++){
                        var option = new Option(data[i].code+' ('+data[i].start+' - '+data[i].end+')', data[i].id, true, true);
                        routelist.append(option).trigger('change');
                    }
                }
            });
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
                    text: 'Route is being added!',
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