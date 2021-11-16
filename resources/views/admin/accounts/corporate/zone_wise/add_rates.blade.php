@extends('admin.layout.master')

@section('title', 'Add Rates')

@section('content')
    <h1>Add Rates</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <h2 class="font-large-1">{{$shipper->name}}
                            <div class="badge badge-success pull-right">Corporate Invoicing Account</div>
                        </h2>
                        @include('admin.inc.messages')
                    </div>


                    <div class="card-content">
                        <form id="ratesAdditionForm" class="card-body card-dashboard" action="#" method="post" novalidate="novalidate">
                            @csrf

                            <div class="card">
                                <input type="hidden" id="corporate_type_id" value="{{$corporate_rate_type_id}}" name="corporate_rate_type_id">

                            </div>
                            <div id="" class="card-header border-success">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3 class="display-inline card-title lead success">Overnight</h3>
                                        @if($sale_person['admin_id'] == Auth::id() || session('role_id') == 1)
                                            <label class="display-inline ml-1">Make Default</label>
                                            <input type="checkbox" name="on_default" id="on_default" class="switchery on_default" data-size="xs" data-switchery="true">
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <a href="javascript:void(0);" class="pull-right" id="on_main_switch"><input name="on_main_switch" type="checkbox"  class="switchery on-main-switch" data-size="sm" /></a>
                                    </div>
                                </div>
                            </div>
                            <div id="overnight" class="card border-success hide"
                                 aria-expanded="true">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-2">
                                                <h3>Add Origin Cities</h3>
                                            </div>
                                            <div class="col-8">
                                                <div class="form-group card border-success p-2">
                                                    <select name="on_origin_hubs[]" id="on_origin_hubs" class="form-control select2" multiple="multiple">
                                                        @foreach($cities as $city)
                                                            <option value="{{$city->id}}">{{$city->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-2">
                                                <h3>Add Destination Cities</h3>
                                            </div>
                                            <div class="col-8">
                                                <div class="form-group card border-success p-2">
                                                    <select name="on_destination_hubs[]" id="on_destination_hubs" class="form-control select2" multiple="multiple">
                                                        @foreach($cities as $city)
                                                            <option value="{{$city->id}}">{{$city->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="weight-addition-overnight">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Doorstep Delivery</h3>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <fieldset class="form-group">
                                                        @foreach($min_weight[1] as $mweight)
                                                            @if($mweight->delivery_type_id == 1)
                                                        <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="{{$mweight->min_chargeable_weight}}" name="on_door_mcw_charges" placeholder="Minimum Chargeable Weight">
                                                            @endif
                                                            @endforeach
                                                    </fieldset>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col text-center">
                                                    <label class="card-title">Range Up</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Range Down</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Base</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (Local)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (Same-Zone)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (Different-Zone)</label>
                                                </div>

                                                <div class="col-1"></div>
                                            </div>

                                            @php
                                                $index_row = 1;
                                            @endphp
                                            @foreach($weight[1] as $index => $onweight)
                                                @if($onweight->delivery_type_id == 1)

                                                <div class="row on_door_weight_row" id="">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" id="on_door_range_up{{$index_row}}" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->range_up}}" name="on_door_range_up[{{$index_row}}]" {{ (($index_row == 0 || $index_row == 1) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" id="on_door_range_down{{$index_row}}" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->range_down}}" name="on_door_range_down[{{$index_row}}]" {{ (($index_row == 0) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="OvernightDoorSwitch{{$index_row}}" class="switchery weightAdditionDoorOvernight" data-color="success" data-size="sm" name="on_door_wa_switch[{{$index_row}}]"/>
                                                        </div>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->local}}" name="on_door_local_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->same_zone}}" name="on_door_same_zone_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->different_zone}}" name="on_door_different_zone_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-1">
                                                        @if($index_row>1)
                                                            <span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 on_weight_close"><i class="ft-x"></i></span>
                                                        @endif
                                                    </div>

                                                </div>
                                                    @php
                                                        $index_row++;
                                                    @endphp
                                                @endif
                                            @endforeach

                                        </div>
                                        <div class="mb-2">
                                            <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="waddition_btn"><i class="la la-plus"></i></button>
                                        </div>
                                        <!--hub-to-hub-start-->
                                        <div class="hub-weight-addition-overnight">
                                                <div class="row">
                                                    <div class="col-2">
                                                        <h3 class="card-title">Hub-Hub Delivery</h3>
                                                    </div>

                                                    <div class="col-2">
                                                        <a href="javascript:void(0);" class="pull-right" id="on_hub_to_hub_switch"><input name="on_hub_to_hub_switch" type="checkbox"  class="switchery on_hub_to_hub_switch" data-size="sm" checked/></a>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <fieldset class="form-group">
                                                            @foreach($min_weight[1] as $mweight)
                                                                @if($mweight->delivery_type_id == 2)
                                                                    <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="{{$mweight->min_chargeable_weight}}" name="on_hub_mcw_charges" placeholder="Minimum Chargeable Weight">
                                                                @endif
                                                            @endforeach
                                                        </fieldset>
                                                    </div>

                                                </div>
                                                <div class="row">
                                                    <div class="col text-center">
                                                        <label class="card-title">Range Up</label>
                                                    </div>
                                                    <div class="col text-center">
                                                        <label class="card-title">Range Down</label>
                                                    </div>
                                                    <div class="col text-center">
                                                        <label class="card-title">Base</label>
                                                    </div>
                                                    <div class="col text-center">
                                                        <label class="card-title">Flat Charges/KG (Local)</label>
                                                    </div>
                                                    <div class="col text-center">
                                                        <label class="card-title">Flat Charges/KG (Same-Zone)</label>
                                                    </div>
                                                    <div class="col text-center">
                                                        <label class="card-title">Flat Charges/KG (Different-Zone)</label>
                                                    </div>
                                                    <div class="col-1"></div>
                                                </div>
                                                @php
                                                    $index_row = 1;
                                                @endphp
                                                @foreach($weight[1] as $index => $onweight)
                                                    @if($onweight->delivery_type_id == 2)
                                                        <div class="row on_hub_weight_row">
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" id="on_hub_range_up{{$index_row}}" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->range_up}}" name="on_hub_range_up[{{$index_row}}]" {{ (($index_row == 0 || $index_row == 1) ? 'disabled' : '') }}>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">

                                                                <fieldset class="form-group">
                                                                    <input type="text" id="on_hub_range_down{{$index_row}}" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->range_down}}" name="on_hub_range_down[{{$index_row}}]" {{ (($index_row == 0) ? 'disabled' : '') }}>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">

                                                                <div class="form-group " style="padding-top: 8px;">
                                                                    <input type="checkbox" id="OvernightHubSwitch{{$index_row}}" class="switchery weightAdditionHubOvernight" data-color="success" data-size="sm" name="on_hub_wa_switch[{{$index_row}}]"/>
                                                                </div>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->local}}" name="on_hub_local_charges[{{$index_row}}]">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->same_zone}}" name="on_hub_same_zone_charges[{{$index_row}}]">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->different_zone}}" name="on_hub_different_zone_charges[{{$index_row}}]">
                                                                </fieldset>
                                                            </div>

                                                            <div class="col-1">
                                                                @if($index_row>1)
                                                                    <span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 on_weight_close"><i class="ft-x"></i></span>
                                                                @endif
                                                            </div>

                                                        </div>
                                                        @php
                                                            $index_row++;
                                                        @endphp
                                                    @endif
                                                @endforeach


                                            </div>

                                        <div>
                                            <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="hub_waddition_btn"><i class="la la-plus"></i></button>
                                        </div>
                                        <!--hub-to-hub-end-->

                                        <div class="row mt-2">

                                            <div class="col-md-3 text-center">
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" >Replacement</span>
                                                        </div>
                                                        <input type="text" class="form-control percent" data-rule-required="true" data-msg-required="This field is required" value="{{$shippingType[1][0]->replacement_charges}}" name="on_replacement_charges">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" >%</span>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" >Try &amp; Buy</span>
                                                        </div>
                                                        <input type="text" class="form-control percent" data-rule-required="true" data-msg-required="This field is required" value="{{$shippingType[1][0]->try_and_buy_charges}}" name="on_tnb_charges">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" >%</span>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col-md-6 text-center">
                                                <div class="row">
                                                   <div class="col-3 mt-1" >
                                                        <fieldset>
                                                            <div class="input-group form-group">
                                                                <label class="card-title">DWS Weight </label>
                                                                <input type="checkbox" name="on_dws" id="on_dws" class="switchery on_dws" data-size="xs" data-switchery="true">
                                                            </div>
                                                        </fieldset>
                                                    </div> 
                                                    <div class="col-4">
                                                        <fieldset>
                                                            <div class="input-group form-group">
                                                                <select name="on_dws_weight" id="on_dws_weight" class="form-control" disabled>
                                                                        <option value="1">High</option>
                                                                        <option value="0">Low</option>
                                                                </select>
                                                            </div>
                                                        </fieldset>
                                                    </div> 
                                                </div>
                                                
                                            </div>
                                            <div class="col-md-6 text-center">
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <h3>DWS Weight</h3>
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" >Try &amp; Buy</span>
                                                        </div>
                                                        <input type="text" class="form-control percent" data-rule-required="true" data-msg-required="This field is required" value="{{$shippingType[1][0]->try_and_buy_charges}}" name="on_tnb_charges">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" >%</span>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Cash Handling Charges</h3>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="on_cash_handling_switch"  class="switchery cashChargesOvernight" data-color="success" data-size="sm" checked/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Range Up</label>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Range Down</label>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Charges</label>
                                            </div>
                                        </div>

                                        <div class="cash-handling-div-overnight slabs">
                                            @foreach($cashHandling[1] as $index => $cash)
                                                <div class="row">
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="on_cash_range_up[{{$index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric"  value="{{$cash->range_up}}">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="on_cash_range_down[{{$index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric"  value="{{$cash->range_down}}">
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="on_cash_charges[{{$index}}]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->charges}}">
                                                        </fieldset>
                                                    </div>
                                                    @if($index>0)
                                                        <div class="col">
                                                            <span class="btn btn-danger rounded btn-sm-width mr-1 mb-1 on_weight_close"><i class="ft-x"></i></span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="cash-handling-btn-overnight">
                                            <button id="addMoreSlabs" type="button" class="btn btn-outline-success mr-1" title="Add more slabs" ><i class="la la-plus"></i></button>
                                        </div>
                                        <hr>

                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Insurance Charges</h3>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="on_insurance_charges_switch" class="switchery insuranceChargesOvernight" data-color="success" data-size="sm" checked/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Range Up</label>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Range Down</label>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Charges</label>
                                            </div>
                                        </div>
                                        <div class="insurance-charges-div-overnight slabs">
                                            @foreach($insuranceCharges[1] as $index => $insurance)
                                                <div class="row" id="on_insurance_handle_0">
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="on_ins_range_up[{{$index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric"  value="{{$insurance->range_up}}">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="on_ins_range_down[{{$index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric"  value="{{$insurance->range_down}}">
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="on_ins_charges[{{$index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control dec-percent"  value="{{$insurance->charges}}">
                                                        </fieldset>
                                                    </div>
                                                    @if($index>0)
                                                        <div class="col">
                                                            <span class="btn btn-danger rounded btn-sm-width mr-1 mb-1 on_weight_close"><i class="ft-x"></i></span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="insurance-charges-btn-overnight">
                                            <button id="addMoreSlabsInsurance" type="button" class="btn btn-outline-success mr-1" title="Add more slabs"><i class="la la-plus"></i></button>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Return Charges</h3>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="on_return_switch" class="switchery returnChargesOvernight" data-color="success" data-size="sm" checked/>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row return-charges-div-overnight justify-content-center">

                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Local Charges</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control amount" name="on_return_local_charges"  value="{{$returnCharges[1][0]->local}}">
                                                </fieldset>
                                            </div>

                                            <div class="col text-center">
                                                <label class="card-title">Same Zone</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent" name="on_return_same_zone_charges"  value="{{$returnCharges[1][0]->same_zone}}">
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <label class="card-title">Different Zone</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent" name="on_return_different_zone_charges"  value="{{$returnCharges[1][0]->different_zone}}">
                                                </fieldset>
                                            </div>
                                        </div>

                                        <hr>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Fuel Surcharge</h3>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="overnight_fuel_switch" class="switchery fuelSurchargeOvernight" data-color="success" data-size="sm" checked/>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row fuel-surcharge-div-overnight">

                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Charges</label>
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <input type="text"  class="form-control " name="overnight_fuel_surcharge" data-rule-required="true" data-msg-required="This field is required" value="{{$fuelCharges[1][0]->fuel_surcharge}}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>

                                        </div>

                                        <hr>
                                        <div class="">
                                            <h3 class="card-title">Discount Rates</h3>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-md-6">
                                                <label class="">Title</label>
                                                <div class='form-group'>
                                                    <input type='text' class="form-control" data-rule-required="true" data-msg-required="This field is required" disabled name="on_discount_title"/>
                                                </div>

                                            </div>
                                            <div class="col-md-6">
                                                <label class="">Apply [to - from]</label>
                                                <div class='input-group form-group'>
                                                    <input type='text' class="form-control daterange" data-rule-required="true" data-msg-required="This field is required" disabled name="on_daterange"/>
                                                    <div class="input-group-append">
                                                            <span class="input-group-text">
                                                              <span class="la la-calendar"></span>
                                                            </span>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Weight</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox" id="" class="switchery discountSwitchesOvernight" name="on_discount_weight_switch" data-size="xs" />
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent on-discount-inp" name="on_discount_weight_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Cash</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="">
                                                                <input type="checkbox" name="on_discount_cash_switch" class="switchery discountSwitchesOvernight" data-size="xs" />
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent on-discount-inp" name="on_discount_cash_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Insurance</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="">
                                                                <input type="checkbox" name="on_discount_insurance_switch" class="switchery discountSwitchesOvernight" data-size="xs" />
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent on-discount-inp" name="on_discount_insurance_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Return</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox"  class="switchery discountSwitchesOvernight" data-size="xs" name="on_discount_return_switch"/>
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent on-discount-inp" name="on_discount_return_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>


                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div id="" class="card-header mt-1 border-success">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3 class="display-inline card-title lead success">Overland</h3>
                                        @if($sale_person['admin_id'] == Auth::id() || session('role_id') == 1)
                                            <label class="display-inline ml-1">Make Default</label>
                                            <input type="checkbox" name="ol_default" id="ol_default" class="switchery ol_default" data-size="xs" data-switchery="true">
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <a id="ol_main_switch" href="javascript:void(0);" class="pull-right"><input name="ol_main_switch" type="checkbox" id="" class="switchery ol-main-switch" data-size="sm"/></a>
                                    </div>
                                </div>

                            </div>
                            <div id="overland" class="border-success no-border-top card hide" aria-expanded="false">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-2">
                                                <h3>Add Origin Cities</h3>
                                            </div>
                                            <div class="col-8">
                                                <div class="form-group card border-success p-2">
                                                    <select name="ol_origin_hubs[]" id="ol_origin_hubs" class="form-control select2" multiple="multiple">
                                                        @foreach($cities as $city)
                                                            <option value="{{$city->id}}">{{$city->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-2">
                                                <h3>Add Destination Cities</h3>
                                            </div>
                                            <div class="col-8">
                                                <div class="form-group card border-success p-2">
                                                    <select name="ol_destination_hubs[]" id="ol_destination_hubs" class="form-control select2" multiple="multiple">
                                                        @foreach($cities as $city)
                                                            <option value="{{$city->id}}">{{$city->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="weight-addition-overland">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Doorstep Delivery</h3>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <fieldset class="form-group">
                                                        @foreach($min_weight[2] as $mweight)
                                                            @if($mweight->delivery_type_id == 1)
                                                                <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="{{$mweight->min_chargeable_weight}}" name="ol_door_mcw_charges" placeholder="Minimum Chargeable Weight">
                                                            @endif
                                                        @endforeach
                                                    </fieldset>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col text-center">
                                                    <label class="card-title">Range Up</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Range Down</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Base</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (Local)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (Same-Zone)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (Different-Zone)</label>
                                                </div>

                                                <div class="col-1"></div>
                                            </div>
                                            @php
                                                $index_row = 1;
                                            @endphp
                                            @foreach($weight[2] as $index => $olweight)
                                                @if($olweight->delivery_type_id == 1)
                                                <div class="row ol_door_weight_row">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" id="ol_door_range_up{{$index_row}}" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->range_up}}" name="ol_door_range_up[{{$index_row}}]" {{ (($index_row == 0 || $index_row == 1) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" id="ol_door_range_down{{$index_row}}" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->range_down}}" name="ol_door_range_down[{{$index_row}}]" {{ (($index_row == 0) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="OverlandDoorSwitch{{$index_row}}" class="switchery weightAdditionDoorOverland" data-color="success" data-size="sm" name="ol_door_wa_switch[{{$index_row}}]"/>
                                                        </div>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->local}}" name="ol_door_local_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->same_zone}}" name="ol_door_same_zone_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div> <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->different_zone}}" name="ol_door_different_zone_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-1">
                                                        @if($index_row>1)
                                                            <span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 ol_weight_close"><i class="ft-x"></i></span>
                                                        @endif
                                                    </div>
                                                </div>{{--Row--}}
                                                    @php
                                                        $index_row++;
                                                    @endphp
                                                @endif
                                            @endforeach
                                        </div>{{--weight addition div--}}
                                        <div class="mb-2">
                                            <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="overland_door_weightadd"><i class="la la-plus"></i></button>
                                        </div>
                                        <div class="hub-weight-addition-overland">
                                            <div class="row">
                                                <div class="col-2">
                                                    <h3 class="card-title">Hub-Hub Delivery</h3>
                                                </div>
                                                <div class="col-2">
                                                    <a href="javascript:void(0);" class="pull-right" id="ol_hub_to_hub_switch"><input name="ol_hub_to_hub_switch" type="checkbox"  class="switchery ol_hub_to_hub_switch" data-size="sm" checked/></a>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <fieldset class="form-group">
                                                        @foreach($min_weight[2] as $mweight)
                                                            @if($mweight->delivery_type_id == 1)
                                                                <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="{{$mweight->min_chargeable_weight}}" name="ol_hub_mcw_charges" placeholder="Minimum Chargeable Weight">
                                                            @endif
                                                        @endforeach
                                                    </fieldset>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col text-center">
                                                    <label class="card-title">Range Up</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Range Down</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Base</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (Local)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (Same-Zone)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (Different-Zone)</label>
                                                </div>

                                                <div class="col-1"></div>
                                            </div>
                                            @php
                                                $index_row = 1;
                                            @endphp
                                            @foreach($weight[2] as $index => $olweight)
                                                @if($olweight->delivery_type_id == 2)
                                                <div class="row ol_hub_weight_row">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" id="ol_hub_range_up{{$index_row}}" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->range_up}}" name="ol_hub_range_up[{{$index_row}}]" {{ (($index_row == 0 || $index_row == 1) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" id="ol_hub_range_down{{$index_row}}" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->range_down}}" name="ol_hub_range_down[{{$index_row}}]" {{ (($index_row == 0) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="OverlandHubSwitch{{$index_row}}" class="switchery weightAdditionHubOverland" data-color="success" data-size="sm" name="ol_hub_wa_switch[{{$index_row}}]"/>
                                                        </div>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->local}}" name="ol_hub_local_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->same_zone}}" name="ol_hub_same_zone_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div> <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->different_zone}}" name="ol_hub_different_zone_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-1">
                                                        @if($index_row>1)
                                                            <span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 ol_weight_close"><i class="ft-x"></i></span>
                                                        @endif
                                                    </div>
                                                </div>{{--Row--}}
                                                    @php
                                                        $index_row++;
                                                    @endphp
                                                @endif
                                            @endforeach

                                        </div>{{--weight addition div--}}
                                        <div>
                                            <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="overland_hub_weightadd"><i class="la la-plus"></i></button>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-3 text-center">
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Replacement</span>
                                                        </div>
                                                        <input type="text" class="form-control percent" data-rule-required="true" data-msg-required="This field is required" name="ol_replacement_charges" value="{{$shippingType[2][0]->replacement_charges}}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Try &amp; Buy</span>
                                                        </div>
                                                        <input type="text" class="form-control percent" data-rule-required="true" data-msg-required="This field is required" name="ol_tnb_charges" value="{{$shippingType[2][0]->try_and_buy_charges}}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col-md-6 text-center">
                                                <div class="row">
                                                   <div class="col-3 mt-1" >
                                                        <fieldset>
                                                            <div class="input-group form-group">
                                                                <label class="card-title">DWS Weight </label>
                                                                <input type="checkbox" name="ol_dws" id="ol_dws" class="switchery ol_dws" data-size="xs" data-switchery="true">
                                                            </div>
                                                        </fieldset>
                                                    </div> 
                                                    <div class="col-4">
                                                        <fieldset>
                                                            <div class="input-group form-group">
                                                                <select name="ol_dws_weight" id="ol_dws_weight" class="form-control" disabled>
                                                                        <option value="1">High</option>
                                                                        <option value="0">Low</option>
                                                                </select>
                                                            </div>
                                                        </fieldset>
                                                    </div> 
                                                </div>
                                                
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Cash Handling Charges</h3>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="ol_cash_handling_switch" class="switchery cashChargesOverland" data-color="success" data-size="sm" checked/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Range Up</label>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Range Down</label>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Charges</label>
                                            </div>
                                        </div>

                                        <div class="cash-handling-div-overland slabs">
                                            @foreach($cashHandling[2] as $index => $cash)
                                                <div class="row" id="ol_cash_handle_0">
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="ol_cash_range_up[{{$index}}]" type="text" class="form-control  numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->range_up}}">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="ol_cash_range_down[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->range_down}}">
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="ol_cash_charges[{{$index}}]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->charges}}">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        @if($index>0)
                                                            <span class="ol_row_delete btn btn-danger rounded btn-sm-width mr-1 mb-1"><i class="ft-x"></i></span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="cash-handling-btn-overland">
                                            <button id="overlandaddMoreSlabs" type="button" class="btn btn-outline-success mr-1" title="Add more slabs" ><i class="la la-plus"></i></button>
                                        </div>
                                        <hr>

                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Insurance Charges</h3>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="ol_insurance_charges_switch" class="switchery insuranceChargesoverland" data-color="success" data-size="sm" checked/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Range Up</label>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Range Down</label>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Charges</label>
                                            </div>
                                        </div>
                                        <div class="insurance-charges-div-overland slabs">
                                            @foreach($insuranceCharges[2] as $index => $ol_insurance)
                                                <div class="row">
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="ol_ins_range_up[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$ol_insurance->range_up}}">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="ol_ins_range_down[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$ol_insurance->range_down}}">
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="ol_ins_charges[{{$index}}]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$ol_insurance->charges}}">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        @if($index>0)
                                                            <span class="btn btn-danger rounded btn-sm-width mr-1 mb-1 ol_row_delete"><i class="ft-x"></i></span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="insurance-charges-btn-overland">
                                            <button id="oladdMoreSlabsInsurance" type="button" class="btn btn-outline-success mr-1" title="Add more slabs"><i class="la la-plus"></i></button>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Return Charges</h3>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="ol_return_switch" class="switchery returnChargesOverland" data-color="success" data-size="sm" checked/>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row return-charges-div-overland justify-content-center">

                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Local Charges</label>
                                                <fieldset class="form-group">
                                                    <input type="text" class="form-control amount" name="ol_return_local_charges" data-rule-required="true" data-msg-required="This field is required" value="{{$returnCharges[2][0]->local}}">
                                                </fieldset>
                                            </div>

                                            <div class="col text-center">
                                                <label class="card-title">Same Zone</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent" name="ol_return_same_zone_charges"  value="{{$returnCharges[2][0]->same_zone}}">
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <label class="card-title">Different Zone</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent" name="ol_return_different_zone_charges"  value="{{$returnCharges[2][0]->different_zone}}">
                                                </fieldset>
                                            </div>

                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Fuel Surcharge</h3>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="overland_fuel_switch" class="switchery fuelSurchargeOverland" data-color="success" data-size="sm" checked/>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row fuel-surcharge-div-overland">

                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Charges</label>
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <input type="text"  class="form-control " name="overland_fuel_surcharge" data-rule-required="true" data-msg-required="This field is required" value="{{$fuelCharges[2][0]->fuel_surcharge}}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>

                                        </div>

                                        <hr>
                                        <div class="">
                                            <h3 class="card-title">Discount Rates</h3>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-md-6">
                                                <label class="">Title</label>
                                                <div class='form-group'>
                                                    <input type='text' class="form-control" data-rule-required="true" data-msg-required="This field is required" disabled name="ol_discount_title"/>
                                                </div>

                                            </div>
                                            <div class="col-md-6">
                                                <label class="">Apply [to - from]</label>
                                                <div class='input-group form-group'>
                                                    <input type='text' class="form-control daterange" data-rule-required="true" data-msg-required="This field is required" disabled name="ol_daterange"/>
                                                    <div class="input-group-append">
                                                            <span class="input-group-text">
                                                              <span class="la la-calendar"></span>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Weight</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox" id="" class="switchery discountSwitchesOverland" name="ol_discount_weight_switch" data-size="xs" />
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent ol-discount-inp" name="ol_discount_weight_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Cash</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="">
                                                                <input type="checkbox" name="ol_cash_weight_switch" class="switchery discountSwitchesOverland" data-size="xs" />
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent ol-discount-inp" name="ol_discount_cash_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Insurance</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="">
                                                                <input type="checkbox" name="ol_discount_insurance_switch" class="switchery discountSwitchesOverland" data-size="xs" />
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent ol-discount-inp" name="ol_discount_insurance_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Return</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="radio-addon3">
                                                                <input type="checkbox"  class="switchery discountSwitchesOverland" data-size="xs" name="ol_discount_return_switch"/>
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent ol-discount-inp" name="ol_discount_return_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>


                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div id="" class="card-header mt-1 border-success">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3 class="display-inline card-title lead success">Detain</h3>
                                        @if($sale_person['admin_id'] == Auth::id() || session('role_id') == 1)
                                            <label class="display-inline ml-1">Make Default</label>
                                            <input type="checkbox" name="det_default" id="det_default" class="switchery det_default" data-size="xs" data-switchery="true">
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <a id="detain_main_switch" href="javascript:void(0);" class="pull-right"><input name="detain_main_switch" type="checkbox" id="" class="switchery detain-main-switch" data-size="sm"/></a>
                                    </div>
                                </div>

                            </div>
                            <div id="detain"  class="border-success no-border-top card hide"
                                 aria-expanded="false">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-2">
                                                <h3>Add Origin Cities</h3>
                                            </div>
                                            <div class="col-8">
                                                <div class="form-group card border-success p-2">
                                                    <select name="detain_origin_hubs[]" id="detain_origin_hubs" class="form-control select2" multiple="multiple">
                                                        @foreach($cities as $city)
                                                            <option value="{{$city->id}}">{{$city->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-2">
                                                <h3>Add Destination Cities</h3>
                                            </div>
                                            <div class="col-8">
                                                <div class="form-group card border-success p-2">
                                                    <select name="detain_destination_hubs[]" id="detain_destination_hubs" class="form-control select2" multiple="multiple">
                                                        @foreach($cities as $city)
                                                            <option value="{{$city->id}}">{{$city->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="weight-addition-detain">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Doorstep Delivery</h3>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <fieldset class="form-group">
                                                        @foreach($min_weight[3] as $mweight)
                                                            @if($mweight->delivery_type_id == 1)
                                                                <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="{{$mweight->min_chargeable_weight}}" name="detain_door_mcw_charges" placeholder="Minimum Chargeable Weight">
                                                            @endif
                                                        @endforeach
                                                    </fieldset>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col text-center">
                                                    <label class="card-title">Range Up</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Range Down</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Base</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (Local)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (Same-Zone)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (Different-Zone)</label>
                                                </div>

                                                <div class="col-1"></div>
                                            </div>
                                            @php
                                                $index_row = 1;
                                            @endphp
                                            @foreach($weight[3] as $index => $detweight)
                                                @if($detweight->delivery_type_id == 1)
                                                <div class="row detain_door_weight_row">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" id="detain_door_range_up{{$index_row}}" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->range_up}}" name="detain_door_range_up[{{$index_row}}]" {{ (($index_row == 0 || $index_row == 1) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" id="detain_door_range_down{{$index_row}}" class="form-control decimal weight_range"data-rule-required="true" data-msg-required="This field is required"  value="{{$detweight->range_down}}" name="detain_door_range_down[{{$index_row}}]" {{ (($index_row == 0) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="DetainDoorSwitch{{$index_row}}" class="switchery weightAdditionDoorDetain" data-color="success" data-size="sm" name="detain_door_wa_switch[{{$index_row}}]"/>
                                                        </div>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->local}}" name="detain_door_local_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->same_zone}}" name="detain_door_same_zone_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->different_zone}}" name="detain_door_different_zone_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-1">
                                                        @if($index_row>1)
                                                            <span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 detain_weight_close"><i class="ft-x"></i></span>
                                                        @endif
                                                    </div>

                                                </div>{{--Row--}}
                                                    @php
                                                        $index_row++;
                                                    @endphp
                                                @endif
                                            @endforeach
                                        </div>{{--weight addition div--}}
                                        <div class="mb-2">
                                            <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="detain_door_weightadd"><i class="la la-plus"></i></button>
                                        </div>
                                        <div class="hub-weight-addition-detain">
                                            <div class="row">
                                                <div class="col-2">
                                                    <h3 class="card-title">Hub-Hub Delivery</h3>
                                                </div>
                                                <div class="col-2">
                                                    <a href="javascript:void(0);" class="pull-right" id="detain_hub_to_hub_switch"><input name="detain_hub_to_hub_switch" type="checkbox"  class="switchery detain_hub_to_hub_switch" data-size="sm" checked/></a>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <fieldset class="form-group">
                                                        @foreach($min_weight[3] as $mweight)
                                                            @if($mweight->delivery_type_id == 2)
                                                                <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="{{$mweight->min_chargeable_weight}}" name="detain_hub_mcw_charges" placeholder="Minimum Chargeable Weight">
                                                            @endif
                                                        @endforeach
                                                    </fieldset>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col text-center">
                                                    <label class="card-title">Range Up</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Range Down</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Base</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (Local)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (Same-Zone)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (Different-Zone)</label>
                                                </div>

                                                <div class="col-1"></div>
                                            </div>
                                            @php
                                                $index_row = 1;
                                            @endphp
                                            @foreach($weight[3] as $index => $detweight)
                                                @if($detweight->delivery_type_id == 2)
                                                <div class="row detain_hub_weight_row">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" id="detain_hub_range_up{{$index_row}}" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->range_up}}" name="detain_hub_range_up[{{$index_row}}]" {{ (($index_row == 0 || $index_row == 1) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" id="detain_hub_range_down{{$index_row}}" class="form-control decima weight_range" data-rule-required="true" data-msg-required="This field is required"  value="{{$detweight->range_down}}" name="detain_hub_range_down[{{$index_row}}]" {{ (($index_row == 0) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="DetainHubSwitch{{$index_row}}" class="switchery weightAdditionHubDetain" data-color="success" data-size="sm" name="detain_hub_wa_switch[{{$index_row}}]"/>
                                                        </div>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->local}}" name="detain_hub_local_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->same_zone}}" name="detain_hub_same_zone_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->different_zone}}" name="detain_hub_different_zone_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-1">
                                                        @if($index_row>1)
                                                            <span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 detain_weight_close"><i class="ft-x"></i></span>
                                                        @endif
                                                    </div>

                                                </div>{{--Row--}}
                                                    @php
                                                        $index_row++;
                                                    @endphp
                                                @endif
                                            @endforeach
                                        </div>{{--weight addition div--}}
                                        <div class="mb-2">
                                            <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="detain_hub_weightadd"><i class="la la-plus"></i></button>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-3 text-center">
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Replacement</span>
                                                        </div>
                                                        <input type="text"  class="form-control percent" name="detain_replacement_charges" data-rule-required="true" data-msg-required="This field is required" value="{{$shippingType[3][0]->replacement_charges}}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Try &amp; Buy</span>
                                                        </div>
                                                        <input type="text"  class="form-control percent" name="detain_tnb_charges" data-rule-required="true" data-msg-required="This field is required" value="{{$shippingType[3][0]->try_and_buy_charges}}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col-md-6 text-center">
                                                <div class="row">
                                                   <div class="col-3 mt-1" >
                                                        <fieldset>
                                                            <div class="input-group form-group">
                                                                <label class="card-title">DWS Weight </label>
                                                                <input type="checkbox" name="detain_dws" id="detain_dws" class="switchery detain_dws" data-size="xs" data-switchery="true">
                                                            </div>
                                                        </fieldset>
                                                    </div> 
                                                    <div class="col-4">
                                                        <fieldset>
                                                            <div class="input-group form-group">
                                                                <select name="detain_dws_weight" id="detain_dws_weight" class="form-control" disabled>
                                                                        <option value="1">High</option>
                                                                        <option value="0">Low</option>
                                                                </select>
                                                            </div>
                                                        </fieldset>
                                                    </div> 
                                                </div>
                                                
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Cash Handling Charges</h3>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="detain_cash_handling_switch"  class="switchery cashChargesDetain" data-color="success" data-size="sm" checked/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Range Up</label>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Range Down</label>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Charges</label>
                                            </div>
                                        </div>

                                        <div class="cash-handling-div-detain slabs">
                                            @foreach($cashHandling[3] as $index => $cash)
                                                <div class="row">
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="detain_cash_range_up[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->range_up}}">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="detain_cash_range_down[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->range_down}}">
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <fieldset class="form-group">
                                                            <input name="detain_cash_charges[{{$index}}]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->charges}}">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        @if($index>0)
                                                            <span class="detain_row_delete btn btn-danger rounded btn-sm-width mr-1 mb-1"><i class="ft-x"></i></span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="cash-handling-btn-detain">
                                            <button id="detainaddMoreSlabs" type="button" class="btn btn-outline-success mr-1" title="Add more slabs" ><i class="la la-plus"></i></button>
                                        </div>
                                        <hr>

                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Insurance Charges</h3>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="detain_insurance_charges_switch" class="switchery insuranceChargesdetain" data-color="success" data-size="sm" checked/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Range Up</label>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Range Down</label>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Charges</label>
                                            </div>
                                        </div>
                                        <div class="insurance-charges-div-detain slabs">
                                            @foreach($insuranceCharges[3] as $index => $det_insurance)
                                                <div class="row">
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="detain_ins_range_up[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$det_insurance->range_up}}">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="detain_ins_range_down[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$det_insurance->range_down}}">
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <fieldset class="form-group">
                                                            <input name="detain_ins_charges[{{$index}}]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$det_insurance->charges}}">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        @if($index>0)
                                                            <span class="btn btn-danger rounded btn-sm-width mr-1 mb-1 detain_row_delete"><i class="ft-x"></i></span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="insurance-charges-btn-detain">
                                            <button id="detainaddMoreSlabsInsurance" type="button" class="btn btn-outline-success mr-1" title="Add more slabs"><i class="la la-plus"></i></button>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Return Charges</h3>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="detain_return_switch" class="switchery returnChargesDetain" data-color="success" data-size="sm" checked/>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row return-charges-div-detain justify-content-center">

                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Local Charges</label>
                                                <fieldset class="form-group">
                                                    <input type="text" class="form-control amount" name="detain_return_local_charges" data-rule-required="true" data-msg-required="This field is required" value="{{$returnCharges[3][0]->local}}">
                                                </fieldset>
                                            </div>

                                            <div class="col text-center">
                                                <label class="card-title">Same Zone</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent" name="detain_return_same_zone_charges"  value="{{$returnCharges[3][0]->same_zone}}">
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <label class="card-title">Different Zone</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent" name="detain_return_different_zone_charges"  value="{{$returnCharges[3][0]->different_zone}}">
                                                </fieldset>
                                            </div>
                                        </div>

                                        <hr>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Fuel Surcharge</h3>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="detain_fuel_switch" class="switchery fuelSurchargeDetain" data-color="success" data-size="sm" checked/>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row fuel-surcharge-div-detain">

                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Charges</label>
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <input type="text"  class="form-control " name="detain_fuel_surcharge" data-rule-required="true" data-msg-required="This field is required" value="{{$fuelCharges[3][0]->fuel_surcharge}}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>

                                        </div>

                                        <hr>
                                        <div class="">
                                            <h3 class="card-title">Discount Rates</h3>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-md-6">
                                                <label class="">Title</label>
                                                <div class='form-group'>
                                                    <input type='text' class="form-control" data-rule-required="true" data-msg-required="This field is required" disabled name="detain_discount_title"/>
                                                </div>

                                            </div>
                                            <div class="col-md-6">
                                                <label class="">Apply [to - from]</label>
                                                <div class='input-group form-group'>
                                                    <input type='text' class="form-control daterange" data-rule-required="true" data-msg-required="This field is required" name="detain_daterange" disabled/>
                                                    <div class="input-group-append">
                                                            <span class="input-group-text">
                                                              <span class="la la-calendar"></span>
                                                            </span>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Weight</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox" id="" class="switchery discountSwitchesDetain" name="detain_discount_weight_switch" data-size="xs" />
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent detain-discount-inp" name="detain_discount_weight_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Cash</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="">
                                                                <input type="checkbox" name="detain_cash_weight_switch" class="switchery discountSwitchesDetain" data-size="xs" />
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent detain-discount-inp" name="detain_discount_cash_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Insurance</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="">
                                                                <input type="checkbox" name="detain_discount_insurance_switch" class="switchery discountSwitchesDetain" data-size="xs" />
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent detain-discount-inp" name="detain_discount_insurance_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Return</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox"  class="switchery discountSwitchesDetain" data-size="xs" name="detain_discount_return_switch"/>
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent detain-discount-inp" name="detain_discount_return_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div id="" class="card-header mt-1 border-success">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3 class="display-inline card-title lead success">Sameday</h3>
                                        @if($sale_person['admin_id'] == Auth::id() || session('role_id') == 1)
                                            <label class="display-inline ml-1">Make Default</label>
                                            <input type="checkbox" name="sameday_default" id="sameday_default" class="switchery sameday_default" data-size="xs" data-switchery="true">
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <a id="sameday_main_switch" href="javascript:void(0);" class="pull-right"><input name="sameday_main_switch" type="checkbox" id="" class="switchery sameday-main-switch" data-size="sm"/></a>
                                    </div>
                                </div>

                            </div>
                            <div id="sameday" class="border-success no-border-top card hide">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-2">
                                                <h3>Add Origin Cities</h3>
                                            </div>
                                            <div class="col-8">
                                                <div class="form-group card border-success p-2">
                                                    <select name="sameday_origin_hubs[]" id="sameday_origin_hubs" class="form-control select2" multiple="multiple">
                                                        @foreach($cities as $city)
                                                            <option value="{{$city->id}}">{{$city->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-2">
                                                <h3>Add Destination Cities</h3>
                                            </div>
                                            <div class="col-8">
                                                <div class="form-group card border-success p-2">
                                                    <select name="sameday_destination_hubs[]" id="sameday_destination_hubs" class="form-control select2" multiple="multiple">
                                                        @foreach($cities as $city)
                                                            <option value="{{$city->id}}">{{$city->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="weight-addition-sameday">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Doorstep Delivery</h3>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <fieldset class="form-group">
                                                        @foreach($min_weight[4] as $mweight)
                                                            @if($mweight->delivery_type_id == 1)
                                                                <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="{{$mweight->min_chargeable_weight}}" name="sameday_door_mcw_charges" placeholder="Minimum Chargeable Weight">
                                                            @endif
                                                        @endforeach
                                                    </fieldset>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col text-center">
                                                    <label class="card-title">Range Up</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Range Down</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Base</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">6hr Charges</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Sameday Charges</label>
                                                </div>
                                                <div class="col-1"></div>

                                            </div>
                                            @php
                                                $index_row = 1;
                                            @endphp
                                            @foreach($weight[4] as $index => $sameweight)
                                                @if($sameweight->delivery_type_id == 1)
                                                <div class="row sameday_door_weight_row">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" id="sameday_door_range_up{{$index_row}}" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->range_up}}" name="sameday_door_range_up[{{$index_row}}]" {{ (($index_row == 0 || $index_row == 1) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" id="sameday_door_range_down{{$index_row}}" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->range_down}}" name="sameday_door_range_down[{{$index_row}}]" {{ (($index_row == 0) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="SamedayDoorSwitch{{$index_row}}" class="switchery weightAdditionDoorSameday" data-color="success" data-size="sm" name="sameday_door_wa_switch[{{$index_row}}]"/>
                                                        </div>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->local}}" name="sameday_door_local_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->same_zone}}" name="sameday_door_same_zone_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-1">
                                                        @if($index_row>1)
                                                            <span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 sameday_weight_close"><i class="ft-x"></i></span>
                                                        @endif
                                                    </div>
                                                </div>{{--Row--}}
                                                    @php
                                                        $index_row++;
                                                    @endphp
                                                @endif
                                            @endforeach
                                        </div>{{--weight addition div--}}
                                        <div class="mb-2">
                                            <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="sameday_door_weightadd"><i class="la la-plus"></i></button>
                                        </div>
                                        <div class="hub-weight-addition-sameday">
                                            <div class="row">
                                                <div class="col-2">
                                                    <h3 class="card-title">Hub-Hub Delivery</h3>
                                                </div>
                                                <div class="col-2">
                                                    <a href="javascript:void(0);" class="pull-right" id="sameday_hub_to_hub_switch"><input name="sameday_hub_to_hub_switch" type="checkbox"  class="switchery sameday_hub_to_hub_switch" data-size="sm" checked/></a>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <fieldset class="form-group">
                                                        @foreach($min_weight[4] as $mweight)
                                                            @if($mweight->delivery_type_id == 1)
                                                                <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="{{$mweight->min_chargeable_weight}}" name="sameday_hub_mcw_charges" placeholder="Minimum Chargeable Weight">
                                                            @endif
                                                        @endforeach
                                                    </fieldset>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col text-center">
                                                    <label class="card-title">Range Up</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Range Down</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Base</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">6hr Charges</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Sameday Charges</label>
                                                </div>
                                                <div class="col-1"></div>

                                            </div>
                                            @php
                                                $index_row = 1;
                                            @endphp
                                            @foreach($weight[4] as $index => $sameweight)
                                                @if($sameweight->delivery_type_id == 2)
                                                <div class="row sameday_hub_weight_row">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" id="sameday_hub_range_up{{$index_row}}" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->range_up}}" name="sameday_hub_range_up[{{$index_row}}]" {{ (($index_row == 0 || $index_row == 1) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" id="sameday_hub_range_down{{$index_row}}" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->range_down}}" name="sameday_hub_range_down[{{$index_row}}]" {{ (($index_row == 0) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="SamedayHubSwitch{{$index_row}}" class="switchery weightAdditionHubSameday" data-color="success" data-size="sm" name="sameday_hub_wa_switch[{{$index_row}}]"/>
                                                        </div>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->local}}" name="sameday_hub_local_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-pecent" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->same_zone}}" name="sameday_hub_same_zone_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-1">
                                                        @if($index_row>1)
                                                            <span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 sameday_weight_close"><i class="ft-x"></i></span>
                                                        @endif
                                                    </div>
                                                </div>{{--Row--}}
                                                    @php
                                                        $index_row++;
                                                    @endphp
                                                @endif
                                            @endforeach
                                        </div>{{--weight addition div--}}
                                        <div class="mb-2">
                                            <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="sameday_hub_weightadd"><i class="la la-plus"></i></button>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-3 text-center">
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Replacement</span>
                                                        </div>
                                                        <input type="text"  class="form-control percent" name="sameday_replacement_charges" data-rule-required="true" data-msg-required="This field is required" value="{{$shippingType[4][0]->replacement_charges}}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Try & Buy</span>
                                                        </div>
                                                        <input type="text"  class="form-control percent" name="sameday_tnb_charges" data-rule-required="true" data-msg-required="This field is required" value="{{$shippingType[4][0]->try_and_buy_charges}}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col-md-6 text-center">
                                                <div class="row">
                                                   <div class="col-3 mt-1" >
                                                        <fieldset>
                                                            <div class="input-group form-group">
                                                                <label class="card-title">DWS Weight </label>
                                                                <input type="checkbox" name="sameday_dws" id="sameday_dws" class="switchery sameday_dws" data-size="xs" data-switchery="true">
                                                            </div>
                                                        </fieldset>
                                                    </div> 
                                                    <div class="col-4">
                                                        <fieldset>
                                                            <div class="input-group form-group">
                                                                <select name="sameday_dws_weight" id="sameday_dws_weight" class="form-control" disabled>
                                                                        <option value="1">High</option>
                                                                        <option value="0">Low</option>
                                                                </select>
                                                            </div>
                                                        </fieldset>
                                                    </div> 
                                                </div>
                                                
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Cash Handling Charges</h3>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="sameday_cash_handling_switch"  class="switchery cashChargesSameday" data-color="success" data-size="sm" checked/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Range Up</label>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Range Down</label>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Charges</label>
                                            </div>
                                        </div>

                                        <div class="cash-handling-div-sameday slabs">
                                            @foreach($cashHandling[4] as $index => $cash)
                                                <div class="row">
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="sameday_cash_range_up[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->range_up}}">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="sameday_cash_range_down[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->range_down}}">
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="sameday_cash_charges[{{$index}}]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->charges}}">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        @if($index>0)
                                                            <span class="sameday_row_delete btn btn-danger rounded btn-sm-width mr-1 mb-1"><i class="ft-x"></i></span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="cash-handling-btn-sameday">
                                            <button id="samedayaddMoreSlabs" type="button" class="btn btn-outline-success mr-1" title="Add more slabs" ><i class="la la-plus"></i></button>
                                        </div>
                                        <hr>

                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Insurance Charges</h3>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="sameday_insurance_charges_switch" class="switchery insuranceChargessameday" data-color="success" data-size="sm" checked/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Range Up</label>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Range Down</label>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Charges</label>
                                            </div>
                                        </div>
                                        <div class="insurance-charges-div-sameday slabs">
                                            @foreach($insuranceCharges[4] as $index => $same_insurance)
                                                <div class="row">
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="sameday_ins_range_up[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$same_insurance->range_up}}">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="sameday_ins_range_down[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$same_insurance->range_down}}">
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="sameday_ins_charges[{{$index}}]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$same_insurance->charges}}">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        @if($index>0)
                                                            <span class="btn btn-danger rounded btn-sm-width mr-1 mb-1 sameday_row_delete"><i class="ft-x"></i></span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="insurance-charges-btn-sameday">
                                            <button id="samedayaddMoreSlabsInsurance" type="button" class="btn btn-outline-success mr-1" title="Add more slabs"><i class="la la-plus"></i></button>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Return Charges</h3>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="sameday_return_switch" class="switchery returnChargesSameday" data-color="success" data-size="sm" checked/>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row return-charges-div-sameday">

                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Local Charges</label>
                                                <fieldset class="form-group">
                                                    <input type="text"  class="form-control amount" name="sameday_return_local_charges"  data-rule-required="true" data-msg-required="This field is required" value="{{$returnCharges[4][0]->local}}">
                                                </fieldset>
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="card-title">National Charges</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent" name="sameday_return_same_zone_charges"  value="{{$returnCharges[4][0]->same_zone}}">
                                                </fieldset>
                                            </div>
                                        </div>

                                        <hr>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Fuel Surcharge</h3>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="sameday_fuel_switch" class="switchery fuelSurchargeSameday" data-color="success" data-size="sm" checked/>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row fuel-surcharge-div-sameday">

                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Charges</label>
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <input type="text"  class="form-control " name="sameday_fuel_surcharge" data-rule-required="true" data-msg-required="This field is required" value="{{$fuelCharges[4][0]->fuel_surcharge}}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>

                                        </div>

                                        <hr>

                                        <hr>
                                        <div class="">
                                            <h3 class="card-title">Discount Rates</h3>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-md-6">
                                                <label class="">Title</label>
                                                <div class='form-group'>
                                                    <input type='text' class="form-control" data-rule-required="true" data-msg-required="This field is required" disabled name="sameday_discount_title"/>
                                                </div>

                                            </div>
                                            <div class="col-md-6">
                                                <label class="">Apply [to - from]</label>
                                                <div class='input-group form-group'>
                                                    <input type='text' class="form-control daterange" data-rule-required="true" data-msg-required="This field is required" disabled name="sameday_daterange"/>
                                                    <div class="input-group-append">
                                                            <span class="input-group-text">
                                                              <span class="la la-calendar"></span>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Weight</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox" id="" class="switchery discountSwitchesSameday" name="sameday_discount_weight_switch" data-size="xs" />
                                                              </span>
                                                        </div>
                                                        <input type="text"  class="form-control dec-percent sameday-discount-inp"  data-rule-required="true" data-msg-required="This field is required" name="sameday_discount_weight_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Cash</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="">
                                                                <input type="checkbox" name="sameday_cash_weight_switch" class="switchery discountSwitchesSameday" data-size="xs" />
                                                              </span>
                                                        </div>
                                                        <input type="text"  class="form-control dec-percent sameday-discount-inp"  data-rule-required="true" data-msg-required="This field is required" name="sameday_discount_cash_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Insurance</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="">
                                                                <input type="checkbox" name="sameday_discount_insurance_switch" class="switchery discountSwitchesSameday" data-size="xs" />
                                                              </span>
                                                        </div>
                                                        <input type="text"  class="form-control dec-percent sameday-discount-inp"  data-rule-required="true" data-msg-required="This field is required" name="sameday_discount_insurance_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Return</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox"  class="switchery discountSwitchesSameday" data-size="xs" name="sameday_discount_return_switch"/>
                                                              </span>
                                                        </div>
                                                        <input type="text"  class="form-control dec-percent sameday-discount-inp"  data-rule-required="true" data-msg-required="This field is required" name="sameday_discount_return_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div id="" class="card-header mt-1 border-primary">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3 class="display-inline card-title lead primary">Warehousing</h3>
                                        
                                    </div>
                                    <div class="col-md-6">
                                        <a id="warehouse_main_switch" href="javascript:void(0);" class="pull-right"><input name="warehouse_main_switch" type="checkbox" class="switchery warehouse-main-switch" data-size="sm" data-color="info"/></a>
                                    </div>
                                </div>

                            </div>
                            <div id="warehousing" class="border-primary no-border-top card hide">
                                <div class="card-content">
                                    <div class="card-body pb-0">
                                        <div class="row">
                                            <div class="col-3 form-group">
                                                <select name="invoicing_cycle" class="select2" id="invoicing_cycle_select" data-rule-required="true" data-msg-required="Invoicing cycle is required">
                                                    @foreach($invoicing_cycles as $cycle)
                                                        <option value="{{ $cycle->id }}">{{ $cycle->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                        </div>
                                        <div class="col-12">
                                                    <h3 class="card-title">Stocking Charges</h3>
                                                </div>

                                        <div class="card border-primary p-2">
                                            <div class="row">
                        
                                                <div class="col-12">
                                                    <div class="row">
                                                        <div class="col-4 text-center">
                                                            <fieldset>
                                                                <div class="input-group input-group-sm form-group">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text" id="">Per Product Charges</span>
                                                                    </div>
                                                                    <div class="input-group-prepend">
                                                                          <span class="input-group-text" id="">
                                                                            <input type="checkbox" name="ppc_switch" data-color="info" class="switchery PPCSwitch" data-size="xs" checked/>
                                                                          </span>
                                                                    </div>
                                                                    <input type="text"  class="form-control numeric ppc-inp"  data-rule-required="true" data-msg-required="This field is required" name="ppc_charges" >
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-4 text-center">
                                                            <fieldset>
                                                                <div class="input-group input-group-sm form-group">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text" id="">Per Square Foot Charges</span>
                                                                    </div>
                                                                    <div class="input-group-prepend">
                                                                          <span class="input-group-text" id="">
                                                                            <input type="checkbox" name="psf_switch" data-color="info" class="switchery PSFSwitch" data-size="xs" checked/>
                                                                          </span>
                                                                    </div>
                                                                    <input type="text"  class="form-control numeric psf-inp"  data-rule-required="true" data-msg-required="This field is required" name="psf_charges" >
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-3">
                                                            <h3 class="card-title">Storage Type Charges</h3>
                                                        </div>
                                                        <div class="col-3">
                                                            <div class="form-group ">
                                                                <input type="checkbox" name="storage_charges_switch" class="switchery storageCharges" data-color="info" data-size="sm" checked/>
                                                            </div>
                                                        </div>
                                                        <div class="col-12" id="wms_storage_types_div">
                                                            <div class="row storage_type_row" id="storage_type_row0">
                                                                <input type="hidden" id="storage_type_input0" name="storage_type[0]">
                                                                <div class="col-md-2 st_select">
                                                                    <fieldset class="form-group">
                                                                        <select class="select2 form-control storage_type" name="storage_type[0]" data-rule-required="true" data-msg-required="This field is required">
                                                                        </select>
                                                                    </fieldset>
                                                                </div>

                                                                <div class="col-md-2">
                                                                    <fieldset class="form-group">
                                                                        <input name="storage_type_charges[0]" data-rule-required="true" data-msg-required="Charges are required" type="text" class="form-control numeric" placeholder="Charges">
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col actions">
                                                                        <span id="storage_type_add" class="btn btn-sm btn-outline-primary d-none"><i class="la la-check"></i></span>
                                                                        <span class="storage_type_row_delete btn btn-sm btn-outline-danger d-none"><i class="la la-trash"></i></span>
                                                                </div>
                                                            </div>
                                                            
                                                            
                                                        </div>
                                                        
                                                    </div>

                                                </div>
                                               
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="card-body">
                                                <div>
                                                    <h3 class="card-title">Fulfillment Charges</h3>
                                                </div>

                                            <div class="card border-primary p-2">
                                            <div class="row">
                                                
                                                <div class="col-12">
                                                    

                                                    <div class="row">
                                                        <div class="col-3">
                                                            <h3 class="card-title">Packing Charges</h3>
                                                        </div>
                                                        <div class="col-3">
                                                            <div class="form-group ">
                                                                <input type="checkbox" name="packing_charges_switch" class="switchery packingCharges" data-color="info" data-size="sm" checked/>
                                                            </div>
                                                        </div>

                                                        <div class="col-12" id="wms_packing_charges_div">
                                                            <div class="row packing_type_row" id="packing_type_row0">
                                                                <input type="hidden" id="packing_type_input0" name="packing_type[0]">
                                                                <div class="col-md-2">
                                                                    <fieldset class="form-group">
                                                                        <select class="select2 form-control packing_type" name="packing_type[0]" data-rule-required="true" data-msg-required="This field is required">
                                                                        </select>
                                                                    </fieldset>
                                                                </div>
                                                                <input type="hidden" id="packing_size_input0" name="packing_size[0]">
                                                                <div class="col-md-2">
                                                                    <fieldset class="form-group">
                                                                        <select class="select2 form-control packing_size" name="packing_size[0]" data-rule-required="true" data-msg-required="This field is required">
                                                                        </select>
                                                                    </fieldset>
                                                                </div>

                                                                <div class="col-md-2">
                                                                    <fieldset class="form-group">
                                                                        <input name="packing_charges[0]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control amount" placeholder="Charges">
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <fieldset class="form-group">
                                                                        <span id="packing_type_add" class="btn btn-outline-primary d-none" title="Add" ><i class="la la-check"></i></span>
                                                                        <span class="packing_type_row_delete btn btn-sm btn-outline-danger d-none"><i class="la la-trash"></i></span>
                                                                    </fieldset>
                                                                </div>
                                                            </div>
                                                            
                                                        </div>
                                                        
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-2">
                                                            <h3 class="card-title">Labelling Charges</h3>
                                                        </div>
                                                        <div class="col-3">
                                                            <div class="form-group ">
                                                                <input type="checkbox" name="labelling_charges_switch" class="switchery labellingSwitch" data-color="info" data-size="sm" checked/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                        
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <fieldset class="form-group">
                                                                <input name="labelling_charges" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control amount" placeholder="Charges">
                                                            </fieldset>
                                                        </div>
                                                    </div>
                                                            
                                                        
                                                    </div>


                                                </div>
                                               
                                                </div>
                                            </div>
                                </div>

                            </div><!--End Warehousing -->


                            <div class="row mt-2 justify-content-center">
                                <div class="col-5 form-group">
                                    <textarea name="rate_remarks" id="rate_remarks" class="form-control" placeholder="Rate Remarks..." rows="3"></textarea>
                                </div>
                                
                            </div>  
                            <div class="text-center mt-2">
                                <div class="form-group">

                                    <button id="addRatesSubmit" type="submit" class="btn btn-outline-success round btn-min-width mr-1 mb-1">Submit</button>
                                </div>
                            </div>


                        </form>

                    </div>
                </div>
            </div>
        </div>

    </section>


@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

    <style type="text/css">
        .hide{
            display:none;
        }
    </style>


@endsection

@section('js')
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            var rate_type = $('#corporate_type_id').val();
            console.log(rate_type);

            $('body').on('change', '#rate_remarks', function () {
                $(this).val($(this).val().trim());
            });

            $('#on_dws_weight').select2({
                placeholder: "Select Weight Type",
                width:'100%'
            });
            $("#on_dws").on('change', function(){
                if($("#on_dws").is(":checked")){
                    $('#on_dws_weight').attr('disabled', false);
                }else{
                    $('#on_dws_weight').attr('disabled', true);
                }
            });
            $("#ol_dws").on('change', function(){
                if($("#ol_dws").is(":checked")){
                    $('#ol_dws_weight').attr('disabled', false);
                }else{
                    $('#ol_dws_weight').attr('disabled', true);
                }
            });

            $("#detain_dws").on('change', function(){
                if($("#detain_dws").is(":checked")){
                    $('#detain_dws_weight').attr('disabled', false);
                }else{
                    $('#detain_dws_weight').attr('disabled', true);
                }
            });

            $("#sameday_dws").on('change', function(){
                if($("#sameday_dws").is(":checked")){
                    $('#sameday_dws_weight').attr('disabled', false);
                }else{
                    $('#sameday_dws_weight').attr('disabled', true);
                }
            });

            
            $("#on_default").on('change', function(){
                if($("#ol_default").is(":checked")){
                    $("#ol_default").trigger('click');
                }
                if($("#det_default").is(":checked")){
                    $("#det_default").trigger('click');
                }
                if($("#sameday_default").is(":checked")){
                    $("#sameday_default").trigger('click');
                }
                if (this.checked != true) {
                    $(this).trigger('click');
                }
            });

            $("#ol_default").on('change', function(){
                if($("#on_default").is(":checked")){
                    $("#on_default").trigger('click');
                }
                if($("#det_default").is(":checked")){
                    $("#det_default").trigger('click');
                }
                if($("#sameday_default").is(":checked")){
                    $("#sameday_default").trigger('click');
                }
                if (this.checked != true) {
                    $(this).trigger('click');
                }
            });

            $("#det_default").on('change', function(){
                if($("#on_default").is(":checked")){
                    $("#on_default").trigger('click');
                }
                if($("#ol_default").is(":checked")){
                    $("#ol_default").trigger('click');
                }
                if($("#sameday_default").is(":checked")){
                    $("#sameday_default").trigger('click');
                }
                if (this.checked != true) {
                    $(this).trigger('click');
                }
            });

            $("#sameday_default").on('change', function(){
                if($("#on_default").is(":checked")){
                    $("#on_default").trigger('click');
                }
                if($("#ol_default").is(":checked")){
                    $("#ol_default").trigger('click');
                }
                if($("#det_default").is(":checked")){
                    $("#det_default").trigger('click');
                }
                if (this.checked != true) {
                    $(this).trigger('click');
                }
            });

            $('input[name="on_door_mcw_charges"]').on('change', function () {
                var value = $(this).val();
                var maxvalue = parseFloat(value);
                var minvalue = maxvalue + 0.01;
                $('input[name="on_door_range_down[1]"]').val(maxvalue);
                $('input[name="on_door_range_up[2]"]').val(minvalue);
            });

            $('input[name="on_hub_mcw_charges"]').on('change', function () {
                var value = $(this).val();
                var maxvalue = parseFloat(value);
                var minvalue = maxvalue + 0.01;
                $('input[name="on_hub_range_down[1]"]').val(maxvalue);
                $('input[name="on_hub_range_up[2]"]').val(minvalue);
            });
            $('input[name="ol_door_mcw_charges"]').on('change', function () {
                var value = $(this).val();
                var maxvalue = parseFloat(value);
                var minvalue = maxvalue + 0.01;
                $('input[name="ol_door_range_down[1]"]').val(maxvalue);
                $('input[name="ol_door_range_up[2]"]').val(minvalue);
            });
            $('input[name="ol_hub_mcw_charges"]').on('change', function () {
                var value = $(this).val();
                var maxvalue = parseFloat(value);
                var minvalue = maxvalue + 0.01;
                $('input[name="ol_hub_range_down[1]"]').val(maxvalue);
                $('input[name="ol_hub_range_up[2]"]').val(minvalue);
            });
            $('input[name="detain_door_mcw_charges"]').on('change', function () {
                var value = $(this).val();
                var maxvalue = parseFloat(value);
                var minvalue = maxvalue + 0.01;
                $('input[name="detain_door_range_down[1]"]').val(maxvalue);
                $('input[name="detain_door_range_up[2]"]').val(minvalue);
            });
            $('input[name="detain_hub_mcw_charges"]').on('change', function () {
                var value = $(this).val();
                var maxvalue = parseFloat(value);
                var minvalue = maxvalue + 0.01;
                $('input[name="detain_hub_range_down[1]"]').val(maxvalue);
                $('input[name="detain_hub_range_up[2]"]').val(minvalue);
            });
            $('input[name="sameday_door_mcw_charges"]').on('change', function () {
                var value = $(this).val();
                var maxvalue = parseFloat(value);
                var minvalue = maxvalue + 0.01;
                $('input[name="sameday_door_range_down[1]"]').val(maxvalue);
                $('input[name="sameday_door_range_up[2]"]').val(minvalue);
            });
            $('input[name="sameday_hub_mcw_charges"]').on('change', function () {
                var value = $(this).val();
                var maxvalue = parseFloat(value);
                var minvalue = maxvalue + 0.01;
                $('input[name="sameday_hub_range_down[1]"]').val(maxvalue);
                $('input[name="sameday_hub_range_up[2]"]').val(minvalue);
            });

            //Hub to Hub Switches
            $('#on_hub_to_hub_switch').on('change',function(){
                var onhub_to_hub_switch = document.querySelector('.switchery.on_hub_to_hub_switch');
                if (onhub_to_hub_switch.checked === true) {
                    $('input[name="on_hub_mcw_charges"]').attr('disabled', false);
                    $('.on_hub_weight_row input').attr('disabled', false);
                    $('#on_hub_range_up1').attr('disabled', true);
                    $('#hub_waddition_btn').attr('disabled', false);
                } else if (onhub_to_hub_switch.checked === false) {
                    $('.on_hub_weight_row input').attr('disabled', true);
                    $('#hub_waddition_btn').attr('disabled', true);
                    $('input[name="on_hub_mcw_charges"]').attr('disabled', true);
                }
            });

            $('#ol_hub_to_hub_switch').on('change',function(){
                var ol_hub_to_hub_switch = document.querySelector('.switchery.ol_hub_to_hub_switch');
                if (ol_hub_to_hub_switch.checked === true) {
                    $('input[name="ol_hub_mcw_charges"]').attr('disabled', false);
                    $('.ol_hub_weight_row input').attr('disabled', false);
                    $('#ol_hub_range_up1').attr('disabled', true);
                    $('#overland_hub_weightadd').attr('disabled', false);
                } else if (ol_hub_to_hub_switch.checked === false) {
                    $('.ol_hub_weight_row input').attr('disabled', true);
                    $('#overland_hub_weightadd').attr('disabled', true);
                    $('input[name="ol_hub_mcw_charges"]').attr('disabled', true);

                }
            });

            $('#detain_hub_to_hub_switch').on('change',function(){
                var detain_hub_to_hub_switch = document.querySelector('.switchery.detain_hub_to_hub_switch');
                if (detain_hub_to_hub_switch.checked === true) {
                    $('input[name="detain_hub_mcw_charges"]').attr('disabled', false);
                    $('.detain_hub_weight_row input').attr('disabled', false);
                    $('#detain_hub_range_up1').attr('disabled', true);
                    $('#detain_hub_weightadd').attr('disabled', false);
                } else if (detain_hub_to_hub_switch.checked === false) {
                    $('.detain_hub_weight_row input').attr('disabled', true);
                    $('#detain_hub_weightadd').attr('disabled', true);
                    $('input[name="detain_hub_mcw_charges"]').attr('disabled', true);

                }
            });

            $('#sameday_hub_to_hub_switch').on('change',function(){
                var sameday_hub_to_hub_switch = document.querySelector('.switchery.sameday_hub_to_hub_switch');
                if (sameday_hub_to_hub_switch.checked === true) {
                    $('input[name="sameday_hub_mcw_charges"]').attr('disabled', false);
                    $('.sameday_hub_weight_row input').attr('disabled', false);
                    $('#sameday_hub_range_up1').attr('disabled', true);
                    $('#sameday_hub_weightadd').attr('disabled', false);
                } else if (sameday_hub_to_hub_switch.checked === false) {
                    $('.sameday_hub_weight_row input').attr('disabled', true);
                    $('#sameday_hub_weightadd').attr('disabled', true);
                    $('input[name="sameday_hub_mcw_charges"]').attr('disabled', true);

                }
            });


            // var on_main_switch = document.querySelector('#on_main_switch');
            $('#on_main_switch').on('change',function(){

                var onmainswitch = document.querySelector('.switchery.on-main-switch');
                if (onmainswitch.checked === true) {
                    $('#overnight').slideDown('slow');

                } else if (onmainswitch.checked === false) {
                    $('#overnight').slideUp('slow');


                }
            });
            $('#ol_main_switch').on('change',function(){

                var olmainswitch = document.querySelector('.switchery.ol-main-switch');
                if (olmainswitch.checked === true) {
                    $('#overland').slideDown('slow');

                } else if (olmainswitch.checked === false) {
                    $('#overland').slideUp('slow');


                }
            });
            $('#detain_main_switch').on('change',function(){
                var detainmainswitch = document.querySelector('.switchery.detain-main-switch');
                if (detainmainswitch.checked === true) {
                    $('#detain').slideDown('slow');

                } else if (detainmainswitch.checked === false) {
                    $('#detain').slideUp('slow');


                }
            });
            $('#sameday_main_switch').on('change',function(){
                var samedaymainswitch = document.querySelector('.switchery.sameday-main-switch');
                if (samedaymainswitch.checked === true) {
                    $('#sameday').slideDown('slow');

                } else if (samedaymainswitch.checked === false) {
                    $('#sameday').slideUp('slow');


                }
            });
            var shipper = @json($shipper->id);

            if(rate_type !== null && rate_type != '' ){
                console.log(1);
                var route = '{!! route('admin.corporate.default.change_rate_type', ':id') !!}';
                route = route.replace(':id', shipper);
                $("#ratesAdditionForm").attr('action', route);

            }
            else{
                console.log(2);
                var route = '{!! route('admin.corporate.zone_wise.add.rates', ':id') !!}';
                route = route.replace(':id', shipper);
                $("#ratesAdditionForm").attr('action', route);
            }


        });


        $('.decimal').inputmask({
            'alias': 'decimal',
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            'digits': 2,
            'min': 0.00,
            'max': 100000
        });
        $('.amount').inputmask({
            'alias': 'decimal',
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            'digits': 2,
            'min': 0.00,
            'max': 1000000.00
        });

        $('.percent').inputmask({
            'alias': 'numeric',
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            'min': 0,
            'max': 500
        });
        $('.numeric').inputmask({
            'alias': 'integer',
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            'min': 0,
            'max': 1000000
        });
        $('.dec-percent').inputmask("Regex",{
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            regex: '^\\d{1,9}(\\.\\d{1,2})?%?$'
        });

        $(".daterange").daterangepicker();

        //Overnight

        var cashhandlingswitch = document.querySelector('.switchery.cashChargesOvernight');
        var insuranceChargesSwitch = document.querySelector('.switchery.insuranceChargesOvernight');
        var returnChargesSwitch = document.querySelector('.switchery.returnChargesOvernight');
        var fuelChargesSwitch = document.querySelector('.switchery.fuelSurchargeOvernight');



        function masks() {

            $('.decimal').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 3,
                'min': 0.00,
                'max': 100000
            });
            $('.amount').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 2,
                'min': 0.00,
                'max': 1000000.00
            });
            $('.numeric').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'min': 0,
                'max': 1000000
            });
            $('.dec-percent').inputmask("Regex",{
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                regex: '^\\d{1,9}(\\.\\d{1,2})?%?$'
            });
        }

        $('body').on('click','.on_weight_close',function () {
            $(this).parent().parent().remove();
        });
        var on_door_count = $('.on_door_weight_row').length + 1;
        $('body').on('click','#waddition_btn',function () {
            var row_count = on_door_count-1;
            var on_door_range_down = parseFloat($('#on_door_range_down' + row_count).val());
            var on_door_new_range_down = on_door_range_down + 0.01;
            let htmdiv = '<div class="row" id="on_door_weight_row'+on_door_count+'"><div class="col text-center"><fieldset class="form-group"><input type="text" id="on_door_range_up'+on_door_count+'" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="' + on_door_new_range_down + '" name="on_door_range_up['+on_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" id="on_door_range_down'+on_door_count+'" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="on_door_range_down['+on_door_count+']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionDoorOvernight'+on_door_count+'" data-color="success" data-size="sm" name="on_door_wa_switch['+on_door_count+']"/></div></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required" value="" name="on_door_local_charges['+on_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="on_door_same_zone_charges['+on_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="on_door_different_zone_charges['+on_door_count+']"></fieldset></div><div class="col-1">\n' +
                '<span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 on_weight_close"><i class="ft-x"></i></span></div></div>';
            $('.weight-addition-overnight').append(htmdiv);
            var switches = document.querySelector('.switchery.weightAdditionDoorOvernight'+on_door_count);
            var switchery = new Switchery(switches, { disabled: false,color: '#37BC9B',size:'small' });
            $(".touchspin-color").TouchSpin({
                min: 0.5,
                max: 100,
                step: 0.5,
                decimals: 2,
                buttondown_class: "btn btn-success",
                buttonup_class: "btn btn-success",
                buttondown_txt: '<i class="ft-minus"></i>',
                buttonup_txt: '<i class="ft-plus"></i>'
            });

            masks();

            $("#on_door_weight_row"+on_door_count+" .validated").each(function(){
                $( this ).rules( "add", {
                    required: true,
                });

            });
            on_door_count++;
        });
        var on_hub_count = $('.on_hub_weight_row').length + 1;

        $('body').on('click','#hub_waddition_btn',function () {
            var row_count = on_hub_count-1;
            var on_hub_range_down = parseFloat($('#on_hub_range_down' + row_count).val());
            var on_hub_new_range_down = on_hub_range_down + 0.01;

            let htmdiv = '<div class="row on_hub_weight_row" id="on_hub_weight_row'+on_hub_count+'"><div class="col text-center"><fieldset class="form-group"><input type="text" id="on_hub_range_up'+ on_hub_count +'" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="'+on_hub_new_range_down+'" name="on_hub_range_up['+on_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" id="on_hub_range_down'+ on_hub_count +'" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="on_hub_range_down['+on_hub_count+']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionHubOvernight'+on_hub_count+'" data-color="success" data-size="sm" name="on_hub_wa_switch['+on_hub_count+']"/></div></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required" value="" name="on_hub_local_charges['+on_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="on_hub_same_zone_charges['+on_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="on_hub_different_zone_charges['+on_hub_count+']"></fieldset></div><div class="col-1">\n' +
                '<span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 on_weight_close"><i class="ft-x"></i></span></div></div>';
            $('.hub-weight-addition-overnight').append(htmdiv);

            var switches = document.querySelector('.switchery.weightAdditionHubOvernight'+on_hub_count);
            var switchery = new Switchery(switches, { disabled: false,color: '#37BC9B',size:'small' });
            $(".touchspin-color").TouchSpin({
                min: 0.5,
                max: 100,
                step: 0.5,
                decimals: 2,
                buttondown_class: "btn btn-success",
                buttonup_class: "btn btn-success",
                buttondown_txt: '<i class="ft-minus"></i>',
                buttonup_txt: '<i class="ft-plus"></i>'
            });

            masks();

            $("#on_hub_weight_row"+on_hub_count+" .validated").each(function(){
                $( this ).rules( "add", {
                    required: true,
                });

            });
            on_hub_count++;
        });

        // var switchery = new Switchery('.switchery.weightAddition'+count, { color: '#37BC9B' });


        //addMoreSlabs
        var on_slab_count = 5;
        $('body').on('click','#addMoreSlabs',function () {
            let htmdiv = '<div class="row" id="on_insurance_handle_'+on_slab_count+'">\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="on_cash_range_up['+on_slab_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric validated" >\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="on_cash_range_down['+on_slab_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric validated">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="on_cash_charges['+on_slab_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control dec-percent validated"></fieldset></div><div class="col">\n' +
                '<span class="btn btn-danger rounded btn-sm-width mr-1 mb-1 on_weight_close"><i class="ft-x"></i></span></div></div>';
            $('.cash-handling-div-overnight').append(htmdiv);
            masks();
            $("#on_insurance_handle_"+on_slab_count+" .validated").each(function(){
                $( this ).rules( "add", {
                    required: true,
                });

            });
            // $(this).parent().prev().find('div.slabs').append(htmdiv);
            // console.log();
            on_slab_count++;
        });
        //add more slabs insurance
        var on_ins_count = 3;
        $('body').on('click','#addMoreSlabsInsurance',function () {
            let htmdiv = '<div class="row" id="on_insurance_charge_'+on_ins_count+'">\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="on_ins_range_up['+on_ins_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric validated">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="on_ins_range_down['+on_ins_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric validated">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="on_ins_charges['+on_ins_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control dec-percent validated">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '<div class="col">\n' +
                '<span class="btn btn-danger rounded btn-sm-width mr-1 mb-1 on_weight_close"><i class="ft-x"></i></span></div></div>';
            $('.insurance-charges-div-overnight').append(htmdiv);
            masks();
            $("#on_insurance_charge_"+on_ins_count+" .validated").each(function(){
                $( this ).rules( "add", {
                    required: true,
                });

            });
            on_ins_count++;
        });
        //Cash handling
        // cashChargesOvernight
        cashhandlingswitch.onchange = function () {
            if(cashhandlingswitch.checked === true){
                $('.cash-handling-div-overnight').find('input').prop('disabled',false);
                $('.cash-handling-btn-overnight').find('button').prop('disabled',false);
            }else if(cashhandlingswitch.checked === false){
                $('.cash-handling-div-overnight').find('input').prop('disabled',true);
                $('.cash-handling-btn-overnight').find('button').prop('disabled',true);

            }
        };

        // InsuranceOvernight
        insuranceChargesSwitch.onchange = function () {
            if(insuranceChargesSwitch.checked === true){
                $('.insurance-charges-div-overnight').find('input').prop('disabled',false);
                $('.insurance-charges-btn-overnight').find('button').prop('disabled',false);
            }else if(insuranceChargesSwitch.checked === false){
                $('.insurance-charges-div-overnight').find('input').prop('disabled',true);
                $('.insurance-charges-btn-overnight').find('button').prop('disabled',true);

            }
        };
        // Return Overnight
        returnChargesSwitch.onchange = function () {
            if(returnChargesSwitch.checked === true){
                $('.return-charges-div-overnight').find('input').prop('disabled',false);
            }else if(returnChargesSwitch.checked === false){
                $('.return-charges-div-overnight').find('input').prop('disabled',true);

            }
        };
        fuelChargesSwitch.onchange = function () {
            if(fuelChargesSwitch.checked === true){
                $('.fuel-surcharge-div-overnight').find('input').prop('disabled',false);
            }else if(fuelChargesSwitch.checked === false){
                $('.fuel-surcharge-div-overnight').find('input').prop('disabled',true);

            }
        };

        //Overland
        //var weightAdditionOverland = document.querySelector('.switchery.weightAdditionOverland0');
        var cashhandlingswitchOverland = document.querySelector('.switchery.cashChargesOverland');
        var insuranceChargesSwitchOverland = document.querySelector('.switchery.insuranceChargesoverland');
        var returnChargesSwitchOverland = document.querySelector('.switchery.returnChargesOverland');
        var fuelChargesSwitchOL = document.querySelector('.switchery.fuelSurchargeOverland');
        // var packagingChargesSwitch = document.querySelector('.switchery.packagingChargesSwitch');

        //Overland


        var overland_door_count = $('.ol_door_weight_row').length + 1;
        $('body').on('click','#overland_door_weightadd',function () {
            var row_count = overland_door_count-1;
            var ol_door_range_down = parseFloat($('#ol_door_range_down' + row_count).val());
            var ol_door_new_range_down = ol_door_range_down + 0.01;

            let htmdiv1 = '<div class="row" id="ol_door_weight_row'+overland_door_count+'"><div class="col text-center"><fieldset class="form-group"><input type="text" id="ol_door_range_up'+ overland_door_count +'" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="'+ol_door_new_range_down+'" name="ol_door_range_down['+overland_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" id="ol_door_range_down'+ overland_door_count +'" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_door_range_down['+overland_door_count+']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionDoorOverland'+overland_door_count+'" data-color="success" data-size="sm" name="ol_door_wa_switch['+overland_door_count+']"/></div></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_door_local_charges['+overland_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="ol_door_same_zone_charges['+overland_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="ol_door_different_zone_charges['+overland_door_count+']"></fieldset></div><div class="col-1">\n' +
                '<span class="btn btn-danger rounded btn-sm-width mr-1 mb-1 ol_weight_close"><i class="ft-x"></i></span></div></div>';
            $('.weight-addition-overland').append(htmdiv1);

            var switches = document.querySelector('.switchery.weightAdditionDoorOverland'+overland_door_count);
            var switchery = new Switchery(switches, { disabled: false,color: '#37BC9B',size:'small' });
            $(".touchspin-color").TouchSpin({
                min: 0.5,
                max: 100,
                step: 0.5,
                decimals: 2,
                buttondown_class: "btn btn-success",
                buttonup_class: "btn btn-success",
                buttondown_txt: '<i class="ft-minus"></i>',
                buttonup_txt: '<i class="ft-plus"></i>'
            });

            masks();

            $("#ol_weight_row"+overland_door_count+" .validated").each(function(){
                $( this ).rules( "add", {
                    required: true,
                });

            });
            overland_door_count++;
        });
        var overland_hub_count = $('.ol_hub_weight_row').length + 1;
        $('body').on('click','#overland_hub_weightadd',function () {
            var row_count = overland_hub_count-1;
            var ol_hub_range_down = parseFloat($('#ol_hub_range_down' + row_count).val());
            var ol_hub_new_range_down = ol_hub_range_down + 0.01;

            let htmdiv1 = '<div class="row ol_hub_weight_row" id="ol_hub_weight_row'+overland_hub_count+'"><div class="col text-center"><fieldset class="form-group"><input type="text" id="ol_hub_range_up'+ overland_hub_count +'" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="'+ol_hub_new_range_down+'" name="ol_hub_range_up['+overland_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" id="ol_hub_range_down'+ overland_hub_count +'" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_hub_range_down['+overland_hub_count+']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionHubOverland'+overland_hub_count+'" data-color="success" data-size="sm" name="ol_hub_wa_switch['+overland_hub_count+']"/></div></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_hub_local_charges['+overland_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="ol_hub_same_zone_charges['+overland_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="ol_hub_different_zone_charges['+overland_hub_count+']"></fieldset></div><div class="col-1">\n' +
                '<span class="btn btn-danger rounded btn-sm-width mr-1 mb-1 ol_weight_close"><i class="ft-x"></i></span></div></div>';
            $('.hub-weight-addition-overland').append(htmdiv1);

            var switches = document.querySelector('.switchery.weightAdditionHubOverland'+overland_hub_count);
            var switchery = new Switchery(switches, { disabled: false,color: '#37BC9B',size:'small' });
            $(".touchspin-color").TouchSpin({
                min: 0.5,
                max: 100,
                step: 0.5,
                decimals: 2,
                buttondown_class: "btn btn-success",
                buttonup_class: "btn btn-success",
                buttondown_txt: '<i class="ft-minus"></i>',
                buttonup_txt: '<i class="ft-plus"></i>'
            });

            masks();

            $("#ol_hub_weight_row"+overland_hub_count+" .validated").each(function(){
                $( this ).rules( "add", {
                    required: true,
                });

            });
            overland_hub_count++;
        });

        //addMoreSlabs
        $('body').on('click','.ol_weight_close',function () {
            $(this).parent().parent().remove();
        });
        $('body').on('click','.ol_row_delete',function () {
            $(this).parent().parent().remove();
        });
        var ol_slab_count = 5;
        $('body').on('click','#overlandaddMoreSlabs',function () {
            let htmdiv = '<div class="row" id="ol_cash_handle_'+ol_slab_count+'">\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="ol_cash_range_up['+ol_slab_count+']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="ol_cash_range_down['+ol_slab_count+']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '                                                <div class="col-md-2">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="ol_cash_charges['+ol_slab_count+']" type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '<div class="col">\n' +
                '<span class="ol_row_delete btn btn-danger rounded btn-sm-width mr-1 mb-1"><i class="ft-x"></i></span></div></div>';
            $('.cash-handling-div-overland').append(htmdiv);
            ol_slab_count++;
            masks();
            // $(this).parent().prev().find('div.slabs').append(htmdiv);
            // console.log();

        });
        //add more slabs insurance
        var ol_ins_count = 3;
        $('body').on('click','#oladdMoreSlabsInsurance',function () {
            let htmdiv = '<div class="row" id="ol_insurance_charge_'+ol_ins_count+'">\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="ol_ins_range_up['+ol_ins_count+']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="ol_ins_range_down['+ol_ins_count+']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="ol_ins_charges['+ol_ins_count+']" type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '<div class="col">\n' +
                '<span  class="ol_row_delete btn btn-danger rounded btn-sm-width mr-1 mb-1"><i class="ft-x"></i></span></div></div>';
            $('.insurance-charges-div-overland').append(htmdiv);
            ol_ins_count++;
            masks();
            // $(this).parent().prev().find('div.slabs').append(htmdiv);
            // console.log();

        });
        //Cash handling
        // cashChargesOvernight
        cashhandlingswitchOverland.onchange = function () {
            if(cashhandlingswitchOverland.checked === true){
                // $('.cash-handling-div').
                $('.cash-handling-div-overland').find('input').prop('disabled',false);
                $('.cash-handling-btn-overland').find('button').prop('disabled',false);
            }else if(cashhandlingswitchOverland.checked === false){
                $('.cash-handling-div-overland').find('input').prop('disabled',true);
                $('.cash-handling-btn-overland').find('button').prop('disabled',true);

            }
        };

        // InsuranceOvernight
        insuranceChargesSwitchOverland.onchange = function () {
            if(insuranceChargesSwitchOverland.checked === true){
                // $('.cash-handling-div').
                $('.insurance-charges-div-overland').find('input').prop('disabled',false);
                $('.insurance-charges-btn-overland').find('button').prop('disabled',false);
            }else if(insuranceChargesSwitchOverland.checked === false){
                $('.insurance-charges-div-overland').find('input').prop('disabled',true);
                $('.insurance-charges-btn-overland').find('button').prop('disabled',true);

            }
        };
        // Return Overnight
        returnChargesSwitchOverland.onchange = function () {
            if(returnChargesSwitchOverland.checked === true){
                $('.return-charges-div-overland').find('input').prop('disabled',false);
            }else if(returnChargesSwitchOverland.checked === false){
                $('.return-charges-div-overland').find('input').prop('disabled',true);

            }
        };
        fuelChargesSwitchOL.onchange = function () {
            if(fuelChargesSwitchOL.checked === true){
                $('.fuel-surcharge-div-overland').find('input').prop('disabled',false);
            }else if(fuelChargesSwitchOL.checked === false){
                $('.fuel-surcharge-div-overland').find('input').prop('disabled',true);

            }
        };

        // packagingChargesSwitch.onchange = function () {
        //     if(packagingChargesSwitch.checked === true){
        //         $('#packaging_material_charges_div').slideDown('slow');
        //         $('.packaging-charges-div').find('input').prop('disabled',false);
        //     }else if(packagingChargesSwitch.checked === false){
        //         $('#packaging_material_charges_div').slideUp('slow');
        //         $('.packaging-charges-div').find('input').prop('disabled',true);
        //
        //     }
        // };

{{--        @if(count($packaging_material_types) > 0)--}}
{{--            @foreach($packaging_material_types as $index => $type)--}}
{{--                var PackageSwitch = [];--}}
{{--                var type_id_{{$index}} = '{{$type->id}}';--}}
{{--                var type_id = '{{$type->id}}';--}}
{{--                PackageSwitch[type_id] = document.querySelector('.packaging_type_'+type_id);--}}
{{--                PackageSwitch[type_id].onchange = function () {--}}

{{--                    if ($(this).is(':checked') === true) {--}}
{{--                        $('#package_type_'+type_id_{{$index}}).slideDown('slow');--}}

{{--                    } else if ($(this).is(':checked') === false) {--}}
{{--                        $('#package_type_'+type_id_{{$index}}).slideUp('slow');--}}

{{--                    }--}}
{{--                };--}}
{{--            @endforeach--}}
{{--        @endif--}}
        //overland end
        //detain
        //var weightAdditionDetain = document.querySelector('.switchery.weightAdditionDetain0');
        var cashhandlingswitchDetain = document.querySelector('.switchery.cashChargesDetain');
        var insuranceChargesSwitchDetain = document.querySelector('.switchery.insuranceChargesdetain');
        var returnChargesSwitchDetain = document.querySelector('.switchery.returnChargesDetain');
        var fuelChargesSwitchDetain = document.querySelector('.switchery.fuelSurchargeDetain');


        //detain


        var detain_door_count = $('.detain_door_weight_row').length + 1;
        $('body').on('click','#detain_door_weightadd',function () {
            var row_count = detain_door_count-1;
            var detain_door_range_down = parseFloat($('#detain_door_range_down' + row_count).val());
            var detain_door_new_range_down = detain_door_range_down + 0.01;

            let htmdiv1 = '<div class="row" id="detain_door_weight_row'+detain_door_count+'"><div class="col text-center"><fieldset class="form-group"><input type="text" id="detain_door_range_up'+detain_door_count+'" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="'+detain_door_new_range_down+'" name="detain_door_range_up['+detain_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" id="detain_door_range_down'+detain_door_count+'" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_door_range_down['+detain_door_count+']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionDoorDetain'+detain_door_count+'" data-color="success" data-size="sm" name="detain_door_wa_switch['+detain_door_count+']"/></div></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_door_local_charges['+detain_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="detain_door_same_zone_charges['+detain_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="detain_door_different_zone_charges['+detain_door_count+']"></fieldset></div><div class="col-1">\n' +
                '<span id="detain_weight_close" class="btn btn-danger rounded btn-sm-width mr-1 mb-1 detain_weight_close"><i class="ft-x"></i></span></div></div>';
            $('.weight-addition-detain').append(htmdiv1);

            var switches = document.querySelector('.switchery.weightAdditionDoorDetain'+detain_door_count);
            var switchery = new Switchery(switches, { disabled: false,color: '#37BC9B',size:'small' });
            $(".touchspin-color").TouchSpin({
                min: 0.5,
                max: 100,
                step: 0.5,
                decimals: 2,
                buttondown_class: "btn btn-success",
                buttonup_class: "btn btn-success",
                buttondown_txt: '<i class="ft-minus"></i>',
                buttonup_txt: '<i class="ft-plus"></i>'
            });

            masks();


            $("#detain_door_weight_row"+detain_door_count+" .validated").each(function(){
                $( this ).rules( "add", {
                    required: true,
                });

            });
            detain_door_count++;
        });
        var detain_hub_count = $('.detain_hub_weight_row').length + 1;
        $('body').on('click','#detain_hub_weightadd',function () {
            var row_count = detain_hub_count-1;
            var detain_hub_range_down = parseFloat($('#detain_hub_range_down' + row_count).val());
            var detain_hub_new_range_down = detain_hub_range_down + 0.01;

            let htmdiv1 = '<div class="row detain_hub_weight_row" id="detain_hub_weight_row'+detain_hub_count+'"><div class="col text-center"><fieldset class="form-group"><input type="text" id="detain_hub_range_up'+detain_hub_count+'" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="'+detain_hub_new_range_down+'" name="detain_hub_range_up['+detain_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" id="detain_hub_range_down'+detain_hub_count+'" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_hub_range_down['+detain_hub_count+']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionHubDetain'+detain_hub_count+'" data-color="success" data-size="sm" name="detain_hub_wa_switch['+detain_hub_count+']"/></div></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_hub_local_charges['+detain_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="detain_hub_same_zone_charges['+detain_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="detain_hub_different_zone_charges['+detain_hub_count+']"></fieldset></div><div class="col-1">\n' +
                '<span id="detain_weight_close" class="btn btn-danger rounded btn-sm-width mr-1 mb-1 detain_weight_close"><i class="ft-x"></i></span></div></div>';
            $('.hub-weight-addition-detain').append(htmdiv1);

            var switches = document.querySelector('.switchery.weightAdditionHubDetain'+detain_hub_count);
            var switchery = new Switchery(switches, { disabled: false,color: '#37BC9B',size:'small' });
            $(".touchspin-color").TouchSpin({
                min: 0.5,
                max: 100,
                step: 0.5,
                decimals: 2,
                buttondown_class: "btn btn-success",
                buttonup_class: "btn btn-success",
                buttondown_txt: '<i class="ft-minus"></i>',
                buttonup_txt: '<i class="ft-plus"></i>'
            });

            masks();


            $("#detain_hub_weight_row"+detain_hub_count+" .validated").each(function(){
                $( this ).rules( "add", {
                    required: true,
                });

            });
            detain_hub_count++;
        });

        //addMoreSlabs
        $('body').on('click','.detain_weight_close',function () {
            $(this).parent().parent().remove();
        });
        $('body').on('click','.detain_row_delete',function () {
            $(this).parent().parent().remove();
        });
        var detain_slab_count = 5;
        $('body').on('click','#detainaddMoreSlabs',function () {
            let htmdiv = '<div class="row" id="detain_cash_handle_'+detain_slab_count+'">\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="detain_cash_range_up['+detain_slab_count+']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="detain_cash_range_down['+detain_slab_count+']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '                                                <div class="col-md-2">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="detain_cash_charges['+detain_slab_count+']" type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '<div class="col">\n' +
                '<span class="detain_row_delete btn btn-danger rounded btn-sm-width mr-1 mb-1"><i class="ft-x"></i></span></div></div>';
            $('.cash-handling-div-detain').append(htmdiv);
            detain_slab_count++;
            masks();
            // $(this).parent().prev().find('div.slabs').append(htmdiv);
            // console.log();

        });
        //add more slabs insurance
        var detain_ins_count = 3;
        $('body').on('click','#detainaddMoreSlabsInsurance',function () {
            let htmdiv = '<div class="row" id="detain_insurance_charge_'+detain_ins_count+'">\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="detain_ins_range_up['+detain_ins_count+']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="detain_ins_range_down['+detain_ins_count+']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="detain_ins_charges['+detain_ins_count+']" type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '<div class="col">\n' +
                '<span  class="detain_row_delete btn btn-danger rounded btn-sm-width mr-1 mb-1"><i class="ft-x"></i></span></div></div>';
            $('.insurance-charges-div-detain').append(htmdiv);
            detain_ins_count++;
            masks();
            // $(this).parent().prev().find('div.slabs').append(htmdiv);
            // console.log();

        });
        //Cash handling
        // cashChargesOvernight
        cashhandlingswitchDetain.onchange = function () {
            if(cashhandlingswitchDetain.checked === true){
                // $('.cash-handling-div').
                $('.cash-handling-div-detain').find('input').prop('disabled',false);
                $('.cash-handling-btn-detain').find('button').prop('disabled',false);
            }else if(cashhandlingswitchDetain.checked === false){
                $('.cash-handling-div-detain').find('input').prop('disabled',true);
                $('.cash-handling-btn-detain').find('button').prop('disabled',true);

            }
        };

        // InsuranceOvernight
        insuranceChargesSwitchDetain.onchange = function () {
            if(insuranceChargesSwitchDetain.checked === true){
                // $('.cash-handling-div').
                $('.insurance-charges-div-detain').find('input').prop('disabled',false);
                $('.insurance-charges-btn-detain').find('button').prop('disabled',false);
            }else if(insuranceChargesSwitchDetain.checked === false){
                $('.insurance-charges-div-detain').find('input').prop('disabled',true);
                $('.insurance-charges-btn-detain').find('button').prop('disabled',true);

            }
        };
        // Return Overnight
        returnChargesSwitchDetain.onchange = function () {
            if(returnChargesSwitchDetain.checked === true){
                $('.return-charges-div-detain').find('input').prop('disabled',false);
            }else if(returnChargesSwitchDetain.checked === false){
                $('.return-charges-div-detain').find('input').prop('disabled',true);

            }
        };
        fuelChargesSwitchDetain.onchange = function () {
            if(fuelChargesSwitchDetain.checked === true){
                $('.fuel-surcharge-div-detain').find('input').prop('disabled',false);
            }else if(fuelChargesSwitchDetain.checked === false){
                $('.fuel-surcharge-div-detain').find('input').prop('disabled',true);

            }
        };


        //Detain end
        //sameday start
        //var weightAdditionSameday = document.querySelector('.switchery.weightAdditionSameday0');
        var cashhandlingswitchSameday = document.querySelector('.switchery.cashChargesSameday');
        var insuranceChargesSwitchSameday = document.querySelector('.switchery.insuranceChargessameday');
        var returnChargesSwitchSameday = document.querySelector('.switchery.returnChargesSameday');
        var fuelChargesSwitchSameday = document.querySelector('.switchery.fuelSurchargeSameday');

        //detain


        var sameday_door_count = $('.sameday_door_weight_row').length + 1;
        $('body').on('click','#sameday_door_weightadd',function () {
            var row_count = sameday_door_count-1;
            var sameday_door_range_down = parseFloat($('#sameday_door_range_down' + row_count).val());
            var sameday_door_new_range_down = sameday_door_range_down + 0.01;

            let htmdiv1 = '<div class="row" id="sameday_door_weight_row'+sameday_door_count+'"><div class="col text-center"><fieldset class="form-group"><input type="text" id="sameday_door_range_up'+sameday_door_count+'" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="'+sameday_door_new_range_down+'" name="sameday_door_range_up['+sameday_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" id="sameday_door_range_down'+sameday_door_count+'" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_door_range_down['+sameday_door_count+']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionDoorSameday'+sameday_door_count+'" data-color="success" data-size="sm" name="sameday_door_wa_switch['+sameday_door_count+']"/></div></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_door_local_charges['+sameday_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="sameday_door_same_zone_charges['+sameday_door_count+']"></fieldset></div><div class="col-1">\n' +
                '<span id="sameday_weight_close" class="btn btn-danger rounded btn-sm-width mr-1 mb-1 sameday_weight_close"><i class="ft-x"></i></span></div></div>';
            $('.weight-addition-sameday').append(htmdiv1);

            var switches = document.querySelector('.switchery.weightAdditionDoorSameday'+sameday_door_count);
            var switchery = new Switchery(switches, { disabled: false,color: '#37BC9B',size:'small' });
            $(".touchspin-color").TouchSpin({
                min: 0.5,
                max: 100,
                step: 0.5,
                decimals: 2,
                buttondown_class: "btn btn-success",
                buttonup_class: "btn btn-success",
                buttondown_txt: '<i class="ft-minus"></i>',
                buttonup_txt: '<i class="ft-plus"></i>'
            });

            masks();


            $("#sameday_door_weight_row"+sameday_door_count+" .validated").each(function(){
                $( this ).rules( "add", {
                    required: true,
                });

            });
            sameday_door_count++;
        });
        var sameday_hub_count = $('.sameday_hub_weight_row').length + 1;
        $('body').on('click','#sameday_hub_weightadd',function () {
            var row_count = sameday_hub_count-1;
            var sameday_hub_range_down = parseFloat($('#sameday_hub_range_down' + row_count).val());
            var sameday_hub_new_range_down = sameday_hub_range_down + 0.01;

            let htmdiv1 = '<div class="row sameday_hub_weight_row" id="sameday_hub_weight_row'+sameday_hub_count+'"><div class="col text-center"><fieldset class="form-group"><input type="text" id="sameday_hub_range_up'+sameday_hub_count+'" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="'+sameday_hub_new_range_down+'" name="sameday_hub_range_up['+sameday_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" id="sameday_hub_range_down'+sameday_hub_count+'" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_hub_range_down['+sameday_hub_count+']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionHubSameday'+sameday_hub_count+'" data-color="success" data-size="sm" name="sameday_hub_wa_switch['+sameday_hub_count+']"/></div></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_hub_local_charges['+sameday_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="sameday_hub_same_zone_charges['+sameday_hub_count+']"></fieldset></div><div class="col-1">\n' +
                '<span id="sameday_weight_close" class="btn btn-danger rounded btn-sm-width mr-1 mb-1 sameday_weight_close"><i class="ft-x"></i></span></div></div>';
            $('.hub-weight-addition-sameday').append(htmdiv1);

            var switches = document.querySelector('.switchery.weightAdditionHubSameday'+sameday_hub_count);
            var switchery = new Switchery(switches, { disabled: false,color: '#37BC9B',size:'small' });
            $(".touchspin-color").TouchSpin({
                min: 0.5,
                max: 100,
                step: 0.5,
                decimals: 2,
                buttondown_class: "btn btn-success",
                buttonup_class: "btn btn-success",
                buttondown_txt: '<i class="ft-minus"></i>',
                buttonup_txt: '<i class="ft-plus"></i>'
            });

            masks();
            $("#sameday_hub_weight_row"+sameday_hub_count+" .validated").each(function(){
                $( this ).rules( "add", {
                    required: true,
                });

            });
            sameday_hub_count++;
        });

        //addMoreSlabs
        $('body').on('click','.sameday_weight_close',function () {
            $(this).parent().parent().remove();
        });
        $('body').on('click','.sameday_row_delete',function () {
            $(this).parent().parent().remove();
        });
        var sameday_slab_count = 5;
        $('body').on('click','#samedayaddMoreSlabs',function () {
            let htmdiv = '<div class="row" id="sameday_cash_handle_'+sameday_slab_count+'">\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="sameday_cash_range_up['+sameday_slab_count+']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="sameday_cash_range_down['+sameday_slab_count+']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '                                                <div class="col-md-2">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="sameday_cash_charges['+sameday_slab_count+']" type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '<div class="col">\n' +
                '<span class="sameday_row_delete btn btn-danger rounded btn-sm-width mr-1 mb-1"><i class="ft-x"></i></span></div></div>';
            $('.cash-handling-div-sameday').append(htmdiv);
            sameday_slab_count++;
            masks();
            // $(this).parent().prev().find('div.slabs').append(htmdiv);
            // console.log();

        });
        //add more slabs insurance
        var sameday_ins_count = 3;
        $('body').on('click','#samedayaddMoreSlabsInsurance',function () {
            let htmdiv = '<div class="row" id="sameday_insurance_charge_'+sameday_ins_count+'">\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="sameday_ins_range_up['+sameday_ins_count+']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="sameday_ins_range_down['+sameday_ins_count+']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '\n' +
                '                                                <div class="col-md-2 text-center">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="sameday_ins_charges['+sameday_ins_count+']" type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '<div class="col">\n' +
                '<span  class="sameday_row_delete btn btn-danger rounded btn-sm-width mr-1 mb-1"><i class="ft-x"></i></span></div></div>';
            $('.insurance-charges-div-sameday').append(htmdiv);
            sameday_ins_count++;
            masks();
            // $(this).parent().prev().find('div.slabs').append(htmdiv);
            // console.log();

        });
        //Cash handling
        // cashChargesOvernight
        cashhandlingswitchSameday.onchange = function () {
            if(cashhandlingswitchSameday.checked === true){
                // $('.cash-handling-div').
                $('.cash-handling-div-sameday').find('input').prop('disabled',false);
                $('.cash-handling-btn-sameday').find('button').prop('disabled',false);
            }else if(cashhandlingswitchSameday.checked === false){
                $('.cash-handling-div-sameday').find('input').prop('disabled',true);
                $('.cash-handling-btn-sameday').find('button').prop('disabled',true);

            }
        };

        // InsuranceOvernight
        insuranceChargesSwitchSameday.onchange = function () {
            if(insuranceChargesSwitchSameday.checked === true){
                // $('.cash-handling-div').
                $('.insurance-charges-div-sameday').find('input').prop('disabled',false);
                $('.insurance-charges-btn-sameday').find('button').prop('disabled',false);
            }else if(insuranceChargesSwitchSameday.checked === false){
                $('.insurance-charges-div-sameday').find('input').prop('disabled',true);
                $('.insurance-charges-btn-sameday').find('button').prop('disabled',true);

            }
        };
        // Return Overnight
        returnChargesSwitchSameday.onchange = function () {
            if(returnChargesSwitchSameday.checked === true){
                $('.return-charges-div-sameday').find('input').prop('disabled',false);
            }else if(returnChargesSwitchSameday.checked === false){
                $('.return-charges-div-sameday').find('input').prop('disabled',true);

            }
        };
        fuelChargesSwitchSameday.onchange = function () {
            if(fuelChargesSwitchSameday.checked === true){
                $('.fuel-surcharge-div-sameday').find('input').prop('disabled',false);
            }else if(fuelChargesSwitchSameday.checked === false){
                $('.fuel-surcharge-div-sameday').find('input').prop('disabled',true);

            }
        };

        //Detain end
        //end sameday
        //for discounts Overnight
        var ondiscountSwitch = Array.prototype.slice.call(document.querySelectorAll('.discountSwitchesOvernight'));


        ondiscountSwitch[0].onchange = function () {
            ONdiscount(ondiscountSwitch[0]);
        };
        ondiscountSwitch[1].onchange = function () {
            ONdiscount(ondiscountSwitch[1]);
        };
        ondiscountSwitch[2].onchange = function () {
            ONdiscount(ondiscountSwitch[2]);
        };
        ondiscountSwitch[3].onchange = function () {
            ONdiscount(ondiscountSwitch[3]);
        };
        // ondiscountSwitch[4].onchange = function () {
        //     ONdiscount(ondiscountSwitch[4]);
        // };

        function ONdiscount(eve) {
            if(eve.checked === true){

                $(eve).parent().parent().next().prop('disabled',false);
                $('input[name="on_discount_title"]').prop('disabled',false);
                $('input[name="on_daterange"]').prop('disabled',false);

            }else if(eve.checked === false){
                $(eve).parent().parent().next().prop('disabled',true);

                if(ondiscountSwitch[0].checked === true || ondiscountSwitch[1].checked === true || ondiscountSwitch[2].checked === true || ondiscountSwitch[3].checked === true){
                    $('input[name="on_discount_title"]').prop('disabled',false);
                    $('input[name="on_daterange"]').prop('disabled',false);
                }else{
                    $('input[name="on_discount_title"]').prop('disabled',true);
                    $('input[name="on_daterange"]').prop('disabled',true);
                }

            }
        }
        //for discounts Overland
        var overlandDiscountSwitch = Array.prototype.slice.call(document.querySelectorAll('.discountSwitchesOverland'));


        overlandDiscountSwitch[0].onchange = function () {
            OLdiscount(overlandDiscountSwitch[0]);
        };
        overlandDiscountSwitch[1].onchange = function () {
            OLdiscount(overlandDiscountSwitch[1]);
        };
        overlandDiscountSwitch[2].onchange = function () {
            OLdiscount(overlandDiscountSwitch[2]);
        };
        overlandDiscountSwitch[3].onchange = function () {
            OLdiscount(overlandDiscountSwitch[3]);
        };

        function OLdiscount(eveOver) {
            if(eveOver.checked === true){

                $(eveOver).parent().parent().next().prop('disabled',false);
                $('input[name="ol_discount_title"]').prop('disabled',false);
                $('input[name="ol_daterange"]').prop('disabled',false);

            }else if(eveOver.checked === false){
                $(eveOver).parent().parent().next().prop('disabled',true);

                if(overlandDiscountSwitch[0].checked === true || overlandDiscountSwitch[1].checked === true || overlandDiscountSwitch[2].checked === true || overlandDiscountSwitch[3].checked === true){
                    $('input[name="ol_discount_title"]').prop('disabled',false);
                    $('input[name="ol_daterange"]').prop('disabled',false);
                }else{
                    $('input[name="ol_discount_title"]').prop('disabled',true);
                    $('input[name="ol_daterange"]').prop('disabled',true);
                }

            }
        }

        //for discounts Overland
        var detainDiscountSwitch = Array.prototype.slice.call(document.querySelectorAll('.discountSwitchesDetain'));


        detainDiscountSwitch[0].onchange = function () {
            Detaindiscount(detainDiscountSwitch[0]);
        };
        detainDiscountSwitch[1].onchange = function () {
            Detaindiscount(detainDiscountSwitch[1]);
        };
        detainDiscountSwitch[2].onchange = function () {
            Detaindiscount(detainDiscountSwitch[2]);
        };
        detainDiscountSwitch[3].onchange = function () {
            Detaindiscount(detainDiscountSwitch[3]);
        };

        function Detaindiscount(eveDet) {
            if(eveDet.checked === true){

                $(eveDet).parent().parent().next().prop('disabled',false);
                $('input[name="detain_discount_title"]').prop('disabled',false);
                $('input[name="detain_daterange"]').prop('disabled',false);

            }else if(eveDet.checked === false){
                $(eveDet).parent().parent().next().prop('disabled',true);

                if(detainDiscountSwitch[0].checked === true || detainDiscountSwitch[1].checked === true || detainDiscountSwitch[2].checked === true || detainDiscountSwitch[3].checked === true){
                    $('input[name="detain_discount_title"]').prop('disabled',false);
                    $('input[name="detain_daterange"]').prop('disabled',false);
                }else{
                    $('input[name="detain_discount_title"]').prop('disabled',true);
                    $('input[name="detain_daterange"]').prop('disabled',true);
                }

            }
        }
        //for discounts Overland
        var samedayDiscountSwitch = Array.prototype.slice.call(document.querySelectorAll('.discountSwitchesSameday'));


        samedayDiscountSwitch[0].onchange = function () {
            SamedayDiscount(samedayDiscountSwitch[0]);
        };
        samedayDiscountSwitch[1].onchange = function () {
            SamedayDiscount(samedayDiscountSwitch[1]);
        };
        samedayDiscountSwitch[2].onchange = function () {
            SamedayDiscount(samedayDiscountSwitch[2]);
        };
        samedayDiscountSwitch[3].onchange = function () {
            SamedayDiscount(samedayDiscountSwitch[3]);
        };

        function SamedayDiscount(eveSameday) {
            if(eveSameday.checked === true){

                $(eveSameday).parent().parent().next().prop('disabled',false);
                $('input[name="sameday_discount_title"]').prop('disabled',false);
                $('input[name="sameday_daterange"]').prop('disabled',false);

            }else if(eveSameday.checked === false){
                $(eveSameday).parent().parent().next().prop('disabled',true);

                if(samedayDiscountSwitch[0].checked === true || samedayDiscountSwitch[1].checked === true || samedayDiscountSwitch[2].checked === true || samedayDiscountSwitch[3].checked === true ){
                    $('input[name="sameday_discount_title"]').prop('disabled',false);
                    $('input[name="sameday_daterange"]').prop('disabled',false);
                }else{
                    $('input[name="sameday_discount_title"]').prop('disabled',true);
                    $('input[name="sameday_daterange"]').prop('disabled',true);
                }

            }
        }



        //Warehousing

        $('#warehouse_main_switch').on('change',function(){
                var warehousemainswitch = document.querySelector('.switchery.warehouse-main-switch');
                if (warehousemainswitch.checked === true) {
                    $('#warehousing').slideDown('slow');

                } else if (warehousemainswitch.checked === false) {
                    $('#warehousing').slideUp('slow');


                }
            });
        var weekly = [1, 2, 3, 4, 5, 6, 7];
        var monthly = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28];
        $('#invoicing_cycle_select').prepend('<option value="" selected></option>').select2({
            placeholder: "Select Invoicing Cycle",
            width:'100%'
        });
        var current_selection = null;
        var storage_type_selected = [];
        var storage_type_data = @json($storage_types);

        var storage_data = $.map(storage_type_data, function (obj) {
                obj.id = obj.id;
                obj.text = obj.name;
                return obj;
            });


        $('select[name="storage_type[0]"]').prepend('<option value="" selected="selected"></option>').select2({
            data:storage_data,
            width:'100%',
            placeholder:'Select Storage Type'
        }).bind('select2:select', function(){
            
            $(this).parents('div.storage_type_row').find('span#storage_type_add').removeClass('d-none');
            $('input[name="storage_type[0]"]').val($(this).val());

            
        });

        var PPCSwitch = document.querySelector('.switchery.PPCSwitch');
        PPCSwitch.onchange = function () {
            if(PPCSwitch.checked === true){
                $('input[name="ppc_charges"]').prop('disabled', false);
            }else if(PPCSwitch.checked === false){
                $('input[name="ppc_charges"]').prop('disabled', true);
            }
        };
        var PSFSwitch = document.querySelector('.switchery.PSFSwitch');
        PSFSwitch.onchange = function () {
            if(PSFSwitch.checked === true){
                $('input[name="psf_charges"]').prop('disabled', false);
            }else if(PSFSwitch.checked === false){
                $('input[name="psf_charges"]').prop('disabled', true);
            }
        };
        var StorageSwitch = document.querySelector('.switchery.storageCharges');
        StorageSwitch.onchange = function () {
            if(StorageSwitch.checked === true){
                $('#wms_storage_types_div select, #wms_storage_types_div input').prop('disabled', false);
            }else if(StorageSwitch.checked === false){
                $('#wms_storage_types_div select, #wms_storage_types_div input').prop('disabled', true);
            }
        };
        var LabellingSwitch = document.querySelector('.switchery.labellingSwitch');
        LabellingSwitch.onchange = function () {
            if(LabellingSwitch.checked === true){
                $('input[name="labelling_charges"]').prop('disabled', false);
            }else if(LabellingSwitch.checked === false){
                $('input[name="labelling_charges"]').prop('disabled', true);
            }
        };
        

        var storage_type_rows = 1;
        
        $('body').on('click','#storage_type_add', function(){
            var previous_row = storage_type_rows - 1;
              
            $(this).addClass('d-none');
            var previous_select = $('select[name="storage_type['+ previous_row +']"]');
            storage_type_selected.push(previous_select.val());
            previous_select.prop('disabled', true);
            var storage_data_new = $.map(storage_type_data, function (obj) {
                var current_id = obj.id.toString();
                var index = $.inArray(current_id, storage_type_selected);
                
                if(index === -1){
                    obj.id = obj.id;
                    obj.text = obj.name;
                    return obj;
                }
                
            });
            if(storage_data_new.length !== 0){
                var htmldiv = '<div class="row storage_type_row" id="storage_type_row'+storage_type_rows+'">\n' +
                '                                                <input id="storage_type_input'+ storage_type_rows +'" type="hidden" name="storage_type['+ storage_type_rows +']" value=""><div class="col-md-2 st_select">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <select class="select2 form-control storage_type" name="storage_type['+ storage_type_rows +']" data-rule-required="true" data-msg-required="This field is required"></select>\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '                                                <div class="col-md-2">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="storage_type_charges['+storage_type_rows+']" type="text" class="form-control validated" data-rule-required="true" data-msg-required="Charges are required" placeholder="Charges">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '<div class="col"><span id="storage_type_add" class="btn btn-sm btn-outline-primary d-none"><i class="la la-check"></i></span>\n'+
                '<span row="'+ storage_type_rows +'" class="storage_type_row_delete btn btn-sm btn-outline-danger"><i class="la la-trash"></i></span></div>';

            $('#wms_storage_types_div').append(htmldiv);

            
            var last_id = storage_type_rows;
            $('select[name="storage_type['+ storage_type_rows +']"]').prepend('<option value="" selected="selected"></option>').select2({
                data:storage_data_new,
                width:'100%',
                placeholder:'Select Storage Type'
            }).on('change', function(){
                var selected_id = $(this).val();
                $(this).parents('div.storage_type_row').find('span#storage_type_add').removeClass('d-none');
                $(this).parents('div.storage_type_row').find('input#storage_type_input'+last_id).val(selected_id);
            });
            $('input[name="storage_type_charges['+storage_type_rows+']"]').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'min': 0,
                'max': 1000000
            });

            storage_type_rows++;
            }


        });

        $('body').on('click','span.storage_type_row_delete', function(){
            var row_id = $(this).attr('row');
            var row_selector = $('#storage_type_row'+row_id);
            var selected = $('input[name="storage_type['+ row_id +']"]').val();
            if(selected !== ''){
                var index = $.inArray(selected, storage_type_selected);
                if(index !== -1){
                    storage_type_selected.splice(index, 1);
                }
            }
            row_selector.remove();
        });

        var packing_sizes = @json($packaging_material_type_sizes);
        var packing_type_rows = 1;
        var packing_type_selected = [];
        var packing_size_selected = [];
        var packing_material_data = @json($packaging_material_types);
        var packing_data = $.map(packing_material_data, function (obj) {
                obj.id = obj.id;
                obj.text = obj.type;
                return obj;
            });

        $('select[name="packing_type[0]"]').prepend('<option value="" selected="selected"></option>').select2({
                data:packing_data,
                width:'100%',
                placeholder:'Select Packing Type'
            }).bind('select2:select', function(){
                var packing_id = $(this).val();
                $('input[name="packing_type[0]"]').val(packing_id);
                
                var packing_sizes_data = $.map(packing_sizes[packing_id], function (obj) {
                obj.id = obj.id;
                obj.text = obj.size;
                return obj;
                });
                $('select[name="packing_size[0]"]').empty().select2({data:packing_sizes_data, placeholder: 'Select Packing Size'}).val(null).trigger('change');
            });

            $('select[name="packing_size[0]"]').select2({
                width:'100%',
                placeholder:'Select Packing Size'
            }).bind('select2:select', function(){
                var packing_size = $(this).val();
                $('input[name="packing_size[0]"]').val(packing_size);
                $(this).parents('div.packing_type_row').find('span#packing_type_add').removeClass('d-none');

            });

        var packingSwitch = document.querySelector('.switchery.packingCharges');
        packingSwitch.onchange = function () {
            if(packingSwitch.checked === true){
                $('#wms_packing_charges_div input, #wms_packing_charges_div select').prop('disabled', false);
            }else if(packingSwitch.checked === false){
                $('#wms_packing_charges_div input, #wms_packing_charges_div select').prop('disabled', true);
            }
        };

        var packing_data = $.map(packing_material_data, function (obj) {
                obj.id = obj.id;
                obj.text = obj.type;
                return obj;
            });
        
        $('body').on('click','#packing_type_add', function(){
            var previous_row = packing_type_rows - 1;
            $(this).addClass('d-none');

            var previous_type = $('select[name="packing_type['+ previous_row +']"]');
            var previous_size = $('select[name="packing_size['+ previous_row +']"]');
            
            packing_size_selected.push(previous_size.val());
            previous_type.prop('disabled', true);
            previous_size.prop('disabled', true);

            
            if(packing_data.length !== 0){
                var htmldiv = '<div class="row packing_type_row" id="packing_type_row'+packing_type_rows+'">\n' +
                '                                                <input id="packing_type_input'+ packing_type_rows +'" type="hidden" name="packing_type['+ packing_type_rows +']" value=""><div class="col-md-2">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <select class="select2 form-control storage_type" name="packing_type['+ packing_type_rows +']" data-rule-required="true" data-msg-required="This field is required"></select>\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '                                                <input id="packing_size_input'+ packing_type_rows +'" type="hidden" name="packing_size['+ packing_type_rows +']" value=""><div class="col-md-2">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <select class="select2 form-control storage_type" name="packing_size['+ packing_type_rows +']" data-rule-required="true" data-msg-required="This field is required"></select>\n' +
                '                                                    </fieldset></div>\n' +
                '                                                <div class="col-md-2">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <input name="packing_charges['+packing_type_rows+']" type="text" class="form-control validated" data-rule-required="true" data-msg-required="Charges are required" placeholder="Charges">\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '<div class="col"><span id="packing_type_add" class="btn btn-sm btn-outline-primary d-none"><i class="la la-check"></i></span>\n' +
                '<span row="'+ packing_type_rows +'" class="packing_type_row_delete btn btn-sm btn-outline-danger"><i class="la la-trash"></i></span></div></div>';

            $('#wms_packing_charges_div').append(htmldiv);
            
            
            var last_id = packing_type_rows;
            $('select[name="packing_type['+ packing_type_rows +']"]').prepend('<option value="" selected="selected"></option>').select2({
                data:packing_data,
                width:'100%',
                placeholder:'Select Packing Type'
            }).on('select2:select', function(){
                var packing_id = $(this).val();
                $(this).parents('div.packing_type_row').find('input#packing_type_input'+last_id).val(packing_id);
                var packing_sizes_data = $.map(packing_sizes[packing_id], function (obj) {
                    var current_id = obj.id.toString();
                    var index = $.inArray(current_id, packing_size_selected);
                    if(index === -1){
                        obj.id = obj.id;
                        obj.text = obj.size;
                        return obj;
                    }  
                });
                $('select[name="packing_size['+ last_id +']"]').empty().select2({data:packing_sizes_data, placeholder: 'Select Storage Size'}).val(null).trigger('change');
            });
            $('select[name="packing_size['+ packing_type_rows +']"]').select2({
                width:'100%',
                placeholder:'Select Packing Type'
            }).on('select2:select', function(){
                var packing_size = $(this).val();
                $(this).parents('div.packing_type_row').find('input#packing_size_input'+last_id).val(packing_size);
                $(this).parents('div.packing_type_row').find('span#packing_type_add').removeClass('d-none');
            });
            $('input[name="packing_charges['+packing_type_rows+']"]').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 2,
                'min': 0.00,
                'max': 1000000.00
            });

            packing_type_rows++;
            }
            

        });

        $('body').on('click','span.packing_type_row_delete', function(){
            var row_id = $(this).parent().parent().attr('row');
            var selected = $('select[name="packing_type['+ row_id +']"]').val();
            if(selected !== ''){
                var index = $.inArray(selected, packing_type_selected);
                if(index !== -1){
                    packing_type_selected.splice(index, 1);
                }
            }
            $(this).parent().parent().remove();
        });

        $('#ratesAdditionForm').on('keypress',function (e) {
            if(e.which == 13 || e.keyCode == 13) {
                e.preventDefault();
            }
        });


        //Origin And Destination Hubs Start
        $('#on_origin_hubs').select2({
            width:'100%',
            placeholder:"Origin(s)",
            allowClear:true
        });

        $('#on_destination_hubs').select2({
            width:'100%',
            placeholder:"Destination(s)",
            allowClear:true
        });

        $('#ol_origin_hubs').select2({
            width:'100%',
            placeholder:"Origin(s)",
            allowClear:true
        });

        $('#ol_destination_hubs').select2({
            width:'100%',
            placeholder:"Destination(s)",
            allowClear:true
        });

        $('#detain_origin_hubs').select2({
            width:'100%',
            placeholder:"Origin(s)",
            allowClear:true
        });

        $('#detain_destination_hubs').select2({
            width:'100%',
            placeholder:"Destination(s)",
            allowClear:true
        });

        $('#sameday_origin_hubs').select2({
            width:'100%',
            placeholder:"Origin(s)",
            allowClear:true
        });

        $('#sameday_destination_hubs').select2({
            width:'100%',
            placeholder:"Destination(s)",
            allowClear:true
        });


        //Origin And Destination Hubs End


        var overnightSwitch = document.querySelector('.switchery.on-main-switch');
        var overlandSwitch = document.querySelector('.switchery.ol-main-switch');
        var detainSwitch = document.querySelector('.switchery.detain-main-switch');
        var samedaySwitch = document.querySelector('.switchery.sameday-main-switch');

        $( "#ratesAdditionForm" ).validate({
            errorClass:"danger",
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
            submitHandler: function(form) {
                if (overnightSwitch.checked == true || overlandSwitch.checked == true || detainSwitch.checked == true || samedaySwitch.checked == true) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    if(overnightSwitch.checked == true){
                        $('#overnight input.weight_range').prop('disabled', false);
                    }
                    if(overlandSwitch.checked == true){
                        $('#overland input.weight_range').prop('disabled', false);
                    }
                    if(detainSwitch.checked == true){
                        $('#detain input.weight_range').prop('disabled', false);
                    }
                    if(samedaySwitch.checked == true){
                        $('#sameday input.weight_range').prop('disabled', false);
                    }
                    swal({
                        title: 'Please Wait!',
                        text: 'Your rates are being added!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
                else {
                    swal({
                        title: 'No Shipping Mode Selected!',
                        text: 'At-least one shipping mode is required!',
                        icon: 'warning'
                    });

                }
            }
        });

    </script>
@endsection