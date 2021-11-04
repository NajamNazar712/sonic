@extends('client.layout.master')
@section('title','Subscription For Final Charges')

@section('content')
    <h1 class="mb-1">
        Subscription For Final Charges
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('client.inc.messages')

                <div class="row justify-content-center">
                    <div class="col-5">
                        <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('cod.settings.final_charges_subscription.store') }}" novalidate="novalidate">
                            {{ csrf_field() }}


                                    @if($user_subscription != null)
                                        <div id="info_display" class="form-group text-center p-1 border border-light rounded">
                                            <label class="d-block">Subscription</label>
                                            @if($user_subscription->status == 1)
                                                <input type="checkbox" name="subscription_status" class="switch hidden" id="subscription_status" checked="checked">
                                            @else
                                                <input type="checkbox" name="subscription_status" class="switch hidden" id="subscription_status">
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="subscription_url" class="form-control subscription_url" placeholder="Subscription Url*" data-rule-required="true" data-msg-required="Subscription Url is required" value="{{ $user_subscription->url }}">
                                        </div>

                                    @else
                                        <div id="info_display" class="form-group text-center p-1 border border-light rounded">
                                            <label class="d-block">Subscription</label>
                                            <input type="checkbox" name="subscription_status" class="switch hidden" id="subscription_status">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="subscription_url" class="form-control subscription_url" placeholder="Subscription Url*" data-rule-required="true" data-msg-required="Subscription Url is required">
                                        </div>

                                    @endif



                            <button type="submit" class="btn btn-primary mt-2">Update</button>
                        </form>
                    </div>
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

            $('#subscription_status').checkboxpicker();

            $('#settings_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                }
            });
        });
    </script>
@endsection