@extends('admin.layout.master')

@section('title', 'Log Fake Statuses')

@section('content')
    <h1 class="mb-1">
        Log Fake Statuses
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body justify-content-center">
                @include('admin.inc.messages')
                <div class="row justify-content-center">
                    <form id="add_fake_status_form" class="col-3 text-center" method="POST" action="{{ route('admin.delivery.fake_status.log.store') }}" novalidate="novalidate">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <input type="text" class="form-control" name="tracking_number" id="tracking_number" placeholder="Tracking Number" data-tags-input-name="tracking_number" data-rule-required="true" data-msg-required="Tracking Number is required">
                        </div>
                        <div class="form-group">
                            <textarea type="text" class="form-control" name="remarks" id="remarks" placeholder="Remarks" data-tags-input-name="remarks" data-rule-required="true" data-msg-required="Remarks is required" rows="6"></textarea>
                        </div>
                        <div class="col">
                            <button type="submit" name="add_fake_status" class="btn btn-primary btn-min-width">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">

    <style>

    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script>
        $(document).ready(function() {

            $('#tracking_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#add_fake_status_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Shipment is being marked as Fake Status',
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