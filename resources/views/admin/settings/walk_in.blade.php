@extends('admin.layout.master')

@section('title', 'Walk-In')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Walk-In
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.walk_in.store') }}" novalidate="novalidate">
                                        {{ csrf_field() }}
                                            <h4 class="input-group form-section mb-2 justify-content-center"><b>OverLand</b></h4>
                                                <div style="width: 450px; float: left; margin-left: 20px;">
                                                    <h4 class="form-section mb-2 text-center" style="text-align: left">Hub to Hub</h4>
                                                    <div class="input-group form-group">
                                                        <label class="form-control">Actual Weight</label>
                                                        <input type="text" name="walk_in_hub_ol_a" class="form-control walk_in_hub_ol_a" placeholder="" data-rule-range="[2,1000]" data-msg-range="Weight needs to be from 2 to 1000" data-rule-required="true" data-msg-required="Actual Weight is required" value="{{ $walk_in_hub_ol->actual_weight }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">KG</span>
                                                        </div>
                                                        <label class="form-control" style="margin-left: 10px">Charges Per Kg</label>
                                                    <input type="text" name="walk_in_hub_ol_c" class="form-control walk_in_hub_ol_c" placeholder="" data-rule-min="60" data-msg-min="Charges must be minimum 60" required data-rule-required="true" data-msg-required="Charges Per kg is required" value="{{ $walk_in_hub_ol->chargeable_weight }}">
                                                    </div>
                                                    <div class="input-group form-group">
                                                        <label class="form-control">Local</label>
                                                        <input type="text" name="walk_in_hub_ol_a_local" class="form-control walk_in_hub_ol_a" placeholder="" data-rule-required="true" data-msg-required="Local rate is required" value="{{ $walk_in_hub_ol->local }}">
                                                        <label class="form-control"  style="margin-left: 10px">National</label>
                                                        <input type="text" name="walk_in_hub_ol_c_national" class="form-control walk_in_hub_ol_c" placeholder="" required data-rule-required="true" data-msg-required="National Rate is required" value="{{ $walk_in_hub_ol->national }}">
                                                    </div>
                                                </div>
                                            <div style="width: 450px; float: right;">
                                                <h4 class="form-section mb-2 text-center" style="text-align: left">Doorstep</h4>
                                                <div class="input-group form-group">
                                                    <label class="form-control">Actual Weight</label>
                                                    <input type="text" name="walk_in_door_ol_a" class="form-control walk_in_door_ol_a" placeholder="" data-rule-range="[2,1000]" data-msg-range="Weight needs to be from 2 to 1000" required data-rule-required="true" data-msg-required="Actual Weight is required" value="{{ $walk_in_door_ol->actual_weight }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">KG</span>
                                                    </div>
                                                    <label class="form-control" style="margin-left: 40px">Charges Per Kg</label>
                                                    <input type="text" name="walk_in_door_ol_c" class="form-control walk_in_door_ol_c" placeholder="" data-rule-min="70" data-msg-min="Charges must be minimum 70" required data-rule-required="true" data-msg-required="Charges Per kg is required" value="{{ $walk_in_door_ol->chargeable_weight }}">
                                                </div>
                                                <div class="input-group form-group">
                                                    <label class="form-control">Local</label>
                                                    <input type="text" name="walk_in_door_ol_a_local" class="form-control walk_in_door_ol_a_local" placeholder="" data-rule-required="true" data-msg-required="Local rate is required" value="{{ $walk_in_door_ol->local }}">
                                                    <label class="form-control"  style="margin-left: 10px">National</label>
                                                    <input type="text" name="walk_in_door_ol_c_national" class="form-control walk_in_door_ol_c_national" placeholder="" required data-rule-required="true" data-msg-required="National Rate is required" value="{{ $walk_in_door_ol->national }}">
                                                </div>
                                            </div>

                                            <h4 class="input-group form-section mb-2 justify-content-center"><b>OverNight</b></h4>
                                            <div style="width: 450px; float: left; margin-left: 20px;">
                                                <h4 class="form-section mb-2 text-center" style="text-align: left">Hub to Hub</h4>
                                                <div class="input-group form-group">
                                                    <label class="form-control">Actual Weight</label>
                                                    <input type="text" name="walk_in_hub_on_a" class="form-control walk_in_hub_on_a" placeholder="" data-rule-range="[5,1000]" data-msg-range="Weight needs to be from 5 to 1000" required data-rule-required="true" data-msg-required="Actual Weight is required" value="{{ $walk_in_hub_on->actual_weight }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">KG</span>
                                                    </div>
                                                    <label class="form-control" style="margin-left: 40px">Charges Per Kg</label>
                                                    <input type="text" name="walk_in_hub_on_c" class="form-control walk_in_hub_on_c" placeholder="" data-rule-min="20" data-msg-min="Charges must be minimum 20" required data-rule-required="true" data-msg-required="Charges Per kg is required" value="{{ $walk_in_hub_on->chargeable_weight }}">
                                                </div>
                                                <div class="input-group form-group">
                                                    <label class="form-control">Local</label>
                                                    <input type="text" name="walk_in_hub_on_a_local" class="form-control walk_in_hub_on_a_local" placeholder="" data-rule-required="true" data-msg-required="Local rate is required" value="{{ $walk_in_hub_on->local }}">
                                                    <label class="form-control"  style="margin-left: 10px">National</label>
                                                    <input type="text" name="walk_in_hub_on_c_national" class="form-control walk_in_hub_on_c_national" placeholder="" required data-rule-required="true" data-msg-required="National Rate is required" value="{{ $walk_in_hub_on->national }}">
                                                </div>
                                            </div>
                                            <div style="width: 450px; float: right;">
                                                <h4 class="form-section mb-2 text-center" style="text-align: left">Doorstep</h4>
                                                <div class="input-group form-group">
                                                    <label class="form-control">Actual Weight</label>
                                                    <input type="text" name="walk_in_door_on_a" class="form-control walk_in_door_on_a" placeholder="" data-rule-range="[5,1000]" data-msg-range="Weight needs to be from 5 to 1000" required data-rule-required="true" data-msg-required="Actual Weight is required" value="{{ $walk_in_door_on->actual_weight }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">KG</span>
                                                    </div>
                                                    <label class="form-control" style="margin-left: 40px">Charges Per Kg</label>
                                                    <input type="text" name="walk_in_door_on_c" class="form-control walk_in_door_on_c" placeholder="" data-rule-min="30" data-msg-min="Charges must be minimum 30" required data-rule-required="true" data-msg-required="Charges Per kg is required" value="{{ $walk_in_door_on->chargeable_weight }}">
                                                </div>
                                                <div class="input-group form-group">
                                                    <label class="form-control">Local</label>
                                                    <input type="text" name="walk_in_door_on_a_local" class="form-control walk_in_door_on_a_local" placeholder="" data-rule-required="true" data-msg-required="Local rate is required" value="{{ $walk_in_door_on->local }}">
                                                    <label class="form-control"  style="margin-left: 10px">National</label>
                                                    <input type="text" name="walk_in_door_on_c_national" class="form-control walk_in_door_on_c_national" placeholder="" required data-rule-required="true" data-msg-required="National Rate is required" value="{{ $walk_in_door_on->national }}">
                                                </div>
                                            </div>

                                            <h4 class="input-group form-section mb-2 justify-content-center"><b>Detain</b></h4>
                                            <div style="width: 450px; float: left; margin-left: 20px;">
                                                <h4 class="form-section mb-2 text-center" style="text-align: left">Hub to Hub</h4>
                                                <div class="input-group form-group">
                                                    <label class="form-control">Actual Weight</label>
                                                    <input type="text" name="walk_in_hub_dn_a" class="form-control walk_in_hub_dn_a" placeholder="" data-rule-range="[10,1000]" data-msg-range="Weight needs to be from 10 to 1000" required data-rule-required="true" data-msg-required="Actual Weight is required" value="{{ $walk_in_hub_dn->actual_weight }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">KG</span>
                                                    </div>
                                                    <label class="form-control" style="margin-left: 40px">Charges Per Kg</label>
                                                    <input type="text" name="walk_in_hub_dn_c" class="form-control walk_in_hub_dn_c" placeholder="" data-rule-min="30" data-msg-min="Charges must be minimum 30" required data-rule-required="true" data-msg-required="Charges Per kg is required" value="{{ $walk_in_hub_dn->chargeable_weight }}">
                                                </div>
                                                <div class="input-group form-group">
                                                    <label class="form-control">Local</label>
                                                    <input type="text" name="walk_in_hub_dn_a_local" class="form-control walk_in_hub_dn_a_local" placeholder="" data-rule-required="true" data-msg-required="Local rate is required" value="{{ $walk_in_hub_dn->local }}">
                                                    <label class="form-control"  style="margin-left: 10px">National</label>
                                                    <input type="text" name="walk_in_hub_dn_c_national" class="form-control walk_in_hub_dn_c_national" placeholder="" required data-rule-required="true" data-msg-required="National Rate is required" value="{{ $walk_in_hub_dn->national }}">
                                                </div>
                                            </div>
                                            <div style="width: 450px; float: right;">
                                                <h4 class="form-section mb-2 text-center"  style="text-align: left">Doorstep</h4>
                                                <div class="input-group form-group">
                                                    <label class="form-control">Actual Weight</label>
                                                    <input type="text" name="walk_in_door_dn_a" class="form-control walk_in_door_ol_a" placeholder="" data-rule-range="[10,1000]" data-msg-range="Weight needs to be from 10 to 1000" required data-rule-required="true" data-msg-required="Actual Weight is required" value="{{ $walk_in_door_dn->actual_weight }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">KG</span>
                                                    </div>
                                                    <label class="form-control" style="margin-left: 40px">Charges Per Kg</label>
                                                    <input type="text" name="walk_in_door_dn_c" class="form-control walk_in_door_ol_c" placeholder="" data-rule-min="40" data-msg-min="Charges must be minimum 40" required data-rule-required="true" data-msg-required="Charges Per kg is required" value="{{ $walk_in_door_dn->chargeable_weight }}">
                                                </div>
                                                <div class="input-group form-group">
                                                    <label class="form-control">Local</label>
                                                    <input type="text" name="walk_in_door_dn_a_local" class="form-control walk_in_door_dn_a_local" placeholder="" data-rule-required="true" data-msg-required="Local rate is required" value="{{ $walk_in_door_dn->local }}">
                                                    <label class="form-control"  style="margin-left: 10px">National</label>
                                                    <input type="text" name="walk_in_door_dn_c_national" class="form-control walk_in_door_dn_c_national" placeholder="" required data-rule-required="true" data-msg-required="National Rate is required" value="{{ $walk_in_door_on->national }}">
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