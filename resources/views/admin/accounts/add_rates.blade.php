@extends('admin.layout.master')

@section('content')
    <h1>Add Rates</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <h2 class="font-large-1">{{$shipper->name}}</h2>
                        @include('admin.inc.messages')
                    </div>


                    <div class="card-content">
                        <form id="ratesAdditionForm" class="card-body card-dashboard" action="{{route('admin.add.rates.submit',['id'=>$shipper->id])}}" method="post" novalidate="novalidate">
                            @csrf
                            <div id="headingCollapse61" class="card-header border-success">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3 class="card-title lead success">Overnight</h3>
                                    </div>
                                    <div class="col-md-6">
                                        <a data-toggle="collapse" href="#overnight" aria-expanded="false" aria-controls="overnight"
                                           class="pull-right"><input name="on_main_switch" type="checkbox" id="" class="switchery on-main-switch" data-size="sm" /></a>
                                    </div>
                                </div>
                            </div>
                            <div id="overnight" role="tabpanel"  class="card-collapse collapse multi-collapse  border-success"
                                 aria-expanded="true">
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
                                                <div class="col text-center">
                                                    <label class="card-title">KG Range</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Local Charges</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">National Charges</label>
                                                </div>
                                                <div class="col"></div>
                                            </div>
                                            <div class="row" id="on_weight_row0">
                                                <div class="col text-center">
                                                    <fieldset class="form-group">
                                                        <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="0.1" name="on_wa_range_up[0]">
                                                    </fieldset>
                                                </div>
                                                <div class="col text-center">

                                                    <fieldset class="form-group">
                                                        <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="0.9" name="on_wa_range_down[0]">
                                                    </fieldset>
                                                </div>
                                                <div class="col text-center">

                                                    <div class="form-group " style="padding-top: 8px;">
                                                        <input type="checkbox" class="switchery weightAdditionOvernight" data-color="success" data-size="sm" name="on_wa_switch[0]"/>
                                                    </div>
                                                </div>
                                                <div class="col text-center">

                                                    <fieldset style="padding-top: 5px;">
                                                        <div class="input-group input-group-sm form-group">
                                                            <input type="text" class="touchspin-color input-sm spkg" value="0" disabled data-bts-button-down-class="btn btn-success"
                                                                   data-bts-button-up-class="btn btn-success" name="on_wa_spkg[0]" data-rule-required="true" data-msg-required="This field is required">
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col text-center">
                                                    <fieldset class="form-group">
                                                        <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="100.00" name="on_wa_local_charges[0]">
                                                    </fieldset>
                                                </div>
                                                <div class="col text-center">
                                                    <fieldset class="form-group">
                                                        <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="150.00" name="on_wa_national_charges[0]">
                                                    </fieldset>
                                                </div>
                                                <div class="col"></div>
                                            </div>{{--Row--}}

                                        </div>{{--weight addition div--}}
                                        <div>
                                            <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="waddition_btn"><i class="la la-plus"></i></button>
                                        </div>
                                        <div class="row mt-2">
                                            
                                            <div class="col-md-3 text-center">
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" >Replacement</span>
                                                        </div>
                                                        <input type="text" class="form-control percent" data-rule-required="true" data-msg-required="This field is required" name="on_replacement_charges">
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
                                                        <input type="text" class="form-control percent" data-rule-required="true" data-msg-required="This field is required" name="on_tnb_charges">
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
                                            <div class="row">
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="on_cash_range_up[0]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric"  value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="on_cash_range_down[0]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric"  value="3000">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="on_cash_charges[0]" type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="0">
                                                    </fieldset>
                                                </div>
                                            </div>
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
                                            <div class="row" id="on_insurance_handle_0">
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="on_ins_range_up[0]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric"  value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="on_ins_range_down[0]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric"  value="3000">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="on_ins_charges[0]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control amount"  value="0">
                                                    </fieldset>
                                                </div>
                                            </div>
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
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control amount" name="on_return_local_charges"  value="100">
                                                </fieldset>
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="card-title">National Charges</label>
                                                <fieldset class="form-group">
                                                    <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control amount" name="on_return_national_charges"  value="150">
                                                </fieldset>
                                            </div>
                                        </div>

                                        <hr>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Packaging Charges</h3>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="on_packaging_switch" class="switchery packagingChargesOvernight" data-color="success" data-size="sm" checked/>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row packaging-charges-div-overnight">
                                            <div class="col-md-3 text-center">
                                                <label class="card-title">Small Flyer</label>
                                                <fieldset class="form-group">
                                                    <input name="on_flyer_sm" type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="10">
                                                </fieldset>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <label class="card-title">Medium Flyer</label>
                                                <fieldset class="form-group">
                                                    <input name="on_flyer_md" type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="20">
                                                </fieldset>
                                            </div>

                                            <div class="col-md-3 text-center">
                                                <label class="card-title">Large Flyer</label>
                                                <fieldset class="form-group">
                                                    <input name="on_flyer_lg" type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="30">
                                                </fieldset>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <label class="card-title">Box</label>
                                                <fieldset class="form-group">
                                                    <input name="on_flyer_box" type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="40">
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
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control numeric on-discount-inp" name="on_discount_weight_rate" disabled>
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
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control numeric on-discount-inp" name="on_discount_cash_rate" disabled>
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
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control numeric on-discount-inp" name="on_discount_insurance_rate" disabled>
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
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control numeric on-discount-inp" name="on_discount_return_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>

                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Packaging</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox"  class="switchery discountSwitchesOvernight" data-size="xs" name="on_discount_packaging_switch"/>
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control numeric on-discount-inp" name="on_discount_packaging_rate" disabled>
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
                                        <a data-toggle="collapse" href="#overland" aria-expanded="false" aria-controls="collapse62"
                                           class="pull-right"><input name="ol_main_switch" type="checkbox" id="" class="switchery ol-main-switch" data-size="sm"/></a>
                                    </div>
                                </div>

                            </div>
                            <div id="overland" role="tabpanel" class="border-success no-border-top card-collapse collapse multi-collapse"
                                 aria-expanded="false">
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
                                                <div class="col text-center">
                                                    <label class="card-title">KG Range</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Local Charges</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">National Charges</label>
                                                </div>
                                                <div class="col"></div>
                                            </div>
                                            <div class="row">
                                                <div class="col text-center">
                                                    <fieldset class="form-group">
                                                        <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="0.1" name="ol_wa_range_up[0]">
                                                    </fieldset>
                                                </div>
                                                <div class="col text-center">

                                                    <fieldset class="form-group">
                                                        <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="0.9" name="ol_wa_range_down[0]">
                                                    </fieldset>
                                                </div>
                                                <div class="col text-center">

                                                    <div class="form-group " style="padding-top: 8px;">
                                                        <input type="checkbox" id="" class="switchery weightAdditionOverland0" data-color="success" data-size="sm" name="ol_wa_switch[0]"/>
                                                    </div>
                                                </div>
                                                <div class="col text-center">

                                                    <fieldset style="padding-top: 5px;">
                                                        <div class="input-group input-group-sm form-group">
                                                            <input type="text" class="touchspin-color input-sm spkg" value="0" disabled data-bts-button-down-class="btn btn-success"
                                                                   data-bts-button-up-class="btn btn-success" name="ol_wa_spkg[0]" data-rule-required="true" data-msg-required="This field is required">
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col text-center">
                                                    <fieldset class="form-group">
                                                        <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="100" name="ol_wa_local_charges[0]">
                                                    </fieldset>
                                                </div>
                                                <div class="col text-center">
                                                    <fieldset class="form-group">
                                                        <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="150" name="ol_wa_national_charges[0]">
                                                    </fieldset>
                                                </div>
                                                <div class="col"></div>
                                            </div>{{--Row--}}

                                        </div>{{--weight addition div--}}
                                        <div>
                                            <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="overland_weightadd"><i class="la la-plus"></i></button>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-3 text-center">
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Replacement</span>
                                                        </div>
                                                        <input type="text" class="form-control percent" data-rule-required="true" data-msg-required="This field is required" name="ol_replacement_charges">
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
                                                        <input type="text" class="form-control percent" data-rule-required="true" data-msg-required="This field is required" name="ol_tnb_charges">
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
                                            <div class="row" id="ol_cash_handle_0">
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="ol_cash_range_up[0]" type="text" class="form-control decimal numeric" data-rule-required="true" data-msg-required="This field is required" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="ol_cash_range_down[0]" type="text" class="form-control decimal numeric" data-rule-required="true" data-msg-required="This field is required" value="3000">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="ol_cash_charges[0]" type="text" class="form-control decimal amount" data-rule-required="true" data-msg-required="This field is required" value="0">
                                                    </fieldset>
                                                </div>
                                            </div>
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
                                            <div class="row">
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="ol_ins_range_up[0]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="ol_ins_range_down[0]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="3000">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="ol_ins_charges[0]" type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="0">
                                                    </fieldset>
                                                </div>
                                            </div>
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
                                                    <input type="text" class="form-control amount" name="ol_return_local_charges" data-rule-required="true" data-msg-required="This field is required" value="100">
                                                </fieldset>
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="card-title">National Charges</label>
                                                <fieldset class="form-group">
                                                    <input type="text" class="form-control amount" name="ol_return_national_charges" data-rule-required="true" data-msg-required="This field is required" value="150">
                                                </fieldset>
                                            </div>
                                        </div>

                                        <hr>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Packaging Charges</h3>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="ol_packaging_switch" class="switchery packagingChargesOverland" data-color="success" data-size="sm" checked/>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row packaging-charges-div-overland">
                                            <div class="col-md-3 text-center">
                                                <label class="card-title">Small Flyer</label>
                                                <fieldset class="form-group">
                                                    <input name="ol_flyer_sm" type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="10">
                                                </fieldset>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <label class="card-title">Medium Flyer</label>
                                                <fieldset class="form-group">
                                                    <input name="ol_flyer_md" type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="20">
                                                </fieldset>
                                            </div>

                                            <div class="col-md-3 text-center">
                                                <label class="card-title">Large Flyer</label>
                                                <fieldset class="form-group">
                                                    <input name="ol_flyer_lg" type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="30">
                                                </fieldset>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <label class="card-title">Box</label>
                                                <fieldset class="form-group">
                                                    <input name="ol_flyer_box" type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="40">
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
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control numeric ol-discount-inp" name="ol_discount_weight_rate" disabled>
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
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control numeric ol-discount-inp" name="ol_discount_cash_rate" disabled>
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
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control numeric ol-discount-inp" name="ol_discount_insurance_rate" disabled>
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
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control numeric ol-discount-inp" name="ol_discount_return_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>

                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Packaging</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="radio-addon3">
                                                                <input type="checkbox"  class="switchery discountSwitchesOverland" data-size="xs" name="ol_discount_packaging_switch"/>
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control numeric ol-discount-inp" name="ol_discount_packaging_rate" disabled>
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
                                        <a data-toggle="collapse" href="#detain" aria-expanded="false"
                                           class="pull-right"><input name="detain_main_switch" type="checkbox" id="" class="switchery detain-main-switch" data-size="sm"/></a>
                                    </div>
                                </div>

                            </div>
                            <div id="detain" role="tabpanel" class="border-success no-border-top card-collapse collapse multi-collapse"
                                 aria-expanded="false">
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
                                                <div class="col text-center">
                                                    <label class="card-title">KG Range</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Local Charges</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">National Charges</label>
                                                </div>
                                                <div class="col"></div>
                                            </div>
                                            <div class="row">
                                                <div class="col text-center">
                                                    <fieldset class="form-group">
                                                        <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="0" name="detain_wa_range_up[0]">
                                                    </fieldset>
                                                </div>
                                                <div class="col text-center">

                                                    <fieldset class="form-group">
                                                        <input type="text" class="form-control decimal"data-rule-required="true" data-msg-required="This field is required"  value="1" name="detain_wa_range_down[0]">
                                                    </fieldset>
                                                </div>
                                                <div class="col text-center">

                                                    <div class="form-group " style="padding-top: 8px;">
                                                        <input type="checkbox" id="" class="switchery weightAdditionDetain0" data-color="success" data-size="sm" name="detain_wa_switch[0]"/>
                                                    </div>
                                                </div>
                                                <div class="col text-center">

                                                    <fieldset style="padding-top: 5px;">
                                                        <div class="input-group input-group-sm form-group">
                                                            <input type="text" class="touchspin-color input-sm spkg" value="0" disabled data-bts-button-down-class="btn btn-success"
                                                                   data-bts-button-up-class="btn btn-success" name="detain_wa_spkg[0]">
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col text-center">
                                                    <fieldset class="form-group">
                                                        <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="100.00" name="detain_wa_local_charges[0]">
                                                    </fieldset>
                                                </div>
                                                <div class="col">
                                                    <fieldset class="form-group">
                                                        <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="150.00" name="detain_wa_national_charges[0]">
                                                    </fieldset>
                                                </div>
                                                <div class="col"></div>

                                            </div>{{--Row--}}

                                        </div>{{--weight addition div--}}
                                        <div>
                                            <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="detain_weightadd"><i class="la la-plus"></i></button>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-3 text-center">
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1">Replacement</span>
                                                        </div>
                                                        <input type="text"  class="form-control percent" name="detain_replacement_charges" data-rule-required="true" data-msg-required="This field is required">
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
                                                            <span class="input-group-text" id="basic-addon1">Try &amp; Buy</span>
                                                        </div>
                                                        <input type="text"  class="form-control percent" name="detain_tnb_charges" data-rule-required="true" data-msg-required="This field is required">
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
                                            <div class="row">
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="detain_cash_range_up[0]" type="text" class="form-control" data-rule-required="true" data-msg-required="This field is required" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="detain_cash_range_down[0]" type="text" class="form-control" data-rule-required="true" data-msg-required="This field is required" value="3000">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2">
                                                    <fieldset class="form-group">
                                                        <input name="detain_cash_charges[0]" type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="0">
                                                    </fieldset>
                                                </div>
                                            </div>
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
                                            <div class="row">
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="detain_ins_range_up[0]" type="text" class="form-control " data-rule-required="true" data-msg-required="This field is required" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="detain_ins_range_down[0]" type="text" class="form-control" data-rule-required="true" data-msg-required="This field is required" value="3000">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2">
                                                    <fieldset class="form-group">
                                                        <input name="detain_ins_charges[0]" type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="0">
                                                    </fieldset>
                                                </div>
                                            </div>
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
                                                    <input type="text" class="form-control amount" name="detain_return_local_charges" data-rule-required="true" data-msg-required="This field is required" value="100">
                                                </fieldset>
                                            </div>

                                            <div class="col-md-2">
                                                <label class="card-title">National Charges</label>
                                                <fieldset class="form-group">
                                                    <input type="text" class="form-control amount" name="detain_return_national_charges" data-rule-required="true" data-msg-required="This field is required" value="150">
                                                </fieldset>
                                            </div>
                                        </div>

                                        <hr>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Packaging Charges</h3>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="detain_packaging_switch" class="switchery packagingChargesDetain" data-color="success" data-size="sm" checked/>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row packaging-charges-div-detain">
                                            <div class="col-md-3 text-center">
                                                <label class="card-title">Small Flyer</label>
                                                <fieldset class="form-group">
                                                    <input name="detain_flyer_sm" type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="10">
                                                </fieldset>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <label class="card-title">Medium Flyer</label>
                                                <fieldset class="form-group">
                                                    <input name="detain_flyer_md" type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="20">
                                                </fieldset>
                                            </div>

                                            <div class="col-md-3 text-center">
                                                <label class="card-title">Large Flyer</label>
                                                <fieldset class="form-group">
                                                    <input name="detain_flyer_lg" type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="30">
                                                </fieldset>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <label class="card-title">Box</label>
                                                <fieldset class="form-group">
                                                    <input name="detain_flyer_box" type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="40">
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
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control numeric detain-discount-inp" name="detain_discount_weight_rate" disabled>
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
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control numeric detain-discount-inp" name="detain_discount_cash_rate" disabled>
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
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control numeric detain-discount-inp" name="detain_discount_insurance_rate" disabled>
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
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control numeric detain-discount-inp" name="detain_discount_return_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>

                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Packaging</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox"  class="switchery discountSwitchesDetain" data-size="xs" name="detain_discount_packaging_switch"/>
                                                              </span>
                                                        </div>
                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control numeric detain-discount-inp" name="detain_discount_packaging_rate" disabled>
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
                                        <a data-toggle="collapse" href="#sameday" aria-expanded="false"
                                           class="pull-right"><input name="sameday_main_switch" type="checkbox" id="" class="switchery sameday-main-switch" data-size="sm"/></a>
                                    </div>
                                </div>

                            </div>
                            <div id="sameday" role="tabpanel" class="border-success no-border-top card-collapse collapse multi-collapse"
                                 aria-expanded="false" style="height: 0px;">
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
                                                <div class="col text-center">
                                                    <label class="card-title">KG Range</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Local Charges</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">National Charges</label>
                                                </div>
                                                <div class="col"></div>

                                            </div>
                                            <div class="row">
                                                <div class="col text-center">
                                                    <fieldset class="form-group">
                                                        <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="0" name="sameday_wa_range_up[0]">
                                                    </fieldset>
                                                </div>
                                                <div class="col text-center">

                                                    <fieldset class="form-group">
                                                        <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="1" name="sameday_wa_range_down[0]">
                                                    </fieldset>
                                                </div>
                                                <div class="col text-center">

                                                    <div class="form-group " style="padding-top: 8px;">
                                                        <input type="checkbox" id="" class="switchery weightAdditionSameday0" data-color="success" data-size="sm" name="sameday_wa_switch[0]"/>
                                                    </div>
                                                </div>
                                                <div class="col text-center">

                                                    <fieldset style="padding-top: 5px;">
                                                        <div class="input-group input-group-sm form-group">
                                                            <input type="text" class="touchspin-color input-sm spkg" data-rule-required="true" data-msg-required="This field is required" value="0" disabled data-bts-button-down-class="btn btn-success"
                                                                   data-bts-button-up-class="btn btn-success" name="sameday_wa_spkg[0]">
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col text-center">
                                                    <fieldset class="form-group">
                                                        <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="100" name="sameday_wa_local_charges[0]">
                                                    </fieldset>
                                                </div>
                                                <div class="col text-center">
                                                    <fieldset class="form-group">
                                                        <input type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="150" name="sameday_wa_national_charges[0]">
                                                    </fieldset>
                                                </div>
                                                <div class="col"></div>
                                            </div>{{--Row--}}

                                        </div>{{--weight addition div--}}
                                        <div>
                                            <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="sameday_weightadd"><i class="la la-plus"></i></button>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-3 text-center">
                                                <fieldset>
                                                    <div class="input-group form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Replacement</span>
                                                        </div>
                                                        <input type="text"  class="form-control percent" name="sameday_replacement_charges" data-rule-required="true" data-msg-required="This field is required">
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
                                                        <input type="text"  class="form-control percent" name="sameday_tnb_charges" data-rule-required="true" data-msg-required="This field is required">
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
                                            <div class="row">
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="sameday_cash_range_up[0]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="sameday_cash_range_down[0]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="3000">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2">
                                                    <fieldset class="form-group">
                                                        <input name="sameday_cash_charges[0]" type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="0">
                                                    </fieldset>
                                                </div>
                                            </div>
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
                                            <div class="row">
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="sameday_ins_range_up[0]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="sameday_ins_range_down[0]" type="text" class="form-control numeric" data-rule-required="true" data-msg-required="This field is required" value="3000">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2">
                                                    <fieldset class="form-group">
                                                        <input name="sameday_ins_charges[0]" type="text" class="form-control amount" data-rule-required="true" data-msg-required="This field is required" value="0">
                                                    </fieldset>
                                                </div>
                                            </div>
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
                                                    <input type="text"  class="form-control amount" name="sameday_return_local_charges"  data-rule-required="true" data-msg-required="This field is required" value="100">
                                                </fieldset>
                                            </div>

                                            <div class="col-md-2">
                                                <label class="card-title">National Charges</label>
                                                <fieldset class="form-group">
                                                    <input type="text"  class="form-control amount" name="sameday_return_national_charges"  data-rule-required="true" data-msg-required="This field is required" value="150">
                                                </fieldset>
                                            </div>
                                        </div>

                                        <hr>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3 class="card-title">Packaging Charges</h3>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group ">
                                                    <input type="checkbox" name="sameday_packaging_switch" class="switchery packagingChargesSameday" data-color="success" data-size="sm" checked/>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row packaging-charges-div-sameday">
                                            <div class="col-md-3 text-center">
                                                <label class="card-title">Small Flyer</label>
                                                <fieldset class="form-group">
                                                    <input name="sameday_flyer_sm" type="text" class="form-control amount"  data-rule-required="true" data-msg-required="This field is required"  value="10">
                                                </fieldset>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <label class="card-title">Medium Flyer</label>
                                                <fieldset class="form-group">
                                                    <input name="sameday_flyer_md" type="text" class="form-control amount"  data-rule-required="true" data-msg-required="This field is required"  value="20">
                                                </fieldset>
                                            </div>

                                            <div class="col-md-3 text-center">
                                                <label class="card-title">Large Flyer</label>
                                                <fieldset class="form-group">
                                                    <input name="sameday_flyer_lg" type="text" class="form-control amount"  data-rule-required="true" data-msg-required="This field is required"  value="30">
                                                </fieldset>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <label class="card-title">Box</label>
                                                <fieldset class="form-group">
                                                    <input name="sameday_flyer_box" type="text" class="form-control amount"  data-rule-required="true" data-msg-required="This field is required"  value="40">
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
                                                        <input type="text"  class="form-control numeric sameday-discount-inp"  data-rule-required="true" data-msg-required="This field is required" name="sameday_discount_weight_rate" disabled>
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
                                                        <input type="text"  class="form-control numeric sameday-discount-inp"  data-rule-required="true" data-msg-required="This field is required" name="sameday_discount_cash_rate" disabled>
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
                                                        <input type="text"  class="form-control numeric sameday-discount-inp"  data-rule-required="true" data-msg-required="This field is required" name="sameday_discount_insurance_rate" disabled>
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
                                                        <input type="text"  class="form-control numeric sameday-discount-inp"  data-rule-required="true" data-msg-required="This field is required" name="sameday_discount_return_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>

                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Packaging</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox"  class="switchery discountSwitchesSameday" data-size="xs" name="sameday_discount_packaging_switch"/>
                                                              </span>
                                                        </div>
                                                        <input type="text"  class="form-control numeric sameday-discount-inp"  data-rule-required="true" data-msg-required="This field is required" name="sameday_discount_packaging_rate" disabled>
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

@section('customjs')
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $('.decimal').inputmask({
            'alias': 'decimal',
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            'digits': 2,
            'min': 0.00,
            'max': 1000
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
            'alias': 'numeric',
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            'min': 0,
            'max': 1000000
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
                        title: 'No Option Selected!',
                        text: 'Please select atleast one option!',
                        icon: 'warning'
                    });

                }
            }
        });


        //form post
        // $('#ratesAdditionForm').on('submit',function (e) {
        //     // $('#ratesAdditionForm').find(":input").prop("disabled", false);
        //     if(overnightSwitch.checked == true || overlandSwitch.checked == true || detainSwitch.checked == true || samedaySwitch.checked == true){
        //         if($('#ratesAdditionForm').valid()){
        //
        //             $('#ratesAdditionForm')[0].submit();
        //         }
        //     }else{
        //         e.preventDefault();
        //     }
        // });

    </script>
@endsection