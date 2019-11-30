@extends('admin.layout.master')

@section('title', 'Shipper CRM Re-Open Request')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Shipper CRM Re-Open Request
                </h1>
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-4">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.crm_reopen.update') }}" novalidate="novalidate">
                                        {{ csrf_field() }}
                                        <div class="form-group mb-4">
                                            <h4>Show Re-Open button to Shipper:</h4>
                                            <div class="row justify-content-center mt-1">
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    @if($settings['text'] == "on")
                                                        <input type="radio" class="custom-control-input" id="defaultInline1" name="reopen" value="on" checked="">
                                                    @else
                                                        <input type="radio" class="custom-control-input" id="defaultInline1" name="reopen" value="on">
                                                    @endif
                                                    <label class="custom-control-label" for="defaultInline1">Limited</label>
                                                </div>

                                                <div class="custom-control custom-radio custom-control-inline">
                                                    @if($settings['text'] == "off")
                                                        <input type="radio" class="custom-control-input" id="defaultInline2" name="reopen" value="off" checked="">
                                                    @else
                                                        <input type="radio" class="custom-control-input" id="defaultInline2" name="reopen" value="off">
                                                    @endif
                                                    <label class="custom-control-label" for="defaultInline2">Unlimited</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row justify-content-center" id="reopen_count_div">
                                            <div class="col-8">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Re-Open Count</span>
                                                        </div>
                                                        <input type="text" name="count" class="form-control count" data-rule-required="true" data-msg-required="Count is required" value="{{ $settings['setting_value'] }}">
                                                    </div>
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

            @if($settings['text'] == "off")
                $('#reopen_count_div').addClass('d-none');
            @endif

            $('input[type=radio][name=reopen]').change(function() {
                if (this.value == 'on') {
                    $('#reopen_count_div').removeClass('d-none')
                }
                else if (this.value == 'off') {
                    $('#reopen_count_div').addClass('d-none')
                }
            });
            $('#settings_form .count').inputmask({
                'alias': 'integer',
                'min': 1,
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