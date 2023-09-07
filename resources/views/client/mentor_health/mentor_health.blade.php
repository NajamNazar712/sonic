@php
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
@endphp
@extends('client.layout.master')
@section('title','Mentor Health')

@section('content')
    <h1 class="mb-1">
        Mentor Health
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('client.inc.messages')
                <div class="row justify-content-center">
                    <form id="upload_logo_form" class="form" action="{{route('cod.mentor_health.add_request')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row justify-content-center">
                            <fieldset class="form-group col">
                                <input type="text" class="form-control" id="logo" name="company_name" value="{{$details['company_name']}}" placeholder="Company Name" data-rule-required="true" data-msg-required="Name is required">
                            </fieldset>
                            <fieldset class="form-group col">
                                <input type="text" class="form-control" id="logo" name="company_email" value="{{$details['company_email']}}" placeholder="Company Email" data-rule-required="true" data-msg-required="Name is required">
                            </fieldset>
                        </div>
                        <div class="row justify-content-center">
                            <fieldset class="form-group col">
                                <input type="text" class="form-control" id="logo" name="company_phone" value="{{$details['company_phone']}}" placeholder="Company Phone" data-rule-required="true" data-msg-required="Name is required">
                            </fieldset>
                            <fieldset class="form-group col">
                                <input type="text" class="form-control" id="logo" name="company_city" value="{{$details['company_city']}}" placeholder="Company City" data-rule-required="true" data-msg-required="Name is required">
                            </fieldset>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col">
                                <button id="submit" type="submit" class="btn btn-primary btn-block">Add Request</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/toggle/bootstrap-switch.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/switch.min.css')}}">



    <style>
        .btn-group .dropdown-menu .dropdown-item {
            white-space: normal;
        }

        #toast-bottom-center.toast-container {
            text-align: center;
        }

        #toast-bottom-center.toast-container .toast {
            display: table;
            width: auto !important;
            text-align: left;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    {{--    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/toggle/bootstrap-checkbox.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {
            $('#upload_logo_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    swal({
                        title: 'Please Wait!',
                        text: 'logo is being uploaded!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });
                    form.submit();
                }
            });

{{--            @if($logo_status == 1)--}}
            $('#remove_logo_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    swal({
                        text: 'Are you sure, you want to remove current logo?',
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
                    }).then(function(confirm) {
                        if (confirm) {
                            swal({
                                title: 'Please Wait!',
                                text: 'logo is being Removed!',
                                icon: 'info',
                                buttons: false,
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                            form.submit();
                        }
                    });
                }
            });
{{--            @endif--}}
        });
    </script>
@endsection