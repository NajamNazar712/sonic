@extends('admin.layout.master')

@section('title', 'International Rates Settings')

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

                                    <div class="col-3 form-group">
                                        <label><strong>Fuel Surcharge in %</strong></label>

                                        <div class="input-group">
                                            <input type="text" name="fuel_surcharge" class="form-control decimal" placeholder="Fuel Surcharge*" data-rule-required="true" data-msg-required="Fuel Surcharge is required" value="{{$fuel_surcharge}}">
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="row justify-content-center">
                                    <div class="col-3 form-group">
                                        <label><strong>Exchange Rate</strong></label>
                                        <div class="input-group">
                                            <input type="text" name="exchange_rate" value="{{$exchange_rate}}" class="form-control amount"  placeholder="Exchange Rate*" data-rule-required="true" data-msg-required="Exchange Rate is required">
                                            <div class="input-group-append">
                                                <span class="input-group-text">PKR</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="col-3 form-group">
                                        <label><strong>GST in %</strong></label>
                                        <div class="input-group">
                                            <input type="text" name="gst" value="{{$gst}}" class="form-control decimal"  placeholder="GST*" data-rule-required="true" data-msg-required="GST is required">
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
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
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('#int_rates_settings .decimal').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 2,
                'min': 0.00,
                'max': 10000.00
            });
            $('#int_rates_settings .amount').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 2,
                'min': 0.00,
                'max': 10000000.00
            });
            $('#int_rates_settings').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    swal({
                        title: 'Please Wait!',
                        text: 'Rate settings are being updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });
                    form.submit();

                }
            });
        });
    </script>
@endsection