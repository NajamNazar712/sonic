@extends('admin.layout.master')

@section('title', 'Rider Deactivation Cron Settings')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Rider Deactivation Cron Settings
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-5 col-sm-4 col-md-3 col-lg-2">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.rider_deactivation_cron.store') }}" novalidate="novalidate">
                                        {{ csrf_field() }}

                                        <div class="form-group text-center">
                                            <label class="font-medium-2 font-weight-bold block">Cron Status</label>
                                            <div class="form-group">
                                                <label for="cron_status" class="font-medium-2 text-bold-600 mr-1">Disable</label>
                                                <input type="checkbox" name="cron_status" id="cron_status" class="cron_status" data-size="sm" data-switchery="true" {{ ($settings[1]->setting_value)? 'checked':'' }}>
                                                <label for="cron_status" class="font-medium-2 text-bold-600 ml-1">Enable</label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Deactivation Days <span class="text-danger">*</span></label>
                                            <input type="text" name="deactivation_days" class="form-control deactivation_days" placeholder="Deactivation Days *" data-rule-required="true" data-msg-required="This field is required" value="{{ $settings[0]->setting_value }}" data-rule-min="1" data-msg-min="Shipment attempt duration can not be less than 1">
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
            var elm = document.getElementById("cron_status");
            var switchery = new Switchery(elm, { className: "switchery switchery-small", color: "#37BC9B" });

            $('#settings_form input.deactivation_days').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
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