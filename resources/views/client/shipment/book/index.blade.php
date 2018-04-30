@extends('client.layout.master')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1>
					Book a Shipment
					<span id="selected_service_type_name">{{ (Session::has('service_type_name')) ? ('(' . Session::get('service_type_name') . ')') : '' }}</span>
					<button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#select_service_type">Change Service Type</button>
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('client.inc.messages')

							<form id="booking_form" class="form-horizontal" method="POST" action="{{ url('cod/shipment/book') }}">
								{{ csrf_field() }}

								<input type="hidden" name="selected_service_type" id="selected_service_type">

								<div class="row">
									<div class="col">
										<h4 class="form-section mb-2 text-center">Shipper Information</h4>

										<div class="form-group">
											<p class="mb-2 border-bottom border-dark text-center font-medium-1 text-bold-600">{{ $user->name }}</p>
										</div>

										<div class="form-group">
											<p class="mb-2 border-bottom border-dark text-center font-medium-1 text-bold-600">{{ $user->phone }}</p>
										</div>

										<div class="form-group">
											<select name="pickup_address" class="select2" id="pickup_address">
												<option value="0">New</option>

												@foreach($user->shipping as $shipping_information)
													<option value="{{ $shipping_information['id'] }}">{{ $shipping_information['poc'] }}: {{ $shipping_information['pickup_address'] }}, {{ $shipping_information['city']['city_name'] }}</option>
												@endforeach
											</select>
										</div>

										<div id="new_pickup_address" class="d-none">
											<div class="form-group">
												<textarea name="pickup_address" class="form-control" placeholder="Pickup Address"></textarea>
											</div>

											<div class="form-group">
												<input type="text" name="pickup_point_of_contact" class="form-control" placeholder="Point of Contact">
											</div>

											<div class="form-group">
												<input type="text" name="pickup_phone_number" class="form-control" placeholder="Phone Number">
											</div>

											<div class="form-group">
												<input type="email" class="form-control" name="pickup_email_address" placeholder="Email Address">
											</div>

											<div class="form-group">
												<select name="pickup_city" class="select2" id="pickup_city">
													@foreach($cities as $city)
														<option value="{{ $city->city_code }}">{{ $city->city_name }}</option>
													@endforeach
												</select>
											</div>

											<div class="form-group text-center">
												<label>Show Information on Address Label</label>
												<input type="checkbox" class="switch hidden" id="information_display" checked="checked">
											</div>
										</div>
									</div>

									<div class="col">
										<h4 class="form-section mb-2 text-center">Consignee Information</h4>

										<div class="form-group">
											<select name="consignee_city" class="select2" id="consignee_city">
												@foreach($cities as $city)
													<option value="{{ $city->city_code }}">{{ $city->city_name }}</option>
												@endforeach
											</select>
										</div>

										<div class="form-group">
											<input type="text" name="consignee_name" class="form-control" placeholder="Name">
										</div>

										<div class="form-group">
											<textarea name="consignee_address" class="form-control" placeholder="Address"></textarea>
										</div>

										<div class="form-group">
											<input type="text" name="consignee_phone_number_1" class="form-control" placeholder="Phone Number 1">
										</div>

										<div class="form-group">
											<input type="text" name="consignee_phone_number_2" class="form-control" placeholder="Phone Number 2">
										</div>

										<div class="form-group">
											<input type="email" name="consignee_email_address" class="form-control" placeholder="Email Address">
										</div>
									</div>

									<div class="col">
										<h4 class="form-section mb-2 text-center">Order Information</h4>

										<div class="form-group">
											<input name="order_id" class="form-control" placeholder="Order ID">
										</div>

										<div class="form-group">
											<select name="product_type" class="select2" id="product_type">
												@foreach($products as $product)
													<option value="{{ $product->id }}">{{ $product->product_name }}</option>
												@endforeach
											</select>
										</div>

										<div class="form-group">
											<textarea name="item_description" class="form-control" placeholder="Item Description"></textarea>
										</div>

										<div class="form-group">
											<input type="number" name="item_quantity" class="form-control" placeholder="Item Quantity" min='1'>
										</div>

										<div class="input-group mb-1">
											<div class="input-group-prepend">
												<span class="input-group-text">
													<span class="la la-calendar-o"></span>
												</span>
											</div>

											<input type="text" name="pickup_date" class="form-control pickadate" id="pickup_date" placeholder="Pickup Date">
										</div>

										<div class="form-group">
											<textarea name="special_instructions" class="form-control" placeholder="Special Instructions"></textarea>
										</div>

										<div id="replacement" class="d-none">
											<div class="form-group">
												<select name="replacement_product_type" class="select2" id="replacement_product_type">
													@foreach($products as $product)
														<option value="{{ $product->id }}">{{ $product->product_name }}</option>
													@endforeach
												</select>
											</div>

											<div class="form-group">
												<textarea name="replacement_item_description" class="form-control" placeholder="Replacement Item Description"></textarea>
											</div>

											<div class="form-group">
												<input type="number" name="replacement_item_quantity" class="form-control" placeholder="Replacement Item Quantity" min='1'>
											</div>
										</div>
									</div>

									<div class="col">
										<h4 class="form-section mb-2 text-center">Shipping Information</h4>

										<div class="form-group">
											<input type="text" name="estimated_weight" class="form-control" placeholder="Estimated Weight">
										</div>

										<div class="form-group">
											<select name="shipping_mode" class="select2" id="shipping_mode">
												@foreach($shipping_modes as $shipping_mode)
													<option value="{{ $shipping_mode->id }}">{{ $shipping_mode->mode }}</option>
												@endforeach
											</select>
										</div>

										<div id="shipping_same-day" class="d-none">
											<select name="same-day_timing" class="select2" id="same-day_timing">
												<option value="1">6 Hours</option>
												<option value="2">Same-day</option>
											</select>
										</div>
									</div>

									<div class="col">
										<h4 class="form-section mb-2 text-center">Payment Information</h4>

										<div class="form-group">
											<input type="text" name="amount" class="form-control" placeholder="Amount">
										</div>

										<div class="form-group">
											<select name="payment_mode" class="select2" id="payment_mode">
												@foreach($payment_modes as $payment_mode)
													<option value="{{ $payment_mode->id }}">{{ $payment_mode->mode }}</option>
												@endforeach
											</select>
										</div>

										<div class="form-group">
											<p class="mt-2 border-bottom border-dark text-center font-medium-1 text-bold-600">Estimated Charges</p>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col">
										<div class="form-group text-center">
											<button type="submit" class="btn btn-primary">Book</a>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>

				<div class="modal fade" id="select_service_type" tabindex="-1" role="dialog" aria-labelledby="select_service_type_title" aria-hidden="true">
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
			</div>
		</div>
	</div>
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/toggle/bootstrap-switch.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/switch.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/toggle/bootstrap-checkbox.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jqBootstrapValidation.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			$('#select_service_type').modal({
				backdrop: 'static',
				keyboard: false,
				show: false
			});

			@if (!Session::has('service_type_id'))
				$('#select_service_type').modal('show');
			@else
				var service_type = '{{ Session::get('service_type_id') }}';

				if (service_type == 2) {
					$('#replacement').removeClass('d-none');
				}
			@endif

			$('#select_service_type form #service_type').select2({
				width: '100%',
				placeholder: 'Select Type of Service*'
			}).val(null).trigger('change');

			$('#select_service_type form').bind('submit', function(e) {
				e.preventDefault();

				var selected = $('#select_service_type form #service_type').find(':selected');

				if (selected.val() !== undefined) {
					$('#select_service_type form #service_type-error').addClass('d-none');

					if (selected.val() == 2) {
						$('#replacement').removeClass('d-none');
					}

					$('#booking_form #selected_service_type').val(selected.val());

					$('#selected_service_type_name').html('(' + selected.html() + ')');

					$('#select_service_type').modal('hide');
				}
				else {
					$('#select_service_type form #service_type-error').removeClass('d-none');
				}
			});

			$('#pickup_address').select2({
				width: '100%',
				placeholder: 'Select Pickup Address'
			}).val(null).trigger('change').bind('change', function() {
				if (this.value == 0) {
					$('#new_pickup_address').removeClass('d-none');
				}
				else {
					$('#new_pickup_address').addClass('d-none');
				}
			});

			$('#pickup_city').select2({
				width: '100%',
				placeholder: 'Select City'
			}).val(null).trigger('change');

			$('#information_display').checkboxpicker();

			$('#consignee_city').select2({
				width: '100%',
				placeholder: 'Select City'
			}).val(null).trigger('change');

			$('#product_type').select2({
				width: '100%',
				placeholder: 'Select Product Type'
			}).val(null).trigger('change');

			$('#pickup_date').pickadate({
				firstDay: 1,
				clear: '',
				min: '{{ Carbon\Carbon::now() }}',
				selectYears: true,
				selectMonths: true,
				onOpen: function() {
					$('#pickup_date_root').css('top', '-350px');
				}
			});

			$('#replacement_product_type').select2({
				width: '100%',
				placeholder: 'Select Replacement Product Type'
			}).val(null).trigger('change');

			$('#shipping_mode').select2({
				width: '100%',
				placeholder: 'Select Mode of Shipping'
			}).val(null).trigger('change').bind('change', function() {
				if (this.value == 4) {
					$('#shipping_same-day').removeClass('d-none');
				}
				else {
					$('#shipping_same-day').addClass('d-none');
				}
			});

			$('#same-day_timing').select2({
				width: '100%',
				placeholder: 'Select Same-day Timing'
			}).val(null).trigger('change');

			$('#payment_mode').select2({
				width: '100%',
				placeholder: 'Select Mode of Payment'
			}).val(null).trigger('change');
		});
	</script>
@endsection