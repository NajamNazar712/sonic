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
                            <form id="employee_information_form" action="{{route('admin.human_resource.fnf.submit')}}" method="post"  novalidate="novalidate">

                                @csrf
                                @method('post')
                                <fieldset>
                                    <div class="row mb-1">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">
                                                    Employee ID:
                                                    <span class="danger">*</span>
                                                </label>
                                                <select name="trax_id" id="trax_id" class="select2 form-control required" style="width: 100%">
                                                    @foreach($employee as $data)
                                                        <option value="{{$data->trax_id}}">{{$data->trax_id}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="shipper_poc">
                                                   Employee Name:
                                                    <span class="danger">*</span>
                                                </label>
                                                <input type="text" class="form-control required" placeholder="Employee Name (Alphabet Only)" name="employee_name" id="employee_name" data-rule-required="true" data-msg-required="Name is required" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="company_address">Designation:
                                                    <span class="danger">*</span>
                                                </label>
                                                <select name="designation" id="designation" class="select2 form-control required" style="width: 100%" disabled>
                                                    @foreach($designations as $designation)
                                                        <option value="{{$designation->id}}">{{$designation->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone">Department:
                                                    <span class="danger">*</span>
                                                </label>
                                                <select name="department_id" id="department_id" class="select2 form-control required" style="width: 100%" disabled>
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
                                                <label for="shipper_poc">
                                                   City:
                                                    <span class="danger">*</span>
                                                </label>
                                                <input type="text" class="form-control required" placeholder="City" name="city" id="city" data-rule-required="true" data-msg-required="Name is required" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="reference">Date of Joining:</label>
                                                <div class="form-group input-group">
                                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                            <span class="la la-calendar-o small-calender-icon"></span>
                                        </span>
                                                    </div>
                                                    <input type="text" id="joining_date" name="joining_date" class="form-control bg-primary border-primary white rounded-right pickadate" id="joining_date" placeholder="Joining Date" data-rule-required="true" data-msg-required="Joining Date is required">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="reference">Date of Resign:</label>
                                                <div class="form-group input-group">
                                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                            <span class="la la-calendar-o small-calender-icon"></span>
                                        </span>
                                                    </div>
                                                    <input type="text" name="resign_date" class="form-control bg-primary border-primary white rounded-right pickadate" id="resign_date" placeholder="Resign Date" data-rule-required="true" data-msg-required="Resign Date is required">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-5">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="average_shipment_duration">Line Manager Email:
                                                    <span class="danger">*</span>
                                                </label>
                                                <div>
                                                    <input type="email" class="form-control required"  name="line_manager" placeholder="Line Manager Email" data-rule-required="true" data-msg-required="Email is required">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="average_shipment_duration">HOD Email:
                                                    <span class="danger">*</span>
                                                </label>
                                                <div>
                                                    <input type="text" class="form-control required"  name="hod" placeholder="HOD Email" data-rule-required="true" data-msg-required="Email is required">
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

            $('#designation').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Designation*'
            });
            
            $('#department_id').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Department*'
            });

            $('#trax_id').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Trax Id*'
            }).bind('change',function(){
                var id = $(this).val();
                if(id) {
                    $.ajax({
                        url: '{!! route('admin.human_resource.fnf.employee_data') !!}',
                        method: 'POST',
                        data: {
                            'id': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                       if(data.status == 1){
                          $('#employee_name').val(data.data.name);
                          $('#designation').val(data.data.designation).trigger('change');
                          $('#department_id').val(data.data.department).trigger('change');
                          $('#city').val(data.data.city);
                          $('#joining_date').val(data.data.joining_date);
                          
                       }
                       else{
                           toastr.error(data.error, 'Error!', {
                               positionClass: 'toast-top-center',
                               containerId: 'toast-top-center'
                           });
                       }
                    });
                }
            });

            var joining_date = $('#joining_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format:'yyyy-mm-dd',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                     //$('#to_date_root').css('top', '40px');
                    $('.picker').css('position','relative');
                },
                onSet: function(context) {

                }
            });

            var resign_date = $('#resign_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format:'yyyy-mm-dd',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                     //$('#to_date_root').css('top', '40px');
                    $('.picker').css('position','relative');
                },
                onSet: function(context) {

                }
            });

            $('#employee_information_form').validate({
                
                
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    $('#department_id').removeAttr('disabled');
                    $('#city').removeAttr('disabled');
                    $('#designation').removeAttr('disabled');
                    $('#employee_name').removeAttr('disabled');
                    
                    swal({
                        title: 'Please Wait!',
                        text: 'Your request is being processed!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });
                    form.submit();
                }
            });
        });
    </script>
@endsection