@extends('admin.layout.master')

@section('title', 'Add Shipment Adjustment')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Add Shipment Adjustment
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							@if (session('role_id') == 1 || in_array(137, session('permissions')))
								<div class="row toggle_row justify-content-center">
									<h3 class="pr-1">Individual</h3>
									<input type="checkbox" class="switchery" data-color="success" data-size="sm" name="switch" id="switch"/>
									<h3 class="pl-1">Bulk</h3>
								</div>
							@endif
							<div class="row individual_adjustemnt mt-4 justify-content-center">
								<form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
									<div class="form-group">
										<input type="text" name="tracking_numbers" class="form-control tracking_number" placeholder="Tracking Number*" data-rule-required="true" data-msg-required="Tracking Number is required">
									</div>

									<div class="form-group ml-1">
										<button type="submit" name="search" class="btn btn-primary search" value="Search">Search</button>
									</div>
								</form>

								<div class="shipment mt-2" id="shipment">
								</div>

								@if (session('role_id') == 1 || in_array(137, session('permissions')))
									<form id="add_adjustment_form" class="form-inline mb-1 justify-content-center mt-2 d-none" method="POST" action="{{ route('admin.finance.add_shipment_adjustment.store') }}" novalidate="novalidate">
										{{ csrf_field() }}

										<input type="hidden" name="shipment_id" class="shipment_id">

										<div class="form-group">
											<input type="text" name="payable" class="form-control payable" placeholder="Payable" data-rule-range="[-500000,500000]" data-msg-range="Payable needs to be from -500000 to 500000" data-rule-not="0" data-msg-not="Payable cannot be 0">
										</div>

										<div class="form-group ml-1">
											<select name="adjustment_type" id="adjustment_types" class="select2 form-control" data-rule-required="true" data-msg-required="Adjustment Type is required">
												@foreach($adjustment_types as $adjustment_type)
													<option value="{{$adjustment_type->id}}">{{$adjustment_type->name}}</option>
												@endforeach
											</select>
										</div>

										<div class="form-group ml-1">
											<input type="text" name="payable_remarks" class="form-control" placeholder="Remarks" data-rule-required="true" data-msg-required="Remarks is required">
										</div>

										<div class="form-group ml-1">
											<button type="submit" name="add" class="btn btn-primary add" value="Add">Add</button>
										</div>
									</form>
								@endif
							</div>

							<div class="row bulk_adjustment mt-4 d-none">
								<form id="bulk_adjustment_form" class="form-horizontal w-100 p-2" method="POST" action="{{ route('admin.finance.add_shipment_adjustment.bulk_store') }}" novalidate="novalidate" enctype="multipart/form-data">
									@csrf
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<input type="file" name="shipments" class="w-100 p-1 border-primary" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
											</div>
										</div>
										<div class="col-md-3 mt-1">
											<div class="form-group">
												<button type="submit" class="btn btn-primary mb-2">Upload</button>
											</div>
										</div>

										<div class="col-md-3 justify-content-end">
											<div class="form-group text-right">
												<a href="{{ asset('file/Bulk Shipment Adjustment Template.xlsx') }}?v=09_06_2021" class="btn btn-primary btn-block"><i class="la la-download"></i> Download Template</a>
											</div>
										</div>
									</div>
								</form>

									<div class="col-3">
										<table class="table table-bordered">
											<thead>
											<tr role="row" class="bg-primary white text-center">
												<th colspan="2" class="border-primary border-darken-1">Adjustment Types</th>
											</tr>
											<tr role="row" class="bg-primary bg-lighten-1 white">
												<th class="text-center border-primary border-lighten-2">ID</th>
												<th class="border-primary border-lighten-2">Name</th>
											</tr>
											</thead>
											<tbody>
											@foreach ($adjustment_types as $adjustment)
												<tr role="row">
													<td class="text-center">{{ $adjustment->id }}</td>
													<td>{{ $adjustment->name }}</td>
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
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

	<style>
		table.table.table-sm td {
			border: 1px solid #626E82 !important;
		}
	</style>
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {

			$("#switch").on('change', function(){
				if($("#switch").is(":checked")){
					$('.individual_adjustemnt').addClass('d-none');
					$('.bulk_adjustment').removeClass('d-none');
				}else{
					$('.individual_adjustemnt').removeClass('d-none');
					$('.bulk_adjustment').addClass('d-none');
				}
			});



			$('#adjustment_types').prepend('<option value="" selected="selected"></option>').select2({
				width:'100%',
				placeholder:"Select Adjustment Type",
				allowClear:true
			});
			$('#search_form input.tracking_number').inputmask({
				'alias': 'integer',
				'allowMinus': false,
				'allowPlus': false
			});

			$('#search_form').validate({
				errorClass: 'danger',
				successClass: 'success',
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parents('form'));
				},
				submitHandler: function(form) {
					$(form).find('button.search').prop('disabled', true);

					$('#shipment').html('');

					@if (session('role_id') == 1 || in_array(137, session('permissions')))
						$('#add_adjustment_form').addClass('d-none');

						$('#add_adjustment_form input.tracking_number').val('');
					@endif

					var tracking_number = $(form).find('input.tracking_number').val();

					form.reset();

					$.ajax({
						url: '{!! route('admin.finance.add_shipment_adjustment.shipment_details') !!}',
						method: 'POST',
						data: {
							'_token': '{{ csrf_token() }}',
							'tracking_number': tracking_number
						}
					})
					.done(function(data) {
						if (data.status == 0) {
							details = data.details;

							shipment = '<div class="row justify-content-between">';

							shipment += '<div class="col-12">';
							shipment += '<table class="table table-sm table-bordered mb-0">';
							shipment += '<tbody>';
							shipment += '<tr>';
							shipment += '<td><strong>Tracking Number</strong></td>';
							shipment += '<td><strong>Status</strong></td>';
							shipment += '<td><strong>Service Type</strong></td>';
							shipment += '<td><strong>Shipping Mode</strong></td>';
							shipment += '<td><strong>Weight</strong></td>';
							shipment += '<td><strong>Payment Mode</strong></td>';
							shipment += '<td><strong>Amount</strong></td>';
							shipment += '</tr>';
							shipment += '<tr>';
							shipment += '<td>' + details.tracking_number + '</td>';
							shipment += '<td>' + details.status + '</td>';
							shipment += '<td>' + details.service_type + '</td>';
							shipment += '<td>' + details.shipping_mode + '</td>';
							shipment += '<td>' + details.weight + ' kg</td>';
							shipment += '<td>' + details.payment_mode + '</td>';
							shipment += '<td>Rs. ' + details.amount + '</td>';
							shipment += '</tr>';
							shipment += '</tbody>';
							shipment += '</table>';
							shipment += '</div>';

							shipment += '<div class="col-6 mt-1">';
							shipment += '<table class="table table-sm table-bordered mb-0">';
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

							shipment += '<div class="col-6 mt-1">';
							shipment += '<table class="table table-sm table-bordered mb-0">';
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

							$('#shipment').html(shipment);

							@if (session('role_id') == 1 || in_array(137, session('permissions')))
								$('#add_adjustment_form').removeClass('d-none');

								$('#add_adjustment_form input.shipment_id').val(details.id);
							@endif

							$(form).find('button.search').prop('disabled', false);

							toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
						}
						else {
							$(form).find('button.search').prop('disabled', false);

							toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
						}
					});

					return false;
				}
			});
			$('#bulk_adjustment_form').validate({
				errorClass: 'danger',
				successClass: 'success',
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parents('form'));
				},
			});

			@if (session('role_id') == 1 || in_array(137, session('permissions')))
				$.validator.addMethod('not', function(value, element, param) {
					return (value != param) && (value == parseInt(value, 10));
				}, 'Invalid Value Entered');

				$('#add_adjustment_form input.payable').inputmask({
					'alias': 'integer',
					'allowMinus': true,
					'allowPlus': true,
					'groupSeparator': ',',
					'autoGroup': true
				});

				$('#add_adjustment_form').validate({
					errorClass: 'danger',
					successClass: 'success',
					normalizer: function(value) {
						return $.trim(value).replace(/,/g, '');
					},
					errorPlacement: function(error, element) {
						error.addClass('w-100').appendTo(element.parents('form'));
					}
				});
			@endif
		});
	</script>
@endsection