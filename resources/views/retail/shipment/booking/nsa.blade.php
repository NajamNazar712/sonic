@extends('retail.layout.master')

@section('title', 'Retail Book a Shipment')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    {{--                    {{dd($nsa_error)}}--}}
                    Retail Book a Shipment (NSA Shipments)
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            <div class="alert alert-info">In case of, <br> Out of Service Area: Additional charges may apply. <br> Non Service Area: Shipment may be returned. <br> For assistance, Call: 021-38772222</div>
                            <form id="booking_form" class="form-horizontal" method="POST" action="{{ route('retail.shipment.book.excel_store') }}" novalidate="novalidate">
                                {{ csrf_field() }}
                                <input type="hidden" name="excel_nsa" value="1">
{{--                                <input type="hidden" name="business_category_id" value="{{$business_category_id}}">--}}
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
                                            <th>Product ID</th>
                                            <th>Quantity</th>
                                            <th>Special Instructions</th>
                                            <th>Estimated Weight (kg)</th>
                                            <th>Parcel Value</th>
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
                                                    <input type="hidden" name="form[{{$no}}][product_id]" value="{{$ro['product_id']}}">
                                                    <input type="hidden" name="form[{{$no}}][business_category_id]" value="{{$ro['business_category_id']}}">
                                                    <input type="hidden" name="form[{{$no}}][shipping_mode_id]" value="{{$ro['shipping_mode_id']}}">
                                                    <input type="hidden" name="form[{{$no}}][volumetric_weight]" value="{{$ro['volumetric_weight']}}">
                                                    <input type="hidden" name="form[{{$no}}][weight]" value="{{$ro['weight']}}">
                                                    <input type="hidden" name="form[{{$no}}][length]" value="{{$ro['length']}}">
                                                    <input type="hidden" name="form[{{$no}}][breadth]" value="{{$ro['breadth']}}">
                                                    <input type="hidden" name="form[{{$no}}][height]" value="{{$ro['height']}}">
                                                    <input type="hidden" name="form[{{$no}}][length]" value="{{$ro['length']}}">
                                                    <input type="hidden" name="form[{{$no}}][pieces]" value="{{$ro['pieces']}}">
                                                    <input type="hidden" name="form[{{$no}}][payment_mode_id]" value="{{$ro['payment_mode_id']}}">
                                                    <input type="hidden" name="form[{{$no}}][charges_mode_id]" value="{{$ro['charges_mode_id']}}">
                                                    <input type="hidden" name="form[{{$no}}][shipper_cell_number]" value="{{$ro['shipper_cell_number']}}">
                                                    <input type="hidden" name="form[{{$no}}][shipper_name]" value="{{$ro['shipper_name']}}">
                                                    <input type="hidden" name="form[{{$no}}][shipper_cnic]" value="{{$ro['shipper_cnic']}}">
                                                    <input type="hidden" name="form[{{$no}}][shipper_address]" value="{{$ro['shipper_address']}}">
                                                    <input type="hidden" name="form[{{$no}}][consignee_cnic]" value="{{$ro['consignee_cnic']}}">
                                                    <input type="hidden" name="form[{{$no}}][insurance_offered]" value="{{$ro['insurance_offered']}}">
                                                    <input type="hidden" name="form[{{$no}}][insurance_value]" value="{{$ro['insurance_value']}}">
                                                    <input type="hidden" name="form[{{$no}}][packaging_charges]" value="{{$ro['packaging_charges']}}">
                                                    <input type="hidden" name="form[{{$no}}][trax_box_id]" value="{{$ro['trax_box_id']}}">
                                                    <input type="hidden" name="form[{{$no}}][iban_number]" value="{{$ro['iban_number']}}">
                                                    <input type="hidden" name="form[{{$no}}][account_number]" value="{{$ro['account_number']}}">
                                                    <input type="hidden" name="form[{{$no}}][bank_id]" value="{{$ro['bank_id']}}">
                                                    <input type="hidden" name="form[{{$no}}][admin_discount]" value="{{$ro['admin_discount']}}">
                                                    <input type="hidden" name="form[{{$no}}][admin_discount_type]" value="{{$ro['admin_discount_type']}}">



                                                    <td><input type="text" name="form[{{$no}}][destination]" class="form-control text" value="{{$ro['destination']}}" readonly="readonly"></td>
                                                    <td><input type="text" name="form[{{$no}}][consignee_name]" class="form-control text" value="{{$ro['consignee_name']}}" readonly="readonly"></td>
                                                    <td><textarea type="text" name="form[{{$no}}][consignee_address]" class="form-control text" readonly="readonly">{{$ro['consignee_address']}}</textarea><font color="red">{{$nsa_error[$key+2]['msg']}}</font></td>
                                                    <td><input type="text" name="form[{{$no}}][consignee_cell_number]" class="form-control phone" value="{{$ro['consignee_cell_number']}}" readonly="readonly"></td>
                                                    <td><input type="text" name="form[{{$no}}][order_id]" class="form-control text" value="{{$ro['order_id']}}" readonly="readonly"></td>
                                                    <td><input type="text" name="form[{{$no}}][product_id]" class="form-control number" value="{{$ro['product_id']}}" readonly="readonly"></td>
                                                    <td><input type="text" name="form[{{$no}}][quantity]" class="form-control number" value="{{$ro['quantity']}}" readonly="readonly"></td>
                                                    <td><textarea type="text" name="form[{{$no}}][special_instruction]" class="form-control text">{{$ro['special_instruction']}}</textarea></td>
                                                    <td><input type="text" name="form[{{$no}}][weight]" class="form-control number" value="{{$ro['weight']}}" readonly="readonly"></td>
                                                    <td><input type="text" name="form[{{$no}}][parcel_amount]" class="form-control text" value="{{$ro['parcel_amount']}}" readonly="readonly"></td>
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