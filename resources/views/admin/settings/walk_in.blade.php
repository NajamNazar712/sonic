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
                                    <form id="settings_form" class="form-horizontal" method="POST" action="{{ route('admin.settings.walk_in.store') }}" novalidate="novalidate">
                                        {{ csrf_field() }}
                                        <h4 class="input-group form-section mb-2 justify-content-center"><b>Overnight</b></h4>
                                        <div style="width: 450px; float: left; margin-left: 20px;">
                                            <h4 class="form-section mb-2 text-center" style="text-align: left">Hub to Hub</h4>
                                            <div class="row">
                                                <div class="col p-0">
                                                    <div class="input-group form-group">
                                                        <label class="form-control">Actual Weight</label>
                                                        <input type="text" name="walk_in_hub_on_a" class="form-control numeric" required data-rule-required="true" data-msg-required="Actual Weight is required" value="{{ $walk_in_hub_on->actual_weight }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">KG</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col p-0">
                                                    <div class="input-group form-group">
                                                        <label class="form-control" style="margin-left: 10px">Charges Per Kg</label>
                                                        <input type="text" name="walk_in_hub_on_c" class="form-control numeric" required data-rule-required="true" data-msg-required="Charges Per kg is required" value="{{ $walk_in_hub_on->chargeable_weight }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <label class="text-bold-600">Return Charges</label>
                                            <div class="row">
                                                <div class="col-12 p-0">
                                                    <div class="input-group form-group">
                                                        <label class="form-control">Local</label>
                                                        <input type="text" name="walk_in_hub_on_a_local" class="form-control local" placeholder="" required data-rule-required="true" data-msg-required="Local rate is required" value="{{ $walk_in_hub_on->local }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 p-0">
                                                    <div class="input-group form-group">
                                                        <label class="form-control">Class A</label>
                                                        <input type="text" name="walk_in_hub_on_return_class_0_charges" class="form-control national" placeholder="" required data-rule-required="true" data-msg-required="National Class A Rate is required" value="{{ $walk_in_hub_on->national_charges_class_0 }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 p-0">
                                                    <div class="input-group form-group">
                                                        <label class="form-control">Class B</label>
                                                        <input type="text" name="walk_in_hub_on_return_class_1_charges" class="form-control national" placeholder="" required data-rule-required="true" data-msg-required="National Class B Rate is required" value="{{ $walk_in_hub_on->national_charges_class_1 }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 p-0">
                                                    <div class="input-group form-group">
                                                        <label class="form-control">Class C</label>
                                                        <input type="text" name="walk_in_hub_on_return_class_2_charges" class="form-control national" placeholder="" required data-rule-required="true" data-msg-required="National Class B Rate is required" value="{{ $walk_in_hub_on->national_charges_class_2 }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 p-0">
                                                    <div class="input-group form-group">
                                                        <label class="form-control">Class D</label>
                                                        <input type="text" name="walk_in_hub_on_return_class_3_charges" class="form-control national" placeholder="" required data-rule-required="true" data-msg-required="National Class B Rate is required" value="{{ $walk_in_hub_on->national_charges_class_3 }}">
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
                                                <div class="col p-0">
                                                    <div class="input-group form-group">
                                                        <label class="form-control">Actual Weight</label>
                                                        <input type="text" name="walk_in_door_on_a" class="form-control numeric" required data-rule-required="true" data-msg-required="Actual Weight is required" value="{{ $walk_in_door_on->actual_weight }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">KG</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col p-0">
                                                    <div class="input-group form-group">
                                                        <label class="form-control" style="margin-left: 10px">Charges Per Kg</label>
                                                        <input type="text" name="walk_in_door_on_c" class="form-control numeric" required data-rule-required="true" data-msg-required="Charges Per kg is required" value="{{ $walk_in_door_on->chargeable_weight }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <label class="text-bold-600">Return Charges</label>
                                            <div class="row">
                                                <div class="col-12 p-0">
                                                    <div class="input-group form-group">
                                                        <label class="form-control">Local</label>
                                                        <input type="text" name="walk_in_door_on_a_local" class="form-control local" placeholder="" required data-rule-required="true" data-msg-required="Local rate is required" value="{{ $walk_in_door_on->local }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 p-0">
                                                    <div class="input-group form-group">
                                                        <label class="form-control">Class A</label>
                                                        <input type="text" name="walk_in_door_on_return_class_0_charges" class="form-control national" placeholder="" required data-rule-required="true" data-msg-required="National Class A Rate is required" value="{{ $walk_in_door_on->national_charges_class_0 }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 p-0">
                                                    <div class="input-group form-group">
                                                        <label class="form-control">Class B</label>
                                                        <input type="text" name="walk_in_door_on_return_class_1_charges" class="form-control national" placeholder="" required data-rule-required="true" data-msg-required="National Class B Rate is required" value="{{ $walk_in_door_on->national_charges_class_1 }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 p-0">
                                                    <div class="input-group form-group">
                                                        <label class="form-control">Class C</label>
                                                        <input type="text" name="walk_in_door_on_return_class_2_charges" class="form-control national" placeholder="" required data-rule-required="true" data-msg-required="National Class B Rate is required" value="{{ $walk_in_door_on->national_charges_class_2 }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 p-0">
                                                    <div class="input-group form-group">
                                                        <label class="form-control">Class D</label>
                                                        <input type="text" name="walk_in_door_on_return_class_3_charges" class="form-control national" placeholder="" required data-rule-required="true" data-msg-required="National Class B Rate is required" value="{{ $walk_in_door_on->national_charges_class_3 }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                            <h4 class="input-group form-section mb-2 justify-content-center"><b>Overland</b></h4>
                                                <div style="width: 450px; float: left; margin-left: 20px;">
                                                    <h4 class="form-section mb-2 text-center" style="text-align: left">Hub to Hub</h4>
                                                    <div class="row">
                                                        <div class="col p-0">
                                                            <div class="input-group form-group">
                                                                <label class="form-control">Actual Weight</label>
                                                                <input type="text" name="walk_in_hub_ol_a" class="form-control numeric" required data-rule-required="true" data-msg-required="Actual Weight is required" value="{{ $walk_in_hub_ol->actual_weight }}">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">KG</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col p-0">
                                                            <div class="input-group form-group">
                                                                <label class="form-control" style="margin-left: 10px">Charges Per Kg</label>
                                                                <input type="text" name="walk_in_hub_ol_c" class="form-control numeric" placeholder="" required data-rule-required="true" data-msg-required="Charges Per kg is required" value="{{ $walk_in_hub_ol->chargeable_weight }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <label class="text-bold-600">Return Charges</label>
                                                    <div class="row">
                                                        <div class="col-12 p-0">
                                                            <div class="input-group form-group">
                                                                <label class="form-control">Local</label>
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

                                                    <div class="col-6 p-0">
                                                        <div class="input-group form-group">
                                                            <label class="form-control">Class A</label>
                                                            <input type="text" name="walk_in_hub_ol_return_class_0_charges" class="form-control national" placeholder="" required data-rule-required="true" data-msg-required="National Class A Rate is required" value="{{ $walk_in_hub_ol->national_charges_class_0 }}">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 p-0">
                                                        <div class="input-group form-group">
                                                            <label class="form-control">Class B</label>
                                                            <input type="text" name="walk_in_hub_ol_return_class_1_charges" class="form-control national" placeholder="" required data-rule-required="true" data-msg-required="National Class B Rate is required" value="{{ $walk_in_hub_ol->national_charges_class_1 }}">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 p-0">
                                                        <div class="input-group form-group">
                                                            <label class="form-control">Class C</label>
                                                            <input type="text" name="walk_in_hub_ol_return_class_2_charges" class="form-control national" placeholder="" required data-rule-required="true" data-msg-required="National Class B Rate is required" value="{{ $walk_in_hub_ol->national_charges_class_2 }}">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 p-0">
                                                        <div class="input-group form-group">
                                                            <label class="form-control">Class D</label>
                                                            <input type="text" name="walk_in_hub_ol_return_class_3_charges" class="form-control national" placeholder="" required data-rule-required="true" data-msg-required="National Class B Rate is required" value="{{ $walk_in_hub_ol->national_charges_class_3 }}">
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
                                                <div class="col p-0">
                                                    <div class="input-group form-group">
                                                            <label class="form-control">Actual Weight</label>
                                                            <input type="text" name="walk_in_door_ol_a" class="form-control numeric" placeholder="" required data-rule-required="true" data-msg-required="Actual Weight is required" value="{{ $walk_in_door_ol->actual_weight }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">KG</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col p-0">
                                                    <div class="input-group form-group">
                                                        <label class="form-control" style="margin-left: 10px">Charges Per Kg</label>
                                                                <input type="text" name="walk_in_door_ol_c" class="form-control numeric" placeholder="" required data-rule-required="true" data-msg-required="Charges Per kg is required" value="{{ $walk_in_door_ol->chargeable_weight }}">
                                                            </div>
                                                </div>
                                            </div>
                                                <label class="text-bold-600">Return Charges</label>
                                            <div class="row">
                                                <div class="col-12 p-0">
                                                    <div class="input-group form-group">
                                                        <label class="form-control">Local</label>
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
                                                <div class="col-6 p-0">
                                                    <div class="input-group form-group">
                                                        <label class="form-control">Class A</label>
                                                        <input type="text" name="walk_in_door_ol_return_class_0_charges" class="form-control national" placeholder="" required data-rule-required="true" data-msg-required="National Class A Rate is required" value="{{ $walk_in_door_ol->national_charges_class_0 }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 p-0">
                                                    <div class="input-group form-group">
                                                        <label class="form-control">Class B</label>
                                                        <input type="text" name="walk_in_door_ol_return_class_1_charges" class="form-control national" placeholder="" required data-rule-required="true" data-msg-required="National Class B Rate is required" value="{{ $walk_in_door_ol->national_charges_class_1 }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 p-0">
                                                    <div class="input-group form-group">
                                                        <label class="form-control">Class C</label>
                                                        <input type="text" name="walk_in_door_ol_return_class_2_charges" class="form-control national" placeholder="" required data-rule-required="true" data-msg-required="National Class B Rate is required" value="{{ $walk_in_door_ol->national_charges_class_2 }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 p-0">
                                                    <div class="input-group form-group">
                                                        <label class="form-control">Class D</label>
                                                        <input type="text" name="walk_in_door_ol_return_class_3_charges" class="form-control national" placeholder="" required data-rule-required="true" data-msg-required="National Class B Rate is required" value="{{ $walk_in_door_ol->national_charges_class_3 }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            </div>

                                            <h4 class="input-group form-section mb-2 justify-content-center"><b>Detain</b></h4>
                                            <div style="width: 450px; float: left; margin-left: 20px;">
                                                <h4 class="form-section mb-2 text-center" style="text-align: left">Hub to Hub</h4>
                                                <div class="row">
                                                    <div class="col p-0">
                                                        <div class="input-group form-group">
                                                    <label class="form-control">Actual Weight</label>
                                                    <input type="text" name="walk_in_hub_dn_a" class="form-control numeric" required data-rule-required="true" data-msg-required="Actual Weight is required" value="{{ $walk_in_hub_dn->actual_weight }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">KG</span>
                                                    </div>
                                                </div>
                                                    </div>
                                                    <div class="col p-0">
                                                        <div class="input-group form-group">
                                                            <label class="form-control" style="margin-left: 10px">Charges Per Kg</label>
                                                            <input type="text" name="walk_in_hub_dn_c" class="form-control numeric" placeholder="" required data-rule-required="true" data-msg-required="Charges Per kg is required" value="{{ $walk_in_hub_dn->chargeable_weight }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <label class="text-bold-600">Return Charges</label>
                                                <div class="row">
                                                    <div class="col-12 p-0">
                                                        <div class="input-group form-group">
                                                            <label class="form-control">Local</label>
                                                            <input type="text" name="walk_in_hub_dn_a_local" class="form-control local" placeholder="" required data-rule-required="true" data-msg-required="Local rate is required" value="{{ $walk_in_hub_dn->local }}">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                        <div class="col-6 p-0">
                                                            <div class="input-group form-group">
                                                                <label class="form-control">Class A</label>
                                                                <input type="text" name="walk_in_hub_dn_return_class_0_charges" class="form-control national" placeholder="" required data-rule-required="true" data-msg-required="National Class A Rate is required" value="{{ $walk_in_hub_dn->national_charges_class_0 }}">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">%</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6 p-0">
                                                            <div class="input-group form-group">
                                                                <label class="form-control">Class B</label>
                                                                <input type="text" name="walk_in_hub_dn_return_class_1_charges" class="form-control national" placeholder="" required data-rule-required="true" data-msg-required="National Class B Rate is required" value="{{ $walk_in_hub_dn->national_charges_class_1 }}">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">%</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6 p-0">
                                                            <div class="input-group form-group">
                                                                <label class="form-control">Class C</label>
                                                                <input type="text" name="walk_in_hub_dn_return_class_2_charges" class="form-control national" placeholder="" required data-rule-required="true" data-msg-required="National Class B Rate is required" value="{{ $walk_in_hub_dn->national_charges_class_2 }}">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">%</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6 p-0">
                                                            <div class="input-group form-group">
                                                                <label class="form-control">Class D</label>
                                                                <input type="text" name="walk_in_hub_dn_return_class_3_charges" class="form-control national" placeholder="" required data-rule-required="true" data-msg-required="National Class B Rate is required" value="{{ $walk_in_hub_dn->national_charges_class_3 }}">
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
                                                    <div class="col p-0">
                                                        <div class="input-group form-group">
                                                    <label class="form-control">Actual Weight</label>
                                                    <input type="text" name="walk_in_door_dn_a" class="form-control numeric" placeholder="" required data-rule-required="true" data-msg-required="Actual Weight is required" value="{{ $walk_in_door_dn->actual_weight }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">KG</span>
                                                    </div>
                                                        </div>
                                                    </div>
                                                        <div class="col p-0">
                                                            <div class="input-group form-group">
                                                        <label class="form-control" style="margin-left: 10px">Charges Per Kg</label>
                                                        <input type="text" name="walk_in_door_dn_c" class="form-control numeric" placeholder="" required data-rule-required="true" data-msg-required="Charges Per kg is required" value="{{ $walk_in_door_dn->chargeable_weight }}">
                                                        </div>
                                                            </div>
                                                </div>
                                                <label class="text-bold-600">Return Charges</label>
                                                <div class="row">
                                                    <div class="col-12 p-0">
                                                <div class="input-group form-group">
                                                    <label class="form-control">Local</label>
                                                    <input type="text" name="walk_in_door_dn_a_local" class="form-control local" placeholder="" required data-rule-required="true" data-msg-required="Local rate is required" value="{{ $walk_in_door_dn->local }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                                    <div class="col-6 p-0">
                                                        <div class="input-group form-group">
                                                            <label class="form-control">Class A</label>
                                                            <input type="text" name="walk_in_door_dn_return_class_0_charges" class="form-control national" placeholder="" required data-rule-required="true" data-msg-required="National Class A Rate is required" value="{{ $walk_in_door_dn->national_charges_class_0 }}">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 p-0">
                                                        <div class="input-group form-group">
                                                            <label class="form-control">Class B</label>
                                                            <input type="text" name="walk_in_door_dn_return_class_1_charges" class="form-control national" placeholder="" required data-rule-required="true" data-msg-required="National Class B Rate is required" value="{{ $walk_in_door_dn->national_charges_class_1 }}">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 p-0">
                                                        <div class="input-group form-group">
                                                            <label class="form-control">Class C</label>
                                                            <input type="text" name="walk_in_door_dn_return_class_2_charges" class="form-control national" placeholder="" required data-rule-required="true" data-msg-required="National Class B Rate is required" value="{{ $walk_in_door_dn->national_charges_class_2 }}">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 p-0">
                                                        <div class="input-group form-group">
                                                            <label class="form-control">Class D</label>
                                                            <input type="text" name="walk_in_door_dn_return_class_3_charges" class="form-control national" placeholder="" required data-rule-required="true" data-msg-required="National Class B Rate is required" value="{{ $walk_in_door_dn->national_charges_class_3 }}">
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