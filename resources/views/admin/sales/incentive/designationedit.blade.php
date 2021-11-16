<form id="edit_designation_form" class="form-horizontal mb-1 justify-content-center" method="POST" action="{{ route('admin.sales.designation.update',['id'=>$designation->id]) }}"  novalidate="novalidate">
    {{ method_field('PUT') }}
    {{ csrf_field()  }}
    <div class="form-group">
        <select name="designation" id="designation" class="form-control select2"  data-msg-required="Designation Name is required">
            @foreach($roles as $role)
                @if($designation->designation==$role->id)
                <option selected value="{{$role->id}}"> {{$role->name}} </option>
                @else
                    <option value="{{$role->id}}"> {{$role->name}} </option>
            @endif
        @endforeach
        </select>
    </div>
    <div class="form-group">
        <input type="text" name="edit_code" id="edit_code" maxlength="50" class="form-control" value="{{$designation->code}}" placeholder="Designation Code*" data-rule-required="true" data-msg-required="Designation Code is required">
    </div>
    <div class="form-group ml-1">
        <button type="submit" name="update" class="btn btn-primary add" value="Update">Update</button>
    </div>
</form>
<script type="text/javascript">
    $(document).ready(function () {
        $('#edit_designation_form #designation').select2({
            width: '100%',
            placeholder: 'Select Role*',
            allowClear:false,
            dropdownParent:$('#edit_designation_form')
        });

        $('#edit_designation_form').validate({
            ignore: ":not(:visible),:disabled",
            errorClass: 'danger',
            successClass: 'success',
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parents('.form-group'));
            },
            submitHandler: function(form) {
                swal({
                    title: 'Please Wait!',
                    text: 'Updating Designation!',
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