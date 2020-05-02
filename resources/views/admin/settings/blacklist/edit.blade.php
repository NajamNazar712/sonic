@extends('admin.layout.master')

@section('title', 'Edit Category')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Edit Category
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <form id="category_form" class="form" method="POST" action="{{ route('admin.settings.blacklist.edit', ['id' => $setting_id]) }}" novalidate="novalidate">
                                {{ csrf_field() }}
                                <input type="hidden" name="setting_id" value="{{$setting_id}}">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
                                        <div class="form-group">
                                            <input type="text" name="name" class="form-control" placeholder="Category Name*" data-rule-required="true" data-msg-required="Category Name is required" value="{{ $blacklist_setting->name }}">
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
                                        <div class="form-group">
                                            <select name="labeling_select" class="select2" id="labeling_select" data-rule-required="true" data-msg-required="Labeling is required">
                                                @foreach($labelings as $labeling)
                                                    <option value="{{ $labeling->id }}">{{ $labeling->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6 col-12">
                                        <div class="form-group text-center">
                                            <label for="color" class="mr-1">Color</label>
                                            <input type="text" name="color" id="color" class="form-control showPaletteOnly " data-rule-required="true" data-msg-required="Color is required" value="{{ $blacklist_setting->color }}">
                                        </div>
                                    </div>

                                </div>
                                <div class="row justify-content-center p-3">
                                    <div class="col"><h3>Conditions:</h3></div>
                                    <div class="col"><button type="button" class="btn btn-black pull-right d-none" id="add_condition"><i class="la la-plus"></i> Add Condition</button></div>
                                </div>
                                <div id="multiple_conditions_div">
                                    @php
                                        $condition_row_id = 1;
                                    @endphp
                                    @foreach($condition_ids as $condition_id)

                                        <div class="border border-primary p-3 mb-2 condition_div_{{$condition_row_id}}">
                                            <div class="row mb-3">
                                                <div class="col-2">Add Condition for</div>
                                                <div class="col-3">
                                                    <div class="form-group">
                                                        <select name="condition_select[{{$condition_row_id}}]" class="select2 form-control condition_select" id="condition_select_{{$condition_row_id}} " data-rule-required="true" data-msg-required="This field is required">
                                                            @foreach($conditions as $condition)
                                                                <option value="{{ $condition->id }}">{{ $condition->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-2 offset-md-5">
                                                    <button type="button" class="btn btn-danger condition_remove" rel="{{$condition_row_id}}" ><i class="la la-trash"></i> Condition</button>
                                                </div>
                                            </div>
                                            <div class="criteria_div">
                                                @php
                                                    $criteria_row_id = 1;
                                                @endphp
                                                @foreach($blacklist_conditions[$condition_id] as $condition)

                                                    <div class="row mb-1">
                                                        <div class="col-2">
                                                            <div class="form-group">
                                                                <select name="logic_select[{{$condition_row_id}}][{{$criteria_row_id}}]" class="select2 form-control logic_select" data-rule-required="true" data-msg-required="This field is required" id="logic_select_{{$condition_row_id}}{{$criteria_row_id}}">
                                                                    @foreach($logics as $logic)
                                                                        <option value="{{ $logic->id }}">{{ $logic->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                        </div>
                                                        <div class="col-2">
                                                            <div class="form-group">
                                                                <input type="text" name="logic_percentage[{{$condition_row_id}}][{{$criteria_row_id}}]" class="form-control dec-percent" placeholder="Percentage or Value" data-rule-required="true" data-msg-required="This field is required" value="{{$condition->blacklist_logic_value}}">
                                                            </div>
                                                        </div>
                                                        <div class="col-2">
                                                            <div class="form-group">
                                                                <select name="shipment_range_select[{{$condition_row_id}}][{{$criteria_row_id}}]" class="select2 form-control shipment_range_select" data-rule-required="true" data-msg-required="This field is required" id="shipment_range_select_{{$condition_row_id}}{{$criteria_row_id}}">
                                                                    @foreach($shipment_ranges as $range)
                                                                        <option value="{{ $range->id }}">{{ $range->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-2">
                                                            <div class="form-group">
                                                                <input type="text" name="shipment_range[{{$condition_row_id}}][{{$criteria_row_id}}]" class="form-control numeric" placeholder="Value" data-rule-required="true" data-msg-required="This field is required" value="{{$condition->blacklist_shipment_range_value}}">
                                                            </div>
                                                        </div>
                                                        <div class="col-2">
                                                            @if($condition->blacklist_operation_id != null)
                                                            <select name="operation_select[{{$condition_row_id}}][{{$criteria_row_id}}]" class="select2 form-control operation_select" id="operation_select_{{$condition_row_id}}{{$criteria_row_id}}">
                                                                @foreach($operations as $operation)
                                                                    <option value="{{ $operation->id }}">{{ $operation->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @endif
                                                        </div>
                                                        <div class="col-2">
{{--                                                            <button type="button" class="btn btn-primary btm-sm add_criteria" id="add_criteria_1"><i class="la la-plus"></i> Add Criteria</button>--}}
                                                        </div>
                                                    </div>
                                                    @php
                                                        $criteria_row_id++;
                                                    @endphp
                                                @endforeach

                                            </div>

                                        </div>
                                        @php
                                            $condition_row_id++;
                                        @endphp
                                    @endforeach
                                </div>
                                <div class="row justify-content-center">
                                    <div class="col-8">
                                        <div class="form-group">
                                            <textarea name="message" id="message" class="form-control" placeholder="Enter warning message here..." cols="30" rows="6" data-rule-required="true" data-msg-required="Warning message is required">{{$blacklist_setting->message}}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group text-center mt-2">
                                        <button type="submit" class="btn btn-primary">Update Category</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    {{--    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">--}}
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/spectrum/spectrum.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    {{--    <script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/spectrum/spectrum.js')}}" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            var conditions_count = '{!! count($conditions) !!}';
            var used_condition_count = '{!! count($condition_ids) !!}';
            conditions_count = parseInt(conditions_count) - parseInt(used_condition_count);
            $('button.condition_remove').on('click', function () {
                var id = parseInt($(this).attr('rel'));
                var div = '.condition_div_'+id;
                console.log(div);
               $(div).remove();
            });
            var colorPalette = [
                ["#000","#333","#666","#999","#bbb","#ddd","#f3f3f3","#fff"],
                ["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],
                ["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],
                ["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],
                ["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],
                ["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],
                ["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],
                ["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]
            ];
            $(".showPaletteOnly").spectrum({
                preferredFormat: "hex3",
                showPaletteOnly: true,
                showPalette:true,
                allowEmpty: true,
                palette: colorPalette
            });
            $('.dec-percent').inputmask("Regex",{
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                regex: '^\\d{1,9}(\\.\\d{1,2})?%?$'
            });
            $('.numeric').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'min': 0,
                'max': 1000000
            });
            $('#labeling_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Labeling*'
            });
            $('#labeling_select').val({!! $blacklist_setting->labeling_id !!}).trigger('change');

            $('.logic_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Logic*'
            });
            $('.shipment_range_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Shipment Range*'
            });
            $('.operation_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Operation'
            });

            $('body').on('change','#message',function() {
                $(this).val($(this).val().trim());
            });


            var condition = 1;


            @foreach($condition_ids as $id)
            $('select[name="condition_select['+ condition +']"]').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Condition*'
            });
            $('select[name="condition_select['+ condition +']"]').val({{$id}}).trigger('change');
            var row = 1;
                @foreach($blacklist_conditions[$id] as $condition)
                    $('#logic_select_'+ condition + row +'').val({!! $condition->blacklist_logic_id !!}).trigger('change');
                    $('#shipment_range_select_'+ condition + row +'').val({!! $condition->blacklist_shipment_range_id !!}).trigger('change');
                    @if($condition->blacklist_operation_id != null)
                        $('#operation_select_'+ condition + row +'').val({!! $condition->blacklist_operation_id !!}).trigger('change');
                    @endif
                    row++;
                @endforeach
            condition++;
            @endforeach



            {{--condition_select.val({!! $condition->blacklist_condition_id !!}).trigger('change');--}}
            $('#category_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Category is being added!',
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