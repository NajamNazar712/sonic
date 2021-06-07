<form id="edit_user_form" class="form-horizontal mb-1 justify-content-center" method="POST" action="{{ route('admin.retail.users.update',['id'=>$retail_user->id]) }}" novalidate="novalidate">
    {{ method_field('PUT') }}
    {{ csrf_field()  }}
    @if ($retail_user->category==1)
    <div class="form-group">
        <select name="store" id="edit_store" disabled class="form-control select2" data-rule-required="true" data-msg-required="Franchise is required">
            <option value="1" selected>Franchise</option>
        </select>
    </div>
    <div class="form-group" id="franchise_div">
        <select name="franchise" id="edit_franchise" disabled class="form-control select2" data-rule-required="true" data-msg-required="Franchise is required">
            
                <option value="{{$retail_user->store->id}}"> {{$retail_user->store->name}} </option>
            
        </select>
    </div>
    @else
    <div class="form-group">
        <select name="store" id="edit_store" disabled class="form-control select2" data-rule-required="true" data-msg-required="Franchise is required">
            <option value="2" selected>Trax Center</option>
        </select>
    </div>
    <div class="form-group" id="franchise_div">
        <select name="trax_center" id="edit_trax_center" disabled class="form-control select2" data-rule-required="true" data-msg-required="Trax Center is required">
                <option value="{{$retail_user->store->id}}"> {{$retail_user->store->name}} </option>
        </select>
    </div>
    @endif
    
   
   
    <div class="form-group">
        <input type="text" name="name" id="edit_name" class="form-control" value="{{$retail_user->name}}" placeholder="User Name*" data-rule-required="true" data-msg-required="Name is required" data-rule-remote="{{ route('admin.retail.users.edit.name',['id'=>$retail_user->id]) }}" data-msg-remote="Name must be unique">
    </div>
    <div class="form-group">
        <input type="text" name="phone_number" id="edit_phone_number" value="{{$retail_user->phone_no}}" class="form-control phone_number" placeholder="Phone Number*" data-rule-required="true" data-msg-required="Phone Number is required">
    </div>
    <div class="form-group position-relative">
        <input type="password" class="form-control" id="edit_password" placeholder="Password" value="" name="password" data-rule-required="true" data-msg-required="Password is required" data-rule-minlength="6" data-msg-minlength="Password needs to be at-least 6 Characters" autocomplete="nope">
        <div class="form-control-position" id="edit_eye">
            <i class="la la-eye success"></i>
        </div>
    </div>
    <div class="form-group">
        <input type="text" name="cnic" id="edit_cnic" class="form-control cnic" value="{{$retail_user->cnic}}" placeholder="CNIC*" data-rule-required="true" data-msg-required="CNIC is required">
    </div>
    <div class="form-group">
        <textarea name="address" id="edit_address" class="form-control address" placeholder="Address*" data-rule-required="true" data-msg-required="Address is required">{{$retail_user->address}}</textarea>
    </div>
    <div class="form-group ml-1">
        <button type="submit" name="update" class="btn btn-primary add" value="Update">Update</button>
    </div>
</form>


<script type="text/javascript">
    $(document).ready(function () {
        $('#edit_eye').on('mousedown',function(){$('input[name="password"]').attr('type','text')}).on('mouseup',function(){$('input[name="password"]').attr('type','password')});
            $('#peye').on('mousedown',function(){$('input[name="password"]').attr('type','text')}).on('mouseup',function(){$('input[name="password"]').attr('type','password')});


            $('#edit_user_form').validate({
                ignore: ":not(:visible),:disabled",
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    swal({
                        title: 'Please Wait!',
                        text: 'Updating User!',
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