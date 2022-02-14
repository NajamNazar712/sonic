@extends('admin.layout.master')

@section('title', 'Rush & Saver Plus Cargo Report City Update')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    {{$origin->name}} Rush & Saver Plus Cargo Report Setting
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                    <form id="settings_form" class="form-horizontal" method="POST" action="{{ route('admin.settings.overnight_overland_cargo_report.edit.update') }}" novalidate="novalidate">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="origin_id" value="{{$origin->id}}">
                                        <h4 class="input-group form-section mb-2 justify-content-center"><b>Cut-Off Time</b></h4>
                                        <div class="row justify-content-center">
                                            <div class="col-4">
                                                <div class="form-group input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                            <span class="la la-clock-o"></span>
                                                        </span>
                                                    </div>
                                                    <input type="text" name="cut_off_time" class="form-control bg-primary border-primary white rounded-right pickatime cut_off_time" value="{{$origin->cut_off_time}}" id="cut_off_time" placeholder="Cut Off Time*" data-rule-required="true" data-msg-required="Cut-Off Time is required">
                                                </div>
                                            </div>
                                        </div>
                                        <h4 class="input-group form-section mb-2 justify-content-center"><b>Hubs For Origin</b></h4>
                                        <div class="col-12 mb-3">
                                            @foreach($hubs as $hub)
                                                <fieldset class="d-inline-block m-1">
                                                    @if (in_array($hub->id, $overnight_hubs))
                                                        <input type="checkbox" id="hub_{{ $hub->id }}" class="hub" name="hub_ids[]" value="{{ $hub->id }}" checked="checked">
                                                    @else
                                                        <input type="checkbox" id="hub_{{ $hub->id }}" class="hub" name="hub_ids[]" value="{{ $hub->id }}">
                                                    @endif
                                                    <label for="hub_{{ $hub->id }}">{{ $hub->name }}</label>
                                                </fieldset>
                                            @endforeach
                                        </div>
                                        <div class="input-group justify-content-center">
                                            <button type="submit" class="btn btn-primary" style="width: 200px">Update</button>
                                        </div>
                                    </form>
                                </div>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.time.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('.cut_off_time').pickatime({
                clear: '',
                format: 'h:i A',
            });

            $('.numeric').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false
            });
            $('.local').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false
            });
            $('.national').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false
            });


            $('#settings_form .hub').each(function() {
                var checkbox = $(this);
                var label = checkbox.next();
                var text = label.text();

                label.remove();

                checkbox.iCheck({
                    checkboxClass: 'icheckbox_line pt-1 pb-1',
                    checkedClass: 'checked bg-success',
                    uncheckedClass: 'bg-danger',
                    insert: '<div class="icheck_line-icon"></div>' + text
                });
            });

            $('#settings_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                }
            });
        });
    </script>
@endsection