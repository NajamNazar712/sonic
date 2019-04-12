@extends('admin.layout.master')

@section('title', 'COD CAP For Zone Classes')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    COD CAP For Zone Classes
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.cod_cap_zones.update') }}" novalidate="novalidate">
                                        {{ csrf_field() }}

                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Class A</span>
                                                </div>
                                                <input type="text" name="class_a" class="form-control class" placeholder="Class A*" data-rule-required="true" data-msg-required="Class A is required" value="{{ $class_a['setting_value'] }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Rs</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Class B</span>
                                                </div>
                                                <input type="text" name="class_b" class="form-control class" placeholder="Class B*" data-rule-required="true" data-msg-required="Class B is required" value="{{ $class_b['setting_value'] }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Rs</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Class C</span>
                                                </div>
                                                <input type="text" name="class_c" class="form-control class" placeholder="Class C*" data-rule-required="true" data-msg-required="Class C is required" value="{{ $class_c['setting_value'] }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Rs</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Class D</span>
                                                </div>
                                                <input type="text" name="class_d" class="form-control class" placeholder="Class D*" data-rule-required="true" data-msg-required="Class D is required" value="{{ $class_d['setting_value'] }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Rs</span>
                                                </div>
                                            </div>
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
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('#settings_form .class').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            $('#settings_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                }
            });
        });
    </script>
@endsection