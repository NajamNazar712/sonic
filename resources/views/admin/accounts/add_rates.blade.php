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
                        <div class="card-body card-dashboard">
                            <div class="card collapse-icon accordion-icon-rotate">
                                <div id="headingCollapse61" class="card-header border-success">
                                    <a data-toggle="collapse" href="#overnight" aria-expanded="true" aria-controls="collapse61"
                                       class="card-title lead success">Overnight</a> <input type="checkbox" id="" class="switchery" data-size="xs" checked/>
                                </div>
                                <div id="overnight" role="tabpanel" aria-labelledby="headingCollapse61" class="card-collapse collapse show border-success"
                                     aria-expanded="true">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Weight Charges</h3>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Up</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0.00">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Down</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="1.10">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Weight Addition</label>
                                                    <div class="form-group " style="padding-top: 8px;">
                                                        <input type="checkbox"  class="switchery" data-color="success" data-size="sm" />
                                                    </div>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Set pr kg Range</label>
                                                    <fieldset style="padding-top: 5px;">
                                                        <div class="input-group input-group-sm">
                                                            <input type="text" class="touchspin-color input-sm" value="0" disabled data-bts-button-down-class="btn btn-success"
                                                                   data-bts-button-up-class="btn btn-success">
                                                        </div>
                                                    </fieldset>
                                                </div>
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
                                            <div>
                                                <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs"><i class="la la-plus"></i></button>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Cash Handling Charges</h3>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group ">
                                                        <input type="checkbox"  class="switchery" data-color="success" data-size="sm" checked/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Up</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0.00">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Down</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="1.10">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2">
                                                    <label class="card-title">Charges</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="150">
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs"><i class="la la-plus"></i></button>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Insurance Charges</h3>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group ">
                                                        <input type="checkbox"  class="switchery" data-color="success" data-size="sm" checked/>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Up</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0.00">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Down</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="1.10">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2">
                                                    <label class="card-title">Charges</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="150">
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs"><i class="la la-plus"></i></button>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Return Charges</h3>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group ">
                                                        <input type="checkbox"  class="switchery" data-color="success" data-size="sm" checked/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">

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
                                                        <input type="checkbox"  class="switchery" data-color="success" data-size="sm" checked/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Small Flyer</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Medium Flyer</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Large Flyer</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Box</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0">
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
                                    <a data-toggle="collapse" href="#overland" aria-expanded="false" aria-controls="collapse62"
                                       class="card-title lead success collapsed">Overland</a> <input type="checkbox" id="" class="switchery" data-color="success" data-size="xs" />
                                </div>
                                <div id="overland" role="tabpanel" aria-labelledby="headingCollapse62" class="border-danger no-border-top card-collapse collapse"
                                     aria-expanded="false">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Weight Charges</h3>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Up</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0.00">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Down</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="1.10">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Weight Addition</label>
                                                    <div class="form-group " style="padding-top: 8px;">
                                                        <input type="checkbox"  class="switchery" data-color="success" data-size="sm" />
                                                    </div>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Set pr kg Range</label>
                                                    <fieldset style="padding-top: 5px;">
                                                        <div class="input-group input-group-sm">
                                                            <input type="text" class="touchspin-color input-sm" value="0" disabled data-bts-button-down-class="btn btn-success"
                                                                   data-bts-button-up-class="btn btn-success">
                                                        </div>
                                                    </fieldset>
                                                </div>
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
                                            <div>
                                                <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs"><i class="la la-plus"></i></button>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Cash Handling Charges</h3>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group ">
                                                        <input type="checkbox"  class="switchery" data-color="success" data-size="sm" checked/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Up</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0.00">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Down</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="1.10">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2">
                                                    <label class="card-title">Charges</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="150">
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs"><i class="la la-plus"></i></button>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Insurance Charges</h3>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group ">
                                                        <input type="checkbox"  class="switchery" data-color="success" data-size="sm" checked/>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Up</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0.00">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Down</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="1.10">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2">
                                                    <label class="card-title">Charges</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="150">
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs"><i class="la la-plus"></i></button>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Return Charges</h3>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group ">
                                                        <input type="checkbox"  class="switchery" data-color="success" data-size="sm" checked/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">

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
                                                        <input type="checkbox"  class="switchery" data-color="success" data-size="sm" checked/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Small Flyer</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Medium Flyer</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Large Flyer</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Box</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0">
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
                                                                <span class="input-group-text" id="">Cash</span>
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
                                                                <span class="input-group-text" id="">Insurance</span>
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
                                                                <span class="input-group-text" id="">Return</span>
                                                            </div>
                                                            <div class="input-group-prepend">
                                                              <span class="input-group-text" id="">
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
                                                              <span class="input-group-text" id="">
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
                                    <a data-toggle="collapse" href="#detain" aria-expanded="false"
                                       class="card-title lead success collapsed">Detain</a> <input type="checkbox" id="" class="switchery" data-color="success" data-size="xs" />
                                </div>
                                <div id="detain" role="tabpanel" class="border-success no-border-top card-collapse collapse"
                                     aria-expanded="false">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Weight Charges</h3>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Up</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0.00">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Down</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="1.10">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Weight Addition</label>
                                                    <div class="form-group " style="padding-top: 8px;">
                                                        <input type="checkbox"  class="switchery" data-color="success" data-size="sm" />
                                                    </div>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Set pr kg Range</label>
                                                    <fieldset style="padding-top: 5px;">
                                                        <div class="input-group input-group-sm">
                                                            <input type="text" class="touchspin-color input-sm" value="0" disabled data-bts-button-down-class="btn btn-success"
                                                                   data-bts-button-up-class="btn btn-success">
                                                        </div>
                                                    </fieldset>
                                                </div>
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
                                            <div>
                                                <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs"><i class="la la-plus"></i></button>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Cash Handling Charges</h3>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group ">
                                                        <input type="checkbox"  class="switchery" data-color="success" data-size="sm" checked/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Up</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0.00">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Down</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="1.10">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2">
                                                    <label class="card-title">Charges</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="150">
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs"><i class="la la-plus"></i></button>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Insurance Charges</h3>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group ">
                                                        <input type="checkbox"  class="switchery" data-color="success" data-size="sm" checked/>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Up</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0.00">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Down</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="1.10">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2">
                                                    <label class="card-title">Charges</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="150">
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs"><i class="la la-plus"></i></button>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Return Charges</h3>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group ">
                                                        <input type="checkbox"  class="switchery" data-color="success" data-size="sm" checked/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">

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
                                                        <input type="checkbox"  class="switchery" data-color="success" data-size="sm" checked/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Small Flyer</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Medium Flyer</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Large Flyer</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Box</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0">
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
                                    <a data-toggle="collapse" href="#sameday" aria-expanded="false"
                                       class="card-title lead success collapsed">Sameday</a> <input type="checkbox" id="" class="switchery" data-color="success" data-size="xs" />
                                </div>
                                <div id="sameday" role="tabpanel" aria-labelledby="headingCollapse64" class="border-success no-border-top card-collapse collapse"
                                     aria-expanded="false" style="height: 0px;">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Weight Charges</h3>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Up</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0.00">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Down</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="1.10">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Weight Addition</label>
                                                    <div class="form-group " style="padding-top: 8px;">
                                                        <input type="checkbox"  class="switchery" data-color="success" data-size="sm" />
                                                    </div>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Set pr kg Range</label>
                                                    <fieldset style="padding-top: 5px;">
                                                        <div class="input-group input-group-sm">
                                                            <input type="text" class="touchspin-color input-sm" value="0" disabled data-bts-button-down-class="btn btn-success"
                                                                   data-bts-button-up-class="btn btn-success">
                                                        </div>
                                                    </fieldset>
                                                </div>
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
                                            <div>
                                                <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs"><i class="la la-plus"></i></button>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Cash Handling Charges</h3>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group ">
                                                        <input type="checkbox"  class="switchery" data-color="success" data-size="sm" checked/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Up</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0.00">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Down</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="1.10">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2">
                                                    <label class="card-title">Charges</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="150">
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs"><i class="la la-plus"></i></button>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Insurance Charges</h3>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group ">
                                                        <input type="checkbox"  class="switchery" data-color="success" data-size="sm" checked/>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Up</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0.00">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <label class="card-title">Range Down</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="1.10">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-2">
                                                    <label class="card-title">Charges</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="150">
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs"><i class="la la-plus"></i></button>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Return Charges</h3>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group ">
                                                        <input type="checkbox"  class="switchery" data-color="success" data-size="sm" checked/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">

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
                                                        <input type="checkbox"  class="switchery" data-color="success" data-size="sm" checked/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Small Flyer</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Medium Flyer</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Large Flyer</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0">
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-3 text-center">
                                                    <label class="card-title">Box</label>
                                                    <fieldset class="form-group">
                                                        <input type="number" class="form-control" id="" value="0">
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


@endsection