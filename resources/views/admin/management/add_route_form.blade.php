{{--/**--}}
 {{--* Created by PhpStorm.--}}
 {{--* User: WaqasTrax--}}
 {{--* Date: 5/30/2018--}}
 {{--* Time: 12:00 PM--}}
 {{--*/--}}
<style>
    textarea#junction {
        resize: none;
    }
</style>
{{--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBMo9kqvMhqVAe_GCXZXOfzfAZ_oeBapkQ&callback=initMap" type="text/javascript"></script>--}}
<form action="{{route('admin.management.route.add')}}" method="post" class="mt-2" id="addRouteForm" novalidate="novalidate">
{{csrf_field()}}
    {{--<div class="row">--}}
        {{--<div class="col-md-6">--}}
    <div class="row mb-2">
        <div class="col">
            <fieldset class="form-group">
                <select name="city_id" id="city_list" class="form-control select2" style="width: 100%;" required data-rule-required="true" data-msg-required="This field is required">
                    <option value="" selected>Select a City</option>
                    @foreach($cities as $city)
                        <option value="{{$city->id}}">{{$city->name}}</option>
                    @endforeach
                </select>
            </fieldset>
        </div>
    </div>

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
              <textarea name="junction" class="form-control" placeholder="Add Junctions (comma seperated)" id="junction" cols="30" rows="5" required data-rule-required="true" data-msg-required="This field is required"></textarea>
          </fieldset>
        </div>
    </div>
            {{--<div class="col-md-6">--}}
                {{--<div id="map_canvas"></div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
    <div class="modal-footer">
        <button type="submit" class="btn btn-warning btn-min-width btn-glow mr-1 mb-1" id="confirmAction">Add Route</button>
        <button type="button" class="btn btn-primary btn-min-width btn-glow mr-1 mb-1" data-dismiss="modal">Cancel</button>

    </div>
</form>

<script type="text/javascript">
    $(document).ready(function () {

        $('.select2').select2({
            dropdownParent: $("#addRoute")
        });

        $("#addRouteForm").validate({

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