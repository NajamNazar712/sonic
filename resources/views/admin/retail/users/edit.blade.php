<form id="edit_user_form" class="form-horizontal mb-1 justify-content-center" method="POST" action="{{ route('admin.retail.users.update',['id'=>$retail_user->id]) }}" novalidate="novalidate">
    {{ method_field('PUT') }}
    {{ csrf_field()  }}

    <div class="container">
        <div class="row">
            <div class="col">
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

                <div class="form-group" id="franchise_div">
                    <select name="franchise" id="edit_franchise"  class="form-control select2" data-rule-required="true" data-msg-required="Franchise is required">
                            {{--<option value="{{$retail_user->store->id}}"> {{$retail_user->store->name}} </option>--}}
                        @foreach($franchises as $franchise)
                            <option value="{{$franchise->id}}"> {{$franchise->name}} </option>
                        @endforeach
                    </select>
                </div>
            
                <div class="form-group" id="trax_center_div">
                    <select name="trax_center" id="edit_trax_center" class="form-control select2" data-rule-required="true" data-msg-required="Trax Center is required">
                     {{--   <option value="{{$retail_user->store->id}}"> {{$retail_user->store->name}} </option>--}}
                        @foreach($trax_centers as $trax_center)
                            <option value="{{$trax_center->id}}"> {{$trax_center->name}} </option>
                        @endforeach
                    </select>
                </div>
            
            
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

                <div class="form-group">
                    <input type="text" name="trax_id" id="trax_id" class="form-control" placeholder="Trax Id">
                </div>
                
                <div class="form-group">
                    <input type="text" name="commission_percentage" id="commission_percentage" class="form-control" placeholder="GST %">
                </div>

                <div class="form-group input-group">
                    <div class="input-group-prepend">
                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                        <span class="la la-calendar-o"></span>
                    </span>
                    </div>
                    <input type="text" name="agreement_start_date" id="delivery_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" placeholder="Agreement Start date">
                </div>

                <div class="form-group">
                    <input type="number" name="salary" id="salary" class="form-control" placeholder="Salary">
                </div>

                <div class="form-group">
                    <input type="text" name="father_name" id="father_name" class="form-control" placeholder="Father Name">
                </div>
                
                <div class="form-group">
                    <input type="text" name="mother_name" id="mother_name" class="form-control" placeholder="Mother Name">
                </div>
            </div>

            <div class="col">
                <div class="form-group">
                    <select name="marital_status" id="marital_status_edit" class="select2 form-control">
                        <option value="" selected disabled>Select Marital Status</option>
                        <option value="1">Single</option>
                        <option value="2">Marital</option>
                    </select>
                </div>

                <div class="form-group marital_details d-none">
                    <div class="form-group">
                        <input type="text" name="marital_name" id="marital_name_edit" class="form-control" placeholder="Spouse Name">
                    </div>
                    <div class="form-group">
                        <input type="text" name="marital_dob" id="marital_dob_edit" class="form-control" placeholder="Spouse DOB">
                    </div>
                    <div class="row">
                        <div class="col-9" id="child_input_container_edit">
                            <div class="child-template-edit d-none">
                                <div class="form-group">
                                    <input type="text" class="form-control child_input" placeholder="Child">
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <button type="button" class="btn btn-primary" id="edit_child_column_btn">
                                Add
                            </button>
                        </div>
                    </div>
                </div>
                

                <div class="form-group">
                    <label for="attachment_1">Attachment 1</label>
                    <input class="form-control form-control-sm" type="file" name="attachment_1" id="attachment_1" accept="image/*,.doc,.docx,.pdf" data-rule-required="true" data-msg-required="Atleast 1 attachment is required">
                </div>

                <div class="form-group">
                    <label for="attachment_2">Attachment 2</label>
                    <input class="form-control form-control-sm" type="file" name="attachment_2" id="attachment_2" accept="image/*,.doc,.docx,.pdf">
                </div>

                <div class="form-group">
                    <label for="attachment_3">Attachment 3</label>
                    <input class="form-control form-control-sm" type="file" name="attachment_3" id="attachment_3" accept="image/*,.doc,.docx,.pdf">
                </div>

                <div class="form-group">
                    <label for="attachment_4">Attachment 4</label>
                    <input class="form-control form-control-sm" type="file" name="attachment_4" id="attachment_4" accept="image/*,.doc,.docx,.pdf">
                </div>

                <div class="form-group">
                    <label for="attachment_5">Attachment 5</label>
                    <input class="form-control form-control-sm" type="file" name="attachment_5" id="attachment_5" accept="image/*,.doc,.docx,.pdf">
                </div>
            </div>
        </div>
        <div class="form-group ml-1">
            <button type="submit" name="update" class="btn btn-primary add" value="Update">Update</button>
        </div>
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

             @if ($retail_user->category == 1)
                $('#edit_user_form #trax_center_div').addClass('d-none');
                $('#edit_franchise').val(store_id).trigger('change');

             @else
                 $('#edit_user_form #franchise_div').addClass('d-none');
                 $('#edit_trax_center').val(store_id).trigger('change');

             @endif

            $('#edit_store').select2({
                width: '100%',

            }).bind('change', function () {
                var id = parseInt($(this).val());
                if (id == 1) {
                    $('#edit_user_form  #trax_center_div').addClass('d-none');
                    $('#edit_user_form  #franchise_div').removeClass('d-none');

                } else if (id == 2) {
                    $('#edit_user_form  #trax_center_div').removeClass('d-none');
                    $('#edit_user_form  #franchise_div').addClass('d-none');
                } else {
                    $('#edit_user_form  #trax_center_div').addClass('d-none');
                    $('#edit_user_form  #franchise_div').addClass('d-none');
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

        $("#marital_status_edit").on('change', function(){
            var selectedOption = $(this).val();
            if (selectedOption == 1){
                $(".marital_details").addClass("d-none");
                $("#marital_name_edit").val('');
                $("#marital_dob_edit").val('');
                // $("#wife_data").val('');
                $(".child_input").val('');
            } else if(selectedOption == 2) {
                $(".marital_details").removeClass("d-none");
            }
        });

        var EditChildCount = 0;
        function addChildInfo() {
            // Update the number to increase child count
            if (EditChildCount < 4){
                var $EditChildInput = $('.child-template-edit').clone().removeClass('child-template-edit').removeClass('d-none');
                EditChildCount++;
                $EditChildInput.find('.child_input').attr({
                    'id': 'child_' + EditChildCount + '_data_edit',
                    'name': 'child_' + EditChildCount + '_data',
                    'placeholder': 'Child ' + EditChildCount
                });
                $('#child_input_container_edit').append($EditChildInput);
            }
        }
        $("#edit_child_column_btn").on('click', function () {
            addChildInfo();
        });

    });
</script>    