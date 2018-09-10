@extends('admin.layout.master')

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
		table.table.table-sm td {
			padding: .3rem;
		}

		table.dataTable {
			margin: 0 !important;
		}

		table.dataTable thead tr th {
			padding-left: 0.3em !important;
			white-space: normal;
			word-wrap: break-word;
			border: 0 !important;
		}

		table.dataTable thead tr th:before,
		table.dataTable thead tr th:after {
			height: 20px;
			margin-bottom: -10px;
			top: auto !important;
			bottom: 50% !important;
		}

		table.dataTable thead tr th:before {
			right: 0.65em !important;
		}

		table.dataTable thead tr th:after {
			right: 0.3em !important;
		}

		table.dataTable tbody tr td {
			padding-left: 0.3em;
			padding-right: 0.3em;
		}

		#toast-bottom-center.toast-container {
			text-align: center;
		}

		#toast-bottom-center.toast-container .toast {
			display: table;
			width: auto !important;
			text-align: left;
		}

		.selectize-control {
			width: 300px;
		}

		.selectize-control .selectize-input {
			vertical-align: middle;
		}

		.selectize-control .selectize-input .item {
			word-break: break-all;
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

						toastr.error(message, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
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

							shipment += '<div class="col-5 mt-1">';
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

							shipment += '<div class="col-5 mt-1">';
							shipment += '<h4><u>Consignee Information</u></h4>';
							shipment += '<div class="border">';
							shipment += '<table class="table table-sm table-borderless mb-0">';
							shipment += '<tbody>';
							shipment += '<tr>';
							shipment += '<td><strong>Consignee</strong></td>';
							shipment += '<td>' + details.consignee.name + '</td>';
							shipment += '<td><strong>Origin</strong></td>';
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
							shipment += '<td><strong>Instruction</strong></td>';
							shipment += '<td colspan="3">' + ((details.order_information.instructions) ? details.order_information.instructions : '-') + '</td>';
							shipment += '</tr>';

							shipment += '</tbody>';
							shipment += '</table>';
							shipment += '</div>';
							shipment += '</div>';

							shipment += '<div class="col-12 mt-2">';
							shipment += '<h4><u>Tracking History</u></h4>';
							shipment += '<div class="border">';

							shipment += '<table class="table table-sm table-borderless datatable">';
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
							shipment += '</thead>';
							shipment += '</table>';

							shipment += '</div>';
							shipment += '</div>';

							shipment += '</div>';
							shipment += '</div>';


							shipment += '</div>';

							$('#tracking').append(shipment);
						});

						$('#tracking table.datatable').DataTable({
							dom: 't',
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
		});
	</script>
@endsection