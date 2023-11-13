@extends('client.layout.master')

@section('title', 'Book a Shipment')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					{{--					@php(dd($user->shipping[0]['id']))--}}
					Book a Shipment
					<span id="selected_service_type_name">{{ (Session::has('service_type_name')) ? ('(' . Session::get('service_type_name') . ')') : '' }}</span>
					<button type="button" class="btn btn-primary d-block mt-1 ml-auto d-sm-block mt-sm-1 ml-sm-auto mt-md-0 float-md-right float-lg-right" data-toggle="modal" data-target="#select_service_type">Change Service Type</button>
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('client.inc.messages')
							<div class="alert bg-info" id="consignee_address_error" style="display: none">
							</div>
							<form id="booking_form" class="form-horizontal" method="POST" action="{{ route('cod.shipment.book.store') }}" enctype="multipart/form-data" novalidate="novalidate">
								{{ csrf_field() }}

								<input type="hidden" name="selected_service_type" id="selected_service_type" value="{{ Session::get('service_type_id') }}">

								<div class="row">
									<div id="shipping_custom" class="col col_custom">
										<h4 id="shipper_header_info" class="form-section mb-2 text-center">Shipper Information</h4>

										<div class="form-group">
											<p class="border-bottom border-light text-center font-medium-1 text-bold-600">{{ $user->name }}</p>
										</div>

										<div class="form-group">
											<p class="border-bottom border-light text-center font-medium-1 text-bold-600">{{ $user->phone }}</p>
										</div>

										<div class="form-group">
											<select name="pickup_address" class="select2" id="pickup_address" data-rule-required="true" data-msg-required="Pickup Address is required">

												@php ($default_pickup_address = FALSE)

												@foreach($user->shipping as $shipping_information)
													@if ($shipping_information['hidden'] == 0 && $shipping_information['status'] == 1)
														@if ($shipping_information['default_address'] == 1)
															@php ($default_pickup_address = TRUE)

															<option value="{{ $shipping_information['id'] }}" data-city-id="{{ $shipping_information['city']['id'] }}" data-city-name="{{ $shipping_information['city']['name'] }}" selected >{{ $shipping_information['poc'] }}: {{ $shipping_information['pickup_address'] }}, {{ $shipping_information['city']['name'] }}</option>
														@else
															<option value="{{ $shipping_information['id'] }}" data-city-id="{{ $shipping_information['city']['id'] }}" data-city-name="{{ $shipping_information['city']['name'] }}">{{ $shipping_information['poc'] }}: {{ $shipping_information['pickup_address'] }}, {{ $shipping_information['city']['name'] }}</option>
														@endif
													@endif
												@endforeach
												<option value="0">New</option>
											</select>
										</div>

										<div class="form-group">
											<p class="border-bottom border-light text-center font-medium-1 text-bold-600" id="pickup_city_name"></p>
										</div>

										<div id="new_pickup_address" class="d-none">
											<div class="form-group">
												<textarea name="new_pickup_address" class="form-control" placeholder="Address*" data-rule-required="true" data-msg-required="Address is required" data-rule-maxlength="190" data-msg-maxlength="Address can be maximum 190 characters"></textarea>
												<input type="checkbox" name="make_default_address" value="1">Make default address<br>
											</div>

											<div class="form-group">
												<input type="text" name="new_pickup_person_of_contact" class="form-control" placeholder="Person of Contact*" data-rule-required="true" data-msg-required="Person of Contact is required" data-rule-maxlength="100" data-msg-maxlength="Person of Contact can be maximum 100 characters">
											</div>

											<div class="form-group">
												<input type="text" name="new_pickup_vendor" class="form-control" placeholder="Vendor" data-rule-maxlength="100" data-msg-maxlength="Vendor can be maximum 100 characters">
											</div>

											<div class="form-group">
												<input type="text" name="new_pickup_phone_number" class="form-control phone_number" placeholder="Phone Number*" data-rule-required="true" data-msg-required="Phone Number is required">
											</div>

											<div class="form-group">
												<input type="email" name="new_pickup_email_address" class="form-control" placeholder="Email Address*" data-rule-required="true" data-msg-required="Email Address is required">
											</div>

											<div class="form-group">
												<select name="new_pickup_city" class="select2" id="new_pickup_city" data-rule-required="true" data-msg-required="City is required">
													@foreach($cities as $city)
														<option value="{{ $city->id }}">{{ $city->name }}</option>
													@endforeach
												</select>
											</div>

										</div>

										@if($omni_user == 1)

											<div class="form-group" id="return_address_div">
												<h5><b>Return Address :</b></h5>

												<select name="return_address" class="select2" id="return_address">
													<option value="0">New</option>

													@foreach($user->shipping as $shipping_information)
														@if ($shipping_information['hidden'] == 0 && $shipping_information['status'] == 1)
															@if ($shipping_information['default_return_address'] == 1)

																<option value="{{ $shipping_information['id'] }}" selected="selected" data-city-id="{{ $shipping_information['city']['id'] }}" data-city-name="{{ $shipping_information['city']['name'] }}">{{ $shipping_information['poc'] }}: {{ $shipping_information['pickup_address'] }}, {{ $shipping_information['city']['name'] }}</option>
															@else
																<option value="{{ $shipping_information['id'] }}" data-city-id="{{ $shipping_information['city']['id'] }}" data-city-name="{{ $shipping_information['city']['name'] }}">{{ $shipping_information['poc'] }}: {{ $shipping_information['pickup_address'] }}, {{ $shipping_information['city']['name'] }}</option>
															@endif
														@endif
													@endforeach
												</select>
											</div>

											<div class="form-group" id="return_city_name_div">
												<p class="border-bottom border-light text-center font-medium-1 text-bold-600" id="return_city_name"></p>
											</div>

											<div id="new_return_address" class="d-none">
												<div class="form-group">
													<textarea name="new_return_address" class="form-control" placeholder="Address*" data-rule-required="true" data-msg-required="Address is required" data-rule-maxlength="190" data-msg-maxlength="Address can be maximum 190 characters"></textarea>
												</div>

												<div class="form-group">
													<select name="new_return_city" class="select2" id="new_return_city" data-rule-required="true" data-msg-required="City is required">
														@foreach($cities as $city)
															<option value="{{ $city->id }}">{{ $city->name }}</option>
														@endforeach
													</select>
												</div>

												<div class="form-group">
													<input type="text" name="new_return_person_of_contact" class="form-control" placeholder="Person of Contact*" data-rule-required="true" data-msg-required="Person of Contact is required" data-rule-maxlength="100" data-msg-maxlength="Person of Contact can be maximum 100 characters">
												</div>

												<div class="form-group">
													<input type="text" name="new_return_vendor" class="form-control" placeholder="Vendor" data-rule-maxlength="100" data-msg-maxlength="Vendor can be maximum 100 characters">
												</div>

												<div class="form-group">
													<input type="text" name="new_return_phone_number" class="form-control phone_number" placeholder="Phone Number*" data-rule-required="true" data-msg-required="Phone Number is required">
												</div>

												<div class="form-group">
													<input type="email" name="new_return_email_address" class="form-control" placeholder="Email Address*" data-rule-required="true" data-msg-required="Email Address is required">
												</div>
											</div>
										@endif
										<div id="info_display" class="form-group text-center p-1 border border-light rounded">
												<label class="d-block">Show Information on Air Waybill</label>
												@if($airway_bill_address_visibility_users)
													<input type="checkbox"  name="information_display" class="switch hidden" id="information_display" checked="checked" disabled>
												@else
													<input type="checkbox"  name="information_display" class="switch hidden" id="information_display" disabled>
												@endif
												
										</div>
										{{-- @if($air_waybill != null)
											<div id="info_display" class="form-group text-center p-1 border border-light rounded">
												<label class="d-block">Show Information on Air Waybill</label>
												@if($air_waybill->information == 1)
													<input type="checkbox" name="information_display" class="switch hidden" id="information_display" checked="checked">
												@else
													<input type="checkbox" name="information_display" class="switch hidden" id="information_display">
												@endif
											</div>
										@else
											<div id="info_display" class="form-group text-center p-1 border border-light rounded">
												<label class="d-block">Show Information on Air Waybill</label>
												<input type="checkbox" name="information_display" class="switch hidden" id="information_display" checked="checked">
											</div>
										@endif --}}

									</div>
									<div id="consignee_header_div" class="col col_custom">
										<h4 id="consignee_header_info" class="form-section mb-2 text-center">Consignee Information</h4>
										<label for="consignee_info">Search By Phone No.</label>
										<div class="form-group">
											<select name="consignee_info" class="select2" id="consignee_info">
											</select>
										</div>
										<div class="form-group">
											<select name="consignee_city" class="select2" id="consignee_city" data-rule-required="true" data-msg-required="City is required">
												@foreach($consignee_cities as $city)
													<option value="{{ $city->id }}">{{ $city->name }}</option>
												@endforeach
											</select>
										</div>

										<div class="form-group">
											<input type="text" name="consignee_name" class="form-control" placeholder="Name*" data-rule-required="true" data-msg-required="Name is required" data-rule-maxlength="100" data-msg-maxlength="Name can be maximum 100 characters">
										</div>

										<div class="form-group">
											{{-- <textarea id="consignee_address" name="consignee_address" class="form-control" rows="5" placeholder="Address*" data-rule-required="true" data-msg-required="Address is required" data-rule-maxlength="255" data-msg-maxlength="Address can be maximum 255 characters"></textarea> --}}
											<textarea id="consignee_address" name="consignee_address" class="form-control" placeholder="Address*" onchange="bdmk()" rows="5"></textarea>
										</div>

										<div class="form-group">
											<input type="text" name="consignee_phone_number_1" class="form-control phone_number" placeholder="Phone Number 1*" data-rule-required="true" data-msg-required="Phone Number is required">
										</div>

										<div class="form-group">
											<input type="text" name="consignee_phone_number_2" class="form-control phone_number" placeholder="Phone Number 2">
										</div>

										<div class="form-group">
											<input type="email" name="consignee_email_address" class="form-control" placeholder="Email Address" data-rule-maxlength="100" data-msg-maxlength="Email Address can be maximum 100 characters">
										</div>



										<div id="self_collection_div" class="form-group text-center p-1 border border-light rounded">
											<label class="d-block">Self Collection</label>
											<input type="checkbox" name="self_collection" class="switch hidden" id="self_collection">
										</div>

										

									</div>

									<div id="order_information_header_div" class="col col_custom_middle">
										<h4 id="order_header_info" class="form-section mb-2 text-center">Order Information</h4>

										@if (Session::has('prefix'))
											<div class="form-group">
												<input name="order_id" class="form-control order_id" placeholder="Order ID" data-rule-maxlength="100" data-rule-required="true" data-msg-required="Order ID is required" data-msg-maxlength="Order ID can be maximum 100 characters" data-rule-remote="{{ route('cod.shipment.book.order_id') }}" data-msg-remote="Order ID must be unique">
											</div>
										@else
											@if (Session::has('restrict_order_id'))
												<div class="form-group">
													<input name="order_id" class="form-control" placeholder="Order ID" data-rule-maxlength="100" data-msg-maxlength="Order ID can be maximum 100 characters" data-rule-remote="{{ route('cod.shipment.book.restrict_order_id') }}" data-msg-remote="Order ID must be unique">
												</div>
											@else
												<div class="form-group">
													<input name="order_id" class="form-control" placeholder="Order ID" data-rule-maxlength="100" data-msg-maxlength="Order ID can be maximum 100 characters">
												</div>
											@endif
										@endif

										<div class="form-group">
											<div class="form-group input-group">
												<div class="input-group-prepend">
													<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
														<span class="la la-calendar-o"></span>
													</span>
												</div>
												<input type="text" name="order_date" class="form-control bg-primary border-primary white rounded-right" id="order_date" placeholder="Order Date">
											</div>
										</div>

										<div id="regular">
											<div class="form-group">
												<select name="product_type" class="select2" id="product_type" data-rule-required="true" data-msg-required="Product Type is required">
													@foreach($products as $product)
														@if($user['product_id'] == $product->id)
															<option value="{{ $product->id }}" selected>{{ $product->product_name }}</option>
														@else
															<option value="{{ $product->id }}">{{ $product->product_name }}</option>
														@endif
													@endforeach
												</select>
											</div>

											<div class="form-group">
												<textarea name="item_description" class="form-control" placeholder="Item Description*" data-rule-required="true" data-msg-required="Item Description is required" data-rule-maxlength="1000" data-msg-maxlength="Item Description can be maximum 1000 characters" rows="5"></textarea>
											</div>

											<div class="form-group input-group">
												<input type="text" name="item_quantity" class="form-control text-center quantity" placeholder="Item Quantity*" data-rule-required="true" data-msg-required="Item Quantity is required" data-toggle="tooltip" data-placement="top" title="" data-original-title="Here you enter the no. of items inside the flyer/box.">
											</div>

											<div class="form-group text-center p-1 border border-light rounded">
												<label class="d-block">Insurance</label>
												<input type="checkbox" name="insurance" class="switch hidden insurance">
											</div>

											<div class="form-group input-group d-none">
												<div class="input-group-prepend">
													<span class="input-group-text">Rs</span>
												</div>

												<input type="text" name="item_price" class="form-control rounded-right price" placeholder="Product Value*" data-rule-required="true" data-msg-required="Product Value is required">
											</div>
										</div>

										<div id="replacement" class="mb-1 d-none">
											<h4 class="text-center m-0 p-1 bg-dark white border border-dark rounded-top">Replacement</h4>

											<div class="pt-1 pl-1 pr-1 border border-light rounded-bottom">
												<div class="form-group">
													<select name="replacement_product_type" class="select2" id="replacement_product_type" data-rule-required="true" data-msg-required="Product Type is required">
														@foreach($products as $product)
															@if($user['product_id'] == $product->id)
																<option value="{{ $product->id }}" selected="selected">{{ $product->product_name }}</option>
															@else
																<option value="{{ $product->id }}">{{ $product->product_name }}</option>
															@endif
														@endforeach
													</select>
												</div>

												<div class="form-group">
													<textarea name="replacement_item_description" class="form-control" placeholder="Item Description*" data-rule-required="true" data-msg-required="Item Description is required" data-rule-maxlength="1000" data-msg-maxlength="Item Description can be maximum 1000 characters" data-toggle="tooltip" data-placement="top" title="" data-original-title="Please enter another flyer with the airway bill"></textarea>
												</div>

												<div class="form-group input-group">
													<input type="text" name="replacement_item_quantity" class="form-control text-center quantity" placeholder="Item Quantity*" data-rule-required="true" data-msg-required="Item Quantity is required">
												</div>
												<label class="d-block">Replacement Parcel Image*</label>
												<div class="form-group input-group">
													<input class="form-control text-center replacement_parcel_img" type="file" name="replacement_parcel_img" id="replacement_parcel_img" data-rule-required="true" data-msg-required="Replacement Image is Required" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">
												</div>
											</div>
										</div>

										<div id="try_and_buy" class="d-none">
											<div class="repeater mb-1">
												<div data-repeater-list="try_and_buy">
													<div class="product mb-1" data-repeater-item>
														<div class="d-flex justify-content-between align-items-center bg-dark border border-dark rounded-top">
															<h4 class="m-1 white">Product #<span>1</span></h4>
															<button data-repeater-delete type="button" class="btn btn-icon btn-danger btn-sm mr-1"><i class="ft-x"></i></button>
														</div>

														<div class="pt-1 pl-1 pr-1 border border-light rounded-bottom">
															<div class="form-group">
																<select name="product_type" class="select2" data-rule-required="true" data-msg-required="Product Type is required">
																	@foreach($products as $product)
																		@if($user['product_id'] == $product->id)
																			<option value="{{ $product->id }}" selected>{{ $product->product_name }}</option>
																		@else
																			<option value="{{ $product->id }}">{{ $product->product_name }}</option>
																		@endif
																	@endforeach
																</select>
															</div>

															<div class="form-group">
																<textarea name="item_description" class="form-control" placeholder="Item Description*" data-rule-required="true" data-msg-required="Item Description is required" data-rule-maxlength="1000" data-msg-maxlength="Item Description can be maximum 1000 characters"></textarea>
															</div>

															<div class="form-group input-group">
																<input type="text" name="item_quantity" class="form-control text-center quantity" placeholder="Item Quantity*" data-rule-required="true" data-msg-required="Item Quanity is required" data-toggle="tooltip" data-placement="top" title="" data-original-title="Here you enter the no. of items inside the flyer/box.">
															</div>

															<div class="form-group input-group">
																<div class="input-group-prepend">
																	<span class="input-group-text">Rs</span>
																</div>

																<input type="text" name="item_price" class="form-control rounded-right price" placeholder="Product Value*" data-rule-required="true" data-msg-required="Product Value is required">
															</div>

															<div class="form-group text-center p-1 border border-light rounded">
																<label class="d-block">Insurance</label>
																<input type="checkbox" name="insurance" class="switch hidden insurance">
															</div>
														</div>
													</div>
												</div>

												<div class="form-group text-right">
													<button data-repeater-create type="button" class="btn btn-block btn-primary" id="try_and_buy_add">Add</button>
												</div>
											</div>

											<div class="form-group">
												<p class="border-bottom border-light text-center font-medium-1 text-bold-600" id="total_quantity">Total Quantity: <span>0</span></p>
											</div>

											<div class="form-group">
												<p class="border-bottom border-light text-center font-medium-1 text-bold-600" id="total_price">Total Product(s) Value: Rs <span>0</span></p>
											</div>

											{{--											<div class="form-group">--}}
											{{--												<div class="form-group text-center p-1 border border-light rounded">--}}
											{{--													<label class="d-block">Type of Package</label>--}}
											{{--													<input type="checkbox" name="package_type" class="switch hidden package_type" id="package_type" checked="checked" data-off-label="Partial" data-on-label="Complete">--}}
											{{--												</div>--}}
											{{--											</div>--}}
										</div>

										<div class="form-group">
											<textarea name="special_instructions" class="form-control" placeholder="Special Instructions" data-rule-maxlength="190" data-msg-maxlength="Special Instructions can be maximum 190 characters" rows="5"></textarea>
										</div>
										<div id="open_shipment_div" class="form-group text-center p-1 border border-light rounded">
                                            <label class="d-block">Open Shipment</label>
                                            <input type="checkbox" name="open_shipment" class="switch hidden" id="open_shipment">
                                        </div>
									</div>

									<div id="shipping_header_div" class="col col_custom">
										<h4 id="shipping_header_info" class="form-section mb-2 text-center">Shipping Information</h4>

										<div class="form-group input-group mb-0">
											<input type="text" name="estimated_weight" class="form-control weight" placeholder="Estimated Weight*" data-rule-required="true" data-msg-required="Estimated Weight is required">

											<div class="input-group-append">
												<span class="input-group-text">kg</span>
											</div>
										</div>

										<h6 class="form-text mb-1 text-justify text-muted text-italic">*Charges will be subjected to the Final Weight measured at the time of Shipment Arrival.</h6>

										<div id="pieces_quantity" class="form-group input-group d-none">
											<input  type="text" name="pieces_quantity" id="pieces" class="form-control text-center pieces" placeholder="Pieces*" data-rule-required="true" data-msg-required="Pieces is required" data-toggle="tooltip" data-placement="top" title="" data-original-title="Here you enter the no. of individual flyers or boxes your shipment is separated into, so each can have it's own indentity slip and be accounted for.">
										</div>

										<div class="form-group">
											<select name="shipping_mode" class="select2" id="shipping_mode" data-rule-required="true" data-msg-required="Mode of Shipping is required">
											</select>
										</div>

										<div id="shipping_same-day" class="d-none">
											<div class="form-group">
												<select name="same-day_timing" class="select2" id="same-day_timing" data-rule-required="true" data-msg-required="Same-day Timing is required">
													@foreach($shipping_mode_same_day_timings as $shipping_mode_same_day_timing)
														<option value="{{ $shipping_mode_same_day_timing->id }}">{{ $shipping_mode_same_day_timing->timing }}</option>
													@endforeach
												</select>
											</div>
										</div>

										<div id="charges_mode_div" class="form-group">
											<select name="charges_mode" class="select2" id="charges_mode" data-rule-required="true" data-msg-required="Charges Mode is required">
												@foreach($charges_modes as $charges_mode)
													@if ($charges_mode->id == 4)
														<option value="{{ $charges_mode->id }}" selected="selected">{{ $charges_mode->charges_mode }}</option>
													@else
														<option value="{{ $charges_mode->id }}">{{ $charges_mode->charges_mode }}</option>
													@endif
												@endforeach
											</select>
										</div>
									</div>

									<div id="payment_info" class="col col_custom">
										<h4 class="form-section mb-2 text-center">Payment Information</h4>
										@if($user->logo_status)
											<div id="cod_breakup" class="form-group text-center p-1 border border-light rounded">
												<label class="d-block">COD Breakup</label>
												<input type="checkbox" name="cod_breakup_checkbox" class="switch" id="cod_breakup_checkbox">
											</div>
										@endif
										<div class="form-group input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">Rs</span>
											</div>

											<input type="text" name="amount" id="amount" class="form-control rounded-right amount" placeholder="Collection Amount*" data-rule-required="true" data-msg-required="Collection Amount is required">
										</div>


										<div class="form-group input-group" id="try_and_buy_charges_div">
											<input type="text" name="try_and_buy_charges" id="try_and_buy_charges" class="form-control amount" placeholder="Try & Buy Charges*" data-rule-required="true" data-msg-required="Charges field is required" value="">
										</div>

										<div class="form-group">
											<select name="payment_mode" class="select2" id="payment_mode" data-rule-required="true" data-msg-required="Mode of Payment is required">
												@foreach($payment_modes as $payment_mode)
													<option value="{{ $payment_mode->id }}">{{ $payment_mode->mode }}</option>
												@endforeach
											</select>
										</div>

										{{--Todo : Parcle Value--}}
										<div class="form-group input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">Rs</span>
											</div>

											<input type="text" name="parcel_value" id="parcel_value"
												   class="form-control rounded-right parcel_value"
												   placeholder="Parcel Value*" data-rule-required="true"
												   data-msg-required="Parcel Value is Required"  oninput="if(this.value==='0') this.value=''">
										</div>
										{{-- todo : Parcle Value End--}}
									</div>
								</div>

								<div id="shipper_references" class="col">
									<h4 class="form-section mb-2 text-center">Shipper References (Optional)</h4>
									<div class="row">
										<div class="form-group col">
											<input name="shipper_reference_1" class="form-control shipper_reference" placeholder="Shipper Reference 1" data-rule-maxlength="190" data-msg-maxlength="Shipper Reference 1 can be maximum 190 characters">
										</div>
										<div class="form-group col">
											<input name="shipper_reference_2" class="form-control shipper_reference" placeholder="Shipper Reference 2" data-rule-maxlength="190" data-msg-maxlength="Shipper Reference 2 can be maximum 190 characters">
										</div>
										<div class="form-group col">
											<input name="shipper_reference_3" class="form-control shipper_reference" placeholder="Shipper Reference 3" data-rule-maxlength="190" data-msg-maxlength="Shipper Reference 3 can be maximum 190 characters">
										</div>
										<div class="form-group col">
											<input name="shipper_reference_4" class="form-control shipper_reference" placeholder="Shipper Reference 4" data-rule-maxlength="190" data-msg-maxlength="Shipper Reference 4 can be maximum 190 characters">
										</div>
										<div class="form-group col">
											<input name="shipper_reference_5" class="form-control shipper_reference" placeholder="Shipper Reference 5" data-rule-maxlength="190" data-msg-maxlength="Shipper Reference 5 can be maximum 190 characters">
										</div>
									</div>
								</div>

								<div class="row mt-2">
									<div class="col">
										<div class="form-group text-center">
											<button type="submit" name="book" class="btn btn-primary submission" value="Book">Book</button>
											<button type="submit" name="book_and_print" class="btn btn-primary ml-1 submission" value="Book & Print">Book & Print</button>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>

				<div class="modal fade" id="select_service_type" role="dialog" aria-labelledby="select_service_type_title" aria-hidden="true">
					<div class="modal-dialog modal-sm" role="document">
						<div class="modal-content">
							<form class="form-horizontal">
								{{ csrf_field() }}

								<div class="modal-header">
									<h4 class="modal-title" id="select_service_type_title">Select Service Type</h4>
								</div>
								<div class="modal-body">
									<div class="control-group">
										<div class="controls">
											<select name="service_type" class="select2" id="service_type">
												@foreach($booking_types as $booking_type)
													<option value="{{ $booking_type->id }}">{{ $booking_type->booking_type }}</option>
												@endforeach
											</select>

											<label id="service_type-error" class="danger d-none">Service Type is required.</label>
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<button type="submit" class="btn btn-primary mx-auto">Select</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			@if($user->logo_status)
				<!--items modal-->
					<div class="modal fade" id="cod_breakup_modal" role="dialog" aria-labelledby="cod_breakup_modal_title" aria-hidden="true">
						<div class="modal-dialog modal-xl" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h4 class="modal-title" id="cod_breakup_modal_title">COD Breakup</h4>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
									</button>
								</div>
								<form id="cod_breakup_form" class="form" style="width: 100%;">
									{{ csrf_field() }}
									<div class="modal-body">
										<table class="table table-bordered datatable" id="cod_breakup_table" style="z-index: 3;min-width: 100%;">
											<thead>
											<tr role="row" class="bg-primary white">
												<th class="border-primary border-darken-1">S. No.</th>
												<th class="border-primary border-darken-1">Item Description</th>
												<th class="border-primary border-darken-1">Amount</th>
												<th class="border-primary border-darken-1"></th>
											</tr>
											</thead>
										</table>
										<div class="row">
											<div class="col-3">
												<div class="form-group">

													<div class="input-group">
														<div class="input-group-prepend">
															<span class="input-group-text">Shipping Charges</span>
														</div>
														<input type="text" name="cod_breakup_shipping_charges" id="cod_breakup_shipping_charges" class="form-control amount" placeholder="Shipping Charges*" data-rule-required="true" data-msg-required="Shipping Charges is required" value="">
													</div>
												</div>
											</div>
											<div class="col-3">
												<div class="form-group">

													<div class="input-group">
														<div class="input-group-prepend">
															<span class="input-group-text">Total COD</span>
														</div>
														<input type="text" name="cod_breakup_total" id="cod_breakup_total" class="form-control amount" placeholder="Total COD*" data-rule-required="true" data-msg-required="Total COD is required" value="">
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="modal-footer">
										<button type="submit" id="cod_breakup_submit_btn" disabled class="btn btn-primary mx-auto">Update</button>
									</div>
								</form>
							</div>
						</div>
					</div>
					<!--items modal-->
				@endif
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
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
	<style>
		table.dataTable {
			font-size: 12px;
		}

		table.dataTable thead tr th {
			padding-left: 0.5em;
			white-space: normal;
			word-wrap: break-word;
		}

		table.dataTable thead tr th:before,
		table.dataTable thead tr th:after {
			height: 20px;
			margin-bottom: -10px;
			bottom: 50% !important;
		}

		table.dataTable tbody tr td {
			padding-left: 0.5em;
			padding-right: 0.5em;
		}

		table.dataTable tbody tr td.select-checkbox:before {
			top: 50%;
			border-color: #64a0d2;
		}

		table.dataTable tbody tr.selected td.select-checkbox:after {
			top: 50%;
			text-shadow: none;
		}

		.btn-group .dropdown-menu .dropdown-item {
			white-space: normal;
		}

		#toast-bottom-center.toast-container {
			text-align: center;
		}

		#toast-bottom-center.toast-container .toast {
			display: table;
			width: auto !important;
			text-align: left;
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
	<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/js/scripts/tooltip/tooltip.js')}}" type="text/javascript"></script>

	<script>

		function bdmk(){
            var city_id = $('#consignee_city').val();
			var city_name = $('#consignee_city option:selected').text();
            var consignee_address = $('#consignee_address').val();


            $.ajax({
                url: '{{route('cod.shipment.book.address_verify')}}',
                method: 'get',
                data: {
                    'city_id': city_id,
                    'consignee_address': consignee_address
                }
            }).done(function (data) {
                if (data) {
                    var er = "Dear User, <br>";
                    if (data.invalid_cities) {
                        $.each(data.invalid_cities, function (key, value) {
                            er +=  "The area <strong>" + value + "</strong> is actually present in <strong>" + key + "</strong> instead of <strong>" + city_name +"</strong>. <br>";
                        });
                        $('#consignee_address_error').html(er.trim() + " For Assistance Call 021-111-118-729");
                        $('#consignee_address_error').show();

                    }else{
                        $('#consignee_address_error').html('');
                        $('#consignee_address_error').hide();
                    }
                }
            });

        }

		$(document).ready(function() {
			//todo: for parcel value
			$('#parcel_value').prop('disabled', true);
			$( "#amount" ).keyup(function() {
				var amt = $('#amount').val();
				if (amt == 0 )
				{
					// $("#parcel_value").css("background-color", "yellow");
					$('#parcel_value').prop('disabled', false);
				}
				else
				{
					// $("#parcel_value").css("background-color", "lightgray");
					$('#parcel_value').prop('disabled', true);
					$('#parcel_value').val('');
				}
			});
			// if((amt !== 0) || (amt == null) || (isEmpty(amt)))
			//todo: for parcel value end

			//todo : increse of pieces to 500
				$(this).find('.pieces').TouchSpin({
					min: 1,
					max: 10,
					buttondown_class: 'btn btn-primary rounded-left',
					buttonup_class: 'btn btn-primary rounded-right',
					buttondown_txt: '<i class="ft-minus"></i>',
					buttonup_txt: '<i class="ft-plus"></i>'
				}).bind('input change', function() {
					$(this).tooltip('show');

					if ($(this).hasClass('danger'))
						$(this).valid();

					if ($(this).hasClass('exceed_pieces'))
						$("#pieces").trigger("touchspin.updatesettings", {max: 500});
					else
						$("#pieces").trigger("touchspin.updatesettings", {max: 10});
				});

				$(".pieces").change(function () {
					if($('#shipping_mode').val() == 2)
						$(".pieces").addClass("exceed_pieces");
					else
						$(".pieces").removeClass("exceed_pieces");
				});

				$("#shipping_mode").change(function () {
					$('#pieces').val(null).trigger('change');
				});
			//todo : increse of pieces to 500 end

            $('#open_shipment').checkboxpicker();

			

			var order_date = $('#order_date').pickadate({
				firstDay: 1,
				clear: 'Clear',
				format:'dd mmmm, yyyy',
				selectYears: true,
				selectMonths: true,
				formatSubmit: 'yyyy-mm-dd',
				hiddenSuffix: '_formatted',
				onOpen: function() {
					$('#from_date_root').css('top','40px');
				}
			});


			@if (session('print'))
			$.ajax({
				url: '{!! route('cod.shipment.book.print_air_waybill') !!}',
				method: 'POST',
				data: {
					'_token': '{{ csrf_token() }}',
					'ids[]': '{{ session('print') }}'
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
			@endif

			function shipping_mode_same_day(pickup_city, consignee_city) {
				if (pickup_city != consignee_city) {
					if ($('#shipping_mode').val() == 4) {
						$('#shipping_mode').val(null).trigger('change');

						$('#shipping_same-day').addClass('d-none');
					}

					$('#shipping_mode option[value="4"]').attr('disabled', 'disabled');
				}
				else {
					$('#shipping_mode option[value="4"]').removeAttr('disabled');
				}

				$('#shipping_mode').select2('destroy').select2({
					width: '100%',
					placeholder: 'Mode of Shipping*'
				}).bind('change', function() {
					if ($(this).hasClass('danger')) {
						$(this).valid();
					}

					if (this.value == 4) {
						$('#shipping_same-day').removeClass('d-none');
					}
					else {
						$('#shipping_same-day').addClass('d-none');
					}
				});
			}

			function try_and_buy_product_numbering() {
				setTimeout(function () {
					$('#try_and_buy .repeater div .product').each(function(index) {
						$(this).children('div').children('h4').children('span').html((index + 1));
					});
				}, 500);
			}

			function try_and_buy_total_quantity() {
				var total_quantity = 0;

				$('#try_and_buy .repeater div .product .quantity').each(function(index) {
					if (this.value != '') {
						total_quantity += parseInt(this.value);

						$('#try_and_buy #total_quantity span').html(total_quantity);
					}
				});
			}

			function try_and_buy_total_price() {
				var total_price = 0;

				$('#try_and_buy .repeater div .product .price').each(function(index) {
					if (this.value != '') {
						total_price += parseInt(this.value.replace(',', ''));

						$('#try_and_buy #total_price span').html(total_price.toLocaleString());
					}
				});
			}

			$('#charges_mode').select2({
				width: '100%',
				placeholder: 'Charges Mode*'
			}).bind('change', function() {
				if ($(this).hasClass('danger')) {
					$(this).valid();
				}
			});

			function shipping_modes() {
				if ($('#pickup_address').val() == 0) {
					var pickup_city_id = $('#new_pickup_city').val();
					$('#pickup_city_name').addClass('d-none');
				}
				else {
					var pickup_city_id = $('#pickup_address').find(':selected').data('city-id');
					var pickup_city_name = $('#pickup_address').find(':selected').data('city-name');
					$('#pickup_city_name').removeClass('d-none');
					$('#pickup_city_name').html('City : ' + pickup_city_name);
				}

				consignee_city_id = $('#consignee_city').val();

				if (consignee_city_id) {
					$.ajax({
						url: '{!! route('cod.shipment.book.shipping_modes') !!}',
						method: 'POST',
						data: {
							'_token': '{{ csrf_token() }}',
							'service_type_id': service_type,
							'pickup_city_id': pickup_city_id,
							'consignee_city_id': consignee_city_id
						}
					})
							.done(function(data) {
								$('#shipping_mode').html('').select2('destroy');

								default_shipping_mode = false;

								if (data.status == 0) {
									$.each(data.shipping_modes, function (index, shipping_mode) {
										if(data.default_shipping_mode === shipping_mode['id']) {
											$('#shipping_mode').append('<option value="' + shipping_mode['id'] + '" selected>' + shipping_mode['mode'] + '</option>');

											default_shipping_mode = true;
										}
										else{
											$('#shipping_mode').append('<option value="' + shipping_mode['id'] + '">' + shipping_mode['mode'] + '</option>');
										}
									});

									present = true;
								}
								else {
									toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

									present = false;
								}
								if(default_shipping_mode === false) {
									$('#shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
										width: '100%',
										placeholder: 'Mode of Shipping*'
									}).bind('change', function () {
										if ($(this).hasClass('danger')) {
											$(this).valid();
										}

										if (this.value == 4) {
											$('#shipping_same-day').removeClass('d-none');
										} else {
											$('#shipping_same-day').addClass('d-none');
										}
									});
								}
								else{
									$('#shipping_mode').select2({
										width: '100%',
										placeholder: 'Mode of Shipping*'
									}).bind('change', function () {
										if ($(this).hasClass('danger')) {
											$(this).valid();
										}

										if (this.value == 4) {
											$('#shipping_same-day').removeClass('d-none');
										} else {
											$('#shipping_same-day').addClass('d-none');
										}
									});
								}

								if (present) {
									$('#shipping_mode').prop('disabled', false);
								}
								else {
									$('#shipping_mode').prop('disabled', true);
								}
							});
				}
			}

			$('#select_service_type').modal({
				backdrop: 'static',
				keyboard: false,
				show: false
			});

			$('#select_service_type form #service_type').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Service Type*'
			});

			var service_type = '';

			@if (!Session::has('service_type_id'))
			$('#select_service_type').modal('show');
			@else

			service_type = '{{ Session::get('service_type_id') }}';

			if(service_type == 1){
				$('#pieces_quantity').removeClass('d-none');
			}
			if (service_type == 2) {
				$('#replacement').removeClass('d-none');
				$('#try_and_buy_charges_div').addClass('d-none');

			}
			if (service_type == 3) {
				$('#regular').addClass('d-none');
				$('#try_and_buy').removeClass('d-none');
				$('#try_and_buy_charges_div').removeClass('d-none');
				$('#amount').prop('disabled', true);
			}
			else{
				$('#amount').prop('disabled', false);
				$('#try_and_buy_charges_div').addClass('d-none');
			}

			$('#select_service_type form #service_type').val(service_type).trigger('change');
			@endif

			$('#select_service_type form').bind('submit', function(e) {
				e.preventDefault();

				var selected = $('#select_service_type form #service_type').find(':selected');

				service_type = selected.val();

				if (service_type !== '' && service_type !== undefined && service_type !== null) {
					var consignee_email = $('input[name="consignee_email_address"]');
					$('#select_service_type form #service_type-error').addClass('d-none');

					if (service_type == 1) {
						$('#shipping_header_div').removeClass('col col_6');
						$('#order_information_header_div').removeClass('col col_6');
						$('#consignee_header_div').removeClass('col col_6');
						$('#shipping_header_div').addClass('col col_custom');
						$('#order_information_header_div').addClass('col col_custom_middle');
						$('#consignee_header_div').addClass('col col_custom');
						$('#regular').removeClass('d-none');
						$('#payment_info').removeClass('d-none');
						$('#replacement').addClass('d-none');
						$('#try_and_buy').addClass('d-none');
						$('#try_and_buy_charges_div').addClass('d-none');
						$('#order_header_info').removeClass('mt-2');
						$('#shipping_header_info').removeClass('mt-2');
						$('#shipper_header_info').html('Shipper Information');
						$('#consignee_header_info').html('Consignee Information');
						$('#amount').prop('disabled', false);
						$('#pieces_quantity').removeClass('d-none');
						$('#self_collection_div').removeClass('d-none');
						$('#return_address_div').removeClass('d-none');

					}
					else if (service_type == 2) {
						$('#shipping_header_div').removeClass('col col_6');
						$('#order_information_header_div').removeClass('col col_6');
						$('#consignee_header_div').removeClass('col col_6');
						$('#shipping_header_div').addClass('col col_custom');
						$('#order_information_header_div').addClass('col col_custom_middle');
						$('#consignee_header_div').addClass('col col_custom');
						$('#regular').removeClass('d-none');
						$('#payment_info').removeClass('d-none');
						$('#replacement').removeClass('d-none');
						$('#try_and_buy').addClass('d-none');
						$('#order_header_info').removeClass('mt-2');
						$('#shipping_header_info').removeClass('mt-2');
						$('#shipper_header_info').html('Shipper Information');
						$('#consignee_header_info').html('Consignee Information');
						$('#amount').prop('disabled', false);
						$('#try_and_buy_charges_div').addClass('d-none');
						$('#pieces_quantity').addClass('d-none');
						$('#self_collection_div').addClass('d-none');
						$('#return_city_name').addClass('d-none');
						$('#return_address_div').addClass('d-none');

					}
					else if (service_type == 3) {
						$('#shipping_header_div').removeClass('col col_6');
						$('#order_information_header_div').removeClass('col col_6');
						$('#consignee_header_div').removeClass('col col_6');
						$('#shipping_header_div').addClass('col col_custom');
						$('#order_information_header_div').addClass('col col_custom_middle');
						$('#consignee_header_div').addClass('col col_custom');
						$('#regular').addClass('d-none');
						$('#replacement').addClass('d-none');
						$('#payment_info').removeClass('d-none');
						$('#try_and_buy').removeClass('d-none');
						$('#order_header_info').removeClass('mt-2');
						$('#shipping_header_info').removeClass('mt-2');
						$('#shipper_header_info').html('Shipper Information');
						$('#consignee_header_info').html('Consignee Information');
						$('#amount').prop('disabled', true);
						$('#try_and_buy_charges_div').removeClass('d-none');
						$('#pieces_quantity').addClass('d-none');
						$('#self_collection_div').addClass('d-none');
						$('#return_city_name').addClass('d-none');
						$('#return_address_div').addClass('d-none');

					}
					else if (service_type == 5) {
						$('#shipping_header_div').removeClass('col col_custom');
						$('#order_information_header_div').removeClass('col col_custom_middle');
						$('#consignee_header_div').removeClass('col col_custom');
						$('#shipping_header_div').addClass('col col_6');
						$('#order_information_header_div').addClass('col col_6');
						$('#consignee_header_div').addClass('col col_6');
						$('#regular').removeClass('d-none');
						$('#replacement').addClass('d-none');
						$('#try_and_buy').addClass('d-none');
						$('#order_header_info').addClass('mt-2');
						$('#shipping_header_info').addClass('mt-2');
						$('#payment_info').addClass('d-none');
						$('#charges_mode_div').addClass('d-none');
						$('#shipper_header_info').html('Shipper Information<br><h6>(Delivery Address)</h6>');
						$('#consignee_header_info').html('Consignee Information<br><h6>(Pickup/Collection Address)</h6>');
						$('#amount').prop('disabled', false);
						$('#try_and_buy_charges_div').addClass('d-none');
						$('#pieces_quantity').addClass('d-none');
						$('#self_collection_div').addClass('d-none');
						$('#return_city_name').addClass('d-none');
						$('#return_address_div').addClass('d-none');


					}
					if(service_type == 5){
						consignee_email.attr('data-toggle', 'tooltip');
						consignee_email.attr('data-placement', 'top');
						consignee_email.attr('data-original-title', 'Please add email address so that we can sent address label to your customer.');
						consignee_email.tooltip('show');
					}else{
						consignee_email.attr('data-toggle', '');
						consignee_email.attr('data-placement', '');
						consignee_email.attr('data-original-title', '');
						consignee_email.tooltip('hide');
					}
					$('#booking_form #selected_service_type').val(service_type);

					$('#selected_service_type_name').html('(' + selected.html() + ')');

					$('#select_service_type').modal('hide');

					shipping_modes();
					set_return_city();
				}
				else {
					$('#select_service_type form #service_type-error').removeClass('d-none');
				}
			});

			{{--console.log(@json($user->shipping[0]['id']));--}}

			{{--@if (!$default_pickup_address)--}}
			{{--$('#pickup_address').prepend('<option value="" selected="selected"></option>');--}}
			{{--@endif--}}

			$('#pickup_address').select2({
				width: '100%',
				placeholder: 'Pickup Address*'
			}).bind('change', function () {
				$(this).valid();

				shipping_modes();

				if (this.value == 0) {
					$('#new_pickup_address').removeClass('d-none');
				} else {
					$('#new_pickup_address').addClass('d-none');
				}

				var pickup_city = $(this).find(':selected').data('city-id');
				var consignee_city = $('#consignee_city').val();

				shipping_mode_same_day(pickup_city, consignee_city);
			});

			$('#new_pickup_city').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'City*'
			}).bind('change', function() {
				$(this).valid();

				shipping_modes();

				var pickup_city = $(this).val();
				var consignee_city = $('#consignee_city').val();

				shipping_mode_same_day(pickup_city, consignee_city);
			});
			

			$('#new_return_city').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'City*'
			}).bind('change', function() {
				$(this).valid();
			});


			function set_return_city(){
				if ($('#return_address').val() == 0 || service_type != 1)
				{
					var return_city_id = $('#return_address').val();
					$('#return_city_name').addClass('d-none');
				}
				else{
					var return_city_name = $('#return_address').find(':selected').data('city-name');
					$('#return_city_name').removeClass('d-none');
					$('#return_city_name').html('City : ' + return_city_name);
				}

			}

			if($('#return_address').val() == 0 | $('#return_address').val() == ''){
				$('#return_address').prepend('<option value="" selected="selected"></option>').select2({
					width: '100%',
					placeholder: 'Return Address'
				}).bind('change', function () {
					$(this).valid();

					if (this.value == 0) {
						$('#new_return_address').removeClass('d-none');
					}
					else {
						$('#new_return_address').addClass('d-none');
					}

					set_return_city();
				});
			}
			else{
				$('#return_address').select2({
					width: '100%',
					placeholder: 'Return Address'
				}).bind('change', function () {
					$(this).valid();

					if (this.value == 0) {
						$('#new_return_address').removeClass('d-none');
					}
					else {
						$('#new_return_address').addClass('d-none');
					}

					set_return_city();
				});
			}

			

			$("#consignee_info").select2({
				width:'100%',
				placeholder: "Search Here...",
				minimumInputLength: 5,
				ajax: {
					url: '{{ route('cod.shipment.book.get_consignee_infos') }}',
					dataType: 'json',
					type: "GET",
					quietMillis: 50,
					data: function (params) {
						return {
							q: params.term,
							page: params.page,
							'shipper': '{{session('user_id')}}'
						};
					},
					processResults: function (data, params) {
						params.page = params.page || 1;

						return {
							results: data.data,
							pagination: {
								more: (params.page * 30) < data.total_count
							}
						};
					},
					cache: true
				},
				escapeMarkup: function (markup) { return markup; },
				templateResult: formatRepo,
				templateSelection: formatRepoSelection

			});
			function formatRepo (repo) {
				if (repo.loading) return repo.text;
				var markup = "<option value='" + repo.id + "'>"+ repo.full_name +"</option>";

				return markup;
			}
			function formatRepoSelection (repo) {
				return repo.full_name || repo.text;
			}

			var blacklist = false;
			var blacklist_message = '';
			var blacklist_color = '';
			function check_consignee_return_ratio(){
				var phone = $('input[name="consignee_phone_number_1"]').val();
				if(phone){
					$.ajax({
						url:'{!! route('cod.shipment.book.check_consignee_return_ratio') !!}',
						method: 'POST',
						data: {
							'_token': '{{ csrf_token() }}',
							'phone': phone,
						}
					}).done(function (data) {
						if(data.status == 0){
							blacklist = true;
							blacklist_message = data.message;
							blacklist_color = data.color;
							return true;
						}
					});
				}
			}

			$('#consignee_info').on('select2:select', function () {
				var id = parseInt($(this).val());
				if(id){
					$.ajax({
						url:'{!! route('cod.shipment.book.get_consignee_info') !!}',
						method: 'POST',
						data: {
							'_token': '{{ csrf_token() }}',
							'id': id,
						}
					}).done(function (data) {
						if(data.status){
							$('#consignee_city').val(data.details.city_id).trigger('change');
							$('input[name="consignee_name"]').val(data.details.name);
							$('#consignee_address').val(data.details.address);
							$('input[name="consignee_phone_number_1"]').val(data.details.phone_number_1).change();
							$('input[name="consignee_phone_number_2"]').val(data.details.phone_number_2);
							$('input[name="consignee_email_address"]').val(data.details.email);
						}else{
							toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
						}
					});
				}
			});

			$('input[name="consignee_phone_number_1"]').bind('change paste keyup', function () {
				if($(this).val().match(/\d/g) != null){
					var length = $(this).val().match(/\d/g).length;
				}
				else{
					var length = 0;
				}
				if(length == 11){
					check_consignee_return_ratio();
				}


			});

			$('#information_display').checkboxpicker();
			$('#self_collection').checkboxpicker();
			


			$('#consignee_city').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'City*'
			}).bind('change', function() {
				$(this).valid();

				
				
				shipping_modes();

				if ($('#pickup_address').val() == 0) {
					var pickup_city = $('#new_pickup_city').val();
				}
				else {
					var pickup_city = $('#pickup_address').find(':selected').data('city-id');
				}

				var consignee_city = $(this).val();

				shipping_mode_same_day(pickup_city, consignee_city);
			});
			$('#product_type').select2({
				width: '100%',
				placeholder: 'Product Type*'
			}).bind('change', function() {
				$(this).valid();
			});

			$('#regular .insurance').checkboxpicker().bind('change', function() {
				var parent = $(this).parent('.form-group').next('.form-group');

				if (this.checked) {
					parent.removeClass('d-none');
				}
				else {
					parent.addClass('d-none');

					parent.children('#item_price-error').remove();
				}
			});

			$('#try_and_buy .insurance').checkboxpicker();

			// $('#package_type').checkboxpicker();

			var current_date = '{{$date}}';

			$('#replacement_product_type').select2({
				width: '100%',
				placeholder: 'Product Type*'
			}).bind('change', function() {
				$(this).valid();
			});

			$('#try_and_buy .select2').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Product Type*'
			}).bind('change', function() {
				$(this).valid();
			});
			var repeater_limit = 5;
			var repeater_count = 1;
			$('#try_and_buy .repeater').repeater({
				isFirstItemUndeletable: true,
				show: function() {
					repeater_count++;
					$(this).find('.select2-container--default').remove();

					$(this).find('.select2').prepend('<option value="" selected="selected"></option>').select2({
						width: '100%',
						placeholder: 'Product Type*'
					}).bind('change', function() {
						$(this).valid();
					});

					$(this).slideDown();

					$('html, body').animate({
						scrollTop: ($(this).offset().top - $('.header-navbar').height())
					}, 1000);

					$(this).find('.quantity').TouchSpin({
						min: 1,
						max: 10000,
						buttondown_class: 'btn btn-primary rounded-left',
						buttonup_class: 'btn btn-primary rounded-right',
						buttondown_txt: '<i class="ft-minus"></i>',
						buttonup_txt: '<i class="ft-plus"></i>'
					}).bind('input change', function() {
						$(this).tooltip('show');

						if ($(this).hasClass('danger')) {
							$(this).valid();
						}

						if (service_type == 3) {
							try_and_buy_total_quantity();
						}
					});

					$('.bootstrap-touchspin-down, .bootstrap-touchspin-up').attr('tabindex', -1);

					$(this).find('.price').inputmask({
						'alias': 'integer',
						'allowMinus': false,
						'allowPlus': false,
						'groupSeparator': ',',
						'autoGroup': true,
						'min': 1,
						'max': 100000
					}).bind('input change', function() {
						if (service_type == 3) {
							try_and_buy_total_price();
						}
					});

					var insurance = $(this).find('.insurance');

					insurance.parent('.form-group').children('.btn-group').remove();

					insurance.checkboxpicker();

					if(repeater_count === repeater_limit){

						console.log(repeater_count);
						$("#try_and_buy_add").hide("slow");
					}

					try_and_buy_product_numbering();
				},
				hide: function(delete_element) {
					var id = $(this).children('div').children('h4').children('span').html();

					swal({
						title: 'Are you sure?',
						text: 'You want to delete Product #' + id + '?',
						icon: 'warning',
						buttons: {
							cancel: {
								text: 'Close',
								value: null,
								visible: true,
								closeModal: true,
							},
							confirm: {
								text: 'Delete',
								value: true,
								visible: true,
								closeModal: true
							}
						},
						closeOnClickOutside: false,
						closeOnEsc: false,
						dangerMode: true
					}).then(function(confirm) {
						if (confirm) {
							repeater_count--;

							if(repeater_count < repeater_limit){
								$("#try_and_buy_add").show("slow");
							}
							$(this).slideUp(delete_element);

							try_and_buy_product_numbering();
						}
					});
				}
			});

			var allow_origin_city = false;
			var allow_destination_city = false;
			function check_city_booking_allow(){

				if ($('#pickup_address').val() == 0) {
					var pickup_city_id = $('#new_pickup_city').val();
				}
				else {
					var pickup_city_id = $('#pickup_address').find(':selected').data('city-id');
				}

				var consignee_city_id = parseInt($('#consignee_city').val());
				var shipping_mode_id = parseInt($('#shipping_mode').val());
				if((pickup_city_id != '') && (consignee_city_id != '') && (shipping_mode_id != '')){
					$.ajax({
						url:'{!! route('cod.shipment.book.check_shipment_allowed_city') !!}',
						method: 'POST',
						data: {
							'_token': '{{ csrf_token() }}',
							'shipping_mode_id': shipping_mode_id,
							'consignee_city_id': consignee_city_id,
							'pickup_city_id': pickup_city_id,
							'service_type_id': service_type
						}
					}).done(function (data) {
						if(data.origin_city_allowed == true){
							allow_origin_city = true;
						}
						if(data.destination_city_allowed == true){
							allow_destination_city = true;
						}

					});
				}
			}

			$('#shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Mode of Shipping*',
				disabled: true,
			}).bind('change', function() {
				if ($(this).hasClass('danger')) {
					$(this).valid();
				}

				if (this.value == 4) {
					$('#shipping_same-day').removeClass('d-none');
				}
				else {
					$('#shipping_same-day').addClass('d-none');
				}
				check_city_booking_allow();
			});

			$('#amount').bind('keypress', function () {
				$('#booking_form .submission').attr('disabled', true);
			});

			$('#amount, #consignee_city, #pickup_address').change(function(){
				$('#booking_form .submission').attr('disabled', true);

				$('#span').remove();
				if ($('#pickup_address').val() == 0) {
					var pickup_city_id = $('#new_pickup_city').val();
				}
				else {
					var pickup_city_id = $('#pickup_address').find(':selected').data('city-id');
				}

				$.ajax({
					url:'{!! route('cod.shipment.book.check_cod_cap_zone_classes') !!}',
					method: 'POST',
					data: {
						'_token': '{{ csrf_token() }}',
						'amount': $('#amount').val(),
						'pickup_city': pickup_city_id,
						'consignee_city': $('#consignee_city').val(),
					}
				}).done(function (data) {
					if(data.status === 1){
						$('#span').remove();
						var span = '<span id="span" style="color: red">'+data.error+'</span>';
						$('#amount').val('');
						$('#amount').parent('div').append(span);

					}
					if(data.status === 2) {
						$('#span').remove();

						$('#booking_form .submission').attr('disabled', false);
					}
					if(data.status === 0) {
						$('#span').remove();
						var span = '<span id="span" style="color: red">'+data.error+'</span>';
						$('#amount').val('');
						$('#amount').parent('div').append(span);
					}
					if(data.status === 3) {
						$('#span').remove();
						if ($('#pickup_address').val() == "") {
							var span = '<span id="span" style="color: red">Pickup address is required</span>';
							$('#pickup_address').parent('div').append(span);
						}
						else{
							var span = '<span id="span" style="color: red">'+data.error+'</span>';
							$('#new_pickup_city').parent('div').append(span);
						}
						$('#amount').val('');
					}
					if(data.status === 4) {
						$('#span').remove();
						var span = '<span id="span" style="color: red">'+data.error+'</span>';
						$('#consignee_city').parent('div').append(span);
						$('#amount').val('');
					}
				});

				var shipping_mode_id = $('#shipping_mode').val();
				if(shipping_mode_id){
					check_city_booking_allow();
				}
			});

			$('#same-day_timing').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Same-day Timing*'
			}).bind('change', function() {
				$(this).valid();
			});

			$('#payment_mode').select2({
				width: '100%',
				placeholder: 'Mode of Payment*'
			}).bind('change', function() {
				$(this).valid();
			});

			function isEmpty(obj) {
				for(var key in obj) {
					if(obj.hasOwnProperty(key))
						return false;
				}
				return true;
			}

			var breakup_rows = {};

			var check = @json($check);

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
					check_consignee_return_ratio();
					check_city_booking_allow();

					if((allow_origin_city == true) && (allow_destination_city ==  true)){
						var pressed_button = $(this.submitButton);

						$(form).append('<input type="hidden" name="' + pressed_button.attr('name') + '" value="' + pressed_button.attr('value') + '">');

						$(form).find('button[type=submit]').attr('disabled', 'disabled');
						var consignee_address = $('#consignee_address').val();
						var strArray = consignee_address.split(/[ ,]+/);
						var present = [];
						for(k=0;k<strArray.length;k++) {
							for (i = 0; i < check.length; i++) {
								if(JSON.stringify(strArray[k]).toLowerCase()=== JSON.stringify(check[i]).toLowerCase()){
									present.push(strArray[k]);
								}
							}
						}
						if(!isEmpty(breakup_rows)){
							$(form).append('<input type="hidden" name="cod_breakup" value="TRUE">');
							var cod_breakup_shipping_charges = $('#cod_breakup_shipping_charges').val();
							var cod_breakup_total_cod = $('#cod_breakup_total').val();
							$(form).append('<input type="hidden" name="cod_breakup_shipping_charges" value="' +cod_breakup_shipping_charges + '">');
							$(form).append('<input type="hidden" name="cod_breakup_total_cod" value="' + cod_breakup_total_cod + '">');

							$.each(breakup_rows, function (index, value) {
								$(form).append('<input type="hidden" name="cod_breakup_description[]" value="' + value.description + '">');
								$(form).append('<input type="hidden" name="cod_breakup_amount[]" value="' + value.amount + '">');
							});
						}

						if(present.length > 0){
							var url = '{{asset('img/nsa_osa.png')}}';
							var html = '<div class="row justify-content-center"><img src="' + url + '"></div>';
							html += '<div class="row justify-content-center"><h2><b>A Possible Address Anomaly: ' + present + ' Detected!</b></h2></div>';
							html += '<div class="text-left">In case of,<br/>';
							html += '<b>Out of Service Area:</b> Additional charges may apply.</br>';
							html += '<b>Non Service Area:</b> Shipment may be returned.</br>';
							html += '<b>For assistance, Call:</b> 021-38772222</br></div>';
							content = document.createElement('div');
							content.innerHTML = html;
							swal({
								content: content,
								buttons: {
									cancel: {
										text: 'Cancel',
										value: null,
										visible: true,
										closeModal: true,
									},
									confirm: {
										text: 'Continue to Booking',
										value: true,
										visible: true,
										closeModal: true
									}
								},
								closeOnClickOutside: false,
								closeOnEsc: false,
								// dangerMode: true
							}).then(function(confirm) {
								if(confirm) {

									if(blacklist == true){
										var html = '<div class="row justify-content-center p-1" style="background-color: '+ blacklist_color +'; color:white;">'+ blacklist_message +'</div>';
										content = document.createElement('div');
										content.innerHTML = html;
										swal({
											content: content,
											buttons: {
												cancel: {
													text: 'Cancel',
													value: null,
													visible: true,
													closeModal: true,
												},
												confirm: {
													text: 'Book Anyway',
													value: true,
													visible: true,
													closeModal: true
												}
											},
											closeOnClickOutside: false,
											closeOnEsc: false,
											// dangerMode: true
										}).then(function(confirm) {
											if (confirm) {
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
											else{
												$(form).find('button[type=submit]').prop('disabled', false);
											}
										});
									}else{
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

								}
								else{
									$(form).find('button[type=submit]').prop('disabled', false);
								}
							});
						}
						else {
							if(blacklist == true) {
								var html = '<div class="row justify-content-center p-1" style="background-color: '+ blacklist_color +'; color:white;">' + blacklist_message + '</div>';
								content = document.createElement('div');
								content.innerHTML = html;
								swal({
									content: content,
									buttons: {
										cancel: {
											text: 'Cancel',
											value: null,
											visible: true,
											closeModal: true,
										},
										confirm: {
											text: 'Book Anyway',
											value: true,
											visible: true,
											closeModal: true
										}
									},
									closeOnClickOutside: false,
									closeOnEsc: false,
									// dangerMode: true
								}).then(function (confirm) {
									if (confirm) {
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
									else{
										$(form).find('button[type=submit]').prop('disabled', false);
									}
								});
							}else{
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

						}
					}
					else{
						if(allow_origin_city == false){
							var error = 'Origin city not allowed, please contact your sales person!';
							toastr.error(error, 'Error!', {
								positionClass: 'toast-top-center',
								containerId: 'toast-top-center'
							});
						}
						if(allow_destination_city == false){
							var error = 'Destination city not allowed, please contact your sales person!';
							toastr.error(error, 'Error!', {
								positionClass: 'toast-top-center',
								containerId: 'toast-top-center'
							});
						}
					}
				}
			});

			$('.phone_number').inputmask({
				'mask': '9999-9999999',
				'clearIncomplete': true
			});

			$(this).find('.quantity').TouchSpin({
				min: 1,
				max: 10000,
				buttondown_class: 'btn btn-primary rounded-left',
				buttonup_class: 'btn btn-primary rounded-right',
				buttondown_txt: '<i class="ft-minus"></i>',
				buttonup_txt: '<i class="ft-plus"></i>'
			}).bind('input change', function() {
				$(this).tooltip('show');

				if ($(this).hasClass('danger')) {
					$(this).valid();
				}

				if (service_type == 3) {
					try_and_buy_total_quantity();
				}
			});

			$('.bootstrap-touchspin-down, .bootstrap-touchspin-up').attr('tabindex', -1);

			$('.price').inputmask({
				'alias': 'integer',
				'allowMinus': false,
				'allowPlus': false,
				'groupSeparator': ',',
				'autoGroup': true,
				'min': 1,
				'max': 100000
			}).bind('input change', function() {
				if (service_type == 3) {
					try_and_buy_total_price();
				}
			});

			$('.weight').inputmask({
				'alias': 'decimal',
				'allowMinus': false,
				'allowPlus': false,
				'digits': 2,
				'min': 0.1,
				'max': 100000
			});

			$('.amount').inputmask({
				'alias': 'integer',
				'allowMinus': false,
				'allowPlus': false
			});

			$('.parcel_value').inputmask({
				'alias': 'integer',
				'allowMinus': false,
				'allowPlus': false
			});
			
					@if($user->logo_status)
			var cb_table = $('#cod_breakup_table').DataTable({
						dom: '<"d-inline-block"l><"pull-right"B>tipr',
						buttons: [{
							title: 'Add Row',
							className: 'btn btn-primary mb-1',
							text: '<i class="la la-plus"></i> Add Row',
							action: function (e) {
								add_row();
							}
						}],
						ordering: false,
						paging: false,
						columns: [
							{
								orderable: false,
								searchable: false,
								name: 'serial_number',
								class: 'align-middle serial_number',
								targets: 0,
								render: function (data, type, row) {
									return '';
								}
							},
							{name: 'item_description', class: 'align-middle item_description form-group', width: '40%'},
							{name: 'amount', class: 'align-middle amount form-group'},
							{name: 'action', class: 'align-middle action'},
						],
						rowCallback: function (row, data, index) {
							var info = cb_table.page.info();
							$('td:eq(0)', row).html(index + 1 + info.page * info.length);

						},
						initComplete: function () {
							this.api().table().columns.adjust();
						}
					});
			$('#cod_breakup_checkbox').checkboxpicker();
			$('#cod_breakup_checkbox').on('change', function() {
				var check = $(this);
				if(check.is(':checked')){
					$('#cod_breakup_modal').modal('show');
				}else{
					breakup_rows = {};
					cb_table.clear();
				}
			});
			var rows_count = 0;
			function add_row() {
				rows_count++;
				var item_description_input = '<input class="form-control item_description" name="item_description['+rows_count+']" placeholder="Item Description*" data-rule-required="true" data-msg-required="Item description is required">';
				var amount_input = '<input class="form-control amount" name="amount['+rows_count+']" placeholder="Amount*" data-rule-required="true" data-msg-required="Amount is required">';

				if(rows_count == 1){
					var remove = '';
				}else{
					var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-sm btn-danger remove_row"><i class="la la-close"></i></a>';

				}
				// deposit_table.row.add(0,1,2,3,4,5);
				cb_table.row.add([0, item_description_input, amount_input,remove]).node().id = rows_count;
				cb_table.draw(true);
				$('#cod_breakup_submit_btn').attr('disabled', false);

				$('input.amount').inputmask({
					'alias': 'decimal',
					'allowMinus': false,
					'allowPlus': false,
					'rightAlign': false,
					'digits': 2,
					'min': 0.00,
					'max': 10000000.00
				});
			}
			$('body').on('change','#cod_breakup_form .item_description',function() {
				$(this).val($(this).val().trim());
			});


			var cod_breakup_form = $('#cod_breakup_form').validate({
				errorClass: 'danger',
				successClass: 'success',
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parents('.form-group'));
				},
				submitHandler: function(form) {
					breakup_rows = {};
					cb_table.rows().every(function(index) {
						var node = $(this.node());
						var id = node.attr('id');
						var description = node.find('td.item_description input').val();
						var amount = node.find('td.amount input').val();
						breakup_rows[id] = {description: description, amount: amount};
					});
					$('#cod_breakup_modal').modal('hide');
					var total_cod_breakup = $('#cod_breakup_total').val();

					$('#amount').val(total_cod_breakup);

				}
			});
			$('body').on('click', '#cod_breakup_table a.remove_row',function () {

				cb_table.row( $(this).parents('tr') ).remove().draw();
			});
			@endif
			$('.order_id').inputmask({
				'alias': 'integer',
				'allowMinus': false,
				'allowPlus': false,
				'min': 0,
				'max': 1000000000000
			});
		});
	</script>
@endsection