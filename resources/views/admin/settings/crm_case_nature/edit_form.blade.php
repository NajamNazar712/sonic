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
                        <form action="{{ route('admin.settings.crm_case_nature_types.store') }}" id="edit_form" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-6">
                                    <fieldset class="form-group">
                                        <label for="case_nature">Case Nature*</label>
                                        <select name="case_nature" id="edit_case_nature" class="form-control select2" required data-rule-required="true" data-msg-required="Case Nature is required">
                                            @foreach($case_nature as $nature)
                                                <option value="{{$nature->id}}">{{$nature->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                
                                    <fieldset class="form-group">
                                        <label class="d-flex">Shipment status visibility (<input type="checkbox" name="select_all_status" id="select_all_status"> select all) </label>
                                        <select name="shipment_status[]" id="edit_shipment_status" class="form-control select2" multiple="multiple">
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
                                            <div id="edit_remarks_section" class="my-2"></div>
                                            <button class="btn btn-success" type="button" id="edit_remarks_section">Add Remark</button>
                                        </fieldset>
                                    </div>
                                </div>
    
                                <div class="col-6">
                                    <fieldset class="form-group">
                                        <label for="case_nature_type">Case Nature Type*</label>
                                        <input type="text" name="case_nature_type" id="edit_case_nature_type" class="form-control" placeholder="Enter Case Nature Type" required data-rule-required="true" data-msg-required="Please enter case nature type">
                                    </fieldset>
    
                                    <fieldset class="form-group">
                                        <label class="d-flex">Admin departments visibility (<input type="checkbox" name="select_all_admin_department" id="edit_select_all_admin_department"> select all) </label>
                                        <select name="admin_departments[]" id="edit_admin_department_visibility" class="form-control select2" multiple="multiple">
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
                    var remarksContainer = $('#edit_remarks_section');
                    remarksContainer.empty();

                    remarks.forEach(function(remark, index) {
                        // Limit to maximum 5 remarks
                        if (index < 5) {
                            var inputHtml = `
                                <div class="form-group">
                                    <label for="remark_${index + 1}">Remark ${index + 1}</label>
                                    <input type="text" id="remark_${index + 1}" name="remarks[]" class="form-control" value="${remark.remarks}">
                                </div>
                            `;
                            remarksContainer.append(inputHtml);
                        }
                        // Add Remark button functionality
                        $('#add_remark_btn').on('click', function() {
                            if (remarks.length < 5) {
                                var newIndex = remarks.length + 1;
                                var newInputHtml = `
                                    <div class="form-group">
                                        <label for="remark_${newIndex}">Remark ${newIndex}</label>
                                        <input type="text" id="remark_${newIndex}" name="remarks[]" class="form-control">
                                    </div>
                                `;
                                remarksContainer.append(newInputHtml);
                                remarks.push({ id: newIndex, case_nature_id: id, remarks: '', created_at: '', updated_at: '' });
                            } else {
                                alert('You can add maximum 5 remarks.');
                            }
                        });
                    });
                }
            });
        });
    </script>
    

@endsection