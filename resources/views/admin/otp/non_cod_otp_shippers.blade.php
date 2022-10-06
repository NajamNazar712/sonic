@extends('admin.layout.master')

@section('title', 'Non-Cod OTP Shippers Setting')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Non-Cod OTP Shippers Setting
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.non_cod_otp_shippers.store') }}" novalidate="novalidate">
                                        {{ csrf_field() }}
                                        <div class="col-12 form-group">
                                            <select name="users[]" id="users_select" class="form-control select2" multiple="multiple">
                                                @foreach($shippers as $shipper)
                                                    <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-12 form-group">
                                            <button type="button" class="col-md-2 btn btn-primary" id="select_all">Select All</button>
                                            <button type="button" class="col-md-2 btn btn-primary" id="select_none" disabled>Select None</button>
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
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {

            $('#users_select').select2({
                placeholder:'Select Shippers',
                width:'100%',
                allowClear:true
            });

            @if(count($user_ids) > 0)
                var ids = @json($user_ids);
                $('#users_select').val(ids).trigger('change');
            @endif
            $('#settings_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                }
            });

            $("#select_all").click(function(){
                $("#users_select > option").prop("selected","selected").trigger("change");
                $("#select_none").removeAttr('disabled');
            });

            $("#select_none").click(function(){
                $('#users_select').val('').trigger('change');
                $("#select_none").attr('disabled');
            });
        });
    </script>
@endsection