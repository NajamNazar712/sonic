@extends('admin.layout.master')
@section('title','Change Password')

@section('content')
    <h1 class="mb-1">
        Change Password
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <form id="password-form" class="form form-horizontal" method="post" action="{{route('admin.update.profile.password.submit')}}">
                    @csrf
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label for="password">Enter Password:<span class="danger">*</span>
                                        </label>
                                        <div class="form-group position-relative">
                                            <input type="password" class="form-control required" id="new_password" placeholder="Minimum 6 Character" value="" name="password" data-rule-minlength="6" data-msg-minlength="Password needs to be at-least 6 Characters">
                                            <div class="form-control-position" id="peye">
                                                <i class="la la-eye success"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="form-group col-md-9">
                                        <label for="password">Confirm Password:<span class="danger">*</span>
                                        </label>
                                        <div class="form-group position-relative">
                                            <input type="password" class="form-control required" id="confirm_password" placeholder="Minimum 6 Character" value="" name="confirm_password">
                                            <div class="form-control-position" id="cpeye">
                                                <i class="la la-eye success"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <button type="submit" class="btn btn-primary col-2">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/selectize/selectize.css')}}">
@endsection


@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function() {
            $('#peye').on('mousedown',function(){$('input[name="password"]').attr('type','text')}).on('mouseup',function(){$('input[name="password"]').attr('type','password')});
            $('#cpeye').on('mousedown',function(){$('input[name="confirm_password"]').attr('type','text')}).on('mouseup',function(){$('input[name="confirm_password"]').attr('type','password')});

            $( "#password-form" ).validate({
                errorClass:"danger",
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    var new_password = $('#new_password').val();
                    var confirm_password = $('#confirm_password').val();
                    if(new_password === confirm_password){
                        swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes to update password',
                            icon: 'warning',
                            buttons: {
                                cancel: {
                                    text: 'No',
                                    value: null,
                                    visible: true,
                                    closeModal: true,
                                },
                                confirm: {
                                    text: 'Yes',
                                    value: true,
                                    visible: true,
                                    closeModal: true
                                }
                            },
                            closeOnClickOutside: false,
                            closeOnEsc: false,
                            dangerMode: true
                        }).then(function (confirm) {
                            if(confirm){
                                form.submit();
                            }
                        });
                    }
                    else{
                        var error = "The password and confirmation password do not match";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }

                }
            });
        });
    </script>
@endsection