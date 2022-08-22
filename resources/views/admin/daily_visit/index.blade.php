@extends('admin.layout.master')
@section('title','Daily Visit Form')

@section('content')
    <h1 class="mb-1 mt-1">
        Daily Visit Form
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <form id="daily_visit_form" class="form-horizontal" method="post" action="{{route('admin.daily_visit.store')}}" enctype="multipart/form-data">
                    @csrf

                    <div class="col justify-content-center">
                        <input type="hidden" name="daily_visit_id" id="daily_visit_id">
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">
                        <div class="col form-group">
                            <select name="lead_status" class="select2" id="lead_status" data-rule-required="true" data-msg-required="Lead Status is required">
                                @foreach($lead_statuses as $lead_status)
                                    <option value="{{ $lead_status->id }}">{{ $lead_status->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col form-group">
                            <select name="shipper" class="select2" id="shipper" data-rule-required="true" data-msg-required="Shipper is required">
                                @foreach($shippers as $shipper)
                                    <option value="{{ $shipper->id }}" data-company_name="{{$shipper->name}}" data-customer_name="{{$shipper->poc}}" data-customer_address="{{$shipper->address}}" data-phone_no="{{$shipper->phone}}" data-email_address="{{$shipper->email}}">{{ $shipper->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col form-group">
                            <input type="text" name="company_name" id="company_name" class="form-control special_inputs" value="{{$daily_visit != null ? $daily_visit->company_name : ''}}" placeholder="Company Name*" data-rule-required="true" data-msg-required="Company Name is required" data-rule-maxlength="100" data-msg-maxlength="Company Name can be maximum 100 characters">
                        </div>
                        <div class="col form-group">
                            <input type="text" name="customer_name" id="customer_name" class="form-control special_inputs" value="{{$daily_visit != null ? $daily_visit->customer_name : ''}}" placeholder="Customer Name*" data-rule-required="true" data-msg-required="Customer Name is required" data-rule-maxlength="100" data-msg-maxlength="Customer Name can be maximum 100 characters">
                        </div>
                        <div class="col form-group">
                            <textarea name="customer_address" id="customer_address" class="form-control special_inputs" placeholder="Customer Address*" data-rule-required="true" data-msg-required="Customer Address is required" data-rule-maxlength="255" data-msg-maxlength="Address can be maximum 255 characters" rows="6">{{$daily_visit != null ? $daily_visit->customer_address : ''}}</textarea>
                        </div>
                        <div class="col form-group">
                            <input type="text" name="phone_no" id="phone_no" class="form-control phone_number special_inputs" value="{{$daily_visit != null ? $daily_visit->phone_no : ''}}" placeholder="Phone Number*" data-rule-required="true" data-msg-required="Phone Number is required">
                        </div>
                        <div class="col form-group">
                            <input type="email" name="email_address" id="email_address" placeholder="Email Address*" class="form-control special_inputs" value="{{$daily_visit != null ? $daily_visit->email : ''}}" data-rule-required="true" data-msg-required="Email Address is required">
                        </div>
                        <div class="col form-group">
                            <textarea name="feedback" class="form-control" placeholder="Meeting Feedback*" data-rule-required="true" data-msg-required="Feedback is required" rows="6">{{$daily_visit != null ? $daily_visit->feedback : ''}}</textarea>
                        </div>
                        <div class="col form-group">
                            <label for="upload_bc_image"><b>Please upload a photo of the business card:</b></label>
                            <input class="form-control form-control-sm" type="file" name="upload_bc_image" id="upload_bc_image" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">
                        </div>

                        <div class="col form-group">
                            <label for="upload_l_image"><b>Location Photo:</b></label>
                            <input class="form-control form-control-sm" type="file" name="upload_l_image" id="upload_l_image" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">
                        </div>

                        <div class="row mt-2">
                            <div class="col">
                                <div class="form-group text-center">
                                    <button type="submit" name="update" id="daily_visit_form_submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>


                </form>

            </div>
        </div>
    </div>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/toggle/bootstrap-switch.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/switch.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/spinner/jquery.bootstrap-touchspin.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/toggle/bootstrap-checkbox.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/spinner/jquery.bootstrap-touchspin.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            var latitude = null;
            var longitude = null;

            $('#lead_status').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Lead Status*'
            });


            $('#shipper').prepend('<option value="" selected="selected"></option><option value="0">Other</option>').select2({
                width: '100%',
                placeholder: 'Select Shipper*'
            }).bind('change',function () {
                var id = $(this).val();
                if(id != 0)
                {
                    var company_name = $(this).find(':selected').attr('data-company_name');
                    var customer_name = $(this).find(':selected').attr('data-customer_name');
                    var customer_address = $(this).find(':selected').attr('data-customer_address');
                    var phone_no = $(this).find(':selected').attr('data-phone_no');
                    var email_address = $(this).find(':selected').attr('data-email_address');

                    $(".special_inputs").attr('readonly',true);
                    $("#company_name").val(company_name);
                    $("#customer_name").val(customer_name);
                    $("#customer_address").val(customer_address);
                    $("#phone_no").val(phone_no);
                    $("#email_address").val(email_address);
                }
                else{
                    $(".special_inputs").attr('readonly',false);
                    $("#company_name").val('');
                    $("#customer_name").val('');
                    $("#customer_address").val('');
                    $("#phone_no").val('');
                    $("#email_address").val('');
                }
            });

            $("#lead_status").on('change',function(){
                id = $(this).val();
                if(id == 4 || id == 5){
                    $('#shipper').val(0).trigger('change');
                    $('#shipper').attr('disabled', true);
                } else{
                    $('#shipper').val('').trigger('change');
                    $('#shipper').removeAttr('disabled');
                }
            });

            @if($daily_visit != null)
                $("#lead_status").val("{{$daily_visit->lead_status_id ?? ''}}").trigger('change');
                $("#shipper").val("{{$daily_visit->shipper_id ?? ''}}").trigger('change');
                $("#daily_visit_id").val("{{$daily_visit->id}}");
                $("#company_name").val("{{$daily_visit->company_name}}");
                $("#customer_name").val("{{$daily_visit->customer_name}}");
                $("#customer_address").val("{{$daily_visit->customer_address}}");
                $("#phone_no").val("{{$daily_visit->phone_no}}");
                $("#email_address").val("{{$daily_visit->email}}");
            @endif

            $('.phone_number').inputmask({
                'mask': '9999-9999999',
                'clearIncomplete': true
            });
            $('#daily_visit_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $('#shipper').removeAttr('disabled');
                    if(latitude == null && longitude == null){
                        swal({
                            title: 'Location Not Found',
                            text: 'Please allow browser to access your location!',
                            icon: 'warning',
                            buttons: {
                                confirm: {
                                    text: 'Ok',
                                    value: true,
                                    visible: true,
                                    closeModal: true
                                }
                            },
                            closeOnClickOutside: false,
                            closeOnEsc: false,
                            dangerMode: true
                        });
                    }
                    else{
                        getLocation();
                        swal({
                            title: 'Please Wait!',
                            text: 'Uploading Form!',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });
                        form.submit();
                    }
                }
            });
            // var x = document.getElementById("daily_visit_form_submit");

            function getLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(showPosition);
                } else {
                    swal({
                        title: 'Location Not Found',
                        text: 'Geolocation is not supported by this browser.!',
                        icon: 'info',
                        buttons: {
                            confirm: {
                                text: 'Ok',
                                value: true,
                                visible: true,
                                closeModal: true
                            }
                        },
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        dangerMode: true
                    });
                }
            }
            function showPosition(position) {
                latitude = position.coords.latitude;
                longitude = position.coords.longitude;
                $('#latitude').val(latitude);
                $('#longitude').val(longitude);
            }
            getLocation();
        });
    </script>
@endsection