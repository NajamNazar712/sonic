@extends('admin.layout.master')

@section('title', 'Debriefing Report Cut-Off Time')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Debriefing Report Cut-Off Time
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-5 col-sm-4 col-md-3 col-lg-2">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.debriefing_report_cut_off_time.store') }}" novalidate="novalidate">
                                        {{ csrf_field() }}

                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="text" name="debriefing_report_cut_off_time" class="form-control debriefing_report_cut_off_time" placeholder="Debriefing Report Cut-Off Time*" data-rule-required="true" data-msg-required="Debriefing Report Cut-Off Time is required" value="{{ $cut_off_time }}" data-rule-min="0" data-msg-min="Debriefing Report Cut-Off Time can not be less than 0" data-rule-max="23" data-msg-min="Debriefing Report Cut-Off Time can not be more than 23">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">hours</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="text" name="debriefing_report_cut_off_time_start" class="form-control debriefing_report_cut_off_time_start" placeholder="Debriefing Report Cut-Off Time Start*" data-rule-required="true" data-msg-required="Debriefing Report Cut-Off Time Start is required" value="{{ $cut_off_time_start }}" data-rule-min="0" data-msg-min="Debriefing Report Cut-Off Time can not be less than 0" data-rule-max="23" data-msg-min="Debriefing Report Cut-Off Time can not be more than 23">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">hours</span>
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
    </div>
@endsection

@section('css')
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('#settings_form input.debriefing_report_cut_off_time, #settings_form input.debriefing_report_cut_off_time_start').inputmask({
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
        });
    </script>
@endsection