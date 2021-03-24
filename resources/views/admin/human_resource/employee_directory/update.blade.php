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
            <div id="tabs" class="card-body">
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
                        <form id="profile-form" class="form form-horizontal" method="post" action="">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-body">
                                        <h4 class="form-section">Personal Info</h4>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                    <label>Father's/Husband Name</label>
                                                    <input type="text" id="name" class="form-control border-primary" value="" name="name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                    <label>Religion</label>
                                                    <select name="religion" id="religion" class="select2 form-control required" style="width: 100%">
                                                        @foreach($religions as $religion)
                                                            <option id="{{$religion->id}}">{{$religion->name}}</option>
                                                        @endforeach
                                                    </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                    <label>Nationality</label>
                                                    <select name="nationality" id="nationality" class="select2 form-control required" style="width: 100%">
                                                        @foreach($nationalities as $nationality)
                                                            <option id="{{$nationality->id}}">{{$nationality->name}}</option>
                                                        @endforeach
                                                    </select>
                                             </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                    <label>Domicile</label>
                                                    <select name="domicile" id="domicile" class="select2 form-control required" style="width: 100%">
                                                        @foreach($domiciles as $domicile)
                                                            <option id="{{$domicile->id}}">{{$domicile->name}}</option>
                                                        @endforeach
                                                    </select>
                                             </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                    <label>Marital Status</label>
                                                    <select name="marital_status" id="marital_status" class="select2 form-control required" style="width: 100%">
                                                        @foreach($maritial_statuses as $maritial_status)
                                                            <option id="{{$maritial_status->id}}">{{$maritial_status->name}}</option>
                                                        @endforeach
                                                    </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                    <label>Blood Group</label>
                                                    <input type="text" id="blood_group" class="form-control border-primary" value="" name="blood_group" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                    <label>Email</label>
                                                    <input type="email" id="email" class="form-control border-primary" value="" name="email" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                    <label>Address</label>
                                                    <textarea class="form-control border-primary" id="address" name="address"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Emergency Contact</label>
                                                <input type="text" id="emergency_contact" class="form-control border-primary" value="" name="emergency_contact" required>
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
                                                <input type="text" name="cnic_issue_date"  class="form-control bg-primary border-primary white rounded-right pickadate" id="cnic_issue_date" placeholder="CNIC Issue Date">
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
                                                <input type="text" name="cnic_expiry_date"  class="form-control bg-primary border-primary white rounded-right pickadate" id="cnic_expiry_date" placeholder="CNIC Expiry Date">
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-body">
                                        <h4 class="form-section">Official Info</h4>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <div class="form-group col-md-9">
                                                        <label>Company Name:</label>
                                                        {{--<span class="danger">*</span>--}}
                                                        @if(session('role_id') == 1 || in_array(250, session('permissions')))
                                                            <input type="text" minlength="3" id="name" class="form-control border-primary" data-rule-remote="" data-msg-remote="Company Name must be unique" data-rule-required="true" data-msg-required="Company Name is required" value="" name="name" required>
                                                        @else
                                                            <input type="text" minlength="3" id="name" class="form-control border-primary" data-rule-remote="" data-msg-remote="Company Name must be unique" data-rule-required="true" data-msg-required="Company Name is required" value="" name="name" required readonly>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <div class="form-group col-md-9">
                                                        <label>Person of Contact:</label>
                                                        <span class="danger">*</span>
                                                        <input type="text" id="poc"  data-rule-required="true" data-msg-required="Person of Contact is required" data-rule-maxlength="100" data-msg-maxlength="Person of Contact can be maximum 100 characters" class="form-control border-primary" value="" name="poc" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <div class="form-group col-md-9">
                                                        <label>Email Address</label>
                                                        <span class="danger">*</span>
                                                        <input type="email" id="email" class="form-control border-primary" data-rule-remote="" data-msg-remote="Email must be unique" data-rule-required="true" data-msg-required="Email address is required" value="" name="email" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <div class="form-group col-md-9">
                                                        <label>Address:</label>
                                                        <span class="danger">*</span>
                                                        <textarea type="text" id="address" class="form-control border-primary" data-rule-maxlength="255" data-msg-maxlength="Address can be maximum 255 characters" data-rule-required="true" data-msg-required="Address is required" value="" name="address" required></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <div class="form-group col-md-9">
                                                        <label>Phone Number 1</label>
                                                        <span class="danger">*</span>
                                                        <input type="text" id="phone" class="form-control border-primary" data-rule-required="true" data-msg-required="Phone number is required" value="" name="phone" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <div class="col-md-9">
                                                        <label>Phone Number 2:</label>
                                                        <input type="text" id="phone2" class="form-control border-primary" value="" name="phone2">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <div class="form-group col-md-9">
                                                        <label>CNIC No.</label>
                                                        <span class="danger">*</span>
                                                        <input type="text" id="cnic" class="form-control border-primary" data-rule-required="true" data-msg-required="CNIC Number is required" value="" name="cnic" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <div class="col-md-9">
                                                        <label>NTN Number</label>
                                                        <input type="text" id="ntn_no" class="form-control border-primary" value="" name="ntn_no">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <div class="col-md-9">
                                                        <label>URL</label>
                                                        <input type="text" data-rule-maxlength="190" data-msg-maxlength="URL can be maximum 190 characters" id="url" class="form-control border-primary" value="" name="url">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="form-group col-md-9">
                                                        <label>Product Type</label>
                                                        <span class="danger">*</span>
                                                        <select name="product_id" id="product_id" data-rule-required="true" data-msg-required="Product Type is required" class="select2 form-control required" style="width: 100%">

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row" id="product_name_div">
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="form-group col-md-9">
                                                                <label>Product Name</label>
                                                                <span class="danger">*</span>
                                                                <input type="text" id="product_name" class="form-control border-primary" value="" name="product_name" data-msg-required="Product Name is required">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <div class="col-md-9">
                                                        <label>Password:</label>
                                                        <input type="password" id="password" class="form-control border-primary" value="" name="password">
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="user_id" value="">
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <div class="form-group col-md-9">
                                                        <label>City</label>
                                                        <span class="danger">*</span>
                                                        <select name="city_id" id="city_id" data-rule-required="true" data-msg-required="City is required" class="select2 form-control required" style="width: 100%">

                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <div class="form-group col-md-9">
                                                        <label for="segments">Segments:
                                                            <span class="danger">*</span>
                                                        </label>

                                                        <select name="segment_id" id="segment_id" class="select2 form-control required" style="width: 100%">

                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <div class="col-md-9">
                                                        <label>STRN Number</label>
                                                        <input type="text" id="strn_no" class="form-control border-primary" value="" name="strn_no">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <div class="col-md-9">
                                                        <label>Brand Name</label>
                                                        <input type="text" id="brand_name" class="form-control border-primary" value="" name="brand_name">
                                                    </div>
                                                </div>
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
        var today = new Date();
        today.setHours(0,0,0,0);

        $(document).ready(function() {
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

            var cnic_expiry_date = $('#profile-form #cnic_expiry_date').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                min: today,
            });

            $("#religion").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Religion",
                width:'100%',
            });

            $("#domicile").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Domicile",
                width:'100%',
            });

            $("#marital_status").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Maritial Status",
                width:'100%',
            });

            $("#nationality").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Nationality",
                width:'100%',
            });
        });
    </script>
@endsection