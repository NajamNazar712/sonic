@extends('admin.layout.master')

@section('title', 'RCP SMS')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                   RCP SMS
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.rcp_sms.update') }}" novalidate="novalidate">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="toggle_check" id="toggle_check" class="toggle_check">
                                    <div class="row">
                                        <div class="input-group ml-1">
                                            <label class="mr-2"><b>On/Off</b></label>
                                            <div class="form-group">
                                                <input type="checkbox" name="count_toggle" id="count_toggle" class="switchery count_toggle" data-size="sm" data-switchery="true" @if(isset($setting->setting_value) && $setting->setting_value == 1) checked @endif>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="input-group">
                                            <label class="m-1"><b>Count</b></label>
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="sms_count"  placeholder="SMS Count" required data-rule-required="true" data-msg-required="This field is required" value=" {{$setting->text}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center">
                                        <div class="col">
                                            <div class="form-group input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                                <span class="">Time*</span>
                                                    </span>
                                                </div>
                                                <div>
                                                    <input type="text" name="time" class="form-control bg-primary border-primary white pickatime time" id="time" value="{{$time->text}}" placeholder="Time*" data-rule-required="true" data-msg-required="Time To is required">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">

    <style>
        input#time {
            margin: 0 50px 0px 0px;
        }

        span.input-group-text.bg-primary.bg-darken-2.border-primary.white.rounded-left span {
            padding: 0 19px 0 11px;
        }

        span.input-group-text.bg-primary.bg-darken-2.border-primary.white.rounded-left {
            margin: 0px -2px 7px 0px;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.time.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('.time').pickatime({
                clear: '',
                format: 'h:i A',
            });

            $('#settings_form input.reattempt_percentage').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            $('#settings_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                }
            });
            @if(isset($setting->setting_value) && $setting->setting_value == 1)
                $('#toggle_check').val(1);
            @else
                $('#toggle_check').val(0);
            @endif
            $("#count_toggle").on('change', function(){
                if($("#count_toggle").is(":checked")){
                    $('#toggle_check').val(1);
                }
                else{
                    $('#toggle_check').val(0);
                }
            });
        });
    </script>
@endsection