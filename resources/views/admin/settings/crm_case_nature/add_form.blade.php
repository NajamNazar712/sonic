@extends('admin.layout.master')

@section('title', 'CRM Case Nature Types')

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <h1 class="mb-1">
                Add CRM Case Nature
            </h1>

            <div class="card">
                <div class="card-content" aria-expanded="true">
                    <div class="card-body">
                        @include('admin.inc.messages')
                        <form action="{{ route('admin.settings.crm_case_nature_types.store') }}" id="add_form" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-6">
                                    <fieldset class="form-group">
                                        <label for="case_nature">Case Nature*</label>
                                        <select name="case_nature" id="case_nature" class="form-control select2" required data-rule-required="true" data-msg-required="Case Nature is required*">
                                            @foreach($case_nature as $nature)
                                                <option value="{{$nature->id}}">{{$nature->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                
                                    <fieldset class="form-group">
                                        <label class="d-flex">Shipment status visibility (<input type="checkbox" name="select_all_status" id="select_all_status"> select all) </label>
                                        <select name="shipment_status[]" id="shipment_status" class="form-control select2" multiple="multiple">
                                            @foreach($shipment_status as $status)
                                                <option value="{{$status->id}}">{{$status->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                
                                    <label for="shipper_visibility">Visibility to Shippers (mark checked for visibility)</label>
                                    <fieldset class="form-group d-flex mt-1" id="shipper_visibility_div">
                                        <input type="checkbox" name="shipper_visibility" id="shipper_visibility">
                                        <label for="shipper_visibility" id="shipper_visibility_label">Visible to shippers</label>
                                    </fieldset>
                
                                    <div class="mt-4">
                                        <label for="">Add remarks (you can add maximum 5 remarks)</label>
                                        <fieldset class="form-group">
                                            <div id="remarks_section" class="my-2"></div>
                                            <button class="btn btn-success" id="add_remarks_section">Add Remark</button>
                                        </fieldset>
                                    </div>
                                </div>
    
                                <div class="col-6">
                                    <fieldset class="form-group">
                                        <label for="case_nature_type">Case Nature Type*</label>
                                        <input type="text" name="case_nature_type" id="case_nature_type" class="form-control" placeholder="Enter Case Nature Type" required data-rule-required="true" data-msg-required="Please enter case nature type*">
                                    </fieldset>
    
                                    <fieldset class="form-group">
                                        <label class="d-flex">Admin departments visibility (<input type="checkbox" name="select_all_admin_department" id="select_all_admin_department"> select all) </label>
                                        <select name="admin_departments[]" id="admin_department_visibility" class="form-control select2" multiple="multiple">
                                            @foreach($admin_departments as $department)
                                                <option value="{{$department->id}}">{{$department->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
    
                                    <label for="">Remarks Visibility (Select Remarks)</label>
                                    <fieldset class="form-group d-flex mt-1" id="remarks_visibility_div">
                                        <input type="checkbox" name="remarks_visibility" id="remarks_visibility">
                                        <label for="remarks_visibility" id="remarks_visibility_label">Remarks Visibility</label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-success" id="add_case_nature_btn">Submit Case</button>
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
        #select_all_status, #shipper_visibility_div, #select_all_admin_department{
            margin: 0px 3px 0px 3px;
        }
        #shipper_visibility_label, #remarks_visibility_label{
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
        var maxRemarks = 5;
        function toggleRemarksVisibility() {
            var isRemarksVisible = $('#remarks_visibility').is(':checked');
            var add_remarks_section = $('#add_remarks_section');
            if (isRemarksVisible) {
                $('#remarks_section .remark-field').show();
                if ($('#remarks_section .remark-field').length >= 0) {
                    add_remarks_section.prop('disabled', false);
                    $('#remarks_section').show();
                    if ($('#remarks_section .remark-field').length > 4){
                        add_remarks_section.prop('disabled', true);
                    } else {
                        add_remarks_section.prop('disabled', false);
                    }
                    // addRemarkField();
                }
            } else {
                $('#remarks_section').hide();
                $('#remarks_section .remark-field').hide();
                add_remarks_section.prop('disabled', true);
            }
            // toggleFirstRemarkRemoveButton();
        }

        function addRemarkField() {
            var currentRemarksCount = $('#remarks_section .remark-field').length;
            if (currentRemarksCount < maxRemarks) {
                $('#remarks_section').append(`
                    <div class="remark-field d-flex mb-2">
                        <input type="text" class="form-control me-2" name="remarks[]" placeholder="Enter remark">
                        <div class="mx-1">
                            <button class="btn btn-danger remove-remark">Remove</button>
                        </div>
                    </div>
                `);
                if (currentRemarksCount >= maxRemarks - 1) {
                    $('#add_remarks_section').attr('disabled', 'disabled');
                }
            }
            // toggleFirstRemarkRemoveButton();
        }

        function toggleFirstRemarkRemoveButton() {
            var isRemarksVisible = $('#remarks_visibility').is(':checked');
            var firstRemoveButton = $('#remarks_section .remark-field:first .remove-remark');
            if (isRemarksVisible) {
                firstRemoveButton.hide();
            } else {
                firstRemoveButton.show();
            }
        }

        function validateRemarks() {
            var isRemarksVisible = $('#remarks_visibility').is(':checked');
            var remarkFields = $('#remarks_section .remark-field input').filter(function() {
                return $(this).is(':visible');
            });
            var currentRemarksCount = remarkFields.length;
            $('#remarks_error').remove();
            if (isRemarksVisible) {
                var filledRemarkCount = 0;
                remarkFields.each(function() {
                    if ($(this).val().trim() !== '') {
                        filledRemarkCount++;
                    }
                });
                if (filledRemarkCount === 0) {
                    var errorMessage = '<span id="remarks_error" class="text-danger">At least one remark is required.</span>';
                    $('#remarks_section').append(errorMessage);
                    return false;
                }
            }
            return true;
        }

        $('#remarks_visibility').change(function() {
            toggleRemarksVisibility();
        });

        $('#add_remarks_section').click(function(e) {
            e.preventDefault();
            addRemarkField();
        });

        $('#remarks_section').on('click', '.remove-remark', function(e) {
            e.preventDefault();
            $(this).closest('.remark-field').remove();
            var currentRemarksCount = $('#remarks_section .remark-field').length;
            if (currentRemarksCount < maxRemarks) {
                $('#add_remarks_section').removeAttr('disabled');
            }
            // toggleFirstRemarkRemoveButton();
        });

        toggleRemarksVisibility();

        $('#shipment_status').select2({
            width:'100%',
            placeholder:"Select Status",
            allowClear:false,
            dropdownParent:$('#add_form')
        });

        $('#select_all_status').change(function() {
            if ($(this).is(':checked')) {
                $('#shipment_status > option').prop('selected', true).trigger('change');
            } else {
                $('#shipment_status > option').prop('selected', false).trigger('change');
            }
        });

        $('#admin_department_visibility').select2({
            width:'100%',
            placeholder:"Select Department",
            allowClear:false,
            dropdownParent:$('#add_form')
        });

        $('#select_all_admin_department').change(function() {
            if ($(this).is(':checked')) {
                // Select all options
                $('#admin_department_visibility > option').prop('selected', true).trigger('change');
            } else {
                // Deselect all options
                $('#admin_department_visibility > option').prop('selected', false).trigger('change');
            }
        });

        $('#case_nature').prepend('<option selected></option>').select2({
            width:'100%',
            placeholder:"Select Case Nature",
            allowClear:true,
            dropdownParent:$('#add_form')
        });

        // Toggling error message
        var case_nature = $('#case_nature');
        case_nature.on('change', function () {
            var case_nature_error = $('#case_nature-error');
            if (case_nature.val() != '') {
                case_nature_error.hide();
            } else {
                case_nature_error.show();
            }
        });

        $( "#add_form" ).validate({
            errorClass:"danger",
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
            submitHandler: function(form) {
                var isValidRemarks = validateRemarks();
                if (isValidRemarks) {
                    form.submit();
                }
            }
        });
    });
    </script>
@endsection