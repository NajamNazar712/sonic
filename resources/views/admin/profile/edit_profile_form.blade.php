
<form id="editProfileForm" class="form form-horizontal" method="post" action="{{route('admin.update.profile.submit')}}">
    @csrf
    <div class="form-body">
        <div class="form-group col">
            <label>Blood Group</label>
            <select name="blood_group" id="blood_group" class="select2 form-control" data-rule-required="true" data-msg-required="This field is required" style="width: 100%" >
                @foreach($blood_groups as $blood_group)
                    <option value="{{$blood_group->id}}">{{$blood_group->name}}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group col">
            <label>Emergency Contact Person<span class="text-danger">*</span></label>
            <input type="text" id="emergency_contact_name" data-rule-required="true"  data-msg-required="Emergency Contact Person is required" class="form-control" value="{{$employee->emergency_contact_person}}" name="emergency_contact_name">
        </div>
        <div class="form-group col">
            <label>Emergency Contact No.<span class="text-danger">*</span></label>
            <input type="text" id="emergency_contact_no" data-rule-required="true"  data-msg-required="Emergency Contact is required" class="form-control" value="{{$employee->emergency_contact}}" name="emergency_contact_no">
        </div>
    </div>
    <div class="form-actions center">
        <button type="submit" class="btn btn-primary">
            Update
        </button>
    </div>
</form>


<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#editProfileForm #emergency_contact_no').inputmask({
            'mask': '9999-9999999',
            'clearIncomplete': true
        });

        $("#editProfileForm #blood_group").prepend('<option value="" selected></option>').select2({
            placeholder: "Select Blood Group",
            width:'100%',
        });
        $("#blood_group").val("{{$employee->blood_group ?? ''}}").trigger('change');

        $("#editProfileForm").validate({

            errorClass: "danger",
            errorPlacement: function (error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
            submitHandler: function (form) {

                $(form).find('button[type=submit]').attr('disabled', 'disabled');
                swal({
                    title: 'Please Wait!',
                    text: 'Profile is being updated!',
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