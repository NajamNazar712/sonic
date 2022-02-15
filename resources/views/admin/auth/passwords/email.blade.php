<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Modern admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities with bitcoin dashboard.">
    <meta name="keywords" content="admin template, modern admin template, dashboard template, flat admin template, responsive admin template, web app, crypto dashboard, bitcoin dashboard">
    <meta name="author" content="Trax IT">
    <title>Reset Pin - Sonic | Trax</title>
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('img/apple-touch-icon_new.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon_new-32x32.png') }} ">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon_new-16x16.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{  asset('img/favicon_new.ico') }}">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Quicksand:300,400,500,700"
          rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/line-awesome/css/line-awesome.min.css')}}">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/vendors.css')}}">
    <!-- END VENDOR CSS-->
    <!-- BEGIN MODERN CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/app.css')}}">
    <!-- END MODERN CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/menu/menu-types/vertical-overlay-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/colors/palette-gradient.css')}}">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/style.css')}}">
    <!-- END Custom CSS-->

    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('css/login.css')}}?v=2.1">
</head>
<body class="vertical-layout vertical-overlay-menu 1-column bg-full-screen-image  menu-expanded blank-page blank-page"
      data-open="click" data-menu="vertical-overlay-menu" data-col="1-column">
<!-- ////////////////////////////////////////////////////////////////////////////-->
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <section class="flexbox-container">
                <div class="col-12 d-flex align-items-center justify-content-center">
                    <div class="col-md-4 col-10 box-shadow-2 p-0">
                        <div class="card border-grey border-lighten-3 px-2 py-2 m-0">
                            <div class="card-header border-0 pb-0">
                                <div class="card-title text-center">
                                    <div class="row align-items-center">
                                        <div class="col sonic_logo align-middle text-left">
                                            <img src="{{asset('img/sonic_logo_new.png')}}" alt="Sonic" class="d-inline-block mx-auto w-75">
                                        </div>

                                        <div class="col trax_logo align-middle text-right">
                                            <img src="{{asset('img/trax_logo_new.png')}}" alt="Trax" class="d-inline-block mx-auto w-75">
                                        </div>
                                    </div>
                                </div>
                                <h6 class="card-subtitle line-on-side text-muted text-center font-small-3 pt-2">
                                    <span>We will send you a OTP to reset pin.</span>
                                </h6>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                   @include('admin.inc.messages')
                                    <form class="form-horizontal" method="post" id="reset_pin_form">
                                        @csrf
                                        <fieldset class="form-group position-relative has-icon-left">
                                            <input type="text" class="form-control" value="" name="phone_number" id="phone_number"
                                                   placeholder="Phone Number" required>
                                            <div class="form-control-position">
                                                <i class="ft-phone"></i>
                                            </div>
                                        </fieldset>
                                        <button type="submit" class="btn btn-outline-info btn-block"><i class="ft-unlock"></i> Reset Pin</button>
                                    </form>
                                </div>
                                <p class="card-subtitle line-on-side text-muted text-center font-small-3 mx-2 my-1">
                                    <span>Go back to Login?</span>
                                </p>
                                <div class="card-body">
                                    <a href="{{route('admin.login')}}" class="btn btn-outline-danger btn-block"><i class="ft-user"></i>Login</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div class="modal fade" id="OtpModal" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="OtpModal"
         aria-hidden="true" style="top:30%;">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content col">
                <div class="modal-header text-center">
                    <div class="row align-items-center">
                        <div class="col sonic_logo align-middle text-left">
                            <img src="{{asset('img/sonic_logo_new.png')}}" alt="Sonic" class="d-inline-block mx-auto w-50">
                        </div>

                        <div class="col trax_logo align-middle text-right">
                            <img src="{{asset('img/trax_logo_new.png')}}" alt="Trax" class="d-inline-block mx-auto w-50">
                        </div>
                    </div>
                </div>
                <form method="post" class="form-horizontal" id="update_pin_form" action="{{route('admin.password.reset.pin')}}">
                    @csrf
                    <div class="modal-body  text-center">
                        <div class="row justify-content-center">
                            <input type="hidden" name="phone_number" id="optp_phone_number">
                            <div class="form-group col-12">
                                <input type="text" name="otp" class="form-control otp" data-rule-required="true" data-msg-required="Verification Code is Required" autofocus id="otp_input" placeholder="Enter Verification Code">
                            </div>
                            <div class="form-group col-6">
                                <input type="password" name="pin" data-rule-minlength="4" data-msg-min="Pin must be atleast 4 Characters" data-rule-required="true" data-msg-required="Pin is Required" class="form-control pin" id="pin_input" placeholder="Enter New Pin">
                            </div>
                            <div class="form-group col-6">
                                <input type="password" class="form-control pin_confirm" data-rule-minlength="4" data-msg-min="Pin must be atleast 4 Characters" data-rule-required="true" data-msg-required="Confirm Pin is Required" data-rule-equalTo="#pin_input" data-msg-equalTo="Confirm Pin Does not Match" id="pin_confirm_input" placeholder="Confirm New Pin">
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button tabindex="-1" type="submit" class="btn btn-primary ml-1" id="otp_submit">Enter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- ////////////////////////////////////////////////////////////////////////////-->
<!-- BEGIN VENDOR JS-->
<script src="{{asset('app-assets/vendors/js/vendors.min.js')}}" type="text/javascript"></script>
<!-- BEGIN VENDOR JS-->
<!-- BEGIN PAGE VENDOR JS-->
<script src="{{asset('app-assets/vendors/js/forms/validation/jqBootstrapValidation.js')}}"
        type="text/javascript"></script>
<!-- END PAGE VENDOR JS-->
<!-- BEGIN MODERN JS-->
<script src="{{asset('app-assets/js/core/app-menu.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/js/core/app.js')}}" type="text/javascript"></script>
<!-- END MODERN JS-->
<!-- BEGIN PAGE LEVEL JS-->
<script src="{{asset('app-assets/js/scripts/forms/form-login-register.js')}}" type="text/javascript"></script>
<!-- END PAGE LEVEL JS-->

<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

<script>
$(document).ready(function (){
    $('#phone_number').inputmask({
        'mask': '9999-9999999',
        'clearIncomplete': true
    });

    $('#otp_input').inputmask({
        'mask': '999999',
        'clearIncomplete': true
    });

    $('#pin_input').inputmask({
        'alias': 'integer',
        'allowMinus': false,
        'allowPlus': false,
        'rightAlign': false,
        'mask': '9999',
        'clearIncomplete': true
    });

    $('#pin_confirm_input').inputmask({
        'alias': 'integer',
        'allowMinus': false,
        'allowPlus': false,
        'rightAlign': false,
        'mask': '9999',
        'clearIncomplete': true
    });

    $('#reset_pin_form').validate({
        errorClass: 'danger',
        successClass: 'success',
        errorPlacement: function(error, element) {
            error.addClass('w-100').appendTo(element.parents('.form-group'));
        },
        submitHandler: function(form) {
            otp_generation();
            return false;
        }
    });

    $('#update_pin_form').validate({
        errorClass: 'danger',
        successClass: 'success',
        errorPlacement: function(error, element) {
            error.addClass('w-100').appendTo(element.parents('.form-group'));
        },
        submitHandler: function(form) {
           form.submit();
        }
    });

    function otp_generation(){
        var phone_number = $('#phone_number').val();
        $("#optp_phone_number").val("");
        if(phone_number){
            $.ajax({
                url: '{!! route('admin.password.generate.otp') !!}',
                method: 'POST',
                data: {
                    'phone_number': phone_number,
                    '_token': '{{ csrf_token() }}'
                }
            }).done(function (data) {
                if(data.status == 1){
                    $('#OtpModal').modal('show');
                    $("#optp_phone_number").val(phone_number);
                    $('#otp_input').focus();
                }
                else{
                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                }
            });
        }
        else{
            var error = "Enter Phone Number!";
            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
        }

    }
});
</script>
</body>
</html>