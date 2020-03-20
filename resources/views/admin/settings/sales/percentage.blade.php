@extends('admin.layout.master')

@section('title', 'Projection Percentage')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Projection Percentage
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-5 col-sm-4 col-md-3 col-lg-2">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.sales.projection.percentage.store') }}" novalidate="novalidate">
                                        {{ csrf_field() }}

                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="text" name="projection_percentage" class="form-control projection_percentage" placeholder="Projection Percentage*" data-rule-required="true" data-msg-required="Projection Percentage in numbers is required" value="{{$projection_percentage}}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('#settings_form input.projection_percentage').inputmask({
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