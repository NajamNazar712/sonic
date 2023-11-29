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
    <title>Get Started - Sonic | Trax</title>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
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

    <link rel="stylesheet" type="text/css" href="{{asset('css/login.css')}}?v=2.6
">
</head>
<body class="vertical-layout vertical-overlay-menu 1-column  bg-full-screen-image menu-expanded blank-page blank-page"
      data-open="click" data-menu="vertical-overlay-menu" data-col="1-column">
<!-- ////////////////////////////////////////////////////////////////////////////-->
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <section class="flexbox-container" style="overflow: auto;">
                <div class="col-12 d-flex align-items-center justify-content-center">
                    <div class="col-md-8 col-10 p-0">
                        <div class="mb-2 text-center">
                            <p class="white bold" style="font-size: 17px !important;">For Help Dial : <a href="tel:021-111-118-729" style="color:#ffffff;text-decoration:underline;">021-111-118-729</a>  or email us <a href="mailto:info@trax.pk" style="color:#ffffff;text-decoration:underline;">info@trax.pk</a></p>
                        </div>
                        <div class="card box-shadow-1 border-grey border-lighten-3 px-1 py-1 m-0">
                            <div class="card-header border-0">
                                <div class="card-title text-center">
                                    <div class="row align-items-center">
                                        <div class="col sonic_logo align-middle">
                                            <img src="{{asset('img/sonic_logo_new.png')}}" alt="Sonic" class="d-inline-block mx-auto w-25">
                                        </div>

                                        <div class="col trax_logo align-middle">
                                            <img src="{{asset('img/trax_logo_new.png')}}" alt="Trax" class="d-inline-block mx-auto w-25">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <p class="card-subtitle line-on-side text-muted text-center font-small-3 mx-2 my-1">
                                    <span>Get Started</span>
                                </p>
                                <div class="card-body">
                                    @include('client.inc.messages')
                                    <form class="form-horizontal" id="get_started_form" method="POST" action="{{ route('cod.getstarted') }}" novalidate>
                                        @csrf
                                        <div class="row">
                                            <div class="col-6">
                                                <fieldset class="form-group position-relative has-icon-left">
                                                    <input type="text" name="name" class="form-control" id="name" placeholder="Full Name" data-rule-required="true" data-msg-required="Full Name is required">
                                                    <div class="form-control-position">
                                                        <i class="ft-user"></i>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col-6">
                                                <fieldset class="form-group position-relative has-icon-left">
                                                    <input type="text" name="phone" class="form-control" id="phone" placeholder="Phone Number"data-rule-required="true" data-msg-required="Phone Number is required">
                                                    <div class="form-control-position">
                                                        <i class="ft-phone"></i>
                                                    </div>
                                                </fieldset>
                                            </div>

                                            <div class="col-6">
                                                <fieldset class="form-group position-relative has-icon-left">
                                                    <input type="text" name="email" class="form-control" id="email" placeholder="Email Address" data-rule-required="true" data-msg-required="Email is required">
                                                    <div class="form-control-position">
                                                        <i class="ft-mail"></i>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col-6">
                                                <fieldset class="form-group position-relative">

                                                    <select name="city" id="city" class="form-control select2" data-rule-required="true" data-msg-required="Business City is required">
                                                        @foreach($cities as $city)
                                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </fieldset>
                                            </div>
                                            <div class="col-6">
                                                <fieldset class="form-group position-relative">
                                                    <select name="territory" id="territory" class="form-control select2" data-rule-required="true" data-msg-required="Territory is required">
                                                    </select>
                                                </fieldset>
                                            </div>
                                            <div class="col-6">
                                                <fieldset class="form-group position-relative">
                                                    <select name="area" id="area" class="form-control select2" data-rule-required="true" data-msg-required="Area is required">
                                                    </select>
                                                </fieldset>
                                            </div>
                                            <div class="col-6">
                                                <fieldset class="form-group position-relative">
                                                    <select name="reference" id="reference" class="form-control select2" data-rule-required="true" data-msg-required="Reference is required">
                                                        @foreach($references as $reference)
                                                            <option value="{{ $reference->id }}">{{ $reference->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </fieldset>
                                            </div>
                                            <div class="col-6">
                                                <fieldset class="form-group position-relative">
                                                    <select name="service" id="service" class="form-control select2" data-rule-required="true" data-msg-required="Service is required">
                                                        @foreach($services as $service)
                                                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </fieldset>
                                            </div>
                                            <div class="col-12 d-none" id="brand_div">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <fieldset class="form-group position-relative has-icon-left">
                                                            <input type="text" name="company_name" class="form-control" id="company_name" placeholder="Company Name" data-rule-required="true" data-msg-required="Company Name is required">
                                                            <div class="form-control-position">
                                                                <i class="ft-user"></i>
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-6">
                                                        <fieldset class="form-group position-relative has-icon-left">
                                                            <input type="text" name="brand_name" class="form-control" id="brand_name" placeholder="Brand Name" data-rule-required="true" data-msg-required="Brand Name is required">
                                                            <div class="form-control-position">
                                                                <i class="ft-user"></i>
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                </div>

                                            </div>


                                            <div class="col-12">
                                                <fieldset class="form-group position-relative">
                                                    <textarea name="message" class="form-control" placeholder="Write Your Message" id="message" cols="30" rows="5" data-rule-required="true" data-msg-required="Message is required"></textarea>
                                                </fieldset>
                                            </div>

                                            <div class="col-12 text-center">
                                                <button type="submit" class="btn btn-outline-info btn-lg"><i class="ft-phone-forwarded"></i> Let's Talk</button>
                                            </div>
                                        </div>
                                        <div class="row justify-content-center">

                                        </div>


                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<script type="text/javascript">
    // var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    // (function(){
    //     var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    //     s1.async=true;
    //     s1.src='https://embed.tawk.to/5d8323ab9f6b7a4457e2756b/default';
    //     s1.charset='UTF-8';
    //     s1.setAttribute('crossorigin','*');
    //     s0.parentNode.insertBefore(s1,s0);
    // })();
</script>
<!-- ////////////////////////////////////////////////////////////////////////////-->
<!-- BEGIN VENDOR JS-->
<script src="{{asset('app-assets/vendors/js/vendors.min.js')}}" ></script>
<!-- BEGIN VENDOR JS-->
<!-- BEGIN PAGE VENDOR JS-->
<script src="{{asset('app-assets/vendors/js/forms/validation/jqBootstrapValidation.js')}}"
></script>
<script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" ></script>
<!-- END PAGE VENDOR JS-->
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
<!-- BEGIN MODERN JS-->
<script src="{{asset('app-assets/js/core/app-menu.js')}}" ></script>
<script src="{{asset('app-assets/js/core/app.js')}}" ></script>
<!-- END MODERN JS-->
<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

<!-- BEGIN PAGE LEVEL JS-->
<script src="{{asset('app-assets/js/scripts/forms/form-login-register.js')}}" ></script>
<!-- END PAGE LEVEL JS-->
<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>


<script type="text/javascript">

    $(document).ready(function (){
        $("input[name='phone']").inputmask({'mask': "9999-9999999", 'clearIncomplete': true});
        $('#city').prepend('<option value="" selected="selected"></option>').select2({
            width: '100%',
            placeholder:'Select City',
        });
        $('#territory').prepend('<option value="" selected="selected"></option>').select2({
            width: '100%',
            placeholder:'Select Territory',
        });
        $('#area').prepend('<option value="" selected="selected"></option>').select2({
            width: '100%',
            placeholder:'Select Area',
        });

        $('#reference').prepend('<option value="" selected="selected"></option>').select2({
            width: '100%',
            placeholder:'Select Reference',
        });

        $('#service').prepend('<option value="" selected="selected"></option>').select2({
            width: '100%',
            placeholder:'Select Service',
        });

        $('#city').on('change',function () {
            city_id = $(this).val();
            if(city_id){
                $.ajax({
                    url: '{!! route('cod.territory') !!}',
                    method: 'POST',
                    data: {
                        'id': city_id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    $('#territory').empty();
                    $('#area').empty();
                    if (data.status == 0) {

                        $.each(data.territory, function (key, value) {
                            var newOption = "<option value="+ value.id +">" + value.name + "</option>";
                            $('#territory').append(newOption);
                        });
                        $('#territory').val('').trigger('change');

                    } else {
                        $('#territory').empty();
                        var error = 'No Territory found for the selected city';
                        toastr.error(error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                });
            }
        });

        $('#territory').on('change',function () {
            territory_id = $(this).val();
            if(territory_id){
                $.ajax({
                    url: '{!! route('cod.area') !!}',
                    method: 'POST',
                    data: {
                        'id': territory_id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {

                    if (data.status == 0) {
                        $('#area').empty();
                        $.each(data.area, function (key, value) {
                            var newOption = "<option value="+ value.id +">" + value.name + "</option>";
                            $('#area').append(newOption);
                        });
                        $('#area').val('').trigger('change');

                    } else {
                        $('#area').empty();
                        var error = 'No Area found for the selected territory';
                        toastr.error(error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                });
            }
        });

        $('#service').change(function () {

            if($('#service').val() == 4 || $('#service').val() == 5){
                $('#company_name').val('');
                $('#brand_name').val('');
                $('#brand_div').removeClass('d-none');
            }
            else {
                $('#brand_div').addClass('d-none');

            }
        });

        $( "#get_started_form" ).validate({
            errorClass: 'danger',
            successClass: 'success',
            errorPlacement: function (error, element) {
                error.addClass('w-100').appendTo(element.parents('.form-group'));
            },
            submitHandler: function (form){

                form.submit();

            }
        });
    });


</script>
</body>
</html>