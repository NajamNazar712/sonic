@extends('admin.layout.master')

@section('title', 'Tracking')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Tracking
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<form id="track_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
								<div class="form-group">
									<input type="text" name="tracking_numbers" class="tracking_numbers" placeholder="Tracking Number(s)*" data-tags-input-name="tracking_number" data-rule-required="true" data-msg-required="Tracking Number is required">
								</div>

								<div class="form-group ml-1">
									<button type="submit" name="track" class="btn btn-primary" value="Track">Track</button>
								</div>
							</form>

							<div class="tracking" id="tracking">
							</div>

							<div class="modal fade" id="rider_information" role="dialog" aria-labelledby="rider_information_title" aria-hidden="true">
								<div class="modal-dialog modal-lg" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h4 class="modal-title" id="rider_information_title">Rider Information</h4>

											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">×</span>
											</button>
										</div>
										<div class="modal-body">
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
										</div>
									</div>
								</div>
							</div>

							<div class="modal fade" id="cargo_consignment_details" role="dialog" aria-labelledby="cargo_consignment_details_title" aria-hidden="true">
								<div class="modal-dialog modal-lg" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h4 class="modal-title" id="cargo_consignment_details_title">Cargo Consignment Details</h4>

											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">×</span>
											</button>
										</div>
										<div class="modal-body">
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
										</div>
									</div>
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
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

	<style>
		.selectize-control {
			width: 300px !important;
		}
	</style>
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			function print(id) {
				$.ajax({
					url: '{!! route('cod.shipment.book.print_air_waybill') !!}',
					method: 'POST',
					data: {
						'ids[]': id,
						'admin': true,
						'_token': '{{ csrf_token() }}'
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
			}

			function track(tracking_numbers) {
                $.ajax({
                    url: '{!! route('admin.tracking.track') !!}',
                    method: 'POST',
                    data: {
                        'tracking_numbers': tracking_numbers,
                        '_token': '{{ csrf_token() }}'
                    }
                })
				.done(function(data) {
					select[0].selectize.clear();

					$('#tracking').html('');

					if (data.invalid !== undefined) {
						var message = 'Invalid Tracking Number(s): ' + data.invalid.join(', ');

						toastr.error(message, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
					}


					if (data.shipments != undefined) {
						$.each(data.shipments, function(id, details) {
							var shipment = '';

							shipment += '<div class="mt-4 border-primary">';
							shipment += '<div class="d-flex align-items-center bg-primary">';
							shipment += '<div class="mb-0 ml-1 font-medium-3 white">' + details.tracking_number + '</div>';
							shipment += '<button class="btn btn-secondary ml-auto print" id=' + id + '>Print</button>';
							shipment += '</div>';

							shipment += '<div class="p-1">';
							shipment += '<div class="row justify-content-between">';

							shipment += '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-5">';
							shipment += '<h4><u>Shipper Information</u></h4>';
							shipment += '<div class="border">';
							shipment += '<table class="table table-sm table-borderless mb-0">';
							shipment += '<tbody>';
							shipment += '<tr>';
							shipment += '<td><strong>Shipper</strong></td>';
							shipment += '<td>' + details.shipper.name + '</td>';
							shipment += '<td><strong>Account No.</strong></td>';
							shipment += '<td>' + details.shipper.account_number + '</td>';
							shipment += '</tr>';
							shipment += '<tr>';
							shipment += '<td><strong>Phone No(s).</strong></td>';

							if (!details.shipper.phone_number_2) {
								shipment += '<td>' + details.shipper.phone_number_1 + '</td>';
							}
							else {
								shipment += '<td>' + details.shipper.phone_number_1 + '<br/>' + details.shipper.phone_number_2 + '</td>';
							}

							shipment += '<td><strong>Origin</strong></td>';
							shipment += '<td>' + details.shipper.origin + '</td>';
							shipment += '</tr>';
							shipment += '<tr>';
							shipment += '<td><strong>Address</strong></td>';
							shipment += '<td colspan="3">' + details.shipper.address + '</td>';
							shipment += '</tr>';
							shipment += '</tbody>';
							shipment += '</table>';
							shipment += '</div>';
							shipment += '</div>';

							shipment += '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-5 mt-xs-2 mt-sm-2 mt-md-2 mt-lg-0">';
							shipment += '<h4><u>Consignee Information</u></h4>';
							shipment += '<div class="border">';
							shipment += '<table class="table table-sm table-borderless mb-0">';
							shipment += '<tbody>';
							shipment += '<tr>';
							shipment += '<td><strong>Consignee</strong></td>';
							shipment += '<td>' + details.consignee.name + '</td>';
							shipment += '<td><strong>Destination</strong></td>';
							shipment += '<td>' + details.consignee.destination + '</td>';
							shipment += '</tr>';
							shipment += '<tr>';
							shipment += '<td><strong>Phone No(s).</strong></td>';

							if (!details.consignee.phone_number_2) {
								shipment += '<td>' + details.consignee.phone_number_1 + '</td>';
							}
							else {
								shipment += '<td>' + details.consignee.phone_number_1 + '<br/>' + details.consignee.phone_number_2 + '</td>';
							}

							shipment += '<td colspan="2"></td>';
							shipment += '</tr>';
							shipment += '<tr>';
							shipment += '<td><strong>Address</strong></td>';
							shipment += '<td colspan="3">' + details.consignee.address + '</td>';
							shipment += '</tr>';
							shipment += '</tbody>';
							shipment += '</table>';
							shipment += '</div>';
							shipment += '</div>';

							shipment += '<div class="col-12 mt-2">';
							shipment += '<h4><u>Order Information</u></h4>';
							shipment += '<div class="border">';
							shipment += '<table class="table table-sm table-borderless mb-0">';
							shipment += '<tbody>';

							$.each(details.order_information.items, function(index, item) {
								shipment += '<tr>';
								shipment += '<td><strong>Product Type</strong></td>';
								shipment += '<td>' + item.product_type + '</td>';
								shipment += '<td><strong>Description</strong></td>';
								shipment += '<td>' + ((item.description) ? item.description : '-') + '</td>';
								shipment += '<td><strong>Quantity</strong></td>';
								shipment += '<td>' + item.quantity + '</td>';
								shipment += '</tr>';
							});

							shipment += '<tr>';
							shipment += '<td><strong>Weight</strong></td>';
							shipment += '<td>' + details.order_information.weight + ' kg</td>';
							shipment += '<td><strong>Shipping Mode</strong></td>';
							shipment += '<td>' + details.order_information.shipping_mode + '</td>';
							shipment += '<td><strong>Collection Amount</strong></td>';
							shipment += '<td>Rs. ' + details.order_information.amount + '</td>';
							shipment += '</tr>';

							shipment += '<tr>';
                            shipment += '<td><strong>Order ID</strong></td>';
                            shipment += '<td>' + ((details.order_information.order_id) ? details.order_information.order_id : '-') + '</td>';
                            shipment += '<td><strong>Instructions</strong></td>';

                            if (details.order_information.booking_type_id != 4) {
                            	shipment += '<td colspan="3">' + ((details.order_information.instructions) ? details.order_information.instructions : '-') + '</td>';
                        	}
                        	else {
                        		shipment += '<td>' + ((details.order_information.instructions) ? details.order_information.instructions : '-') + '</td>';
                        		shipment += '<td><strong>Charges Mode</strong></td>';
                        		shipment += '<td>' + details.order_information.charges_mode + '</td>';
                        	}

                            shipment += '</tr>';

							shipment += '</tbody>';
							shipment += '</table>';
							shipment += '</div>';
							shipment += '</div>';

							shipment += '<div class="col-12 mt-2">';
							shipment += '<h4><u>Tracking History</u></h4>';
							shipment += '<div class="border">';

							shipment += '<table class="table table-sm table-borderless datatable tracking_history">';
							shipment += '<thead>';
							shipment += '<tr role="row">';
							shipment += '<th><strong>Date / Time</strong></th>';
							shipment += '<th><strong>Status</strong></th>';
							shipment += '<th><strong>Reason</strong></th>';
							shipment += '<th><strong>Remarks</strong></th>';
							shipment += '<th><strong>User</strong></th>';
							shipment += '<th><strong>City</strong></th>';
							shipment += '</tr>';
							shipment += '</thead>';
							shipment += '<tbody>';

							$.each(details.tracking_history, function(index, history) {
								shipment += '<tr>';
								shipment += '<td>' + history.date_time + '</td>';
								shipment += '<td>' + history.status + '</td>';
								shipment += '<td>' + ((history.status_reason) ? history.status_reason : '') + '</td>';
								shipment += '<td>' + history.remarks + '</td>';
								shipment += '<td>' + history.user + '</td>';
								shipment += '<td>' + history.city + '</td>';
								shipment += '</tr>';
							});

							shipment += '</tbody>';
							shipment += '</table>';

							shipment += '</div>';
							shipment += '</div>';

							if ('payment_history' in details) {
								shipment += '<div class="col-12 mt-2">';
								shipment += '<h4><u>Payment History</u></h4>';
								shipment += '<div class="border">';

								shipment += '<table class="table table-sm table-borderless datatable payment_history">';
								shipment += '<thead>';
								shipment += '<tr role="row">';
								shipment += '<th><strong>Date / Time</strong></th>';
								shipment += '<th><strong>Status</strong></th>';
								shipment += '<th><strong>User</strong></th>';
								shipment += '<th><strong>Remarks</strong></th>';
								shipment += '</tr>';
								shipment += '</thead>';
								shipment += '<tbody>';

								$.each(details.payment_history, function(index, history) {
									shipment += '<tr>';
									shipment += '<td>' + history.date_time + '</td>';
									shipment += '<td>' + history.status + '</td>';
									shipment += '<td>' + history.user + '</td>';
									shipment += '<td>' + history.payable_remarks + '</td>';
									shipment += '</tr>';
								});

								shipment += '</tbody>';
								shipment += '</table>';

								shipment += '</div>';
								shipment += '</div>';
							}

							if ('pickup_history' in details) {
								shipment += '<div class="col-12 mt-2">';
								shipment += '<h4><u>Pickup History</u></h4>';
								shipment += '<div class="border">';

								shipment += '<table class="table table-sm table-borderless datatable pickup_history">';
								shipment += '<thead>';
								shipment += '<tr role="row">';
								shipment += '<th><strong>Date / Time</strong></th>';
								shipment += '<th><strong>Status</strong></th>';
								shipment += '<th><strong>User</strong></th>';
								shipment += '</tr>';
								shipment += '</thead>';
								shipment += '<tbody>';

								$.each(details.pickup_history, function(index, history) {
									shipment += '<tr>';
									shipment += '<td>' + history.date_time + '</td>';
									shipment += '<td>' + history.status + '</td>';
									shipment += '<td>' + history.user + '</td>';
									shipment += '</tr>';
								});

								shipment += '</tbody>';
								shipment += '</table>';

								shipment += '</div>';
								shipment += '</div>';
							}

							shipment += '</div>';
							shipment += '</div>';


							shipment += '</div>';

							$('#tracking').append(shipment);
						});

						$('#tracking table.datatable.tracking_history').DataTable({
							dom: 't',
							paging: false,
							order: [[0, 'desc']],
							columns: [
								{name: 'date_time', class: 'align-middle date_time'},
								{name: 'status', class: 'align-middle status'},
								{name: 'reason', class: 'align-middle reason'},
								{name: 'remarks', class: 'align-middle remarks'},
								{name: 'user', class: 'align-middle user'},
								{name: 'city', class: 'align-middle city'}
							]
						});

						$('#tracking table.datatable.payment_history').DataTable({
							dom: 't',
							paging: false,
							order: [[0, 'desc']],
							columns: [
								{name: 'date_time', class: 'align-middle date_time'},
								{name: 'status', class: 'align-middle status'},
								{name: 'user', class: 'align-middle user'}
							]
						});
					}
				});
			}

            @if (app('request')->has('tracking_number'))
                track({{ app('request')->input('tracking_number') }});
            @endif

			var select = $('#track_form .tracking_numbers').selectize({
				placeholder: 'Tracking Number(s)*',
				delimiter: ',',
				createOnBlur: true,
				persist: false,
				plugins: ['remove_button'],
				onDropdownOpen: function(dropdown) {
					dropdown.remove();
				},
				onType: function(str) {
					var regex = /^[0-9,]+$/;

					if (!regex.test(str)) {
						select[0].selectize.setTextboxValue('');
					}
				},
				create: function(input) {
					if (input.length >= 12 && Math.floor(input) == input && $.isNumeric(input)) {
						return {
							value: input,
							text: input
						}
					}
					else {
						return false;
					}
				}
			});

			$('#track_form').validate({
				ignore: [],
				errorClass: 'danger',
				successClass: 'success',
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parents('form'));
				},
				submitHandler: function(form) {
                    track($(form).find('.tracking_numbers').val());

					return false;
				}
			});

			$('#tracking').on('click', '.print', function() {
				id = $(this).attr('id');

				print(id);
			});

			$('#tracking').on('click', '.rider_information', function() {
				id = $(this).attr('data-id');

				$.ajax({
				url: '{!! route('admin.tracking.rider_information') !!}',
				method: 'POST',
				data: {
					'_token': '{{ csrf_token() }}',
					'id': id
				}
				})
				.done(function(data) {
					var details = '<table class="table table-sm table-bordered"><tbody>';

					details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Name</strong></td><td class="align-middle text-center">' + data.name + '</td></tr>';
					details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Phone Number</strong></td><td class="align-middle text-center">' + data.phone_number + '</td></tr>';
					details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>City</strong></td><td class="align-middle text-center">' + data.city + '</td></tr>';
					details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Category</strong></td><td class="align-middle text-center">' + data.category + '</td></tr>';
					details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Route</strong></td><td class="align-middle text-center">' + data.route + '</td></tr>';

					details += '</tbody></table>';

					$('#rider_information .modal-body').html(details);

					$('#rider_information').modal('show');
				});
			});

			$('#tracking').on('click', '.cargo_consignment_details', function() {
				id = $(this).attr('data-id');

				$.ajax({
				url: '{!! route('admin.tracking.cargo_consignment_details') !!}',
				method: 'POST',
				data: {
					'_token': '{{ csrf_token() }}',
					'id': id
				}
				})
				.done(function(data) {
					var details = '<table class="table table-sm table-bordered"><tbody>';

					details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Junction 1</strong></td><td class="align-middle text-center">' + data.junction_hub_1 + '</td></tr>';
					details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Junction 2</strong></td><td class="align-middle text-center">' + ((data.junction_hub_2) ? data.junction_hub_2 : '') + '</td></tr>';
					details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Expected Arrival Date</strong></td><td class="align-middle text-center">' + data.expected_arrival_date + '</td></tr>';
					details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Shipping Mode</strong></td><td class="align-middle text-center">' + data.shipping_mode + '</td></tr>';
					details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Transport Mode</strong></td><td class="align-middle text-center">' + data.transport_mode + '</td></tr>';
					details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Transport Mode Vendor</strong></td><td class="align-middle text-center">' + data.transport_mode_vendor + '</td></tr>';
					details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Seal Number</strong></td><td class="align-middle text-center">' + data.seal_number + '</td></tr>';
					details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Builty Number</strong></td><td class="align-middle text-center">' + ((data.builty_number) ? data.builty_number : '') + '</td></tr>';
					details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Shipments Weight</strong></td><td class="align-middle text-center">' + data.shipments_weight + '</td></tr>';
					details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Actual Weight</strong></td><td class="align-middle text-center">' + data.actual_weight + '</td></tr>';
					details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Vendor Weight</strong></td><td class="align-middle text-center">' + ((data.vendor_weight) ? data.vendor_weight : '') + '</td></tr>';
					details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Weight Charges / kg</strong></td><td class="align-middle text-center">' + ((data.weight_charges_per_kg) ? data.weight_charges_per_kg : '') + '</td></tr>';
					details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Extra Charges</strong></td><td class="align-middle text-center">' + ((data.extra_charges) ? data.extra_charges : '') + '</td></tr>';
					details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Total Weight Charges</strong></td><td class="align-middle text-center">' + ((data.total_weight_charges) ? data.total_weight_charges : '') + '</td></tr>';
					details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Sender Name</strong></td><td class="align-middle text-center">' + data.sender_name + '</td></tr>';
					details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>Receiver Name</strong></td><td class="align-middle text-center">' + data.receiver_name + '</td></tr>';

					details += '</tbody></table>';

					$('#cargo_consignment_details .modal-body').html(details);

					$('#cargo_consignment_details').modal('show');
				});
			});
		});
	</script>
@endsection