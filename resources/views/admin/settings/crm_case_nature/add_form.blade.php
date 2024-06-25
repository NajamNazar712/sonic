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
                        <form action="{{ route('admin.settings.crm_case_nature_types.store') }}" id="add_form">
                            @csrf
                            <div class="row">
                                <div class="col-6">
                                    <fieldset class="form-group">
                                        <label for="case_nature_select">Case Nature*</label>
                                        <select name="case_nature_select" id="case_nature_select" class="form-control select2">
                                            @foreach($case_nature as $nature)
                                                <option value="{{$nature->id}}">{{$nature->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                
                                    <fieldset class="form-group">
                                        <label class="d-flex">Shipment status visibility (<input type="checkbox" name="select_all_status" id="select_all_status"> select all) </label>
                                        <select name="case_nature_select_visibility" id="case_nature_select_visibility" class="form-control select2">
                                            @foreach($shipment_status as $status)
                                                <option value="{{$status->id}}">{{$status->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                
                                    <label for="shipper_visibility">Visibility to Shippers (mark checked for visibility)</label>
                                    <fieldset class="form-group d-flex" id="shipper_visibility_div">
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
                                        <select name="case_nature_type" id="case_nature_type" class="form-control select2">
                                            @foreach($case_nature as $nature)
                                                <option value="{{$nature->id}}">{{$nature->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
    
                                    <fieldset class="form-group">
                                        <label class="d-flex">Admin departments visibility (<input type="checkbox" name="select_all_admin_department" id="select_all_admin_department"> select all) </label>
                                        <select name="admin_department_visibility" id="admin_department_visibility" class="form-control select2">
                                            @foreach($shipment_status as $status)
                                                <option value="{{$status->id}}">{{$status->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
    
                                    <label for="remarks_visibility">Remarks Visibility (Select Remarks)</label>
                                    <fieldset class="form-group d-flex" id="remarks_visibility_div">
                                        <input type="checkbox" name="remarks_visibility" id="remarks_visibility">
                                        <label for="remarks_visibility" id="remarks_visibility_label">Remarks Visibility</label>
                                    </fieldset>
                                </div>
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
        $('#add_remarks_section').click(function(e) {
            e.preventDefault();
            var currentRemarksCount = $('#remarks_section .remark-field').length;
            if (currentRemarksCount < maxRemarks) {
                $('#remarks_section').append(`
                    <div class="remark-field d-flex mb-2">
                        <input type="text" class="form-control me-2" name="remarks[]" placeholder="Enter remark">
                        <div class=mx-1>
                            <button class="btn btn-danger remove-remark">Remove</button>
                        </div>
                    </div>
                `);
            }
            if (currentRemarksCount >= maxRemarks) {
                $('#add_remarks_section').attr('disabled', 'disabled');
            }
        });

        $('#remarks_section').on('click', '.remove-remark', function(e) {
            e.preventDefault();
            $(this).closest('.remark-field').remove();
            var currentRemarksCount = $('#remarks_section .remark-field').length;
            if (currentRemarksCount < maxRemarks) {
                $('#add_remarks_section').prop('disabled', false);
            }
        });
    });
    </script>

@endsection