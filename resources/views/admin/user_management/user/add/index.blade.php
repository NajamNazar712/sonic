@extends('admin.layout.master')

@section('title', 'Add User')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Add User
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<form id="user_form" class="form-horizontal" method="POST" action="{{ route('admin.user_management.users.add.store') }}" novalidate="novalidate">
								{{ csrf_field() }}

								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<div class="form-group">
											<input type="text" name="name" class="form-control" placeholder="Name*" data-rule-required="true" data-msg-required="Name is required">
										</div>
									</div>

									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<div class="form-group">
											<input type="text" name="phone_number" id="phone_number" class="form-control unique_phone" placeholder="Phone Number*" data-rule-required="true" data-msg-required="Phone Number is required" data-rule-remote="{{ route('admin.user_management.users.validate_phone') }}" data-msg-remote="Phone Number is not unique">
										</div>
									</div>

									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<div class="form-group">
											<input type="text" name="official_phone_number" id="official_phone_number" class="form-control unique_phone" placeholder="Official Phone Number" data-rule-remote="{{ route('admin.user_management.users.validate_phone') }}" data-msg-remote="Official Phone Number is not unique">
										</div>
									</div>

									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<div class="form-group">
											<input type="text" name="cnic" id="cnic" class="form-control" placeholder="CNIC*" data-rule-required="true" data-msg-required="CNIC is required">
										</div>
									</div>

									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<div class="form-group">
											<input type="email" name="email" class="form-control" placeholder="Outlook Id*" data-rule-required="true" data-msg-required="Outlook Id is required" data-rule-remote="{{ route('admin.user_management.users.email') }}" data-msg-remote="Outlook Id must be unique">
										</div>
									</div>

									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<div class="form-group">
											<input type="password" name="pin" id="pin" class="form-control" placeholder="Bolt & Sonic Pin*" data-rule-required="true" data-msg-required="Bolt & Sonic Pin is required" data-rule-minlength="4" data-msg-minlength="Bolt & Sonic Pin needs to be at-least 4 characters">
										</div>
									</div>
									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<div class="form-group">
											<select name="role_id" class="select2" id="role" data-rule-required="true" data-msg-required="Role is required">
												@foreach($roles as $role)
													<option value="{{ $role->id }}">{{ $role->name }} - {{ $role->department->name }}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<div class="form-group">
											<select name="default_hub" class="select2" id="default_hub" data-rule-required="true" data-msg-required="Default hub is required">
												@foreach($hubs as $hub)
													<option value="{{ $hub->id }}">{{ $hub->name }}</option>
												@endforeach
											</select>
										</div>
									</div>

									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<div class="form-group">
											<select name="designation_id" class="select2" id="designation_id"  data-rule-required="true" data-msg-required="Designation is required">
												@foreach($designations as $designation)
													<option value="{{ $designation->id }}">{{ $designation->name }}</option>
												@endforeach
											</select>
										</div>
									</div>

									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<div class="form-group">
											<input type="text" name="trax_id" class="form-control" placeholder="Trax Id">
										</div>
									</div>


									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<div class="form-group">
											<select name="shift_id" class="select2" id="shift_list" data-rule-required="true" data-msg-required="Employee Shift is required">
												@foreach($shifts as $shift)
													<option value="{{$shift->id}}"> {{$shift->name}}</option>
												@endforeach
											</select>
										</div>
									</div>

									<div class="col-12">
										<h4 class="form-section mb-2">Hubs</h4>
										<div class=" text-center mt-2">
											<button type="button" id="selectAll"  class="btn btn-primary" >Select All Hubs</button>
											<button type="button" id="unselect" class="btn btn-primary">Unselect All Hubs</button>
											</di>
										</div>
									</div>

									@foreach($hubs as $hub)
										<fieldset class="d-inline-block m-1">
											<input type="checkbox" id="hub_{{ $hub->id }}" class="hub" name="hub_ids[]" value="{{ $hub->id }}">
											<label for="hub_{{ $hub->id }}">{{ $hub->name }}</label>
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
			$('#user_form #role').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Role*'
			});

			$('#user_form #default_hub').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Default Hub*'
			});

			$('#user_form #designation_id').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Select Designation'
			});

			$('#user_form #phone_number,#user_form #official_phone_number').inputmask({
				'mask': '9999-9999999',
				'clearIncomplete': true
			});

			$('#user_form #pin').inputmask({
				'alias': 'integer',
				'allowMinus': false,
				'allowPlus': false,
				'rightAlign': false,
				'mask': '9999',
				'clearIncomplete': true
			});

			$('#user_form #shift_list').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Working Shift*'
			});

			$('#user_form #location_list').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Reporting Location'
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

			$.validator.addMethod("unique_phone", function(value, element) {
				var parentForm = $(element).closest('form');
				var timeRepeated = 0;
				if (value != '') {
					$(parentForm.find('.unique_phone')).each(function () {
						if ($(this).val() === value && value != 0) {
							timeRepeated++;
						}
					});
				}
				return timeRepeated === 1 || timeRepeated === 0;

			}, "Phone Number Can Not Be Duplicate");

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
						text: 'User is being added!',
						icon: 'info',
						buttons: false,
						closeOnClickOutside: false,
						closeOnEsc: false
					});

					form.submit();
				}
			});
			$('#selectAll .hub').each(function() {
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

			$("#selectAll").click(function() {

				$('input.hub').each(function () {
					var _this = $(this);
					if(_this.is(':checked') == false) {
						_this.iCheck('check');
					}
				});
			});
			$("#unselect").click(function() {

				$('input.hub').each(function () {
					var _this = $(this);
					if(_this.is(':checked') == true) {
						_this.iCheck('uncheck');
					}
				});
			});
		});
		$('#user_form').on('keypress',function (e) {
			if(e.keyCode == 13) {
				e.preventDefault();
			}
		});



	</script>
@endsection