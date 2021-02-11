@extends('admin.layout.master')

@section('title', 'Edit International Rates')

@section('content')
    <h1>Edit International Rates</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <h2 class="font-large-1">{{$shipper->name}}
                            @if($shipper->account_type_id == 1)
                                <div class="badge badge-success pull-right">Reimbursement Account</div>
                            @else
                                <div class="badge badge-success pull-right">Corporate Invoicing Account</div>
                            @endif
                        </h2>
                        @include('admin.inc.messages')
                    </div>

                    <form id="ratesAdditionForm" class="card-body card-dashboard" action="{{route('admin.international.rates.edit.submit')}}" method="post" novalidate="novalidate">
                        @csrf
                        <input type="hidden" name="shipper_id" id="shipper_id" value="{{$shipper->id}}">
                        <div class="card-content">
                            <div class="box_parent_div">
                                @foreach($rate_statuses as $index => $rate_status)
                                    <div class="parent_box_div_{{$rate_status->box_id}}">
                                        <div class="card-header border-success">
                                            <input type="hidden" name="box_ids[]" value="{{$rate_status->box_id}}">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    @php
                                                        $count = $index + 1;
                                                    @endphp
                                                    <h3 class="display-inline card-title lead success">International Rates {{$count}}</h3>
                                                </div>
                                                @if($rate_status->box_id != 1)
                                                    <div class="col-md-6 text-right">
                                                        <span class="btn btn-danger rounded btn-sm-width rate_box_close_{{$rate_status->box_id}}" box="{{$rate_status->box_id}}">
                                                            <i class="ft-trash"></i>
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="card border-success">
                                            <div class="card-content">
                                                <div class="card-body">
                                                <div class="row">
                                                    <div class="col-2">
                                                        <h3>Add Hubs</h3>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group card border-success p-2">
                                                            <select name="hubs[{{$rate_status->box_id}}][]" id="select_box_{{$rate_status->box_id}}" class="form-control select2" multiple="multiple" required data-rule-required="true" data-msg-required="This field is required">
                                                                @foreach($cities as $city)
                                                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="wa_rows_div_{{$count}}">
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
                                                        <div class="col text-center">
                                                            <label class="card-title">KG Range</label>
                                                        </div>
                                                        <div class="col text-center">
                                                            <label class="card-title">Local Charges</label>
                                                        </div>
                                                        <div class="col-1 text-center">
                                                        </div>
                                                    </div>
                                                    @if(isset($weight_charges))
                                                        @php
                                                            $w_index = 1;
                                                        @endphp
                                                        @foreach($weight_charges as $weight_charge)
                                                            <div class="row" id="wa_row_{{$rate_status->box_id}}">
                                                                @if($weight_charge->box_id == $rate_status->box_id)
                                                                    <div class="col text-center">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" name="range_up[{{$rate_status->box_id}}][{{$w_index}}]" value="{{$weight_charge->range_up}}">
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col text-center">

                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" name="range_down[{{$rate_status->box_id}}][{{$w_index}}]" value="{{$weight_charge->range_down}}">
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col text-center">

                                                                        <div class="form-group " style="padding-top: 8px;">
                                                                            <input type="checkbox" class="switchery wa_switch" data-color="success" data-size="sm" name="wa_switch[{{$rate_status->box_id}}][{{$w_index}}]" {{ ($weight_charge->weight_addition == 1) ? 'checked' : '' }}>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-2 text-center">

                                                                        <fieldset style="padding-top: 5px;">
                                                                            <div class="input-group input-group-sm form-group">
                                                                                <input type="text" class="touchspin-color input-sm spkg" {{ ($weight_charge->weight_addition == 1) ? '' : 'disabled' }} data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="spkg[{{$rate_status->box_id}}][{{$w_index}}]" data-rule-required="true" data-msg-required="This field is required" value="{{$weight_charge->spkg}}">
                                                                            </div>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col text-center">
                                                                        <fieldset class="form-group">
                                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" name="local_charges[{{$rate_status->box_id}}][{{$w_index}}]" value="{{$weight_charge->local_charges}}">
                                                                        </fieldset>
                                                                    </div>

                                                                    <div class="col-1">
                                                                    </div>
                                                                    @php
                                                                        $w_index++;
                                                                    @endphp
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    @endif

                                                </div>{{--weight addition div--}}
                                                <div>
                                                    <button type="button" class="btn btn-outline-success mr-1 wa_btn_{{$rate_status->box_id}}" title="Add more slabs"><i class="la la-plus"></i></button>
                                                </div>

                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <h3 class="card-title">Cash Handling Charges</h3>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group ">
                                                            <input type="checkbox" name="cash_handling_switch_{{$rate_status->box_id}}" id="cash_handling_switch_{{$rate_status->box_id}}" class="switchery cash_handling_switch_{{$rate_status->box_id}}" data-color="success" data-size="sm" {{ ($rate_status->cash_handling_charges == 1) ? 'checked' : '' }}/>
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

                                                @if(isset($cash_handling_charges))
                                                    @php
                                                        $ch_index = 1;
                                                    @endphp
                                                    <div class="cash-handling-div-{{$rate_status->box_id}} slabs">
                                                        @foreach($cash_handling_charges as $cash_handling_charge)
                                                            @if($cash_handling_charge->box_id == $rate_status->box_id)
                                                                <div class="row">
                                                                    <div class="col-md-2 text-center">
                                                                        <fieldset class="form-group">
                                                                            <input name="cash_range_up[{{$rate_status->box_id}}][{{$ch_index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric" value="{{$cash_handling_charge->range_up}}">
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col-md-2 text-center">
                                                                        <fieldset class="form-group">
                                                                            <input name="cash_range_down[{{$rate_status->box_id}}][{{$ch_index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric" value="{{$cash_handling_charge->range_down}}">
                                                                        </fieldset>
                                                                    </div>

                                                                    <div class="col-md-2 text-center">
                                                                        <fieldset class="form-group">
                                                                            <input name="cash_charges[{{$rate_status->box_id}}][{{$ch_index}}]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required" value="{{$cash_handling_charge->charges}}">
                                                                        </fieldset>
                                                                    </div>

                                                                    <div class="col">
                                                                    </div>
                                                                </div>
                                                                @php
                                                                    $ch_index++;
                                                                @endphp
                                                            @endif
                                                        @endforeach
                                                        @if($ch_index == 1)
                                                            <div class="row">
                                                                <div class="col-md-2 text-center">
                                                                    <fieldset class="form-group">
                                                                        <input name="cash_range_up[{{$rate_status->box_id}}][{{$ch_index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric">
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-2 text-center">
                                                                    <fieldset class="form-group">
                                                                        <input name="cash_range_down[{{$rate_status->box_id}}][{{$ch_index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric">
                                                                    </fieldset>
                                                                </div>

                                                                <div class="col-md-2 text-center">
                                                                    <fieldset class="form-group">
                                                                        <input name="cash_charges[{{$rate_status->box_id}}][{{$ch_index}}]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required">
                                                                    </fieldset>
                                                                </div>

                                                                <div class="col">
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                                <div class="cash-handling-btn">
                                                    <button type="button" class="btn btn-outline-success mr-1 add_more_cash_slabs_{{$rate_status->box_id}}" title="Add more slabs" ><i class="la la-plus"></i></button>
                                                </div>
                                                <hr>

                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <h3 class="card-title">Insurance Charges</h3>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group ">
                                                            <input type="checkbox" name="insurance_charges_switch_{{$rate_status->box_id}}" id="insurance_charges_switch_{{$rate_status->box_id}}" class="switchery insurance_charges_switch_{{$rate_status->box_id}}" data-color="success" data-size="sm" {{ ($rate_status->insurance_charges == 1) ? 'checked' : '' }}/>
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

                                                @if(isset($insurance_charges))
                                                    @php
                                                        $in_index = 1;
                                                    @endphp
                                                    <div class="insurance-charges-div-{{$rate_status->box_id}} slabs">
                                                        @foreach($insurance_charges as $insurance_charge)
                                                            @if($insurance_charge->box_id == $rate_status->box_id)
                                                                <div class="row">
                                                                    <div class="col-md-2 text-center">
                                                                        <fieldset class="form-group">
                                                                            <input name="ins_range_up[{{$rate_status->box_id}}][{{$in_index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric" value="{{$insurance_charge->range_up}}">
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col-md-2 text-center">
                                                                        <fieldset class="form-group">
                                                                            <input name="ins_range_down[{{$rate_status->box_id}}][{{$in_index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric" value="{{$insurance_charge->range_down}}">
                                                                        </fieldset>
                                                                    </div>

                                                                    <div class="col-md-2 text-center">
                                                                        <fieldset class="form-group">
                                                                            <input name="ins_charges[{{$rate_status->box_id}}][{{$in_index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control dec-percent" value="{{$insurance_charge->charges}}">
                                                                        </fieldset>
                                                                    </div>

                                                                    <div class="col">
                                                                    </div>

                                                                </div>

                                                                @php
                                                                    $in_index++;
                                                                @endphp
                                                            @endif
                                                        @endforeach
                                                        @if($in_index == 1)
                                                            <div class="row">
                                                                <div class="col-md-2 text-center">
                                                                    <fieldset class="form-group">
                                                                        <input name="ins_range_up[{{$rate_status->box_id}}][{{$in_index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric">
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-md-2 text-center">
                                                                    <fieldset class="form-group">
                                                                        <input name="ins_range_down[{{$rate_status->box_id}}][{{$in_index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric">
                                                                    </fieldset>
                                                                </div>

                                                                <div class="col-md-2 text-center">
                                                                    <fieldset class="form-group">
                                                                        <input name="ins_charges[{{$rate_status->box_id}}][{{$in_index}}]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control dec-percent">
                                                                    </fieldset>
                                                                </div>

                                                                <div class="col">
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                                <div class="insurance-charges-btn">
                                                    <button type="button" class="btn btn-outline-success mr-1 add_more_ins_slabs_{{$rate_status->box_id}}" title="Add more slabs"><i class="la la-plus"></i></button>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <h3 class="card-title">Return Charges</h3>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group ">
                                                            <input type="checkbox" name="return_charges_switch_{{$rate_status->box_id}}" id="return_charges_switch_{{$rate_status->box_id}}" class="switchery return_charges_switch_{{$rate_status->box_id}}" data-color="success" data-size="sm" {{ ($rate_status->return_charges == 1) ? 'checked' : '' }}/>
                                                        </div>
                                                    </div>
                                                </div>

                                                @if(isset($return_charges))
                                                    <div class="row return-charges-div-{{$rate_status->box_id}}">
                                                        @php
                                                            $return_check = false;
                                                        @endphp
                                                        @foreach($return_charges as $return_charge)
                                                            @if($return_charge->box_id == $rate_status->box_id)
                                                                <div class="col-3 text-center">
                                                                    <label class="card-title">Local Charges</label>
                                                                    <fieldset class="form-group">
                                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control amount" name="return_local_charges_{{$rate_status->box_id}}" value="{{$return_charge->local}}">
                                                                    </fieldset>
                                                                </div>
                                                                @php
                                                                    $return_check = true;
                                                                @endphp
                                                            @endif
                                                        @endforeach
                                                        @if($return_check == false)
                                                            <div class="col-3 text-center">
                                                                <label class="card-title">Local Charges</label>
                                                                <fieldset class="form-group">
                                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control amount" name="return_local_charges_{{$rate_status->box_id}}" disabled>
                                                                </fieldset>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="row return-charges-div-{{$rate_status->box_id}}">
                                                        <div class="col-3 text-center">
                                                            <label class="card-title">Local Charges</label>
                                                            <fieldset class="form-group">
                                                                <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control amount" name="return_local_charges_{{$rate_status->box_id}}">
                                                            </fieldset>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                                <hr>
                                                <div class="col">
                                                <div class="">
                                                    <h3 class="card-title">Discount Rates</h3>
                                                </div>
                                                @if(isset($discount_charges[$rate_status->box_id]))
                                                    <div class="row mt-1">
                                                        <div class="col-md-6">
                                                            <label class="">Title</label>
                                                            <div class='form-group'>
                                                                <input type='text' class="form-control" data-rule-required="true" data-msg-required="This field is required" name="discount_title_{{$rate_status->box_id}}" value="{{$discount_charges[$rate_status->box_id]['title']}}">
                                                            </div>

                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="">Apply [to - from]</label>
                                                            <div class='input-group form-group'>
                                                                <input type='text' class="form-control daterange" data-rule-required="true" data-msg-required="This field is required" name="daterange_{{$rate_status->box_id}}" value="{{$discount_charges[$rate_status->box_id]['date']}}/>
                                                                <div class="input-group-append">
                                                                <span class="input-group-text">
                                                                  <span class="la la-calendar"></span>
                                                                </span>
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
                                                                    <input type="checkbox" class="switchery discount_switch_{{$rate_status->box_id}}" name="discount_weight_switch_{{$rate_status->box_id}}" id="discount_weight_switch_{{$rate_status->box_id}}" data-size="xs" {{ ($discount_charges[$rate_status->box_id]['weight'] != null) ? 'checked' : '' }}/>
                                                                  </span>
                                                            </div>
                                                            <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent discount-inp" name="discount_weight_{{$rate_status->box_id}}" {{ ($discount_charges[$rate_status->box_id]['weight'] == null) ? 'disabled' : '' }} value="{{$discount_charges[$rate_status->box_id]['weight']}}">
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
                                                                    <input type="checkbox" name="discount_cash_switch_{{$rate_status->box_id}}" id="discount_cash_switch_{{$rate_status->box_id}}" class="switchery discount_switch_{{$rate_status->box_id}}" data-size="xs" {{ ($discount_charges[$rate_status->box_id]['cash'] != null) ? 'checked' : '' }}/>
                                                                  </span>
                                                            </div>
                                                            <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent discount-inp" name="discount_cash_{{$rate_status->box_id}}" {{ ($discount_charges[$rate_status->box_id]['cash'] == null) ? 'disabled' : '' }} value="{{$discount_charges[$rate_status->box_id]['cash']}}">
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
                                                                    <input type="checkbox" name="discount_insurance_switch_{{$rate_status->box_id}}" id="discount_insurance_switch_{{$rate_status->box_id}}" class="switchery discount_switch_{{$rate_status->box_id}}" data-size="xs" {{ ($discount_charges[$rate_status->box_id]['insurance'] != null) ? 'checked' : '' }}/>
                                                                  </span>
                                                            </div>
                                                            <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent discount-inp" name="discount_insurance_{{$rate_status->box_id}}" {{ ($discount_charges[$rate_status->box_id]['insurance'] == null) ? 'disabled' : '' }} value="{{$discount_charges[$rate_status->box_id]['insurance']}}">
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
                                                                    <input type="checkbox"  class="switchery discount_switch_{{$rate_status->box_id}}" data-size="xs" name="discount_return_switch_{{$rate_status->box_id}}" id="discount_return_switch_{{$rate_status->box_id}}" {{ ($discount_charges[$rate_status->box_id]['return'] != null) ? 'checked' : '' }}/>
                                                                  </span>
                                                            </div>
                                                            <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent discount-inp" name="discount_return_{{$rate_status->box_id}}" {{ ($discount_charges[$rate_status->box_id]['return'] == null) ? 'disabled' : '' }} value="{{$discount_charges[$rate_status->box_id]['return']}}">
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            @else
                                                <div class="row mt-1">
                                                    <div class="col-md-6">
                                                        <label class="">Title</label>
                                                        <div class='form-group'>
                                                            <input type='text' class="form-control" data-rule-required="true" data-msg-required="This field is required" disabled name="discount_title_{{$rate_status->box_id}}">
                                                        </div>

                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="">Apply [to - from]</label>
                                                        <div class='input-group form-group'>
                                                            <input type='text' class="form-control daterange" data-rule-required="true" data-msg-required="This field is required" disabled name="daterange_{{$rate_status->box_id}}"/>
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
                                                                    <input type="checkbox" class="switchery discount_switch_{{$rate_status->box_id}}" name="discount_weight_switch_{{$rate_status->box_id}}" id="discount_weight_switch_{{$rate_status->box_id}}" data-size="xs" />
                                                                  </span>
                                                                </div>
                                                                <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent discount-inp" name="discount_weight_{{$rate_status->box_id}}" disabled>
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
                                                                    <input type="checkbox" name="discount_cash_switch_{{$rate_status->box_id}}" id="discount_cash_switch_{{$rate_status->box_id}}" class="switchery discount_switch_{{$rate_status->box_id}}" data-size="xs" />
                                                                  </span>
                                                                </div>
                                                                <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent discount-inp" name="discount_cash_{{$rate_status->box_id}}" disabled>
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
                                                                    <input type="checkbox" name="discount_insurance_switch_{{$rate_status->box_id}}" id="discount_insurance_switch_{{$rate_status->box_id}}" class="switchery discount_switch_{{$rate_status->box_id}}" data-size="xs" />
                                                                  </span>
                                                                </div>
                                                                <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent discount-inp" name="discount_insurance_{{$rate_status->box_id}}" disabled>
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
                                                                    <input type="checkbox"  class="switchery discount_switch_{{$rate_status->box_id}}" data-size="xs" name="discount_return_switch_{{$rate_status->box_id}}" id="discount_return_switch_{{$rate_status->box_id}}"/>
                                                                  </span>
                                                                </div>
                                                                <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent discount-inp" name="discount_return_{{$rate_status->box_id}}" disabled>
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="form-group text-center">
                                <button id="add_more_rates_hubs" type="button" class="btn btn-outline-success round btn-min-width mr-1 mb-1">Add Rates and Hub</button>
                            </div>
                            <div class="row mt-2 justify-content-center">
                                <div class="col-5 form-group">
                                    <textarea name="rate_remarks" id="rate_remarks" class="form-control" placeholder="Rate Remarks..." rows="3"></textarea>
                                </div>
                            </div>
                            <div class="text-center mt-2">
                                <input type="hidden" name="authorize" id="authorize">
                                <input type="hidden" name="approve" id="approve">
                                <div class="form-group">

                                    <button id="addRatesSubmit" type="submit" class="btn btn-outline-success round btn-min-width mr-1 mb-1">Update Rates</button>


                                    @if ($user_information->status == 4 && (session('role_id') == 1 || in_array(8, session('permissions'))))
                                        <button id="accountActiveSubmit" type="submit" class="btn btn-outline-primary round btn-min-width mr-1 mb-1">Approve</button>
                                        <button id="AuthorizeaccountRejectActiveSubmit" type="button" class="btn btn-outline-danger round btn-min-width mr-1 mb-1">Reject Rates</button>
                                    @endif
                                    @if ($user_information->status == 2 && (session('role_id') == 1 || in_array(140, session('permissions'))))
                                        <button id="accountApproveActiveSubmit" type="submit" class="btn btn-outline-primary round btn-min-width mr-1 mb-1">Approve</button>
                                        <button id="accountRejectActiveSubmit" type="button" class="btn btn-outline-danger round btn-min-width mr-1 mb-1">Reject Rates</button>
                                    @endif
                                </div>

                            </div>

                        </div>
                    </form>
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
                    <textarea id="reject_reason" class="form-control" onkeyup="textAreaAdjust(this)" style="width:100%;overflow:hidden"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-danger" id="RejectRatesSubmit">Yes</button>
                </div>
            </div>
        </div>
    </div>


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
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        function textAreaAdjust(o) {
            o.style.height = "1px";
            o.style.height = (25+o.scrollHeight)+"px";
        }
        $(document).ready(function () {
            @foreach($rate_statuses as $rate_status)
                $('#select_box_{{$rate_status->box_id}}').select2({
                    width:'100%',
                    placeholder:"Select City",
                    allowClear:true
                });
                var hub_ids = [];
                    @foreach($rates_hubs as $hub)
                        @if($hub->box_id == $rate_status->box_id)
                            hub_ids.push({{$hub->hub_id}});
                        @endif
                    @endforeach
                $('#select_box_{{$rate_status->box_id}}').val(hub_ids).trigger('change');
                var box_id = {{$rate_status->box_id}};
            @endforeach
            $(".daterange").daterangepicker();
            $(".touchspin-color").trigger("touchspin.updatesettings", {min: 0.5,step: 0.5, decimals: 2});
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

            $('.wa_switch').on('change',function(){
                var wid = $(this).attr('name');
                var wswitch = document.querySelector('input[name="'+ wid +'"]');
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
            $('body').on('click','.weight_close',function () {
                $(this).parent().parent().remove();
            });

            var box_no = box_id;
            var new_count = {{$count}}
            var wa_rows = @json($w_index);
            var cash_count = @json($ch_index);
            var ins_count = @json($in_index);

            @foreach($rate_statuses as $rate_status)
            {{--var cashhandlingswitch = document.querySelector('.switchery.cash_handling_switch_{{$rate_status->box_id}}');--}}
            {{--var insuranceChargesSwitch = document.querySelector('.switchery.insurance_charges_switch_{{$rate_status->box_id}}');--}}
            {{--var returnChargesSwitch = document.querySelector('.switchery.return_charges_switch_{{$rate_status->box_id}}');--}}
                $('body').on('click','button.wa_btn_{{$rate_status->box_id}}',function () {
                    let html = '<div class="row" id="wa_row_'+ {{$rate_status->box_id}} +'_'+ wa_rows +'">\n' +
                        '                                                    <div class="col text-center">\n' +
                        '                                                        <fieldset class="form-group">\n' +
                        '                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" name="range_up['+ {{$rate_status->box_id}} +']['+ wa_rows +']">\n' +
                        '                                                        </fieldset>\n' +
                        '                                                    </div>\n' +
                        '                                                    <div class="col text-center">\n' +
                        '\n' +
                        '                                                        <fieldset class="form-group">\n' +
                        '                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" name="range_down['+ {{$rate_status->box_id}} +']['+ wa_rows +']">\n' +
                        '                                                        </fieldset>\n' +
                        '                                                    </div>\n' +
                        '                                                    <div class="col text-center">\n' +
                        '\n' +
                        '                                                        <div class="form-group " style="padding-top: 8px;">\n' +
                        '                                                            <input type="checkbox" class="switchery wa_switch'+ wa_rows +'" data-color="success" data-size="sm" name="wa_switch['+ {{$rate_status->box_id}} +']['+ wa_rows +']"/>\n' +
                        '                                                        </div>\n' +
                        '                                                    </div>\n' +
                        '                                                    <div class="col-2 text-center">\n' +
                        '\n' +
                        '                                                        <fieldset style="padding-top: 5px;">\n' +
                        '                                                            <div class="input-group input-group-sm form-group">\n' +
                        '                                                                <input type="text" class="touchspin-color input-sm spkg" value="0.5" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="spkg['+ {{$rate_status->box_id}} +']['+ wa_rows +']" data-rule-required="true" data-msg-required="This field is required">\n' +
                        '                                                            </div>\n' +
                        '                                                        </fieldset>\n' +
                        '                                                    </div>\n' +
                        '                                                    <div class="col text-center">\n' +
                        '                                                        <fieldset class="form-group">\n' +
                        '                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" name="local_charges['+ {{$rate_status->box_id}} +']['+ wa_rows +']">\n' +
                        '                                                        </fieldset>\n' +
                        '                                                    </div>\n' +
                        '\n' +
                        '                                                    <div class="col-1">\n' +
                        '                                                   <span class="btn btn-danger rounded btn-sm-width mr-1 mb-1 weight_close"><i class="ft-x"></i></span>\n' +
                        '                                                    </div>\n' +
                        '\n' +
                        '                                                </div>';
                    $('#wa_rows_div_{{$rate_status->box_id}}').append(html);
                    var switches = document.querySelector('.switchery.wa_switch'+wa_rows);
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

                            $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);
                        } else if (switches.checked === false) {
                            $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

                        }

                    };
                    var row_handel = 'wa_row_'+ {{$rate_status->box_id}} +'_'+ wa_rows+' .validated';
                    $("#"+row_handel).each(function(){
                        $( this ).rules( "add", {
                            required: true,
                        });

                    });
                    wa_rows++;

                });

                $('body').on('click','button.add_more_cash_slabs_{{$rate_status->box_id}}',function () {
                    let htmdiv = '<div class="row" id="cash_handle_'+ {{$rate_status->box_id}} +'_'+ cash_count +'">\n' +
                        '                                                <div class="col-md-2 text-center">\n' +
                        '                                                    <fieldset class="form-group">\n' +
                        '                                                        <input name="cash_range_up['+ {{$rate_status->box_id}} +']['+cash_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric validated" >\n' +
                        '                                                    </fieldset>\n' +
                        '                                                </div>\n' +
                        '                                                <div class="col-md-2 text-center">\n' +
                        '                                                    <fieldset class="form-group">\n' +
                        '                                                        <input name="cash_range_down['+ {{$rate_status->box_id}} +']['+cash_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric validated">\n' +
                        '                                                    </fieldset>\n' +
                        '                                                </div>\n' +
                        '                                                <div class="col-md-2 text-center">\n' +
                        '                                                    <fieldset class="form-group">\n' +
                        '                                                        <input name="cash_charges['+ {{$rate_status->box_id}} +']['+cash_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control dec-percent validated"></fieldset></div><div class="col">\n' +
                        '<span class="btn btn-danger rounded btn-sm-width mr-1 mb-1 weight_close"><i class="ft-x"></i></span></div></div>';
                    $('.cash-handling-div-{{$rate_status->box_id}}').append(htmdiv);
                    masks();
                    $("#cash_handle_'+ {{$rate_status->box_id}} +'_"+cash_count+" .validated").each(function(){
                        $( this ).rules( "add", {
                            required: true,
                        });

                    });
                    // $(this).parent().prev().find('div.slabs').append(htmdiv);
                    // console.log();
                    cash_count++;
                });

                $('body').on('click','button.add_more_ins_slabs_{{$rate_status->box_id}}',function () {
                    let htmdiv = '<div class="row" id="insurance_charge_'+ {{$rate_status->box_id}} +'_'+ins_count+'">\n' +
                        '                                                <div class="col-md-2 text-center">\n' +
                        '                                                    <fieldset class="form-group">\n' +
                        '                                                        <input name="ins_range_up['+ {{$rate_status->box_id}} +']['+ins_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric validated">\n' +
                        '                                                    </fieldset>\n' +
                        '                                                </div>\n' +
                        '                                                <div class="col-md-2 text-center">\n' +
                        '                                                    <fieldset class="form-group">\n' +
                        '                                                        <input name="ins_range_down['+ {{$rate_status->box_id}} +']['+ins_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric validated">\n' +
                        '                                                    </fieldset>\n' +
                        '                                                </div>\n' +
                        '\n' +
                        '                                                <div class="col-md-2 text-center">\n' +
                        '                                                    <fieldset class="form-group">\n' +
                        '                                                        <input name="ins_charges['+ {{$rate_status->box_id}} +']['+ins_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control dec-percent validated">\n' +
                        '                                                    </fieldset>\n' +
                        '                                                </div>\n' +
                        '<div class="col">\n' +
                        '<span class="btn btn-danger rounded btn-sm-width mr-1 mb-1 weight_close"><i class="ft-x"></i></span></div></div>';
                    $('.insurance-charges-div-{{$rate_status->box_id}}').append(htmdiv);
                    masks();
                    $("#insurance_charge_{{$rate_status->box_id}}_"+ins_count+" .validated").each(function(){
                        $( this ).rules( "add", {
                            required: true,
                        });

                    });
                    ins_count++;
                });
                if($('#cash_handling_switch_{{$rate_status->box_id}}').is(":checked") === false){
                    $('.cash-handling-div-{{$rate_status->box_id}}').find('input').prop('disabled',true);
                    $('.add_more_cash_slabs_{{$rate_status->box_id}}').prop('disabled',true);
                }
                if($('#insurance_charges_switch_{{$rate_status->box_id}}').is(":checked") === false){
                    $('.insurance-charges-div-{{$rate_status->box_id}}').find('input').prop('disabled',true);
                    $('.add_more_ins_slabs_{{$rate_status->box_id}}').prop('disabled',true);
                }

                $('#cash_handling_switch_{{$rate_status->box_id}}').on("change" , function() {
                    if($(this).is(":checked") === true){
                        $('.cash-handling-div-{{$rate_status->box_id}}').find('input').prop('disabled',false);
                        $('.add_more_cash_slabs_{{$rate_status->box_id}}').prop('disabled',false);
                    }else if($(this).is(":checked") === false){
                        $('.cash-handling-div-{{$rate_status->box_id}}').find('input').prop('disabled',true);
                        $('.add_more_cash_slabs_{{$rate_status->box_id}}').prop('disabled',true);

                    }
                });

                // InsuranceOvernight
                $('#insurance_charges_switch_{{$rate_status->box_id}}').on("change" , function() {
                    if($(this).is(":checked") === true){
                        $('.insurance-charges-div-{{$rate_status->box_id}}').find('input').prop('disabled',false);
                        $('.add_more_ins_slabs_{{$rate_status->box_id}}').prop('disabled',false);
                    }else if($(this).is(":checked") === false){
                        $('.insurance-charges-div-{{$rate_status->box_id}}').find('input').prop('disabled',true);
                        $('.add_more_ins_slabs_{{$rate_status->box_id}}').prop('disabled',true);

                    }
                });

                // Return Overnight
                $('#return_charges_switch_{{$rate_status->box_id}}').on("change" , function() {
                    if($(this).is(":checked") === true){
                        $('.return-charges-div-{{$rate_status->box_id}}').find('input').prop('disabled',false);
                    }else if($(this).is(":checked") === false){
                        $('.return-charges-div-{{$rate_status->box_id}}').find('input').prop('disabled',true);
                    }
                });

                //for discounts Overnight
                {{--var ondiscountSwitch = Array.prototype.slice.call(document.querySelectorAll('.discount_switch_{{$rate_status->box_id}}'));--}}


                $('#discount_weight_switch_{{$rate_status->box_id}}').on("change" , function() {
                    ONdiscount{{$rate_status->box_id}}($(this));
                });
                $('#discount_cash_switch_{{$rate_status->box_id}}').on("change" , function() {
                    ONdiscount{{$rate_status->box_id}}($(this));
                });
                $('#discount_insurance_switch_{{$rate_status->box_id}}').on("change" , function() {
                    ONdiscount{{$rate_status->box_id}}($(this));
                });
                $('#discount_return_switch_{{$rate_status->box_id}}').on("change" , function() {
                    ONdiscount{{$rate_status->box_id}}($(this));
                });

                function ONdiscount{{$rate_status->box_id}}(eve) {
                    console.log({{$rate_status->box_id}});
                    if(eve.is(":checked") === true){
                        $(eve).parent().parent().next().prop('disabled',false);
                        $('input[name="discount_title_'+ {{$rate_status->box_id}} +'"]').prop('disabled',false);
                        $('input[name="daterange_'+ {{$rate_status->box_id}} +'"]').prop('disabled',false);

                    }else if(eve.is(":checked") === false){
                        $(eve).parent().parent().next().prop('disabled',true);

                        if($('#discount_weight_switch_{{$rate_status->box_id}}').is(":checked") === true || $('#discount_cash_switch_{{$rate_status->box_id}}').is(":checked") === true || $('#discount_insurance_switch_{{$rate_status->box_id}}').is(":checked") === true || $('#discount_return_switch_{{$rate_status->box_id}}').is(":checked") === true){
                            console.log(false);
                            $('input[name="discount_title_'+ {{$rate_status->box_id}} + '"]').prop('disabled',false);
                            $('input[name="daterange_' + {{$rate_status->box_id}} + '"]').prop('disabled',false);
                        }else{
                            console.log(true);
                            $('input[name="discount_title_' + {{$rate_status->box_id}} + '"]').prop('disabled',true);
                            $('input[name="daterange_' + {{$rate_status->box_id}} + '"]').prop('disabled',true);
                        }

                    }
                }

            var old_box_no = {{$rate_status->box_id}} - 1;
            $('.rate_box_close_'+ old_box_no).parent().remove();
            $('body').on('click', 'span.rate_box_close_{{$rate_status->box_id}}', function(){
                var box = $(this).attr('box');
                $('.parent_box_div_'+box).remove();
            });
            @endforeach

            var cities = @json($cities);
            var city_data = $.map(cities, function (obj) {
                obj.id = obj.id;
                obj.text = obj.name;
                return obj;
            });

            $('#add_more_rates_hubs').on('click', function () {
                box_no++;
                new_count++;
                var box_div = '<div class="parent_box_div_'+ box_no +'"><div class="card-header border-success">\n' +
                    '                                    <input type="hidden" value="'+ box_no +'" name="box_ids[]">\n' +
                    '                                    <div class="row">\n' +
                    '                                        <div class="col-md-6">\n' +
                    '                                            <h3 class="display-inline card-title lead success">International Rates '+ new_count +'</h3>\n' +
                    '                                        </div>\n' +
                    '                                        <div class="col-md-6 text-right">\n' +
                    '                                            <span class="btn btn-danger rounded btn-sm-width rate_box_close_'+ box_no +'" box="'+ box_no +'"><i class="ft-trash"></i></span>\n' +
                    '                                        </div>\n' +
                    '                                    </div>\n' +
                    '                                </div>\n' +
                    '                                <div class="card border-success">\n' +
                    '                                    <div class="card-content">\n' +
                    '                                        <div class="card-body">\n' +
                    '                                           <div class="row">\n' +
                    '                                             <div class="col-2">\n' +
                    '                                             <h3>Add Hubs</h3>\n' +
                    '                                             </div>\n' +
                    '                                             <div class="col-6">\n' +
                    '                                             <div class="form-group card border-success p-2">\n' +
                    '                                             <select name="hubs['+ box_no +'][]" id="select_box_'+ box_no +'" class="form-control select2" multiple="multiple" required data-rule-required="true" data-msg-required="This field is required">\n' +
                    '                                             </select>\n' +
                    '                                             </div>\n' +
                    '                                             </div>\n' +
                    '                                             </div>\n' +
                    '                                            <div id="wa_rows_div_'+ box_no +'">\n' +
                    '                                                <div class="row">\n' +
                    '                                                    <div class="col-md-2">\n' +
                    '                                                        <h3 class="card-title">Weight Charges</h3>\n' +
                    '                                                    </div>\n' +
                    '\n' +
                    '                                                </div>\n' +
                    '                                                <div class="row">\n' +
                    '                                                    <div class="col text-center">\n' +
                    '                                                        <label class="card-title">Range Up</label>\n' +
                    '                                                    </div>\n' +
                    '                                                    <div class="col text-center">\n' +
                    '                                                        <label class="card-title">Range Down</label>\n' +
                    '                                                    </div>\n' +
                    '                                                    <div class="col text-center">\n' +
                    '                                                        <label class="card-title">Weight Addition</label>\n' +
                    '                                                    </div>\n' +
                    '                                                    <div class="col text-center">\n' +
                    '                                                        <label class="card-title">KG Range</label>\n' +
                    '                                                    </div>\n' +
                    '                                                    <div class="col text-center">\n' +
                    '                                                        <label class="card-title">Local Charges</label>\n' +
                    '                                                    </div>\n' +
                    '                                                    <div class="col-1 text-center">\n' +
                    '                                                    </div>\n' +
                    '                                                </div>\n' +
                    '\n' +
                    '                                                <div class="row" id="weight_row">\n' +
                    '                                                    <div class="col text-center">\n' +
                    '                                                        <fieldset class="form-group">\n' +
                    '                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" name="range_up['+ box_no +'][1]">\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div>\n' +
                    '                                                    <div class="col text-center">\n' +
                    '\n' +
                    '                                                        <fieldset class="form-group">\n' +
                    '                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" name="range_down['+ box_no +'][1]">\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div>\n' +
                    '                                                    <div class="col text-center">\n' +
                    '\n' +
                    '                                                        <div class="form-group " style="padding-top: 8px;">\n' +
                    '                                                            <input type="checkbox" class="switchery wa_switch_'+ box_no +'" data-color="success" data-size="sm" name="wa_switch['+ box_no +'][1]"/>\n' +
                    '                                                        </div>\n' +
                    '                                                    </div>\n' +
                    '                                                    <div class="col-2 text-center">\n' +
                    '\n' +
                    '                                                        <fieldset style="padding-top: 5px;">\n' +
                    '                                                            <div class="input-group input-group-sm form-group">\n' +
                    '                                                                <input type="text" class="touchspin-color input-sm spkg" value="0.5" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="spkg['+ box_no +'][1]" data-rule-required="true" data-msg-required="This field is required">\n' +
                    '                                                            </div>\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div>\n' +
                    '                                                    <div class="col text-center">\n' +
                    '                                                        <fieldset class="form-group">\n' +
                    '                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" name="local_charges['+ box_no +'][1]">\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div>\n' +
                    '\n' +
                    '                                                    <div class="col-1">\n' +
                    '                                                    </div>\n' +
                    '\n' +
                    '                                                </div>\n' +
                    '\n' +
                    '                                            </div>\n' +
                    '                                            <div>\n' +
                    '                                                <button type="button" class="btn btn-outline-success mr-1 wa_btn_'+ box_no +'" title="Add more slabs"><i class="la la-plus"></i></button>\n' +
                    '                                            </div>\n' +
                    '\n' +
                    '                                            <hr>\n' +
                    '                                            <div class="row">\n' +
                    '                                                <div class="col-md-2">\n' +
                    '                                                    <h3 class="card-title">Cash Handling Charges</h3>\n' +
                    '                                                </div>\n' +
                    '                                                <div class="col-md-2">\n' +
                    '                                                    <div class="form-group ">\n' +
                    '                                                        <input type="checkbox" name="cash_handling_switch_'+ box_no +'" class="switchery cash_handling_switch_'+ box_no +'" data-color="success" data-size="sm" checked/>\n' +
                    '                                                    </div>\n' +
                    '                                                </div>\n' +
                    '                                            </div>\n' +
                    '                                            <div class="row">\n' +
                    '                                                <div class="col-md-2 text-center">\n' +
                    '                                                    <label class="card-title">Range Up</label>\n' +
                    '                                                </div>\n' +
                    '                                                <div class="col-md-2 text-center">\n' +
                    '                                                    <label class="card-title">Range Down</label>\n' +
                    '                                                </div>\n' +
                    '                                                <div class="col-md-2 text-center">\n' +
                    '                                                    <label class="card-title">Charges</label>\n' +
                    '                                                </div>\n' +
                    '                                            </div>\n' +
                    '\n' +
                    '                                            <div class="cash-handling-div-'+ box_no +' slabs">\n' +
                    '\n' +
                    '                                                <div class="row">\n' +
                    '                                                    <div class="col-md-2 text-center">\n' +
                    '                                                        <fieldset class="form-group">\n' +
                    '                                                            <input name="cash_range_up['+ box_no +'][1]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric">\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div>\n' +
                    '                                                    <div class="col-md-2 text-center">\n' +
                    '                                                        <fieldset class="form-group">\n' +
                    '                                                            <input name="cash_range_down['+ box_no +'][1]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric">\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div>\n' +
                    '\n' +
                    '                                                    <div class="col-md-2 text-center">\n' +
                    '                                                        <fieldset class="form-group">\n' +
                    '                                                            <input name="cash_charges['+ box_no +'][1]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required">\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div>\n' +
                    '\n' +
                    '                                                    <div class="col">\n' +
                    '                                                    </div>\n' +
                    '\n' +
                    '                                                </div>\n' +
                    '\n' +
                    '                                            </div>\n' +
                    '                                            <div class="cash-handling-btn">\n' +
                    '                                                <button type="button" class="btn btn-outline-success mr-1 add_more_cash_slabs_'+ box_no +'" title="Add more slabs" ><i class="la la-plus"></i></button>\n' +
                    '                                            </div>\n' +
                    '                                            <hr>\n' +
                    '\n' +
                    '                                            <div class="row">\n' +
                    '                                                <div class="col-md-2">\n' +
                    '                                                    <h3 class="card-title">Insurance Charges</h3>\n' +
                    '                                                </div>\n' +
                    '                                                <div class="col-md-2">\n' +
                    '                                                    <div class="form-group ">\n' +
                    '                                                        <input type="checkbox" name="insurance_charges_switch_'+ box_no +'" class="switchery insurance_charges_switch_'+ box_no +'" data-color="success" data-size="sm" checked/>\n' +
                    '                                                    </div>\n' +
                    '                                                </div>\n' +
                    '                                            </div>\n' +
                    '                                            <div class="row">\n' +
                    '                                                <div class="col-md-2 text-center">\n' +
                    '                                                    <label class="card-title">Range Up</label>\n' +
                    '                                                </div>\n' +
                    '                                                <div class="col-md-2 text-center">\n' +
                    '                                                    <label class="card-title">Range Down</label>\n' +
                    '                                                </div>\n' +
                    '                                                <div class="col-md-2 text-center">\n' +
                    '                                                    <label class="card-title">Charges</label>\n' +
                    '                                                </div>\n' +
                    '                                            </div>\n' +
                    '                                            <div class="insurance-charges-div-'+ box_no +' slabs">\n' +
                    '\n' +
                    '                                                <div class="row" id="insurance_handle_1_1">\n' +
                    '                                                    <div class="col-md-2 text-center">\n' +
                    '                                                        <fieldset class="form-group">\n' +
                    '                                                            <input name="ins_range_up['+ box_no +'][1]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric">\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div>\n' +
                    '                                                    <div class="col-md-2 text-center">\n' +
                    '                                                        <fieldset class="form-group">\n' +
                    '                                                            <input name="ins_range_down['+ box_no +'][1]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric">\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div>\n' +
                    '\n' +
                    '                                                    <div class="col-md-2 text-center">\n' +
                    '                                                        <fieldset class="form-group">\n' +
                    '                                                            <input name="ins_charges['+ box_no +'][1]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control dec-percent">\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div>\n' +
                    '\n' +
                    '                                                    <div class="col">\n' +
                    '                                                    </div>\n' +
                    '\n' +
                    '                                                </div>\n' +
                    '\n' +
                    '                                            </div>\n' +
                    '                                            <div class="insurance-charges-btn">\n' +
                    '                                                <button type="button" class="btn btn-outline-success mr-1 add_more_ins_slabs_'+ box_no +'" title="Add more slabs"><i class="la la-plus"></i></button>\n' +
                    '                                            </div>\n' +
                    '                                            <hr>\n' +
                    '                                            <div class="row">\n' +
                    '                                                <div class="col-md-2">\n' +
                    '                                                    <h3 class="card-title">Return Charges</h3>\n' +
                    '                                                </div>\n' +
                    '                                                <div class="col-md-2">\n' +
                    '                                                    <div class="form-group ">\n' +
                    '                                                        <input type="checkbox" name="return_charges_switch_'+ box_no +'" class="switchery return_charges_switch_'+ box_no +'" data-color="success" data-size="sm" checked/>\n' +
                    '                                                    </div>\n' +
                    '                                                </div>\n' +
                    '                                            </div>\n' +
                    '\n' +
                    '                                            <div class="row return-charges-div-'+ box_no +'">\n' +
                    '\n' +
                    '                                                <div class="col-3 text-center">\n' +
                    '                                                    <label class="card-title">Local Charges</label>\n' +
                    '                                                    <fieldset class="form-group">\n' +
                    '                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control amount" name="return_local_charges_'+ box_no +'">\n' +
                    '                                                    </fieldset>\n' +
                    '                                                </div>\n' +
                    '                                            </div>\n' +
                    '                                            <hr>\n' +
                    '                                           <div class="col">' +
                    '                                            <div class="">\n' +
                    '                                                <h3 class="card-title">Discount Rates</h3>\n' +
                    '                                            </div>\n' +
                    '                                            <div class="row mt-1">\n' +
                    '                                                <div class="col-md-6">\n' +
                    '                                                    <label class="">Title</label>\n' +
                    '                                                    <div class="form-group">\n' +
                    '                                                        <input type="text" class="form-control" data-rule-required="true" data-msg-required="This field is required" disabled name="discount_title_'+ box_no +'"/>\n' +
                    '                                                    </div>\n' +
                    '\n' +
                    '                                                </div>\n' +
                    '                                                <div class="col-md-6">\n' +
                    '                                                    <label class="">Apply [to - from]</label>\n' +
                    '                                                    <div class="input-group form-group">\n' +
                    '                                                        <input type="text" class="form-control daterange" data-rule-required="true" data-msg-required="This field is required" disabled name="daterange_'+ box_no +'"/>\n' +
                    '                                                        <div class="input-group-append">\n' +
                    '                                                            <span class="input-group-text">\n' +
                    '                                                              <span class="la la-calendar"></span>\n' +
                    '                                                            </span>\n' +
                    '                                                        </div>\n' +
                    '                                                    </div>\n' +
                    '\n' +
                    '                                                </div>\n' +
                    '                                            </div>\n' +
                    '                                            <div class="row">\n' +
                    '                                                <div class="col text-center">\n' +
                    '                                                    <fieldset>\n' +
                    '                                                        <div class="input-group input-group-sm form-group">\n' +
                    '                                                            <div class="input-group-prepend">\n' +
                    '                                                                <span class="input-group-text">Weight</span>\n' +
                    '                                                            </div>\n' +
                    '                                                            <div class="input-group-prepend">\n' +
                    '                                                              <span class="input-group-text">\n' +
                    '                                                                <input type="checkbox" class="switchery discount_switch_'+ box_no +'" name="discount_weight_switch_'+ box_no +'" data-size="xs" />\n' +
                    '                                                              </span>\n' +
                    '                                                            </div>\n' +
                    '                                                            <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent discount-inp" name="discount_weight_'+ box_no +'" disabled>\n' +
                    '                                                        </div>\n' +
                    '                                                    </fieldset>\n' +
                    '                                                </div>\n' +
                    '                                                <div class="col text-center">\n' +
                    '                                                    <fieldset>\n' +
                    '                                                        <div class="input-group input-group-sm form-group">\n' +
                    '                                                            <div class="input-group-prepend">\n' +
                    '                                                                <span class="input-group-text" id="">Cash</span>\n' +
                    '                                                            </div>\n' +
                    '                                                            <div class="input-group-prepend">\n' +
                    '                                                              <span class="input-group-text" id="">\n' +
                    '                                                                <input type="checkbox" name="discount_cash_switch_'+ box_no +'" class="switchery discount_switch_'+ box_no +'" data-size="xs" />\n' +
                    '                                                              </span>\n' +
                    '                                                            </div>\n' +
                    '                                                            <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent discount-inp" name="discount_cash_'+ box_no +'" disabled>\n' +
                    '                                                        </div>\n' +
                    '                                                    </fieldset>\n' +
                    '                                                </div>\n' +
                    '                                                <div class="col text-center">\n' +
                    '                                                    <fieldset>\n' +
                    '                                                        <div class="input-group input-group-sm form-group">\n' +
                    '                                                            <div class="input-group-prepend">\n' +
                    '                                                                <span class="input-group-text" id="">Insurance</span>\n' +
                    '                                                            </div>\n' +
                    '                                                            <div class="input-group-prepend">\n' +
                    '                                                              <span class="input-group-text" id="">\n' +
                    '                                                                <input type="checkbox" name="discount_insurance_switch_'+ box_no +'" class="switchery discount_switch_'+ box_no +'" data-size="xs" />\n' +
                    '                                                              </span>\n' +
                    '                                                            </div>\n' +
                    '                                                            <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent discount-inp" name="discount_insurance_'+ box_no +'" disabled>\n' +
                    '                                                        </div>\n' +
                    '                                                    </fieldset>\n' +
                    '                                                </div>\n' +
                    '                                                <div class="col text-center">\n' +
                    '                                                    <fieldset>\n' +
                    '                                                        <div class="input-group input-group-sm form-group">\n' +
                    '                                                            <div class="input-group-prepend">\n' +
                    '                                                                <span class="input-group-text" id="">Return</span>\n' +
                    '                                                            </div>\n' +
                    '                                                            <div class="input-group-prepend">\n' +
                    '                                                              <span class="input-group-text">\n' +
                    '                                                                <input type="checkbox"  class="switchery discount_switch_'+ box_no +'" data-size="xs" name="discount_return_switch_'+ box_no +'"/>\n' +
                    '                                                              </span>\n' +
                    '                                                            </div>\n' +
                    '                                                            <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent discount-inp" name="discount_return_'+ box_no +'" disabled>\n' +
                    '                                                        </div>\n' +
                    '                                                    </fieldset>\n' +
                    '                                                </div>\n' +
                    '                                            </div>\n' +
                    '                                            </div>\n' +
                    '                                        </div>\n' +
                    '                                    </div>\n' +
                    '                                </div></div>';
                $('.box_parent_div').append(box_div);

                masks();
                $(".daterange").daterangepicker();
                $('#select_box_'+box_no).select2({data:city_data,placeholder:'Select City',allowClear:true});

                var wa_switch = document.querySelector('.switchery.wa_switch_'+box_no);

                var cashhandlingswitch = document.querySelector('.switchery.cash_handling_switch_'+box_no);
                var insuranceChargesSwitch = document.querySelector('.switchery.insurance_charges_switch_'+box_no);
                var returnChargesSwitch = document.querySelector('.switchery.return_charges_switch_'+box_no);


                var switchery = new Switchery(wa_switch, { disabled: false,color: '#37BC9B',size:'small' });
                $('.wa_switch_'+box_no).on('change',function(){
                    var wid = $(this).attr('name');
                    var wswitch = document.querySelector('input[name="'+ wid +'"]');
                    if (wswitch.checked === true) {

                        $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);

                    } else if (wswitch.checked === false) {
                        $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

                    }
                });
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

                var switchery2 = new Switchery(cashhandlingswitch, { disabled: false,color: '#37BC9B',size:'small' });
                var switchery3 = new Switchery(insuranceChargesSwitch, { disabled: false,color: '#37BC9B',size:'small' });
                var switchery4 = new Switchery(returnChargesSwitch, { disabled: false,color: '#37BC9B',size:'small' });

                //for discounts Overnight
                var discount_sw = '.discount_switch_'+box_no;
                var discountSwitch = Array.prototype.slice.call(document.querySelectorAll(discount_sw));
                $(discount_sw).each(function() {
                    new Switchery(this, { disabled: false,color: '#37BC9B',size:'small' });
                });
                // var switchery_discount = new Switchery(discountSwitch, { disabled: false,color: '#37BC9B',size:'small' });

                discountSwitch[0].onchange = function () {
                    IRdiscount(discountSwitch[0]);
                };
                discountSwitch[1].onchange = function () {
                    IRdiscount(discountSwitch[1]);
                };
                discountSwitch[2].onchange = function () {
                    IRdiscount(discountSwitch[2]);
                };
                discountSwitch[3].onchange = function () {
                    IRdiscount(discountSwitch[3]);
                };

                function IRdiscount(eve) {
                    var discount_title = $('input[name="discount_title_'+ box_no +'"]');
                    var discount_daterange = $('input[name="daterange_'+ box_no +'"]');
                    if(eve.checked === true){

                        $(eve).parent().parent().next().prop('disabled',false);
                        discount_title.prop('disabled',false);
                        discount_daterange.prop('disabled',false);

                    }else if(eve.checked === false){
                        $(eve).parent().parent().next().prop('disabled',true);

                        if(discountSwitch[0].checked === true || discountSwitch[1].checked === true || discountSwitch[2].checked === true || discountSwitch[3].checked === true){
                            discount_title.prop('disabled',false);
                            discount_daterange.prop('disabled',false);
                        }else{
                            discount_title.prop('disabled',true);
                            discount_daterange.prop('disabled',true);
                        }

                    }
                }
                var cash_handling_div = $('.cash-handling-div-'+box_no);
                var cash_handling_btn = $('.cash-handling-btn-'+box_no);
                cashhandlingswitch.onchange = function () {
                    if(cashhandlingswitch.checked === true){
                        cash_handling_div.find('input').prop('disabled',false);
                        cash_handling_btn.find('button').prop('disabled',false);
                    }else if(cashhandlingswitch.checked === false){
                        cash_handling_div.find('input').prop('disabled',true);
                        cash_handling_btn.find('button').prop('disabled',true);

                    }
                };

                // InsuranceOvernight
                insuranceChargesSwitch.onchange = function () {
                    var insurance_charges_div = $('.insurance-charges-div-'+box_no);
                    var insurance_charges_btn = $('.insurance-charges-btn-'+box_no);
                    if(insuranceChargesSwitch.checked === true){
                        insurance_charges_div.find('input').prop('disabled',false);
                        insurance_charges_btn.find('button').prop('disabled',false);
                    }else if(insuranceChargesSwitch.checked === false){
                        insurance_charges_div.find('input').prop('disabled',true);
                        insurance_charges_btn.find('button').prop('disabled',true);

                    }
                };

                // Return Overnight
                returnChargesSwitch.onchange = function () {
                    var return_charges_div = $('.return-charges-div-'+box_no);
                    if(returnChargesSwitch.checked === true){
                        return_charges_div.find('input').prop('disabled',false);
                    }else if(returnChargesSwitch.checked === false){
                        return_charges_div.find('input').prop('disabled',true);

                    }
                };

                var dynamic_wa_rows = 2;
                $('body').on('click','button.wa_btn_'+box_no,function () {
                    let html = '<div class="row" id="wa_row_'+ box_no +'_'+ dynamic_wa_rows +'">\n' +
                        '                                                    <div class="col text-center">\n' +
                        '                                                        <fieldset class="form-group">\n' +
                        '                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" name="range_up['+ box_no +']['+ dynamic_wa_rows +']">\n' +
                        '                                                        </fieldset>\n' +
                        '                                                    </div>\n' +
                        '                                                    <div class="col text-center">\n' +
                        '\n' +
                        '                                                        <fieldset class="form-group">\n' +
                        '                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" name="range_down['+ box_no +']['+ dynamic_wa_rows +']">\n' +
                        '                                                        </fieldset>\n' +
                        '                                                    </div>\n' +
                        '                                                    <div class="col text-center">\n' +
                        '\n' +
                        '                                                        <div class="form-group " style="padding-top: 8px;">\n' +
                        '                                                            <input type="checkbox" class="switchery wa_switch_'+ box_no +'_'+ dynamic_wa_rows +'" data-color="success" data-size="sm" name="wa_switch['+ box_no +']['+ dynamic_wa_rows +']"/>\n' +
                        '                                                        </div>\n' +
                        '                                                    </div>\n' +
                        '                                                    <div class="col-2 text-center">\n' +
                        '\n' +
                        '                                                        <fieldset style="padding-top: 5px;">\n' +
                        '                                                            <div class="input-group input-group-sm form-group">\n' +
                        '                                                                <input type="text" class="touchspin-color input-sm spkg" value="0.5" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="spkg['+ box_no +']['+ dynamic_wa_rows +']" data-rule-required="true" data-msg-required="This field is required">\n' +
                        '                                                            </div>\n' +
                        '                                                        </fieldset>\n' +
                        '                                                    </div>\n' +
                        '                                                    <div class="col text-center">\n' +
                        '                                                        <fieldset class="form-group">\n' +
                        '                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" name="local_charges['+ box_no +']['+ dynamic_wa_rows +']">\n' +
                        '                                                        </fieldset>\n' +
                        '                                                    </div>\n' +
                        '\n' +
                        '                                                    <div class="col-1">\n' +
                        '                                                   <span class="btn btn-danger rounded btn-sm-width mr-1 mb-1 weight_close"><i class="ft-x"></i></span>\n' +
                        '                                                    </div>\n' +
                        '\n' +
                        '                                                </div>';
                    $('#wa_rows_div_'+box_no).append(html);
                    var switches = document.querySelector('.switchery.wa_switch_'+ box_no +'_'+dynamic_wa_rows);
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

                            $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);
                        } else if (switches.checked === false) {
                            $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

                        }

                    };
                    var row_handel = 'wa_row_'+ box_no +'_'+ dynamic_wa_rows+' .validated';
                    $("#"+row_handel).each(function(){
                        $( this ).rules( "add", {
                            required: true,
                        });

                    });

                    dynamic_wa_rows++;

                });

                var dynamic_cash_count = 2;
                $('body').on('click','button.add_more_cash_slabs_'+box_no, function () {
                    let htmdiv = '<div class="row" id="cash_handle_'+ box_no +'_'+ dynamic_cash_count +'">\n' +
                        '                                                <div class="col-md-2 text-center">\n' +
                        '                                                    <fieldset class="form-group">\n' +
                        '                                                        <input name="cash_range_up['+ box_no +']['+dynamic_cash_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric validated" >\n' +
                        '                                                    </fieldset>\n' +
                        '                                                </div>\n' +
                        '                                                <div class="col-md-2 text-center">\n' +
                        '                                                    <fieldset class="form-group">\n' +
                        '                                                        <input name="cash_range_down['+ box_no +']['+dynamic_cash_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric validated">\n' +
                        '                                                    </fieldset>\n' +
                        '                                                </div>\n' +
                        '                                                <div class="col-md-2 text-center">\n' +
                        '                                                    <fieldset class="form-group">\n' +
                        '                                                        <input name="cash_charges['+ box_no +']['+dynamic_cash_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control dec-percent validated"></fieldset></div><div class="col">\n' +
                        '<span class="btn btn-danger rounded btn-sm-width mr-1 mb-1 weight_close"><i class="ft-x"></i></span></div></div>';
                    $('.cash-handling-div-'+box_no).append(htmdiv);
                    masks();
                    var cash_row_handel = 'cash_handle_'+ box_no +'_'+ wa_rows+' .validated';
                    $(cash_row_handel).each(function(){
                        $( this ).rules( "add", {
                            required: true,
                        });
                    });
                    // $(this).parent().prev().find('div.slabs').append(htmdiv);
                    // console.log();
                    dynamic_cash_count++;
                });

                var dynamic_ins_count = 2;
                $('body').on('click','button.add_more_ins_slabs_'+box_no,function () {
                    let htmdiv = '<div class="row" id="insurance_charge_1'+dynamic_ins_count+'">\n' +
                        '                                                <div class="col-md-2 text-center">\n' +
                        '                                                    <fieldset class="form-group">\n' +
                        '                                                        <input name="ins_range_up['+ box_no +']['+dynamic_ins_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric validated">\n' +
                        '                                                    </fieldset>\n' +
                        '                                                </div>\n' +
                        '                                                <div class="col-md-2 text-center">\n' +
                        '                                                    <fieldset class="form-group">\n' +
                        '                                                        <input name="ins_range_down['+ box_no +']['+dynamic_ins_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric validated">\n' +
                        '                                                    </fieldset>\n' +
                        '                                                </div>\n' +
                        '\n' +
                        '                                                <div class="col-md-2 text-center">\n' +
                        '                                                    <fieldset class="form-group">\n' +
                        '                                                        <input name="ins_charges['+ box_no +']['+dynamic_ins_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control dec-percent validated">\n' +
                        '                                                    </fieldset>\n' +
                        '                                                </div>\n' +
                        '<div class="col">\n' +
                        '<span class="btn btn-danger rounded btn-sm-width mr-1 mb-1 weight_close"><i class="ft-x"></i></span></div></div>';
                    $('.insurance-charges-div-'+box_no).append(htmdiv);
                    masks();
                    var ins_row_handel = 'insurance_charge_'+ box_no +'_'+ wa_rows+' .validated';
                    $(ins_row_handel).each(function(){
                        $( this ).rules( "add", {
                            required: true,
                        });

                    });
                    dynamic_ins_count++;
                });
                var old_box_no = box_no - 1;
                $('.rate_box_close_'+ old_box_no).parent().remove();
                $('body').on('click', 'span.rate_box_close_'+ box_no, function(){
                    var box = $(this).attr('box');
                    $('.parent_box_div_'+box).remove();
                });

            });
            $('body').on('change', '#rate_remarks', function () {
                $(this).val($(this).val().trim());
            });
            var auth_reject = 0;
            $('#accountRejectActiveSubmit').click(function() {
                $('#RejectRatesModal').modal('show');
                auth_reject = 0;
            });
            $('#AuthorizeaccountRejectActiveSubmit').click(function() {
                $('#RejectRatesModal').modal('show');
                auth_reject = 1;
            });
            $('#RejectRatesSubmit').on('click',function () {
                var shipper = $('#shipper_id').val();
                var reject_reason = document.getElementById('reject_reason').value;
                if(reject_reason){
                    $.ajax({
                        url: '{!! route('admin.international.rates.edit.reject') !!}',
                        method: 'POST',
                        data: {
                            'rejected_reason': reject_reason,
                            'shipper_id':shipper,
                            'authorization':auth_reject,
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
            $( "#ratesAdditionForm" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    var msg = "";
                    if($('#authorize').val() == 1 || $('#approve').val() == 1){
                        msg = "Rates are being approved!"
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

                }
            });
        });
    </script>
@endsection