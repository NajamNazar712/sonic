@extends('admin.layout.master')

@section('title', 'FTL Booking')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">FTL (Walk-In)</h1>
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <form id="booking_form" class="form-horizontal" method="POST" action="{{ route('admin.shipment.book.ftl.store') }}" novalidate="novalidate">
                                {{ csrf_field() }}

                                <input type="hidden" name="selected_service_type" id="selected_service_type" value="{{ $booking_types['id'] }}">

                                <div class="row">
                                    <div class="col col_custom">
                                        <h4 class="form-section mb-2 text-center">Sender Information</h4>

                                        <div class="form-group">
                                            <select name="pickup_address" class="select2" id="pickup_address" data-rule-required="true" data-msg-required="Pickup Address is required">
                                                <option value="0">New</option>

                                                @php ($default_pickup_address = FALSE)

                                                @foreach($user_shipping_infos as $shipping_information)
                                                    @if ($shipping_information['hidden'] == 0 && $shipping_information['status'] == 1)
                                                        @if ($shipping_information['default_address'] == 1)
                                                            @php ($default_pickup_address = TRUE)

                                                            <option value="{{ $shipping_information['id'] }}" selected="selected" data-city-id="{{ $shipping_information['city']['id'] }}">{{ $shipping_information['poc'] }} - {{ $shipping_information['phone']}}: {{ $shipping_information['pickup_address'] }}, {{ $shipping_information['city']['name'] }}</option>
                                                        @else
                                                            <option value="{{ $shipping_information['id'] }}" data-city-id="{{ $shipping_information['city']['id'] }}">{{ $shipping_information['poc'] }} - {{ $shipping_information['phone']}}: {{ $shipping_information['pickup_address'] }}, {{ $shipping_information['city']['name'] }}</option>
                                                        @endif
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
                                                        <option value="{{ $city->city_id }}">{{ $city->city_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                     {{--   <div id="pickup_div" class="form-group text-center p-1 border border-light rounded">
                                            <label class="d-block">Pickup</label>
                                            <input type="checkbox" name="pickup" class="switch hidden" id="pickup">
                                        </div>--}}
                                       {{-- <input type="hidden" name="pickup" value="0" id="pickup">--}}
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
                                            <select name="consignee_city" class="select2 consignee_city" id="consignee_city" data-rule-required="true" data-msg-required="City is required">
                                                @foreach($cities as $city)
                                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="consignee_name" class="form-control" placeholder="Name*" data-rule-required="true" data-msg-required="Name is required" data-rule-maxlength="100" data-msg-maxlength="Name can be maximum 100 characters">
                                        </div>

                                        <div class="form-group">
                                            <textarea id="consignee_address" name="consignee_address" class="form-control" placeholder="Address*" data-rule-required="true" data-msg-required="Address is required" data-rule-maxlength="255" data-msg-maxlength="Address can be maximum 255 characters" rows="5"></textarea>
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

                                    <div class="col col_custom">
                                        <h4 class="form-section mb-2 text-center">Order Information</h4>

                                        <div class="form-group">
                                            <input name="order_id" class="form-control" placeholder="Order ID" data-rule-maxlength="100" data-msg-maxlength="Order ID can be maximum 100 characters">
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
                                                <textarea name="item_description" class="form-control" placeholder="Item Description*" data-rule-required="true" data-msg-required="Item Description is required" data-rule-maxlength="500" data-msg-maxlength="Item Description can be maximum 500 characters" rows="5"></textarea>
                                            </div>

                                            <div class="form-group input-group">
                                                <input type="text" name="item_quantity" class="form-control text-center quantity" placeholder="Item Qty.*" data-rule-required="true" data-msg-required="Item Quantity is required">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <textarea name="special_instructions" class="form-control" placeholder="Special Instructions" data-rule-maxlength="190" data-msg-maxlength="Special Instructions can be maximum 190 characters" rows="5"></textarea>
                                        </div>
                                    </div>

                                    <div class="col col_custom_middle">
                                        <h4 class="form-section mb-2 text-center">Shipping Information</h4>

                                        <div class="form-group">
                                            <select name="shipping_mode" class="select2" id="shipping_mode" data-rule-required="true" data-msg-required="Mode of Shipping is required">
                                                @foreach($shipping_mode as $shipping)
                                                    <option value="{{ $shipping->id }}">{{ $shipping->mode }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group" id="actual_weight_div">
                                            <div class="form-group input-group mb-0">
                                                <input type="text" name="actual_weight" class="form-control weight" id="actual_weight" placeholder="Total Actual Weight*" data-rule-required="true" data-msg-required="Total Actual Weight is required">

                                                <div class="input-group-append">
                                                    <span class="input-group-text">kg</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="frieght_div" class="form-group ">
                                            <select name="approve_frieght_request" class="select2" id="approve_frieght_request" data-rule-required="true" data-msg-required="Request ID is required">
                                                @foreach($approve_ftl_requests as $request)
                                                    <option value="{{ $request->id }}">{{ str_pad($request->id, 3, '0', STR_PAD_LEFT) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col col_custom">
                                        <h4 class="form-section mb-2 text-center">Charges Information</h4>

                                        <div class="form-group" id="charges_div">
                                            <input type="text" name="ftl_charges" id="ftl_charges" class="form-control" placeholder="Charges" readonly>
                                        </div>

                                        <div class="form-group" id="ftl_collection_type_div">
                                            <select name="ftl_collection_type" class="select2" id="ftl_collection_type" data-rule-required="true" data-msg-required="Mode of Collection is required">
                                                <option value="1">Invoice</option>
                                                <option value="2">Cash</option>
                                            </select>
                                        </div>
                                       {{-- <div class="form-group">
                                            <select name="charges_mode" class="select2" id="charges_mode" data-rule-required="true" data-msg-required="Charges Mode is required">
                                                @foreach($charges_modes as $charges_mode)
                                                    <option value="{{ $charges_mode->id }}">{{ $charges_mode->charges_mode }}</option>
                                                @endforeach
                                            </select>
                                        </div>--}}

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

            $('#ftl_collection_type').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Collection Type*'
            });

            $('#approve_frieght_request').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Approve Frieght Request*'
            }).bind('change',function(){
                var id = $(this).val();
                if(id) {
                    $.ajax({
                        url: '{!! route('admin.shipment.book.ftl.get_ftl_info') !!}',
                        method: 'POST',
                        data: {
                            'id': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if (data.status == 1) {
                           console.log(data.data.origin_id);
                            $('#consignee_city').val(data.data.origin_id).trigger('change');
                            console.log(data.data.origin_id);
                            $('.quantity').val(data.data.quantity).trigger('change');
                            $('#actual_weight').val(data.data.weight);
                            $('#ftl_charges').val(data.data.total_charges);
                            $('#shipping_mode').val(2).trigger('change');

                        } else {

                            var error = 'No Data found for the selected request';
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });
                }
            });

            @if(request()->has('ftl_req'))
                $('#approve_frieght_request').val("{{request()->get('ftl_req')}}").trigger('change');
            @endif
                    

            $('#delivery_type').change(function () {
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
                    'ids': '{{ session('print') }}'
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

                //consignee_city_id = $('#consignee_city').val();
            }


            $('#new_pickup_city').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'City*'
            }).bind('change', function() {
                if ($(this).hasClass('danger')) {
                    $(this).valid();
                }
            });

            $('#delivery_type').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Delivery Type*'
            }).bind('change', function() {
                if ($(this).hasClass('danger')) {
                    $(this).valid();
                }
                //$('#actual_weight').val('');
                $('#charges_per_kg').val('');
                $('#span').remove();
            });

            $('#consignee_city').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'City*'
            }).bind('change', function() {
                if ($(this).hasClass('danger')) {
                    $(this).valid();
                }
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
            }).bind('change', function() {
                $(this).valid();

                shipping_modes();

                if (this.value == 0) {
                    $('#new_pickup_address').removeClass('d-none');
                }
                else {
                    $('#new_pickup_address').addClass('d-none');
                }

                var pickup_city = $(this).find(':selected').data('city-id');
             /*   var consignee_city = $('#consignee_city').val();*/
            });

            $('#new_pickup_city').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'City*'
            }).bind('change', function() {
                $(this).valid();

                shipping_modes();

                var pickup_city = $(this).val();
               /* var consignee_city = $('#consignee_city').val();*/

            });

            $('#shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Mode of Shipping*'
            }).bind('change', function() {
                if ($(this).hasClass('danger')) {
                    $(this).valid();
                }

                //$('#actual_weight').val(null);
                $('#charges_per_kg').val(null);
                $('#fuel_surcharge').val(null);
                $('#total_charges').val(null);
                $('#gst').val(null);
                $('#total_receivable').val(null);

                $('#span').remove();
            });

            $('#charges_mode').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Charges Mode*'
            }).bind('change', function() {
                if ($(this).hasClass('danger')) {
                    $(this).valid();
                }
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

            $('.weight').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'digits': 2,
                'min': 0.1,
                'max': 100000
            });

            $('.charges_per_kg').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'max': 1000000
            });


             /*   if(flag == false){

                    $.ajax({
                        url: '{!! route('admin.shipment.book.check_quantity') !!}',
                        method: 'POST',
                        data: {
                            'hub_id':pickup_address_id,
                            'type_id': type,
                            'size_id': size,
                            'quantity': quantity,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {

                        if(data.status == 0){
                            $.each(packaging_charges, function(key, value){

                                if(value.id == size && value.type_id == type){
                                    total_packaging_charges = value.standard_charges;
                                }
                            });

                            var charges = parseInt($('#packaging_charges').val());
                            var total_receivable = parseInt($('#total_receivable').val());
                            console.log(total_receivable)
                            var row_charges = total_packaging_charges * parseInt(quantity);
                            charges += row_charges;
                            total_receivable += row_charges;
                            $('#packaging_charges').val(charges);
                            $('#total_receivable').val(total_receivable);

                            var type_cell = '<td><input type="hidden" name="pack_type['+ type + size +']" value="'+ type +'">'+ type_name +'</td>';
                            var size_cell = '<td><input type="hidden" name="pack_size['+ type + size +']" value="'+ size +'">'+ size_name +'</td>';
                            var quantity_cell = '<td><input type="hidden" name="pack_quantity['+ type + size +']" value="'+ quantity +'">'+ quantity +'</td>';
                            var remove = '<a href="javascript:void(0);" class="btn btn-sm btn-danger premove"><i class="la la-close"></i></a>';
                            ptable.row.add([type_cell,size_cell,quantity_cell, remove]).node().id = rid;
                            ptable.draw(false);
                            $('tr#'+rid).attr('charges', row_charges);
                            packaging_index_array.push(rid);
                            total_packaging_charges = 0;
                            already_selected_size.push(parseInt(type+size));
                            rid++;
                            $('#pack_material_type').val(null).trigger('change');
                            $('#pack_material_size').val(null).trigger('change');
                            $('#pack_material_quantity').val('');
                        }
                        else{
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }

                    });

                }*/
            

            $('#pickup').checkboxpicker();

            $('#pickup').checkboxpicker().bind('change', function() {

                if (this.checked) {
                    shipment_pickup = 1;
                    $('#actual_weight_div').addClass('d-none');
                    $('#shipment_charges_div').addClass('d-none');
                }
                else {
                    shipment_pickup = 0;
                    $('#actual_weight_div').removeClass('d-none');
                    $('#shipment_charges_div').removeClass('d-none');
                }
            });

        });
    </script>
@endsection