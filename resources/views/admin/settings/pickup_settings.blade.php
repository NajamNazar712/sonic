@extends('admin.layout.master')

@section('title', 'Pickup Cut-Off Settings')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Pickup Cut-Off Settings
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-6">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.pickup.pickup_settings_store') }}" novalidate="novalidate">
                                        {{ csrf_field() }}

                                        <div class="form-group">

                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Request Cut-Off Time</span>
                                                </div>
                                                <input type="text" name="request_cut_off_time" class="form-control request_cut_off_time" placeholder="Request Cut-Off Time*" data-rule-required="true" data-msg-required="Request Cut-Off Time is required" value="{{ $pickup_request_cut_off_time }}" data-rule-min="0" data-msg-min="Request Cut-Off Time can not be less than 0" data-rule-max="23" data-msg-min="Pickup Cut-Off Time can not be more than 23">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">hours</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Arrival Cut-Off Time</span>
                                                </div>
                                                <input type="text" name="arrival_cut_off_time" class="form-control arrival_cut_off_time" placeholder="Arrival Cut-Off Time*" data-rule-required="true" data-msg-required="Arrival Cut-Off Time is required" value="{{ $pickup_arrival_cut_off_time }}" data-rule-min="0" data-msg-min="Arrival Cut-Off Time can not be less than 0" data-rule-max="23" data-msg-min="Arrival Cut-Off Time can not be more than 23">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">hours</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rider Assignment-Off Time</span>
                                                </div>
                                                <input type="text" name="rider_assignment_off_time" class="form-control rider_assignment_off_time" placeholder="Rider Assignment-Off Time Start"  value="{{ $rider_assignment_cut_off_time }}" data-rule-min="0" data-msg-min="Rider Assignment-Off Time can not be less than 0" data-rule-max="23" data-msg-min="Rider Assignment-Off Time can not be more than 23">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">hours</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Global Rider ID</span>
                                                </div>
                                                <input type="text" name="global_rider_id" class="form-control global_rider_id" placeholder="GLobal Rider ID*" data-rule-required="true" data-msg-required="Global Rider ID is required" value="{{ $global_rider_id }}" data-rule-min="0" data-msg-min="Global Rider Id can not be less than 0">
                                                <div class="input-group-append">
                                                    <!-- <span class="input-group-text">hours</span> -->
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
            $('#settings_form input.request_cut_off_time, #settings_form input.arrival_cut_off_time, #settings_form input.rider_assignment_off_time, #settings_form input.global_rider_id').inputmask({
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