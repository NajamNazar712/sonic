@extends('admin.layout.master')

@section('title', 'Delivery Call Verification Ratio')

@section('css')
    {{--<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/custom.css')}}">--}}
    {{--<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">--}}
    {{--<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">--}}

@endsection
@section('content')
    <h1>Delivery Call Verification Ratio</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <div class="row justify-content-center">
                                <div class="col-md-9">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.cod_cap_zones.update') }}">
                                        {{ csrf_field() }}
                                        @if(!$settings->isEmpty())
                                            @foreach($settings as $setting)
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="col ratio">
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">Minimum Range</span>
                                                                </div>
                                                                <input type="text" class="form-control" value="{{$setting->min}}" name="min[{{$setting->id}}]" placeholder="Minimum Range" required data-rule-required="true" data-msg-required="Minimum range is required">
                                                                <div class="input-group-append mr-1">
                                                                    <span class="input-group-text">%</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col ratio">
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">Maximum Range</span>
                                                                </div>
                                                                <input type="text" class="form-control" value="{{$setting->max}}" name="max[{{$setting->id}}]" placeholder="Maximum Range" required data-rule-required="true" data-msg-required="Maximum range is required">
                                                                <div class="input-group-append mr-1">
                                                                    <span class="input-group-text">%</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col ratio">
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">Call Verification</span>
                                                                </div>
                                                                <input type="text" class="form-control" value="{{$setting->verification}}" name="verification[{{$setting->id}}]" placeholder="Call Verification Range" required data-rule-required="true" data-msg-required="Call verification range is required">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">%</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="col ratio">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Minimum Range</span>
                                                            </div>
                                                            <input type="text" class="form-control ratio" name="min[1]" placeholder="Minimum Range" required data-rule-required="true" data-msg-required="Minimum range is required">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col ratio">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Maximum Range</span>
                                                            </div>
                                                            <input type="text" class="form-control ratio" name="max[1]" placeholder="Maximum Range" required data-rule-required="true" data-msg-required="Maximum range is required">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col ratio">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Call Verification</span>
                                                            </div>
                                                            <input type="text" class="form-control ratio" name="verification[1]" placeholder="Call Verification Range" required data-rule-required="true" data-msg-required="Call verification range is required">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <button type="button" class="btn btn-primary">Update</button>
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#settings_form').validate({

                errorClass: "danger",
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parents('.ratio'));
                },
                submitHandler: function (form) {

                    $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    swal({
                        title: 'Please Wait!',
                        text: 'Pickup Request Weight is being added!',
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