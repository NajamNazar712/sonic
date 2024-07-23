@extends('admin.layout.master')

@section('title', 'CRM Case Nature Types')

@section('content')
@php
    $url = url()->current();
    $segments = explode('/', $url);
    $id = end($segments);
@endphp
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <h1 class="mb-1">
                Edit CRM Case Nature
            </h1>

            <div class="card">
                <div class="card-content" aria-expanded="true">
                    <div class="card-body">
                        @include('admin.inc.messages')
                        <form action="{{ route('admin.settings.crm_case_nature_types.update') }}" id="edit_form" method="POST">
                            @csrf
                            <input type="hidden" name="case_nature_id" value="{{ $id }}">
                            <div class="row">
                                <div class="col-6">
                                    <fieldset class="form-group">
                                        <label for="case_nature">Case Nature*</label>
                                        <select name="case_nature" id="edit_case_nature" class="form-control select2" data-rule-required="true" data-msg-required="Case Nature is required*">
                                            @foreach($case_nature as $nature)
                                                <option value="{{$nature->id}}">{{$nature->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                
                                    <fieldset class="form-group">
                                        <label class="d-flex">Shipment status visibility (<input type="checkbox" name="select_all_status" id="select_all_status"> select all) </label>
                                        <select name="shipment_status[]" id="edit_shipment_status" class="form-control select2" multiple="multiple" data-rule-required="true" data-msg-required="Select At least 1 shipment status">
                                            @foreach($shipment_status as $status)
                                                <option value="{{$status->id}}">{{$status->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                
                                    <label for="shipper_visibility">Visibility to Shippers (mark checked for visibility)</label>
                                    <fieldset class="form-group d-flex mt-1" id="edit_shipper_visibility_div">
                                        <input type="checkbox" name="shipper_visibility" id="edit_shipper_visibility">
                                        <label for="edit_shipper_visibility" id="edit_shipper_visibility_label">Visible to shippers</label>
                                    </fieldset>
                
                                    <div class="mt-4">
                                        <label for="">Add remarks (you can add maximum 5 remarks)</label>
                                        <fieldset class="form-group">
                                            <div id="edit_remarks_section" class="mb-2"></div>
                                            <button class="btn btn-success" type="button" id="edit_add_remark_btn">Add Remark</button>
                                        </fieldset>
                                    </div>
                                </div>
    
                                <div class="col-6">
                                    <fieldset class="form-group">
                                        <label for="case_nature_type">Case Nature Type*</label>
                                        <input type="text" name="case_nature_type" id="edit_case_nature_type" class="form-control" placeholder="Enter Case Nature Type" data-rule-required="true" data-msg-required="Please enter case nature type*" required="required">
                                    </fieldset>
    
                                    <fieldset class="form-group">
                                        <label class="d-flex">Admin departments visibility (<input type="checkbox" name="select_all_admin_department" id="edit_select_all_admin_department"> select all) </label>
                                        <select name="admin_departments[]" id="edit_admin_department_visibility" class="form-control select2" multiple="multiple" data-rule-required="true" data-msg-required="Select At least 1 department">
                                            @foreach($admin_departments as $department)
                                                <option value="{{$department->id}}">{{$department->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
    
                                    <label for="">Remarks Visibility (Select Remarks)</label>
                                    <fieldset class="form-group d-flex mt-1" id="edit_remarks_visibility_div">
                                        <input type="checkbox" name="remarks_visibility" id="edit_remarks_visibility">
                                        <label for="edit_remarks_visibility" id="edit_remarks_visibility_label">Remarks Visibility</label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-success" id="edit_case_nature_btn">Edit Case</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

    <style>
        #select_all_status, #edit_shipper_visibility_div, #edit_select_all_admin_department{
            margin: 0px 3px 0px 3px;
        }
        #edit_shipper_visibility_label, #edit_remarks_visibility_label{
            margin: 0px 0px 0px 5px;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('#edit_case_nature').select2({
                width:'100%',
                placeholder:"Select Case Nature",
                allowClear:true,
                dropdownParent:$('#edit_form')
            });

            $('#edit_admin_department_visibility').select2({
                width:'100%',
                placeholder:"Select Department",
                allowClear:false,
                dropdownParent:$('#edit_form')
            });

            $('#edit_shipment_status').select2({
                width:'100%',
                placeholder:"Select Status",
                allowClear:false,
                dropdownParent:$('#edit_form')
            });

            $('#select_all_status').on('change', function() {
                if ($(this).prop('checked')) {
                    $('#edit_shipment_status').find('option').prop('selected', true);
                } else {
                    $('#edit_shipment_status').find('option').prop('selected', false);
                }
                $('#edit_shipment_status').trigger('change');
            });

            $('#edit_select_all_admin_department').on('change', function() {
                if ($(this).prop('checked')) {
                    $('#edit_admin_department_visibility').find('option').prop('selected', true);
                } else {
                    $('#edit_admin_department_visibility').find('option').prop('selected', false);
                }
                $('#edit_admin_department_visibility').trigger('change');
            });            

            var url = window.location.href;
            var url_parts = new URL(url);
            var path = url_parts.pathname;
            var id = path.split('/')[5];

            var ajaxUrl = '{{ route("admin.settings.crm_case_nature_types.edit_ajax", ":id") }}';
            ajaxUrl = ajaxUrl.replace(':id', id);

            $.ajax({
                url: ajaxUrl,
                type: 'GET',
                success: function(data) {
                    var case_nature = data.data.case_nature[0];
                    var remarks = data.data.remarks;

                    $('#edit_case_nature').val(case_nature.nature_id).trigger('change');
                    $('#edit_case_nature_type').val(case_nature.type);
                    
                    // shipment status
                    var shipment_status = JSON.parse(case_nature.shipment_status);
                    $('#edit_shipment_status').val(shipment_status).trigger('change');

                    // admin departments
                    var admin_departments = JSON.parse(case_nature.admin_departments);
                    $('#edit_admin_department_visibility').val(admin_departments).trigger('change');

                    if (case_nature.shipper_visibility == 1){
                        $('#edit_shipper_visibility').prop('checked', true);
                    }

                    if (case_nature.remarks_visibility == 1){
                        $('#edit_remarks_visibility').prop('checked', true);
                    }

                    // remarks section
                    var remarksContainer = $('#edit_remarks_section');
                    var edit_add_remark_btn = $('#edit_add_remark_btn');
                    var errorMessage = '<span id="remarks_error" class="text-danger">At least one remark is required.</span>';
                    var remarks_visibility = $('#edit_remarks_visibility').is(':checked');
                    var edit_case_nature_btn = $('#edit_case_nature_btn');
                    var errorMessageEmptyRemarks = '<span id="remarks_error_empty" class="text-danger">Cannot submit empty remarks.</span>';

                    function toggleErrorMessage() {
                        if (remarks_visibility && remarksContainer.children().length === 0) {
                            if ($('#remarks_error').length === 0) {
                                // remarksContainer.after(errorMessage);
                            }
                            edit_case_nature_btn.prop('disabled', true);
                        } else {
                            $('#remarks_error').remove();
                            edit_case_nature_btn.prop('disabled', false);
                        }
                    }

                    function populateRemarks() {
                        remarksContainer.empty();
                        remarks.forEach(function(remark, index) {
                            if (index < 5) {
                                var inputHtml = `
                                    <div class="form-group" id="remark_${remark.id}_container">
                                        <div class="input-group">
                                            <input type="text" id="remark_${remark.id}" name="remarks[]" class="form-control edit_remarks_new" value="${remark.remarks}">
                                            <div class="input-group-append mx-1">
                                                <button class="btn btn-danger remove-remark-btn" data-remark-id="${remark.id}" type="button">Remove</button>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                remarksContainer.append(inputHtml);
                            }
                        });
                        $('#edit_add_remark_btn').prop('disabled', remarks.length >= 5);
                        if (!remarks_visibility) {
                            remarksContainer.hide();
                            $('#edit_add_remark_btn').prop('disabled', true);
                        } else {
                            remarksContainer.show();
                            $('#edit_add_remark_btn').prop('disabled', false);
                        }
                        // toggleErrorMessage();
                    }
                    populateRemarks();

                    function toggleCaseNatureButton() {
                        var inputLength = $('.edit_remarks_new').length;
                        edit_add_remark_btn.prop('disabled', inputLength >= 5);
                    }

                    $('#edit_add_remark_btn').on('click', function() {
                        var currentInputLength = $('.edit_remarks_new').length;
                        if (currentInputLength < 5) {
                            var newInputHtml = `
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" name="remarks[]" class="form-control edit_remarks_new" placeholder="Add Remark">
                                        <div class="input-group-append mx-1">
                                            <button class="btn btn-danger remove-remark-btn" type="button">Remove</button>
                                        </div>
                                    </div>
                                </div>
                            `;
                            remarksContainer.append(newInputHtml);
                            toggleCaseNatureButton();

                            $('.edit_remarks_new').off('input').on('input', function() {
                                var allInputs = $('.edit_remarks_new');
                                var allFilled = allInputs.toArray().every(input => $(input).val().trim() !== '');
                                edit_add_remark_btn.prop('disabled', allFilled && allInputs.length < 5);
                                toggleCaseNatureButton();
                            });
                        }
                    });
                    toggleCaseNatureButton();

                    var updateRemarksLength = function() {
                        var remark_length = $('.edit_remarks_new').length;
                        if (remark_length === 0 && $('#edit_remarks_visibility').is(':checked')) {
                            $('#remarks_error').remove();
                        } else {
                            $('#remarks_error').remove();
                        }
                    };

                    var remove_remark_btn = $('.remove-remark-btn');
                    remove_remark_btn.on('click', function() {
                        $(this).closest('.form-group').remove();
                        updateRemarksLength();
                    });

                    $('#edit_add_remark_btn').on('click', function() {
                        var newRemarkLength = $('.edit_remarks_new').length;
                        if (newRemarkLength > 0) {
                            $('#remarks_error').remove();
                            // $('#edit_case_nature_btn').prop('disabled', false);
                        } else {
                            $('#remarks_error').remove();
                            // $('#edit_case_nature_btn').prop('disabled', true);
                            // $('#edit_remarks_section').after(errorMessage);
                        }
                    });

                    $(document).on('click', '.remove-remark-btn', function() {
                        $(this).closest('.form-group').remove();
                        updateRemarksLength();
                    });

                    $('#edit_remarks_visibility').on('change', function() {
                        updateRemarksLength();
                    });
                    updateRemarksLength();

                    $(document).on('click', '.remove-remark-btn', function() {
                        var remarkId = $(this).data('remark-id');
                        $('#remark_' + remarkId + '_container').remove();
                        remarks = remarks.filter(function(remark) {
                            return remark.id !== remarkId;
                        });

                        // Enable "Add Remark" button if remarks count is less than 5
                        $('#edit_add_remark_btn').prop('disabled', remarks.length >= 5);
                    });

                    $('#edit_remarks_visibility').on('change', function() {
                        remarks_visibility = $(this).is(':checked');
                        populateRemarks();
                    });

                    edit_case_nature_btn.on('click', function() {
                        var hasEmptyRemarks = false;
                        $('.edit_remarks_new').each(function() {
                            if ($(this).val().trim() === '') {
                                hasEmptyRemarks = true;
                                return false;
                            }
                        });
                        if (hasEmptyRemarks) {
                            if ($('#remarks_error_empty').length === 0) {
                                remarksContainer.append(errorMessageEmptyRemarks);
                            }
                            return false;  // Prevent form submission or other actions
                        } else {
                            $('#remarks_error').remove();  // Remove error message if all fields are filled
                        }
                        if (remarks_visibility && remarksContainer.children().length == 0){
                            remarksContainer.append(errorMessage);
                            return false;
                        } 
                        var filledRemarkCount = 0;
                        var emptyRemarkCount = 0;
                        edit_remarks_new.each(function() {
                            if ($(this).val().trim() === '') {
                                emptyRemarkCount++;
                            } else {
                                filledRemarkCount++;
                            }
                        });
                        var isEmpty = false;
                        edit_remarks_new.each(function() {
                            if ($(this).val().trim() == '') {
                                isEmpty = true;
                                return false;
                            }
                        });
                        if (isEmpty) {
                            $('#remarks_error').remove();
                            remarksContainer.before(errorMessage);
                            return false;
                        }
                    });

                    $(document).on('keyup', '.edit_remarks_new', function() {
                        if ($('#remarks_error_empty').length > 0) {
                            $('#remarks_error_empty').remove();
                        }
                    });

                    // Toggling error message
                    var edit_case_nature = $('#edit_case_nature');
                    edit_case_nature.on('change', function(){
                        var edit_case_nature_error = $('#edit_case_nature-error');
                        if (edit_case_nature.val() == null){
                            edit_case_nature_error.removeClass('d-none');
                            return false;
                        } else {
                            edit_case_nature_error.addClass('d-none');
                        }
                    });

                    var edit_admin_department_visibility = $('#edit_admin_department_visibility');
                    edit_admin_department_visibility.on('change', function(){
                        var edit_admin_visibility_error = $('#edit_admin_department_visibility-error');
                        if (edit_admin_department_visibility.val() != ''){
                            edit_admin_visibility_error.hide();
                        } else {
                            edit_admin_visibility_error.show();
                        }
                    });

                    var edit_shipment_status = $('#edit_shipment_status');
                    edit_shipment_status.on('change', function(){
                        var edit_shipment_status_error = $('#edit_shipment_status-error');
                        if (edit_shipment_status.val() != ''){
                            edit_shipment_status_error.hide();
                        } else {
                            edit_shipment_status_error.show();
                        }
                    });

                    $( "#edit_form" ).validate({
                        errorClass:"danger",
                        errorPlacement: function(error, element) {
                            error.addClass('w-100').appendTo(element.parent('.form-group'));
                        },
                        submitHandler: function(form) {
                            form.submit();
                        }
                    });
                }
            });
        });
    </script>
    

@endsection