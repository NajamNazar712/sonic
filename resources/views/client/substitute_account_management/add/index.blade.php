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
									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<select name="restriction" class="select2" id="restriction" data-rule-required="true" data-msg-required="Restriction is required">
											<option value="1">Enable</option>
											<option value="0">Disable</option>
										</select>
									</div>

									{{-- Store pickup addresses for substitute account --}}
									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
										<label for="select_all_address">
											Select All Addresses
											<input type="checkbox" name="select_all_address" id="select_all_address">
										</label>
										<select name="pickup_address[]" class="select2" id="pickup_address" multiple>
											@foreach ($pickup_addresses as $address)
												<option value="{{ $address->id }}" data-name="{{ $address->pickup_address }}">{{ $address->pickup_address }}</option>
											@endforeach
											{{-- <option value="0">New</option> --}}
										</select>
									</div>

									{{-- if new is selected from dropdown --}}
									<div id="new_pickup_address" class="row d-none">
										<div class="form-group col-4">
											<textarea name="new_pickup_address" class="form-control" placeholder="Address*" 
													data-rule-required="true" data-msg-required="Address is required" 
													data-rule-maxlength="190" data-msg-maxlength="Address can be maximum 190 characters"></textarea>
											<input type="checkbox" name="make_default_address" value="1"> Make default address
										</div>
									
										<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-4">
											<input type="text" name="new_pickup_person_of_contact" class="form-control" 
												placeholder="Person of Contact*" data-rule-required="true" 
												data-msg-required="Person of Contact is required" 
												data-rule-maxlength="100" data-msg-maxlength="Person of Contact can be maximum 100 characters">
										</div>
									
										<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-4">
											<input type="text" name="new_pickup_vendor" class="form-control" 
												placeholder="Vendor" data-rule-maxlength="100" 
												data-msg-maxlength="Vendor can be maximum 100 characters">
										</div>
									
										<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-4">
											<input type="text" name="new_pickup_phone_number" class="form-control phone_number" 
												placeholder="Phone Number*" data-rule-required="true" 
												data-msg-required="Phone Number is required">
										</div>
									
										<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-4">
											<input type="email" name="new_pickup_email_address" class="form-control" 
												placeholder="Email Address*" data-rule-required="true" 
												data-msg-required="Email Address is required">
										</div>
									
										<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-4">
											<select name="new_pickup_city" class="select2 form-control" id="new_pickup_city" 
													data-rule-required="true" data-msg-required="City is required">
												@foreach($cities as $city)
													<option value="{{ $city->id }}">{{ $city->name }}</option>
												@endforeach
											</select>
										</div>
									</div>

									<div class="col-12">
										<h4 class="form-section mb-2">Permissions</h4>

										@foreach($permissions as $permission)
											@if ($permission->id == 15)
												@if (session('user_id')==7306)
													<fieldset class="d-inline-block m-1">
														<input type="checkbox" id="permission_{{ $permission->id }}" class="permission" name="permission_ids[]" value="{{ $permission->id }}">
														<label for="permission_{{ $permission->id }}">{{ $permission->name }}</label>
													</fieldset>
												@endif
											@else
											<fieldset class="d-inline-block m-1">
												<input type="checkbox" id="permission_{{ $permission->id }}" class="permission" name="permission_ids[]" value="{{ $permission->id }}">
												<label for="permission_{{ $permission->id }}">{{ $permission->name }}</label>
											</fieldset>
											@endif
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
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">

	<style>
		#new_pickup_address{
			margin: 22px 0px 0px 0px;
		}
	</style>
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			$('#restriction').prepend('<option value="" selected></option>').select2({
				width: '100%',
				placeholder: 'Restriction*'
			});
			$('#pickup_address').select2({
				width: '100%',
				placeholder: 'Pickup Address*'
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
						text: 'Substitute Account is being added!',
						icon: 'info',
						buttons: false,
						closeOnClickOutside: false,
						closeOnEsc: false
					});

					form.submit();
				}
			});

			// multi-select pickup addresses 
			$('#select_all_address').on('change', function () {
				const allOptions = $('#pickup_address option');
				if ($(this).is(':checked')) {
					// Select all options except the one with value "0"
					const selectedValues = allOptions
						.filter(function() { return $(this).val() !== "0"; }) // Exclude value "0"
						.map(function() { return $(this).val(); })
						.get();

					$('#pickup_address').val(selectedValues).trigger('change');
				} else {
					$('#pickup_address').val(null).trigger('change');
				}
			});

			// Handle selection changes for the pickup_address
			let isUpdating = false;
			$('#pickup_address').on('change', function () {
				// Prevent recursion
				if (isUpdating) return;

				const selectedValues = $(this).val();
				if (selectedValues && selectedValues.includes("0")) {
					// If "0" (New) is selected, deselect all other options
					isUpdating = true;
					$(this).val(["0"]).trigger('change'); // Set the value to only "0"
					isUpdating = false;

					// Show the new pickup address form
					$('#new_pickup_address').removeClass('d-none');
				} else {
					// Hide the new pickup address form if "0" is not selected
					$('#new_pickup_address').addClass('d-none');
				}
			});

		});
	</script>
@endsection