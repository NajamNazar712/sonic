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
                                <div class="col">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.cod_cap_zones.update') }}">
                                        {{ csrf_field() }}
                                        <div class="ratios_wrapper justify-content-center" id="ratios_wrapper">
                                            @if(!$settings->isEmpty())
                                                @foreach($settings as $index => $setting)
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <div class="col-3 ratio">
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text">Minimum Range</span>
                                                                    </div>
                                                                    <input type="text" class="form-control" value="{{$setting->min}}" name="min[{{$setting->id}}]" required data-rule-required="true" data-msg-required="Minimum range is required">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">%</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-3 ratio">
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text">Maximum Range</span>
                                                                    </div>
                                                                    <input type="text" class="form-control" value="{{$setting->max}}" name="max[{{$setting->id}}]" required data-rule-required="true" data-msg-required="Maximum range is required">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">%</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-3 ratio">
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text">Call Verification</span>
                                                                    </div>
                                                                    <input type="text" class="form-control" value="{{$setting->verification}}" name="verification[{{$setting->id}}]" required data-rule-required="true" data-msg-required="Call verification range is required">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">%</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @if($index != 0)
                                                            <div class="col-1">
                                                                <span  class="btn btn-danger rounded btn-sm-width row_close" id="row_close"><i class="ft-x"></i></span>
                                                            </div>
                                                            @endif
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
                                                                <input type="text" class="form-control ratio" name="min[1]" required data-rule-required="true" data-msg-required="Minimum range is required">
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
                                                                <input type="text" class="form-control ratio" name="max[1]" required data-rule-required="true" data-msg-required="Maximum range is required">
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
                                                                <input type="text" class="form-control ratio" name="verification[1]" required data-rule-required="true" data-msg-required="Call verification range is required">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">%</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-outline-success mb-1" title="Add more ratios" id="add_row_btn"><i class="la la-plus"></i></button>
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
    </section>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            var settings = @json($settings);
            var row_count = 0;
            if(settings.length != 0){
                row_count = settings.length + 1;
            }
            else{
                row_count = 2;
            }
            console.log(row_count);
            $('#add_row_btn').on('click',function () {
                let htmdiv = '<div class="form-group">' +
                                '<div class="input-group">' +
                                    '<div class="col ratio">' +
                                        '<div class="input-group">' +
                                            '<div class="input-group-prepend">' +
                                                '<span class="input-group-text">Minimum Range</span>' +
                                            '</div>' +
                                            '<input type="text" class="form-control ratio" name="min['+ row_count + ']" required data-rule-required="true" data-msg-required="Minimum range is required">' +
                                            '<div class="input-group-append">' +
                                                '<span class="input-group-text">%</span>' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>' +
                                    '<div class="col ratio">' +
                                        '<div class="input-group">' +
                                            '<div class="input-group-prepend">' +
                                                '<span class="input-group-text">Maximum Range</span>' +
                                            '</div>' +
                                            '<input type="text" class="form-control ratio" name="max['+ row_count + ']" required data-rule-required="true" data-msg-required="Maximum range is required">' +
                                            '<div class="input-group-append">' +
                                                '<span class="input-group-text">%</span>' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>' +
                                    '<div class="col ratio">' +
                                        '<div class="input-group">' +
                                            '<div class="input-group-prepend">' +
                                                '<span class="input-group-text">Call Verification</span>' +
                                            '</div>' +
                                            '<input type="text" class="form-control ratio" name="verification['+ row_count + ']" required data-rule-required="true" data-msg-required="Call verification range is required">' +
                                            '<div class="input-group-append">' +
                                                '<span class="input-group-text">%</span>' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>' +
                                    '<div class="col-1">' +
                                    '<span  class="btn btn-danger rounded btn-sm-width row_close" id="row_close"><i class="ft-x"></i></span>' +
                                    '</div>' +
                                '</div>' +
                            '</div>';
                $('#ratios_wrapper').append(htmdiv);
                // masks();
                row_count++;
            });

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