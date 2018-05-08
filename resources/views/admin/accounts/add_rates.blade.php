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
                        <form class="card-body card-dashboard" action="{{route('admin.add.rates.submit',['id'=>$shipper->id])}}" method="post">
                            @csrf
                                <div id="headingCollapse61" class="card-header border-success">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h3 class="card-title lead success">Overnight</h3>
                                        </div>
                                        <div class="col-md-6">
                                            <a data-toggle="collapse" href="#overnight" aria-expanded="false" aria-controls="overnight"
                                               class="pull-right"><input type="checkbox" id="" class="switchery" data-size="sm"/></a>
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
                                                        <input type="number" class="form-control" id="" value="0.00" name="wa_range_up[]">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">

                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="1.10" name="wa_range_down[]">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">

                                                    <div class="form-group " style="padding-top: 8px;">
                                                        <input type="checkbox" id="" class="switchery weightAddition" data-color="success" data-size="sm" name="wa_switch[]"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 text-center">

                                                    <fieldset style="padding-top: 5px;">
                                                        <div class="input-group input-group-sm">
                                                            <input type="text" class="touchspin-color input-sm spkg" value="0" disabled data-bts-button-down-class="btn btn-success"
                                                                   data-bts-button-up-class="btn btn-success" name="wa_spkg[]">
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="100" name="wa_local_charges[]">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2">
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="150" name="wa_national_charges[]">
                                                    </fieldset>
                                                </div>
                                            </div>{{--Row--}}

                                            </div>{{--weight addition div--}}
                                            <div>
                                                <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="waddition_btn"><i class="la la-plus"></i></button>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Cash Handling Charges</h3>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group ">
                                                        <input type="checkbox"  class="switchery cashChargesOvernight" data-color="success" data-size="sm" checked/>
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
                                                        <input name="cash_range_up[]" type="number" class="form-control" id="" value="">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="cash_range_down[]" type="number" class="form-control" id="" value="">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2">
                                                    <fieldset class="form-group">
                                                        <input name="cash_charges[]" type="number" class="form-control" id="" value="">
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
                                                        <input type="checkbox"  class="switchery insuranceChargesOvernight" data-color="success" data-size="sm" checked/>
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
                                                        <input name="ins_range_up[]" type="number" class="form-control" id="" value="0.00">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <fieldset class="form-group">
                                                        <input name="ins_range_down[]" type="number" class="form-control" id="" value="1.10">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2">
                                                    <fieldset class="form-group">
                                                        <input name="ins_charges[]" type="number" class="form-control" id="" value="150">
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
                                                        <input type="checkbox"  class="switchery returnChargesOvernight" data-color="success" data-size="sm" checked/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row return-charges-div-overnight">

                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Local Charges</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="100">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2">
                                                    <label class="card-title">National Charges</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="150">
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
                                                        <input type="checkbox"  class="switchery packagingChargesOvernight" data-color="success" data-size="sm" checked/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row packaging-charges-div-overnight">
                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Small Flyer</label>
                                                    <fieldset class="form-group">
                                                        <input name="flyer-sm-overnight" type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Medium Flyer</label>
                                                    <fieldset class="form-group">
                                                        <input name="flyer-md-overnight" type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Large Flyer</label>
                                                    <fieldset class="form-group">
                                                        <input name="flyer-lg-overnight" type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Box</label>
                                                    <fieldset class="form-group">
                                                        <input name="flyer-box-overnight" type="number" class="form-control" id="" value="0">
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
                                                              <span class="input-group-text" id="radio-addon3">
                                                                <input type="checkbox" id="" class="switchery" data-size="xs" />
                                                              </span>
                                                            </div>
                                                            <input type="text" class="form-control" >
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
                                                              <span class="input-group-text" id="radio-addon3">
                                                                <input type="checkbox" id="" class="switchery" data-size="xs" />
                                                              </span>
                                                            </div>
                                                            <input type="text" class="form-control" >
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
                                                                <input type="checkbox" id="" class="switchery" data-size="xs" />
                                                              </span>
                                                            </div>
                                                            <input type="text" class="form-control" >
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
                                                                <input type="checkbox" id="switchery" class="switchery" data-size="xs" />
                                                              </span>
                                                            </div>
                                                            <input type="text" class="form-control" >
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
                                                                <input type="checkbox" id="switchery" class="switchery" data-size="xs"/>
                                                              </span>
                                                            </div>
                                                            <input type="text" class="form-control" >
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row mt-1">
                                                <div class="col-md-6">
                                                    <label class="">Apply discount [to - from]</label>
                                                    <div class='input-group'>
                                                        <input type='text' class="form-control daterange" name="daterange"/>
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
                                               class="pull-right"><input type="checkbox" id="" class="switchery" data-size="sm"/></a>
                                        </div>
                                    </div>

                                </div>
                                <div id="overland" role="tabpanel" aria-labelledby="headingCollapse62" class="border-success no-border-top card-collapse collapse multi-collapse"
                                     aria-expanded="false">
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
                                                            <input type="number" class="form-control" id="" value="0.00" name="wa_range_up[]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">

                                                        <fieldset class="form-group">
                                                            <input type="number" class="form-control" id="" value="1.10" name="wa_range_down[]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="" class="switchery weightAddition" data-color="success" data-size="sm" name="wa_switch[]"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 text-center">

                                                        <fieldset style="padding-top: 5px;">
                                                            <div class="input-group input-group-sm">
                                                                <input type="text" class="touchspin-color input-sm spkg" value="0" disabled data-bts-button-down-class="btn btn-success"
                                                                       data-bts-button-up-class="btn btn-success" name="wa_spkg[]">
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input type="number" class="form-control" id="" value="100" name="wa_local_charges[]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <fieldset class="form-group">
                                                            <input type="number" class="form-control" id="" value="150" name="wa_national_charges[]">
                                                        </fieldset>
                                                    </div>
                                                </div>{{--Row--}}

                                            </div>{{--weight addition div--}}
                                            <div>
                                                <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="waddition_btn"><i class="la la-plus"></i></button>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Cash Handling Charges</h3>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group ">
                                                        <input type="checkbox"  class="switchery cashChargesOvernight" data-color="success" data-size="sm" checked/>
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
                                                            <input name="cash_range_up[]" type="number" class="form-control" id="" value="">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="cash_range_down[]" type="number" class="form-control" id="" value="">
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <fieldset class="form-group">
                                                            <input name="cash_charges[]" type="number" class="form-control" id="" value="">
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
                                                        <input type="checkbox"  class="switchery insuranceChargesOvernight" data-color="success" data-size="sm" checked/>
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
                                                            <input name="ins_range_up[]" type="number" class="form-control" id="" value="0.00">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="ins_range_down[]" type="number" class="form-control" id="" value="1.10">
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <fieldset class="form-group">
                                                            <input name="ins_charges[]" type="number" class="form-control" id="" value="150">
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
                                                        <input type="checkbox"  class="switchery returnChargesOvernight" data-color="success" data-size="sm" checked/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row return-charges-div-overnight">

                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Local Charges</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="100">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2">
                                                    <label class="card-title">National Charges</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="150">
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
                                                        <input type="checkbox"  class="switchery packagingChargesOvernight" data-color="success" data-size="sm" checked/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row packaging-charges-div-overnight">
                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Small Flyer</label>
                                                    <fieldset class="form-group">
                                                        <input name="flyer-sm-overnight" type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Medium Flyer</label>
                                                    <fieldset class="form-group">
                                                        <input name="flyer-md-overnight" type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Large Flyer</label>
                                                    <fieldset class="form-group">
                                                        <input name="flyer-lg-overnight" type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Box</label>
                                                    <fieldset class="form-group">
                                                        <input name="flyer-box-overnight" type="number" class="form-control" id="" value="0">
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
                                                              <span class="input-group-text" id="radio-addon3">
                                                                <input type="checkbox" id="" class="switchery" data-size="xs" />
                                                              </span>
                                                            </div>
                                                            <input type="text" class="form-control" >
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
                                                              <span class="input-group-text" id="radio-addon3">
                                                                <input type="checkbox" id="" class="switchery" data-size="xs" />
                                                              </span>
                                                            </div>
                                                            <input type="text" class="form-control" >
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
                                                                <input type="checkbox" id="" class="switchery" data-size="xs" />
                                                              </span>
                                                            </div>
                                                            <input type="text" class="form-control" >
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
                                                                <input type="checkbox" id="switchery" class="switchery" data-size="xs" />
                                                              </span>
                                                            </div>
                                                            <input type="text" class="form-control" >
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
                                                                <input type="checkbox" id="switchery" class="switchery" data-size="xs"/>
                                                              </span>
                                                            </div>
                                                            <input type="text" class="form-control" >
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row mt-1">
                                                <div class="col-md-6">
                                                    <label class="">Apply discount [to - from]</label>
                                                    <div class='input-group'>
                                                        <input type='text' class="form-control daterange" name="daterange"/>
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
                                               class="pull-right"><input type="checkbox" id="" class="switchery" data-size="sm"/></a>
                                        </div>
                                    </div>

                                </div>
                                <div id="detain" role="tabpanel" class="border-success no-border-top card-collapse collapse multi-collapse"
                                     aria-expanded="false">
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
                                                            <input type="number" class="form-control" id="" value="0.00" name="wa_range_up[]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">

                                                        <fieldset class="form-group">
                                                            <input type="number" class="form-control" id="" value="1.10" name="wa_range_down[]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="" class="switchery weightAddition" data-color="success" data-size="sm" name="wa_switch[]"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 text-center">

                                                        <fieldset style="padding-top: 5px;">
                                                            <div class="input-group input-group-sm">
                                                                <input type="text" class="touchspin-color input-sm spkg" value="0" disabled data-bts-button-down-class="btn btn-success"
                                                                       data-bts-button-up-class="btn btn-success" name="wa_spkg[]">
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input type="number" class="form-control" id="" value="100" name="wa_local_charges[]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <fieldset class="form-group">
                                                            <input type="number" class="form-control" id="" value="150" name="wa_national_charges[]">
                                                        </fieldset>
                                                    </div>
                                                </div>{{--Row--}}

                                            </div>{{--weight addition div--}}
                                            <div>
                                                <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="waddition_btn"><i class="la la-plus"></i></button>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Cash Handling Charges</h3>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group ">
                                                        <input type="checkbox"  class="switchery cashChargesOvernight" data-color="success" data-size="sm" checked/>
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
                                                            <input name="cash_range_up[]" type="number" class="form-control" id="" value="">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="cash_range_down[]" type="number" class="form-control" id="" value="">
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <fieldset class="form-group">
                                                            <input name="cash_charges[]" type="number" class="form-control" id="" value="">
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
                                                        <input type="checkbox"  class="switchery insuranceChargesOvernight" data-color="success" data-size="sm" checked/>
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
                                                            <input name="ins_range_up[]" type="number" class="form-control" id="" value="0.00">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="ins_range_down[]" type="number" class="form-control" id="" value="1.10">
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <fieldset class="form-group">
                                                            <input name="ins_charges[]" type="number" class="form-control" id="" value="150">
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
                                                        <input type="checkbox"  class="switchery returnChargesOvernight" data-color="success" data-size="sm" checked/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row return-charges-div-overnight">

                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Local Charges</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="100">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2">
                                                    <label class="card-title">National Charges</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="150">
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
                                                        <input type="checkbox"  class="switchery packagingChargesOvernight" data-color="success" data-size="sm" checked/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row packaging-charges-div-overnight">
                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Small Flyer</label>
                                                    <fieldset class="form-group">
                                                        <input name="flyer-sm-overnight" type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Medium Flyer</label>
                                                    <fieldset class="form-group">
                                                        <input name="flyer-md-overnight" type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Large Flyer</label>
                                                    <fieldset class="form-group">
                                                        <input name="flyer-lg-overnight" type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Box</label>
                                                    <fieldset class="form-group">
                                                        <input name="flyer-box-overnight" type="number" class="form-control" id="" value="0">
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
                                                              <span class="input-group-text" id="radio-addon3">
                                                                <input type="checkbox" id="" class="switchery" data-size="xs" />
                                                              </span>
                                                            </div>
                                                            <input type="text" class="form-control" >
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
                                                              <span class="input-group-text" id="radio-addon3">
                                                                <input type="checkbox" id="" class="switchery" data-size="xs" />
                                                              </span>
                                                            </div>
                                                            <input type="text" class="form-control" >
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
                                                                <input type="checkbox" id="" class="switchery" data-size="xs" />
                                                              </span>
                                                            </div>
                                                            <input type="text" class="form-control" >
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
                                                                <input type="checkbox" id="switchery" class="switchery" data-size="xs" />
                                                              </span>
                                                            </div>
                                                            <input type="text" class="form-control" >
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
                                                                <input type="checkbox" id="switchery" class="switchery" data-size="xs"/>
                                                              </span>
                                                            </div>
                                                            <input type="text" class="form-control" >
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row mt-1">
                                                <div class="col-md-6">
                                                    <label class="">Apply discount [to - from]</label>
                                                    <div class='input-group'>
                                                        <input type='text' class="form-control daterange" name="daterange"/>
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
                                               class="pull-right"><input type="checkbox" id="" class="switchery" data-size="sm"/></a>
                                        </div>
                                    </div>

                                </div>
                                <div id="sameday" role="tabpanel" aria-labelledby="headingCollapse64" class="border-success no-border-top card-collapse collapse multi-collapse"
                                     aria-expanded="false" style="height: 0px;">
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
                                                            <input type="number" class="form-control" id="" value="0.00" name="wa_range_up[]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">

                                                        <fieldset class="form-group">
                                                            <input type="number" class="form-control" id="" value="1.10" name="wa_range_down[]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">

                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" id="" class="switchery weightAddition" data-color="success" data-size="sm" name="wa_switch[]"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 text-center">

                                                        <fieldset style="padding-top: 5px;">
                                                            <div class="input-group input-group-sm">
                                                                <input type="text" class="touchspin-color input-sm spkg" value="0" disabled data-bts-button-down-class="btn btn-success"
                                                                       data-bts-button-up-class="btn btn-success" name="wa_spkg[]">
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input type="number" class="form-control" id="" value="100" name="wa_local_charges[]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <fieldset class="form-group">
                                                            <input type="number" class="form-control" id="" value="150" name="wa_national_charges[]">
                                                        </fieldset>
                                                    </div>
                                                </div>{{--Row--}}

                                            </div>{{--weight addition div--}}
                                            <div>
                                                <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="waddition_btn"><i class="la la-plus"></i></button>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Cash Handling Charges</h3>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group ">
                                                        <input type="checkbox"  class="switchery cashChargesOvernight" data-color="success" data-size="sm" checked/>
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
                                                            <input name="cash_range_up[]" type="number" class="form-control" id="" value="">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="cash_range_down[]" type="number" class="form-control" id="" value="">
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <fieldset class="form-group">
                                                            <input name="cash_charges[]" type="number" class="form-control" id="" value="">
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
                                                        <input type="checkbox"  class="switchery insuranceChargesOvernight" data-color="success" data-size="sm" checked/>
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
                                                            <input name="ins_range_up[]" type="number" class="form-control" id="" value="0.00">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <fieldset class="form-group">
                                                            <input name="ins_range_down[]" type="number" class="form-control" id="" value="1.10">
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <fieldset class="form-group">
                                                            <input name="ins_charges[]" type="number" class="form-control" id="" value="150">
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
                                                        <input type="checkbox"  class="switchery returnChargesOvernight" data-color="success" data-size="sm" checked/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row return-charges-div-overnight">

                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Local Charges</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="100">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2">
                                                    <label class="card-title">National Charges</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="150">
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
                                                        <input type="checkbox"  class="switchery packagingChargesOvernight" data-color="success" data-size="sm" checked/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row packaging-charges-div-overnight">
                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Small Flyer</label>
                                                    <fieldset class="form-group">
                                                        <input name="flyer-sm-overnight" type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Medium Flyer</label>
                                                    <fieldset class="form-group">
                                                        <input name="flyer-md-overnight" type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Large Flyer</label>
                                                    <fieldset class="form-group">
                                                        <input name="flyer-lg-overnight" type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Box</label>
                                                    <fieldset class="form-group">
                                                        <input name="flyer-box-overnight" type="number" class="form-control" id="" value="0">
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
                                                              <span class="input-group-text" id="radio-addon3">
                                                                <input type="checkbox" id="" class="switchery" data-size="xs" />
                                                              </span>
                                                            </div>
                                                            <input type="text" class="form-control" >
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
                                                              <span class="input-group-text" id="radio-addon3">
                                                                <input type="checkbox" id="" class="switchery" data-size="xs" />
                                                              </span>
                                                            </div>
                                                            <input type="text" class="form-control" >
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
                                                                <input type="checkbox" id="" class="switchery" data-size="xs" />
                                                              </span>
                                                            </div>
                                                            <input type="text" class="form-control" >
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
                                                                <input type="checkbox" id="switchery" class="switchery" data-size="xs" />
                                                              </span>
                                                            </div>
                                                            <input type="text" class="form-control" >
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
                                                                <input type="checkbox" id="switchery" class="switchery" data-size="xs"/>
                                                              </span>
                                                            </div>
                                                            <input type="text" class="form-control" >
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row mt-1">
                                                <div class="col-md-6">
                                                    <label class="">Apply discount [to - from]</label>
                                                    <div class='input-group'>
                                                        <input type='text' class="form-control daterange" name="daterange"/>
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

                                            <button id="addRatesSubmit" type="submit" class="btn btn-outline-success round btn-min-width mr-1 mb-1">Submit</button>
                                        </div>
                                    </div>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


@endsection