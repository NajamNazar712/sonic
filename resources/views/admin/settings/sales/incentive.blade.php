@extends('admin.layout.master')

@section('title', 'Sales Incentive Settings')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                   Sales Incentive Settings
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <form method="post" id="sales_incentive_settings" class="form-horizontal text-center" method="post" action="{{ route('admin.settings.sales.incentive.add') }}" novalidate="novalidate">
                                {{ csrf_field() }}
                                <div class="row justify-content-center">
                                    <div class="col-4 form-group">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                                            </div>
                                            <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Date (From)" value="{{ Carbon\Carbon::parse($incentives[0]->fromdate)->format('F d Y') }}" data-rule-required="true" data-msg-required="Date is required">
                                        </div>
                                    </div>
                                    <div class="col-4 form-group">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                                            </div>
                                            <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Date (To)" value="{{ Carbon\Carbon::parse($incentives[0]->todate)->format('F d Y') }}" data-rule-required="true" data-msg-required="Date is required">
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="col-3 form-group">
                                        <label><strong>BDM</strong></label>
                                        <div class="input-group">
                                            <input type="text" name="BDM" class="form-control amount" placeholder="BDM*" value="{{ $incentives[0]->incentive }}" data-rule-required="true" data-msg-required="BDM incentive is required">
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row justify-content-center">
                                    <button type="submit" class="btn btn-primary">Update</button>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>

    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            
            $('#sales_incentive_settings .amount').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'min': 0,
                'max': 10000000
            });
            $('#search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_date_to').pickadate('picker').set('min', $('#search_date_from').pickadate('picker').get('select'));
                    }
                }
            });

            $('#search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_date_from').pickadate('picker').set('max', $('#search_date_to').pickadate('picker').get('select'));
                    }
                }
            });

            $('#sales_incentive_settings').validate({
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