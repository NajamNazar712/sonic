@extends('admin.layout.master')

@section('title', 'Create Cargo')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Create Cargo
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<div id="camera_scan" class="d-none">
			                    <div id="camera_view" class="camera_view"></div>
			                </div>

							<form id="add_shipment_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
								<div class="form-group">
									<input type="text" name="tracking_number" class="form-control tracking_number" placeholder="Tracking Number*" data-rule-required="true" data-msg-required="Tracking Number is required">

									<div class="d-inline-block ml-1">
										<a href="#" id="camera_scan_initiate" tabindex="-1">
				                            <i class="ft-camera h1"></i>
				                        </a>
				                    </div>
								</div>

								<div class="form-group ml-1">
									<button type="submit" name="add" class="btn btn-primary add" value="Add">Add</button>
								</div>
							</form>

							<div id="information" class="information text-center">
								Hub: <span class="hub">None</span> | Scanned: <span class="scanned">0</span>/<span class="total">0</span>
							</div>

							<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">Tracking Number</th>
										<th class="border-primary border-darken-1">Order ID</th>
										<th class="border-primary border-darken-1">Service Type</th>
										<th class="border-primary border-darken-1">Destination</th>
										<th class="border-primary border-darken-1">Amount</th>
										<th class="border-primary border-darken-1">Open Box</th>
										<th class="border-primary border-darken-1"></th>
									</tr>
								</thead>
							</table>

							<div class="text-center">
								<button type="submit" class="btn btn-primary mr-2" id="cargo_consignment_confirm" data-toggle="modal" data-target="#cargo_consignment" disabled="disabled">Confirm</button>
								<button type="submit" class="btn btn-primary" id="add_draft_cargo" disabled="disabled">Save To Draft</button>
							</div>

							<div class="modal fade" id="cargo_consignment" role="dialog" aria-labelledby="cargo_consignment_title" aria-hidden="true">
								<div class="modal-dialog modal-lg" role="document">
									<div class="modal-content">
										<form class="form-horizontal" method="POST" action="{{ route('admin.cargo.create.store') }}" novalidate="novalidate">
											{{ csrf_field() }}

											<input type="hidden" name="cargo_type" class="cargo_type">


											<input type="hidden" name="shipment_ids" class="shipment_ids">

											<input type="hidden" name="open_box_ids" class="open_box_ids">
											<div class="modal-header">
												<h4 class="modal-title" id="cargo_consignment_title">Cargo Consignment</h4>
											</div>
											<div class="modal-body">
												<div class="row">
													<div class="col-12">
														<h4 class="form-section mb-2 text-center">Shipper Information</h4>
													</div>

													<div class="col">
														<div class="form-group">
															<input type="hidden" name="origin_hub_id" class="origin_hub_id">

															<p class="mt-1 border-bottom border-light text-center font-medium-1 text-bold-600 origin"></p>
														</div>
													</div>

													<div class="col">
														<div class="form-group">
															<input type="hidden" name="destination_hub_id" class="destination_hub_id">

															<p class="mt-1 border-bottom border-light text-center font-medium-1 text-bold-600 destination"></p>
														</div>
													</div>

													<div class="col">
														<div class="form-group">
															<select name="junction_1" class="select2 junction_1" data-rule-required="true" data-msg-required="Junction 1 is required">
															</select>
														</div>
													</div>

													<div class="col">
														<div class="form-group">
															<select name="junction_2" class="select2 junction_2">
															</select>
														</div>
													</div>

													<div class="w-100"></div>

													<div class="col">
														<div class="form-group">
															<select name="transport_mode" class="select2 transport_mode" data-rule-required="true" data-msg-required="Transport Mode is required">
															</select>
														</div>
													</div>

													<div class="col">
														<div class="form-group">
															<select name="transport_mode_vendor" class="select2 transport_mode_vendor" data-rule-required="true" data-msg-required="Vendor is required">
															</select>
														</div>
													</div>

													<div id="new_vendor" class="col d-none">
														<div class="form-group">
															<input type="text" name="vendor_name" class="form-control vendor_name" placeholder="Vendor Name*" data-rule-required="true" data-msg-required="Vendor Name is required">
														</div>
													</div>

													<div class="w-100"></div>

													<div class="col">
														<div class="form-group">
															<input type="text" name="seal_number" class="form-control rounded-right seal_number" placeholder="Seal Number*" data-rule-required="true" data-msg-required="Seal Number is required" data-rule-minlength="5" data-msg-minlength="Seal Number needs to be at-least 5 numbers" data-rule-remote="{{ route('admin.cargo.create.seal_number') }}" data-msg-remote="Seal Number must be unique">
														</div>
													</div>

													<div class="col">
														<div class="form-group">
															<input type="text" name="actual_weight" class="form-control rounded-right actual_weight" placeholder="Actual Weight*" data-rule-required="true" data-msg-required="Actual Weight is required">
														</div>
													</div>
													<div class="col-12">
														<div class="form-group">
															<select name="shipping_mode_id" class="select2 shipping_mode_select" data-rule-required="true" data-msg-required="Shipping Mode is required">
															</select>
														</div>
													</div>

													<div class="col-12">
														<h4 class="form-section mb-2 text-center">Sender Information</h4>
													</div>

													<div class="col">
														<div class="form-group">
															<input type="hidden" name="sender_id" class="sender_id">

															<p class="mt-1 border-bottom border-light text-center font-medium-1 text-bold-600 sender_name"></p>
														</div>
													</div>

													<div class="col">
														<div class="form-group">
															<select name="receiver_id" class="select2 receiver_id">
															</select>
														</div>
													</div>
												</div>
											</div>
											<div class="modal-footer text-center justify-content-around">
												<button type="submit" name="submit_and_print_form" class="btn btn-primary" value="submit_and_print_form">Submit &amp; Print</button>
												<button type="submit" name="submit_form" class="btn btn-primary" value="submit_form">Submit</button>
											</div>
										</form>
								</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>



	<div class="modal fade" id="ShipmentPiecesModal" data-backdrop="static" role="dialog" aria-labelledby="ShipmentPiecesModal" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="delivered_shipments_modal_title">Shipment Piece(s)</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body text-center">
					<form id="add_shipment_pieces_form" class="form-horizontal mb-1 justify-content-center" novalidate="novalidate">
						<input type="hidden" name="piece_shipment_id" id="piece_shipment_id">
						<input type="hidden" name="piece_tracking_number" id="piece_tracking_number">
						<input type="hidden" name="piece_shipment_count" id="piece_shipment_count">
						<div class="row justify-content-center">
							<div class="form-group col-5">
								<input type="text" name="scan_piece" id="scan_piece" class="form-control scan_piece" placeholder="Scan Piece">
							</div>
						</div>
						<div class="row justify-content-center">
							<div class="row">
								<p id="total_item_count"></p>
							</div>
						</div>
						<table class="table table-bordered datatable" id="piece_datatable" style="z-index: 3;">
							<thead>
							<tr role="row" class="bg-primary white">
								<th class="border-primary border-darken-1">S. No.</th>
								<th class="border-primary border-darken-1">Piece ID</th>
								<th class="border-primary border-darken-1">Tracking Number</th>
								<th class="border-primary border-darken-1"></th>
							</tr>
							</thead>
						</table>

						<div class="row justify-content-center">
							<div class="form-group col-5">
								<input type="text" name="scan_piece_tracking_number" id="scan_piece_tracking_number" class="form-control scan_piece_tracking_number" placeholder="Scan Tracking Number*" data-rule-required="true" data-msg-required="Tracking Number is required" disabled="disabled">
							</div>
						</div>

						<div class="form-group">
							<button type="submit" class="btn btn-primary piece_confirm" id="piece_confirm" disabled="disabled">Confirm</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/quagga/quagga.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/custom.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			@if (session('print'))
				$.ajax({
					url: '{!! route('admin.cargo.in_transit.print') !!}',
					method: 'POST',
					data: {
						'id': '{{ session('print') }}',
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
			@endif

			$('#add_shipment_pieces_form input.scan_piece').inputmask({
				'alias': 'integer',
				'allowMinus': false,
				'allowPlus': false
			});
			var shipment_ids = [];
			var open_box_ids = [];
			var hub_id = 0;
			var cargo_type = 0;
			var shipping_mode_id = 0;
			var shipment_piece_ids = [];
			var all_shipment_piece_ids = [];

			var table = $('#datatable').DataTable({
				dom: 'ltipr',
				scrollX: true,
                autoWidth : false,
                paging:false,
                columns: [
					{name: 'serial_number', orderable: false, searchable: false, class: 'align-middle serial_number'},
					{name: 'tracking_number', class: 'align-middle tracking_number', orderable: false},
					{name: 'order_id', class: 'align-middle order_id', orderable: false},
					{name: 'service_type', class: 'align-middle service_type', orderable: false},
					{name: 'destination', class: 'align-middle destination', orderable: false},
					{name: 'amount', class: 'align-middle amount', orderable: false},
					{name: 'open_box', class: 'align-middle open_box', orderable: false},
					{name: 'action', class: 'align-middle action',orderable: false, searchable: false}
				],
				rowCallback: function(row, data, index) {
					// var info = table.page.info();
                    //
					// $('td:eq(0)', row).html(index + 1 + info.page * info.length);
				},
				initComplete: function() {
					this.api().table().columns.adjust();
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
					    blockPagePermanently();
						$.ajax({
							url: '{!! route('admin.cargo.create.shipment_details') !!}',
							method: 'POST',
							data: {
								'tracking_number': tracking_number,
								'hub_id': hub_id,
								'shipping_mode_id': shipping_mode_id,
								'cargo_type': cargo_type,
								'_token': '{{ csrf_token() }}'
							},
							// timeout: 30000,
							error: function (data) {
								$('#add_shipment_form button.add').prop('disabled', false);
								UnblockPagePermanently();
                                scan_sound(2);
								toastr.error('Couldn\'t connect to server, check internet connection and re-enter!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
							},
							success: function(data) {
								if (data.status == 0) {
									id = data.details.id;

									var index = $.inArray(id, shipment_ids);

									if (index === -1) {
	                                    var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-danger cargo_remove"><i class="la la-close"></i></a>';
	                                    var open_box = '<input type="checkbox" class="form-control open_box" name="open_box['+ data.details.id+']">';
	                                    var rowNo = table.rows().count();
	                                    table.row.add([rowNo+1, data.details.tracking_number, data.details.order_id, data.details.service_type, data.details.destination, data.details.amount, open_box,remove]).node().id = data.details.id;
										table.draw(false);
	                                    table.order([0, 'desc']).draw();
	                                    shipment_ids.push(data.details.id);
	                                    scan_sound(1);
										$('#information .scanned').html(shipment_ids.length);

										if (hub_id == 0) {
											hub_id = data.details.hub.id;

											$('#information .hub').html(data.details.hub.name);

											$('#information .total').html(data.details.total);
										}

										if (shipping_mode_id == 0) {
											shipping_mode_id = data.details.shipping_mode.id;

											// $('#information .shipping_mode').html(data.details.shipping_mode.name);
										}

										if (cargo_type == 0) {
											cargo_type = data.details.cargo_type;
										}

										$('#add_shipment_form button.add').prop('disabled', false);

										$('#cargo_consignment_confirm').prop('disabled', false);
	                                    $('#add_draft_cargo').prop('disabled', false);
	                                    UnblockPagePermanently();
										toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
									}
								}
								else if(data.status == 2){
									$('#scan_piece_tracking_number').prop('disabled', true);
									$('#scan_piece_tracking_number').val(tracking_number);
									$('#piece_confirm').prop('disabled', true);
									if(data.details.scanned_shipment_piece){
										var piece_index = $.inArray(parseInt(data.details.scanned_shipment_piece), all_shipment_piece_ids);
										if (piece_index === -1) {
											var piece_remove_button = '<button type="button" class="btn btn-icon btn-danger"><i class="la la-close"></i></button>';
											var piece_rowNo = piece_table.rows().count();
											piece_table.row.add([piece_rowNo + 1, data.details.scanned_shipment_piece, data.details.tracking_number, piece_remove_button]).node().id = data.details.scanned_shipment_piece;
											piece_table.draw(false);
											piece_table.columns.adjust().draw();
											scan_sound(1);
											shipment_piece_ids.push(data.details.scanned_shipment_piece);
											$('#piece_shipment_id').val(data.details.id);
											$('#piece_tracking_number').val(data.details.tracking_number);
											$('#piece_shipment_count').val(data.details.pieces);
											$('#total_piece_count').html('Total Shipment Piece(s): ' + data.details.pieces);
											// all_shipment_item_ids.push(data.scanned_shipment_item);
											var check = parseInt(piece_rowNo) + 1;
											if(parseInt(data.details.piece) === parseInt(check)){
												$('#scan_piece_tracking_number').prop('disabled', false);
												$('#piece_confirm').prop('disabled', false);
											}
											toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
											$('#ShipmentPiecesModal').modal('show');
										}
										else{
											toastr.error('Shipment has been added already', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
										}
									}
									else{
										$('#piece_shipment_id').val(data.details.id);
										$('#piece_tracking_number').val(data.details.tracking_number);
										$('#piece_shipment_count').val(data.details.pieces_count);
										$('#total_piece_count').html('Total Shipment Pieces: ' + data.details.pieces_count);
										$('#ShipmentPiecesModal').modal('show');
										toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
									}
									$('#add_shipment_form button.add').prop('disabled', false);

									$('#arrival_of_shipments_form button.confirm').prop('disabled', false);
									UnblockPagePermanently();
								}
								else {
									$('#add_shipment_form button.add').prop('disabled', false);
									UnblockPagePermanently();
	                                scan_sound(2);
									toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
								}
							}
						});
					}
					else {
						$('#add_shipment_form button.add').prop('disabled', false);
                        scan_sound(2);
						toastr.error('Shipment has been added already', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
					}

					return false;
				}
			});

			$('#cargo_consignment_confirm').bind('click', function() {

				if ($('#cargo_consignment form .junction_1').hasClass('select2-hidden-accessible')) {
					$('#cargo_consignment form .junction_1').html('').select2('destroy');
				}

				if ($('#cargo_consignment form .junction_2').hasClass('select2-hidden-accessible')) {
					$('#cargo_consignment form .junction_2').html('').select2('destroy');
				}

				if ($('#cargo_consignment form .transport_mode').hasClass('select2-hidden-accessible')) {
					$('#cargo_consignment form .transport_mode').html('').select2('destroy');
				}

				if ($('#cargo_consignment form .transport_mode_vendor').hasClass('select2-hidden-accessible')) {
					$('#cargo_consignment form .transport_mode_vendor').html('').select2('destroy');
				}

				if ($('#cargo_consignment form .shipping_mode_select').hasClass('select2-hidden-accessible')) {
					$('#cargo_consignment form .shipping_mode_select').html('').select2('destroy');
				}

				if ($('#cargo_consignment form .receiver_id').hasClass('select2-hidden-accessible')) {
					$('#cargo_consignment form .receiver_id').html('').select2('destroy');
				}
                blockPagePermanently();
				$.ajax({
					url: '{!! route('admin.cargo.create.consignment_details') !!}',
					method: 'POST',
					data: {
						'shipment_ids': shipment_ids,
						'cargo_type': cargo_type,
						'_token': '{{ csrf_token() }}'
					},
					// timeout: 30000,
					error: function (data) {
						$('#cargo_consignment').modal('hide');

						UnblockPagePermanently();

						toastr.error('Couldn\'t connect to server, check internet connection and re-enter!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
					},
					success: function (data) {
						open_box_ids = [];
						table.rows().every(function(index) {
	                    	var node = $(this.node());
	                    	if(node.find('td.open_box input').is(':checked')){
	                    		open_box_ids.push(parseInt(node.attr('id')));
	                    	}
						});
						$('#cargo_consignment form .cargo_type').val(cargo_type);
						// $('#cargo_consignment form .shipping_mode_id').val(shipping_mode_id);
						$('#cargo_consignment form .shipment_ids').val(shipment_ids);
						$('#cargo_consignment form .open_box_ids').val(open_box_ids);

						$('#cargo_consignment form .origin_hub_id').val(data.origin.id);
						$('#cargo_consignment form .origin').html(data.origin.name);

						$('#cargo_consignment form .destination_hub_id').val(data.destination.id);
						$('#cargo_consignment form .destination').html(data.destination.name);

						$('#cargo_consignment form .actual_weight').val(data.actual_weight);

						$.each(data.junctions, function(index, junction) {
							$('#cargo_consignment form .junction_1').append('<option value="' + junction.id + '">' + junction.name + '</option>');
							$('#cargo_consignment form .junction_2').append('<option value="' + junction.id + '">' + junction.name + '</option>');
						});

						if(data.junction_1) {
							$('#cargo_consignment form .junction_1').val(data.junction_1);
							$('#cargo_consignment form .junction_1').select2({
								width: '100%',
								placeholder: 'Junction 1*'
							}).bind('change', function() {
								$(this).valid();
							});
						}
						else{
							$('#cargo_consignment form .junction_1').prepend('<option value="" selected="selected"></option>').select2({
								width: '100%',
								placeholder: 'Junction 1*'
							}).bind('change', function() {
								$(this).valid();
							});
						}
						if(data.junction_2) {
							$('#cargo_consignment form .junction_2').val(data.junction_2);
							$('#cargo_consignment form .junction_2').select2({
								width: '100%',
								placeholder: 'Junction 2',
								allowClear: true
							});
						}
						else{
							$('#cargo_consignment form .junction_2').prepend('<option value="" selected="selected"></option>').select2({
								width: '100%',
								placeholder: 'Junction 2',
								allowClear: true
							})
						}

						$('#cargo_consignment form input.seal_number').inputmask({
							'alias': 'integer',
							'allowMinus': false,
							'allowPlus': false
						});

						$('#cargo_consignment form input.actual_weight').inputmask({
							'alias': 'decimal',
							'allowMinus': false,
							'allowPlus': false,
							'digits': 2,
							'min': 0.1,
							'max': 100000
						});
						$.each(data.shipping_modes, function(index, shipping_mode) {
							$('#cargo_consignment form .shipping_mode_select').append('<option value="' + shipping_mode.id + '">' + shipping_mode.mode + '</option>');
						});

						$('#cargo_consignment form .shipping_mode_select').prepend('<option value="" selected="selected"></option>').select2({
							width: '100%',
							placeholder: 'Select Shipping Mode*'
						}).bind('change', function() {
							$(this).valid();
						});

						$.each(data.transport_modes, function(index, transport_mode) {
							$('#cargo_consignment form .transport_mode').append('<option value="' + transport_mode.id + '">' + transport_mode.name + '</option>');
						});

						$('#cargo_consignment form .transport_mode').prepend('<option value="" selected="selected"></option>').select2({
							width: '100%',
							placeholder: 'Transport Mode*'
						}).bind('change', function() {
							$(this).valid();

							$('#cargo_consignment form .transport_mode_vendor').html('');

							$.each(transport_mode_vendors[this.value], function(index, vendor) {
								var option = new Option(vendor.name, vendor.id, false, false);
								$('#cargo_consignment form .transport_mode_vendor').append(option);
							});

							var option = new Option('Others', 0, false, false);
							$('#cargo_consignment form .transport_mode_vendor').append(option);

							$('#cargo_consignment form .transport_mode_vendor').val(null).trigger('change');
						});

						transport_mode_vendors = data.transport_mode_vendors;

						$('#cargo_consignment form .transport_mode_vendor').prepend('<option value="" selected="selected"></option>').select2({
							width: '100%',
							placeholder: 'Vendor*'
						}).bind('change', function() {
							if (this.value) {
								$(this).valid();
							}

							if (this.value && this.value == 0) {
								$('#cargo_consignment #new_vendor').removeClass('d-none');
							}
							else {
								$('#cargo_consignment #new_vendor').addClass('d-none');

								$('#cargo_consignment #vendor_name-error').remove();
							}
						});

						$('#cargo_consignment form .sender_id').val(data.sender.id);
						$('#cargo_consignment form .sender_name').html(data.sender.name);

						$.each(data.receivers, function(index, receiver) {
							$('#cargo_consignment form .receiver_id').append('<option value="' + receiver.id + '">' + receiver.name + '</option>');
						});

						if(data.receiver){
							$('#cargo_consignment form .receiver_id').val(data.receiver);
							$('#cargo_consignment form .receiver_id').select2({
								width: '100%',
								placeholder: 'Receiver Name',
								allowClear: true
							}).bind('change', function() {
								$(this).valid();
							});
						}
						else{
							$('#cargo_consignment form .receiver_id').prepend('<option value="" selected="selected"></option>').select2({
								width: '100%',
								placeholder: 'Receiver Name',
								allowClear: true
							}).bind('change', function() {
								$(this).valid();
							});
						}
	                    UnblockPagePermanently();
						$('#cargo_consignment form').validate({
							errorClass: 'danger',
							successClass: 'success',
							errorPlacement: function(error, element) {
								error.addClass('w-100').appendTo(element.parent('.form-group'));
							},
							normalizer: function(value) {
								return $.trim(value);
							},
							submitHandler: function(form) {
								var pressed_button = $(this.submitButton);

								$(form).append('<input type="hidden" name="' + pressed_button.attr('name') + '" value="' + pressed_button.attr('value') + '">');

								$(form).find('button[type=submit]').attr('disabled', 'disabled');

	                            blockPagePermanently();

	                            swal({
									text: 'Are you sure you want to submit?',
									icon: 'info',
									buttons: {
										cancel: {
											text: 'No',
											value: null,
											visible: true,
											closeModal: true,
										},
										confirm: {
											text: 'Yes',
											value: true,
											visible: true,
											closeModal: true
										}
									},
									closeOnClickOutside: false,
									closeOnEsc: false,
								}).then(function(confirm) {
									if(confirm) {
										swal({
											title: 'Please Wait!',
											text: 'Your cargo is being created!',
											icon: 'info',
											buttons: false,
											closeOnClickOutside: false,
											closeOnEsc: false
										});

										form.submit();
									}
									else {
										$(form).find('button[type=submit]').prop('disabled', false);

										UnblockPagePermanently();
									}
								});
							}
						});
					}
				});
			});
            //Draft
            $('#add_draft_cargo').on('click', function () {
                if(shipment_ids.length > 0){
                    blockPagePermanently();
                    $.ajax({
                        url: '{!! route('admin.cargo.draft.add') !!}',
                        method: 'POST',
                        data: {
                            'shipment_ids': shipment_ids,
                            'cargo_type': cargo_type,
                            'hub_id' : hub_id,
                            'shipping_mode_id': shipping_mode_id,
                            '_token': '{{ csrf_token() }}'
                        },
                        // timeout: 30000,
						error: function (data) {
							UnblockPagePermanently();

							toastr.error('Couldn\'t connect to server, check internet connection and re-enter!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
						},
						success: function (data) {
							if(data.status){
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            }else{
                                UnblockPagePermanently();
                                var error = "Something went wrong, Please try again";
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                            }
                            window.location.reload();
						}
                    });
                }
            });

            $('#camera_scan_initiate').bind('click', function() {
				if ($('#camera_scan').hasClass('d-none')) {
	                $('#camera_scan').removeClass('d-none');

	                camera_scanning_start('#camera_view');
            	}
            	else {
            		$('#camera_scan').addClass('d-none');

            		camera_scanning_stop();
            	}
            });

            $('body').on('click','.cargo_remove',function () {
                var shipment_id = parseInt($(this).parents('tr').attr('id'));
                var index = $.inArray(shipment_id, shipment_ids);
                if(index !== -1){
                    shipment_ids.splice(index,1);
                    table.row( $(this).parents('tr') ).remove().draw();
                    if(shipment_ids.length == 0){
                        $('#information .scanned').html(shipment_ids.length);

                        hub_id = 0;
                        cargo_type = 0;
                        shipping_mode_id = 0;


                        $('#information .hub').text('None');

                        $('#information .total').text(0);


                        $('#information .shipping_mode').text('None');


                        $('#add_shipment_form button.add').prop('disabled', false);

                        $('#cargo_consignment_confirm').prop('disabled', false);

                    }else{
                        $('#information .scanned').html(shipment_ids.length);

                    }
				}


            });

			$('#add_shipment_pieces_form input.scan_piece').focus();

			var piece_table = $('#piece_datatable').DataTable({
				dom: 'ltipr',
				paging:false,
				autoWidth: false,
				columns: [
					{orderable: false, searchable: false, name: 'piece_serial_number', class: 'align-middle serial_number'},
					{name: 'piece_id', class: 'align-middle piece_id', orderable: false, searchable: false},
					{name: 'piece_tracking_number', class: 'align-middle tracking_number', orderable: false, searchable: false},
					{name: 'piece_remove', class: 'align-middle remove', sortable: false, orderable: false, searchable: false}
				],
				initComplete: function() {
					this.api().table().columns.adjust();
				}
			});

			$('#scan_piece').on('change', function () {
				var item = parseInt($(this).val());
				$('#scan_piece').val('').focus();
				if(item){
					var new_item_index = $.inArray(item, shipment_piece_ids);
					if (new_item_index === -1) {
						var shipment_id = $('#piece_shipment_id').val();
						var shipment_tracking_number = $('#piece_tracking_number').val();
						var shipment_piece_count = $('#piece_shipment_count').val();
						$.ajax({
							url: '{!! route('admin.cargo.piece_details') !!}',
							method: 'POST',
							data: {
								'shipment_id': shipment_id,
								'piece_id': item,
								'_token': '{{ csrf_token() }}'
							},
							// timeout: 30000,
							error: function (data) {
								toastr.error('Couldn\'t connect to server, check internet connection and re-enter!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
							},
							success: function (data) {
								if(data.status == 0){
									var piece_remove_button = '<button type="button" class="btn btn-icon btn-danger"><i class="la la-close"></i></button>';
									var piece_rowNo = piece_table.rows().count();
									piece_table.row.add([piece_rowNo + 1, data.scanned_shipment_piece, shipment_tracking_number, piece_remove_button]).node().id = data.scanned_shipment_piece;
									piece_table.draw(false);
									piece_table.columns.adjust().draw();
									scan_sound(1);
									shipment_piece_ids.push(data.scanned_shipment_piece);
									var check = parseInt(piece_rowNo) + 1;
									if(parseInt(shipment_piece_count) === parseInt(check)){
										$('#scan_piece_tracking_number').prop('disabled', false);
										$('#piece_confirm').prop('disabled', false);
									}
									toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
								}
								else{
									toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
								}
							}
						});
					}
					else{
						toastr.error('Shipment Item has been added already', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
					}
				}
			});
			$('#piece_datatable tbody').on('click', 'tr td.remove button', function() {
				var parent = $(this).parents('tr');
				var id = parseInt(parent.attr('id'));
				var index = $.inArray(id, shipment_piece_ids);
				if (index !== -1) {
					piece_table.row(parent).remove();
					piece_table.draw(false);
					var piece_rowNo = piece_table.rows().count();
					shipment_piece_ids.splice(index, 1);
					var shipment_piece_count = $('#piece_shipment_count').val();
					var check = parseInt(piece_rowNo);
					if(parseInt(shipment_piece_count) !== parseInt(check)){
						$('#scan_piece_tracking_number').prop('disabled', true);
						$('#piece_confirm').prop('disabled', true);
					}
				}
			});

			$('#add_shipment_pieces_form').validate({
				errorClass: 'danger',
				successClass: 'success',
				errorPlacement: function (error, element) {
					error.addClass('w-100').appendTo(element.parents('.form-group'));
				},
				submitHandler: function (form) {
					var shipment_id = $('#piece_shipment_id').val();
					var tracking_number = $(form).find('input.scan_piece_tracking_number').val();
					$.ajax({
						url: '{!! route('admin.cargo.create.shipment_details') !!}',
						method: 'POST',
						data: {
							'tracking_number': tracking_number,
							'pieces_confirm': 1,
							'_token': '{{ csrf_token() }}'
						}
					})
							.done(function(data) {
								if (data.status == 0) {
									id = data.details.id;

									var index = $.inArray(id, shipment_ids);

									if (index === -1) {
										var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-danger cargo_remove"><i class="la la-close"></i></a>';
										var open_box = '<input type="checkbox" class="form-control open_box" name="open_box['+ data.details.id+']">';
										var rowNo = table.rows().count();
										table.row.add([rowNo+1, data.details.tracking_number, data.details.order_id, data.details.service_type, data.details.destination, data.details.amount, open_box,remove]).node().id = data.details.id;
										table.draw(false);
										table.order([0, 'desc']).draw();
										shipment_ids.push(data.details.id);
										scan_sound(1);
										$('#information .scanned').html(shipment_ids.length);

										if (hub_id == 0) {
											hub_id = data.details.hub.id;

											$('#information .hub').html(data.details.hub.name);

											$('#information .total').html(data.details.total);
										}

										if (shipping_mode_id == 0) {
											shipping_mode_id = data.details.shipping_mode.id;

											// $('#information .shipping_mode').html(data.details.shipping_mode.name);
										}

										if (cargo_type == 0) {
											cargo_type = data.details.cargo_type;
										}

										$('#add_shipment_form button.add').prop('disabled', false);

										$('#cargo_consignment_confirm').prop('disabled', false);
										$('#add_draft_cargo').prop('disabled', false);
										UnblockPagePermanently();
										toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
										$('#ShipmentPiecesModal').modal('hide');
									}
								}
								else {
									$('#add_shipment_form button.add').prop('disabled', false);
									UnblockPagePermanently();
									scan_sound(2);
									toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
								}
							});
					return false;
				}
			});

			$('#ShipmentPiecesModal').on('hide.bs.modal', function (e) {
				$('#scan_piece_tracking_number').val('');
				shipment_piece_ids = [];
				piece_table.clear().draw();
			});
		});

		function camera_scan_detected(tracking_number) {
            $('#add_shipment_form input.tracking_number').val(tracking_number);

            $('#add_shipment_form').trigger('submit');
        }


	</script>
@endsection