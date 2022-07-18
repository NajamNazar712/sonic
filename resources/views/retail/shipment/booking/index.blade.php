@extends('retail.layout.master')

@section('title', 'Booking Form')

@section('content')
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
                        <div class="alert alert-danger" id="consignee_address_error" style="display: none">
                        </div>
                        <form id="booking_form" class="form-horizontal" method="POST" action="{{ route('retail.shipment.book.store') }}" novalidate="novalidate" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="row">
                                <div id="consignment_info" class="col-3 border">
                                    <h4 id="shipper_header_info" class="form-section mb-2 text-center">Consignment Info</h4>
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
                                    <div class="form-group col-6">
                                        <input type="text" name="shipper_name" id="shipper_name" class="form-control shipper_name" placeholder="Shipper Name*" data-rule-required="true" data-msg-required="Shipper Name is required">
                                    </div>
                                    <div class="form-group col-6">
                                        <input type="text" name="shipper_cnic" id="shipper_cnic" class="form-control cnic" placeholder="Shipper CNIC">
                                    </div>
                                    <div class="form-group col">
                                        <textarea name="shipper_address" class="form-control address" id="shipper_address" rows="2" placeholder="Shipper Address*" data-rule-required="true" data-msg-required="Shipper Address is required" data-rule-maxlength="255" data-msg-maxlength="Shipper Address can be maximum 255 characters"></textarea>
                                    </div>
                                    <div class="form-group col-6">
                                        <input type="text" name="consignee_phone_no" id="consignee_phone_no" class="form-control phone" placeholder="Consignee Cell Number*" data-rule-required="true" data-msg-required="Consignee Cell Number is required">
                                    </div>
                                    <div class="form-group col-6">
                                        <input type="text" name="consignee_name" id="consignee_name" class="form-control consignee_name" placeholder="Consignee Name*" data-rule-required="true" data-msg-required="Consignee Name is required">
                                    </div>
                                    <div class="form-group col-6">
                                        <input type="text" name="consignee_cnic" id="consignee_cnic" class="form-control cnic" placeholder="Consignee CNIC">
                                    </div>
                                    <div class="form-group col">
                                        {{-- <textarea name="consignee_address" id="consignee_address" class="form-control address" rows="2" placeholder="Consignee Address*" data-rule-required="true" data-msg-required="Consignee Address is required" data-rule-maxlength="255" data-msg-maxlength="Consignee Address can be maximum 255 characters"></textarea> --}}
                                        <textarea id="consignee_address" name="consignee_address" class="form-control" placeholder="Consignee Address*"  rows="5"></textarea>
                                    </div>
                                    <div class="col">
                                        <div class="row d-none" id="cod_check">
                                            <div class="form-group col-6">
                                                <input type="text" name="cod" id="cod" class="form-control rounded-right amount" placeholder="COD Amount*" data-rule-required="true" data-msg-required="COD Amount is required">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-6">
                                                <select name="insurance_offered" id="insurance_offered" class="select2 form-control" data-rule-required="true" data-msg-required="Insurance Offered is required">
{{--                                                    <option value="1">Yes</option>--}}
                                                    <option value="0">No</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-6 d-none" id="insurance_amount_div">
                                                <input type="text" name="insurance_amount" id="insurance_amount" class="form-control decimal" placeholder="Insurance Amount*" data-rule-required="true" data-msg-required="Insurance Amount is required">
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
                                        <div class="row border-dashed">
                                            <div class="form-group col-6">
                                                <input type="text" name="weight_charges" id="weight_charges" class="form-control decimal" placeholder="Weight Charges*" data-rule-required="true" data-msg-required="Weight Charges is required">
                                            </div>
                                            <div class="form-group col-6">
                                                <input type="text" name="fuel_surcharge" id="fuel_surcharge" class="form-control fuel_decimal" placeholder="Fuel Surcharge*" data-rule-required="true" data-msg-required="Fuel Surcharge is required">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="external_info" class="ml-1 col border">
                                    <div class="col pt-5 mt-2 mb-3">
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
                                    <div class="col pt-5">
                                        <div class="form-group">
                                            <input type="text" name="total_charges_without_gst" id="total_charges_without_gst" class="form-control" placeholder="Charges" disabled>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="gst" id="gst" class="form-control" placeholder="GST" disabled>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="total_charges" id="total_charges" class="form-control" placeholder="Total Charges" disabled>
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
                                        <input type="text" class="form-control iban required" placeholder="(e.g: PK37MEZN0001220100004069)" value="" name="iban_no" id="iban_no" data-rule-maxlength="24" data-rule-maxlength-message="Max character length 24">
                                    </div>
                                    <div class="form-group col">
                                        <label for="account_name">Account Number:
                                            <span class="danger">*</span></label>
                                        <input type="text" class="form-control required" value="" name="account_no" id="account_no" placeholder="Account Number*">
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
        $(document).ready(function () {
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

            $('#product').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Shipment*",
                allowClear:true
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
                }
                else{
                    if(shipping_mode == 1)
                    {
                        alert(1);
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

            $('#insurance_offered').select2({
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
                                $('#shipper_name').val(data.details.shipper_name);
                                $('#shipper_cnic').val(data.details.shipper_cnic);
                                $('#shipper_address').val(data.details.shipper_address);
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
                                else if(cod == true && ($('#iban_no').val() == null || $('#iban_no').val() == '') && $('#shipping_mode').val() == 3){
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
                        });
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
                        $('#weight_charges').val('');
                        // $('#cash_handling_charges').val('');
                        $('#fuel_surcharge').val('');
                        $('#total_charges_without_gst').val('');
                        $('#gst').val('');
                        $('#total_charges').val('');
                        $('#insurance_amount').val('');
                        $('#cod').val('');
                        $('#trax_box').val('').trigger('change');
                        $('#insurance_offered').val('').trigger('change');

                    $('#book_button').val(1);
                    $('#book').attr('disabled', false);
               }
            });

            $('#booking_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                rules: {
                    consignee_address: {
                        remote: {
                            url: '{{route('retail.shipment.book.address_verify')}}',
                            data: {
                                city_id: function () {
                                    return $("#domestic_destination").val();
                                },
                            },
                            dataFilter: function(data) {
                                // var json = JSON.parse(data);
                                // if(json.status === "true") {
                                //     return true;
                                // }
                                // return "\"" + json.error + "\"";

                                return true;


                            },
                            complete: function (data) {
                                if (data.responseText) {
                                    var json = JSON.parse(data.responseText);
                                    var er = "";
                                    if (json.invalid_cities) {
                                        $.each(json.invalid_cities, function (key, value) {
                                            er +=  "Select " + key + " in destination for " + value+"<br>";
                                        });
                                        $('#consignee_address_error').html("Address Anomaly detected Keyword "+"<br>"+er.trim() + " For Assistance Call 021-111-118-729");
                                        $('#consignee_address_error').show();

                                    }else{
                                        $('#consignee_address_error').html('');
                                        $('#consignee_address_error').hide();
                                    }
                                }
                            }

                        },
                        maxlength: 255,
                    },
                },
                messages: {
                    consignee_address: {
                        required: "Address Is Required",
                        maxlength :"Address can be maximum 255 characters",
                    },
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
            $('#calculate_rates').on('click', function () {
                if($('#weight_charges').val() != '' && $('#fuel_surcharge').val() != ''){
                    var weight_charges = parseFloat($('#weight_charges').val().replace(/,/g, ''));
                    // var cash_handling_charges = parseFloat($('#cash_handling_charges').val());
                    var fuel_surcharge = parseFloat($('#fuel_surcharge').val().replace(/,/g, ''));

                    // var total_charges_without_gst = weight_charges + cash_handling_charges + fuel_surcharge;
                    var total_charges_without_gst = weight_charges + fuel_surcharge;
                    $.ajax({
                        url: '{!! route('retail.shipment.book.calculate_rates') !!}',
                        method: 'POST',
                        data: {
                            'total_charges_without_gst': total_charges_without_gst,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                        .done(function (data) {
                            if(data.status){
                                $('#total_charges_without_gst').val(data.details.total_charges_without_gst);
                                $('#gst').val(data.details.gst);
                                $('#total_charges').val(data.details.total_charges);
                            }
                        });
                }
            });

            $('#print').on('click', function () {
                if(shipment_ids.length > 0){
                    print(shipment_ids);
                }
            });
        });
    </script>
@endsection