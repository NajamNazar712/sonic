@extends('client.layout.master')

@section('title', 'Book Excel Shipment(s)')

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
                        {!! Form::model($data,['method' => 'POST', 'route' => 'cod.shipment.book.excel_store']) !!}
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
                                <th>Item Price</th>
                                <th>Replacement Item Product Type ID</th>
                                <th>Replacement Item Description</th>
                                <th>Replacement Item Quantity</th>
                                <th>Pickup Date (YYYY-MM-DD)</th>
                                <th>Special Instructions</th>
                                <th>Estimated Weight (kg)</th>
                                <th>Mode of Shipment ID</th>
                                <th>Same Day Timing ID</th>
                                <th>Amount</th>
                                <th>Mode of Payment ID</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                            $no=1;
                            @endphp
                            @foreach($data as $ro)
                            <tr>
                            <td >{!! $no=$no+1!!}</td>
                                @if(isset($errors[$no]['service_type_id']))
                                    <td>{!! Form::select('form[' . $no . '][service_type_id]',['1'=>'1','2'=>'2'], ['class' => 'form-control is-invalid', 'selected hidden']) !!}<font color="red">{{$errors[$no]['service_type_id']}}</font></td>
                                    @else
                                    <td>{!! Form::text('form[' . $no . '][service_type_id]', $ro['service_type_id'], ['class' => 'form-control ','style'=>'width:40px', 'readonly' => 'readonly']) !!}</td>
                                @endif
                                @if(isset($errors[$no]['pickup_address_id']))
                                    <td>{!! Form::text('form[' . $no . '][pickup_address_id]',$ro['pickup_address_id'], ['class' => 'form-control is-invalid','style'=>'width:80px']) !!}<font color="red">{{$errors[$no]['pickup_address_id']}}</font></td>
                                    @else
                                    <td>{!! Form::text('form[' . $no . '][pickup_address_id]', $ro['pickup_address_id'], ['class' => 'form-control','style'=>'width:80px', 'readonly' => 'readonly']) !!}</td>
                                @endif
                                @if(isset($errors[$no]['information_display']))
                                    <td>{!! Form::select('form[' . $no . '][information_display]',['no'=>'no','yes'=>'yes'], ['class' => 'form-control is-invalid']) !!}<font color="red">{{$errors[$no]['information_display']}}</font></td>
                                    @else
                                    <td>{!! Form::text('form[' . $no . '][information_display]', $ro['information_display'], ['class' => 'form-control','style'=>'width:60px', 'readonly' => 'readonly']) !!}</td>
                                @endif
                                @if(isset($errors[$no]['consignee_city_name']))
                                    <td>{!! Form::select('form[' . $no . '][consignee_city_name]',$cities ,['class' => 'form-control is-invalid', 'selected hidden']) !!}<font color="red">{{$errors[$no]['consignee_city_name']}}</font></td>
                                    @else
                                    <td>{!! Form::text('form[' . $no . '][consignee_city_name]', $ro['consignee_city_name'],['class' => 'form-control','style'=>'width:144px', 'readonly' => 'readonly']) !!}</td>
                                @endif
                                @if(isset($errors[$no]['consignee_name']))
                                    <td>{!! Form::text('form[' . $no . '][consignee_name]', $ro['consignee_name'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['consignee_name']}}</font></td>
                                    @else
                                    <td>{!! Form::text('form[' . $no . '][consignee_name]', $ro['consignee_name'],['class' => 'form-control','style'=>'width:auto', 'readonly' => 'readonly']) !!}</td>
                                @endif
                                @if(isset($errors[$no]['Address']))
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
                                @if(isset($errors[$no]['order_id']))
                                    <td>{!! Form::text('form[' . $no . '][order_id]', $ro['order_id'],['class' => 'form-control is-invalid','style'=>'width:auto']) !!}<font color="red">{{$errors[$no]['order_id']}}</font></td>
                                @else
                                    <td>{!! Form::text('form[' . $no . '][order_id]', $ro['order_id'],['class' => 'form-control','style'=>'width:100px','readonly' => 'readonly']) !!}</td>
                                @endif
                                @if(isset($errors[$no]['item_product_type_id']))
                                    <td>{!! Form::select('form[' . $no . '][item_product_type_id]',['1' => '1', '2' => '2','3' => '3','4' => '4','5' => '5','6' => '6','7' => '7','8' => '8','9' => '9','10' => '10','11' => '11','12' => '12','13' => '13','14' => '14','15' => '15','16' => '16','17' => '17','18' => '18','19' => '19','20' => '20','21' => '21','22' => '22','23' => '23','24' => '24' ],['class' => 'form-control is-invalid','style'=>'width:40px', 'selected hidden']) !!}<font color="red">{{$errors[$no]['item_product_type_id']}}</font></td>
                                @else
                                    <td>{!! Form::text('form[' . $no . '][item_product_type_id]', $ro['item_product_type_id'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                @endif
                                @if(isset($errors[$no]['item_description']))
                                    <td>{!! Form::textarea('form[' . $no . '][item_description]', $ro['item_description'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}<font color="red">{{$errors[$no]['item_description']}}</font></td>
                                @else
                                    <td>{!! Form::textarea('form[' . $no . '][item_description]', $ro['item_description'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20,'readonly' => 'readonly']) !!}</td>
                                @endif
                                @if(isset($errors[$no]['item_quantity']))
                                    <td>{!! Form::text('form[' . $no . '][item_quantity]', $ro['item_quantity'],['class' => 'form-control is-invalid','style'=>'width:40px']) !!}<font color="red">{{$errors[$no]['item_quantity']}}</font></td>
                                @else
                                    <td>{!! Form::text('form[' . $no . '][item_quantity]', $ro['item_quantity'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                @endif
                                @if(isset($errors[$no]['item_insurance']))
                                    <td>{!! Form::select('form[' . $no . '][item_insurance]',['no'=>'no','yes'=>'yes'],['class' => 'form-control is-invalid']) !!}<font color="red">{{$errors[$no]['item_insurance']}}</font></td>
                                @else
                                    <td>{!! Form::text('form[' . $no . '][item_insurance]', $ro['item_insurance'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                @endif
                                @if(isset($errors[$no]['item_price']))
                                    <td>{!! Form::text('form[' . $no . '][item_price]', $ro['item_price'],['class' => 'form-control is-invalid','style'=>'width:100px']) !!}<font color="red">{{$errors[$no]['item_price']}}</font></td>
                                @else
                                    <td>{!! Form::text('form[' . $no . '][item_price]', $ro['item_price'],['class' => 'form-control','style'=>'width:100px','readonly' => 'readonly']) !!}</td>
                                @endif
                                @if(isset($errors[$no]['replacement_item_product_type_id']))
                                    <td>{!! Form::select('form[' . $no . '][replacement_item_product_type_id]', ['1' => '1', '2' => '2','3' => '3','4' => '4','5' => '5','6' => '6','7' => '7','8' => '8','9' => '9','10' => '10','11' => '11','12' => '12','13' => '13','14' => '14','15' => '15','16' => '16','17' => '17','18' => '18','19' => '19','20' => '20','21' => '21','22' => '22','23' => '23','24' => '24' ],['class' => 'form-control is-invalid','selected hidden']) !!}<font color="red">{{$errors[$no]['replacement_item_product_type_id']}}</font></td>
                                @else
                                    <td>{!! Form::text('form[' . $no . '][replacement_item_product_type_id]', $ro['replacement_item_product_type_id'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                @endif
                                @if(isset($errors[$no]['replacement_item_description']))
                                    <td>{!! Form::textarea('form[' . $no . '][replacement_item_description]', $ro['replacement_item_description'],['class' => 'form-control is-invalid','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}<font color="red">{{$errors[$no]['replacement_item_description']}}</font></td>
                                @else
                                    <td>{!! Form::textarea('form[' . $no . '][replacement_item_description]', $ro['replacement_item_description'],['class' => 'form-control','style'=>'width:auto','rows' => 4,'cols' => 20,'readonly' => 'readonly']) !!}</td>
                                @endif
                                @if(isset($errors[$no]['replacement_item_quantity']))
                                    <td>{!! Form::text('form[' . $no . '][replacement_item_quantity]',['class' => 'form-control is-invalid','style'=>'width:60px']) !!}<font color="red">{{$errors[$no]['replacement_item_quantity']}}</font></td>
                                @else
                                    <td>{!! Form::text('form[' . $no . '][replacement_item_quantity]', $ro['replacement_item_quantity'],['class' => 'form-control','style'=>'width:60px','readonly' => 'readonly']) !!}</td>
                                @endif
                                @if(isset($errors[$no]['pickup_date']))
                                    <td>{!! Form::date('form[' . $no . '][pickup_date]', $ro['pickup_date'],['class' => 'form-control pickadate bg-primary border-primary white rounded-right','id' => 'pickup_date','style'=>'width:auto','rows' => 4,'cols' => 20]) !!}<font color="red">{{$errors[$no]['pickup_date']}}</font></td>
                                @else
                                    <td>{!! Form::date('form[' . $no . '][pickup_date]', $ro['pickup_date'],['class' => 'form-control','style'=>'width:auto','readonly' => 'readonly']) !!}</td>
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
                                    <td>{!! Form::select('form[' . $no . '][shipping_mode_id]',['1' => '1', '2' => '2','3' => '3','4' => '4'],['class' => 'form-control is-invalid','style'=>'width:40px','selected hidden']) !!}<font color="red">{{$errors[$no]['shipping_mode_id']}}</font></td>
                                @else
                                    <td>{!! Form::text('form[' . $no . '][shipping_mode_id]', $ro['shipping_mode_id'],['class' => 'form-control','style'=>'width:40px','readonly' => 'readonly']) !!}</td>
                                @endif
                                @if(isset($errors[$no]['same_day_timing_id']))
                                    <td>{!! Form::select('form[' . $no . '][same_day_timing_id]',['1' => '1', '2' => '2'],['class' => 'form-control is-invalid','style'=>'width:40px','selected hidden']) !!}<font color="red">{{$errors[$no]['same_day_timing_id']}}</font></td>
                                @else
                                    <td>{!! Form::text('form[' . $no . '][same_day_timing_id]', $ro['same_day_timing_id'],['class' => 'form-control','readonly' => 'readonly']) !!}</td>
                                @endif
                                @if(isset($errors[$no]['amount']))
                                    <td>{!! Form::text('form[' . $no . '][amount]', $ro['amount'],['class' => 'form-control is-invalid','style'=>'width:100px']) !!}<font color="red">{{$errors[$no]['amount']}}</font></td>
                                @else
                                    <td>{!! Form::text('form[' . $no . '][amount]', $ro['amount'],['class' => 'form-control','style'=>'width:100px','readonly' => 'readonly']) !!}</td>
                                @endif
                                @if(isset($errors[$no]['payment_mode_id']))
                                    <td>{!! Form::select('form[' . $no . '][payment_mode_id]', ['1' => '1'],['class' => 'form-control is-invalid', 'style'=>'width:40px']) !!}<font color="red">{{$errors[$no]['payment_mode_id']}}</font></td>
                                @else
                                    <td>{!! Form::text('form[' . $no . '][payment_mode_id]', $ro['payment_mode_id'],['class' => 'form-control','style'=>'width:40px','readonly' => 'readonly']) !!}</td>
                                @endif
                            </tr>

                            @endforeach
                            </tbody>
                        </table>
                                </div>
                            <div align="center">
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
        <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            $('#pickup_date').pickadate({
                firstDay: 1,
                clear: '',
                min: '{{ Carbon\Carbon::now() }}',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function () {
                    $('#pickup_date_root').css('top', '-350px');
                },
                onSet: function (context) {
                    $('#pickup_date').valid();
                }
            });
        });
        </script>
@endsection