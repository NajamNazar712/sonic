@php
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
@endphp
@extends('client.layout.master')
@section('title','Upload Logo')

@section('content')
    <h1 class="mb-1">
        Upload Logo
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('client.inc.messages')

                @if($logo_status == 1)
                    <div class="row justify-content-center">
                        <div class="col-3 mb-1">
                            <img class="" src="{{asset($logo)}}" alt="" title="" style="max-width: 100%"/>
                        </div>
                    </div>
                    <form id="remove_logo_form" class="form" action="{{route('cod.settings.logo.remove')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row justify-content-center">
                            <div class="col-1 mb-3">
                                <button id="remove" type="submit" class="btn btn-sm btn-danger btn-block">Remove</button>
                            </div>
                        </div>
                    </form>
                @endif
                <div class="row justify-content-center">
                        <form id="upload_logo_form" class="form" action="{{route('cod.settings.logo.upload')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row justify-content-center">
                                <fieldset class="form-group col">
                                    <input type="file" class="form-control-file" id="logo" name="upload_logo" accept="image/*" data-rule-required="true" data-msg-required="Logo is required" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2048000" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">
                                </fieldset>
                            </div>

                            <hr>
                            <div class="row justify-content-center">
                                <div class="col">
                                    <button id="submit" type="submit" class="btn btn-primary btn-block">Upload</button>
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

            @if($logo_status == 1)
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
            @endif
        });
    </script>
@endsection