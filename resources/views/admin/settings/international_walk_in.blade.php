@extends('admin.layout.master')

@section('title', 'International Walk-In')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    International Walk-In
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                    <form id="settings_form" class="form-horizontal" method="POST" action="{{ route('admin.settings.international_walk_in.store') }}" novalidate="novalidate">
                                        {{ csrf_field() }}
                                        <div style="width: 450px; float: left; margin-left: 20px;">
                                            <h4 class="form-section mb-2 text-center" style="text-align: left">Hub to Hub</h4>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Actual Weight</span>
                                                        </div>
                                                        <input type="text" name="walk_in_hub_ol_a" class="form-control numeric" required data-rule-required="true" data-msg-required="Actual Weight is required" value="{{ $walk_in_hub_ol->actual_weight }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">KG</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <label class="text-bold-600">Charges Per KG</label>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="input-group form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Charges</span>
                                                        </div>
                                                        <input type="text" name="walk_in_hub_ol_chargeable_weight_local" class="form-control local" placeholder="" required data-rule-required="true" data-msg-required="Local rate is required" value="{{ $walk_in_hub_ol->chargeable_weight_local }}">
                                                        {{--<div class="input-group-append">--}}
                                                        {{--<span class="input-group-text">%</span>--}}
                                                        {{--</div>--}}
                                                    </div>
                                                </div>
                                            </div>
                                            <label class="text-bold-600">Return Charges</label>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="input-group form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Charges</span>
                                                        </div>
                                                        <input type="text" name="walk_in_hub_ol_a_local"
                                                               class="form-control local" placeholder=""
                                                               required data-rule-required="true"
                                                               data-msg-required="Local rate is required"
                                                               value="{{ $walk_in_hub_ol->local }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div style="width: 450px; float: right; margin-left: 20px;">
                                            <h4 class="form-section mb-2 text-center" style="text-align: right">Doorstep</h4>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Actual Weight</span>
                                                        </div>
                                                        <input type="text" name="walk_in_door_ol_a" class="form-control numeric" placeholder="" required data-rule-required="true" data-msg-required="Actual Weight is required" value="{{ $walk_in_door_ol->actual_weight }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">KG</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <label class="text-bold-600">Charges Per KG</label>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="input-group form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Charges</span>
                                                        </div>
                                                        <input type="text" name="walk_in_door_ol_chargeable_weight_local" class="form-control local" placeholder="" required data-rule-required="true" data-msg-required="Local rate is required" value="{{ $walk_in_door_ol->chargeable_weight_local }}">
                                                        {{--<div class="input-group-append">--}}
                                                        {{--<span class="input-group-text">%</span>--}}
                                                        {{--</div>--}}
                                                    </div>
                                                </div>
                                            </div>
                                            <label class="text-bold-600">Return Charges</label>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="input-group form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Charges</span>
                                                        </div>
                                                        <input type="text" name="walk_in_door_ol_a_local"
                                                               class="form-control local" placeholder="" required
                                                               data-rule-required="true"
                                                               data-msg-required="Local rate is required"
                                                               value="{{ $walk_in_door_ol->local }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="input-group justify-content-center">
                                            <button type="submit" class="btn btn-primary" style="width: 200px">Update</button>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection
@section('js')
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('.numeric').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false
            });
            $('.local').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false
            });
            $('.national').inputmask({
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