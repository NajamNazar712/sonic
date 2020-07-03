@extends('admin.layout.master')

@section('title', 'Crm Default Agent')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Crm Default Agent
                </h1>

                <div class="card height-300">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-5">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.default_agent.store') }}" novalidate="novalidate">
                                        {{ csrf_field() }}

                                        <div class="form-group">
                                            <select name="sale_person" id="assign_agent" class="form-control select2" data-rule-required="true">
                                                @foreach($agents as $agent)
                                                    @if($setting)
                                                        @if($setting->setting_value == $agent->id)
                                                            <option value="{{ $agent->id }}" selected> {{ $agent->name }} </option>
                                                        @else
                                                            <option value="{{ $agent->id }}" > {{ $agent->name }} </option>
                                                        @endif
                                                    @else
                                                        <option value="{{ $agent->id }}" > {{ $agent->name }} </option>
                                                    @endif
                                                @endforeach
                                            </select>
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
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            @if($setting)
                $('#assign_agent').select2({
                    width:'100%',
                    placeholder:"Select Default Agent",
                    allowClear:true,
                    dropdownParent:$('#settings_form')
                });
            @else
                $('#assign_agent').prepend('<option value="" selected="selected"></option>').select2({
                    width:'100%',
                    placeholder:"Select Default Agent",
                    allowClear:true,
                    dropdownParent:$('#settings_form')
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