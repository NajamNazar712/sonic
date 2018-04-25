<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Trax Logistics, Sonic Project">
    <meta name="keywords" content="Trax Logistics">
    <meta name="author" content="Waqas">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sonic Login</title>
    <link rel="apple-touch-icon" href="{{asset('app-assets/images/ico/apple-icon-120.png')}}">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('app-assets/images/ico/favicon.ico')}}">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Quicksand:300,400,500,700"
          rel="stylesheet">
    <link href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css"
          rel="stylesheet">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/vendors.css')}}">
    {{--date picker--}}
    {{--<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/daterange/daterangepicker.css')}}">--}}
    {{--<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">--}}
    {{--date picker--}}
    {{--<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">--}}
    {{--<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/custom.css')}}">--}}
    <!-- END VENDOR CSS-->
    <!-- BEGIN MODERN CSS-->

    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/app.css')}}">
    <!-- END MODERN CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/menu/menu-types/vertical-overlay-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/colors/palette-gradient.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/wizard.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/pages/login-register.css')}}">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/style.css')}}">
    <!-- END Custom CSS-->
    <style>
        .hide{
            display: none;
        }
    </style>
</head>
<body class="vertical-layout vertical-overlay-menu 1-column  bg-full-screen-image menu-expanded fixed-navbar"
      data-open="click" data-menu="vertical-overlay-menu" data-col="1-column">


<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <section class="flexbox-container">
                <div class="col-12 d-flex align-items-center justify-content-center">
                    <div class="col-md-8 col-10 box-shadow-2 p-0">
                        <div class="card border-grey border-lighten-3 m-0">
                            <div class="card-header border-0 pb-0">
                                <div class="card-title text-center">
                                    <img src="{{asset('app-assets/images/logo/logo-dark.png')}}" alt="branding logo">
                                </div>
                                <h6 class="card-subtitle line-on-side text-muted text-center font-small-3 pt-2">
                                    <span>Please Sign Up</span>
                                </h6>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form id="registership" action="{{route('cod.register.submit')}}" method="post" class="steps-validation wizard-circle">
                                        <!-- Step 1 -->
                                        @csrf
                                        @method('post')
                                        <h6>Personal Information</h6>
                                        @include('client.inc.messages')
                                        <fieldset>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="company_name">
                                                            Company Name:
                                                            <span class="danger">*</span>
                                                        </label>

                                                        <input type="text" class="form-control required" value="{{ old('name') }}"  name="name" minlength="3">
                                                        
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="shipper_poc">
                                                            Person of Contact:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control required" placeholder="Person Name" name="shipper_poc" value="{{old('shipper_poc')}}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="company_address">Company Address:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control required"  name="company_address" value="{{ old('company_address') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="shipper_phone">Phone Number 1:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <input type="tel" class="form-control required" placeholder="0345-9999999" name="shipper_phone" value="{{ old('shipper_phone') }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="cnic">CNIC:
                                                            <span class="danger">*</span></label>
                                                        <input type="text" class="form-control required" placeholder="XXXXX-1234567-X" value="{{ old('cnic') }}"  name="cnic">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="shipper_phone2">Phone Number 2:</label>
                                                        <input type="tel" class="form-control" placeholder="0345-9999999"  value="{{ old('shipper_phone2') }}" name="shipper_phone2">                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" placeholder="(e.g: 1234567-8)" value="{{ old('ntn_no') }}"  name="ntn_no">                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" value="{{ old('url') }}" name="url" placeholder="URL/Facebook Page">                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">

                                                        <label for="shipper_city">City:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <div>
                                                            <select name="shipper_city" id="shipper_city" class="select2 form-control required" style="width: 100%">
                                                                <option value="{{ old('shipper_city') }}" selected>Select City</option>
                                                                @foreach($all_cities as $city)
                                                                    <option value="{{$city->city_code}}">{{$city->city_name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <!-- Step 2 -->
                                        <h6>Shipping Information</h6>
                                        <fieldset>
                                            <div class="row vertical-scroll" id="shipInfo" style="max-height: 350px;overflow: scroll;">

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="pickup_address">
                                                            Pickup Address:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control required" value="{{ old('pickup_address[]') }}"  name="pickup_address[]">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="shipping_poc">
                                                            Person of Contact:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control required" value="{{ old('shipping_poc[]') }}"  name="shipping_poc[]">                                                    </div>
                                                    <div class="form-group">

                                                        <label for="url">Product Type:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <div>
                                                            <select name="product_type[]" id="product_select" class="select2 form-control required" style="width: 100%">
                                                                <option value="{{ old('product_type[]') }}" selected>Select Product Type</option>
                                                                @foreach($products as $product)
                                                                    <option value="{{$product->id}}">{{$product->product_name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="shipping_phone">
                                                            Phone Number:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <input type="tel" class="form-control required" placeholder="(0345) 999-9999" name="shipping_phone[]" value="{{ old('shipping_phone[]') }}">                                                    </div>
                                                    <div class="form-group">
                                                        <label for="shipping_phone">
                                                            Email:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <input type="email" name="shipping_email[]" value="{{ old('shipping_email[]') }}" class="form-control required">                                                    </div>
                                                    <div class="form-group">

                                                        <label for="shipping_city">Shipper City:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <div>
                                                            <select name="shipping_city[]" id="shipping_city" class="select2 form-control required" style="width: 100%">
                                                                <option value="{{ old('shipping_city[]') }}" selected>Select Shipper City</option>
                                                                @foreach($cities as $city)
                                                                    <option value="{{$city->city_code}}">{{$city->city_name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                </div>

                                                {{--Add more addresses--}}
                                                {{--Accordion--}}
                                                <div class="col-12" id="newAddress">

                                                </div>{{--column end--}}
                                                {{--Accordion--}}
                                                <div class="col-12">

                                                    <button id="addMoreAddress" type="button" class="btn btn-primary btn-min-width mr-1 mb-1"><i class="la la-plus"></i>&nbsp Add Pickup Locations</button>

                                                </div>

                                            </div>


                                        </fieldset>
                                        <!-- Step 3 -->
                                        <h6>Bank Information</h6>
                                        <fieldset>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="bank_name">
                                                            Bank Name:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control required"   name="bank_name">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="bank_branch">
                                                            Bank Branch:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control required" name="bank_branch">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="account_name">Account Number:
                                                            <span class="danger">*</span></label>
                                                        <input type="text" class="form-control required" name="account_no">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="mode_of_payment">Mode of Payment:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <div>
                                                        <select name="mode_of_payment" class="select2 form-control required" style="width: 100%;">
                                                            <option value="" selected="" disabled="">Select Mode of Payment</option>
                                                            <option value="ibft">IBFT Reimbursements</option>
                                                            <option value="invoices">Invoices</option>
                                                        </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="account_title">
                                                            Account Title:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <input type='text' class="form-control required" name="account_title">

                                                    </div>

                                                    <div class="form-group">
                                                        <label for="iban">
                                                            IBAN Number:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control required" placeholder="(e.g: PK-37-MEZN-0001-2201-0000-4069)" name="iban_no">
                                                    </div>

                                                        <div class="form-group">

                                                            <label for="bank_city">Bank City:
                                                                <span class="danger">*</span>
                                                            </label>
                                                            <div>
                                                                <select name="bank_city" id="bank_city" class="select2 form-control required" style="width: 100%">
                                                                    <option value="" selected>Select Bank City</option>
                                                                    @foreach($all_cities as $city)
                                                                        <option value="{{$city->city_code}}">{{$city->city_name}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    <div class="form-group">

                                                            <label for="cycle_of_payment">Cycle of Payment:
                                                                <span class="danger">*</span>
                                                            </label>
                                                        <div>
                                                        <select name="cycle_of_payment" class="select2 form-control required" style="width: 100%;">
                                                            <option value="" selected="" disabled="">Select Cycle of Payment</option>
                                                            <option value="daily">Daily</option>
                                                            <option value="weekly">Weekly</option>
                                                            <option value="fortnight">Fortnight</option>
                                                            <option value="monthly">Monthly</option>
                                                        </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                </div>

                                        </fieldset>
                                        <!-- Step 4 -->
                                        <h6>Login Information</h6>
                                        <fieldset>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="email">
                                                            Email Address:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control required" placeholder="abc@example.com"  name="email">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="password">
                                                            Password:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <div class="form-group position-relative">

                                                        <input type="password" class="form-control required" placeholder="Minimum 6 Character" name="password">
                                                        <div class="form-control-position" id="peye">
                                                            <i class="la la-eye success"></i>
                                                        </div>

                                                        </div>
                                                    </div>
                                                    {{--<div class="form-group">--}}
                                                        {{--<label for="password-confirm">--}}
                                                           {{--Confirm Password :--}}
                                                            {{--<span class="danger">*</span>--}}
                                                        {{--</label>--}}
                                                        {{--<input type="password" class="form-control required"  name="password-confirm">--}}
                                                        {{--<span id="perror" class="danger" style="display: none;">* Password doesn't match</span>--}}
                                                    {{--</div>--}}
                                                </div>

                                            </div>
                                        </fieldset>
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
<!-- ////////////////////////////////////////////////////////////////////////////-->
<!-- BEGIN VENDOR JS-->
<script src="{{asset('app-assets/vendors/js/vendors.min.js')}}" ></script>
<!-- BEGIN VENDOR JS-->
<script src="{{asset('app-assets/vendors/js/extensions/jquery.steps.min.js')}}" type="text/javascript"></script>
<!-- BEGIN PAGE VENDOR JS-->
<script src="{{asset('app-assets/vendors/js/forms/validation/jqBootstrapValidation.js')}}"
></script>
<script src="{{asset('app-assets/vendors/js/pickers/dateTime/moment-with-locales.min.js')}}"
        type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/pickers/daterange/daterangepicker.js')}}"
        type="text/javascript"></script>
{{--<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>--}}
{{--<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>--}}
<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" ></script>
<!-- END PAGE VENDOR JS-->
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/js/scripts/forms/wizard-steps.js')}}" type="text/javascript"></script>
<!-- BEGIN MODERN JS-->
<script src="{{asset('app-assets/js/core/app-menu.js')}}" ></script>
<script src="{{asset('app-assets/js/core/app.js')}}" ></script>
<!-- END MODERN JS-->
{{--<script src="{{asset('app-assets/js/scripts/customizer.js')}}" type="text/javascript"></script>--}}
<!-- BEGIN PAGE LEVEL JS-->
<script src="{{asset('app-assets/js/scripts/forms/form-login-register.js')}}" ></script>
<!-- END PAGE LEVEL JS-->
<script>
    //$('.pickadate').pickadate();
    $(document).ready(function () {

       $('.select2').select2({
           dropdownParent:$('#registership')
       });
        $('#product_select').select2({
            dropdownParent:$('#registership')
        });
        $('#peye').on('mousedown',function(){$('input[name="password"]').attr('type','text')}).on('mouseup',function(){$('input[name="password"]').attr('type','password')})
        $("input[name='cnic']").inputmask({'mask': "99999-9999999-9", 'clearIncomplete': true});
        $("input[name='shipper_phone'],input[name='shipper_phone2'],input[name='shipping_phone']").inputmask({'mask': "9999-9999999", 'clearIncomplete': true});
        $("input[name='ntn_no']").inputmask({'mask': "9999999-9", 'clearIncomplete': true});
        // Vertical Scroll
        $('.vertical-scroll').perfectScrollbar({
            suppressScrollX : true,
            theme: 'dark',
            wheelPropagation: true
        });
        var count = 1;
        $('body').on('click','#addMoreAddress',function () {
            $.get( 'new/address', function( data ) {
                $('#newAddress').append(data);

            }).done(function() {
                //var cc = $('.card.naddress').length;
                var nid = $('.card.naddress').eq(count);
                nid.attr('id','shipping_'+count);
                $('#shipping_'+count+' h3.card-title' ).text('Address '+count);
                $('#shipping_' + count + ' .select2').select2({
                    dropdownParent:$('#registership')
                });
                $('#shipping_' + count + ' a[data-action="close"]').on('click',function(){
                    $(this).closest('.card').removeClass().slideUp('fast');
                });
                //const container = document.querySelector('#shipping_'+count);
                //container.scrollTop = 0;
                //form validatiion
                // Initialize validation
                $(function () { $("input,select,textarea").not("[type=submit]").jqBootstrapValidation(); } );
                //
                count++;

            });


        });
        $('body').on('change','input[name="name"]',function () {
            var name = $(this).val();
            var error = 0;
            var err0 = '<span name="cname" class="danger" for="name">Atleast 3 characters required.</span>';
            var err = '<span name="cname" class="danger" for="name">company name already exists, select another name.</span>';
            if(name.length < 3){
                $(err0).insertAfter('input[name="name"]');
                error = 1;
            }else{
                error = 0;
                $('span[name="cname"]').css('display','none');
            }
            if(error == 0){
                $.ajax({
                    url: "name/match/{name}",
                    type:'GET',
                    data: {name:name},
                    success: function(data) {
                        if(data.status == 0){
                            $(err).insertAfter('input[name="name"]');
                            console.log(data.status);
                        }else if(data.status == 1){
                            $('span[name="cname"]').css('display','none');
                            console.log(data.status);

                        }
                        console.log(data.status);
                    }
                });
            }


        });

        // $('a[href="#next"]').on('click',function(e){
        //     // $("#registership").validate().element("");
        // });
        // $( 'a[href="#next"]' ).dblclick(function() {
        //     alert( "Handler for .dblclick() called." );
        // });

        // $('body').on('dblclick', 'a[href="#next"]', function(e) {
        //     e.preventDefault();
        //     // alert('You skipped one step');
        //     //$('a[href="#previous"]').trigger('click');
        // });

    });
</script>
</body>
</html>