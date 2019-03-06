@extends('admin.layout.master')

@section('title', 'Auto-Invoice Generation & Due Date Length')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Auto-Invoice Generation & Due Date Length
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-4">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.auto_invoice_generation_and_due_date.store') }}" novalidate="novalidate">
                                        {{ csrf_field() }}

                                                <div class="input-group form-group">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Auto Invoice Generation*</span>
                                                    </div>
                                                    <input type="text" name="auto_invoice_generation_hours" class="form-control auto_invoice_generation_hours" placeholder="" data-rule-required="true" data-msg-required="Auto Invoice Generation hours is required" value="{{ $auto_invoice_generation_time->setting_value }}" data-rule-min="1" data-msg-min="Auto Invoice Generation hours can not be less than 1" data-rule-max="24" data-msg-max="Auto Invoice Generation hours can not be greater than 24">
                                                    <div class="input-group-append" style="width: 14px">
                                                        <span class="input-group-text">Hours*</span>
                                                    </div>
                                                </div>
                                                <div class="input-group form-group">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Due Date*</span>
                                                    </div>
                                                    <input type="text" name="due_date_days" class="form-control due_date_days" placeholder="" data-rule-required="true" data-msg-required="Due Date Days is required" value="{{ $due_date_days->setting_value }}" data-rule-min="1" data-msg-min="Due Date Days can not be less than 1">
                                                    <div class="input-group-append" style="width: 5px">
                                                        <span class="input-group-text">Days*</span>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('#settings_form input.auto_invoice_generation_days').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false
            });

            $('#settings_form input.due_date_days').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false
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