@extends('admin.layout.master')

@section('title', 'Email Delivery timing')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Email Delivery timing
                </h1>
                <div class="card" id="main_card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-6">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" {{-- action="{{ route('admin.settings.reports.email_delivery_time.update') }}" --}} action="{{ route('admin.settings.email_delivery_time.update') }}" novalidate="novalidate">
                                        {{ csrf_field() }}

                                        <div class="mb-4">
                                            <div class="bg-blue">
                                                <h3 class="form-section white">
                                                    <b>ID:</b> 241 | <b>Name:</b> Pending Deliveries Report Email
                                                </h3>
                                            </div>
                                            <div class="col-12 mb-2 d-flex">
                                                Email delivery based on today date:
                                            </div>
                                            <div class="col-12 form-group">
                                                <div class="row d-flex align-items-center">
                                                    <div class="col-md-6">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                                    Time:
                                                                </span>
                                                            </div>
                                                            <input type="text" name="pending_deliveries_report_time" class="form-control bg-primary border-primary white rounded-right pickatime pending_deliveries_report_time" value="{{ $pending_deliveries_report_time ?? '09:00 AM' }}" id="pending_deliveries_report_time" placeholder="Time">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 d-flex align-items-center">
                                                        <label class="mr-2 font-small-3"><b>Email Delivery: </b></label>
                                                        <input type="checkbox" name="pending_deliveries_report_toggle" id="pending_deliveries_report_toggle" class="switchery pending_deliveries_report_toggle" data-size="sm" data-switchery="true" {{ $pending_deliveries_report_toggle == 1 ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <div class="bg-blue">
                                                <h3 class="form-section white">
                                                    <b>ID:</b> 242 | <b>Name:</b> Quality of Service Report Email
                                                </h3>
                                            </div>
                                            <div class="col-12 mb-2 d-flex">
                                                Email delivery based on today date:
                                            </div>
                                            <div class="col-12 form-group">
                                                <div class="row d-flex align-items-center">
                                                    <div class="col-4">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                                    Time 1:
                                                                </span>
                                                            </div>
                                                            <input type="text" name="quality_of_service_report_time" class="form-control bg-primary border-primary white rounded-right pickatime quality_of_service_report_time" value="{{ $quality_of_service_report_time ?? '09:00 AM' }}" id="quality_of_service_report_time" placeholder="Time">
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                                    Time 2:
                                                                </span>
                                                            </div>
                                                            <input type="text" name="quality_of_service_report_other_time" class="form-control bg-primary border-primary white rounded-right pickatime quality_of_service_report_other_time" value="{{ $quality_of_service_report_other_time ?? '02:00 PM' }}" id="quality_of_service_report_other_time" placeholder="Time">
                                                        </div>
                                                    </div>
                                                    <div class="col-4 d-flex align-items-center">
                                                        <label class="mr-2 font-small-3"><b>Email Delivery: </b></label>
                                                        <input type="checkbox" name="quality_of_service_toggle" id="quality_of_service_toggle" class="switchery quality_of_service_toggle" data-size="sm" data-switchery="true"{{ $quality_of_service_report_toggle == 1 ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        

                                        <div class="mb-4">
                                            <div class="bg-blue">
                                                <h3 class="form-section white">
                                                    <b>ID:</b> 236 | <b>Name:</b> Receive Deliveries Report Email
                                                </h3>
                                            </div>
                                            <div class="col-12 mb-2 d-flex">
                                                Email delivery based on today date:
                                            </div>
                                            <div class="col-12 form-group">
                                                <div class="row d-flex align-items-center">
                                                    <div class="col-md-6">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                                    Time:
                                                                </span>
                                                            </div>
                                                            <input type="text" name="receive_deliveries_report_time" class="form-control bg-primary border-primary white rounded-right pickatime receive_deliveries_report_time" value="{{ $receive_deliveries_report_time ?? '09:00 AM' }}" id="receive_deliveries_report_time" placeholder="Time">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 d-flex align-items-center">
                                                        <label class="mr-2 font-small-3"><b>Email Delivery: </b></label>
                                                        <input type="checkbox" name="receive_delivieries_toggle" id="receive_delivieries_toggle" class="switchery receive_delivieries_toggle" data-size="sm" data-switchery="true"{{ $receive_deliveries_toggle == 1 ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <div class="bg-blue">
                                                <h3 class="form-section white">
                                                    <b>ID:</b> 237 | <b>Name:</b> Receive Return Deliveries Report Email
                                                </h3>
                                            </div>
                                            <div class="col-12 mb-2 d-flex">
                                                Email delivery based on today date:
                                            </div>
                                            <div class="col-12 form-group">
                                                <div class="row d-flex align-items-center">
                                                    <div class="col-md-6">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                                    Time:
                                                                </span>
                                                            </div>
                                                            <input type="text" name="receive_return_deliveries_report_time" class="form-control bg-primary border-primary white rounded-right pickatime receive_return_deliveries_report_time" value="{{ $receive_return_deliveries_report_time ?? '09:00 AM' }}" id="receive_return_deliveries_report_time" placeholder="Time">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 d-flex align-items-center">
                                                        <label class="mr-2 font-small-3"><b>Email Delivery: </b></label>
                                                        <input type="checkbox" name="receive_return_deliveries_toggle" id="receive_return_deliveries_toggle" class="switchery receive_return_deliveries_toggle" data-size="sm" data-switchery="true"{{ $receive_return_deliveries_report_toggle == 1 ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <div class="bg-blue">
                                                <h3 class="form-section white">
                                                    <b>ID:</b> 238 | <b>Name:</b> Delivery Note History Email
                                                </h3>
                                            </div>
                                            <div class="col-12 mb-2 d-flex">
                                                Email delivery based on today date:
                                            </div>
                                            <div class="col-12 form-group">
                                                <div class="row d-flex align-items-center">
                                                    <div class="col-md-6">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                                    Time:
                                                                </span>
                                                            </div>
                                                            <input type="text" name="delivery_note_history_report_time" class="form-control bg-primary border-primary white rounded-right pickatime delivery_note_history_report_time" value="{{ $delivery_note_history_report_time ?? '09:00 AM' }}" id="delivery_note_history_report_time" placeholder="Time">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 d-flex align-items-center">
                                                        <label class="mr-2 font-small-3"><b>Email Delivery: </b></label>
                                                        <input type="checkbox" name="delivery_note_history_toggle" id="delivery_note_history_toggle" class="switchery delivery_note_history_toggle" data-size="sm" data-switchery="true"{{ $delivery_note_history_report_toggle == 1 ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <div class="bg-blue">
                                                <h3 class="form-section white">
                                                    <b>ID:</b> 239 | <b>Name:</b> Weight QC Report Email
                                                </h3>
                                            </div>
                                            <div class="col-12 mb-2 d-flex">
                                                Email delivery based on today date:
                                            </div>
                                            <div class="col-12 form-group">
                                                <div class="row d-flex align-items-center">
                                                    <div class="col-md-6">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                                    Time:
                                                                </span>
                                                            </div>
                                                            <input type="text" name="weight_qc_report_time" class="form-control bg-primary border-primary white rounded-right pickatime weight_qc_report_time" value="{{ $weight_qc_report_time ?? '09:00 AM' }}" id="weight_qc_report_time" placeholder="Time">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 d-flex align-items-center">
                                                        <label class="mr-2 font-small-3"><b>Email Delivery: </b></label>
                                                        <input type="checkbox" name="weight_qc_toggle" id="weight_qc_toggle" class="switchery weight_qc_toggle" data-size="sm" data-switchery="true"{{ $weight_qc_report_toggle == 1 ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <div class="bg-blue">
                                                <h3 class="form-section white">
                                                    <b>ID:</b> 240 | <b>Name:</b> Overall Sales Report Email
                                                </h3>
                                            </div>
                                            <div class="col-12 mb-2 d-flex">
                                                Email delivery based on today date:
                                            </div>
                                            <div class="col-12 form-group">
                                                <div class="row d-flex align-items-center">
                                                    <div class="col-md-6">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                                    Time:
                                                                </span>
                                                            </div>
                                                            <input type="text" name="overall_sales_report_time" class="form-control bg-primary border-primary white rounded-right pickatime overall_sales_report_time" value="{{ $overall_sales_report_time ?? '09:00 AM' }}" id="overall_sales_report_time" placeholder="Time">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 d-flex align-items-center">
                                                        <label class="mr-2 font-small-3"><b>Email Delivery: </b></label>
                                                        <input type="checkbox" name="overall_sales_toggle" id="overall_sales_toggle" class="switchery overall_sales_toggle" data-size="sm" data-switchery="true"{{ $overall_sales_report_toggle == 1 ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12 form-group mt-4">
                                            <button type="submit" class="col-md-4 btn btn-primary">Update</button>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">

    <style>
        #main_card {
            height: 1600px;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.time.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script>
        
            $(document).ready(function () {
                $('.pending_deliveries_report_time').pickatime({
                    clear: 'Clear',
                    format: 'h:i A',
                    interval: 15,
                    onSet: function(context) {
                        if (context.select) {
                            $('#arrival_time_to').pickatime('picker').set('min', $('#pending_deliveries_report_time').pickatime('picker').get('select'));
                        }
                    }
                });

                $('.quality_of_service_report_time').pickatime({
                    clear: 'Clear',
                    format: 'h:i A',
                    interval: 15,
                    onSet: function(context) {
                        if (context.select) {
                            $('#arrival_time_to').pickatime('picker').set('min', $('#quality_of_service_report_time').pickatime('picker').get('select'));
                        }
                    }
                });

                $('.quality_of_service_report_other_time').pickatime({
                    clear: 'Clear',
                    format: 'h:i A',
                    interval: 15,
                    onSet: function(context) {
                        if (context.select) {
                            $('#arrival_time_to').pickatime('picker').set('min', $('#quality_of_service_report_other_time').pickatime('picker').get('select'));
                        }
                    }
                });

                $('.receive_deliveries_report_time').pickatime({
                    clear: 'Clear',
                    format: 'h:i A',
                    interval: 15,
                    onSet: function(context) {
                        if (context.select) {
                            $('#arrival_time_to').pickatime('picker').set('min', $('#receive_deliveries_report_time').pickatime('picker').get('select'));
                        }
                    }
                });

                $('.receive_return_deliveries_report_time').pickatime({
                    clear: 'Clear',
                    format: 'h:i A',
                    interval: 15,
                    onSet: function(context) {
                        if (context.select) {
                            $('#arrival_time_to').pickatime('picker').set('min', $('#receive_return_deliveries_report_time').pickatime('picker').get('select'));
                        }
                    }
                });

                $('.delivery_note_history_report_time').pickatime({
                    clear: 'Clear',
                    format: 'h:i A',
                    interval: 15,
                    onSet: function(context) {
                        if (context.select) {
                            $('#arrival_time_to').pickatime('picker').set('min', $('#delivery_note_history_report_time').pickatime('picker').get('select'));
                        }
                    }
                });

                $('.weight_qc_report_time').pickatime({
                    clear: 'Clear',
                    format: 'h:i A',
                    interval: 15,
                    onSet: function(context) {
                        if (context.select) {
                            $('#arrival_time_to').pickatime('picker').set('min', $('#weight_qc_report_time').pickatime('picker').get('select'));
                        }
                    }
                });

                $('.overall_sales_report_time').pickatime({
                    clear: 'Clear',
                    format: 'h:i A',
                    interval: 15,
                    onSet: function(context) {
                        if (context.select) {
                            $('#arrival_time_to').pickatime('picker').set('min', $('#overall_sales_report_time').pickatime('picker').get('select'));
                        }
                    }
                });

            });
        
    </script>
@endsection
