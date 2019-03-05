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
                        <form id="ratesAdditionForm" class="card-body card-dashboard" action="{{route('admin.corporate.add.rates',['id'=>$shipper->id])}}" method="post" novalidate="novalidate">
                            @csrf
                            <div id="" class="card-header border-success">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3 class="card-title lead success">Overnight</h3>
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
                                                    <label class="card-title">Flat Charges/KG (Local)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (National-Zone A)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (National-Zone B)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (National-Zone C)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (National-Zone D)</label>
                                                </div>
                                                <div class="col-1"></div>
                                            </div>

                                            @php
                                                $index_row = 0;
                                            @endphp
                                            @foreach($weight[1] as $index => $onweight)
                                                @if($onweight->delivery_type_id == 1)

                                                <div class="row on_door_weight_row" id="">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->range_up}}" name="on_door_range_up[{{$index_row}}]" {{ (($index_row == 0 || $index_row == 1) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->range_down}}" name="on_door_range_down[{{$index_row}}]" {{ (($index_row == 0) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->local_or_6hr}}" name="on_door_local_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->national_charges_class_0}}" name="on_door_class_0_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->national_charges_class_1}}" name="on_door_class_1_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->national_charges_class_2}}" name="on_door_class_2_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->national_charges_class_3}}" name="on_door_class_3_charges[{{$index_row}}]">
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
                                        <div class="hub-weight-addition-overnight">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Hub-Hub Delivery</h3>
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
                                                    <label class="card-title">Flat Charges/KG (Local)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (National-Zone A)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (National-Zone B)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (National-Zone C)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (National-Zone D)</label>
                                                </div>
                                                <div class="col-1"></div>
                                            </div>
                                            @php
                                                $index_row = 0;
                                            @endphp
                                            @foreach($weight[1] as $index => $onweight)
                                                @if($onweight->delivery_type_id == 2)
                                                <div class="row on_hub_weight_row" id="">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->range_up}}" name="on_hub_range_up[{{$index_row}}]" {{ (($index_row == 0 || $index_row == 1) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->range_down}}" name="on_hub_range_down[{{$index_row}}]" {{ (($index_row == 0) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->local_or_6hr}}" name="on_hub_local_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->national_charges_class_0}}" name="on_hub_class_0_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->national_charges_class_1}}" name="on_hub_class_1_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->national_charges_class_2}}" name="on_hub_class_2_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->national_charges_class_3}}" name="on_hub_class_3_charges[{{$index_row}}]">
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
                                        </div>{{--weight addition div--}}
                                        <div>
                                            <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="hub_waddition_btn"><i class="la la-plus"></i></button>
                                        </div>

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

                                        <div class="row return-charges-div-overnight">

                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Local Charges</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control amount" name="on_return_local_charges"  value="{{$returnCharges[1][0]->local}}">
                                                </fieldset>
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="card-title">National Charges</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control amount" name="on_return_national_charges"  value="{{$returnCharges[1][0]->national}}">
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
                                        <h3 class="card-title lead success">Overland</h3>
                                    </div>
                                    <div class="col-md-6">
                                        <a id="ol_main_switch" href="javascript:void(0);" class="pull-right"><input name="ol_main_switch" type="checkbox" id="" class="switchery ol-main-switch" data-size="sm"/></a>
                                    </div>
                                </div>

                            </div>
                            <div id="overland" class="border-success no-border-top card hide" aria-expanded="false">
                                <div class="card-content">
                                    <div class="card-body">
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
                                                    <label class="card-title">Flat Charges/KG (Local)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (National-Zone A)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (National-Zone B)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (National-Zone C)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (National-Zone D)</label>
                                                </div>
                                                <div class="col-1"></div>
                                            </div>
                                            @php
                                                $index_row = 0;
                                            @endphp
                                            @foreach($weight[2] as $index => $olweight)
                                                @if($olweight->delivery_type_id == 1)
                                                <div class="row ol_door_weight_row">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->range_up}}" name="ol_door_range_up[{{$index_row}}]" {{ (($index_row == 0 || $index_row == 1) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->range_down}}" name="ol_door_range_down[{{$index_row}}]" {{ (($index_row == 0) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->local_or_6hr}}" name="ol_door_local_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->national_charges_class_0}}" name="ol_door_class_0_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div> <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->national_charges_class_1}}" name="ol_door_class_1_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->national_charges_class_2}}" name="ol_door_class_2_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->national_charges_class_3}}" name="ol_door_class_3_charges[{{$index_row}}]">
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
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Hub-Hub Delivery</h3>
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
                                                    <label class="card-title">Flat Charges/KG (Local)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (National-Zone A)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (National-Zone B)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (National-Zone C)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (National-Zone D)</label>
                                                </div>
                                                <div class="col-1"></div>
                                            </div>
                                            @php
                                                $index_row = 0;
                                            @endphp
                                            @foreach($weight[2] as $index => $olweight)
                                                @if($olweight->delivery_type_id == 2)
                                                <div class="row ol_hub_weight_row">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->range_up}}" name="ol_hub_range_up[{{$index_row}}]" {{ (($index_row == 0 || $index_row == 1) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->range_down}}" name="ol_hub_range_down[{{$index_row}}]" {{ (($index_row == 0) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->local_or_6hr}}" name="ol_hub_local_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->national_charges_class_0}}" name="ol_hub_class_0_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div> <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->national_charges_class_1}}" name="ol_hub_class_1_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->national_charges_class_2}}" name="ol_hub_class_2_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->national_charges_class_3}}" name="ol_hub_class_3_charges[{{$index_row}}]">
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

                                        <div class="row return-charges-div-overland">

                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Local Charges</label>
                                                <fieldset class="form-group">
                                                    <input type="text" class="form-control amount" name="ol_return_local_charges" data-rule-required="true" data-msg-required="This field is required" value="{{$returnCharges[2][0]->local}}">
                                                </fieldset>
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="card-title">National Charges</label>
                                                <fieldset class="form-group">
                                                    <input type="text" class="form-control amount" name="ol_return_national_charges" data-rule-required="true" data-msg-required="This field is required" value="{{$returnCharges[2][0]->national}}">
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
                                        <h3 class="card-title lead success">Detain</h3>
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
                                                    <label class="card-title">Flat Charges/KG (Local)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (National-Zone A)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (National-Zone B)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (National-Zone C)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (National-Zone D)</label>
                                                </div>
                                                <div class="col-1"></div>
                                            </div>
                                            @php
                                                $index_row = 0;
                                            @endphp
                                            @foreach($weight[3] as $index => $detweight)
                                                @if($detweight->delivery_type_id == 1)
                                                <div class="row detain_door_weight_row">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->range_up}}" name="detain_door_range_up[{{$index_row}}]" {{ (($index_row == 0 || $index_row == 1) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range"data-rule-required="true" data-msg-required="This field is required"  value="{{$detweight->range_down}}" name="detain_door_range_down[{{$index_row}}]" {{ (($index_row == 0) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->local_or_6hr}}" name="detain_door_local_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->national_charges_class_0}}" name="detain_door_class_0_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->national_charges_class_1}}" name="detain_door_class_1_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->national_charges_class_2}}" name="detain_door_class_2_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->national_charges_class_3}}" name="detain_door_class_3_charges[{{$index_row}}]">
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
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Hub-Hub Delivery</h3>
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
                                                    <label class="card-title">Flat Charges/KG (Local)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (National-Zone A)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (National-Zone B)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (National-Zone C)</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges/KG (National-Zone D)</label>
                                                </div>
                                                <div class="col-1"></div>
                                            </div>
                                            @php
                                                $index_row = 0;
                                            @endphp
                                            @foreach($weight[3] as $index => $detweight)
                                                @if($detweight->delivery_type_id == 2)
                                                <div class="row detain_hub_weight_row">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->range_up}}" name="detain_hub_range_up[{{$index_row}}]" {{ (($index_row == 0 || $index_row == 1) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decima weight_range" data-rule-required="true" data-msg-required="This field is required"  value="{{$detweight->range_down}}" name="detain_hub_range_down[{{$index_row}}]" {{ (($index_row == 0) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->local_or_6hr}}" name="detain_hub_local_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->national_charges_class_0}}" name="detain_hub_class_0_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->national_charges_class_1}}" name="detain_hub_class_1_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->national_charges_class_2}}" name="detain_hub_class_2_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->national_charges_class_3}}" name="detain_hub_class_3_charges[{{$index_row}}]">
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

                                        <div class="row return-charges-div-detain">

                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Local Charges</label>
                                                <fieldset class="form-group">
                                                    <input type="text" class="form-control amount" name="detain_return_local_charges" data-rule-required="true" data-msg-required="This field is required" value="{{$returnCharges[3][0]->local}}">
                                                </fieldset>
                                            </div>

                                            <div class="col-md-2">
                                                <label class="card-title">National Charges</label>
                                                <fieldset class="form-group">
                                                    <input type="text" class="form-control amount" name="detain_return_national_charges" data-rule-required="true" data-msg-required="This field is required" value="{{$returnCharges[3][0]->national}}">
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
                                        <h3 class="card-title lead success">Sameday</h3>
                                    </div>
                                    <div class="col-md-6">
                                        <a id="sameday_main_switch" href="javascript:void(0);" class="pull-right"><input name="sameday_main_switch" type="checkbox" id="" class="switchery sameday-main-switch" data-size="sm"/></a>
                                    </div>
                                </div>

                            </div>
                            <div id="sameday" class="border-success no-border-top card hide">
                                <div class="card-content">
                                    <div class="card-body">
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
                                                    <label class="card-title">6hr Charges</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Sameday Charges</label>
                                                </div>
                                                <div class="col-1"></div>

                                            </div>
                                            @php
                                                $index_row = 0;
                                            @endphp
                                            @foreach($weight[4] as $index => $sameweight)
                                                @if($sameweight->delivery_type_id == 1)
                                                <div class="row sameday_door_weight_row">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->range_up}}" name="sameday_door_range_up[{{$index_row}}]" {{ (($index_row == 0 || $index_row == 1) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->range_down}}" name="sameday_door_range_down[{{$index_row}}]" {{ (($index_row == 0) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->local_or_6hr}}" name="sameday_door_local_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->national_charges_class_0}}" name="sameday_door_class_0_charges[{{$index_row}}]">
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
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Hub-Hub Delivery</h3>
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
                                                    <label class="card-title">6hr Charges</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Sameday Charges</label>
                                                </div>
                                                <div class="col-1"></div>

                                            </div>
                                            @php
                                                $index_row = 0;
                                            @endphp
                                            @foreach($weight[4] as $index => $sameweight)
                                                @if($sameweight->delivery_type_id == 2)
                                                <div class="row sameday_hub_weight_row">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->range_up}}" name="sameday_hub_range_up[{{$index_row}}]" {{ (($index_row == 0 || $index_row == 1) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->range_down}}" name="sameday_hub_range_down[{{$index_row}}]" {{ (($index_row == 0) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->local_or_6hr}}" name="sameday_hub_local_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->national_charges_class_0}}" name="sameday_hub_class_0_charges[{{$index_row}}]">
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
                                                    <input type="text"  class="form-control amount" name="sameday_return_national_charges"  data-rule-required="true" data-msg-required="This field is required" value="{{$returnCharges[4][0]->national}}">
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
    <style type="text/css">
        .hide{
            display:none;
        }
    </style>


@endsection

@section('js')
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('input[name="on_door_mcw_charges"]').on('change', function () {
                var value = $(this).val();
                var maxvalue = parseFloat(value);
                var minvalue = maxvalue + 0.01;
                $('input[name="on_door_range_down[0]"]').val(maxvalue);
                $('input[name="on_door_range_up[1]"]').val(minvalue);
            });

            $('input[name="on_hub_mcw_charges"]').on('change', function () {
                var value = $(this).val();
                var maxvalue = parseFloat(value);
                var minvalue = maxvalue + 0.01;
                $('input[name="on_hub_range_down[0]"]').val(maxvalue);
                $('input[name="on_hub_range_up[1]"]').val(minvalue);
            });
            $('input[name="ol_door_mcw_charges"]').on('change', function () {
                var value = $(this).val();
                var maxvalue = parseFloat(value);
                var minvalue = maxvalue + 0.01;
                $('input[name="ol_door_range_down[0]"]').val(maxvalue);
                $('input[name="ol_door_range_up[1]"]').val(minvalue);
            });
            $('input[name="ol_hub_mcw_charges"]').on('change', function () {
                var value = $(this).val();
                var maxvalue = parseFloat(value);
                var minvalue = maxvalue + 0.01;
                $('input[name="ol_hub_range_down[0]"]').val(maxvalue);
                $('input[name="ol_hub_range_up[1]"]').val(minvalue);
            });
            $('input[name="detain_door_mcw_charges"]').on('change', function () {
                var value = $(this).val();
                var maxvalue = parseFloat(value);
                var minvalue = maxvalue + 0.01;
                $('input[name="detain_door_range_down[0]"]').val(maxvalue);
                $('input[name="detain_door_range_up[1]"]').val(minvalue);
            });
            $('input[name="detain_hub_mcw_charges"]').on('change', function () {
                var value = $(this).val();
                var maxvalue = parseFloat(value);
                var minvalue = maxvalue + 0.01;
                $('input[name="detain_hub_range_down[0]"]').val(maxvalue);
                $('input[name="detain_hub_range_up[1]"]').val(minvalue);
            });
            $('input[name="sameday_door_mcw_charges"]').on('change', function () {
                var value = $(this).val();
                var maxvalue = parseFloat(value);
                var minvalue = maxvalue + 0.01;
                $('input[name="sameday_door_range_down[0]"]').val(maxvalue);
                $('input[name="sameday_door_range_up[1]"]').val(minvalue);
            });
            $('input[name="sameday_hub_mcw_charges"]').on('change', function () {
                var value = $(this).val();
                var maxvalue = parseFloat(value);
                var minvalue = maxvalue + 0.01;
                $('input[name="sameday_hub_range_down[0]"]').val(maxvalue);
                $('input[name="sameday_hub_range_up[1]"]').val(minvalue);
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
        });


        $('.decimal').inputmask({
            'alias': 'decimal',
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            'digits': 3,
            'min': 0.00,
            'max': 10000
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
                'max': 10000
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
        var on_door_count = $('.on_door_weight_row').length;
        $('body').on('click','#waddition_btn',function () {

            let htmdiv = '<div class="row" id="on_door_weight_row'+on_door_count+'"><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="on_door_range_up['+on_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="on_door_range_down['+on_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required" value="" name="on_door_local_charges['+on_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required"  name="on_door_class_0_charges['+on_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="on_door_class_1_charges['+on_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="on_door_class_2_charges['+on_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="on_door_class_3_charges['+on_door_count+']"></fieldset></div><div class="col-1">\n' +
                '<span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 on_weight_close"><i class="ft-x"></i></span></div></div>';
            $('.weight-addition-overnight').append(htmdiv);

            masks();

            $("#on_door_weight_row"+on_door_count+" .validated").each(function(){
                $( this ).rules( "add", {
                    required: true,
                });

            });
            on_door_count++;
        });
        var on_hub_count = $('.on_hub_weight_row').length;

        $('body').on('click','#hub_waddition_btn',function () {

            let htmdiv = '<div class="row" id="on_hub_weight_row'+on_hub_count+'"><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="on_hub_range_up['+on_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="on_hub_range_down['+on_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required" value="" name="on_hub_local_charges['+on_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required"  name="on_hub_class_0_charges['+on_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="on_hub_class_1_charges['+on_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="on_hub_class_2_charges['+on_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="on_hub_class_3_charges['+on_hub_count+']"></fieldset></div><div class="col-1">\n' +
                '<span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 on_weight_close"><i class="ft-x"></i></span></div></div>';
            $('.hub-weight-addition-overnight').append(htmdiv);

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
            console.log(cashhandlingswitch);
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

        //Overland


        var overland_door_count = $('.ol_door_weight_row').length;
        $('body').on('click','#overland_door_weightadd',function () {

            let htmdiv1 = '<div class="row" id="ol_door_weight_row'+overland_door_count+'"><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_door_range_up['+overland_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_door_range_down['+overland_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_door_local_charges['+overland_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required"  name="ol_door_class_0_charges['+overland_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="ol_door_class_1_charges['+overland_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="ol_door_class_2_charges['+overland_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="ol_door_class_3_charges['+overland_door_count+']"></fieldset></div><div class="col-1">\n' +
                '<span class="btn btn-danger rounded btn-sm-width mr-1 mb-1 ol_weight_close"><i class="ft-x"></i></span></div></div>';
            $('.weight-addition-overland').append(htmdiv1);

            masks();

            $("#ol_weight_row"+overland_door_count+" .validated").each(function(){
                $( this ).rules( "add", {
                    required: true,
                });

            });
            overland_door_count++;
        });
        var overland_hub_count = $('.ol_hub_weight_row').length;
        $('body').on('click','#overland_hub_weightadd',function () {

            let htmdiv1 = '<div class="row" id="ol_hub_weight_row'+overland_hub_count+'"><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_hub_range_up['+overland_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_hub_range_down['+overland_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_hub_local_charges['+overland_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required"  name="ol_hub_class_0_charges['+overland_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="ol_hub_class_1_charges['+overland_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="ol_hub_class_2_charges['+overland_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="ol_hub_class_3_charges['+overland_hub_count+']"></fieldset></div><div class="col-1">\n' +
                '<span class="btn btn-danger rounded btn-sm-width mr-1 mb-1 ol_weight_close"><i class="ft-x"></i></span></div></div>';
            $('.hub-weight-addition-overland').append(htmdiv1);

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
        //overland end
        //detain
        //var weightAdditionDetain = document.querySelector('.switchery.weightAdditionDetain0');
        var cashhandlingswitchDetain = document.querySelector('.switchery.cashChargesDetain');
        var insuranceChargesSwitchDetain = document.querySelector('.switchery.insuranceChargesdetain');
        var returnChargesSwitchDetain = document.querySelector('.switchery.returnChargesDetain');
        var fuelChargesSwitchDetain = document.querySelector('.switchery.fuelSurchargeDetain');

        //detain


        var detain_door_count = $('.detain_door_weight_row').length;
        $('body').on('click','#detain_door_weightadd',function () {

            let htmdiv1 = '<div class="row" id="detain_door_weight_row'+detain_door_count+'"><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_door_range_up['+detain_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_door_range_down['+detain_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_door_local_charges['+detain_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required"  name="detain_door_class_0_charges['+detain_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="detain_door_class_1_charges['+detain_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="detain_door_class_2_charges['+detain_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="detain_door_class_3_charges['+detain_door_count+']"></fieldset></div><div class="col-1">\n' +
                '<span id="detain_weight_close" class="btn btn-danger rounded btn-sm-width mr-1 mb-1 detain_weight_close"><i class="ft-x"></i></span></div></div>';
            $('.weight-addition-detain').append(htmdiv1);

            masks();

            $("#detain_door_weight_row"+detain_door_count+" .validated").each(function(){
                $( this ).rules( "add", {
                    required: true,
                });

            });
            detain_door_count++;
        });
        var detain_hub_count = $('.detain_hub_weight_row').length;
        $('body').on('click','#detain_hub_weightadd',function () {

            let htmdiv1 = '<div class="row" id="detain_hub_weight_row'+detain_hub_count+'"><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_hub_range_up['+detain_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_hub_range_down['+detain_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_hub_local_charges['+detain_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required"  name="detain_hub_class_0_charges['+detain_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="detain_hub_class_1_charges['+detain_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="detain_hub_class_2_charges['+detain_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="detain_hub_class_3_charges['+detain_hub_count+']"></fieldset></div><div class="col-1">\n' +
                '<span id="detain_weight_close" class="btn btn-danger rounded btn-sm-width mr-1 mb-1 detain_weight_close"><i class="ft-x"></i></span></div></div>';
            $('.hub-weight-addition-detain').append(htmdiv1);

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


        var sameday_door_count = $('.sameday_door_weight_row').length;
        $('body').on('click','#sameday_door_weightadd',function () {

            let htmdiv1 = '<div class="row" id="sameday_door_weight_row'+sameday_door_count+'"><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_door_range_up['+sameday_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_door_range_down['+sameday_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_door_local_charges['+sameday_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required"  name="sameday_door_class_0_charges['+sameday_door_count+']"></fieldset></div><div class="col-1">\n' +
                '<span id="sameday_weight_close" class="btn btn-danger rounded btn-sm-width mr-1 mb-1 sameday_weight_close"><i class="ft-x"></i></span></div></div>';
            $('.weight-addition-sameday').append(htmdiv1);

            masks();

            $("#sameday_door_weight_row"+sameday_door_count+" .validated").each(function(){
                $( this ).rules( "add", {
                    required: true,
                });

            });
            sameday_door_count++;
        });
        var sameday_hub_count = $('.sameday_hub_weight_row').length;
        $('body').on('click','#sameday_hub_weightadd',function () {

            let htmdiv1 = '<div class="row" id="sameday_hub_weight_row'+sameday_hub_count+'"><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_hub_range_up['+sameday_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_hub_range_down['+sameday_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_hub_local_charges['+sameday_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required"  name="sameday_hub_class_0_charges['+sameday_hub_count+']"></fieldset></div><div class="col-1">\n' +
                '<span id="sameday_weight_close" class="btn btn-danger rounded btn-sm-width mr-1 mb-1 sameday_weight_close"><i class="ft-x"></i></span></div></div>';
            $('.hub-weight-addition-sameday').append(htmdiv1);

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

        $('#ratesAdditionForm').on('keypress',function (e) {
            if(e.which == 13) {
                e.preventDefault();
            }
        });

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