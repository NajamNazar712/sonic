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
                       {{-- <h2 class="font-large-1">{{$shipper->name}}
                            <div class="badge badge-success pull-right">Corporate Invoicing Account</div>
                        </h2>--}}
                        <div class="row">
                            <div class="col-4">
                                <h2 class="font-large-1">{{$shipper->name}} </h2>
                            </div>
                            <div class="col-4 text-right mt-1">
                                <input type="checkbox" id="packaging_invoice_toggle" class="switchery packaging_invoice_toggle" data-size="xs" data-switchery="true" @if(isset($packaging_invoice->status) && $packaging_invoice->status == 1) checked @endif>
                                <label class="display-inline ml-1 font-medium-1">Generate Packaging Invoice</label>
                            </div>
                            <div class="col-4">
                                <div class="badge badge-success pull-right"><h2 class="text-white">Corporate Invoicing Account</h2></div>
                            </div>
                        </div>
                        @include('admin.inc.messages')
                    </div>
                    <input type="hidden" id="shipper_id" value="{{$shipper->id}}">


                    <div class="card-content">
                        <form id="ratesAdditionForm" class="card-body card-dashboard" action="{{route('admin.corporate.edit.rates',['id'=>$shipper->id])}}" method="post" novalidate="novalidate">
                            @csrf
                            @method('PUT')

                            <input type="hidden" id="packaging_invoice" name="packaging_invoice">

                            <div class="card">

                                <div class="">
{{--                                    @if(count($packaging_material_types) > 0)--}}

{{--                                        @php--}}
{{--                                            $packaging_switch = '';--}}
{{--                                        if(count($packaging_charges) > 0){--}}
{{--                                            $packaging_switch = 'checked';--}}
{{--                                             }else{--}}
{{--                                            $packaging_switch = '';--}}
{{--                                            }--}}
{{--                                        @endphp--}}
{{--                                        <div class="card-header border-primary">--}}
{{--                                            <div class="row">--}}
{{--                                                <div class="col-6"><h3 class="card-title lead primary">Packaging Material Charges</h3></div>--}}
{{--                                                <div class="col-6"><a href="javascript:void(0);" class="pull-right" id="packaging_main_switch"><input type="checkbox" name="packaging_switch" class="switchery pull-right packagingChargesSwitch" data-color="info" data-size="sm" {{$packaging_switch}}/></a></div>--}}

{{--                                            </div>--}}
{{--                                        </div>--}}

{{--                                        <div id="packaging_material_charges_div" class="card border-primary p-1 {{$packaging_switch == 'checked'? '':'hide'}}">--}}
{{--                                            @foreach($packaging_material_types as $index => $type)--}}
{{--                                                @php--}}
{{--                                                    $packaging_type_switch = '';--}}
{{--                                                    if(in_array($type->id, $packaging_type_ids)){--}}
{{--                                                        $packaging_type_switch = 'checked';--}}
{{--                                                    }--}}
{{--                                                @endphp--}}
{{--                                                <div class="card-header border-primary">--}}
{{--                                                    <div class="row">--}}
{{--                                                        <div class="col-6"><h4 class="card-title lead primary">{{$type->type}}</h4></div>--}}
{{--                                                        <div class="col-6"><a href="javascript:void(0);" class="pull-right"><a href="javascript:void(0);" class="pull-right" id="packaging_type_{{$type->id}}"><input name="packaging_type_{{$type->id}}" type="checkbox"  class="switchery packaging_type_{{$type->id}}" data-color="info" data-size="sm" {{$packaging_type_switch}}/></a></a></div>--}}

{{--                                                    </div>--}}
{{--                                                </div>--}}

{{--                                                <div id="package_type_{{$type->id}}" class="card border-primary {{$packaging_type_switch == 'checked'? '':'hide'}}" aria-expanded="true">--}}
{{--                                                    <div class="card-content">--}}
{{--                                                        <div class="card-body packaging-charges-div">--}}
{{--                                                            <div class="row">--}}
{{--                                                                @foreach($type->sizes as $ind => $size)--}}
{{--                                                                    @if(isset($packaging_charges[$type->id][$ind]) && $packaging_charges[$type->id][$ind]->size_id == $size->id)--}}
{{--                                                                        <div class="col-md-3 text-center">--}}
{{--                                                                            <label class="card-title">{{$size->size}}</label>--}}
{{--                                                                            <fieldset class="form-group">--}}
{{--                                                                                <input name="packaging_material_size[{{$size->id}}]" type="text" class="form-control @if ((isset($e_packaging_charges[$type->id][$ind]) && isset($packaging_charges[$type->id][$ind]) && $e_packaging_charges[$type->id][$ind]->size_id == $size->id) && ($e_packaging_charges[$type->id][$ind]->charges != $packaging_charges[$type->id][$ind]->charges)) changed @endif amount" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_packaging_charges[$type->id][$ind]) && isset($packaging_charges[$type->id][$ind]) && $e_packaging_charges[$type->id][$ind]->size_id == $size->id) && ($e_packaging_charges[$type->id][$ind]->charges != $packaging_charges[$type->id][$ind]->charges)) {{$e_packaging_charges[$type->id][$ind]->charges}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$packaging_charges[$type->id][$ind]->charges}}">--}}
{{--                                                                            </fieldset>--}}
{{--                                                                        </div>--}}
{{--                                                                    @else--}}
{{--                                                                        <div class="col-md-3 text-center">--}}
{{--                                                                            <label class="card-title">{{$size->size}}</label>--}}
{{--                                                                            <fieldset class="form-group">--}}
{{--                                                                                <input name="packaging_material_size[{{$size->id}}]" type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="{{$size->standard_charges}}">--}}
{{--                                                                            </fieldset>--}}
{{--                                                                        </div>--}}
{{--                                                                    @endif--}}

{{--                                                                @endforeach--}}
{{--                                                            </div>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}

{{--                                                </div>--}}
{{--                                            @endforeach--}}
{{--                                        </div>--}}


{{--                                    @endif--}}

                                </div>

                            </div>
                            <div id="" class="card-header border-success">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3 class="display-inline card-title lead success">Rush</h3>
                                        @if($sale_person['admin_id'] == Auth::id() || session('role_id') == 1)
                                            <label class="display-inline ml-1">Make Default</label>
                                            @if($shipper['default_shipping_mode'] == 1)
                                                <input type="checkbox" name="on_default" id="on_default" class="switchery on_default" checked data-size="xs" data-switchery="true">
                                            @else
                                                <input type="checkbox" name="on_default" id="on_default" class="switchery on_default" data-size="xs" data-switchery="true">
                                            @endif
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <a href="javascript:void(0);" class="pull-right" id="on_main_switch"><input name="on_main_switch" type="checkbox"  class="switchery on-main-switch" data-size="sm" {{ ((isset($switches[1][0]) && $switches[1][0]->status == 1) ? 'checked' : '') }}/></a>
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
                                                <h3>Destination Cities</h3>
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
                                                        @if(isset($min_weight[1]))
                                                        @foreach($min_weight[1] as $index => $mweight)
                                                            @if($mweight->delivery_type_id == 1)
                                                                    <input type="hidden" name="overnight_door_min_chargeable_weight" value="{{$mweight->id}}">
                                                                    <input type="text" class="form-control @if ((isset($e_min_weight[1][$index]) && $e_min_weight[1][$index]->delivery_type_id == 1) && ($e_min_weight[1][$index]->min_chargeable_weight != $mweight->min_chargeable_weight)) changed @endif decimal" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_min_weight[1][$index]) && $e_min_weight[1][$index]->delivery_type_id == 1) && ($e_min_weight[1][$index]->min_chargeable_weight != $mweight->min_chargeable_weight)) {{$e_min_weight[1][$index]->min_chargeable_weight}} @endif" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="{{$mweight->min_chargeable_weight}}" name="on_door_mcw_charges" placeholder="Minimum Chargeable Weight">
                                                            @endif
                                                        @endforeach
                                                        @else
                                                            <input type="hidden" name="overnight_door_min_chargeable_weight" value="">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="" name="on_door_mcw_charges" placeholder="Minimum Chargeable Weight">
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
                                            @if(isset($weight[1]))
                                                @php
                                                    $index_row = 1;
                                                    $on_index_row = 0;
                                                @endphp
                                            @foreach($weight[1] as $index => $onweight)
                                                @if($onweight->delivery_type_id == 1)
                                                <div class="row on_door_weight_row" id="on_door_weight_row{{$index_row}}">
                                                    <input type="hidden" name="on_door_weight_record[{{$index_row}}]" value="{{$onweight->id}}">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 1) && ($e_weight[1][$on_index_row]->range_up != $onweight->range_up)) changed @elseif(((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 2) || !isset($e_weight[1][$on_index_row])) && $existing == 1) new @endif decimal weight_range" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 1) && ($e_weight[1][$on_index_row]->range_up != $onweight->range_up)) {{$e_weight[1][$on_index_row]->range_up}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->range_up}}" name="on_door_range_up[{{$index_row}}]" {{ (($index_row == 0 || $index_row == 1) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 1) && ($e_weight[1][$on_index_row]->range_down != $onweight->range_down)) changed @elseif(((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 2) || !isset($e_weight[1][$on_index_row])) && $existing == 1) new @endif decimal weight_range" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 1) && ($e_weight[1][$on_index_row]->range_down != $onweight->range_down)) {{$e_weight[1][$on_index_row]->range_down}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->range_down}}" name="on_door_range_down[{{$index_row}}]" {{ (($index_row == 0) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="OvernightDoorSwitch{{$index_row}}" class="switchery weightAdditionDoorOvernight" data-color="success" data-size="sm" name="on_door_wa_switch[{{$index_row}}]" {{ ($onweight->base == 1) ? 'checked' : '' }}/>
                                                        </div>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 1) && ($e_weight[1][$on_index_row]->local_or_6hr != $onweight->local_or_6hr)) changed @elseif(((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 2) || !isset($e_weight[1][$on_index_row])) && $existing == 1) new @endif  numeric" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title=" @if ((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 1) && ($e_weight[1][$on_index_row]->local_or_6hr != $onweight->local_or_6hr)) {{$e_weight[1][$on_index_row]->local_or_6hr}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->local_or_6hr}}" name="on_door_local_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 1) && ($e_weight[1][$on_index_row]->national_charges_class_0 != $onweight->national_charges_class_0)) changed @elseif(((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 2) || !isset($e_weight[1][$on_index_row])) && $existing == 1) new @endif  numeric" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title=" @if ((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 1) && ($e_weight[1][$on_index_row]->national_charges_class_0 != $onweight->national_charges_class_0)) {{$e_weight[1][$on_index_row]->national_charges_class_0}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->national_charges_class_0}}" name="on_door_class_0_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 1) && ($e_weight[1][$on_index_row]->national_charges_class_1 != $onweight->national_charges_class_1)) changed @elseif(((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 2) || !isset($e_weight[1][$on_index_row])) && $existing == 1) new @endif  dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 1) && ($e_weight[1][$on_index_row]->national_charges_class_1 != $onweight->national_charges_class_1)) {{$e_weight[1][$on_index_row]->national_charges_class_1}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->national_charges_class_1}}" name="on_door_class_1_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 1) && ($e_weight[1][$on_index_row]->national_charges_class_2 != $onweight->national_charges_class_2)) changed @elseif(((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 2) || !isset($e_weight[1][$on_index_row])) && $existing == 1) new @endif  dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 1) && ($e_weight[1][$on_index_row]->national_charges_class_2 != $onweight->national_charges_class_2)) {{$e_weight[1][$on_index_row]->national_charges_class_2}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->national_charges_class_2}}" name="on_door_class_2_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 1) && ($e_weight[1][$on_index_row]->national_charges_class_3 != $onweight->national_charges_class_3)) changed @elseif(((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 2) || !isset($e_weight[1][$on_index_row])) && $existing == 1) new @endif  dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 1) && ($e_weight[1][$on_index_row]->national_charges_class_3 != $onweight->national_charges_class_3)) {{$e_weight[1][$on_index_row]->national_charges_class_3}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->national_charges_class_3}}" name="on_door_class_3_charges[{{$index_row}}]">
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
                                                            if(isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 1){
                                                                $on_index_row++;
                                                            }
                                                        @endphp
                                                    @endif
                                            @endforeach
                                                @else
                                                <div class="row on_door_weight_row" id="on_door_weight_row0">
                                                    <input type="hidden" name="on_door_weight_record[1]" value="">

                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="0.01" name="on_door_range_up[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="" name="on_door_range_down[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="OvernightDoorSwitch0" class="switchery weightAdditionDoorOvernight" data-color="success" data-size="sm" name="on_door_wa_switch[1]"/>
                                                        </div>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="" name="on_door_local_charges[1]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="" name="on_door_class_0_charges[1]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="on_door_class_1_charges[1]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="on_door_class_2_charges[1]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="on_door_class_3_charges[1]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-1">
                                                    </div>

                                                </div>
                                            @endif

                                        </div>
                                        <div class="mb-2">
                                            <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="waddition_btn"><i class="la la-plus"></i></button>
                                        </div>
                                        <div class="hub-weight-addition-overnight">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Hub-Hub Delivery</h3>
                                                </div>
                                                <div class="col-2">
                                                    <a href="javascript:void(0);" class="pull-right" id="on_hub_to_hub_switch"><input name="on_hub_to_hub_switch" type="checkbox"  class="switchery on_hub_to_hub_switch" data-size="sm" @if(isset($hub_delivery_type_status[1])) checked @endif/></a>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <fieldset class="form-group">
                                                        @if(isset($min_weight[1]) && isset($hub_delivery_type_status[1]))
                                                        @foreach($min_weight[1] as $index => $mweight)
                                                            @if($mweight->delivery_type_id == 2)
                                                                    <input type="hidden" name="overnight_hub_min_chargeable_weight" value="{{$mweight->id}}">
                                                                    <input type="text" class="form-control @if ((isset($e_min_weight[1][$index]) && $e_min_weight[1][$index]->delivery_type_id == 2) && (isset($e_min_weight[1][$index]) && $e_min_weight[1][$index]->min_chargeable_weight != $mweight->min_chargeable_weight)) changed @endif decimal" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_min_weight[1][$index]) && $e_min_weight[1][$index]->delivery_type_id == 2) && (isset($e_min_weight[1][$index]) && $e_min_weight[1][$index]->min_chargeable_weight != $mweight->min_chargeable_weight)) {{$e_min_weight[1][$index]->min_chargeable_weight}} @endif" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="{{$mweight->min_chargeable_weight}}" name="on_hub_mcw_charges" placeholder="Minimum Chargeable Weight">
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
                                            @if(isset($weight[1]) && isset($hub_delivery_type_status[1]))
                                                @php
                                                    $index_row = 1;
                                                @endphp
                                            @foreach($weight[1] as $index => $onweight)
                                                @if($onweight->delivery_type_id == 2)
                                                    <div class="row on_hub_weight_row" id="on_hub_weight_row{{$index_row}}">
                                                        <input type="hidden" name="on_hub_weight_record[{{$index_row}}]" value="{{$onweight->id}}">

                                                        <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 2) && ($e_weight[1][$on_index_row]->range_up != $onweight->range_up)) changed @elseif(((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 1) || !isset($e_weight[1][$on_index_row])) && $existing == 1) new @endif decimal weight_range" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 2) && ($e_weight[1][$on_index_row]->range_up != $onweight->range_up)) {{$e_weight[1][$on_index_row]->range_up}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->range_up}}" name="on_hub_range_up[{{$index_row}}]" {{ (($index_row == 0 || $index_row == 1) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 2) && ($e_weight[1][$on_index_row]->range_down != $onweight->range_down)) changed @elseif(((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 1) || !isset($e_weight[1][$on_index_row])) && $existing == 1) new @endif decimal weight_range" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 2) && ($e_weight[1][$on_index_row]->range_down != $onweight->range_down)) {{$e_weight[1][$on_index_row]->range_down}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->range_down}}" name="on_hub_range_down[{{$index_row}}]" {{ (($index_row == 0) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="OvernightHubSwitch{{$index_row}}" class="switchery weightAdditionHubOvernight" data-color="success" data-size="sm" name="on_hub_wa_switch[{{$index_row}}]" {{ ($onweight->base == 1) ? 'checked' : '' }}/>
                                                        </div>
                                                    </div>

                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 2) && ($e_weight[1][$on_index_row]->local_or_6hr != $onweight->local_or_6hr)) changed @elseif((!isset($e_weight[1][$on_index_row])) && $existing == 1) new @endif numeric" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 2) && ($e_weight[1][$on_index_row]->local_or_6hr != $onweight->local_or_6hr)) {{$e_weight[1][$on_index_row]->local_or_6hr}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->local_or_6hr}}" name="on_hub_local_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 2) && ($e_weight[1][$on_index_row]->national_charges_class_0 != $onweight->national_charges_class_0)) changed @elseif((!isset($e_weight[1][$on_index_row])) && $existing == 1) new @endif numeric" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 2) && ($e_weight[1][$on_index_row]->national_charges_class_0 != $onweight->national_charges_class_0)) {{$e_weight[1][$on_index_row]->national_charges_class_0}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->national_charges_class_0}}" name="on_hub_class_0_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 2) && ($e_weight[1][$on_index_row]->national_charges_class_1 != $onweight->national_charges_class_1)) changed @elseif((!isset($e_weight[1][$on_index_row])) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 2) && ($e_weight[1][$on_index_row]->national_charges_class_1 != $onweight->national_charges_class_1)) {{$e_weight[1][$on_index_row]->national_charges_class_1}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->national_charges_class_1}}" name="on_hub_class_1_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 2) && ($e_weight[1][$on_index_row]->national_charges_class_2 != $onweight->national_charges_class_2)) changed @elseif((!isset($e_weight[1][$on_index_row])) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 2) && ($e_weight[1][$on_index_row]->national_charges_class_2 != $onweight->national_charges_class_2)) {{$e_weight[1][$on_index_row]->national_charges_class_2}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->national_charges_class_2}}" name="on_hub_class_2_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 2) && ($e_weight[1][$on_index_row]->national_charges_class_3 != $onweight->national_charges_class_3)) changed @elseif((!isset($e_weight[1][$on_index_row])) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 2) && ($e_weight[1][$on_index_row]->national_charges_class_3 != $onweight->national_charges_class_3)) {{$e_weight[1][$on_index_row]->national_charges_class_3}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$onweight->national_charges_class_3}}" name="on_hub_class_3_charges[{{$index_row}}]">
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
                                                            if(isset($e_weight[1][$on_index_row]) && $e_weight[1][$on_index_row]->delivery_type_id == 2){
                                                                $on_index_row++;
                                                            }
                                                        @endphp
                                                    @endif
                                            @endforeach
                                                @else
                                                <div class="row on_hub_weight_row" id="on_hub_weight_row0">
                                                    <input type="hidden" name="on_hub_weight_record[1]" value="">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="0.01" name="on_hub_range_up[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="" name="on_hub_range_down[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="OvernightHubSwitch0" class="switchery weightAdditionHubOvernight" data-color="success" data-size="sm" name="on_hub_wa_switch[1]"/>
                                                        </div>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="" name="on_hub_local_charges[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="" name="on_hub_class_0_charges[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="on_hub_class_1_charges[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="on_hub_class_2_charges[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="on_hub_class_3_charges[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-1">
                                                    </div>

                                                </div>
                                            @endif
                                        </div>{{--weight addition div--}}
                                        <div>
                                            <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="hub_waddition_btn"><i class="la la-plus"></i></button>
                                        </div>
                                        <div class="row mt-2">
                                            <input type="hidden" name="on_booking_record" value="{{ (isset($shippingType[1][0]) && $shippingType[1][0]->id != '')? $shippingType[1][0]->id : ''}}">
                                            {{--{{dd($e_shippingType[1])}}--}}
                                            <div class="col-md-3 text-center">
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" >Replacement</span>
                                                        </div>
                                                        <input type="text" class="form-control @if(isset($e_shippingType[1][0]) && isset($shippingType[1][0]) && $e_shippingType[1][0]->replacement_charges != $shippingType[1][0]->replacement_charges) changed @elseif(!isset($e_shippingType[1][0]) && $existing == 1) @if(isset($shippingType[1][0])) new @endif @endif percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_shippingType[1][0]) && isset($shippingType[1][0]) && $e_shippingType[1][0]->replacement_charges != $shippingType[1][0]->replacement_charges) {{$e_shippingType[1][0]->replacement_charges}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($shippingType[1][0]) && $shippingType[1][0]->replacement_charges != '')? $shippingType[1][0]->replacement_charges : ''}}" name="on_replacement_charges">
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
                                                        <input type="text" class="form-control @if(isset($e_shippingType[1][0]) && isset($shippingType[1][0]) && $e_shippingType[1][0]->try_and_buy_charges != $shippingType[1][0]->try_and_buy_charges) changed @elseif(!isset($e_shippingType[1][0]) && $existing == 1) @if(isset($shippingType[1][0])) new @endif @endif percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_shippingType[1][0]) && isset($shippingType[1][0]) && $e_shippingType[1][0]->try_and_buy_charges != $shippingType[1][0]->try_and_buy_charges) {{$e_shippingType[1][0]->try_and_buy_charges}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($shippingType[1][0]) && $shippingType[1][0]->try_and_buy_charges != '')? $shippingType[1][0]->try_and_buy_charges : ''}}" name="on_tnb_charges">
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
                                                                <input type="checkbox" name="on_dws" id="on_dws" class="switchery on_dws" {{ (($on_dws_charges != null) ? 'checked' : '') }} data-size="xs" data-switchery="true">
                                                            </div>
                                                        </fieldset>
                                                    </div> 
                                                    <div class="col-4">
                                                        <fieldset>
                                                            <div class="input-group form-group">
                                                                <select name="on_dws_weight" id="on_dws_weight" class="form-control" {{ (($on_dws_charges != null) ? '' : 'disabled') }} >
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
                                                                <input name="on_cash_range_up[{{$index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control @if(isset($e_cashHandling[1][$index]) && $e_cashHandling[1][$index]->range_up != $cash->range_up) changed @elseif(!isset($e_cashHandling[1][$index]) && $existing == 1) new @endif numeric" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_cashHandling[1][$index]) && $e_cashHandling[1][$index]->range_up != $cash->range_up) {{$e_cashHandling[1][$index]->range_up}} @endif" value="{{$cash->range_up}}" {{$on_cash_sw}}>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-md-2 text-center">
                                                            <fieldset class="form-group">
                                                                <input name="on_cash_range_down[{{$index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control @if(isset($e_cashHandling[1][$index]) && $e_cashHandling[1][$index]->range_down != $cash->range_down) changed @elseif(!isset($e_cashHandling[1][$index]) && $existing == 1) new @endif numeric" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_cashHandling[1][$index]) && $e_cashHandling[1][$index]->range_down != $cash->range_down) {{$e_cashHandling[1][$index]->range_down}} @endif" value="{{$cash->range_down}}" {{$on_cash_sw}}>
                                                            </fieldset>
                                                        </div>

                                                        <div class="col-md-2 text-center">
                                                            <fieldset class="form-group">
                                                                <input name="on_cash_charges[{{$index}}]" type="text" class="form-control @if(isset($e_cashHandling[1][$index]) && $e_cashHandling[1][$index]->charges != $cash->charges) changed @elseif(!isset($e_cashHandling[1][$index]) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_cashHandling[1][$index]) && $e_cashHandling[1][$index]->charges != $cash->charges) {{$e_cashHandling[1][$index]->charges}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->charges}}" {{$on_cash_sw}}>
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
                                            <button id="addMoreSlabs" type="button" class="btn btn-outline-success mr-1" title="Add more slabs" ><i class="la la-plus"></i></button>
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
                                                                <input name="on_ins_range_up[{{$index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control @if(isset($e_insuranceCharges[1][$index]) && $e_insuranceCharges[1][$index]->range_up != $insurance->range_up) changed @elseif(!isset($e_insuranceCharges[1][$index]) && $existing == 1) new @endif numeric" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_insuranceCharges[1][$index]) && $e_insuranceCharges[1][$index]->range_up != $insurance->range_up) {{$e_insuranceCharges[1][$index]->range_up}} @endif" value="{{$insurance->range_up}}" {{$on_ins_sw}}>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-md-2 text-center">
                                                            <fieldset class="form-group">
                                                                <input name="on_ins_range_down[{{$index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control @if(isset($e_insuranceCharges[1][$index]) && $e_insuranceCharges[1][$index]->range_down != $insurance->range_down) changed @elseif(!isset($e_insuranceCharges[1][$index]) && $existing == 1) new @endif numeric" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_insuranceCharges[1][$index]) && $e_insuranceCharges[1][$index]->range_down != $insurance->range_down) {{$e_insuranceCharges[1][$index]->range_down}} @endif" value="{{$insurance->range_down}}" {{$on_ins_sw}}>
                                                            </fieldset>
                                                        </div>

                                                        <div class="col-md-2 text-center">
                                                            <fieldset class="form-group">
                                                                <input name="on_ins_charges[{{$index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control @if(isset($e_insuranceCharges[1][$index]) && $e_insuranceCharges[1][$index]->charges != $insurance->charges) changed @elseif(!isset($e_insuranceCharges[1][$index]) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_insuranceCharges[1][$index]) && $e_insuranceCharges[1][$index]->charges != $insurance->charges) {{$e_insuranceCharges[1][$index]->charges}} @endif" value="{{$insurance->charges}}" {{$on_ins_sw}}>
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
                                            <button id="addMoreSlabsInsurance" type="button" class="btn btn-outline-success mr-1" title="Add more slabs"><i class="la la-plus"></i></button>
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
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control @if(isset($e_returnCharges[1][0]) && isset($returnCharges[1][0]) && $e_returnCharges[1][0]->local != $returnCharges[1][0]->local) changed @elseif(!isset($e_returnCharges[1][0]) && $existing == 1) new @endif amount" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_returnCharges[1][0]) && isset($returnCharges[1][0]) && $e_returnCharges[1][0]->local != $returnCharges[1][0]->local) {{$e_returnCharges[1][0]->local}} @endif" name="on_return_local_charges"  value="{{ (isset($returnCharges[1][0]) && $returnCharges[1][0]->local !== '')? $returnCharges[1][0]->local : ''}}" {{$on_return_sw}}>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <label class="card-title">National Charges Class A</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control @if(isset($e_returnCharges[1][0]) && isset($returnCharges[1][0]) && $e_returnCharges[1][0]->national_charges_class_0 != $returnCharges[1][0]->national_charges_class_0) changed @elseif(!isset($e_returnCharges[1][0]) && $existing == 1) new @endif amount" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_returnCharges[1][0]) && isset($returnCharges[1][0]) && $e_returnCharges[1][0]->national_charges_class_0 != $returnCharges[1][0]->national_charges_class_0) {{$e_returnCharges[1][0]->national_charges_class_0}} @endif" name="on_return_class_0_charges"  value="{{ (isset($returnCharges[1][0]) && $returnCharges[1][0]->national_charges_class_0 !== '')? $returnCharges[1][0]->national_charges_class_0 : ''}}" {{$on_return_sw}}>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <label class="card-title">National Charges Class B</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control @if(isset($e_returnCharges[1][0]) && isset($returnCharges[1][0]) && $e_returnCharges[1][0]->national_charges_class_1 != $returnCharges[1][0]->national_charges_class_1) changed @elseif(!isset($e_returnCharges[1][0]) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_returnCharges[1][0]) && isset($returnCharges[1][0]) && $e_returnCharges[1][0]->national_charges_class_1 != $returnCharges[1][0]->national_charges_class_1) {{$e_returnCharges[1][0]->national_charges_class_1}} @endif" name="on_return_class_1_charges"  value="{{ (isset($returnCharges[1][0]) && $returnCharges[1][0]->national_charges_class_1 !== '')? $returnCharges[1][0]->national_charges_class_1 : ''}}" {{$on_return_sw}}>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <label class="card-title">National Charges Class C</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control @if(isset($e_returnCharges[1][0]) && isset($returnCharges[1][0]) && $e_returnCharges[1][0]->national_charges_class_2 != $returnCharges[1][0]->national_charges_class_2) changed @elseif(!isset($e_returnCharges[1][0]) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_returnCharges[1][0]) && isset($returnCharges[1][0]) && $e_returnCharges[1][0]->national_charges_class_2 != $returnCharges[1][0]->national_charges_class_2) {{$e_returnCharges[1][0]->national_charges_class_2}} @endif" name="on_return_class_2_charges"  value="{{ (isset($returnCharges[1][0]) && $returnCharges[1][0]->national_charges_class_2 !== '')? $returnCharges[1][0]->national_charges_class_2 : ''}}" {{$on_return_sw}}>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <label class="card-title">National Charges Class D</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control @if(isset($e_returnCharges[1][0]) && isset($returnCharges[1][0]) && $e_returnCharges[1][0]->national_charges_class_3 != $returnCharges[1][0]->national_charges_class_3) changed @elseif(!isset($e_returnCharges[1][0]) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_returnCharges[1][0]) && isset($returnCharges[1][0]) && $e_returnCharges[1][0]->national_charges_class_3 != $returnCharges[1][0]->national_charges_class_3) {{$e_returnCharges[1][0]->national_charges_class_3}} @endif" name="on_return_class_3_charges"  value="{{ (isset($returnCharges[1][0]) && $returnCharges[1][0]->national_charges_class_3 !== '')? $returnCharges[1][0]->national_charges_class_3 : ''}}" {{$on_return_sw}}>
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
                                                    <input type="checkbox" name="overnight_fuel_switch" class="switchery fuelSurchargeOvernight" data-color="success" data-size="sm" {{$on_fuel_switch}}/>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row fuel-surcharge-div-overnight">
                                            <input type="hidden" name="on_fuel_record" value="{{ (isset($fuelCharges[1][0]) && $fuelCharges[1][0]->id != '')? $fuelCharges[1][0]->id : ''}}">
                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Charges</label>
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <input type="text"  class="form-control @if(isset($e_fuelCharges[1][0]) && isset($fuelCharges[1][0]) && $e_fuelCharges[1][0]->fuel_surcharge != $fuelCharges[1][0]->fuel_surcharge) changed @elseif(!isset($e_fuelCharges[1][0]) && $existing == 1) new @endif" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_fuelCharges[1][0]) && isset($fuelCharges[1][0]) && $e_fuelCharges[1][0]->fuel_surcharge != $fuelCharges[1][0]->fuel_surcharge) {{$e_fuelCharges[1][0]->fuel_surcharge}} @endif" name="overnight_fuel_surcharge" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($fuelCharges[1][0]) && $fuelCharges[1][0]->fuel_surcharge != '')? $fuelCharges[1][0]->fuel_surcharge : ''}}" {{$on_fuel_sw}}>
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
                                                    if((isset($e_discountCharges[1][0]) && $e_discountCharges[1][0]->title != '')){
                                                    $e_on_discount_title = $e_discountCharges[1][0]->title;
                                                     }else{
                                                    $e_on_discount_title = '';
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
                                                    <input type='text' class="form-control @if(isset($e_discountCharges[1][0]) && $on_discount_title != $e_on_discount_title) changed @elseif(!isset($e_discountCharges[1][0]) && $existing == 1) new @endif" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_discountCharges[1][0]) && $on_discount_title != $e_on_discount_title) {{$e_on_discount_title}} @endif" data-rule-required="true" data-msg-required="This field is required" {{$on_discount_title_switch}} name="on_discount_title" value="{{$on_discount_title}}"/>
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
                                            if((isset($e_discountCharges[1][0]) && $e_discountCharges[1][0]->to != '')){
                                                $e_to = date('m/d/Y', strtotime($e_discountCharges[1][0]->to));
                                                $e_from = date('m/d/Y', strtotime($e_discountCharges[1][0]->from));

                                                $e_on_discount_daterange = $e_to.' - '.$e_from;

                                             }else{
                                                $e_on_discount_daterange = '';
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
                                                    <input type='text' class="form-control @if(isset($e_discountCharges[1][0]) && $e_on_discount_daterange != $on_discount_daterange) changed @elseif(!isset($e_discountCharges[1][0]) && $existing == 1) new @endif daterange" data-rule-required="true" data-msg-required="This field is required" {{$on_discount_daterange_switch}} name="on_daterange" value="{{$on_discount_daterange}}"/>
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
                                        if((isset($e_discountCharges[1][0]) && $e_discountCharges[1][0]->weight != '')){
                                            $e_on_discount_weight_sw = $e_discountCharges[1][0]->weight;
                                         }else{
                                            $e_on_discount_weight_sw = '';
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
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control @if(isset($e_discountCharges[1][0]) && $e_on_discount_weight_sw != $on_discount_weight_sw) changed @elseif(!isset($e_discountCharges[1][0]) && $existing == 1) new @endif dec-percent on-discount-inp" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_discountCharges[1][0]) && $e_on_discount_weight_sw != $on_discount_weight_sw) {{$e_on_discount_weight_sw}} @endif" name="on_discount_weight_rate" value="{{$on_discount_weight_sw}}" {{$on_discount_weight_disable}}>
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
                                            if((isset($e_discountCharges[1][0]) && $e_discountCharges[1][0]->cash != '')){
                                            $e_on_discount_cash_sw = $e_discountCharges[1][0]->cash;
                                             }else{
                                            $e_on_discount_cash_sw = '';
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
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control @if(isset($e_discountCharges[1][0]) && $e_on_discount_cash_sw != $on_discount_cash_sw) changed @elseif(!isset($e_discountCharges[1][0]) && $existing == 1) new @endif dec-percent on-discount-inp" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_discountCharges[1][0]) && $e_on_discount_cash_sw != $on_discount_cash_sw) {{$e_on_discount_cash_sw}} @endif" name="on_discount_cash_rate" value="{{$on_discount_cash_sw}}" {{$on_discount_cash_disable}}>
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
                                            if((isset($e_discountCharges[1][0]) && $e_discountCharges[1][0]->insurance != '')){
                                            $e_on_discount_insurance_sw = $e_discountCharges[1][0]->insurance;
                                             }else{
                                            $e_on_discount_insurance_sw = '';
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
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control @if(isset($e_discountCharges[1][0]) && $e_on_discount_insurance_sw != $on_discount_insurance_sw) changed @elseif(!isset($e_discountCharges[1][0]) && $existing == 1) new @endif dec-percent on-discount-inp" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_discountCharges[1][0]) && $e_on_discount_insurance_sw != $on_discount_insurance_sw) {{$e_on_discount_insurance_sw}} @endif" name="on_discount_insurance_rate" value="{{$on_discount_insurance_sw}}" {{$on_discount_insurance_disable}}>
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
                                            if((isset($e_discountCharges[1][0]) && $e_discountCharges[1][0]->return != '')){
                                            $e_on_discount_return_sw = $e_discountCharges[1][0]->return;
                                             }else{
                                            $e_on_discount_return_sw = '';
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
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control @if(isset($e_discountCharges[1][0]) && $e_on_discount_return_sw != $on_discount_return_sw) changed @elseif(!isset($e_discountCharges[1][0]) && $existing == 1) new @endif dec-percent on-discount-inp" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_discountCharges[1][0]) && $e_on_discount_return_sw != $on_discount_return_sw) {{$e_on_discount_return_sw}} @endif" name="on_discount_return_rate" value="{{$on_discount_return_sw}}" {{$on_discount_return_disable}}>
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
                                        <h3 class="display-inline card-title lead success">Saver Plus</h3>
                                        @if($sale_person['admin_id'] == Auth::id() || session('role_id') == 1)
                                            <label class="display-inline ml-1">Make Default</label>
                                            @if($shipper['default_shipping_mode'] == 2)
                                                <input type="checkbox" name="ol_default" id="ol_default" class="switchery ol_default" checked data-size="xs" data-switchery="true">
                                            @else
                                                <input type="checkbox" name="ol_default" id="ol_default" class="switchery ol_default" data-size="xs" data-switchery="true">
                                            @endif
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <a id="ol_main_switch" href="javascript:void(0);" class="pull-right"><input name="ol_main_switch" type="checkbox" id="" class="switchery ol-main-switch" data-size="sm" {{ ((isset($switches[2][0]) && $switches[2][0]->status == 1) ? 'checked' : '') }}/></a>
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
                                                <h3>Destination Cities</h3>
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
                                                        @if(isset($min_weight[2]))
                                                        @foreach($min_weight[2] as $index => $mweight)
                                                            @if($mweight->delivery_type_id == 1)
                                                                    <input type="hidden" name="overland_door_min_chargeable_weight" value="{{$mweight->id}}">
                                                                    <input type="text" class="form-control @if ((isset($e_min_weight[2][$index]) && $e_min_weight[2][$index]->delivery_type_id == 1) && ($e_min_weight[2][$index]->min_chargeable_weight != $mweight->min_chargeable_weight)) changed @endif decimal" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_min_weight[2][$index]) && $e_min_weight[2][$index]->delivery_type_id == 1) && ($e_min_weight[2][$index]->min_chargeable_weight != $mweight->min_chargeable_weight)) {{$e_min_weight[2][$index]->min_chargeable_weight}} @endif" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="{{$mweight->min_chargeable_weight}}" name="ol_door_mcw_charges" placeholder="Minimum Chargeable Weight">
                                                            @endif
                                                        @endforeach
                                                            @else
                                                            <input type="hidden" name="overland_door_min_chargeable_weight" value="">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="" name="ol_door_mcw_charges" placeholder="Minimum Chargeable Weight">

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
                                            @if(isset($weight[2]))
                                                @php
                                                    $index_row = 1;
                                                    $ol_index_row = 0;
                                                @endphp
                                            @foreach($weight[2] as $index => $olweight)
                                                @if($olweight->delivery_type_id == 1)
                                                <div class="row ol_door_weight_row" id="ol_door_weight_row{{$index_row}}">
                                                    <input type="hidden" name="ol_door_weight_record[{{$index_row}}]" value="{{$olweight->id}}">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 1) && ($e_weight[2][$ol_index_row]->range_up != $olweight->range_up)) changed @elseif(((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 2) || !isset($e_weight[2][$ol_index_row])) && $existing == 1) new @endif decimal weight_range" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 1) && ($e_weight[2][$ol_index_row]->range_up != $olweight->range_up)) {{$e_weight[2][$ol_index_row]->range_up}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->range_up}}" name="ol_door_range_up[{{$index_row}}]" {{ (($index_row == 0 || $index_row == 1) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 1) && ($e_weight[2][$ol_index_row]->range_down != $olweight->range_down)) changed @elseif(((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 2) || !isset($e_weight[2][$ol_index_row])) && $existing == 1) new @endif decimal weight_range" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 1) && ($e_weight[2][$ol_index_row]->range_down != $olweight->range_down)) {{$e_weight[2][$ol_index_row]->range_down}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->range_down}}" name="ol_door_range_down[{{$index_row}}]" {{ (($index_row == 0) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="OverlandDoorSwitch{{$index_row}}" class="switchery weightAdditionDoorOverland" data-color="success" data-size="sm" name="ol_door_wa_switch[{{$index_row}}]" {{ ($olweight->base == 1) ? 'checked' : '' }}/>
                                                        </div>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 1) && ($e_weight[2][$ol_index_row]->local_or_6hr != $olweight->local_or_6hr)) changed @elseif(((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 2) || !isset($e_weight[2][$ol_index_row])) && $existing == 1) new @endif decimal" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 1) && ($e_weight[2][$ol_index_row]->local_or_6hr != $olweight->local_or_6hr)) {{$e_weight[2][$ol_index_row]->local_or_6hr}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->local_or_6hr}}" name="ol_door_local_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 1) && ($e_weight[2][$ol_index_row]->national_charges_class_0 != $olweight->national_charges_class_0)) changed @elseif(((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 2) || !isset($e_weight[2][$ol_index_row])) && $existing == 1) new @endif decimal" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 1) && ($e_weight[2][$ol_index_row]->national_charges_class_0 != $olweight->national_charges_class_0)) {{$e_weight[2][$ol_index_row]->national_charges_class_0}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->national_charges_class_0}}" name="ol_door_class_0_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div> <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 1) && ($e_weight[2][$ol_index_row]->national_charges_class_1 != $olweight->national_charges_class_1)) changed @elseif(((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 2) || !isset($e_weight[2][$ol_index_row])) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 1) && ($e_weight[2][$ol_index_row]->national_charges_class_1 != $olweight->national_charges_class_1)) {{$e_weight[2][$ol_index_row]->national_charges_class_1}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->national_charges_class_1}}" name="ol_door_class_1_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 1) && ($e_weight[2][$ol_index_row]->national_charges_class_2 != $olweight->national_charges_class_2)) changed @elseif(((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 2) || !isset($e_weight[2][$ol_index_row])) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 1) && ($e_weight[2][$ol_index_row]->national_charges_class_2 != $olweight->national_charges_class_2)) {{$e_weight[2][$ol_index_row]->national_charges_class_2}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->national_charges_class_2}}" name="ol_door_class_2_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 1) && ($e_weight[2][$ol_index_row]->national_charges_class_3 != $olweight->national_charges_class_3)) changed @elseif(((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 2) || !isset($e_weight[2][$ol_index_row])) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 1) && ($e_weight[2][$ol_index_row]->national_charges_class_3 != $olweight->national_charges_class_3)) {{$e_weight[2][$ol_index_row]->national_charges_class_3}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->national_charges_class_3}}" name="ol_door_class_3_charges[{{$index_row}}]">
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
                                                            if(isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 1){
                                                                $ol_index_row++;
                                                            }
                                                        @endphp
                                                    @endif
                                            @endforeach
                                                @else
                                                <div class="row ol_door_weight_row" id="ol_door_weight_row1">
                                                    <input type="hidden" name="ol_door_weight_record[1]" value="">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="0.01" name="ol_door_range_up[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_door_range_down[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="OverlandDoorSwitch0" class="switchery weightAdditionDoorOverland" data-color="success" data-size="sm" name="ol_door_wa_switch[1]"/>
                                                        </div>
                                                    </div>

                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_door_local_charges[1]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_door_class_0_charges[1]">
                                                        </fieldset>
                                                    </div> <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_door_class_1_charges[1]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_door_class_2_charges[1]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_door_class_3_charges[1]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-1">
                                                    </div>
                                                </div>{{--Row--}}
                                            @endif
                                        </div>{{--weight addition div--}}
                                        <div class="mb-2">
                                            <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="overland_door_weightadd"><i class="la la-plus"></i></button>
                                        </div>
                                        <div class="hub-weight-addition-overland">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Hub-Hub Delivery</h3>
                                                </div>
                                                <div class="col-2">
                                                    <a href="javascript:void(0);" class="pull-right" id="ol_hub_to_hub_switch"><input name="ol_hub_to_hub_switch" type="checkbox"  class="switchery ol_hub_to_hub_switch" data-size="sm" @if(isset($hub_delivery_type_status[2])) checked @endif/></a>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <fieldset class="form-group">
                                                        @if(isset($min_weight[2]) && isset($hub_delivery_type_status[2]))
                                                        @foreach($min_weight[2] as $index => $mweight)
                                                            @if($mweight->delivery_type_id == 2)
                                                                    <input type="hidden" name="overland_hub_min_chargeable_weight" value="{{$mweight->id}}">
                                                                    <input type="text" class="form-control @if ((isset($e_min_weight[2][$index]) && $e_min_weight[2][$index]->delivery_type_id == 2) && (isset($e_min_weight[2][$index]) && $e_min_weight[2][$index]->min_chargeable_weight != $mweight->min_chargeable_weight)) changed @endif decimal" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_min_weight[2][$index]) && $e_min_weight[2][$index]->delivery_type_id == 2) && (isset($e_min_weight[2][$index]) && $e_min_weight[2][$index]->min_chargeable_weight != $mweight->min_chargeable_weight)) {{$e_min_weight[2][$index]->min_chargeable_weight}} @endif" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="{{$mweight->min_chargeable_weight}}" name="ol_hub_mcw_charges" placeholder="Minimum Chargeable Weight">
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
                                            @if(isset($weight[2]) && isset($hub_delivery_type_status[2]))
                                                @php
                                                    $index_row = 1;
                                                @endphp
                                            @foreach($weight[2] as $index => $olweight)
                                                @if($olweight->delivery_type_id == 2)
                                                <div class="row ol_hub_weight_row" id="ol_hub_weight_row{{$index_row}}">
                                                    <input type="hidden" name="ol_hub_weight_record[{{$index_row}}]" value="{{$olweight->id}}">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 2) && ($e_weight[2][$ol_index_row]->range_up != $olweight->range_up)) changed @elseif(((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 1) || !isset($e_weight[2][$ol_index_row])) && $existing == 1) new @endif decimal weight_range" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 2) && ($e_weight[2][$ol_index_row]->range_up != $olweight->range_up)) {{$e_weight[2][$ol_index_row]->range_up}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->range_up}}" name="ol_hub_range_up[{{$index_row}}]" {{ (($index_row == 0 || $index_row == 1) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 2) && ($e_weight[2][$ol_index_row]->range_down != $olweight->range_down)) changed @elseif(((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 1) || !isset($e_weight[2][$ol_index_row])) && $existing == 1) new @endif decimal weight_range" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 2) && ($e_weight[2][$ol_index_row]->range_down != $olweight->range_down)) {{$e_weight[2][$ol_index_row]->range_down}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->range_down}}" name="ol_hub_range_down[{{$index_row}}]" {{ (($index_row == 0) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="OverlandHubSwitch{{$index_row}}" class="switchery weightAdditionHubOverland" data-color="success" data-size="sm" name="ol_hub_wa_switch[{{$index_row}}]" {{ ($olweight->base == 1) ? 'checked' : '' }}/>
                                                        </div>
                                                    </div>

                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 2) && ($e_weight[2][$ol_index_row]->local_or_6hr != $olweight->local_or_6hr)) changed @elseif(((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 1) || !isset($e_weight[2][$ol_index_row])) && $existing == 1) new @endif amount" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 2) && ($e_weight[2][$ol_index_row]->local_or_6hr != $olweight->local_or_6hr)) {{$e_weight[2][$ol_index_row]->local_or_6hr}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->local_or_6hr}}" name="ol_hub_local_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 2) && ($e_weight[2][$ol_index_row]->national_charges_class_0 != $olweight->national_charges_class_0)) changed @elseif(((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 1) || !isset($e_weight[2][$ol_index_row])) && $existing == 1) new @endif amount" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 2) && ($e_weight[2][$ol_index_row]->national_charges_class_0 != $olweight->national_charges_class_0)) {{$e_weight[2][$ol_index_row]->national_charges_class_0}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->national_charges_class_0}}" name="ol_hub_class_0_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div> <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 2) && ($e_weight[2][$ol_index_row]->national_charges_class_1 != $olweight->national_charges_class_1)) changed @elseif(((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 1) || !isset($e_weight[2][$ol_index_row])) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 2) && ($e_weight[2][$ol_index_row]->national_charges_class_1 != $olweight->national_charges_class_1)) {{$e_weight[2][$ol_index_row]->national_charges_class_1}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->national_charges_class_1}}" name="ol_hub_class_1_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 2) && ($e_weight[2][$ol_index_row]->national_charges_class_2 != $olweight->national_charges_class_2)) changed @elseif(((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 1) || !isset($e_weight[2][$ol_index_row])) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 2) && ($e_weight[2][$ol_index_row]->national_charges_class_2 != $olweight->national_charges_class_2)) {{$e_weight[2][$ol_index_row]->national_charges_class_2}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->national_charges_class_2}}" name="ol_hub_class_2_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 2) && ($e_weight[2][$ol_index_row]->national_charges_class_3 != $olweight->national_charges_class_3)) changed @elseif(((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 1) || !isset($e_weight[2][$ol_index_row])) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[2][$ol_index_row]) && $e_weight[2][$ol_index_row]->delivery_type_id == 2) && ($e_weight[2][$ol_index_row]->national_charges_class_3 != $olweight->national_charges_class_3)) {{$e_weight[2][$ol_index_row]->national_charges_class_3}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$olweight->national_charges_class_3}}" name="ol_hub_class_3_charges[{{$index_row}}]">
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
                                                @else
                                                <div class="row ol_hub_weight_row" id="ol_hub_weight_row1">
                                                    <input type="hidden" name="ol_hub_weight_record[1]" value="">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="0.01" name="ol_hub_range_up[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_hub_range_down[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="OverlandHubSwitch0" class="switchery weightAdditionHubOverland" data-color="success" data-size="sm" name="ol_hub_wa_switch[1]"/>
                                                        </div>
                                                    </div>

                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_hub_local_charges[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_hub_class_0_charges[1]" disabled>
                                                        </fieldset>
                                                    </div> <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_hub_class_1_charges[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_hub_class_2_charges[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_hub_class_3_charges[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-1">
                                                    </div>
                                                </div>{{--Row--}}
                                            @endif
                                        </div>{{--weight addition div--}}
                                        <div>
                                            <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="overland_hub_weightadd"><i class="la la-plus"></i></button>
                                        </div>
                                        <div class="row mt-2">
                                            <input type="hidden" name="ol_booking_record" value="{{ (isset($shippingType[2][0]) && $shippingType[2][0]->id != '')? $shippingType[2][0]->id : ''}}">

                                            <div class="col-md-3 text-center">
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Replacement</span>
                                                        </div>
                                                        <input type="text" class="form-control @if(isset($e_shippingType[2][0]) && isset($shippingType[2][0]) && $e_shippingType[2][0]->replacement_charges != $shippingType[2][0]->replacement_charges) changed @elseif(!isset($e_shippingType[2][0]) && $existing == 1) @if(isset($shippingType[2][0])) new @endif @endif percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_shippingType[2][0]) && isset($shippingType[2][0]) && $e_shippingType[2][0]->replacement_charges != $shippingType[2][0]->replacement_charges) {{$e_shippingType[2][0]->replacement_charges}} @endif" data-rule-required="true" data-msg-required="This field is required" name="ol_replacement_charges" value="{{ (isset($shippingType[2][0]) && $shippingType[2][0]->replacement_charges != '')? $shippingType[2][0]->replacement_charges : ''}}">
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
                                                        <input type="text" class="form-control @if(isset($e_shippingType[2][0]) && isset($shippingType[2][0]) && $e_shippingType[2][0]->try_and_buy_charges != $shippingType[2][0]->try_and_buy_charges) changed @elseif(!isset($e_shippingType[2][0]) && $existing == 1) @if(isset($shippingType[2][0])) new @endif @endif percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_shippingType[2][0]) && isset($shippingType[2][0]) && $e_shippingType[2][0]->try_and_buy_charges != $shippingType[2][0]->try_and_buy_charges) {{$e_shippingType[2][0]->try_and_buy_charges}} @endif" data-rule-required="true" data-msg-required="This field is required" name="ol_tnb_charges" value="{{ (isset($shippingType[2][0]) && $shippingType[2][0]->try_and_buy_charges != '')? $shippingType[2][0]->try_and_buy_charges : ''}}">
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
                                                                <input type="checkbox" name="ol_dws" id="ol_dws" class="switchery ol_dws" {{ (($ol_dws_charges != null) ? 'checked' : '') }} data-size="xs" data-switchery="true">
                                                            </div>
                                                        </fieldset>
                                                    </div> 
                                                    <div class="col-4">
                                                        <fieldset>
                                                            <div class="input-group form-group">
                                                                <select name="ol_dws_weight" id="ol_dws_weight" class="form-control"  {{ (($ol_dws_charges != null) ? '' : 'disabled') }}>
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
                                                                <input name="ol_cash_range_up[{{$index}}]" type="text" class="form-control @if(isset($e_cashHandling[2][$index]) && $e_cashHandling[2][$index]->range_up != $cash->range_up) changed @elseif(!isset($e_cashHandling[2][$index]) && $existing == 1) new @endif numeric" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_cashHandling[2][$index]) && $e_cashHandling[2][$index]->range_up != $cash->range_up) {{$e_cashHandling[2][$index]->range_up}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->range_up}}" {{$ol_cash_sw}}>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-md-2 text-center">
                                                            <fieldset class="form-group">
                                                                <input name="ol_cash_range_down[{{$index}}]" type="text" class="form-control @if(isset($e_cashHandling[2][$index]) && $e_cashHandling[2][$index]->range_down != $cash->range_down) changed @elseif(!isset($e_cashHandling[2][$index]) && $existing == 1) new @endif numeric" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_cashHandling[2][$index]) && $e_cashHandling[2][$index]->range_down != $cash->range_down) {{$e_cashHandling[2][$index]->range_down}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->range_down}}" {{$ol_cash_sw}}>
                                                            </fieldset>
                                                        </div>

                                                        <div class="col-md-2 text-center">
                                                            <fieldset class="form-group">
                                                                <input name="ol_cash_charges[{{$index}}]" type="text" class="form-control @if(isset($e_cashHandling[2][$index]) && $e_cashHandling[2][$index]->charges != $cash->charges) changed @elseif(!isset($e_cashHandling[2][$index]) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_cashHandling[2][$index]) && $e_cashHandling[2][$index]->charges != $cash->charges) {{$e_cashHandling[2][$index]->charges}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->charges}}" {{$ol_cash_sw}}>
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
                                                                <input name="ol_ins_range_up[{{$index}}]" type="text" class="form-control @if(isset($e_insuranceCharges[2][$index]) && $e_insuranceCharges[2][$index]->range_up != $ol_insurance->range_up) changed @elseif(!isset($e_insuranceCharges[2][$index]) && $existing == 1) new @endif numeric" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_insuranceCharges[2][$index]) && $e_insuranceCharges[2][$index]->range_up != $ol_insurance->range_up) {{$e_insuranceCharges[2][$index]->range_up}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$ol_insurance->range_up}}" {{$ol_ins_sw}}>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-md-2 text-center">
                                                            <fieldset class="form-group">
                                                                <input name="ol_ins_range_down[{{$index}}]" type="text" class="form-control @if(isset($e_insuranceCharges[2][$index]) && $e_insuranceCharges[2][$index]->range_down != $ol_insurance->range_down) changed @elseif(!isset($e_insuranceCharges[2][$index]) && $existing == 1) new @endif numeric" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_insuranceCharges[2][$index]) && $e_insuranceCharges[2][$index]->range_down != $ol_insurance->range_down) {{$e_insuranceCharges[2][$index]->range_down}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$ol_insurance->range_down}}" {{$ol_ins_sw}}>
                                                            </fieldset>
                                                        </div>

                                                        <div class="col-md-2 text-center">
                                                            <fieldset class="form-group">
                                                                <input name="ol_ins_charges[{{$index}}]" type="text" class="form-control @if(isset($e_insuranceCharges[2][$index]) && $e_insuranceCharges[2][$index]->charges != $ol_insurance->charges) changed @elseif(!isset($e_insuranceCharges[2][$index]) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_insuranceCharges[2][$index]) && $e_insuranceCharges[2][$index]->charges != $ol_insurance->charges) {{$e_insuranceCharges[2][$index]->charges}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$ol_insurance->charges}}" {{$ol_ins_sw}}>
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

                                        <div class="row return-charges-div-overland justify-content-center">
                                            <input type="hidden" name="ol_return_record" value="{{ (isset($returnCharges[2][0]) && $returnCharges[2][0]->id != '')? $returnCharges[2][0]->id : ''}}">

                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Local Charges</label>
                                                <fieldset class="form-group">
                                                    <input type="text" class="form-control @if(isset($e_returnCharges[2][0]) && isset($returnCharges[2][0]) && $e_returnCharges[2][0]->local != $returnCharges[2][0]->local) changed @elseif(!isset($e_returnCharges[2][0]) && $existing == 1) new @endif amount" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_returnCharges[2][0]) && isset($returnCharges[2][0]) && $e_returnCharges[2][0]->local != $returnCharges[2][0]->local) {{$e_returnCharges[2][0]->local}} @endif" name="ol_return_local_charges" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($returnCharges[2][0]) && $returnCharges[2][0]->local !== '')? $returnCharges[2][0]->local : ''}}" {{$ol_return_sw}}>
                                                </fieldset>
                                            </div>

                                            <div class="col text-center">
                                                <label class="card-title">National Charges Class A</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control @if(isset($e_returnCharges[2][0]) && isset($returnCharges[2][0]) && $e_returnCharges[2][0]->national_charges_class_0 != $returnCharges[2][0]->national_charges_class_0) changed @elseif(!isset($e_returnCharges[2][0]) && $existing == 1) new @endif amount" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_returnCharges[2][0]) && isset($returnCharges[2][0]) && $e_returnCharges[2][0]->national_charges_class_0 != $returnCharges[2][0]->national_charges_class_0) {{$e_returnCharges[2][0]->national_charges_class_0}} @endif" name="ol_return_class_0_charges"  value="{{ (isset($returnCharges[2][0]) && $returnCharges[2][0]->national_charges_class_0 !== '')? $returnCharges[2][0]->national_charges_class_0 : ''}}" {{$ol_return_sw}}>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <label class="card-title">National Charges Class B</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control @if(isset($e_returnCharges[2][0]) && isset($returnCharges[2][0]) && $e_returnCharges[2][0]->national_charges_class_1 != $returnCharges[2][0]->national_charges_class_1) changed @elseif(!isset($e_returnCharges[2][0]) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_returnCharges[2][0]) && isset($returnCharges[2][0]) && $e_returnCharges[2][0]->national_charges_class_1 != $returnCharges[2][0]->national_charges_class_1) {{$e_returnCharges[2][0]->national_charges_class_1}} @endif" name="ol_return_class_1_charges"  value="{{ (isset($returnCharges[2][0]) && $returnCharges[2][0]->national_charges_class_1 !== '')? $returnCharges[2][0]->national_charges_class_1 : ''}}" {{$ol_return_sw}}>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <label class="card-title">National Charges Class C</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control @if(isset($e_returnCharges[2][0]) && isset($returnCharges[2][0]) && $e_returnCharges[2][0]->national_charges_class_2 != $returnCharges[2][0]->national_charges_class_2) changed @elseif(!isset($e_returnCharges[2][0]) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_returnCharges[2][0]) && isset($returnCharges[2][0]) && $e_returnCharges[2][0]->national_charges_class_2 != $returnCharges[2][0]->national_charges_class_2) {{$e_returnCharges[2][0]->national_charges_class_2}} @endif" name="ol_return_class_2_charges"  value="{{ (isset($returnCharges[2][0]) && $returnCharges[2][0]->national_charges_class_2 !== '')? $returnCharges[2][0]->national_charges_class_2 : ''}}" {{$ol_return_sw}}>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <label class="card-title">National Charges Class D</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control @if(isset($e_returnCharges[2][0]) && isset($returnCharges[2][0]) && $e_returnCharges[2][0]->national_charges_class_3 != $returnCharges[2][0]->national_charges_class_3) changed @elseif(!isset($e_returnCharges[2][0]) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_returnCharges[2][0]) && isset($returnCharges[2][0]) && $e_returnCharges[2][0]->national_charges_class_3 != $returnCharges[2][0]->national_charges_class_3) {{$e_returnCharges[2][0]->national_charges_class_3}} @endif" name="ol_return_class_3_charges" value="{{ (isset($returnCharges[2][0]) && $returnCharges[2][0]->national_charges_class_3 !== '')? $returnCharges[2][0]->national_charges_class_3 : ''}}" {{$ol_return_sw}}>
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
                                                        <input type="text"  class="form-control @if(isset($e_fuelCharges[2][0]) && isset($fuelCharges[2][0]) && $e_fuelCharges[2][0]->fuel_surcharge != $fuelCharges[2][0]->fuel_surcharge) changed @elseif(!isset($e_fuelCharges[2][0]) && $existing == 1) new @endif" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_fuelCharges[2][0]) && isset($fuelCharges[2][0]) && $e_fuelCharges[2][0]->fuel_surcharge != $fuelCharges[2][0]->fuel_surcharge) {{$e_fuelCharges[2][0]->fuel_surcharge}} @endif" name="overland_fuel_surcharge" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($fuelCharges[2][0]) && $fuelCharges[2][0]->fuel_surcharge != '')? $fuelCharges[2][0]->fuel_surcharge : ''}}" {{$ol_fuel_sw}}>
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
                                                if((isset($e_discountCharges[2][0]) && $e_discountCharges[2][0]->title != '')){
                                                $e_ol_discount_title = $e_discountCharges[2][0]->title;
                                                 }else{
                                                $e_ol_discount_title = '';
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
                                                    <input type='text' class="form-control @if(isset($e_discountCharges[2][0]) && $ol_discount_title != $e_ol_discount_title) changed @elseif(!isset($e_discountCharges[2][0]) && $existing == 1) new @endif" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_discountCharges[2][0]) && $ol_discount_title != $e_ol_discount_title) {{$e_ol_discount_title}} @endif" data-rule-required="true" data-msg-required="This field is required" {{$ol_discount_title_switch}} name="ol_discount_title" value="{{$ol_discount_title}}" />
                                                </div>

                                            </div>
                                            @php
                                                $ol_discount_daterange = '';
                                                $e_ol_discount_daterange = '';
                                                $ol_discount_daterange_switch = '';
                                            if((isset($discountCharges[2][0]) && $discountCharges[2][0]->daterange != '')){

                                            $to = date('m/d/Y', strtotime($discountCharges[2][0]->to));
                                            $from = date('m/d/Y', strtotime($discountCharges[2][0]->from));

                                            $ol_discount_daterange = $to.' - '.$from;

                                             }else{
                                            $ol_discount_daterange = '';

                                            }

                                            if((isset($e_discountCharges[2][0]) && $e_discountCharges[2][0]->daterange != '')){

                                            $e_to = date('m/d/Y', strtotime($e_discountCharges[2][0]->to));
                                            $e_from = date('m/d/Y', strtotime($e_discountCharges[2][0]->from));

                                            $e_ol_discount_daterange = $e_to.' - '.$e_from;

                                             }else{
                                            $e_ol_discount_daterange = '';

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
                                                    <input type='text' class="form-control @if(isset($e_discountCharges[2][0]) && $e_ol_discount_daterange != $ol_discount_daterange) changed @elseif(!isset($e_discountCharges[2][0]) && $existing == 1) new @endif daterange" data-rule-required="true" data-msg-required="This field is required" {{$ol_discount_daterange_switch}} name="ol_daterange" {{$ol_discount_daterange_switch}} value="{{$ol_discount_daterange}}" />
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
                                            $e_ol_discount_weight_sw = '';
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
                                        if((isset($e_discountCharges[2][0]) && $e_discountCharges[2][0]->weight != '')){
                                        $e_ol_discount_weight_sw = $e_discountCharges[2][0]->weight;
                                         }else{
                                        $e_ol_discount_weight_sw = '';
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
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control @if(isset($e_discountCharges[2][0]) && $e_ol_discount_weight_sw != $ol_discount_weight_sw) changed @elseif(!isset($e_discountCharges[2][0]) && $existing == 1) new @endif dec-percent ol-discount-inp" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_discountCharges[2][0]) && $e_ol_discount_weight_sw != $ol_discount_weight_sw) {{$e_ol_discount_weight_sw}} @endif" name="ol_discount_weight_rate" {{$ol_discount_weight_disable}} value="{{$ol_discount_weight_sw}}">
                                                    </div>
                                                </fieldset>
                                            </div>
                                            @php
                                                $ol_discount_cash_sw = '';
                                                $e_ol_discount_cash_sw = '';
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
                                            if((isset($e_discountCharges[2][0]) && $e_discountCharges[2][0]->cash != '')){
                                            $e_ol_discount_cash_sw = $e_discountCharges[2][0]->cash;
                                             }else{
                                            $e_ol_discount_cash_sw = '';
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
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control @if(isset($e_discountCharges[2][0]) && $e_ol_discount_cash_sw != $ol_discount_cash_sw) changed @elseif(!isset($e_discountCharges[2][0]) && $existing == 1) new @endif dec-percent ol-discount-inp" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_discountCharges[2][0]) && $e_ol_discount_cash_sw != $ol_discount_cash_sw) {{$e_ol_discount_cash_sw}} @endif" name="ol_discount_cash_rate" {{$ol_discount_cash_disable}} value="{{$ol_discount_cash_sw}}">
                                                    </div>
                                                </fieldset>
                                            </div>
                                            @php
                                                $ol_discount_insurance_sw = '';
                                                $e_ol_discount_insurance_sw = '';
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
                                            if((isset($e_discountCharges[2][0]) && $e_discountCharges[2][0]->insurance != '')){
                                            $e_ol_discount_insurance_sw = $e_discountCharges[2][0]->insurance;
                                             }else{
                                            $e_ol_discount_insurance_sw = '';
                                            }
                                            @en
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
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control @if(isset($e_discountCharges[2][0]) && $e_ol_discount_insurance_sw != $ol_discount_insurance_sw) changed @elseif(!isset($e_discountCharges[2][0]) && $existing == 1) new @endif dec-percent ol-discount-inp" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title=" @if(isset($e_discountCharges[2][0]) && $e_ol_discount_insurance_sw != $ol_discount_insurance_sw) {{$e_ol_discount_insurance_sw}} @endif" name="ol_discount_insurance_rate" value="{{$ol_discount_insurance_sw}}" {{$ol_discount_insurance_disable}}>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            @php
                                                $ol_discount_return_sw = '';
                                                $e_ol_discount_return_sw = '';
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
                                            if((isset($e_discountCharges[2][0]) && $e_discountCharges[2][0]->return != '')){
                                            $e_ol_discount_return_sw = $e_discountCharges[2][0]->return;
                                             }else{
                                            $e_ol_discount_return_sw = '';
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
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control @if(isset($e_discountCharges[2][0]) && $e_ol_discount_return_sw != $ol_discount_return_sw) changed @elseif(!isset($e_discountCharges[2][0]) && $existing == 1) new @endif dec-percent ol-discount-inp" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_discountCharges[2][0]) && $e_ol_discount_return_sw != $ol_discount_return_sw) {{$e_ol_discount_return_sw}} @endif" name="ol_discount_return_rate" value="{{$ol_discount_return_sw}}" {{$ol_discount_return_disable}}>
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
                                        <h3 class="display-inline card-title lead success">Swift</h3>
                                        @if($sale_person['admin_id'] == Auth::id() || session('role_id') == 1)
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
                                                <h3>Destination Cities</h3>
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
                                                        @if(isset($min_weight[3]))
                                                        @foreach($min_weight[3] as $index => $mweight)
                                                            @if($mweight->delivery_type_id == 1)
                                                                    <input type="hidden" name="detain_door_min_chargeable_weight" value="{{$mweight->id}}">
                                                                    <input type="text" class="form-control @if ((isset($e_min_weight[3][$index]) && $e_min_weight[3][$index]->delivery_type_id == 1) && ($e_min_weight[3][$index]->min_chargeable_weight != $mweight->min_chargeable_weight)) changed @endif decimal" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_min_weight[3][$index]) && $e_min_weight[3][$index]->delivery_type_id == 1) && ($e_min_weight[3][$index]->min_chargeable_weight != $mweight->min_chargeable_weight)) {{$e_min_weight[3][$index]->min_chargeable_weight}} @endif" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="{{$mweight->min_chargeable_weight}}" name="detain_door_mcw_charges" placeholder="Minimum Chargeable Weight">
                                                            @endif
                                                        @endforeach
                                                            @else
                                                            <input type="hidden" name="detain_door_min_chargeable_weight" value="">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="" name="detain_door_mcw_charges" placeholder="Minimum Chargeable Weight">

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
                                            @if(isset($weight[3]))
                                                @php
                                                    $index_row = 1;
                                                    $det_index_row = 0;
                                                @endphp
                                            @foreach($weight[3] as $index => $detweight)
                                                @if($detweight->delivery_type_id == 1)
                                                <div class="row detain_door_weight_row" id="detain_door_weight_row{{$index_row}}">
                                                    <input type="hidden" name="detain_door_weight_record[{{$index_row}}]" value="{{$detweight->id}}">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 1) && ($e_weight[3][$det_index_row]->range_up != $detweight->range_up)) changed @elseif(((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 2) || !isset($e_weight[3][$det_index_row])) && $existing == 1) new @endif decimal weight_range" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 1) && ($e_weight[3][$det_index_row]->range_up != $detweight->range_up)) {{$e_weight[3][$det_index_row]->range_up}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->range_up}}" name="detain_door_range_up[{{$index_row}}]" {{ (($index_row == 0 || $index_row == 1) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 1) && ($e_weight[3][$det_index_row]->range_down != $detweight->range_down)) changed @elseif(((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 2) || !isset($e_weight[3][$det_index_row])) && $existing == 1) new @endif decimal weight_range" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 1) && ($e_weight[3][$det_index_row]->range_down != $detweight->range_down)) {{$e_weight[3][$det_index_row]->range_down}} @endif" data-rule-required="true" data-msg-required="This field is required"  value="{{$detweight->range_down}}" name="detain_door_range_down[{{$index_row}}]" {{ (($index_row == 0) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="DetainDoorSwitch{{$index_row}}" class="switchery weightAdditionDoorDetain" data-color="success" data-size="sm" name="detain_door_wa_switch[{{$index_row}}]" {{ ($detweight->base == 1) ? 'checked' : '' }}/>
                                                        </div>
                                                    </div>

                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 1) && ($e_weight[3][$det_index_row]->local_or_6hr != $detweight->local_or_6hr)) changed @elseif(((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 2) || !isset($e_weight[3][$det_index_row])) && $existing == 1) new @endif decimal" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 1) && ($e_weight[3][$det_index_row]->local_or_6hr != $detweight->local_or_6hr)) {{$e_weight[3][$det_index_row]->local_or_6hr}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->local_or_6hr}}" name="detain_door_local_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 1) && ($e_weight[3][$det_index_row]->national_charges_class_0 != $detweight->national_charges_class_0)) changed @elseif(((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 2) || !isset($e_weight[3][$det_index_row])) && $existing == 1) new @endif decimal" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 1) && ($e_weight[3][$det_index_row]->national_charges_class_0 != $detweight->national_charges_class_0)) {{$e_weight[3][$det_index_row]->national_charges_class_0}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->national_charges_class_0}}" name="detain_door_class_0_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 1) && ($e_weight[3][$det_index_row]->national_charges_class_1 != $detweight->national_charges_class_1)) changed @elseif(((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 2) || !isset($e_weight[3][$det_index_row])) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 1) && ($e_weight[3][$det_index_row]->national_charges_class_1 != $detweight->national_charges_class_1)) {{$e_weight[3][$det_index_row]->national_charges_class_1}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->national_charges_class_1}}" name="detain_door_class_1_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 1) && ($e_weight[3][$det_index_row]->national_charges_class_2 != $detweight->national_charges_class_2)) changed @elseif(((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 2) || !isset($e_weight[3][$det_index_row])) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 1) && ($e_weight[3][$det_index_row]->national_charges_class_2 != $detweight->national_charges_class_2)) {{$e_weight[3][$det_index_row]->national_charges_class_2}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->national_charges_class_2}}" name="detain_door_class_2_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 1) && ($e_weight[3][$det_index_row]->national_charges_class_3 != $detweight->national_charges_class_3)) changed @elseif(((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 2) || !isset($e_weight[3][$det_index_row])) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 1) && ($e_weight[3][$det_index_row]->national_charges_class_3 != $detweight->national_charges_class_3)) {{$e_weight[3][$det_index_row]->national_charges_class_3}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->national_charges_class_3}}" name="detain_door_class_3_charges[{{$index_row}}]">
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
                                                            if(isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 1){
                                                                $det_index_row++;
                                                            }
                                                        @endphp
                                                @endif
                                            @endforeach
                                                @else
                                                <div class="row detain_door_weight_row" id="detain_door_weight_row1">
                                                    <input type="hidden" name="detain_door_weight_record[1]" value="">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="0.01" name="detain_door_range_up[1]"  disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range"data-rule-required="true" data-msg-required="This field is required"  value="" name="detain_door_range_down[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="DetainDoorSwitch0" class="switchery weightAdditionDoorDetain" data-color="success" data-size="sm" name="detain_door_wa_switch[1]"/>
                                                        </div>
                                                    </div>

                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_door_local_charges[1]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_door_class_0_charges[1]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_door_class_1_charges[1]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_door_class_2_charges[1]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_door_class_3_charges[1]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-1">
                                                    </div>

                                                </div>{{--Row--}}
                                            @endif
                                        </div>{{--weight addition div--}}
                                        <div class="mb-2">
                                            <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="detain_door_weightadd"><i class="la la-plus"></i></button>
                                        </div>
                                        <div class="hub-weight-addition-detain">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Hub-Hub Delivery</h3>
                                                </div>
                                                <div class="col-2">
                                                    <a href="javascript:void(0);" class="pull-right" id="detain_hub_to_hub_switch"><input name="detain_hub_to_hub_switch" type="checkbox"  class="switchery detain_hub_to_hub_switch" data-size="sm" @if(isset($hub_delivery_type_status[3])) checked @endif/></a>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <fieldset class="form-group">
                                                        @if(isset($min_weight[3]) && isset($hub_delivery_type_status[3]))
                                                        @foreach($min_weight[3] as $index => $mweight)
                                                            @if($mweight->delivery_type_id == 2)
                                                                    <input type="hidden" name="detain_hub_min_chargeable_weight" value="{{$mweight->id}}">

                                                                    <input type="text" class="form-control @if ((isset($e_min_weight[3][$index]) && $e_min_weight[3][$index]->delivery_type_id == 2) && (isset($e_min_weight[3][$index]) && $e_min_weight[3][$index]->min_chargeable_weight != $mweight->min_chargeable_weight)) changed @endif decimal" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_min_weight[3][$index]) && $e_min_weight[3][$index]->delivery_type_id == 2) && (isset($e_min_weight[3][$index]) && $e_min_weight[3][$index]->min_chargeable_weight != $mweight->min_chargeable_weight)) {{$e_min_weight[3][$index]->min_chargeable_weight}} @endif" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="{{$mweight->min_chargeable_weight}}" name="detain_hub_mcw_charges" placeholder="Minimum Chargeable Weight">
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
                                            @if(isset($weight[3]) && isset($hub_delivery_type_status[3]))
                                                @php
                                                    $index_row = 1;
                                                @endphp
                                            @foreach($weight[3] as $index => $detweight)
                                                @if($detweight->delivery_type_id == 2)
                                                <div class="row detain_hub_weight_row" id="detain_hub_weight_row{{$index_row}}">
                                                    <input type="hidden" name="detain_hub_weight_record[{{$index_row}}]" value="{{$detweight->id}}">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 2) && ($e_weight[3][$det_index_row]->range_up != $detweight->range_up)) changed @elseif(((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 1) || !isset($e_weight[3][$det_index_row])) && $existing == 1) new @endif decimal weight_range" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 2) && ($e_weight[3][$det_index_row]->range_up != $detweight->range_up)) {{$e_weight[3][$det_index_row]->range_up}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->range_up}}" name="detain_hub_range_up[{{$index_row}}]" {{ (($index_row == 0 || $index_row == 1) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 2) && ($e_weight[3][$det_index_row]->range_down != $detweight->range_down)) changed @elseif(((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 1) || !isset($e_weight[3][$det_index_row])) && $existing == 1) new @endif decimal weight_range" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 2) && ($e_weight[3][$det_index_row]->range_down != $detweight->range_down)) {{$e_weight[3][$det_index_row]->range_down}} @endif" data-rule-required="true" data-msg-required="This field is required"  value="{{$detweight->range_down}}" name="detain_hub_range_down[{{$index_row}}]" {{ (($index_row == 0) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="DetainHubSwitch{{$index_row}}" class="switchery weightAdditionHubDetain" data-color="success" data-size="sm" name="detain_hub_wa_switch[{{$index_row}}]" {{ ($detweight->base == 1) ? 'checked' : '' }}/>
                                                        </div>
                                                    </div>

                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 2) && ($e_weight[3][$det_index_row]->local_or_6hr != $detweight->local_or_6hr)) changed @elseif(((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 1) || !isset($e_weight[3][$det_index_row])) && $existing == 1) new @endif decimal" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 2) && ($e_weight[3][$det_index_row]->local_or_6hr != $detweight->local_or_6hr)) {{$e_weight[3][$det_index_row]->local_or_6hr}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->local_or_6hr}}" name="detain_hub_local_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 2) && ($e_weight[3][$det_index_row]->national_charges_class_0 != $detweight->national_charges_class_0)) changed @elseif(((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 1) || !isset($e_weight[3][$det_index_row])) && $existing == 1) new @endif decimal" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 2) && ($e_weight[3][$det_index_row]->national_charges_class_0 != $detweight->national_charges_class_0)) {{$e_weight[3][$det_index_row]->national_charges_class_0}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->national_charges_class_0}}" name="detain_hub_class_0_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 2) && ($e_weight[3][$det_index_row]->national_charges_class_1 != $detweight->national_charges_class_1)) changed @elseif(((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 1) || !isset($e_weight[3][$det_index_row])) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 2) && ($e_weight[3][$det_index_row]->national_charges_class_1 != $detweight->national_charges_class_1)) {{$e_weight[3][$det_index_row]->national_charges_class_1}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->national_charges_class_1}}" name="detain_hub_class_1_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 2) && ($e_weight[3][$det_index_row]->national_charges_class_2 != $detweight->national_charges_class_2)) changed @elseif(((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 1) || !isset($e_weight[3][$det_index_row])) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 2) && ($e_weight[3][$det_index_row]->national_charges_class_2 != $detweight->national_charges_class_2)) {{$e_weight[3][$det_index_row]->national_charges_class_2}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->national_charges_class_2}}" name="detain_hub_class_2_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 2) && ($e_weight[3][$det_index_row]->national_charges_class_3 != $detweight->national_charges_class_3)) changed @elseif(((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 1) || !isset($e_weight[3][$det_index_row])) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 2) && ($e_weight[3][$det_index_row]->national_charges_class_3 != $detweight->national_charges_class_3)) {{$e_weight[3][$det_index_row]->national_charges_class_3}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$detweight->national_charges_class_3}}" name="detain_hub_class_3_charges[{{$index_row}}]">
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
                                                            if(isset($e_weight[3][$det_index_row]) && $e_weight[3][$det_index_row]->delivery_type_id == 2){
                                                                $det_index_row++;
                                                            }
                                                        @endphp
                                                @endif
                                            @endforeach
                                                @else
                                                <div class="row detain_hub_weight_row" id="detain_hub_weight_row1">
                                                    <input type="hidden" name="detain_hub_weight_record[1]" value="">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="0.01" name="detain_hub_range_up[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range"data-rule-required="true" data-msg-required="This field is required"  value="" name="detain_hub_range_down[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="DetainHubSwitch0" class="switchery weightAdditionHubDetain" data-color="success" data-size="sm" name="detain_hub_wa_switch[1]"/>
                                                        </div>
                                                    </div>

                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_hub_local_charges[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_hub_class_0_charges[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_hub_class_1_charges[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_hub_class_2_charges[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_hub_class_3_charges[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-1">
                                                    </div>

                                                </div>{{--Row--}}
                                            @endif
                                        </div>{{--weight addition div--}}
                                        <div class="mb-2">
                                            <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="detain_hub_weightadd"><i class="la la-plus"></i></button>
                                        </div>
                                        <div class="row mt-2">
                                            <input type="hidden" name="detain_booking_record" value="{{ (isset($shippingType[3][0]) && $shippingType[3][0]->id != '')? $shippingType[3][0]->id : ''}}">

                                            <div class="col-md-3 text-center">
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Replacement</span>
                                                        </div>
                                                        <input type="text"  class="form-control @if(isset($e_shippingType[3][0]) &&  isset($shippingType[3][0]) && $e_shippingType[3][0]->replacement_charges != $shippingType[3][0]->replacement_charges) changed @elseif(!isset($e_shippingType[3][0]) && $existing == 1) @if(isset($shippingType[3][0])) new @endif @endif percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_shippingType[3][0]) &&  isset($shippingType[3][0]) && $e_shippingType[3][0]->replacement_charges != $shippingType[3][0]->replacement_charges) {{$e_shippingType[3][0]->replacement_charges}} @endif" name="detain_replacement_charges" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($shippingType[3][0]) && $shippingType[3][0]->replacement_charges != '')? $shippingType[3][0]->replacement_charges : ''}}">
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
                                                        <input type="text"  class="form-control @if(isset($e_shippingType[3][0]) && isset($shippingType[3][0]) && $e_shippingType[3][0]->try_and_buy_charges != $shippingType[3][0]->try_and_buy_charges) changed @elseif(!isset($e_shippingType[3][0]) && $existing == 1) @if(isset($shippingType[3][0])) new @endif @endif percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_shippingType[3][0]) &&  isset($shippingType[3][0]) && $e_shippingType[3][0]->try_and_buy_charges != $shippingType[3][0]->try_and_buy_charges) {{$e_shippingType[3][0]->try_and_buy_charges}} @endif" name="detain_tnb_charges" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($shippingType[3][0]) && $shippingType[3][0]->try_and_buy_charges != '')? $shippingType[3][0]->try_and_buy_charges : ''}}">
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
                                                                <input type="checkbox" name="detain_dws" id="detain_dws" class="switchery detain_dws"  {{ (($detain_dws_charges != null) ? 'checked' : '') }} data-size="xs" data-switchery="true">
                                                            </div>
                                                        </fieldset>
                                                    </div> 
                                                    <div class="col-4">
                                                        <fieldset>
                                                            <div class="input-group form-group">
                                                                <select name="detain_dws_weight" id="detain_dws_weight" class="form-control"   {{ (($detain_dws_charges != null) ? '' : 'disabled') }}>
                                                                    @if ($detain_dws_charges != null)
                                                                    
                                                                        @if ($detain_dws_charges == 1)
                                                                        <option value="1" selected>High</option>
                                                                        <option value="2">Low</option>
                                                                        @elseif ($detain_dws_charges == 3)
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
                                                                <input name="detain_cash_range_up[{{$index}}]" type="text" class="form-control @if(isset($e_cashHandling[3][$index]) && $e_cashHandling[3][$index]->range_up != $cash->range_up) changed @elseif(!isset($e_cashHandling[3][$index]) && $existing == 1) new @endif numeric" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_cashHandling[3][$index]) && $e_cashHandling[3][$index]->range_up != $cash->range_up) {{$e_cashHandling[3][$index]->range_up}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->range_up}}" {{$det_cash_sw}}>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-md-2 text-center">
                                                            <fieldset class="form-group">
                                                                <input name="detain_cash_range_down[{{$index}}]" type="text" class="form-control @if(isset($e_cashHandling[3][$index]) && $e_cashHandling[3][$index]->range_down != $cash->range_down) changed @elseif(!isset($e_cashHandling[3][$index]) && $existing == 1) new @endif numeric" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_cashHandling[3][$index]) && $e_cashHandling[3][$index]->range_down != $cash->range_down) {{$e_cashHandling[3][$index]->range_down}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->range_down}}" {{$det_cash_sw}}>
                                                            </fieldset>
                                                        </div>

                                                        <div class="col-md-2">
                                                            <fieldset class="form-group">
                                                                <input name="detain_cash_charges[{{$index}}]" type="text" class="form-control @if(isset($e_cashHandling[3][$index]) && $e_cashHandling[3][$index]->charges != $cash->charges) changed @elseif(!isset($e_cashHandling[3][$index]) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_cashHandling[3][$index]) && $e_cashHandling[3][$index]->charges != $cash->charges) {{$e_cashHandling[3][$index]->charges}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->charges}}" {{$det_cash_sw}}>
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
                                                                <input name="detain_ins_range_up[{{$index}}]" type="text" class="form-control @if(isset($e_insuranceCharges[3][$index]) && $e_insuranceCharges[3][$index]->range_up != $det_insurance->range_up) changed @elseif(!isset($e_insuranceCharges[3][$index]) && $existing == 1) new @endif numeric" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_insuranceCharges[3][$index]) && $e_insuranceCharges[3][$index]->range_up != $det_insurance->range_up) {{$e_insuranceCharges[3][$index]->range_up}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$det_insurance->range_up}}" {{$det_ins_sw}}>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-md-2 text-center">
                                                            <fieldset class="form-group">
                                                                <input name="detain_ins_range_down[{{$index}}]" type="text" class="form-control @if(isset($e_insuranceCharges[3][$index]) && $e_insuranceCharges[3][$index]->range_down != $det_insurance->range_down) changed @elseif(!isset($e_insuranceCharges[3][$index]) && $existing == 1) new @endif numeric" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_insuranceCharges[3][$index]) && $e_insuranceCharges[3][$index]->range_down != $det_insurance->range_down) {{$e_insuranceCharges[3][$index]->range_down}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$det_insurance->range_down}}" {{$det_ins_sw}}>
                                                            </fieldset>
                                                        </div>

                                                        <div class="col-md-2">
                                                            <fieldset class="form-group">
                                                                <input name="detain_ins_charges[{{$index}}]" type="text" class="form-control @if(isset($e_insuranceCharges[3][$index]) && $e_insuranceCharges[3][$index]->charges != $det_insurance->charges) changed @elseif(!isset($e_insuranceCharges[3][$index]) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_insuranceCharges[3][$index]) && $e_insuranceCharges[3][$index]->charges != $det_insurance->charges) {{$e_insuranceCharges[3][$index]->charges}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$det_insurance->charges}}" {{$det_ins_sw}}>
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

                                        <div class="row return-charges-div-detain justify-content-center">
                                            <input type="hidden" name="detain_return_record" value="{{ (isset($returnCharges[3][0]) && $returnCharges[3][0]->id != '')? $returnCharges[3][0]->id : ''}}">

                                            <div class="col-md-2 text-center">
                                                <label class="card-title">Local Charges</label>
                                                <fieldset class="form-group">
                                                    <input type="text" class="form-control @if(isset($e_returnCharges[3][0]) && isset($returnCharges[3][0]) && $e_returnCharges[3][0]->local != $returnCharges[3][0]->local) changed @elseif(!isset($e_returnCharges[3][0]) && $existing == 1) new @endif amount" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_returnCharges[3][0]) && isset($returnCharges[3][0]) && $e_returnCharges[3][0]->local != $returnCharges[3][0]->local) {{$e_returnCharges[3][0]->local}} @endif" name="detain_return_local_charges" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($returnCharges[3][0]) && $returnCharges[3][0]->local !== '')? $returnCharges[3][0]->local : ''}}" {{$det_return_sw}}>
                                                </fieldset>
                                            </div>

                                            <div class="col text-center">
                                                <label class="card-title">National Charges Class A</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control @if(isset($e_returnCharges[3][0]) && isset($returnCharges[3][0]) && $e_returnCharges[3][0]->national_charges_class_0 != $returnCharges[3][0]->national_charges_class_0) changed @elseif(!isset($e_returnCharges[3][0]) && $existing == 1) new @endif amount" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_returnCharges[3][0]) && isset($returnCharges[3][0]) && $e_returnCharges[3][0]->national_charges_class_0 != $returnCharges[3][0]->national_charges_class_0) {{$e_returnCharges[3][0]->national_charges_class_0}} @endif" name="detain_return_class_0_charges"  value="{{ (isset($returnCharges[3][0]) && $returnCharges[3][0]->national_charges_class_0 !== '')? $returnCharges[3][0]->national_charges_class_0 : ''}}" {{$det_return_sw}}>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <label class="card-title">National Charges Class B</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control @if(isset($e_returnCharges[3][0]) && isset($returnCharges[3][0]) && $e_returnCharges[3][0]->national_charges_class_1 != $returnCharges[3][0]->national_charges_class_1) changed @elseif(!isset($e_returnCharges[3][0]) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_returnCharges[3][0]) && isset($returnCharges[3][0]) && $e_returnCharges[3][0]->national_charges_class_1 != $returnCharges[3][0]->national_charges_class_1) {{$e_returnCharges[3][0]->national_charges_class_1}} @endif" name="detain_return_class_1_charges"  value="{{ (isset($returnCharges[3][0]) && $returnCharges[3][0]->national_charges_class_1 !== '')? $returnCharges[3][0]->national_charges_class_1 : ''}}" {{$det_return_sw}}>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <label class="card-title">National Charges Class C</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control @if(isset($e_returnCharges[3][0]) && isset($returnCharges[3][0]) && $e_returnCharges[3][0]->national_charges_class_2 != $returnCharges[3][0]->national_charges_class_2) changed @elseif(!isset($e_returnCharges[3][0]) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_returnCharges[3][0]) && isset($returnCharges[3][0]) && $e_returnCharges[3][0]->national_charges_class_2 != $returnCharges[3][0]->national_charges_class_2) {{$e_returnCharges[3][0]->national_charges_class_2}} @endif" name="detain_return_class_2_charges"  value="{{ (isset($returnCharges[3][0]) && $returnCharges[3][0]->national_charges_class_2 !== '')? $returnCharges[3][0]->national_charges_class_2 : ''}}" {{$det_return_sw}}>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <label class="card-title">National Charges Class D</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control @if(isset($e_returnCharges[3][0]) && isset($returnCharges[3][0]) && $e_returnCharges[3][0]->national_charges_class_3 != $returnCharges[3][0]->national_charges_class_3) changed @elseif(!isset($e_returnCharges[3][0]) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_returnCharges[3][0]) && isset($returnCharges[3][0]) && $e_returnCharges[3][0]->national_charges_class_3 != $returnCharges[3][0]->national_charges_class_3) {{$e_returnCharges[3][0]->national_charges_class_3}} @endif" name="detain_return_class_3_charges"  value="{{ (isset($returnCharges[3][0]) && $returnCharges[3][0]->national_charges_class_3 !== '')? $returnCharges[3][0]->national_charges_class_3 : ''}}" {{$det_return_sw}}>
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
                                                        <input type="text"  class="form-control @if(isset($e_fuelCharges[3][0]) && isset($fuelCharges[3][0]) && $e_fuelCharges[3][0]->fuel_surcharge != $fuelCharges[3][0]->fuel_surcharge) changed @elseif(!isset($e_fuelCharges[3][0]) && $existing == 1) new @endif" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title=" @if(isset($e_fuelCharges[3][0]) && isset($fuelCharges[3][0]) && $e_fuelCharges[3][0]->fuel_surcharge != $fuelCharges[3][0]->fuel_surcharge) {{$e_fuelCharges[3][0]->fuel_surcharge}} @endif" name="detain_fuel_surcharge" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($fuelCharges[3][0]) && $fuelCharges[3][0]->fuel_surcharge != '')? $fuelCharges[3][0]->fuel_surcharge : ''}}" {{$det_fuel_sw}}>
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
                                                if((isset($e_discountCharges[3][0]) && $e_discountCharges[3][0]->title != '')){
                                                $e_det_discount_title = $e_discountCharges[3][0]->title;
                                                 }else{
                                                $e_det_discount_title = '';
                                                }
                                        @endphp
                                        <input type="hidden" name="detain_discount_record" value="{{$detain_discount_id}}">

                                        <div class="row mt-1">
                                            <div class="col-md-6">
                                                <label class="">Title</label>
                                                <div class='form-group'>
                                                    <input type='text' class="form-control @if(isset($e_discountCharges[3][0]) && $e_det_discount_title != $det_discount_title) changed @elseif(!isset($e_discountCharges[3][0]) && $existing == 1) new @endif" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_discountCharges[3][0]) && $e_det_discount_title != $det_discount_title) {{$e_det_discount_title}} @endif" data-rule-required="true" data-msg-required="This field is required" {{$det_discount_title_switch}} name="detain_discount_title" value="{{$det_discount_title}}"/>
                                                </div>

                                            </div>
                                            @php
                                                $det_discount_daterange = '';
                                                $e_det_discount_daterange = '';
                                                $det_discount_daterange_switch = '';
                                            if((isset($discountCharges[3][0]) && $discountCharges[3][0]->daterange != '')){

                                            $to = date('m/d/Y', strtotime($discountCharges[3][0]->to));
                                            $from = date('m/d/Y', strtotime($discountCharges[3][0]->from));

                                            $det_discount_daterange = $to.' - '.$from;

                                             }else{
                                            $det_discount_daterange = '';

                                            }
                                            if((isset($e_discountCharges[3][0]) && $e_discountCharges[3][0]->daterange != '')){

                                            $e_to = date('m/d/Y', strtotime($e_discountCharges[3][0]->to));
                                            $e_from = date('m/d/Y', strtotime($e_discountCharges[3][0]->from));

                                            $e_det_discount_daterange = $e_to.' - '.$e_from;

                                             }else{
                                            $e_det_discount_daterange = '';

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
                                                    <input type='text' class="form-control @if(isset($e_discountCharges[3][0]) && $e_det_discount_daterange != $det_discount_daterange) changed @elseif(!isset($e_discountCharges[3][0]) && $existing == 1) new @endif daterange" data-rule-required="true" data-msg-required="This field is required" name="detain_daterange" value="{{$det_discount_daterange}}"  {{$det_discount_daterange_switch}}/>
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
                                            $e_det_discount_weight_sw = '';
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
                                        if((isset($e_discountCharges[3][0]) && $e_discountCharges[3][0]->weight != '')){
                                        $e_det_discount_weight_sw = $e_discountCharges[3][0]->weight;
                                         }else{
                                        $e_det_discount_weight_sw = '';
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
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control @if(isset($e_discountCharges[3][0]) && $e_det_discount_weight_sw != $det_discount_weight_sw) changed @elseif(!isset($e_discountCharges[3][0]) && $existing == 1) new @endif dec-percent detain-discount-inp" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_discountCharges[3][0]) && $e_det_discount_weight_sw != $det_discount_weight_sw) {{$e_det_discount_weight_sw}} @endif" name="detain_discount_weight_rate" value="{{$det_discount_weight_sw}}" {{$det_discount_weight_disable}}>
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
                                            if((isset($e_discountCharges[3][0]) && $e_discountCharges[3][0]->cash != '')){
                                            $e_det_discount_cash_sw = $e_discountCharges[3][0]->cash;
                                             }else{
                                            $e_det_discount_cash_sw = '';
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
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control @if(isset($e_discountCharges[3][0]) && $e_det_discount_cash_sw != $det_discount_cash_sw) changed @elseif(!isset($e_discountCharges[3][0]) && $existing == 1) new @endif dec-percent detain-discount-inp" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_discountCharges[3][0]) && $e_det_discount_cash_sw != $det_discount_cash_sw) {{$e_det_discount_cash_sw}} @endif" name="detain_discount_cash_rate" value="{{$det_discount_cash_sw}}" {{$det_discount_cash_disable}}>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            @php
                                                $det_discount_insurance_sw = '';
                                                $e_det_discount_insurance_sw = '';
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
                                            if((isset($e_discountCharges[3][0]) && $e_discountCharges[3][0]->insurance != '')){
                                            $e_det_discount_insurance_sw = $e_discountCharges[3][0]->insurance;
                                             }else{
                                            $e_det_discount_insurance_sw = '';
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
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control @if(isset($e_discountCharges[3][0]) && $e_det_discount_insurance_sw != $det_discount_insurance_sw) changed @elseif(!isset($e_discountCharges[3][0]) && $existing == 1) new @endif dec-percent detain-discount-inp" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_discountCharges[3][0]) && $e_det_discount_insurance_sw != $det_discount_insurance_sw) {{$e_det_discount_insurance_sw}} @endif" name="detain_discount_insurance_rate" value="{{$det_discount_insurance_sw}}" {{$det_discount_insurance_disable}}>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            @php
                                                $det_discount_return_sw = '';
                                                $e_det_discount_return_sw = '';
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
                                            if((isset($e_discountCharges[3][0]) && $e_discountCharges[3][0]->return != '')){
                                            $e_det_discount_return_sw = $e_discountCharges[3][0]->return;
                                             }else{
                                            $e_det_discount_return_sw = '';
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
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control @if(isset($e_discountCharges[3][0]) && $e_det_discount_return_sw != $det_discount_return_sw) changed @elseif(!isset($e_discountCharges[3][0]) && $existing == 1) new @endif dec-percent detain-discount-inp" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_discountCharges[3][0]) && $e_det_discount_return_sw != $det_discount_return_sw) {{$e_det_discount_return_sw}} @endif" name="detain_discount_return_rate" value="{{$det_discount_return_sw}}" {{$det_discount_return_disable}}>
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
                                                <h3>Destination Cities</h3>
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
                                                        @if(isset($min_weight[4]))
                                                        @foreach($min_weight[4] as $index => $mweight)
                                                            @if($mweight->delivery_type_id == 1)
                                                                    <input type="hidden" name="sameday_door_min_chargeable_weight" value="{{$mweight->id}}">

                                                                    <input type="text" class="form-control @if ((isset($e_min_weight[4][$index]) && $e_min_weight[4][$index]->delivery_type_id == 1) && ($e_min_weight[4][$index]->min_chargeable_weight != $mweight->min_chargeable_weight)) changed @endif decimal" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_min_weight[4][$index]) && $e_min_weight[4][$index]->delivery_type_id == 1) && ($e_min_weight[4][$index]->min_chargeable_weight != $mweight->min_chargeable_weight)) {{$e_min_weight[4][$index]->min_chargeable_weight}} @endif" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="{{$mweight->min_chargeable_weight}}" name="sameday_door_mcw_charges" placeholder="Minimum Chargeable Weight">
                                                            @endif
                                                        @endforeach
                                                            @else
                                                            <input type="hidden" name="sameday_door_min_chargeable_weight" value="">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="" name="sameday_door_mcw_charges" placeholder="Minimum Chargeable Weight">
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
                                                    $index_row = 1;
                                                    $same_index_row = 0;
                                                @endphp
                                            @foreach($weight[4] as $index => $sameweight)
                                                @if($sameweight->delivery_type_id == 1)
                                                <div class="row sameday_door_weight_row" id="sameday_door_weight_row{{$index_row}}">
                                                    <input type="hidden" name="sameday_door_weight_record[{{$index_row}}]" value="{{$sameweight->id}}">

                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[4][$same_index_row]) && $e_weight[4][$same_index_row]->delivery_type_id == 1) && ($e_weight[4][$same_index_row]->range_up != $sameweight->range_up)) changed @elseif(((isset($e_weight[4][$same_index_row]) && $e_weight[4][$same_index_row]->delivery_type_id == 2) || !isset($e_weight[4][$same_index_row])) && $existing == 1) new @endif decimal weight_range" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[4][$same_index_row]) && $e_weight[4][$same_index_row]->delivery_type_id == 1) && ($e_weight[4][$same_index_row]->range_up != $sameweight->range_up)) {{$e_weight[4][$same_index_row]->range_up}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->range_up}}" name="sameday_door_range_up[{{$index_row}}]" {{ (($index_row == 0 || $index_row == 1) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[4][$same_index_row]) && $e_weight[4][$same_index_row]->delivery_type_id == 1) && ($e_weight[4][$same_index_row]->range_down != $sameweight->range_down)) changed @elseif(((isset($e_weight[4][$same_index_row]) && $e_weight[4][$same_index_row]->delivery_type_id == 2) || !isset($e_weight[4][$same_index_row])) && $existing == 1) new @endif decimal weight_range" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[4][$same_index_row]) && $e_weight[4][$same_index_row]->delivery_type_id == 1) && ($e_weight[4][$same_index_row]->range_down != $sameweight->range_down)) {{$e_weight[4][$same_index_row]->range_down}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->range_down}}" name="sameday_door_range_down[{{$index_row}}]" {{ (($index_row == 0) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="SamedayDoorSwitch{{$index_row}}" class="switchery weightAdditionDoorSameday" data-color="success" data-size="sm" name="sameday_door_wa_switch[{{$index_row}}]" {{ ($sameweight->base == 1) ? 'checked' : '' }}/>
                                                        </div>
                                                    </div>

                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[4][$same_index_row]) && $e_weight[4][$same_index_row]->delivery_type_id == 1) && ($e_weight[4][$same_index_row]->local_or_6hr != $sameweight->local_or_6hr)) changed @elseif(((isset($e_weight[4][$same_index_row]) && $e_weight[4][$same_index_row]->delivery_type_id == 2) || !isset($e_weight[4][$same_index_row])) && $existing == 1) new @endif decimal" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[4][$same_index_row]) && $e_weight[4][$same_index_row]->delivery_type_id == 1) && ($e_weight[4][$same_index_row]->local_or_6hr != $sameweight->local_or_6hr)) {{$e_weight[4][$same_index_row]->local_or_6hr}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->local_or_6hr}}" name="sameday_door_local_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[4][$same_index_row]) && $e_weight[4][$same_index_row]->delivery_type_id == 1) && ($e_weight[4][$same_index_row]->national_charges_class_0 != $sameweight->national_charges_class_0)) changed @elseif(((isset($e_weight[4][$same_index_row]) && $e_weight[4][$same_index_row]->delivery_type_id == 2) || !isset($e_weight[4][$same_index_row])) && $existing == 1) new @endif decimal" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[4][$same_index_row]) && $e_weight[4][$same_index_row]->delivery_type_id == 1) && ($e_weight[4][$same_index_row]->national_charges_class_0 != $sameweight->national_charges_class_0)) {{$e_weight[4][$same_index_row]->national_charges_class_0}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->national_charges_class_0}}" name="sameday_door_class_0_charges[{{$index_row}}]">
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
                                                            if(isset($e_weight[4][$same_index_row]) && $e_weight[4][$same_index_row]->delivery_type_id == 1){
                                                                $same_index_row++;
                                                            }
                                                        @endphp
                                                    @endif
                                            @endforeach
                                                @else
                                                <div class="row sameday_door_weight_row" id="sameday_door_weight_row0">
                                                    <input type="hidden" name="sameday_door_weight_record[1]" value="">

                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="0.01" name="sameday_door_range_up[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_door_range_down[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="SamedayDoorSwitch0" class="switchery weightAdditionDoorSameday" data-color="success" data-size="sm" name="sameday_door_wa_switch[1]"/>
                                                        </div>
                                                    </div>

                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_door_local_charges[1]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_door_class_0_charges[1]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-1">
                                                    </div>
                                                </div>{{--Row--}}
                                            @endif
                                        </div>{{--weight addition div--}}
                                        <div class="mb-2">
                                            <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="sameday_door_weightadd"><i class="la la-plus"></i></button>
                                        </div>
                                        <div class="hub-weight-addition-sameday">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Hub-Hub Delivery</h3>
                                                </div>
                                                <div class="col-2">
                                                    <a href="javascript:void(0);" class="pull-right" id="sameday_hub_to_hub_switch"><input name="sameday_hub_to_hub_switch" type="checkbox"  class="switchery sameday_hub_to_hub_switch" data-size="sm" @if(isset($hub_delivery_type_status[4])) checked @endif/></a>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <fieldset class="form-group">
                                                        @if(isset($min_weight[4]) && isset($hub_delivery_type_status[4]))
                                                        @foreach($min_weight[4] as $index => $mweight)
                                                            @if($mweight->delivery_type_id == 2)
                                                                    <input type="hidden" name="sameday_hub_min_chargeable_weight" value="{{$mweight->id}}">
                                                                    <input type="text" class="form-control @if ((isset($e_min_weight[4][$index]) && $e_min_weight[4][$index]->delivery_type_id == 2) && (isset($e_min_weight[4][$index]) && $e_min_weight[4][$index]->min_chargeable_weight != $mweight->min_chargeable_weight)) changed @endif decimal" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_min_weight[4][$index]) && $e_min_weight[4][$index]->delivery_type_id == 2) && (isset($e_min_weight[4][$index]) && $e_min_weight[4][$index]->min_chargeable_weight != $mweight->min_chargeable_weight)) {{$e_min_weight[4][$index]->min_chargeable_weight}} @endif" data-rule-required="true" data-msg-required="Minimum Chargeable Weight is required" value="{{$mweight->min_chargeable_weight}}" name="sameday_hub_mcw_charges" placeholder="Minimum Chargeable Weight">
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
                                            @if(isset($weight[4]) && isset($hub_delivery_type_status[4]))
                                                @php
                                                    $index_row = 1;
                                                @endphp
                                            @foreach($weight[4] as $index => $sameweight)
                                                @if($sameweight->delivery_type_id == 2)
                                                <div class="row sameday_hub_weight_row" id="sameday_hub_weight_row{{$index_row}}">
                                                    <input type="hidden" name="sameday_hub_weight_record[{{$index_row}}]" value="{{$sameweight->id}}">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[4][$same_index_row]) && $e_weight[4][$same_index_row]->delivery_type_id == 2) && ($e_weight[4][$same_index_row]->range_up != $sameweight->range_up)) changed @elseif(((isset($e_weight[4][$same_index_row]) && $e_weight[4][$same_index_row]->delivery_type_id == 1) || !isset($e_weight[4][$same_index_row])) && $existing == 1) new @endif decimal weight_range" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[4][$same_index_row]) && $e_weight[4][$same_index_row]->delivery_type_id == 2) && ($e_weight[4][$same_index_row]->range_up != $sameweight->range_up)) {{$e_weight[4][$same_index_row]->range_up}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->range_up}}" name="sameday_hub_range_up[{{$index_row}}]" {{ (($index_row == 0 || $index_row == 1) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[4][$same_index_row]) && $e_weight[4][$same_index_row]->delivery_type_id == 2) && ($e_weight[4][$same_index_row]->range_down != $sameweight->range_down)) changed @elseif(((isset($e_weight[4][$same_index_row]) && $e_weight[4][$same_index_row]->delivery_type_id == 1) || !isset($e_weight[4][$same_index_row])) && $existing == 1) new @endif decimal weight_range" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[4][$same_index_row]) && $e_weight[4][$same_index_row]->delivery_type_id == 2) && ($e_weight[4][$same_index_row]->range_down != $sameweight->range_down)) {{$e_weight[4][$same_index_row]->range_down}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->range_down}}" name="sameday_hub_range_down[{{$index_row}}]" {{ (($index_row == 0) ? 'disabled' : '') }}>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="SamedayHubSwitch{{$index_row}}" class="switchery weightAdditionHubSameday" data-color="success" data-size="sm" name="sameday_hub_wa_switch[{{$index_row}}]" {{ ($sameweight->base == 1) ? 'checked' : '' }}/>
                                                        </div>
                                                    </div>

                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[4][$same_index_row]) && $e_weight[4][$same_index_row]->delivery_type_id == 2) && ($e_weight[4][$same_index_row]->local_or_6hr != $sameweight->local_or_6hr)) changed @elseif(((isset($e_weight[4][$same_index_row]) && $e_weight[4][$same_index_row]->delivery_type_id == 1) || !isset($e_weight[4][$same_index_row])) && $existing == 1) new @endif decimal" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[4][$same_index_row]) && $e_weight[4][$same_index_row]->delivery_type_id == 2) && ($e_weight[4][$same_index_row]->local_or_6hr != $sameweight->local_or_6hr)) {{$e_weight[4][$same_index_row]->local_or_6hr}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->local_or_6hr}}" name="sameday_hub_local_charges[{{$index_row}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control @if ((isset($e_weight[4][$same_index_row]) && $e_weight[4][$same_index_row]->delivery_type_id == 2) && ($e_weight[4][$same_index_row]->national_charges_class_0 != $sameweight->national_charges_class_0)) changed @elseif(((isset($e_weight[4][$same_index_row]) && $e_weight[4][$same_index_row]->delivery_type_id == 1) || !isset($e_weight[4][$same_index_row])) && $existing == 1) new @endif decimal" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if ((isset($e_weight[4][$same_index_row]) && $e_weight[4][$same_index_row]->delivery_type_id == 2) && ($e_weight[4][$same_index_row]->national_charges_class_0 != $sameweight->national_charges_class_0)) {{$e_weight[4][$same_index_row]->national_charges_class_0}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$sameweight->national_charges_class_0}}" name="sameday_hub_class_0_charges[{{$index_row}}]">
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
                                                            if(isset($e_weight[4][$same_index_row]) && $e_weight[4][$same_index_row]->delivery_type_id == 2){
                                                                $same_index_row++;
                                                            }
                                                        @endphp
                                                    @endif
                                            @endforeach
                                                @else
                                                <div class="row sameday_hub_weight_row" id="sameday_hub_weight_row0">
                                                    <input type="hidden" name="sameday_hub_weight_record[1]" value="">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="0.01" name="sameday_hub_range_up[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_hub_range_down[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="SamedayHubSwitch0" class="switchery weightAdditionHubSameday" data-color="success" data-size="sm" name="sameday_hub_wa_switch[1]"/>
                                                        </div>
                                                    </div>

                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_hub_local_charges[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_hub_class_0_charges[1]" disabled>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-1">

                                                    </div>
                                                </div>{{--Row--}}
                                            @endif
                                        </div>{{--weight addition div--}}
                                        <div class="mb-2">
                                            <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="sameday_hub_weightadd"><i class="la la-plus"></i></button>
                                        </div>
                                        <div class="row mt-2">
                                            <input type="hidden" name="sameday_booking_record" value="{{ (isset($shippingType[4][0]) && $shippingType[4][0]->id != '')? $shippingType[4][0]->id : ''}}">

                                            <div class="col-md-3 text-center">
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Replacement</span>
                                                        </div>
                                                        <input type="text"  class="form-control @if(isset($e_shippingType[4][0]) && isset($shippingType[4][0]) && $e_shippingType[4][0]->replacement_charges != $shippingType[4][0]->replacement_charges) changed @elseif(!isset($e_shippingType[4][0]) && $existing == 1) @if(isset($shippingType[4][0])) new @endif @endif percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_shippingType[4][0]) && isset($shippingType[4][0]) && $e_shippingType[4][0]->replacement_charges != $shippingType[4][0]->replacement_charges) {{$e_shippingType[4][0]->replacement_charges}} @endif" name="sameday_replacement_charges" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($shippingType[4][0]) && $shippingType[4][0]->replacement_charges != '')? $shippingType[4][0]->replacement_charges : ''}}">
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
                                                        <input type="text"  class="form-control @if(isset($e_shippingType[4][0]) && isset($shippingType[4][0]) && $e_shippingType[4][0]->try_and_buy_charges != $shippingType[4][0]->try_and_buy_charges) changed @elseif(!isset($e_shippingType[4][0]) && $existing == 1) @if(isset($shippingType[4][0])) new @endif @endif percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_shippingType[4][0]) && isset($shippingType[4][0]) && $e_shippingType[4][0]->try_and_buy_charges != $shippingType[4][0]->try_and_buy_charges) {{$e_shippingType[4][0]->try_and_buy_charges}} @endif" name="sameday_tnb_charges" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($shippingType[4][0]) && $shippingType[4][0]->try_and_buy_charges != '')? $shippingType[4][0]->try_and_buy_charges : ''}}">
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
                                                                <input type="checkbox" name="sameday_dws" id="sameday_dws" class="switchery sameday_dws" {{ (($sameday_dws_charges != null) ? 'checked' : '') }} data-size="xs" data-switchery="true">
                                                            </div>
                                                        </fieldset>
                                                    </div> 
                                                    <div class="col-4">
                                                        <fieldset>
                                                            <div class="input-group form-group">
                                                                <select name="sameday_dws_weight" id="sameday_dws_weight" class="form-control"  {{ (($sameday_dws_charges != null) ? '' : 'disabled') }}>
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
                                                                <input name="sameday_cash_range_up[{{$index}}]" type="text" class="form-control @if(isset($e_cashHandling[4][$index]) && $e_cashHandling[4][$index]->range_up != $cash->range_up) changed @elseif(!isset($e_cashHandling[4][$index]) && $existing == 1) new @endif numeric" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_cashHandling[4][$index]) && $e_cashHandling[4][$index]->range_up != $cash->range_up) {{$e_cashHandling[4][$index]->range_up}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->range_up}}" {{$same_cash_sw}}>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-md-2 text-center">
                                                            <fieldset class="form-group">
                                                                <input name="sameday_cash_range_down[{{$index}}]" type="text" class="form-control @if(isset($e_cashHandling[4][$index]) && $e_cashHandling[4][$index]->range_down != $cash->range_down) changed @elseif(!isset($e_cashHandling[4][$index]) && $existing == 1) new @endif numeric" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_cashHandling[4][$index]) && $e_cashHandling[4][$index]->range_down != $cash->range_down) {{$e_cashHandling[4][$index]->range_down}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->range_down}}" {{$same_cash_sw}}>
                                                            </fieldset>
                                                        </div>

                                                        <div class="col-md-2 text-center">
                                                            <fieldset class="form-group">
                                                                <input name="sameday_cash_charges[{{$index}}]" type="text" class="form-control @if(isset($e_cashHandling[4][$index]) && $e_cashHandling[4][$index]->charges != $cash->charges) changed @elseif(!isset($e_cashHandling[4][$index]) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_cashHandling[4][$index]) && $e_cashHandling[4][$index]->charges != $cash->charges) {{$e_cashHandling[4][$index]->charges}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$cash->charges}}" {{$same_cash_sw}}>
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
                                                                <input name="sameday_ins_range_up[{{$index}}]" type="text" class="form-control @if(isset($e_insuranceCharges[4][$index]) && $e_insuranceCharges[4][$index]->range_up != $same_insurance->range_up) changed @elseif(!isset($e_insuranceCharges[4][$index]) && $existing == 1) new @endif numeric" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_insuranceCharges[4][$index]) && $e_insuranceCharges[4][$index]->range_up != $same_insurance->range_up) {{$e_insuranceCharges[4][$index]->range_up}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$same_insurance->range_up}}" {{$same_ins_sw}}>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-md-2 text-center">
                                                            <fieldset class="form-group">
                                                                <input name="sameday_ins_range_down[{{$index}}]" type="text" class="form-control @if(isset($e_insuranceCharges[4][$index]) && $e_insuranceCharges[4][$index]->range_down != $same_insurance->range_down) changed @elseif(!isset($e_insuranceCharges[4][$index]) && $existing == 1) new @endif numeric" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_insuranceCharges[4][$index]) && $e_insuranceCharges[4][$index]->range_down != $same_insurance->range_down) {{$e_insuranceCharges[4][$index]->range_down}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$same_insurance->range_down}}" {{$same_ins_sw}}>
                                                            </fieldset>
                                                        </div>

                                                        <div class="col-md-2 text-center">
                                                            <fieldset class="form-group">
                                                                <input name="sameday_ins_charges[{{$index}}]" type="text" class="form-control @if(isset($e_insuranceCharges[4][$index]) && $e_insuranceCharges[4][$index]->charges != $same_insurance->charges) changed @elseif(!isset($e_insuranceCharges[4][$index]) && $existing == 1) new @endif dec-percent" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_insuranceCharges[4][$index]) && $e_insuranceCharges[4][$index]->charges != $same_insurance->charges) {{$e_insuranceCharges[4][$index]->charges}} @endif" data-rule-required="true" data-msg-required="This field is required" value="{{$same_insurance->charges}}" {{$same_ins_sw}}>
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
                                                    <input type="text"  class="form-control @if(isset($e_returnCharges[4][0]) && isset($returnCharges[4][0]) && $e_returnCharges[4][0]->local != $returnCharges[4][0]->local) changed @elseif(!isset($e_returnCharges[4][0]) && $existing == 1) new @endif amount" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_returnCharges[4][0]) && isset($returnCharges[4][0]) && $e_returnCharges[4][0]->local != $returnCharges[4][0]->local) {{$e_returnCharges[4][0]->local}} @endif" name="sameday_return_local_charges"  data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($returnCharges[4][0]) && $returnCharges[4][0]->local !== '')? $returnCharges[4][0]->local : ''}}" {{$same_return_sw}}>
                                                </fieldset>
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="card-title">National Charges</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control @if(isset($e_returnCharges[4][0]) && isset($returnCharges[4][0]) && $e_returnCharges[4][0]->national_charges_class_0 != $returnCharges[4][0]->national_charges_class_0) changed @elseif(!isset($e_returnCharges[4][0]) && $existing == 1) new @endif amount" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_returnCharges[4][0]) && isset($returnCharges[4][0]) && $e_returnCharges[4][0]->national_charges_class_0 != $returnCharges[4][0]->national_charges_class_0) {{$e_returnCharges[4][0]->national_charges_class_0}} @endif" name="sameday_return_class_0_charges" value="{{ (isset($returnCharges[4][0]) && $returnCharges[4][0]->national_charges_class_0 !== '')? $returnCharges[4][0]->national_charges_class_0 : ''}}" {{$same_return_sw}}>
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
                                                        <input type="text"  class="form-control @if(isset($e_fuelCharges[4][0]) && isset($fuelCharges[4][0]) && $e_fuelCharges[4][0]->fuel_surcharge != $fuelCharges[4][0]->fuel_surcharge) changed @elseif(!isset($e_fuelCharges[4][0]) && $existing == 1) new @endif" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_fuelCharges[4][0]) && isset($fuelCharges[4][0]) && $e_fuelCharges[4][0]->fuel_surcharge != $fuelCharges[4][0]->fuel_surcharge) {{$e_fuelCharges[4][0]->fuel_surcharge}} @endif" name="sameday_fuel_surcharge" data-rule-required="true" data-msg-required="This field is required" value="{{ (isset($fuelCharges[4][0]) && $fuelCharges[4][0]->fuel_surcharge != '')? $fuelCharges[4][0]->fuel_surcharge : ''}}" {{$same_fuel_sw}}>
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
                                            if((isset($e_discountCharges[4][0]) && $e_discountCharges[4][0]->title != '')){
                                            $e_same_discount_title = $e_discountCharges[4][0]->title;
                                             }else{
                                            $e_same_discount_title = '';
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
                                                    <input type='text' class="form-control @if(isset($e_discountCharges[4][0]) && $same_discount_title != $e_same_discount_title) changed @elseif(!isset($e_discountCharges[4][0]) && $existing == 1) new @endif" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_discountCharges[4][0]) && $same_discount_title != $e_same_discount_title) {{$e_same_discount_title}} @endif" data-rule-required="true" data-msg-required="This field is required"  name="sameday_discount_title" value="{{$same_discount_title}}" {{$same_discount_title_switch}}/>
                                                </div>

                                            </div>
                                            @php
                                                $same_discount_daterange = '';
                                                $e_same_discount_daterange = '';
                                                $same_discount_daterange_switch = '';
                                            if((isset($discountCharges[4][0]) && $discountCharges[4][0]->daterange != '')){

                                            $to = date('m/d/Y', strtotime($discountCharges[4][0]->to));
                                            $from = date('m/d/Y', strtotime($discountCharges[4][0]->from));

                                            $same_discount_daterange = $to.' - '.$from;

                                             }else{
                                            $same_discount_daterange = '';

                                            }
                                                $e_same_discount_daterange = '';
                                            if((isset($discountCharges[4][0]->cash)) || (isset($discountCharges[4][0]->weight)) || (isset($discountCharges[4][0]->insurance)) || (isset($discountCharges[4][0]->return)) || (isset($discountCharges[4][0]->packaging))){
                                            $same_discount_daterange_switch = '';
                                            }else{
                                            $same_discount_daterange_switch = 'disabled';
                                            }
                                            @endphp
                                            <div class="col-md-6">
                                                <label class="">Apply [to - from]</label>
                                                <div class='input-group form-group'>
                                                    <input type='text' class="form-control @if(isset($e_discountCharges[4][0]) && $e_same_discount_daterange != $same_discount_daterange) changed @elseif(!isset($e_discountCharges[4][0]) && $existing == 1) new @endif daterange" data-rule-required="true" data-msg-required="This field is required" {{$same_discount_daterange_switch}} name="sameday_daterange" value="{{$same_discount_daterange}}"/>
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
                                            $e_same_discount_weight_sw = '';
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
                                        if((isset($e_discountCharges[4][0]) && $e_discountCharges[4][0]->weight != '')){
                                        $e_same_discount_weight_sw = $e_discountCharges[4][0]->weight;
                                         }else{
                                        $e_same_discount_weight_sw = '';
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
                                                        <input type="text"  class="form-control @if(isset($e_discountCharges[4][0]) && $e_same_discount_weight_sw != $same_discount_weight_sw) changed @elseif(!isset($e_discountCharges[4][0]) && $existing == 1) new @endif dec-percent sameday-discount-inp" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_discountCharges[4][0]) && $e_same_discount_weight_sw != $same_discount_weight_sw) {{$e_same_discount_weight_sw}} @endif" data-rule-required="true" data-msg-required="This field is required" name="sameday_discount_weight_rate" {{$same_discount_weight_disable}} value="{{$same_discount_weight_sw}}">
                                                    </div>
                                                </fieldset>
                                            </div>
                                            @php
                                                $same_discount_cash_sw = '';
                                                $e_same_discount_cash_sw = '';
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
                                            if((isset($e_discountCharges[4][0]) && $e_discountCharges[4][0]->weight != '')){
                                            $e_same_discount_cash_sw = $e_discountCharges[4][0]->cash;
                                             }else{
                                            $e_same_discount_cash_sw = '';
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
                                                        <input type="text"  class="form-control @if(isset($e_discountCharges[4][0]) && $e_same_discount_cash_sw != $same_discount_cash_sw) changed @elseif(!isset($e_discountCharges[4][0]) && $existing == 1) new @endif dec-percent sameday-discount-inp" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_discountCharges[4][0]) && $e_same_discount_cash_sw != $same_discount_cash_sw) {{$e_same_discount_cash_sw}} @endif" data-rule-required="true" data-msg-required="This field is required" name="sameday_discount_cash_rate" value="{{$same_discount_cash_sw}}" {{$same_discount_cash_disable}}>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            @php
                                                $same_discount_insurance_sw = '';
                                                $e_same_discount_insurance_sw = '';
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
                                            if((isset($e_discountCharges[4][0]) && $e_discountCharges[4][0]->insurance != '')){
                                            $e_same_discount_insurance_sw = $e_discountCharges[4][0]->insurance;
                                             }else{
                                            $e_same_discount_insurance_sw = '';
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
                                                        <input type="text"  class="form-control @if(isset($e_discountCharges[4][0]) && $e_same_discount_insurance_sw != $same_discount_insurance_sw) changed @elseif(!isset($e_discountCharges[4][0]) && $existing == 1) new @endif dec-percent sameday-discount-inp" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_discountCharges[4][0]) && $e_same_discount_insurance_sw != $same_discount_insurance_sw) {{$e_same_discount_insurance_sw}} @endif" data-rule-required="true" data-msg-required="This field is required" name="sameday_discount_insurance_rate" value="{{$same_discount_insurance_sw}}" {{$same_discount_insurance_disable}}>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            @php
                                                $same_discount_return_sw = '';
                                                $e_same_discount_return_sw = '';
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
                                            if((isset($e_discountCharges[4][0]) && $e_discountCharges[4][0]->return != '')){
                                            $e_same_discount_return_sw = $e_discountCharges[4][0]->return;
                                             }else{
                                            $e_same_discount_return_sw = '';
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
                                                        <input type="text"  class="form-control @if(isset($e_discountCharges[4][0]) && $e_same_discount_return_sw != $same_discount_return_sw) changed @elseif(!isset($e_discountCharges[4][0]) && $existing == 1) new @endif dec-percent sameday-discount-inp" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="@if(isset($e_discountCharges[4][0]) && $e_same_discount_return_sw != $same_discount_return_sw) {{$e_same_discount_return_sw}} @endif" data-rule-required="true" data-msg-required="This field is required" name="sameday_discount_return_rate" value="{{$same_discount_return_sw}}" {{$same_discount_return_disable}}>
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
                                        <a id="warehouse_main_switch" href="javascript:void(0);" class="pull-right"><input name="warehouse_main_switch" type="checkbox" class="switchery warehouse-main-switch" data-size="sm" data-color="info" {{ ($wms_user_info->warehousing)? 'checked':'' }}/></a>
                                    </div>
                                </div>

                            </div>
                            <div id="warehousing" class="border-primary no-border-top card {{ ($wms_user_info->warehousing)? '':'hide' }}">
                                <div class="card-content">
                                    <div class="card-body pb-0">
                                        <div class="row">
                                            <div class="col-3 form-group">
                                                <select name="invoicing_cycle" class="select2" id="invoicing_cycle_select" data-rule-required="true" data-msg-required="Invoicing cycle is required">
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
                                                                            <input type="checkbox" name="ppc_switch" data-color="info" class="switchery PPCSwitch" data-size="xs" {{ ($wms_user_info->per_product_charges)? 'checked':'' }}/>
                                                                          </span>
                                                                    </div>
                                                                    <input type="text"  class="form-control numeric ppc-inp"  data-rule-required="true" data-msg-required="This field is required" name="ppc_charges" value="{{ isset($wms_product_charges)? $wms_product_charges->charges:0}}">
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
                                                                            <input type="checkbox" name="psf_switch" data-color="info" class="switchery PSFSwitch" data-size="xs" {{ ($wms_user_info->per_square_foot_charges)? 'checked':'' }}/>
                                                                          </span>
                                                                    </div>
                                                                    <input type="text"  class="form-control numeric psf-inp"  data-rule-required="true" data-msg-required="This field is required" name="psf_charges" value="{{ isset($wms_square_foot_charges)? $wms_square_foot_charges->charges:0 }}">
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
                                                                <input type="checkbox" name="storage_charges_switch" class="switchery storageCharges" data-color="info" data-size="sm" {{ ($wms_user_info->storage_charges)? 'checked':'' }}/>
                                                            </div>
                                                        </div>
                                                        <div class="col-12" id="wms_storage_types_div">
                                                            @if($wms_user_info->storage_charges)
                                                            @php
                                                                $storage_count = count($wms_storage_charges);
                                                                $row_count = 1;
                                                            @endphp
                                                            @foreach($wms_storage_charges as $key => $storage)
                                                            <div class="row storage_type_row" id="storage_type_row{{$key}}">
                                                                <input type="hidden" id="storage_type_input{{$key}}" name="storage_type[{{$key}}]" value="{{$storage->storage_type_id}}">
                                                                <div class="col-md-2 st_select">
                                                                    <fieldset class="form-group">
                                                                        <select class="select2 form-control storage_type" name="storage_type[{{$key}}]" data-rule-required="true" data-msg-required="This field is required" disabled="disabled">
                                                                            @foreach($storage_types as $types)
                                                                               <option value="{{$types->id}}">{{$types->name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </fieldset>
                                                                </div>

                                                                <div class="col-md-2">
                                                                    <fieldset class="form-group">
                                                                        <input name="storage_type_charges[{{$key}}]" data-rule-required="true" data-msg-required="Charges are required" type="text" class="form-control numeric" placeholder="Charges" value="{{ $storage->charges }}">
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col actions">
                                                                        <span id="storage_type_add" class="btn btn-sm btn-outline-primary {{ ($storage_count == $row_count)? '':'d-none' }}"><i class="la la-check"></i></span>
                                                                        @if($key > 0)
                                                                        <span  row="{{$key}}" class="storage_type_row_delete btn btn-sm btn-outline-danger"><i class="la la-trash"></i></span>
                                                                        @endif
                                                                </div>
                                                            </div>
                                                            @php  $row_count++ @endphp
                                                            @endforeach
                                                            @else
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
                                                                <input type="checkbox" name="packing_charges_switch" class="switchery packingCharges" data-color="info" data-size="sm" {{ ($wms_user_info->packing_charges)? 'checked':''}}/>
                                                            </div>
                                                        </div>

                                                        <div class="col-12" id="wms_packing_charges_div">
                                                            @if($wms_user_info->packing_charges)
                                                            @php
                                                                $packing_count = count($wms_packing_charges);
                                                                $packing_row_count = 1;
                                                            @endphp
                                                            @foreach($wms_packing_charges as $pkey => $packing)
                                                            <div class="row packing_type_row" id="packing_type_row{{$pkey}}">
                                                                <input type="hidden" id="packing_type_input{{$pkey}}" name="packing_type[{{$pkey}}]" value="{{$packing->packing_type_id}}">
                                                                <div class="col-md-2">
                                                                    <fieldset class="form-group">
                                                                        <select class="select2 form-control packing_type" name="packing_type[{{$pkey}}]" data-rule-required="true" data-msg-required="This field is required" disabled="disabled">
                                                                            @foreach($packaging_material_types as $mtype)
                                                                                <option value="{{$mtype->id}}">{{$mtype->type}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </fieldset>
                                                                </div>
                                                                <input type="hidden" id="packing_size_input{{$pkey}}" name="packing_size[{{$pkey}}]" value="{{$packing->packing_size_id}}">
                                                                <div class="col-md-2">
                                                                    <fieldset class="form-group">
                                                                        <select class="select2 form-control packing_size" name="packing_size[{{$pkey}}]" data-rule-required="true" data-msg-required="This field is required" disabled="disabled">
                                                                            @foreach($packaging_material_types as $mtype)
                                                                                @foreach($mtype->sizes as $size)
                                                                                    <option value="{{$size->id}}">{{$size->size}}</option>
                                                                                @endforeach
                                                                            @endforeach
                                                                        </select>
                                                                    </fieldset>
                                                                </div>

                                                                <div class="col-md-2">
                                                                    <fieldset class="form-group">
                                                                        <input name="packing_charges[{{$pkey}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control amount" placeholder="Charges" value="{{$packing->charges}}" {{ ($wms_user_info->packing_charges)? '':'disabled'}}>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-2">
                                                                        <span id="packing_type_add" class="btn btn-sm btn-outline-primary {{ ($packing_count == $packing_row_count)? '':'d-none' }}" title="Add" ><i class="la la-check"></i></span>
                                                                        @if($pkey > 0)
                                                                        <span row="{{$pkey}}" class="packing_type_row_delete btn btn-sm btn-outline-danger"><i class="la la-trash"></i></span>
                                                                        @endif
                                                                   
                                                                </div>
                                                            </div>
                                                            @php  $packing_row_count++ @endphp
                                                            @endforeach
                                                            @else
                                                                 
                                                                <div class="row packing_type_row" id="packing_type_row0">
                                                                    <input type="hidden" id="packing_type_input0" name="packing_type[0]">
                                                                    <div class="col-md-2">
                                                                        <fieldset class="form-group">
                                                                            <select class="select2 form-control packing_type" name="packing_type[0]" data-rule-required="true" data-msg-required="This field is required" disabled="disabled">
                                                                            </select>
                                                                        </fieldset>
                                                                    </div>
                                                                    <input type="hidden" id="packing_size_input0" name="packing_size[0]">
                                                                    <div class="col-md-2">
                                                                        <fieldset class="form-group">
                                                                            <select class="select2 form-control packing_size" name="packing_size[0]" data-rule-required="true" data-msg-required="This field is required" disabled="disabled">
                                                                            </select>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <fieldset class="form-group">
                                                                            <input name="packing_charges[0]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control amount" placeholder="Charges" disabled="disabled">
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                       <span id="packing_type_add" class="btn btn-sm btn-outline-primary d-none" title="Add" ><i class="la la-check"></i></span>
                                                                        <span class="packing_type_row_delete btn btn-sm btn-outline-danger d-none"><i class="la la-trash"></i></span>
                                                                        
                                                                    </div>
                                                                </div>
                                                                    
                                                                
                                                            @endif
                                                        </div>
                                                        
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-2">
                                                            <h3 class="card-title">Labelling Charges</h3>
                                                        </div>
                                                        <div class="col-3">
                                                            <div class="form-group ">
                                                                <input type="checkbox" name="labelling_charges_switch" class="switchery labellingSwitch" data-color="info" data-size="sm" {{ ($wms_user_info->labelling_charges)? 'checked':''}}/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                        
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <fieldset class="form-group">
                                                                <input name="labelling_charges" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control amount" placeholder="Charges" value="{{ ($wms_labelling_charges)? $wms_labelling_charges->charges:0 }}" {{ ($wms_user_info->labelling_charges)? '':'disabled'}}>
                                                            </fieldset>
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
                                                                    
                                                                        <span id="packing_type_add" class="btn btn-sm btn-outline-primary d-none" title="Add" ><i class="la la-check"></i></span>
                                                                        <span class="packing_type_row_delete btn btn-sm btn-outline-danger d-none"><i class="la la-trash"></i></span>
                                                                    
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
                                        
                                    </div>
                                 @endisset 
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

                            <div class="row justify-content-center mt-2" id="commission_div">
                                <div class="form-group row">
                                    <label class="col-md-4 label-control" for="commission">Total Commission</label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Total Commission" id="commission_max" name="commission_max" value="{{$commission_percentage}}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div id="add_user_commission_form" class="form mb-1 justify-content-center">
                                        <div class="row justify-content-center">
                                            <div class="col-2 form-group">
                                                <select name="sales_tier" class="select2" id="sales_tier_select">
                                                    @foreach($sales_tiers as $tier)
                                                        <option value="{{ $tier->id }}" type="{{$tier->tier_type}}" sales="{{$tier->sales_status}}">{{ $tier->tier_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-3 form-group">
                                                <input type="text" id="external_person_name" name="external_person_name" class="form-control" placeholder="External Tier Person Name" disabled data-rule-required="true" data-msg-required="Person Name is required">
                                            </div>
                                            <div class="col-2 form-group">
                                                <select name="user" class="select2" id="user_select" disabled>
                                                </select>
                                            </div>
                                            <div class="col-3 form-group">
                                                <div class="input-group form-group">
                                                    <input type="text" id="user_commission" class="form-control commission" placeholder="User Commission" name="user_commission">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-1 form-group">
                                                <button type="button" class="btn btn-primary" id="commission_add_button">Add</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                        <thead>
                                        <tr role="row" class="bg-primary white">
                                            <th class="border-primary border-darken-1">S. No.</th>
                                            <th class="border-primary border-darken-1">User Name</th>
                                            <th class="border-primary border-darken-1">Tier</th>
                                            <th class="border-primary border-darken-1">Commission Percentage</th>
                                            <th class="border-primary border-darken-1"></th>
                                        </tr>
                                        </thead>
                                        <tfoot>
                                        <input type="hidden" value="0" name="total_commission" id="total_commission">
                                        <input type="hidden" value="0" name="edit_commission" id="edit_commission">
                                        <tr><th colspan="3" style="text-align:right" rowspan="1">Total Commission:</th><th rowspan="1" colspan="2"><span id="total_commission_value">0</span>%</th></tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="row mt-2 justify-content-center">
                                <div class="col-5 form-group">
                                    <textarea name="rate_remarks" id="rate_remarks" class="form-control" placeholder="Rate Remarks..." rows="3"></textarea>
                                </div>
                                
                            </div> 

                            <div class="text-center mt-2">
                                <div class="form-group">
                                    <input type="hidden" id="authorize" name="authorize">
                                    <input type="hidden" name="approve" id="approve">
                                    <input type="hidden" name="approve_change_rate_type" id="approve_change_rate_type">
                                    <button id="addRatesSubmit" type="submit" class="btn btn-outline-success round btn-min-width mr-1 mb-1">Update Rates</button>

                                    @if (($shipper->status == 1 || $shipper->status == 5) && (session('role_id') == 1 || in_array(8, session('permissions'))))
                                        <button id="accountActiveSubmit" type="submit" class="btn btn-outline-primary round btn-min-width mr-1 mb-1">Authorize</button>
                                    @endif

                                    @if ($shipper->rate_status ==1 && $shipper->status == 3 && (session('role_id') == 1 || in_array(140, session('permissions'))) || $shipper->status == 3 && $shipper->rate_type_id_status == 1)
                                        <button id="accountApproveActiveSubmit" type="submit" class="btn btn-outline-primary round btn-min-width mr-1 mb-1">Approve</button>
                                    @endif
                                    @if (($shipper->rate_status ==0 && ($shipper->status == 1 || $shipper->status == 5) && (session('role_id') == 1 || in_array(8, session('permissions'))))|| ($shipper->rate_status ==1 && (session('role_id') == 1 || in_array(140, session('permissions')))) || $shipper->status == 3 && $shipper->rate_type_id_status == 1)
                                        <button id="accountRejectActiveSubmit" type="button" class="btn btn-outline-danger round btn-min-width mr-1 mb-1">Reject Rates</button>
                                    @endif
                                 {{--   @if ($shipper->new_rate_type_id != null && $shipper->status == 3 && $shipper->rate_type_id_status == 1 && (session('role_id') == 1 || in_array(140, session('permissions'))))
                                        <button id="accountApproveChangeSubmit" type="submit" class="btn btn-outline-primary round btn-min-width mr-1 mb-1">Approve Change</button>
                                    @endif
                                    @if (($shipper->new_rate_type_id != null && ($shipper->status == 3 && $shipper->rate_type_id_status == 1) && (session('role_id') == 1 || in_array(8, session('permissions')))) && (session('role_id') == 1 || in_array(140, session('permissions')))))
                                        <button id="accountRejectActiveSubmit" type="button" class="btn btn-outline-danger round btn-min-width mr-1 mb-1">Reject Change</button>
                                    @endif--}}
                                </div>
                            </div>

                            

                        </form>

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
{{--    <div class="modal fade text-left" id="UserDocumentModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="UserDocumentModal"--}}
{{--         aria-hidden="true">--}}
{{--        <div class="modal-dialog modal-md" role="document">--}}
{{--            <div class="modal-content">--}}
{{--                <div class="modal-header">--}}
{{--                    <h4 class="modal-title" id="">Document Attachment</h4>--}}
{{--                </div>--}}
{{--                <div class="modal-body">--}}
{{--                    <form id="user_document_form" novalidate="novalidate" enctype="multipart/form-data">--}}
{{--                        @csrf--}}
{{--                        <input type="hidden" name="user_id" id="user_document_user_id" value="{{$shipper->id}}">--}}
{{--                        <input type="hidden" name="doc_upload" id="doc_upload" value="0">--}}
{{--                        <input type="hidden" name="shipper_status" id="shipper_status" value="{{$shipper->status}}">--}}
{{--                        <div class="col form-group">--}}
{{--                            <label for="filled_and_signed_image">--}}
{{--                                Pdf of filled and signed document:--}}
{{--                            </label>--}}
{{--                            <input class="form-control form-control-sm" type="file" name="filled_and_signed_pdf" id="filled_and_signed_pdf" data-rule-accept="application/pdf" data-msg-accept="Only Pdf file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5,120‬ KB).">--}}
{{--                        </div>--}}
{{--                        <div class="col form-group">--}}
{{--                            <label for="signed_acknowledgement_image">--}}
{{--                                Pdf of signed Acknowledgement form:--}}
{{--                            </label>--}}
{{--                            <input class="form-control form-control-sm" type="file" name="signed_acknowledgement_pdf" id="signed_acknowledgement_pdf" data-rule-accept="application/pdf" data-msg-accept="Only Pdf file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5,120‬ KB).">--}}
{{--                        </div>--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--                <div class="modal-footer">--}}
{{--                    <button type="button" class="btn btn" data-dismiss="modal">Cancel</button>--}}
{{--                    <button type="button" class="btn btn-danger" id="UserDocumentSubmit">Upload</button>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

        @else
            <h1>Standard rates not set.</h1>
        @endif
    @else
        <h1>Shipper does not exist.</h1>
    @endif
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

    <style type="text/css">
        .hide{
            display:none;
        }
        .changed{
            background-color: #F7F087;
        }
        .new{
            background-color: #78FF67;
        }
    </style>


@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            //Hub to Hub Switches
            $('#on_hub_to_hub_switch').on('change',function(){
                var onhub_to_hub_switch = document.querySelector('.switchery.on_hub_to_hub_switch');
                if (onhub_to_hub_switch.checked === true) {
                    $('input[name="on_hub_mcw_charges"]').attr('disabled', false);
                    $('.on_hub_weight_row input').attr('disabled', false);
                    $('input[name="on_hub_range_up[1]"]').attr('disabled', true);
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
                    $('input[name="ol_hub_range_up[1]"]').attr('disabled', true);
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
                    $('input[name="detain_hub_range_up[1]"]').attr('disabled', true);
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
                    $('input[name="sameday_hub_range_up[1]"]').attr('disabled', true);
                    $('#sameday_hub_weightadd').attr('disabled', false);
                } else if (sameday_hub_to_hub_switch.checked === false) {
                    $('.sameday_hub_weight_row input').attr('disabled', true);
                    $('#sameday_hub_weightadd').attr('disabled', true);
                    $('input[name="sameday_hub_mcw_charges"]').attr('disabled', true);

                }
            });

            //hub to hub end
            $('#on_dws_weight').select2({
                placeholder: "Select Weight Type",
                width:'100%'
            });
            $('#ol_dws_weight').select2({
                placeholder: "Select Weight Type",
                width:'100%'
            });
            $('#detain_dws_weight').select2({
                placeholder: "Select Weight Type",
                width:'100%'
            });
            $('#sameday_dws_weight').select2({
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

        //sales tier
        var selected_users = [];
        var users = @json($users);
        var users_data = $.map(users, function (obj) {
            obj.id = obj.id || obj.text;
            return obj;
        });
        $('#user_select').prepend('<option value="" selected></option>').select2({
            placeholder: "Select User",
            width:'100%'
        }).bind('change', function () {
            var id = $(this).val();

            var index = $.inArray(id, selected_users);
            if (index !== -1) {
                var error = 'User previously selected!';
                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                $('#user_select').val(null).trigger('change');
            }
        });
        $('#user_select').select2({data:users_data,placeholder:'Select User'});

        $('#sales_tier_select').prepend('<option value="" selected></option>').select2({
            placeholder: "Select Sales Tier",
            width:'100%'
        }).bind('change', function () {
            $('#user_select').attr('disabled', true);
            $('#external_person_name').attr('disabled', true);
            var type = $(this).find(":selected").attr('type');
            if(type == 1){
                $('#user_select').attr('disabled', false);
            }else{
                $('#external_person_name').attr('disabled', false);
            }

        });

        var commission_max = $('#commission_max').val();
        $('.commission').inputmask({
            'alias': 'decimal',
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            'digits': 2,
            'min': 0.00,
            'max': commission_max,
        });

        var table = $('#datatable').DataTable({
            dom: 'ltipr',
            paging: false,
            ordering:false,
            sorting:false,
            bInfo:false,
            columns: [
                {
                    orderable: false,
                    searchable: false,
                    name: 'serial_number',
                    class: 'align-middle serial_number',
                    targets: 1,
                    render: function (data, type, row) {
                        return '';
                    }
                },
                { name: 'user_name', class: 'align-middle user_name'},
                { name: 'tier', class: 'align-middle tier'},
                { name: 'commission_percentage', class: 'align-middle commission_percentage'},
                { name: 'action', class: 'align-middle action'}

            ],
            rowCallback: function (row, data, index) {
                var info = table.page.info();

                $('td:eq(0)', row).html(index + 1 + info.page * info.length);

            },
        });

        $('body').on('change','#external_person_name',function() {
            $(this).val($(this).val().trim());
        });


        var row = 1;
        var selected_commission = 0;
        function add_commission_row(tier_id, tier_name, tier_type, user_id, user_name, commission){
            var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-danger remove"><i class="la la-close"></i></a>';
            var tier = '<div><input type="hidden" name="tier_id['+ row +']"  value="'+ tier_id +'">'+ tier_name +'</div>';
            if(tier_type == 1){
                var name = '<div><input type="hidden" name="user_id['+ row +']"  value="'+ user_id +'">'+ user_name +'</div>';
            }else{
                var name = '<div><input type="hidden" name="user_id['+ row +']"  value="'+ user_name +'">'+ user_name +'</div>';
            }
            var commission_percentage = '<div><input type="hidden" name="commission_percentage['+ row +']"  value="'+ commission +'">'+ commission +'%</div>';
            table.row.add([row,name,tier,commission_percentage,remove]).node().id = row;
            table.draw(false);
            if(tier_type == 1){
                selected_users.push(user_id.toString());
            }
            $('#commission_add_button').attr('disabled', false);
            $('#total_commission_value').html(selected_commission);
            $('#total_commission').val(selected_commission);
            row++;
        }

        var existing_commissions = @json($existing_commission_array);
        existing_commissions.forEach(function(existing_commission){
            selected_commission = roundToTwo(selected_commission + existing_commission['commission']);
            add_commission_row(existing_commission['tier_id'], existing_commission['tier_name'], existing_commission['tier_type_id'], existing_commission['user_id'], existing_commission['user_name'], existing_commission['commission']);
        });
        function roundToTwo(num) {
            return +(Math.round(num + "e+2")  + "e-2");
        }
        $('#commission_add_button').on('click', function () {
            var commission = parseFloat($('#user_commission').val());
            var this_btn = $(this);

            var flag = true;
            var type = $('#sales_tier_select').find(":selected").attr('type');
            if(!$('#sales_tier_select').valid()){
                flag = false;
            }
            if(type == 1){
                if(!$('#user_select').valid()){
                    flag = false;
                }
            }
            if(type == 2){
                if(!$('#external_person_name').valid()){
                    flag = false;
                }
            }
            if(!$('#user_commission').valid()){
                flag = false;
            }
            if($('#sales_tier_select').val() == '' || $('#sales_tier_select').val() == null){
                var error = 'Please select Sales Tier!';
                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                flag = false;
            }
            if($('#sales_tier_select').val() != '' || $('#sales_tier_select').val() != null){
                if($('#sales_tier_select').find(":selected").attr('type') == 1){
                    if($('#user_select').val() == '' || $('#user_select').val() == null){
                        var error = 'Please select User!';
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        flag = false;
                    }
                }
            }
            if($('#user_commission').val() == '' || $('#user_commission').val() == null){
                var error = 'Please set commission!';
                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                flag = false;
            }
            if(flag){
                if(commission <= commission_max){
                    selected_commission = roundToTwo(selected_commission + commission);
                    commission_max = commission_max - commission;
                    this_btn.attr('disabled', true);
                    var user_id = '';
                    var user_name = '';
                    var tier_id = '';
                    var tier_name = '';
                    var tier_type = '';
                    tier_id = $('#sales_tier_select').val();
                    tier_name = $('#sales_tier_select').find(":selected").text();
                    tier_type = $('#sales_tier_select').find(":selected").attr('type');
                    if(tier_type == 1){
                        user_id = $('#user_select').val();
                        user_name = $('#user_select').find(":selected").text();
                    }else{
                        user_name = $('#external_person_name').val();
                    }

                    add_commission_row(tier_id, tier_name, tier_type, user_id, user_name, commission);
                    $('#sales_tier_select').val(null).trigger('change');
                    $('#user_select').val(null).trigger('change');
                    $('#user_select').attr('disabled', true);
                    $('#external_person_name').val('');
                    $('#external_person_name').attr('disabled', true);
                    $('#user_commission').val('');
                    $('#edit_commission').val(1);

                }else{
                    var error = 'Selected Commission value exceeds!';
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
            }
        });
        $('#datatable tbody').on('click', 'tr td.action a.remove', function() {
            var id = $(this).parents('tr').attr('id');

            var user_id = $('input[name="user_id['+ id +']"]').val();
            if(user_id){
                var index = $.inArray(user_id, selected_users);
                if (index !== -1) {
                    selected_users.splice(index, 1);
                }
            }
            var commission =  parseFloat($('input[name="commission_percentage['+ id +']"]').val());
            commission_max = roundToTwo(commission_max + commission);
            selected_commission = roundToTwo(selected_commission - commission);
            $('#total_commission_value').html(selected_commission);
            $('#total_commission').val(selected_commission);
            table.row( $(this).parents('tr') ).remove().draw();
        });
        //sales tier end

        function textAreaAdjust(o) {
            o.style.height = "1px";
            o.style.height = (25+o.scrollHeight)+"px";
        }

        $('#accountRejectActiveSubmit').click(function() {
            $('#RejectRatesModal').modal('show');
        });
        $('#RejectRatesSubmit').on('click',function () {
            var shipper = $('#shipper_id').val();
            var reject_reason = document.getElementById('reject_reason').value;
            if(reject_reason){
                $.ajax({
                    url: '{!! route('admin.corporate.rejectreason.submit') !!}',
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
        $('#accountApproveChangeSubmit').on('click',function(){
            $('#approve_change_rate_type').val(1);
        });

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
        var packagingChargesSwitch = document.querySelector('.switchery.packagingChargesSwitch');




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

            let htmdiv = '<div class="row on_door_weight_row" id="on_door_weight_row'+on_door_count+'"><input type="hidden" name="on_door_weight_record['+on_door_count+']" value=""><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="on_door_range_up['+on_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="on_door_range_down['+on_door_count+']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionDoorOvernight'+on_door_count+'" data-color="success" data-size="sm" name="on_door_wa_switch['+on_door_count+']"/></div></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required" value="" name="on_door_local_charges['+on_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required"  name="on_door_class_0_charges['+on_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="on_door_class_1_charges['+on_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="on_door_class_2_charges['+on_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="on_door_class_3_charges['+on_door_count+']"></fieldset></div><div class="col-1">\n' +
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

            let htmdiv = '<div class="row on_hub_weight_row" id="on_hub_weight_row'+on_hub_count+'"><input type="hidden" name="on_hub_weight_record['+on_hub_count+']" value=""><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="on_hub_range_up['+on_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="on_hub_range_down['+on_hub_count+']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionHubOvernight'+on_hub_count+'" data-color="success" data-size="sm" name="on_hub_wa_switch['+on_hub_count+']"/></div></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required" value="" name="on_hub_local_charges['+on_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required"  name="on_hub_class_0_charges['+on_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="on_hub_class_1_charges['+on_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="on_hub_class_2_charges['+on_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="on_hub_class_3_charges['+on_hub_count+']"></fieldset></div><div class="col-1">\n' +
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
        // packagingChargesSwitch.onchange = function () {
        //     if(packagingChargesSwitch.checked === true){
        //         $('#packaging_material_charges_div').slideDown('slow');
        //         $('.packaging-charges-div-overnight').find('input').prop('disabled',false);
        //     }else if(packagingChargesSwitch.checked === false){
        //         $('#packaging_material_charges_div').slideUp('slow');
        //         $('.packaging-charges-div-overnight').find('input').prop('disabled',true);
        //
        //     }
        // };

        {{--@if(count($packaging_material_types) > 0)--}}
        {{--    @foreach($packaging_material_types as $index => $type)--}}
        {{--        var PackageSwitch = [];--}}
        {{--        var type_id_{{$index}} = '{{$type->id}}';--}}
        {{--        var type_id = '{{$type->id}}';--}}
        {{--        PackageSwitch[type_id] = document.querySelector('.packaging_type_'+type_id);--}}
        {{--        PackageSwitch[type_id].onchange = function () {--}}

        {{--            if ($(this).is(':checked') === true) {--}}
        {{--                $('#package_type_'+type_id_{{$index}}).slideDown('slow');--}}

        {{--            } else if ($(this).is(':checked') === false) {--}}
        {{--                $('#package_type_'+type_id_{{$index}}).slideUp('slow');--}}

        {{--            }--}}
        {{--        };--}}
        {{--    @endforeach--}}
        {{--@endif--}}

        //Overland
        //var weightAdditionOverland = document.querySelector('.switchery.weightAdditionOverland0');
        var cashhandlingswitchOverland = document.querySelector('.switchery.cashChargesOverland');
        var insuranceChargesSwitchOverland = document.querySelector('.switchery.insuranceChargesoverland');
        var returnChargesSwitchOverland = document.querySelector('.switchery.returnChargesOverland');
        var fuelChargesSwitchOL = document.querySelector('.switchery.fuelSurchargeOverland');

        //Overland


        var overland_door_count = $('.ol_door_weight_row').length + 1;
        $('body').on('click','#overland_door_weightadd',function () {

            let htmdiv1 = '<div class="row ol_door_weight_row" id="ol_door_weight_row'+overland_door_count+'"><input type="hidden" name="ol_door_weight_record['+overland_door_count+']" value=""><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_door_range_up['+overland_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_door_range_down['+overland_door_count+']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionDoorOverland'+overland_door_count+'" data-color="success" data-size="sm" name="ol_door_wa_switch['+overland_door_count+']"/></div></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_door_local_charges['+overland_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required"  name="ol_door_class_0_charges['+overland_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="ol_door_class_1_charges['+overland_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="ol_door_class_2_charges['+overland_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="ol_door_class_3_charges['+overland_door_count+']"></fieldset></div><div class="col-1">\n' +
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


            $("#ol_door_weight_row"+overland_door_count+" .validated").each(function(){
                $( this ).rules( "add", {
                    required: true,
                });

            });
            overland_door_count++;
        });
        var overland_hub_count = $('.ol_hub_weight_row').length + 1;
        $('body').on('click','#overland_hub_weightadd',function () {

            let htmdiv1 = '<div class="row ol_hub_weight_row" id="ol_hub_weight_row'+overland_hub_count+'"><input type="hidden" name="ol_hub_weight_record['+overland_hub_count+']" value=""><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_hub_range_up['+overland_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_hub_range_down['+overland_hub_count+']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionHubOverland'+overland_hub_count+'" data-color="success" data-size="sm" name="ol_hub_wa_switch['+overland_hub_count+']"/></div></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_hub_local_charges['+overland_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required"  name="ol_hub_class_0_charges['+overland_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="ol_hub_class_1_charges['+overland_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="ol_hub_class_2_charges['+overland_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="ol_hub_class_3_charges['+overland_hub_count+']"></fieldset></div><div class="col-1">\n' +
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

            let htmdiv1 = '<div class="row detain_door_weight_row" id="detain_door_weight_row'+detain_door_count+'"><input type="hidden" name="detain_door_weight_record['+detain_door_count+']" value=""><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_door_range_up['+detain_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_door_range_down['+detain_door_count+']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionDoorDetain'+detain_door_count+'" data-color="success" data-size="sm" name="detain_door_wa_switch['+detain_door_count+']"/></div></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_door_local_charges['+detain_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required"  name="detain_door_class_0_charges['+detain_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="detain_door_class_1_charges['+detain_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="detain_door_class_2_charges['+detain_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="detain_door_class_3_charges['+detain_door_count+']"></fieldset></div><div class="col-1">\n' +
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

            let htmdiv1 = '<div class="row detain_hub_weight_row" id="detain_hub_weight_row'+detain_hub_count+'"><input type="hidden" name="detain_hub_weight_record['+detain_hub_count+']" value=""><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_hub_range_up['+detain_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_hub_range_down['+detain_hub_count+']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionHubDetain'+detain_hub_count+'" data-color="success" data-size="sm" name="detain_hub_wa_switch['+detain_hub_count+']"/></div></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_hub_local_charges['+detain_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required"  name="detain_hub_class_0_charges['+detain_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="detain_hub_class_1_charges['+detain_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="detain_hub_class_2_charges['+detain_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="detain_hub_class_3_charges['+detain_hub_count+']"></fieldset></div><div class="col-1">\n' +
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

        //sameday


        var sameday_door_count = $('.sameday_door_weight_row').length + 1;
        $('body').on('click','#sameday_door_weightadd',function () {

            let htmdiv1 = '<div class="row sameday_door_weight_row" id="sameday_door_weight_row'+sameday_door_count+'"><input type="hidden" name="sameday_door_weight_record['+sameday_door_count+']" value=""><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_door_range_up['+sameday_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_door_range_down['+sameday_door_count+']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionDoorSameday'+sameday_door_count+'" data-color="success" data-size="sm" name="sameday_door_wa_switch['+sameday_door_count+']"/></div></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_door_local_charges['+sameday_door_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required"  name="sameday_door_class_0_charges['+sameday_door_count+']"></fieldset></div><div class="col-1">\n' +
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

            let htmdiv1 = '<div class="row sameday_hub_weight_row" id="sameday_hub_weight_row'+sameday_hub_count+'"><input type="hidden" name="sameday_hub_weight_record['+sameday_hub_count+']" value=""><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_hub_range_up['+sameday_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_hub_range_down['+sameday_hub_count+']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionHubSameday'+sameday_hub_count+'" data-color="success" data-size="sm" name="sameday_hub_wa_switch['+sameday_hub_count+']"/></div></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_hub_local_charges['+sameday_hub_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required"  name="sameday_hub_class_0_charges['+sameday_hub_count+']"></fieldset></div><div class="col-1">\n' +
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
        var storage_type_selected = [];
        var packing_type_selected = [];
        var packing_size_selected = [];
        var packing_material_data = @json($packaging_material_types);
        var packing_sizes = @json($packaging_material_type_sizes);
        var packing_data = $.map(packing_material_data, function (obj) {
                obj.id = obj.id;
                obj.text = obj.type;
                return obj;
            });
        @if(!empty($wms_user_info))
            $('#invoicing_cycle_select').val({{$wms_user_info->invoicing_cycle}}).trigger('change');
                @foreach($wms_storage_charges as $skey => $storage)
                    $('select[name="storage_type[{{$skey}}]"]').select2({
                        width:'100%',
                        placeholder:'Select Storage Type'
                    });
                    $('select[name="storage_type[{{$skey}}]"]').val({{$storage->storage_type_id}}).trigger('change');
                    storage_type_selected.push('{{$storage->storage_type_id}}');
                @endforeach
                @if(count($wms_packing_charges) > 0)
                console.log('here')
                    @foreach($wms_packing_charges as $indx => $packing)
                    $('select[name="packing_type[{{$indx}}]"]').select2({
                        width:'100%',
                        placeholder:'Select Packing Type'
                    });
                    $('select[name="packing_type[{{$indx}}]"]').val({{$packing->packing_type_id}}).trigger('change');
                    packing_type_selected.push('{{$packing->packing_type_id}}');
                    $('select[name="packing_size[{{$indx}}]"]').select2({
                        width:'100%',
                        placeholder:'Select Packing Size'
                    });
                    $('select[name="packing_size[{{$indx}}]"]').val({{$packing->packing_size_id}}).trigger('change');
                    packing_size_selected.push('{{$packing->packing_size_id}}');
                    @endforeach
                @endif
            @else
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
            $('select[name="packing_size[0]"]').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:'Select Packing Size'
            }).bind('select2:select', function(){
                $(this).parents('div.packing_type_row').find('span#packing_type_add').removeClass('d-none');
                $('input[name="packing_size[0]"]').val($(this).val());

            });
            
           

        @endif
        var current_selection = null;
        var storage_type_data = @json($storage_types);
         var storage_data = $.map(storage_type_data, function (obj) {
                obj.id = obj.id;
                obj.text = obj.name;
                return obj;
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
        
        @if(count($wms_storage_charges) > 0)
        var storage_type_rows = {{ count($wms_storage_charges) }};
        @else
        var storage_type_rows = 1;
        @endempty
        
        

        $('body').on('click','#storage_type_add', function(){
            
            var previous_row = storage_type_rows - 1;
              
            $(this).addClass('d-none');
            var previous_select = $('select[name="storage_type['+ previous_row +']"]');
            var index = $.inArray(previous_select.val(), storage_type_selected);
            if(index === -1){
                storage_type_selected.push(previous_select.val());
            }
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


        @if(count($wms_packing_charges) > 0)
        var packing_type_rows = {{ count($wms_packing_charges) }};
        @else
        var packing_type_rows = 1;
        @endif
       
        
        @isset($wms_user_info->warehousing)
        @if(!$wms_user_info->packing_charges)

            $('select[name="packing_type[0]"]').prepend('<option value="" selected="selected"></option>').select2({
                data:packing_data,
                width:'100%',
                placeholder:'Select Packing Type'
            }).bind('select2:select', function(){
                $('input[name="packing_type[0]"]').val($(this).val());
                var packing_sizes_data = $.map(packing_sizes[$(this).val()], function (obj) {
                obj.id = obj.id;
                obj.text = obj.size;
                return obj;
                });
                $('select[name="packing_size[0]"]').empty().select2({data:packing_sizes_data, placeholder: 'Select Packing Size'}).val(null).trigger('change');
            });
            $('select[name="packing_size[0]"]').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:'Select Packing Size'
            }).bind('select2:select', function(){
               var packing_size = $(this).val();
                $(this).parents('div.packing_type_row').find('input#packing_size_input0').val(packing_size);
                $(this).parents('div.packing_type_row').find('span#packing_type_add').removeClass('d-none');

            });
        $('input[name="packing_charges[0]"]').inputmask({
            'alias': 'decimal',
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            'digits': 2,
            'min': 0.00,
            'max': 1000000.00
        });
        @endif
        @endisset
        var packingSwitch = document.querySelector('.switchery.packingCharges');
        packingSwitch.onchange = function () {
            if(packingSwitch.checked === true){
                $('#wms_packing_charges_div input, #wms_packing_charges_div select').prop('disabled', false);
            }else if(packingSwitch.checked === false){
                $('#wms_packing_charges_div input, #wms_packing_charges_div select').prop('disabled', true);
            }
        };

        
        
        $('body').on('click','#packing_type_add', function(){
            var previous_row = packing_type_rows - 1;
            $(this).addClass('d-none');

            var previous_type = $('select[name="packing_type['+ previous_row +']"]');
            var previous_size = $('select[name="packing_size['+ previous_row +']"]');
            var index = $.inArray(previous_type.val(), packing_type_selected);
            packing_size_selected.push(previous_size.val());
            
            previous_type.prop('disabled', true);
            previous_size.prop('disabled', true);

            
            // if(packing_data.length !== 0){
                var htmldiv = '<div class="row packing_type_row" id="packing_type_row'+packing_type_rows+'">\n' +
                '                                                <input id="packing_type_input'+ packing_type_rows +'" type="hidden" name="packing_type['+ packing_type_rows +']" value=""><div class="col-md-2">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <select class="select2 form-control packing_type" name="packing_type['+ packing_type_rows +']" data-rule-required="true" data-msg-required="This field is required"></select>\n' +
                '                                                    </fieldset>\n' +
                '                                                </div>\n' +
                '                                               <input id="packing_size_input'+ packing_type_rows +'" type="hidden" name="packing_size['+ packing_type_rows +']" value=""><div class="col-md-2">\n' +
                '                                                    <fieldset class="form-group">\n' +
                '                                                        <select class="select2 form-control packing_size" name="packing_size['+ packing_type_rows +']" data-rule-required="true" data-msg-required="This field is required"></select>\n' +
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
                $('select[name="packing_size['+ last_id +']"]').empty().select2({data:packing_sizes_data, placeholder: 'Select Packing Size'}).val(null).trigger('change');
            });
             $('select[name="packing_size['+ packing_type_rows +']"]').select2({
                width:'100%',
                placeholder:'Select Packing Size'
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
            // }
            

        });

        $('body').on('click','span.packing_type_row_delete', function(){
            var row_id = $(this).parent().parent().attr('row'); 
            var size_selected = $('select[name="packing_size['+ row_id +']"]').val();
            if(size_selected !== ''){
                var index = $.inArray(size_selected, packing_size_selected);
                if(index !== -1){
                    packing_size_selected.splice(index, 1);
                }
            }
            $(this).parent().parent().remove();
        });



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
            ignore:":not(:visible),:disabled",
            errorClass:"danger",
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parent('.form-group'));
            },
            submitHandler: function(form) {
                if (overnightSwitch.checked == true || overlandSwitch.checked == true || detainSwitch.checked == true || samedaySwitch.checked == true) {
                    // if($('#authorize').val() != 1 && $('#approve').val() != 1 && $('#doc_upload').val() == 0 && $('#shipper_status').val() == 3){
                    //     $('#UserDocumentModal').modal('show');
                    // }
                    // else{
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
                            var msg = "";
                            if($('#authorize').val() == 1){
                                msg = "Rates are being authorized!"
                            }else{
                                msg = 'Rates are being updated!';
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
                        // }
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
        $('#UserDocumentSubmit').on('click',function () {
            var pdf_of_filled_and_signed_document= $('#filled_and_signed_pdf').val();
            var pdf_of_signed_acknowledgment = $('#signed_acknowledgement_pdf').val();
            if(pdf_of_filled_and_signed_document && pdf_of_signed_acknowledgment){
                var formData = new FormData($('#user_document_form')[0]);
                $.ajax({
                    url: '{!! route('admin.corporate.edit.user_documents') !!}',
                    method: 'POST',
                    enctype: 'multipart/form-data',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                })
                    .done(function(data) {
                        $('#UserDocumentModal').modal('hide');
                        $('#doc_upload').val(1);
                        $('#ratesAdditionForm').submit();
                    });
            }else{
                if(!pdf_of_filled_and_signed_document){
                    var error = "Please attach Pdf of filled and signed documents!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
                if(!pdf_of_signed_acknowledgment){
                    var error = "Please attach Pdf of signed Acknowledment!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
            }
        });

        $("#packaging_invoice_toggle").on('change', function(){
            if($("#packaging_invoice_toggle").is(":checked")){
                $('#packaging_invoice').val('on');
            }else{
                $('#packaging_invoice').val('off');
            }
        });

    </script>
@endsection