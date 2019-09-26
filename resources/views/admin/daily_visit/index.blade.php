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
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">
                        <div class="col form-group">
                            <input type="text" name="company_name" class="form-control" placeholder="Company Name*" data-rule-required="true" data-msg-required="Company Name is required" data-rule-maxlength="100" data-msg-maxlength="Company Name can be maximum 100 characters">
                        </div>
                        <div class="col form-group">
                            <input type="text" name="customer_name" class="form-control" placeholder="Customer Name*" data-rule-required="true" data-msg-required="Customer Name is required" data-rule-maxlength="100" data-msg-maxlength="Customer Name can be maximum 100 characters">
                        </div>
                        <div class="col form-group">
                            <textarea name="customer_address" class="form-control" placeholder="Customer Address*" data-rule-required="true" data-msg-required="Customer Address is required" data-rule-maxlength="190" data-msg-maxlength="Address can be maximum 190 characters" rows="6"></textarea>
                        </div>
                        <div class="col form-group">
                            <input type="text" name="phone_no" class="form-control phone_number" placeholder="Phone Number*" data-rule-required="true" data-msg-required="Phone Number is required">
                        </div>
                        <div class="col form-group">
                            <input type="email" name="email_address" placeholder="Email Address*" class="form-control" data-rule-required="true" data-msg-required="Email Address is required">
                        </div>
                        <div class="col form-group">
                            <select name="lead_status" class="select2" id="lead_status" data-rule-required="true" data-msg-required="Lead Status is required">
                                @foreach($lead_statuses as $lead_status)
                                    <option value="{{ $lead_status->id }}">{{ $lead_status->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col form-group">
                            <textarea name="feedback" class="form-control" placeholder="Meeting Feedback*" data-rule-required="true" data-msg-required="Feedback is required" rows="6"></textarea>
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