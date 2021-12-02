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
							@include('client.inc.messages')

							<form id="booking_form" class="form-horizontal" method="POST" action="{{ route('cod.shipment.book.excel_store') }}" novalidate="novalidate" enctype="multipart/form-data">
								{{ csrf_field() }}
								<div class="row align-items-center justify-content-center mb-2">
									<div class="col">
										<div class="form-group">
											<input type="file" name="shipments" class="w-100 p-1 border-primary" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
										</div>
									</div>

									<div class="col">
										<div class="row p-1 border-primary">
											<div class="col-12">
												<h5 class="form-section text-center">Template Type</h5>
											</div>
											<div class="col-12">
												<div class="row justify-content-center">
													<fieldset>
														<div class="custom-control custom-radio col">
															<input type="radio" class="custom-control-input iad_radio" name="excel_type" id="excel_type_1" value="1">
															<label class="custom-control-label" for="excel_type_1">Overall</label>
														</div>
													</fieldset>
													<fieldset>
														<div class="custom-control custom-radio col">
															<input type="radio" class="custom-control-input iad_radio" name="excel_type" id="excel_type_2" value="2" checked>
															<label class="custom-control-label" for="excel_type_2">Regular</label>
														</div>
													</fieldset>
													<fieldset>
														<div class="custom-control custom-radio col">
															<input type="radio" class="custom-control-input iad_radio" name="excel_type" id="excel_type_3" value="3">
															<label class="custom-control-label" for="excel_type_3">Replacement</label>
														</div>
													</fieldset>
													<fieldset>
														<div class="custom-control custom-radio col">
															<input type="radio" class="custom-control-input iad_radio" name="excel_type" id="excel_type_4" value="4">
															<label class="custom-control-label" for="excel_type_4">Try And Buy</label>
														</div>
													</fieldset>
													<fieldset>
														<div class="custom-control custom-radio col">
															<input type="radio" class="custom-control-input iad_radio" name="excel_type" id="excel_type_5" value="5">
															<label class="custom-control-label" for="excel_type_5">Reverse Pickup</label>
														</div>
													</fieldset>
													<fieldset>
														<div class="custom-control custom-radio col">
															<input type="radio" class="custom-control-input iad_radio" name="excel_type" id="excel_type_6" value="6">
															<label class="custom-control-label" for="excel_type_6">Omni</label>
														</div>
													</fieldset>
												</div>
											</div>
										</div>
									</div>

									<div class="col-auto">
										<div class="form-group text-left">
											<button type="submit" name="upload" class="btn btn-primary">Upload</button>
										</div>
									</div>

									<div class="col-12">
										<h5 class="form-section mt-2 mb-2 text-center">Template Download</h5>

										<div class="row">
											<div class="col">
												<div class="form-group text-right">
													<a href="{{ asset('file/Trax Book Regular Shipment Template.xlsx') }}?v=09_06_2021" class="btn btn-primary btn-block"><i class="la la-download"></i> Regular</a>
												</div>
											</div>
											<div class="col">
												<div class="form-group text-right">
												<a href="{{ asset('file/Trax Book Replacement Shipment Template.xlsx') }}?v=09_06_2021" class="btn btn-primary btn-block"><i class="la la-download"></i> Replacement</a>
												</div>
											</div>
											<div class="col">
												<div class="form-group text-right">
													<a href="{{ asset('file/Trax Book Try And Buy Shipment Template.xlsx') }}?v=09_06_2021" class="btn btn-primary btn-block"><i class="la la-download"></i> Try And Buy</a>
												</div>
											</div>
											<div class="col">
												<div class="form-group text-right">
													<a href="{{ asset('file/Trax Book Reverse Pickup Shipment Template.xlsx') }}?v=09_06_2021" class="btn btn-primary btn-block"><i class="la la-download"></i> Reverse Pickup</a>
												</div>
											</div>
											<div class="col">
												<div class="form-group text-right">
													<a href="{{ asset('file/Trax Book Shipment Template.xlsx') }}?v=09_06_2021" class="btn btn-primary btn-block"><i class="la la-download"></i> Overall</a>
												</div>
											</div>
											<div class="col">
												<div class="form-group text-right">
													<a href="{{ asset('file/Trax Omni Shipment Template.xlsx') }}?v=09_06_2021" class="btn btn-primary btn-block"><i class="la la-download"></i> Omni</a>
												</div>
											</div>
										</div>
									</div>
								</div>
							</form>

							<div class="row">
								<div class="col">
									<table class="table table-bordered">
										<thead>
										<tr role="row" class="bg-primary white text-center">
											<th colspan="2" class="border-primary border-darken-1">Service Types</th>
										</tr>
										<tr role="row" class="bg-primary bg-lighten-1 white">
											<th class="text-center border-primary border-lighten-2">ID</th>
											<th class="border-primary border-lighten-2">Name</th>
										</tr>
										</thead>
										<tbody>
										@foreach ($booking_types as $booking_type)
											<tr role="row">
												<td class="text-center">{{ $booking_type->id }}</td>
												<td>{{ $booking_type->booking_type }}</td>
											</tr>
										@endforeach
										</tbody>
									</table>

									<table class="table table-bordered">
										<thead>
										<tr role="row" class="bg-primary white text-center">
											<th colspan="2" class="border-primary border-darken-1">Charges Modes</th>
										</tr>
										<tr role="row" class="bg-primary bg-lighten-1 white">
											<th class="text-center border-primary border-lighten-2">ID</th>
											<th class="border-primary border-lighten-2">Name</th>
										</tr>
										</thead>
										<tbody>
										@foreach ($charges_modes as $charges_mode)
											<tr role="row">
												<td class="text-center">{{ $charges_mode->id }}</td>
												<td>{{ $charges_mode->charges_mode }}</td>
											</tr>
										@endforeach
										</tbody>
									</table>

									<table class="table table-bordered">
										<thead>
										<tr role="row" class="bg-primary white text-center">
											<th colspan="2" class="border-primary border-darken-1">Show Information on Air Waybill</th>
										</tr>
										</thead>
										<tbody>
										<tr role="row">
											<td class="text-center">No</td>
											<td class="text-center">Yes</td>
										</tr>
										</tbody>
									</table>
									<table class="table table-bordered">
										<thead>
										<tr role="row" class="bg-primary white text-center">
											<th colspan="2" class="border-primary border-darken-1">Self Collection</th>
										</tr>
										</thead>
										<tbody>
										<tr role="row">
											<td class="text-center">No</td>
											<td class="text-center">Yes</td>
										</tr>
										</tbody>
									</table>

									<table class="table table-bordered">
										<thead>
										<tr role="row" class="bg-primary white text-center">
											<th colspan="2" class="border-primary border-darken-1">Item Insurance</th>
										</tr>
										</thead>
										<tbody>
										<tr role="row">
											<td class="text-center">No</td>
											<td class="text-center">Yes</td>
										</tr>
										</tbody>
									</table>

									<table class="table table-bordered">
										<thead>
										<tr role="row" class="bg-primary white text-center">
											<th colspan="2" class="border-primary border-darken-1">Shipping Modes</th>
										</tr>
										<tr role="row" class="bg-primary bg-lighten-1 white">
											<th class="text-center border-primary border-lighten-2">ID</th>
											<th class="border-primary border-lighten-2">Name</th>
										</tr>
										</thead>
										<tbody>
										@foreach ($shipping_modes as $shipping_mode)
											<tr role="row">
												<td class="text-center">{{ $shipping_mode->id }}</td>
												<td>{{ $shipping_mode->mode }}</td>
											</tr>
										@endforeach
										</tbody>
									</table>

									@if ($shipping_mode_same_day_timings)
										<table class="table table-bordered">
											<thead>
											<tr role="row" class="bg-primary white text-center">
												<th colspan="2" class="border-primary border-darken-1">Same Day Timings</th>
											</tr>
											<tr role="row" class="bg-primary bg-lighten-1 white">
												<th class="text-center border-primary border-lighten-2">ID</th>
												<th class="border-primary border-lighten-2">Name</th>
											</tr>
											</thead>
											<tbody>
											@foreach ($shipping_mode_same_day_timings as $shipping_mode_same_day_timing)
												<tr role="row">
													<td class="text-center">{{ $shipping_mode_same_day_timing->id }}</td>
													<td>{{ $shipping_mode_same_day_timing->timing }}</td>
												</tr>
											@endforeach
											</tbody>
										</table>
									@endif

									<table class="table table-bordered">
										<thead>
										<tr role="row" class="bg-primary white text-center">
											<th colspan="2" class="border-primary border-darken-1">Payment Modes</th>
										</tr>
										<tr role="row" class="bg-primary bg-lighten-1 white">
											<th class="text-center border-primary border-lighten-2">ID</th>
											<th class="border-primary border-lighten-2">Name</th>
										</tr>
										</thead>
										<tbody>
										@foreach ($payment_modes as $payment_mode)
											<tr role="row">
												<td class="text-center">{{ $payment_mode->id }}</td>
												<td>{{ $payment_mode->mode }}</td>
											</tr>
										@endforeach
										</tbody>
									</table>

									<table class="table table-bordered">
										<thead>
										<tr role="row" class="bg-primary white text-center">
											<th colspan="2" class="border-primary border-darken-1">Open Shipment</th>
										</tr>
										</thead>
										<tbody>
										<tr role="row">
											<td class="text-center">No</td>
											<td class="text-center">Yes</td>
										</tr>
										</tbody>
									</table>
								</div>

								<div class="col">
									<table class="table table-bordered">
										<thead>
										<tr role="row" class="bg-primary white text-center">
											<th colspan="2" class="border-primary border-darken-1">Pickup Addresses</th>
										</tr>
										<tr role="row" class="bg-primary bg-lighten-1 white">
											<th class="text-center border-primary border-lighten-2">ID</th>
											<th class="border-primary border-lighten-2">Address</th>
										</tr>
										</thead>
										<tbody>
										@if ($pickup_addresses->count())
											@foreach ($pickup_addresses as $pickup_address)
												<tr role="row">
													<td class="text-center">{{ $pickup_address->id }}</td>
													<td>{{ $pickup_address->pickup_address }}, {{ $pickup_address->city->name }}</td>
												</tr>
											@endforeach
										@else
											<tr role="row">
												<td colspan="2" class="text-center">No Active Pickup Addresses</td>
											</tr>
										@endif
										</tbody>

									</table>



									<table class="table table-bordered">
										<thead>
										<tr role="row" class="bg-primary white text-center">
											<th colspan="2" class="border-primary border-darken-1">Character Limits</th>
										</tr>
										<tr role="row" class="bg-primary bg-lighten-1 white">
											<th class="border-primary border-lighten-2">Field</th>
											<th class="text-center border-primary border-lighten-2">Limit</th>
										</tr>
										</thead>
										<tbody>
										<tr role="row">
											<td>Consignee City Name</td>
											<td class="text-center">190</td>
										</tr>
										<tr role="row">
											<td>Consignee Name</td>
											<td class="text-center">100</td>
										</tr>
										<tr role="row">
											<td>Consignee Address</td>
											<td class="text-center">190</td>
										</tr>
										<tr role="row">
											<td>Consignee Email Address</td>
											<td class="text-center">100</td>
										</tr>
										<tr role="row">
											<td>Order ID</td>
											<td class="text-center">100</td>
										</tr>
										<tr role="row">
											<td>Item Description</td>
											<td class="text-center">1000</td>
										</tr>
										<tr role="row">
											<td>Item Quantity</td>
											<td class="text-center">1000</td>
										</tr>
										<tr role="row">
											<td>Product Value</td>
											<td class="text-center">100000</td>
										</tr>
										<tr role="row">
											<td>Replacement Item Description</td>
											<td class="text-center">190</td>
										</tr>
										<tr role="row">
											<td>Replacement Item Quantity</td>
											<td class="text-center">1000</td>
										</tr>
										<tr role="row">
											<td>Special Instructions</td>
											<td class="text-center">190</td>
										</tr>
										<tr role="row">
											<td>Estimated Weight (kg)</td>
											<td class="text-center">100000</td>
										</tr>
										<tr role="row">
											<td>Collection Amount</td>
											<td class="text-center">1000000</td>
										</tr>
										</tbody>
									</table>


								</div>

								<div class="col">
									<table class="table table-bordered">
										<thead>
										<tr role="row" class="bg-primary white text-center">
											<th colspan="2" class="border-primary border-darken-1">Product Types</th>
										</tr>
										<tr role="row" class="bg-primary bg-lighten-1 white">
											<th class="text-center border-primary border-lighten-2">ID</th>
											<th class="border-primary border-lighten-2">Name</th>
										</tr>
										</thead>
										<tbody>
										@foreach ($products as $product)
											<tr role="row">
												<td class="text-center">{{ $product->id }}</td>
												<td>{{ $product->product_name }}</td>
											</tr>
										@endforeach
										</tbody>
									</table>
								</div>

								<div class="col">
									<table class="table table-bordered">
										<thead>
										<tr role="row" class="bg-primary white text-center">
											<th colspan="2" class="border-primary border-darken-1">Cities</th>
										</tr>
										<tr role="row" class="bg-primary bg-lighten-1 white">
											<th class="border-primary border-lighten-2">Name</th>
										</tr>
										</thead>
										<tbody>
										@foreach ($cities as $city)
											<tr role="row">
												<td>{{ $city }}</td>
											</tr>
										@endforeach
										</tbody>
									</table>
								</div>
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

	<script>
		$(document).ready(function() {
			$.validator.addMethod('maxsize', function(value, element, params) {
				if ($(element).attr('type') === 'file') {
					if (element.files && element.files.length) {
						console.log(element.files);
						for (var c = 0; c < element.files.length; c++) {
							if (element.files[c].size > params) {
								return false;
							}
						}
					}
				}

				return true;
			}, $.validator.format("File Size must not exceed {0} bytes."));

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
						text: 'Your shipment(s) are being booked!',
						icon: 'info',
						buttons: false,
						closeOnClickOutside: false,
						closeOnEsc: false
					});

					form.submit();
				}
			});
		});
	</script>
@endsection