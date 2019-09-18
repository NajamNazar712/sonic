@extends('admin.layout.master')

@section('title', 'Edit Rates')

@section('content')
    @if(!empty($shipper))

        @if(count($switches) > 0)
    <h1>Edit Rates</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <h2 class="font-large-1">{{$shipper->name}}
                            <div class="badge badge-success pull-right">Reimbursement Account</div>
                        </h2>
                        @include('admin.inc.messages')
                    </div>
                    <input type="hidden" id="shipper_id" value="{{$shipper->id}}">

                    <div class="card-content">
                        <form id="ratesAdditionForm" class="card-body card-dashboard" action="{{route('admin.edit.rates.submit',['id'=>$shipper->id])}}" method="post" novalidate="novalidate">
                            @csrf

                            <div class="card">

                                <div class="">
                                    @if(count($packaging_material_types) > 0)

                                        @php
                                            $packaging_switch = '';
                                        if(count($packaging_charges) > 0){
                                            $packaging_switch = 'checked';
                                             }else{
                                            $packaging_switch = '';
                                            }
                                        @endphp
                                        <div class="card-header border-primary">
                                            <div class="row">
                                                <div class="col-6"><h3 class="card-title lead primary">Packaging Material Charges</h3></div>
                                                <div class="col-6"><a href="javascript:void(0);" class="pull-right" id="packaging_main_switch"><input type="checkbox" name="packaging_switch" class="switchery pull-right packagingChargesSwitch" data-color="info" data-size="sm" {{$packaging_switch}}/></a></div>

                                            </div>
                                        </div>

                                        <div id="packaging_material_charges_div" class="card border-primary p-1 {{$packaging_switch == 'checked'? '':'hide'}}">
                                            @foreach($packaging_material_types as $index => $type)
                                                @php
                                                $packaging_type_switch = '';
                                                if(in_array($type->id, $packaging_type_ids)){
                                                    $packaging_type_switch = 'checked';
                                                }
                                                @endphp
                                                <div class="card-header border-primary">
                                                    <div class="row">
                                                        <div class="col-6"><h4 class="card-title lead primary">{{$type->type}}</h4></div>
                                                        <div class="col-6"><a href="javascript:void(0);" class="pull-right"><a href="javascript:void(0);" class="pull-right" id="packaging_type_{{$type->id}}"><input name="packaging_type_{{$type->id}}" type="checkbox"  class="switchery packaging_type_{{$type->id}}" data-color="info" data-size="sm" {{$packaging_type_switch}}/></a></a></div>

                                                    </div>
                                                </div>

                                                <div id="package_type_{{$type->id}}" class="card border-primary {{$packaging_type_switch == 'checked'? '':'hide'}}" aria-expanded="true">
                                                    <div class="card-content">
                                                        <div class="card-body packaging-charges-div">
                                                            <div class="row">
                                                                @foreach($type->sizes as $ind => $size)

                                                                    @if(isset($packaging_charges[$type->id][$ind]) && $packaging_charges[$type->id][$ind]->size_id == $size->id)
                                                                        <div class="col-md-3 text-center">
                                                                            <label class="card-title">{{$size->size}}</label>
                                                                            <fieldset class="form-group">
                                                                                <input name="packaging_material_size[{{$size->id}}]" type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$packaging_charges[$type->id][$ind]->charges}}">
                                                                            </fieldset>
                                                                        </div>
                                                                    @else
                                                                        <div class="col-md-3 text-center">
                                                                            <label class="card-title">{{$size->size}}</label>
                                                                            <fieldset class="form-group">
                                                                                <input name="packaging_material_size[{{$size->id}}]" type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$size->standard_charges}}">
                                                                            </fieldset>
                                                                        </div>
                                                                    @endif

                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            @endforeach
                                        </div>


                                    @endif

                                </div>

                            </div>



                            <input type="hidden" name="_method" value="PUT"/>

                            <div id="headingCollapse61" class="card-header border-success">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3 class="display-inline card-title lead success">Overnight</h3>
                                        @if($sale_person['admin_id'] == \Illuminate\Support\Facades\Auth::id() || session('role_id') == 1)
                                            <label class="display-inline ml-1">Make Default</label>
                                            @if($shipper['default_shipping_mode'] == 1)
                                                <input type="checkbox" name="on_default" id="on_default" class="switchery on_default" checked data-size="xs" data-switchery="true">
                                            @else
                                                <input type="checkbox" name="on_default" id="on_default" class="switchery on_default" data-size="xs" data-switchery="true">
                                            @endif
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <a href="javascript:void(0);" class="pull-right" id="on_main_switch"><input name="on_main_switch" type="checkbox" id="" class="switchery on-main-switch" data-size="sm" {{ ((isset($switches[1][0]) && $switches[1][0]->status == 1) ? 'checked' : '') }}/></a>
                                    </div>
                                </div>
                            </div>
                            <div id="overnight" class="card  border-success {{ ((isset($switches[1][0]) && $switches[1][0]->status == 1) ? '' : 'hide') }}"
                                 aria-expanded="true">
                                <input type="hidden" name="on_rate_record" value="{{ ((isset($switches[1][0]) && $switches[1][0]->id != '') ? $switches[1][0]->id : '') }}">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="weight-addition-overnight">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Weight Charges</h3>
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
                                                    <label class="card-title">Weight Addition</label>
                                                </div>
                                                <div class="col-2 text-center">
                                                    <label class="card-title">KG Range</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Local Charges</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">National Charges Class A</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">National Charges Class B</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">National Charges Class C</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">National Charges Class D</label>
                                                </div>
                                                <div class="col-1"></div>
                                            </div>
                                            @if(isset($weight[1]))
                                            @foreach($weight[1] as $index => $onweight)
                                                <div class="row on_weight_row" id="on_weight_row{{$index}}">
                                                    <input type="hidden" name="on_weight_record[{{$index}}]" value="{{$onweight->id}}">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->range_up}}" name="on_wa_range_up[{{$index}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->range_down}}" name="on_wa_range_down[{{$index}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="OvernightSwitch{{$index}}" class="switchery weightAdditionOvernight" data-color="success" data-size="sm" name="on_wa_switch[{{$index}}]" {{ ($onweight->weight_addition == 1) ? 'checked' : '' }}/>
                                                        </div>
                                                    </div>
                                                    <div class="col-2 text-center">

                                                        <fieldset style="padding-top: 5px;">
                                                            <div class="input-group input-group-sm form-group">
                                                                <input type="text" class="touchspin-color input-sm spkg" value="{{$onweight->spkg}}" {{ ($onweight->weight_addition == 1) ? '' : 'disabled' }} data-bts-button-down-class="btn btn-success"
                                                                       data-bts-button-up-class="btn btn-success" name="on_wa_spkg[{{$index}}]" data-rule-required="true" data-msg-required="This field is required">
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->local_or_6hr}}" name="on_wa_local_charges[{{$index}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->national_charges_class_0}}" name="on_class_0_charges[{{$index}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->national_charges_class_1}}" name="on_class_1_charges[{{$index}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->national_charges_class_2}}" name="on_class_2_charges[{{$index}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->national_charges_class_3}}" name="on_class_3_charges[{{$index}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-1">
                                                        @if($index>0)
                                                            <span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 on_weight_close"><i class="ft-x"></i></span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                                @else
                                                <div class="row on_weight_row" id="on_weight_row0">
                                                    <input type="hidden" name="on_weight_record[0]" value="">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="" name="on_wa_range_up[0]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="" name="on_wa_range_down[0]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="OvernightSwitch0" class="switchery weightAdditionOvernight" data-color="success" data-size="sm" name="on_wa_switch[0]"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-2 text-center">

                                                        <fieldset style="padding-top: 5px;">
                                                            <div class="input-group input-group-sm form-group">
                                                                <input type="text" class="touchspin-color input-sm spkg" value="0" disabled data-bts-button-down-class="btn btn-success"
                                                                       data-bts-button-up-class="btn btn-success" name="on_wa_spkg[0]" data-rule-required="true" data-msg-required="This field is required">
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="" name="on_wa_local_charges[0]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="" name="on_class_0_charges[0]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="on_class_1_charges[0]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="on_class_2_charges[0]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="on_class_3_charges[0]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-1">

                                                    </div>
                                                </div>
                                                @endif
                                        </div>{{--weight addition div--}}
                                        <div>
                                            <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="waddition_btn"><i class="la la-plus"></i></button>
                                        </div>
                                        <div class="row mt-2">
                                        <input type="hidden" name="on_booking_record" value="{{ (isset($shippingType[1][0]) && $shippingType[1][0]->id != '')? $shippingType[1][0]->id : ''}}">

                                            <div class="col-md-3 text-center">
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" >Replacement</span>
                                                        </div>
                                                        <input type="text" class="form-control percent" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($shippingType[1][0]) && $shippingType[1][0]->replacement_charges != '')? $shippingType[1][0]->replacement_charges : ''}}" name="on_replacement_charges">
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
                                                        <input type="text" class="form-control percent" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($shippingType[1][0]) && $shippingType[1][0]->try_and_buy_charges != '')? $shippingType[1][0]->try_and_buy_charges : ''}}" name="on_tnb_charges">
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
                                            @php
                                                $on_cash_sw = '';
                                                $on_cash_switch = '';
                                             if((isset($switches[1][0]) && $switches[1][0]->cash_handling_charges == 1)){
                                            $on_cash_sw = '';
                                            $on_cash_switch = 'checked';
                                             }else{
                                            $on_cash_sw = 'disabled';
                                            $on_cash_switch = '';
                                            }
                                            @endphp

                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="on_cash_handling_switch"  class="switchery cashChargesOvernight" data-color="success" data-size="sm" {{$on_cash_switch}}/>
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
                                            @if(isset($cashHandling[1]))
                                            @foreach($cashHandling[1] as $index => $cash)
                                                <div class="row on_cash_handling_row">
                                                    <input type="hidden" name="on_cash_record[{{$index}}]" value="{{$cash->id}}">
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="on_cash_range_up[{{$index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric"  value="{{$cash->range_up}}" {{$on_cash_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="on_cash_range_down[{{$index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric"  value="{{$cash->range_down}}" {{$on_cash_sw}}>
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="on_cash_charges[{{$index}}]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->charges}}" {{$on_cash_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    @if($index>0)
                                                        <div class="col">
                                                            <span class="btn btn-danger rounded btn-sm-width mr-1 mb-1 on_weight_close"><i class="ft-x"></i></span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                            @else
                                                 <div class="row on_cash_handling_row">
                                                     <input type="hidden" name="on_cash_record[0]" value="">
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="on_cash_range_up[0]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric"  value="" {{$on_cash_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="on_cash_range_down[0]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric"  value="" {{$on_cash_sw}}>
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="on_cash_charges[0]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" {{$on_cash_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col"></div>

                                                </div>
                                            @endif
                                        </div>
                                        <div class="cash-handling-btn-overnight">
                                            <button id="addMoreSlabs" type="button" class="btn btn-outline-success mr-1" title="Add more slabs" {{$on_cash_sw}}><i class="la la-plus"></i></button>
                                        </div>
                                        <hr>

                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Insurance Charges</h3>
                                            </div>
                                            @php
                                                $on_ins_sw = '';
                                                $on_insurance_switch = '';
                                            if((isset($switches[1][0]) && $switches[1][0]->insurance_charges == 1)){
                                            $on_ins_sw = '';
                                            $on_insurance_switch = 'checked';
                                             }else{
                                            $on_ins_sw = 'disabled';
                                            $on_insurance_switch = '';
                                            }
                                            @endphp
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="on_insurance_charges_switch" class="switchery insuranceChargesOvernight" data-color="success" data-size="sm" {{$on_insurance_switch}}/>
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
                                            @if(isset($insuranceCharges[1]))
                                            @foreach($insuranceCharges[1] as $index => $insurance)
                                                <div class="row on_insurance_row" id="on_insurance_handle_0">
                                                    <input type="hidden" name="on_insurance_record[{{$index}}]" value="{{$insurance->id}}">
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="on_ins_range_up[{{$index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric"  value="{{$insurance->range_up}}" {{$on_ins_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="on_ins_range_down[{{$index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric"  value="{{$insurance->range_down}}" {{$on_ins_sw}}>
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="on_ins_charges[{{$index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control dec-percent"  value="{{$insurance->charges}}" {{$on_ins_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    @if($index>0)
                                                        <div class="col">
                                                            <span class="btn btn-danger rounded btn-sm-width mr-1 mb-1 on_weight_close"><i class="ft-x"></i></span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                            @else
                                                <div class="row on_insurance_row" id="on_insurance_handle_0">
                                                    <input type="hidden" name="on_insurance_record[0]" value="">
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="on_ins_range_up[0]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric"  value="" {{$on_ins_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="on_ins_range_down[0]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric"  value="" {{$on_ins_sw}}>
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="on_ins_charges[0]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control dec-percent"  value="" {{$on_ins_sw}}>
                                                        </fieldset>
                                                    </div>
                                                        <div class="col"></div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="insurance-charges-btn-overnight">
                                            <button id="addMoreSlabsInsurance" type="button" class="btn btn-outline-success mr-1" title="Add more slabs" {{$on_ins_sw}}><i class="la la-plus"></i></button>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Return Charges</h3>
                                            </div>
                                            @php
                                                $on_return_sw = '';
                                                $on_return_switch = '';
                                            if((isset($switches[1][0]) && $switches[1][0]->return_charges == 1)){
                                            $on_return_sw = '';
                                            $on_return_switch = 'checked';
                                             }else{
                                            $on_return_sw = 'disabled';
                                            $on_return_switch = '';
                                            }
                                            @endphp
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="on_return_switch" class="switchery returnChargesOvernight" data-color="success" data-size="sm" {{$on_return_switch}}/>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row return-charges-div-overnight">
                                            <input type="hidden" name="on_return_record" value="{{ (isset($returnCharges[1][0]) && $returnCharges[1][0]->id != '')? $returnCharges[1][0]->id : ''}}">
                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Local Charges</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control amount" name="on_return_local_charges"  value="{{ (isset($returnCharges[1][0]) && $returnCharges[1][0]->local !== '')? $returnCharges[1][0]->local : ''}}" {{$on_return_sw}}>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <label class="card-title">National Charges Class A</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control amount" name="on_return_class_0_charges"  value="{{ (isset($returnCharges[1][0]) && $returnCharges[1][0]->national_charges_class_0 !== '')? $returnCharges[1][0]->national_charges_class_0 : ''}}" {{$on_return_sw}}>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <label class="card-title">National Charges Class B</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent" name="on_return_class_1_charges"  value="{{ (isset($returnCharges[1][0]) && $returnCharges[1][0]->national_charges_class_1 !== '')? $returnCharges[1][0]->national_charges_class_1 : ''}}" {{$on_return_sw}}>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <label class="card-title">National Charges Class C</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent" name="on_return_class_2_charges"  value="{{ (isset($returnCharges[1][0]) && $returnCharges[1][0]->national_charges_class_2 !== '')? $returnCharges[1][0]->national_charges_class_2 : ''}}" {{$on_return_sw}}>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <label class="card-title">National Charges Class D</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent" name="on_return_class_3_charges"  value="{{ (isset($returnCharges[1][0]) && $returnCharges[1][0]->national_charges_class_3 !== '')? $returnCharges[1][0]->national_charges_class_3 : ''}}" {{$on_return_sw}}>
                                                </fieldset>
                                            </div>
                                        </div>

                                        <hr>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Fuel Surcharge</h3>
                                            </div>
                                            @php
                                                $on_fuel_sw = '';
                                                $on_fuel_switch = '';
                                            if((isset($switches[1][0]) && $switches[1][0]->fuel_charges == 1)){
                                            $on_fuel_sw = '';
                                            $on_fuel_switch = 'checked';
                                             }else{
                                            $on_fuel_sw = 'disabled';
                                            $on_fuel_switch = '';
                                            }
                                            @endphp
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="overnight_fuel_switch" class="switchery fuelSurchargeOvernight" data-color="success" data-size="sm" {{$on_fuel_switch}} />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row fuel-surcharge-div-overnight">
                                            <input type="hidden" name="on_fuel_record" value="{{ (isset($fuelCharges[1][0]) && $fuelCharges[1][0]->id != '')? $fuelCharges[1][0]->id : ''}}">
                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Charges</label>
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <input type="text"  class="form-control " name="overnight_fuel_surcharge" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($fuelCharges[1][0]) && $fuelCharges[1][0]->fuel_surcharge != '')? $fuelCharges[1][0]->fuel_surcharge : ''}}" {{$on_fuel_sw}}>
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
                                        @php
                                            $on_discount_id = '';
                                            if((isset($discountCharges[1][0])) && $discountCharges[1][0]->id != ''){
                                            $on_discount_id = $discountCharges[1][0]->id;
                                            }
                                                    $on_discount_title_switch = '';
                                                        $on_discount_title = '';
                                                    if((isset($discountCharges[1][0]) && $discountCharges[1][0]->title != '')){
                                                    $on_discount_title = $discountCharges[1][0]->title;
                                                     }else{
                                                    $on_discount_title = '';
                                                    }
                                                    if((isset($discountCharges[1][0]->cash)) || (isset($discountCharges[1][0]->weight)) || (isset($discountCharges[1][0]->insurance)) || (isset($discountCharges[1][0]->return)) || (isset($discountCharges[1][0]->packaging))){
                                                        $on_discount_title_switch = '';
                                                        }else{
                                                        $on_discount_title_switch = 'disabled';
                                                        }
                                        @endphp
                                        <input type="hidden" name="on_discount_record" value="{{$on_discount_id}}">
                                        <div class="row mt-1">
                                            <div class="col-md-6">
                                                <label class="">Title</label>
                                                <div class='form-group'>
                                                    <input type='text' class="form-control" data-rule-required="true" data-msg-required="This field is required" {{$on_discount_title_switch}} name="on_discount_title" value="{{$on_discount_title}}"/>
                                                </div>

                                            </div>
                                            @php
                                                $on_discount_daterange = '';
                                                $on_discount_daterange_switch = '';
                                            if((isset($discountCharges[1][0]) && $discountCharges[1][0]->to != '')){

                                            $to = date('m/d/Y', strtotime($discountCharges[1][0]->to));
                                            $from = date('m/d/Y', strtotime($discountCharges[1][0]->from));

                                            $on_discount_daterange = $to.' - '.$from;

                                             }else{
                                            $on_discount_daterange = '';

                                            }
                                            if((isset($discountCharges[1][0]->cash)) || (isset($discountCharges[1][0]->weight)) || (isset($discountCharges[1][0]->insurance)) || (isset($discountCharges[1][0]->return)) || (isset($discountCharges[1][0]->packaging))){
                                            $on_discount_daterange_switch = '';
                                            }else{
                                            $on_discount_daterange_switch = 'disabled';
                                            }
                                            @endphp
                                            <div class="col-md-6">
                                                <label class="">Apply [to - from]{{$on_discount_daterange}}</label>
                                                <div class='input-group form-group'>
                                                    <input type='text' class="form-control daterange" data-rule-required="true" data-msg-required="This field is required" {{$on_discount_daterange_switch}} name="on_daterange" value="{{$on_discount_daterange}}"/>
                                                    <div class="input-group-append">
                                                            <span class="input-group-text">
                                                              <span class="la la-calendar"></span>
                                                            </span>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        @php
                                            $on_discount_weight_sw = '';
                                            $on_discount_weight_switch = '';
                                            $on_discount_weight_disable = '';
                                        if((isset($discountCharges[1][0]) && $discountCharges[1][0]->weight != '')){
                                        $on_discount_weight_sw = $discountCharges[1][0]->weight;
                                        $on_discount_weight_switch = 'checked';
                                        $on_discount_weight_disable = '';
                                         }else{
                                        $on_discount_weight_sw = '';
                                        $on_discount_weight_switch = '';
                                        $on_discount_weight_disable = 'disabled';
                                        }
                                        @endphp
                                        <div class="row">
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Weight</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox" id="" class="switchery discountSwitchesOvernight" name="on_discount_weight_switch" data-size="xs" {{$on_discount_weight_switch}}/>
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent on-discount-inp" name="on_discount_weight_rate" value="{{$on_discount_weight_sw}}" {{$on_discount_weight_disable}}>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            @php
                                                $on_discount_cash_sw = '';
                                                $on_discount_cash_switch = '';
                                                $on_discount_cash_disable = '';
                                            if((isset($discountCharges[1][0]) && $discountCharges[1][0]->cash != '')){
                                            $on_discount_cash_sw = $discountCharges[1][0]->cash;
                                            $on_discount_cash_switch = 'checked';
                                            $on_discount_cash_disable = '';
                                             }else{
                                            $on_discount_cash_sw = '';
                                            $on_discount_cash_switch = '';
                                            $on_discount_cash_disable = 'disabled';
                                            }
                                            @endphp
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Cash</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="">
                                                                <input type="checkbox" name="on_discount_cash_switch" class="switchery discountSwitchesOvernight" data-size="xs" {{$on_discount_cash_switch}}/>
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent on-discount-inp" name="on_discount_cash_rate" value="{{$on_discount_cash_sw}}" {{$on_discount_cash_disable}}>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            @php
                                                $on_discount_insurance_sw = '';
                                                $on_discount_insurance_switch = '';
                                                $on_discount_insurance_disable = '';
                                            if((isset($discountCharges[1][0]) && $discountCharges[1][0]->insurance != '')){
                                            $on_discount_insurance_sw = $discountCharges[1][0]->insurance;
                                            $on_discount_insurance_switch = 'checked';
                                            $on_discount_insurance_disable = '';
                                             }else{
                                            $on_discount_insurance_sw = '';
                                            $on_discount_insurance_switch = '';
                                            $on_discount_insurance_disable = 'disabled';
                                            }
                                            @endphp
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Insurance</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="">
                                                                <input type="checkbox" name="on_discount_insurance_switch" class="switchery discountSwitchesOvernight" data-size="xs" {{$on_discount_insurance_switch}}/>
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent on-discount-inp" name="on_discount_insurance_rate" value="{{$on_discount_insurance_sw}}" {{$on_discount_insurance_disable}}>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            @php
                                                $on_discount_return_sw = '';
                                                $on_discount_return_switch = '';
                                                $on_discount_return_disable = '';
                                            if((isset($discountCharges[1][0]) && $discountCharges[1][0]->return != '')){
                                            $on_discount_return_sw = $discountCharges[1][0]->return;
                                            $on_discount_return_switch = 'checked';
                                            $on_discount_return_disable = '';
                                             }else{
                                            $on_discount_return_sw = '';
                                            $on_discount_return_switch = '';
                                            $on_discount_return_disable = 'disabled';
                                            }
                                            @endphp
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Return</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox"  class="switchery discountSwitchesOvernight" data-size="xs" name="on_discount_return_switch" {{$on_discount_return_switch}}/>
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent on-discount-inp" name="on_discount_return_rate" value="{{$on_discount_return_sw}}" {{$on_discount_return_disable}}>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            @php
                                                $on_discount_packaging_sw = '';
                                                $on_discount_packaging_switch = '';
                                                $on_discount_packaging_disable = '';
                                            if((isset($discountCharges[1][0]) && $discountCharges[1][0]->packaging != '')){
                                            $on_discount_packaging_sw = $discountCharges[1][0]->packaging;
                                            $on_discount_packaging_switch = 'checked';
                                            $on_discount_packaging_disable = '';

                                             }else{
                                            $on_discount_packaging_sw = '';
                                            $on_discount_packaging_switch = '';
                                            $on_discount_packaging_disable = 'disabled';
                                            }
                                            @endphp
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Packaging</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox"  class="switchery discountSwitchesOvernight" data-size="xs" name="on_discount_packaging_switch" {{$on_discount_packaging_switch}}/>
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent on-discount-inp" name="on_discount_packaging_rate" value="{{$on_discount_packaging_sw}}" {{$on_discount_packaging_disable}}>
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
                                        @if($sale_person['admin_id'] == \Illuminate\Support\Facades\Auth::id() || session('role_id') == 1)
                                            <label class="display-inline ml-1">Make Default</label>
                                            @if($shipper['default_shipping_mode'] == 2)
                                                <input type="checkbox" name="ol_default" id="ol_default" class="switchery ol_default" checked data-size="xs" data-switchery="true">
                                            @else
                                                <input type="checkbox" name="ol_default" id="ol_default" class="switchery ol_default" data-size="xs" data-switchery="true">
                                            @endif
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <a href="javascript:void(0);" class="pull-right" id="ol_main_switch"><input name="ol_main_switch" type="checkbox" class="switchery ol-main-switch" data-size="sm" {{ ((isset($switches[2][0]) && $switches[2][0]->status == 1) ? 'checked' : '') }}/></a>
                                    </div>
                                </div>

                            </div>
                            <div id="overland" class="border-success no-border-top card {{ ((isset($switches[2][0]) && $switches[2][0]->status == 1) ? '' : 'hide') }}"
                                 aria-expanded="false">
                                 <input type="hidden" name="ol_rate_record" value="{{ ((isset($switches[2][0]) && $switches[2][0]->id != '') ? $switches[2][0]->id : '') }}">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="weight-addition-overland">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Weight Charges</h3>
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
                                                    <label class="card-title">Weight Addition</label>
                                                </div>
                                                <div class="col-2 text-center">
                                                    <label class="card-title">KG Range</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Local Charges</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">National Charges Class A</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">National Charges Class B</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">National Charges Class C</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">National Charges Class D</label>
                                                </div>
                                                <div class="col-1"></div>
                                            </div>
                                            @if(isset($weight[2]))
                                            @foreach($weight[2] as $index => $olweight)
                                                <div class="row ol_weight_row">
                                                    <input type="hidden" name="ol_weight_record[{{$index}}]" value="{{$olweight->id}}">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->range_up}}" name="ol_wa_range_up[{{$index}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->range_down}}" name="ol_wa_range_down[{{$index}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="OverlandSwitch{{$index}}" class="switchery weightAdditionOverland" data-color="success" data-size="sm" name="ol_wa_switch[{{$index}}]" {{ ($olweight->weight_addition == 1) ? 'checked' : '' }}/>
                                                        </div>
                                                    </div>
                                                    <div class="col-2 text-center">

                                                        <fieldset style="padding-top: 5px;">
                                                            <div class="input-group input-group-sm form-group">
                                                                <input type="text" class="touchspin-color input-sm spkg" value="{{$olweight->spkg}}" {{ ($olweight->weight_addition == 1) ? '' : 'disabled' }} data-bts-button-down-class="btn btn-success"
                                                                       data-bts-button-up-class="btn btn-success" name="ol_wa_spkg[{{$index}}]" data-rule-required="true" data-msg-required="This field is required">
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->local_or_6hr}}" name="ol_wa_local_charges[{{$index}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->national_charges_class_0}}" name="ol_class_0_charges[{{$index}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->national_charges_class_1}}" name="ol_class_1_charges[{{$index}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->national_charges_class_2}}" name="ol_class_2_charges[{{$index}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->national_charges_class_3}}" name="ol_class_3_charges[{{$index}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-1">
                                                        @if($index>0)
                                                            <span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 ol_weight_close"><i class="ft-x"></i></span>
                                                        @endif
                                                    </div>
                                                </div>{{--Row--}}
                                            @endforeach
                                                @else
                                                <div class="row ol_weight_row">
                                                    <input type="hidden" name="ol_weight_record[0]" value="">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_wa_range_up[0]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_wa_range_down[0]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="OverlandSwitch0" class="switchery weightAdditionOverland" data-color="success" data-size="sm" name="ol_wa_switch[0]"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-2 text-center">

                                                        <fieldset style="padding-top: 5px;">
                                                            <div class="input-group input-group-sm form-group">
                                                                <input type="text" class="touchspin-color input-sm spkg" value="" disabled data-bts-button-down-class="btn btn-success"
                                                                       data-bts-button-up-class="btn btn-success" name="ol_wa_spkg[0]" data-rule-required="true" data-msg-required="This field is required">
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_wa_local_charges[0]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_class_0_charges[0]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_class_1_charges[0]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_class_2_charges[0]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_class_3_charges[0]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-1"></div>
                                                </div>{{--Row--}}
                                                @endif
                                        </div>{{--weight addition div--}}
                                        <div>
                                            <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="overland_weightadd"><i class="la la-plus"></i></button>
                                        </div>
                                        <div class="row mt-2">
                                        <input type="hidden" name="ol_booking_record" value="{{ (isset($shippingType[2][0]) && $shippingType[2][0]->id != '')? $shippingType[2][0]->id : ''}}">

                                            <div class="col-md-3 text-center">
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Replacement</span>
                                                        </div>
                                                        <input type="text" class="form-control percent" data-rule-required="true" data-msg-required="This field is required" name="ol_replacement_charges" value="{{ (isset($shippingType[2][0]) && $shippingType[2][0]->replacement_charges != '')? $shippingType[2][0]->replacement_charges : ''}}">
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
                                                        <input type="text" class="form-control percent" data-rule-required="true" data-msg-required="This field is required" name="ol_tnb_charges" value="{{ (isset($shippingType[2][0]) && $shippingType[2][0]->try_and_buy_charges != '')? $shippingType[2][0]->try_and_buy_charges : ''}}">
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
                                            @php
                                                $ol_cash_sw = '';
                                                $ol_cash_switch = '';
                                             if((isset($switches[2][0]) && $switches[2][0]->cash_handling_charges == 1)){
                                            $ol_cash_sw = '';
                                            $ol_cash_switch = 'checked';
                                             }else{
                                            $ol_cash_sw = 'disabled';
                                            $ol_cash_switch = '';
                                            }
                                            @endphp
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="ol_cash_handling_switch" class="switchery cashChargesOverland" data-color="success" data-size="sm" {{$ol_cash_switch}}/>
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
                                            @if(isset($cashHandling[2]))
                                            @foreach($cashHandling[2] as $index => $cash)
                                                <div class="row ol_cash_handling_row" id="ol_cash_handle_0">
                                                    <input type="hidden" name="ol_cash_record[{{$index}}]" value="{{$cash->id}}">
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="ol_cash_range_up[{{$index}}]" type="text" class="form-control  numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->range_up}}" {{$ol_cash_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="ol_cash_range_down[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->range_down}}" {{$ol_cash_sw}}>
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="ol_cash_charges[{{$index}}]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->charges}}" {{$ol_cash_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        @if($index>0)
                                                            <span class="ol_row_delete btn btn-danger rounded btn-sm-width mr-1 mb-1"><i class="ft-x"></i></span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                            @else
                                                <div class="row ol_cash_handling_row" id="ol_cash_handle_0">
                                                    <input type="hidden" name="ol_cash_record[0]" value="">
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="ol_cash_range_up[0]" type="text" class="form-control  numeric" data-rule-required="true" data-msg-required="This field is required" value="" {{$ol_cash_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="ol_cash_range_down[0]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="" {{$ol_cash_sw}}>
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="ol_cash_charges[0]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" {{$ol_cash_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col"></div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="cash-handling-btn-overland">
                                            <button id="overlandaddMoreSlabs" type="button" class="btn btn-outline-success mr-1" title="Add more slabs" {{$ol_cash_sw}}><i class="la la-plus"></i></button>
                                        </div>
                                        <hr>

                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Insurance Charges</h3>
                                            </div>
                                            @php
                                                $ol_ins_sw = '';
                                                $ol_insurance_switch = '';
                                            if((isset($switches[2][0]) && $switches[2][0]->insurance_charges == 1)){
                                            $ol_ins_sw = '';
                                            $ol_insurance_switch = 'checked';
                                             }else{
                                            $ol_ins_sw = 'disabled';
                                            $ol_insurance_switch = '';
                                            }
                                            @endphp
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="ol_insurance_charges_switch" class="switchery insuranceChargesoverland" data-color="success" data-size="sm" {{$ol_insurance_switch}}/>
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
                                            @if(isset($insuranceCharges[2]))
                                            @foreach($insuranceCharges[2] as $index => $ol_insurance)
                                                <div class="row ol_insurance_row">
                                                    <input type="hidden" name="ol_insurance_record[{{$index}}]" value="{{$ol_insurance->id}}">
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="ol_ins_range_up[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$ol_insurance->range_up}}" {{$ol_ins_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="ol_ins_range_down[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$ol_insurance->range_down}}" {{$ol_ins_sw}}>
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="ol_ins_charges[{{$index}}]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$ol_insurance->charges}}" {{$ol_ins_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        @if($index>0)
                                                            <span class="btn btn-danger rounded btn-sm-width mr-1 mb-1 ol_row_delete"><i class="ft-x"></i></span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                            @else
                                                <div class="row ol_insurance_row">
                                                    <input type="hidden" name="ol_insurance_record[0]" value="">
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="ol_ins_range_up[0]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="" {{$ol_ins_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="ol_ins_range_down[0]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="" {{$ol_ins_sw}}>
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="ol_ins_charges[0]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" {{$ol_ins_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col"></div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="insurance-charges-btn-overland">
                                            <button id="oladdMoreSlabsInsurance" type="button" class="btn btn-outline-success mr-1" title="Add more slabs" {{$ol_ins_sw}}><i class="la la-plus"></i></button>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Return Charges</h3>
                                            </div>
                                            @php
                                                $ol_return_sw = '';
                                                $ol_return_switch = '';
                                            if((isset($switches[2][0]) && $switches[2][0]->return_charges == 1)){
                                            $ol_return_sw = '';
                                            $ol_return_switch = 'checked';
                                             }else{
                                            $ol_return_sw = 'disabled';
                                            $ol_return_switch = '';
                                            }
                                            @endphp
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="ol_return_switch" class="switchery returnChargesOverland" data-color="success" data-size="sm" {{$ol_return_switch}}/>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row return-charges-div-overland">
                                            <input type="hidden" name="ol_return_record" value="{{ (isset($returnCharges[2][0]) && $returnCharges[2][0]->id != '')? $returnCharges[2][0]->id : ''}}">

                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Local Charges</label>
                                                <fieldset class="form-group">
                                                    <input type="text" class="form-control amount" name="ol_return_local_charges" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($returnCharges[2][0]) && $returnCharges[2][0]->local !== '')? $returnCharges[2][0]->local : ''}}" {{$ol_return_sw}}>
                                                </fieldset>
                                            </div>

                                            <div class="col text-center">
                                                <label class="card-title">National Charges Class A</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control amount" name="ol_return_class_0_charges"  value="{{ (isset($returnCharges[2][0]) && $returnCharges[2][0]->national_charges_class_0 !== '')? $returnCharges[2][0]->national_charges_class_0 : ''}}" {{$ol_return_sw}}>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <label class="card-title">National Charges Class B</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent" name="ol_return_class_1_charges"  value="{{ (isset($returnCharges[2][0]) && $returnCharges[2][0]->national_charges_class_1 !== '')? $returnCharges[2][0]->national_charges_class_1 : ''}}" {{$ol_return_sw}}>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <label class="card-title">National Charges Class C</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent" name="ol_return_class_2_charges"  value="{{ (isset($returnCharges[2][0]) && $returnCharges[2][0]->national_charges_class_2 !== '')? $returnCharges[2][0]->national_charges_class_2 : ''}}" {{$ol_return_sw}}>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <label class="card-title">National Charges Class D</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent" name="ol_return_class_3_charges"  value="{{ (isset($returnCharges[2][0]) && $returnCharges[2][0]->national_charges_class_3 !== '')? $returnCharges[2][0]->national_charges_class_3 : ''}}" {{$ol_return_sw}}>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Fuel Surcharge</h3>
                                            </div>
                                            @php
                                                $ol_fuel_sw = '';
                                                $ol_fuel_switch = '';
                                            if((isset($switches[2][0]) && $switches[2][0]->fuel_charges == 1)){
                                            $ol_fuel_sw = '';
                                            $ol_fuel_switch = 'checked';
                                             }else{
                                            $ol_fuel_sw = 'disabled';
                                            $ol_fuel_switch = '';
                                            }
                                            @endphp
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="overland_fuel_switch" class="switchery fuelSurchargeOverland" data-color="success" data-size="sm" {{$ol_fuel_switch}}/>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row fuel-surcharge-div-overland">
                                            <input type="hidden" name="ol_fuel_record" value="{{ (isset($fuelCharges[2][0]) && $fuelCharges[2][0]->id != '')? $fuelCharges[2][0]->id : ''}}">

                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Charges</label>
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <input type="text"  class="form-control " name="overland_fuel_surcharge" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($fuelCharges[2][0]) && $fuelCharges[2][0]->fuel_surcharge != '')? $fuelCharges[2][0]->fuel_surcharge : ''}}" {{$ol_fuel_sw}}>
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
                                        @php
                                        $ol_discount_id = '';
                                            if((isset($discountCharges[2][0])) && $discountCharges[2][0]->id != ''){
                                            $ol_discount_id = $discountCharges[2][0]->id;
                                            }
                                            $ol_discount_title_switch = '';
                                                $ol_discount_title = '';
                                            if((isset($discountCharges[2][0]) && $discountCharges[2][0]->title != '')){
                                            $ol_discount_title = $discountCharges[2][0]->title;
                                             }else{
                                            $ol_discount_title = '';
                                            }
                                            if((isset($discountCharges[2][0]->cash)) || (isset($discountCharges[2][0]->weight)) || (isset($discountCharges[2][0]->insurance)) || (isset($discountCharges[2][0]->return)) || (isset($discountCharges[2][0]->packaging))){
                                                $ol_discount_title_switch = '';
                                                }else{
                                                $ol_discount_title_switch = 'disabled';
                                                }
                                        @endphp
                                        <input type="hidden" name="ol_discount_record" value="{{$ol_discount_id}}">
                                        <div class="row mt-1">
                                            <div class="col-md-6">
                                                <label class="">Title</label>
                                                <div class='form-group'>
                                                    <input type='text' class="form-control" data-rule-required="true" data-msg-required="This field is required" {{$ol_discount_title_switch}} name="ol_discount_title" value="{{$ol_discount_title}}" />
                                                </div>

                                            </div>
                                            @php
                                                $ol_discount_daterange = '';
                                                $ol_discount_daterange_switch = '';
                                            if((isset($discountCharges[2][0]) && $discountCharges[2][0]->daterange != '')){

                                            $to = date('m/d/Y', strtotime($discountCharges[2][0]->to));
                                            $from = date('m/d/Y', strtotime($discountCharges[2][0]->from));

                                            $ol_discount_daterange = $to.' - '.$from;

                                             }else{
                                            $ol_discount_daterange = '';

                                            }
                                            if((isset($discountCharges[2][0]->cash)) || (isset($discountCharges[2][0]->weight)) || (isset($discountCharges[2][0]->insurance)) || (isset($discountCharges[2][0]->return)) || (isset($discountCharges[2][0]->packaging))){
                                            $ol_discount_daterange_switch = '';
                                            }else{
                                            $ol_discount_daterange_switch = 'disabled';
                                            }
                                            @endphp
                                            <div class="col-md-6">
                                                <label class="">Apply [to - from]</label>
                                                <div class='input-group form-group'>
                                                    <input type='text' class="form-control daterange" data-rule-required="true" data-msg-required="This field is required" {{$ol_discount_daterange_switch}} name="ol_daterange" {{$ol_discount_daterange_switch}} value="{{$ol_discount_daterange}}" />
                                                    <div class="input-group-append">
                                                            <span class="input-group-text">
                                                              <span class="la la-calendar"></span>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                         @php
                                            $ol_discount_weight_sw = '';
                                            $ol_discount_weight_switch = '';
                                            $ol_discount_weight_disable = '';
                                        if((isset($discountCharges[2][0]) && $discountCharges[2][0]->weight != '')){
                                        $ol_discount_weight_sw = $discountCharges[2][0]->weight;
                                        $ol_discount_weight_switch = 'checked';
                                        $ol_discount_weight_disable = '';
                                         }else{
                                        $ol_discount_weight_sw = '';
                                        $ol_discount_weight_switch = '';
                                        $ol_discount_weight_disable = 'disabled';
                                        }
                                        @endphp
                                        <div class="row">
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Weight</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox" id="" class="switchery discountSwitchesOverland" name="ol_discount_weight_switch" data-size="xs" {{$ol_discount_weight_switch}}/>
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent ol-discount-inp" name="ol_discount_weight_rate" {{$ol_discount_weight_disable}} value="{{$ol_discount_weight_sw}}">
                                                    </div>
                                                </fieldset>
                                            </div>
                                            @php
                                                $ol_discount_cash_sw = '';
                                                $ol_discount_cash_switch = '';
                                                $ol_discount_cash_disable = '';
                                            if((isset($discountCharges[2][0]) && $discountCharges[2][0]->cash != '')){
                                            $ol_discount_cash_sw = $discountCharges[2][0]->cash;
                                            $ol_discount_cash_switch = 'checked';
                                            $ol_discount_cash_disable = '';
                                             }else{
                                            $ol_discount_cash_sw = '';
                                            $ol_discount_cash_switch = '';
                                            $ol_discount_cash_disable = 'disabled';
                                            }
                                            @endphp
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Cash</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="">
                                                                <input type="checkbox" name="ol_cash_weight_switch" class="switchery discountSwitchesOverland" data-size="xs" {{$ol_discount_cash_switch}}/>
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent ol-discount-inp" name="ol_discount_cash_rate" {{$ol_discount_cash_disable}} value="{{$ol_discount_cash_sw}}">
                                                    </div>
                                                </fieldset>
                                            </div>
                                            @php
                                                $ol_discount_insurance_sw = '';
                                                $ol_discount_insurance_switch = '';
                                                $ol_discount_insurance_disable = '';
                                            if((isset($discountCharges[2][0]) && $discountCharges[2][0]->insurance != '')){
                                            $ol_discount_insurance_sw = $discountCharges[2][0]->insurance;
                                            $ol_discount_insurance_switch = 'checked';
                                            $ol_discount_insurance_disable = '';
                                             }else{
                                            $ol_discount_insurance_sw = '';
                                            $ol_discount_insurance_switch = '';
                                            $ol_discount_insurance_disable = 'disabled';
                                            }
                                            @endphp
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Insurance</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="">
                                                                <input type="checkbox" name="ol_discount_insurance_switch" class="switchery discountSwitchesOverland" data-size="xs" {{$ol_discount_insurance_switch}}/>
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent ol-discount-inp" name="ol_discount_insurance_rate" value="{{$ol_discount_insurance_sw}}" {{$ol_discount_insurance_disable}}>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            @php
                                                $ol_discount_return_sw = '';
                                                $ol_discount_return_switch = '';
                                                $ol_discount_return_disable = '';
                                            if((isset($discountCharges[2][0]) && $discountCharges[2][0]->return != '')){
                                            $ol_discount_return_sw = $discountCharges[2][0]->return;
                                            $ol_discount_return_switch = 'checked';
                                            $ol_discount_return_disable = '';
                                             }else{
                                            $ol_discount_return_sw = '';
                                            $ol_discount_return_switch = '';
                                            $ol_discount_return_disable = 'disabled';
                                            }
                                            @endphp
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Return</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="radio-addon3">
                                                                <input type="checkbox"  class="switchery discountSwitchesOverland" data-size="xs" name="ol_discount_return_switch" {{$ol_discount_return_switch}}/>
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent ol-discount-inp" name="ol_discount_return_rate" value="{{$ol_discount_return_sw}}" {{$ol_discount_return_disable}}>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            @php
                                                $ol_discount_packaging_sw = '';
                                                $ol_discount_packaging_switch = '';
                                                $ol_discount_packaging_disable = '';
                                            if((isset($discountCharges[2][0]) && $discountCharges[2][0]->packaging != '')){
                                            $ol_discount_packaging_sw = $discountCharges[2][0]->packaging;
                                            $ol_discount_packaging_switch = 'checked';
                                            $ol_discount_packaging_disable = '';

                                             }else{
                                            $ol_discount_packaging_sw = '';
                                            $ol_discount_packaging_switch = '';
                                            $ol_discount_packaging_disable = 'disabled';
                                            }
                                            @endphp
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Packaging</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="radio-addon3">
                                                                <input type="checkbox"  class="switchery discountSwitchesOverland" data-size="xs" name="ol_discount_packaging_switch" {{$ol_discount_packaging_switch}}/>
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent ol-discount-inp" name="ol_discount_packaging_rate" value="{{$ol_discount_packaging_sw}}" {{$ol_discount_packaging_disable}}>
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
                                        @if($sale_person['admin_id'] == \Illuminate\Support\Facades\Auth::id() || session('role_id') == 1)
                                            <label class="display-inline ml-1">Make Default</label>
                                            @if($shipper['default_shipping_mode'] == 3)
                                                <input type="checkbox" name="det_default" id="det_default" class="switchery det_default" checked data-size="xs" data-switchery="true">
                                            @else
                                                <input type="checkbox" name="det_default" id="det_default" class="switchery det_default" data-size="xs" data-switchery="true">
                                            @endif
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <a id="detain_main_switch" href="javascript:void(0);" class="pull-right"><input name="detain_main_switch" type="checkbox" id="" class="switchery detain-main-switch" data-size="sm" {{ ((isset($switches[3][0]) && $switches[3][0]->status == 1) ? 'checked' : '') }}/></a>
                                    </div>
                                </div>

                            </div>
                            <div id="detain" class="border-success no-border-top card {{ ((isset($switches[3][0]) && $switches[3][0]->status == 1) ? '' : 'hide') }}"
                                 aria-expanded="false">
                                 <input type="hidden" name="det_rate_record" value="{{ ((isset($switches[3][0]) && $switches[3][0]->id != '') ? $switches[3][0]->id : '') }}">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="weight-addition-detain">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Weight Charges</h3>
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
                                                    <label class="card-title">Weight Addition</label>
                                                </div>
                                                <div class="col-2 text-center">
                                                    <label class="card-title">KG Range</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Local Charges</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">National Charges Class A</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">National Charges Class B</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">National Charges Class C</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">National Charges Class D</label>
                                                </div>
                                                <div class="col-1"></div>
                                            </div>
                                            @if(isset($weight[3]))
                                            @foreach($weight[3] as $index => $detweight)
                                                <div class="row det_weight_row">
                                                    <input type="hidden" name="detain_weight_record[{{$index}}]" value="{{$detweight->id}}">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->range_up}}" name="detain_wa_range_up[{{$index}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal"data-rule-required="true" data-msg-required="This field is required"  value="{{$detweight->range_down}}" name="detain_wa_range_down[{{$index}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="DetainSwitch{{$index}}" class="switchery weightAdditionDetain" data-color="success" data-size="sm" name="detain_wa_switch[{{$index}}]" {{ ($detweight->weight_addition == 1) ? 'checked' : '' }}/>
                                                        </div>
                                                    </div>
                                                    <div class="col-2 text-center">

                                                        <fieldset style="padding-top: 5px;">
                                                            <div class="input-group input-group-sm form-group">
                                                                <input type="text" class="touchspin-color input-sm spkg" value="{{$detweight->spkg}}" {{ ($detweight->weight_addition == 1) ? '' : 'disabled' }} data-bts-button-down-class="btn btn-success"
                                                                       data-bts-button-up-class="btn btn-success" name="detain_wa_spkg[{{$index}}]">
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->local_or_6hr}}" name="detain_wa_local_charges[{{$index}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->national_charges_class_0}}" name="detain_class_0_charges[{{$index}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->national_charges_class_1}}" name="detain_class_1_charges[{{$index}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->national_charges_class_2}}" name="detain_class_2_charges[{{$index}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->national_charges_class_3}}" name="detain_class_3_charges[{{$index}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-1">
                                                        @if($index>0)
                                                            <span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 detain_weight_close"><i class="ft-x"></i></span>
                                                        @endif
                                                    </div>

                                                </div>{{--Row--}}
                                            @endforeach
                                                @else
                                                    <div class="row det_weight_row">
                                                        <input type="hidden" name="detain_weight_record[0]" value="">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_wa_range_up[0]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal"data-rule-required="true" data-msg-required="This field is required"  value="" name="detain_wa_range_down[0]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="DetainSwitch0" class="switchery weightAdditionDetain" data-color="success" data-size="sm" name="detain_wa_switch[0]"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-2 text-center">

                                                        <fieldset style="padding-top: 5px;">
                                                            <div class="input-group input-group-sm form-group">
                                                                <input type="text" class="touchspin-color input-sm spkg" value="0" disabled data-bts-button-down-class="btn btn-success"
                                                                       data-bts-button-up-class="btn btn-success" name="detain_wa_spkg[0]">
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_wa_local_charges[0]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_class_0_charges[0]">
                                                        </fieldset>
                                                    </div>
                                                        <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_class_1_charges[0]">
                                                        </fieldset>
                                                    </div>
                                                        <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_class_2_charges[0]">
                                                        </fieldset>
                                                    </div>
                                                        <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_class_3_charges[0]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-1"></div>

                                                </div>{{--Row--}}
                                                @endif
                                        </div>{{--weight addition div--}}
                                        <div>
                                            <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="detain_weightadd"><i class="la la-plus"></i></button>
                                        </div>
                                        <div class="row mt-2">
                                            <input type="hidden" name="detain_booking_record" value="{{ (isset($shippingType[3][0]) && $shippingType[3][0]->id != '')? $shippingType[3][0]->id : ''}}">

                                            <div class="col-md-3 text-center">
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Replacement</span>
                                                        </div>
                                                        <input type="text"  class="form-control percent" name="detain_replacement_charges" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($shippingType[3][0]) && $shippingType[3][0]->replacement_charges != '')? $shippingType[3][0]->replacement_charges : ''}}">
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
                                                        <input type="text"  class="form-control percent" name="detain_tnb_charges" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($shippingType[3][0]) && $shippingType[3][0]->try_and_buy_charges != '')? $shippingType[3][0]->try_and_buy_charges : ''}}">
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
                                            @php
                                                $det_cash_sw = '';
                                                $det_cash_switch = '';
                                             if((isset($switches[3][0]) && $switches[3][0]->cash_handling_charges == 1)){
                                            $det_cash_sw = '';
                                            $det_cash_switch = 'checked';
                                             }else{
                                            $det_cash_sw = 'disabled';
                                            $det_cash_switch = '';
                                            }
                                            @endphp
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="detain_cash_handling_switch"  class="switchery cashChargesDetain" data-color="success" data-size="sm" {{$det_cash_switch}}/>
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
                                             @if(isset($cashHandling[3]))
                                            @foreach($cashHandling[3] as $index => $cash)
                                                <div class="row det_cash_handling_row">
                                                    <input type="hidden" name="detain_cash_record[{{$index}}]" value="{{$cash->id}}">
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="detain_cash_range_up[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->range_up}}" {{$det_cash_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="detain_cash_range_down[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->range_down}}" {{$det_cash_sw}}>
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <fieldset class="form-group">
                                                            <input name="detain_cash_charges[{{$index}}]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->charges}}" {{$det_cash_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        @if($index>0)
                                                            <span class="detain_row_delete btn btn-danger rounded btn-sm-width mr-1 mb-1"><i class="ft-x"></i></span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                            @else
                                                <div class="row det_cash_handling_row">
                                                    <input type="hidden" name="detain_cash_record[0]" value="">

                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="detain_cash_range_up[0]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="" {{$det_cash_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="detain_cash_range_down[0]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="" {{$det_cash_sw}}>
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <fieldset class="form-group">
                                                            <input name="detain_cash_charges[0]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" {{$det_cash_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col"></div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="cash-handling-btn-detain">
                                            <button id="detainaddMoreSlabs" type="button" class="btn btn-outline-success mr-1" title="Add more slabs" {{$det_cash_sw}}><i class="la la-plus"></i></button>
                                        </div>
                                        <hr>

                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Insurance Charges</h3>
                                            </div>
                                            @php
                                                $det_ins_sw = '';
                                                $det_insurance_switch = '';
                                            if((isset($switches[3][0]) && $switches[3][0]->insurance_charges == 1)){
                                            $det_ins_sw = '';
                                            $det_insurance_switch = 'checked';
                                             }else{
                                            $det_ins_sw = 'disabled';
                                            $det_insurance_switch = '';
                                            }
                                            @endphp
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="detain_insurance_charges_switch" class="switchery insuranceChargesdetain" data-color="success" data-size="sm" {{$det_insurance_switch}}/>
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
                                            @if(isset($insuranceCharges[3]))
                                            @foreach($insuranceCharges[3] as $index => $det_insurance)
                                            <input type="hidden" name="detain_insurance_record[{{$index}}]" value="{{$det_insurance->id}}">
                                                <div class="row det_insurance_row">
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="detain_ins_range_up[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$det_insurance->range_up}}" {{$det_ins_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="detain_ins_range_down[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$det_insurance->range_down}}" {{$det_ins_sw}}>
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <fieldset class="form-group">
                                                            <input name="detain_ins_charges[{{$index}}]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$det_insurance->charges}}" {{$det_ins_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        @if($index>0)
                                                            <span class="btn btn-danger rounded btn-sm-width mr-1 mb-1 detain_row_delete"><i class="ft-x"></i></span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                            @else
                                                 <div class="row det_insurance_row">
                                            <input type="hidden" name="detain_insurance_record[0]" value="">

                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="detain_ins_range_up[0]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="" {{$det_ins_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="detain_ins_range_down[0]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="" {{$det_ins_sw}}>
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <fieldset class="form-group">
                                                            <input name="detain_ins_charges[0]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" {{$det_ins_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col"></div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="insurance-charges-btn-detain">
                                            <button id="detainaddMoreSlabsInsurance" type="button" class="btn btn-outline-success mr-1" title="Add more slabs" {{$det_ins_sw}}><i class="la la-plus"></i></button>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Return Charges</h3>
                                            </div>
                                            @php
                                                $det_return_sw = '';
                                                $det_return_switch = '';
                                            if((isset($switches[3][0]) && $switches[3][0]->return_charges == 1)){
                                            $det_return_sw = '';
                                            $det_return_switch = 'checked';
                                             }else{
                                            $det_return_sw = 'disabled';
                                            $det_return_switch = '';
                                            }
                                            @endphp
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="detain_return_switch" class="switchery returnChargesDetain" data-color="success" data-size="sm" {{$det_return_switch}}/>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row return-charges-div-detain">
                                        <input type="hidden" name="detain_return_record" value="{{ (isset($returnCharges[3][0]) && $returnCharges[3][0]->id != '')? $returnCharges[3][0]->id : ''}}">

                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Local Charges</label>
                                                <fieldset class="form-group">
                                                    <input type="text" class="form-control amount" name="detain_return_local_charges" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($returnCharges[3][0]) && $returnCharges[3][0]->local !== '')? $returnCharges[3][0]->local : ''}}" {{$det_return_sw}}>
                                                </fieldset>
                                            </div>

                                            <div class="col text-center">
                                                <label class="card-title">National Charges Class A</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control amount" name="detain_return_class_0_charges"  value="{{ (isset($returnCharges[3][0]) && $returnCharges[3][0]->national_charges_class_0 !== '')? $returnCharges[3][0]->national_charges_class_0 : ''}}" {{$det_return_sw}}>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <label class="card-title">National Charges Class B</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent" name="detain_return_class_1_charges"  value="{{ (isset($returnCharges[3][0]) && $returnCharges[3][0]->national_charges_class_1 !== '')? $returnCharges[3][0]->national_charges_class_1 : ''}}" {{$det_return_sw}}>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <label class="card-title">National Charges Class C</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent" name="detain_return_class_2_charges"  value="{{ (isset($returnCharges[3][0]) && $returnCharges[3][0]->national_charges_class_2 !== '')? $returnCharges[3][0]->national_charges_class_2 : ''}}" {{$det_return_sw}}>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <label class="card-title">National Charges Class D</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent" name="detain_return_class_3_charges"  value="{{ (isset($returnCharges[3][0]) && $returnCharges[3][0]->national_charges_class_3 !== '')? $returnCharges[3][0]->national_charges_class_3 : ''}}" {{$det_return_sw}}>
                                                </fieldset>
                                            </div>
                                        </div>

                                        <hr>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Fuel Surcharge</h3>
                                            </div>
                                            @php
                                                $det_fuel_sw = '';
                                                $det_fuel_switch = '';
                                            if((isset($switches[3][0]) && $switches[3][0]->fuel_charges == 1)){
                                            $det_fuel_sw = '';
                                            $det_fuel_switch = 'checked';
                                             }else{
                                            $det_fuel_sw = 'disabled';
                                            $det_fuel_switch = '';
                                            }
                                            @endphp
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="detain_fuel_switch" class="switchery fuelSurchargeDetain" data-color="success" data-size="sm" {{$det_fuel_switch}}/>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row fuel-surcharge-div-detain">
                                        <input type="hidden" name="detain_fuel_record" value="{{ (isset($fuelCharges[3][0]) && $fuelCharges[3][0]->id != '')? $fuelCharges[3][0]->id : ''}}">

                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Charges</label>
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <input type="text"  class="form-control " name="detain_fuel_surcharge" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($fuelCharges[3][0]) && $fuelCharges[3][0]->fuel_surcharge != '')? $fuelCharges[3][0]->fuel_surcharge : ''}}" {{$det_fuel_sw}}>
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
                                        @php
                                        $detain_discount_id = '';
                                            if((isset($discountCharges[3][0])) && $discountCharges[3][0]->id != ''){
                                            $detain_discount_id = $discountCharges[3][0]->id;
                                            }
                                            $det_discount_title_switch = '';
                                            $det_discount_title = '';
                                            if((isset($discountCharges[3][0]) && $discountCharges[3][0]->title != '')){
                                            $det_discount_title = $discountCharges[3][0]->title;
                                             }else{
                                            $det_discount_title = '';
                                            }
                                            if((isset($discountCharges[3][0]->cash)) || (isset($discountCharges[3][0]->weight)) || (isset($discountCharges[3][0]->insurance)) || (isset($discountCharges[3][0]->return)) || (isset($discountCharges[3][0]->packaging))){
                                                $det_discount_title_switch = '';
                                                }else{
                                                $det_discount_title_switch = 'disabled';
                                                }
                                        @endphp
                                        <input type="hidden" name="detain_discount_record" value="{{$detain_discount_id}}">

                                        <div class="row mt-1">
                                            <div class="col-md-6">
                                                <label class="">Title</label>
                                                <div class='form-group'>
                                                    <input type='text' class="form-control" data-rule-required="true" data-msg-required="This field is required" {{$det_discount_title_switch}} name="detain_discount_title" value="{{$det_discount_title}}"/>
                                                </div>

                                            </div>
                                            @php
                                                $det_discount_daterange = '';
                                                $det_discount_daterange_switch = '';
                                            if((isset($discountCharges[3][0]) && $discountCharges[3][0]->daterange != '')){

                                            $to = date('m/d/Y', strtotime($discountCharges[3][0]->to));
                                            $from = date('m/d/Y', strtotime($discountCharges[3][0]->from));

                                            $det_discount_daterange = $to.' - '.$from;

                                             }else{
                                            $det_discount_daterange = '';

                                            }
                                            if((isset($discountCharges[3][0]->cash)) || (isset($discountCharges[3][0]->weight)) || (isset($discountCharges[3][0]->insurance)) || (isset($discountCharges[3][0]->return)) || (isset($discountCharges[3][0]->packaging))){
                                            $det_discount_daterange_switch = '';
                                            }else{
                                            $det_discount_daterange_switch = 'disabled';
                                            }
                                            @endphp
                                            <div class="col-md-6">
                                                <label class="">Apply [to - from]</label>
                                                <div class='input-group form-group'>
                                                    <input type='text' class="form-control daterange" data-rule-required="true" data-msg-required="This field is required" name="detain_daterange" value="{{$det_discount_daterange}}"  {{$det_discount_daterange_switch}}/>
                                                    <div class="input-group-append">
                                                            <span class="input-group-text">
                                                              <span class="la la-calendar"></span>
                                                            </span>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        @php
                                            $det_discount_weight_sw = '';
                                            $det_discount_weight_switch = '';
                                            $det_discount_weight_disable = '';
                                        if((isset($discountCharges[3][0]) && $discountCharges[3][0]->weight != '')){
                                        $det_discount_weight_sw = $discountCharges[3][0]->weight;
                                        $det_discount_weight_switch = 'checked';
                                        $det_discount_weight_disable = '';
                                         }else{
                                        $det_discount_weight_sw = '';
                                        $det_discount_weight_switch = '';
                                        $det_discount_weight_disable = 'disabled';
                                        }
                                        @endphp
                                        <div class="row">
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Weight</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox" id="" class="switchery discountSwitchesDetain" name="detain_discount_weight_switch" data-size="xs" {{$det_discount_weight_switch}}/>
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent detain-discount-inp" name="detain_discount_weight_rate" value="{{$det_discount_weight_sw}}" {{$det_discount_weight_disable}}>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            @php
                                                $det_discount_cash_sw = '';
                                                $det_discount_cash_switch = '';
                                                $det_discount_cash_disable = '';
                                            if((isset($discountCharges[3][0]) && $discountCharges[3][0]->cash != '')){
                                            $det_discount_cash_sw = $discountCharges[3][0]->cash;
                                            $det_discount_cash_switch = 'checked';
                                            $det_discount_cash_disable = '';
                                             }else{
                                            $det_discount_cash_sw = '';
                                            $det_discount_cash_switch = '';
                                            $det_discount_cash_disable = 'disabled';
                                            }
                                            @endphp
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Cash</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="">
                                                                <input type="checkbox" name="detain_cash_weight_switch" class="switchery discountSwitchesDetain" data-size="xs" {{$det_discount_cash_switch}}/>
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent detain-discount-inp" name="detain_discount_cash_rate" value="{{$det_discount_cash_sw}}" {{$det_discount_cash_disable}}>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            @php
                                                $det_discount_insurance_sw = '';
                                                $det_discount_insurance_switch = '';
                                                $det_discount_insurance_disable = '';
                                            if((isset($discountCharges[3][0]) && $discountCharges[3][0]->insurance != '')){
                                            $det_discount_insurance_sw = $discountCharges[3][0]->insurance;
                                            $det_discount_insurance_switch = 'checked';
                                            $det_discount_insurance_disable = '';
                                             }else{
                                            $det_discount_insurance_sw = '';
                                            $det_discount_insurance_switch = '';
                                            $det_discount_insurance_disable = 'disabled';
                                            }
                                            @endphp
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Insurance</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="">
                                                                <input type="checkbox" name="detain_discount_insurance_switch" class="switchery discountSwitchesDetain" data-size="xs" {{$det_discount_insurance_switch}}/>
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent detain-discount-inp" name="detain_discount_insurance_rate" value="{{$det_discount_insurance_sw}}" {{$det_discount_insurance_disable}}>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            @php
                                                $det_discount_return_sw = '';
                                                $det_discount_return_switch = '';
                                                $det_discount_return_disable = '';
                                            if((isset($discountCharges[3][0]) && $discountCharges[3][0]->return != '')){
                                            $det_discount_return_sw = $discountCharges[3][0]->return;
                                            $det_discount_return_switch = 'checked';
                                            $det_discount_return_disable = '';
                                             }else{
                                            $det_discount_return_sw = '';
                                            $det_discount_return_switch = '';
                                            $det_discount_return_disable = 'disabled';
                                            }
                                            @endphp
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Return</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox"  class="switchery discountSwitchesDetain" data-size="xs" name="detain_discount_return_switch" {{$det_discount_return_switch}}/>
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent detain-discount-inp" name="detain_discount_return_rate" value="{{$det_discount_return_sw}}" {{$det_discount_return_disable}}>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            @php
                                                $det_discount_packaging_sw = '';
                                                $det_discount_packaging_switch = '';
                                                $det_discount_packaging_disable = '';
                                            if((isset($discountCharges[3][0]) && $discountCharges[3][0]->packaging != '')){
                                            $det_discount_packaging_sw = $discountCharges[3][0]->packaging;
                                            $det_discount_packaging_switch = 'checked';
                                            $det_discount_packaging_disable = '';

                                             }else{
                                            $det_discount_packaging_sw = '';
                                            $det_discount_packaging_switch = '';
                                            $det_discount_packaging_disable = 'disabled';
                                            }
                                            @endphp
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Packaging</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox"  class="switchery discountSwitchesDetain" data-size="xs" name="detain_discount_packaging_switch" {{$det_discount_packaging_switch}}/>
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent detain-discount-inp" name="detain_discount_packaging_rate" value="{{$det_discount_packaging_sw}}" {{$det_discount_packaging_disable}}>
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
                                        @if($sale_person['admin_id'] == \Illuminate\Support\Facades\Auth::id() || session('role_id') == 1)
                                            <label class="display-inline ml-1">Make Default</label>
                                            @if($shipper['default_shipping_mode'] == 4)
                                                <input type="checkbox" name="sameday_default" id="sameday_default" class="switchery sameday_default" checked data-size="xs" data-switchery="true">
                                            @else
                                                <input type="checkbox" name="sameday_default" id="sameday_default" class="switchery sameday_default" data-size="xs" data-switchery="true">
                                            @endif
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <a id="sameday_main_switch" href="javascript:void(0);" class="pull-right"><input name="sameday_main_switch" type="checkbox" id="" class="switchery sameday-main-switch" data-size="sm" {{ ((isset($switches[4][0]) && $switches[4][0]->status == 1) ? 'checked' : '') }}/></a>
                                    </div>
                                </div>

                            </div>
                            <div id="sameday" class="border-success no-border-top card {{ ((isset($switches[4][0]) && $switches[4][0]->status == 1) ? '' : 'hide') }}"
                                 aria-expanded="false" >
                                 <input type="hidden" name="same_rate_record" value="{{ ((isset($switches[4][0]) && $switches[4][0]->id != '') ? $switches[4][0]->id : '') }}">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="weight-addition-sameday">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Weight Charges</h3>
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
                                                    <label class="card-title">Weight Addition</label>
                                                </div>
                                                <div class="col-2 text-center">
                                                    <label class="card-title">KG Range</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">6hr Charges</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Sameday Charges</label>
                                                </div>

                                                <div class="col-1"></div>

                                            </div>
                                            @if(isset($weight[4]))
                                            @foreach($weight[4] as $index => $sameweight)
                                                <div class="row same_weight_row">
                                                   <input type="hidden" name="sameday_weight_record[{{$index}}]" value="{{$sameweight->id}}">

                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->range_up}}" name="sameday_wa_range_up[{{$index}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->range_down}}" name="sameday_wa_range_down[{{$index}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="SamedaySwitch{{$index}}" class="switchery weightAdditionSameday" data-color="success" data-size="sm" name="sameday_wa_switch[{{$index}}]" {{ ($sameweight->weight_addition == 1) ? 'checked' : '' }}/>
                                                        </div>
                                                    </div>
                                                    <div class="col-2 text-center">

                                                        <fieldset style="padding-top: 5px;">
                                                            <div class="input-group input-group-sm form-group">
                                                                <input type="text" class="touchspin-color input-sm spkg" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->spkg}}" {{ ($sameweight->weight_addition == 1) ? '' : 'disabled' }} data-bts-button-down-class="btn btn-success"
                                                                       data-bts-button-up-class="btn btn-success" name="sameday_wa_spkg[{{$index}}]">
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->local_or_6hr}}" name="sameday_wa_local_charges[{{$index}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->national_charges_class_0}}" name="sameday_class_0_charges[{{$index}}]">
                                                        </fieldset>
                                                    </div>
                                                    
                                                    <div class="col-1">
                                                        @if($index>0)
                                                            <span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 sameday_weight_close"><i class="ft-x"></i></span>
                                                        @endif
                                                    </div>
                                                </div>{{--Row--}}
                                            @endforeach
                                                @else
                                                <div class="row same_weight_row">
                                                   <input type="hidden" name="sameday_weight_record[0]" value="">

                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_wa_range_up[0]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_wa_range_down[0]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="SamedaySwitch0" class="switchery weightAdditionSameday" data-color="success" data-size="sm" name="sameday_wa_switch[0]"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-2 text-center">

                                                        <fieldset style="padding-top: 5px;">
                                                            <div class="input-group input-group-sm form-group">
                                                                <input type="text" class="touchspin-color input-sm spkg" data-rule-required="true" data-msg-required="This field is required" value="0" disabled data-bts-button-down-class="btn btn-success"
                                                                       data-bts-button-up-class="btn btn-success" name="sameday_wa_spkg[0]">
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_wa_local_charges[0]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_class_0_charges[0]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-1"></div>
                                                </div>{{--Row--}}
                                                @endif
                                        </div>{{--weight addition div--}}
                                        <div>
                                            <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="sameday_weightadd"><i class="la la-plus"></i></button>
                                        </div>
                                        <div class="row mt-2">
                                        <input type="hidden" name="sameday_booking_record" value="{{ (isset($shippingType[4][0]) && $shippingType[4][0]->id != '')? $shippingType[4][0]->id : ''}}">

                                            <div class="col-md-3 text-center">
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Replacement</span>
                                                        </div>
                                                        <input type="text"  class="form-control percent" name="sameday_replacement_charges" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($shippingType[4][0]) && $shippingType[4][0]->replacement_charges != '')? $shippingType[4][0]->replacement_charges : ''}}">
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
                                                        <input type="text"  class="form-control percent" name="sameday_tnb_charges" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($shippingType[4][0]) && $shippingType[4][0]->try_and_buy_charges != '')? $shippingType[4][0]->try_and_buy_charges : ''}}">
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
                                            @php
                                                $same_cash_sw = '';
                                                $same_cash_switch = '';
                                             if((isset($switches[4][0]) && $switches[4][0]->cash_handling_charges == 1)){
                                            $same_cash_sw = '';
                                            $same_cash_switch = 'checked';
                                             }else{
                                            $same_cash_sw = 'disabled';
                                            $same_cash_switch = '';
                                            }
                                            @endphp
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="sameday_cash_handling_switch"  class="switchery cashChargesSameday" data-color="success" data-size="sm" {{$same_cash_switch}}/>
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
                                            @if(isset($cashHandling[4]))
                                            @foreach($cashHandling[4] as $index => $cash)
                                                <div class="row same_cash_handling_row">
                                                <input type="hidden" name="sameday_cash_record[{{$index}}]" value="{{$cash->id}}">

                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="sameday_cash_range_up[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->range_up}}" {{$same_cash_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="sameday_cash_range_down[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->range_down}}" {{$same_cash_sw}}>
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="sameday_cash_charges[{{$index}}]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->charges}}" {{$same_cash_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        @if($index>0)
                                                            <span class="sameday_row_delete btn btn-danger rounded btn-sm-width mr-1 mb-1"><i class="ft-x"></i></span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                            @else
                                                <div class="row same_cash_handling_row">
                                                <input type="hidden" name="sameday_cash_record[0]" value="">

                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="sameday_cash_range_up[0]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="" {{$same_cash_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="sameday_cash_range_down[0]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="" {{$same_cash_sw}}>
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="sameday_cash_charges[0]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" {{$same_cash_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col"></div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="cash-handling-btn-sameday">
                                            <button id="samedayaddMoreSlabs" type="button" class="btn btn-outline-success mr-1" title="Add more slabs" {{$same_cash_sw}}><i class="la la-plus"></i></button>
                                        </div>
                                        <hr>

                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Insurance Charges</h3>
                                            </div>
                                            @php
                                                $same_ins_sw = '';
                                                $same_insurance_switch = '';
                                            if((isset($switches[4][0]) && $switches[4][0]->insurance_charges == 1)){
                                            $same_ins_sw = '';
                                            $same_insurance_switch = 'checked';
                                             }else{
                                            $same_ins_sw = 'disabled';
                                            $same_insurance_switch = '';
                                            }
                                            @endphp
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="sameday_insurance_charges_switch" class="switchery insuranceChargessameday" data-color="success" data-size="sm" {{$same_insurance_switch}}/>
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
                                            @if(isset($insuranceCharges[4]))
                                            @foreach($insuranceCharges[4] as $index => $same_insurance)
                                            <input type="hidden" name="sameday_insurance_record[{{$index}}]" value="{{$same_insurance->id}}">

                                                <div class="row same_insurance_row">
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="sameday_ins_range_up[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$same_insurance->range_up}}" {{$same_ins_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="sameday_ins_range_down[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$same_insurance->range_down}}" {{$same_ins_sw}}>
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="sameday_ins_charges[{{$index}}]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$same_insurance->charges}}" {{$same_ins_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        @if($index>0)
                                                            <span class="btn btn-danger rounded btn-sm-width mr-1 mb-1 sameday_row_delete"><i class="ft-x"></i></span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                            @else
                                                <div class="row same_insurance_row">
                                            <input type="hidden" name="sameday_insurance_record[0]" value="">

                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="sameday_ins_range_up[0]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="" {{$same_ins_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="sameday_ins_range_down[0]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="" {{$same_ins_sw}}>
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="sameday_ins_charges[0]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" {{$same_ins_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col"></div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="insurance-charges-btn-sameday">
                                            <button id="samedayaddMoreSlabsInsurance" type="button" class="btn btn-outline-success mr-1" title="Add more slabs" {{$same_ins_sw}}><i class="la la-plus"></i></button>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Return Charges</h3>
                                            </div>
                                            @php
                                                $same_return_sw = '';
                                                $same_return_switch = '';
                                            if((isset($switches[4][0]) && $switches[4][0]->return_charges == 1)){
                                            $same_return_sw = '';
                                            $same_return_switch = 'checked';
                                             }else{
                                            $same_return_sw = 'disabled';
                                            $same_return_switch = '';
                                            }
                                            @endphp
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="sameday_return_switch" class="switchery returnChargesSameday" data-color="success" data-size="sm" {{$same_return_switch}}/>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row return-charges-div-sameday">
                                        <input type="hidden" name="sameday_return_record" value="{{ (isset($returnCharges[4][0]) && $returnCharges[4][0]->id != '')? $returnCharges[4][0]->id : ''}}">

                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Local Charges</label>
                                                <fieldset class="form-group">
                                                    <input type="text"  class="form-control amount" name="sameday_return_local_charges"  data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($returnCharges[4][0]) && $returnCharges[4][0]->local !== '')? $returnCharges[4][0]->local : ''}}" {{$same_return_sw}}>
                                                </fieldset>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <label class="card-title">National Charges</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control amount" name="sameday_return_class_0_charges"  value="{{ (isset($returnCharges[4][0]) && $returnCharges[4][0]->national_charges_class_0 !== '')? $returnCharges[4][0]->national_charges_class_0 : ''}}" {{$same_return_sw}}>
                                                </fieldset>
                                            </div>
                                        </div>

                                        <hr>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Fuel Surcharge</h3>
                                            </div>
                                            @php
                                                $same_fuel_sw = '';
                                                $same_fuel_switch = '';
                                            if((isset($switches[4][0]) && $switches[4][0]->fuel_charges == 1)){
                                            $same_fuel_sw = '';
                                            $same_fuel_switch = 'checked';
                                             }else{
                                            $same_fuel_sw = 'disabled';
                                            $same_fuel_switch = '';
                                            }
                                            @endphp
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="sameday_fuel_switch" class="switchery fuelSurchargeSameday" data-color="success" data-size="sm" {{$same_fuel_switch}}/>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row fuel-surcharge-div-sameday">
                                        <input type="hidden" name="sameday_fuel_record" value="{{ (isset($fuelCharges[4][0]) && $fuelCharges[4][0]->id != '')? $fuelCharges[4][0]->id : ''}}">

                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Charges</label>
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <input type="text"  class="form-control " name="sameday_fuel_surcharge" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($fuelCharges[4][0]) && $fuelCharges[4][0]->fuel_surcharge != '')? $fuelCharges[4][0]->fuel_surcharge : ''}}" {{$same_fuel_sw}}>
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
                                        @php
                                            $sameday_discount_id = '';
                                            if((isset($discountCharges[4][0])) && $discountCharges[4][0]->id != ''){
                                            $sameday_discount_id = $discountCharges[4][0]->id;
                                            }
                                            $same_discount_title_switch = '';
                                                $same_discount_title = '';
                                            if((isset($discountCharges[4][0]) && $discountCharges[4][0]->title != '')){
                                            $same_discount_title = $discountCharges[4][0]->title;
                                             }else{
                                            $same_discount_title = '';
                                            }
                                            if((isset($discountCharges[4][0]->cash)) || (isset($discountCharges[4][0]->weight)) || (isset($discountCharges[4][0]->insurance)) || (isset($discountCharges[4][0]->return)) || (isset($discountCharges[4][0]->packaging))){
                                                $same_discount_title_switch = '';
                                                }else{
                                                $same_discount_title_switch = 'disabled';
                                                }
                                        @endphp
                                            <input type="hidden" name="sameday_discount_record" value="{{$sameday_discount_id}}">

                                        <div class="row mt-1">
                                            <div class="col-md-6">
                                                <label class="">Title</label>
                                                <div class='form-group'>
                                                    <input type='text' class="form-control" data-rule-required="true" data-msg-required="This field is required"  name="sameday_discount_title" value="{{$same_discount_title}}" {{$same_discount_title_switch}}/>
                                                </div>

                                            </div>
                                            @php
                                                $same_discount_daterange = '';
                                                $same_discount_daterange_switch = '';
                                            if((isset($discountCharges[4][0]) && $discountCharges[4][0]->daterange != '')){

                                            $to = date('m/d/Y', strtotime($discountCharges[4][0]->to));
                                            $from = date('m/d/Y', strtotime($discountCharges[4][0]->from));

                                            $same_discount_daterange = $to.' - '.$from;

                                             }else{
                                            $same_discount_daterange = '';

                                            }
                                            if((isset($discountCharges[4][0]->cash)) || (isset($discountCharges[4][0]->weight)) || (isset($discountCharges[4][0]->insurance)) || (isset($discountCharges[4][0]->return)) || (isset($discountCharges[4][0]->packaging))){
                                            $same_discount_daterange_switch = '';
                                            }else{
                                            $same_discount_daterange_switch = 'disabled';
                                            }
                                            @endphp
                                            <div class="col-md-6">
                                                <label class="">Apply [to - from]</label>
                                                <div class='input-group form-group'>
                                                    <input type='text' class="form-control daterange" data-rule-required="true" data-msg-required="This field is required" {{$same_discount_daterange_switch}} name="sameday_daterange" value="{{$same_discount_daterange}}"/>
                                                    <div class="input-group-append">
                                                            <span class="input-group-text">
                                                              <span class="la la-calendar"></span>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @php
                                            $same_discount_weight_sw = '';
                                            $same_discount_weight_switch = '';
                                            $same_discount_weight_disable = '';
                                        if((isset($discountCharges[4][0]) && $discountCharges[4][0]->weight != '')){
                                        $same_discount_weight_sw = $discountCharges[4][0]->weight;
                                        $same_discount_weight_switch = 'checked';
                                        $same_discount_weight_disable = '';
                                         }else{
                                        $same_discount_weight_sw = '';
                                        $same_discount_weight_switch = '';
                                        $same_discount_weight_disable = 'disabled';
                                        }
                                        @endphp
                                        <div class="row">
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Weight</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox" id="" class="switchery discountSwitchesSameday" name="sameday_discount_weight_switch" data-size="xs" {{$same_discount_weight_switch}}/>
                                                              </span>
                                                        </div>
                                                        <input type="text"  class="form-control dec-percent sameday-discount-inp"  data-rule-required="true" data-msg-required="This field is required" name="sameday_discount_weight_rate" {{$same_discount_weight_disable}} value="{{$same_discount_weight_sw}}">
                                                    </div>
                                                </fieldset>
                                            </div>
                                            @php
                                                $same_discount_cash_sw = '';
                                                $same_discount_cash_switch = '';
                                                $same_discount_cash_disable = '';
                                            if((isset($discountCharges[4][0]) && $discountCharges[4][0]->cash != '')){
                                            $same_discount_cash_sw = $discountCharges[4][0]->cash;
                                            $same_discount_cash_switch = 'checked';
                                            $same_discount_cash_disable = '';
                                             }else{
                                            $same_discount_cash_sw = '';
                                            $same_discount_cash_switch = '';
                                            $same_discount_cash_disable = 'disabled';
                                            }
                                            @endphp
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Cash</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="">
                                                                <input type="checkbox" name="sameday_cash_weight_switch" class="switchery discountSwitchesSameday" data-size="xs" {{$same_discount_cash_switch}}/>
                                                              </span>
                                                        </div>
                                                        <input type="text"  class="form-control dec-percent sameday-discount-inp"  data-rule-required="true" data-msg-required="This field is required" name="sameday_discount_cash_rate" value="{{$same_discount_cash_sw}}" {{$same_discount_cash_disable}}>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            @php
                                                $same_discount_insurance_sw = '';
                                                $same_discount_insurance_switch = '';
                                                $same_discount_insurance_disable = '';
                                            if((isset($discountCharges[4][0]) && $discountCharges[4][0]->insurance != '')){
                                            $same_discount_insurance_sw = $discountCharges[4][0]->insurance;
                                            $same_discount_insurance_switch = 'checked';
                                            $same_discount_insurance_disable = '';
                                             }else{
                                            $same_discount_insurance_sw = '';
                                            $same_discount_insurance_switch = '';
                                            $same_discount_insurance_disable = 'disabled';
                                            }
                                            @endphp
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Insurance</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="">
                                                                <input type="checkbox" name="sameday_discount_insurance_switch" class="switchery discountSwitchesSameday" data-size="xs" {{$same_discount_insurance_switch}}/>
                                                              </span>
                                                        </div>
                                                        <input type="text"  class="form-control dec-percent sameday-discount-inp"  data-rule-required="true" data-msg-required="This field is required" name="sameday_discount_insurance_rate" value="{{$same_discount_insurance_sw}}" {{$same_discount_insurance_disable}}>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            @php
                                                $same_discount_return_sw = '';
                                                $same_discount_return_switch = '';
                                                $same_discount_return_disable = '';
                                            if((isset($discountCharges[4][0]) && $discountCharges[4][0]->return != '')){
                                            $same_discount_return_sw = $discountCharges[4][0]->return;
                                            $same_discount_return_switch = 'checked';
                                            $same_discount_return_disable = '';
                                             }else{
                                            $same_discount_return_sw = '';
                                            $same_discount_return_switch = '';
                                            $same_discount_return_disable = 'disabled';
                                            }
                                            @endphp
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Return</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox"  class="switchery discountSwitchesSameday" data-size="xs" name="sameday_discount_return_switch" {{$same_discount_return_switch}}/>
                                                              </span>
                                                        </div>
                                                        <input type="text"  class="form-control dec-percent sameday-discount-inp"  data-rule-required="true" data-msg-required="This field is required" name="sameday_discount_return_rate" value="{{$same_discount_return_sw}}" {{$same_discount_return_disable}}>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            @php
                                                $same_discount_packaging_sw = '';
                                                $same_discount_packaging_switch = '';
                                                $same_discount_packaging_disable = '';
                                            if((isset($discountCharges[4][0]) && $discountCharges[4][0]->packaging != '')){
                                            $same_discount_packaging_sw = $discountCharges[4][0]->packaging;
                                            $same_discount_packaging_switch = 'checked';
                                            $same_discount_packaging_disable = '';

                                             }else{
                                            $same_discount_packaging_sw = '';
                                            $same_discount_packaging_switch = '';
                                            $same_discount_packaging_disable = 'disabled';
                                            }
                                            @endphp
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Packaging</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox"  class="switchery discountSwitchesSameday" data-size="xs" name="sameday_discount_packaging_switch" {{$same_discount_packaging_switch}}/>
                                                              </span>
                                                        </div>
                                                        <input type="text"  class="form-control dec-percent sameday-discount-inp"  data-rule-required="true" data-msg-required="This field is required" name="sameday_discount_packaging_rate" value="{{$same_discount_packaging_sw}}" {{$same_discount_packaging_disable}}>
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-2">
                                <input type="hidden" name="authorize" id="authorize">
                                <input type="hidden" name="approve" id="approve">
                                <div class="form-group">

                                    <button id="addRatesSubmit" type="submit" class="btn btn-outline-success round btn-min-width mr-1 mb-1">Update Rates</button>

                                    @if ($shipper->status == 1 && (session('role_id') == 1 || in_array(8, session('permissions'))))
                                        <button id="accountActiveSubmit" type="submit" class="btn btn-outline-primary round btn-min-width mr-1 mb-1">Authorize</button>
                                    @endif

                                    @if ($shipper->rate_status ==1 && $shipper->status == 3 && (session('role_id') == 1 || in_array(140, session('permissions'))))
                                        <button id="accountApproveActiveSubmit" type="submit" class="btn btn-outline-primary round btn-min-width mr-1 mb-1">Approve</button>
                                    @endif
                                </div>

                            </div>


                        </form>
                        <div style="text-align:center;">
                        @if (($shipper->rate_status ==0 && $shipper->status == 1 && (session('role_id') == 1 || in_array(8, session('permissions'))))|| ($shipper->rate_status ==1 && (session('role_id') == 1 || in_array(140, session('permissions')))))
                            <button id="accountRejectActiveSubmit" type="button" class="btn btn-outline-danger round btn-min-width mr-1 mb-1">Reject Rates</button>
                        @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <div class="modal fade text-left" id="RejectRatesModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="RejectRatesModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Write a reason to reject rates!</h4>
                </div>
                <div class="modal-body">
                    <textarea id="reject_reason" onkeyup="textAreaAdjust(this)" style="width:100%;overflow:hidden"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-danger" id="RejectRatesSubmit">Yes</button>
                </div>
            </div>
        </div>
    </div>
            @else
            <h1>Standard rates not set.</h1>
            @endif
    @else
        <h1>Shipper does not exist.</h1>
    @endif
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <style type="text/css">
        .hide{
            display:none;
        }
    </style>


@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        function textAreaAdjust(o) {
            o.style.height = "1px";
            o.style.height = (25+o.scrollHeight)+"px";
        }
        $(document).ready(function () {

                $("#on_default").on('change', function () {
                    if ($("#ol_default").is(":checked")) {
                        $("#ol_default").trigger('click');
                    }
                    if ($("#det_default").is(":checked")) {
                        $("#det_default").trigger('click');
                    }
                    if ($("#sameday_default").is(":checked")) {
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

            $(".touchspin-color").trigger("touchspin.updatesettings", {min: 0.5,step: 0.5, decimals: 2});

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
        $('#accountRejectActiveSubmit').click(function() {
            $('#RejectRatesModal').modal('show');
        });
        $('#RejectRatesSubmit').on('click',function () {
            var shipper = $('#shipper_id').val();
            var reject_reason = document.getElementById('reject_reason').value;
            if(reject_reason){
                $.ajax({
                    url: '{!! route('admin.accounts.rejectreason.submit') !!}',
                    method: 'POST',
                    data: {
                        'rejected_reason': reject_reason,
                        'shipper_id':shipper,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function(data) {
                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        $('#RejectRatesModal').modal('hide');
                        window.setTimeout(function () {window.location.reload()}, 3000);
                    });
            }else{
                var error = "You have not selected any reason!";
                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
            }
        });
        $('#accountActiveSubmit').on('click',function(){
            $('#authorize').val(1);
        });
        $('#accountApproveActiveSubmit').on('click',function(){
            $('#approve').val(1);
            // console.log('ddd');
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
        $('.return-amount').inputmask({
            'alias': 'integer',
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            'min': 0,
            'max': 1000000
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


        //Inputmask({ regex: "\\d{1,9}(\\.\\d{1,2})?%?$" }).mask('.decpercent');
        $(".daterange").daterangepicker();

        //Overnight
        // var clickCheckbox = Array.prototype.slice.call(document.querySelector('.switchery.weightAdditionOvernight'));
        //var clickCheckbox = document.querySelector('.switchery.weightAdditionOvernight');
        var cashhandlingswitch = document.querySelector('.switchery.cashChargesOvernight');
        var insuranceChargesSwitch = document.querySelector('.switchery.insuranceChargesOvernight');
        var returnChargesSwitch = document.querySelector('.switchery.returnChargesOvernight');
        var fuelChargesSwitch = document.querySelector('.switchery.fuelSurchargeOvernight');
        var packagingChargesSwitch = document.querySelector('.switchery.packagingChargesSwitch');

        $('.weightAdditionOvernight').on('change',function(){
            var wid = $(this).attr('id');
            var wswitch = document.querySelector('#'+wid);
            if (wswitch.checked === true) {

                $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);

            } else if (wswitch.checked === false) {
                $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

            }
        });


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
        var count = $('.on_weight_row').length;
        $('body').on('click','#waddition_btn',function () {

            var htmdiv = '<div class="row on_weight_row" id="on_weight_row'+count+'"><input type="hidden" name="on_weight_record['+count+']" value=""><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="on_wa_range_up['+count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="on_wa_range_down['+count+']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionOvernight'+count+'" data-color="success" data-size="sm" name="on_wa_switch['+count+']"/></div></div><div class="col-2 text-center"><fieldset style="padding-top: 5px;"><div class="input-group input-group-sm form-group"><input type="text" class="touchspin-color input-sm spkg" value="0.5" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="on_wa_spkg['+count+']"></div></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required" value="" name="on_wa_local_charges['+count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required"  name="on_class_0_charges['+count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="on_class_1_charges['+count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="on_class_2_charges['+count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="on_class_3_charges['+count+']"></fieldset></div><div class="col-1">\n' +
                '<span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 on_weight_close"><i class="ft-x"></i></span></div></div>';
            $('.weight-addition-overnight').append(htmdiv);
            var switches = document.querySelector('.switchery.weightAdditionOvernight'+count);
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

            switches.onchange = function () {

                if (switches.checked === true) {
                    // $(this).next('.spkg').attr('disabled','');
                    // $(this).closest('div.col-md-2').find('input.spkg').attr('disabled','');

                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);
                } else if (switches.checked === false) {
                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

                }

            };
            $("#on_weight_row"+count+" .validated").each(function(){
                $( this ).rules( "add", {
                    required: true,
                });

            });
            count++;
        });
        // var switchery = new Switchery('.switchery.weightAddition'+count, { color: '#37BC9B' });


        //addMoreSlabs
        var on_slab_count = $('.row.on_cash_handling_row').length;
        $('body').on('click','#addMoreSlabs',function () {
            let htmdiv = '<div class="row" id="on_insurance_handle_'+on_slab_count+'"><input type="hidden" name="on_cash_record['+on_slab_count+']" value="">\n' +
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
        var on_ins_count = $('.row.on_insurance_row').length;
        $('body').on('click','#addMoreSlabsInsurance',function () {
            let htmdiv = '<div class="row" id="on_insurance_charge_'+on_ins_count+'"><input type="hidden" name="on_insurance_record['+on_ins_count+']" value="">\n' +
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
        // Packaging Charges Overnight
        packagingChargesSwitch.onchange = function () {
            if(packagingChargesSwitch.checked === true){
                $('#packaging_material_charges_div').slideDown('slow');
                $('.packaging-charges-div-overnight').find('input').prop('disabled',false);
            }else if(packagingChargesSwitch.checked === false){
                $('#packaging_material_charges_div').slideUp('slow');
                $('.packaging-charges-div-overnight').find('input').prop('disabled',true);

            }
        };

        @if(count($packaging_material_types) > 0)


        @foreach($packaging_material_types as $index => $type)
            var PackageSwitch = [];
            var type_id_{{$index}} = '{{$type->id}}';
            var type_id = '{{$type->id}}';
            PackageSwitch[type_id] = document.querySelector('.packaging_type_'+type_id);
            PackageSwitch[type_id].onchange = function () {

                if ($(this).is(':checked') === true) {
                    $('#package_type_'+type_id_{{$index}}).slideDown('slow');

                } else if ($(this).is(':checked') === false) {
                    $('#package_type_'+type_id_{{$index}}).slideUp('slow');

                }
            };

        @endforeach

        @endif



        //Overland
        //var weightAdditionOverland = document.querySelector('.switchery.weightAdditionOverland0');
        var cashhandlingswitchOverland = document.querySelector('.switchery.cashChargesOverland');
        var insuranceChargesSwitchOverland = document.querySelector('.switchery.insuranceChargesoverland');
        var returnChargesSwitchOverland = document.querySelector('.switchery.returnChargesOverland');

        var fuelChargesSwitchOL = document.querySelector('.switchery.fuelSurchargeOverland');

        $('.weightAdditionOverland').on('change',function() {
            var wid = $(this).attr('id');

            var wswitch = document.querySelector('#' + wid);
            if (wswitch.checked === true) {

                $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);

            } else if (wswitch.checked === false) {
                $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

            }
        });



        var overland_count = $('.ol_weight_row').length;
        $('body').on('click','#overland_weightadd',function () {

            let htmdiv1 = '<div class="row ol_weight_row" id="ol_weight_row'+overland_count+'"><input type="hidden" name="ol_weight_record['+overland_count+']" value=""><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_wa_range_up['+overland_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_wa_range_down['+overland_count+']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionOverland'+overland_count+'" data-color="success" data-size="sm" name="ol_wa_switch['+overland_count+']"/></div></div><div class="col-2 text-center"><fieldset style="padding-top: 5px;"><div class="input-group input-group-sm form-group"><input type="text" class="touchspin-color input-sm spkg" value="0.5" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="ol_wa_spkg['+overland_count+']"></div></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_wa_local_charges['+overland_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required"  name="ol_class_0_charges['+overland_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="ol_class_1_charges['+overland_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="ol_class_2_charges['+overland_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="ol_class_3_charges['+overland_count+']"></fieldset></div><div class="col-1">\n' +
                '<span class="btn btn-danger rounded btn-sm-width mr-1 mb-1 ol_weight_close"><i class="ft-x"></i></span></div></div>';
            $('.weight-addition-overland').append(htmdiv1);
            var ol_weight_switches = document.querySelector('.switchery.weightAdditionOverland'+overland_count);
            var switchery = new Switchery(ol_weight_switches, { disabled: false,color: '#37BC9B',size:'small' });
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
            ol_weight_switches.onchange = function () {

                if (ol_weight_switches.checked === true) {
                    // $(this).next('.spkg').attr('disabled','');
                    // $(this).closest('div.col-md-2').find('input.spkg').attr('disabled','');

                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);
                } else if (ol_weight_switches.checked === false) {
                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

                }

            };
            $("#ol_weight_row"+overland_count+" .validated").each(function(){
                $( this ).rules( "add", {
                    required: true,
                });

            });
            overland_count++;
        });

        //addMoreSlabs
        $('body').on('click','.ol_weight_close',function () {
            $(this).parent().parent().remove();
        });
        $('body').on('click','.ol_row_delete',function () {
            $(this).parent().parent().remove();
        });
        var ol_slab_count = $('.row.ol_cash_handling_row').length;
        $('body').on('click','#overlandaddMoreSlabs',function () {
            let htmdiv = '<div class="row ol_cash_handling_row" id="ol_cash_handle_'+ol_slab_count+'"><input type="hidden" name="ol_cash_record['+ol_slab_count+']" value="">\n' +
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
        var ol_ins_count = $('.row.ol_insurance_row').length;
        $('body').on('click','#oladdMoreSlabsInsurance',function () {
            let htmdiv = '<div class="row ol_insurance_row" id="ol_insurance_charge_'+ol_ins_count+'"><input type="hidden" name="ol_insurance_record['+ol_ins_count+']" value="">\n' +
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

        $('.weightAdditionDetain').on('change',function() {
            var wid = $(this).attr('id');
            console.log(wid);
            var wswitch = document.querySelector('#' + wid);
            if (wswitch.checked === true) {

                $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);

            } else if (wswitch.checked === false) {
                $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

            }
        });



        var detain_count = $('.det_weight_row').length;
        $('body').on('click','#detain_weightadd',function () {

            let htmdiv1 = '<div class="row det_weight_row" id="detain_weight_row'+detain_count+'"><input type="hidden" name="detain_weight_record['+detain_count+']" value=""><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_wa_range_up['+detain_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_wa_range_down['+detain_count+']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionDetain'+detain_count+'" data-color="success" data-size="sm" name="detain_wa_switch['+detain_count+']"/></div></div><div class="col-2 text-center"><fieldset style="padding-top: 5px;"><div class="input-group input-group-sm form-group"><input type="text" class="touchspin-color input-sm spkg" value="0.5" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="detain_wa_spkg['+detain_count+']"></div></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_wa_local_charges['+detain_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required"  name="detain_class_0_charges['+detain_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="detain_class_1_charges['+detain_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="detain_class_2_charges['+detain_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="detain_class_3_charges['+detain_count+']"></fieldset></div><div class="col-1">\n' +
                '<span id="detain_weight_close" class="btn btn-danger rounded btn-sm-width mr-1 mb-1"><i class="ft-x"></i></span></div></div>';
            $('.weight-addition-detain').append(htmdiv1);
            var detain_weight_switches = document.querySelector('.switchery.weightAdditionDetain'+detain_count);
            var switchery = new Switchery(detain_weight_switches, { disabled: false,color: '#37BC9B',size:'small' });
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
            detain_weight_switches.onchange = function () {

                if (detain_weight_switches.checked === true) {
                    // $(this).next('.spkg').attr('disabled','');
                    // $(this).closest('div.col-md-2').find('input.spkg').attr('disabled','');

                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);
                } else if (detain_weight_switches.checked === false) {
                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

                }

            };
            $("#detain_weight_row"+detain_count+" .validated").each(function(){
                $( this ).rules( "add", {
                    required: true,
                });

            });
            detain_count++;
        });

        //addMoreSlabs
        $('body').on('click','#detain_weight_close',function () {
            $(this).parent().parent().remove();
        });
        $('body').on('click','.detain_row_delete',function () {
            $(this).parent().parent().remove();
        });
        var detain_slab_count = $('.row.det_cash_handling_row').length;
        $('body').on('click','#detainaddMoreSlabs',function () {
            let htmdiv = '<div class="row det_cash_handling_row" id="detain_cash_handle_'+detain_slab_count+'"><input type="hidden" name="detain_cash_record['+detain_slab_count+']" value="">\n' +
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
        var detain_ins_count = $('.row.det_insurance_row').length;
        $('body').on('click','#detainaddMoreSlabsInsurance',function () {
            let htmdiv = '<div class="row det_insurance_row" id="detain_insurance_charge_'+detain_ins_count+'"><input type="hidden" name="detain_insurance_record['+detain_ins_count+']" value="">\n' +
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

        $('.weightAdditionSameday').on('change',function() {
            var wid = $(this).attr('id');

            var wswitch = document.querySelector('#' + wid);
            if (wswitch.checked === true) {

                $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);

            } else if (wswitch.checked === false) {
                $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

            }
        });
        // weightAdditionSameday.onchange = function () {
        //     if (weightAdditionSameday.checked === true) {
        //         // $(this).next('.spkg').attr('disabled','');
        //         // $(this).closest('div.col-md-2').find('input.spkg').attr('disabled','');
        //         $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);
        //
        //     } else if (weightAdditionSameday.checked === false) {
        //         $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);
        //
        //     }
        // };
        //detain


        var sameday_count = $('.same_weight_row').length;
        $('body').on('click','#sameday_weightadd',function () {
            let htmdiv1 = '<div class="row same_weight_row" id="sameday_weight_row'+sameday_count+'"><input type="hidden" name="same_weight_record['+sameday_count+']" value=""><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_wa_range_up['+sameday_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_wa_range_down['+sameday_count+']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionDetain'+sameday_count+'" data-color="success" data-size="sm" name="sameday_wa_switch['+sameday_count+']"/></div></div><div class="col-2 text-center"><fieldset style="padding-top: 5px;"><div class="input-group input-group-sm form-group"><input type="text" class="touchspin-color input-sm spkg" value="0.5" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="sameday_wa_spkg['+sameday_count+']"></div></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_wa_local_charges['+sameday_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required"  name="sameday_class_0_charges['+sameday_count+']"></fieldset></div><div class="col-1">\n' +
                '<span id="" class="btn btn-danger rounded btn-sm-width mr-1 mb-1 sameday_weight_close"><i class="ft-x"></i></span></div></div>';
            $('.weight-addition-sameday').append(htmdiv1);
            var sameday_weight_switches = document.querySelector('.switchery.weightAdditionDetain'+sameday_count);
            var switchery = new Switchery(sameday_weight_switches, { disabled: false,color: '#37BC9B',size:'small' });
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
            sameday_weight_switches.onchange = function () {

                if (sameday_weight_switches.checked === true) {
                    // $(this).next('.spkg').attr('disabled','');
                    // $(this).closest('div.col-md-2').find('input.spkg').attr('disabled','');

                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);
                } else if (sameday_weight_switches.checked === false) {
                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

                }

            };
            $("#sameday_weight_row"+sameday_count+" .validated").each(function(){
                $( this ).rules( "add", {
                    required: true,
                });

            });
            sameday_count++;
        });

        //addMoreSlabs
        $('body').on('click','.sameday_weight_close',function () {
            $(this).parent().parent().remove();
        });
        $('body').on('click','.sameday_row_delete',function () {
            $(this).parent().parent().remove();
        });
        var sameday_slab_count = $('.row.same_cash_handling_row').length;
        $('body').on('click','#samedayaddMoreSlabs',function () {
            let htmdiv = '<div class="row same_cash_handling_row" id="sameday_cash_handle_'+sameday_slab_count+'"><input type="hidden" name="sameday_cash_record['+sameday_slab_count+']" value="">\n' +
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
        var sameday_ins_count = $('.row.same_insurance_row').length;
        $('body').on('click','#samedayaddMoreSlabsInsurance',function () {
            let htmdiv = '<div class="row same_insurance_row" id="sameday_insurance_charge_'+sameday_ins_count+'"><input type="hidden" name="sameday_insurance_record['+sameday_ins_count+']" value="">\n' +
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
        ondiscountSwitch[4].onchange = function () {
            ONdiscount(ondiscountSwitch[4]);
        };
        // $.each(ondiscountSwitch,function () {
        //     console.log('heeee');
        // });
        function ONdiscount(eve) {
            if(eve.checked === true){

                $(eve).parent().parent().next().prop('disabled',false);
                $('input[name="on_discount_title"]').prop('disabled',false);
                $('input[name="on_daterange"]').prop('disabled',false);

            }else if(eve.checked === false){
                $(eve).parent().parent().next().prop('disabled',true);

                if(ondiscountSwitch[0].checked === true || ondiscountSwitch[1].checked === true || ondiscountSwitch[2].checked === true || ondiscountSwitch[3].checked === true || ondiscountSwitch[4].checked === true){
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
        overlandDiscountSwitch[4].onchange = function () {
            OLdiscount(overlandDiscountSwitch[4]);
        };
        function OLdiscount(eveOver) {
            if(eveOver.checked === true){

                $(eveOver).parent().parent().next().prop('disabled',false);
                $('input[name="ol_discount_title"]').prop('disabled',false);
                $('input[name="ol_daterange"]').prop('disabled',false);

            }else if(eveOver.checked === false){
                $(eveOver).parent().parent().next().prop('disabled',true);

                if(overlandDiscountSwitch[0].checked === true || overlandDiscountSwitch[1].checked === true || overlandDiscountSwitch[2].checked === true || overlandDiscountSwitch[3].checked === true || overlandDiscountSwitch[4].checked === true){
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
        detainDiscountSwitch[4].onchange = function () {
            Detaindiscount(detainDiscountSwitch[4]);
        };
        function Detaindiscount(eveDet) {
            if(eveDet.checked === true){

                $(eveDet).parent().parent().next().prop('disabled',false);
                $('input[name="detain_discount_title"]').prop('disabled',false);
                $('input[name="detain_daterange"]').prop('disabled',false);

            }else if(eveDet.checked === false){
                $(eveDet).parent().parent().next().prop('disabled',true);

                if(detainDiscountSwitch[0].checked === true || detainDiscountSwitch[1].checked === true || detainDiscountSwitch[2].checked === true || detainDiscountSwitch[3].checked === true || detainDiscountSwitch[4].checked === true){
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
        samedayDiscountSwitch[4].onchange = function () {
            SamedayDiscount(samedayDiscountSwitch[4]);
        };
        function SamedayDiscount(eveSameday) {
            if(eveSameday.checked === true){

                $(eveSameday).parent().parent().next().prop('disabled',false);
                $('input[name="sameday_discount_title"]').prop('disabled',false);
                $('input[name="sameday_daterange"]').prop('disabled',false);

            }else if(eveSameday.checked === false){
                $(eveSameday).parent().parent().next().prop('disabled',true);

                if(samedayDiscountSwitch[0].checked === true || samedayDiscountSwitch[1].checked === true || samedayDiscountSwitch[2].checked === true || samedayDiscountSwitch[3].checked === true || samedayDiscountSwitch[4].checked === true){
                    $('input[name="sameday_discount_title"]').prop('disabled',false);
                    $('input[name="sameday_daterange"]').prop('disabled',false);
                }else{
                    $('input[name="sameday_discount_title"]').prop('disabled',true);
                    $('input[name="sameday_daterange"]').prop('disabled',true);
                }

            }
        }

        $('#ratesAdditionForm').on('keypress',function (e) {
            if(e.which == 13 || e.keyCode == 13) {
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
                    var msg = "";
                    if($('#authorize').val() == 1){
                        msg = "Rates are being authorized!"
                    }else{
                        msg = 'Rates are being added!';
                    }


                    swal({
                        title: 'Please Wait!',
                        text: msg,
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