@extends('admin.layout.master')

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
                            @include('admin.inc.messages')

                            <form id="booking_form" class="form-horizontal" method="POST" action="{{ route('admin.shipment.book.store') }}" novalidate="novalidate">
                                {{ csrf_field() }}

                                <input type="hidden" name="selected_service_type" id="selected_service_type" value="{{ $booking_types['id'] }}">

                                <div class="row">
                                    <div class="col col_custom">
                                        <h4 class="form-section mb-2 text-center">Sender Information</h4>

                                        <div class="form-group">
                                            <select name="pickup_address" class="select2" id="pickup_address" data-rule-required="true" data-msg-required="Pickup Address is required">
                                                <option value="0">New</option>

                                                @php ($default_pickup_address = FALSE)

                                                @foreach($user as $shipping_information)
                                                    @if ($shipping_information['hidden'] == 0 && $shipping_information['status'] == 1)
                                                            @php ($default_pickup_address = TRUE)

                                                            <option value="{{ $shipping_information['id'] }}" selected="selected" data-city-id="{{ $shipping_information['city']['id'] }}">{{ $shipping_information['poc'] }} : {{ $shipping_information['phone']}} : {{ $shipping_information['pickup_address'] }}</option>
                                                        @endif
                                                @endforeach
                                            </select>
                                        </div>

                                        <div id="new_pickup_address" class="d-none">

                                            <div class="form-group">
                                                <input type="text" name="new_pickup_person_of_contact" class="form-control" placeholder="Person of Contact*" data-rule-required="true" data-msg-required="Person of Contact is required" data-rule-maxlength="100" data-msg-maxlength="Person of Contact can be maximum 100 characters">
                                            </div>

                                            <div class="form-group">
                                                <input type="text" name="new_pickup_phone_number" class="form-control phone_number" placeholder="Phone Number*" data-rule-required="true" data-msg-required="Phone Number is required">
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
                                            <select name="delivery_type" class="form-control select2" id="delivery_type" data-rule-required="true" data-msg-required="Delivery Type is required">
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
                                                <input type="text" name="actual_weight" class="form-control weight" id="actual_weight" placeholder="Total Actual Weight*" data-rule-required="true" data-msg-required="Total Actual Weight is required">

                                                <div class="input-group-append">
                                                    <span class="input-group-text">kg</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="charges_per_kg" class="form-control charges_per_kg" id="charges_per_kg" placeholder="Charges Per kg*" data-rule-required="true" data-msg-required="Charges Per kg is required">
                                        </div>

                                        <div class="form-group">
                                            <label>Total Charges:</label>
                                            <input type="text" name="total_charges" class="form-control total_charges" id="total_charges" placeholder="Total Charges" readonly="readonly">
                                        </div>

                                        <div class="form-group">
                                            <label>Fuel Surcharge:</label>
                                            <input type="text" name="fuel_surcharge" class="form-control fuel_surcharge" id="fuel_surcharge" placeholder="Fuel Surcharge" readonly="readonly">
                                        </div>

                                        <div class="form-group">
                                            <label>GST:</label>
                                            <input type="text" name="gst" class="form-control gst" id="gst" placeholder="GST" readonly="readonly">
                                        </div>

                                        <div class="form-group">
                                            <label>Total Receivable:</label>
                                            <input type="text" name="total_receivable" class="form-control total_receivable" id="total_receivable" placeholder="Total Receivable" readonly="readonly">
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
                                            <button type="submit" name="book" id="sub_book" class="btn btn-primary" value="Book">Book</button>
                                            <button type="submit" name="book_and_print" id="sub_book_print" class="btn btn-primary ml-1" value="Book & Print">Book &amp; Print</button>
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
            $('#shipping_mode').change(function(){
               $('#actual_weight').val(null);
                $('#charges_per_kg').val(null);
            });
            $('#actual_weight, #charges_per_kg, #shipping_mode, #new_pickup_city').change(function(){
                var actual_weight = parseFloat($('#actual_weight').val()) || 0;
                var charges_per_kg = parseFloat($('#charges_per_kg').val()) || 0;
                if ($('#pickup_address').val() == 0) {
                    var pickup_city_id = $('#new_pickup_city').val();
                }
                else{
                    var pickup_city_id = $('#pickup_address').find(':selected').data('city-id');
                }

                $('#total_charges').val(actual_weight * charges_per_kg);
                $.ajax({
                    url:'{!! route('admin.shipment.book.add_fuel_surcharge_gst_total') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'shipping_mode_id': $('#shipping_mode').val(),
                        'city_id': pickup_city_id,
                        'total_c': $('#total_charges').val()
                    }
                }).done(function (data) {
                        $('#fuel_surcharge').val(data.fuel);
                        $('#gst').val(data.gst);
                        $('#total_receivable').val(data.receivable);
                });
            });

            $('#actual_weight, #charges_per_kg').change(function(){

                $.ajax({
                    url:'{!! route('admin.shipment.book.check_standard_weight') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'actual_weight': $('#actual_weight').val(),
                        'charges_per_kg': $('#charges_per_kg').val(),
                        'delivery_type': $('#delivery_type').val(),
                        'shipping_mode': $('#shipping_mode').val()
                    }
                }).done(function (data) {
                        if(data.status === 1){
                            $('#span').remove();
                            var span = '<span id="span" style="color: red">'+data.error+'</span>';
                            $('#actual_weight').parent('div').append(span);
                            $('#sub_book').prop('disabled', true);
                            $('#sub_book_print').prop('disabled', true);

                        }
                        if(data.status === 0) {
                            $('#span').remove();
                            var span = '<span id="span" style="color: red">'+data.error+'</span>';
                            $('#charges_per_kg').parent('div').append(span);
                            $('#sub_book').prop('disabled', true);
                            $('#sub_book_print').prop('disabled', true);
                        }
                    if(data.status === 2) {
                        $('#span').remove();
                        $('#sub_book').prop('disabled', false);
                        $('#sub_book_print').prop('disabled', false);
                    }
                });
            });

            $('#delivery_type, #consignee_city').change(function () {
                if($('#delivery_type').val() == 2){
                    $('#consignee_address').prop('disabled', true);
                }
                else{
                    $('#consignee_address').prop('disabled', false);
                }
            });

            @if (session('print'))
            $.ajax({
                url: '{!! route('admin.shipment.book.print_air_waybill') !!}',
                method: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'ids': '{{ session('print') }}',
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

            @if (!$default_pickup_address)
            $('#pickup_address').prepend('<option value="" selected="selected"></option>');
            @endif

            $('#pickup_address').select2({
                width: '100%',
                placeholder: 'Pickup Address*',
                closeOnSelect: true
            }).bind('change', function() {
                $(this).valid();
                console.log('gggg')
                shipping_modes();

                if (this.value == 0) {
                    $('#new_pickup_address').removeClass('d-none');
                }
                else {
                    $('#new_pickup_address').addClass('d-none');
                }

                var pickup_city = $(this).find(':selected').data('city-id');
                var consignee_city = $('#consignee_city').val();

                // shipping_mode_same_day(pickup_city, consignee_city);
            });

            $('#new_pickup_city').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'City*'
            }).bind('change', function() {
                $(this).valid();

                shipping_modes();

                var pickup_city = $(this).val();
                var consignee_city = $('#consignee_city').val();

            });

            $('#shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Mode of Shipping*'
            });

            $('#payment_mode').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Mode of Payment*'
            });

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
            });
            $('.weight').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'digits': 2,
                'min': 0.1,
                'max': 1000
            });

            $('.total_receivable').inputmask({
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