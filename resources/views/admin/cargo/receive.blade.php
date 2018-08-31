@extends('admin.layout.master')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Receive Cargo
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<form id="add_shipment_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
								<div class="form-group">
									<input type="text" name="tracking_number" class="form-control tracking_number" placeholder="Tracking Number*" data-rule-required="true" data-msg-required="Tracking Number is required">
								</div>

								<div class="form-group ml-1">
									<button type="submit" name="add" class="btn btn-primary" value="Add">Add</button>
								</div>
							</form>

							<div id="information" class="information text-center">
								Cargo No #{{ session('cargo_consignment_id') }} | Scanned: <span class="scanned">0</span>/<span class="total">{{ $total }}</span>
							</div>

							<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">Tracking Number</th>
										<th class="border-primary border-darken-1">Origin</th>
										<th class="border-primary border-darken-1">Destination</th>
										<th class="border-primary border-darken-1">Hub</th>
										<th class="border-primary border-darken-1">Consignee</th>
										<th class="border-primary border-darken-1">Amount</th>
										<th class="border-primary border-darken-1">Shipping Mode</th>
										<th class="border-primary border-darken-1">Service Type</th>
									</tr>
								</thead>
							</table>

							<form id="receive_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.cargo.receive.store') }}" novalidate="novalidate">
								{{ csrf_field() }}

								<input type="hidden" name="cargo_consignment_id" class="cargo_consignment_id" value="{{ session('cargo_consignment_id') }}">

								<input type="hidden" name="short_received" class="short_received">

								<input type="hidden" name="shipment_ids" class="shipment_ids">

								<div class="form-group ml-1">
									<button type="submit" name="receive" class="btn btn-primary receive" value="Confirm" disabled="disabled">Receive</button>
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
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

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
			border-color: #666EE8;
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
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			var cargo_consignment_id = {{ session('cargo_consignment_id') }};

			var shipment_ids = [];

			var table = $('#datatable').DataTable({
				dom: 'ltipr',
				fixedHeader: {
					header: true,
					headerOffset: $('.header-navbar').height()
				},
				lengthMenu: [[1, 25, 50, 100], [1, 25, 50, 100]],
				pageLength: 25,
				pagingType: 'full_numbers',
				columns: [
					{name: 'serial_number', orderable: false, searchable: false, class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{name: 'tracking_number', class: 'align-middle tracking_number'},
					{name: 'origin', class: 'align-middle origin'},
					{name: 'destination', class: 'align-middle destination'},
					{name: 'hub', class: 'align-middle hub'},
					{name: 'consignee', class: 'align-middle consignee'},
					{name: 'amount', class: 'align-middle amount'},
					{name: 'shipping_mode', class: 'align-middle shipping_mode'},
					{name: 'service_type', class: 'align-middle service_type'}
				],
				rowCallback: function(row, data, index) {
					var info = table.page.info();

					$('td:eq(0)', row).html(index + 1 + info.page * info.length);
				}
			});

			$('#add_shipment_form input.tracking_number').inputmask({
				'alias': 'integer',
				'allowMinus': false,
				'allowPlus': false
			});

			$('#add_shipment_form').validate({
				errorClass: 'danger',
				successClass: 'success',
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parents('form'));
				},
				submitHandler: function(form) {
					$('#add_shipment_form button.add').prop('disabled', true);

					var tracking_number = $(form).find('input.tracking_number').val();

					form.reset();

					if (table.columns('.tracking_number').data().eq(0).indexOf(parseInt(tracking_number)) === -1) {
						$.ajax({
							url: '{!! route('admin.cargo.receive.shipment_details') !!}',
							method: 'POST',
							data: {
								'tracking_number': tracking_number,
								'cargo_consignment_id': cargo_consignment_id,
								'_token': '{{ csrf_token() }}'
							}
						})
						.done(function(data) {
							$('#add_shipment_form button.add').prop('disabled', false);

							if (data.status == 0) {
								table.row.add([0, data.details.tracking_number, data.details.origin, data.details.destination, data.details.hub, data.details.consignee, data.details.amount, data.details.shipping_mode, data.details.service_type]);
								table.draw(false);

								shipment_ids.push(data.details.id);

								$('#information .scanned').html(shipment_ids.length);

								$('#receive_form .receive').prop('disabled', false);

								toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
							}
							else {
								toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
							}
						});
					}
					else {
						$('#add_shipment_form button.add').prop('disabled', false);

						toastr.error('Shipment has been added already', 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
					}

					$('#add_shipment_form button.add').prop('disabled', false);

					return false;
				}
			});

			$('#receive_form').bind('submit', function(e) {
				e.preventDefault();

				var form = this;

				$('#receive_form input.shipment_ids').val(shipment_ids);

				$.ajax({
					url: '{!! route('admin.cargo.receive.short_received') !!}',
					method: 'POST',
					data: {
						'cargo_consignment_id': cargo_consignment_id,
						'shipment_ids': shipment_ids,
						'_token': '{{ csrf_token() }}'
					}
				})
				.done(function(data) {
					if (data.status == 0) {
						if (data.short_received) {
							var html = 'There are shipments that are short received from Cargo No#' + cargo_consignment_id + ':<br/>';

							$.each(data.short_received, function(index, tracking_number) {
								html += tracking_number + '<br/>';
							});

							html += 'Are you sure, you want to confirm this Cargo received?';

							$('#receive_form input.short_received').val(1);
						}
						else {
							var html = 'Are you sure, you want to confirm Cargo No#' + cargo_consignment_id + ' as received?';

							$('#receive_form input.short_received').val(0);
						}

						content = document.createElement('div');
						content.innerHTML = html;

						swal({
							content: content,
							icon: 'warning',
							buttons: {
								cancel: {
									text: 'Close',
									value: null,
									visible: true,
									closeModal: true,
								},
								confirm: {
									text: 'Done',
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
								form.submit();
							}
						});
					}
					else {
						toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
					}
				});
			});
		});
	</script>
@endsection