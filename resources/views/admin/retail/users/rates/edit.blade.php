@extends('admin.layout.master')

@section('title', 'Retail Standard Rates')

@section('content')
    <h1>Retail Standard Rates</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('admin.inc.messages')
                    <div class="card-header">
                        <h2 class="font-large-1">
                            <div class="badge badge-success pull-right">Retail Rates</div>
                        </h2>
                    </div>
                    <div class="card-content">
                        <form id="ratesAdditionForm" class="card-body card-dashboard" action="{{route('admin.retail.standard.rates.update')}}" method="post" novalidate="novalidate">
                            @csrf
                            <div class="card">

                                {{--saver plus start --}}
                                <div id="" class="card-header border-success">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h3 class="display-inline card-title lead success">Saver +</h3>

                                        </div>
                                    </div>
                                </div>
                                <div id="saver_plus" class="card border-success hide" aria-expanded="true">
                                    <div class="card-content">
                                        <div class="card-body">

                                            <div class="weight-addition-saver-plus">
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
                                                        <label class="card-title">Zone A</label>
                                                    </div>
                                                    <div class="col text-center">
                                                        <label class="card-title">Zone B</label>
                                                    </div>
                                                    <div class="col text-center">
                                                        <label class="card-title">Zone C</label>
                                                    </div>
                                                    <div class="col text-center">
                                                        <label class="card-title">Zone D</label>
                                                    </div>
                                                    <div class="col-1"></div>
                                                </div>

                                                @if(isset($saver_plus))
                                                    @foreach($saver_plus as $index => $sp)
                                                        <div class="row saver_plus_row" id="saver_plus_row{{$index}}">
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" id="saver_plus_range_up{{$index}}" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" name="saver_plus_range_up[{{$index}}]" value="{{$sp->range_up}}">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">

                                                                <fieldset class="form-group">
                                                                    <input type="text" id="saver_plus_range_down{{$index}}" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" name="saver_plus_range_down[{{$index}}]"  value="{{$sp->range_down}}">
                                                                </fieldset>
                                                            </div>
                                                            {{--<div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required"  name="saver_plus_kg_range[{{$index}}]"  value="{{$sp->kg_range}}">
                                                                </fieldset>
                                                            </div>--}}
                                                            <div class="col text-center">
                                                                <div class="form-group " style="padding-top: 8px;">
                                                                    <input type="checkbox" id="SaverPlusSwitch{{$index}}" class="switchery weightAdditionSaverPlus{{$index}}" data-color="success" @if($sp->weight_addition == 1) checked @endif data-size="sm" name="saver_plus_switch[{{$index}}]" >
                                                                </div>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset style="padding-top: 5px;">
                                                                    <div class="input-group input-group-sm form-group">
                                                                        <input type="text" class="touchspin-color input-sm spkg" data-bts-button-down-class="btn btn-success"
                                                                               data-bts-button-up-class="btn btn-success" @if($sp->weight_addition == 0) disabled @endif  name="saver_plus_kg_range[{{$index}}]" value="{{$sp->kg_range}}">
                                                                    </div>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required"  name="saver_plus_zone_a[{{$index}}]"  value="{{$sp->zone_a}}">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required"  name="saver_plus_zone_b[{{$index}}]"  value="{{$sp->zone_b}}">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required"  name="saver_plus_zone_c[{{$index}}]"  value="{{$sp->zone_c}}">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required"  name="saver_plus_zone_d[{{$index}}]"  value="{{$sp->zone_d}}">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-1">
                                                                @if($index>0)
                                                                    <span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 sp_weight_close"><i class="ft-x"></i></span>
                                                                @endif
                                                            </div>

                                                        </div>
                                                    @endforeach

                                                @endif
                                            </div>
                                            <div class="mb-2">
                                                <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="waddition_btn"><i class="la la-plus"></i></button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                {{--saver plus end --}}

                                {{--rush start --}}
                                <div id="" class="card-header border-success mt-2">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h3 class="display-inline card-title lead success">Rush</h3>
                                        </div>
                                        <div class="col-md-6">
                                            {{--  <a href="javascript:void(0);" class="pull-right" id="rush_main_switch"><input name="rush_main_switch" type="checkbox"  class="switchery rush-main-switch" data-size="sm" /></a>--}}
                                        </div>
                                    </div>
                                </div>
                                <div id="rush" class="card border-success hide" aria-expanded="true">
                                    <div class="card-content">
                                        <div class="card-body">

                                            <div class="weight-addition-rush">
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
                                                        <label class="card-title">Within City</label>
                                                    </div>
                                                    <div class="col text-center">
                                                        <label class="card-title">Same Zone</label>
                                                    </div>
                                                    <div class="col text-center">
                                                        <label class="card-title">Different Zone</label>
                                                    </div>
                                                    <div class="col-1"></div>
                                                </div>

                                                @if(isset($rush_data))
                                                    @foreach($rush_data as $index => $rush)
                                                        <div class="row rush_row" id="rush_row{{$index}}">
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" id="rush_range_up{{$index}}" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" name="rush_range_up[{{$index}}]" value="{{$rush->range_up}}">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">

                                                                <fieldset class="form-group">
                                                                    <input type="text" id="rush_range_down{{$index}}" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" name="rush_range_down[{{$index}}]"  value="{{$rush->range_down}}">
                                                                </fieldset>
                                                            </div>
                                                            {{--                                                    <div class="col text-center">--}}
                                                            {{--                                                        <fieldset class="form-group">--}}
                                                            {{--                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required"  name="rush_kg_range[{{$index}}]"  value="{{$rush->kg_range}}">--}}
                                                            {{--                                                        </fieldset>--}}
                                                            {{--                                                    </div>--}}
                                                            <div class="col text-center">
                                                                <div class="form-group " style="padding-top: 8px;">
                                                                    <input type="checkbox" id="RushSwitchSwitch{{$index}}" class="switchery weightAdditionRush" data-color="success" data-size="sm" name="rush_switch[{{$index}}]" @if($rush->weight_addition == 1) checked @endif >
                                                                </div>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset style="padding-top: 5px;">

                                                                    <div class="input-group input-group-sm form-group">
                                                                        <input type="text" class="touchspin-color input-sm spkg" data-bts-button-down-class="btn btn-success"
                                                                               data-bts-button-up-class="btn btn-success" @if($rush->weight_addition == 0) disabled @endif  name="rush_kg_range[{{$index}}]" value="{{$rush->weight_addition}}">
                                                                    </div>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required"  name="rush_wc[{{$index}}]"  value="{{$rush->within_city}}">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required"  name="rush_sz[{{$index}}]"  value="{{$rush->same_zone}}">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required"  name="rush_dz[{{$index}}]"  value="{{$rush->different_zone}}">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-1">
                                                                @if($index>0)
                                                                    <span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 rush_weight_close"><i class="ft-x"></i></span>
                                                                @endif
                                                            </div>

                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="row rush_row" id="rush_row">
                                                        <div class="col text-center">
                                                            <fieldset class="form-group">
                                                                <input type="text" id="rush_range_up[0]" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" name="rush_range_up[0]" value="0">
                                                            </fieldset>
                                                        </div>
                                                        <div class="col text-center">

                                                            <fieldset class="form-group">
                                                                <input type="text" id="rush_range_down[0]" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" name="rush_range_down[0]"  value="0">
                                                            </fieldset>
                                                        </div>
                                                        <div class="col text-center">
                                                            <fieldset class="form-group">
                                                                <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required"  name="rush_kg_range[0]"  value="0">
                                                            </fieldset>
                                                        </div>
                                                        <div class="col text-center">
                                                            <fieldset class="form-group">
                                                                <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required"  name="rush_wc[0]"  value="0">
                                                            </fieldset>
                                                        </div>
                                                        <div class="col text-center">
                                                            <fieldset class="form-group">
                                                                <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required"  name="rush_sz[0]"  value="0">
                                                            </fieldset>
                                                        </div>
                                                        <div class="col text-center">
                                                            <fieldset class="form-group">
                                                                <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required"  name="rush_dz[0]"  value="0">
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-1">
                                                            @if($index>0)
                                                                <span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 rush_weight_close"><i class="ft-x"></i></span>
                                                            @endif
                                                        </div>

                                                    </div>
                                                @endif
                                            </div>
                                            <div class="mb-2">
                                                <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="rush_waddition_btn"><i class="la la-plus"></i></button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                {{--rush end --}}

                                {{--cod start --}}
                                <div id="" class="card-header border-success mt-2">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h3 class="display-inline card-title lead success">COD</h3>
                                        </div>
                                        <div class="col-md-6">
                                            {{-- <a href="javascript:void(0);" class="pull-right" id="cod_main_switch"><input name="cod_main_switch" type="checkbox"  class="switchery cod-main-switch" data-size="sm" /></a>--}}
                                        </div>
                                    </div>
                                </div>
                                <div id="cod" class="card border-success hide" aria-expanded="true">
                                    <div class="card-content">
                                        <div class="card-body">

                                            <div class="weight-addition-cod">
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
                                                        <label class="card-title">Within City</label>
                                                    </div>
                                                    <div class="col text-center">
                                                        <label class="card-title">Same Zone</label>
                                                    </div>
                                                    <div class="col text-center">
                                                        <label class="card-title">Different Zone</label>
                                                    </div>
                                                    <div class="col-1"></div>
                                                </div>

                                                @if(isset($cod_data))
                                                    @foreach($cod_data as $index => $cod)
                                                        <div class="row cod_row" id="cod_row{{$index}}">
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" id="cod_range_up{{$index}}" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" name="cod_range_up[{{$index}}]" value="{{$cod->range_up}}">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">

                                                                <fieldset class="form-group">
                                                                    <input type="text" id="cod_range_down{{$index}}" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" name="cod_range_down[{{$index}}]"  value="{{$cod->range_down}}">
                                                                </fieldset>
                                                            </div>
                                                            {{-- <div class="col text-center">
                                                                 <fieldset class="form-group">
                                                                     <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required"  name="cod_kg_range[{{$index}}]"  value="{{$cod->kg_range}}">
                                                                 </fieldset>
                                                             </div>--}}
                                                            <div class="col text-center">
                                                                <div class="form-group " style="padding-top: 8px;">
                                                                    <input type="checkbox" id="CodSwitch{{$index}}" class="switchery weightAdditionCod" data-color="success" data-size="sm" name="cod_switch[{{$index}}]" @if($cod->weight_addition == 1) checked @endif>
                                                                </div>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset style="padding-top: 5px;">
                                                                    <div class="input-group input-group-sm form-group">
                                                                        <input type="text" class="touchspin-color input-sm spkg" data-bts-button-down-class="btn btn-success"
                                                                               data-bts-button-up-class="btn btn-success" @if($cod->weight_addition == 0) disabled @endif  name="cod_kg_range[{{$index}}]" value="{{$cod->kg_range}}">
                                                                    </div>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required"  name="cod_wc[{{$index}}]"  value="{{$cod->within_city}}">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required"  name="cod_sz[{{$index}}]"  value="{{$cod->same_zone}}">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required"  name="cod_dz[{{$index}}]"  value="{{$cod->different_zone}}">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-1">
                                                                @if($index>0)
                                                                    <span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 cod_weight_close"><i class="ft-x"></i></span>
                                                                @endif
                                                            </div>

                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="row cod_row" id="cod_row">
                                                        <div class="col text-center">
                                                            <fieldset class="form-group">
                                                                <input type="text" id="cod_range_up[0]" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" name="cod_range_up[0]" value="0">
                                                            </fieldset>
                                                        </div>
                                                        <div class="col text-center">

                                                            <fieldset class="form-group">
                                                                <input type="text" id="cod_range_down[0]" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" name="cod_range_down[0]"  value="[0]">
                                                            </fieldset>
                                                        </div>
                                                        <div class="col text-center">
                                                            <fieldset class="form-group">
                                                                <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required"  name="cod_kg_range[0]"  value="0">
                                                            </fieldset>
                                                        </div>
                                                        <div class="col text-center">
                                                            <fieldset class="form-group">
                                                                <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required"  name="cod_wc[0]"  value="0">
                                                            </fieldset>
                                                        </div>
                                                        <div class="col text-center">
                                                            <fieldset class="form-group">
                                                                <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required"  name="cod_sz[0]"  value="0">
                                                            </fieldset>
                                                        </div>
                                                        <div class="col text-center">
                                                            <fieldset class="form-group">
                                                                <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required"  name="cod_dz[0]"  value="0">
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-1">
                                                            @if($index>0)
                                                                <span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 cod_weight_close"><i class="ft-x"></i></span>
                                                            @endif
                                                        </div>
                                                        @endif

                                                    </div>
                                                    <div class="mb-2">
                                                        <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="cod_waddition_btn"><i class="la la-plus"></i></button>
                                                    </div>
                                            </div>

                                        </div>
                                    </div>
                                    {{--cod end --}}

                                    {{--swift start --}}
                                    <div id="" class="card-header border-success mt-2">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h3 class="display-inline card-title lead success">Swift</h3>
                                            </div>
                                            <div class="col-md-6">
                                                {{-- <a href="javascript:void(0);" class="pull-right" id="swift_main_switch"><input name="swift_main_switch" type="checkbox"  class="switchery swift-main-switch" data-size="sm" /></a>--}}
                                            </div>
                                        </div>
                                    </div>
                                    <div id="swift" class="card border-success hide" aria-expanded="true">
                                        <div class="card-content">
                                            <div class="card-body">

                                                <div class="weight-addition-swift">
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
                                                            <label class="card-title">Within City</label>
                                                        </div>
                                                        <div class="col text-center">
                                                            <label class="card-title">Same Zone</label>
                                                        </div>
                                                        <div class="col text-center">
                                                            <label class="card-title">Different Zone</label>
                                                        </div>
                                                        <div class="col-1"></div>
                                                    </div>

                                                    @if(isset($swift))
                                                        @foreach($swift as $index => $sw)
                                                            <div class="row swift_row" id="swift_row{{$index}}">
                                                                <div class="col text-center">
                                                                    <fieldset class="form-group">
                                                                        <input type="text" id="swift_range_up{{$index}}" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" name="swift_range_up[{{$index}}]" value="{{$sw->range_up}}">
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col text-center">

                                                                    <fieldset class="form-group">
                                                                        <input type="text" id="swift_range_down{{$index}}" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" name="swift_range_down[{{$index}}]"  value="{{$sw->range_down}}">
                                                                    </fieldset>
                                                                </div>
                                                                {{-- <div class="col text-center">
                                                                     <fieldset class="form-group">
                                                                         <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required"  name="swift_kg_range[{{$index}}]"  value="{{$sw->kg_range}}">
                                                                     </fieldset>
                                                                 </div>--}}
                                                                <div class="col text-center">

                                                                    <div class="form-group " style="padding-top: 8px;">
                                                                        <input type="checkbox" id="SwiftSwitch{{$index}}" class="switchery weightAdditionSwift" data-color="success" data-size="sm" name="swift_switch[{{$index}}]" @if($sw->weight_addition == 1) checked @endif>
                                                                    </div>
                                                                </div>
                                                                <div class="col text-center">
                                                                    <fieldset style="padding-top: 5px;">

                                                                        <div class="input-group input-group-sm form-group">
                                                                            <input type="text" class="touchspin-color input-sm spkg" data-bts-button-down-class="btn btn-success"
                                                                                   data-bts-button-up-class="btn btn-success" @if($sw->weight_addition == 0) disabled @endif  name="swift_kg_range[{{$index}}]" value="{{$sw->kg_range}}">
                                                                        </div>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col text-center">
                                                                    <fieldset class="form-group">
                                                                        <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required"  name="swift_wc[{{$index}}]"  value="{{$sw->within_city}}">
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col text-center">
                                                                    <fieldset class="form-group">
                                                                        <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required"  name="swift_sz[{{$index}}]"  value="{{$sw->same_zone}}">
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col text-center">
                                                                    <fieldset class="form-group">
                                                                        <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required"  name="swift_dz[{{$index}}]"  value="{{$sw->different_zone}}">
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-1">
                                                                    @if($index>0)
                                                                        <span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 swift_weight_close"><i class="ft-x"></i></span>
                                                                    @endif
                                                                </div>

                                                            </div>

                                                        @endforeach
                                                    @else
                                                        <div class="row swift_row" id="swift_row">
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" id="swift_range_up[0]" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" name="swift_range_up[0]" value="0">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">

                                                                <fieldset class="form-group">
                                                                    <input type="text" id="swift_range_down[0]" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" name="swift_range_down[0]"  value="0">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required"  name="swift_kg_range[0]"  value="0">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required"  name="swift_wc[0]"  value="0">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required"  name="swift_sz[0]"  value="0">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required"  name="swift_dz[0]"  value="0">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-1">
                                                                @if($index>0)
                                                                    <span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 swift_weight_close"><i class="ft-x"></i></span>
                                                                @endif
                                                            </div>

                                                        </div>
                                                    @endif

                                                </div>
                                                <div class="mb-2">
                                                    <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="swift_waddition_btn"><i class="la la-plus"></i></button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    {{--swift end --}}

                                    {{--flyers start --}}
                                    <div id="" class="card-header border-success mt-2">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h3 class="display-inline card-title lead success">Flyers</h3>

                                            </div>
                                        </div>
                                    </div>
                                    <div id="flyer" class="card border-success hide" aria-expanded="true">
                                        <div class="card-content">
                                            <div class="card-body">

                                                <div class="weight-addition-flyer">
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
                                                            <label class="card-title">Within City</label>
                                                        </div>
                                                        <div class="col text-center">
                                                            <label class="card-title">Same Zone</label>
                                                        </div>
                                                        <div class="col text-center">
                                                            <label class="card-title">Different Zone</label>
                                                        </div>
                                                        <div class="col-1"></div>
                                                    </div>

                                                    @if(isset($flyers))
                                                        @foreach($flyers as $index => $flyer)


                                                            <div class="row flyer_row" id="flyer_row{{$index}}">
                                                                <div class="col text-center">
                                                                    <fieldset class="form-group">
                                                                        <input type="text" id="flyer_range_up{{$index}}" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" name="flyer_range_up[{{$index}}]" value="{{$flyer->range_up}}">
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col text-center">

                                                                    <fieldset class="form-group">
                                                                        <input type="text" id="flyer_range_down{{$index}}" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" name="flyer_range_down[{{$index}}]"  value="{{$flyer->range_down}}">
                                                                    </fieldset>
                                                                </div>
                                                                {{-- <div class="col text-center">
                                                                     <fieldset class="form-group">
                                                                         <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required"  name="flyer_kg_range[{{$index}}]"  value="{{$flyer->kg_range}}">
                                                                     </fieldset>
                                                                 </div>--}}
                                                                <div class="col text-center">
                                                                    <div class="form-group " style="padding-top: 8px;">
                                                                        <input type="checkbox" id="FlyerSwitch{{$index}}" class="switchery weightAdditionFlyer" data-color="success" data-size="sm" name="flyer_switch[{{$index}}]" @if($flyer->weight_addition == 1) checked @endif >
                                                                    </div>
                                                                </div>
                                                                <div class="col text-center">
                                                                    <fieldset style="padding-top: 5px;">
                                                                        <div class="input-group input-group-sm form-group">
                                                                            <input type="text" class="touchspin-color input-sm spkg" data-bts-button-down-class="btn btn-success"
                                                                                   data-bts-button-up-class="btn btn-success" @if($flyer->weight_addition == 0) disabled @endif  name="flyer_kg_range[{{$index}}]" value="{{$flyer->kg_range}}">
                                                                        </div>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col text-center">
                                                                    <fieldset class="form-group">
                                                                        <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required"  name="flyer_wc[{{$index}}]"  value="{{$flyer->within_city}}">
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col text-center">
                                                                    <fieldset class="form-group">
                                                                        <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required"  name="flyer_sz[{{$index}}]"  value="{{$flyer->same_zone}}">
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col text-center">
                                                                    <fieldset class="form-group">
                                                                        <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required"  name="flyer_dz[{{$index}}]"  value="{{$flyer->different_zone}}">
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-1">
                                                                    @if($index>0)
                                                                        <span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 flyer_weight_close"><i class="ft-x"></i></span>
                                                                    @endif
                                                                </div>

                                                            </div>

                                                        @endforeach
                                                    @else
                                                        <div class="row flyer_row" id="flyer_row[0]">
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" id="flyer_range_up[0]" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" name="flyer_range_up[0]" value="0">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">

                                                                <fieldset class="form-group">
                                                                    <input type="text" id="flyer_range_down[0]" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" name="flyer_range_down[0]"  value="0">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required"  name="flyer_kg_range[0]"  value="0">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required"  name="flyer_wc[0]"  value="0">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required"  name="flyer_sz[0]"  value="0">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required"  name="flyer_dz[0]"  value="0">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-1">
                                                                @if($index>0)
                                                                    <span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 flyer_weight_close"><i class="ft-x"></i></span>
                                                                @endif
                                                            </div>

                                                        </div>
                                                    @endif

                                                </div>
                                                <div class="mb-2">
                                                    <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="flyer_waddition_btn"><i class="la la-plus"></i></button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    {{--flyers end --}}

                                    {{--hard docs start --}}
                                    <div id="" class="card-header border-success mt-2">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h3 class="display-inline card-title lead success">Hard Docs</h3>
                                            </div>
                                            <div class="col-md-6">
                                                {{--  <a href="javascript:void(0);" class="pull-right" id="hdocs_main_switch"><input name="hdcos_main_switch" type="checkbox"  class="switchery hdocs-main-switch" data-size="sm" /></a>--}}
                                            </div>
                                        </div>
                                    </div>
                                    <div id="hdocs" class="card border-success hide" aria-expanded="true">
                                        <div class="card-content">
                                            <div class="card-body">

                                                <div class="weight-addition-hdocs">
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
                                                            <label class="card-title">Within City</label>
                                                        </div>
                                                        <div class="col text-center">
                                                            <label class="card-title">Same Zone</label>
                                                        </div>
                                                        <div class="col text-center">
                                                            <label class="card-title">Different Zone</label>
                                                        </div>
                                                        <div class="col-1"></div>
                                                    </div>

                                                    @if(isset($hard_docs))
                                                        @foreach($hard_docs as $index => $hd)

                                                            <div class="row hdocs_row" id="hdocs_row{{$index}}">
                                                                <div class="col text-center">
                                                                    <fieldset class="form-group">
                                                                        <input type="text" id="hdocs_range_up{{$index}}" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" name="hdocs_range_up[{{$index}}]" value="{{$hd->range_up}}">
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col text-center">

                                                                    <fieldset class="form-group">
                                                                        <input type="text" id="hdocs_range_down{{$index}}" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" name="hdocs_range_down[{{$index}}]"  value="{{$hd->range_down}}">
                                                                    </fieldset>
                                                                </div>
                                                                {{-- <div class="col text-center">
                                                                     <fieldset class="form-group">
                                                                         <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required"  name="hdocs_kg_range[{{$index}}]"  value="{{$hd->kg_range}}">
                                                                     </fieldset>
                                                                 </div>--}}
                                                                <div class="col text-center">
                                                                    <div class="form-group " style="padding-top: 8px;">
                                                                        <input type="checkbox" id="HdocsSwitch{{$index}}" class="switchery weightAdditionHdocs" data-color="success" data-size="sm" name="hdocs_switch[{{$index}}]"  @if($hd->weight_addition == 1) checked @endif>
                                                                    </div>
                                                                </div>
                                                                <div class="col text-center">
                                                                    <fieldset style="padding-top: 5px;">
                                                                        <div class="input-group input-group-sm form-group">
                                                                            <input type="text" class="touchspin-color input-sm spkg" data-bts-button-down-class="btn btn-success"
                                                                                   data-bts-button-up-class="btn btn-success" @if($hd->weight_addition == 0) disabled @endif  name="hdocs_kg_range[{{$index}}]" value="{{$hd->kg_range}}">
                                                                        </div>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col text-center">
                                                                    <fieldset class="form-group">
                                                                        <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required"  name="hdocs_wc[{{$index}}]"  value="{{$hd->within_city}}">
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col text-center">
                                                                    <fieldset class="form-group">
                                                                        <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required"  name="hdocs_sz[{{$index}}]"  value="{{$hd->same_zone}}">
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col text-center">
                                                                    <fieldset class="form-group">
                                                                        <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required"  name="hdocs_dz[{{$index}}]"  value="{{$hd->different_zone}}">
                                                                    </fieldset>
                                                                </div>
                                                                <div class="col-1">
                                                                    @if($index>0)
                                                                        <span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 hdocs_weight_close"><i class="ft-x"></i></span>
                                                                    @endif
                                                                </div>

                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="row hdocs_row" id="hdocs_row[0]">
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" id="hdocs_range_up[0]" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" name="hdocs_range_up[0]" value="0">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">

                                                                <fieldset class="form-group">
                                                                    <input type="text" id="hdocs_range_down[0]" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" name="hdocs_range_down[0]"  value="0">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required"  name="hdocs_kg_range[0]"  value="0">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required"  name="hdocs_wc[0]"  value="0">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required"  name="hdocs_sz[0]"  value="0">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col text-center">
                                                                <fieldset class="form-group">
                                                                    <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required"  name="hdocs_dz[0]"  value="0">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-1">
                                                                @if($index>0)
                                                                    <span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 hdocs_weight_close"><i class="ft-x"></i></span>
                                                                @endif
                                                            </div>

                                                        </div>
                                                    @endif

                                                </div>
                                                <div class="mb-2">
                                                    <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="hdocs_waddition_btn"><i class="la la-plus"></i></button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    {{--hard docs end --}}

                                    {{--trax boxes start --}}
                                    <div id="" class="card-header border-success mt-2">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h3 class="display-inline card-title lead success">Trax Boxes</h3>
                                            </div>
                                            <div class="col-md-6">
                                                {{-- <a href="javascript:void(0);" class="pull-right" id="trax_box_main_switch"><input name="trax_box_main_switch" type="checkbox"  class="switchery trax-box-main-switch" data-size="sm" /></a>--}}
                                            </div>
                                        </div>
                                    </div>
                                    <div id="trax_boxes" class="card border-success hide" aria-expanded="true">
                                        <div class="card-content">
                                            <div class="card-body">
                                                @if(isset($trax_box))
                                                    @foreach($trax_box as $index => $data)
                                                        <div id="" class="card-header border-success mt-2">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <h3 class="display-inline card-title lead success">For {{$data->weight}}</h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="trax_box_{{$data->weight}}" class="card border-success hide" aria-expanded="true">
                                                            <div class="card-content">
                                                                <div class="card-body">
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
                                                                            <label class="card-title">Within City</label>
                                                                        </div>
                                                                        <div class="col text-center">
                                                                            <label class="card-title">Same Zone</label>
                                                                        </div>
                                                                        <div class="col text-center">
                                                                            <label class="card-title">Different Zone</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row trax_box_{{$data->weight}}_fields" id="">
                                                                        <div class="col text-center">
                                                                            <fieldset class="form-group">
                                                                                <input type="text" id="trax_box_{{$data->weight}}_range_up" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" name="trax_box_{{$data->weight}}_range_up" value="{{$data->range_up}}">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col text-center">

                                                                            <fieldset class="form-group">
                                                                                <input type="text" id="trax_box_{{$data->weight}}_range_down" class="form-control decimal weight_range" data-rule-required="true" data-msg-required="This field is required" name="trax_box_{{$data->weight}}_range_down"  value="{{$data->range_down}}">
                                                                            </fieldset>
                                                                        </div>

                                                                        {{-- <input type="hidden" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required"  name="trax_box_{{$data->kg_range}}_weight"  value="{{$data->kg_range}}">--}}
                                                                        <div class="col text-center">
                                                                            <div class="form-group " style="padding-top: 8px;">
                                                                                <input type="checkbox" id="TraxBox{{$data->weight}}Switch" class="switchery weightAdditionTraxBoxTwo" data-color="success" data-size="sm" name="trax_box_{{$data->weight}}_switch" @if($data->weight_addition == 1) checked @endif>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col text-center">
                                                                            <fieldset style="padding-top: 5px;">
                                                                                <div class="input-group input-group-sm form-group">
                                                                                    <input type="text" class="touchspin-color input-sm spkg" data-bts-button-down-class="btn btn-success"
                                                                                           data-bts-button-up-class="btn btn-success" name="trax_box_{{$data->weight}}_weight" value="{{$data->kg_range}}" @if($data->weight_addition == 0) disabled @endif >
                                                                                </div>
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col text-center">
                                                                            <fieldset class="form-group">
                                                                                <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required"  name="trax_box_{{$data->weight}}_wc"  value="{{$data->within_city}}">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col text-center">
                                                                            <fieldset class="form-group">
                                                                                <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required"  name="trax_box_{{$data->weight}}_sz"  value="{{$data->same_zone}}">
                                                                            </fieldset>
                                                                        </div>
                                                                        <div class="col text-center">
                                                                            <fieldset class="form-group">
                                                                                <input type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required"  name="trax_box_{{$data->weight}}_dz"  value="{{$data->different_zone}}">
                                                                            </fieldset>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    {{--trax boxes end --}}
                                </div>

                                <div class="text-center mt-2">
                                    <div class="form-group">

                                        <button id="addRatesSubmit" type="submit" class="btn btn-outline-success round btn-min-width mr-1 mb-1">Update</button>
                                    </div>
                                </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

    </section>


@endsection


@section('js')
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            var on_door_count = $('.saver_plus_row').length;
            for (let i = 0; i < on_door_count; i++) {
                $('#SaverPlusSwitch'+i+'').bind('change', function () {
                    if ($('#SaverPlusSwitch'+i+'').is(":checked")) {
                        $('input[name="saver_plus_kg_range['+i+']"]').prop('disabled', false);
                    } else {
                        $('input[name="saver_plus_kg_range['+i+']"]').prop('disabled', true);
                    }
                });
            }


            var rush_count = $('.rush_row').length;
            for (let i = 0; i < rush_count; i++) {
                $('#RushSwitchSwitch'+i+'').bind('change', function () {
                    if ($('#RushSwitchSwitch'+i+'').is(":checked")) {
                        $('input[name="rush_kg_range['+i+']"]').prop('disabled', false);
                    } else {
                        $('input[name="rush_kg_range['+i+']"]').prop('disabled', true);
                    }
                });
            }


            var cod_count = $('.cod_row').length;
            for (let i = 0; i < cod_count; i++) {
                $('#CodSwitch'+i+'').bind('change', function () {
                    if ($('#CodSwitch'+i+'').is(":checked")) {
                        $('input[name="cod_kg_range['+i+']"]').prop('disabled', false);
                    } else {
                        $('input[name="cod_kg_range['+i+']"]').prop('disabled', true);
                    }
                });
            }

            var swift_count = $('.swift_row').length;
            for (let i = 0; i < swift_count; i++) {
                $('#SwiftSwitch'+i+'').bind('change', function () {
                    if ($('#SwiftSwitch'+i+'').is(":checked")) {
                        $('input[name="swift_kg_range['+i+']"]').prop('disabled', false);
                    } else {
                        $('input[name="swift_kg_range['+i+']"]').prop('disabled', true);
                    }
                });
            }

            var flyer_count = $('.flyer_row').length;
            for (let i = 0; i < flyer_count; i++) {
                $('#FlyerSwitch'+i+'').bind('change', function () {
                    if ($('#FlyerSwitch'+i+'').is(":checked")) {
                        $('input[name="flyer_kg_range['+i+']"]').prop('disabled', false);
                    } else {
                        $('input[name="flyer_kg_range['+i+']"]').prop('disabled', true);
                    }
                });
            }

            var hd_count = $('.hdocs_row').length;
            for (let i = 0; i < hd_count; i++) {
                $('#HdocsSwitch'+i+'').bind('change', function () {
                    if ($('#HdocsSwitch'+i+'').is(":checked")) {
                        $('input[name="hdocs_kg_range['+i+']"]').prop('disabled', false);
                    } else {
                        $('input[name="hdocs_kg_range['+i+']"]').prop('disabled', true);
                    }
                });
            }

            $('#TraxBox2kgSwitch').bind('change',function(){
                if($('#TraxBox2kgSwitch').is(":checked")){
                    $('input[name="trax_box_2kg_weight"]').prop('disabled', false);
                }
                else{
                    $('input[name="trax_box_2kg_weight"]').prop('disabled', true);
                }
            });
            $('#TraxBox5kgSwitch').bind('change',function(){
                if($('#TraxBox5kgSwitch').is(":checked")){
                    $('input[name="trax_box_5kg_weight"]').prop('disabled', false);
                }
                else{
                    $('input[name="trax_box_5kg_weight"]').prop('disabled', true);
                }
            });
            $('#TraxBox10kgSwitch').bind('change',function(){
                if($('#TraxBox10kgSwitch').is(":checked")){
                    $('input[name="trax_box_10kg_weight"]').prop('disabled', false);
                }
                else{
                    $('input[name="trax_box_10kg_weight"]').prop('disabled', true);
                }
            });
            $('#TraxBox15kgSwitch').bind('change',function(){
                if($('#TraxBox15kgSwitch').is(":checked")){
                    $('input[name="trax_box_15kg_weight"]').prop('disabled', false);
                }
                else{
                    $('input[name="trax_box_15kg_weight"]').prop('disabled', true);
                }
            });
            $('#TraxBox20kgSwitch').bind('change',function(){
                if($('#TraxBox20kgSwitch').is(":checked")){
                    $('input[name="trax_box_20kg_weight"]').prop('disabled', false);
                }
                else{
                    $('input[name="trax_box_20kg_weight"]').prop('disabled', true);
                }
            });
            $('#TraxBox30kgSwitch').bind('change',function(){
                if($('#TraxBox30kgSwitch').is(":checked")){
                    $('input[name="trax_box_30kg_weight"]').prop('disabled', false);
                }
                else{
                    $('input[name="trax_box_30kg_weight"]').prop('disabled', true);
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

            //saver plus start
            $('body').on('click', '.sp_weight_close', function () {
                $(this).parent().parent().remove();
            });
            var on_door_count = $('.saver_plus_row').length;
            $('body').on('click', '#waddition_btn', function () {
                var row_count = on_door_count - 1;
                var saver_plus_range_down = parseFloat($('#saver_plus_range_down' + row_count).val());
                var on_door_new_range_down = saver_plus_range_down + 0.01;
                let htmdiv = '<div class="row" id="saver_plus_row' + on_door_count + '"><div class="col text-center"><fieldset class="form-group"><input type="text" id="saver_plus_range_up' + on_door_count + '" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="saver_plus_range_up[' + on_door_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" id="saver_plus_range_down' + on_door_count + '" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="saver_plus_range_down[' + on_door_count + ']"></fieldset></div> <div class="col text-center">\n' +
                    '                  <div class="form-group " style="padding-top: 8px;">\n' +
                    '                   <input type="checkbox" id="SaverPlusSwitch[' + on_door_count + ']" class="switchery weightAdditionSaverPlus' + on_door_count + '" data-color="success" data-size="sm" name="saver_plus_switch[' + on_door_count + ']">\n' +
                    '                </div>\n' +
                    '       </div><div class="col text-center">\n' +
                    '                                                        <fieldset style="padding-top: 5px;">\n' +
                    '                                                            <div class="input-group input-group-sm form-group">\n' +
                    '                                                                <input type="text" class="touchspin-color input-sm spkg" data-toggle="tooltip" data-trigger="hover" data-placement="top"  data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="saver_plus_kg_range[' + on_door_count + ']" disabled>\n' +
                    '                                                            </div>\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required"  name="saver_plus_zone_a[' + on_door_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="saver_plus_zone_b[' + on_door_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="saver_plus_zone_c[' + on_door_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="saver_plus_zone_d[' + on_door_count + ']"></fieldset></div><div class="col-1">\n' +
                    '<span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 sp_weight_close"><i class="ft-x"></i></span></div></div>';
                $('.weight-addition-saver-plus').append(htmdiv);
                var switches = document.querySelector('.switchery.weightAdditionSaverPlus' + on_door_count);
                var switchery = new Switchery(switches, {disabled: false, color: '#37BC9B', size: 'small'});
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
                $("#saver_plus_row" + on_door_count + " .validated").each(function () {
                    $(this).rules("add", {
                        required: true,
                    });

                });
                on_door_count++;
            });

            //saver plus end

            //rush start
            $('body').on('click', '.rush_weight_close', function () {
                $(this).parent().parent().remove();
            });

            var rush_count = $('.rush_row').length;
            $('body').on('click', '#rush_waddition_btn', function () {
                var row_count = rush_count - 1;
                var rush_range_down = parseFloat($('#rush_range_down' + row_count).val());
                var on_door_new_range_down = rush_range_down + 0.01;
                let htmdiv = '<div class="row" id="rush_row' + rush_count + '"><div class="col text-center"><fieldset class="form-group"><input type="text" id="rush_range_up' + rush_count + '" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="rush_range_up[' + rush_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" id="rush_range_down' + rush_count + '" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="rush_range_down[' + rush_count + ']"></fieldset></div><div class="col text-center">\n' +
                    '                  <div class="form-group " style="padding-top: 8px;">\n' +
                    '                   <input type="checkbox" id="RushSwitch[' + rush_count + ']" class="switchery weightAdditionRush' + rush_count + '" data-color="success" data-size="sm" name="rush_switch[' + rush_count + ']">\n' +
                    '                </div>\n' +
                    '       </div><div class="col text-center">\n' +
                    '                                                        <fieldset style="padding-top: 5px;">\n' +
                    '                                                            <div class="input-group input-group-sm form-group">\n' +
                    '                                                                <input type="text" class="touchspin-color input-sm spkg" data-toggle="tooltip" data-trigger="hover" data-placement="top"  data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="rush_kg_range[' + rush_count + ']" disabled>\n' +
                    '                                                            </div>\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required"  name="rush_wc[' + rush_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="rush_sz[' + rush_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="rush_dz[' + rush_count + ']"></fieldset></div><div class="col-1">\n' +
                    '<span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 rush_weight_close"><i class="ft-x"></i></span></div></div>';
                $('.weight-addition-rush').append(htmdiv);
                var switches = document.querySelector('.switchery.weightAdditionRush' + rush_count);
                var switchery = new Switchery(switches, {disabled: false, color: '#37BC9B', size: 'small'});
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
                $("#rush_row" + rush_count + " .validated").each(function () {
                    $(this).rules("add", {
                        required: true,
                    });

                });
                rush_count++;
            });
            //rush end


            //cod start
            $('body').on('click', '.cod_weight_close', function () {
                $(this).parent().parent().remove();
            });

            var cod_count = $('.cod_row').length;
            $('body').on('click', '#cod_waddition_btn', function () {
                var row_count = cod_count - 1;
                var cod_range_down = parseFloat($('#cod_range_down' + row_count).val());
                var cod_new_range_down = cod_range_down + 0.01;
                let htmdiv = '<div class="row" id="cod_row' + cod_count + '"><div class="col text-center"><fieldset class="form-group"><input type="text" id="cod_range_up' + cod_count + '" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="cod_range_up[' + cod_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" id="cod_range_down' + cod_count + '" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="cod_range_down[' + cod_count + ']"></fieldset></div><div class="col text-center">\n' +
                    '                  <div class="form-group " style="padding-top: 8px;">\n' +
                    '                   <input type="checkbox" id="RushSwitch[' + cod_count + ']" class="switchery weightAdditionCod' + cod_count + '" data-color="success" data-size="sm" name="cod_switch[' + cod_count + ']">\n' +
                    '                </div>\n' +
                    '       </div><div class="col text-center">\n' +
                    '                                                        <fieldset style="padding-top: 5px;">\n' +
                    '                                                            <div class="input-group input-group-sm form-group">\n' +
                    '                                                                <input type="text" class="touchspin-color  input-sm spkg" data-toggle="tooltip" data-trigger="hover" data-placement="top"  data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="cod_kg_range[' + cod_count + ']" disabled>\n' +
                    '                                                            </div>\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required"  name="cod_wc[' + cod_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="cod_sz[' + cod_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="cod_dz[' + cod_count + ']"></fieldset></div><div class="col-1">\n' +
                    '<span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 cod_weight_close"><i class="ft-x"></i></span></div></div>';
                $('.weight-addition-cod').append(htmdiv);
                var switches = document.querySelector('.switchery.weightAdditionCod' + cod_count);
                var switchery = new Switchery(switches, {disabled: false, color: '#37BC9B', size: 'small'});
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

                $("#rush_row" + rush_count + " .validated").each(function () {
                    $(this).rules("add", {
                        required: true,
                    });

                });
                cod_count++;
            });
            //cod end

            //swift start
            $('body').on('click', '.swift_weight_close', function () {
                $(this).parent().parent().remove();
            });

            var swift_count = $('.swift_row').length;
            $('body').on('click', '#swift_waddition_btn', function () {
                var row_count = swift_count - 1;
                var swift_range_down = parseFloat($('#swift_range_down' + row_count).val());
                var swift_new_range_down = swift_range_down + 0.01;
                let htmdiv = '<div class="row" id="swift_row' + swift_count + '"><div class="col text-center"><fieldset class="form-group"><input type="text" id="swift_range_up' + swift_count + '" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="swift_range_up[' + swift_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" id="swift_range_down' + swift_count + '" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="swift_range_down[' + swift_count + ']"></fieldset></div><div class="col text-center">\n' +
                    '                  <div class="form-group " style="padding-top: 8px;">\n' +
                    '                   <input type="checkbox" id="SwiftSwitch[' + swift_count + ']" class="switchery weightAdditionSwift' + swift_count + '" data-color="success" data-size="sm" name="swift_switch[' + swift_count + ']">\n' +
                    '                </div>\n' +
                    '       </div><div class="col text-center">\n' +
                    '                                                        <fieldset style="padding-top: 5px;">\n' +
                    '                                                            <div class="input-group input-group-sm form-group">\n' +
                    '                                                                <input type="text" class="touchspin-color  input-sm spkg" data-toggle="tooltip" data-trigger="hover" data-placement="top"  data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="swift_kg_range[' + swift_count + ']" disabled>\n' +
                    '                                                            </div>\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required"  name="swift_wc[' + swift_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="swift_sz[' + swift_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="swift_dz[' + swift_count + ']"></fieldset></div><div class="col-1">\n' +
                    '<span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 swift_weight_close"><i class="ft-x"></i></span></div></div>';
                $('.weight-addition-swift').append(htmdiv);
                var switches = document.querySelector('.switchery.weightAdditionSwift' + swift_count);
                var switchery = new Switchery(switches, {disabled: false, color: '#37BC9B', size: 'small'});
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
                $("#swift_row" + swift_count + " .validated").each(function () {
                    $(this).rules("add", {
                        required: true,
                    });

                });
                swift_count++;
            });
            //swift end


            //flyer start
            $('body').on('click', '.flyer_weight_close', function () {
                $(this).parent().parent().remove();
            });

            var flyer_count = $('.flyer_row').length;
            $('body').on('click', '#flyer_waddition_btn', function () {
                var row_count = flyer_count - 1;
                var flyer_range_down = parseFloat($('#flyer_range_down' + row_count).val());
                var flyer_new_range_down = flyer_range_down + 0.01;
                let htmdiv = '<div class="row" id="flyer_row' + flyer_count + '"><div class="col text-center"><fieldset class="form-group"><input type="text" id="flyer_range_up' + flyer_count + '" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="flyer_range_up[' + flyer_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" id="flyer_range_down' + flyer_count + '" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="flyer_range_down[' + flyer_count + ']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;">\n' +
                    '                   <input type="checkbox" id="FlyerSwitch[' + flyer_count + ']" class="switchery weightAdditionFlyer' + flyer_count + '" data-color="success" data-size="sm" name="flyer_switch[' + flyer_count + ']">\n' +
                    '                </div>\n' +
                    '       </div><div class="col text-center">\n' +
                    '                                                        <fieldset style="padding-top: 5px;">\n' +
                    '                                                            <div class="input-group input-group-sm form-group">\n' +
                    '                                                                <input type="text" class="touchspin-color  input-sm spkg validated" data-toggle="tooltip" data-trigger="hover" data-placement="top"  data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="flyer_kg_range[' + flyer_count + ']" disabled>\n' +
                    '                                                            </div>\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required"  name="flyer_wc[' + flyer_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="flyer_sz[' + flyer_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="flyer_dz[' + flyer_count + ']"></fieldset></div><div class="col-1">\n' +
                    '<span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 swift_weight_close"><i class="ft-x"></i></span></div></div>';
                $('.weight-addition-flyer').append(htmdiv);
                var switches = document.querySelector('.switchery.weightAdditionFlyer' + flyer_count);
                var switchery = new Switchery(switches, {disabled: false, color: '#37BC9B', size: 'small'});
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

                $("#flyer_row" + flyer_count + " .validated").each(function () {
                    $(this).rules("add", {
                        required: true,
                    });

                });
                flyer_count++;
            });
            //flyer end

            //hdocs start
            $('body').on('click', '.hdocs_weight_close', function () {
                $(this).parent().parent().remove();
            });

            var doc_count = $('.hdocs_row').length;
            $('body').on('click', '#hdocs_waddition_btn', function () {
                var row_count = doc_count - 1;
                var hdocs_range_down = parseFloat($('#hdocs_range_down' + row_count).val());
                var hdocs_new_range_down = hdocs_range_down + 0.01;
                let htmdiv = '<div class="row" id="hdocs_row' + doc_count + '"><div class="col text-center"><fieldset class="form-group"><input type="text" id="hdocs_range_up' + doc_count + '" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="hdocs_range_up[' + doc_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" id="hdocs_range_down' + doc_count + '" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="hdocs_range_down[' + doc_count + ']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;">\n' +
                    '                   <input type="checkbox" id="SwiftSwitch[' + doc_count + ']" class="switchery weightAdditionHdocs' + doc_count + '" data-color="success" data-size="sm" name="swift_switch[' + doc_count + ']">\n' +
                    '                </div>\n' +
                    '       </div><div class="col text-center">\n' +
                    '                                                        <fieldset style="padding-top: 5px;">\n' +
                    '                                                            <div class="input-group input-group-sm form-group">\n' +
                    '                                                                <input type="text" class="touchspin-color  input-sm spkg validated" data-toggle="tooltip" data-trigger="hover" data-placement="top"  data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="swift_kg_range[' + doc_count + ']" disabled>\n' +
                    '                                                            </div>\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required"  name="hdocs_wc[' + doc_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="hdocs_sz[' + doc_count + ']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control dec-percent validated" data-rule-required="true" data-msg-required="This field is required"  name="hdocs_dz[' + doc_count + ']"></fieldset></div><div class="col-1">\n' +
                    '<span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 hdocs_weight_close"><i class="ft-x"></i></span></div></div>';
                $('.weight-addition-hdocs').append(htmdiv);
                var switches = document.querySelector('.switchery.weightAdditionHdocs' + doc_count);
                var switchery = new Switchery(switches, {disabled: false, color: '#37BC9B', size: 'small'});
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
                $("#hdocs_row" + doc_count + " .validated").each(function () {
                    $(this).rules("add", {
                        required: true,
                    });

                });
                doc_count++;
            });
            //hdocs end

            $('#ratesAdditionForm').on('keypress', function (e) {
                if (e.which == 13 || e.keyCode == 13) {
                    e.preventDefault();
                }
            });

            $("#ratesAdditionForm").validate({
                errorClass: "danger",
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {

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
            });

        });

    </script>
@endsection

