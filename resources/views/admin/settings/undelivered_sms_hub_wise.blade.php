@extends('admin.layout.master')

@section('title', 'Undelivered SMS City Wise')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Undelivered SMS City Wise
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.undelivered_sms_hub_wise.submit') }}" novalidate="novalidate">
                                        {{ csrf_field() }}
                                        {{-- <div class="col-12 form-group">
                                            <select name="hub_id" id="hub_id" class="form-control select2" >
                                                @foreach($hubs as $hub)
                                                    <option value="{{$hub->id}}">{{$hub->name}}</option>
                                                @endforeach
                                            </select>
                                        </div> --}}
                                        <div class="form-group">
                                            <select name="city_id[]" id="cities" multiple class="select2 form-control " style="width: 100%" data-rule-required="true" data-msg-required="City is required">
                                                @foreach($cities as $city)
                                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <button type="button" id="selectAll" class="btn btn-success">Select All</button>
                                            <button type="button" id="unselectAll" class="btn btn-danger">Un-Select All</button>
                                        </div>


                                        <button type="submit" class="btn btn-primary">Update</button>
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

            $("#settings_form #selectAll").on('click',function (){
                $("#settings_form #cities > option").prop("selected","selected");
                $("#settings_form #cities").trigger("change");
            });

            $("#settings_form #unselectAll").on('click',function (){
                $("#settings_form #cities > option").prop("selected","");
                $("#settings_form #cities").trigger("change");
            });
            $("#settings_form #cities").select2({
                placeholder: "Select Cities",
                width:'100%',
                dropdownParent: $("#settings_form")
            });
          

            var ids = @json($city_id);
            console.log(ids);
                $('#cities').val(ids).trigger('change');

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