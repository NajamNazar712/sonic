@extends('admin.layout.master')

@section('title', 'Manage Geocodes Settings (TPL)')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Manage Geocodes Settings (TPL)
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.geo_codes.global_setting.store') }}" novalidate="novalidate">
                                        {{ csrf_field() }}

                                        <div class="col-12 form-group">
                                            <label class="mr-2 font-small-3"><b>Enable Geo Codes: </b></label>
                                            <input type="checkbox" name="geo_codes_enabled" id="geo_codes_enabled" class="switchery geo_codes_enabled" data-size="sm" data-switchery="true" @if($geo_codes_enabled == 1) checked @endif>
                                        </div>
                                        <div class="col-12 form-group">
                                            <label>Maximum API Calls Allowed <span class="text-danger">*</span></label>
                                            <input type="hidden" name="limit_is_changed" id="limit_is_changed" value="0">
                                            <input type="text" name="geo_codes_max_limit" id="geo_codes_max_limit" class="form-control limit_geo_code" placeholder="Maximum API Calls Allowed*" data-rule-required="true" data-msg-required="This field is required" value="{{ $geo_codes_max_limit }}">
                                        </div>
                                        <div class="col-12 form-group">
                                            <label>Destination Hubs ( <input type="checkbox" class="checkAll"> Select All)</label>
                                            <select name="destination_hub_ids[]" id="destination_hub_id" class="form-control select2" multiple="multiple" >
                                                <option value="" disabled>Select</option>
                                                @foreach($destination_hubs as $destination_hub)
                                                    <option value="{{ $destination_hub->id }}" {{ in_array($destination_hub->id, $assigned_destination_hubs)  ? 'selected' : '' }}> {{ $destination_hub->name }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 form-group">
                                            <label>Sub Segments ( <input type="checkbox" class="checkAll"> Select All)</label>
                                            <select name="sub_segment_ids[]" id="sub_segment_id" class="form-control select2" multiple="multiple" >
                                                <option value="" disabled>Select</option>
                                                @foreach($sub_segments as $sub_segment)
                                                    <option value="{{ $sub_segment->id }}" {{ in_array($sub_segment->id, $assigned_sub_segments)  ? 'selected' : '' }}> {{ $sub_segment->name }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if(session('role_id') == 1 || in_array(1058, session('permissions')))
                                            <div class="col-md-12 form-group">
                                                <button type="submit" class="col-md-4 btn btn-primary">Update</button>
                                            </div>
                                        @endif
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
    <style>
        .select2-container--classic .select2-selection--multiple .select2-selection__choice, .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #64a0d2 !important;
            border-color: #5587b4 !important;
            color: #FFFFFF;
        }
        .select2-search.select2-search--inline, .select2-search.select2-search--inline input{
            width: 100% !important;
        }
        .limit_geo_code{
            width: 50%!important;
            margin-left: 24%!important;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {

            $('#destination_hub_id').select2({
                width:'100%',
                placeholder:"Select HUB",
                allowClear:false,
            });
            $('#sub_segment_id').select2({
                width:'100%',
                placeholder:"Select Sub Segment",
                allowClear:false,
            });

            $('#settings_form input.limit_geo_code').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
            });
            $("#settings_form .checkAll").on('click',function (){
                var nextSelect = $(this).closest('.form-group').find('select');
                var options = $(nextSelect).find('option');
                if($(this).is(':checked')) {
                    options.prop("selected", "selected");
                    $(nextSelect).trigger("change");
                }else{
                    options.prop("selected", false);
                    $(nextSelect).trigger("change");
                }
            });
            $('#settings_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    // if($('#all_shipper_toggle').is(':checked')){
                    //     $('#only_users').val(null).trigger('change');
                    // }
                    // else{
                    //     $('#excluded_users').val(null).trigger('change');
                    // }
                    swal({
                        title: 'Please Wait!',
                        text: 'Setting is being updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });
                    form.submit();
                }
            });

            $('#geo_codes_max_limit').on('input change', function () {
                $('#limit_is_changed').val(1);
            });

            // $("#all_shipper_toggle").change(function(){
            //     if($("#all_shipper_toggle").is(':checked') ){
            //         $("#excluded_users_container").removeClass('d-none');
            //         $("#only_users_container").addClass('d-none');
            //     }else{
            //         $("#excluded_users_container").addClass('d-none');
            //         $("#only_users_container").removeClass('d-none');
            //     }
            // });

            {{--var geo_codes_enabled = '{!! $geo_codes_enabled !!}';--}}
            {{--if(geo_codes_enabled == 0){--}}
            {{--    $("#shippers_wrapper").addClass('d-none');--}}
            {{--}--}}

            {{--$("#bypass_setting").change(function(){--}}
            {{--    if($("#bypass_setting").is(':checked') ){--}}
            {{--        $("#shippers_wrapper").removeClass('d-none');--}}
            {{--    }else{--}}
            {{--        $("#shippers_wrapper").addClass('d-none');--}}
            {{--    }--}}
            {{--});--}}

        });
    </script>
@endsection