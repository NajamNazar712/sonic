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
    <title>Register - Sonic | Trax</title>
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('img/apple-touch-icon_new.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon_new-32x32.png') }} ">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon_new-16x16.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{  asset('img/favicon_new.ico') }}">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Quicksand:300,400,500,700"
          rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/line-awesome/css/line-awesome.min.css')}}">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/vendors.css')}}">
    {{--date picker--}}
    {{--<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/daterange/daterangepicker.css')}}">--}}
    {{--<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">--}}
    {{--date picker--}}
    {{--<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">--}}
    {{--<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/custom.css')}}">--}}
    <!-- END VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
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
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/login.css')}}?v=2.2">
    <style type="text/css">
        #generation_date_root .picker__holder { bottom: 0; margin-bottom: 42px;}
    </style>
</head>
<body class="vertical-layout vertical-overlay-menu 1-column  bg-full-screen-image menu-expanded"
      data-open="click" data-menu="vertical-overlay-menu" data-col="1-column">


<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <section class="flexbox-container" style="overflow: auto;">
                <div class="col-12 d-flex align-items-center justify-content-center">
                    <div class="col-md-8 col-10 box-shadow-2 p-0">
                        <div class="card border-grey border-lighten-3 m-0">
                            <div class="card-header border-0 pb-0">
                                <div class="card-title text-center">
                                    <div class="row align-items-center">
                                        <div class="col sonic_logo align-middle text-left">
                                            <img src="{{asset('img/sonic_logo_new.png')}}" alt="Sonic" class="d-inline-block mx-auto w-50">
                                        </div>

                                        <div class="col trax_logo align-middle text-right">
                                            <img src="{{asset('img/trax_logo_new.png')}}" alt="Trax" class="d-inline-block mx-auto w-50">
                                        </div>
                                    </div>
                                </div>
                                <h6 class="card-subtitle line-on-side text-muted text-center font-small-3 pt-2">
                                    <span>Please Sign Up</span>
                                </h6>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form id="registership" action="{{route('cod.register.submit')}}" method="post" class="steps-validation wizard-circle" enctype="multipart/form-data">
                                        <!-- Step 1 -->
                                        @csrf
                                        @method('post')
                                        @if($lead != null)
                                            <input type="hidden" name="lead_id" value="{{$lead->id}}">
                                        @endif
                                        <h6>Profile Information</h6>
                                        @include('client.inc.messages')
                                        <fieldset>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="name">
                                                            Company Name:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control required" value="{{ old('name') }}" name="name">
                                                        <span name="cname" class="danger" for="name" style="display: none;">Atleast 3 Characters Required</span>
                                                        <span name="ename" class="danger" for="name" style="display: none;">Company Name Already Exists</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="shipper_poc">
                                                            Person of Contact:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control required" placeholder="Person Name (Alphabet Only)" name="shipper_poc" value="@if(old('shipper_poc') != null){{old('shipper_poc')}}@elseif($lead != null){{$lead->contact_person}}@else{{old('shipper_poc')}}@endif">
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
                                                        <label for="phone">Phone Number 1:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control required" placeholder="0345-9999999 / 0213-9999999" name="phone" value="@if(old('phone') != null){{old('phone')}}@elseif($lead != null){{$lead->phone_number}}@else{{old('phone')}}@endif">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="cnic">CNIC Number:
                                                            <span class="danger">*</span></label>
                                                        <input type="text" class="form-control required" placeholder="XXXXX-1234567-X" value="{{ old('cnic') }}"  name="cnic">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="phone2">Phone Number 2:</label>
                                                        <input type="text" class="form-control" placeholder="0345-9999999 / 0213-9999999"  value="{{ old('phone2') }}" name="phone2">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                       <label for="ntn_no">NTN Number:</label>
                                                        <input type="text" class="form-control" placeholder="(e.g: 1234567-8)" value="{{ old('ntn_no') }}"  name="ntn_no">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                       <label for="strn_no">STRN Number:</label>
                                                        <input type="text" class="form-control" placeholder="(e.g: 1234567891234)" value="{{ old('strn_no') }}"  name="strn_no">
                                                    </div>
                                                </div>
                                        </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="url">URL:</label>
                                                        <span class="danger">*</span>
                                                        <input type="text" class="form-control required" value="{{ old('url') }}" name="url" placeholder="Webiste / Facebook Page">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">

                                                        <label for="shipper_city">City:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <div>
                                                            <select name="shipper_city" id="shipper_city" class="select2 form-control required" style="width: 100%">
                                                                @foreach($all_cities as $city)
                                                                    <option value="{{$city->id}}" {{ old('shipper_city') == $city->id ? 'selected' : '' }} >{{$city->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="nature_of_account">Nature Of Account:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <div>
                                                            <select name="nature_of_account" id="nature_of_account" class="select2 form-control required">
                                                                @foreach($account_types as $type)
                                                                    <option value="{{$type->id}}" {{ old('nature_of_account') == $type->id ? 'selected' : '' }}>{{$type->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">

                                                        <label for="shipper_product_type">Product Type:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <div>
                                                            <select name="shipper_product_type" id="shipper_product_type" class="select2 form-control required" style="width: 100%">
                                                                @foreach($products as $product)
                                                                    <option value="{{$product->id}}" {{ old('shipper_product_type') == $product->id ? 'selected' : '' }} >{{$product->product_name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12 d-none" id="product_name_div">
                                                            <div class="form-group">
                                                                <label for="product_name">Product Name:
                                                                    <span class="danger">*</span>
                                                                </label>
                                                                <div>
                                                                    <input type="text" class="form-control" value="{{ old('product_name') }}" name="product_name" placeholder="Product Name">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="average_shipment">Expected Average Shipments:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <div>
                                                            <input type="text" class="form-control required" value="{{ old('average_shipment') }}" name="average_shipment" placeholder="Expected Average Shipments">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="average_shipment_duration">Expected Average Shipment Duration:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <div>
                                                            <select name="average_shipment_duration" id="average_shipment_duration" class="select2 form-control required" style="width: 100%">
                                                                @foreach($average_shipment_durations as $average_shipment_duration)
                                                                    <option value="{{$average_shipment_duration->id}}" {{ old('average_shipment_duration') == $average_shipment_duration->id ? 'selected' : '' }} >{{$average_shipment_duration->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="reference">Reference:</label>
                                                        <span class="danger">*</span>
                                                        <div>
                                                            <select name="reference" id="reference" class="select2 form-control required" style="width: 100%">
                                                                @foreach($references as $reference)
                                                                    <option value="{{$reference->id}}" {{ old('reference') == $reference->id ? 'selected' : '' }} >{{$reference->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6" id="sale_person_div">
                                                    <div class="form-group">
                                                        <label for="sale_person">Sale Person:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <div>
                                                            <select name="sale_person" id="sale_person" class="select2 form-control required" style="width: 100%"></select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="brand_name">Brand Name:
                                                        </label>
                                                        <div>
                                                            <input type="text" class="form-control" value="{{ old('brand_name') }}" name="brand_name" placeholder="Brand Name">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="segments">Segments:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <div>
                                                            <select name="segments" id="segments" class="select2 form-control required" style="width: 100%">
                                                                @foreach($segments as $segment)
                                                                    <option value="{{$segment->id}}"> {{$segment->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                               
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="sub_segments">Sub Segments:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <div>
                                                            <select name="sub_segments" id="sub_segments" class="select2 form-control required" style="width: 100%">
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <!-- Step 2 -->
                                        <h6>Shipping Information</h6>
                                        <fieldset>
                                            <div class="row position-relative vertical-scroll" id="shipInfo" style="height: 385px;overflow: auto;">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="pickup_address">
                                                            Pickup Address:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <input type="text" id="pickup_address" class="form-control required" value="{{ old('pickup_address.0') }}"  name="pickup_address[]" placeholder="Pickup Address">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="shipping_phone">
                                                            Phone Number:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <input type="text" id="pickup_phone" class="form-control required" placeholder="0345-9999999" name="shipping_phone[]" value="{{ old('shipping_phone.0') }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="shipping_poc">
                                                            Person of Contact:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <input type="text" id="pickup_poc" class="form-control required" placeholder="Person Name" value="{{ old('shipping_poc.0') }}"  name="shipping_poc[]">
                                                    </div>
                                                    <div class="form-group">

                                                        <label for="url">Product Type:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <div>
                                                            <select name="product_type[]" id="product_select" class="select2 form-control required" style="width: 100%">
                                                                @foreach($products as $product)
                                                                    <option value="{{$product->id}}" {{ (collect(old('product_type'))->contains($product->id)) ? 'selected' : '' }} >{{$product->product_name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="shipping_phone">
                                                            Brand Name:
                                                        </label>
                                                        <input type="text" id="pickup_brand_name" class="form-control" placeholder="Brand Name" name="pickup_brand_name[]" value="{{ old('pickup_brand_name.0') }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="shipping_phone">
                                                            Email Address:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <input type="email" name="shipping_email[]" placeholder="abc@example.com" value="{{ old('shipping_email.0') }}" class="form-control required">
                                                    </div>
                                                    <div class="form-group">

                                                        <label for="shipping_city">Shipper City:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <div>
                                                            <select name="shipping_city[]" id="shipping_city" class="select2 form-control required" style="width: 100%">
                                                                @foreach($pickup_city_list as $pickup_city)
                                                                    <option value="{{$pickup_city->id}}" {{ (collect(old('shipping_city'))->contains($pickup_city->id)) ? 'selected' : '' }} >{{$pickup_city->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{--Add more addresses--}}
                                                {{--Accordion--}}
                                                <div class="col-12" id="newAddress">

                                            <!--To find if the form was submitted showing validation error
                                                 it will not work by defualt -->
                                        @if (old('pickup_address'))
                                            @php ($i = 1)
                                        @else
                                            @php ($i = 0)
                                        @endif
                                        @while (old('pickup_address.'.$i) != null)

                                        <div class="card naddress" id="shipping_{{$i}}">
                                            <div class="card-header">
                                                <h4 class="card-title">New Address</h4>
                                                <div class="heading-elements">
                                                    <ul class="list-inline mb-0">
                                                        <li><a data-action="close"><i class="ft-x"></i></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="card-content">
                                                <div class="">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="pickup_address">
                                                                    Pickup Address:
                                                                    <span class="danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control required" value="{{ old('pickup_address.'.$i) }}" name="pickup_address[]" placeholder="Pickup Address">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="shipping_poc">
                                                                    Person of Contact:
                                                                    <span class="danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control required" value="{{ old('shipping_poc.'.$i) }}"  name="shipping_poc[]">
                                                            </div>
                                                            <div class="form-group">

                                                                <label for="url">Product Type:
                                                                    <span class="danger">*</span>
                                                                </label>
                                                                <div>
                                                                    <select name="product_type[]" class="select2 form-control required" style="width: 100%">
                                                                        @foreach($products as $product)
                                                                            <option value="{{$product->id}}" {{ (collect(old('product_type.'.$i))->contains($product->id)) ? 'selected' : '' }} >{{$product->product_name}}</option>
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
                                                                <input type="text" class="form-control required" placeholder="0399-9999999" value="{{ old('shipping_phone.'.$i) }}" name="shipping_phone[]">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="shipping_email">Email Address:
                                                                <span class="danger">*</span>
                                                            </label>
                                                                <input type="email" name="shipping_email[]" class="form-control required" value="{{ old('shipping_email.'.$i) }}" placeholder="abc@example.com">
                                                            </div>
                                                            <div class="form-group">

                                                                <label for="shipping_city">Shipper City:
                                                                    <span class="danger">*</span>
                                                                </label>
                                                                <div>
                                                                    <select name="shipping_city[]" class="select2 form-control required" style="width: 100%">
                                                                        @foreach($pickup_city_list as $city)
                                                                            <option value="{{$city->id}}" {{ (collect(old('shipping_city.'.$i))->contains($city->id)) ? 'selected' : '' }} >{{$city->name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>{{--row end--}}
                                                </div>{{--card body end--}}
                                            </div>
                                        </div>
                                        @php ($i++)
                                        @endwhile
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
                                            <div class="row position-relative vertical-scroll" id="bankInfo" style="height: 385px;overflow: auto;">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="bank_name">
                                                            Bank Name:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        
                                                        <div>
                                                            <select name="bank_name[]" id="bank_name" class="select2 form-control required" style="width: 100%">

                                                                @foreach($banks as $bank)
                                                                    <option value="{{$bank->id}}"  {{ old('bank_name.0') == $bank->id ? 'selected' : '' }} >{{$bank->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="bank_branch">
                                                            Branch Name:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control required" value="{{ old('bank_branch.0') }}" name="bank_branch[]" placeholder="Branch Name*">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="account_name">Account Number:
                                                            <span class="danger">*</span></label>
                                                        <input type="text" class="form-control required" value="{{ old('account_no.0') }}" name="account_no[]" placeholder="Account Number*">
                                                    </div>

                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="account_title">
                                                            Account Title:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <input type='text' class="form-control required" value="{{ old('account_title.0') }}" name="account_title[]" placeholder="Account Title*">

                                                    </div>

                                                    <div class="form-group">
                                                        <label for="iban">
                                                            IBAN Number:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control iban required" placeholder="(e.g: PK37MEZN0001220100004069)" value="{{ old('iban_no.0') }}" name="iban_no[]" data-rule-maxlength="24" data-rule-maxlength-message="Max character length 24">
                                                    </div>

                                                        <div class="form-group">

                                                            <label for="bank_city">Bank City:
                                                                <span class="danger">*</span>
                                                            </label>
                                                            <div>
                                                                <select name="bank_city[]" id="bank_city" class="select2 form-control required" style="width: 100%">
                                                                    @foreach($all_cities as $bank_city)
                                                                       <option value="{{$bank_city->id}}"  {{ old('bank_city.0') == $bank_city->id ? 'selected' : '' }} >{{$bank_city->name}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                </div>
                                                <div class="col-md-12">
                                                    <div id="multiple_banks_section" >
                                                @if (old('bank_name'))
                                                    @php ($b = 1)
                                                @else
                                                    @php ($b = 0)
                                                @endif
                                                @while (old('bank_name.'.$b) != null)
                                                        <div class="card nbank" id="">
                                                            <div class="card-header">
                                                                <h3 class="card-title">New Bank</h3>
                                                                <div class="heading-elements">
                                                                    <ul class="list-inline mb-0">
                                                                        <li><a data-action="close"><i class="ft-x"></i></a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="bank_name">
                                                                                Bank Name:
                                                                                <span class="danger">*</span>
                                                                            </label>
                                                                            
                                                                            <div>
                                                                                <select name="bank_name[]" class="select2 form-control required" style="width: 100%">

                                                                                    @foreach($banks as $bank)
                                                                                        <option value="{{$bank->id}}"  selected="{{ (collect(old('bank_name.'.$b))->contains($bank->id)) ? 'selected':'' }}">{{$bank->name}}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="bank_branch">
                                                                                Branch Name:
                                                                                <span class="danger">*</span>
                                                                            </label>
                                                                            <input type="text" class="form-control required" value="{{ old('bank_branch.'.$b) }}" name="bank_branch[]" placeholder="Branch Name*">
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="account_name">Account Number:
                                                                                <span class="danger">*</span></label>
                                                                            <input type="text" class="form-control required" value="{{ old('account_no.'.$b) }}" name="account_no[]" placeholder="Account Number*">
                                                                        </div>

                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="account_title">
                                                                                Account Title:
                                                                                <span class="danger">*</span>
                                                                            </label>
                                                                            <input type='text' class="form-control required" value="{{ old('account_title.'.$b) }}" name="account_title[]" placeholder="Account Title*">

                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="iban">
                                                                                IBAN Number:
                                                                                <span class="danger">*</span>
                                                                            </label>
                                                                            <input type="text" class="form-control required" placeholder="(e.g: PK37MEZN0001220100004069)" value="{{ old('iban_no.'.$b) }}" name="iban_no[]" data-rule-maxlength="24" data-rule-maxlength-message="Max character length 24">
                                                                        </div>

                                                                            <div class="form-group">

                                                                                <label for="bank_city">Bank City:
                                                                                    <span class="danger">*</span>
                                                                                </label>
                                                                                <div>
                                                                                    <select name="bank_city[]" id="bank_city" class="select2 form-control required" style="width: 100%">
                                                                                        @foreach($all_cities as $bank_city)
                                                                                           <option value="{{$bank_city->id}}"  {{ (collect(old('bank_city.'.$b))->contains($bank_city->id)) ? 'selected' : '' }} >{{$bank_city->name}}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                        </div>
                                                @php ($b++)
                                                @endwhile
                                                
                                                </div>
                                                </div>
                                                    
                                            </div>
                                            
                                            <div class="row" id="more_banks_btn_div">
                                                <div class="col-12">
                                                        <button id="addMoreBanks" type="button" class="btn btn-primary btn-min-width mr-1 mb-1"><i class="la la-plus"></i>&nbsp; Add More Banks</button>
                                                </div>

                                            </div>
                                            <div id="billing_information_div" class="row d-none">
                                                <div class="col-md-6">

                                                    <div class="form-group">

                                                        <label for="cycle_of_invoicing">Cycle Of Invoicing:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <div>
                                                            <select name="cycle_of_invoicing" id="cycle_of_invoicing" class="select2 form-control required">
                                                                @foreach($invoicing_cycle as $cycle)
                                                                    <option value="{{$cycle->id}}"  {{ old('cycle_of_invoicing') == $cycle->id ? 'selected' : '' }} >{{$cycle->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">

                                                <div class="form-group" id="generation_div">

                                                    <label for="generation_date">Generation Date:
                                                        <span class="danger">*</span>
                                                    </label>
                                                    <div>
                                                      {{--  <select name="generation_date" id="generation_date" class="select2 form-control required" style="width: 100%"></select>--}}
                                                        <select name="generation_date" id="generation_date" class="select2 form-control d-none"></select>
                                                    </div>
                                                </div>
                                                </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="bank_branch">
                                                                Billing Person Name:
                                                                <span class="danger">*</span>
                                                            </label>
                                                            <input type="text" class="form-control required" value="{{ old('billing_person_name') }}" name="billing_person_name" placeholder="Billing Person Name*">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="bank_branch">
                                                                Billing Person Phone:
                                                                <span class="danger">*</span>
                                                            </label>
                                                            <input type="text" id="billing_phone" class="form-control required" value="{{ old('billing_person_phone') }}" name="billing_person_phone" placeholder="Billing Person Phone*">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="bank_branch">
                                                                Billing Person Email:
                                                                <span class="danger">*</span>
                                                            </label>
                                                            <input type="email" class="form-control required" value="{{ old('billing_person_email') }}" name="billing_person_email" placeholder="abc@mail.com*">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="bank_branch">
                                                                Billing Address:
                                                                <span class="danger">*</span>
                                                            </label>
                                                            <input type="text" class="form-control required" value="{{ old('billing_address') }}" name="billing_address" placeholder="Billing Address*">
                                                        </div>
                                                    </div>

                                            </div>

                                        </fieldset><!-- Step 4 -->
                                        <h6>Documents Attachment</h6>
                                        <fieldset>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="cnic_front_image">
                                                            Picture of CNIC (Front):
                                                        </label>
                                                        <input class="form-control form-control-sm  required" type="file" name="cnic_front_image" id="cnic_front_image" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="cnic_back_image">
                                                            Picture of CNIC (Back):
                                                        </label>
                                                        <input class="form-control form-control-sm  required" type="file" name="cnic_back_image" id="cnic_back_image" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="blank_cheque_image">
                                                            Picture of Blank cheque:
                                                        </label>
                                                        <input class="form-control form-control-sm  required" type="file" name="blank_cheque_image" id="blank_cheque_image" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">
                                                    </div>
                                                </div>

                                            </div>
                                        </fieldset>
                                        <!-- Step 5 -->
                                        <h6>Login Information</h6>
                                        <fieldset>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="email">
                                                            Email Address:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control required" placeholder="abc@example.com" value="@if(old('email') != null){{old('email')}}@elseif($lead != null){{$lead->email_address}}@else{{old('email')}}@endif"  name="email">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="password">
                                                            Enter Password:
                                                            <span class="danger">*</span>
                                                        </label>
                                                        <div class="form-group position-relative">

                                                        <input type="password" class="form-control required" placeholder="Minimum 6 Character" value="{{ old('password') }}" name="password">
                                                        <div class="form-control-position" id="peye">
                                                            <i class="la la-eye success"></i>
                                                        </div>

                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        {!! app('captcha')->display() !!}
                                                        <!--<button id="reset" class="btn btn-primary btn-min-width mr-1 mb-1">Reset</button>-->
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
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
<!-- END PAGE VENDOR JS-->
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/js/scripts/forms/wizard-steps.js')}}" type="text/javascript"></script>
<!-- BEGIN MODERN JS-->
<script src="{{asset('app-assets/js/core/app-menu.js')}}" ></script>
<script src="{{asset('app-assets/js/core/app.js')}}" ></script>
<script type="text/javascript" src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
<!-- END MODERN JS-->
{{--<script src="{{asset('app-assets/js/scripts/customizer.js')}}" type="text/javascript"></script>--}}
<!-- BEGIN PAGE LEVEL JS-->
<script src="{{asset('app-assets/js/scripts/forms/form-login-register.js')}}" ></script>
<!-- END PAGE LEVEL JS-->
<script>
    //$('.pickadate').pickadate();
    $(document).ready(function () {


       $('#shipper_city').prepend('<option value="" selected="selected"></option>').select2({
           width: '100%',
           placeholder:'Select City',
       }).bind('change', function () {
           var id = $(this).val();
            if(id) {
                $.ajax({
                    url: '{!! route('cod.salesPerson') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if (data.status == 0) {
                        $('#sale_person').empty();

                        $.each(data.sale_persons, function (key, value) {
                            var newOption = "<option value="+ value.id +">" + value.name + "</option>";
                            $('#sale_person').append(newOption);
                        });
                        $('#sale_person').val('').trigger('change');

                        @if($lead != null)
                            @if($lead->sale_person_id != null)
                                $('#sale_person').val({{$lead->sale_person_id}}).trigger('change');
                            @endif
                        @endif
                    } else {
                        toastr.error(data.error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                });
                
            }



            });


        @if($lead != null)
            $('#shipper_city').val({{$lead->city_id}}).trigger('change');
        @endif

        $('#generation_date').prepend('<option value="" selected="selected"></option>').select2({
            width:'100%',
            placeholder:'Select Date',
            // dropdownParent:$('#registership')
        });

       //multiple banks
       $('select[name="bank_name[]"]').prepend('<option value="" selected="selected"></option>').select2({
           placeholder:'Select Bank',
       });


       $('#bank_name').prepend('<option value="" selected="selected"></option>').select2({
           placeholder:'Select Bank',
        
       });
       $('#nature_of_account').prepend('<option value="" selected="selected"></option>').select2({
           width:'100%',
           placeholder:'Select Nature of Account',
        
       }).bind('change', function() {

           if (this.value == 2) {
               $('#billing_information_div').removeClass('d-none');
               $('#more_banks_btn_div').addClass('d-none');
           }
           else {

               $('#more_banks_btn_div').removeClass('d-none');
               $('#billing_information_div').addClass('d-none');
           }
       });
        
       $('#bank_city').prepend('<option value="" selected="selected"></option>').select2({
           placeholder:'Select Bank City',
        
       });
     
       $('select[name="bank_city[]"]').prepend('<option value="" selected="selected"></option>').select2({
           placeholder:'Select Bank City',
        
       });

       $('select[name="shipping_city[]"]').prepend('<option value="" selected="selected"></option>').select2({
           placeholder:'Select Pickup City',
        
       });
        $('select[name="product_type[]"]').prepend('<option value="" selected="selected"></option>').select2({
            placeholder:'Select Product Type',
            // dropdownParent:$('#registership')
        });
        $('select[name="shipper_product_type"]').prepend('<option value="" selected="selected"></option>').select2({
            placeholder:'Select Product Type',
            // dropdownParent:$('#registership')
        }).bind('select2:select', function(){
            var product_type_id = $(this).val();
            if(product_type_id == 24){
                $('#product_name_div').removeClass('d-none');
                $('#product_name').addClass('required');
            }else{
                $('#product_name_div').addClass('d-none');
                $('#product_name').removeClass('required');

            }
        });
        $('select[name="average_shipment_duration"]').prepend('<option value="" selected="selected"></option>').select2({
            placeholder:'Select Duration',
            // dropdownParent:$('#registership')
        });
        $('select[name="reference"]').prepend('<option value="" selected="selected"></option>').select2({
            placeholder:'Select Reference',
            // dropdownParent:$('#registership')
        });
        $('select[name="sale_person"]').prepend('<option value="" selected="selected"></option>').select2({
            placeholder:'Select Sale Person',
            // dropdownParent:$('#registership')
        });
        $('select[name="sub_segments"]').prepend('<option value="" selected="selected"></option>').select2({
            placeholder:'Select Sub Segments',
        });
        $('select[name="segments"]').prepend('<option value="" selected="selected"></option>').select2({
            placeholder:'Select Segment',
        }).bind('change', function() {
                var id = $(this).val();
                $(this).valid();
                $.ajax({
                    url: '{!! route('cod.get_sub_segment') !!}',
                    method: 'POST',
                    data: {
                        'segment_id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                   if (data.status == 0) {
                       $('#sub_segments').children().remove();
                                    $('#sub_segments').prepend('<option value="" selected="selected"></option>')
                                $.each(data.sub_segments, function (index, sub_segments) {
                                    $('#sub_segments').append('<option value="'+sub_segments.id+'" id="trax_center">'+sub_segments.name+'</option>')
                                });
                   }
                });
            });
        
        
        
        $("input[name='average_shipment']").inputmask({
            'alias': 'integer',
            'allowMinus': false,
            'allowPlus': false
        });
        $('#peye').on('mousedown',function(){$('input[name="password"]').attr('type','text')}).on('mouseup',function(){$('input[name="password"]').attr('type','password')});
        $("input[name='cnic']").inputmask({'mask': "99999-9999999-9", 'clearIncomplete': true});
        $("input[name='phone'],input[name='phone2'],input[name='billing_person_phone'],input[name='shipping_phone[]']").inputmask({'mask': "9999-9999999", 'clearIncomplete': true});
        $("input[name='ntn_no']").inputmask({'mask': "9999999-9", 'clearIncomplete': true});

        $('#shipInfo').perfectScrollbar({
            suppressScrollX : true,
            theme: 'dark',
            wheelPropagation: true
        });
        $('#shipInfo').perfectScrollbar('update');
        $('#bankInfo').perfectScrollbar({
            suppressScrollX : true,
            theme: 'dark',
            wheelPropagation: true
        });
        $('#bankInfo').perfectScrollbar('update');

        var banks_count = parseInt('{{$b}}');
        $('body').on('click', '#addMoreBanks',function(){
            
            $.get('new/bank', function(bView){
                $('#multiple_banks_section').append(bView);
            }).done(function(){
                var bcc = $('.card.nbank').length;
                var bid = $('.card.nbank').eq(bcc-1);
                var banking_div = banks_count + 1;
                bid.attr('id','banking_'+banking_div);
                $('#banking_'+banking_div+' h3.card-title' ).text('Bank '+banking_div);
                var innerdivcount = banks_count + 1;
                var temp_bank_name = $('#banking_'+banking_div+' select[name="temp_bank_name"]');
                temp_bank_name.attr('name','bank_name['+innerdivcount+']');
                var temp_bank_city = $('#banking_'+banking_div+' select[name="temp_bank_city"]');
                temp_bank_city.attr('name','bank_city['+innerdivcount+']');
                var temp_bank_branch = $('#banking_'+banking_div+' input[name="temp_bank_branch"]');
                temp_bank_branch.attr('name','bank_branch['+innerdivcount+']');
                var temp_account_no = $('#banking_'+banking_div+' input[name="temp_account_no"]');
                temp_account_no.attr('name','account_no['+innerdivcount+']');
                var temp_account_title = $('#banking_'+banking_div+' input[name="temp_account_title"]');
                temp_account_title.attr('name','account_title['+innerdivcount+']');
                var temp_iban_no = $('#banking_'+banking_div+' input[name="temp_iban_no"]');
                temp_iban_no.attr('name','iban_no['+innerdivcount+']');
                $('#bankInfo').stop().animate({
                  scrollTop: $('#bankInfo')[0].scrollHeight
                }, 2000);
                $('#banking_' + banking_div + ' .select2').select2({
                });


                $('#banking_' + banking_div + ' a[data-action="close"]').on('click',function(){
                    $(this).closest('.card').remove();
                    $('#bankInfo').perfectScrollbar('update');
                });
                banks_count++;
            });
        });


        var count = parseInt('{{$i}}');
        $('body').on('click','#addMoreAddress',function () {
            $.get( 'new/address', function( data ) {
                $('#newAddress').append(data);

            }).done(function() {
                var cc = $('.card.naddress').length;
                var nid = $('.card.naddress').eq(cc-1);
                count++;
                nid.attr('id','shipping_'+count);
                $('#shipping_'+count+' h3.card-title' ).text('Address '+count);
                //becasuse count is starting from 0 and 0 index is there by default for following values
                var innerdivcount = count + 1;
                var temp_pickupaddress = $('#shipping_'+count+' input[name="temp_pickupaddress"]');
                temp_pickupaddress.attr('name','pickup_address['+innerdivcount+']');
                var temp_shipping_poc = $('#shipping_'+count+' input[name="temp_shipping_poc"]');
                temp_shipping_poc.attr('name','shipping_poc['+innerdivcount+']');
                var temp_product_type = $('#shipping_'+count+' select[name="temp_product_type"]');
                temp_product_type.attr('name','product_type['+innerdivcount+']');
                var temp_shipping_phone = $('#shipping_'+count+' input[name="temp_shipping_phone"]');
                temp_shipping_phone.attr('name','shipping_phone['+innerdivcount+']');
                var temp_pickup_brand_name = $('#shipping_'+count+' input[name="temp_pickup_brand_name"]');
                temp_pickup_brand_name.attr('name','pickup_brand_name['+innerdivcount+']');
                var temp_shipping_email = $('#shipping_'+count+' input[name="temp_shipping_email"]');
                temp_shipping_email.attr('name','shipping_email['+innerdivcount+']');
                var temp_shipping_city = $('#shipping_'+count+' select[name="temp_shipping_city"]');
                temp_shipping_city.attr('name','shipping_city['+innerdivcount+']');

                $('#shipInfo').stop().animate({
                  scrollTop: $('#shipInfo')[0].scrollHeight
                }, 2000);
                $("input[name='shipping_phone[]']").inputmask({'mask': "9999-9999999", 'clearIncomplete': true});

                $('#shipping_' + count + ' .select2').select2({
                    // dropdownParent:$('#registership')
                });
                $('#shipping_' + count + ' a[data-action="close"]').on('click',function(){
                  //  $(this).closest('.card').removeClass().slideUp('fast'); // comenting this because display none will allow values to be posted
                    $(this).closest('.card').remove();
                    $('#shipInfo').perfectScrollbar('update');

                });
                

            });


        });
        $('body').on('change','input[name="name"]',function () {
            var name = $(this).val();
            var error = 0;
            var err0 = $('span[name=cname]');
            var err =  $('span[name=ename]');
            if(name.length < 3){
                err0.css('display','block');
                error = 1;
            }else{
                error = 0;
                err0.css('display','none');

            }
            if(error == 0){
                $.ajax({
                    url: "name/match/{name}",
                    type:'GET',
                    data: {name:name},
                    success: function(data) {
                        if(data.status == 0){
                            err.css('display','block');

                        }else if(data.status == 1){
                            err.css('display','none');
                        }

                    }
                });
            }

        });

        $('#cycle_of_invoicing').prepend('<option value="" selected="selected"></option>').select2({
            width:'100%',
            placeholder:'Select Cycle Of Invoicing',

        }).bind('change', function() {

            if (this.value == 1) {
                var weekly = [1, 2, 3, 4, 5, 6, 7];
                $('#generation_div').removeClass('d-none');
                $('#generation_date').removeClass('d-none');
                $('#generation_date').addClass('required');
                $('#generation_date').empty().trigger('change');
                $('#generation_date').select2({data:weekly,placeholder:'Select Date'});
            }
            else if(this.value == 3){
                var monthly = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28];
                $('#generation_div').removeClass('d-none');
                $('#generation_date').removeClass('d-none');
                $('#generation_date').addClass('required');
                $('#generation_date').empty().trigger('change');
                $('#generation_date').select2({data:monthly,placeholder:'Select Date'});
            }
            else if(this.value == 2 || this.value == 4){
                $('#generation_div').addClass('d-none');
                $('#generation_date').addClass('d-none');
                $('#generation_date').removeClass('required');
            }

        });


        // $('#shipper_city').on('change',function () {
        //     }).done(function (data) {
        //         if(data.status == 0){
        //             $('#sale_person').empty();
        //
        //             $.each(data.sizes,function (key,value) {
        //                 var type_size = parseInt(type_id+value.id);
        //
        //                 var index = $.inArray(type_size, already_selected_size);
        //
        //                 if(index === -1){
        //         }
        //
        //
        // });

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

    // var reset = document.querySelector('#reset');
    // if (reset) {
    //     reset.addEventListener('click', () => {
    //       grecaptcha.reset()
    //     });
    // }
    //         }

</script>
</body>
</html>