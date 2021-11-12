@extends('admin.layout.master')

@section('title', 'Update Employee Details')

@section('content')

    <h1 class="mb-1">
        Update Employee Details
    </h1>
    <div class="card">
        <div class="card-header">
            @include('admin.inc.messages')
        </div>
        <div class="card-content">
            <div id="tabs" class="card-body mb-5">
                <ul class="nav nav-tabs nav-justified">
                    <li class="nav-item">
                        <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" aria-controls="active" aria-expanded="true">Employee Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="medical-tab" data-toggle="tab" href="#medical" aria-controls="link" aria-expanded="false">Medical Information</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="bank-tab" data-toggle="tab" href="#bank" aria-controls="linkOpt">Bank Information</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="education-tab" data-toggle="tab" href="#education" aria-controls="linkEmail">Educational Background</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="employment_history-tab" data-toggle="tab" href="#employment_history" aria-controls="linkEmail">Employment History</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="references-tab" data-toggle="tab" href="#references" aria-controls="linkEmail">References</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="attachments-tab" data-toggle="tab" href="#attachments" aria-controls="linkEmail">Attachments</a>
                    </li>
                </ul>
                <div class="tab-content px-1 pt-1">
                    <div role="tabpanel" class="tab-pane active" id="profile" aria-labelledby="profile-tab" aria-expanded="true">
                        <form id="profile-form" class="form form-horizontal" method="post" action="{{route('admin.human_resource.employee_directory.profile.update',$employee->id)}}" novalidate="novalidate">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-body">
                                        <h4 class="form-section">Personal Info</h4>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Employee Name<span class="text-danger">*</span></label>
                                                <input type="text" id="employee_name" class="form-control border-primary" value="{{$employee->name}}" data-rule-required="true" data-msg-required="Employee Name is required" name="employee_name">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Father/Husband Name<span class="text-danger">*</span></label>
                                                <input type="text" id="name" class="form-control border-primary" value="{{$employee->guardian_name}}" data-rule-required="true" data-msg-required="Father/Husband Name is required" name="name">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Mother Name<span class="text-danger">*</span></label>
                                                <input type="text" id="mother_name" class="form-control border-primary" value="{{$employee->mother_name}}" data-rule-required="true" data-msg-required="Mother Name is required" name="mother_name">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Religion</label>
                                                <select name="religion" id="religion" class="select2 form-control " style="width: 100%">
                                                    @foreach($religions as $religion)
                                                        <option value="{{$religion->id}}">{{$religion->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Nationality</label>
                                                <select name="nationality" id="nationality" class="select2 form-control " style="width: 100%">
                                                    @foreach($nationalities as $nationality)
                                                        <option value="{{$nationality->id}}">{{$nationality->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Domicile</label>
                                                <select name="domicile" id="domicile" class="select2 form-control " style="width: 100%">
                                                    @foreach($domiciles as $domicile)
                                                        <option value="{{$domicile->id}}">{{$domicile->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Marital Status</label>
                                                <select name="marital_status" id="marital_status" class="select2 form-control " style="width: 100%">
                                                    @foreach($maritial_statuses as $maritial_status)
                                                        <option value="{{$maritial_status->id}}">{{$maritial_status->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Blood Group</label>
                                                <select name="blood_group" id="blood_group" class="select2 form-control " style="width: 100%">
                                                    @foreach($blood_groups as $blood_group)
                                                        <option value="{{$blood_group->id}}">{{$blood_group->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Personal Number<span class="text-danger">*</span></label>
                                                <input type="text" id="personal_number" class="form-control border-primary" value="{{$employee->phone_number}}" data-rule-required="true" data-msg-required="Phone Number is required" name="personal_number">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Personal Email</label>
                                                <input type="text" id="personal_email" class="form-control border-primary email_mask" value="{{$employee->personal_email}}" name="personal_email">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Address<span class="text-danger">*</span></label>
                                                <textarea data-rule-required="true" data-msg-required="Address is required"  class="form-control border-primary" id="address" name="address">{{$employee->address}}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Emergency Contact<span class="text-danger">*</span></label>
                                                <input type="text" id="emergency_contact" data-rule-required="true"  data-msg-required="Emergency Contact is required" class="form-control border-primary" value="{{$employee->emergency_contact}}" name="emergency_contact">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Place Of Birth</label>
                                                <select name="place_of_birth" id="place_of_birth" class="select2 form-control " style="width: 100%">
                                                    @foreach($place_of_birth_cities as $place_of_birth_city)
                                                        <option value="{{$place_of_birth_city->id}}">{{$place_of_birth_city->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label>Date Of Birth</label>
                                            <div class="form-group input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                        <span class="la la-calendar-o small-calender-icon"></span>
                                                    </span>
                                                </div>
                                                <input type="text" name="date_of_birth" data-value="{{$employee->date_of_birth != null ? $employee->date_of_birth : ''}}" class="form-control bg-primary border-primary white rounded-right pickadate" id="date_of_birth" placeholder="Date of Birth">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>CNIC<span class="text-danger">*</span></label>
                                                <input type="text" id="cnic" data-rule-required="true"  data-msg-required="CNIC is required" class="form-control border-primary" value="{{$employee->cnic}}" name="cnic">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label>CNIC Issue Date</label>
                                            <div class="form-group input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                        <span class="la la-calendar-o small-calender-icon"></span>
                                                    </span>
                                                </div>
                                                <input type="text" name="cnic_issue_date" data-value="{{$employee->cnic_issue_date != null ? date('Y/m/d',strtotime($employee->cnic_issue_date)) : ''}}" class="form-control bg-primary border-primary white rounded-right pickadate" id="cnic_issue_date" placeholder="CNIC Issue Date">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label>CNIC Expiry Date</label>
                                            <div class="form-group input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                        <span class="la la-calendar-o small-calender-icon"></span>
                                                    </span>
                                                </div>
                                                <input type="text" name="cnic_expiry_date" data-value="{{$employee->cnic_expiry_date != null ? date('Y/m/d',strtotime($employee->cnic_expiry_date)) : ''}}"  class="form-control bg-primary border-primary white rounded-right pickadate" id="cnic_expiry_date" placeholder="CNIC Expiry Date">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-body">
                                        <h4 class="form-section">Official Info</h4>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>City<span class="text-danger">*</span></label>
                                                <select name="city" id="city" class="select2 form-control " data-rule-required="true"  data-msg-required="City is required" style="width: 100%">
                                                    @foreach($cities as $city)
                                                        <option value="{{$city->id}}">{{$city->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Zone<span class="text-danger">*</span></label>
                                                <select name="zone" id="zone" data-rule-required="true"  data-msg-required="Zone is required" class="select2 form-control " style="width: 100%">
                                                    @foreach($zones as $zone)
                                                        <option value="{{$zone->id}}">{{$zone->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Department<span class="text-danger">*</span></label>
                                                <select name="department" id="department" data-rule-required="true"  data-msg-required="Department is required" class="select2 form-control " style="width: 100%">
                                                    @foreach($departments as $department)
                                                        <option value="{{$department->id}}">{{$department->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Designation<span class="text-danger">*</span></label>
                                                <select name="designation" id="designation" data-rule-required="true" data-msg-required="Designation is Required" class="select2 form-control " style="width: 100%">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Bolt & Sonic Pin<span class="text-danger">*</span></label>
                                                <input type="password" id="bolt_pin" data-rule-required="true" data-msg-required="Bolt & Sonic Pin is required" class="form-control border-primary" value="{{$employee->pin}}" name="bolt_pin" data-rule-minlength="4" data-rule-maxlength="4">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Official Number</label>
                                                <input type="text" id="official_number" class="form-control border-primary" value="{{$employee->official_phone_number}}" name="official_number" >
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Official & Outlook ID</label>
                                                <input type="text" id="official_email" class="form-control border-primary email_mask" value="{{$employee->official_email}}" name="official_email" >
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Working Shift<span class="text-danger">*</span></label>
                                                <select name="shift_id" id="shift_list" data-rule-required="true"  data-msg-required="Shift is required" class="select2 form-control " style="width: 100%">
                                                    @foreach($shifts as $shift)
                                                        <option value="{{$shift->id}}">{{$shift->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-actions center">
                                        <button id="cancel-button-profile" type="button" class="btn btn-warning mr-1">
                                            Cancel
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            Update
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="medical" aria-labelledby="medical-tab" >
                        <form id="medical-form" novalidate="novalidate" class="form form-horizontal" method="post" action="{{route('admin.human_resource.employee_directory.medical.update',$employee->id)}}">
                            @csrf
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="form-body" id="medical_info_container">
                                        <h4 class="form-section">Medical Details</h4>
                                        @forelse($medical_infos as $index => $medical_info)
                                            <div class="row">
                                                @if(!$loop->first)
                                                    <hr style="width: 100%;">
                                                @endif
                                                <div class="col-md-10">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Name Of Family Member<span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control border-primary name_family_member" value="{{$medical_info->name}}" name="name[{{$index}}]" data-rule-required="true" data-msg-required="Name is required">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Relationship<span class="text-danger">*</span></label>
                                                            <select name="relationship[{{$index}}]" id="relationship_family_member_{{$loop->index}}" class="select2 form-control relationship_family_member" data-rule-required="true" data-msg-required="Relation is required" style="width: 100%">
                                                                @foreach($relationships as $relationship)
                                                                    <option value="{{$relationship->id}}">{{$relationship->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label>Date Of Birth<span class="text-danger">*</span></label>
                                                        <div class="form-group input-group">
                                                            <div class="input-group-prepend">
                                                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                                        <span class="la la-calendar-o small-calender-icon"></span>
                                                                    </span>
                                                            </div>
                                                            <input type="text" data-rule-required="true" data-msg-required="Date Of Birth is required" name="dob[{{$index}}]" data-value="{{date('Y/m/d',strtotime($medical_info->date_of_birth))}}"  class="form-control bg-primary border-primary white rounded-right pickadate dob_family_member" placeholder="Date Of Birth">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Marital Status<span class="text-danger">*</span></label>
                                                            <select name="marital_status[{{$index}}]" data-rule-required="true" data-msg-required="Marital Status is required" id="marital_status_family_member_{{$loop->index}}" class="select2 form-control marital_status_family_member" style="width: 100%">
                                                                @foreach($maritial_statuses as $maritial_status)
                                                                    <option value="{{$maritial_status->id}}">{{$maritial_status->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-body">
                                                        @if(!$loop->first)
                                                            <button type="button" class="btn btn-danger btn-xs mt-2 remove_family_member"><i class="ft-minus"></i></button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="row">
                                                <div class="col-md-10">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Name Of Family Member<span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control border-primary name_family_member" value="" name="name[0]" data-rule-required="true" data-msg-required="Name is required">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Relationship<span class="text-danger">*</span></label>
                                                            <select name="relationship[0]" id="relationship_family_member_0" class="select2 form-control relationship_family_member" data-rule-required="true" data-msg-required="Relationship is required" style="width: 100%">
                                                                @foreach($relationships as $relationship)
                                                                    <option value="{{$relationship->id}}">{{$relationship->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label>Date Of Birth<span class="text-danger">*</span></label>
                                                        <div class="form-group input-group">
                                                            <div class="input-group-prepend">
                                                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                                        <span class="la la-calendar-o small-calender-icon"></span>
                                                                    </span>
                                                            </div>
                                                            <input type="text" name="dob[0]" data-rule-required="true" data-msg-required="Date Of Birth is required"  class="form-control bg-primary border-primary white rounded-right pickadate dob_family_member" placeholder="Date Of Birth">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Marital Status<span class="text-danger">*</span></label>
                                                            <select name="marital_status[0]" id="marital_status_family_member_0" class="select2 form-control marital_status_family_member" data-rule-required="true" data-msg-required="Marital Status is required" style="width: 100%">
                                                                @foreach($maritial_statuses as $maritial_status)
                                                                    <option value="{{$maritial_status->id}}">{{$maritial_status->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-body">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-actions center">
                                        <button type="button" id="add_family_member" class="btn btn-success mr-1"><i class="ft-plus"></i> Add Family Member</button>
                                        <button id="cancel-button-profile" type="button" class="btn btn-warning mr-1">
                                            Cancel
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            Update
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="bank" aria-labelledby="bank-tab" >
                        <form id="bank-form" novalidate="novalidate" class="form form-horizontal" method="post" action="{{route('admin.human_resource.employee_directory.bank.update',$employee->id)}}">
                            @csrf
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="form-body">
                                        <h4 class="form-section">Bank Information</h4>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Account Title<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control border-primary" id="account_title" value="{{$bank_info->account_title ?? ''}}" name="account_title" data-rule-required="true" data-msg-required="Account Title is required">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Branch Code<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control border-primary" id="branch_code" value="{{$bank_info->branch_code ?? ''}}" name="branch_code" data-rule-required="true" data-msg-required="Branch Code is required">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Account Number<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control border-primary" id="account_number" value="{{$bank_info->account_no ?? ''}}" name="account_number" data-rule-required="true" data-msg-required="Account Number is required">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Bank Name<span class="text-danger">*</span></label>
                                                <select name="bank_name" id="bank_name" data-rule-required="true" data-msg-required="Bank Name is required" class="select2 form-control required" style="width: 100%">
                                                    @foreach($banks as $bank)
                                                        <option value="{{$bank->id}}">{{$bank->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Branch Name<span class="text-danger">*</span></label>
                                                <input type="text" data-rule-required="true" data-msg-required="Branch Name is required" class="form-control border-primary" id="branch_name" value="{{$bank_info->branch_name ?? ''}}" name="branch_name">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>IBAN Number<span class="text-danger">*</span></label>
                                                <input type="text" data-rule-required="true" data-msg-required="IBAN Number is required" placeholder="(e.g: PK37MEZN0001220100004069)" class="form-control border-primary" id="iban_number" value="{{$bank_info->iban ?? ''}}" name="iban_number">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-actions center">
                                        <button id="cancel-button-profile" type="button" class="btn btn-warning mr-1">
                                            Cancel
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            Update
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="education" aria-labelledby="education-tab" >
                        <form id="education-form" novalidate="novalidate" class="form form-horizontal" method="post" action="{{route('admin.human_resource.employee_directory.education.update',$employee->id)}}">
                            @csrf
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="form-body" id="education_info_container">
                                        <h4 class="form-section">Educational Background</h4>
                                        @forelse($educations as $index => $education)
                                            @if(!$loop->first)
                                                <hr style="width: 100%;">
                                            @endif
                                            <div class="row">
                                                <div class="col-md-10">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Name Of Institute<span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control border-primary name_institute" value="{{$education->name}}" name="name[{{$index}}]" data-rule-required="true" data-msg-required="Name Of Institute is required">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Degree Awarded<span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control border-primary degree" value="{{$education->degree}}" name="degree[{{$index}}]" data-rule-required="true" data-msg-required="Degree is required">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Grade Achieved<span class="text-danger">*</span></label>
                                                            <select name="grade[{{$index}}]" id="grade_{{$loop->index}}" data-rule-required="true" data-msg-required="Grade is required" class="select2 form-control grade" style="width: 100%">
                                                                <option>A+</option>
                                                                <option>A</option>
                                                                <option>B</option>
                                                                <option>C</option>
                                                                <option>D</option>
                                                                <option>E</option>
                                                                <option>F</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label>Graduation Year<span class="text-danger">*</span></label>
                                                        <div class="form-group input-group">
                                                            <div class="input-group-prepend">
                                                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                                        <span class="la la-calendar-o small-calender-icon"></span>
                                                                    </span>
                                                            </div>
                                                            <input type="text" data-rule-required="true" data-value="{{date('Y/m/d',strtotime($education->passing_year))}}" data-msg-required="Graduation Year is required" name="passing_year[{{$index}}]"  class="form-control bg-primary border-primary white rounded-right pickadate passing_year" placeholder="Graduation Year">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-body">
                                                        @if(!$loop->first)
                                                            <button type="button" class="btn btn-danger btn-xs mt-2 remove_education"><i class="ft-minus"></i></button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="row">
                                                <div class="col-md-10">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Name Of Institute<span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control border-primary name_institute" value="" name="name[0]" data-rule-required="true" data-msg-required="Name Of Institute is required">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Degree Awarded<span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control border-primary degree" value="" name="degree[0]" data-rule-required="true" data-msg-required="Degree is required">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Grade Achieved<span class="text-danger">*</span></label>
                                                            <select name="grade[0]" id="grade_0" data-rule-required="true" data-msg-required="Grade is required" class="select2 form-control grade" style="width: 100%">
                                                                <option>A+</option>
                                                                <option>A</option>
                                                                <option>B</option>
                                                                <option>C</option>
                                                                <option>D</option>
                                                                <option>E</option>
                                                                <option>F</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label>Graduation Year<span class="text-danger">*</span></label>
                                                        <div class="form-group input-group">
                                                            <div class="input-group-prepend">
                                                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                                        <span class="la la-calendar-o small-calender-icon"></span>
                                                                    </span>
                                                            </div>
                                                            <input type="text" data-rule-required="true" data-msg-required="Graduation Year is required" name="passing_year[0]"  class="form-control bg-primary border-primary white rounded-right pickadate passing_year" placeholder="Graduation Year">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-body">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-actions center">
                                        <button type="button" id="add_education" class="btn btn-success mr-1"><i class="ft-plus"></i> Add Another Institute</button>
                                        <button id="cancel-button-profile" type="button" class="btn btn-warning mr-1">
                                            Cancel
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            Update
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="employment_history" aria-labelledby="employment_history-tab" >
                        <form id="employment_history-form" class="form form-horizontal" method="post" action="{{route('admin.human_resource.employee_directory.employment.update',$employee->id)}}">
                            @csrf
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="form-body" id="employment_info_container">
                                        <h4 class="form-section">Employment History</h4>
                                        @forelse($employments as $index => $employment)
                                            <div class="row">
                                                @if(!$loop->first)
                                                    <hr style="width: 100%;">
                                                @endif
                                                <div class="col-md-10">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Company/Organization Name<span class="text-danger">*</span></label>
                                                            <input type="text" data-rule-required="true" data-msg-required="Company/Organization Name is required" class="form-control border-primary name_emplotment" value="{{$employment->name ?? ''}}" name="name[{{$index}}]">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Position/Designation<span class="text-danger">*</span></label>
                                                            <input type="text" data-rule-required="true" data-msg-required="Position/Designation is required" class="form-control border-primary position_employment" value="{{$employment->designation ?? ''}}" name="position[{{$index}}]">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label>From<span class="text-danger">*</span></label>
                                                        <div class="form-group input-group">
                                                            <div class="input-group-prepend">
                                                                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                                            <span class="la la-calendar-o small-calender-icon"></span>
                                                                        </span>
                                                            </div>
                                                            <input type="text" data-value="{{date('Y/m/d',strtotime($employment->from))}}" data-rule-required="true" data-msg-required="From is required" name="from[{{$index}}]"  class="form-control bg-primary border-primary white rounded-right pickadate from_employment" placeholder="From">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label>To<span class="text-danger">*</span></label>
                                                        <div class="form-group input-group">
                                                            <div class="input-group-prepend">
                                                                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                                            <span class="la la-calendar-o small-calender-icon"></span>
                                                                        </span>
                                                            </div>
                                                            <input type="text" data-value="{{date('Y/m/d',strtotime($employment->to))}}" data-value="" data-rule-required="true" data-msg-required="To is required" name="to[{{$index}}]"  class="form-control bg-primary border-primary white rounded-right pickadate to_employment" placeholder="To">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Reason<span class="text-danger">*</span></label>
                                                            <textarea data-rule-required="true" data-msg-required="Reason is required" class="form-control border-primary reason_employment" name="reason[{{$index}}]">{{$employment->reason}}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-body">
                                                        @if(!$loop->first)
                                                            <button type="button" class="btn btn-danger btn-xs mt-2 remove_employment"><i class="ft-minus"></i></button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="row">
                                                <div class="col-md-10">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Company/Organization Name<span class="text-danger">*</span></label>
                                                            <input type="text" data-rule-required="true" data-msg-required="Company/Organization Name is required" class="form-control border-primary name_emplotment" value="" name="name[0]">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Position/Designation<span class="text-danger">*</span></label>
                                                            <input type="text" data-rule-required="true" data-msg-required="Position/Designation is required" class="form-control border-primary position_employment" value="" name="position[0]">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label>From<span class="text-danger">*</span></label>
                                                        <div class="form-group input-group">
                                                            <div class="input-group-prepend">
                                                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                                        <span class="la la-calendar-o small-calender-icon"></span>
                                                                    </span>
                                                            </div>
                                                            <input type="text" data-rule-required="true" data-msg-required="From is required" name="from[0]"  class="form-control bg-primary border-primary white rounded-right pickadate from_employment" placeholder="From">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label>To<span class="text-danger">*</span></label>
                                                        <div class="form-group input-group">
                                                            <div class="input-group-prepend">
                                                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                                        <span class="la la-calendar-o small-calender-icon"></span>
                                                                    </span>
                                                            </div>
                                                            <input type="text" data-rule-required="true" data-msg-required="To is required" name="to[0]"  class="form-control bg-primary border-primary white rounded-right pickadate to_employment" placeholder="To">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Reason<span class="text-danger">*</span></label>
                                                            <textarea data-rule-required="true" data-msg-required="Reason is required" class="form-control border-primary reason_employment" name="reason[0]"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-body">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-actions center">
                                        <button type="button" id="add_employment" class="btn btn-success mr-1"><i class="ft-plus"></i> Add Experience</button>
                                        <button id="cancel-button-profile" type="button" class="btn btn-warning mr-1">
                                            Cancel
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            Update
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="references" aria-labelledby="references-tab" >
                        <form id="references-form" novalidate="novalidate" class="form form-horizontal" method="post" action="{{route('admin.human_resource.employee_directory.reference.update',$employee->id)}}">
                            @csrf
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="form-body">
                                        <h4 class="form-section">References</h4>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Name</label>
                                                <input type="text" class="form-control border-primary" id="reference_name" value="{{$reference->name ?? ''}}" name="name">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Occupation</label>
                                                <input type="text" class="form-control border-primary" id="reference_occupation" value="{{$reference->occupation ?? ''}}" name="occupation">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Relationship</label>
                                                <input type="text" class="form-control border-primary" id="reference_relationship" value="{{$reference->relationship ?? ''}}" name="relationship">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Years</label>
                                                <input type="text" class="form-control border-primary" id="reference_years" value="{{$reference->years ?? ''}}" name="years">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Phone Number</label>
                                                <input type="text" class="form-control border-primary" id="references_phone" value="{{$reference->phone_number ?? ''}}" name="phone">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="text" class="form-control border-primary email_mask" id="references_email" value="{{$reference->email ?? ''}}" name="email">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-actions center">
                                        <button id="cancel-button-profile" type="button" class="btn btn-warning mr-1">
                                            Cancel
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            Update
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="attachments" aria-labelledby="attachments-tab" >
                        <form id="attachments-form" novalidate="novalidate" class="form form-horizontal" method="post" enctype="multipart/form-data" action="{{route('admin.human_resource.employee_directory.attachments.update',$employee->id)}}">
                            @csrf
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="form-body" id="employment_info_container">
                                        <h4 class="form-section">Attachments</h4>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>CV/Resume</label>
                                                    @if(isset($attachments) && $attachments->cv != null)
                                                        @php
                                                            $cvs = explode(',', $attachments->cv);
                                                        @endphp
                                                    @endif
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="cv_1" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($cvs))
                                                                    @foreach($cvs as $cv)
                                                                        @php
                                                                            $pos = strpos($cv, "cv_1_");
                                                                        @endphp
                                                                        @if($pos !== false)
                                                                            <a href="{{asset(Storage::url($cv))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                        @endif
                                                                    @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="cv_2" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($cvs))
                                                                    @foreach($cvs as $cv)
                                                                        @php
                                                                            $pos = strpos($cv, "cv_2_");
                                                                        @endphp
                                                                        @if($pos !== false)
                                                                            <a href="{{asset(Storage::url($cv))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                        @endif
                                                                    @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="cv_3" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($cvs))
                                                                        @foreach($cvs as $cv)
                                                                            @php
                                                                                $pos = strpos($cv, "cv_3_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($cv))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="cv_4" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($cvs))
                                                                    @foreach($cvs as $cv)
                                                                        @php
                                                                            $pos = strpos($cv, "cv_4_");
                                                                        @endphp
                                                                        @if($pos !== false)
                                                                            <a href="{{asset(Storage::url($cv))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                        @endif
                                                                    @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Scanned CNIC</label>
                                                    @if(isset($attachments) && $attachments->cnic != null)
                                                        @php
                                                            $cnics = explode(',', $attachments->cnic);
                                                        @endphp
                                                    @endif
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="cnic_1" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($cnics))
                                                                        @foreach($cnics as $cnic)
                                                                            @php
                                                                                $pos = strpos($cnic, "cnic_1_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($cnic))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="cnic_2" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($cnics))
                                                                        @foreach($cnics as $cnic)
                                                                            @php
                                                                                $pos = strpos($cnic, "cnic_2_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($cnic))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="cnic_3" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($cnics))
                                                                        @foreach($cnics as $cnic)
                                                                            @php
                                                                                    $pos = strpos($cnic, "cnic_3_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($cnic))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="cnic_4" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($cnics))
                                                                        @foreach($cnics as $cnic)
                                                                            @php
                                                                                $pos = strpos($cnic, "cnic_4_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($cnic))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Passport Size Photo<span class="text-danger">*</span></label>
                                                    @if(isset($attachments) && $attachments->photo != null)
                                                        @php
                                                            $photos = explode(',', $attachments->photo);
                                                        @endphp
                                                    @endif
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="photo_1" class="w-100 p-1 border-primary" title="Select File" @if(!isset($attachments) || $attachments->photo == null) data-rule-required="true" data-msg-required="Atleast One Image is Required" @endif data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($photos))
                                                                        @foreach($photos as $photo)
                                                                            @php
                                                                                $pos = strpos($photo, "photo_1_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($photo))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="photo_2" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($photos))
                                                                        @foreach($photos as $photo)
                                                                            @php
                                                                                $pos = strpos($photo, "photo_2_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($photo))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="photo_3" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($photos))
                                                                        @foreach($photos as $photo)
                                                                            @php
                                                                                $pos = strpos($photo, "photo_3_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($photo))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="photo_4" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($photos))
                                                                        @foreach($photos as $photo)
                                                                            @php
                                                                                $pos = strpos($photo, "photo_4_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($photo))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Academic Credentials</label>
                                                    @if(isset($attachments) && $attachments->academic != null)
                                                        @php
                                                            $academics = explode(',', $attachments->academic);
                                                        @endphp
                                                    @endif
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="academic_1" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($academics))
                                                                        @foreach($academics as $academic)
                                                                            @php
                                                                                $pos = strpos($academic, "academic_1_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($academic))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="academic_2" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($academics))
                                                                        @foreach($academics as $academic)
                                                                            @php
                                                                                $pos = strpos($academic, "academic_2_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($academic))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="academic_3" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($academics))
                                                                        @foreach($academics as $academic)
                                                                            @php
                                                                                $pos = strpos($academic, "academic_3_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($academic))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="academic_4" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($academics))
                                                                        @foreach($academics as $academic)
                                                                            @php
                                                                                $pos = strpos($academic, "academic_4_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($academic))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Experience Certificates</label>
                                                    @if(isset($attachments) && $attachments->experience != null)
                                                        @php
                                                            $experience_certificates = explode(',', $attachments->experience);
                                                        @endphp
                                                    @endif
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="experience_certificate_1" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($experience_certificates))
                                                                        @foreach($experience_certificates as $experience_certificate)
                                                                            @php
                                                                                $pos = strpos($experience_certificate, "experience_certificate_1_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($experience_certificate))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="experience_certificate_2" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($experience_certificates))
                                                                        @foreach($experience_certificates as $experience_certificate)
                                                                            @php
                                                                                $pos = strpos($experience_certificate, "experience_certificate_2_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($experience_certificate))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="experience_certificate_3" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($experience_certificates))
                                                                        @foreach($experience_certificates as $experience_certificate)
                                                                            @php
                                                                                $pos = strpos($experience_certificate, "experience_certificate_3_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($experience_certificate))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="experience_certificate_4" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($experience_certificates))
                                                                        @foreach($experience_certificates as $experience_certificate)
                                                                            @php
                                                                                $pos = strpos($experience_certificate, "experience_certificate_4_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($experience_certificate))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Last Pay Slip</label>
                                                    @if(isset($attachments) && $attachments->last_pay_slip != null)
                                                        @php
                                                            $last_pay_slips = explode(',', $attachments->last_pay_slip);
                                                        @endphp
                                                    @endif
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="last_pay_slip_1" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($last_pay_slips))
                                                                        @foreach($last_pay_slips as $last_pay_slip)
                                                                            @php
                                                                                $pos = strpos($last_pay_slip, "last_pay_slip_1_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($last_pay_slip))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="last_pay_slip_2" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($last_pay_slips))
                                                                        @foreach($last_pay_slips as $last_pay_slip)
                                                                            @php
                                                                                $pos = strpos($last_pay_slip, "last_pay_slip_2_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($last_pay_slip))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="last_pay_slip_3" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($last_pay_slips))
                                                                        @foreach($last_pay_slips as $last_pay_slip)
                                                                            @php
                                                                                $pos = strpos($last_pay_slip, "last_pay_slip_3_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($last_pay_slip))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="last_pay_slip_4" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($last_pay_slips))
                                                                        @foreach($last_pay_slips as $last_pay_slip)
                                                                            @php
                                                                                $pos = strpos($last_pay_slip, "last_pay_slip_4_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($last_pay_slip))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Nikkah Nama</label>
                                                    @if(isset($attachments) && $attachments->nikkah_nama != null)
                                                        @php
                                                            $nikkah_namas = explode(',', $attachments->nikkah_nama);
                                                        @endphp
                                                    @endif
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="nikkah_nama_1" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($nikkah_namas))
                                                                        @foreach($nikkah_namas as $nikkah_nama)
                                                                            @php
                                                                                $pos = strpos($nikkah_nama, "nikkah_nama_1_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($nikkah_nama))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="nikkah_nama_2" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($nikkah_namas))
                                                                        @foreach($nikkah_namas as $nikkah_nama)
                                                                            @php
                                                                                $pos = strpos($nikkah_nama, "nikkah_nama_2_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($nikkah_nama))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="nikkah_nama_3" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($nikkah_namas))
                                                                        @foreach($nikkah_namas as $nikkah_nama)
                                                                            @php
                                                                                $pos = strpos($nikkah_nama, "nikkah_nama_3_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($nikkah_nama))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="nikkah_nama_4" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($nikkah_namas))
                                                                        @foreach($nikkah_namas as $nikkah_nama)
                                                                            @php
                                                                                $pos = strpos($nikkah_nama, "nikkah_nama_4_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($nikkah_nama))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Scanned CNIC Spouse</label>
                                                    @if(isset($attachments) && $attachments->cnic_spouse != null)
                                                        @php
                                                            $cnic_spouses = explode(',', $attachments->cnic_spouse);
                                                        @endphp
                                                    @endif
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="cnic_spouse_1" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($cnic_spouses))
                                                                        @foreach($cnic_spouses as $cnic_spouse)
                                                                            @php
                                                                                $pos = strpos($cnic_spouse, "cnic_spouse_1_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($cnic_spouse))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="cnic_spouse_2" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($cnic_spouses))
                                                                        @foreach($cnic_spouses as $cnic_spouse)
                                                                            @php
                                                                                $pos = strpos($cnic_spouse, "cnic_spouse_2_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($cnic_spouse))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="cnic_spouse_3" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($cnic_spouses))
                                                                        @foreach($cnic_spouses as $cnic_spouse)
                                                                            @php
                                                                                $pos = strpos($cnic_spouse, "cnic_spouse_3_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($cnic_spouse))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="cnic_spouse_4" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($cnic_spouses))
                                                                        @foreach($cnic_spouses as $cnic_spouse)
                                                                            @php
                                                                                $pos = strpos($cnic_spouse, "cnic_spouse_4_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($cnic_spouse))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Children B-Form</label>
                                                    @if(isset($attachments) && $attachments->child_b_form != null)
                                                        @php
                                                            $child_b_forms = explode(',', $attachments->child_b_form);
                                                        @endphp
                                                    @endif
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="child_b_form_1" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($child_b_forms))
                                                                        @foreach($child_b_forms as $child_b_form)
                                                                            @php
                                                                                $pos = strpos($child_b_form, "child_b_form_1_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($child_b_form))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="child_b_form_2" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($child_b_forms))
                                                                        @foreach($child_b_forms as $child_b_form)
                                                                            @php
                                                                                $pos = strpos($child_b_form, "child_b_form_2_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($child_b_form))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="child_b_form_3" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($child_b_forms))
                                                                        @foreach($child_b_forms as $child_b_form)
                                                                            @php
                                                                                $pos = strpos($child_b_form, "child_b_form_3_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($child_b_form))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="child_b_form_4" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($child_b_forms))
                                                                        @foreach($child_b_forms as $child_b_form)
                                                                            @php
                                                                                $pos = strpos($child_b_form, "child_b_form_4_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($child_b_form))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                   </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Scanned CNIC Nominee</label>
                                                    @if(isset($attachments) && $attachments->cnic_nominee != null)
                                                        @php
                                                            $cnic_nominees = explode(',', $attachments->cnic_nominee);
                                                        @endphp
                                                    @endif
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="cnic_nominee_1" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($cnic_nominees))
                                                                        @foreach($cnic_nominees as $cnic_nominee)
                                                                            @php
                                                                                $pos = strpos($cnic_nominee, "cnic_nominee_1_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($cnic_nominee))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="cnic_nominee_2" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($cnic_nominees))
                                                                        @foreach($cnic_nominees as $cnic_nominee)
                                                                            @php
                                                                                $pos = strpos($cnic_nominee, "cnic_nominee_2_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($cnic_nominee))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="cnic_nominee_3" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($cnic_nominees))
                                                                        @foreach($cnic_nominees as $cnic_nominee)
                                                                            @php
                                                                                $pos = strpos($cnic_nominee, "cnic_nominee_3_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($cnic_nominee))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="cnic_nominee_4" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($cnic_nominees))
                                                                        @foreach($cnic_nominees as $cnic_nominee)
                                                                            @php
                                                                                $pos = strpos($cnic_nominee, "cnic_nominee_4_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($cnic_nominee))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                  </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Scanned Utility Bill</label>
                                                    @if(isset($attachments) && $attachments->utility_bill != null)
                                                        @php
                                                            $utility_bills = explode(',', $attachments->utility_bill);
                                                        @endphp
                                                    @endif
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="utility_bill_1" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($utility_bills))
                                                                        @foreach($utility_bills as $utility_bill)
                                                                            @php
                                                                                $pos = strpos($utility_bill, "utility_bill_1_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($utility_bill))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="utility_bill_2" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($utility_bills))
                                                                        @foreach($utility_bills as $utility_bill)
                                                                            @php
                                                                                $pos = strpos($utility_bill, "utility_bill_2_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($utility_bill))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="utility_bill_3" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($utility_bills))
                                                                        @foreach($utility_bills as $utility_bill)
                                                                            @php
                                                                                $pos = strpos($utility_bill, "utility_bill_3_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($utility_bill))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="utility_bill_4" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($utility_bills))
                                                                        @foreach($utility_bills as $utility_bill)
                                                                            @php
                                                                                $pos = strpos($utility_bill, "utility_bill_4_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($utility_bill))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Affidavit</label>
                                                    @if(isset($attachments) && $attachments->affidavit != null)
                                                        @php
                                                            $affidavits = explode(',', $attachments->affidavit);
                                                        @endphp
                                                    @endif
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="affidavit_1" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($affidavits))
                                                                        @foreach($affidavits as $affidavit)
                                                                            @php
                                                                                $pos = strpos($affidavit, "affidavit_1_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($affidavit))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="affidavit_2" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($affidavits))
                                                                        @foreach($affidavits as $affidavit)
                                                                            @php
                                                                                $pos = strpos($affidavit, "affidavit_2_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($affidavit))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="affidavit_3" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($affidavits))
                                                                        @foreach($affidavits as $affidavit)
                                                                            @php
                                                                                $pos = strpos($affidavit, "affidavit_3_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($affidavit))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="affidavit_4" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($affidavits))
                                                                        @foreach($affidavits as $affidavit)
                                                                            @php
                                                                                $pos = strpos($affidavit, "affidavit_4_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($affidavit))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                     </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Cheque<span class="text-danger">*</span></label>
                                                    @if(isset($attachments) && $attachments->cheque != null)
                                                        @php
                                                            $cheques = explode(',', $attachments->cheque);
                                                        @endphp
                                                    @endif
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="cheque_1" class="w-100 p-1 border-primary" title="Select File" @if(!isset($attachments) || $attachments->cheque == null) data-rule-required="true" data-msg-required="Atleast One Cheque is Required" @endif data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($cheques))
                                                                        @foreach($cheques as $cheque)
                                                                            @php
                                                                                $pos = strpos($cheque, "cheque_1_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($cheque))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="cheque_2" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($cheques))
                                                                        @foreach($cheques as $cheque)
                                                                            @php
                                                                                $pos = strpos($cheque, "cheque_2_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($cheque))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="cheque_3" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($cheques))
                                                                        @foreach($cheques as $cheque)
                                                                            @php
                                                                                $pos = strpos($cheque, "cheque_3_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($cheque))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <input type="file" name="cheque_4" class="w-100 p-1 border-primary" title="Select File" data-rule-extension="docx|pdf|doc" data-msg-extension="Only file with extension docx , doc or pdf allowed" data-rule-accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-msg-accept="Only Pdf or Word file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if(isset($cheques))
                                                                        @foreach($cheques as $cheque)
                                                                            @php
                                                                                $pos = strpos($cheque, "cheque_4_");
                                                                            @endphp
                                                                            @if($pos !== false)
                                                                                <a href="{{asset(Storage::url($cheque))}}" target="_blank"><button type="button" class="btn btn-primary btn-block w-100">View</button></a>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                     </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-actions center">
                                        <button id="cancel-button-profile" type="button" class="btn btn-warning mr-1">
                                            Cancel
                                        </button>
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
        $("#cancel-button-profile").on('click',function (){
            window.location.href = "{{route("admin.human_resource.employee_directory.index")}}";
        });
        var today = new Date();
        today.setHours(0,0,0,0);
        $(document).ready(function() {
            $('#profile-form #emergency_contact, #profile-form #personal_number , #profile-form #official_number , #references-form #references_phone').inputmask({
                'mask': '9999-9999999',
                'clearIncomplete': true
            });

            $('#profile-form #bolt_pin').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'mask': '9999',
                'clearIncomplete': true,
            });

            $('.email_mask').inputmask({
                'alias': 'email',
                'clearIncomplete': true
            });

            $('#profile-form #cnic').inputmask({
                'mask': '99999-9999999-9',
                'clearIncomplete': true
            });

            $('#references-form #reference_years').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
            });

            var cnic_issue_date = $('#profile-form #cnic_issue_date').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                max: today,
                onSet: function(context) {
                    if (context.select) {
                        $('#profile-form #cnic_expiry_date').pickadate('picker').set('min', $('#profile-form #cnic_issue_date').pickadate('picker').get('select'));
                    }
                }
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

            var cnic_expiry_date = $('#profile-form #cnic_expiry_date').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                min: today,
            });

            var dob = $('#medical-form .dob_family_member').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: 100,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenPrefix: 'formatted_',
                max: today,
            });

            var passing_year = $('#education-form .passing_year').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenPrefix: 'formatted_',
                max: today,
            });

            var from = $('#employment_history-form .from_employment').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenPrefix: 'formatted_',
                max: today,
            });

            var to = $('#employment_history-form .to_employment').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenPrefix: 'formatted_',
                max: today,
            });

            $("#religion").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Religion",
                width:'100%',
            });
            $("#religion").val("{{$employee->religion_id ?? ''}}").trigger('change');

            $("#domicile").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Domicile",
                width:'100%',
            });
            $("#domicile").val("{{$employee->domicile_id ?? ''}}").trigger('change');

            $("#marital_status").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Maritial Status",
                width:'100%',
            });
            $("#marital_status").val("{{$employee->marital_status_id ?? ''}}").trigger('change');

            $("#blood_group").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Blood Group",
                width:'100%',
            });
            $("#blood_group").val("{{$employee->blood_group ?? ''}}").trigger('change');

            $("#nationality").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Nationality",
                width:'100%',
            });
            $("#nationality").val("{{$employee->nationality_id ?? ''}}").trigger('change');

            $("#designation").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Designation",
                width:'100%',
            });
            $("#designation").val("{{$employee->designation_id ?? ''}}").trigger('change');

            $("#city").prepend('<option value="" selected></option>').select2({
                placeholder: "Select City",
                width:'100%',
            });
            $("#city").val("{{$employee->city_id ?? ''}}").trigger('change');


            $("#shift_list").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Working Shift",
                width:'100%',
            });
            $("#shift_list").val("{{$employee->shift_id ?? ''}}").trigger('change');

            $("#place_of_birth").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Place of Birth",
                width:'100%',
            });
            $("#place_of_birth").val("{{$employee->place_of_birth ?? ''}}").trigger('change');

            $("#zone").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Zone",
                width:'100%',
            });
            $("#zone").val("{{$employee->zone_id ?? ''}}").trigger('change');

            $("#department").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Department",
                width:'100%',
            });

            $("#department").on('change',function(){
                id = $(this).val();
                $.ajax({
                    url: '{!! route('admin.human_resource.employee_directory.get.designation') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'department_id': id
                    }
                })
                .done(function(data) {
                    $("#designation").html('');
                    if(data.status == 1)
                    {
                        $.each(data.designations,function (i,value){
                            $("#designation").append('<option value='+value.id+'>'+value.name+'</option>');
                        });
                        $("#designation").val("{{$employee->designation_id}}").trigger('change');
                    }
                    else{
                        toastr.error(data.error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                });
            });

            $("#department").val("{{$employee->department_id ?? ''}}").trigger('change');


            var family_member_index = 0;
            $(".marital_status_family_member").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Marital Status",
                width:'100%',
            });

            $(".relationship_family_member").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Relationship",
                width:'100%',
            });

            @foreach($medical_infos as $medical_info)
            $("#marital_status_family_member_{{$loop->index}}").val({{$medical_info->marital_status}}).trigger('change');
            $("#relationship_family_member_{{$loop->index}}").val({{$medical_info->relationship_id}}).trigger('change');
            family_member_index = {{$loop->index}};
            @endforeach

            $("#bank_name").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Bank",
                width:'100%',
            });
            $("#bank_name").val("{{$bank_info->bank_id ?? ''}}").trigger('change');


            $(".grade").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Grade",
                width:'100%',
            });
            var education_background_index = 0;

            @foreach($educations as $education)
            $("#grade_{{$loop->index}}").val("{{$education->grade}}").trigger('change');
            education_background_index = {{$loop->index}};
            @endforeach

            $("#add_family_member").on('click',function () {
                family_member_index++;
                html = `<div class="row">
                            <hr style="width:100%">
                            <div class="col-md-10">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Name Of Family Member<span class="text-danger">*</span></label>
                                        <input type="text" data-rule-required="true" data-msg-required="Name is required" class="form-control border-primary name_family_member validated" value="" name="name[${family_member_index}]" id="family_name${family_member_index}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Relationship<span class="text-danger">*</span></label>
                                        <select name="relationship[${family_member_index}]" data-rule-required="true" data-msg-required="Relationship is required" id="relationship_family_member_${family_member_index}" class="select2 form-control relationship_family_member validated" style="width: 100%">
                                            @foreach($relationships as $relationship)
                <option value="{{$relationship->id}}">{{$relationship->name}}</option>
                                            @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <label>Date Of Birth<span class="text-danger">*</span></label>
            <div class="form-group input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                        <span class="la la-calendar-o small-calender-icon"></span>
                    </span>
                </div>
                <input type="text" name="dob[${family_member_index}]" id="dob${family_member_index}" data-rule-required="true" data-msg-required="Date Of Birth is required"  class="form-control bg-primary border-primary white rounded-right pickadate dob_family_member validated" placeholder="Date Of Birth">
                                    </div>
                                </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Marital Status<span class="text-danger">*</span></label>
                                    <select name="marital_status[${family_member_index}]" data-rule-required="true" data-msg-required="Marital Status is required" id="marital_status_family_member_${family_member_index}" class="select2 form-control marital_status_family_member validated" style="width: 100%">
                                        @foreach($maritial_statuses as $maritial_status)
                <option value="{{$maritial_status->id}}">{{$maritial_status->name}}</option>
                                        @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-body">
            <button type="button" class="btn btn-danger btn-xs mt-2 remove_family_member"><i class="ft-minus"></i></button>
        </div>
    </div>
</div>`;
                $("#medical_info_container").append(html);
                $('#medical-form .dob_family_member').pickadate({
                    firstDay: 1,
                    clear: '',
                    selectYears: 100,
                    selectMonths: true,
                    formatSubmit: 'yyyy-mm-dd 00:00:00',
                    hiddenPrefix: 'formatted_',
                    max: today,
                });
                $("#marital_status_family_member_" + family_member_index).prepend('<option value="" selected></option>').select2({
                    placeholder: "Select Marital Status",
                    width:'100%',
                });
                $("#relationship_family_member_" + family_member_index).prepend('<option value="" selected></option>').select2({
                    placeholder: "Select Relationship",
                    width:'100%',
                });
            });

            $("#add_education").on('click',function () {
                education_background_index++;
                html = `<div class="row">
                            <hr style="width:100%">
                            <div class="col-md-10">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Name Of Institute<span class="text-danger">*</span></label>
                                        <input type="text" data-rule-required="true" data-msg-required="Name of Institute is required" class="form-control border-primary name_institute validated" value="" name="name[${education_background_index}]" id="education_name${education_background_index}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Degree Awarded<span class="text-danger">*</span></label>
                                        <input type="text" data-rule-required="true" data-msg-required="Degree is required" class="form-control border-primary degree validated" value="" name="degree[${education_background_index}]" id="degree${education_background_index}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Grade Achieved<span class="text-danger">*</span></label>
                                        <select name="grade[${education_background_index}]" id="grade_${education_background_index}" data-rule-required="true" data-msg-required="Grade is required" class="select2 form-control grade validated" style="width: 100%">
                                            <option>A+</option>
                                            <option>A</option>
                                            <option>B</option>
                                            <option>C</option>
                                            <option>D</option>
                                            <option>E</option>
                                            <option>F</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label>Graduation Year<span class="text-danger">*</span></label>
                                    <div class="form-group input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o small-calender-icon"></span>
                                            </span>
                                        </div>
                                        <input type="text" data-rule-required="true" data-msg-required="Graduation Year is required" name="passing_year[${education_background_index}]"  class="form-control bg-primary border-primary white rounded-right pickadate passing_year validated" placeholder="Graduation Year" id="passing_year${education_background_index}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-body">
                                    <button type="button" class="btn btn-danger btn-xs mt-2 remove_education"><i class="ft-minus"></i></button>
                                </div>
                            </div>
                        </div>`;
                $("#education_info_container").append(html);
                $("#grade_"+ education_background_index).prepend('<option value="" selected></option>').select2({
                    placeholder: "Select Grade",
                    width:'100%',
                });
                $('#education-form .passing_year').pickadate({
                    firstDay: 1,
                    clear: '',
                    selectYears: true,
                    selectMonths: true,
                    formatSubmit: 'yyyy-mm-dd 00:00:00',
                    hiddenPrefix: 'formatted_',
                    max: today,
                });
            });
            var employment_index = 0;
            $("#add_employment").on('click',function () {
                employment_index++;
                html = `<div class="row">
                            <hr style="width:100%">
                            <div class="col-md-10">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Company/Organization Name<span class="text-danger">*</span></label>
                                        <input type="text" data-rule-required="true" data-msg-required="Company/Organization Name is required" class="form-control border-primary name_emplotment validated" value="" name="name[${employment_index}]" id="employment_name${employment_index}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Position/Designation<span class="text-danger">*</span></label>
                                        <input type="text" data-rule-required="true" data-msg-required="Position/Designation is required" class="form-control border-primary position_employment validated" value="" name="position[${employment_index}]" id="position${employment_index}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label>From<span class="text-danger">*</span></label>
                                    <div class="form-group input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o small-calender-icon"></span>
                                            </span>
                                        </div>
                                        <input type="text" data-rule-required="true" data-msg-required="From is required" name="from[${employment_index}]"  class="form-control bg-primary border-primary white rounded-right pickadate from_employment validated" placeholder="From" id="employment_from${employment_index}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label>To<span class="text-danger">*</span></label>
                                    <div class="form-group input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o small-calender-icon"></span>
                                            </span>
                                        </div>
                                        <input type="text" name="to[${employment_index}]" data-rule-required="true" data-msg-required="To is required"  class="form-control bg-primary border-primary white rounded-right pickadate to_employment validated" placeholder="To" id="employment_to${employment_index}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Reason<span class="text-danger">*</span></label>
                                        <textarea data-rule-required="true" data-msg-required="Reason is required" class="form-control border-primary reason_employment validated" name="reason[${employment_index}]" id="employment_reason${employment_index}"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-body">
                                    <button type="button" class="btn btn-danger btn-xs mt-2 remove_employment"><i class="ft-minus"></i></button>
                                </div>
                            </div>
                        </div>`;

                $("#employment_info_container").append(html);
                $('#employment_history-form .from_employment').pickadate({
                    firstDay: 1,
                    clear: '',
                    selectYears: true,
                    selectMonths: true,
                    formatSubmit: 'yyyy-mm-dd 00:00:00',
                    hiddenPrefix: 'formatted_',
                    max: today,
                });
                $('#employment_history-form .to_employment').pickadate({
                    firstDay: 1,
                    clear: '',
                    selectYears: true,
                    selectMonths: true,
                    formatSubmit: 'yyyy-mm-dd 00:00:00',
                    hiddenPrefix: 'formatted_',
                    max: today,
                });
            });

            $(document).on('click',".remove_family_member , .remove_education , .remove_employment",function () {
                $(this).closest('div.row').remove();
            });

            $('#profile-form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                }
            });

            $('#medical-form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                }
            });

            $('#bank-form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                }
            });

            $('#education-form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                }
            });

            $('#employment_history-form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                }
            });

            $('#attachments-form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                }
            });

            $('#references-form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                }
            });

        });
    </script>
@endsection