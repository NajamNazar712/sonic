@extends('admin.layout.master')

@section('title', 'Update Subsitute Account')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Update Substitute Account
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<form id="substitute_account_form" class="form-horizontal" method="POST" action="{{ route("admin.accounts.substitute_account_management.update.store", ["$shipper_id", "$id"]) }}" novalidate="novalidate">
								{{ csrf_field() }}

								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<div class="form-group">
											<input type="text" name="name" class="form-control" placeholder="Name*" data-rule-required="true" data-msg-required="Name is required" value="{{ $substitute_user->name }}">
										</div>
									</div>

									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<div class="form-group">
											<input type="text" name="phone_number" id="phone_number" class="form-control" placeholder="Phone Number*" data-rule-required="true" data-msg-required="Phone Number is required" value="{{ $substitute_user->phone_number }}">
										</div>
									</div>

									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<div class="form-group">
											<input type="text" name="cnic" id="cnic" class="form-control" placeholder="CNIC*" data-rule-required="true" data-msg-required="CNIC is required" value="{{ $substitute_user->cnic }}">
										</div>
									</div>

									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<div class="form-group">
											<input type="email" name="email" class="form-control" placeholder="Email*" data-rule-required="true" data-msg-required="Email is required" data-rule-remote="{{ route("admin.accounts.substitute_account_management.check_email", "$substitute_user->id") }}" data-msg-remote="Email must be unique" value="{{ $substitute_user->email }}">
										</div>
									</div>

									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<div class="form-group">
											<input type="password" name="password" class="form-control" placeholder="Password" data-rule-minlength="6" data-msg-minlength="Password needs to be at-least 6 characters">
										</div>
									</div>
									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<select name="restriction" class="select2" id="restriction" data-rule-required="true" data-msg-required="Restriction is required">
											@if($substitute_user->restriction == 1)
												<option value="1" selected>Enable</option>
												<option value="0">Disable</option>
											@else
												<option value="1">Enable</option>
												<option value="0" selected>Disable</option>
											@endif
										</select>
									</div>

									<div class="col-12">
										<h4 class="form-section mb-2">Permissions</h4>

										@foreach($permissions as $permission)
										@if ($permission->id == 15)
											@if (session('user_id')==7306)
												<fieldset class="d-inline-block m-1">
													@if (in_array($permission->id, $substitute_user_permissions))
														<input type="checkbox" id="permission_{{ $permission->id }}" class="permission" name="permission_ids[]" value="{{ $permission->id }}" checked="checked">
													@else
														<input type="checkbox" id="permission_{{ $permission->id }}" class="permission" name="permission_ids[]" value="{{ $permission->id }}">
													@endif
													<label for="permission_{{ $permission->id }}">{{ $permission->name }}</label>
												</fieldset>
											@endif
										@else
											<fieldset class="d-inline-block m-1">
												@if (in_array($permission->id, $substitute_user_permissions))
													<input type="checkbox" id="permission_{{ $permission->id }}" class="permission" name="permission_ids[]" value="{{ $permission->id }}" checked="checked">
												@else
													<input type="checkbox" id="permission_{{ $permission->id }}" class="permission" name="permission_ids[]" value="{{ $permission->id }}">
												@endif
												<label for="permission_{{ $permission->id }}">{{ $permission->name }}</label>
											</fieldset>
										@endif
											
										@endforeach
									</div>

									<div class="col-12">
										<div class="form-group text-center">
											<button type="submit" class="btn btn-primary">Update</button>
										</div>
									</div>
								</form>
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
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			$('#restriction').select2({
				width: '100%',
				placeholder: 'Restriction*'
			});
			$('#substitute_account_form #phone_number').inputmask({
				'mask': '9999-9999999',
				'clearIncomplete': true
			});

			$('#substitute_account_form #cnic').inputmask({
				'mask': '99999-9999999-9',
				'clearIncomplete': true
			});

			$('#substitute_account_form .permission').each(function() {
				var checkbox = $(this);
				var label = checkbox.next();
				var text = label.text();

				label.remove();

				checkbox.iCheck({
					checkboxClass: 'icheckbox_line pt-1 pb-1',
					checkedClass: 'checked bg-success',
					uncheckedClass: 'bg-danger',
					insert: '<div class="icheck_line-icon"></div>' + text
				});
			});

			$('#substitute_account_form').validate({
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
						text: 'Substitute Account is being updated!',
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