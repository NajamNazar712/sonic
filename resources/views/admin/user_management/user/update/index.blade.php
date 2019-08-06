@extends('admin.layout.master')

@section('title', 'Update User')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Update User
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<form id="user_form" class="form-horizontal" method="POST" action="{{ route('admin.user_management.users.update.store', ['id' => $user->id]) }}" novalidate="novalidate">
								{{ csrf_field() }}

								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<div class="form-group">
											<input type="text" name="name" class="form-control" placeholder="Name*" data-rule-required="true" data-msg-required="Name is required" value="{{ $user->name }}">
										</div>
									</div>

									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<div class="form-group">
											<input type="text" name="phone_number" id="phone_number" class="form-control" placeholder="Phone Number*" data-rule-required="true" data-msg-required="Phone Number is required" value="{{ $user->phone_number }}">
										</div>
									</div>

									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<div class="form-group">
											<input type="text" name="cnic" id="cnic" class="form-control" placeholder="CNIC*" data-rule-required="true" data-msg-required="CNIC is required" value="{{ $user->cnic }}">
										</div>
									</div>

									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<div class="form-group">
											<input type="email" name="email" class="form-control" placeholder="Email*" data-rule-required="true" data-msg-required="Email is required" data-rule-remote="{{ route('admin.user_management.users.email', ['id' => $user->id]) }}" data-msg-remote="Email must be unique" value="{{ $user->email }}">
										</div>
									</div>

									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<div class="form-group">
											<input type="password" name="password" class="form-control" placeholder="Password" data-rule-minlength="6" data-msg-minlength="Password needs to be at-least 6 characters">
										</div>
									</div>

									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<div class="form-group">
											<select name="role_id" class="select2" id="role" data-rule-required="true" data-msg-required="Role is required">
												@foreach($roles as $role)
													@if ($role->id == $user->role_id)
														<option value="{{ $role->id }}" selected="selected">{{ $role->name }} - {{ $role->department->name }}</option>
													@else
														<option value="{{ $role->id }}">{{ $role->name }} - {{ $role->department->name }}</option>
													@endif
												@endforeach
											</select>
										</div>
									</div>
									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<div class="form-group">
											<select name="default_hub" class="select2" id="default_hub" data-rule-required="true" data-msg-required="Default hub is required">
												@foreach($hubs as $hub)
													@if ($hub->id == $user->default_hub_id)
														<option value="{{ $hub->id }}" selected="selected">{{ $hub->name }}</option>
													@else
														<option value="{{ $hub->id }}">{{ $hub->name }}</option>
													@endif
												@endforeach
											</select>
										</div>
									</div>

									<div class="col-12">
										<h4 class="form-section mb-2">Hubs</h4>

										@foreach($hubs as $hub)
											<fieldset class="d-inline-block m-1">
												@if (in_array($hub->id, $user_hubs))
													<input type="checkbox" id="hub_{{ $hub->id }}" class="hub" name="hub_ids[]" value="{{ $hub->id }}" checked="checked">
												@else
													<input type="checkbox" id="hub_{{ $hub->id }}" class="hub" name="hub_ids[]" value="{{ $hub->id }}">
												@endif
												<label for="hub_{{ $hub->id }}">{{ $hub->name }}</label>
											</fieldset>
										@endforeach
									</div>

									<div class="col-12">
										<div class="form-group text-center">
											<button type="submit" class="btn btn-primary">Update</button>
										</div>
									</div>
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
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			$('#user_form #role').select2({
				width: '100%',
				placeholder: 'Role*'
			});

			@if ($user->default_hub_id === null)
				$('#user_form #default_hub').prepend('<option value="" selected="selected"></option>').select2({
					width: '100%',
					placeholder: 'Default Hub*'
				});
			@else
				$('#user_form #default_hub').select2({
					width: '100%',
					placeholder: 'Default Hub*'
				});
			@endif

			$('#user_form #phone_number').inputmask({
				'mask': '9999-9999999',
				'clearIncomplete': true
			});

			$('#user_form #cnic').inputmask({
				'mask': '99999-9999999-9',
				'clearIncomplete': true
			});

			$('#user_form .hub').each(function() {
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

			$('#user_form').validate({
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
						text: 'User is being updated!',
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