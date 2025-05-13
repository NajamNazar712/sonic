<form id="edit_user_form" class="form-horizontal mb-1 justify-content-center" method="POST" action="{{ route('admin.retail.users.update',['id'=>$retail_user->id]) }}" novalidate="novalidate" enctype="multipart/form-data">
    {{ method_field('PUT') }}
    {{ csrf_field()  }}

    <input type="hidden" name="retail_user_id" id="retail_user_id" value="{{ $retail_user_id }}">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="form-group">
                    @if ($retail_user->category==1)
                        <h4>Franchise</h4>

                        {{-- allows updating franchise user accounts --}}
                        <input type="hidden" name="store" value="1">

                    @else
                        <h4>Trax Center</h4>
                        <input type="hidden" name="store" value="2">
                    @endif
                    {{-- <select name="store" id="edit_store" class="form-control select2" data-rule-required="true" data-msg-required="Franchise is required">
                        @if ($retail_user->category==1)
                            <option value="1" selected>Franchise</option>
                            <option value="2" >Trax Center</option>
                        @else
                            <option value="1">Franchise</option>
                            <option value="2" selected>Trax Center</option>
                        @endif
                    </select> --}}
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
                    <input type="password" class="form-control" id="edit_password" placeholder="Password" value="" name="password" data-rule-minlength="6" data-msg-minlength="Password needs to be at-least 6 Characters" autocomplete="nope">
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
                    <input type="text" name="trax_id" id="trax_id_edit" class="form-control" placeholder="Trax Id" value="{{ $retail_user->trax_id ?? '' }}">
                </div>
                
                {{-- <div class="form-group">
                    <input type="text" name="commission_percentage" id="commission_percentage" class="form-control" placeholder="GST %">
                </div> --}}

                <div class="form-group input-group">
                    <div class="input-group-prepend">
                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                        <span class="la la-calendar-o"></span>
                    </span>
                    </div>
                    <input type="text" name="agreement_start_date" id="delivery_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" placeholder="Agreement Start date">
                </div>

                <div class="form-group" id="salary_div">
                    <input type="number" name="salary" id="salary" class="form-control" placeholder="Salary" value="{{ count($retail_user_salary) > 0 ? $retail_user_salary[0] : '' }}">
                </div>

                <div class="form-group">
                    <input type="text" name="family_member_name[]" id="father_name" class="form-control" placeholder="Father Name" value="{{ count($retail_user_family_names) > 0 ? $retail_user_family_names[0] : '' }}">
                </div>
                
                <div class="form-group">
                    <input type="text" name="family_member_name[]" id="mother_name" class="form-control" placeholder="Mother Name" value="{{ count($retail_user_family_names) > 0 ? $retail_user_family_names[1] : '' }}">
                </div>
            </div>

            <div class="col">
                <div id="edit_for_trax_user" class="">
                    <div class="form-group">
                        <select name="marital_status" id="marital_status_edit" class="select2 form-control">
                            <option value="" disabled>Select Marital Status</option>
                            <option value="1" {{ count($retail_user_family_names) > 0 ? '' : 'selected' }}>Single</option>
                            <option value="2" {{ count($retail_user_family_names) > 0 ? 'selected' : '' }}>Marital</option>
                        </select>
                    </div>                
    
                    <div class="form-group marital_details d-none">
                        <div class="form-group">
                            <input type="text" name="family_member_name[]" id="marital_name_edit" class="form-control" placeholder="Spouse Name">
                        </div>
                        <div class="form-group">
                            <input type="date" name="family_member_name[]" id="marital_dob_edit" class="form-control" placeholder="Spouse DOB">
                        </div>
                        <div class="row">
                            <div class="col-9" id="child_input_container_edit">
    
                                {{-- <div class="child-template-edit d-none">
                                    <div class="form-group row">
                                        <div class="col-9">
                                            <input type="text" class="form-control child_input_edit" placeholder="Child">
                                        </div>
                                        <div class="col-3 remove_btn_div"></div>
                                    </div>
                                </div> --}}
    
    
                            </div>
                            <div class="col-3">
                                <button type="button" class="btn btn-primary" id="edit_child_column_btn">
                                    Add
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col">
                        <h2>Commission</h2>
                        <div class="row">
                            <div class="col-5">
                                <div class="form-group">
                                    <select name="retail_shipping_mode_id[]" id="retail_shipping_mode_id_edit" class="select2 form-control retail_shipping_mode_id_edit" data-rule-required="true" data-msg-required="Please choose a shipping mode">
                                        @foreach($shipping_modes as $shipping_mode)
                                            <option value="{{$shipping_mode->id}}">{{$shipping_mode->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
    
                            <div class="col-5">
                                <div class="form-group">
                                    <div class="input-group mb-2">
                                        <input type="text" name="product_percentage[]" id="product_percentage_edit" class="form-control product_percentage_edit" placeholder="Commission"  value="">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-2">
                                <input type="button" class="btn btn-primary" id="edit_retail_product_add_btn" value="Add">
                            </div>
                        </div>
                        <span id="error_message_edit" class="text-danger"></span>
    
                        <div class="row" id="editTableRow" style="display: none;">
                            <div class="col">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Selected Option</th>
                                            <th>Product Percentage</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="editTableBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    @if ($retail_user->category==1)
                        <label for="attachment_1">Agreement Details*</label>
                        <input class="form-control form-control-sm" type="file" name="attachment_1" id="attachment_1" accept=".doc,.docx,.pdf" data-rule-required="true" data-msg-required="Agreement Details are required">
                    @else
                        <label for="attachment_1">Employee Form*</label>
                        <input class="form-control form-control-sm" type="file" name="attachment_1" id="attachment_1" accept=".doc,.docx,.pdf" data-rule-required="true" data-msg-required="Employee Form is required">
                    @endif
                    <a id="attachment_1_filename" target="_blank"></a>
                </div>

                <div class="form-group">
                    <label for="attachment_2">CNIC front image*</label>
                    <input class="form-control form-control-sm" type="file" name="attachment_2" id="attachment_2" accept="image/*,.doc,.docx,.pdf" data-rule-required="true" data-msg-required="CNIC front image is required">
                    <a id="attachment_2_filename" target="_blank"></a>
                </div>

                <div class="form-group">
                    <label for="attachment_3">CNIC back image*</label>
                    <input class="form-control form-control-sm" type="file" name="attachment_3" id="attachment_3" accept="image/*,.doc,.docx,.pdf" data-rule-required="true" data-msg-required="CNIC back image is required">
                    <a id="attachment_3_filename" target="_blank"></a>
                </div>

                <div class="form-group">
                    <label for="attachment_4">Profile picture</label>
                    <input class="form-control form-control-sm" type="file" name="attachment_4" id="attachment_4" accept="image/*,.doc,.docx,.pdf">
                    <a id="attachment_4_filename" target="_blank"></a>
                </div>

                <div class="form-group">
                    <label for="attachment_5">Attachment 5</label>
                    <input class="form-control form-control-sm" type="file" name="attachment_5" id="attachment_5" accept="image/*,.doc,.docx,.pdf">
                    <a id="attachment_5_filename" target="_blank"></a>
                </div>

                <div class="old_working_place">
                    <h1></h1>
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
        var franchise_account = @json($retail_user->category);
        if (franchise_account == 1) {
            $('#edit_for_trax_user').addClass('d-none');
            $('#salary_div').addClass('d-none');
            $('#edit_user_form #delivery_date_from').attr('placeholder', 'Agreement Start date');
        } else if (franchise_account != 1) { 
            $('#edit_for_trax_user').removeClass('d-none');
            $('#salary_div').removeClass('d-none');
            $('#edit_user_form #delivery_date_from').attr('placeholder', 'Joining date');
        }

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
                $('#trax_id_edit').addClass('d-none');

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

        var agreement_start_date = {!! $jsonAgreementStartDate ?? 'null' !!};

        if (agreement_start_date) {
            $('#edit_user_form #delivery_date_from').val(agreement_start_date);
        } else {
            $('#edit_user_form #delivery_date_from').val('');
        }
        $('#edit_user_form #delivery_date_from').pickadate({
            firstDay: 1,
            clear: '',
            selectYears: true,
            selectMonths: true,
            formatSubmit: 'yyyy-mm-dd 00:00:00',
            hiddenSuffix: '_formatted',
            onSet: function(context) {
                if (context.select) {
                    $('#add_trax_center_form #delivery_date_to').pickadate('picker').set('min', $('#add_trax_center_form #delivery_date_from').pickadate('picker').get('select'));
                }
            }
        });

        var preSelectedMaritalStatus = $("#marital_status_edit").val();
        var family_member_info = @json($retail_user_family_names);
        var spouse_name = @json($retail_user_family_names[2] ?? '');
        var spouse_dob = @json($retail_user_family_names[3] ?? '');

        if (preSelectedMaritalStatus == 2) {
            $(".marital_details").removeClass("d-none");
            if (family_member_info.length > 0) {
                $("#marital_name_edit").val(spouse_name);
            } else {
                return;
            }
            if (family_member_info.length > 0 ) {
                $("#marital_dob_edit").val(spouse_dob);
            } else {
                return;
            }
        }

        $("#marital_status_edit").on('change', function(){
            var selectedOption = $(this).val();
            if (selectedOption == 1){
                $(".marital_details").addClass("d-none");
                $("#marital_name_edit").val('');
                $("#marital_dob_edit").val('');
                $(".child_input").val('');
            } else if(selectedOption == 2) {
                $(".marital_details").removeClass("d-none");
            }
        });

        var retail_user_id = $("#retail_user_id").val();
        var selectedOption = $("#retail_shipping_mode_id_edit option:selected").text();
        var productPercentage = $(".product_percentage_edit").val();
        $.ajax({
            type: "GET",
            url: '{{ route('admin.retail.users.retail_user_percentage') }}',
            data: { retail_user_id: retail_user_id },
            success: function (response) {
                if (response.data.length > 0) {
                    $('#editTableBody').empty();
                    $.each(response.data, function(index, item) {
                        var row = "<tr><td>" + item.selected_option + "</td><td>" + item.product_percentage + "</td><td><button class='btn btn-danger btn-sm remove-btn-edit-form'>Remove</button></td></tr>";
                        $("#editTableBody").append(row);
                    });
                    $("#editTableRow").show();
                }
                else {
                    $("#editTableRow").hide();
                }
            }
        });

        $('#editTableBody').on('click', '.remove-btn-edit-form', function() {
            $(this).closest('tr').remove();
        });

        $("#edit_retail_product_add_btn").on('click', function (event) {
            var selectedOptionEdit = $("#retail_shipping_mode_id_edit option:selected").text();
            var productPercentageEdit = $("#product_percentage_edit").val();

            if (productPercentageEdit.trim() === '' || !$.isNumeric(productPercentageEdit) || productPercentageEdit > 100 ) {
                $("#error_message_edit").text("Please enter a valid product percentage.").show();
                $("#product_percentage_edit").attr("required", true);
            } else {
                $("#error_message_edit").hide();
                $("#product_percentage_edit").removeAttr("required");
                var isDuplicate = false;
                $("#editTableBody").find("tr").each(function() {
                    if ($(this).find("td:first").text() === selectedOptionEdit) {
                        isDuplicate = true;
                        return false;
                    }
                });

                if (isDuplicate) {
                    $("#error_message_edit").text("Error: Cannot add same product.").show();
                } else {
                    var newRow = $("<tr><td>" + selectedOptionEdit + "</td><td>" + productPercentageEdit + "%</td><td><button class='btn btn-danger btn-sm remove-item'>Remove</button></td></tr>");
                    $("#editTableBody").append(newRow);
                    newRow.find('.remove-item').click(function() {
                        $(this).closest("tr").remove();
                        if ($("#editTableBody").find("tr").length === 0) {
                            $("#editTableRow").hide();
                        }
                    });
                    $("#editTableRow").show();
                    $("#product_percentage_edit").val('');
                }
            }
        });

        $("#edit_user_form").submit(function(event) {
            event.preventDefault();
            var retailShippingIdsEdit = [];
            var productPercentagesEdit = [];
            $("#editTableBody").find("tr").each(function() {
                var selectedOptionEdit = $(this).find("td:first").text();
                var productPercentageEdit = $(this).find("td:nth-child(2)").text();
                retailShippingIdsEdit.push(selectedOptionEdit);
                productPercentagesEdit.push(productPercentageEdit);
            });
            $(this).append("<input type='hidden' name='retail_shipping_mode_id' value='" + JSON.stringify(retailShippingIdsEdit) + "'>");
            $(this).append("<input type='hidden' name='product_percentage' value='" + JSON.stringify(productPercentagesEdit) + "'>");
            this.submit();
        });

        var numChildren = 0;
        // Function to initialize child inputs based on family member info
        function initializeChildInputs() {
            numChildren = 0;
            for (var i = 4; i < family_member_info.length; i++) {
                if (numChildren < 4) {
                    numChildren++;
                    var childName = family_member_info[i];
                    var childTemplate = `
                        <div class="form-group row initial_child_template">
                            <div class="col-9">
                                <input type="text" class="form-control child_input_edit" 
                                    id="child_${numChildren}_data_edit" 
                                    name="family_member_name[]" 
                                    placeholder="Child ${numChildren}" 
                                    value="${childName}">
                            </div>
                            <div class="col-3 remove_btn_div">
                                <button type="button" class="btn btn-danger remove-child-btn" 
                                        id="remove_child_column_btn_${numChildren}">Remove</button>
                            </div>
                        </div>
                    `;

                    // Append the child template to the container
                    $('#child_input_container_edit').append(childTemplate);
                    childTemplate.length
                }
            }

            // Disable add child button if the maximum number of children is reached
            if (numChildren >= 4) {
                $('#edit_child_column_btn').prop('disabled', true);
            }
        }

        initializeChildInputs();

        $('#edit_child_column_btn').on('click', function () {
            if (numChildren < 4) {
                numChildren++;
                // Create the child input template
                var childTemplate = `
                    <div class="form-group row">
                        <div class="col-9">
                            <input type="text" class="form-control child_input_edit_field" 
                                id="child_${numChildren}_data_edit" 
                                name="family_member_name[]" 
                                placeholder="Child ${numChildren}">
                        </div>
                        <div class="col-3 remove_btn_div">
                            <button type="button" class="btn btn-danger remove_child_column_btn_form" 
                                    id="remove_child_column_btn_form_${numChildren}">Remove</button>
                        </div>
                    </div>
                `;

                // Append the child template to the container
                $('#child_input_container_edit').append(childTemplate);

                var numChildTemplates = $('#child_input_container_edit').find('.initial_child_template').length;

                // Disable add child button if the maximum number of children is reached
                if (numChildTemplates >= 4) {
                    $(this).prop('disabled', true);
                }
            }
        });

        // Event handler for removing a child input (initialized inputs)
        $(document).on('click', '.remove-child-btn', function () {
            var $formGroup = $(this).closest('.form-group');
            $formGroup.remove();
            if (numChildren > 0) {
                numChildren--;
            }
            $('#edit_child_column_btn').prop('disabled', false);
        });

        // Event handler for removing a child input (dynamically added inputs)
        $(document).on('click', '.remove_child_column_btn_form', function () {
            var $formGroup = $(this).closest('.form-group');
            $formGroup.remove();
            if (numChildren > 0) {
                numChildren--;
            }
            $('#edit_child_column_btn').prop('disabled', false);
        });

        $('.edit_user_close_modal').on('click', function(event){
            $('#edit_child_column_btn').off('click');
        });

        $('#editRetailUser').on('hidden.bs.modal', function () {
            $('#edit_child_column_btn').off('click');
        });

        var retail_user_id = $('#retail_user_id').val();
        $.ajax({
            type: "GET",
            url: '{{ route('admin.retail.users.retail_user_attachments') }}',
            data: { retail_user_id: retail_user_id },
            success: function (response) {
                if (response.data) {
                    var retail_user_id = response.data.franchise_id;
                    if (retail_user_id === retail_user_id) {
                        for (var i = 1; i <= 5; i++) {
                            var attachmentKey = 'attachment_' + i;
                            var attachmentFileName = response.data[attachmentKey];
                            if (attachmentFileName) {
                                var attachmentURL = '/storage/' + attachmentFileName;
                                var fileNameParts = attachmentFileName.split('/');
                                var fileName = fileNameParts[fileNameParts.length - 1];
                                var attachmentLink = $('<a>').attr('href', attachmentURL).attr('target', '_blank').text(fileName);
                                $('#attachment_' + i + '_filename').html(attachmentLink);
                            }
                        }
                    }
                }
            }
        });

        $('#edit_store').on('change', function(){
            var category = $(this).val();
            if (category == 1) {
                $('#trax_id_edit').addClass("d-none");
                $('#salary_div').addClass("d-none");
                $('#edit_for_trax_user').addClass("d-none");
            } else {
                $('#trax_id_edit').removeClass("d-none");
                $('#trax_id_edit').on('input', function(event) {
                    $(this).val(function(_, value) {
                        return value.replace(/\D/g, '');
                    });
                });
                $('#salary_div').removeClass("d-none");
                $('#edit_for_trax_user').removeClass("d-none");
            }
        });

    });
</script>    