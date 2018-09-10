@extends('client.layout.master')

@section('title', 'Add Subsitute Account')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Add Substitute Account
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('client.inc.messages')

							<form id="substitute_account_form" class="form-horizontal" method="POST" action="{{ route('cod.substitute_account_management.add.store') }}" novalidate="novalidate">
								{{ csrf_field() }}

								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<div class="form-group">
											<input type="text" name="name" class="form-control" placeholder="Name*" data-rule-required="true" data-msg-required="Name is required">
										</div>
									</div>

									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<div class="form-group">
											<input type="text" name="phone_number" id="phone_number" class="form-control" placeholder="Phone Number*" data-rule-required="true" data-msg-required="Phone Number is required">
										</div>
									</div>

									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<div class="form-group">
											<input type="text" name="cnic" id="cnic" class="form-control" placeholder="CNIC*" data-rule-required="true" data-msg-required="CNIC is required">
										</div>
									</div>

									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<div class="form-group">
											<input type="email" name="email" class="form-control" placeholder="Email*" data-rule-required="true" data-msg-required="Email is required" data-rule-remote="{{ route('cod.substitute_account_management.email') }}" data-msg-remote="Email must be unique">
										</div>
									</div>

									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<div class="form-group">
											<input type="password" name="password" class="form-control" placeholder="Password*" data-rule-required="true" data-msg-required="Password is required" data-rule-minlength="6" data-msg-minlength="Password needs to be at-least 6 characters">
										</div>
									</div>

									<div class="col-12">
										<h4 class="form-section mb-2">Permissions</h4>

										@foreach($permissions as $permission)
											<fieldset class="d-inline-block m-1">
												<input type="checkbox" id="permission_{{ $permission->id }}" class="permission" name="permission_ids[]" value="{{ $permission->id }}">
												<label for="permission_{{ $permission->id }}">{{ $permission->name }}</label>
											</fieldset>
										@endforeach
									</div>


									<div class="col-12">
										<div class="form-group text-center mt-2">
											<button type="submit" class="btn btn-primary">Add</button>
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
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
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
						text: 'Substitute Account is being added!',
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