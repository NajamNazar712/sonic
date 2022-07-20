@extends('admin.layout.master')

@section('title', 'International Extra Service Charges')

@section('content')



    <h1 class="mb-1">
        International Extra Service Charges
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <form id="add_shipment_form" class="form mb-1" novalidate="novalidate" method="post" action="{{route('admin.international.extra_service_charges.submit')}}">
                    @csrf
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-4 col-sm-6">
                            <div class="form-group">
                                <input type="text" name="tracking_number" class="form-control tracking_number" placeholder="Tracking Number*" data-rule-required="true" data-msg-required="Tracking Number is required">

                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-4 col-sm-6">
                            <div class="form-group">
                                <input type="text" name="amount" class="form-control amount" placeholder="Enter Amount*" data-rule-required="true" data-msg-required="Amount is required" disabled>

                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-lg-6 col-md-6 col-sm-6 text-center">
                            <button type="submit" class="btn btn-primary submit" disabled>Submit</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>



@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/toggle/bootstrap-switch.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/toggle/bootstrap-checkbox.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/quagga/quagga.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/custom.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {


            $('#add_shipment_form input.tracking_number').focus();

            $('#add_shipment_form input.tracking_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            var tracking_number = $('#add_shipment_form .tracking_number').val();

            $('.tracking_number').bind('change',function () {
                $.ajax({
                    url: '{!! route('admin.international.extra_service_charges.shipment.detail') !!}',
                    method: 'POST',
                    data: {
                        'tracking_number': this.value,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function(data) {
                     if(data.status == 1){

                         $('#add_shipment_form .amount').prop('disabled', false);
                         $('#add_shipment_form .submit').prop('disabled', false);
                     }
                     else{
                         $('#add_shipment_form .amount').prop('disabled', true);
                         $('#add_shipment_form .submit').prop('disabled', true);
                         toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                     }
                });
            });


            $('#add_shipment_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    swal({
                        title: 'Please Wait!',
                        text: 'Charges are being added!',
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