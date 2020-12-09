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
                                        <div class="parent_div">
                                                @if(count($walk_in_standard_charges) > 0)
                                                    @foreach($walk_in_standard_charges as $standard_charges)
                                                        <div class="col parent_div_{{$standard_charges->id}}" id="parent_div_{{$standard_charges->id}}">
                                                            <div class="input-group form-section mb-2" id="section_{{$standard_charges->id}}">
                                                                <div class="col display-inline">
                                                                    <div class="float-left">
                                                                        <h3><b>International Walk-In Rates {{$standard_charges->id}}</b></h3>
                                                                    </div>
                                                                    @if($standard_charges->id != 1)
                                                                        <div class="float-right">
                                                                            <span class="btn btn-sm btn-danger rate_div_close" id="rate_div_close_{{$standard_charges->id}}" box="{{$standard_charges->id}}">
                                                                                <i class="ft-trash"></i>
                                                                            </span>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <input type="hidden" name="standard_charges[]"
                                                                   value="{{$standard_charges->id}}">
                                                            <div class="row justify-content-center">
                                                                <div class="col-6">
                                                                    <div class="form-group">
                                                                        <select name="hubs[{{$standard_charges->id}}][]"
                                                                                id="select_box_{{$standard_charges->id}}"
                                                                                class="form-control select2"
                                                                                multiple="multiple" required
                                                                                data-rule-required="true"
                                                                                data-msg-required="Hub(s) is required">
                                                                            @foreach($cities as $city)
                                                                                <option value="{{$city->id}}">{{$city->name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div style="width: 450px; float: left; margin-left: 20px;">
                                                                <h4 class="form-section mb-2 text-center"
                                                                    style="text-align: left">Hub to Hub</h4>
                                                                <div class="row">
                                                                    <div class="col">
                                                                        <div class="input-group form-group">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text">Actual Weight</span>
                                                                            </div>
                                                                            <input type="text"
                                                                                   name="hub_actual_weight[{{$standard_charges->id}}]"
                                                                                   id="hub_actual_weight_{{$standard_charges->id}}"
                                                                                   class="form-control numeric"
                                                                                   required
                                                                                   data-rule-required="true"
                                                                                   data-msg-required="Actual Weight is required"
                                                                                   value="{{ $standard_charges->hub_actual_weight }}">
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
                                                                            <input type="text"
                                                                                   name="hub_chargeable_weight[{{$standard_charges->id}}]"
                                                                                   id="hub_chargeable_weight_{{$standard_charges->id}}"
                                                                                   class="form-control local"
                                                                                   placeholder="" required
                                                                                   data-rule-required="true"
                                                                                   data-msg-required="Charges Per KG is required"
                                                                                   value="{{ $standard_charges->hub_chargeable_weight }}">
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
                                                                            <input type="text"
                                                                                   name="hub_return_charges[{{$standard_charges->id}}]"
                                                                                   id="hub_return_charges_{{$standard_charges->id}}"
                                                                                   class="form-control local"
                                                                                   placeholder="" required
                                                                                   data-rule-required="true"
                                                                                   data-msg-required="Return Charges is required"
                                                                                   value="{{ $standard_charges->hub_return_charges }}">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">%</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div style="width: 450px; float: right; margin-left: 20px;">
                                                                <h4 class="form-section mb-2 text-center"
                                                                    style="text-align: right">Doorstep</h4>
                                                                <div class="row">
                                                                    <div class="col">
                                                                        <div class="input-group form-group">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text">Actual Weight</span>
                                                                            </div>
                                                                            <input type="text"
                                                                                   name="door_actual_weight[{{$standard_charges->id}}]"
                                                                                   id="door_actual_weight_{{$standard_charges->id}}"
                                                                                   class="form-control numeric"
                                                                                   placeholder="" required
                                                                                   data-rule-required="true"
                                                                                   data-msg-required="Actual Weight is required"
                                                                                   value="{{ $standard_charges->door_actual_weight }}">
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
                                                                            <input type="text"
                                                                                   name="door_chargeable_weight[{{$standard_charges->id}}]"
                                                                                   id="door_chargeable_weight_{{$standard_charges->id}}"
                                                                                   class="form-control local"
                                                                                   placeholder="" required
                                                                                   data-rule-required="true"
                                                                                   data-msg-required="Charges Per KG is required"
                                                                                   value="{{ $standard_charges->door_chargeable_weight}}">
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
                                                                            <input type="text"
                                                                                   name="door_return_charges[{{$standard_charges->id}}]"
                                                                                   id="door_return_charges_{{$standard_charges->id}}"
                                                                                   class="form-control local"
                                                                                   placeholder="" required
                                                                                   data-rule-required="true"
                                                                                   data-msg-required="Return Charges is required"
                                                                                   value="{{ $standard_charges->door_return_charges }}">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">%</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="col parent_box_div_1" id="parent_box_div_1">
                                                        <div class="input-group form-section mb-2" id="section_1">
                                                            <div class="col display-inline">
                                                                <div class="float-left">
                                                                    <h3><b>International Walk-In Rates 1</b></h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row justify-content-center">
                                                            <input type="hidden" name="standard_charges[]"
                                                                   value="1">
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <select name="hubs[1][]" id="select_box_1"
                                                                            class="form-control select2"
                                                                            multiple="multiple"
                                                                            required data-rule-required="true"
                                                                            data-msg-required="Hub(s) is required">
                                                                        @foreach($cities as $city)
                                                                            <option value="{{$city->id}}">{{$city->name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div style="width: 450px; float: left; margin-left: 20px;">
                                                            <h4 class="form-section mb-2 text-center"
                                                                style="text-align: left">Hub to Hub</h4>
                                                            <div class="row">
                                                                <div class="col">
                                                                    <div class="input-group form-group">
                                                                        <div class="input-group-prepend">
                                                                            <span class="input-group-text">Actual Weight</span>
                                                                        </div>
                                                                        <input type="text"
                                                                               name="hub_actual_weight[1]"
                                                                               id="hub_actual_weight_1"
                                                                               class="form-control numeric" required
                                                                               data-rule-required="true"
                                                                               data-msg-required="Actual Weight is required"
                                                                               value="">
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
                                                                        <input type="text"
                                                                               name="hub_chargeable_weight[1]"
                                                                               id="hub_chargeable_weight_1"
                                                                               class="form-control local"
                                                                               placeholder=""
                                                                               required data-rule-required="true"
                                                                               data-msg-required="Charges Per KG is required"
                                                                               value="">
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
                                                                        <input type="text"
                                                                               name="hub_return_charges[1]"
                                                                               id="hub_return_charges_1"
                                                                               class="form-control local"
                                                                               placeholder=""
                                                                               required data-rule-required="true"
                                                                               data-msg-required="Return Charges is required"
                                                                               value="">
                                                                        <div class="input-group-append">
                                                                            <span class="input-group-text">%</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div style="width: 450px; float: right; margin-left: 20px;">
                                                            <h4 class="form-section mb-2 text-center"
                                                                style="text-align: right">Doorstep</h4>
                                                            <div class="row">
                                                                <div class="col">
                                                                    <div class="input-group form-group">
                                                                        <div class="input-group-prepend">
                                                                            <span class="input-group-text">Actual Weight</span>
                                                                        </div>
                                                                        <input type="text"
                                                                               name="door_actual_weight[1]"
                                                                               id="door_actual_weight_1"
                                                                               class="form-control numeric"
                                                                               placeholder=""
                                                                               required data-rule-required="true"
                                                                               data-msg-required="Actual Weight is required"
                                                                               value="">
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
                                                                        <input type="text"
                                                                               name="door_chargeable_weight[1]"
                                                                               id="door_chargeable_weight_1"
                                                                               class="form-control local"
                                                                               placeholder=""
                                                                               required data-rule-required="true"
                                                                               data-msg-required="Charges Per KG is required"
                                                                               value="">
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
                                                                        <input type="text"
                                                                               name="door_return_charges[1]"
                                                                               id="door_return_charges_1"
                                                                               class="form-control local"
                                                                               placeholder=""
                                                                               required data-rule-required="true"
                                                                               data-msg-required="Return Charges is required"
                                                                               value="">
                                                                        <div class="input-group-append">
                                                                            <span class="input-group-text">%</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                        </div>
                                        <div class="input-group justify-content-center mt-2">
                                            <button id="add_more_rates_hubs" type="button" class="btn btn-outline-success round btn-min-width mr-1 mb-1">Add Rates and Hub</button>
                                        </div>
                                        <div class="input-group justify-content-center mt-2">
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
            div_count = 1;
            @if(count($walk_in_standard_charges) > 0)
                div_count = 0;
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

                    @if($standard_charges->id > 1)
                        var old_box_no = {{$standard_charges->id}} - 1;
                        $('#rate_div_close_'+ old_box_no).parent().remove();
                    @endif
                div_count++;
                @endforeach
            @else
                div_count = 1;
                $('#select_box_' + div_count).select2({
                    width:'100%',
                    placeholder:"Select Hub(s)",
                    allowClear:true
                });
            @endif
            $('#add_more_rates_hubs').on('click', function () {
                div_count++;
                var div = '<div class="col parent_div_'+ div_count +'" id="parent_div_'+ div_count +'">' +
                    '         <div class="input-group form-section mb-2" id="section_'+ div_count +'">\n' +
                    '              <div class="col display-inline">' +
                    '                   <div class="float-left">' +
                    '                        <h3><b>International Walk-In Rates ' + div_count + '</b></h3>' +
                    '                   </div>' +
                    '                   <div class="float-right">' +
                    '                       <span class="btn btn-sm btn-danger rate_div_close" id="rate_div_close_' + div_count + '" box="' + div_count + '">' +
                    '                            <i class="ft-trash"></i>' +
                    '                       </span>' +
                    '                   </div>' +
                    '             </div>' +
                    '        </div>' +
                    '        <div class="row justify-content-center">'+
                    '            <input type="hidden" name="standard_charges[]" value="'+ div_count +'">'+
                    '            <div class="col-6">'+
                    '                <div class="form-group">'+
                    '                    <select name="hubs['+ div_count +'][]" id="select_box_'+ div_count +'" class="form-control select2" multiple="multiple" required'+
                    '                            data-rule-required="true" data-msg-required="Hub(s) is required">'+
                    '                        @foreach($cities as $city)'+
                    '                        <option value="{{$city->id}}">{{$city->name}}</option>'+
                    '                        @endforeach'+
                    '                    </select>'+
                    '                </div>'+
                    '            </div>'+
                    '        </div>'+
                    '        <div style="width: 450px; float: left; margin-left: 20px;">'+
                    '            <h4 class="form-section mb-2 text-center" style="text-align: left">Hub to Hub</h4>'+
                    '            <div class="row">'+
                    '                <div class="col">'+
                    '                    <div class="input-group form-group">'+
                    '                        <div class="input-group-prepend">'+
                    '                            <span class="input-group-text">Actual Weight</span>'+
                    '                        </div>'+
                    '                        <input type="text" name="hub_actual_weight['+ div_count +']" id="hub_actual_weight_'+ div_count +'"'+
                    '                               class="form-control numeric" required data-rule-required="true"'+
                    '                               data-msg-required="Actual Weight is required" value="">'+
                    '                        <div class="input-group-append">'+
                    '                            <span class="input-group-text">KG</span>'+
                    '                        </div>'+
                    '                    </div>'+
                    '                </div>'+
                    '            </div>'+
                    '            <label class="text-bold-600">Charges Per KG</label>'+
                    '            <div class="row">'+
                    '                <div class="col-12">'+
                    '                    <div class="input-group form-group">'+
                    '                        <div class="input-group-prepend">'+
                    '                            <span class="input-group-text">Charges</span>'+
                    '                        </div>'+
                    '                        <input type="text" name="hub_chargeable_weight['+ div_count +']" id="hub_chargeable_weight_'+ div_count +'"'+
                    '                               class="form-control local" placeholder="" required data-rule-required="true"'+
                    '                               data-msg-required="Charges Per KG is required" value="">'+
                    '                    </div>'+
                    '                </div>'+
                    '            </div>'+
                    '            <label class="text-bold-600">Return Charges</label>'+
                    '            <div class="row">'+
                    '                <div class="col-12">'+
                    '                    <div class="input-group form-group">'+
                    '                        <div class="input-group-prepend">'+
                    '                            <span class="input-group-text">Charges</span>'+
                    '                        </div>'+
                    '                        <input type="text" name="hub_return_charges['+ div_count +']" id="hub_return_charges_'+ div_count +'"'+
                    '                               class="form-control local" placeholder="" required data-rule-required="true"'+
                    '                               data-msg-required="Return Charges is required" value="">'+
                    '                        <div class="input-group-append">'+
                    '                            <span class="input-group-text">%</span>'+
                    '                        </div>'+
                    '                    </div>'+
                    '                </div>'+
                    '            </div>'+
                    '        </div>'+
                    '        <div style="width: 450px; float: right; margin-left: 20px;">'+
                    '            <h4 class="form-section mb-2 text-center" style="text-align: right">Doorstep</h4>'+
                    '            <div class="row">'+
                    '                <div class="col">'+
                    '                    <div class="input-group form-group">'+
                    '                        <div class="input-group-prepend">'+
                    '                            <span class="input-group-text">Actual Weight</span>'+
                    '                        </div>'+
                    '                        <input type="text" name="door_actual_weight['+ div_count +']" id="door_actual_weight_'+ div_count +'"'+
                    '                               class="form-control numeric" placeholder="" required data-rule-required="true"'+
                    '                               data-msg-required="Actual Weight is required" value="">'+
                    '                        <div class="input-group-append">'+
                    '                            <span class="input-group-text">KG</span>'+
                    '                        </div>'+
                    '                    </div>'+
                    '                </div>'+
                    '            </div>'+
                    '            <label class="text-bold-600">Charges Per KG</label>'+
                    '            <div class="row">'+
                    '                <div class="col-12">'+
                    '                    <div class="input-group form-group">'+
                    '                        <div class="input-group-prepend">'+
                    '                            <span class="input-group-text">Charges</span>'+
                    '                        </div>'+
                    '                        <input type="text" name="door_chargeable_weight['+ div_count +']" id="door_chargeable_weight_'+ div_count +'"'+
                    '                               class="form-control local" placeholder="" required data-rule-required="true"'+
                    '                               data-msg-required="Charges Per KG is required" value="">'+
                    '                    </div>'+
                    '                </div>'+
                    '            </div>'+
                    '            <label class="text-bold-600">Return Charges</label>'+
                    '            <div class="row">'+
                    '                <div class="col-12">'+
                    '                    <div class="input-group form-group">'+
                    '                        <div class="input-group-prepend">'+
                    '                            <span class="input-group-text">Charges</span>'+
                    '                        </div>'+
                    '                        <input type="text" name="door_return_charges['+ div_count +']" id="door_return_charges_'+ div_count +'"'+
                    '                               class="form-control local" placeholder="" required data-rule-required="true"'+
                    '                               data-msg-required="Return Charges is required" value="">'+
                    '                        <div class="input-group-append">'+
                    '                            <span class="input-group-text">%</span>'+
                    '                        </div>'+
                    '                    </div>'+
                    '                </div>'+
                    '            </div>'+
                    '        </div>'+
                    '    </div>';
                $('.parent_div').append(div);

                $('#select_box_' + div_count).select2({
                    width:'100%',
                    placeholder:"Select Hub(s)",
                    allowClear:true
                });
                var old_box_no = div_count - 1;
                $('#rate_div_close_'+ old_box_no).parent().remove();
            });

            $('body').on('click', 'span.rate_div_close', function(){
                var old_div = $(this).attr('box');
                $('.parent_div_'+old_div).remove();
            });

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