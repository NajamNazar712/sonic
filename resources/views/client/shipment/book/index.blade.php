@extends('client.layout.master')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Book a Shipment
					<span id="selected_service_type_name">{{ (Session::has('service_type_name')) ? ('(' . Session::get('service_type_name') . ')') : '' }}</span>
					<button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#select_service_type">Change Service Type</button>
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('client.inc.messages')

							<form id="booking_form" class="form-horizontal" method="POST" action="{{ route('cod.shipment.book.store') }}" novalidate="novalidate">
								{{ csrf_field() }}

								<input type="hidden" name="selected_service_type" id="selected_service_type" value="{{ Session::get('service_type_id') }}">

								<div class="row">
									<div class="col" style="max-width: 20%;">
										<h4 class="form-section mb-2 text-center">Shipper Information</h4>

										<div class="form-group">
											<p class="border-bottom border-light text-center font-medium-1 text-bold-600">{{ $user->name }}</p>
										</div>

										<div class="form-group">
											<p class="border-bottom border-light text-center font-medium-1 text-bold-600">{{ $user->phone }}</p>
										</div>

										<div class="form-group">
											<select name="pickup_address" class="select2" id="pickup_address" data-rule-required="true" data-msg-required="Pickup Address is required">
												<option value="0">New</option>

												@foreach($user->shipping as $shipping_information)
													<option value="{{ $shipping_information['id'] }}" data-city-id="{{ $shipping_information['city']['id'] }}">{{ $shipping_information['poc'] }}: {{ $shipping_information['pickup_address'] }}, {{ $shipping_information['city']['city_name'] }}</option>
												@endforeach
											</select>
										</div>

										<div id="new_pickup_address" class="d-none">
											<div class="form-group">
												<textarea name="new_pickup_address" class="form-control" placeholder="Address*" data-rule-required="true" data-msg-required="Address is required"></textarea>
											</div>

											<div class="form-group">
												<input type="text" name="new_pickup_person_of_contact" class="form-control" placeholder="Person of Contact*" data-rule-required="true" data-msg-required="Person of Contact is required">
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
														<option value="{{ $city->id }}">{{ $city->city_name }}</option>
													@endforeach
												</select>
											</div>
										</div>

										<div class="form-group text-center p-1 border border-light rounded">
											<label class="d-block">Show Information on Air Waybill</label>
											<input type="checkbox" name="information_display" class="switch hidden" id="information_display" checked="checked">
										</div>
									</div>

									<div class="col" style="max-width: 20%;">
										<h4 class="form-section mb-2 text-center">Consignee Information</h4>

										<div class="form-group">
											<select name="consignee_city" class="select2" id="consignee_city" data-rule-required="true" data-msg-required="City is required">
												@foreach($cities as $city)
													<option value="{{ $city->id }}">{{ $city->city_name }}</option>
												@endforeach
											</select>
										</div>

										<div class="form-group">
											<input type="text" name="consignee_name" class="form-control" placeholder="Name*" data-rule-required="true" data-msg-required="Name is required">
										</div>

										<div class="form-group">
											<textarea name="consignee_address" class="form-control" placeholder="Address*" data-rule-required="true" data-msg-required="Address is required"></textarea>
										</div>

										<div class="form-group">
											<input type="text" name="consignee_phone_number_1" class="form-control phone_number" placeholder="Phone Number 1*" data-rule-required="true" data-msg-required="Phone Number is required">
										</div>

										<div class="form-group">
											<input type="text" name="consignee_phone_number_2" class="form-control phone_number" placeholder="Phone Number 2">
										</div>

										<div class="form-group">
											<input type="email" name="consignee_email_address" class="form-control" placeholder="Email Address">
										</div>
									</div>

									<div class="col" style="max-width: 20%;">
										<h4 class="form-section mb-2 text-center">Order Information</h4>

										<div class="form-group">
											<input name="order_id" class="form-control" placeholder="Order ID" data-rule-remote="{{ route('cod.shipment.book.order_id') }}" data-msg-remote="Order ID must be unique">
										</div>

										<div id="regular">
											<div class="form-group">
												<select name="product_type" class="select2" id="product_type" data-rule-required="true" data-msg-required="Product Type is required">
													@foreach($products as $product)
														<option value="{{ $product->id }}">{{ $product->product_name }}</option>
													@endforeach
												</select>
											</div>

											<div class="form-group">
												<textarea name="item_description" class="form-control" placeholder="Item Description"></textarea>
											</div>

											<div class="form-group input-group">
												<input type="text" name="item_quantity" class="form-control text-center quantity" placeholder="Item Quantity*" data-rule-required="true" data-msg-required="Item Quantity is required">
											</div>

											<div class="form-group input-group d-none">
												<div class="input-group-prepend">
													<span class="input-group-text">Rs</span>
												</div>

												<input type="text" name="item_price" class="form-control rounded-right price" placeholder="Price*" data-rule-required="true" data-msg-required="Price is required">
											</div>

											<div class="form-group text-center p-1 border border-light rounded">
												<label class="d-block">Insurance</label>
												<input type="checkbox" name="insurance" class="switch hidden insurance">
											</div>
										</div>

										<div id="replacement" class="mb-1 d-none">
											<h4 class="text-center m-0 p-1 bg-dark white border border-dark rounded-top">Replacement</h4>

											<div class="pt-1 pl-1 pr-1 border border-light rounded-bottom">
												<div class="form-group">
													<select name="replacement_product_type" class="select2" id="replacement_product_type" data-rule-required="true" data-msg-required="Product Type is required">
														@foreach($products as $product)
															<option value="{{ $product->id }}">{{ $product->product_name }}</option>
														@endforeach
													</select>
												</div>

												<div class="form-group">
													<textarea name="replacement_item_description" class="form-control" placeholder="Item Description"></textarea>
												</div>

												<div class="form-group input-group">
													<input type="text" name="replacement_item_quantity" class="form-control text-center quantity" placeholder="Item Quantity*" data-rule-required="true" data-msg-required="Replacement Item Quantity is required">
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
																		<option value="{{ $product->id }}">{{ $product->product_name }}</option>
																	@endforeach
																</select>
															</div>

															<div class="form-group">
																<textarea name="item_description" class="form-control" placeholder="Item Description"></textarea>
															</div>

															<div class="form-group input-group">
																<input type="text" name="item_quantity" class="form-control text-center quantity" placeholder="Item Quantity*" data-rule-required="true" data-msg-required="Item Quanity is required">
															</div>

															<div class="form-group input-group">
																<div class="input-group-prepend">
																	<span class="input-group-text">Rs</span>
																</div>

																<input type="text" name="item_price" class="form-control rounded-right price" placeholder="Price*" data-rule-required="true" data-msg-required="Price is required">
															</div>

															<div class="form-group text-center p-1 border border-light rounded">
																<label class="d-block">Insurance</label>
																<input type="checkbox" name="insurance" class="switch hidden insurance">
															</div>
														</div>
													</div>
												</div>

												<div class="form-group text-right">
													<button data-repeater-create type="button" class="btn btn-block btn-primary">Add</button>
												</div>
											</div>

											<div class="form-group">
												<p class="border-bottom border-light text-center font-medium-1 text-bold-600" id="total_quantity">Total Quantity: <span>0</span></p>
											</div>

											<div class="form-group">
												<p class="border-bottom border-light text-center font-medium-1 text-bold-600" id="total_price">Total Price: Rs <span>0</span></p>
											</div>

											<div class="form-group">
												<div class="form-group text-center p-1 border border-light rounded">
													<label class="d-block">Type of Package</label>
													<input type="checkbox" name="package_type" class="switch hidden package_type" id="package_type" checked="checked" data-off-label="Partial" data-on-label="Complete">
												</div>
											</div>
										</div>

										<div class="form-group input-group">
											<div class="input-group-prepend">
												<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
													<span class="la la-calendar-o"></span>
												</span>
											</div>

											<input type="text" name="pickup_date" class="form-control pickadate bg-primary border-primary white rounded-right" id="pickup_date" placeholder="Pickup Date*" data-rule-required="true" data-msg-required="Pickup Date is required">
										</div>

										<div class="form-group">
											<textarea name="special_instructions" class="form-control" placeholder="Special Instructions"></textarea>
										</div>
									</div>

									<div class="col" style="max-width: 20%;">
										<h4 class="form-section mb-2 text-center">Shipping Information</h4>

										<div class="form-group input-group">
											<input type="text" name="estimated_weight" class="form-control weight" placeholder="Estimated Weight*" data-rule-required="true" data-msg-required="Estimated Weight is required">

											<div class="input-group-append">
												<span class="input-group-text">kg</span>
											</div>
										</div>

										<div class="form-group">
											<select name="shipping_mode" class="select2" id="shipping_mode" data-rule-required="true" data-msg-required="Mode of Shipping is required">
												@foreach($shipping_modes as $shipping_mode)
													<option value="{{ $shipping_mode->id }}">{{ $shipping_mode->mode }}</option>
												@endforeach
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
									</div>

									<div class="col" style="max-width: 20%;">
										<h4 class="form-section mb-2 text-center">Payment Information</h4>

										<div class="form-group input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">Rs</span>
											</div>

											<input type="text" name="amount" class="form-control rounded-right amount" placeholder="Amount*" data-rule-required="true" data-msg-required="Amount is required">
										</div>

										<div class="form-group">
											<select name="payment_mode" class="select2" id="payment_mode" data-rule-required="true" data-msg-required="Mode of Payment is required">
												@foreach($payment_modes as $payment_mode)
													<option value="{{ $payment_mode->id }}">{{ $payment_mode->mode }}</option>
												@endforeach
											</select>
										</div>

										<div class="form-group">
											<p class="border-bottom border-light text-center font-medium-1 text-bold-600">Estimated Charges</p>
										</div>
									</div>
								</div>

								<div class="row mt-2">
									<div class="col">
										<div class="form-group text-center">
											<button type="submit" name="book" class="btn btn-primary" value="Book">Book</button>
											<button type="submit" name="book_and_print" class="btn btn-primary ml-1" value="Book & Print">Book &amp; Print</button>
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
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
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

	<script>
		$(document).ready(function() {
			@if (session('print'))
				$.ajax({
					url: '{!! route('cod.shipment.book.print_air_waybill') !!}',
					method: 'POST',
					data: {
						'ids[]': '{{ session('print') }}',
						'_token': '{{ csrf_token() }}'
					}
				})
				.done(function(data) {
					var tab = window.open('', '_blank');

					if(!tab || tab.outerHeight === 0) {
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

				if (service_type == 2) {
					$('#replacement').removeClass('d-none');
				}
				if (service_type == 3) {
					$('#regular').addClass('d-none');
					$('#try_and_buy').removeClass('d-none');
				}

				$('#select_service_type form #service_type').val(service_type).trigger('change');
			@endif

			$('#select_service_type form').bind('submit', function(e) {
				e.preventDefault();

				var selected = $('#select_service_type form #service_type').find(':selected');

				service_type = selected.val();

				if (service_type !== '' && service_type !== undefined && service_type !== null) {
					$('#select_service_type form #service_type-error').addClass('d-none');

					if (service_type == 1) {
						$('#regular').removeClass('d-none');
						$('#replacement').addClass('d-none');
						$('#try_and_buy').addClass('d-none');
					}
					else if (service_type == 2) {
						$('#regular').removeClass('d-none');
						$('#replacement').removeClass('d-none');
						$('#try_and_buy').addClass('d-none');
					}
					else if (service_type == 3) {
						$('#regular').addClass('d-none');
						$('#replacement').addClass('d-none');
						$('#try_and_buy').removeClass('d-none');
					}

					$('#booking_form #selected_service_type').val(service_type);

					$('#selected_service_type_name').html('(' + selected.html() + ')');

					$('#select_service_type').modal('hide');
				}
				else {
					$('#select_service_type form #service_type-error').removeClass('d-none');
				}
			});

			$('#pickup_address').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Pickup Address*'
			}).bind('change', function() {
				$(this).valid();

				if (this.value == 0) {
					$('#new_pickup_address').removeClass('d-none');
				}
				else {
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

				var pickup_city = $(this).val();
				var consignee_city = $('#consignee_city').val();

				shipping_mode_same_day(pickup_city, consignee_city);
			});

			$('#information_display').checkboxpicker();

			$('#consignee_city').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'City*'
			}).bind('change', function() {
				$(this).valid();

				if ($('#pickup_address').val() == 0) {
					var pickup_city = $('#new_pickup_city').val();
				}
				else {
					var pickup_city = $('#pickup_address').find(':selected').data('city-id');
				}

				var consignee_city = $(this).val();

				shipping_mode_same_day(pickup_city, consignee_city);
			});

			$('#product_type').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Product Type*'
			}).bind('change', function() {
				$(this).valid();
			});

			$('#regular .insurance').checkboxpicker().bind('change', function() {
				var parent = $(this).parent('.form-group').prev('.form-group');

				if (this.checked) {
					parent.removeClass('d-none');
				}
				else {
					parent.addClass('d-none');

					parent.children('#item_price-error').remove();
				}
			});

			$('#try_and_buy .insurance').checkboxpicker();

			$('#package_type').checkboxpicker();

			$('#pickup_date').pickadate({
				firstDay: 1,
				clear: '',
				min: '{{ Carbon\Carbon::now() }}',
				selectYears: true,
				selectMonths: true,
				formatSubmit: 'yyyy-mm-dd 00:00:00',
				hiddenSuffix: '_formatted',
				onOpen: function() {
					$('#pickup_date_root').css('top', '-350px');
				},
				onSet: function(context) {
					$('#pickup_date').valid();
				}
			});

			$('#replacement_product_type').prepend('<option value="" selected="selected"></option>').select2({
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

			$('#try_and_buy .repeater').repeater({
				isFirstItemUndeletable: true,
				show: function() {
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
						max: 1000,
						buttondown_class: 'btn btn-primary rounded-left',
						buttonup_class: 'btn btn-primary rounded-right',
						buttondown_txt: '<i class="ft-minus"></i>',
						buttonup_txt: '<i class="ft-plus"></i>'
					}).bind('input change', function() {
						if ($(this).hasClass('danger')) {
							$(this).valid();
						}

						if (service_type == 3) {
							try_and_buy_total_quantity();
						}
					});

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
								text: 'Cancel',
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
							$(this).slideUp(delete_element);

							try_and_buy_product_numbering();
						}
					});
				}
			});

			$('#shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
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

			$('#same-day_timing').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Same-day Timing*'
			}).bind('change', function() {
				$(this).valid();
			});

			$('#payment_mode').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Mode of Payment*'
			}).bind('change', function() {
				$(this).valid();
			});

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
					$(form).find('button[type=submit]').attr('disabled', 'disabled');

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
			});

			$('.phone_number').inputmask({
				'mask': '9999-9999999',
				'clearIncomplete': true
			});

			$('.quantity').TouchSpin({
				min: 1,
				max: 1000,
				buttondown_class: 'btn btn-primary rounded-left',
				buttonup_class: 'btn btn-primary rounded-right',
				buttondown_txt: '<i class="ft-minus"></i>',
				buttonup_txt: '<i class="ft-plus"></i>'
			}).bind('input change', function() {
				if ($(this).hasClass('danger')) {
					$(this).valid();
				}

				if (service_type == 3) {
					try_and_buy_total_quantity();
				}
			});

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
				'max': 1000
			});

			$('.amount').inputmask({
				'alias': 'integer',
				'allowMinus': false,
				'allowPlus': false,
				'groupSeparator': ',',
				'autoGroup': true,
				'max': 1000000
			});
		});
	</script>
@endsection