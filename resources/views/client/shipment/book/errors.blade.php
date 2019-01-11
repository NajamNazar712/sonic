@extends('client.layout.master')

@section('title', 'Book Excel Shipment(s)')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Book Excel Shipment(s)
                </h1>
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                        {!! Form::model($data,['method' => 'POST', 'route' => 'cod.shipment.book.excel_store', 'files' => true, 'name'=>'shipments' ]) !!}
                            <div class="table-responsive">
                                <table class='table table-bordered' id='tbl'>
                            <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Service Type ID</th>
                                <th>Pickup Address ID</th>
                                <th>Show Information on Air Waybill (Optional)</th>
                                <th>Consignee City Name</th>
                                <th>Consignee Name</th>
                                <th>Consignee Address</th>
                                <th>Consignee Phone Number 1 (03000000000)</th>
                                <th>Consignee Phone Number 2 (03000000000)</th>
                                <th>Consignee Email Address</th>
                                <th>Order ID</th>
                                <th>Item Product Type ID</th>
                                <th>Item Description</th>
                                <th>Item Quantity</th>
                                <th>Item Insurance</th>
                                <th>Product Value</th>
                                <th>Replacement Item Product Type ID</th>
                                <th>Replacement Item Description</th>
                                <th>Replacement Item Quantity</th>
                                <th>Pickup Date (YYYY-MM-DD)</th>
                                <th>Special Instructions</th>
                                <th>Estimated Weight (kg)</th>
                                <th>Mode of Shipment ID</th>
                                <th>Same Day Timing ID</th>
                                <th>Collection Amount</th>
                                <th>Mode of Payment ID</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                            $no=1;
                            $index=0;
                            $rectify= array();
                            @endphp
                            @foreach($data as $ro)
                            {{$index=$index+1}}
                            <tr>
                            <td >{!! $no=$no+1!!}</td>
                                @if(isset($errors[$no]['service_type_id']))
                                    <td>{!! Form::text('Service Type ID', $rectify[$index]=$ro['service_type_id'], ['class' => 'form-control is-invalid']) !!}</td>
                                    @else
                                    <td>{!! Form::text('Service Type ID', $rectify[$index]=$ro['service_type_id'], ['class' => 'form-control ']) !!}</td>
                                @endif
                                @if(isset($errors[$no]['pickup_address_id']))
                                    <td>{!! Form::textarea('Pickup Address ID', $rectify[$index]=$ro['pickup_address_id'], ['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                    @else
                                    <td>{!! Form::textarea('Pickup Address ID', $rectify[$index]=$ro['pickup_address_id'], ['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                @endif
                                @if(isset($errors[$no]['information_display']))
                                    <td>{!! Form::text('Information Display', $rectify[$index]=$ro['information_display'], ['class' => 'form-control is-invalid']) !!}</td>
                                    @else
                                    <td>{!! Form::text('Information Display', $rectify[$index]=$ro['information_display'], ['class' => 'form-control']) !!}</td>
                                @endif
                                @if(isset($errors[$no]['consignee_city_name']))
                                    <td>{!! Form::textarea('Consignee City Name', $rectify[$index]=$ro['consignee_city_name'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                    @else
                                    <td>{!! Form::textarea('Consignee City Name', $rectify[$index]=$ro['consignee_city_name'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                @endif
                                @if(isset($errors[$no]['consignee_name']))
                                    <td>{!! Form::textarea('Consignee Name', $rectify[$index]=$ro['consignee_name'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                    @else
                                    <td>{!! Form::textarea('Consignee Name', $rectify[$index]=$ro['consignee_name'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                @endif
                                @if(isset($errors[$no]['Address']))
                                    <td>{!! Form::textarea('Consignee Address', $rectify[$index]=$ro['consignee_address'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                    @else
                                    <td>{!! Form::textarea('Consignee Address', $rectify[$index]=$ro['consignee_address'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                @endif
                                @if(isset($errors[$no]['consignee_phone_number_1']))
                                    <td>{!! Form::textarea('Consignee Phone Number 1', $rectify[$index]=$ro['consignee_phone_number_1'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                    @else
                                    <td>{!! Form::textarea('Consignee Phone Number 1', $rectify[$index]=$ro['consignee_phone_number_1'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                @endif
                                @if(isset($errors[$no]['consignee_phone_number_2']))
                                    <td>{!! Form::textarea('Consignee Phone Number 2', $rectify[$index]=$ro['consignee_phone_number_2'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                @else
                                    <td>{!! Form::textarea('Consignee Phone Number 2', $rectify[$index]=$ro['consignee_phone_number_2'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                @endif
                                @if(isset($errors[$no]['consignee_email_address']))
                                    <td>{!! Form::textarea('Consignee Email Address', $rectify[$index]=$ro['consignee_email_address'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                @else
                                    <td>{!! Form::textarea('Consignee Email Address', $rectify[$index]=$ro['consignee_email_address'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                @endif
                                @if(isset($errors[$no]['order_id']))
                                    <td>{!! Form::text('Order ID', $rectify[$index]=$ro['order_id'],['class' => 'form-control is-invalid']) !!}</td>
                                @else
                                    <td>{!! Form::text('Order ID', $rectify[$index]=$ro['order_id'],['class' => 'form-control']) !!}</td>
                                @endif
                                @if(isset($errors[$no]['item_product_type_id']))
                                    <td>{!! Form::text('Item Product Type ID', $rectify[$index]=$ro['item_product_type_id'],['class' => 'form-control is-invalid']) !!}</td>
                                @else
                                    <td>{!! Form::text('Item Product Type ID', $rectify[$index]=$ro['item_product_type_id'],['class' => 'form-control']) !!}</td>
                                @endif
                                @if(isset($errors[$no]['item_description']))
                                    <td>{!! Form::textarea('Item Description', $rectify[$index]=$ro['item_description'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                @else
                                    <td>{!! Form::textarea('Item Description', $rectify[$index]=$ro['item_description'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                @endif
                                @if(isset($errors[$no]['item_quantity']))
                                    <td>{!! Form::text('Item Quantity', $rectify[$index]=$ro['item_quantity'],['class' => 'form-control is-invalid']) !!}</td>
                                @else
                                    <td>{!! Form::text('Item Quantity', $rectify[$index]=$ro['item_quantity'],['class' => 'form-control']) !!}</td>
                                @endif
                                @if(isset($errors[$no]['item_insurance']))
                                    <td>{!! Form::textarea('Item Insurance', $rectify[$index]=$ro['item_insurance'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                @else
                                    <td>{!! Form::textarea('Item Insurance', $rectify[$index]=$ro['item_insurance'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                @endif
                                @if(isset($errors[$no]['item_price']))
                                    <td>{!! Form::textarea('Product Value', $rectify[$index]=$ro['item_price'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                @else
                                    <td>{!! Form::textarea('Product Value', $rectify[$index]=$ro['item_price'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                @endif
                                @if(isset($errors[$no]['replacement_item_product_type_id']))
                                    <td>{!! Form::text('Replacement Item Product Type ID', $rectify[$index]=$ro['replacement_item_product_type_id'],['class' => 'form-control is-invalid']) !!}</td>
                                @else
                                    <td>{!! Form::text('Replacement Item Product Type ID', $rectify[$index]=$ro['replacement_item_product_type_id'],['class' => 'form-control']) !!}</td>
                                @endif
                                @if(isset($errors[$no]['replacement_item_description']))
                                    <td>{!! Form::textarea('Replacement Item Description', $rectify[$index]=$ro['replacement_item_description'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                @else
                                    <td>{!! Form::textarea('Replacement Item Description', $rectify[$index]=$ro['replacement_item_description'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                @endif
                                @if(isset($errors[$no]['replacement_item_quantity']))
                                    <td>{!! Form::text('Replacement Item Quantity', $rectify[$index]=$ro['replacement_item_quantity'],['class' => 'form-control is-invalid']) !!}</td>
                                @else
                                    <td>{!! Form::text('Replacement Item Quantity', $rectify[$index]=$ro['replacement_item_quantity'],['class' => 'form-control']) !!}</td>
                                @endif
                                @if(isset($errors[$no]['pickup_date']))
                                    <td>{!! Form::textarea('Pickup Date', $rectify[$index]=$ro['pickup_date'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                @else
                                    <td>{!! Form::textarea('Pickup Date', $rectify[$index]=$ro['pickup_date'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                @endif
                                @if(isset($errors[$no]['special_instructions']))
                                    <td>{!! Form::textarea('Special Instructions', $rectify[$index]=$ro['special_instructions'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                @else
                                    <td>{!! Form::textarea('Special Instructions', $rectify[$index]=$ro['special_instructions'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                @endif
                                @if(isset($errors[$no]['estimated_weight']))
                                    <td>{!! Form::textarea('Estimated Weight', $rectify[$index]=$ro['estimated_weight'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                @else
                                    <td>{!! Form::textarea('Estimated Weight', $rectify[$index]=$ro['estimated_weight'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                @endif
                                @if(isset($errors[$no]['shipping_mode_id']))
                                    <td>{!! Form::textarea('Shipping Mode ID', $rectify[$index]=$ro['shipping_mode_id'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                @else
                                    <td>{!! Form::textarea('Shipping Mode ID', $rectify[$index]=$ro['shipping_mode_id'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                @endif
                                @if(isset($errors[$no]['same_day_timing_id']))
                                    <td>{!! Form::text('Same Day Timing ID', $rectify[$index]=$ro['same_day_timing_id'],['class' => 'form-control is-invalid']) !!}</td>
                                @else
                                    <td>{!! Form::text('Same Day Timing ID', $rectify[$index]=$ro['same_day_timing_id'],['class' => 'form-control']) !!}</td>
                                @endif
                                @if(isset($errors[$no]['amount']))
                                    <td>{!! Form::textarea('Collection Amount', $rectify[$index]=$ro['amount'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                @else
                                    <td>{!! Form::textarea('Collection Amount', $rectify[$index]=$ro['amount'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}</td>
                                @endif
                                @if(isset($errors[$no]['payment_mode_id']))
                                    <td>{!! Form::text('Payment Mode ID', $rectify[$index]=$ro['payment_mode_id'],['class' => 'form-control is-invalid']) !!}</td>
                                @else
                                    <td>{!! Form::text('Payment Mode ID', $rectify[$index]=$ro['payment_mode_id'],['class' => 'form-control']) !!}</td>
                                @endif
                            </tr>

                            @endforeach
                            </tbody>
                        </table>
                                </div>
                            <div align="center">
                                {{ Form::button('Submit', array('class' => 'btn btn-success submit', 'type' => 'submit', 'style'=>'width:10%'))}}
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('css')
        <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
@endsection