@extends('admin.layout.master')

@section('title', 'Notifications')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Notifications
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">Name</th>
										<th class="border-primary border-darken-1">Type</th>
										<th class="border-primary border-darken-1">Updated Datetime</th>
										<th class="border-primary border-darken-1">Updated by</th>
										<th class="border-primary border-darken-1">Status</th>
										<th class="border-primary border-darken-1"></th>
									</tr>
								</thead>
							</table>

							@if (session('role_id') == 1 || in_array(103, session('permissions')))
								<div class="modal fade" id="send_custom_email" role="dialog" aria-labelledby="send_custom_email_title" aria-hidden="true">
									<div class="modal-dialog modal-lg" role="document">
										<div class="modal-content">
											<form class="form-horizontal" method="POST" action="{{ route('admin.notifications.send_custom_email') }}" novalidate="novalidate">
												{{ csrf_field() }}

												<div class="modal-header">
													<h4 class="modal-title" id="send_custom_email_title">Send Custom Email</h4>

													<button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">×</span>
													</button>
												</div>
												<div class="modal-body">
													<div class="form-group">
														<select name="receiver" class="select2 receiver" data-rule-required="true" data-msg-required="Receiver is required">
															<option value="" selected="selected"></option>
															<option value="1">Employees</option>
															<option value="2">Shippers</option>
														</select>
													</div>

													<div class="form-group">
														<label>Subject</label>
														<input type="text" name="subject" class="form-control subject" placeholder="Subject*" data-rule-required="true" data-msg-required="Subject is required">
													</div>

													<div class="form-group">
														<label>Body</label>
														<textarea type="text" name="body" class="form-control body" placeholder="Body*" data-rule-required="true" data-msg-required="Body is required"></textarea>
													</div>
												</div>
												<div class="modal-footer">
													<button type="button" class="mr-auto btn btn-secondary" data-dismiss="modal">Close</button>
													<button type="submit" name="send" class="btn btn-primary">Send</button>
												</div>
											</form>
										</div>
									</div>
								</div>
							@endif

							@if (session('role_id') == 1 || in_array(101, session('permissions')))
								<div class="modal fade" id="edit" role="dialog" aria-labelledby="edit_title" aria-hidden="true">
									<div class="modal-dialog modal-lg" role="document">
										<div class="modal-content">
											<form class="form-horizontal" novalidate="novalidate">
												<input type="hidden" name="id" class="id">

												<div class="modal-header">
													<h4 class="modal-title" id="edit_title">Edit</h4>

													<button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">×</span>
													</button>
												</div>
												<div class="modal-body">
													<div class="form-group email">
														<label>Subject</label>
														<input type="text" name="subject" class="form-control subject" placeholder="Subject*" data-rule-required="true" data-msg-required="Subject is required" data-rule-field="true">
													</div>

													<div class="form-group">
														<label>Body</label>
														<textarea type="text" name="body" class="form-control body" placeholder="Body*" data-rule-required="true" data-msg-required="Body is required" data-rule-field="true"></textarea>
													</div>

													<div class="form-group">
														<label>Fields</label>
														<div class="fields">
														</div>
													</div>
												</div>
												<div class="modal-footer">
													<button type="button" class="mr-auto btn btn-secondary" data-dismiss="modal">Close</button>
													<button type="submit" name="edit" class="btn btn-primary">Edit</button>
												</div>
											</form>
										</div>
									</div>
								</div>
							@endif
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
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/textarea/autosize.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			@if (session('role_id') == 1 || in_array(103, session('permissions')))
				$('#send_custom_email .receiver').select2({
					width: '100%',
					placeholder: 'Receiver'
				}).bind('change', function() {
					if ($(this).hasClass('danger')) {
						$(this).valid();
					}
				});

				autosize($('#send_custom_email .body')[0]);
			@endif

			@if (session('role_id') == 1 || in_array(101, session('permissions')))
				autosize($('#edit .body')[0]);
			@endif

			var valid_fields = [];

			var table = $('#datatable').DataTable({
				@if (session('role_id') == 1 || in_array(103, session('permissions')))
					dom: '<"d-inline-block"l><"pull-right"B>tipr',
					buttons: [{
						text: 'Send Custom Email',
						className: 'btn btn-primary send_custom_email',
						action: function (e, dt, node, config) {
							$('#send_custom_email').modal('show');
						}
					}],
				@else
                	dom: 'ltipr',
				@endif
				scrollX: true, scrollY: '300px',
				lengthMenu: [[25, 50, 100], [25, 50, 100]],
				pageLength: 25,
				pagingType: 'full_numbers',
				processing: true,
				serverSide: true,
				ajax: '{{ route('admin.notifications.list') }}',
				rowId: 'id',
				order: [[1, 'asc']],
				columns: [
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data: 'name', name: 'notifications.name', class: 'align-middle name'},
					{data: 'type', name: 'nt.name', class: 'align-middle type'},
					{data: 'updated_at', name: 'notifications.updated_at', class: 'align-middle updated_at'},
					{data: 'updated_by', name: 'a.name', class: 'align-middle updated_by'},
					{data: 'status', name: 'notifications.status', class: 'align-middle status'},
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

			@if (session('role_id') == 1 || in_array(103, session('permissions')))
				$('#send_custom_email form').validate({
					errorClass: 'danger',
					successClass: 'success',
					normalizer: function(value) {
						return $.trim(value);
					},
					errorPlacement: function(error, element) {
						error.addClass('w-100').appendTo(element.parent('.form-group'));
					}
				});
			@endif

			$('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
				var notification_id = parseInt($(this).parents('tr').attr('id'));
				var notification_type = parseInt($(this).parents('tr').attr('data-type'));

				@if (session('role_id') == 1 || in_array(101, session('permissions')))
					if ($(this).hasClass('edit')) {
						$.ajax({
							url: '{!! route('admin.notifications.details') !!}',
							method: 'POST',
							data: {
								'_token': '{{ csrf_token() }}',
								'id': notification_id
							}
						})
						.done(function(data) {
							$('#edit .id').val(notification_id);

							if (notification_type == 1) {
								$('#edit .email').removeClass('d-none');

								$('#edit .subject').val(data.subject);
							}
							else {
								$('#edit .email').addClass('d-none');

								$('#edit .subject').val('');
							}

							$('#edit .body').val(data.body);

							$('#edit .fields').html('');

							valid_fields = [];

							$.each(data.fields, function(index, field) {
								$('#edit .fields').append('<span class="d-inline-block mb-1 mr-1 bg-info text-highlight white">[' + field + ']</span>');

								valid_fields.push(field);
							});

							$('#edit').modal('show');
						});
					}
				@endif

				@if (session('role_id') == 1 || in_array(102, session('permissions')))
					if ($(this).hasClass('enable')) {
						$.ajax({
							url: '{!! route('admin.notifications.status') !!}',
							method: 'POST',
							data: {
								'id': notification_id,
								'status': 1,
								'_token': '{{ csrf_token() }}'
							}
						})
						.done(function(data) {
							if (data.status == 0) {
								table.draw(false);

								toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
							}
							else {
								toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
							}
						});
					}
					else if ($(this).hasClass('disable')) {
						$.ajax({
							url: '{!! route('admin.notifications.status') !!}',
							method: 'POST',
							data: {
								'id': notification_id,
								'status': 0,
								'_token': '{{ csrf_token() }}'
							}
						})
						.done(function(data) {
							if (data.status == 0) {
								table.draw(false);

								toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
							}
							else {
								toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
							}
						});
					}
				@endif
			});

			@if (session('role_id') == 1 || in_array(101, session('permissions')))
				$('#edit').on('shown.bs.modal', function (e) {
					autosize.update($('#edit .body')[0]);
				})

				$.validator.addMethod('field', function(value, element) {
					var valid = true;

					var entered_fields = value.match(/[^[\]]+(?=])/g);

					$.each(entered_fields, function(index, field) {
						if ($.inArray(field, valid_fields) === -1) {
							valid = false;

							return valid;
						}
					});

					return valid;
				}, 'One or more invalid Field(s) entered');

				$('#edit form').validate({
					errorClass: 'danger',
					successClass: 'success',
					normalizer: function(value) {
						return $.trim(value);
					},
					errorPlacement: function(error, element) {
						error.addClass('w-100').appendTo(element.parent('.form-group'));
					},
					submitHandler: function(form) {
						var id = $(form).find('.id').val();
						var subject = $(form).find('.subject').val();
						var body = $(form).find('.body').val();

						$.ajax({
							url: '{!! route('admin.notifications.edit') !!}',
							method: 'POST',
							data: {
								'_token': '{{ csrf_token() }}',
								'id': id,
								'subject': subject,
								'body': body
							}
						})
						.done(function(data) {
							if (data.status == 0) {
								$('#edit').modal('hide');

								toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
							}
							else {
								toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
							}
						});
					}
				});
			@endif
		});
	</script>
@endsection