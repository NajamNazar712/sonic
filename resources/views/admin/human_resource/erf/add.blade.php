@extends('admin.layout.master')

@section('title', 'Add ERF')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Add ERF
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <form id="erf_form" class="form-inline" method="POST" action="{{ route('admin.human_resource.erf.submit') }}" novalidate="novalidate">
                                {{ csrf_field() }}
                                <div class="row m-2">
                                    <h2 class="mr-2"> ERF Type</h2>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="erf_type" id="additional"  value="1" checked />
                                        <label class="form-check-label" for="inlineRadio1" >Additional</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="erf_type" id="replacement" value="2"/>
                                        <label class="form-check-label" for="inlineRadio2">Replacement</label>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                        <div class="form-group">
                                            <select name="department" class="select2" id="department" data-rule-required="true" data-msg-required="Department is required">
                                                @foreach($departments as $department)
                                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                        <div class="form-group">
                                            <select name="designation" class="select2" id="designation" data-rule-required="true" data-msg-required="Designation is required">
                                                @foreach($designations as $designation)
                                                    <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                        <div class="form-group">
                                            <select name="hub" class="select2" id="hub" data-rule-required="true" data-msg-required="Hub is required">
                                                @foreach($hubs as $hub)
                                                    <option value="{{ $hub->id }}">{{ $hub->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                        <div class="form-group">
                                            <select name="city" class="select2" id="city" data-rule-required="true" data-msg-required="City is required">
                                                @foreach($cities as $city)
                                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                        <div class="form-group">
                                            <select name="department_head" class="select2" id="department_head" data-rule-required="true" data-msg-required="Department Head is required">
                                                @foreach($department_heads as $head)
                                                    <option value="{{ $head->id }}">{{ $head->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                        <div class="form-group">
                                            <select name="vacancies" class="select2" id="vacancies">
                                                @for ($i=1; $i<=10; $i++)
                                                    <option value="{{$i}}">{{$i}}</option>
                                                 @endfor
    }
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                        <div class="form-group">
                                            <select name="position" class="select2" id="position">
                                                @foreach($admin_positions as $position)
                                                    <option value="{{ $position->id }}">{{ $position->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                        <div class="form-group ">
                                            <h5 class="pull-left mr-2"><strong>Salary Range</strong></h5>

                                                <input type="number" class="form-control mr-1" id="from" name="salary_from" placeholder="From">

                                                 <input type="number" class="form-control" id="to" name="salary_to" placeholder="To">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                        <div class="form-group ">
                                            <textarea class="form-control" rows="5" cols="100" id="qualification" name="qualification" placeholder="Qualifications"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                        <div class="form-group ">
                                            <textarea class="form-control" rows="5" cols="100" id="skills" name="skills" placeholder="Required Skills"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                        <div class="form-group ">
                                            <textarea class="form-control" rows="5" cols="100" id="job_description" name="job_description" placeholder="Job Description"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                        <div class="form-group ">
                                            <select name="allowances[]" class="select2" id="allowance" multiple="multiple">
                                              @foreach($allowances as $allowance)
                                                  <option value="{{ $allowance->id }}">{{ $allowance->name }}</option>
                                              @endforeach
                                          </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-12">
                                    <div class="form-group justify-content-center mt-2">
                                        <button type="submit" class="btn btn-primary">Submit</button>
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
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>


    <script>
        $(document).ready(function() {
            $('#erf_form #department').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Department*'
            });

            $('#erf_form #hub').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Hub*'
            });
            $('#erf_form #city').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select City*'
            });
            $('#erf_form #designation').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Designation*'
            });

            $('#erf_form #department_head').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Line Manager*'
            });
            $('#erf_form #vacancies').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Number Of Vacancies'
            });
            $('#erf_form #position').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Position Type'
            });
            $('#allowance').select2({
                width:'100%',
                placeholder:"Allowances",
                allowClear:true,
                dropdownParent:$('#erf_form')
            });


          

            $('#erf_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Request is being submitted!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });
        });
        $('#erf_form').on('keypress',function (e) {
            if(e.keyCode == 13) {
                e.preventDefault();
            }
        });

    </script>
@endsection