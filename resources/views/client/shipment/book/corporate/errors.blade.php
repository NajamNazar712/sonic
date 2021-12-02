@extends('client.layout.master')

@section('title', 'Book Excel Shipment(s)')
{{--{{dd($errors)}}--}}
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Errors In Book Excel Shipment(s)
                </h1>
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            {!! Form::model($data,['method' => 'POST', 'route' => 'cod.shipment.book.corporate_excel_store']) !!}
                            {!! Form::hidden('service_type_check_id', $service_type_check_id) !!}
                            <div class="table-responsive">
                                <table class='table table-bordered' id='tbl'>
                                    <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Service Type ID</th>
                                        <th>Pickup Address ID</th>
                                        @if($service_type_check_id != 5)
                                            <th>Delivery Type ID</th>
                                        @endif
                                        <th>Show Information on Air Waybill (Optional)</th>
                                        <th>Consignee City Name</th>
                                        <th>Consignee Name</th>
                                        <th>Consignee Address</th>
                                        <th>Consignee Phone Number 1 (03000000000)</th>
                                        <th>Consignee Phone Number 2 (03000000000)</th>
                                        <th>Consignee Email Address</th>
                                        @if($service_type_check_id == 1 || $service_type_check_id == null)
                                            <th>Self Collection</th>
                                        @endif
                                        <th>Order ID</th>
                                        <th>Order Date (YYYY-MM-DD)</th>
                                        <th>Item Product Type ID</th>
                                        <th>Item Description</th>
                                        <th>Item Quantity</th>
                                        <th>Item Insurance</th>
                                        <th>Item Price</th>
                                        @if($service_type_check_id == 2 || $service_type_check_id == null)
                                            <th>Replacement Item Product Type ID</th>
                                            <th>Replacement Item Description</th>
                                            <th>Replacement Item Quantity</th>
                                        @endif
                                        @if($service_type_check_id == 3 )
                                           {{-- <th>Item Product Type ID 1</th>
                                            <th>Item Description 1</th>
                                            <th>Item Quantity 1</th>
                                            <th>Item Insurance 1</th>
                                            <th>Item Price 1</th>--}}
                                            <th>Item Product Type ID 2</th>
                                            <th>Item Description 2</th>
                                            <th>Item Quantity 2</th>
                                            <th>Item Insurance 2</th>
                                            <th>Item Price 2</th>
                                            <th>Item Product Type ID 3</th>
                                            <th>Item Description 3</th>
                                            <th>Item Quantity 3</th>
                                            <th>Item Insurance 3</th>
                                            <th>Item Price 3</th>
                                            <th>Item Product Type ID 4</th>
                                            <th>Item Description 4</th>
                                            <th>Item Quantity 4</th>
                                            <th>Item Insurance 4</th>
                                            <th>Item Price 4</th>
                                            <th>Item Product Type ID 5</th>
                                            <th>Item Description 5</th>
                                            <th>Item Quantity 5</th>
                                            <th>Item Insurance 5</th>
                                            <th>Item Price 5</th>
                                        @endif
                                        <th>Special Instructions</th>
                                        <th>Estimated Weight (kg)</th>
                                        <th>Mode of Shipment ID</th>
                                        <th>Same Day Timing ID</th>
                                        @if($service_type_check_id == 3)
                                            <th>Try and Buy Charges</th>
                                        @endif
                                        @if($service_type_check_id == 1 || $service_type_check_id == 2 || $service_type_check_id == null)
                                            <th>Amount</th>
                                        @endif
                                        @if($service_type_check_id != 5)
                                            <th>Mode of Payment ID</th>
                                            <th>Charges Mode ID</th>
                                        @endif
                                        <th>Pieces</th>
                                        <th>Shipper Reference 1</th>
                                        <th>Shipper Reference 2</th>
                                        <th>Shipper Reference 3</th>
                                        <th>Shipper Reference 4</th>
                                        <th>Shipper Reference 5</th>
                                        <th>Open Shipment</th>
                                        @if($service_type_check_id == 1 && $omni == 1)
                                            <th>Return Address ID</th>
                                        @endif

                                      
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $check_sameday = '';
                                            foreach ($user_shipping_modes as $user_shipping_mode){
                                                if($user_shipping_mode == 4){
                                                    $check_sameday = 1;
                                                    }
                                                    else{
                                                        $check_sameday = 0;
                                                    }
                                                }
                                            $no=1;
                                    @endphp
                                    @foreach($data as $ro)
                                        <tr>
                                            @if(isset($errors[$no+1]))
                                                <td><h4 style="color: red">{!! $no=$no+1!!}</h4><font color="red">{{ 'Error(s) in this row' }}</font></td>
                                            @else
                                                <td>{!! $no=$no+1!!}</td>
                                            @endif
                                            @if(isset($errors[$no]['service_type_id']))
                                                <td>{!! Form::select('form[' . $no . '][service_type_id]',$booking_types,null, ['class' => 'form-control is-invalid service_type_id select2','id'=>'service_type_id','style'=>'width:auto','placeholder' => '']) !!}<font color="red">{{$errors[$no]['service_type_id']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][service_type_id]', $ro['service_type_id'], ['class' => 'form-control ','style'=>'width:144px', 'readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['pickup_address_id']))
                                                <td>{!! Form::text('form[' . $no . '][pickup_address_id]',$ro['pickup_address_id'], ['class' => 'form-control is-invalid','style'=>'width:80px']) !!}<font color="red">{{$errors[$no]['pickup_address_id']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][pickup_address_id]', $ro['pickup_address_id'], ['class' => 'form-control','style'=>'width:80px', 'readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if($service_type_check_id != 5)
                                                @if(isset($errors[$no]['delivery_type_id']))
                                                    <td>{!! Form::select('form[' . $no . '][delivery_type_id]',$delivery_types,null, ['class' => 'form-control is-invalid delivery_type_id select2','id'=>'delivery_type_id','style'=>'width:auto','placeholder' => '']) !!}<font color="red">{{$errors[$no]['delivery_type_id']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][delivery_type_id]', $ro['delivery_type_id'], ['class' => 'form-control ','style'=>'width:144px', 'readonly' => 'readonly']) !!}</td>
                                                @endif
                                            @endif
                                            @if(isset($errors[$no]['information_display']))
                                                <td>{!! Form::select('form[' . $no . '][information_display]',['no'=>'no','yes'=>'yes'],null, ['class' => 'form-control is-invalid select2','id'=>'information_display','placeholder' => '']) !!}<font color="red">{{$errors[$no]['information_display']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][information_display]', $ro['information_display'], ['class' => 'form-control','style'=>'width:60px', 'readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['consignee_city_name']))
                                                <td>{!! Form::select('form[' . $no . '][consignee_city_name]',$cities ,null,['class' => 'form-control is-invalid consignee_city_name select2','id'=>'consignee_city_name','style'=>'width:auto','placeholder' => '']) !!}<font color="red">{{$errors[$no]['consignee_city_name']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][consignee_city_name]', $ro['consignee_city_name'],['class' => 'form-control','style'=>'width:144px', 'readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['consignee_name']))
                                                <td>{!! Form::text('form[' . $no . '][consignee_name]', $ro['consignee_name'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['consignee_name']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][consignee_name]', $ro['consignee_name'],['class' => 'form-control','style'=>'width:auto', 'readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['consignee_address']))
                                                <td>{!! Form::textarea('form[' . $no . '][consignee_address]', $ro['consignee_address'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}<font color="red">{{$errors[$no]['consignee_address']}}</font></td>
                                            @else
                                                <td>{!! Form::textarea('form[' . $no . '][consignee_address]', $ro['consignee_address'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20, 'readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['consignee_phone_number_1']))
                                                <td>{!! Form::text('form[' . $no . '][consignee_phone_number_1]', $ro['consignee_phone_number_1'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['consignee_phone_number_1']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][consignee_phone_number_1]', $ro['consignee_phone_number_1'],['class' => 'form-control','style'=>'width:auto','readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['consignee_phone_number_2']))
                                                <td>{!! Form::text('form[' . $no . '][consignee_phone_number_2]', $ro['consignee_phone_number_2'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['consignee_phone_number_2']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][consignee_phone_number_2]', $ro['consignee_phone_number_2'],['class' => 'form-control','style'=>'width:auto','readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['consignee_email_address']))
                                                <td>{!! Form::textarea('form[' . $no . '][consignee_email_address]', $ro['consignee_email_address'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}<font color="red">{{$errors[$no]['consignee_email_address']}}</font></td>
                                            @else
                                                <td>{!! Form::textarea('form[' . $no . '][consignee_email_address]', $ro['consignee_email_address'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20,'readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if($service_type_check_id == 1 || $service_type_check_id == null)
                                                @if(isset($errors[$no]['self_collection']))
                                                    <td>{!! Form::select('form[' . $no . '][self_collection]',['no'=>'no','yes'=>'yes'],null, ['class' => 'form-control is-invalid self_collection select2','id'=>'self_collection','placeholder' => '']) !!}<font color="red">{{$errors[$no]['self_collection']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][self_collection]', $ro['self_collection'], ['class' => 'form-control','style'=>'width:60px', 'readonly' => 'readonly']) !!}</td>
                                                @endif
                                            @endif
                                            @if(isset($errors[$no]['order_id']))
                                                <td>{!! Form::text('form[' . $no . '][order_id]', $ro['order_id'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['order_id']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][order_id]', $ro['order_id'],['class' => 'form-control','style'=>'width:100px','readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['order_date']))
                                                <td>{!! Form::text('form[' . $no . '][order_date]', $ro['order_date'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['order_date']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][order_date]', $ro['order_date'],['class' => 'form-control','style'=>'width:100px','readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if($service_type_check_id != 3)
                                                @if(isset($errors[$no]['item_product_type_id']))
                                                    <td>{!! Form::select('form[' . $no . '][item_product_type_id]',$products,null,['class' => 'form-control is-invalid item_product_type_id select2','id'=>'item_product_type_id','style'=>'width:auto','placeholder' => '']) !!}<font color="red">{{$errors[$no]['item_product_type_id']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][item_product_type_id]', $ro['item_product_type_id'],['class' => 'form-control','style'=>'width:144px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_description']))
                                                    <td>{!! Form::textarea('form[' . $no . '][item_description]', $ro['item_description'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}<font color="red">{{$errors[$no]['item_description']}}</font></td>
                                                @else
                                                    <td>{!! Form::textarea('form[' . $no . '][item_description]', $ro['item_description'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20,'readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_quantity']))
                                                    <td>{!! Form::text('form[' . $no . '][item_quantity]', $ro['item_quantity'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['item_quantity']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][item_quantity]', $ro['item_quantity'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_insurance']))
                                                    <td>{!! Form::select('form[' . $no . '][item_insurance]',['no'=>'no','yes'=>'yes'],null,['class' => 'form-control is-invalid item_insurance select2','id'=>'item_insurance','style'=>'width:80px','placeholder' => '']) !!}<font color="red">{{$errors[$no]['item_insurance']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][item_insurance]', $ro['item_insurance'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_price']))
                                                    <td>{!! Form::text('form[' . $no . '][item_price]', $ro['item_price'],['class' => 'form-control is-invalid','style'=>'width:100px']) !!}<font color="red">{{$errors[$no]['item_price']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][item_price]', $ro['item_price'],['class' => 'form-control','style'=>'width:100px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                            @endif
                                            @if($service_type_check_id == 2 || $service_type_check_id == null)
                                                @if(isset($errors[$no]['replacement_item_product_type_id']))
                                                    <td>{!! Form::select('form[' . $no . '][replacement_item_product_type_id]', $products,null,['class' => 'form-control is-invalid replacement_item_product_type_id select2','id'=>'replacement_item_product_type_id','placeholder' => '']) !!}<font color="red">{{$errors[$no]['replacement_item_product_type_id']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][replacement_item_product_type_id]', $ro['replacement_item_product_type_id'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['replacement_item_description']))
                                                    <td>{!! Form::textarea('form[' . $no . '][replacement_item_description]', $ro['replacement_item_description'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}<font color="red">{{$errors[$no]['replacement_item_description']}}</font></td>
                                                @else
                                                    <td>{!! Form::textarea('form[' . $no . '][replacement_item_description]', $ro['replacement_item_description'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20,'readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['replacement_item_quantity']))
                                                    <td>{!! Form::text('form[' . $no . '][replacement_item_quantity]', $ro['replacement_item_quantity'], ['class' => 'form-control is-invalid','style'=>'width:60px']) !!}<font color="red">{{$errors[$no]['replacement_item_quantity']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][replacement_item_quantity]', $ro['replacement_item_quantity'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                            @endif
                                            @if($service_type_check_id == 3)
                                                @if(isset($errors[$no]['item_product_type_id_1']))
                                                    <td>{!! Form::select('form[' . $no . '][item_product_type_id_1]',$products,null,['class' => 'form-control is-invalid item_product_type_id select2','id'=>'item_product_type_id_1','style'=>'width:auto','placeholder' => '']) !!}<font color="red">{{$errors[$no]['item_product_type_id_1']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][item_product_type_id_1]', $ro['item_product_type_id_1'],['class' => 'form-control','style'=>'width:144px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_description_1']))
                                                    <td>{!! Form::textarea('form[' . $no . '][item_description_1]', $ro['item_description_1'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}<font color="red">{{$errors[$no]['item_description_1']}}</font></td>
                                                @else
                                                    <td>{!! Form::textarea('form[' . $no . '][item_description_1]', $ro['item_description_1'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20,'readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_quantity_1']))
                                                    <td>{!! Form::text('form[' . $no . '][item_quantity_1]', $ro['item_quantity_1'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['item_quantity_1']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][item_quantity_1]', $ro['item_quantity_1'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_insurance_1']))
                                                    <td>{!! Form::select('form[' . $no . '][item_insurance_1]',['no'=>'no','yes'=>'yes'],null,['class' => 'form-control is-invalid item_insurance select2','id'=>'item_insurance_1','style'=>'width:80px','placeholder' => '']) !!}<font color="red">{{$errors[$no]['item_insurance_1']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][item_insurance_1]', $ro['item_insurance_1'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_price_1']))
                                                    <td>{!! Form::text('form[' . $no . '][item_price_1]', $ro['item_price_1'],['class' => 'form-control is-invalid','style'=>'width:100px']) !!}<font color="red">{{$errors[$no]['item_price_1']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][item_price_1]', $ro['item_price_1'],['class' => 'form-control','style'=>'width:100px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_product_type_id_2']))
                                                    <td>{!! Form::select('form[' . $no . '][item_product_type_id_2]',$products,null,['class' => 'form-control is-invalid item_product_type_id select2','id'=>'item_product_type_id_2','style'=>'width:auto','placeholder' => '']) !!}<font color="red">{{$errors[$no]['item_product_type_id_2']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][item_product_type_id_2]', $ro['item_product_type_id_2'],['class' => 'form-control','style'=>'width:144px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_description_2']))
                                                    <td>{!! Form::textarea('form[' . $no . '][item_description_2]', $ro['item_description_2'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}<font color="red">{{$errors[$no]['item_description_2']}}</font></td>
                                                @else
                                                    <td>{!! Form::textarea('form[' . $no . '][item_description_2]', $ro['item_description_2'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20,'readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_quantity_2']))
                                                    <td>{!! Form::text('form[' . $no . '][item_quantity_2]', $ro['item_quantity_2'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['item_quantity_2']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][item_quantity_2]', $ro['item_quantity_2'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_insurance_2']))
                                                    <td>{!! Form::select('form[' . $no . '][item_insurance_2]',['no'=>'no','yes'=>'yes'],null,['class' => 'form-control is-invalid item_insurance select2','id'=>'item_insurance_2','style'=>'width:80px','placeholder' => '']) !!}<font color="red">{{$errors[$no]['item_insurance_2']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][item_insurance_2]', $ro['item_insurance_2'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_price_2']))
                                                    <td>{!! Form::text('form[' . $no . '][item_price_2]', $ro['item_price_2'],['class' => 'form-control is-invalid','style'=>'width:100px']) !!}<font color="red">{{$errors[$no]['item_price_2']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][item_price_2]', $ro['item_price_2'],['class' => 'form-control','style'=>'width:100px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_product_type_id_3']))
                                                    <td>{!! Form::select('form[' . $no . '][item_product_type_id_3]',$products,null,['class' => 'form-control is-invalid item_product_type_id select2','id'=>'item_product_type_id_3','style'=>'width:auto','placeholder' => '']) !!}<font color="red">{{$errors[$no]['item_product_type_id_3']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][item_product_type_id_3]', $ro['item_product_type_id_3'],['class' => 'form-control','style'=>'width:144px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_description_3']))
                                                    <td>{!! Form::textarea('form[' . $no . '][item_description_3]', $ro['item_description_3'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}<font color="red">{{$errors[$no]['item_description_3']}}</font></td>
                                                @else
                                                    <td>{!! Form::textarea('form[' . $no . '][item_description_3]', $ro['item_description_3'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20,'readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_quantity_3']))
                                                    <td>{!! Form::text('form[' . $no . '][item_quantity_3]', $ro['item_quantity_3'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['item_quantity_3']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][item_quantity_3]', $ro['item_quantity_3'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_insurance_3']))
                                                    <td>{!! Form::select('form[' . $no . '][item_insurance_3]',['no'=>'no','yes'=>'yes'],null,['class' => 'form-control is-invalid item_insurance select2','id'=>'item_insurance_3','style'=>'width:80px','placeholder' => '']) !!}<font color="red">{{$errors[$no]['item_insurance_3']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][item_insurance_3]', $ro['item_insurance_3'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_price_3']))
                                                    <td>{!! Form::text('form[' . $no . '][item_price_3]', $ro['item_price_3'],['class' => 'form-control is-invalid','style'=>'width:100px']) !!}<font color="red">{{$errors[$no]['item_price_3']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][item_price_3]', $ro['item_price_3'],['class' => 'form-control','style'=>'width:100px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_product_type_id_4']))
                                                    <td>{!! Form::select('form[' . $no . '][item_product_type_id_4]',$products,null,['class' => 'form-control is-invalid item_product_type_id select2','id'=>'item_product_type_id_4','style'=>'width:auto','placeholder' => '']) !!}<font color="red">{{$errors[$no]['item_product_type_id_4']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][item_product_type_id_4]', $ro['item_product_type_id_4'],['class' => 'form-control','style'=>'width:144px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_description_4']))
                                                    <td>{!! Form::textarea('form[' . $no . '][item_description_4]', $ro['item_description_4'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}<font color="red">{{$errors[$no]['item_description_4']}}</font></td>
                                                @else
                                                    <td>{!! Form::textarea('form[' . $no . '][item_description_4]', $ro['item_description_4'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20,'readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_quantity_4']))
                                                    <td>{!! Form::text('form[' . $no . '][item_quantity_4]', $ro['item_quantity_4'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['item_quantity_4']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][item_quantity_4]', $ro['item_quantity_4'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_insurance_4']))
                                                    <td>{!! Form::select('form[' . $no . '][item_insurance_4]',['no'=>'no','yes'=>'yes'],null,['class' => 'form-control is-invalid item_insurance select2','id'=>'item_insurance_4','style'=>'width:80px','placeholder' => '']) !!}<font color="red">{{$errors[$no]['item_insurance_4']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][item_insurance_4]', $ro['item_insurance_4'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_price_4']))
                                                    <td>{!! Form::text('form[' . $no . '][item_price_4]', $ro['item_price_4'],['class' => 'form-control is-invalid','style'=>'width:100px']) !!}<font color="red">{{$errors[$no]['item_price_4']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][item_price_4]', $ro['item_price_4'],['class' => 'form-control','style'=>'width:100px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                                {{--@if(isset($errors[$no]['item_product_type_id_5']))
                                                    <td>{!! Form::select('form[' . $no . '][item_product_type_id_5]',$products,null,['class' => 'form-control is-invalid item_product_type_id select2','id'=>'item_product_type_id_5','style'=>'width:auto','placeholder' => '']) !!}<font color="red">{{$errors[$no]['item_product_type_id_5']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][item_product_type_id_5]', $ro['item_product_type_id_5'],['class' => 'form-control','style'=>'width:144px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_description_5']))
                                                    <td>{!! Form::textarea('form[' . $no . '][item_description_5]', $ro['item_description_5'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}<font color="red">{{$errors[$no]['item_description_5']}}</font></td>
                                                @else
                                                    <td>{!! Form::textarea('form[' . $no . '][item_description_5]', $ro['item_description_5'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20,'readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_quantity_5']))
                                                    <td>{!! Form::text('form[' . $no . '][item_quantity_5]', $ro['item_quantity_5'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['item_quantity_5']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][item_quantity_5]', $ro['item_quantity_5'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_insurance_5']))
                                                    <td>{!! Form::select('form[' . $no . '][item_insurance_5]',['no'=>'no','yes'=>'yes'],null,['class' => 'form-control is-invalid item_insurance select2','id'=>'item_insurance_5','style'=>'width:80px','placeholder' => '']) !!}<font color="red">{{$errors[$no]['item_insurance_5']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][item_insurance_5]', $ro['item_insurance_5'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_price_5']))
                                                    <td>{!! Form::text('form[' . $no . '][item_price_5]', $ro['item_price_5'],['class' => 'form-control is-invalid','style'=>'width:100px']) !!}<font color="red">{{$errors[$no]['item_price_5']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][item_price_5]', $ro['item_price_5'],['class' => 'form-control','style'=>'width:100px','readonly' => 'readonly']) !!}</td>
                                                @endif--}}
                                                @if(isset($errors[$no]['item_product_type_id_5']))
                                                    <td>{!! Form::select('form[' . $no . '][item_product_type_id_5]',$products,null,['class' => 'form-control is-invalid item_product_type_id select2','id'=>'item_product_type_id_5','style'=>'width:auto','placeholder' => '']) !!}<font color="red">{{$errors[$no]['item_product_type_id_5']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][item_product_type_id_5]', $ro['item_product_type_id_5'],['class' => 'form-control','style'=>'width:144px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_description_5']))
                                                    <td>{!! Form::textarea('form[' . $no . '][item_description_5]', $ro['item_description_5'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}<font color="red">{{$errors[$no]['item_description_5']}}</font></td>
                                                @else
                                                    <td>{!! Form::textarea('form[' . $no . '][item_description_5]', $ro['item_description_5'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20,'readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_quantity_5']))
                                                    <td>{!! Form::text('form[' . $no . '][item_quantity_5]', $ro['item_quantity_5'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['item_quantity_5']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][item_quantity_5]', $ro['item_quantity_5'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_insurance_5']))
                                                    <td>{!! Form::select('form[' . $no . '][item_insurance_5]',['no'=>'no','yes'=>'yes'],null,['class' => 'form-control is-invalid item_insurance select2','id'=>'item_insurance_5','style'=>'width:80px','placeholder' => '']) !!}<font color="red">{{$errors[$no]['item_insurance_5']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][item_insurance_5]', $ro['item_insurance_5'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['item_price_5']))
                                                    <td>{!! Form::text('form[' . $no . '][item_price_5]', $ro['item_price_5'],['class' => 'form-control is-invalid','style'=>'width:100px']) !!}<font color="red">{{$errors[$no]['item_price_5']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][item_price_5]', $ro['item_price_5'],['class' => 'form-control','style'=>'width:100px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                            @endif
                                            @if(isset($errors[$no]['special_instructions']))
                                                <td>{!! Form::textarea('form[' . $no . '][special_instructions]', $ro['special_instructions'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}<font color="red">{{$errors[$no]['special_instructions']}}</font></td>
                                            @else
                                                <td>{!! Form::textarea('form[' . $no . '][special_instructions]', $ro['special_instructions'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20,'readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['estimated_weight']))
                                                <td>{!! Form::text('form[' . $no . '][estimated_weight]', $ro['estimated_weight'],['class' => 'form-control is-invalid','style'=>'width:80px']) !!}<font color="red">{{$errors[$no]['estimated_weight']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][estimated_weight]', $ro['estimated_weight'],['class' => 'form-control','style'=>'width:80px','readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['shipping_mode_id']))
                                                <td>{!! Form::select('form[' . $no . '][shipping_mode_id]',$shipping_modes,null,['class' => 'form-control is-invalid shipping_mode_id select2','style'=>'width:auto','placeholder' => '']) !!}<font color="red">{{$errors[$no]['shipping_mode_id']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][shipping_mode_id]', $ro['shipping_mode_id'],['class' => 'form-control','style'=>'width:100px','readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['same_day_timing_id']) && ($check_sameday == 1))
                                                <td>{!! Form::select('form[' . $no . '][same_day_timing_id]',$shipping_mode_same_day_timings,null,['class' => 'form-control is-invalid same_day_timing_id select2','id'=>'same_day_timing_id', 'style'=>'width:100px','placeholder' => '']) !!}<font color="red">{{$errors[$no]['same_day_timing_id']}}</font></td>
                                            @else
                                                @if($check_sameday == 1)
                                                <td>{!! Form::text('form[' . $no . '][same_day_timing_id]', $ro['same_day_timing_id'] ,['class' => 'form-control', 'style'=>'width:100px','readonly' => 'readonly']) !!}</td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][same_day_timing_id]', null,['class' => 'form-control', 'style'=>'width:100px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                            @endif
                                            @if($service_type_check_id == 3)
                                                    @if(isset($errors[$no]['try_and_buy_charges']))
                                                        <td>{!! Form::text('form[' . $no . '][try_and_buy_charges]', $ro['try_and_buy_charges'],['class' => 'form-control is-invalid','style'=>'width:100px']) !!}<font color="red">{{$errors[$no]['try_and_buy_charges']}}</font></td>
                                                    @else
                                                        <td>{!! Form::text('form[' . $no . '][try_and_buy_charges]', $ro['try_and_buy_charges'],['class' => 'form-control','style'=>'width:100px','readonly' => 'readonly']) !!}</td>
                                                    @endif
                                            @endif
                                            @if($service_type_check_id == 1 || $service_type_check_id == 2 || $service_type_check_id == null)
                                                @if(isset($errors[$no]['amount']))
                                                    <td>{!! Form::text('form[' . $no . '][amount]', $ro['amount'],['class' => 'form-control is-invalid','style'=>'width:100px']) !!}<font color="red">{{$errors[$no]['amount']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][amount]', $ro['amount'],['class' => 'form-control','style'=>'width:100px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                            @endif
                                            @if($service_type_check_id != 5)
                                                @if(isset($errors[$no]['payment_mode_id']))
                                                    <td>{!! Form::select('form[' . $no . '][payment_mode_id]', $payment_modes,null,['class' => 'form-control is-invalid payment_mode_id select2','id'=>'payment_mode_id', 'style'=>'width:80px','placeholder' => '']) !!}<font color="red">{{$errors[$no]['payment_mode_id']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][payment_mode_id]', $ro['payment_mode_id'],['class' => 'form-control','style'=>'width:40px','readonly' => 'readonly']) !!}</td>
                                                @endif
                                                @if(isset($errors[$no]['charges_mode_id']))
                                                    <td>{!! Form::select('form[' . $no . '][charges_mode_id]',$charges_modes,null, ['class' => 'form-control is-invalid charges_mode_id select2','id'=>'charges_mode_id','style'=>'width:auto','placeholder' => '']) !!}<font color="red">{{$errors[$no]['charges_mode_id']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][charges_mode_id]', $ro['charges_mode_id'], ['class' => 'form-control ','style'=>'width:144px', 'readonly' => 'readonly']) !!}</td>
                                                @endif
                                            @endif
                                            @if(isset($errors[$no]['pieces_quantity']))
                                                <td>{!! Form::text('form[' . $no . '][pieces_quantity]', $ro['pieces_quantity'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['pieces_quantity']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][pieces_quantity]', $ro['pieces_quantity'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['shipper_reference_number_1']))
                                                <td>{!! Form::text('form[' . $no . '][shipper_reference_number_1]', $ro['shipper_reference_number_1'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['shipper_reference_number_1']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][shipper_reference_number_1]', $ro['shipper_reference_number_1'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['shipper_reference_number_2']))
                                                <td>{!! Form::text('form[' . $no . '][shipper_reference_number_2]', $ro['shipper_reference_number_2'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['shipper_reference_number_2']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][shipper_reference_number_2]', $ro['shipper_reference_number_2'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['shipper_reference_number_3']))
                                                <td>{!! Form::text('form[' . $no . '][shipper_reference_number_3]', $ro['shipper_reference_number_3'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['shipper_reference_number_3']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][shipper_reference_number_3]', $ro['shipper_reference_number_3'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['shipper_reference_number_4']))
                                                <td>{!! Form::text('form[' . $no . '][shipper_reference_number_4]', $ro['shipper_reference_number_4'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['shipper_reference_number_4']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][shipper_reference_number_4]', $ro['shipper_reference_number_4'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['shipper_reference_number_5']))
                                                <td>{!! Form::text('form[' . $no . '][shipper_reference_number_5]', $ro['shipper_reference_number_5'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['shipper_reference_number_5']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][shipper_reference_number_5]', $ro['shipper_reference_number_5'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if(isset($errors[$no]['open_shipment']))
                                                <td>{!! Form::text('form[' . $no . '][open_shipment]', $ro['open_shipment'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['open_shipment']}}</font></td>
                                            @else
                                                <td>{!! Form::text('form[' . $no . '][open_shipment]', $ro['open_shipment'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                            @endif
                                            @if($service_type_check_id == 1 && $omni == 1)
                                                @if(isset($errors[$no]['return_address_id']))
                                                    <td>{!! Form::text('form[' . $no . '][return_address_id]', $ro['return_address_id'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['return_address_id']}}</font></td>
                                                @else
                                                    <td>{!! Form::text('form[' . $no . '][return_address_id]', $ro['return_address_id'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                                @endif
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
            $('.datepicker').pickadate({
                closeOnSelect: false,
                closeOnClear: false
            });
            $('.service_type_id').select2({
                width: '100%',
                placeholder: 'Service Type'
            });
            $('.delivery_type_id').select2({
                width: '100%',
                placeholder: 'Delivery Type'
            });
            $('.information_display').select2({
                width: '100%',
                placeholder: 'Air Waybill'
            });
            $('.consignee_city_name').select2({
                width: '100%',
                placeholder: 'City Name'
            });
            $('.item_product_type_id').select2({
                width: '100%',
                placeholder: 'Product Type'
            });
            $('.item_insurance').select2({
                width: '100%',
                placeholder: 'Insurance'
            });
            $('.replacement_item_product_type_id').select2({
                width: '100%',
                placeholder: 'Product Type'
            });
            $('.shipping_mode_id').select2({
                width: '100%',
                placeholder: 'Shipping Mode'
            });
            $('.same_day_timing_id').select2({
                width: '100%',
                placeholder: 'Same Day Timing'
            });
            $('.payment_mode_id').select2({
                width: '100%',
                placeholder: 'Payment Mode'
            });
            $('.charges_mode_id').select2({
                width: '100%',
                placeholder: 'Charges Mode'
            });

            @if(session('rate_type_id') == 3)
              $('.delivery_type_id').val(1);
            @endif

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