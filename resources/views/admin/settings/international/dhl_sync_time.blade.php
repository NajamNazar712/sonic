@extends('admin.layout.master')

@section('title', 'DHL International Shipment Sync Time')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    DHL International Shipment Sync Time
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-5">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.dhl_sync_time.store') }}" novalidate="novalidate">
                                        {{ csrf_field() }}

                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="text" name="shipment_sync_time_1" class="form-control shipment_sync_time" placeholder="International Shipment Sync Time*" data-rule-required="true" data-msg-required="International Shipment Sync Time is required" value="{{ $time_1 }}" data-rule-min="1" data-msg-min="International Shipment Sync Time can not be less than 1" data-rule-max="23" data-msg-min="International Shipment Sync Time can not be more than 23">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">hrs</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="text" name="shipment_sync_time_2" class="form-control shipment_sync_time" placeholder="International Shipment Sync Time*" data-rule-required="true" data-msg-required="International Shipment Sync Time is required" value="{{ $time_2 }}" data-rule-min="1" data-msg-min="International Shipment Sync Time can not be less than 1" data-rule-max="23" data-msg-min="International Shipment Sync Time can not be more than 23">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">hrs</span>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('#settings_form input.shipment_sync_time').inputmask({
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