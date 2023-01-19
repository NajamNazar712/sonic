@extends('admin.layout.master')

@section('title', 'One Link Charges')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    One Link Charges
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.short_received_hub_wise_cron.store') }}" novalidate="novalidate">
                                    {{ csrf_field() }}
                                    {{-- <div class="row justify-content-center">
                                        <div class="form-group">
                                            <input type="text" name="default_time" class="form-control time" placeholder="Default Time*" data-rule-required="true" data-msg-required="Default Time is required" value="{{ $default_time }}" data-rule-min="0" data-msg-min="Default Time can not be less than 0" data-rule-max="23" data-msg-max="Default Time can not be more than 23">
                                        </div>
                                    </div> --}}
                                    <div class="cities_div">
                                        <div class="row justify-content-center">
                                        <div class="col">
                                            <div class="form-group">
                                                <label>Range Up</label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label>Range Down</label>

                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label>Charges</label>

                                            </div>
                                        </div>
                                    </div>
                                        @if(count($existing_cities))
                                            @foreach($existing_cities as $index => $ext_city)
                                                <div class="row justify_content_center mb-1">
                                                    
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="text" name="range_up_[{{$index}}]" id="range_up_{{$index}}" class="form-control range_up" value="{{$ext_city->time}}" placeholder="Value" data-rule-required="true" data-msg-required="This field is required">
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="text" name="range_down_[{{$index}}]" id="range_down_{{$index}}" class="form-control range_down" value="{{$ext_city->time}}" placeholder="Value" data-rule-required="true" data-msg-required="This field is required">
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="text" name="charges_[{{$index}}]" id="charges_{{$index}}" class="form-control charges" value="{{$ext_city->time}}" placeholder="Value" data-rule-required="true" data-msg-required="This field is required">
                                                        </div>
                                                    </div>

                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="col mb-1">
                                        <button type="button" class="btn btn-outline-success btm-sm add_row" id="add_row"><i class="la la-plus"></i></button>
                                    </div>

                                    <div class="col">
                                        <button type="submit" class="btn btn-primary width-250">Update</button>
                                    </div>
                                </form>
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
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('#settings_form input.time').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#settings_form input.range_up').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#settings_form input.range_down').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#settings_form input.charges').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            var row = 1;
            var cities =[];
            var existing_cities = @json($existing_cities);
            if(existing_cities.length > 0){
                row = existing_cities.length;
                @foreach($existing_cities as $city)
                   var hub_id = @json($city->hub_id);
                   cities.push(hub_id);
                @endforeach
            }
            $('body').on('click', '.add_row', function () {
                var old_row = row -1;
                flag = true;
                if(old_row != 0){
                    var old_city = $('#cities_select_' + old_row).val();
                    var old_time = $('#time_' + old_row).val();
                    var old_range_up = $('#range_up_' + old_row).val();
                    var old_range_down = $('#range_down_' + old_row).val();
                    var old_charges = $('#charges_' + old_row).val();
                    if((old_city == null || old_city == '') || ((old_time == null || old_time == ''))){
                        flag = false;
                        if(old_city == null || old_city == ''){
                            var error = "Please select City first!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        if(old_time == null || old_time == ''){
                            var error = "Please enter time first!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        if(old_range_up == null || old_range_up == ''){
                            var error = "Please enter Range_up first!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        if(old_range_down == null || old_range_down == ''){
                            var error = "Please enter Range_down first!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        if(old_charges == null || old_charges == ''){
                            var error = "Please enter Range_down first!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    }
                }

                if(flag == true){
                    if(old_row != 0) {
                        $('#cities_select_' + old_row).prop('disabled', true);
                    }
                    var html = '<div class="row justify_content_center mb-1">\n' +
                        '                                        <div class="col">\n' +
                        '                                            <div class="form-group">\n' +
                        '                                                <select name="cities['+ row +']" id="cities_select_'+ row +'" class="select2 form-control cities_select" data-rule-required="true" data-msg-required="This field is required">\n' +
                        '                                                    @foreach($cities as $city)\n' +
                        '                                                        <option value="{{ $city->id }}">{{ $city->name }}</option>\n' +
                        '                                                    @endforeach\n' +
                        '                                                </select>\n' +
                        '                                            </div>\n' +
                        '\n' +
                        '                                        </div>\n' +
                        '                                        <div class="col">\n' +
                        '                                            <div class="form-group">\n' +
                        '                                            <input type="text" name="time['+ row +']" id="time_'+ row +'" class="form-control time" placeholder="Value" data-rule-required="true" data-msg-required="This field is required">\n' +
                        '                                            </div>\n' +
                        '                                        </div>\n' +
                        '</div>\n' +
                        '                                    </div>';
                    $('div.cities_div').append(html);

                    $('#cities_select_'+ row).prepend('<option value="" selected="selected"></option>').select2({
                        width: '100%',
                        placeholder: 'Select City*'
                    }).bind('change', function () {
                        var id = parseInt($(this).val());

                        var index = $.inArray(id, cities);

                        if (index === -1) {
                            cities.push(id);
                        }
                        else{
                            $(this).val('').change();
                            var error = "Same City already selected!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    });
                    $('#settings_form input.time').inputmask({
                        'alias': 'integer',
                        'allowMinus': false,
                        'allowPlus': false
                    });
                    $('#settings_form input.range_up').inputmask({
                        'alias': 'integer',
                        'allowMinus': false,
                        'allowPlus': false
                    });
                    $('#settings_form input.range_down').inputmask({
                        'alias': 'integer',
                        'allowMinus': false,
                        'allowPlus': false
                    });
                    $('#settings_form input.charges').inputmask({
                        'alias': 'integer',
                        'allowMinus': false,
                        'allowPlus': false
                    });
                    row++;
                }

            });

            $('#settings_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    swal({
                        title: 'Please Wait!',
                        text: 'Settings are being updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });
                    for(i = 0; i < row; i++){
                        $('#cities_select_' + i).prop('disabled', false);
                    }

                    form.submit();
                }
            });
        });
    </script>
@endsection