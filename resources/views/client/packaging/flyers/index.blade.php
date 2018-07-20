@extends('client.layout.master')

@section('content')
    <h1 class="mb-1">
        Packaging Material Request
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('client.inc.messages')
            <div class="container">
                <form action="{{route('cod.packaging.requests.submit')}}" id="material_request_form" method="post">
                    @csrf
                    <div class="row justify-content-md-center">
                        <div class="col-md-6">
                            <div class="form-body">
                                <div class="form-group">
                                    <select name="address_select" id="address_select" class="select2 form-control" style="width:100%;" data-rule-required="true" data-msg-required="This field is required">
                                        <option value="0">New</option>
                                        @foreach($address as $pickup)
                                            <option value="{{$pickup->id}}">{{$pickup->pickup_address}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="new_pickup_address" class="d-none">
                                    <div class="form-group">
                                        <textarea name="new_pickup_address" class="form-control" placeholder="Address*" data-rule-required="true" data-msg-required="Address is required"></textarea>
                                    </div>

                                    <div class="form-group">
                                        <input type="text" name="new_pickup_person_of_contact" class="form-control" placeholder="Person of Contact*" data-rule-required="true" data-msg-required="Person of Contact is required">
                                    </div>

                                    <div class="form-group">
                                        <input type="text" name="new_pickup_phone_number" id="new_pickup_phone_number" class="form-control phone_number" placeholder="Phone Number*" data-rule-required="true" data-msg-required="Phone Number is required">
                                    </div>
                                    <div class="form-group">
                                        <select name="new_pickup_city" class="select2" id="new_pickup_city" data-rule-required="true" data-msg-required="City is required">
                                            @foreach($cities as $city)
                                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="sm_flyer">Small Flyers</label>
                                    <input type="text" id="sm_flyer" class="form-control numeric flyer" placeholder="Small Flyers Quantity" name="sm_flyer">
                                </div>
                                <div class="form-group">
                                    <label for="md_flyer">Medium Flyers</label>
                                    <input type="text" id="md_flyer" class="form-control numeric flyer" placeholder="Medium Flyers Quantity" name="md_flyer">
                                </div>
                                <div class="form-group">
                                    <label for="lg_flyer">Large Flyers</label>
                                    <input type="text" id="lg_flyer" class="form-control numeric flyer" placeholder="Large Flyers Quantity" name="lg_flyer">
                                </div>
                                <div class="form-group">
                                    <label for="boxes">Boxes</label>
                                    <input type="text" id="boxes" class="form-control numeric flyer" placeholder="Boxes Quantity" name="boxes">
                                </div>
                                <div class="form-group">
                                    <label for="boxes">Flyer Mode of Paymengt</label>
                                    <select name="mode_of_payment" class="select2" id="mode_of_payment" data-rule-required="true" data-msg-required="Payment mode is required">
                                        <option></option>
                                        @foreach($payment_mode as $mode)
                                            <option value="{{$mode->id}}">{{$mode->mode}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <hr>
                                <button id="RequestMaterialBtn" type="submit" class="btn btn-primary btn-block">Request Material</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            </div>
        </div>
    </div>


@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    {{--<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">--}}

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
{{--    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $('document').ready(function(){
            $('body').on('change','#material_request_form input,#material_request_form textarea',function() {
                $(this).val($(this).val().trim());
            });
            $("#new_pickup_phone_number").inputmask({'mask': "9999-9999999", 'clearIncomplete': true});
            $('.numeric').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'min': 1,
                'max': 10000
            });
            $('#mode_of_payment').select2({
                width: '100%',
                placeholder: 'Select Payment Mode'
            });
            $('#address_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Send To*'
            }).bind('change', function() {
                $(this).valid();

                if (this.value == 0) {
                    $('#new_pickup_address').removeClass('d-none');
                }
                else {
                    $('#new_pickup_address').addClass('d-none');
                }

                // var pickup_city = $(this).find(':selected').data('city-id');
                // var consignee_city = $('#consignee_city').val();

                // shipping_mode_same_day(pickup_city, consignee_city);
            });
            $('#new_pickup_city').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'City*'
            }).bind('change', function() {
                $(this).valid();

                // var pickup_city = $(this).val();
                // var consignee_city = $('#consignee_city').val();
                //
                // shipping_mode_same_day(pickup_city, consignee_city);
            });


            $('#material_request_form').validate({
                rules: {
                    sm_flyer: {
                        require_from_group: [1, ".flyer"]
                    },
                    md_flyer: {
                        require_from_group: [1, ".flyer"]
                    },
                    lg_flyer: {
                        require_from_group: [1, ".flyer"]
                    },
                    boxes: {
                        require_from_group: [1, ".flyer"]
                    }
                },
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Your request is being submitted!',
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