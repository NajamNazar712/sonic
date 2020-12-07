@extends('admin.layout.master')

@section('title', 'International Walk-In')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    International Walk-In
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                    <form id="settings_form" class="form-horizontal" method="POST" action="{{ route('admin.settings.international_walk_in.store') }}" novalidate="novalidate">
                                        {{ csrf_field() }}
                                        @if(count($walk_in_standard_charges) > 0)
                                            @foreach($walk_in_standard_charges as $standard_charges)
                                                <input type="hidden" name="standard_charges[]" value="{{$standard_charges->id}}">
                                                <div class="row justify-content-center">
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <select name="hubs[{{$standard_charges->id}}][]" id="select_box_{{$standard_charges->id}}" class="form-control select2" multiple="multiple" required data-rule-required="true" data-msg-required="Hub(s) is required">
                                                                @foreach($cities as $city)
                                                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div style="width: 450px; float: left; margin-left: 20px;">
                                                    <h4 class="form-section mb-2 text-center" style="text-align: left">Hub to Hub</h4>
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="input-group form-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">Actual Weight</span>
                                                                </div>
                                                                <input type="text" name="hub_actual_weight[{{$standard_charges->id}}]" id="hub_actual_weight_{{$standard_charges->id}}" class="form-control numeric" required data-rule-required="true" data-msg-required="Actual Weight is required" value="{{ $standard_charges->hub_actual_weight }}">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">KG</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <label class="text-bold-600">Charges Per KG</label>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="input-group form-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">Charges</span>
                                                                </div>
                                                                <input type="text" name="hub_chargeable_weight[{{$standard_charges->id}}]" id="hub_chargeable_weight_{{$standard_charges->id}}" class="form-control local" placeholder="" required data-rule-required="true" data-msg-required="Charges Per KG is required" value="{{ $standard_charges->hub_chargeable_weight }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <label class="text-bold-600">Return Charges</label>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="input-group form-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">Charges</span>
                                                                </div>
                                                                <input type="text" name="hub_return_charges[{{$standard_charges->id}}]" id="hub_return_charges_{{$standard_charges->id}}" class="form-control local" placeholder="" required data-rule-required="true" data-msg-required="Return Charges is required" value="{{ $standard_charges->hub_return_charges }}">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">%</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div style="width: 450px; float: right; margin-left: 20px;">
                                                    <h4 class="form-section mb-2 text-center" style="text-align: right">Doorstep</h4>
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="input-group form-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">Actual Weight</span>
                                                                </div>
                                                                <input type="text" name="door_actual_weight[{{$standard_charges->id}}]" id="door_actual_weight_{{$standard_charges->id}}" class="form-control numeric" placeholder="" required data-rule-required="true" data-msg-required="Actual Weight is required" value="{{ $standard_charges->door_actual_weight }}">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">KG</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <label class="text-bold-600">Charges Per KG</label>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="input-group form-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">Charges</span>
                                                                </div>
                                                                <input type="text" name="door_chargeable_weight[{{$standard_charges->id}}]" id="door_chargeable_weight_{{$standard_charges->id}}" class="form-control local" placeholder="" required data-rule-required="true" data-msg-required="Charges Per KG is required" value="{{ $standard_charges->door_chargeable_weight}}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <label class="text-bold-600">Return Charges</label>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="input-group form-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">Charges</span>
                                                                </div>
                                                                <input type="text" name="door_return_charges[{{$standard_charges->id}}]" id="door_return_charges_{{$standard_charges->id}}" class="form-control local" placeholder="" required data-rule-required="true" data-msg-required="Return Charges is required" value="{{ $standard_charges->door_return_charges }}">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">%</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="row justify-content-center">
                                                <input type="hidden" name="standard_charges[]" value="1">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <select name="hubs[1][]" id="select_box_1" class="form-control select2" multiple="multiple" required data-rule-required="true" data-msg-required="Hub(s) is required">
                                                            @foreach($cities as $city)
                                                                <option value="{{$city->id}}">{{$city->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div style="width: 450px; float: left; margin-left: 20px;">
                                                <h4 class="form-section mb-2 text-center" style="text-align: left">Hub to Hub</h4>
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="input-group form-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Actual Weight</span>
                                                            </div>
                                                            <input type="text" name="hub_actual_weight[1]" id="hub_actual_weight_1" class="form-control numeric" required data-rule-required="true" data-msg-required="Actual Weight is required" value="">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">KG</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <label class="text-bold-600">Charges Per KG</label>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="input-group form-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Charges</span>
                                                            </div>
                                                            <input type="text" name="hub_chargeable_weight[1]" id="hub_chargeable_weight_1" class="form-control local" placeholder="" required data-rule-required="true" data-msg-required="Charges Per KG is required" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <label class="text-bold-600">Return Charges</label>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="input-group form-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Charges</span>
                                                            </div>
                                                            <input type="text" name="hub_return_charges[1]" id="hub_return_charges_1" class="form-control local" placeholder="" required data-rule-required="true" data-msg-required="Return Charges is required" value="">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div style="width: 450px; float: right; margin-left: 20px;">
                                                <h4 class="form-section mb-2 text-center" style="text-align: right">Doorstep</h4>
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="input-group form-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Actual Weight</span>
                                                            </div>
                                                            <input type="text" name="door_actual_weight[1]" id="door_actual_weight_1" class="form-control numeric" placeholder="" required data-rule-required="true" data-msg-required="Actual Weight is required" value="">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">KG</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <label class="text-bold-600">Charges Per KG</label>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="input-group form-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Charges</span>
                                                            </div>
                                                            <input type="text" name="door_chargeable_weight[1]" id="door_chargeable_weight_1" class="form-control local" placeholder="" required data-rule-required="true" data-msg-required="Charges Per KG is required" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <label class="text-bold-600">Return Charges</label>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="input-group form-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Charges</span>
                                                            </div>
                                                            <input type="text" name="door_return_charges[1]" id="door_return_charges_1" class="form-control local" placeholder="" required data-rule-required="true" data-msg-required="Return Charges is required" value="">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="input-group justify-content-center">
                                            <button type="submit" class="btn btn-primary" style="width: 200px">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {

            $('.numeric').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false
            });
            $('.local').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false
            });
            $('.national').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false
            });
            @if(count($walk_in_standard_charges) > 0)
                @foreach($walk_in_standard_charges as $standard_charges)
                    $('#select_box_{{$standard_charges->id}}').select2({
                        width:'100%',
                        placeholder:"Select Hub(s)",
                        allowClear:true
                    });

                    var hub_ids = [];
                    @if(count($standard_charges->hubs))
                        @foreach($standard_charges->hubs as $hub)
                            @if($hub->international_charges_id == $standard_charges->id)
                                hub_ids.push({{$hub->hub_id}});
                            @endif
                        @endforeach
                    @endif
                    $('#select_box_{{$standard_charges->id}}').val(hub_ids).trigger('change');
                @endforeach
            @else
                $('#select_box_1').select2({
                    width:'100%',
                    placeholder:"Select Hub(s)",
                    allowClear:true
                });
            @endif
            $('#settings_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                }
            });
        });
    </script>
@endsection