@extends('admin.layout.master')

@section('title', 'Consignee Refused OTP Bypass Setting')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Consignee Refused OTP Bypass Setting
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.consignee_refused_otp_bypass.store') }}" novalidate="novalidate">
                                        {{ csrf_field() }}

                                        <div class="col-12 form-group">
                                            <label class="mr-2 font-small-3"><b>Bypass Setting: </b></label>
                                            <input type="checkbox" name="bypass_setting" id="bypass_setting" class="switchery bypass_setting" data-size="sm" data-switchery="true" @if($bypass_setting == 1) checked @endif>
                                        </div>

                                        <div class="col-12" id="shippers_wrapper">
                                            <div class="col-12 form-group">
                                                <label class="mr-2 font-small-3"><b>All Shippers: </b></label>
                                                <input type="checkbox" name="all_shipper_toggle" id="all_shipper_toggle" class="switchery all_shipper_toggle" data-size="sm" data-switchery="true" @if(isset($all_shippers) && $all_shippers == 1) checked @endif>
                                            </div>

                                            <div class="col-12 form-group d-none" id="excluded_users_container">
                                                <label class="mr-2 font-small-3"><b>Exclude Shipper(s) </b></label>
                                                <select name="excluded_users[]" id="excluded_users" class="form-control select2" multiple="multiple">
                                                    @foreach($shippers as $shipper)
                                                        <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-12 form-group d-none" id="only_users_container">
                                                <label class="mr-2 font-small-3"><b>Only Shipper(s) </b></label>
                                                <select name="only_users[]" id="only_users" class="form-control select2" data-rule-required="true"  data-msg-required="This Field is required" multiple="multiple">
                                                    @foreach($shippers as $shipper)
                                                        <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                        </div>


                                        <div class="col-md-12 form-group">
                                            <button type="submit" class="col-md-4 btn btn-primary">Update</button>
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
    <style>
        .select2-container--classic .select2-selection--multiple .select2-selection__choice, .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #64a0d2 !important;
            border-color: #5587b4 !important;
            color: #FFFFFF;
        }
        .select2-search.select2-search--inline, .select2-search.select2-search--inline input{
            width: 100% !important;
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


            $('#excluded_users').select2({
                placeholder:'Select Excluded Shippers',
                width:'100%',
                allowClear:true
            });

            $('#only_users').select2({
                placeholder:'Select Only Shippers',
                width:'100%',
                allowClear:true
            });

            @if(isset($all_shippers) && $all_shippers == 1)
                $("#excluded_users_container").removeClass('d-none');
            @else
                $("#only_users_container").removeClass('d-none');
            @endif


            @if(count($excluded_shippers) > 0)
            var ids = @json($excluded_shippers);
            $('#excluded_users').val(ids).trigger('change');
            @endif

            @if(count($only_shippers) > 0)
            var ids = @json($only_shippers);
            $('#only_users').val(ids).trigger('change');
            @endif

            $('#settings_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {

                    if($('#all_shipper_toggle').is(':checked')){
                        $('#only_users').val(null).trigger('change');
                    }
                    else{
                        $('#excluded_users').val(null).trigger('change');
                    }

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
            $("#all_shipper_toggle").change(function(){
                if($("#all_shipper_toggle").is(':checked') ){
                    $("#excluded_users_container").removeClass('d-none');
                    $("#only_users_container").addClass('d-none');
                }else{
                    $("#excluded_users_container").addClass('d-none');
                    $("#only_users_container").removeClass('d-none');
                }
            });

            var bypass_setting = '{!! $bypass_setting !!}';
            if(bypass_setting == 0){
                $("#shippers_wrapper").addClass('d-none');
            }

            $("#bypass_setting").change(function(){
                if($("#bypass_setting").is(':checked') ){
                    $("#shippers_wrapper").removeClass('d-none');
                }else{
                    $("#shippers_wrapper").addClass('d-none');
                }
            });

        });
    </script>
@endsection