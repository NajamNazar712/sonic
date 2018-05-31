{{--/**--}}
 {{--* Created by PhpStorm.--}}
 {{--* User: WaqasTrax--}}
 {{--* Date: 5/30/2018--}}
 {{--* Time: 1:59 PM--}}
 {{--*/--}}
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
<form action="{{route('admin.management.route.edit',['id'=>$route_id])}}" method="post" class="mt-2" id="editRouteForm" novalidate="novalidate">
    @csrf
    @method('PUT')
    <div class="row mb-2">
        <div class="col">
            <fieldset class="form-group">
                <select name="city_id" id="city_id" class="form-control select2" style="width: 100%;" required data-rule-required="true" data-msg-required="This field is required">
                    <option value="{{$route->city->id}}" selected>{{$route->city->name}}</option>
                    @foreach($cities as $c)
                        <option value="{{$c->id}}">{{$c->name}}</option>
                    @endforeach
                </select>
            </fieldset>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col">
            <fieldset class="form-group">
                <input type="text" class="form-control" name="route_code" value="{{$route->code}}" placeholder="Route Code" required data-rule-required="true" data-msg-required="This field is required">
            </fieldset>

        </div>
        <div class="col">
            <fieldset class="form-group">
                <input type="text" class="form-control" name="start" value="{{$route->start}}" placeholder="Start Point" required data-rule-required="true" data-msg-required="This field is required">
            </fieldset>
        </div>
        <div class="col">
            <fieldset class="form-group">
                <input type="text" class="form-control" name="end" value="{{$route->end}}"  placeholder="End Point" required data-rule-required="true" data-msg-required="This field is required">
            </fieldset>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col">
            <fieldset class="form-group">
                <textarea name="junction" class="form-control" placeholder="Add Junctions (comma seperated)" id="junction" cols="30" rows="5" required data-rule-required="true" data-msg-required="This field is required">{{$route->junction}}</textarea>
            </fieldset>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-warning btn-min-width btn-glow mr-1 mb-1" id="">Update Route</button>
        <button type="button" class="btn btn-primary btn-min-width btn-glow mr-1 mb-1" data-dismiss="modal">Cancel</button>

    </div>
</form>

<script type="text/javascript">

    $('.select2').select2({
        dropdownParent: $("#editRoute")
    });

    $( "#editRouteForm" ).validate({

        errorClass:"danger",
        errorPlacement: function(error, element) {
            error.addClass('w-100').appendTo(element.parent('.form-group'));
        },
        submitHandler: function(form) {

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
</script>