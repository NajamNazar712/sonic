@extends('client.layout.master')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Book a Shipment
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('client.inc.messages')

							<form id="booking_form" class="form-horizontal" method="POST" action="{{ route('cod.shipment.book.excel_store') }}" novalidate="novalidate" enctype="multipart/form-data">
								{{ csrf_field() }}

								<div class="row align-items-center justify-content-center">
									<div class="col">
										<div class="form-group">
											<input type="file" name="shipments" class="w-100 p-1 border-primary" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed">
										</div>
									</div>

									<div class="col">
										<div class="form-group text-left">
											<button type="submit" name="upload" class="btn btn-primary">Upload</button>
										</div>
									</div>

									<div class="col ml-auto">
										<div class="form-group text-right">
											<a href="{{ asset('file/Trax Book Shipment Template.xlsx') }}" class="btn btn-primary"><i class="la la-download"></i> Download Template</a>
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
												<th colspan="2" class="border-primary border-darken-1">Show Informaiton on Air Waybill</th>
											</tr>
										</thead>
										<tbody>
											<tr role="row">
												<td class="text-center">Hide</td>
												<td class="text-center">Show</td>
											</tr>
										</tbody>
									</table>

									<table class="table table-bordered">
										<thead>
											<tr role="row" class="bg-primary white text-center">
												<th colspan="2" class="border-primary border-darken-1">Insurance</th>
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
											@foreach ($pickup_addresses as $pickup_address)
												<tr role="row">
													<td class="text-center">{{ $pickup_address->id }}</td>
													<td>{{ $pickup_address->pickup_address }}, {{ $pickup_address->city->name }}</td>
												</tr>
											@endforeach
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
												<th class="text-center border-primary border-lighten-2">ID</th>
												<th class="border-primary border-lighten-2">Name</th>
											</tr>
										</thead>
										<tbody>
											@foreach ($cities as $city)
												<tr role="row">
													<td class="text-center">{{ $city->id }}</td>
													<td>{{ $city->name }}</td>
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