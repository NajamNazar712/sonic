@extends('retail.layout.master')

@section('title', 'Book Excel Retail Shipment(s)')
{{--{{dd($errors)}}--}}
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Errors In Book Excel Retail Shipment(s)
                </h1>
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            {!! Form::model($data,['method' => 'POST', 'route' => 'retail.shipment.book.excel_store']) !!}
                            <div class="table-responsive">
                                <table class='table table-bordered' id='tbl'>
                                    <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Shipment ID</th>
                                        <th>Business Category ID</th>
                                        <th>Product ID</th>
                                        <th>Destination</th>
                                        <th>Volumetric Weight</th>
                                        <th>Weight (kg)</th>
                                        <th>Length (cm)</th>
                                        <th>Breadth (cm)</th>
                                        <th>Height (cm)</th>
                                        <th>Pieces</th>
                                        <th>Payment Mode ID</th>
                                        <th>Charges Mode ID</th>
                                        <th>Shipper Cell Number (03000000000)</th>
                                        <th>Shipper Name</th>
                                        <th>Shipper CNIC</th>
                                        <th>Shipper Address</th>
                                        <th>Consignee Cell Number (03000000000)</th>
                                        <th>Consignee Name</th>
                                        <th>Consignee CNIC</th>
                                        <th>Consignee Address</th>
                                        <th>Order ID</th>
                                        <th>Insurance Offered</th>
                                        <th>Insurance Value</th>
                                        <th>Packaging Charges</th>
                                        <th>Trax Box ID</th>
                                      {{--  <th>Weight Charges</th>
                                        <th>Fuel Surcharge</th>--}}
                                        <th>IBAN Number</th>
                                        <th>Account Number</th>
                                        <th>Bank ID</th>
                                        <th>Special Instruction</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                            $no=1;
                                    @endphp
                                    @foreach($data as $ro)
                                        <tr>
                                            @if(isset($errors[$no+1]))
                                                <td><h4 style="color: red">{!! $no=$no+1!!}</h4><font color="red">{{ 'Error(s) in this row' }}</font></td>
                                            @else
                                                <td>{!! $no=$no+1!!}</td>
                                            @endif
                                            @if(isset($errors[$no]['product_id']))
                                                <td>{!! Form::select('form[' . $no . '][product_id]',$products,null, ['class' => 'form-control is-invalid product_id select2','id'=>'product_id','style'=>'width:auto','placeholder' => '']) !!}<font color="red">{{$errors[$no]['product_id']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][product_id]', $ro['product_id'], ['class' => 'form-control ','style'=>'width:144px', 'readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['business_category_id']))
                                                <td>{!! Form::select('form[' . $no . '][business_category_id]',$business_categories,null, ['class' => 'form-control is-invalid business_category_id select2','id'=>'business_category_id','style'=>'width:auto','placeholder' => '']) !!}<font color="red">{{$errors[$no]['business_category_id']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][business_category_id]', $ro['business_category_id'], ['class' => 'form-control ','style'=>'width:144px', 'readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['shipping_mode_id']))
                                                <td>{!! Form::select('form[' . $no . '][shipping_mode_id]',$shipping_modes,null, ['class' => 'form-control is-invalid shipping_mode_id select2','id'=>'shipping_mode_id','style'=>'width:auto','placeholder' => '']) !!}<font color="red">{{$errors[$no]['shipping_mode_id']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][shipping_mode_id]', $ro['shipping_mode_id'], ['class' => 'form-control ','style'=>'width:144px', 'readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['destination']))
                                                @if($ro['shipping_mode_id'] == 1)
                                                    <td>{!! Form::select('form[' . $no . '][destination]', $domestic_overland_cities,null, ['class' => 'form-control is-invalid destination select2','id'=>'destination','style'=>'width:auto','placeholder' => '']) !!}<font color="red">{{$errors[$no]['destination']}}</font></td>
                                                @else
                                                    <td>{!! Form::select('form[' . $no . '][destination]', $domestic_cities,null, ['class' => 'form-control is-invalid destination select2','id'=>'destination','style'=>'width:auto','placeholder' => '']) !!}<font color="red">{{$errors[$no]['destination']}}</font></td>
                                                @endif
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][destination]', $ro['destination'], ['class' => 'form-control ','style'=>'width:144px', 'readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['volumetric_weight']))
                                                <td>{!! Form::select('form[' . $no . '][volumetric_weight]',['no'=>'no','yes'=>'yes'],null, ['class' => 'form-control volumetric_weight is-invalid select2','id'=>'volumetric_weight','placeholder' => '']) !!}<font color="red">{{$errors[$no]['volumetric_weight']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][volumetric_weight]', $ro['volumetric_weight'], ['class' => 'form-control','style'=>'width:60px', 'readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['weight']))
                                                <td>{!! Form::text('form[' . $no . '][weight]',$ro['weight'], ['class' => 'form-control is-invalid','style'=>'width:80px']) !!}<font color="red">{{$errors[$no]['weight']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][weight]', $ro['weight'], ['class' => 'form-control','style'=>'width:80px', 'readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['length']))
                                                <td>{!! Form::text('form[' . $no . '][length]',$ro['length'], ['class' => 'form-control is-invalid','style'=>'width:80px']) !!}<font color="red">{{$errors[$no]['length']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][length]', $ro['length'], ['class' => 'form-control','style'=>'width:80px', 'readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['breadth']))
                                                <td>{!! Form::text('form[' . $no . '][breadth]',$ro['breadth'], ['class' => 'form-control is-invalid','style'=>'width:80px']) !!}<font color="red">{{$errors[$no]['breadth']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][breadth]', $ro['breadth'], ['class' => 'form-control','style'=>'width:80px', 'readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['height']))
                                                <td>{!! Form::text('form[' . $no . '][height]',$ro['height'], ['class' => 'form-control is-invalid','style'=>'width:80px']) !!}<font color="red">{{$errors[$no]['height']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][height]', $ro['height'], ['class' => 'form-control','style'=>'width:80px', 'readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['pieces']))
                                                <td>{!! Form::text('form[' . $no . '][pieces]', $ro['pieces'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['pieces']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][pieces]', $ro['pieces'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['payment_mode_id']))
                                                <td>{!! Form::select('form[' . $no . '][payment_mode_id]',$payment_modes ,null,['class' => 'form-control is-invalid payment_mode_id select2','id'=>'payment_mode_id','style'=>'width:auto','placeholder' => '']) !!}<font color="red">{{$errors[$no]['payment_mode_id']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][payment_mode_id]', $ro['payment_mode_id'],['class' => 'form-control','style'=>'width:144px', 'readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['charges_mode_id']))
                                                <td>{!! Form::select('form[' . $no . '][charges_mode_id]',$charges_modes ,null,['class' => 'form-control is-invalid charges_mode_id select2','id'=>'charges_mode_id','style'=>'width:auto','placeholder' => '']) !!}<font color="red">{{$errors[$no]['charges_mode_id']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][charges_mode_id]', $ro['charges_mode_id'],['class' => 'form-control','style'=>'width:144px', 'readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['shipper_cell_number']))
                                                <td>{!! Form::text('form[' . $no . '][shipper_cell_number]', $ro['shipper_cell_number'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['shipper_cell_number']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][shipper_cell_number]', $ro['shipper_cell_number'],['class' => 'form-control','style'=>'width:auto','readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['shipper_name']))
                                                <td>{!! Form::text('form[' . $no . '][shipper_name]', $ro['shipper_name'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['shipper_name']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][shipper_name]', $ro['shipper_name'],['class' => 'form-control','style'=>'width:auto', 'readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['shipper_cnic']))
                                                <td>{!! Form::text('form[' . $no . '][shipper_cnic]', $ro['shipper_cnic'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['shipper_cnic']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][shipper_cnic]', $ro['shipper_cnic'],['class' => 'form-control','style'=>'width:auto', 'readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['shipper_address']))
                                                <td>{!! Form::textarea('form[' . $no . '][shipper_address]', $ro['shipper_address'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}<font color="red">{{$errors[$no]['shipper_address']}}</font></td>
                                            @else
                                                <td>{!! Form::textarea('form[' . $no . '][shipper_address]', $ro['shipper_address'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20, 'readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['consignee_cell_number']))
                                                <td>{!! Form::text('form[' . $no . '][consignee_cell_number]', $ro['consignee_cell_number'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['consignee_cell_number']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][consignee_cell_number]', $ro['consignee_cell_number'],['class' => 'form-control','style'=>'width:auto','readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['consignee_name']))
                                                <td>{!! Form::text('form[' . $no . '][consignee_name]', $ro['consignee_name'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['consignee_name']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][consignee_name]', $ro['consignee_name'],['class' => 'form-control','style'=>'width:auto', 'readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['consignee_cnic']))
                                                <td>{!! Form::text('form[' . $no . '][consignee_cnic]', $ro['consignee_cnic'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['consignee_cnic']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][consignee_cnic]', $ro['consignee_cnic'],['class' => 'form-control','style'=>'width:auto', 'readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['consignee_address']))
                                                <td>{!! Form::textarea('form[' . $no . '][consignee_address]', $ro['consignee_address'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}<font color="red">{{$errors[$no]['consignee_address']}}</font></td>
                                            @else
                                                <td>{!! Form::textarea('form[' . $no . '][consignee_address]', $ro['consignee_address'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20, 'readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['order_id']))
                                                <td>{!! Form::text('form[' . $no . '][order_id]', $ro['order_id'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['order_id']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][order_id]', $ro['order_id'],['class' => 'form-control','style'=>'width:100px','readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['insurance_offered']))
                                                <td>{!! Form::select('form[' . $no . '][insurance_offered]',['no'=>'no','yes'=>'yes'],null, ['class' => 'form-control insurance_offered is-invalid select2','id'=>'insurance_offered','placeholder' => '']) !!}<font color="red">{{$errors[$no]['insurance_offered']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][insurance_offered]', $ro['insurance_offered'], ['class' => 'form-control','style'=>'width:60px', 'readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['insurance_value']))
                                                <td>{!! Form::text('form[' . $no . '][insurance_value]', $ro['insurance_value'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['insurance_value']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][insurance_value]', $ro['insurance_value'],['class' => 'form-control','style'=>'width:100px','readonly' => 'readonly']) !!}</td>
                                            @endif
                                                @if(isset($errors[$no]['packaging_charges']))
                                                    <td>{!! Form::text('form[' . $no . '][packaging_charges]', $ro['packaging_charges'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['packaging_charges']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][packaging_charges]', $ro['packaging_charges'],['class' => 'form-control','style'=>'width:100px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                            @if(isset($errors[$no]['trax_box_id']))
                                                <td>{!! Form::select('form[' . $no . '][trax_box_id]',$trax_boxes ,null,['class' => 'form-control is-invalid trax_box_id select2','id'=>'trax_box_id','style'=>'width:auto','placeholder' => '']) !!}<font color="red">{{$errors[$no]['trax_box_id']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][trax_box_id]', $ro['trax_box_id'],['class' => 'form-control','style'=>'width:144px', 'readonly' => 'readonly']) !!}</td>
                                            @endif
                                            {{--@if(isset($errors[$no]['weight_charges']))
                                                <td>{!! Form::text('form[' . $no . '][weight_charges]', $ro['weight_charges'],['class' => 'form-control is-invalid','style'=>'width:100px']) !!}<font color="red">{{$errors[$no]['weight_charges']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][weight_charges]', $ro['weight_charges'],['class' => 'form-control','style'=>'width:100px','readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['fuel_surcharge']))
                                                <td>{!! Form::text('form[' . $no . '][fuel_surcharge]', $ro['fuel_surcharge'],['class' => 'form-control is-invalid','style'=>'width:100px']) !!}<font color="red">{{$errors[$no]['fuel_surcharge']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][fuel_surcharge]', $ro['fuel_surcharge'],['class' => 'form-control','style'=>'width:100px','readonly' => 'readonly']) !!}</td>
                                            @endif--}}
                                            @if(isset($errors[$no]['iban_number']))
                                                <td>{!! Form::text('form[' . $no . '][iban_number]', $ro['iban_number'],['class' => 'form-control is-invalid','style'=>'width:100px']) !!}<font color="red">{{$errors[$no]['iban_number']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][iban_number]', $ro['iban_number'],['class' => 'form-control','style'=>'width:100px','readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['account_number']))
                                                <td>{!! Form::text('form[' . $no . '][account_number]', $ro['account_number'],['class' => 'form-control is-invalid','style'=>'width:100px']) !!}<font color="red">{{$errors[$no]['account_number']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][account_number]', $ro['account_number'],['class' => 'form-control','style'=>'width:100px','readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['bank_id']))
                                                <td>{!! Form::select('form[' . $no . '][bank_id]',$banks ,null,['class' => 'form-control is-invalid bank_id select2','id'=>'bank_id','style'=>'width:auto','placeholder' => '']) !!}<font color="red">{{$errors[$no]['bank_id']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][bank_id]', $ro['bank_id'],['class' => 'form-control','style'=>'width:144px', 'readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['special_instruction']))
                                                <td>{!! Form::text('form[' . $no . '][special_instruction]', $ro['special_instruction'],['class' => 'form-control is-invalid','style'=>'width:100px']) !!}<font color="red">{{$errors[$no]['special_instruction']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][special_instruction]', $ro['special_instruction'],['class' => 'form-control','style'=>'width:100px','readonly' => 'readonly']) !!}</td>
                                            @endif
                                            <td><button type="button" class="btn btn-icon btn-danger cancel_shipment"><i class="la la-close"></i> </button></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div align="center" style="margin-top: 2%">
                                {!! Form::button('Submit', array('class' => 'btn btn-success submit', 'type' => 'submit', 'style'=>'width:10%'))!!}
                            </div>
                            {!!Form::close()!!}
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
            $('.product_id').select2({
                width: '100%',
                placeholder: 'Shipment'
            });
            $('.business_category_id').select2({
                width: '100%',
                placeholder: 'Business Category'
            });
            $('.shipping_mode_id').select2({
                width: '100%',
                placeholder: 'Product'
            });
            $('.destination').select2({
                width: '100%',
                placeholder: 'Destination'
            });
            $('.volumetric_weight').select2({
                width: '100%',
                placeholder: 'Volumetric Weight'
            });
            $('.insurance_offered').select2({
                width: '100%',
                placeholder: 'Insurance Offered'
            });
            $('.payment_mode_id').select2({
                width: '100%',
                placeholder: 'Payment Mode'
            });
            $('.charges_mode_id').select2({
                width: '100%',
                placeholder: 'Charges Mode'
            });
            $('.trax_box_id').select2({
                width: '100%',
                placeholder: 'Trax Box'
            });
            $('.bank_id').select2({
                width: '100%',
                placeholder: 'Bank'
            });

            var rowCount = $("#tbl td").closest("tr").length;
            if(rowCount == 1){
                $('.cancel_shipment').addClass('d-none');
            }
            else{
                $('#tbl .cancel_shipment').on('click', function(e){
                    $(this).closest('tr').remove();
                    rowCount = $("#tbl td").closest("tr").length;
                    if(rowCount == 1){
                        $('.cancel_shipment').addClass('d-none');
                    }
                });
            }

        });
    </script>
@endsection