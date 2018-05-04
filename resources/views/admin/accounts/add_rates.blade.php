@extends('admin.layout.master')

@section('content')
    <h1>Add Rates</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <h2 class="font-large-1">{{$shipper->name}}</h2>
                    </div>

                    <div class="card-content">
                        <form id="ratesAdditionForm" class="card-body card-dashboard" action="{{route('admin.add.rates.submit',['id'=>$shipper->id])}}" method="post">
                            @csrf
                            <div id="headingCollapse61" class="card-header border-success">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3 class="card-title lead success">Overnight</h3>
                                    </div>
                                    <div class="col-md-6">
                                        <a data-toggle="collapse" href="#overnight" aria-expanded="false" aria-controls="overnight"
                                           class="pull-right"><input name="on_main_switch" type="checkbox" id="" class="switchery" data-size="sm" /></a>
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
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Up</label>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Down</label>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Weight Addition</label>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Set pr kg Range</label>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Local Charges</label>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">National Charges</label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0.00" name="on_wa_range_up[]">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">

                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="1.10" name="on_wa_range_down[]">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">

                                                    <div class="form-group " style="padding-top: 8px;">
                                                        <input type="checkbox" id="" class="switchery weightAdditionOvernight" data-color="success" data-size="sm" name="on_wa_switch[0]"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 text-center">

                                                    <fieldset style="padding-top: 5px;">
                                                        <div class="input-group input-group-sm">
                                                            <input type="text" class="touchspin-color input-sm spkg" value="0" disabled data-bts-button-down-class="btn btn-success"
                                                                   data-bts-button-up-class="btn btn-success" name="on_wa_spkg[0]">
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="100" name="on_wa_local_charges[]">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2">
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="150" name="on_wa_national_charges[]">
                                                    </fieldset>
                                                </div>
                                            </div>{{--Row--}}

                                        </div>{{--weight addition div--}}
                                        <div>
                                            <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="waddition_btn"><i class="la la-plus"></i></button>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-3 text-center">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1">Replacement</span>
                                                        </div>
                                                        <input type="number" class="form-control" name="on_replacement_charges" aria-describedby="basic-addon1">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" id="basic-addon2">%</span>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1">Try & Buy</span>
                                                        </div>
                                                        <input type="number" class="form-control" name="on_tnb_charges" aria-describedby="basic-addon1">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" id="basic-addon2">%</span>
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
                                                        <input name="on_cash_range_up[]" type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="on_cash_range_down[]" type="number" class="form-control" id="" value="3000">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2">
                                                    <fieldset class="form-group">
                                                        <input name="on_cash_charges[]" type="number" class="form-control" id="" value="0">
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
                                            <div class="row">
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="on_ins_range_up[]" type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="on_ins_range_down[]" type="number" class="form-control" id="" value="3000">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2">
                                                    <fieldset class="form-group">
                                                        <input name="on_ins_charges[]" type="number" class="form-control" id="" value="0">
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
                                                    <input type="number" class="form-control" name="on_return_local_charges" value="100">
                                                </fieldset>
                                            </div>

                                            <div class="col-md-2">
                                                <label class="card-title">National Charges</label>
                                                <fieldset class="form-group">
                                                    <input type="number" class="form-control" name="on_return_national_charges" value="150">
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
                                                    <input name="on_flyer_sm" type="number" class="form-control" id="" value="10">
                                                </fieldset>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <label class="card-title">Medium Flyer</label>
                                                <fieldset class="form-group">
                                                    <input name="on_flyer_md" type="number" class="form-control" id="" value="20">
                                                </fieldset>
                                            </div>

                                            <div class="col-md-3 text-center">
                                                <label class="card-title">Large Flyer</label>
                                                <fieldset class="form-group">
                                                    <input name="on_flyer_lg" type="number" class="form-control" id="" value="30">
                                                </fieldset>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <label class="card-title">Box</label>
                                                <fieldset class="form-group">
                                                    <input name="on_flyer_box" type="number" class="form-control" id="" value="40">
                                                </fieldset>
                                            </div>
                                        </div>

                                        <hr>
                                        <div class="">
                                            <h3 class="card-title">Discount Rates</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Weight</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox" id="" class="switchery discountSwitchesOvernight" name="on_discount_weight_switch" data-size="xs" />
                                                              </span>
                                                        </div>
                                                        <input type="text" class="form-control on-discount-inp" name="on_discount_weight_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Cash</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="">
                                                                <input type="checkbox" name="on_discount_cash_switch" class="switchery discountSwitchesOvernight" data-size="xs" />
                                                              </span>
                                                        </div>
                                                        <input type="text" class="form-control on-discount-inp" name="on_discount_cash_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Insurance</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="">
                                                                <input type="checkbox" name="on_discount_insurance_switch" class="switchery discountSwitchesOvernight" data-size="xs" />
                                                              </span>
                                                        </div>
                                                        <input type="text" class="form-control on-discount-inp" name="on_discount_insurance_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Return</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox"  class="switchery discountSwitchesOvernight" data-size="xs" name="on_discount_return_switch"/>
                                                              </span>
                                                        </div>
                                                        <input type="text" class="form-control on-discount-inp" name="on_discount_return_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>

                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Packaging</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox"  class="switchery discountSwitchesOvernight" data-size="xs" name="on_discount_packaging_switch"/>
                                                              </span>
                                                        </div>
                                                        <input type="text" class="form-control on-discount-inp" name="on_discount_packaging_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-md-6">
                                                <label class="">Apply discount [to - from]</label>
                                                <div class='input-group'>
                                                    <input type='text' class="form-control daterange" name="on_daterange"/>
                                                    <div class="input-group-append">
                                                            <span class="input-group-text">
                                                              <span class="la la-calendar"></span>
                                                            </span>
                                                    </div>
                                                </div>

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
                                           class="pull-right"><input name="ol_main_switch" type="checkbox" id="" class="switchery" data-size="sm"/></a>
                                    </div>
                                </div>

                            </div>
                            <div id="overland" role="tabpanel" aria-labelledby="headingCollapse62" class="border-success no-border-top card-collapse collapse multi-collapse"
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
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Up</label>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Down</label>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Weight Addition</label>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Set pr kg Range</label>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Local Charges</label>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">National Charges</label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0.00" name="ol_wa_range_up[]">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">

                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="1.10" name="ol_wa_range_down[]">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">

                                                    <div class="form-group " style="padding-top: 8px;">
                                                        <input type="checkbox" id="" class="switchery weightAdditionOverland" data-color="success" data-size="sm" name="on_wa_switch[]"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 text-center">

                                                    <fieldset style="padding-top: 5px;">
                                                        <div class="input-group input-group-sm">
                                                            <input type="text" class="touchspin-color input-sm spkg" value="0" disabled data-bts-button-down-class="btn btn-success"
                                                                   data-bts-button-up-class="btn btn-success" name="ol_wa_spkg[]">
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="100" name="ol_wa_local_charges[]">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2">
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="150" name="ol_wa_national_charges[]">
                                                    </fieldset>
                                                </div>
                                            </div>{{--Row--}}

                                        </div>{{--weight addition div--}}
                                        <div>
                                            <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="overland_weightadd"><i class="la la-plus"></i></button>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-3 text-center">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1">Replacement</span>
                                                        </div>
                                                        <input type="number" class="form-control" name="ol_replacement_charges" aria-describedby="basic-addon1">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" id="basic-addon2">%</span>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1">Try & Buy</span>
                                                        </div>
                                                        <input type="number" class="form-control" name="ol_tnb_charges" aria-describedby="basic-addon1">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" id="basic-addon2">%</span>
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
                                                    <input type="checkbox" name="ol_cash_handling_switch"  class="switchery cashChargesOverland" data-color="success" data-size="sm" checked/>
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
                                            <div class="row">
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="ol_cash_range_up[]" type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="ol_cash_range_down[]" type="number" class="form-control" id="" value="3000">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2">
                                                    <fieldset class="form-group">
                                                        <input name="ol_cash_charges[]" type="number" class="form-control" id="" value="0">
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
                                                        <input name="ol_ins_range_up[]" type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="ol_ins_range_down[]" type="number" class="form-control" id="" value="3000">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2">
                                                    <fieldset class="form-group">
                                                        <input name="ol_ins_charges[]" type="number" class="form-control" id="" value="0">
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
                                                    <input type="number" class="form-control" name="ol_return_local_charges" value="100">
                                                </fieldset>
                                            </div>

                                            <div class="col-md-2">
                                                <label class="card-title">National Charges</label>
                                                <fieldset class="form-group">
                                                    <input type="number" class="form-control" name="ol_return_national_charges" value="150">
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
                                                    <input name="ol_flyer_sm" type="number" class="form-control" id="" value="10">
                                                </fieldset>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <label class="card-title">Medium Flyer</label>
                                                <fieldset class="form-group">
                                                    <input name="ol_flyer_md" type="number" class="form-control" id="" value="20">
                                                </fieldset>
                                            </div>

                                            <div class="col-md-3 text-center">
                                                <label class="card-title">Large Flyer</label>
                                                <fieldset class="form-group">
                                                    <input name="ol_flyer_lg" type="number" class="form-control" id="" value="30">
                                                </fieldset>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <label class="card-title">Box</label>
                                                <fieldset class="form-group">
                                                    <input name="ol_flyer_box" type="number" class="form-control" id="" value="40">
                                                </fieldset>
                                            </div>
                                        </div>

                                        <hr>
                                        <div class="">
                                            <h3 class="card-title">Discount Rates</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Weight</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox" id="" class="switchery discountSwitchesOverland" name="ol_discount_weight_switch" data-size="xs" />
                                                              </span>
                                                        </div>
                                                        <input type="text" class="form-control ol-discount-inp" name="ol_discount_weight_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Cash</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="">
                                                                <input type="checkbox" name="ol_cash_weight_switch" class="switchery discountSwitchesOverland" data-size="xs" />
                                                              </span>
                                                        </div>
                                                        <input type="text" class="form-control ol-discount-inp" name="ol_discount_cash_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Insurance</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="">
                                                                <input type="checkbox" name="ol_discount_insurance_switch" class="switchery discountSwitchesOverland" data-size="xs" />
                                                              </span>
                                                        </div>
                                                        <input type="text" class="form-control ol-discount-inp" name="ol_discount_insurance_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Return</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="radio-addon3">
                                                                <input type="checkbox"  class="switchery discountSwitchesOverland" data-size="xs" name="ol_discount_return_switch"/>
                                                              </span>
                                                        </div>
                                                        <input type="text" class="form-control ol-discount-inp" name="ol_discount_return_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>

                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Packaging</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="radio-addon3">
                                                                <input type="checkbox"  class="switchery discountSwitchesOverland" data-size="xs" name="ol_discount_packaging_switch"/>
                                                              </span>
                                                        </div>
                                                        <input type="text" class="form-control ol-discount-inp" name="ol_discount_packaging_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-md-6">
                                                <label class="">Apply discount [to - from]</label>
                                                <div class='input-group'>
                                                    <input type='text' class="form-control daterange" name="ol_daterange"/>
                                                    <div class="input-group-append">
                                                            <span class="input-group-text">
                                                              <span class="la la-calendar"></span>
                                                            </span>
                                                    </div>
                                                </div>

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
                                           class="pull-right"><input name="detain_main_switch" type="checkbox" id="" class="switchery" data-size="sm"/></a>
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
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Up</label>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Down</label>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Weight Addition</label>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Set pr kg Range</label>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Local Charges</label>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">National Charges</label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0.00" name="detain_wa_range_up[]">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">

                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="1.10" name="detain_wa_range_down[]">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">

                                                    <div class="form-group " style="padding-top: 8px;">
                                                        <input type="checkbox" id="" class="switchery weightAdditionDetain" data-color="success" data-size="sm" name="detain_wa_switch[]"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 text-center">

                                                    <fieldset style="padding-top: 5px;">
                                                        <div class="input-group input-group-sm">
                                                            <input type="text" class="touchspin-color input-sm spkg" value="0" disabled data-bts-button-down-class="btn btn-success"
                                                                   data-bts-button-up-class="btn btn-success" name="detain_wa_spkg[]">
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="100" name="detain_wa_local_charges[]">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2">
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="150" name="detain_wa_national_charges[]">
                                                    </fieldset>
                                                </div>
                                            </div>{{--Row--}}

                                        </div>{{--weight addition div--}}
                                        <div>
                                            <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="detain_weightadd"><i class="la la-plus"></i></button>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-3 text-center">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1">Replacement</span>
                                                        </div>
                                                        <input type="number" class="form-control" name="detain_replacement_charges" aria-describedby="basic-addon1">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" id="basic-addon2">%</span>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1">Try & Buy</span>
                                                        </div>
                                                        <input type="number" class="form-control" name="detain_tnb_charges" aria-describedby="basic-addon1">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" id="basic-addon2">%</span>
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
                                                        <input name="detain_cash_range_up[]" type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="detain_cash_range_down[]" type="number" class="form-control" id="" value="3000">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2">
                                                    <fieldset class="form-group">
                                                        <input name="detain_cash_charges[]" type="number" class="form-control" id="" value="0">
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
                                                        <input name="detain_ins_range_up[]" type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="detain_ins_range_down[]" type="number" class="form-control" id="" value="3000">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2">
                                                    <fieldset class="form-group">
                                                        <input name="detain_ins_charges[]" type="number" class="form-control" id="" value="0">
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
                                                    <input type="number" class="form-control" name="detain_return_local_charges" value="100">
                                                </fieldset>
                                            </div>

                                            <div class="col-md-2">
                                                <label class="card-title">National Charges</label>
                                                <fieldset class="form-group">
                                                    <input type="number" class="form-control" name="detain_return_national_charges" value="150">
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
                                                    <input name="detain_flyer_sm" type="number" class="form-control" id="" value="10">
                                                </fieldset>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <label class="card-title">Medium Flyer</label>
                                                <fieldset class="form-group">
                                                    <input name="detain_flyer_md" type="number" class="form-control" id="" value="20">
                                                </fieldset>
                                            </div>

                                            <div class="col-md-3 text-center">
                                                <label class="card-title">Large Flyer</label>
                                                <fieldset class="form-group">
                                                    <input name="detain_flyer_lg" type="number" class="form-control" id="" value="30">
                                                </fieldset>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <label class="card-title">Box</label>
                                                <fieldset class="form-group">
                                                    <input name="detain_flyer_box" type="number" class="form-control" id="" value="40">
                                                </fieldset>
                                            </div>
                                        </div>

                                        <hr>
                                        <div class="">
                                            <h3 class="card-title">Discount Rates</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Weight</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox" id="" class="switchery discountSwitchesDetain" name="detain_discount_weight_switch" data-size="xs" />
                                                              </span>
                                                        </div>
                                                        <input type="text" class="form-control detain-discount-inp" name="detain_discount_weight_rate">
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Cash</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="">
                                                                <input type="checkbox" name="detain_cash_weight_switch" class="switchery discountSwitchesDetain" data-size="xs" />
                                                              </span>
                                                        </div>
                                                        <input type="text" class="form-control detain-discount-inp" name="detain_discount_cash_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Insurance</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="">
                                                                <input type="checkbox" name="detain_discount_insurance_switch" class="switchery discountSwitchesDetain" data-size="xs" />
                                                              </span>
                                                        </div>
                                                        <input type="text" class="form-control detain-discount-inp" name="detain_discount_insurance_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Return</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox"  class="switchery discountSwitchesDetain" data-size="xs" name="detain_discount_return_switch"/>
                                                              </span>
                                                        </div>
                                                        <input type="text" class="form-control detain-discount-inp" name="detain_discount_return_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>

                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Packaging</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox"  class="switchery discountSwitchesDetain" data-size="xs" name="detain_discount_packaging_switch"/>
                                                              </span>
                                                        </div>
                                                        <input type="text" class="form-control detain-discount-inp" name="detain_discount_packaging_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-md-6">
                                                <label class="">Apply discount [to - from]</label>
                                                <div class='input-group'>
                                                    <input type='text' class="form-control daterange" name="detain_daterange"/>
                                                    <div class="input-group-append">
                                                            <span class="input-group-text">
                                                              <span class="la la-calendar"></span>
                                                            </span>
                                                    </div>
                                                </div>

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
                                           class="pull-right"><input name="sameday_main_switch" type="checkbox" id="" class="switchery" data-size="sm"/></a>
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
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Up</label>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Down</label>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Weight Addition</label>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Set pr kg Range</label>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Local Charges</label>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">National Charges</label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0.00" name="sameday_wa_range_up[]">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">

                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="1.10" name="sameday_wa_range_down[]">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">

                                                    <div class="form-group " style="padding-top: 8px;">
                                                        <input type="checkbox" id="" class="switchery weightAdditionSameday" data-color="success" data-size="sm" name="sameday_wa_switch[]"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 text-center">

                                                    <fieldset style="padding-top: 5px;">
                                                        <div class="input-group input-group-sm">
                                                            <input type="text" class="touchspin-color input-sm spkg" value="0" disabled data-bts-button-down-class="btn btn-success"
                                                                   data-bts-button-up-class="btn btn-success" name="sameday_wa_spkg[]">
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="100" name="sameday_wa_local_charges[]">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2">
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="150" name="sameday_wa_national_charges[]">
                                                    </fieldset>
                                                </div>
                                            </div>{{--Row--}}

                                        </div>{{--weight addition div--}}
                                        <div>
                                            <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="sameday_weightadd"><i class="la la-plus"></i></button>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-3 text-center">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1">Replacement</span>
                                                        </div>
                                                        <input type="number" class="form-control" name="sameday_replacement_charges" aria-describedby="basic-addon1">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" id="basic-addon2">%</span>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1">Try & Buy</span>
                                                        </div>
                                                        <input type="number" class="form-control" name="sameday_tnb_charges" aria-describedby="basic-addon1">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" id="basic-addon2">%</span>
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
                                                        <input name="sameday_cash_range_up[]" type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="sameday_cash_range_down[]" type="number" class="form-control" id="" value="3000">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2">
                                                    <fieldset class="form-group">
                                                        <input name="sameday_cash_charges[]" type="number" class="form-control" id="" value="0">
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
                                                        <input name="sameday_ins_range_up[]" type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="sameday_ins_range_down[]" type="number" class="form-control" id="" value="3000">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2">
                                                    <fieldset class="form-group">
                                                        <input name="sameday_ins_charges[]" type="number" class="form-control" id="" value="0">
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
                                                    <input type="number" class="form-control" name="sameday_return_local_charges" value="100">
                                                </fieldset>
                                            </div>

                                            <div class="col-md-2">
                                                <label class="card-title">National Charges</label>
                                                <fieldset class="form-group">
                                                    <input type="number" class="form-control" name="sameday_return_national_charges" value="150">
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
                                                    <input name="sameday_flyer_sm" type="number" class="form-control" id="" value="10">
                                                </fieldset>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <label class="card-title">Medium Flyer</label>
                                                <fieldset class="form-group">
                                                    <input name="sameday_flyer_md" type="number" class="form-control" id="" value="20">
                                                </fieldset>
                                            </div>

                                            <div class="col-md-3 text-center">
                                                <label class="card-title">Large Flyer</label>
                                                <fieldset class="form-group">
                                                    <input name="sameday_flyer_lg" type="number" class="form-control" id="" value="30">
                                                </fieldset>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <label class="card-title">Box</label>
                                                <fieldset class="form-group">
                                                    <input name="sameday_flyer_box" type="number" class="form-control" id="" value="40">
                                                </fieldset>
                                            </div>
                                        </div>

                                        <hr>
                                        <div class="">
                                            <h3 class="card-title">Discount Rates</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Weight</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox" id="" class="switchery discountSwitchesSameday" name="sameday_discount_weight_switch" data-size="xs" />
                                                              </span>
                                                        </div>
                                                        <input type="text" class="form-control sameday-discount-inp" name="sameday_discount_weight_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Cash</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="">
                                                                <input type="checkbox" name="sameday_cash_weight_switch" class="switchery discountSwitchesSameday" data-size="xs" />
                                                              </span>
                                                        </div>
                                                        <input type="text" class="form-control sameday-discount-inp" name="sameday_discount_cash_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Insurance</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text" id="">
                                                                <input type="checkbox" name="sameday_discount_insurance_switch" class="switchery discountSwitchesSameday" data-size="xs" />
                                                              </span>
                                                        </div>
                                                        <input type="text" class="form-control sameday-discount-inp" name="sameday_discount_insurance_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Return</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox"  class="switchery discountSwitchesSameday" data-size="xs" name="sameday_discount_return_switch"/>
                                                              </span>
                                                        </div>
                                                        <input type="text" class="form-control sameday-discount-inp" name="sameday_discount_return_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>

                                            <div class="col text-center">
                                                <fieldset>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Packaging</span>
                                                        </div>
                                                        <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox"  class="switchery discountSwitchesSameday" data-size="xs" name="sameday_discount_packaging_switch"/>
                                                              </span>
                                                        </div>
                                                        <input type="text" class="form-control sameday-discount-inp" name="sameday_discount_packaging_rate" disabled>
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-md-6">
                                                <label class="">Apply discount [to - from]</label>
                                                <div class='input-group'>
                                                    <input type='text' class="form-control daterange" name="sameday_daterange"/>
                                                    <div class="input-group-append">
                                                            <span class="input-group-text">
                                                              <span class="la la-calendar"></span>
                                                            </span>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-2">
                                <div class="form-group">

                                    <button id="addRatesSubmit" type="button" class="btn btn-outline-success round btn-min-width mr-1 mb-1">Submit</button>
                                </div>
                            </div>


                        </form>

                    </div>
                </div>
            </div>
        </div>

    </section>


@endsection