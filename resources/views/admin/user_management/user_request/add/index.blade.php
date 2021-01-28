@extends('admin.layout.master')

@section('title', 'Add User Request')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Add User Request
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <form id="user_form" class="form-horizontal" method="POST" action="{{ route('admin.user_management.user_requests.add.store') }}" novalidate="novalidate">
                                {{ csrf_field() }}

                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
                                        <label for="name" class="font-weight-bold">Name</label>
                                        <div class="form-group">
                                            <input type="text" name="name" class="form-control" placeholder="Name*" data-rule-required="true" data-msg-required="Name is required">
                                        </div>
                                    </div>

                                   {{-- <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
                                        <div class="form-group">
                                            <input type="text" name="phone_number" id="phone_number" class="form-control" placeholder="Phone Number*" data-rule-required="true" data-msg-required="Phone Number is required">
                                        </div>
                                    </div>--}}

                                    {{--<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
                                        <div class="form-group">
                                            <input type="text" name="cnic" id="cnic" class="form-control" placeholder="CNIC*" data-rule-required="true" data-msg-required="CNIC is required">
                                        </div>
                                    </div>--}}

                                  {{--  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
                                        <div class="form-group">
                                            <input type="email" name="email" class="form-control" placeholder="Email*" data-rule-required="true" data-msg-required="Email is required" data-rule-remote="{{ route('admin.user_management.users.email') }}" data-msg-remote="Email must be unique">
                                        </div>
                                    </div>--}}


                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
                                        <label for="department" class="font-weight-bold">Department</label>
                                        <div class="form-group">
                                            <select name="department" class="select2" id="department" data-rule-required="true" data-msg-required="Department is required">
                                                @foreach($departments as $department)
                                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                   {{-- <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
                                        <div class="form-group">
                                            <select name="default_hub" class="select2" id="default_hub" data-rule-required="true" data-msg-required="Default hub is required">
                                                @foreach($hubs as $hub)
                                                    <option value="{{ $hub->id }}">{{ $hub->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>--}}

                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
                                        <label for="designation" class="font-weight-bold">Designation</label>
                                        <div class="form-group">
                                            <input type="text" name="designation" class="form-control" placeholder="Designation*" data-rule-required="true" data-msg-required="Designation is required">
                                        </div>
                                    </div>

                                    <div class="col text-center">
                                        <h4 class="font-weight-bold">Also want to create OUTLOOK ID?</h4>
                                        <input type="checkbox" name="outlook_email" id="outlook_email" class="switchery outlook_email" data-switchery="true">
                                    </div>
                                    {{--<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
                                        <div class="form-group">
                                            <input type="text" name="trax_id" id="trax_id" class="form-control" placeholder="Trax ID">
                                        </div>
                                    </div>--}}

                                    <div class="col-12">
                                        <h4 class="form-section mb-2">Hubs</h4>
                                        <div class=" text-center mt-2">
                                            <button type="button" id="selectAll"  class="btn btn-primary" >Select All Hubs</button>
                                            <button type="button" id="unselect" class="btn btn-primary">Unselect All Hubs</button>
                                            </di>
                                        </div>
                                        @foreach($hubs as $hub)
                                            <fieldset class="d-inline-block m-1">
                                                    <input type="checkbox" id="hub_{{ $hub->id }}" class="hub" name="hub_ids[]" value="{{ $hub->id }}">
                                                <label for="hub_{{ $hub->id }}">{{ $hub->name }}</label>
                                            </fieldset>
                                        @endforeach
                                    </div>

                                <div class="col-12">
                                    <div class="form-group text-center mt-2">
                                        <button type="submit" class="btn btn-primary">Add</button>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
            <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>


    <script>
        $(document).ready(function() {
            $('#user_form #department').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Department*'
            });

            $('#user_form #default_hub').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Default Hub*'
            });

            $('#user_form #phone_number').inputmask({
                'mask': '9999-9999999',
                'clearIncomplete': true
            });
            $('#user_form #trax_id').inputmask({
               /* 'mask': '9999-9999999',*/
                'digits' : 10,
                'min': 6,
                'max': 10
            });

            $('#user_form #cnic').inputmask({
                'mask': '99999-9999999-9',
                'clearIncomplete': true
            });

            $('#user_form .hub').each(function() {
                var checkbox = $(this);
                var label = checkbox.next();
                var text = label.text();

                label.remove();

                checkbox.iCheck({
                    checkboxClass: 'icheckbox_line pt-1 pb-1',
                    checkedClass: 'checked bg-success',
                    uncheckedClass: 'bg-danger',
                    insert: '<div class="icheck_line-icon"></div>' + text
                });
            });

            $('#user_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    checked = $(".hub:checked").length;
                    if(checked > 0){
                        $(form).find('button[type=submit]').attr('disabled', 'disabled');
                        swal({
                            title: 'Please Wait!',
                            text: 'User is being added!',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        form.submit();
                    }
                    else{
                        var error = 'Please select at least one Hub';
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                }
            });
            $('#selectAll .hub').each(function() {
                var checkbox = $(this);
                var label = checkbox.next();
                var text = label.text();

                label.remove();

                checkbox.iCheck({
                    checkboxClass: 'icheckbox_line pt-1 pb-1',
                    checkedClass: 'checked bg-success',
                    uncheckedClass: 'bg-danger',
                    insert: '<div class="icheck_line-icon"></div>' + text
                });
            });

            $("#selectAll").click(function() {

                $('input.hub').each(function () {
                    var _this = $(this);
                    if(_this.is(':checked') == false) {
                        _this.iCheck('check');
                    }
                });
            });
            $("#unselect").click(function() {

                $('input.hub').each(function () {
                    var _this = $(this);
                    if(_this.is(':checked') == true) {
                        _this.iCheck('uncheck');
                    }
                });
            });
        });
        $('#user_form').on('keypress',function (e) {
            if(e.keyCode == 13) {
                e.preventDefault();
            }
        });

    </script>
@endsection