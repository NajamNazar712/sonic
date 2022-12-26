@extends('client.layout.master')

@section('title', 'Shipments List')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Shipments List
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('client.inc.messages')

							<form id="consignee_phone_number_search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
								<div class="row justify-content-center">
									<div class="form-group mr-2">
										<label for="print_by_checkbox" class="font-medium-2 text-bold-600 mr-1">Consignee Phone Number</label>
										<input type="checkbox" name="print_by_checkbox" id="print_by_checkbox" class="switchery print_by_checkbox" data-size="sm" data-switchery="true">
										<label for="print_by_checkbox" class="font-medium-2 text-bold-600 ml-1">Order ID</label>
									</div>
									<div class="form-group" id="phone_number_div">
										<input type="text" name="consignee_phone_number" class="form-control consignee_phone_number" placeholder="Consignee Phone Number" data-rule-required="true" data-msg-required="Consignee Phone Number is required">
									</div>
									<div class="form-group display-hidden" id="order_id_div">
										<input type="text" name="order_id" class="form-control order_id" placeholder="Order ID" data-rule-required="true" data-msg-required="Order ID is required">
									</div>

									<div class="form-group ml-1">
										<button type="submit" name="add" class="btn btn-primary add" value="Add">Add</button>
									</div>
								</div>
							</form>

							<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">Tracking Number</th>
										<th class="border-primary border-darken-1">Consignee Name</th>
										<th class="border-primary border-darken-1">Consignee Phone Number</th>
										<th class="border-primary border-darken-1">Order ID</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/toggle/switchery.min.css')}}">

	<style>
		#consignee_phone_number_search_form input.consignee_phone_number {
			min-width: 225px;
		}
	</style>
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/toggle/switchery.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			var print_by_elem = document.querySelector('.print_by_checkbox');
			var print_by_switchery = new Switchery(print_by_elem);

			$(".print_by_checkbox").change(function() {
				if(this.checked) {
					$('#order_id_div').removeClass('display-hidden');
					$('#phone_number_div').addClass('display-hidden');
				}
				else{
					$('#order_id_div').addClass('display-hidden');
					$('#phone_number_div').removeClass('display-hidden');
				}
			});
			$('#consignee_phone_number_search_form input.consignee_phone_number').focus();

			function print(shipment_ids) {
                $.ajax({
					url: '{!! route('cod.shipment.book.print_air_waybill') !!}',
					method: 'POST',
					data: {
						'_token': '{{ csrf_token() }}',
						'ids': shipment_ids
					}
				})
				.done(function (data) {
					var tab = window.open('', '_blank');

					if (!tab) {
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

			var table = $('#datatable').DataTable({
				dom: '<"d-inline-block"l><"pull-right"B>tipr',
				buttons: [{
					text: '<i class="la la-print"></i> Print',
					className: 'btn btn-primary print',
					enabled: false,
					action: function (e, dt, node, config) {
						print(shipment_ids);
					}
				}, {
					extend: 'excel',
					title: 'Shipments List',
					className:'btn btn-primary',
					text: '<i class="la la-file-excel-o"></i> Excel'
				}],
				paging: false,
				columns: [
					{name: 'serial_number', orderable: false, searchable: false, class: 'align-middle serial_number'},
					{name: 'tracking_number', class: 'align-middle tracking_number', orderable: false},
					{name: 'consignee_name', class: 'align-middle consignee_name', orderable: false},
					{name: 'consignee_phone_number', class: 'align-middle consignee_phone_number', orderable: false},
					{name: 'order_id', class: 'align-middle order_id', orderable: false}
				]
			});

			$('#consignee_phone_number_search_form input.consignee_phone_number').inputmask({
				'alias': 'integer',
				'allowMinus': false,
				'allowPlus': false
			});

			var shipment_ids = [];
			var serial_number = 1;

			$('#consignee_phone_number_search_form').validate({
				errorClass: 'danger',
				successClass: 'success',
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parents('form'));
				},
				submitHandler: function(form) {
					$('#consignee_phone_number_search_form button.add').prop('disabled', true);

					var consignee_phone_number = $(form).find('input.consignee_phone_number').val();
					var order_id = $(form).find('input.order_id').val();

					$(form).find('input.consignee_phone_number').val('');
					$(form).find('input.order_id').val('');

					if (table.columns('.consignee_phone_number').data().eq(0).indexOf(consignee_phone_number) === -1 && table.columns('.order_id').data().eq(0).indexOf(order_id) === -1 ) {
						blockPagePermanently();

						$.ajax({
							url: '{!! route('cod.shipment.list.store') !!}',
							method: 'POST',
							data: {
								'_token': '{{ csrf_token() }}',
								'consignee_phone_number': consignee_phone_number,
								'order_id': order_id,
								'print_by': $('.print_by_checkbox').val()
							}
						})
						.done(function(data) {
							if (data.status == 0) {
								id = data.shipment.id;

								var index = $.inArray(id, shipment_ids);

								if (index === -1) {
									table.row.add([serial_number, data.shipment.tracking_number, data.shipment.consignee_name, data.shipment.consignee_phone_number, data.shipment.order_id]).node().id = data.shipment.id;

									shipment_ids.push(data.shipment.id);

									serial_number++;

									table.draw(false);

									table.order([0, 'desc']).draw();

									table.button('.print').enable();

									scan_sound(1);

									UnblockPagePermanently();
									$('#consignee_phone_number_search_form #consignee_phone_number-error').remove();
									$('#consignee_phone_number_search_form #order_id-error').remove();

									$('#consignee_phone_number_search_form button.add').prop('disabled', false);

									toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
								}
								else {
									$('#consignee_phone_number_search_form #consignee_phone_number-error').remove();
									$('#consignee_phone_number_search_form #order_id-error').remove();

									$('#consignee_phone_number_search_form button.add').prop('disabled', false);

									UnblockPagePermanently();

									scan_sound(2);


									toastr.error('Shipment has been added already', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
								}
							}
							else if (data.status == 2) {
								$('#consignee_phone_number_search_form #consignee_phone_number-error').remove();
								$('#consignee_phone_number_search_form #order_id-error').remove();

								scan_sound(2);

								swal({
									title: data.error,
									icon: 'error',
									buttons: {
										confirm: {
											text: 'OK',
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
										$('#consignee_phone_number_search_form button.add').prop('disabled', false);

										UnblockPagePermanently();
									}
								});
							}
							else {
								$('#consignee_phone_number_search_form #consignee_phone_number-error').remove();
								$('#consignee_phone_number_search_form #order_id-error').remove();

								$('#consignee_phone_number_search_form button.add').prop('disabled', false);

								UnblockPagePermanently();

								scan_sound(2);

								toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
							}

							var checkBox = document.getElementById("print_by_checkbox");

							if (checkBox.checked == true){
								$('#consignee_phone_number_search_form input.order_id').focus();
							} else {
								$('#consignee_phone_number_search_form input.consignee_phone_number').focus();
							}
						});
					}
					else {
						$('#consignee_phone_number_search_form #consignee_phone_number-error').remove();
						$('#consignee_phone_number_search_form #order_id-error').remove();

						$('#consignee_phone_number_search_form button.add').prop('disabled', false);

						UnblockPagePermanently();

						scan_sound(2);

						$('#consignee_phone_number_search_form input.consignee_phone_number').focus();

						toastr.error('Shipment has been added already', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
					}

					return false;
				}
			});
		});
	</script>
@endsection