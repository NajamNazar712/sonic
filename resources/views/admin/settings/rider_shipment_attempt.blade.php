@extends('admin.layout.master')

@section('title', 'Rider Shipments Attempt Settings')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Rider Shipments Attempt Settings
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-5 col-sm-4 col-md-3 col-lg-2">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.rider_shipment_attempt.store') }}" novalidate="novalidate">
                                        {{ csrf_field() }}

                                        <div class="form-group">
                                            <label>Consecutive Shipment Count</label>
                                            <input type="text" name="shipment_attempt_count" class="form-control shipment_attempt_count" placeholder="Shipment Attempt Count*" data-rule-required="true" data-msg-required="This field is required" value="{{ $settings[0]->setting_value }}" data-rule-min="1" data-msg-min="Shipment attempt count can not be less than 1">
                                        </div>

                                        <div class="form-group">
                                            <label>Waiting Duration (Minutes)</label>
                                            <input type="text" name="shipment_attempt_duration" class="form-control shipment_attempt_duration" placeholder="Shipment Attempt Duration (Minutes)*" data-rule-required="true" data-msg-required="This field is required" value="{{ $settings[1]->setting_value }}" data-rule-min="0" data-msg-min="Shipment attempt duration can not be less than 0">
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('#settings_form input.shipment_attempt_count, #settings_form input.shipment_attempt_duration').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
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