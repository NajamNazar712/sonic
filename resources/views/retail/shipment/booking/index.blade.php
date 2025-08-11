@extends('retail.layout.master')

@section('title', 'Booking Form')

@section('content')
    <style>
        /* Custom bigger modal size */
        .modal-xl-custom {
            max-width: 95% !important; /* or any width you like */
        }
    </style>
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <h1 class="mb-1">
                    Retail-Booking Screen
                </h1>
            </div>
            <div class="card">
                <div class="card-content" aria-expanded="true">
                    <div class="card-body">
                        @include('retail.inc.messages')
                        <div class="alert bg-info" id="consignee_address_error" style="display: none">
                        </div>
                        <form id="booking_form" class="form-horizontal" method="POST" action="{{ route('retail.shipment.book.store') }}" novalidate="novalidate" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="row">
                                <div id="consignment_info" class="col-3 border">
                                    <h4 id="shipper_header_info" class="form-section mb-2 text-center">Consignment Info</h4>
                                    <!-- <div class="form-group">
                                        <select name="parent_product_id" id="parent_product_id" class="select2 form-control" data-rule-required="true" data-msg-required="Parent Product is required">
                                            @foreach($parent_products as $product)
                                                <option value="{{$product->id}}">{{$product->name}}</option>
                                            @endforeach
                                        </select>
                                    </div> -->
                                    <div class="form-group">
                                        <select name="product" id="product" class="select2 form-control" data-rule-required="true" data-msg-required="Shipment is required">
                                            @foreach($products as $product)
                                                <option value="{{$product->id}}">{{$product->product_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <select name="business_category" id="business_category" class="select2 form-control" data-rule-required="true" data-msg-required="Shipment Category is required">
                                            @foreach($business_categories as $business_category)
                                                <option value="{{$business_category->id}}">{{$business_category->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <select name="shipping_mode" id="shipping_mode" class="select2 form-control" data-rule-required="true" data-msg-required="Product is required">
                                            @foreach($shipping_modes as $shipping_mode)
                                                <option value="{{$shipping_mode->id}}">{{$shipping_mode->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <select name="ref" id="ref" class="select2 form-control" data-rule-required="true" data-msg-required="Reference is required">
                                            @foreach($refs as $ref)
                                                <option value="{{$ref}}">{{$ref}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="d-none" id="other_ref">
                                        <div class="form-group">
                                            <input type="text" name="ref_name" id="ref_name" class="form-control" placeholder="New Reference" data-rule-required="true" data-msg-required="Reference is required">
                                        </div>
                                    </div>
                                    <div class="form-group d-none" id="domestic_destination_div">
                                        <select name="domestic_destination" id="domestic_destination" class="select2 form-control destination" data-rule-required="true" data-msg-required="Destination is required">
                                            @foreach($domestic_cities as $domestic_city)
                                                <option value="{{$domestic_city->id}}">{{$domestic_city->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group d-none" id="domestic_overland_destination_div">
                                        <select name="domestic_overland_destination" id="domestic_overland_destination" class="select2 form-control destination" data-rule-required="true" data-msg-required="Destination is required">
                                            @foreach($domestic_overland_cities as $domestic_overland_city)
                                                <option value="{{$domestic_overland_city->id}}">{{$domestic_overland_city->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group d-none" id="international_destination_div">
                                        <select name="international_destination" id="international_destination" class="select2 form-control destination" data-rule-required="true" data-msg-required="Destination is required">
                                            @foreach($international_cities as $international_city)
                                                <option value="{{$international_city->id}}">{{$international_city->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group text-center">
                                        <label class="mr-1">Volumetric Weight</label>
                                        <input type="checkbox" name="volumetric_weight" class="switch hidden volumetric_weight" data-group-cls="btn-group-sm">
                                    </div>
                                    <div class="form-group volumetric_weights">
                                        <input type="text" name="length" id="length" class="form-control form-control-sm length" placeholder="Length (cm)*" data-rule-required="true" data-msg-required="Length is required" data-rule-range="[0.1,375]" data-msg-range="Length needs to be from 0.1 to 375" disabled="disabled">
                                    </div>
                                    <div class="form-group volumetric_weights">
                                        <input type="text" name="breadth" id="breadth" class="form-control form-control-sm breadth" placeholder="Breadth (cm)*" data-rule-required="true" data-msg-required="Breadth is required" data-rule-range="[0.1,375]" data-msg-range="Length needs to be from 0.1 to 375" disabled="disabled">
                                    </div>
                                    <div class="form-group volumetric_weights">
                                        <input type="text" name="height" id="height" class="form-control form-control-sm height" placeholder="Height (cm)*" data-rule-required="true" data-msg-required="Height is required" data-rule-range="[0.1,375]" data-msg-range="Length needs to be from 0.1 to 375" disabled="disabled">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="weight" id="weight" class="form-control weight" placeholder="Weight (kg)*" data-rule-required="true" data-msg-required="Weight is required" data-rule-range="[0.01,100000]" data-msg-range="Weight needs to be from 0.01 to 100000">
                                    </div>
{{--                                    <div class="form-group">--}}
{{--                                        <input name="discount" class="form-control discount" id="discount" placeholder="Discount" value="">--}}
{{--                                    </div>--}}
                                    <div class="form-group input-group">
                                        <input  type="text" name="pieces" id="pieces" class="form-control text-center pieces" placeholder="Pieces*" data-rule-required="true" data-msg-required="Pieces is required" data-toggle="tooltip" data-placement="top" title="" data-original-title="Here you enter the no. of individual flyers or boxes your shipment is separated into, so each can have it's own indentity slip and be accounted for.">
                                    </div>
                                    <div class="form-group">
                                        <select name="payment_mode" id="payment_mode" class="select2 form-control" data-rule-required="true" data-msg-required="Payment Mode is required">
                                            @foreach($payment_modes as $payment_mode)
                                                <option value="{{$payment_mode->id}}">{{$payment_mode->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <select name="charges_mode" id="charges_mode" class="select2 form-control" data-rule-required="true" data-msg-required="Charges Mode is required">
                                            @foreach($charges_modes as $charges_mode)
                                                <option value="{{$charges_mode->id}}">{{$charges_mode->charges_mode}}</option>
                                            @endforeach
                                        </select>
                                    </div>
{{--                                    <div class="form-group">--}}
{{--                                        <input name="payment_transaction_id" class="form-control number" id="payment_transaction_id" placeholder="Payment Transaction ID" value=""  data-rule-required="true" data-msg-required="Payment Transaction ID is required">--}}
{{--                                    </div>--}}
                                </div>
                                <div id="consignee_shipper_info" class="ml-1 col-6 border">
                                    <h4 id="shipper_header_info" class="form-section mb-2 text-center">Consignee & Shipper Info</h4>

                                    <div class="row ml-0">
                                        <div class="form-group col-6">
                                            <input type="text" name="shipper_phone_no" id="shipper_phone_no" class="form-control phone1" placeholder="Shipper Cell Number*" data-rule-required="true" data-msg-required="Shipper Cell Number is required">
                                        </div>

                                        <div class="form-group col-6">
                                            <input type="text" name="order_id" id="order_id" class="form-control" placeholder="Order ID">
                                        </div>
                                    </div>
                                    {{-- <div class="form-group col-6 previous-names">
                                        <select name="previous_name" id="previous_name" class="select2 form-control">
                                        </select>
                                    </div> --}}

                                    <div class="form-group col-6">
                                        <a href="javascript:void(0);" id="auto_fetch_shipper" class="btn btn-sm btn-outline-success sm" disabled="disabled">Auto Fetch</a>
                                    </div>

                                    <div class="form-group col-6">
                                        <input type="text" name="shipper_name" id="shipper_name" class="form-control shipper_name" placeholder="Shipper Name*" data-rule-required="true" data-msg-required="Shipper Name is required">
                                    </div>
                                    <div class="form-group col-6">
                                        <input type="text" name="shipper_cnic" id="shipper_cnic" class="form-control cnic" placeholder="Shipper CNIC" data-rule-required="true"data-msg-required="Shipper CNIC is required">
                                    </div>
                                    <div class="form-group col">
                                        <textarea name="shipper_address" class="form-control address" id="shipper_address" rows="2" placeholder="Shipper Address*" data-rule-required="true" data-msg-required="Shipper Address is required" data-rule-maxlength="255" data-msg-maxlength="Shipper Address can be maximum 255 characters"></textarea>
                                    </div>
                                    <div class="form-group col-6" id="wallet_id_div">
                                        <input type="hidden" name="wallet_id" id="wallet_id">
                                        <input type="hidden" name="complete_shipper_info" id="complete_shipper_info" value="0">
                                        <div class="form-group text-center p-1 border border-light rounded">
                                            <label class="d-block">Wallet Shipment</label>
                                            <input type="checkbox" name="is_wallet" class="switch hidden is_wallet" id="is_wallet" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group col-6">
                                        <input type="text" name="consignee_phone_no" id="consignee_phone_no" class="form-control" placeholder="Consignee Cell Number*" data-rule-required="true" data-msg-required="Consignee Cell Number is required">
                                    </div>
                                    <div class="form-group col-6">
                                        <a href="javascript:void(0);" id="auto_fetch" class="btn btn-sm btn-outline-success sm" disabled="disabled">Auto Fetch</a>
                                    </div>
                                    <div class="form-group col-6">
                                        <input type="text" name="consignee_name" id="consignee_name" class="form-control consignee_name" placeholder="Consignee Name*" data-rule-required="true" data-msg-required="Consignee Name is required">
                                    </div>
                                    <div class="form-group col-6">
                                        <input type="text" name="consignee_cnic" id="consignee_cnic" class="form-control cnic" placeholder="Consignee CNIC">
                                    </div>
                                    <div class="form-group col">
                                        {{-- <textarea name="consignee_address" id="consignee_address" class="form-control address" rows="2" placeholder="Consignee Address*" data-rule-required="true" data-msg-required="Consignee Address is required" data-rule-maxlength="255" data-msg-maxlength="Consignee Address can be maximum 255 characters"></textarea> --}}
                                        <textarea id="consignee_address" name="consignee_address" class="form-control" placeholder="Consignee Address*" onchange="bdmk()" rows="5" data-rule-required="true" data-msg-required="Consignee Address is required"></textarea>
                                    </div>
                                    <div class="col">
                                        <div class="row d-none" id="cod_check">
                                            <div class="form-group col-6">
                                                <input type="text" name="cod" id="cod" class="form-control rounded-right amount" placeholder="COD Amount*" data-rule-required="true" data-msg-required="COD Amount is required">
                                            </div>

                                            <div class="form-group col-3">
                                                <input type="text" name="wht" id="wht" class="form-control rounded-right wht" placeholder="WHT" readonly>
                                            </div>

                                            <div class="form-group col-3">
                                                <input type="text" name="cod_sst" id="cod_sst" class="form-control rounded-right cod_sst" placeholder="COD SST" readonly>
                                            </div>

                                        </div>
                                        
                                        <div class="row">
                                            <div class="form-group col-6">
                                                <select name="insurance_offered" id="insurance_offered" class="select2 form-control" data-rule-required="true" data-msg-required="Insurance Offered is required">
                                                    <option value="1">Yes</option>
                                                    <option value="0">No</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-6 d-none" id="insurance_amount_div">
                                                <input type="text" name="insurance_amount" id="insurance_amount" class="form-control decimal" placeholder="Insurance Amount*" data-rule-required="true" data-msg-required="Insurance Amount is required">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-6">
                                                <input type="text" name="packaging_amount" id="packaging_amount" class="form-control rounded-right amount" placeholder="Packaging Amount">
                                            </div>

                                            <div class="form-group col-6">
                                                <input type="text" name="parcel_amount" id="parcel_amount" class="form-control rounded-right" placeholder="Parcel Value*" data-rule-required="true" data-msg-required="Parcel Value is required">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-6">
                                                <input type="text" name="quantity" id="quantity" class="form-control rounded-right" placeholder="Quantity*" data-rule-required="true" data-msg-required="Quantity is required">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-6 d-none" id="trax_box_div">
                                                <select name="trax_box" id="trax_box" class="select2 form-control" data-rule-required="true" data-msg-required="Trax Box is required">
                                                    @foreach($trax_boxes as $trax_box)
                                                        <option value="{{$trax_box->id}}">{{$trax_box->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                      {{--  <div class="row border-dashed">
                                            <div class="form-group col-6">
                                                <input type="text" name="weight_charges" id="weight_charges" class="form-control decimal" placeholder="Weight Charges*" data-rule-required="true" data-msg-required="Weight Charges is required">
                                            </div>
                                            <div class="form-group col-6">
                                                <input type="text" name="fuel_surcharge" id="fuel_surcharge" class="form-control fuel_decimal" placeholder="Fuel Surcharge*" data-rule-required="true" data-msg-required="Fuel Surcharge is required">
                                            </div>
                                        </div>--}}
                                    </div>
                                    <div class="form-group">
                                        <textarea id="special_instructions" name="special_instructions" class="form-control" placeholder="Special Instructions" data-rule-maxlength="190" data-msg-maxlength="Special Instructions can be maximum 190 characters" rows="5"></textarea>
                                    </div>
                                </div>
                                <div id="external_info" class="ml-1 col border">
                                    <div class="col mt-2">
                                        <div class="form-group text-center p-1 border border-light rounded">
                                            <label class="d-block">City Request</label>
                                            <a href="javascript:void(0);" id="add_city_req" class="btn btn-outline-success" >Add</a>
                                        </div>
                                    </div>
                                    <div class="col mt-1">
                                        <div class="form-group text-center p-1 border border-light rounded">
                                            <label class="d-block">Bulk Shipment</label>
                                            <input type="checkbox" name="bulk_shipment" class="switch hidden bulk_shipment">
                                        </div>
                                    </div>

{{--                                    <div class="col pt-2">--}}
{{--                                        <div class="form-group text-center p-1 border border-light rounded" style="background-color: black">--}}
{{--                                            <div id='tiles'>--}}
{{--                                                <span>0</span>--}}
{{--                                                <span>0</span>--}}
{{--                                                <span>0</span>--}}
{{--                                                <span>0</span>--}}
{{--                                                <span>0</span>--}}
{{--                                                <span>0</span>--}}
{{--                                            </div>--}}

{{--                                            <div class="mt-1">--}}
{{--                                                <h6 class="white">Incentive Counter</h6>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                    <div class="col mt-1">
                                        <div class="form-group">
                                            <div class="row">
                                               <div class="col-md-7">
                                                   <label>Admin Discount</label>
                                                   <input type="text" name="admin_discount" id="admin_discount"
                                                          class="form-control form-control-sm" placeholder="Admin Discount">
                                               </div>
                                                <div class="col-md-5 align-self-end">
                                                    <label for="" class="">Flat</label>
                                                    <input type="checkbox" id="admin_discount_type" name="admin_discount_type" class="switchery"
                                                           data-size="sm" data-switchery="true">
                                                    <label for="" class="">%</label>
                                                    <input id="admin_discount_type1" value="0" name="admin_discount_type1" hidden>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group" id="retail_discount_code_div">
                                            <label>Discount Code</label>
                                            <input type="text" name="discount_code" id="discount_code" class="form-control form-control-sm" placeholder="Discount Code">
                                            <input type="hidden" name="retail_discount_percentage" id="retail_discount_percentage" value="0">
                                        </div>
                                        <div class="form-group">
                                            <label>Charges</label>
                                            <input type="text" name="charges" id="charges" class="form-control form-control-sm" placeholder="Charges" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>Discount</label>
                                            <input type="text" name="discount" id="discount" class="form-control form-control-sm" placeholder="Discount" disabled>
                                            <input type="hidden" name="retail_discount_amount" id="retail_discount_amount" value="0">
                                        </div>
                                        <div class="form-group">
                                            <label>Charges with Discount</label>
                                            <input type="text" name="discount" id="charges_with_discount" class="form-control form-control-sm" placeholder="Charges With Discount" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>GST</label>
                                            <input type="text" name="gst" id="gst" class="form-control form-control-sm" placeholder="GST" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>Packaging & Insurance Charges</label>
                                            <input type="text" name="packaging_and_insurance_charges" id="packaging_and_insurance_charges" class="form-control form-control-sm" placeholder="Packaging & Insurance Charges" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>Total Charges</label>
                                            <input type="text" name="total_charges" id="total_charges" class="form-control form-control-sm" placeholder="Total Charges" disabled>
                                        </div>
                                        <div class="form-group text-center">
                                            <button type="button" name="calculate_rates" id="calculate_rates" class="btn btn-outline-success width-150" value="calculate_rates">Calculate Rates</button>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center">
                                        <div class="position-absolute" style="bottom: 0;">
                                            <div class="form-group text-center d-none" id="print_div">
                                                <button type="button" name="print" id="print" class="btn btn-outline-cyan width-150" value="print">Print Slip</button>
                                            </div>
                                            <div class="form-group text-center d-none" id="save">
                                                <input type="hidden" name="book_button" id="book_button" value="1">
                                                <button type="button" name="save" id="book" class="btn btn-primary width-150" value="save">Save</button>
                                            </div>
                                            <div class="form-group text-center" id="book_and_print">
                                                <button type="submit" name="book_and_print" class="btn btn-primary width-150" value="Book & Print">Book & Print</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center p-2" id="account_details">
                                <div class="col-5 border">
                                    <h4 id="account_detail_header" class="form-section mb-2 text-center">Account Details</h4>
                                    <div class="form-group col">
                                        <label for="iban">
                                            IBAN Number:
                                            <span class="danger">*</span>
                                        </label>
                                        <input type="text" class="form-control iban text-uppercase required" placeholder="(e.g: PK37MEZN0001220100004069)" value="" name="iban_no" id="iban_no" data-rule-maxlength="24" data-rule-maxlength-message="Max character length 24">
                                        <span id="iban_no_error" class="danger" style="display: none;">IBAN Number must be of 24 Characters</span>
                                    </div>
                                    <div class="form-group col">
                                        <label for="account_name">Account Number:
                                            <span class="danger">*</span></label>
                                        <input type="text" class="form-control required" value="" name="account_no" id="account_no" placeholder="Account Number*" autocomplete="off">
                                    </div>
                                    <div class="form-group col">
                                        <label for="bank">
                                            Bank Name:
                                            <span class="danger">*</span>
                                        </label>
                                        <select name="bank" id="bank" class="select2 form-control required">
                                            @foreach($banks as $bank)
                                                <option value="{{$bank->id}}">{{$bank->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col">
                                        <label for="bank">
                                            Cheque Image:
                                            <span class="danger">*</span>
                                        </label>
                                        <input class="form-control form-control-sm" type="file" name="cheque_image" id="cheque_image" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="AddCityReqModal" data-backdrop="static" role="dialog"
         aria-labelledby="AddCityReqModal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add City Request</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="add_city_req_form" class="form-horizontal" novalidate="novalidate">
                    @csrf
                    <div class="modal-body">
                        <div class="row justify-content-center">
                            <div class="col-12 form-group">
                                <select name="city_shipping_mode" id="city_shipping_mode" class="select2 form-control"
                                        data-rule-required="true" data-msg-required="Service Type is required">
                                    @foreach($shipping_modes as $shipping_mode)
                                        <option value="{{$shipping_mode->id}}">{{$shipping_mode->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 form-group">
                                <select name="city_business_category" id="city_business_category"
                                        class="select2 form-control" data-rule-required="true"
                                        data-msg-required="City Category is required">
                                    @foreach($business_categories as $business_category)
                                        <option value="{{$business_category->id}}">{{$business_category->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 form-group d-none" id="cities_domestic">
                                <select name="city_domestic" id="city_domestic" class="select2 form-control"
                                        data-rule-required="true" data-msg-required="City is required">
                                    <option value="other">Other</option>
                                    @foreach($domestic_cities as $domestic_city)
                                        <option value="{{$domestic_city->name}}">{{$domestic_city->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 d-none" id="other_city_domestics">
                                <div class="form-group">
                                    <input type="text" name="other_city_domestic" id="other_city_domestic"
                                           class="form-control" placeholder="New City" data-rule-required="true"
                                           data-msg-required="City is required">
                                </div>
                            </div>
                            <div class="col-12 form-group d-none" id="cities_international">
                                <select name="city_international" id="city_international" class="select2 form-control"
                                        data-rule-required="true" data-msg-required="City is required">
                                    <option value="other">Other</option>
                                    @foreach($international_cities as $international_city)
                                        <option value="{{$international_city->name}}">{{$international_city->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 d-none" id="other_cities_internationals">
                                <div class="form-group">
                                    <input type="text" name="other_cities_international" id="other_cities_international"
                                           class="form-control" placeholder="New City Type" data-rule-required="true"
                                           data-msg-required="City is required">
                                </div>
                            </div>
                            <div class="col-12 form-group d-none" id="cities_phone_number">
                                <input type="text" class="form-control" id="city_phone_number" name="city_phone_number"
                                       placeholder="Phone Number*" data-rule-required="true"
                                       data-msg-required="Phone # is required">
                            </div>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button id="AddFleetBtn" type="submit" class="btn btn-info">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade text-left" id="AutoFetchConsignee" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="AutoFetchConsignee"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Consignee Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="auto_fetch_consignee" class="form-horizontal" novalidate="novalidate">
                    @csrf
                    <div class="modal-body">
                        <div class="row justify-content-center">
                            <div class="col-12 form-group">
                                <h3 class="text-danger text-center" id="black_listed_employee">Employee is
                                    Blacklisted</h3>
                            </div>
                            <div class="col-12 form-group">
                                <table class="table" id="consignee_table">

                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="AutoFetchShipper" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="AutoFetchShipper"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Shipper Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="auto_fetch_shipper_form" class="form-horizontal" novalidate="novalidate">
                    @csrf
                    <div class="modal-body">
                        <div class="row justify-content-center">
                            {{-- <div class="col-12 form-group">
                                <h3 class="text-danger text-center" id="black_listed_employee">Employee is Blacklisted</h3>
                            </div> --}}
                            <div class="col-12 form-group">
                                <table class="table" id="shipper_table">
                                    <div id="no_info_div" class="d-none">
                                        <span id="no_info_text"></span>
                                    </div>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="walletSignupModal" tabindex="-1" role="dialog" aria-labelledby="walletSignupLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-xl-custom" role="document"> <!-- changed class -->
            <div class="modal-content border-primary">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="walletSignupLabel">
                        Wallet Signup for <strong id="wallet_user_text"></strong>
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                    <!-- Wallet Signup Form -->
                    <form id="main-form" class="form-horizontal" method="POST"  novalidate="novalidate" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="wallet_user_id" id="wallet_user_id">
                        <div class="form-body">
                            <h4 class="form-section">
                                Profile Information (Please verify your profile information before sign up to wallet)
                            </h4>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group col-md-9">
                                        <label>Enter Name (As per CNIC OR Bank Account Title)</label>
                                        <span class="danger">*</span>
                                        <input type="text" id="name" class="form-control border-primary"
                                               name="name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group col-md-9">
                                        <label>Phone Number 1:</label>
                                        <span class="danger">*</span>
                                        <input type="text" id="phone" class="form-control border-primary"
                                               name="phone" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group col-md-9">
                                        <label>CNIC:</label>
                                        <input type="text" id="cnic" class="form-control border-primary" name="cnic">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group col-md-9">
                                        <label>Email Address:</label>
                                        <span class="danger">*</span>
                                        <input type="email" id="email" class="form-control border-primary" name="email"
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group col-md-9">
                                        <label>Cnic Front:</label>
                                        <span class="danger">*</span>
                                        <input class="form-control form-control-sm" type="file" name="cnic_front" id="cnic_front" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group col-md-9">
                                        <label>Cnic Back:</label>
                                        <span class="danger">*</span>
                                        <input class="form-control form-control-sm" type="file" name="cnic_back" id="cnic_back" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">

                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group col-md-9">
                                        <input type="checkbox" id="confirm" name="confirm">
                                        <span>
                                        By signing up for the wallet, all your payments will be credited directly to your wallet.
                                        Please check this box to agree.
                                    </span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions right">
                                <button type="submit" id="signup_button" class="btn btn-primary" disabled>
                                    Signup to Wallet
                                </button>
                            </div>
                        </div>
                    </form>
                    <!-- End Wallet Signup Form -->

                </div>
            </div>
        </div>
    </div>

@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/line-awesome/css/line-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/cryptocoins/cryptocoins.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <style>

        #tiles{
            position: relative;
            z-index: 1;
        }

        #tiles > span{
            width: auto;
            font: bold 32px 'Droid Sans', Arial, sans-serif;
            text-align: center;
            color: #111;
            background-color: #ddd;
            background-image: -webkit-linear-gradient(top, #bbb, #eee);
            background-image:    -moz-linear-gradient(top, #bbb, #eee);
            background-image:     -ms-linear-gradient(top, #bbb, #eee);
            background-image:      -o-linear-gradient(top, #bbb, #eee);
            border-top: 1px solid #fff;
            border-radius: 3px;
            box-shadow: 0px 0px 12px rgba(0, 0, 0, 0.7);
            margin: auto;
            padding: auto;
            display: inline-block;
            position: relative;
        }

        #tiles > span:before{
            content:"";
            width: 100%;
            height: 13px;
            background: #111;
            display: block;
            padding: auto;
            position: absolute;
            top: 41%; left: -3px;
            z-index: -1;
        }

        #tiles > span:after{
            content:"";
            width: 100%;
            height: 1px;
            background: #eee;
            border-top: 1px solid #333;
            display: block;
            position: absolute;
            top: 48%; left: 0;
        }
    </style>
@endsection

@section('js')
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/require.js/2.3.6/require.min.js" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/charts/echarts/echarts.common.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/charts/chartjs/chart.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pagination/moment.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>



    <script type="text/javascript">

        function bdmk(){
            var city_id = $('#domestic_destination').val();
            var city_name = $('#domestic_destination option:selected').text();
            var consignee_address = $('#consignee_address').val();


            $.ajax({
                url: '{{route('retail.shipment.book.address_verify')}}',
                method: 'get',
                data: {
                    'city_id': city_id,
                    'consignee_address': consignee_address
                }
            }).done(function (data) {

                if (data) {
                    var er = "Dear User, <br>";
                    if (data.invalid_cities) {
                        $.each(data.invalid_cities, function (key, value) {
                            er +=  "The area <strong>" + value + "</strong> is actually present in <strong>" + key + "</strong> instead of <strong>" + city_name +"</strong>. <br>";
                        });
                        $('#consignee_address_error').html(er.trim() + " For Assistance Call 021-111-118-729");
                        $('#consignee_address_error').show();

                    }else{
                        $('#consignee_address_error').html('');
                        $('#consignee_address_error').hide();
                    }
                }

            });

        }

        $(document).ready(function () {
            $('#wallet_id_div').hide();
            $('#admin_discount').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });


            //if admin_discount is not empty then disable the retail_discount_code input field
            $('#admin_discount').on('input', function () {
                if ($(this).val() != '') {
                    $('#discount_code').prop('disabled', true);
                }
                else {
                    $('#discount_code').prop('disabled', false);
                } 
            });

            //if discount_code is not empty then disable the retail_discount_code input field
            $('#discount_code').on('input', function () {
                if ($(this).val() != '') {
                    $('#admin_discount').prop('disabled', true);
                }
                else {
                    $('#admin_discount').prop('disabled', false);
                } 
            });

            $('#discount_code').inputmask('Regex', {regex: "^[A-Za-z0-9]*$"});
            var shipping_modes = @json($shipping_modes);
            var international_shipping_modes = @json($retail_international_shipping_modes);

            $('#consignee_phone_no').inputmask({
                mask: 'R',
                repeat:25,
                greedy: false,
                definitions: {
                    R: {
                        validator: '[0-9]',
                    },
                },
            });
            
            function print(ids){
                $.ajax({
                    url: '{!! route('retail.shipment.book.slip') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'ids[]': ids
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
            }
            @if (session('print'))
                print({{ session('print') }});
            @endif

            // $('#parent_product_id').prepend('<option value="" selected="selected"></option>').select2({
            //     width:'100%',
            //     placeholder:"Select Parent Product*",
            //     allowClear:true
            // }).bind('select2:select', function() {
            //         var id = $(this).val();
            //        $.ajax({
            //         url: '{!! route('retail.shipment.book.get_products') !!}',
            //         method: 'POST',
            //         data: {
            //             '_token': '{{ csrf_token() }}',
            //             'parent_product_id': id
            //         }
            //         }).done(function(data) {
            //             if (data.status == 0) {
            //                 $('#product').children().remove();
            //                 $('#product').prepend('<option value="" selected="selected"></option>')
            //                 $.each(data.products, function(index, products) {
            //                     $('#product').append('<option value="' + products.id +
            //                         '" >' + products.product_name + '</option>')
            //                 });
            //             }
            //         });
            // });
            $('#product').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Shipment*",
                allowClear:true
            });
            $('#ref').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Where did you hear about us*",
                allowClear:true
            }).bind('change', function() {
                if ($(this).val() === 'Others') {
                    $('#other_ref').removeClass('d-none');
                }
                else{
                    $('#other_ref').addClass('d-none');
                }
            });
            $('#business_category').select2({
                width:'100%',
                placeholder:"Select Shipment Category*"
            }).bind('change',function(){
                var id = parseInt($(this).val());
                var shipping_mode = parseInt($("#shipping_mode").val());
                if(id == 2)
                {
                    $('.phone').inputmask("Regex", { regex: "[+|0][0-9]*"});
                    $('#domestic_overland_destination_div').addClass('d-none');
                    $('#domestic_destination_div').addClass('d-none');
                    $('#international_destination_div').removeClass('d-none');
                    

                    $('#shipping_mode').empty();
                    $.each(international_shipping_modes, function (key, value) {
                        var newOption = "<option value="+ value.id +">" + value.name + "</option>";
                        $('#shipping_mode').append(newOption);
                    });

                    if(!$('#retail_discount_code_div').hasClass('d-none'))
                    {
                        $('#retail_discount_code_div').addClass('d-none');
                    };

                    $('#admin_discount').prop('disabled', false);
                    $('#discount_code').val('');

                }
                else{

                    if(id == 1)
                    {
                        $('#retail_discount_code_div').removeClass('d-none');
                        $('#discount_code').val('');
                    }

                    $('#shipping_mode').empty();
                    $.each(shipping_modes, function (key, value) {
                        var newOption = "<option value="+ value.id +">" + value.name + "</option>";
                        $('#shipping_mode').append(newOption);
                    });

                    if(shipping_mode == 1)
                    {

                        $('#domestic_overland_destination_div').removeClass('d-none');
                        $('#domestic_destination_div').addClass('d-none');
                        $('#international_destination_div').addClass('d-none');
                    }
                    else{
                        $('.phone').inputmask("Regex", { regex: "[+|0][0-9]*"});
                        $('#domestic_destination_div').removeClass('d-none');
                        $('#domestic_overland_destination_div').addClass('d-none');
                        $('#international_destination_div').addClass('d-none');
                    }
                }
            });
            var overland = false;
            $('#shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Product*"
            }).bind('change', function () {
                $('#shipper_phone_no').val('');
                var id = parseInt($(this).val());
                var business_category = parseInt($("#business_category").val());
                if(id === 1){
                    $('#cod_check').addClass('d-none');
                    if(business_category == 1)
                    {
                        $('#domestic_overland_destination_div').removeClass('d-none');
                        $('#domestic_destination_div').addClass('d-none');
                        $('#international_destination_div').addClass('d-none');
                    }
                    else {

                        $('#domestic_overland_destination_div').addClass('d-none');
                        $('#domestic_destination_div').addClass('d-none');
                        $('#international_destination_div').removeClass('d-none');
                    }
                    overland = true;
                }
                else{
                    if(business_category == 1) {
                        $('#domestic_destination_div').removeClass('d-none');
                        $('#domestic_overland_destination_div').addClass('d-none');
                        $('#international_destination_div').addClass('d-none');
                    }
                    else{
                        $('#domestic_destination_div').addClass('d-none');
                        $('#domestic_overland_destination_div').addClass('d-none');
                        $('#international_destination_div').removeClass('d-none');
                    }
                    if(id == 5){
                        $('#trax_box_div').removeClass('d-none');
                    }
                    else{
                        $('#trax_box_div').addClass('d-none');
                    }
                    if(id == 3){
                        $('#cod').val('');
                        $('#cod_check').removeClass('d-none');
                    }
                    else{
                        $('#cod_check').addClass('d-none');
                    }
                    overland = false;
                }
            });
            $('.destination').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Destination*",
                allowClear:true
            });

            $(this).find('.pieces').TouchSpin({
                min: 1,
                max: 50,
                buttondown_class: 'btn btn-primary rounded-left',
                buttonup_class: 'btn btn-primary rounded-right',
                buttondown_txt: '<i class="ft-minus"></i>',
                buttonup_txt: '<i class="ft-plus"></i>'
            }).bind('input change', function() {
                $(this).tooltip('show');

                if ($(this).hasClass('danger')) {
                    $(this).valid();
                }
            });

            $('#payment_mode').select2({
                width:'100%',
                placeholder:"Select Payment Mode*",
                allowClear:true
            });

            $('#charges_mode').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Charges Mode*",
                allowClear:true
            });

            $('#iban_no').inputmask({
                mask: 'R',
                repeat: 24,
                greedy: false,
                definitions: {
                    R: {
                        validator: '[a-zA-Z0-9]',
                    },
                },
            });

            $('#iban_no').on('input', function (e) {
                var iban = $(this).val().replace(/\s+/g, '').toUpperCase(); // Remove white spaces and convert to uppercase
                var defaultPrefix = 'PK';

                if (!iban.startsWith(defaultPrefix)) {
                    iban = defaultPrefix + iban.substring(defaultPrefix.length);
                }

                if (iban.length > 2 && !iban.startsWith(defaultPrefix)) {
                    $(this).val(defaultPrefix + iban.substring(defaultPrefix.length));
                } else {
                    $(this).val(iban);
                }

                if (iban.length !== 24 || !iban.startsWith(defaultPrefix)) {
                    $('#iban_no_error').show();
                } else {
                    $('#iban_no_error').hide();
                }
            });



            $('#weight').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'digits': 2,
            });

            $('#booking_form input.volumetric_weight').checkboxpicker().bind('change', function() {
                if (this.checked) {
                    $('#booking_form input.weight').val('').prop('disabled', true);

                    $('#booking_form .volumetric_weights input').val('').prop('disabled', false);
                }
                else {
                    $('#booking_form input.weight').val('').prop('disabled', false);

                    $('#booking_form .volumetric_weights input').val('').prop('disabled', true);
                }
            });

            $('#booking_form .volumetric_weights input.length').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'digits': 2
            });

            $('#booking_form .volumetric_weights input.breadth').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'digits': 2
            });

            $('#booking_form .volumetric_weights input.height').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'digits': 2
            });

            $('.number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'groupSeparator': ',',
                'autoGroup': true,
                dropdownParent:$('#booking_form')
            });


            $(".phone1").inputmask({'mask': "9999-9999999", 'clearIncomplete': true});
            $("#city_phone_number").inputmask({'mask': "9999-9999999", 'clearIncomplete': true});
            
            $('.phone').inputmask("Regex", { regex: "[+|0][0-9]*"});
            $(".cnic").inputmask({'mask': "99999-9999999-9", 'clearIncomplete': true});

            $('.amount').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'groupSeparator': ',',
                'autoGroup': true,
            });

            $('.decimal').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 3,
                'min': 0.01,
                'max': 10000000,
                'groupSeparator': ',',
                'autoGroup': true,
            });
            $('.fuel_decimal').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 3,
                'min': 0.00,
                'max': 10000000,
                'groupSeparator': ',',
                'autoGroup': true,
            });

            $('#insurance_offered').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Insurance Offered*",
                allowClear:true
            }).bind('change', function () {
                var id = parseInt($(this).val());
                if(id == 1){
                    $('#insurance_amount_div').removeClass('d-none');
                }
                else{
                    $('#insurance_amount_div').addClass('d-none');
                }
            });

            $('#trax_box').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Trax Box*",
                allowClear:true
            });

            $('.bulk_shipment').checkboxpicker().bind('change', function() {
                if (this.checked) {
                    $('#save').removeClass('d-none');
                    $('#book_and_print').addClass('d-none');
                }
                else {
                    $('#booking_form').trigger("reset");
                    $('#save').addClass('d-none');
                    $('#book_and_print').removeClass('d-none');
                }
            });
            var complete_shipper_info = false;
            var first_shipment = false;

            $('#shipper_phone_no').on('change', function () {
                $('#is_wallet').prop('disabled', true).prop('checked', false);
                $('#complete_shipper_info').val(0);
                if(this.value !== '' && this.value != null){
                    $.ajax({
                        url: '{!! route('retail.shipment.shipper_info') !!}',
                        method: 'POST',
                        data: {
                            'shipper_phone_no': this.value,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                        .done(function (data) {
                            var cod = data.cod;

                            if(data.status == 1){
                                $('#shipper_phone_no').val(data.details.shipper_phone_no);
                                /*$('#shipper_name').val(data.details.shipper_name);
                                $('#shipper_cnic').val(data.details.shipper_cnic);
                                $('#shipper_address').val(data.details.shipper_address);*/
                                $('#iban_no').val(data.details.iban);
                                $('#account_no').val(data.details.account_number);
                                $('#bank').val(data.details.bank_id).trigger('change');
                                if(data.complete_info == false){
                                    complete_shipper_info = false;
                                    $('#account_details').removeClass('d-none');
                                }
                                else{
                                    complete_shipper_info = true;
                                    $('#account_details').addClass('d-none');
                                }
                            }

                            else if (data.status == 2){
                                $('#iban_no').val('');
                                $('#account_no').val('');
                                $('#bank').val('').trigger('change');
                                $('#account_details').removeClass('d-none');
                            }

                            else{
                                complete_shipper_info = false;
                                first_shipment = true;
                                $('#account_details').removeClass('d-none');
                            }
                            if(complete_shipper_info == false){
                                var mode = $('#shipping_mode').val();
                                if(first_shipment == true){
                                    $('#iban_no').removeClass('required');
                                    $('#account_no').removeClass('required');
                                    $('#bank').removeClass('required');
                                    first_shipment = false;
                                }
                                else if(cod == true && ($('#iban_no').val() == null || $('#iban_no').val() == '')) {
                                    $('#iban_no').addClass('required');
                                    $('#account_no').addClass('required');
                                    $('#bank').addClass('required');
                                }
                                else if($('#shipping_mode').val() != 3){
                                    $('#iban_no').removeClass('required');
                                    $('#account_no').removeClass('required');
                                    $('#bank').removeClass('required');
                                }
                                else{
                                    $('#account_details').removeClass('d-none');
                                    $('#iban_no').addClass('required');
                                    $('#account_no').addClass('required');
                                    $('#bank').addClass('required');
                                }
                            }

                            if(complete_shipper_info) {
                                $('#complete_shipper_info').val(1);
                            }else{
                                $('#complete_shipper_info').val(0);
                            }
                            $('#is_wallet').prop('disabled', false);
                        });


                }
            });

            var toggleValue = false;
            $('#admin_discount_type').change( function () {
                toggleValue = !toggleValue;
                if(toggleValue)
                {
                   $('#admin_discount_type1').val("1");
                }
                else
                {
                    $('#admin_discount_type1').val("0");
                }
            });

            $('#apply_discount_code').change(function () {
                if($(this).is(':checked')){
                    $('#admin_discount').attr('disabled', true);
                    $('#admin_discount_type').attr('disabled', true);
                    $('#admin_discount').val('');
                    $('#admin_discount_type').val('');

                }
                else{
                    $('#admin_discount').attr('disabled', false);
                    $('#admin_discount_type').attr('disabled', false);
                }
            });


            var shipment_ids = [];
            $('#book').on('click', function () {
               var validator = $('#booking_form').valid();
               if(validator) {
                   swal({
                       title: 'Please Wait!',
                       text: 'Your shipment is being booked!',
                       icon: 'info',
                       buttons: false,
                       closeOnClickOutside: false,
                       closeOnEsc: false
                   });
                    $('#book_button').val(0);
                   var booking_form = new FormData($('#booking_form')[0]);
                   $('#book').attr('disabled', true);
                   $.ajax({
                       url: '{!! route('retail.shipment.book.store') !!}',
                       method: 'POST',
                       enctype: 'multipart/form-data',
                       data: booking_form,
                       dataType: 'json',
                       processData: false,
                       contentType: false,
                   })
                       .done(function (data) {
                           swal.close();
                           if (data.status) {
                               shipment_ids.push(data.shipment_id);
                               html = data.success;
                               html += '</br><p style="red">Note: Please click on print button to print all bulk Shipment(s) Slip</p>';

                               content = document.createElement('div');
                               content.innerHTML = html;

                               swal({
                                   title: 'Shipment Booked!',
                                   content: content,
                                   icon: 'success',
                                   buttons: {
                                       cancel: {
                                           text: 'Close',
                                           value: null,
                                           visible: true,
                                           closeModal: true,
                                       },
                                   },
                                   closeOnClickOutside: false,
                                   closeOnEsc: false,
                                   dangerMode: true
                               });
                               $('#print_div').removeClass('d-none');
                           }
                       });
                        $("#order_id").val('');
                        $('#length').val('');
                        $('#breadth').val('');
                        $('#height').val('');
                        $('#weight').val('');
                        $('#domestic_destination').val('').trigger('change');
                        $('#domestic_overland_destination').val('').trigger('change');
                        $('#pieces').val('');
                        $('#consignee_phone_no').val('');
                        $('#consignee_name').val('');
                        $('#consignee_cnic').val('');
                        $('#consignee_address').val('');
                        //$('#weight_charges').val('');
                        // $('#cash_handling_charges').val('');
                        //$('#fuel_surcharge').val('');
                        $('#charges').val('');
                        $('#discount').val('');
                        $('#total_charges').val('');
                        $('#gst').val('');
                        $('#charges_with_discount').val('');
                        $('#insurance_amount').val('');
                        $('#packaging_amount').val('');
                        $('#cod').val('');
                        $('#trax_box').val('').trigger('change');
                        $('#insurance_offered').val('').trigger('change');
                        $('#special_instructions').val('');
                        
                    $('#book_button').val(1);
                    $('#book').attr('disabled', false);
               }
            });

            $('#booking_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                rules: {
                    consignee_address: {
                        // remote: {
                        //     url: '{{route('retail.shipment.book.address_verify')}}',
                        //     data: {
                        //         city_id: function () {
                        //             return $("#domestic_destination").val();
                        //         },
                        //     },
                        //     dataFilter: function(data) {
                        //         // var json = JSON.parse(data);
                        //         // if(json.status === "true") {
                        //         //     return true;
                        //         // }
                        //         // return "\"" + json.error + "\"";

                        //         return true;


                        //     },
                        //     complete: function (data) {
                        //         if (data.responseText) {
                        //             var json = JSON.parse(data.responseText);
                        //             var er = "";
                        //             if (json.invalid_cities) {
                        //                 $.each(json.invalid_cities, function (key, value) {
                        //                     er +=  "Select " + key + " in destination for " + value+"<br>";
                        //                 });
                        //                 $('#consignee_address_error').html("Address Anomaly detected Keyword "+"<br>"+er.trim() + " For Assistance Call 021-111-118-729");
                        //                 $('#consignee_address_error').show();

                        //             }else{
                        //                 $('#consignee_address_error').html('');
                        //                 $('#consignee_address_error').hide();
                        //             }
                        //         }
                        //     }

                        // },
                        maxlength: 255,
                    },
                    parcel_amount: {
                        required: true,
                        min: 2
                    }
                },
                messages: {
                    consignee_address: {
                        required: "Address Is Required",
                        maxlength :"Address can be maximum 255 characters",
                    },
                    parcel_amount: {
                        required: "Parcel Value is required",
                        min: "Parcel Value must be greater than 1"
                    }
                },
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
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

            $('#bank').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:'Select Bank',
            });
            $('#account_details_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $('#book').click;
                }
            });
            var city_id = null;
            var trax_box = null;
            var length = null;
            var breadth = null;
            var height = null;
            var insurance = null;
            var packaging = null;

            $('#calculate_rates').on('click', function () {
                var destination = '';
                var shipping_mode_id = $('#shipping_mode').val();
                var business_category = $('#business_category').val();
                var retail_discount_code = $('#discount_code').val();
                var product_id = $('#product').val();
                var cod = $('#cod').val();

                var retail_discount_percentage = 0;
                var retail_discount_applied = ($('#discount_code').val() == '') ? 0 : 1;

                if(retail_discount_code !== '' && business_category == 1){

                        $.ajax({
                            url: '{{route('retail.shipment.book.discount_code_verify')}}'+`/${retail_discount_code}`,
                            method: 'get',
                        }).done(function (data) {
                            if (data.status == 1) {
                                retail_discount_percentage = data.data.discount_percentage;
                                $('#retail_discount_percentage').val(retail_discount_percentage);

                                calculateRates(shipping_mode_id,business_category,destination,weight,trax_box,length,breadth, insurance, packaging, height, admin_discount, admin_discount_type, retail_discount_applied, retail_discount_percentage,product_id , cod);

                                toastr.success(retail_discount_percentage+'% Discount Applied', 'Success!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }
                            else
                            {
                                var error = data.message;
                                toastr.error(error, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }


                        });
                }
                else
                {
                    calculateRates(shipping_mode_id,business_category,destination,weight,trax_box,length,breadth, insurance, packaging, height, admin_discount, admin_discount_type, retail_discount_applied, retail_discount_percentage, product_id , cod);
                }

            });

            function calculateRates(shipping_mode_id,business_category,destination,weight,trax_box,length,breadth, insurance, packaging, height, admin_discount, admin_discount_type, retail_discount_applied, retail_discount_percentage, product_id , cod)
            {
                
                if(business_category == 1){
                    if(shipping_mode_id == 1){
                        destination = $('#domestic_overland_destination').val();
                    }
                    else if (shipping_mode_id == 2){
                        destination = $('#domestic_destination').val();
                    }
                    else{
                        destination = $('#domestic_destination').val();
                    }
                }
                else{
                    destination = $('#international_destination').val();
                }

                var weight = $('#weight').val();
                var trax_box = $('#trax_box').val();
                 length = $('#length').val();
                 breadth = $('#breadth').val();
                 height = $('#height').val();
                 insurance = $('#insurance_amount').val();
                 packaging = $('#packaging_amount').val();
                var admin_discount = $('#admin_discount').val();
                var admin_discount_type = $('#admin_discount_type1').val();

                 if($('#insurance_offered').val() == 1 && (insurance == null || insurance == '')){
                     var error = 'Insurance Amount is required';
                     toastr.error(error, 'Error!', {
                         positionClass: 'toast-top-center',
                         containerId: 'toast-top-center'
                     });
                     return false;
                 }
                 else if($('#insurance_offered').val() == '' || $('#insurance_offered').val() == null){
                     var error = 'Select option for insurance';
                     toastr.error(error, 'Error!', {
                         positionClass: 'toast-top-center',
                         containerId: 'toast-top-center'
                     });
                     return false;
                 }

                if(shipping_mode_id != '' && business_category != '' && destination != ''  && (weight != '' || length != '')){
                    if(shipping_mode_id == 5 && trax_box == ''){
                        var error = 'Trax Box field is required';
                        toastr.error(error, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                        return false;
                    }                 
                    $.ajax({
                        url: '{!! route('retail.shipment.book.calculate_rates') !!}',
                        method: 'POST',
                        data: {
                            'shipping_mode_id': shipping_mode_id,
                            'business_category_id': business_category,
                            'consignee_city_id': destination,
                            'weight': weight,
                            'trax_box': trax_box,
                            'length': length,
                            'breadth': breadth,
                            'insurance_amount': insurance,
                            'packaging_amount': packaging,
                            'height': height,
                            'admin_discount': admin_discount,
                            'admin_discount_type1': admin_discount_type,
                            'retail_discount_applied': retail_discount_applied,
                            'retail_discount_percentage': retail_discount_percentage,
                            'product_id' : product_id,
                            'cod' : cod,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                .done(function (data) {
                            if (data.status === 1)
                            {
                                var total_charges = '';


                                    $('#charges').val(data.details.charges);
                                    $('#discount').val(data.details.discount_amount);
                                    $('#charges_with_discount').val(data.details.charges_with_discount);
                                    $('#gst').val(data.details.gst_charges);
                                    $('#packaging_and_insurance_charges').val(data.details.packaging_and_insurance_charges);
                                    $('#retail_discount_amount').val(data.details.discount_amount);
                                    $('#total_charges').val(data.details.total_charges);
                                    $('#wht').val(data.details.wht);
                                    $('#cod_sst').val(data.details.cod_sst);

                            }
                            else
                            {
                                toastr.error(data.error, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });

                                $('#charges').val('');
                                $('#discount').val('');
                                $('#charges_with_discount').val('');
                                $('#gst').val('');
                                $('#packaging_and_insurance_charges').val('');

                                $('#total_charges').val('');

                                $('#admin_discount').val('');
                            }

                });
                }
                else{
                    var error = 'Shipping Mode,Business Category,Destination and Weight/Volumetric weight should not be empty';
                    toastr.error(error, 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                }
            }

            $('#print').on('click', function () {
                if(shipment_ids.length > 0){
                    print(shipment_ids);
                }
            });
            

            $("#add_city_req").on('click', function(){
                $('#AddCityReqModal').modal('show');
            });

            $('#city_shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Shipping Modes*"
            });
            $('#city_business_category').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select City Category*"
            }).bind('change',function(){
                var id = parseInt($(this).val());
                if(id == 1){

                    $('#cities_domestic').removeClass('d-none');
                    $('#cities_international').addClass('d-none');
                }else if(id == 2){
                    $('#cities_domestic').addClass('d-none');
                    $('#cities_international').removeClass('d-none');
                }
                $('#cities_phone_number').removeClass('d-none');

                
                
            });
            $('#city_domestic').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select City*"
            }).bind('change', function() {
                if ($(this).val() === 'other') {
                    $('#other_city_domestics').removeClass('d-none');
                }
                else{
                    $('#other_city_domestics').addClass('d-none');
                }
            });
            $('#city_international').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select City*"
            }).bind('change', function() {

                if ($(this).val() === 'other') {
                    $('#other_cities_internationals').removeClass('d-none');
                }
                else{
                    $('#other_cities_internationals').addClass('d-none');
                }
            });
            $('#add_city_req_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $.ajax({
                    url: '{!! route('retail.shipment.book.add_city_req') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'city_shipping_mode': $('select[name="city_shipping_mode"]').val(),
                        'city_business_category': $('select[name="city_business_category"]').val(),
                        'city_phone_number': $('input[name="city_phone_number"]').val(),
                        
                        'city_domestic': $('select[name="city_domestic"]').val(),
                        'other_city_domestic': $('input[name="other_city_domestic"]').val(),
                        'city_international': $('select[name="city_international"]').val(),
                        'other_cities_international': $('input[name="other_cities_international"]').val()
                    }
                })
                    .done(function(data) {
                        if(data.status == 1){
                            UnblockPagePermanently();

                            toastr.success(data.success, 'Success!', {
                                positionClass: 'toast-bottom-center',
                                containerId: 'toast-bottom-center'
                            });
                            $('#AddCityReqModal').modal('hide');
                            $('#city_shipping_mode').val('').trigger('change.select2');
                            $('#city_business_category').val('').trigger('change.select2');
                            $('#city_domestic').val('').trigger('change.select2');
                            $('#other_city_domestic').val('');
                            $('#city_international').val('').trigger('change.select2');
                            $('#other_cities_international').val('');
                            $('#city_phone_number').val('');
                        }
                    });
                }
            });

            $('#AddCityReqModal').on('hidden.bs.modal', function () {
                
                $('#city_shipping_mode').val('').trigger('change.select2');
                $('#city_business_category').val('').trigger('change.select2');

                $('#city_domestic').val('').trigger('change.select2');
                $('#other_city_domestic').val('');
                $('#city_international').val('').trigger('change.select2');
                $('#other_cities_international').val('');
                $('#other_city_domestics').addClass('d-none');
                $('#other_cities_internationals').addClass('d-none');
                $('#cities_international').addClass('d-none');
                $('#cities_domestic').addClass('d-none');
                
            });

        

            $('#auto_fetch').on('click', function(){
                if($('input[name="consignee_phone_no"]').val().match(/\d/g) != null){
					var length = $('input[name="consignee_phone_no"]').val().match(/\d/g).length;
				}
				else{
					var length = 0;
				}
				if(length == 11){
					$.ajax({
                    url: '{!! route('retail.shipment.book.consignee_info') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'phone': $('input[name="consignee_phone_no"]').val(),
                        
                    }
                })
                    .done(function(data) {
                        if(data.status == 1){
                            toastr.error(data.message, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                            });
                        }else{
                            if(data.blacklist == 0){
                                $('#black_listed_employee').addClass('d-none');
                            }else{
                                $('#black_listed_employee').removeClass('d-none');
                            }
                            $('#AutoFetchConsignee').modal('show');
                            var html = '';
                            $.each(data.consignee, function (index, details) {
                                    html +='<tr><td><a href="javascript:void(0)" class="btn btn-outline-success btn-sm auto_fetch_btn"><i class="ft-check"></i></a></td>';
                                    html +='<td>'+details.name+'</td>';
                                    html +='<td>'+details.address+'</td></tr>';
                                });
                                $('#consignee_table').html(html);   
                                $('.auto_fetch_btn').on('click', function(){
                                    var name = $(this).parent().next().html();
                                    var address = $(this).parent().next().next().html();

                                    $('#consignee_name').val(name);
                                    $('#consignee_address').val(address);
                                    $('#AutoFetchConsignee').modal('hide');

                                });
                        }
                      
                    });
				}
                
            });

            $('#AutoFetchConsignee').on('hidden.bs.modal', function () {
                $('#consignee_table').html('');   

            });

            $('#auto_fetch_shipper').on('click', function(){
                if($('input[name="shipper_phone_no"]').val().match(/\d/g) != null){
					var length = $('input[name="shipper_phone_no"]').val().match(/\d/g).length;
				}
				else{
					var length = 0;
				}
				if(length == 11){
					$.ajax({
                    url: '{!! route('retail.shipment.book.previous_names_verify') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'phone_number': $('input[name="shipper_phone_no"]').val(),
                        
                    }
                })
                    .done(function(data) {
                        if(data.status == 0){
                            toastr.error(data.message, 'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                            });
                        }
                        else if (data.data.length < 1){
                            $('#AutoFetchShipper').modal('show');
                            $("#no_info_div").removeClass('d-none');
                            $("#no_info_div").addClass('text-center font-medium-2');
                            $("#no_info_text").text('No information found')
                        }
                        else{
                            // if(data.blacklist == 0){
                            //     $('#black_listed_employee').addClass('d-none');
                            // }else{
                            //     $('#black_listed_employee').removeClass('d-none');
                            // }
                            $("#no_info_div").addClass('d-none');
                            $('#AutoFetchShipper').modal('show');
                            var html = '';
                            $.each(data.data, function (index, details) {
                                    html +='<tr><td><a href="javascript:void(0)" class="btn btn-outline-success btn-sm shipper_auto_fetch_btn"><i class="ft-check"></i></a></td>';
                                    html +='<td>'+details.shipper_name+'</td>';
                                    html +='<td>'+details.shipper_address+'</td>';
                                    html +='<td>'+details.shipper_cnic+'</td></tr>';
                                });
                                $('#shipper_table').html(html);   
                                $('.shipper_auto_fetch_btn  ').on('click', function(){
                                    var name = $(this).parent().next().html();
                                    var address = $(this).parent().next().next().html();
                                    var shipper_cnic = $(this).parent().next().next().next().html();

                                    $('#shipper_name').val(name);
                                    $('#shipper_address').val(address);
                                    $('#shipper_cnic').val(shipper_cnic);
                                    $('#AutoFetchShipper').modal('hide');

                                });
                        }
                    });
				}
                
            });

            $('#AutoFetchShipper').on('hidden.bs.modal', function () {
                $('#shipper_table').html('');   
            });

            $('#previous_name').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Previous Name",
                allowClear:true
            });

            $('#shipper_phone_no').keyup(function () {
                var phone_number = $(this).val();
                var cleaned_phone_number = phone_number.replace(/[-_]/g, '');
                if (cleaned_phone_number.length !== 11)
                {
                    $('#previous_name').empty();
                    $('#shipper_cnic').val('');
                    $('#shipper_address').val('');
                    $('#shipper_name').val('');
                }

                if (cleaned_phone_number.length === 11)
                {
                    $.ajax({
                        url: '{!! route('retail.shipment.book.previous_names_verify') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'phone_number': phone_number
                        },
                    })
                        .done(function (data) {
                            if (data.status === 1) {
                                var previousNames = data.data;
                                $('#previous_name').empty();
                                $('#previous_name').append('<option value="" selected="selected">Select Previous Name</option>');
                                previousNames.forEach(function (data) {
                                    $('#previous_name').append('<option value="' + data.id + '" data-value="'+ data.shipper_cnic+'" data-value1="'+ data.shipper_address+'" data-value2="'+ data.shipper_name+'">' + data.shipper_name + '</option>');
                                });

                                $('#previous_name').trigger('change');
                                $('#shipper_cnic').val('');
                                $('#shipper_address').val('');
                                $('#shipper_name').val('');
                            }
                        });
                }
            });

            $('#previous_name').on('change', function() {

                var selectedOption  = $('#previous_name option:selected');
                var selectedValue = selectedOption.val();
                var cnic = selectedOption.data('value');
                var address = selectedOption.data('value1');
                var name = selectedOption.data('value2');

                $('#shipper_cnic').val(cnic);
                $('#shipper_address').val(address);
                $('#shipper_name').val(name);

            });

            $('[name="book_and_print"]').on('click', function () {
                var ibanNoValue = $('#iban_no').val();
                var accountNoValue = $('#account_no').val();
                var bankValue = $('#bank').val();
                var chequeImageValue = $('#cheque_image').val();

                if ((ibanNoValue === '' || accountNoValue === '' || bankValue === '' || chequeImageValue === '') && $('#shipping_mode').val() == 3) {
                    if (ibanNoValue === '') {
                        $('#iban_no').addClass('required');
                    } else {
                        $('#iban_no').removeClass('required');
                    }

                    if (accountNoValue === '') {
                        $('#account_no').addClass('required');
                    } else {
                        $('#account_no').removeClass('required');
                    }

                    if (bankValue === '') {
                        $('#bank').addClass('required');
                    } else {
                        $('#bank').removeClass('required');
                    }

                    if (chequeImageValue === '') {
                        $('#cheque_image').addClass('required');
                    } else {
                        $('#cheque_image').removeClass('required');
                    }
                }else{
                    $('#iban_no').removeClass('required');
                    $('#account_no').removeClass('required');
                    $('#bank').removeClass('required');
                    $('#cheque_image').removeClass('required');
                }
            });

            $('#parcel_amount').on('input', function() {
                var parcel_amount = $(this).val();
                $(this).val(parcel_amount.replace(/[^0-9]/g, ''));
            });

            $('#quantity').on('input', function() {
                var quantity = $(this).val();
                $(this).val(quantity.replace(/[^0-9]/g, ''));
            });

            $('#account_no').attr('autocomplete', 'off');

            $('#shipping_mode').on('change', function () {
                if(this.value == 3){
                    $('#wallet_id_div').show();
                }else{
                    $('#wallet_id_div').hide();
                }
            });

            $(document).on('change', '#is_wallet', function () {
                if ($(this).is(':checked')) {
                    let shipperName = $('#shipper_name').val().trim();
                    let shipperCnic = $('#shipper_cnic').val().trim();
                    let shipperAddress = $('#shipper_address').val().trim();
                    let shipper_phone_no = $('#shipper_phone_no').val().trim();
                    let shipping_mode_check = $('#shipping_mode').val();
                    let ibanNo = $('#iban_no').val().trim();
                    let accountNo = $('#account_no').val().trim();
                    let bank = $('#bank').val().trim();
                    let chequeImage = $('#cheque_image').val();
                    let complete_shipper_info = parseInt($('#complete_shipper_info').val());

                    // Always validate base fields
                    if (!shipperName || !shipperCnic || !shipperAddress || !shipper_phone_no) {
                        swal({
                            title: 'Missing Information',
                            text: 'Please fill shipper phone, shipper name, CNIC, and address before proceeding.',
                            icon: 'error'
                        });
                        $(this).prop('checked', false);
                        return;
                    }

                    // Extra check for mode 3 if info not already complete
                    if (shipping_mode_check == 3 && complete_shipper_info === 0) {
                        if (!ibanNo || !accountNo || !bank || !chequeImage) {
                            swal({
                                title: 'Missing Bank Details',
                                text: 'Please fill IBAN, Account No, Bank, and upload Cheque Image.',
                                icon: 'error'
                            });
                            $(this).prop('checked', false);
                            return;
                        }
                    }

                    // Confirmation
                    swal({
                        title: "Continue?",
                        text: "Do you want to Continue with this Shipper?",
                        icon: "warning",
                        buttons: true,
                        dangerMode: false
                    }).then((willContinue) => {
                        if (willContinue) {
                            let formData = new FormData();

                            // If info already complete → only send phone
                            if (complete_shipper_info === 1) {
                                formData.append('shipper_phone_no', shipper_phone_no);
                                formData.append('complete_shipper_info', 1);
                            }
                            // Else send all details
                            else {
                                formData.append('shipper_phone_no', shipper_phone_no);
                                formData.append('shipper_name', shipperName);
                                formData.append('shipper_cnic', shipperCnic);
                                formData.append('shipper_address', shipperAddress);
                                formData.append('shipping_mode', shipping_mode_check);
                                formData.append('iban_no', ibanNo);
                                formData.append('account_no', accountNo);
                                formData.append('bank', bank);
                                formData.append('complete_shipper_info', 0);

                                let chequeFile = $('#cheque_image')[0].files[0];
                                if (chequeFile) {
                                    formData.append('cheque_image', chequeFile);
                                }
                            }

                            $.ajax({
                                url: '{{ route('retail.shipment.RetailAddShipper') }}',
                                method: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            }).done(function (data) {
                                if (data.status == 1 && data.shipper.id) {
                                    $('#wallet_user_id').val(data.shipper.id);
                                    $('#wallet_user_text').text(data.shipper.shipper_name+`(${data.shipper.shipper_phone_no})`);
                                    $('#walletSignupModal').modal('show');
                                } else {
                                    swal({
                                        title: 'Something Went Wrong',
                                        text: 'Contact Admin',
                                        icon: 'error'
                                    });
                                }
                            });

                        } else {
                            $('#is_wallet').prop('checked', false);
                        }
                    });

                }
            });

        });

        $(document).ready(function () {
            $( "#main-form" ).validate({
                errorClass: "danger",
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $('#signup_button').prop('disabled',true);
                    swal({
                        title: "Processing...",
                        text: "Please wait while we process your request.",
                        content: (() => {
                            // Create a container for the spinner
                            let content = document.createElement("div");
                            content.innerHTML = `
                            <div style="display: flex; justify-content: center; align-items: center;">
                                <div class="spinner" style="width: 30px; height: 30px; border: 4px solid rgba(0,0,0,0.2); border-top: 4px solid #3498db; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                            </div>
                        `;
                            return content;
                        })(),
                        buttons: false, // Disable buttons
                        closeOnClickOutside: false, // Disable outside click
                        closeOnEsc: false // Disable escape key
                    });

                    const style = document.createElement("style");
                    style.textContent = `
                    @keyframes spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }`;
                    document.head.appendChild(style);


                    var formData = new FormData();

                    // Add CSRF token
                    formData.append('_token', '{{ csrf_token() }}');


                    var rowId = 0;

                    formData.append(`users[${rowId}][id]`, rowId);
                    formData.append(`users[${rowId}][wallet_user_id]`, $('#wallet_user_id').val());
                    formData.append(`users[${rowId}][name]`, $('#name').val());
                    formData.append(`users[${rowId}][phone]`, $('#phone').val());
                    formData.append(`users[${rowId}][cnic]`, $('#cnic').val());
                    formData.append(`users[${rowId}][email]`, $('#email').val());

                    let cnic_front = $('#cnic_front')[0].files[0];
                    let cnic_back = $('#cnic_back')[0].files[0];

                    if (cnic_front && cnic_back) {
                        formData.append(`users[${rowId}][cnic_front]`,cnic_front);
                        formData.append(`users[${rowId}][cnic_back]`, cnic_back);
                    }


                    $.ajax({
                        url: '{{ route('retail.shipment.profile_wallet') }}',
                        method: 'POST',
                        processData: false,
                        contentType: false,
                        data: formData,
                        success: function (data) {

                            swal.close();

                            if (data.status === 0) {

                                if (data.error) {
                                    var summaryErrorMessages = '<ul style="color: #e56464;">';
                                    $.each(data.error, function (rowId, errors) {

                                        var errorMessages = '<ul>';
                                        summaryErrorMessages += `<li><strong>Row ${rowId}:</strong></li><ul>`;

                                        // Iterate over errors for the row
                                        $.each(errors, function (field, message) {
                                            // Append individual field errors to the row and summary
                                            errorMessages += `<li><strong>${field}:</strong> ${message}</li>`;
                                            summaryErrorMessages += `<li><strong>${field}:</strong> ${message}</li>`;
                                        });

                                        errorMessages += '</ul>';
                                        summaryErrorMessages += '</ul>';
                                    });
                                }
                                summaryErrorMessages += '</ul>';

                                swal({
                                    content: (() => {
                                        let content = document.createElement('div');
                                        content.innerHTML = summaryErrorMessages;
                                        return content;
                                    })(),
                                    title: 'Errors Found!',
                                    icon: 'warning',
                                    className: 'custom-swal-width' // Optional: Use custom class for wider modal
                                });


                                if (data.error_2) {
                                    let summaryErrorMessages = '<ul style="color: #e56464;">';

                                    // Iterate through the rows in error_2
                                    $.each(data.error_2, function (rowId, rowData) {


                                        summaryErrorMessages += `<li>Row ID: ${rowId}</li><ul>`;

                                        // Display the specific errors for each field in the row
                                        if (rowData.message) {
                                            $.each(rowData.message, function (field, messages) {
                                                $.each(messages, function (index, message) {
                                                    summaryErrorMessages += `<li>${field}: ${message}</li>`;
                                                });
                                            });
                                        }

                                        summaryErrorMessages += '</ul>';
                                    });

                                    let scrollableContent = document.createElement('div');
                                    scrollableContent.style.maxHeight = '400px'; // Adjust the height as needed
                                    scrollableContent.style.overflowY = 'auto';  // Add vertical scrolling
                                    scrollableContent.style.padding = '10px';   // Optional: Add padding for readability
                                    scrollableContent.innerHTML = summaryErrorMessages;

                                    swal({
                                        content: scrollableContent,
                                        title: 'Error!',
                                        className: 'custom-swal-width',
                                        text: 'Errors occurred in the following rows.',
                                        icon: 'warning',
                                    });
                                }




                            } else {
                                swal({
                                    title: 'Success',
                                    text: 'Success',
                                    icon: 'success',
                                });
                                var rdUrl = data.output.url ;
                                window.location.href = `{{ url('cod/wallet/finja_dashboard') }}?url=${rdUrl}`;
                            }
                            $('#signup_button').prop('disabled',false);
                        },
                        error: function (xhr) {
                            $('#signup_button').prop('disabled',false);
                            swal.close();

                            console.error(xhr.responseText);
                        }
                    });
                }
            });


        });

        $(document).ready(function () {
            // Monitor checkbox state
            $('#confirm').on('change', function () {
                // Enable or disable the button based on the checkbox state
                if ($(this).is(':checked')) {
                    $('#signup_button').prop('disabled', false); // Enable button
                } else {
                    $('#signup_button').prop('disabled', true); // Disable button
                }
            });
        });

    </script>
@endsection