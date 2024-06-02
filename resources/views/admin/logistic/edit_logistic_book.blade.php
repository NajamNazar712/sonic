@extends('admin.layout.master')

@section('title', 'Update Logistic Booking')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">Update Logistic Booking</h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <form id="logistic_booking_form" class="form-horizontal" method="POST" action="{{ route('admin.logistic.update') }}" novalidate="novalidate">
                                {{ csrf_field() }}
                                 @method('put')

                                    <input type="hidden" name="booking_id" value="{{$logistic_booking->id}}">
                                    <div class="row">
                                        <div class="col-md-12"><hr style="background-color: black;"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="row">
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Consignment #:</label>
                                                            <input type="text" name="cn_number" class="form-control" value="{{$logistic_booking->cn_number}}" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Staff #</label>
                                                            <select class="select select2 mb-1" name="rider_id" id="rider_select">
                                                                @foreach ($riders as  $rider)
                                                                    @if($rider->id == $logistic_booking->rider_id)
                                                                        <option value="{{$rider->id}}" selected>{{$rider->name}} - {{$rider->trax_id}}</option>
                                                                    @else
                                                                        <option value="{{$rider->id}}">{{$rider->name}} - {{$rider->trax_id}}</option>
                                                                    @endif
                                                                @endforeach

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Booking Date</label>
                                                            <input type="text" name="booking_date" class="form-control rounded-right booking_date" id="pickup_datepicker" value="{{ $logistic_booking->booking_date }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Customer</label>
                                                            <select class="select select2 mb-1" name="shipper_id" id="shipper_select">
                                                                @foreach ($shippers as $shipper)
                                                                    <option value="{{ $shipper->id }}">{{ $shipper->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                            </div>
                                            <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Product</label>
                                                            <select class="select select2 mb-1" id="product_select" name="product_id">
                                                                @foreach($products as $product)
                                                                    <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Service</label>
                                                            <select class="select select2 mb-1" id="service_select" name="service_id">
                                                                @foreach($services as $service)
                                                                    <option value="{{ $service->id }}">{{ $service->service_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Origin</label>
                                                            <select class="select select2 mb-1" name="origin_id" id="origin_select">
                                                                @foreach ($trax_stations as $trax_station)
                                                                     <option value="{{ $trax_station->id }}">{{  $trax_station->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                     <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Destination</label>
                                                            <select class="select select2 mb-1" name="destination_id" id="destination_select">
                                                                @foreach ($trax_stations as $trax_station)
                                                                    <option value="{{ $trax_station->id }}">{{  $trax_station->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                            </div>
                                            <div class="row">

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Total Pieces</label>
                                                            <input type="text" name="total_pieces" class="form-control" value="{{$logistic_booking->total_pieces}}" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Booking Weight (KG)</label>
                                                            <input type="text" name="booking_weight" class="form-control" value="{{$logistic_booking->total_booking_weight}}" >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Dense Weight</label>
                                                            <input type="text" name="dense_weight" class="form-control" value="{{$logistic_booking->total_dense_weight}}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {{-- umetric --}}
                                                            <label>Vol. Weight (KG)</label>
                                                            <input type="text" name="volumetric_weight" class="form-control " value="{{$logistic_booking->total_volumetric_weight}}">
                                                        </div>
                                                    </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Pay Mode</label>
                                                        <select class="select select2 mb-1" name="payment_mode_id" id="payment_mode_select">
                                                            @foreach ($payment_modes as $paymentmode)
                                                                <option value="{{ $paymentmode->id }}">{{  $paymentmode->mode }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Light/Heavy</label>
                                                        <select class="select form-control" name="consignment_type" id="consignment_type_Select">
                                                            <option value="" selected disabled>Select Type</option>
                                                            <option value="L">Light</option>
                                                            <option value="H">Heavy</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Handling Instructions</label>
                                                        <input type="text" name="handling_inst" class="form-control "  value="{{$logistic_booking->handling_inst}}" >
                                                    </div>
                                                </div>
                                            </div>
                                            <hr style="background-color: black;">
                                            <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group"> <h4 style="color: black"><b>Shipper Info</b></h4></div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label>Shipper Address</label>
                                                                    <select class="select select2 mb-1" name="shipper_address_id"  id="shipper_address_select">
                                                                        @foreach ($pickup_addresses as $pickup_address)
                                                                            <option value="{{ $pickup_address->id }}" data-poc="{{$pickup_address->poc}}" data-phone="{{$pickup_address->phone}}" data-email="{{$pickup_address->email}}">{{ $pickup_address->pickup_address }} </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Shipper Phone</label>
                                                                    <input type="text" name="shipper_phone" class="form-control "  readonly >
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Shipper Name</label>
                                                                    <input type="text" name="shipper_name" class="form-control "  readonly >
                                                                </div>
                                                            </div>
                                                        </div>
                                                       
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label>Shipper Email</label>
                                                                    <input type="text" name="shipper_email" class="form-control "   readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Zip/Postal Code</label>
                                                                    <input type="text" name="shipper_postal" class="form-control "   >
                                                                </div>
                                                            </div>
                                                        </div> --}}
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group"> <h4 style="color: black"><b>Consignee Info</b></h4></div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Consignee Name</label>
                                                                    <input type="text" name="consignee_name" class="form-control " value="{{$logistic_booking->consignee_name}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Consignee Phone</label>
                                                                    <input type="text" name="consignee_phone_1" class="form-control "  value="{{$logistic_booking->consignee_phone_1}}" >
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label>Consignee Address</label>
                                                                    <input type="text" name="consignee_address" class="form-control "  value="{{$logistic_booking->consignee_address}}" >
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label>Consignee Email</label>
                                                                    <input type="text" name="consignee_email" class="form-control " value="{{$logistic_booking->consignee_email}}"  >
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Zip/Postal Code</label>
                                                                    <input type="text" name="" class="form-control "   >
                                                                </div>
                                                            </div>
                                                        </div> --}}
                                                    </div>
                                            </div>
                                            <hr style="background-color: black;">
                                            <div class="row">
                                                <div class="col-md-2" style="margin-right: -10px;"><span class="btn btn-primary mb-3" data-toggle="collapse" data-target="#book_pieces">Booking Pieces</span></div>
                                                <div class="col-md-2"><span class="btn btn-primary mb-3" data-toggle="collapse" data-target="#sp_ins">Special Handling</span></div>
                                                <div class="col-md-3"><span class="btn btn-primary float-left" data-toggle="collapse" data-target="#item_sp">Volumatric Item Specification</span></div>
                                                <div class="col-md-2"><span class="btn btn-primary float-left mr-1" data-toggle="collapse" data-target="#other_charges">Other's</span></div>
                                            </div>
                                            <div id="book_pieces" class="collapse">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group"> <h4 style="color: black"><b>Booking Pieces</b></h4></div>
                                                    </div>
                                                        <div class="col-md-12">
                                                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;width:100% !important;">
                                                                <thead>
                                                                <tr role="row" class="bg-primary white">
                                                                    <th class="border-primary border-darken-1">S. No</th>
                                                                    <th class="border-primary border-darken-1">CN Number</th>
                                                                    <th class="border-primary border-darken-1">Scan Rider</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>

                                                                    @foreach($booking_pieces as $key=>$booking_piece)
                                                                       <tr>
                                                                           <td class=" align-middle serial_no"> {{$key+1}}</td>
                                                                           <td class=" align-middle from_pieces"> {{$booking_piece->piece_cn_number}}</td>
                                                                           <td class=" align-middle to_pieces"> {{$booking_piece->rider_name}}</td>
                                                                       </tr>
                                                                    @endforeach


                                                                </tbody>
                                                            </table>
                                                        </div>
                                                </div>
                                            </div>
                                            <hr style="background-color: black;">
                                            <div id="sp_ins" class="collapse">

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group"> <h4 style="color: black"><b>Special Handling</b></h4></div>
                                                    </div>
                                                    <input type="hidden" name="item_insurance_id" value="{{isset($item_insurance->id)?$item_insurance->id:0}}">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Special Handling</label>
                                                            <select class="select select2 mb-1" name="special_handling_id" id="special_handling_select">
                                                                @foreach ($special_handlings as $special_handling)
                                                                    <option value="{{ $special_handling->id }}">{{  $special_handling->description }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Insurance</label>
                                                            <input type="number" name="item_insurance" class="form-control" value="{{isset($item_insurance->insurance)?$item_insurance->insurance:old('item_insurance')}}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                          <div class="form-group">
                                                             <label>Item Specification</label>
                                                             <input type="text" name="insurance_item_code" class="form-control"  value="{{isset($item_insurance->item_code)?$item_insurance->item_code:old('insurance_item_code')}}">
                                                           </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="item_sp" class="collapse">
{{--                                                <hr style="background-color: black;">--}}
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group"> <h4 style="color: black"><b>Volumatric Item Specification</b></h4></div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Item Specification</label>
                                                            <input type="text" name="reference_item_code" class="form-control "   value="{{isset($item_references[0]->item_code)?$item_references[0]->item_code:old('reference_item_code')}}" >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="item_box" style="border: 1px solid lightgrey; padding:10px;">
                                                            <div class="item_detail">
                                                                @if(@count($item_references)>0)
                                                                <div class="row" style="margin-bottom: -17px;">
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label>Pieces</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label>Height (in)</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label>Width (in)</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label>Length (in)</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label>Weight</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label>Action</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @endif
                                                                @if(@count($item_references)>0)
                                                                    @foreach($item_references as $item_reference)
                                                                        <div class="row">
                                                                            <input type="hidden" name="item_reference_id[]" value="{{$item_reference->id}}">
                                                                            {{-- <div class="col-md-2">
                                                                                <div class="form-group">
                                                                                    <label>Item Id</label>
                                                                                    <input type="text" name="cn_number[]" class="form-control "   >
                                                                                </div>
                                                                            </div> --}}

                                                                            <div class="col-md-2">
                                                                                <div class="form-group">
                                                                                    <input type="text" name="no_piece[]" class="form-control "  value="{{$item_reference->no_piece}}" >
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <div class="form-group">
                                                                                    <input type="text" name="height[]" class="form-control "  value="{{$item_reference->height}}"  >
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <div class="form-group">
                                                                                    <input type="text" name="width[]" class="form-control "  value="{{$item_reference->width}}"  >
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <div class="form-group">
                                                                                    <input type="text" name="length[]" class="form-control "   value="{{$item_reference->length}}" >
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <div class="form-group">
                                                                                    <input type="text" name="weight[]" class="form-control "    value="{{$item_reference->weight}}">
                                                                                </div>
                                                                            </div>
{{--                                                                            <div class="col-md-1">--}}
{{--                                                                                <div class="form-group">--}}
{{--                                                                                    <label>Action</label>--}}
{{--                                                                                </div>--}}
{{--                                                                            </div>--}}
                                                                        </div>
                                                                    @endforeach
                                                                @else
                                                                    <div class="row">
                                                                        {{-- <div class="col-md-2">
                                                                            <div class="form-group">
                                                                                <label>Item Id</label>
                                                                                <input type="text" name="cn_number[]" class="form-control "   >
                                                                            </div>
                                                                        </div> --}}

                                                                        <div class="col-md-2">
                                                                            <div class="form-group">
                                                                                <label>Pieces</label>
                                                                                <input type="text" name="no_piece[]" class="form-control "   >
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            <div class="form-group">
                                                                                <label>Height (in)</label>
                                                                                <input type="text" name="height[]" class="form-control "   >
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            <div class="form-group">
                                                                                <label>Width (in)</label>
                                                                                <input type="text" name="width[]" class="form-control "   >
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            <div class="form-group">
                                                                                <label>Length (in)</label>
                                                                                <input type="text" name="length[]" class="form-control "   >
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            <div class="form-group">
                                                                                <label>Weight</label>
                                                                                <input type="text" name="weight[]" class="form-control "   >
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-1">
                                                                            <div class="form-group">
                                                                                <label>Action</label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                            </div>
{{--                                                            <div class="row">--}}
{{--                                                                <div class="col-md-12">--}}
{{--                                                                    <div class="form-group" ><span class="btn btn-primary float-right add_row_item_btn ">Add Row</span></div>--}}
{{--                                                                </div>--}}
{{--                                                            </div>--}}
                                                        </div>
                                                       
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="other_charges" class="collapse">
                                                <hr style="background-color: black;">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Route</label>
                                                            <input type="text" name="route_id" class="form-control "   >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Ot Service (Rs.)</label>
                                                            <input type="text" name="ot_service" class="form-control "   >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Gst (Rs.)</label>
                                                            <input type="text" name="gst" class="form-control "   >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Decl. Value (Rs.)</label>
                                                            <input type="text" name="descl" class="form-control "   >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Product Service Charges (Rs.)</label>
                                                            <input type="text" name="psc" class="form-control "   >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Province Sales Tax (Rs.)</label>
                                                            <input type="text" name="pst" class="form-control "   >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Insurance (Rs.)</label>
                                                            <input type="text" name="insurance" class="form-control "   >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Handling (Rs.)</label>
                                                            <input type="text" name="handling" class="form-control "   >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Total (Rs.)</label>
                                                            <input type="text" name="total" class="form-control "   >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                       
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Others (Rs.)</label>
                                                            <input type="text" name="other_charges" class="form-control "   >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <input type="text" name="" class="form-control "   >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Fuel Charges (Rs.)</label>
                                                            <input type="text" name="fuel_charges" class="form-control "   >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                       
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4" >
                                            <div class="row">
                                                <div class="col-md-12" >
                                                    <div class="label_img">
                                                        @if(isset($booking_img_url))
                                                            <img id="myimage" src="{{asset($booking_img_url)}}" >
                                                        @else
                                                            <img id="myimage" src="" alt="Booking label not available" >
                                                        @endif
                                                        <hr>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-4">
                                                <div class="col-md-12 text-center" style="border: 1px solid lightgrey; padding:7px;">
                                                    <button type="submit" name="book" id="add" class="btn btn-primary w-50" value="Book">Update Booking</button>
{{--                                                    @if(isset($batch_id) && $batch_id!=0)--}}
                                                        <a href="{{route('admin.logistic.batch.batch_bookings',['batch_id'=>$batch_id])}}" class="btn btn-danger">Skip Entry</a>

{{--                                                    @endif--}}
                                                </div>
                                            </div>
                                            <div class="row mt-4">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="batch_id" value="{{$batch_id}}">
                                                    <button type="button" name="batch_release" id="batch_release" class="btn btn-primary w-50" value="Book">Release Batch</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                            

                                <div class="row mt-2">
                                    <div class="col">
                                        <div class="form-group text-center">
                                         
                                            {{-- <button type="submit" name="book_and_print" id="sub_book_print" class="btn btn-primary ml-1" value="Book & Print">Book &amp; Print</button> --}}
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
    <style>

        .label_img {
            max-width: 445px;
            height: 400px;
            /*background-color: red;*/
            position: relative;
        }
        .label_img img {
            width: 100%;
            height: 100%;
            /* object-fit: cover; Maintain image aspect ratio and cover the entire container */
        }
        .img-magnifier-glass{
            display: none;
            position: absolute;
            border: 1px solid black;
            /* border-radius: 50%; */
            cursor: none;
            box-shadow: 5px 5px 12px black;
            /*Set the size of the magnifier glass:*/
            width: 250px;
            height: 250px; 
        }
        #item_sp input {
            pointer-events: none;
        }

    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/toggle/bootstrap-checkbox.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/spinner/jquery.bootstrap-touchspin.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    
  <script>

    $(document).ready(function(){
        

        {{--var shippers =@json($shippers);--}}
        var bookings = @json($logistic_booking);



    {{--$('#shipper_select').prepend('<option value="" selected="selected">Select Customer</option>').select2({--}}
        {{--   width: '100%',--}}
        {{--   placeholder: 'Select Customer'--}}
        {{--}).bind('select2:select',function(){--}}

        {{--    var shipper_id = parseInt($(this).val());--}}
        {{--    $("#shipper_address_select").empty();--}}
        {{--    --}}
        {{--            $.ajax({--}}
        {{--                url: '{{ route('admin.logistic.shipper_info') }}',--}}
        {{--                method: 'POST',--}}
        {{--                data: {--}}
        {{--                    'shipper_id': shipper_id,--}}
        {{--                    '_token': '{{ csrf_token() }}'--}}
        {{--                }--}}
        {{--            }).done(function (data) {--}}
        {{--                $('#product_select').empty();--}}
        {{--                if (data.status == 0) {--}}
        {{--                    $.each(data.pickup_addresses,function(key,value) {--}}
        {{--                        var name = value.city.name + ' - ' + value.pickup_address;--}}
        {{--                        var pickup = new Option(name, value.id,false, false);--}}
        {{--                        $(pickup).attr('data-poc', value.poc).attr('data-phone',value.phone).attr('data-email',value.email);--}}
        {{--                        $('#shipper_address_select').append(pickup).trigger('change');--}}
        {{--                    });--}}

        {{--                    $('#shipper_address_select').select2({--}}
        {{--                        placeholder: 'Select Shipper Address',--}}
        {{--                        width: '100%',--}}
        {{--                    }).val(null).trigger('change');--}}

        {{--                    var product = new Option(data.product.product_name, data.product.id, false, false);--}}
        {{--                    $('#product_select').append(product).trigger('change');--}}

        {{--                } else {--}}
        {{--                    toastr.error('No pickup address found!', 'Error!', {--}}
        {{--                        positionClass: 'toast-top-center',--}}
        {{--                        containerId: 'toast-top-center'--}}
        {{--                    });--}}
        {{--                }--}}
        {{--            });--}}
        {{--});--}}

        $('#shipper_select').prepend('<option value="" selected="selected">Select Customer</option>').select2({
            width: '100%',
            placeholder: 'Select Customer'
        }).val(bookings.shipper_id).trigger('change');

        var item_insurance = @json($item_insurance);

        $('#special_handling_select').prepend('<option value="" selected="selected">Select Special Handling</option>').select2({
            width: '100%',
            placeholder: 'Select Special Handling'
        });
        if(item_insurance!=null)
        {
            $("#special_handling_select").val(item_insurance.special_handling_id).trigger('change');
        }

        $('#origin_select').prepend('<option value="" selected="selected">Select Origin</option>').select2({
           width: '100%',
           placeholder: 'Select Origin'
        }).val(bookings.origin_id).trigger('change');



        $('#destination_select').prepend('<option value="" selected="selected">Select Destination</option>').select2({
           width: '100%',
           placeholder: 'Select Destination'
        }).val(bookings.destination_id).trigger('change');

        $('#payment_mode_select').prepend('<option value="" selected="selected">Select Payment Mode</option>').select2({
           width: '100%',
           placeholder: 'Select Payment Mode'
        }).val(bookings.payment_mode_id).trigger('change');

        $('#shipper_address_select').prepend('<option value="" selected="selected">Select Shipper Address</option>').select2({
           width: '100%',
           placeholder: 'Select Shipper Address'
        }).bind('change',function(){
            $("input[name=shipper_name]").val($('#shipper_address_select').find(':selected').attr('data-poc'));
            $("input[name=shipper_phone]").val($('#shipper_address_select').find(':selected').attr('data-phone'));
            $("input[name=shipper_email]").val($('#shipper_address_select').find(':selected').attr('data-email'));
        }).val(bookings.shipper_address_id).trigger('change');


        $('#product_select').prepend('<option value="" selected="selected">Select Product</option>').select2({
           width: '100%',
           placeholder: 'Select Product'
        }).val(bookings.product_id).trigger('change');

        $('#service_select').prepend('<option value="" selected="selected">Select Service</option>').select2({
            width: '100%',
            placeholder: 'Select Service'
        }).val(bookings.product_id).trigger('change');
        {{--.bind('change',function(){--}}
        {{--    var product_id = parseInt($(this).val());--}}
        {{--    $.ajax({--}}
        {{--        url: '{{ route('admin.logistic.product_services') }}',--}}
        {{--        method: 'POST',--}}
        {{--        data: {--}}
        {{--            'product_id': product_id,--}}
        {{--            '_token': '{{ csrf_token() }}'--}}
        {{--        }--}}
        {{--    }).done(function(data){--}}
        {{--        if(data.status==0){--}}
        {{--         --}}
        {{--            $.each(data.services,function(key,value) {--}}
        {{--                        var service = new Option(value.service_name, value.id, false, false);--}}
        {{--                        $('#service_select').append(service).trigger('change');--}}
        {{--            });--}}
        {{--            $('#service_select').select2({--}}
        {{--                        placeholder: 'Select Service',--}}
        {{--                        width: '100%',--}}
        {{--            }).val(bookings.service_id).trigger('change');--}}
        {{--        }else{--}}
        {{--            toastr.error('No Serivce found!', 'Error!', {--}}
        {{--                 positionClass: 'toast-top-center',--}}
        {{--                 containerId: 'toast-top-center'--}}
        {{--           });--}}
        {{--        }--}}
        {{--    });--}}

        {{--}).val(bookings.product_id).trigger('change');--}}




        $('#pickup_datepicker').pickadate({
                firstDay: 1,
                clear: '',
                min: '{{ Carbon\Carbon::today() }}',
                max: '{{ Carbon\Carbon::today() }}',
                // format: 'dd mmmm, yyyy',
                // value:'{{ Carbon\Carbon::today() }}',
                format: 'yyyy-mm-dd',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
        });
        $('#rider_select').prepend('<option value="" selected="selected">Select Rider</option>').select2({
           width: '100%',
           placeholder: 'Select Rider'
        }).val(bookings.rider_id).trigger('change');



        //validate form request
        $('#logistic_booking_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $('#logistic_booking_form button#add').prop('disabled', true);
                        swal({
                            title: 'Please Wait!',
                            text: 'Logistic Booking is being added!',
                            icon: 'info',
                        buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });
                        form.submit();
                }
            }); 

            $("#batch_release").on('click',function (){
                var batch_id = $("input[name=batch_id]").val();
                $.ajax({
                    url: '{{ route('admin.logistic.batch.release_batch') }}',
                    method: 'POST',
                    data: {
                        'batch_id': batch_id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data){

                    if(data.status==0)
                    {
                        window.location = '{{route('admin.logistic.batch.index')}}';
                    }else{
                        toastr.error(data.error ,'Error!', {
                            positionClass: 'toast-top-center',
                            containerId: 'toast-top-center'
                        });
                    }
                });
            });




        function magnify(imgID, zoom) {

            var img, glass, w, h, bw;
            img = document.getElementById(imgID);

            /*create magnifier glass:*/
            glass = document.createElement("DIV");
            glass.setAttribute("class", "img-magnifier-glass");
            /*insert magnifier glass:*/
            img.parentElement.insertBefore(glass, img);
            /*set background properties for the magnifier glass:*/
            glass.style.backgroundImage = "url('" + img.src + "')";
            glass.style.backgroundRepeat = "no-repeat";
            glass.style.backgroundSize = (img.width * zoom) + "px " + (img.height * zoom) + "px";
            bw = 3;
            w = glass.offsetWidth / 2;
            h = glass.offsetHeight / 2;
            /*execute a function when someone moves the magnifier glass over the image:*/
            glass.addEventListener("mousemove", moveMagnifier);
            img.addEventListener("mousemove", moveMagnifier);
            /*and also for touch screens:*/
            glass.addEventListener("touchmove", moveMagnifier);
            img.addEventListener("touchmove", moveMagnifier);
            function moveMagnifier(e) {

                $(".img-magnifier-glass").css('display','block');
                var pos, x, y;
                /*prevent any other actions that may occur when moving over the image*/
                e.preventDefault();
                /*get the cursor's x and y positions:*/
                pos = getCursorPos(e);
                x = pos.x;
                y = pos.y;
                /*prevent the magnifier glass from being positioned outside the image:*/
                if (x > img.width - (w / zoom)) {x = img.width - (w / zoom);}
                if (x < w / zoom) {x = w / zoom;}
                if (y > img.height - (h / zoom)) {y = img.height - (h / zoom);}
                if (y < h / zoom) {y = h / zoom;}
                /*set the position of the magnifier glass:*/
                glass.style.left = (x - w) + "px";
                glass.style.top = (y - h) + "px";
                /*display what the magnifier glass "sees":*/
                glass.style.backgroundPosition = "-" + ((x * zoom) - w + bw) + "px -" + ((y * zoom) - h + bw) + "px";
            }
            function getCursorPos(e) {
                var a, x = 0, y = 0;
                e = e || window.event;
                /*get the x and y positions of the image:*/
                a = img.getBoundingClientRect();
                /*calculate the cursor's x and y coordinates, relative to the image:*/
                x = e.pageX - a.left;
                y = e.pageY - a.top;
                /*consider any page scrolling:*/
                x = x - window.pageXOffset;
                y = y - window.pageYOffset;
                return {x : x, y : y};
            }
            }

            magnify("myimage", 2.5);

    });

    //add row in item btn
    $('.add_row_item_btn').click(function(){
        var row=' <div class="row"> <div class="col-md-2"> <div class="form-group"> <input type="text" name="no_piece[]" class="form-control " > </div> </div> <div class="col-md-2"> <div class="form-group"> <input type="text" name="height[]" class="form-control"> </div> </div> <div class="col-md-2"> <div class="form-group"> <input type="text" name="width[]" class="form-control"> </div> </div> <div class="col-md-2"> <div class="form-group"> <input type="text" name="length[]" class="form-control"> </div> </div> <div class="col-md-2"> <div class="form-group"> <input type="text" name="weight[]" class="form-control"> </div> </div> <div class="col-md-1"> <div class="form-group"> <span class="btn btn-danger btn-sm remove_row_btn"><i class="la la-times m-0"></i></span> </div> </div> </div>';
        $('.item_detail').append(row);
    });
    //remove row in item
    $('body').on('click','.remove_row_btn',function(){
        $(this).closest('.row').remove();
    });
    // $(".remove_row_btn").click(function(){
    //     console.log($(this).closest('row'));
    // });

    // magnify image
    function hidemagnify(){
        $(".img-magnifier-glass").css('display','none');
    }
  </script>
@endsection