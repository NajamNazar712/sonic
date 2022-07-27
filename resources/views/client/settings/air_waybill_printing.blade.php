@extends('client.layout.master')
@section('title','Air Waybill Information And Print Count')

@section('content')
    <h1 class="mb-1">
        Air Waybill Setting
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('client.inc.messages')
                <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('cod.settings.air_waybill_printing.store') }}" novalidate="novalidate">
                    {{ csrf_field() }}

                        <div class="row justify-content-center">
                            <div class="col-4">
                                <div class="form-group">
                                    <div class="input-group">
                                        @if($air_waybill != null)
                                            <div id="info_display" class="form-group text-center p-1" style="display: contents">
                                                <label class="d-block">Show Information on Air Waybill (Default)</label>
                                                @if($air_waybill->information == 1)
                                                    <input type="checkbox" name="information_display" class="switch hidden" id="information_display" checked="checked">
                                                @else
                                                    <input type="checkbox" name="information_display" class="switch hidden" id="information_display">
                                                @endif
                                            </div>
{{--                                            <input type="text" name="air_waybill_printing_count" class="form-control air_waybill_printing_count" placeholder="Air Waybill*" data-rule-required="true" data-msg-required="Air Waybill Print Count is required" value="{{ $air_waybill->print_count }}" data-rule-min="1" data-msg-min="Air Waybill Print Count can not be less than 1" data-rule-max="3" data-msg-min="Air Waybill Print Count can not be more than 3">--}}
                                        @else
                                            <div id="info_display" class="form-group text-center p-1" style="display: contents">
                                                <label class="d-block">Show Information on Air Waybill for all Shipments</label>
                                                <input type="checkbox" name="information_display" class="switch hidden" id="information_display" checked="checked">
                                            </div>
{{--                                            <input type="text" name="air_waybill_printing_count" class="form-control air_waybill_printing_count" placeholder="Air Waybill*" data-rule-required="true" data-msg-required="Air Waybill Print Count is required" value="1" data-rule-min="1" data-msg-min="Air Waybill Print Count can not be less than 1" data-rule-max="3" data-msg-min="Air Waybill Print Count can not be more than 3">--}}
                                        @endif
{{--                                        <div class="input-group-append">--}}
{{--                                            <span class="input-group-text">No. of Prints</span>--}}
{{--                                        </div>--}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-4">
                                <div class="form-group">
                                    <div class="input-group">
                                            <div id="info_display" class="form-group text-center p-1" style="display: contents">
                                                <label class="d-block">Air Waybill Page Break &nbsp;</label>
                                                <input type="checkbox" name="page_breaker"  class="switch hidden" id="page_breaker" @if(isset($air_waybill->page_breaker)) @if($air_waybill->page_breaker == 1)   checked="checked" @endif @endif >
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-4">
                            <div class="form-group" style="width: 88%">
                                <div class="input-group">
                                        <label class="d-block">No of Air Waybill Print &nbsp;</label>
                                        @if($air_waybill != null)
                                            <input type="text" name="air_waybill_printing_count" class="form-control air_waybill_printing_count" placeholder="Air Waybill*" data-rule-required="true" data-msg-required="Air Waybill Print Count is required" value="{{ $air_waybill->print_count }}" data-rule-min="1" data-msg-min="Air Waybill Print Count can not be less than 1" data-rule-max="3" data-msg-max="Air Waybill Print Count can not be more than 3">
                                            @else
                                            <input type="text" name="air_waybill_printing_count" class="form-control air_waybill_printing_count" placeholder="Air Waybill*" data-rule-required="true" data-msg-required="Air Waybill Print Count is required" value="1" data-rule-min="1" data-msg-min="Air Waybill Print Count can not be less than 1" data-rule-max="3" data-msg-max="Air Waybill Print Count can not be more than 3">
                                        @endif

                                </div>
                            </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>

                </form>
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
        .btn-group{
            margin-left: 20px;
        }
        .d-block{
            margin-top: 10px;
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

            $('#information_display').checkboxpicker();
            $('#page_breaker').checkboxpicker();

            $('#settings_form input.air_waybill_printing_count').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'max': 3,
                'min': 1
            });

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