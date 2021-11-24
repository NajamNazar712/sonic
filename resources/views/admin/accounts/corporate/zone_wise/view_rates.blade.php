@extends('admin.layout.master')

@section('title', 'View Rates')

@section('content')
    @if(!empty($shipper))

        @if(count($switches) > 0)
            <h1>View Rates</h1>

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
                                    @method('PUT')

                                    <div class="card">

                                     {{--   <div class="">
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
                                                        <div class="col-6"><a href="javascript:void(0);" class="pull-right" id="packaging_main_switch"><input type="checkbox" name="packaging_switch" class="switchery pull-right packagingChargesSwitch" data-color="info" data-size="sm" {{$packaging_switch}} disabled/></a></div>

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
                                                                <div class="col-6"><a href="javascript:void(0);" class="pull-right"><a href="javascript:void(0);" class="pull-right" id="packaging_type_{{$type->id}}"><input name="packaging_type_{{$type->id}}" type="checkbox"  class="switchery packaging_type_{{$type->id}}" data-color="info" data-size="sm" {{$packaging_type_switch}} disabled/></a></a></div>

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
                                                                                        <input name="packaging_material_size[{{$size->id}}]" type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$packaging_charges[$type->id][$ind]->charges}}" disabled>
                                                                                    </fieldset>
                                                                                </div>
                                                                            @else
                                                                                <div class="col-md-3 text-center">
                                                                                    <label class="card-title">{{$size->size}}</label>
                                                                                    <fieldset class="form-group">
                                                                                        <input name="packaging_material_size[{{$size->id}}]" type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$size->standard_charges}}" disabled>
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

                                        </div>--}}

                                    </div>
                                    <div id="" class="card-header border-success">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h3 class="display-inline card-title lead success">Overnight</h3>
                                                @if($sale_person['admin_id'] == Auth::id() || session('role_id') == 1)
                                                    <label class="display-inline ml-1"> Default</label>
                                                    @if($shipper['default_shipping_mode'] == 1)
                                                        <input type="checkbox" name="on_default" id="on_default" class="switchery on_default" checked data-size="xs" data-switchery="true" disabled>
                                                    @else
                                                        <input type="checkbox" name="on_default" id="on_default" class="switchery on_default" data-size="xs" data-switchery="true" disabled>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <a href="javascript:void(0);" class="pull-right" id="on_main_switch"><input name="on_main_switch" type="checkbox"  class="switchery on-main-switch" data-size="sm" {{ ((isset($switches[1][0]) && $switches[1][0]->status == 1) ? 'checked' : '') }} disabled/></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="overnight" class="card border-success {{ ((isset($switches[1][0]) && $switches[1][0]->status == 1) ? '' : 'hide') }}" aria-expanded="true">
                                        <input type="hidden" name="on_rate_record" value="{{ ((isset($switches[1][0]) && $switches[1][0]->id != '') ? $switches[1][0]->id : '') }}">

                                        <div class="card-content">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-2">
                                                        <h3>Origin Cities</h3>
                                                    </div>
                                                    <div class="col-8">
                                                        <div class="form-group card border-success p-2">
                                                            <select name="on_origin_hubs[]" id="on_origin_hubs" class="form-control select2" multiple="multiple" disabled>
                                                                @foreach($cities as $city)
                                                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-2">
                                                        <h3>Destination Cities</h3>
                                                    </div>
                                                    <div class="col-8">
                                                        <div class="form-group card border-success p-2">
                                                            <select name="on_destination_hubs[]" id="on_destination_hubs" class="form-control select2" multiple="multiple" disabled>
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
                                                                @if(isset($min_weight[1]))
                                                                    @foreach($min_weight[1] as $mweight)
                                                                        @if($mweight->delivery_type_id == 1)
                                                                            <input type="hidden" name="overnight_door_min_chargeable_weight" value="{{$mweight->id}}">
                                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="{{$mweight->min_chargeable_weight}}" name="on_door_mcw_charges" placeholder="Minimum Chargeable Weight" disabled>
                                                                        @endif
                                                                    @endforeach
                                                                @else
                                                                    <input type="hidden" name="overnight_door_min_chargeable_weight" value="">
                                                                    <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="" name="on_door_mcw_charges" placeholder="Minimum Chargeable Weight" disabled>
                                                                @endif
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
                                                    @if(isset($weight[1]))
                                                        @php
                                                            $index_row = 0;
                                                        @endphp
                                                        @foreach($weight[1] as $index => $onweight)
                                                            @if($onweight->delivery_type_id == 1)

                                                                <div class="row on_door_weight_row" id="on_door_weight_row{{$index_row}}">
                                                                    <input type="hidden" name="on_door_weight_record[{{$index_row}}]" value="{{$onweight->id}}">
                                                                    <div class="col text-center">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->range_up}}" name="on_door_range_up[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col text-center">

                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->range_down}}" name="on_door_range_down[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col text-center">

                                                                        <div class="form-group " style="padding-top: 8px;">
                                                                            <input type="checkbox" class="switchery weightAdditionDoorOvernight" data-color="success" data-size="sm" {{ ($onweight->base == 1) ? 'checked' : '' }}  disabled/>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col text-center">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->local}}" disabled name="on_door_local_charges[{{$index_row}}]">
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->same_zone}}" disabled name="on_door_same_zone_charges[{{$index_row}}]">
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->different_zone}}" disabled name="on_door_different_zone_charges[{{$index_row}}]">
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col-1">
                                                                    </div>

                                                                </div>
                                                                @php
                                                                    $index_row++;
                                                                @endphp
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <div class="row on_door_weight_row" id="on_door_weight_row0">
                                                            <input type="hidden" name="on_door_weight_record[0]" value="">

                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="0.01" name="on_door_range_up[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="" name="on_door_range_down[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">

                                                                <div class="form-group " style="padding-top: 8px;">
                                                                    <input type="checkbox" class="switchery weightAdditionDoorOvernight" data-color="success" data-size="sm" name="on_wa_switch[0]" disabled/>
                                                                </div>
                                                            </div>

                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="" name="on_door_local_charges[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="on_door_same_zone_charges[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="on_door_different_zone_charges[]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-1">
                                                            </div>

                                                        </div>
                                                    @endif

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
                                                                @if(isset($min_weight[1]))
                                                                    @foreach($min_weight[1] as $mweight)
                                                                        @if($mweight->delivery_type_id == 2)
                                                                            <input type="hidden" name="overnight_hub_min_chargeable_weight" value="{{$mweight->id}}">
                                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="{{$mweight->min_chargeable_weight}}" name="on_hub_mcw_charges" placeholder="Minimum Chargeable Weight" disabled>
                                                                        @endif
                                                                    @endforeach
                                                                @else
                                                                    <input type="hidden" name="overnight_hub_min_chargeable_weight" value="">
                                                                    <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="" name="on_hub_mcw_charges" placeholder="Minimum Chargeable Weight" disabled>

                                                                @endif
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
                                                    @if(isset($weight[1]))
                                                        @php
                                                            $index_row = 0;
                                                        @endphp
                                                        @foreach($weight[1] as $index => $onweight)
                                                            @if($onweight->delivery_type_id == 2)
                                                                <div class="row on_hub_weight_row" id="on_hub_weight_row{{$index_row}}">
                                                                    <input type="hidden" name="on_hub_weight_record[{{$index_row}}]" value="{{$onweight->id}}">

                                                                    <div class="col text-center">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->range_up}}" name="on_hub_range_up[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col text-center">

                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->range_down}}" name="on_hub_range_down[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col text-center">

                                                                        <div class="form-group " style="padding-top: 8px;">
                                                                            <input type="checkbox" class="switchery weightAdditionHubOvernight" data-color="success" data-size="sm"  {{ ($onweight->base == 1) ? 'checked' : '' }}  disabled/>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col text-center">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->local}}" name="on_hub_local_charges[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->same_zone}}" name="on_hub_same_zone_charges[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->different_zone}}" name="on_hub_different_zone_charges[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col-1">

                                                                    </div>

                                                                </div>
                                                                @php
                                                                    $index_row++;
                                                                @endphp
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <div class="row" id="on_hub_weight_row0">
                                                            <input type="hidden" name="on_hub_weight_record[0]" value="">
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="0.01" name="on_hub_range_up[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">

                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="" name="on_hub_range_down[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">

                                                                <div class="form-group " style="padding-top: 8px;">
                                                                    <input type="checkbox" class="switchery weightAdditionHubOvernight" data-color="success" data-size="sm" name="on_wa_switch[0]" disabled/>
                                                                </div>
                                                            </div>

                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="" name="on_hub_local_charges[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="on_hub_same_zone_charges[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="on_hub_different_zone_charges[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-1">
                                                            </div>

                                                        </div>
                                                    @endif
                                                </div>{{--weight addition div--}}
                                                <div class="row mt-2">
                                                    <input type="hidden" name="on_booking_record" value="{{ (isset($shippingType[1][0]) && $shippingType[1][0]->id != '')? $shippingType[1][0]->id : ''}}">

                                                    <div class="col-md-3 text-center">
                                                        <fieldset>
                                                            <div class="input-group form-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" >Replacement</span>
                                                                </div>
                                                                <input type="text" class="form-control percent" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($shippingType[1][0]) && $shippingType[1][0]->replacement_charges != '')? $shippingType[1][0]->replacement_charges : ''}}" name="on_replacement_charges" disabled>
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
                                                                <input type="text" class="form-control percent" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($shippingType[1][0]) && $shippingType[1][0]->try_and_buy_charges != '')? $shippingType[1][0]->try_and_buy_charges : ''}}" name="on_tnb_charges" disabled>
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
                                                                        <label class="card-title mr-1">DWS Weight </label>
                                                                        <input type="checkbox" name="on_dws" id="on_dws" class="switchery on_dws" {{ (($on_dws_charges != null) ? 'checked' : '') }} data-size="xs" data-switchery="true" disabled>
                                                                    </div>
                                                                </fieldset>
                                                            </div> 
                                                            <div class="col-4">
                                                                <fieldset>
                                                                    <div class="input-group form-group">
                                                                        <select name="on_dws_weight" id="on_dws_weight" class="form-control" disabled>
                                                                            @if ($on_dws_charges != null)
                                                                            
                                                                                @if ($on_dws_charges == 1)
                                                                                <option value="1" selected>High</option>
                                                                                <option value="2">Low</option>
                                                                                @elseif ($on_dws_charges == 2)
                                                                                <option value="1" >High</option>
                                                                                <option value="2" selected>Low</option>
                                                                                @else
                                                                                <option value="1">High</option>
                                                                                <option value="2">Low</option>
                                                                                @endif
                                                                            @else
                                                                                <option value="1">High</option>
                                                                                <option value="2">Low</option>
                                                                             @endif
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
                                                            <input type="checkbox" name="on_cash_handling_switch"  class="switchery cashChargesOvernight" data-color="success" data-size="sm" {{$on_cash_switch}} disabled/>
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
                                                                        <input name="on_cash_range_up[{{$index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric"  value="{{$cash->range_up}}" {{$on_cash_sw}} disabled>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-2 text-center">
                                                                    <fieldset class="form-group">
                                                                        <input name="on_cash_range_down[{{$index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric"  value="{{$cash->range_down}}" {{$on_cash_sw}} disabled>
                                                                    </fieldset>
                                                                </div>

                                                                <div class="col-md-2 text-center">
                                                                    <fieldset class="form-group">
                                                                        <input name="on_cash_charges[{{$index}}]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->charges}}" {{$on_cash_sw}} disabled>
                                                                    </fieldset>
                                                                </div>
                                                                @if($index>0)
                                                                    <div class="col">

                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="row on_cash_handling_row">
                                                            <input type="hidden" name="on_cash_record[0]" value="">
                                                            <div class="col-md-2 text-center">
                                                                <fieldset class="form-group">
                                                                    <input name="on_cash_range_up[0]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric"  value="" {{$on_cash_sw}} disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-md-2 text-center">
                                                                <fieldset class="form-group">
                                                                    <input name="on_cash_range_down[0]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric"  value="" {{$on_cash_sw}} disabled>
                                                                </fieldset>
                                                            </div>

                                                            <div class="col-md-2 text-center">
                                                                <fieldset class="form-group">
                                                                    <input name="on_cash_charges[0]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" {{$on_cash_sw}} disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col"></div>

                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="cash-handling-btn-overnight">

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
                                                            <input type="checkbox" name="on_insurance_charges_switch" class="switchery insuranceChargesOvernight" data-color="success" data-size="sm" {{$on_insurance_switch}} disabled/>
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
                                                                        <input name="on_ins_range_up[{{$index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric"  value="{{$insurance->range_up}}" {{$on_ins_sw}} disabled>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-2 text-center">
                                                                    <fieldset class="form-group">
                                                                        <input name="on_ins_range_down[{{$index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric"  value="{{$insurance->range_down}}" {{$on_ins_sw}} disabled>
                                                                    </fieldset>
                                                                </div>

                                                                <div class="col-md-2 text-center">
                                                                    <fieldset class="form-group">
                                                                        <input name="on_ins_charges[{{$index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control dec-percent"  value="{{$insurance->charges}}" {{$on_ins_sw}} disabled>
                                                                    </fieldset>
                                                                </div>
                                                                @if($index>0)
                                                                    <div class="col">
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="row on_insurance_row" id="on_insurance_handle_0">
                                                            <input type="hidden" name="on_insurance_record[0]" value="">
                                                            <div class="col-md-2 text-center">
                                                                <fieldset class="form-group">
                                                                    <input name="on_ins_range_up[0]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric"  value="" {{$on_ins_sw}} disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-md-2 text-center">
                                                                <fieldset class="form-group">
                                                                    <input name="on_ins_range_down[0]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric"  value="" {{$on_ins_sw}} disabled>
                                                                </fieldset>
                                                            </div>

                                                            <div class="col-md-2 text-center">
                                                                <fieldset class="form-group">
                                                                    <input name="on_ins_charges[0]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control dec-percent"  value="" {{$on_ins_sw}} disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col"></div>
                                                        </div>
                                                    @endif
                                                </div>

                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <h3 class="card-title">Return Charges</h3>
                                                    </div>
                                                    @php
                                                        $on_return_sw = 'disabled';
                                                        $on_return_switch = '';
                                                    if((isset($switches[1][0]) && $switches[1][0]->return_charges == 1)){
                                                    $on_return_sw = 'disabled';
                                                    $on_return_switch = 'checked';
                                                     }else{
                                                    $on_return_sw = 'disabled';
                                                    $on_return_switch = '';
                                                    }
                                                    @endphp
                                                    <div class="col-md-2">
                                                        <div class="form-group ">
                                                            <input type="checkbox" name="on_return_switch" class="switchery returnChargesOvernight" data-color="success" data-size="sm" {{$on_return_switch}} disabled/>
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
                                                        <label class="card-title">Same Zone</label>
                                                        <fieldset class="form-group">
                                                            <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent" name="on_return_same_zone_charges"  value="{{ (isset($returnCharges[1][0]) && $returnCharges[1][0]->same_zone !== '')? $returnCharges[1][0]->same_zone : ''}}" {{$on_return_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <label class="card-title">Different Zone</label>
                                                        <fieldset class="form-group">
                                                            <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent" name="on_return_different_zone_charges"  value="{{ (isset($returnCharges[1][0]) && $returnCharges[1][0]->different_zone !== '')? $returnCharges[1][0]->different_zone : ''}}" {{$on_return_sw}}>
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
                                                            <input type="checkbox" name="overnight_fuel_switch" class="switchery fuelSurchargeOvernight" data-color="success" data-size="sm" {{$on_fuel_switch}} disabled/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row fuel-surcharge-div-overnight">
                                                    <input type="hidden" name="on_fuel_record" value="{{ (isset($fuelCharges[1][0]) && $fuelCharges[1][0]->id != '')? $fuelCharges[1][0]->id : ''}}">
                                                    <div class="col-md-2 text-center">
                                                        <label class="card-title">Charges</label>
                                                        <fieldset>
                                                            <div class="input-group form-group">
                                                                <input type="text"  class="form-control " name="overnight_fuel_surcharge" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($fuelCharges[1][0]) && $fuelCharges[1][0]->fuel_surcharge != '')? $fuelCharges[1][0]->fuel_surcharge : ''}}" {{$on_fuel_sw}} disabled>
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
                                                            if((isset($discountCharges[1][0]->cash)) || (isset($discountCharges[1][0]->weight)) || (isset($discountCharges[1][0]->insurance)) || (isset($discountCharges[1][0]->return))){
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
                                                            <input type='text' class="form-control" data-rule-required="true" data-msg-required="This field is required" {{$on_discount_title_switch}} name="on_discount_title" value="{{$on_discount_title}}" disabled/>
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
                                                            <input type='text' class="form-control daterange" data-rule-required="true" data-msg-required="This field is required" {{$on_discount_daterange_switch}} name="on_daterange" value="{{$on_discount_daterange}}" disabled/>
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
                                                                <input type="checkbox" id="" class="switchery discountSwitchesOvernight" name="on_discount_weight_switch" data-size="xs" {{$on_discount_weight_switch}}/ disabled>
                                                              </span>
                                                                </div>
                                                                <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent on-discount-inp" name="on_discount_weight_rate" value="{{$on_discount_weight_sw}}" {{$on_discount_weight_disable}} disabled>
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
                                                                <input type="checkbox" name="on_discount_cash_switch" class="switchery discountSwitchesOvernight" data-size="xs" {{$on_discount_cash_switch}} disabled/>
                                                              </span>
                                                                </div>
                                                                <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent on-discount-inp" name="on_discount_cash_rate" value="{{$on_discount_cash_sw}}" {{$on_discount_cash_disable}} disabled>
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
                                                                <input type="checkbox" name="on_discount_insurance_switch" class="switchery discountSwitchesOvernight" data-size="xs" {{$on_discount_insurance_switch}} disabled/>
                                                              </span>
                                                                </div>
                                                                <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent on-discount-inp" name="on_discount_insurance_rate" value="{{$on_discount_insurance_sw}}" {{$on_discount_insurance_disable}} disabled>
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
                                                                <input type="checkbox"  class="switchery discountSwitchesOvernight" data-size="xs" name="on_discount_return_switch" {{$on_discount_return_switch}} disabled/>
                                                              </span>
                                                                </div>
                                                                <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent on-discount-inp" name="on_discount_return_rate" value="{{$on_discount_return_sw}}" {{$on_discount_return_disable}} disabled>
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
                                                    <label class="display-inline ml-1"> Default</label>
                                                    @if($shipper['default_shipping_mode'] == 2)
                                                        <input type="checkbox" name="ol_default" id="ol_default" class="switchery ol_default" checked data-size="xs" data-switchery="true" disabled>
                                                    @else
                                                        <input type="checkbox" name="ol_default" id="ol_default" class="switchery ol_default" data-size="xs" data-switchery="true" disabled>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <a id="ol_main_switch" href="javascript:void(0);" class="pull-right"><input name="ol_main_switch" type="checkbox" id="" class="switchery ol-main-switch" data-size="sm" {{ ((isset($switches[2][0]) && $switches[2][0]->status == 1) ? 'checked' : '') }} disabled/></a>
                                            </div>
                                        </div>

                                    </div>
                                    <div id="overland" class="border-success no-border-top card {{ ((isset($switches[2][0]) && $switches[2][0]->status == 1) ? '' : 'hide') }}" aria-expanded="false">
                                        <input type="hidden" name="ol_rate_record" value="{{ ((isset($switches[2][0]) && $switches[2][0]->id != '') ? $switches[2][0]->id : '') }}">

                                        <div class="card-content">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-2">
                                                        <h3>Origin Cities</h3>
                                                    </div>
                                                    <div class="col-8">
                                                        <div class="form-group card border-success p-2">
                                                            <select name="ol_origin_hubs[]" id="ol_origin_hubs" class="form-control select2" multiple="multiple" disabled>
                                                                @foreach($cities as $city)
                                                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-2">
                                                        <h3>Destination Cities</h3>
                                                    </div>
                                                    <div class="col-8">
                                                        <div class="form-group card border-success p-2">
                                                            <select name="ol_destination_hubs[]" id="ol_destination_hubs" class="form-control select2" multiple="multiple" disabled>
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
                                                                @if(isset($min_weight[2]))
                                                                    @foreach($min_weight[2] as $mweight)
                                                                        @if($mweight->delivery_type_id == 1)
                                                                            <input type="hidden" name="overland_door_min_chargeable_weight" value="{{$mweight->id}}">
                                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="{{$mweight->min_chargeable_weight}}" name="ol_door_mcw_charges" placeholder="Minimum Chargeable Weight" disabled>
                                                                        @endif
                                                                    @endforeach
                                                                @else
                                                                    <input type="hidden" name="overland_door_min_chargeable_weight" value="">
                                                                    <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="" name="ol_door_mcw_charges" placeholder="Minimum Chargeable Weight" disabled>

                                                                @endif
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
                                                    @if(isset($weight[2]))
                                                        @php
                                                            $index_row = 0;
                                                        @endphp
                                                        @foreach($weight[2] as $index => $olweight)
                                                            @if($olweight->delivery_type_id == 1)
                                                                <div class="row ol_door_weight_row" id="ol_door_weight_row{{$index_row}}">
                                                                    <input type="hidden" name="ol_door_weight_record[{{$index_row}}]" value="{{$olweight->id}}">
                                                                    <div class="col text-center">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->range_up}}" name="ol_door_range_up[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col text-center">

                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->range_down}}" name="ol_door_range_down[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col text-center">

                                                                        <div class="form-group " style="padding-top: 8px;">
                                                                            <input type="checkbox" class="switchery weightAdditionDoorOverland" data-color="success" data-size="sm" {{ ($olweight->base == 1) ? 'checked' : '' }}  disabled/>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col text-center">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->local}}" name="ol_door_local_charges[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->same_zone}}" name="ol_door_same_zone_charges[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div> <div class="col text-center">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->different_zone}}" name="ol_door_different_zone_charges[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col-1">

                                                                    </div>
                                                                </div>{{--Row--}}
                                                                @php
                                                                    $index_row++;
                                                                @endphp
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <div class="row ol_door_weight_row" id="ol_door_weight_row0">
                                                            <input type="hidden" name="ol_door_weight_record[0]" value="">
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="0.01" name="ol_door_range_up[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">

                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_door_range_down[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">

                                                                <div class="form-group " style="padding-top: 8px;">
                                                                    <input type="checkbox" class="switchery weightAdditionDoorOverland" data-color="success" data-size="sm" name="on_wa_switch[0]" disabled/>
                                                                </div>
                                                            </div>

                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_door_local_charges[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_door_same_zone_charges[0]">
                                                                </fieldset>
                                                            </div> <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_door_different_zone_charges[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-1">
                                                            </div>
                                                        </div>{{--Row--}}
                                                    @endif
                                                </div>{{--weight addition div--}}
                                                <div class="mb-2">

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
                                                                @if(isset($min_weight[2]))
                                                                    @foreach($min_weight[2] as $mweight)
                                                                        @if($mweight->delivery_type_id == 2)
                                                                            <input type="hidden" name="overland_hub_min_chargeable_weight" value="{{$mweight->id}}">
                                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="{{$mweight->min_chargeable_weight}}" name="ol_hub_mcw_charges" placeholder="Minimum Chargeable Weight" disabled>
                                                                        @endif
                                                                    @endforeach
                                                                @else
                                                                    <input type="hidden" name="overland_hub_min_chargeable_weight" value="">
                                                                    <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="" name="ol_hub_mcw_charges" placeholder="Minimum Chargeable Weight" disabled>

                                                                @endif
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
                                                    @if(isset($weight[2]))
                                                        @php
                                                            $index_row = 0;
                                                        @endphp
                                                        @foreach($weight[2] as $index => $olweight)
                                                            @if($olweight->delivery_type_id == 2)
                                                                <div class="row ol_hub_weight_row" id="ol_hub_weight_row{{$index_row}}">
                                                                    <input type="hidden" name="ol_hub_weight_record[{{$index_row}}]" value="{{$olweight->id}}">
                                                                    <div class="col text-center">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->range_up}}" name="ol_hub_range_up[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col text-center">

                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->range_down}}" name="ol_hub_range_down[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col text-center">

                                                                        <div class="form-group " style="padding-top: 8px;">
                                                                            <input type="checkbox" class="switchery weightAdditionHubOverland" data-color="success" data-size="sm" {{ ($olweight->base == 1) ? 'checked' : '' }}  disabled/>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col text-center">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->local}}" name="ol_hub_local_charges[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->same_zone}}" name="ol_hub_same_zone_charges[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div> <div class="col text-center">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->different_zone}}" name="ol_hub_different_zone_charges[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col-1">

                                                                    </div>
                                                                </div>{{--Row--}}
                                                                @php
                                                                    $index_row++;
                                                                @endphp
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <div class="row ol_hub_weight_row" id="ol_hub_weight_row0">
                                                            <input type="hidden" name="ol_hub_weight_record[0]" value="">
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="0.01" name="ol_hub_range_up[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">

                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_hub_range_down[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">

                                                                <div class="form-group " style="padding-top: 8px;">
                                                                    <input type="checkbox" class="switchery weightAdditionHubOverland" data-color="success" data-size="sm" name="on_wa_switch[0]" disabled/>
                                                                </div>
                                                            </div>

                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_hub_local_charges[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_hub_same_zone_charges[0]" disabled>
                                                                </fieldset>
                                                            </div> <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_hub_different_zone_charges[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-1">
                                                            </div>
                                                        </div>{{--Row--}}
                                                    @endif
                                                </div>{{--weight addition div--}}
                                                <div class="row mt-2">
                                                    <input type="hidden" name="ol_booking_record" value="{{ (isset($shippingType[2][0]) && $shippingType[2][0]->id != '')? $shippingType[2][0]->id : ''}}">

                                                    <div class="col-md-3 text-center">
                                                        <fieldset>
                                                            <div class="input-group form-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">Replacement</span>
                                                                </div>
                                                                <input type="text" class="form-control percent" data-rule-required="true" data-msg-required="This field is required" name="ol_replacement_charges" value="{{ (isset($shippingType[2][0]) && $shippingType[2][0]->replacement_charges != '')? $shippingType[2][0]->replacement_charges : ''}}" disabled>
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
                                                                <input type="text" class="form-control percent" data-rule-required="true" data-msg-required="This field is required" name="ol_tnb_charges" value="{{ (isset($shippingType[2][0]) && $shippingType[2][0]->try_and_buy_charges != '')? $shippingType[2][0]->try_and_buy_charges : ''}}" disabled>
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
                                                                        <label class="card-title mr-1">DWS Weight </label>
                                                                        <input type="checkbox" name="ol_dws" id="ol_dws" class="switchery ol_dws" {{ (($ol_dws_charges != null) ? 'checked' : '') }} data-size="xs" data-switchery="true" disabled>
                                                                    </div>
                                                                </fieldset>
                                                            </div> 
                                                            <div class="col-4">
                                                                <fieldset>
                                                                    <div class="input-group form-group">
                                                                        <select name="ol_dws_weight" id="ol_dws_weight" class="form-control" disabled>
                                                                            @if ($ol_dws_charges != null)
                                                                           
                                                                                @if ($ol_dws_charges == 1)
                                                                                <option value="1" selected>High</option>
                                                                                <option value="2">Low</option>
                                                                                @elseif ($ol_dws_charges == 2)
                                                                                <option value="1" >High</option>
                                                                                <option value="2" selected>Low</option>
                                                                                @else
                                                                                <option value="1">High</option>
                                                                                <option value="2">Low</option>
                                                                                @endif
                                                                            @else
                                                                                <option value="1">High</option>
                                                                                <option value="2">Low</option>
                                                                            @endif
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
                                                            <input type="checkbox" name="ol_cash_handling_switch" class="switchery cashChargesOverland" data-color="success" data-size="sm" {{$ol_cash_switch}} disabled/>
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
                                                                        <input name="ol_cash_range_up[{{$index}}]" type="text" class="form-control  numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->range_up}}" {{$ol_cash_sw}} disabled>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-2 text-center">
                                                                    <fieldset class="form-group">
                                                                        <input name="ol_cash_range_down[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->range_down}}" {{$ol_cash_sw}} disabled>
                                                                    </fieldset>
                                                                </div>

                                                                <div class="col-md-2 text-center">
                                                                    <fieldset class="form-group">
                                                                        <input name="ol_cash_charges[{{$index}}]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->charges}}" {{$ol_cash_sw}} disabled>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col">
                                                                    @if($index>0)

                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="row ol_cash_handling_row" id="ol_cash_handle_0">
                                                            <input type="hidden" name="ol_cash_record[0]" value="">
                                                            <div class="col-md-2 text-center">
                                                                <fieldset class="form-group">
                                                                    <input name="ol_cash_range_up[0]" type="text" class="form-control  numeric" data-rule-required="true" data-msg-required="This field is required" value="" {{$ol_cash_sw}} disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-md-2 text-center">
                                                                <fieldset class="form-group">
                                                                    <input name="ol_cash_range_down[0]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="" {{$ol_cash_sw}} disabled>
                                                                </fieldset>
                                                            </div>

                                                            <div class="col-md-2 text-center">
                                                                <fieldset class="form-group">
                                                                    <input name="ol_cash_charges[0]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" {{$ol_cash_sw}} disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col"></div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="cash-handling-btn-overland">

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
                                                            <input type="checkbox" name="ol_insurance_charges_switch" class="switchery insuranceChargesoverland" data-color="success" data-size="sm" {{$ol_insurance_switch}} disabled/>
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
                                                                        <input name="ol_ins_range_up[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$ol_insurance->range_up}}" {{$ol_ins_sw}} disabled>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-2 text-center">
                                                                    <fieldset class="form-group">
                                                                        <input name="ol_ins_range_down[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$ol_insurance->range_down}}" {{$ol_ins_sw}} disabled>
                                                                    </fieldset>
                                                                </div>

                                                                <div class="col-md-2 text-center">
                                                                    <fieldset class="form-group">
                                                                        <input name="ol_ins_charges[{{$index}}]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$ol_insurance->charges}}" {{$ol_ins_sw}} disabled>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col">

                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="row ol_insurance_row">
                                                            <input type="hidden" name="ol_insurance_record[0]" value="">
                                                            <div class="col-md-2 text-center">
                                                                <fieldset class="form-group">
                                                                    <input name="ol_ins_range_up[0]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="" {{$ol_ins_sw}} disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-md-2 text-center">
                                                                <fieldset class="form-group">
                                                                    <input name="ol_ins_range_down[0]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="" {{$ol_ins_sw}} disabled>
                                                                </fieldset>
                                                            </div>

                                                            <div class="col-md-2 text-center">
                                                                <fieldset class="form-group">
                                                                    <input name="ol_ins_charges[0]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" {{$ol_ins_sw}} disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col"></div>
                                                        </div>
                                                    @endif
                                                </div>

                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <h3 class="card-title">Return Charges</h3>
                                                    </div>
                                                    @php
                                                        $ol_return_sw = 'disabled';
                                                        $ol_return_switch = '';
                                                    if((isset($switches[2][0]) && $switches[2][0]->return_charges == 1)){
                                                    $ol_return_sw = 'disabled';
                                                    $ol_return_switch = 'checked';
                                                     }else{
                                                    $ol_return_sw = 'disabled';
                                                    $ol_return_switch = '';
                                                    }
                                                    @endphp
                                                    <div class="col-md-2">
                                                        <div class="form-group ">
                                                            <input type="checkbox" name="ol_return_switch" class="switchery returnChargesOverland" data-color="success" data-size="sm" {{$ol_return_switch}} disabled/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row return-charges-div-overland justify-content-center">
                                                    <input type="hidden" name="ol_return_record" value="{{ (isset($returnCharges[2][0]) && $returnCharges[2][0]->id != '')? $returnCharges[2][0]->id : ''}}">

                                                    <div class="col-md-2 text-center">
                                                        <label class="card-title">Local Charges</label>
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" name="ol_return_local_charges" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($returnCharges[2][0]) && $returnCharges[2][0]->local !== '')? $returnCharges[2][0]->local : ''}}" {{$ol_return_sw}}>
                                                        </fieldset>
                                                    </div>

                                                    <div class="col text-center">
                                                        <label class="card-title">Same Zone</label>
                                                        <fieldset class="form-group">
                                                            <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent" name="ol_return_same_zone_charges"  value="{{ (isset($returnCharges[2][0]) && $returnCharges[2][0]->same_zone !== '')? $returnCharges[2][0]->same_zone : ''}}" {{$ol_return_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <label class="card-title">Different Zone</label>
                                                        <fieldset class="form-group">
                                                            <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent" name="ol_return_different_zone_charges"  value="{{ (isset($returnCharges[2][0]) && $returnCharges[2][0]->different_zone !== '')? $returnCharges[2][0]->different_zone : ''}}" {{$ol_return_sw}}>
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
                                                            <input type="checkbox" name="overland_fuel_switch" class="switchery fuelSurchargeOverland" data-color="success" data-size="sm" {{$ol_fuel_switch}} disabled/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row fuel-surcharge-div-overland">
                                                    <input type="hidden" name="ol_fuel_record" value="{{ (isset($fuelCharges[2][0]) && $fuelCharges[2][0]->id != '')? $fuelCharges[2][0]->id : ''}}">

                                                    <div class="col-md-2 text-center">
                                                        <label class="card-title">Charges</label>
                                                        <fieldset>
                                                            <div class="input-group form-group">
                                                                <input type="text"  class="form-control " name="overland_fuel_surcharge" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($fuelCharges[2][0]) && $fuelCharges[2][0]->fuel_surcharge != '')? $fuelCharges[2][0]->fuel_surcharge : ''}}" {{$ol_fuel_sw}} disabled>
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
                                                            <input type='text' class="form-control" data-rule-required="true" data-msg-required="This field is required" {{$ol_discount_title_switch}} name="ol_discount_title" value="{{$ol_discount_title}}" disabled/>
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
                                                            <input type='text' class="form-control daterange" data-rule-required="true" data-msg-required="This field is required" {{$ol_discount_daterange_switch}} name="ol_daterange" {{$ol_discount_daterange_switch}} value="{{$ol_discount_daterange}}" disabled/>
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
                                                                <input type="checkbox" id="" class="switchery discountSwitchesOverland" name="ol_discount_weight_switch" data-size="xs" {{$ol_discount_weight_switch}} disabled/>
                                                              </span>
                                                                </div>
                                                                <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent ol-discount-inp" name="ol_discount_weight_rate" {{$ol_discount_weight_disable}} value="{{$ol_discount_weight_sw}}" disabled>
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
                                                                <input type="checkbox" name="ol_cash_weight_switch" class="switchery discountSwitchesOverland" data-size="xs" {{$ol_discount_cash_switch}} disabled/>
                                                              </span>
                                                                </div>
                                                                <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent ol-discount-inp" name="ol_discount_cash_rate" {{$ol_discount_cash_disable}} value="{{$ol_discount_cash_sw}}" disabled>
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
                                                                <input type="checkbox" name="ol_discount_insurance_switch" class="switchery discountSwitchesOverland" data-size="xs" {{$ol_discount_insurance_switch}} disabled/>
                                                              </span>
                                                                </div>
                                                                <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent ol-discount-inp" name="ol_discount_insurance_rate" value="{{$ol_discount_insurance_sw}}" {{$ol_discount_insurance_disable}} disabled>
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
                                                                <input type="checkbox"  class="switchery discountSwitchesOverland" data-size="xs" name="ol_discount_return_switch" {{$ol_discount_return_switch}} disabled/>
                                                              </span>
                                                                </div>
                                                                <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent ol-discount-inp" name="ol_discount_return_rate" value="{{$ol_discount_return_sw}}" {{$ol_discount_return_disable}}>
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
                                                    <label class="display-inline ml-1"> Default</label>
                                                    @if($shipper['default_shipping_mode'] == 3)
                                                        <input type="checkbox" name="det_default" id="det_default" class="switchery det_default" checked data-size="xs" data-switchery="true" disabled>
                                                    @else
                                                        <input type="checkbox" name="det_default" id="det_default" class="switchery det_default" data-size="xs" data-switchery="true" disabled>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <a id="detain_main_switch" href="javascript:void(0);" class="pull-right"><input name="detain_main_switch" type="checkbox" id="" class="switchery detain-main-switch" data-size="sm" {{ ((isset($switches[3][0]) && $switches[3][0]->status == 1) ? 'checked' : '') }} disabled/></a>
                                            </div>
                                        </div>

                                    </div>
                                    <div id="detain"  class="border-success no-border-top card {{ ((isset($switches[3][0]) && $switches[3][0]->status == 1) ? '' : 'hide') }}" aria-expanded="false">
                                        <input type="hidden" name="det_rate_record" value="{{ ((isset($switches[3][0]) && $switches[3][0]->id != '') ? $switches[3][0]->id : '') }}">

                                        <div class="card-content">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-2">
                                                        <h3>Origin Cities</h3>
                                                    </div>
                                                    <div class="col-8">
                                                        <div class="form-group card border-success p-2">
                                                            <select name="detain_origin_hubs[]" id="detain_origin_hubs" class="form-control select2" multiple="multiple" disabled>
                                                                @foreach($cities as $city)
                                                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-2">
                                                        <h3>Destination Cities</h3>
                                                    </div>
                                                    <div class="col-8">
                                                        <div class="form-group card border-success p-2">
                                                            <select name="detain_destination_hubs[]" id="detain_destination_hubs" class="form-control select2" multiple="multiple" disabled>
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
                                                                @if(isset($min_weight[3]))
                                                                    @foreach($min_weight[3] as $mweight)
                                                                        @if($mweight->delivery_type_id == 1)
                                                                            <input type="hidden" name="detain_door_min_chargeable_weight" value="{{$mweight->id}}">
                                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="{{$mweight->min_chargeable_weight}}" name="detain_door_mcw_charges" placeholder="Minimum Chargeable Weight" disabled>
                                                                        @endif
                                                                    @endforeach
                                                                @else
                                                                    <input type="hidden" name="detain_door_min_chargeable_weight" value="">
                                                                    <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="" name="detain_door_mcw_charges" placeholder="Minimum Chargeable Weight" disabled>

                                                                @endif
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
                                                    @if(isset($weight[3]))
                                                        @php
                                                            $index_row = 0;
                                                        @endphp
                                                        @foreach($weight[3] as $index => $detweight)
                                                            @if($detweight->delivery_type_id == 1)
                                                                <div class="row detain_door_weight_row" id="detain_door_weight_row{{$index_row}}">
                                                                    <input type="hidden" name="detain_door_weight_record[{{$index_row}}]" value="{{$detweight->id}}">
                                                                    <div class="col text-center">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->range_up}}" name="detain_door_range_up[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col text-center">

                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control decimal"data-rule-required="true" data-msg-required="This field is required"  value="{{$detweight->range_down}}" name="detain_door_range_down[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col text-center">

                                                                        <div class="form-group " style="padding-top: 8px;">
                                                                            <input type="checkbox" class="switchery weightAdditionDoorDetain" data-color="success" data-size="sm" {{ ($detweight->base == 1) ? 'checked' : '' }}  disabled/>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col text-center">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->local}}" name="detain_door_local_charges[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->same_zone}}" name="detain_door_same_zone_charges[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->different_zone}}" name="detain_different_zone_charges[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>

                                                                    <div class="col-1">

                                                                    </div>

                                                                </div>{{--Row--}}
                                                                @php
                                                                    $index_row++;
                                                                @endphp
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <div class="row detain_door_weight_row" id="detain_door_weight_row0">
                                                            <input type="hidden" name="detain_door_weight_record[0]" value="">
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="0.01" name="detain_door_range_up[0]"  disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">

                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control decimal"data-rule-required="true" data-msg-required="This field is required"  value="" name="detain_door_range_down[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">

                                                                <div class="form-group " style="padding-top: 8px;">
                                                                    <input type="checkbox" class="switchery weightAdditionDoorDetain" data-color="success" data-size="sm" disabled/>
                                                                </div>
                                                            </div>

                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_door_local_charges[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_door_same_zone_charges[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_different_zone_charges[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-1">
                                                            </div>

                                                        </div>{{--Row--}}
                                                    @endif
                                                </div>{{--weight addition div--}}

                                                <div class="hub-weight-addition-detain">
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <h3 class="card-title">Hub-Hub Delivery</h3>
                                                        </div>

                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <fieldset class="form-group">
                                                                @if(isset($min_weight[3]))
                                                                    @foreach($min_weight[3] as $mweight)
                                                                        @if($mweight->delivery_type_id == 2)
                                                                            <input type="hidden" name="detain_hub_min_chargeable_weight" value="{{$mweight->id}}">

                                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="{{$mweight->min_chargeable_weight}}" name="detain_hub_mcw_charges" placeholder="Minimum Chargeable Weight" disabled>
                                                                        @endif
                                                                    @endforeach
                                                                @else
                                                                    <input type="hidden" name="detain_hub_min_chargeable_weight" value="">
                                                                    <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="" name="detain_hub_mcw_charges" placeholder="Minimum Chargeable Weight" disabled>
                                                                @endif
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
                                                    @if(isset($weight[3]))
                                                        @php
                                                            $index_row = 0;
                                                        @endphp
                                                        @foreach($weight[3] as $index => $detweight)
                                                            @if($detweight->delivery_type_id == 1)

                                                                <div class="row detain_hub_weight_row" id="detain_hub_weight_row{{$index_row}}">
                                                                    <input type="hidden" name="detain_hub_weight_record[{{$index_row}}]" value="{{$detweight->id}}">
                                                                    <div class="col text-center">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->range_up}}" name="detain_hub_range_up[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col text-center">

                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control decimal"data-rule-required="true" data-msg-required="This field is required"  value="{{$detweight->range_down}}" name="detain_hub_range_down[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col text-center">

                                                                        <div class="form-group " style="padding-top: 8px;">
                                                                            <input type="checkbox" class="switchery weightAdditionHubDetain" data-color="success" data-size="sm" {{ ($detweight->base == 1) ? 'checked' : '' }}  disabled/>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col text-center">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->local}}" name="detain_hub_local_charges[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->same_zone}}" name="detain_hub_same_zone_charges[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->different_zone}}" name="detain_hub_different_zone_charges[{{$index_row}}]" disabled>
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
                                                    @else
                                                        <div class="row detain_hub_weight_row" id="detain_hub_weight_row0">
                                                            <input type="hidden" name="detain_hub_weight_record[0]" value="">
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="0.01" name="detain_hub_range_up[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">

                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control decimal"data-rule-required="true" data-msg-required="This field is required"  value="" name="detain_hub_range_down[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">

                                                                <div class="form-group " style="padding-top: 8px;">
                                                                    <input type="checkbox" class="switchery weightAdditionHubDetain" data-color="success" data-size="sm" disabled/>
                                                                </div>
                                                            </div>

                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_hub_local_charges[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_hub_same_zone_charges[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_hub_different_zone_charges[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-1">
                                                            </div>

                                                        </div>{{--Row--}}
                                                    @endif
                                                </div>{{--weight addition div--}}
                                                <div class="row mt-2">
                                                    <input type="hidden" name="detain_booking_record" value="{{ (isset($shippingType[3][0]) && $shippingType[3][0]->id != '')? $shippingType[3][0]->id : ''}}">

                                                    <div class="col-md-3 text-center">
                                                        <fieldset>
                                                            <div class="input-group form-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">Replacement</span>
                                                                </div>
                                                                <input type="text"  class="form-control percent" name="detain_replacement_charges" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($shippingType[3][0]) && $shippingType[3][0]->replacement_charges != '')? $shippingType[3][0]->replacement_charges : ''}}" disabled>
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
                                                                <input type="text"  class="form-control percent" name="detain_tnb_charges" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($shippingType[3][0]) && $shippingType[3][0]->try_and_buy_charges != '')? $shippingType[3][0]->try_and_buy_charges : ''}}" disabled>
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
                                                                        <label class="card-title mr-1">DWS Weight </label>
                                                                        <input type="checkbox" name="detain_dws" id="detain_dws" class="switchery detain_dws"  {{ (($detain_dws_charges != null) ? 'checked' : '') }} data-size="xs" data-switchery="true" disabled>
                                                                    </div>
                                                                </fieldset>
                                                            </div> 
                                                            <div class="col-4">
                                                                <fieldset>
                                                                    <div class="input-group form-group">
                                                                        <select name="detain_dws_weight" id="detain_dws_weight" class="form-control" disabled>
                                                                            @if ($detain_dws_charges != null)
                                                                            
                                                                                @if ($detain_dws_charges == 1)
                                                                                <option value="1" selected>High</option>
                                                                                <option value="2">Low</option>
                                                                                @elseif ($detain_dws_charges == 2)
                                                                                <option value="1" >High</option>
                                                                                <option value="2" selected>Low</option>
                                                                                @else
                                                                                <option value="1">High</option>
                                                                                <option value="2">Low</option>
                                                                                @endif
                                                                            @else
                                                                                <option value="1">High</option>
                                                                                <option value="2">Low</option>
                                                                            @endif
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
                                                            <input type="checkbox" name="detain_cash_handling_switch"  class="switchery cashChargesDetain" data-color="success" data-size="sm" {{$det_cash_switch}} disabled/>
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
                                                                        <input name="detain_cash_range_up[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->range_up}}" {{$det_cash_sw}} disabled>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-2 text-center">
                                                                    <fieldset class="form-group">
                                                                        <input name="detain_cash_range_down[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->range_down}}" {{$det_cash_sw}} disabled>
                                                                    </fieldset>
                                                                </div>

                                                                <div class="col-md-2">
                                                                    <fieldset class="form-group">
                                                                        <input name="detain_cash_charges[{{$index}}]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->charges}}" {{$det_cash_sw}} disabled>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col">

                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="row det_cash_handling_row">
                                                            <input type="hidden" name="detain_cash_record[0]" value="">

                                                            <div class="col-md-2 text-center">
                                                                <fieldset class="form-group">
                                                                    <input name="detain_cash_range_up[0]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="" {{$det_cash_sw}} disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-md-2 text-center">
                                                                <fieldset class="form-group">
                                                                    <input name="detain_cash_range_down[0]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="" {{$det_cash_sw}} disabled>
                                                                </fieldset>
                                                            </div>

                                                            <div class="col-md-2">
                                                                <fieldset class="form-group">
                                                                    <input name="detain_cash_charges[0]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" {{$det_cash_sw}} disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col"></div>
                                                        </div>
                                                    @endif
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
                                                            <input type="checkbox" name="detain_insurance_charges_switch" class="switchery insuranceChargesdetain" data-color="success" data-size="sm" {{$det_insurance_switch}} disabled/>
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
                                                                        <input name="detain_ins_range_up[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$det_insurance->range_up}}" {{$det_ins_sw}} disabled>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-2 text-center">
                                                                    <fieldset class="form-group">
                                                                        <input name="detain_ins_range_down[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$det_insurance->range_down}}" {{$det_ins_sw}} disabled>
                                                                    </fieldset>
                                                                </div>

                                                                <div class="col-md-2">
                                                                    <fieldset class="form-group">
                                                                        <input name="detain_ins_charges[{{$index}}]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$det_insurance->charges}}" {{$det_ins_sw}} disabled>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col">

                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="row det_insurance_row">
                                                            <input type="hidden" name="detain_insurance_record[0]" value="">

                                                            <div class="col-md-2 text-center">
                                                                <fieldset class="form-group">
                                                                    <input name="detain_ins_range_up[0]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="" {{$det_ins_sw}} disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-md-2 text-center">
                                                                <fieldset class="form-group">
                                                                    <input name="detain_ins_range_down[0]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="" {{$det_ins_sw}} disabled>
                                                                </fieldset>
                                                            </div>

                                                            <div class="col-md-2">
                                                                <fieldset class="form-group">
                                                                    <input name="detain_ins_charges[0]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" {{$det_ins_sw}} disabled>
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
                                                        $det_return_sw = 'disabled';
                                                        $det_return_switch = '';
                                                    if((isset($switches[3][0]) && $switches[3][0]->return_charges == 1)){
                                                    $det_return_sw = 'disabled';
                                                    $det_return_switch = 'checked';
                                                     }else{
                                                    $det_return_sw = 'disabled';
                                                    $det_return_switch = '';
                                                    }
                                                    @endphp
                                                    <div class="col-md-2">
                                                        <div class="form-group ">
                                                            <input type="checkbox" name="detain_return_switch" class="switchery returnChargesDetain" data-color="success" data-size="sm" {{$det_return_switch}} disabled/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row return-charges-div-detain justify-content-center">
                                                    <input type="hidden" name="detain_return_record" value="{{ (isset($returnCharges[3][0]) && $returnCharges[3][0]->id != '')? $returnCharges[3][0]->id : ''}}">

                                                    <div class="col-md-2 text-center">
                                                        <label class="card-title">Local Charges</label>
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control amount" name="detain_return_local_charges" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($returnCharges[3][0]) && $returnCharges[3][0]->local !== '')? $returnCharges[3][0]->local : ''}}" {{$det_return_sw}}>
                                                        </fieldset>
                                                    </div>

                                                    <div class="col text-center">
                                                        <label class="card-title">Same Zone</label>
                                                        <fieldset class="form-group">
                                                            <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent" name="detain_return_same_zone_charges"  value="{{ (isset($returnCharges[3][0]) && $returnCharges[3][0]->same_zone !== '')? $returnCharges[3][0]->same_zone : ''}}" {{$det_return_sw}}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <label class="card-title">Different Zone</label>
                                                        <fieldset class="form-group">
                                                            <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent" name="detain_return_different_zone_charges"  value="{{ (isset($returnCharges[3][0]) && $returnCharges[3][0]->different_zone !== '')? $returnCharges[3][0]->different_zone : ''}}" {{$det_return_sw}}>
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
                                                            <input type="checkbox" name="detain_fuel_switch" class="switchery fuelSurchargeDetain" data-color="success" data-size="sm" {{$det_fuel_switch}} disabled/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row fuel-surcharge-div-detain">
                                                    <input type="hidden" name="detain_fuel_record" value="{{ (isset($fuelCharges[3][0]) && $fuelCharges[3][0]->id != '')? $fuelCharges[3][0]->id : ''}}">

                                                    <div class="col-md-2 text-center">
                                                        <label class="card-title">Charges</label>
                                                        <fieldset>
                                                            <div class="input-group form-group">
                                                                <input type="text"  class="form-control " name="detain_fuel_surcharge" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($fuelCharges[3][0]) && $fuelCharges[3][0]->fuel_surcharge != '')? $fuelCharges[3][0]->fuel_surcharge : ''}}" {{$det_fuel_sw}} disabled>
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
                                                            <input type='text' class="form-control" data-rule-required="true" data-msg-required="This field is required" {{$det_discount_title_switch}} name="detain_discount_title" value="{{$det_discount_title}}" disabled/>
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
                                                            <input type='text' class="form-control daterange" data-rule-required="true" data-msg-required="This field is required" name="detain_daterange" value="{{$det_discount_daterange}}"  {{$det_discount_daterange_switch}} disabled/>
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
                                                                <input type="checkbox" id="" class="switchery discountSwitchesDetain" name="detain_discount_weight_switch" data-size="xs" {{$det_discount_weight_switch}} disabled/>
                                                              </span>
                                                                </div>
                                                                <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent detain-discount-inp" name="detain_discount_weight_rate" value="{{$det_discount_weight_sw}}" {{$det_discount_weight_disable}} disabled>
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
                                                                <input type="checkbox" name="detain_cash_weight_switch" class="switchery discountSwitchesDetain" data-size="xs" {{$det_discount_cash_switch}} disabled/>
                                                              </span>
                                                                </div>
                                                                <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent detain-discount-inp" name="detain_discount_cash_rate" value="{{$det_discount_cash_sw}}" {{$det_discount_cash_disable}} disabled>
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
                                                                <input type="checkbox" name="detain_discount_insurance_switch" class="switchery discountSwitchesDetain" data-size="xs" {{$det_discount_insurance_switch}} disabled/>
                                                              </span>
                                                                </div>
                                                                <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent detain-discount-inp" name="detain_discount_insurance_rate" value="{{$det_discount_insurance_sw}}" {{$det_discount_insurance_disable}} disabled>
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
                                                                <input type="checkbox"  class="switchery discountSwitchesDetain" data-size="xs" name="detain_discount_return_switch" {{$det_discount_return_switch}} disabled/>
                                                              </span>
                                                                </div>
                                                                <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent detain-discount-inp" name="detain_discount_return_rate" value="{{$det_discount_return_sw}}" {{$det_discount_return_disable}} disabled>
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
                                                    <label class="display-inline ml-1"> Default</label>
                                                    @if($shipper['default_shipping_mode'] == 4)
                                                        <input type="checkbox" name="sameday_default" id="sameday_default" class="switchery sameday_default" checked data-size="xs" data-switchery="true" disabled>
                                                    @else
                                                        <input type="checkbox" name="sameday_default" id="sameday_default" class="switchery sameday_default" data-size="xs" data-switchery="true" disabled>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <a id="sameday_main_switch" href="javascript:void(0);" class="pull-right"><input name="sameday_main_switch" type="checkbox" id="" class="switchery sameday-main-switch" data-size="sm" {{ ((isset($switches[4][0]) && $switches[4][0]->status == 1) ? 'checked' : '') }} disabled/></a>
                                            </div>
                                        </div>

                                    </div>
                                    <div id="sameday" class="border-success no-border-top card {{ ((isset($switches[4][0]) && $switches[4][0]->status == 1) ? '' : 'hide') }}">
                                        <input type="hidden" name="same_rate_record" value="{{ ((isset($switches[4][0]) && $switches[4][0]->id != '') ? $switches[4][0]->id : '') }}">

                                        <div class="card-content">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-2">
                                                        <h3>Origin Cities</h3>
                                                    </div>
                                                    <div class="col-8">
                                                        <div class="form-group card border-success p-2">
                                                            <select name="sameday_origin_hubs[]" id="sameday_origin_hubs" class="form-control select2" multiple="multiple" disabled>
                                                                @foreach($cities as $city)
                                                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-2">
                                                        <h3>Destination Cities</h3>
                                                    </div>
                                                    <div class="col-8">
                                                        <div class="form-group card border-success p-2">
                                                            <select name="sameday_destination_hubs[]" id="sameday_destination_hubs" class="form-control select2" multiple="multiple" disabled>
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
                                                                @if(isset($min_weight[4]))
                                                                    @foreach($min_weight[4] as $mweight)
                                                                        @if($mweight->delivery_type_id == 1)
                                                                            <input type="hidden" name="sameday_door_min_chargeable_weight" value="{{$mweight->id}}">

                                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="{{$mweight->min_chargeable_weight}}" name="sameday_door_mcw_charges" placeholder="Minimum Chargeable Weight" disabled>
                                                                        @endif
                                                                    @endforeach
                                                                @else
                                                                    <input type="hidden" name="sameday_door_min_chargeable_weight" value="">
                                                                    <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="" name="sameday_door_mcw_charges" placeholder="Minimum Chargeable Weight" disabled>
                                                                @endif
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
                                                    @if(isset($weight[4]))
                                                        @php
                                                            $index_row = 0;
                                                        @endphp
                                                        @foreach($weight[4] as $index => $sameweight)
                                                            @if($sameweight->delivery_type_id == 1)
                                                                <div class="row sameday_door_weight_row" id="sameday_door_weight_row{{$index_row}}">
                                                                    <input type="hidden" name="sameday_door_weight_record[{{$index_row}}]" value="{{$sameweight->id}}">

                                                                    <div class="col text-center">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->range_up}}" name="sameday_door_range_up[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col text-center">

                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->range_down}}" name="sameday_door_range_down[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col text-center">

                                                                        <div class="form-group " style="padding-top: 8px;">
                                                                            <input type="checkbox" class="switchery weightAdditionDoorSameday" data-color="success" data-size="sm" {{ ($sameweight->base == 1) ? 'checked' : '' }}  disabled/>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col text-center">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->local}}" name="sameday_door_local_charges[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->same_zone}}" name="sameday_door_same_zone_charges[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col-1">

                                                                    </div>
                                                                </div>{{--Row--}}
                                                                @php
                                                                    $index_row++;
                                                                @endphp
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <div class="row sameday_door_weight_row" id="sameday_door_weight_row0">
                                                            <input type="hidden" name="sameday_door_weight_record[0]" value="">

                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="0.01" name="sameday_door_range_up[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">

                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_door_range_down[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">

                                                                <div class="form-group " style="padding-top: 8px;">
                                                                    <input type="checkbox" class="switchery weightAdditionDoorSameday" data-color="success" data-size="sm" disabled/>
                                                                </div>
                                                            </div>

                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_door_local_charges[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_door_same_zone_charges[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-1">
                                                            </div>
                                                        </div>{{--Row--}}
                                                    @endif
                                                </div>{{--weight addition div--}}

                                                <div class="hub-weight-addition-sameday">
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <h3 class="card-title">Hub-Hub Delivery</h3>
                                                        </div>

                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <fieldset class="form-group">
                                                                @if(isset($min_weight[4]))
                                                                    @foreach($min_weight[4] as $mweight)
                                                                        @if($mweight->delivery_type_id == 2)
                                                                            <input type="hidden" name="sameday_hub_min_chargeable_weight" value="{{$mweight->id}}">
                                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="{{$mweight->min_chargeable_weight}}" name="sameday_hub_mcw_charges" placeholder="Minimum Chargeable Weight" disabled>
                                                                        @endif
                                                                    @endforeach
                                                                @else
                                                                    <input type="hidden" name="sameday_hub_min_chargeable_weight" value="">
                                                                    <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="" name="sameday_hub_mcw_charges" placeholder="Minimum Chargeable Weight" disabled>
                                                                @endif
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
                                                    @if(isset($weight[4]))
                                                        @php
                                                            $index_row = 0;
                                                        @endphp
                                                        @foreach($weight[4] as $index => $sameweight)
                                                            @if($sameweight->delivery_type_id == 2)
                                                                <div class="row sameday_hub_weight_row" id="sameday_hub_weight_row{{$index_row}}">
                                                                    <input type="hidden" name="sameday_hub_weight_record[{{$index_row}}]" value="{{$sameweight->id}}">
                                                                    <div class="col text-center">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->range_up}}" name="sameday_hub_range_up[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col text-center">

                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->range_down}}" name="sameday_hub_range_down[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col text-center">

                                                                        <div class="form-group " style="padding-top: 8px;">
                                                                            <input type="checkbox" class="switchery weightAdditionHubSameday" data-color="success" data-size="sm" {{ ($sameweight->base == 1) ? 'checked' : '' }}  disabled/>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col text-center">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->local}}" name="sameday_hub_local_charges[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->same_zone}}" name="sameday_hub_same_zone_charges[{{$index_row}}]" disabled>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col-1">

                                                                    </div>
                                                                </div>{{--Row--}}
                                                                @php
                                                                    $index_row++;
                                                                @endphp
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <div class="row sameday_hub_weight_row" id="sameday_hub_weight_row0">
                                                            <input type="hidden" name="sameday_hub_weight_record[0]" value="">
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="0.01" name="sameday_hub_range_up[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">

                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_hub_range_down[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">

                                                                <div class="form-group " style="padding-top: 8px;">
                                                                    <input type="checkbox" class="switchery weightAdditionHubSameday" data-color="success" data-size="sm"  disabled/>
                                                                </div>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_hub_local_charges[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_hub_same_zone_charges[0]" disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-1">

                                                            </div>
                                                        </div>{{--Row--}}
                                                    @endif
                                                </div>{{--weight addition div--}}
                                                <div class="row mt-2">
                                                    <input type="hidden" name="sameday_booking_record" value="{{ (isset($shippingType[4][0]) && $shippingType[4][0]->id != '')? $shippingType[4][0]->id : ''}}">

                                                    <div class="col-md-3 text-center">
                                                        <fieldset>
                                                            <div class="input-group form-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">Replacement</span>
                                                                </div>
                                                                <input type="text"  class="form-control percent" name="sameday_replacement_charges" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($shippingType[4][0]) && $shippingType[4][0]->replacement_charges != '')? $shippingType[4][0]->replacement_charges : ''}}" disabled>
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
                                                                <input type="text"  class="form-control percent" name="sameday_tnb_charges" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($shippingType[4][0]) && $shippingType[4][0]->try_and_buy_charges != '')? $shippingType[4][0]->try_and_buy_charges : ''}}" disabled>
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
                                                                        <label class="card-title mr-1">DWS Weight </label>
                                                                        <input type="checkbox" name="sameday_dws" id="sameday_dws" class="switchery sameday_dws" {{ (($sameday_dws_charges != null) ? 'checked' : '') }} data-size="xs" data-switchery="true" disabled>
                                                                    </div>
                                                                </fieldset>
                                                            </div> 
                                                            <div class="col-4">
                                                                <fieldset>
                                                                    <div class="input-group form-group">
                                                                        <select name="sameday_dws_weight" id="sameday_dws_weight" class="form-control" disabled>
                                                                            @if ($sameday_dws_charges != null)
                                                                            
                                                                                @if ($sameday_dws_charges == 1)
                                                                                <option value="1" selected>High</option>
                                                                                <option value="2">Low</option>
                                                                                @elseif ($sameday_dws_charges == 2)
                                                                                <option value="1" >High</option>
                                                                                <option value="2" selected>Low</option>
                                                                                @else
                                                                                <option value="1">High</option>
                                                                                <option value="2">Low</option>
                                                                                @endif
                                                                            @else
                                                                                <option value="1">High</option>
                                                                                <option value="2">Low</option>
                                                                             @endif
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
                                                            <input type="checkbox" name="sameday_cash_handling_switch"  class="switchery cashChargesSameday" data-color="success" data-size="sm" {{$same_cash_switch}} disabled/>
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
                                                                        <input name="sameday_cash_range_up[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->range_up}}" {{$same_cash_sw}} disabled>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-2 text-center">
                                                                    <fieldset class="form-group">
                                                                        <input name="sameday_cash_range_down[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->range_down}}" {{$same_cash_sw}} disabled>
                                                                    </fieldset>
                                                                </div>

                                                                <div class="col-md-2 text-center">
                                                                    <fieldset class="form-group">
                                                                        <input name="sameday_cash_charges[{{$index}}]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->charges}}" {{$same_cash_sw}} disabled>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col">

                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="row same_cash_handling_row">
                                                            <input type="hidden" name="sameday_cash_record[0]" value="">

                                                            <div class="col-md-2 text-center">
                                                                <fieldset class="form-group">
                                                                    <input name="sameday_cash_range_up[0]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="" {{$same_cash_sw}} disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-md-2 text-center">
                                                                <fieldset class="form-group">
                                                                    <input name="sameday_cash_range_down[0]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="" {{$same_cash_sw}} disabled>
                                                                </fieldset>
                                                            </div>

                                                            <div class="col-md-2 text-center">
                                                                <fieldset class="form-group">
                                                                    <input name="sameday_cash_charges[0]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" {{$same_cash_sw}} disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col"></div>
                                                        </div>
                                                    @endif
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
                                                            <input type="checkbox" name="sameday_insurance_charges_switch" class="switchery insuranceChargessameday" data-color="success" data-size="sm" {{$same_insurance_switch}} disabled/>
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
                                                                        <input name="sameday_ins_range_up[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$same_insurance->range_up}}" {{$same_ins_sw}} disabled>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-2 text-center">
                                                                    <fieldset class="form-group">
                                                                        <input name="sameday_ins_range_down[{{$index}}]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="{{$same_insurance->range_down}}" {{$same_ins_sw}} disabled>
                                                                    </fieldset>
                                                                </div>

                                                                <div class="col-md-2 text-center">
                                                                    <fieldset class="form-group">
                                                                        <input name="sameday_ins_charges[{{$index}}]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$same_insurance->charges}}" {{$same_ins_sw}} disabled>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col">

                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="row same_insurance_row">
                                                            <input type="hidden" name="sameday_insurance_record[0]" value="">

                                                            <div class="col-md-2 text-center">
                                                                <fieldset class="form-group">
                                                                    <input name="sameday_ins_range_up[0]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="" {{$same_ins_sw}} disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-md-2 text-center">
                                                                <fieldset class="form-group">
                                                                    <input name="sameday_ins_range_down[0]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="" {{$same_ins_sw}} disabled>
                                                                </fieldset>
                                                            </div>

                                                            <div class="col-md-2 text-center">
                                                                <fieldset class="form-group">
                                                                    <input name="sameday_ins_charges[0]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" {{$same_ins_sw}} disabled>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col"></div>
                                                        </div>
                                                    @endif
                                                </div>

                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <h3 class="card-title">Return Charges</h3>
                                                    </div>
                                                    @php
                                                        $same_return_sw = 'disabled';
                                                        $same_return_switch = '';
                                                    if((isset($switches[4][0]) && $switches[4][0]->return_charges == 1)){
                                                    $same_return_sw = 'disabled';
                                                    $same_return_switch = 'checked';
                                                     }else{
                                                    $same_return_sw = 'disabled';
                                                    $same_return_switch = '';
                                                    }
                                                    @endphp
                                                    <div class="col-md-2">
                                                        <div class="form-group ">
                                                            <input type="checkbox" name="sameday_return_switch" class="switchery returnChargesSameday" data-color="success" data-size="sm" {{$same_return_switch}} disabled/>
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
                                                            <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent" name="sameday_return_same_zone_charges"  value="{{ (isset($returnCharges[4][0]) && $returnCharges[4][0]->same_zone !== '')? $returnCharges[4][0]->same_zone : ''}}" {{$same_return_sw}}>
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
                                                            <input type="checkbox" name="sameday_fuel_switch" class="switchery fuelSurchargeSameday" data-color="success" data-size="sm" {{$same_fuel_switch}} disabled/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row fuel-surcharge-div-sameday">
                                                    <input type="hidden" name="sameday_fuel_record" value="{{ (isset($fuelCharges[4][0]) && $fuelCharges[4][0]->id != '')? $fuelCharges[4][0]->id : ''}}">

                                                    <div class="col-md-2 text-center">
                                                        <label class="card-title">Charges</label>
                                                        <fieldset>
                                                            <div class="input-group form-group">
                                                                <input type="text"  class="form-control " name="sameday_fuel_surcharge" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($fuelCharges[4][0]) && $fuelCharges[4][0]->fuel_surcharge != '')? $fuelCharges[4][0]->fuel_surcharge : ''}}" {{$same_fuel_sw}} disabled>
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
                                                            <input type='text' class="form-control" data-rule-required="true" data-msg-required="This field is required"  name="sameday_discount_title" value="{{$same_discount_title}}" {{$same_discount_title_switch}} disabled/>
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
                                                            <input type='text' class="form-control daterange" data-rule-required="true" data-msg-required="This field is required" {{$same_discount_daterange_switch}} name="sameday_daterange" value="{{$same_discount_daterange}}" disabled/>
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
                                                                <input type="checkbox" id="" class="switchery discountSwitchesSameday" name="sameday_discount_weight_switch" data-size="xs" {{$same_discount_weight_switch}} disabled/>
                                                              </span>
                                                                </div>
                                                                <input type="text"  class="form-control dec-percent sameday-discount-inp"  data-rule-required="true" data-msg-required="This field is required" name="sameday_discount_weight_rate" {{$same_discount_weight_disable}} value="{{$same_discount_weight_sw}}" disabled>
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
                                                                <input type="checkbox" name="sameday_cash_weight_switch" class="switchery discountSwitchesSameday" data-size="xs" {{$same_discount_cash_switch}} disabled/>
                                                              </span>
                                                                </div>
                                                                <input type="text"  class="form-control dec-percent sameday-discount-inp"  data-rule-required="true" data-msg-required="This field is required" name="sameday_discount_cash_rate" value="{{$same_discount_cash_sw}}" {{$same_discount_cash_disable}} disabled>
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
                                                                <input type="checkbox" name="sameday_discount_insurance_switch" class="switchery discountSwitchesSameday" data-size="xs" {{$same_discount_insurance_switch}} disabled/>
                                                              </span>
                                                                </div>
                                                                <input type="text"  class="form-control dec-percent sameday-discount-inp"  data-rule-required="true" data-msg-required="This field is required" name="sameday_discount_insurance_rate" value="{{$same_discount_insurance_sw}}" {{$same_discount_insurance_disable}} disabled>
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
                                                                <input type="checkbox"  class="switchery discountSwitchesSameday" data-size="xs" name="sameday_discount_return_switch" {{$same_discount_return_switch}} disabled/>
                                                              </span>
                                                                </div>
                                                                <input type="text"  class="form-control dec-percent sameday-discount-inp"  data-rule-required="true" data-msg-required="This field is required" name="sameday_discount_return_rate" value="{{$same_discount_return_sw}}" {{$same_discount_return_disable}} disabled>
                                                            </div>
                                                        </fieldset>
                                                    </div>

                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                   @isset($wms_user_info->warehousing)
                            <div id="" class="card-header mt-1 border-primary">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3 class="display-inline card-title lead primary">Warehousing</h3>

                                    </div>
                                    <div class="col-md-6">
                                        <a id="warehouse_main_switch" href="javascript:void(0);" class="pull-right"><input name="warehouse_main_switch" type="checkbox" class="switchery warehouse-main-switch" data-size="sm" data-color="info" {{ ($wms_user_info->warehousing)? 'checked':'' }} disabled/></a>
                                    </div>
                                </div>

                            </div>

                            <div id="warehousing" class="border-primary no-border-top card {{ ($wms_user_info->warehousing)? '':'hide' }}">
                                <div class="card-content">
                                    <div class="card-body pb-0">
                                        <div class="row">
                                            <div class="col-3 form-group">
                                                <select name="invoicing_cycle" class="select2" id="invoicing_cycle_select" data-rule-required="true" data-msg-required="Invoicing cycle is required" disabled="disabled">
                                                    @foreach($invoicing_cycles as $cycle)
                                                        @if($cycle->id == $wms_user_info->invoicing_cycle)
                                                            <option value="{{ $cycle->id }}" selected="selected">{{ $cycle->name }}</option>
                                                        @else
                                                            <option value="{{ $cycle->id }}">{{ $cycle->name }}</option>
                                                        @endif

                                                    @endforeach
                                                </select>
                                            </div>
                                            @php
                                            $class = '';
                                                if($wms_user_info->invoicing_cycle == 2){
                                                    $class = 'd-none';
                                                }
                                            @endphp

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
                                                                            <input type="checkbox" name="ppc_switch" data-color="info" class="switchery PPCSwitch" data-size="xs" {{ ($wms_user_info->per_product_charges)? 'checked':'' }} disabled/>
                                                                          </span>
                                                                    </div>
                                                                    <input type="text"  class="form-control numeric ppc-inp" value="{{ isset($wms_product_charges)? $wms_product_charges->charges:0}}"  data-rule-required="true" data-msg-required="This field is required" name="ppc_charges"disabled >
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
                                                                            <input type="checkbox" name="psf_switch" data-color="info" class="switchery PSFSwitch" data-size="xs" {{ ($wms_user_info->per_square_foot_charges)? 'checked':'' }} disabled/>
                                                                          </span>
                                                                    </div>
                                                                    <input type="text"  class="form-control numeric psf-inp" value="{{ isset($wms_square_foot_charges)? $wms_square_foot_charges->charges:0 }}"  data-rule-required="true" data-msg-required="This field is required" name="psf_charges" disabled>
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
                                                                <input type="checkbox" name="storage_charges_switch" class="switchery storageCharges" data-color="info" data-size="sm" {{ ($wms_user_info->storage_charges)? 'checked':'' }} disabled/>
                                                            </div>
                                                        </div>
                                                        <div class="col-12" id="wms_storage_types_div">
                                                            @if($wms_user_info->storage_charges)
                                                            @foreach($wms_storage_charges as $key => $storage)
                                                            <div class="row">

                                                                <div class="col-md-2">
                                                                    <fieldset class="form-group">
                                                                        <select class="select2 form-control storage_type" name="storage_type[{{$key}}]" data-rule-required="true" data-msg-required="This field is required" disabled>
                                                                            @foreach($storage_types as $types)
                                                                               <option value="{{$types->id}}">{{$types->name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </fieldset>
                                                                </div>

                                                                <div class="col-md-2">
                                                                    <fieldset class="form-group">
                                                                        <input name="storage_type_charges[0]" data-rule-required="true" data-msg-required="Charges are required" type="text" class="form-control numeric" placeholder="Charges" value="{{$storage->charges}}" disabled="disabled">
                                                                    </fieldset>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                            @endif
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
                                                                <input type="checkbox" name="packing_charges_switch" class="switchery packingCharges" data-color="info" data-size="sm" {{ ($wms_user_info->packing_charges)? 'checked':''}} disabled="disabled"/>
                                                            </div>
                                                        </div>

                                                        <div class="col-12" id="wms_packing_charges_div">
                                                            @if($wms_user_info->packing_charges)
                                                            @foreach($wms_packing_charges as $key => $packing)
                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <fieldset class="form-group">
                                                                        <select class="select2 form-control packing_type" name="packing_type[{{$key}}]" data-rule-required="true" data-msg-required="This field is required" disabled="disabled">
                                                                           @foreach($packaging_material_types as $mtype)
                                                                                <option value="{{$mtype->id}}">{{$mtype->type}}</option>
                                                                           @endforeach
                                                                        </select>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <fieldset class="form-group">
                                                                        <select class="select2 form-control packing_size" name="packing_size[{{$key}}]" data-rule-required="true" data-msg-required="This field is required" disabled="disabled">
                                                                           @foreach($packaging_material_types as $material_type)
                                                                                @foreach($material_type->sizes as $size)
                                                                                    <option value="{{$size->id}}">{{$size->size}}</option>
                                                                                @endforeach
                                                                           @endforeach
                                                                        </select>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <fieldset class="form-group">
                                                                        <input name="packing_charges[0]" value="{{$packing->charges}}" type="text" class="form-control numeric" placeholder="Charges" disabled="disabled">
                                                                    </fieldset>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                            @endif
                                                        </div>

                                                    </div>

                                                    <div class="row">
                                                        <div class="col-2">
                                                            <h3 class="card-title">Labelling Charges</h3>
                                                        </div>
                                                        <div class="col-3">
                                                            <div class="form-group ">
                                                                <input type="checkbox" name="labelling_charges_switch" class="switchery labellingSwitch" data-color="info" data-size="sm" {{ ($wms_user_info->labelling_charges)? 'checked':''}} disabled/>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <fieldset class="form-group">
                                                                @if($wms_user_info->labelling_charges)
                                                                <input name="labelling_charges" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric" placeholder="Charges" value="{{ $wms_labelling_charges->charges }}" disabled="disabled">
                                                                @else
                                                                <input name="labelling_charges" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric" placeholder="Charges" value="0" disabled="disabled">
                                                                @endif
                                                            </fieldset>
                                                        </div>
                                                    </div>


                                                    </div>


                                                </div>

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            @else
                            <div id="" class="card-header mt-1 border-primary">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3 class="display-inline card-title lead primary">Warehousing</h3>

                                    </div>
                                    <div class="col-md-6">
                                        <a id="warehouse_main_switch" href="javascript:void(0);" class="pull-right"><input name="warehouse_main_switch" type="checkbox" class="switchery warehouse-main-switch" data-size="sm" data-color="info" disabled/></a>
                                    </div>
                                </div>

                            </div>
                            @endisset

                        @if($sales_commission)
                            <div class="row justify-content-center mt-2 mb-2">
                                <div class="col-3 border border-primary p-1"><b>Total Commission</b></div>
                                <div class="col-3 border border-primary p-1"><b>{{$sales_commission->commission}}%</b></div>

                                <div class="col-12 mt-1   ">
                                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                        <thead>
                                        <tr role="row" class="bg-primary white">
                                            <th class="border-primary border-darken-1">S. No.</th>
                                            <th class="border-primary border-darken-1">User Name</th>
                                            <th class="border-primary border-darken-1">Tier</th>
                                            <th class="border-primary border-darken-1">Commission Percentage</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($sales_commission->users as $index => $sales_user)
                                            <tr>
                                                <td>{{++$index}}</td>
                                                @if($sales_user->tier_type_id == 1)
                                                    <td>{{$sales_user->sales_person->name}}</td>
                                                @else
                                                    <td>{{$sales_user->sales_person_external->name}}</td>
                                                @endif
                                                <td>{{$sales_user->tier->tier_name}}</td>
                                                <td>{{$sales_user->commission}}%</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                            @if(count($rate_remarks) > 0)
                                <div class="row justify-content-center">
                                    <div class="col-6">
                                        <div class="card">
                                            <div class="card-header mb-0 pb-0">
                                                <h3 class="">Remarks</h3>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-stripped table-bordered datatable" style="z-index: 3">
                                                <thead>
                                                    <tr class="bg-primary white">
                                                        <th class="border-primary border-darken-1">Remarks</th>
                                                        <th class="border-primary border-darken-1">Admin</th>
                                                        <th class="border-primary border-darken-1">Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($rate_remarks as $remark)
                                                        <tr>
                                                            <td>{{$remark->remarks}}</td>
                                                            <td>{{$remark->admin->name}}</td>
                                                            <td>{{$remark->created_at}}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            @endif

                                </form>

                            </div>
                        </div>
                    </div>
                </div>

            </section>

        @else
            <h1>Standard rates not set.</h1>
        @endif

    @else
        <h1>Shipper does not exist.</h1>
    @endif
@endsection
@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">

    <style type="text/css">
        .hide{
            display:none;
        }
    </style>


@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>

    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
        @isset($wms_user_info->warehousing)
        var weekly = [1, 2, 3, 4, 5, 6, 7];
        var monthly = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28];
        var invoicing_cycle = '{{$wms_user_info->invoicing_cycle}}';
        invoicing_cycle = parseInt(invoicing_cycle);
        
        $('#invoicing_cycle_select').select2({
            placeholder: "Select Invoicing Cycle",
            width:'100%'
        });

        @foreach($wms_storage_charges as $index => $storage)
        $('select[name="storage_type[{{$index}}]"]').select2({
            width:'100%',
            placeholder:'Select Storage Type'
        });
        $('select[name="storage_type[{{$index}}]"]').val({{$storage->storage_type_id}}).trigger('change');
        @endforeach

        @foreach($wms_packing_charges as $ind => $packing)
        $('select[name="packing_type[{{$ind}}]"]').select2({
            width:'100%',
            placeholder:'Select Packing Type'
        });
        $('select[name="packing_type[{{$ind}}]"]').val({{$packing->packing_type_id}}).trigger('change');
        $('select[name="packing_size[{{$ind}}]"]').select2({
            width:'100%',
            placeholder:'Select Packing Size'
        });
        $('select[name="packing_size[{{$ind}}]"]').val({{$packing->packing_size_id}}).trigger('change');
        @endforeach

        
        @endisset

            //Origin And Destination Cities Start
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

            var overnight_origins = @json($overnight_origins);
            var overland_origins = @json($overland_origins);
            var detain_origins = @json($detain_origins);
            var sameday_origins = @json($sameday_origins);

            $('#on_origin_hubs').val(overnight_origins).trigger('change');


            $('#ol_origin_hubs').val(overland_origins).trigger('change');


            $('#detain_origin_hubs').val(detain_origins).trigger('change');


            $('#sameday_origin_hubs').val(sameday_origins).trigger('change');

            var overnight_destinations = @json($overnight_destinations);
            var overland_destinations = @json($overland_destinations);
            var detain_destinations = @json($detain_destinations);
            var sameday_destinations = @json($sameday_destinations);

            $('#on_destination_hubs').val(overnight_destinations).trigger('change');


            $('#ol_destination_hubs').val(overland_destinations).trigger('change');


            $('#detain_destination_hubs').val(detain_destinations).trigger('change');


            $('#sameday_destination_hubs').val(sameday_destinations).trigger('change');
            //Origin And Destination Cities End

        });


    </script>
@endsection