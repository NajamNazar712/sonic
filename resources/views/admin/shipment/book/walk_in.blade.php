@extends('client.layout.master')

@section('title', 'Book a Shipment')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">Book a Shipment (Walk-In)</h1>
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('client.inc.messages')

                            <form id="booking_form" class="form-horizontal" method="POST" action="{{ route('cod.shipment.book.store') }}" novalidate="novalidate">
                                {{ csrf_field() }}

                                <input type="hidden" name="selected_service_type" id="selected_service_type" value="{{ $booking_types['id'] }}">

                                <div class="row">
                                    <div class="col col_custom">
                                        <h4 class="form-section mb-2 text-center">Pickup Information</h4>

                                        <div id="new_pickup_address">
                                            <div class="form-group">
                                                <textarea name="new_pickup_address" class="form-control" placeholder="Address*" data-rule-required="true" data-msg-required="Address is required" data-rule-maxlength="190" data-msg-maxlength="Address can be maximum 190 characters"></textarea>
                                            </div>

                                            <div class="form-group">
                                                <input type="text" name="new_pickup_person_of_contact" class="form-control" placeholder="Person of Contact*" data-rule-required="true" data-msg-required="Person of Contact is required" data-rule-maxlength="100" data-msg-maxlength="Person of Contact can be maximum 100 characters">
                                            </div>

                                            <div class="form-group">
                                                <input type="text" name="new_pickup_phone_number_1" class="form-control phone_number" placeholder="Phone Number 1*" data-rule-required="true" data-msg-required="Phone Number is required">
                                            </div>

                                            <div class="form-group">
                                                <input type="text" name="new_pickup_phone_number_2" class="form-control phone_number" placeholder="Phone Number 2">
                                            </div>

                                            <div class="form-group">
                                                <input type="email" name="new_pickup_email_address" class="form-control" placeholder="Email Address*" data-rule-required="true" data-msg-required="Email Address is required">
                                            </div>

                                            <div class="form-group">
                                                <select name="new_pickup_city" class="select2" id="new_pickup_city" data-rule-required="true" data-msg-required="City is required">
                                                    @foreach($cities as $city)
                                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col col_custom">
                                        <h4 class="form-section mb-2 text-center">Consignee Information</h4>

                                        <div class="form-group">
                                            <select name="Delivery_type" class="form-control select2" id="delivery_type" data-rule-required="true" data-msg-required="Delivery Type is required">
                                                @foreach($delivery_type as $delivery)
                                                    <option value="{{ $delivery->id }}">{{ $delivery->delivery_type }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <select name="consignee_city" class="select2" id="consignee_city" data-rule-required="true" data-msg-required="City is required">
                                                @foreach($consignee_cities as $city)
                                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="consignee_name" class="form-control" placeholder="Name*" data-rule-required="true" data-msg-required="Name is required" data-rule-maxlength="100" data-msg-maxlength="Name can be maximum 100 characters">
                                        </div>

                                        <div class="form-group">
                                            <textarea id="consignee_address" name="consignee_address" class="form-control" placeholder="Address*" data-rule-required="true" data-msg-required="Address is required" data-rule-maxlength="190" data-msg-maxlength="Address can be maximum 190 characters"></textarea>
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="consignee_phone_number_1" class="form-control phone_number" placeholder="Phone Number 1*" data-rule-required="true" data-msg-required="Phone Number is required">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="consignee_phone_number_2" class="form-control phone_number" placeholder="Phone Number 2">
                                        </div>

                                        <div class="form-group">
                                            <input type="email" name="consignee_email_address" class="form-control" placeholder="Email Address" data-rule-maxlength="100" data-msg-maxlength="Email Address can be maximum 100 characters">
                                        </div>
                                    </div>

                                    <div class="col col_custom_middle">
                                        <h4 class="form-section mb-2 text-center">Order Information</h4>

                                        <div class="form-group">
                                            <input name="order_id" class="form-control" placeholder="Order ID" data-rule-remote="{{ route('cod.shipment.book.order_id') }}" data-msg-remote="Order ID must be unique" data-rule-maxlength="100" data-msg-maxlength="Order ID can be maximum 100 characters">
                                        </div>

                                        <div id="regular">
                                            <div class="form-group">
                                                <select name="product_type" class="select2" id="product_type" data-rule-required="true" data-msg-required="Product Type is required">
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <textarea name="item_description" class="form-control" placeholder="Item Description*" data-rule-required="true" data-msg-required="Item Description is required" data-rule-maxlength="250" data-msg-maxlength="Item Description can be maximum 250 characters"></textarea>
                                            </div>

                                            <div class="form-group input-group">
                                                <input type="text" name="item_quantity" class="form-control text-center quantity" placeholder="Item Quantity*" data-rule-required="true" data-msg-required="Item Quantity is required">
                                            </div>
                                        </div>

                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
												<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
													<span class="la la-calendar-o"></span>
												</span>
                                            </div>

                                            <input type="text" name="pickup_date" class="form-control pickadate bg-primary border-primary white rounded-right" id="pickup_date" placeholder="Pickup Date*" data-rule-required="true" data-msg-required="Pickup Date is required">
                                        </div>

                                        <div class="form-group">
                                            <textarea name="special_instructions" class="form-control" placeholder="Special Instructions" data-rule-maxlength="190" data-msg-maxlength="Special Instructions can be maximum 190 characters"></textarea>
                                        </div>
                                    </div>

                                    <div class="col col_custom">
                                        <h4 class="form-section mb-2 text-center">Shipping Information</h4>

                                        <div class="form-group">
                                            <select name="shipping_mode" class="select2" id="shipping_mode" data-rule-required="true" data-msg-required="Mode of Shipping is required">
                                                @foreach($shipping_mode as $shipping)
                                                    <option value="{{ $shipping->id }}">{{ $shipping->mode }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <div class="form-group input-group mb-0">
                                                <input type="text" name="actual_weight" class="form-control weight" placeholder="Total Actual Weight*" data-rule-required="true" data-msg-required="Total Actual Weight is required">

                                                <div class="input-group-append">
                                                    <span class="input-group-text">kg</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="charges_per_kg" class="form-control charges_per_kg" placeholder="Charges Per kg*" data-rule-required="true" data-msg-required="Charges Per kg is required">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="total_charges" class="form-control total_charges" placeholder="Total Charges" readonly="readonly">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="fuel_surcharge" class="form-control fuel_surcharge" placeholder="Fuel Surcharge" readonly="readonly">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="gst" class="form-control gst" placeholder="GST" readonly="readonly">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="total_receivable" class="form-control total_receivable" placeholder="Total Receivable" readonly="readonly">
                                        </div>
                                    </div>

                                    <div class="col col_custom">
                                        <h4 class="form-section mb-2 text-center">Payment Information</h4>

                                        <div class="form-group">
                                            <select name="payment_mode" class="select2" id="payment_mode" data-rule-required="true" data-msg-required="Mode of Payment is required">
                                                @foreach($payment_modes as $payment_mode)
                                                    <option value="{{ $payment_mode->id }}">{{ $payment_mode->mode }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col">
                                        <div class="form-group text-center">
                                            <button type="submit" name="book" class="btn btn-primary" value="Book">Book</button>
                                            <button type="submit" name="book_and_print" class="btn btn-primary ml-1" value="Book & Print">Book &amp; Print</button>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/toggle/bootstrap-switch.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/switch.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/spinner/jquery.bootstrap-touchspin.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/toggle/bootstrap-checkbox.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/spinner/jquery.bootstrap-touchspin.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script>


        $(document).ready(function() {

            @if (session('print'))
            $.ajax({
                url: '{!! route('cod.shipment.book.print_air_waybill') !!}',
                method: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'ids[]': '{{ session('print') }}',
                    'twice': true
                }
            })
                .done(function(data) {
                    var tab = window.open('', '_blank');

                    if(!tab) {
                        swal({
                            title: 'Popup Blocker Enabled!',
                            text: 'Please add this site to your exception list.',
                            icon: 'error',
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });
                    }
                    else {
                        tab.document.write(data);
                        tab.document.close();
                        tab.focus();
                    }
                });
            @endif

            function shipping_modes() {
                if ($('#pickup_address').val() == 0) {
                    var pickup_city_id = $('#new_pickup_city').val();
                }

                consignee_city_id = $('#consignee_city').val();
            }


            $('#new_pickup_city').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'City*'
            });

            $('#delivery_type').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Delivery Type*'
            });

            $('#consignee_city').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'City*'
            });

            $('#product_type').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Product Type*'
            }).bind('change', function() {
                $(this).valid();
            });

            $('#pickup_date').pickadate({
                firstDay: 1,
                clear: '',
                min: '{{ Carbon\Carbon::now() }}',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#pickup_date_root').css('top', '-350px');
                },
                onSet: function(context) {
                    $('#pickup_date').valid();
                }
            });

            $('#shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Mode of Shipping*'
            });

            $('#payment_mode').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Mode of Payment*'
            })

            var check = @json($check);
            $('#booking_form').validate({
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
                    var consignee_address = $('#consignee_address').val();
                    var strArray = consignee_address.split(/[ ,]+/);
                    var present = [];
                    for(k=0;k<strArray.length;k++) {
                        for (i = 0; i < check.length; i++) {
                            if(JSON.stringify(strArray[k]).toLowerCase()=== JSON.stringify(check[i]).toLowerCase()){
                                present.push(strArray[k]);
                            }
                        }
                    }
                    // console.log(present.length);
                    // console.log(present);
                    if(present.length > 0){
                        swal({
                            title: 'Warning',
                            text: 'Potential Non Service Area: ' + present,
                            icon: 'info',
                            buttons:{
                                confirm: {
                                    text: 'Ok',
                                    value: false,
                                    visible: true,
                                    closeModal: true
                                }},
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        }).then(function() {
                            swal({
                                title: 'Please Wait!',
                                text: 'Your shipment is being booked!',
                                icon: 'info',
                                buttons: false,
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });

                            form.submit();
                        });
                    }
                    else {
                        swal({
                            title: 'Please Wait!',
                            text: 'Your shipment is being booked!',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        form.submit();
                    }
                }
            });

            $('.phone_number').inputmask({
                'mask': '9999-9999999',
                'clearIncomplete': true
            });

            $('.quantity').TouchSpin({
                min: 1,
                max: 1000,
                buttondown_class: 'btn btn-primary rounded-left',
                buttonup_class: 'btn btn-primary rounded-right',
                buttondown_txt: '<i class="ft-minus"></i>',
                buttonup_txt: '<i class="ft-plus"></i>'
            }).bind('input change', function() {
                if ($(this).hasClass('danger')) {
                    $(this).valid();
                }

                if (service_type == 3) {
                    try_and_buy_total_quantity();
                }
            });

            $('.bootstrap-touchspin-down, .bootstrap-touchspin-up').attr('tabindex', -1);

            $('.price').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'groupSeparator': ',',
                'autoGroup': true,
                'min': 1,
                'max': 100000
            }).bind('input change', function() {
                if (service_type == 3) {
                    try_and_buy_total_price();
                }
            });

            $('.weight').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'digits': 2,
                'min': 0.1,
                'max': 1000
            });

            $('.amount').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'groupSeparator': ',',
                'autoGroup': true,
                'max': 1000000
            });
        });
    </script>
@endsection