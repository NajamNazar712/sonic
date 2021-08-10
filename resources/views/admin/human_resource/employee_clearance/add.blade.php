@extends('admin.layout.master')

@section('title', 'Add Employee Information')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Add Employee Information
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <form id="employee_information_form" action="{{route('admin.human_resource.employee_clearance.submit')}}" method="post">

                                @csrf
                                @method('post')
                                <fieldset>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">
                                                    Employee ID:
                                                    <span class="danger">*</span>
                                                </label>
                                                <input type="text" class="form-control required" name="employee_id" placeholder="Employee ID">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="shipper_poc">
                                                   Employee Name:
                                                    <span class="danger">*</span>
                                                </label>
                                                <input type="text" class="form-control required" placeholder="Employee Name (Alphabet Only)" name="employee_name">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="company_address">Designation:
                                                    <span class="danger">*</span>
                                                </label>
                                                <input type="text" class="form-control required"  name="designation" placeholder="Designation">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone">Department:
                                                    <span class="danger">*</span>
                                                </label>
                                                <select name="department_id" id="department_id" class="select2 form-control required" style="width: 100%">
                                                    @foreach($departments as $department)
                                                        <option value="{{$department->id}}">{{$department->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="emp_status">Emp. Status:
                                                    <span class="danger">*</span>
                                                </label>
                                                <select name="status" id="status" class="select2 form-control required" style="width: 100%">
                                                    @foreach($employee_statuses as $status)
                                                        <option value="{{$status->id}}">{{$status->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone2">Sub-Department</label>
                                                <input type="text" class="form-control" placeholder="Sub-Department"  name="sub_department">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="ntn_no">Region / Grade </label>
                                                <input type="text" class="form-control" placeholder="(e.g: 1234567-8)"  name="region">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="strn_no">Location:</label>
                                                <input type="text" class="form-control" placeholder="(e.g: 1234567891234)" value="{{ old('strn_no') }}"  name="strn_no">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="url">CNIC No:</label>
                                                <span class="danger">*</span>
                                                <input type="text" class="form-control required" name="cnic" placeholder="Webiste / Facebook Page">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">

                                                <label for="shipper_city">Date of Joining:
                                                    <span class="danger">*</span>
                                                </label>
                                                <div>
                                                    <input type="text" class="form-control required" name="joining_date" placeholder="Date of Joining">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nature_of_account">Mobile No:
                                                    <span class="danger">*</span>
                                                </label>
                                                <div>
                                                    <input type="text" class="form-control required" name="mobile_number" placeholder="Mobile No">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">

                                                <label for="shipper_product_type">Date of Resign:
                                                    <span class="danger">*</span>
                                                </label>
                                                <div>
                                                    <input type="text" class="form-control required" name="resign_date" placeholder="Date of Joining">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 d-none" id="product_name_div">
                                            <div class="form-group">
                                                <label for="product_name">Email Address:
                                                    <span class="danger">*</span>
                                                </label>
                                                <div>
                                                    <input type="text" class="form-control"  name="email" placeholder="Email">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="average_shipment">Last Working Date:
                                                    <span class="danger">*</span>
                                                </label>
                                                <div>
                                                    <input type="text" class="form-control required"  name="average_shipment" placeholder="Last Working Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                       <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="average_shipment_duration">Line Manager:
                                                    <span class="danger">*</span>
                                                </label>
                                                <div>
                                                    <input type="text" class="form-control required"  name="line_manager" placeholder="Line Manager">
                                                </div>
                                            </div>
                                        </div>
                                           <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="average_shipment_duration">HOD:
                                                    <span class="danger">*</span>
                                                </label>
                                                <div>
                                                    <input type="text" class="form-control required"  name="hod" placeholder="HOD">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="reference">Created By:</label>
                                                <div>
                                                    <input type="text" class="form-control required"  name="hod" placeholder="Created By">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6" id="sale_person_div">
                                            <div class="form-group">
                                                <label for="reference">Creation Date:</label>
                                                <div>
                                                    <input type="text" class="form-control required"  name="hod" placeholder="Creation Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <div class="row justify-content-center">
                                    <div class="col-md-6 text-center">
                                        <button type="submit" id="submit_employee_info" class="btn btn-primary">Submit</button>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>


    <script>
        $(document).ready(function() {

        });
    </script>
@endsection