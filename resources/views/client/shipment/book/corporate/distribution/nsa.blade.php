@extends('client.layout.master')

@section('title', 'Book a Shipment')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Book a Shipment (NSA Shipments)
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            <div class="alert alert-info">In case of, <br> Out of Service Area: Additional charges may apply. <br> Non Service Area: Shipment may be returned. <br> For assistance, Call: 021-38772222</div>
                            <form id="booking_form" class="form-horizontal" method="POST" action="{{ route('cod.shipment.book.corporate_excel_distribution.store') }}" novalidate="novalidate">
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
                                            @for($i = 1; $i <= 10; $i++)
                                                <th>Distribution Product Name {{$i}}</th>
                                                <th>Distribution Items Per SKU {{$i}}</th>
                                                <th>Distribution Units Per Item {{$i}}</th>
                                                <th>Distribution Item Insurance {{$i}}</th>
                                                <th>Distribution Product Value {{$i}}</th>
                                            @endfor
                                            <th>Estimated Weight (kg)</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            $no=1;
                                        @endphp
                                        @foreach($data as $key => $ro)
                                            <div class="d-none">{!! $no=$no+1!!}</div>
                                            <tr>
                                                @if(isset($nsa_error[$key+2]['msg']))
                                                    <td><button type="button" class="btn btn-icon btn-danger cancel_shipment"><i class="la la-close"></i> </button></td>
                                                    <input type="hidden" name="form[{{$no}}][pickup_address_id]" value="{{$ro['pickup_address_id']}}">


                                                    <input type="hidden" name="form[{{$no}}][information_display]" value="{{$ro['information_display']}}">
                                                    <td><input type="text" name="form[{{$no}}][consignee_city_name]" class="form-control text" value="{{$ro['consignee_city_name']}}" readonly="readonly"></td>
                                                    <td><input type="text" name="form[{{$no}}][consignee_name]" class="form-control text" value="{{$ro['consignee_name']}}" readonly="readonly"></td>
                                                    <td><textarea type="text" name="form[{{$no}}][consignee_address]" class="form-control text" readonly="readonly">{{$ro['consignee_address']}}</textarea><font color="red">{{$nsa_error[$key+2]['msg']}}</font></td>
                                                    <td><input type="text" name="form[{{$no}}][consignee_phone_number_1]" class="form-control phone" value="{{$ro['consignee_phone_number_1']}}" readonly="readonly"></td>
                                                    <input type="hidden" name="form[{{$no}}][consignee_phone_number_2]" value="{{$ro['consignee_phone_number_2']}}">
                                                    <input type="hidden" name="form[{{$no}}][consignee_email_address]" value="{{$ro['consignee_email_address']}}">
                                                    <td><input type="text" name="form[{{$no}}][order_id]" class="form-control text" value="{{$ro['order_id']}}" readonly="readonly"></td>
                                                    <td><input type="text" name="form[{{$no}}][order_date]" class="form-control text" value="{{$ro['order_date']}}" readonly="readonly"></td>
                                                    @for($i = 1; $i <= 10; $i++)
                                                       <td>{!! Form::text('form[' . $no . '][distribution_product_type_id_'.$i.']', $ro['distribution_product_type_id_'.$i],['class' => 'form-control','style'=>'width:144px','readonly' => 'readonly']) !!}</td>
                                                        <td>{!! Form::text('form[' . $no . '][distribution_item_per_sku_'.$i.']', $ro['distribution_item_per_sku_'.$i],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                                        <td>{!! Form::text('form[' . $no . '][distribution_unit_per_item_'.$i.']', $ro['distribution_unit_per_item_'.$i],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                                        <td>{!! Form::text('form[' . $no . '][distribution_item_insurance_'.$i.']', $ro['distribution_item_insurance_'.$i],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                                        <td>{!! Form::text('form[' . $no . '][distribution_item_price_'.$i.']', $ro['distribution_item_price_'.$i],['class' => 'form-control','style'=>'width:100px','readonly' => 'readonly']) !!}</td>
                                                    @endfor
                                                    <td><input type="text" name="form[{{$no}}][estimated_weight]" class="form-control number" value="{{$ro['estimated_weight']}}" readonly="readonly"></td>
                                                    <input type="hidden" name="form[{{$no}}][shipping_mode_id]" value="{{$ro['shipping_mode_id']}}">
                                                    <input type="hidden" name="form[{{$no}}][payment_mode_id]" value="{{$ro['payment_mode_id']}}">
                                                    <input type="hidden" name="form[{{$no}}][charges_mode_id]" value="{{$ro['charges_mode_id']}}">
                                                    <input type="hidden" name="form[{{$no}}][delivery_type_id]" value="{{$ro['delivery_type_id']}}">
                                                @else
                                                    <input type="hidden" name="form[{{$no}}][pickup_address_id]" value="{{$ro['pickup_address_id']}}">
                                                    <input type="hidden" name="form[{{$no}}][information_display]" value="{{$ro['information_display']}}">
                                                    <input type="hidden" name="form[{{$no}}][consignee_city_name]" value="{{$ro['consignee_city_name']}}">
                                                    <input type="hidden" name="form[{{$no}}][consignee_name]" value="{{$ro['consignee_name']}}">
                                                    <input type="hidden" name="form[{{$no}}][consignee_address]" value="{{$ro['consignee_address']}}">
                                                    <input type="hidden" name="form[{{$no}}][consignee_phone_number_1]" value="{{$ro['consignee_phone_number_1']}}">
                                                    <input type="hidden" name="form[{{$no}}][consignee_phone_number_2]" value="{{$ro['consignee_phone_number_2']}}">
                                                    <input type="hidden" name="form[{{$no}}][consignee_email_address]" value="{{$ro['consignee_email_address']}}">
                                                    <input type="hidden" name="form[{{$no}}][order_id]" value="{{$ro['order_id']}}">
                                                    <input type="hidden" name="form[{{$no}}][order_date]" value="{{$ro['order_date']}}">
                                                    @for($i = 1; $i <= 10; $i++)
                                                        <input type="hidden" name="form[{{$no}}][distribution_product_type_id_{{$i}}]" value="{{$ro['distribution_product_type_id_'.$i]}}">
                                                        <input type="hidden" name="form[{{$no}}][distribution_item_per_sku_{{$i}}]" value="{{$ro['distribution_item_per_sku_'.$i]}}">
                                                        <input type="hidden" name="form[{{$no}}][distribution_unit_per_item_{{$i}}]" value="{{$ro['distribution_unit_per_item_'.$i]}}">
                                                        <input type="hidden" name="form[{{$no}}][distribution_item_insurance_{{$i}}]" value="{{$ro['distribution_item_insurance_'.$i]}}">
                                                        <input type="hidden" name="form[{{$no}}][distribution_item_price_{{$i}}]" value="{{$ro['distribution_item_price_'.$i]}}">
                                                    @endfor
                                                    <input type="hidden" name="form[{{$no}}][estimated_weight]" value="{{$ro['estimated_weight']}}">
                                                    <input type="hidden" name="form[{{$no}}][shipping_mode_id]" value="{{$ro['shipping_mode_id']}}">
                                                    <input type="hidden" name="form[{{$no}}][payment_mode_id]" value="{{$ro['payment_mode_id']}}">
                                                    <input type="hidden" name="form[{{$no}}][charges_mode_id]" value="{{$ro['charges_mode_id']}}">
                                                    <input type="hidden" name="form[{{$no}}][delivery_type_id]" value="{{$ro['delivery_type_id']}}">
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