@extends('admin.layout.master')

@section('title', 'Minimum Chargeable Weight')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Minimum Chargeable Weight
                </h1>
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <form id="settings_form" class="form-horizontal row justify-content-center" method="POST" action="{{ route('admin.settings.minimum_chargeable_weight.update') }}" novalidate="novalidate">
                                {{ csrf_field() }}
                                <div class="col-4">
                                    <div class="form-group row">
                                        <h4 class="text-center" style="text-align: left"><b>Rush*:</b></h4>
                                        <input type="text" name="on" class="form-control weights" placeholder="Minimum Chargeable Weight*" data-rule-required="true" data-msg-required="Minimum chargeable weight is required" value="{{ $on }}" data-rule-min="0.01" data-msg-min="Minimum chargeable weight can not be less than 0.01">
                                    </div>

                                    <div class="form-group row">
                                        <h4 class="text-center" style="text-align: left"><b>SaverPlus*:</b></h4>
                                        <input type="text" name="ol" class="form-control weights" placeholder="Minimum Chargeable Weight*" data-rule-required="true" data-msg-required="Minimum chargeable weight is required" value="{{ $ol}}" data-rule-min="0.01" data-msg-min="Minimum chargeable weight can not be less than 0.01">
                                    </div>

                                    <div class="form-group row">
                                        <h4 class="text-center" style="text-align: left"><b>Swift*:</b></h4>
                                        <input type="text" name="det" class="form-control weights" placeholder="Minimum Chargeable Weight*" data-rule-required="true" data-msg-required="Minimum chargeable weight is required" value="{{ $det}}" data-rule-min="0.01" data-msg-min="Minimum chargeable weight can not be less than 0.01">
                                    </div>

                                    <div class="form-group row">
                                        <h4 class="text-center" style="text-align: left"><b>Sameday*:</b></h4>
                                        <input type="text" name="same_day" class="form-control weights" placeholder="Minimum Chargeable Weight*" data-rule-required="true" data-msg-required="Minimum chargeable weight is required" value="{{ $same_day}}" data-rule-min="0.01" data-msg-min="Minimum chargeable weight can not be less than 0.01">
                                    </div>
                                    <div class="form-group text-center">
                                        <button type="submit" class="btn btn-primary">Update</button>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('#settings_form input.weights').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false
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