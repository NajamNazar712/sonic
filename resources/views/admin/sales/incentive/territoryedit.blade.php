<form id="edit_territory_form" class="form-horizontal mb-1 justify-content-center" method="POST" action="{{ route('admin.sales.territory.update',['id'=>$territory->id]) }}"  novalidate="novalidate">
    {{ method_field('PUT') }}
    {{ csrf_field()  }}
    <div class="form-group">
        <input type="text" name="edit_name" id="edit_name" class="form-control" value="{{$territory->name}}" maxlength="50" placeholder="Territory Name*" data-rule-required="true" data-msg-required="Territory Name is required" data-msg-remote="Territory Name must be unique">
    </div>
    <div class="form-group">
        <select name="edit_city" id="edit_city" class="form-control select2" data-rule-required="true" data-msg-required="City is required">
            @foreach($hubs as $city)
                @if($city->id==$territory->cityid)
                    <option selected value="{{$city->id}}"> {{$city->name}} </option>
                @else
                    <option value="{{$city->id}}"> {{$city->name}} </option>
                @endif
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <input type="text" name="edit_code" id="edit_code" class="form-control" maxlength="50" value="{{$territory->code}}" placeholder="Territory Code*" data-rule-required="true" data-msg-required="Territory Code is required">
    </div>

    @if(count($designations) > 0)
        @foreach($designations as $designation)
            <div class="form-group">
                <select name="designations[{{$designation->id}}][]" id="designation_{{$designation->id}}" class="form-control select2" multiple="multiple">
                    @foreach($admins as $admin)
                        @if($admin->role_id == $designation->designation)
                            <option value="{{$admin->id}}"> {{$admin->name}} </option>
                        @endif
                    @endforeach
                </select>
            </div>
        @endforeach
    @endif
    <div class="form-group ml-1">
        <button type="submit" name="update" class="btn btn-primary add" value="Update">Update</button>
    </div>
</form>
<script type="text/javascript">
    $(document).ready(function () {


        @if(count($designations) > 0)
            var designation_admins = [];
            @foreach($designations as $designation)
                var designation_id = @json($designation->id);
                var designation_code = @json($designation->code);
                $('#edit_territory_form #designation_' + designation_id).select2({
                    width: '100%',
                    placeholder: 'Select ' + designation_code,
                    allowClear:false,
                    dropdownParent:$('#edit_territory_form')
                });
                designation_admins[designation_id] = [];
            @endforeach
            @foreach($territory_admins as $territory_admin)
                var designation_id = @json($territory_admin->designation_id);
                var admin_id = @json($territory_admin->admin_id);
                designation_admins[designation_id].push(admin_id);
            @endforeach
            $.each(designation_admins, function(designation_id, admin_ids) {
                $('#edit_territory_form #designation_' + designation_id).val(admin_ids).trigger('change');
            });
        @endif

        $('#edit_city').select2({
            width: '100%',
            placeholder: 'Select City*',
            allowClear:false,
            dropdownParent:$('#edit_territory_form')
        }).bind('select2:select', function () {
            var city_id = parseInt($(this).val());
            if(city_id != null && city_id != ''){
                $.ajax({
                    url: '{!! route('admin.sales.territory.city_admins') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'city_id': city_id,
                    }
                })
                    .done(function(data) {
                                @if(count($designations) > 0)
                                @foreach($designations as $designation)
                        var designation_id = @json($designation->id);
                        var designation_code = @json($designation->code);
                        $('#edit_territory_form #designation_' + designation_id).html('').select2('destroy');

                        $('#edit_territory_form #designation_' + designation_id).select2({
                            width: '100%',
                            placeholder: 'Select ' + designation_code,
                            allowClear:false,
                            dropdownParent:$('#edit_territory_form')
                        });
                        if(data.designations.hasOwnProperty(designation_id)){
                            $.each(data.designations[designation_id].admins, function (index, admin) {
                                $('#edit_territory_form #designation_' + designation_id).append('<option value="' + admin['id'] + '">' + admin['name'] + '</option>');
                            });
                        }
                        @endforeach
                        @endif
                    });
            }
        });

        @if(count($designations) > 0)
            var designation_admins = [];
            @foreach($designations as $designation)
                var designation_id = @json($designation->id);
                var designation_code = @json($designation->code);
                $('#edit_territory_form #designation_' + designation_id).select2({
                    width: '100%',
                    placeholder: 'Select ' + designation_code,
                    allowClear:false,
                    dropdownParent:$('#edit_territory_form')
                });
                designation_admins[designation_id] = [];
            @endforeach
            @foreach($territory_admins as $territory_admin)
                var designation_id = @json($territory_admin->designation_id);
                var admin_id = @json($territory_admin->admin_id);
                designation_admins[designation_id].push(admin_id);
            @endforeach
            $.each(designation_admins, function(designation_id, admin_ids) {
                $('#edit_territory_form #designation_' + designation_id).val(admin_ids).trigger('change');
            });
        @endif

        $('#edit_territory_form').validate({
            ignore: ":not(:visible),:disabled",
            errorClass: 'danger',
            successClass: 'success',
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parents('.form-group'));
            },
            submitHandler: function(form) {
                swal({
                    title: 'Please Wait!',
                    text: 'Updating Territory!',
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