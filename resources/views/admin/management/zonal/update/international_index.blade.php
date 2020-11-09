@extends('admin.layout.master')

@section('title', 'Update Zone')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Update Zone
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <form id="zone_form" class="form-horizontal" method="POST" action="{{ route('admin.management.zonal.update.international.store', ['id' => $zone->id]) }}" novalidate="novalidate">
                                {{ csrf_field() }}

                                <div class="row">
                                    <div class="col-8">
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input type="text" name="name" class="form-control subject" placeholder="Name*" data-rule-required="true" data-msg-required="Name is required" value="{{ $zone->name }}">
                                        </div>
                                    </div>

                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>GST</label>
                                            <input type="text" name="gst" class="form-control gst" placeholder="GST*" data-rule-required="true" data-msg-required="GST is required"value="{{ $zone->gst }}">
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group text-center">
                                            <button type="submit" class="btn btn-primary">Update</button>
                                        </div>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('#zone_form .gst').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'digits': 2,
                'min': 0.1,
                'max': 100
            });

            $('#zone_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Zone is being updated!',
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