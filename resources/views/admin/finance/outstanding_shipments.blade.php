@extends('admin.layout.master')

@section('title', 'Outstanding Shipments')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Outstanding Shipments
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
								<div class="form-group">
									<select name="hub" class="select2" id="hub" data-rule-required="true" data-msg-required="Hub is required">
										<option value="0">All</option>

										@foreach($hubs as $hub)
											<option value="{{ $hub->id }}">{{ $hub->name }}</option>
										@endforeach
									</select>
								</div>

								<div class="form-group ml-1">
									<select name="service" class="select2" id="service">
										<option value="0">All</option>

										@foreach($booking_types as $booking_type)
											<option value="{{ $booking_type->id }}">{{ $booking_type->booking_type }}</option>
										@endforeach
									</select>
								</div>

								<div class="form-group input-group ml-1">
									<div class="input-group-prepend">
										<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
											<span class="la la-calendar-o"></span>
										</span>
									</div>

									<input type="text" name="delivery_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="delivery_date_from" placeholder="Delivery Date (From)">
								</div>

								<div class="form-group input-group ml-1">
									<div class="input-group-prepend">
										<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
											<span class="la la-calendar-o"></span>
										</span>
									</div>

									<input type="text" name="delivery_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="delivery_date_to" placeholder="Delivery Date (To)">
								</div>

								<div class="form-group ml-1">
									<button type="submit" name="search" class="btn btn-primary" value="Search">Search</button>
								</div>
							</form>

							<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">Tracking Number</th>
										<th class="border-primary border-darken-1">Consignee</th>
										<th class="border-primary border-darken-1">Address</th>
										<th class="border-primary border-darken-1">Destination</th>
										<th class="border-primary border-darken-1">Hub</th>
										<th class="border-primary border-darken-1">Shipper</th>
										<th class="border-primary border-darken-1">Service Type</th>
										<th class="border-primary border-darken-1">Amount</th>
										<th class="border-primary border-darken-1">Status</th>
										<th class="border-primary border-darken-1">Status Updated Datetime</th>
										<th class="border-primary border-darken-1">Remarks</th>
										<th class="border-primary border-darken-1">DNCC</th>
										<th class="border-primary border-darken-1">SDN</th>
										<th class="border-primary border-darken-1">Aging</th>
										<th class="border-primary border-darken-1"></th>
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
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			$('#search_form #hub').prepend('<option value="" selected="selected"></option>').select2({
				width: '150px',
				placeholder: 'Hub*'
			}).bind('change', function() {
				$(this).valid();
			});

			$('#search_form #service').prepend('<option value="" selected="selected"></option>').select2({
				width: '150px',
				placeholder: 'Service'
			});

			$('#search_form #delivery_date_from').pickadate({
				firstDay: 1,
				clear: '',
				selectYears: true,
				selectMonths: true,
				formatSubmit: 'yyyy-mm-dd 00:00:00',
				hiddenSuffix: '_formatted',
				onSet: function(context) {
					if (context.select) {
						$('#search_form #delivery_date_to').pickadate('picker').set('min', $('#search_form #delivery_date_from').pickadate('picker').get('select'));
					}
				}
			});

			$('#search_form #delivery_date_to').pickadate({
				firstDay: 1,
				clear: '',
				selectYears: true,
				selectMonths: true,
				formatSubmit: 'yyyy-mm-dd 00:00:00',
				hiddenSuffix: '_formatted',
				onSet: function(context) {
					if (context.select) {
						$('#search_form #delivery_date_from').pickadate('picker').set('max', $('#search_form #delivery_date_to').pickadate('picker').get('select'));
					}
				}
			});

			$('#search_form').validate({
				errorClass: 'danger',
				successClass: 'success',
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parents('form'));
				},
				submitHandler: function(form) {
					table.draw();

					return false;
				}
			});

			var table = $('#datatable').DataTable({
				dom: 'ltipr',
				scrollX: true, scrollY: '300px',
				lengthMenu: [[25, 50, 100], [25, 50, 100]],
				pageLength: 25,
				pagingType: 'full_numbers',
				processing: true,
				serverSide: true,
				ajax: {
					url: '{{ route('admin.finance.outstanding_shipments.list') }}',
					data: function (d) {
						d.hub = $('#search_form #hub').val();
						d.service = $('#search_form #service').val();
						d.delivery_date_from = $('#search_form input[name="delivery_date_from_formatted"]').val();
						d.delivery_date_to = $('#search_form input[name="delivery_date_to_formatted"]').val();
					}
				},
				rowId: 'id',
				order: [[1, 'asc']],
				columns: [
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data:'tracking_number', name: 's.tracking_number', class: 'align-middle text-center tracking_number'},
					{data:'consignee', name: 's.consignee_name', class: 'align-middle text-center consignee'},
					{data:'address', name: 's.consignee_address', class: 'align-middle text-center address'},
					{data:'destination', name: 'dc.name', class: 'align-middle text-center destination'},
					{data:'hub', name: 'hc.name', class: 'align-middle text-center hub'},
					{data:'shipper', name: 'u.name', class: 'align-middle text-center shipper'},
					{data:'service_type', name: 'bt.booking_type', class: 'align-middle text-center service_type'},
					{data:'amount', name: 's.amount', class: 'align-middle text-center amount'},
					{data:'status', name: 'ss.name as status', class: 'align-middle text-center status'},
					{data:'status_updated_at', name: 'sj.updated_at', class: 'align-middle text-center status_updated_at'},
					{data:'remarks', name: 'sj.remarks', class: 'align-middle text-center remarks'},
					{data:'dncc', name: 'delivery_note_shipments.delivery_note_id', class: 'align-middle text-center dncc'},
					{data:'sdn', name: 'dnsdn.station_deposit_note_id', class: 'align-middle text-center sdn'},
					{data:'aging', name: 'aging', class: 'align-middle text-center aging'},
					{data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
				],
				rowCallback: function(row, data, index) {
					var info = table.page.info();

					$('td:eq(0)', row).html(index + 1 + info.page * info.length);
				},
				initComplete: function() {
					var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

					var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
					var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
					var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

					this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.serial_number') || $(header).is('.action')) {
							$(td).appendTo($(search));
						}
						else {
							var current = $(input).appendTo($(search)).on('change', function() {
								column.search($(this).val(), false, false, true).draw();
							}).wrap(td).after(icon);

							if (column.search()) {
								current.val(column.search());
							}
						}
					});

					this.api().table().columns.adjust();
				}
			});

			$('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
				var id = parseInt($(this).parents('tr').attr('id'));
				var tracking_number = $(this).parents('tr').children('td.tracking_number').text();

				if ($(this).hasClass('resolve')) {
					swal({
						title: 'Are you sure?',
						text: 'You want to mark ' + tracking_number + ' Resolved?',
						icon: 'success',
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
						dangerMode: true
					}).then(function(confirm) {
						if (confirm) {
							$.ajax({
								url: '{!! route('admin.finance.outstanding_shipments.resolved') !!}',
								method: 'PUT',
								data: {
									'id': id,
									'_token': '{{ csrf_token() }}'
								}
							})
							.done(function(data) {
								if (data.status == 0) {
									toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
								}
								else {
									toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
								}

								table.draw(false);
							});
						}
					});
				}
				else if ($(this).hasClass('adjust_in_payment')) {
					swal({
						title: 'Are you sure?',
						text: 'You want to Adjust ' + tracking_number + ' in Payment?',
						icon: 'warning',
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
						dangerMode: true
					}).then(function(confirm) {
						if (confirm) {
							$.ajax({
								url: '{!! route('admin.finance.outstanding_shipments.adjust_in_payment') !!}',
								method: 'PUT',
								data: {
									'id': id,
									'_token': '{{ csrf_token() }}'
								}
							})
							.done(function(data) {
								if (data.status == 0) {
									toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
								}
								else {
									toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
								}

								table.draw(false);
							});
						}
					});
				}
			});
		});
	</script>
@endsection