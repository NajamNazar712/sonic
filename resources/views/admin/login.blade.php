<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Trax, Sonic">
    <meta name="keywords" content="Trax">
    <meta name="author" content="Trax IT">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Sonic | Trax</title>
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('img/apple-touch-icon_new.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon_new-32x32.png') }} ">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon_new-16x16.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{  asset('img/favicon_new.ico') }}">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Quicksand:300,400,500,700"
          rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/line-awesome/css/line-awesome.min.css')}}">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/vendors.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/custom.css')}}">
    <!-- END VENDOR CSS-->
    <!-- BEGIN MODERN CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/app.css')}}">
    <!-- END MODERN CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/menu/menu-types/vertical-overlay-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/colors/palette-gradient.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/pages/login-register.css')}}">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/style.css')}}">
    <!-- END Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('css/login.css')}}?v=2.2">
</head>
<body class="vertical-layout vertical-overlay-menu 1-column  bg-full-screen-image menu-expanded blank-page blank-page"
      data-open="click" data-menu="vertical-overlay-menu" data-col="1-column">
<!-- ////////////////////////////////////////////////////////////////////////////-->
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <section class="flexbox-container">
                <div class="col-12 d-flex align-items-center justify-content-end">

                    <div class="col-md-4 col-10 p-0">
                        <div class="mb-2 text-center">
                            <p class="white bold" style="font-size: 17px !important;">For Help Dial : 021-111-118-729 or email us info@trax.pk</p>
                        </div>

                        <div class="card box-shadow-1 border-grey border-lighten-3 px-1 py-1 m-0">
                            <div class="card-header border-0">
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
                            </div>
                            <div class="card-content">
                                <p class="card-subtitle line-on-side text-muted text-center font-small-3 mx-2 my-1">
                                    <span>Login Details</span>
                                </p>
                                <div class="card-body">
                                    @include('admin.inc.messages')
                                    <form class="form-horizontal" id="admin_login_form" method="POST" action="{{ route('admin.login.submit') }}" autocomplete="off">
                                    {{ csrf_field()  }}
                                        <fieldset class="form-group position-relative has-icon-left">
                                            <input type="text" name="phone_number" class="form-control {{ $errors->has('phone_number') ? ' is-invalid' : '' }}" id="phone_number" placeholder="Phone Number" value="{{old('phone_number')}}"  required>
                                            <div class="form-control-position">
                                                <i class="ft-user"></i>
                                            </div>
                                        </fieldset>
                                        <fieldset class="form-group position-relative has-icon-left">
                                            <input type="password" name="pin" class="form-control {{ $errors->has('pin') ? ' is-invalid' : '' }}" id="pin" placeholder="Enter Pin" required>
                                            <div class="form-control-position">
                                                <i class="la la-key"></i>
                                            </div>
                                            @if ($errors->has('pin'))
                                                <span class="invalid-feedback">
                                            <strong>{{ $errors->first('pin') }}</strong>
                                            </span>
                                            @endif
                                        </fieldset>
                                        <div class="form-group row">
                                            <div class="col-md-6 col-12 text-center text-sm-left">
                                                <fieldset>
                                                    <input type="checkbox" id="remember-me" class="chk-remember" {{ old('remember') ? 'checked' : '' }}>
                                                    <label for="remember-me"> Remember Me</label>
                                                </fieldset>
                                            </div>
                                            <div class="col-md-6 col-12 float-sm-left text-center text-sm-right"><a href="{{ route('admin.password.request') }}" class="card-link">Forgot Pin?</a></div>
                                        </div>
                                        <button type="button" class="btn btn-outline-info btn-block" id="login_button"><i class="ft-unlock"></i> Login</button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </section>
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
                        <div class="modal-body  text-center">
                            <div class="row justify-content-center">
<!--                                <div class="form-group form-inline">
                                    <p>We have sent a six-digit verification code on mobile,<br>Please verify by entering it below</p>
                                </div>-->
                                <div class="form-group form-inline">
                                    <input type="text" class="form-control otp" autofocus id="otp_input" placeholder="Enter Verification Code">
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button tabindex="-1" type="button" class="btn btn-primary ml-1" id="otp_submit" disabled>Enter</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ////////////////////////////////////////////////////////////////////////////-->
<!-- BEGIN VENDOR JS-->
<script src="{{asset('app-assets/vendors/js/vendors.min.js')}}" ></script>
<!-- BEGIN VENDOR JS-->
<!-- BEGIN PAGE VENDOR JS-->
<script src="{{asset('app-assets/vendors/js/forms/validation/jqBootstrapValidation.js')}}"
></script>
<script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" ></script>
<!-- END PAGE VENDOR JS-->
<!-- BEGIN MODERN JS-->
<script src="{{asset('app-assets/js/core/app-menu.js')}}" ></script>
<script src="{{asset('app-assets/js/core/app.js')}}" ></script>
<!-- END MODERN JS-->
<!-- BEGIN PAGE LEVEL JS-->
<script src="{{asset('app-assets/js/scripts/forms/form-login-register.js')}}" ></script>
<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
<!-- END PAGE LEVEL JS-->

<script type="text/javascript">
    $(document).ready(function () {
        var phone_number = null;
        var pin = null;


        $('#otp_input').inputmask({
            'alias': 'integer',
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            'mask': '999999'
        });

        $('#phone_number').inputmask({
            'mask': '9999-9999999',
            'clearIncomplete': true
        });

        $('#pin').inputmask({
            'alias': 'integer',
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            'mask': '9999',
        });

        $('body').on('keyup change','#otp_input',function() {
            if($(this).val().length === 6){
                $('#otp_submit').attr('disabled', false);
            }
            else{
                $('#otp_submit').attr('disabled', true);
            }
        });
        $('#otp_submit').on('click', function () {
            var otp = $('#otp_input').val();

            if(otp.length == 6){
                $.ajax({
                    url: '{!! route('admin.login.verify_otp') !!}',
                    type: 'POST',
                    data: {
                        'phone_number': phone_number,
                        'otp': otp,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status === 0){
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        $('#otp_input').val('');
                        $('#otp_submit').attr('disabled', true);
                    }else{
                        $('#PasswordModal').modal('hide');
                        $('#admin_login_form').submit();
                    }
                });
            }
        });
        $('#phone_number').on('change', function () {
            var phone_check = $('#phone_number').valid();
            if(!phone_check){
                $('#phone_number-error').addClass('danger');
            }
        });
        $('#pin').on('change', function () {
            var pin_check = $('#pin').valid();
            if(!pin_check){
                $('#pin-error').addClass('danger');
            }
        });
        $('#otp_input').keypress(function (event) {
            if(event.keyCode == 13){
                var otp = $('#otp_input').val();

                if(otp.length == 6){
                    $.ajax({
                        url: '{!! route('admin.login.verify_otp') !!}',
                        type: 'POST',
                        data: {
                            'phone_number': phone_number,
                            'otp': otp,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if(data.status === 0){
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            $('#otp_input').val('');
                            $('#otp_submit').attr('disabled', true);
                        }else{
                            $('#OtpModal').modal('hide');
                            $('#admin_login_form').submit();
                        }
                    });
                }
            }

        });
        $('#login_button').on('click', function () {
            var phone_check = $('#phone_number').valid();
            var pin_check = $('#pin').valid();
            if(phone_check && pin_check){
                phone_number = $('#phone_number').val();
                pin = $('#pin').val();
                $.ajax({
                    url: '{!! route('admin.login.credentials') !!}',
                    method: 'POST',
                    data: {
                        'phone_number': phone_number,
                        'pin': pin,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status === 1){
                        $('#OtpModal').modal('show');
                    }else{
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                });
            }
            else{
                $('#phone_number-error').addClass('danger');
                $('#pin-error').addClass('danger');
            }
        });

        $('#admin_login_form input').keypress(function () {
            if(event.keyCode == 13){
                var phone_check = $('#phone_number').valid();
                var pin_check = $('#pin').valid();
                if(phone_check && pin_check){
                    phone_number = $('#phone_number').val();
                    pin = $('#pin').val();
                    $.ajax({
                        url: '{!! route('admin.login.credentials') !!}',
                        method: 'POST',
                        data: {
                            'phone_number': phone_number,
                            'pin': pin,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if(data.status === 1){
                            $('#OtpModal').modal('show');
                        }else{
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    });
                }
                else{
                    $('#phone_number-error').addClass('danger');
                    $('#pin-error').addClass('danger');
                }
            }
        });
        $('#OtpModal').on('shown.bs.modal', function () {
            $('#otp_input').focus();
        });  
    });
</script>
</body>
</html>