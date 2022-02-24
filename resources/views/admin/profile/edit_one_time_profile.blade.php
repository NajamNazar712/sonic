@extends('admin.layout.master')

@section('title', 'Update Details')

@section('content')

    <h1 class="mb-1">
        Update Details
    </h1>
    <div class="card">
        <div class="card-header">
            @include('admin.inc.messages')
        </div>
        <div class="card-content">
            <div class="card-body card-dashboard">
                <form id="profile-form" class="form form-horizontal" method="post" action="{{route('admin.update_one_time_profile.submit')}}" novalidate="novalidate">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-body">
                                <h4 class="form-section">Personal Info</h4>
                                <input type="hidden" class="form-control" name="employee_id"id="employee_id" value="{{$employee->id}}">
                                @if(!$employee->guardian_name)
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Father/Husband Name<span class="text-danger">*</span></label>
                                        <input type="text" id="name" class="form-control" data-rule-required="true" data-msg-required="Father/Husband Name is required" name="guardian_name">
                                    </div>
                                </div>
                                @endif
                                @if(!$employee->mother_name)
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Mother Name<span class="text-danger">*</span></label>
                                        <input type="text" id="mother_name" class="form-control" data-rule-required="true" data-msg-required="Mother Name is required" name="mother_name">
                                    </div>
                                </div>
                                @endif
                                @if(!$employee->address)
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Address<span class="text-danger">*</span></label>
                                        <textarea data-rule-required="true" data-msg-required="Address is required"  class="form-control" id="address" name="address"></textarea>
                                    </div>
                                </div>
                                @endif
                                @if(!$employee->emergency_contact_person)
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Emergency Contact Person<span class="text-danger">*</span></label>
                                        <input type="text" id="emergency_contact_person" data-rule-required="true"  data-msg-required="Emergency Contact Person is required" class="form-control"  name="emergency_contact_person">
                                    </div>
                                </div>
                                @endif
                                @if(!$employee->emergency_contact)
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Emergency Contact<span class="text-danger">*</span></label>
                                        <input type="text" id="emergency_contact" data-rule-required="true"  data-msg-required="Emergency Contact is required" class="form-control" name="emergency_contact">
                                    </div>
                                </div>
                                @endif
                                @if(!$employee->date_of_birth)
                                    <div class="col-md-12">
                                        <label>Date Of Birth<span class="text-danger">*</span></label>
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                        <span class="la la-calendar-o small-calender-icon"></span>
                                                    </span>
                                            </div>
                                            <input type="text" name="date_of_birth" data-rule-required="true" data-msg-required="Date of Birth is required" data-value="{{$employee->date_of_birth != null ? $employee->date_of_birth : ''}}" class="form-control bg-primary border-primary white rounded-right pickadate" id="date_of_birth" placeholder="Date of Birth">
                                        </div>
                                    </div>
                                @endif
                                @if(!$employee->employee_gender_id)
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Gender<span class="text-danger">*</span></label>
                                        <select name="gender" id="gender" data-rule-required="true"  data-msg-required="Gender is required" class="select2 form-control " style="width: 100%" >
                                            @foreach($genders as $gender)
                                                <option value="{{$gender->id}}">{{$gender->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endif
                                @if(!$employee->religion_id)
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Religion<span class="text-danger">*</span></label>
                                        <select name="religion" id="religion" data-rule-required="true" data-msg-required="Religion is required" class="select2 form-control " style="width: 100%">
                                            @foreach($religions as $religion)
                                                <option value="{{$religion->id}}">{{$religion->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endif
                                @if(!$employee->marital_status_id)
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Marital Status<span class="text-danger">*</span></label>
                                        <select name="marital_status" id="marital_status" data-rule-required="true" data-msg-required="Marital Status is required" class="select2 form-control " style="width: 100%">
                                            @foreach($maritial_statuses as $maritial_status)
                                                <option value="{{$maritial_status->id}}">{{$maritial_status->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endif
                                @if(!$employee->domicile_id)
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Domicile<span class="text-danger">*</span></label>
                                        <select name="domicile" id="domicile" class="select2 form-control " data-rule-required="true" data-msg-required="Domicile is required" style="width: 100%">
                                            @foreach($domiciles as $domicile)
                                                <option value="{{$domicile->id}}">{{$domicile->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endif
                                @if(!$employee->blood_group)
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Blood Group<span class="text-danger">*</span></label>
                                        <select name="blood_group" id="blood_group" class="select2 form-control " data-rule-required="true" data-msg-required="Blood Group is required" style="width: 100%">
                                            @foreach($blood_groups as $blood_group)
                                                <option value="{{$blood_group->id}}">{{$blood_group->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-body">
                                <h4 class="form-section">Official Info</h4>
                                @if(!$employee->staff_category_id)
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Staff Category<span class="text-danger">*</span></label>
                                        <select name="staff_category" id="staff_category" data-rule-required="true" data-msg-required="Staff Category is Required" class="select2 form-control " style="width: 100%">
                                            @foreach($staff_categories as $staff_category)
                                                <option value="{{$staff_category->id}}">{{$staff_category->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endif

                                @if(!$employee->official_email)
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Official & Outlook ID</label>
                                            <input type="text" id="official_email" class="form-control email_mask" name="official_email" >
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Working Shift<span class="text-danger">*</span></label>
                                        <select name="shift_id" id="shift_list" data-rule-required="true"  data-msg-required="Shift is required" class="select2 form-control " style="width: 100%">
                                            @foreach($shifts as $shift)
                                                <option value="{{$shift->id}}">{{$shift->name}} ({{$shift->start_time}} - {{$shift->end_time}})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-actions center">
                                <button type="submit" class="btn btn-primary">
                                    Update
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/selectize/selectize.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        var today = new Date();
        today.setHours(0,0,0,0);
        $(document).ready(function() {


            $('.email_mask').inputmask({
                'alias': 'email',
                'clearIncomplete': true
            });

            $('#profile-form #emergency_contact').inputmask({
                'mask': '9999-9999999',
                'clearIncomplete': true
            });

            var date_of_birth = $('#profile-form #date_of_birth').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: 100,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
                max: today,
            });

            $("#religion").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Religion",
                width:'100%',
            });

            $("#gender").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Gender",
                width:'100%',
            });

            $("#domicile").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Domicile",
                width:'100%',
            });

            $("#marital_status").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Marital Status",
                width:'100%',
            });

            $("#blood_group").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Blood Group",
                width:'100%',
            });

            $("#staff_category").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Staff Category",
                width:'100%',
            });

            $("#shift_list").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Shift",
                width:'100%',
            });

            $('#profile-form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                }
            });

        });
    </script>
@endsection