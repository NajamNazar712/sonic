@extends('client.layout.master')

@section('title', 'Book a Shipment')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    {{--                    {{dd($nsa_error)}}--}}
                    Book a Shipment (NSA Shipments)
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            <div class="alert alert-info">In case of, <br> Out of Service Area: Additional charges may apply. <br> Non Service Area: Shipment may be returned. <br> For assistance, Call: 021-38772222</div>
                            <form id="booking_form" class="form-horizontal" method="POST" action="{{ route('cod.shipment.book.corporate_excel_store') }}" novalidate="novalidate">
                                {{ csrf_field() }}
                                <input type="hidden" name="excel_nsa" value="1">
                                <div class="table-responsive">
                                    <table class='table table-bordered' id='tbl'>
                                        <thead>
                                        <tr>
                                            <th></th>
                                            <th>Consignee City Name</th>
                                            <th>Consignee Name</th>
                                            <th>Consignee Address</th>
                                            <th>Consignee Phone Number 1 (03000000000)</th>
                                            <th>Order ID</th>
                                            <th>Order Date (YYYY-MM-DD)</th>
                                            <th>Item Product Type ID</th>
                                            <th>Item Description</th>
                                            <th>Item Quantity</th>
                                            @if($service_type_check_id == 2 || $service_type_check_id == null)
                                                <th>Replacement Item Product Type ID</th>
                                                <th>Replacement Item Description</th>
                                                <th>Replacement Item Quantity</th>
                                            @endif
                                            <th>Special Instructions</th>
                                            <th>Estimated Weight (kg)</th>
                                            @if($service_type_check_id == 1 || $service_type_check_id == 2 || $service_type_check_id == null)
                                                <th>Amount</th>
                                            @endif
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            $no=1;
                                        @endphp
                                        @foreach($data as $key => $ro)
                                            {{--{{dd($key)}}--}}
                                            <div class="d-none">{!! $no=$no+1!!}</div>
                                            <tr>
                                                @if(isset($nsa_error[$key+2]['msg']))
                                                    <td><button type="button" class="btn btn-icon btn-danger cancel_shipment"><i class="la la-close"></i> </button></td>
                                                    <input type="hidden" name="form[{{$no}}][service_type_id]" value="{{$ro['service_type_id']}}">
                                                    <input type="hidden" name="form[{{$no}}][pickup_address_id]" value="{{$ro['pickup_address_id']}}">
                                                    <input type="hidden" name="form[{{$no}}][return_address_id]" value="{{$ro['return_address_id']}}">


                                                    <input type="hidden" name="form[{{$no}}][information_display]" value="{{$ro['information_display']}}">
                                                    <td><input type="text" name="form[{{$no}}][consignee_city_name]" class="form-control text" value="{{$ro['consignee_city_name']}}" readonly="readonly"></td>
                                                    <td><input type="text" name="form[{{$no}}][consignee_name]" class="form-control text" value="{{$ro['consignee_name']}}" readonly="readonly"></td>
                                                    <td><textarea type="text" name="form[{{$no}}][consignee_address]" class="form-control text" readonly="readonly">{{$ro['consignee_address']}}</textarea><font color="red">{{$nsa_error[$key+2]['msg']}}</font></td>
                                                    <td><input type="text" name="form[{{$no}}][consignee_phone_number_1]" class="form-control phone" value="{{$ro['consignee_phone_number_1']}}" readonly="readonly"></td>
                                                    <input type="hidden" name="form[{{$no}}][consignee_phone_number_2]" value="{{$ro['consignee_phone_number_2']}}">
                                                    <input type="hidden" name="form[{{$no}}][consignee_email_address]" value="{{$ro['consignee_email_address']}}">
                                                    <input type="hidden" name="form[{{$no}}][self_collection]" value="{{$ro['self_collection']}}">
                                                    <input type="hidden" name="form[{{$no}}][open_shipment]" value="{{$ro['open_shipment']}}">
                                                    <td><input type="text" name="form[{{$no}}][order_id]" class="form-control text" value="{{$ro['order_id']}}" readonly="readonly"></td>
                                                    <td><input type="text" name="form[{{$no}}][order_date]" class="form-control text" value="{{$ro['order_date']}}" readonly="readonly"></td>
                                                    <td><input type="text" name="form[{{$no}}][item_product_type_id]" class="form-control number" value="{{$ro['item_product_type_id']}}" readonly="readonly"></td>
                                                    <td><input type="text" name="form[{{$no}}][item_description]" class="form-control text" value="{{$ro['item_description']}}" readonly="readonly"></td>
                                                    <td><input type="text" name="form[{{$no}}][item_quantity]" class="form-control number" value="{{$ro['item_quantity']}}" readonly="readonly"></td>
                                                    <input type="hidden" name="form[{{$no}}][item_insurance]" value="{{$ro['item_insurance']}}">
                                                    <input type="hidden" name="form[{{$no}}][item_price]" value="{{$ro['item_price']}}">
                                                    @if($service_type_check_id == 2 || $service_type_check_id == null)
                                                        <td><input type="text" name="form[{{$no}}][replacement_item_product_type_id]" class="form-control number" value="{{$ro['replacement_item_product_type_id']}}" readonly="readonly"></td>
                                                        <td><input type="text" name="form[{{$no}}][replacement_item_description]" class="form-control text" value="{{$ro['replacement_item_description']}}" readonly="readonly"></td>
                                                        <td><input type="text" name="form[{{$no}}][replacement_item_quantity]" class="form-control number" value="{{$ro['replacement_item_quantity']}}" readonly="readonly"></td>
                                                    @endif
                                                    @if($service_type_check_id == 3)
                                                        <input type="hidden" name="form[{{$no}}][item_product_type_id_1]" value="{{$ro['item_product_type_id_1']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_description_1]" value="{{$ro['item_description_1']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_quantity_1]" value="{{$ro['item_quantity_1']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_insurance_1]" value="{{$ro['item_insurance_1']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_price_1]" value="{{$ro['item_price_1']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_product_type_id_2]" value="{{$ro['item_product_type_id_2']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_description_2]" value="{{$ro['item_description_2']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_quantity_2]" value="{{$ro['item_quantity_2']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_insurance_2]" value="{{$ro['item_insurance_2']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_price_2]" value="{{$ro['item_price_2']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_product_type_id_3]" value="{{$ro['item_product_type_id_3']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_description_3]" value="{{$ro['item_description_3']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_quantity_3]" value="{{$ro['item_quantity_3']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_insurance_3]" value="{{$ro['item_insurance_3']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_price_3]" value="{{$ro['item_price_3']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_product_type_id_4]" value="{{$ro['item_product_type_id_4']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_description_4]" value="{{$ro['item_description_4']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_quantity_4]" value="{{$ro['item_quantity_4']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_insurance_4]" value="{{$ro['item_insurance_4']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_price_4]" value="{{$ro['item_price_4']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_product_type_id_5]" value="{{$ro['item_product_type_id_5']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_description_5]" value="{{$ro['item_description_5']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_quantity_5]" value="{{$ro['item_quantity_5']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_insurance_5]" value="{{$ro['item_insurance_5']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_price_5]" value="{{$ro['item_price_5']}}">
                                                    @endif
                                                    <td><textarea type="text" name="form[{{$no}}][special_instructions]" class="form-control text" readonly="readonly">{{$ro['special_instructions']}}</textarea></td>
                                                    <td><input type="text" name="form[{{$no}}][estimated_weight]" class="form-control number" value="{{$ro['estimated_weight']}}" readonly="readonly"></td>
                                                    <input type="hidden" name="form[{{$no}}][shipping_mode_id]" value="{{$ro['shipping_mode_id']}}">
                                                    <input type="hidden" name="form[{{$no}}][same_day_timing_id]" value="{{$ro['same_day_timing_id']}}">
                                                    @if($service_type_check_id == 1 || $service_type_check_id == 2 || $service_type_check_id == null)
                                                        <td><input type="text" name="form[{{$no}}][amount]" class="form-control text" value="{{$ro['amount']}}" readonly="readonly"></td>
                                                    @endif
                                                    @if($service_type_check_id == 3)
                                                        <input type="hidden" name="form[{{$no}}][try_and_buy_charges]" value="{{$ro['try_and_buy_charges']}}">
                                                    @endif
                                                    <input type="hidden" name="form[{{$no}}][payment_mode_id]" value="{{$ro['payment_mode_id']}}">
                                                    <input type="hidden" name="form[{{$no}}][charges_mode_id]" value="{{$ro['charges_mode_id']}}">
                                                    <input type="hidden" name="form[{{$no}}][delivery_type_id]" value="{{$ro['delivery_type_id']}}">
                                                    <input type="hidden" name="form[{{$no}}][pieces_quantity]" value="{{$ro['pieces_quantity']}}">
                                                    <input type="hidden" name="form[{{$no}}][shipper_reference_number_1]" value="{{$ro['shipper_reference_number_1']}}">
                                                    <input type="hidden" name="form[{{$no}}][shipper_reference_number_2]" value="{{$ro['shipper_reference_number_2']}}">
                                                    <input type="hidden" name="form[{{$no}}][shipper_reference_number_3]" value="{{$ro['shipper_reference_number_3']}}">
                                                    <input type="hidden" name="form[{{$no}}][shipper_reference_number_4]" value="{{$ro['shipper_reference_number_4']}}">
                                                    <input type="hidden" name="form[{{$no}}][shipper_reference_number_5]" value="{{$ro['shipper_reference_number_5']}}">
                                                @else
                                                    <input type="hidden" name="form[{{$no}}][service_type_id]" value="{{$ro['service_type_id']}}">
                                                    <input type="hidden" name="form[{{$no}}][pickup_address_id]" value="{{$ro['pickup_address_id']}}">
                                                    <input type="hidden" name="form[{{$no}}][information_display]" value="{{$ro['information_display']}}">
                                                    <input type="hidden" name="form[{{$no}}][consignee_city_name]" value="{{$ro['consignee_city_name']}}">
                                                    <input type="hidden" name="form[{{$no}}][consignee_name]" value="{{$ro['consignee_name']}}">
                                                    <input type="hidden" name="form[{{$no}}][consignee_address]" value="{{$ro['consignee_address']}}">
                                                    <input type="hidden" name="form[{{$no}}][consignee_phone_number_1]" value="{{$ro['consignee_phone_number_1']}}">
                                                    <input type="hidden" name="form[{{$no}}][consignee_phone_number_2]" value="{{$ro['consignee_phone_number_2']}}">
                                                    <input type="hidden" name="form[{{$no}}][consignee_email_address]" value="{{$ro['consignee_email_address']}}">
                                                    <input type="hidden" name="form[{{$no}}][self_collection]" value="{{$ro['self_collection']}}">
                                                    <input type="hidden" name="form[{{$no}}][open_shipment]" value="{{$ro['open_shipment']}}">
                                                    <input type="hidden" name="form[{{$no}}][order_id]" value="{{$ro['order_id']}}">
                                                    <input type="hidden" name="form[{{$no}}][order_date]" value="{{$ro['order_date']}}">
                                                    <input type="hidden" name="form[{{$no}}][item_product_type_id]" value="{{$ro['item_product_type_id']}}">
                                                    <input type="hidden" name="form[{{$no}}][item_description]" value="{{$ro['item_description']}}">
                                                    <input type="hidden" name="form[{{$no}}][item_quantity]" value="{{$ro['item_quantity']}}">
                                                    <input type="hidden" name="form[{{$no}}][item_insurance]" value="{{$ro['item_insurance']}}">
                                                    <input type="hidden" name="form[{{$no}}][item_price]" value="{{$ro['item_price']}}">
                                                    @if($service_type_check_id == 2 || $service_type_check_id == null)
                                                        <input type="hidden" name="form[{{$no}}][replacement_item_product_type_id]" value="{{$ro['replacement_item_product_type_id']}}">
                                                        <input type="hidden" name="form[{{$no}}][replacement_item_description]" value="{{$ro['replacement_item_description']}}">
                                                        <input type="hidden" name="form[{{$no}}][replacement_item_quantity]" value="{{$ro['replacement_item_quantity']}}">
                                                    @endif
                                                    @if($service_type_check_id == 3)
                                                        <input type="hidden" name="form[{{$no}}][item_product_type_id_1]" value="{{$ro['item_product_type_id_1']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_description_1]" value="{{$ro['item_description_1']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_quantity_1]" value="{{$ro['item_quantity_1']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_insurance_1]" value="{{$ro['item_insurance_1']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_price_1]" value="{{$ro['item_price_1']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_product_type_id_2]" value="{{$ro['item_product_type_id_2']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_description_2]" value="{{$ro['item_description_2']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_quantity_2]" value="{{$ro['item_quantity_2']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_insurance_2]" value="{{$ro['item_insurance_2']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_price_2]" value="{{$ro['item_price_2']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_product_type_id_3]" value="{{$ro['item_product_type_id_3']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_description_3]" value="{{$ro['item_description_3']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_quantity_3]" value="{{$ro['item_quantity_3']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_insurance_3]" value="{{$ro['item_insurance_3']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_price_3]" value="{{$ro['item_price_3']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_product_type_id_4]" value="{{$ro['item_product_type_id_4']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_description_4]" value="{{$ro['item_description_4']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_quantity_4]" value="{{$ro['item_quantity_4']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_insurance_4]" value="{{$ro['item_insurance_4']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_price_4]" value="{{$ro['item_price_4']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_product_type_id_5]" value="{{$ro['item_product_type_id_5']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_description_5]" value="{{$ro['item_description_5']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_quantity_5]" value="{{$ro['item_quantity_5']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_insurance_5]" value="{{$ro['item_insurance_5']}}">
                                                        <input type="hidden" name="form[{{$no}}][item_price_5]" value="{{$ro['item_price_5']}}">
                                                    @endif
                                                    <input type="hidden" name="form[{{$no}}][special_instructions]" value="{{$ro['special_instructions']}}">
                                                    <input type="hidden" name="form[{{$no}}][estimated_weight]" value="{{$ro['estimated_weight']}}">
                                                    <input type="hidden" name="form[{{$no}}][shipping_mode_id]" value="{{$ro['shipping_mode_id']}}">
                                                    <input type="hidden" name="form[{{$no}}][same_day_timing_id]" value="{{$ro['same_day_timing_id']}}">
                                                    @if($service_type_check_id == 1 || $service_type_check_id == 2 || $service_type_check_id == null)
                                                        <input type="hidden" name="form[{{$no}}][amount]" value="{{$ro['amount']}}">
                                                    @endif
                                                    @if($service_type_check_id == 3)
                                                        <input type="hidden" name="form[{{$no}}][try_and_buy_charges]" value="{{$ro['try_and_buy_charges']}}">
                                                    @endif
                                                    <input type="hidden" name="form[{{$no}}][payment_mode_id]" value="{{$ro['payment_mode_id']}}">
                                                    <input type="hidden" name="form[{{$no}}][charges_mode_id]" value="{{$ro['charges_mode_id']}}">
                                                    <input type="hidden" name="form[{{$no}}][delivery_type_id]" value="{{$ro['delivery_type_id']}}">
                                                    <input type="hidden" name="form[{{$no}}][pieces_quantity]" value="{{$ro['pieces_quantity']}}">
                                                    <input type="hidden" name="form[{{$no}}][shipper_reference_number_1]" value="{{$ro['shipper_reference_number_1']}}">
                                                    <input type="hidden" name="form[{{$no}}][shipper_reference_number_2]" value="{{$ro['shipper_reference_number_2']}}">
                                                    <input type="hidden" name="form[{{$no}}][shipper_reference_number_3]" value="{{$ro['shipper_reference_number_3']}}">
                                                    <input type="hidden" name="form[{{$no}}][shipper_reference_number_4]" value="{{$ro['shipper_reference_number_4']}}">
                                                    <input type="hidden" name="form[{{$no}}][shipper_reference_number_5]" value="{{$ro['shipper_reference_number_5']}}">
                                                @endif
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row mt-2">
                                    <div class="col">
                                        <div class="form-group text-center">
                                            <button type="submit" name="book" class="btn btn-primary book" value="Book">Book</button>
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
    <style>
        form .text{
            width :auto;
        }
        form .number{
            width :80px;
        }
        form .phone{
            width :150px;
        }
        form .date{
            width :110px;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/spinner/jquery.bootstrap-touchspin.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            $('#tbl').on('click', '.cancel_shipment', function(e){
                $(this).closest('tr').remove()
            });
        });
    </script>
@endsection