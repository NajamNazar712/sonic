<form id="edit_user_form" class="form-horizontal mb-1 justify-content-center" method="POST" action="{{ route('admin.retail.users.update',['id'=>$retail_user->id]) }}" novalidate="novalidate">
    {{ method_field('PUT') }}
    {{ csrf_field()  }}

    <div class="form-group">
        <select name="store" id="edit_store"  class="form-control select2" data-rule-required="true" data-msg-required="Franchise is required">
            @if ($retail_user->category==1)
                <option value="1" selected>Franchise</option>
                <option value="2" >Trax Center</option>
            @else
                <option value="1">Franchise</option>
                <option value="2" selected>Trax Center</option>
            @endif
        </select>
    </div>

    @if ($retail_user->category==1)
    <div class="form-group" id="franchise_div">
        <select name="franchise" id="edit_franchise"  class="form-control select2" data-rule-required="true" data-msg-required="Franchise is required">
                {{--<option value="{{$retail_user->store->id}}"> {{$retail_user->store->name}} </option>--}}
            @foreach($franchises as $franchise)
                <option value="{{$franchise->id}}"> {{$franchise->name}} </option>
            @endforeach
        </select>
    </div>
   @else
        <div class="form-group" id="trax_center_div">
            <select name="trax_center" id="edit_trax_center" class="form-control select2" data-rule-required="true" data-msg-required="Trax Center is required">
             {{--   <option value="{{$retail_user->store->id}}"> {{$retail_user->store->name}} </option>--}}
                @foreach($trax_centers as $trax_center)
                    <option value="{{$trax_center->id}}"> {{$trax_center->name}} </option>
                @endforeach
            </select>
        </div>
    @endif

        {{--  <div class="form-group">
             <select name="store" id="edit_store"  class="form-control select2" data-rule-required="true" data-msg-required="Franchise is required">
                 <option value="2" selected>Trax Center</option>
             </select>
         </div>

         @endif--}}
    
   
   
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
            $("#edit_cnic").inputmask({'mask': "99999-9999999-9", 'clearIncomplete': true});
            $('#edit_phone_number').inputmask({
                'mask': '9999-9999999',
                'clearIncomplete': true
            });

        $('#edit_franchise').select2({
            width: '100%',
            allowClear:true
        });
        $('#edit_trax_center').select2({
            width: '100%',
            allowClear:true
        });


            var store_id = @json($retail_user->store->id);

             @if ($retail_user->category==1)
                $('#edit_franchise').val(store_id).trigger('change');
             @else
                 $('#edit_trax_center').val(store_id).trigger('change');
             @endif

            $('#edit_store').select2({
                width: '100%',
                allowClear:true
            }).bind('change', function () {
                var id = parseInt($(this).val());
                if (id == 1) {
                    $('#trax_center_div').addClass('d-none');
                    $('#franchise_div').removeClass('d-none');

                } else if (id == 2) {
                    $('#trax_center_div').removeClass('d-none');
                    $('#franchise_div').addClass('d-none');
                } else {
                    $('#trax_center_div').addClass('d-none');
                    $('#franchise_div').addClass('d-none');
                }
            });

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