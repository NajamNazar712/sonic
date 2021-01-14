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
                        <form id="booking_form" class="form-horizontal" method="POST" action="{{ route('retail.shipment.book.store') }}" novalidate="novalidate">
                            {{ csrf_field() }}
                            <div class="row">
                                <div id="consignment_info" class="col-3 border">
                                    <h4 id="shipper_header_info" class="form-section mb-2 text-center">Consignment Info</h4>
                                    <div class="form-group">
                                        <select name="product" id="product" class="select2 form-control" data-rule-required="true" data-msg-required="Shipment is required">
                                            @foreach($products as $product)
                                                <option value="{{$product->id}}">{{$product->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <input name="shipper_account_no" class="form-control number" id="shipper_account_no" placeholder="Shipper Account No" value="">
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
                                        <input  type="text" name="pieces" class="form-control text-center pieces" placeholder="Pieces*" data-rule-required="true" data-msg-required="Pieces is required" data-toggle="tooltip" data-placement="top" title="" data-original-title="Here you enter the no. of individual flyers or boxes your shipment is separated into, so each can have it's own indentity slip and be accounted for.">
                                    </div>
                                    <div class="form-group">
                                        <select name="payment_mode" id="payment_mode" class="select2 form-control" data-rule-required="true" data-msg-required="Payment Mode is required">
                                            @foreach($payment_modes as $payment_mode)
                                                <option value="{{$payment_mode->id}}">{{$payment_mode->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
{{--                                    <div class="form-group">--}}
{{--                                        <input name="payment_transaction_id" class="form-control number" id="payment_transaction_id" placeholder="Payment Transaction ID" value=""  data-rule-required="true" data-msg-required="Payment Transaction ID is required">--}}
{{--                                    </div>--}}
                                </div>
                                <div id="consignee_shipper_info" class="ml-1 col-6 border">
                                    <h4 id="shipper_header_info" class="form-section mb-2 text-center">Consignee & Shipper Info</h4>

                                    <div class="form-group col-6">
                                        <input type="text" name="shipper_phone_no" id="shipper_phone_no" class="form-control phone" placeholder="Shipper Cell Number*" data-rule-required="true" data-msg-required="Shipper Cell Number is required">
                                    </div>
                                    <div class="form-group col-6">
                                        <input type="text" name="shipper_name" id="shipper_name" class="form-control shipper_name" placeholder="Shipper Name*" data-rule-required="true" data-msg-required="Shipper Name is required">
                                    </div>
                                    <div class="form-group col-6">
                                        <input type="text" name="shipper_cnic" id="shipper_cnic" class="form-control cnic" placeholder="Shipper CNIC*" data-rule-required="true" data-msg-required="Shipper CNIC is required">
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
                                        <textarea name="consignee_address" id="consignee_address" class="form-control address" rows="2" placeholder="Consignee Address*" data-rule-required="true" data-msg-required="Consignee Address is required" data-rule-maxlength="255" data-msg-maxlength="Consignee Address can be maximum 255 characters"></textarea>
                                    </div>
                                    <div class="col">
                                        <div class="row justify-content-end">
                                            <div class="form-group col-6">
                                                <select name="insurance_offered" id="insurance_offered" class="select2 form-control" data-rule-required="true" data-msg-required="Insurance Offered is required">
                                                    <option value="1">Yes</option>
                                                    <option value="0">No</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-6">
                                                <input type="text" name="weight_charges" id="weight_charges" class="form-control decimal" placeholder="Weight Charges*" data-rule-required="true" data-msg-required="Weight Charges is required">
                                            </div>
                                        </div>
                                        <div class="row d-none" id="insurance_amount_div">
                                            <div class="form-group col-6">
                                                <input type="text" name="insurance_amount" class="form-control decimal" placeholder="Insurance Amount*" data-rule-required="true" data-msg-required="Insurance Amount is required">
                                            </div>
                                        </div>
                                        <div class="row justify-content-end">
                                            <div class="form-group col-6 d-none" id="trax_box_div">
                                                <select name="trax_box" id="trax_box" class="select2 form-control" data-rule-required="true" data-msg-required="Trax Box is required">
                                                    @foreach($trax_boxes as $trax_box)
                                                        <option value="{{$trax_box->id}}">{{$trax_box->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-6">
                                                <input type="text" name="cash_handling_charges" id="cash_handling_charges" class="form-control decimal" placeholder="Cash Handling Charges*" data-rule-required="true" data-msg-required="Cash Handling Charges is required">
                                            </div>
                                        </div>
                                        <div class="row justify-content-end">
                                            <div class="form-group col-6">
                                                <input type="text" name="fuel_surcharge" id="fuel_surcharge" class="form-control decimal" placeholder="Fuel Surcharge*" data-rule-required="true" data-msg-required="Fuel Surcharge is required">
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
                                            <input type="text" name="total_charges" id="total_charges" class="form-control decimal" placeholder="Total Charges" disabled>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="gst_charges" id="gst_charges" class="form-control decimal" placeholder="GST Charges" disabled>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="total_amount" id="total_amount" class="form-control decimal" placeholder="Total Amount" disabled>
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
            });
            var overland = false;
            $('#shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Product*"
            }).bind('change', function () {
                var id = parseInt($(this).val());
                if(id === 1){
                    $('#domestic_overland_destination_div').removeClass('d-none');
                    $('#domestic_destination_div').addClass('d-none');
                    overland = true;
                }
                else{
                    $('#domestic_destination_div').removeClass('d-none');
                    $('#domestic_overland_destination_div').addClass('d-none');
                    if(id == 5){
                        $('#trax_box_div').removeClass('d-none');
                    }
                    else{
                        $('#trax_box_div').addClass('d-none');
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
                max: 10,
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
                dropdownParent:$('#booking_form')
            });


            $(".phone").inputmask({'mask': "9999-9999999", 'clearIncomplete': true});
            $(".cnic").inputmask({'mask': "99999-9999999-9", 'clearIncomplete': true});

            $('.amount').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            $('.decimal').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 3,
                'min': 0.01,
                'max': 10000000
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
                    $('#save').addClass('d-none');
                    $('#book_and_print').removeClass('d-none');
                }
            });
            var shipper_info = false;
            $('#shipper_account_no').on('change', function () {
                if(this.value !== '' && this.value != null && shipper_info === false){
                    $.ajax({
                        url: '{!! route('retail.shipment.shipper_info') !!}',
                        method: 'POST',
                        data: {
                            'shipper_account_no': this.value,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                        .done(function (data) {
                            if(data.status){
                                $('#shipper_account_no').val(data.details.shipper_account_no);
                                $('#shipper_phone_no').val(data.details.shipper_phone_no);
                                $('#shipper_name').val(data.details.shipper_name);
                                $('#shipper_cnic').val(data.details.shipper_cnic);
                                $('#shipper_address').val(data.details.shipper_address);
                                shipper_info = true;
                            }
                        });
                }
            });

            $('#shipper_phone_no').on('change', function () {
                if(this.value !== '' && this.value != null && shipper_info === false){
                    $.ajax({
                        url: '{!! route('retail.shipment.shipper_info') !!}',
                        method: 'POST',
                        data: {
                            'shipper_phone_no': this.value,
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                        .done(function (data) {
                            if(data.status){
                                $('#shipper_account_no').val(data.details.shipper_account_no);
                                $('#shipper_phone_no').val(data.details.shipper_phone_no);
                                $('#shipper_name').val(data.details.shipper_name);
                                $('#shipper_cnic').val(data.details.shipper_cnic);
                                $('#shipper_address').val(data.details.shipper_address);
                                shipper_info = true;
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
                        $('#cash_handling_charges').val('');
                        $('#fuel_surcharge').val('');
                        $('#total_charges').val('');
                        $('#gst_charges').val('');
                        $('#total_amount').val('');
                        $('#trax_box').val('').trigger('change');
                        $('#insurance_offered').val('').trigger('change');

                    $('#book_button').val(1);
                    $('#book').attr('disabled', false);
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
            var city_id = null;
            $('#calculate_rates').on('click', function () {
                if(overland){
                    city_id = $('#domestic_overland_destination').val();
                }
                else{
                    city_id = $('#domestic_destination').val();
                }
                if(city_id != '' && $('#weight_charges').val() != '' && $('#cash_handling_charges').val() != '' && $('#fuel_surcharge').val() != ''){
                    var weight_charges = parseFloat($('#weight_charges').val());
                    var cash_handling_charges = parseFloat($('#cash_handling_charges').val());
                    var fuel_surcharge = parseFloat($('#fuel_surcharge').val());

                    var total_charges = weight_charges + cash_handling_charges + fuel_surcharge;
                    $.ajax({
                        url: '{!! route('retail.shipment.book.calculate_rates') !!}',
                        method: 'POST',
                        data: {
                            'total_charges': total_charges,
                            'city_id': parseInt(city_id),
                            '_token': '{{ csrf_token() }}'
                        }
                    })
                        .done(function (data) {
                            if(data.status){
                                $('#total_charges').val(data.details.total_charges);
                                $('#gst_charges').val(data.details.gst_charges);
                                $('#total_amount').val(data.details.total_amount);
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