@extends('admin.layout.master')

@section('title', 'Auto Account Disabled Days')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                   International Rates Settings
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <form method="post" id="int_rates_settings" class="form-horizontal text-center" action="{{ route('admin.settings.international_rates.update') }}" novalidate="novalidate">
                                {{ csrf_field() }}
                                <div class="row justify-content-center">
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label>Fuel Surcharge</label>
                                            <input type="text" name="fuel_surcharge" value="{{$fuel_surcharge->setting_value}}" class="select2 form-control" placeholder="Fuel Surcharge" data-rule-required="true" data-msg-required="Fuel Surcharge is required">
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label>Exchange Rate</label>
                                            <input type="text" name="exchange_rate" value="{{$exchange_rate->setting_value}}" class="select2 form-control"  placeholder="Exchange Rate" data-rule-required="true" data-msg-required="Exchange Rate is required">
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <button type="submit" class="btn btn-primary">Update Rates</button>
                                </div>

                            </form>

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
            $('#settings_form input.auto_account_disabled_days').inputmask({
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