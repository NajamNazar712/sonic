@extends('client.layout.master')

@section('title', 'Book a Shipment')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Book a Shipment (Corporate)
                    {{--                    @php(dd($min_chargeable_weight[0]['id']))--}}
                    <span id="selected_service_type_name">{{ (Session::has('service_type_name')) ? ('(' . Session::get('service_type_name') . ')') : '' }}</span>
                    <button type="button" class="btn btn-prselected_service_type_nameimary d-block mt-1 ml-auto d-sm-block mt-sm-1 ml-sm-auto mt-md-0 float-md-right float-lg-right" data-toggle="modal" data-target="#select_service_type">Change Service Type</button>
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('client.inc.messages')

                            <form id="booking_form" class="form-horizontal" method="POST" action="{{ route('cod.shipment.book.corporate.store') }}" novalidate="novalidate">
                                {{ csrf_field() }}

                                <input type="hidden" name="selected_service_type" id="selected_service_type" value="{{ Session::get('service_type_id') }}">

                                <div class="row">
                                    <div id="shipping_custom" class="col col_custom">
                                        <h4 id="shipper_header_info" class="form-section mb-2 text-center">Shipper Information</h4>

                                        <div class="form-group">
                                            <p class="border-bottom border-light text-center font-medium-1 text-bold-600">{{ $user->name }}</p>
                                        </div>

                                        <div class="form-group">
                                            <p class="border-bottom border-light text-center font-medium-1 text-bold-600">{{ $user->phone }}</p>
                                        </div>

                                        <div class="form-group">
                                            <select name="pickup_address" class="select2" id="pickup_address" data-rule-required="true" data-msg-required="Pickup Address is required">
                                                <option value="0">New</option>

                                                @php ($default_pickup_address = FALSE)

                                                @foreach($user->shipping as $shipping_information)
                                                    @if ($shipping_information['hidden'] == 0 && $shipping_information['status'] == 1)
                                                        @if ($shipping_information['default_address'] == 1)
                                                            @php ($default_pickup_address = TRUE)

                                                            <option value="{{ $shipping_information['id'] }}" selected="selected" data-city-id="{{ $shipping_information['city']['id'] }}" data-city-name="{{ $shipping_information['city']['name'] }}">{{ $shipping_information['poc'] }}: {{ $shipping_information['pickup_address'] }}, {{ $shipping_information['city']['name'] }}</option>
                                                        @else
                                                            <option value="{{ $shipping_information['id'] }}" data-city-id="{{ $shipping_information['city']['id'] }}" data-city-name="{{ $shipping_information['city']['name'] }}">{{ $shipping_information['poc'] }}: {{ $shipping_information['pickup_address'] }}, {{ $shipping_information['city']['name'] }}</option>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <p class="border-bottom border-light text-center font-medium-1 text-bold-600" id="pickup_city_name"></p>
                                        </div>

                                        <div id="new_pickup_address" class="d-none">
                                            <div class="form-group">
                                                <textarea name="new_pickup_address" class="form-control" placeholder="Address*" data-rule-required="true" data-msg-required="Address is required" data-rule-maxlength="190" data-msg-maxlength="Address can be maximum 190 characters"></textarea>
                                            </div>

                                            <div class="form-group">
                                                <select name="new_pickup_city" class="select2" id="new_pickup_city" data-rule-required="true" data-msg-required="City is required">
                                                    @foreach($cities as $city)
                                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <input type="text" name="new_pickup_person_of_contact" class="form-control" placeholder="Person of Contact*" data-rule-required="true" data-msg-required="Person of Contact is required" data-rule-maxlength="100" data-msg-maxlength="Person of Contact can be maximum 100 characters">
                                            </div>

                                            <div class="form-group">
                                                <input type="text" name="new_pickup_vendor" class="form-control" placeholder="Vendor" data-rule-maxlength="100" data-msg-maxlength="Vendor can be maximum 100 characters">
                                            </div>

                                            <div class="form-group">
                                                <input type="text" name="new_pickup_phone_number" class="form-control phone_number" placeholder="Phone Number*" data-rule-required="true" data-msg-required="Phone Number is required">
                                            </div>

                                            <div class="form-group">
                                                <input type="email" name="new_pickup_email_address" class="form-control" placeholder="Email Address*" data-rule-required="true" data-msg-required="Email Address is required">
                                            </div>
                                        </div>

                                        @if($air_waybill != null)
                                            <div id="info_display" class="form-group text-center p-1 border border-light rounded">
                                                <label class="d-block">Show Information on Air Waybill</label>
                                                @if($air_waybill->information == 1)
                                                    <input type="checkbox" name="information_display" class="switch hidden" id="information_display" checked="checked">
                                                @else
                                                    <input type="checkbox" name="information_display" class="switch hidden" id="information_display">
                                                @endif
                                            </div>
                                        @else
                                            <div id="info_display" class="form-group text-center p-1 border border-light rounded">
                                                <label class="d-block">Show Information on Air Waybill</label>
                                                <input type="checkbox" name="information_display" class="switch hidden" id="information_display" checked="checked">
                                            </div>
                                        @endif
                                    </div>

                                    <div id="consignee_header_div" class="col col_custom">
                                        <h4 id="consignee_header_info" class="form-section mb-2 text-center">Consignee Information</h4>
                                        <label for="consignee_info">Search By Phone No.</label>
                                        <div class="form-group">
                                            <select name="consignee_info" class="select2" id="consignee_info">
                                            </select>
                                        </div>
                                        <div id="delivery_type_div" class="form-group">
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

                                        <div id="self_collection_div" class="form-group text-center p-1 border border-light rounded">
                                            <label class="d-block">Self Collection</label>
                                            <input type="checkbox" name="self_collection" class="switch hidden" id="self_collection">
                                        </div>
                                    </div>

                                    <div id="order_information_header_div" class="col col_custom_middle">
                                        <h4 id="order_header_info" class="form-section mb-2 text-center">Order Information</h4>
                                        @if (Session::has('prefix'))
                                            <div class="form-group">
                                                <input name="order_id" class="form-control order_id" placeholder="Order ID" data-rule-maxlength="100" data-rule-required="true" data-msg-required="Order ID is required" data-msg-maxlength="Order ID can be maximum 100 characters" data-rule-remote="{{ route('cod.shipment.book.order_id') }}" data-msg-remote="Order ID must be unique">
                                            </div>
                                        @else
                                            <div class="form-group">
                                                <input name="order_id" class="form-control" placeholder="Order ID" data-rule-maxlength="100" data-msg-maxlength="Order ID can be maximum 100 characters">
                                            </div>
                                        @endif

                                        <div class="form-group">
                                            <div class="form-group input-group">
                                                <div class="input-group-prepend">
													<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
														<span class="la la-calendar-o"></span>
													</span>
                                                </div>
                                                <input type="text" name="order_date" class="form-control bg-primary border-primary white rounded-right" id="order_date" placeholder="Order Date">
                                            </div>
                                        </div>

                                        <div id="regular">
                                            <div class="form-group">
                                                <select name="product_type" class="select2" id="product_type" data-rule-required="true" data-msg-required="Product Type is required">
                                                    @foreach($products as $product)
                                                        @if($user['product_id'] == $product->id)
                                                            <option value="{{ $product->id }}" selected>{{ $product->product_name }}</option>
                                                        @else
                                                            <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <textarea name="item_description" class="form-control" placeholder="Item Description*" data-rule-required="true" data-msg-required="Item Description is required" data-rule-maxlength="1000" data-msg-maxlength="Item Description can be maximum 1000 characters" rows="5"></textarea>
                                            </div>

                                            <div class="form-group input-group">
                                                <input type="text" name="item_quantity" class="form-control text-center quantity" placeholder="Item Quantity*" data-rule-required="true" data-msg-required="Item Quantity is required">
                                            </div>
                                            <div id="pieces_quantity" class="form-group input-group d-none">
                                                <input  type="text" name="pieces_quantity" class="form-control text-center pieces" placeholder="Pieces*" data-rule-required="true" data-msg-required="Pieces is required">
                                            </div>
                                            <div class="form-group text-center p-1 border border-light rounded">
                                                <label class="d-block">Insurance</label>
                                                <input type="checkbox" name="insurance" class="switch hidden insurance">
                                            </div>

                                            <div class="form-group input-group d-none">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rs</span>
                                                </div>

                                                <input type="text" name="item_price" class="form-control rounded-right price" placeholder="Product Value*" data-rule-required="true" data-msg-required="Product Value is required">
                                            </div>
                                        </div>

                                        <div id="replacement" class="mb-1 d-none">
                                            <h4 class="text-center m-0 p-1 bg-dark white border border-dark rounded-top">Replacement</h4>

                                            <div class="pt-1 pl-1 pr-1 border border-light rounded-bottom">
                                                <div class="form-group">
                                                    <select name="replacement_product_type" class="select2" id="replacement_product_type" data-rule-required="true" data-msg-required="Product Type is required">
                                                        @foreach($products as $product)
                                                            @if($user['product_id'] == $product->id)
                                                                <option value="{{ $product->id }}" selected>{{ $product->product_name }}</option>
                                                            @else
                                                                <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <textarea name="replacement_item_description" class="form-control" placeholder="Item Description*" data-rule-required="true" data-msg-required="Item Description is required" data-rule-maxlength="1000" data-msg-maxlength="Item Description can be maximum 1000 characters" data-toggle="tooltip" data-placement="top" title="" data-original-title="Please describe in a way that rider can understand what to collect from the consignee"></textarea>
                                                </div>

                                                <div class="form-group input-group">
                                                    <input type="text" name="replacement_item_quantity" class="form-control text-center quantity" placeholder="Item Quantity*" data-rule-required="true" data-msg-required="Item Quantity is required">
                                                </div>
                                            </div>
                                        </div>

                                        <div id="try_and_buy" class="d-none">
                                            <div class="repeater mb-1">
                                                <div data-repeater-list="try_and_buy">
                                                    <div class="product mb-1" data-repeater-item>
                                                        <div class="d-flex justify-content-between align-items-center bg-dark border border-dark rounded-top">
                                                            <h4 class="m-1 white">Product #<span>1</span></h4>
                                                            <button data-repeater-delete type="button" class="btn btn-icon btn-danger btn-sm mr-1"><i class="ft-x"></i></button>
                                                        </div>

                                                        <div class="pt-1 pl-1 pr-1 border border-light rounded-bottom">
                                                            <div class="form-group">
                                                                <select name="product_type" class="select2" data-rule-required="true" data-msg-required="Product Type is required">
                                                                    @foreach($products as $product)
                                                                        @if($user['product_id'] == $product->id)
                                                                            <option value="{{ $product->id }}" selected>{{ $product->product_name }}</option>
                                                                        @else
                                                                            <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="form-group">
                                                                <textarea name="item_description" class="form-control" placeholder="Item Description*" data-rule-required="true" data-msg-required="Item Description is required" data-rule-maxlength="1000" data-msg-maxlength="Item Description can be maximum 1000 characters"></textarea>
                                                            </div>

                                                            <div class="form-group input-group">
                                                                <input type="text" name="item_quantity" class="form-control text-center quantity" placeholder="Item Quantity*" data-rule-required="true" data-msg-required="Item Quanity is required">
                                                            </div>

                                                            <div class="form-group input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">Rs</span>
                                                                </div>

                                                                <input type="text" name="item_price" class="form-control rounded-right price" placeholder="Product Value*" data-rule-required="true" data-msg-required="Product Value is required">
                                                            </div>

                                                            <div class="form-group text-center p-1 border border-light rounded">
                                                                <label class="d-block">Insurance</label>
                                                                <input type="checkbox" name="insurance" class="switch hidden insurance">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group text-right">
                                                    <button data-repeater-create type="button" class="btn btn-block btn-primary">Add</button>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <p class="border-bottom border-light text-center font-medium-1 text-bold-600" id="total_quantity">Total Quantity: <span>0</span></p>
                                            </div>

                                            <div class="form-group">
                                                <p class="border-bottom border-light text-center font-medium-1 text-bold-600" id="total_price">Total Product(s) Value: Rs <span>0</span></p>
                                            </div>

                                            {{--<div class="form-group">
                                                <div class="form-group text-center p-1 border border-light rounded">
                                                    <label class="d-block">Type of Package</label>
                                                    <input type="checkbox" name="package_type" class="switch hidden package_type" id="package_type" checked="checked" data-off-label="Partial" data-on-label="Complete">
                                                </div>
                                            </div>--}}
                                        </div>

                                        <div class="form-group">
                                            <textarea name="special_instructions" class="form-control" placeholder="Special Instructions" data-rule-maxlength="190" data-msg-maxlength="Special Instructions can be maximum 190 characters" rows="5"></textarea>
                                        </div>
                                    </div>

                                    <div id="shipping_header_div" class="col col_custom">
                                        <h4 id="shipping_header_info" class="form-section mb-2 text-center">Shipping Information</h4>

                                        <div class="form-group input-group mb-0">
                                            <input type="text" name="estimated_weight" id="estimated_weight" class="form-control weight" placeholder="Estimated Weight*" data-rule-required="true" data-msg-required="Estimated Weight is required">

                                            <div class="input-group-append">
                                                <span class="input-group-text">kg</span>
                                            </div>
                                        </div>

                                        <h6 class="form-text mb-1 text-justify text-muted text-italic">*Charges will be subjected to the Final Weight measured at the time of Shipment Arrival.</h6>

                                        <div class="form-group">
                                            <select name="shipping_mode" class="select2" id="shipping_mode" data-rule-required="true" data-msg-required="Mode of Shipping is required">
                                            </select>
                                        </div>

                                        <div id="charges_mode_div" class="form-group">
                                            <select name="charges_mode" class="select2" id="charges_mode" data-rule-required="true" data-msg-required="Charges Mode is required">
                                                @foreach($charges_modes as $charges_mode)
                                                    @if ($charges_mode->id == 3)
                                                        <option value="{{ $charges_mode->id }}" selected="selected">{{ $charges_mode->charges_mode }}</option>
                                                    @else
                                                        <option value="{{ $charges_mode->id }}">{{ $charges_mode->charges_mode }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div id="payment_info" class="col col_custom">
                                        <h4 class="form-section mb-2 text-center">Payment Information</h4>

                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rs</span>
                                            </div>

                                            <input type="text" name="amount" id="amount" class="form-control rounded-right amount" placeholder="Collection Amount*" data-rule-required="true" data-msg-required="Collection Amount is required">
                                        </div>
                                        <div class="form-group input-group" id="try_and_buy_charges_div">
                                            <input type="text" name="try_and_buy_charges" id="try_and_buy_charges" class="form-control amount" placeholder="Try & Buy Charges*" data-rule-required="true" data-msg-required="Charges field is required" value="">
                                        </div>

                                        <div class="form-group">
                                            <select name="payment_mode" class="select2" id="payment_mode" data-rule-required="true" data-msg-required="Mode of Payment is required">
                                                @foreach($payment_modes as $payment_mode)
                                                    <option value="{{ $payment_mode->id }}">{{ $payment_mode->mode }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div id="shipper_references" class="col">
                                    <h4 class="form-section mb-2 text-center">Shipper References (Optional)</h4>
                                    <div class="row">
                                        <div class="form-group col">
                                            <input name="shipper_reference_1" class="form-control shipper_reference" placeholder="Shipper Reference 1" data-rule-maxlength="190" data-msg-maxlength="Shipper Reference 1 can be maximum 190 characters">
                                        </div>
                                        <div class="form-group col">
                                            <input name="shipper_reference_2" class="form-control shipper_reference" placeholder="Shipper Reference 2" data-rule-maxlength="190" data-msg-maxlength="Shipper Reference 2 can be maximum 190 characters">
                                        </div>
                                        <div class="form-group col">
                                            <input name="shipper_reference_3" class="form-control shipper_reference" placeholder="Shipper Reference 3" data-rule-maxlength="190" data-msg-maxlength="Shipper Reference 3 can be maximum 190 characters">
                                        </div>
                                        <div class="form-group col">
                                            <input name="shipper_reference_4" class="form-control shipper_reference" placeholder="Shipper Reference 4" data-rule-maxlength="190" data-msg-maxlength="Shipper Reference 4 can be maximum 190 characters">
                                        </div>
                                        <div class="form-group col">
                                            <input name="shipper_reference_5" class="form-control shipper_reference" placeholder="Shipper Reference 5" data-rule-maxlength="190" data-msg-maxlength="Shipper Reference 5 can be maximum 190 characters">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col">
                                        <div class="form-group text-center">
                                            <button type="submit" name="book" class="btn btn-primary submission" value="Book">Book</button>
                                            <button type="submit" name="book_and_print" class="btn btn-primary ml-1 submission" value="Book & Print">Book & Print</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="select_service_type" role="dialog" aria-labelledby="select_service_type_title" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <form class="form-horizontal">
                                {{ csrf_field() }}

                                <div class="modal-header">
                                    <h4 class="modal-title" id="select_service_type_title">Select Service Type</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="control-group">
                                        <div class="controls">
                                            <select name="service_type" class="select2" id="service_type">
                                                @foreach($booking_types as $booking_type)
                                                    <option value="{{ $booking_type->id }}">{{ $booking_type->booking_type }}</option>
                                                @endforeach
                                            </select>

                                            <label id="service_type-error" class="danger d-none">Service Type is required.</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary mx-auto">Select</button>
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
    <script src="{{asset('app-assets/js/scripts/tooltip/tooltip.js')}}" type="text/javascript"></script>

    <script>


        $(document).ready(function() {

            var order_date = $('#order_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#from_date_root').css('top','40px');
                }
            });
            $(this).find('.pieces').TouchSpin({
                min: 1,
                max: 10,
                buttondown_class: 'btn btn-primary rounded-left',
                buttonup_class: 'btn btn-primary rounded-right',
                buttondown_txt: '<i class="ft-minus"></i>',
                buttonup_txt: '<i class="ft-plus"></i>'
            }).bind('input change', function() {
                if ($(this).hasClass('danger')) {
                    $(this).valid();
                }
            });

            @if (session('print'))
            $.ajax({
                url: '{!! route('cod.shipment.book.print_air_waybill') !!}',
                method: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'ids[]': '{{ session('print') }}'
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

            function shipping_mode_same_day(pickup_city, consignee_city) {
                if (pickup_city != consignee_city) {
                    if ($('#shipping_mode').val() == 4) {
                        $('#shipping_mode').val(null).trigger('change');

                        $('#shipping_same-day').addClass('d-none');
                    }

                    $('#shipping_mode option[value="4"]').attr('disabled', 'disabled');
                }
                else {
                    $('#shipping_mode option[value="4"]').removeAttr('disabled');
                }

                $('#shipping_mode').select2('destroy').select2({
                    width: '100%',
                    placeholder: 'Mode of Shipping*'
                }).bind('change', function() {
                    if ($(this).hasClass('danger')) {
                        $(this).valid();
                    }
                })
            }

            function try_and_buy_product_numbering() {
                setTimeout(function () {
                    $('#try_and_buy .repeater div .product').each(function(index) {
                        $(this).children('div').children('h4').children('span').html((index + 1));
                    });
                }, 500);
            }

            function try_and_buy_total_quantity() {
                var total_quantity = 0;

                $('#try_and_buy .repeater div .product .quantity').each(function(index) {
                    if (this.value != '') {
                        total_quantity += parseInt(this.value);

                        $('#try_and_buy #total_quantity span').html(total_quantity);
                    }
                });
            }

            function try_and_buy_total_price() {
                var total_price = 0;

                $('#try_and_buy .repeater div .product .price').each(function(index) {
                    if (this.value != '') {
                        total_price += parseInt(this.value.replace(',', ''));

                        $('#try_and_buy #total_price span').html(total_price.toLocaleString());
                    }
                });
            }

            $('#delivery_type, #consignee_city').change(function () {
                if($('#delivery_type').val() == 2){
                    $('#consignee_address').prop('disabled', true);
                }
                else{
                    $('#consignee_address').prop('disabled', false);
                }
            });

            $('#charges_mode').select2({
                width: '100%',
                placeholder: 'Charges Mode*'
            }).bind('change', function() {
                if ($(this).hasClass('danger')) {
                    $(this).valid();
                }
            });

            $('#amount').bind('keypress', function () {
                $('#booking_form .submission').attr('disabled', true);
            });

            function shipping_modes() {
                if ($('#pickup_address').val() == 0) {
                    var pickup_city_id = $('#new_pickup_city').val();
                    $('#pickup_city_name').addClass('d-none');
                }
                else {
                    var pickup_city_id = $('#pickup_address').find(':selected').data('city-id');
                    var pickup_city_name = $('#pickup_address').find(':selected').data('city-name');
                    $('#pickup_city_name').removeClass('d-none');
                    $('#pickup_city_name').html('City : ' + pickup_city_name);
                }

                consignee_city_id = $('#consignee_city').val();

                if (consignee_city_id) {
                    $.ajax({
                        url: '{!! route('cod.shipment.book.corporate_shipping_modes') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'service_type_id': 1,
                            'pickup_city_id': pickup_city_id,
                            'consignee_city_id': consignee_city_id
                        }
                    })
                        .done(function(data) {
                            $('#shipping_mode').html('').select2('destroy');

                            default_shipping_mode = false;

                            if (data.status == 0) {
                                $.each(data.shipping_modes, function (index, shipping_mode) {
                                    if(data.default_shipping_mode === shipping_mode['id']) {
                                        $('#shipping_mode').append('<option value="' + shipping_mode['id'] + '" selected>' + shipping_mode['mode'] + '</option>');

                                        default_shipping_mode = true;
                                    }
                                    else{
                                        $('#shipping_mode').append('<option value="' + shipping_mode['id'] + '">' + shipping_mode['mode'] + '</option>');
                                    }
                                });

                                present = true;
                            }
                            else {
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                                present = false;
                            }
                            if(default_shipping_mode === false) {
                                $('#shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
                                    width: '100%',
                                    placeholder: 'Mode of Shipping*'
                                }).bind('change', function () {
                                    if ($(this).hasClass('danger')) {
                                        $(this).valid();
                                    }

                                    if (this.value == 4) {
                                        $('#shipping_same-day').removeClass('d-none');
                                    } else {
                                        $('#shipping_same-day').addClass('d-none');
                                    }
                                });
                            }
                            else{
                                $('#shipping_mode').select2({
                                    width: '100%',
                                    placeholder: 'Mode of Shipping*'
                                }).bind('change', function () {
                                    if ($(this).hasClass('danger')) {
                                        $(this).valid();
                                    }

                                    if (this.value == 4) {
                                        $('#shipping_same-day').removeClass('d-none');
                                    } else {
                                        $('#shipping_same-day').addClass('d-none');
                                    }
                                });
                            }

                            if (present) {
                                $('#shipping_mode').prop('disabled', false);
                            }
                            else {
                                $('#shipping_mode').prop('disabled', true);
                            }
                        });
                }
            }

            $('#select_service_type').modal({
                backdrop: 'static',
                keyboard: false,
                show: false
            });

            $('#select_service_type form #service_type').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Service Type*'
            });

            var service_type = '';

            @if (!Session::has('service_type_id'))
            $('#select_service_type').modal('show');
            @else
                service_type = '{{ Session::get('service_type_id') }}';

            if(service_type == 1){
                $('#pieces_quantity').removeClass('d-none');
            }
            if (service_type == 2) {
                $('#replacement').removeClass('d-none');
                $('#try_and_buy_charges_div').addClass('d-none');
            }
            if (service_type == 3) {
                $('#regular').addClass('d-none');
                $('#try_and_buy').removeClass('d-none');
                $('#try_and_buy_charges_div').removeClass('d-none');
                $('#amount').prop('disabled', true);
            }
            else{
                $('#amount').prop('disabled', false);
                $('#try_and_buy_charges_div').addClass('d-none');
            }

            $('#select_service_type form #service_type').val(service_type).trigger('change');
            @endif
            var open_delivery_status = @json($user_delivery_types);
            $('#select_service_type form').bind('submit', function(e) {
                e.preventDefault();

                var selected = $('#select_service_type form #service_type').find(':selected');

                service_type = selected.val();

                if (service_type !== '' && service_type !== undefined && service_type !== null) {
                    $('#select_service_type form #service_type-error').addClass('d-none');

                    if (service_type == 1) {
                        $('#shipping_header_div').removeClass('col col_6');
                        $('#order_information_header_div').removeClass('col col_6');
                        $('#consignee_header_div').removeClass('col col_6');
                        $('#shipping_header_div').addClass('col col_custom');
                        $('#order_information_header_div').addClass('col col_custom_middle');
                        $('#consignee_header_div').addClass('col col_custom');
                        $('#regular').removeClass('d-none');
                        $('#payment_info').removeClass('d-none');
                        $('#charges_mode_div').removeClass('d-none');
                        $('#info_display').removeClass('d-none');
                        $('#delivery_type_div').removeClass('d-none');
                        $('#replacement').addClass('d-none');
                        $('#try_and_buy').addClass('d-none');
                        $('#try_and_buy_charges_div').addClass('d-none');
                        $('#order_header_info').removeClass('mt-2');
                        $('#shipping_header_info').removeClass('mt-2');
                        $('#shipper_header_info').html('Shipper Information');
                        $('#consignee_header_info').html('Consignee Information');
                        $('#amount').prop('disabled', false);
                        $('#pieces_quantity').removeClass('d-none');
                        $('#self_collection_div').removeClass('d-none');
                    }
                    else if (service_type == 2) {
                        $('#shipping_header_div').removeClass('col col_6');
                        $('#order_information_header_div').removeClass('col col_6');
                        $('#consignee_header_div').removeClass('col col_6');
                        $('#shipping_header_div').addClass('col col_custom');
                        $('#order_information_header_div').addClass('col col_custom_middle');
                        $('#consignee_header_div').addClass('col col_custom');
                        $('#regular').removeClass('d-none');
                        $('#payment_info').removeClass('d-none');
                        $('#charges_mode_div').removeClass('d-none');
                        $('#info_display').removeClass('d-none');
                        $('#replacement').removeClass('d-none');
                        $('#try_and_buy').addClass('d-none');
                        $('#delivery_type_div').removeClass('d-none');
                        $('#order_header_info').removeClass('mt-2');
                        $('#shipping_header_info').removeClass('mt-2');
                        $('#shipper_header_info').html('Shipper Information');
                        $('#amount').prop('disabled', false);
                        $('#try_and_buy_charges_div').addClass('d-none');
                        $('#consignee_header_info').html('Consignee Information');
                        $('#self_collection_div').addClass('d-none');
                    }
                    else if (service_type == 3) {
                        $('#shipping_header_div').removeClass('col col_6');
                        $('#order_information_header_div').removeClass('col col_6');
                        $('#consignee_header_div').removeClass('col col_6');
                        $('#shipping_header_div').addClass('col col_custom');
                        $('#order_information_header_div').addClass('col col_custom_middle');
                        $('#consignee_header_div').addClass('col col_custom');
                        $('#regular').addClass('d-none');
                        $('#replacement').addClass('d-none');
                        $('#payment_info').removeClass('d-none');
                        $('#charges_mode_div').removeClass('d-none');
                        $('#info_display').removeClass('d-none');
                        $('#delivery_type_div').removeClass('d-none');
                        $('#try_and_buy').removeClass('d-none');
                        $('#order_header_info').removeClass('mt-2');
                        $('#shipping_header_info').removeClass('mt-2');
                        $('#shipper_header_info').html('Shipper Information');
                        $('#amount').prop('disabled', true);
                        $('#try_and_buy_charges_div').removeClass('d-none');
                        $('#consignee_header_info').html('Consignee Information');
                        $('#self_collection_div').addClass('d-none');
                    }
                    else if (service_type == 5) {
                        $('#shipping_header_div').removeClass('col col_custom');
                        $('#order_information_header_div').removeClass('col col_custom_middle');
                        $('#consignee_header_div').removeClass('col col_custom');
                        $('#shipping_header_div').addClass('col col_6');
                        $('#order_information_header_div').addClass('col col_6');
                        $('#consignee_header_div').addClass('col col_6');
                        $('#regular').removeClass('d-none');
                        $('#replacement').addClass('d-none');
                        $('#try_and_buy').addClass('d-none');
                        $('#order_header_info').addClass('mt-2');
                        $('#shipping_header_info').addClass('mt-2');
                        $('#payment_info').addClass('d-none');
                        $('#charges_mode_div').addClass('d-none');
                        $('#delivery_type_div').addClass('d-none');
                        $('#info_display').addClass('d-none');
                        $('#shipper_header_info').html('Shipper Information<br><h6>(Delivery Address)</h6>');
                        $('#consignee_header_info').html('Consignee Information<br><h6>(Pickup/Collection Address)</h6>');
                        $('#amount').prop('disabled', false);
                        $('#try_and_buy_charges_div').addClass('d-none');
                        $('#self_collection_div').addClass('d-none');
                        var consignee_email = $('input[name="consignee_email_address"]');
                        consignee_email.attr('data-toggle', 'tooltip');
                        consignee_email.attr('data-placement', 'top');
                        consignee_email.attr('data-original-title', 'Please add email address so that we can sent address label to your customer.');
                        consignee_email.tooltip('show');
                    }
                    $('#booking_form #selected_service_type').val(service_type);

                    $('#selected_service_type_name').html('(' + selected.html() + ')');

                    $('#select_service_type').modal('hide');

                    shipping_modes();
                }
                else {
                    $('#select_service_type form #service_type-error').removeClass('d-none');
                }
            });

            $('#delivery_type').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Delivery Type*'
            });

            @if (!$default_pickup_address)
            $('#pickup_address').prepend('<option value="" selected="selected"></option>');
            @endif

            $('#pickup_address').select2({
                width: '100%',
                placeholder: 'Pickup Address*'
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
                var consignee_city = $('#consignee_city').val();

                shipping_mode_same_day(pickup_city, consignee_city);
            });

            $('#new_pickup_city').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'City*'
            }).bind('change', function() {
                $(this).valid();

                shipping_modes();

                var pickup_city = $(this).val();
                var consignee_city = $('#consignee_city').val();

                shipping_mode_same_day(pickup_city, consignee_city);
            });

            $("#consignee_info").select2({
                width:'100%',
                placeholder: "Search Here...",
                minimumInputLength: 5,
                ajax: {
                    url: '{{ route('cod.shipment.book.get_consignee_infos') }}',
                    dataType: 'json',
                    type: "GET",
                    quietMillis: 50,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page,
                            'shipper': '{{session('user_id')}}'
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;

                        return {
                            results: data.data,
                            pagination: {
                                more: (params.page * 30) < data.total_count
                            }
                        };
                    },
                    cache: true
                },
                escapeMarkup: function (markup) { return markup; },
                templateResult: formatRepo,
                templateSelection: formatRepoSelection

            });
            function formatRepo (repo) {
                if (repo.loading) return repo.text;
                var markup = "<option value='" + repo.id + "'>"+ repo.full_name +"</option>";

                return markup;
            }
            function formatRepoSelection (repo) {
                return repo.full_name || repo.text;
            }

            var blacklist = false;
            var blacklist_message = '';
            var blacklist_color = '';
            function check_consignee_return_ratio(){
                var phone = $('input[name="consignee_phone_number_1"]').val();
                if(phone){
                    $.ajax({
                        url:'{!! route('cod.shipment.book.check_consignee_return_ratio') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'phone': phone,
                        }
                    }).done(function (data) {
                        if(data.status == 0){
                            blacklist = true;
                            blacklist_message = data.message;
                            blacklist_color = data.color;
                            return true;
                        }
                    });
                }
            }

            $('#consignee_info').on('select2:select', function () {
                var id = parseInt($(this).val());
                if(id){
                    $.ajax({
                        url:'{!! route('cod.shipment.book.get_consignee_info') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'id': id,
                        }
                    }).done(function (data) {
                        if(data.status){
                            $('#consignee_city').val(data.details.city_id).trigger('change');
                            $('input[name="consignee_name"]').val(data.details.name);
                            $('#consignee_address').val(data.details.address);
                            $('input[name="consignee_phone_number_1"]').val(data.details.phone_number_1).change();
                            $('input[name="consignee_phone_number_2"]').val(data.details.phone_number_2);
                            $('input[name="consignee_email_address"]').val(data.details.email);
                        }else{
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    });
                }
            });

            $('input[name="consignee_phone_number_1"]').bind('change paste keyup', function () {
                if($(this).val().match(/\d/g) != null){
                    var length = $(this).val().match(/\d/g).length;
                }
                else{
                    var length = 0;
                }
                if(length == 11){
                    check_consignee_return_ratio();
                }
            });

            $('#information_display').checkboxpicker();
            $('#self_collection').checkboxpicker();

            $('#consignee_city').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'City*'
            }).bind('change', function() {
                $(this).valid();

                shipping_modes();

                if ($('#pickup_address').val() == 0) {
                    var pickup_city = $('#new_pickup_city').val();
                }
                else {
                    var pickup_city = $('#pickup_address').find(':selected').data('city-id');
                }

                var consignee_city = $(this).val();

                shipping_mode_same_day(pickup_city, consignee_city);
            });

            $('#product_type').select2({
                width: '100%',
                placeholder: 'Product Type*'
            }).bind('change', function() {
                $(this).valid();
            });

            $('#regular .insurance').checkboxpicker().bind('change', function() {
                var parent = $(this).parent('.form-group').next('.form-group');

                if (this.checked) {
                    parent.removeClass('d-none');
                }
                else {
                    parent.addClass('d-none');

                    parent.children('#item_price-error').remove();
                }
            });
            $('#try_and_buy .insurance').checkboxpicker();

            // $('#package_type').checkboxpicker();
            var current_date = '{{$date}}';
            $('#replacement_product_type').select2({
                width: '100%',
                placeholder: 'Product Type*'
            }).bind('change', function() {
                $(this).valid();
            });

            $('#try_and_buy .select2').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Product Type*'
            }).bind('change', function() {
                $(this).valid();
            });
            var repeater_limit = 5;
            var repeater_count = 1;
            $('#try_and_buy .repeater').repeater({
                isFirstItemUndeletable: true,
                show: function() {
                    $(this).find('.select2-container--default').remove();

                    $(this).find('.select2').prepend('<option value="" selected="selected"></option>').select2({
                        width: '100%',
                        placeholder: 'Product Type*'
                    }).bind('change', function() {
                        $(this).valid();
                    });

                    $(this).slideDown();

                    $('html, body').animate({
                        scrollTop: ($(this).offset().top - $('.header-navbar').height())
                    }, 1000);

                    $(this).find('.quantity').TouchSpin({
                        min: 1,
                        max: 10000,
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

                    $(this).find('.price').inputmask({
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

                    var insurance = $(this).find('.insurance');

                    insurance.parent('.form-group').children('.btn-group').remove();

                    insurance.checkboxpicker();

                    if(repeater_count === repeater_limit){

                        $("#try_and_buy_add").hide("slow");
                    }

                    try_and_buy_product_numbering();
                },
                hide: function(delete_element) {
                    var id = $(this).children('div').children('h4').children('span').html();

                    swal({
                        title: 'Are you sure?',
                        text: 'You want to delete Product #' + id + '?',
                        icon: 'warning',
                        buttons: {
                            cancel: {
                                text: 'Close',
                                value: null,
                                visible: true,
                                closeModal: true,
                            },
                            confirm: {
                                text: 'Delete',
                                value: true,
                                visible: true,
                                closeModal: true
                            }
                        },
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        dangerMode: true
                    }).then(function(confirm) {
                        if (confirm) {
                            repeater_count--;
                            if(repeater_count < repeater_limit){
                                $("#try_and_buy_add").show("slow");
                            }
                            $(this).slideUp(delete_element);

                            try_and_buy_product_numbering();
                        }
                    });
                }
            });

            $('#amount, #consignee_city, #pickup_address').change(function(){
                $('#span').remove();
                $('#booking_form .submission').attr('disabled', true);
                if ($('#pickup_address').val() == 0) {
                    var pickup_city_id = $('#new_pickup_city').val();
                }
                else {
                    var pickup_city_id = $('#pickup_address').find(':selected').data('city-id');
                }

                $.ajax({
                    url:'{!! route('cod.shipment.book.check_cod_cap_zone_classes') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'amount': $('#amount').val(),
                        'pickup_city': pickup_city_id,
                        'consignee_city': $('#consignee_city').val(),
                    }
                }).done(function (data) {
                    if(data.status === 1){
                        $('#span').remove();
                        var span = '<span id="span" style="color: red">'+data.error+'</span>';
                        $('#amount').val('');
                        $('#amount').parent('div').append(span);

                    }
                    if(data.status === 2) {
                        $('#span').remove();
                        $('#booking_form .submission').attr('disabled', false);
                    }
                    if(data.status === 0) {
                        $('#span').remove();
                        var span = '<span id="span" style="color: red">'+data.error+'</span>';
                        $('#amount').val('');
                        $('#amount').parent('div').append(span);
                    }
                    if(data.status === 3) {
                        $('#span').remove();
                        if ($('#pickup_address').val() == "") {
                            var span = '<span id="span" style="color: red">Pickup address is required</span>';
                            $('#pickup_address').parent('div').append(span);
                        }
                        else{
                            var span = '<span id="span" style="color: red">'+data.error+'</span>';
                            $('#new_pickup_city').parent('div').append(span);
                        }
                        $('#amount').val('');
                    }
                    if(data.status === 4) {
                        $('#span').remove();
                        var span = '<span id="span" style="color: red">'+data.error+'</span>';
                        $('#consignee_city').parent('div').append(span);
                        $('#amount').val('');
                    }
                });
            });

            $('#shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Mode of Shipping*',
                disabled: true,
            }).bind('change', function() {
                if ($(this).hasClass('danger')) {
                    $(this).valid();
                }

                if (this.value == 4) {
                    $('#shipping_same-day').removeClass('d-none');
                }
                else {
                    $('#shipping_same-day').addClass('d-none');
                }
                var index = $.inArray(parseInt(this.value), open_delivery_status);

                if(index == -1){
                    $("#delivery_type").select2('destroy');
                    $("#delivery_type").find('option[value="2"]').attr('disabled', true);
                    $("#delivery_type").prepend('<option value="" selected="selected"></option>').select2({
                        width: '100%',
                        placeholder: 'Delivery Type*'
                    });
                }else{
                    if ($('#delivery_type').hasClass("select2-hidden-accessible")) {
                        $("#delivery_type").select2('destroy');
                        $("#delivery_type").find('option[value="2"]').attr('disabled', false);
                        $("#delivery_type").prepend('<option value="" selected="selected"></option>').select2({
                            width: '100%',
                            placeholder: 'Delivery Type*'
                        });
                    }
                }
            });

            $('#same-day_timing').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Same-day Timing*'
            }).bind('change', function() {
                $(this).valid();
            });

            $('#payment_mode').select2({
                width: '100%',
                placeholder: 'Mode of Payment*'
            }).bind('change', function() {
                $(this).valid();
            });

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
                    check_consignee_return_ratio();
                    var pressed_button = $(this.submitButton);

                    $(form).append('<input type="hidden" name="' + pressed_button.attr('name') + '" value="' + pressed_button.attr('value') + '">');

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
                    $.ajax({
                        url: '{!! route('cod.shipment.book.corporate_min_chargeable_weight') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'shipping_mode': $('#shipping_mode').val(),
                            'delivery_type': $('#delivery_type').val(),
                            'estimated_weight': $('#estimated_weight').val()
                        }
                    }).done(function(data) {
                        if(data.status == 1){
                            swal({
                                title: 'Warning',
                                text: 'Dear Customer, this shipment will be charged to a minimum of ' + data.min + ' kg, based on your mode of shipping and delivery type',
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
                                if(present.length > 0){
                                    var html = '<div class="text-left">In case of,<br/>';
                                    html += '<b>Out of Service Area:</b> Additional charges may apply.</br>';
                                    html += '<b>Non Service Area:</b> Shipment may be returned.</br>';
                                    html += '<b>For assistance, Call:</b> 021-38772222</br></div>';
                                    content = document.createElement('div');
                                    content.innerHTML = html;
                                    swal({
                                        title: 'A Possible Address Anomaly: ' + present + ' Detected!',
                                        content: content,
                                        icon: 'info',
                                        buttons: {
                                            cancel: {
                                                text: 'Cancel',
                                                value: null,
                                                visible: true,
                                                closeModal: true,
                                            },
                                            confirm: {
                                                text: 'Continue to Booking',
                                                value: true,
                                                visible: true,
                                                closeModal: true
                                            }
                                        },
                                        closeOnClickOutside: false,
                                        closeOnEsc: false,
                                        // dangerMode: true
                                    }).then(function(confirm) {
                                        if(confirm) {
                                            if(blacklist == true){
                                                var html = '<div class="row justify-content-center p-1" style="background-color: '+ blacklist_color +'; color:white;">'+ blacklist_message +'</div>';
                                                content = document.createElement('div');
                                                content.innerHTML = html;
                                                swal({
                                                    content: content,
                                                    buttons: {
                                                        cancel: {
                                                            text: 'Cancel',
                                                            value: null,
                                                            visible: true,
                                                            closeModal: true,
                                                        },
                                                        confirm: {
                                                            text: 'Book Anyway',
                                                            value: true,
                                                            visible: true,
                                                            closeModal: true
                                                        }
                                                    },
                                                    closeOnClickOutside: false,
                                                    closeOnEsc: false,
                                                    // dangerMode: true
                                                }).then(function(confirm) {
                                                    if (confirm) {
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
                                                    else{
                                                        $(form).find('button[type=submit]').prop('disabled', false);
                                                    }
                                                });
                                            }else{
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
                                        else{
                                            $(form).find('button[type=submit]').prop('disabled', false);
                                        }
                                    });
                                }
                                else {
                                    if(blacklist == true) {
                                        var html = '<div class="row justify-content-center p-1" style="background-color: '+ blacklist_color +'; color:white;">' + blacklist_message + '</div>';
                                        content = document.createElement('div');
                                        content.innerHTML = html;
                                        swal({
                                            content: content,
                                            buttons: {
                                                cancel: {
                                                    text: 'Cancel',
                                                    value: null,
                                                    visible: true,
                                                    closeModal: true,
                                                },
                                                confirm: {
                                                    text: 'Book Anyway',
                                                    value: true,
                                                    visible: true,
                                                    closeModal: true
                                                }
                                            },
                                            closeOnClickOutside: false,
                                            closeOnEsc: false,
                                            // dangerMode: true
                                        }).then(function (confirm) {
                                            if (confirm) {
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
                                            else{
                                                $(form).find('button[type=submit]').prop('disabled', false);
                                            }
                                        });
                                    }else{
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
                        }
                        else {
                            if(present.length > 0){
                                var html = '<div class="text-left">In case of,<br/>';
                                html += '<b>Out of Service Area:</b> Additional charges may apply.</br>';
                                html += '<b>Non Service Area:</b> Shipment may be returned.</br>';
                                html += '<b>For assistance, Call:</b> 021-38772222</br></div>';
                                content = document.createElement('div');
                                content.innerHTML = html;
                                swal({
                                    title: present + ' Detected!',
                                    content: content,
                                    icon: 'info',
                                    buttons: {
                                        cancel: {
                                            text: 'No',
                                            value: null,
                                            visible: true,
                                            closeModal: true,
                                        },
                                        confirm: {
                                            text: 'Yes',
                                            value: true,
                                            visible: true,
                                            closeModal: true
                                        }
                                    },
                                    closeOnClickOutside: false,
                                    closeOnEsc: false,
                                    // dangerMode: true
                                }).then(function(confirm) {
                                    if(confirm){
                                        if(blacklist == true){
                                            var html = '<div class="row justify-content-center p-1" style="background-color: '+ blacklist_color +'; color:white;">'+ blacklist_message +'</div>';
                                            content = document.createElement('div');
                                            content.innerHTML = html;
                                            swal({
                                                content: content,
                                                buttons: {
                                                    cancel: {
                                                        text: 'Cancel',
                                                        value: null,
                                                        visible: true,
                                                        closeModal: true,
                                                    },
                                                    confirm: {
                                                        text: 'Book Anyway',
                                                        value: true,
                                                        visible: true,
                                                        closeModal: true
                                                    }
                                                },
                                                closeOnClickOutside: false,
                                                closeOnEsc: false,
                                                // dangerMode: true
                                            }).then(function(confirm) {
                                                if (confirm) {
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
                                                else{
                                                    $(form).find('button[type=submit]').prop('disabled', false);
                                                }
                                            });
                                        }else{
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
                            }
                            else {
                                if(blacklist == true){
                                    var html = '<div class="row justify-content-center p-1" style="background-color: '+ blacklist_color +'; color:white;">'+ blacklist_message +'</div>';
                                    content = document.createElement('div');
                                    content.innerHTML = html;
                                    swal({
                                        content: content,
                                        buttons: {
                                            cancel: {
                                                text: 'Cancel',
                                                value: null,
                                                visible: true,
                                                closeModal: true,
                                            },
                                            confirm: {
                                                text: 'Book Anyway',
                                                value: true,
                                                visible: true,
                                                closeModal: true
                                            }
                                        },
                                        closeOnClickOutside: false,
                                        closeOnEsc: false,
                                        // dangerMode: true
                                    }).then(function(confirm) {
                                        if (confirm) {
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
                                        else{
                                            $(form).find('button[type=submit]').prop('disabled', false);
                                        }
                                    });
                                }else{
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
                        }
                    });

                }
            });

            $('.phone_number').inputmask({
                'mask': ['9999-9999999', '+99-999-9999999', '999-9999999'],
                'clearIncomplete': true
            });

            $('.quantity').TouchSpin({
                min: 1,
                max: 10000,
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
                'max': 100000
            });

            $('.amount').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('.order_id').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'min': 0,
                'max': 1000000000000
            });
        });
    </script>
@endsection